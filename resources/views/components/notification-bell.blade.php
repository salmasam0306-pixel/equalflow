{{-- resources/views/components/notification-bell.blade.php --}}
@props(['count' => 0])

<div x-data="notificationBell()"
     x-init="init()"
     @click.outside="open = false"
     @notification-updated.window="loadNotifications()"
     class="relative">

    <!-- Bell Button -->
    <button @click="toggle()"
            class="relative p-2 text-gray-400 hover:text-gray-600 transition rounded-lg hover:bg-gray-100"
            aria-label="Notifications">
        <i class="fas fa-bell text-xl"></i>
        <span x-show="unreadCount > 0"
              x-transition
              x-cloak
              class="absolute -top-1 -right-1 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white bg-red-500 rounded-full min-w-5 h-5 pulse-dot">
            <span x-text="unreadCount > 99 ? '99+' : unreadCount"></span>
        </span>
    </button>

    <!-- Dropdown -->
    <div x-show="open"
         x-cloak
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95 translate-y-2"
         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 scale-100 translate-y-0"
         x-transition:leave-end="opacity-0 scale-95 translate-y-2"
         class="absolute right-0 mt-2 w-96 bg-white rounded-xl shadow-2xl border border-gray-200 overflow-hidden z-50">

        <!-- Header -->
        <div class="flex items-center justify-between px-4 py-3 border-b border-gray-200 bg-gray-50">
            <h3 class="font-semibold text-gray-900 text-sm flex items-center gap-2">
                <i class="fas fa-bell text-[#1a2a4a]"></i>
                Notifications
                <span x-show="unreadCount > 0"
                      class="text-xs bg-red-500 text-white px-2 py-0.5 rounded-full"
                      x-text="unreadCount"></span>
            </h3>
            <div class="flex items-center gap-2">
                <button x-show="unreadCount > 0"
                        @click="markAllAsRead()"
                        class="text-xs text-[#1a2a4a] hover:text-[#0f1a30] font-medium transition">
                    Mark all read
                </button>
                <a href="{{ route('notifications.index') }}"
                   class="text-xs text-gray-400 hover:text-gray-600 transition"
                   title="View all">
                    <i class="fas fa-external-link-alt"></i>
                </a>
            </div>
        </div>

        <!-- Loading -->
        <div x-show="loading" class="flex items-center justify-center py-8">
            <i class="fas fa-spinner fa-spin text-2xl text-gray-400"></i>
            <span class="ml-2 text-sm text-gray-400">Loading...</span>
        </div>

        <!-- Notifications List -->
        <div x-show="!loading" class="overflow-y-auto max-h-96 notification-scroll">
            <template x-if="notifications.length === 0">
                <div class="text-center py-8">
                    <i class="fas fa-inbox text-4xl text-gray-300 mb-3 block"></i>
                    <p class="text-sm text-gray-400">No notifications yet</p>
                    <p class="text-xs text-gray-300 mt-1">You're all caught up! 🎉</p>
                </div>
            </template>

            <template x-for="notification in notifications" :key="notification.id">
                <div @click="!notification.is_read ? markAsRead(notification.id) : null"
                     class="flex items-start gap-3 px-4 py-3 border-b border-gray-100 hover:bg-gray-50 transition cursor-pointer group"
                     :class="notification.is_read ? 'bg-white' : 'bg-blue-50/30'">

                    <!-- Icon -->
                    <div class="flex-shrink-0 mt-0.5">
                        <div class="w-9 h-9 rounded-full flex items-center justify-center text-lg"
                             :class="notification.color || 'bg-gray-100'">
                            <i :class="getIcon(notification.type)"></i>
                        </div>
                    </div>

                    <!-- Content -->
                    <div class="flex-1 min-w-0">
                        <div class="flex items-start justify-between gap-2">
                            <p class="text-sm font-medium text-gray-900 truncate"
                               x-text="notification.title"></p>
                            <span class="text-xs text-gray-400 flex-shrink-0 whitespace-nowrap"
                                  x-text="notification.time_ago || 'Just now'"></span>
                        </div>
                        <p class="text-xs text-gray-500 line-clamp-2"
                           x-text="notification.message"></p>
                        <a x-show="notification.link"
                           :href="notification.link"
                           class="text-xs text-[#1a2a4a] hover:text-[#0f1a30] font-medium mt-1 inline-block group-hover:underline">
                            View Details →
                        </a>
                    </div>

                    <!-- Unread Indicator -->
                    <div x-show="!notification.is_read"
                         class="flex-shrink-0 w-2 h-2 mt-2 bg-blue-500 rounded-full pulse-dot"></div>
                </div>
            </template>
        </div>

        <!-- Footer -->
        <div class="px-4 py-2 border-t border-gray-200 bg-gray-50 text-center">
            <a href="{{ route('notifications.index') }}"
               class="text-xs text-[#1a2a4a] hover:text-[#0f1a30] font-medium transition">
                View all notifications <i class="fas fa-arrow-right ml-1"></i>
            </a>
        </div>
    </div>
</div>

<script>
function notificationBell() {
    return {
        open: false,
        notifications: [],
        unreadCount: {{ (int) $count }},
        loading: false,
        pollHandle: null,

        csrf() {
            return document.querySelector('meta[name="csrf-token"]')?.content ?? '';
        },

        init() {
            // 1. Fetch immediately so the badge is correct on page load
            this.loadNotifications();

            // 2. Re-fetch when another component signals an update
            window.addEventListener('notification-updated', () => {
                this.loadNotifications();
            });

            // 3. Poll every 30s to catch notifications from other sessions/users
            this.pollHandle = setInterval(() => this.loadNotifications(), 30000);
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
            // Don't flash the spinner on background polls — only on first load or when opening
            if (this.notifications.length === 0) {
                this.loading = true;
            }
            try {
                const response = await fetch('/notifications/data?limit=10', {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    credentials: 'same-origin',
                });
                const data = await response.json();
                if (data.success) {
                    this.notifications = data.data;
                    this.unreadCount = data.unread_count;
                }
            } catch (error) {
                console.error('Error loading notifications:', error);
            }
            this.loading = false;
        },

        async markAsRead(id) {
            try {
                await fetch(`/notifications/${id}/read`, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': this.csrf(),
                    },
                    credentials: 'same-origin',
                });
                this.notifications = this.notifications.map(n =>
                    n.id === id ? { ...n, is_read: true } : n
                );
                this.unreadCount = Math.max(this.unreadCount - 1, 0);
                window.dispatchEvent(new CustomEvent('notification-updated'));
            } catch (error) {
                console.error('Error marking as read:', error);
            }
        },

        async markAllAsRead() {
            try {
                await fetch('/notifications/mark-all-read', {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': this.csrf(),
                    },
                    credentials: 'same-origin',
                });
                this.notifications = this.notifications.map(n => ({ ...n, is_read: true }));
                this.unreadCount = 0;
                window.dispatchEvent(new CustomEvent('notification-updated'));
            } catch (error) {
                console.error('Error marking all as read:', error);
            }
        },

        getIcon(type) {
            const icons = {
                'task_assigned': 'fa-tasks text-blue-500',
                'task_submitted': 'fa-upload text-purple-500',
                'task_reviewed': 'fa-check-circle text-green-500',
                'task_completed': 'fa-check-double text-emerald-500',
                'task_overdue': 'fa-exclamation-triangle text-red-500',
                'project_created': 'fa-folder-plus text-indigo-500',
                'member_invited': 'fa-user-plus text-cyan-500',
                'member_joined': 'fa-user-check text-green-500',
                'comment_added': 'fa-comment text-yellow-500',
                'mention': 'fa-at text-pink-500',
                'system': 'fa-bell text-gray-500'
            };
            return icons[type] || 'fa-bell text-gray-500';
        }
    }
}
</script>