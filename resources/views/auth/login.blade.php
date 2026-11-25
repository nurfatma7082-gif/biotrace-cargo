@extends('layouts.app', ['title' => 'Sign In'])

@section('content')
<div class="min-h-[80vh] flex flex-col justify-center py-12 sm:px-6 lg:px-8">
    <div class="sm:mx-auto sm:w-full sm:max-w-md">
        <div class="flex justify-center">
            <div class="w-16 h-16 rounded-2xl bg-gradient-to-tr from-emerald-500 to-cyan-500 flex items-center justify-center text-white text-3xl shadow-xl shadow-emerald-500/20">
                🧬
            </div>
        </div>
        <h2 class="mt-4 text-center text-2xl font-bold tracking-tight text-slate-900">
            BioTrace Control Center
        </h2>
        <p class="mt-1 text-center text-sm text-slate-500">
            Global Cold-Chain Visibility for Sensitive Biospecimens
        </p>
    </div>

    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
        <div class="bg-white py-8 px-6 shadow-xl shadow-slate-200/50 sm:rounded-2xl sm:px-10 border border-slate-100">
            
            <!-- Quick Demo Accounts for UAS Evaluator / Lecturer -->
            <div class="mb-6 pb-6 border-b border-slate-100">
                <span class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">
                    ⚡ Quick Demo Access (1-Click Examiner Login)
                </span>
                <div class="grid grid-cols-3 gap-2">
                    <a href="{{ route('quick-login', 'admin') }}" class="flex flex-col items-center justify-center p-2.5 rounded-xl border border-purple-200 bg-purple-50/60 hover:bg-purple-100 text-purple-700 text-xs font-medium transition text-center group">
                        <span class="text-base group-hover:scale-110 transition">👑</span>
                        <span class="font-bold mt-1">Admin</span>
                    </a>
                    <a href="{{ route('quick-login', 'operator') }}" class="flex flex-col items-center justify-center p-2.5 rounded-xl border border-emerald-200 bg-emerald-50/60 hover:bg-emerald-100 text-emerald-700 text-xs font-medium transition text-center group">
                        <span class="text-base group-hover:scale-110 transition">🩺</span>
                        <span class="font-bold mt-1">Operator</span>
                    </a>
                    <a href="{{ route('quick-login', 'customer') }}" class="flex flex-col items-center justify-center p-2.5 rounded-xl border border-cyan-200 bg-cyan-50/60 hover:bg-cyan-100 text-cyan-700 text-xs font-medium transition text-center group">
                        <span class="text-base group-hover:scale-110 transition">🔬</span>
                        <span class="font-bold mt-1">Customer</span>
                    </a>
                </div>
            </div>

            <form class="space-y-5" action="{{ route('login') }}" method="POST">
                @csrf
                <div>
                    <label for="email" class="block text-xs font-semibold uppercase tracking-wider text-slate-600">Email Address</label>
                    <div class="mt-1.5">
                        <input id="email" name="email" type="email" autocomplete="email" required value="{{ old('email', 'admin@biotrace.io') }}"
                            class="block w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-slate-900 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm">
                    </div>
                </div>

                <div>
                    <label for="password" class="block text-xs font-semibold uppercase tracking-wider text-slate-600">Password</label>
                    <div class="mt-1.5">
                        <input id="password" name="password" type="password" required value="password"
                            class="block w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-slate-900 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm">
                    </div>
                </div>

                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <input id="remember" name="remember" type="checkbox" checked
                            class="h-4 w-4 rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                        <label for="remember" class="ml-2 block text-xs text-slate-600">Remember session</label>
                    </div>
                </div>

                <div>
                    <button type="submit"
                        class="w-full flex justify-center py-2.5 px-4 border border-transparent rounded-xl shadow-md text-sm font-semibold text-white bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition">
                        Authenticate & Enter Portal
                    </button>
                </div>
            </form>

            <div class="mt-6 text-center text-xs text-slate-500">
                Authorized clinical personnel only. All access attempts are recorded in compliance with Good Laboratory Practice (GLP) and HIPAA audit guidelines.
            </div>
        </div>
    </div>
</div>
@endsection

