<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>EqualFlow - @yield('title', 'Welcome')</title>

    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,400;14..32,500;14..32,600;14..32,700;14..32,800&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        * { font-family: 'Inter', sans-serif; }
        
        /* ===== LEFT SIDE - BACKGROUND IMAGE ===== */
        .auth-left {
            background-image: url('https://images.unsplash.com/photo-1581094794329-c8112a89af12?w=1920&q=80');
            background-size: cover;
            background-position: center;
            position: relative;
            min-height: 100vh;
        }
        
        .auth-left::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(15, 23, 42, 0.85) 0%, rgba(26, 42, 74, 0.75) 100%);
            z-index: 1;
        }
        
        .auth-left-content {
            position: relative;
            z-index: 2;
        }

        /* ===== ANIMATED ELEMENTS ===== */
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }

        @keyframes pulse-slow {
            0%, 100% { opacity: 0.3; }
            50% { opacity: 0.6; }
        }

        .animate-float {
            animation: float 6s ease-in-out infinite;
        }

        .animate-pulse-slow {
            animation: pulse-slow 4s ease-in-out infinite;
        }

        .delay-1000 {
            animation-delay: 1000ms;
        }

        /* ===== FLOATING ORBS ===== */
        .orb-1 {
            position: absolute;
            top: 10%;
            right: 15%;
            width: 200px;
            height: 200px;
            background: radial-gradient(circle, rgba(99, 102, 241, 0.15), transparent 70%);
            border-radius: 50%;
            z-index: 0;
            animation: pulse-slow 6s ease-in-out infinite;
        }

        .orb-2 {
            position: absolute;
            bottom: 15%;
            left: 10%;
            width: 150px;
            height: 150px;
            background: radial-gradient(circle, rgba(139, 92, 246, 0.12), transparent 70%);
            border-radius: 50%;
            z-index: 0;
            animation: pulse-slow 8s ease-in-out infinite reverse;
        }

        /* ===== RIGHT SIDE ===== */
        .auth-right {
            background: #ffffff;
            min-height: 100vh;
        }

        /* ===== FORM STYLES ===== */
        .auth-input {
            transition: all 0.3s ease;
            border-color: #e5e7eb;
        }

        .auth-input:focus {
            border-color: #1a2a4a;
            box-shadow: 0 0 0 3px rgba(26, 42, 74, 0.1);
            outline: none;
        }

        .auth-input:hover {
            border-color: #9ca3af;
        }

        .auth-btn {
            transition: all 0.3s ease;
            background: #1a2a4a;
            color: white;
        }

        .auth-btn:hover {
            background: #0f1a30;
            transform: translateY(-1px);
            box-shadow: 0 8px 25px -8px rgba(26, 42, 74, 0.3);
        }

        .auth-btn:active {
            transform: translateY(0px);
        }

        .auth-link {
            color: #1a2a4a;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .auth-link:hover {
            color: #0f1a30;
            text-decoration: underline;
        }

        /* ===== GLASSMORPHISM BADGE ===== */
        .glass-badge {
            background: rgba(255, 255, 255, 0.08);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            transition: all 0.3s ease;
        }

        .glass-badge:hover {
            background: rgba(255, 255, 255, 0.15);
            border-color: rgba(255, 255, 255, 0.2);
            transform: translateY(-2px);
        }

        /* ===== LOGO STYLING ===== */
        .logo-equal {
            color: #1a2a4a;
        }

        .logo-flow {
            color: #5b7a9a;
        }

        /* ===== RESPONSIVE ===== */
        @media (max-width: 1024px) {
            .auth-left {
                min-height: 50vh;
            }
            
            .auth-right {
                min-height: 50vh;
            }
        }

        @media (max-width: 640px) {
            .auth-left-content h1 {
                font-size: 2.5rem;
            }
            
            .auth-left-content {
                text-align: center !important;
            }
        }
    </style>
</head>
<body class="bg-white text-gray-900 antialiased">

<div class="flex flex-col lg:flex-row min-h-screen">
    
    <!-- ======================================== -->
    <!-- LEFT SIDE - Background Image (60%)      -->
    <!-- ======================================== -->
    <div class="lg:w-3/5 auth-left flex items-center justify-center p-8 lg:p-12 relative overflow-hidden">
        
        <!-- Floating Orbs -->
        <div class="orb-1"></div>
        <div class="orb-2"></div>
        
        <!-- Content - LEFT ALIGNED -->
        <div class="auth-left-content max-w-md w-full relative z-10 text-left">
            <!-- Logo -->
            <div class="flex items-center gap-2 mb-6">
                <svg class="w-10 h-10 text-white flex-shrink-0" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"/>
                </svg>
                <span class="text-3xl font-extrabold text-white tracking-tight">
                    Equal<span class="text-indigo-300">Flow</span>
                </span>
            </div>

            <!-- Main Heading - LEFT ALIGNED -->
            <h1 class="text-4xl md:text-5xl font-extrabold text-white leading-tight">
                Welcome to the<br />
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-indigo-300 to-purple-300">
                    future of teamwork
                </span>
            </h1>
            
            <!-- Description - LEFT ALIGNED -->
            <p class="mt-4 text-base md:text-lg text-gray-300 leading-relaxed">
                Built for teams where <strong class="text-white font-semibold">site work</strong> and 
                <strong class="text-white font-semibold">office work</strong> need to stay on the same page.
            </p>

            <!-- Features Badges - LEFT ALIGNED -->
            <div class="mt-8 flex flex-wrap items-center gap-3 text-sm">
                <span class="glass-badge px-4 py-2 rounded-full text-gray-300 flex items-center gap-2">
                    <i class="fas fa-shield-alt text-indigo-400"></i> Secure
                </span>
                <span class="glass-badge px-4 py-2 rounded-full text-gray-300 flex items-center gap-2">
                    <i class="fas fa-lock text-indigo-400"></i> Encrypted
                </span>
                <span class="glass-badge px-4 py-2 rounded-full text-gray-300 flex items-center gap-2">
                    <i class="fas fa-bolt text-indigo-400"></i> 2-min setup
                </span>
            </div>

        </div>
    </div>

    <!-- ======================================== -->
    <!-- RIGHT SIDE - White Form (40%)            -->
    <!-- ======================================== -->
    <div class="lg:w-2/5 auth-right flex items-center justify-center p-8 lg:p-12">
        <div class="w-full max-w-md">
            
            <!-- Mobile Logo (shows on smaller screens) -->
            <div class="lg:hidden flex items-center justify-center gap-2 mb-8">
                <svg class="w-8 h-8 text-[#1a2a4a]" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M12 12.75c1.63 0 3.07.39 4.24.9 1.08.48 1.76 1.56 1.76 2.73V18H6v-1.61c0-1.18.68-2.26 1.76-2.73 1.17-.52 2.61-.91 4.24-.91zM4 13c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2zm1.13 1.1c-.37-.06-.74-.1-1.13-.1-.99 0-1.93.21-2.78.58C.48 14.9 0 15.62 0 16.43V18h4.5v-1.61c0-.83.23-1.61.63-2.29zM20 13c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2zm4 3.43c0-.81-.48-1.53-1.22-1.85-.85-.37-1.79-.58-2.78-.58-.39 0-.76.04-1.13.1.4.68.63 1.46.63 2.29V18H24v-1.57zm-7.76-2.78c-1.17-.52-2.61-.91-4.24-.91s-3.07.39-4.24.91C6.68 14.54 6 15.62 6 16.79V18h12v-1.21c0-1.17-.68-2.25-1.76-2.72zM8.07 7.93c.79-.79 2.08-1.07 3.43-1.07s2.64.28 3.43 1.07c.79.79 1.07 2.08 1.07 3.43s-.28 2.64-1.07 3.43c-.79.79-2.08 1.07-3.43 1.07s-2.64-.28-3.43-1.07c-.79-.79-1.07-2.08-1.07-3.43s.28-2.64 1.07-3.43z"/>
                    <path d="M12 6c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2z"/>
                </svg>
                <span class="text-2xl font-extrabold text-gray-900 tracking-tight">
                    Equal<span class="text-[#5b7a9a]">Flow</span>
                </span>
            </div>

            <!-- Form Content -->
            @yield('content')
            
            <!-- Footer Links -->
            <div class="mt-8 pt-6 border-t border-gray-100 text-center">
                <p class="text-xs text-gray-400">
                    &copy; 2026 EqualFlow. All rights reserved.
                </p>
            </div>
        </div>
    </div>
</div>

</body>
</html>