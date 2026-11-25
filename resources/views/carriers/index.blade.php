@extends('layouts.app', ['title' => 'Carriers'])

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-8">
    
    <div class="flex flex-col md:flex-row md:items-center justify-between pb-6 border-b border-slate-200">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900">
                Specialized Medical Carriers
            </h1>
            <p class="mt-1 text-sm text-slate-500">
                Cold-chain certified logistics providers with dedicated thermal monitoring protocols.
            </p>
        </div>
        <button onclick="document.getElementById('modal-carrier').classList.remove('hidden')" class="mt-4 md:mt-0 px-4 py-2 rounded-xl text-sm font-semibold text-white bg-slate-900 hover:bg-slate-800 transition">
            + Add Carrier
        </button>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-8">
        @foreach($carriers as $carrier)
        <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-sm flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between">
                    <span class="px-2.5 py-1 rounded-lg bg-slate-100 font-mono font-bold text-xs text-slate-700">
                        {{ $carrier->code }}
                    </span>
                    @if($carrier->is_cold_chain_certified)
                    <span class="px-2.5 py-0.5 rounded-full bg-emerald-100 text-emerald-800 text-[11px] font-bold">
                        ✓ IATA CEIV Certified
                    </span>
                    @endif
                </div>

                <h3 class="text-lg font-bold text-slate-900 mt-3">{{ $carrier->name }}</h3>
                <p class="text-xs text-slate-500 mt-1">{{ $carrier->notes }}</p>

                <div class="mt-4 pt-4 border-t border-slate-100 text-xs text-slate-600 space-y-1">
                    <p><strong>Hotline:</strong> {{ $carrier->contact_phone ?? 'N/A' }}</p>
                    <p><strong>Desk Email:</strong> {{ $carrier->contact_email ?? 'N/A' }}</p>
                </div>
            </div>

            <div class="mt-6 pt-4 border-t border-slate-100 flex items-center justify-between text-xs text-slate-400">
                <span>Active Shipments</span>
                <span class="font-bold text-slate-900 text-sm">{{ $carrier->shipments_count }}</span>
            </div>
        </div>
        @endforeach
    </div>

</div>

<!-- Add Carrier Modal -->
<div id="modal-carrier" class="hidden fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl border border-slate-100">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-base font-bold text-slate-900">Add Medical Carrier</h3>
            <button onclick="document.getElementById('modal-carrier').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 font-bold">&times;</button>
        </div>
        <form action="{{ route('carriers.store') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-semibold uppercase text-slate-600 mb-1">Carrier Name</label>
                <input type="text" name="name" required placeholder="e.g. QuickStat Bio-Pharma" class="w-full text-sm rounded-xl border border-slate-300 p-2.5">
            </div>
            <div>
                <label class="block text-xs font-semibold uppercase text-slate-600 mb-1">Code (Short Unique)</label>
                <input type="text" name="code" required placeholder="e.g. QSTAT" class="w-full text-sm rounded-xl border border-slate-300 p-2.5 font-mono">
            </div>
            <div>
                <label class="block text-xs font-semibold uppercase text-slate-600 mb-1">Contact Phone</label>
                <input type="text" name="contact_phone" placeholder="+1 800..." class="w-full text-sm rounded-xl border border-slate-300 p-2.5">
            </div>
            <div>
                <label class="block text-xs font-semibold uppercase text-slate-600 mb-1">Contact Email</label>
                <input type="email" name="contact_email" placeholder="ops@carrier.com" class="w-full text-sm rounded-xl border border-slate-300 p-2.5">
            </div>
            <div>
                <label class="block text-xs font-semibold uppercase text-slate-600 mb-1">Notes / Scope</label>
                <textarea name="notes" rows="2" placeholder="e.g. Cryo shipper specialist" class="w-full text-sm rounded-xl border border-slate-300 p-2.5"></textarea>
            </div>
            <div class="flex items-center">
                <input type="checkbox" name="is_cold_chain_certified" value="1" id="cert" checked class="rounded border-slate-300 text-emerald-600">
                <label for="cert" class="ml-2 text-xs text-slate-700">Cold Chain / CEIV Certified</label>
            </div>
            <button type="submit" class="w-full py-2.5 bg-slate-900 hover:bg-slate-800 text-white rounded-xl text-sm font-bold shadow">
                Save Carrier
            </button>
        </form>
    </div>
</div>
@endsection

