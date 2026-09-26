{{-- resources/views/notifications/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Notifications')

@section('content')

@php
    $initialUnread = $unreadCount ?? 0;
    $initialTotal  = $total ?? 0;
@endphp

<div x-data="notificationApp()"
     x-init="loadNotifications()"
     class="max-w-4xl mx-auto px-4 py-6">

    {{-- Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 flex items-center gap-2">
                <span class="w-9 h-9 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-xl flex items-center justify-center text-white text-sm">
                    <i class="fas fa-bell"></i>
                </span>
                Notifications
            </h2>
            <p class="text-sm text-gray-500 mt-0.5">Stay updated with your activity</p>
        </div>
        <div class="flex items-center gap-2">
            <button x-show="unreadCount > 0"
                    @click="markAllAsRead()"
                    class="px-4 py-2 text-sm text-[#1a2a4a] hover:text-[#0f1a30] font-medium transition">
                Mark all as read
            </button>
            <a href="{{ route('profile.edit') }}"
               class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-lg transition">
                <i class="fas fa-cog mr-1"></i> Preferences
            </a>
        </div>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
        <div class="bg-white rounded-xl border border-gray-200 p-4 text-center">
            <p class="text-2xl font-bold text-gray-900" x-text="total"></p>
            <p class="text-xs text-gray-500 mt-0.5">Total</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-4 text-center">
            <p class="text-2xl font-bold text-blue-600" x-text="unreadCount"></p>
            <p class="text-xs text-gray-500 mt-0.5">Unread</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-4 text-center">
            <p class="text-2xl font-bold text-green-600" x-text="readCount"></p>
            <p class="text-xs text-gray-500 mt-0.5">Read</p>
        </div>
    </div>

    {{-- Filters --}}
    <div class="bg-white rounded-xl border border-gray-200 p-4 mb-6">
        <div class="flex flex-wrap items-center gap-3">
            <input type="text" x-model="search" placeholder="Search notifications..."
                   class="flex-1 min-w-[200px] px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#1a2a4a] focus:border-transparent text-sm">
            <select x-model="filterType"
                    class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#1a2a4a] focus:border-transparent bg-white text-sm">
                <option value="all">All Types</option>
                <option value="task_assigned">Task Assigned</option>
                <option value="task_submitted">Task Submitted</option>
                <option value="task_reviewed">Task Reviewed</option>
                <option value="task_completed">Task Completed</option>
                <option value="project_created">Project Created</option>
                <option value="comment_added">Comment Added</option>
                <option value="system">System</option>
            </select>
            <select x-model="filterStatus"
                    class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#1a2a4a] focus:border-transparent bg-white text-sm">
                <option value="all">All Status</option>
                <option value="unread">Unread</option>
                <option value="read">Read</option>
            </select>
        </div>
    </div>

    {{-- Loading --}}
    <div x-show="loading" class="text-center py-12">
        <i class="fas fa-spinner fa-spin text-3xl text-gray-400"></i>
        <p class="mt-2 text-gray-400 text-sm">Loading notifications...</p>
    </div>

    {{-- List --}}
    <div x-show="!loading" class="space-y-3">
        <template x-for="notification in filteredNotifications" :key="notification.id">
            <div class="bg-white rounded-xl border p-4 hover:shadow-md transition cursor-pointer"
                 :class="notification.is_read ? 'border-gray-200' : 'border-blue-200 bg-blue-50/30'"
                 @click="!notification.is_read ? markAsRead(notification.id) : null">

                <div class="flex items-start gap-4">
                    {{-- Icon --}}
                    <div class="flex-shrink-0">
                        <div class="w-10 h-10 rounded-full flex items-center justify-center"
                             :class="iconBg(notification.type)">
                            <i class="fas" :class="iconClass(notification.type)"></i>
                        </div>
                    </div>

                    {{-- Content --}}
                    <div class="flex-1 min-w-0">
                        <div class="flex items-start justify-between gap-2">
                            <h4 class="text-sm font-semibold text-gray-900" x-text="notification.title"></h4>
                            <span class="text-xs text-gray-400 flex-shrink-0"
                                  x-text="notification.time_ago || 'Just now'"></span>
                        </div>
                        <p class="text-sm text-gray-600 mt-1" x-text="notification.message"></p>
                        <div class="flex items-center gap-4 mt-2">
                            <a x-show="notification.link" :href="notification.link"
                               class="text-xs text-[#1a2a4a] hover:text-[#0f1a30] font-medium">
                                View Details →
                            </a>
                            <span x-show="!notification.is_read"
                                  class="text-xs text-blue-500 font-medium">
                                ● Unread
                            </span>
                            <span x-show="notification.sender"
                                  class="text-xs text-gray-400">
                                From: <span x-text="notification.sender?.name"></span>
                            </span>
                        </div>
                    </div>

                    {{-- Actions --}}
                    <div class="flex items-center gap-1 flex-shrink-0">
                        <button x-show="!notification.is_read"
                                @click.stop="markAsRead(notification.id)"
                                class="p-1 text-blue-500 hover:text-blue-700 transition"
                                title="Mark as read">
                            <i class="fas fa-check"></i>
                        </button>
                        <button @click.stop="deleteNotification(notification.id)"
                                class="p-1 text-red-400 hover:text-red-600 transition"
                                title="Delete">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </div>
                </div>
            </div>
        </template>

        {{-- Empty --}}
        <div x-show="filteredNotifications.length === 0"
             class="text-center py-12 bg-white rounded-xl border border-gray-200">
            <i class="fas fa-inbox text-5xl text-gray-300 mb-4 block"></i>
            <p class="text-gray-400 text-sm">No notifications found</p>
            <p class="text-xs text-gray-300 mt-1">Try adjusting your filters</p>
        </div>
    </div>

    {{-- Load more --}}
    <div x-show="hasMore" class="text-center mt-6">
        <button @click="loadMore()"
                :disabled="loadingMore"
                class="px-6 py-2 text-[#1a2a4a] hover:text-[#0f1a30] font-medium transition">
            <span x-show="!loadingMore">Load more...</span>
            <span x-show="loadingMore"><i class="fas fa-spinner fa-spin"></i> Loading...</span>
        </button>
    </div>
</div>

<script>
function notificationApp() {
    return {
        notifications: [],
        unreadCount: {{ (int) $initialUnread }},
        total: {{ (int) $initialTotal }},
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
            this.readCount = Math.max(this.total - this.unreadCount, 0);
        },

        async loadNotifications() {
            this.loading = true;
            try {
                const res = await fetch(`/notifications/data?limit=${this.limit}`, {
                    headers: { 'Accept': 'application/json' },
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
                    headers: { 'Accept': 'application/json' },
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
    };
}
</script>

@endsection