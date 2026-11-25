<?php

namespace App\Http\Controllers;

use App\Models\Carrier;
use Illuminate\Http\Request;

class CarrierController extends Controller
{
    public function index()
    {
        $carriers = Carrier::withCount('shipments')->get();
        return view('carriers.index', compact('carriers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:20', 'unique:carriers,code'],
            'contact_phone' => ['nullable', 'string', 'max:50'],
            'contact_email' => ['nullable', 'email', 'max:255'],
            'is_cold_chain_certified' => ['boolean'],
            'notes' => ['nullable', 'string'],
        ]);

        $validated['is_cold_chain_certified'] = $request->boolean('is_cold_chain_certified');

        Carrier::create($validated);

        return back()->with('success', 'Medical carrier added successfully.');
    }
}

