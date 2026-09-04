@extends('layouts.pharmacy')

@section('header', 'Pharmacy Dashboard')

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
        <span class="text-slate-700 dark:text-slate-300 font-semibold">Real-Time Dashboard</span>
    </nav>

    <!-- Top Hero Banner & Queue Stats (Content Management Style) -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-stretch">
        <!-- Pharmacist Welcome Banner -->
        <div class="lg:col-span-8 relative overflow-hidden bg-gradient-to-br from-slate-900 via-slate-950 to-emerald-950 rounded-3xl p-6 sm:p-8 border border-slate-800 shadow-xl flex flex-col justify-between text-white">
            <div class="absolute -right-16 -top-16 w-64 h-64 bg-emerald-500/10 rounded-full blur-3xl pointer-events-none"></div>
            
            <div>
                <div class="inline-flex items-center gap-2 px-3 py-1 bg-emerald-500/20 text-emerald-300 text-[11px] font-bold uppercase tracking-wider rounded-lg border border-emerald-500/30 mb-4 shadow-xs">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    Pharmacy Dispensing Station
                </div>

                <div class="flex items-center gap-4 sm:gap-6 mt-1">
                    <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-2xl bg-emerald-500/20 border-2 border-emerald-400/30 flex items-center justify-center overflow-hidden shrink-0 shadow-lg shadow-emerald-500/10">
                        @if(auth()->user() && auth()->user()->avatar_url)
                            <img src="{{ auth()->user()->avatar_url }}" alt="{{ auth()->user()->name }}" class="w-full h-full object-cover">
                        @else
                            <span class="text-2xl sm:text-3xl font-black text-emerald-400">
                                {{ auth()->user() ? auth()->user()->initials : 'PH' }}
                            </span>
                        @endif
                    </div>
                    <div>
                        <h2 class="text-2xl sm:text-3xl font-black tracking-tight text-white">
                            Welcome back, {{ auth()->user() ? auth()->user()->formatted_name : 'Pharmacist' }}
                        </h2>
                        <p class="text-xs sm:text-sm text-slate-300 mt-1 max-w-xl font-normal leading-relaxed">
                            Manage pending prescription orders, dispense medicines safely, and monitor warehouse batch retention.
                        </p>
                    </div>
                </div>
            </div>

            <div class="mt-6 pt-5 border-t border-white/10 flex flex-wrap items-center justify-between gap-4">
                <div class="flex items-center gap-4 text-xs font-semibold text-slate-300">
                    <span class="flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <span>{{ now()->format('l, F d, Y') }}</span>
                    </span>
                    <span class="hidden sm:inline text-slate-600">•</span>
                    <span class="hidden sm:inline text-emerald-400 font-bold">Auto-Sync Active</span>
                </div>

                <div class="flex items-center gap-2">
                    <a href="{{ route('pharmacy.medicines') }}" class="px-4 py-2 rounded-xl bg-white/10 hover:bg-white/20 text-white text-xs font-bold transition border border-white/10 flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                        <span>Browse Inventory</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- Pending Prescriptions Stat Card -->
        <div class="lg:col-span-4 bg-white dark:bg-slate-900 rounded-3xl p-6 sm:p-8 border border-slate-200/90 dark:border-slate-800 shadow-xs flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-4">
                    <div class="w-11 h-11 rounded-2xl bg-emerald-100/80 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center border border-emerald-500/20 shadow-2xs">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                    </div>
                    <span class="px-2.5 py-1 rounded-full text-[10px] font-black uppercase tracking-wider bg-amber-100 text-amber-800 dark:bg-amber-950/60 dark:text-amber-300 border border-amber-200 dark:border-amber-800/60 shadow-2xs animate-pulse">
                        Active Queue
                    </span>
                </div>
                <h3 class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Pending Orders</h3>
                <p class="text-4xl sm:text-5xl font-black text-slate-900 dark:text-white mt-2 tracking-tight">
                    {{ $prescriptions->count() }}
                </p>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-2 font-medium">Patients waiting for prescription review & dispensing today.</p>
            </div>

            <div class="mt-6 pt-4 border-t border-slate-100 dark:border-slate-800/80">
                <a href="#queue-table" class="w-full py-2.5 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-bold text-xs flex items-center justify-center gap-1.5 transition-colors">
                    <span>Jump to Pending Queue</span>
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </a>
            </div>
        </div>
    </div>

    <!-- Inventory Alert Metrics (Content Management Cards) -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Expired Card -->
        <a href="{{ route('pharmacy.medicines', ['status_filter' => 'expired']) }}" class="bg-white dark:bg-slate-900 rounded-3xl p-5 sm:p-6 border border-slate-200/90 dark:border-slate-800 shadow-xs hover:shadow-md transition-all group flex flex-col justify-between">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 rounded-2xl bg-rose-100/80 dark:bg-rose-950/60 text-rose-600 dark:text-rose-400 flex items-center justify-center border border-rose-500/20 shadow-2xs">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-rose-600 dark:text-rose-400 bg-rose-50 dark:bg-rose-950/40 px-2 py-0.5 rounded-full">Expired</span>
            </div>
            <div>
                <p class="text-3xl font-black text-slate-900 dark:text-white">{{ $expiredBatchesCount }}</p>
                <p class="text-xs text-slate-500 dark:text-slate-400 font-medium mt-1">Batch(es) must be purged</p>
            </div>
        </a>

        <!-- Expiring Soon Card -->
        <a href="{{ route('pharmacy.medicines', ['status_filter' => 'expiring_soon']) }}" class="bg-white dark:bg-slate-900 rounded-3xl p-5 sm:p-6 border border-slate-200/90 dark:border-slate-800 shadow-xs hover:shadow-md transition-all group flex flex-col justify-between">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 rounded-2xl bg-amber-100/80 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400 flex items-center justify-center border border-amber-500/20 shadow-2xs">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-amber-600 dark:text-amber-400 bg-amber-50 dark:bg-amber-950/40 px-2 py-0.5 rounded-full">Within 30d</span>
            </div>
            <div>
                <p class="text-3xl font-black text-slate-900 dark:text-white">{{ $expiringSoonCount }}</p>
                <p class="text-xs text-slate-500 dark:text-slate-400 font-medium mt-1">Near expiry date</p>
            </div>
        </a>

        <!-- Low Stock Card -->
        <a href="{{ route('pharmacy.medicines', ['status_filter' => 'low_stock']) }}" class="bg-white dark:bg-slate-900 rounded-3xl p-5 sm:p-6 border border-slate-200/90 dark:border-slate-800 shadow-xs hover:shadow-md transition-all group flex flex-col justify-between">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 rounded-2xl bg-blue-100/80 dark:bg-blue-950/60 text-blue-600 dark:text-blue-400 flex items-center justify-center border border-blue-500/20 shadow-2xs">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-950/40 px-2 py-0.5 rounded-full">&lt; 20 Units</span>
            </div>
            <div>
                <p class="text-3xl font-black text-slate-900 dark:text-white">{{ $lowStockCount }}</p>
                <p class="text-xs text-slate-500 dark:text-slate-400 font-medium mt-1">Low stock medicines</p>
            </div>
        </a>

        <!-- Out of Stock Card -->
        <a href="{{ route('pharmacy.medicines', ['status_filter' => 'out_of_stock']) }}" class="bg-white dark:bg-slate-900 rounded-3xl p-5 sm:p-6 border border-slate-200/90 dark:border-slate-800 shadow-xs hover:shadow-md transition-all group flex flex-col justify-between">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 rounded-2xl bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 flex items-center justify-center border border-slate-200 dark:border-slate-700 shadow-2xs">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path></svg>
                </div>
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-600 dark:text-slate-400 bg-slate-100 dark:bg-slate-800 px-2 py-0.5 rounded-full">Depleted</span>
            </div>
            <div>
                <p class="text-3xl font-black text-slate-900 dark:text-white">{{ $outOfStockCount }}</p>
                <p class="text-xs text-slate-500 dark:text-slate-400 font-medium mt-1">Zero inventory available</p>
            </div>
        </a>
    </div>

    <!-- Active Prescription Dispensing Queue Table -->
    <div id="queue-table" class="bg-white dark:bg-slate-900 rounded-3xl shadow-xs border border-slate-200/90 dark:border-slate-800 overflow-hidden">
        <div class="px-6 py-5 border-b border-slate-100 dark:border-slate-800/80 bg-slate-50/70 dark:bg-slate-900/60 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h3 class="text-base font-bold text-slate-900 dark:text-white">Active Dispensing Queue</h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Real-time orders awaiting pharmacy inventory fulfillment today.</p>
            </div>
            <span class="px-3 py-1 rounded-full text-xs font-black bg-emerald-50 dark:bg-emerald-950/50 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800/60 shadow-2xs">
                {{ $prescriptions->count() }} In Queue
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/90 dark:bg-slate-950/80 text-slate-500 dark:text-slate-400 text-[11px] font-extrabold uppercase tracking-wider border-b border-slate-200/80 dark:border-slate-800">
                        <th class="p-4 sm:p-5 w-[18%]">Prescription No.</th>
                        <th class="p-4 sm:p-5 flex-1">Patient Details</th>
                        <th class="p-4 sm:p-5 w-[25%]">Attending Clinician</th>
                        <th class="p-4 sm:p-5 w-[15%]">Status</th>
                        <th class="p-4 sm:p-5 w-[18%] text-right">Action</th>
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
                                        <p class="text-[11px] font-semibold text-slate-500 dark:text-slate-400 mt-0.5">{{ $request->created_at->format('h:i A') }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="p-4 sm:p-5 align-top">
                                <p class="font-bold text-slate-900 dark:text-white text-sm">{{ $request->patient->full_name ?? 'Unknown' }}</p>
                                <p class="text-xs font-mono font-medium text-slate-500 dark:text-slate-400 mt-0.5">ID: {{ $request->patient->patient_id ?? '—' }}</p>
                                <p class="text-[11px] text-slate-400 mt-0.5">{{ $request->patient->dob ? \Carbon\Carbon::parse($request->patient->dob)->age . ' yrs' : '?' }} • {{ $request->patient->sex ?? '?' }}</p>
                            </td>
                            <td class="p-4 sm:p-5 align-top">
                                <p class="text-sm font-bold text-slate-800 dark:text-slate-200">{{ $request->doctor->formatted_name ?? 'Clinician' }}</p>
                                <p class="text-xs font-medium text-slate-500 dark:text-slate-400 mt-0.5">{{ $request->items->count() }} item(s) ordered</p>
                            </td>
                            <td class="p-4 sm:p-5 align-top">
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800 dark:bg-amber-950/60 dark:text-amber-300 border border-amber-200 dark:border-amber-800/60 shadow-2xs">
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                    Awaiting Dispense
                                </span>
                            </td>
                            <td class="p-4 sm:p-5 align-top text-right">
                                <button onclick="openDispenseModal({{ $request->id }})" class="h-9 px-3.5 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white text-xs font-bold shadow-md shadow-emerald-600/20 transition-all inline-flex items-center gap-1.5 active:scale-95 cursor-pointer">
                                    <span>Review & Dispense</span>
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="p-16 text-center text-slate-500 dark:text-slate-400">
                                <div class="w-14 h-14 rounded-2xl bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 mx-auto flex items-center justify-center mb-3 shadow-xs border border-emerald-500/20">
                                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                </div>
                                <h3 class="text-base font-bold text-slate-900 dark:text-white mb-1">Queue is clear</h3>
                                <p class="text-xs text-slate-500 dark:text-slate-400">No pending prescriptions waiting for fulfillment right now.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Analytics & Demand Insights Section -->
    <div class="space-y-4 pt-2">
        <div class="border-b border-slate-200/80 dark:border-slate-800 pb-3">
            <h3 class="text-lg font-bold text-slate-900 dark:text-white flex items-center gap-2">
                <span class="w-8 h-8 rounded-xl bg-emerald-100/80 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center border border-emerald-500/20 shadow-2xs">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                </span>
                <span>Inventory & Consumption Analytics</span>
            </h3>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Top Dispensed Chart -->
            <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 sm:p-7 border border-slate-200/90 dark:border-slate-800 shadow-xs">
                <div class="flex items-center justify-between mb-4 pb-3 border-b border-slate-100 dark:border-slate-800/80">
                    <div>
                        <h4 class="text-sm font-bold text-slate-900 dark:text-white">Top Dispensed Medicines</h4>
                        <p class="text-[11px] text-slate-400">Past 30 days total volume</p>
                    </div>
                    <span class="text-xs font-bold text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-950/40 px-2.5 py-1 rounded-lg border border-emerald-500/20">30-Day Window</span>
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
                        <p class="text-[11px] text-slate-400">Past 6 months longitudinal volume</p>
                    </div>
                    <span class="text-xs font-bold text-sky-600 dark:text-sky-400 bg-sky-50 dark:bg-sky-950/40 px-2.5 py-1 rounded-lg border border-sky-500/20">6-Month Trend</span>
                </div>
                <div class="h-64 relative">
                    <canvas id="monthlyTrendChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Dispense Modals for Pending Prescriptions --}}
@foreach($prescriptions as $request)
    <div id="dispenseModal-{{ $request->id }}" class="fixed inset-0 z-[999] hidden bg-slate-950/70 backdrop-blur-sm overflow-y-auto w-full h-full text-left">
        <div class="flex items-center justify-center min-h-screen px-4 py-8">
            <div class="bg-white dark:bg-slate-900 rounded-3xl shadow-2xl overflow-hidden border border-slate-200/90 dark:border-slate-800 max-w-xl w-full">
                <div class="px-6 py-5 border-b border-slate-100 dark:border-slate-800 flex justify-between items-center bg-slate-50/70 dark:bg-slate-900/60">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-emerald-100/80 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center border border-emerald-500/20 shadow-2xs">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-slate-900 dark:text-white">Dispense Order #{{ $request->id }}</h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400">Review stock availability before releasing medication</p>
                        </div>
                    </div>
                    <button onclick="closeDispenseModal({{ $request->id }})" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 p-1.5 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
                <div class="p-6 sm:p-7">
                    <!-- Patient Summary Card -->
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

                    <!-- Items Checklist -->
                    <div class="mb-6">
                        <h4 class="text-xs font-black uppercase text-slate-400 mb-3 tracking-wider">Requested Medications</h4>
                        <ul class="space-y-2.5">
                            @foreach($request->items as $item)
                                @php
                                    $cleanName = trim(preg_replace('/\s*\([^)]*\)$/', '', $item->medicine_name));
                                    $medicine = \App\Models\Medicine::where('name', $item->medicine_name)
                                        ->orWhere('generic_name', $item->medicine_name)
                                        ->orWhere('name', $cleanName)
                                        ->orWhere('generic_name', $cleanName)
                                        ->first();
                                    $stock = $medicine ? $medicine->total_stock : 0;
                                    $sufficient = $stock >= ($item->quantity ?: 0);
                                @endphp
                                <li class="p-4 rounded-2xl border border-slate-200/90 dark:border-slate-800 bg-white dark:bg-slate-800/80 flex items-center justify-between gap-3 shadow-2xs">
                                    <div>
                                        <p class="font-bold text-slate-900 dark:text-white text-sm">{{ $item->medicine_name }}</p>
                                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">{{ $item->dosage }} • {{ $item->frequency }} • {{ $item->duration }}</p>
                                        <p class="text-[11px] text-slate-400 mt-1"><span class="font-semibold text-slate-600 dark:text-slate-300">Sig:</span> {{ $item->instruction }}</p>
                                    </div>
                                    <div class="text-right shrink-0">
                                        <p class="text-sm font-black text-slate-900 dark:text-white">Qty: {{ $item->quantity ?: 'N/A' }}</p>
                                        @if($item->quantity)
                                            @if($sufficient)
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800/60 mt-1">
                                                    In Stock ({{ $stock }})
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-100 text-rose-800 dark:bg-rose-950/60 dark:text-rose-300 border border-rose-200 dark:border-rose-800/60 mt-1">
                                                    Low ({{ $stock }} avail)
                                                </span>
                                            @endif
                                        @endif
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>

                    <form action="{{ route('pharmacy.dispense', $request->id) }}" method="POST">
                        @csrf
                        <div class="flex justify-end gap-2.5 pt-4 border-t border-slate-100 dark:border-slate-800">
                            <button type="button" onclick="closeDispenseModal({{ $request->id }})" class="px-4 py-2.5 rounded-xl text-xs font-semibold text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition border border-slate-300 dark:border-slate-700 cursor-pointer">
                                Cancel
                            </button>
                            <button type="submit" class="px-5 py-2.5 rounded-xl text-xs font-bold bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white transition shadow-sm cursor-pointer active:scale-95 flex items-center gap-1.5">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                <span>Confirm & Dispense</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endforeach

@push('scripts')
<script>
    function openDispenseModal(id) {
        document.getElementById('dispenseModal-' + id).classList.remove('hidden');
    }
    function closeDispenseModal(id) {
        document.getElementById('dispenseModal-' + id).classList.add('hidden');
    }

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
