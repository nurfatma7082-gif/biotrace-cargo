<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\Notification;
use App\Models\Shipment;
use App\Models\ShipmentException;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\DB;

class ExceptionService
{
    public function createException(Shipment $shipment, array $data, ?User $actor = null): ShipmentException
    {
        return DB::transaction(function () use ($shipment, $data, $actor) {
            $exception = ShipmentException::create([
                'shipment_id' => $shipment->id,
                'exception_type' => $data['exception_type'],
                'severity' => $data['severity'] ?? 'MEDIUM',
                'status' => 'OPEN',
                'detected_at' => $data['detected_at'] ?? now(),
                'description' => $data['description'],
                'resolution_notes' => $data['resolution_notes'] ?? null,
            ]);

            // Notify customer if severity is MEDIUM or HIGH
            if (in_array($exception->severity, ['MEDIUM', 'HIGH'])) {
                Notification::create([
                    'user_id' => $shipment->customer_id,
                    'shipment_id' => $shipment->id,
                    'title' => "Shipment Exception Alert ({$exception->exception_type})",
                    'message' => "Shipment {$shipment->shipment_number}: {$exception->description}",
                    'alert_type' => $exception->severity === 'HIGH' ? 'CRITICAL' : 'WARNING',
                    'is_read' => false,
                ]);
            }

            AuditLog::create([
                'user_id' => $actor?->id,
                'action' => 'RAISE_EXCEPTION',
                'entity_type' => ShipmentException::class,
                'entity_id' => $exception->id,
                'old_payload' => null,
                'new_payload' => $exception->toArray(),
                'ip_address' => request()->ip() ?? '127.0.0.1',
            ]);

            return $exception;
        });
    }

    public function updateStatus(
        ShipmentException $exception,
        string $status,
        ?string $resolutionNotes = null,
        ?User $actor = null
    ): ShipmentException {
        if (! in_array($status, ['OPEN', 'INVESTIGATING', 'RESOLVED'])) {
            throw new Exception("Invalid exception status: {$status}");
        }

        return DB::transaction(function () use ($exception, $status, $resolutionNotes, $actor) {
            $oldPayload = $exception->toArray();

            $exception->status = $status;
            if ($resolutionNotes) {
                $exception->resolution_notes = $resolutionNotes;
            }
            if ($status === 'RESOLVED') {
                $exception->resolved_at = now();
            }
            $exception->save();

            AuditLog::create([
                'user_id' => $actor?->id,
                'action' => 'UPDATE_EXCEPTION_STATUS',
                'entity_type' => ShipmentException::class,
                'entity_id' => $exception->id,
                'old_payload' => $oldPayload,
                'new_payload' => $exception->toArray(),
                'ip_address' => request()->ip() ?? '127.0.0.1',
            ]);

            return $exception;
        });
    }
}

