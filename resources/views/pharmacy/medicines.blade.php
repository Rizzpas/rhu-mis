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
    </div>

    <!-- Medicines Table -->
    <div class="bg-white dark:bg-slate-800/80 backdrop-blur-sm rounded-2xl shadow-sm hover:shadow-lg border border-slate-200 dark:border-slate-700/60 overflow-hidden transition-all duration-300">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 dark:bg-slate-800/50 border-b border-slate-100 dark:border-slate-700/70 text-[10px] sm:text-xs uppercase tracking-wider text-slate-500 dark:text-slate-400 font-extrabold">
                        <th class="p-4 sm:p-5 w-[5%] text-center">#</th>
                        <th class="p-4 sm:p-5 w-[30%]">Brand Name</th>
                        <th class="p-4 sm:p-5 w-[30%]">Generic Name</th>
                        <th class="p-4 sm:p-5 w-[20%]">Form</th>
                        <th class="p-4 sm:p-5 w-[15%] text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-700/50 text-sm">
                    @forelse($medicines as $medicine)
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/80 transition-colors group">
                            <td class="p-4 sm:p-5 text-center text-slate-400 font-medium">
                                {{ $loop->iteration + ($medicines->currentPage() - 1) * $medicines->perPage() }}
                            </td>
                            <td class="p-4 sm:p-5">
                                <span class="font-bold text-slate-800 dark:text-slate-200">{{ $medicine->name }}</span>
                            </td>
                            <td class="p-4 sm:p-5 text-slate-600 dark:text-slate-300">
                                {{ $medicine->generic_name ?? 'N/A' }}
                            </td>
                            <td class="p-4 sm:p-5">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-400">
                                    {{ $medicine->form ?? 'N/A' }}
                                </span>
                            </td>
                            <td class="p-4 sm:p-5 text-center">
                                <span class="inline-flex items-center gap-1.5 text-xs font-bold text-emerald-600 dark:text-emerald-400">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                    In Stock
                                </span>
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
@endsection
