@extends('layouts.doctor')

@section('header', 'Diagnostic Results Tracker')

@section('content')
<div class="max-w-7xl mx-auto space-y-6 pb-16"
     x-data="{
         searchQuery: '',
         filterType: 'all',
         init() {
             window.addEventListener('doctor-global-search', (e) => {
                 this.searchQuery = e.detail;
             });
         },
         matches(item) {
             const q = this.searchQuery.toLowerCase().trim();
             const matchesSearch = !q || 
                 item.name.toLowerCase().includes(q) || 
                 item.queueNum.toLowerCase().includes(q) || 
                 item.patientId.toLowerCase().includes(q) || 
                 item.tests.toLowerCase().includes(q);

             if (!matchesSearch) return false;

             if (this.filterType === 'all') return true;
             if (this.filterType === 'ready') return item.isReady;
             if (this.filterType === 'lab') return item.hasLab;
             if (this.filterType === 'rad') return item.hasRad;
             return true;
         }
     }">

    {{-- Breadcrumb --}}
    <x-breadcrumb :items="['Active Queue' => route('doctor.dashboard'), 'Diagnostic Results Tracker' => '']" />

    {{-- Diagnostic Summary Bento Cards --}}
    @php
        $totalAwaiting = $awaitingPatients->total();
        $allRequests = $awaitingPatients->getCollection()->flatMap->ancillaryRequests;
        $totalLab = $allRequests->where('type', 'Laboratory')->count();
        $totalRad = $allRequests->where('type', 'Radiology')->count();
        $resultsReadyCount = $awaitingPatients->getCollection()->where('status', 'results_ready')->count();
    @endphp

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-5">
        {{-- Card 1: Total Waiting --}}
        <div class="rounded-3xl bg-white/90 dark:bg-slate-900/90 border border-slate-200/90 dark:border-slate-800 p-6 shadow-xs backdrop-blur-xs relative overflow-hidden flex flex-col justify-between">
            <div class="absolute top-0 inset-x-0 h-1.5 bg-gradient-to-r from-amber-500 to-orange-500"></div>
            <div class="flex items-center justify-between mb-2">
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Total In Diagnostics</span>
                <span class="w-8 h-8 rounded-xl bg-amber-50 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400 flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </span>
            </div>
            <div class="flex items-baseline gap-2">
                <span class="font-display text-3xl sm:text-4xl font-extrabold text-slate-900 dark:text-white">{{ $totalAwaiting }}</span>
                <span class="text-xs text-slate-500">patients on hold</span>
            </div>
        </div>

        {{-- Card 2: Results Ready (Urgent) --}}
        <div class="rounded-3xl bg-white/90 dark:bg-slate-900/90 border border-emerald-300 dark:border-emerald-800/80 p-6 shadow-xs backdrop-blur-xs relative overflow-hidden flex flex-col justify-between ring-2 ring-emerald-500/10">
            <div class="absolute top-0 inset-x-0 h-1.5 bg-gradient-to-r from-emerald-500 to-teal-600"></div>
            <div class="flex items-center justify-between mb-2">
                <span class="text-[11px] font-bold uppercase tracking-wider text-emerald-700 dark:text-emerald-400">Results Ready</span>
                <span class="w-8 h-8 rounded-xl bg-emerald-100 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-400 flex items-center justify-center animate-pulse">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </span>
            </div>
            <div class="flex items-baseline gap-2">
                <span class="font-display text-3xl sm:text-4xl font-extrabold text-emerald-700 dark:text-emerald-400">{{ $resultsReadyCount }}</span>
                <span class="text-xs text-emerald-600 dark:text-emerald-400 font-semibold">ready to resume</span>
            </div>
        </div>

        {{-- Card 3: Laboratory Tests --}}
        <div class="rounded-3xl bg-white/90 dark:bg-slate-900/90 border border-slate-200/90 dark:border-slate-800 p-6 shadow-xs backdrop-blur-xs relative overflow-hidden flex flex-col justify-between">
            <div class="absolute top-0 inset-x-0 h-1.5 bg-gradient-to-r from-purple-500 to-fuchsia-600"></div>
            <div class="flex items-center justify-between mb-2">
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Lab Orders</span>
                <span class="w-8 h-8 rounded-xl bg-purple-50 dark:bg-purple-950/60 text-purple-600 dark:text-purple-400 flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/></svg>
                </span>
            </div>
            <div class="flex items-baseline gap-2">
                <span class="font-display text-3xl sm:text-4xl font-extrabold text-purple-700 dark:text-purple-400">{{ $totalLab }}</span>
                <span class="text-xs text-slate-500">specimens tracked</span>
            </div>
        </div>

        {{-- Card 4: Radiology Orders --}}
        <div class="rounded-3xl bg-white/90 dark:bg-slate-900/90 border border-slate-200/90 dark:border-slate-800 p-6 shadow-xs backdrop-blur-xs relative overflow-hidden flex flex-col justify-between">
            <div class="absolute top-0 inset-x-0 h-1.5 bg-gradient-to-r from-indigo-500 to-sky-600"></div>
            <div class="flex items-center justify-between mb-2">
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Radiology Orders</span>
                <span class="w-8 h-8 rounded-xl bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400 flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                </span>
            </div>
            <div class="flex items-baseline gap-2">
                <span class="font-display text-3xl sm:text-4xl font-extrabold text-indigo-700 dark:text-indigo-400">{{ $totalRad }}</span>
                <span class="text-xs text-slate-500">scans ordered</span>
            </div>
        </div>
    </div>

    {{-- Interactive Search & Category Filter Toolbar --}}
    <div class="p-4 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200/90 dark:border-slate-800 shadow-xs flex flex-col sm:flex-row items-center justify-between gap-4">
        {{-- Search Input --}}
        <div class="relative w-full sm:w-96">
            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                <svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </div>
            <input type="text" 
                   x-model="searchQuery" 
                   placeholder="Search patient, ID, or test name (e.g. CBC, X-Ray)..." 
                   class="no-uppercase w-full pl-10 pr-9 py-2 rounded-2xl bg-slate-50 dark:bg-slate-800/70 border border-slate-200 dark:border-slate-700 text-xs sm:text-sm text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all"
                   style="text-transform: none !important;">
            <button type="button" 
                    x-show="searchQuery.length > 0" 
                    @click="searchQuery = ''"
                    class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        {{-- Filter Chips --}}
        <div class="inline-flex items-center p-1 rounded-2xl bg-slate-100 dark:bg-slate-800/80 border border-slate-200/80 dark:border-slate-700/70 overflow-x-auto w-full sm:w-auto">
            <button type="button" 
                    @click="filterType = 'all'"
                    class="px-3 py-1.5 rounded-xl text-xs font-bold transition-all cursor-pointer whitespace-nowrap"
                    :class="filterType === 'all' ? 'bg-white dark:bg-slate-900 text-slate-900 dark:text-white shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900'">
                All Tests
            </button>
            <button type="button" 
                    @click="filterType = 'ready'"
                    class="px-3 py-1.5 rounded-xl text-xs font-bold transition-all cursor-pointer whitespace-nowrap"
                    :class="filterType === 'ready' ? 'bg-white dark:bg-slate-900 text-emerald-700 dark:text-emerald-400 shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900'">
                Results Ready
            </button>
            <button type="button" 
                    @click="filterType = 'lab'"
                    class="px-3 py-1.5 rounded-xl text-xs font-bold transition-all cursor-pointer whitespace-nowrap"
                    :class="filterType === 'lab' ? 'bg-white dark:bg-slate-900 text-purple-700 dark:text-purple-400 shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900'">
                Laboratory
            </button>
            <button type="button" 
                    @click="filterType = 'rad'"
                    class="px-3 py-1.5 rounded-xl text-xs font-bold transition-all cursor-pointer whitespace-nowrap"
                    :class="filterType === 'rad' ? 'bg-white dark:bg-slate-900 text-indigo-700 dark:text-indigo-400 shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900'">
                Radiology
            </button>
        </div>
    </div>

    {{-- Main Diagnostic Patient Cards --}}
    <div class="space-y-4">
        @if($awaitingPatients->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                @foreach($awaitingPatients as $consultation)
                    @php
                        $isReady = $consultation->status === 'results_ready';
                        $hasLab = $consultation->ancillaryRequests->where('type', 'Laboratory')->isNotEmpty();
                        $hasRad = $consultation->ancillaryRequests->where('type', 'Radiology')->isNotEmpty();
                        $testsList = $consultation->ancillaryRequests->pluck('test_name')->implode(', ');
                        $completedCount = $consultation->ancillaryRequests->where('status', 'Done')->count();
                        $totalTests = $consultation->ancillaryRequests->count();
                    @endphp

                    <div class="rounded-3xl bg-white dark:bg-slate-900 border transition-all duration-300 relative overflow-hidden shadow-xs hover:shadow-md flex flex-col justify-between {{ $isReady ? 'border-emerald-400 dark:border-emerald-500/80 ring-2 ring-emerald-500/20' : 'border-slate-200/90 dark:border-slate-800 hover:border-amber-400 dark:hover:border-amber-600' }}"
                         x-show="matches({
                             name: '{{ addslashes($consultation->patient->full_name) }}',
                             queueNum: '{{ $consultation->queue_number }}',
                             patientId: '{{ $consultation->patient->patient_id }}',
                             tests: '{{ addslashes($testsList) }}',
                             isReady: {{ $isReady ? 'true' : 'false' }},
                             hasLab: {{ $hasLab ? 'true' : 'false' }},
                             hasRad: {{ $hasRad ? 'true' : 'false' }}
                         })"
                         x-transition>

                        {{-- Card Header --}}
                        <div class="p-4 border-b {{ $isReady ? 'border-emerald-200/80 dark:border-emerald-900/40 bg-emerald-50/70 dark:bg-emerald-950/30' : 'border-slate-100 dark:border-slate-800/80 bg-slate-50/60 dark:bg-slate-800/40' }} flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <span class="font-mono text-xs font-black px-2.5 py-1 rounded-lg bg-white dark:bg-slate-800 text-slate-900 dark:text-white border border-slate-200 dark:border-slate-700 shadow-2xs">
                                    {{ $consultation->queue_number }}
                                </span>
                                <span class="text-[11px] font-semibold text-slate-500 dark:text-slate-400">
                                    {{ $consultation->patient->patient_id }}
                                </span>
                            </div>

                            @if($isReady)
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider bg-emerald-600 text-white shadow-2xs animate-pulse">
                                    <span class="w-1.5 h-1.5 rounded-full bg-white"></span>
                                    Results Ready
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800 dark:bg-amber-950/60 dark:text-amber-300 border border-amber-200 dark:border-amber-800/60">
                                    <svg class="w-3 h-3 animate-spin text-amber-600" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                    Under Analysis
                                </span>
                            @endif
                        </div>

                        {{-- Card Body --}}
                        <div class="p-5 flex-1 flex flex-col justify-between space-y-4">
                            <div>
                                <div class="flex items-start justify-between gap-2">
                                    <div class="min-w-0 flex-1">
                                        <h4 class="text-base font-extrabold text-slate-900 dark:text-white truncate" title="{{ $consultation->patient->full_name }}">
                                            {{ $consultation->patient->full_name }}
                                        </h4>
                                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                                            {{ $consultation->patient->dob ? \Carbon\Carbon::parse($consultation->patient->dob)->age . ' yrs' : 'Age: ?' }} • {{ $consultation->patient->sex }} • {{ $consultation->patient->classification }}
                                        </p>
                                    </div>
                                </div>

                                {{-- Ordered Procedures List --}}
                                <div class="mt-3.5 space-y-2">
                                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Ordered Tests:</span>
                                    <div class="space-y-1.5 max-h-36 overflow-y-auto custom-scrollbar pr-1">
                                        @foreach($consultation->ancillaryRequests as $req)
                                            <div class="p-2 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200/60 dark:border-slate-700/60 flex items-center justify-between gap-2">
                                                <div class="flex items-center gap-2 min-w-0">
                                                    <span class="px-2 py-0.5 rounded text-[9px] font-extrabold uppercase tracking-wider {{ $req->type === 'Laboratory' ? 'bg-purple-100 text-purple-700 dark:bg-purple-950/60 dark:text-purple-300' : 'bg-indigo-100 text-indigo-700 dark:bg-indigo-950/60 dark:text-indigo-300' }}">
                                                        {{ $req->type }}
                                                    </span>
                                                    <span class="text-xs font-semibold text-slate-800 dark:text-slate-200 truncate">{{ $req->test_name }}</span>
                                                </div>
                                                @if($req->status === 'Done')
                                                    <span class="text-[9px] font-black text-emerald-700 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-950/60 px-2 py-0.5 rounded border border-emerald-200 dark:border-emerald-800 shrink-0">
                                                        ✓ Ready
                                                    </span>
                                                @else
                                                    <span class="text-[9px] font-bold text-amber-700 dark:text-amber-400 bg-amber-50 dark:bg-amber-950/60 px-2 py-0.5 rounded border border-amber-200 dark:border-amber-800 shrink-0">
                                                        Pending
                                                    </span>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                            {{-- Completion Progress & Action CTA --}}
                            <div class="pt-3 border-t border-slate-100 dark:border-slate-800/80 space-y-3">
                                <div class="flex items-center justify-between text-[11px] font-bold">
                                    <span class="text-slate-500">Test Progress:</span>
                                    <span class="{{ $completedCount === $totalTests && $totalTests > 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-slate-700 dark:text-slate-300' }}">
                                        {{ $completedCount }} / {{ $totalTests }} Complete ({{ $totalTests > 0 ? round(($completedCount / $totalTests) * 100) : 0 }}%)
                                    </span>
                                </div>

                                <a href="{{ route('doctor.consultation.start', $consultation->id) }}"
                                   class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold text-white shadow-sm transition-all cursor-pointer {{ $isReady ? 'bg-gradient-to-r from-emerald-600 to-teal-700 hover:from-emerald-700 hover:to-teal-800 shadow-emerald-900/10' : 'bg-gradient-to-r from-amber-600 to-orange-600 hover:from-amber-700 hover:to-orange-700 shadow-amber-900/10' }}">
                                    <span>{{ $isReady ? 'Review Results & Resume Consultation' : 'Open Consultation Record' }}</span>
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Pagination --}}
            <div class="pt-4">
                {{ $awaitingPatients->links() }}
            </div>
        @else
            <div class="rounded-3xl border border-dashed border-slate-200 dark:border-slate-800 bg-white/70 dark:bg-slate-900/60 p-12 sm:p-16 text-center">
                <div class="w-16 h-16 rounded-2xl bg-amber-50 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400 flex items-center justify-center mx-auto mb-4 shadow-inner">
                    <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <h4 class="font-display text-xl font-bold text-slate-900 dark:text-white">No Pending Diagnostics</h4>
                <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 max-w-sm mx-auto mt-1">All ordered laboratory and radiology tests have been completed, or no patients are currently awaiting results.</p>
                <div class="mt-6">
                    <a href="{{ route('doctor.dashboard') }}" 
                       class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-xs font-bold text-white bg-slate-900 hover:bg-emerald-700 transition">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                        <span>Return to Active Queue</span>
                    </a>
                </div>
            </div>
        @endif
    </div>

</div>
@endsection
