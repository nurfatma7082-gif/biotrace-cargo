<?php

namespace App\Http\Controllers;

use App\Models\Shipment;
use App\Services\TrackingService;
use Exception;
use Illuminate\Http\Request;

class TrackingEventController extends Controller
{
    public function __construct(
        protected TrackingService $trackingService
    ) {}

    public function store(Request $request, Shipment $shipment)
    {
        $user = $request->user();

        if ($user->isCustomer()) {
            abort(403, 'Customers cannot record operational tracking events.');
        }

        $validated = $request->validate([
            'event_type' => ['required', 'string'],
            'location_id' => ['nullable', 'exists:locations,id'],
            'recorded_temp_celsius' => ['nullable', 'numeric'],
            'event_time' => ['required', 'date'],
            'description' => ['required', 'string', 'max:500'],
            'latitude' => ['nullable', 'numeric'],
            'longitude' => ['nullable', 'numeric'],
        ]);

        try {
            $this->trackingService->recordEvent($shipment, $validated, $user);
            return back()->with('success', 'Tracking event logged and timeline updated.');
        } catch (Exception $e) {
            return back()->withErrors(['event' => $e->getMessage()]);
        }
    }
}

