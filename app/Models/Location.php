<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Location extends Model
{
    use HasFactory;

    protected $fillable = [
        'facility_name',
        'facility_code',
        'facility_type',
        'city',
        'country',
        'latitude',
        'longitude',
    ];

    protected $casts = [
        'latitude' => 'float',
        'longitude' => 'float',
    ];

    public function outboundShipments(): HasMany
    {
        return $this->hasMany(Shipment::class, 'origin_id');
    }

    public function inboundShipments(): HasMany
    {
        return $this->hasMany(Shipment::class, 'destination_id');
    }

    public function trackingEvents(): HasMany
    {
        return $this->hasMany(TrackingEvent::class);
    }
}

