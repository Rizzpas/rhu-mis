<div x-data="notificationSystem()" class="relative mr-3" x-init="init()">
    <!-- Bell Icon Button -->
    <button @click="toggleDropdown" @click.away="dropdownOpen = false" type="button" class="relative inline-flex items-center justify-center p-2 rounded-full text-emerald-100 hover:text-white hover:bg-emerald-700/50 transition-colors focus:outline-none focus:ring-2 focus:ring-emerald-400">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
        
        <!-- Badge -->
        <span x-show="unreadCount > 0" x-text="unreadCount" style="display: none;" class="absolute top-0 right-0 -mt-1 -mr-1 inline-flex items-center justify-center w-5 h-5 text-xs font-bold text-white bg-red-500 rounded-full shadow-sm animate-pulse">
        </span>
    </button>

    <!-- Dropdown Menu -->
    <div x-show="dropdownOpen" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95 -translate-y-2" x-transition:enter-end="opacity-100 scale-100 translate-y-0" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 scale-100 translate-y-0" x-transition:leave-end="opacity-0 scale-95 -translate-y-2" style="display: none;" class="absolute right-0 mt-2 w-80 sm:w-96 bg-white dark:bg-slate-800 rounded-2xl shadow-[0_10px_40px_-10px_rgba(0,0,0,0.3)] border border-slate-100 dark:border-slate-700 overflow-hidden z-[100]">
        
        <!-- Header -->
        <div class="px-4 py-3 bg-slate-50 dark:bg-slate-800/80 border-b border-slate-100 dark:border-slate-700 flex justify-between items-center backdrop-blur-sm">
            <h3 class="text-sm font-bold text-slate-800 dark:text-white">Notifications</h3>
            <button @click="markAllAsRead" x-show="unreadCount > 0" class="text-xs font-semibold text-emerald-600 dark:text-emerald-400 hover:text-emerald-700 dark:hover:text-emerald-300 transition-colors">
                Mark all read
            </button>
        </div>

        <!-- Notification List -->
        <div class="max-h-80 overflow-y-auto custom-scrollbar divide-y divide-slate-50 dark:divide-slate-700/50">
            <template x-if="notifications.length === 0">
                <div class="p-6 text-center text-slate-500 dark:text-slate-400">
                    <svg class="w-10 h-10 mx-auto text-slate-300 dark:text-slate-600 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                    <p class="text-sm font-medium">No new notifications</p>
                </div>
            </template>
            
            <template x-for="notification in notifications" :key="notification.id">
                <a :href="notification.data.url" @click.prevent="markAsReadAndNavigate(notification)" class="block px-4 py-3 hover:bg-slate-50 dark:hover:bg-slate-700/30 transition-colors relative" :class="{'bg-emerald-50/50 dark:bg-emerald-900/10': notification.read_at === null}">
                    
                    <!-- Unread Indicator -->
                    <div x-show="notification.read_at === null" class="absolute left-2 top-1/2 -translate-y-1/2 w-1.5 h-1.5 bg-emerald-500 rounded-full"></div>
                    
                    <div class="flex items-start gap-3 pl-3">
                        <div class="w-8 h-8 rounded-full bg-emerald-100 dark:bg-emerald-900/50 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0 mt-0.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm text-slate-800 dark:text-slate-200 font-medium" x-text="notification.data.message"></p>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5" x-text="formatDate(notification.created_at)"></p>
                        </div>
                    </div>
                </a>
            </template>
        </div>
    </div>
</div>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('notificationSystem', () => ({
            dropdownOpen: false,
            unreadCount: 0,
            notifications: [],
            pollInterval: null,

            init() {
                this.fetchNotifications();
                // Poll every 15 seconds
                this.pollInterval = setInterval(() => {
                    this.fetchNotifications();
                }, 15000);
            },

            toggleDropdown() {
                this.dropdownOpen = !this.dropdownOpen;
                if (this.dropdownOpen) {
                    this.fetchNotifications();
                }
            },

            async fetchNotifications() {
                try {
                    const response = await fetch('/api/notifications', {
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });
                    
                    if (response.ok) {
                        const data = await response.json();
                        this.unreadCount = data.unread_count;
                        this.notifications = data.notifications;
                    }
                } catch (error) {
                    console.error('Failed to fetch notifications:', error);
                }
            },

            async markAsReadAndNavigate(notification) {
                if (notification.read_at === null) {
                    try {
                        let csrfTokenMeta = document.querySelector('meta[name="csrf-token"]');
                        let csrfToken = csrfTokenMeta ? csrfTokenMeta.getAttribute('content') : '';
                        await fetch(`/api/notifications/${notification.id}/read`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': csrfToken,
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        });
                        this.unreadCount = Math.max(0, this.unreadCount - 1);
                        notification.read_at = new Date().toISOString();
                    } catch (error) {
                        console.error('Failed to mark notification as read:', error);
                    }
                }
                
                // Navigate after marking as read
                if (notification.data.url) {
                    window.location.href = notification.data.url;
                }
            },

            async markAllAsRead() {
                try {
                    let csrfTokenMeta = document.querySelector('meta[name="csrf-token"]');
                    let csrfToken = csrfTokenMeta ? csrfTokenMeta.getAttribute('content') : '';
                    await fetch(`/api/notifications/read-all`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });
                    
                    this.unreadCount = 0;
                    this.notifications = this.notifications.map(n => ({...n, read_at: new Date().toISOString()}));
                } catch (error) {
                    console.error('Failed to mark all as read:', error);
                }
            },

            formatDate(dateString) {
                const date = new Date(dateString);
                const now = new Date();
                const diffMs = now - date;
                const diffMins = Math.floor(diffMs / 60000);
                const diffHours = Math.floor(diffMins / 60);
                
                if (diffMins < 1) return 'Just now';
                if (diffMins < 60) return `${diffMins}m ago`;
                if (diffHours < 24) return `${diffHours}h ago`;
                
                return date.toLocaleDateString(undefined, { month: 'short', day: 'numeric' });
            }
        }));
    });
</script>
