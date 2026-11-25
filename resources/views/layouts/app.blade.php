<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'BioTrace' }} — Cold-Chain Biospecimen Tracking</title>
    
    <!-- Tailwind CSS (CDN for zero-build instant run) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            50: '#ecfdf5',
                            100: '#d1fae5',
                            500: '#10b981',
                            600: '#059669',
                            700: '#047857',
                            900: '#064e3b',
                        },
                        cryo: {
                            50: '#f0f9ff',
                            100: '#e0f2fe',
                            500: '#0ea5e9',
                            600: '#0284c7',
                            700: '#0369a1',
                        }
                    }
                }
            }
        }
    </script>

    <!-- Leaflet.js for Interactive Map -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

    <!-- Chart.js for Metrics -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        .leaflet-container { font-family: inherit; }
    </style>
</head>
<body class="h-full flex flex-col text-slate-800 antialiased font-sans">
    
    <!-- Top Medical Navigation Bar -->
    <header class="bg-slate-900 border-b border-slate-800 sticky top-0 z-50 shadow-md">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <!-- Brand Logo & Domain -->
                <div class="flex items-center space-x-3">
                    <a href="{{ route('dashboard') }}" class="flex items-center space-x-2">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-emerald-500 to-cyan-500 flex items-center justify-center text-white font-black text-xl shadow-lg shadow-emerald-500/20">
                            🧬
                        </div>
                        <div>
                            <span class="text-xl font-bold text-white tracking-tight">Bio<span class="text-emerald-400">Trace</span></span>
                            <span class="block text-[10px] uppercase tracking-wider text-slate-400 font-semibold">Cold-Chain Visibility</span>
                        </div>
                    </a>

                    <!-- Nav Links -->
                    @auth
                    <nav class="hidden md:flex ml-8 space-x-1">
                        <a href="{{ route('dashboard') }}" class="px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('dashboard') ? 'bg-slate-800 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">Dashboard</a>
                        <a href="{{ route('shipments.index') }}" class="px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('shipments.*') ? 'bg-slate-800 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">Shipments</a>
                        <a href="{{ route('exceptions.index') }}" class="px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('exceptions.*') ? 'bg-slate-800 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">Exceptions</a>
                        @if(Auth::user()->isAdmin() || Auth::user()->isOperator())
                        <a href="{{ route('carriers.index') }}" class="px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('carriers.*') ? 'bg-slate-800 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">Carriers</a>
                        <a href="{{ route('locations.index') }}" class="px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('locations.*') ? 'bg-slate-800 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">Facilities & Nodes</a>
                        @endif
                    </nav>
                    @endauth
                </div>

                <!-- User Profile & Action -->
                @auth
                <div class="flex items-center space-x-4">
                    <div class="hidden sm:flex flex-col text-right">
                        <span class="text-sm font-semibold text-white">{{ Auth::user()->name }}</span>
                        <div class="flex items-center justify-end space-x-1.5">
                            <span class="inline-block w-2 h-2 rounded-full {{ Auth::user()->role === 'ADMIN' ? 'bg-purple-400' : (Auth::user()->role === 'OPERATOR' ? 'bg-emerald-400' : 'bg-cyan-400') }}"></span>
                            <span class="text-xs text-slate-400 font-mono">{{ Auth::user()->role }} • {{ Str::limit(Auth::user()->organization, 24) }}</span>
                        </div>
                    </div>
                    
                    <form action="{{ route('logout') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="p-2 rounded-lg text-slate-400 hover:text-white hover:bg-slate-800 transition text-sm">
                            Sign Out
                        </button>
                    </form>
                </div>
                @else
                <div class="flex items-center space-x-3">
                    <a href="{{ route('login') }}" class="text-sm font-medium text-slate-300 hover:text-white">Sign In</a>
                    <a href="{{ route('register') }}" class="px-3 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-500 text-white text-sm font-medium shadow">Register</a>
                </div>
                @endauth
            </div>
        </div>
    </header>

    <!-- Global Alert / Flash Messages -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4 w-full">
        @if(session('success'))
        <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 flex items-center justify-between mb-4 shadow-sm">
            <div class="flex items-center space-x-3">
                <span class="text-xl">✅</span>
                <span class="text-sm font-medium">{{ session('success') }}</span>
            </div>
            <button onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700 font-bold">&times;</button>
        </div>
        @endif

        @if(session('error') || $errors->any())
        <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 mb-4 shadow-sm">
            <div class="flex items-center space-x-3 font-semibold mb-1">
                <span class="text-xl">⚠️</span>
                <span>Attention Required:</span>
            </div>
            @if(session('error'))
                <p class="text-sm ml-8">{{ session('error') }}</p>
            @endif
            @if($errors->any())
                <ul class="list-disc list-inside text-sm ml-8 space-y-0.5">
                    @foreach($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            @endif
        </div>
        @endif
    </div>

    <!-- Main Dynamic Content -->
    <main class="flex-1 pb-16">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-slate-200 py-6 text-center text-xs text-slate-500">
        <div class="max-w-7xl mx-auto px-4">
            <p class="font-medium text-slate-600">BioTrace Global — Web Programming 2 UAS Project</p>
            <p class="mt-1 text-slate-400">Cold-Chain Specimen & Clinical Trial Tracking • Built with Laravel, MySQL, Leaflet.js & Tailwind CSS</p>
        </div>
    </footer>

    @stack('scripts')
</body>
</html>

