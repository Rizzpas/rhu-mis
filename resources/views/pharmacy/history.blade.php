@extends('layouts.pharmacy')

@section('header', 'Prescription History')

@section('content')
<div class="max-w-7xl mx-auto space-y-6 pb-12">
    
    <!-- Header -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-800 p-6">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h2 class="text-2xl font-extrabold text-slate-900 dark:text-white flex items-center gap-2">
                    <span class="bg-emerald-100 dark:bg-emerald-900/50 text-emerald-600 dark:text-emerald-400 p-2 rounded-xl">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </span>
                    Historical Log
                </h2>
                <p class="text-slate-500 dark:text-slate-400 text-sm mt-1">Audit and review all past dispensed prescriptions.</p>
            </div>
            <form id="filterForm" action="{{ route('pharmacy.history') }}" method="GET" class="shrink-0 flex flex-col sm:flex-row gap-3">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                    </div>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search ID or Patient..." 
                        oninput="clearTimeout(this.timer); this.timer = setTimeout(() => { this.form.submit(); }, 500);"
                        class="pl-10 pr-4 py-2 border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 rounded-xl text-sm focus:ring-emerald-500 focus:border-emerald-500 block w-full dark:text-white shadow-sm transition-colors">
                </div>
                <!-- Hidden inputs to preserve pagination -->
                <input type="hidden" name="per_page" value="{{ request('per_page', 10) }}">
            </form>
        </div>
    </div>

    <!-- Active Consultations List -->
    <div class="pt-2">
        <div class="bg-white dark:bg-slate-800/80 backdrop-blur-sm rounded-2xl shadow-sm hover:shadow-lg border border-slate-200 dark:border-slate-700/60 overflow-hidden transition-all duration-300">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 dark:bg-slate-800/50 border-b border-slate-100 dark:border-slate-700/70 text-[10px] sm:text-xs uppercase tracking-wider text-slate-500 dark:text-slate-400 font-extrabold">
                            <th class="p-4 sm:p-5 w-[15%]">Prescription Details</th>
                            <th class="p-4 sm:p-5 flex-1">Patient Details</th>
                            <th class="p-4 sm:p-5 w-[25%]">Requested By</th>
                            <th class="p-4 sm:p-5 w-[15%]">Status</th>
                            <th class="p-4 sm:p-5 w-[20%] text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700/50 text-sm">
                        @forelse($prescriptions as $request)
                            <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-700/30 transition-colors group">
                                <td class="p-4 sm:p-5 align-top">
                                    <div class="flex items-center gap-3">
                                        <div class="shrink-0 w-10 h-10 rounded-full bg-slate-100 dark:bg-slate-700 flex items-center justify-center text-slate-600 dark:text-slate-400 font-bold border border-slate-200 dark:border-slate-600 shadow-sm">
                                            RX
                                        </div>
                                        <div>
                                            <p class="font-extrabold text-slate-900 dark:text-white text-sm">#{{ $request->id }}</p>
                                            <p class="text-[11px] font-semibold text-slate-500 dark:text-slate-400 mt-0.5">{{ $request->updated_at->format('M d, Y h:i A') }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="p-4 sm:p-5 align-top">
                                    <p class="font-bold text-slate-800 dark:text-slate-200">{{ $request->patient->full_name ?? 'Unknown' }}</p>
                                    <p class="text-xs font-medium text-slate-500 dark:text-slate-400 mt-1">{{ $request->patient->patient_id ?? '---' }}</p>
                                    <p class="text-[11px] text-slate-500 mt-0.5">{{ $request->patient->dob ? \Carbon\Carbon::parse($request->patient->dob)->age : '?' }} yrs • {{ $request->patient->sex ?? '?' }}</p>
                                </td>
                                <td class="p-4 sm:p-5 align-top">
                                    <p class="text-sm font-semibold text-slate-800 dark:text-slate-200">{{ $request->doctor->formatted_name ?? 'Unknown' }}</p>
                                    <p class="text-[11px] font-medium text-slate-500 dark:text-slate-400 mt-1">{{ $request->items->count() }} Items Prescribed</p>
                                </td>
                                <td class="p-4 sm:p-5 align-top">
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold bg-green-100 text-green-700 dark:bg-green-900/50 dark:text-green-400 border border-green-200 dark:border-green-800/60 shadow-sm">
                                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                        Dispensed
                                    </span>
                                </td>
                                <td class="p-4 sm:p-5 align-top text-right">
                                    <button onclick="openViewModal({{ $request->id }})" class="inline-flex items-center gap-2 px-3 py-1.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 text-xs font-bold rounded-lg transition-all shadow-sm border border-slate-200 dark:border-slate-600">
                                        View Details
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
                                        <p class="text-xl font-extrabold text-slate-800 dark:text-white mb-2">No historical records found</p>
                                        <p class="text-sm max-w-sm mx-auto">There are no dispensed prescriptions that match your search.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            @if($prescriptions->hasPages() || $prescriptions->total() > 10)
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
                        @if($prescriptions->hasPages())
                            {{ $prescriptions->appends(request()->query())->links('vendor.pagination.shadcn') }}
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

@foreach($prescriptions as $request)
    <!-- View Modal -->
    <div id="viewModal-{{ $request->id }}" class="fixed inset-0 z-50 hidden bg-slate-900/50 backdrop-blur-sm overflow-y-auto w-full h-full text-left">
        <div class="relative w-full max-w-2xl mx-auto top-20 p-5">
            <div class="bg-white dark:bg-slate-800 rounded-3xl shadow-2xl overflow-hidden border border-slate-200 dark:border-slate-700">
                <div class="px-6 py-5 border-b border-slate-100 dark:border-slate-700 flex justify-between items-center bg-slate-50/50 dark:bg-slate-800/50">
                    <div>
                        <h3 class="text-xl font-extrabold text-slate-900 dark:text-white">Prescription Details</h3>
                        <p class="text-sm font-medium text-slate-500 dark:text-slate-400 mt-1">Dispensed on {{ $request->updated_at->format('M d, Y h:i A') }}</p>
                    </div>
                    <button onclick="closeViewModal({{ $request->id }})" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 bg-white dark:bg-slate-900 shadow-sm border border-slate-200 dark:border-slate-700 p-2 rounded-xl transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
                <div class="p-6">
                    <div class="bg-slate-50 border border-slate-100 dark:bg-slate-900 dark:border-slate-800 rounded-xl p-4 mb-6">
                        <h4 class="font-bold text-slate-900 dark:text-white text-sm mb-2">Dispensed Items</h4>
                        <ul class="space-y-3">
                            @foreach($request->items as $item)
                                <li class="bg-white dark:bg-slate-800 p-3 rounded-lg border border-slate-200 dark:border-slate-700 shadow-sm flex items-start gap-3">
                                    <div class="shrink-0 pt-0.5">
                                        <svg class="w-5 h-5 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <p class="font-bold text-slate-900 dark:text-white text-sm">{{ $item->medicine_name }}</p>
                                        <div class="flex flex-col sm:flex-row sm:items-center gap-1 sm:gap-4 mt-1">
                                            <p class="text-xs text-slate-600 dark:text-slate-400"><span class="font-semibold">Sig:</span> {{ $item->instruction }}</p>
                                            <p class="text-xs text-slate-600 dark:text-slate-400"><span class="font-semibold">Qty Dispensed:</span> {{ $item->quantity ?: 'As needed' }}</p>
                                        </div>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>

                    <div class="flex justify-end mt-4">
                        <button type="button" onclick="closeViewModal({{ $request->id }})" class="px-5 py-2.5 text-sm font-bold text-slate-600 dark:text-slate-300 bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 dark:hover:bg-slate-600 rounded-xl transition-colors">
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
