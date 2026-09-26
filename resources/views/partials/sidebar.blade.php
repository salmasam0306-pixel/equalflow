<aside id="sidebar" class="sidebar sidebar-hidden fixed lg:sticky top-0 left-0 z-40 w-64 h-screen bg-[#1a2a4a] flex-shrink-0 overflow-y-auto transition-all duration-300">
    <div class="flex flex-col h-full p-5">
        <!-- ===== LOGO ===== -->
        <div class="mb-6">
            <a href="{{ route('dashboard') }}" class="text-2xl font-extrabold text-white flex items-center gap-2">
                <svg class="w-7 h-7 text-indigo-300" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"/>
                </svg>
                Equal<span class="text-indigo-300">Flow</span>
            </a>
        </div>

        @php
            $user = auth()->user();
            $org = $user->currentOrganization();
        @endphp

        <!-- ===== ORGANISATION PROFILE (if in an organisation) ===== -->
        @if($org)
            <div class="bg-white/5 rounded-xl p-3 mb-5 border border-white/10 hover:bg-white/10 transition-all duration-200">
                <div class="flex items-center gap-3">
                    <img src="{{ $org->getLogoOrFallback() }}"
                         alt="{{ $org->name }}"
                         class="w-10 h-10 rounded-lg object-cover border border-white/20 flex-shrink-0">
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-white truncate">{{ $org->name }}</p>
                        <p class="text-[0.55rem] text-gray-400 truncate">{{ $org->industry ?? 'Organisation' }}</p>
                    </div>
                </div>
            </div>
        @endif

        <!-- ===== NAVIGATION ===== -->
        <nav class="flex-1 space-y-0.5 overflow-y-auto">

            <!-- Dashboard -->
            <a href="{{ route('dashboard') }}"
               class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-white/60 hover:text-white hover:bg-white/5 transition-all duration-200 {{ request()->routeIs('dashboard') ? 'bg-white/10 text-white' : '' }}">
                <i class="fas fa-chart-pie w-5 text-center text-current"></i>
                <span class="text-sm font-medium">Dashboard</span>
            </a>

            <!-- Divider -->
            <div class="border-t border-white/10 my-2.5"></div>

            <!-- ===== PERSONAL PROJECTS ===== -->
            <p class="text-[0.55rem] text-gray-400 uppercase tracking-wider font-semibold mt-3 mb-2 px-3.5">
                <i class="fas fa-user text-indigo-300 mr-1.5"></i> Personal
            </p>

            <!-- My Projects -->
            <a href="{{ route('personal.index') }}"
               class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-white/60 hover:text-white hover:bg-white/5 transition-all duration-200 {{ request()->routeIs('personal.index') || request()->routeIs('personal.show') || request()->routeIs('personal.edit') || request()->routeIs('personal.invite') ? 'bg-white/10 text-white' : '' }}">
                <i class="fas fa-user-circle w-5 text-center text-current"></i>
                <span class="text-sm font-medium">My Projects</span>
            </a>

            <!-- Create Project (personal) -->
            <a href="{{ route('personal.create') }}"
               class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-white/60 hover:text-white hover:bg-white/5 transition-all duration-200 {{ request()->routeIs('personal.create') ? 'bg-white/10 text-white' : '' }}">
                <i class="fas fa-plus-circle w-5 text-center text-current"></i>
                <span class="text-sm font-medium">Create Project</span>
            </a>

            <!-- Join Project (personal) -->
            <a href="{{ route('personal.join-page') }}"
               class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-white/60 hover:text-white hover:bg-white/5 transition-all duration-200 {{ request()->routeIs('personal.join-page') || request()->routeIs('personal.join-by-code') ? 'bg-white/10 text-white' : '' }}">
                <i class="fas fa-user-plus w-5 text-center text-current"></i>
                <span class="text-sm font-medium">Join Project</span>
            </a>

            <!-- Divider -->
            <div class="border-t border-white/10 my-2.5"></div>

            <!-- ===== ORGANISATION SECTION ===== -->
            @if($org)
                <p class="text-[0.55rem] text-gray-400 uppercase tracking-wider font-semibold mt-3 mb-2 px-3.5">
                    <i class="fas fa-building text-indigo-300 mr-1.5"></i> Organisation
                </p>

                <a href="{{ route('departments.index') }}"
                   class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-white/60 hover:text-white hover:bg-white/5 transition-all duration-200 {{ request()->routeIs('departments.*') ? 'bg-white/10 text-white' : '' }}">
                    <i class="fas fa-users w-5 text-center text-current"></i>
                    <span class="text-sm font-medium">Departments</span>
                </a>

                <!-- Projects -->
                <a href="{{ route('projects.index') }}"
                   class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-white/60 hover:text-white hover:bg-white/5 transition-all duration-200 {{ request()->routeIs('projects.index') || request()->routeIs('projects.show') || request()->routeIs('projects.edit') || request()->routeIs('projects.report') ? 'bg-white/10 text-white' : '' }}">
                    <i class="fas fa-building w-5 text-center text-current"></i>
                    <span class="text-sm font-medium">Projects</span>
                </a>

                @if($user->isPrincipal())
                    <!-- Create Project (organisation) -->
                    <a href="{{ route('projects.create') }}"
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-white/60 hover:text-white hover:bg-white/5 transition-all duration-200 {{ request()->routeIs('projects.create') ? 'bg-white/10 text-white' : '' }}">
                        <i class="fas fa-hard-hat w-5 text-center text-current"></i>
                        <span class="text-sm font-medium">Create Project</span>
                    </a>

                    <!-- Settings -->
                    <a href="{{ route('company.settings') }}"
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-white/60 hover:text-white hover:bg-white/5 transition-all duration-200 {{ request()->routeIs('company.settings') || request()->routeIs('company.settings.update') || request()->routeIs('company.settings.regenerate-invite') ? 'bg-white/10 text-white' : '' }}">
                        <i class="fas fa-cog w-5 text-center text-current"></i>
                        <span class="text-sm font-medium">Settings</span>
                    </a>
                @endif

            @else
                <!-- ===== NO ORGANISATION ===== -->
                <p class="text-[0.55rem] text-gray-400 uppercase tracking-wider font-semibold mt-3 mb-2 px-3.5">
                    <i class="fas fa-building text-indigo-300 mr-1.5"></i> Organisation
                </p>

                <a href="{{ route('company.create') }}"
                   class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-white/60 hover:text-white hover:bg-white/5 transition-all duration-200 {{ request()->routeIs('company.create') || request()->routeIs('company.store') ? 'bg-white/10 text-white' : '' }}">
                    <i class="fas fa-plus-circle w-5 text-center text-current"></i>
                    <span class="text-sm font-medium">Create Organisation</span>
                </a>

                <a href="{{ route('company.join') }}"
                   class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-white/60 hover:text-white hover:bg-white/5 transition-all duration-200 {{ request()->routeIs('company.join') || request()->routeIs('company.join.submit') ? 'bg-white/10 text-white' : '' }}">
                    <i class="fas fa-link w-5 text-center text-current"></i>
                    <span class="text-sm font-medium">Join Organisation</span>
                </a>
            @endif

            {{-- AI Status --}}
            <div class="mt-4 pt-4 border-t border-white/10">
                @php
                    $aiService = app(\App\Services\AIService::class);
                    $aiRunning = $aiService->isRunning();
                @endphp
                <div class="flex items-center gap-2 px-4 py-2 rounded-lg {{ $aiRunning ? 'bg-green-500/10' : 'bg-red-500/10' }}">
                    <span class="w-2 h-2 rounded-full {{ $aiRunning ? 'bg-green-500 animate-pulse' : 'bg-red-500' }}"></span>
                    <span class="text-xs text-white/70">
                        AI: {{ $aiRunning ? 'Connected' : 'Offline' }}
                    </span>
                    @if(!$aiRunning)
                        <button onclick="window.location.href='{{ route('ai.start') }}'"
                                class="text-xs text-indigo-300 hover:text-indigo-200 ml-auto">
                            Start
                        </button>
                    @endif
                </div>
            </div>

        </nav>

        <!-- ===== BOTTOM SECTION ===== -->
        <div class="pt-4 border-t border-white/10 mt-3">
            <!-- User Profile -->
            <div class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-white/60 hover:text-white hover:bg-white/5 transition-all duration-200 group">
                <img src="{{ auth()->user()->getProfilePictureUrl() }}"
                     alt="Profile"
                     class="w-9 h-9 rounded-full border-2 border-white/20 object-cover flex-shrink-0 group-hover:border-indigo-300 transition-all duration-200">
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-white/90 truncate">{{ auth()->user()->name }}</p>
                    <p class="text-[0.55rem] text-gray-400 truncate capitalize">{{ auth()->user()->role }}</p>
                </div>
                <div class="flex-shrink-0">
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[0.5rem] font-medium bg-{{ auth()->user()->getStatusColor() }}-500/20 text-{{ auth()->user()->getStatusColor() }}-300 border border-{{ auth()->user()->getStatusColor() }}-500/30">
                        <i class="fas {{ auth()->user()->getStatusIcon() }} text-[0.4rem]"></i>
                        {{ auth()->user()->getStatusLabel() }}
                    </span>
                </div>
            </div>

            <!-- Profile Settings -->
            <a href="{{ route('profile.edit') }}"
               class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-white/60 hover:text-white hover:bg-white/5 transition-all duration-200 {{ request()->routeIs('profile.*') || request()->routeIs('status.*') ? 'bg-white/10 text-white' : '' }}">
                <i class="fas fa-user-cog w-5 text-center text-current"></i>
                <span class="text-sm font-medium">Profile Settings</span>
            </a>

            <!-- Logout -->
            <form method="POST" action="{{ route('logout') }}" class="mt-0.5">
                @csrf
                <button type="submit"
                        class="w-full flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-red-400/70 hover:text-red-400 hover:bg-red-500/10 transition-all duration-200">
                    <i class="fas fa-sign-out-alt w-5 text-center text-current"></i>
                    <span class="text-sm font-medium">Logout</span>
                </button>
            </form>
        </div>
    </div>
</aside>

<!-- ===== MOBILE OVERLAY ===== -->
<div id="sidebarOverlay" class="fixed inset-0 bg-black/50 z-30 hidden lg:hidden backdrop-blur-sm transition-opacity duration-300" onclick="toggleSidebar()"></div>

<!-- ===== MOBILE TOGGLE BUTTON ===== -->
<button id="sidebarToggle"
        class="lg:hidden fixed bottom-6 right-6 z-50 w-12 h-12 bg-[#1a2a4a] hover:bg-[#0f1a30] text-white rounded-full shadow-lg hover:shadow-xl transition-all duration-300 flex items-center justify-center border border-white/10">
    <i class="fas fa-bars text-lg"></i>
</button>

<!-- ===== SCRIPTS ===== -->
<script>
function toggleSidebar() {
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebarOverlay');

    sidebar.classList.toggle('sidebar-hidden');
    overlay.classList.toggle('hidden');

    const toggleBtn = document.getElementById('sidebarToggle');
    if (toggleBtn) {
        const icon = toggleBtn.querySelector('i');
        if (sidebar.classList.contains('sidebar-hidden')) {
            icon.className = 'fas fa-bars text-lg';
        } else {
            icon.className = 'fas fa-times text-lg';
        }
    }
}

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        const sidebar = document.getElementById('sidebar');
        if (!sidebar.classList.contains('sidebar-hidden')) {
            toggleSidebar();
        }
    }
});

document.addEventListener('DOMContentLoaded', function() {
    const sidebarLinks = document.querySelectorAll('#sidebar nav a, #sidebar .pt-4 a, #sidebar .pt-4 button');
    const sidebar = document.getElementById('sidebar');

    sidebarLinks.forEach(link => {
        link.addEventListener('click', function() {
            if (window.innerWidth < 1024) {
                if (!sidebar.classList.contains('sidebar-hidden')) {
                    toggleSidebar();
                }
            }
        });
    });
});

function copyToClipboard(text, message) {
    navigator.clipboard.writeText(text).then(function() {
        showToast('✅ ' + message, 'success');
    }).catch(function() {
        const input = document.createElement('input');
        input.value = text;
        document.body.appendChild(input);
        input.select();
        document.execCommand('copy');
        document.body.removeChild(input);
        showToast('✅ ' + message, 'success');
    });
}

function showToast(message, type = 'success') {
    const toast = document.createElement('div');
    const colors = {
        success: 'bg-green-500',
        error: 'bg-red-500',
        info: 'bg-blue-500'
    };
    toast.className = `fixed bottom-24 right-6 ${colors[type] || 'bg-green-500'} text-white px-4 py-2.5 rounded-lg shadow-lg text-sm z-50 transition-opacity duration-300 flex items-center gap-2`;
    toast.innerHTML = message;
    document.body.appendChild(toast);

    setTimeout(() => {
        toast.style.opacity = '0';
        setTimeout(() => {
            toast.remove();
        }, 300);
    }, 3000);
}
</script>

<!-- ===== STYLES ===== -->
<style>
.sidebar {
    transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    transform: translateX(0);
}
.sidebar-hidden {
    transform: translateX(-100%);
}

#sidebar::-webkit-scrollbar { width: 4px; }
#sidebar::-webkit-scrollbar-track { background: transparent; }
#sidebar::-webkit-scrollbar-thumb {
    background: rgba(255, 255, 255, 0.15);
    border-radius: 4px;
}
#sidebar::-webkit-scrollbar-thumb:hover {
    background: rgba(255, 255, 255, 0.25);
}

#sidebar nav a.bg-white\/10 { position: relative; }
#sidebar nav a.bg-white\/10::before {
    content: '';
    position: absolute;
    left: 0;
    top: 50%;
    transform: translateY(-50%);
    width: 3px;
    height: 24px;
    background: linear-gradient(180deg, #818CF8, #A78BFA);
    border-radius: 0 4px 4px 0;
}

#sidebar nav a {
    position: relative;
    overflow: hidden;
}
#sidebar nav a::after {
    content: '';
    position: absolute;
    inset: 0;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.05), transparent);
    transform: translateX(-100%);
    transition: transform 0.6s ease;
}
#sidebar nav a:hover::after { transform: translateX(100%); }

#sidebarToggle {
    box-shadow: 0 0 0 0 rgba(26, 42, 74, 0.4);
    animation: pulse-ring 2s ease-in-out infinite;
}
@keyframes pulse-ring {
    0%   { box-shadow: 0 0 0 0 rgba(26, 42, 74, 0.4); }
    70%  { box-shadow: 0 0 0 12px rgba(26, 42, 74, 0); }
    100% { box-shadow: 0 0 0 0 rgba(26, 42, 74, 0); }
}

@media (max-width: 1024px) {
    .sidebar {
        transform: translateX(-100%);
        width: 280px;
    }
    .sidebar:not(.sidebar-hidden) { transform: translateX(0); }
    #sidebarToggle { display: flex !important; }
}
@media (min-width: 1024px) {
    #sidebarToggle { display: none !important; }
    .sidebar-hidden { transform: translateX(0) !important; }
}
</style>