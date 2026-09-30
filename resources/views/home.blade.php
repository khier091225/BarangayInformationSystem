<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#276747">
    <link rel="icon" type="image/jpeg" href="{{ asset('images/Logo_kay-anlog.jpg') }}">
    <title>Barangay Kay-Anlog | Official Information &amp; E-Services Portal</title>
    @fonts
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        .glass-panel {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border-bottom: 1px solid rgb(225 231 222 / 80%);
        }
        dialog::backdrop {
            background: rgb(12 48 42 / 65%);
            backdrop-filter: blur(6px);
            -webkit-backdrop-filter: blur(6px);
        }
        dialog {
            margin: auto;
        }
        .home-hero :focus-visible, #emergency :focus-visible {
            outline-color: var(--sidebar-active);
        }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 font-sans antialiased selection:bg-emerald-200 selection:text-emerald-900">
    <!-- Utility Bar -->
    <div class="bg-primary-active text-emerald-50 py-1.5 sm:py-2 border-b border-emerald-900/60 relative z-50">
        <div class="container mx-auto px-4 lg:px-8 flex flex-col sm:flex-row justify-between items-center gap-1 sm:gap-4 text-[0.7rem] sm:text-xs">
            <div class="flex items-center gap-1.5 sm:gap-2 text-center sm:text-left">
                <i data-lucide="flag" class="w-3.5 h-3.5 text-amber-400 flex-shrink-0"></i>
                <span class="tracking-wide text-emerald-100">Republic of the Philippines &middot; Calamba City &middot; <strong class="font-semibold text-white">Barangay Kay-Anlog</strong></span>
            </div>
            <div class="flex items-center gap-3 sm:gap-4 font-medium text-emerald-200">
                <span class="flex items-center gap-1.5">
                    <i data-lucide="clock" class="w-3 h-3 sm:w-3.5 sm:h-3.5 text-emerald-400"></i>
                    Mon&ndash;Fri, 8:00 AM &ndash; 5:00 PM
                </span>
                <span class="inline-flex items-center gap-1.5 text-amber-300 font-semibold">
                    <i data-lucide="phone" class="w-3 h-3"></i>
                    Hotline: <a href="tel:911" class="hover:underline">911</a>
                </span>
            </div>
        </div>
    </div>

    <!-- Header & Navigation -->
    <header class="sticky top-0 z-40 glass-panel shadow-sm transition-all duration-200">
        <div class="container mx-auto px-4 lg:px-8 h-16 sm:h-20 flex items-center justify-between">
            <!-- Brand -->
            <a href="{{ route('home') }}" class="flex items-center gap-2.5 sm:gap-3.5 group min-w-0">
                <img src="{{ asset('images/Logo_kay-anlog.jpg') }}" alt="" aria-hidden="true" width="44" height="44" class="w-9 h-9 sm:w-11 sm:h-11 rounded-full object-cover bg-white border border-slate-200 shadow-sm flex-shrink-0">
                <div class="min-w-0">
                    <h1 class="text-sm sm:text-lg lg:text-xl font-extrabold text-slate-900 tracking-tight leading-none group-hover:text-emerald-800 transition-colors whitespace-nowrap">Barangay Kay-Anlog</h1>
                    <span class="hidden sm:block text-xs font-bold text-emerald-600 tracking-wider sm:tracking-[0.16em] uppercase truncate mt-0.5">Information &amp; E-Services</span>
                </div>
            </a>
            
            <!-- Desktop Nav Links -->
            <nav class="hidden lg:flex items-center gap-8 text-sm font-semibold text-slate-600">
                <a href="#services" class="hover:text-emerald-700 transition-colors py-2">Services</a>
                <a href="#how-it-works" class="hover:text-emerald-700 transition-colors py-2">How It Works</a>
                <a href="#demographics" class="hover:text-emerald-700 transition-colors py-2">Demographics</a>
                <a href="#officials" class="hover:text-emerald-700 transition-colors py-2">Officials</a>
                <a href="#emergency" class="hover:text-emerald-700 transition-colors py-2">Hotlines</a>
            </nav>

            <!-- Header Actions -->
            <div class="flex items-center gap-2 sm:gap-3 flex-shrink-0">
                @auth
                    @if (auth()->user()->role === 'staff')
                        <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-1.5 sm:gap-2 px-3.5 sm:px-5 py-2 sm:py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs sm:text-sm font-semibold rounded-xl transition-all shadow-sm hover:shadow-md hover:shadow-emerald-900/10 whitespace-nowrap">
                            <i data-lucide="layout-dashboard" class="w-4 h-4"></i>
                            <span class="hidden sm:inline">Staff</span> Dashboard
                        </a>
                    @else
                        <a href="{{ route('account') }}" class="inline-flex items-center gap-1.5 sm:gap-2 px-3.5 sm:px-5 py-2 sm:py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs sm:text-sm font-semibold rounded-xl transition-all shadow-sm hover:shadow-md hover:shadow-emerald-900/10 whitespace-nowrap">
                            <i data-lucide="user-round" class="w-4 h-4"></i>
                            <span class="hidden sm:inline">Resident</span> Portal
                        </a>
                    @endif
                @else
                    <a href="{{ route('register') }}" class="hidden sm:inline-flex items-center px-4 py-2.5 text-sm font-semibold text-slate-700 hover:text-emerald-700 hover:bg-emerald-50/70 rounded-xl transition-colors">Register</a>
                    <a href="{{ route('login') }}" class="whitespace-nowrap inline-flex items-center gap-1.5 sm:gap-2 px-3.5 sm:px-5 py-2 sm:py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs sm:text-sm font-semibold rounded-xl transition-all shadow-sm hover:shadow-md hover:shadow-emerald-900/15">
                        <i data-lucide="log-in" class="w-3.5 h-3.5 sm:w-4 sm:h-4"></i>
                        <span>Sign In</span>
                    </a>
                @endauth

                <!-- Mobile Menu Button -->
                <button id="mobile-menu-btn" type="button" class="lg:hidden p-2 text-slate-600 hover:text-slate-900 hover:bg-slate-100 rounded-xl transition-colors" aria-label="Toggle mobile menu" aria-expanded="false">
                    <i data-lucide="menu" id="menu-icon" class="w-5 h-5 sm:w-6 sm:h-6"></i>
                    <i data-lucide="x" id="close-icon" class="w-5 h-5 sm:w-6 sm:h-6 hidden"></i>
                </button>
            </div>
        </div>

        <!-- Mobile Drawer -->
        <div id="mobile-menu" class="hidden lg:hidden border-t border-slate-200/80 bg-white/95 backdrop-blur-xl px-4 py-6 shadow-xl animate-in fade-in duration-200">
            <nav class="flex flex-col gap-2">
                <a href="#services" class="mobile-nav-link flex items-center justify-between px-4 py-3 rounded-xl text-base font-semibold text-slate-700 hover:text-emerald-700 hover:bg-emerald-50 transition-colors">
                    Services <i data-lucide="chevron-right" class="w-4 h-4 text-slate-400"></i>
                </a>
                <a href="#how-it-works" class="mobile-nav-link flex items-center justify-between px-4 py-3 rounded-xl text-base font-semibold text-slate-700 hover:text-emerald-700 hover:bg-emerald-50 transition-colors">
                    How It Works <i data-lucide="chevron-right" class="w-4 h-4 text-slate-400"></i>
                </a>
                <a href="#demographics" class="mobile-nav-link flex items-center justify-between px-4 py-3 rounded-xl text-base font-semibold text-slate-700 hover:text-emerald-700 hover:bg-emerald-50 transition-colors">
                    Demographics <i data-lucide="chevron-right" class="w-4 h-4 text-slate-400"></i>
                </a>
                <a href="#officials" class="mobile-nav-link flex items-center justify-between px-4 py-3 rounded-xl text-base font-semibold text-slate-700 hover:text-emerald-700 hover:bg-emerald-50 transition-colors">
                    Officials <i data-lucide="chevron-right" class="w-4 h-4 text-slate-400"></i>
                </a>
                <a href="#emergency" class="mobile-nav-link flex items-center justify-between px-4 py-3 rounded-xl text-base font-semibold text-slate-700 hover:text-emerald-700 hover:bg-emerald-50 transition-colors">
                    Hotlines <i data-lucide="chevron-right" class="w-4 h-4 text-slate-400"></i>
                </a>
            </nav>
            <div class="mt-6 pt-6 border-t border-slate-200 flex flex-col gap-3">
                @guest
                    <a href="{{ route('register') }}" class="w-full py-3 text-center text-sm font-semibold text-emerald-700 bg-emerald-50 hover:bg-emerald-100 rounded-xl transition-colors">Create Resident Account</a>
                @endguest
            </div>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="home-hero relative min-h-[85vh] flex items-center pt-10 pb-20 lg:pt-0 overflow-hidden bg-slate-900">
        <!-- Background Image with tuned dark overlay -->
        <div class="absolute inset-0 z-0">
            <div class="absolute inset-0 bg-gradient-to-r from-slate-900 via-slate-900/30 to-slate-900/70 z-10"></div>
            <img src="{{ asset('images/maxresdefault.jpg') }}" alt="Barangay Kay-Anlog Calamba Panoramic View" class="w-full h-full object-cover object-center opacity-55 mix-blend-overlay">
        </div>
        
        <div class="container mx-auto px-4 lg:px-8 relative z-20 flex flex-col lg:flex-row items-center gap-12 lg:gap-16 py-8">
            <!-- Left Copy -->
            <div class="w-full lg:w-1/2 text-white">
                <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-emerald-500/10 border border-emerald-400/25 backdrop-blur-md mb-6 sm:mb-8">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    <span class="text-xs font-bold tracking-widest text-emerald-300 uppercase">Transparent Local Governance</span>
                </div>
                
                <h2 class="text-4xl sm:text-5xl lg:text-6xl font-black tracking-tight leading-[1.1] mb-5">
                    Your Community.<br>
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-emerald-300 via-teal-200 to-teal-100">Just A Click Away.</span>
                </h2>
                
                <p class="text-base sm:text-lg text-white/90 mb-8 max-w-xl leading-relaxed font-normal drop-shadow-sm">
                    Welcome to the official portal of Barangay Kay-Anlog, Calamba City. Request barangay documents and file blotter reports through your resident account.
                </p>
                
                <!-- CTA Action Buttons -->
                <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3.5 mb-8">
                    @auth
                        @if (auth()->user()->role === 'staff')
                            <a href="{{ route('dashboard') }}" class="inline-flex justify-center items-center gap-2 px-7 py-3.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl transition-all shadow-lg shadow-emerald-900/30 hover:-translate-y-0.5">
                                <i data-lucide="layout-dashboard" class="w-5 h-5"></i>
                                Open staff workspace
                            </a>
                        @else
                            <a href="{{ route('account.requests.certificate.create') }}" class="inline-flex justify-center items-center gap-2 px-7 py-3.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl transition-all shadow-lg shadow-emerald-900/30 hover:-translate-y-0.5">
                                <i data-lucide="file-check-2" class="w-5 h-5"></i>
                                Request a document
                            </a>
                            <a href="{{ route('account') }}" class="inline-flex justify-center items-center gap-2 px-6 py-3.5 bg-white/15 hover:bg-white/25 backdrop-blur-md text-white border border-white/30 font-semibold rounded-xl transition-all shadow-lg shadow-black/20 hover:-translate-y-0.5">
                                <i data-lucide="user-round" class="w-5 h-5"></i>
                                Resident Portal
                            </a>
                        @endif
                    @else
                        <a href="{{ route('login') }}" class="inline-flex justify-center items-center gap-2 px-7 py-3.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl transition-all shadow-lg shadow-emerald-900/30 hover:-translate-y-0.5">
                            <i data-lucide="file-check-2" class="w-5 h-5"></i>
                            Request a document
                        </a>
                        <a href="{{ route('register') }}" class="inline-flex justify-center items-center gap-2 px-6 py-3.5 bg-white/15 hover:bg-white/25 backdrop-blur-md text-white border border-white/30 font-semibold rounded-xl transition-all hover:-translate-y-0.5 shadow-lg shadow-black/20">
                            <i data-lucide="user-round-plus" class="w-5 h-5"></i>
                            Create resident account
                        </a>
                    @endauth
                </div>

                <!-- Trust Micro-Badges -->
                <div class="flex flex-wrap items-center gap-4 sm:gap-6 text-xs text-slate-300 font-medium pt-4 border-t border-white/10">
                    <span class="inline-flex items-center gap-1.5"><i data-lucide="shield-check" class="w-4 h-4 text-emerald-400"></i> Official LGU Records</span>
                    <span class="inline-flex items-center gap-1.5"><i data-lucide="zap" class="w-4 h-4 text-teal-300"></i> 24-48h Processing</span>
                    <span class="inline-flex items-center gap-1.5"><i data-lucide="badge-check" class="w-4 h-4 text-sky-400"></i> Verified Citizens</span>
                </div>
            </div>

            <!-- Right Glassmorphic Stats Panel -->
            <div class="w-full lg:w-1/2 relative lg:pl-6" id="demographics">
                <div class="bg-slate-900/75 backdrop-blur-2xl border border-white/15 rounded-3xl p-6 sm:p-8 shadow-2xl relative overflow-hidden">
                    <!-- Ambient Glow -->
                    <div class="absolute -top-32 -right-32 w-64 h-64 bg-emerald-500/20 rounded-full blur-3xl pointer-events-none"></div>
                    
                    <!-- Card Topbar -->
                    <div class="flex items-center justify-between mb-7 border-b border-white/10 pb-5 relative z-10">
                        <div class="flex items-center gap-3">
                            <div class="w-11 h-11 rounded-2xl bg-gradient-to-br from-emerald-500 to-teal-700 flex items-center justify-center text-white shadow-lg shadow-emerald-900/40">
                                <i data-lucide="trending-up" class="w-5 h-5"></i>
                            </div>
                            <div>
                                <h3 class="text-xl sm:text-2xl font-bold text-white tracking-tight">Community At A Glance</h3>
                                <p class="text-xs text-slate-300 font-medium mt-0.5">Real-time local governance demographics</p>
                            </div>
                        </div>
                        <div class="hidden sm:inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-500/10 border border-emerald-400/25 whitespace-nowrap flex-shrink-0">
                            <span class="relative flex h-2 w-2 flex-shrink-0">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-400"></span>
                            </span>
                            <span class="text-[0.7rem] font-bold text-emerald-300 uppercase tracking-wider whitespace-nowrap">Live System</span>
                        </div>
                    </div>

                    <!-- Primary Stats Grid -->
                    <div class="grid grid-cols-2 gap-3.5 sm:gap-4 relative z-10 mb-6">
                        <!-- Residents -->
                        <div class="bg-white/[0.04] border border-white/10 rounded-2xl p-4 sm:p-5 hover:bg-white/[0.08] hover:border-emerald-500/30 transition-all duration-300 group">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-[0.7rem] font-bold text-slate-300 uppercase tracking-wider">Residents</span>
                                <div class="w-8 h-8 rounded-lg bg-emerald-500/15 text-emerald-400 flex items-center justify-center group-hover:scale-110 transition-transform">
                                    <i data-lucide="users-round" class="w-4 h-4"></i>
                                </div>
                            </div>
                            <span class="block text-2xl sm:text-3xl font-black text-white tracking-tight">{{ number_format($stats['residents']) }}</span>
                            <span class="text-[0.75rem] text-emerald-200/90 font-medium mt-0.5 block">Registered Population</span>
                        </div>

                        <!-- Households -->
                        <div class="bg-white/[0.04] border border-white/10 rounded-2xl p-4 sm:p-5 hover:bg-white/[0.08] hover:border-sky-500/30 transition-all duration-300 group">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-[0.7rem] font-bold text-slate-300 uppercase tracking-wider">Households</span>
                                <div class="w-8 h-8 rounded-lg bg-sky-500/15 text-sky-400 flex items-center justify-center group-hover:scale-110 transition-transform">
                                    <i data-lucide="house" class="w-4 h-4"></i>
                                </div>
                            </div>
                            <span class="block text-2xl sm:text-3xl font-black text-white tracking-tight">{{ number_format($stats['households']) }}</span>
                            <span class="text-[0.75rem] text-sky-200/90 font-medium mt-0.5 block">Mapped Families</span>
                        </div>

                        <!-- Certificates -->
                        <div class="bg-white/[0.04] border border-white/10 rounded-2xl p-4 sm:p-5 hover:bg-white/[0.08] hover:border-amber-500/30 transition-all duration-300 group">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-[0.7rem] font-bold text-slate-300 uppercase tracking-wider">Certificates</span>
                                <div class="w-8 h-8 rounded-lg bg-amber-500/15 text-amber-400 flex items-center justify-center group-hover:scale-110 transition-transform">
                                    <i data-lucide="file-check-2" class="w-4 h-4"></i>
                                </div>
                            </div>
                            <span class="block text-2xl sm:text-3xl font-black text-white tracking-tight">{{ number_format($stats['certificates']) }}</span>
                            <span class="text-[0.75rem] text-amber-200/90 font-medium mt-0.5 block">Official Documents</span>
                        </div>

                        <!-- Turnaround -->
                        <div class="bg-white/[0.04] border border-white/10 rounded-2xl p-4 sm:p-5 hover:bg-white/[0.08] hover:border-teal-500/30 transition-all duration-300 group">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-[0.7rem] font-bold text-slate-300 uppercase tracking-wider">Turnaround</span>
                                <div class="w-8 h-8 rounded-lg bg-teal-500/15 text-teal-400 flex items-center justify-center group-hover:scale-110 transition-transform">
                                    <i data-lucide="zap" class="w-4 h-4"></i>
                                </div>
                            </div>
                            <span class="block text-2xl sm:text-3xl font-black text-white tracking-tight">24h</span>
                            <span class="text-[0.75rem] text-teal-200/90 font-medium mt-0.5 block">Fast Processing</span>
                        </div>
                    </div>

                    <!-- Sub-Demographic Indicators -->
                    <div class="pt-4 border-t border-white/10 grid grid-cols-2 gap-3 text-xs text-slate-300 relative z-10">
                        <div class="flex items-center gap-2">
                            <i data-lucide="badge-check" class="w-4 h-4 text-emerald-400 flex-shrink-0"></i>
                            <span>Voters: <strong class="text-white">{{ number_format($stats['voters']) }}</strong></span>
                        </div>
                        <div class="flex items-center gap-2">
                            <i data-lucide="heart-pulse" class="w-4 h-4 text-emerald-400 flex-shrink-0"></i>
                            <span>Senior Citizens: <strong class="text-white">{{ number_format($stats['seniors']) }}</strong></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Bottom subtle curve divider -->
        <div class="absolute bottom-0 left-0 w-full h-12 bg-gradient-to-t from-slate-50 to-transparent z-10"></div>
    </section>

    <!-- How It Works Section -->
    <section id="how-it-works" class="py-16 bg-white border-b border-slate-200">
        <div class="container mx-auto px-4 lg:px-8">
            <div class="max-w-2xl mx-auto text-center mb-12">
                <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">How requests work</h2>
            </div>

            <div class="grid md:grid-cols-3 gap-8 relative">
                <!-- Step 1 -->
                <div class="flex flex-col items-center text-center p-6 rounded-2xl bg-slate-50 border border-slate-200/80 hover:bg-emerald-50/40 hover:border-emerald-200 transition-all duration-300">
                    <div class="w-12 h-12 rounded-2xl bg-emerald-600 text-white font-extrabold flex items-center justify-center text-lg mb-5 shadow-md shadow-emerald-900/10">
                        1
                    </div>
                    <h3 class="text-lg font-bold text-slate-900 mb-2">Choose Service</h3>
                    <p class="text-sm text-slate-600 leading-relaxed">Choose a document to request or submit an incident report.</p>
                </div>

                <!-- Step 2 -->
                <div class="flex flex-col items-center text-center p-6 rounded-2xl bg-slate-50 border border-slate-200/80 hover:bg-emerald-50/40 hover:border-emerald-200 transition-all duration-300">
                    <div class="w-12 h-12 rounded-2xl bg-emerald-600 text-white font-extrabold flex items-center justify-center text-lg mb-5 shadow-md shadow-emerald-900/10">
                        2
                    </div>
                    <h3 class="text-lg font-bold text-slate-900 mb-2">Submit Online</h3>
                    <p class="text-sm text-slate-600 leading-relaxed">Sign in to your verified resident account and send the required details.</p>
                </div>

                <!-- Step 3 -->
                <div class="flex flex-col items-center text-center p-6 rounded-2xl bg-slate-50 border border-slate-200/80 hover:bg-emerald-50/40 hover:border-emerald-200 transition-all duration-300">
                    <div class="w-12 h-12 rounded-2xl bg-emerald-600 text-white font-extrabold flex items-center justify-center text-lg mb-5 shadow-md shadow-emerald-900/10">
                        3
                    </div>
                    <h3 class="text-lg font-bold text-slate-900 mb-2">Track your request</h3>
                    <p class="text-sm text-slate-600 leading-relaxed">Check My requests for staff updates. If a document is issued, contact the barangay office about collection.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Services Section -->
    <section id="services" class="py-20 relative bg-slate-50">
        <div class="container mx-auto px-4 lg:px-8 relative z-10">
            <div class="max-w-2xl mx-auto text-center mb-14">
                <h2 class="text-3xl sm:text-4xl font-extrabold text-slate-900 tracking-tight mb-3">Quick Barangay Services</h2>
            </div>

            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6 lg:gap-8">
                <!-- Barangay Clearance -->
                <div class="group bg-white rounded-3xl p-7 sm:p-8 border border-slate-200/80 shadow-sm hover:shadow-xl hover:border-emerald-200 hover:-translate-y-1.5 transition-all duration-300 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between mb-6">
                            <div class="w-14 h-14 rounded-2xl bg-emerald-50 flex items-center justify-center border border-emerald-100 text-emerald-700 group-hover:bg-emerald-600 group-hover:text-white transition-colors duration-300 shadow-sm">
                                <i data-lucide="file-check-2" class="w-7 h-7"></i>
                            </div>
                            <span class="text-xs font-bold px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-800 border border-emerald-100">Official Clearance</span>
                        </div>
                        <h3 class="text-xl font-bold text-slate-900 mb-2.5 tracking-tight">Barangay Clearance</h3>
                        <p class="text-slate-600 text-sm leading-relaxed mb-6">Certifies good standing and lack of derogatory record for local employment, business permits, and postal IDs.</p>
                    </div>
                    <button class="inline-flex items-center justify-between text-emerald-700 font-bold hover:text-emerald-800 bg-emerald-50 hover:bg-emerald-100/80 px-4 py-3 rounded-xl w-full text-sm transition-colors" onclick="showServiceModal('Barangay Clearance', 'Certifies that the resident has no pending derogatory record within Barangay Kay-Anlog.', ['Valid Government-issued ID (with address)', 'Proof of Billing or Barangay Residence Record', 'Cedula / Community Tax Certificate (CTC)'], '24 to 48 Hours', 'Standard LGU fee applies (free for first-time jobseekers under RA 11261)')">
                        <span>View Requirements</span>
                        <i data-lucide="arrow-right" class="w-4 h-4 group-hover:translate-x-1 transition-transform"></i>
                    </button>
                </div>

                <!-- Certificate of Residency -->
                <div class="group bg-white rounded-3xl p-7 sm:p-8 border border-slate-200/80 shadow-sm hover:shadow-xl hover:border-emerald-200 hover:-translate-y-1.5 transition-all duration-300 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between mb-6">
                            <div class="w-14 h-14 rounded-2xl bg-sky-50 flex items-center justify-center border border-sky-100 text-sky-700 group-hover:bg-sky-600 group-hover:text-white transition-colors duration-300 shadow-sm">
                                <i data-lucide="house" class="w-7 h-7"></i>
                            </div>
                            <span class="text-xs font-bold px-2.5 py-1 rounded-full bg-sky-50 text-sky-800 border border-sky-100">Proof of Address</span>
                        </div>
                        <h3 class="text-xl font-bold text-slate-900 mb-2.5 tracking-tight">Certificate of Residency</h3>
                        <p class="text-slate-600 text-sm leading-relaxed mb-6">Validates bona fide residence for utility meter installation (Meralco/Water), bank account opening, and school enrollment.</p>
                    </div>
                    <button class="inline-flex items-center justify-between text-emerald-700 font-bold hover:text-emerald-800 bg-emerald-50 hover:bg-emerald-100/80 px-4 py-3 rounded-xl w-full text-sm transition-colors" onclick="showServiceModal('Certificate of Residency', 'Certifies bona fide living residence within the territory of Barangay Kay-Anlog.', ['1 Valid Government ID', 'Household Registration / Purok Leader Certification', 'Recent utility bill or lease agreement'], '24 Hours', 'Standard administrative document fee')">
                        <span>View Requirements</span>
                        <i data-lucide="arrow-right" class="w-4 h-4 group-hover:translate-x-1 transition-transform"></i>
                    </button>
                </div>

                <!-- Certificate of Indigency -->
                <div class="group bg-white rounded-3xl p-7 sm:p-8 border border-slate-200/80 shadow-sm hover:shadow-xl hover:border-amber-200 hover:-translate-y-1.5 transition-all duration-300 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between mb-6">
                            <div class="w-14 h-14 rounded-2xl bg-amber-50 flex items-center justify-center border border-amber-100 text-amber-700 group-hover:bg-amber-600 group-hover:text-white transition-colors duration-300 shadow-sm">
                                <i data-lucide="hand-heart" class="w-7 h-7"></i>
                            </div>
                            <span class="text-xs font-bold px-2.5 py-1 rounded-full bg-amber-50 text-amber-800 border border-amber-100">Free / Subsidized</span>
                        </div>
                        <h3 class="text-xl font-bold text-slate-900 mb-2.5 tracking-tight">Certificate of Indigency</h3>
                        <p class="text-slate-600 text-sm leading-relaxed mb-6">Supporting document for financial aid, educational scholarships, medical assistance (DSWD/Malasakit), and PAO legal aid.</p>
                    </div>
                    <button class="inline-flex items-center justify-between text-emerald-700 font-bold hover:text-emerald-800 bg-emerald-50 hover:bg-emerald-100/80 px-4 py-3 rounded-xl w-full text-sm transition-colors" onclick="showServiceModal('Certificate of Indigency', 'Special certification for low-income residents applying for government aid or legal assistance.', ['Valid ID of applicant or parent/guardian', 'Purok Chairman Endorsement or Case Assessment', 'Hospital bill, prescription, or school assessment (if medical/educational)'], 'Same Day to 24 Hours', 'FREE / 100% Fee-Exempt under National Guidelines')">
                        <span>View Requirements</span>
                        <i data-lucide="arrow-right" class="w-4 h-4 group-hover:translate-x-1 transition-transform"></i>
                    </button>
                </div>

                <!-- Peace & Order / Blotter Conciliation Banner -->
                <div class="md:col-span-2 lg:col-span-3 bg-slate-900 rounded-3xl p-7 sm:p-10 border border-slate-800 shadow-xl relative overflow-hidden flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
                    <div class="relative z-10 max-w-2xl">
                        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-500/10 border border-emerald-500/20 text-emerald-300 text-xs font-bold uppercase tracking-wider mb-3">
                            <i data-lucide="shield-check" class="w-3.5 h-3.5"></i> Lupon Tagapamayapa &middot; RA 7160
                        </div>
                        <h3 class="text-2xl sm:text-3xl font-extrabold text-white tracking-tight mb-2">Blotter &amp; Incident Reporting</h3>
                        <p class="text-slate-300 text-sm sm:text-base leading-relaxed">Confidential documentation and mediation of neighborhood disputes, property matters, and peace and order concerns handled through community conciliation.</p>
                    </div>
                    <div class="relative z-10 flex-shrink-0">
                        <button class="inline-flex justify-center items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold px-6 py-3.5 rounded-xl transition-all shadow-md shadow-emerald-900/40 w-full sm:w-auto" onclick="showServiceModal('Blotter &amp; Incident Reporting', 'Formal documentation of incident reports and mediation under the Katarungang Pambarangay.', ['Personal appearance of Complainant/Incident Reporter', 'Valid Government ID', 'Narrative description of incident and involved parties'], 'Formal Hearing Schedule Assigned', 'Strictly confidential and handled by the Barangay Lupon')">
                            <span>View Mediation Guidelines</span>
                            <i data-lucide="arrow-right" class="w-4 h-4"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Officials Section -->
    <section id="officials" class="py-20 bg-white border-t border-slate-200">
        <div class="container mx-auto px-4 lg:px-8">
            <div class="text-center max-w-2xl mx-auto mb-14">
                <span class="text-xs font-bold tracking-widest text-emerald-600 uppercase mb-2.5 block">Elected Leadership</span>
                <h2 class="text-3xl sm:text-4xl font-extrabold text-slate-900 tracking-tight mb-3">Barangay Officials Roster</h2>
                <p class="text-base text-slate-600">Meet the dedicated leaders serving Barangay Kay-Anlog with integrity and transparent governance.</p>
            </div>

            @if($officials->isEmpty())
                <div class="text-center py-16 bg-slate-50 rounded-3xl border-2 border-slate-200 border-dashed max-w-md mx-auto">
                    <i data-lucide="users-round" class="w-12 h-12 text-slate-300 mx-auto mb-3"></i>
                    <p class="text-base text-slate-500 font-medium">No official records currently listed.</p>
                </div>
            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    @foreach($officials as $official)
                    @php
                        $isLeader = str_contains(strtolower($official->position), 'captain') || str_contains(strtolower($official->position), 'punong');
                        $initials = collect(explode(' ', $official->name))->map(fn($part) => substr($part, 0, 1))->take(2)->implode('');
                    @endphp
                    <div class="group bg-white rounded-2xl border {{ $isLeader ? 'border-emerald-300 ring-2 ring-emerald-500/10 shadow-md' : 'border-slate-200' }} text-center hover:shadow-xl hover:border-emerald-300 transition-all duration-300 relative overflow-hidden flex flex-col h-full">
                        <!-- Top Banner Accent -->
                        <div class="h-20 w-full relative border-b border-slate-100 {{ $isLeader ? 'bg-gradient-to-r from-emerald-100 to-teal-100' : 'bg-slate-50' }}">
                            @if($isLeader)
                                <span class="absolute top-2 right-2 text-[0.65rem] font-bold uppercase tracking-wider bg-emerald-700 text-white px-2 py-0.5 rounded-full">Punong Barangay</span>
                            @endif
                        </div>
                        
                        <div class="px-5 pb-6 -mt-12 flex-grow flex flex-col items-center relative z-10">
                            <!-- Avatar / Initials -->
                            <div class="w-28 h-28 mx-auto rounded-full bg-white border-4 border-white shadow-md flex items-center justify-center overflow-hidden mb-3.5 group-hover:scale-105 transition-transform duration-300">
                                @if(!empty($official->image_path))
                                    <img src="{{ asset('storage/' . $official->image_path) }}" alt="{{ $official->name }}" class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full {{ $isLeader ? 'bg-emerald-600 text-white' : 'bg-slate-100 text-emerald-800' }} flex items-center justify-center font-bold text-2xl">
                                        {{ $initials ?: 'BO' }}
                                    </div>
                                @endif
                            </div>
                            
                            <h3 class="text-base font-extrabold text-slate-900 leading-snug mb-1">{{ $official->name }}</h3>
                            <span class="inline-block px-2.5 py-0.5 {{ $isLeader ? 'bg-emerald-100 text-emerald-900' : 'bg-slate-100 text-slate-700' }} text-[0.65rem] font-bold tracking-wider uppercase rounded-md mb-4">{{ $official->position }}</span>
                            
                            @if($official->contact_number)
                            <div class="mt-auto pt-3 w-full border-t border-slate-100">
                                <a href="tel:{{ preg_replace('/[^0-9+]/', '', $official->contact_number) }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-600 hover:text-emerald-700 transition-colors bg-slate-50 hover:bg-emerald-50 px-3 py-1.5 rounded-full w-full justify-center">
                                    <i data-lucide="phone" class="w-3 h-3 text-emerald-600"></i>
                                    <span>{{ $official->contact_number }}</span>
                                </a>
                            </div>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            @endif
        </div>
    </section>

    <!-- Emergency Hotlines Section -->
    <section id="emergency" class="py-20 bg-primary-active text-white relative overflow-hidden">
        <!-- Abstract subtle red/amber glow for emergency feel -->
        <div class="absolute -top-40 -right-40 w-96 h-96 bg-red-500/10 rounded-full blur-[100px] pointer-events-none"></div>
        <div class="absolute -bottom-40 -left-40 w-96 h-96 bg-emerald-500/10 rounded-full blur-[100px] pointer-events-none"></div>
        
        <div class="container mx-auto px-4 lg:px-8 relative z-10">
            <div class="max-w-2xl mb-12">
                <span class="text-xs font-bold tracking-widest text-amber-400 uppercase mb-2.5 flex items-center gap-2">
                    <i data-lucide="megaphone" class="w-3.5 h-3.5"></i> 24/7 First Responders &amp; Dispatch
                </span>
                <h2 class="text-3xl sm:text-4xl font-extrabold text-white tracking-tight mb-3">Emergency Hotlines</h2>
                <p class="text-base text-emerald-100/80 leading-relaxed">Direct lines for immediate medical rescue, fire dispatch, peace and order, and local security assistance.</p>
            </div>

            <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-5">
                <!-- Barangay Hall -->
                <div class="bg-white/5 border border-white/10 rounded-2xl p-6 backdrop-blur-md hover:bg-white/10 transition-all duration-300 flex flex-col justify-between">
                    <div>
                        <div class="w-12 h-12 rounded-xl bg-white/10 flex items-center justify-center mb-5 text-amber-400">
                            <i data-lucide="building-2" class="w-6 h-6"></i>
                        </div>
                        <h3 class="text-lg font-bold mb-1">Barangay Hall</h3>
                        <p class="text-sm text-emerald-100/60 leading-relaxed mb-4">Executive desk &amp; community patrols.</p>
                    </div>
                    <div>
                        <p class="text-2xl font-black text-white tracking-tight mb-3">(02) 8123-4567</p>
                        <a href="tel:0281234567" class="inline-flex items-center justify-center gap-2 w-full py-2.5 px-4 bg-white/10 hover:bg-amber-500 hover:text-slate-900 text-xs font-bold rounded-xl transition-all">
                            <i data-lucide="phone" class="w-3.5 h-3.5"></i> Call Action Desk
                        </a>
                    </div>
                </div>
                
                <!-- Police Station -->
                <div class="bg-white/5 border border-white/10 rounded-2xl p-6 backdrop-blur-md hover:bg-white/10 transition-all duration-300 flex flex-col justify-between">
                    <div>
                        <div class="w-12 h-12 rounded-xl bg-blue-500/20 flex items-center justify-center mb-5 text-blue-400">
                            <i data-lucide="badge-check" class="w-6 h-6"></i>
                        </div>
                        <h3 class="text-lg font-bold mb-1">Police Station</h3>
                        <p class="text-sm text-emerald-100/60 leading-relaxed mb-4">Calamba City Police 911 dispatch.</p>
                    </div>
                    <div>
                        <p class="text-2xl font-black text-white tracking-tight mb-3">911</p>
                        <a href="tel:911" class="inline-flex items-center justify-center gap-2 w-full py-2.5 px-4 bg-blue-500/20 hover:bg-blue-500 hover:text-white text-xs font-bold rounded-xl transition-all">
                            <i data-lucide="phone" class="w-3.5 h-3.5"></i> Call Emergency 911
                        </a>
                    </div>
                </div>
                
                <!-- Bureau of Fire -->
                <div class="bg-white/5 border border-white/10 rounded-2xl p-6 backdrop-blur-md hover:bg-white/10 transition-all duration-300 flex flex-col justify-between">
                    <div>
                        <div class="w-12 h-12 rounded-2xl bg-red-500/20 flex items-center justify-center mb-5 text-red-400">
                            <i data-lucide="bell" class="w-6 h-6"></i>
                        </div>
                        <h3 class="text-lg font-bold mb-1">Bureau of Fire (BFP)</h3>
                        <p class="text-sm text-emerald-100/60 leading-relaxed mb-4">Calamba Fire Station responders.</p>
                    </div>
                    <div>
                        <p class="text-2xl font-black text-white tracking-tight mb-3">160</p>
                        <a href="tel:160" class="inline-flex items-center justify-center gap-2 w-full py-2.5 px-4 bg-red-500/20 hover:bg-red-500 hover:text-white text-xs font-bold rounded-xl transition-all">
                            <i data-lucide="phone" class="w-3.5 h-3.5"></i> Call BFP Fire Desk
                        </a>
                    </div>
                </div>
                
                <!-- Health Center -->
                <div class="bg-white/5 border border-white/10 rounded-2xl p-6 backdrop-blur-md hover:bg-white/10 transition-all duration-300 flex flex-col justify-between">
                    <div>
                        <div class="w-12 h-12 rounded-xl bg-emerald-500/20 flex items-center justify-center mb-5 text-emerald-400">
                            <i data-lucide="heart-pulse" class="w-6 h-6"></i>
                        </div>
                        <h3 class="text-lg font-bold mb-1">Health Center</h3>
                        <p class="text-sm text-emerald-100/60 leading-relaxed mb-4">24/7 medical rescue &amp; ambulance.</p>
                    </div>
                    <div>
                        <p class="text-2xl font-black text-white tracking-tight mb-3">0917-123-4567</p>
                        <a href="tel:09171234567" class="inline-flex items-center justify-center gap-2 w-full py-2.5 px-4 bg-emerald-500/20 hover:bg-emerald-500 hover:text-white text-xs font-bold rounded-xl transition-all">
                            <i data-lucide="phone" class="w-3.5 h-3.5"></i> Call Health Rescue
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Official Civic Footer -->
    <footer class="bg-white border-t border-slate-200 pt-12 pb-8">
        <div class="container mx-auto px-4 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8 pb-10 border-b border-slate-100">
                <!-- Col 1: Brand Info -->
                <div class="md:col-span-2">
                    <a href="{{ route('home') }}" class="flex items-center gap-3 mb-4">
                        <img src="{{ asset('images/Logo_kay-anlog.jpg') }}" alt="" aria-hidden="true" width="40" height="40" loading="lazy" class="w-10 h-10 rounded-full object-cover bg-white border border-slate-200 shadow-sm flex-shrink-0">
                        <div>
                            <span class="font-extrabold text-slate-900 block leading-tight">Barangay Kay-Anlog</span>
                            <span class="text-xs text-emerald-600 font-semibold tracking-wider uppercase">Calamba City, Laguna</span>
                        </div>
                    </a>
                    <p class="text-slate-600 text-sm leading-relaxed max-w-sm mb-4">
                        Official civic information and citizen e-services portal. Promoting transparent, digital-first governance for all residents.
                    </p>
                    <div class="flex items-center gap-2 text-xs text-slate-500">
                        <i data-lucide="map-pin" class="w-4 h-4 text-emerald-600 flex-shrink-0"></i>
                        <span>Barangay Hall, Kay-Anlog, Calamba City, Laguna 4027</span>
                    </div>
                </div>

                <!-- Col 2: Citizen E-Services -->
                <div>
                    <h4 class="text-xs font-bold text-slate-900 uppercase tracking-widest mb-4">Citizen Services</h4>
                    <ul class="space-y-2 text-sm text-slate-600">
                        <li><a href="#services" class="hover:text-emerald-700 transition-colors">Barangay Clearance</a></li>
                        <li><a href="#services" class="hover:text-emerald-700 transition-colors">Certificate of Residency</a></li>
                        <li><a href="#services" class="hover:text-emerald-700 transition-colors">Certificate of Indigency</a></li>
                        <li><a href="#services" class="hover:text-emerald-700 transition-colors">Blotter &amp; Conciliation</a></li>
                    </ul>
                </div>

                <!-- Col 3: Portal Access -->
                <div>
                    <h4 class="text-xs font-bold text-slate-900 uppercase tracking-widest mb-4">Quick Links</h4>
                    <ul class="space-y-2 text-sm text-slate-600">
                        @auth
                            <li><a href="{{ auth()->user()->role === 'staff' ? route('dashboard') : route('account') }}" class="hover:text-emerald-700 transition-colors font-semibold text-emerald-700">Go to Dashboard</a></li>
                        @else
                            <li><a href="{{ route('login') }}" class="hover:text-emerald-700 transition-colors">Staff &amp; Resident Sign In</a></li>
                            <li><a href="{{ route('register') }}" class="hover:text-emerald-700 transition-colors">Create resident account</a></li>
                        @endauth
                        <li><a href="#officials" class="hover:text-emerald-700 transition-colors">Barangay Officials Roster</a></li>
                        <li><a href="#emergency" class="hover:text-emerald-700 transition-colors">24/7 Emergency Hotlines</a></li>
                    </ul>
                </div>
            </div>

            <!-- Bottom Row -->
            <div class="pt-6 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-slate-500">
                <p>&copy; {{ date('Y') }} Barangay Kay-Anlog, City of Calamba. All rights reserved.</p>
                <div class="flex items-center gap-4">
                    <a href="#top" class="inline-flex items-center gap-1.5 hover:text-emerald-700 font-semibold transition-colors">
                        <span>Back to top</span>
                        <i data-lucide="arrow-up" class="w-3.5 h-3.5"></i>
                    </a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Comprehensive Service Requirement Modal -->
    <dialog id="service-dialog" class="w-full max-w-lg bg-white p-0 rounded-3xl overflow-hidden shadow-2xl backdrop:bg-slate-900/60 backdrop:backdrop-blur-sm border border-slate-200">
        <div class="p-6 sm:p-8">
            <div class="flex items-start justify-between mb-5">
                <div class="w-11 h-11 rounded-xl bg-emerald-50 flex items-center justify-center text-emerald-600 border border-emerald-100 shadow-sm">
                    <i data-lucide="info" class="w-5 h-5"></i>
                </div>
                <button type="button" onclick="document.getElementById('service-dialog').close()" class="p-2 text-slate-400 hover:text-slate-700 hover:bg-slate-100 rounded-full transition-colors" aria-label="Close modal">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>
            
            <h3 id="modal-title" class="text-2xl font-extrabold text-slate-900 mb-2 tracking-tight"></h3>
            <p id="modal-description" class="text-slate-600 text-sm leading-relaxed mb-5"></p>
            
            <!-- Requirements List Box -->
            <div class="bg-slate-50 rounded-2xl p-4 sm:p-5 border border-slate-200/80 mb-5">
                <h4 class="text-xs font-bold text-slate-900 uppercase tracking-wider mb-3 flex items-center gap-1.5">
                    <i data-lucide="clipboard-list" class="w-4 h-4 text-emerald-600"></i>
                    Required Documents / Checklist
                </h4>
                <ul id="modal-checklist" class="space-y-2 text-xs sm:text-sm text-slate-700"></ul>
            </div>

            <!-- Meta Badges -->
            <div class="grid grid-cols-2 gap-3 mb-6 text-xs">
                <div class="bg-emerald-50/70 border border-emerald-100 p-3 rounded-xl">
                    <span class="text-slate-500 font-medium block">Processing Time:</span>
                    <strong id="modal-turnaround" class="text-emerald-800 font-bold"></strong>
                </div>
                <div class="bg-slate-100 p-3 rounded-xl border border-slate-200/70">
                    <span class="text-slate-500 font-medium block">Fee Guidelines:</span>
                    <strong id="modal-fees" class="text-slate-800 font-bold"></strong>
                </div>
            </div>
            
            <div class="flex flex-col-reverse sm:flex-row justify-end gap-3 pt-5 border-t border-slate-100">
                <button type="button" onclick="document.getElementById('service-dialog').close()" class="w-full sm:w-auto px-5 py-2.5 text-sm font-semibold text-slate-600 hover:bg-slate-100 rounded-xl transition-colors">
                    Close
                </button>
                @auth
                    <a href="{{ route('account.requests.certificate.create') }}" class="w-full sm:w-auto inline-flex justify-center items-center gap-2 px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-bold rounded-xl transition-colors shadow-md shadow-emerald-900/15">
                        Request Online <i data-lucide="arrow-right" class="w-4 h-4"></i>
                    </a>
                @else
                    <a href="{{ route('login') }}" class="w-full sm:w-auto inline-flex justify-center items-center gap-2 px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-bold rounded-xl transition-colors shadow-md shadow-emerald-900/15">
                        Proceed to Request <i data-lucide="arrow-right" class="w-4 h-4"></i>
                    </a>
                @endauth
            </div>
        </div>
    </dialog>

    <!-- Mobile Drawer & Modal Scripts -->
    <script>
        // Mobile Navigation Toggle
        const menuBtn = document.getElementById('mobile-menu-btn');
        const mobileMenu = document.getElementById('mobile-menu');
        const menuIcon = document.getElementById('menu-icon');
        const closeIcon = document.getElementById('close-icon');

        if (menuBtn && mobileMenu) {
            menuBtn.addEventListener('click', () => {
                const isOpen = !mobileMenu.classList.contains('hidden');
                mobileMenu.classList.toggle('hidden', isOpen);
                menuIcon.classList.toggle('hidden', !isOpen);
                closeIcon.classList.toggle('hidden', isOpen);
                menuBtn.setAttribute('aria-expanded', String(!isOpen));
            });

            // Close on nav link click
            document.querySelectorAll('.mobile-nav-link').forEach(link => {
                link.addEventListener('click', () => {
                    mobileMenu.classList.add('hidden');
                    menuIcon.classList.remove('hidden');
                    closeIcon.classList.add('hidden');
                    menuBtn.setAttribute('aria-expanded', 'false');
                });
            });
        }

        // Service Details Modal Function
        function showServiceModal(title, description, checklist, turnaround, fees) {
            document.getElementById('modal-title').textContent = title;
            document.getElementById('modal-description').textContent = description;
            
            const listEl = document.getElementById('modal-checklist');
            listEl.innerHTML = '';
            checklist.forEach(item => {
                const li = document.createElement('li');
                li.className = 'flex items-start gap-2';
                li.innerHTML = '<span class="text-emerald-600 font-bold">&check;</span><span>' + item + '</span>';
                listEl.appendChild(li);
            });

            document.getElementById('modal-turnaround').textContent = turnaround;
            document.getElementById('modal-fees').textContent = fees;

            document.getElementById('service-dialog').showModal();
        }
    </script>
<x-chatbot-widget />
</body>
</html>
