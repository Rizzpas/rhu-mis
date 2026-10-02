@extends('layouts.pharmacy')

@section('header', 'Pharmacy Dashboard')

@section('content')
<div class="max-w-7xl mx-auto space-y-6 pb-12" x-data="dispensingQueue()">
    
    <!-- Breadcrumb Navigation -->
    <nav class="flex items-center gap-2 text-xs font-medium text-slate-500 dark:text-slate-400 py-2 border-b border-slate-200/60 dark:border-slate-800" aria-label="Breadcrumb">
        <a href="{{ route('pharmacy.dashboard') }}" class="hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors flex items-center gap-1.5 shrink-0">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
            <span>Home</span>
        </a>
        <span class="text-slate-300 dark:text-slate-700">/</span>
        <span class="hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors">Pharmacy</span>
        <span class="text-slate-300 dark:text-slate-700">/</span>
        <span class="text-slate-700 dark:text-slate-300 font-semibold">Real-Time Dashboard</span>
    </nav>

    <!-- Top Header & Actions Section -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pb-2 border-b border-slate-200/80 dark:border-slate-800 print:hidden">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-emerald-100 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800/60">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                    Live Dispensing Station
                </span>
                <span class="text-xs text-slate-400 dark:text-slate-500 font-medium">•</span>
                <span class="text-xs text-slate-500 dark:text-slate-400 font-semibold">{{ now()->format('l, F d, Y') }}</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-black text-slate-900 dark:text-white tracking-tight">
                Pharmacy Dashboard
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-1 max-w-2xl">
                Review active clinician orders, dispense medication lots with safety validation, and monitor formulary inventory.
            </p>
        </div>

        <!-- Quick Actions Toolbar -->
        <div class="flex items-center gap-2.5 flex-wrap sm:flex-nowrap shrink-0">
            <button type="button" onclick="exportQueueCsv(this)" id="exportQueueBtn"
                class="h-10 px-4 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 hover:bg-slate-50 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-200 text-xs font-bold transition-all shadow-xs flex items-center gap-2 cursor-pointer active:scale-95">
                <svg id="exportQueueIcon" class="w-4 h-4 text-emerald-600 dark:text-emerald-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <svg id="exportQueueSpinner" class="hidden animate-spin h-4 w-4 text-emerald-600 dark:text-emerald-400 shrink-0" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span id="exportQueueText">Export Queue (CSV)</span>
            </button>

            <a href="{{ route('pharmacy.medicines') }}" 
                class="h-10 px-4 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white text-xs font-bold shadow-md shadow-emerald-600/20 transition-all flex items-center gap-2 active:scale-95 cursor-pointer shrink-0">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                </svg>
                <span>Formulary & Stock</span>
            </a>
        </div>
    </div>

    <!-- Balanced 4-Metric Command Deck -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- 1. Pending Prescriptions -->
        <a href="#queue-table" class="bg-white dark:bg-slate-900 rounded-3xl p-5 sm:p-6 border border-slate-200/90 dark:border-slate-800 shadow-xs hover:shadow-md transition-all group flex flex-col justify-between relative overflow-hidden">
            <div class="flex items-center justify-between mb-3">
                <div class="w-11 h-11 rounded-2xl bg-emerald-100/80 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center border border-emerald-500/20 shadow-xs group-hover:scale-105 transition-transform shrink-0">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                </div>
                <span class="px-2.5 py-1 rounded-full text-[10px] font-black uppercase tracking-wider bg-emerald-100 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800/60 shadow-xs animate-pulse">
                    Active Queue
                </span>
            </div>
            <div>
                <p id="pending-stat-count" class="text-3xl sm:text-4xl font-black text-slate-900 dark:text-white tracking-tight">
                    {{ $prescriptions->total() }}
                </p>
                <p class="text-xs text-slate-500 dark:text-slate-400 font-medium mt-1">Pending Orders Today</p>
            </div>
            <div class="mt-3 pt-2.5 border-t border-slate-100 dark:border-slate-800/80 flex items-center justify-between text-[11px] font-bold text-emerald-600 dark:text-emerald-400">
                <span>View Queue</span>
                <svg class="w-3.5 h-3.5 group-hover:translate-x-0.5 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
            </div>
        </a>

        <!-- 2. Expired Batches (Safety Alert) -->
        <a href="{{ route('pharmacy.medicines', ['status_filter' => 'expired']) }}" class="bg-white dark:bg-slate-900 rounded-3xl p-5 sm:p-6 border border-slate-200/90 dark:border-slate-800 shadow-xs hover:shadow-md transition-all group flex flex-col justify-between relative overflow-hidden">
            <div class="flex items-center justify-between mb-3">
                <div class="w-11 h-11 rounded-2xl bg-rose-100/80 dark:bg-rose-950/60 text-rose-600 dark:text-rose-400 flex items-center justify-center border border-rose-500/20 shadow-xs group-hover:scale-105 transition-transform shrink-0">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <span class="px-2.5 py-1 rounded-full text-[10px] font-black uppercase tracking-wider text-rose-700 dark:text-rose-300 bg-rose-100 dark:bg-rose-950/60 border border-rose-200 dark:border-rose-800/60 shadow-xs">
                    Purge Required
                </span>
            </div>
            <div>
                <p class="text-3xl sm:text-4xl font-black text-rose-600 dark:text-rose-400 tracking-tight">
                    {{ $expiredBatchesCount }}
                </p>
                <p class="text-xs text-slate-500 dark:text-slate-400 font-medium mt-1">Expired Batch(es)</p>
            </div>
            <div class="mt-3 pt-2.5 border-t border-slate-100 dark:border-slate-800/80 flex items-center justify-between text-[11px] font-bold text-rose-600 dark:text-rose-400">
                <span>Filter Expired</span>
                <svg class="w-3.5 h-3.5 group-hover:translate-x-0.5 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
            </div>
        </a>

        <!-- 3. Expiring Soon (<30 Days) -->
        <a href="{{ route('pharmacy.medicines', ['status_filter' => 'expiring_soon']) }}" class="bg-white dark:bg-slate-900 rounded-3xl p-5 sm:p-6 border border-slate-200/90 dark:border-slate-800 shadow-xs hover:shadow-md transition-all group flex flex-col justify-between relative overflow-hidden">
            <div class="flex items-center justify-between mb-3">
                <div class="w-11 h-11 rounded-2xl bg-amber-100/80 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400 flex items-center justify-center border border-amber-500/20 shadow-xs group-hover:scale-105 transition-transform shrink-0">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <span class="px-2.5 py-1 rounded-full text-[10px] font-black uppercase tracking-wider text-amber-800 dark:text-amber-300 bg-amber-100 dark:bg-amber-950/60 border border-amber-200 dark:border-amber-800/60 shadow-xs">
                    Within 30d
                </span>
            </div>
            <div>
                <p class="text-3xl sm:text-4xl font-black text-amber-600 dark:text-amber-400 tracking-tight">
                    {{ $expiringSoonCount }}
                </p>
                <p class="text-xs text-slate-500 dark:text-slate-400 font-medium mt-1">Expiring Soon Batches</p>
            </div>
            <div class="mt-3 pt-2.5 border-t border-slate-100 dark:border-slate-800/80 flex items-center justify-between text-[11px] font-bold text-amber-600 dark:text-amber-400">
                <span>View Watchlist</span>
                <svg class="w-3.5 h-3.5 group-hover:translate-x-0.5 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
            </div>
        </a>

        <!-- 4. Low & Depleted Stock -->
        <a href="{{ route('pharmacy.medicines', ['status_filter' => 'low_stock']) }}" class="bg-white dark:bg-slate-900 rounded-3xl p-5 sm:p-6 border border-slate-200/90 dark:border-slate-800 shadow-xs hover:shadow-md transition-all group flex flex-col justify-between relative overflow-hidden">
            <div class="flex items-center justify-between mb-3">
                <div class="w-11 h-11 rounded-2xl bg-blue-100/80 dark:bg-blue-950/60 text-blue-600 dark:text-blue-400 flex items-center justify-center border border-blue-500/20 shadow-xs group-hover:scale-105 transition-transform shrink-0">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" /></svg>
                </div>
                <span class="px-2.5 py-1 rounded-full text-[10px] font-black uppercase tracking-wider text-blue-800 dark:text-blue-300 bg-blue-100 dark:bg-blue-950/60 border border-blue-200 dark:border-blue-800/60 shadow-xs">
                    {{ $outOfStockCount }} Out of Stock
                </span>
            </div>
            <div>
                <p class="text-3xl sm:text-4xl font-black text-slate-900 dark:text-white tracking-tight">
                    {{ $lowStockCount }}
                </p>
                <p class="text-xs text-slate-500 dark:text-slate-400 font-medium mt-1">Low Stock Formulary Items</p>
            </div>
            <div class="mt-3 pt-2.5 border-t border-slate-100 dark:border-slate-800/80 flex items-center justify-between text-[11px] font-bold text-blue-600 dark:text-blue-400">
                <span>Replenish Stock</span>
                <svg class="w-3.5 h-3.5 group-hover:translate-x-0.5 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
            </div>
        </a>
    </div>

    <!-- Active Prescription Dispensing Queue Table -->
    <div id="queue-table" data-dynamic-block="true" class="bg-white dark:bg-slate-900 rounded-3xl shadow-xs border border-slate-200/90 dark:border-slate-800 overflow-hidden">
        <div class="px-6 py-5 border-b border-slate-100 dark:border-slate-800/80 bg-slate-50/70 dark:bg-slate-900/60 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <div class="flex items-center gap-2.5">
                    <h3 class="text-base sm:text-lg font-black text-slate-900 dark:text-white">Active Dispensing Queue</h3>
                    <span id="queue-count-badge" class="px-2.5 py-0.5 rounded-full text-xs font-black bg-emerald-100 dark:bg-emerald-950/70 text-emerald-800 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800 shadow-xs">
                        {{ $prescriptions->total() }} In Queue
                    </span>
                </div>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Pending prescription orders awaiting pharmacy inventory allocation and patient dispensing today.</p>
            </div>
            
            <div class="flex items-center gap-2 shrink-0">
                <button type="button" @click="refreshQueue()" title="Refresh Queue" 
                    class="h-9 px-3 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 hover:bg-slate-100 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-300 text-xs font-bold transition-all shadow-xs flex items-center gap-1.5 cursor-pointer active:scale-95">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                    <span>Refresh</span>
                </button>
            </div>
        </div>

        <div class="overflow-x-auto custom-scrollbar">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/90 dark:bg-slate-950/80 text-slate-500 dark:text-slate-400 text-[11px] font-extrabold uppercase tracking-wider border-b border-slate-200/80 dark:border-slate-800">
                        <th class="py-4 px-5 sm:px-6 w-[20%]">Order & Timestamp</th>
                        <th class="py-4 px-5 sm:px-6 flex-1">Citizen / Patient</th>
                        <th class="py-4 px-5 sm:px-6 w-[24%]">Attending Clinician</th>
                        <th class="py-4 px-5 sm:px-6 w-[18%]">Queue Status</th>
                        <th class="py-4 px-5 sm:px-6 w-[18%] text-right print:hidden min-w-[200px]">Action</th>
                    </tr>
                </thead>
                <tbody id="queue-tbody" class="divide-y divide-slate-100 dark:divide-slate-800 text-sm">
                    @forelse($prescriptions as $request)
                        <tr id="prescription-row-{{ $request->id }}" class="hover:bg-emerald-50/30 dark:hover:bg-slate-800/40 transition-colors group">
                            <!-- Order & Timestamp -->
                            <td class="py-5 px-5 sm:px-6 align-top">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-2xl bg-emerald-100/80 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 font-black flex items-center justify-center border border-emerald-500/20 shadow-xs shrink-0 text-xs">
                                        RX
                                    </div>
                                    <div>
                                        <p class="font-extrabold text-slate-900 dark:text-white text-sm">#{{ $request->id }}</p>
                                        <p class="text-[11px] font-semibold text-slate-500 dark:text-slate-400 mt-0.5">{{ $request->created_at->format('h:i A') }}</p>
                                        <span class="text-[10px] text-slate-400 dark:text-slate-500 font-medium">{{ $request->created_at->diffForHumans() }}</span>
                                    </div>
                                </div>
                            </td>

                            <!-- Patient Details -->
                            <td class="py-5 px-5 sm:px-6 align-top">
                                <p class="font-bold text-slate-900 dark:text-white text-sm sm:text-base tracking-tight group-hover:text-emerald-600 dark:group-hover:text-emerald-400 transition-colors">
                                    {{ $request->patient->full_name ?? 'Unknown Patient' }}
                                </p>
                                <div class="flex items-center gap-2 mt-1 flex-wrap">
                                    <span class="text-xs font-mono font-bold text-slate-600 dark:text-slate-300 bg-slate-100 dark:bg-slate-800 px-2 py-0.5 rounded-lg border border-slate-200 dark:border-slate-700">
                                        ID: {{ $request->patient->patient_id ?? '—' }}
                                    </span>
                                    <span class="text-[11px] text-slate-500 dark:text-slate-400 font-medium">
                                        {{ $request->patient->dob ? \Carbon\Carbon::parse($request->patient->dob)->age . ' yrs' : 'Age ?' }} • {{ $request->patient->sex ?? '—' }}
                                    </span>
                                </div>
                            </td>

                            <!-- Attending Clinician -->
                            <td class="py-5 px-5 sm:px-6 align-top">
                                <p class="text-sm font-bold text-slate-800 dark:text-slate-200">{{ $request->doctor->formatted_name ?? 'Attending Clinician' }}</p>
                                <div class="flex items-center gap-1.5 mt-1">
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[11px] font-bold bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-700">
                                        <svg class="w-3 h-3 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                                        <span>{{ $request->items->count() }} item(s) ordered</span>
                                    </span>
                                </div>
                            </td>

                            <!-- Status & Expiry -->
                            <td id="status-cell-{{ $request->id }}" class="py-5 px-5 sm:px-6 align-top">
                                <div class="flex flex-col items-start gap-1">
                                    @if($request->status === 'partially_dispensed')
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider bg-purple-100 text-purple-800 dark:bg-purple-950/60 dark:text-purple-300 border border-purple-200 dark:border-purple-800/60 shadow-xs">
                                            <span class="w-1.5 h-1.5 rounded-full bg-purple-500 animate-pulse"></span>
                                            Partially Dispensed
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider bg-amber-100 text-amber-800 dark:bg-amber-950/60 dark:text-amber-300 border border-amber-200 dark:border-amber-800/60 shadow-xs">
                                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                            Awaiting Dispense
                                        </span>
                                    @endif

                                    @if($request->expires_at)
                                        @php
                                            $expiryDays = (int) now()->startOfDay()->diffInDays($request->expires_at->copy()->startOfDay(), true);
                                            $isPast = $request->expires_at->startOfDay()->lt(now()->startOfDay());
                                        @endphp
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-md text-[10px] font-bold
                                            @if($isPast)
                                                bg-rose-100 text-rose-800 dark:bg-rose-950/60 dark:text-rose-300 border border-rose-200 dark:border-rose-800/60
                                            @elseif($expiryDays === 0)
                                                bg-amber-100 text-amber-800 dark:bg-amber-950/60 dark:text-amber-300 border border-amber-200 dark:border-amber-800/60
                                            @else
                                                bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300 border border-slate-200 dark:border-slate-700
                                            @endif
                                        ">
                                            <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            @if($isPast)
                                                Expired {{ $expiryDays }}d ago
                                            @elseif($expiryDays === 0)
                                                Expires today
                                            @else
                                                Expires in {{ $expiryDays }}d
                                            @endif
                                        </span>
                                    @endif
                                </div>
                            </td>

                            <!-- Action -->
                            <td class="py-5 px-5 sm:px-6 align-top text-right print:hidden whitespace-nowrap min-w-[200px]">
                                <div class="inline-flex items-center justify-end gap-2.5">
                                    <button type="button"
                                        @click="openModal({{ $request->id }})"
                                        class="h-9 px-3.5 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white text-xs font-bold shadow-md shadow-emerald-600/20 transition-all inline-flex items-center gap-1.5 active:scale-95 cursor-pointer shrink-0">
                                        <span>Review &amp; Dispense</span>
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                                    </button>

                                    <button type="button"
                                        @click="requestCancel({{ $request->id }})"
                                        title="Cancel this prescription"
                                        class="w-9 h-9 min-w-[36px] min-h-[36px] rounded-xl text-rose-600 dark:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-950/40 bg-slate-100 dark:bg-slate-800 border border-rose-200 dark:border-rose-800/60 transition-all inline-flex items-center justify-center active:scale-95 cursor-pointer shrink-0">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-16 px-6 text-center text-slate-500 dark:text-slate-400">
                                <div class="w-16 h-16 rounded-3xl bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 mx-auto flex items-center justify-center mb-4 shadow-xs border border-emerald-500/20">
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                </div>
                                <h3 class="text-base font-extrabold text-slate-900 dark:text-white mb-1">Queue is Clear</h3>
                                <p class="text-xs text-slate-500 dark:text-slate-400 max-w-sm mx-auto leading-relaxed">
                                    There are currently no active or pending prescription orders awaiting fulfillment.
                                </p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($prescriptions->hasPages())
            <div class="px-6 py-4 border-t border-slate-100 dark:border-slate-800 bg-slate-50/70 dark:bg-slate-900/60">
                {{ $prescriptions->appends(request()->query())->links('vendor.pagination.shadcn') }}
            </div>
        @endif
    </div>

    <!-- Analytics & Consumption Insights Section -->
    <div class="space-y-4 pt-2">
        <div class="border-b border-slate-200/80 dark:border-slate-800 pb-3 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <h3 class="text-base sm:text-lg font-bold text-slate-900 dark:text-white flex items-center gap-2.5">
                <span class="w-8 h-8 rounded-xl bg-emerald-100/80 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center border border-emerald-500/20 shadow-xs">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                </span>
                <span>Inventory & Consumption Analytics</span>
            </h3>

            <div class="flex items-center gap-3">
                <span class="hidden sm:inline-block text-xs font-semibold text-slate-400 dark:text-slate-500">Live Formulary Activity</span>
                <button type="button" onclick="exportAnalyticsCsv(this)" id="exportAnalyticsBtn"
                    class="h-9 px-3.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 hover:bg-slate-50 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-200 text-xs font-bold transition-all shadow-2xs flex items-center gap-2 cursor-pointer active:scale-95 shrink-0">
                    <svg id="exportAnalyticsIcon" class="w-3.5 h-3.5 text-emerald-600 dark:text-emerald-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <svg id="exportAnalyticsSpinner" class="hidden animate-spin h-3.5 w-3.5 text-emerald-600 dark:text-emerald-400 shrink-0" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span id="exportAnalyticsText">Export CSV</span>
                </button>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Top Dispensed Chart -->
            <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 sm:p-7 border border-slate-200/90 dark:border-slate-800 shadow-xs">
                <div class="flex items-center justify-between mb-4 pb-3 border-b border-slate-100 dark:border-slate-800/80">
                    <div>
                        <h4 class="text-sm font-bold text-slate-900 dark:text-white">Top Dispensed Medicines</h4>
                        <p class="text-[11px] text-slate-400">Total volume released in the last 30 days</p>
                    </div>
                    <span class="text-xs font-bold text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-950/40 px-2.5 py-1 rounded-lg border border-emerald-500/20 shadow-xs">30-Day Window</span>
                </div>
                <div class="h-64 relative">
                    <canvas id="topDispensedChart"></canvas>
                </div>
            </div>

            <!-- Dispensing Trend Chart -->
            <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 sm:p-7 border border-slate-200/90 dark:border-slate-800 shadow-xs">
                <div class="flex items-center justify-between mb-4 pb-3 border-b border-slate-100 dark:border-slate-800/80">
                    <div>
                        <h4 class="text-sm font-bold text-slate-900 dark:text-white">Monthly Dispensing Trend</h4>
                        <p class="text-[11px] text-slate-400">Longitudinal dispensing history across 6 months</p>
                    </div>
                    <span class="text-xs font-bold text-teal-600 dark:text-teal-400 bg-teal-50 dark:bg-teal-950/40 px-2.5 py-1 rounded-lg border border-teal-500/20 shadow-xs">6-Month Trend</span>
                </div>
                <div class="h-64 relative">
                    <canvas id="monthlyTrendChart"></canvas>
                </div>
            </div>
        </div>
    </div>

{{-- Dispense Modals for Active Prescriptions --}}
@foreach($prescriptions as $request)
    <template x-teleport="body">
        <div x-show="openId === {{ $request->id }}" x-cloak @keydown.escape.window="closeModal()"
             class="fixed inset-0 z-[999] overflow-y-auto w-full h-full text-left"
             aria-labelledby="modal-title-{{ $request->id }}" role="dialog" aria-modal="true">
            <!-- Fullscreen Window-Wide Backdrop Overlay with Blur -->
            <div x-show="openId === {{ $request->id }}"
                 x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                 x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                 class="fixed inset-0 bg-slate-950/75 backdrop-blur-md transition-opacity"
                 @click="closeModal()" aria-hidden="true"></div>

            <!-- Modal Positioning Container -->
            <div class="flex items-center justify-center min-h-screen px-4 py-6 sm:py-10 relative z-10 pointer-events-none">
                <div class="bg-white dark:bg-slate-900 rounded-3xl shadow-2xl overflow-hidden border border-slate-200/90 dark:border-slate-800 max-w-2xl w-full flex flex-col max-h-[92vh] pointer-events-auto transition-all transform duration-200"
                     x-show="openId === {{ $request->id }}"
                     x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     @click.stop>
                    
                    <!-- Pinned Modal Header -->
                    <div class="px-6 py-4 sm:px-7 sm:py-5 border-b border-slate-200/80 dark:border-slate-800 bg-slate-50/80 dark:bg-slate-900/80 backdrop-blur-sm flex items-center justify-between gap-4 shrink-0">
                    <div class="flex items-center gap-3.5">
                        <div class="w-11 h-11 rounded-2xl bg-gradient-to-br from-emerald-500 to-teal-600 text-white flex items-center justify-center shadow-md shadow-emerald-500/20 shrink-0">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                            </svg>
                        </div>
                        <div>
                            <div class="flex items-center gap-2">
                                <h3 class="text-base sm:text-lg font-black text-slate-900 dark:text-white tracking-tight">
                                    Dispense Prescription #{{ $request->id }}
                                </h3>
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-emerald-100 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800/60">
                                    Live Check
                                </span>
                            </div>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                                Verify batch availability and authorize medication release
                            </p>
                        </div>
                    </div>

                    <button type="button" @click="closeModal()" 
                        title="Close (Esc)"
                        class="w-9 h-9 min-w-[36px] min-h-[36px] rounded-xl text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 hover:bg-slate-200/60 dark:hover:bg-slate-800 transition-colors flex items-center justify-center cursor-pointer shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Modal Body Form with Internal Scroll -->
                <form id="dispenseForm-{{ $request->id }}" action="{{ route('pharmacy.dispense', $request->id) }}" method="POST"
                      @submit.prevent="submitDispense($event, {{ $request->id }})"
                      class="flex flex-col flex-1 min-h-0">
                    @csrf

                    <div class="p-6 sm:p-7 overflow-y-auto flex-1 space-y-5">

                        <!-- Clinical Deck: Patient & Prescribing Clinician -->
                        <div class="rounded-2xl p-4 bg-slate-50 dark:bg-slate-800/60 border border-slate-200/80 dark:border-slate-700/60 grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <!-- Patient Profile -->
                            <div class="flex items-start gap-3">
                                <div class="w-10 h-10 rounded-xl bg-emerald-100 dark:bg-emerald-950/80 text-emerald-700 dark:text-emerald-300 flex items-center justify-center font-black text-xs shrink-0 border border-emerald-500/20 shadow-xs">
                                    {{ strtoupper(substr($request->patient->first_name ?? 'P', 0, 1) . substr($request->patient->last_name ?? 'T', 0, 1)) }}
                                </div>
                                <div class="min-w-0">
                                    <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 dark:text-slate-500 block">Patient Profile</span>
                                    <p class="font-bold text-slate-900 dark:text-white text-sm truncate">
                                        {{ $request->patient->full_name ?? 'Unknown Patient' }}
                                    </p>
                                    <div class="flex items-center gap-2 mt-0.5 text-xs text-slate-500 dark:text-slate-400 font-mono">
                                        <span>ID: {{ $request->patient->patient_id ?? '—' }}</span>
                                        @if($request->patient->dob)
                                            <span class="text-slate-300 dark:text-slate-700">•</span>
                                            <span>{{ \Carbon\Carbon::parse($request->patient->dob)->age }}y</span>
                                        @endif
                                        @if($request->patient->sex)
                                            <span class="text-slate-300 dark:text-slate-700">•</span>
                                            <span>{{ ucfirst($request->patient->sex) }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- Prescribing Clinician -->
                            <div class="flex items-start gap-3 sm:border-l border-slate-200/70 dark:border-slate-700/60 pt-3 sm:pt-0 sm:pl-4 border-t sm:border-t-0">
                                <div class="w-10 h-10 rounded-xl bg-teal-100 dark:bg-teal-950/80 text-teal-700 dark:text-teal-300 flex items-center justify-center shrink-0 border border-teal-500/20 shadow-xs">
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                </div>
                                <div class="min-w-0">
                                    <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 dark:text-slate-500 block">Prescribing Doctor</span>
                                    <p class="font-bold text-slate-900 dark:text-white text-sm truncate">
                                        {{ $request->doctor->formatted_name ?? 'Attending Clinician' }}
                                    </p>
                                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                                        Queued {{ $request->created_at ? $request->created_at->format('M d, Y • h:i A') : 'Today' }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Requested Medications Section -->
                        <div>
                            <div class="flex items-center justify-between mb-3">
                                <h4 class="text-xs font-black uppercase text-slate-400 dark:text-slate-500 tracking-wider flex items-center gap-1.5">
                                    <span>Requested Medications</span>
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400">
                                        {{ $request->items->count() }} item(s)
                                    </span>
                                </h4>
                                <span class="text-[11px] font-medium text-slate-400 dark:text-slate-500">FIFO Stock Allocation</span>
                            </div>

                            <div class="space-y-3.5">
                                @foreach($request->items as $index => $item)
                                    @php
                                        $stock = $stockSummary[$item->id] ?? ['available' => 0, 'earliest_expiry' => null, 'tracked' => false];
                                        $maxAllowed = (int) $item->outstanding_quantity;
                                        $availableStock = (int) ($stock['available'] ?? 0);
                                        $short = $maxAllowed > 0 && $availableStock < $maxAllowed;
                                        $isOutOfStock = $stock['tracked'] && $availableStock <= 0;
                                        $isUntracked = ! $stock['tracked'];
                                        // Intelligent default: if out of stock, default to 0 to prevent immediate validation rejection;
                                        // otherwise fill with available stock up to the outstanding limit.
                                        $defaultDispense = ($availableStock > 0) ? min($availableStock, $maxAllowed) : 0;
                                    @endphp
                                    
                                    <div class="p-4 sm:p-5 rounded-2xl border border-slate-200/90 dark:border-slate-800 bg-white dark:bg-slate-800/70 space-y-3.5 shadow-2xs hover:border-slate-300 dark:hover:border-slate-700 transition"
                                         :class="hasIssue({{ $item->id }}) ? 'border-rose-400 dark:border-rose-600 ring-2 ring-rose-400/20' : ''">
                                        
                                        <!-- Medication Header & Quantity Breakdown -->
                                        <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-3">
                                            <div class="flex-1">
                                                <div class="flex items-center gap-2">
                                                    <span class="w-6 h-6 rounded-lg bg-emerald-100/70 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0">
                                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                                        </svg>
                                                    </span>
                                                    <h5 class="font-extrabold text-slate-900 dark:text-white text-sm sm:text-base">
                                                        {{ $item->medicine_name }}
                                                    </h5>
                                                </div>

                                                <!-- Dosage & Instructions -->
                                                <div class="flex flex-wrap items-center gap-1.5 mt-2">
                                                    @if($item->dosage)
                                                        <span class="px-2 py-0.5 rounded-md bg-slate-100 dark:bg-slate-700/60 text-slate-700 dark:text-slate-300 text-xs font-semibold">
                                                            {{ $item->dosage }}
                                                        </span>
                                                    @endif
                                                    @if($item->frequency)
                                                        <span class="px-2 py-0.5 rounded-md bg-slate-100 dark:bg-slate-700/60 text-slate-700 dark:text-slate-300 text-xs font-semibold">
                                                            {{ $item->frequency }}
                                                        </span>
                                                    @endif
                                                    @if($item->duration)
                                                        <span class="px-2 py-0.5 rounded-md bg-slate-100 dark:bg-slate-700/60 text-slate-700 dark:text-slate-300 text-xs font-semibold">
                                                            {{ $item->duration }}
                                                        </span>
                                                    @endif
                                                </div>

                                                @if($item->instructions || $item->frequency)
                                                    <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-2 bg-slate-50 dark:bg-slate-900/60 px-2.5 py-1 rounded-lg border border-slate-200/60 dark:border-slate-800 inline-block">
                                                        <span class="font-bold text-slate-700 dark:text-slate-300">Sig:</span> {{ $item->frequency }} {{ $item->instructions ? '— ' . $item->instructions : '' }}
                                                    </p>
                                                @endif
                                            </div>

                                            <!-- 3-Metric Order Breakdown -->
                                            <div class="grid grid-cols-3 gap-2 text-center p-2 rounded-xl bg-slate-50 dark:bg-slate-900/70 border border-slate-200/60 dark:border-slate-800 shrink-0">
                                                <div class="px-2">
                                                    <span class="text-[9px] font-extrabold uppercase tracking-wider text-slate-400 dark:text-slate-500 block">Ordered</span>
                                                    <span class="text-xs font-black text-slate-800 dark:text-slate-200">{{ $item->quantity ?: 'N/A' }}</span>
                                                </div>
                                                <div class="px-2 border-x border-slate-200 dark:border-slate-700/60">
                                                    <span class="text-[9px] font-extrabold uppercase tracking-wider text-slate-400 dark:text-slate-500 block">Released</span>
                                                    <span class="text-xs font-black text-slate-600 dark:text-slate-400">{{ $item->dispensed_quantity ?? 0 }}</span>
                                                </div>
                                                <div class="px-2">
                                                    <span class="text-[9px] font-extrabold uppercase tracking-wider text-emerald-600 dark:text-emerald-400 block">Pending</span>
                                                    <span class="text-xs font-black text-emerald-600 dark:text-emerald-400">{{ $item->outstanding_quantity }}</span>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Stock Safety Evaluation Callout -->
                                        @if($isUntracked)
                                            <div class="p-3 rounded-xl bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 flex items-center justify-between gap-2.5 text-xs">
                                                <div class="flex items-center gap-2">
                                                    <svg class="w-4 h-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                    </svg>
                                                    <span class="font-semibold text-slate-700 dark:text-slate-300">Medication not tracked in RHU formulary batches</span>
                                                </div>
                                                <span class="px-2 py-0.5 rounded-md text-[10px] font-black uppercase tracking-wider bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-300 shrink-0">
                                                    Untracked
                                                </span>
                                            </div>
                                        @elseif($isOutOfStock)
                                            <div class="p-3 rounded-xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200/80 dark:border-rose-900/60 flex items-start gap-2.5 text-xs">
                                                <svg class="w-4 h-4 text-rose-600 dark:text-rose-400 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                                </svg>
                                                <div class="flex-1">
                                                    <p class="font-bold text-rose-700 dark:text-rose-300">Out of Stock</p>
                                                    <p class="text-[11px] text-rose-600 dark:text-rose-400 mt-0.5">
                                                        No unexpired batches are available in the pharmacy. This item cannot be fulfilled from stock.
                                                    </p>
                                                </div>
                                                <span class="px-2 py-0.5 rounded-md text-[10px] font-black uppercase tracking-wider bg-rose-100 dark:bg-rose-900/60 text-rose-700 dark:text-rose-300 shrink-0">
                                                    0 Units Available
                                                </span>
                                            </div>
                                        @elseif($short)
                                            <div class="p-3 rounded-xl bg-amber-50 dark:bg-amber-950/40 border border-amber-200/80 dark:border-amber-900/60 flex items-start gap-2.5 text-xs">
                                                <svg class="w-4 h-4 text-amber-600 dark:text-amber-400 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                                </svg>
                                                <div class="flex-1">
                                                    <p class="font-bold text-amber-800 dark:text-amber-300">Partial Stock Available</p>
                                                    <p class="text-[11px] text-amber-700 dark:text-amber-400 mt-0.5">
                                                        Only {{ $availableStock }} unit(s) in active stock (short by {{ $maxAllowed - $availableStock }}).
                                                        @if($stock['earliest_expiry'])
                                                            <span class="block text-[10px] text-amber-600 dark:text-amber-400/80 mt-0.5">Earliest batch expiry: {{ $stock['earliest_expiry']->format('F Y') }}</span>
                                                        @endif
                                                    </p>
                                                </div>
                                                <span class="px-2 py-0.5 rounded-md text-[10px] font-black uppercase tracking-wider bg-amber-100 dark:bg-amber-900/60 text-amber-800 dark:text-amber-300 shrink-0">
                                                    {{ $availableStock }} Available
                                                </span>
                                            </div>
                                        @else
                                            <div class="p-3 rounded-xl bg-emerald-50/70 dark:bg-emerald-950/30 border border-emerald-200/70 dark:border-emerald-800/50 flex items-center justify-between gap-3 text-xs">
                                                <div class="flex items-center gap-2">
                                                    <span class="w-5 h-5 rounded-full bg-emerald-100 dark:bg-emerald-900/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0">
                                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                                    </span>
                                                    <div>
                                                        <span class="font-bold text-emerald-800 dark:text-emerald-300">Stock Verified</span>
                                                        <span class="text-slate-400 dark:text-slate-500 mx-1">•</span>
                                                        <span class="text-[11px] text-slate-600 dark:text-slate-400">{{ $availableStock }} units ready in pharmacy</span>
                                                        @if($stock['earliest_expiry'])
                                                            <span class="text-[10px] text-slate-400 dark:text-slate-500 block">Earliest expiry: {{ $stock['earliest_expiry']->format('F Y') }}</span>
                                                        @endif
                                                    </div>
                                                </div>
                                                <span class="px-2 py-0.5 rounded-md text-[10px] font-black uppercase tracking-wider bg-emerald-100 dark:bg-emerald-900/50 text-emerald-700 dark:text-emerald-300 shrink-0">
                                                    In Stock
                                                </span>
                                            </div>
                                        @endif

                                        <!-- Stepper Quantity Dispenser -->
                                        @if($maxAllowed > 0)
                                            <div class="pt-3 border-t border-slate-100 dark:border-slate-800/80 flex flex-col sm:flex-row sm:items-center justify-between gap-3"
                                                 x-data="{ 
                                                     qty: {{ $defaultDispense }},
                                                     max: {{ $maxAllowed }},
                                                     inStock: {{ $availableStock }}
                                                 }">
                                                <div>
                                                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300">
                                                        Quantity to Dispense Now
                                                    </label>
                                                    <p class="text-[11px] text-slate-400 dark:text-slate-500">
                                                        Outstanding: <span class="font-bold text-slate-600 dark:text-slate-300">{{ $maxAllowed }}</span> units
                                                        @if($availableStock > 0 && $availableStock < $maxAllowed)
                                                            <span class="text-amber-500 font-semibold">• Max in stock: {{ $availableStock }}</span>
                                                        @endif
                                                    </p>
                                                </div>

                                                <!-- Stepper Input Group -->
                                                <div class="flex items-center gap-2">
                                                    <div class="inline-flex items-center rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50/90 dark:bg-slate-900/80 p-1 shadow-2xs">
                                                        <!-- Decrement Button -->
                                                        <button type="button" 
                                                            @click="qty = Math.max(0, (parseInt(qty) || 0) - 1); $refs.qtyInput.value = qty; clearIssue({{ $item->id }})"
                                                            class="w-8 h-8 rounded-lg bg-white dark:bg-slate-800 hover:bg-slate-100 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-300 flex items-center justify-center font-bold text-sm shadow-xs border border-slate-200/70 dark:border-slate-700 transition active:scale-95 cursor-pointer">
                                                            −
                                                        </button>

                                                        <!-- Number Input -->
                                                        <input type="number"
                                                               x-ref="qtyInput"
                                                               name="items[{{ $index }}][quantity]"
                                                               :value="qty"
                                                               @input="qty = parseInt($event.target.value) || 0; clearIssue({{ $item->id }})"
                                                               min="0"
                                                               max="{{ $maxAllowed }}"
                                                               data-item-id="{{ $item->id }}"
                                                               class="w-16 sm:w-20 text-center font-black text-slate-900 dark:text-white bg-transparent border-0 focus:ring-0 text-sm py-1.5 [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none"
                                                               :class="hasIssue({{ $item->id }}) ? 'text-rose-600 dark:text-rose-400 font-extrabold' : ''">

                                                        <!-- Increment Button -->
                                                        <button type="button" 
                                                            @click="qty = Math.min(max, (parseInt(qty) || 0) + 1); $refs.qtyInput.value = qty; clearIssue({{ $item->id }})"
                                                            class="w-8 h-8 rounded-lg bg-white dark:bg-slate-800 hover:bg-slate-100 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-300 flex items-center justify-center font-bold text-sm shadow-xs border border-slate-200/70 dark:border-slate-700 transition active:scale-95 cursor-pointer">
                                                            +
                                                        </button>
                                                    </div>

                                                    <!-- Quick Fill / Zero Actions -->
                                                    <div class="flex items-center gap-1">
                                                        @if($availableStock > 0)
                                                            <button type="button" 
                                                                @click="qty = Math.min(max, inStock); $refs.qtyInput.value = qty; clearIssue({{ $item->id }})"
                                                                title="Fill maximum available quantity"
                                                                class="h-8 px-2.5 rounded-lg bg-emerald-50 dark:bg-emerald-950/50 hover:bg-emerald-100 dark:hover:bg-emerald-900/60 text-emerald-700 dark:text-emerald-300 text-[11px] font-bold border border-emerald-200/80 dark:border-emerald-800/60 transition active:scale-95 cursor-pointer shrink-0">
                                                                Max
                                                            </button>
                                                        @endif
                                                        <button type="button" 
                                                            @click="qty = 0; $refs.qtyInput.value = 0; clearIssue({{ $item->id }})"
                                                            title="Set quantity to 0"
                                                            class="h-8 px-2.5 rounded-lg bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-500 dark:text-slate-400 text-[11px] font-bold border border-slate-200 dark:border-slate-700 transition active:scale-95 cursor-pointer shrink-0">
                                                            0
                                                        </button>
                                                    </div>

                                                    <input type="hidden" name="items[{{ $index }}][item_id]" value="{{ $item->id }}">
                                                </div>
                                            </div>
                                        @else
                                            <div class="pt-3 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between">
                                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800/60">
                                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                                    Fully Dispensed
                                                </span>
                                                <input type="hidden" name="items[{{ $index }}][item_id]" value="{{ $item->id }}">
                                                <input type="hidden" name="items[{{ $index }}][quantity]" value="0">
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- Pharmacist Notes Section -->
                        <div class="pt-2">
                            <label for="pharmacist_notes_{{ $request->id }}" class="flex items-center gap-1.5 text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                                <svg class="w-3.5 h-3.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                                <span>Pharmacist Notes & Counseling Remarks (Optional)</span>
                            </label>
                            <textarea id="pharmacist_notes_{{ $request->id }}" name="pharmacist_notes" rows="2" 
                                      class="w-full text-xs sm:text-sm rounded-xl border border-slate-200 dark:border-slate-700 focus:border-emerald-500 focus:ring-emerald-500/20 py-2.5 px-3.5 bg-slate-50/60 dark:bg-slate-800/80 placeholder-slate-400 text-slate-800 dark:text-slate-100 transition resize-none shadow-2xs"
                                      placeholder="Add dispensing notes, substitution remarks, or patient counseling instructions..."></textarea>
                        </div>
                    </div>

                    <!-- Pinned Modal Actions Footer -->
                    <div class="px-6 py-4 sm:px-7 border-t border-slate-200/90 dark:border-slate-800 bg-slate-50/80 dark:bg-slate-900/80 backdrop-blur-sm flex flex-col-reverse sm:flex-row items-center justify-between gap-3 shrink-0">
                        <div class="flex items-center gap-1.5 text-[11px] text-slate-400 dark:text-slate-500 text-center sm:text-left">
                            <svg class="w-3.5 h-3.5 text-emerald-600 dark:text-emerald-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                            </svg>
                            <span>Deducted strictly using FEFO unexpired batches</span>
                        </div>

                        <div class="flex items-center gap-2.5 w-full sm:w-auto justify-end">
                            <button type="button" @click="closeModal()" 
                                class="h-10 px-4 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-300 text-xs font-bold hover:bg-slate-50 dark:hover:bg-slate-700 transition active:scale-95 cursor-pointer shrink-0">
                                Cancel
                            </button>
                            <button type="submit" :disabled="submitting" 
                                class="h-10 px-5 rounded-xl text-xs font-bold bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white transition shadow-md shadow-emerald-600/20 cursor-pointer active:scale-95 disabled:opacity-70 disabled:cursor-not-allowed flex items-center justify-center gap-2 shrink-0">
                                <svg x-show="submitting" x-cloak class="animate-spin -ml-1 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <svg x-show="!submitting" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                <span x-text="submitting ? 'Dispensing…' : 'Confirm & Dispense'">Confirm & Dispense</span>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    </template>
@endforeach
</div>

@php
    $pharmacyQueue = $prescriptions->mapWithKeys(function ($p) {
        return [
            $p->id => [
                'action' => route('pharmacy.prescription.cancel', $p->id),
                'patient' => $p->patient->full_name ?? 'Unknown',
                'releasedUnits' => (int) $p->items->sum(function ($i) { return (int) ($i->dispensed_quantity ?? 0); }),
                'outstandingUnits' => (int) $p->outstandingTotal(),
                'totalUnits' => (int) $p->items->sum(function ($i) { return (int) ($i->quantity ?? 0); }),
            ],
        ];
    })->all();

    $queueExportRows = $prescriptions->map(function ($p) {
        $meds = $p->items->map(function ($i) {
            return ($i->medicine_name ?? 'Medication') . ' (' . ($i->dosage ?? '') . ' - ' . ($i->quantity ?? 0) . ' qty)';
        })->implode('; ');

        return [
            'id' => $p->id,
            'queued_at' => $p->created_at ? $p->created_at->format('Y-m-d H:i:s') : '',
            'patient_name' => $p->patient->full_name ?? 'Unknown',
            'patient_id' => $p->patient->patient_id ?? '—',
            'age' => $p->patient->dob ? \Carbon\Carbon::parse($p->patient->dob)->age : '—',
            'sex' => $p->patient->sex ?? '—',
            'clinician' => $p->doctor->formatted_name ?? 'Attending Clinician',
            'items_count' => $p->items->count(),
            'medications' => $meds,
            'status' => ucfirst(str_replace('_', ' ', $p->status)),
            'expires_at' => $p->expires_at ? $p->expires_at->format('Y-m-d') : '—',
        ];
    })->values()->all();

    $topDispensedExport = $topDispensed->map(function ($item, $index) {
        return [
            'rank' => $index + 1,
            'brand_name' => $item->medicine ? ($item->medicine->name ?? '—') : '—',
            'generic_name' => $item->medicine ? ($item->medicine->generic_name ?? '—') : '—',
            'total_dispensed' => (int) $item->total_dispensed,
        ];
    })->values()->all();

    $monthlyTrendExport = $monthlyTrend->map(function ($item) {
        return [
            'period' => $item->label ?? ($item->year . '-' . str_pad($item->month, 2, '0', STR_PAD_LEFT)),
            'total_dispensed' => (int) $item->total_dispensed,
        ];
    })->values()->all();

    $analyticsSummaryExport = [
        'generated_at' => now()->format('Y-m-d H:i:s'),
        'total_formulary_medicines' => (int) ($totalMedicines ?? 0),
        'low_stock_medicines' => (int) ($lowStockCount ?? 0),
        'out_of_stock_medicines' => (int) ($outOfStockCount ?? 0),
        'expired_batches' => (int) ($expiredBatchesCount ?? 0),
        'expiring_soon_batches' => (int) ($expiringSoonCount ?? 0),
    ];
@endphp

@push('scripts')
<script>
    const PHARMACY_QUEUE = @json($pharmacyQueue);

    function exportQueueCsv(btn) {
        const icon = document.getElementById('exportQueueIcon');
        const spinner = document.getElementById('exportQueueSpinner');
        const text = document.getElementById('exportQueueText');

        const rows = @json($queueExportRows);

        if (!rows || rows.length === 0) {
            window.dispatchEvent(new CustomEvent('add-toast', { 
                detail: { type: 'warning', message: 'No active prescriptions in the queue to export.' } 
            }));
            return;
        }

        if (btn) btn.disabled = true;
        if (icon) icon.classList.add('hidden');
        if (spinner) spinner.classList.remove('hidden');
        if (text) text.textContent = 'Generating CSV...';

        setTimeout(() => {
            try {
                const headers = [
                    'Prescription #',
                    'Queued Date & Time',
                    'Patient Full Name',
                    'Patient ID',
                    'Age',
                    'Sex',
                    'Attending Clinician',
                    'Items Ordered Count',
                    'Medications & Quantities',
                    'Queue Status',
                    'Order Expiration'
                ];

                const csvLines = [headers.map(h => `"${h.replace(/"/g, '""')}"`).join(',')];

                rows.forEach(r => {
                    const rowData = [
                        `#${r.id}`,
                        r.queued_at,
                        r.patient_name,
                        r.patient_id,
                        r.age,
                        r.sex,
                        r.clinician,
                        r.items_count,
                        r.medications,
                        r.status,
                        r.expires_at
                    ];
                    csvLines.push(rowData.map(val => `"${String(val ?? '').replace(/"/g, '""')}"`).join(','));
                });

                const csvContent = '\uFEFF' + csvLines.join('\r\n');
                const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
                const url = URL.createObjectURL(blob);
                const a = document.createElement('a');
                const dateStr = new Date().toISOString().slice(0, 10);
                a.href = url;
                a.download = `pharmacy-active-queue-${dateStr}.csv`;
                document.body.appendChild(a);
                a.click();
                document.body.removeChild(a);
                URL.revokeObjectURL(url);

                window.dispatchEvent(new CustomEvent('add-toast', { 
                    detail: { type: 'success', message: `Exported ${rows.length} queue order(s) to CSV successfully!` } 
                }));
            } catch (err) {
                console.error('Queue CSV export failed:', err);
                window.dispatchEvent(new CustomEvent('add-toast', { 
                    detail: { type: 'error', message: 'Failed to generate queue CSV export.' } 
                }));
            } finally {
                if (btn) btn.disabled = false;
                if (icon) icon.classList.remove('hidden');
                if (spinner) spinner.classList.add('hidden');
                if (text) text.textContent = 'Export Queue (CSV)';
            }
        }, 250);
    }
    window.exportQueueCsv = exportQueueCsv;

    function exportAnalyticsCsv(btn) {
        const icon = document.getElementById('exportAnalyticsIcon');
        const spinner = document.getElementById('exportAnalyticsSpinner');
        const text = document.getElementById('exportAnalyticsText');

        const summary = @json($analyticsSummaryExport);
        const topDispensed = @json($topDispensedExport);
        const monthlyTrend = @json($monthlyTrendExport);

        if ((!topDispensed || topDispensed.length === 0) && (!monthlyTrend || monthlyTrend.length === 0)) {
            window.dispatchEvent(new CustomEvent('add-toast', { 
                detail: { type: 'warning', message: 'No consumption analytics data found to export.' } 
            }));
            return;
        }

        if (btn) btn.disabled = true;
        if (icon) icon.classList.add('hidden');
        if (spinner) spinner.classList.remove('hidden');
        if (text) text.textContent = 'Generating CSV...';

        setTimeout(() => {
            try {
                const escapeCsv = (str) => `"${String(str ?? '').replace(/"/g, '""')}"`;
                const csvLines = [];

                // Report Header
                csvLines.push(['Rural Health Unit - Inventory & Consumption Analytics Report'].map(escapeCsv).join(','));
                csvLines.push([`Generated On: ${summary.generated_at}`].map(escapeCsv).join(','));
                csvLines.push([]);

                // Executive KPIs
                csvLines.push(['--- INVENTORY HEALTH SUMMARY ---'].map(escapeCsv).join(','));
                csvLines.push(['Metric', 'Count / Value'].map(escapeCsv).join(','));
                csvLines.push(['Total Active Medicines in Formulary', summary.total_formulary_medicines].map(escapeCsv).join(','));
                csvLines.push(['Stock Depleted / Out of Stock', summary.out_of_stock_medicines].map(escapeCsv).join(','));
                csvLines.push(['Low Stock Medicines (Under 20 units)', summary.low_stock_medicines].map(escapeCsv).join(','));
                csvLines.push(['Expired Batches (Purge Required)', summary.expired_batches].map(escapeCsv).join(','));
                csvLines.push(['Expiring Batches (Within 30 Days)', summary.expiring_soon_batches].map(escapeCsv).join(','));
                csvLines.push([]);

                // Section 1: Top Dispensed Medicines (Last 30 Days)
                csvLines.push(['--- TOP DISPENSED MEDICINES (LAST 30 DAYS) ---'].map(escapeCsv).join(','));
                csvLines.push(['Rank', 'Brand Name', 'Generic Name', 'Total Volume Dispensed (Units)'].map(escapeCsv).join(','));
                if (topDispensed && topDispensed.length > 0) {
                    topDispensed.forEach(item => {
                        csvLines.push([
                            `#${item.rank}`,
                            item.brand_name,
                            item.generic_name,
                            item.total_dispensed
                        ].map(escapeCsv).join(','));
                    });
                } else {
                    csvLines.push(['—', 'No dispensed records found in the last 30 days', '—', '0'].map(escapeCsv).join(','));
                }
                csvLines.push([]);

                // Section 2: 6-Month Longitudinal Dispensing Trend
                csvLines.push(['--- MONTHLY DISPENSING TREND (LAST 6 MONTHS) ---'].map(escapeCsv).join(','));
                csvLines.push(['Month / Period', 'Total Volume Dispensed (Units)'].map(escapeCsv).join(','));
                if (monthlyTrend && monthlyTrend.length > 0) {
                    monthlyTrend.forEach(item => {
                        csvLines.push([
                            item.period,
                            item.total_dispensed
                        ].map(escapeCsv).join(','));
                    });
                } else {
                    csvLines.push(['No longitudinal trend records found', '0'].map(escapeCsv).join(','));
                }

                const csvContent = '\uFEFF' + csvLines.join('\r\n');
                const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
                const url = URL.createObjectURL(blob);
                const a = document.createElement('a');
                const dateStr = new Date().toISOString().slice(0, 10);
                a.href = url;
                a.download = `pharmacy-consumption-analytics-${dateStr}.csv`;
                document.body.appendChild(a);
                a.click();
                document.body.removeChild(a);
                URL.revokeObjectURL(url);

                window.dispatchEvent(new CustomEvent('add-toast', { 
                    detail: { type: 'success', message: 'Consumption analytics exported to CSV successfully!' } 
                }));
            } catch (err) {
                console.error('Analytics CSV export failed:', err);
                window.dispatchEvent(new CustomEvent('add-toast', { 
                    detail: { type: 'error', message: 'Failed to generate analytics CSV export.' } 
                }));
            } finally {
                if (btn) btn.disabled = false;
                if (icon) icon.classList.remove('hidden');
                if (spinner) spinner.classList.add('hidden');
                if (text) text.textContent = 'Export CSV';
            }
        }, 250);
    }
    window.exportAnalyticsCsv = exportAnalyticsCsv;

    const CANCEL_REASONS = [
        { value: 'Stock unavailable at RHU', label: 'Stock unavailable at RHU' },
        { value: 'Patient no-show', label: 'Patient no-show' },
        { value: 'Clinician decision', label: 'Clinician decision' },
        { value: 'Duplicate order', label: 'Duplicate order' },
        { value: '__other__', label: 'Other (specify below)' },
    ];

    document.addEventListener('alpine:init', () => {
        Alpine.data('dispensingQueue', () => ({
            openId: null,
            submitting: false,
            issueItemIds: [],

            init() {
                window.addEventListener('confirmed-action', () => {
                    if (this.openId !== null) this.closeModal();
                    this.refreshQueue();
                });
            },

            openModal(id) {
                // Rows added by the 15s poll have no server-rendered modal yet.
                if (!document.getElementById(`dispenseForm-${id}`)) {
                    window.location.reload();
                    return;
                }

                this.issueItemIds = [];
                this.openId = id;
                this.$nextTick(() => {
                    const input = document.querySelector(`#dispenseForm-${id} input[type="number"]`);
                    if (input) input.focus();
                });
            },

            closeModal() {
                if (this.submitting) return;
                this.openId = null;
            },

            hasIssue(itemId) {
                return this.issueItemIds.some((id) => String(id) === String(itemId));
            },

            clearIssue(itemId) {
                this.issueItemIds = this.issueItemIds.filter((id) => String(id) !== String(itemId));
            },

            /** Cancel through the app-wide confirmation modal (Step 4 of the layout). */
            requestCancel(id) {
                const row = PHARMACY_QUEUE[id];
                if (!row) {
                    // Unknown row (added by the poll since load) — reload so it renders.
                    window.location.reload();
                    return;
                }

                const fields = [{
                    name: 'cancellation_reason',
                    label: 'Reason for cancellation',
                    type: 'select',
                    required: true,
                    options: CANCEL_REASONS,
                }, {
                    name: 'cancellation_note',
                    label: 'Additional note (optional)',
                    type: 'textarea',
                    rows: 2,
                    placeholder: 'Add context for the audit trail…',
                    help: 'Only needed when the reason above is not self-explanatory.',
                }];

                if (row.releasedUnits > 0) {
                    fields.push({
                        name: 'acknowledge_dispensed_stock',
                        label: 'Acknowledge released stock',
                        type: 'checkbox',
                        value: '1',
                        help: `${row.releasedUnits} of ${row.totalUnits} unit(s) were already dispensed and will NOT be returned to stock.`,
                    });
                }

                window.dispatchEvent(new CustomEvent('open-confirmation', {
                    detail: {
                        title: `Cancel Prescription #${id}`,
                        message: `Patient: ${row.patient}. This removes the order from the dispensing queue.`,
                        action: row.action,
                        method: 'POST',
                        confirmText: 'Yes, Cancel Prescription',
                        type: 'danger',
                        ajax: true,
                        fields,
                    },
                }));
            },

            /** Serialise the live form element so the payload always matches what is on screen. */
            async submitDispense(event, id) {
                if (this.submitting) return;

                const form = event?.target || document.getElementById(`dispenseForm-${id}`);
                if (!form) return;

                this.submitting = true;
                this.issueItemIds = [];

                try {
                    const csrfMeta = document.querySelector('meta[name="csrf-token"]');
                    const response = await fetch(form.action, {
                        method: 'POST',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrfMeta ? csrfMeta.content : '',
                        },
                        body: new FormData(form),
                    });

                    const data = await response.json().catch(() => null);

                    if (response.ok && data && (data.success === true || data.ok === true)) {
                        this.openId = null;
                        this.toast(data.message || 'Prescription dispensed successfully.', 'success');
                        await this.refreshQueue();
                    } else {
                        const message = (data && data.message)
                            || 'Could not dispense. Nothing was saved — please try again.';
                        this.issueItemIds = (data && data.item_ids) || [];
                        this.toast(message, 'error');
                    }
                } catch (e) {
                    this.toast('Could not reach the server. Check your connection and try again.', 'error');
                } finally {
                    this.submitting = false;
                }
            },

            /**
             * One fetch, two swaps: the queue table and both pending-order counters.
             * Mirrors the 15s poll in layouts/pharmacy.blade.php, which only heals the table.
             */
            async refreshQueue() {
                try {
                    const url = new URL(window.location.href);
                    url.searchParams.append('polling', '1');

                    const response = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
                    if (!response.ok) return;

                    const doc = new DOMParser().parseFromString(await response.text(), 'text/html');

                    const newTable = doc.getElementById('queue-table');
                    const currentTable = document.getElementById('queue-table');
                    if (newTable && currentTable) {
                        currentTable.innerHTML = newTable.innerHTML;
                    }

                    const count = doc.getElementById('pending-stat-count')?.textContent?.trim();
                    if (count !== undefined) {
                        const stat = document.getElementById('pending-stat-count');
                        if (stat) stat.textContent = count;
                    }

                    const badgeText = doc.getElementById('queue-count-badge')?.textContent;
                    if (badgeText !== undefined && badgeText !== null) {
                        const badge = document.getElementById('queue-count-badge');
                        if (badge) badge.textContent = badgeText.trim();
                    }
                } catch (e) {
                    // The 15s layout poll remains the safety net.
                }
            },

            toast(message, type = 'info') {
                if (window.toast && typeof window.toast[type] === 'function') {
                    window.toast[type](message);
                } else {
                    window.dispatchEvent(new CustomEvent('add-toast', { detail: { type, message } }));
                }
            },
        }));
    });

    document.addEventListener('DOMContentLoaded', function() {
        const isDarkMode = document.documentElement.classList.contains('dark');
        const textColor = isDarkMode ? '#cbd5e1' : '#475569';
        const gridColor = isDarkMode ? '#334155' : '#e2e8f0';

        Chart.defaults.color = textColor;
        Chart.defaults.font.family = 'Inter, sans-serif';

        // Top Dispensed Chart
        const topDispensedCtx = document.getElementById('topDispensedChart');
        if (topDispensedCtx) {
            const topDispensedData = @json($topDispensed);
            const labels = topDispensedData.map(item => item.medicine ? (item.medicine.name || item.medicine.generic_name) : 'Unknown');
            const data = topDispensedData.map(item => item.total_dispensed);

            new Chart(topDispensedCtx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Quantity Dispensed',
                        data: data,
                        backgroundColor: 'rgba(16, 185, 129, 0.85)',
                        borderRadius: 6,
                        barThickness: 'flex',
                        maxBarThickness: 36
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: isDarkMode ? '#0f172a' : '#ffffff',
                            titleColor: isDarkMode ? '#f8fafc' : '#0f172a',
                            bodyColor: isDarkMode ? '#cbd5e1' : '#475569',
                            borderColor: isDarkMode ? '#334155' : '#e2e8f0',
                            borderWidth: 1,
                            padding: 12,
                            boxPadding: 4
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: { color: gridColor, drawBorder: false },
                            ticks: { precision: 0 }
                        },
                        x: {
                            grid: { display: false, drawBorder: false },
                            ticks: {
                                maxRotation: 45,
                                minRotation: 45
                            }
                        }
                    }
                }
            });
        }

        // Monthly Trend Chart
        const monthlyTrendCtx = document.getElementById('monthlyTrendChart');
        if (monthlyTrendCtx) {
            const monthlyTrendData = @json($monthlyTrend);
            const labels = monthlyTrendData.map(item => item.label);
            const data = monthlyTrendData.map(item => item.total_dispensed);

            new Chart(monthlyTrendCtx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Total Dispensed',
                        data: data,
                        borderColor: '#10b981',
                        backgroundColor: 'rgba(16, 185, 129, 0.1)',
                        borderWidth: 3,
                        pointBackgroundColor: '#ffffff',
                        pointBorderColor: '#10b981',
                        pointBorderWidth: 2,
                        pointRadius: 4,
                        pointHoverRadius: 6,
                        fill: true,
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: isDarkMode ? '#0f172a' : '#ffffff',
                            titleColor: isDarkMode ? '#f8fafc' : '#0f172a',
                            bodyColor: isDarkMode ? '#cbd5e1' : '#475569',
                            borderColor: isDarkMode ? '#334155' : '#e2e8f0',
                            borderWidth: 1,
                            padding: 12,
                            mode: 'index',
                            intersect: false,
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: { color: gridColor, drawBorder: false },
                            ticks: { precision: 0 }
                        },
                        x: {
                            grid: { display: false, drawBorder: false }
                        }
                    },
                    interaction: {
                        mode: 'nearest',
                        axis: 'x',
                        intersect: false
                    }
                }
            });
        }
    });
</script>
@endpush
@endsection
