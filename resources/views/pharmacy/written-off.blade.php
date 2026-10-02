@extends('layouts.pharmacy')

@section('header', 'Written-Off Inventory')

@push('styles')
<style>
    /* Custom Modern Flatpickr Theme - Rural Health Unit Pharmacy */
    .flatpickr-calendar {
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif !important;
        background: #ffffff !important;
        border: 1px solid #e2e8f0 !important;
        border-radius: 1.25rem !important;
        box-shadow: 0 20px 25px -5px rgba(15, 23, 42, 0.15), 0 8px 10px -6px rgba(15, 23, 42, 0.1) !important;
        padding: 0.75rem !important;
        width: 312px !important;
        overflow: hidden !important;
        z-index: 9999 !important;
    }

    .dark .flatpickr-calendar {
        background: #0f172a !important;
        border: 1px solid #1e293b !important;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.6) !important;
    }

    .flatpickr-calendar::before,
    .flatpickr-calendar::after {
        display: none !important;
    }

    .flatpickr-months {
        display: flex !important;
        align-items: center !important;
        justify-content: space-between !important;
        padding-bottom: 0.5rem !important;
        border-bottom: 1px solid #f1f5f9 !important;
        margin-bottom: 0.5rem !important;
    }
    .dark .flatpickr-months {
        border-bottom-color: #1e293b !important;
    }

    .flatpickr-months .flatpickr-month {
        height: 38px !important;
        color: #0f172a !important;
        fill: #0f172a !important;
    }
    .dark .flatpickr-months .flatpickr-month {
        color: #f8fafc !important;
        fill: #f8fafc !important;
    }

    .flatpickr-current-month {
        font-size: 0.95rem !important;
        font-weight: 700 !important;
        padding-top: 4px !important;
    }

    .flatpickr-current-month .flatpickr-monthDropdown-months {
        font-weight: 700 !important;
        color: inherit !important;
        background: transparent !important;
        border-radius: 0.5rem !important;
        padding: 2px 6px !important;
        cursor: pointer !important;
    }
    .flatpickr-current-month .flatpickr-monthDropdown-months:hover {
        background: #f1f5f9 !important;
    }
    .dark .flatpickr-current-month .flatpickr-monthDropdown-months:hover {
        background: #1e293b !important;
    }

    .flatpickr-current-month input.cur-year {
        font-weight: 700 !important;
        color: inherit !important;
    }

    .flatpickr-months .flatpickr-prev-month,
    .flatpickr-months .flatpickr-next-month {
        position: static !important;
        height: 32px !important;
        width: 32px !important;
        padding: 6px !important;
        border-radius: 0.625rem !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        transition: all 0.15s ease !important;
        color: #64748b !important;
        fill: #64748b !important;
        cursor: pointer !important;
    }

    .flatpickr-months .flatpickr-prev-month:hover,
    .flatpickr-months .flatpickr-next-month:hover {
        background: #f1f5f9 !important;
        color: #0f172a !important;
        fill: #0f172a !important;
    }
    .dark .flatpickr-months .flatpickr-prev-month:hover,
    .dark .flatpickr-months .flatpickr-next-month:hover {
        background: #1e293b !important;
        color: #f8fafc !important;
        fill: #f8fafc !important;
    }

    span.flatpickr-weekday {
        font-size: 0.7rem !important;
        font-weight: 800 !important;
        text-transform: uppercase !important;
        letter-spacing: 0.05em !important;
        color: #94a3b8 !important;
    }
    .dark span.flatpickr-weekday {
        color: #64748b !important;
    }

    .flatpickr-days {
        width: 100% !important;
    }
    .dayContainer {
        width: 100% !important;
        min-width: 100% !important;
        max-width: 100% !important;
        justify-content: space-around !important;
    }

    .flatpickr-day {
        height: 36px !important;
        line-height: 36px !important;
        max-width: 36px !important;
        border-radius: 0.75rem !important;
        font-size: 0.8125rem !important;
        font-weight: 600 !important;
        color: #334155 !important;
        border: 1px solid transparent !important;
        transition: all 0.12s ease !important;
        cursor: pointer !important;
    }
    .dark .flatpickr-day {
        color: #cbd5e1 !important;
    }

    .flatpickr-day:hover {
        background: #ecfdf5 !important;
        color: #059669 !important;
        border-color: rgba(5, 150, 105, 0.2) !important;
    }
    .dark .flatpickr-day:hover {
        background: rgba(6, 78, 59, 0.4) !important;
        color: #6ee7b7 !important;
        border-color: rgba(5, 150, 105, 0.3) !important;
    }

    .flatpickr-day.today {
        border-color: #059669 !important;
        color: #059669 !important;
        font-weight: 800 !important;
    }
    .dark .flatpickr-day.today {
        border-color: #10b981 !important;
        color: #34d399 !important;
    }

    .flatpickr-day.selected,
    .flatpickr-day.startRange,
    .flatpickr-day.endRange {
        background: #059669 !important;
        border-color: #059669 !important;
        color: #ffffff !important;
        font-weight: 800 !important;
        box-shadow: 0 4px 10px rgba(5, 150, 105, 0.35) !important;
    }
    .dark .flatpickr-day.selected {
        background: #10b981 !important;
        border-color: #10b981 !important;
        color: #022c22 !important;
    }

    .flatpickr-day.prevMonthDay,
    .flatpickr-day.nextMonthDay {
        color: #cbd5e1 !important;
    }
    .dark .flatpickr-day.prevMonthDay,
    .dark .flatpickr-day.nextMonthDay {
        color: #475569 !important;
    }

    .flatpickr-day.flatpickr-disabled,
    .flatpickr-day.flatpickr-disabled:hover {
        color: #e2e8f0 !important;
        background: transparent !important;
        cursor: not-allowed !important;
    }
    .dark .flatpickr-day.flatpickr-disabled {
        color: #334155 !important;
    }
</style>
@endpush

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
            <span class="text-rose-700 dark:text-rose-400 font-extrabold">Written-Off Inventory</span>
        </div>
        <div class="hidden sm:flex items-center gap-2 text-[11px] font-semibold text-slate-400 dark:text-slate-500">
            <span class="w-2 h-2 rounded-full bg-rose-500 animate-pulse"></span>
            <span>Permanent Disposal Vault</span>
        </div>
    </nav>

    <!-- Header Section -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pb-2 border-b border-slate-200/80 dark:border-slate-800 print:hidden">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-rose-100 text-rose-800 dark:bg-rose-950/60 dark:text-rose-300 border border-rose-200 dark:border-rose-800/60">
                    <svg class="w-3 h-3 text-rose-600 dark:text-rose-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                    Stock Disposal Audit
                </span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-black text-slate-900 dark:text-white tracking-tight">
                Written-Off &amp; Disposed Inventory
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-1 max-w-2xl">
                Archival audit trail of expired, damaged, recalled, and written-off batches purged from clinic active storage.
            </p>
        </div>

        <div class="flex items-center gap-2.5 shrink-0 flex-wrap sm:flex-nowrap">
            <button type="button" onclick="exportWrittenOffCsv(this)" id="exportWrittenOffBtn"
                class="h-10 px-4 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 hover:bg-slate-50 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-200 text-xs font-bold transition-all shadow-xs flex items-center gap-2 cursor-pointer active:scale-95">
                <svg id="exportWrittenOffIcon" class="w-4 h-4 text-rose-600 dark:text-rose-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <svg id="exportWrittenOffSpinner" class="hidden animate-spin h-4 w-4 text-rose-600 dark:text-rose-400 shrink-0" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span id="exportWrittenOffText">Export Audit (CSV)</span>
            </button>

            <a href="{{ route('pharmacy.dashboard') }}" 
                class="h-10 px-4 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 hover:bg-slate-50 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-200 text-xs font-bold transition-all shadow-xs flex items-center gap-2 cursor-pointer active:scale-95">
                <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                <span>Dashboard</span>
            </a>
            <a href="{{ route('pharmacy.medicines') }}" 
                class="h-10 px-4 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white text-xs font-bold shadow-md shadow-emerald-600/20 transition-all flex items-center gap-2 active:scale-95 cursor-pointer shrink-0">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                </svg>
                <span>Active Formulary</span>
            </a>
        </div>
    </div>

    <!-- Summary Statistics Deck -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 print:hidden">
        <!-- 1. Affected Medicines -->
        <div class="bg-white dark:bg-slate-900 rounded-3xl p-5 sm:p-6 border border-slate-200/90 dark:border-slate-800 shadow-xs flex flex-col justify-between">
            <div class="flex items-center justify-between mb-3">
                <div class="w-11 h-11 rounded-2xl bg-sky-100/80 dark:bg-sky-950/60 text-sky-600 dark:text-sky-400 flex items-center justify-center border border-sky-500/20 shadow-xs shrink-0">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" /></svg>
                </div>
                <span class="text-[10px] font-black uppercase tracking-wider text-sky-700 dark:text-sky-300 bg-sky-100 dark:bg-sky-950/60 px-2.5 py-0.5 rounded-full border border-sky-200 dark:border-sky-800/60">
                    Formulary Scope
                </span>
            </div>
            <div>
                <p class="text-3xl sm:text-4xl font-black text-slate-900 dark:text-white tracking-tight">{{ number_format($affectedMedicinesCount) }}</p>
                <p class="text-xs text-slate-500 dark:text-slate-400 font-medium mt-1">Unique Affected Items</p>
            </div>
            <div class="mt-3 pt-2.5 border-t border-slate-100 dark:border-slate-800/80 text-[11px] text-slate-400 dark:text-slate-500 font-medium">
                Medicines with written-off lots
            </div>
        </div>

        <!-- 2. Total Disposed Batches -->
        <div class="bg-white dark:bg-slate-900 rounded-3xl p-5 sm:p-6 border border-slate-200/90 dark:border-slate-800 shadow-xs flex flex-col justify-between">
            <div class="flex items-center justify-between mb-3">
                <div class="w-11 h-11 rounded-2xl bg-rose-100/80 dark:bg-rose-950/60 text-rose-600 dark:text-rose-400 flex items-center justify-center border border-rose-500/20 shadow-xs shrink-0">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                </div>
                <span class="text-[10px] font-black uppercase tracking-wider text-rose-700 dark:text-rose-300 bg-rose-100 dark:bg-rose-950/60 px-2.5 py-0.5 rounded-full border border-rose-200 dark:border-rose-800/60">
                    Purged Batches
                </span>
            </div>
            <div>
                <p class="text-3xl sm:text-4xl font-black text-rose-600 dark:text-rose-400 tracking-tight">{{ number_format($totalDisposedBatches) }}</p>
                <p class="text-xs text-slate-500 dark:text-slate-400 font-medium mt-1">Total Disposed Lots</p>
            </div>
            <div class="mt-3 pt-2.5 border-t border-slate-100 dark:border-slate-800/80 text-[11px] text-slate-400 dark:text-slate-500 font-medium">
                Decommissioned from storage
            </div>
        </div>

        <!-- 3. Total Units Destroyed -->
        <div class="bg-white dark:bg-slate-900 rounded-3xl p-5 sm:p-6 border border-slate-200/90 dark:border-slate-800 shadow-xs flex flex-col justify-between">
            <div class="flex items-center justify-between mb-3">
                <div class="w-11 h-11 rounded-2xl bg-amber-100/80 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400 flex items-center justify-center border border-amber-500/20 shadow-xs shrink-0">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" /></svg>
                </div>
                <span class="text-[10px] font-black uppercase tracking-wider text-amber-800 dark:text-amber-300 bg-amber-100 dark:bg-amber-950/60 px-2.5 py-0.5 rounded-full border border-amber-200 dark:border-amber-800/60">
                    Physical Volume
                </span>
            </div>
            <div>
                <p class="text-3xl sm:text-4xl font-black text-slate-900 dark:text-white tracking-tight">{{ number_format($totalUnitsDestroyed) }}</p>
                <p class="text-xs text-slate-500 dark:text-slate-400 font-medium mt-1">Total Units Destroyed</p>
            </div>
            <div class="mt-3 pt-2.5 border-t border-slate-100 dark:border-slate-800/80 text-[11px] text-slate-400 dark:text-slate-500 font-medium">
                Logged across all disposals
            </div>
        </div>

        <!-- 4. Reason Breakdown Progress -->
        <div class="bg-white dark:bg-slate-900 rounded-3xl p-5 sm:p-6 border border-slate-200/90 dark:border-slate-800 shadow-xs flex flex-col justify-between">
            <div class="flex items-center justify-between mb-2">
                <p class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 dark:text-slate-500">Breakdown by Reason</p>
                <span class="text-[10px] font-bold text-slate-400">{{ count($reasonBreakdown) }} Category(ies)</span>
            </div>
            @if(count($reasonBreakdown) > 0)
                <div class="space-y-2 my-auto">
                    @foreach($reasonBreakdown as $reason => $count)
                        @php
                            $pct = $totalDisposedBatches > 0 ? round(($count / $totalDisposedBatches) * 100) : 0;
                            $barColor = match($reason) {
                                'Expired' => 'bg-rose-500',
                                'Damaged / Broken' => 'bg-amber-500',
                                'Contaminated' => 'bg-purple-500',
                                'Supplier Recall' => 'bg-violet-500',
                                default => 'bg-sky-500',
                            };
                        @endphp
                        <div>
                            <div class="flex items-center justify-between text-[11px] font-bold mb-1">
                                <span class="text-slate-700 dark:text-slate-300 truncate max-w-[120px]">{{ $reason ?? 'Unknown' }}</span>
                                <span class="text-slate-500 dark:text-slate-400 font-mono">{{ $count }} ({{ $pct }}%)</span>
                            </div>
                            <div class="h-1.5 rounded-full bg-slate-100 dark:bg-slate-800 overflow-hidden">
                                <div class="{{ $barColor }} h-full rounded-full transition-all duration-500" style="width: {{ $pct }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="py-4 text-center">
                    <p class="text-xs text-slate-400 dark:text-slate-500">No batch disposal history yet.</p>
                </div>
            @endif
            <div class="mt-2 pt-2 border-t border-slate-100 dark:border-slate-800/80 text-[10px] text-slate-400 dark:text-slate-500">
                Verified clinical waste records
            </div>
        </div>
    </div>

    <!-- Search & Filter Toolbar -->
    <div class="bg-white/95 dark:bg-slate-900/95 backdrop-blur-md rounded-3xl border border-slate-200/90 dark:border-slate-800 p-5 sm:p-6 shadow-xs print:hidden">
        <form id="filterForm" action="{{ route('pharmacy.written-off') }}" method="GET" class="space-y-4">
            <div class="flex flex-col sm:flex-row gap-3.5 items-stretch sm:items-center">
                <!-- Search -->
                <div class="relative flex-1">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400 dark:text-slate-500">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                    </div>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by medicine name, generic name, or batch lot number..."
                        oninput="clearTimeout(this.timer); this.timer = setTimeout(() => { this.form.submit(); }, 400);"
                        onkeydown="if(event.key === 'Enter') { clearTimeout(this.timer); this.form.submit(); } else if(event.key === 'Escape') { this.value = ''; this.form.submit(); }"
                        class="no-uppercase h-11 sm:h-12 pl-10 pr-10 block w-full rounded-xl border border-slate-300/80 dark:border-slate-700 bg-slate-50/70 dark:bg-slate-800/60 text-slate-900 dark:text-white text-sm font-semibold focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 shadow-xs transition-all placeholder:text-slate-400 placeholder:font-normal"
                        style="text-transform: none !important;">
                    @if(request('search'))
                        <button type="button" onclick="const input = this.previousElementSibling; input.value = ''; input.form.submit();" class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition-colors cursor-pointer" title="Clear Search">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    @endif
                </div>

                <!-- Styled Disposal Reason Custom Dropdown -->
                <div class="sm:w-64 relative" 
                     x-data="{
                         open: false,
                         selected: @js(request('reason', 'all')),
                         reasons: [
                             { value: 'all', label: 'All Disposal Reasons', color: 'slate', count: {{ $totalDisposedBatches }} },
                             @foreach($distinctReasons as $reason)
                                 { 
                                     value: @js($reason), 
                                     label: @js($reason), 
                                     color: @js(match($reason) {
                                         'Expired' => 'rose',
                                         'Damaged / Broken' => 'amber',
                                         'Contaminated' => 'purple',
                                         'Supplier Recall' => 'violet',
                                         default => 'sky',
                                     }),
                                     count: {{ $reasonBreakdown[$reason] ?? 0 }}
                                 },
                             @endforeach
                         ],
                         get current() {
                             return this.reasons.find(r => r.value === this.selected) || this.reasons[0];
                         },
                         select(val) {
                             this.selected = val;
                             this.open = false;
                             $nextTick(() => {
                                 document.getElementById('wo_hidden_reason_input').value = val;
                                 document.getElementById('filterForm').submit();
                             });
                         }
                     }"
                     @click.outside="open = false"
                     @keydown.escape.window="open = false">

                    <!-- Hidden Input for Form Submission -->
                    <input type="hidden" name="reason" id="wo_hidden_reason_input" :value="selected">

                    <!-- Trigger Button -->
                    <button type="button" 
                            @click="open = !open" 
                            :aria-expanded="open"
                            class="h-11 sm:h-12 px-3.5 w-full rounded-xl border bg-slate-50/70 dark:bg-slate-800/60 text-slate-900 dark:text-white text-sm font-semibold flex items-center justify-between gap-2 shadow-xs transition-all cursor-pointer focus:outline-none focus:ring-2 focus:ring-emerald-500/20"
                            :class="open ? 'border-emerald-500 ring-2 ring-emerald-500/20 bg-white dark:bg-slate-800' : 'border-slate-300/80 dark:border-slate-700 hover:border-slate-400 dark:hover:border-slate-600'">
                        
                        <div class="flex items-center gap-2.5 truncate min-w-0">
                            <!-- Dynamic Colored Dot / Badge Icon -->
                            <span class="w-2.5 h-2.5 rounded-full shrink-0 ring-2 ring-white dark:ring-slate-900 shadow-xs"
                                  :class="{
                                      'bg-slate-400 dark:bg-slate-500': current.color === 'slate',
                                      'bg-rose-500 ring-rose-200 dark:ring-rose-950': current.color === 'rose',
                                      'bg-amber-500 ring-amber-200 dark:ring-amber-950': current.color === 'amber',
                                      'bg-purple-500 ring-purple-200 dark:ring-purple-950': current.color === 'purple',
                                      'bg-violet-500 ring-violet-200 dark:ring-violet-950': current.color === 'violet',
                                      'bg-sky-500 ring-sky-200 dark:ring-sky-950': current.color === 'sky',
                                  }"></span>
                            <span class="truncate font-semibold text-slate-800 dark:text-slate-100 text-sm" x-text="current.label"></span>
                        </div>

                        <div class="flex items-center gap-1.5 shrink-0">
                            <span x-show="current.count !== undefined" 
                                  class="text-[11px] font-bold px-1.5 py-0.5 rounded-md bg-slate-200/70 dark:bg-slate-700/60 text-slate-600 dark:text-slate-300 font-mono"
                                  x-text="current.count"></span>
                            
                            <svg class="w-4 h-4 text-slate-400 transition-transform duration-200" 
                                 :class="open ? 'rotate-180 text-emerald-600 dark:text-emerald-400' : ''" 
                                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </div>
                    </button>

                    <!-- Custom Dropdown Popover Panel -->
                    <div x-show="open" 
                         x-transition:enter="transition ease-out duration-150"
                         x-transition:enter-start="opacity-0 translate-y-1 scale-95"
                         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                         x-transition:leave="transition ease-in duration-100"
                         x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                         x-transition:leave-end="opacity-0 translate-y-1 scale-95"
                         x-cloak
                         class="absolute z-50 mt-1.5 w-full min-w-[260px] rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/90 dark:border-slate-800 shadow-2xl shadow-slate-900/15 dark:shadow-black/50 p-1.5 backdrop-blur-xl">
                        
                        <div class="px-2.5 py-1.5 text-[10px] font-black uppercase tracking-wider text-slate-400 dark:text-slate-500 border-b border-slate-100 dark:border-slate-800/80 mb-1 flex items-center justify-between">
                            <span>Filter By Disposal Reason</span>
                            <span>Batches</span>
                        </div>

                        <ul class="space-y-0.5 max-h-64 overflow-y-auto custom-scrollbar">
                            <template x-for="r in reasons" :key="r.value">
                                <li @click="select(r.value)"
                                    class="group flex items-center justify-between px-3 py-2 text-xs sm:text-sm rounded-xl transition-all cursor-pointer select-none"
                                    :class="selected === r.value 
                                        ? 'bg-emerald-50 dark:bg-emerald-950/60 text-emerald-900 dark:text-emerald-200 font-bold border border-emerald-500/20' 
                                        : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800/70'">
                                    
                                    <div class="flex items-center gap-2.5 truncate min-w-0">
                                        <span class="w-2.5 h-2.5 rounded-full shrink-0 ring-2 ring-white dark:ring-slate-900 shadow-xs"
                                              :class="{
                                                  'bg-slate-400 dark:bg-slate-500': r.color === 'slate',
                                                  'bg-rose-500 ring-rose-200 dark:ring-rose-950': r.color === 'rose',
                                                  'bg-amber-500 ring-amber-200 dark:ring-amber-950': r.color === 'amber',
                                                  'bg-purple-500 ring-purple-200 dark:ring-purple-950': r.color === 'purple',
                                                  'bg-violet-500 ring-violet-200 dark:ring-violet-950': r.color === 'violet',
                                                  'bg-sky-500 ring-sky-200 dark:ring-sky-950': r.color === 'sky',
                                              }"></span>
                                        <span class="truncate" x-text="r.label"></span>
                                    </div>

                                    <div class="flex items-center gap-2 shrink-0 ml-2">
                                        <span class="text-[10px] font-bold px-1.5 py-0.5 rounded-md font-mono transition-colors"
                                              :class="selected === r.value 
                                                  ? 'bg-emerald-200/60 dark:bg-emerald-800/60 text-emerald-900 dark:text-emerald-100' 
                                                  : 'bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400 group-hover:bg-slate-200 dark:group-hover:bg-slate-700'"
                                              x-text="r.count"></span>

                                        <svg x-show="selected === r.value" 
                                             class="w-4 h-4 text-emerald-600 dark:text-emerald-400 shrink-0" 
                                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                        </svg>
                                    </div>
                                </li>
                            </template>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Date Range & Reset Row -->
            <div class="flex flex-col lg:flex-row gap-3.5 items-stretch lg:items-center justify-between pt-3 border-t border-slate-100 dark:border-slate-800/80">
                <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 flex-1">
                    <!-- Date From Input -->
                    <div class="flex items-center gap-2 flex-1">
                        <span class="text-[11px] font-extrabold text-slate-500 dark:text-slate-400 uppercase tracking-wider shrink-0 w-11 sm:w-auto">From:</span>
                        <div class="relative flex-1">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400 dark:text-slate-500 z-10">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            <input type="text" id="date_from" name="date_from" value="{{ request('date_from') }}" placeholder="Start Date (YYYY-MM-DD)" autocomplete="off"
                                class="no-uppercase h-11 pl-10 pr-9 block w-full rounded-xl border border-slate-300/80 dark:border-slate-700 bg-slate-50/70 dark:bg-slate-800/60 text-slate-900 dark:text-white text-sm font-semibold focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 shadow-xs transition-all cursor-pointer placeholder:text-slate-400 placeholder:font-normal"
                                style="text-transform: none !important;">
                            @if(request('date_from'))
                                <button type="button" onclick="clearDate('date_from')" class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition-colors cursor-pointer z-10" title="Clear Start Date">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                </button>
                            @endif
                        </div>
                    </div>

                    <!-- Date To Input -->
                    <div class="flex items-center gap-2 flex-1">
                        <span class="text-[11px] font-extrabold text-slate-500 dark:text-slate-400 uppercase tracking-wider shrink-0 w-11 sm:w-auto">To:</span>
                        <div class="relative flex-1">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400 dark:text-slate-500 z-10">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            <input type="text" id="date_to" name="date_to" value="{{ request('date_to') }}" placeholder="End Date (YYYY-MM-DD)" autocomplete="off"
                                class="no-uppercase h-11 pl-10 pr-9 block w-full rounded-xl border border-slate-300/80 dark:border-slate-700 bg-slate-50/70 dark:bg-slate-800/60 text-slate-900 dark:text-white text-sm font-semibold focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 shadow-xs transition-all cursor-pointer placeholder:text-slate-400 placeholder:font-normal"
                                style="text-transform: none !important;">
                            @if(request('date_to'))
                                <button type="button" onclick="clearDate('date_to')" class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition-colors cursor-pointer z-10" title="Clear End Date">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                </button>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Quick Presets & Actions -->
                <div class="flex items-center gap-2 flex-wrap shrink-0">
                    <!-- Quick Preset Buttons -->
                    <div class="flex items-center gap-1 bg-slate-100 dark:bg-slate-800/80 p-1 rounded-xl border border-slate-200/80 dark:border-slate-700/80 text-xs">
                        <button type="button" onclick="setQuickDateRange('today')" class="px-2.5 py-1 rounded-lg font-bold text-slate-600 dark:text-slate-300 hover:bg-white dark:hover:bg-slate-700 hover:text-emerald-600 dark:hover:text-emerald-400 transition-all cursor-pointer">
                            Today
                        </button>
                        <button type="button" onclick="setQuickDateRange('last7')" class="px-2.5 py-1 rounded-lg font-bold text-slate-600 dark:text-slate-300 hover:bg-white dark:hover:bg-slate-700 hover:text-emerald-600 dark:hover:text-emerald-400 transition-all cursor-pointer">
                            Last 7d
                        </button>
                        <button type="button" onclick="setQuickDateRange('thisMonth')" class="px-2.5 py-1 rounded-lg font-bold text-slate-600 dark:text-slate-300 hover:bg-white dark:hover:bg-slate-700 hover:text-emerald-600 dark:hover:text-emerald-400 transition-all cursor-pointer">
                            This Month
                        </button>
                        <button type="button" onclick="setQuickDateRange('last30')" class="px-2.5 py-1 rounded-lg font-bold text-slate-600 dark:text-slate-300 hover:bg-white dark:hover:bg-slate-700 hover:text-emerald-600 dark:hover:text-emerald-400 transition-all cursor-pointer">
                            30d
                        </button>
                    </div>

                    @if(request('search') || (request('reason') && request('reason') !== 'all') || request('date_from') || request('date_to'))
                        <a href="{{ route('pharmacy.written-off') }}" class="h-9 sm:h-10 px-3.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-300 text-xs font-bold transition-colors flex items-center justify-center gap-1.5 shadow-xs shrink-0 cursor-pointer" title="Reset all filters">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            <span>Reset</span>
                        </a>
                    @endif
                </div>
            </div>

            <input type="hidden" name="per_page" id="wo_per_page" value="{{ request('per_page', 15) }}">
        </form>
    </div>

    <!-- Written-Off Table (Grouped by Medicine) -->
    <div class="bg-white dark:bg-slate-900 rounded-3xl shadow-xs border border-slate-200/90 dark:border-slate-800 overflow-hidden">
        <div class="px-6 py-5 border-b border-slate-100 dark:border-slate-800/80 bg-slate-50/70 dark:bg-slate-900/60 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div>
                <h3 class="text-base sm:text-lg font-bold text-slate-900 dark:text-white">Written-Off Formulary Inventory</h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">{{ $medicines->total() }} medicine(s) with decommissioned lots</p>
            </div>
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-extrabold bg-rose-50 dark:bg-rose-950/50 text-rose-700 dark:text-rose-300 border border-rose-200 dark:border-rose-800/60 shadow-xs">
                <span>Page {{ $medicines->currentPage() }} of {{ max(1, $medicines->lastPage()) }}</span>
            </span>
        </div>

        <div class="overflow-x-auto custom-scrollbar">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/90 dark:bg-slate-950/80 text-slate-500 dark:text-slate-400 text-[11px] font-extrabold uppercase tracking-wider border-b border-slate-200/80 dark:border-slate-800">
                        <th class="py-4 px-5 sm:px-6 w-[28%]">Formulary Item</th>
                        <th class="py-4 px-5 sm:px-6 text-center w-[14%]">Batches Disposed</th>
                        <th class="py-4 px-5 sm:px-6 text-center w-[14%]">Units Destroyed</th>
                        <th class="py-4 px-5 sm:px-6 w-[20%]">Reasons Recorded</th>
                        <th class="py-4 px-5 sm:px-6 w-[12%]">Latest Disposal</th>
                        <th class="py-4 px-5 sm:px-6 text-right print:hidden min-w-[140px]">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800 text-sm">
                    @forelse($medicines as $medicine)
                        @php
                            $disposedBatches = $medicine->batches;
                            $batchCount = $disposedBatches->count();
                            $totalUnits = $disposedBatches->sum('original_quantity');
                            $latestDisposal = $disposedBatches->sortByDesc('disposed_at')->first();
                            $reasons = $disposedBatches->pluck('disposal_reason')->filter()->unique()->values();
                        @endphp
                        <tr class="hover:bg-rose-50/30 dark:hover:bg-slate-800/40 transition-colors group">
                            <!-- Medicine Info -->
                            <td class="py-5 px-5 sm:px-6 align-top">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-2xl bg-rose-100/80 dark:bg-rose-950/60 text-rose-600 dark:text-rose-400 font-black flex items-center justify-center border border-rose-500/20 shadow-xs shrink-0 text-xs">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" /></svg>
                                    </div>
                                    <div class="min-w-0">
                                        <p class="font-extrabold text-slate-900 dark:text-white text-sm sm:text-base tracking-tight group-hover:text-rose-600 dark:group-hover:text-rose-400 transition-colors truncate">{{ $medicine->name }}</p>
                                        @if($medicine->generic_name)
                                            <p class="text-xs text-slate-500 dark:text-slate-400 font-medium mt-0.5 truncate">
                                                Generic: <span class="font-semibold text-slate-700 dark:text-slate-300">{{ $medicine->generic_name }}</span>
                                            </p>
                                        @endif
                                        <div class="flex items-center gap-1.5 mt-1 flex-wrap">
                                            @if($medicine->form)
                                                <span class="text-[10px] px-2 py-0.5 rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 font-bold border border-slate-200 dark:border-slate-700">{{ $medicine->form }}</span>
                                            @endif
                                            @if($medicine->category)
                                                <span class="text-[10px] px-2 py-0.5 rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 font-bold border border-slate-200 dark:border-slate-700">{{ $medicine->category }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <!-- Batch Count -->
                            <td class="py-5 px-5 sm:px-6 align-top text-center">
                                <span class="inline-flex items-center justify-center h-8 px-3 rounded-xl bg-rose-100/80 dark:bg-rose-950/60 text-rose-700 dark:text-rose-300 text-xs font-black border border-rose-200/80 dark:border-rose-800/60 shadow-xs">
                                    {{ $batchCount }} lot{{ $batchCount > 1 ? 's' : '' }}
                                </span>
                            </td>

                            <!-- Total Units -->
                            <td class="py-5 px-5 sm:px-6 align-top text-center">
                                <p class="text-base font-black text-rose-600 dark:text-rose-400 tracking-tight">{{ number_format($totalUnits) }}</p>
                                <p class="text-[10px] text-slate-400 dark:text-slate-500 font-bold uppercase tracking-wider mt-0.5">
                                    {{ $medicine->unit ? Str::plural($medicine->unit, $totalUnits) : 'Units' }}
                                </p>
                            </td>

                            <!-- Reasons -->
                            <td class="py-5 px-5 sm:px-6 align-top">
                                <div class="flex flex-wrap gap-1.5">
                                    @foreach($reasons as $reason)
                                        @php
                                            $reasonBadge = match($reason) {
                                                'Expired' => 'bg-rose-100 text-rose-800 dark:bg-rose-950/60 dark:text-rose-300 border-rose-200 dark:border-rose-800/60',
                                                'Damaged / Broken' => 'bg-amber-100 text-amber-800 dark:bg-amber-950/60 dark:text-amber-300 border-amber-200 dark:border-amber-800/60',
                                                'Contaminated' => 'bg-purple-100 text-purple-800 dark:bg-purple-950/60 dark:text-purple-300 border-purple-200 dark:border-purple-800/60',
                                                'Supplier Recall' => 'bg-violet-100 text-violet-800 dark:bg-violet-950/60 dark:text-violet-300 border-violet-200 dark:border-violet-800/60',
                                                default => 'bg-slate-100 text-slate-800 dark:bg-slate-800 dark:text-slate-300 border-slate-200 dark:border-slate-700',
                                            };
                                        @endphp
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider {{ $reasonBadge }} border shadow-xs">
                                            {{ $reason }}
                                        </span>
                                    @endforeach
                                </div>
                            </td>

                            <!-- Latest Disposal -->
                            <td class="py-5 px-5 sm:px-6 align-top">
                                @if($latestDisposal)
                                    <p class="text-xs font-bold text-slate-800 dark:text-slate-200">{{ $latestDisposal->disposed_at->format('M d, Y') }}</p>
                                    <p class="text-[10px] text-slate-500 dark:text-slate-400 mt-0.5">{{ $latestDisposal->disposed_at->format('h:i A') }}</p>
                                    @if($latestDisposal->disposer)
                                        <p class="text-[10px] text-slate-400 dark:text-slate-500 mt-0.5 truncate max-w-[120px]">by {{ $latestDisposal->disposer->name }}</p>
                                    @endif
                                @else
                                    <span class="text-xs text-slate-400">—</span>
                                @endif
                            </td>

                            <!-- Details Button -->
                            <td class="py-5 px-5 sm:px-6 align-top text-right print:hidden whitespace-nowrap min-w-[140px]">
                                <button type="button" @click="$dispatch('open-batch-vault-{{ $medicine->id }}')" 
                                    class="h-9 px-3.5 rounded-xl inline-flex items-center gap-1.5 text-xs font-bold text-slate-700 dark:text-slate-300 bg-slate-100 dark:bg-slate-800 hover:bg-rose-50 dark:hover:bg-rose-950/60 hover:text-rose-700 dark:hover:text-rose-300 border border-slate-200 dark:border-slate-700 hover:border-rose-300 dark:hover:border-rose-700 shadow-xs transition-all cursor-pointer active:scale-95 shrink-0">
                                    <span>Inspect Batches</span>
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-16 px-6 text-center text-slate-500 dark:text-slate-400">
                                <div class="w-16 h-16 rounded-3xl bg-slate-100 dark:bg-slate-800 text-slate-400 dark:text-slate-500 mx-auto flex items-center justify-center mb-4 shadow-xs border border-slate-200/80 dark:border-slate-700">
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                </div>
                                <h3 class="text-base font-extrabold text-slate-900 dark:text-white mb-1">No Written-Off Records</h3>
                                <p class="text-xs text-slate-500 dark:text-slate-400 max-w-sm mx-auto leading-relaxed">
                                    No disposed inventory records match your selected search or date range filters.
                                </p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($medicines->hasPages() || $medicines->total() > 15)
            <div class="px-6 py-4 border-t border-slate-100 dark:border-slate-800/80 bg-slate-50/70 dark:bg-slate-900/60 flex flex-col sm:flex-row items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <label class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Per Page</label>
                    <x-select 
                        :options="[
                            '15' => '15 per page',
                            '30' => '30 per page',
                            '50' => '50 per page',
                            '100' => '100 per page'
                        ]" 
                        :value="request('per_page', 15)"
                        size="sm"
                        containerClass="w-36"
                        @change="document.getElementById('wo_per_page').value = $event.detail; document.getElementById('filterForm').submit()"
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

<!-- Batch Vault Modals (one per medicine) -->
@foreach($medicines as $medicine)
    @php
        $disposedBatches = $medicine->batches->sortByDesc('disposed_at');
    @endphp
    <div x-data="{ open: false }" 
         @open-batch-vault-{{ $medicine->id }}.window="open = true">
        <template x-teleport="body">
            <div x-show="open" 
                 x-cloak
                 @keydown.escape.window="open = false"
                 class="fixed inset-0 z-[999] overflow-y-auto" 
                 aria-labelledby="modal-title-{{ $medicine->id }}" role="dialog" aria-modal="true">
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
                <div class="px-6 py-5 border-b border-slate-200 dark:border-slate-800 bg-rose-50/60 dark:bg-rose-950/30">
                    <div class="flex justify-between items-start mb-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-2xl bg-rose-100 dark:bg-rose-950/80 text-rose-600 dark:text-rose-400 flex items-center justify-center border border-rose-500/20 shadow-xs shrink-0">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                            </div>
                            <div>
                                <h3 class="text-base font-bold text-slate-900 dark:text-white" id="modal-title-{{ $medicine->id }}">Disposed Batch Vault</h3>
                                <p class="text-xs text-slate-500 dark:text-slate-400">{{ $medicine->name }}</p>
                            </div>
                        </div>
                        <button type="button" @click="open = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 p-1.5 rounded-xl hover:bg-slate-200 dark:hover:bg-slate-700 transition-colors cursor-pointer">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>

                    <!-- Medicine Summary Strip -->
                    <div class="grid grid-cols-3 gap-3 pt-3.5 border-t border-slate-200/90 dark:border-slate-700/80">
                        <div>
                            <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block">Medicine</span>
                            <span class="font-bold text-slate-900 dark:text-white text-sm block mt-0.5">{{ $medicine->name }}</span>
                            @if($medicine->generic_name)
                                <span class="text-[11px] text-slate-500 dark:text-slate-400 block">{{ $medicine->generic_name }}</span>
                            @endif
                        </div>
                        <div>
                            <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block">Batches Disposed</span>
                            <span class="font-black text-rose-600 dark:text-rose-400 text-lg block mt-0.5">{{ $disposedBatches->count() }}</span>
                        </div>
                        <div>
                            <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block">Total Units Lost</span>
                            <span class="font-black text-rose-600 dark:text-rose-400 text-lg block mt-0.5">{{ number_format($disposedBatches->sum('original_quantity')) }}</span>
                        </div>
                    </div>
                </div>

                <!-- Batch List -->
                <div class="p-6 max-h-[60vh] overflow-y-auto custom-scrollbar">
                    <h4 class="text-xs font-black uppercase text-slate-400 mb-3 tracking-wider">Individual Decommissioned Batch Lots</h4>
                    <ul class="space-y-3">
                        @foreach($disposedBatches as $batch)
                            <li class="bg-white dark:bg-slate-800/80 rounded-2xl border border-slate-200/90 dark:border-slate-800 shadow-xs overflow-hidden">
                                <!-- Batch Header Strip -->
                                <div class="px-4 py-3 bg-slate-50/70 dark:bg-slate-900/60 border-b border-slate-100 dark:border-slate-800/80 flex items-center justify-between">
                                    <div class="flex items-center gap-2.5">
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 text-xs font-mono font-bold border border-slate-200 dark:border-slate-700 shadow-xs">
                                            Lot #{{ $batch->batch_number }}
                                        </span>
                                        @php
                                            $reasonBadge = match($batch->disposal_reason) {
                                                'Expired' => 'bg-rose-100 text-rose-800 dark:bg-rose-950/60 dark:text-rose-300 border-rose-200 dark:border-rose-800/60',
                                                'Damaged / Broken' => 'bg-amber-100 text-amber-800 dark:bg-amber-950/60 dark:text-amber-300 border-amber-200 dark:border-amber-800/60',
                                                'Contaminated' => 'bg-purple-100 text-purple-800 dark:bg-purple-950/60 dark:text-purple-300 border-purple-200 dark:border-purple-800/60',
                                                'Supplier Recall' => 'bg-violet-100 text-violet-800 dark:bg-violet-950/60 dark:text-violet-300 border-violet-200 dark:border-violet-800/60',
                                                default => 'bg-slate-100 text-slate-800 dark:bg-slate-800 dark:text-slate-300 border-slate-200 dark:border-slate-700',
                                            };
                                        @endphp
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider {{ $reasonBadge }} border shadow-xs">
                                            {{ $batch->disposal_reason ?? 'Unknown' }}
                                        </span>
                                    </div>
                                    <span class="text-[11px] font-bold text-slate-500 dark:text-slate-400">{{ $batch->disposed_at ? $batch->disposed_at->format('M d, Y h:i A') : '—' }}</span>
                                </div>

                                <!-- Batch Details Grid -->
                                <div class="px-4 py-3">
                                    <div class="grid grid-cols-3 gap-3 text-xs">
                                        <div>
                                            <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block">Original Qty</span>
                                            <span class="font-black text-slate-900 dark:text-white block mt-0.5">{{ number_format($batch->original_quantity ?? 0) }}</span>
                                        </div>
                                        <div>
                                            <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block">Expiry Date</span>
                                            <span class="font-bold text-slate-700 dark:text-slate-300 block mt-0.5">{{ $batch->expiration_date ? $batch->expiration_date->format('M d, Y') : '—' }}</span>
                                        </div>
                                        <div>
                                            <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block">Authorized By</span>
                                            <span class="font-bold text-slate-700 dark:text-slate-300 block mt-0.5">{{ $batch->disposer->name ?? 'System' }}</span>
                                        </div>
                                    </div>

                                    @if($batch->disposal_notes)
                                        <div class="mt-3 pt-3 border-t border-slate-100 dark:border-slate-800/80">
                                            <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block mb-1">Clinical Disposal Notes</span>
                                            <p class="text-xs text-slate-600 dark:text-slate-400 leading-relaxed bg-slate-50 dark:bg-slate-800/50 rounded-xl p-3 border border-slate-100 dark:border-slate-700/60">{{ $batch->disposal_notes }}</p>
                                        </div>
                                    @endif
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>

                <!-- Modal Footer -->
                <div class="px-6 py-4 border-t border-slate-100 dark:border-slate-800 bg-slate-50/40 dark:bg-slate-900/40 flex justify-end">
                    <button type="button" @click="open = false" 
                        class="px-5 py-2.5 text-xs font-bold text-slate-700 dark:text-slate-300 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 rounded-xl transition-colors cursor-pointer active:scale-95">
                        Close Vault
                    </button>
                </div>
            </div>
        </div>
    </div>
    </template>
</div>
@endforeach

@php
    $writtenOffExportRows = [];
    foreach ($medicines as $med) {
        foreach ($med->batches as $batch) {
            $units = (int) ($batch->original_quantity ?? $batch->quantity);
            $writtenOffExportRows[] = [
                'medicine_name' => $med->name,
                'generic_name' => $med->generic_name ?? '—',
                'form' => $med->form ?? '—',
                'category' => $med->category ?? '—',
                'unit' => $med->unit ?? 'Unit',
                'batch_number' => $batch->batch_number,
                'units_destroyed' => $units,
                'expiration_date' => $batch->expiration_date ? $batch->expiration_date->format('Y-m-d') : '—',
                'disposed_at' => $batch->disposed_at ? $batch->disposed_at->format('Y-m-d H:i') : '—',
                'disposal_reason' => $batch->disposal_reason ?? 'Unspecified',
                'authorized_by' => $batch->disposer->name ?? 'System Administrator',
                'disposal_notes' => $batch->disposal_notes ?? '—',
            ];
        }
    }

    $writtenOffSummaryExport = [
        'generated_at' => now()->format('Y-m-d H:i:s'),
        'affected_medicines' => (int) ($affectedMedicinesCount ?? 0),
        'total_disposed_batches' => (int) ($totalDisposedBatches ?? 0),
        'total_units_destroyed' => (int) ($totalUnitsDestroyed ?? 0),
        'filter_search' => request('search') ?: 'All',
        'filter_reason' => request('reason') ?: 'All',
        'filter_date_from' => request('date_from') ?: 'All',
        'filter_date_to' => request('date_to') ?: 'All',
    ];
@endphp

@push('scripts')
<script>
    function exportWrittenOffCsv(btn) {
        const icon = document.getElementById('exportWrittenOffIcon');
        const spinner = document.getElementById('exportWrittenOffSpinner');
        const text = document.getElementById('exportWrittenOffText');

        const rows = @json($writtenOffExportRows);
        const summary = @json($writtenOffSummaryExport);

        if (!rows || rows.length === 0) {
            const msg = 'No written-off or disposed records found matching the active filter to export.';
            if (window.toast && typeof window.toast.warning === 'function') {
                window.toast.warning(msg);
            } else {
                window.dispatchEvent(new CustomEvent('add-toast', { detail: { type: 'warning', message: msg } }));
            }
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

                // Report Header & Scope
                csvLines.push(['Rural Health Unit - Written-Off & Disposed Inventory Audit Report'].map(escapeCsv).join(','));
                csvLines.push([`Generated On: ${summary.generated_at}`].map(escapeCsv).join(','));
                csvLines.push([`Active Filters: Search: ${summary.filter_search} | Reason: ${summary.filter_reason} | Date Range: ${summary.filter_date_from} to ${summary.filter_date_to}`].map(escapeCsv).join(','));
                csvLines.push([]);

                // Executive KPI Summary
                csvLines.push(['--- EXECUTIVE DISPOSAL SUMMARY ---'].map(escapeCsv).join(','));
                csvLines.push(['Metric', 'Count / Total Units'].map(escapeCsv).join(','));
                csvLines.push(['Total Unique Affected Medicines', summary.affected_medicines].map(escapeCsv).join(','));
                csvLines.push(['Total Disposed Batches / Lots', summary.total_disposed_batches].map(escapeCsv).join(','));
                csvLines.push(['Total Physical Units Destroyed', summary.total_units_destroyed].map(escapeCsv).join(','));
                csvLines.push([]);

                // Itemized Columns
                csvLines.push(['--- ITEMIZED DISPOSED BATCHES AUDIT TRAIL ---'].map(escapeCsv).join(','));
                const headers = [
                    'Medicine Name',
                    'Generic Name',
                    'Dosage Form',
                    'Category',
                    'Batch / Lot #',
                    'Units Destroyed',
                    'Unit Type',
                    'Expiration Date',
                    'Disposal Timestamp',
                    'Disposal Reason',
                    'Authorized By',
                    'Clinical Disposal Notes'
                ];
                csvLines.push(headers.map(escapeCsv).join(','));

                // Data Rows
                rows.forEach(r => {
                    const rowData = [
                        r.medicine_name,
                        r.generic_name,
                        r.form,
                        r.category,
                        r.batch_number,
                        r.units_destroyed,
                        r.unit,
                        r.expiration_date,
                        r.disposed_at,
                        r.disposal_reason,
                        r.authorized_by,
                        r.disposal_notes
                    ];
                    csvLines.push(rowData.map(escapeCsv).join(','));
                });

                const csvContent = '\uFEFF' + csvLines.join('\r\n');
                const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
                const url = URL.createObjectURL(blob);
                const a = document.createElement('a');
                const dateStr = new Date().toISOString().slice(0, 10);
                a.href = url;
                a.download = `pharmacy-written-off-inventory-${dateStr}.csv`;
                document.body.appendChild(a);
                a.click();
                document.body.removeChild(a);
                URL.revokeObjectURL(url);

                const successMsg = `Exported ${rows.length} disposed lot record(s) to CSV successfully!`;
                if (window.toast && typeof window.toast.success === 'function') {
                    window.toast.success(successMsg);
                } else {
                    window.dispatchEvent(new CustomEvent('add-toast', { detail: { type: 'success', message: successMsg } }));
                }
            } catch (err) {
                console.error('Written-off CSV export failed:', err);
                const errMsg = 'Failed to generate written-off inventory CSV export.';
                if (window.toast && typeof window.toast.error === 'function') {
                    window.toast.error(errMsg);
                } else {
                    window.dispatchEvent(new CustomEvent('add-toast', { detail: { type: 'error', message: errMsg } }));
                }
            } finally {
                if (btn) btn.disabled = false;
                if (icon) icon.classList.remove('hidden');
                if (spinner) spinner.classList.add('hidden');
                if (text) text.textContent = 'Export Audit (CSV)';
            }
        }, 250);
    }
    window.exportWrittenOffCsv = exportWrittenOffCsv;

    // Flatpickr Date Picker Initialization
    document.addEventListener('DOMContentLoaded', function () {
        if (typeof flatpickr !== 'undefined') {
            window.fpFromInstance = flatpickr('#date_from', {
                dateFormat: 'Y-m-d',
                allowInput: true,
                monthSelectorType: 'static',
                disableMobile: true,
                onChange: function (selectedDates, dateStr) {
                    if (window.fpToInstance) {
                        window.fpToInstance.set('minDate', dateStr || null);
                    }
                    if (dateStr) {
                        document.getElementById('filterForm').submit();
                    }
                }
            });

            window.fpToInstance = flatpickr('#date_to', {
                dateFormat: 'Y-m-d',
                allowInput: true,
                monthSelectorType: 'static',
                disableMobile: true,
                onChange: function (selectedDates, dateStr) {
                    if (window.fpFromInstance) {
                        window.fpFromInstance.set('maxDate', dateStr || null);
                    }
                    if (dateStr) {
                        document.getElementById('filterForm').submit();
                    }
                }
            });

            // Link pre-filled values
            const initFrom = document.getElementById('date_from')?.value;
            const initTo = document.getElementById('date_to')?.value;
            if (initFrom && window.fpToInstance) {
                window.fpToInstance.set('minDate', initFrom);
            }
            if (initTo && window.fpFromInstance) {
                window.fpFromInstance.set('maxDate', initTo);
            }
        }
    });

    function setQuickDateRange(type) {
        const today = new Date();
        const pad = n => String(n).padStart(2, '0');
        const format = d => `${d.getFullYear()}-${pad(d.getMonth() + 1)}-${pad(d.getDate())}`;

        let from = '';
        let to = format(today);

        if (type === 'today') {
            from = to;
        } else if (type === 'last7') {
            const d = new Date();
            d.setDate(d.getDate() - 6);
            from = format(d);
        } else if (type === 'thisMonth') {
            from = `${today.getFullYear()}-${pad(today.getMonth() + 1)}-01`;
        } else if (type === 'last30') {
            const d = new Date();
            d.setDate(d.getDate() - 29);
            from = format(d);
        }

        if (window.fpFromInstance) {
            window.fpFromInstance.setDate(from, false);
        } else {
            const el = document.getElementById('date_from');
            if (el) el.value = from;
        }

        if (window.fpToInstance) {
            window.fpToInstance.setDate(to, false);
        } else {
            const el = document.getElementById('date_to');
            if (el) el.value = to;
        }

        document.getElementById('filterForm').submit();
    }

    function clearDate(id) {
        if (id === 'date_from' && window.fpFromInstance) {
            window.fpFromInstance.clear();
        } else if (id === 'date_to' && window.fpToInstance) {
            window.fpToInstance.clear();
        } else {
            const el = document.getElementById(id);
            if (el) el.value = '';
        }
        document.getElementById('filterForm').submit();
    }

    window.setQuickDateRange = setQuickDateRange;
    window.clearDate = clearDate;
</script>
@endpush
@endsection
