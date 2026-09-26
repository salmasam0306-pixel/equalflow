<nav class="bg-white border-b border-gray-100 px-6 py-4 sticky top-0 z-30 shadow-sm">
    <div class="flex items-center justify-between">
        <!-- Left Section -->
        <div class="flex items-center gap-4">
            <!-- Mobile Sidebar Toggle -->
            <button id="sidebarToggle" class="lg:hidden p-2 rounded-lg hover:bg-gray-100 transition">
                <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                </svg>
            </button>

            <!-- Breadcrumb / Page Title -->
            <div class="hidden sm:block">
                <h1 class="text-lg font-semibold text-gray-800">
                    @yield('page-title', 'Dashboard')
                </h1>
                <p class="text-xs text-gray-400 hidden md:block">
                    {{ auth()->user()->name }}
                    <span class="text-gray-300">•</span>
                    <span class="text-gray-400">{{ ucfirst(auth()->user()->role ?? 'member') }}</span>
                </p>
            </div>
        </div>

        <!-- Center: Logo (mobile only) -->
        <a href="{{ route('dashboard') }}" class="text-xl font-extrabold text-gray-900 lg:hidden">
            Equal<span class="text-[#1a2a4a]">Flow</span>
        </a>

        <!-- Right Section -->
        <div class="flex items-center gap-3">
            <!-- Date -->
            <span class="text-sm text-gray-400 hidden lg:inline">
                {{ now()->format('l, d M Y') }}
            </span>

            <!-- Notification Bell -->
            <x-notification-bell :count="auth()->user()->unreadNotificationsCount()" />

            <!-- User Dropdown -->
            <div class="relative" x-data="{ open: false }">
                <button @click="open = !open" 
                        class="flex items-center gap-2 text-sm text-gray-700 hover:text-gray-900 focus:outline-none group">
                    <img src="{{ auth()->user()->getProfilePictureUrl() }}" 
                         alt="{{ auth()->user()->name }}" 
                         class="w-9 h-9 rounded-full object-cover border-2 border-gray-200 group-hover:border-[#1a2a4a] transition">
                    <span class="hidden md:inline font-medium">{{ auth()->user()->name }}</span>
                    <i class="fas fa-chevron-down text-xs text-gray-400 hidden md:inline"></i>
                </button>

                <!-- Dropdown Menu -->
                <div x-show="open" 
                     @click.away="open = false"
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 scale-95 translate-y-2"
                     x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                     x-transition:leave-end="opacity-0 scale-95 translate-y-2"
                     class="absolute right-0 mt-2 w-56 bg-white rounded-xl shadow-lg border border-gray-200 py-1 z-50">
                    
                    <!-- User Info -->
                    <div class="px-4 py-3 border-b border-gray-100">
                        <p class="text-sm font-semibold text-gray-900">{{ auth()->user()->name }}</p>
                        <p class="text-xs text-gray-400">{{ auth()->user()->email }}</p>
                        <span class="inline-block mt-1 text-xs px-2 py-0.5 rounded-full bg-{{ auth()->user()->getStatusColor() }}-100 text-{{ auth()->user()->getStatusColor() }}-700">
                            <i class="fas {{ auth()->user()->getStatusIcon() }} mr-1"></i>
                            {{ auth()->user()->getStatusLabel() }}
                        </span>
                    </div>
                    
                    <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition">
                        <i class="fas fa-user mr-2 text-blue-500"></i> My Profile
                    </a>
                    
                    <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition">
                        <i class="fas fa-cog mr-2 text-gray-500"></i> Settings
                    </a>
                    
                    <a href="{{ route('dashboard') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition">
                        <i class="fas fa-tachometer-alt mr-2 text-green-500"></i> Dashboard
                    </a>
                    
                    <hr class="my-1 border-gray-200">
                    
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition">
                            <i class="fas fa-sign-out-alt mr-2"></i> Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</nav>