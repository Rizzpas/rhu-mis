<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>RHU - Silang | {{ auth()->check() && auth()->user()->hasRole('super_admin') ? 'Super Admin Portal' : 'Admin Portal' }}</title>
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
    </style>

    <script>
        // On page load or when changing themes, best to add inline in 'head' to avoid FOUC
        if (localStorage.getItem('color-theme') === 'dark' || (!('color-theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark')
        }
    </script>
</head>

<body class="print:h-auto print:overflow-visible bg-gray-100 dark:bg-gray-900 flex h-screen overflow-hidden" x-data="{ 
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
                this.confirmText = detail.confirmText || '{{ __('Confirm') }}';
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
        class="print:hidden fixed inset-y-0 left-0 z-30 w-64 bg-white dark:bg-gray-800 border-r border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-300 flex flex-col flex-shrink-0 transition-transform duration-300 transform md:relative md:translate-x-0 shadow-lg">

        <!-- Sidebar Header: RHU Logo -->
        <div class="px-5 py-5 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <img src="{{ asset('assets/images/logo.png') }}" alt="RHU Logo" class="w-9 h-9 object-contain shrink-0">
                <div>
                    <p class="text-sm font-bold text-gray-800 dark:text-white leading-tight uppercase">Rural Health Unit
                    </p>
                    <p class="text-xs text-gray-400 leading-tight">Silang, Cavite</p>
                </div>
            </div>
            <!-- Mobile Close -->
            <button @click="sidebarOpen = false"
                class="md:hidden p-1.5 rounded-md text-gray-400 hover:text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 dark:bg-gray-900 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <!-- Navigation -->
        <nav class="flex-1 px-4 py-4 space-y-0.5 text-sm overflow-y-auto">
            <a href="{{ route('admin.dashboard') }}"
                class="@if(request()->routeIs('admin.dashboard')) bg-gradient-to-r from-emerald-600 to-emerald-800 text-white shadow-md font-semibold @else text-gray-900 hover:bg-gray-100 dark:hover:bg-gray-700 dark:text-white @endif group flex items-center px-3 py-2.5 rounded-lg transition-colors">
                <svg class="mr-3 h-4 w-4 @if(request()->routeIs('admin.dashboard')) text-white @else text-gray-900 dark:text-white @endif shrink-0"
                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                </svg>
                {{ __('Dashboard') }}
            </a>

            <a href="{{ route('admin.analytics') }}"
                class="@if(request()->routeIs('admin.analytics')) bg-gradient-to-r from-emerald-600 to-emerald-800 text-white shadow-md font-semibold @else text-gray-900 hover:bg-gray-100 dark:hover:bg-gray-700 dark:bg-gray-800 hover:text-gray-900 dark:hover:text-white dark:text-white @endif group flex items-center px-3 py-2.5 rounded-lg transition-colors mt-1">
                <svg class="mr-3 h-4 w-4 @if(request()->routeIs('admin.analytics')) text-white @else text-gray-900 dark:text-white @endif shrink-0"
                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                </svg>
                {{ __('Analytics') }}
            </a>

            <div class="pt-5 pb-1.5 px-3">
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">{{ __('Website Settings') }}
                </p>
            </div>

            <a href="{{ route('admin.announcements.index') }}"
                class="@if(request()->routeIs('admin.announcements.*')) bg-gradient-to-r from-emerald-600 to-emerald-800 text-white shadow-md font-semibold @else text-gray-900 hover:bg-gray-100 dark:hover:bg-gray-700 dark:bg-gray-800 hover:text-gray-900 dark:hover:text-white dark:text-white @endif group flex items-center px-3 py-2.5 rounded-lg transition-colors">
                <svg class="mr-3 h-4 w-4 @if(request()->routeIs('admin.announcements.*')) text-white @else text-gray-900 dark:text-white @endif shrink-0"
                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z" />
                </svg>
                {{ __('Announcements') }}
            </a>

            <a href="{{ route('admin.staff.index') }}"
                class="@if(request()->routeIs('admin.staff.*')) bg-gradient-to-r from-emerald-600 to-emerald-800 text-white shadow-md font-semibold @else text-gray-900 hover:bg-gray-100 dark:hover:bg-gray-700 dark:bg-gray-800 hover:text-gray-900 dark:hover:text-white dark:text-white @endif group flex items-center px-3 py-2.5 rounded-lg transition-colors">
                <svg class="mr-3 h-4 w-4 @if(request()->routeIs('admin.staff.*')) text-white @else text-gray-900 dark:text-white @endif shrink-0"
                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
                {{ __('Staff Management') }}
            </a>

            <a href="{{ route('admin.content.index') }}"
                class="@if(request()->routeIs('admin.content.*')) bg-gradient-to-r from-emerald-600 to-emerald-800 text-white shadow-md font-semibold @else text-gray-900 hover:bg-gray-100 dark:hover:bg-gray-700 dark:bg-gray-800 hover:text-gray-900 dark:hover:text-white dark:text-white @endif group flex items-center px-3 py-2.5 rounded-lg transition-colors">
                <svg class="mr-3 h-4 w-4 @if(request()->routeIs('admin.content.*')) text-white @else text-gray-900 dark:text-white @endif shrink-0"
                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
                </svg>
                {{ __('Content Management') }}
            </a>

            <div class="pt-5 pb-1.5 px-3">
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">{{ __('System') }}</p>
            </div>

            <a href="{{ route('admin.archive.index') }}"
                class="@if(request()->routeIs('admin.archive.*')) bg-gradient-to-r from-emerald-600 to-emerald-800 text-white shadow-md font-semibold @else text-gray-900 hover:bg-gray-100 dark:hover:bg-gray-700 dark:bg-gray-800 hover:text-gray-900 dark:hover:text-white dark:text-white @endif group flex items-center px-3 py-2.5 rounded-lg transition-colors">
                <svg class="mr-3 h-4 w-4 @if(request()->routeIs('admin.archive.*')) text-white @else text-gray-900 dark:text-white @endif shrink-0"
                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
                </svg>
                {{ __('Archive') }}
            </a>

            @can('view-audit-logs')
            <a href="{{ route('admin.audit.index') }}"
                class="@if(request()->routeIs('admin.audit.*')) bg-gradient-to-r from-emerald-600 to-emerald-800 text-white shadow-md font-semibold @else text-gray-900 hover:bg-gray-100 dark:hover:bg-gray-700 dark:bg-gray-800 hover:text-gray-900 dark:hover:text-white dark:text-white @endif group flex items-center px-3 py-2.5 rounded-lg transition-colors mt-1">
                <svg class="mr-3 h-4 w-4 @if(request()->routeIs('admin.audit.*')) text-white @else text-gray-900 dark:text-white @endif shrink-0"
                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                {{ __('Security Audit') }}
            </a>
            @endcan

            <div class="pt-5 pb-1.5 px-3">
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">{{ __('Pharmacy') }}</p>
            </div>

            <a href="{{ route('pharmacy.medicines') }}"
                class="@if(request()->routeIs('pharmacy.medicines')) bg-gradient-to-r from-emerald-600 to-emerald-800 text-white shadow-md font-semibold @else text-gray-900 hover:bg-gray-100 dark:hover:bg-gray-700 dark:bg-gray-800 hover:text-gray-900 dark:hover:text-white dark:text-white @endif group flex items-center px-3 py-2.5 rounded-lg transition-colors mt-1">
                <svg class="mr-3 h-4 w-4 @if(request()->routeIs('pharmacy.medicines')) text-white @else text-gray-900 dark:text-white @endif shrink-0"
                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                </svg>
                {{ __('Inventory Management') }}
            </a>

            <div class="pt-5 pb-1.5 px-3">
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">{{ __('Patient Data') }}</p>
            </div>

            <a href="{{ route('admin.patients.index') }}"
                class="@if(request()->routeIs('admin.patients.*')) bg-gradient-to-r from-emerald-600 to-emerald-800 text-white shadow-md font-semibold @else text-gray-900 hover:bg-gray-100 dark:hover:bg-gray-700 dark:bg-gray-800 hover:text-gray-900 dark:hover:text-white dark:text-white @endif group flex items-center px-3 py-2.5 rounded-lg transition-colors">
                <svg class="mr-3 h-4 w-4 @if(request()->routeIs('admin.patients.*')) text-white @else text-gray-900 dark:text-white @endif shrink-0"
                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                {{ __('Patient Records') }}
            </a>
        </nav>

        <!-- Bottom User Panel -->
        <div class="border-t border-gray-100 dark:border-gray-700 px-6 py-8">
            <!-- User Info -->
            <div class="flex items-center gap-3 mb-4">
                <div
                    class="w-10 h-10 rounded-full bg-green-100 dark:bg-green-900/40 flex items-center justify-center shrink-0 overflow-hidden border border-green-200 dark:border-green-800">
                    @if(auth()->user()->avatar_url)
                        <img src="{{ auth()->user()->avatar_url }}" alt="{{ auth()->user()->name }}" class="w-full h-full object-cover">
                    @else
                        <span class="text-sm font-bold text-green-700 dark:text-green-400">
                            {{ auth()->user()->initials }}
                        </span>
                    @endif
                </div>
                <div class="min-w-0">
                    <p class="text-base font-semibold text-gray-800 dark:text-white truncate">
                        {{ auth()->user()->name ?? 'Admin User' }}
                    </p>
                    <p class="text-xs text-gray-400 truncate capitalize">{{ ucfirst(auth()->user()->role ?? 'Admin') }}
                    </p>
                </div>
            </div>
            <!-- Settings & Logout -->
            <a href="{{ route('profile.edit') }}"
                class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 dark:bg-gray-800 hover:text-black transition-colors mb-1">
                <svg class="w-4 h-4 text-gray-700 dark:text-gray-300 shrink-0" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                <span>{{ __('Settings') }}</span>
            </a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                    class="w-full flex items-center gap-3 px-3 py-2 rounded-lg text-sm text-red-500 hover:bg-red-500 hover:text-white dark:hover:bg-red-800 dark:bg-gray-800 dark:hover:text-white transition-colors">
                    <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                    <span>{{ __('Logout') }}</span>
                </button>
            </form>
        </div>
    </div>

    <!-- Main Content -->
    <div
        class="print:overflow-visible flex-1 flex flex-col min-w-0 overflow-hidden bg-gray-100 dark:bg-gray-900 transition-colors duration-200">
        <!-- Top bar -->
        <header class="print:hidden bg-emerald-600 dark:bg-emerald-800 shadow-md z-20 transition-colors duration-200 sticky top-0"
            x-data="{
                searchQuery: '',
                showResults: false,
                links: [
                    @can('view-audit-logs')
                    { name: 'Security Audit', route: '{{ route('admin.audit.index') }}', keywords: ['audit', 'history', 'security', 'logs', 'changes'] },
                    @endcan
                    { name: 'Archive / Data Retention', route: '{{ route('admin.archive.index') }}', keywords: ['archive', 'delete', 'trash', 'recycle', 'retention'] },
                    { name: 'Staff Management', route: '{{ route('admin.staff.index') }}', keywords: ['staff', 'users', 'doctors', 'nurses', 'employees', 'personnel'] },
                    { name: 'Patient Records', route: '{{ route('admin.patients.index') }}', keywords: ['patients', 'records', 'masterlist', 'people'] },
                    { name: 'Announcements', route: '{{ route('admin.announcements.index') }}', keywords: ['announcements', 'news', 'updates', 'events'] },
                    { name: 'Content Management', route: '{{ route('admin.content.index') }}', keywords: ['content', 'landing page', 'cms', 'website', 'edit'] },
                    { name: 'Dashboard', route: '{{ route('admin.dashboard') }}', keywords: ['dashboard', 'home', 'overview', 'stats'] },
                    { name: 'Analytics', route: '{{ route('admin.analytics') }}', keywords: ['analytics', 'charts', 'reports', 'demographics', 'volume', 'statistics', 'export'] }
                ],
                get filteredLinks() {
                    if (this.searchQuery.trim() === '') return [];
                    const query = this.searchQuery.toLowerCase();
                    return this.links.filter(link => {
                        return link.name.toLowerCase().includes(query) || link.keywords.some(k => k.includes(query));
                    });
                }
            }"
            @click.away="showResults = false"
        >
            <div class="flex justify-between items-center px-4 sm:px-6 py-3.5 gap-4">
                <div class="flex items-center gap-4 flex-1">
                    <!-- Hamburger Button -->
                    <button @click="sidebarOpen = true"
                        class="md:hidden text-white hover:bg-emerald-700 dark:hover:bg-emerald-900 focus:outline-none p-1.5 rounded-md transition-colors">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                    
                    <h1 class="text-xl font-bold text-white tracking-tight hidden sm:block whitespace-nowrap">
                        @yield('header', __('Dashboard Overview'))
                    </h1>

                    <!-- Intelligent Search Bar -->
                    <div class="relative w-full max-w-xl sm:ml-6 group">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-4 w-4 text-emerald-800 dark:text-emerald-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                        <input type="text" 
                            x-model="searchQuery" 
                            @focus="showResults = true"
                            @keydown.escape="showResults = false"
                            placeholder="Search..." 
                            class="w-full pl-10 pr-4 py-2 bg-white rounded-full border-none shadow-inner focus:ring-2 focus:ring-emerald-300 focus:outline-none text-sm text-emerald-900 placeholder-emerald-800/60 dark:bg-emerald-950 dark:text-emerald-100 dark:placeholder-emerald-400/50 transition-shadow">
                        
                        <!-- Search Dropdown -->
                        <div x-show="showResults && searchQuery.length > 0" 
                            x-transition:enter="transition ease-out duration-100"
                            x-transition:enter-start="opacity-0 translate-y-1"
                            x-transition:enter-end="opacity-100 translate-y-0"
                            style="display: none;"
                            class="absolute z-50 mt-2 w-full bg-white dark:bg-slate-800 rounded-xl shadow-xl border border-slate-100 dark:border-slate-700 overflow-hidden top-full left-0 py-2">
                            
                            <template x-if="filteredLinks.length > 0">
                                <ul class="max-h-64 overflow-y-auto custom-scrollbar">
                                    <template x-for="link in filteredLinks" :key="link.name">
                                        <li>
                                            <a :href="link.route" class="block px-4 py-3 hover:bg-emerald-50 dark:hover:bg-slate-700/50 text-sm transition-colors group/item">
                                                <div class="flex items-center gap-3">
                                                    <div class="p-1.5 rounded-md bg-emerald-100 dark:bg-slate-900 text-emerald-600 dark:text-emerald-400 group-hover/item:scale-110 transition-transform">
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

                            <template x-if="filteredLinks.length === 0">
                                <div class="px-4 py-6 text-center">
                                    <svg class="w-8 h-8 text-slate-300 dark:text-slate-600 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    <p class="text-sm text-slate-500 dark:text-slate-400 font-medium">No results found for "<span x-text="searchQuery"></span>"</p>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-2 sm:gap-4 shrink-0">

                    <!-- Theme Toggle -->
                    <button id="theme-toggle" type="button" class="relative inline-flex h-6 w-[42px] shrink-0 cursor-pointer items-center justify-start rounded-full border-2 border-transparent bg-emerald-700/50 dark:bg-emerald-950/50 transition-colors duration-200 ease-in-out hover:bg-emerald-700 dark:hover:bg-emerald-900">
                        <span class="sr-only">Toggle theme</span>
                        <span class="pointer-events-none relative inline-flex h-5 w-5 transform items-center justify-center rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out translate-x-0 dark:translate-x-[18px]">
                            <svg id="theme-toggle-dark-icon" class="hidden w-3 h-3 text-slate-700" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"></path>
                            </svg>
                            <svg id="theme-toggle-light-icon" class="hidden w-3 h-3 text-slate-900" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                <path d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z" fill-rule="evenodd" clip-rule="evenodd"></path>
                            </svg>
                        </span>
                    </button>
                    <!-- Logout button could go here -->
                </div>
            </div>
        </header>

        <main class="print:overflow-visible flex-1 overflow-y-auto p-6">
            @include('partials.toast')

            @yield('content')

            <!-- Admin Footer -->
            <footer
                class="mt-12 border-t border-gray-200 dark:border-gray-700 pt-6 text-center text-sm text-gray-500 dark:text-gray-400 pb-6">
                <div class="flex flex-col md:flex-row justify-between items-center px-4">
                    <p>&copy; {{ date('Y') }} Rural Health Unit Admin Portal. All rights reserved.</p>
                    <div class="flex space-x-4 mt-2 md:mt-0">
                        <a href="{{ route('admin.dashboard') }}" class="hover:text-teal-600 transition">Dashboard</a>
                        <span class="text-gray-300">|</span>
                        <a href="{{ route('admin.announcements.index') }}"
                            class="hover:text-teal-600 transition">Announcements</a>
                        <span class="text-gray-300">|</span>
                        <a href="{{ route('welcome') }}" target="_blank"
                            class="hover:text-teal-600 transition flex items-center gap-1">
                            Visit Live Site <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14">
                                </path>
                            </svg>
                        </a>
                    </div>
                </div>
            </footer>
        </main>
    </div>

    <!-- Global Confirmation Modal -->
    <div x-show="open" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title"
        role="dialog" aria-modal="true">

        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <!-- Background overlay -->
            <div x-show="open" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="open = false"
                aria-hidden="true"></div>

            <!-- Modal panel -->
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div x-show="open" x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full">

                <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div
                            class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-slate-100 dark:bg-slate-800 sm:mx-0 sm:h-10 sm:w-10">
                            <svg class="h-6 w-6 text-slate-600 dark:text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                            <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white" x-text="title"
                                id="modal-title">
                            </h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500 dark:text-gray-400" x-text="message"></p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-gray-50 dark:bg-gray-900 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <form :action="action" method="POST" class="inline-flex w-full sm:ml-3 sm:w-auto">
                        @csrf
                        <input type="hidden" name="_method" :value="method">
                        <button type="submit"
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 text-base font-medium text-white focus:outline-none focus:ring-2 focus:ring-offset-2 sm:text-sm"
                            :class="confirmClass" x-text="confirmText">
                        </button>
                    </form>
                    <button type="button"
                        class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 dark:border-gray-600 shadow-sm px-4 py-2 bg-white dark:bg-gray-800 text-base font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 dark:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
                        @click="open = false">
                        {{ __('Cancel') }}
                    </button>
                </div>
            </div>
        </div>
    </div>


    <script>
        var themeToggleDarkIcon = document.getElementById('theme-toggle-dark-icon');
        var themeToggleLightIcon = document.getElementById('theme-toggle-light-icon');

        if (themeToggleDarkIcon && themeToggleLightIcon) {
            // Change the icons inside the button based on previous settings
            if (localStorage.getItem('color-theme') === 'dark' || (!('color-theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                themeToggleLightIcon.classList.remove('hidden');
            } else {
                themeToggleDarkIcon.classList.remove('hidden');
            }

            var themeToggleBtn = document.getElementById('theme-toggle');

            if (themeToggleBtn) {
                themeToggleBtn.addEventListener('click', function () {
                    // toggle icons inside button
                    themeToggleDarkIcon.classList.toggle('hidden');
                    themeToggleLightIcon.classList.toggle('hidden');

                    // if set via local storage previously
                    if (localStorage.getItem('color-theme')) {
                        if (localStorage.getItem('color-theme') === 'light') {
                            document.documentElement.classList.add('dark');
                            localStorage.setItem('color-theme', 'dark');
                        } else {
                            document.documentElement.classList.remove('dark');
                            localStorage.setItem('color-theme', 'light');
                        }
                        // if NOT set via local storage previously
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
        }
    </script>
    @include('partials.idle-timeout')
    @include('partials.heartbeat')

        <!-- Dynamic SPA & Polling Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const dynamicBlocks = document.querySelectorAll('[data-dynamic-block="true"]');
            
            // 1. Polling Engine (every 15s)
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

            // 2. SPA Interceptor for Pagination Links
            document.addEventListener('click', async function(e) {
                const link = e.target.closest('a');
                if (!link) return;
                
                const dynamicBlock = link.closest('[data-dynamic-block="true"]');
                // Check if it's a pagination link pointing to the same route
                if (dynamicBlock && link.href && link.hostname === window.location.hostname && link.pathname === window.location.pathname && link.href.includes('page=')) {
                    e.preventDefault();
                    await fetchDynamicContent(link.href, dynamicBlock);
                }
            });

            // 3. SPA Interceptor for Jump To Forms
            document.addEventListener('submit', async function(e) {
                const form = e.target;
                const dynamicBlock = form.closest('[data-dynamic-block="true"]');
                if (dynamicBlock && form.method.toLowerCase() === 'get' && new URL(form.action).pathname === window.location.pathname) {
                    e.preventDefault();
                    const url = new URL(form.action);
                    new FormData(form).forEach((v, k) => url.searchParams.set(k, v));
                    await fetchDynamicContent(url.toString(), dynamicBlock);
                }
            });

            async function fetchDynamicContent(targetUrl, block) {
                // Optional: add a slight opacity to show loading state
                const originalOpacity = block.style.opacity;
                block.style.opacity = '0.5';
                block.style.pointerEvents = 'none';
                
                try {
                    const response = await fetch(targetUrl, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
                    if (!response.ok) return;
                    const html = await response.text();
                    const doc = new DOMParser().parseFromString(html, 'text/html');
                    
                    const newBlock = doc.getElementById(block.id);
                    if (newBlock) {
                        block.innerHTML = newBlock.innerHTML;
                    }
                    window.history.pushState({}, '', targetUrl);
                } catch (error) {
                    window.location.href = targetUrl;
                } finally {
                    block.style.opacity = originalOpacity;
                    block.style.pointerEvents = 'auto';
                }
            }
        });
    </script>
</body>

</html>