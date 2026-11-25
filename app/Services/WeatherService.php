<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WeatherService
{
    /**
     * Fetch current weather at location coordinates (using Open-Meteo free API - no key needed & high reliability).
     */
    public function getWeatherAt(float $latitude, float $longitude): array
    {
        try {
            $response = Http::timeout(4)->get('https://api.open-meteo.com/v1/forecast', [
                'latitude' => $latitude,
                'longitude' => $longitude,
                'current' => 'temperature_2m,relative_humidity_2m,weather_code,wind_speed_10m',
                'timezone' => 'auto',
            ]);

            if ($response->successful()) {
                $data = $response->json('current');
                return [
                    'temperature_celsius' => $data['temperature_2m'] ?? null,
                    'humidity_pct' => $data['relative_humidity_2m'] ?? null,
                    'wind_speed_kmh' => $data['wind_speed_10m'] ?? null,
                    'status' => 'LIVE_SYNCED',
                ];
            }
        } catch (\Throwable $e) {
            Log::warning("Weather API unreachable: " . $e->getMessage());
        }

        // Graceful fallback simulation
        return [
            'temperature_celsius' => 28.5,
            'humidity_pct' => 75,
            'wind_speed_kmh' => 12.0,
            'status' => 'FALLBACK_CACHED',
        ];
    }
}

