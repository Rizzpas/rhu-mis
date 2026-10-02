<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="bg-gray-100 dark:bg-gray-900">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>RHU - Silang | Front Desk Portal</title>
    <link rel="icon" type="image/png" href="{{ asset('assets/images/logo.png') }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

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
        html {
            background-color: #f3f4f6 !important;
        }
        .dark html, html.dark {
            background-color: #111827 !important;
        }
        body {
            font-family: 'Inter', sans-serif;
            letter-spacing: -0.025em;
            background-color: #f3f4f6;
        }
        .dark body {
            background-color: #111827;
        }
        [x-cloak] { display: none !important; }
        
        /* Globally remove focus rings, borders halos, and outlines across all frontdesk pages */
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

        /* Option tags dark and light mode styling */
        option {
            background-color: #ffffff;
            color: #1e293b;
        }
        .dark option {
            background-color: #0f172a;
            color: #f1f5f9;
        }

        /* MIS Global Uppercase */
        input[type="text"]:not(.no-uppercase), 
        input[type="search"]:not(.no-uppercase), 
        textarea:not(.no-uppercase) {
            text-transform: uppercase;
        }
        input[type="text"]:not(.no-uppercase)::placeholder, 
        input[type="search"]:not(.no-uppercase)::placeholder, 
        textarea:not(.no-uppercase)::placeholder {
            text-transform: none;
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

        /* Custom subtle scrollbar */
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

<body class="print:h-auto print:overflow-visible bg-gray-100 dark:bg-gray-900 flex h-screen overflow-hidden text-slate-800 dark:text-slate-100" x-data="{ 
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
        class="fixed inset-0 bg-slate-950/60 backdrop-blur-xs z-20 md:hidden" style="display: none;"></div>

    <!-- Sidebar -->
    <div :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
        class="print:hidden fixed inset-y-0 left-0 z-30 w-64 bg-white dark:bg-gray-800 border-r border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-300 flex flex-col shrink-0 transition-transform duration-300 transform md:relative md:translate-x-0 shadow-lg">

        <!-- Sidebar Header -->
        <div class="px-5 py-5 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <img src="{{ asset('assets/images/logo.png') }}" alt="RHU Logo" class="w-9 h-9 object-contain shrink-0">
                <div>
                    <p class="text-sm font-bold text-gray-800 dark:text-white leading-tight uppercase whitespace-nowrap">Rural Health Unit</p>
                    <p class="text-xs text-gray-400 leading-tight">Front Desk Portal</p>
                </div>
            </div>
            <button @click="sidebarOpen = false" class="md:hidden p-1.5 rounded-lg text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
            </button>
        </div>

        <!-- Navigation -->
        <nav class="flex-1 px-4 py-4 space-y-1 text-sm overflow-y-auto custom-scrollbar">
            <a href="{{ route('frontdesk.dashboard') }}"
                class="@if(request()->routeIs('frontdesk.dashboard')) bg-gradient-to-r from-emerald-600 to-emerald-800 text-white shadow-md shadow-emerald-600/20 font-semibold @else text-gray-900 hover:bg-gray-100 dark:hover:bg-gray-700 dark:text-white @endif group flex items-center px-3 py-2.5 rounded-xl transition-all">
                <svg class="mr-3 h-4 w-4 @if(request()->routeIs('frontdesk.dashboard')) text-white @else text-emerald-600 dark:text-emerald-400 @endif shrink-0 transition-transform group-hover:scale-110" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                </svg>
                Dashboard
            </a>

            <a href="{{ route('frontdesk.registration.index') }}"
                class="@if(request()->routeIs('frontdesk.registration.*')) bg-gradient-to-r from-emerald-600 to-emerald-800 text-white shadow-md shadow-emerald-600/20 font-semibold @else text-gray-900 hover:bg-gray-100 dark:hover:bg-gray-700 dark:text-white @endif group flex items-center px-3 py-2.5 rounded-xl transition-all">
                <svg class="mr-3 h-4 w-4 @if(request()->routeIs('frontdesk.registration.*')) text-white @else text-emerald-600 dark:text-emerald-400 @endif shrink-0 transition-transform group-hover:scale-110" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                </svg>
                Registration &amp; Triage
            </a>

            <a href="{{ route('frontdesk.queue-overview') }}"
                class="@if(request()->routeIs('frontdesk.queue-overview')) bg-gradient-to-r from-emerald-600 to-emerald-800 text-white shadow-md shadow-emerald-600/20 font-semibold @else text-gray-900 hover:bg-gray-100 dark:hover:bg-gray-700 dark:text-white @endif group flex items-center px-3 py-2.5 rounded-xl transition-all">
                <svg class="mr-3 h-4 w-4 @if(request()->routeIs('frontdesk.queue-overview')) text-white @else text-emerald-600 dark:text-emerald-400 @endif shrink-0 transition-transform group-hover:scale-110" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                </svg>
                Queue Overview
            </a>

            <a href="{{ route('frontdesk.patients.index') }}"
                class="@if(request()->routeIs('frontdesk.patients.*')) bg-gradient-to-r from-emerald-600 to-emerald-800 text-white shadow-md shadow-emerald-600/20 font-semibold @else text-gray-900 hover:bg-gray-100 dark:hover:bg-gray-700 dark:text-white @endif group flex items-center px-3 py-2.5 rounded-xl transition-all">
                <svg class="mr-3 h-4 w-4 @if(request()->routeIs('frontdesk.patients.*')) text-white @else text-emerald-600 dark:text-emerald-400 @endif shrink-0 transition-transform group-hover:scale-110" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
                Patient Master List
            </a>

            <a href="{{ route('frontdesk.followups.index') }}"
                class="@if(request()->routeIs('frontdesk.followups.*')) bg-gradient-to-r from-emerald-600 to-emerald-800 text-white shadow-md shadow-emerald-600/20 font-semibold @else text-gray-900 hover:bg-gray-100 dark:hover:bg-gray-700 dark:text-white @endif group flex items-center justify-between px-3 py-2.5 rounded-xl transition-all">
                <div class="flex items-center">
                    <svg class="mr-3 h-4 w-4 @if(request()->routeIs('frontdesk.followups.*')) text-white @else text-emerald-600 dark:text-emerald-400 @endif shrink-0 transition-transform group-hover:scale-110" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <span>Follow-Up Tracker</span>
                </div>
                @php
                    $pendingFollowupsBadge = \App\Models\Consultation::where('is_followup_needed', true)->whereNull('followup_completed_at')->whereDate('followup_date', '<=', today())->count();
                @endphp
                @if($pendingFollowupsBadge > 0)
                    <span class="inline-flex items-center px-1.5 py-0.5 rounded-full text-[10px] font-black {{ request()->routeIs('frontdesk.followups.*') ? 'bg-white text-emerald-800' : 'bg-rose-500 text-white animate-pulse' }} shadow-2xs">
                        {{ $pendingFollowupsBadge }}
                    </span>
                @endif
            </a>

            @if(auth()->check() && auth()->user()->hasRole('admin', 'super_admin'))
                <a href="{{ route('admin.dashboard') }}"
                    class="text-gray-900 hover:bg-gray-100 dark:hover:bg-gray-700 dark:text-white group flex items-center px-3 py-2.5 rounded-xl transition-colors mt-4 border-t border-gray-100 dark:border-gray-700 pt-4 font-semibold">
                    <svg class="mr-3 h-4 w-4 text-emerald-600 dark:text-emerald-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Back to Admin
                </a>
            @endif
        </nav>

        <!-- Bottom User Panel -->
        <div class="border-t border-gray-100 dark:border-gray-700 px-6 py-6">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 rounded-full bg-emerald-100 dark:bg-emerald-900/40 flex items-center justify-center shrink-0 overflow-hidden border border-emerald-200 dark:border-emerald-800 shadow-2xs">
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
            <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 px-3 py-2 rounded-xl text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 mb-1 transition-colors">
                <svg class="w-4 h-4 text-gray-700 dark:text-gray-300 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                <span>Settings</span>
            </a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full flex items-center gap-3 px-3 py-2 rounded-xl text-sm text-red-500 hover:bg-red-500 hover:text-white dark:hover:bg-red-800 dark:hover:text-white transition-colors cursor-pointer">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" /></svg>
                    <span>Logout</span>
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
                links: [
                    { name: 'Dashboard Overview', route: '{{ route('frontdesk.dashboard') }}', desc: 'Calendar, arrivals & queue stats', keywords: ['dashboard', 'home', 'overview', 'calendar', 'appointments'] },
                    { name: 'Registration & Triage', route: '{{ route('frontdesk.registration.index') }}', desc: 'Frontline intake & patient check-in', keywords: ['registration', 'triage', 'patient check-in', 'new patient', 'vitals'] },
                    { name: 'Queue Overview', route: '{{ route('frontdesk.queue-overview') }}', desc: 'Live physician rooms & holding queue', keywords: ['queue', 'waiting list', 'overview', 'status', 'doctors', 'nurses'] },
                    { name: 'Patient Master List', route: '{{ route('frontdesk.patients.index') }}', desc: 'Directory, clinical records & intake routing', keywords: ['patients', 'records', 'master list', 'search patient', 'clinical'] },
                    { name: 'Follow-Up Tracker', route: '{{ route('frontdesk.followups.index') }}', desc: 'Scheduled return visits & overdue checkups', keywords: ['followup', 'follow-up', 'overdue', 'returns', 'schedule', 'tracking'] },
                    { name: 'Profile Settings', route: '{{ route('profile.edit') }}', desc: 'Account credentials & duty profile', keywords: ['profile', 'account', 'settings', 'password'] }
                ],
                get filteredLinks() {
                    if (this.searchQuery.trim() === '') return [];
                    const query = this.searchQuery.toLowerCase().trim();
                    return this.links.filter(link => {
                        return link.name.toLowerCase().includes(query) || (link.desc && link.desc.toLowerCase().includes(query)) || link.keywords.some(k => k.includes(query));
                    });
                }
            }" @click.away="showResults = false">
            
            <div class="flex justify-between items-center px-4 sm:px-6 py-2.5 sm:py-3 min-h-[64px] gap-3 sm:gap-4">
                <div class="flex items-center gap-3 sm:gap-4 flex-1 min-w-0">
                    <button @click="sidebarOpen = true"
                        class="md:hidden text-white/90 hover:text-white hover:bg-white/10 dark:hover:bg-black/20 focus:outline-none p-2 rounded-xl transition-all active:scale-95 shrink-0"
                        aria-label="Open sidebar menu">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" /></svg>
                    </button>
                    
                    <div class="flex items-center gap-2.5 min-w-0 shrink-0">
                        <h1 class="text-sm sm:text-base md:text-lg font-bold text-white tracking-tight truncate max-w-[150px] sm:max-w-xs md:max-w-sm lg:max-w-md flex items-center gap-2">
                            @yield('header', 'Front Desk Dashboard')
                        </h1>
                    </div>

                    <!-- Intelligent Search Bar -->
                    <div class="relative flex-1 max-w-md lg:max-w-xl mx-1 sm:mx-4 group">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-emerald-800/60 dark:text-emerald-300/70">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                        </div>
                        <input type="text" 
                            x-model="searchQuery" 
                            @focus="showResults = true" 
                            @click="showResults = true"
                            @input="showResults = true"
                            @keydown.escape.stop="showResults = false; $el.blur()" 
                            placeholder="Search patient records, queues, or links..." 
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
                        
                        <!-- Search Dropdown -->
                        <div x-show="showResults && searchQuery.trim().length > 0" 
                            x-transition:enter="transition ease-out duration-75"
                            x-transition:enter-start="opacity-0 scale-98"
                            x-transition:enter-end="opacity-100 scale-100"
                            x-transition:leave="transition ease-in duration-50"
                            x-transition:leave-start="opacity-100 scale-100"
                            x-transition:leave-end="opacity-0 scale-98"
                            style="display: none;" 
                            class="absolute z-50 mt-2 w-full bg-white dark:bg-slate-900 rounded-2xl shadow-2xl border border-slate-200/90 dark:border-slate-700/80 overflow-hidden top-full left-0 py-2">
                            
                            <div class="px-4 py-1.5 border-b border-slate-100 dark:border-slate-800 text-[11px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500 flex items-center justify-between">
                                <span>Quick Navigation</span>
                                <button type="button" @click="showResults = false" class="text-emerald-600 dark:text-emerald-400 hover:text-emerald-700 dark:hover:text-emerald-300 font-semibold lowercase text-[10px] cursor-pointer">Press Esc to close</button>
                            </div>

                            <template x-if="filteredLinks.length > 0">
                                <ul class="max-h-64 overflow-y-auto custom-scrollbar divide-y divide-slate-50 dark:divide-slate-800/40">
                                    <template x-for="link in filteredLinks" :key="link.name">
                                        <li>
                                            <a :href="link.route" class="flex items-center gap-3 px-4 py-2.5 hover:bg-emerald-50 dark:hover:bg-slate-800/80 text-sm transition-colors group/item">
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
                </div>

                <div class="flex items-center gap-2 sm:gap-3 shrink-0">
                    <!-- Theme Toggle -->
                    @include('partials.theme-toggle')

                    <!-- Live Date Badge -->
                    <div class="hidden lg:flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-white/10 dark:bg-emerald-950/40 border border-white/15 dark:border-emerald-700/30 text-xs font-semibold text-emerald-50">
                        <svg class="w-3.5 h-3.5 text-emerald-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        <span>{{ now()->format('l, F j, Y') }}</span>
                    </div>
                </div>
            </div>
        </header>

        <main class="print:overflow-visible print:p-0 flex-1 overflow-y-auto flex flex-col p-4 sm:p-6 lg:p-8 custom-scrollbar bg-slate-100/90 dark:bg-slate-900/95 transition-colors duration-200">
            <div class="flex-1 w-full max-w-7xl mx-auto flex flex-col">
                @include('partials.toast')
                @if ($errors->any())
                    <div class="mb-5 bg-red-50 dark:bg-red-950/40 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-300 p-4 rounded-2xl shadow-xs">
                        <p class="font-bold mb-1 text-sm">Please correct the following errors:</p>
                        <ul class="list-disc list-inside text-xs space-y-0.5">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                @yield('content')
            </div>

            <!-- Modern Unified Frontdesk Footer -->
            <footer class="mt-auto pt-8 pb-4 border-t border-slate-200/80 dark:border-slate-800 text-xs text-slate-500 dark:text-slate-400">
                <div class="w-full max-w-7xl mx-auto flex flex-col sm:flex-row justify-between items-center gap-3 text-center sm:text-left">
                    <p>&copy; {{ date('Y') }} Rural Health Unit &ndash; Silang. Information Desk Portal. All rights reserved.</p>
                    <div class="flex items-center gap-4 text-xs">
                        <a href="{{ route('frontdesk.dashboard') }}" class="hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors">Dashboard</a>
                        <span class="text-slate-300 dark:text-slate-700">•</span>
                        <a href="{{ route('frontdesk.registration.index') }}" class="hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors">Registration</a>
                        <span class="text-slate-300 dark:text-slate-700">•</span>
                        <span class="inline-flex items-center gap-1.5 font-medium text-emerald-600 dark:text-emerald-400">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                            Desk Active
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
                            Cancel
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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    @stack('scripts')

    @include('partials.chat-widget')
</body>

</html>