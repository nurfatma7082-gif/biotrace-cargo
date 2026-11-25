@extends('layouts.app', ['title' => 'Facilities & Nodes'])

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-8">
    
    <div class="flex flex-col md:flex-row md:items-center justify-between pb-6 border-b border-slate-200">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900">
                Clinical Facilities & Airport Hub Nodes
            </h1>
            <p class="mt-1 text-sm text-slate-500">
                Origin laboratories, coolport transit hubs, and biobank destinations with GPS coordinates.
            </p>
        </div>
        <button onclick="document.getElementById('modal-location').classList.remove('hidden')" class="mt-4 md:mt-0 px-4 py-2 rounded-xl text-sm font-semibold text-white bg-slate-900 hover:bg-slate-800 transition">
            + Register Facility
        </button>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mt-8">
        @foreach($locations as $loc)
        <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-sm flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between">
                    <span class="px-2 py-0.5 rounded bg-slate-100 font-mono font-bold text-xs text-slate-700">
                        {{ $loc->facility_code }}
                    </span>
                    <span class="px-2 py-0.5 rounded-full text-[11px] font-bold
                        {{ $loc->facility_type === 'HOSPITAL' ? 'bg-purple-100 text-purple-800' : ($loc->facility_type === 'AIRPORT_HUB' ? 'bg-sky-100 text-sky-800' : 'bg-emerald-100 text-emerald-800') }}">
                        {{ $loc->facility_type }}
                    </span>
                </div>

                <h3 class="text-base font-bold text-slate-900 mt-3">{{ $loc->facility_name }}</h3>
                <p class="text-xs text-slate-500 mt-0.5">{{ $loc->city }}, {{ $loc->country }}</p>

                <div class="mt-4 p-3 rounded-xl bg-slate-50 border border-slate-100 font-mono text-xs text-slate-600 flex justify-between">
                    <span>GPS Lat: {{ $loc->latitude }}</span>
                    <span>Lon: {{ $loc->longitude }}</span>
                </div>
            </div>

            <div class="mt-6 pt-4 border-t border-slate-100 flex items-center justify-between text-xs text-slate-400">
                <span>Shipments Handled</span>
                <span class="font-bold text-slate-900 text-sm">{{ $loc->outbound_shipments_count + $loc->inbound_shipments_count }}</span>
            </div>
        </div>
        @endforeach
    </div>

</div>

<!-- Add Location Modal -->
<div id="modal-location" class="hidden fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl border border-slate-100">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-base font-bold text-slate-900">Register Medical Facility / Hub</h3>
            <button onclick="document.getElementById('modal-location').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 font-bold">&times;</button>
        </div>
        <form action="{{ route('locations.store') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-semibold uppercase text-slate-600 mb-1">Facility Name</label>
                <input type="text" name="facility_name" required placeholder="e.g. Johns Hopkins Biobank" class="w-full text-sm rounded-xl border border-slate-300 p-2.5">
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold uppercase text-slate-600 mb-1">Facility Code</label>
                    <input type="text" name="facility_code" required placeholder="JHU-BIO-01" class="w-full text-sm rounded-xl border border-slate-300 p-2.5 font-mono">
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase text-slate-600 mb-1">Type</label>
                    <select name="facility_type" required class="w-full text-sm rounded-xl border border-slate-300 p-2.5">
                        <option value="HOSPITAL">HOSPITAL</option>
                        <option value="RESEARCH_LAB">RESEARCH_LAB</option>
                        <option value="AIRPORT_HUB">AIRPORT_HUB</option>
                        <option value="BIOBANK">BIOBANK</option>
                    </select>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold uppercase text-slate-600 mb-1">City</label>
                    <input type="text" name="city" required placeholder="Baltimore" class="w-full text-sm rounded-xl border border-slate-300 p-2.5">
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase text-slate-600 mb-1">Country</label>
                    <input type="text" name="country" required placeholder="United States" class="w-full text-sm rounded-xl border border-slate-300 p-2.5">
                </div>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold uppercase text-slate-600 mb-1">Latitude</label>
                    <input type="number" step="0.000001" name="latitude" required placeholder="39.297" class="w-full text-sm rounded-xl border border-slate-300 p-2.5 font-mono">
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase text-slate-600 mb-1">Longitude</label>
                    <input type="number" step="0.000001" name="longitude" required placeholder="-76.592" class="w-full text-sm rounded-xl border border-slate-300 p-2.5 font-mono">
                </div>
            </div>
            <button type="submit" class="w-full py-2.5 bg-slate-900 hover:bg-slate-800 text-white rounded-xl text-sm font-bold shadow">
                Save Facility Node
            </button>
        </form>
    </div>
</div>
@endsection

