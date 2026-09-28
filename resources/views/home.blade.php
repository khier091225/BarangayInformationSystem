<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#064e3b">
    <title>Barangay Kay-Anlog | Information &amp; E-Services Portal</title>
    @fonts
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        .glass-panel {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border-bottom: 1px solid rgba(226, 232, 240, 0.8);
        }
        dialog::backdrop {
            background: rgba(15, 23, 42, 0.6);
            backdrop-filter: blur(4px);
            -webkit-backdrop-filter: blur(4px);
        }
        dialog {
            margin: auto;
        }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 font-sans antialiased selection:bg-emerald-200 selection:text-emerald-900">
    <!-- Utility Bar -->
    <div class="bg-[#064e3b] text-emerald-50 py-2.5 border-b border-emerald-900 relative z-50">
        <div class="container mx-auto px-4 lg:px-8 flex flex-wrap justify-between items-center gap-2 text-[0.8125rem]">
            <div class="flex items-center gap-2">
                <i data-lucide="flag" class="w-3.5 h-3.5 text-amber-400"></i>
                <span class="tracking-wide">Republic of the Philippines &middot; Province of Laguna &middot; City of Calamba &middot; <strong class="font-semibold text-white">Barangay Kay-Anlog</strong></span>
            </div>
            <div class="flex items-center gap-2 font-medium">
                <i data-lucide="clock" class="w-3.5 h-3.5 text-emerald-300"></i>
                <span>Office Hours: Mon-Fri, 8:00 AM &ndash; 5:00 PM</span>
            </div>
        </div>
    </div>

    <!-- Header -->
    <header class="sticky top-0 z-40 glass-panel shadow-sm">
        <div class="container mx-auto px-4 lg:px-8 h-20 flex items-center justify-between">
            <a href="{{ route('home') }}" class="flex items-center gap-3 group">
                <div class="w-12 h-12 rounded-xl bg-emerald-50 flex items-center justify-center border border-emerald-100 group-hover:bg-emerald-100 transition-colors">
                    <i data-lucide="landmark" class="w-6 h-6 text-emerald-700"></i>
                </div>
                <div>
                    <h1 class="text-xl font-extrabold text-slate-900 tracking-tight">Barangay Kay-Anlog</h1>
                    <span class="text-[0.65rem] font-bold text-emerald-600 tracking-[0.15em] uppercase block mt-0.5">Information &amp; E-Services Portal</span>
                </div>
            </a>
            
            <nav class="hidden lg:flex items-center gap-8 text-sm font-semibold text-slate-600">
                <a href="#services" class="hover:text-emerald-700 transition-colors">Services</a>
                <a href="#demographics" class="hover:text-emerald-700 transition-colors">Demographics</a>
                <a href="#officials" class="hover:text-emerald-700 transition-colors">Officials</a>
                <a href="#emergency" class="hover:text-emerald-700 transition-colors">Emergency</a>
            </nav>

            <div class="flex items-center gap-3">
                @auth
                    @if (auth()->user()->role === 'staff')
                        <a href="{{ route('dashboard') }}" class="hidden sm:inline-flex items-center gap-2 px-5 py-2.5 bg-emerald-700 hover:bg-emerald-800 text-white text-sm font-semibold rounded-lg transition-all shadow-md shadow-emerald-900/20">
                            <i data-lucide="layout-dashboard" class="w-4 h-4"></i>
                            Staff Dashboard
                        </a>
                    @else
                        <a href="{{ route('account') }}" class="hidden sm:inline-flex items-center gap-2 px-5 py-2.5 bg-emerald-700 hover:bg-emerald-800 text-white text-sm font-semibold rounded-lg transition-all shadow-md shadow-emerald-900/20">
                            <i data-lucide="user-round" class="w-4 h-4"></i>
                            Resident Portal
                        </a>
                    @endif
                @else
                    <a href="{{ route('register') }}" class="hidden sm:block px-4 py-2 text-sm font-semibold text-slate-600 hover:text-emerald-700 transition-colors">Register</a>
                    <a href="{{ route('login') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-emerald-700 hover:bg-emerald-800 text-white text-sm font-semibold rounded-lg transition-all shadow-md shadow-emerald-900/20">
                        <i data-lucide="log-in" class="w-4 h-4"></i>
                        Sign In
                    </a>
                @endauth
            </div>
        </div>
    </header>

    <!-- Hero -->
    <section class="relative min-h-[85vh] flex items-center pt-10 pb-24 lg:pt-0 overflow-hidden bg-slate-900">
        <!-- Background Image with beautiful overlay -->
        <div class="absolute inset-0 z-0">
            <div class="absolute inset-0 bg-gradient-to-r from-slate-900 via-slate-900/50 to-slate-900/52 z-10"></div>
            <!-- Background Image -->
            <img src="{{ asset('images/maxresdefault.jpg') }}" alt="Barangay Kay-Anlog Background" class="w-full h-full object-cover object-center opacity-55 mix-blend-overlay">
        </div>
        
        <div class="container mx-auto px-4 lg:px-8 relative z-20 flex flex-col lg:flex-row items-center gap-16">
            <!-- Left Copy -->
            <div class="w-full lg:w-1/2 text-white">
                <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-emerald-500/10 border border-emerald-400/20 backdrop-blur-md mb-8">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    <span class="text-xs font-bold tracking-widest text-emerald-50 uppercase">Transparent Local Governance</span>
                </div>
                <h2 class="text-5xl lg:text-7xl font-extrabold tracking-tighter leading-[1.05] mb-6">
                    Your Community.<br>
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-emerald-200 to-teal-100">Just A Click Away.</span>
                </h2>
                <p class="text-lg text-white/90 mb-10 max-w-xl leading-relaxed font-normal drop-shadow-sm">
                    Welcome to the official digital portal of Barangay Kay-Anlog, Calamba City. Request certificates, file incident reports, inspect official records, and stay connected online.
                </p>
                <div class="flex flex-wrap gap-4">
                    @auth
                        @if (auth()->user()->role === 'staff')
                            <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 px-7 py-4 bg-emerald-600 hover:bg-emerald-500 text-white font-semibold rounded-xl transition-all shadow-lg shadow-emerald-900/30">
                                <i data-lucide="layout-dashboard" class="w-5 h-5"></i>
                                Open Staff Dashboard
                            </a>
                        @else
                            <a href="{{ route('account.requests.certificate.create') }}" class="inline-flex items-center gap-2 px-7 py-4 bg-emerald-600 hover:bg-emerald-500 text-white font-semibold rounded-xl transition-all shadow-lg shadow-emerald-900/30">
                                <i data-lucide="file-check-2" class="w-5 h-5"></i>
                                Request Document Online
                            </a>
                            <a href="{{ route('account') }}" class="inline-flex items-center gap-2 px-7 py-4 bg-white/15 hover:bg-white/25 backdrop-blur-md text-white border border-white/30 font-semibold rounded-xl transition-all shadow-lg shadow-black/20">
                                <i data-lucide="user-round" class="w-5 h-5"></i>
                                Go to My Account
                            </a>
                        @endif
                    @else
                        <a href="{{ route('login') }}" class="inline-flex items-center gap-2 px-7 py-4 bg-emerald-600 hover:bg-emerald-500 text-white font-semibold rounded-xl transition-all shadow-lg shadow-emerald-900/30 hover:-translate-y-0.5">
                            <i data-lucide="file-check-2" class="w-5 h-5"></i>
                            Request Certificate
                        </a>
                        <a href="{{ route('register') }}" class="inline-flex items-center gap-2 px-7 py-4 bg-white/15 hover:bg-white/25 backdrop-blur-md text-white border border-white/30 font-semibold rounded-xl transition-all hover:-translate-y-0.5 shadow-lg shadow-black/20">
                            <i data-lucide="user-round-plus" class="w-5 h-5"></i>
                            Resident Registration
                        </a>
                    @endauth
                </div>
            </div>

            <!-- Right Glassmorphic Stats Panel -->
            <div class="w-full lg:w-1/2 relative lg:pl-12 mt-12 lg:mt-0" id="demographics">
                <div class="bg-slate-900/70 backdrop-blur-2xl border border-white/15 rounded-3xl p-7 lg:p-9 shadow-2xl relative overflow-hidden">
                    <!-- Ambient Glow -->
                    <div class="absolute -top-32 -right-32 w-64 h-64 bg-emerald-500/20 rounded-full blur-3xl pointer-events-none"></div>
                    
                    <div class="flex items-center justify-between mb-8 border-b border-white/10 pb-5 relative z-10">
                        <div class="flex items-center gap-3.5">
                            <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-emerald-500 to-teal-700 flex items-center justify-center text-white shadow-lg shadow-emerald-900/40">
                                <i data-lucide="trending-up" class="w-6 h-6"></i>
                            </div>
                            <div>
                                <h3 class="text-2xl font-bold text-white tracking-tight">Community At A Glance</h3>
                                <p class="text-xs text-slate-300 font-medium mt-0.5">Real-time local governance data</p>
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

                    <div class="grid grid-cols-2 gap-4 lg:gap-5 relative z-10">
                        <!-- Residents -->
                        <div class="bg-white/[0.04] border border-white/10 rounded-2xl p-5 hover:bg-white/[0.08] hover:border-emerald-500/30 hover:-translate-y-1 transition-all duration-300 group">
                            <div class="flex items-center justify-between mb-3">
                                <span class="text-xs font-semibold text-slate-300 uppercase tracking-wider">Residents</span>
                                <div class="w-9 h-9 rounded-xl bg-emerald-500/15 text-emerald-400 flex items-center justify-center group-hover:scale-110 transition-transform">
                                    <i data-lucide="users-round" class="w-4 h-4"></i>
                                </div>
                            </div>
                            <span class="block text-3xl lg:text-4xl font-black text-white tracking-tight">{{ number_format($stats['residents']) }}</span>
                            <span class="text-xs text-emerald-200/90 font-medium mt-1 block">Registered &amp; Verified</span>
                        </div>

                        <!-- Households -->
                        <div class="bg-white/[0.04] border border-white/10 rounded-2xl p-5 hover:bg-white/[0.08] hover:border-sky-500/30 hover:-translate-y-1 transition-all duration-300 group">
                            <div class="flex items-center justify-between mb-3">
                                <span class="text-xs font-semibold text-slate-300 uppercase tracking-wider">Households</span>
                                <div class="w-9 h-9 rounded-xl bg-sky-500/15 text-sky-400 flex items-center justify-center group-hover:scale-110 transition-transform">
                                    <i data-lucide="house" class="w-4 h-4"></i>
                                </div>
                            </div>
                            <span class="block text-3xl lg:text-4xl font-black text-white tracking-tight">{{ number_format($stats['households']) }}</span>
                            <span class="text-xs text-sky-200/90 font-medium mt-1 block">Mapped Families</span>
                        </div>

                        <!-- Certificates -->
                        <div class="bg-white/[0.04] border border-white/10 rounded-2xl p-5 hover:bg-white/[0.08] hover:border-amber-500/30 hover:-translate-y-1 transition-all duration-300 group">
                            <div class="flex items-center justify-between mb-3">
                                <span class="text-xs font-semibold text-slate-300 uppercase tracking-wider">Certificates</span>
                                <div class="w-9 h-9 rounded-xl bg-amber-500/15 text-amber-400 flex items-center justify-center group-hover:scale-110 transition-transform">
                                    <i data-lucide="file-check-2" class="w-4 h-4"></i>
                                </div>
                            </div>
                            <span class="block text-3xl lg:text-4xl font-black text-white tracking-tight">{{ number_format($stats['certificates']) }}</span>
                            <span class="text-xs text-amber-200/90 font-medium mt-1 block">Issued &amp; Certified</span>
                        </div>

                        <!-- Turnaround -->
                        <div class="bg-white/[0.04] border border-white/10 rounded-2xl p-5 hover:bg-white/[0.08] hover:border-teal-500/30 hover:-translate-y-1 transition-all duration-300 group">
                            <div class="flex items-center justify-between mb-3">
                                <span class="text-xs font-semibold text-slate-300 uppercase tracking-wider">Processing</span>
                                <div class="w-9 h-9 rounded-xl bg-teal-500/15 text-teal-400 flex items-center justify-center group-hover:scale-110 transition-transform">
                                    <i data-lucide="zap" class="w-4 h-4"></i>
                                </div>
                            </div>
                            <span class="block text-3xl lg:text-4xl font-black text-white tracking-tight">24h</span>
                            <span class="text-xs text-teal-200/90 font-medium mt-1 block">Fast E-Service</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Bottom gradient fade -->
        <div class="absolute bottom-0 left-0 w-full h-24 bg-gradient-to-t from-slate-50 to-transparent z-10"></div>
    </section>

    <!-- Services -->
    <section id="services" class="py-24 relative bg-slate-50">
        <div class="container mx-auto px-4 lg:px-8 relative z-10">
            <div class="max-w-2xl mx-auto text-center mb-16">
                <span class="text-sm font-bold tracking-widest text-emerald-600 uppercase mb-3 block">Citizen Services</span>
                <h2 class="text-4xl lg:text-5xl font-extrabold text-slate-900 tracking-tight mb-5">Quick Barangay Services</h2>
                <p class="text-lg text-slate-600">Select any service card to view requirements, processing guidelines, or file an online request instantly.</p>
            </div>

            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Barangay Clearance -->
                <div class="group bg-white rounded-3xl p-8 border border-slate-200 shadow-sm hover:shadow-2xl hover:-translate-y-2 transition-all duration-300 relative overflow-hidden flex flex-col">
                    <div class="absolute -right-8 -top-8 opacity-5 group-hover:opacity-10 transition-opacity transform group-hover:scale-110 duration-500 pointer-events-none">
                        <i data-lucide="file-check-2" class="w-48 h-48 text-emerald-900"></i>
                    </div>
                    <div class="w-16 h-16 rounded-2xl bg-emerald-50 flex items-center justify-center border border-emerald-100 text-emerald-700 mb-8 group-hover:bg-emerald-600 group-hover:text-white transition-colors duration-300">
                        <i data-lucide="file-check-2" class="w-8 h-8"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-slate-900 mb-3 tracking-tight relative z-10">Barangay Clearance</h3>
                    <p class="text-slate-600 mb-8 leading-relaxed flex-grow relative z-10">Required for local employment, business permits, postal ID, and general official transactions.</p>
                    <button class="relative z-10 inline-flex items-center gap-2 text-emerald-700 font-bold group-hover:text-emerald-800 bg-emerald-50 px-5 py-3 rounded-xl w-max transition-colors" onclick="showDialog('Barangay Clearance', 'Official clearance certifying that the resident has no derogatory records within Barangay Kay-Anlog. Requirements: 1 valid government ID, proof of residence, and online control number. Processing time: 24 to 48 hours.')">
                        View Requirements <i data-lucide="arrow-right" class="w-4 h-4 group-hover:translate-x-1 transition-transform"></i>
                    </button>
                </div>

                <!-- Certificate of Residency -->
                <div class="group bg-white rounded-3xl p-8 border border-slate-200 shadow-sm hover:shadow-2xl hover:-translate-y-2 transition-all duration-300 relative overflow-hidden flex flex-col">
                    <div class="absolute -right-8 -top-8 opacity-5 group-hover:opacity-10 transition-opacity transform group-hover:scale-110 duration-500 pointer-events-none">
                        <i data-lucide="house" class="w-48 h-48 text-emerald-900"></i>
                    </div>
                    <div class="w-16 h-16 rounded-2xl bg-emerald-50 flex items-center justify-center border border-emerald-100 text-emerald-700 mb-8 group-hover:bg-emerald-600 group-hover:text-white transition-colors duration-300">
                        <i data-lucide="house" class="w-8 h-8"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-slate-900 mb-3 tracking-tight relative z-10">Certificate of Residency</h3>
                    <p class="text-slate-600 mb-8 leading-relaxed flex-grow relative z-10">Official certification confirming verified residence for utility installation, bank opening, and school.</p>
                    <button class="relative z-10 inline-flex items-center gap-2 text-emerald-700 font-bold group-hover:text-emerald-800 bg-emerald-50 px-5 py-3 rounded-xl w-max transition-colors" onclick="showDialog('Certificate of Residency', 'Certifies bona fide residency within Barangay Kay-Anlog. Commonly requested for utility meter installations, school admissions, and financial institution requirements. Processing time: 24 hours.')">
                        View Requirements <i data-lucide="arrow-right" class="w-4 h-4 group-hover:translate-x-1 transition-transform"></i>
                    </button>
                </div>

                <!-- Certificate of Indigency -->
                <div class="group bg-white rounded-3xl p-8 border border-slate-200 shadow-sm hover:shadow-2xl hover:-translate-y-2 transition-all duration-300 relative overflow-hidden flex flex-col">
                    <div class="absolute -right-8 -top-8 opacity-5 group-hover:opacity-10 transition-opacity transform group-hover:scale-110 duration-500 pointer-events-none">
                        <i data-lucide="hand-heart" class="w-48 h-48 text-amber-900"></i>
                    </div>
                    <div class="w-16 h-16 rounded-2xl bg-amber-50 flex items-center justify-center border border-amber-100 text-amber-700 mb-8 group-hover:bg-amber-500 group-hover:text-white transition-colors duration-300">
                        <i data-lucide="hand-heart" class="w-8 h-8"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-slate-900 mb-3 tracking-tight relative z-10">Certificate of Indigency</h3>
                    <p class="text-slate-600 mb-8 leading-relaxed flex-grow relative z-10">Supporting document for financial aid, educational scholarships, medical subsidies, and PAO.</p>
                    <button class="relative z-10 inline-flex items-center gap-2 text-amber-700 font-bold group-hover:text-amber-800 bg-amber-50 px-5 py-3 rounded-xl w-max transition-colors" onclick="showDialog('Certificate of Indigency', 'Issued to low-income residents for medical assistance, educational scholarships, burial aid, and public attorney services (PAO). Fee-exempt under national guidelines. Processing time: Same day to 24 hours.')">
                        View Requirements <i data-lucide="arrow-right" class="w-4 h-4 group-hover:translate-x-1 transition-transform"></i>
                    </button>
                </div>

                <!-- Blotter & Incident Reporting -->
                <div class="group bg-slate-900 rounded-3xl p-8 border border-slate-800 shadow-lg hover:shadow-2xl hover:shadow-emerald-900/20 hover:-translate-y-2 transition-all duration-300 relative overflow-hidden flex flex-col lg:col-span-3 lg:flex-row lg:items-center lg:justify-between gap-8">
                    <!-- Abstract Background Graphic -->
                    <div class="absolute inset-0 bg-gradient-to-r from-slate-900 via-slate-900 to-emerald-900/40 z-0 pointer-events-none"></div>
                    
                    <div class="relative z-10 flex-grow lg:max-w-3xl">
                        <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-slate-800 text-slate-300 border border-slate-700 text-xs font-bold uppercase tracking-wider mb-4">
                            <i data-lucide="megaphone" class="w-3.5 h-3.5"></i> Peace &amp; Order
                        </div>
                        <h3 class="text-3xl font-bold text-white mb-4 tracking-tight">Blotter &amp; Incident Reporting</h3>
                        <p class="text-slate-300 text-lg leading-relaxed">Confidential documentation of neighborhood disputes, peace &amp; order incidents, and conciliation proceedings under the local Lupon Tagapamayapa.</p>
                    </div>

                    <div class="relative z-10 flex-shrink-0">
                        <button class="inline-flex justify-center items-center gap-2 bg-emerald-500 hover:bg-emerald-400 text-slate-900 font-bold px-8 py-4 rounded-xl transition-all shadow-lg shadow-emerald-500/20 w-full lg:w-auto hover:scale-105" onclick="showDialog('Blotter &amp; Incident Reporting', 'Formal documentation of neighborhood disputes, property matters, and incidents referred to the Lupon Tagapamayapa under Republic Act 7160. Handled with utmost confidentiality.')">
                            View Guidelines <i data-lucide="arrow-right" class="w-5 h-5"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Officials Section -->
    <section id="officials" class="py-24 bg-white border-t border-slate-200">
        <div class="container mx-auto px-4 lg:px-8">
            <div class="text-center max-w-2xl mx-auto mb-16">
                <span class="text-sm font-bold tracking-widest text-emerald-600 uppercase mb-3 block">Council Leadership</span>
                <h2 class="text-4xl font-extrabold text-slate-900 tracking-tight mb-5">Barangay Officials Roster</h2>
                <p class="text-lg text-slate-600">Meet the dedicated leaders serving Barangay Kay-Anlog with transparency and commitment.</p>
            </div>

            @if($officials->isEmpty())
                <div class="text-center py-16 bg-slate-50 rounded-3xl border-2 border-slate-200 border-dashed">
                    <i data-lucide="users-round" class="w-16 h-16 text-slate-300 mx-auto mb-4"></i>
                    <p class="text-lg text-slate-500 font-medium">No official records currently listed.</p>
                </div>
            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 xl:gap-8">
                    @foreach($officials as $official)
                    <div class="group bg-white rounded-2xl border border-slate-200 text-center hover:shadow-2xl hover:border-emerald-200 transition-all duration-300 relative overflow-hidden flex flex-col h-full">
                        <!-- Top Banner -->
                        <div class="h-20 bg-gradient-to-br from-emerald-50 to-slate-50 w-full relative border-b border-slate-100 group-hover:from-emerald-100 group-hover:to-teal-50 transition-colors duration-500">
                            <div class="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjAiIGhlaWdodD0iMjAiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PGNpcmNsZSBjeD0iMiIgY3k9IjIiIHI9IjIiIGZpbGw9IiMwNjRlM2IiIGZpbGwtb3BhY2l0eT0iMC4wNSIvPjwvc3ZnPg==')] opacity-50"></div>
                        </div>
                        
                        <div class="px-6 pb-6 -mt-10 flex-grow flex flex-col items-center relative z-10">
                            <div class="w-20 h-20 mx-auto rounded-full bg-white border-4 border-white shadow-md flex items-center justify-center overflow-hidden relative group-hover:scale-105 transition-transform duration-500 mb-4">
                                @if($official->image_path)
                                    <img src="{{ asset('storage/' . $official->image_path) }}" alt="{{ $official->name }}" class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full bg-slate-100 flex items-center justify-center text-slate-400">
                                        <i data-lucide="user-round" class="w-8 h-8"></i>
                                    </div>
                                @endif
                            </div>
                            
                            <h3 class="text-lg font-extrabold text-slate-900 leading-tight mb-1.5">{{ $official->name }}</h3>
                            <span class="inline-block px-3 py-1 bg-emerald-100/80 text-emerald-800 text-[0.65rem] font-bold tracking-widest uppercase rounded-md mb-4">{{ $official->position }}</span>
                            
                            @if($official->contact_number)
                            <div class="mt-auto pt-4 w-full border-t border-slate-100/80">
                                <span class="inline-block text-sm font-semibold text-slate-500 group-hover:text-emerald-700 transition-colors tracking-wide bg-slate-50 px-3 py-1 rounded-full group-hover:bg-emerald-50">{{ $official->contact_number }}</span>
                            </div>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            @endif
        </div>
    </section>

    <!-- Emergency Hotlines -->
    <section id="emergency" class="py-24 bg-[#0a2720] text-white relative overflow-hidden">
        <!-- Abstract red glow -->
        <div class="absolute top-0 right-0 w-[800px] h-[800px] bg-red-600/10 rounded-full blur-[120px] pointer-events-none transform translate-x-1/2 -translate-y-1/2"></div>
        
        <div class="container mx-auto px-4 lg:px-8 relative z-10">
            <div class="max-w-3xl mb-16">
                <span class="text-sm font-bold tracking-widest text-amber-400 uppercase mb-4 block flex items-center gap-2">
                    <i data-lucide="megaphone" class="w-4 h-4"></i> Immediate Response
                </span>
                <h2 class="text-4xl lg:text-5xl font-extrabold text-white tracking-tight mb-6">Emergency Hotlines</h2>
                <p class="text-xl text-emerald-100/70 max-w-2xl leading-relaxed">Keep these direct numbers readily available in case of peace and order, medical, or fire emergencies.</p>
            </div>

            <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="bg-white/5 border border-white/10 rounded-3xl p-6 xl:p-8 backdrop-blur-md hover:bg-white/10 hover:-translate-y-1 transition-all duration-300">
                    <div class="w-14 h-14 rounded-2xl bg-white/10 flex items-center justify-center mb-6 text-amber-400">
                        <i data-lucide="building-2" class="w-7 h-7"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-2">Barangay Hall</h3>
                    <p class="text-2xl xl:text-3xl font-extrabold text-white mb-4 tracking-tight whitespace-nowrap">(02) 8123-4567</p>
                    <p class="text-sm text-emerald-100/60 leading-relaxed">Direct assistance &amp; barangay executive officer action desk.</p>
                </div>
                
                <div class="bg-white/5 border border-white/10 rounded-3xl p-6 xl:p-8 backdrop-blur-md hover:bg-white/10 hover:-translate-y-1 transition-all duration-300">
                    <div class="w-14 h-14 rounded-2xl bg-blue-500/20 flex items-center justify-center mb-6 text-blue-400">
                        <i data-lucide="badge-check" class="w-7 h-7"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-2">Police Station</h3>
                    <p class="text-2xl xl:text-3xl font-extrabold text-white mb-4 tracking-tight whitespace-nowrap">911</p>
                    <p class="text-sm text-emerald-100/60 leading-relaxed">Calamba City Police Station emergency and 911 dispatch.</p>
                </div>
                
                <div class="bg-red-500/10 border border-red-500/20 rounded-3xl p-6 xl:p-8 backdrop-blur-md hover:bg-red-500/20 hover:-translate-y-1 transition-all duration-300 relative overflow-hidden group">
                    <div class="absolute -right-6 -bottom-6 opacity-20 group-hover:opacity-40 transition-opacity">
                        <i data-lucide="bell" class="w-40 h-40 text-red-500"></i>
                    </div>
                    <div class="relative z-10">
                        <div class="w-14 h-14 rounded-2xl bg-red-500/20 flex items-center justify-center mb-6 text-red-400">
                            <i data-lucide="bell" class="w-7 h-7"></i>
                        </div>
                        <h3 class="text-xl font-bold mb-2 text-red-50">Bureau of Fire</h3>
                        <p class="text-2xl xl:text-3xl font-extrabold text-red-400 mb-4 tracking-tight whitespace-nowrap">160</p>
                        <p class="text-sm text-red-100/70 leading-relaxed">BFP Calamba Fire Station emergency responders.</p>
                    </div>
                </div>
                
                <div class="bg-white/5 border border-white/10 rounded-3xl p-6 xl:p-8 backdrop-blur-md hover:bg-white/10 hover:-translate-y-1 transition-all duration-300">
                    <div class="w-14 h-14 rounded-2xl bg-emerald-500/20 flex items-center justify-center mb-6 text-emerald-400">
                        <i data-lucide="heart-pulse" class="w-7 h-7"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-2">Health Center</h3>
                    <p class="text-2xl xl:text-3xl font-extrabold text-white mb-4 tracking-tight whitespace-nowrap">0917-123-4567</p>
                    <p class="text-sm text-emerald-100/60 leading-relaxed">Barangay Health Center 24/7 rescue and ambulance service.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-white py-12 border-t border-slate-200">
        <div class="container mx-auto px-4 lg:px-8">
            <div class="flex flex-col md:flex-row items-center justify-between gap-6">
                <a href="{{ route('home') }}" class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-lg bg-emerald-50 flex items-center justify-center border border-emerald-100">
                        <i data-lucide="landmark" class="w-5 h-5 text-emerald-700"></i>
                    </div>
                    <div>
                        <span class="font-bold text-slate-900 block">Barangay Kay-Anlog</span>
                        <span class="text-xs text-emerald-600 font-semibold tracking-wider">Information System</span>
                    </div>
                </a>
                
                <p class="text-sm text-slate-500 font-medium">
                    &copy; {{ date('Y') }} Republic of the Philippines &middot; City of Calamba, Laguna
                </p>
                
                <a href="#" class="inline-flex items-center gap-2 px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-lg text-sm font-semibold transition-colors">
                    Back to top <i data-lucide="arrow-up" class="w-4 h-4"></i>
                </a>
            </div>
        </div>
    </footer>

    <!-- Native Detail Dialog -->
    <dialog id="detail-dialog" class="w-full max-w-lg bg-white p-0 rounded-3xl overflow-hidden shadow-2xl backdrop:bg-slate-900/60 backdrop:backdrop-blur-sm border border-slate-200">
        <div class="p-8">
            <div class="flex items-start justify-between mb-6">
                <div class="w-12 h-12 rounded-2xl bg-emerald-50 flex items-center justify-center text-emerald-600 border border-emerald-100">
                    <i data-lucide="info" class="w-6 h-6"></i>
                </div>
                <button onclick="document.getElementById('detail-dialog').close()" class="p-2 text-slate-400 hover:text-slate-700 hover:bg-slate-100 rounded-full transition-colors">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>
            
            <h3 id="dialog-title" class="text-2xl font-bold text-slate-900 mb-4 tracking-tight"></h3>
            <p id="dialog-text" class="text-slate-600 text-lg leading-relaxed mb-8"></p>
            
            <div class="flex flex-col-reverse sm:flex-row justify-end gap-3 pt-6 border-t border-slate-100">
                <button onclick="document.getElementById('detail-dialog').close()" class="w-full sm:w-auto px-6 py-3 text-sm font-bold text-slate-600 hover:bg-slate-100 rounded-xl transition-colors">
                    Close
                </button>
                <a href="{{ route('login') }}" class="w-full sm:w-auto inline-flex justify-center items-center gap-2 px-6 py-3 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-bold rounded-xl transition-colors shadow-lg shadow-emerald-900/20">
                    Proceed to Request <i data-lucide="arrow-right" class="w-4 h-4"></i>
                </a>
            </div>
        </div>
    </dialog>

    <script>
        function showDialog(title, text) {
            document.getElementById('dialog-title').innerText = title;
            document.getElementById('dialog-text').innerText = text;
            document.getElementById('detail-dialog').showModal();
        }
    </script>
</body>
</html>
