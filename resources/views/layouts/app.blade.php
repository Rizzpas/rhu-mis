<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Rural Health Unit - Silang</title>
    <link rel="icon" type="image/png" href="{{ asset('assets/images/logo.png') }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=DM+Sans:wght@700;800&family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    @stack('head')

    <!-- Flatpickr for better date pickers -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

    <!-- Styles & Scripts -->
    <link rel="stylesheet" href="{{ asset('css/print.css') }}">
    <script src="{{ asset('js/print-helper.js') }}"></script>
    <script src="{{ asset('js/secure-image-validator.js') }}"></script>
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <script>
            tailwind.config = {
                darkMode: 'class',
                theme: {
                    extend: {
                        colors: {
                            teal: {
                                50: '#f0fdfa',
                                100: '#ccfbf1',
                                500: '#14b8a6',
                                600: '#0d9488',
                                900: '#134e4a',
                            }
                        },
                        fontFamily: {
                            sans: ['Inter', 'Outfit', 'sans-serif'],
                        }
                    }
                }
            }
        </script>
    @endif

    <style>
        body {
            font-family: 'Inter', 'Outfit', sans-serif;
            letter-spacing: -0.025em;
            background-color: #faf8f2;
        }

        .glass {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.3);
        }

        /* --- Global MIS Capitalization Logic --- */
        input[type="text"]:not(.no-uppercase):not(.raw-text),
        input[type="search"]:not(.no-uppercase):not(.raw-text),
        textarea:not(.no-uppercase):not(.raw-text) {
            text-transform: capitalize;
        }

        /* Custom subtle scrollbar (matches Frontdesk & Admin portals) */
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

<body
    class="antialiased bg-[#faf8f2] dark:bg-[#081a1c] text-gray-800 dark:text-gray-100 font-sans relative custom-scrollbar"
    x-data="{ 
            open: false,
            title: '',
            message: '',
            callback: null, // For when we need to run JS instead of form submit
            action: '',
            method: 'POST',
            confirmText: 'Confirm',
            confirmClass: 'bg-emerald-600 hover:bg-emerald-700 shadow-emerald-600/20',
            iconBgClass: 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400',
            showPrivacy: false,
            showFaq: false,
            showTerms: false,
            
            show(detail) {
                this.title = detail.title || 'Confirm Action';
                this.message = detail.message || 'Are you sure you want to proceed?';
                this.callback = detail.callback; // Function reference or event name
                this.action = detail.action || '';
                this.method = detail.method || 'POST';
                this.confirmText = detail.confirmText || 'Yes, Proceed';
                if(detail.type === 'danger') {
                    this.confirmClass = 'bg-rose-600 hover:bg-rose-700 shadow-rose-600/20';
                    this.iconBgClass = 'bg-rose-100 dark:bg-rose-900/30 text-rose-600 dark:text-rose-400';
                } else {
                    this.confirmClass = 'bg-emerald-600 hover:bg-emerald-700 shadow-emerald-600/20';
                    this.iconBgClass = 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400';
                }
                this.open = true;
            },
            
            confirm() {
                if (this.callback) {
                    // Dispatch event if callback is string, or execute if function (hard to pass func from event)
                    // We will assume callback is a custom event name to dispatch back to the component
                    window.dispatchEvent(new CustomEvent(this.callback));
                }
                this.open = false;
            }
        }" @open-confirmation.window="show($event.detail)">

    {{-- Global Aura Gradient: "Frosted Jade" Background --}}
    <div class="aura-bg" aria-hidden="true">
        <div class="aura-layer-1"></div>
        <div class="aura-layer-2"></div>
        <div class="aura-layer-3"></div>
        <div class="aura-layer-4"></div>
    </div>

    @include('partials.skeleton-app')
    <!-- Top Bar — Official Republic & Municipal Header -->
    <div class="bg-emerald-900 text-white text-[11px] sm:text-xs py-2 px-4 md:px-8 flex flex-wrap justify-between items-center font-medium gap-2 sm:gap-4 relative z-10 border-b border-emerald-800/60">
        <div class="hidden md:flex items-center gap-2 mx-auto md:mx-0">
            <span class="font-bold tracking-wide uppercase">{{ \App\Models\SiteSetting::get('topbar_republic', 'Republic of the Philippines') }}</span>
            <span class="text-emerald-400 opacity-60">•</span>
            <span class="text-emerald-200">{{ \App\Models\SiteSetting::get('topbar_province', 'Province of Cavite') }}</span>
            <span class="text-emerald-400 opacity-60">•</span>
            <span class="text-emerald-200 font-semibold">{{ \App\Models\SiteSetting::get('topbar_municipality', 'Municipality of Silang') }}</span>
        </div>
        <div class="flex items-center gap-4 text-emerald-100/90 mx-auto md:mx-0">
            <span><span class="hidden sm:inline text-emerald-300/80">Clinic Hours: </span>{{ \App\Models\SiteSetting::get('clinic_hours', 'Mon - Fri | 8:00 AM - 5:00 PM') }}</span>
            <span class="text-emerald-400 opacity-60">•</span>
            <span><span class="hidden sm:inline text-emerald-300/80">Hotlines: </span><span class="font-bold text-white">{{ \App\Models\SiteSetting::get('emergency_hotlines', '911 | (046) 432-1234') }}</span></span>
        </div>
    </div>

    <!-- Main Navigation -->
    <nav id="main-navbar" class="bg-white/95 dark:bg-slate-900/95 backdrop-blur-md sticky w-full z-50 top-0 start-0 border-b border-slate-200/90 dark:border-slate-800/90 shadow-2xs">
        <div class="flex flex-wrap justify-between items-center mx-auto max-w-7xl px-4 py-3.5 md:px-8">
            <a href="{{ route('welcome') }}" class="flex items-center gap-3 rtl:space-x-reverse">
                <div class="shrink-0">
                    <img src="{{ asset('assets/images/logo.png') }}" alt="RHU Official Seal"
                        class="w-11 h-11 object-contain bg-white dark:bg-slate-800 rounded-full p-0.5 shadow-2xs border border-slate-200/80 dark:border-slate-700/80" />
                </div>
                <div>
                    <h1 class="font-extrabold text-base sm:text-lg leading-tight text-slate-900 dark:text-white tracking-tight">RURAL HEALTH UNIT</h1>
                    <p class="text-[10px] text-emerald-700 dark:text-emerald-400 font-bold tracking-widest uppercase">Municipality of Silang, Cavite</p>
                </div>
            </a>
            <button data-collapse-toggle="mega-menu-full" type="button"
                class="inline-flex items-center justify-center w-10 h-10 text-slate-600 dark:text-slate-300 rounded-xl md:hidden bg-slate-100/80 dark:bg-slate-800/80 hover:bg-emerald-50 dark:hover:bg-emerald-950/40 hover:text-emerald-600 dark:hover:text-emerald-400 border border-slate-200/80 dark:border-slate-700/80 focus:outline-none focus:ring-2 focus:ring-emerald-500/30 transition-all cursor-pointer shadow-2xs"
                aria-controls="mega-menu-full" aria-expanded="false">
                <span class="sr-only">Toggle navigation</span>
                <svg class="w-5 h-5 hamburger-open-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
                <svg class="w-5 h-5 hamburger-close-icon hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
            <div id="mega-menu-full" class="items-center justify-between hidden w-full md:flex md:w-auto md:order-1">
                <!-- DESKTOP NAVIGATION (Visible on md screens and up) -->
                <ul class="hidden md:flex flex-row space-x-8 items-center font-medium">
                    <li>
                        <a href="{{ route('welcome') }}"
                            class="block py-2 px-3 text-slate-800 dark:text-slate-200 hover:text-emerald-700 dark:hover:text-emerald-400 font-semibold md:p-0 transition text-sm"
                            aria-current="page">Home</a>
                    </li>
                    <li>
                        <button id="mega-menu-full-dropdown-button" data-collapse-toggle="mega-menu-full-dropdown"
                            class="flex items-center justify-between w-full py-2 px-3 font-semibold text-slate-800 dark:text-slate-200 md:w-auto hover:text-emerald-700 dark:hover:text-emerald-400 md:p-0 transition text-sm cursor-pointer">
                            RHU Facilities
                            <svg class="w-4 h-4 ms-1 transition-transform duration-200" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 9-7 7-7-7" />
                            </svg>
                        </button>
                    </li>
                    <li>
                        <button id="mega-menu-appointment-dropdown-button" data-collapse-toggle="mega-menu-appointment-dropdown"
                            class="flex items-center justify-between w-full py-2 px-3 font-semibold text-slate-800 dark:text-slate-200 md:w-auto hover:text-emerald-700 dark:hover:text-emerald-400 md:p-0 transition text-sm cursor-pointer">
                            Appointments
                            <svg class="w-4 h-4 ms-1 transition-transform duration-200" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 9-7 7-7-7" />
                            </svg>
                        </button>
                    </li>

                    <li>
                        <button id="mega-menu-updates-dropdown-button" data-collapse-toggle="mega-menu-updates-dropdown"
                            class="flex items-center justify-between w-full py-2 px-3 font-semibold text-slate-800 dark:text-slate-200 md:w-auto hover:text-emerald-700 dark:hover:text-emerald-400 md:p-0 transition text-sm cursor-pointer">
                            About & Bulletins
                            <svg class="w-4 h-4 ms-1 transition-transform duration-200" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 9-7 7-7-7" />
                            </svg>
                        </button>
                    </li>

                    <!-- Desktop Theme Toggle -->
                    <li>
                        @include('partials.theme-toggle', ['id' => 'theme-toggle'])
                    </li>
                    <!-- Desktop Book Appointment CTA -->
                    <li>
                        <a href="{{ route('appointment.create') }}"
                            class="inline-flex items-center justify-center gap-2 px-4 py-2 text-xs sm:text-sm font-semibold text-white bg-emerald-700 hover:bg-emerald-800 active:bg-emerald-900 rounded-xl shadow-xs transition-all duration-200">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
                            </svg>
                            <span>Book Appointment</span>
                        </a>
                    </li>
                </ul>

                <!-- MOBILE NAVIGATION CARD (Visible on mobile screens only) -->
                <div class="block md:hidden w-full mt-3 p-3.5 sm:p-4 rounded-2xl bg-white/95 dark:bg-slate-900/95 backdrop-blur-xl border border-slate-200/90 dark:border-slate-800/90 shadow-2xl space-y-1.5">
                    
                    <!-- Mobile: Home -->
                    <a href="{{ route('welcome') }}"
                        class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-bold text-slate-800 dark:text-slate-100 hover:bg-emerald-50 dark:hover:bg-emerald-950/40 hover:text-emerald-700 dark:hover:text-emerald-300 transition-colors">
                        <span class="w-8 h-8 rounded-lg bg-emerald-100 dark:bg-emerald-900/40 text-emerald-700 dark:text-emerald-400 flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                        </span>
                        <span>Home</span>
                    </a>

                    <!-- Mobile: RHU Facilities Accordion -->
                    <div x-data="{ open: false }">
                        <button type="button" @click="open = !open"
                            class="w-full flex items-center justify-between px-3.5 py-2.5 rounded-xl text-sm font-bold text-slate-800 dark:text-slate-100 hover:bg-emerald-50 dark:hover:bg-emerald-950/40 hover:text-emerald-700 dark:hover:text-emerald-300 transition-colors cursor-pointer">
                            <div class="flex items-center gap-3">
                                <span class="w-8 h-8 rounded-lg bg-teal-100 dark:bg-teal-900/40 text-teal-700 dark:text-teal-400 flex items-center justify-center shrink-0">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                </span>
                                <span>RHU Facilities</span>
                            </div>
                            <svg class="w-4 h-4 text-slate-400 transition-transform duration-200" :class="open ? 'rotate-180 text-emerald-600 dark:text-emerald-400' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 9-7 7-7-7" />
                            </svg>
                        </button>

                        <!-- Collapsible Facility Links -->
                        <div x-show="open" x-collapse x-transition
                            class="mt-1.5 p-1.5 rounded-xl bg-slate-50/80 dark:bg-slate-800/50 border border-slate-200/60 dark:border-slate-800/80 space-y-1">
                            @forelse($globalFacilityUnits as $fUnit)
                                <a href="{{ route('units.show', $fUnit->slug) }}"
                                    class="flex items-center gap-3 p-2 rounded-lg hover:bg-white dark:hover:bg-slate-700/60 transition-all group">
                                    <span class="shrink-0 w-8 h-8 rounded-lg bg-emerald-100 dark:bg-emerald-900/40 flex items-center justify-center text-emerald-700 dark:text-emerald-400 group-hover:bg-emerald-600 group-hover:text-white transition-colors overflow-hidden border border-emerald-200/50 dark:border-emerald-800/50">
                                        @if($fUnit->image_url)
                                            <img src="{{ $fUnit->image_url }}" alt="" class="w-full h-full object-cover">
                                        @else
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                        @endif
                                    </span>
                                    <div class="flex-1 min-w-0">
                                        <span class="block text-xs font-bold text-slate-800 dark:text-slate-100 group-hover:text-emerald-600 dark:group-hover:text-emerald-400 transition-colors truncate">{{ $fUnit->name }}</span>
                                        <span class="block text-[10px] text-slate-500 dark:text-slate-400 truncate">{{ $fUnit->category ?: ($fUnit->operating_hours ?: 'Healthcare Facility') }}</span>
                                    </div>
                                </a>
                            @empty
                                <div class="px-3 py-2 text-xs text-slate-400 italic">No facilities available</div>
                            @endforelse
                            <div class="pt-1 px-1 border-t border-slate-200/50 dark:border-slate-700/50">
                                <a href="{{ route('units.index') }}"
                                    class="flex items-center justify-between px-2.5 py-1.5 rounded-lg text-xs font-bold text-emerald-600 dark:text-emerald-400 hover:bg-emerald-50 dark:hover:bg-emerald-950/30 transition">
                                    <span>View All Facilities</span>
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Mobile: Appointments Accordion -->
                    <div x-data="{ open: false }">
                        <button type="button" @click="open = !open"
                            class="w-full flex items-center justify-between px-3.5 py-2.5 rounded-xl text-sm font-bold text-slate-800 dark:text-slate-100 hover:bg-emerald-50 dark:hover:bg-emerald-950/40 hover:text-emerald-700 dark:hover:text-emerald-300 transition-colors cursor-pointer">
                            <div class="flex items-center gap-3">
                                <span class="w-8 h-8 rounded-lg bg-green-100 dark:bg-green-900/40 text-green-700 dark:text-green-400 flex items-center justify-center shrink-0">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                </span>
                                <span>Appointments</span>
                            </div>
                            <svg class="w-4 h-4 text-slate-400 transition-transform duration-200" :class="open ? 'rotate-180 text-emerald-600 dark:text-emerald-400' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 9-7 7-7-7" />
                            </svg>
                        </button>

                        <!-- Collapsible Appointment Links -->
                        <div x-show="open" x-collapse x-transition
                            class="mt-1.5 p-1.5 rounded-xl bg-slate-50/80 dark:bg-slate-800/50 border border-slate-200/60 dark:border-slate-800/80 space-y-1">
                            <a href="{{ route('appointment.create') }}"
                                class="flex items-center gap-3 p-2 rounded-lg hover:bg-white dark:hover:bg-slate-700/60 transition-all group">
                                <span class="shrink-0 w-8 h-8 rounded-lg bg-green-100 dark:bg-green-900/40 flex items-center justify-center text-green-600 dark:text-green-400 group-hover:bg-green-600 group-hover:text-white transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                                </span>
                                <div class="flex-1 min-w-0">
                                    <span class="block text-xs font-bold text-slate-800 dark:text-slate-100 group-hover:text-emerald-600 dark:group-hover:text-emerald-400 transition-colors">Book New Appointment</span>
                                    <span class="block text-[10px] text-slate-500 dark:text-slate-400 truncate">Schedule your consultation online</span>
                                </div>
                            </a>
                            <a href="{{ route('appointment.manage') }}"
                                class="flex items-center gap-3 p-2 rounded-lg hover:bg-white dark:hover:bg-slate-700/60 transition-all group">
                                <span class="shrink-0 w-8 h-8 rounded-lg bg-teal-100 dark:bg-teal-900/40 flex items-center justify-center text-teal-600 dark:text-teal-400 group-hover:bg-teal-600 group-hover:text-white transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z"/></svg>
                                </span>
                                <div class="flex-1 min-w-0">
                                    <span class="block text-xs font-bold text-slate-800 dark:text-slate-100 group-hover:text-emerald-600 dark:group-hover:text-emerald-400 transition-colors">Manage Appointment</span>
                                    <span class="block text-[10px] text-slate-500 dark:text-slate-400 truncate">Check status, reschedule or cancel</span>
                                </div>
                            </a>
                        </div>
                    </div>

                    <!-- Mobile: About & Bulletins Accordion -->
                    <div x-data="{ open: false }">
                        <button type="button" @click="open = !open"
                            class="w-full flex items-center justify-between px-3.5 py-2.5 rounded-xl text-sm font-bold text-slate-800 dark:text-slate-100 hover:bg-emerald-50 dark:hover:bg-emerald-950/40 hover:text-emerald-700 dark:hover:text-emerald-300 transition-colors cursor-pointer">
                            <div class="flex items-center gap-3">
                                <span class="w-8 h-8 rounded-lg bg-emerald-100 dark:bg-emerald-900/40 text-emerald-700 dark:text-emerald-400 flex items-center justify-center shrink-0">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/></svg>
                                </span>
                                <span>About & Bulletins</span>
                            </div>
                            <svg class="w-4 h-4 text-slate-400 transition-transform duration-200" :class="open ? 'rotate-180 text-emerald-600 dark:text-emerald-400' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 9-7 7-7-7" />
                            </svg>
                        </button>

                        <!-- Collapsible About Links -->
                        <div x-show="open" x-collapse x-transition
                            class="mt-1.5 p-1.5 rounded-xl bg-slate-50/80 dark:bg-slate-800/50 border border-slate-200/60 dark:border-slate-800/80 space-y-1">
                            <a href="{{ route('announcements.index') }}"
                                class="flex items-center gap-3 p-2 rounded-lg hover:bg-white dark:hover:bg-slate-700/60 transition-all group">
                                <span class="shrink-0 w-8 h-8 rounded-lg bg-green-100 dark:bg-green-900/40 flex items-center justify-center text-green-600 dark:text-green-400 group-hover:bg-green-600 group-hover:text-white transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10.34 15.84c-.063.046-.129.088-.198.127a3.75 3.75 0 01-4.088-.288L3.25 13.5A2.25 2.25 0 012.25 11.75v-1.5a2.25 2.25 0 011-1.75l2.804-2.179a3.75 3.75 0 014.088-.288c.069.04.135.081.198.127m0 9.68l4.41 4.41a1.5 1.5 0 002.122 0l1.414-1.414a1.5 1.5 0 000-2.122L12 14.004m-1.66 1.836V7.996m0 0a3.75 3.75 0 013.75-3.75h1.5a2.25 2.25 0 012.25 2.25v6a2.25 2.25 0 01-2.25 2.25h-1.5a3.75 3.75 0 01-3.75-3.75z"/>
                                    </svg>
                                </span>
                                <div class="flex-1 min-w-0">
                                    <span class="block text-xs font-bold text-slate-800 dark:text-slate-100 group-hover:text-emerald-600 dark:group-hover:text-emerald-400 transition-colors">Announcements</span>
                                    <span class="block text-[10px] text-slate-500 dark:text-slate-400 truncate">Health advisories & community alerts</span>
                                </div>
                            </a>
                            <a href="{{ route('about') }}"
                                class="flex items-center gap-3 p-2 rounded-lg hover:bg-white dark:hover:bg-slate-700/60 transition-all group">
                                <span class="shrink-0 w-8 h-8 rounded-lg bg-emerald-100 dark:bg-emerald-900/40 flex items-center justify-center text-emerald-600 dark:text-emerald-400 group-hover:bg-emerald-600 group-hover:text-white transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 21a9.004 9.004 0 008.716-6.747M12 21a9.004 9.004 0 01-8.716-6.747M12 21c2.485 0 4.5-4.03 4.5-9S14.485 3 12 3m0 18c-2.485 0-4.5-4.03-4.5-9S9.515 3 12 3m0 0a8.997 8.997 0 017.843 4.582M12 3a8.997 8.997 0 00-7.843 4.582m15.686 0A11.953 11.953 0 0112 10.5c-2.998 0-5.74-1.1-7.843-2.918m15.686 0A8.959 8.959 0 0121 12c0 .778-.099 1.533-.284 2.253m0 0A17.919 17.919 0 0112 16.5c-3.162 0-6.133-.815-8.716-2.247m0 0A9.015 9.015 0 013 12c0-.778.099-1.533.284-2.253"/>
                                    </svg>
                                </span>
                                <div class="flex-1 min-w-0">
                                    <span class="block text-xs font-bold text-slate-800 dark:text-slate-100 group-hover:text-emerald-600 dark:group-hover:text-emerald-400 transition-colors">About RHU Silang</span>
                                    <span class="block text-[10px] text-slate-500 dark:text-slate-400 truncate">Mission, facilities & healthcare services</span>
                                </div>
                            </a>
                        </div>
                    </div>

                    <!-- Section Divider -->
                    <div class="my-2 border-t border-slate-200/80 dark:border-slate-800/80"></div>

                    <!-- Mobile Theme Appearance Switch -->
                    <div class="flex items-center justify-between px-3.5 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-800/50 border border-slate-200/80 dark:border-slate-700/80">
                        <div class="flex items-center gap-2.5">
                            <span class="w-7 h-7 rounded-lg bg-amber-100 dark:bg-slate-700 text-amber-600 dark:text-amber-400 flex items-center justify-center shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                            </span>
                            <div>
                                <span class="block text-xs font-bold text-slate-800 dark:text-white">Appearance</span>
                                <span class="block text-[10px] text-slate-500 dark:text-slate-400 font-medium">Switch Light / Dark Theme</span>
                            </div>
                        </div>
                        @include('partials.theme-toggle', ['id' => 'mobile-theme-toggle'])
                    </div>

                    <!-- Mobile Book Appointment Button -->
                    <div class="pt-1.5">
                        <a href="{{ route('appointment.create') }}"
                            class="w-full flex items-center justify-center gap-2 px-4 py-3 rounded-xl font-bold text-white bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 active:scale-[0.98] shadow-lg shadow-emerald-700/25 transition-all text-sm">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
                            </svg>
                            <span>Book Appointment</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div id="mega-menu-full-dropdown"
            class="hidden bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-700 shadow-xl border-y absolute w-full z-50 left-0 max-h-[60vh] overflow-y-auto custom-scrollbar">
            <div class="max-w-7xl px-4 py-6 mx-auto lg:px-8"
                aria-labelledby="mega-menu-full-dropdown-button">

                {{-- Header row --}}
                <div class="flex items-center justify-between mb-4 px-1">
                    <h3 class="font-display text-lg font-bold text-gray-900 dark:text-white">Our Facilities</h3>
                    <a href="{{ route('units.index') }}" class="inline-flex items-center gap-1.5 text-sm font-semibold text-green-600 dark:text-green-400 hover:text-green-700 transition">
                        View all
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                    </a>
                </div>

                <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-3">
                    @forelse($globalFacilityUnits as $fUnit)
                        <a href="{{ route('units.show', $fUnit->slug) }}"
                            class="flex items-start gap-4 p-4 rounded-xl hover:bg-emerald-50 dark:hover:bg-emerald-950/20 transition border border-transparent hover:border-emerald-100 dark:hover:border-emerald-800/30 group">
                            <span class="shrink-0 w-11 h-11 rounded-xl bg-emerald-100 dark:bg-emerald-900/40 flex items-center justify-center text-emerald-700 dark:text-emerald-400 group-hover:bg-emerald-700 group-hover:text-white transition-colors overflow-hidden border border-emerald-200/50 dark:border-emerald-800/50">
                                @if($fUnit->image_url)
                                    <img src="{{ $fUnit->image_url }}" alt="" class="w-full h-full object-cover">
                                @else
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                @endif
                            </span>
                            <span class="min-w-0 flex-1">
                                <span class="block font-semibold text-slate-900 dark:text-white text-sm group-hover:text-emerald-700 dark:group-hover:text-emerald-400 transition-colors truncate">{{ $fUnit->name }}</span>
                                <span class="block text-xs text-slate-500 dark:text-slate-400 mt-0.5 line-clamp-2">
                                    {{ $fUnit->description ?: ($fUnit->operating_hours ?: 'Municipal health facility & clinic') }}
                                </span>
                                <span class="inline-block mt-1 text-[10px] font-bold text-emerald-700 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-950/60 px-2 py-0.5 rounded border border-emerald-200/60 dark:border-emerald-800/50">
                                    {{ $fUnit->category ?: 'Clinical Unit' }}
                                </span>
                            </span>
                        </a>
                    @empty
                        <div class="col-span-full py-6 text-center text-slate-400 text-sm">No active facilities listed.</div>
                    @endforelse
                </div>

            </div>
        </div>

        <div id="mega-menu-appointment-dropdown"
            class="hidden bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-700 shadow-xl border-y absolute w-full z-50 left-0 max-h-[60vh] overflow-y-auto custom-scrollbar">
            <div class="max-w-7xl px-4 py-6 mx-auto lg:px-8"
                aria-labelledby="mega-menu-appointment-dropdown-button">

                {{-- Header row --}}
                <div class="flex items-center justify-between mb-4 px-1">
                    <h3 class="font-display text-lg font-bold text-gray-900 dark:text-white">Appointments</h3>
                    <a href="{{ route('appointment.create') }}" class="inline-flex items-center gap-1.5 text-sm font-semibold text-green-600 dark:text-green-400 hover:text-green-700 transition">
                        Book now
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                    </a>
                </div>

                <div class="grid sm:grid-cols-2 gap-3">
                    <a href="{{ route('appointment.create') }}"
                        class="flex items-start gap-4 p-4 rounded-xl hover:bg-green-50 dark:hover:bg-green-900/20 transition border border-transparent hover:border-green-100 dark:hover:border-green-800/30 group">
                        <span class="shrink-0 w-11 h-11 rounded-xl bg-green-100 dark:bg-green-900/40 flex items-center justify-center text-green-600 dark:text-green-400 group-hover:bg-green-600 group-hover:text-white transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                        </span>
                        <span>
                            <span class="block font-semibold text-gray-900 dark:text-white text-sm group-hover:text-green-700 dark:group-hover:text-green-400 transition-colors">Get Started</span>
                            <span class="block text-xs text-gray-500 dark:text-gray-400 mt-0.5 line-clamp-2">New patient or booking? Schedule your visit online in a few quick steps</span>
                        </span>
                    </a>

                    <a href="{{ route('appointment.manage') }}"
                        class="flex items-start gap-4 p-4 rounded-xl hover:bg-teal-50 dark:hover:bg-teal-900/20 transition border border-transparent hover:border-teal-100 dark:hover:border-teal-800/30 group">
                        <span class="shrink-0 w-11 h-11 rounded-xl bg-teal-100 dark:bg-teal-900/40 flex items-center justify-center text-teal-600 dark:text-teal-400 group-hover:bg-teal-600 group-hover:text-white transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z"/></svg>
                        </span>
                        <span>
                            <span class="block font-semibold text-gray-900 dark:text-white text-sm group-hover:text-teal-700 dark:group-hover:text-teal-400 transition-colors">Manage Appointment</span>
                            <span class="block text-xs text-gray-500 dark:text-gray-400 mt-0.5 line-clamp-2">Already booked? Check status, reschedule, or cancel your booking</span>
                        </span>
                    </a>
                </div>

            </div>
        </div>

        <div id="mega-menu-updates-dropdown"
            class="hidden bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-700 shadow-xl border-y absolute w-full z-50 left-0 max-h-[60vh] overflow-y-auto custom-scrollbar">
            <div class="max-w-7xl px-4 py-6 mx-auto lg:px-8"
                aria-labelledby="mega-menu-updates-dropdown-button">

                {{-- Header row --}}
                <div class="flex items-center justify-between mb-4 px-1">
                    <h3 class="font-display text-lg font-bold text-gray-900 dark:text-white">About & Updates</h3>
                    <a href="{{ route('announcements.index') }}" class="inline-flex items-center gap-1.5 text-sm font-semibold text-green-600 dark:text-green-400 hover:text-green-700 transition">
                        View all announcements
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                    </a>
                </div>

                <div class="grid sm:grid-cols-2 gap-3">
                    <a href="{{ route('announcements.index') }}"
                        class="flex items-start gap-4 p-4 rounded-xl hover:bg-green-50 dark:hover:bg-green-900/20 transition border border-transparent hover:border-green-100 dark:hover:border-green-800/30 group">
                        <span class="shrink-0 w-11 h-11 rounded-xl bg-green-100 dark:bg-green-900/40 flex items-center justify-center text-green-600 dark:text-green-400 group-hover:bg-green-600 group-hover:text-white transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10.34 15.84c-.063.046-.129.088-.198.127a3.75 3.75 0 01-4.088-.288L3.25 13.5A2.25 2.25 0 012.25 11.75v-1.5a2.25 2.25 0 011-1.75l2.804-2.179a3.75 3.75 0 014.088-.288c.069.04.135.081.198.127m0 9.68l4.41 4.41a1.5 1.5 0 002.122 0l1.414-1.414a1.5 1.5 0 000-2.122L12 14.004m-1.66 1.836V7.996m0 0a3.75 3.75 0 013.75-3.75h1.5a2.25 2.25 0 012.25 2.25v6a2.25 2.25 0 01-2.25 2.25h-1.5a3.75 3.75 0 01-3.75-3.75z"/>
                            </svg>
                        </span>
                        <span>
                            <span class="block font-semibold text-gray-900 dark:text-white text-sm group-hover:text-green-700 dark:group-hover:text-green-400 transition-colors">Announcements</span>
                            <span class="block text-xs text-gray-500 dark:text-gray-400 mt-0.5 line-clamp-2">Health advisories, public advisories, medical mission notices & community alerts</span>
                        </span>
                    </a>

                    <a href="{{ route('about') }}"
                        class="flex items-start gap-4 p-4 rounded-xl hover:bg-emerald-50 dark:hover:bg-emerald-900/20 transition border border-transparent hover:border-emerald-100 dark:hover:border-emerald-800/30 group">
                        <span class="shrink-0 w-11 h-11 rounded-xl bg-emerald-100 dark:bg-emerald-900/40 flex items-center justify-center text-emerald-600 dark:text-emerald-400 group-hover:bg-emerald-600 group-hover:text-white transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 21a9.004 9.004 0 008.716-6.747M12 21a9.004 9.004 0 01-8.716-6.747M12 21c2.485 0 4.5-4.03 4.5-9S14.485 3 12 3m0 18c-2.485 0-4.5-4.03-4.5-9S9.515 3 12 3m0 0a8.997 8.997 0 017.843 4.582M12 3a8.997 8.997 0 00-7.843 4.582m15.686 0A11.953 11.953 0 0112 10.5c-2.998 0-5.74-1.1-7.843-2.918m15.686 0A8.959 8.959 0 0121 12c0 .778-.099 1.533-.284 2.253m0 0A17.919 17.919 0 0112 16.5c-3.162 0-6.133-.815-8.716-2.247m0 0A9.015 9.015 0 013 12c0-.778.099-1.533.284-2.253"/>
                            </svg>
                        </span>
                        <span>
                            <span class="block font-semibold text-gray-900 dark:text-white text-sm group-hover:text-emerald-700 dark:group-hover:text-emerald-400 transition-colors">About Us</span>
                            <span class="block text-xs text-gray-500 dark:text-gray-400 mt-0.5 line-clamp-2">Learn about RHU Silang's mission, healthcare leadership, facilities & history</span>
                        </span>
                    </a>
                </div>

            </div>
        </div>
    </nav>

    <main class="min-h-screen">
        @include('partials.toast')

        @yield('content')
    </main>

    <footer class="bg-gradient-to-b from-[#143818] via-[#0f2d13] to-[#0a1e0d] dark:from-[#061814] dark:via-[#04120f] dark:to-[#020a08] border-t border-emerald-800/40 dark:border-emerald-950/80 text-emerald-100 text-sm w-full relative overflow-hidden" role="contentinfo" aria-label="Site Footer">
        <!-- Ambient Top Accent Glow -->
        <div class="absolute top-0 inset-x-0 h-px bg-gradient-to-r from-transparent via-emerald-400/40 to-transparent" aria-hidden="true"></div>
        <div class="absolute -top-32 left-1/2 -translate-x-1/2 w-96 h-96 bg-emerald-500/5 rounded-full blur-3xl pointer-events-none" aria-hidden="true"></div>

        <!-- Main Navigation & Content Container -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-12 pb-10 relative z-10" x-data="{ mobileCol: null }">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-12 gap-8 lg:gap-10">
                
                <!-- COLUMN 1: Brand, Identity & Direct Contact (lg:col-span-4) -->
                <div class="lg:col-span-4 md:col-span-2 space-y-4">
                    <div class="flex items-center gap-4">
                        <a href="{{ route('welcome') }}"
                        class="shrink-0 rounded-full focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-400 focus-visible:ring-offset-2 focus-visible:ring-offset-[#051512]"
                        aria-label="Rural Health Unit Silang - Back to Homepage">
                            <img src="{{ asset('assets/images/logo.png') }}"
                                class="h-16 w-16 object-contain sm:h-[58px] sm:w-[58px]"
                                alt="Rural Health Unit Silang Official Seal">
                        </a>

                        <div class="min-w-0">
                            <span class="block text-lg font-bold uppercase leading-tight tracking-tight text-white sm:text-xl">
                                Rural Health Unit
                            </span>
                            <span class="block text-xs font-semibold uppercase leading-snug tracking-wider text-emerald-300 sm:text-[13px]">
                                Municipality of Silang, Cavite
                            </span>
                        </div>
                    </div>

                    <p class="text-xs text-emerald-100/85 leading-relaxed max-w-sm">
                        Dedicated to providing accessible, comprehensive, and compassionate public healthcare services to every citizen of Silang. Free medical consultations and community wellness.
                    </p>

                    <!-- Contact & Physical Address -->
                    <div class="space-y-2 pt-1 text-xs">
                        <div class="flex items-start gap-2.5 text-emerald-200/90">
                            <svg class="w-4 h-4 text-emerald-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <a href="https://maps.google.com/?q={{ urlencode(\App\Models\SiteSetting::get('footer_address_line1', 'M.H del Pilar St.').', '.\App\Models\SiteSetting::get('footer_address_line2', 'Silang, Cavite')) }}"
                               target="_blank" rel="noopener noreferrer"
                               class="hover:text-white underline underline-offset-2 decoration-emerald-500/40 hover:decoration-white transition focus-visible:ring-2 focus-visible:ring-emerald-400 focus-visible:outline-none rounded">
                                {{ \App\Models\SiteSetting::get('footer_address_line1', 'M.H del Pilar St.') }}, {{ \App\Models\SiteSetting::get('footer_address_line2', 'Silang, Cavite') }}
                            </a>
                        </div>

                        <div class="flex items-center gap-2.5 text-emerald-200/90">
                            <svg class="w-4 h-4 text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                            </svg>
                            <a href="tel:{{ preg_replace('/[^0-9+]/', '', \App\Models\SiteSetting::get('footer_phone', '(046) 414-0209')) }}"
                               class="hover:text-white underline underline-offset-2 decoration-emerald-500/40 hover:decoration-white transition font-medium focus-visible:ring-2 focus-visible:ring-emerald-400 focus-visible:outline-none rounded">
                                {{ \App\Models\SiteSetting::get('footer_phone', '(046) 414-0209') }}
                            </a>
                        </div>

                        <div class="flex items-center gap-2.5 text-emerald-200/90">
                            <svg class="w-4 h-4 text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                            <a href="mailto:{{ \App\Models\SiteSetting::get('footer_email', 'contact@silang.gov.ph') }}"
                               class="hover:text-white underline underline-offset-2 decoration-emerald-500/40 hover:decoration-white transition font-medium focus-visible:ring-2 focus-visible:ring-emerald-400 focus-visible:outline-none rounded">
                                {{ \App\Models\SiteSetting::get('footer_email', 'contact@silang.gov.ph') }}
                            </a>
                        </div>

                        <!-- Emergency Hotlines -->
                        <div class="flex items-center gap-2.5 text-rose-300 font-semibold pt-0.5">
                            <svg class="w-4 h-4 text-rose-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                            <span class="text-xs">
                                <span class="text-rose-300 font-bold">Emergency:</span>
                                <span class="text-white ml-1">{{ \App\Models\SiteSetting::get('emergency_hotlines', '911 | (046) 432-1234') }}</span>
                            </span>
                        </div>
                    </div>

                    <!-- Social Media Links (Inline SVGs with aria-labels) -->
                    <div class="pt-2">
                        <span class="sr-only">Official RHU and Municipal Links</span>
                        <div class="flex items-center gap-2">
                            <a href="https://www.facebook.com/silang.rhu" target="_blank" rel="noopener noreferrer"
                               class="w-9 h-9 rounded-xl bg-emerald-950/70 border border-emerald-700/60 hover:border-emerald-400 hover:bg-emerald-800 text-emerald-200 hover:text-white flex items-center justify-center transition-all duration-200 focus-visible:ring-2 focus-visible:ring-emerald-400 focus-visible:outline-none cursor-pointer"
                               aria-label="Visit Rural Health Unit Silang on Facebook (opens in new tab)"
                               title="Official Facebook Page (TODO: update URL)">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path fill-rule="evenodd" d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z" clip-rule="evenodd"/>
                                </svg>
                            </a>
                            <!-- Department of Health (DOH) Portal -->
                            <a href="https://doh.gov.ph" target="_blank" rel="noopener noreferrer"
                               class="w-9 h-9 rounded-xl bg-emerald-950/70 border border-emerald-700/60 hover:border-emerald-400 hover:bg-emerald-800 text-emerald-200 hover:text-white flex items-center justify-center transition-all duration-200 focus-visible:ring-2 focus-visible:ring-emerald-400 focus-visible:outline-none cursor-pointer"
                               aria-label="Department of Health Portal (opens in new tab)"
                               title="Department of Health (DOH)">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9" />
                                </svg>
                            </a>
                            <!-- Telephone Direct Call -->
                            <a href="tel:{{ preg_replace('/[^0-9+]/', '', \App\Models\SiteSetting::get('footer_phone', '(046) 414-0209')) }}"
                               class="w-9 h-9 rounded-xl bg-emerald-950/70 border border-emerald-700/60 hover:border-emerald-400 hover:bg-emerald-800 text-emerald-200 hover:text-white flex items-center justify-center transition-all duration-200 focus-visible:ring-2 focus-visible:ring-emerald-400 focus-visible:outline-none cursor-pointer"
                               aria-label="Call Rural Health Unit Telephone Line"
                               title="Call Telephone Desk ({{ \App\Models\SiteSetting::get('footer_phone', '(046) 414-0209') }})">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                </svg>
                            </a>
                            <!-- Email Direct Inquiry -->
                            <a href="mailto:{{ \App\Models\SiteSetting::get('footer_email', 'contact@silang.gov.ph') }}"
                               class="w-9 h-9 rounded-xl bg-emerald-950/70 border border-emerald-700/60 hover:border-emerald-400 hover:bg-emerald-800 text-emerald-200 hover:text-white flex items-center justify-center transition-all duration-200 focus-visible:ring-2 focus-visible:ring-emerald-400 focus-visible:outline-none cursor-pointer"
                               aria-label="Send Email Inquiry to RHU Silang"
                               title="Email Helpdesk">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- COLUMN 2: Public Information & Resources (lg:col-span-4 md:col-span-1) -->
                <nav aria-label="Public Information and Community" class="lg:col-span-4 md:col-span-1">
                    <div class="border-b border-emerald-800/40 md:border-b-0 pb-3 md:pb-0">
                        <button type="button" @click="mobileCol = (mobileCol === 'info' ? null : 'info')"
                                class="w-full flex items-center justify-between text-left font-bold text-white uppercase tracking-wider text-xs sm:text-[13px] md:cursor-default"
                                :aria-expanded="mobileCol === 'info'">
                            <span class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <span>Public Information</span>
                            </span>
                            <svg class="w-4 h-4 text-emerald-400 transition-transform duration-200 md:hidden"
                                 :class="mobileCol === 'info' ? 'rotate-180' : ''"
                                 fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <div :class="mobileCol === 'info' ? 'block' : 'hidden md:block'" class="pt-2 md:pt-2.5 pl-4 sm:pl-5">
                            <ul class="space-y-1 sm:space-y-1.5" role="list">
                                <li>
                                    <a href="{{ route('about') }}"
                                       class="py-1 flex items-center gap-2 text-emerald-100/85 hover:text-white hover:translate-x-1 transition-all duration-150 text-xs sm:text-[13px] font-medium focus-visible:ring-2 focus-visible:ring-emerald-400 focus-visible:outline-none rounded">
                                        <svg class="w-3.5 h-3.5 text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        <span>About RHU Silang</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('announcements.index') }}"
                                       class="py-1 flex items-center gap-2 text-emerald-100/85 hover:text-white hover:translate-x-1 transition-all duration-150 text-xs sm:text-[13px] font-medium focus-visible:ring-2 focus-visible:ring-emerald-400 focus-visible:outline-none rounded">
                                        <svg class="w-3.5 h-3.5 text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/></svg>
                                        <span>Health Bulletins &amp; News</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ url('/#announcements') }}"
                                       class="py-1 flex items-center gap-2 text-emerald-100/85 hover:text-white hover:translate-x-1 transition-all duration-150 text-xs sm:text-[13px] font-medium focus-visible:ring-2 focus-visible:ring-emerald-400 focus-visible:outline-none rounded">
                                        <svg class="w-3.5 h-3.5 text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                                        <span>Community Advisories &amp; Alerts</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('units.index') }}"
                                       class="py-1 flex items-center gap-2 text-emerald-100/85 hover:text-white hover:translate-x-1 transition-all duration-150 text-xs sm:text-[13px] font-medium focus-visible:ring-2 focus-visible:ring-emerald-400 focus-visible:outline-none rounded">
                                        <svg class="w-3.5 h-3.5 text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                        <span>RHU Facilities &amp; Clinics Directory</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ url('/#schedule') }}"
                                       class="py-1 flex items-center gap-2 text-emerald-100/85 hover:text-white hover:translate-x-1 transition-all duration-150 text-xs sm:text-[13px] font-medium focus-visible:ring-2 focus-visible:ring-emerald-400 focus-visible:outline-none rounded">
                                        <svg class="w-3.5 h-3.5 text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                        <span>Doctors' Consultation Schedule</span>
                                    </a>
                                </li>
                                <li>
                                    <button type="button" @click="showFaq = true"
                                            class="w-full py-1 flex items-center gap-2 text-emerald-100/85 hover:text-white hover:translate-x-1 transition-all duration-150 text-xs sm:text-[13px] font-medium focus-visible:ring-2 focus-visible:ring-emerald-400 focus-visible:outline-none rounded cursor-pointer text-left">
                                        <svg class="w-3.5 h-3.5 text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        <span>Frequently Asked Questions</span>
                                    </button>
                                </li>
                                <li>
                                    <a href="{{ route('about') }}#charter"
                                       class="py-1 flex items-center gap-2 text-emerald-100/85 hover:text-white hover:translate-x-1 transition-all duration-150 text-xs sm:text-[13px] font-medium focus-visible:ring-2 focus-visible:ring-emerald-400 focus-visible:outline-none rounded">
                                        <svg class="w-3.5 h-3.5 text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                        <span>Citizen's Charter &amp; Standards</span>
                                    </a>
                                </li>
                                <li>
                                    <!-- Direct Helpdesk Inquiry (TODO: Dedicated Contact Us form page) -->
                                    <a href="mailto:{{ \App\Models\SiteSetting::get('footer_email', 'contact@silang.gov.ph') }}?subject=RHU%20Public%20Inquiry"
                                       class="py-1 flex items-center gap-2 text-emerald-100/85 hover:text-white hover:translate-x-1 transition-all duration-150 text-xs sm:text-[13px] font-medium focus-visible:ring-2 focus-visible:ring-emerald-400 focus-visible:outline-none rounded"
                                       title="Email Helpdesk (TODO: Create dedicated /contact route)">
                                        <svg class="w-3.5 h-3.5 text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/></svg>
                                        <span>Contact &amp; Citizen Inquiries</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </nav>

                <!-- COLUMN 3: Book Your Appointment CTA (lg:col-span-4 md:col-span-1) -->
                <div class="lg:col-span-4 md:col-span-1 space-y-3.5">
                    <h3 class="font-bold text-white text-xs sm:text-[13px] uppercase tracking-wider flex items-center gap-2">
                        <svg class="w-4 h-4 text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <span>Book an Appointment</span>
                    </h3>
                    <p class="text-xs text-emerald-200/80 leading-relaxed">
                        Skip the long lines. Secure your consultation slot with an RHU Silang healthcare provider online.
                    </p>

                    <div>

                        <a href="{{ route('appointment.create') }}"
                           class="w-full min-h-[44px] px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 active:bg-emerald-700 text-white font-semibold text-xs transition-all duration-200 flex items-center justify-center gap-2 shadow-sm shadow-emerald-900/40 cursor-pointer focus-visible:ring-2 focus-visible:ring-emerald-400 focus-visible:outline-none">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
                            </svg>
                            <span>Book Appointment Now</span>
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                            </svg>
                        </a>

                        <div class="pt-2 border-t border-emerald-800/50 flex items-center justify-between text-[11px] text-emerald-200/80">
                            <span>Already have a booking?</span>
                            <a href="{{ route('appointment.manage') }}"
                               class="text-emerald-300 hover:text-white font-medium underline underline-offset-2 decoration-emerald-500/50 hover:decoration-white transition">
                                Manage / Reschedule
                            </a>
                        </div>
                    </div>
                </div>

            </div>

            <!-- SUB-FOOTER BOTTOM BAR -->
            <div class="border-t border-emerald-800/40 dark:border-emerald-950/80 pt-6 mt-10">
                <div class="flex flex-col md:flex-row items-center justify-between gap-4 text-xs">
                    
                    <!-- Left: Disguised Staff Access Portal Link & Copyright (Must be preserved for staff login access) -->
                    <div class="flex flex-wrap items-center justify-center md:justify-start gap-2">
                        <a href="{{ route('staff.access') }}"
                           class="text-emerald-200/75 transition-colors duration-200 focus-visible:ring-2 focus-visible:ring-emerald-400 focus-visible:outline-none rounded cursor-default no-underline"
                           style="text-decoration: none;">
                            &copy; <span x-text="new Date().getFullYear()">{{ date('Y') }}</span> Rural Health Unit &bull; Municipality of Silang, Cavite.
                        </a>
                        <span class="hidden sm:inline text-emerald-700 dark:text-emerald-800" aria-hidden="true">|</span>
                        <span class="text-emerald-300/60 font-normal">All rights reserved.</span>
                    </div>

                    <!-- Center: Legal Links Nav -->
                    <nav aria-label="Legal and Compliance Links" class="flex flex-wrap items-center justify-center gap-x-4 gap-y-2 text-[11px] text-emerald-200/80">
                        <button type="button" @click="showPrivacy = true"
                                class="hover:text-white hover:underline transition-colors focus-visible:ring-2 focus-visible:ring-emerald-400 focus-visible:outline-none rounded cursor-pointer min-h-[44px] sm:min-h-0 flex items-center">
                            Data Privacy Policy
                        </button>
                        <span class="text-emerald-700/60" aria-hidden="true">&bull;</span>
                        <button type="button" @click="showTerms = true"
                                class="hover:text-white hover:underline transition-colors focus-visible:ring-2 focus-visible:ring-emerald-400 focus-visible:outline-none rounded cursor-pointer min-h-[44px] sm:min-h-0 flex items-center">
                            Citizen's Terms of Service
                        </button>
                        <span class="text-emerald-700/60" aria-hidden="true">&bull;</span>
                        <!-- TODO: Add dedicated comprehensive sitemap page (/sitemap) -->
                        <a href="{{ route('units.index') }}"
                           class="hover:text-white hover:underline transition-colors focus-visible:ring-2 focus-visible:ring-emerald-400 focus-visible:outline-none rounded min-h-[44px] sm:min-h-0 flex items-center"
                           title="Directory of Facilities and Units (TODO: dedicated sitemap page)">
                            Sitemap Directory
                        </a>
                        @auth
                            <span class="text-emerald-700/60" aria-hidden="true">&bull;</span>
                            <a href="{{ \App\Http\Controllers\AuthController::homeRouteForRole(auth()->user()->role ?? 'guest') }}"
                               class="font-bold text-teal-300 hover:text-white hover:underline transition-colors focus-visible:ring-2 focus-visible:ring-emerald-400 focus-visible:outline-none rounded min-h-[44px] sm:min-h-0 flex items-center">
                                Staff Dashboard
                            </a>
                        @endauth
                    </nav>

                    <!-- Right: Back to Top & MIS Attribution -->
                    <div class="flex items-center gap-3">
                        <button type="button"
                                @click="window.scrollTo({top: 0, behavior: 'smooth'})"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-emerald-950/60 hover:bg-emerald-800/80 active:scale-95 border border-emerald-700/40 text-[11px] font-semibold text-emerald-200 hover:text-white transition-all duration-200 focus-visible:ring-2 focus-visible:ring-emerald-400 focus-visible:outline-none cursor-pointer min-h-[44px] sm:min-h-0"
                                aria-label="Scroll back to top of page">
                            <span>Back to top</span>
                            <svg class="w-3.5 h-3.5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"/>
                            </svg>
                        </button>
                    </div>

                </div>
            </div>

        </div>
    </footer>


    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
        // Flash Message Auto-dismiss
        const flash = document.getElementById('flash-message');
        if (flash) {
            setTimeout(() => {
                flash.style.transform = 'translateX(150%)';
                setTimeout(() => flash.remove(), 500);
            }, 4000);
        }

        // Global Auto-Capitalization (Title Case)
        @auth
            @unless(auth()->user()->hasRole('admin', 'super_admin'))
                document.addEventListener('input', function (e) {
                    const target = e.target;
                    if ((target.tagName === 'INPUT' && target.type === 'text') || target.tagName === 'TEXTAREA') {
                        if (!target.classList.contains('no-uppercase') && !target.classList.contains('raw-text') && !target.classList.contains('no-capitalize')) {
                            let start = target.selectionStart;
                            let end = target.selectionEnd;

                            // Basic Title Case: capitalize first letter of each word
                            let words = target.value.split(' ');
                            for (let i = 0; i < words.length; i++) {
                                if (words[i].length > 0) {
                                    words[i] = words[i].charAt(0).toUpperCase() + words[i].slice(1).toLowerCase();
                                }
                            }
                            target.value = words.join(' ');

                            target.setSelectionRange(start, end);
                        }
                    }
                });
            @endunless
        @endauth
    </script>
    @stack('scripts')
    <!-- Global Confirmation Modal (Public) -->
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
                        <template x-if="action">
                            <form :action="action" method="POST" class="inline-flex w-full sm:w-auto">
                                @csrf
                                <input type="hidden" name="_method" :value="method">
                                <button type="submit"
                                    class="w-full inline-flex justify-center rounded-xl border border-transparent shadow-lg px-6 py-2 text-sm font-bold text-white transition-all cursor-pointer"
                                    :class="confirmClass" x-text="confirmText"></button>
                            </form>
                        </template>
                        <template x-if="!action">
                            <button type="button" @click="confirm()"
                                class="w-full inline-flex justify-center rounded-xl border border-transparent shadow-lg px-6 py-2 text-sm font-bold text-white transition-all cursor-pointer"
                                :class="confirmClass" x-text="confirmText"></button>
                        </template>
                        <button type="button" @click="open = false"
                            class="inline-flex justify-center rounded-xl border border-slate-300 dark:border-slate-700 shadow-sm px-6 py-2 bg-white dark:bg-slate-800 text-sm font-bold text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 transition-all cursor-pointer">
                            Cancel
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </template>

    <!-- Go to Top Button -->
    <button x-data="{ show: false }" @scroll.window="show = (window.pageYOffset > 300)"
        @click="window.scrollTo({top: 0, behavior: 'smooth'})" x-show="show"
        x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-10"
        x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-300"
        x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 translate-y-10"
        class="fixed bottom-8 right-8 bg-teal-600 hover:bg-teal-700 text-white p-3 rounded-full shadow-lg z-40 focus:outline-none"
        style="display: none;">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"></path>
        </svg>
    </button>

    <!-- Global Privacy Modal -->
    <div x-show="showPrivacy" style="display: none;" class="fixed inset-0 z-100 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-gray-900/75 transition-opacity" @click="showPrivacy = false"></div>
        <div
            class="bg-white dark:bg-gray-800 rounded-xl overflow-hidden shadow-2xl transform transition-all max-w-lg w-full max-h-[80vh] flex flex-col z-110">
            <div
                class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center bg-teal-50 dark:bg-teal-900/30">
                <h3 class="text-lg font-bold text-teal-900 dark:text-teal-400">Data Privacy Policy</h3>
                <button @click="showPrivacy = false"
                    class="text-gray-400 hover:text-gray-600 dark:text-gray-400 focus:outline-none">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                        </path>
                    </svg>
                </button>
            </div>
            <div class="p-6 overflow-y-auto">
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-4 font-semibold italic">
                    {{ \App\Models\SiteSetting::get('privacy_intro', 'Compliance with Republic Act No. 10173 (Data Privacy Act of 2012)') }}
                </p>
                <div class="prose prose-sm text-gray-600 dark:text-gray-400">
                    <p class="mb-3">The Rural Health Unit (RHU) is committed to protecting your personal information. By
                        using our services, you understand and agree to the following:</p>
                    <ul class="list-disc pl-5 mb-4 space-y-1">
                        @php
                            $privacyItems = \App\Models\SiteSetting::getJson('privacy_items', [
                                'We collect personal data for medical records, appointment scheduling, and public health tracking.',
                                'Your information is treated with strict confidentiality and is only accessible by authorized health personnel.',
                                'We do not share your data with third parties unless required by law or for referral purposes with your consent.',
                                'You have the right to access, correct, or request deletion of your data (subject to retention laws).'
                            ]);
                        @endphp
                        @foreach($privacyItems as $item)
                            <li>{{ $item }}</li>
                        @endforeach
                    </ul>
                    <p>{{ \App\Models\SiteSetting::get('privacy_footer', 'For any privacy concerns, please contact our Data Protection Officer at the Municipal Hall.') }}</p>
                </div>
            </div>
            <div class="px-6 py-4 bg-gray-50 dark:bg-gray-900 flex justify-end">
                <button type="button" @click="showPrivacy = false"
                    class="bg-teal-600 text-white px-4 py-2 rounded-lg hover:bg-teal-700 shadow-md transition font-medium">Close</button>
            </div>
        </div>
    </div>

    <!-- Global FAQ Modal -->
    <div x-show="showFaq" style="display: none;" class="fixed inset-0 z-100 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-gray-900/75 transition-opacity" @click="showFaq = false"></div>
        <div
            class="bg-white dark:bg-gray-800 rounded-xl overflow-hidden shadow-2xl transform transition-all max-w-2xl w-full max-h-[80vh] flex flex-col z-110">
            <div
                class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center bg-teal-50 dark:bg-teal-900/30">
                <h3 class="text-lg font-bold text-teal-900">Frequently Asked Questions</h3>
                <button @click="showFaq = false"
                    class="text-gray-400 hover:text-gray-600 dark:text-gray-400 focus:outline-none">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                        </path>
                    </svg>
                </button>
            </div>
            <div class="p-6 overflow-y-auto space-y-6">

                @php
                    $faqs = \App\Models\SiteSetting::getJson('faq_items', [
                        ['question' => 'What are your operating hours?', 'answer' => 'We are open Monday to Friday, from 8:00 AM to 5:00 PM. Emergency services are available 24/7 at the main facility.'],
                        ['question' => 'Do I need an appointment for a check-up?', 'answer' => 'Appointments are highly recommended for specialized clinics (like Dental, Prenatal, and Pediatrics) to ensure you are served promptly. However, we accept walk-ins for general consultations, subject to doctor availability.'],
                        ['question' => 'Is the anti-rabies vaccination free?', 'answer' => 'Yes, anti-rabies vaccines are generally free for the first few doses, subject to stock availability. Please check our Announcements page for stock updates.'],
                        ['question' => 'What should I bring during my visit?', 'answer' => 'Please bring a valid ID. If you have a PhilHealth ID/MDR, Senior Citizen ID, or PWD ID, please present it at the admitting section.']
                    ]);
                @endphp

                @foreach($faqs as $faq)
                <div>
                    <h4 class="font-bold text-gray-800 dark:text-white text-base mb-2">{{ $faq['question'] ?? '' }}</h4>
                    <p class="text-gray-600 dark:text-gray-400 text-sm">{{ $faq['answer'] ?? '' }}</p>
                </div>
                @endforeach

            </div>
            <div class="px-6 py-4 bg-gray-50 dark:bg-gray-900 flex justify-end">
                <button type="button" @click="showFaq = false"
                    class="bg-teal-600 text-white px-4 py-2 rounded-lg hover:bg-teal-700 shadow-md transition font-medium">Got
                    it</button>
            </div>
        </div>
    </div>

    <!-- Global Terms & Conditions / Citizen's Charter Modal -->
    <div x-show="showTerms" style="display: none;" class="fixed inset-0 z-100 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-gray-900/75 transition-opacity" @click="showTerms = false"></div>
        <div class="bg-white dark:bg-gray-800 rounded-2xl overflow-hidden shadow-2xl transform transition-all max-w-2xl w-full max-h-[80vh] flex flex-col z-110 border border-slate-200 dark:border-slate-700">
            <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center bg-teal-50 dark:bg-teal-900/30">
                <div class="flex items-center gap-2.5">
                    <span class="w-8 h-8 rounded-lg bg-teal-100 dark:bg-teal-900/50 text-teal-700 dark:text-teal-400 flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </span>
                    <h3 class="text-base sm:text-lg font-bold text-teal-950 dark:text-white">Citizen's Charter &amp; Service Terms</h3>
                </div>
                <button type="button" @click="showTerms = false"
                        class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 p-1 rounded-lg focus-visible:ring-2 focus-visible:ring-teal-500 focus:outline-none"
                        aria-label="Close Terms modal">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="p-6 overflow-y-auto space-y-4 text-sm text-gray-600 dark:text-gray-300 leading-relaxed custom-scrollbar">
                <p class="font-semibold text-gray-800 dark:text-gray-200">
                    Terms of Public Health Service &amp; Community Engagement &mdash; RHU Silang
                </p>
                <div class="space-y-3">
                    <div class="p-3.5 rounded-xl bg-slate-50 dark:bg-slate-900/50 border border-slate-200/60 dark:border-slate-700/60">
                        <h4 class="font-bold text-gray-900 dark:text-white text-xs uppercase tracking-wide mb-1">1. Non-Emergency Consultations</h4>
                        <p class="text-xs">The online appointment booking system is intended for non-emergency healthcare consultations, routine medical checkups, and specialized clinical visits. In case of life-threatening medical emergencies, please dial 911 or proceed immediately to the nearest hospital emergency room.</p>
                    </div>
                    <div class="p-3.5 rounded-xl bg-slate-50 dark:bg-slate-900/50 border border-slate-200/60 dark:border-slate-700/60">
                        <h4 class="font-bold text-gray-900 dark:text-white text-xs uppercase tracking-wide mb-1">2. Verification &amp; Punctuality</h4>
                        <p class="text-xs">Patients are advised to arrive 15 minutes prior to their scheduled appointment slot with a valid government ID or PhilHealth document. Walk-in queues are accommodated in accordance with triage urgency and clinic capacity.</p>
                    </div>
                    <div class="p-3.5 rounded-xl bg-slate-50 dark:bg-slate-900/50 border border-slate-200/60 dark:border-slate-700/60">
                        <h4 class="font-bold text-gray-900 dark:text-white text-xs uppercase tracking-wide mb-1">3. Accurate Medical Information</h4>
                        <p class="text-xs">Patients are responsible for providing truthful and accurate personal and clinical information during scheduling and triage admissions to ensure safety and quality healthcare guidance.</p>
                    </div>
                    <div class="p-3.5 rounded-xl bg-slate-50 dark:bg-slate-900/50 border border-slate-200/60 dark:border-slate-700/60">
                        <h4 class="font-bold text-gray-900 dark:text-white text-xs uppercase tracking-wide mb-1">4. Equal Access &amp; ARTA Compliance</h4>
                        <p class="text-xs">RHU Silang strictly adheres to the Anti-Red Tape Authority (ARTA) mandates and the Universal Health Care Act (RA 11223), ensuring non-discriminatory, prompt, and dignified health services for all citizens.</p>
                    </div>
                </div>
            </div>
            <div class="px-6 py-4 bg-gray-50 dark:bg-gray-900 border-t border-gray-100 dark:border-gray-700 flex justify-end">
                <button type="button" @click="showTerms = false"
                        class="bg-teal-600 text-white px-5 py-2 rounded-xl hover:bg-teal-700 shadow-md transition font-medium text-xs cursor-pointer">
                    Understood
                </button>
            </div>
        </div>
    </div>

    <script>
        if (!window.toggleTheme) {
            window.toggleTheme = function() {
                var isDark = document.documentElement.classList.toggle('dark');
                localStorage.setItem('color-theme', isDark ? 'dark' : 'light');
                window.dispatchEvent(new CustomEvent('theme-changed', { detail: { isDark: isDark } }));
            };
        }
    </script>

</html>