@extends('layouts.app', ['title' => 'Cold-Chain Exceptions'])

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-8">
    
    <div class="flex flex-col md:flex-row md:items-center justify-between pb-6 border-b border-slate-200">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900">
                Incident & Exception Log
            </h1>
            <p class="mt-1 text-sm text-slate-500">
                Track temperature excursions, coolant depletion, customs quarantine, and flight disruptions.
            </p>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="mt-6 bg-white p-4 rounded-2xl border border-slate-200/80 shadow-sm">
        <form method="GET" action="{{ route('exceptions.index') }}" class="flex flex-wrap items-center gap-4">
            <div>
                <label class="block text-xs font-semibold uppercase text-slate-400 mb-1">Status</label>
                <select name="status" class="text-sm rounded-xl border border-slate-300 px-3 py-2">
                    <option value="">All Statuses</option>
                    <option value="OPEN" {{ request('status') === 'OPEN' ? 'selected' : '' }}>OPEN (Requires Action)</option>
                    <option value="INVESTIGATING" {{ request('status') === 'INVESTIGATING' ? 'selected' : '' }}>INVESTIGATING</option>
                    <option value="RESOLVED" {{ request('status') === 'RESOLVED' ? 'selected' : '' }}>RESOLVED</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold uppercase text-slate-400 mb-1">Severity</label>
                <select name="severity" class="text-sm rounded-xl border border-slate-300 px-3 py-2">
                    <option value="">All Severities</option>
                    <option value="HIGH" {{ request('severity') === 'HIGH' ? 'selected' : '' }}>HIGH (Degradation Threat)</option>
                    <option value="MEDIUM" {{ request('severity') === 'MEDIUM' ? 'selected' : '' }}>MEDIUM</option>
                    <option value="LOW" {{ request('severity') === 'LOW' ? 'selected' : '' }}>LOW</option>
                </select>
            </div>

            <div class="flex items-end space-x-2 pt-5">
                <button type="submit" class="px-4 py-2 bg-slate-900 text-white rounded-xl text-sm font-semibold hover:bg-slate-800 transition">
                    Filter
                </button>
                <a href="{{ route('exceptions.index') }}" class="px-3 py-2 bg-slate-100 text-slate-600 rounded-xl text-sm font-medium hover:bg-slate-200 transition">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Exceptions Grid / Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mt-6">
        @forelse($exceptions as $ex)
        <div class="bg-white rounded-2xl border {{ $ex->severity === 'HIGH' ? 'border-rose-200 shadow-rose-100' : 'border-slate-200/80' }} p-6 shadow-sm flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-mono font-bold
                        {{ $ex->severity === 'HIGH' ? 'bg-rose-100 text-rose-800' : ($ex->severity === 'MEDIUM' ? 'bg-amber-100 text-amber-800' : 'bg-slate-100 text-slate-800') }}">
                        {{ $ex->severity }} SEVERITY
                    </span>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold
                        {{ $ex->status === 'RESOLVED' ? 'bg-emerald-100 text-emerald-800' : ($ex->status === 'INVESTIGATING' ? 'bg-amber-100 text-amber-800' : 'bg-rose-100 text-rose-800') }}">
                        {{ $ex->status }}
                    </span>
                </div>

                <div class="mt-3">
                    <a href="{{ route('shipments.show', $ex->shipment) }}" class="text-xs font-mono font-bold text-emerald-600 hover:underline">
                        {{ $ex->shipment->shipment_number }} &rarr;
                    </a>
                    <h3 class="text-base font-bold text-slate-900 mt-0.5">{{ $ex->exception_type }}</h3>
                    <p class="text-xs text-slate-500">Route: {{ $ex->shipment->origin->city }} to {{ $ex->shipment->destination->city }}</p>
                    <p class="text-sm text-slate-700 mt-2 bg-slate-50 p-3 rounded-xl border border-slate-100">{{ $ex->description }}</p>
                </div>

                @if($ex->resolution_notes)
                <div class="mt-3 text-xs bg-emerald-50/70 p-3 rounded-xl border border-emerald-100 text-emerald-800">
                    <strong class="font-bold">Corrective Action Log:</strong> {{ $ex->resolution_notes }}
                </div>
                @endif
            </div>

            <div class="mt-4 pt-4 border-t border-slate-100 flex items-center justify-between text-xs text-slate-400">
                <span>Detected: {{ $ex->detected_at->format('d M Y, H:i') }}</span>
                @if($ex->resolved_at)
                <span class="text-emerald-600 font-medium">Resolved: {{ $ex->resolved_at->format('d M, H:i') }}</span>
                @endif
            </div>

            @if(!Auth::user()->isCustomer())
            <form action="{{ route('exceptions.update', $ex) }}" method="POST" class="mt-4 pt-3 border-t border-slate-100 flex items-center space-x-2">
                @csrf
                @method('PATCH')
                <input type="text" name="resolution_notes" placeholder="Add resolution note..." value="{{ $ex->resolution_notes }}" class="text-xs rounded-xl border border-slate-300 px-3 py-1.5 flex-1">
                <select name="status" class="text-xs rounded-xl border border-slate-300 px-2 py-1.5 font-semibold">
                    <option value="OPEN" {{ $ex->status === 'OPEN' ? 'selected' : '' }}>OPEN</option>
                    <option value="INVESTIGATING" {{ $ex->status === 'INVESTIGATING' ? 'selected' : '' }}>INVESTIGATING</option>
                    <option value="RESOLVED" {{ $ex->status === 'RESOLVED' ? 'selected' : '' }}>RESOLVED</option>
                </select>
                <button type="submit" class="px-3.5 py-1.5 bg-slate-900 hover:bg-slate-800 text-white rounded-xl text-xs font-bold transition">
                    Save
                </button>
            </form>
            @endif
        </div>
        @empty
        <div class="col-span-2 bg-white rounded-2xl border border-slate-200 p-12 text-center text-slate-400 text-sm">
            No exceptions currently logged.
        </div>
        @endforelse
    </div>

    @if($exceptions->hasPages())
    <div class="mt-6">
        {{ $exceptions->links() }}
    </div>
    @endif

</div>
@endsection

