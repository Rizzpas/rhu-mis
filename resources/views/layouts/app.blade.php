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
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@700;800&family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    @stack('head')

    <!-- Flatpickr for better date pickers -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

    <!-- Styles & Scripts -->
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
                            sans: ['Outfit', 'sans-serif'],
                        }
                    }
                }
            }
        </script>
    @endif

    <style>
        body {
            font-family: 'Outfit', sans-serif;
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
    class="antialiased bg-[#faf8f2] dark:bg-[#081a1c] text-gray-800 dark:text-gray-100 font-sans relative"
    x-data="{ 
            open: false,
            title: '',
            message: '',
            callback: null, // For when we need to run JS instead of form submit
            action: '',
            method: 'POST',
            confirmText: 'Confirm',
            showPrivacy: false,
            showFaq: false,
            
            show(detail) {
                this.title = detail.title || 'Confirm Action';
                this.message = detail.message || 'Are you sure you want to proceed?';
                this.callback = detail.callback; // Function reference or event name
                this.action = detail.action || '';
                this.method = detail.method || 'POST';
                this.confirmText = detail.confirmText || 'Yes, Proceed';
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
        <div class="flex items-center gap-2 mx-auto md:mx-0">
            <span class="font-bold tracking-wide uppercase">Republic of the Philippines</span>
            <span class="text-emerald-400 opacity-60">•</span>
            <span class="text-emerald-200">Province of Cavite</span>
            <span class="text-emerald-400 opacity-60">•</span>
            <span class="text-emerald-200 font-semibold">Municipality of Silang</span>
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
                class="inline-flex items-center p-2 w-10 h-10 justify-center text-sm text-slate-500 dark:text-slate-400 rounded-lg md:hidden hover:bg-slate-100 dark:hover:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500"
                aria-controls="mega-menu-full" aria-expanded="false">
                <span class="sr-only">Open main menu</span>
                <svg class="w-6 h-6" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                    fill="none" viewBox="0 0 24 24">
                    <path stroke="currentColor" stroke-linecap="round" stroke-width="2" d="M5 7h14M5 12h14M5 17h14" />
                </svg>
            </button>
            <div id="mega-menu-full" class="items-center justify-between hidden w-full md:flex md:w-auto md:order-1">
                <ul
                    class="flex flex-col mt-4 font-medium md:flex-row md:mt-0 md:space-x-8 rtl:space-x-reverse items-center">
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

                    <!-- Theme Toggle -->
                    <li>
                        <button id="theme-toggle" type="button"
                            class="relative inline-flex h-6 w-[42px] shrink-0 cursor-pointer items-center justify-start rounded-full border-2 border-transparent bg-slate-200 dark:bg-slate-700 transition-colors duration-200 ease-in-out">
                            <span class="sr-only">Toggle theme</span>
                            <span
                                class="pointer-events-none relative inline-flex h-5 w-5 transform items-center justify-center rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out translate-x-0 dark:translate-x-[18px]">
                                <svg id="theme-toggle-dark-icon" class="hidden w-3 h-3 text-slate-700"
                                    fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"></path>
                                </svg>
                                <svg id="theme-toggle-light-icon" class="hidden w-3 h-3 text-slate-900"
                                    fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z"
                                        fill-rule="evenodd" clip-rule="evenodd"></path>
                                </svg>
                            </span>
                        </button>
                    </li>
                    <!-- Book an Appointment CTA -->
                    <li class="w-full md:w-auto">
                        <a href="{{ route('appointment.create') }}"
                            class="inline-flex items-center justify-center gap-2 px-4 py-2 text-xs sm:text-sm font-semibold text-white bg-emerald-700 hover:bg-emerald-800 active:bg-emerald-900 rounded-xl shadow-xs transition-all duration-200 w-full md:w-auto">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
                            </svg>
                            <span>Book Appointment</span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        <div id="mega-menu-full-dropdown"
            class="hidden bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-700 shadow-xl border-y absolute w-full z-50 left-0 max-h-[60vh] overflow-y-auto">
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
                    <a href="{{ route('units.show', 'main-health-center') }}"
                        class="flex items-start gap-4 p-4 rounded-xl hover:bg-emerald-50 dark:hover:bg-emerald-950/20 transition border border-transparent hover:border-emerald-100 dark:hover:border-emerald-800/30 group">
                        <span class="shrink-0 w-11 h-11 rounded-xl bg-emerald-100 dark:bg-emerald-900/40 flex items-center justify-center text-emerald-700 dark:text-emerald-400 group-hover:bg-emerald-700 group-hover:text-white transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                        </span>
                        <span>
                            <span class="block font-semibold text-slate-900 dark:text-white text-sm group-hover:text-emerald-700 dark:group-hover:text-emerald-400 transition-colors">Main Health Center</span>
                            <span class="block text-xs text-slate-500 dark:text-slate-400 mt-0.5 line-clamp-2">Check-ups, diagnostics & general consultations</span>
                        </span>
                    </a>

                    <a href="{{ route('units.show', 'lying-in-clinic') }}"
                        class="flex items-start gap-4 p-4 rounded-xl hover:bg-emerald-50 dark:hover:bg-emerald-950/20 transition border border-transparent hover:border-emerald-100 dark:hover:border-emerald-800/30 group">
                        <span class="shrink-0 w-11 h-11 rounded-xl bg-emerald-100 dark:bg-emerald-900/40 flex items-center justify-center text-emerald-700 dark:text-emerald-400 group-hover:bg-emerald-700 group-hover:text-white transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z"/></svg>
                        </span>
                        <span>
                            <span class="block font-semibold text-slate-900 dark:text-white text-sm group-hover:text-emerald-700 dark:group-hover:text-emerald-400 transition-colors">Lying-in Birthing Clinic</span>
                            <span class="block text-xs text-slate-500 dark:text-slate-400 mt-0.5 line-clamp-2">24/7 maternity care, delivery & newborn screening</span>
                        </span>
                    </a>

                    <a href="{{ route('units.show', 'dental-clinic') }}"
                        class="flex items-start gap-4 p-4 rounded-xl hover:bg-emerald-50 dark:hover:bg-emerald-950/20 transition border border-transparent hover:border-emerald-100 dark:hover:border-emerald-800/30 group">
                        <span class="shrink-0 w-11 h-11 rounded-xl bg-emerald-100 dark:bg-emerald-900/40 flex items-center justify-center text-emerald-700 dark:text-emerald-400 group-hover:bg-emerald-700 group-hover:text-white transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15.182 15.182a4.5 4.5 0 01-6.364 0M21 12a9 9 0 11-18 0 9 9 0 0118 0zM9.75 9.75c0 .414-.168.75-.375.75S9 10.164 9 9.75 9.168 9 9.375 9s.375.336.375.75zm-.375 0h.008v.015h-.008V9.75zm5.625 0c0 .414-.168.75-.375.75s-.375-.336-.375-.75.168-.75.375-.75.375.336.375.75zm-.375 0h.008v.015h-.008V9.75z"/></svg>
                        </span>
                        <span>
                            <span class="block font-semibold text-slate-900 dark:text-white text-sm group-hover:text-emerald-700 dark:group-hover:text-emerald-400 transition-colors">Dental Clinic</span>
                            <span class="block text-xs text-slate-500 dark:text-slate-400 mt-0.5 line-clamp-2">Extraction, prophylaxis & dental hygiene</span>
                        </span>
                    </a>

                    <a href="{{ route('units.show', 'tb-dots-facility') }}"
                        class="flex items-start gap-4 p-4 rounded-xl hover:bg-emerald-50 dark:hover:bg-emerald-950/20 transition border border-transparent hover:border-emerald-100 dark:hover:border-emerald-800/30 group">
                        <span class="shrink-0 w-11 h-11 rounded-xl bg-emerald-100 dark:bg-emerald-900/40 flex items-center justify-center text-emerald-700 dark:text-emerald-400 group-hover:bg-emerald-700 group-hover:text-white transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.75 3.104v5.714a2.25 2.25 0 01-.659 1.591L5 14.5M9.75 3.104c-.251.023-.501.05-.75.082m.75-.082a24.301 24.301 0 014.5 0m0 0v5.714c0 .597.237 1.17.659 1.591L19.8 15.3M14.25 3.104c.251.023.501.05.75.082M19.8 15.3l-1.57.393A9.065 9.065 0 0112 15a9.065 9.065 0 00-6.23.693L5 14.5m14.8.8l1.402 1.402c1.232 1.232.65 3.318-1.067 3.611A48.309 48.309 0 0112 21c-2.773 0-5.491-.235-8.135-.687-1.718-.293-2.3-2.379-1.067-3.61L5 14.5"/></svg>
                        </span>
                        <span>
                            <span class="block font-semibold text-slate-900 dark:text-white text-sm group-hover:text-emerald-700 dark:group-hover:text-emerald-400 transition-colors">TB DOTS Center</span>
                            <span class="block text-xs text-slate-500 dark:text-slate-400 mt-0.5 line-clamp-2">TB screening, direct observation therapy & monitoring</span>
                        </span>
                    </a>

                    <a href="{{ route('units.show', 'animal-bite-center') }}"
                        class="flex items-start gap-4 p-4 rounded-xl hover:bg-emerald-50 dark:hover:bg-emerald-950/20 transition border border-transparent hover:border-emerald-100 dark:hover:border-emerald-800/30 group">
                        <span class="shrink-0 w-11 h-11 rounded-xl bg-emerald-100 dark:bg-emerald-900/40 flex items-center justify-center text-emerald-700 dark:text-emerald-400 group-hover:bg-emerald-700 group-hover:text-white transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v3m0-10.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285zm0 13.036h.008v.008H12v-.008z"/></svg>
                        </span>
                        <span>
                            <span class="block font-semibold text-slate-900 dark:text-white text-sm group-hover:text-emerald-700 dark:group-hover:text-emerald-400 transition-colors">Animal Bite Treatment Center</span>
                            <span class="block text-xs text-slate-500 dark:text-slate-400 mt-0.5 line-clamp-2">Immediate wound care & anti-rabies vaccination</span>
                        </span>
                    </a>
                </div>

            </div>
        </div>

        <div id="mega-menu-appointment-dropdown"
            class="hidden bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-700 shadow-xl border-y absolute w-full z-50 left-0 max-h-[60vh] overflow-y-auto">
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
            class="hidden bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-700 shadow-xl border-y absolute w-full z-50 left-0 max-h-[60vh] overflow-y-auto">
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

    <!-- STAY INFORMED section removed -->

    <footer class="bg-[#1e4620] pt-16 pb-8 text-green-100 text-sm w-full">
        <div class="max-w-7xl mx-auto px-4 md:px-8 grid grid-cols-1 md:grid-cols-4 gap-10 mb-12 relative z-10">
            <div class="col-span-1 md:col-span-2 flex flex-col sm:flex-row gap-6">
                <div class="h-20 w-20 shrink-0 flex items-center justify-center p-2 shadow-inner">
                    <img src="{{ asset('assets/images/logo.png') }}" class="w-full h-auto object-contain"
                        alt="RHU Logo">
                </div>
                <div>
                    <p class="text-lg font-medium text-white mb-1">{{ \App\Models\SiteSetting::get('footer_address_line1', 'M.H del Pilar St.') }}</p>
                    <p class="text-lg font-medium text-white mb-4">{{ \App\Models\SiteSetting::get('footer_address_line2', 'Silang, Cavite') }}</p>
                    <a href="tel:{{ \App\Models\SiteSetting::get('footer_phone', '(046) 414-0209') }}"
                        class="block mb-2 hover:text-white underline underline-offset-4 decoration-green-600/50 font-medium transition">{{ \App\Models\SiteSetting::get('footer_phone', '(046) 414-0209') }}</a>
                    <a href="mailto:{{ \App\Models\SiteSetting::get('footer_email', 'contact@silang.gov.ph') }}"
                        class="block hover:text-white underline underline-offset-4 decoration-green-600/50 font-medium transition">{{ \App\Models\SiteSetting::get('footer_email', 'contact@silang.gov.ph') }}</a>
                </div>
            </div>

            <div class="flex flex-col gap-3">
                <h3 class="font-bold text-white mb-2 uppercase tracking-wide">Quick Links</h3>
                <a href="{{ route('welcome') }}" class="hover:text-white hover:underline transition">Home</a>
                <a href="{{ url('/#schedule') }}" class="hover:text-white hover:underline transition">Doctors
                    Schedule</a>
                <a href="{{ url('/#announcements') }}" class="hover:text-white hover:underline transition">Events &
                    News</a>
                <a href="{{ route('appointment.create') }}" class="hover:text-white hover:underline transition">Book
                    Appointment</a>
                <a href="{{ route('appointment.manage') }}" class="hover:text-white hover:underline transition">Manage
                    Appointment</a>
                <a href="#" @click.prevent="showFaq = true" class="hover:text-white hover:underline transition">FAQs</a>
            </div>

            <div class="flex flex-col gap-3">
                <h3 class="font-bold text-white mb-2 uppercase tracking-wide">Legal & Staff</h3>
                <a href="#" @click.prevent="showPrivacy = true" class="hover:text-white hover:underline transition">Data
                    Privacy Policy</a>
                @auth
                    <a href="{{ \App\Http\Controllers\AuthController::homeRouteForRole(auth()->user()->role ?? 'guest') }}"
                        class="hover:text-white hover:underline transition mt-4 font-bold text-teal-300">Go to Dashboard</a>
                @else
                    <a href="{{ route('login') }}"
                        class="hover:text-white hover:underline transition mt-4 font-bold text-green-400">Staff Log In</a>
                @endauth
            </div>
        </div>
        <div
            class="text-center text-xs text-green-800 dark:text-green-400/60 mt-8 pt-8 border-t border-green-800/30 flex flex-col md:flex-row justify-between items-center max-w-7xl mx-auto px-4 md:px-8 relative z-10">
            <p class="text-green-200/60 font-medium">&copy; {{ date('Y') }} Rural Health Unit. All rights reserved.</p>
            <p class="text-green-200/60 mt-2 md:mt-0 font-medium">Refined by <span class="text-green-400 font-bold">MIS
                    Team</span></p>
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
    <div x-show="open" style="display: none;" class="fixed inset-0 z-100 overflow-y-auto" aria-labelledby="modal-title"
        role="dialog" aria-modal="true">

        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="open" class="fixed inset-0 bg-gray-900/75 transition-opacity" @click="open = false"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div x-show="open"
                class="relative inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full">
                <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div
                            class="mx-auto shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-teal-100 dark:bg-teal-900/40 sm:mx-0 sm:h-10 sm:w-10">
                            <svg class="h-6 w-6 text-teal-600 dark:text-teal-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                            <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white" x-text="title"></h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500 dark:text-gray-400" x-text="message"></p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 dark:bg-gray-900 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <template x-if="action">
                        <form :action="action" method="POST" class="inline-flex w-full sm:ml-3 sm:w-auto">
                            @csrf
                            <input type="hidden" name="_method" :value="method">
                            <button type="submit"
                                class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-teal-600 text-base font-medium text-white hover:bg-teal-700 focus:outline-none sm:text-sm"
                                x-text="confirmText"></button>
                        </form>
                    </template>
                    <template x-if="!action">
                        <button type="button" @click="confirm()"
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-teal-600 text-base font-medium text-white hover:bg-teal-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm"
                            x-text="confirmText"></button>
                    </template>
                    <button type="button" @click="open = false"
                        class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 dark:border-gray-600 shadow-sm px-4 py-2 bg-white dark:bg-gray-800 text-base font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 dark:bg-gray-900 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">Cancel</button>
                </div>
            </div>
        </div>
    </div>

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

</html>