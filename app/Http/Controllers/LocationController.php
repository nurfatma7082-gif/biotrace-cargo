<?php

namespace App\Http\Controllers;

use App\Models\Location;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    public function index()
    {
        $locations = Location::withCount(['outboundShipments', 'inboundShipments'])->get();
        return view('locations.index', compact('locations'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'facility_name' => ['required', 'string', 'max:255'],
            'facility_code' => ['required', 'string', 'max:30', 'unique:locations,facility_code'],
            'facility_type' => ['required', 'in:HOSPITAL,RESEARCH_LAB,AIRPORT_HUB,BIOBANK'],
            'city' => ['required', 'string', 'max:100'],
            'country' => ['required', 'string', 'max:100'],
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
        ]);

        Location::create($validated);

        return back()->with('success', 'Medical facility / transit node registered.');
    }
}

