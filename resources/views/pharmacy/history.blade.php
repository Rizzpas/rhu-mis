@extends('layouts.pharmacy')

@section('header', 'Prescription History')

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
            <span class="text-emerald-700 dark:text-emerald-400 font-extrabold">Prescription History</span>
        </div>
        <div class="hidden sm:flex items-center gap-2 text-[11px] font-semibold text-slate-400 dark:text-slate-500">
            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
            <span>Permanent Clinical Log</span>
        </div>
    </nav>

    <!-- Header Section -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pb-2 border-b border-slate-200/80 dark:border-slate-800 print:hidden">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-emerald-100 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800/60">
                    <svg class="w-3 h-3 text-emerald-600 dark:text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" /></svg>
                    Clinical Audit Trail
                </span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-black text-slate-900 dark:text-white tracking-tight flex items-center gap-2.5">
                <span>Prescription History &amp; Dispensing Log</span>
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-1 max-w-2xl">
                Comprehensive archive of fulfilled and closed prescription orders, dispensing timestamps, and clinician records.
            </p>
        </div>

        <div class="flex items-center gap-2.5 shrink-0">
            <a href="{{ route('pharmacy.dashboard') }}" 
                class="h-10 px-4 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 hover:bg-slate-50 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-200 text-xs font-bold transition-all shadow-xs flex items-center gap-2 cursor-pointer active:scale-95">
                <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                <span>Active Queue</span>
            </a>
            <a href="{{ route('pharmacy.medicines') }}" 
                class="h-10 px-4 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white text-xs font-bold shadow-md shadow-emerald-600/20 transition-all flex items-center gap-2 active:scale-95 cursor-pointer shrink-0">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                </svg>
                <span>Formulary &amp; Stock</span>
            </a>
        </div>
    </div>

    <!-- Quick Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 print:hidden">
        <div class="bg-white dark:bg-slate-900 rounded-3xl p-5 border border-slate-200/90 dark:border-slate-800 shadow-xs flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-emerald-100/80 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center border border-emerald-500/20 shadow-xs shrink-0">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
            </div>
            <div>
                <p class="text-[11px] font-extrabold uppercase tracking-wider text-slate-400 dark:text-slate-500">Total Historical Orders</p>
                <p class="text-2xl sm:text-3xl font-black text-slate-900 dark:text-white mt-0.5 tracking-tight">{{ number_format($prescriptions->total()) }}</p>
                <p class="text-xs text-slate-500 dark:text-slate-400 font-medium">Logged &amp; preserved</p>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-900 rounded-3xl p-5 border border-slate-200/90 dark:border-slate-800 shadow-xs flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-teal-100/80 dark:bg-teal-950/60 text-teal-600 dark:text-teal-400 flex items-center justify-center border border-teal-500/20 shadow-xs shrink-0">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" /></svg>
            </div>
            <div>
                <p class="text-[11px] font-extrabold uppercase tracking-wider text-slate-400 dark:text-slate-500">Record Classification</p>
                <p class="text-2xl sm:text-3xl font-black text-slate-900 dark:text-white mt-0.5 tracking-tight">Closed</p>
                <p class="text-xs text-slate-500 dark:text-slate-400 font-medium">Dispensed, Cancelled or Expired</p>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-900 rounded-3xl p-5 border border-slate-200/90 dark:border-slate-800 shadow-xs flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-sky-100/80 dark:bg-sky-950/60 text-sky-600 dark:text-sky-400 flex items-center justify-center border border-sky-500/20 shadow-xs shrink-0">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" /></svg>
            </div>
            <div>
                <p class="text-[11px] font-extrabold uppercase tracking-wider text-slate-400 dark:text-slate-500">Audit Compliance</p>
                <p class="text-2xl sm:text-3xl font-black text-emerald-600 dark:text-emerald-400 mt-0.5 tracking-tight">100%</p>
                <p class="text-xs text-slate-500 dark:text-slate-400 font-medium">Traceable to practitioner &amp; patient</p>
            </div>
        </div>
    </div>

    <!-- Search & Filter Toolbar -->
    <div class="bg-white/95 dark:bg-slate-900/95 backdrop-blur-md rounded-3xl border border-slate-200/90 dark:border-slate-800 p-5 sm:p-6 shadow-xs print:hidden">
        <form id="filterForm" action="{{ route('pharmacy.history') }}" method="GET" class="flex flex-col sm:flex-row gap-3.5 items-center justify-between">
            <div class="relative flex-1 w-full">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400 dark:text-slate-500">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                </div>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by prescription order #, patient name, or patient ID..." 
                    oninput="clearTimeout(this.timer); this.timer = setTimeout(() => { this.form.submit(); }, 350);"
                    onkeydown="if(event.key === 'Enter') { clearTimeout(this.timer); this.form.submit(); } else if(event.key === 'Escape') { this.value = ''; this.form.submit(); }"
                    class="no-uppercase h-11 sm:h-12 pl-10 pr-10 block w-full rounded-xl border border-slate-300/80 dark:border-slate-700 bg-slate-50/70 dark:bg-slate-800/60 text-slate-900 dark:text-white text-sm font-semibold focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 shadow-xs transition-all placeholder:text-slate-400 placeholder:font-normal"
                    style="text-transform: none !important;">
                @if(request('search'))
                    <button type="button" onclick="const input = this.previousElementSibling; input.value = ''; input.form.submit();" class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition-colors cursor-pointer" title="Clear Search">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                @endif
            </div>

            @if(request('search'))
                <a href="{{ route('pharmacy.history') }}" class="h-11 sm:h-12 px-4 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-300 text-xs font-bold transition-colors flex items-center gap-1.5 shadow-xs shrink-0 cursor-pointer">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    <span>Reset Filter</span>
                </a>
            @endif

            <input type="hidden" name="per_page" id="history_per_page" value="{{ request('per_page', 10) }}">
        </form>
    </div>

    <!-- History Table Container -->
    <div class="bg-white dark:bg-slate-900 rounded-3xl shadow-xs border border-slate-200/90 dark:border-slate-800 overflow-hidden">
        <div class="px-6 py-5 border-b border-slate-100 dark:border-slate-800/80 bg-slate-50/70 dark:bg-slate-900/60 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div>
                <h3 class="text-base sm:text-lg font-bold text-slate-900 dark:text-white">Historical Prescription Archive</h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Showing {{ $prescriptions->count() }} of {{ number_format($prescriptions->total()) }} closed orders</p>
            </div>
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-extrabold bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-700 shadow-xs">
                <span>Page {{ $prescriptions->currentPage() }} of {{ max(1, $prescriptions->lastPage()) }}</span>
            </span>
        </div>

        <div class="overflow-x-auto custom-scrollbar">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/90 dark:bg-slate-950/80 text-slate-500 dark:text-slate-400 text-[11px] font-extrabold uppercase tracking-wider border-b border-slate-200/80 dark:border-slate-800">
                        <th class="py-4 px-5 sm:px-6 w-[20%]">Prescription Order</th>
                        <th class="py-4 px-5 sm:px-6 flex-1">Citizen / Patient</th>
                        <th class="py-4 px-5 sm:px-6 w-[24%]">Attending Clinician</th>
                        <th class="py-4 px-5 sm:px-6 w-[18%]">Fulfillment Status</th>
                        <th class="py-4 px-5 sm:px-6 w-[16%] text-right print:hidden min-w-[140px]">Details</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800 text-sm">
                    @forelse($prescriptions as $request)
                        <tr class="hover:bg-emerald-50/30 dark:hover:bg-slate-800/40 transition-colors group">
                            <!-- Order ID & Timestamp -->
                            <td class="py-5 px-5 sm:px-6 align-top">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-2xl bg-emerald-100/80 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 font-black flex items-center justify-center border border-emerald-500/20 shadow-xs shrink-0 text-xs">
                                        RX
                                    </div>
                                    <div>
                                        <p class="font-extrabold text-slate-900 dark:text-white text-sm">#{{ $request->id }}</p>
                                        <p class="text-[11px] font-semibold text-slate-500 dark:text-slate-400 mt-0.5">{{ $request->updated_at->format('M d, Y') }}</p>
                                        <span class="text-[10px] text-slate-400 dark:text-slate-500 font-medium">{{ $request->updated_at->format('h:i A') }}</span>
                                    </div>
                                </div>
                            </td>

                            <!-- Patient Details -->
                            <td class="py-5 px-5 sm:px-6 align-top">
                                <p class="font-bold text-slate-900 dark:text-white text-sm sm:text-base tracking-tight group-hover:text-emerald-600 dark:group-hover:text-emerald-400 transition-colors">
                                    {{ $request->patient->full_name ?? 'Unknown Citizen' }}
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

                            <!-- Clinician -->
                            <td class="py-5 px-5 sm:px-6 align-top">
                                <p class="text-sm font-bold text-slate-800 dark:text-slate-200">{{ $request->doctor->formatted_name ?? 'Attending Clinician' }}</p>
                                <div class="flex items-center gap-1.5 mt-1">
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[11px] font-bold bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-700">
                                        <svg class="w-3 h-3 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                                        <span>{{ $request->items->count() }} item(s) ordered</span>
                                    </span>
                                </div>
                            </td>

                            <!-- Status -->
                            <td class="py-5 px-5 sm:px-6 align-top">
                                @php
                                    $statusMap = [
                                        'dispensed' => ['bg-emerald-100 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300 border-emerald-200 dark:border-emerald-800/60', 'Dispensed'],
                                        'partially_dispensed' => ['bg-amber-100 text-amber-800 dark:bg-amber-950/60 dark:text-amber-300 border-amber-200 dark:border-amber-800/60', 'Partially Dispensed'],
                                        'cancelled' => ['bg-rose-100 text-rose-800 dark:bg-rose-950/60 dark:text-rose-300 border-rose-200 dark:border-rose-800/60', 'Cancelled'],
                                        'expired' => ['bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300 border-slate-200 dark:border-slate-700', 'Expired'],
                                    ];
                                    $statusInfo = $statusMap[$request->status] ?? ['bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300 border-slate-200 dark:border-slate-700', ucfirst($request->status)];
                                @endphp
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider {{ $statusInfo[0] }} border shadow-xs">
                                    @if($request->status === 'dispensed')
                                        <svg class="w-3 h-3 text-emerald-600 dark:text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                    @elseif($request->status === 'partially_dispensed')
                                        <svg class="w-3 h-3 text-amber-600 dark:text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                                    @elseif($request->status === 'cancelled')
                                        <svg class="w-3 h-3 text-rose-600 dark:text-rose-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                                    @else
                                        <svg class="w-3 h-3 text-slate-500 dark:text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    @endif
                                    {{ $statusInfo[1] }}
                                </span>
                            </td>

                            <!-- Details -->
                            <td class="py-5 px-5 sm:px-6 align-top text-right print:hidden whitespace-nowrap min-w-[140px]">
                                <button onclick="openViewModal({{ $request->id }})" 
                                    class="h-9 px-3.5 rounded-xl inline-flex items-center gap-1.5 text-xs font-bold text-slate-700 dark:text-slate-300 bg-slate-100 dark:bg-slate-800 hover:bg-emerald-50 dark:hover:bg-emerald-950/60 hover:text-emerald-700 dark:hover:text-emerald-300 border border-slate-200 dark:border-slate-700 hover:border-emerald-300 dark:hover:border-emerald-700 shadow-xs transition-all cursor-pointer active:scale-95 shrink-0">
                                    <span>Inspect Order</span>
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-16 px-6 text-center text-slate-500 dark:text-slate-400">
                                <div class="w-16 h-16 rounded-3xl bg-slate-100 dark:bg-slate-800 text-slate-400 dark:text-slate-500 mx-auto flex items-center justify-center mb-4 shadow-xs border border-slate-200/80 dark:border-slate-700">
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                </div>
                                <h3 class="text-base font-extrabold text-slate-900 dark:text-white mb-1">No Historical Prescriptions</h3>
                                <p class="text-xs text-slate-500 dark:text-slate-400 max-w-sm mx-auto leading-relaxed">
                                    No closed prescription records matched your query. Try clearing the search filter.
                                </p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination Controls -->
        @if($prescriptions->hasPages() || $prescriptions->total() > 10)
            <div class="px-6 py-4 border-t border-slate-100 dark:border-slate-800/80 bg-slate-50/70 dark:bg-slate-900/60 flex flex-col sm:flex-row items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <label class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Per Page</label>
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
                        @change="document.getElementById('history_per_page').value = $event.detail; document.getElementById('filterForm').submit()"
                    />
                </div>
                
                <div class="w-full sm:w-auto">
                    @if($prescriptions->hasPages())
                        {{ $prescriptions->appends(request()->query())->links('vendor.pagination.shadcn') }}
                    @endif
                </div>
            </div>
        @endif
    </div>
</div>

{{-- Inspect Details Modals --}}
@foreach($prescriptions as $request)
    <div id="viewModal-{{ $request->id }}" class="fixed inset-0 z-[999] hidden bg-slate-950/70 backdrop-blur-sm overflow-y-auto w-full h-full text-left">
        <div class="flex items-center justify-center min-h-screen px-4 py-8">
            <div class="bg-white dark:bg-slate-900 rounded-3xl shadow-2xl overflow-hidden border border-slate-200/90 dark:border-slate-800 max-w-xl w-full"
                 onclick="event.stopPropagation()">
                <div class="px-6 py-5 border-b border-slate-200 dark:border-slate-800 bg-slate-50/80 dark:bg-slate-800/80">
                    <div class="flex justify-between items-start mb-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-2xl bg-emerald-100 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center border border-emerald-500/20 shadow-xs shrink-0">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            </div>
                            <div>
                                <h3 class="text-base font-bold text-slate-900 dark:text-white">Prescription Order #{{ $request->id }}</h3>
                                <p class="text-xs text-slate-500 dark:text-slate-400">Processed on {{ $request->updated_at->format('M d, Y h:i A') }}</p>
                            </div>
                        </div>
                        <button onclick="closeViewModal({{ $request->id }})" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 p-1.5 rounded-xl hover:bg-slate-200 dark:hover:bg-slate-700 transition-colors cursor-pointer">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>

                    <!-- Patient & Clinician Metadata -->
                    <div class="grid grid-cols-2 gap-4 pt-3.5 border-t border-slate-200/90 dark:border-slate-700/80">
                        <div>
                            <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block">Patient</span>
                            <span class="font-bold text-slate-900 dark:text-white text-sm block mt-0.5">{{ $request->patient->full_name ?? 'Unknown' }}</span>
                            <span class="text-xs text-slate-500 dark:text-slate-400 font-mono block mt-0.5">ID: {{ $request->patient->patient_id ?? '—' }}</span>
                        </div>
                        <div>
                            <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block">Attending Clinician</span>
                            <span class="font-bold text-slate-900 dark:text-white text-sm block mt-0.5">{{ $request->doctor->formatted_name ?? 'Clinician' }}</span>
                            <span class="text-xs text-slate-500 dark:text-slate-400 block mt-0.5">Status: {{ ucfirst(str_replace('_', ' ', $request->status)) }}</span>
                        </div>
                    </div>
                </div>

                <div class="p-6">
                    <!-- Dispensed Items List -->
                    <div>
                        <h4 class="text-xs font-black uppercase text-slate-400 mb-3 tracking-wider">Ordered Medications &amp; Instructions</h4>
                        <ul class="space-y-2.5">
                            @foreach($request->items as $item)
                                <li class="bg-white dark:bg-slate-800/80 p-4 rounded-2xl border border-slate-200/90 dark:border-slate-800 shadow-xs flex items-start gap-3">
                                    <div class="w-8 h-8 rounded-xl bg-emerald-100/80 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0 mt-0.5">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <div class="flex items-center justify-between gap-2">
                                            <p class="font-bold text-slate-900 dark:text-white text-sm">{{ $item->medicine_name }}</p>
                                            <span class="text-xs font-black px-2.5 py-0.5 rounded-full bg-emerald-100 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800/60 shrink-0">
                                                Qty: {{ $item->quantity ?: 'As needed' }}
                                            </span>
                                        </div>
                                        <div class="text-xs text-slate-600 dark:text-slate-400 mt-1">
                                            <span class="font-bold text-slate-700 dark:text-slate-300">Directions (Sig):</span> {{ $item->frequency }}
                                        </div>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>

                    <div class="flex justify-end mt-6 pt-4 border-t border-slate-100 dark:border-slate-800">
                        <button type="button" onclick="closeViewModal({{ $request->id }})" 
                            class="px-5 py-2.5 text-xs font-bold text-slate-700 dark:text-slate-300 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 rounded-xl transition-colors cursor-pointer active:scale-95">
                            Close
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endforeach

@push('scripts')
<script>
    function openViewModal(id) {
        const modal = document.getElementById('viewModal-' + id);
        if (modal) {
            if (modal.parentElement !== document.body) {
                document.body.appendChild(modal);
            }
            modal.classList.remove('hidden');
        }
    }

    function closeViewModal(id) {
        const modal = document.getElementById('viewModal-' + id);
        if (modal) modal.classList.add('hidden');
    }

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            document.querySelectorAll('[id^="viewModal-"]').forEach(modal => {
                modal.classList.add('hidden');
            });
        }
    });
</script>
@endpush
@endsection
