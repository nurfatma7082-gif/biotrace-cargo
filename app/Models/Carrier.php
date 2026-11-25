<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Carrier extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'contact_phone',
        'contact_email',
        'is_cold_chain_certified',
        'notes',
    ];

    protected $casts = [
        'is_cold_chain_certified' => 'boolean',
    ];

    public function shipments(): HasMany
    {
        return $this->hasMany(Shipment::class);
    }
}

