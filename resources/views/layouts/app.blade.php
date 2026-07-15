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
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">

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
            background-image: url('{{ asset('assets/images/bg-image-light.png') }}') !important;
        }

        html.dark body {
            background-image: url('{{ asset('assets/images/bg-image-dark.png') }}') !important;
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
    class="antialiased bg-theme-light dark:bg-theme-dark bg-cover md:bg-size-[100%_auto] bg-top bg-no-repeat bg-[#eefcf1] dark:bg-gray-900 text-gray-800 dark:text-gray-100 font-sans"
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
    @include('partials.skeleton-app')
    <!-- Top Bar -->
    <div
        class="bg-green-700 text-white text-xs py-2 px-4 md:px-8 flex md:flex-row justify-center items-center font-medium gap-4 md:gap-7 text-center">
        <span><span class="hidden sm:inline">Clinic Hours: </span>{{ \App\Models\SiteSetting::get('clinic_hours', 'Mon - Fri | 8:00 AM - 5:00 PM') }}</span>
        <span><span class="hidden sm:inline">Emergency Hotlines: </span>{{ \App\Models\SiteSetting::get('emergency_hotlines', '911 | (046) 432-1234') }}</span>
    </div>

    <!-- Main Navigation -->
    <nav
        class="bg-white dark:bg-gray-800 sticky w-full z-50 top-0 start-0 border-b border-gray-200 dark:border-gray-700 shadow-sm">
        <div class="flex flex-wrap justify-between items-center mx-auto max-w-7xl px-4 py-4 md:px-8">
            <a href="{{ route('welcome') }}" class="flex items-center gap-3 rtl:space-x-reverse">
                <div class="shrink-0">
                    <img src="{{ asset('assets/images/logo.png') }}" alt="RHU Logo"
                        class="w-10 h-10 object-contain bg-white dark:bg-gray-800 rounded-full p-0.5" />
                </div>
                <div>
                    <h1 class="font-extrabold text-lg leading-tight text-gray-900 dark:text-white">RURAL HEALTH UNIT
                    </h1>
                    <p class="text-[10px] text-gray-500 dark:text-gray-400 font-bold tracking-wider uppercase">of
                        Silang, Cavite</p>
                </div>
            </a>
            <button data-collapse-toggle="mega-menu-full" type="button"
                class="inline-flex items-center p-2 w-10 h-10 justify-center text-sm text-gray-500 dark:text-gray-400 rounded-lg md:hidden hover:bg-green-50 dark:bg-green-900/30 hover:text-green-700 dark:text-green-400 focus:outline-none focus:ring-2 focus:ring-green-200"
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
                            class="block py-2 px-3 text-gray-900 dark:text-white hover:text-green-600 hover:bg-green-50 md:hover:bg-transparent md:border-0 md:hover:text-green-600 md:p-0 transition"
                            aria-current="page">Home</a>
                    </li>
                    <li>
                        <button id="mega-menu-full-dropdown-button" data-collapse-toggle="mega-menu-full-dropdown"
                            class="flex items-center justify-between w-full py-2 px-3 font-medium text-gray-900 dark:text-white md:w-auto hover:bg-green-50 md:hover:bg-transparent md:border-0 md:hover:text-green-600 md:p-0 transition">
                            RHU Units
                            <svg class="w-4 h-4 ms-1.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24"
                                height="24" fill="none" viewBox="0 0 24 24">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="2" d="m19 9-7 7-7-7" />
                            </svg>
                        </button>
                    </li>
                    <li>
                        <a href="{{ route('appointment.create') }}"
                            class="block py-2 px-3 text-gray-900 dark:text-white hover:text-green-600 hover:bg-green-50 md:hover:bg-transparent md:border-0 md:hover:text-green-600 md:p-0 transition">
                            Appointment</a>
                    </li>

                    <li>
                        <a href="{{ route('about') }}"
                            class="block py-2 px-3 text-gray-900 dark:text-white hover:text-green-600 hover:bg-green-50 md:hover:bg-transparent md:border-0 md:hover:text-green-600 md:p-0 transition">About
                            Us</a>
                    </li>

                    <!-- Theme Toggle -->
                    <li>
                        <button id="theme-toggle" type="button"
                            class="relative inline-flex h-6 w-[42px] shrink-0 cursor-pointer items-center justify-start rounded-full border-2 border-transparent bg-gray-200 dark:bg-slate-600 transition-colors duration-200 ease-in-out">
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
                    <!-- Language Switcher -->
                    <li
                        class="hidden md:flex items-center space-x-2 border-l border-gray-200 dark:border-gray-700 pl-4 ml-2">
                        <a href="{{ route('locale.switch', 'en') }}"
                            class="text-xs font-bold {{ app()->getLocale() == 'en' ? 'text-green-700' : 'text-gray-400 hover:text-gray-600 dark:text-gray-400' }} transition">EN</a>
                        <span class="text-gray-300 text-xs">|</span>
                        <a href="{{ route('locale.switch', 'fil') }}"
                            class="text-xs font-bold {{ app()->getLocale() == 'fil' ? 'text-green-700' : 'text-gray-400 hover:text-gray-600 dark:text-gray-400' }} transition">FIL</a>
                    </li>
                </ul>
            </div>
        </div>

        <div id="mega-menu-full-dropdown"
            class="hidden bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-700 shadow-lg border-y absolute w-full z-50 left-0 max-h-[60vh] overflow-y-auto">
            <div class="grid max-w-7xl px-4 py-5 mx-auto text-gray-900 dark:text-white sm:grid-cols-2 lg:grid-cols-3 lg:px-8 gap-4"
                aria-labelledby="mega-menu-full-dropdown-button">

                <a href="{{ route('units.index') }}"
                    class="block p-4 rounded-xl bg-green-50 dark:bg-green-900/30 hover:bg-green-600/20 transition border border-transparent">
                    <div class="font-bold text-green-900 dark:text-green-400 mb-1">View All Units</div>
                    <span class="text-sm text-gray-600 dark:text-gray-400">See our complete facility overview.</span>
                </a>

                <a href="{{ route('units.show', 'main-health-center') }}"
                    class="block p-4 rounded-xl bg-green-50 dark:bg-green-900/30 hover:bg-green-600/20 transition border border-transparent">
                    <div class="font-semibold text-green-800 dark:text-green-400 mb-1">Main Health Center</div>
                    <span class="text-sm text-gray-500 dark:text-gray-400">Comprehensive check-ups, diagnostics, and
                        general medical
                        consultations.</span>
                </a>

                <a href="{{ route('units.show', 'lying-in-clinic') }}"
                    class="block p-4 rounded-xl bg-green-50 dark:bg-green-900/30 hover:bg-green-600/20 transition border border-transparent">
                    <div class="font-semibold text-green-800 dark:text-green-400 mb-1">Lying-in Clinic</div>
                    <span class="text-sm text-gray-500 dark:text-gray-400">24/7 maternity care, safe delivery, and
                        newborn screening
                        services.</span>
                </a>

                <a href="{{ route('units.show', 'dental-clinic') }}"
                    class="block p-4 rounded-xl bg-green-50 dark:bg-green-900/30 hover:bg-green-600/20 transition border border-transparent">
                    <div class="font-semibold text-green-800 dark:text-green-400 mb-1">Dental Clinic</div>
                    <span class="text-sm text-gray-500 dark:text-gray-400">Tooth extraction, oral prophylaxis, and
                        general dental
                        hygiene.</span>
                </a>

                <a href="{{ route('units.show', 'tb-dots-facility') }}"
                    class="block p-4 rounded-xl bg-green-50 dark:bg-green-900/30 hover:bg-green-600/20 transition border border-transparent">
                    <div class="font-semibold text-green-800 dark:text-green-400 mb-1">TB DOTS Facility</div>
                    <span class="text-sm text-gray-500 dark:text-gray-400">Tuberculosis screening, medication, and full
                        treatment
                        monitoring.</span>
                </a>

                <a href="{{ route('units.show', 'animal-bite-center') }}"
                    class="block p-4 rounded-xl bg-green-50 dark:bg-green-900/30 hover:bg-green-600/20 transition border border-transparent">
                    <div class="font-semibold text-green-800 dark:text-green-400 mb-1">Animal Bite Center</div>
                    <span class="text-sm text-gray-500 dark:text-gray-400">Immediate care and vaccination for rabies
                        prevention.</span>
                </a>

            </div>
        </div>
    </nav>

    <main class="min-h-screen">
        @include('partials.toast')

        @yield('content')
    </main>

    <section class="bg-green-900 py-12">
        <div class="max-w-7xl mx-auto px-8 flex flex-col md:flex-row items-center justify-between gap-6">
            <div class="text-white">
                <h3 class="font-bold text-xl mb-1">STAY INFORMED</h3>
                <p class="text-green-100 text-sm max-w-md">Receive real-time health advisories, vaccination schedules,
                    and community news from the City Health Office.</p>
            </div>
            <div class="flex w-full md:w-auto">
                <input type="email" placeholder="Email Address"
                    class="px-4 py-2 w-full md:w-64 rounded-l focus:outline-none text-sm text-gray-800">
                <button
                    class="bg-green-500 hover:bg-green-600 text-white px-6 py-2 rounded-r text-sm font-medium transition cursor-pointer">Subscribe</button>
            </div>
        </div>
    </section>

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