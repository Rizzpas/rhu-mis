@extends('layouts.pharmacy')

@section('header', 'Medicine List')

@section('content')
<div class="max-w-7xl mx-auto space-y-6 pb-12">
    
    <!-- Breadcrumb Navigation -->
    <nav class="flex items-center gap-2 text-xs font-medium text-slate-500 dark:text-slate-400 py-2 border-b border-slate-200/60 dark:border-slate-800" aria-label="Breadcrumb">
        <a href="{{ route('pharmacy.dashboard') }}" class="hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors flex items-center gap-1.5 shrink-0">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
            <span>Home</span>
        </a>
        <span class="text-slate-300 dark:text-slate-700">/</span>
        <span class="hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors">Pharmacy</span>
        <span class="text-slate-300 dark:text-slate-700">/</span>
        <span class="text-slate-700 dark:text-slate-300 font-semibold">Inventory Management</span>
    </nav>

    <!-- Header Section (Content Management Style) -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 pb-4 border-b border-slate-200/80 dark:border-slate-800">
        <div>
            <h1 class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white tracking-tight flex items-center gap-2.5">
                <span class="w-9 h-9 sm:w-10 sm:h-10 rounded-2xl bg-emerald-100/80 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0 border border-emerald-500/20 shadow-2xs">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                    </svg>
                </span>
                <span>Inventory Overview & Formulary</span>
            </h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Manage medicines, monitor active batch quantities, and review expiration dates.</p>
        </div>

        <div class="flex items-center gap-2.5">
            <button @click="$dispatch('open-add-medicine')" class="px-4 py-2.5 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white text-xs font-bold shadow-md shadow-emerald-600/20 flex items-center gap-2 active:scale-95 transition-all cursor-pointer">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                <span>Add New Medicine</span>
            </button>
        </div>
    </div>

    <!-- Inventory Alerts Strip -->
    @if(($expiredBatchesCount ?? 0) > 0 || ($expiringSoonCount ?? 0) > 0 || ($lowStockCount ?? 0) > 0 || ($outOfStockCount ?? 0) > 0)
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3.5">
        @if(($expiredBatchesCount ?? 0) > 0)
        <a href="{{ route('pharmacy.medicines', ['status_filter' => 'expired']) }}" class="p-4 rounded-2xl bg-rose-50/80 hover:bg-rose-100/80 dark:bg-rose-950/40 dark:hover:bg-rose-950/60 border border-rose-200/80 dark:border-rose-800/60 transition-all group flex items-center justify-between shadow-2xs">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-rose-100 dark:bg-rose-900/60 text-rose-600 dark:text-rose-400 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <div>
                    <p class="text-xs font-bold text-rose-900 dark:text-rose-300">Expired Batches</p>
                    <p class="text-lg font-black text-rose-700 dark:text-rose-400">{{ $expiredBatchesCount }} Batch(es)</p>
                </div>
            </div>
            <span class="relative flex h-2.5 w-2.5"><span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-rose-400 opacity-75"></span><span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-rose-500"></span></span>
        </a>
        @endif

        @if(($expiringSoonCount ?? 0) > 0)
        <a href="{{ route('pharmacy.medicines', ['status_filter' => 'expiring_soon']) }}" class="p-4 rounded-2xl bg-amber-50/80 hover:bg-amber-100/80 dark:bg-amber-950/40 dark:hover:bg-amber-950/60 border border-amber-200/80 dark:border-amber-800/60 transition-all group flex items-center justify-between shadow-2xs">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-amber-100 dark:bg-amber-900/60 text-amber-600 dark:text-amber-400 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <div>
                    <p class="text-xs font-bold text-amber-900 dark:text-amber-300">Expiring Soon (30d)</p>
                    <p class="text-lg font-black text-amber-700 dark:text-amber-400">{{ $expiringSoonCount }} Batch(es)</p>
                </div>
            </div>
            <span class="relative flex h-2.5 w-2.5"><span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-amber-400 opacity-75"></span><span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-amber-500"></span></span>
        </a>
        @endif

        @if(($lowStockCount ?? 0) > 0)
        <a href="{{ route('pharmacy.medicines', ['status_filter' => 'low_stock']) }}" class="p-4 rounded-2xl bg-blue-50/80 hover:bg-blue-100/80 dark:bg-blue-950/40 dark:hover:bg-blue-950/60 border border-blue-200/80 dark:border-blue-800/60 transition-all group flex items-center justify-between shadow-2xs">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-blue-100 dark:bg-blue-900/60 text-blue-600 dark:text-blue-400 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <div>
                    <p class="text-xs font-bold text-blue-900 dark:text-blue-300">Low Stock (&lt;20)</p>
                    <p class="text-lg font-black text-blue-700 dark:text-blue-400">{{ $lowStockCount }} Item(s)</p>
                </div>
            </div>
        </a>
        @endif

        @if(($outOfStockCount ?? 0) > 0)
        <a href="{{ route('pharmacy.medicines', ['status_filter' => 'out_of_stock']) }}" class="p-4 rounded-2xl bg-slate-100/80 hover:bg-slate-200/80 dark:bg-slate-800/60 dark:hover:bg-slate-800/90 border border-slate-200/80 dark:border-slate-700/60 transition-all group flex items-center justify-between shadow-2xs">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-slate-200 dark:bg-slate-700 text-slate-600 dark:text-slate-400 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path></svg>
                </div>
                <div>
                    <p class="text-xs font-bold text-slate-700 dark:text-slate-300">Out of Stock</p>
                    <p class="text-lg font-black text-slate-800 dark:text-slate-200">{{ $outOfStockCount }} Item(s)</p>
                </div>
            </div>
        </a>
        @endif
    </div>
    @endif

    <!-- Search and Filter Bar (Floating Container) -->
    <div class="bg-white/80 dark:bg-slate-900/80 backdrop-blur-md rounded-2xl border border-slate-200/80 dark:border-slate-800 p-4 sm:p-5 shadow-xs">
        <form id="filterForm" action="{{ route('pharmacy.medicines') }}" method="GET" class="flex flex-col sm:flex-row gap-3 items-center justify-between">
            <div class="relative flex-1 w-full">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                </div>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search brand name, generic name..." 
                    oninput="clearTimeout(this.timer); this.timer = setTimeout(() => { this.form.submit(); }, 400);"
                    class="h-11 pl-10 pr-4 block w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50/70 dark:bg-slate-800/60 text-slate-900 dark:text-white text-sm font-semibold focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 shadow-2xs transition-all placeholder:text-slate-400">
            </div>

            <div class="flex items-center gap-3 w-full sm:w-auto">
                @php
                    $formFilterOptions = ['all' => 'All Dosage Forms'];
                    foreach($forms as $formItem) {
                        $formFilterOptions[$formItem] = ucfirst($formItem);
                    }
                @endphp
                <div class="w-full sm:w-52">
                    <x-select 
                        name="form_filter" 
                        :options="$formFilterOptions" 
                        :value="request('form_filter', 'all')"
                        @change="$el.closest('form').submit()"
                        class="!h-11 font-semibold"
                    />
                </div>

                @if(request('status_filter') || request('search') || (request('form_filter') && request('form_filter') !== 'all'))
                    <a href="{{ route('pharmacy.medicines') }}" class="h-11 px-3.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700 text-xs font-bold transition-colors flex items-center gap-1.5 shadow-2xs shrink-0 cursor-pointer" title="Clear Filters">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        <span>Reset</span>
                    </a>
                @endif
            </div>
            
            <input type="hidden" name="per_page" id="medicines_per_page" value="{{ request('per_page', 10) }}">
            @if(request('status_filter'))
                <input type="hidden" name="status_filter" value="{{ request('status_filter') }}">
            @endif
        </form>
    </div>

    <!-- Active Filter Banner (If Active) -->
    @if(request('status_filter'))
        @php
            $filterLabels = [
                'expired' => ['label' => 'Expired Batches', 'class' => 'bg-rose-50 text-rose-800 border-rose-200 dark:bg-rose-950/40 dark:text-rose-300 dark:border-rose-800/60'],
                'expiring_soon' => ['label' => 'Expiring Soon (Within 30 Days)', 'class' => 'bg-amber-50 text-amber-800 border-amber-200 dark:bg-amber-950/40 dark:text-amber-300 dark:border-amber-800/60'],
                'low_stock' => ['label' => 'Low Stock (< 20 units)', 'class' => 'bg-blue-50 text-blue-800 border-blue-200 dark:bg-blue-950/40 dark:text-blue-300 dark:border-blue-800/60'],
                'out_of_stock' => ['label' => 'Out of Stock Items', 'class' => 'bg-slate-100 text-slate-800 border-slate-200 dark:bg-slate-800 dark:text-slate-300 dark:border-slate-700'],
            ];
            $currentFilter = $filterLabels[request('status_filter')] ?? null;
        @endphp
        @if($currentFilter)
            <div class="p-3.5 rounded-2xl border {{ $currentFilter['class'] }} flex items-center justify-between shadow-2xs text-xs">
                <span class="font-bold flex items-center gap-2">
                    <span>Filtering by:</span>
                    <span class="underline font-black">{{ $currentFilter['label'] }}</span>
                    <span>({{ $medicines->total() }} matches)</span>
                </span>
                <a href="{{ route('pharmacy.medicines') }}" class="font-black hover:underline cursor-pointer">Remove Filter &times;</a>
            </div>
        @endif
    @endif

    <!-- Medicine Table Container -->
    <div class="bg-white dark:bg-slate-900 rounded-3xl shadow-xs border border-slate-200/90 dark:border-slate-800 overflow-hidden">
        <div class="px-6 py-5 border-b border-slate-100 dark:border-slate-800/80 bg-slate-50/70 dark:bg-slate-900/60 flex items-center justify-between">
            <div>
                <h3 class="text-base font-bold text-slate-900 dark:text-white">Medicines Catalog</h3>
                <span class="text-xs text-slate-500 dark:text-slate-400 font-medium">{{ $medicines->total() }} formulary item(s) registered</span>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/90 dark:bg-slate-950/80 text-slate-500 dark:text-slate-400 text-[11px] font-extrabold uppercase tracking-wider border-b border-slate-200/80 dark:border-slate-800">
                        <th class="p-4 sm:p-5 w-16 text-center">#</th>
                        <th class="p-4 sm:p-5">Brand & Status</th>
                        <th class="p-4 sm:p-5">Generic Name</th>
                        <th class="p-4 sm:p-5">Form & Category</th>
                        <th class="p-4 sm:p-5 text-center">Total Stock</th>
                        <th class="p-4 sm:p-5 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800 text-sm">
                    @forelse($medicines as $medicine)
                        @php
                            $totalStock = $medicine->total_stock;
                            $hasExpired = $medicine->batches->contains(function ($batch) {
                                return $batch->quantity > 0 && $batch->expiration_date < now();
                            });
                            $hasExpiringSoon = $medicine->batches->contains(function ($batch) {
                                return $batch->quantity > 0 && $batch->expiration_date >= now() && $batch->expiration_date->diffInDays(now()) <= 30;
                            });

                            if ($hasExpired) {
                                $rowClass = "hover:bg-rose-50/40 dark:hover:bg-rose-950/20 transition-colors border-l-4 border-rose-500";
                                $statusBadge = '<span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-rose-100 text-rose-800 dark:bg-rose-950/60 dark:text-rose-300 border border-rose-200 dark:border-rose-800/60 shadow-2xs uppercase">Expired Batch</span>';
                            } elseif ($totalStock == 0) {
                                $rowClass = "hover:bg-slate-100/50 dark:hover:bg-slate-800/40 transition-colors border-l-4 border-slate-400 opacity-85";
                                $statusBadge = '<span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-slate-200 text-slate-700 dark:bg-slate-800 dark:text-slate-300 border border-slate-300 dark:border-slate-700 shadow-2xs uppercase">Out of Stock</span>';
                            } elseif ($hasExpiringSoon) {
                                $rowClass = "hover:bg-amber-50/40 dark:hover:bg-amber-950/20 transition-colors border-l-4 border-amber-500";
                                $statusBadge = '<span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-amber-100 text-amber-800 dark:bg-amber-950/60 dark:text-amber-300 border border-amber-200 dark:border-amber-800/60 shadow-2xs uppercase animate-pulse">Expiring Soon</span>';
                            } elseif ($totalStock < 20) {
                                $rowClass = "hover:bg-blue-50/40 dark:hover:bg-blue-950/20 transition-colors border-l-4 border-blue-500";
                                $statusBadge = '<span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-blue-100 text-blue-800 dark:bg-blue-950/60 dark:text-blue-300 border border-blue-200 dark:border-blue-800/60 shadow-2xs uppercase">Low Stock</span>';
                            } else {
                                $rowClass = "hover:bg-emerald-50/30 dark:hover:bg-slate-800/40 transition-colors border-l-4 border-transparent";
                                $statusBadge = '<span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-emerald-100 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800/60 shadow-2xs uppercase">Optimal</span>';
                            }
                        @endphp
                        <tr class="{{ $rowClass }}">
                            <td class="p-4 sm:p-5 text-center text-xs font-mono font-bold text-slate-400">
                                {{ $loop->iteration + ($medicines->currentPage() - 1) * $medicines->perPage() }}
                            </td>
                            <td class="p-4 sm:p-5">
                                <div class="flex flex-col items-start gap-1">
                                    <span class="font-bold text-slate-900 dark:text-white text-sm sm:text-base">{{ $medicine->name }}</span>
                                    {!! $statusBadge !!}
                                </div>
                            </td>
                            <td class="p-4 sm:p-5 text-slate-700 dark:text-slate-300 font-semibold text-xs sm:text-sm">
                                {{ $medicine->generic_name ?? '—' }}
                            </td>
                            <td class="p-4 sm:p-5">
                                <div class="flex flex-col gap-1">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-700 w-fit">
                                        {{ $medicine->form ?? 'N/A' }}
                                    </span>
                                    <span class="text-[11px] text-slate-400 dark:text-slate-500 font-medium">
                                        {{ $medicine->category ?? 'General' }}
                                    </span>
                                </div>
                            </td>
                            <td class="p-4 sm:p-5 text-center">
                                <div class="text-lg font-black text-slate-900 dark:text-white">
                                    {{ $totalStock }}
                                </div>
                                <div class="text-[10px] text-slate-400 uppercase tracking-wider font-bold">
                                    {{ $medicine->unit ? Str::plural($medicine->unit, $totalStock) : 'Units' }}
                                </div>
                            </td>
                            <td class="p-4 sm:p-5 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    <!-- Batches Button -->
                                    <button @click="$dispatch('open-view-batches-{{ $medicine->id }}')" class="h-8 px-2.5 rounded-xl inline-flex items-center gap-1.5 text-xs font-bold text-amber-800 dark:text-amber-300 bg-amber-50 dark:bg-amber-950/40 hover:bg-amber-100 dark:hover:bg-amber-900/50 border border-amber-200 dark:border-amber-800/60 shadow-2xs transition-all cursor-pointer active:scale-95" title="View Batches">
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                                        <span>Batches ({{ $medicine->batches->count() }})</span>
                                    </button>
                                    
                                    <!-- Add Stock Button -->
                                    <button @click="$dispatch('open-add-stock-{{ $medicine->id }}')" class="h-8 px-2.5 rounded-xl inline-flex items-center gap-1.5 text-xs font-bold text-emerald-800 dark:text-emerald-300 bg-emerald-50 dark:bg-emerald-950/40 hover:bg-emerald-100 dark:hover:bg-emerald-900/50 border border-emerald-200 dark:border-emerald-800/60 shadow-2xs transition-all cursor-pointer active:scale-95" title="Add Stock">
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                        <span>+ Stock</span>
                                    </button>

                                    <!-- Edit Button -->
                                    <button @click="$dispatch('open-edit-medicine-{{ $medicine->id }}')" class="h-8 w-8 rounded-xl inline-flex items-center justify-center text-slate-700 dark:text-slate-300 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 border border-slate-200 dark:border-slate-700 shadow-2xs transition-all cursor-pointer active:scale-95" title="Edit Medicine">
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-16 text-center text-slate-500 dark:text-slate-400">
                                <div class="w-14 h-14 rounded-2xl bg-slate-100 dark:bg-slate-800 text-slate-400 dark:text-slate-500 mx-auto flex items-center justify-center mb-3 shadow-xs">
                                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                                </div>
                                <h3 class="text-base font-bold text-slate-900 dark:text-white mb-1">No medicines found</h3>
                                <p class="text-xs text-slate-500 dark:text-slate-400">There are no records matching your search or filters.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination Controls with Styled <option> Tags -->
        @if($medicines->hasPages() || $medicines->total() > 10)
            <div class="px-6 py-4 border-t border-slate-100 dark:border-slate-800/80 bg-slate-50/70 dark:bg-slate-900/60 flex flex-col sm:flex-row items-center justify-between gap-4">
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

{{-- Modals for Existing Medicines --}}
@foreach($medicines as $medicine)
<!-- Edit Medicine Modal (Content Management Style) -->
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
             class="inline-block align-bottom bg-white dark:bg-slate-900 rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full border border-slate-200/90 dark:border-slate-800">
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
                <button type="button" @click="open = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 p-1.5 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
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

<!-- Add Stock Modal (Content Management Style) -->
<div x-data="{ open: false }" 
     @open-add-stock-{{ $medicine->id }}.window="open = true" 
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
             class="inline-block align-bottom bg-white dark:bg-slate-900 rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md w-full border border-slate-200/90 dark:border-slate-800">
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
                <button type="button" @click="open = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 p-1.5 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            <div class="p-6 sm:p-7">
                <form action="{{ route('pharmacy.medicines.add-stock', $medicine->id) }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">Batch / Lot Number <span class="text-rose-500">*</span></label>
                        <input type="text" name="batch_number" required placeholder="e.g. LOT-2026-A"
                            class="w-full h-11 px-4 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50/70 dark:bg-slate-800/60 text-slate-900 dark:text-white text-sm font-semibold focus:border-emerald-500 shadow-2xs transition-all">
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

<!-- View Batches Modal (Content Management Style) -->
<div x-data="{ open: false }" 
     @open-view-batches-{{ $medicine->id }}.window="open = true" 
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
             class="inline-block align-bottom bg-white dark:bg-slate-900 rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-xl w-full border border-slate-200/90 dark:border-slate-800">
            <div class="px-6 py-5 border-b border-slate-100 dark:border-slate-800 flex justify-between items-center bg-slate-50/70 dark:bg-slate-900/60">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-amber-100/80 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400 flex items-center justify-center border border-amber-500/20 shadow-2xs">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-slate-900 dark:text-white">Active Batches Vault</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400">Inventory breakdown for {{ $medicine->name }}</p>
                    </div>
                </div>
                <button type="button" @click="open = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 p-1.5 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            <div class="p-6 sm:p-7">
                @if($medicine->batches->count() > 0)
                    <div class="overflow-hidden rounded-2xl border border-slate-200/90 dark:border-slate-800 shadow-2xs">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-slate-50/90 dark:bg-slate-950/80 border-b border-slate-200/80 dark:border-slate-800 text-[11px] uppercase tracking-wider text-slate-500 dark:text-slate-400 font-extrabold">
                                    <th class="p-3.5">Batch No.</th>
                                    <th class="p-3.5 text-center">Remaining</th>
                                    <th class="p-3.5 text-right">Expiration</th>
                                    <th class="p-3.5 text-right">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-slate-800 text-sm">
                                @foreach($medicine->batches as $batch)
                                    @php
                                        $isExpired = $batch->expiration_date < now();
                                        $isExpiringSoon = $batch->expiration_date->diffInDays(now()) <= 30 && !$isExpired;
                                    @endphp
                                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-850 transition-colors">
                                        <td class="p-3.5 font-mono text-xs font-bold text-slate-800 dark:text-slate-200">
                                            {{ $batch->batch_number ?? 'N/A' }}
                                        </td>
                                        <td class="p-3.5 text-center font-black text-slate-900 dark:text-white">
                                            {{ $batch->quantity }}
                                        </td>
                                        <td class="p-3.5 text-right text-xs font-semibold text-slate-600 dark:text-slate-400">
                                            {{ $batch->expiration_date->format('M d, Y') }}
                                        </td>
                                        <td class="p-3.5 text-right">
                                            @if($isExpired)
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-100 text-rose-800 dark:bg-rose-950/60 dark:text-rose-300 border border-rose-200 dark:border-rose-800/60 uppercase">Expired</span>
                                            @elseif($isExpiringSoon)
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800 dark:bg-amber-950/60 dark:text-amber-300 border border-amber-200 dark:border-amber-800/60 uppercase animate-pulse">Near Expiry</span>
                                            @else
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800/60 uppercase">Good</span>
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
        </div>
    </div>
</div>
@endforeach

<!-- Add New Medicine Modal (Content Management Style) -->
<div x-data="{ open: false }" 
     @open-add-medicine.window="open = true" 
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
             class="inline-block align-bottom bg-white dark:bg-slate-900 rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full border border-slate-200/90 dark:border-slate-800">
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
                <button type="button" @click="open = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 p-1.5 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
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
                                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">Batch / Lot Number <span class="text-rose-500">*</span></label>
                                <input type="text" name="batch_number" required placeholder="e.g. LOT-2026-A" 
                                    class="w-full h-11 px-4 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800/80 text-slate-900 dark:text-white text-sm font-semibold focus:border-emerald-500 shadow-2xs transition-all placeholder:text-slate-400">
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
@endsection
