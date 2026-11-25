@extends('layouts.app', ['title' => 'Manifest ' . $shipment->shipment_number])

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-8">
    
    <!-- Top Back & Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between pb-6 border-b border-slate-200">
        <div>
            <div class="flex items-center space-x-3">
                <a href="{{ route('shipments.index') }}" class="text-xs font-semibold text-slate-500 hover:text-slate-900">&larr; Back to Shipments</a>
                <span class="text-slate-300">/</span>
                <span class="text-xs font-mono font-bold text-emerald-600">{{ $shipment->shipment_number }}</span>
            </div>
            <div class="flex items-center space-x-3 mt-1.5">
                <h1 class="text-2xl font-black tracking-tight text-slate-900">
                    {{ $shipment->shipment_number }}
                </h1>
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold
                    {{ $shipment->status === 'DELIVERED' ? 'bg-emerald-100 text-emerald-800' : ($shipment->status === 'CUSTOMS_HOLD' ? 'bg-amber-100 text-amber-800' : 'bg-sky-100 text-sky-800') }}">
                    {{ $shipment->status }}
                </span>
                @if($shipment->is_delayed)
                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-rose-100 text-rose-800 animate-pulse">
                    ⚠️ Delayed +{{ $shipment->delay_hours }}h ({{ $shipment->delay_severity }} Severity)
                </span>
                @else
                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700">
                    ✅ On Schedule
                </span>
                @endif
            </div>
        </div>

        <!-- Operator Actions -->
        @if(!Auth::user()->isCustomer())
        <div class="mt-4 md:mt-0 flex flex-wrap gap-2">
            <!-- Add Tracking Event Modal Trigger -->
            <button onclick="document.getElementById('modal-event').classList.remove('hidden')" class="px-3.5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold shadow transition">
                + Log Tracking Event
            </button>

            <!-- Transition Status Trigger -->
            <button onclick="document.getElementById('modal-status').classList.remove('hidden')" class="px-3.5 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold shadow transition">
                Advance Status
            </button>

            <!-- Update ETA Trigger -->
            <button onclick="document.getElementById('modal-eta').classList.remove('hidden')" class="px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition">
                Adjust ETA
            </button>

            <!-- Raise Exception Trigger -->
            <button onclick="document.getElementById('modal-exception').classList.remove('hidden')" class="px-3.5 py-2 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 text-xs font-bold transition">
                🚨 Flag Exception
            </button>
        </div>
        @endif
    </div>

    <!-- Active Exception Alert Banner -->
    @if($shipment->openExceptions->count() > 0)
    <div class="mt-6 p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-900 shadow-sm">
        <div class="flex items-start justify-between">
            <div class="flex items-start space-x-3">
                <span class="text-2xl">🚨</span>
                <div>
                    <h3 class="text-sm font-bold uppercase tracking-wide text-rose-800">
                        Active Cold-Chain Exception Detected
                    </h3>
                    @foreach($shipment->openExceptions as $ex)
                    <div class="mt-2 text-xs bg-white/70 p-3 rounded-xl border border-rose-100">
                        <div class="flex items-center space-x-2 font-semibold">
                            <span class="px-2 py-0.5 rounded bg-rose-200 text-rose-900 font-mono">{{ $ex->exception_type }}</span>
                            <span>Severity: {{ $ex->severity }}</span>
                            <span class="text-slate-400">• Detected {{ $ex->detected_at->format('d M Y, H:i') }}</span>
                        </div>
                        <p class="mt-1 text-slate-700">{{ $ex->description }}</p>
                        @if($ex->resolution_notes)
                        <p class="mt-1 text-emerald-700 font-medium">Resolution in progress: {{ $ex->resolution_notes }}</p>
                        @endif
                        
                        @if(!Auth::user()->isCustomer())
                        <form action="{{ route('exceptions.update', $ex) }}" method="POST" class="mt-2 flex items-center space-x-2">
                            @csrf
                            @method('PATCH')
                            <input type="text" name="resolution_notes" placeholder="Update resolution notes..." class="text-xs rounded-lg border border-slate-300 px-2.5 py-1 flex-1">
                            <select name="status" class="text-xs rounded-lg border border-slate-300 px-2 py-1">
                                <option value="INVESTIGATING" {{ $ex->status === 'INVESTIGATING' ? 'selected' : '' }}>INVESTIGATING</option>
                                <option value="RESOLVED">RESOLVED</option>
                            </select>
                            <button type="submit" class="px-3 py-1 rounded-lg bg-slate-900 text-white text-xs font-semibold">Update</button>
                        </form>
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- 2 Column Grid: Specimen Info + Map & Timeline -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mt-6">
        
        <!-- Left Col: Manifest & Biological Details -->
        <div class="space-y-6">
            
            <!-- Biospecimen Details -->
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-5">
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Biological Cargo Spec</span>
                <h3 class="text-base font-bold text-slate-900 mt-1">Preserved Specimen Items</h3>

                @foreach($shipment->items as $item)
                <div class="mt-3 p-3.5 rounded-xl bg-slate-50 border border-slate-100">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-mono font-bold text-emerald-700">{{ $item->specimen_identifier }}</span>
                        <span class="text-[11px] font-bold px-2 py-0.5 rounded bg-purple-100 text-purple-800">
                            {{ $item->biohazard_level }}
                        </span>
                    </div>
                    <p class="text-sm font-semibold text-slate-900 mt-1">{{ $item->specimen_type }}</p>
                    <p class="text-xs text-slate-500 mt-0.5">Quantity: <strong class="text-slate-800">{{ $item->quantity_vials }} vials</strong></p>
                    <p class="text-xs text-slate-500 mt-0.5">Target Storage: <strong class="text-cyan-700 font-mono">{{ $item->required_temp_celsius }}°C</strong></p>
                    @if($item->storage_instructions)
                    <div class="mt-2 text-[11px] text-slate-600 bg-white p-2 rounded-lg border border-slate-200/60">
                        {{ $item->storage_instructions }}
                    </div>
                    @endif
                </div>
                @endforeach

                <div class="mt-4 pt-4 border-t border-slate-100 text-xs space-y-2">
                    <div class="flex justify-between">
                        <span class="text-slate-400">Preservation Class:</span>
                        <span class="font-bold text-slate-800">{{ $shipment->storage_temp }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-400">Total Weight:</span>
                        <span class="font-semibold text-slate-800">{{ $shipment->total_weight_kg }} kg (incl. coolant)</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-400">Courier Partner:</span>
                        <span class="font-semibold text-slate-800">{{ $shipment->carrier->name ?? 'Dedicated Courier' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-400">Client Organization:</span>
                        <span class="font-semibold text-slate-800">{{ $shipment->customer->organization ?? $shipment->customer->name }}</span>
                    </div>
                </div>
            </div>

            <!-- Lane Nodes -->
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-5">
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Transit Corridor</span>
                <div class="mt-3 space-y-4">
                    <div>
                        <span class="text-xs font-semibold text-slate-400 uppercase">Origin Facility</span>
                        <p class="text-sm font-bold text-slate-900">{{ $shipment->origin->facility_name }}</p>
                        <p class="text-xs text-slate-500">{{ $shipment->origin->city }}, {{ $shipment->origin->country }}</p>
                    </div>
                    <div class="border-l-2 border-dashed border-emerald-300 pl-3 ml-2 text-xs text-slate-400 py-1">
                        Cold-Chain Handover Lane
                    </div>
                    <div>
                        <span class="text-xs font-semibold text-slate-400 uppercase">Destination Facility</span>
                        <p class="text-sm font-bold text-slate-900">{{ $shipment->destination->facility_name }}</p>
                        <p class="text-xs text-slate-500">{{ $shipment->destination->city }}, {{ $shipment->destination->country }}</p>
                        @if($destinationWeather)
                        <div class="mt-2 inline-flex items-center px-2.5 py-1 rounded-lg bg-sky-50 text-sky-800 text-[11px] font-medium border border-sky-100">
                            🌤️ Dest. Weather: {{ $destinationWeather['temperature_celsius'] }}°C, {{ $destinationWeather['humidity_pct'] }}% Humidity
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Schedule & Delay Metrics -->
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-5">
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Timetable & Degradation Rules</span>
                <div class="mt-3 text-xs space-y-2">
                    <div class="flex justify-between">
                        <span class="text-slate-400">Departure Time:</span>
                        <span class="font-medium text-slate-800">{{ $shipment->expected_departure->format('d M Y, H:i') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-400">Expected Arrival:</span>
                        <span class="font-medium text-slate-800">{{ $shipment->expected_arrival->format('d M Y, H:i') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-400">Current ETA:</span>
                        <span class="font-bold {{ $shipment->is_delayed ? 'text-rose-600' : 'text-emerald-700' }}">
                            {{ $shipment->estimated_arrival->format('d M Y, H:i') }}
                        </span>
                    </div>
                    @if($shipment->is_delayed)
                    <div class="p-2.5 rounded-xl bg-rose-50 border border-rose-100 mt-2">
                        <span class="block font-bold text-rose-800">Delayed: +{{ $shipment->delay_hours }} hours</span>
                        <p class="text-slate-600 mt-0.5">{{ $shipment->delay_reason ?? 'Delay registered due to route conditions.' }}</p>
                    </div>
                    @endif
                </div>
            </div>

        </div>

        <!-- Right 2 Cols: Interactive Map & Timeline -->
        <div class="lg:col-span-2 space-y-6">
            
            <!-- Leaflet Interactive Map (Brief Slide 09) -->
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-5">
                <div class="flex items-center justify-between mb-3">
                    <div>
                        <h2 class="text-base font-bold text-slate-900">Geospatial Transit Route</h2>
                        <p class="text-xs text-slate-500">Milestone locations and current GPS telemetry</p>
                    </div>
                    <span class="text-xs font-mono text-slate-400">Leaflet.js + OSM</span>
                </div>
                
                <div id="shipment-map" class="w-full h-80 rounded-xl border border-slate-200 z-10"></div>
            </div>

            <!-- Historical Chronological Timeline (Brief Slide 07) -->
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h2 class="text-base font-bold text-slate-900">Shipment Milestone Timeline</h2>
                        <p class="text-xs text-slate-500">Immutable historical tracking log</p>
                    </div>
                    <span class="text-xs text-slate-400">{{ $shipment->trackingEvents->count() }} milestones</span>
                </div>

                <div class="flow-root">
                    <ul class="-mb-8">
                        @forelse($shipment->trackingEvents as $event)
                        <li>
                            <div class="relative pb-8">
                                @if(!$loop->last)
                                <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-slate-200" aria-hidden="true"></span>
                                @endif
                                <div class="relative flex space-x-3 items-start">
                                    <div>
                                        <span class="h-8 w-8 rounded-full {{ $event->event_type === 'DELIVERED' ? 'bg-emerald-500 text-white' : ($event->event_type === 'CUSTOMS_HOLD' ? 'bg-amber-500 text-white' : 'bg-slate-900 text-white') }} flex items-center justify-center ring-8 ring-white text-xs">
                                            @if($event->event_type === 'DELIVERED') ✓ @elseif($event->event_type === 'CUSTOMS_HOLD') ⚠️ @else 📍 @endif
                                        </span>
                                    </div>
                                    <div class="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4">
                                        <div>
                                            <p class="text-sm font-bold text-slate-900">
                                                {{ $event->event_type }}
                                                @if($event->location)
                                                <span class="text-slate-500 font-normal">at {{ $event->location->facility_name }}</span>
                                                @endif
                                            </p>
                                            <p class="text-xs text-slate-600 mt-1">{{ $event->description }}</p>
                                            
                                            <div class="flex items-center space-x-2 mt-2">
                                                @if($event->recorded_temp_celsius !== null)
                                                <span class="text-[11px] font-mono font-bold px-2 py-0.5 rounded bg-cyan-50 text-cyan-800 border border-cyan-100">
                                                    Sensor: {{ $event->recorded_temp_celsius }}°C
                                                </span>
                                                @endif
                                                @if($event->recordedBy)
                                                <span class="text-[11px] text-slate-400">
                                                    by {{ $event->recordedBy->name }}
                                                </span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="text-right text-xs whitespace-nowrap text-slate-400 font-mono">
                                            {{ $event->event_time->format('d M, H:i') }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </li>
                        @empty
                        <p class="text-xs text-slate-400 py-6 text-center">No tracking events logged yet.</p>
                        @endforelse
                    </ul>
                </div>
            </div>

        </div>

    </div>

</div>

<!-- Modals for Operator Actions -->

<!-- 1. Log Tracking Event Modal -->
<div id="modal-event" class="hidden fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl border border-slate-100">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-base font-bold text-slate-900">Log Tracking Milestone</h3>
            <button onclick="document.getElementById('modal-event').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 font-bold">&times;</button>
        </div>
        <form action="{{ route('shipments.tracking.store', $shipment) }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-semibold uppercase text-slate-600 mb-1">Milestone Event Type</label>
                <select name="event_type" required class="w-full text-sm rounded-xl border border-slate-300 p-2.5">
                    <option value="PICKED_UP">PICKED_UP (Collected by Courier)</option>
                    <option value="AIRPORT_DEPARTURE">AIRPORT_DEPARTURE (Flight Loaded)</option>
                    <option value="HUB_TRANSIT">HUB_TRANSIT (Cold Storage Scan)</option>
                    <option value="REICING_PERFORMED">REICING_PERFORMED (Coolant Replenished)</option>
                    <option value="CUSTOMS_HOLD">CUSTOMS_HOLD (Quarantine Inspection)</option>
                    <option value="OUT_FOR_DELIVERY">OUT_FOR_DELIVERY (Final Mile)</option>
                    <option value="DELIVERED">DELIVERED (Verified at Lab)</option>
                </select>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold uppercase text-slate-600 mb-1">Facility Node</label>
                    <select name="location_id" class="w-full text-sm rounded-xl border border-slate-300 p-2.5">
                        <option value="">Select Facility...</option>
                        @foreach($locations as $loc)
                            <option value="{{ $loc->id }}">{{ $loc->facility_name }} ({{ $loc->city }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase text-slate-600 mb-1">Preservation Temp (°C)</label>
                    <input type="number" step="0.1" name="recorded_temp_celsius" placeholder="-78.5" class="w-full text-sm rounded-xl border border-slate-300 p-2.5 font-mono">
                </div>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold uppercase text-slate-600 mb-1">GPS Latitude (Optional)</label>
                    <input type="number" step="0.000001" name="latitude" placeholder="e.g. 1.36442" class="w-full text-sm rounded-xl border border-slate-300 p-2.5 font-mono">
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase text-slate-600 mb-1">GPS Longitude (Optional)</label>
                    <input type="number" step="0.000001" name="longitude" placeholder="e.g. 103.9915" class="w-full text-sm rounded-xl border border-slate-300 p-2.5 font-mono">
                </div>
            </div>
            <div>
                <label class="block text-xs font-semibold uppercase text-slate-600 mb-1">Timestamp</label>
                <input type="datetime-local" name="event_time" required value="{{ now()->format('Y-m-d\TH:i') }}" class="w-full text-sm rounded-xl border border-slate-300 p-2.5">
            </div>
            <div>
                <label class="block text-xs font-semibold uppercase text-slate-600 mb-1">Telemetry Description</label>
                <textarea name="description" required rows="2" placeholder="e.g. Dry ice levels checked; sensor steady." class="w-full text-sm rounded-xl border border-slate-300 p-2.5"></textarea>
            </div>
            <button type="submit" class="w-full py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl text-sm font-bold shadow">
                Save Tracking Milestone
            </button>
        </form>
    </div>
</div>

<!-- 2. Advance Status Modal -->
<div id="modal-status" class="hidden fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl border border-slate-100">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-base font-bold text-slate-900">Advance Lifecycle Status</h3>
            <button onclick="document.getElementById('modal-status').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 font-bold">&times;</button>
        </div>
        <form action="{{ route('shipments.status', $shipment) }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-semibold uppercase text-slate-600 mb-1">Next Valid Status</label>
                <select name="status" required class="w-full text-sm rounded-xl border border-slate-300 p-2.5">
                    @foreach(['CRYOPACKED', 'PICKED_UP', 'IN_TRANSIT', 'CUSTOMS_HOLD', 'OUT_FOR_DELIVERY', 'DELIVERED', 'CANCELLED'] as $st)
                        <option value="{{ $st }}">{{ $st }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold uppercase text-slate-600 mb-1">Handover Notes</label>
                <input type="text" name="reason" placeholder="e.g. Customs released, moving to local courier" class="w-full text-sm rounded-xl border border-slate-300 p-2.5">
            </div>
            <button type="submit" class="w-full py-2.5 bg-slate-900 hover:bg-slate-800 text-white rounded-xl text-sm font-bold shadow">
                Update Status
            </button>
        </form>
    </div>
</div>

<!-- 3. Adjust ETA Modal -->
<div id="modal-eta" class="hidden fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl border border-slate-100">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-base font-bold text-slate-900">Adjust Estimated Arrival (ETA)</h3>
            <button onclick="document.getElementById('modal-eta').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 font-bold">&times;</button>
        </div>
        <form action="{{ route('shipments.eta', $shipment) }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-semibold uppercase text-slate-600 mb-1">New Estimated Arrival</label>
                <input type="datetime-local" name="estimated_arrival" required value="{{ $shipment->estimated_arrival->format('Y-m-d\TH:i') }}" class="w-full text-sm rounded-xl border border-slate-300 p-2.5">
            </div>
            <div>
                <label class="block text-xs font-semibold uppercase text-slate-600 mb-1">Reason for Delay</label>
                <textarea name="delay_reason" rows="2" placeholder="e.g. Flight delay at Frankfurt hub due to snow." class="w-full text-sm rounded-xl border border-slate-300 p-2.5">{{ $shipment->delay_reason }}</textarea>
            </div>
            <button type="submit" class="w-full py-2.5 bg-slate-900 hover:bg-slate-800 text-white rounded-xl text-sm font-bold shadow">
                Recalculate Viability Impact
            </button>
        </form>
    </div>
</div>

<!-- 4. Raise Exception Modal -->
<div id="modal-exception" class="hidden fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl border border-slate-100">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-base font-bold text-rose-700">🚨 Raise Cold-Chain Incident</h3>
            <button onclick="document.getElementById('modal-exception').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 font-bold">&times;</button>
        </div>
        <form action="{{ route('shipments.exceptions.store', $shipment) }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-semibold uppercase text-slate-600 mb-1">Exception Type</label>
                <select name="exception_type" required class="w-full text-sm rounded-xl border border-slate-300 p-2.5">
                    <option value="TEMPERATURE_EXCURSION">TEMPERATURE_EXCURSION (Thermal Spike)</option>
                    <option value="DRY_ICE_DEPLETION">DRY_ICE_DEPLETION (Depleted Coolant)</option>
                    <option value="CUSTOMS_BIOHAZARD_HOLD">CUSTOMS_BIOHAZARD_HOLD (Quarantine Inspection)</option>
                    <option value="WEATHER_DELAY">WEATHER_DELAY (Airport Ground Stop)</option>
                    <option value="CONTAINER_DAMAGE">CONTAINER_DAMAGE (Physical Breach)</option>
                    <option value="DOCUMENTATION_ISSUE">DOCUMENTATION_ISSUE (Permit Discrepancy)</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold uppercase text-slate-600 mb-1">Severity Tier</label>
                <select name="severity" required class="w-full text-sm rounded-xl border border-slate-300 p-2.5">
                    <option value="LOW">LOW (Tolerable drift)</option>
                    <option value="MEDIUM" selected>MEDIUM (Intervention required)</option>
                    <option value="HIGH">HIGH (Degradation threat imminent)</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold uppercase text-slate-600 mb-1">Detection Time</label>
                <input type="datetime-local" name="detected_at" required value="{{ now()->format('Y-m-d\TH:i') }}" class="w-full text-sm rounded-xl border border-slate-300 p-2.5">
            </div>
            <div>
                <label class="block text-xs font-semibold uppercase text-slate-600 mb-1">Incident Description</label>
                <textarea name="description" required rows="2" placeholder="e.g. Dry ice vapor below minimum line." class="w-full text-sm rounded-xl border border-slate-300 p-2.5"></textarea>
            </div>
            <button type="submit" class="w-full py-2.5 bg-rose-600 hover:bg-rose-500 text-white rounded-xl text-sm font-bold shadow">
                Publish Exception Alert
            </button>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Prepare Leaflet map with origin, destination, and event markers
        var originLat = {{ $shipment->origin->latitude }};
        var originLng = {{ $shipment->origin->longitude }};
        var destLat = {{ $shipment->destination->latitude }};
        var destLng = {{ $shipment->destination->longitude }};

        var map = L.map('shipment-map').setView([originLat, originLng], 3);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);

        var bounds = [];

        // Origin Marker (Blue)
        var originMarker = L.marker([originLat, originLng]).addTo(map)
            .bindPopup("<b>Origin:</b> {{ $shipment->origin->facility_name }}<br>{{ $shipment->origin->city }}");
        bounds.push([originLat, originLng]);

        // Destination Marker (Green)
        var destMarker = L.marker([destLat, destLng]).addTo(map)
            .bindPopup("<b>Destination:</b> {{ $shipment->destination->facility_name }}<br>{{ $shipment->destination->city }}");
        bounds.push([destLat, destLng]);

        // Polyline Points
        var latlngs = [
            [originLat, originLng]
        ];

        @foreach($shipment->trackingEvents as $ev)
            @if($ev->latitude && $ev->longitude)
                latlngs.push([{{ $ev->latitude }}, {{ $ev->longitude }}]);
                bounds.push([{{ $ev->latitude }}, {{ $ev->longitude }}]);
                
                L.circleMarker([{{ $ev->latitude }}, {{ $ev->longitude }}], {
                    color: '#059669',
                    fillColor: '#10b981',
                    fillOpacity: 0.8,
                    radius: 6
                }).addTo(map).bindPopup("<b>{{ $ev->event_type }}</b><br>{{ $ev->description }}<br><small>{{ $ev->event_time->format('d M, H:i') }}</small>");
            @endif
        @endforeach

        latlngs.push([destLat, destLng]);

        var polyline = L.polyline(latlngs, {
            color: '#0ea5e9',
            weight: 3,
            dashArray: '5, 8'
        }).addTo(map);

        map.fitBounds(bounds, { padding: [40, 40] });
    });
</script>
@endpush

