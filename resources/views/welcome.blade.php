<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BioTrace — Cold-Chain Biospecimen Logistics Platform</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { font-family: 'Inter', sans-serif; }
        h1,h2,h3 { font-family: 'Space Grotesk', sans-serif; }

        /* Animated gradient background */
        .hero-bg {
            background: linear-gradient(135deg, #0a1628 0%, #0d2d4a 25%, #0a3d3a 50%, #062b2a 75%, #071e3d 100%);
            background-size: 400% 400%;
            animation: gradientShift 12s ease infinite;
        }
        @keyframes gradientShift {
            0%   { background-position: 0% 50%; }
            50%  { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        /* Floating particles */
        .particle { position: absolute; border-radius: 50%; animation: float linear infinite; }
        @keyframes float {
            0%   { transform: translateY(100vh) rotate(0deg); opacity: 0; }
            10%  { opacity: 1; }
            90%  { opacity: 1; }
            100% { transform: translateY(-100px) rotate(360deg); opacity: 0; }
        }

        /* Pulse rings */
        .pulse-ring {
            animation: pulseRing 2.5s cubic-bezier(0.215, 0.61, 0.355, 1) infinite;
        }
        @keyframes pulseRing {
            0%   { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(20, 184, 166, 0.5); }
            70%  { transform: scale(1); box-shadow: 0 0 0 20px rgba(20, 184, 166, 0); }
            100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(20, 184, 166, 0); }
        }

        /* DNA helix animation */
        .dna-dot {
            animation: dnaSpin 3s ease-in-out infinite;
        }
        @keyframes dnaSpin {
            0%, 100% { transform: scaleX(1); opacity: 1; }
            50% { transform: scaleX(-1); opacity: 0.5; }
        }

        /* Glowing card */
        .glow-card {
            transition: all 0.3s ease;
            border: 1px solid rgba(20, 184, 166, 0.2);
        }
        .glow-card:hover {
            border-color: rgba(20, 184, 166, 0.6);
            box-shadow: 0 0 30px rgba(20, 184, 166, 0.15), 0 20px 60px rgba(0,0,0,0.3);
            transform: translateY(-6px);
        }

        /* Stat counter */
        .stat-num { font-variant-numeric: tabular-nums; }

        /* Currency table row hover */
        .currency-row:hover { background: rgba(20, 184, 166, 0.08); }

        /* Scroll indicator */
        .scroll-indicator { animation: bounce 2s infinite; }
        @keyframes bounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(8px); }
        }

        /* Navbar scroll effect */
        .navbar-scrolled {
            background: rgba(10, 22, 40, 0.95) !important;
            backdrop-filter: blur(20px);
            box-shadow: 0 4px 30px rgba(0,0,0,0.5);
        }

        /* Feature icon */
        .feature-icon {
            background: linear-gradient(135deg, #0d9488, #0369a1);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        /* Live badge */
        .live-dot {
            animation: livePulse 1.5s ease-in-out infinite;
        }
        @keyframes livePulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.3; }
        }

        /* Shimmer loading */
        .shimmer {
            background: linear-gradient(90deg, rgba(255,255,255,0.05) 25%, rgba(255,255,255,0.1) 50%, rgba(255,255,255,0.05) 75%);
            background-size: 200% 100%;
            animation: shimmer 1.5s infinite;
        }
        @keyframes shimmer {
            0% { background-position: -200% 0; }
            100% { background-position: 200% 0; }
        }

        /* Section divider wave */
        .wave-divider svg { display: block; }

        /* Image overlay */
        .img-overlay {
            background: linear-gradient(to right, rgba(10,22,40,0.95) 40%, rgba(10,22,40,0.3) 100%);
        }
        .img-overlay-dark {
            background: linear-gradient(to bottom, rgba(10,22,40,0) 0%, rgba(10,22,40,0.8) 100%);
        }
    </style>
</head>
<body class="bg-slate-950 text-white overflow-x-hidden">

<!-- ========================================
     PARTICLES BACKGROUND
======================================== -->
<div class="fixed inset-0 pointer-events-none z-0 overflow-hidden">
    <div class="particle w-2 h-2 bg-teal-400/30" style="left:10%;animation-duration:15s;animation-delay:0s;"></div>
    <div class="particle w-1 h-1 bg-blue-400/40" style="left:20%;animation-duration:12s;animation-delay:2s;"></div>
    <div class="particle w-3 h-3 bg-teal-300/20" style="left:35%;animation-duration:18s;animation-delay:4s;"></div>
    <div class="particle w-1 h-1 bg-cyan-400/50" style="left:50%;animation-duration:14s;animation-delay:1s;"></div>
    <div class="particle w-2 h-2 bg-blue-300/30" style="left:65%;animation-duration:16s;animation-delay:3s;"></div>
    <div class="particle w-1 h-1 bg-teal-500/40" style="left:80%;animation-duration:13s;animation-delay:5s;"></div>
    <div class="particle w-2 h-2 bg-cyan-300/25" style="left:90%;animation-duration:17s;animation-delay:2s;"></div>
</div>

<!-- ========================================
     NAVBAR
======================================== -->
<nav id="navbar" class="fixed top-0 left-0 right-0 z-50 transition-all duration-500 py-4 px-6">
    <div class="max-w-7xl mx-auto flex items-center justify-between">
        <!-- Logo -->
        <div class="flex items-center gap-3">
            <div class="relative">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-teal-500 to-cyan-600 flex items-center justify-center pulse-ring">
                    <i class="fas fa-dna text-white text-lg"></i>
                </div>
            </div>
            <div>
                <span class="text-xl font-bold text-white tracking-tight">Bio<span class="text-teal-400">Trace</span></span>
                <div class="text-xs text-teal-400/70 font-medium -mt-1">Cold-Chain Platform</div>
            </div>
        </div>

        <!-- Nav links -->
        <div class="hidden md:flex items-center gap-8">
            <a href="#features" class="text-slate-300 hover:text-teal-400 text-sm font-medium transition-colors">Features</a>
            <a href="#how-it-works" class="text-slate-300 hover:text-teal-400 text-sm font-medium transition-colors">How It Works</a>
            <a href="#currency" class="text-slate-300 hover:text-teal-400 text-sm font-medium transition-colors">Live Rates</a>
            <a href="#stats" class="text-slate-300 hover:text-teal-400 text-sm font-medium transition-colors">Stats</a>
        </div>

        <!-- CTA Buttons -->
        <div class="flex items-center gap-3">
            <a href="{{ route('login') }}" class="text-sm font-medium text-slate-300 hover:text-white transition-colors px-4 py-2 rounded-lg hover:bg-white/5">
                Sign In
            </a>
            <a href="{{ route('register') }}" class="text-sm font-semibold bg-gradient-to-r from-teal-500 to-cyan-500 hover:from-teal-400 hover:to-cyan-400 text-white px-5 py-2.5 rounded-xl transition-all shadow-lg shadow-teal-900/50 hover:shadow-teal-800/60">
                Get Started <i class="fas fa-arrow-right ml-1 text-xs"></i>
            </a>
        </div>
    </div>
</nav>

<!-- ========================================
     HERO SECTION
======================================== -->
<section class="hero-bg relative min-h-screen flex items-center overflow-hidden">
    <!-- Background image with overlay -->
    <div class="absolute inset-0 z-0">
        <img src="https://images.unsplash.com/photo-1576091160399-112ba8d25d1d?w=1920&q=80&fit=crop"
             alt="Biomedical Lab"
             class="w-full h-full object-cover opacity-20"
             onerror="this.style.display='none'">
        <div class="absolute inset-0 img-overlay"></div>
    </div>

    <!-- Grid pattern overlay -->
    <div class="absolute inset-0 opacity-5"
         style="background-image: linear-gradient(rgba(20,184,166,0.5) 1px, transparent 1px), linear-gradient(90deg, rgba(20,184,166,0.5) 1px, transparent 1px); background-size: 60px 60px;">
    </div>

    <div class="relative z-10 max-w-7xl mx-auto px-6 pt-32 pb-24 w-full">
        <div class="grid lg:grid-cols-2 gap-16 items-center">
            <!-- Left: Text Content -->
            <div>
                <!-- Badge -->
                <div class="inline-flex items-center gap-2 bg-teal-500/10 border border-teal-500/30 text-teal-300 text-xs font-semibold px-4 py-2 rounded-full mb-6">
                    <span class="live-dot w-2 h-2 bg-teal-400 rounded-full inline-block"></span>
                    Real-Time Biospecimen Tracking
                </div>

                <h1 class="text-5xl lg:text-6xl xl:text-7xl font-bold leading-tight mb-6">
                    <span class="text-white">Track Every</span><br>
                    <span class="bg-gradient-to-r from-teal-400 via-cyan-400 to-blue-400 bg-clip-text text-transparent">
                        Biospecimen
                    </span><br>
                    <span class="text-white">With Precision</span>
                </h1>

                <p class="text-slate-400 text-lg leading-relaxed mb-8 max-w-lg">
                    BioTrace adalah platform logistik <strong class="text-teal-300">cold-chain biomedical</strong> berteknologi tinggi untuk memastikan 
                    stem cells, sampel genomik, dan biospecimen klinis tiba tepat waktu dan terjaga viabilitasnya.
                </p>

                <!-- Key metrics row -->
                <div class="flex flex-wrap gap-6 mb-10">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-lg bg-teal-500/20 flex items-center justify-center">
                            <i class="fas fa-thermometer-half text-teal-400 text-sm"></i>
                        </div>
                        <div>
                            <div class="text-white font-bold text-sm">-196°C</div>
                            <div class="text-slate-500 text-xs">Cryo Monitoring</div>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-lg bg-blue-500/20 flex items-center justify-center">
                            <i class="fas fa-route text-blue-400 text-sm"></i>
                        </div>
                        <div>
                            <div class="text-white font-bold text-sm">Real-Time</div>
                            <div class="text-slate-500 text-xs">Route Tracking</div>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-lg bg-emerald-500/20 flex items-center justify-center">
                            <i class="fas fa-shield-halved text-emerald-400 text-sm"></i>
                        </div>
                        <div>
                            <div class="text-white font-bold text-sm">ISO 13485</div>
                            <div class="text-slate-500 text-xs">Compliant</div>
                        </div>
                    </div>
                </div>

                <!-- CTA Buttons -->
                <div class="flex flex-wrap gap-4">
                    <a href="{{ route('login') }}"
                       class="inline-flex items-center gap-3 bg-gradient-to-r from-teal-600 to-cyan-600 hover:from-teal-500 hover:to-cyan-500 text-white font-semibold px-8 py-4 rounded-2xl transition-all shadow-xl shadow-teal-900/40 hover:shadow-teal-800/60 hover:-translate-y-1">
                        <i class="fas fa-sign-in-alt"></i>
                        Masuk ke Dashboard
                    </a>
                    <a href="#features"
                       class="inline-flex items-center gap-3 border border-white/20 hover:border-teal-500/50 text-white hover:text-teal-300 font-semibold px-8 py-4 rounded-2xl transition-all hover:bg-white/5">
                        <i class="fas fa-play-circle"></i>
                        Pelajari Lebih Lanjut
                    </a>
                </div>

                <!-- Quick Login Demo -->
                <div class="mt-8 p-4 bg-white/5 border border-white/10 rounded-2xl">
                    <p class="text-slate-400 text-xs mb-3 font-medium"><i class="fas fa-bolt text-yellow-400 mr-1"></i> Demo — Login 1 Klik:</p>
                    <div class="flex flex-wrap gap-2">
                        <a href="{{ route('quick-login', 'ADMIN') }}"
                           class="flex items-center gap-2 bg-purple-500/20 hover:bg-purple-500/30 border border-purple-500/30 text-purple-300 text-xs font-semibold px-3 py-2 rounded-lg transition-all">
                            <i class="fas fa-crown text-xs"></i> Admin
                        </a>
                        <a href="{{ route('quick-login', 'OPERATOR') }}"
                           class="flex items-center gap-2 bg-blue-500/20 hover:bg-blue-500/30 border border-blue-500/30 text-blue-300 text-xs font-semibold px-3 py-2 rounded-lg transition-all">
                            <i class="fas fa-user-tie text-xs"></i> Operator
                        </a>
                        <a href="{{ route('quick-login', 'CUSTOMER') }}"
                           class="flex items-center gap-2 bg-teal-500/20 hover:bg-teal-500/30 border border-teal-500/30 text-teal-300 text-xs font-semibold px-3 py-2 rounded-lg transition-all">
                            <i class="fas fa-hospital text-xs"></i> Customer
                        </a>
                    </div>
                </div>
            </div>

            <!-- Right: Visual cards -->
            <div class="hidden lg:block relative">
                <!-- Main image card -->
                <div class="relative rounded-3xl overflow-hidden shadow-2xl shadow-teal-900/30 border border-teal-500/20">
                    <img src="https://images.unsplash.com/photo-1559757148-5c350d0d3c56?w=700&q=80&fit=crop"
                         alt="Blood Samples Cold Chain"
                         class="w-full h-80 object-cover"
                         onerror="this.src='https://images.unsplash.com/photo-1582719471384-894fbb16e074?w=700&q=80&fit=crop'">
                    <div class="absolute inset-0 img-overlay-dark"></div>
                    <div class="absolute bottom-4 left-4 right-4">
                        <div class="bg-black/60 backdrop-blur-md rounded-xl p-3 border border-white/10 flex items-center gap-3">
                            <div class="w-10 h-10 bg-teal-500/20 rounded-lg flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-vial text-teal-400"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="text-white text-xs font-semibold">BT-2024-0042 · Stem Cell Sample</div>
                                <div class="text-teal-400 text-xs">In Transit · -80°C · Jakarta → Bandung</div>
                            </div>
                            <span class="flex-shrink-0 text-xs bg-emerald-500/20 text-emerald-400 border border-emerald-500/30 px-2 py-1 rounded-lg font-semibold">SAFE</span>
                        </div>
                    </div>
                </div>

                <!-- Floating stat cards -->
                <div class="absolute -top-6 -right-6 bg-slate-800/90 backdrop-blur-md border border-teal-500/30 rounded-2xl p-4 shadow-xl">
                    <div class="text-xs text-slate-400 mb-1">Active Shipments</div>
                    <div class="text-3xl font-bold text-teal-400 stat-num">247</div>
                    <div class="flex items-center gap-1 text-emerald-400 text-xs mt-1">
                        <i class="fas fa-arrow-up text-xs"></i> 12% this week
                    </div>
                </div>

                <div class="absolute -bottom-6 -left-6 bg-slate-800/90 backdrop-blur-md border border-blue-500/30 rounded-2xl p-4 shadow-xl">
                    <div class="text-xs text-slate-400 mb-1">Temp Compliance</div>
                    <div class="text-3xl font-bold text-blue-400 stat-num">99.8%</div>
                    <div class="flex items-center gap-1 text-emerald-400 text-xs mt-1">
                        <i class="fas fa-check-circle text-xs"></i> All samples safe
                    </div>
                </div>

                <!-- Second image -->
                <div class="mt-4 rounded-2xl overflow-hidden border border-white/10 shadow-lg">
                    <img src="https://images.unsplash.com/photo-1584036561566-baf8f5f1b144?w=700&q=80&fit=crop"
                         alt="Medical Cold Chain"
                         class="w-full h-40 object-cover opacity-80">
                </div>
            </div>
        </div>
    </div>

    <!-- Scroll indicator -->
    <div class="absolute bottom-8 left-1/2 -translate-x-1/2 scroll-indicator">
        <div class="flex flex-col items-center gap-2 text-slate-500">
            <span class="text-xs font-medium">Scroll</span>
            <i class="fas fa-chevron-down text-teal-500"></i>
        </div>
    </div>
</section>

<!-- Wave divider -->
<div class="wave-divider bg-slate-950 relative -mt-1">
    <svg viewBox="0 0 1440 80" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="none" class="w-full" style="fill:#0a1628; transform: rotate(180deg);">
        <path d="M0,40 C360,80 1080,0 1440,40 L1440,80 L0,80 Z"/>
    </svg>
</div>

<!-- ========================================
     BIOSPECIMEN TYPES BANNER
======================================== -->
<section class="bg-slate-950 py-12 border-y border-white/5">
    <div class="max-w-7xl mx-auto px-6">
        <p class="text-center text-slate-500 text-sm font-medium mb-8">Cargo Biomedical yang Kami Tangani</p>
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4">
            @foreach([
                ['fas fa-droplet', 'Stem Cell', '-196°C Cryo', 'teal'],
                ['fas fa-dna', 'Genomic DNA', '-80°C Ultra', 'blue'],
                ['fas fa-flask-vial', 'Biopsi Jaringan', '2-8°C Chilled', 'cyan'],
                ['fas fa-vial', 'Darah Klinis', '15-25°C Amb', 'emerald'],
                ['fas fa-eye-dropper', 'Plasma/Serum', '-20°C Frozen', 'indigo'],
                ['fas fa-microscope', 'Kultur Sel', '37°C CO₂', 'violet'],
            ] as [$icon, $label, $temp, $color])
            <div class="glow-card bg-slate-900/50 rounded-2xl p-4 text-center group cursor-default">
                <div class="w-12 h-12 mx-auto mb-3 rounded-xl bg-{{ $color }}-500/10 flex items-center justify-center group-hover:scale-110 transition-transform">
                    <i class="{{ $icon }} text-{{ $color }}-400 text-xl"></i>
                </div>
                <div class="text-white text-sm font-semibold">{{ $label }}</div>
                <div class="text-{{ $color }}-400 text-xs mt-1 font-mono">{{ $temp }}</div>
            </div>
            @endforeach
        </div>
    </div>
</section>

<!-- ========================================
     FEATURES SECTION
======================================== -->
<section id="features" class="bg-slate-950 py-24">
    <div class="max-w-7xl mx-auto px-6">
        <!-- Section header -->
        <div class="text-center mb-16">
            <div class="inline-flex items-center gap-2 bg-teal-500/10 border border-teal-500/20 text-teal-400 text-xs font-semibold px-4 py-2 rounded-full mb-4">
                <i class="fas fa-star"></i> Platform Features
            </div>
            <h2 class="text-4xl lg:text-5xl font-bold text-white mb-4">
                Teknologi <span class="text-teal-400">Medical-Grade</span><br>untuk Logistik Biomedis
            </h2>
            <p class="text-slate-400 max-w-2xl mx-auto">Setiap fitur dirancang khusus untuk memenuhi standar regulasi biomedical internasional</p>
        </div>

        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach([
                ['fas fa-temperature-low', 'Cold-Chain Monitoring', 'Monitor suhu real-time dari -196°C hingga ambient. Alert otomatis saat terjadi temperature excursion yang membahayakan viabilitas sampel.', 'teal', 'Suhu Terjaga'],
                ['fas fa-map-location-dot', 'Live GPS Tracking', 'Pantau posisi setiap shipment secara real-time menggunakan peta interaktif Leaflet.js dengan data rute terkini.', 'blue', 'Lokasi Akurat'],
                ['fas fa-triangle-exclamation', 'Exception Management', 'Sistem deteksi dan eskalasi otomatis untuk customs hold, temperature breach, dan delay kritis dengan SOP response terstruktur.', 'amber', 'Zero Miss'],
                ['fas fa-cloud-sun', 'Weather Intelligence', 'Integrasi Open-Meteo API untuk prediksi cuaca di rute pengiriman, membantu antisipasi risiko logistik proaktif.', 'cyan', 'Cuaca Terpantau'],
                ['fas fa-clock-rotate-left', 'ETA Viability Engine', 'Kalkulasi otomatis dampak delay terhadap viabilitas sampel. Severity scoring: NONE/LOW/MEDIUM/HIGH berdasarkan jenis biospecimen.', 'emerald', 'Delay Scoring'],
                ['fas fa-code', 'REST API Sanctum', 'API endpoint terautentikasi dengan Laravel Sanctum untuk integrasi dengan sistem LIMS, HIS, atau aplikasi mobile rumah sakit.', 'violet', 'API Ready'],
            ] as [$icon, $title, $desc, $color, $badge])
            <div class="glow-card bg-slate-900/60 rounded-2xl p-6 backdrop-blur-sm">
                <div class="flex items-start justify-between mb-4">
                    <div class="w-12 h-12 rounded-xl bg-{{ $color }}-500/15 flex items-center justify-center">
                        <i class="{{ $icon }} text-{{ $color }}-400 text-xl"></i>
                    </div>
                    <span class="text-xs font-semibold bg-{{ $color }}-500/10 text-{{ $color }}-400 border border-{{ $color }}-500/20 px-2 py-1 rounded-lg">{{ $badge }}</span>
                </div>
                <h3 class="text-white font-semibold text-lg mb-2">{{ $title }}</h3>
                <p class="text-slate-400 text-sm leading-relaxed">{{ $desc }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>

<!-- ========================================
     HOW IT WORKS
======================================== -->
<section id="how-it-works" class="py-24 bg-gradient-to-b from-slate-950 to-slate-900">
    <div class="max-w-7xl mx-auto px-6">
        <div class="text-center mb-16">
            <div class="inline-flex items-center gap-2 bg-blue-500/10 border border-blue-500/20 text-blue-400 text-xs font-semibold px-4 py-2 rounded-full mb-4">
                <i class="fas fa-diagram-project"></i> Alur Kerja
            </div>
            <h2 class="text-4xl font-bold text-white mb-4">Bagaimana BioTrace Bekerja</h2>
            <p class="text-slate-400 max-w-xl mx-auto">Dari pendaftaran pengiriman hingga konfirmasi penerimaan — semua terpantau dalam satu platform</p>
        </div>

        <!-- Steps with connecting line -->
        <div class="relative">
            <div class="hidden lg:block absolute top-16 left-1/2 -translate-x-1/2 w-3/4 h-0.5 bg-gradient-to-r from-transparent via-teal-500/50 to-transparent"></div>
            <div class="grid lg:grid-cols-5 gap-8">
                @foreach([
                    ['fas fa-file-medical', '01', 'Registrasi', 'Input data biospecimen, carrier, suhu target, dan rute pengiriman', 'teal'],
                    ['fas fa-box-open', '02', 'Pick-Up', 'Konfirmasi pengambilan sampel dari lab/RS dengan tracking event real-time', 'blue'],
                    ['fas fa-plane', '03', 'In-Transit', 'Monitor suhu, posisi, dan kondisi selama perjalanan antar kota/negara', 'cyan'],
                    ['fas fa-clipboard-check', '04', 'Clearance', 'Penanganan bea cukai & dokumentasi regulatory di titik masuk', 'indigo'],
                    ['fas fa-circle-check', '05', 'Delivered', 'Konfirmasi penerimaan, laporan viabilitas, dan audit trail lengkap', 'emerald'],
                ] as [$icon, $num, $step, $desc, $color])
                <div class="flex flex-col items-center text-center">
                    <div class="relative mb-4">
                        <div class="w-16 h-16 rounded-2xl bg-{{ $color }}-500/15 border border-{{ $color }}-500/30 flex items-center justify-center shadow-lg">
                            <i class="{{ $icon }} text-{{ $color }}-400 text-2xl"></i>
                        </div>
                        <div class="absolute -top-2 -right-2 w-6 h-6 rounded-full bg-{{ $color }}-500 text-white text-xs font-bold flex items-center justify-center">{{ $num }}</div>
                    </div>
                    <h3 class="text-white font-semibold mb-2">{{ $step }}</h3>
                    <p class="text-slate-400 text-sm leading-relaxed">{{ $desc }}</p>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Screenshots / mockup area -->
        <div class="mt-20 grid lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 rounded-2xl overflow-hidden border border-white/10 shadow-2xl">
                <img src="https://images.unsplash.com/photo-1582719471384-894fbb16e074?w=900&q=80&fit=crop"
                     alt="Biomedical Lab Operations"
                     class="w-full h-64 object-cover"
                     onerror="this.src='https://images.unsplash.com/photo-1576091160399-112ba8d25d1d?w=900&q=80'">
                <div class="bg-slate-900 p-4 border-t border-white/5">
                    <div class="flex items-center gap-3">
                        <i class="fas fa-microscope text-teal-400"></i>
                        <div>
                            <div class="text-white text-sm font-semibold">Lab Origin — Verifikasi Kualitas</div>
                            <div class="text-slate-400 text-xs">Setiap sampel diverifikasi sebelum diserahkan ke carrier</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="rounded-2xl overflow-hidden border border-white/10 shadow-2xl">
                <img src="https://images.unsplash.com/photo-1587351021759-3e566b6af7cc?w=400&q=80&fit=crop"
                     alt="Cold Storage Container"
                     class="w-full h-64 object-cover"
                     onerror="this.src='https://images.unsplash.com/photo-1584036561566-baf8f5f1b144?w=400&q=80'">
                <div class="bg-slate-900 p-4 border-t border-white/5">
                    <div class="flex items-center gap-3">
                        <i class="fas fa-box text-blue-400"></i>
                        <div>
                            <div class="text-white text-sm font-semibold">Cryo Container Tracking</div>
                            <div class="text-slate-400 text-xs">Monitor nitrogen liquid level & suhu real-time</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ========================================
     LIVE CURRENCY EXCHANGE
======================================== -->
<section id="currency" class="py-24 bg-slate-900">
    <div class="max-w-7xl mx-auto px-6">
        <div class="grid lg:grid-cols-5 gap-12 items-start">
            <!-- Left: Info -->
            <div class="lg:col-span-2">
                <div class="inline-flex items-center gap-2 bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-xs font-semibold px-4 py-2 rounded-full mb-6">
                    <span class="live-dot w-2 h-2 bg-emerald-400 rounded-full"></span>
                    Live Exchange Rates
                </div>
                <h2 class="text-3xl lg:text-4xl font-bold text-white mb-4">
                    Kurs Mata Uang<br><span class="text-emerald-400">Real-Time</span>
                </h2>
                <p class="text-slate-400 leading-relaxed mb-6">
                    Data kurs diperbarui otomatis setiap saat dari <strong class="text-white">Frankfurter API</strong> (European Central Bank). 
                    Penting untuk kalkulasi biaya pengiriman biospecimen internasional lintas negara.
                </p>

                <div class="space-y-4">
                    <div class="flex items-start gap-3">
                        <div class="w-8 h-8 rounded-lg bg-blue-500/20 flex items-center justify-center flex-shrink-0 mt-0.5">
                            <i class="fas fa-globe text-blue-400 text-sm"></i>
                        </div>
                        <div>
                            <div class="text-white text-sm font-semibold">Multi-Currency Invoicing</div>
                            <div class="text-slate-400 text-xs mt-1">Tagih biaya pengiriman dalam 10+ mata uang secara akurat</div>
                        </div>
                    </div>
                    <div class="flex items-start gap-3">
                        <div class="w-8 h-8 rounded-lg bg-teal-500/20 flex items-center justify-center flex-shrink-0 mt-0.5">
                            <i class="fas fa-sync-alt text-teal-400 text-sm"></i>
                        </div>
                        <div>
                            <div class="text-white text-sm font-semibold">Auto-refresh Setiap 60 Detik</div>
                            <div class="text-slate-400 text-xs mt-1">Kurs selalu terkini dari European Central Bank</div>
                        </div>
                    </div>
                    <div class="flex items-start gap-3">
                        <div class="w-8 h-8 rounded-lg bg-violet-500/20 flex items-center justify-center flex-shrink-0 mt-0.5">
                            <i class="fas fa-calculator text-violet-400 text-sm"></i>
                        </div>
                        <div>
                            <div class="text-white text-sm font-semibold">Konversi Instan</div>
                            <div class="text-slate-400 text-xs mt-1">Konversi dari USD ke mata uang tujuan pengiriman</div>
                        </div>
                    </div>
                </div>

                <!-- Currency converter -->
                <div class="mt-8 bg-slate-800/60 border border-white/10 rounded-2xl p-5">
                    <div class="text-xs text-slate-400 font-medium mb-3">Quick Converter</div>
                    <div class="flex gap-2 mb-3">
                        <input type="number" id="convertAmount" value="100" min="1"
                               class="flex-1 bg-slate-700/50 border border-white/10 text-white rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-teal-500 transition-colors">
                        <select id="convertFrom"
                                class="bg-slate-700/50 border border-white/10 text-white rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:border-teal-500 transition-colors">
                            <option value="USD">USD</option>
                            <option value="EUR">EUR</option>
                            <option value="IDR">IDR</option>
                            <option value="SGD">SGD</option>
                        </select>
                    </div>
                    <div id="convertResult" class="text-teal-400 font-semibold text-sm min-h-[2rem] flex items-center">
                        <i class="fas fa-spinner fa-spin mr-2 text-xs"></i> Memuat kurs...
                    </div>
                </div>
            </div>

            <!-- Right: Currency table -->
            <div class="lg:col-span-3">
                <div class="bg-slate-800/40 border border-white/10 rounded-2xl overflow-hidden shadow-2xl">
                    <!-- Table header -->
                    <div class="flex items-center justify-between p-4 border-b border-white/5 bg-slate-800/60">
                        <div class="flex items-center gap-2">
                            <i class="fas fa-chart-line text-emerald-400"></i>
                            <span class="text-white font-semibold text-sm">Exchange Rates vs USD</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="live-dot w-2 h-2 bg-emerald-400 rounded-full inline-block"></span>
                            <span class="text-emerald-400 text-xs font-medium">LIVE</span>
                            <span id="lastUpdated" class="text-slate-500 text-xs ml-2"></span>
                        </div>
                    </div>

                    <!-- Table -->
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="border-b border-white/5">
                                    <th class="px-4 py-3 text-left text-slate-400 font-medium text-xs">MATA UANG</th>
                                    <th class="px-4 py-3 text-left text-slate-400 font-medium text-xs">KODE</th>
                                    <th class="px-4 py-3 text-right text-slate-400 font-medium text-xs">1 USD =</th>
                                    <th class="px-4 py-3 text-right text-slate-400 font-medium text-xs">STATUS</th>
                                </tr>
                            </thead>
                            <tbody id="currencyTable">
                                <!-- Loading skeleton -->
                                @for($i = 0; $i < 10; $i++)
                                <tr class="border-b border-white/5">
                                    <td class="px-4 py-3"><div class="shimmer h-4 w-32 rounded"></div></td>
                                    <td class="px-4 py-3"><div class="shimmer h-4 w-12 rounded"></div></td>
                                    <td class="px-4 py-3 text-right"><div class="shimmer h-4 w-24 rounded ml-auto"></div></td>
                                    <td class="px-4 py-3 text-right"><div class="shimmer h-4 w-16 rounded ml-auto"></div></td>
                                </tr>
                                @endfor
                            </tbody>
                        </table>
                    </div>

                    <div class="p-3 border-t border-white/5 text-center">
                        <span class="text-slate-500 text-xs">Source: Frankfurter API (European Central Bank) · Auto-refresh 60s</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ========================================
     STATS SECTION
======================================== -->
<section id="stats" class="py-24 bg-gradient-to-b from-slate-900 to-slate-950 relative overflow-hidden">
    <div class="absolute inset-0 opacity-10"
         style="background: radial-gradient(ellipse at 20% 50%, #0d9488 0%, transparent 50%), radial-gradient(ellipse at 80% 50%, #1d4ed8 0%, transparent 50%);">
    </div>
    <div class="max-w-7xl mx-auto px-6 relative z-10">
        <div class="text-center mb-16">
            <h2 class="text-4xl font-bold text-white mb-4">BioTrace dalam Angka</h2>
            <p class="text-slate-400">Platform kepercayaan institusi riset dan rumah sakit terkemuka</p>
        </div>

        <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-8">
            @foreach([
                ['fas fa-vials', '12,847', 'Sampel Terkirim', 'Biospecimen berhasil dikirim tanpa insiden', 'teal'],
                ['fas fa-hospital', '89', 'Institusi Partner', 'RS, Biolab, dan Pusat Riset terintegrasi', 'blue'],
                ['fas fa-thermometer-quarter', '99.8%', 'Cold-Chain Compliance', 'Suhu terjaga sepanjang perjalanan', 'emerald'],
                ['fas fa-clock', '< 2 jam', 'Avg Response Time', 'Waktu respons rata-rata untuk exception', 'violet'],
            ] as [$icon, $num, $label, $desc, $color])
            <div class="text-center group">
                <div class="w-16 h-16 mx-auto mb-4 rounded-2xl bg-{{ $color }}-500/10 border border-{{ $color }}-500/20 flex items-center justify-center group-hover:scale-110 transition-transform">
                    <i class="{{ $icon }} text-{{ $color }}-400 text-2xl"></i>
                </div>
                <div class="text-4xl font-bold text-white mb-1 stat-num">{{ $num }}</div>
                <div class="text-{{ $color }}-400 font-semibold text-sm mb-2">{{ $label }}</div>
                <div class="text-slate-500 text-xs leading-relaxed">{{ $desc }}</div>
            </div>
            @endforeach
        </div>
    </div>
</section>

<!-- ========================================
     TRUST / COMPLIANCE SECTION
======================================== -->
<section class="py-16 bg-slate-950 border-t border-white/5">
    <div class="max-w-7xl mx-auto px-6">
        <p class="text-center text-slate-500 text-sm mb-10">Memenuhi Standar Regulasi Internasional</p>
        <div class="flex flex-wrap justify-center items-center gap-8 lg:gap-16">
            @foreach(['IATA P650', 'ISBER 2018', 'ISO 13485', 'GxP Compliant', 'WHO TC-01', 'GDPR Ready'] as $standard)
            <div class="text-center group">
                <div class="w-14 h-14 mx-auto mb-2 rounded-2xl bg-white/5 border border-white/10 flex items-center justify-center group-hover:border-teal-500/40 transition-colors">
                    <i class="fas fa-certificate text-teal-500/60 text-xl group-hover:text-teal-400 transition-colors"></i>
                </div>
                <div class="text-slate-400 text-xs font-semibold group-hover:text-white transition-colors">{{ $standard }}</div>
            </div>
            @endforeach
        </div>
    </div>
</section>

<!-- ========================================
     CTA SECTION
======================================== -->
<section class="py-24 bg-gradient-to-b from-slate-950 to-slate-900">
    <div class="max-w-4xl mx-auto px-6 text-center">
        <div class="relative bg-gradient-to-br from-teal-900/40 to-blue-900/40 border border-teal-500/20 rounded-3xl p-12 shadow-2xl overflow-hidden">
            <div class="absolute inset-0 opacity-10"
                 style="background: radial-gradient(circle at 50% 0%, #14b8a6 0%, transparent 60%);">
            </div>
            <div class="relative z-10">
                <div class="w-20 h-20 mx-auto mb-6 rounded-3xl bg-teal-500/20 flex items-center justify-center">
                    <i class="fas fa-dna text-teal-400 text-3xl"></i>
                </div>
                <h2 class="text-4xl font-bold text-white mb-4">
                    Siap Mulai Tracking<br>Biospecimen Anda?
                </h2>
                <p class="text-slate-400 mb-8 text-lg">
                    Bergabunglah dengan institusi medis dan penelitian yang telah mempercayai BioTrace untuk keamanan sampel biomedis mereka.
                </p>
                <div class="flex flex-wrap justify-center gap-4">
                    <a href="{{ route('register') }}"
                       class="inline-flex items-center gap-3 bg-gradient-to-r from-teal-500 to-cyan-500 hover:from-teal-400 hover:to-cyan-400 text-white font-bold px-10 py-4 rounded-2xl transition-all shadow-xl shadow-teal-900/50 hover:-translate-y-1">
                        <i class="fas fa-user-plus"></i>
                        Daftar Gratis Sekarang
                    </a>
                    <a href="{{ route('login') }}"
                       class="inline-flex items-center gap-3 border border-white/20 hover:border-teal-500/50 text-white font-semibold px-10 py-4 rounded-2xl transition-all hover:bg-white/5">
                        <i class="fas fa-sign-in-alt"></i>
                        Sudah Punya Akun
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ========================================
     FOOTER
======================================== -->
<footer class="bg-slate-900 border-t border-white/5 py-12">
    <div class="max-w-7xl mx-auto px-6">
        <div class="grid md:grid-cols-4 gap-8 mb-8">
            <!-- Brand -->
            <div class="md:col-span-2">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-teal-500 to-cyan-600 flex items-center justify-center">
                        <i class="fas fa-dna text-white"></i>
                    </div>
                    <div>
                        <span class="text-xl font-bold text-white">Bio<span class="text-teal-400">Trace</span></span>
                        <div class="text-xs text-teal-400/70 -mt-1">Cold-Chain Biospecimen Platform</div>
                    </div>
                </div>
                <p class="text-slate-400 text-sm leading-relaxed max-w-sm">
                    Platform tracking biospecimen cold-chain berteknologi tinggi untuk rumah sakit, laboratorium, dan institusi riset biomedis Indonesia dan internasional.
                </p>
            </div>

            <!-- Links -->
            <div>
                <h4 class="text-white font-semibold text-sm mb-4">Platform</h4>
                <ul class="space-y-2 text-slate-400 text-sm">
                    <li><a href="#features" class="hover:text-teal-400 transition-colors">Features</a></li>
                    <li><a href="#how-it-works" class="hover:text-teal-400 transition-colors">How It Works</a></li>
                    <li><a href="#currency" class="hover:text-teal-400 transition-colors">Live Rates</a></li>
                    <li><a href="{{ route('login') }}" class="hover:text-teal-400 transition-colors">Dashboard</a></li>
                </ul>
            </div>

            <div>
                <h4 class="text-white font-semibold text-sm mb-4">Kontak</h4>
                <ul class="space-y-2 text-slate-400 text-sm">
                    <li class="flex items-center gap-2"><i class="fas fa-envelope text-teal-500 text-xs"></i> info@biotrace.io</li>
                    <li class="flex items-center gap-2"><i class="fas fa-phone text-teal-500 text-xs"></i> +62 21 1234 5678</li>
                    <li class="flex items-center gap-2"><i class="fas fa-location-dot text-teal-500 text-xs"></i> Jakarta, Indonesia</li>
                </ul>
            </div>
        </div>

        <div class="border-t border-white/5 pt-6 flex flex-col md:flex-row items-center justify-between gap-4">
            <p class="text-slate-500 text-xs">
                &copy; {{ date('Y') }} BioTrace Platform. All rights reserved. &mdash; Final Project UAS Web Programming 2
            </p>
            <div class="flex items-center gap-4 text-slate-500 text-xs">
                <span class="flex items-center gap-1"><i class="fas fa-shield-halved text-teal-500"></i> ISO 13485 Compliant</span>
                <span class="flex items-center gap-1"><i class="fas fa-lock text-blue-500"></i> End-to-End Encrypted</span>
            </div>
        </div>
    </div>
</footer>

<!-- ========================================
     JAVASCRIPT — Navbar + Currency API
======================================== -->
<script>
// ---- Navbar scroll effect ----
const navbar = document.getElementById('navbar');
window.addEventListener('scroll', () => {
    if (window.scrollY > 50) {
        navbar.classList.add('navbar-scrolled');
    } else {
        navbar.classList.remove('navbar-scrolled');
    }
});

// ---- Currency data config ----
const currencies = [
    { code: 'IDR', name: 'Rupiah Indonesia', flag: '🇮🇩', region: 'Asia Tenggara' },
    { code: 'EUR', name: 'Euro', flag: '🇪🇺', region: 'Eropa' },
    { code: 'GBP', name: 'Pound Sterling', flag: '🇬🇧', region: 'Inggris' },
    { code: 'JPY', name: 'Yen Jepang', flag: '🇯🇵', region: 'Asia Timur' },
    { code: 'SGD', name: 'Dolar Singapura', flag: '🇸🇬', region: 'Asia Tenggara' },
    { code: 'AUD', name: 'Dolar Australia', flag: '🇦🇺', region: 'Oseania' },
    { code: 'CNY', name: 'Yuan Tiongkok', flag: '🇨🇳', region: 'Asia Timur' },
    { code: 'KRW', name: 'Won Korea', flag: '🇰🇷', region: 'Asia Timur' },
    { code: 'MYR', name: 'Ringgit Malaysia', flag: '🇲🇾', region: 'Asia Tenggara' },
    { code: 'THB', name: 'Baht Thailand', flag: '🇹🇭', region: 'Asia Tenggara' },
    { code: 'CHF', name: 'Franc Swiss', flag: '🇨🇭', region: 'Eropa' },
    { code: 'CAD', name: 'Dolar Kanada', flag: '🇨🇦', region: 'Amerika Utara' },
];

let ratesData = {};
let previousRates = {};

function formatRate(code, rate) {
    if (code === 'IDR' || code === 'KRW' || code === 'JPY') {
        return rate.toLocaleString('id-ID', { minimumFractionDigits: 0, maximumFractionDigits: 0 });
    }
    return rate.toLocaleString('en-US', { minimumFractionDigits: 4, maximumFractionDigits: 4 });
}

function getTrend(code) {
    if (!previousRates[code] || !ratesData[code]) return 'neutral';
    if (ratesData[code] > previousRates[code]) return 'up';
    if (ratesData[code] < previousRates[code]) return 'down';
    return 'neutral';
}

function renderCurrencyTable() {
    const tbody = document.getElementById('currencyTable');
    tbody.innerHTML = '';

    currencies.forEach(c => {
        const rate = ratesData[c.code];
        if (!rate) return;

        const trend = getTrend(c.code);
        const trendIcon = trend === 'up'
            ? '<i class="fas fa-arrow-up text-xs"></i>'
            : trend === 'down'
            ? '<i class="fas fa-arrow-down text-xs"></i>'
            : '<i class="fas fa-minus text-xs"></i>';
        const trendColor = trend === 'up' ? 'text-red-400' : trend === 'down' ? 'text-emerald-400' : 'text-slate-500';
        const trendBg = trend === 'up' ? 'bg-red-500/10' : trend === 'down' ? 'bg-emerald-500/10' : 'bg-slate-500/10';

        const tr = document.createElement('tr');
        tr.className = 'currency-row border-b border-white/5 transition-colors cursor-default';
        tr.innerHTML = `
            <td class="px-4 py-3">
                <div class="flex items-center gap-2">
                    <span class="text-xl">${c.flag}</span>
                    <div>
                        <div class="text-white text-sm font-medium">${c.name}</div>
                        <div class="text-slate-500 text-xs">${c.region}</div>
                    </div>
                </div>
            </td>
            <td class="px-4 py-3">
                <span class="font-mono text-xs font-bold bg-slate-700/50 text-teal-300 px-2 py-1 rounded-lg">${c.code}</span>
            </td>
            <td class="px-4 py-3 text-right">
                <span class="text-white font-semibold font-mono">${formatRate(c.code, rate)}</span>
                <span class="text-slate-400 text-xs ml-1">${c.code}</span>
            </td>
            <td class="px-4 py-3 text-right">
                <span class="inline-flex items-center gap-1 ${trendColor} ${trendBg} text-xs font-semibold px-2 py-1 rounded-lg">
                    ${trendIcon}
                    ${trend === 'neutral' ? 'Stable' : trend === 'up' ? 'Naik' : 'Turun'}
                </span>
            </td>
        `;
        tbody.appendChild(tr);
    });
}

function updateConverter() {
    const amount = parseFloat(document.getElementById('convertAmount').value) || 100;
    const from = document.getElementById('convertFrom').value;
    const el = document.getElementById('convertResult');

    if (!ratesData['IDR'] || !ratesData[from]) {
        el.innerHTML = '<i class="fas fa-spinner fa-spin mr-2 text-xs"></i> Memuat...';
        return;
    }

    // Convert from selected currency to USD first, then to IDR
    let usd = from === 'USD' ? amount : amount / ratesData[from];
    const results = [
        { code: 'IDR', name: 'IDR', flag: '🇮🇩' },
        { code: 'SGD', name: 'SGD', flag: '🇸🇬' },
        { code: 'EUR', name: 'EUR', flag: '🇪🇺' },
    ].filter(r => r.code !== from);

    el.innerHTML = results.slice(0, 2).map(r => {
        const val = r.code === 'USD' ? usd : usd * ratesData[r.code];
        return `<span class="mr-4">${r.flag} <strong class="text-white">${formatRate(r.code, val)}</strong> <span class="text-slate-400 text-xs">${r.code}</span></span>`;
    }).join('');
}

async function fetchRates() {
    const codes = currencies.map(c => c.code).join(',');
    try {
        const res = await fetch(`https://api.frankfurter.app/latest?from=USD&to=${codes}`);
        if (!res.ok) throw new Error('API error');
        const data = await res.json();

        previousRates = { ...ratesData };
        ratesData = { ...data.rates, USD: 1 };

        renderCurrencyTable();
        updateConverter();

        const now = new Date();
        document.getElementById('lastUpdated').textContent =
            `Updated ${now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' })}`;
    } catch (err) {
        // Fallback static rates if API fails
        if (Object.keys(ratesData).length === 0) {
            ratesData = {
                IDR: 15950, EUR: 0.9218, GBP: 0.7891, JPY: 149.82,
                SGD: 1.3401, AUD: 1.5321, CNY: 7.2401, KRW: 1324.5,
                MYR: 4.6901, THB: 35.21, CHF: 0.8921, CAD: 1.3621
            };
            renderCurrencyTable();
            updateConverter();
            document.getElementById('lastUpdated').textContent = '(offline fallback)';
        }
    }
}

// Initialize
fetchRates();
setInterval(fetchRates, 60000); // Auto-refresh every 60 seconds

// Converter events
document.getElementById('convertAmount').addEventListener('input', updateConverter);
document.getElementById('convertFrom').addEventListener('change', updateConverter);

// Smooth scroll
document.querySelectorAll('a[href^="#"]').forEach(a => {
    a.addEventListener('click', e => {
        e.preventDefault();
        const target = document.querySelector(a.getAttribute('href'));
        if (target) target.scrollIntoView({ behavior: 'smooth', block: 'start' });
    });
});
</script>

</body>
</html>
