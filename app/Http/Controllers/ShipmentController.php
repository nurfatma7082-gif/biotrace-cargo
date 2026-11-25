<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Carrier;
use App\Models\Location;
use App\Models\Shipment;
use App\Models\ShipmentItem;
use App\Services\EtaViabilityService;
use App\Services\ShipmentService;
use App\Services\WeatherService;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ShipmentController extends Controller
{
    public function __construct(
        protected ShipmentService $shipmentService,
        protected EtaViabilityService $etaService,
        protected WeatherService $weatherService
    ) {}

    public function index(Request $request)
    {
        $user = $request->user();
        $query = Shipment::with(['customer', 'carrier', 'origin', 'destination', 'openExceptions'])
            ->latest();

        // Customer can only view their own shipments
        if ($user->isCustomer()) {
            $query->where('customer_id', $user->id);
        }

        // Filters per brief Slide 10
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('shipment_number', 'like', "%{$search}%")
                  ->orWhereHas('items', fn($sub) => $sub->where('specimen_type', 'like', "%{$search}%"));
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('carrier_id')) {
            $query->where('carrier_id', $request->carrier_id);
        }

        if ($request->filled('storage_temp')) {
            $query->where('storage_temp', $request->storage_temp);
        }

        if ($request->filled('is_delayed')) {
            $query->where('is_delayed', $request->boolean('is_delayed'));
        }

        $shipments = $query->paginate(10)->withQueryString();
        $carriers = Carrier::all();
        $locations = Location::all();

        return view('shipments.index', compact('shipments', 'carriers', 'locations'));
    }

    public function create()
    {
        $carriers = Carrier::all();
        $locations = Location::all();

        return view('shipments.create', compact('carriers', 'locations'));
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
            // Specimen item
            'specimen_type' => ['required', 'string', 'max:255'],
            'specimen_identifier' => ['nullable', 'string', 'max:50'],
            'quantity_vials' => ['required', 'integer', 'min:1'],
            'biohazard_level' => ['required', 'in:BSL_1,BSL_2,BSL_3'],
            'required_temp_celsius' => ['required', 'numeric'],
            'storage_instructions' => ['nullable', 'string'],
        ]);

        $shipment = DB::transaction(function () use ($validated, $user) {
            $expectedArrival = Carbon::parse($validated['expected_arrival']);
            $shipmentNumber = $this->shipmentService->generateShipmentNumber();

            $shipment = Shipment::create([
                'shipment_number' => $shipmentNumber,
                'customer_id' => $user->isCustomer() ? $user->id : ($user->id), // if operator, assigns on behalf
                'carrier_id' => $validated['carrier_id'],
                'origin_id' => $validated['origin_id'],
                'destination_id' => $validated['destination_id'],
                'storage_temp' => $validated['storage_temp'],
                'status' => 'REGISTERED',
                'total_weight_kg' => $validated['total_weight_kg'],
                'expected_departure' => $validated['expected_departure'],
                'expected_arrival' => $expectedArrival,
                'estimated_arrival' => $expectedArrival, // initial estimated = expected
                'is_delayed' => false,
                'delay_hours' => 0,
                'delay_severity' => 'NONE',
            ]);

            ShipmentItem::create([
                'shipment_id' => $shipment->id,
                'specimen_type' => $validated['specimen_type'],
                'specimen_identifier' => $validated['specimen_identifier'] ?? 'SMP-' . strtoupper(bin2hex(random_bytes(3))),
                'quantity_vials' => $validated['quantity_vials'],
                'biohazard_level' => $validated['biohazard_level'],
                'required_temp_celsius' => $validated['required_temp_celsius'],
                'storage_instructions' => $validated['storage_instructions'],
            ]);

            AuditLog::create([
                'user_id' => $user->id,
                'action' => 'CREATE_SHIPMENT',
                'entity_type' => Shipment::class,
                'entity_id' => $shipment->id,
                'old_payload' => null,
                'new_payload' => $shipment->toArray(),
                'ip_address' => request()->ip() ?? '127.0.0.1',
            ]);

            return $shipment;
        });

        return redirect()->route('shipments.show', $shipment)
            ->with('success', "Biomedical shipment {$shipment->shipment_number} registered successfully.");
    }

    public function show(Shipment $shipment, Request $request)
    {
        $user = $request->user();

        // Customer access verification
        if ($user->isCustomer() && $shipment->customer_id !== $user->id) {
            abort(403, 'Unauthorized access: you may only inspect your own clinical specimens.');
        }

        $shipment->load([
            'customer',
            'carrier',
            'origin',
            'destination',
            'items',
            'trackingEvents.location',
            'trackingEvents.recordedBy',
            'exceptions',
        ]);

        $locations = Location::all();

        // Fetch live weather at destination
        $destinationWeather = null;
        if ($shipment->destination) {
            $destinationWeather = $this->weatherService->getWeatherAt(
                $shipment->destination->latitude,
                $shipment->destination->longitude
            );
        }

        return view('shipments.show', compact('shipment', 'locations', 'destinationWeather'));
    }

    public function updateStatus(Request $request, Shipment $shipment)
    {
        $user = $request->user();

        // Only Operator or Admin can transition status
        if ($user->isCustomer()) {
            abort(403, 'Customers cannot manually transition shipment status.');
        }

        $validated = $request->validate([
            'status' => ['required', 'string'],
            'reason' => ['nullable', 'string', 'max:255'],
        ]);

        try {
            $this->shipmentService->updateStatus($shipment, $validated['status'], $user, $validated['reason']);
            return back()->with('success', "Status transitioned to {$validated['status']}.");
        } catch (Exception $e) {
            return back()->withErrors(['status' => $e->getMessage()]);
        }
    }

    public function updateEta(Request $request, Shipment $shipment)
    {
        $user = $request->user();
        if ($user->isCustomer()) {
            abort(403, 'Customers cannot modify shipment ETA.');
        }

        $validated = $request->validate([
            'estimated_arrival' => ['required', 'date'],
            'delay_reason' => ['nullable', 'string'],
        ]);

        $shipment->estimated_arrival = Carbon::parse($validated['estimated_arrival']);
        $this->etaService->recalculateShipmentDelay($shipment, $validated['delay_reason']);

        return back()->with('success', 'ETA updated and thermal degradation risk re-evaluated.');
    }

    public function destroy(Shipment $shipment, Request $request)
    {
        $user = $request->user();
        if (! $user->isAdmin()) {
            abort(403, 'Only Administrators can purge shipment manifests.');
        }

        $shipmentNumber = $shipment->shipment_number;
        $shipment->delete();

        return redirect()->route('shipments.index')
            ->with('success', "Shipment {$shipmentNumber} purged.");
    }
}

