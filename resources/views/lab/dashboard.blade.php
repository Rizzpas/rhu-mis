@extends('layouts.lab')

@section('header', $type . ' Dashboard')

@section('content')
<div class="max-w-7xl mx-auto space-y-6 pb-12" x-data="labDashboard()">

    <!-- Global Floating Toast for Async Progressions -->
    <div x-show="toastMessage" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 -translate-y-4"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 -translate-y-4"
         style="display: none;"
         class="fixed top-6 right-6 z-[100] max-w-md p-4 rounded-2xl shadow-2xl border text-sm font-bold flex items-center gap-3 backdrop-blur-md"
         :class="toastType === 'error' ? 'bg-rose-50/95 dark:bg-rose-950/95 border-rose-300 dark:border-rose-700 text-rose-800 dark:text-rose-200' : 'bg-emerald-50/95 dark:bg-emerald-950/95 border-emerald-300 dark:border-emerald-700 text-emerald-800 dark:text-emerald-200'">
        <template x-if="toastType !== 'error'">
            <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
        </template>
        <template x-if="toastType === 'error'">
            <svg class="w-5 h-5 text-rose-600 dark:text-rose-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
        </template>
        <span x-text="toastMessage" class="flex-1"></span>
        <button type="button" @click="toastMessage = ''" class="opacity-60 hover:opacity-100 transition">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
    </div>

    <!-- Breadcrumb Navigation -->
    <nav class="flex items-center gap-2 text-xs font-medium text-slate-500 dark:text-slate-400 py-2 border-b border-slate-200/60 dark:border-slate-800" aria-label="Breadcrumb">
        <a href="{{ route('lab.dashboard') }}" class="hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors flex items-center gap-1.5 shrink-0">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
            <span>Home</span>
        </a>
        <span class="text-slate-300 dark:text-slate-700">/</span>
        <span class="hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors">{{ $type }}</span>
        <span class="text-slate-300 dark:text-slate-700">/</span>
        <span class="text-slate-700 dark:text-slate-300 font-semibold">Real-Time Dashboard</span>
    </nav>

    <!-- Top Hero Banner & Queue Stats (Bento Style matching other roles) -->
    <div id="lab-stats-section" data-dynamic-block="true" class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-stretch">
        <!-- Diagnostic Welcome Banner -->
        <div class="lg:col-span-8 relative overflow-hidden bg-gradient-to-br from-slate-900 via-slate-950 to-emerald-950 rounded-3xl p-6 sm:p-8 border border-slate-800 shadow-xl flex flex-col justify-between text-white">
            <div class="absolute -right-16 -top-16 w-64 h-64 bg-emerald-500/10 rounded-full blur-3xl pointer-events-none"></div>
            
            <div>
                <div class="inline-flex items-center gap-2 px-3 py-1 bg-emerald-500/20 text-emerald-300 border-emerald-500/30 text-[11px] font-bold uppercase tracking-wider rounded-lg border mb-4 shadow-xs">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    {{ $type }} Diagnostic Station
                </div>

                <div class="flex items-center gap-4 sm:gap-6 mt-1">
                    <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-2xl bg-emerald-500/20 border-emerald-400/30 shadow-emerald-500/10 border-2 flex items-center justify-center overflow-hidden shrink-0 shadow-lg">
                        @if(auth()->user() && auth()->user()->avatar_url)
                            <img src="{{ auth()->user()->avatar_url }}" alt="{{ auth()->user()->name }}" class="w-full h-full object-cover">
                        @else
                            <span class="text-2xl sm:text-3xl font-black text-emerald-400">
                                {{ auth()->user() ? auth()->user()->initials : ($type === 'Radiology' ? 'RD' : 'LB') }}
                            </span>
                        @endif
                    </div>
                    <div>
                        <h2 class="text-2xl sm:text-3xl font-black tracking-tight text-white">
                            Welcome back, {{ auth()->user() ? auth()->user()->formatted_name : 'Technician' }}
                        </h2>
                        <p class="text-xs sm:text-sm text-slate-300 mt-1 max-w-xl font-normal leading-relaxed">
                            Process incoming {{ strtolower($type) }} orders, verify clinical results with structured templates, and maintain permanent diagnostic audit logs.
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
                    <button type="button" @click="location.reload()" class="px-4 py-2 rounded-xl bg-white/10 hover:bg-white/20 text-white text-xs font-bold transition border border-white/10 flex items-center gap-1.5 cursor-pointer">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                        <span>Refresh Queue</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Active Queue Stat Card -->
        <div class="lg:col-span-4 bg-white dark:bg-slate-900 rounded-3xl p-6 sm:p-8 border border-slate-200/90 dark:border-slate-800 shadow-xs flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-4">
                    <div class="w-11 h-11 rounded-2xl bg-emerald-100/80 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 border-emerald-500/20 flex items-center justify-center border shadow-2xs">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                    </div>
                    <span class="px-2.5 py-1 rounded-full text-[10px] font-black uppercase tracking-wider bg-amber-100 text-amber-800 dark:bg-amber-950/60 dark:text-amber-300 border border-amber-200 dark:border-amber-800/60 shadow-2xs animate-pulse">
                        Live Queue
                    </span>
                </div>
                <h3 class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Active Queue</h3>
                <p id="pending-stat-count" class="text-4xl sm:text-5xl font-black text-slate-900 dark:text-white mt-2 tracking-tight">
                    {{ $pendingRequests->count() }}
                </p>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-2 font-medium">Patients waiting for collection, testing, or findings encoding today.</p>
            </div>

            <div class="mt-6 pt-4 border-t border-slate-100 dark:border-slate-800/80">
                <button type="button" @click="setTab('pending')" class="w-full py-2.5 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-bold text-xs flex items-center justify-center gap-1.5 transition-colors cursor-pointer">
                    <span>Jump to Active Queue</span>
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </button>
            </div>
        </div>
    </div>

    <!-- KPI Metric Cards Grid (Content Management Style matching other dashboards) -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Card 1: Step 1 (Specimen / Intake) -->
        <div class="bg-white dark:bg-slate-900 rounded-3xl p-5 sm:p-6 border border-slate-200/90 dark:border-slate-800 shadow-xs flex flex-col justify-between">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 rounded-2xl bg-amber-100/80 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400 flex items-center justify-center border border-amber-500/20 shadow-2xs">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path></svg>
                </div>
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-amber-600 dark:text-amber-400 bg-amber-50 dark:bg-amber-950/40 px-2 py-0.5 rounded-full">Step 1</span>
            </div>
            <div>
                <p class="text-3xl font-black text-slate-900 dark:text-white">{{ $pendingRequests->where('status', 'Pending')->count() }}</p>
                <p class="text-xs text-slate-500 dark:text-slate-400 font-medium mt-1">{{ $type === 'Radiology' ? 'Awaiting Scan' : 'Specimen Collection' }}</p>
            </div>
        </div>

        <!-- Card 2: Step 2 (In Processing / Reading) -->
        <div class="bg-white dark:bg-slate-900 rounded-3xl p-5 sm:p-6 border border-slate-200/90 dark:border-slate-800 shadow-xs flex flex-col justify-between">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 rounded-2xl bg-teal-100/80 dark:bg-teal-950/60 text-teal-600 dark:text-teal-400 flex items-center justify-center border border-teal-500/20 shadow-2xs">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-teal-600 dark:text-teal-400 bg-teal-50 dark:bg-teal-950/40 px-2 py-0.5 rounded-full">Step 2</span>
            </div>
            <div>
                <p class="text-3xl font-black text-slate-900 dark:text-white">{{ $pendingRequests->whereIn('status', ['Specimen Collected', 'In Progress'])->count() }}</p>
                <p class="text-xs text-slate-500 dark:text-slate-400 font-medium mt-1">In Processing / Reading</p>
            </div>
        </div>

        <!-- Card 3: Completed Today -->
        <div class="bg-white dark:bg-slate-900 rounded-3xl p-5 sm:p-6 border border-slate-200/90 dark:border-slate-800 shadow-xs flex flex-col justify-between">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 rounded-2xl bg-emerald-100/80 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center border border-emerald-500/20 shadow-2xs">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-950/40 px-2 py-0.5 rounded-full">Done</span>
            </div>
            <div>
                <p class="text-3xl font-black text-slate-900 dark:text-white">{{ $completedRequests->filter(fn($r) => $r->completed_at && $r->completed_at->isToday())->count() }}</p>
                <p class="text-xs text-slate-500 dark:text-slate-400 font-medium mt-1">Completed Today</p>
            </div>
        </div>

        <!-- Card 4: Historical 6-Month Retained Records -->
        <div class="bg-white dark:bg-slate-900 rounded-3xl p-5 sm:p-6 border border-slate-200/90 dark:border-slate-800 shadow-xs flex flex-col justify-between">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 rounded-2xl bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 flex items-center justify-center border border-slate-200 dark:border-slate-700 shadow-2xs">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path></svg>
                </div>
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-600 dark:text-slate-400 bg-slate-100 dark:bg-slate-800 px-2 py-0.5 rounded-full">Retained</span>
            </div>
            <div>
                <p class="text-3xl font-black text-slate-900 dark:text-white">{{ $completedRequests->total() }}</p>
                <p class="text-xs text-slate-500 dark:text-slate-400 font-medium mt-1">6-Month Records Vault</p>
            </div>
        </div>
    </div>

    <!-- ═══════════════════ TAB NAVIGATION ═══════════════════ -->
    <div id="tab-counts-section" data-dynamic-block="true" class="bg-slate-100/70 dark:bg-slate-800/60 p-1.5 rounded-2xl flex gap-1.5 border border-slate-200/80 dark:border-slate-700/80 shadow-2xs">
        <button @click="setTab('pending')"
            :class="activeTab === 'pending' ? 'bg-white dark:bg-slate-900 text-emerald-700 dark:text-emerald-400 shadow-sm border-emerald-200 dark:border-emerald-800 font-extrabold' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white border-transparent font-bold'"
            class="flex-1 px-4 py-2.5 rounded-xl text-sm transition-all duration-200 border flex items-center justify-center gap-2 cursor-pointer">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
            Active Queue
            <span class="bg-emerald-100 dark:bg-emerald-900/60 text-emerald-700 dark:text-emerald-300 text-xs font-extrabold px-2 py-0.5 rounded-full">{{ $pendingRequests->count() }}</span>
        </button>
        <button @click="setTab('finished')"
            :class="activeTab === 'finished' ? 'bg-white dark:bg-slate-900 text-emerald-700 dark:text-emerald-400 shadow-sm border-emerald-200 dark:border-emerald-800 font-extrabold' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white border-transparent font-bold'"
            class="flex-1 px-4 py-2.5 rounded-xl text-sm transition-all duration-200 border flex items-center justify-center gap-2 cursor-pointer">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            Finished Tests
            <span class="bg-emerald-100 dark:bg-emerald-900/60 text-emerald-700 dark:text-emerald-300 text-xs font-extrabold px-2 py-0.5 rounded-full">{{ $completedRequests->total() }}</span>
        </button>
        <button @click="setTab('archive')"
            :class="activeTab === 'archive' ? 'bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-200 shadow-sm border-slate-300 dark:border-slate-600 font-extrabold' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white border-transparent font-bold'"
            class="flex-1 px-4 py-2.5 rounded-xl text-sm transition-all duration-200 border flex items-center justify-center gap-2 cursor-pointer">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path></svg>
            Archive / Cancelled / Rejected
            <span class="bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-300 text-xs font-extrabold px-2 py-0.5 rounded-full">{{ $archivedRequests->total() }}</span>
        </button>
    </div>

    <!-- ═══════════════════ TAB 1: ACTIVE QUEUE ═══════════════════ -->
    <div x-show="activeTab === 'pending'" 
         x-transition:enter="transition ease-out duration-200" 
         x-transition:enter-start="opacity-0 translate-y-2" 
         x-transition:enter-end="opacity-100 translate-y-0"
         x-data="{ searchPending: '' }">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
            <div>
                <h3 class="font-extrabold text-slate-900 dark:text-white text-2xl flex items-center gap-2">
                    <span class="bg-emerald-100 dark:bg-emerald-900/50 text-emerald-600 dark:text-emerald-400 p-1.5 rounded-lg">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                    </span>
                    Active Queue & Diagnostic Tracking
                </h3>
                <span class="text-xs text-slate-500 font-semibold">Workflow: Specimen Collection &rarr; Processing &rarr; Result Encoding</span>
            </div>
            <div class="relative flex items-center">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
                <input type="text" 
                       x-model.debounce.100ms="searchPending" 
                       placeholder="Filter queue by patient or test..." 
                       class="text-xs rounded-xl border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 pl-9 pr-8 py-2 focus:ring-2 focus:ring-emerald-500 w-64 sm:w-80 transition shadow-xs">
                <button type="button" 
                        x-show="searchPending" 
                        @click="searchPending = ''" 
                        class="absolute inset-y-0 right-0 pr-2.5 flex items-center text-slate-400 hover:text-slate-600 text-xs" 
                        title="Clear filter">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        </div>

        <div id="pending-queue-section" data-dynamic-block="true">
        @if($pendingRequests->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($pendingRequests as $index => $req)
                    @php
                        $searchPendingContent = strtolower(implode(' ', array_filter([
                            $req->test_name,
                            $req->type,
                            $req->consultation?->patient?->full_name,
                            $req->consultation?->patient?->patient_id,
                            $req->status
                        ])));
                    @endphp
                    <div class="group flex flex-col bg-white dark:bg-slate-900 rounded-2xl shadow-sm hover:shadow-xl border {{ $req->status === 'In Progress' ? 'border-emerald-400 dark:border-emerald-600 ring-2 ring-emerald-400/20' : ($req->status === 'Specimen Collected' ? 'border-sky-300 dark:border-sky-700' : 'border-slate-200 dark:border-slate-700/60') }} overflow-hidden transition-all duration-300 relative"
                         x-data="{ openResult: false, openReject: false, openCancel: false }"
                         data-search="{{ $searchPendingContent }}"
                         x-show="!searchPending || $el.dataset.search.includes(searchPending.toLowerCase().trim())">
                        <div class="p-5 flex-1 flex flex-col">
                            <div class="flex justify-between items-start mb-3 gap-2">
                                <div class="flex flex-wrap items-center gap-1.5">
                                    <span class="bg-emerald-100 dark:bg-emerald-900/40 text-emerald-800 dark:text-emerald-400 font-extrabold px-2.5 py-1 rounded-lg text-sm border border-emerald-200 dark:border-emerald-800/30">
                                        {{ $req->test_name }}
                                    </span>
                                    @if($req->is_repeat || $req->parent_id)
                                        <span class="bg-amber-100 dark:bg-amber-900/40 text-amber-700 dark:text-amber-300 font-bold px-2 py-0.5 rounded text-[11px] border border-amber-200 dark:border-amber-800/40" title="Reordered after sample rejection">
                                            Repeat Test
                                        </span>
                                    @endif
                                </div>
                                <span class="text-xs font-bold text-slate-500 dark:text-slate-400 shrink-0">
                                    {{ $req->created_at->diffForHumans() }}
                                </span>
                            </div>

                            <!-- Workflow Status Badge -->
                            <div class="mb-3">
                                @if($req->status === 'Specimen Collected')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-black bg-sky-100 text-sky-800 dark:bg-sky-950/60 dark:text-sky-300 border border-sky-200 dark:border-sky-800/60">
                                        <svg class="w-3.5 h-3.5 text-sky-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                        Specimen Collected ({{ $req->specimen_collected_at?->format('h:i A') ?? 'Done' }})
                                    </span>
                                @elseif($req->status === 'In Progress')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-black bg-teal-100 text-teal-800 dark:bg-teal-950/60 dark:text-teal-300 border border-teal-200 dark:border-teal-800/60 animate-pulse">
                                        <svg class="w-3.5 h-3.5 animate-spin text-teal-600" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                        Processing / In Progress
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-black bg-amber-100 text-amber-800 dark:bg-amber-950/60 dark:text-amber-300 border border-amber-200 dark:border-amber-800/60">
                                        <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                                        {{ $req->type === 'Laboratory' ? 'Awaiting Specimen' : 'Awaiting Patient' }}
                                    </span>
                                @endif
                            </div>

                            <h4 class="text-xl font-extrabold text-slate-900 dark:text-white leading-tight mb-1 truncate">{{ $req->consultation->patient->full_name }}</h4>
                            <div class="flex items-center gap-2 text-xs font-semibold text-slate-500 dark:text-slate-400 mb-4">
                                <span class="text-emerald-600 dark:text-emerald-400">{{ $req->consultation->patient->classification }}</span>
                                <span>&bull;</span>
                                <span>{{ $req->consultation->patient->dob ? \Carbon\Carbon::parse($req->consultation->patient->dob)->age . ' yrs' : '? yrs' }}</span>
                                <span>&bull;</span>
                                <span>{{ $req->consultation->patient->sex ?? 'N/A' }}</span>
                                <span>&bull;</span>
                                <span class="font-mono text-[11px] text-slate-400">{{ $req->consultation->patient->patient_id }}</span>
                            </div>

                            <div class="flex-1 mb-5">
                                <p class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-1">Requested By</p>
                                <div class="flex items-center gap-2">
                                    <div class="w-6 h-6 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 flex items-center justify-center font-bold text-[10px] border border-slate-200 dark:border-slate-700">
                                        {{ strtoupper(substr(str_replace('Dr. ', '', $req->consultation->doctor->name ?? 'U'), 0, 1)) }}
                                    </div>
                                    <p class="text-sm font-semibold text-slate-700 dark:text-slate-300">{{ $req->consultation->doctor->name ?? 'Unassigned' }}</p>
                                </div>
                                @if($req->remarks)
                                <div class="mt-3 bg-yellow-50 dark:bg-yellow-900/20 p-3 rounded-xl border border-yellow-100 dark:border-yellow-900/50">
                                    <p class="text-[10px] font-bold text-yellow-800 dark:text-yellow-500 uppercase tracking-wider mb-0.5">Clinical Remarks</p>
                                    <p class="text-xs text-yellow-900 dark:text-yellow-400 font-medium italic">"{{ $req->remarks }}"</p>
                                </div>
                                @endif
                            </div>

                            <!-- ═══════ DYNAMIC ACTION WORKFLOW BUTTONS ═══════ -->
                            <div class="space-y-2 pt-3 border-t border-slate-100 dark:border-slate-800">
                                <div class="flex items-center gap-2">
                                    @if($req->type === 'Laboratory')
                                        @if($req->status === 'Pending')
                                            {{-- Step 1: Collect Specimen --}}
                                            <form action="{{ route('lab.ancillary.collect-specimen', $req->id) }}" method="POST" class="flex-1" @submit.prevent="submitProgression($event)">
                                                @csrf
                                                <button type="submit" class="w-full flex items-center justify-center gap-1.5 bg-sky-600 hover:bg-sky-700 text-white font-bold py-2.5 px-3 rounded-xl shadow-xs transition text-xs">
                                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/></svg>
                                                    Collect Specimen
                                                </button>
                                            </form>
                                        @elseif($req->status === 'Specimen Collected')
                                            {{-- Step 2: Start Processing --}}
                                            <form action="{{ route('lab.ancillary.start-processing', $req->id) }}" method="POST" class="flex-1" @submit.prevent="submitProgression($event)">
                                                @csrf
                                                <button type="submit" class="w-full flex items-center justify-center gap-1.5 bg-teal-600 hover:bg-teal-700 text-white font-bold py-2.5 px-3 rounded-xl shadow-xs transition text-xs">
                                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                    Start Processing
                                                </button>
                                            </form>
                                        @endif
                                    @else
                                        {{-- Radiology Flow --}}
                                        @if($req->status === 'Pending')
                                            <form action="{{ route('lab.ancillary.start-processing', $req->id) }}" method="POST" class="flex-1" @submit.prevent="submitProgression($event)">
                                                @csrf
                                                <button type="submit" class="w-full flex items-center justify-center gap-1.5 bg-teal-600 hover:bg-teal-700 text-white font-bold py-2.5 px-3 rounded-xl shadow-xs transition text-xs">
                                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                                    Begin Scan / Exam
                                                </button>
                                            </form>
                                        @endif
                                    @endif

                                    {{-- Step 3: Enter Results (Highlighted in emerald) --}}
                                    <button type="button" @click="openResult = true" 
                                            class="flex-1 flex items-center justify-center gap-1.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-2.5 px-3 rounded-xl shadow-xs transition text-xs">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                                        Enter Results
                                    </button>
                                </div>

                                {{-- Secondary Action Bar: Reject, Cancel, Archive --}}
                                <div class="flex items-center justify-between pt-1">
                                    <div class="flex items-center gap-1.5">
                                        @if($req->type === 'Laboratory')
                                            <button type="button" @click="openReject = true" title="Reject compromised sample" class="text-[11px] font-bold text-rose-600 hover:text-rose-700 bg-rose-50 dark:bg-rose-900/20 hover:bg-rose-100 dark:hover:bg-rose-900/40 px-2.5 py-1 rounded-lg border border-rose-200 dark:border-rose-800 transition">
                                                Reject Sample
                                            </button>
                                        @endif
                                        <button type="button" @click="openCancel = true" title="Cancel request" class="text-[11px] font-bold text-slate-600 hover:text-slate-800 dark:text-slate-400 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 px-2.5 py-1 rounded-lg border border-slate-200 dark:border-slate-700 transition">
                                            Cancel
                                        </button>
                                    </div>
                                    <form action="{{ route('lab.ancillary.archive', $req->id) }}" method="POST" @submit.prevent="submitProgression($event)">
                                        @csrf
                                        <button type="submit" title="Archive / No-Show" class="p-1.5 text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 transition">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>
                                        </button>
                                    </form>
                                </div>
                            </div>

                            <!-- ═══════ MODAL 1: RESULT ENTRY MODAL ═══════ -->
                            <template x-teleport="body">
                                <div x-show="openResult" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                                    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                                        <div x-show="openResult" x-transition.opacity class="fixed inset-0 bg-slate-900/75 backdrop-blur-sm transition-opacity" @click="openResult = false"></div>
                                        <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
                                        <div x-show="openResult" x-transition class="inline-block align-bottom bg-white dark:bg-slate-800 rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-3xl w-full border border-slate-200 dark:border-slate-700">
                                            <form action="{{ route('lab.ancillary.complete', $req->id) }}" method="POST" enctype="multipart/form-data" @submit.prevent="submitProgression($event, () => openResult = false)">
                                                @csrf
                                                <div class="bg-white dark:bg-slate-800 px-6 pt-6 pb-6 max-h-[80vh] overflow-y-auto">
                                                    <div class="flex items-center gap-3 mb-6">
                                                        <div class="w-12 h-12 rounded-xl bg-emerald-100 dark:bg-emerald-900/40 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0">
                                                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path></svg>
                                                        </div>
                                                        <div>
                                                            <h3 class="text-xl font-extrabold text-slate-900 dark:text-white">{{ $req->test_name }}</h3>
                                                            <p class="text-sm font-semibold text-slate-500 dark:text-slate-400">Patient: {{ $req->consultation->patient->full_name }} ({{ $req->consultation->patient->patient_id }})</p>
                                                        </div>
                                                    </div>
                                                    
                                                    @if($req->type === 'Laboratory')
                                                        @include('lab.partials.lab-form', ['req' => $req])
                                                    @else
                                                        @include('lab.partials.rad-form', ['req' => $req])
                                                    @endif
                                                    
                                                    <!-- Optional File Upload (All Tests) -->
                                                    <div class="mt-4 pt-4 border-t border-slate-100 dark:border-slate-700"
                                                         x-data="{
                                                             uploadError: null,
                                                             async validateFile(e) {
                                                                 const input = e.target;
                                                                 if (!input.files || input.files.length === 0) {
                                                                     this.uploadError = null;
                                                                     return;
                                                                 }
                                                                 if (window.SecureImageValidator) {
                                                                     const res = await window.SecureImageValidator.validateFile(input.files[0]);
                                                                     if (!res.valid) {
                                                                         this.uploadError = res.message;
                                                                         input.value = '';
                                                                         return;
                                                                     }
                                                                 }
                                                                 this.uploadError = null;
                                                             }
                                                         }">
                                                        <label class="block text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1">Attach Image / Scan (Optional)</label>
                                                        <input type="file" name="result_file" 
                                                               accept=".jpeg,.jpg,.png,.webp,image/jpeg,image/png,image/webp"
                                                               @change="validateFile($event)"
                                                               class="w-full text-sm text-slate-500 dark:text-slate-400 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100 dark:file:bg-emerald-900/50 dark:file:text-emerald-400 transition cursor-pointer">
                                                        <template x-if="uploadError">
                                                            <p class="mt-2 text-xs text-rose-600 dark:text-rose-400 font-semibold flex items-center gap-1.5" role="alert">
                                                                <svg class="w-4 h-4 shrink-0 text-rose-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                                                <span x-text="uploadError"></span>
                                                            </p>
                                                        </template>
                                                        <p x-show="!uploadError" class="mt-1 text-xs text-slate-400">Accepts JPEG, JPG, PNG, and WebP up to 10MB.</p>
                                                    </div>
                                                </div>
                                                <div class="bg-slate-50 dark:bg-slate-800/80 px-6 py-4 border-t border-slate-100 dark:border-slate-700 flex justify-end gap-3 rounded-b-2xl">
                                                    <button type="button" @click="openResult = false" class="px-5 py-2.5 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-600 rounded-xl shadow-sm text-sm font-bold text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 transition">Cancel</button>
                                                    <button type="submit" class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 border border-transparent rounded-xl shadow-sm text-sm font-bold text-white transition focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 flex items-center justify-center gap-2">
                                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                                        Submit Results
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </template>

                            <!-- ═══════ MODAL 2: REJECT SPECIMEN MODAL ═══════ -->
                            <template x-teleport="body">
                                <div x-show="openReject" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                                    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                                        <div x-show="openReject" x-transition.opacity class="fixed inset-0 bg-slate-900/75 backdrop-blur-sm transition-opacity" @click="openReject = false"></div>
                                        <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
                                        <div x-show="openReject" x-transition class="inline-block align-bottom bg-white dark:bg-slate-800 rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md w-full border border-rose-200 dark:border-rose-900">
                                            <form action="{{ route('lab.ancillary.reject', $req->id) }}" method="POST" @submit.prevent="submitProgression($event, () => openReject = false)">
                                                @csrf
                                                <div class="p-6">
                                                    <div class="flex items-center gap-3 mb-4">
                                                        <div class="w-10 h-10 rounded-xl bg-rose-100 dark:bg-rose-900/40 text-rose-600 dark:text-rose-400 flex items-center justify-center shrink-0">
                                                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                                        </div>
                                                        <div>
                                                            <h4 class="font-extrabold text-slate-900 dark:text-white">Reject Laboratory Sample</h4>
                                                            <p class="text-xs text-slate-500">{{ $req->test_name }} &bull; {{ $req->consultation->patient->full_name }}</p>
                                                        </div>
                                                    </div>

                                                    <div class="space-y-4">
                                                        <div>
                                                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Reason for Sample Rejection <span class="text-rose-500">*</span></label>
                                                            <select name="rejection_reason" required class="w-full bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-600 rounded-xl px-3 py-2 text-xs font-semibold text-slate-800 dark:text-white">
                                                                <option value="" disabled selected>-- Select Rejection Criteria --</option>
                                                                <option value="hemolyzed">Hemolyzed Specimen</option>
                                                                <option value="clotted">Clotted Sample (EDTA tube)</option>
                                                                <option value="insufficient_quantity">Quantity Not Sufficient (QNS)</option>
                                                                <option value="improper_container">Improper Container / Additive</option>
                                                                <option value="unlabeled">Unlabeled / Misidentified Tube</option>
                                                                <option value="other">Other Reason</option>
                                                            </select>
                                                        </div>

                                                        <div>
                                                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Rejection Notes & Doctor Guidance</label>
                                                            <textarea name="rejection_notes" rows="3" placeholder="Provide details to attending physician (e.g. please redraw using lavender-top EDTA tube)..." class="w-full bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-600 rounded-xl p-2.5 text-xs text-slate-900 dark:text-white"></textarea>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="bg-slate-50 dark:bg-slate-800/80 px-6 py-3 border-t border-slate-100 dark:border-slate-700 flex justify-end gap-2">
                                                    <button type="button" @click="openReject = false" class="px-4 py-2 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-600 rounded-xl text-xs font-bold text-slate-700 dark:text-slate-300">Cancel</button>
                                                    <button type="submit" class="px-4 py-2 bg-rose-600 hover:bg-rose-700 rounded-xl text-xs font-bold text-white shadow-xs">Confirm Rejection</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </template>

                            <!-- ═══════ MODAL 3: CANCEL REQUEST MODAL ═══════ -->
                            <template x-teleport="body">
                                <div x-show="openCancel" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                                    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                                        <div x-show="openCancel" x-transition.opacity class="fixed inset-0 bg-slate-900/75 backdrop-blur-sm transition-opacity" @click="openCancel = false"></div>
                                        <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
                                        <div x-show="openCancel" x-transition class="inline-block align-bottom bg-white dark:bg-slate-800 rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md w-full border border-slate-200 dark:border-slate-700">
                                            <form action="{{ route('lab.ancillary.cancel', $req->id) }}" method="POST" @submit.prevent="submitProgression($event, () => openCancel = false)">
                                                @csrf
                                                <div class="p-6">
                                                    <h4 class="font-extrabold text-slate-900 dark:text-white mb-2">Cancel Diagnostic Request</h4>
                                                    <p class="text-xs text-slate-500 mb-4">Patient: {{ $req->consultation->patient->full_name }} &bull; Test: {{ $req->test_name }}</p>

                                                    <div>
                                                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Reason for Cancellation <span class="text-rose-500">*</span></label>
                                                        <textarea name="cancellation_reason" required rows="3" placeholder="Explain why this request is cancelled (e.g. patient refused, test contraindicated, requested by doctor)..." class="w-full bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-600 rounded-xl p-2.5 text-xs text-slate-900 dark:text-white"></textarea>
                                                    </div>
                                                </div>
                                                <div class="bg-slate-50 dark:bg-slate-800/80 px-6 py-3 border-t border-slate-100 dark:border-slate-700 flex justify-end gap-2">
                                                    <button type="button" @click="openCancel = false" class="px-4 py-2 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-600 rounded-xl text-xs font-bold text-slate-700 dark:text-slate-300">Back</button>
                                                    <button type="submit" class="px-4 py-2 bg-slate-800 hover:bg-slate-900 text-white rounded-xl text-xs font-bold">Confirm Cancel</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </template>

                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="bg-white dark:bg-slate-900 rounded-3xl border border-dashed border-slate-300 dark:border-slate-700 py-20 px-6 flex flex-col items-center justify-center text-center">
                <div class="w-20 h-20 bg-emerald-50 dark:bg-emerald-900/20 rounded-full flex items-center justify-center mb-5 text-emerald-500 dark:text-emerald-400">
                    <svg class="w-10 h-10" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                </div>
                <h4 class="text-2xl font-extrabold text-slate-900 dark:text-white mb-2">Queue is Clear!</h4>
                <p class="text-slate-500 dark:text-slate-400 max-w-sm mx-auto">There are currently no pending {{ strtolower($type) }} requests. New requests will appear here automatically.</p>
            </div>
        @endif
        </div>
    </div>

    <!-- ═══════════════════ TAB 2: FINISHED TESTS ═══════════════════ -->
    <div x-show="activeTab === 'finished'" style="display: none;" 
         x-transition:enter="transition ease-out duration-200" 
         x-transition:enter-start="opacity-0 translate-y-2" 
         x-transition:enter-end="opacity-100 translate-y-0"
         x-data="{
             searchFinished: '',
             get hasVisibleRows() {
                 if (!this.searchFinished.trim()) return true;
                 let q = this.searchFinished.toLowerCase().trim();
                 return Array.from($el.querySelectorAll('tbody tr[data-search]')).some(tr => tr.dataset.search.includes(q));
             }
         }">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
            <div>
                <h3 class="font-extrabold text-slate-900 dark:text-white text-xl flex items-center gap-2">
                    <span class="bg-emerald-100 dark:bg-emerald-900/50 text-emerald-600 dark:text-emerald-400 p-1.5 rounded-lg">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </span>
                    Completed Diagnostics (Retained for 6+ Months)
                </h3>
                <p class="text-xs text-slate-500 mt-1">Medical laboratory records permanently preserved for audit and historical consultations.</p>
            </div>
            <div class="relative flex items-center">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
                <input type="text" 
                       x-model.debounce.100ms="searchFinished" 
                       placeholder="Search patient, ID, or test..." 
                       class="text-xs rounded-xl border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 pl-9 pr-8 py-2 focus:ring-2 focus:ring-emerald-500 w-64 sm:w-80 transition shadow-xs">
                <button type="button" 
                        x-show="searchFinished" 
                        @click="searchFinished = ''" 
                        class="absolute inset-y-0 right-0 pr-2.5 flex items-center text-slate-400 hover:text-slate-600 text-xs" 
                        title="Clear search">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        </div>

        <div id="finished-tests-section" data-dynamic-block="true">
        @if($completedRequests->count() > 0)
        <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700/60 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 dark:bg-slate-800/50 border-b border-slate-100 dark:border-slate-700/70 text-[10px] sm:text-xs uppercase tracking-wider text-slate-500 dark:text-slate-400 font-extrabold">
                            <th class="p-4 w-[25%]">Test Name</th>
                            <th class="p-4 flex-1">Patient</th>
                            <th class="p-4 w-[20%]">Processed By</th>
                            <th class="p-4 w-[18%]">Completed</th>
                            <th class="p-4 w-[20%] text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700/50 text-sm">
                        @foreach($completedRequests as $req)
                            @php
                                $searchFinishedContent = strtolower(implode(' ', array_filter([
                                     $req->test_name,
                                     $req->type,
                                     $req->consultation?->patient?->full_name,
                                     $req->consultation?->patient?->patient_id,
                                     $req->technician?->name,
                                     $req->amender?->name,
                                     $req->completed_at?->format('M d, Y')
                                ])));
                            @endphp
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/40 transition-colors" 
                                x-data="{ showResult: false, showAmend: false, showHistory: false }"
                                data-search="{{ $searchFinishedContent }}"
                                x-show="!searchFinished || $el.dataset.search.includes(searchFinished.toLowerCase().trim())">
                                <td class="p-4">
                                    <div class="flex items-center gap-1.5 flex-wrap">
                                        <p class="font-bold text-slate-900 dark:text-white">{{ $req->test_name }}</p>
                                        @if($req->amended_at)
                                            <span class="bg-amber-100 dark:bg-amber-900/50 text-amber-800 dark:text-amber-300 text-[10px] font-black px-1.5 py-0.5 rounded border border-amber-300 dark:border-amber-700" title="Amended on {{ $req->amended_at->format('M d, Y h:i A') }}">
                                                AMENDED
                                            </span>
                                        @endif
                                    </div>
                                    <p class="text-[11px] text-slate-400">{{ $req->type }}</p>
                                </td>
                                <td class="p-4">
                                    <p class="font-bold text-slate-900 dark:text-white">{{ $req->consultation->patient->full_name }}</p>
                                    <p class="text-xs text-slate-500 dark:text-slate-400">{{ $req->consultation->patient->patient_id }}</p>
                                </td>
                                <td class="p-4">
                                    <div class="flex items-center gap-2">
                                        <div class="w-7 h-7 rounded-full bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 flex items-center justify-center font-bold text-xs">
                                            {{ strtoupper(substr($req->technician->name ?? 'U', 0, 1)) }}
                                        </div>
                                        <div>
                                            <p class="font-semibold text-slate-800 dark:text-white text-xs">{{ $req->technician->name ?? 'Unknown' }}</p>
                                            @if($req->amended_at && $req->amender)
                                                <p class="text-[10px] text-amber-600 dark:text-amber-400">Corr. by {{ $req->amender->name }}</p>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="p-4">
                                    <span class="text-slate-500 dark:text-slate-400 font-medium text-xs">{{ $req->completed_at->format('M d, h:i A') }}</span>
                                </td>
                                <td class="p-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <button @click="showResult = true" class="text-xs font-bold text-emerald-600 dark:text-emerald-400 hover:bg-emerald-50 dark:hover:bg-emerald-900/30 px-2.5 py-1 rounded-lg border border-emerald-200 dark:border-emerald-800/50 transition">
                                            View
                                        </button>
                                        <button @click="showAmend = true" class="text-xs font-bold text-amber-700 dark:text-amber-400 hover:bg-amber-50 dark:hover:bg-amber-900/30 px-2.5 py-1 rounded-lg border border-amber-300 dark:border-amber-800/50 transition">
                                            Amend
                                        </button>
                                    </div>

                                    <!-- ═══════ VIEW RESULT MODAL (Shared UI as Amend, Read-Only) ═══════ -->
                                    <template x-teleport="body">
                                        <div x-show="showResult" x-transition style="display:none;" class="fixed inset-0 z-50 flex items-center justify-center p-4 text-left overflow-y-auto">
                                            <div class="fixed inset-0 bg-slate-900/75 backdrop-blur-sm" @click="showResult = false"></div>
                                            <div class="relative bg-white dark:bg-slate-800 rounded-3xl shadow-2xl border border-slate-200 dark:border-slate-700 sm:max-w-3xl w-full max-h-[88vh] overflow-y-auto z-10 flex flex-col my-8">
                                                
                                                <!-- Modal Header -->
                                                <div class="p-6 border-b border-slate-100 dark:border-slate-700/80 flex items-start justify-between gap-4 bg-slate-50/50 dark:bg-slate-900/40 rounded-t-3xl">
                                                    <div class="flex items-center gap-3.5">
                                                        <div class="w-12 h-12 rounded-2xl bg-emerald-100 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20 flex items-center justify-center shrink-0 shadow-2xs">
                                                            @if($req->type === 'Radiology')
                                                                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                                            @else
                                                                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/></svg>
                                                            @endif
                                                        </div>
                                                        <div>
                                                            <div class="flex items-center gap-2 flex-wrap">
                                                                <h3 class="text-xl font-extrabold text-slate-900 dark:text-white">{{ $req->test_name }}</h3>
                                                                <span class="inline-flex items-center gap-1 text-[10px] font-black uppercase tracking-wider px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800/60">
                                                                    <svg class="w-2.5 h-2.5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                                                    Verified Result
                                                                </span>
                                                                @if($req->amended_at)
                                                                    <span class="text-[10px] font-black uppercase tracking-wider px-2 py-0.5 rounded-full bg-amber-100 text-amber-800 dark:bg-amber-950/60 dark:text-amber-300 border border-amber-200 dark:border-amber-800/60">
                                                                        Amended
                                                                    </span>
                                                                @endif
                                                            </div>
                                                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5 font-medium">
                                                                Patient: <span class="font-bold text-slate-800 dark:text-slate-200">{{ $req->consultation->patient->full_name }}</span> ({{ $req->consultation->patient->patient_id }}) &bull; Attending: {{ $req->consultation->doctor->name ?? 'Physician' }}
                                                            </p>
                                                        </div>
                                                    </div>
                                                    <button type="button" @click="showResult = false" class="p-2 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-800 transition cursor-pointer">
                                                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                                    </button>
                                                </div>

                                                <!-- Modal Body -->
                                                <div class="p-6 space-y-5 flex-1 overflow-y-auto">
                                                    <div id="lab-result-print-{{ $req->id }}">
                                                        <x-print-layout 
                                                            :isFullPage="false"
                                                            title="Official Diagnostic Examination Report"
                                                            subtitle="{{ $req->test_name }} — {{ $req->type }}"
                                                            :period="$req->completed_at ? $req->completed_at->format('M d, Y') : now()->format('M d, Y')"
                                                            :generatedBy="($req->technician->name ?? auth()->user()->name) . ' — Medical Technologist'"
                                                        >
                                                        <!-- Clinical Metadata Strip -->
                                                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 p-3.5 bg-slate-50 dark:bg-slate-900/50 rounded-2xl border border-slate-200/80 dark:border-slate-700/80 text-xs mb-4">
                                                            <div>
                                                                <span class="block text-[10px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500">Completed Date</span>
                                                                <span class="font-bold text-slate-800 dark:text-slate-200 mt-0.5 block">{{ $req->completed_at ? $req->completed_at->format('M d, Y h:i A') : 'N/A' }}</span>
                                                            </div>
                                                            <div>
                                                                <span class="block text-[10px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500">Processed By</span>
                                                                <span class="font-bold text-slate-800 dark:text-slate-200 mt-0.5 block">{{ $req->technician->name ?? 'Technician' }}</span>
                                                            </div>
                                                            <div>
                                                                <span class="block text-[10px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500">Age / Sex</span>
                                                                <span class="font-bold text-slate-800 dark:text-slate-200 mt-0.5 block">{{ $req->consultation->patient->dob ? \Carbon\Carbon::parse($req->consultation->patient->dob)->age . 'y' : '—' }} / {{ $req->consultation->patient->sex ?? '—' }}</span>
                                                            </div>
                                                            <div>
                                                                <span class="block text-[10px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500">Order Ref</span>
                                                                <span class="font-mono font-bold text-slate-800 dark:text-slate-200 mt-0.5 block">ANC-{{ str_pad($req->id, 5, '0', STR_PAD_LEFT) }}</span>
                                                            </div>
                                                        </div>

                                                        @if($req->amended_at)
                                                            <div class="bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-800/80 rounded-2xl p-4 text-xs mb-4">
                                                                <div class="flex items-center justify-between">
                                                                    <p class="font-bold text-amber-900 dark:text-amber-200 flex items-center gap-1.5">
                                                                        <svg class="w-4 h-4 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                                                        Officially Amended Result
                                                                    </p>
                                                                    <span class="text-[11px] text-amber-700 dark:text-amber-400 font-semibold">{{ $req->amended_at->format('M d, Y h:i A') }}</span>
                                                                </div>
                                                                <p class="text-amber-800 dark:text-amber-300 mt-1 font-medium"><strong>Reason:</strong> {{ $req->amendment_reason }}</p>
                                                                <p class="text-amber-700 dark:text-amber-400 text-[11px] mt-0.5">Amended by {{ $req->amender->name ?? 'Staff' }}</p>
                                                                
                                                                @if($req->previous_result_data)
                                                                    <button type="button" @click="showHistory = !showHistory" class="mt-2 text-[11px] font-bold text-amber-800 dark:text-amber-300 underline cursor-pointer print:hidden">
                                                                        <span x-text="showHistory ? 'Hide Previous Values' : 'View Prior Unamended Values (Audit)'"></span>
                                                                    </button>
                                                                    <div x-show="showHistory" class="mt-2 p-3 bg-white/80 dark:bg-slate-900/80 rounded-xl border border-amber-200 dark:border-amber-900 space-y-1.5">
                                                                        @foreach($req->previous_result_data as $pk => $pv)
                                                                            <div class="flex justify-between text-[11px]">
                                                                                <span class="text-slate-500 font-bold uppercase">{{ str_replace('_', ' ', $pk) }}:</span>
                                                                                <span class="text-slate-700 dark:text-slate-300 font-mono">{{ $pv }}</span>
                                                                            </div>
                                                                        @endforeach
                                                                    </div>
                                                                @endif
                                                            </div>
                                                        @endif

                                                        <!-- ═══════ READONLY RESULT TEMPLATE (Exact Same UI as Amend) ═══════ -->
                                                        <div class="mb-4">
                                                            @if($req->type === 'Laboratory')
                                                                 @include('lab.partials.lab-form', ['req' => $req, 'readonly' => true])
                                                            @else
                                                                 @include('lab.partials.rad-form', ['req' => $req, 'readonly' => true])
                                                            @endif
                                                        </div>

                                                        @if($req->result_file_path)
                                                            <div class="pt-4 border-t border-slate-200 dark:border-slate-700 mb-4 avoid-break">
                                                                 <h5 class="text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">Attached Diagnostic Scan / Image</h5>
                                                                 @php
                                                                     $ext = strtolower(pathinfo($req->result_file_path, PATHINFO_EXTENSION));
                                                                     $isImg = in_array($ext, ['jpg', 'jpeg', 'png', 'webp']);
                                                                 @endphp
                                                                 @if($isImg)
                                                                     <div class="mb-3 rounded-2xl overflow-hidden border border-slate-200 dark:border-slate-700 max-h-72 bg-slate-900 flex items-center justify-center">
                                                                         <img src="{{ Storage::url($req->result_file_path) }}" alt="{{ $req->test_name }}" class="max-h-72 object-contain w-full">
                                                                     </div>
                                                                 @endif
                                                                 <a href="{{ Storage::url($req->result_file_path) }}" target="_blank" class="inline-flex items-center gap-2 text-xs font-bold text-emerald-600 dark:text-emerald-400 hover:text-emerald-800 bg-emerald-50 dark:bg-emerald-900/30 px-3.5 py-2 rounded-xl border border-emerald-200 dark:border-emerald-800/50 transition print:hidden">
                                                                     <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" /></svg>
                                                                     Open Original Image / File in New Tab
                                                                 </a>
                                                            </div>
                                                        @endif

                                                        {{-- Official Diagnostic Signatures Block --}}
                                                        <div class="rhu-signature-strip pt-4 border-t border-slate-200">
                                                            <div class="rhu-signature-box">
                                                                <div class="rhu-signature-line"></div>
                                                                <div class="rhu-signature-name">{{ $req->technician->name ?? 'Medical Technologist' }}</div>
                                                                <div class="rhu-signature-role">Lic. Medical Technologist / RadTech</div>
                                                            </div>
                                                            <div class="rhu-signature-box">
                                                                <div class="rhu-signature-line"></div>
                                                                <div class="rhu-signature-name">{{ $req->consultation->doctor->name ?? 'Medical Officer / Pathologist' }}</div>
                                                                <div class="rhu-signature-role">Attending Physician / Pathologist</div>
                                                            </div>
                                                        </div>
                                                        </x-print-layout>
                                                    </div>
                                                </div>

                                                <!-- Modal Footer -->
                                                <div class="p-4 bg-slate-50 dark:bg-slate-900/60 border-t border-slate-100 dark:border-slate-700/80 flex items-center justify-between gap-3 rounded-b-3xl">
                                                    <button type="button" @click="showResult = false; showAmend = true" class="px-4 py-2 bg-amber-50 hover:bg-amber-100 text-amber-800 dark:bg-amber-950/60 dark:hover:bg-amber-900/60 dark:text-amber-300 font-bold text-xs rounded-xl border border-amber-300 dark:border-amber-700 transition flex items-center gap-1.5 cursor-pointer">
                                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                                        <span>Amend Results</span>
                                                    </button>
                                                    <div class="flex items-center gap-2">
                                                        <button type="button" onclick="window.printIsolated(document.getElementById('lab-result-print-{{ $req->id }}').innerHTML, { title: 'Diagnostic Report - {{ $req->test_name }}' })" class="px-4 py-2 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-600 rounded-xl text-xs font-bold text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 transition flex items-center gap-1.5 cursor-pointer">
                                                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                                                            <span>Print Report</span>
                                                        </button>
                                                        <button type="button" @click="showResult = false" class="px-5 py-2 bg-slate-900 hover:bg-black dark:bg-slate-700 dark:hover:bg-slate-600 text-white font-bold text-xs rounded-xl transition cursor-pointer">
                                                            Close
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </template>

                                    <!-- ═══════ AMENDMENT MODAL ═══════ -->
                                    <template x-teleport="body">
                                        <div x-show="showAmend" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto text-left" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                                            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                                                <div x-show="showAmend" x-transition.opacity class="fixed inset-0 bg-slate-900/75 backdrop-blur-sm transition-opacity" @click="showAmend = false"></div>
                                                <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
                                                <div x-show="showAmend" x-transition class="inline-block align-bottom bg-white dark:bg-slate-800 rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-3xl w-full border border-amber-300 dark:border-amber-700">
                                                    <form action="{{ route('lab.ancillary.amend', $req->id) }}" method="POST" enctype="multipart/form-data" @submit.prevent="submitProgression($event, () => showAmend = false)">
                                                        @csrf
                                                        <div class="bg-white dark:bg-slate-800 px-6 pt-6 pb-6 max-h-[80vh] overflow-y-auto">
                                                            <div class="flex items-center gap-3 mb-6">
                                                                <div class="w-12 h-12 rounded-xl bg-amber-100 dark:bg-amber-900/40 text-amber-600 dark:text-amber-400 flex items-center justify-center shrink-0">
                                                                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                                                </div>
                                                                <div>
                                                                    <h3 class="text-xl font-extrabold text-slate-900 dark:text-white">Amend / Correct Diagnostic Result</h3>
                                                                    <p class="text-sm font-semibold text-slate-500 dark:text-slate-400">Test: {{ $req->test_name }} &bull; Patient: {{ $req->consultation->patient->full_name }}</p>
                                                                </div>
                                                            </div>

                                                            <div class="mb-5 bg-amber-50 dark:bg-amber-950/30 border border-amber-200 dark:border-amber-800/60 p-4 rounded-xl">
                                                                <label class="block text-xs font-black text-amber-900 dark:text-amber-300 uppercase tracking-wider mb-1">
                                                                    Clinical Reason for Amendment <span class="text-rose-500">*</span>
                                                                </label>
                                                                <textarea name="amendment_reason" required rows="2" placeholder="Mandatory audit explanation (e.g. Typographical error corrected in platelet count; sample rerun per physician request)..." class="w-full bg-white dark:bg-slate-900 border border-amber-300 dark:border-amber-700 rounded-lg p-2.5 text-xs text-slate-900 dark:text-white focus:ring-2 focus:ring-amber-500"></textarea>
                                                            </div>

                                                            @if($req->type === 'Laboratory')
                                                                @include('lab.partials.lab-form', ['req' => $req])
                                                            @else
                                                                @include('lab.partials.rad-form', ['req' => $req])
                                                            @endif

                                                            <!-- Optional Replacement File Upload -->
                                                            <div class="mt-4 pt-4 border-t border-slate-100 dark:border-slate-700">
                                                                <label class="block text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1">Replace Image / Scan (Optional)</label>
                                                                <input type="file" name="result_file" accept=".jpeg,.jpg,.png,.webp,image/jpeg,image/png,image/webp" class="w-full text-sm text-slate-500 dark:text-slate-400 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-amber-50 file:text-amber-700 hover:file:bg-amber-100 dark:file:bg-amber-900/50 dark:file:text-amber-400 transition cursor-pointer">
                                                            </div>
                                                        </div>
                                                        <div class="bg-slate-50 dark:bg-slate-800/80 px-6 py-4 border-t border-slate-100 dark:border-slate-700 flex justify-end gap-3 rounded-b-2xl">
                                                            <button type="button" @click="showAmend = false" class="px-5 py-2.5 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-600 rounded-xl shadow-sm text-sm font-bold text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 transition">Cancel</button>
                                                            <button type="submit" class="px-6 py-2.5 bg-amber-600 hover:bg-amber-700 border border-transparent rounded-xl shadow-sm text-sm font-bold text-white transition flex items-center justify-center gap-2">
                                                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                                                Save & Issue Amended Result
                                                            </button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </template>

                                </td>
                            </tr>
                        @endforeach
                        <tr x-show="searchFinished && !hasVisibleRows" style="display: none;">
                            <td colspan="5" class="py-12 text-center text-slate-400 text-sm">
                                <svg class="w-8 h-8 mx-auto mb-2 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                                No completed tests match "<span class="font-bold text-slate-600 dark:text-slate-300" x-text="searchFinished"></span>"
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            @if($completedRequests->hasPages())
                <div class="px-6 py-4 border-t border-slate-100 dark:border-slate-700/80 bg-slate-50/70 dark:bg-slate-900/60">
                    {{ $completedRequests->appends(request()->query())->links('vendor.pagination.shadcn') }}
                </div>
            @endif
        </div>
        @else
            <div class="bg-white dark:bg-slate-900 rounded-3xl border border-dashed border-slate-300 dark:border-slate-700 py-16 px-6 flex flex-col items-center justify-center text-center">
                <div class="w-16 h-16 bg-emerald-50 dark:bg-emerald-900/20 rounded-full flex items-center justify-center mb-4 text-emerald-500">
                    <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <h4 class="text-xl font-extrabold text-slate-900 dark:text-white mb-2">No completed tests found</h4>
                <p class="text-slate-500 dark:text-slate-400 max-w-sm mx-auto">Completed diagnostic records from the last 6 months will appear here.</p>
            </div>
        @endif
        </div>
    </div>

    <!-- ═══════════════════ TAB 3: ARCHIVE / CANCELLED / REJECTED ═══════════════════ -->
    <div x-show="activeTab === 'archive'" style="display: none;" 
         x-transition:enter="transition ease-out duration-200" 
         x-transition:enter-start="opacity-0 translate-y-2" 
         x-transition:enter-end="opacity-100 translate-y-0"
         x-data="{
             searchArchive: '',
             get hasVisibleRows() {
                 if (!this.searchArchive.trim()) return true;
                 let q = this.searchArchive.toLowerCase().trim();
                 return Array.from($el.querySelectorAll('tbody tr[data-search]')).some(tr => tr.dataset.search.includes(q));
             }
         }">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
            <div>
                <h3 class="font-extrabold text-slate-900 dark:text-white text-xl flex items-center gap-2">
                    <span class="bg-slate-200 dark:bg-slate-700 text-slate-500 dark:text-slate-400 p-1.5 rounded-lg">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path></svg>
                    </span>
                    Archive, Cancelled & Rejected Diagnostics (6+ Months Retention)
                </h3>
                <p class="text-xs text-slate-500 mt-1">Includes no-shows, sample rejections, and physician cancellations with 1-click restore to queue.</p>
            </div>
            <div class="relative flex items-center">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
                <input type="text" 
                       x-model.debounce.100ms="searchArchive" 
                       placeholder="Search archived patient, ID, or test..." 
                       class="text-xs rounded-xl border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 pl-9 pr-8 py-2 focus:ring-2 focus:ring-slate-500 w-64 sm:w-80 transition shadow-xs">
                <button type="button" 
                        x-show="searchArchive" 
                        @click="searchArchive = ''" 
                        class="absolute inset-y-0 right-0 pr-2.5 flex items-center text-slate-400 hover:text-slate-600 text-xs" 
                        title="Clear search">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        </div>

        <div id="archive-section" data-dynamic-block="true">
        @if($archivedRequests->count() > 0)
        <div class="bg-white dark:bg-slate-800/80 backdrop-blur-sm rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700/60 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 dark:bg-slate-800/50 border-b border-slate-100 dark:border-slate-700/70 text-[10px] sm:text-xs uppercase tracking-wider text-slate-500 dark:text-slate-400 font-extrabold">
                            <th class="p-4">Test Name</th>
                            <th class="p-4">Patient</th>
                            <th class="p-4">Requested By</th>
                            <th class="p-4">Status & Details</th>
                            <th class="p-4">Date</th>
                            <th class="p-4 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700/50 text-sm">
                        @foreach($archivedRequests as $req)
                            @php
                                $searchArchiveContent = strtolower(implode(' ', array_filter([
                                    $req->test_name,
                                    $req->type,
                                    $req->consultation?->patient?->full_name,
                                    $req->consultation?->patient?->patient_id,
                                    $req->consultation?->doctor?->name,
                                    $req->status,
                                    $req->rejection_reason,
                                    $req->rejection_notes,
                                    $req->cancellation_reason,
                                    $req->rejector?->name,
                                    $req->canceller?->name,
                                    $req->created_at?->format('M d, Y')
                                ])));
                            @endphp
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/40 transition-colors"
                                data-search="{{ $searchArchiveContent }}"
                                x-show="!searchArchive || $el.dataset.search.includes(searchArchive.toLowerCase().trim())">
                                <td class="p-4">
                                    <p class="font-bold text-slate-900 dark:text-white">{{ $req->test_name }}</p>
                                    <p class="text-[10px] font-semibold text-slate-400 uppercase">{{ $req->type }}</p>
                                </td>
                                <td class="p-4">
                                    <p class="font-bold text-slate-900 dark:text-white">{{ $req->consultation->patient->full_name ?? 'Unknown' }}</p>
                                    <p class="text-xs text-slate-500 dark:text-slate-400">{{ $req->consultation->patient->patient_id ?? '' }}</p>
                                </td>
                                <td class="p-4">
                                    <p class="text-sm font-semibold text-slate-700 dark:text-slate-300">{{ $req->consultation->doctor->name ?? 'Unassigned' }}</p>
                                </td>
                                <td class="p-4">
                                    @if($req->status === 'Rejected')
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-rose-100 dark:bg-rose-900/30 text-rose-700 dark:text-rose-400">
                                            Sample Rejected: {{ ucwords(str_replace('_', ' ', $req->rejection_reason ?? 'Compromised')) }}
                                        </span>
                                        @if($req->rejection_notes)
                                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 italic font-medium">"{{ $req->rejection_notes }}"</p>
                                        @endif
                                        @if($req->rejector)
                                            <p class="text-[10px] text-slate-400 mt-0.5">By {{ $req->rejector->formatted_name }}</p>
                                        @endif
                                    @elseif($req->status === 'Cancelled')
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300">
                                            Cancelled
                                        </span>
                                        @if($req->cancellation_reason)
                                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 italic font-medium">"{{ $req->cancellation_reason }}"</p>
                                        @endif
                                        @if($req->canceller)
                                            <p class="text-[10px] text-slate-400 mt-0.5">By {{ $req->canceller->formatted_name }}</p>
                                        @endif
                                    @elseif($req->archived_at)
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-extrabold {{ $req->archived_reason === 'manual' ? 'bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400' : 'bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300' }}">
                                            {{ $req->archived_reason === 'manual' ? 'Manually Archived' : 'Auto-Archived (No-Show)' }}
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-rose-100 dark:bg-rose-900/30 text-rose-700 dark:text-rose-400">
                                            No-Show (Past Date)
                                        </span>
                                    @endif
                                </td>
                                <td class="p-4">
                                    <span class="text-slate-500 dark:text-slate-400 font-medium text-xs">{{ $req->created_at->format('M d, Y h:i A') }}</span>
                                </td>
                                <td class="p-4 text-right">
                                    <form action="{{ route('lab.ancillary.restore', $req->id) }}" method="POST" class="inline" @submit.prevent="submitProgression($event)">
                                        @csrf
                                        <button type="submit" class="text-xs font-bold text-emerald-600 dark:text-emerald-400 hover:underline bg-emerald-50 dark:bg-emerald-900/30 px-3 py-1.5 rounded-lg border border-emerald-200 dark:border-emerald-800/50 transition hover:bg-emerald-100">
                                            Restore to Queue
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                        <tr x-show="searchArchive && !hasVisibleRows" style="display: none;">
                            <td colspan="6" class="py-12 text-center text-slate-400 text-sm">
                                <svg class="w-8 h-8 mx-auto mb-2 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                                No archived records match "<span class="font-bold text-slate-600 dark:text-slate-300" x-text="searchArchive"></span>"
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            @if($archivedRequests->hasPages())
                <div class="px-6 py-4 border-t border-slate-100 dark:border-slate-700/80 bg-slate-50/70 dark:bg-slate-900/60">
                    {{ $archivedRequests->appends(request()->query())->links('vendor.pagination.shadcn') }}
                </div>
            @endif
        </div>
        @else
            <div class="bg-white dark:bg-slate-900 rounded-3xl border border-dashed border-slate-300 dark:border-slate-700 py-16 px-6 flex flex-col items-center justify-center text-center">
                <div class="w-16 h-16 bg-slate-100 dark:bg-slate-800 rounded-full flex items-center justify-center mb-4 text-slate-400">
                    <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path></svg>
                </div>
                <h4 class="text-xl font-extrabold text-slate-900 dark:text-white mb-2">Archive is empty</h4>
                <p class="text-slate-500 dark:text-slate-400 max-w-sm mx-auto">No-show, cancelled, or rejected requests will appear here.</p>
            </div>
        @endif
        </div>
</div>

<script>
function labDashboard() {
    return {
        activeTab: '{{ request()->has("archived_page") ? "archive" : (request()->has("completed_page") ? "finished" : "") }}' || ((window.location.hash && ['#pending', '#finished', '#archive'].includes(window.location.hash)) ? window.location.hash.replace('#', '') : 'pending'),
        toastMessage: '',
        toastType: 'success',
        setTab(tab) {
            this.activeTab = tab;
            history.replaceState(null, '', '#' + tab);
        },
        showToast(msg, type = 'success') {
            this.toastMessage = msg;
            this.toastType = type;
            setTimeout(() => { if (this.toastMessage === msg) this.toastMessage = ''; }, 4000);
        },
        async submitProgression(e, closeCallback = null) {
            if (e && e.preventDefault) e.preventDefault();
            const form = e ? (e.target.tagName === 'FORM' ? e.target : e.target.closest('form')) : null;
            if (!form) return;

            const submitBtn = form.querySelector('button[type="submit"]') || form.querySelector('button');
            const origHtml = submitBtn ? submitBtn.innerHTML : '';
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.style.opacity = '0.7';
            }

            try {
                const formData = new FormData(form);
                const csrfMeta = document.querySelector('meta[name="csrf-token"]');
                const csrfToken = csrfMeta ? csrfMeta.content : '';
                const response = await fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: formData
                });

                const data = await response.json().catch(() => null);

                if (response.ok && (!data || data.success !== false)) {
                    if (typeof closeCallback === 'function') closeCallback();
                    this.showToast(data?.message || 'Progression saved successfully.', 'success');
                    await this.refreshDashboard();
                } else {
                    const errorText = data?.message || (data?.errors ? Object.values(data.errors).flat().join(' ') : 'Failed to update request.');
                    this.showToast(errorText, 'error');
                }
            } catch (err) {
                this.showToast('Network error while processing request.', 'error');
            } finally {
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.style.opacity = '1';
                    submitBtn.innerHTML = origHtml;
                }
            }
        },
        async refreshDashboard() {
            try {
                // Remove closed/orphaned dialogs from document.body before re-rendering
                document.querySelectorAll('body > div[role="dialog"]').forEach(dialog => {
                    if (window.getComputedStyle(dialog).display === 'none') {
                        dialog.remove();
                    }
                });

                // Build clean URL without hash, with cache-busting timestamp
                const url = new URL(window.location.pathname, window.location.origin);
                url.search = window.location.search;
                url.searchParams.set('_t', Date.now().toString());

                const res = await fetch(url.toString(), {
                    cache: 'no-store',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Cache-Control': 'no-cache, no-store, must-revalidate',
                        'Pragma': 'no-cache'
                    }
                });
                if (!res.ok) return;
                const html = await res.text();
                const doc = new DOMParser().parseFromString(html, 'text/html');

                const blockIds = ['pending-queue-section', 'finished-tests-section', 'archive-section', 'tab-counts-section', 'lab-stats-section'];
                blockIds.forEach(id => {
                    const oldEl = document.getElementById(id);
                    const newEl = doc.getElementById(id);
                    if (oldEl && newEl) {
                        oldEl.innerHTML = newEl.innerHTML;
                        if (window.Alpine && window.Alpine.initTree) {
                            window.Alpine.initTree(oldEl);
                        }
                    }
                });
            } catch (e) {
                console.error('Error refreshing diagnostic queue:', e);
            }
        }
    };
}
</script>
@endsection
