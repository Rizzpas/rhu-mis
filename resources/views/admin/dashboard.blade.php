@extends('layouts.admin')

@section('header', 'Analytics Dashboard')

@section('content')
    <div class="mb-6 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Management Information System</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Real-time operational and epidemiological analytics.
            </p>
        </div>

        <div class="flex items-center gap-3">
            <button onclick="window.print()" class="hidden md:flex items-center gap-2 h-11 px-5 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white font-bold text-sm shadow-md shadow-emerald-600/20 hover:shadow-emerald-600/30 transition-all active:scale-95 cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                <span>Print Report</span>
            </button>
            <div x-data class="h-11 flex items-center gap-2 bg-white dark:bg-slate-800 px-3 rounded-xl shadow-2xs border border-slate-300 dark:border-slate-700">
                <label for="time_filter"
                    class="text-[11px] font-extrabold text-slate-500 dark:text-slate-400 uppercase tracking-wider pl-1">Timeframe:</label>
                <div class="w-36">
                    <x-select 
                        id="time_filter" 
                        :options="['today' => 'Today', 'weekly' => 'This Week', 'monthly' => 'This Month', 'yearly' => 'This Year', 'all' => 'All Time']" 
                        value="monthly"
                        x-model="$store.dashboard.timeFilter" 
                        @change="$store.dashboard.refresh()"
                        size="sm"
                        class="border-0 shadow-none font-bold text-slate-800 dark:text-white !py-0"
                    />
                </div>
                <div x-show="$store.dashboard.loading" class="ml-1">
                    <svg class="animate-spin h-4 w-4 text-emerald-500" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Hero Banner / Quick Status (Mockup Chrome + Black-Green Gradient) -->
    <div class="relative overflow-hidden rounded-3xl p-6 sm:p-8 mb-10 text-white bg-gradient-to-br from-slate-950 via-[#0a1513] to-[#04241d] border border-emerald-500/20 shadow-2xl">
        <!-- Ambient radial glow -->
        <div class="absolute -top-24 -right-24 w-96 h-96 bg-emerald-500/10 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-24 -left-24 w-80 h-80 bg-teal-500/10 rounded-full blur-3xl pointer-events-none"></div>

        <!-- Browser Chrome Mockup Top Bar -->
        <div class="relative z-10 flex flex-wrap items-center justify-between gap-3 pb-5 mb-6 border-b border-white/10">
            <div class="flex items-center gap-3">
                <div class="flex items-center gap-1.5">
                    <span class="w-2.5 h-2.5 rounded-full bg-rose-500/80 inline-block shadow-xs"></span>
                    <span class="w-2.5 h-2.5 rounded-full bg-amber-500/80 inline-block shadow-xs"></span>
                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-500/80 inline-block shadow-xs"></span>
                </div>
                <div class="px-3 py-1 rounded-lg bg-white/5 text-[11px] text-slate-300 font-mono flex items-center gap-1.5 border border-white/10 shadow-inner">
                    <svg class="w-3 h-3 text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                    <span>silang.gov.ph / Rural Health Unit &bull; Command Center</span>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <span class="text-[11px] font-bold uppercase tracking-wider text-emerald-400 flex items-center gap-1.5 bg-emerald-500/10 px-3 py-1 rounded-full border border-emerald-500/20">
                    <span class="relative flex h-2 w-2">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                    </span>
                    Live System Operational
                </span>
            </div>
        </div>

        <!-- Main Content Grid -->
        <div class="relative z-10 grid grid-cols-1 lg:grid-cols-12 gap-8 items-center">
            <!-- Left Info Column -->
            <div class="lg:col-span-8 space-y-4">
                <div class="flex flex-wrap items-center gap-2">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-emerald-500/15 text-emerald-300 text-[11px] font-bold uppercase tracking-widest rounded-lg border border-emerald-500/30 shadow-xs">
                        Municipality of Silang
                    </span>
                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-white/10 text-slate-300 border border-white/10">
                        {{ ucfirst(Auth::user()->role ?? 'Admin') }} Portal
                    </span>
                </div>

                <h1 class="text-2xl sm:text-4xl font-black tracking-tight leading-tight text-white">
                    Welcome back, <span class="bg-gradient-to-r from-emerald-400 via-teal-300 to-emerald-200 bg-clip-text text-transparent">{{ Auth::user()->name }}</span>!
                </h1>

                <p class="text-xs sm:text-sm text-slate-300 leading-relaxed font-normal max-w-2xl" x-data>
                    The health facility is currently active and processing patients. You have overseen
                    <span class="font-bold text-white bg-emerald-500/20 px-2 py-0.5 rounded-md border border-emerald-500/30" x-text="$store.dashboard.filtered.currentPeriodConsultations">{{ number_format($currentPeriodConsultations) }}</span>
                    consultations during this <span class="text-emerald-300 font-semibold" x-text="$store.dashboard.filterLabel">{{ $timeFilter }}</span> window.
                </p>

                <!-- Live Metrics Pills -->
                <div class="pt-2 flex flex-wrap items-center gap-3">
                    <div class="bg-slate-900/80 backdrop-blur-md rounded-xl px-3.5 py-2 border border-slate-700/60 flex items-center gap-2.5 shadow-sm"
                        x-data="{ time: '{{ now()->format('h:i:s A') }}' }"
                        x-init="setInterval(() => { time = new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: true }) }, 1000)">
                        <div class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></div>
                        <div>
                            <p class="text-[9px] uppercase font-bold tracking-widest text-slate-400">Local Time</p>
                            <p class="text-xs font-bold text-white tabular-nums" x-text="time">{{ now()->format('h:i:s A') }}</p>
                        </div>
                    </div>

                    <div class="bg-slate-900/80 backdrop-blur-md rounded-xl px-3.5 py-2 border border-slate-700/60 flex items-center gap-2.5 shadow-sm">
                        <svg class="w-3.5 h-3.5 text-teal-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        <div>
                            <p class="text-[9px] uppercase font-bold tracking-widest text-slate-400">System Date</p>
                            <p class="text-xs font-bold text-white">{{ now()->format('M d, Y') }}</p>
                        </div>
                    </div>

                    <div class="bg-slate-900/80 backdrop-blur-md rounded-xl px-3.5 py-2 border border-slate-700/60 flex items-center gap-2.5 shadow-sm">
                        <svg class="w-3.5 h-3.5 text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        <div>
                            <p class="text-[9px] uppercase font-bold tracking-widest text-slate-400">Active Duty</p>
                            <p class="text-xs font-bold text-white">{{ $presentDoctors + $presentNurses }} Personnel</p>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="pt-2 flex flex-wrap items-center gap-3">
                    <a href="{{ route('admin.analytics') }}" class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-emerald-500 to-teal-500 hover:from-emerald-400 hover:to-teal-400 text-slate-950 text-xs font-extrabold shadow-lg shadow-emerald-500/25 transition-all hover:scale-[1.02] active:scale-95 flex items-center gap-2 cursor-pointer">
                        <span>Launch Analytics</span>
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </a>
                    <button onclick="window.print()" class="px-4 py-2.5 rounded-xl bg-white/10 hover:bg-white/15 text-white text-xs font-semibold border border-white/10 transition-all flex items-center gap-2 cursor-pointer">
                        <svg class="w-3.5 h-3.5 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                        <span>Print Report</span>
                    </button>
                </div>
            </div>

            <!-- Right Circular Showcase Frame -->
            <div class="lg:col-span-4 flex justify-center">
                <div class="relative">
                    <div class="relative w-44 h-44 sm:w-56 sm:h-56 rounded-full overflow-hidden border-4 border-emerald-400/40 shadow-2xl shadow-emerald-500/30 bg-slate-900 flex items-center justify-center ring-8 ring-emerald-500/10 transition-transform duration-500 hover:scale-105">
                        <img src="{{ asset('assets/images/rhu-facility.jpg') }}" alt="RHU Facility" class="w-full h-full object-cover">
                    </div>
                    <!-- Overlaid status badge -->
                    <div class="absolute -bottom-2 left-1/2 -translate-x-1/2 whitespace-nowrap bg-slate-900/90 backdrop-blur-md px-3.5 py-1 rounded-full border border-emerald-400/30 text-[10px] font-bold text-emerald-300 shadow-lg flex items-center gap-1.5">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                        Main Clinical Facility
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Key Performance Indicators (KPIs) -->
    <div class="grid grid-cols-2 lg:grid-cols-6 gap-5 xl:gap-6 mb-10" x-data="{
                stats: {
                    totalDoctors: {{ $totalDoctors }},
                    presentDoctors: {{ $presentDoctors }},
                    presentNurses: {{ $presentNurses }},
                    todayAppointments: {{ $todayAppointments }},
                },
                fetchStats() {
                    fetch('{{ route('admin.dashboard.stats') }}')
                        .then(r => r.json())
                        .then(data => { this.stats = data; })
                        .catch(err => console.error(err));
                },
                init() {
                    setInterval(() => { this.fetchStats(); }, 10000);
                }
             }" x-init="init()">

        <!-- 1. Doctors Present -->
        <div x-data="{ showTooltip: false }" @mouseenter="showTooltip = true" @mouseleave="showTooltip = false"
            class="bg-white dark:bg-slate-800 rounded-3xl p-5 shadow-xs hover:shadow-sm transition-all border border-slate-200/90 dark:border-slate-700/80 flex flex-col justify-between relative group">
            
            <!-- Custom Tooltip -->
            <div x-show="showTooltip" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 translate-y-1" class="absolute z-50 bottom-full mb-3 left-1/2 -translate-x-1/2 w-48 p-3 bg-slate-950 text-white text-[10px] rounded-xl shadow-2xl pointer-events-none text-center font-medium leading-relaxed border border-slate-800" style="display: none;">
                Number of doctors currently marked as 'Present' vs total doctors registered in the system.
                <div class="absolute top-full left-1/2 -ml-1 border-[6px] border-transparent border-t-slate-950"></div>
            </div>

            <div class="flex items-center justify-between mb-3">
                <span class="text-[10px] text-slate-500 dark:text-slate-400 uppercase font-bold tracking-wider">Doctors Present</span>
                <div class="w-8 h-8 rounded-xl bg-slate-100 dark:bg-slate-700/70 text-slate-600 dark:text-slate-300 flex items-center justify-center border border-slate-200/80 dark:border-slate-600/60">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                </div>
            </div>
            <div class="flex items-baseline gap-2">
                <p class="text-3xl font-black text-slate-900 dark:text-white" x-text="stats.presentDoctors">
                    {{ $presentDoctors }}
                </p>
                <p class="text-xs font-bold text-slate-400 dark:text-slate-500">/ <span x-text="stats.totalDoctors">{{ $totalDoctors }}</span> total</p>
            </div>
            <div class="mt-2 text-[10px] text-slate-500 dark:text-slate-400 flex items-center gap-1.5 font-medium">
                <span class="w-1.5 h-1.5 rounded-full bg-slate-400 dark:bg-slate-500"></span>
                <span>Active in clinic</span>
            </div>
        </div>

        <!-- 2. Nurses Present -->
        <div x-data="{ showTooltip: false }" @mouseenter="showTooltip = true" @mouseleave="showTooltip = false"
            class="bg-white dark:bg-slate-800 rounded-3xl p-5 shadow-xs hover:shadow-sm transition-all border border-slate-200/90 dark:border-slate-700/80 flex flex-col justify-between relative group">
            
            <!-- Custom Tooltip -->
            <div x-show="showTooltip" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 translate-y-1" class="absolute z-50 bottom-full mb-3 left-1/2 -translate-x-1/2 w-48 p-3 bg-slate-950 text-white text-[10px] rounded-xl shadow-2xl pointer-events-none text-center font-medium leading-relaxed border border-slate-800" style="display: none;">
                Total number of clinical and vitals nurses currently on duty.
                <div class="absolute top-full left-1/2 -ml-1 border-[6px] border-transparent border-t-slate-950"></div>
            </div>

            <div class="flex items-center justify-between mb-3">
                <span class="text-[10px] text-slate-500 dark:text-slate-400 uppercase font-bold tracking-wider">Nurses Present</span>
                <div class="w-8 h-8 rounded-xl bg-slate-100 dark:bg-slate-700/70 text-slate-600 dark:text-slate-300 flex items-center justify-center border border-slate-200/80 dark:border-slate-600/60">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                </div>
            </div>
            <div class="flex items-baseline gap-2">
                <p class="text-3xl font-black text-slate-900 dark:text-white" x-text="stats.presentNurses">
                    {{ $presentNurses }}
                </p>
                <p class="text-xs font-bold text-slate-400 dark:text-slate-500">active duty</p>
            </div>
            <div class="mt-2 text-[10px] text-slate-500 dark:text-slate-400 flex items-center gap-1.5 font-medium">
                <span class="w-1.5 h-1.5 rounded-full bg-slate-400 dark:bg-slate-500"></span>
                <span>Triage & Vitals</span>
            </div>
        </div>

        <!-- 3. Today's Appointments -->
        <div x-data="{ showTooltip: false }" @mouseenter="showTooltip = true" @mouseleave="showTooltip = false"
            class="bg-white dark:bg-slate-800 rounded-3xl p-5 shadow-xs hover:shadow-sm transition-all border border-slate-200/90 dark:border-slate-700/80 flex flex-col justify-between relative group">
            
            <!-- Custom Tooltip -->
            <div x-show="showTooltip" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 translate-y-1" class="absolute z-50 bottom-full mb-3 left-1/2 -translate-x-1/2 w-48 p-3 bg-slate-950 text-white text-[10px] rounded-xl shadow-2xl pointer-events-none text-center font-medium leading-relaxed border border-slate-800" style="display: none;">
                Total appointments scheduled for the current date.
                <div class="absolute top-full left-1/2 -ml-1 border-[6px] border-transparent border-t-slate-950"></div>
            </div>

            <div class="flex items-center justify-between mb-3">
                <span class="text-[10px] text-slate-500 dark:text-slate-400 uppercase font-bold tracking-wider">Today's Appts</span>
                <div class="w-8 h-8 rounded-xl bg-slate-100 dark:bg-slate-700/70 text-slate-600 dark:text-slate-300 flex items-center justify-center border border-slate-200/80 dark:border-slate-600/60">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                </div>
            </div>
            <div class="flex items-baseline gap-2">
                <p class="text-3xl font-black text-slate-900 dark:text-white" x-text="stats.todayAppointments">
                    {{ $todayAppointments }}
                </p>
                <p class="text-xs font-bold text-slate-400 dark:text-slate-500">bookings</p>
            </div>
            <div class="mt-2 text-[10px] text-slate-500 dark:text-slate-400 flex items-center gap-1.5 font-medium">
                <span class="w-1.5 h-1.5 rounded-full bg-slate-400 dark:bg-slate-500"></span>
                <span>Scheduled today</span>
            </div>
        </div>

        <!-- 4. Period Volume -->
        <div x-data="{ showTooltip: false }" @mouseenter="showTooltip = true" @mouseleave="showTooltip = false"
            class="bg-white dark:bg-slate-800 rounded-3xl p-5 shadow-xs hover:shadow-sm transition-all border border-slate-200/90 dark:border-slate-700/80 flex flex-col justify-between relative group">
            
            <!-- Custom Tooltip -->
            <div x-show="showTooltip" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 translate-y-1" class="absolute z-50 bottom-full mb-3 left-1/2 -translate-x-1/2 w-48 p-3 bg-slate-950 text-white text-[10px] rounded-xl shadow-2xl pointer-events-none text-center font-medium leading-relaxed border border-slate-800" style="display: none;">
                Total volume of consultations handled within the currently selected timeframe.
                <div class="absolute top-full left-1/2 -ml-1 border-[6px] border-transparent border-t-slate-950"></div>
            </div>

            <div class="flex items-center justify-between mb-3">
                <span class="text-[10px] text-slate-500 dark:text-slate-400 uppercase font-bold tracking-wider">Period Volume</span>
                <div class="w-8 h-8 rounded-xl bg-slate-100 dark:bg-slate-700/70 text-slate-600 dark:text-slate-300 flex items-center justify-center border border-slate-200/80 dark:border-slate-600/60">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                </div>
            </div>
            <div class="flex items-baseline gap-2">
                <p class="text-3xl font-black text-slate-900 dark:text-white" x-data x-text="$store.dashboard.filtered.currentPeriodConsultations">
                    {{ number_format($currentPeriodConsultations) }}
                </p>
                <span x-data="{ get trend() { return $store.dashboard.filtered.trendPercentage } }" class="text-[11px] font-bold flex items-center"
                    :class="trend > 0 ? 'text-emerald-600 dark:text-emerald-400' : (trend < 0 ? 'text-rose-600 dark:text-rose-400' : 'text-slate-400')">
                    <template x-if="trend > 0">
                        <span class="flex items-center"><svg class="w-3 h-3 mr-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg><span x-text="'+' + trend + '%'"></span></span>
                    </template>
                    <template x-if="trend < 0">
                        <span class="flex items-center"><svg class="w-3 h-3 mr-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"></path></svg><span x-text="trend + '%'"></span></span>
                    </template>
                    <template x-if="trend === 0">
                        <span>0%</span>
                    </template>
                </span>
            </div>
            <div class="mt-2 text-[10px] text-slate-500 dark:text-slate-400 flex items-center gap-1.5 font-medium">
                <span class="w-1.5 h-1.5 rounded-full bg-slate-400 dark:bg-slate-500"></span>
                <span>Total consults</span>
            </div>
        </div>

        <!-- 5. Avg Wait Time -->
        <div x-data="{ showTooltip: false }" @mouseenter="showTooltip = true" @mouseleave="showTooltip = false"
            class="bg-white dark:bg-slate-800 rounded-3xl p-5 shadow-xs hover:shadow-sm transition-all border border-slate-200/90 dark:border-slate-700/80 flex flex-col justify-between relative group">
            
            <!-- Custom Tooltip -->
            <div x-show="showTooltip" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 translate-y-1" class="absolute z-50 bottom-full mb-3 left-1/2 -translate-x-1/2 w-48 p-3 bg-slate-950 text-white text-[10px] rounded-xl shadow-2xl pointer-events-none text-center font-medium leading-relaxed border border-slate-800" style="display: none;">
                Average time a patient spends in the queue from triage until they are seen by a doctor.
                <div class="absolute top-full left-1/2 -ml-1 border-[6px] border-transparent border-t-slate-950"></div>
            </div>

            <div class="flex items-center justify-between mb-3">
                <span class="text-[10px] text-slate-500 dark:text-slate-400 uppercase font-bold tracking-wider">Avg Wait Time</span>
                <div class="w-8 h-8 rounded-xl bg-slate-100 dark:bg-slate-700/70 text-slate-600 dark:text-slate-300 flex items-center justify-center border border-slate-200/80 dark:border-slate-600/60">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
            <div class="flex items-baseline gap-2">
                <p class="text-3xl font-black text-slate-900 dark:text-white" x-data>
                    <span x-text="`${$store.dashboard.filtered.avgWaitTime}m`">{{ $avgWaitTime }}m</span>
                </p>
                <p class="text-xs font-bold text-slate-400 dark:text-slate-500">triage to doc</p>
            </div>
            <div class="mt-2 text-[10px] text-slate-500 dark:text-slate-400 flex items-center gap-1.5 font-medium">
                <span class="w-1.5 h-1.5 rounded-full bg-slate-400 dark:bg-slate-500"></span>
                <span>Queue efficiency</span>
            </div>
        </div>

        <!-- 6. Return Rate -->
        <div
            class="bg-white dark:bg-slate-800 rounded-3xl p-5 shadow-xs hover:shadow-sm transition-all border border-slate-200/90 dark:border-slate-700/80 flex flex-col justify-between relative overflow-hidden group">
            <div class="flex items-center justify-between mb-2">
                <span class="text-[10px] text-slate-500 dark:text-slate-400 uppercase font-bold tracking-wider">Return Rate</span>
                <div class="w-8 h-8 rounded-xl bg-slate-100 dark:bg-slate-700/70 text-slate-600 dark:text-slate-300 flex items-center justify-center border border-slate-200/80 dark:border-slate-600/60">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
            <div class="flex items-center justify-between">
                <div class="relative w-12 h-12 shrink-0">
                    <canvas id="returnRateChart"></canvas>
                    <div class="absolute inset-0 flex items-center justify-center" x-data>
                        <span class="text-[11px] font-black text-slate-900 dark:text-white"
                            x-text="$store.dashboard.filtered.complianceRate + '%'">{{ $complianceRate }}%</span>
                    </div>
                </div>
                <div class="text-right">
                    <p class="text-xs font-black text-slate-900 dark:text-white" x-data x-text="`${$store.dashboard.filtered.complianceRate}%`">{{ $complianceRate }}%</p>
                    <p class="text-[10px] text-slate-400 dark:text-slate-500">Follow-up</p>
                </div>
            </div>
            <div class="mt-2 text-[10px] text-slate-500 dark:text-slate-400 flex items-center gap-1.5 font-medium">
                <span class="w-1.5 h-1.5 rounded-full bg-slate-400 dark:bg-slate-500"></span>
                <span>Compliance target</span>
            </div>
            <!-- Tooltip Hover Overlay -->
            <div
                class="absolute inset-0 bg-slate-950/90 flex items-center justify-center p-3 opacity-0 group-hover:opacity-100 transition-opacity duration-300" x-data>
                <p class="text-[10px] text-white text-center font-medium leading-tight">Based on <span x-text="$store.dashboard.filtered.totalFollowupsNeeded" class="font-bold text-slate-200">{{ $totalFollowupsNeeded }}</span>
                    patients requiring follow-up in the last 30+ days.</p>
            </div>
        </div>
    </div>

    <!-- Dashboard Main Layout Restructure -->
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-8 mb-8">

        <!-- Left Column: Announcements & System Alerts -->
        <div class="xl:col-span-1 space-y-8 order-2 xl:order-2">
            <!-- Recent Announcements -->
            <div
                class="bg-white dark:bg-slate-800 rounded-3xl shadow-xs border border-slate-200/90 dark:border-slate-700/80 h-fit overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-700/80 flex justify-between items-center bg-slate-50/50 dark:bg-slate-800/60">
                    <div class="flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-slate-400 dark:bg-slate-500"></span>
                        <h3 class="text-xs font-bold text-slate-800 dark:text-white uppercase tracking-wider">Recent Announcements</h3>
                    </div>
                    <a href="{{ route('admin.announcements.index') }}"
                        class="text-xs font-bold text-emerald-600 dark:text-emerald-400 hover:text-emerald-700 dark:hover:text-emerald-300 transition-colors">Manage All</a>
                </div>
                <div class="p-6 space-y-4">
                    @forelse($announcements as $announcement)
                        <div
                            class="border-b border-slate-100 dark:border-slate-700/60 pb-4 last:border-0 last:pb-0 hover:translate-x-1 transition-transform">
                            <p class="text-sm font-bold text-slate-900 dark:text-white truncate">{{ $announcement->title }}</p>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 line-clamp-2">
                                {{ Str::limit($announcement->content, 80) }}
                            </p>
                            <span
                                class="text-[10px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500 block mt-2">{{ $announcement->created_at->diffForHumans() }}</span>
                        </div>
                    @empty
                        <div
                            class="flex flex-col items-center justify-center py-10 text-center text-slate-500 dark:text-slate-400">
                            <div
                                class="p-3 bg-slate-100 dark:bg-slate-700/50 rounded-2xl mb-3 border border-slate-200/60 dark:border-slate-600/50">
                                <svg class="w-6 h-6 text-slate-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z">
                                    </path>
                                </svg>
                            </div>
                            <p class="text-sm font-bold text-slate-800 dark:text-white">No active announcements</p>
                            <p class="text-xs mt-1 px-4">There are currently no announcements to display for this period.</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Data Retention Alerts -->
            <div
                class="bg-white dark:bg-slate-800 rounded-3xl shadow-xs border border-slate-200/90 dark:border-slate-700/80 h-fit overflow-hidden">
                <div
                    class="px-6 py-4 border-b border-slate-100 dark:border-slate-700/80 bg-slate-50/50 dark:bg-slate-800/60 flex justify-between items-center">
                    <h3
                        class="text-xs font-bold text-slate-800 dark:text-white flex items-center gap-2 uppercase tracking-wider">
                        <svg class="w-4 h-4 text-slate-500 dark:text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                            </path>
                        </svg>
                        Data Retention Compliance
                    </h3>
                    @if($pendingDeletionCount > 0)
                        <span
                            class="bg-rose-100 dark:bg-rose-900/50 text-rose-800 dark:text-rose-300 text-[10px] px-2 py-0.5 rounded-full border border-rose-200 dark:border-rose-800 font-extrabold">{{ $pendingDeletionCount }}
                            Pending</span>
                    @endif
                </div>
                <div class="p-6">
                    @if($pendingDeletionCount > 0)
                        <div
                            class="bg-rose-50 dark:bg-rose-950/30 border border-rose-200 dark:border-rose-900/50 text-rose-700 dark:text-rose-300 p-4 rounded-2xl mb-4 text-xs leading-relaxed shadow-xs">
                            <strong class="font-bold">Critical Action Required:</strong> There are <strong
                                class="font-bold text-rose-800 dark:text-rose-200">{{ $pendingDeletionCount }}</strong> inactive patient records scheduled for
                            permanent deletion based on the 10-year RHU Data Retention Policy.
                        </div>
                        <a href="{{ route('admin.retention.index') }}"
                            class="block bg-rose-600 hover:bg-rose-700 text-white font-bold py-2.5 px-4 rounded-xl text-xs transition-all text-center shadow-md shadow-rose-600/20 active:scale-95">
                            Review & Manage Records
                        </a>
                    @else
                        <div
                            class="flex flex-col items-center justify-center py-4 text-center text-slate-500 dark:text-slate-400">
                            <div
                                class="p-3 bg-slate-100 dark:bg-slate-700/50 text-slate-600 dark:text-slate-300 rounded-2xl mb-3 border border-slate-200/80 dark:border-slate-600/60">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7">
                                    </path>
                                </svg>
                            </div>
                            <p class="font-bold text-slate-800 dark:text-white text-sm">All records are compliant.</p>
                            <p class="text-xs mt-1">No expired records require deletion at this time.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Right Column: Operational & Deep Analytics Charts -->
        <div class="xl:col-span-2 space-y-8 order-1 xl:order-1">

            <!-- Operational Charts Row -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div
                    class="bg-white dark:bg-slate-800 rounded-3xl p-6 sm:p-8 shadow-xs border border-slate-200/90 dark:border-slate-700/80 flex flex-col">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h3 class="text-sm font-bold text-slate-800 dark:text-white uppercase tracking-wider flex items-center gap-1.5" title="Tracks the volume of patient visits over the last 7 days.">
                                Daily Volume
                                <svg class="w-3.5 h-3.5 text-slate-400 cursor-help" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Patient consultation throughput</p>
                        </div>
                    </div>
                    <div class="relative h-64 w-full">
                        <canvas id="visitVolumeChart"></canvas>
                    </div>
                </div>

                <div
                    class="bg-white dark:bg-slate-800 rounded-3xl p-6 sm:p-8 shadow-xs border border-slate-200/90 dark:border-slate-700/80 flex flex-col">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h3 class="text-sm font-bold text-slate-800 dark:text-white uppercase tracking-wider flex items-center gap-1.5" title="Distribution of patient arrival times throughout the day.">
                                Peak Hours
                                <svg class="w-3.5 h-3.5 text-slate-400 cursor-help" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Distribution across operating hours</p>
                        </div>
                    </div>
                    <div class="relative h-64 w-full">
                        <canvas id="peakHoursChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Pharmacy Inventory Insights -->
            <div class="mt-8">
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-10 h-10 bg-slate-100 dark:bg-slate-700/70 text-slate-700 dark:text-slate-300 rounded-2xl border border-slate-200/80 dark:border-slate-600/60 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" /></svg>
                    </div>
                    <div>
                        <h3 class="text-base font-extrabold text-slate-800 dark:text-white">Pharmacy Inventory Insights</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400">Medicine dispensing demand and trends for restocking decisions.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <!-- Top Dispensed Medicines -->
                    <div class="bg-white dark:bg-slate-800 rounded-3xl p-6 sm:p-8 shadow-xs border border-slate-200/90 dark:border-slate-700/80">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-xs font-bold text-slate-800 dark:text-white uppercase tracking-wider flex items-center gap-1">
                                Top Dispensed (30 Days)
                                <svg class="w-3.5 h-3.5 text-slate-400 cursor-help" fill="none" stroke="currentColor" viewBox="0 0 24 24" title="Most frequently dispensed medicines over the last 30 days."><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </h3>
                        </div>
                        @if($topDispensed->count() > 0)
                            <div class="relative h-64 w-full">
                                <canvas id="adminTopDispensedChart"></canvas>
                            </div>
                        @else
                            <div class="flex items-center justify-center h-64 text-slate-400 dark:text-slate-500">
                                <div class="text-center">
                                    <svg class="w-10 h-10 mx-auto mb-2 opacity-50" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" /></svg>
                                    <p class="text-xs font-medium">No dispensing data available yet.</p>
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Monthly Dispensing Trend -->
                    <div class="bg-white dark:bg-slate-800 rounded-3xl p-6 sm:p-8 shadow-xs border border-slate-200/90 dark:border-slate-700/80">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-xs font-bold text-slate-800 dark:text-white uppercase tracking-wider flex items-center gap-1">
                                Monthly Dispensing Trend
                                <svg class="w-3.5 h-3.5 text-slate-400 cursor-help" fill="none" stroke="currentColor" viewBox="0 0 24 24" title="Total units dispensed per month over the last 6 months."><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </h3>
                        </div>
                        @if($monthlyTrend->count() > 0)
                            <div class="relative h-64 w-full">
                                <canvas id="adminMonthlyTrendChart"></canvas>
                            </div>
                        @else
                            <div class="flex items-center justify-center h-64 text-slate-400 dark:text-slate-500">
                                <div class="text-center">
                                    <svg class="w-10 h-10 mx-auto mb-2 opacity-50" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z" /></svg>
                                    <p class="text-xs font-medium">No trend data available yet.</p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Analytics Call To Action (Matching Dark Black-Green Aesthetic) -->
            <div class="mt-8 bg-gradient-to-br from-slate-950 via-slate-900 to-emerald-950 rounded-3xl p-8 border border-emerald-500/20 shadow-xl relative overflow-hidden group text-white">
                <div class="relative z-10 flex flex-col md:flex-row justify-between items-center gap-6">
                    <div class="max-w-xl space-y-2">
                        <div class="inline-flex items-center gap-1.5 px-3 py-1 bg-emerald-500/10 text-emerald-400 text-[10px] font-bold uppercase tracking-wider rounded-full border border-emerald-500/20">
                            Advanced Intelligence
                        </div>
                        <h3 class="text-xl sm:text-2xl font-black tracking-tight">Need Deeper Epidemiological Insights?</h3>
                        <p class="text-slate-300 text-xs sm:text-sm font-normal leading-relaxed">
                            Access the full Epidemiological & Operational Analytics suite. View full-screen interactive charts for Demographics, Barangay Heatmaps, Triage Severity, Staff Workload, and export comprehensive CSV and PDF reports.
                        </p>
                    </div>
                    <a href="{{ route('admin.analytics') }}" class="shrink-0 bg-gradient-to-r from-emerald-500 to-teal-500 hover:from-emerald-400 hover:to-teal-400 text-slate-950 px-7 py-3.5 rounded-2xl font-extrabold text-xs transition-all shadow-lg shadow-emerald-500/25 hover:scale-105 active:scale-95 flex items-center gap-2.5 cursor-pointer">
                        <span>Launch Analytics</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                    </a>
                </div>
                <!-- Ambient Glow Elements -->
                <div class="absolute -right-20 -bottom-20 w-64 h-64 bg-emerald-500/10 rounded-full blur-3xl group-hover:scale-110 transition-transform duration-700 pointer-events-none"></div>
                <div class="absolute -left-20 -top-20 w-48 h-48 bg-teal-500/10 rounded-full blur-2xl pointer-events-none"></div>
            </div>
        </div>
    </div>

    <!-- Chart.js Setup for Summary Dashboard -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.2.0"></script>
    <script>
        // Register Alpine store BEFORE Alpine initializes components
        document.addEventListener('alpine:init', () => {
            Alpine.store('dashboard', {
                timeFilter: @json($timeFilter),
                loading: false,
                filtered: {
                    currentPeriodConsultations: @json(number_format($currentPeriodConsultations)),
                    currentPeriodConsultationsRaw: {{ $currentPeriodConsultations }},
                    trendPercentage: {{ $trendPercentage }},
                    avgWaitTime: {{ $avgWaitTime }},
                    complianceRate: {{ $complianceRate }},
                    totalFollowupsNeeded: @json(number_format($totalFollowupsNeeded))
                },
                get filterLabel() {
                    const labels = { today: 'today', weekly: 'weekly', monthly: 'monthly', yearly: 'yearly', all: 'all-time' };
                    return labels[this.timeFilter] || this.timeFilter;
                },
                async refresh() {
                    this.loading = true;
                    try {
                        const statsRes = await fetch(window.__dashboardStatsUrl + '?time_filter=' + this.timeFilter);
                        const stats = await statsRes.json();
                        this.filtered = { ...this.filtered, ...stats };
                        await window.__refreshVolumeChart(this.timeFilter);
                        await window.__refreshPeakChart(this.timeFilter);
                        window.__refreshReturnRate(this.filtered.complianceRate);
                    } catch (e) {
                        console.error('Dashboard refresh error:', e);
                    } finally {
                        this.loading = false;
                    }
                }
            });
        });

        document.addEventListener("DOMContentLoaded", function () {
            Chart.register(ChartDataLabels);

            // Modern Global Defaults
            Chart.defaults.font.family = "'Inter', 'system-ui', 'sans-serif'";
            Chart.defaults.color = '#94a3b8';
            Chart.defaults.plugins.tooltip.backgroundColor = '#0f172a';
            Chart.defaults.plugins.tooltip.titleFont = { size: 13, weight: '600', family: "'Inter', sans-serif" };
            Chart.defaults.plugins.tooltip.bodyFont = { size: 12, family: "'Inter', sans-serif" };
            Chart.defaults.plugins.tooltip.padding = { top: 10, bottom: 10, left: 14, right: 14 };
            Chart.defaults.plugins.tooltip.cornerRadius = 10;
            Chart.defaults.plugins.tooltip.displayColors = false;
            Chart.defaults.plugins.tooltip.borderColor = 'rgba(255,255,255,0.1)';
            Chart.defaults.plugins.tooltip.borderWidth = 1;
            Chart.defaults.plugins.datalabels.color = '#ffffff';
            Chart.defaults.plugins.datalabels.font = { weight: '600', size: 11 };
            Chart.defaults.plugins.datalabels.formatter = Math.round;
            Chart.defaults.plugins.datalabels.display = function(ctx) { return ctx.dataset.data[ctx.dataIndex] > 0; };

            // --- Chart Instances (stored for dynamic updates) ---
            let volumeChart = null;
            let peakChart = null;
            let returnRateChartInstance = null;

            const chartBaseUrl = @json(route('admin.analytics.chart', ['chart' => '__CHART__']));
            window.__dashboardStatsUrl = @json(route('admin.dashboard.statsData'));

            function getChartUrl(chart, filter) {
                return chartBaseUrl.replace('__CHART__', chart) + '?time_filter=' + filter;
            }

            // --- Helper: Create gradient ---
            function makeGradient(ctx, color, height = 300) {
                const g = ctx.createLinearGradient(0, 0, 0, height);
                g.addColorStop(0, color.replace(')', ', 0.35)').replace('rgb', 'rgba'));
                g.addColorStop(1, color.replace(')', ', 0.01)').replace('rgb', 'rgba'));
                return g;
            }

            // --- Return Rate Ring ---
            function renderReturnRate(rate) {
                const ctx = document.getElementById('returnRateChart').getContext('2d');
                const color = rate >= 50 ? '#10b981' : '#e11d48';
                if (returnRateChartInstance) returnRateChartInstance.destroy();
                returnRateChartInstance = new Chart(ctx, {
                    type: 'doughnut',
                    data: { datasets: [{ data: [rate, 100 - rate], backgroundColor: [color, 'rgba(100,116,139,0.08)'], borderWidth: 0, borderRadius: 6, cutout: '78%' }] },
                    options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false }, tooltip: { enabled: false }, datalabels: { display: false } }, animation: { animateScale: true, animateRotate: true, duration: 800 } }
                });
            }
            function refreshReturnRate(rate) { renderReturnRate(rate); }
            window.__refreshReturnRate = refreshReturnRate;

            // --- Visit Volume Chart ---
            async function refreshVolumeChart(filter) {
                const res = await fetch(getChartUrl('volume', filter));
                const data = await res.json();
                const ctx = document.getElementById('visitVolumeChart').getContext('2d');
                const gradient = makeGradient(ctx, 'rgb(16, 185, 129)');
                if (volumeChart) volumeChart.destroy();
                volumeChart = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: data.labels,
                        datasets: [{
                            label: 'Patient Visits',
                            data: data.data,
                            borderColor: '#10b981',
                            backgroundColor: gradient,
                            borderWidth: 2.5,
                            tension: 0.45,
                            fill: true,
                            pointBackgroundColor: '#ffffff',
                            pointBorderColor: '#10b981',
                            pointBorderWidth: 2,
                            pointRadius: data.data.length > 15 ? 0 : 4,
                            pointHoverRadius: 6
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        layout: { padding: { top: 20 } },
                        interaction: { intersect: false, mode: 'index' },
                        plugins: { legend: { display: false }, datalabels: { align: 'top', anchor: 'end', color: '#10b981', backgroundColor: 'rgba(255,255,255,0.85)', borderRadius: 6, padding: { top: 3, bottom: 3, left: 6, right: 6 }, font: { size: 10, weight: '600' }, display: function(ctx) { return ctx.dataset.data.length <= 15 && ctx.dataset.data[ctx.dataIndex] > 0; } } },
                        scales: {
                            y: { beginAtZero: true, suggestedMax: 5, ticks: { precision: 0, stepSize: 1, font: { size: 11 } }, grid: { color: 'rgba(148,163,184,0.08)', drawBorder: false }, border: { display: false } },
                            x: { grid: { display: false }, border: { display: false }, ticks: { font: { size: 10 }, maxRotation: data.data.length > 15 ? 45 : 0, autoSkip: true, maxTicksLimit: 12 } }
                        },
                        animation: { duration: 600, easing: 'easeOutQuart' }
                    }
                });
            }
            window.__refreshVolumeChart = refreshVolumeChart;

            // --- Peak Hours Chart ---
            async function refreshPeakChart(filter) {
                const res = await fetch(getChartUrl('peak', filter));
                const data = await res.json();
                const ctx = document.getElementById('peakHoursChart').getContext('2d');
                const barGradient = ctx.createLinearGradient(0, 0, 0, 280);
                barGradient.addColorStop(0, '#8b5cf6');
                barGradient.addColorStop(1, '#6d28d9');
                if (peakChart) peakChart.destroy();
                peakChart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: data.labels,
                        datasets: [{
                            label: 'Consultations',
                            data: data.data,
                            backgroundColor: barGradient,
                            borderRadius: 8,
                            borderSkipped: false,
                            barPercentage: 0.55,
                            categoryPercentage: 0.8
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        layout: { padding: { top: 20 } },
                        plugins: { legend: { display: false }, datalabels: { align: 'top', anchor: 'end', color: '#7c3aed', font: { size: 11, weight: '600' } } },
                        scales: {
                            y: { beginAtZero: true, ticks: { precision: 0, font: { size: 11 } }, grid: { color: 'rgba(148,163,184,0.08)', drawBorder: false }, border: { display: false } },
                            x: { grid: { display: false }, border: { display: false }, ticks: { font: { size: 10 } } }
                        },
                        animation: { duration: 600, easing: 'easeOutQuart' }
                    }
                });
            }
            window.__refreshPeakChart = refreshPeakChart;

            // --- Initial Render ---
            renderReturnRate({{ $complianceRate }});
            refreshVolumeChart(@json($timeFilter));
            refreshPeakChart(@json($timeFilter));

            // --- Pharmacy: Top Dispensed Bar Chart ---
            @if($topDispensed->count() > 0)
            (function() {
                const topData = @json($topDispensed);
                const labels = topData.map(d => d.medicine ? (d.medicine.name || d.medicine.generic_name || 'Unknown') : 'Unknown');
                const values = topData.map(d => d.total_dispensed);
                const ctx = document.getElementById('adminTopDispensedChart').getContext('2d');
                const barGradient = ctx.createLinearGradient(0, 0, 0, 250);
                barGradient.addColorStop(0, '#14b8a6');
                barGradient.addColorStop(1, '#0d9488');
                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Units Dispensed',
                            data: values,
                            backgroundColor: barGradient,
                            borderRadius: 8,
                            borderSkipped: false,
                            barPercentage: 0.6,
                            categoryPercentage: 0.8
                        }]
                    },
                    options: {
                        indexAxis: 'y',
                        responsive: true,
                        maintainAspectRatio: false,
                        layout: { padding: { right: 20 } },
                        plugins: {
                            legend: { display: false },
                            datalabels: { align: 'end', anchor: 'end', color: '#0d9488', font: { size: 11, weight: '600' } }
                        },
                        scales: {
                            x: { beginAtZero: true, ticks: { precision: 0, font: { size: 10 } }, grid: { color: 'rgba(148,163,184,0.08)', drawBorder: false }, border: { display: false } },
                            y: { grid: { display: false }, border: { display: false }, ticks: { font: { size: 10 }, callback: function(value) { const l = this.getLabelForValue(value); return l.length > 15 ? l.substring(0,15)+'…' : l; } } }
                        },
                        animation: { duration: 600, easing: 'easeOutQuart' }
                    }
                });
            })();
            @endif

            // --- Pharmacy: Monthly Dispensing Trend ---
            @if($monthlyTrend->count() > 0)
            (function() {
                const trendData = @json($monthlyTrend);
                const labels = trendData.map(d => d.label);
                const values = trendData.map(d => d.total_dispensed);
                const ctx = document.getElementById('adminMonthlyTrendChart').getContext('2d');
                const gradient = makeGradient(ctx, 'rgb(20, 184, 166)');
                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Units Dispensed',
                            data: values,
                            borderColor: '#14b8a6',
                            backgroundColor: gradient,
                            borderWidth: 2.5,
                            tension: 0.4,
                            fill: true,
                            pointBackgroundColor: '#ffffff',
                            pointBorderColor: '#14b8a6',
                            pointBorderWidth: 2,
                            pointRadius: 5,
                            pointHoverRadius: 7
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        layout: { padding: { top: 20 } },
                        interaction: { intersect: false, mode: 'index' },
                        plugins: {
                            legend: { display: false },
                            datalabels: { align: 'top', anchor: 'end', color: '#0d9488', font: { size: 11, weight: '600' } }
                        },
                        scales: {
                            y: { beginAtZero: true, ticks: { precision: 0, font: { size: 11 } }, grid: { color: 'rgba(148,163,184,0.08)', drawBorder: false }, border: { display: false } },
                            x: { grid: { display: false }, border: { display: false }, ticks: { font: { size: 10 } } }
                        },
                        animation: { duration: 600, easing: 'easeOutQuart' }
                    }
                });
            })();
            @endif
        });
    </script>
    <style>
        /* Custom Scrollbar for Diagnoses list */
        .custom-scrollbar::-webkit-scrollbar {
            width: 4px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: transparent;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 4px;
        }

        .dark .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #475569;
        }

        /* Print Styles for A4 Layout */
        @media print {
            @page {
                size: A4 portrait;
                margin: 15mm;
            }
            body {
                background: white !important;
                color: black !important;
                font-size: 11px !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            .print\:hidden, nav, header, aside, button, select, .no-print {
                display: none !important;
            }
            main {
                overflow: visible !important;
                padding: 0 !important;
            }
            .rounded-3xl, .rounded-2xl {
                border-radius: 8px !important;
            }
            .shadow-sm, .shadow-lg, [class*='shadow-'] {
                box-shadow: none !important;
            }
            .grid {
                display: block !important;
            }
            .grid > * {
                page-break-inside: avoid;
                break-inside: avoid;
                margin-bottom: 12px;
            }
            canvas {
                max-height: 220px !important;
            }
            .mb-10, .mb-8 {
                margin-bottom: 12px !important;
            }
            .p-8 {
                padding: 12px !important;
            }
            .gap-8, .gap-6 {
                gap: 8px !important;
            }
            h1, h2, h3 {
                page-break-after: avoid;
            }
        }
    </style>
@endsection