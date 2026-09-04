@extends('layouts.pharmacy')

@section('header', 'Prescription History')

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
        <span class="text-slate-700 dark:text-slate-300 font-semibold">Prescription History</span>
    </nav>

    <!-- Header Section (Content Management Style) -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 pb-4 border-b border-slate-200/80 dark:border-slate-800">
        <div>
            <h1 class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white tracking-tight flex items-center gap-2.5">
                <span class="w-9 h-9 sm:w-10 sm:h-10 rounded-2xl bg-emerald-100/80 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0 border border-emerald-500/20 shadow-2xs">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </span>
                <span>Prescription History & Dispensing Log</span>
            </h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Audit and review all past fulfilled prescriptions, dispensing timestamps, and clinician orders.</p>
        </div>

        <div class="flex items-center gap-2.5">
            <span class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 text-slate-600 dark:text-slate-400 text-xs font-semibold shadow-2xs">
                <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                <span>Permanent Clinical Record</span>
            </span>
        </div>
    </div>

    <!-- Search Toolbar -->
    <div class="bg-white/80 dark:bg-slate-900/80 backdrop-blur-md rounded-2xl border border-slate-200/80 dark:border-slate-800 p-4 sm:p-5 shadow-xs">
        <form id="filterForm" action="{{ route('pharmacy.history') }}" method="GET" class="flex flex-col sm:flex-row gap-3 items-center justify-between">
            <div class="relative flex-1 w-full">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                </div>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by prescription ID or patient name..." 
                    oninput="clearTimeout(this.timer); this.timer = setTimeout(() => { this.form.submit(); }, 400);"
                    class="h-11 pl-10 pr-4 block w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50/70 dark:bg-slate-800/60 text-slate-900 dark:text-white text-sm font-semibold focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 shadow-2xs transition-all placeholder:text-slate-400">
            </div>

            @if(request('search'))
                <a href="{{ route('pharmacy.history') }}" class="h-11 px-4 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700 text-xs font-bold transition-colors flex items-center gap-1.5 shadow-2xs shrink-0 cursor-pointer">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    <span>Clear Search</span>
                </a>
            @endif

            <input type="hidden" name="per_page" id="history_per_page" value="{{ request('per_page', 10) }}">
        </form>
    </div>

    <!-- History Table Container -->
    <div class="bg-white dark:bg-slate-900 rounded-3xl shadow-xs border border-slate-200/90 dark:border-slate-800 overflow-hidden">
        <div class="px-6 py-5 border-b border-slate-100 dark:border-slate-800/80 bg-slate-50/70 dark:bg-slate-900/60 flex items-center justify-between">
            <div>
                <h3 class="text-base font-bold text-slate-900 dark:text-white">Fulfilled Orders</h3>
                <span class="text-xs text-slate-500 dark:text-slate-400 font-medium">{{ $prescriptions->total() }} total dispensed prescription(s)</span>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/90 dark:bg-slate-950/80 text-slate-500 dark:text-slate-400 text-[11px] font-extrabold uppercase tracking-wider border-b border-slate-200/80 dark:border-slate-800">
                        <th class="p-4 sm:p-5 w-[18%]">Prescription Order</th>
                        <th class="p-4 sm:p-5 flex-1">Citizen / Patient</th>
                        <th class="p-4 sm:p-5 w-[25%]">Attending Clinician</th>
                        <th class="p-4 sm:p-5 w-[15%]">Fulfillment Status</th>
                        <th class="p-4 sm:p-5 w-[15%] text-right">Details</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800 text-sm">
                    @forelse($prescriptions as $request)
                        <tr class="hover:bg-emerald-50/30 dark:hover:bg-slate-800/40 transition-colors group">
                            <td class="p-4 sm:p-5 align-top">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-2xl bg-emerald-100/80 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 font-black flex items-center justify-center border border-emerald-500/20 shadow-2xs shrink-0 text-xs">
                                        RX
                                    </div>
                                    <div>
                                        <p class="font-extrabold text-slate-900 dark:text-white text-sm">#{{ $request->id }}</p>
                                        <p class="text-[11px] font-semibold text-slate-500 dark:text-slate-400 mt-0.5">{{ $request->updated_at->format('M d, Y h:i A') }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="p-4 sm:p-5 align-top">
                                <p class="font-bold text-slate-900 dark:text-white text-sm">{{ $request->patient->full_name ?? 'Unknown' }}</p>
                                <p class="text-xs font-mono font-medium text-slate-500 dark:text-slate-400 mt-0.5">ID: {{ $request->patient->patient_id ?? '—' }}</p>
                                <p class="text-[11px] text-slate-400 mt-0.5">{{ $request->patient->dob ? \Carbon\Carbon::parse($request->patient->dob)->age . ' yrs' : '?' }} • {{ $request->patient->sex ?? '?' }}</p>
                            </td>
                            <td class="p-4 sm:p-5 align-top">
                                <p class="text-sm font-bold text-slate-800 dark:text-slate-200">{{ $request->doctor->formatted_name ?? 'Doctor' }}</p>
                                <p class="text-xs font-medium text-slate-500 dark:text-slate-400 mt-0.5">{{ $request->items->count() }} item(s) dispensed</p>
                            </td>
                            <td class="p-4 sm:p-5 align-top">
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800/60 shadow-2xs">
                                    <svg class="w-3 h-3 text-emerald-600 dark:text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                    Dispensed
                                </span>
                            </td>
                            <td class="p-4 sm:p-5 align-top text-right">
                                <button onclick="openViewModal({{ $request->id }})" class="h-8 px-3 rounded-xl inline-flex items-center gap-1.5 text-xs font-bold text-slate-700 dark:text-slate-300 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 border border-slate-200 dark:border-slate-700 shadow-2xs transition-all cursor-pointer active:scale-95">
                                    <span>Inspect Order</span>
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="p-16 text-center text-slate-500 dark:text-slate-400">
                                <div class="w-14 h-14 rounded-2xl bg-slate-100 dark:bg-slate-800 text-slate-400 dark:text-slate-500 mx-auto flex items-center justify-center mb-3 shadow-xs">
                                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                </div>
                                <h3 class="text-base font-bold text-slate-900 dark:text-white mb-1">No historical prescriptions</h3>
                                <p class="text-xs text-slate-500 dark:text-slate-400">No dispensed records match your search criteria.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination Controls with Styled <option> Tags -->
        @if($prescriptions->hasPages() || $prescriptions->total() > 10)
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

@foreach($prescriptions as $request)
    <!-- View Details Modal (Content Management Style) -->
    <div id="viewModal-{{ $request->id }}" class="fixed inset-0 z-[999] hidden bg-slate-950/70 backdrop-blur-sm overflow-y-auto w-full h-full text-left">
        <div class="flex items-center justify-center min-h-screen px-4 py-8">
            <div class="bg-white dark:bg-slate-900 rounded-3xl shadow-2xl overflow-hidden border border-slate-200/90 dark:border-slate-800 max-w-xl w-full">
                <div class="px-6 py-5 border-b border-slate-100 dark:border-slate-800 flex justify-between items-center bg-slate-50/70 dark:bg-slate-900/60">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-emerald-100/80 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center border border-emerald-500/20 shadow-2xs">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-slate-900 dark:text-white">Prescription #{{ $request->id }}</h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400">Dispensed on {{ $request->updated_at->format('M d, Y h:i A') }}</p>
                        </div>
                    </div>
                    <button onclick="closeViewModal({{ $request->id }})" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 p-1.5 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
                <div class="p-6 sm:p-7">
                    <!-- Patient & Doctor Card -->
                    <div class="grid grid-cols-2 gap-4 p-4 rounded-2xl bg-slate-50/80 dark:bg-slate-800/40 border border-slate-200/80 dark:border-slate-800 mb-6">
                        <div>
                            <p class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">Patient</p>
                            <p class="font-bold text-slate-900 dark:text-white text-sm mt-0.5">{{ $request->patient->full_name ?? 'Unknown' }}</p>
                            <p class="text-xs text-slate-500 dark:text-slate-400 font-mono mt-0.5">ID: {{ $request->patient->patient_id ?? '—' }}</p>
                        </div>
                        <div>
                            <p class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">Prescribing Doctor</p>
                            <p class="font-bold text-slate-900 dark:text-white text-sm mt-0.5">{{ $request->doctor->formatted_name ?? 'Clinician' }}</p>
                        </div>
                    </div>

                    <!-- Dispensed Items List -->
                    <div>
                        <h4 class="text-xs font-black uppercase text-slate-400 mb-3 tracking-wider">Dispensed Medicines & Instructions</h4>
                        <ul class="space-y-2.5">
                            @foreach($request->items as $item)
                                <li class="bg-white dark:bg-slate-800/80 p-4 rounded-2xl border border-slate-200/90 dark:border-slate-800 shadow-2xs flex items-start gap-3">
                                    <div class="w-8 h-8 rounded-xl bg-emerald-100/80 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0 mt-0.5">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <div class="flex items-center justify-between">
                                            <p class="font-bold text-slate-900 dark:text-white text-sm">{{ $item->medicine_name }}</p>
                                            <span class="text-xs font-black px-2.5 py-0.5 rounded-full bg-emerald-100 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800/60">
                                                Qty: {{ $item->quantity ?: 'As needed' }}
                                            </span>
                                        </div>
                                        <div class="text-xs text-slate-600 dark:text-slate-400 mt-1">
                                            <span class="font-bold text-slate-700 dark:text-slate-300">Directions (Sig):</span> {{ $item->instruction }}
                                        </div>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>

                    <div class="flex justify-end mt-6 pt-4 border-t border-slate-100 dark:border-slate-800">
                        <button type="button" onclick="closeViewModal({{ $request->id }})" class="px-5 py-2.5 text-xs font-bold text-slate-700 dark:text-slate-300 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 rounded-xl transition-colors cursor-pointer">
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
        document.getElementById('viewModal-' + id).classList.remove('hidden');
    }

    function closeViewModal(id) {
        document.getElementById('viewModal-' + id).classList.add('hidden');
    }
</script>
@endpush
@endsection
