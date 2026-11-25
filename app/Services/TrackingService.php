<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\Shipment;
use App\Models\ShipmentException;
use App\Models\TrackingEvent;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;

class TrackingService
{
    public function __construct(
        protected ShipmentService $shipmentService,
        protected EtaViabilityService $etaService
    ) {}

    public function recordEvent(
        Shipment $shipment,
        array $data,
        ?User $actor = null
    ): TrackingEvent {
        return DB::transaction(function () use ($shipment, $data, $actor) {
            $eventTime = Carbon::parse($data['event_time'] ?? now());

            // Validate chronological integrity
            $latestEvent = $shipment->latestTrackingEvent;
            if ($latestEvent && $eventTime->lt($latestEvent->event_time)) {
                throw new Exception("Event timestamp ({$eventTime}) cannot precede the previous recorded event timestamp ({$latestEvent->event_time}).");
            }

            $event = TrackingEvent::create([
                'shipment_id' => $shipment->id,
                'recorded_by_user_id' => $actor?->id,
                'location_id' => $data['location_id'] ?? null,
                'event_type' => $data['event_type'],
                'recorded_temp_celsius' => $data['recorded_temp_celsius'] ?? null,
                'latitude' => $data['latitude'] ?? null,
                'longitude' => $data['longitude'] ?? null,
                'event_time' => $eventTime,
                'description' => $data['description'] ?? 'Status update logged.',
            ]);

            // Auto-advance lifecycle if event_type maps to a valid status
            $lifecycleEventMap = [
                'CRYOPACKED' => 'CRYOPACKED',
                'PICKED_UP' => 'PICKED_UP',
                'IN_TRANSIT' => 'IN_TRANSIT',
                'AIRPORT_DEPARTURE' => 'IN_TRANSIT',
                'HUB_TRANSIT' => 'IN_TRANSIT',
                'CUSTOMS_HOLD' => 'CUSTOMS_HOLD',
                'OUT_FOR_DELIVERY' => 'OUT_FOR_DELIVERY',
                'DELIVERED' => 'DELIVERED',
            ];

            if (isset($lifecycleEventMap[$data['event_type']])) {
                $targetStatus = $lifecycleEventMap[$data['event_type']];
                if ($this->shipmentService->canTransition($shipment->status, $targetStatus)) {
                    $this->shipmentService->updateStatus($shipment, $targetStatus, $actor, "Triggered by event {$data['event_type']}");
                }
            }

            // Check for temperature excursion against required specimen storage tier
            if (isset($data['recorded_temp_celsius']) && $shipment->storage_temp === 'DRY_ICE') {
                if ($data['recorded_temp_celsius'] > -65.0) { // Critical cutoff for dry ice
                    ShipmentException::create([
                        'shipment_id' => $shipment->id,
                        'exception_type' => 'TEMPERATURE_EXCURSION',
                        'severity' => 'HIGH',
                        'status' => 'OPEN',
                        'detected_at' => $eventTime,
                        'description' => "Critical temperature excursion detected: {$data['recorded_temp_celsius']}°C exceeds dry ice safety margin (-65°C).",
                        'resolution_notes' => 'Immediate re-icing requested at nearest cold-chain station.',
                    ]);
                }
            }

            AuditLog::create([
                'user_id' => $actor?->id,
                'action' => 'RECORD_TRACKING_EVENT',
                'entity_type' => TrackingEvent::class,
                'entity_id' => $event->id,
                'old_payload' => null,
                'new_payload' => $event->toArray(),
                'ip_address' => request()->ip() ?? '127.0.0.1',
            ]);

            return $event;
        });
    }
}

