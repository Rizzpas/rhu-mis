<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>RHU - Silang | Front Desk Portal</title>
    <link rel="icon" type="image/png" href="{{ asset('assets/images/logo.png') }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Styles -->
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
        }
        [x-cloak] { display: none !important; }
        
        /* MIS Global Uppercase */
        input[type="text"]:not(.no-uppercase), 
        input[type="search"]:not(.no-uppercase), 
        textarea:not(.no-uppercase) {
            text-transform: uppercase;
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
            confirmText: 'Confirm',
            confirmClass: 'bg-teal-600 hover:bg-teal-700 focus:ring-teal-500', 
            
            show(detail) {
                this.title = detail.title || 'Confirm Action';
                this.message = detail.message || 'Are you sure you want to proceed?';
                this.action = detail.action;
                this.method = detail.method || 'POST';
                this.confirmText = detail.confirmText || 'Confirm';
                if(detail.type === 'danger') {
                    this.confirmClass = 'bg-red-600 hover:bg-red-700 focus:ring-red-500';
                } else {
                    this.confirmClass = 'bg-teal-600 hover:bg-teal-700 focus:ring-teal-500';
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
        class="fixed inset-0 bg-gray-900 bg-opacity-50 z-20 md:hidden" style="display: none;"></div>

    <!-- Sidebar -->
    <div :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
        class="fixed inset-y-0 left-0 z-30 w-64 bg-white dark:bg-gray-800 border-r border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-300 flex flex-col shrink-0 transition-transform duration-300 transform md:relative md:translate-x-0 shadow-lg">

        <!-- Sidebar Header -->
        <div class="px-5 py-5 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <img src="{{ asset('assets/images/logo.png') }}" alt="RHU Logo" class="w-9 h-9 object-contain shrink-0">
                <div>
                    <p class="text-sm font-bold text-gray-800 dark:text-white leading-tight uppercase whitespace-nowrap">Rural Health Unit</p>
                    <p class="text-xs text-gray-400 leading-tight">Front Desk</p>
                </div>
            </div>
            <button @click="sidebarOpen = false" class="md:hidden p-1.5 text-gray-400 hover:text-gray-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
            </button>
        </div>

        <!-- Navigation -->
        <nav class="flex-1 px-4 py-4 space-y-0.5 text-sm overflow-y-auto">
            <a href="{{ route('frontdesk.dashboard') }}"
                class="@if(request()->routeIs('frontdesk.dashboard')) bg-gradient-to-r from-emerald-600 to-emerald-800 text-white shadow-md font-semibold @else text-gray-900 hover:bg-gray-100 dark:hover:bg-gray-700 dark:text-white @endif group flex items-center px-3 py-2.5 rounded-lg transition-colors">
                <svg class="mr-3 h-4 w-4 @if(request()->routeIs('frontdesk.dashboard')) text-white @else text-gray-900 dark:text-white @endif shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                </svg>
                Dashboard
            </a>

            <a href="{{ route('frontdesk.registration.index') }}"
                class="@if(request()->routeIs('frontdesk.registration.*')) bg-gradient-to-r from-emerald-600 to-emerald-800 text-white shadow-md font-semibold @else text-gray-900 hover:bg-gray-100 dark:hover:bg-gray-700 dark:text-white @endif group flex items-center px-3 py-2.5 rounded-lg transition-colors">
                <svg class="mr-3 h-4 w-4 @if(request()->routeIs('frontdesk.registration.*')) text-white @else text-gray-900 dark:text-white @endif shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                </svg>
                Registration &amp; Triage
            </a>

            <a href="{{ route('frontdesk.queue-overview') }}"
                class="@if(request()->routeIs('frontdesk.queue-overview')) bg-gradient-to-r from-emerald-600 to-emerald-800 text-white shadow-md font-semibold @else text-gray-900 hover:bg-gray-100 dark:hover:bg-gray-700 dark:text-white @endif group flex items-center px-3 py-2.5 rounded-lg transition-colors">
                <svg class="mr-3 h-4 w-4 @if(request()->routeIs('frontdesk.queue-overview')) text-white @else text-gray-900 dark:text-white @endif shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                </svg>
                Queue Overview
            </a>

            <a href="{{ route('frontdesk.patients.index') }}"
                class="@if(request()->routeIs('frontdesk.patients.*')) bg-gradient-to-r from-emerald-600 to-emerald-800 text-white shadow-md font-semibold @else text-gray-900 hover:bg-gray-100 dark:hover:bg-gray-700 dark:text-white @endif group flex items-center px-3 py-2.5 rounded-lg transition-colors">
                <svg class="mr-3 h-4 w-4 @if(request()->routeIs('frontdesk.patients.*')) text-white @else text-gray-900 dark:text-white @endif shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
                Patient Master List
            </a>

            @if(auth()->check() && auth()->user()->hasRole('admin', 'super_admin'))
                <a href="{{ route('admin.dashboard') }}"
                    class="text-gray-900 hover:bg-gray-100 dark:hover:bg-gray-700 dark:text-white group flex items-center px-3 py-2.5 rounded-lg transition-colors mt-4 border-t border-gray-100 dark:border-gray-700 pt-4 font-semibold">
                    <svg class="mr-3 h-4 w-4 text-emerald-600 dark:text-emerald-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Back to Admin
                </a>
            @endif
        </nav>

        <!-- Bottom User Panel -->
        <div class="border-t border-gray-100 dark:border-gray-700 px-6 py-8">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 rounded-full bg-emerald-100 dark:bg-emerald-900/40 flex items-center justify-center shrink-0 overflow-hidden border border-emerald-200 dark:border-emerald-800">
                    @if(auth()->user()->avatar_url)
                        <img src="{{ auth()->user()->avatar_url }}" alt="{{ auth()->user()->name }}" class="w-full h-full object-cover">
                    @else
                        <span class="text-sm font-bold text-emerald-700 dark:text-emerald-400">{{ auth()->user()->initials }}</span>
                    @endif
                </div>
                <div class="min-w-0">
                    <p class="text-base font-semibold text-gray-800 dark:text-white truncate">{{ auth()->user()->name }}</p>
                    <p class="text-xs text-gray-400 truncate capitalize">{{ ucfirst(str_replace('_', ' ', auth()->user()->role)) }}</p>
                </div>
            </div>
            <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 mb-1">
                <svg class="w-4 h-4 text-gray-700 dark:text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                <span>Settings</span>
            </a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full flex items-center gap-3 px-3 py-2 rounded-lg text-sm text-red-500 hover:bg-red-500 hover:text-white transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" /></svg>
                    <span>Logout</span>
                </button>
            </form>
        </div>
    </div>

    <!-- Main Content -->
    <div class="flex-1 flex flex-col min-w-0 overflow-hidden bg-gray-100 dark:bg-gray-900 transition-colors duration-200">
        <!-- Top bar -->
        <header class="bg-emerald-600 dark:bg-emerald-800 shadow-md z-20 sticky top-0"
            x-data="{
                searchQuery: '',
                showResults: false,
                links: [
                    { name: 'Dashboard', route: '{{ route('frontdesk.dashboard') }}', keywords: ['dashboard', 'home', 'overview'] },
                    { name: 'Registration & Triage', route: '{{ route('frontdesk.registration.index') }}', keywords: ['registration', 'triage', 'patient check-in', 'new patient'] },
                    { name: 'Queue Overview', route: '{{ route('frontdesk.queue-overview') }}', keywords: ['queue', 'waiting list', 'overview', 'status'] },
                    { name: 'Patient Master List', route: '{{ route('frontdesk.patients.index') }}', keywords: ['patients', 'records', 'master list', 'search patient'] }
                ],
                get filteredLinks() {
                    if (this.searchQuery.trim() === '') return [];
                    const query = this.searchQuery.toLowerCase();
                    return this.links.filter(link => {
                        return link.name.toLowerCase().includes(query) || link.keywords.some(k => k.includes(query));
                    });
                }
            }" @click.away="showResults = false">
            
            <div class="flex justify-between items-center px-4 sm:px-6 py-3.5 gap-4">
                <div class="flex items-center gap-4 flex-1">
                    <button @click="sidebarOpen = true" class="md:hidden text-white hover:bg-emerald-700 p-1.5 rounded-md transition-colors">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" /></svg>
                    </button>
                    
                    <h1 class="text-xl font-bold text-white tracking-tight hidden sm:block whitespace-nowrap">
                        @yield('header', 'Dashboard')
                    </h1>

                    <!-- Search Bar -->
                    <div class="relative w-full max-w-xl sm:ml-6 group">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-4 w-4 text-emerald-800 dark:text-emerald-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                        </div>
                        <input type="text" x-model="searchQuery" @focus="showResults = true" @keydown.escape="showResults = false" placeholder="Search..." 
                            class="w-full pl-10 pr-4 py-2 bg-white rounded-full border-none shadow-inner focus:ring-2 focus:ring-emerald-300 focus:outline-none text-sm text-emerald-900 placeholder-emerald-800/60 dark:bg-emerald-950 dark:text-emerald-100 dark:placeholder-emerald-400/50 transition-shadow">
                        
                        <!-- Search Dropdown -->
                        <div x-show="showResults && searchQuery.length > 0" style="display: none;" class="absolute z-50 mt-2 w-full bg-white dark:bg-slate-800 rounded-xl shadow-xl border border-slate-100 dark:border-slate-700 overflow-hidden top-full left-0 py-2">
                            <template x-if="filteredLinks.length > 0">
                                <ul class="max-h-64 overflow-y-auto custom-scrollbar">
                                    <template x-for="link in filteredLinks" :key="link.name">
                                        <li>
                                            <a :href="link.route" class="block px-4 py-3 hover:bg-emerald-50 dark:hover:bg-slate-700/50 text-sm transition-colors group/item">
                                                <div class="flex items-center gap-3">
                                                    <div class="p-1.5 rounded-md bg-emerald-100 dark:bg-slate-900 text-emerald-600 dark:text-emerald-400">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7"></path></svg>
                                                    </div>
                                                    <div>
                                                        <span class="font-semibold text-slate-800 dark:text-slate-200" x-text="link.name"></span>
                                                        <span class="block text-xs text-slate-500 dark:text-slate-400 mt-0.5">Quick Navigation</span>
                                                    </div>
                                                </div>
                                            </a>
                                        </li>
                                    </template>
                                </ul>
                            </template>
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-2 sm:gap-4 shrink-0">
                    <!-- Theme Toggle -->
                    <button id="theme-toggle" type="button" class="relative inline-flex h-6 w-[42px] shrink-0 cursor-pointer items-center justify-start rounded-full border-2 border-transparent bg-emerald-700/50 dark:bg-emerald-950/50 transition-colors duration-200">
                        <span class="pointer-events-none relative inline-flex h-5 w-5 transform items-center justify-center rounded-full bg-white shadow ring-0 transition duration-200 translate-x-0 dark:translate-x-[18px]">
                            <svg id="theme-toggle-dark-icon" class="hidden w-3 h-3 text-slate-700" fill="currentColor" viewBox="0 0 20 20"><path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"></path></svg>
                            <svg id="theme-toggle-light-icon" class="hidden w-3 h-3 text-slate-900" fill="currentColor" viewBox="0 0 20 20"><path d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z" fill-rule="evenodd" clip-rule="evenodd"></path></svg>
                        </span>
                    </button>
                    <span class="text-xs text-emerald-100 hidden lg:block">{{ now()->format('l, F j, Y') }}</span>
                </div>
            </div>
        </header>

        <main class="flex-1 overflow-y-auto p-6">
            @include('partials.toast')
            @if ($errors->any())
                <div class="mb-4 bg-red-50 border-l-4 border-red-500 text-red-700 p-4 rounded shadow-sm">
                    <p class="font-bold mb-1">Please correct the following errors:</p>
                    <ul class="list-disc list-inside text-sm">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            @yield('content')
            <footer class="mt-12 border-t border-gray-200 dark:border-gray-700 pt-6 text-center text-sm text-gray-500 dark:text-gray-400 pb-6">
                <p>&copy; {{ date('Y') }} Rural Health Unit Front Desk Portal. All rights reserved.</p>
            </footer>
        </main>
    </div>

    <!-- Confirmation Modal -->
    <div x-show="open" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div x-show="open" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="open = false"></div>
            <div class="relative bg-white dark:bg-gray-800 rounded-lg overflow-hidden shadow-xl max-w-lg w-full p-6">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white" x-text="title"></h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-2" x-text="message"></p>
                <div class="mt-6 flex justify-end gap-3">
                    <button @click="open = false" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md">Cancel</button>
                    <form :action="action" method="POST">
                        @csrf
                        <input type="hidden" name="_method" :value="method">
                        <button type="submit" class="px-4 py-2 text-sm font-medium text-white rounded-md" :class="confirmClass" x-text="confirmText"></button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        var themeToggleDarkIcon = document.getElementById('theme-toggle-dark-icon');
        var themeToggleLightIcon = document.getElementById('theme-toggle-light-icon');
        if (themeToggleDarkIcon && themeToggleLightIcon) {
            if (localStorage.getItem('color-theme') === 'dark' || (!('color-theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                themeToggleLightIcon.classList.remove('hidden');
            } else {
                themeToggleDarkIcon.classList.remove('hidden');
            }
            document.getElementById('theme-toggle').addEventListener('click', function() {
                themeToggleDarkIcon.classList.toggle('hidden');
                themeToggleLightIcon.classList.toggle('hidden');
                if (localStorage.getItem('color-theme')) {
                    if (localStorage.getItem('color-theme') === 'light') {
                        document.documentElement.classList.add('dark');
                        localStorage.setItem('color-theme', 'dark');
                    } else {
                        document.documentElement.classList.remove('dark');
                        localStorage.setItem('color-theme', 'light');
                    }
                } else {
                    if (document.documentElement.classList.contains('dark')) {
                        document.documentElement.classList.remove('dark');
                        localStorage.setItem('color-theme', 'light');
                    } else {
                        document.documentElement.classList.add('dark');
                        localStorage.setItem('color-theme', 'dark');
                    }
                }
            });
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
    <script src=\"https://cdn.jsdelivr.net/npm/flatpickr\"></script>
    @stack('scripts')
</body>

</html>