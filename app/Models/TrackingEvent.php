<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TrackingEvent extends Model
{
    use HasFactory;

    protected $fillable = [
        'shipment_id',
        'recorded_by_user_id',
        'location_id',
        'event_type',
        'recorded_temp_celsius',
        'latitude',
        'longitude',
        'event_time',
        'description',
    ];

    protected $casts = [
        'recorded_temp_celsius' => 'float',
        'latitude' => 'float',
        'longitude' => 'float',
        'event_time' => 'datetime',
    ];

    public function shipment(): BelongsTo
    {
        return $this->belongsTo(Shipment::class);
    }

    public function recordedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by_user_id');
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }
}

