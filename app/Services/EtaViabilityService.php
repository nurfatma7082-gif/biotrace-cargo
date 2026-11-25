<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\Shipment;
use Carbon\Carbon;

class EtaViabilityService
{
    /**
     * Threshold constants in hours
     */
    public const THRESHOLD_LOW = 6;
    public const THRESHOLD_MEDIUM = 12;

    public function evaluateDelay(Carbon $expectedArrival, Carbon $estimatedArrival): array
    {
        $diffMinutes = $expectedArrival->diffInMinutes($estimatedArrival, false);
        $delayHours = max(0, (int) round($diffMinutes / 60));
        $isDelayed = $diffMinutes > 0;

        $severity = 'NONE';
        if ($isDelayed) {
            if ($delayHours <= self::THRESHOLD_LOW) {
                $severity = 'LOW';
            } elseif ($delayHours <= self::THRESHOLD_MEDIUM) {
                $severity = 'MEDIUM';
            } else {
                $severity = 'HIGH';
            }
        }

        return [
            'is_delayed' => $isDelayed,
            'delay_hours' => $delayHours,
            'severity' => $severity,
        ];
    }

    public function recalculateShipmentDelay(Shipment $shipment, ?string $reason = null): Shipment
    {
        $evaluation = $this->evaluateDelay($shipment->expected_arrival, $shipment->estimated_arrival);

        $wasHigh = $shipment->delay_severity === 'HIGH';
        
        $shipment->is_delayed = $evaluation['is_delayed'];
        $shipment->delay_hours = $evaluation['delay_hours'];
        $shipment->delay_severity = $evaluation['severity'];
        if ($reason) {
            $shipment->delay_reason = $reason;
        }
        $shipment->save();

        // If newly escalated to HIGH, generate critical notification to customer
        if ($evaluation['severity'] === 'HIGH' && ! $wasHigh) {
            Notification::create([
                'user_id' => $shipment->customer_id,
                'shipment_id' => $shipment->id,
                'title' => "High Thermal Degradation Risk: {$shipment->shipment_number}",
                'message' => "Estimated arrival delayed by {$shipment->delay_hours} hours. Specimen viability limits at risk. Emergency replenishment required.",
                'alert_type' => 'CRITICAL',
                'is_read' => false,
            ]);
        }

        return $shipment;
    }
}

