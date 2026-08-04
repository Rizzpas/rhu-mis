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
                    <button onclick="openAddMedicineModal()" class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-bold rounded-xl transition-all shadow-sm hover:-translate-y-0.5">
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
    <!-- Expiration Warning Alert Banner -->
    @if(($expiredBatchesCount ?? 0) > 0 || ($expiringSoonCount ?? 0) > 0 || ($expiring60Count ?? 0) > 0)
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            @if(($expiredBatchesCount ?? 0) > 0)
                <div class="bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800/60 p-4 rounded-2xl flex items-center justify-between shadow-sm">
                    <div class="flex items-center gap-3">
                        <div class="p-2.5 bg-rose-500 text-white rounded-xl font-black text-sm">🔴</div>
                        <div>
                            <p class="text-xs font-bold text-rose-800 dark:text-rose-300 uppercase tracking-wider">Expired Batches</p>
                            <p class="text-xl font-extrabold text-rose-900 dark:text-rose-200">{{ $expiredBatchesCount }} Batch(es)</p>
                        </div>
                    </div>
                    <span class="text-[10px] font-extrabold bg-rose-200 dark:bg-rose-900 text-rose-800 dark:text-rose-200 px-2 py-1 rounded-full uppercase">Action Required</span>
                </div>
            @endif
            @if(($expiringSoonCount ?? 0) > 0)
                <div class="bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-800/60 p-4 rounded-2xl flex items-center justify-between shadow-sm">
                    <div class="flex items-center gap-3">
                        <div class="p-2.5 bg-amber-500 text-white rounded-xl font-black text-sm">🟡</div>
                        <div>
                            <p class="text-xs font-bold text-amber-800 dark:text-amber-300 uppercase tracking-wider">Expiring within 30 Days</p>
                            <p class="text-xl font-extrabold text-amber-900 dark:text-amber-200">{{ $expiringSoonCount }} Batch(es)</p>
                        </div>
                    </div>
                    <span class="text-[10px] font-extrabold bg-amber-200 dark:bg-amber-900 text-amber-800 dark:text-amber-200 px-2 py-1 rounded-full uppercase">Near Expiry</span>
                </div>
            @endif
            @if(($expiring60Count ?? 0) > 0)
                <div class="bg-blue-50 dark:bg-blue-950/40 border border-blue-200 dark:border-blue-800/60 p-4 rounded-2xl flex items-center justify-between shadow-sm">
                    <div class="flex items-center gap-3">
                        <div class="p-2.5 bg-blue-500 text-white rounded-xl font-black text-sm">🔵</div>
                        <div>
                            <p class="text-xs font-bold text-blue-800 dark:text-blue-300 uppercase tracking-wider">Expiring in 60 Days</p>
                            <p class="text-xl font-extrabold text-blue-900 dark:text-blue-200">{{ $expiring60Count }} Batch(es)</p>
                        </div>
                    </div>
                    <span class="text-[10px] font-extrabold bg-blue-200 dark:bg-blue-900 text-blue-800 dark:text-blue-200 px-2 py-1 rounded-full uppercase">Monitor</span>
                </div>
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
                        @endphp
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
                                @if($totalStock > 20)
                                    <span class="inline-flex items-center gap-1.5 text-xs font-bold text-emerald-600 dark:text-emerald-400">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                        {{ $totalStock }} available
                                    </span>
                                @elseif($totalStock > 0)
                                    <span class="inline-flex items-center gap-1.5 text-xs font-bold text-amber-600 dark:text-amber-400">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                        Low Stock ({{ $totalStock }})
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 text-xs font-bold text-red-600 dark:text-red-400">
                                        <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>
                                        Out of Stock
                                    </span>
                                @endif
                                
                                @if($medicine->batches->count() > 0)
                                    @php
                                        $earliestBatch = $medicine->batches->first();
                                        $isExpiringSoon = $earliestBatch->expiration_date->diffInDays(now()) <= 30 && $earliestBatch->expiration_date >= now();
                                        $isExpired = $earliestBatch->expiration_date < now();
                                    @endphp
                                    <div class="mt-1 flex flex-col items-center">
                                        <p class="text-[10px] text-slate-400">Earliest Exp: {{ $earliestBatch->expiration_date->format('M Y') }}</p>
                                        @if($isExpired)
                                            <span class="text-[9px] font-bold text-red-500 uppercase tracking-wider mt-0.5">Expired</span>
                                        @elseif($isExpiringSoon)
                                            <span class="text-[9px] font-bold text-amber-500 uppercase tracking-wider mt-0.5 animate-pulse">Expiring Soon</span>
                                        @endif
                                    </div>
                                @endif
                            </td>
                            <td class="p-4 sm:p-5 text-right flex items-center justify-end gap-2">
                                <button onclick="openEditMedicineModal({{ $medicine->id }})" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-300 text-xs font-bold rounded-lg transition-colors border border-slate-200 dark:border-slate-600">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                    Edit
                                </button>
                                <button onclick="openAddStockModal({{ $medicine->id }})" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-blue-50 hover:bg-blue-100 text-blue-700 text-xs font-bold rounded-lg transition-colors border border-blue-200">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                    Stock
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
<div id="editMedicineModal-{{ $medicine->id }}" class="fixed inset-0 z-50 hidden bg-slate-900/50 backdrop-blur-sm overflow-y-auto w-full h-full text-left">
    <div class="relative w-full max-w-md mx-auto top-20 p-5">
        <div class="bg-white dark:bg-slate-800 rounded-3xl shadow-2xl overflow-hidden border border-slate-200 dark:border-slate-700">
            <div class="px-6 py-5 border-b border-slate-100 dark:border-slate-700 flex justify-between items-center bg-slate-50/50 dark:bg-slate-800/50">
                <div>
                    <h3 class="text-lg font-extrabold text-slate-900 dark:text-white">Edit Medicine</h3>
                </div>
                <button type="button" onclick="closeEditMedicineModal({{ $medicine->id }})" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-300">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            <div class="p-6">
                <form action="{{ route('pharmacy.medicines.update', $medicine->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1">Brand Name <span class="text-red-500">*</span></label>
                            <input type="text" name="name" value="{{ $medicine->name }}" required class="w-full rounded-xl border-slate-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1">Generic Name</label>
                            <input type="text" name="generic_name" value="{{ $medicine->generic_name }}" class="w-full rounded-xl border-slate-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1">Form (e.g. Tablet, Syrup, Capsule)</label>
                            <input type="text" name="form" value="{{ $medicine->form }}" class="w-full rounded-xl border-slate-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
                        </div>
                    </div>
                    <div class="flex justify-end gap-3 mt-6">
                        <button type="button" onclick="closeEditMedicineModal({{ $medicine->id }})" class="px-5 py-2.5 text-sm font-bold text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-xl transition-colors">Cancel</button>
                        <button type="submit" class="px-5 py-2.5 text-sm font-bold text-white bg-emerald-600 hover:bg-emerald-700 rounded-xl shadow-sm transition-all">Update Medicine</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Add Stock Modal -->
<div id="addStockModal-{{ $medicine->id }}" class="fixed inset-0 z-50 hidden bg-slate-900/50 backdrop-blur-sm overflow-y-auto w-full h-full text-left">
    <div class="relative w-full max-w-md mx-auto top-20 p-5">
        <div class="bg-white dark:bg-slate-800 rounded-3xl shadow-2xl overflow-hidden border border-slate-200 dark:border-slate-700">
            <div class="px-6 py-5 border-b border-slate-100 dark:border-slate-700 flex justify-between items-center bg-slate-50/50 dark:bg-slate-800/50">
                <div>
                    <h3 class="text-lg font-extrabold text-slate-900 dark:text-white">Add Stock for {{ $medicine->name }}</h3>
                </div>
                <button type="button" onclick="closeAddStockModal({{ $medicine->id }})" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-300">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            <div class="p-6">
                <form action="{{ route('pharmacy.medicines.add-stock', $medicine->id) }}" method="POST">
                    @csrf
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1">Batch Number (Optional)</label>
                            <input type="text" name="batch_number" class="w-full rounded-xl border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
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
                        <button type="button" onclick="closeAddStockModal({{ $medicine->id }})" class="px-5 py-2.5 text-sm font-bold text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-xl transition-colors">Cancel</button>
                        <button type="submit" class="px-5 py-2.5 text-sm font-bold text-white bg-blue-600 hover:bg-blue-700 rounded-xl shadow-sm transition-all">Save Stock</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endforeach

@endsection

<!-- Add New Medicine Modal -->
<div id="addMedicineModal" class="fixed inset-0 z-50 hidden bg-slate-900/50 backdrop-blur-sm overflow-y-auto w-full h-full text-left">
    <div class="relative w-full max-w-md mx-auto top-20 p-5">
        <div class="bg-white dark:bg-slate-800 rounded-3xl shadow-2xl overflow-hidden border border-slate-200 dark:border-slate-700">
            <div class="px-6 py-5 border-b border-slate-100 dark:border-slate-700 flex justify-between items-center bg-slate-50/50 dark:bg-slate-800/50">
                <div>
                    <h3 class="text-lg font-extrabold text-slate-900 dark:text-white">Add New Medicine</h3>
                </div>
                <button type="button" onclick="closeAddMedicineModal()" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-300">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            <div class="p-6">
                <form action="{{ route('pharmacy.medicines.store') }}" method="POST">
                    @csrf
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1">Brand Name <span class="text-red-500">*</span></label>
                            <input type="text" name="name" required placeholder="e.g. Biogesic" class="w-full rounded-xl border-slate-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1">Generic Name</label>
                            <input type="text" name="generic_name" placeholder="e.g. Paracetamol" class="w-full rounded-xl border-slate-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1">Form</label>
                            <input type="text" name="form" placeholder="e.g. Tablet, Syrup, Capsule" class="w-full rounded-xl border-slate-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
                        </div>
                    </div>
                    <div class="flex justify-end gap-3 mt-6">
                        <button type="button" onclick="closeAddMedicineModal()" class="px-5 py-2.5 text-sm font-bold text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-xl transition-colors">Cancel</button>
                        <button type="submit" class="px-5 py-2.5 text-sm font-bold text-white bg-emerald-600 hover:bg-emerald-700 rounded-xl shadow-sm transition-all">Save Medicine</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function openAddMedicineModal() {
        document.getElementById('addMedicineModal').classList.remove('hidden');
    }
    function closeAddMedicineModal() {
        document.getElementById('addMedicineModal').classList.add('hidden');
    }
    function openEditMedicineModal(id) {
        document.getElementById('editMedicineModal-' + id).classList.remove('hidden');
    }
    function closeEditMedicineModal(id) {
        document.getElementById('editMedicineModal-' + id).classList.add('hidden');
    }
    function openAddStockModal(id) {
        document.getElementById('addStockModal-' + id).classList.remove('hidden');
    }
    function closeAddStockModal(id) {
        document.getElementById('addStockModal-' + id).classList.add('hidden');
    }
</script>
@endpush
