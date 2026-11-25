<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Carrier;
use App\Models\Location;
use App\Models\Shipment;
use App\Models\ShipmentException;
use App\Models\ShipmentItem;
use App\Services\EtaViabilityService;
use App\Services\ExceptionService;
use App\Services\ShipmentService;
use App\Services\TrackingService;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ShipmentApiController extends Controller
{
    public function __construct(
        protected ShipmentService $shipmentService,
        protected TrackingService $trackingService,
        protected ExceptionService $exceptionService,
        protected EtaViabilityService $etaService
    ) {}

    public function index(Request $request)
    {
        $user = $request->user();
        $query = Shipment::with(['origin', 'destination', 'carrier', 'items', 'latestTrackingEvent'])->latest();

        if ($user && $user->isCustomer()) {
            $query->where('customer_id', $user->id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('storage_temp')) {
            $query->where('storage_temp', $request->storage_temp);
        }

        return response()->json($query->paginate($request->input('per_page', 15)));
    }

    public function store(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'carrier_id' => ['required', 'exists:carriers,id'],
            'origin_id' => ['required', 'exists:locations,id', 'different:destination_id'],
            'destination_id' => ['required', 'exists:locations,id'],
            'storage_temp' => ['required', 'in:CRYO_LN2,DRY_ICE,CHILLED'],
            'total_weight_kg' => ['required', 'numeric', 'min:0.1'],
            'expected_departure' => ['required', 'date'],
            'expected_arrival' => ['required', 'date', 'after:expected_departure'],
            'specimen_type' => ['required', 'string'],
            'quantity_vials' => ['required', 'integer', 'min:1'],
            'biohazard_level' => ['required', 'in:BSL_1,BSL_2,BSL_3'],
            'required_temp_celsius' => ['required', 'numeric'],
            'storage_instructions' => ['nullable', 'string'],
        ]);

        $shipment = DB::transaction(function () use ($validated, $user) {
            $expectedArrival = Carbon::parse($validated['expected_arrival']);
            $shipment = Shipment::create([
                'shipment_number' => $this->shipmentService->generateShipmentNumber(),
                'customer_id' => $user->id,
                'carrier_id' => $validated['carrier_id'],
                'origin_id' => $validated['origin_id'],
                'destination_id' => $validated['destination_id'],
                'storage_temp' => $validated['storage_temp'],
                'status' => 'REGISTERED',
                'total_weight_kg' => $validated['total_weight_kg'],
                'expected_departure' => $validated['expected_departure'],
                'expected_arrival' => $expectedArrival,
                'estimated_arrival' => $expectedArrival,
                'is_delayed' => false,
                'delay_hours' => 0,
                'delay_severity' => 'NONE',
            ]);

            ShipmentItem::create([
                'shipment_id' => $shipment->id,
                'specimen_type' => $validated['specimen_type'],
                'quantity_vials' => $validated['quantity_vials'],
                'biohazard_level' => $validated['biohazard_level'],
                'required_temp_celsius' => $validated['required_temp_celsius'],
                'storage_instructions' => $validated['storage_instructions'] ?? null,
            ]);

            return $shipment;
        });

        return response()->json([
            'message' => 'Shipment registered successfully',
            'shipment' => $shipment->load(['origin', 'destination', 'carrier', 'items']),
        ], 201);
    }

    public function show(Request $request, $id)
    {
        $user = $request->user();
        $shipment = Shipment::with([
            'customer',
            'carrier',
            'origin',
            'destination',
            'items',
            'trackingEvents.location',
            'exceptions',
        ])->findOrFail($id);

        if ($user && $user->isCustomer() && $shipment->customer_id !== $user->id) {
            return response()->json(['message' => 'Forbidden: access restricted.'], 403);
        }

        return response()->json($shipment);
    }

    public function update(Request $request, $id)
    {
        $user = $request->user();
        if ($user && $user->isCustomer()) {
            return response()->json(['message' => 'Forbidden: customers cannot modify shipments.'], 403);
        }

        $shipment = Shipment::findOrFail($id);

        if ($request->has('status')) {
            try {
                $this->shipmentService->updateStatus($shipment, $request->status, $user, $request->input('reason'));
            } catch (Exception $e) {
                return response()->json(['message' => $e->getMessage()], 422);
            }
        }

        if ($request->has('estimated_arrival')) {
            $shipment->estimated_arrival = Carbon::parse($request->estimated_arrival);
            $this->etaService->recalculateShipmentDelay($shipment, $request->input('delay_reason'));
        }

        return response()->json([
            'message' => 'Shipment updated successfully',
            'shipment' => $shipment->fresh(),
        ]);
    }

    public function destroy(Request $request, $id)
    {
        $user = $request->user();
        if ($user && ! $user->isAdmin()) {
            return response()->json(['message' => 'Forbidden: administrator privilege required.'], 403);
        }

        $shipment = Shipment::findOrFail($id);
        $shipment->delete();

        return response()->json(['message' => 'Shipment deleted successfully']);
    }

    public function getTracking(Request $request, $id)
    {
        $user = $request->user();
        $shipment = Shipment::findOrFail($id);

        if ($user && $user->isCustomer() && $shipment->customer_id !== $user->id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        return response()->json($shipment->trackingEvents()->with('location')->get());
    }

    public function postTracking(Request $request, $id)
    {
        $user = $request->user();
        if ($user && $user->isCustomer()) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $shipment = Shipment::findOrFail($id);

        $validated = $request->validate([
            'event_type' => ['required', 'string'],
            'location_id' => ['nullable', 'exists:locations,id'],
            'recorded_temp_celsius' => ['nullable', 'numeric'],
            'latitude' => ['nullable', 'numeric'],
            'longitude' => ['nullable', 'numeric'],
            'event_time' => ['required', 'date'],
            'description' => ['required', 'string'],
        ]);

        try {
            $event = $this->trackingService->recordEvent($shipment, $validated, $user);
            return response()->json([
                'message' => 'Tracking event logged',
                'event' => $event,
            ], 201);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    public function getExceptions(Request $request, $id)
    {
        $shipment = Shipment::findOrFail($id);
        return response()->json($shipment->exceptions);
    }

    public function postException(Request $request, $id)
    {
        $user = $request->user();
        if ($user && $user->isCustomer()) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $shipment = Shipment::findOrFail($id);
        $validated = $request->validate([
            'exception_type' => ['required', 'string'],
            'severity' => ['required', 'in:LOW,MEDIUM,HIGH'],
            'detected_at' => ['required', 'date'],
            'description' => ['required', 'string'],
            'resolution_notes' => ['nullable', 'string'],
        ]);

        $exception = $this->exceptionService->createException($shipment, $validated, $user);

        return response()->json([
            'message' => 'Exception logged',
            'exception' => $exception,
        ], 201);
    }

    public function patchException(Request $request, $exceptionId)
    {
        $user = $request->user();
        if ($user && $user->isCustomer()) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $exception = ShipmentException::findOrFail($exceptionId);
        $validated = $request->validate([
            'status' => ['required', 'in:OPEN,INVESTIGATING,RESOLVED'],
            'resolution_notes' => ['nullable', 'string'],
        ]);

        $this->exceptionService->updateStatus(
            $exception,
            $validated['status'],
            $validated['resolution_notes'] ?? null,
            $user
        );

        return response()->json([
            'message' => 'Exception updated',
            'exception' => $exception->fresh(),
        ]);
    }

    public function listCarriers()
    {
        return response()->json(Carrier::all());
    }

    public function storeCarrier(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string'],
            'code' => ['required', 'string', 'unique:carriers,code'],
            'contact_phone' => ['nullable', 'string'],
            'is_cold_chain_certified' => ['boolean'],
        ]);

        $carrier = Carrier::create($validated);
        return response()->json($carrier, 201);
    }

    public function listLocations()
    {
        return response()->json(Location::all());
    }

    public function storeLocation(Request $request)
    {
        $validated = $request->validate([
            'facility_name' => ['required', 'string'],
            'facility_code' => ['required', 'string', 'unique:locations,facility_code'],
            'facility_type' => ['required', 'string'],
            'city' => ['required', 'string'],
            'country' => ['required', 'string'],
            'latitude' => ['required', 'numeric'],
            'longitude' => ['required', 'numeric'],
        ]);

        $location = Location::create($validated);
        return response()->json($location, 201);
    }
}

