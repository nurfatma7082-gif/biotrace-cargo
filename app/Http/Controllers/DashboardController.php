<?php

namespace App\Http\Controllers;

use App\Models\Carrier;
use App\Models\Location;
use App\Models\Shipment;
use App\Models\ShipmentException;
use App\Models\TrackingEvent;
use App\Services\WeatherService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __construct(
        protected WeatherService $weatherService
    ) {}

    public function index(Request $request)
    {
        $user = $request->user();

        $query = Shipment::query();

        // If Customer, restrict to own shipments
        if ($user->isCustomer()) {
            $query->where('customer_id', $user->id);
        }

        $totalShipments = (clone $query)->count();
        $inTransitCount = (clone $query)->whereIn('status', ['IN_TRANSIT', 'PICKED_UP', 'OUT_FOR_DELIVERY', 'CUSTOMS_HOLD'])->count();
        $deliveredCount = (clone $query)->where('status', 'DELIVERED')->count();
        $delayedCount = (clone $query)->where('is_delayed', true)->count();
        
        $exceptionsQuery = ShipmentException::query();
        if ($user->isCustomer()) {
            $exceptionsQuery->whereHas('shipment', function ($q) use ($user) {
                $q->where('customer_id', $user->id);
            });
        }
        $openExceptionsCount = $exceptionsQuery->whereIn('status', ['OPEN', 'INVESTIGATING'])->count();

        // Status breakdown
        $statusBreakdown = (clone $query)
            ->selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        // Temp storage breakdown
        $tempBreakdown = (clone $query)
            ->selectRaw('storage_temp, count(*) as count')
            ->groupBy('storage_temp')
            ->pluck('count', 'storage_temp')
            ->toArray();

        // Recent tracking events
        $recentEventsQuery = TrackingEvent::with(['shipment', 'location', 'recordedBy'])
            ->latest('event_time')
            ->take(6);

        if ($user->isCustomer()) {
            $recentEventsQuery->whereHas('shipment', function ($q) use ($user) {
                $q->where('customer_id', $user->id);
            });
        }
        $recentEvents = $recentEventsQuery->get();

        // High priority delayed/exceptional shipments
        $criticalShipments = (clone $query)
            ->with(['origin', 'destination', 'carrier', 'openExceptions'])
            ->where(function ($q) {
                $q->where('delay_severity', 'HIGH')
                  ->orWhereHas('exceptions', fn($sub) => $sub->where('status', 'OPEN'));
            })
            ->latest()
            ->take(5)
            ->get();

        // Weather sample at primary hubs
        $weatherHubs = [
            'Jakarta' => $this->weatherService->getWeatherAt(-6.1970, 106.8480),
            'Singapore' => $this->weatherService->getWeatherAt(1.3644, 103.9915),
            'Frankfurt' => $this->weatherService->getWeatherAt(50.0379, 8.5622),
        ];

        return view('dashboard.index', compact(
            'totalShipments',
            'inTransitCount',
            'deliveredCount',
            'delayedCount',
            'openExceptionsCount',
            'statusBreakdown',
            'tempBreakdown',
            'recentEvents',
            'criticalShipments',
            'weatherHubs'
        ));
    }
}

