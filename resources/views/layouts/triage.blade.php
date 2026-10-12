<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>RHU - Silang | Vitals Station</title>
    <link rel="icon" type="image/png" href="{{ asset('assets/images/logo.png') }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Styles -->
    <link rel="stylesheet" href="{{ asset('css/print.css') }}">
    <script src="{{ asset('js/print-helper.js') }}"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
        }
    </script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        body {
            font-family: 'Inter', sans-serif;
            letter-spacing: -0.025em;
        }
        [x-cloak] { display: none !important; }
        
        /* MIS Global Uppercase */
        input[type="text"]:not(.no-uppercase), 
        input[type="search"]:not(.no-uppercase) {
            text-transform: uppercase;
        }

        /* Force search bars to stay sentence-case */
        header input[type="text"],
        header input[type="search"],
        .search-input {
            text-transform: none !important;
        }

        /* Custom subtle scrollbar (matches Frontdesk portal) */
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        ::-webkit-scrollbar-track {
            background: transparent;
        }
        ::-webkit-scrollbar-thumb {
            background: rgba(148, 163, 184, 0.4);
            border-radius: 9999px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: rgba(100, 116, 139, 0.6);
        }
        .dark ::-webkit-scrollbar-thumb {
            background: rgba(71, 85, 105, 0.5);
        }
        .dark ::-webkit-scrollbar-thumb:hover {
            background: rgba(100, 116, 139, 0.8);
        }

        /* Firefox support */
        * {
            scrollbar-width: thin;
            scrollbar-color: rgba(148, 163, 184, 0.4) transparent;
        }
        .dark, .dark * {
            scrollbar-color: rgba(71, 85, 105, 0.5) transparent;
        }

        /* Explicit class matching frontdesk */
        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: transparent;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: rgba(148, 163, 184, 0.4);
            border-radius: 9999px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: rgba(100, 116, 139, 0.6);
        }
        .dark .custom-scrollbar::-webkit-scrollbar-thumb {
            background: rgba(71, 85, 105, 0.5);
        }
        .dark .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: rgba(100, 116, 139, 0.8);
        }
    </style>

    <script>
        if (localStorage.getItem('color-theme') === 'dark' || (!('color-theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark')
        }
    </script>
</head>

<body class="bg-gray-100 dark:bg-gray-900 flex h-screen overflow-hidden" x-data="{ 
            sidebarOpen: false,
            open: false,
            title: '',
            message: '',
            action: '',
            method: 'POST',
            confirmClass: 'bg-rose-600 hover:bg-rose-700 shadow-rose-600/20', 
            iconBgClass: 'bg-rose-100 dark:bg-rose-900/30 text-rose-600 dark:text-rose-400',
            
            show(detail) {
                this.title = detail.title || 'Confirm Action';
                this.message = detail.message || 'Are you sure you want to proceed?';
                this.action = detail.action;
                this.method = detail.method || 'POST';
                this.confirmText = detail.confirmText || 'Confirm';
                if(detail.type === 'danger' || !detail.type) {
                    this.confirmClass = 'bg-rose-600 hover:bg-rose-700 shadow-rose-600/20';
                    this.iconBgClass = 'bg-rose-100 dark:bg-rose-900/30 text-rose-600 dark:text-rose-400';
                } else {
                    this.confirmClass = 'bg-emerald-600 hover:bg-emerald-700 shadow-emerald-600/20';
                    this.iconBgClass = 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400';
                }
                this.open = true;
            }
        }" @open-confirmation.window="show($event.detail)">
    
    @include('partials.skeleton-dashboard')

    <!-- Mobile Backdrop -->
    <div x-show="sidebarOpen" @click="sidebarOpen = false"
        x-transition:enter="transition-opacity ease-linear duration-300" x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100" x-transition:leave="transition-opacity ease-linear duration-300"
        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
        class="fixed inset-0 bg-gray-900 bg-opacity-50 z-40 md:hidden" style="display: none;"></div>

    <!-- Sidebar -->
    <div :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
        class="fixed inset-y-0 left-0 z-50 w-64 bg-white dark:bg-gray-800 border-r border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-300 flex flex-col shrink-0 transition-transform duration-300 transform md:relative md:translate-x-0 shadow-lg">

        <!-- Sidebar Header -->
        <div class="px-5 py-5 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <img src="{{ asset('assets/images/logo.png') }}" alt="RHU Logo" class="w-9 h-9 object-contain shrink-0">
                <div>
                    <p class="text-sm font-bold text-gray-800 dark:text-white leading-tight uppercase whitespace-nowrap">Rural Health Unit</p>
                    <p class="text-xs text-gray-400 leading-tight">Vitals Station</p>
                </div>
            </div>
            <button @click="sidebarOpen = false" class="md:hidden p-1.5 text-gray-400 hover:text-gray-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
            </button>
        </div>

        <!-- Navigation -->
        <nav class="flex-1 px-4 py-4 space-y-0.5 text-sm overflow-y-auto custom-scrollbar">
            <a href="{{ route('nurse.dashboard') }}"
                class="@if(request()->routeIs('nurse.dashboard')) bg-gradient-to-r from-emerald-600 to-emerald-800 text-white shadow-md font-semibold @else text-gray-900 hover:bg-gray-100 dark:hover:bg-gray-700 dark:text-white @endif group flex items-center px-3 py-2.5 rounded-lg transition-colors">
                <svg class="mr-3 h-4 w-4 @if(request()->routeIs('nurse.dashboard')) text-white @else text-gray-900 dark:text-white @endif shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                </svg>
                Dashboard
            </a>

            @if(auth()->check() && auth()->user()->hasRole('admin', 'super_admin'))
                <div class="pt-4 mt-4 border-t border-gray-100 dark:border-gray-700">
                    <a href="{{ route('admin.dashboard') }}"
                        class="text-gray-900 hover:bg-gray-100 dark:hover:bg-gray-700 dark:text-white group flex items-center px-3 py-2.5 rounded-lg transition-colors font-semibold">
                        <svg class="mr-3 h-4 w-4 text-emerald-600 dark:text-emerald-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        Back to Admin
                    </a>
                </div>
            @endif
        </nav>
    </div>

    <!-- Main Content -->
    <div class="flex-1 flex flex-col min-w-0 overflow-hidden bg-slate-100/90 dark:bg-slate-900/95 transition-colors duration-200">
        <!-- Top bar -->
        <header class="print:hidden bg-emerald-600 dark:bg-emerald-800/95 sticky top-0 z-40 shadow-xs border-b border-emerald-500/20 dark:border-emerald-700/40 backdrop-blur-md transition-colors duration-200">
            <div class="flex justify-between items-center px-4 sm:px-6 py-2.5 sm:py-3 min-h-[64px] gap-3 sm:gap-4 relative">
                <!-- Left: Mobile Menu Toggle & Title -->
                <div class="flex items-center gap-2 sm:gap-4 min-w-0">
                    <button @click="sidebarOpen = true"
                        class="md:hidden text-white/90 hover:text-white hover:bg-white/10 dark:hover:bg-black/20 focus:outline-none p-2 rounded-xl transition-all active:scale-95 shrink-0 cursor-pointer"
                        aria-label="Open sidebar menu">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                    
                    <div class="flex items-center gap-2 min-w-0">
                        <h1 class="text-sm sm:text-base md:text-lg font-bold text-white tracking-tight truncate flex items-center gap-2">
                            @yield('header', 'Vitals Station')
                        </h1>
                    </div>
                </div>

                <!-- Right Side Actions: Date Pill, Theme Toggle, Account Menu -->
                <div class="flex items-center gap-2 sm:gap-3 shrink-0">
                    <!-- Live Date Badge -->
                    <div class="hidden md:flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-white/10 dark:bg-emerald-950/40 border border-white/15 dark:border-emerald-700/30 text-xs font-semibold text-emerald-50 shrink-0">
                        <svg class="w-3.5 h-3.5 text-emerald-200 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <span>{{ now()->format('l, F j, Y') }}</span>
                    </div>

                    <!-- Theme Toggle -->
                    @include('partials.theme-toggle', ['variant' => 'circle'])

                    <!-- Staff Messages -->
                    @include('partials.chat-nav-button')

                    <!-- Account Menu -->
                    @include('partials.account-menu')
                </div>
            </div>
        </header>

        <main class="print:overflow-visible print:p-0 flex-1 overflow-y-auto flex flex-col p-4 sm:p-6 lg:p-8 custom-scrollbar bg-slate-100/90 dark:bg-slate-900/95 transition-colors duration-200">
            <div class="flex-1 w-full max-w-7xl mx-auto flex flex-col">
                @include('partials.toast')
                @yield('content')
            </div>

            <!-- Modern Unified Vitals Station Footer -->
            <footer class="mt-auto pt-8 pb-4 border-t border-slate-200/80 dark:border-slate-800 text-xs text-slate-500 dark:text-slate-400">
                <div class="w-full max-w-7xl mx-auto flex flex-col sm:flex-row justify-between items-center gap-3 text-center sm:text-left">
                    <p>&copy; {{ date('Y') }} Rural Health Unit &ndash; Silang. Vitals Station. All rights reserved.</p>
                    <div class="flex items-center gap-4 text-xs">
                        <a href="{{ route('triage.dashboard') }}" class="hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors">Vitals Queue</a>
                        <span class="text-slate-300 dark:text-slate-700">•</span>
                        <a href="{{ route('nurse.dashboard') }}" class="hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors">Nurse Dashboard</a>
                        <span class="text-slate-300 dark:text-slate-700">•</span>
                        <span class="inline-flex items-center gap-1.5 font-medium text-emerald-600 dark:text-emerald-400">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                            Station Online
                        </span>
                    </div>
                </div>
            </footer>
        </main>
    </div>

    <!-- Confirmation Modal -->
    <template x-teleport="body">
        <div x-show="open" x-cloak style="display: none;" class="fixed inset-0 z-[100] overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true" @keydown.escape.window="open = false">
            <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <!-- Background overlay -->
                <div x-show="open" 
                    x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                    x-transition:leave="ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                    class="fixed inset-0 bg-slate-950/80 backdrop-blur-sm transition-opacity" @click="open = false" aria-hidden="true"></div>

                <!-- Modal panel -->
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                <div x-show="open" 
                    x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                    x-transition:leave="ease-in duration-150" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                    @click.stop
                    class="relative z-50 inline-block align-bottom bg-white dark:bg-slate-900 rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full border border-slate-200 dark:border-slate-800">

                    <div class="bg-white dark:bg-slate-900 px-6 pt-6 pb-6">
                        <div class="sm:flex sm:items-start">
                            <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-2xl sm:mx-0 sm:h-10 sm:w-10" :class="iconBgClass">
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                            </div>
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                                <h3 class="text-xl font-bold text-slate-900 dark:text-white" id="modal-title" x-text="title"></h3>
                                <div class="mt-2">
                                    <p class="text-sm text-slate-500 dark:text-slate-400" x-text="message"></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-slate-50 dark:bg-slate-950/50 px-6 py-4 flex flex-row-reverse gap-3">
                        <form :action="action" method="POST" class="inline-flex w-full sm:w-auto">
                            @csrf
                            <input type="hidden" name="_method" :value="method">
                            <button type="submit"
                                class="w-full inline-flex justify-center rounded-xl border border-transparent shadow-lg px-6 py-2 text-sm font-bold text-white transition-all cursor-pointer"
                                :class="confirmClass" x-text="confirmText">
                            </button>
                        </form>
                        <button type="button"
                            class="inline-flex justify-center rounded-xl border border-slate-300 dark:border-slate-700 shadow-sm px-6 py-2 bg-white dark:bg-slate-800 text-sm font-bold text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 transition-all cursor-pointer"
                            @click="open = false">
                            {{ __('Cancel') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </template>

    <script>
        if (!window.toggleTheme) {
            window.toggleTheme = function() {
                var isDark = document.documentElement.classList.toggle('dark');
                localStorage.setItem('color-theme', isDark ? 'dark' : 'light');
                window.dispatchEvent(new CustomEvent('theme-changed', { detail: { isDark: isDark } }));
            };
        }
    </script>
    @include('partials.idle-timeout')
    @include('partials.heartbeat')
    
    <!-- Polling Engine Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const dynamicBlocks = document.querySelectorAll('[data-dynamic-block="true"]');
            
            if (dynamicBlocks.length > 0) {
                setInterval(async () => {
                    try {
                        const url = new URL(window.location.href);
                        url.searchParams.append('polling', '1');
                        
                        const response = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
                        if (!response.ok) return;
                        const html = await response.text();
                        const doc = new DOMParser().parseFromString(html, 'text/html');
                        
                        dynamicBlocks.forEach(block => {
                            if (block.id) {
                                const newBlock = doc.getElementById(block.id);
                                if (newBlock) block.innerHTML = newBlock.innerHTML;
                            }
                        });
                    } catch (error) {}
                }, 15000);
            }
        });
    </script>

    @include('partials.chat-widget')
</body>

</html>