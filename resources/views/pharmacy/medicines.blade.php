@extends('layouts.pharmacy')

@section('header', 'Medicine List')

@section('content')
<div class="max-w-7xl mx-auto space-y-6 sm:space-y-8 pb-16">
    
    <!-- Breadcrumb Navigation -->
    <nav class="flex items-center justify-between gap-2 text-xs font-medium text-slate-500 dark:text-slate-400 py-2.5 border-b border-slate-200/70 dark:border-slate-800 print:hidden" aria-label="Breadcrumb">
        <div class="flex items-center gap-2 flex-wrap">
            <a href="{{ route('pharmacy.dashboard') }}" class="hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors flex items-center gap-1.5 shrink-0">
                <svg class="w-3.5 h-3.5 text-slate-400 dark:text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                <span>Home</span>
            </a>
            <span class="text-slate-300 dark:text-slate-700">/</span>
            <span class="text-slate-500 dark:text-slate-400">Pharmacy</span>
            <span class="text-slate-300 dark:text-slate-700">/</span>
            <span class="text-slate-800 dark:text-slate-200 font-bold">Inventory Management</span>
            <span class="text-slate-300 dark:text-slate-700">/</span>
            <span class="text-emerald-700 dark:text-emerald-400 font-extrabold">Formulary</span>
        </div>
        <div class="hidden sm:flex items-center gap-2 text-[11px] font-semibold text-slate-400 dark:text-slate-500">
            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
            <span>Live Formulary Sync</span>
        </div>
    </nav>

    <!-- Header Section (Clean Healthcare Visual Identity) -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 pb-6 border-b border-slate-200/80 dark:border-slate-800 print:hidden">
        <div>
            <div class="flex items-center gap-3.5">
                <div class="w-11 h-11 sm:w-12 sm:h-12 rounded-2xl bg-gradient-to-br from-emerald-500/20 to-teal-500/20 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0 border border-emerald-500/30 shadow-xs">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                    </svg>
                </div>
                <div>
                    <h1 class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white tracking-tight flex items-center gap-2.5">
                        <span>Inventory Overview & Formulary</span>
                    </h1>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Manage medicines, monitor active batch quantities, and review expiration dates.</p>
                </div>
            </div>
        </div>

        <div class="flex items-center gap-3 w-full sm:w-auto">
            <!-- Print Formulary & Active Stock Sheet -->
            <button type="button" onclick="window.print()" class="flex-1 sm:flex-initial h-10.5 px-4 py-3 rounded-xl border border-slate-300 dark:border-slate-700 bg-white hover:bg-slate-50 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 font-bold text-xs shadow-2xs hover:shadow-xs transition-all active:scale-95 cursor-pointer flex items-center justify-center gap-2 group" title="Print Official Pharmacy Formulary & Active Stock Sheet">
                <svg class="w-4 h-4 shrink-0 text-slate-500 dark:text-slate-400 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                </svg>
                <span>Print Formulary</span>
            </button>

            <!-- Export Stock CSV -->
            <button onclick="exportStockCsv(this)" id="exportStockCsvBtn" class="flex-1 sm:flex-initial h-10.5 px-5 py-3 rounded-xl border border-emerald-600/30 dark:border-emerald-500/30 bg-emerald-50/70 hover:bg-emerald-100/90 dark:bg-emerald-950/40 dark:hover:bg-emerald-900/60 text-emerald-800 dark:text-emerald-300 font-bold text-xs shadow-2xs hover:shadow-xs transition-all active:scale-95 cursor-pointer flex items-center justify-center gap-2 disabled:opacity-60 disabled:cursor-not-allowed disabled:active:scale-100 group">
                <svg id="exportStockIcon" class="w-4 h-4 shrink-0 text-emerald-600 dark:text-emerald-400 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                <svg id="exportStockSpinner" class="w-4 h-4 shrink-0 animate-spin hidden text-emerald-600 dark:text-emerald-400" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                <span id="exportStockText">Export Stock (CSV)</span>
            </button>
            
            <!-- Add New Medicine -->
            <button @click="$dispatch('open-add-medicine')" class="flex-1 sm:flex-initial h-10.5 px-5 py-3 rounded-xl bg-gradient-to-r from-emerald-600 via-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 text-white text-xs font-bold shadow-md shadow-emerald-600/25 flex items-center justify-center gap-2 active:scale-95 transition-all cursor-pointer">
                <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
                <span>Add New Medicine</span>
            </button>
        </div>
    </div>

    <x-print-layout 
        :isFullPage="false"
        title="Official Pharmacy Formulary & Stock Report"
        subtitle="Active medicine inventory, unit formulations, batch quantities, and expiration schedules."
        :period="now()->format('F Y')"
        :generatedBy="auth()->check() ? auth()->user()->name : 'Pharmacist in Charge'"
    >
    <div class="space-y-6 sm:space-y-8">

        <!-- Balanced Inventory Health & Surveillance Cards (4-Column Clean Deck) -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-5 print:hidden">
            <!-- Card 1: Registered Formulary Items -->
            <a href="{{ route('pharmacy.medicines') }}" class="group bg-white dark:bg-slate-900 rounded-2xl sm:rounded-3xl p-5 sm:p-6 border border-slate-200/90 dark:border-slate-800 shadow-2xs hover:shadow-md hover:border-emerald-500/30 dark:hover:border-emerald-500/30 transition-all flex flex-col justify-between min-h-[145px] relative overflow-hidden">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-10 h-10 rounded-xl bg-emerald-100/80 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center border border-emerald-500/20 shadow-2xs shrink-0 group-hover:scale-105 transition-transform">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" /></svg>
                    </div>
                    <span class="text-[10px] font-black uppercase tracking-wider text-emerald-700 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-950/50 px-2.5 py-0.5 rounded-full border border-emerald-200/60 dark:border-emerald-800/40">
                        Formulary
                    </span>
                </div>
                <div>
                    <p class="text-2xl sm:text-3xl font-black text-slate-900 dark:text-white tracking-tight">{{ $medicines->total() }}</p>
                    <p class="text-xs font-bold text-slate-700 dark:text-slate-300 mt-1">Formulary Items</p>
                    <p class="text-[11px] text-slate-400 dark:text-slate-500 mt-0.5">
                        {{ max(0, $medicines->total() - ($archivedMedicinesCount ?? 0)) }} active · {{ $archivedMedicinesCount ?? 0 }} archived
                    </p>
                </div>
            </a>

            <!-- Card 2: Expiration Safety & Critical Alerts -->
            @php
                $hasExpired = ($expiredBatchesCount ?? 0) > 0;
                $hasExpiringSoon = ($expiringSoonCount ?? 0) > 0;
            @endphp
            @if($hasExpired)
                <a href="{{ route('pharmacy.medicines', ['status_filter' => 'expired']) }}" class="group bg-rose-50/70 hover:bg-rose-100/70 dark:bg-rose-950/30 dark:hover:bg-rose-950/50 rounded-2xl sm:rounded-3xl p-5 sm:p-6 border border-rose-200/90 dark:border-rose-800/60 shadow-2xs hover:shadow-md transition-all flex flex-col justify-between min-h-[145px]">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-10 h-10 rounded-xl bg-rose-100 dark:bg-rose-900/60 text-rose-600 dark:text-rose-400 flex items-center justify-center border border-rose-500/20 shadow-2xs shrink-0 group-hover:scale-105 transition-transform">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <span class="relative flex h-2.5 w-2.5">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-rose-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-rose-500"></span>
                        </span>
                    </div>
                    <div>
                        <p class="text-2xl sm:text-3xl font-black text-rose-700 dark:text-rose-400 tracking-tight">{{ $expiredBatchesCount }}</p>
                        <p class="text-xs font-bold text-rose-900 dark:text-rose-300 mt-1">Expired Batches</p>
                        <p class="text-[11px] text-rose-600 dark:text-rose-400 font-semibold mt-0.5">Purge / write-off required &rarr;</p>
                    </div>
                </a>
            @elseif($hasExpiringSoon)
                <a href="{{ route('pharmacy.medicines', ['status_filter' => 'expiring_soon']) }}" class="group bg-amber-50/70 hover:bg-amber-100/70 dark:bg-amber-950/30 dark:hover:bg-amber-950/50 rounded-2xl sm:rounded-3xl p-5 sm:p-6 border border-amber-200/90 dark:border-amber-800/60 shadow-2xs hover:shadow-md transition-all flex flex-col justify-between min-h-[145px]">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-10 h-10 rounded-xl bg-amber-100 dark:bg-amber-900/60 text-amber-600 dark:text-amber-400 flex items-center justify-center border border-amber-500/20 shadow-2xs shrink-0 group-hover:scale-105 transition-transform">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <span class="relative flex h-2.5 w-2.5">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-amber-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-amber-500"></span>
                        </span>
                    </div>
                    <div>
                        <p class="text-2xl sm:text-3xl font-black text-amber-700 dark:text-amber-400 tracking-tight">{{ $expiringSoonCount }}</p>
                        <p class="text-xs font-bold text-amber-900 dark:text-amber-300 mt-1">Expiring Soon (&lt;30d)</p>
                        <p class="text-[11px] text-amber-600 dark:text-amber-400 font-semibold mt-0.5">Review active batches &rarr;</p>
                    </div>
                </a>
            @else
                <div class="bg-white dark:bg-slate-900 rounded-2xl sm:rounded-3xl p-5 sm:p-6 border border-slate-200/90 dark:border-slate-800 shadow-2xs flex flex-col justify-between min-h-[145px]">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-10 h-10 rounded-xl bg-emerald-100/80 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center border border-emerald-500/20 shadow-2xs shrink-0">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <span class="text-[10px] font-black uppercase tracking-wider text-emerald-700 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-950/50 px-2.5 py-0.5 rounded-full border border-emerald-200/60 dark:border-emerald-800/40">
                            In-Date
                        </span>
                    </div>
                    <div>
                        <p class="text-2xl sm:text-3xl font-black text-emerald-700 dark:text-emerald-400 tracking-tight">0</p>
                        <p class="text-xs font-bold text-slate-700 dark:text-slate-300 mt-1">Zero Expired Lots</p>
                        <p class="text-[11px] text-slate-400 dark:text-slate-500 mt-0.5">All active stock safe & compliant</p>
                    </div>
                </div>
            @endif

            <!-- Card 3: 30–60 Day Horizon Watchlist -->
            <div class="bg-white dark:bg-slate-900 rounded-2xl sm:rounded-3xl p-5 sm:p-6 border border-slate-200/90 dark:border-slate-800 shadow-2xs flex flex-col justify-between min-h-[145px]">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-10 h-10 rounded-xl bg-sky-100/80 dark:bg-sky-950/60 text-sky-600 dark:text-sky-400 flex items-center justify-center border border-sky-500/20 shadow-2xs shrink-0">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    </div>
                    <span class="text-[10px] font-black uppercase tracking-wider text-sky-700 dark:text-sky-400 bg-sky-50 dark:bg-sky-950/50 px-2.5 py-0.5 rounded-full border border-sky-200/60 dark:border-sky-800/40">
                        Horizon
                    </span>
                </div>
                <div>
                    <p class="text-2xl sm:text-3xl font-black text-slate-900 dark:text-white tracking-tight">{{ $expiring60Count ?? 0 }}</p>
                    <p class="text-xs font-bold text-slate-700 dark:text-slate-300 mt-1">30–60d Expiry Watch</p>
                    <p class="text-[11px] text-slate-400 dark:text-slate-500 mt-0.5">Restocking forecast window</p>
                </div>
            </div>

            <!-- Card 4: Written-Off / Disposed Batches (Direct Link to Audit Vault) -->
            <a href="{{ route('pharmacy.written-off') }}" class="group bg-white dark:bg-slate-900 rounded-2xl sm:rounded-3xl p-5 sm:p-6 border border-slate-200/90 dark:border-slate-800 shadow-2xs hover:shadow-md hover:border-slate-300 dark:hover:border-slate-700 transition-all flex flex-col justify-between min-h-[145px]">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-10 h-10 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 flex items-center justify-center border border-slate-200 dark:border-slate-700 shadow-2xs shrink-0 group-hover:scale-105 transition-transform">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    </div>
                    <span class="text-[10px] font-black uppercase tracking-wider text-slate-600 dark:text-slate-400 bg-slate-100 dark:bg-slate-800 px-2.5 py-0.5 rounded-full border border-slate-200 dark:border-slate-700">
                        Audit Log
                    </span>
                </div>
                <div>
                    <p class="text-2xl sm:text-3xl font-black text-slate-900 dark:text-white tracking-tight">{{ $disposedBatchesCount ?? 0 }}</p>
                    <p class="text-xs font-bold text-slate-700 dark:text-slate-300 mt-1">Written-Off Batches</p>
                    <p class="text-[11px] text-emerald-600 dark:text-emerald-400 font-semibold mt-0.5 group-hover:underline">View audit trail &rarr;</p>
                </div>
            </a>
        </div>

        <!-- High-Priority Expiration Alert Banner (If Any Expired Batches Exist) -->
        @if(($expiredBatchesCount ?? 0) > 0)
            <div class="p-4 sm:p-5 rounded-2xl sm:rounded-3xl bg-rose-50/90 dark:bg-rose-950/40 border border-rose-200/90 dark:border-rose-900/60 shadow-2xs flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 text-rose-900 dark:text-rose-200 print:hidden">
                <div class="flex items-center gap-3.5">
                    <div class="w-10 h-10 rounded-xl bg-rose-100 dark:bg-rose-900/60 text-rose-600 dark:text-rose-400 flex items-center justify-center shrink-0 border border-rose-300 dark:border-rose-800 shadow-2xs">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    </div>
                    <div>
                        <p class="text-xs sm:text-sm font-extrabold text-rose-900 dark:text-rose-200">
                            Clinical Safety Action: {{ $expiredBatchesCount }} batch(es) have expired in clinic storage
                        </p>
                        <p class="text-[11px] text-rose-700 dark:text-rose-400 mt-0.5">
                            Dispensing expired stock violates pharmacy regulations. Please filter by expired batches and initiate formal write-off.
                        </p>
                    </div>
                </div>
                <a href="{{ route('pharmacy.medicines', ['status_filter' => 'expired']) }}" class="h-9 px-4 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs shadow-sm flex items-center gap-1.5 transition-all active:scale-95 shrink-0 cursor-pointer">
                    <span>Filter Expired Batches</span>
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </a>
            </div>
        @endif

        <!-- Search, Filter & Quick Segment Bar -->
        <div class="bg-white/95 dark:bg-slate-900/95 backdrop-blur-md rounded-3xl border border-slate-200/90 dark:border-slate-800 p-5 sm:p-6 shadow-xs space-y-5 print:hidden">
            <!-- Top Search Row -->
            <form id="filterForm" action="{{ route('pharmacy.medicines') }}" method="GET" class="flex flex-col md:flex-row gap-3.5 items-center justify-between">
                <div class="relative flex-1 w-full">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400 dark:text-slate-500">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                    </div>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search brand name, generic name..." 
                        oninput="clearTimeout(this.timer); this.timer = setTimeout(() => { this.form.submit(); }, 350);"
                        onkeydown="if(event.key === 'Enter') { clearTimeout(this.timer); this.form.submit(); } else if(event.key === 'Escape') { this.value = ''; this.form.submit(); }"
                        class="no-uppercase h-11 sm:h-12 pl-10 pr-10 block w-full rounded-xl border border-slate-300/80 dark:border-slate-700 bg-slate-50/70 dark:bg-slate-800/60 text-slate-900 dark:text-white text-sm font-semibold focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 shadow-2xs transition-all placeholder:text-slate-400 placeholder:font-normal"
                        style="text-transform: none !important;">
                    @if(request('search'))
                        <button type="button" onclick="const input = this.previousElementSibling; input.value = ''; input.form.submit();" class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition-colors cursor-pointer" title="Clear Search">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    @endif
                </div>

                <div class="flex items-center gap-3 w-full md:w-auto">
                    @php
                        $formFilterOptions = ['all' => 'All Dosage Forms'];
                        foreach($forms as $formItem) {
                            $formFilterOptions[$formItem] = ucfirst($formItem);
                        }
                    @endphp
                    <div class="w-full sm:w-60">
                        <x-select 
                            name="form_filter" 
                            :options="$formFilterOptions" 
                            :value="request('form_filter', 'all')"
                            @change="$el.closest('form').submit()"
                            class="!h-11 sm:!h-12 font-semibold"
                        />
                    </div>

                    @if(request('status_filter') || request('search') || (request('form_filter') && request('form_filter') !== 'all'))
                        <a href="{{ route('pharmacy.medicines') }}" class="h-11 sm:h-12 px-4 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-300 text-xs font-bold transition-colors flex items-center gap-1.5 shadow-2xs shrink-0 cursor-pointer" title="Reset all search & filters">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                            <span>Reset</span>
                        </a>
                    @endif
                </div>
                
                <input type="hidden" name="per_page" id="medicines_per_page" value="{{ request('per_page', 10) }}">
                @if(request('status_filter'))
                    <input type="hidden" name="status_filter" value="{{ request('status_filter') }}">
                @endif
            </form>

            <!-- Quick Status Filter Pills (One-Click Categorization) -->
            <div class="pt-4 mt-2 border-t border-slate-100 dark:border-slate-800/80 flex items-center gap-2.5 overflow-x-auto pb-1 sm:pb-0 text-xs font-bold custom-scrollbar">
                <span class="text-[11px] font-extrabold uppercase tracking-wider text-slate-400 dark:text-slate-500 shrink-0 mr-1">Filter by Status:</span>

                @php
                    $activeStatus = request('status_filter');
                    $makeFilterUrl = function($status) {
                        $params = request()->query();
                        if ($status === null) {
                            unset($params['status_filter']);
                        } else {
                            $params['status_filter'] = $status;
                        }
                        unset($params['page']);
                        return route('pharmacy.medicines', $params);
                    };
                @endphp

                <!-- All -->
                <a href="{{ $makeFilterUrl(null) }}" class="px-3.5 py-2 rounded-xl border transition-all shrink-0 cursor-pointer {{ !$activeStatus ? 'bg-emerald-600 text-white border-emerald-600 shadow-2xs' : 'bg-slate-100/80 hover:bg-slate-200/80 dark:bg-slate-800/60 dark:hover:bg-slate-700/60 text-slate-700 dark:text-slate-300 border-slate-200/80 dark:border-slate-700/60' }}">
                    All Formulary
                </a>

                <!-- Expiring Soon -->
                <a href="{{ $makeFilterUrl('expiring_soon') }}" class="px-3.5 py-2 rounded-xl border transition-all shrink-0 flex items-center gap-1.5 cursor-pointer {{ $activeStatus === 'expiring_soon' ? 'bg-amber-600 text-white border-amber-600 shadow-2xs' : 'bg-slate-100/80 hover:bg-amber-50 dark:bg-slate-800/60 dark:hover:bg-amber-950/40 text-slate-700 dark:text-slate-300 hover:text-amber-800 dark:hover:text-amber-300 border-slate-200/80 dark:border-slate-700/60 hover:border-amber-300' }}">
                    <span>Expiring Soon (&lt;30d)</span>
                    @if(($expiringSoonCount ?? 0) > 0)
                        <span class="px-1.5 py-0.2 rounded-full text-[10px] {{ $activeStatus === 'expiring_soon' ? 'bg-white/20 text-white' : 'bg-amber-200 text-amber-900 dark:bg-amber-900/60 dark:text-amber-200' }}">{{ $expiringSoonCount }}</span>
                    @endif
                </a>

                <!-- Expired Batches -->
                <a href="{{ $makeFilterUrl('expired') }}" class="px-3.5 py-2 rounded-xl border transition-all shrink-0 flex items-center gap-1.5 cursor-pointer {{ $activeStatus === 'expired' ? 'bg-rose-600 text-white border-rose-600 shadow-2xs' : 'bg-slate-100/80 hover:bg-rose-50 dark:bg-slate-800/60 dark:hover:bg-rose-950/40 text-slate-700 dark:text-slate-300 hover:text-rose-800 dark:hover:text-rose-300 border-slate-200/80 dark:border-slate-700/60 hover:border-rose-300' }}">
                    <span>Expired Lots</span>
                    @if(($expiredBatchesCount ?? 0) > 0)
                        <span class="px-1.5 py-0.2 rounded-full text-[10px] {{ $activeStatus === 'expired' ? 'bg-white/20 text-white' : 'bg-rose-200 text-rose-900 dark:bg-rose-900/60 dark:text-rose-200' }}">{{ $expiredBatchesCount }}</span>
                    @endif
                </a>

                <!-- Low Stock -->
                <a href="{{ $makeFilterUrl('low_stock') }}" class="px-3.5 py-2 rounded-xl border transition-all shrink-0 cursor-pointer {{ $activeStatus === 'low_stock' ? 'bg-blue-600 text-white border-blue-600 shadow-2xs' : 'bg-slate-100/80 hover:bg-blue-50 dark:bg-slate-800/60 dark:hover:bg-blue-950/40 text-slate-700 dark:text-slate-300 hover:text-blue-800 dark:hover:text-blue-300 border-slate-200/80 dark:border-slate-700/60 hover:border-blue-300' }}">
                    Low Stock (&lt;20)
                </a>

                <!-- Out of Stock -->
                <a href="{{ $makeFilterUrl('out_of_stock') }}" class="px-3.5 py-2 rounded-xl border transition-all shrink-0 cursor-pointer {{ $activeStatus === 'out_of_stock' ? 'bg-slate-800 text-white border-slate-800 shadow-2xs dark:bg-slate-700' : 'bg-slate-100/80 hover:bg-slate-200 dark:bg-slate-800/60 dark:hover:bg-slate-700/60 text-slate-700 dark:text-slate-300 border-slate-200/80 dark:border-slate-700/60' }}">
                    Out of Stock
                </a>

                <!-- Archived -->
                <a href="{{ $makeFilterUrl('archived') }}" class="px-3.5 py-2 rounded-xl border transition-all shrink-0 flex items-center gap-1.5 cursor-pointer {{ $activeStatus === 'archived' ? 'bg-purple-600 text-white border-purple-600 shadow-2xs' : 'bg-slate-100/80 hover:bg-purple-50 dark:bg-slate-800/60 dark:hover:bg-purple-950/40 text-slate-700 dark:text-slate-300 hover:text-purple-800 dark:hover:text-purple-300 border-slate-200/80 dark:border-slate-700/60 hover:border-purple-300' }}">
                    <span>Archived</span>
                    @if(($archivedMedicinesCount ?? 0) > 0)
                        <span class="px-1.5 py-0.2 rounded-full text-[10px] {{ $activeStatus === 'archived' ? 'bg-white/20 text-white' : 'bg-purple-200 text-purple-900 dark:bg-purple-900/60 dark:text-purple-200' }}">{{ $archivedMedicinesCount }}</span>
                    @endif
                </a>
            </div>
        </div>

        <!-- Active Filter Feedback Bar -->
        @if(request('status_filter'))
            @php
                $filterLabels = [
                    'expired' => ['label' => 'Expired Batches', 'class' => 'bg-rose-50 text-rose-800 border-rose-200 dark:bg-rose-950/40 dark:text-rose-300 dark:border-rose-800/60'],
                    'expiring_soon' => ['label' => 'Expiring Soon (Within 30 Days)', 'class' => 'bg-amber-50 text-amber-800 border-amber-200 dark:bg-amber-950/40 dark:text-amber-300 dark:border-amber-800/60'],
                    'low_stock' => ['label' => 'Low Stock (< 20 units)', 'class' => 'bg-blue-50 text-blue-800 border-blue-200 dark:bg-blue-950/40 dark:text-blue-300 dark:border-blue-800/60'],
                    'out_of_stock' => ['label' => 'Out of Stock Items', 'class' => 'bg-slate-100 text-slate-800 border-slate-200 dark:bg-slate-800 dark:text-slate-300 dark:border-slate-700'],
                    'archived' => ['label' => 'Archived Formulary Items', 'class' => 'bg-purple-50 text-purple-800 border-purple-200 dark:bg-purple-950/40 dark:text-purple-300 dark:border-purple-800/60'],
                ];
                $currentFilter = $filterLabels[request('status_filter')] ?? null;
            @endphp
            @if($currentFilter)
                <div class="p-3.5 px-5 rounded-2xl border {{ $currentFilter['class'] }} flex items-center justify-between shadow-2xs text-xs print:hidden">
                    <span class="font-bold flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-current"></span>
                        <span>Active Status Filter:</span>
                        <span class="underline font-black">{{ $currentFilter['label'] }}</span>
                        <span>({{ $medicines->total() }} matching records)</span>
                    </span>
                    <a href="{{ route('pharmacy.medicines', request()->except('status_filter')) }}" class="font-black hover:underline cursor-pointer flex items-center gap-1">
                        <span>Clear Status Filter</span>
                        <span>&times;</span>
                    </a>
                </div>
            @endif
        @endif

        <!-- Redesigned Medicines Catalog Table Container -->
        <div class="bg-white dark:bg-slate-900 rounded-3xl shadow-xs border border-slate-200/90 dark:border-slate-800 overflow-hidden">
            <!-- Catalog Header Strip -->
            <div class="px-6 sm:px-7 py-5 border-b border-slate-100 dark:border-slate-800/80 bg-slate-50/70 dark:bg-slate-900/60 flex flex-col sm:flex-row sm:items-center justify-between gap-3.5">
                <div>
                    <h3 class="text-base font-extrabold text-slate-900 dark:text-white flex items-center gap-2.5">
                        <span>Medicines Catalog</span>
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-100 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 border border-emerald-200/60 dark:border-emerald-800/40">
                            {{ $medicines->total() }} Registered
                        </span>
                    </h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 font-medium mt-1">
                        Showing {{ $medicines->firstItem() ?? 0 }} to {{ $medicines->lastItem() ?? 0 }} of {{ $medicines->total() }} formulary items
                    </p>
                </div>
                
                <div class="flex items-center gap-3.5 text-xs text-slate-500 dark:text-slate-400 font-medium print:hidden">
                    <span class="inline-flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-emerald-500"></span> In Stock</span>
                    <span class="inline-flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-amber-500"></span> Low / Near Expiry</span>
                    <span class="inline-flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-rose-500"></span> Expired</span>
                </div>
            </div>

            <!-- Table View -->
            <div class="overflow-x-auto custom-scrollbar">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50/90 dark:bg-slate-950/80 text-slate-500 dark:text-slate-400 text-[11px] font-extrabold uppercase tracking-wider border-b border-slate-200/80 dark:border-slate-800">
                            <th class="py-4 px-4 sm:px-6 w-16 text-center">#</th>
                            <th class="py-4 px-5 sm:px-6">Brand & Formulary Item</th>
                            <th class="py-4 px-5 sm:px-6">Form & Category</th>
                            <th class="py-4 px-5 sm:px-6">Earliest Expiry</th>
                            <th class="py-4 px-5 sm:px-6 text-center">Total Stock</th>
                            <th class="py-4 px-5 sm:px-6 text-right print:hidden min-w-[320px]">Actions & Lots</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800 text-sm">
                        @forelse($medicines as $medicine)
                            @php
                                $totalStock = $medicine->total_stock;
                                $hasExpired = $medicine->batches->contains(function ($batch) {
                                    return $batch->quantity > 0 && $batch->status !== 'disposed' && $batch->expiration_date < now();
                                });
                                $hasExpiringSoon = $medicine->batches->contains(function ($batch) {
                                    return $batch->quantity > 0 && $batch->status !== 'disposed' && $batch->expiration_date >= now() && $batch->expiration_date <= now()->addDays(30);
                                });

                                if (! $medicine->is_active) {
                                    $rowClass = "hover:bg-slate-50/70 dark:hover:bg-slate-800/40 transition-colors border-l-4 border-slate-400 opacity-75 bg-slate-50/40 dark:bg-slate-900/30";
                                    $statusBadge = '<span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-slate-200 text-slate-700 dark:bg-slate-800 dark:text-slate-300 border border-slate-300 dark:border-slate-700 shadow-2xs uppercase">Archived</span>';
                                } elseif ($hasExpired) {
                                    $rowClass = "hover:bg-rose-50/40 dark:hover:bg-rose-950/20 transition-colors border-l-4 border-rose-500 bg-rose-50/20 dark:bg-rose-950/10";
                                    $statusBadge = '<span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-rose-100 text-rose-800 dark:bg-rose-950/60 dark:text-rose-300 border border-rose-200 dark:border-rose-800/60 shadow-2xs uppercase">Expired Batch</span>';
                                } elseif ($totalStock == 0) {
                                    $rowClass = "hover:bg-slate-50 dark:hover:bg-slate-800/40 transition-colors border-l-4 border-slate-400";
                                    $statusBadge = '<span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-slate-200 text-slate-700 dark:bg-slate-800 dark:text-slate-300 border border-slate-300 dark:border-slate-700 shadow-2xs uppercase">Out of Stock</span>';
                                } elseif ($hasExpiringSoon) {
                                    $rowClass = "hover:bg-amber-50/40 dark:hover:bg-amber-950/20 transition-colors border-l-4 border-amber-500 bg-amber-50/20 dark:bg-amber-950/10";
                                    $statusBadge = '<span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-amber-100 text-amber-800 dark:bg-amber-950/60 dark:text-amber-300 border border-amber-200 dark:border-amber-800/60 shadow-2xs uppercase animate-pulse">Expiring Soon</span>';
                                } elseif ($totalStock < 20) {
                                    $rowClass = "hover:bg-blue-50/40 dark:hover:bg-blue-950/20 transition-colors border-l-4 border-blue-500 bg-blue-50/20 dark:bg-blue-950/10";
                                    $statusBadge = '<span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-blue-100 text-blue-800 dark:bg-blue-950/60 dark:text-blue-300 border border-blue-200 dark:border-blue-800/60 shadow-2xs uppercase">Low Stock</span>';
                                } else {
                                    $rowClass = "hover:bg-emerald-50/30 dark:hover:bg-slate-800/40 transition-colors border-l-4 border-transparent";
                                    $statusBadge = '<span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-emerald-100 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800/60 shadow-2xs uppercase">Optimal</span>';
                                }
                            @endphp
                            <tr class="{{ $rowClass }} group">
                                <!-- Index -->
                                <td class="py-5 px-4 sm:px-6 text-center text-xs font-mono font-bold text-slate-400 group-hover:text-slate-600 dark:group-hover:text-slate-300 transition-colors">
                                    {{ $loop->iteration + ($medicines->currentPage() - 1) * $medicines->perPage() }}
                                </td>

                                <!-- Brand & Generic Name -->
                                <td class="py-5 px-5 sm:px-6">
                                    <div class="flex flex-col items-start gap-1.5">
                                        <div class="flex items-center gap-2 flex-wrap">
                                            <span class="font-extrabold text-slate-900 dark:text-white text-sm sm:text-base tracking-tight group-hover:text-emerald-600 dark:group-hover:text-emerald-400 transition-colors">
                                                {{ $medicine->name }}
                                            </span>
                                            {!! $statusBadge !!}
                                        </div>
                                        <p class="text-xs text-slate-500 dark:text-slate-400 font-medium">
                                            <span class="text-slate-400 dark:text-slate-500 font-normal">Generic:</span> 
                                            <span class="font-semibold text-slate-700 dark:text-slate-300">{{ $medicine->generic_name ?? '—' }}</span>
                                        </p>
                                    </div>
                                </td>

                                <!-- Form & Category -->
                                <td class="py-5 px-5 sm:px-6">
                                    <div class="flex flex-col gap-1 items-start">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-lg text-xs font-bold bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 border border-slate-200/80 dark:border-slate-700/80">
                                            {{ $medicine->form ?? 'N/A' }}
                                        </span>
                                        <span class="text-[11px] text-slate-400 dark:text-slate-500 font-medium">
                                            {{ $medicine->category ?? 'General' }}
                                        </span>
                                    </div>
                                </td>

                                <!-- Earliest Expiry (Crucial Clinical Information) -->
                                <td class="py-5 px-5 sm:px-6">
                                    @if($medicine->earliest_expiry)
                                        @php
                                            $expDate = \Carbon\Carbon::parse($medicine->earliest_expiry);
                                            $isPast = $expDate->isPast();
                                            $isNear = !$isPast && $expDate->lte(now()->addDays(30));
                                        @endphp
                                        @if($isPast)
                                            <div class="flex flex-col">
                                                <span class="inline-flex items-center gap-1 text-xs font-bold text-rose-600 dark:text-rose-400">
                                                    <svg class="w-3.5 h-3.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                    <span>{{ $expDate->format('M d, Y') }}</span>
                                                </span>
                                                <span class="text-[10px] text-rose-500 font-semibold mt-0.5">Expired lot</span>
                                            </div>
                                        @elseif($isNear)
                                            <div class="flex flex-col">
                                                <span class="inline-flex items-center gap-1 text-xs font-bold text-amber-600 dark:text-amber-400">
                                                    <svg class="w-3.5 h-3.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                    <span>{{ $expDate->format('M d, Y') }}</span>
                                                </span>
                                                <span class="text-[10px] text-amber-600 dark:text-amber-400 font-semibold mt-0.5">{{ $expDate->diffForHumans() }}</span>
                                            </div>
                                        @else
                                            <div class="flex flex-col">
                                                <span class="text-xs font-semibold text-slate-700 dark:text-slate-300">
                                                    {{ $expDate->format('M d, Y') }}
                                                </span>
                                                <span class="text-[10px] text-slate-400 dark:text-slate-500 font-medium mt-0.5">
                                                    {{ $expDate->diffForHumans() }}
                                                </span>
                                            </div>
                                        @endif
                                    @else
                                        <span class="text-xs text-slate-400 dark:text-slate-500 italic font-medium">No active lots</span>
                                    @endif
                                </td>

                                <!-- Total Stock -->
                                <td class="py-5 px-5 sm:px-6 text-center">
                                    <div class="flex flex-col items-center justify-center">
                                        <span class="text-lg sm:text-xl font-black {{ $totalStock == 0 ? 'text-slate-400 dark:text-slate-500' : ($totalStock < 20 ? 'text-blue-600 dark:text-blue-400' : 'text-slate-900 dark:text-white') }}">
                                            {{ number_format($totalStock) }}
                                        </span>
                                        <span class="text-[10px] text-slate-400 dark:text-slate-500 uppercase tracking-wider font-extrabold mt-0.5">
                                            {{ $medicine->unit ? Str::plural($medicine->unit, $totalStock) : 'Units' }}
                                        </span>
                                    </div>
                                </td>

                                <!-- Actions & Lots -->
                                <td class="py-5 px-5 sm:px-6 text-right print:hidden whitespace-nowrap">
                                    <div class="inline-flex items-center justify-end gap-3 flex-nowrap">
                                        <!-- Batches Vault Button -->
                                        <button @click="$dispatch('open-view-batches-{{ $medicine->id }}')" 
                                            class="h-9 px-3.5 rounded-xl inline-flex items-center gap-2 text-xs font-bold text-amber-900 dark:text-amber-200 bg-amber-50 hover:bg-amber-100 dark:bg-amber-950/50 dark:hover:bg-amber-900/60 border border-amber-200/90 dark:border-amber-800/60 shadow-2xs transition-all cursor-pointer active:scale-95 shrink-0" 
                                            title="View & Manage Batches">
                                            <svg class="w-4 h-4 text-amber-600 dark:text-amber-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                            </svg>
                                            <span>Batches</span>
                                            <span class="px-2 py-0.5 rounded-full text-[10px] bg-amber-200/80 dark:bg-amber-800/80 text-amber-950 dark:text-amber-100 font-extrabold">{{ $medicine->batches->count() }}</span>
                                        </button>
                                        
                                        <!-- Add Stock Button (Only if active) -->
                                        @if($medicine->is_active)
                                        <button @click="$dispatch('open-add-stock-{{ $medicine->id }}')" 
                                            class="h-9 px-3.5 rounded-xl inline-flex items-center gap-1.5 text-xs font-bold text-emerald-900 dark:text-emerald-200 bg-emerald-50 hover:bg-emerald-100 dark:bg-emerald-950/50 dark:hover:bg-emerald-900/60 border border-emerald-200/90 dark:border-emerald-800/60 shadow-2xs transition-all cursor-pointer active:scale-95 shrink-0" 
                                            title="Receive / Add Stock Lot">
                                            <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path>
                                            </svg>
                                            <span>Add Stock</span>
                                        </button>
                                        @endif

                                        <!-- Subtle Vertical Divider -->
                                        <span class="h-5 w-px bg-slate-200 dark:bg-slate-700 mx-1 shrink-0" aria-hidden="true"></span>

                                        <!-- Edit Button -->
                                        <button @click="$dispatch('open-edit-medicine-{{ $medicine->id }}')" 
                                            class="h-9 w-9 rounded-xl inline-flex items-center justify-center text-slate-500 hover:text-emerald-600 dark:text-slate-400 dark:hover:text-emerald-400 bg-slate-100/90 hover:bg-emerald-50 dark:bg-slate-800/90 dark:hover:bg-emerald-950/50 border border-slate-200/90 dark:border-slate-700/80 hover:border-emerald-300 dark:hover:border-emerald-700 shadow-2xs transition-all cursor-pointer active:scale-95 shrink-0" 
                                            title="Edit Medicine Information">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                                            </svg>
                                        </button>

                                        <!-- Archive / Activate Toggle Button -->
                                        <button @click="$dispatch('confirm-toggle-medicine', { id: {{ $medicine->id }}, name: '{{ addslashes($medicine->name) }}', is_active: {{ $medicine->is_active ? 'true' : 'false' }} })"
                                            class="h-9 w-9 rounded-xl inline-flex items-center justify-center {{ $medicine->is_active ? 'text-slate-500 hover:text-amber-600 dark:text-slate-400 dark:hover:text-amber-400 bg-slate-100/90 hover:bg-amber-50 dark:bg-slate-800/90 dark:hover:bg-amber-950/40 border border-slate-200/90 dark:border-slate-700/80 hover:border-amber-300 dark:hover:border-amber-700' : 'text-emerald-700 dark:text-emerald-300 bg-emerald-100/90 dark:bg-emerald-950/60 hover:bg-emerald-200 dark:hover:bg-emerald-900 border border-emerald-300 dark:border-emerald-700' }} shadow-2xs transition-all cursor-pointer active:scale-95 shrink-0" 
                                            title="{{ $medicine->is_active ? 'Archive / Deactivate Item' : 'Activate Item' }}">
                                            @if($medicine->is_active)
                                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/>
                                                </svg>
                                            @else
                                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                </svg>
                                            @endif
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="p-16 text-center text-slate-500 dark:text-slate-400">
                                    <div class="w-16 h-16 rounded-3xl bg-slate-100 dark:bg-slate-800/80 text-slate-400 dark:text-slate-500 mx-auto flex items-center justify-center mb-4 border border-slate-200/80 dark:border-slate-700 shadow-xs">
                                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                                    </div>
                                    <h3 class="text-base font-extrabold text-slate-900 dark:text-white mb-1">No medicines found</h3>
                                    <p class="text-xs text-slate-500 dark:text-slate-400 max-w-sm mx-auto leading-relaxed">
                                        There are no formulary records matching your search query or selected status filter.
                                    </p>
                                    <div class="mt-5 flex items-center justify-center gap-3">
                                        <a href="{{ route('pharmacy.medicines') }}" class="px-4 py-2 rounded-xl text-xs font-bold text-slate-700 dark:text-slate-300 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 border border-slate-300/80 dark:border-slate-700 transition">
                                            Clear All Filters
                                        </a>
                                        <button @click="$dispatch('open-add-medicine')" class="px-4 py-2 rounded-xl text-xs font-bold text-white bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 transition shadow-sm">
                                            + Add New Medicine
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination Controls with Styled Options -->
            @if($medicines->hasPages() || $medicines->total() > 10)
                <div class="px-6 sm:px-7 py-4.5 sm:py-5 border-t border-slate-100 dark:border-slate-800/80 bg-slate-50/70 dark:bg-slate-900/60 flex flex-col sm:flex-row items-center justify-between gap-4 print:hidden">
                    <div class="flex items-center gap-3">
                        <label class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Items per page</label>
                        <x-select 
                            :options="[
                                '10' => '10 per page',
                                '20' => '20 per page',
                                '30' => '30 per page',
                                '50' => '50 per page'
                            ]" 
                            :value="request('per_page', 10)"
                            size="sm"
                            containerClass="w-36"
                            :dropUp="true"
                            @change="document.getElementById('medicines_per_page').value = $event.detail; document.getElementById('filterForm').submit()"
                        />
                    </div>
                    
                    <div class="w-full sm:w-auto">
                        @if($medicines->hasPages())
                            {{ $medicines->appends(request()->query())->links('vendor.pagination.shadcn') }}
                        @endif
                    </div>
                </div>
            @endif
        </div>

    </div>
    </x-print-layout>
</div>

{{-- Modals for Existing Medicines --}}
@foreach($medicines as $medicine)
<!-- Edit Medicine Modal -->
<div x-data="{ open: false }" 
     @open-edit-medicine-{{ $medicine->id }}.window="open = true" 
     x-show="open" 
     style="display: none;" 
     class="fixed inset-0 z-[999] overflow-y-auto" 
     aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
        <div x-show="open" 
             x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" 
             x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" 
             class="fixed inset-0 bg-slate-950/70 backdrop-blur-sm transition-opacity" 
             @click="open = false" aria-hidden="true"></div>

        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div x-show="open" 
             x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" 
             x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
             @click.stop
             class="relative z-50 inline-block align-bottom bg-white dark:bg-slate-900 rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full border border-slate-200/90 dark:border-slate-800">
            <div class="px-6 py-5 border-b border-slate-100 dark:border-slate-800 flex justify-between items-center bg-slate-50/70 dark:bg-slate-900/60">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-emerald-100/80 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center border border-emerald-500/20 shadow-2xs">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-slate-900 dark:text-white" id="modal-title">Edit Medicine Information</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400">Update formulary specifications for {{ $medicine->name }}</p>
                    </div>
                </div>
                <button type="button" @click="open = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 p-1.5 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors cursor-pointer">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            <div class="p-6 sm:p-7">
                <form action="{{ route('pharmacy.medicines.update', $medicine->id) }}" method="POST" class="space-y-4">
                    @csrf
                    @method('PUT')
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">Brand Name <span class="text-rose-500">*</span></label>
                            <input type="text" name="name" value="{{ $medicine->name }}" required 
                                class="w-full h-11 px-4 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50/70 dark:bg-slate-800/60 text-slate-900 dark:text-white text-sm font-semibold focus:border-emerald-500 shadow-2xs transition-all">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">Generic Name</label>
                            <input type="text" name="generic_name" value="{{ $medicine->generic_name }}" 
                                class="w-full h-11 px-4 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50/70 dark:bg-slate-800/60 text-slate-900 dark:text-white text-sm font-semibold focus:border-emerald-500 shadow-2xs transition-all">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">Category</label>
                            <x-select 
                                name="category" 
                                placeholder="Select Category..."
                                :options="[
                                    'Analgesic' => 'Analgesic',
                                    'Antibiotic' => 'Antibiotic',
                                    'Antihistamine' => 'Antihistamine',
                                    'Antipyretic' => 'Antipyretic',
                                    'Vitamins' => 'Vitamins',
                                    'Supplement' => 'Supplement',
                                    'Other' => 'Other'
                                ]" 
                                :value="$medicine->category"
                                class="!h-11 !py-0 flex items-center font-semibold text-xs sm:text-sm bg-slate-50/70 dark:bg-slate-800/60"
                            />
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">Form</label>
                            <x-select 
                                name="form" 
                                placeholder="Select Form..."
                                :options="[
                                    'Tablet' => 'Tablet',
                                    'Capsule' => 'Capsule',
                                    'Syrup' => 'Syrup',
                                    'Suspension' => 'Suspension',
                                    'Drops' => 'Drops',
                                    'Ointment' => 'Ointment',
                                    'Cream' => 'Cream',
                                    'Injection' => 'Injection',
                                    'Other' => 'Other'
                                ]" 
                                :value="$medicine->form"
                                class="!h-11 !py-0 flex items-center font-semibold text-xs sm:text-sm bg-slate-50/70 dark:bg-slate-800/60"
                            />
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">Unit</label>
                            <x-select 
                                name="unit" 
                                placeholder="Select Unit..."
                                :options="[
                                    'Piece' => 'Piece',
                                    'Box' => 'Box',
                                    'Bottle' => 'Bottle',
                                    'Tube' => 'Tube',
                                    'Vial' => 'Vial',
                                    'Ampoule' => 'Ampoule',
                                    'Other' => 'Other'
                                ]" 
                                :value="$medicine->unit"
                                class="!h-11 !py-0 flex items-center font-semibold text-xs sm:text-sm bg-slate-50/70 dark:bg-slate-800/60"
                            />
                        </div>
                    </div>

                    <div class="flex justify-end gap-2.5 pt-4 border-t border-slate-100 dark:border-slate-800">
                        <button type="button" @click="open = false" class="px-4 py-2.5 rounded-xl text-xs font-semibold text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition border border-slate-300 dark:border-slate-700 cursor-pointer">Cancel</button>
                        <button type="submit" class="px-5 py-2.5 rounded-xl text-xs font-bold bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white transition shadow-sm cursor-pointer active:scale-95">Update Medicine</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Add Stock Modal -->
<div x-data="{ 
         open: false,
         batchVal: '',
         generateBatch() {
             const now = new Date();
             const yr = now.getFullYear();
             const mo = String(now.getMonth() + 1).padStart(2, '0');
             const rand = Math.random().toString(36).substring(2, 6).toUpperCase();
             this.batchVal = `RHU-${yr}${mo}-${rand}`;
         }
     }" 
     @open-add-stock-{{ $medicine->id }}.window="open = true; batchVal = '';" 
     x-show="open" 
     style="display: none;" 
     class="fixed inset-0 z-[999] overflow-y-auto" 
     aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
        <div x-show="open" 
             x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" 
             x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" 
             class="fixed inset-0 bg-slate-950/70 backdrop-blur-sm transition-opacity" 
             @click="open = false" aria-hidden="true"></div>

        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div x-show="open" 
             x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" 
             x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
             @click.stop
             class="relative z-50 inline-block align-bottom bg-white dark:bg-slate-900 rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md w-full border border-slate-200/90 dark:border-slate-800">
            <div class="px-6 py-5 border-b border-slate-100 dark:border-slate-800 flex justify-between items-center bg-slate-50/70 dark:bg-slate-900/60">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-emerald-100/80 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center border border-emerald-500/20 shadow-2xs">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-slate-900 dark:text-white" id="modal-title">Receive Stock Batch</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400">Add inventory lot for {{ $medicine->name }}</p>
                    </div>
                </div>
                <button type="button" @click="open = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 p-1.5 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors cursor-pointer">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            <div class="p-6 sm:p-7">
                <form action="{{ route('pharmacy.medicines.add-stock', $medicine->id) }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider"
                                title="Printed on the medicine box by the manufacturer. Required for recall traceability.">
                                Batch / Lot Number <span class="text-rose-500">*</span>
                            </label>
                            <button type="button" @click="generateBatch()" class="inline-flex items-center gap-1 text-[11px] font-bold text-emerald-600 hover:text-emerald-700 dark:text-emerald-400 hover:underline cursor-pointer">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                <span>⚡ Generate Lot #</span>
                            </button>
                        </div>
                        <input type="text" name="batch_number" x-model="batchVal" required maxlength="40" placeholder="e.g. 4AB123X or RHU-202610-A89B"
                            @input="batchVal = $el.value.toUpperCase()"
                            @blur="batchVal = batchVal.replace(/\s+/g, ' ').trim().toUpperCase().replace(/[\s.,;:\/-]+$/, '')"
                            class="w-full h-11 px-4 rounded-xl font-mono uppercase border border-slate-300 dark:border-slate-700 bg-slate-50/70 dark:bg-slate-800/60 text-slate-900 dark:text-white text-sm font-semibold focus:border-emerald-500 shadow-2xs transition-all placeholder:normal-case placeholder:font-sans">
                        <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1.5 leading-relaxed">
                            Type manufacturer code, or click <strong>Generate Lot #</strong> for clinic stock.
                        </p>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">Expiration Date <span class="text-rose-500">*</span></label>
                        <input type="date" name="expiration_date" required min="{{ date('Y-m-d') }}" 
                            class="w-full h-11 px-4 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50/70 dark:bg-slate-800/60 text-slate-900 dark:text-white text-sm font-semibold focus:border-emerald-500 shadow-2xs transition-all">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">Received Quantity <span class="text-rose-500">*</span></label>
                        <input type="number" name="quantity" required min="1" placeholder="e.g. 100"
                            class="w-full h-11 px-4 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50/70 dark:bg-slate-800/60 text-slate-900 dark:text-white text-sm font-semibold focus:border-emerald-500 shadow-2xs transition-all">
                    </div>
                    <div class="flex justify-end gap-2.5 pt-4 border-t border-slate-100 dark:border-slate-800">
                        <button type="button" @click="open = false" class="px-4 py-2.5 rounded-xl text-xs font-semibold text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition border border-slate-300 dark:border-slate-700 cursor-pointer">Cancel</button>
                        <button type="submit" class="px-5 py-2.5 rounded-xl text-xs font-bold bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white transition shadow-sm cursor-pointer active:scale-95">Save Batch</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- View Batches Modal (Adjustment & Disposal Subviews) -->
<div x-data="{ 
         open: false,
         subView: 'list',
         activeBatch: null,
         newQuantity: 0,
         adjustReason: 'Physical Recount',
         adjustNotes: '',
         disposeReason: 'Expired',
         disposeNotes: '',
         startAdjust(batch) {
             this.activeBatch = batch;
             this.newQuantity = batch.quantity;
             this.adjustReason = 'Physical Recount';
             this.adjustNotes = '';
             this.subView = 'adjust';
         },
         startDispose(batch) {
             this.activeBatch = batch;
             this.disposeReason = batch.is_expired ? 'Expired' : 'Damaged / Broken';
             this.disposeNotes = '';
             this.subView = 'dispose';
         }
     }" 
     @open-view-batches-{{ $medicine->id }}.window="open = true; subView = 'list'; activeBatch = null;" 
     x-show="open" 
     style="display: none;" 
     class="fixed inset-0 z-[999] overflow-y-auto" 
     aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
        <div x-show="open" 
             x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" 
             x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" 
             class="fixed inset-0 bg-slate-950/70 backdrop-blur-sm transition-opacity" 
             @click="open = false" aria-hidden="true"></div>

        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div x-show="open" 
             x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" 
             x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
             @click.stop
             class="relative z-50 inline-block align-bottom bg-white dark:bg-slate-900 rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl w-full border border-slate-200/90 dark:border-slate-800">
            
            <!-- Modal Header -->
            <div class="px-6 py-5 border-b border-slate-100 dark:border-slate-800 flex justify-between items-center bg-slate-50/70 dark:bg-slate-900/60">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-amber-100/80 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400 flex items-center justify-center border border-amber-500/20 shadow-2xs">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-slate-900 dark:text-white" id="modal-title">Active Batches Vault</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400">Inventory breakdown & lifecycle for <span class="font-bold text-slate-700 dark:text-slate-200">{{ $medicine->name }}</span></p>
                    </div>
                </div>
                <button type="button" @click="open = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 p-1.5 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors cursor-pointer">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <!-- Main Content: List View -->
            <div x-show="subView === 'list'" class="p-6 sm:p-7">
                @if($medicine->batches->count() > 0)
                    <div class="overflow-x-auto rounded-2xl border border-slate-200/90 dark:border-slate-800 shadow-2xs custom-scrollbar">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-slate-50/90 dark:bg-slate-950/80 border-b border-slate-200/80 dark:border-slate-800 text-[11px] uppercase tracking-wider text-slate-500 dark:text-slate-400 font-extrabold">
                                    <th class="p-3.5">Batch No.</th>
                                    <th class="p-3.5 text-center">Remaining / Orig.</th>
                                    <th class="p-3.5 text-right">Expiration</th>
                                    <th class="p-3.5 text-center">Status</th>
                                    <th class="p-3.5 text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-slate-800 text-sm">
                                @foreach($medicine->batches as $batch)
                                    @php
                                        $isDisposed = $batch->status === 'disposed';
                                        $isExpired = $batch->expiration_date < now();
                                        $isExpiringSoon = !$isExpired && $batch->expiration_date <= now()->addDays(30);
                                    @endphp
                                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-850 transition-colors {{ $isDisposed ? 'opacity-60 bg-rose-50/20' : '' }}">
                                        <td class="p-3.5 font-mono text-xs font-bold text-slate-800 dark:text-slate-200">
                                            {{ $batch->batch_number ?? 'N/A' }}
                                        </td>
                                        <td class="p-3.5 text-center">
                                            <span class="font-black text-slate-900 dark:text-white">{{ $batch->quantity }}</span>
                                            @if($batch->original_quantity)
                                                <span class="text-[11px] text-slate-400">/ {{ $batch->original_quantity }}</span>
                                            @endif
                                        </td>
                                        <td class="p-3.5 text-right text-xs font-semibold text-slate-600 dark:text-slate-400">
                                            {{ $batch->expiration_date->format('M d, Y') }}
                                        </td>
                                        <td class="p-3.5 text-center">
                                            @if($isDisposed)
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-200 text-slate-700 dark:bg-slate-800 dark:text-slate-300 border border-slate-300 dark:border-slate-700 uppercase" title="Disposed: {{ $batch->disposal_reason }}">Disposed</span>
                                            @elseif($batch->quantity == 0)
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-400 border border-slate-200 dark:border-slate-700 uppercase">Depleted</span>
                                            @elseif($isExpired)
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-100 text-rose-800 dark:bg-rose-950/60 dark:text-rose-300 border border-rose-200 dark:border-rose-800/60 uppercase">Expired</span>
                                            @elseif($isExpiringSoon)
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800 dark:bg-amber-950/60 dark:text-amber-300 border border-amber-200 dark:border-amber-800/60 uppercase animate-pulse">Near Expiry</span>
                                            @else
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800/60 uppercase">Good</span>
                                            @endif
                                        </td>
                                        <td class="p-3.5 text-right">
                                            @if(! $isDisposed)
                                                <div class="flex items-center justify-end gap-1.5">
                                                    <button type="button" @click="startAdjust({ id: {{ $batch->id }}, number: '{{ addslashes($batch->batch_number) }}', quantity: {{ $batch->quantity }} })"
                                                        class="h-7 px-2.5 rounded-lg text-[11px] font-bold text-slate-700 dark:text-slate-300 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 border border-slate-200 dark:border-slate-700 transition cursor-pointer active:scale-95"
                                                        title="Adjust Stock Count">
                                                        Adjust
                                                    </button>
                                                    <button type="button" @click="startDispose({ id: {{ $batch->id }}, number: '{{ addslashes($batch->batch_number) }}', quantity: {{ $batch->quantity }}, is_expired: {{ $isExpired ? 'true' : 'false' }} })"
                                                        class="h-7 px-2.5 rounded-lg text-[11px] font-bold text-rose-700 dark:text-rose-300 bg-rose-50 dark:bg-rose-950/40 hover:bg-rose-100 dark:hover:bg-rose-900/60 border border-rose-200 dark:border-rose-800/60 transition cursor-pointer active:scale-95"
                                                        title="Dispose / Write Off Batch">
                                                        Dispose
                                                    </button>
                                                </div>
                                            @else
                                                <span class="text-[11px] text-slate-400 dark:text-slate-500 font-medium" title="{{ $batch->disposal_notes ?? 'No notes recorded' }}">
                                                    {{ $batch->disposal_reason ?? 'Disposed' }}
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-10">
                        <p class="text-xs text-slate-500 dark:text-slate-400 font-medium">No recorded batches found for this item.</p>
                    </div>
                @endif
                <div class="flex justify-end mt-6">
                    <button type="button" @click="open = false" class="px-5 py-2.5 text-xs font-bold text-slate-700 dark:text-slate-300 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 rounded-xl transition-colors cursor-pointer">Close</button>
                </div>
            </div>

            <!-- SubView: Adjust Stock Count -->
            <div x-show="subView === 'adjust'" class="p-6 sm:p-7">
                <div class="mb-5 pb-4 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
                    <div>
                        <h4 class="text-sm font-bold text-slate-900 dark:text-white">Adjust Stock Count</h4>
                        <p class="text-xs text-slate-500 dark:text-slate-400">Batch <span class="font-mono font-bold text-slate-800 dark:text-slate-200" x-text="activeBatch?.number"></span> (Current: <span class="font-bold text-emerald-600 dark:text-emerald-400" x-text="activeBatch?.quantity + ' units'"></span>)</p>
                    </div>
                    <button type="button" @click="subView = 'list'" class="text-xs font-bold text-slate-500 hover:text-slate-700 dark:hover:text-slate-300 flex items-center gap-1 cursor-pointer">
                        &larr; Back to Batches
                    </button>
                </div>

                <form :action="'{{ url('pharmacy/batches') }}/' + activeBatch?.id + '/adjust'" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">New Physical Count <span class="text-rose-500">*</span></label>
                        <input type="number" name="new_quantity" x-model.number="newQuantity" required min="0" 
                            class="w-full h-11 px-4 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50/70 dark:bg-slate-800/60 text-slate-900 dark:text-white text-sm font-bold focus:border-emerald-500 shadow-2xs transition-all">
                        <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1">
                            Difference: <span class="font-bold" :class="(newQuantity - (activeBatch?.quantity || 0)) >= 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400'" x-text="((newQuantity - (activeBatch?.quantity || 0)) > 0 ? '+' : '') + (newQuantity - (activeBatch?.quantity || 0)) + ' units'"></span>
                        </p>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">Adjustment Reason <span class="text-rose-500">*</span></label>
                        <select name="adjustment_reason" x-model="adjustReason" required
                            class="w-full h-11 px-4 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50/70 dark:bg-slate-800/60 text-slate-900 dark:text-white text-sm font-semibold focus:border-emerald-500 shadow-2xs transition-all">
                            <option value="Physical Recount">Physical Recount</option>
                            <option value="Damage / Breakage">Damage / Breakage</option>
                            <option value="Audit Correction">Audit Correction</option>
                            <option value="Received Adjustment">Received Adjustment</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">Notes / Remarks</label>
                        <textarea name="adjustment_notes" x-model="adjustNotes" rows="2" maxlength="500" placeholder="Optional context for inventory audit..."
                            class="w-full px-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50/70 dark:bg-slate-800/60 text-slate-900 dark:text-white text-xs font-medium focus:border-emerald-500 shadow-2xs transition-all"></textarea>
                    </div>

                    <div class="flex justify-end gap-2.5 pt-4 border-t border-slate-100 dark:border-slate-800">
                        <button type="button" @click="subView = 'list'" class="px-4 py-2.5 rounded-xl text-xs font-semibold text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition border border-slate-300 dark:border-slate-700 cursor-pointer">Cancel</button>
                        <button type="submit" class="px-5 py-2.5 rounded-xl text-xs font-bold bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white transition shadow-sm cursor-pointer active:scale-95">Save Adjustment</button>
                    </div>
                </form>
            </div>

            <!-- SubView: Dispose / Write Off Batch -->
            <div x-show="subView === 'dispose'" class="p-6 sm:p-7">
                <div class="mb-5 pb-4 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
                    <div>
                        <h4 class="text-sm font-bold text-rose-600 dark:text-rose-400">Dispose & Write Off Batch</h4>
                        <p class="text-xs text-slate-500 dark:text-slate-400">Batch <span class="font-mono font-bold text-slate-800 dark:text-slate-200" x-text="activeBatch?.number"></span></p>
                    </div>
                    <button type="button" @click="subView = 'list'" class="text-xs font-bold text-slate-500 hover:text-slate-700 dark:hover:text-slate-300 flex items-center gap-1 cursor-pointer">
                        &larr; Back to Batches
                    </button>
                </div>

                <div class="p-4 mb-4 rounded-2xl bg-rose-50/80 dark:bg-rose-950/40 border border-rose-200/80 dark:border-rose-800/60 text-xs text-rose-800 dark:text-rose-300 flex items-start gap-3">
                    <svg class="w-5 h-5 shrink-0 mt-0.5 text-rose-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    <div>
                        <p class="font-bold">Permanent Stock Write-Off</p>
                        <p class="mt-0.5 text-[11px] leading-relaxed">
                            Writing off this batch will zero out the remaining <strong x-text="activeBatch?.quantity + ' unit(s)'"></strong> and record an official disposal audit log. This action cannot be reversed.
                        </p>
                    </div>
                </div>

                <form :action="'{{ url('pharmacy/batches') }}/' + activeBatch?.id + '/dispose'" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">Disposal Reason <span class="text-rose-500">*</span></label>
                        <select name="disposal_reason" x-model="disposeReason" required
                            class="w-full h-11 px-4 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50/70 dark:bg-slate-800/60 text-slate-900 dark:text-white text-sm font-semibold focus:border-rose-500 shadow-2xs transition-all">
                            <option value="Expired">Expired</option>
                            <option value="Damaged / Broken">Damaged / Broken</option>
                            <option value="Contaminated">Contaminated</option>
                            <option value="Supplier Recall">Supplier Recall</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">Disposal Notes / Method</label>
                        <textarea name="disposal_notes" x-model="disposeNotes" rows="2" maxlength="500" placeholder="e.g. Incinerated per biohazard protocol, quarantined for return to DOH..."
                            class="w-full px-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50/70 dark:bg-slate-800/60 text-slate-900 dark:text-white text-xs font-medium focus:border-rose-500 shadow-2xs transition-all"></textarea>
                    </div>

                    <div class="flex justify-end gap-2.5 pt-4 border-t border-slate-100 dark:border-slate-800">
                        <button type="button" @click="subView = 'list'" class="px-4 py-2.5 rounded-xl text-xs font-semibold text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition border border-slate-300 dark:border-slate-700 cursor-pointer">Cancel</button>
                        <button type="submit" class="px-5 py-2.5 rounded-xl text-xs font-bold bg-gradient-to-r from-rose-600 to-red-600 hover:from-rose-700 hover:to-red-700 text-white transition shadow-sm cursor-pointer active:scale-95">Confirm Write-Off & Dispose</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endforeach

<!-- Add New Medicine Modal -->
<div x-data="{ 
         open: false,
         batchVal: '',
         generateBatch() {
             const now = new Date();
             const yr = now.getFullYear();
             const mo = String(now.getMonth() + 1).padStart(2, '0');
             const rand = Math.random().toString(36).substring(2, 6).toUpperCase();
             this.batchVal = `RHU-${yr}${mo}-${rand}`;
         }
     }" 
     @open-add-medicine.window="open = true; batchVal = '';" 
     x-show="open" 
     style="display: none;" 
     class="fixed inset-0 z-[999] overflow-y-auto" 
     aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
        <div x-show="open" 
             x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" 
             x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" 
             class="fixed inset-0 bg-slate-950/70 backdrop-blur-sm transition-opacity" 
             @click="open = false" aria-hidden="true"></div>

        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div x-show="open" 
             x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" 
             x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
             @click.stop
             class="relative z-50 inline-block align-bottom bg-white dark:bg-slate-900 rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full border border-slate-200/90 dark:border-slate-800">
            <div class="px-6 py-5 border-b border-slate-100 dark:border-slate-800 flex justify-between items-center bg-slate-50/70 dark:bg-slate-900/60">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-emerald-100/80 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center border border-emerald-500/20 shadow-2xs">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-slate-900 dark:text-white" id="modal-title">Register Formulary Item</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400">Add medicine and initial batch stock</p>
                    </div>
                </div>
                <button type="button" @click="open = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 p-1.5 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors cursor-pointer">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            <div class="p-6 sm:p-7">
                <form action="{{ route('pharmacy.medicines.store') }}" method="POST" class="space-y-5">
                    @csrf
                    <!-- Medicine Information -->
                    <div class="p-4 bg-slate-50/80 dark:bg-slate-800/40 rounded-2xl border border-slate-200/80 dark:border-slate-800">
                        <h4 class="text-xs font-black uppercase text-slate-500 dark:text-slate-400 mb-3 tracking-wider">Medicine Information</h4>
                        <div class="space-y-4">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">Brand Name <span class="text-rose-500">*</span></label>
                                    <input type="text" name="name" required placeholder="e.g. Biogesic" 
                                        class="w-full h-11 px-4 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800/80 text-slate-900 dark:text-white text-sm font-semibold focus:border-emerald-500 shadow-2xs transition-all placeholder:text-slate-400">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">Generic Name</label>
                                    <input type="text" name="generic_name" placeholder="e.g. Paracetamol" 
                                        class="w-full h-11 px-4 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800/80 text-slate-900 dark:text-white text-sm font-semibold focus:border-emerald-500 shadow-2xs transition-all placeholder:text-slate-400">
                                </div>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">Category</label>
                                    <x-select 
                                        name="category" 
                                        placeholder="Select Category..."
                                        :options="[
                                            'Analgesic' => 'Analgesic',
                                            'Antibiotic' => 'Antibiotic',
                                            'Antihistamine' => 'Antihistamine',
                                            'Antipyretic' => 'Antipyretic',
                                            'Vitamins' => 'Vitamins',
                                            'Supplement' => 'Supplement',
                                            'Other' => 'Other'
                                        ]" 
                                        value=""
                                        class="!h-11 !py-0 flex items-center font-semibold text-xs sm:text-sm bg-white dark:bg-slate-800/80"
                                    />
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">Form</label>
                                    <x-select 
                                        name="form" 
                                        placeholder="Select Form..."
                                        :options="[
                                            'Tablet' => 'Tablet',
                                            'Capsule' => 'Capsule',
                                            'Syrup' => 'Syrup',
                                            'Suspension' => 'Suspension',
                                            'Drops' => 'Drops',
                                            'Ointment' => 'Ointment',
                                            'Cream' => 'Cream',
                                            'Injection' => 'Injection',
                                            'Other' => 'Other'
                                        ]" 
                                        value=""
                                        class="!h-11 !py-0 flex items-center font-semibold text-xs sm:text-sm bg-white dark:bg-slate-800/80"
                                    />
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">Unit</label>
                                    <x-select 
                                        name="unit" 
                                        placeholder="Select Unit..."
                                        :options="[
                                            'Piece' => 'Piece',
                                            'Box' => 'Box',
                                            'Bottle' => 'Bottle',
                                            'Tube' => 'Tube',
                                            'Vial' => 'Vial',
                                            'Ampoule' => 'Ampoule',
                                            'Other' => 'Other'
                                        ]" 
                                        value=""
                                        class="!h-11 !py-0 flex items-center font-semibold text-xs sm:text-sm bg-white dark:bg-slate-800/80"
                                    />
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Initial Batch Information -->
                    <div class="p-4 bg-emerald-50/60 dark:bg-emerald-950/20 rounded-2xl border border-emerald-200/60 dark:border-emerald-800/40">
                        <h4 class="text-xs font-black uppercase text-emerald-700 dark:text-emerald-400 mb-3 tracking-wider">Initial Batch Stock (Required)</h4>
                        <div class="space-y-4">
                            <div>
                                <div class="flex items-center justify-between mb-1.5">
                                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider"
                                        title="Printed on the medicine box by the manufacturer. Required for recall traceability.">
                                        Batch / Lot Number <span class="text-rose-500">*</span>
                                    </label>
                                    <button type="button" @click="generateBatch()" class="inline-flex items-center gap-1 text-[11px] font-bold text-emerald-600 hover:text-emerald-700 dark:text-emerald-400 hover:underline cursor-pointer">
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                        <span>⚡ Generate Lot #</span>
                                    </button>
                                </div>
                                <input type="text" name="batch_number" x-model="batchVal" required maxlength="40" placeholder="e.g. 4AB123X or RHU-202610-A89B"
                                    @input="batchVal = $el.value.toUpperCase()"
                                    @blur="batchVal = batchVal.replace(/\s+/g, ' ').trim().toUpperCase().replace(/[\s.,;:\/-]+$/, '')"
                                    class="w-full h-11 px-4 rounded-xl font-mono uppercase border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800/80 text-slate-900 dark:text-white text-sm font-semibold focus:border-emerald-500 shadow-2xs transition-all placeholder:normal-case placeholder:font-sans">
                                <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1.5 leading-relaxed">
                                    Type manufacturer code, or click <strong>Generate Lot #</strong> for clinic stock.
                                </p>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">Expiration Date <span class="text-rose-500">*</span></label>
                                    <input type="date" name="expiration_date" required min="{{ date('Y-m-d') }}" 
                                        class="w-full h-11 px-4 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800/80 text-slate-900 dark:text-white text-sm font-semibold focus:border-emerald-500 shadow-2xs transition-all">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">Initial Quantity <span class="text-rose-500">*</span></label>
                                    <input type="number" name="quantity" required min="1" placeholder="e.g. 100" 
                                        class="w-full h-11 px-4 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800/80 text-slate-900 dark:text-white text-sm font-semibold focus:border-emerald-500 shadow-2xs transition-all placeholder:text-slate-400">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end gap-2.5 pt-4 border-t border-slate-100 dark:border-slate-800">
                        <button type="button" @click="open = false" class="px-4 py-2.5 rounded-xl text-xs font-semibold text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition border border-slate-300 dark:border-slate-700 cursor-pointer">Cancel</button>
                        <button type="submit" class="px-5 py-2.5 rounded-xl text-xs font-bold bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white transition shadow-sm cursor-pointer active:scale-95">Save Formulary Item</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Medicine Archive / Activate Confirmation Modal -->
<div x-data="{
        open: false,
        medicine: { id: null, name: '', is_active: true }
    }"
    @confirm-toggle-medicine.window="medicine = $event.detail; open = true;"
    x-show="open"
    style="display: none;"
    class="fixed inset-0 z-[1000] overflow-y-auto"
    aria-labelledby="archive-modal-title" role="dialog" aria-modal="true">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
        <div x-show="open" 
             x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" 
             x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" 
             class="fixed inset-0 bg-slate-950/70 backdrop-blur-sm transition-opacity" 
             @click="open = false" aria-hidden="true"></div>

        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div x-show="open" 
             x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" 
             x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
             @click.stop
             class="relative z-50 inline-block align-bottom bg-white dark:bg-slate-900 rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md w-full border border-slate-200/90 dark:border-slate-800">
            <div class="px-6 py-5 border-b border-slate-100 dark:border-slate-800 flex justify-between items-center bg-slate-50/70 dark:bg-slate-900/60">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center border shadow-2xs"
                        :class="medicine.is_active ? 'bg-amber-100/80 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400 border-amber-500/20' : 'bg-emerald-100/80 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 border-emerald-500/20'">
                        <template x-if="medicine.is_active">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>
                        </template>
                        <template x-if="!medicine.is_active">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </template>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-slate-900 dark:text-white" id="archive-modal-title" x-text="medicine.is_active ? 'Deactivate / Archive Medicine' : 'Activate Medicine'"></h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400">Formulary Item Status</p>
                    </div>
                </div>
                <button type="button" @click="open = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 p-1.5 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors cursor-pointer">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            
            <div class="p-6 sm:p-7">
                <template x-if="medicine.is_active">
                    <div class="p-4 rounded-2xl bg-amber-50/80 dark:bg-amber-950/40 border border-amber-200/80 dark:border-amber-800/60 text-xs text-amber-900 dark:text-amber-300 mb-5">
                        <p class="font-bold">Are you sure you want to deactivate <span class="font-black" x-text="medicine.name"></span>?</p>
                        <p class="mt-1 text-[11px] leading-relaxed text-amber-800 dark:text-amber-400">
                            Archived medicines cannot be prescribed by doctors or nurses during clinical consultations. Existing dispensing records and batches are preserved.
                        </p>
                    </div>
                </template>
                <template x-if="!medicine.is_active">
                    <div class="p-4 rounded-2xl bg-emerald-50/80 dark:bg-emerald-950/40 border border-emerald-200/80 dark:border-emerald-800/60 text-xs text-emerald-900 dark:text-emerald-300 mb-5">
                        <p class="font-bold">Activate <span class="font-black" x-text="medicine.name"></span>?</p>
                        <p class="mt-1 text-[11px] leading-relaxed text-emerald-800 dark:text-emerald-400">
                            This medicine will become immediately available in the clinical consultation prescribing formulary.
                        </p>
                    </div>
                </template>

                <form :action="'{{ url('pharmacy/medicines') }}/' + medicine.id + '/toggle-status'" method="POST" class="flex justify-end gap-2.5">
                    @csrf
                    <button type="button" @click="open = false" class="px-4 py-2.5 rounded-xl text-xs font-semibold text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition border border-slate-300 dark:border-slate-700 cursor-pointer">Cancel</button>
                    <button type="submit" 
                        class="px-5 py-2.5 rounded-xl text-xs font-bold text-white transition shadow-sm cursor-pointer active:scale-95"
                        :class="medicine.is_active ? 'bg-gradient-to-r from-amber-600 to-orange-600 hover:from-amber-700 hover:to-orange-700' : 'bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700'"
                        x-text="medicine.is_active ? 'Yes, Archive Item' : 'Yes, Activate Item'"></button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    async function exportStockCsv(btn) {
        if (!btn) btn = document.getElementById('exportStockCsvBtn');
        const icon = document.getElementById('exportStockIcon');
        const spinner = document.getElementById('exportStockSpinner');
        const text = document.getElementById('exportStockText');

        if (btn) btn.disabled = true;
        if (icon) icon.classList.add('hidden');
        if (spinner) spinner.classList.remove('hidden');
        if (text) text.textContent = 'Exporting...';

        try {
            const form = document.getElementById('filterForm');
            const params = form ? new URLSearchParams(new FormData(form)) : new URLSearchParams(window.location.search);
            const response = await fetch('{{ route('pharmacy.medicines.export-csv') }}?' + params.toString());

            if (response.status === 404) {
                const err = await response.json();
                window.dispatchEvent(new CustomEvent('add-toast', { detail: { message: err.message || 'No stock records found to export.', type: 'warning' } }));
                return;
            }

            if (!response.ok) {
                window.dispatchEvent(new CustomEvent('add-toast', { detail: { message: 'Export failed. Please try again.', type: 'error' } }));
                return;
            }

            const blob = await response.blob();
            const disposition = response.headers.get('Content-Disposition');
            let filename = 'pharmacy-stock-report.csv';
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

            window.dispatchEvent(new CustomEvent('add-toast', { detail: { message: 'Inventory stock CSV exported successfully!', type: 'success' } }));
        } catch (error) {
            console.error('Stock CSV Export Error:', error);
            window.dispatchEvent(new CustomEvent('add-toast', { detail: { message: 'Export failed. Please try again.', type: 'error' } }));
        } finally {
            if (btn) btn.disabled = false;
            if (icon) icon.classList.remove('hidden');
            if (spinner) spinner.classList.add('hidden');
            if (text) text.textContent = 'Export Stock (CSV)';
        }
    }
    window.exportStockCsv = exportStockCsv;
</script>
@endsection
