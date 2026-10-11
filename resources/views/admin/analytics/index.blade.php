@extends('layouts.admin')

@section('header', 'Detailed Analytics')

@section('content')
<!-- Header Section (Content Management Style) -->
<div class="mb-6 flex flex-col md:flex-row justify-between items-start md:items-center gap-4 pb-4 border-b border-slate-200/80 dark:border-slate-800 print:hidden">
    <div>
        <h1 class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white tracking-tight flex items-center gap-2.5">
            <span class="w-9 h-9 sm:w-10 sm:h-10 rounded-2xl bg-emerald-100/80 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0 border border-emerald-500/20 shadow-2xs">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 8v8m-4-5v5m-4-2v2m-2 4h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
            </span>
            <span>Epidemiological & Operational Analytics</span>
        </h1>
        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Deep-dive into facility metrics, population demographics, and patient flow trends.</p>
    </div>

    <div class="flex flex-wrap items-center gap-3">
        <!-- Export Dropdown -->
        <div class="relative" x-data="{ exportOpen: false }" @click.outside="exportOpen = false">
            <button @click="exportOpen = !exportOpen" type="button" class="flex items-center gap-2 h-10 px-4 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white font-bold text-xs shadow-md shadow-emerald-600/20 hover:shadow-emerald-600/30 transition-all active:scale-95 cursor-pointer select-none">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                <span>Export</span>
                <svg class="w-3.5 h-3.5 transition-transform duration-200" :class="exportOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
            </button>

            <div x-show="exportOpen" x-cloak
                 x-transition:enter="transition ease-out duration-150"
                 x-transition:enter-start="opacity-0 scale-95 -translate-y-1"
                 x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-100"
                 x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                 x-transition:leave-end="opacity-0 scale-95 -translate-y-1"
                 class="absolute right-0 mt-2 w-72 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl shadow-xl py-1.5 z-50 divide-y divide-slate-100 dark:divide-slate-800">
                
                <div class="px-3.5 py-1.5 text-[10px] font-black uppercase tracking-wider text-slate-400">
                    Export Options
                </div>

                <div class="p-1 space-y-0.5">
                    <!-- 1. Graph / Analytics Summary (CSV/Excel) -->
                    <button type="button" @click="exportOpen = false; exportAnalyticsSummaryCsv()" class="w-full text-left flex items-start gap-3 px-3 py-2.5 rounded-xl hover:bg-emerald-50/80 dark:hover:bg-slate-800/80 transition-colors group cursor-pointer">
                        <div class="w-8 h-8 rounded-lg bg-emerald-100 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0 mt-0.5 group-hover:bg-emerald-600 group-hover:text-white transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                        </div>
                        <div>
                            <span class="block text-xs font-bold text-slate-800 dark:text-white group-hover:text-emerald-600 dark:group-hover:text-emerald-400">Analytics Graph Summary (CSV)</span>
                            <span class="block text-[11px] text-slate-500 dark:text-slate-400 mt-0.5">Aggregated metrics, charts & epidemiological totals</span>
                        </div>
                    </button>

                    <!-- 2. Raw Detailed Consultations (CSV) -->
                    <button type="button" @click="exportOpen = false; exportAnalyticsCsv()" class="w-full text-left flex items-start gap-3 px-3 py-2.5 rounded-xl hover:bg-emerald-50/80 dark:hover:bg-slate-800/80 transition-colors group cursor-pointer">
                        <div class="w-8 h-8 rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 flex items-center justify-center shrink-0 mt-0.5 group-hover:bg-slate-700 group-hover:text-white transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        </div>
                        <div>
                            <span class="block text-xs font-bold text-slate-800 dark:text-white group-hover:text-emerald-600 dark:group-hover:text-emerald-400">Raw Consultation Records (CSV)</span>
                            <span class="block text-[11px] text-slate-500 dark:text-slate-400 mt-0.5">Itemized 32-column patient encounter logs</span>
                        </div>
                    </button>
                </div>

                <div class="p-1">
                    <!-- 3. Print / PDF Export Report -->
                    <button type="button" @click="exportOpen = false; printAnalyticsReport()" class="w-full text-left flex items-start gap-3 px-3 py-2.5 rounded-xl hover:bg-emerald-50/80 dark:hover:bg-slate-800/80 transition-colors group cursor-pointer">
                        <div class="w-8 h-8 rounded-lg bg-teal-100 dark:bg-teal-950/60 text-teal-600 dark:text-teal-400 flex items-center justify-center shrink-0 mt-0.5 group-hover:bg-teal-600 group-hover:text-white transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                        </div>
                        <div>
                            <span class="block text-xs font-bold text-slate-800 dark:text-white group-hover:text-teal-600 dark:group-hover:text-teal-400">Print / Save as PDF</span>
                            <span class="block text-[11px] text-slate-500 dark:text-slate-400 mt-0.5">Official printable dossier with chart graphs</span>
                        </div>
                    </button>
                </div>
            </div>
        </div>
        <form action="{{ route('admin.analytics') }}" method="GET" class="h-10 flex items-center gap-2 bg-white dark:bg-slate-900/90 px-3 rounded-xl shadow-2xs border border-slate-200/90 dark:border-slate-700/80 focus-within:border-emerald-500 transition-all">
            <label for="time_filter" class="text-[10px] font-black text-slate-400 dark:text-slate-400 uppercase tracking-wider pl-1 shrink-0">Timeframe:</label>
            <div class="w-32">
                <x-select 
                    name="time_filter" 
                    id="time_filter" 
                    :options="['today' => 'Today', 'weekly' => 'This Week', 'monthly' => 'This Month', 'yearly' => 'This Year', 'all' => 'All Time']" 
                    :value="$timeFilter" 
                    @change="$el.closest('form').submit()"
                    size="sm"
                    class="border-0 shadow-none font-bold text-xs text-slate-800 dark:text-white !py-0 focus:ring-0 bg-transparent"
                />
            </div>
        </form>
    </div>
</div>



@php
    $totalPeriodVisits = array_sum($visitVolumeData['data'] ?? [0]);
    $peakHourMaxVal = max($peakHoursData['data'] ?? [0]);
    $peakHourIndex = array_search($peakHourMaxVal, $peakHoursData['data'] ?? []);
    $busiestHourLabel = ($peakHourMaxVal > 0 && $peakHourIndex !== false) ? ($peakHoursData['labels'][$peakHourIndex] ?? 'N/A') : '8:00 AM - 5:00 PM';
    
    $maxDemoGroup = 'N/A';
    $maxDemoCount = 0;
    foreach (($demoData['labels'] ?? []) as $idx => $label) {
        $totalDemo = ($demoData['Male'][$idx] ?? 0) + ($demoData['Female'][$idx] ?? 0);
        if ($totalDemo > $maxDemoCount) {
            $maxDemoCount = $totalDemo;
            $maxDemoGroup = $label;
        }
    }
    
    $dominantSeverity = 'Standard';
    if (!empty($severityData) && count($severityData) > 0) {
        $sortedSev = is_array($severityData) ? $severityData : $severityData->toArray();
        arsort($sortedSev);
        $dominantSeverity = ucfirst(array_key_first($sortedSev) ?? 'Mild');
    }
@endphp

<!-- Scope Clarification Banner (Detailed Analytics vs Executive Dashboard) -->
<div class="mb-6 p-4 sm:p-5 rounded-2xl bg-gradient-to-r from-teal-50 via-emerald-50 to-sky-50 dark:from-slate-800/90 dark:via-emerald-950/20 dark:to-slate-800/90 border border-emerald-200/80 dark:border-emerald-800/40 flex flex-col md:flex-row items-start md:items-center justify-between gap-4 shadow-xs print:hidden">
    <div class="flex items-start gap-3.5">
        <div class="w-10 h-10 rounded-2xl bg-emerald-600 text-white flex items-center justify-center shrink-0 shadow-sm mt-0.5">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
        </div>
        <div>
            <div class="flex items-center gap-2 flex-wrap">
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-emerald-600 text-white shadow-2xs">Deep Analytics Scope</span>
                <span class="text-xs font-bold text-slate-800 dark:text-slate-100">Longitudinal Epidemiological Surveillance & HR Telemetry</span>
            </div>
            <p class="text-xs text-slate-600 dark:text-slate-300 mt-1 leading-relaxed">
                This module provides longitudinal public health epidemiology, vulnerable demographic distributions, disease morbidity surveillance, catchment heatmaps, and staff productivity telemetry. For daily operational pulse, immediate inventory alerts, and real-time live queues, switch to the <a href="{{ route('admin.dashboard') }}" class="font-bold text-emerald-700 dark:text-emerald-400 underline hover:text-emerald-800">Executive Dashboard &rarr;</a>
            </p>
        </div>
    </div>
    <div class="flex items-center gap-2 shrink-0 w-full md:w-auto justify-end">
        <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-white dark:bg-slate-800 border border-emerald-300 dark:border-emerald-700 text-emerald-700 dark:text-emerald-300 font-bold text-xs hover:bg-emerald-50 dark:hover:bg-slate-700/80 transition-all shadow-2xs">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
            <span>Executive Dashboard</span>
        </a>
    </div>
</div>

<!-- Section Shortcut Jump Anchors -->
<div class="flex items-center gap-2 overflow-x-auto pb-2 mb-6 scrollbar-none print:hidden">
    <span class="text-[10px] uppercase font-black text-slate-400 tracking-wider shrink-0">Quick Jump:</span>
    <a href="#section-population" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-white dark:bg-slate-800 border border-slate-200/90 dark:border-slate-700 text-slate-700 dark:text-slate-300 text-xs font-bold hover:border-emerald-500 hover:text-emerald-600 transition-colors shrink-0 shadow-2xs">
        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
        <span>1. Population & Demographics</span>
    </a>
    <a href="#section-facility-flow" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-white dark:bg-slate-800 border border-slate-200/90 dark:border-slate-700 text-slate-700 dark:text-slate-300 text-xs font-bold hover:border-purple-500 hover:text-purple-600 transition-colors shrink-0 shadow-2xs">
        <span class="w-2 h-2 rounded-full bg-purple-500"></span>
        <span>2. Facility Influx Dynamics</span>
    </a>
    <a href="#section-staff-telemetry" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-white dark:bg-slate-800 border border-slate-200/90 dark:border-slate-700 text-slate-700 dark:text-slate-300 text-xs font-bold hover:border-amber-500 hover:text-amber-600 transition-colors shrink-0 shadow-2xs">
        <span class="w-2 h-2 rounded-full bg-amber-500"></span>
        <span>3. Clinical Staff Telemetry</span>
    </a>
</div>

<!-- Restored: Four Summary Cards (Operational Flow Highlights) -->
<div class="mb-6">
    <div class="flex items-center justify-between mb-3">
        <div class="flex items-center gap-2">
            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
            <h2 class="text-xs font-black uppercase tracking-wider text-slate-500 dark:text-slate-400">Operational Flow Highlights &bull; Selected Period Peaks</h2>
        </div>
        <span class="text-[11px] font-bold text-slate-400 dark:text-slate-500">Quick intelligence snapshot</span>
    </div>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- 1. Total Visits -->
        <div class="bg-white dark:bg-slate-800 rounded-3xl p-5 border border-slate-200/90 dark:border-slate-700/80 shadow-xs flex flex-col justify-between">
            <div class="flex items-center justify-between mb-3">
                <p class="text-[10px] uppercase font-bold text-slate-500 dark:text-slate-400 tracking-wider">Total Period Encounters</p>
                <div class="w-9 h-9 rounded-2xl bg-emerald-50 dark:bg-emerald-950/50 text-emerald-600 dark:text-emerald-400 flex items-center justify-center border border-emerald-500/20">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                </div>
            </div>
            <div>
                <p class="text-3xl font-black text-slate-900 dark:text-white tracking-tight tabular-nums">{{ number_format($totalPeriodVisits) }}</p>
                <div class="mt-2.5 pt-2.5 border-t border-slate-100 dark:border-slate-700/60 flex items-center justify-between text-[11px] text-slate-500 dark:text-slate-400 font-medium">
                    <span>Consultations recorded</span>
                    <span class="font-bold text-emerald-600 dark:text-emerald-400">Volume Total</span>
                </div>
            </div>
        </div>

        <!-- 2. Peak Flow Window -->
        <div class="bg-white dark:bg-slate-800 rounded-3xl p-5 border border-slate-200/90 dark:border-slate-700/80 shadow-xs flex flex-col justify-between">
            <div class="flex items-center justify-between mb-3">
                <p class="text-[10px] uppercase font-bold text-slate-500 dark:text-slate-400 tracking-wider">Peak Patient Flow</p>
                <div class="w-9 h-9 rounded-2xl bg-purple-50 dark:bg-purple-950/50 text-purple-600 dark:text-purple-400 flex items-center justify-center border border-purple-500/20">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
            <div>
                <p class="text-2xl font-black text-slate-900 dark:text-white tracking-tight truncate" title="{{ $busiestHourLabel }}">{{ $busiestHourLabel }}</p>
                <div class="mt-2.5 pt-2.5 border-t border-slate-100 dark:border-slate-700/60 flex items-center justify-between text-[11px] text-slate-500 dark:text-slate-400 font-medium">
                    <span>Busiest operating time</span>
                    <span class="font-bold text-purple-600 dark:text-purple-400">{{ number_format($peakHourMaxVal) }} visits peak</span>
                </div>
            </div>
        </div>

        <!-- 3. Top Demographic -->
        <div class="bg-white dark:bg-slate-800 rounded-3xl p-5 border border-slate-200/90 dark:border-slate-700/80 shadow-xs flex flex-col justify-between">
            <div class="flex items-center justify-between mb-3">
                <p class="text-[10px] uppercase font-bold text-slate-500 dark:text-slate-400 tracking-wider">Top Demographic Cohort</p>
                <div class="w-9 h-9 rounded-2xl bg-sky-50 dark:bg-sky-950/50 text-sky-600 dark:text-sky-400 flex items-center justify-center border border-sky-500/20">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                </div>
            </div>
            <div>
                <p class="text-2xl font-black text-slate-900 dark:text-white tracking-tight truncate">{{ $maxDemoGroup }}</p>
                <div class="mt-2.5 pt-2.5 border-t border-slate-100 dark:border-slate-700/60 flex items-center justify-between text-[11px] text-slate-500 dark:text-slate-400 font-medium">
                    <span>Largest consulting cohort</span>
                    <span class="font-bold text-sky-600 dark:text-sky-400">{{ number_format($maxDemoCount) }} cases</span>
                </div>
            </div>
        </div>

        <!-- 4. Dominant Acuity / Severity -->
        <div class="bg-white dark:bg-slate-800 rounded-3xl p-5 border border-slate-200/90 dark:border-slate-700/80 shadow-xs flex flex-col justify-between">
            <div class="flex items-center justify-between mb-3">
                <p class="text-[10px] uppercase font-bold text-slate-500 dark:text-slate-400 tracking-wider">Prevalent Clinical Acuity</p>
                <div class="w-9 h-9 rounded-2xl bg-amber-50 dark:bg-amber-950/50 text-amber-600 dark:text-amber-400 flex items-center justify-center border border-amber-500/20">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                </div>
            </div>
            <div>
                <p class="text-2xl font-black text-slate-900 dark:text-white tracking-tight truncate">{{ $dominantSeverity }}</p>
                <div class="mt-2.5 pt-2.5 border-t border-slate-100 dark:border-slate-700/60 flex items-center justify-between text-[11px] text-slate-500 dark:text-slate-400 font-medium">
                    <span>Dominant triage severity</span>
                    <span class="font-bold text-amber-600 dark:text-amber-400">Triage Profile</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Patient Census Ribbon (Municipal Registry Baseline) -->
<div class="mb-4">
    <div class="flex items-center justify-between mb-3">
        <div class="flex items-center gap-2">
            <span class="w-2 h-2 rounded-full bg-blue-500"></span>
            <h2 class="text-xs font-black uppercase tracking-wider text-slate-500 dark:text-slate-400">Municipal Patient Census &bull; Official Master Registry</h2>
        </div>
        <span class="text-[11px] font-bold text-slate-400 dark:text-slate-500">Registry Totals</span>
    </div>
</div>
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
    <div class="bg-white dark:bg-slate-800 rounded-3xl p-5 border border-slate-200/90 dark:border-slate-700/80 shadow-xs flex flex-col justify-between">
        <div class="flex items-center justify-between mb-3">
            <p class="text-[10px] uppercase font-bold text-slate-500 dark:text-slate-400 tracking-wider">Total Registered Patients</p>
            <div class="w-9 h-9 rounded-2xl bg-emerald-50 dark:bg-emerald-950/50 text-emerald-600 dark:text-emerald-400 flex items-center justify-center border border-emerald-500/20">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
            </div>
        </div>
        <div>
            <p class="text-3xl font-black text-slate-900 dark:text-white tracking-tight tabular-nums">{{ number_format($census['total_registered']) }}</p>
            <div class="mt-2.5 pt-2.5 border-t border-slate-100 dark:border-slate-700/60 flex items-center justify-between text-[11px]">
                <div class="flex items-center gap-1.5 font-bold">
                    <span class="text-blue-600 dark:text-blue-400">M: {{ number_format($census['male_registered']) }}</span>
                    <span class="text-slate-300 dark:text-slate-600">&bull;</span>
                    <span class="text-pink-600 dark:text-pink-400">F: {{ number_format($census['female_registered']) }}</span>
                </div>
                <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 border border-emerald-500/20">
                    {{ $census['philhealth_percentage'] }}% PhilHealth
                </span>
            </div>
        </div>
    </div>

    <div class="bg-white dark:bg-slate-800 rounded-3xl p-5 border border-slate-200/90 dark:border-slate-700/80 shadow-xs flex flex-col justify-between">
        <div class="flex items-center justify-between mb-3">
            <p class="text-[10px] uppercase font-bold text-slate-500 dark:text-slate-400 tracking-wider">New Registrations</p>
            <div class="w-9 h-9 rounded-2xl bg-teal-50 dark:bg-teal-950/50 text-teal-600 dark:text-teal-400 flex items-center justify-center border border-teal-500/20">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
            </div>
        </div>
        <div>
            <p class="text-3xl font-black text-slate-900 dark:text-white tracking-tight tabular-nums">{{ number_format($census['new_registered']) }}</p>
            <div class="mt-2.5 pt-2.5 border-t border-slate-100 dark:border-slate-700/60 flex items-center justify-between text-[11px] text-slate-500 dark:text-slate-400 font-medium">
                <span>Enrolled this period</span>
                <span class="font-bold text-teal-600 dark:text-teal-400">{{ ucfirst($timeFilter) }}</span>
            </div>
        </div>
    </div>

    <div class="bg-white dark:bg-slate-800 rounded-3xl p-5 border border-slate-200/90 dark:border-slate-700/80 shadow-xs flex flex-col justify-between">
        <div class="flex items-center justify-between mb-3">
            <p class="text-[10px] uppercase font-bold text-slate-500 dark:text-slate-400 tracking-wider">Unique Patients Served</p>
            <div class="w-9 h-9 rounded-2xl bg-indigo-50 dark:bg-indigo-950/50 text-indigo-600 dark:text-indigo-400 flex items-center justify-center border border-indigo-500/20">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
            </div>
        </div>
        <div>
            <p class="text-3xl font-black text-slate-900 dark:text-white tracking-tight tabular-nums">{{ number_format($census['unique_patients_served']) }}</p>
            <div class="mt-2.5 pt-2.5 border-t border-slate-100 dark:border-slate-700/60 flex items-center justify-between text-[11px] text-slate-500 dark:text-slate-400 font-medium">
                <span>Distinct people seen</span>
                <span class="font-bold text-indigo-600 dark:text-indigo-400">Deduplicated</span>
            </div>
        </div>
    </div>

    <div class="bg-white dark:bg-slate-800 rounded-3xl p-5 border border-slate-200/90 dark:border-slate-700/80 shadow-xs flex flex-col justify-between">
        <div class="flex items-center justify-between mb-3">
            <p class="text-[10px] uppercase font-bold text-slate-500 dark:text-slate-400 tracking-wider">Total Consultations</p>
            <div class="w-9 h-9 rounded-2xl bg-amber-50 dark:bg-amber-950/50 text-amber-600 dark:text-amber-400 flex items-center justify-center border border-amber-500/20">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
            </div>
        </div>
        <div>
            <div class="flex items-baseline justify-between">
                <p class="text-3xl font-black text-slate-900 dark:text-white tracking-tight tabular-nums">{{ number_format($census['total_consultations']) }}</p>
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-extrabold {{ $census['trend_percentage'] > 0 ? 'bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 border border-emerald-500/20' : ($census['trend_percentage'] < 0 ? 'bg-rose-50 dark:bg-rose-950/60 text-rose-700 dark:text-rose-300 border border-rose-500/20' : 'bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300') }}">
                    @if($census['trend_percentage'] > 0)
                        <svg class="w-3 h-3 mr-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>+{{ $census['trend_percentage'] }}%
                    @elseif($census['trend_percentage'] < 0)
                        <svg class="w-3 h-3 mr-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"></path></svg>{{ $census['trend_percentage'] }}%
                    @else
                        0%
                    @endif
                </span>
            </div>
            <div class="mt-2.5 pt-2.5 border-t border-slate-100 dark:border-slate-700/60 flex items-center justify-between text-[11px] text-slate-500 dark:text-slate-400 font-medium">
                <span>Encounters recorded</span>
                <span>vs previous window</span>
            </div>
        </div>
    </div>
</div>

<!-- Alpine component to manage individual chart data fetching -->
<!-- Alpine component to manage individual chart data fetching -->
<div x-data="analyticsDashboard()" class="space-y-12">

    <!-- ========================================================================= -->
    <!-- SECTION 1: POPULATION HEALTH & EPIDEMIOLOGICAL SURVEILLANCE              -->
    <!-- ========================================================================= -->
    <div id="section-population" class="pt-2">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 pb-3 border-b border-slate-200/80 dark:border-slate-800 mb-6">
            <div class="flex items-center gap-2.5">
                <h2 class="text-base sm:text-lg font-black text-slate-900 dark:text-white tracking-tight">
                    1. Population Health, Morbidity &amp; Demographic Profile
                </h2>
            </div>
            <p class="text-xs text-slate-500 dark:text-slate-400">
                DOH-aligned morbidity ranking, vulnerable sector matrices, and community catchment
            </p>
        </div>

        <div class="space-y-8">
            <!-- Row 1.1: Patient Classification Matrix & Top 10 Leading Diagnoses (Evaluator Priority) -->
            <!-- Row 4.5: Patient Classification Matrix & Top 10 Leading Causes of Morbidity (Evaluator Priority) -->
    <div class="grid grid-cols-1 xl:grid-cols-2 gap-8">
        <!-- 1. Patient Classification Breakdown Matrix -->
        <div class="bg-white dark:bg-slate-800 rounded-3xl shadow-xs hover:shadow-sm transition-all border border-slate-200/90 dark:border-slate-700/80 p-6 sm:p-8 flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-2xl bg-slate-100 dark:bg-slate-700/70 text-slate-600 dark:text-slate-300 flex items-center justify-center border border-slate-200/80 dark:border-slate-600/60">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                        </div>
                        <div>
                            <h3 class="text-base sm:text-lg font-black text-slate-900 dark:text-white tracking-tight">Patient Classification Matrix</h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400">All-time registered census vs period clinical utilization</p>
                        </div>
                    </div>
                    <span class="px-2.5 py-1 rounded-xl text-[10px] font-extrabold uppercase tracking-wider bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 border border-emerald-500/20">
                        {{ ucfirst($timeFilter) }}
                    </span>
                </div>

                <div class="overflow-x-auto rounded-2xl border border-slate-200/80 dark:border-slate-700/70 mt-4">
                    <table class="w-full text-xs text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50/80 dark:bg-slate-800/80 border-b border-slate-200/80 dark:border-slate-700/70 text-[10px] uppercase font-black text-slate-500 dark:text-slate-400 tracking-wider">
                                <th class="py-3 px-4">Classification Sector</th>
                                <th class="py-3 px-4 text-right">Registered (All-Time)</th>
                                <th class="py-3 px-4 text-right">Served (Period)</th>
                                <th class="py-3 px-4 text-left">Clinical Program</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-700/60 font-medium">
                            <!-- Pediatric -->
                            <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-700/30 transition-colors">
                                <td class="py-3 px-4 font-bold text-slate-900 dark:text-white flex items-center gap-2">
                                    <span class="w-2.5 h-2.5 rounded-full bg-sky-500"></span>
                                    <span>Pediatric (0–12 yrs)</span>
                                </td>
                                <td class="py-3 px-4 text-right tabular-nums">
                                    <span class="font-bold text-slate-900 dark:text-white">{{ number_format($classificationSummary['categories']['pediatric']['registered_count']) }}</span>
                                    <span class="text-[10px] text-slate-400">({{ $classificationSummary['categories']['pediatric']['registered_pct'] }}%)</span>
                                </td>
                                <td class="py-3 px-4 text-right tabular-nums">
                                    <span class="font-bold text-sky-600 dark:text-sky-400">{{ number_format($classificationSummary['categories']['pediatric']['served_count']) }}</span>
                                    <span class="text-[10px] text-slate-400">({{ $classificationSummary['categories']['pediatric']['served_pct'] }}%)</span>
                                </td>
                                <td class="py-3 px-4 text-slate-500 dark:text-slate-400 text-[11px]">Well-Child / EPI / Pedia OPD</td>
                            </tr>
                            <!-- Regular Adult -->
                            <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-700/30 transition-colors">
                                <td class="py-3 px-4 font-bold text-slate-900 dark:text-white flex items-center gap-2">
                                    <span class="w-2.5 h-2.5 rounded-full bg-indigo-500"></span>
                                    <span>Regular Adult (13–59 yrs)</span>
                                </td>
                                <td class="py-3 px-4 text-right tabular-nums">
                                    <span class="font-bold text-slate-900 dark:text-white">{{ number_format($classificationSummary['categories']['regular_adult']['registered_count']) }}</span>
                                    <span class="text-[10px] text-slate-400">({{ $classificationSummary['categories']['regular_adult']['registered_pct'] }}%)</span>
                                </td>
                                <td class="py-3 px-4 text-right tabular-nums">
                                    <span class="font-bold text-indigo-600 dark:text-indigo-400">{{ number_format($classificationSummary['categories']['regular_adult']['served_count']) }}</span>
                                    <span class="text-[10px] text-slate-400">({{ $classificationSummary['categories']['regular_adult']['served_pct'] }}%)</span>
                                </td>
                                <td class="py-3 px-4 text-slate-500 dark:text-slate-400 text-[11px]">General OPD &amp; Adult Consults</td>
                            </tr>
                            <!-- Senior Citizen -->
                            <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-700/30 transition-colors">
                                <td class="py-3 px-4 font-bold text-slate-900 dark:text-white flex items-center gap-2">
                                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                                    <span>Senior Citizen (60+ yrs)</span>
                                </td>
                                <td class="py-3 px-4 text-right tabular-nums">
                                    <span class="font-bold text-slate-900 dark:text-white">{{ number_format($classificationSummary['categories']['senior_citizen']['registered_count']) }}</span>
                                    <span class="text-[10px] text-slate-400">({{ $classificationSummary['categories']['senior_citizen']['registered_pct'] }}%)</span>
                                </td>
                                <td class="py-3 px-4 text-right tabular-nums">
                                    <span class="font-bold text-emerald-600 dark:text-emerald-400">{{ number_format($classificationSummary['categories']['senior_citizen']['served_count']) }}</span>
                                    <span class="text-[10px] text-slate-400">({{ $classificationSummary['categories']['senior_citizen']['served_pct'] }}%)</span>
                                </td>
                                <td class="py-3 px-4 text-slate-500 dark:text-slate-400 text-[11px]">Geriatric Fast-Track &amp; NCD</td>
                            </tr>
                            <!-- Subtotal Row -->
                            <tr class="bg-slate-100/70 dark:bg-slate-700/50 font-black">
                                <td class="py-2.5 px-4 text-slate-900 dark:text-white uppercase tracking-wider text-[11px]">Demographic Subtotal</td>
                                <td class="py-2.5 px-4 text-right tabular-nums text-slate-900 dark:text-white">
                                    {{ number_format($classificationSummary['total_registered']) }} <span class="text-[10px] text-slate-400">(100%)</span>
                                </td>
                                <td class="py-2.5 px-4 text-right tabular-nums text-slate-900 dark:text-white">
                                    {{ number_format($classificationSummary['unique_served_total']) }} <span class="text-[10px] text-slate-400">(100%)</span>
                                </td>
                                <td class="py-2.5 px-4 text-slate-500 dark:text-slate-400 text-[10px] uppercase">Master Registry</td>
                            </tr>
                            <!-- PWD Overlay Row -->
                            <tr class="bg-amber-50/70 dark:bg-amber-950/30 border-t-2 border-amber-500/30 font-bold">
                                <td class="py-3 px-4 text-amber-900 dark:text-amber-200 flex items-center gap-2">
                                    <span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span>
                                    <span>PWD (Disability Condition Overlay)</span>
                                </td>
                                <td class="py-3 px-4 text-right tabular-nums text-amber-900 dark:text-amber-200">
                                    {{ number_format($classificationSummary['pwd']['registered_count']) }}
                                    <span class="text-[10px] text-amber-600 dark:text-amber-400">({{ $classificationSummary['pwd']['registered_percentage'] }}%)</span>
                                </td>
                                <td class="py-3 px-4 text-right tabular-nums text-amber-900 dark:text-amber-200">
                                    {{ number_format($classificationSummary['pwd']['served_count']) }}
                                    <span class="text-[10px] text-amber-600 dark:text-amber-400">({{ $classificationSummary['pwd']['served_percentage'] }}%)</span>
                                </td>
                                <td class="py-3 px-4 text-amber-800 dark:text-amber-300 text-[11px] font-medium">
                                    Express Priority / Patients can be Senior + PWD
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <p class="text-[11px] text-slate-400 dark:text-slate-500 mt-3 italic">
                * Note: PWD is tracked as an independent health condition so patients can accurately belong to an age sector (Senior/Adult) while receiving PWD accessibility priority.
            </p>
        </div>

        <!-- 2. Top 10 Leading Diagnoses / Causes of Morbidity -->
        <div class="bg-white dark:bg-slate-800 rounded-3xl shadow-xs hover:shadow-sm transition-all border border-slate-200/90 dark:border-slate-700/80 p-6 sm:p-8 flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-2xl bg-slate-100 dark:bg-slate-700/70 text-slate-600 dark:text-slate-300 flex items-center justify-center border border-slate-200/80 dark:border-slate-600/60">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                        </div>
                        <div>
                            <h3 class="text-base sm:text-lg font-black text-slate-900 dark:text-white tracking-tight">Top 10 Diagnoses (Morbidity Surveillance)</h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400">Leading clinical causes of consultation during this timeframe</p>
                        </div>
                    </div>
                    <span class="text-[11px] font-bold text-slate-400">
                        {{ $topDiagnoses->sum('count') }} Total Cases
                    </span>
                </div>

                <div class="overflow-x-auto rounded-2xl border border-slate-200/80 dark:border-slate-700/70 mt-4 max-h-[360px] overflow-y-auto">
                    <table class="w-full text-xs text-left border-collapse">
                        <thead class="sticky top-0 bg-slate-50 dark:bg-slate-800 z-10">
                            <tr class="border-b border-slate-200/80 dark:border-slate-700/70 text-[10px] uppercase font-black text-slate-500 dark:text-slate-400 tracking-wider">
                                <th class="py-3 px-3 w-10 text-center">#</th>
                                <th class="py-3 px-4">Clinical Diagnosis</th>
                                <th class="py-3 px-4 text-right w-24">Cases</th>
                                <th class="py-3 px-4 text-left w-36">% Share</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-700/60 font-medium">
                            @forelse($topDiagnoses as $index => $diag)
                                <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-700/30 transition-colors">
                                    <td class="py-2.5 px-3 text-center font-bold text-slate-400">
                                        <span class="inline-flex items-center justify-center w-5 h-5 rounded-full {{ $index === 0 ? 'bg-rose-100 text-rose-700 font-black' : ($index === 1 ? 'bg-amber-100 text-amber-700' : ($index === 2 ? 'bg-teal-100 text-teal-700' : 'bg-slate-100 text-slate-600')) }} text-[10px]">
                                            {{ $index + 1 }}
                                        </span>
                                    </td>
                                    <td class="py-2.5 px-4 font-bold text-slate-900 dark:text-white truncate max-w-[200px]" title="{{ $diag->diagnosis }}">
                                        {{ $diag->diagnosis }}
                                    </td>
                                    <td class="py-2.5 px-4 text-right font-black text-slate-900 dark:text-white tabular-nums">
                                        {{ number_format($diag->count) }}
                                    </td>
                                    <td class="py-2.5 px-4">
                                        <div class="flex items-center gap-2">
                                            <div class="flex-1 bg-slate-100 dark:bg-slate-700 h-2 rounded-full overflow-hidden">
                                                <div class="bg-gradient-to-r from-emerald-500 to-teal-500 h-full rounded-full" style="width: {{ min(100, $diag->percentage) }}%"></div>
                                            </div>
                                            <span class="text-[10px] font-bold text-slate-500 dark:text-slate-400 tabular-nums w-10 text-right">
                                                {{ $diag->percentage }}%
                                            </span>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="py-8 text-center text-slate-400">
                                        No diagnoses recorded for this timeframe.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <p class="text-[11px] text-slate-400 dark:text-slate-500 mt-3">
                Source: Primary physician clinical notes and encounter diagnoses encoded into EMR.
            </p>
        </div>
    </div>

            <!-- Row 1.2: Age-Sex Demographic Pyramid (Full Width) -->
            <!-- Row 3: Age-Sex Pyramid (Full Width) -->
    <div class="bg-white dark:bg-slate-800 rounded-3xl shadow-xs hover:shadow-sm transition-all border border-slate-200/90 dark:border-slate-700/80 p-6 sm:p-8 flex flex-col w-full h-[520px]">
        <div class="flex justify-between items-center mb-6">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-2xl bg-slate-100 dark:bg-slate-700/70 text-slate-600 dark:text-slate-300 flex items-center justify-center border border-slate-200/80 dark:border-slate-600/60">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                </div>
                <div>
                    <h3 x-data="{ showTooltip: false }" @mouseenter="showTooltip = true" @mouseleave="showTooltip = false"
                        class="text-lg font-bold text-slate-900 dark:text-white flex items-center gap-2 relative">
                        Patient Demographics (Age-Sex Pyramid)
                        <svg class="w-3.5 h-3.5 text-slate-400 cursor-help" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        
                        <!-- Custom Tooltip -->
                        <div x-show="showTooltip" x-transition class="absolute z-50 bottom-full mb-2 left-0 w-48 p-2 bg-slate-950 text-white text-[10px] rounded-xl shadow-2xl pointer-events-none font-medium leading-tight border border-slate-800" style="display: none;">
                            Age and sex distribution of the patient population.
                            <div class="absolute top-full left-4 border-[4px] border-transparent border-t-slate-950"></div>
                        </div>
                    </h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Age and gender distribution among patients</p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <!-- Custom Timeframe Dropdown -->
                <div class="relative" x-data="{
                    open: false,
                    selected: '{{ $timeFilter }}',
                    options: {
                        'today': 'Today',
                        'weekly': 'This Week',
                        'monthly': 'This Month',
                        'yearly': 'This Year',
                        'all': 'All Time'
                    },
                    select(val) {
                        this.selected = val;
                        this.open = false;
                        updateChart('demographics', val);
                    }
                }" @click.outside="open = false">
                    <button type="button" @click="open = !open"
                            class="h-9 px-3.5 rounded-xl border border-slate-200/90 dark:border-slate-700 bg-white dark:bg-slate-900/80 text-slate-700 dark:text-slate-200 text-xs font-bold shadow-2xs hover:border-emerald-500/60 dark:hover:border-emerald-500/60 hover:bg-slate-50 dark:hover:bg-slate-800 focus:outline-none focus:border-emerald-500 transition-all flex items-center gap-2 cursor-pointer"
                            :class="open ? 'border-emerald-500 bg-white dark:bg-slate-800 text-emerald-600 dark:text-emerald-400' : ''">
                        <span x-text="options[selected] || 'This Month'"></span>
                        <svg class="w-3.5 h-3.5 text-slate-400 dark:text-slate-500 transition-transform duration-200"
                             :class="open ? 'rotate-180 text-emerald-500' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>

                    <div x-show="open"
                         x-transition:enter="transition ease-out duration-150"
                         x-transition:enter-start="opacity-0 translate-y-1 scale-95"
                         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                         x-transition:leave="transition ease-in duration-100"
                         x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                         x-transition:leave-end="opacity-0 translate-y-1 scale-95"
                         style="display: none;"
                         class="absolute right-0 z-50 mt-1.5 w-36 rounded-2xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 shadow-xl shadow-slate-900/20 p-1.5 backdrop-blur-md">
                        <template x-for="(label, key) in options" :key="key">
                            <button type="button" @click="select(key)"
                                    class="w-full px-3 py-2 rounded-xl text-xs font-semibold flex items-center justify-between transition-colors cursor-pointer text-left"
                                    :class="selected === key 
                                        ? 'bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 font-bold' 
                                        : 'text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-700/60'">
                                <span x-text="label"></span>
                                <svg x-show="selected === key" class="w-3.5 h-3.5 text-emerald-600 dark:text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path>
                                </svg>
                            </button>
                        </template>
                    </div>
                </div>

                <!-- Custom Export Dropdown -->
                <div class="relative" x-data="{ openExport: false }" @click.outside="openExport = false">
                    <button @click="openExport = !openExport" title="Export Chart" 
                            class="h-9 w-9 flex items-center justify-center bg-white dark:bg-slate-900/80 text-slate-600 dark:text-slate-300 hover:bg-emerald-50 dark:hover:bg-emerald-950/50 hover:text-emerald-600 dark:hover:text-emerald-400 border border-slate-200/90 dark:border-slate-700 hover:border-emerald-500/60 rounded-xl transition-all shadow-2xs focus:outline-none cursor-pointer"
                            :class="openExport ? 'border-emerald-500 bg-white dark:bg-slate-800 text-emerald-600 dark:text-emerald-400' : ''">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                    </button>
                    <div x-show="openExport" 
                         x-transition:enter="transition ease-out duration-150"
                         x-transition:enter-start="opacity-0 translate-y-1 scale-95"
                         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                         x-transition:leave="transition ease-in duration-100"
                         x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                         x-transition:leave-end="opacity-0 translate-y-1 scale-95"
                         style="display: none;" 
                         class="absolute right-0 mt-1.5 w-40 bg-white dark:bg-slate-800 rounded-2xl shadow-xl shadow-slate-900/20 border border-slate-200 dark:border-slate-700 p-1.5 z-50 backdrop-blur-md">
                        <a href="#" @click.prevent="exportChart('ageSexChart'); openExport = false" 
                           class="flex items-center gap-2 px-3 py-2 text-xs font-semibold text-slate-700 dark:text-slate-200 hover:bg-emerald-50 dark:hover:bg-emerald-950/60 hover:text-emerald-700 dark:hover:text-emerald-300 rounded-xl transition-colors">
                            <svg class="w-3.5 h-3.5 text-slate-400 dark:text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            <span>Download PNG</span>
                        </a>
                        <a href="#" @click.prevent="exportCSV('demographics'); openExport = false" 
                           class="flex items-center gap-2 px-3 py-2 text-xs font-semibold text-slate-700 dark:text-slate-200 hover:bg-emerald-50 dark:hover:bg-emerald-950/60 hover:text-emerald-700 dark:hover:text-emerald-300 rounded-xl transition-colors">
                            <svg class="w-3.5 h-3.5 text-slate-400 dark:text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            <span>Download CSV</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div class="relative flex-1 w-full min-h-0"><canvas id="ageSexChart"></canvas></div>
    </div>

            <!-- Row 1.3: Patient Classification & Severity (Doughnuts) -->
            <!-- Row 4: Classification & Severity (Half/Half Doughnuts) -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <div class="bg-white dark:bg-slate-800 rounded-3xl shadow-xs hover:shadow-sm transition-all border border-slate-200/90 dark:border-slate-700/80 p-6 sm:p-8 flex flex-col h-[420px]">
            <div class="flex justify-between items-center mb-6">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-slate-100 dark:bg-slate-700/70 text-slate-600 dark:text-slate-300 flex items-center justify-center border border-slate-200/80 dark:border-slate-600/60">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"/></svg>
                    </div>
                    <div>
                        <h3 x-data="{ showTooltip: false }" @mouseenter="showTooltip = true" @mouseleave="showTooltip = false"
                            class="text-lg font-bold text-slate-900 dark:text-white flex items-center gap-2 relative">
                            Patient Classification
                            <svg class="w-3.5 h-3.5 text-slate-400 cursor-help" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            
                            <!-- Custom Tooltip -->
                            <div x-show="showTooltip" x-transition class="absolute z-50 bottom-full mb-2 left-0 w-48 p-2 bg-slate-950 text-white text-[10px] rounded-xl shadow-2xl pointer-events-none font-medium leading-tight border border-slate-800" style="display: none;">
                                Breakdown of patients by age group (Pediatric, Adult, etc.).
                                <div class="absolute top-full left-4 border-[4px] border-transparent border-t-slate-950"></div>
                            </div>
                        </h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400">Distribution by patient sector</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <!-- Custom Timeframe Dropdown -->
                    <div class="relative" x-data="{
                        open: false,
                        selected: '{{ $timeFilter }}',
                        options: {
                            'today': 'Today',
                            'weekly': 'This Week',
                            'monthly': 'This Month',
                            'yearly': 'This Year',
                            'all': 'All Time'
                        },
                        select(val) {
                            this.selected = val;
                            this.open = false;
                            updateChart('classification', val);
                        }
                    }" @click.outside="open = false">
                        <button type="button" @click="open = !open"
                                class="h-9 px-3.5 rounded-xl border border-slate-200/90 dark:border-slate-700 bg-white dark:bg-slate-900/80 text-slate-700 dark:text-slate-200 text-xs font-bold shadow-2xs hover:border-emerald-500/60 dark:hover:border-emerald-500/60 hover:bg-slate-50 dark:hover:bg-slate-800 focus:outline-none focus:border-emerald-500 transition-all flex items-center gap-2 cursor-pointer"
                                :class="open ? 'border-emerald-500 bg-white dark:bg-slate-800 text-emerald-600 dark:text-emerald-400' : ''">
                            <span x-text="options[selected] || 'This Month'"></span>
                            <svg class="w-3.5 h-3.5 text-slate-400 dark:text-slate-500 transition-transform duration-200"
                                 :class="open ? 'rotate-180 text-emerald-500' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>

                        <div x-show="open"
                             x-transition:enter="transition ease-out duration-150"
                             x-transition:enter-start="opacity-0 translate-y-1 scale-95"
                             x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                             x-transition:leave="transition ease-in duration-100"
                             x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                             x-transition:leave-end="opacity-0 translate-y-1 scale-95"
                             style="display: none;"
                             class="absolute right-0 z-50 mt-1.5 w-36 rounded-2xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 shadow-xl shadow-slate-900/20 p-1.5 backdrop-blur-md">
                            <template x-for="(label, key) in options" :key="key">
                                <button type="button" @click="select(key)"
                                        class="w-full px-3 py-2 rounded-xl text-xs font-semibold flex items-center justify-between transition-colors cursor-pointer text-left"
                                        :class="selected === key 
                                            ? 'bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 font-bold' 
                                            : 'text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-700/60'">
                                    <span x-text="label"></span>
                                    <svg x-show="selected === key" class="w-3.5 h-3.5 text-emerald-600 dark:text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                </button>
                            </template>
                        </div>
                    </div>

                    <!-- Custom Export Dropdown -->
                    <div class="relative" x-data="{ openExport: false }" @click.outside="openExport = false">
                        <button @click="openExport = !openExport" title="Export Chart" 
                                class="h-9 w-9 flex items-center justify-center bg-white dark:bg-slate-900/80 text-slate-600 dark:text-slate-300 hover:bg-emerald-50 dark:hover:bg-emerald-950/50 hover:text-emerald-600 dark:hover:text-emerald-400 border border-slate-200/90 dark:border-slate-700 hover:border-emerald-500/60 rounded-xl transition-all shadow-2xs focus:outline-none cursor-pointer"
                                :class="openExport ? 'border-emerald-500 bg-white dark:bg-slate-800 text-emerald-600 dark:text-emerald-400' : ''">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                        </button>
                        <div x-show="openExport" 
                             x-transition:enter="transition ease-out duration-150"
                             x-transition:enter-start="opacity-0 translate-y-1 scale-95"
                             x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                             x-transition:leave="transition ease-in duration-100"
                             x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                             x-transition:leave-end="opacity-0 translate-y-1 scale-95"
                             style="display: none;" 
                             class="absolute right-0 mt-1.5 w-40 bg-white dark:bg-slate-800 rounded-2xl shadow-xl shadow-slate-900/20 border border-slate-200 dark:border-slate-700 p-1.5 z-50 backdrop-blur-md">
                            <a href="#" @click.prevent="exportChart('classificationChart'); openExport = false" 
                               class="flex items-center gap-2 px-3 py-2 text-xs font-semibold text-slate-700 dark:text-slate-200 hover:bg-emerald-50 dark:hover:bg-emerald-950/60 hover:text-emerald-700 dark:hover:text-emerald-300 rounded-xl transition-colors">
                                <svg class="w-3.5 h-3.5 text-slate-400 dark:text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                <span>Download PNG</span>
                            </a>
                            <a href="#" @click.prevent="exportCSV('classification'); openExport = false" 
                                class="flex items-center gap-2 px-3 py-2 text-xs font-semibold text-slate-700 dark:text-slate-200 hover:bg-emerald-50 dark:hover:bg-emerald-950/60 hover:text-emerald-700 dark:hover:text-emerald-300 rounded-xl transition-colors">
                                <svg class="w-3.5 h-3.5 text-slate-400 dark:text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                <span>Download CSV</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="relative flex-1 w-full min-h-0"><canvas id="classificationChart"></canvas></div>
        </div>

        <div class="bg-white dark:bg-slate-800 rounded-3xl shadow-xs hover:shadow-sm transition-all border border-slate-200/90 dark:border-slate-700/80 p-6 sm:p-8 flex flex-col h-[420px]">
            <div class="flex justify-between items-center mb-6">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-slate-100 dark:bg-slate-700/70 text-slate-600 dark:text-slate-300 flex items-center justify-center border border-slate-200/80 dark:border-slate-600/60">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    </div>
                    <div>
                        <h3 x-data="{ showTooltip: false }" @mouseenter="showTooltip = true" @mouseleave="showTooltip = false"
                            class="text-lg font-bold text-slate-900 dark:text-white flex items-center gap-2 relative">
                            Triage Severity
                            <svg class="w-3.5 h-3.5 text-slate-400 cursor-help" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            
                            <!-- Custom Tooltip -->
                            <div x-show="showTooltip" x-transition class="absolute z-50 bottom-full mb-2 left-0 w-48 p-2 bg-slate-950 text-white text-[10px] rounded-xl shadow-2xl pointer-events-none font-medium leading-tight border border-slate-800" style="display: none;">
                                Percentage of patients categorized by urgency level (Mild, Moderate, Severe).
                                <div class="absolute top-full left-4 border-[4px] border-transparent border-t-slate-950"></div>
                            </div>
                        </h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400">Case priority and urgency breakdown</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <!-- Custom Timeframe Dropdown -->
                    <div class="relative" x-data="{
                        open: false,
                        selected: '{{ $timeFilter }}',
                        options: {
                            'today': 'Today',
                            'weekly': 'This Week',
                            'monthly': 'This Month',
                            'yearly': 'This Year',
                            'all': 'All Time'
                        },
                        select(val) {
                            this.selected = val;
                            this.open = false;
                            updateChart('severity', val);
                        }
                    }" @click.outside="open = false">
                        <button type="button" @click="open = !open"
                                class="h-9 px-3.5 rounded-xl border border-slate-200/90 dark:border-slate-700 bg-white dark:bg-slate-900/80 text-slate-700 dark:text-slate-200 text-xs font-bold shadow-2xs hover:border-emerald-500/60 dark:hover:border-emerald-500/60 hover:bg-slate-50 dark:hover:bg-slate-800 focus:outline-none focus:border-emerald-500 transition-all flex items-center gap-2 cursor-pointer"
                                :class="open ? 'border-emerald-500 bg-white dark:bg-slate-800 text-emerald-600 dark:text-emerald-400' : ''">
                            <span x-text="options[selected] || 'This Month'"></span>
                            <svg class="w-3.5 h-3.5 text-slate-400 dark:text-slate-500 transition-transform duration-200"
                                 :class="open ? 'rotate-180 text-emerald-500' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>

                        <div x-show="open"
                             x-transition:enter="transition ease-out duration-150"
                             x-transition:enter-start="opacity-0 translate-y-1 scale-95"
                             x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                             x-transition:leave="transition ease-in duration-100"
                             x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                             x-transition:leave-end="opacity-0 translate-y-1 scale-95"
                             style="display: none;"
                             class="absolute right-0 z-50 mt-1.5 w-36 rounded-2xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 shadow-xl shadow-slate-900/20 p-1.5 backdrop-blur-md">
                            <template x-for="(label, key) in options" :key="key">
                                <button type="button" @click="select(key)"
                                        class="w-full px-3 py-2 rounded-xl text-xs font-semibold flex items-center justify-between transition-colors cursor-pointer text-left"
                                        :class="selected === key 
                                            ? 'bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 font-bold' 
                                            : 'text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-700/60'">
                                    <span x-text="label"></span>
                                    <svg x-show="selected === key" class="w-3.5 h-3.5 text-emerald-600 dark:text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                </button>
                            </template>
                        </div>
                    </div>

                    <!-- Custom Export Dropdown -->
                    <div class="relative" x-data="{ openExport: false }" @click.outside="openExport = false">
                        <button @click="openExport = !openExport" title="Export Chart" 
                                class="h-9 w-9 flex items-center justify-center bg-white dark:bg-slate-900/80 text-slate-600 dark:text-slate-300 hover:bg-emerald-50 dark:hover:bg-emerald-950/50 hover:text-emerald-600 dark:hover:text-emerald-400 border border-slate-200/90 dark:border-slate-700 hover:border-emerald-500/60 rounded-xl transition-all shadow-2xs focus:outline-none cursor-pointer"
                                :class="openExport ? 'border-emerald-500 bg-white dark:bg-slate-800 text-emerald-600 dark:text-emerald-400' : ''">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                        </button>
                        <div x-show="openExport" 
                             x-transition:enter="transition ease-out duration-150"
                             x-transition:enter-start="opacity-0 translate-y-1 scale-95"
                             x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                             x-transition:leave="transition ease-in duration-100"
                             x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                             x-transition:leave-end="opacity-0 translate-y-1 scale-95"
                             style="display: none;" 
                             class="absolute right-0 mt-1.5 w-40 bg-white dark:bg-slate-800 rounded-2xl shadow-xl shadow-slate-900/20 border border-slate-200 dark:border-slate-700 p-1.5 z-50 backdrop-blur-md">
                            <a href="#" @click.prevent="exportChart('severityChart'); openExport = false" 
                               class="flex items-center gap-2 px-3 py-2 text-xs font-semibold text-slate-700 dark:text-slate-200 hover:bg-emerald-50 dark:hover:bg-emerald-950/60 hover:text-emerald-700 dark:hover:text-emerald-300 rounded-xl transition-colors">
                                <svg class="w-3.5 h-3.5 text-slate-400 dark:text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                <span>Download PNG</span>
                            </a>
                            <a href="#" @click.prevent="exportCSV('severity'); openExport = false" 
                               class="flex items-center gap-2 px-3 py-2 text-xs font-semibold text-slate-700 dark:text-slate-200 hover:bg-emerald-50 dark:hover:bg-emerald-950/60 hover:text-emerald-700 dark:hover:text-emerald-300 rounded-xl transition-colors">
                                <svg class="w-3.5 h-3.5 text-slate-400 dark:text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                <span>Download CSV</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="relative flex-1 w-full min-h-0"><canvas id="severityChart"></canvas></div>
        </div>
    </div>

            <!-- Row 1.4: Barangay Heatmap (Full Width) -->
            <!-- Row 5: Barangay Heatmap (Full Width) -->
    <div class="bg-white dark:bg-slate-800 rounded-3xl shadow-xs hover:shadow-sm transition-all border border-slate-200/90 dark:border-slate-700/80 p-6 sm:p-8 flex flex-col w-full" style="min-height: 400px;">
        <div class="flex justify-between items-center mb-6">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-2xl bg-slate-100 dark:bg-slate-700/70 text-slate-600 dark:text-slate-300 flex items-center justify-center border border-slate-200/80 dark:border-slate-600/60">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </div>
                <div>
                    <h3 x-data="{ showTooltip: false }" @mouseenter="showTooltip = true" @mouseleave="showTooltip = false"
                        class="text-lg font-bold text-slate-900 dark:text-white flex items-center gap-2 relative">
                        Barangay Heatmap
                        <svg class="w-3.5 h-3.5 text-slate-400 cursor-help" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        
                        <!-- Custom Tooltip -->
                        <div x-show="showTooltip" x-transition class="absolute z-50 bottom-full mb-2 left-0 w-48 p-2 bg-slate-950 text-white text-[10px] rounded-xl shadow-2xl pointer-events-none font-medium leading-tight border border-slate-800" style="display: none;">
                            Distribution of patients by their residence location within Silang.
                            <div class="absolute top-full left-4 border-[4px] border-transparent border-t-slate-950"></div>
                        </div>
                    </h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Patient distribution across Silang communities</p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <!-- Custom Timeframe Dropdown -->
                <div class="relative" x-data="{
                    open: false,
                    selected: '{{ $timeFilter }}',
                    options: {
                        'today': 'Today',
                        'weekly': 'This Week',
                        'monthly': 'This Month',
                        'yearly': 'This Year',
                        'all': 'All Time'
                    },
                    select(val) {
                        this.selected = val;
                        this.open = false;
                        updateChart('barangay', val);
                    }
                }" @click.outside="open = false">
                    <button type="button" @click="open = !open"
                            class="h-9 px-3.5 rounded-xl border border-slate-200/90 dark:border-slate-700 bg-white dark:bg-slate-900/80 text-slate-700 dark:text-slate-200 text-xs font-bold shadow-2xs hover:border-emerald-500/60 dark:hover:border-emerald-500/60 hover:bg-slate-50 dark:hover:bg-slate-800 focus:outline-none focus:border-emerald-500 transition-all flex items-center gap-2 cursor-pointer"
                            :class="open ? 'border-emerald-500 bg-white dark:bg-slate-800 text-emerald-600 dark:text-emerald-400' : ''">
                        <span x-text="options[selected] || 'This Month'"></span>
                        <svg class="w-3.5 h-3.5 text-slate-400 dark:text-slate-500 transition-transform duration-200"
                             :class="open ? 'rotate-180 text-emerald-500' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>

                    <div x-show="open"
                         x-transition:enter="transition ease-out duration-150"
                         x-transition:enter-start="opacity-0 translate-y-1 scale-95"
                         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                         x-transition:leave="transition ease-in duration-100"
                         x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                         x-transition:leave-end="opacity-0 translate-y-1 scale-95"
                         style="display: none;"
                         class="absolute right-0 z-50 mt-1.5 w-36 rounded-2xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 shadow-xl shadow-slate-900/20 p-1.5 backdrop-blur-md">
                            <template x-for="(label, key) in options" :key="key">
                            <button type="button" @click="select(key)"
                                    class="w-full px-3 py-2 rounded-xl text-xs font-semibold flex items-center justify-between transition-colors cursor-pointer text-left"
                                    :class="selected === key 
                                        ? 'bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 font-bold' 
                                        : 'text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-700/60'">
                                <span x-text="label"></span>
                                <svg x-show="selected === key" class="w-3.5 h-3.5 text-emerald-600 dark:text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path>
                                </svg>
                            </button>
                        </template>
                    </div>
                </div>

                <!-- Custom Export Dropdown -->
                <div class="relative" x-data="{ openExport: false }" @click.outside="openExport = false">
                    <button @click="openExport = !openExport" title="Export Chart" 
                            class="h-9 w-9 flex items-center justify-center bg-white dark:bg-slate-900/80 text-slate-600 dark:text-slate-300 hover:bg-emerald-50 dark:hover:bg-emerald-950/50 hover:text-emerald-600 dark:hover:text-emerald-400 border border-slate-200/90 dark:border-slate-700 hover:border-emerald-500/60 rounded-xl transition-all shadow-2xs focus:outline-none cursor-pointer"
                            :class="openExport ? 'border-emerald-500 bg-white dark:bg-slate-800 text-emerald-600 dark:text-emerald-400' : ''">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                    </button>
                    <div x-show="openExport" 
                         x-transition:enter="transition ease-out duration-150"
                         x-transition:enter-start="opacity-0 translate-y-1 scale-95"
                         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                         x-transition:leave="transition ease-in duration-100"
                         x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                         x-transition:leave-end="opacity-0 translate-y-1 scale-95"
                         style="display: none;" 
                         class="absolute right-0 mt-1.5 w-40 bg-white dark:bg-slate-800 rounded-2xl shadow-xl shadow-slate-900/20 border border-slate-200 dark:border-slate-700 p-1.5 z-50 backdrop-blur-md">
                        <a href="#" @click.prevent="exportChart('barangayChart'); openExport = false" 
                           class="flex items-center gap-2 px-3 py-2 text-xs font-semibold text-slate-700 dark:text-slate-200 hover:bg-emerald-50 dark:hover:bg-emerald-950/60 hover:text-emerald-700 dark:hover:text-emerald-300 rounded-xl transition-colors">
                            <svg class="w-3.5 h-3.5 text-slate-400 dark:text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            <span>Download PNG</span>
                        </a>
                        <a href="#" @click.prevent="exportCSV('barangay'); openExport = false" 
                           class="flex items-center gap-2 px-3 py-2 text-xs font-semibold text-slate-700 dark:text-slate-200 hover:bg-emerald-50 dark:hover:bg-emerald-950/60 hover:text-emerald-700 dark:hover:text-emerald-300 rounded-xl transition-colors">
                            <svg class="w-3.5 h-3.5 text-slate-400 dark:text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            <span>Download CSV</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div class="relative flex-1 w-full min-h-0 overflow-y-auto" id="barangayChartContainer">
            <canvas id="barangayChart" style="{{ empty($barangayData) || count($barangayData) === 0 ? 'display:none;' : '' }}"></canvas>
            <div id="barangayEmptyState" style="{{ empty($barangayData) || count($barangayData) === 0 ? 'display:flex;' : 'display:none;' }}" class="flex-col items-center justify-center py-12 text-center h-full">
                <div class="w-12 h-12 rounded-2xl bg-slate-100 dark:bg-slate-700/60 text-slate-400 dark:text-slate-500 flex items-center justify-center mb-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </div>
                <p class="text-xs font-bold text-slate-700 dark:text-slate-300">No Barangay Records in This Timeframe</p>
                <p class="text-[11px] text-slate-400 dark:text-slate-500 mt-1">Select a broader timeframe above (e.g. This Year or All Time) to view community distributions.</p>
            </div>
        </div>
    </div>
        </div>
    </div>


    <!-- ========================================================================= -->
    <!-- SECTION 2: FACILITY INFLUX & PATIENT TRAFFIC FLOW                        -->
    <!-- ========================================================================= -->
    <div id="section-facility-flow" class="pt-4">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 pb-3 border-b border-slate-200/80 dark:border-slate-800 mb-6">
            <div class="flex items-center gap-2.5">
                <h2 class="text-base sm:text-lg font-black text-slate-900 dark:text-white tracking-tight">
                    2. Facility Influx &amp; Hourly Rush-Hour Dynamics
                </h2>
            </div>
            <p class="text-xs text-slate-500 dark:text-slate-400">
                Temporal encounter volumes and peak arrival rush hours to forecast staffing needs
            </p>
        </div>

        <div class="space-y-8">
            <!-- Row 2.1: Visit Volume (Full Width) -->
            <!-- Row 1: Visit Volume (Full Width) -->
    <div class="bg-white dark:bg-slate-800 rounded-3xl shadow-xs hover:shadow-sm transition-all border border-slate-200/90 dark:border-slate-700/80 p-6 sm:p-8 flex flex-col w-full h-[520px]">
        <div class="flex justify-between items-center mb-6">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-2xl bg-slate-100 dark:bg-slate-700/70 text-slate-600 dark:text-slate-300 flex items-center justify-center border border-slate-200/80 dark:border-slate-600/60">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                </div>
                <div>
                    <h3 x-data="{ showTooltip: false }" @mouseenter="showTooltip = true" @mouseleave="showTooltip = false" 
                        class="text-lg font-bold text-slate-900 dark:text-white flex items-center gap-2 relative">
                        Visit Volume
                        <svg class="w-3.5 h-3.5 text-slate-400 cursor-help" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        
                        <!-- Custom Tooltip -->
                        <div x-show="showTooltip" x-transition class="absolute z-50 bottom-full mb-2 left-0 w-48 p-2 bg-slate-950 text-white text-[10px] rounded-xl shadow-2xl pointer-events-none font-medium leading-tight border border-slate-800" style="display: none;">
                            Tracks the total number of consultations recorded over the selected timeframe.
                            <div class="absolute top-full left-4 border-[4px] border-transparent border-t-slate-950"></div>
                        </div>
                    </h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Total patient consultations over time</p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <!-- Custom Timeframe Dropdown -->
                <div class="relative" x-data="{
                    open: false,
                    selected: '{{ $timeFilter }}',
                    options: {
                        'today': 'Today',
                        'weekly': 'This Week',
                        'monthly': 'This Month',
                        'yearly': 'This Year',
                        'all': 'All Time'
                    },
                    select(val) {
                        this.selected = val;
                        this.open = false;
                        updateChart('volume', val);
                    }
                }" @click.outside="open = false">
                    <button type="button" @click="open = !open"
                            class="h-9 px-3.5 rounded-xl border border-slate-200/90 dark:border-slate-700 bg-white dark:bg-slate-900/80 text-slate-700 dark:text-slate-200 text-xs font-bold shadow-2xs hover:border-emerald-500/60 dark:hover:border-emerald-500/60 hover:bg-slate-50 dark:hover:bg-slate-800 focus:outline-none focus:border-emerald-500 transition-all flex items-center gap-2 cursor-pointer"
                            :class="open ? 'border-emerald-500 bg-white dark:bg-slate-800 text-emerald-600 dark:text-emerald-400' : ''">
                        <span x-text="options[selected] || 'This Month'"></span>
                        <svg class="w-3.5 h-3.5 text-slate-400 dark:text-slate-500 transition-transform duration-200"
                             :class="open ? 'rotate-180 text-emerald-500' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>

                    <!-- Custom Floating Menu -->
                    <div x-show="open"
                         x-transition:enter="transition ease-out duration-150"
                         x-transition:enter-start="opacity-0 translate-y-1 scale-95"
                         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                         x-transition:leave="transition ease-in duration-100"
                         x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                         x-transition:leave-end="opacity-0 translate-y-1 scale-95"
                         style="display: none;"
                         class="absolute right-0 z-50 mt-1.5 w-36 rounded-2xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 shadow-xl shadow-slate-900/20 p-1.5 backdrop-blur-md">
                        <template x-for="(label, key) in options" :key="key">
                            <button type="button" @click="select(key)"
                                    class="w-full px-3 py-2 rounded-xl text-xs font-semibold flex items-center justify-between transition-colors cursor-pointer text-left"
                                    :class="selected === key 
                                        ? 'bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 font-bold' 
                                        : 'text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-700/60'">
                                <span x-text="label"></span>
                                <svg x-show="selected === key" class="w-3.5 h-3.5 text-emerald-600 dark:text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path>
                                </svg>
                            </button>
                        </template>
                    </div>
                </div>

                <!-- Custom Export Dropdown -->
                <div class="relative" x-data="{ openExport: false }" @click.outside="openExport = false">
                    <button @click="openExport = !openExport" title="Export Chart" 
                            class="h-9 w-9 flex items-center justify-center bg-white dark:bg-slate-900/80 text-slate-600 dark:text-slate-300 hover:bg-emerald-50 dark:hover:bg-emerald-950/50 hover:text-emerald-600 dark:hover:text-emerald-400 border border-slate-200/90 dark:border-slate-700 hover:border-emerald-500/60 rounded-xl transition-all shadow-2xs focus:outline-none cursor-pointer"
                            :class="openExport ? 'border-emerald-500 bg-white dark:bg-slate-800 text-emerald-600 dark:text-emerald-400' : ''">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                    </button>
                    <div x-show="openExport" 
                         x-transition:enter="transition ease-out duration-150"
                         x-transition:enter-start="opacity-0 translate-y-1 scale-95"
                         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                         x-transition:leave="transition ease-in duration-100"
                         x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                         x-transition:leave-end="opacity-0 translate-y-1 scale-95"
                         style="display: none;" 
                         class="absolute right-0 mt-1.5 w-40 bg-white dark:bg-slate-800 rounded-2xl shadow-xl shadow-slate-900/20 border border-slate-200 dark:border-slate-700 p-1.5 z-50 backdrop-blur-md">
                        <a href="#" @click.prevent="exportChart('visitVolumeChart'); openExport = false" 
                           class="flex items-center gap-2 px-3 py-2 text-xs font-semibold text-slate-700 dark:text-slate-200 hover:bg-emerald-50 dark:hover:bg-emerald-950/60 hover:text-emerald-700 dark:hover:text-emerald-300 rounded-xl transition-colors">
                            <svg class="w-3.5 h-3.5 text-slate-400 dark:text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            <span>Download PNG</span>
                        </a>
                        <a href="#" @click.prevent="exportCSV('volume'); openExport = false" 
                           class="flex items-center gap-2 px-3 py-2 text-xs font-semibold text-slate-700 dark:text-slate-200 hover:bg-emerald-50 dark:hover:bg-emerald-950/60 hover:text-emerald-700 dark:hover:text-emerald-300 rounded-xl transition-colors">
                            <svg class="w-3.5 h-3.5 text-slate-400 dark:text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            <span>Download CSV</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div class="relative flex-1 w-full min-h-0">
            <canvas id="visitVolumeChart"></canvas>
        </div>
    </div>

            <!-- Row 2.2: Peak Operating Hours (Full Width) -->
            <div class="bg-white dark:bg-slate-800 rounded-3xl shadow-xs hover:shadow-sm transition-all border border-slate-200/90 dark:border-slate-700/80 p-6 sm:p-8 flex flex-col w-full h-[450px]">
            <div class="flex justify-between items-center mb-6">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-slate-100 dark:bg-slate-700/70 text-slate-600 dark:text-slate-300 flex items-center justify-center border border-slate-200/80 dark:border-slate-600/60">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <div>
                        <h3 x-data="{ showTooltip: false }" @mouseenter="showTooltip = true" @mouseleave="showTooltip = false"
                            class="text-lg font-bold text-slate-900 dark:text-white flex items-center gap-2 relative">
                            Peak Hours
                            <svg class="w-3.5 h-3.5 text-slate-400 cursor-help" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            
                            <!-- Custom Tooltip -->
                            <div x-show="showTooltip" x-transition class="absolute z-50 bottom-full mb-2 left-0 w-48 p-2 bg-slate-950 text-white text-[10px] rounded-xl shadow-2xl pointer-events-none font-medium leading-tight border border-slate-800" style="display: none;">
                                Visualizes the busiest times of day based on consultation start times.
                                <div class="absolute top-full left-4 border-[4px] border-transparent border-t-slate-950"></div>
                            </div>
                        </h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400">Consultation distribution by hour</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <!-- Custom Timeframe Dropdown -->
                    <div class="relative" x-data="{
                        open: false,
                        selected: '{{ $timeFilter }}',
                        options: {
                            'today': 'Today',
                            'weekly': 'This Week',
                            'monthly': 'This Month',
                            'yearly': 'This Year',
                            'all': 'All Time'
                        },
                        select(val) {
                            this.selected = val;
                            this.open = false;
                            updateChart('peak', val);
                        }
                    }" @click.outside="open = false">
                        <button type="button" @click="open = !open"
                                class="h-9 px-3.5 rounded-xl border border-slate-200/90 dark:border-slate-700 bg-white dark:bg-slate-900/80 text-slate-700 dark:text-slate-200 text-xs font-bold shadow-2xs hover:border-emerald-500/60 dark:hover:border-emerald-500/60 hover:bg-slate-50 dark:hover:bg-slate-800 focus:outline-none focus:border-emerald-500 transition-all flex items-center gap-2 cursor-pointer"
                                :class="open ? 'border-emerald-500 bg-white dark:bg-slate-800 text-emerald-600 dark:text-emerald-400' : ''">
                            <span x-text="options[selected] || 'This Month'"></span>
                            <svg class="w-3.5 h-3.5 text-slate-400 dark:text-slate-500 transition-transform duration-200"
                                 :class="open ? 'rotate-180 text-emerald-500' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>

                        <div x-show="open"
                             x-transition:enter="transition ease-out duration-150"
                             x-transition:enter-start="opacity-0 translate-y-1 scale-95"
                             x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                             x-transition:leave="transition ease-in duration-100"
                             x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                             x-transition:leave-end="opacity-0 translate-y-1 scale-95"
                             style="display: none;"
                             class="absolute right-0 z-50 mt-1.5 w-36 rounded-2xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 shadow-xl shadow-slate-900/20 p-1.5 backdrop-blur-md">
                            <template x-for="(label, key) in options" :key="key">
                                <button type="button" @click="select(key)"
                                        class="w-full px-3 py-2 rounded-xl text-xs font-semibold flex items-center justify-between transition-colors cursor-pointer text-left"
                                        :class="selected === key 
                                            ? 'bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 font-bold' 
                                            : 'text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-700/60'">
                                    <span x-text="label"></span>
                                    <svg x-show="selected === key" class="w-3.5 h-3.5 text-emerald-600 dark:text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                </button>
                            </template>
                        </div>
                    </div>

                    <!-- Custom Export Dropdown -->
                    <div class="relative" x-data="{ openExport: false }" @click.outside="openExport = false">
                        <button @click="openExport = !openExport" title="Export Chart" 
                                class="h-9 w-9 flex items-center justify-center bg-white dark:bg-slate-900/80 text-slate-600 dark:text-slate-300 hover:bg-emerald-50 dark:hover:bg-emerald-950/50 hover:text-emerald-600 dark:hover:text-emerald-400 border border-slate-200/90 dark:border-slate-700 hover:border-emerald-500/60 rounded-xl transition-all shadow-2xs focus:outline-none cursor-pointer"
                                :class="openExport ? 'border-emerald-500 bg-white dark:bg-slate-800 text-emerald-600 dark:text-emerald-400' : ''">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                        </button>
                        <div x-show="openExport" 
                             x-transition:enter="transition ease-out duration-150"
                             x-transition:enter-start="opacity-0 translate-y-1 scale-95"
                             x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                             x-transition:leave="transition ease-in duration-100"
                             x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                             x-transition:leave-end="opacity-0 translate-y-1 scale-95"
                             style="display: none;" 
                             class="absolute right-0 mt-1.5 w-40 bg-white dark:bg-slate-800 rounded-2xl shadow-xl shadow-slate-900/20 border border-slate-200 dark:border-slate-700 p-1.5 z-50 backdrop-blur-md">
                            <a href="#" @click.prevent="exportChart('peakHoursChart'); openExport = false" 
                               class="flex items-center gap-2 px-3 py-2 text-xs font-semibold text-slate-700 dark:text-slate-200 hover:bg-emerald-50 dark:hover:bg-emerald-950/60 hover:text-emerald-700 dark:hover:text-emerald-300 rounded-xl transition-colors">
                                <svg class="w-3.5 h-3.5 text-slate-400 dark:text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                <span>Download PNG</span>
                            </a>
                            <a href="#" @click.prevent="exportCSV('peak'); openExport = false" 
                               class="flex items-center gap-2 px-3 py-2 text-xs font-semibold text-slate-700 dark:text-slate-200 hover:bg-emerald-50 dark:hover:bg-emerald-950/60 hover:text-emerald-700 dark:hover:text-emerald-300 rounded-xl transition-colors">
                                <svg class="w-3.5 h-3.5 text-slate-400 dark:text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                <span>Download CSV</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="relative flex-1 w-full min-h-0"><canvas id="peakHoursChart"></canvas></div>
        </div>
        </div>
    </div>


    <!-- ========================================================================= -->
    <!-- SECTION 3: CLINICAL PERSONNEL PERFORMANCE & OPERATIONAL HR TELEMETRY      -->
    <!-- ========================================================================= -->
    <div id="section-staff-telemetry" class="pt-4">
        <div class="p-4 sm:p-5 rounded-3xl bg-amber-50/70 dark:bg-amber-950/20 border border-amber-200/80 dark:border-amber-800/40 mb-6">
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-2.5">
                <div class="flex items-center gap-2.5">
                    <h2 class="text-base sm:text-lg font-black text-slate-900 dark:text-white tracking-tight">
                        3. Clinical Personnel Productivity, Workload &amp; Front-Line Efficiency
                    </h2>
                </div>
                <span class="text-[11px] font-bold text-amber-700 dark:text-amber-400 shrink-0">Administrative Telemetry</span>
            </div>
            <p class="text-xs text-amber-900/80 dark:text-amber-300/80 mt-2 leading-relaxed">
                <strong>Administrative Notice:</strong> The metrics in this section evaluate internal provider throughput, physician consultation duration, nurse triage encoding speed, and queue wait times. These are administrative operational telemetry figures, isolated here so they are not confused with patient public health epidemiology.
            </p>
        </div>

        <div class="space-y-8">
            <!-- Row 3.1: Staff Workload Distribution (Full Width) -->
            <div class="bg-white dark:bg-slate-800 rounded-3xl shadow-xs hover:shadow-sm transition-all border border-slate-200/90 dark:border-slate-700/80 p-6 sm:p-8 flex flex-col w-full h-[450px]">
            <div class="flex justify-between items-center mb-6">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-slate-100 dark:bg-slate-700/70 text-slate-600 dark:text-slate-300 flex items-center justify-center border border-slate-200/80 dark:border-slate-600/60">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    </div>
                    <div>
                        <h3 x-data="{ showTooltip: false }" @mouseenter="showTooltip = true" @mouseleave="showTooltip = false"
                            class="text-lg font-bold text-slate-900 dark:text-white flex items-center gap-2 relative">
                            Staff Workload
                            <svg class="w-3.5 h-3.5 text-slate-400 cursor-help" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            
                            <!-- Custom Tooltip -->
                            <div x-show="showTooltip" x-transition class="absolute z-50 bottom-full mb-2 left-0 w-48 p-2 bg-slate-950 text-white text-[10px] rounded-xl shadow-2xl pointer-events-none font-medium leading-tight border border-slate-800" style="display: none;">
                                Shows the distribution of patient cases across different doctors and nurses.
                                <div class="absolute top-full left-4 border-[4px] border-transparent border-t-slate-950"></div>
                            </div>
                        </h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400">Consultations per doctor/nurse</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <!-- Custom Timeframe Dropdown -->
                    <div class="relative" x-data="{
                        open: false,
                        selected: '{{ $timeFilter }}',
                        options: {
                            'today': 'Today',
                            'weekly': 'This Week',
                            'monthly': 'This Month',
                            'yearly': 'This Year',
                            'all': 'All Time'
                        },
                        select(val) {
                            this.selected = val;
                            this.open = false;
                            updateChart('workload', val);
                        }
                    }" @click.outside="open = false">
                        <button type="button" @click="open = !open"
                                class="h-9 px-3.5 rounded-xl border border-slate-200/90 dark:border-slate-700 bg-white dark:bg-slate-900/80 text-slate-700 dark:text-slate-200 text-xs font-bold shadow-2xs hover:border-emerald-500/60 dark:hover:border-emerald-500/60 hover:bg-slate-50 dark:hover:bg-slate-800 focus:outline-none focus:border-emerald-500 transition-all flex items-center gap-2 cursor-pointer"
                                :class="open ? 'border-emerald-500 bg-white dark:bg-slate-800 text-emerald-600 dark:text-emerald-400' : ''">
                            <span x-text="options[selected] || 'This Month'"></span>
                            <svg class="w-3.5 h-3.5 text-slate-400 dark:text-slate-500 transition-transform duration-200"
                                 :class="open ? 'rotate-180 text-emerald-500' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>

                        <div x-show="open"
                             x-transition:enter="transition ease-out duration-150"
                             x-transition:enter-start="opacity-0 translate-y-1 scale-95"
                             x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                             x-transition:leave="transition ease-in duration-100"
                             x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                             x-transition:leave-end="opacity-0 translate-y-1 scale-95"
                             style="display: none;"
                             class="absolute right-0 z-50 mt-1.5 w-36 rounded-2xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 shadow-xl shadow-slate-900/20 p-1.5 backdrop-blur-md">
                            <template x-for="(label, key) in options" :key="key">
                                <button type="button" @click="select(key)"
                                        class="w-full px-3 py-2 rounded-xl text-xs font-semibold flex items-center justify-between transition-colors cursor-pointer text-left"
                                        :class="selected === key 
                                            ? 'bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 font-bold' 
                                            : 'text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-700/60'">
                                    <span x-text="label"></span>
                                    <svg x-show="selected === key" class="w-3.5 h-3.5 text-emerald-600 dark:text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                </button>
                            </template>
                        </div>
                    </div>

                    <!-- Custom Export Dropdown -->
                    <div class="relative" x-data="{ openExport: false }" @click.outside="openExport = false">
                        <button @click="openExport = !openExport" title="Export Chart" 
                                class="h-9 w-9 flex items-center justify-center bg-white dark:bg-slate-900/80 text-slate-600 dark:text-slate-300 hover:bg-emerald-50 dark:hover:bg-emerald-950/50 hover:text-emerald-600 dark:hover:text-emerald-400 border border-slate-200/90 dark:border-slate-700 hover:border-emerald-500/60 rounded-xl transition-all shadow-2xs focus:outline-none cursor-pointer"
                                :class="openExport ? 'border-emerald-500 bg-white dark:bg-slate-800 text-emerald-600 dark:text-emerald-400' : ''">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                        </button>
                        <div x-show="openExport" 
                             x-transition:enter="transition ease-out duration-150"
                             x-transition:enter-start="opacity-0 translate-y-1 scale-95"
                             x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                             x-transition:leave="transition ease-in duration-100"
                             x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                             x-transition:leave-end="opacity-0 translate-y-1 scale-95"
                             style="display: none;" 
                             class="absolute right-0 mt-1.5 w-40 bg-white dark:bg-slate-800 rounded-2xl shadow-xl shadow-slate-900/20 border border-slate-200 dark:border-slate-700 p-1.5 z-50 backdrop-blur-md">
                            <a href="#" @click.prevent="exportChart('workloadChart'); openExport = false" 
                               class="flex items-center gap-2 px-3 py-2 text-xs font-semibold text-slate-700 dark:text-slate-200 hover:bg-emerald-50 dark:hover:bg-emerald-950/60 hover:text-emerald-700 dark:hover:text-emerald-300 rounded-xl transition-colors">
                                <svg class="w-3.5 h-3.5 text-slate-400 dark:text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                <span>Download PNG</span>
                            </a>
                            <a href="#" @click.prevent="exportCSV('workload'); openExport = false" 
                               class="flex items-center gap-2 px-3 py-2 text-xs font-semibold text-slate-700 dark:text-slate-200 hover:bg-emerald-50 dark:hover:bg-emerald-950/60 hover:text-emerald-700 dark:hover:text-emerald-300 rounded-xl transition-colors">
                                <svg class="w-3.5 h-3.5 text-slate-400 dark:text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                <span>Download CSV</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="relative flex-1 w-full min-h-0"><canvas id="workloadChart"></canvas></div>
        </div>

            <!-- Row 3.2: Staff Productivity & Front-Line Efficiency Panel -->
            <!-- Row 6: Staff Productivity -->
    <div class="bg-white dark:bg-slate-800 rounded-3xl shadow-xs hover:shadow-sm border border-slate-200/90 dark:border-slate-700/80 p-6 sm:p-8 flex flex-col w-full mt-8 transition-all">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-2xl bg-slate-100 dark:bg-slate-700/70 text-slate-600 dark:text-slate-300 flex items-center justify-center border border-slate-200/80 dark:border-slate-600/60 shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <h3 class="text-base sm:text-lg font-black text-slate-900 dark:text-white tracking-tight">Staff Productivity</h3>
                        <div x-data="{ showTooltip: false }" class="relative inline-flex items-center">
                            <button @mouseenter="showTooltip = true" @mouseleave="showTooltip = false" type="button" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-300">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </button>
                            <div x-show="showTooltip" x-transition class="absolute z-50 bottom-full mb-2 left-1/2 -translate-x-1/2 w-64 p-3 bg-slate-900 text-white text-[10px] rounded-xl shadow-xl pointer-events-none font-medium leading-relaxed" style="display: none;">
                                Aggregate performance data measuring how quickly and effectively staff process patient records and consultations.
                                <div class="absolute top-full left-1/2 -translate-x-1/2 border-[6px] border-transparent border-t-slate-900"></div>
                            </div>
                        </div>
                    </div>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Clinical performance metrics, throughput, and operational efficiency</p>
                </div>
            </div>
            <div class="flex flex-wrap items-center gap-2.5">
                <!-- Custom Staff Selector Dropdown with Live Search -->
                <div class="relative" x-data="{
                    open: false,
                    search: '',
                    staffList: [
                        { id: 'all', name: 'All Staff' },
                        @foreach($staffList as $staff)
                            { id: '{{ $staff->id }}', name: '{{ addslashes($staff->formatted_name) }}' },
                        @endforeach
                    ],
                    get filteredStaff() {
                        if (!this.search.trim()) return this.staffList;
                        const q = this.search.toLowerCase().trim();
                        return this.staffList.filter(s => s.name.toLowerCase().includes(q));
                    },
                    get selectedLabel() {
                        const found = this.staffList.find(s => String(s.id) === String(productivityStaff));
                        return found ? found.name : 'Select Staff';
                    },
                    select(val) {
                        productivityStaff = val;
                        this.open = false;
                        this.search = '';
                        fetchProductivity();
                    }
                }" @click.outside="open = false; search = ''">
                    <button type="button" 
                            @click="open = !open; if(open) $nextTick(() => $refs.staffSearchInput?.focus())"
                            class="h-9 px-3.5 rounded-xl border border-slate-200/90 dark:border-slate-700 bg-white dark:bg-slate-900/80 text-slate-700 dark:text-slate-200 text-xs font-bold shadow-2xs hover:border-emerald-500/60 dark:hover:border-emerald-500/60 hover:bg-slate-50 dark:hover:bg-slate-800 focus:outline-none focus:border-emerald-500 transition-all flex items-center gap-2.5 cursor-pointer"
                            :class="open ? 'border-emerald-500 bg-white dark:bg-slate-800 text-emerald-600 dark:text-emerald-400 ring-2 ring-emerald-500/10' : ''">
                        <span class="max-w-[150px] sm:max-w-[200px] truncate" x-text="selectedLabel"></span>
                        <svg class="w-3.5 h-3.5 text-slate-400 dark:text-slate-500 transition-transform duration-200 shrink-0"
                             :class="open ? 'rotate-180 text-emerald-500' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>

                    <!-- Dropdown Panel with Search -->
                    <div x-show="open"
                         x-transition:enter="transition ease-out duration-150"
                         x-transition:enter-start="opacity-0 translate-y-1 scale-95"
                         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                         x-transition:leave="transition ease-in duration-100"
                         x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                         x-transition:leave-end="opacity-0 translate-y-1 scale-95"
                         style="display: none;"
                         class="absolute right-0 z-50 mt-1.5 w-64 sm:w-72 rounded-2xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 shadow-xl shadow-slate-900/20 overflow-hidden backdrop-blur-md">
                        
                        <!-- Search Bar Header -->
                        <div class="p-2.5 border-b border-slate-100 dark:border-slate-700/80 bg-slate-50/70 dark:bg-slate-800/80">
                            <div class="relative flex items-center">
                                <svg class="w-3.5 h-3.5 text-slate-400 absolute left-2.5 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                                <input type="text"
                                       x-model="search"
                                       x-ref="staffSearchInput"
                                       @keydown.escape="open = false; search = ''"
                                       placeholder="Search staff by name..."
                                       class="w-full pl-8 pr-7 py-1.5 text-xs bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-800 dark:text-slate-100 placeholder-slate-400 focus:outline-none focus:border-emerald-500 transition-all">
                                <button x-show="search.length > 0" 
                                        @click="search = ''; $refs.staffSearchInput.focus()" 
                                        type="button" 
                                        class="absolute right-2 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 p-0.5 cursor-pointer">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <!-- Scrollable Staff Options List -->
                        <div class="max-h-60 overflow-y-auto p-1.5 space-y-0.5 custom-scrollbar">
                            <template x-for="item in filteredStaff" :key="item.id">
                                <button type="button" @click="select(item.id)"
                                        class="w-full px-3 py-2 rounded-xl text-xs font-semibold flex items-center justify-between transition-colors cursor-pointer text-left"
                                        :class="String(productivityStaff) === String(item.id) 
                                            ? 'bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 font-bold' 
                                            : 'text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-700/60'">
                                    <span class="truncate" x-text="item.name"></span>
                                    <svg x-show="String(productivityStaff) === String(item.id)" class="w-3.5 h-3.5 text-emerald-600 dark:text-emerald-400 shrink-0 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                </button>
                            </template>

                            <!-- Empty Search Feedback -->
                            <div x-show="filteredStaff.length === 0" class="py-5 px-3 text-center text-xs text-slate-400 dark:text-slate-500">
                                <svg class="w-6 h-6 mx-auto mb-1.5 text-slate-300 dark:text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                                No staff matching "<span x-text="search" class="font-bold text-slate-700 dark:text-slate-300"></span>"
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Custom Productivity Timeframe Dropdown -->
                <div class="relative" x-data="{
                    open: false,
                    options: {
                        'today': 'Today',
                        'weekly': 'This Week',
                        'monthly': 'This Month',
                        'yearly': 'This Year',
                        'all': 'All Time'
                    },
                    select(val) {
                        productivityTime = val;
                        this.open = false;
                        fetchProductivity();
                    }
                }" @click.outside="open = false">
                    <button type="button" @click="open = !open"
                            class="h-9 px-3.5 rounded-xl border border-slate-200/90 dark:border-slate-700 bg-white dark:bg-slate-900/80 text-slate-700 dark:text-slate-200 text-xs font-bold shadow-2xs hover:border-emerald-500/60 dark:hover:border-emerald-500/60 hover:bg-slate-50 dark:hover:bg-slate-800 focus:outline-none focus:border-emerald-500 transition-all flex items-center gap-2.5 cursor-pointer"
                            :class="open ? 'border-emerald-500 bg-white dark:bg-slate-800 text-emerald-600 dark:text-emerald-400 ring-2 ring-emerald-500/10' : ''">
                        <span x-text="options[productivityTime] || 'This Month'"></span>
                        <svg class="w-3.5 h-3.5 text-slate-400 dark:text-slate-500 transition-transform duration-200"
                             :class="open ? 'rotate-180 text-emerald-500' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div x-show="open"
                         x-transition:enter="transition ease-out duration-150"
                         x-transition:enter-start="opacity-0 translate-y-1 scale-95"
                         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                         x-transition:leave="transition ease-in duration-100"
                         x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                         x-transition:leave-end="opacity-0 translate-y-1 scale-95"
                         style="display: none;"
                         class="absolute right-0 z-50 mt-1.5 w-36 rounded-2xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 shadow-xl shadow-slate-900/20 p-1.5 backdrop-blur-md">
                        <template x-for="(label, key) in options" :key="key">
                            <button type="button" @click="select(key)"
                                    class="w-full px-3 py-2 rounded-xl text-xs font-semibold flex items-center justify-between transition-colors cursor-pointer text-left"
                                    :class="productivityTime === key 
                                        ? 'bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 font-bold' 
                                        : 'text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-700/60'">
                                <span x-text="label"></span>
                                <svg x-show="productivityTime === key" class="w-3.5 h-3.5 text-emerald-600 dark:text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path>
                                </svg>
                            </button>
                        </template>
                    </div>
                </div>
            </div>
        </div>

        <!-- Segmented Tab Switcher -->
        <div class="inline-flex p-1 rounded-2xl bg-slate-100 dark:bg-slate-800/80 border border-slate-200/60 dark:border-slate-700/60 gap-1 mb-6 self-start">
            <button type="button" @click="productivityTab = 'clinical'"
                    class="px-4 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-2 cursor-pointer"
                    :class="productivityTab === 'clinical' 
                        ? 'bg-white dark:bg-slate-700 text-slate-900 dark:text-white shadow-xs' 
                        : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-200'">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                Clinical Performance (Doctors/Nurses)
            </button>
            <button type="button" @click="productivityTab = 'triage'"
                    class="px-4 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-2 cursor-pointer"
                    :class="productivityTab === 'triage' 
                        ? 'bg-white dark:bg-slate-700 text-slate-900 dark:text-white shadow-xs' 
                        : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-200'">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                Front-Line Efficiency (Triage/Info Desk)
            </button>
        </div>

        <!-- KPI Cards -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
            <!-- Clinical KPI Cards -->
            <template x-if="productivityTab === 'clinical'">
                <div class="bg-slate-50/70 dark:bg-slate-800/70 rounded-2xl p-4 border border-slate-200/80 dark:border-slate-700/80 flex flex-col justify-between hover:border-slate-300 dark:hover:border-slate-600 transition-all">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Avg Duration</span>
                        <div class="w-7 h-7 rounded-xl bg-slate-200/70 dark:bg-slate-700 text-slate-600 dark:text-slate-300 flex items-center justify-center shrink-0">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                    </div>
                    <div class="flex items-baseline gap-1.5">
                        <span class="text-2xl font-black text-slate-900 dark:text-white tracking-tight" x-text="productivityData.avgDuration">{{ $staffProductivity['avgDuration'] }}</span>
                        <span class="text-xs font-semibold text-slate-500 dark:text-slate-400">min / patient</span>
                    </div>
                </div>
            </template>
            <template x-if="productivityTab === 'clinical'">
                <div class="bg-slate-50/70 dark:bg-slate-800/70 rounded-2xl p-4 border border-slate-200/80 dark:border-slate-700/80 flex flex-col justify-between hover:border-slate-300 dark:hover:border-slate-600 transition-all">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Total Patients</span>
                        <div class="w-7 h-7 rounded-xl bg-slate-200/70 dark:bg-slate-700 text-slate-600 dark:text-slate-300 flex items-center justify-center shrink-0">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                        </div>
                    </div>
                    <div class="flex items-baseline gap-1.5">
                        <span class="text-2xl font-black text-slate-900 dark:text-white tracking-tight" x-text="productivityData.totalPatients">{{ $staffProductivity['totalPatients'] }}</span>
                        <span class="text-xs font-semibold text-slate-500 dark:text-slate-400">completed</span>
                    </div>
                </div>
            </template>
            <template x-if="productivityTab === 'clinical'">
                <div class="bg-slate-50/70 dark:bg-slate-800/70 rounded-2xl p-4 border border-slate-200/80 dark:border-slate-700/80 flex flex-col justify-between hover:border-slate-300 dark:hover:border-slate-600 transition-all">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Throughput</span>
                        <div class="w-7 h-7 rounded-xl bg-slate-200/70 dark:bg-slate-700 text-slate-600 dark:text-slate-300 flex items-center justify-center shrink-0">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                        </div>
                    </div>
                    <div class="flex items-baseline gap-1.5">
                        <span class="text-2xl font-black text-slate-900 dark:text-white tracking-tight" x-text="productivityData.patientsPerHour">{{ $staffProductivity['patientsPerHour'] }}</span>
                        <span class="text-xs font-semibold text-slate-500 dark:text-slate-400">patients / hr</span>
                    </div>
                </div>
            </template>
            <template x-if="productivityTab === 'clinical'">
                <div class="bg-slate-50/70 dark:bg-slate-800/70 rounded-2xl p-4 border border-slate-200/80 dark:border-slate-700/80 flex flex-col justify-between hover:border-slate-300 dark:hover:border-slate-600 transition-all">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Cancellation Rate</span>
                        <div class="w-7 h-7 rounded-xl bg-slate-200/70 dark:bg-slate-700 text-slate-600 dark:text-slate-300 flex items-center justify-center shrink-0">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                    </div>
                    <div class="flex items-baseline gap-1.5">
                        <span class="text-2xl font-black text-slate-900 dark:text-white tracking-tight" x-text="productivityData.cancellationRate + '%'">{{ $staffProductivity['cancellationRate'] }}%</span>
                        <span class="text-xs font-semibold text-slate-500 dark:text-slate-400">rate</span>
                    </div>
                </div>
            </template>

            <!-- Triage KPI Cards -->
            <template x-if="productivityTab === 'triage'">
                <div class="bg-slate-50/70 dark:bg-slate-800/70 rounded-2xl p-4 border border-slate-200/80 dark:border-slate-700/80 flex flex-col justify-between hover:border-slate-300 dark:hover:border-slate-600 transition-all col-span-2 sm:col-span-1">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Avg Queue Wait (Triage &rarr; MD)</span>
                        <div class="w-7 h-7 rounded-xl bg-slate-200/70 dark:bg-slate-700 text-slate-600 dark:text-slate-300 flex items-center justify-center shrink-0">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                    </div>
                    <div class="flex items-baseline gap-1.5">
                        <span class="text-2xl font-black text-slate-900 dark:text-white tracking-tight" x-text="productivityData.avgWaitTime">{{ $staffProductivity['avgWaitTime'] }}</span>
                        <span class="text-xs font-semibold text-slate-500 dark:text-slate-400">min in queue</span>
                    </div>
                    <p class="text-[10px] text-slate-400 dark:text-slate-500 mt-1 leading-tight">
                        Triage completion to physician examination (queue duration, not door arrival).
                    </p>
                </div>
            </template>
        </div>

        <!-- Charts Area -->
        <div class="h-[400px]">
            <!-- Clinical Chart -->
            <div x-show="productivityTab === 'clinical'" class="flex flex-col h-full w-full">
                <div class="flex justify-between items-center mb-4">
                    <div class="flex items-center gap-2">
                        <h4 class="text-xs sm:text-sm font-bold text-slate-700 dark:text-slate-200">Avg Consultation Duration (mins) per Provider</h4>
                        <div x-data="{ showTooltip: false }" class="relative inline-flex items-center">
                            <button @mouseenter="showTooltip = true" @mouseleave="showTooltip = false" type="button" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-300">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </button>
                            <div x-show="showTooltip" x-transition class="absolute z-50 bottom-full mb-2 left-0 w-48 p-2.5 bg-slate-900 text-white text-[10px] rounded-xl shadow-xl pointer-events-none font-medium leading-tight" style="display: none;">
                                The mean time spent in actual consultation per patient.
                                <div class="absolute top-full left-4 border-[4px] border-transparent border-t-slate-900"></div>
                            </div>
                        </div>
                    </div>
                    <div class="relative" x-data="{ openExport: false }" @click.outside="openExport = false">
                        <button @click="openExport = !openExport" title="Export Chart" 
                                class="h-8 w-8 flex items-center justify-center bg-white dark:bg-slate-900/80 text-slate-600 dark:text-slate-300 hover:bg-emerald-50 dark:hover:bg-emerald-950/50 hover:text-emerald-600 dark:hover:text-emerald-400 border border-slate-200/90 dark:border-slate-700 hover:border-emerald-500/60 rounded-xl transition-all shadow-2xs focus:outline-none focus:ring-4 focus:ring-emerald-500/20 cursor-pointer"
                                :class="openExport ? 'border-emerald-500 ring-4 ring-emerald-500/20 bg-white dark:bg-slate-800 text-emerald-600 dark:text-emerald-400' : ''">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                        </button>
                        <div x-show="openExport" 
                             x-transition:enter="transition ease-out duration-150"
                             x-transition:enter-start="opacity-0 translate-y-1 scale-95"
                             x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                             x-transition:leave="transition ease-in duration-100"
                             x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                             x-transition:leave-end="opacity-0 translate-y-1 scale-95"
                             style="display: none;" 
                             class="absolute right-0 mt-1.5 w-36 bg-white dark:bg-slate-800 rounded-2xl shadow-xl shadow-slate-900/20 border border-slate-200 dark:border-slate-700 p-1 z-50 backdrop-blur-md">
                            <a href="#" @click.prevent="exportChart('durationStaffChart'); openExport = false" 
                               class="flex items-center gap-2 px-3 py-1.5 text-xs font-semibold text-slate-700 dark:text-slate-200 hover:bg-emerald-50 dark:hover:bg-emerald-950/60 hover:text-emerald-700 dark:hover:text-emerald-300 rounded-xl transition-colors">
                                <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                <span>Download PNG</span>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="relative flex-1 w-full min-h-0"><canvas id="durationStaffChart"></canvas></div>
            </div>

            <!-- Triage Chart -->
            <div x-show="productivityTab === 'triage'" class="flex flex-col h-full w-full">
                <div class="flex justify-between items-center mb-4">
                    <div class="flex items-center gap-2">
                        <h4 class="text-xs sm:text-sm font-bold text-slate-700 dark:text-slate-200">Triage Encoding Speed (seconds) per Nurse</h4>
                        <div x-data="{ showTooltip: false }" class="relative inline-flex items-center">
                            <button @mouseenter="showTooltip = true" @mouseleave="showTooltip = false" type="button" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-300">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </button>
                            <div x-show="showTooltip" x-transition class="absolute z-50 bottom-full mb-2 left-0 w-48 p-2.5 bg-slate-900 text-white text-[10px] rounded-xl shadow-xl pointer-events-none font-medium leading-tight" style="display: none;">
                                Average time taken by triage nurses to input patient vital signs and symptoms.
                                <div class="absolute top-full left-4 border-[4px] border-transparent border-t-slate-900"></div>
                            </div>
                        </div>
                    </div>
                    <div class="relative" x-data="{ openExport: false }" @click.outside="openExport = false">
                        <button @click="openExport = !openExport" title="Export Chart" 
                                class="h-8 w-8 flex items-center justify-center bg-white dark:bg-slate-900/80 text-slate-600 dark:text-slate-300 hover:bg-emerald-50 dark:hover:bg-emerald-950/50 hover:text-emerald-600 dark:hover:text-emerald-400 border border-slate-200/90 dark:border-slate-700 hover:border-emerald-500/60 rounded-xl transition-all shadow-2xs focus:outline-none focus:ring-4 focus:ring-emerald-500/20 cursor-pointer"
                                :class="openExport ? 'border-emerald-500 ring-4 ring-emerald-500/20 bg-white dark:bg-slate-800 text-emerald-600 dark:text-emerald-400' : ''">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                        </button>
                        <div x-show="openExport" 
                             x-transition:enter="transition ease-out duration-150"
                             x-transition:enter-start="opacity-0 translate-y-1 scale-95"
                             x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                             x-transition:leave="transition ease-in duration-100"
                             x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                             x-transition:leave-end="opacity-0 translate-y-1 scale-95"
                             style="display: none;" 
                             class="absolute right-0 mt-1.5 w-36 bg-white dark:bg-slate-800 rounded-2xl shadow-xl shadow-slate-900/20 border border-slate-200 dark:border-slate-700 p-1 z-50 backdrop-blur-md">
                            <a href="#" @click.prevent="exportChart('encodingSpeedChart'); openExport = false" 
                               class="flex items-center gap-2 px-3 py-1.5 text-xs font-semibold text-slate-700 dark:text-slate-200 hover:bg-emerald-50 dark:hover:bg-emerald-950/60 hover:text-emerald-700 dark:hover:text-emerald-300 rounded-xl transition-colors">
                                <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                <span>Download PNG</span>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="relative flex-1 w-full min-h-0"><canvas id="encodingSpeedChart"></canvas></div>
            </div>
        </div>
    </div>
        </div>
    </div>

</div>


<!-- Chart.js Setup -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.2.0"></script>
<script>
    // Global Chart Configuration
    Chart.register(ChartDataLabels);
    Chart.defaults.font.family = "'Inter', 'sans-serif'";
    Chart.defaults.color = '#64748b';
    Chart.defaults.plugins.tooltip.backgroundColor = 'rgba(15, 23, 42, 0.9)';
    Chart.defaults.plugins.tooltip.titleFont = { size: 14, weight: 'bold' };
    Chart.defaults.plugins.tooltip.padding = 12;
    Chart.defaults.plugins.tooltip.cornerRadius = 8;
    
    Chart.defaults.plugins.datalabels.color = '#ffffff';
    Chart.defaults.plugins.datalabels.font = { weight: 'bold', size: 12 };
    Chart.defaults.plugins.datalabels.formatter = Math.round;
    Chart.defaults.plugins.datalabels.display = function(context) { return context.dataset.data[context.dataIndex] > 0; };

    // Chart Instances Store
    const charts = {};

    document.addEventListener("DOMContentLoaded", function () {
        // 1. Visit Volume
        const volumeCtx = document.getElementById('visitVolumeChart').getContext('2d');
        const volumeData = {!! json_encode($visitVolumeData) !!};
        let gradient = volumeCtx.createLinearGradient(0, 0, 0, 400);
        gradient.addColorStop(0, 'rgba(16, 185, 129, 0.4)');
        gradient.addColorStop(1, 'rgba(16, 185, 129, 0.0)');

        charts['volume'] = new Chart(volumeCtx, {
            type: 'line',
            data: {
                labels: volumeData.labels,
                datasets: [{
                    label: 'Patient Visits', data: volumeData.data,
                    borderColor: '#10b981', backgroundColor: gradient, borderWidth: 3,
                    tension: 0.4, fill: true, pointBackgroundColor: '#ffffff', pointBorderColor: '#10b981', pointBorderWidth: 2, pointRadius: 5, pointHoverRadius: 7
                }]
            },
            options: {
                responsive: true, maintainAspectRatio: false, layout: { padding: { top: 25 } },
                plugins: { legend: { display: false }, datalabels: { align: 'top', anchor: 'end', color: '#10b981', backgroundColor: 'rgba(255, 255, 255, 0.9)', borderRadius: 4, padding: 4 } },
                scales: { y: { beginAtZero: true, suggestedMax: 5, ticks: { precision: 0, stepSize: 1 }, grid: { borderDash: [4, 4] } }, x: { grid: { display: false } } }
            }
        });

        // 2. Peak Hours
        const peakCtx = document.getElementById('peakHoursChart').getContext('2d');
        const peakData = {!! json_encode($peakHoursData) !!};
        charts['peak'] = new Chart(peakCtx, {
            type: 'bar',
            data: {
                labels: peakData.labels,
                datasets: [{ label: 'Consultations', data: peakData.data, backgroundColor: '#8b5cf6', borderRadius: 4 }]
            },
            options: {
                responsive: true, maintainAspectRatio: false, layout: { padding: { top: 25 } },
                plugins: { legend: { display: false }, datalabels: { align: 'top', anchor: 'end', color: '#8b5cf6' } },
                scales: { y: { beginAtZero: true, ticks: { precision: 0 }, grid: { borderDash: [4, 4] } }, x: { grid: { display: false } } }
            }
        });

        // 3. Workload
        const workCtx = document.getElementById('workloadChart').getContext('2d');
        const workDataRaw = {!! json_encode($workloadFormatted) !!};
        charts['workload'] = new Chart(workCtx, {
            type: 'bar',
            data: {
                labels: Object.keys(workDataRaw),
                datasets: [{ label: 'Consultations', data: Object.values(workDataRaw), backgroundColor: '#f59e0b', borderRadius: 4 }]
            },
            options: {
                indexAxis: 'y', responsive: true, maintainAspectRatio: false, layout: { padding: { right: 40 } },
                plugins: { legend: { display: false }, datalabels: { align: 'right', anchor: 'end', color: '#f59e0b' } },
                scales: { x: { beginAtZero: true, ticks: { precision: 0 } }, y: { grid: { display: false } } }
            }
        });

        // 4. Age-Sex Pyramid
        const ageSexCtx = document.getElementById('ageSexChart').getContext('2d');
        const demoData = {!! json_encode($demoData) !!};
        charts['demographics'] = new Chart(ageSexCtx, {
            type: 'bar',
            data: {
                labels: demoData.labels,
                datasets: [
                    { label: 'Male', data: demoData.Male.map(v => -Math.abs(v)), backgroundColor: '#3b82f6', borderRadius: { topLeft: 4, bottomLeft: 4 } },
                    { label: 'Female', data: demoData.Female, backgroundColor: '#ec4899', borderRadius: { topRight: 4, bottomRight: 4 } }
                ]
            },
            options: {
                indexAxis: 'y', responsive: true, maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'top', labels: { boxWidth: 14, usePointStyle: true, font: { size: 13 } } },
                    datalabels: { formatter: (value) => Math.abs(value) },
                    tooltip: { callbacks: { label: function (context) { return context.dataset.label + ': ' + Math.abs(context.raw); } } }
                },
                scales: { x: { stacked: true, ticks: { callback: function (value) { return Math.abs(value); }, precision: 0 } }, y: { stacked: true, grid: { display: false } } }
            }
        });

        // Center Text Plugin for Doughnut Charts (Displays Total in Donut Center)
        const doughnutCenterTextPlugin = {
            id: 'doughnutCenterText',
            beforeDraw: function(chart) {
                if (chart.config.type !== 'doughnut') return;
                const chartArea = chart.chartArea;
                if (!chartArea) return;
                const { ctx } = chart;
                ctx.save();
                const dataset = chart.data.datasets[0];
                const total = (dataset ? dataset.data : []).reduce((a, b) => a + Number(b), 0);
                const subLabel = chart.canvas.id === 'classificationChart' ? 'Patients' : 'Cases';
                const centerX = (chartArea.left + chartArea.right) / 2;
                const centerY = (chartArea.top + chartArea.bottom) / 2;

                ctx.textAlign = 'center';
                ctx.textBaseline = 'middle';

                ctx.font = '800 22px "Inter", sans-serif';
                ctx.fillStyle = document.documentElement.classList.contains('dark') ? '#f8fafc' : '#0f172a';
                ctx.fillText(total.toLocaleString(), centerX, centerY - 8);

                ctx.font = '700 10px "Inter", sans-serif';
                ctx.fillStyle = '#94a3b8';
                ctx.fillText(subLabel.toUpperCase(), centerX, centerY + 12);
                ctx.restore();
            }
        };

        // 5. Classification Doughnut (Formatted in Percentages with Center Total)
        const classCtx = document.getElementById('classificationChart').getContext('2d');
        const classDataRaw = {!! json_encode($classificationData) !!};
        charts['classification'] = new Chart(classCtx, {
            type: 'doughnut',
            data: {
                labels: Object.keys(classDataRaw),
                datasets: [{
                    data: Object.values(classDataRaw),
                    backgroundColor: ['#0ea5e9', '#6366f1', '#10b981'],
                    borderWidth: 0,
                    hoverOffset: 6
                }]
            },
            plugins: [doughnutCenterTextPlugin],
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '72%',
                plugins: {
                    legend: {
                        position: window.innerWidth < 768 ? 'bottom' : 'right',
                        labels: {
                            boxWidth: 12,
                            padding: 12,
                            font: { size: 11, weight: '600' },
                            usePointStyle: true,
                            generateLabels: function(chart) {
                                const data = chart.data;
                                if (data.labels.length && data.datasets.length) {
                                    const dataset = data.datasets[0];
                                    const total = dataset.data.reduce((a, b) => a + Number(b), 0);
                                    return data.labels.map((label, i) => {
                                        const val = Number(dataset.data[i]) || 0;
                                        const pct = total > 0 ? ((val / total) * 100).toFixed(1) : '0.0';
                                        const meta = chart.getDatasetMeta(0);
                                        const style = meta.controller ? meta.controller.getStyle(i) : {};
                                        return {
                                            text: `${label}: ${val.toLocaleString()} (${pct}%)`,
                                            fillStyle: style.backgroundColor || dataset.backgroundColor[i],
                                            strokeStyle: '#ffffff',
                                            lineWidth: 0,
                                            hidden: isNaN(dataset.data[i]) || meta.data[i]?.hidden,
                                            index: i
                                        };
                                    });
                                }
                                return [];
                            }
                        }
                    },
                    datalabels: {
                        color: '#ffffff',
                        font: { weight: '800', size: 11 },
                        formatter: function(value, ctx) {
                            const sum = ctx.chart.data.datasets[0].data.reduce((a, b) => a + Number(b), 0);
                            if (!sum || value === 0) return '';
                            return ((value / sum) * 100).toFixed(1) + '%';
                        },
                        display: function(ctx) {
                            return ctx.dataset.data[ctx.dataIndex] > 0;
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const val = Number(context.raw) || 0;
                                const sum = context.dataset.data.reduce((a, b) => a + Number(b), 0);
                                const pct = sum > 0 ? ((val / sum) * 100).toFixed(1) : '0.0';
                                return ` ${context.label}: ${val.toLocaleString()} (${pct}%)`;
                            }
                        }
                    }
                },
                onResize: function(chart, size) {
                    chart.options.plugins.legend.position = size.width < 500 ? 'bottom' : 'right';
                }
            }
        });

        // 6. Severity Doughnut (Formatted in Percentages with Center Total)
        const sevCtx = document.getElementById('severityChart').getContext('2d');
        const sevDataRaw = {!! json_encode($severityData) !!};
        const sevColors = Object.keys(sevDataRaw).map(l => {
            const lower = l.toLowerCase();
            if (lower === 'light') return '#10b981';
            if (lower === 'mild') return '#f59e0b';
            if (lower === 'severe' || lower === 'emergency') return '#ef4444';
            if (lower === 'urgent') return '#ea580c';
            return '#64748b';
        });
        charts['severity'] = new Chart(sevCtx, {
            type: 'doughnut',
            data: {
                labels: Object.keys(sevDataRaw).map(l => l.charAt(0).toUpperCase() + l.slice(1)),
                datasets: [{
                    data: Object.values(sevDataRaw),
                    backgroundColor: sevColors,
                    borderWidth: 0,
                    hoverOffset: 6
                }]
            },
            plugins: [doughnutCenterTextPlugin],
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '72%',
                plugins: {
                    legend: {
                        position: window.innerWidth < 768 ? 'bottom' : 'right',
                        labels: {
                            boxWidth: 12,
                            padding: 12,
                            font: { size: 11, weight: '600' },
                            usePointStyle: true,
                            generateLabels: function(chart) {
                                const data = chart.data;
                                if (data.labels.length && data.datasets.length) {
                                    const dataset = data.datasets[0];
                                    const total = dataset.data.reduce((a, b) => a + Number(b), 0);
                                    return data.labels.map((label, i) => {
                                        const val = Number(dataset.data[i]) || 0;
                                        const pct = total > 0 ? ((val / total) * 100).toFixed(1) : '0.0';
                                        const meta = chart.getDatasetMeta(0);
                                        const style = meta.controller ? meta.controller.getStyle(i) : {};
                                        return {
                                            text: `${label}: ${val.toLocaleString()} (${pct}%)`,
                                            fillStyle: style.backgroundColor || dataset.backgroundColor[i],
                                            strokeStyle: '#ffffff',
                                            lineWidth: 0,
                                            hidden: isNaN(dataset.data[i]) || meta.data[i]?.hidden,
                                            index: i
                                        };
                                    });
                                }
                                return [];
                            }
                        }
                    },
                    datalabels: {
                        color: '#ffffff',
                        font: { weight: '800', size: 11 },
                        formatter: function(value, ctx) {
                            const sum = ctx.chart.data.datasets[0].data.reduce((a, b) => a + Number(b), 0);
                            if (!sum || value === 0) return '';
                            return ((value / sum) * 100).toFixed(1) + '%';
                        },
                        display: function(ctx) {
                            return ctx.dataset.data[ctx.dataIndex] > 0;
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const val = Number(context.raw) || 0;
                                const sum = context.dataset.data.reduce((a, b) => a + Number(b), 0);
                                const pct = sum > 0 ? ((val / sum) * 100).toFixed(1) : '0.0';
                                return ` ${context.label}: ${val.toLocaleString()} (${pct}%)`;
                            }
                        }
                    }
                },
                onResize: function(chart, size) {
                    chart.options.plugins.legend.position = size.width < 500 ? 'bottom' : 'right';
                }
            }
        });

        // 7. Barangay Heatmap (Dynamic height based on count)
        const brgyCtx = document.getElementById('barangayChart').getContext('2d');
        const brgyDataRaw = {!! json_encode($barangayData) !!};
        const brgyCount = Object.keys(brgyDataRaw).length;
        // Dynamic height: 45px per barangay, minimum 300px
        const brgyHeight = Math.max(300, brgyCount * 45);
        document.getElementById('barangayChartContainer').style.height = brgyHeight + 'px';
        // Generate a gradient color palette based on value intensity
        function computeBrgyColors(values) {
            if (!values || values.length === 0) return [];
            const maxVal = Math.max(...values, 1);
            return values.map(v => {
                const intensity = v / maxVal;
                const r = Math.round(20 + (0 - 20) * intensity);
                const g = Math.round(184 + (180 - 184) * intensity);
                const b = Math.round(166 + (100 - 166) * intensity);
                return `rgba(${r}, ${g}, ${b}, ${0.5 + intensity * 0.5})`;
            });
        }
        const brgyValues = Object.values(brgyDataRaw);
        const brgyColors = computeBrgyColors(brgyValues);
        charts['barangay'] = new Chart(brgyCtx, {
            type: 'bar',
            data: {
                labels: Object.keys(brgyDataRaw),
                datasets: [{ label: 'Visits', data: brgyValues, backgroundColor: brgyColors, borderRadius: 6, borderSkipped: false, barPercentage: 0.7, categoryPercentage: 0.85 }]
            },
            options: {
                indexAxis: 'y', responsive: true, maintainAspectRatio: false, layout: { padding: { right: 40 } },
                plugins: { legend: { display: false }, datalabels: { align: 'right', anchor: 'end', color: '#0d9488', font: { weight: '600', size: 12 } } },
                scales: { x: { beginAtZero: true, ticks: { precision: 0, font: { size: 11 } }, grid: { color: 'rgba(148,163,184,0.08)' } }, y: { grid: { display: false }, ticks: { font: { size: 12, weight: '500' } } } }
            }
        });

        // 8. Staff Productivity: Duration Chart
        const durCtx = document.getElementById('durationStaffChart').getContext('2d');
        const durDataRaw = {!! json_encode($staffProductivity) !!};
        charts['duration'] = new Chart(durCtx, {
            type: 'bar',
            data: {
                labels: durDataRaw.durationChartLabels,
                datasets: [{ label: 'Minutes', data: durDataRaw.durationChartData, backgroundColor: '#3b82f6', borderRadius: 4 }]
            },
            options: {
                indexAxis: 'y', responsive: true, maintainAspectRatio: false, layout: { padding: { right: 40 } },
                plugins: { legend: { display: false }, datalabels: { align: 'right', anchor: 'end', color: '#3b82f6', formatter: function(value) { return value + 'm'; } } },
                scales: { x: { beginAtZero: true, ticks: { precision: 0 } }, y: { grid: { display: false } } }
            }
        });

        // 9. Staff Productivity: Encoding Speed Chart
        const encCtx = document.getElementById('encodingSpeedChart').getContext('2d');
        charts['encoding'] = new Chart(encCtx, {
            type: 'bar',
            data: {
                labels: durDataRaw.encodingChartLabels,
                datasets: [{ label: 'Seconds', data: durDataRaw.encodingChartData, backgroundColor: '#8b5cf6', borderRadius: 4 }]
            },
            options: {
                indexAxis: 'y', responsive: true, maintainAspectRatio: false, layout: { padding: { right: 40 } },
                plugins: { legend: { display: false }, datalabels: { align: 'right', anchor: 'end', color: '#8b5cf6', formatter: function(value) { return value + 's'; } } },
                scales: { x: { beginAtZero: true, ticks: { precision: 0 } }, y: { grid: { display: false } } }
            }
        });
    });

    // Export CSV handler
    async function exportAnalyticsCsv(btn) {
        if (!btn) btn = document.getElementById('exportAnalyticsCsvBtn');
        const icon = document.getElementById('exportCsvIcon');
        const spinner = document.getElementById('exportCsvSpinner');
        const text = document.getElementById('exportCsvText');

        if (btn) btn.disabled = true;
        if (icon) icon.classList.add('hidden');
        if (spinner) spinner.classList.remove('hidden');
        if (text) text.textContent = 'Exporting...';

        try {
            const timeFilter = '{{ $timeFilter }}';
            const response = await fetch('/admin/analytics/export-csv?time_filter=' + encodeURIComponent(timeFilter));
            if (response.status === 404) {
                const err = await response.json();
                window.dispatchEvent(new CustomEvent('add-toast', { detail: { message: err.message || 'No data to export.', type: 'warning' } }));
                return;
            }
            if (!response.ok) {
                window.dispatchEvent(new CustomEvent('add-toast', { detail: { message: 'Export failed. Please try again.', type: 'error' } }));
                return;
            }
            const blob = await response.blob();
            const disposition = response.headers.get('Content-Disposition');
            let filename = 'detailed-analytics.csv';
            if (disposition) {
                const match = disposition.match(/filename="?([^"]+)"?/);
                if (match) filename = match[1];
            }
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = filename;
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            URL.revokeObjectURL(url);
            window.dispatchEvent(new CustomEvent('add-toast', { detail: { message: 'CSV exported successfully!', type: 'success' } }));
        } catch (e) {
            console.error('CSV Export Error:', e);
            window.dispatchEvent(new CustomEvent('add-toast', { detail: { message: 'Export failed. Please try again.', type: 'error' } }));
        } finally {
            if (btn) btn.disabled = false;
            if (icon) icon.classList.remove('hidden');
            if (spinner) spinner.classList.add('hidden');
            if (text) text.textContent = 'Export CSV';
        }
    }
    window.exportAnalyticsCsv = exportAnalyticsCsv;
    window.__exportAnalyticsCsv = exportAnalyticsCsv;

    // Export Analytics Graph Summary CSV handler
    async function exportAnalyticsSummaryCsv() {
        window.dispatchEvent(new CustomEvent('add-toast', { detail: { message: 'Generating Analytics Summary Report...', type: 'info' } }));

        try {
            const timeFilter = '{{ $timeFilter }}';
            const response = await fetch('/admin/analytics/export-summary-csv?time_filter=' + encodeURIComponent(timeFilter));
            if (response.status === 404) {
                const err = await response.json();
                window.dispatchEvent(new CustomEvent('add-toast', { detail: { message: err.message || 'No data to export.', type: 'warning' } }));
                return;
            }
            if (!response.ok) {
                window.dispatchEvent(new CustomEvent('add-toast', { detail: { message: 'Export failed. Please try again.', type: 'error' } }));
                return;
            }
            const blob = await response.blob();
            const disposition = response.headers.get('Content-Disposition');
            let filename = 'analytics-summary-graphs.csv';
            if (disposition) {
                const match = disposition.match(/filename="?([^"]+)"?/);
                if (match) filename = match[1];
            }
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = filename;
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            URL.revokeObjectURL(url);
            window.dispatchEvent(new CustomEvent('add-toast', { detail: { message: 'Analytics Summary CSV exported successfully!', type: 'success' } }));
        } catch (e) {
            console.error('Summary CSV Export Error:', e);
            window.dispatchEvent(new CustomEvent('add-toast', { detail: { message: 'Export failed. Please try again.', type: 'error' } }));
        }
    }
    window.exportAnalyticsSummaryCsv = exportAnalyticsSummaryCsv;

    function analyticsDashboard() {
        return {
            productivityTab: 'clinical',
            productivityStaff: 'all',
            productivityTime: '{{ $timeFilter }}',
            productivityData: {!! json_encode($staffProductivity) !!},
            async fetchProductivity() {
                try {
                    const response = await fetch(`/admin/analytics/staff-productivity?time_filter=${this.productivityTime}&staff_id=${this.productivityStaff}`);
                    const data = await response.json();
                    this.productivityData = data;
                    
                    // Update Duration Chart
                    const durChart = charts['duration'];
                    if (durChart) {
                        durChart.data.labels = data.durationChartLabels;
                        durChart.data.datasets[0].data = data.durationChartData;
                        durChart.update();
                    }

                    // Update Encoding Chart
                    const encChart = charts['encoding'];
                    if (encChart) {
                        encChart.data.labels = data.encodingChartLabels;
                        encChart.data.datasets[0].data = data.encodingChartData;
                        encChart.update();
                    }
                } catch (e) {
                    console.error("Failed to fetch productivity", e);
                }
            },
            async updateChart(chartId, filterValue) {
                try {
                    const response = await fetch(`/admin/analytics/chart/${chartId}?time_filter=${filterValue}`);
                    const json = await response.json();
                    const chart = charts[chartId];
                    if (chart) {
                        if (chartId === 'demographics') {
                            chart.data.labels = json.labels;
                            chart.data.datasets[0].data = json.Male.map(v => -Math.abs(v));
                            chart.data.datasets[1].data = json.Female;
                        } else {
                            chart.data.labels = json.labels;
                            chart.data.datasets[0].data = json.data;
                            
                            if (chartId === 'classification') {
                                chart.data.datasets[0].backgroundColor = ['#0ea5e9', '#6366f1', '#10b981'];
                            }

                            // Adjust colors if severity dynamically changes length
                            if (chartId === 'severity') {
                                chart.data.datasets[0].backgroundColor = json.labels.map(l => {
                                    const lower = l.toLowerCase();
                                    if (lower === 'light') return '#10b981';
                                    if (lower === 'mild') return '#f59e0b';
                                    if (lower === 'severe' || lower === 'emergency') return '#ef4444';
                                    if (lower === 'urgent') return '#ea580c';
                                    return '#64748b';
                                });
                            }

                            // Dynamic update for Barangay Heatmap
                            if (chartId === 'barangay') {
                                const vals = json.data || [];
                                chart.data.datasets[0].backgroundColor = computeBrgyColors(vals);

                                const count = (json.labels || []).length;
                                const container = document.getElementById('barangayChartContainer');
                                if (container) {
                                    container.style.height = Math.max(300, count * 45) + 'px';
                                }

                                const emptyState = document.getElementById('barangayEmptyState');
                                const canvasEl = document.getElementById('barangayChart');
                                if (emptyState && canvasEl) {
                                    if (count === 0) {
                                        emptyState.style.display = 'flex';
                                        canvasEl.style.display = 'none';
                                    } else {
                                        emptyState.style.display = 'none';
                                        canvasEl.style.display = 'block';
                                    }
                                }
                            }
                        }
                        chart.update();
                    }
                } catch (e) {
                    console.error("Failed to update chart", e);
                }
            },
            exportChart(canvasId) {
                const canvas = document.getElementById(canvasId);
                const image = canvas.toDataURL("image/png").replace("image/png", "image/octet-stream");
                const link = document.createElement('a');
                link.download = canvasId + '-' + new Date().toISOString().split('T')[0] + '.png';
                link.href = image;
                link.click();
            },
            exportCSV(chartKey) {
                const chart = charts[chartKey];
                if (!chart) return;
                
                let csvContent = "data:text/csv;charset=utf-8,";
                
                if (chartKey === 'demographics') {
                    csvContent += "Age Group,Male,Female\n";
                    const labels = chart.data.labels;
                    const males = chart.data.datasets[0].data.map(v => Math.abs(v));
                    const females = chart.data.datasets[1].data;
                    
                    for (let i = 0; i < labels.length; i++) {
                        csvContent += `"${labels[i]}",${males[i]},${females[i]}\n`;
                    }
                } else {
                    csvContent += "Label,Value\n";
                    const labels = chart.data.labels;
                    const data = chart.data.datasets[0].data;
                    
                    for (let i = 0; i < labels.length; i++) {
                        csvContent += `"${labels[i]}",${data[i]}\n`;
                    }
                }
                
                const encodedUri = encodeURI(csvContent);
                const link = document.createElement("a");
                link.setAttribute("href", encodedUri);
                link.setAttribute("download", chartKey + "_data_" + new Date().toISOString().split('T')[0] + ".csv");
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
            }
        }
    }
</script>

{{-- ═══════════════════════════════════════════════════════════════════
     ANALYTICS PRINT REPORT GENERATOR
     Builds a clean data-focused report with actual tables, numbers,
     and rasterized chart images. Prints via isolated iframe.
═══════════════════════════════════════════════════════════════════ --}}
<script>
    function printAnalyticsReport() {
        // 1. Rasterize all chart canvases
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

        const chartImages = {
            visitVolume: rasterize('visitVolumeChart'),
            peakHours: rasterize('peakHoursChart'),
            workload: rasterize('workloadChart'),
            ageSex: rasterize('ageSexChart'),
            classification: rasterize('classificationChart'),
            severity: rasterize('severityChart'),
            barangay: rasterize('barangayChart'),
            durationStaff: rasterize('durationStaffChart'),
            encodingSpeed: rasterize('encodingSpeedChart'),
        };

        // 2. Build accessible data tables from server-side data
        @php
            $thStyle = 'style="background:#f1f5f9;color:#1e293b;font-size:7pt;font-weight:800;text-transform:uppercase;border:1px solid #cbd5e1;padding:4px 6px;text-align:left;"';
            $thStyleR = 'style="background:#f1f5f9;color:#1e293b;font-size:7pt;font-weight:800;text-transform:uppercase;border:1px solid #cbd5e1;padding:4px 6px;text-align:right;"';
            $thStyleC = 'style="background:#f1f5f9;color:#1e293b;font-size:7pt;font-weight:800;text-transform:uppercase;border:1px solid #cbd5e1;padding:4px 6px;text-align:center;"';

            // Timeframe label for print report
            $printTimeframeLabel = match($timeFilter) {
                'today' => 'Today (' . now()->format('F d, Y') . ')',
                'weekly' => 'This Week (' . now()->startOfWeek()->format('M d') . ' - ' . now()->endOfWeek()->format('M d, Y') . ')',
                'monthly' => 'This Month (' . now()->format('F Y') . ')',
                'yearly' => 'This Year (' . now()->format('Y') . ')',
                default => 'All Time (Complete Historical Data)',
            };


            // 1. Visit Volume Table
            $vvRows = '';
            $vvTotal = array_sum($visitVolumeData['data'] ?? []);
            if(isset($visitVolumeData['labels']) && count($visitVolumeData['labels']) > 0) {
                foreach($visitVolumeData['labels'] as $vi => $vLbl) {
                    $vCnt = $visitVolumeData['data'][$vi] ?? 0;
                    $vPct = $vvTotal > 0 ? round(($vCnt / $vvTotal) * 100, 1) : 0;
                    $vvRows .= '<tr' . ($vi % 2 === 1 ? ' style="background:#f8fafc;"' : '') . '>';
                    $vvRows .= '<td style="padding:4px 6px;border:1px solid #e2e8f0;color:#334155;font-size:7.5pt;">' . addslashes($vLbl) . '</td>';
                    $vvRows .= '<td style="padding:4px 6px;border:1px solid #e2e8f0;color:#0f172a;font-weight:700;text-align:right;font-size:7.5pt;font-variant-numeric:tabular-nums;">' . number_format($vCnt) . '</td>';
                    $vvRows .= '<td style="padding:4px 6px;border:1px solid #e2e8f0;color:#64748b;text-align:right;font-size:7.5pt;font-variant-numeric:tabular-nums;">' . $vPct . '%</td></tr>';
                }
            }
            if(empty($vvRows)) {
                $vvRows = '<tr><td colspan="3" style="padding:6px;text-align:center;color:#94a3b8;font-size:7.5pt;">No consultation traffic recorded in this period.</td></tr>';
            }
            $vvTable = '<table style="width:100%;border-collapse:collapse;font-size:7.5pt;border:1px solid #cbd5e1;border-radius:4px;overflow:hidden;"><thead><tr><th ' . $thStyle . '>Time Interval</th><th ' . $thStyleR . '>Consultations</th><th ' . $thStyleR . '>% Share</th></tr></thead><tbody>' . $vvRows . '</tbody></table>';

            // 2. Peak Hours Table
            $phRows = '';
            $phTotal = array_sum($peakHoursData['data'] ?? []);
            if(isset($peakHoursData['labels']) && count($peakHoursData['labels']) > 0) {
                foreach($peakHoursData['labels'] as $pi => $pLbl) {
                    $pCnt = $peakHoursData['data'][$pi] ?? 0;
                    $pPct = $phTotal > 0 ? round(($pCnt / $phTotal) * 100, 1) : 0;
                    $level = $pCnt >= 10 ? '<span style="color:#b91c1c;font-weight:800;">Surge</span>' : ($pCnt >= 4 ? '<span style="color:#d97706;font-weight:700;">Moderate</span>' : '<span style="color:#059669;font-weight:600;">Normal</span>');
                    $phRows .= '<tr' . ($pi % 2 === 1 ? ' style="background:#f8fafc;"' : '') . '>';
                    $phRows .= '<td style="padding:4px 6px;border:1px solid #e2e8f0;color:#334155;font-size:7.5pt;">' . addslashes($pLbl) . '</td>';
                    $phRows .= '<td style="padding:4px 6px;border:1px solid #e2e8f0;color:#0f172a;font-weight:700;text-align:right;font-size:7.5pt;font-variant-numeric:tabular-nums;">' . number_format($pCnt) . '</td>';
                    $phRows .= '<td style="padding:4px 6px;border:1px solid #e2e8f0;color:#64748b;text-align:right;font-size:7.5pt;font-variant-numeric:tabular-nums;">' . $pPct . '%</td>';
                    $phRows .= '<td style="padding:4px 6px;border:1px solid #e2e8f0;text-align:center;font-size:7.5pt;">' . $level . '</td></tr>';
                }
            }
            if(empty($phRows)) {
                $phRows = '<tr><td colspan="4" style="padding:6px;text-align:center;color:#94a3b8;font-size:7.5pt;">No arrival records found for operating hours.</td></tr>';
            }
            $phTable = '<table style="width:100%;border-collapse:collapse;font-size:7.5pt;border:1px solid #cbd5e1;border-radius:4px;overflow:hidden;"><thead><tr><th ' . $thStyle . '>Operating Hour</th><th ' . $thStyleR . '>Patients</th><th ' . $thStyleR . '>% Share</th><th ' . $thStyleC . '>Traffic Status</th></tr></thead><tbody>' . $phRows . '</tbody></table>';

            // 3. Demographics Table
            $demoRows = '';
            $totalMale = array_sum($demoData['Male'] ?? []);
            $totalFemale = array_sum($demoData['Female'] ?? []);
            $overallDemo = $totalMale + $totalFemale;
            if(isset($demoData['labels']) && count($demoData['labels']) > 0) {
                foreach($demoData['labels'] as $di => $lbl) {
                    $m = $demoData['Male'][$di] ?? 0;
                    $f = $demoData['Female'][$di] ?? 0;
                    $tot = $m + $f;
                    $pct = $overallDemo > 0 ? round(($tot / $overallDemo) * 100, 1) : 0;
                    $demoRows .= '<tr' . ($di % 2 === 1 ? ' style="background:#f8fafc;"' : '') . '>';
                    $demoRows .= '<td style="padding:4px 6px;border:1px solid #e2e8f0;color:#334155;font-size:7.5pt;">' . addslashes($lbl) . '</td>';
                    $demoRows .= '<td style="padding:4px 6px;border:1px solid #e2e8f0;color:#1e40af;font-weight:700;text-align:right;font-size:7.5pt;font-variant-numeric:tabular-nums;">' . number_format($m) . '</td>';
                    $demoRows .= '<td style="padding:4px 6px;border:1px solid #e2e8f0;color:#be185d;font-weight:700;text-align:right;font-size:7.5pt;font-variant-numeric:tabular-nums;">' . number_format($f) . '</td>';
                    $demoRows .= '<td style="padding:4px 6px;border:1px solid #e2e8f0;color:#0f172a;font-weight:800;text-align:right;font-size:7.5pt;font-variant-numeric:tabular-nums;">' . number_format($tot) . '</td>';
                    $demoRows .= '<td style="padding:4px 6px;border:1px solid #e2e8f0;color:#64748b;text-align:right;font-size:7.5pt;font-variant-numeric:tabular-nums;">' . $pct . '%</td></tr>';
                }
            }
            $demoTable = '<table style="width:100%;border-collapse:collapse;font-size:7.5pt;border:1px solid #cbd5e1;border-radius:4px;overflow:hidden;"><thead><tr><th ' . $thStyle . '>Age Cohort</th><th ' . $thStyleR . '>Male</th><th ' . $thStyleR . '>Female</th><th ' . $thStyleR . '>Total</th><th ' . $thStyleR . '>% Share</th></tr></thead><tbody>' . $demoRows . '</tbody></table>';

            // 4. Top Diagnoses Table
            $diagRows = '';
            if(isset($topDiagnoses) && count($topDiagnoses) > 0) {
                $diagTotal = $topDiagnoses->sum('count');
                foreach($topDiagnoses as $i => $diag) {
                    $dName = addslashes($diag->diagnosis ?? 'Unknown');
                    $dPct = $diagTotal > 0 ? round(($diag->count / $diagTotal) * 100, 1) : 0;
                    $diagRows .= '<tr' . ($i % 2 === 1 ? ' style="background:#f8fafc;"' : '') . '>';
                    $diagRows .= '<td style="padding:4px 6px;border:1px solid #e2e8f0;color:#334155;font-size:7.5pt;">' . ($i+1) . '</td>';
                    $diagRows .= '<td style="padding:4px 6px;border:1px solid #e2e8f0;color:#0f172a;font-weight:700;font-size:7.5pt;">' . $dName . '</td>';
                    $diagRows .= '<td style="padding:4px 6px;border:1px solid #e2e8f0;color:#0f172a;font-weight:800;text-align:right;font-size:7.5pt;font-variant-numeric:tabular-nums;">' . number_format($diag->count) . '</td>';
                    $diagRows .= '<td style="padding:4px 6px;border:1px solid #e2e8f0;color:#64748b;text-align:right;font-size:7.5pt;font-variant-numeric:tabular-nums;">' . $dPct . '%</td></tr>';
                }
            }
            if(empty($diagRows)) {
                $diagRows = '<tr><td colspan="4" style="padding:6px;text-align:center;color:#94a3b8;font-size:7.5pt;">No diagnosis records encoded for this period.</td></tr>';
            }
            $diagTable = '<table style="width:100%;border-collapse:collapse;font-size:7.5pt;border:1px solid #cbd5e1;border-radius:4px;overflow:hidden;"><thead><tr><th ' . $thStyle . '>#</th><th ' . $thStyle . '>Clinical Diagnosis / Morbidity</th><th ' . $thStyleR . '>Cases</th><th ' . $thStyleR . '>% Share</th></tr></thead><tbody>' . $diagRows . '</tbody></table>';

            // 5. Classification Table
            $classRows = '';
            if (isset($classificationSummary['categories'])) {
                foreach($classificationSummary['categories'] as $cKey => $cData) {
                    $cName = match($cKey) {
                        'pediatric' => 'Pediatric (0–12 yrs)',
                        'regular_adult' => 'Regular Adult (13–59 yrs)',
                        'senior_citizen' => 'Senior Citizen (60+ yrs)',
                        default => ucfirst($cKey),
                    };
                    $classRows .= '<tr>';
                    $classRows .= '<td style="padding:4px 6px;border:1px solid #e2e8f0;font-weight:700;">' . $cName . '</td>';
                    $classRows .= '<td style="padding:4px 6px;border:1px solid #e2e8f0;text-align:right;font-variant-numeric:tabular-nums;">' . number_format($cData['registered_count']) . '</td>';
                    $classRows .= '<td style="padding:4px 6px;border:1px solid #e2e8f0;text-align:right;color:#64748b;">' . $cData['registered_pct'] . '%</td>';
                    $classRows .= '<td style="padding:4px 6px;border:1px solid #e2e8f0;text-align:right;font-weight:700;font-variant-numeric:tabular-nums;">' . number_format($cData['served_count']) . '</td>';
                    $classRows .= '<td style="padding:4px 6px;border:1px solid #e2e8f0;text-align:right;color:#64748b;">' . $cData['served_pct'] . '%</td>';
                    $classRows .= '</tr>';
                }
                // Demographic Subtotal
                $classRows .= '<tr style="background:#f1f5f9;font-weight:800;">';
                $classRows .= '<td style="padding:4px 6px;border:1px solid #cbd5e1;">Demographic Subtotal</td>';
                $classRows .= '<td style="padding:4px 6px;border:1px solid #cbd5e1;text-align:right;">' . number_format($classificationSummary['total_registered']) . '</td>';
                $classRows .= '<td style="padding:4px 6px;border:1px solid #cbd5e1;text-align:right;">100%</td>';
                $classRows .= '<td style="padding:4px 6px;border:1px solid #cbd5e1;text-align:right;">' . number_format($classificationSummary['unique_served_total']) . '</td>';
                $classRows .= '<td style="padding:4px 6px;border:1px solid #cbd5e1;text-align:right;">100%</td>';
                $classRows .= '</tr>';
                // PWD Overlay
                $classRows .= '<tr style="background:#fef3c7;font-weight:700;color:#92400e;">';
                $classRows .= '<td style="padding:4px 6px;border:1px solid #e2e8f0;">PWD (Disability Condition Overlay)</td>';
                $classRows .= '<td style="padding:4px 6px;border:1px solid #e2e8f0;text-align:right;">' . number_format($classificationSummary['pwd']['registered_count']) . '</td>';
                $classRows .= '<td style="padding:4px 6px;border:1px solid #e2e8f0;text-align:right;">' . $classificationSummary['pwd']['registered_percentage'] . '%</td>';
                $classRows .= '<td style="padding:4px 6px;border:1px solid #e2e8f0;text-align:right;">' . number_format($classificationSummary['pwd']['served_count']) . '</td>';
                $classRows .= '<td style="padding:4px 6px;border:1px solid #e2e8f0;text-align:right;">' . $classificationSummary['pwd']['served_percentage'] . '%</td>';
                $classRows .= '</tr>';
            }
            if(empty($classRows)) {
                $classRows = '<tr><td colspan="5" style="padding:6px;text-align:center;color:#94a3b8;font-size:7.5pt;">No patient classification data available.</td></tr>';
            }
            $classTable = '<table style="width:100%;border-collapse:collapse;font-size:7.5pt;border:1px solid #cbd5e1;border-radius:4px;overflow:hidden;"><thead><tr><th ' . $thStyle . '>Classification Cohort</th><th ' . $thStyleR . '>Registered</th><th ' . $thStyleR . '>% Registry</th><th ' . $thStyleR . '>Served (Period)</th><th ' . $thStyleR . '>% Volume</th></tr></thead><tbody>' . $classRows . '</tbody></table>';

            // 6. Severity Table
            $sevRows = '';
            $sevArr = is_array($severityData) ? $severityData : (is_object($severityData) ? $severityData->toArray() : []);
            $sevTotal = array_sum($sevArr);
            $si = 0;
            foreach($sevArr as $sev => $cnt) {
                $pct = $sevTotal > 0 ? round(($cnt / $sevTotal) * 100, 1) : 0;
                $badge = match(strtolower($sev)) {
                    'emergency', 'severe' => '<span style="color:#b91c1c;font-weight:800;">Emergency (Immediate)</span>',
                    'urgent' => '<span style="color:#ea580c;font-weight:800;">Urgent (Prompt)</span>',
                    'semi-urgent', 'mild' => '<span style="color:#d97706;font-weight:700;">Semi-Urgent (Standard)</span>',
                    default => '<span style="color:#15803d;font-weight:600;">Non-Urgent (Routine)</span>',
                };
                $sevRows .= '<tr' . ($si % 2 === 1 ? ' style="background:#f8fafc;"' : '') . '>';
                $sevRows .= '<td style="padding:4px 6px;border:1px solid #e2e8f0;color:#334155;font-size:7.5pt;">' . ucfirst(addslashes($sev)) . '</td>';
                $sevRows .= '<td style="padding:4px 6px;border:1px solid #e2e8f0;color:#0f172a;font-weight:700;text-align:right;font-size:7.5pt;font-variant-numeric:tabular-nums;">' . number_format($cnt) . '</td>';
                $sevRows .= '<td style="padding:4px 6px;border:1px solid #e2e8f0;color:#64748b;text-align:right;font-size:7.5pt;font-variant-numeric:tabular-nums;">' . $pct . '%</td>';
                $sevRows .= '<td style="padding:4px 6px;border:1px solid #e2e8f0;text-align:center;font-size:7.5pt;">' . $badge . '</td></tr>';
                $si++;
            }
            if(empty($sevRows)) {
                $sevRows = '<tr><td colspan="4" style="padding:6px;text-align:center;color:#94a3b8;font-size:7.5pt;">No triage severity cases logged.</td></tr>';
            }
            $sevTable = '<table style="width:100%;border-collapse:collapse;font-size:7.5pt;border:1px solid #cbd5e1;border-radius:4px;overflow:hidden;"><thead><tr><th ' . $thStyle . '>Acuity Level</th><th ' . $thStyleR . '>Cases</th><th ' . $thStyleR . '>% Share</th><th ' . $thStyleC . '>Triage Protocol</th></tr></thead><tbody>' . $sevRows . '</tbody></table>';

            // 7. Barangay Table
            $bgyRows = '';
            $bgyArr = is_array($barangayData) ? $barangayData : (is_object($barangayData) ? $barangayData->toArray() : []);
            arsort($bgyArr);
            $bgyTotal = array_sum($bgyArr);
            $bi = 0;
            foreach(array_slice($bgyArr, 0, 15, true) as $bgy => $cnt) {
                $pct = $bgyTotal > 0 ? round(($cnt / $bgyTotal) * 100, 1) : 0;
                $bgyRows .= '<tr' . ($bi % 2 === 1 ? ' style="background:#f8fafc;"' : '') . '>';
                $bgyRows .= '<td style="padding:4px 6px;border:1px solid #e2e8f0;color:#334155;font-size:7.5pt;">' . addslashes($bgy) . '</td>';
                $bgyRows .= '<td style="padding:4px 6px;border:1px solid #e2e8f0;color:#0f172a;font-weight:800;text-align:right;font-size:7.5pt;font-variant-numeric:tabular-nums;">' . number_format($cnt) . '</td>';
                $bgyRows .= '<td style="padding:4px 6px;border:1px solid #e2e8f0;color:#64748b;text-align:right;font-size:7.5pt;font-variant-numeric:tabular-nums;">' . $pct . '%</td></tr>';
                $bi++;
            }
            if(empty($bgyRows)) {
                $bgyRows = '<tr><td colspan="3" style="padding:6px;text-align:center;color:#94a3b8;font-size:7.5pt;">No barangay distribution data recorded.</td></tr>';
            }
            $bgyTable = '<table style="width:100%;border-collapse:collapse;font-size:7.5pt;border:1px solid #cbd5e1;border-radius:4px;overflow:hidden;"><thead><tr><th ' . $thStyle . '>Barangay Catchment</th><th ' . $thStyleR . '>Patients</th><th ' . $thStyleR . '>% Share</th></tr></thead><tbody>' . $bgyRows . '</tbody></table>';

            // 8. Staff Workload Table
            $wlRows = '';
            $wlArr = is_array($workloadFormatted) ? $workloadFormatted : [];
            $wlTotal = array_sum($wlArr);
            $wi = 0;
            foreach($wlArr as $wName => $wCnt) {
                $pct = $wlTotal > 0 ? round(($wCnt / $wlTotal) * 100, 1) : 0;
                $wlRows .= '<tr' . ($wi % 2 === 1 ? ' style="background:#f8fafc;"' : '') . '>';
                $wlRows .= '<td style="padding:4px 6px;border:1px solid #e2e8f0;color:#334155;font-size:7.5pt;">' . addslashes($wName) . '</td>';
                $wlRows .= '<td style="padding:4px 6px;border:1px solid #e2e8f0;color:#0f172a;font-weight:800;text-align:right;font-size:7.5pt;font-variant-numeric:tabular-nums;">' . number_format($wCnt) . '</td>';
                $wlRows .= '<td style="padding:4px 6px;border:1px solid #e2e8f0;color:#64748b;text-align:right;font-size:7.5pt;font-variant-numeric:tabular-nums;">' . $pct . '%</td></tr>';
                $wi++;
            }
            if(empty($wlRows)) {
                $wlRows = '<tr><td colspan="3" style="padding:6px;text-align:center;color:#94a3b8;font-size:7.5pt;">No practitioner encounters recorded for this timeframe.</td></tr>';
            }
            $wlTable = '<table style="width:100%;border-collapse:collapse;font-size:7.5pt;border:1px solid #cbd5e1;border-radius:4px;overflow:hidden;"><thead><tr><th ' . $thStyle . '>Attending Practitioner</th><th ' . $thStyleR . '>Encounters</th><th ' . $thStyleR . '>% Workload</th></tr></thead><tbody>' . $wlRows . '</tbody></table>';

            // 9. Duration Table
            $durRows = '';
            $durLabels = $staffProductivity['durationChartLabels'] ?? [];
            $durValues = $staffProductivity['durationChartData'] ?? [];
            foreach($durLabels as $di => $dName) {
                $mins = $durValues[$di] ?? 0;
                $status = $mins >= 20 ? '<span style="color:#d97706;font-weight:700;">Extended (Thorough)</span>' : ($mins >= 8 ? '<span style="color:#15803d;font-weight:600;">Optimal (Standard)</span>' : '<span style="color:#0284c7;font-weight:600;">Expedited / Brief</span>');
                $durRows .= '<tr' . ($di % 2 === 1 ? ' style="background:#f8fafc;"' : '') . '>';
                $durRows .= '<td style="padding:4px 6px;border:1px solid #e2e8f0;color:#334155;font-size:7.5pt;">' . addslashes($dName) . '</td>';
                $durRows .= '<td style="padding:4px 6px;border:1px solid #e2e8f0;color:#0f172a;font-weight:800;text-align:right;font-size:7.5pt;font-variant-numeric:tabular-nums;">' . number_format($mins, 1) . ' mins</td>';
                $durRows .= '<td style="padding:4px 6px;border:1px solid #e2e8f0;text-align:center;font-size:7.5pt;">' . $status . '</td></tr>';
            }
            if(empty($durRows)) {
                $durRows = '<tr><td colspan="3" style="padding:6px;text-align:center;color:#94a3b8;font-size:7.5pt;">No completed consultations to benchmark.</td></tr>';
            }
            $durTable = '<table style="width:100%;border-collapse:collapse;font-size:7.5pt;border:1px solid #cbd5e1;border-radius:4px;overflow:hidden;"><thead><tr><th ' . $thStyle . '>Practitioner</th><th ' . $thStyleR . '>Avg Duration</th><th ' . $thStyleC . '>Benchmarking</th></tr></thead><tbody>' . $durRows . '</tbody></table>';

            // 10. Encoding Speed Table
            $encRows = '';
            $encLabels = $staffProductivity['encodingChartLabels'] ?? [];
            $encValues = $staffProductivity['encodingChartData'] ?? [];
            foreach($encLabels as $ei => $eName) {
                $secs = $encValues[$ei] ?? 0;
                $status = $secs <= 60 ? '<span style="color:#15803d;font-weight:700;">Rapid (&lt;60s)</span>' : ($secs <= 120 ? '<span style="color:#0284c7;font-weight:600;">Standard (1-2m)</span>' : '<span style="color:#d97706;font-weight:600;">Paced (&gt;2m)</span>');
                $encRows .= '<tr' . ($ei % 2 === 1 ? ' style="background:#f8fafc;"' : '') . '>';
                $encRows .= '<td style="padding:4px 6px;border:1px solid #e2e8f0;color:#334155;font-size:7.5pt;">' . addslashes($eName) . '</td>';
                $encRows .= '<td style="padding:4px 6px;border:1px solid #e2e8f0;color:#0f172a;font-weight:800;text-align:right;font-size:7.5pt;font-variant-numeric:tabular-nums;">' . number_format($secs) . ' secs</td>';
                $encRows .= '<td style="padding:4px 6px;border:1px solid #e2e8f0;text-align:center;font-size:7.5pt;">' . $status . '</td></tr>';
            }
            if(empty($encRows)) {
                $encRows = '<tr><td colspan="3" style="padding:6px;text-align:center;color:#94a3b8;font-size:7.5pt;">No chart entries recorded for this timeframe.</td></tr>';
            }
            $encTable = '<table style="width:100%;border-collapse:collapse;font-size:7.5pt;border:1px solid #cbd5e1;border-radius:4px;overflow:hidden;"><thead><tr><th ' . $thStyle . '>Staff Member</th><th ' . $thStyleR . '>Avg EMR Speed</th><th ' . $thStyleC . '>Informatics Rating</th></tr></thead><tbody>' . $encRows . '</tbody></table>';
        @endphp

        // 3. Helper to make chart section with exact data summary and descriptive note
        function chartBlock(img, title, description, tableHtml) {
            if (!img && !tableHtml) return '';
            return '<div style="border:1px solid #cbd5e1;border-radius:6px;padding:12px;background:#ffffff;page-break-inside:avoid;margin-bottom:14px;box-shadow:0 1px 2px rgba(0,0,0,0.03);display:flex;flex-direction:column;justify-content:space-between;">' +
                '<div>' +
                    '<div style="font-size:8.5pt;font-weight:800;text-transform:uppercase;color:#1e293b;letter-spacing:0.04em;margin-bottom:6px;border-bottom:1px solid #f1f5f9;padding-bottom:4px;">' + title + '</div>' +
                    (img ? '<img src="' + img + '" style="width:100%;height:auto;max-height:200px;object-fit:contain;display:block;margin:0 auto 10px auto;">' : '') +
                    (tableHtml ? '<div style="margin:6px 0 8px 0;">' +
                        '<div style="font-size:6.8pt;font-weight:800;text-transform:uppercase;color:#64748b;margin-bottom:4px;letter-spacing:0.04em;">Accessible Data Summary (Exact Values):</div>' +
                        tableHtml +
                    '</div>' : '') +
                '</div>' +
                (description ? '<div style="font-size:7.3pt;color:#475569;line-height:1.45;background:#f8fafc;border-top:1px solid #e2e8f0;padding:6px 8px;border-radius:4px;margin-top:6px;">' +
                    '<strong style="color:#0f172a;font-weight:700;">Chart Description & Analytical Insight:</strong> ' + description +
                '</div>' : '') +
            '</div>';
        }

        // 4. Build report HTML
        const reportHTML = `
            <table class="rhu-print-table" style="display:table;width:100%;border-collapse:collapse;">
                <thead class="rhu-print-thead" style="display:table-header-group;">
                    <tr><td style="padding:0;border:none;">
                        <header style="width:100%;margin-bottom:12px;">
                            <div style="display:flex;align-items:center;justify-content:space-between;gap:16px;width:100%;padding:2px 0 6px 0;">
                                <div style="width:64px;height:64px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                    <img src="/assets/images/logo.png" alt="Seal" style="max-width:64px;max-height:64px;object-fit:contain;" onerror="this.style.display='none'">
                                </div>
                                <div style="flex:1;text-align:center;padding:0 4px;">
                                    <div style="font-size:8.5pt;font-weight:700;text-transform:uppercase;letter-spacing:0.08em;color:#475569;">{{ addslashes(\App\Models\SiteSetting::get('topbar_republic', 'Republic of the Philippines')) }}</div>
                                    <div style="font-size:8pt;font-weight:600;letter-spacing:0.04em;color:#64748b;">{{ addslashes(\App\Models\SiteSetting::get('topbar_province', 'Province of Cavite')) }}</div>
                                    <h2 style="font-size:13pt;font-weight:900;text-transform:uppercase;letter-spacing:0.04em;color:#0f172a;margin:3px 0 2px 0;">{{ addslashes(\App\Models\SiteSetting::get('topbar_municipality', 'Municipality of Silang')) }} — Rural Health Unit</h2>
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
                    <main style="font-family:'Inter',system-ui,sans-serif;">

                        <!-- Title -->
                        <div style="margin-bottom:14px;padding-bottom:10px;border-bottom:1px solid #e2e8f0;">
                            <h1 style="font-size:15pt;font-weight:800;color:#0f172a;letter-spacing:-0.02em;margin:0 0 2px 0;">Epidemiological & Operational Analytics Report</h1>
                            <p style="font-size:8.5pt;font-weight:500;color:#64748b;margin:0 0 10px 0;">Deep-dive facility metrics, demographics, and patient flow trends.</p>
                            <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:8px 14px;background:#f8fafc;border:1px solid #e2e8f0;border-radius:4px;padding:8px 12px;font-size:8pt;">
                                <div><div style="font-size:6.5pt;font-weight:800;text-transform:uppercase;letter-spacing:0.05em;color:#64748b;">Reporting Period</div><div style="font-weight:700;color:#0f172a;margin-top:1px;">{{ addslashes($printTimeframeLabel) }}</div></div>
                                <div><div style="font-size:6.5pt;font-weight:800;text-transform:uppercase;letter-spacing:0.05em;color:#64748b;">Facility</div><div style="font-weight:700;color:#0f172a;margin-top:1px;">{{ addslashes(\App\Models\SiteSetting::get('topbar_municipality', 'Municipality of Silang')) }} — RHU</div></div>
                                <div><div style="font-size:6.5pt;font-weight:800;text-transform:uppercase;letter-spacing:0.05em;color:#64748b;">Generated By</div><div style="font-weight:700;color:#0f172a;margin-top:1px;">{{ auth()->check() ? auth()->user()->name : 'System Generated' }}</div></div>
                                <div><div style="font-size:6.5pt;font-weight:800;text-transform:uppercase;letter-spacing:0.05em;color:#64748b;">Date & Time</div><div style="font-weight:700;color:#0f172a;margin-top:1px;">{{ now()->format('M d, Y h:i A') }}</div></div>
                            </div>
                        </div>

                        <!-- Quick Stats -->
                        <h3 style="font-size:11pt;font-weight:800;color:#0f172a;margin:16px 0 8px 0;text-transform:uppercase;letter-spacing:0.04em;">Summary Indicators</h3>
                        <table style="width:100%;border-collapse:collapse;margin-bottom:16px;font-size:8.5pt;">
                            <thead><tr>
                                <th {!! $thStyle !!}>Indicator</th>
                                <th {!! $thStyleR !!}>Value</th>
                            </tr></thead>
                            <tbody>
                                <tr><td style="padding:6px 10px;border:1px solid #e2e8f0;color:#334155;font-weight:700;">Total Master Registry Patients</td><td style="padding:6px 10px;border:1px solid #e2e8f0;color:#0f172a;font-weight:800;text-align:right;font-variant-numeric:tabular-nums;">{{ number_format($census['total_registered']) }}</td></tr>
                                <tr style="background:#f8fafc;"><td style="padding:6px 10px;border:1px solid #e2e8f0;color:#334155;font-weight:700;">New Registrations in Period</td><td style="padding:6px 10px;border:1px solid #e2e8f0;color:#0f172a;font-weight:800;text-align:right;font-variant-numeric:tabular-nums;">{{ number_format($census['new_registered']) }}</td></tr>
                                <tr><td style="padding:6px 10px;border:1px solid #e2e8f0;color:#334155;font-weight:700;">Unique Individuals Served</td><td style="padding:6px 10px;border:1px solid #e2e8f0;color:#0f172a;font-weight:800;text-align:right;font-variant-numeric:tabular-nums;">{{ number_format($census['unique_patients_served']) }}</td></tr>
                                <tr style="background:#f8fafc;"><td style="padding:6px 10px;border:1px solid #e2e8f0;color:#334155;font-weight:700;">Total Consultations</td><td style="padding:6px 10px;border:1px solid #e2e8f0;color:#0f172a;font-weight:800;text-align:right;font-variant-numeric:tabular-nums;">{{ number_format($totalPeriodVisits) }}</td></tr>
                                <tr><td style="padding:6px 10px;border:1px solid #e2e8f0;color:#334155;">Peak Patient Flow Window</td><td style="padding:6px 10px;border:1px solid #e2e8f0;color:#0f172a;font-weight:800;text-align:right;">{{ $busiestHourLabel }}</td></tr>
                                <tr style="background:#f8fafc;"><td style="padding:6px 10px;border:1px solid #e2e8f0;color:#334155;">Top Demographic Group</td><td style="padding:6px 10px;border:1px solid #e2e8f0;color:#0f172a;font-weight:800;text-align:right;">{{ $maxDemoGroup }}</td></tr>
                                <tr><td style="padding:6px 10px;border:1px solid #e2e8f0;color:#334155;">Prevalent Triage Priority</td><td style="padding:6px 10px;border:1px solid #e2e8f0;color:#0f172a;font-weight:800;text-align:right;">{{ $dominantSeverity }}</td></tr>
                                <tr style="background:#f8fafc;"><td style="padding:6px 10px;border:1px solid #e2e8f0;color:#334155;">Avg Consultation Duration</td><td style="padding:6px 10px;border:1px solid #e2e8f0;color:#0f172a;font-weight:800;text-align:right;font-variant-numeric:tabular-nums;">{{ $staffProductivity['avgDuration'] ?? 0 }} min</td></tr>
                                <tr><td style="padding:6px 10px;border:1px solid #e2e8f0;color:#334155;">Clinical Throughput</td><td style="padding:6px 10px;border:1px solid #e2e8f0;color:#0f172a;font-weight:800;text-align:right;font-variant-numeric:tabular-nums;">{{ $staffProductivity['patientsPerHour'] ?? 0 }} patients/hr</td></tr>
                                <tr style="background:#f8fafc;"><td style="padding:6px 10px;border:1px solid #e2e8f0;color:#334155;">Avg Queue Wait Time</td><td style="padding:6px 10px;border:1px solid #e2e8f0;color:#0f172a;font-weight:800;text-align:right;font-variant-numeric:tabular-nums;">{{ $staffProductivity['avgWaitTime'] ?? 0 }} min</td></tr>
                                <tr><td style="padding:6px 10px;border:1px solid #e2e8f0;color:#334155;">Cancellation Rate</td><td style="padding:6px 10px;border:1px solid #e2e8f0;color:{{ ($staffProductivity['cancellationRate'] ?? 0) > 10 ? '#dc2626' : '#0f172a' }};font-weight:800;text-align:right;font-variant-numeric:tabular-nums;">{{ $staffProductivity['cancellationRate'] ?? 0 }}%</td></tr>
                            </tbody>
                        </table>

                        <!-- ═════════ SECTION 1: VISIT VOLUME & PEAK HOURS ═════════ -->
                        <h3 style="font-size:11pt;font-weight:800;color:#0f172a;margin:20px 0 8px 0;text-transform:uppercase;letter-spacing:0.04em;">Section 1 &mdash; Visit Volume &amp; Peak Hours</h3>
                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-bottom:16px;">
                            ${chartBlock(chartImages.visitVolume, 'Visit Volume Over Time', 'Tracks total patient consultation traffic across the reporting period to identify daily trends, surges, and seasonal patterns for clinical staffing and medicine inventory planning.', {!! json_encode($vvTable) !!})}
                            ${chartBlock(chartImages.peakHours, 'Peak Hours Distribution', 'Displays patient arrival density by hour of the day. Identifies high-traffic triage windows (typically 8:00 AM - 11:00 AM) to optimize staff scheduling and reduce waiting hall congestion.', {!! json_encode($phTable) !!})}
                        </div>

                        <!-- ═════════ SECTION 2: STAFF WORKLOAD ═════════ -->
                        <h3 style="font-size:11pt;font-weight:800;color:#0f172a;margin:20px 0 8px 0;text-transform:uppercase;letter-spacing:0.04em;">Section 2 &mdash; Staff Workload Distribution</h3>
                        <div style="display:grid;grid-template-columns:1fr;gap:14px;margin-bottom:16px;">
                            ${chartBlock(chartImages.workload, 'Clinical Staff Workload', 'Quantifies patient consultations and clinical cases handled by doctors and healthcare staff, ensuring balanced clinical duty allocation and provider productivity monitoring.', {!! json_encode($wlTable) !!})}
                        </div>

                        <!-- ═════════ SECTION 3: PATIENT DEMOGRAPHICS ═════════ -->
                        <div style="page-break-before:auto;">
                            <h3 style="font-size:11pt;font-weight:800;color:#0f172a;margin:20px 0 8px 0;text-transform:uppercase;letter-spacing:0.04em;">Section 3 &mdash; Patient Demographics (Age-Sex Pyramid)</h3>
                            <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-bottom:16px;">
                                ${chartBlock(chartImages.ageSex, 'Age-Sex Demographic Pyramid', 'Epidemiological demographic pyramid classifying patient visits by age bracket and sex, highlighting pediatric, reproductive-age, and geriatric healthcare utilization across Silang.', {!! json_encode($demoTable) !!})}
                                ${chartBlock(null, 'Top Diagnoses & Morbidity Surveillance', 'Prevalent diseases recorded among attending patients during the reporting period, informing public health prevention and pharmaceutical stockpiling.', {!! json_encode($diagTable) !!})}
                            </div>
                        </div>

                        <!-- ═════════ SECTION 4: PATIENT CLASSIFICATION & TRIAGE SEVERITY ═════════ -->
                        <div style="page-break-before:auto;">
                            <h3 style="font-size:11pt;font-weight:800;color:#0f172a;margin:20px 0 8px 0;text-transform:uppercase;letter-spacing:0.04em;">Section 4 &mdash; Patient Classification &amp; Triage Severity</h3>
                            <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-bottom:16px;">
                                ${chartBlock(chartImages.classification, 'Patient Classification Breakdown', 'Categorizes patients by priority classification (Senior Citizen, 4Ps/Indigent, PWD, Pediatric, General), supporting municipal healthcare subsidy audits and PhilHealth primary care coverage.', {!! json_encode($classTable) !!})}
                                ${chartBlock(chartImages.severity, 'Triage Severity Distribution', 'Clinical triage acuity breakdown (Non-Urgent, Semi-Urgent, Urgent, Emergency), monitoring acute clinical severity to evaluate emergency readiness and doctor allocation.', {!! json_encode($sevTable) !!})}
                            </div>
                        </div>

                        <!-- ═════════ SECTION 5: BARANGAY HEATMAP ═════════ -->
                        <div style="page-break-before:auto;">
                            <h3 style="font-size:11pt;font-weight:800;color:#0f172a;margin:20px 0 8px 0;text-transform:uppercase;letter-spacing:0.04em;">Section 5 &mdash; Barangay Patient Heatmap &amp; Geographic Catchment</h3>
                            <div style="display:grid;grid-template-columns:1fr;gap:14px;margin-bottom:16px;">
                                ${chartBlock(chartImages.barangay, 'Barangay Patient Distribution', 'Geographic catchment mapping of patients across Silang barangays, identifying high-demand communities to guide mobile clinic deployments and health outreach programs.', {!! json_encode($bgyTable) !!})}
                            </div>
                        </div>

                        <!-- ═════════ SECTION 6: STAFF PRODUCTIVITY & PERFORMANCE ═════════ -->
                        <div style="page-break-before:auto;">
                            <h3 style="font-size:11pt;font-weight:800;color:#0f172a;margin:20px 0 8px 0;text-transform:uppercase;letter-spacing:0.04em;">Section 6 &mdash; Staff Productivity &amp; Operational Performance</h3>
                            
                            {{-- Staff Productivity KPI Cards --}}
                            <div style="display:grid;grid-template-columns:repeat(5,1fr);gap:10px;margin-bottom:16px;">
                                <div style="border:1px solid #cbd5e1;border-radius:6px;padding:10px 12px;background:#f8fafc;page-break-inside:avoid;">
                                    <div style="font-size:6.5pt;font-weight:800;text-transform:uppercase;letter-spacing:0.05em;color:#64748b;margin-bottom:4px;">Avg Duration</div>
                                    <div style="font-size:14pt;font-weight:900;color:#0f172a;font-variant-numeric:tabular-nums;">{{ $staffProductivity['avgDuration'] ?? 0 }}<span style="font-size:7.5pt;font-weight:600;color:#64748b;margin-left:2px;">min</span></div>
                                    <div style="font-size:6.5pt;color:#64748b;margin-top:2px;">Per patient encounter</div>
                                </div>
                                <div style="border:1px solid #cbd5e1;border-radius:6px;padding:10px 12px;background:#f8fafc;page-break-inside:avoid;">
                                    <div style="font-size:6.5pt;font-weight:800;text-transform:uppercase;letter-spacing:0.05em;color:#64748b;margin-bottom:4px;">Total Completed</div>
                                    <div style="font-size:14pt;font-weight:900;color:#0f172a;font-variant-numeric:tabular-nums;">{{ number_format($staffProductivity['totalPatients'] ?? 0) }}</div>
                                    <div style="font-size:6.5pt;color:#64748b;margin-top:2px;">Finished consultations</div>
                                </div>
                                <div style="border:1px solid #cbd5e1;border-radius:6px;padding:10px 12px;background:#f8fafc;page-break-inside:avoid;">
                                    <div style="font-size:6.5pt;font-weight:800;text-transform:uppercase;letter-spacing:0.05em;color:#64748b;margin-bottom:4px;">Throughput</div>
                                    <div style="font-size:14pt;font-weight:900;color:#0f172a;font-variant-numeric:tabular-nums;">{{ $staffProductivity['patientsPerHour'] ?? 0 }}<span style="font-size:7.5pt;font-weight:600;color:#64748b;margin-left:2px;">pts/hr</span></div>
                                    <div style="font-size:6.5pt;color:#64748b;margin-top:2px;">Patient processing rate</div>
                                </div>
                                <div style="border:1px solid #cbd5e1;border-radius:6px;padding:10px 12px;background:#f8fafc;page-break-inside:avoid;">
                                    <div style="font-size:6.5pt;font-weight:800;text-transform:uppercase;letter-spacing:0.05em;color:#64748b;margin-bottom:4px;">Avg Wait Time</div>
                                    <div style="font-size:14pt;font-weight:900;color:#0f172a;font-variant-numeric:tabular-nums;">{{ $staffProductivity['avgWaitTime'] ?? 0 }}<span style="font-size:7.5pt;font-weight:600;color:#64748b;margin-left:2px;">min</span></div>
                                    <div style="font-size:6.5pt;color:#64748b;margin-top:2px;">From queue to consult</div>
                                </div>
                                <div style="border:1px solid #cbd5e1;border-radius:6px;padding:10px 12px;background:#f8fafc;page-break-inside:avoid;">
                                    <div style="font-size:6.5pt;font-weight:800;text-transform:uppercase;letter-spacing:0.05em;color:#64748b;margin-bottom:4px;">Cancellation Rate</div>
                                    <div style="font-size:14pt;font-weight:900;color:{{ ($staffProductivity['cancellationRate'] ?? 0) > 10 ? '#dc2626' : '#0f172a' }};font-variant-numeric:tabular-nums;">{{ $staffProductivity['cancellationRate'] ?? 0 }}%</div>
                                    <div style="font-size:6.5pt;color:#64748b;margin-top:2px;">Cancelled vs completed</div>
                                </div>
                            </div>

                            {{-- Sub-section A: Clinical Performance (Doctors/Nurses) --}}
                            <div style="margin-bottom:10px;padding:6px 10px;background:#f0fdf4;border:1px solid #bbf7d0;border-radius:4px;">
                                <div style="font-size:8pt;font-weight:800;color:#166534;text-transform:uppercase;letter-spacing:0.04em;">&#9654; Clinical Performance &mdash; Doctors / Nurses</div>
                                <div style="font-size:7pt;color:#15803d;margin-top:1px;">Average consultation duration per attending physician, measuring clinical thoroughness vs patient throughput.</div>
                            </div>
                            <div style="display:grid;grid-template-columns:1fr;gap:14px;margin-bottom:16px;">
                                ${chartBlock(chartImages.durationStaff, 'Avg Consultation Duration per Provider (Minutes)', 'Average examination and consultation minutes per provider, benchmarking clinical thoroughness against patient throughput and queue efficiency.', {!! json_encode($durTable) !!})}
                            </div>

                            {{-- Sub-section B: Front-Line Efficiency (Triage/Info Desk) --}}
                            <div style="margin-bottom:10px;padding:6px 10px;background:#eff6ff;border:1px solid #bfdbfe;border-radius:4px;">
                                <div style="font-size:8pt;font-weight:800;color:#1e40af;text-transform:uppercase;letter-spacing:0.04em;">&#9654; Front-Line Efficiency &mdash; Triage / Info Desk</div>
                                <div style="font-size:7pt;color:#2563eb;margin-top:1px;">Average EMR charting turnaround time at the triage and information desk, assessing front-line processing speed.</div>
                            </div>
                            <div style="display:grid;grid-template-columns:1fr;gap:14px;margin-bottom:16px;">
                                ${chartBlock(chartImages.encodingSpeed, 'Triage Encoding Speed per Nurse (Seconds)', 'Average electronic medical records (EMR) charting turnaround time per consultation at the triage and information desk, assessing front-line processing speed and digital health compliance.', {!! json_encode($encTable) !!})}
                            </div>
                        </div>

                    </main>
                </td></tr></tbody>

                <tfoot style="display:table-footer-group;"><tr><td style="padding:0;border:none;">
                    <footer style="width:100%;padding-top:8px;margin-top:8px;">
                        <div style="width:100%;border-top:1px solid #cbd5e1;margin-bottom:6px;"></div>
                        <div style="display:flex;justify-content:space-between;align-items:flex-start;gap:16px;font-size:7pt;color:#64748b;line-height:1.35;">
                            <div style="flex:1;max-width:65%;text-align:justify;">CONFIDENTIAL HEALTH & ADMINISTRATIVE RECORD — Contains protected health information subject to Republic Act No. 10173 (Data Privacy Act of 2012). Unauthorized disclosure, copying, or distribution is strictly prohibited.</div>
                            <div style="flex-shrink:0;text-align:right;font-weight:600;color:#334155;"><div>Epidemiological & Operational Analytics Report</div><div>Timeframe: {{ addslashes($printTimeframeLabel) }}</div><div>Printed: {{ now()->format('M d, Y h:i A') }} &bull; {{ auth()->check() ? auth()->user()->name : 'System' }}</div></div>
                        </div>
                    </footer>
                </td></tr></tfoot>
            </table>
        `;

        window.printIsolated(reportHTML, {
            title: 'Epidemiological & Operational Analytics Report — {{ addslashes($printTimeframeLabel) }}',
            paperSize: 'auto'
        });
    }
</script>
@endsection
