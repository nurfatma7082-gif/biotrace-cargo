<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ShipmentItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'shipment_id',
        'specimen_type',
        'specimen_identifier',
        'quantity_vials',
        'biohazard_level',
        'required_temp_celsius',
        'storage_instructions',
    ];

    protected $casts = [
        'quantity_vials' => 'integer',
        'required_temp_celsius' => 'float',
    ];

    public function shipment(): BelongsTo
    {
        return $this->belongsTo(Shipment::class);
    }
}

