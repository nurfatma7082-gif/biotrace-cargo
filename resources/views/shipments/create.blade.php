@extends('layouts.app', ['title' => 'Register Manifest'])

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 pt-8">
    
    <div class="pb-6 border-b border-slate-200">
        <a href="{{ route('shipments.index') }}" class="text-xs font-semibold text-slate-500 hover:text-slate-900">&larr; Back to Shipments</a>
        <h1 class="text-2xl font-bold tracking-tight text-slate-900 mt-2">
            Register Clinical Biospecimen Manifest
        </h1>
        <p class="text-sm text-slate-500 mt-1">
            Specify biological preservation criteria, transport route, and biohazard containment specifications.
        </p>
    </div>

    <form action="{{ route('shipments.store') }}" method="POST" class="mt-8 space-y-8">
        @csrf

        <!-- Section 1: Route & Logistics -->
        <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm space-y-5">
            <h2 class="text-base font-bold text-slate-900 border-b border-slate-100 pb-3">
                1. Transit Corridor & Assigned Bio-Carrier
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-xs font-semibold uppercase text-slate-600 mb-1.5">Origin Clinical Facility</label>
                    <select name="origin_id" required class="w-full text-sm rounded-xl border border-slate-300 p-2.5">
                        <option value="">Select Origin...</option>
                        @foreach($locations as $loc)
                            <option value="{{ $loc->id }}" {{ old('origin_id') == $loc->id ? 'selected' : '' }}>
                                {{ $loc->facility_name }} ({{ $loc->city }}, {{ $loc->country }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase text-slate-600 mb-1.5">Destination Lab / Biobank</label>
                    <select name="destination_id" required class="w-full text-sm rounded-xl border border-slate-300 p-2.5">
                        <option value="">Select Destination...</option>
                        @foreach($locations as $loc)
                            <option value="{{ $loc->id }}" {{ old('destination_id') == $loc->id ? 'selected' : '' }}>
                                {{ $loc->facility_name }} ({{ $loc->city }}, {{ $loc->country }})
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-xs font-semibold uppercase text-slate-600 mb-1.5">Specialized Medical Courier</label>
                    <select name="carrier_id" required class="w-full text-sm rounded-xl border border-slate-300 p-2.5">
                        <option value="">Select Cold-Chain Carrier...</option>
                        @foreach($carriers as $car)
                            <option value="{{ $car->id }}" {{ old('carrier_id') == $car->id ? 'selected' : '' }}>
                                {{ $car->name }} ({{ $car->code }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase text-slate-600 mb-1.5">Total Weight (kg incl. packaging)</label>
                    <input type="number" step="0.1" name="total_weight_kg" required value="{{ old('total_weight_kg', 12.0) }}" class="w-full text-sm rounded-xl border border-slate-300 p-2.5">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-xs font-semibold uppercase text-slate-600 mb-1.5">Expected Departure</label>
                    <input type="datetime-local" name="expected_departure" required value="{{ old('expected_departure', now()->format('Y-m-d\TH:i')) }}" class="w-full text-sm rounded-xl border border-slate-300 p-2.5">
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase text-slate-600 mb-1.5">Expected Arrival (Cutoff SLA)</label>
                    <input type="datetime-local" name="expected_arrival" required value="{{ old('expected_arrival', now()->addHours(24)->format('Y-m-d\TH:i')) }}" class="w-full text-sm rounded-xl border border-slate-300 p-2.5">
                </div>
            </div>
        </div>

        <!-- Section 2: Biological Specimen Manifest -->
        <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm space-y-5">
            <h2 class="text-base font-bold text-slate-900 border-b border-slate-100 pb-3">
                2. Biological Cargo Specifications & Thermal Safeguards
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                <div class="md:col-span-2">
                    <label class="block text-xs font-semibold uppercase text-slate-600 mb-1.5">Biospecimen Classification / Description</label>
                    <input type="text" name="specimen_type" required placeholder="e.g. Hematopoietic Stem Cells (HSC) or RNA-Seq Biopsy" value="{{ old('specimen_type') }}" class="w-full text-sm rounded-xl border border-slate-300 p-2.5">
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase text-slate-600 mb-1.5">Tube / Batch ID</label>
                    <input type="text" name="specimen_identifier" placeholder="e.g. VIAL-994" value="{{ old('specimen_identifier') }}" class="w-full text-sm rounded-xl border border-slate-300 p-2.5 font-mono">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                <div>
                    <label class="block text-xs font-semibold uppercase text-slate-600 mb-1.5">Storage Tier</label>
                    <select name="storage_temp" required class="w-full text-sm rounded-xl border border-slate-300 p-2.5">
                        <option value="CRYO_LN2" {{ old('storage_temp') === 'CRYO_LN2' ? 'selected' : '' }}>Cryo LN2 (-196°C to -150°C)</option>
                        <option value="DRY_ICE" {{ old('storage_temp') === 'DRY_ICE' ? 'selected' : '' }} selected>Dry Ice (-80°C to -20°C)</option>
                        <option value="CHILLED" {{ old('storage_temp') === 'CHILLED' ? 'selected' : '' }}>Chilled (2°C to 8°C)</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase text-slate-600 mb-1.5">Quantity of Vials</label>
                    <input type="number" name="quantity_vials" required min="1" value="{{ old('quantity_vials', 4) }}" class="w-full text-sm rounded-xl border border-slate-300 p-2.5">
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase text-slate-600 mb-1.5">Biohazard Containment Level</label>
                    <select name="biohazard_level" required class="w-full text-sm rounded-xl border border-slate-300 p-2.5 font-mono">
                        <option value="BSL_1">BSL-1 (Minimal Hazard)</option>
                        <option value="BSL_2" selected>BSL-2 (Moderate Clinical Hazard)</option>
                        <option value="BSL_3">BSL-3 (Strict Biohazard Containment)</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold uppercase text-slate-600 mb-1.5">Target Preserved Temperature (°C)</label>
                <input type="number" step="0.1" name="required_temp_celsius" required value="{{ old('required_temp_celsius', -80.0) }}" class="w-full text-sm rounded-xl border border-slate-300 p-2.5 font-mono">
            </div>

            <div>
                <label class="block text-xs font-semibold uppercase text-slate-600 mb-1.5">Handling Protocols & Emergency Actions</label>
                <textarea name="storage_instructions" rows="3" placeholder="e.g. Keep upright. In event of delay >12h, replenish 5kg dry ice at transit hub." class="w-full text-sm rounded-xl border border-slate-300 p-2.5">{{ old('storage_instructions') }}</textarea>
            </div>
        </div>

        <div class="flex items-center justify-end space-x-3">
            <a href="{{ route('shipments.index') }}" class="px-5 py-2.5 rounded-xl border border-slate-300 text-slate-700 text-sm font-semibold hover:bg-slate-50 transition">
                Cancel
            </a>
            <button type="submit" class="px-6 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-sm font-bold shadow-md shadow-emerald-600/20 transition">
                Register & Initialize Manifest
            </button>
        </div>
    </form>

</div>
@endsection

