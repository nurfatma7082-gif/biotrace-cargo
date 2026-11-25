@extends('layouts.app', ['title' => 'Clinical Shipments'])

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-8">
    
    <div class="flex flex-col md:flex-row md:items-center justify-between pb-6 border-b border-slate-200">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900">
                Biological Shipment Manifests
            </h1>
            <p class="mt-1 text-sm text-slate-500">
                Track, filter, and inspect sensitive clinical specimens across active transit corridors.
            </p>
        </div>
        @if(!Auth::user()->isCustomer())
        <div class="mt-4 md:mt-0">
            <a href="{{ route('shipments.create') }}" class="inline-flex items-center px-4 py-2 rounded-xl text-sm font-semibold text-white bg-emerald-600 hover:bg-emerald-500 shadow-md shadow-emerald-600/20 transition">
                + Register New Manifest
            </a>
        </div>
        @endif
    </div>

    <!-- Filters & Search Toolbar (Brief Slide 10) -->
    <div class="mt-6 bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm">
        <form method="GET" action="{{ route('shipments.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
            
            <!-- Search Text -->
            <div class="lg:col-span-2">
                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1">Search Identifier</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Shipment No (e.g. BIO-2026) or Specimen..."
                    class="w-full text-sm rounded-xl border border-slate-300 px-3.5 py-2 focus:border-emerald-500 focus:ring-emerald-500">
            </div>

            <!-- Status -->
            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1">Transit Status</label>
                <select name="status" class="w-full text-sm rounded-xl border border-slate-300 px-3 py-2 focus:border-emerald-500 focus:ring-emerald-500">
                    <option value="">All Statuses</option>
                    @foreach(['REGISTERED', 'CRYOPACKED', 'PICKED_UP', 'IN_TRANSIT', 'CUSTOMS_HOLD', 'OUT_FOR_DELIVERY', 'DELIVERED', 'CANCELLED'] as $st)
                        <option value="{{ $st }}" {{ request('status') === $st ? 'selected' : '' }}>{{ $st }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Storage Temp -->
            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1">Thermal Tier</label>
                <select name="storage_temp" class="w-full text-sm rounded-xl border border-slate-300 px-3 py-2 focus:border-emerald-500 focus:ring-emerald-500">
                    <option value="">All Temperatures</option>
                    <option value="CRYO_LN2" {{ request('storage_temp') === 'CRYO_LN2' ? 'selected' : '' }}>Cryo LN2 (-196°C)</option>
                    <option value="DRY_ICE" {{ request('storage_temp') === 'DRY_ICE' ? 'selected' : '' }}>Dry Ice (-80°C)</option>
                    <option value="CHILLED" {{ request('storage_temp') === 'CHILLED' ? 'selected' : '' }}>Chilled (2-8°C)</option>
                </select>
            </div>

            <!-- Buttons -->
            <div class="flex items-end space-x-2">
                <button type="submit" class="flex-1 py-2 px-4 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-sm font-semibold transition">
                    Filter
                </button>
                <a href="{{ route('shipments.index') }}" class="py-2 px-3 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 text-sm font-medium transition">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Data Table -->
    <div class="mt-6 bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50 text-[11px] uppercase tracking-wider text-slate-400 font-semibold border-b border-slate-100">
                    <tr>
                        <th class="px-6 py-3.5">Shipment Number</th>
                        <th class="px-6 py-3.5">Origin &rarr; Destination</th>
                        <th class="px-6 py-3.5">Preservation Tier</th>
                        <th class="px-6 py-3.5">Status</th>
                        <th class="px-6 py-3.5">ETA & Delay</th>
                        <th class="px-6 py-3.5">Client Lab</th>
                        <th class="px-6 py-3.5 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($shipments as $shipment)
                    <tr class="hover:bg-slate-50/80 transition">
                        <td class="px-6 py-4 font-mono font-bold text-slate-900">
                            <a href="{{ route('shipments.show', $shipment) }}" class="text-emerald-600 hover:underline">
                                {{ $shipment->shipment_number }}
                            </a>
                            @if($shipment->openExceptions->count() > 0)
                            <span class="inline-block ml-1 text-xs text-rose-500 font-bold" title="Open Exception">🚨</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <div class="font-medium text-slate-800">{{ $shipment->origin->city }} &rarr; {{ $shipment->destination->city }}</div>
                            <div class="text-xs text-slate-400">{{ $shipment->carrier->name ?? 'Standard BioCarrier' }}</div>
                        </td>
                        <td class="px-6 py-4">
                            @if($shipment->storage_temp === 'CRYO_LN2')
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold bg-cyan-100 text-cyan-800">
                                    ❄️ CRYO (-196°C)
                                </span>
                            @elseif($shipment->storage_temp === 'DRY_ICE')
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold bg-indigo-100 text-indigo-800">
                                    🧊 DRY ICE (-80°C)
                                </span>
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold bg-emerald-100 text-emerald-800">
                                    🌡️ CHILLED (2-8°C)
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold
                                {{ $shipment->status === 'DELIVERED' ? 'bg-emerald-100 text-emerald-800' : ($shipment->status === 'CUSTOMS_HOLD' ? 'bg-amber-100 text-amber-800' : 'bg-sky-100 text-sky-800') }}">
                                {{ $shipment->status }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-xs font-medium text-slate-700">
                                {{ $shipment->estimated_arrival->format('d M Y, H:i') }}
                            </div>
                            @if($shipment->is_delayed)
                                <div class="text-xs font-bold text-rose-600 mt-0.5">
                                    Delayed +{{ $shipment->delay_hours }}h ({{ $shipment->delay_severity }})
                                </div>
                            @else
                                <div class="text-[11px] text-emerald-600 font-semibold mt-0.5">On Schedule</div>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-xs text-slate-600">
                            {{ Str::limit($shipment->customer->organization ?? $shipment->customer->name, 22) }}
                        </td>
                        <td class="px-6 py-4 text-right">
                            <a href="{{ route('shipments.show', $shipment) }}" class="px-3 py-1.5 rounded-lg bg-emerald-50 hover:bg-emerald-100 text-emerald-700 text-xs font-semibold transition">
                                View Details &rarr;
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-slate-400 text-sm">
                            No shipments match the selected filters.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($shipments->hasPages())
        <div class="px-6 py-4 border-t border-slate-100">
            {{ $shipments->links() }}
        </div>
        @endif
    </div>

</div>
@endsection

