@extends('layouts.pharmacy')

@section('header', 'Written-Off Inventory')

@section('content')
<div class="max-w-7xl mx-auto space-y-6 pb-12">

    {{-- Breadcrumb Navigation --}}
    <nav class="flex items-center gap-2 text-xs font-medium text-slate-500 dark:text-slate-400 py-2 border-b border-slate-200/60 dark:border-slate-800" aria-label="Breadcrumb">
        <a href="{{ route('pharmacy.dashboard') }}" class="hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors flex items-center gap-1.5 shrink-0">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
            <span>Home</span>
        </a>
        <span class="text-slate-300 dark:text-slate-700">/</span>
        <span class="hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors">Pharmacy</span>
        <span class="text-slate-300 dark:text-slate-700">/</span>
        <span class="text-slate-700 dark:text-slate-300 font-semibold">Written-Off Inventory</span>
    </nav>

    {{-- Header Section --}}
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 pb-4 border-b border-slate-200/80 dark:border-slate-800">
        <div>
            <h1 class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white tracking-tight flex items-center gap-2.5">
                <span class="w-9 h-9 sm:w-10 sm:h-10 rounded-2xl bg-rose-100/80 dark:bg-rose-950/60 text-rose-600 dark:text-rose-400 flex items-center justify-center shrink-0 border border-rose-500/20 shadow-2xs">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                </span>
                <span>Written-Off & Disposed Inventory</span>
            </h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Audit trail of all batches disposed, written off, or destroyed — grouped by medicine.</p>
        </div>

        <div class="flex items-center gap-2.5">
            <span class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 text-slate-600 dark:text-slate-400 text-xs font-semibold shadow-2xs">
                <svg class="w-4 h-4 text-rose-600 dark:text-rose-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                </svg>
                <span>Permanent Audit Record</span>
            </span>
        </div>
    </div>

    {{-- Summary Statistics Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        {{-- Affected Medicines --}}
        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 p-5 shadow-xs">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-sky-100/80 dark:bg-sky-950/60 text-sky-600 dark:text-sky-400 flex items-center justify-center border border-sky-500/20 shadow-2xs shrink-0">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" /></svg>
                </div>
                <div>
                    <p class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">Affected Medicines</p>
                    <p class="text-2xl font-black text-slate-900 dark:text-white">{{ $affectedMedicinesCount }}</p>
                    <p class="text-[10px] text-slate-500 dark:text-slate-400">unique item(s)</p>
                </div>
            </div>
        </div>

        {{-- Total Disposed Batches --}}
        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 p-5 shadow-xs">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-rose-100/80 dark:bg-rose-950/60 text-rose-600 dark:text-rose-400 flex items-center justify-center border border-rose-500/20 shadow-2xs shrink-0">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                </div>
                <div>
                    <p class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">Total Disposed</p>
                    <p class="text-2xl font-black text-slate-900 dark:text-white">{{ $totalDisposedBatches }}</p>
                    <p class="text-[10px] text-slate-500 dark:text-slate-400">batch(es)</p>
                </div>
            </div>
        </div>

        {{-- Total Units Destroyed --}}
        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 p-5 shadow-xs">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-amber-100/80 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400 flex items-center justify-center border border-amber-500/20 shadow-2xs shrink-0">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" /></svg>
                </div>
                <div>
                    <p class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">Units Destroyed</p>
                    <p class="text-2xl font-black text-slate-900 dark:text-white">{{ number_format($totalUnitsDestroyed) }}</p>
                    <p class="text-[10px] text-slate-500 dark:text-slate-400">total units written off</p>
                </div>
            </div>
        </div>

        {{-- Reason Breakdown --}}
        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 p-5 shadow-xs">
            <p class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 mb-2.5">Breakdown by Reason</p>
            @if(count($reasonBreakdown) > 0)
                <div class="space-y-1.5">
                    @foreach($reasonBreakdown as $reason => $count)
                        @php
                            $pct = $totalDisposedBatches > 0 ? round(($count / $totalDisposedBatches) * 100) : 0;
                            $barColor = match($reason) {
                                'Expired' => 'bg-slate-500',
                                'Damaged / Broken' => 'bg-amber-500',
                                'Contaminated' => 'bg-rose-500',
                                'Supplier Recall' => 'bg-violet-500',
                                default => 'bg-sky-500',
                            };
                        @endphp
                        <div class="flex items-center gap-2">
                            <span class="text-[10px] font-bold text-slate-600 dark:text-slate-400 w-28 truncate shrink-0">{{ $reason ?? 'Unknown' }}</span>
                            <div class="flex-1 h-1.5 rounded-full bg-slate-100 dark:bg-slate-800 overflow-hidden">
                                <div class="{{ $barColor }} h-full rounded-full" style="width: {{ $pct }}%"></div>
                            </div>
                            <span class="text-[10px] font-bold text-slate-500 dark:text-slate-400 w-6 text-right shrink-0">{{ $count }}</span>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-xs text-slate-400 dark:text-slate-500">No disposed batches yet.</p>
            @endif
        </div>
    </div>

    {{-- Search & Filter Toolbar --}}
    <div class="bg-white/80 dark:bg-slate-900/80 backdrop-blur-md rounded-2xl border border-slate-200/80 dark:border-slate-800 p-4 sm:p-5 shadow-xs">
        <form id="filterForm" action="{{ route('pharmacy.written-off') }}" method="GET" class="space-y-3">
            <div class="flex flex-col sm:flex-row gap-3 items-stretch sm:items-center">
                {{-- Search --}}
                <div class="relative flex-1">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                    </div>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by medicine name, generic name, or lot number..."
                        oninput="clearTimeout(this.timer); this.timer = setTimeout(() => { this.form.submit(); }, 500);"
                        class="h-11 pl-10 pr-4 block w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50/70 dark:bg-slate-800/60 text-slate-900 dark:text-white text-sm font-semibold focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 shadow-2xs transition-all placeholder:text-slate-400">
                </div>

                {{-- Reason Filter --}}
                <select name="reason" onchange="this.form.submit()"
                    class="h-11 px-4 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50/70 dark:bg-slate-800/60 text-slate-900 dark:text-white text-sm font-semibold focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 shadow-2xs transition-all cursor-pointer sm:w-52">
                    <option value="all">All Reasons</option>
                    @foreach($distinctReasons as $reason)
                        <option value="{{ $reason }}" @selected(request('reason') === $reason)>{{ $reason }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Date range row --}}
            <div class="flex flex-col sm:flex-row gap-3 items-stretch sm:items-center">
                <div class="flex items-center gap-2 flex-1">
                    <label class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider shrink-0">From</label>
                    <input type="date" name="date_from" value="{{ request('date_from') }}" onchange="this.form.submit()"
                        class="h-11 px-4 flex-1 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50/70 dark:bg-slate-800/60 text-slate-900 dark:text-white text-sm font-semibold focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 shadow-2xs transition-all">
                </div>
                <div class="flex items-center gap-2 flex-1">
                    <label class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider shrink-0">To</label>
                    <input type="date" name="date_to" value="{{ request('date_to') }}" onchange="this.form.submit()"
                        class="h-11 px-4 flex-1 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50/70 dark:bg-slate-800/60 text-slate-900 dark:text-white text-sm font-semibold focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 shadow-2xs transition-all">
                </div>

                @if(request('search') || request('reason') || request('date_from') || request('date_to'))
                    <a href="{{ route('pharmacy.written-off') }}" class="h-11 px-4 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700 text-xs font-bold transition-colors flex items-center gap-1.5 shadow-2xs shrink-0 cursor-pointer">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        <span>Clear Filters</span>
                    </a>
                @endif
            </div>

            <input type="hidden" name="per_page" id="wo_per_page" value="{{ request('per_page', 15) }}">
        </form>
    </div>

    {{-- Written-Off Table (Grouped by Medicine) --}}
    <div class="bg-white dark:bg-slate-900 rounded-3xl shadow-xs border border-slate-200/90 dark:border-slate-800 overflow-hidden">
        <div class="px-6 py-5 border-b border-slate-100 dark:border-slate-800/80 bg-slate-50/70 dark:bg-slate-900/60 flex items-center justify-between">
            <div>
                <h3 class="text-base font-bold text-slate-900 dark:text-white">Disposed Medicines</h3>
                <span class="text-xs text-slate-500 dark:text-slate-400 font-medium">{{ $medicines->total() }} medicine(s) with written-off batches</span>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/90 dark:bg-slate-950/80 text-slate-500 dark:text-slate-400 text-[11px] font-extrabold uppercase tracking-wider border-b border-slate-200/80 dark:border-slate-800">
                        <th class="p-4 sm:p-5">Medicine</th>
                        <th class="p-4 sm:p-5 text-center">Batches Disposed</th>
                        <th class="p-4 sm:p-5 text-center">Total Units Destroyed</th>
                        <th class="p-4 sm:p-5">Reasons</th>
                        <th class="p-4 sm:p-5">Latest Disposal</th>
                        <th class="p-4 sm:p-5 text-right">Details</th>
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
                            {{-- Medicine --}}
                            <td class="p-4 sm:p-5 align-top">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-2xl bg-rose-100/80 dark:bg-rose-950/60 text-rose-600 dark:text-rose-400 font-black flex items-center justify-center border border-rose-500/20 shadow-2xs shrink-0 text-xs">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" /></svg>
                                    </div>
                                    <div class="min-w-0">
                                        <p class="font-extrabold text-slate-900 dark:text-white text-sm truncate">{{ $medicine->name }}</p>
                                        @if($medicine->generic_name)
                                            <p class="text-[11px] font-semibold text-slate-500 dark:text-slate-400 mt-0.5 truncate">{{ $medicine->generic_name }}</p>
                                        @endif
                                        <div class="flex items-center gap-1.5 mt-1">
                                            @if($medicine->form)
                                                <span class="text-[10px] px-1.5 py-0.5 rounded bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400 font-semibold border border-slate-200/60 dark:border-slate-700/60">{{ $medicine->form }}</span>
                                            @endif
                                            @if($medicine->unit)
                                                <span class="text-[10px] px-1.5 py-0.5 rounded bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400 font-semibold border border-slate-200/60 dark:border-slate-700/60">{{ $medicine->unit }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </td>

                            {{-- Batch Count --}}
                            <td class="p-4 sm:p-5 align-top text-center">
                                <span class="inline-flex items-center justify-center w-9 h-9 rounded-xl bg-rose-100/80 dark:bg-rose-950/60 text-rose-700 dark:text-rose-300 text-sm font-black border border-rose-200/60 dark:border-rose-800/40 shadow-2xs">
                                    {{ $batchCount }}
                                </span>
                            </td>

                            {{-- Total Units --}}
                            <td class="p-4 sm:p-5 align-top text-center">
                                <p class="text-sm font-black text-rose-700 dark:text-rose-400">{{ number_format($totalUnits) }}</p>
                                <p class="text-[10px] text-slate-500 dark:text-slate-400 mt-0.5">units</p>
                            </td>

                            {{-- Reasons --}}
                            <td class="p-4 sm:p-5 align-top">
                                <div class="flex flex-wrap gap-1">
                                    @foreach($reasons as $reason)
                                        @php
                                            $reasonColors = match($reason) {
                                                'Expired' => 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300 border-slate-200 dark:border-slate-700',
                                                'Damaged / Broken' => 'bg-amber-100 text-amber-800 dark:bg-amber-950/60 dark:text-amber-300 border-amber-200 dark:border-amber-800/60',
                                                'Contaminated' => 'bg-rose-100 text-rose-800 dark:bg-rose-950/60 dark:text-rose-300 border-rose-200 dark:border-rose-800/60',
                                                'Supplier Recall' => 'bg-violet-100 text-violet-800 dark:bg-violet-950/60 dark:text-violet-300 border-violet-200 dark:border-violet-800/60',
                                                default => 'bg-sky-100 text-sky-800 dark:bg-sky-950/60 dark:text-sky-300 border-sky-200 dark:border-sky-800/60',
                                            };
                                        @endphp
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold {{ $reasonColors }} border shadow-2xs">
                                            {{ $reason }}
                                        </span>
                                    @endforeach
                                </div>
                            </td>

                            {{-- Latest Disposal --}}
                            <td class="p-4 sm:p-5 align-top">
                                @if($latestDisposal)
                                    <p class="text-sm font-bold text-slate-800 dark:text-slate-200">{{ $latestDisposal->disposed_at->format('M d, Y') }}</p>
                                    <p class="text-[10px] text-slate-500 dark:text-slate-400 mt-0.5">{{ $latestDisposal->disposed_at->format('h:i A') }}</p>
                                    @if($latestDisposal->disposer)
                                        <p class="text-[10px] text-slate-400 mt-0.5">by {{ $latestDisposal->disposer->name }}</p>
                                    @endif
                                @else
                                    <span class="text-sm text-slate-400">—</span>
                                @endif
                            </td>

                            {{-- Details Button --}}
                            <td class="p-4 sm:p-5 align-top text-right">
                                <button type="button" @click="$dispatch('open-batch-vault-{{ $medicine->id }}')" class="h-8 px-3 rounded-xl inline-flex items-center gap-1.5 text-xs font-bold text-slate-700 dark:text-slate-300 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 border border-slate-200 dark:border-slate-700 shadow-2xs transition-all cursor-pointer active:scale-95">
                                    <span>View Batches</span>
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-16 text-center text-slate-500 dark:text-slate-400">
                                <div class="w-14 h-14 rounded-2xl bg-slate-100 dark:bg-slate-800 text-slate-400 dark:text-slate-500 mx-auto flex items-center justify-center mb-3 shadow-xs">
                                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                </div>
                                <h3 class="text-base font-bold text-slate-900 dark:text-white mb-1">No written-off medicines</h3>
                                <p class="text-xs text-slate-500 dark:text-slate-400">No disposed inventory records match your filter criteria.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($medicines->hasPages() || $medicines->total() > 15)
            <div class="px-6 py-4 border-t border-slate-100 dark:border-slate-800/80 bg-slate-50/70 dark:bg-slate-900/60 flex flex-col sm:flex-row items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <label class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Items per page</label>
                    <select onchange="document.getElementById('wo_per_page').value = this.value; document.getElementById('filterForm').submit()"
                        class="h-9 px-3 pr-8 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-white text-xs font-bold shadow-2xs cursor-pointer focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500">
                        @foreach([15, 30, 50, 100] as $size)
                            <option value="{{ $size }}" @selected(request('per_page', 15) == $size)>{{ $size }} per page</option>
                        @endforeach
                    </select>
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

{{-- ═══════════════════════════════════════════════════════════ --}}
{{-- Batch Vault Modals (one per medicine) --}}
{{-- ═══════════════════════════════════════════════════════════ --}}
@foreach($medicines as $medicine)
    @php
        $disposedBatches = $medicine->batches->sortByDesc('disposed_at');
    @endphp
    <div x-data="{ open: false }" 
         @open-batch-vault-{{ $medicine->id }}.window="open = true" 
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
                
                {{-- Modal Header --}}
                <div class="px-6 py-5 border-b border-slate-200 dark:border-slate-800 bg-rose-50/50 dark:bg-rose-950/20">
                    <div class="flex justify-between items-start mb-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-rose-100 dark:bg-rose-950/60 text-rose-600 dark:text-rose-400 flex items-center justify-center border border-rose-500/20 shadow-2xs">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                            </div>
                            <div>
                                <h3 class="text-base font-bold text-slate-900 dark:text-white" id="modal-title">Disposed Batch Vault</h3>
                                <p class="text-xs text-slate-500 dark:text-slate-400">{{ $medicine->name }}</p>
                            </div>
                        </div>
                        <button type="button" @click="open = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 p-1.5 rounded-xl hover:bg-slate-200 dark:hover:bg-slate-700 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>

                    {{-- Medicine Summary --}}
                    <div class="grid grid-cols-3 gap-3 pt-3.5 border-t border-slate-200/90 dark:border-slate-700/80">
                        <div>
                            <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block">Medicine</span>
                            <span class="font-bold text-slate-900 dark:text-white text-sm block mt-0.5">{{ $medicine->name }}</span>
                            @if($medicine->generic_name)
                                <span class="text-[10px] text-slate-500 dark:text-slate-400 block">{{ $medicine->generic_name }}</span>
                            @endif
                        </div>
                        <div>
                            <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block">Batches Disposed</span>
                            <span class="font-black text-rose-700 dark:text-rose-400 text-lg block mt-0.5">{{ $disposedBatches->count() }}</span>
                        </div>
                        <div>
                            <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block">Total Units Lost</span>
                            <span class="font-black text-rose-700 dark:text-rose-400 text-lg block mt-0.5">{{ number_format($disposedBatches->sum('original_quantity')) }}</span>
                        </div>
                    </div>
                </div>

                {{-- Batch List --}}
                <div class="p-6 max-h-[60vh] overflow-y-auto custom-scrollbar">
                    <h4 class="text-xs font-black uppercase text-slate-400 mb-3 tracking-wider">Individual Batch Records</h4>
                    <ul class="space-y-3">
                        @foreach($disposedBatches as $batch)
                            <li class="bg-white dark:bg-slate-800/80 rounded-2xl border border-slate-200/90 dark:border-slate-800 shadow-2xs overflow-hidden">
                                {{-- Batch header strip --}}
                                <div class="px-4 py-3 bg-slate-50/70 dark:bg-slate-900/60 border-b border-slate-100 dark:border-slate-800/80 flex items-center justify-between">
                                    <div class="flex items-center gap-2.5">
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 text-xs font-mono font-bold border border-slate-200 dark:border-slate-700 shadow-2xs">
                                            {{ $batch->batch_number }}
                                        </span>
                                        @php
                                            $reasonColors = match($batch->disposal_reason) {
                                                'Expired' => 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300 border-slate-200 dark:border-slate-700',
                                                'Damaged / Broken' => 'bg-amber-100 text-amber-800 dark:bg-amber-950/60 dark:text-amber-300 border-amber-200 dark:border-amber-800/60',
                                                'Contaminated' => 'bg-rose-100 text-rose-800 dark:bg-rose-950/60 dark:text-rose-300 border-rose-200 dark:border-rose-800/60',
                                                'Supplier Recall' => 'bg-violet-100 text-violet-800 dark:bg-violet-950/60 dark:text-violet-300 border-violet-200 dark:border-violet-800/60',
                                                default => 'bg-sky-100 text-sky-800 dark:bg-sky-950/60 dark:text-sky-300 border-sky-200 dark:border-sky-800/60',
                                            };
                                        @endphp
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold {{ $reasonColors }} border shadow-2xs">
                                            {{ $batch->disposal_reason ?? 'Unknown' }}
                                        </span>
                                    </div>
                                    <span class="text-[10px] font-bold text-slate-400">{{ $batch->disposed_at ? $batch->disposed_at->format('M d, Y h:i A') : '—' }}</span>
                                </div>

                                {{-- Batch details grid --}}
                                <div class="px-4 py-3">
                                    <div class="grid grid-cols-3 gap-3 text-xs">
                                        <div>
                                            <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block">Original Qty</span>
                                            <span class="font-black text-slate-900 dark:text-white block mt-0.5">{{ $batch->original_quantity ?? '—' }}</span>
                                        </div>
                                        <div>
                                            <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block">Expiry Date</span>
                                            <span class="font-bold text-slate-700 dark:text-slate-300 block mt-0.5">{{ $batch->expiration_date ? $batch->expiration_date->format('M d, Y') : '—' }}</span>
                                        </div>
                                        <div>
                                            <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block">Disposed By</span>
                                            <span class="font-bold text-slate-700 dark:text-slate-300 block mt-0.5">{{ $batch->disposer->name ?? 'Unknown' }}</span>
                                        </div>
                                    </div>

                                    @if($batch->disposal_notes)
                                        <div class="mt-3 pt-3 border-t border-slate-100 dark:border-slate-800/80">
                                            <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block mb-1">Notes</span>
                                            <p class="text-xs text-slate-600 dark:text-slate-400 leading-relaxed bg-slate-50 dark:bg-slate-800/40 rounded-lg p-2.5 border border-slate-100 dark:border-slate-700/60">{{ $batch->disposal_notes }}</p>
                                        </div>
                                    @endif
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>

                {{-- Modal Footer --}}
                <div class="px-6 py-4 border-t border-slate-100 dark:border-slate-800 bg-slate-50/40 dark:bg-slate-900/40 flex justify-end">
                    <button type="button" @click="open = false" class="px-5 py-2.5 text-xs font-bold text-slate-700 dark:text-slate-300 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 rounded-xl transition-colors cursor-pointer">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>
@endforeach
@endsection
