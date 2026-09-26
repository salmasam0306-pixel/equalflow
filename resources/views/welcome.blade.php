<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>EqualFlow — AI Team Collaboration</title>

    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,400;14..32,500;14..32,600;14..32,700;14..32,800&display=swap" rel="stylesheet" />

    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        * { font-family: 'Inter', sans-serif; }
        
        /* ===== ANIMATIONS ===== */
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }

        @keyframes pulse-slow {
            0%, 100% { opacity: 0.3; }
            50% { opacity: 0.6; }
        }

        @keyframes shimmer {
            0% { background-position: -200% center; }
            100% { background-position: 200% center; }
        }

        .animate-float {
            animation: float 6s ease-in-out infinite;
        }

        .animate-pulse-slow {
            animation: pulse-slow 4s ease-in-out infinite;
        }

        .delay-1000 { animation-delay: 1000ms; }
        .delay-2000 { animation-delay: 2000ms; }
        .delay-3000 { animation-delay: 3000ms; }

        /* ===== HERO SECTION ===== */
        .hero-section {
            background-image: url('https://images.unsplash.com/photo-1581094794329-c8112a89af12?w=1920&q=80');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            position: relative;
        }
        
        .hero-section::before {
            content: '';
            position: absolute;
            inset: 0;
            background: rgba(15, 23, 42, 0.75);
            z-index: 1;
        }
        
        .hero-content {
            position: relative;
            z-index: 2;
        }
        
        /* ===== FEATURE CARDS ===== */
        .feature-card {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .feature-card:hover { 
            transform: translateY(-6px); 
            box-shadow: 0 20px 40px -12px rgba(15, 23, 42, 0.15); 
        }

        .feature-card .icon-wrapper {
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .feature-card:hover .icon-wrapper {
            transform: scale(1.1) rotate(-5deg);
        }

        /* ===== CONTACT CARDS ===== */
        .contact-card {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border: 1px solid transparent;
        }

        .contact-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 40px -12px rgba(15, 23, 42, 0.12);
            border-color: rgba(26, 42, 74, 0.1);
        }

        .contact-card .icon-wrapper {
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .contact-card:hover .icon-wrapper {
            transform: scale(1.1);
        }

        /* ===== SCROLLBAR ===== */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        ::-webkit-scrollbar-thumb {
            background: #1a2a4a;
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #0f1a30;
        }

        /* ===== SMOOTH SCROLL ===== */
        html {
            scroll-behavior: smooth;
        }

        /* ===== GLASSMORPHISM UTILITY ===== */
        .glass {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .glass-dark {
            background: rgba(15, 23, 42, 0.8);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.05);
        }

        /* ===== NAVIGATION ===== */
        .nav-link {
            position: relative;
            transition: color 0.3s ease;
        }

        .nav-link::after {
            content: '';
            position: absolute;
            bottom: -4px;
            left: 0;
            width: 0;
            height: 2px;
            background: #1a2a4a;
            transition: width 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .nav-link:hover::after {
            width: 100%;
        }

        /* ===== BUTTON ANIMATIONS ===== */
        .btn-primary {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px -5px rgba(26, 42, 74, 0.3);
        }

        .btn-primary:active {
            transform: translateY(0px);
        }

        /* ===== RESPONSIVE ===== */
        @media (max-width: 640px) {
            .hero-section {
                background-attachment: scroll;
            }
            
            .text-7xl {
                font-size: 2.5rem;
            }
        }

        /* ===== SHIMMER EFFECT FOR BADGES ===== */
        .shimmer {
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.1), transparent);
            background-size: 200% auto;
            animation: shimmer 3s linear infinite;
        }

        /* ===== GRID PATTERN BACKGROUND ===== */
        .grid-pattern {
            background-image: radial-gradient(circle at 2px 2px, rgba(26, 42, 74, 0.05) 1px, transparent 0);
            background-size: 40px 40px;
        }

        /* ===== TRUST BADGES ===== */
        .trust-badge {
            transition: all 0.3s ease;
        }

        .trust-badge:hover {
            transform: translateY(-2px);
            color: #fff;
        }
    </style>
</head>
<body class="bg-white text-gray-900 antialiased">

    <!-- ======================================== -->
    <!-- ===== NAVIGATION ===== -->
    <!-- ======================================== -->
    <nav class="flex items-center justify-between px-6 md:px-16 py-4 bg-white/95 backdrop-blur-md border-b border-gray-100/50 sticky top-0 z-50 shadow-sm">
        <a href="/" class="text-2xl font-extrabold text-gray-900 flex items-center gap-1.5">
    <!-- Team Icon -->
            <svg class="w-7 h-7 text-[#1a2a4a]" viewBox="0 0 24 24" fill="currentColor">
                <path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"/>
            </svg>
            
            <!-- Equal (Dark) + Flow (Light) -->
            <span class="text-[#1a2a4a] tracking-tight">Equal</span>
            <span class="text-[#5b7a9a] tracking-tight">Flow</span>
        </a>

        <div class="hidden md:flex items-center gap-8">
            <a href="#features" class="nav-link text-gray-600 hover:text-[#1a2a4a] text-sm font-medium transition-colors">
                <i class="fas fa-th-large mr-1"></i> Features
            </a>
            <a href="#about" class="nav-link text-gray-600 hover:text-[#1a2a4a] text-sm font-medium transition-colors">
                <i class="fas fa-info-circle mr-1"></i> About
            </a>
            <a href="#contact" class="nav-link text-gray-600 hover:text-[#1a2a4a] text-sm font-medium transition-colors">
                <i class="fas fa-envelope mr-1"></i> Contact
            </a>
        </div>

        <div class="flex items-center gap-3">
            @guest
                <a href="{{ route('login') }}" class="hidden sm:inline-block px-5 py-2.5 border-2 border-gray-200 rounded-xl text-sm font-semibold text-gray-700 hover:bg-gray-50 hover:border-[#1a2a4a] transition-all bg-white/80 backdrop-blur-sm">
                    <i class="fas fa-sign-in-alt mr-2"></i> Log In
                </a>
                <a href="{{ route('register') }}" class="btn-primary px-5 py-2.5 bg-[#1a2a4a] hover:bg-[#0f1a30] text-white text-sm font-semibold rounded-xl shadow-md hover:shadow-lg transition-all transform hover:-translate-y-0.5">
                    <i class="fas fa-user-plus mr-2"></i> Sign Up
                </a>
            @endguest

            @auth
                <span class="text-sm text-gray-600 hidden sm:inline-block flex items-center gap-2">
                    <img src="{{ auth()->user()->getProfilePictureUrl() }}" class="w-8 h-8 rounded-full object-cover border-2 border-[#1a2a4a]">
                    {{ auth()->user()->name }}
                </span>
                <a href="{{ route('dashboard') }}" class="btn-primary px-5 py-2.5 bg-[#1a2a4a] hover:bg-[#0f1a30] text-white text-sm font-semibold rounded-xl shadow-md hover:shadow-lg transition-all transform hover:-translate-y-0.5">
                    <i class="fas fa-tachometer-alt mr-2"></i> Dashboard
                </a>
                <form method="POST" action="{{ route('logout') }}" class="inline">
                    @csrf
                    <button type="submit" class="text-sm text-red-500 hover:text-red-700 font-medium transition-all">
                        <i class="fas fa-sign-out-alt mr-1"></i>
                    </button>
                </form>
            @endauth
        </div>

        <!-- Mobile Menu Toggle -->
        <button id="menuToggle" class="md:hidden flex flex-col gap-1.5 p-1">
            <span class="w-6 h-0.5 bg-gray-900 rounded transition"></span>
            <span class="w-6 h-0.5 bg-gray-900 rounded transition"></span>
            <span class="w-6 h-0.5 bg-gray-900 rounded transition"></span>
        </button>
    </nav>

    <!-- Mobile Menu -->
    <div id="mobileMenu" class="md:hidden hidden bg-white/95 backdrop-blur-md border-b border-gray-100 px-6 py-4">
        <div class="flex flex-col gap-3">
            <a href="#features" class="text-gray-600 hover:text-gray-900 text-sm font-medium">
                <i class="fas fa-th-large mr-2"></i> Features
            </a>
            <a href="#about" class="text-gray-600 hover:text-gray-900 text-sm font-medium">
                <i class="fas fa-info-circle mr-2"></i> About
            </a>
            <a href="#contact" class="text-gray-600 hover:text-gray-900 text-sm font-medium">
                <i class="fas fa-envelope mr-2"></i> Contact
            </a>
            @auth
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="text-red-500 text-sm font-medium">
                        <i class="fas fa-sign-out-alt mr-2"></i> Log Out
                    </button>
                </form>
            @endauth
        </div>
    </div>

    <!-- ======================================== -->
    <!-- ===== HERO SECTION ===== -->
    <!-- ======================================== -->
    <section class="hero-section flex flex-col lg:flex-row items-center min-h-[calc(100vh-80px)] px-6 md:px-16 py-12 gap-12 lg:gap-16 overflow-hidden">
        
        <!-- Animated Background Elements -->
        <div class="absolute inset-0 z-0 opacity-20">
            <div class="absolute top-20 left-10 w-64 h-64 bg-indigo-500/20 rounded-full blur-3xl animate-pulse-slow"></div>
            <div class="absolute bottom-20 right-10 w-96 h-96 bg-purple-500/20 rounded-full blur-3xl animate-pulse-slow delay-1000"></div>
        </div>

        <div class="hero-content flex-1 max-w-2xl lg:max-w-none text-center lg:text-left relative z-10">
            <!-- Animated Badge -->
            <div class="inline-flex items-center gap-2 bg-white/10 backdrop-blur-sm px-4 py-2 rounded-full border border-white/20 mb-6 animate-float">
                <span class="relative flex h-2 w-2">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2 w-2 bg-green-500"></span>
                </span>
                <span class="text-xs text-white font-medium">AI-Powered Team Collaboration</span>
            </div>

            <h1 class="text-4xl sm:text-5xl lg:text-7xl font-extrabold leading-tight text-white">
                Keep your team
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-indigo-300 to-purple-300">
                    on the same page
                </span>
            </h1>
            
            <p class="mt-6 text-lg text-gray-200 leading-relaxed max-w-lg mx-auto lg:mx-0">
                <span class="font-bold text-indigo-300">EqualFlow</span> helps site teams and office teams collaborate seamlessly with AI-driven insights, workload monitoring, and smart task assignment.
            </p>
            
            <!-- Stats Banner -->

            <div class="mt-10 flex flex-col sm:flex-row gap-4 justify-center lg:justify-start">
                @guest
                    <a href="{{ route('register') }}" class="btn-primary px-8 py-4 bg-[#1a2a4a] hover:bg-[#0f1a30] text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transition-all transform hover:-translate-y-1 flex items-center justify-center gap-2 group">
                        <i class="fas fa-rocket group-hover:animate-pulse"></i> Sign Up
                        <i class="fas fa-arrow-right group-hover:translate-x-1 transition-transform"></i>
                    </a>
                @endguest
                @auth
                    <a href="{{ route('dashboard') }}" class="btn-primary px-8 py-4 bg-[#1a2a4a] hover:bg-[#0f1a30] text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transition-all transform hover:-translate-y-1 flex items-center justify-center gap-2">
                        <i class="fas fa-tachometer-alt"></i> Go to Dashboard →
                    </a>
                @endauth
            </div>
        </div>

        <!-- Enhanced Dashboard Preview - Light Version -->
        <div class="hero-content flex-1 max-w-2xl lg:max-w-none w-full relative z-10">
            <div class="relative">
                <!-- Floating Elements - Soft Blue -->
                <div class="absolute -top-6 -right-6 w-16 h-16 bg-blue-200/50 rounded-full blur-xl animate-pulse-slow"></div>
                <div class="absolute -bottom-6 -left-6 w-20 h-20 bg-indigo-200/50 rounded-full blur-xl animate-pulse-slow delay-1000"></div>
                <div class="absolute top-1/2 -left-10 w-12 h-12 bg-cyan-200/40 rounded-full blur-xl animate-pulse-slow delay-2000"></div>
                
                <!-- Main Card - Soft Blue Tint -->
                <div class="bg-gradient-to-br from-white via-blue-50/80 to-indigo-50/70 backdrop-blur-sm rounded-2xl p-6 shadow-xl border border-blue-100/60 relative">
                    <!-- Card Header -->
                    <div class="flex items-center gap-3 pb-4 border-b border-blue-100/60">
                        <div class="flex gap-1.5">
                            <span class="w-3 h-3 rounded-full bg-rose-400/80"></span>
                            <span class="w-3 h-3 rounded-full bg-amber-400/80"></span>
                            <span class="w-3 h-3 rounded-full bg-emerald-400/80"></span>
                        </div>
                        <span class="text-blue-700/80 text-xs font-medium ml-2">
                            <i class="fas fa-chart-pie mr-1 text-blue-600"></i> Dashboard
                        </span>
                    </div>

                    <!-- Stats Grid -->
                    <div class="grid grid-cols-2 gap-3 pt-4">
                        <!-- Total Tasks -->
                        <div class="bg-white/70 backdrop-blur-sm rounded-xl p-4 hover:bg-blue-50/80 transition-all border border-blue-100/50 hover:border-blue-300/50 shadow-sm hover:shadow-md">
                            <div class="text-blue-600/70 text-[0.6rem] uppercase tracking-wider font-semibold">
                                <i class="fas fa-tasks mr-1 text-blue-500"></i> Total Tasks
                            </div>
                            <div class="text-blue-900 text-2xl font-bold">24</div>
                            <div class="text-emerald-600 text-xs">
                                <i class="fas fa-user-plus mr-1"></i> 6 unassigned
                            </div>
                        </div>

                        <!-- Active Members -->
                        <div class="bg-white/70 backdrop-blur-sm rounded-xl p-4 hover:bg-blue-50/80 transition-all border border-blue-100/50 hover:border-blue-300/50 shadow-sm hover:shadow-md">
                            <div class="text-blue-600/70 text-[0.6rem] uppercase tracking-wider font-semibold">
                                <i class="fas fa-users mr-1 text-blue-500"></i> Active Members
                            </div>
                            <div class="text-blue-900 text-2xl font-bold">6/7</div>
                            <div class="text-rose-500 text-xs">
                                <i class="fas fa-exclamation-circle mr-1"></i> 1 inactive
                            </div>
                        </div>

                        <!-- Completion Rate -->
                        <div class="bg-white/70 backdrop-blur-sm rounded-xl p-4 hover:bg-blue-50/80 transition-all border border-blue-100/50 hover:border-blue-300/50 shadow-sm hover:shadow-md">
                            <div class="text-blue-600/70 text-[0.6rem] uppercase tracking-wider font-semibold">
                                <i class="fas fa-percentage mr-1 text-blue-500"></i> Completion Rate
                            </div>
                            <div class="text-blue-900 text-2xl font-bold">68%</div>
                            <div class="text-emerald-600 text-xs">
                                <i class="fas fa-arrow-up mr-1"></i> +5% from last week
                            </div>
                        </div>

                        <!-- Overdue Tasks -->
                        <div class="bg-white/70 backdrop-blur-sm rounded-xl p-4 hover:bg-blue-50/80 transition-all border border-blue-100/50 hover:border-blue-300/50 shadow-sm hover:shadow-md">
                            <div class="text-blue-600/70 text-[0.6rem] uppercase tracking-wider font-semibold">
                                <i class="fas fa-clock mr-1 text-blue-500"></i> Overdue Tasks
                            </div>
                            <div class="text-blue-900 text-2xl font-bold">2</div>
                            <div class="text-rose-500 text-xs">
                                <i class="fas fa-flag mr-1"></i> flagged by AI
                            </div>
                        </div>

                        <!-- Team Workload - Full Width -->
                        <div class="col-span-2 bg-white/70 backdrop-blur-sm rounded-xl p-4 hover:bg-blue-50/80 transition-all border border-blue-100/50 hover:border-blue-300/50 shadow-sm hover:shadow-md">
                            <div class="text-blue-600/70 text-[0.6rem] uppercase tracking-wider mb-3 font-semibold">
                                <i class="fas fa-chart-bar mr-1 text-blue-500"></i> Team Workload
                            </div>
                            <div class="grid grid-cols-4 gap-4">
                                <!-- Sophia -->
                                <div>
                                    <div class="flex items-center gap-1.5 text-blue-700 text-[0.6rem] font-medium">
                                        <div class="w-5 h-5 rounded-full bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-[0.4rem] text-white font-bold shadow-sm">
                                            S
                                        </div>
                                        Sophia
                                    </div>
                                    <div class="w-full h-2 bg-blue-100/60 rounded-full mt-1.5 overflow-hidden">
                                        <div class="h-full bg-gradient-to-r from-blue-400 to-indigo-500 rounded-full transition-all shadow-sm" style="width:60%"></div>
                                    </div>
                                    <div class="text-[0.5rem] text-blue-400/70 mt-0.5">6 tasks</div>
                                </div>

                                <!-- Alex -->
                                <div>
                                    <div class="flex items-center gap-1.5 text-rose-700 text-[0.6rem] font-medium">
                                        <div class="w-5 h-5 rounded-full bg-gradient-to-br from-rose-500 to-rose-600 flex items-center justify-center text-[0.4rem] text-white font-bold shadow-sm">
                                            A
                                        </div>
                                        Alex
                                    </div>
                                    <div class="w-full h-2 bg-rose-100/60 rounded-full mt-1.5 overflow-hidden">
                                        <div class="h-full bg-gradient-to-r from-rose-400 to-rose-500 rounded-full transition-all shadow-sm" style="width:90%"></div>
                                    </div>
                                    <div class="text-[0.5rem] text-rose-400/70 mt-0.5">9 tasks</div>
                                </div>

                                <!-- Anna -->
                                <div>
                                    <div class="flex items-center gap-1.5 text-amber-700 text-[0.6rem] font-medium">
                                        <div class="w-5 h-5 rounded-full bg-gradient-to-br from-amber-500 to-amber-600 flex items-center justify-center text-[0.4rem] text-white font-bold shadow-sm">
                                            A
                                        </div>
                                        Anna
                                    </div>
                                    <div class="w-full h-2 bg-amber-100/60 rounded-full mt-1.5 overflow-hidden">
                                        <div class="h-full bg-gradient-to-r from-amber-400 to-amber-500 rounded-full transition-all shadow-sm" style="width:20%"></div>
                                    </div>
                                    <div class="text-[0.5rem] text-amber-400/70 mt-0.5">2 tasks</div>
                                </div>

                                <!-- Eugene -->
                                <div>
                                    <div class="flex items-center gap-1.5 text-emerald-700 text-[0.6rem] font-medium">
                                        <div class="w-5 h-5 rounded-full bg-gradient-to-br from-emerald-500 to-emerald-600 flex items-center justify-center text-[0.4rem] text-white font-bold shadow-sm">
                                            E
                                        </div>
                                        Eugene
                                    </div>
                                    <div class="w-full h-2 bg-emerald-100/60 rounded-full mt-1.5 overflow-hidden">
                                        <div class="h-full bg-gradient-to-r from-emerald-400 to-emerald-500 rounded-full transition-all shadow-sm" style="width:50%"></div>
                                    </div>
                                    <div class="text-[0.5rem] text-emerald-400/70 mt-0.5">5 tasks</div>
                                </div>
                            </div>

                            <!-- Team Average -->
                            <div class="flex items-center justify-between mt-3 pt-2 border-t border-blue-100/50">
                                <div class="text-blue-500/70 text-[0.55rem]">
                                    <i class="fas fa-arrow-down mr-1 text-emerald-500"></i> Team average: 7 tasks
                                </div>
                                <div class="flex items-center gap-2">
                                    <div class="flex items-center gap-1 text-blue-400 text-[0.5rem]">
                                        <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                                        <span>Low</span>
                                    </div>
                                    <div class="flex items-center gap-1 text-amber-400 text-[0.5rem]">
                                        <span class="w-2 h-2 rounded-full bg-amber-400"></span>
                                        <span>Medium</span>
                                    </div>
                                    <div class="flex items-center gap-1 text-rose-400 text-[0.5rem]">
                                        <span class="w-2 h-2 rounded-full bg-rose-400"></span>
                                        <span>High</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ======================================== -->
    <!-- ===== FEATURES SECTION ===== -->
    <!-- ======================================== -->
    <section id="features" class="px-6 md:px-16 py-24 bg-white relative overflow-hidden">
        <!-- Background Pattern -->
        <div class="absolute inset-0 opacity-5 grid-pattern"></div>

        <div class="max-w-3xl mx-auto text-center mb-16 relative">
            <span class="inline-block px-4 py-1.5 bg-[#1a2a4a]/10 text-[#1a2a4a] text-sm font-semibold rounded-full tracking-wide mb-4">
                <i class="fas fa-cogs mr-1"></i> Features
            </span>
            <h2 class="text-4xl md:text-5xl font-bold text-gray-900">
                Built for <span class="text-[#1a2a4a]">modern</span> teams
            </h2>
            <p class="mt-4 text-lg text-gray-500 max-w-2xl mx-auto">Everything you need to keep site work and office work in perfect sync.</p>
        </div>

        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8 max-w-6xl mx-auto relative">
            @php
                $features = [
                    [
                        'icon' => 'fa-robot',
                        'title' => 'AI-Powered Matching',
                        'description' => 'Smart task assignment based on skills, workload, and performance history.',
                        'gradient' => 'from-blue-500 to-indigo-600'
                    ],
                    [
                        'icon' => 'fa-chart-line',
                        'title' => 'Workload Analysis',
                        'description' => 'Real-time visibility into team capacity with automatic overload alerts.',
                        'gradient' => 'from-green-500 to-emerald-600'
                    ],
                    [
                        'icon' => 'fa-bullseye',
                        'title' => 'Smart Recommendations',
                        'description' => 'AI-driven insights for task reassignment and project optimization.',
                        'gradient' => 'from-purple-500 to-pink-600'
                    ],
                    [
                        'icon' => 'fa-bell',
                        'title' => 'Proactive Alerts',
                        'description' => 'Get notified about overdue tasks, inactive members, and capacity risks.',
                        'gradient' => 'from-orange-500 to-red-600'
                    ],
                    [
                        'icon' => 'fa-clipboard-list',
                        'title' => 'Workflow Canvas',
                        'description' => 'Visual task mapping with dependencies and real-time progress tracking.',
                        'gradient' => 'from-teal-500 to-cyan-600'
                    ],
                    [
                        'icon' => 'fa-users-cog',
                        'title' => 'Role-Based Views',
                        'description' => 'Custom dashboards for leaders, members, and principals.',
                        'gradient' => 'from-indigo-500 to-purple-600'
                    ]
                ];
            @endphp

            @foreach($features as $index => $feature)
                <div class="group bg-gray-50/80 backdrop-blur-sm p-8 rounded-2xl border border-gray-100 hover:border-[#1a2a4a]/20 transition-all duration-300 hover:-translate-y-2 hover:shadow-xl relative overflow-hidden">
                    <!-- Gradient Icon Background -->
                    <div class="icon-wrapper w-14 h-14 rounded-2xl bg-gradient-to-br {{ $feature['gradient'] }} flex items-center justify-center text-2xl mb-5 shadow-lg">
                        <i class="fas {{ $feature['icon'] }} text-white"></i>
                    </div>
                    
                    <h3 class="text-xl font-semibold text-gray-900 mb-2 group-hover:text-[#1a2a4a] transition-colors">
                        {{ $feature['title'] }}
                    </h3>
                    <p class="text-gray-500 text-sm leading-relaxed">{{ $feature['description'] }}</p>
                    
                    <!-- Learn More Link -->
                    <div class="mt-4 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                        <a href="#" class="text-[#1a2a4a] text-sm font-semibold inline-flex items-center gap-1 hover:gap-2 transition-all">
                            Learn More <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>

                    <!-- Hover Gradient Overlay -->
                    <div class="absolute inset-0 bg-gradient-to-br {{ $feature['gradient'] }} opacity-0 group-hover:opacity-[0.03] transition-opacity duration-300 rounded-2xl pointer-events-none"></div>
                </div>
            @endforeach
        </div>
    </section>

    <!-- ======================================== -->
    <!-- ===== ABOUT SECTION WITH BACKGROUND ===== -->
    <!-- ======================================== -->
    <section id="about" class="relative px-6 md:px-16 py-24 overflow-hidden">
        <!-- ===== BACKGROUND LAYER ===== -->
        <div class="absolute inset-0 z-0">
            <!-- Dark overlay with gradient -->
            <div class="absolute inset-0 bg-gradient-to-br from-[#0a1428] via-[#1a2a4a] to-[#0f1a30] z-10"></div>
            
            <!-- Animated Gradient Orbs -->
            <div class="absolute top-10 left-10 w-96 h-96 bg-indigo-600/20 rounded-full blur-3xl animate-pulse-slow z-0"></div>
            <div class="absolute bottom-10 right-10 w-80 h-80 bg-purple-600/20 rounded-full blur-3xl animate-pulse-slow delay-1000 z-0"></div>
            <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-64 h-64 bg-blue-500/10 rounded-full blur-3xl animate-pulse-slow delay-2000 z-0"></div>
            
            <!-- Grid Pattern Overlay -->
            <div class="absolute inset-0 z-0 opacity-10" 
                style="background-image: radial-gradient(circle at 2px 2px, rgba(255,255,255,0.3) 1px, transparent 0); background-size: 40px 40px;"></div>
        </div>
        
        <!-- ===== CONTENT LAYER ===== -->
        <div class="relative z-20 max-w-6xl mx-auto">
            <!-- Section Header -->
            <div class="text-center mb-16">
                <span class="inline-block px-4 py-1.5 bg-white/10 backdrop-blur-sm text-white text-sm font-semibold rounded-full tracking-wide mb-4 border border-white/10 hover:bg-white/20 transition-all">
                    <i class="fas fa-info-circle mr-1"></i> About EqualFlow
                </span>
                <h2 class="text-4xl md:text-5xl font-bold text-white">
                    Empowering teams to <span class="text-transparent bg-clip-text bg-gradient-to-r from-indigo-300 via-purple-300 to-pink-300">work smarter</span>
                </h2>
                <p class="mt-4 text-lg text-gray-300 max-w-2xl mx-auto">Built for engineering and construction teams who need to stay in sync.</p>
            </div>

            <!-- Main Content Grid -->
            <div class="grid lg:grid-cols-2 gap-12 items-start">
                <!-- Left Column: Mission & Values -->
                <div class="space-y-6">
                    <!-- Mission Card -->
                    <div class="bg-white/5 backdrop-blur-md rounded-2xl p-8 border border-white/10 hover:bg-white/10 hover:border-white/20 transition-all duration-300 group">
                        <h3 class="text-2xl font-bold text-white mb-4 flex items-center gap-3">
                            <i class="fas fa-rocket text-indigo-400 group-hover:scale-110 group-hover:rotate-[-10deg] transition-transform"></i> 
                            Our Mission
                        </h3>
                        <p class="text-gray-300 leading-relaxed">
                            <span class="font-bold text-white">EqualFlow</span> was created to solve the real-world challenges of team collaboration in 
                            engineering and construction firms. We understand that <strong class="text-white">site work</strong> and 
                            <strong class="text-white">office work</strong> often operate in silos — and we're here to change that.
                        </p>
                        <p class="text-gray-300 leading-relaxed mt-3">
                            Our AI-powered platform ensures that every team member gets credit for their work, 
                            workloads are balanced, and projects stay on track.
                        </p>
                    </div>

                    <!-- Values Card -->
                    <div class="bg-white/5 backdrop-blur-md rounded-2xl p-8 border border-white/10 hover:bg-white/10 hover:border-white/20 transition-all duration-300">
                        <h3 class="text-2xl font-bold text-white mb-4 flex items-center gap-3">
                            <i class="fas fa-heart text-indigo-400"></i> 
                            Our Values
                        </h3>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="bg-white/5 p-4 rounded-xl border border-white/5 hover:border-white/20 hover:bg-white/10 transition-all duration-300 group">
                                <div class="text-2xl mb-2 group-hover:scale-110 group-hover:rotate-[-5deg] transition-transform">
                                    <i class="fas fa-scale-balanced text-indigo-400"></i>
                                </div>
                                <p class="font-semibold text-white text-sm">Fairness</p>
                                <p class="text-xs text-gray-400">Equal workload distribution</p>
                            </div>
                            <div class="bg-white/5 p-4 rounded-xl border border-white/5 hover:border-white/20 hover:bg-white/10 transition-all duration-300 group">
                                <div class="text-2xl mb-2 group-hover:scale-110 group-hover:rotate-[5deg] transition-transform">
                                    <i class="fas fa-eye text-indigo-400"></i>
                                </div>
                                <p class="font-semibold text-white text-sm">Transparency</p>
                                <p class="text-xs text-gray-400">Clear visibility into progress</p>
                            </div>
                            <div class="bg-white/5 p-4 rounded-xl border border-white/5 hover:border-white/20 hover:bg-white/10 transition-all duration-300 group">
                                <div class="text-2xl mb-2 group-hover:scale-110 group-hover:rotate-[-5deg] transition-transform">
                                    <i class="fas fa-lightbulb text-indigo-400"></i>
                                </div>
                                <p class="font-semibold text-white text-sm">Innovation</p>
                                <p class="text-xs text-gray-400">AI-driven solutions</p>
                            </div>
                            <div class="bg-white/5 p-4 rounded-xl border border-white/5 hover:border-white/20 hover:bg-white/10 transition-all duration-300 group">
                                <div class="text-2xl mb-2 group-hover:scale-110 group-hover:rotate-[5deg] transition-transform">
                                    <i class="fas fa-handshake text-indigo-400"></i>
                                </div>
                                <p class="font-semibold text-white text-sm">Collaboration</p>
                                <p class="text-xs text-gray-400">Bridging teams together</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Right Column: Who We Serve -->
                <div class="bg-gradient-to-br from-[#1a2a4a]/90 to-[#0f1a30]/90 backdrop-blur-md rounded-2xl p-8 border border-white/10 hover:border-white/20 transition-all duration-300 relative overflow-hidden">
                    <!-- Decorative Element -->
                    <div class="absolute -top-20 -right-20 w-64 h-64 bg-indigo-500/10 rounded-full blur-3xl"></div>
                    <div class="absolute -bottom-20 -left-20 w-64 h-64 bg-purple-500/10 rounded-full blur-3xl"></div>
                    
                    <div class="relative z-10">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="w-12 h-12 rounded-full bg-gradient-to-br from-indigo-500/30 to-purple-500/30 flex items-center justify-center">
                                <i class="fas fa-users text-2xl text-indigo-400 animate-float"></i>
                            </div>
                            <h4 class="text-xl font-bold text-white">Who We Serve</h4>
                        </div>
                        
                        <ul class="space-y-4">
                            <li class="flex items-center gap-4 p-3 rounded-xl hover:bg-white/5 transition-all duration-300 group cursor-default">
                                <div class="w-10 h-10 rounded-full bg-gradient-to-br from-indigo-500/30 to-indigo-600/30 flex items-center justify-center text-indigo-400 group-hover:scale-110 transition-transform">
                                    <i class="fas fa-building"></i>
                                </div>
                                <div>
                                    <p class="font-semibold text-white">Civil & Structural Engineering Firms</p>
                                    <p class="text-xs text-gray-400">Design and infrastructure projects</p>
                                </div>
                            </li>
                            <li class="flex items-center gap-4 p-3 rounded-xl hover:bg-white/5 transition-all duration-300 group cursor-default">
                                <div class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-500/30 to-blue-600/30 flex items-center justify-center text-blue-400 group-hover:scale-110 transition-transform">
                                    <i class="fas fa-hard-hat"></i>
                                </div>
                                <div>
                                    <p class="font-semibold text-white">Construction Project Teams</p>
                                    <p class="text-xs text-gray-400">Site management and supervision</p>
                                </div>
                            </li>
                            <li class="flex items-center gap-4 p-3 rounded-xl hover:bg-white/5 transition-all duration-300 group cursor-default">
                                <div class="w-10 h-10 rounded-full bg-gradient-to-br from-green-500/30 to-green-600/30 flex items-center justify-center text-green-400 group-hover:scale-110 transition-transform">
                                    <i class="fas fa-clipboard-check"></i>
                                </div>
                                <div>
                                    <p class="font-semibold text-white">Site Supervisors & Inspectors</p>
                                    <p class="text-xs text-gray-400">On-site quality control</p>
                                </div>
                            </li>
                            <li class="flex items-center gap-4 p-3 rounded-xl hover:bg-white/5 transition-all duration-300 group cursor-default">
                                <div class="w-10 h-10 rounded-full bg-gradient-to-br from-purple-500/30 to-purple-600/30 flex items-center justify-center text-purple-400 group-hover:scale-110 transition-transform">
                                    <i class="fas fa-user-tie"></i>
                                </div>
                                <div>
                                    <p class="font-semibold text-white">Office Managers & Principals</p>
                                    <p class="text-xs text-gray-400">Leadership and oversight</p>
                                </div>
                            </li>
                            <li class="flex items-center gap-4 p-3 rounded-xl hover:bg-white/5 transition-all duration-300 group cursor-default">
                                <div class="w-10 h-10 rounded-full bg-gradient-to-br from-pink-500/30 to-pink-600/30 flex items-center justify-center text-pink-400 group-hover:scale-110 transition-transform">
                                    <i class="fas fa-graduation-cap"></i>
                                </div>
                                <div>
                                    <p class="font-semibold text-white">Students & Individual Users</p>
                                    <p class="text-xs text-gray-400">Learning and personal projects</p>
                                </div>
                            </li>
                        </ul>
                        
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ======================================== -->
    <!-- ===== CONTACT SECTION ===== -->
    <!-- ======================================== -->
    <section id="contact" class="px-6 md:px-16 py-24 bg-white relative overflow-hidden">
        <div class="max-w-4xl mx-auto relative">
            <div class="text-center mb-16">
                <span class="inline-block px-4 py-1.5 bg-[#1a2a4a]/10 text-[#1a2a4a] text-sm font-semibold rounded-full tracking-wide mb-4">
                    <i class="fas fa-envelope mr-1"></i> Contact
                </span>
                <h2 class="text-4xl md:text-5xl font-bold text-gray-900">
                    Get in <span class="text-[#1a2a4a]">Touch</span>
                </h2>
                <p class="mt-4 text-lg text-gray-500">Have questions? We'd love to hear from you.</p>
            </div>

            <div class="grid md:grid-cols-3 gap-6">
                <div class="contact-card bg-gray-50 rounded-2xl p-8 text-center hover:shadow-xl transition-all duration-300 hover:-translate-y-2 border border-transparent hover:border-[#1a2a4a]/10">
                    <div class="icon-wrapper w-16 h-16 bg-gradient-to-br from-[#1a2a4a] to-[#0f1a30] rounded-2xl flex items-center justify-center text-3xl mx-auto mb-4 shadow-lg">
                        <i class="fas fa-envelope text-white"></i>
                    </div>
                    <h4 class="font-semibold text-gray-900 mb-2">Email Us</h4>
                    <a href="mailto:info@equalflow.com" class="text-gray-500 hover:text-[#1a2a4a] transition text-sm hover:underline">
                        info@equalflow.com
                    </a>
                    <p class="text-xs text-gray-400 mt-2">We'll respond within 24 hours</p>
                </div>

                <div class="contact-card bg-gray-50 rounded-2xl p-8 text-center hover:shadow-xl transition-all duration-300 hover:-translate-y-2 border border-transparent hover:border-[#1a2a4a]/10">
                    <div class="icon-wrapper w-16 h-16 bg-gradient-to-br from-[#1a2a4a] to-[#0f1a30] rounded-2xl flex items-center justify-center text-3xl mx-auto mb-4 shadow-lg">
                        <i class="fas fa-phone text-white"></i>
                    </div>
                    <h4 class="font-semibold text-gray-900 mb-2">Call Us</h4>
                    <a href="tel:+60123456789" class="text-gray-500 hover:text-[#1a2a4a] transition text-sm hover:underline">
                        +60 12-345 6789
                    </a>
                    <p class="text-xs text-gray-400 mt-2">Mon-Fri, 9AM - 6PM</p>
                </div>

                <div class="contact-card bg-gray-50 rounded-2xl p-8 text-center hover:shadow-xl transition-all duration-300 hover:-translate-y-2 border border-transparent hover:border-[#1a2a4a]/10">
                    <div class="icon-wrapper w-16 h-16 bg-gradient-to-br from-[#1a2a4a] to-[#0f1a30] rounded-2xl flex items-center justify-center text-3xl mx-auto mb-4 shadow-lg">
                        <i class="fas fa-map-marker-alt text-white"></i>
                    </div>
                    <h4 class="font-semibold text-gray-900 mb-2">Visit Us</h4>
                    <p class="text-gray-500 text-sm leading-relaxed">
                        Politeknik Kuala Terengganu<br>
                        Terengganu, Malaysia
                    </p>
                    <p class="text-xs text-gray-400 mt-2">By appointment only</p>
                </div>
            </div>
        </div>
    </section>

    <!-- ======================================== -->
    <!-- ===== CTA SECTION ===== -->
    <!-- ======================================== -->
    <section class="relative px-6 md:px-16 py-24 overflow-hidden">
        <!-- Animated Background -->
        <div class="absolute inset-0 bg-gradient-to-br from-[#1a2a4a] via-[#0f1a30] to-[#1a2a4a]">
            <div class="absolute top-0 left-0 w-full h-full opacity-30">
                <div class="absolute top-20 left-10 w-64 h-64 bg-indigo-500/30 rounded-full blur-3xl animate-pulse-slow"></div>
                <div class="absolute bottom-20 right-10 w-96 h-96 bg-purple-500/30 rounded-full blur-3xl animate-pulse-slow delay-1000"></div>
            </div>
        </div>

        <div class="relative z-10 max-w-3xl mx-auto text-center">
            <span class="inline-block px-4 py-1.5 bg-white/10 backdrop-blur-sm text-white text-sm font-semibold rounded-full tracking-wide mb-6 border border-white/10 animate-float">
                <i class="fas fa-rocket mr-1"></i> Sign Up
            </span>
            <h2 class="text-4xl md:text-5xl font-bold text-white">
                Ready to <span class="text-transparent bg-clip-text bg-gradient-to-r from-indigo-300 to-purple-300">transform</span> your team's workflow?
            </h2>
            <p class="mt-4 text-lg text-gray-300 max-w-2xl mx-auto">Join teams already using EqualFlow to collaborate smarter and deliver better results.</p>
            
            <div class="mt-10 flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ route('register') }}" class="group px-8 py-4 bg-white text-[#1a2a4a] font-semibold rounded-xl shadow-lg hover:shadow-xl transition-all transform hover:-translate-y-1 flex items-center justify-center gap-2">
                    <i class="fas fa-user-plus group-hover:animate-pulse"></i> Sign Up
                    <i class="fas fa-arrow-right group-hover:translate-x-1 transition-transform"></i>
                </a>
            </div>

        </div>
    </section>

    <!-- ======================================== -->
    <!-- ===== FOOTER ===== -->
    <!-- ======================================== -->
    <footer class="bg-white border-t border-gray-100 px-6 md:px-16 py-10">
        <div class="max-w-6xl mx-auto flex flex-col md:flex-row items-center justify-between">
            <div class="flex items-center gap-3">
                <svg class="w-6 h-6 text-[#1a2a4a]" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 17.93c-3.95-.49-7-3.85-7-7.93 0-.62.08-1.21.21-1.79L9 15v1c0 1.1.9 2 2 2v1.93zm6.9-2.54c-.26-.81-1-1.39-1.9-1.39h-1v-3c0-.55-.45-1-1-1H8v-2h2c.55 0 1-.45 1-1V7h2c1.1 0 2-.9 2-2v-.41c2.93 1.19 5 4.06 5 7.41 0 2.08-.8 3.97-2.1 5.39z"/>
                </svg>
                <p class="text-sm text-gray-500">
                    <i class="far fa-copyright mr-1"></i> 2026 EqualFlow
                </p>
            </div>
            <div class="flex items-center gap-6 mt-4 md:mt-0">
                <a href="#features" class="text-sm text-gray-400 hover:text-gray-600 transition-colors">
                    <i class="fas fa-th-large mr-1"></i> Features
                </a>
                <a href="#about" class="text-sm text-gray-400 hover:text-gray-600 transition-colors">
                    <i class="fas fa-info-circle mr-1"></i> About
                </a>
                <a href="#contact" class="text-sm text-gray-400 hover:text-gray-600 transition-colors">
                    <i class="fas fa-envelope mr-1"></i> Contact
                </a>
            </div>
        </div>
    </footer>

    <!-- ===== JAVASCRIPT ===== -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Mobile Menu Toggle
            const toggle = document.getElementById('menuToggle');
            const menu = document.getElementById('mobileMenu');
            if (toggle && menu) {
                toggle.addEventListener('click', function() {
                    menu.classList.toggle('hidden');
                    // Animate hamburger
                    const spans = this.querySelectorAll('span');
                    spans.forEach(span => span.classList.toggle('bg-[#1a2a4a]'));
                });
            }

            // Smooth scroll for anchor links
            document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                anchor.addEventListener('click', function(e) {
                    const href = this.getAttribute('href');
                    if (href === '#') return;
                    
                    e.preventDefault();
                    const target = document.querySelector(href);
                    if (target) {
                        target.scrollIntoView({
                            behavior: 'smooth',
                            block: 'start'
                        });
                        // Close mobile menu if open
                        if (menu && !menu.classList.contains('hidden')) {
                            menu.classList.add('hidden');
                        }
                    }
                });
            });

            // Intersection Observer for fade-in animations
            const observerOptions = {
                threshold: 0.1,
                rootMargin: '0px 0px -50px 0px'
            };

            const observer = new IntersectionObserver(function(entries) {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.style.opacity = '1';
                        entry.target.style.transform = 'translateY(0)';
                    }
                });
            }, observerOptions);

            // Observe feature cards and contact cards
            document.querySelectorAll('.feature-card, .contact-card').forEach(el => {
                el.style.opacity = '0';
                el.style.transform = 'translateY(20px)';
                el.style.transition = 'all 0.6s cubic-bezier(0.4, 0, 0.2, 1)';
                observer.observe(el);
            });

            // Auto-dismiss flash messages if any
            const flashMessages = document.querySelectorAll('.flash-message');
            flashMessages.forEach(function(message) {
                setTimeout(function() {
                    message.style.transition = 'opacity 0.5s ease';
                    message.style.opacity = '0';
                    setTimeout(function() {
                        message.remove();
                    }, 500);
                }, 5000);
            });
        });
    </script>
</body>
</html>