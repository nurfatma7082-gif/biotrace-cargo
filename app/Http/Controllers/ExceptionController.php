<?php

namespace App\Http\Controllers;

use App\Models\Shipment;
use App\Models\ShipmentException;
use App\Services\ExceptionService;
use Exception;
use Illuminate\Http\Request;

class ExceptionController extends Controller
{
    public function __construct(
        protected ExceptionService $exceptionService
    ) {}

    public function index(Request $request)
    {
        $user = $request->user();
        $query = ShipmentException::with(['shipment.origin', 'shipment.destination', 'shipment.carrier'])
            ->latest('detected_at');

        if ($user->isCustomer()) {
            $query->whereHas('shipment', function ($q) use ($user) {
                $q->where('customer_id', $user->id);
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('severity')) {
            $query->where('severity', $request->severity);
        }

        $exceptions = $query->paginate(12)->withQueryString();

        return view('exceptions.index', compact('exceptions'));
    }

    public function store(Request $request, Shipment $shipment)
    {
        $user = $request->user();
        if ($user->isCustomer()) {
            abort(403, 'Unauthorized.');
        }

        $validated = $request->validate([
            'exception_type' => ['required', 'string'],
            'severity' => ['required', 'in:LOW,MEDIUM,HIGH'],
            'detected_at' => ['required', 'date'],
            'description' => ['required', 'string'],
            'resolution_notes' => ['nullable', 'string'],
        ]);

        $this->exceptionService->createException($shipment, $validated, $user);

        return back()->with('success', 'Incident exception flagged and logged.');
    }

    public function update(Request $request, ShipmentException $exception)
    {
        $user = $request->user();
        if ($user->isCustomer()) {
            abort(403, 'Unauthorized.');
        }

        $validated = $request->validate([
            'status' => ['required', 'in:OPEN,INVESTIGATING,RESOLVED'],
            'resolution_notes' => ['nullable', 'string'],
        ]);

        $this->exceptionService->updateStatus(
            $exception,
            $validated['status'],
            $validated['resolution_notes'],
            $user
        );

        return back()->with('success', "Exception status updated to {$validated['status']}.");
    }
}

