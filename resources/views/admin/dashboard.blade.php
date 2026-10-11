@extends('layouts.admin')

@section('header', 'RHU Executive Dashboard')

@section('content')
<div x-data>
    <!-- Header Section -->
    <div class="mb-6 flex flex-col md:flex-row justify-between items-start md:items-center gap-4 pb-4 border-b border-slate-200/80 dark:border-slate-800">
        <div>
            <h1 class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white tracking-tight flex items-center gap-2.5">
                <span class="w-9 h-9 sm:w-10 sm:h-10 rounded-2xl bg-emerald-100/80 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0 border border-emerald-500/20 shadow-2xs">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                </span>
                <span>Management Information System</span>
            </h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                High-level operational overview, patient registry census, vulnerability cohorts, and immediate administrative action triggers.
            </p>
        </div>

        <div class="flex items-center flex-wrap gap-2.5">
            <!-- Launch Analytics CTA -->
            <a href="{{ route('admin.analytics') }}" 
               class="flex items-center gap-2 h-11 px-4 rounded-xl bg-slate-900 dark:bg-slate-800 hover:bg-slate-800 dark:hover:bg-slate-700 text-white font-bold text-xs shadow-sm transition-all border border-slate-700/60 active:scale-95 cursor-pointer group">
                <svg class="w-4 h-4 text-emerald-400 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                </svg>
                <span>Detailed Analytics Suite</span>
                <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                </svg>
            </a>

            <!-- Print Report -->
            <button onclick="printDashboardReport()" 
                    class="flex items-center gap-2 h-11 px-4 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white font-bold text-xs shadow-md shadow-emerald-600/20 hover:shadow-emerald-600/30 transition-all active:scale-95 cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                </svg>
                <span>Print Dossier</span>
            </button>

            <!-- Timeframe Filter -->
            <div class="h-11 flex items-center gap-2 bg-white dark:bg-slate-800 px-3 rounded-xl shadow-2xs border border-slate-300 dark:border-slate-700">
                <label for="time_filter" class="text-[11px] font-extrabold text-slate-500 dark:text-slate-400 uppercase tracking-wider pl-1">Period:</label>
                <div class="w-36">
                    <x-select 
                        id="time_filter" 
                        :options="['today' => 'Today', 'weekly' => 'This Week', 'monthly' => 'This Month', 'yearly' => 'This Year', 'all' => 'All Time', 'custom' => 'Custom Range']" 
                        :value="$timeFilter"
                        x-model="$store.dashboard.timeFilter" 
                        @change="$store.dashboard.refresh()"
                        size="sm"
                        class="border-0 shadow-none font-bold text-slate-800 dark:text-white !py-0"
                    />
                </div>
                <!-- Custom Range Pickers -->
                <div x-show="$store.dashboard.timeFilter === 'custom'" class="flex items-center gap-1.5 pl-2 border-l border-slate-200 dark:border-slate-700" style="display: none;">
                    <input type="date" x-model="$store.dashboard.startDate" @change="$store.dashboard.refresh()" class="h-8 px-2 text-xs rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-800 dark:text-white font-medium" />
                    <span class="text-xs text-slate-400">&ndash;</span>
                    <input type="date" x-model="$store.dashboard.endDate" @change="$store.dashboard.refresh()" class="h-8 px-2 text-xs rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-800 dark:text-white font-medium" />
                </div>
                <div x-show="$store.dashboard.loading" class="ml-1" style="display: none;">
                    <svg class="animate-spin h-4 w-4 text-emerald-500" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                </div>
            </div>
        </div>
    </div>

    <!-- ═══════════════════════════════════════════════════════════════════════════
         SIGNATURE COMMAND CENTER HERO BANNER (Clock, Date, Live Readiness, Queue Wait)
         ═══════════════════════════════════════════════════════════════════════════ -->
    <div class="relative overflow-hidden rounded-3xl p-6 sm:p-8 mb-8 text-white bg-gradient-to-br from-slate-950 via-[#0a1513] to-[#04241d] border border-emerald-500/20 shadow-2xl print-hidden">
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
                        {{ ucwords(str_replace('_', ' ', Auth::user()->role ?? 'Admin')) }} Portal
                    </span>
                </div>

                <h1 class="text-2xl sm:text-4xl font-black tracking-tight leading-tight text-white">
                    Welcome back, <span class="bg-gradient-to-r from-emerald-400 via-teal-300 to-emerald-200 bg-clip-text text-transparent">{{ Auth::user()->name }}</span>!
                </h1>

                <p class="text-xs sm:text-sm text-slate-300 leading-relaxed font-normal max-w-2xl">
                    The health center is actively operating. You are monitoring
                    <span class="font-bold text-white bg-emerald-500/20 px-2 py-0.5 rounded-md border border-emerald-500/30" x-text="$store.dashboard.filtered.currentPeriodConsultations">
                        {{ number_format($currentPeriodConsultations) }}
                    </span>
                    consultations recorded during this <span class="text-emerald-300 font-semibold" x-text="$store.dashboard.filterLabel">{{ ucfirst($timeFilter) }}</span> window.
                </p>

                <!-- Live Metrics Pills -->
                <div class="pt-2 flex flex-wrap items-center gap-3">
                    <!-- 1. Real-time Clock -->
                    <div class="bg-slate-900/80 backdrop-blur-md rounded-xl px-3.5 py-2 border border-slate-700/60 flex items-center gap-2.5 shadow-sm"
                        x-data="{ time: '{{ now()->format('h:i:s A') }}' }"
                        x-init="setInterval(() => { time = new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: true }) }, 1000)">
                        <div class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></div>
                        <div>
                            <p class="text-[9px] uppercase font-bold tracking-widest text-slate-400">Local Time</p>
                            <p class="text-xs font-bold text-white tabular-nums" x-text="time">{{ now()->format('h:i:s A') }}</p>
                        </div>
                    </div>

                    <!-- 2. System Date -->
                    <div class="bg-slate-900/80 backdrop-blur-md rounded-xl px-3.5 py-2 border border-slate-700/60 flex items-center gap-2.5 shadow-sm">
                        <svg class="w-3.5 h-3.5 text-teal-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        <div>
                            <p class="text-[9px] uppercase font-bold tracking-widest text-slate-400">System Date</p>
                            <p class="text-xs font-bold text-white">{{ now()->format('M d, Y') }}</p>
                        </div>
                    </div>

                    <!-- 3. Active Clinical Personnel -->
                    <div class="bg-slate-900/80 backdrop-blur-md rounded-xl px-3.5 py-2 border border-slate-700/60 flex items-center gap-2.5 shadow-sm">
                        <svg class="w-3.5 h-3.5 text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        <div>
                            <p class="text-[9px] uppercase font-bold tracking-widest text-slate-400">Active Duty</p>
                            <p class="text-xs font-bold text-white">{{ $presentDoctors + $presentNurses }} Personnel</p>
                        </div>
                    </div>

                    <!-- 4. Avg Clinical Queue Wait (Triage to Doctor) -->
                    <div class="bg-slate-900/80 backdrop-blur-md rounded-xl px-3.5 py-2 border border-slate-700/60 flex items-center gap-2.5 shadow-sm"
                         title="Average duration from triage encoding completion until physician consultation begins.">
                        <svg class="w-3.5 h-3.5 text-amber-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <div>
                            <div class="flex items-center gap-1">
                                <p class="text-[9px] uppercase font-bold tracking-widest text-slate-400">Avg Queue Wait</p>
                                <svg class="w-2.5 h-2.5 text-slate-500 cursor-help" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            <p class="text-xs font-bold text-amber-300 tabular-nums">
                                <span x-text="`${$store.dashboard.filtered.avgWaitTime}m`">{{ $avgWaitTime }}m</span>
                                <span class="text-[10px] text-slate-400 font-normal">triage&rarr;doctor</span>
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Action CTAs -->
                <div class="pt-3 flex flex-wrap items-center gap-3">
                    <a href="{{ route('admin.analytics') }}" class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-emerald-500 to-teal-500 hover:from-emerald-400 hover:to-teal-400 text-slate-950 text-xs font-extrabold shadow-lg shadow-emerald-500/25 transition-all hover:scale-[1.02] active:scale-95 flex items-center gap-2 cursor-pointer">
                        <span>Launch Detailed Analytics Suite</span>
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </a>
                    <button onclick="printDashboardReport()" class="px-4 py-2.5 rounded-xl bg-white/10 hover:bg-white/15 text-white text-xs font-semibold border border-white/10 transition-all flex items-center gap-2 cursor-pointer">
                        <svg class="w-3.5 h-3.5 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                        <span>Print Dossier</span>
                    </button>
                </div>
            </div>

            <!-- Right Circular Showcase Frame -->
            <div class="lg:col-span-4 flex justify-center">
                <div class="relative">
                    <div class="relative w-40 h-40 sm:w-52 sm:h-52 rounded-full overflow-hidden border-4 border-emerald-400/40 shadow-2xl shadow-emerald-500/30 bg-slate-900 flex items-center justify-center ring-8 ring-emerald-500/10 transition-transform duration-500 hover:scale-105">
                        @if(auth()->user()?->avatar_url)
                            <img src="{{ auth()->user()->avatar_url }}" alt="{{ auth()->user()->name }}" class="w-full h-full object-cover">
                        @elseif(auth()->user()?->initials)
                            <div class="w-full h-full flex items-center justify-center bg-emerald-950/60 text-emerald-300 font-extrabold text-5xl">
                                {{ auth()->user()->initials }}
                            </div>
                        @else
                            <img src="{{ asset('assets/images/rhu-facility.jpg') }}" alt="RHU Facility" class="w-full h-full object-cover" onerror="this.style.display='none'">
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ═══════════════════════════════════════════════════════════════════════════
         DOMAIN ARCHITECTURE SEPARATOR (Removes Confusion between Dashboard & Analytics)
         ═══════════════════════════════════════════════════════════════════════════ -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 p-4 bg-slate-100 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 rounded-2xl mb-8 shadow-2xs">
        <div class="flex items-center gap-3">
            <span class="px-2.5 py-1 rounded-lg bg-emerald-600 text-white text-[10px] font-black uppercase tracking-wider shrink-0">
                Dashboard Overview
            </span>
            <p class="text-xs text-slate-700 dark:text-slate-300">
                Showing <strong>Executive Facility Health &amp; Daily Traffic Pulse</strong>. Looking for in-depth epidemiological pyramids, morbidity surveillance, or staff productivity?
            </p>
        </div>
        <a href="{{ route('admin.analytics') }}" class="text-xs font-bold text-emerald-600 dark:text-emerald-400 hover:underline flex items-center gap-1 shrink-0">
            <span>Open Detailed Analytics</span>
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
        </a>
    </div>

    <!-- ═══════════════════════════════════════════════════════════════════════════
         ROW 1: PATIENT CENSUS RIBBON (Primary Municipal Registry Indicators)
         ═══════════════════════════════════════════════════════════════════════════ -->
    <div class="mb-8">
        <div class="flex items-center justify-between mb-3 px-1">
            <div class="flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
                <h2 class="text-xs font-black uppercase tracking-wider text-slate-600 dark:text-slate-300">
                    Municipal Patient Census &amp; Service Utilization
                </h2>
            </div>
            <span class="text-[11px] font-semibold text-slate-400 dark:text-slate-500" x-text="'Window: ' + $store.dashboard.filterLabel">
                Window: {{ ucfirst($timeFilter) }}
            </span>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 xl:gap-5">
            <!-- 1. Total Registered Patients (Master Registry) -->
            <div class="bg-white dark:bg-slate-800 rounded-3xl p-5 border border-slate-200/90 dark:border-slate-700/80 shadow-xs hover:shadow-md transition-all flex flex-col justify-between relative overflow-hidden group">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-[10px] uppercase font-black text-slate-500 dark:text-slate-400 tracking-wider">Total Registered Patients</span>
                    <div class="w-9 h-9 rounded-2xl bg-emerald-50 dark:bg-emerald-950/50 text-emerald-600 dark:text-emerald-400 flex items-center justify-center border border-emerald-500/20">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                    </div>
                </div>

                <div>
                    <div class="flex items-baseline gap-2">
                        <p class="text-3xl font-black text-slate-900 dark:text-white tracking-tight tabular-nums"
                           x-text="$store.dashboard.filtered.census.total_registered.toLocaleString()">
                            {{ number_format($census['total_registered']) }}
                        </p>
                        <span class="text-[10px] font-bold text-slate-400 uppercase">all-time registry</span>
                    </div>

                    <div class="mt-3 pt-3 border-t border-slate-100 dark:border-slate-700/60 flex items-center justify-between text-[11px] font-semibold text-slate-500 dark:text-slate-400">
                        <div class="flex items-center gap-2">
                            <span class="text-blue-600 dark:text-blue-400 flex items-center gap-1">
                                <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>
                                M: <span x-text="$store.dashboard.filtered.census.male_registered.toLocaleString()">{{ number_format($census['male_registered']) }}</span>
                                <span class="text-[10px] text-slate-400 font-normal">(<span x-text="$store.dashboard.filtered.census.male_percentage">{{ $census['male_percentage'] }}</span>%)</span>
                            </span>
                            <span class="text-pink-600 dark:text-pink-400 flex items-center gap-1">
                                <span class="w-1.5 h-1.5 rounded-full bg-pink-500"></span>
                                F: <span x-text="$store.dashboard.filtered.census.female_registered.toLocaleString()">{{ number_format($census['female_registered']) }}</span>
                                <span class="text-[10px] text-slate-400 font-normal">(<span x-text="$store.dashboard.filtered.census.female_percentage">{{ $census['female_percentage'] }}</span>%)</span>
                            </span>
                        </div>
                        <span class="text-emerald-600 dark:text-emerald-400 font-bold">
                            <span x-text="$store.dashboard.filtered.census.philhealth_percentage">{{ $census['philhealth_percentage'] }}</span>% PhilHealth
                        </span>
                    </div>
                </div>
            </div>

            <!-- 2. New Registrations in Current Window -->
            <div class="bg-white dark:bg-slate-800 rounded-3xl p-5 border border-slate-200/90 dark:border-slate-700/80 shadow-xs hover:shadow-md transition-all flex flex-col justify-between group">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-[10px] uppercase font-black text-slate-500 dark:text-slate-400 tracking-wider">New Registrations</span>
                    <div class="w-9 h-9 rounded-2xl bg-sky-50 dark:bg-sky-950/50 text-sky-600 dark:text-sky-400 flex items-center justify-center border border-sky-500/20">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                        </svg>
                    </div>
                </div>

                <div>
                    <div class="flex items-baseline gap-2">
                        <p class="text-3xl font-black text-slate-900 dark:text-white tracking-tight tabular-nums"
                           x-text="$store.dashboard.filtered.census.new_registered.toLocaleString()">
                            {{ number_format($census['new_registered']) }}
                        </p>
                        <span class="text-[10px] font-bold text-sky-600 dark:text-sky-400 uppercase">in selected period</span>
                    </div>
                    <div class="mt-3 pt-3 border-t border-slate-100 dark:border-slate-700/60 flex items-center justify-between text-[11px] text-slate-500 dark:text-slate-400 font-medium">
                        <span>New electronic medical records created</span>
                    </div>
                </div>
            </div>

            <!-- 3. Unique Individuals Served (Deduplicated Period Patients) -->
            <div class="bg-white dark:bg-slate-800 rounded-3xl p-5 border border-slate-200/90 dark:border-slate-700/80 shadow-xs hover:shadow-md transition-all flex flex-col justify-between group">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-[10px] uppercase font-black text-slate-500 dark:text-slate-400 tracking-wider">Unique Patients Served</span>
                    <div class="w-9 h-9 rounded-2xl bg-indigo-50 dark:bg-indigo-950/50 text-indigo-600 dark:text-indigo-400 flex items-center justify-center border border-indigo-500/20">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                    </div>
                </div>

                <div>
                    <div class="flex items-baseline gap-2">
                        <p class="text-3xl font-black text-slate-900 dark:text-white tracking-tight tabular-nums"
                           x-text="$store.dashboard.filtered.census.unique_patients_served.toLocaleString()">
                            {{ number_format($census['unique_patients_served']) }}
                        </p>
                        <span class="text-[10px] font-bold text-indigo-600 dark:text-indigo-400 uppercase">distinct individuals</span>
                    </div>
                    <div class="mt-3 pt-3 border-t border-slate-100 dark:border-slate-700/60 flex items-center justify-between text-[11px] text-slate-500 dark:text-slate-400 font-medium">
                        <span>Distinct people receiving care</span>
                        <span class="text-[10px] text-slate-400">deduplicated</span>
                    </div>
                </div>
            </div>

            <!-- 4. Total Consultations & Historical Trend -->
            <div class="bg-white dark:bg-slate-800 rounded-3xl p-5 border border-slate-200/90 dark:border-slate-700/80 shadow-xs hover:shadow-md transition-all flex flex-col justify-between group">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-[10px] uppercase font-black text-slate-500 dark:text-slate-400 tracking-wider">Total Consultations</span>
                    <div class="w-9 h-9 rounded-2xl bg-teal-50 dark:bg-teal-950/50 text-teal-600 dark:text-teal-400 flex items-center justify-center border border-teal-500/20">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                </div>

                <div>
                    <div class="flex items-baseline gap-2">
                        <p class="text-3xl font-black text-slate-900 dark:text-white tracking-tight tabular-nums"
                           x-text="$store.dashboard.filtered.census.total_consultations.toLocaleString()">
                            {{ number_format($census['total_consultations']) }}
                        </p>
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-extrabold"
                              :class="$store.dashboard.filtered.census.trend_percentage > 0 ? 'bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 border border-emerald-500/20' : ($store.dashboard.filtered.census.trend_percentage < 0 ? 'bg-rose-50 dark:bg-rose-950/60 text-rose-700 dark:text-rose-300 border border-rose-500/20' : 'bg-slate-100 text-slate-600')">
                            <template x-if="$store.dashboard.filtered.census.trend_percentage > 0">
                                <span class="flex items-center"><svg class="w-3 h-3 mr-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>+<span x-text="$store.dashboard.filtered.census.trend_percentage"></span>%</span>
                            </template>
                            <template x-if="$store.dashboard.filtered.census.trend_percentage < 0">
                                <span class="flex items-center"><svg class="w-3 h-3 mr-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"></path></svg><span x-text="$store.dashboard.filtered.census.trend_percentage"></span>%</span>
                            </template>
                            <template x-if="$store.dashboard.filtered.census.trend_percentage === 0">
                                <span>0%</span>
                            </template>
                        </span>
                    </div>
                    <div class="mt-3 pt-3 border-t border-slate-100 dark:border-slate-700/60 flex items-center justify-between text-[11px] text-slate-500 dark:text-slate-400 font-medium">
                        <span>Encounters completed</span>
                        <span>vs previous period</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ═══════════════════════════════════════════════════════════════════════════
         ROW 2: PATIENT CLASSIFICATION SUMMARY (Evaluator Core Priority)
         Pediatric (0-12), Adult (13-59), Senior (60+), and PWD condition overlay
         ═══════════════════════════════════════════════════════════════════════════ -->
    <div class="mb-8">
        <div class="flex items-center justify-between mb-3 px-1">
            <div>
                <h2 class="text-xs font-black uppercase tracking-wider text-slate-600 dark:text-slate-300 flex items-center gap-2">
                    <span>Patient Classification &amp; Demographics Breakdown</span>
                    <span class="text-[10px] px-2 py-0.5 rounded-full bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 font-bold lowercase">all-time registered vs period served</span>
                </h2>
            </div>
            <a href="{{ route('admin.analytics') }}" class="text-xs font-bold text-emerald-600 dark:text-emerald-400 hover:underline">
                View detailed matrix &rarr;
            </a>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 xl:gap-5">
            <!-- 1. Pediatric (0-12 yrs) -->
            <div class="bg-white dark:bg-slate-800 rounded-3xl p-5 border border-slate-200/90 dark:border-slate-700/80 shadow-xs hover:border-sky-500/40 transition-all flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <span class="px-2.5 py-1 rounded-xl text-[10px] font-extrabold uppercase tracking-wider bg-sky-50 dark:bg-sky-950/60 text-sky-700 dark:text-sky-300 border border-sky-500/20">
                            Pediatric (0–12 yrs)
                        </span>
                        <div class="w-8 h-8 rounded-xl bg-sky-100 dark:bg-sky-900/40 text-sky-600 dark:text-sky-300 flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                    </div>

                    <div class="mt-3">
                        <div class="text-[10px] uppercase font-bold text-slate-400 tracking-wider">Registered in System</div>
                        <div class="flex items-baseline gap-2 mt-0.5">
                            <span class="text-2xl font-black text-slate-900 dark:text-white tabular-nums"
                                  x-text="$store.dashboard.filtered.classification.categories.pediatric.registered_count.toLocaleString()">
                                {{ number_format($classification['categories']['pediatric']['registered_count']) }}
                            </span>
                            <span class="text-xs font-bold text-slate-500 dark:text-slate-400">
                                (<span x-text="$store.dashboard.filtered.classification.categories.pediatric.registered_pct">{{ $classification['categories']['pediatric']['registered_pct'] }}</span>%)
                            </span>
                        </div>
                    </div>
                </div>

                <div class="mt-4 pt-3 border-t border-slate-100 dark:border-slate-700/60">
                    <div class="flex items-center justify-between text-xs">
                        <span class="text-[11px] font-medium text-slate-500 dark:text-slate-400">Served this period:</span>
                        <span class="font-black text-sky-600 dark:text-sky-400 tabular-nums">
                            <span x-text="$store.dashboard.filtered.classification.categories.pediatric.served_count.toLocaleString()">{{ number_format($classification['categories']['pediatric']['served_count']) }}</span>
                            <span class="text-[10px] font-semibold text-slate-400">(<span x-text="$store.dashboard.filtered.classification.categories.pediatric.served_pct">{{ $classification['categories']['pediatric']['served_pct'] }}</span>%)</span>
                        </span>
                    </div>
                    <div class="mt-1 text-[10px] text-slate-400 dark:text-slate-500 font-medium">Clinic: Pedia &amp; Well-Child</div>
                </div>
            </div>

            <!-- 2. Regular Adult (13-59 yrs) -->
            <div class="bg-white dark:bg-slate-800 rounded-3xl p-5 border border-slate-200/90 dark:border-slate-700/80 shadow-xs hover:border-indigo-500/40 transition-all flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <span class="px-2.5 py-1 rounded-xl text-[10px] font-extrabold uppercase tracking-wider bg-indigo-50 dark:bg-indigo-950/60 text-indigo-700 dark:text-indigo-300 border border-indigo-500/20">
                            Regular Adult (13–59 yrs)
                        </span>
                        <div class="w-8 h-8 rounded-xl bg-indigo-100 dark:bg-indigo-900/40 text-indigo-600 dark:text-indigo-300 flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        </div>
                    </div>

                    <div class="mt-3">
                        <div class="text-[10px] uppercase font-bold text-slate-400 tracking-wider">Registered in System</div>
                        <div class="flex items-baseline gap-2 mt-0.5">
                            <span class="text-2xl font-black text-slate-900 dark:text-white tabular-nums"
                                  x-text="$store.dashboard.filtered.classification.categories.regular_adult.registered_count.toLocaleString()">
                                {{ number_format($classification['categories']['regular_adult']['registered_count']) }}
                            </span>
                            <span class="text-xs font-bold text-slate-500 dark:text-slate-400">
                                (<span x-text="$store.dashboard.filtered.classification.categories.regular_adult.registered_pct">{{ $classification['categories']['regular_adult']['registered_pct'] }}</span>%)
                            </span>
                        </div>
                    </div>
                </div>

                <div class="mt-4 pt-3 border-t border-slate-100 dark:border-slate-700/60">
                    <div class="flex items-center justify-between text-xs">
                        <span class="text-[11px] font-medium text-slate-500 dark:text-slate-400">Served this period:</span>
                        <span class="font-black text-indigo-600 dark:text-indigo-400 tabular-nums">
                            <span x-text="$store.dashboard.filtered.classification.categories.regular_adult.served_count.toLocaleString()">{{ number_format($classification['categories']['regular_adult']['served_count']) }}</span>
                            <span class="text-[10px] font-semibold text-slate-400">(<span x-text="$store.dashboard.filtered.classification.categories.regular_adult.served_pct">{{ $classification['categories']['regular_adult']['served_pct'] }}</span>%)</span>
                        </span>
                    </div>
                    <div class="mt-1 text-[10px] text-slate-400 dark:text-slate-500 font-medium">Clinic: General OPD &amp; Adult</div>
                </div>
            </div>

            <!-- 3. Senior Citizen (60+ yrs) -->
            <div class="bg-white dark:bg-slate-800 rounded-3xl p-5 border border-slate-200/90 dark:border-slate-700/80 shadow-xs hover:border-emerald-500/40 transition-all flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <span class="px-2.5 py-1 rounded-xl text-[10px] font-extrabold uppercase tracking-wider bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 border border-emerald-500/20">
                            Senior Citizen (60+ yrs)
                        </span>
                        <div class="w-8 h-8 rounded-xl bg-emerald-100 dark:bg-emerald-900/40 text-emerald-600 dark:text-emerald-300 flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                        </div>
                    </div>

                    <div class="mt-3">
                        <div class="text-[10px] uppercase font-bold text-slate-400 tracking-wider">Registered in System</div>
                        <div class="flex items-baseline gap-2 mt-0.5">
                            <span class="text-2xl font-black text-slate-900 dark:text-white tabular-nums"
                                  x-text="$store.dashboard.filtered.classification.categories.senior_citizen.registered_count.toLocaleString()">
                                {{ number_format($classification['categories']['senior_citizen']['registered_count']) }}
                            </span>
                            <span class="text-xs font-bold text-slate-500 dark:text-slate-400">
                                (<span x-text="$store.dashboard.filtered.classification.categories.senior_citizen.registered_pct">{{ $classification['categories']['senior_citizen']['registered_pct'] }}</span>%)
                            </span>
                        </div>
                    </div>
                </div>

                <div class="mt-4 pt-3 border-t border-slate-100 dark:border-slate-700/60">
                    <div class="flex items-center justify-between text-xs">
                        <span class="text-[11px] font-medium text-slate-500 dark:text-slate-400">Served this period:</span>
                        <span class="font-black text-emerald-600 dark:text-emerald-400 tabular-nums">
                            <span x-text="$store.dashboard.filtered.classification.categories.senior_citizen.served_count.toLocaleString()">{{ number_format($classification['categories']['senior_citizen']['served_count']) }}</span>
                            <span class="text-[10px] font-semibold text-slate-400">(<span x-text="$store.dashboard.filtered.classification.categories.senior_citizen.served_pct">{{ $classification['categories']['senior_citizen']['served_pct'] }}</span>%)</span>
                        </span>
                    </div>
                    <div class="mt-1 text-[10px] text-slate-400 dark:text-slate-500 font-medium">Priority Fast-Track Lane</div>
                </div>
            </div>

            <!-- 4. PWD (Independent Condition Overlay) -->
            <div class="bg-gradient-to-br from-amber-500/10 via-white to-amber-500/5 dark:from-amber-950/40 dark:via-slate-800 dark:to-slate-800 rounded-3xl p-5 border border-amber-500/30 shadow-xs hover:border-amber-500/60 transition-all flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <span class="px-2.5 py-1 rounded-xl text-[10px] font-extrabold uppercase tracking-wider bg-amber-100 dark:bg-amber-900/60 text-amber-800 dark:text-amber-200 border border-amber-500/30">
                            PWD (Disability Overlay)
                        </span>
                        <div class="w-8 h-8 rounded-xl bg-amber-100 dark:bg-amber-900/50 text-amber-700 dark:text-amber-300 flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/></svg>
                        </div>
                    </div>

                    <div class="mt-3">
                        <div class="text-[10px] uppercase font-bold text-amber-600 dark:text-amber-400 tracking-wider">Independent Condition</div>
                        <div class="flex items-baseline gap-2 mt-0.5">
                            <span class="text-2xl font-black text-slate-900 dark:text-white tabular-nums"
                                  x-text="$store.dashboard.filtered.classification.pwd.registered_count.toLocaleString()">
                                {{ number_format($classification['pwd']['registered_count']) }}
                            </span>
                            <span class="text-xs font-bold text-amber-600 dark:text-amber-400">
                                (<span x-text="$store.dashboard.filtered.classification.pwd.registered_percentage">{{ $classification['pwd']['registered_percentage'] }}</span>%)
                            </span>
                        </div>
                    </div>
                </div>

                <div class="mt-4 pt-3 border-t border-amber-200/50 dark:border-amber-900/40">
                    <div class="flex items-center justify-between text-xs">
                        <span class="text-[11px] font-medium text-slate-500 dark:text-slate-400">Served this period:</span>
                        <span class="font-black text-amber-700 dark:text-amber-300 tabular-nums">
                            <span x-text="$store.dashboard.filtered.classification.pwd.served_count.toLocaleString()">{{ number_format($classification['pwd']['served_count']) }}</span>
                            <span class="text-[10px] font-semibold text-slate-400">(<span x-text="$store.dashboard.filtered.classification.pwd.served_percentage">{{ $classification['pwd']['served_percentage'] }}</span>%)</span>
                        </span>
                    </div>
                    <div class="mt-1 text-[10px] text-amber-600/80 dark:text-amber-400/80 font-medium">Patients can be Senior + PWD</div>
                </div>
            </div>
        </div>
    </div>

    <!-- ═══════════════════════════════════════════════════════════════════════════
         ROW 3: ACTION NEEDED ALERT RIBBON (Immediate Administrative Queue)
         ═══════════════════════════════════════════════════════════════════════════ -->
    <div class="mb-8 bg-slate-900 text-white rounded-3xl p-5 border border-slate-800 shadow-xl">
        <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-3 pb-3 mb-3 border-b border-white/10">
            <div class="flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-rose-500 animate-ping"></span>
                <h3 class="text-xs font-black uppercase tracking-wider text-slate-200">
                    Action Needed &bull; Immediate Operational Alerts
                </h3>
            </div>
            <span class="text-[10px] text-slate-400 font-mono">
                Real-time facility triggers requiring administrative decision
            </span>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
            <!-- 1. Pending Announcements -->
            <a href="{{ route('admin.announcements.index') }}" 
               class="p-3.5 rounded-2xl bg-white/5 hover:bg-white/10 border border-white/10 transition-all flex flex-col justify-between group">
                <div class="flex items-center justify-between">
                    <span class="text-[10px] uppercase font-bold text-slate-400">Announcements</span>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold {{ $pendingAnnouncements > 0 ? 'bg-amber-500/20 text-amber-300 border border-amber-500/30' : 'bg-emerald-500/20 text-emerald-300' }}">
                        {{ $pendingAnnouncements }} pending
                    </span>
                </div>
                <div class="mt-2 flex items-center justify-between">
                    <span class="text-xs font-bold text-white group-hover:text-emerald-300 transition-colors">
                        {{ $pendingAnnouncements > 0 ? 'Review & Publish' : 'All Published' }}
                    </span>
                    <svg class="w-3.5 h-3.5 text-slate-400 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </div>
            </a>

            <!-- 2. Pharmacy Low/Out of Stock -->
            <a href="{{ route('pharmacy.medicines') }}" 
               class="p-3.5 rounded-2xl bg-white/5 hover:bg-white/10 border border-white/10 transition-all flex flex-col justify-between group">
                <div class="flex items-center justify-between">
                    <span class="text-[10px] uppercase font-bold text-slate-400">Pharmacy Stocks</span>
                    @php $stockAlerts = $inventoryAlerts['out_of_stock'] + $inventoryAlerts['low_stock']; @endphp
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold {{ $stockAlerts > 0 ? 'bg-rose-500/20 text-rose-300 border border-rose-500/30' : 'bg-emerald-500/20 text-emerald-300' }}">
                        {{ $stockAlerts }} alerts
                    </span>
                </div>
                <div class="mt-2 flex items-center justify-between">
                    <span class="text-xs font-bold text-white group-hover:text-emerald-300 transition-colors">
                        {{ $inventoryAlerts['out_of_stock'] }} out / {{ $inventoryAlerts['low_stock'] }} low
                    </span>
                    <svg class="w-3.5 h-3.5 text-slate-400 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </div>
            </a>

            <!-- 3. Expiring Batches -->
            <a href="{{ route('pharmacy.dashboard') }}" 
               class="p-3.5 rounded-2xl bg-white/5 hover:bg-white/10 border border-white/10 transition-all flex flex-col justify-between group">
                <div class="flex items-center justify-between">
                    <span class="text-[10px] uppercase font-bold text-slate-400">Medicine Expiry</span>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold {{ $inventoryAlerts['expiring_soon'] > 0 ? 'bg-amber-500/20 text-amber-300 border border-amber-500/30' : 'bg-emerald-500/20 text-emerald-300' }}">
                        {{ $inventoryAlerts['expiring_soon'] }} batches
                    </span>
                </div>
                <div class="mt-2 flex items-center justify-between">
                    <span class="text-xs font-bold text-white group-hover:text-emerald-300 transition-colors">
                        Expiring within 30d
                    </span>
                    <svg class="w-3.5 h-3.5 text-slate-400 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </div>
            </a>

            <!-- 4. Overdue Follow-ups -->
            <a href="{{ route('frontdesk.followups.index') }}" 
               class="p-3.5 rounded-2xl bg-white/5 hover:bg-white/10 border border-white/10 transition-all flex flex-col justify-between group">
                <div class="flex items-center justify-between">
                    <span class="text-[10px] uppercase font-bold text-slate-400">Follow-up Care</span>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold {{ $followupStats['overdue'] > 0 ? 'bg-rose-500/20 text-rose-300 border border-rose-500/30' : 'bg-emerald-500/20 text-emerald-300' }}">
                        {{ $followupStats['overdue'] }} overdue
                    </span>
                </div>
                <div class="mt-2 flex items-center justify-between">
                    <span class="text-xs font-bold text-white group-hover:text-emerald-300 transition-colors">
                        {{ $followupStats['due_today'] }} due today
                    </span>
                    <svg class="w-3.5 h-3.5 text-slate-400 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </div>
            </a>

            <!-- 5. Data Retention Purge -->
            <a href="{{ route('admin.retention.index') }}" 
               class="p-3.5 rounded-2xl bg-white/5 hover:bg-white/10 border border-white/10 transition-all flex flex-col justify-between group">
                <div class="flex items-center justify-between">
                    <span class="text-[10px] uppercase font-bold text-slate-400">Data Retention</span>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold {{ $pendingDeletionCount > 0 ? 'bg-rose-500/20 text-rose-300 border border-rose-500/30' : 'bg-emerald-500/20 text-emerald-300' }}">
                        {{ $pendingDeletionCount }} expired
                    </span>
                </div>
                <div class="mt-2 flex items-center justify-between">
                    <span class="text-xs font-bold text-white group-hover:text-emerald-300 transition-colors">
                        {{ $pendingDeletionCount > 0 ? '10-Year Purge Req.' : 'Fully Compliant' }}
                    </span>
                    <svg class="w-3.5 h-3.5 text-slate-400 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </div>
            </a>
        </div>
    </div>

    <!-- ═══════════════════════════════════════════════════════════════════════════
         ROW 4: FACILITY TRAFFIC & DAILY INFLUX SNAPSHOT (Brought Back!)
         Daily Consultation Volume & Hourly Peak Operating Windows
         ═══════════════════════════════════════════════════════════════════════════ -->
    <div class="mb-8">
        <div class="flex items-center justify-between mb-3 px-1">
            <div class="flex items-center gap-2">
                <h2 class="text-xs font-black uppercase tracking-wider text-slate-600 dark:text-slate-300 flex items-center gap-2">
                    <span>Facility Influx &amp; Daily Traffic Pulse</span>
                    <span class="text-[10px] px-2 py-0.5 rounded-full bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 font-bold lowercase">operational snapshot</span>
                </h2>
            </div>
            <a href="{{ route('admin.analytics') }}" class="text-xs font-bold text-emerald-600 dark:text-emerald-400 hover:underline flex items-center gap-1">
                <span>Detailed Longitudinal Analytics</span>
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </a>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- 1. Daily Volume Line Chart -->
            <div class="bg-white dark:bg-slate-800 rounded-3xl p-6 border border-slate-200/90 dark:border-slate-700/80 shadow-xs flex flex-col">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h3 class="text-sm font-black text-slate-900 dark:text-white uppercase tracking-wider flex items-center gap-1.5">
                            Daily Consultation Volume
                            <svg class="w-3.5 h-3.5 text-slate-400 cursor-help" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Patient consultation trend over time</p>
                    </div>
                    <span class="px-2.5 py-1 rounded-xl text-[10px] font-extrabold uppercase bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 border border-emerald-500/20" x-text="$store.dashboard.filterLabel">
                        {{ ucfirst($timeFilter) }}
                    </span>
                </div>
                <div class="relative h-64 w-full">
                    <canvas id="visitVolumeChart"></canvas>
                </div>
            </div>

            <!-- 2. Peak Operating Hours Bar Chart -->
            <div class="bg-white dark:bg-slate-800 rounded-3xl p-6 border border-slate-200/90 dark:border-slate-700/80 shadow-xs flex flex-col">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h3 class="text-sm font-black text-slate-900 dark:text-white uppercase tracking-wider flex items-center gap-1.5">
                            Peak Operating Hours
                            <svg class="w-3.5 h-3.5 text-slate-400 cursor-help" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Patient check-in density across clinic hours</p>
                    </div>
                    <span class="px-2.5 py-1 rounded-xl text-[10px] font-extrabold uppercase bg-purple-50 dark:bg-purple-950/60 text-purple-700 dark:text-purple-300 border border-purple-500/20" x-text="$store.dashboard.filterLabel">
                        {{ ucfirst($timeFilter) }}
                    </span>
                </div>
                <div class="relative h-64 w-full">
                    <canvas id="peakHoursChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- ═══════════════════════════════════════════════════════════════════════════
         ROW 5: SYSTEM MODULE SUMMARIES (Staff, Inventory, Archive, Security Audit)
         ═══════════════════════════════════════════════════════════════════════════ -->
    <div class="mb-8">
        <div class="flex items-center justify-between mb-3 px-1">
            <h2 class="text-xs font-black uppercase tracking-wider text-slate-600 dark:text-slate-300">
                Core Module Telemetry &amp; System Health
            </h2>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 xl:gap-5">
            <!-- 1. Staff Summary -->
            <div class="bg-white dark:bg-slate-800 rounded-3xl p-6 border border-slate-200/90 dark:border-slate-700/80 shadow-xs flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-700/60 mb-4">
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 rounded-xl bg-blue-50 dark:bg-blue-950/50 text-blue-600 dark:text-blue-400 flex items-center justify-center">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                            </div>
                            <div>
                                <h3 class="text-xs font-black uppercase tracking-wider text-slate-900 dark:text-white">Staff on Duty</h3>
                                <p class="text-[10px] text-slate-400">Clinical &amp; Administrative</p>
                            </div>
                        </div>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-blue-100 dark:bg-blue-900/50 text-blue-700 dark:text-blue-300">
                            {{ $staffSummary['on_duty'] }} Active
                        </span>
                    </div>

                    <div class="space-y-2.5 text-xs">
                        <div class="flex items-center justify-between">
                            <span class="text-slate-500 dark:text-slate-400">Doctors Present:</span>
                            <span class="font-bold text-slate-900 dark:text-white tabular-nums">
                                {{ $staffSummary['doctors_present'] }} / {{ $staffSummary['total_doctors'] }}
                            </span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-slate-500 dark:text-slate-400">Nurses Present:</span>
                            <span class="font-bold text-slate-900 dark:text-white tabular-nums">
                                {{ $staffSummary['nurses_present'] }} / {{ $staffSummary['total_nurses'] }}
                            </span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-slate-500 dark:text-slate-400">Avg Queue Wait Time:</span>
                            <span class="font-bold text-amber-600 dark:text-amber-400 tabular-nums">
                                <span x-text="`${$store.dashboard.filtered.avgWaitTime}m`">{{ $avgWaitTime }}m</span>
                            </span>
                        </div>
                    </div>
                </div>

                <div class="mt-4 pt-3 border-t border-slate-100 dark:border-slate-700/60 flex items-center justify-between">
                    <a href="{{ route('admin.staff.index') }}" class="text-xs font-bold text-blue-600 dark:text-blue-400 hover:underline">
                        Manage Accounts
                    </a>
                    <a href="{{ route('admin.analytics') }}#staff-performance" class="text-[11px] font-semibold text-slate-400 hover:text-slate-600 dark:hover:text-slate-300">
                        Workload Telemetry &rarr;
                    </a>
                </div>
            </div>

            <!-- 2. Pharmacy Inventory Summary -->
            <div class="bg-white dark:bg-slate-800 rounded-3xl p-6 border border-slate-200/90 dark:border-slate-700/80 shadow-xs flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-700/60 mb-4">
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 rounded-xl bg-emerald-50 dark:bg-emerald-950/50 text-emerald-600 dark:text-emerald-400 flex items-center justify-center">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/></svg>
                            </div>
                            <div>
                                <h3 class="text-xs font-black uppercase tracking-wider text-slate-900 dark:text-white">Pharmacy Stock</h3>
                                <p class="text-[10px] text-slate-400">Formulary &amp; Orders</p>
                            </div>
                        </div>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-black {{ ($inventoryAlerts['out_of_stock'] + $inventoryAlerts['low_stock']) > 0 ? 'bg-rose-100 dark:bg-rose-900/50 text-rose-700 dark:text-rose-300' : 'bg-emerald-100 dark:bg-emerald-900/50 text-emerald-700 dark:text-emerald-300' }}">
                            {{ $inventoryAlerts['total_medicines'] }} Meds
                        </span>
                    </div>

                    <div class="space-y-2.5 text-xs">
                        <div class="flex items-center justify-between">
                            <span class="text-slate-500 dark:text-slate-400">Stockouts / Low:</span>
                            <span class="font-bold text-rose-600 dark:text-rose-400 tabular-nums">
                                {{ $inventoryAlerts['out_of_stock'] }} out &bull; {{ $inventoryAlerts['low_stock'] }} low
                            </span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-slate-500 dark:text-slate-400">Expiring (&le;30d):</span>
                            <span class="font-bold text-amber-600 dark:text-amber-400 tabular-nums">
                                {{ $inventoryAlerts['expiring_soon'] }} batches
                            </span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-slate-500 dark:text-slate-400">Pending Prescriptions:</span>
                            <span class="font-bold text-slate-900 dark:text-white tabular-nums">
                                {{ $inventoryAlerts['pending_prescriptions'] }} orders
                            </span>
                        </div>
                    </div>
                </div>

                <div class="mt-4 pt-3 border-t border-slate-100 dark:border-slate-700/60">
                    <a href="{{ route('pharmacy.medicines') }}" class="text-xs font-bold text-emerald-600 dark:text-emerald-400 hover:underline flex items-center justify-between">
                        <span>Open Pharmacy Portal</span>
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </a>
                </div>
            </div>

            <!-- 3. System Archive Summary -->
            <div class="bg-white dark:bg-slate-800 rounded-3xl p-6 border border-slate-200/90 dark:border-slate-700/80 shadow-xs flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-700/60 mb-4">
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 rounded-xl bg-purple-50 dark:bg-purple-950/50 text-purple-600 dark:text-purple-400 flex items-center justify-center">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>
                            </div>
                            <div>
                                <h3 class="text-xs font-black uppercase tracking-wider text-slate-900 dark:text-white">System Archive</h3>
                                <p class="text-[10px] text-slate-400">Historical &amp; Trashed</p>
                            </div>
                        </div>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-purple-100 dark:bg-purple-900/50 text-purple-700 dark:text-purple-300">
                            {{ $totalArchives }} Archived
                        </span>
                    </div>

                    <div class="space-y-2.5 text-xs">
                        <div class="flex items-center justify-between">
                            <span class="text-slate-500 dark:text-slate-400">Diagnostic Tests:</span>
                            <span class="font-bold text-slate-900 dark:text-white tabular-nums">
                                {{ $archiveCategories['ancillary']['count'] ?? 0 }} records
                            </span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-slate-500 dark:text-slate-400">Archived Bookings:</span>
                            <span class="font-bold text-slate-900 dark:text-white tabular-nums">
                                {{ $archiveCategories['appointments']['count'] ?? 0 }} appointments
                            </span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-slate-500 dark:text-slate-400">Archived Prescriptions:</span>
                            <span class="font-bold text-slate-900 dark:text-white tabular-nums">
                                {{ $archiveCategories['prescriptions']['count'] ?? 0 }} orders
                            </span>
                        </div>
                    </div>
                </div>

                <div class="mt-4 pt-3 border-t border-slate-100 dark:border-slate-700/60">
                    <a href="{{ route('admin.archive.index') }}" class="text-xs font-bold text-purple-600 dark:text-purple-400 hover:underline flex items-center justify-between">
                        <span>Browse Archive Vault</span>
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </a>
                </div>
            </div>

            <!-- 4. Security Audit Telemetry -->
            <div class="bg-white dark:bg-slate-800 rounded-3xl p-6 border border-slate-200/90 dark:border-slate-700/80 shadow-xs flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-700/60 mb-4">
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 rounded-xl bg-rose-50 dark:bg-rose-950/50 text-rose-600 dark:text-rose-400 flex items-center justify-center">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                            </div>
                            <div>
                                <h3 class="text-xs font-black uppercase tracking-wider text-slate-900 dark:text-white">Security &amp; Audit</h3>
                                <p class="text-[10px] text-slate-400">Access &amp; Audit Trail</p>
                            </div>
                        </div>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-300 tabular-nums"
                              x-text="$store.dashboard.filtered.securityStats.total_events + ' Events'">
                            {{ $securityStats['total_events'] }} Events
                        </span>
                    </div>

                    <div class="space-y-2.5 text-xs">
                        <div class="flex items-center justify-between">
                            <span class="text-slate-500 dark:text-slate-400">Successful Logins:</span>
                            <span class="font-bold text-emerald-600 dark:text-emerald-400 tabular-nums"
                                  x-text="$store.dashboard.filtered.securityStats.logins">
                                {{ $securityStats['logins'] }}
                            </span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-slate-500 dark:text-slate-400">Failed Logins:</span>
                            <span class="font-bold text-rose-600 dark:text-rose-400 tabular-nums"
                                  x-text="$store.dashboard.filtered.securityStats.failed_logins">
                                {{ $securityStats['failed_logins'] }}
                            </span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-slate-500 dark:text-slate-400">Critical Deletions:</span>
                            <span class="font-bold text-amber-600 dark:text-amber-400 tabular-nums"
                                  x-text="$store.dashboard.filtered.securityStats.critical_actions">
                                {{ $securityStats['critical_actions'] }}
                            </span>
                        </div>
                    </div>
                </div>

                <div class="mt-4 pt-3 border-t border-slate-100 dark:border-slate-700/60">
                    <a href="{{ route('admin.audit.index') }}" class="text-xs font-bold text-rose-600 dark:text-rose-400 hover:underline flex items-center justify-between">
                        <span>Review Security Logs</span>
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- ═══════════════════════════════════════════════════════════════════════════
         ROW 6: ACTIVITY FEEDS (Recent Security Audit & Health Bulletins)
         ═══════════════════════════════════════════════════════════════════════════ -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
        <!-- Feed 1: Security Audit Trail -->
        <div class="bg-white dark:bg-slate-800 rounded-3xl p-6 border border-slate-200/90 dark:border-slate-700/80 shadow-xs flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-700/60 mb-4">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-xl bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-300 flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <div>
                            <h3 class="text-xs font-black uppercase tracking-wider text-slate-900 dark:text-white">Live Audit Log Stream</h3>
                            <p class="text-[10px] text-slate-400">Recent System Encounters</p>
                        </div>
                    </div>
                    <a href="{{ route('admin.audit.index') }}" class="text-[11px] font-bold text-emerald-600 dark:text-emerald-400 hover:underline">
                        View All
                    </a>
                </div>

                <div class="space-y-3">
                    @forelse($recentLogs as $log)
                        <div class="flex items-start gap-3 p-2.5 rounded-2xl hover:bg-slate-50 dark:hover:bg-slate-700/40 transition-colors border border-transparent hover:border-slate-200/60 dark:hover:border-slate-700/60">
                            <span class="w-2 h-2 rounded-full mt-1.5 shrink-0 {{ str_starts_with($log->action, 'delete') ? 'bg-rose-500' : (str_starts_with($log->action, 'update') ? 'bg-amber-500' : 'bg-emerald-500') }}"></span>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between text-xs">
                                    <span class="font-bold text-slate-800 dark:text-white truncate">
                                        {{ $log->user ? $log->user->name : 'System Automated' }}
                                    </span>
                                    <span class="text-[10px] text-slate-400 font-mono shrink-0 ml-2">
                                        {{ $log->created_at->diffForHumans() }}
                                    </span>
                                </div>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400 capitalize mt-0.5">
                                    {{ str_replace('_', ' ', $log->action) }} &bull; {{ class_basename($log->model_type ?? 'Audit') }}
                                </p>
                            </div>
                        </div>
                    @empty
                        <div class="py-8 text-center text-slate-400">
                            <p class="text-xs">No audit events logged yet.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Feed 2: Health Announcements -->
        <div class="bg-white dark:bg-slate-800 rounded-3xl p-6 border border-slate-200/90 dark:border-slate-700/80 shadow-xs flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-700/60 mb-4">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-xl bg-teal-50 dark:bg-teal-950/50 text-teal-600 dark:text-teal-400 flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/></svg>
                        </div>
                        <div>
                            <h3 class="text-xs font-black uppercase tracking-wider text-slate-900 dark:text-white">Public Bulletins &amp; Advisories</h3>
                            <p class="text-[10px] text-slate-400">Community Outreach</p>
                        </div>
                    </div>
                    <a href="{{ route('admin.announcements.index') }}" class="text-[11px] font-bold text-teal-600 dark:text-teal-400 hover:underline">
                        Manage
                    </a>
                </div>

                <div class="space-y-3">
                    @forelse($announcements as $item)
                        <div class="p-3 rounded-2xl bg-slate-50 dark:bg-slate-700/40 border border-slate-100 dark:border-slate-700/60 hover:border-teal-500/30 transition-all">
                            <div class="flex items-center justify-between text-xs">
                                <span class="font-bold text-slate-900 dark:text-white truncate max-w-[260px]">
                                    {{ $item->title }}
                                </span>
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $item->status === 'published' ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-300' : 'bg-amber-100 text-amber-700 dark:bg-amber-950/60 dark:text-amber-300' }}">
                                    {{ ucfirst($item->status) }}
                                </span>
                            </div>
                            <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1 line-clamp-1">
                                {{ Str::limit(strip_tags($item->content), 90) }}
                            </p>
                        </div>
                    @empty
                        <div class="py-8 text-center text-slate-400">
                            <p class="text-xs">No active health advisories.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Alpine Store for Real-Time Period Switching & Live Chart Updating -->
<script>
    let volumeChartInstance = null;
    let peakChartInstance = null;

    document.addEventListener('alpine:init', () => {
        Alpine.store('dashboard', {
            timeFilter: @json($timeFilter),
            startDate: @json($startDate?->format('Y-m-d') ?? now()->startOfMonth()->format('Y-m-d')),
            endDate: @json($endDate?->format('Y-m-d') ?? now()->format('Y-m-d')),
            dateRangeLabel: @json($dateRangeLabel),
            loading: false,
            filtered: {
                census: @json($census),
                classification: @json($classification),
                securityStats: @json($securityStats),
                avgWaitTime: {{ $avgWaitTime }},
                currentPeriodConsultations: '{{ number_format($currentPeriodConsultations) }}',
                trendPercentage: {{ $trendPercentage }},
                visitVolumeData: @json($visitVolumeData),
                peakHoursData: @json($peakHoursData),
                staffSummary: @json($staffSummary),
                inventoryAlerts: @json($inventoryAlerts),
                followupStats: @json($followupStats),
                pendingAnnouncements: {{ $pendingAnnouncements }},
                pendingDeletionCount: {{ $pendingDeletionCount }},
                totalArchives: {{ $totalArchives }},
                archiveCategories: @json($archiveCategories),
            },
            get filterLabel() {
                const labels = {
                    today: 'Today',
                    weekly: 'This Week',
                    monthly: 'This Month',
                    yearly: 'This Year',
                    all: 'All Time',
                    custom: 'Custom Range'
                };
                return labels[this.timeFilter] || this.timeFilter;
            },
            async refresh() {
                this.loading = true;
                try {
                    let url = window.__dashboardStatsUrl + '?time_filter=' + encodeURIComponent(this.timeFilter);
                    if (this.timeFilter === 'custom' && this.startDate && this.endDate) {
                        url += '&start_date=' + encodeURIComponent(this.startDate) + '&end_date=' + encodeURIComponent(this.endDate);
                    }
                    const res = await fetch(url);
                    if (res.ok) {
                        const data = await res.json();
                        this.filtered.census = data.census;
                        this.filtered.classification = data.classification;
                        this.filtered.securityStats = data.securityStats;
                        this.filtered.avgWaitTime = data.avgWaitTime;
                        this.filtered.currentPeriodConsultations = data.currentPeriodConsultations;
                        this.filtered.trendPercentage = data.trendPercentage;
                        if (data.dateRangeLabel) {
                            this.dateRangeLabel = data.dateRangeLabel;
                        }
                        if (data.visitVolumeData) {
                            this.filtered.visitVolumeData = data.visitVolumeData;
                        }
                        if (data.peakHoursData) {
                            this.filtered.peakHoursData = data.peakHoursData;
                        }

                        // Dynamically update charts on Dashboard
                        if (data.visitVolumeData && volumeChartInstance) {
                            volumeChartInstance.data.labels = data.visitVolumeData.labels;
                            volumeChartInstance.data.datasets[0].data = data.visitVolumeData.data;
                            volumeChartInstance.data.datasets[0].pointRadius = (data.visitVolumeData.data?.length || 0) > 15 ? 0 : 3.5;
                            volumeChartInstance.update();
                        } else if (window.__refreshVolumeChart) {
                            await window.__refreshVolumeChart(this.timeFilter);
                        }

                        if (data.peakHoursData && peakChartInstance) {
                            peakChartInstance.data.labels = data.peakHoursData.labels;
                            peakChartInstance.data.datasets[0].data = data.peakHoursData.data;
                            peakChartInstance.update();
                        } else if (window.__refreshPeakChart) {
                            await window.__refreshPeakChart(this.timeFilter);
                        }
                    }
                } catch (e) {
                    console.error('Error refreshing dashboard:', e);
                } finally {
                    this.loading = false;
                }
            }
        });
    });

    window.__dashboardStatsUrl = @json(route('admin.dashboard.statsData'));

    function initDashboardCharts() {
        if (typeof Chart === 'undefined') {
            setTimeout(initDashboardCharts, 50);
            return;
        }

        // --- Helper for gradient ---
        function makeGradient(ctx, color) {
            const gradient = ctx.createLinearGradient(0, 0, 0, 240);
            gradient.addColorStop(0, color.replace('rgb', 'rgba').replace(')', ', 0.25)'));
            gradient.addColorStop(1, 'rgba(255, 255, 255, 0)');
            return gradient;
        }

        // --- 1. Daily Volume Chart ---
        const volumeCanvas = document.getElementById('visitVolumeChart');
        const initialVolumeData = @json($visitVolumeData);
        if (volumeCanvas && !volumeChartInstance) {
            const volumeCtx = volumeCanvas.getContext('2d');
            const grad = makeGradient(volumeCtx, 'rgb(16, 185, 129)');
            volumeChartInstance = new Chart(volumeCtx, {
                type: 'line',
                data: {
                    labels: initialVolumeData?.labels || [],
                    datasets: [{
                        label: 'Patient Consultations',
                        data: initialVolumeData?.data || [],
                        borderColor: '#10b981',
                        backgroundColor: grad,
                        borderWidth: 2.5,
                        tension: 0.4,
                        fill: true,
                        pointBackgroundColor: '#ffffff',
                        pointBorderColor: '#10b981',
                        pointBorderWidth: 2,
                        pointRadius: (initialVolumeData?.data?.length || 0) > 15 ? 0 : 3.5,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: { precision: 0, font: { size: 10 } },
                            grid: { color: 'rgba(148, 163, 184, 0.1)' }
                        },
                        x: {
                            ticks: { font: { size: 9 }, maxRotation: 45 },
                            grid: { display: false }
                        }
                    }
                }
            });
        }

        // --- 2. Peak Operating Hours Bar Chart ---
        const peakCanvas = document.getElementById('peakHoursChart');
        const initialPeakData = @json($peakHoursData);
        if (peakCanvas && !peakChartInstance) {
            const peakCtx = peakCanvas.getContext('2d');
            const barGrad = peakCtx.createLinearGradient(0, 0, 0, 240);
            barGrad.addColorStop(0, '#8b5cf6');
            barGrad.addColorStop(1, '#6d28d9');
            peakChartInstance = new Chart(peakCtx, {
                type: 'bar',
                data: {
                    labels: initialPeakData?.labels || [],
                    datasets: [{
                        label: 'Check-ins',
                        data: initialPeakData?.data || [],
                        backgroundColor: barGrad,
                        borderRadius: 6,
                        barPercentage: 0.6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: { precision: 0, font: { size: 10 } },
                            grid: { color: 'rgba(148, 163, 184, 0.1)' }
                        },
                        x: {
                            ticks: { font: { size: 9 } },
                            grid: { display: false }
                        }
                    }
                }
            });
        }

        async function refreshVolumeChart(filter) {
            try {
                const res = await fetch('/admin/analytics/chart/volume?time_filter=' + encodeURIComponent(filter));
                if (!res.ok) return;
                const data = await res.json();
                if (volumeChartInstance) {
                    volumeChartInstance.data.labels = data.labels || [];
                    volumeChartInstance.data.datasets[0].data = data.data || [];
                    volumeChartInstance.data.datasets[0].pointRadius = (data.data?.length || 0) > 15 ? 0 : 3.5;
                    volumeChartInstance.update();
                }
            } catch (err) {
                console.error('Failed to update volume chart:', err);
            }
        }
        window.__refreshVolumeChart = refreshVolumeChart;

        async function refreshPeakChart(filter) {
            try {
                const res = await fetch('/admin/analytics/chart/peak?time_filter=' + encodeURIComponent(filter));
                if (!res.ok) return;
                const data = await res.json();
                if (peakChartInstance) {
                    peakChartInstance.data.labels = data.labels || [];
                    peakChartInstance.data.datasets[0].data = data.data || [];
                    peakChartInstance.update();
                }
            } catch (err) {
                console.error('Failed to update peak hours chart:', err);
            }
        }
        window.__refreshPeakChart = refreshPeakChart;
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initDashboardCharts);
    } else {
        initDashboardCharts();
    }
</script>

{{-- ═══════════════════════════════════════════════════════════════════════════
     ISOLATED PRINT REPORT DOSSIER
     ═══════════════════════════════════════════════════════════════════════════ --}}
<script>
    function printDashboardReport() {
        // 1. Rasterize chart canvases if rendered
        function rasterize(canvasId) {
            const canvas = document.getElementById(canvasId);
            if (!canvas || !canvas.offsetWidth) return '';
            try {
                if (window.Chart && Chart.getChart) {
                    const ch = Chart.getChart(canvas);
                    if (ch) { ch.stop(); ch.render(); }
                }
                return canvas.toDataURL('image/png', 1.0);
            } catch(e) { return ''; }
        }

        const volumeImg = rasterize('visitVolumeChart');
        const peakImg = rasterize('peakHoursChart');

        // 2. Retrieve dynamic data from Alpine store with authoritative server fallbacks
        const store = Alpine.store('dashboard');
        const c = store?.filtered?.census || @json($census);
        const cl = store?.filtered?.classification || @json($classification);
        const s = store?.filtered?.securityStats || @json($securityStats);
        const staff = store?.filtered?.staffSummary || @json($staffSummary);
        const inv = store?.filtered?.inventoryAlerts || @json($inventoryAlerts);
        const follow = store?.filtered?.followupStats || @json($followupStats);
        const pendingAnn = store?.filtered?.pendingAnnouncements ?? {{ $pendingAnnouncements }};
        const pendingDel = store?.filtered?.pendingDeletionCount ?? {{ $pendingDeletionCount }};
        const archives = store?.filtered?.archiveCategories || @json($archiveCategories);
        const totalArch = store?.filtered?.totalArchives ?? {{ $totalArchives }};
        const waitTime = store?.filtered?.avgWaitTime ?? {{ $avgWaitTime }};
        const trendPct = store?.filtered?.trendPercentage ?? {{ $trendPercentage }};
        const curConsult = store?.filtered?.currentPeriodConsultations || '{{ number_format($currentPeriodConsultations) }}';
        const tf = store?.filterLabel || '{{ ucfirst($timeFilter) }}';
        const dateLabel = store?.dateRangeLabel || @json($dateRangeLabel);

        const vv = (volumeChartInstance ? { labels: volumeChartInstance.data.labels, data: volumeChartInstance.data.datasets[0].data } : (store?.filtered?.visitVolumeData || @json($visitVolumeData)));
        const ph = (peakChartInstance ? { labels: peakChartInstance.data.labels, data: peakChartInstance.data.datasets[0].data } : (store?.filtered?.peakHoursData || @json($peakHoursData)));

        // 3. Construct Accessible Data Table for Visit Volume
        let vvRows = '';
        const vvLabels = vv?.labels || [];
        const vvData = vv?.data || [];
        const vvTotal = vvData.reduce((acc, v) => acc + (Number(v) || 0), 0);
        const hasVvData = vvTotal > 0 || vvData.some(v => Number(v) > 0);

        if (hasVvData && vvLabels.length > 0) {
            vvLabels.forEach((lbl, idx) => {
                const cnt = Number(vvData[idx]) || 0;
                const pct = vvTotal > 0 ? ((cnt / vvTotal) * 100).toFixed(1) : '0.0';
                const bg = idx % 2 === 1 ? 'background:#f8fafc;' : '';
                vvRows += `
                    <tr style="${bg}">
                        <td style="padding:4px 8px;border:1px solid #e2e8f0;font-weight:600;color:#334155;">${lbl}</td>
                        <td style="padding:4px 8px;border:1px solid #e2e8f0;font-weight:800;text-align:right;color:#0f172a;font-variant-numeric:tabular-nums;">${cnt.toLocaleString()}</td>
                        <td style="padding:4px 8px;border:1px solid #e2e8f0;text-align:right;color:#64748b;font-variant-numeric:tabular-nums;">${pct}%</td>
                    </tr>
                `;
            });
            const avgVv = (vvTotal / (vvLabels.length || 1)).toFixed(1);
            vvRows += `
                <tr style="background:#f1f5f9;font-weight:800;">
                    <td style="padding:4px 8px;border:1px solid #cbd5e1;color:#0f172a;">Period Total / Average</td>
                    <td style="padding:4px 8px;border:1px solid #cbd5e1;text-align:right;color:#0f172a;">${vvTotal.toLocaleString()}</td>
                    <td style="padding:4px 8px;border:1px solid #cbd5e1;text-align:right;color:#0f6b57;">Avg: ${avgVv} / interval</td>
                </tr>
            `;
        } else {
            vvRows = `
                <tr>
                    <td colspan="3" style="padding:10px;text-align:center;color:#64748b;font-style:italic;">
                        No consultation traffic recorded for the selected time frame (${dateLabel}).
                    </td>
                </tr>
            `;
        }

        // 4. Construct Accessible Data Table for Peak Operating Hours
        let phRows = '';
        const phLabels = ph?.labels || [];
        const phData = ph?.data || [];
        const phTotal = phData.reduce((acc, v) => acc + (Number(v) || 0), 0);
        const hasPhData = phTotal > 0 || phData.some(v => Number(v) > 0);

        if (hasPhData && phLabels.length > 0) {
            let peakHourLbl = '';
            let peakHourVal = -1;

            phLabels.forEach((lbl, idx) => {
                const cnt = Number(phData[idx]) || 0;
                if (cnt > peakHourVal) {
                    peakHourVal = cnt;
                    peakHourLbl = lbl;
                }
                const pct = phTotal > 0 ? ((cnt / phTotal) * 100).toFixed(1) : '0.0';
                const density = cnt >= 10 
                    ? '<span style="color:#b91c1c;font-weight:800;">Surge Window</span>' 
                    : (cnt >= 4 
                        ? '<span style="color:#d97706;font-weight:700;">Moderate Load</span>' 
                        : (cnt > 0 ? '<span style="color:#15803d;font-weight:600;">Routine Flow</span>' : '<span style="color:#94a3b8;">Zero Arrivals</span>'));
                const bg = idx % 2 === 1 ? 'background:#f8fafc;' : '';
                phRows += `
                    <tr style="${bg}">
                        <td style="padding:4px 8px;border:1px solid #e2e8f0;font-weight:600;color:#334155;">${lbl}</td>
                        <td style="padding:4px 8px;border:1px solid #e2e8f0;font-weight:800;text-align:right;color:#0f172a;font-variant-numeric:tabular-nums;">${cnt.toLocaleString()}</td>
                        <td style="padding:4px 8px;border:1px solid #e2e8f0;text-align:right;color:#64748b;font-variant-numeric:tabular-nums;">${pct}%</td>
                        <td style="padding:4px 8px;border:1px solid #e2e8f0;text-align:center;">${density}</td>
                    </tr>
                `;
            });
            phRows += `
                <tr style="background:#f1f5f9;font-weight:800;">
                    <td style="padding:4px 8px;border:1px solid #cbd5e1;color:#0f172a;">Total Check-Ins / Peak</td>
                    <td style="padding:4px 8px;border:1px solid #cbd5e1;text-align:right;color:#0f172a;">${phTotal.toLocaleString()}</td>
                    <td colspan="2" style="padding:4px 8px;border:1px solid #cbd5e1;text-align:center;color:#6d28d9;">
                        Peak Arrival Window: ${peakHourLbl} (${peakHourVal} check-ins)
                    </td>
                </tr>
            `;
        } else {
            phRows = `
                <tr>
                    <td colspan="4" style="padding:10px;text-align:center;color:#64748b;font-style:italic;">
                        No arrival records logged for operating hours in the selected time frame (${dateLabel}).
                    </td>
                </tr>
            `;
        }

        // 5. Helper to create chart blocks with visuals, data tables, and descriptive insights
        function makeChartBlock(img, title, subtitle, tableRows, headersHtml, insight) {
            return `
                <div style="border:1px solid #cbd5e1;border-radius:6px;padding:10px 12px;background:#ffffff;page-break-inside:avoid;break-inside:avoid;margin-bottom:12px;box-shadow:0 1px 2px rgba(0,0,0,0.02);">
                    <div style="margin-bottom:6px;border-bottom:1px solid #f1f5f9;padding-bottom:4px;display:flex;justify-content:space-between;align-items:center;">
                        <span style="font-size:8.5pt;font-weight:800;text-transform:uppercase;color:#0f172a;letter-spacing:0.04em;">${title}</span>
                        <span style="font-size:7pt;color:#64748b;font-weight:600;">${subtitle}</span>
                    </div>
                    ${img ? `<div style="text-align:center;margin:6px 0 8px 0;"><img src="${img}" style="max-width:100%;max-height:180px;height:auto;object-fit:contain;display:block;margin:0 auto;border:1px solid #f1f5f9;border-radius:4px;"></div>` : ''}
                    <div style="margin:6px 0;">
                        <div style="font-size:6.8pt;font-weight:800;text-transform:uppercase;color:#475569;margin-bottom:3px;letter-spacing:0.04em;">Supporting Data Table (Exact System Figures):</div>
                        <table style="width:100%;border-collapse:collapse;font-size:7.5pt;border:1px solid #cbd5e1;border-radius:4px;overflow:hidden;">
                            <thead>${headersHtml}</thead>
                            <tbody>${tableRows}</tbody>
                        </table>
                    </div>
                    ${insight ? `<div style="font-size:7pt;color:#334155;background:#f8fafc;border-left:3px solid #0f6b57;padding:5px 8px;margin-top:6px;border-radius:0 4px 4px 0;"><strong style="color:#0f172a;">Analytical Insight:</strong> ${insight}</div>` : ''}
                </div>
            `;
        }

        const vvHeaders = `
            <tr style="background:#f1f5f9;">
                <th style="padding:4px 8px;border:1px solid #cbd5e1;text-align:left;font-size:7pt;font-weight:800;text-transform:uppercase;color:#1e293b;">Time Interval</th>
                <th style="padding:4px 8px;border:1px solid #cbd5e1;text-align:right;font-size:7pt;font-weight:800;text-transform:uppercase;color:#1e293b;">Consultations</th>
                <th style="padding:4px 8px;border:1px solid #cbd5e1;text-align:right;font-size:7pt;font-weight:800;text-transform:uppercase;color:#1e293b;">% Share</th>
            </tr>
        `;

        const phHeaders = `
            <tr style="background:#f1f5f9;">
                <th style="padding:4px 8px;border:1px solid #cbd5e1;text-align:left;font-size:7pt;font-weight:800;text-transform:uppercase;color:#1e293b;">Operating Hour</th>
                <th style="padding:4px 8px;border:1px solid #cbd5e1;text-align:right;font-size:7pt;font-weight:800;text-transform:uppercase;color:#1e293b;">Check-Ins</th>
                <th style="padding:4px 8px;border:1px solid #cbd5e1;text-align:right;font-size:7pt;font-weight:800;text-transform:uppercase;color:#1e293b;">% Share</th>
                <th style="padding:4px 8px;border:1px solid #cbd5e1;text-align:center;font-size:7pt;font-weight:800;text-transform:uppercase;color:#1e293b;">Traffic Acuity</th>
            </tr>
        `;

        @php
            $recentLogRows = '';
            foreach($recentLogs as $log) {
                $userStr = $log->user ? addslashes($log->user->name) : 'System Automated';
                $timeStr = $log->created_at->format('M d, Y h:i A');
                $actStr = ucwords(str_replace('_', ' ', $log->action));
                $modStr = class_basename($log->model_type ?? 'Audit');
                $recentLogRows .= '<tr><td style="padding:4px 6px;border:1px solid #e2e8f0;color:#475569;font-size:7pt;">' . $timeStr . '</td><td style="padding:4px 6px;border:1px solid #e2e8f0;font-weight:700;color:#0f172a;font-size:7pt;">' . $userStr . '</td><td style="padding:4px 6px;border:1px solid #e2e8f0;color:#334155;font-size:7pt;">' . $actStr . '</td><td style="padding:4px 6px;border:1px solid #e2e8f0;color:#64748b;font-size:7pt;">' . $modStr . '</td></tr>';
            }
            if (empty($recentLogRows)) {
                $recentLogRows = '<tr><td colspan="4" style="padding:6px;text-align:center;color:#94a3b8;font-size:7pt;">No recent audit activity logged.</td></tr>';
            }

            $announcementRows = '';
            foreach($announcements as $ann) {
                $tStr = addslashes($ann->title);
                $stStr = ucfirst($ann->status);
                $cStr = addslashes(Str::limit(strip_tags($ann->content), 80));
                $dStr = $ann->created_at->format('M d, Y');
                $announcementRows .= '<tr><td style="padding:4px 6px;border:1px solid #e2e8f0;font-weight:700;color:#0f172a;font-size:7pt;">' . $tStr . '</td><td style="padding:4px 6px;border:1px solid #e2e8f0;text-align:center;font-size:7pt;font-weight:700;color:' . ($ann->status === 'published' ? '#047857' : '#b45309') . ';">' . $stStr . '</td><td style="padding:4px 6px;border:1px solid #e2e8f0;color:#475569;font-size:7pt;">' . $cStr . '</td><td style="padding:4px 6px;border:1px solid #e2e8f0;color:#64748b;font-size:7pt;text-align:right;">' . $dStr . '</td></tr>';
            }
            if (empty($announcementRows)) {
                $announcementRows = '<tr><td colspan="4" style="padding:6px;text-align:center;color:#94a3b8;font-size:7pt;">No public health advisories recorded.</td></tr>';
            }
        @endphp

        // 6. Build Comprehensive HTML Print Dossier
        const reportHTML = `
            <table class="rhu-print-table" style="display:table;width:100%;border-collapse:collapse;font-family:'Inter',system-ui,sans-serif;">
                <thead class="rhu-print-thead" style="display:table-header-group;">
                    <tr><td style="padding:0;border:none;">
                        <header style="width:100%;margin-bottom:12px;">
                            <div style="display:flex;align-items:center;justify-content:space-between;gap:16px;width:100%;padding:2px 0 6px 0;">
                                <div style="width:64px;height:64px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                    <img src="/assets/images/logo.png" alt="Municipal Seal" style="max-width:64px;max-height:64px;object-fit:contain;" onerror="this.style.display='none'">
                                </div>
                                <div style="flex:1;text-align:center;padding:0 4px;">
                                    <div style="font-size:8.5pt;font-weight:700;text-transform:uppercase;letter-spacing:0.08em;color:#475569;">{{ addslashes(\App\Models\SiteSetting::get('topbar_republic', 'Republic of the Philippines')) }}</div>
                                    <div style="font-size:8pt;font-weight:600;letter-spacing:0.04em;color:#64748b;">{{ addslashes(\App\Models\SiteSetting::get('topbar_province', 'Province of Cavite')) }}</div>
                                    <h2 style="font-size:13pt;font-weight:900;text-transform:uppercase;letter-spacing:0.04em;color:#0f172a;margin:3px 0 2px 0;">{{ addslashes(\App\Models\SiteSetting::get('topbar_municipality', 'Municipality of Silang')) }} &mdash; Rural Health Unit</h2>
                                    <div style="font-size:8pt;font-weight:700;letter-spacing:0.06em;text-transform:uppercase;color:#0f6b57;">Rural Health Unit Management Information System (RHU MIS)</div>
                                </div>
                                <div style="width:64px;height:64px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                    <svg viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg" style="max-width:64px;max-height:64px;"><circle cx="32" cy="32" r="30" stroke="#0f6b57" stroke-width="2.5" fill="#f0fdf4"/><circle cx="32" cy="32" r="25" stroke="#0f6b57" stroke-width="1" stroke-dasharray="2 2"/><path d="M32 14v36M22 24h20M24 38h16" stroke="#0f6b57" stroke-width="3" stroke-linecap="round"/><circle cx="32" cy="13" r="3" fill="#0f6b57"/><path d="M26 21c3-2 9-2 12 0M26 29c3-2 9-2 12 0M26 37c3-2 9-2 12 0" stroke="#0f6b57" stroke-width="1.5" stroke-linecap="round"/></svg>
                                </div>
                            </div>
                            <div style="width:100%;height:4px;border-top:1px solid #0f6b57;border-bottom:2px solid #0f6b57;margin:4px 0 10px 0;"></div>
                        </header>
                    </td></tr>
                </thead>

                <tbody style="display:table-row-group;"><tr><td style="padding:0;border:none;">
                    <main>
                        <!-- Title Block & Active Window Metadata -->
                        <div style="margin-bottom:14px;padding-bottom:10px;border-bottom:1px solid #e2e8f0;">
                            <h1 style="font-size:15pt;font-weight:800;color:#0f172a;margin:0 0 2px 0;">RHU Executive Management Dossier &amp; Analytics Report</h1>
                            <p style="font-size:8.5pt;color:#64748b;margin:0 0 10px 0;">Consolidated patient registry census, classification audit, facility traffic dynamics, and operational telemetry.</p>
                            <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:8px 14px;background:#f8fafc;border:1px solid #e2e8f0;border-radius:4px;padding:8px 12px;font-size:8pt;">
                                <div><div style="font-size:6.5pt;font-weight:800;text-transform:uppercase;color:#64748b;">Reporting Period</div><div style="font-weight:700;color:#0f172a;">${dateLabel} &bull; ${tf}</div></div>
                                <div><div style="font-size:6.5pt;font-weight:800;text-transform:uppercase;color:#64748b;">Healthcare Facility</div><div style="font-weight:700;color:#0f172a;">Silang Rural Health Unit</div></div>
                                <div><div style="font-size:6.5pt;font-weight:800;text-transform:uppercase;color:#64748b;">Generated By</div><div style="font-weight:700;color:#0f172a;">{{ auth()->check() ? auth()->user()->name : 'System Generated' }} ({{ ucwords(str_replace('_', ' ', auth()->user()?->role ?? 'Admin')) }})</div></div>
                                <div><div style="font-size:6.5pt;font-weight:800;text-transform:uppercase;color:#64748b;">Generation Timestamp</div><div style="font-weight:700;color:#0f172a;">{{ now()->format('M d, Y h:i A') }}</div></div>
                            </div>
                        </div>

                        <!-- Executive Snapshot Scorecard Ribbon -->
                        <div style="display:grid;grid-template-columns:repeat(5,1fr);gap:8px;margin-bottom:16px;page-break-inside:avoid;break-inside:avoid;">
                            <div style="border:1px solid #cbd5e1;border-radius:6px;padding:8px 10px;background:#f8fafc;">
                                <div style="font-size:6.5pt;font-weight:800;text-transform:uppercase;color:#64748b;">Total Master Registry</div>
                                <div style="font-size:13pt;font-weight:900;color:#0f172a;font-variant-numeric:tabular-nums;margin-top:2px;">${Number(c.total_registered).toLocaleString()}</div>
                                <div style="font-size:6.5pt;color:#64748b;margin-top:2px;">All-time registered</div>
                            </div>
                            <div style="border:1px solid #cbd5e1;border-radius:6px;padding:8px 10px;background:#f8fafc;">
                                <div style="font-size:6.5pt;font-weight:800;text-transform:uppercase;color:#64748b;">New Period Registrations</div>
                                <div style="font-size:13pt;font-weight:900;color:#0284c7;font-variant-numeric:tabular-nums;margin-top:2px;">${Number(c.new_registered).toLocaleString()}</div>
                                <div style="font-size:6.5pt;color:#64748b;margin-top:2px;">New medical records</div>
                            </div>
                            <div style="border:1px solid #cbd5e1;border-radius:6px;padding:8px 10px;background:#f8fafc;">
                                <div style="font-size:6.5pt;font-weight:800;text-transform:uppercase;color:#64748b;">Period Consultations</div>
                                <div style="font-size:13pt;font-weight:900;color:#0f6b57;font-variant-numeric:tabular-nums;margin-top:2px;">${curConsult}</div>
                                <div style="font-size:6.5pt;color:${trendPct >= 0 ? '#059669' : '#dc2626'};font-weight:700;margin-top:2px;">${trendPct > 0 ? '+' : ''}${trendPct}% vs prior period</div>
                            </div>
                            <div style="border:1px solid #cbd5e1;border-radius:6px;padding:8px 10px;background:#f8fafc;">
                                <div style="font-size:6.5pt;font-weight:800;text-transform:uppercase;color:#64748b;">Active Duty Personnel</div>
                                <div style="font-size:13pt;font-weight:900;color:#2563eb;font-variant-numeric:tabular-nums;margin-top:2px;">${staff.doctors_present + staff.nurses_present} Staff</div>
                                <div style="font-size:6.5pt;color:#64748b;margin-top:2px;">${staff.doctors_present} Drs &bull; ${staff.nurses_present} Nurses</div>
                            </div>
                            <div style="border:1px solid #cbd5e1;border-radius:6px;padding:8px 10px;background:#f8fafc;">
                                <div style="font-size:6.5pt;font-weight:800;text-transform:uppercase;color:#64748b;">Avg Queue Wait Time</div>
                                <div style="font-size:13pt;font-weight:900;color:#d97706;font-variant-numeric:tabular-nums;margin-top:2px;">${waitTime} min</div>
                                <div style="font-size:6.5pt;color:#64748b;margin-top:2px;">Triage &rarr; Doctor</div>
                            </div>
                        </div>

                        <!-- SECTION 1: Patient Census & Service Utilization -->
                        <h3 style="font-size:9.5pt;font-weight:800;color:#0f172a;margin:16px 0 6px 0;text-transform:uppercase;letter-spacing:0.04em;">1. Municipal Patient Registry &amp; Service Utilization Census</h3>
                        <table style="width:100%;border-collapse:collapse;margin-bottom:14px;font-size:8pt;border:1px solid #cbd5e1;page-break-inside:avoid;break-inside:avoid;">
                            <thead>
                                <tr style="background:#f1f5f9;">
                                    <th style="padding:5px 8px;border:1px solid #cbd5e1;text-align:left;">Census Indicator</th>
                                    <th style="padding:5px 8px;border:1px solid #cbd5e1;text-align:right;">Actual Count</th>
                                    <th style="padding:5px 8px;border:1px solid #cbd5e1;text-align:left;">Breakdown / Operational Context</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td style="padding:5px 8px;border:1px solid #e2e8f0;font-weight:700;">Total Master Registry Patients</td>
                                    <td style="padding:5px 8px;border:1px solid #e2e8f0;font-weight:800;text-align:right;font-variant-numeric:tabular-nums;">${Number(c.total_registered).toLocaleString()}</td>
                                    <td style="padding:5px 8px;border:1px solid #e2e8f0;color:#475569;">Male: ${Number(c.male_registered).toLocaleString()} (${c.male_percentage}%) &bull; Female: ${Number(c.female_registered).toLocaleString()} (${c.female_percentage}%) &bull; PhilHealth Covered: ${Number(c.philhealth_registered || c.philhealth_count || 0).toLocaleString()} (${c.philhealth_percentage}%)</td>
                                </tr>
                                <tr style="background:#f8fafc;">
                                    <td style="padding:5px 8px;border:1px solid #e2e8f0;font-weight:700;">New Registrations in Period</td>
                                    <td style="padding:5px 8px;border:1px solid #e2e8f0;font-weight:800;text-align:right;font-variant-numeric:tabular-nums;">${Number(c.new_registered).toLocaleString()}</td>
                                    <td style="padding:5px 8px;border:1px solid #e2e8f0;color:#475569;">Newly created patient electronic medical records during ${dateLabel}</td>
                                </tr>
                                <tr>
                                    <td style="padding:5px 8px;border:1px solid #e2e8f0;font-weight:700;">Unique Individuals Served</td>
                                    <td style="padding:5px 8px;border:1px solid #e2e8f0;font-weight:800;text-align:right;font-variant-numeric:tabular-nums;">${Number(c.unique_patients_served || c.unique_served || 0).toLocaleString()}</td>
                                    <td style="padding:5px 8px;border:1px solid #e2e8f0;color:#475569;">Distinct people receiving clinical services in period (deduplicated patient headcount)</td>
                                </tr>
                                <tr style="background:#f8fafc;">
                                    <td style="padding:5px 8px;border:1px solid #e2e8f0;font-weight:700;">Total Period Consultations</td>
                                    <td style="padding:5px 8px;border:1px solid #e2e8f0;font-weight:800;text-align:right;font-variant-numeric:tabular-nums;">${curConsult}</td>
                                    <td style="padding:5px 8px;border:1px solid #e2e8f0;color:#475569;">Period-over-period trend: ${trendPct > 0 ? '+' : ''}${trendPct}% vs preceding period window</td>
                                </tr>
                            </tbody>
                        </table>

                        <!-- SECTION 2: Patient Classification Matrix (Registered vs Period Served) -->
                        <h3 style="font-size:9.5pt;font-weight:800;color:#0f172a;margin:16px 0 6px 0;text-transform:uppercase;letter-spacing:0.04em;">2. Patient Classification Matrix (All-Time Registered vs Period Served)</h3>
                        <table style="width:100%;border-collapse:collapse;margin-bottom:14px;font-size:8pt;border:1px solid #cbd5e1;page-break-inside:avoid;break-inside:avoid;">
                            <thead>
                                <tr style="background:#f1f5f9;">
                                    <th style="padding:5px 8px;border:1px solid #cbd5e1;text-align:left;">Classification Cohort</th>
                                    <th style="padding:5px 8px;border:1px solid #cbd5e1;text-align:left;">Target Clinical Care Department</th>
                                    <th style="padding:5px 8px;border:1px solid #cbd5e1;text-align:right;">Registered (All-Time)</th>
                                    <th style="padding:5px 8px;border:1px solid #cbd5e1;text-align:right;">% of Registry</th>
                                    <th style="padding:5px 8px;border:1px solid #cbd5e1;text-align:right;">Served in Period</th>
                                    <th style="padding:5px 8px;border:1px solid #cbd5e1;text-align:right;">% of Volume</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td style="padding:5px 8px;border:1px solid #e2e8f0;font-weight:700;">Pediatric (0&ndash;12 yrs)</td>
                                    <td style="padding:5px 8px;border:1px solid #e2e8f0;color:#0369a1;">Pedia &amp; Well-Child Clinic</td>
                                    <td style="padding:5px 8px;border:1px solid #e2e8f0;text-align:right;font-variant-numeric:tabular-nums;">${Number(cl.categories.pediatric.registered_count).toLocaleString()}</td>
                                    <td style="padding:5px 8px;border:1px solid #e2e8f0;text-align:right;font-variant-numeric:tabular-nums;">${cl.categories.pediatric.registered_pct}%</td>
                                    <td style="padding:5px 8px;border:1px solid #e2e8f0;text-align:right;font-weight:700;color:#0369a1;font-variant-numeric:tabular-nums;">${Number(cl.categories.pediatric.served_count).toLocaleString()}</td>
                                    <td style="padding:5px 8px;border:1px solid #e2e8f0;text-align:right;font-variant-numeric:tabular-nums;">${cl.categories.pediatric.served_pct}%</td>
                                </tr>
                                <tr style="background:#f8fafc;">
                                    <td style="padding:5px 8px;border:1px solid #e2e8f0;font-weight:700;">Regular Adult (13&ndash;59 yrs)</td>
                                    <td style="padding:5px 8px;border:1px solid #e2e8f0;color:#4338ca;">General OPD &amp; Adult Medicine</td>
                                    <td style="padding:5px 8px;border:1px solid #e2e8f0;text-align:right;font-variant-numeric:tabular-nums;">${Number(cl.categories.regular_adult.registered_count).toLocaleString()}</td>
                                    <td style="padding:5px 8px;border:1px solid #e2e8f0;text-align:right;font-variant-numeric:tabular-nums;">${cl.categories.regular_adult.registered_pct}%</td>
                                    <td style="padding:5px 8px;border:1px solid #e2e8f0;text-align:right;font-weight:700;color:#4338ca;font-variant-numeric:tabular-nums;">${Number(cl.categories.regular_adult.served_count).toLocaleString()}</td>
                                    <td style="padding:5px 8px;border:1px solid #e2e8f0;text-align:right;font-variant-numeric:tabular-nums;">${cl.categories.regular_adult.served_pct}%</td>
                                </tr>
                                <tr>
                                    <td style="padding:5px 8px;border:1px solid #e2e8f0;font-weight:700;">Senior Citizen (60+ yrs)</td>
                                    <td style="padding:5px 8px;border:1px solid #e2e8f0;color:#047857;">Priority Fast-Track Senior Lane</td>
                                    <td style="padding:5px 8px;border:1px solid #e2e8f0;text-align:right;font-variant-numeric:tabular-nums;">${Number(cl.categories.senior_citizen.registered_count).toLocaleString()}</td>
                                    <td style="padding:5px 8px;border:1px solid #e2e8f0;text-align:right;font-variant-numeric:tabular-nums;">${cl.categories.senior_citizen.registered_pct}%</td>
                                    <td style="padding:5px 8px;border:1px solid #e2e8f0;text-align:right;font-weight:700;color:#047857;font-variant-numeric:tabular-nums;">${Number(cl.categories.senior_citizen.served_count).toLocaleString()}</td>
                                    <td style="padding:5px 8px;border:1px solid #e2e8f0;text-align:right;font-variant-numeric:tabular-nums;">${cl.categories.senior_citizen.served_pct}%</td>
                                </tr>
                                <tr style="background:#f1f5f9;font-weight:800;">
                                    <td style="padding:5px 8px;border:1px solid #cbd5e1;">Demographic Subtotal</td>
                                    <td style="padding:5px 8px;border:1px solid #cbd5e1;color:#64748b;">All Primary Care Disciplines</td>
                                    <td style="padding:5px 8px;border:1px solid #cbd5e1;text-align:right;font-variant-numeric:tabular-nums;">${Number(cl.total_registered).toLocaleString()}</td>
                                    <td style="padding:5px 8px;border:1px solid #cbd5e1;text-align:right;">100%</td>
                                    <td style="padding:5px 8px;border:1px solid #cbd5e1;text-align:right;font-variant-numeric:tabular-nums;">${Number(cl.unique_served_total).toLocaleString()}</td>
                                    <td style="padding:5px 8px;border:1px solid #cbd5e1;text-align:right;">100%</td>
                                </tr>
                                <tr style="background:#fef3c7;">
                                    <td style="padding:5px 8px;border:1px solid #e2e8f0;font-weight:700;color:#92400e;">PWD (Disability Condition Overlay)</td>
                                    <td style="padding:5px 8px;border:1px solid #e2e8f0;color:#92400e;font-size:7.5pt;">Independent Disability Overlay (Can be Senior + PWD)</td>
                                    <td style="padding:5px 8px;border:1px solid #e2e8f0;text-align:right;font-weight:700;color:#92400e;font-variant-numeric:tabular-nums;">${Number(cl.pwd.registered_count).toLocaleString()}</td>
                                    <td style="padding:5px 8px;border:1px solid #e2e8f0;text-align:right;color:#92400e;font-variant-numeric:tabular-nums;">${cl.pwd.registered_percentage}%</td>
                                    <td style="padding:5px 8px;border:1px solid #e2e8f0;text-align:right;font-weight:700;color:#92400e;font-variant-numeric:tabular-nums;">${Number(cl.pwd.served_count).toLocaleString()}</td>
                                    <td style="padding:5px 8px;border:1px solid #e2e8f0;text-align:right;color:#92400e;font-variant-numeric:tabular-nums;">${cl.pwd.served_percentage}%</td>
                                </tr>
                            </tbody>
                        </table>

                        <!-- SECTION 3: Facility Influx & Daily Traffic Pulse (CHARTS + ACCESSIBLE TABLES) -->
                        <div style="page-break-before:auto;">
                            <h3 style="font-size:9.5pt;font-weight:800;color:#0f172a;margin:18px 0 6px 0;text-transform:uppercase;letter-spacing:0.04em;">3. Facility Influx &amp; Operational Traffic Pulse</h3>
                            <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-bottom:14px;">
                                ${makeChartBlock(
                                    volumeImg,
                                    'Daily Consultation Volume',
                                    'Consultation Trend Over Time',
                                    vvRows,
                                    vvHeaders,
                                    'Tracks patient consultation volume over the reporting period to monitor daily trends, surge peaks, and clinical staffing requirements.'
                                )}
                                ${makeChartBlock(
                                    peakImg,
                                    'Peak Operating Hours Distribution',
                                    'Check-in Density Across Clinic Hours',
                                    phRows,
                                    phHeaders,
                                    'Displays arrival density by hour of day (6:00 AM - 6:00 PM), identifying high-traffic triage windows to optimize doctor/nurse duty schedules.'
                                )}
                            </div>
                        </div>

                        <!-- SECTION 4: Immediate Operational Action Triggers & Risk Alerts -->
                        <div style="page-break-before:auto;">
                            <h3 style="font-size:9.5pt;font-weight:800;color:#0f172a;margin:16px 0 6px 0;text-transform:uppercase;letter-spacing:0.04em;">4. Immediate Operational Action Triggers &amp; Risk Alerts</h3>
                            <table style="width:100%;border-collapse:collapse;margin-bottom:14px;font-size:8pt;border:1px solid #cbd5e1;page-break-inside:avoid;break-inside:avoid;">
                                <thead>
                                    <tr style="background:#f1f5f9;">
                                        <th style="padding:5px 8px;border:1px solid #cbd5e1;text-align:left;">Alert Category</th>
                                        <th style="padding:5px 8px;border:1px solid #cbd5e1;text-align:center;">Priority Acuity</th>
                                        <th style="padding:5px 8px;border:1px solid #cbd5e1;text-align:right;">Quantitative Trigger</th>
                                        <th style="padding:5px 8px;border:1px solid #cbd5e1;text-align:left;">Protocol &amp; Action Required</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td style="padding:5px 8px;border:1px solid #e2e8f0;font-weight:700;">Community Health Bulletins</td>
                                        <td style="padding:5px 8px;border:1px solid #e2e8f0;text-align:center;">
                                            <span style="font-weight:700;color:${pendingAnn > 0 ? '#b45309' : '#047857'};">${pendingAnn > 0 ? 'Pending Review' : 'All Published'}</span>
                                        </td>
                                        <td style="padding:5px 8px;border:1px solid #e2e8f0;font-weight:800;text-align:right;">${pendingAnn} advisories</td>
                                        <td style="padding:5px 8px;border:1px solid #e2e8f0;color:#475569;">${pendingAnn > 0 ? 'Review & publish queued advisories to the public health bulletin.' : 'No pending announcements requiring administrative approval.'}</td>
                                    </tr>
                                    <tr style="background:#f8fafc;">
                                        <td style="padding:5px 8px;border:1px solid #e2e8f0;font-weight:700;">Pharmacy Stockouts (Zero Inventory)</td>
                                        <td style="padding:5px 8px;border:1px solid #e2e8f0;text-align:center;">
                                            <span style="font-weight:800;color:${inv.out_of_stock > 0 ? '#b91c1c' : '#047857'};">${inv.out_of_stock > 0 ? 'CRITICAL STOCKOUT' : 'Normal'}</span>
                                        </td>
                                        <td style="padding:5px 8px;border:1px solid #e2e8f0;font-weight:800;text-align:right;color:${inv.out_of_stock > 0 ? '#b91c1c' : '#0f172a'};">${inv.out_of_stock} medicines</td>
                                        <td style="padding:5px 8px;border:1px solid #e2e8f0;color:#475569;">${inv.out_of_stock > 0 ? 'Emergency requisition required for out-of-stock formulary medicines.' : 'All formulary medicines currently maintain positive stock.'}</td>
                                    </tr>
                                    <tr>
                                        <td style="padding:5px 8px;border:1px solid #e2e8f0;font-weight:700;">Pharmacy Low Buffer Stock (&le;20 Units)</td>
                                        <td style="padding:5px 8px;border:1px solid #e2e8f0;text-align:center;">
                                            <span style="font-weight:700;color:${inv.low_stock > 0 ? '#d97706' : '#047857'};">${inv.low_stock > 0 ? 'Reorder Warning' : 'Normal'}</span>
                                        </td>
                                        <td style="padding:5px 8px;border:1px solid #e2e8f0;font-weight:800;text-align:right;color:${inv.low_stock > 0 ? '#d97706' : '#0f172a'};">${inv.low_stock} medicines</td>
                                        <td style="padding:5px 8px;border:1px solid #e2e8f0;color:#475569;">${inv.low_stock > 0 ? 'Trigger supply order before stock reaches critical depletion threshold.' : 'No medicines below safety buffer threshold.'}</td>
                                    </tr>
                                    <tr style="background:#f8fafc;">
                                        <td style="padding:5px 8px;border:1px solid #e2e8f0;font-weight:700;">Medicines Expiring Soon (&le;30 Days)</td>
                                        <td style="padding:5px 8px;border:1px solid #e2e8f0;text-align:center;">
                                            <span style="font-weight:700;color:${inv.expiring_soon > 0 ? '#d97706' : '#047857'};">${inv.expiring_soon > 0 ? 'Expiring Batches' : 'Optimal'}</span>
                                        </td>
                                        <td style="padding:5px 8px;border:1px solid #e2e8f0;font-weight:800;text-align:right;">${inv.expiring_soon} batches</td>
                                        <td style="padding:5px 8px;border:1px solid #e2e8f0;color:#475569;">${inv.expiring_soon > 0 ? 'Enforce FEFO (First-Expired, First-Out) dispensing or return to distributor.' : 'No medicine batches expiring in the next 30 days.'}</td>
                                    </tr>
                                    <tr>
                                        <td style="padding:5px 8px;border:1px solid #e2e8f0;font-weight:700;">Patient Follow-Up Care Tracking</td>
                                        <td style="padding:5px 8px;border:1px solid #e2e8f0;text-align:center;">
                                            <span style="font-weight:700;color:${follow.overdue > 0 ? '#b91c1c' : '#047857'};">${follow.overdue > 0 ? 'Overdue Follow-ups' : 'Current'}</span>
                                        </td>
                                        <td style="padding:5px 8px;border:1px solid #e2e8f0;font-weight:800;text-align:right;">${follow.overdue} overdue &bull; ${follow.due_today} today</td>
                                        <td style="padding:5px 8px;border:1px solid #e2e8f0;color:#475569;">${follow.overdue > 0 ? 'Outreach team action required to contact patients with missed clinical reviews.' : 'All scheduled patient follow-up appointments are up to date.'}</td>
                                    </tr>
                                    <tr style="background:#f8fafc;">
                                        <td style="padding:5px 8px;border:1px solid #e2e8f0;font-weight:700;">Data Retention Schedule (10-Year Archival)</td>
                                        <td style="padding:5px 8px;border:1px solid #e2e8f0;text-align:center;">
                                            <span style="font-weight:800;color:${pendingDel > 0 ? '#b91c1c' : '#047857'};">${pendingDel > 0 ? 'PURGE REQUIRED' : 'Compliant'}</span>
                                        </td>
                                        <td style="padding:5px 8px;border:1px solid #e2e8f0;font-weight:800;text-align:right;color:${pendingDel > 0 ? '#b91c1c' : '#0f172a'};">${pendingDel} expired records</td>
                                        <td style="padding:5px 8px;border:1px solid #e2e8f0;color:#475569;">${pendingDel > 0 ? 'Execute statutory 10-year retention purge under RHU records management compliance.' : 'All electronic medical records comply with national retention timeline.'}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- SECTION 5: Core System Module Telemetry & Health Indicators -->
                        <div style="page-break-before:auto;">
                            <h3 style="font-size:9.5pt;font-weight:800;color:#0f172a;margin:16px 0 6px 0;text-transform:uppercase;letter-spacing:0.04em;">5. Core System Module Telemetry &amp; Health Indicators</h3>
                            <table style="width:100%;border-collapse:collapse;margin-bottom:14px;font-size:8pt;border:1px solid #cbd5e1;page-break-inside:avoid;break-inside:avoid;">
                                <thead>
                                    <tr style="background:#f1f5f9;">
                                        <th style="padding:5px 8px;border:1px solid #cbd5e1;text-align:left;">Functional Module</th>
                                        <th style="padding:5px 8px;border:1px solid #cbd5e1;text-align:left;">Key Operational Metrics &amp; Counts</th>
                                        <th style="padding:5px 8px;border:1px solid #cbd5e1;text-align:right;">Subtotal Count</th>
                                        <th style="padding:5px 8px;border:1px solid #cbd5e1;text-align:left;">Compliance / Performance Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td style="padding:5px 8px;border:1px solid #e2e8f0;font-weight:700;">Clinical Personnel on Duty</td>
                                        <td style="padding:5px 8px;border:1px solid #e2e8f0;">${staff.doctors_present} / ${staff.total_doctors} Doctors &bull; ${staff.nurses_present} / ${staff.total_nurses} Nurses</td>
                                        <td style="padding:5px 8px;border:1px solid #e2e8f0;font-weight:800;text-align:right;">${staff.on_duty} on active duty</td>
                                        <td style="padding:5px 8px;border:1px solid #e2e8f0;color:#047857;font-weight:700;">Avg Queue Wait: ${waitTime} min (triage &rarr; physician)</td>
                                    </tr>
                                    <tr style="background:#f8fafc;">
                                        <td style="padding:5px 8px;border:1px solid #e2e8f0;font-weight:700;">Pharmacy Inventory &amp; Dispensary</td>
                                        <td style="padding:5px 8px;border:1px solid #e2e8f0;">${inv.total_medicines} Formulary Medicines &bull; ${inv.pending_prescriptions} Pending Orders</td>
                                        <td style="padding:5px 8px;border:1px solid #e2e8f0;font-weight:800;text-align:right;color:${(inv.out_of_stock + inv.low_stock) > 0 ? '#b45309' : '#047857'};">${inv.out_of_stock} out &bull; ${inv.low_stock} low</td>
                                        <td style="padding:5px 8px;border:1px solid #e2e8f0;">Dispensary orders and batch expiry monitored</td>
                                    </tr>
                                    <tr>
                                        <td style="padding:5px 8px;border:1px solid #e2e8f0;font-weight:700;">System Historical Archive Vault</td>
                                        <td style="padding:5px 8px;border:1px solid #e2e8f0;">${archives['ancillary']?.count || 0} Lab/X-Ray &bull; ${archives['appointments']?.count || 0} Bookings &bull; ${archives['prescriptions']?.count || 0} Prescriptions</td>
                                        <td style="padding:5px 8px;border:1px solid #e2e8f0;font-weight:800;text-align:right;">${totalArch} files</td>
                                        <td style="padding:5px 8px;border:1px solid #e2e8f0;color:#6b21a8;font-weight:700;">Secure digital storage vault</td>
                                    </tr>
                                    <tr style="background:#f8fafc;">
                                        <td style="padding:5px 8px;border:1px solid #e2e8f0;font-weight:700;">Security Audit Trail (${tf})</td>
                                        <td style="padding:5px 8px;border:1px solid #e2e8f0;">${s.logins} Logins &bull; ${s.failed_logins} Failed &bull; ${s.critical_actions} Critical Actions &bull; ${s.patient_updates || 0} EMR Updates</td>
                                        <td style="padding:5px 8px;border:1px solid #e2e8f0;font-weight:800;text-align:right;">${s.total_events} events</td>
                                        <td style="padding:5px 8px;border:1px solid #e2e8f0;color:#047857;font-weight:700;">Role-governed audit trail verified</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- SECTION 6: Operational Activity Feeds & Bulletins Summary -->
                        <div style="page-break-before:auto;">
                            <h3 style="font-size:9.5pt;font-weight:800;color:#0f172a;margin:16px 0 6px 0;text-transform:uppercase;letter-spacing:0.04em;">6. Operational Activity Surveillance &amp; Recent Bulletins</h3>
                            <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-bottom:14px;">
                                <!-- Live Audit Log Stream -->
                                <div style="border:1px solid #cbd5e1;border-radius:6px;padding:8px 10px;background:#ffffff;page-break-inside:avoid;break-inside:avoid;">
                                    <div style="font-size:8pt;font-weight:800;text-transform:uppercase;color:#0f172a;margin-bottom:6px;border-bottom:1px solid #e2e8f0;padding-bottom:3px;">
                                        Security Audit Trail (Recent Activity)
                                    </div>
                                    <table style="width:100%;border-collapse:collapse;font-size:7pt;">
                                        <thead>
                                            <tr style="background:#f1f5f9;">
                                                <th style="padding:3px 6px;border:1px solid #cbd5e1;text-align:left;">Timestamp</th>
                                                <th style="padding:3px 6px;border:1px solid #cbd5e1;text-align:left;">Personnel</th>
                                                <th style="padding:3px 6px;border:1px solid #cbd5e1;text-align:left;">Action</th>
                                                <th style="padding:3px 6px;border:1px solid #cbd5e1;text-align:left;">Entity</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            {!! $recentLogRows !!}
                                        </tbody>
                                    </table>
                                </div>

                                <!-- Public Bulletins & Advisories -->
                                <div style="border:1px solid #cbd5e1;border-radius:6px;padding:8px 10px;background:#ffffff;page-break-inside:avoid;break-inside:avoid;">
                                    <div style="font-size:8pt;font-weight:800;text-transform:uppercase;color:#0f172a;margin-bottom:6px;border-bottom:1px solid #e2e8f0;padding-bottom:3px;">
                                        Community Outreach Bulletins
                                    </div>
                                    <table style="width:100%;border-collapse:collapse;font-size:7pt;">
                                        <thead>
                                            <tr style="background:#f1f5f9;">
                                                <th style="padding:3px 6px;border:1px solid #cbd5e1;text-align:left;">Advisory Title</th>
                                                <th style="padding:3px 6px;border:1px solid #cbd5e1;text-align:center;">Status</th>
                                                <th style="padding:3px 6px;border:1px solid #cbd5e1;text-align:left;">Excerpt</th>
                                                <th style="padding:3px 6px;border:1px solid #cbd5e1;text-align:right;">Date</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            {!! $announcementRows !!}
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                    </main>
                </td></tr></tbody>

                <tfoot style="display:table-footer-group;"><tr><td style="padding:0;border:none;">
                    <footer style="width:100%;padding-top:8px;margin-top:8px;border-top:1px solid #cbd5e1;">
                        <div style="display:flex;justify-content:space-between;align-items:flex-start;gap:16px;font-size:7pt;color:#64748b;">
                            <div style="flex:1;max-width:65%;">CONFIDENTIAL HEALTH &amp; ADMINISTRATIVE DOSSIER &bull; Republic Act No. 10173 (Data Privacy Act of 2012). Official RHU Administrative Record. Unauthorized distribution prohibited.</div>
                            <div style="text-align:right;font-weight:600;color:#334155;">
                                <div>Timeframe: ${dateLabel}</div>
                                <div>Printed: {{ now()->format('M d, Y h:i A') }} &bull; {{ auth()->check() ? auth()->user()->name : 'System' }}</div>
                            </div>
                        </div>
                    </footer>
                </td></tr></tfoot>
            </table>
        `;

        window.printIsolated(reportHTML, {
            title: `RHU Executive Management Dossier — ${dateLabel}`,
            paperSize: 'auto'
        });
    }
</script>
@endsection