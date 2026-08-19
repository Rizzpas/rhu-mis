@extends('layouts.pharmacy')

@section('header', 'Medicine List')

@section('content')
<div class="max-w-7xl mx-auto space-y-6 pb-12">
    
    <!-- Header -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-800 p-6">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h2 class="text-2xl font-extrabold text-slate-900 dark:text-white flex items-center gap-2">
                    <span class="bg-emerald-100 dark:bg-emerald-900/50 text-emerald-600 dark:text-emerald-400 p-2 rounded-xl">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                        </svg>
                    </span>
                    Inventory Overview
                </h2>
                <p class="text-slate-500 dark:text-slate-400 text-sm mt-1">Manage and track available medicines and their forms.</p>
                <div class="mt-3">
                    <button @click="$dispatch('open-add-medicine')" class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-bold rounded-xl transition-all shadow-sm hover:-translate-y-0.5">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        Add New Medicine
                    </button>
                </div>
            </div>
            <form id="filterForm" action="{{ route('pharmacy.medicines') }}" method="GET" class="shrink-0 flex flex-col sm:flex-row gap-3">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                    </div>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search medicines..." 
                        oninput="clearTimeout(this.timer); this.timer = setTimeout(() => { this.form.submit(); }, 500);"
                        class="pl-10 pr-4 py-2 border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 rounded-xl text-sm focus:ring-emerald-500 focus:border-emerald-500 block w-full dark:text-white shadow-sm transition-colors">
                </div>
                <div>
                    <select name="form_filter" onchange="this.form.submit()" 
                        class="w-full sm:w-auto py-2 pl-3 pr-8 border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 rounded-xl text-sm focus:ring-emerald-500 focus:border-emerald-500 dark:text-white shadow-sm transition-colors cursor-pointer">
                        <option value="all">All Forms</option>
                        @foreach($forms as $formItem)
                            <option value="{{ $formItem }}" {{ request('form_filter') == $formItem ? 'selected' : '' }}>
                                {{ ucfirst($formItem) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <!-- Hidden inputs to preserve pagination -->
                <input type="hidden" name="per_page" value="{{ request('per_page', 10) }}">
            </form>
        </div>
    </div> <!-- Close Header Container -->
    
    <!-- Active Status Filter Banner -->
    @if(request('status_filter'))
        @php
            $filterLabels = [
                'expired' => ['label' => 'Expired Medicines', 'color' => 'rose', 'icon' => '🔴'],
                'expiring_soon' => ['label' => 'Expiring Soon (Within 30 Days)', 'color' => 'amber', 'icon' => '🟡'],
                'low_stock' => ['label' => 'Low Stock Medicines (< 20 units)', 'color' => 'blue', 'icon' => '🔵'],
                'out_of_stock' => ['label' => 'Out of Stock Medicines', 'color' => 'slate', 'icon' => '⚫'],
            ];
            $currentFilter = $filterLabels[request('status_filter')] ?? null;
        @endphp
        @if($currentFilter)
            <div class="bg-{{ $currentFilter['color'] }}-50 dark:bg-{{ $currentFilter['color'] }}-950/30 border border-{{ $currentFilter['color'] }}-200 dark:border-{{ $currentFilter['color'] }}-800/50 rounded-2xl p-4 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <span class="text-xl">{{ $currentFilter['icon'] }}</span>
                    <div>
                        <p class="text-sm font-bold text-{{ $currentFilter['color'] }}-800 dark:text-{{ $currentFilter['color'] }}-300">Showing: {{ $currentFilter['label'] }}</p>
                        <p class="text-xs text-{{ $currentFilter['color'] }}-600 dark:text-{{ $currentFilter['color'] }}-400">{{ $medicines->total() }} medicine(s) found matching this filter.</p>
                    </div>
                </div>
                <a href="{{ route('pharmacy.medicines') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-300 text-xs font-bold rounded-xl border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors shadow-sm">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    Clear Filter
                </a>
            </div>
        @endif
    @endif
     <!-- Expiration/Stock Alerts Strip -->
    @if(($expiredBatchesCount ?? 0) > 0 || ($expiringSoonCount ?? 0) > 0 || ($expiring60Count ?? 0) > 0 || ($lowStockCount ?? 0) > 0)
    <div class="flex flex-wrap gap-3 items-center bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-3 shadow-sm mb-6">
        <div class="flex items-center gap-2 pr-4 border-r border-slate-200 dark:border-slate-700">
            <svg class="w-5 h-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
            <span class="text-sm font-bold text-slate-700 dark:text-slate-300">Inventory Alerts</span>
        </div>
        
        @if(($expiredBatchesCount ?? 0) > 0)
        <a href="{{ route('pharmacy.medicines', ['status_filter' => 'expired']) }}" class="flex items-center gap-2 px-3 py-1.5 bg-rose-50 hover:bg-rose-100 dark:bg-rose-900/40 dark:hover:bg-rose-900/60 text-rose-700 dark:text-rose-400 rounded-xl transition-colors border border-rose-100 dark:border-rose-800/50">
            <span class="relative flex h-2 w-2"><span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-rose-400 opacity-75"></span><span class="relative inline-flex rounded-full h-2 w-2 bg-rose-500"></span></span>
            <span class="text-xs font-bold">{{ $expiredBatchesCount }} Expired Batches</span>
        </a>
        @endif
        
        @if(($expiringSoonCount ?? 0) > 0 || ($expiring60Count ?? 0) > 0)
        <a href="{{ route('pharmacy.medicines', ['status_filter' => 'expiring_soon']) }}" class="flex items-center gap-2 px-3 py-1.5 bg-amber-50 hover:bg-amber-100 dark:bg-amber-900/40 dark:hover:bg-amber-900/60 text-amber-700 dark:text-amber-400 rounded-xl transition-colors border border-amber-100 dark:border-amber-800/50">
            <span class="h-2 w-2 rounded-full bg-amber-500"></span>
            <span class="text-xs font-bold">{{ ($expiringSoonCount ?? 0) + ($expiring60Count ?? 0) }} Expiring Soon</span>
        </a>
        @endif
        
        @if(($lowStockCount ?? 0) > 0)
        <a href="{{ route('pharmacy.medicines', ['status_filter' => 'low_stock']) }}" class="flex items-center gap-2 px-3 py-1.5 bg-blue-50 hover:bg-blue-100 dark:bg-blue-900/40 dark:hover:bg-blue-900/60 text-blue-700 dark:text-blue-400 rounded-xl transition-colors border border-blue-100 dark:border-blue-800/50">
            <span class="h-2 w-2 rounded-full bg-blue-500"></span>
            <span class="text-xs font-bold">{{ $lowStockCount ?? 0 }} Low Stock</span>
        </a>
        @endif
    </div>
    @endif

    <!-- Medicines Table -->
    <div class="bg-white dark:bg-slate-800/80 backdrop-blur-sm rounded-2xl shadow-sm hover:shadow-lg border border-slate-200 dark:border-slate-700/60 overflow-hidden transition-all duration-300">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 dark:bg-slate-800/50 border-b border-slate-100 dark:border-slate-700/70 text-[10px] sm:text-xs uppercase tracking-wider text-slate-500 dark:text-slate-400 font-extrabold">
                        <th class="p-4 sm:p-5 w-[5%] text-center">#</th>
                        <th class="p-4 sm:p-5 w-[25%]">Brand Name</th>
                        <th class="p-4 sm:p-5 w-[20%]">Generic Name</th>
                        <th class="p-4 sm:p-5 w-[15%]">Form</th>
                        <th class="p-4 sm:p-5 w-[15%] text-center">Stock</th>
                        <th class="p-4 sm:p-5 w-[20%] text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-700/50 text-sm">
                    @forelse($medicines as $medicine)
                        @php
                            $totalStock = $medicine->total_stock;
                            $hasExpired = false;
                            $hasExpiringSoon = false;
                            if($medicine->batches->count() > 0) {
                                $earliestBatch = $medicine->batches->first();
                                if ($earliestBatch->expiration_date < now()) {
                                    $hasExpired = true;
                                } elseif ($earliestBatch->expiration_date->diffInDays(now()) <= 30) {
                                    $hasExpiringSoon = true;
                                }
                            }
                            
                            $rowClass = "hover:bg-slate-50 dark:hover:bg-slate-800/80 transition-colors group";
                            $statusBadge = "";
                            
                            if ($hasExpired) {
                                $rowClass = "bg-rose-50/50 hover:bg-rose-50 dark:bg-rose-900/10 dark:hover:bg-rose-900/20 transition-colors group border-l-4 border-rose-500";
                                $statusBadge = '<span class="inline-flex items-center gap-1 px-2 py-1 rounded-md text-[10px] font-extrabold bg-rose-100 text-rose-700 dark:bg-rose-900/50 dark:text-rose-400 uppercase tracking-wider shadow-sm"><svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg> Expired Batch</span>';
                            } elseif ($totalStock == 0) {
                                $rowClass = "bg-slate-50/50 hover:bg-slate-100 dark:bg-slate-800/30 dark:hover:bg-slate-800/50 transition-colors group opacity-80 border-l-4 border-slate-400";
                                $statusBadge = '<span class="inline-flex items-center gap-1 px-2 py-1 rounded-md text-[10px] font-extrabold bg-slate-200 text-slate-700 dark:bg-slate-700 dark:text-slate-300 uppercase tracking-wider shadow-sm"><svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path></svg> Out of Stock</span>';
                            } elseif ($hasExpiringSoon) {
                                $rowClass = "bg-amber-50/50 hover:bg-amber-50 dark:bg-amber-900/10 dark:hover:bg-amber-900/20 transition-colors group border-l-4 border-amber-500";
                                $statusBadge = '<span class="inline-flex items-center gap-1 px-2 py-1 rounded-md text-[10px] font-extrabold bg-amber-100 text-amber-800 dark:bg-amber-900/50 dark:text-amber-400 uppercase tracking-wider shadow-sm animate-pulse"><svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg> Expiring Soon</span>';
                            } elseif ($totalStock < 20) {
                                $rowClass = "bg-blue-50/50 hover:bg-blue-50 dark:bg-blue-900/10 dark:hover:bg-blue-900/20 transition-colors group border-l-4 border-blue-400";
                                $statusBadge = '<span class="inline-flex items-center gap-1 px-2 py-1 rounded-md text-[10px] font-extrabold bg-blue-100 text-blue-800 dark:bg-blue-900/50 dark:text-blue-400 uppercase tracking-wider shadow-sm"><svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg> Low Stock</span>';
                            } else {
                                $rowClass = "hover:bg-slate-50 dark:hover:bg-slate-800/80 transition-colors group border-l-4 border-transparent";
                                $statusBadge = '<span class="inline-flex items-center gap-1 px-2 py-1 rounded-md text-[10px] font-extrabold bg-emerald-100 text-emerald-800 dark:bg-emerald-900/50 dark:text-emerald-400 uppercase tracking-wider shadow-sm"><svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg> Good</span>';
                            }
                        @endphp
                        <tr class="{{ $rowClass }}">
                            <td class="p-4 sm:p-5 text-center text-slate-400 font-medium">
                                {{ $loop->iteration + ($medicines->currentPage() - 1) * $medicines->perPage() }}
                            </td>
                            <td class="p-4 sm:p-5">
                                <div class="flex flex-col items-start gap-1.5">
                                    <span class="font-bold text-slate-800 dark:text-slate-200 text-base">{{ $medicine->name }}</span>
                                    {!! $statusBadge !!}
                                </div>
                            </td>
                            <td class="p-4 sm:p-5 text-slate-600 dark:text-slate-300">
                                {{ $medicine->generic_name ?? 'N/A' }}
                            </td>
                            <td class="p-4 sm:p-5">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-200/50 text-slate-700 dark:bg-slate-700 dark:text-slate-300 border border-slate-300/50 dark:border-slate-600">
                                    {{ $medicine->form ?? 'N/A' }}
                                </span>
                            </td>
                            <td class="p-4 sm:p-5 text-center">
                                <div class="text-lg font-black text-slate-800 dark:text-slate-200">
                                    {{ $totalStock }}
                                </div>
                                <div class="text-[10px] text-slate-500 uppercase tracking-widest font-bold">Qty</div>
                                
                            </td>
                            <td class="p-4 sm:p-5 text-right flex items-center justify-end gap-2">
                                <button @click="$dispatch('open-view-batches-{{ $medicine->id }}')" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-amber-50 hover:bg-amber-100 text-amber-700 text-xs font-bold rounded-lg transition-colors border border-amber-200 shadow-sm" title="View Batches">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                                    Batches ({{ $medicine->batches->count() }})
                                </button>
                                <button @click="$dispatch('open-add-stock-{{ $medicine->id }}')" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-blue-50 hover:bg-blue-100 text-blue-700 text-xs font-bold rounded-lg transition-colors border border-blue-200 shadow-sm" title="Add Stock">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                    + Stock
                                </button>
                                <button @click="$dispatch('open-edit-medicine-{{ $medicine->id }}')" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-300 text-xs font-bold rounded-lg transition-colors border border-slate-200 dark:border-slate-600 shadow-sm" title="Edit">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="p-12 sm:p-20 text-center text-slate-500 dark:text-slate-400">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="p-4 bg-slate-50 dark:bg-slate-900/50 text-slate-400 dark:text-slate-500 rounded-full mb-5 shadow-sm border border-slate-100 dark:border-slate-800">
                                        <svg class="w-10 h-10 sm:w-12 sm:h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                                    </div>
                                    <p class="text-xl font-extrabold text-slate-800 dark:text-white mb-2">No medicines found</p>
                                    <p class="text-sm max-w-sm mx-auto">There are no records in the medicine inventory database.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        @if($medicines->hasPages() || $medicines->total() > 10)
            <div class="px-6 py-4 border-t border-slate-100 dark:border-slate-700/70 bg-slate-50 dark:bg-slate-800/30 flex flex-col sm:flex-row items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <label class="text-sm font-medium text-slate-500 dark:text-slate-400">Items per page</label>
                    <select name="per_page" form="filterForm" onchange="document.getElementById('filterForm').submit()"
                        class="rounded-lg border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-white focus:ring-emerald-500 focus:border-emerald-500 py-1.5 px-3 text-sm shadow-sm transition-all cursor-pointer">
                        <option value="10" {{ request('per_page', 10) == 10 ? 'selected' : '' }}>10</option>
                        <option value="20" {{ request('per_page') == 20 ? 'selected' : '' }}>20</option>
                        <option value="30" {{ request('per_page') == 30 ? 'selected' : '' }}>30</option>
                        <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
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

@foreach($medicines as $medicine)
<!-- Edit Medicine Modal -->
<div x-data="{ open: false }" 
     @open-edit-medicine-{{ $medicine->id }}.window="open = true" 
     x-show="open" 
     style="display: none;" 
     class="fixed inset-0 z-50 overflow-y-auto" 
     aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
        <div x-show="open" 
             x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" 
             x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" 
             class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm transition-opacity" 
             @click="open = false" aria-hidden="true"></div>

        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div x-show="open" 
             x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" 
             x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
             class="inline-block align-bottom bg-white dark:bg-slate-800 rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md w-full border border-slate-200 dark:border-slate-700">
            <div class="px-6 py-5 border-b border-slate-100 dark:border-slate-700 flex justify-between items-center bg-slate-50/50 dark:bg-slate-800/50">
                <div>
                    <h3 class="text-lg font-extrabold text-slate-900 dark:text-white" id="modal-title">Edit Medicine</h3>
                </div>
                <button type="button" @click="open = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-300">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            <div class="p-6">
                <form action="{{ route('pharmacy.medicines.update', $medicine->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="space-y-4">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1">Brand Name <span class="text-red-500">*</span></label>
                                <input type="text" name="name" value="{{ $medicine->name }}" required class="w-full rounded-xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900/50 text-slate-900 dark:text-white px-4 py-2.5 shadow-sm focus:border-emerald-500 focus:ring-emerald-500/20 transition-all duration-200 placeholder:text-slate-400">
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1">Generic Name</label>
                                <input type="text" name="generic_name" value="{{ $medicine->generic_name }}" class="w-full rounded-xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900/50 text-slate-900 dark:text-white px-4 py-2.5 shadow-sm focus:border-emerald-500 focus:ring-emerald-500/20 transition-all duration-200 placeholder:text-slate-400">
                            </div>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1">Category</label>
                                <select name="category" class="w-full rounded-xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900/50 text-slate-900 dark:text-white px-4 py-2.5 shadow-sm focus:border-emerald-500 focus:ring-emerald-500/20 transition-all duration-200">
                                    <option value="">Select Category...</option>
                                    @foreach(['Analgesic', 'Antibiotic', 'Antihistamine', 'Antipyretic', 'Vitamins', 'Supplement', 'Other'] as $cat)
                                        <option value="{{ $cat }}" {{ $medicine->category == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1">Form</label>
                                <select name="form" class="w-full rounded-xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900/50 text-slate-900 dark:text-white px-4 py-2.5 shadow-sm focus:border-emerald-500 focus:ring-emerald-500/20 transition-all duration-200">
                                    <option value="">Select Form...</option>
                                    @foreach(['Tablet', 'Capsule', 'Syrup', 'Suspension', 'Drops', 'Ointment', 'Cream', 'Injection', 'Other'] as $f)
                                        <option value="{{ $f }}" {{ $medicine->form == $f ? 'selected' : '' }}>{{ $f }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1">Unit</label>
                                <select name="unit" class="w-full rounded-xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900/50 text-slate-900 dark:text-white px-4 py-2.5 shadow-sm focus:border-emerald-500 focus:ring-emerald-500/20 transition-all duration-200">
                                    <option value="">Select Unit...</option>
                                    @foreach(['Piece', 'Box', 'Bottle', 'Tube', 'Vial', 'Ampoule', 'Other'] as $u)
                                        <option value="{{ $u }}" {{ $medicine->unit == $u ? 'selected' : '' }}>{{ $u }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="flex justify-end gap-3 mt-6">
                        <button type="button" @click="open = false" class="px-5 py-2.5 text-sm font-bold text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-xl transition-colors">Cancel</button>
                        <button type="submit" class="px-5 py-2.5 text-sm font-bold text-white bg-emerald-600 hover:bg-emerald-700 rounded-xl shadow-sm transition-all">Update Medicine</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Add Stock Modal -->
<div x-data="{ open: false }" 
     @open-add-stock-{{ $medicine->id }}.window="open = true" 
     x-show="open" 
     style="display: none;" 
     class="fixed inset-0 z-50 overflow-y-auto" 
     aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
        <div x-show="open" 
             x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" 
             x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" 
             class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm transition-opacity" 
             @click="open = false" aria-hidden="true"></div>

        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div x-show="open" 
             x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" 
             x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
             class="inline-block align-bottom bg-white dark:bg-slate-800 rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md w-full border border-slate-200 dark:border-slate-700">
            <div class="px-6 py-5 border-b border-slate-100 dark:border-slate-700 flex justify-between items-center bg-slate-50/50 dark:bg-slate-800/50">
                <div>
                    <h3 class="text-lg font-extrabold text-slate-900 dark:text-white">Add Stock for {{ $medicine->name }}</h3>
                </div>
                <button type="button" @click="open = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-300">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            <div class="p-6">
                <form action="{{ route('pharmacy.medicines.add-stock', $medicine->id) }}" method="POST">
                    @csrf
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1">Batch Number <span class="text-red-500">*</span></label>
                            <input type="text" name="batch_number" required class="w-full rounded-xl border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1">Expiration Date <span class="text-red-500">*</span></label>
                            <input type="date" name="expiration_date" required min="{{ date('Y-m-d') }}" class="w-full rounded-xl border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1">Quantity <span class="text-red-500">*</span></label>
                            <input type="number" name="quantity" required min="1" class="w-full rounded-xl border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                    </div>
                    <div class="flex justify-end gap-3 mt-6">
                        <button type="button" @click="open = false" class="px-5 py-2.5 text-sm font-bold text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-xl transition-colors">Cancel</button>
                        <button type="submit" class="px-5 py-2.5 text-sm font-bold text-white bg-blue-600 hover:bg-blue-700 rounded-xl shadow-sm transition-all">Save Stock</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- View Batches Modal -->
<div x-data="{ open: false }" 
     @open-view-batches-{{ $medicine->id }}.window="open = true" 
     x-show="open" 
     style="display: none;" 
     class="fixed inset-0 z-50 overflow-y-auto" 
     aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
        <div x-show="open" 
             x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" 
             x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" 
             class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm transition-opacity" 
             @click="open = false" aria-hidden="true"></div>

        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div x-show="open" 
             x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" 
             x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
             class="inline-block align-bottom bg-white dark:bg-slate-800 rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full border border-slate-200 dark:border-slate-700">
            <div class="px-6 py-5 border-b border-slate-100 dark:border-slate-700 flex justify-between items-center bg-slate-50/50 dark:bg-slate-800/50">
                <div>
                    <h3 class="text-lg font-extrabold text-slate-900 dark:text-white">Active Batches for {{ $medicine->name }}</h3>
                </div>
                <button type="button" @click="open = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-300">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            <div class="p-6">
                @if($medicine->batches->count() > 0)
                    <div class="overflow-hidden rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-slate-50 dark:bg-slate-800/50 border-b border-slate-100 dark:border-slate-700 text-xs uppercase tracking-wider text-slate-500 dark:text-slate-400 font-bold">
                                    <th class="p-3">Batch Number</th>
                                    <th class="p-3 text-center">Quantity</th>
                                    <th class="p-3 text-right">Expiration Date</th>
                                    <th class="p-3 text-right">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-slate-700/50 text-sm">
                                @foreach($medicine->batches as $batch)
                                    @php
                                        $isExpired = $batch->expiration_date < now();
                                        $isExpiringSoon = $batch->expiration_date->diffInDays(now()) <= 30 && !$isExpired;
                                    @endphp
                                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/80 transition-colors">
                                        <td class="p-3 font-medium text-slate-800 dark:text-slate-200">
                                            {{ $batch->batch_number ?? 'N/A' }}
                                        </td>
                                        <td class="p-3 text-center font-bold text-slate-700 dark:text-slate-300">
                                            {{ $batch->quantity }}
                                        </td>
                                        <td class="p-3 text-right font-medium text-slate-600 dark:text-slate-400">
                                            {{ $batch->expiration_date->format('M d, Y') }}
                                        </td>
                                        <td class="p-3 text-right">
                                            @if($isExpired)
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-rose-100 text-rose-700 dark:bg-rose-900/30 dark:text-rose-400 uppercase">Expired</span>
                                            @elseif($isExpiringSoon)
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400 uppercase">Near Expiry</span>
                                            @else
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400 uppercase">Good</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-8">
                        <p class="text-slate-500 dark:text-slate-400 font-medium">No active batches available.</p>
                    </div>
                @endif
                <div class="flex justify-end mt-6">
                    <button type="button" @click="open = false" class="px-5 py-2.5 text-sm font-bold text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-xl transition-colors">Close</button>
                </div>
            </div>
        </div>
    </div>
</div>
@endforeach

@endsection

<!-- Add New Medicine Modal -->
<div x-data="{ open: false }" 
     @open-add-medicine.window="open = true" 
     x-show="open" 
     style="display: none;" 
     class="fixed inset-0 z-50 overflow-y-auto" 
     aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
        <div x-show="open" 
             x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" 
             x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" 
             class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm transition-opacity" 
             @click="open = false" aria-hidden="true"></div>

        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div x-show="open" 
             x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" 
             x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
             class="inline-block align-bottom bg-white dark:bg-slate-800 rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md w-full border border-slate-200 dark:border-slate-700">
            <div class="px-6 py-5 border-b border-slate-100 dark:border-slate-700 flex justify-between items-center bg-slate-50/50 dark:bg-slate-800/50">
                <div>
                    <h3 class="text-lg font-extrabold text-slate-900 dark:text-white">Add New Medicine</h3>
                </div>
                <button type="button" @click="open = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-300">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            <div class="p-6">
                <form action="{{ route('pharmacy.medicines.store') }}" method="POST">
                    @csrf
                    <div class="space-y-5">
                        <!-- Medicine Details -->
                        <div class="p-4 bg-slate-50 dark:bg-slate-800/50 rounded-2xl border border-slate-100 dark:border-slate-700/50">
                            <h4 class="text-xs font-black uppercase text-slate-400 mb-3 tracking-wider">Medicine Information</h4>
                            <div class="space-y-4">
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1">Brand Name <span class="text-red-500">*</span></label>
                                        <input type="text" name="name" required placeholder="e.g. Biogesic" class="w-full rounded-xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900/50 text-slate-900 dark:text-white px-4 py-2.5 shadow-sm focus:border-emerald-500 focus:ring-emerald-500/20 transition-all duration-200 placeholder:text-slate-400">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1">Generic Name</label>
                                        <input type="text" name="generic_name" placeholder="e.g. Paracetamol" class="w-full rounded-xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900/50 text-slate-900 dark:text-white px-4 py-2.5 shadow-sm focus:border-emerald-500 focus:ring-emerald-500/20 transition-all duration-200 placeholder:text-slate-400">
                                    </div>
                                </div>
                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                                    <div>
                                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1">Category</label>
                                        <select name="category" class="w-full rounded-xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900/50 text-slate-900 dark:text-white px-4 py-2.5 shadow-sm focus:border-emerald-500 focus:ring-emerald-500/20 transition-all duration-200">
                                            <option value="">Select Category...</option>
                                            @foreach(['Analgesic', 'Antibiotic', 'Antihistamine', 'Antipyretic', 'Vitamins', 'Supplement', 'Other'] as $cat)
                                                <option value="{{ $cat }}">{{ $cat }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1">Form</label>
                                        <select name="form" class="w-full rounded-xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900/50 text-slate-900 dark:text-white px-4 py-2.5 shadow-sm focus:border-emerald-500 focus:ring-emerald-500/20 transition-all duration-200">
                                            <option value="">Select Form...</option>
                                            @foreach(['Tablet', 'Capsule', 'Syrup', 'Suspension', 'Drops', 'Ointment', 'Cream', 'Injection', 'Other'] as $f)
                                                <option value="{{ $f }}">{{ $f }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1">Unit</label>
                                        <select name="unit" class="w-full rounded-xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900/50 text-slate-900 dark:text-white px-4 py-2.5 shadow-sm focus:border-emerald-500 focus:ring-emerald-500/20 transition-all duration-200">
                                            <option value="">Select Unit...</option>
                                            @foreach(['Piece', 'Box', 'Bottle', 'Tube', 'Vial', 'Ampoule', 'Other'] as $u)
                                                <option value="{{ $u }}">{{ $u }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Initial Stock Details -->
                        <div class="p-4 bg-emerald-50/50 dark:bg-emerald-900/10 rounded-2xl border border-emerald-100 dark:border-emerald-800/30">
                            <h4 class="text-xs font-black uppercase text-emerald-600 dark:text-emerald-500 mb-3 tracking-wider">Initial Stock (Required)</h4>
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1">Batch Number <span class="text-red-500">*</span></label>
                                    <input type="text" name="batch_number" required placeholder="Enter batch or lot number" class="w-full rounded-xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900/50 text-slate-900 dark:text-white px-4 py-2.5 shadow-sm focus:border-emerald-500 focus:ring-emerald-500/20 transition-all duration-200 placeholder:text-slate-400">
                                </div>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1">Expiration Date <span class="text-red-500">*</span></label>
                                        <input type="date" name="expiration_date" required min="{{ date('Y-m-d') }}" class="w-full rounded-xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900/50 text-slate-900 dark:text-white px-4 py-2.5 shadow-sm focus:border-emerald-500 focus:ring-emerald-500/20 transition-all duration-200 placeholder:text-slate-400">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1">Quantity <span class="text-red-500">*</span></label>
                                        <input type="number" name="quantity" required min="1" placeholder="e.g. 50" class="w-full rounded-xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900/50 text-slate-900 dark:text-white px-4 py-2.5 shadow-sm focus:border-emerald-500 focus:ring-emerald-500/20 transition-all duration-200 placeholder:text-slate-400">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="flex justify-end gap-3 mt-6">
                        <button type="button" @click="open = false" class="px-5 py-2.5 text-sm font-bold text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-xl transition-colors">Cancel</button>
                        <button type="submit" class="px-5 py-2.5 text-sm font-bold text-white bg-emerald-600 hover:bg-emerald-700 rounded-xl shadow-sm transition-all">Save Medicine</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
