<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\Shipment;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\DB;

class ShipmentService
{
    /**
     * Directed Acyclic Graph of allowed status transitions for biomedical shipments.
     */
    protected array $allowedTransitions = [
        'REGISTERED' => ['CRYOPACKED', 'CANCELLED'],
        'CRYOPACKED' => ['PICKED_UP', 'CANCELLED'],
        'PICKED_UP' => ['IN_TRANSIT', 'CANCELLED'],
        'IN_TRANSIT' => ['CUSTOMS_HOLD', 'OUT_FOR_DELIVERY', 'CANCELLED'],
        'CUSTOMS_HOLD' => ['IN_TRANSIT', 'CANCELLED'],
        'OUT_FOR_DELIVERY' => ['DELIVERED', 'CANCELLED'],
        'DELIVERED' => [], // Terminal state
        'CANCELLED' => [], // Terminal state
    ];

    public function canTransition(string $currentStatus, string $newStatus): bool
    {
        if ($currentStatus === $newStatus) {
            return true;
        }

        return in_array($newStatus, $this->allowedTransitions[$currentStatus] ?? [], true);
    }

    public function updateStatus(Shipment $shipment, string $newStatus, ?User $actor = null, ?string $reason = null): Shipment
    {
        if ($shipment->isDelivered() || $shipment->isCancelled()) {
            throw new Exception("Shipment is in a terminal status ({$shipment->status}) and cannot be modified.");
        }

        if (! $this->canTransition($shipment->status, $newStatus)) {
            throw new Exception("Invalid biomedical lifecycle status transition from '{$shipment->status}' to '{$newStatus}'.");
        }

        return DB::transaction(function () use ($shipment, $newStatus, $actor, $reason) {
            $oldStatus = $shipment->status;
            $shipment->status = $newStatus;
            $shipment->save();

            AuditLog::create([
                'user_id' => $actor?->id,
                'action' => 'SHIPMENT_STATUS_TRANSITION',
                'entity_type' => Shipment::class,
                'entity_id' => $shipment->id,
                'old_payload' => ['status' => $oldStatus],
                'new_payload' => ['status' => $newStatus, 'reason' => $reason],
                'ip_address' => request()->ip() ?? '127.0.0.1',
            ]);

            return $shipment;
        });
    }

    public function generateShipmentNumber(): string
    {
        $year = date('Y');
        $random = strtoupper(bin2hex(random_bytes(2)));
        $count = Shipment::whereYear('created_at', $year)->count() + 1;
        
        return sprintf('BIO-%s-%04d-%s', $year, $count, $random);
    }
}

