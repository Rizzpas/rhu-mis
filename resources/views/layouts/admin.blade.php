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
    <link rel="stylesheet" href="{{ asset('css/print.css') }}">
    <script src="{{ asset('js/print-helper.js') }}"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
        }
    </script>
    <script src="{{ asset('js/secure-image-validator.js') }}"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        body {
            font-family: 'Inter', sans-serif;
            letter-spacing: -0.025em;
        }

        /* Globally remove focus rings, borders halos, and outlines across all admin pages */
        *, *::before, *::after {
            --tw-ring-offset-shadow: 0 0 #0000 !important;
            --tw-ring-shadow: 0 0 #0000 !important;
            --tw-ring-color: transparent !important;
            --tw-ring-offset-width: 0px !important;
        }
        
        *:focus,
        *:focus-visible,
        *:focus-within,
        input:focus,
        input:focus-visible,
        input:focus-within,
        select:focus,
        select:focus-visible,
        textarea:focus,
        textarea:focus-visible,
        button:focus,
        button:focus-visible,
        a:focus,
        a:focus-visible,
        [tabindex]:focus,
        [tabindex]:focus-visible {
            outline: none !important;
            box-shadow: none !important;
            --tw-ring-offset-shadow: 0 0 #0000 !important;
            --tw-ring-shadow: 0 0 #0000 !important;
            --tw-ring-color: transparent !important;
            --tw-ring-offset-width: 0px !important;
            ring: 0 !important;
        }

        header input[type="text"],
        header input[type="search"],
        .search-input {
            text-transform: none !important;
        }
        header input[type="text"]::placeholder,
        header input[type="search"]::placeholder,
        .search-input::placeholder {
            text-transform: none !important;
        }

        /* Option tags dark and light mode styling */
        option {
            background-color: #ffffff;
            color: #1e293b;
        }
        .dark option {
            background-color: #0f172a;
            color: #f1f5f9;
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
            confirmClass: 'bg-rose-600 hover:bg-rose-700 shadow-rose-600/20', 
            iconBgClass: 'bg-rose-100 dark:bg-rose-900/30 text-rose-600 dark:text-rose-400',
            
            show(detail) {
                this.title = detail.title || 'Confirm Action';
                this.message = detail.message || 'Are you sure you want to proceed?';
                this.action = detail.action;
                this.method = detail.method || 'POST';
                this.confirmText = detail.confirmText || '{{ __('Confirm') }}';
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
        class="fixed inset-0 bg-gray-900 bg-opacity-50 z-20 md:hidden" style="display: none;"></div>

    <!-- Sidebar -->
    <div :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
        class="print:hidden fixed inset-y-0 left-0 z-30 w-64 bg-white dark:bg-gray-800 border-r border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-300 flex flex-col flex-shrink-0 transition-transform duration-300 transform md:relative md:translate-x-0 shadow-lg">

        <!-- Sidebar Header: RHU Logo -->
        <div class="px-5 py-5 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <img src="{{ asset('assets/images/logo.png') }}" alt="RHU Logo" class="w-9 h-9 object-contain shrink-0">
                <div>
                    <p class="text-sm font-bold text-gray-800 dark:text-white leading-tight uppercase whitespace-nowrap">Rural Health Unit</p>
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
        <nav class="flex-1 px-4 py-4 space-y-0.5 text-sm overflow-y-auto custom-scrollbar">
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
                    @if(auth()->user()->hasRole('super_admin'))
                        <div class="flex items-center gap-1.5 mt-0.5">
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-extrabold uppercase bg-purple-100 dark:bg-purple-950/60 text-purple-700 dark:text-purple-300 border border-purple-200 dark:border-purple-800/60 shadow-2xs">
                                <svg class="w-3 h-3 text-purple-600 dark:text-purple-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 2a1 1 0 01.832.445l2.168 3.253 3.864.561a1 1 0 01.554 1.705l-2.796 2.725.66 3.848a1 1 0 01-1.451 1.054L10 13.788l-3.467 1.803a1 1 0 01-1.451-1.054l.66-3.848-2.796-2.725a1 1 0 01.554-1.705l3.864-.561L9.168 2.445A1 1 0 0110 2z" clip-rule="evenodd"/>
                                </svg>
                                <span>Super Admin</span>
                            </span>
                        </div>
                    @else
                        <div class="flex items-center gap-1.5 mt-0.5">
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-bold uppercase bg-emerald-100 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800/60 shadow-2xs">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                <span>Administrator</span>
                            </span>
                        </div>
                    @endif
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
    <div class="print:overflow-visible flex-1 flex flex-col min-w-0 overflow-hidden bg-slate-100 dark:bg-slate-950 transition-colors duration-200">
        <!-- Top bar -->
        <header class="print:hidden bg-emerald-600 dark:bg-emerald-800/95 sticky top-0 z-20 shadow-xs border-b border-emerald-500/20 dark:border-emerald-700/40 backdrop-blur-md transition-colors duration-200"
            x-data="{
                searchQuery: '',
                showResults: false,
                mobileSearchOpen: false,
                links: [
                    @can('view-audit-logs')
                    { name: 'Security Audit', route: '{{ route('admin.audit.index') }}', desc: 'System activity & security logs', keywords: ['audit', 'history', 'security', 'logs', 'changes'] },
                    @endcan
                    { name: 'Archive / Data Retention', route: '{{ route('admin.archive.index') }}', desc: 'Retention policies & soft-deleted records', keywords: ['archive', 'delete', 'trash', 'recycle', 'retention'] },
                    { name: 'Staff Management', route: '{{ route('admin.staff.index') }}', desc: 'Personnel roster & access roles', keywords: ['staff', 'users', 'doctors', 'nurses', 'employees', 'personnel'] },
                    { name: 'Patient Records', route: '{{ route('admin.patients.index') }}', desc: 'Centralized patient masterlist', keywords: ['patients', 'records', 'masterlist', 'people'] },
                    { name: 'Announcements', route: '{{ route('admin.announcements.index') }}', desc: 'Public & staff broadcast advisories', keywords: ['announcements', 'news', 'updates', 'events'] },
                    { name: 'Content Management', route: '{{ route('admin.content.index') }}', desc: 'Landing page text & municipal sections', keywords: ['content', 'landing page', 'cms', 'website', 'edit'] },
                    { name: 'Dashboard Overview', route: '{{ route('admin.dashboard') }}', desc: 'Management information overview', keywords: ['dashboard', 'home', 'overview', 'stats'] },
                    { name: 'Detailed Analytics', route: '{{ route('admin.analytics') }}', desc: 'Epidemiological trends & reports', keywords: ['analytics', 'charts', 'reports', 'demographics', 'volume', 'statistics', 'export'] }
                ],
                get filteredLinks() {
                    if (this.searchQuery.trim() === '') return [];
                    const query = this.searchQuery.toLowerCase().trim();
                    return this.links.filter(link => {
                        return link.name.toLowerCase().includes(query) || (link.desc && link.desc.toLowerCase().includes(query)) || link.keywords.some(k => k.includes(query));
                    });
                }
            }"
            @click.away="showResults = false"
        >
            <div class="flex justify-between items-center px-4 sm:px-6 py-2.5 sm:py-3 min-h-[64px] gap-2 sm:gap-4 relative">
                
                {{-- MOBILE EXPANDED SEARCH VIEW (When mobileSearchOpen is true on small screens) --}}
                <div x-show="mobileSearchOpen" 
                     x-transition:enter="transition ease-out duration-150"
                     x-transition:enter-start="opacity-0 -translate-y-2"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     class="sm:hidden absolute inset-0 z-30 bg-emerald-600 dark:bg-emerald-900 px-3 flex items-center gap-2">
                    <button type="button" 
                            @click="mobileSearchOpen = false; showResults = false; searchQuery = '';" 
                            class="p-2 rounded-xl text-white/90 hover:text-white hover:bg-white/10 active:scale-95 transition cursor-pointer"
                            aria-label="Close search">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    </button>

                    <div class="relative flex-1">
                        <input type="text" 
                            x-ref="mobileSearchInput"
                            x-model="searchQuery" 
                            @focus="showResults = true"
                            @click="showResults = true"
                            @input="showResults = true"
                            @keydown.escape.stop="mobileSearchOpen = false; showResults = false;"
                            placeholder="Search navigation or modules..." 
                            class="no-uppercase w-full pl-9 pr-8 py-2 bg-white dark:bg-slate-900 rounded-full border border-emerald-400/40 text-xs text-slate-800 placeholder-slate-400 dark:text-slate-100 shadow-sm focus:outline-none focus:ring-2 focus:ring-white/40"
                            autocomplete="off"
                            spellcheck="false">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                        </div>
                        <button type="button" 
                            x-show="searchQuery.length > 0" 
                            @click="searchQuery = ''; showResults = false; $refs.mobileSearchInput.focus();" 
                            class="absolute inset-y-0 right-0 pr-2.5 flex items-center text-slate-400 hover:text-slate-600">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                </div>

                {{-- NORMAL NAVBAR CONTENT (Visible when mobileSearchOpen is false on mobile, always visible on sm:) --}}
                <div class="flex items-center gap-2 sm:gap-4 flex-1 min-w-0" :class="mobileSearchOpen ? 'invisible sm:visible' : ''">
                    <!-- Hamburger Button -->
                    <button @click="sidebarOpen = true"
                        class="md:hidden text-white/90 hover:text-white hover:bg-white/10 dark:hover:bg-black/20 focus:outline-none p-2 rounded-xl transition-all active:scale-95 shrink-0"
                        aria-label="Open sidebar menu">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                    
                    <!-- Header Title -->
                    <div class="flex items-center gap-2 min-w-0">
                        <h1 class="text-sm sm:text-base md:text-lg font-bold text-white tracking-tight truncate flex items-center gap-2">
                            @yield('header', __('Dashboard Overview'))
                        </h1>
                    </div>

                    <!-- Intelligent Search Bar (Desktop / Tablet view: sm and above) -->
                    <div class="hidden sm:block relative flex-1 max-w-md lg:max-w-xl mx-2 sm:mx-4 group">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-emerald-800/60 dark:text-emerald-300/70">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                        <input type="text" 
                            x-model="searchQuery" 
                            @focus="showResults = true"
                            @click="showResults = true"
                            @input="showResults = true"
                            @keydown.escape.stop="showResults = false; $el.blur()"
                            placeholder="Search navigation, actions, or modules..." 
                            class="no-uppercase w-full pl-9 pr-9 py-2 bg-white/95 hover:bg-white focus:bg-white dark:bg-slate-900/90 dark:hover:bg-slate-900 dark:focus:bg-slate-900 rounded-full border border-emerald-400/30 dark:border-emerald-700/50 shadow-inner focus:ring-2 focus:ring-white/40 dark:focus:ring-emerald-500/40 focus:outline-none text-xs sm:text-sm text-slate-800 placeholder-slate-400 dark:text-slate-100 dark:placeholder-slate-400 transition-all"
                            style="text-transform: none !important;"
                            autocomplete="off"
                            spellcheck="false">
                        
                        <!-- Clear Search Button -->
                        <button type="button" 
                            x-show="searchQuery.length > 0" 
                            @click="searchQuery = ''; showResults = false;" 
                            class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 cursor-pointer transition-colors"
                            aria-label="Clear search">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                </div>

                {{-- Right Side Actions --}}
                <div class="flex items-center gap-1.5 sm:gap-3 shrink-0" :class="mobileSearchOpen ? 'invisible sm:visible' : ''">
                    <!-- Mobile Search Trigger Button -->
                    <button type="button" 
                            @click="mobileSearchOpen = true; showResults = true; $nextTick(() => $refs.mobileSearchInput.focus());" 
                            class="sm:hidden text-white/90 hover:text-white hover:bg-white/10 dark:hover:bg-black/20 focus:outline-none p-2 rounded-xl transition-all active:scale-95"
                            aria-label="Search navigation">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </button>

                    <!-- Theme Toggle -->
                    @include('partials.theme-toggle')

                    <!-- Live Date Badge -->
                    <div class="hidden lg:flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-white/10 dark:bg-emerald-950/40 border border-white/15 dark:border-emerald-700/30 text-xs font-semibold text-emerald-50">
                        <svg class="w-3.5 h-3.5 text-emerald-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        <span>{{ now()->format('l, F j, Y') }}</span>
                    </div>
                </div>

                <!-- Search Dropdown (Responsive: Full-width anchored on mobile, nested under search bar on desktop) -->
                <div x-show="showResults && searchQuery.trim().length > 0" 
                    x-transition:enter="transition ease-out duration-75"
                    x-transition:enter-start="opacity-0 scale-98"
                    x-transition:enter-end="opacity-100 scale-100"
                    x-transition:leave="transition ease-in duration-50"
                    x-transition:leave-start="opacity-100 scale-100"
                    x-transition:leave-end="opacity-0 scale-98"
                    style="display: none;"
                    class="fixed inset-x-3 top-[68px] sm:absolute sm:inset-x-auto sm:top-full sm:left-1/2 sm:-translate-x-1/2 sm:w-full sm:max-w-xl z-50 bg-white dark:bg-slate-900 rounded-2xl shadow-2xl border border-slate-200/90 dark:border-slate-700/80 overflow-hidden py-2">
                    
                    <div class="px-4 py-2 border-b border-slate-100 dark:border-slate-800 text-[11px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500 flex items-center justify-between">
                        <span>Quick Navigation</span>
                        <button type="button" @click="showResults = false; mobileSearchOpen = false;" class="text-emerald-600 dark:text-emerald-400 hover:text-emerald-700 dark:hover:text-emerald-300 font-semibold text-[11px] cursor-pointer">Close ✕</button>
                    </div>

                    <template x-if="filteredLinks.length > 0">
                        <ul class="max-h-[60vh] sm:max-h-64 overflow-y-auto custom-scrollbar divide-y divide-slate-50 dark:divide-slate-800/40">
                            <template x-for="link in filteredLinks" :key="link.name">
                                <li>
                                    <a :href="link.route" @click="mobileSearchOpen = false; showResults = false;" class="flex items-center gap-3 px-4 py-2.5 hover:bg-emerald-50 dark:hover:bg-slate-800/80 text-sm transition-colors group/item">
                                        <div class="p-1.5 rounded-lg bg-emerald-100/80 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-400 group-hover/item:scale-105 transition-transform shrink-0">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7"></path></svg>
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <span class="font-bold text-slate-800 dark:text-white text-xs sm:text-sm block truncate" x-text="link.name"></span>
                                            <span class="block text-[11px] text-slate-500 dark:text-slate-400 truncate" x-text="link.desc || 'Quick navigation shortcut'"></span>
                                        </div>
                                    </a>
                                </li>
                            </template>
                        </ul>
                    </template>

                    <template x-if="filteredLinks.length === 0">
                        <div class="px-5 py-6 text-center">
                            <div class="w-10 h-10 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-400 dark:text-slate-500 mx-auto flex items-center justify-center mb-2.5">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7" />
                                </svg>
                            </div>
                            <p class="text-xs sm:text-sm font-bold text-slate-800 dark:text-slate-200">No results found</p>
                            <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5">We couldn't find any matches for "<span class="font-medium text-slate-700 dark:text-slate-300" x-text="searchQuery"></span>"</p>
                        </div>
                    </template>
                </div>
            </div>
        </header>

        <main class="print:overflow-visible print:p-0 flex-1 overflow-y-auto flex flex-col p-4 sm:p-6 lg:p-8 custom-scrollbar bg-slate-100/90 dark:bg-slate-900/95 transition-colors duration-200">
            <div class="flex-1 w-full max-w-7xl mx-auto flex flex-col">
                @include('partials.toast')
                @yield('content')
            </div>

            <!-- Modern Unified Admin Footer -->
            <footer class="mt-auto pt-8 pb-4 border-t border-slate-200/80 dark:border-slate-800 text-xs text-slate-500 dark:text-slate-400">
                <div class="w-full max-w-7xl mx-auto flex flex-col sm:flex-row justify-between items-center gap-3 text-center sm:text-left">
                    <p>&copy; {{ date('Y') }} Rural Health Unit &ndash; Silang. Admin Portal. All rights reserved.</p>
                    <div class="flex items-center gap-4 text-xs">
                        <a href="{{ route('admin.dashboard') }}" class="hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors">Dashboard</a>
                        <span class="text-slate-300 dark:text-slate-700">•</span>
                        <a href="{{ route('admin.announcements.index') }}" class="hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors">Announcements</a>
                        <span class="text-slate-300 dark:text-slate-700">•</span>
                        <a href="{{ route('welcome') }}" target="_blank" class="hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors inline-flex items-center gap-1">
                            <span>Public Portal</span>
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" /></svg>
                        </a>
                        <span class="text-slate-300 dark:text-slate-700 hidden md:inline">•</span>
                        <span class="hidden md:inline-flex items-center gap-1.5 font-medium text-emerald-600 dark:text-emerald-400">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                            System Online
                        </span>
                    </div>
                </div>
            </footer>
        </main>
    </div>

    <!-- Global Confirmation Modal -->
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
            
            // Polling Engine (every 15s)
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