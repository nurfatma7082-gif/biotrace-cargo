@extends('layouts.app', ['title' => 'Operational Dashboard'])

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-8">
    
    <!-- Top Welcome & Status Banner -->
    <div class="flex flex-col md:flex-row md:items-center justify-between pb-6 border-b border-slate-200">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900">
                Cold-Chain Biospecimen Operations
            </h1>
            <p class="mt-1 text-sm text-slate-500">
                Real-time thermal monitoring, viability risk modeling & shipment visibility across global nodes.
            </p>
        </div>
        <div class="mt-4 md:mt-0 flex items-center space-x-3">
            @if(!Auth::user()->isCustomer())
            <a href="{{ route('shipments.create') }}" class="inline-flex items-center px-4 py-2 rounded-xl text-sm font-semibold text-white bg-emerald-600 hover:bg-emerald-500 shadow-md shadow-emerald-600/20 transition">
                <span class="mr-1.5 text-base">+</span> Register New Shipment
            </a>
            @endif
        </div>
    </div>

    <!-- 4 Core Metric KPI Cards (Brief Slide 10) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mt-6">
        <!-- Total Shipments -->
        <div class="p-5 bg-white rounded-2xl border border-slate-200/80 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Total Monitored</p>
                <p class="text-3xl font-black text-slate-900 mt-1">{{ $totalShipments }}</p>
                <p class="text-xs text-slate-500 mt-1">Across all international lanes</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-slate-100 flex items-center justify-center text-xl">
                📦
            </div>
        </div>

        <!-- In-Transit -->
        <div class="p-5 bg-white rounded-2xl border border-slate-200/80 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-sky-500">Active In Transit</p>
                <p class="text-3xl font-black text-slate-900 mt-1">{{ $inTransitCount }}</p>
                <p class="text-xs text-sky-600 font-medium mt-1">Live active telemetry</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-sky-50 text-sky-600 flex items-center justify-center text-xl">
                ✈️
            </div>
        </div>

        <!-- Delayed Shipments -->
        <div class="p-5 bg-white rounded-2xl border border-slate-200/80 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-amber-500">Delayed Shipments</p>
                <p class="text-3xl font-black {{ $delayedCount > 0 ? 'text-amber-600' : 'text-slate-900' }} mt-1">{{ $delayedCount }}</p>
                <p class="text-xs text-slate-500 mt-1">
                    {{ $totalShipments > 0 ? round(($delayedCount / $totalShipments) * 100, 1) : 0 }}% of total volume
                </p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-xl">
                ⏱️
            </div>
        </div>

        <!-- Open Exceptions -->
        <div class="p-5 bg-white rounded-2xl border border-slate-200/80 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-rose-500">Open Exceptions</p>
                <p class="text-3xl font-black {{ $openExceptionsCount > 0 ? 'text-rose-600' : 'text-slate-900' }} mt-1">{{ $openExceptionsCount }}</p>
                <p class="text-xs text-rose-600 font-medium mt-1">Requires coordinator action</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center text-xl">
                🚨
            </div>
        </div>
    </div>

    <!-- Weather & Node Telemetry (Brief Slide 09) -->
    <div class="mt-8 bg-gradient-to-r from-slate-900 via-slate-800 to-slate-900 rounded-2xl p-6 text-white shadow-xl">
        <div class="flex flex-col md:flex-row md:items-center justify-between mb-4">
            <div>
                <span class="text-xs font-bold uppercase tracking-widest text-emerald-400">External Live Integration</span>
                <h3 class="text-lg font-bold text-white mt-0.5">Primary Transit Hubs — Climate & Ambient Telemetry</h3>
            </div>
            <span class="text-xs text-slate-400 mt-2 md:mt-0 font-mono">Synced via Open-Meteo REST API</span>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-2">
            @foreach($weatherHubs as $city => $w)
            <div class="bg-white/10 backdrop-blur-sm rounded-xl p-4 border border-white/10 flex items-center justify-between">
                <div>
                    <span class="text-xs text-slate-300 font-semibold">{{ $city }} Air Hub</span>
                    <p class="text-2xl font-bold mt-0.5">{{ $w['temperature_celsius'] ?? '--' }}°C</p>
                    <p class="text-xs text-slate-400 mt-0.5">Humidity: {{ $w['humidity_pct'] ?? '--' }}% • Wind: {{ $w['wind_speed_kmh'] ?? '--' }} km/h</p>
                </div>
                <div class="text-2xl">
                    @if(($w['temperature_celsius'] ?? 20) > 25) ☀️ @else ❄️ @endif
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <!-- 2 Column Layout: Critical Priority Alerts + Recent Events -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mt-8">
        
        <!-- Left 2 Cols: Critical Shipments & Thermal Deviations -->
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                    <div>
                        <h2 class="text-base font-bold text-slate-900">Priority Clinical Manifests (Urgent Follow-Up)</h2>
                        <p class="text-xs text-slate-500">Shipments flagged with HIGH delay risk or active exceptions</p>
                    </div>
                    <a href="{{ route('shipments.index') }}" class="text-xs font-semibold text-emerald-600 hover:text-emerald-700">View All &rarr;</a>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-slate-600">
                        <thead class="bg-slate-50 text-[11px] uppercase tracking-wider text-slate-400 font-semibold">
                            <tr>
                                <th class="px-6 py-3">Shipment No</th>
                                <th class="px-6 py-3">Specimen Lane</th>
                                <th class="px-6 py-3">Storage Tier</th>
                                <th class="px-6 py-3">Status</th>
                                <th class="px-6 py-3">Viability Risk</th>
                                <th class="px-6 py-3 text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($criticalShipments as $shipment)
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="px-6 py-4 font-mono font-bold text-slate-900">
                                    <a href="{{ route('shipments.show', $shipment) }}" class="text-emerald-600 hover:underline">
                                        {{ $shipment->shipment_number }}
                                    </a>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="font-medium text-slate-800">{{ $shipment->origin->city }} &rarr; {{ $shipment->destination->city }}</div>
                                    <div class="text-xs text-slate-400">{{ $shipment->carrier->name ?? 'Dedicated BioCourier' }}</div>
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
                                    @if($shipment->delay_severity === 'HIGH')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold bg-rose-100 text-rose-800 animate-pulse">
                                            ⚠️ HIGH (+{{ $shipment->delay_hours }}h)
                                        </span>
                                    @elseif($shipment->delay_severity === 'MEDIUM')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold bg-amber-100 text-amber-800">
                                            MEDIUM (+{{ $shipment->delay_hours }}h)
                                        </span>
                                    @else
                                        <span class="text-xs text-emerald-600 font-semibold">Nominal (On Time)</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <a href="{{ route('shipments.show', $shipment) }}" class="px-3 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-medium transition">
                                        Inspect
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="px-6 py-8 text-center text-slate-400 text-sm">
                                    No critical delayed manifests at this time. All biological cold-chains are within nominal tolerances.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Temperature Distribution Visual Summary -->
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6">
                <h3 class="text-sm font-bold text-slate-900 mb-3">Biological Preservation Class Distribution</h3>
                <div class="grid grid-cols-3 gap-4 text-center">
                    <div class="p-4 rounded-xl bg-cyan-50 border border-cyan-100">
                        <span class="text-xs font-bold text-cyan-800 uppercase">Cryo Nitrogen</span>
                        <p class="text-2xl font-black text-cyan-900 mt-1">{{ $tempBreakdown['CRYO_LN2'] ?? 0 }}</p>
                        <p class="text-[11px] text-cyan-600 mt-0.5">-196°C to -150°C</p>
                    </div>
                    <div class="p-4 rounded-xl bg-indigo-50 border border-indigo-100">
                        <span class="text-xs font-bold text-indigo-800 uppercase">Solid Dry Ice</span>
                        <p class="text-2xl font-black text-indigo-900 mt-1">{{ $tempBreakdown['DRY_ICE'] ?? 0 }}</p>
                        <p class="text-[11px] text-indigo-600 mt-0.5">-80°C to -20°C</p>
                    </div>
                    <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-100">
                        <span class="text-xs font-bold text-emerald-800 uppercase">Chilled Gel</span>
                        <p class="text-2xl font-black text-emerald-900 mt-1">{{ $tempBreakdown['CHILLED'] ?? 0 }}</p>
                        <p class="text-[11px] text-emerald-600 mt-0.5">+2°C to +8°C</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Col: Live Chronological Tracking Stream -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6">
            <h2 class="text-base font-bold text-slate-900 mb-1">Live Tracking Feed</h2>
            <p class="text-xs text-slate-500 mb-6">Latest milestones and sensor telemetry updates</p>

            <div class="flow-root">
                <ul class="-mb-8">
                    @forelse($recentEvents as $idx => $event)
                    <li>
                        <div class="relative pb-8">
                            @if(!$loop->last)
                            <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-slate-200" aria-hidden="true"></span>
                            @endif
                            <div class="relative flex space-x-3 items-start">
                                <div>
                                    <span class="h-8 w-8 rounded-full bg-emerald-100 flex items-center justify-center ring-8 ring-white text-xs">
                                        📍
                                    </span>
                                </div>
                                <div class="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4">
                                    <div>
                                        <p class="text-xs font-bold text-slate-900">
                                            <a href="{{ route('shipments.show', $event->shipment) }}" class="text-emerald-600 hover:underline">
                                                {{ $event->shipment->shipment_number }}
                                            </a>
                                            — {{ $event->event_type }}
                                        </p>
                                        <p class="text-xs text-slate-600 mt-0.5">{{ $event->description }}</p>
                                        @if($event->recorded_temp_celsius !== null)
                                        <span class="inline-block mt-1 text-[11px] font-mono font-bold px-2 py-0.5 rounded bg-slate-100 text-slate-700">
                                            Sensor: {{ $event->recorded_temp_celsius }}°C
                                        </span>
                                        @endif
                                    </div>
                                    <div class="text-right text-[11px] whitespace-nowrap text-slate-400">
                                        {{ $event->event_time->diffForHumans() }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </li>
                    @empty
                    <p class="text-xs text-slate-400 py-6 text-center">No tracking events recorded yet.</p>
                    @endforelse
                </ul>
            </div>
        </div>

    </div>

</div>
@endsection

