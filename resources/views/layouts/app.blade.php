<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'EqualFlow')</title>

    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,400;14..32,500;14..32,600;14..32,700;14..32,800&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Tailwind (CDN) -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Alpine components registered BEFORE Alpine boots -->
    <script>
        document.addEventListener('alpine:init', () => {

            // ==========================================================
            // Notification page component (used by notifications/index)
            // x-data="notificationApp"
            // ==========================================================
            Alpine.data('notificationApp', () => ({
                notifications: [],
                unreadCount: 0,
                total: 0,
                readCount: 0,
                loading: true,
                loadingMore: false,
                hasMore: true,
                offset: 0,
                limit: 20,
                search: '',
                filterType: 'all',
                filterStatus: 'all',

                init() {
                    this.loadNotifications();
                },

                async loadNotifications() {
                    this.loading = true;
                    try {
                        const res = await fetch(`/notifications/data?limit=${this.limit}`, {
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest',
                            },
                            credentials: 'same-origin',
                        });
                        const data = await res.json();
                        if (data.success) {
                            this.notifications = data.data;
                            this.unreadCount = data.unread_count;
                            this.total = data.total;
                            this.readCount = Math.max(this.total - this.unreadCount, 0);
                            this.hasMore = data.data.length === this.limit;
                        }
                    } catch (err) {
                        console.error('Failed to load notifications', err);
                    }
                    this.loading = false;
                },

                async loadMore() {
                    if (this.loadingMore) return;
                    this.loadingMore = true;
                    const nextOffset = this.offset + this.limit;
                    try {
                        const res = await fetch(`/notifications/data?limit=${this.limit}&offset=${nextOffset}`, {
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest',
                            },
                            credentials: 'same-origin',
                        });
                        const data = await res.json();
                        if (data.success) {
                            this.notifications = [...this.notifications, ...data.data];
                            this.offset = nextOffset;
                            this.hasMore = data.data.length === this.limit;
                        }
                    } catch (err) {
                        console.error('Failed to load more', err);
                    }
                    this.loadingMore = false;
                },

                async markAsRead(id) {
                    try {
                        const res = await fetch(`/notifications/${id}/read`, {
                            method: 'POST',
                            headers: {
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            },
                            credentials: 'same-origin',
                        });
                        const data = await res.json();
                        if (data.success) {
                            this.notifications = this.notifications.map(n =>
                                n.id === id ? { ...n, is_read: true } : n
                            );
                            this.unreadCount = Math.max(this.unreadCount - 1, 0);
                            this.readCount = Math.max(this.total - this.unreadCount, 0);
                            window.dispatchEvent(new CustomEvent('notification-updated'));
                        }
                    } catch (err) {
                        console.error('Failed to mark as read', err);
                    }
                },

                async markAllAsRead() {
                    try {
                        const res = await fetch('/notifications/mark-all-read', {
                            method: 'POST',
                            headers: {
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            },
                            credentials: 'same-origin',
                        });
                        const data = await res.json();
                        if (data.success) {
                            this.notifications = this.notifications.map(n => ({ ...n, is_read: true }));
                            this.unreadCount = 0;
                            this.readCount = this.total;
                            window.dispatchEvent(new CustomEvent('notification-updated'));
                        }
                    } catch (err) {
                        console.error('Failed to mark all as read', err);
                    }
                },

                async deleteNotification(id) {
                    if (!confirm('Delete this notification?')) return;
                    try {
                        const res = await fetch(`/notifications/${id}`, {
                            method: 'DELETE',
                            headers: {
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            },
                            credentials: 'same-origin',
                        });
                        const data = await res.json();
                        if (data.success) {
                            this.notifications = this.notifications.filter(n => n.id !== id);
                            this.total = Math.max(this.total - 1, 0);
                            this.readCount = Math.max(this.total - this.unreadCount, 0);
                            window.dispatchEvent(new CustomEvent('notification-updated'));
                        }
                    } catch (err) {
                        console.error('Failed to delete', err);
                    }
                },

                iconClass(type) {
                    const map = {
                        task_assigned: 'fa-tasks text-blue-600',
                        task_submitted: 'fa-upload text-purple-600',
                        task_reviewed: 'fa-check-circle text-green-600',
                        task_completed: 'fa-check-double text-emerald-600',
                        task_overdue: 'fa-exclamation-triangle text-red-600',
                        project_created: 'fa-folder-plus text-indigo-600',
                        member_invited: 'fa-user-plus text-cyan-600',
                        member_joined: 'fa-user-check text-green-600',
                        comment_added: 'fa-comment text-yellow-600',
                        mention: 'fa-at text-pink-600',
                        system: 'fa-bell text-gray-500',
                    };
                    return map[type] || 'fa-bell text-gray-500';
                },

                iconBg(type) {
                    const map = {
                        task_assigned: 'bg-blue-100',
                        task_submitted: 'bg-purple-100',
                        task_reviewed: 'bg-green-100',
                        task_completed: 'bg-emerald-100',
                        task_overdue: 'bg-red-100',
                        project_created: 'bg-indigo-100',
                        member_invited: 'bg-cyan-100',
                        member_joined: 'bg-green-100',
                        comment_added: 'bg-yellow-100',
                        mention: 'bg-pink-100',
                        system: 'bg-gray-100',
                    };
                    return map[type] || 'bg-gray-100';
                },

                get filteredNotifications() {
                    let filtered = this.notifications;

                    if (this.search) {
                        const q = this.search.toLowerCase();
                        filtered = filtered.filter(n =>
                            (n.title || '').toLowerCase().includes(q) ||
                            (n.message || '').toLowerCase().includes(q)
                        );
                    }

                    if (this.filterType !== 'all') {
                        filtered = filtered.filter(n => n.type === this.filterType);
                    }

                    if (this.filterStatus === 'unread') {
                        filtered = filtered.filter(n => !n.is_read);
                    } else if (this.filterStatus === 'read') {
                        filtered = filtered.filter(n => n.is_read);
                    }

                    return filtered;
                },
            }));

            // ==========================================================
            // Notification bell component (used by partials/navbar)
            // x-data="notificationBell"
            // ==========================================================
            Alpine.data('notificationBell', () => ({
                open: false,
                notifications: [],
                unreadCount: 0,
                loading: false,
                pollHandle: null,

                init() {
                    // 1. Fetch immediately so the badge appears on page load
                    this.loadNotifications();

                    // 2. Re-fetch when another component signals an update
                    window.addEventListener('notification-updated', () => {
                        this.loadNotifications();
                    });

                    // 3. Poll every 10s to catch notifications from other users
                    this.pollHandle = setInterval(() => this.loadNotifications(), 10000);
                },

                destroy() {
                    if (this.pollHandle) {
                        clearInterval(this.pollHandle);
                        this.pollHandle = null;
                    }
                },

                toggle() {
                    this.open = !this.open;
                    if (this.open) {
                        this.loadNotifications();
                    }
                },

                async loadNotifications() {
                    // Only show the spinner on first load — background polls stay silent
                    if (this.notifications.length === 0) {
                        this.loading = true;
                    }
                    try {
                        const res = await fetch('/notifications/data?limit=10', {
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest',
                            },
                            credentials: 'same-origin',
                        });
                        const data = await res.json();
                        if (data.success) {
                            this.notifications = data.data;
                            this.unreadCount = data.unread_count;
                        }
                    } catch (err) {
                        console.error('Failed to load notifications', err);
                    }
                    this.loading = false;
                },

                async markAsRead(id) {
                    try {
                        await fetch(`/notifications/${id}/read`, {
                            method: 'POST',
                            headers: {
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            },
                            credentials: 'same-origin',
                        });
                        this.notifications = this.notifications.map(n =>
                            n.id === id ? { ...n, is_read: true } : n
                        );
                        this.unreadCount = Math.max(this.unreadCount - 1, 0);
                        window.dispatchEvent(new CustomEvent('notification-updated'));
                    } catch (err) {
                        console.error('Failed to mark as read', err);
                    }
                },

                async markAllAsRead() {
                    try {
                        await fetch('/notifications/mark-all-read', {
                            method: 'POST',
                            headers: {
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            },
                            credentials: 'same-origin',
                        });
                        this.notifications = this.notifications.map(n => ({ ...n, is_read: true }));
                        this.unreadCount = 0;
                        window.dispatchEvent(new CustomEvent('notification-updated'));
                    } catch (err) {
                        console.error('Failed to mark all as read', err);
                    }
                },

                iconClass(type) {
                    const map = {
                        task_assigned: 'fa-tasks text-blue-500',
                        task_submitted: 'fa-upload text-purple-500',
                        task_reviewed: 'fa-check-circle text-green-500',
                        task_completed: 'fa-check-double text-emerald-500',
                        task_overdue: 'fa-exclamation-triangle text-red-500',
                        project_created: 'fa-folder-plus text-indigo-500',
                        member_invited: 'fa-user-plus text-cyan-500',
                        member_joined: 'fa-user-check text-green-500',
                        comment_added: 'fa-comment text-yellow-500',
                        mention: 'fa-at text-pink-500',
                        system: 'fa-bell text-gray-500',
                    };
                    return map[type] || 'fa-bell text-gray-500';
                },

                iconBg(type) {
                    const map = {
                        task_assigned: 'bg-blue-100',
                        task_submitted: 'bg-purple-100',
                        task_reviewed: 'bg-green-100',
                        task_completed: 'bg-emerald-100',
                        task_overdue: 'bg-red-100',
                        project_created: 'bg-indigo-100',
                        member_invited: 'bg-cyan-100',
                        member_joined: 'bg-green-100',
                        comment_added: 'bg-yellow-100',
                        mention: 'bg-pink-100',
                        system: 'bg-gray-100',
                    };
                    return map[type] || 'bg-gray-100';
                },
            }));
        });
    </script>

    <!-- Alpine.js (must load AFTER the alpine:init listener above) -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.3/dist/cdn.min.js"></script>

    <style>
        * { font-family: 'Inter', sans-serif; }

        /* ===== Tinted blue page background ===== */
        body {
            background: linear-gradient(180deg, #e8f1fc 0%, #eff6ff 40%, #f8fbff 100%);
            background-attachment: fixed;
            min-height: 100vh;
        }

        .stat-card:hover { transform: translateY(-2px); transition: all 0.2s ease; }
        .sidebar { transition: transform 0.3s ease-in-out; }
        .sidebar-hidden { transform: translateX(-100%); }
        @media (min-width: 1024px) {
            .sidebar-hidden { transform: translateX(0); }
        }

        @keyframes pulse-dot {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.2); }
        }
        .pulse-dot {
            animation: pulse-dot 2s infinite;
        }

        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .notification-scroll::-webkit-scrollbar {
            width: 4px;
        }
        .notification-scroll::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }
        .notification-scroll::-webkit-scrollbar-thumb {
            background: #d1d5db;
            border-radius: 10px;
        }
        .notification-scroll::-webkit-scrollbar-thumb:hover {
            background: #9ca3af;
        }

        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="text-gray-900 antialiased">

<div class="flex min-h-screen">

    <!-- Sidebar -->
    @include('partials.sidebar')

    <!-- Main Content -->
    <div class="flex-1 flex flex-col min-h-screen">

        <!-- Navbar -->
        @include('partials.navbar')

        <!-- Page Content -->
        <main class="flex-1 p-6 md:p-8">
            @if(session('success'))
                <div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-700 rounded-xl flex items-center gap-2">
                    <i class="fas fa-check-circle text-green-500"></i>
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-700 rounded-xl flex items-center gap-2">
                    <i class="fas fa-exclamation-circle text-red-500"></i>
                    {{ session('error') }}
                </div>
            @endif

            @if(session('warning'))
                <div class="mb-6 p-4 bg-yellow-50 border border-yellow-200 text-yellow-700 rounded-xl flex items-center gap-2">
                    <i class="fas fa-exclamation-triangle text-yellow-500"></i>
                    {{ session('warning') }}
                </div>
            @endif

            @if(session('info'))
                <div class="mb-6 p-4 bg-blue-50 border border-blue-200 text-blue-700 rounded-xl flex items-center gap-2">
                    <i class="fas fa-info-circle text-blue-500"></i>
                    {{ session('info') }}
                </div>
            @endif

            @yield('content')
        </main>
    </div>
</div>

<!-- Mobile Sidebar Toggle + Flash Auto-dismiss -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const toggle = document.getElementById('sidebarToggle');
        const sidebar = document.getElementById('sidebar');

        if (toggle && sidebar) {
            toggle.addEventListener('click', function() {
                sidebar.classList.toggle('sidebar-hidden');
            });
        }

        // Auto-dismiss flash messages after 5 seconds
        const flashMessages = document.querySelectorAll('.bg-green-50, .bg-red-50, .bg-yellow-50, .bg-blue-50');
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

<!-- If the previous request created a notification, refresh the bell immediately -->
@if(session('notify_refresh'))
<script>
    window.addEventListener('DOMContentLoaded', () => {
        window.dispatchEvent(new CustomEvent('notification-updated'));
    });
</script>
@endif

</body>
</html>