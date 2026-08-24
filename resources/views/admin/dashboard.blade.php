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
            <button onclick="window.print()" class="hidden md:flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-2 px-4 rounded-xl shadow-sm transition-colors text-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                Print Report
            </button>
            <div x-data class="flex items-center gap-2 bg-white dark:bg-slate-800 p-1.5 rounded-xl shadow-xs border border-slate-200 dark:border-slate-700">
                <label for="time_filter"
                    class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest pl-2">Timeframe:</label>
                <div class="w-36">
                    <x-select 
                        id="time_filter" 
                        :options="['today' => 'Today', 'weekly' => 'This Week', 'monthly' => 'This Month', 'yearly' => 'This Year', 'all' => 'All Time']" 
                        value="monthly"
                        x-model="$store.dashboard.timeFilter" 
                        @change="$store.dashboard.refresh()"
                        size="sm"
                        class="border-0 shadow-none font-bold text-slate-800 dark:text-white"
                    />
                </div>
                <div x-show="$store.dashboard.loading" class="ml-1">
                    <svg class="animate-spin h-4 w-4 text-emerald-500" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Hero Banner / Quick Status -->
    <div
        class="relative overflow-hidden bg-gradient-to-r from-emerald-600 to-emerald-800 rounded-3xl p-8 mb-10 shadow-[0_20px_50px_rgba(16,185,129,0.15)] text-white">
        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="flex items-center gap-6">
                <div class="shrink-0">
                    <div class="w-20 h-20 rounded-2xl bg-white/20 backdrop-blur-md border border-white/30 flex items-center justify-center overflow-hidden shadow-lg">
                        @if(Auth::user()->avatar_url)
                            <img src="{{ Auth::user()->avatar_url }}" alt="{{ Auth::user()->name }}" class="w-full h-full object-cover">
                        @else
                            <span class="text-3xl font-black text-white">
                                {{ Auth::user()->initials }}
                            </span>
                        @endif
                    </div>
                </div>
                <div>
                    <h1 class="text-2xl md:text-3xl font-black mb-2">Welcome back, {{ Auth::user()->name }}!</h1>
                    <p class="text-emerald-50 text-sm font-medium opacity-90 max-w-xl" x-data>
                        The health center is currently <span
                            class="px-2 py-0.5 bg-emerald-400/30 rounded-lg font-bold">Operational</span>.
                        You have overseen <span
                            class="font-bold underline decoration-emerald-300 underline-offset-4" x-text="$store.dashboard.filtered.currentPeriodConsultations">{{ number_format($currentPeriodConsultations) }}</span>
                        consultations during this <span x-text="$store.dashboard.filterLabel">{{ $timeFilter }}</span> window.
                    </p>
                </div>
            </div>
            <div class="flex flex-wrap items-center gap-4">
                <!-- Live Time Card -->
                <div class="bg-white/10 backdrop-blur-md rounded-2xl p-4 border border-white/20 min-w-[130px]"
                    x-data="{ time: '{{ now()->format('h:i A') }}' }"
                    x-init="setInterval(() => { time = new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: true }) }, 1000)">
                    <p class="text-[10px] uppercase font-bold tracking-widest text-emerald-200 opacity-80 mb-1">Local Time
                    </p>
                    <p class="text-lg font-bold tabular-nums" x-text="time">{{ now()->format('h:i A') }}</p>
                </div>
                <!-- Date Card -->
                <div class="bg-white/10 backdrop-blur-md rounded-2xl p-4 border border-white/20 min-w-[130px]">
                    <p class="text-[10px] uppercase font-bold tracking-widest text-emerald-200 opacity-80 mb-1">System Date
                    </p>
                    <p class="text-lg font-bold">{{ now()->format('M d, Y') }}</p>
                </div>
                <!-- Personnel Card -->
                <div class="bg-white/10 backdrop-blur-md rounded-2xl p-4 border border-white/20 min-w-[130px]">
                    <p class="text-[10px] uppercase font-bold tracking-widest text-emerald-200 opacity-80 mb-1">Active Duty
                    </p>
                    <p class="text-lg font-bold">{{ $presentDoctors + $presentNurses }} Personnel</p>
                </div>
            </div>
        </div>
        <!-- Decorative background elements -->
        <div class="absolute top-0 right-0 -mr-20 -mt-20 w-64 h-64 bg-white/10 rounded-full blur-3xl"></div>
        <div class="absolute bottom-0 left-0 -ml-20 -mb-20 w-64 h-64 bg-emerald-400/20 rounded-full blur-3xl"></div>
    </div>

    <!-- Key Performance Indicators (KPIs) -->
    <div class="grid grid-cols-2 lg:grid-cols-6 gap-6 xl:gap-8 mb-10" x-data="{
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

        <!-- Real-time Stats -->
        <div x-data="{ showTooltip: false }" @mouseenter="showTooltip = true" @mouseleave="showTooltip = false"
            class="bg-white dark:bg-slate-900 rounded-3xl p-6 shadow-[0_8px_30px_rgb(0,0,0,0.04)] dark:shadow-none border border-slate-100 dark:border-slate-800/80 flex flex-col justify-center relative">
            
            <!-- Custom Tooltip -->
            <div x-show="showTooltip" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 translate-y-1" class="absolute z-50 bottom-full mb-3 left-1/2 -translate-x-1/2 w-48 p-3 bg-slate-900 text-white text-[10px] rounded-xl shadow-xl pointer-events-none text-center font-medium leading-relaxed" style="display: none;">
                Number of doctors currently marked as 'Present' vs total doctors registered in the system.
                <div class="absolute top-full left-1/2 -ml-1 border-[6px] border-transparent border-t-slate-900"></div>
            </div>

            <p class="text-[10px] text-slate-500 dark:text-slate-400 uppercase font-bold tracking-wider mb-1 flex items-center gap-1">
                Doctors Present
                <svg class="w-3 h-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </p>
            <div class="flex items-end gap-2">
                <p class="text-2xl font-black text-green-600 dark:text-green-400" x-text="stats.presentDoctors">
                    {{ $presentDoctors }}
                </p>
                <p class="text-sm font-bold text-slate-400 dark:text-slate-500 mb-1">/ <span
                        x-text="stats.totalDoctors">{{ $totalDoctors }}</span></p>
            </div>
        </div>

        <div x-data="{ showTooltip: false }" @mouseenter="showTooltip = true" @mouseleave="showTooltip = false"
            class="bg-white dark:bg-slate-900 rounded-3xl p-6 shadow-[0_8px_30px_rgb(0,0,0,0.04)] dark:shadow-none border border-slate-100 dark:border-slate-800/80 flex flex-col justify-center relative">
            
            <!-- Custom Tooltip -->
            <div x-show="showTooltip" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 translate-y-1" class="absolute z-50 bottom-full mb-3 left-1/2 -translate-x-1/2 w-48 p-3 bg-slate-900 text-white text-[10px] rounded-xl shadow-xl pointer-events-none text-center font-medium leading-relaxed" style="display: none;">
                Total number of clinical and vitals nurses currently on duty.
                <div class="absolute top-full left-1/2 -ml-1 border-[6px] border-transparent border-t-slate-900"></div>
            </div>

            <p class="text-[10px] text-slate-500 dark:text-slate-400 uppercase font-bold tracking-wider mb-1 flex items-center gap-1">
                Nurses Present
                <svg class="w-3 h-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </p>
            <p class="text-2xl font-black text-teal-600 dark:text-teal-400" x-text="stats.presentNurses">
                {{ $presentNurses }}
            </p>
        </div>

        <div x-data="{ showTooltip: false }" @mouseenter="showTooltip = true" @mouseleave="showTooltip = false"
            class="bg-white dark:bg-slate-900 rounded-3xl p-6 shadow-[0_8px_30px_rgb(0,0,0,0.04)] dark:shadow-none border border-slate-100 dark:border-slate-800/80 flex flex-col justify-center relative">
            
            <!-- Custom Tooltip -->
            <div x-show="showTooltip" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 translate-y-1" class="absolute z-50 bottom-full mb-3 left-1/2 -translate-x-1/2 w-48 p-3 bg-slate-900 text-white text-[10px] rounded-xl shadow-xl pointer-events-none text-center font-medium leading-relaxed" style="display: none;">
                Total appointments scheduled for the current date.
                <div class="absolute top-full left-1/2 -ml-1 border-[6px] border-transparent border-t-slate-900"></div>
            </div>

            <p class="text-[10px] text-slate-500 dark:text-slate-400 uppercase font-bold tracking-wider mb-1 flex items-center gap-1">
                Today's Appts
                <svg class="w-3 h-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </p>
            <p class="text-2xl font-black text-purple-600 dark:text-purple-400" x-text="stats.todayAppointments">
                {{ $todayAppointments }}
            </p>
        </div>

        <!-- Filtered Stats -->
        <div x-data="{ showTooltip: false }" @mouseenter="showTooltip = true" @mouseleave="showTooltip = false"
            class="bg-white dark:bg-slate-900 rounded-3xl p-6 shadow-[0_8px_30px_rgb(0,0,0,0.04)] dark:shadow-none border border-slate-100 dark:border-slate-800/80 flex flex-col justify-center relative transition-all duration-300">
            
            <!-- Custom Tooltip -->
            <div x-show="showTooltip" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 translate-y-1" class="absolute z-50 bottom-full mb-3 left-1/2 -translate-x-1/2 w-48 p-3 bg-slate-900 text-white text-[10px] rounded-xl shadow-xl pointer-events-none text-center font-medium leading-relaxed" style="display: none;">
                Total volume of consultations handled within the currently selected timeframe.
                <div class="absolute top-full left-1/2 -ml-1 border-[6px] border-transparent border-t-slate-900"></div>
            </div>

            <p class="text-[10px] text-slate-500 dark:text-slate-400 uppercase font-bold tracking-wider mb-1 flex items-center gap-1">
                Period Volume
                <svg class="w-3 h-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </p>
            <div class="flex items-end gap-2">
                <p class="text-2xl font-black text-slate-800 dark:text-white" x-data x-text="$store.dashboard.filtered.currentPeriodConsultations">
                    {{ number_format($currentPeriodConsultations) }}
                </p>
                <span x-data="{ get trend() { return $store.dashboard.filtered.trendPercentage } }" class="text-xs font-bold mb-1 flex items-center"
                    :class="trend > 0 ? 'text-red-500' : (trend < 0 ? 'text-green-500' : 'text-slate-400')">
                    <template x-if="trend > 0">
                        <span class="flex items-center"><svg class="w-3 h-3 mr-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg><span x-text="trend + '%'"></span></span>
                    </template>
                    <template x-if="trend < 0">
                        <span class="flex items-center"><svg class="w-3 h-3 mr-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"></path></svg><span x-text="Math.abs(trend) + '%'"></span></span>
                    </template>
                    <template x-if="trend === 0">
                        <span>— 0%</span>
                    </template>
                </span>
            </div>
        </div>

        <div x-data="{ showTooltip: false }" @mouseenter="showTooltip = true" @mouseleave="showTooltip = false"
            class="bg-white dark:bg-slate-900 rounded-3xl p-6 shadow-[0_8px_30px_rgb(0,0,0,0.04)] dark:shadow-none border border-slate-100 dark:border-slate-800/80 flex flex-col justify-center relative">
            
            <!-- Custom Tooltip -->
            <div x-show="showTooltip" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 translate-y-1" class="absolute z-50 bottom-full mb-3 left-1/2 -translate-x-1/2 w-48 p-3 bg-slate-900 text-white text-[10px] rounded-xl shadow-xl pointer-events-none text-center font-medium leading-relaxed" style="display: none;">
                Average time a patient spends in the queue from triage until they are seen by a doctor.
                <div class="absolute top-full left-1/2 -ml-1 border-[6px] border-transparent border-t-slate-900"></div>
            </div>

            <p class="text-[10px] text-slate-500 dark:text-slate-400 uppercase font-bold tracking-wider mb-1 flex items-center gap-1">
                Avg Wait Time
                <svg class="w-3 h-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </p>
            <p class="text-2xl font-black text-orange-600 dark:text-orange-400" x-data>
                <span x-text="`${$store.dashboard.filtered.avgWaitTime} min`">{{ $avgWaitTime }} min</span>
            </p>
        </div>

        <div
            class="bg-white dark:bg-slate-900 rounded-3xl p-6 shadow-[0_8px_30px_rgb(0,0,0,0.04)] dark:shadow-none border border-slate-100 dark:border-slate-800/80 flex flex-col justify-center relative overflow-hidden group">
            <p class="text-[10px] text-slate-500 dark:text-slate-400 uppercase font-bold tracking-wider mb-2 flex items-center gap-1" title="Percentage of patients who returned for their required follow-up appointments within the designated window.">
                Return Rate
                <svg class="w-3 h-3 text-slate-400 cursor-help" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </p>
            <div class="relative w-16 h-16 ml-auto mr-auto">
                <canvas id="returnRateChart"></canvas>
                <div class="absolute inset-0 flex items-center justify-center" x-data>
                    <span class="text-sm font-black"
                        :class="$store.dashboard.filtered.complianceRate >= 50 ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400'"
                        x-text="$store.dashboard.filtered.complianceRate + '%'">{{ $complianceRate }}%</span>
                </div>
            </div>
            <!-- Tooltip -->
            <div
                class="absolute inset-0 bg-slate-900/90 flex items-center justify-center p-3 opacity-0 group-hover:opacity-100 transition-opacity duration-300" x-data>
                <p class="text-[10px] text-white text-center font-medium leading-tight">Based on <span x-text="$store.dashboard.filtered.totalFollowupsNeeded">{{ $totalFollowupsNeeded }}</span>
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
                class="bg-white dark:bg-slate-900 rounded-3xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] dark:shadow-none border border-slate-100 dark:border-slate-800/80 h-fit overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-800/80 flex justify-between items-center">
                    <h3 class="text-sm font-bold text-slate-800 dark:text-white uppercase tracking-wide">Recent
                        Announcements</h3>
                    <a href="{{ route('admin.announcements.index') }}"
                        class="text-xs font-bold text-emerald-600 dark:text-emerald-400 hover:text-emerald-800 transition-colors">Manage
                        All</a>
                </div>
                <div class="p-6 space-y-4">
                    @forelse($announcements as $announcement)
                        <div
                            class="border-b border-slate-100 dark:border-slate-800/50 pb-4 last:border-0 last:pb-0 hover:-translate-y-0.5 transition-transform">
                            <p class="text-sm font-bold text-slate-900 dark:text-white truncate">{{ $announcement->title }}</p>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                                {{ Str::limit($announcement->content, 60) }}
                            </p>
                            <span
                                class="text-[10px] font-bold uppercase tracking-wider text-emerald-600 dark:text-emerald-400 block mt-2">{{ $announcement->created_at->diffForHumans() }}</span>
                        </div>
                    @empty
                        <div
                            class="flex flex-col items-center justify-center py-12 text-center text-slate-500 dark:text-slate-400">
                            <div
                                class="p-4 bg-slate-50 dark:bg-slate-800/50 rounded-full mb-3 border border-slate-100 dark:border-slate-800/50">
                                <svg class="w-8 h-8 text-slate-300 dark:text-slate-600" fill="none" stroke="currentColor"
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
                class="bg-white dark:bg-slate-900 rounded-3xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] dark:shadow-none border border-red-100 dark:border-red-900/30 h-fit overflow-hidden">
                <div
                    class="px-6 py-4 border-b border-red-100 dark:border-red-900/30 bg-red-50/50 dark:bg-red-900/10 flex justify-between items-center">
                    <h3
                        class="text-sm font-bold text-red-800 dark:text-red-400 flex items-center gap-2 uppercase tracking-wide">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                            </path>
                        </svg>
                        Data Retention
                    </h3>
                    @if($pendingDeletionCount > 0)
                        <span
                            class="bg-red-100 dark:bg-red-900/50 text-red-800 dark:text-red-400 text-xs px-2 py-0.5 rounded border border-red-200 dark:border-red-800 font-extrabold">{{ $pendingDeletionCount }}
                            Pending</span>
                    @endif
                </div>
                <div class="p-6">
                    @if($pendingDeletionCount > 0)
                        <div
                            class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800/50 text-red-700 dark:text-red-400 p-4 rounded-2xl mb-4 text-sm shadow-sm">
                            <strong class="font-bold">Critical Action Required:</strong> There are <strong
                                class="font-bold">{{ $pendingDeletionCount }}</strong> inactive patient records scheduled for
                            permanent deletion based on the 10-year RHU Data Retention Policy.
                        </div>
                        <a href="{{ route('admin.retention.index') }}"
                            class="block bg-red-600 hover:bg-red-700 text-white font-bold py-2.5 px-4 rounded-xl text-sm transition-colors text-center shadow-sm">
                            Review & Manage Records
                        </a>
                    @else
                        <div
                            class="flex flex-col items-center justify-center py-4 text-center text-slate-500 dark:text-slate-400">
                            <div
                                class="p-3 bg-emerald-50 dark:bg-emerald-900/20 text-emerald-500 dark:text-emerald-400 rounded-full mb-3 border border-emerald-100 dark:border-emerald-800/30">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7">
                                    </path>
                                </svg>
                            </div>
                            <p class="font-bold text-slate-800 dark:text-white text-sm">All data is compliant.</p>
                            <p class="text-xs mt-1">No expired records require deletion.</p>
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
                    class="bg-white dark:bg-slate-900 rounded-3xl p-8 shadow-[0_8px_30px_rgb(0,0,0,0.04)] dark:shadow-none border border-slate-100 dark:border-slate-800/80">
                    <h3 class="text-sm font-bold text-slate-800 dark:text-white uppercase tracking-wide mb-4 flex items-center gap-1" title="Tracks the volume of patient visits over the last 7 days.">
                        Daily Volume
                        <svg class="w-3 h-3 text-slate-400 cursor-help" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </h3>
                    <div class="relative h-64 w-full">
                        <canvas id="visitVolumeChart"></canvas>
                    </div>
                </div>

                <div
                    class="bg-white dark:bg-slate-900 rounded-3xl p-8 shadow-[0_8px_30px_rgb(0,0,0,0.04)] dark:shadow-none border border-slate-100 dark:border-slate-800/80">
                    <h3 class="text-sm font-bold text-slate-800 dark:text-white uppercase tracking-wide mb-4 flex items-center gap-1" title="Distribution of patient arrival times throughout the day.">
                        Peak Hours
                        <svg class="w-3 h-3 text-slate-400 cursor-help" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </h3>
                    <div class="relative h-64 w-full">
                        <canvas id="peakHoursChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Pharmacy Inventory Insights -->
            <div class="mt-8">
                <div class="flex items-center gap-3 mb-6">
                    <div class="p-2.5 bg-teal-100 dark:bg-teal-900/40 text-teal-600 dark:text-teal-400 rounded-xl">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" /></svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-extrabold text-slate-800 dark:text-white">Pharmacy Inventory Insights</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400">Medicine dispensing demand and trends for restocking decisions.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <!-- Top Dispensed Medicines -->
                    <div class="bg-white dark:bg-slate-900 rounded-3xl p-8 shadow-[0_8px_30px_rgb(0,0,0,0.04)] dark:shadow-none border border-slate-100 dark:border-slate-800/80">
                        <h3 class="text-sm font-bold text-slate-800 dark:text-white uppercase tracking-wide mb-4 flex items-center gap-1">
                            Top Dispensed (30 Days)
                            <svg class="w-3 h-3 text-slate-400 cursor-help" fill="none" stroke="currentColor" viewBox="0 0 24 24" title="Most frequently dispensed medicines over the last 30 days."><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </h3>
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
                    <div class="bg-white dark:bg-slate-900 rounded-3xl p-8 shadow-[0_8px_30px_rgb(0,0,0,0.04)] dark:shadow-none border border-slate-100 dark:border-slate-800/80">
                        <h3 class="text-sm font-bold text-slate-800 dark:text-white uppercase tracking-wide mb-4 flex items-center gap-1">
                            Monthly Dispensing Trend
                            <svg class="w-3 h-3 text-slate-400 cursor-help" fill="none" stroke="currentColor" viewBox="0 0 24 24" title="Total units dispensed per month over the last 6 months."><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </h3>
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

            <!-- Analytics Call To Action -->
            <div class="mt-8 bg-gradient-to-br from-emerald-600 to-emerald-800 rounded-3xl p-8 shadow-lg relative overflow-hidden group">
                <div class="absolute inset-0 opacity-10 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')] mix-blend-overlay"></div>
                <div class="relative z-10 flex flex-col md:flex-row justify-between items-center gap-6">
                    <div class="text-white max-w-xl">
                        <h3 class="text-2xl font-black mb-2 tracking-tight">Need Deeper Insights?</h3>
                        <p class="text-emerald-100/90 text-sm font-medium leading-relaxed">
                            Access the full Epidemiological & Operational Analytics suite. View full-screen interactive charts for Demographics, Barangay Heatmaps, Triage Severity, Staff Workload, and export comprehensive CSV and PDF reports.
                        </p>
                    </div>
                    <a href="{{ route('admin.analytics') }}" class="shrink-0 bg-white text-emerald-800 hover:bg-emerald-50 px-8 py-4 rounded-2xl font-bold transition-all shadow-md hover:shadow-xl hover:-translate-y-1 flex items-center gap-3">
                        Launch Analytics
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
                    </a>
                </div>
                <!-- Decorative Elements -->
                <div class="absolute -right-20 -bottom-20 w-64 h-64 bg-emerald-500 rounded-full mix-blend-multiply filter blur-3xl opacity-50 group-hover:scale-110 transition-transform duration-700"></div>
                <div class="absolute -left-20 -top-20 w-48 h-48 bg-emerald-400 rounded-full mix-blend-multiply filter blur-2xl opacity-50 group-hover:scale-110 transition-transform duration-700"></div>
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