@extends('layouts.nurse')

@section('header', 'Nurse Triage Queue')

@section('content')
<div class="max-w-7xl mx-auto space-y-6 pb-16"
     x-data="{
        searchFilter: '',
        categoryFilter: 'all',
        forwardModalOpen: false,
        forwardTargetId: null,
        forwardTargetName: '',
        forwardTargetQueue: '',
        forwardActionUrl: '',
        cancelModalOpen: false,
        cancelTargetId: null,
        cancelTargetName: '',
        cancelTargetQueue: '',
        cancelActionUrl: '',
        init() {
            window.addEventListener('triage-global-search', (e) => {
                this.searchFilter = e.detail;
            });
            window.addEventListener('nurse-global-search', (e) => {
                this.searchFilter = e.detail;
            });
        },
        matches(card) {
            const q = this.searchFilter.toLowerCase().trim();
            const matchesSearch = !q || 
                card.name.toLowerCase().includes(q) || 
                card.queueNum.toLowerCase().includes(q) || 
                card.patientId.toLowerCase().includes(q) || 
                card.symptoms.toLowerCase().includes(q) ||
                card.classification.toLowerCase().includes(q);

            if (!matchesSearch) return false;

            if (this.categoryFilter === 'all') return true;
            if (this.categoryFilter === 'active') return card.status === 'active';
            if (this.categoryFilter === 'priority') return card.isPriority;
            if (this.categoryFilter === 'regular') return !card.isPriority && card.status !== 'active';
            return true;
        }
     }">

    {{-- Breadcrumb Navigation --}}
    <x-breadcrumb :items="['Nurse Portal' => '', 'Nurse Triage Queue' => '']" />

    <!-- Header Section (Content Management Style) -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 pb-4 border-b border-slate-200/80 dark:border-slate-800">
        <div>
            <h1 class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white tracking-tight flex items-center gap-2.5">
                <span class="w-9 h-9 sm:w-10 sm:h-10 rounded-2xl bg-emerald-100/80 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0 border border-emerald-500/20 shadow-2xs">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                    </svg>
                </span>
                <span>Nurse Triage Queue</span>
            </h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Real-time consultation queue, patient intake evaluation, and physician clinical referral.</p>
        </div>

        <div class="flex items-center gap-2.5">
            <span class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl border border-emerald-500/20 bg-white dark:bg-slate-900 text-emerald-700 dark:text-emerald-400 text-xs font-semibold shadow-2xs">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                <span>Triage Session Active</span>
            </span>
        </div>
    </div>

    {{-- Top Metrics Bento Grid --}}
    @php
        $priorityCount = $queue->filter(fn($c) => in_array($c->classification ?? $c->patient?->classification, ['Senior Citizen', 'PWD']) || str_starts_with($c->queue_number, 'PED-E'))->count();
        $activeCount = $queue->where('status', 'active')->count();
        $waitingCount = $queue->where('status', 'queued')->count();
    @endphp
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-4 sm:gap-5">
        
        {{-- Card 1: Active Triage Queue --}}
        <div class="lg:col-span-3 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 p-6 shadow-xs hover:shadow-md hover:border-slate-300 dark:hover:border-slate-700/80 transition-all duration-200 flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-3">
                    <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Waiting Patients</span>
                    <span class="w-10 h-10 rounded-2xl bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center border border-emerald-100 dark:border-emerald-800/40 shadow-2xs">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    </span>
                </div>
                <div class="flex items-baseline gap-2">
                    <span class="font-display text-4xl sm:text-5xl font-extrabold text-slate-900 dark:text-white tracking-tight">{{ count($queue) }}</span>
                    <span class="text-xs font-semibold text-slate-500 dark:text-slate-400">in queue</span>
                </div>
            </div>
            <div class="mt-4 pt-3.5 border-t border-slate-100 dark:border-slate-800/80 flex items-center gap-2 text-[11px] font-bold text-slate-600 dark:text-slate-400">
                <span class="inline-flex items-center gap-1.5 text-yellow-600 dark:text-yellow-400">
                    <span class="w-2 h-2 rounded-full bg-yellow-500"></span>
                    {{ $activeCount }} in progress
                </span>
                <span class="text-slate-300 dark:text-slate-700">•</span>
                <span class="inline-flex items-center gap-1.5 text-emerald-700 dark:text-emerald-400">
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                    {{ $waitingCount }} waiting
                </span>
            </div>
        </div>

        {{-- Card 2: Priority & Senior/PWD --}}
        <div class="lg:col-span-3 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 p-6 shadow-xs hover:shadow-md hover:border-amber-300 dark:hover:border-amber-600/70 transition-all duration-200 flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-3">
                    <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Priority Patients</span>
                    <span class="w-10 h-10 rounded-2xl bg-amber-50 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400 flex items-center justify-center border border-amber-100 dark:border-amber-800/40 shadow-2xs">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    </span>
                </div>
                <div class="flex items-baseline gap-2">
                    <span class="font-display text-4xl sm:text-5xl font-extrabold text-amber-600 dark:text-amber-400 tracking-tight">{{ $priorityCount }}</span>
                    <span class="text-xs font-semibold text-slate-500 dark:text-slate-400">priority cases</span>
                </div>
            </div>
            <div class="mt-4 pt-3.5 border-t border-slate-100 dark:border-slate-800/80 text-[11px] font-medium text-slate-500 dark:text-slate-400 truncate">
                Senior Citizens, PWD & urgent cases
            </div>
        </div>

        {{-- Card 3: Completed Today --}}
        <div class="lg:col-span-3 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 p-6 shadow-xs hover:shadow-md hover:border-slate-300 dark:hover:border-slate-700/80 transition-all duration-200 flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-3">
                    <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Completed Today</span>
                    <span class="w-10 h-10 rounded-2xl bg-teal-50 dark:bg-teal-950/60 text-teal-600 dark:text-teal-400 flex items-center justify-center border border-teal-100 dark:border-teal-800/40 shadow-2xs">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </span>
                </div>
                <div class="flex items-baseline gap-2">
                    <span class="font-display text-4xl sm:text-5xl font-extrabold text-slate-900 dark:text-white tracking-tight">{{ $handledCount ?? count($handledPatients) }}</span>
                    <span class="text-xs font-semibold text-slate-500 dark:text-slate-400">patients finished</span>
                </div>
            </div>
            <div class="mt-4 pt-3.5 border-t border-slate-100 dark:border-slate-800/80 text-[11px] font-medium text-slate-500 dark:text-slate-400 truncate">
                Triaged, consulted or referred
            </div>
        </div>

        {{-- Card 4: Nurse On-Duty Banner --}}
        <div class="lg:col-span-3 rounded-3xl bg-gradient-to-br from-emerald-700 via-emerald-800 to-teal-900 text-white p-6 shadow-md relative overflow-hidden flex flex-col justify-between">
            <div class="absolute -top-12 -right-12 w-32 h-32 bg-white/10 rounded-full blur-2xl pointer-events-none"></div>
            <div class="flex items-center gap-3 mb-3 relative z-10">
                <div class="w-12 h-12 rounded-2xl bg-white/15 backdrop-blur-md border border-white/20 flex items-center justify-center overflow-hidden shrink-0 shadow-inner">
                    @if(auth()->user()->avatar_url)
                        <img src="{{ auth()->user()->avatar_url }}" alt="{{ auth()->user()->name }}" class="w-full h-full object-cover">
                    @else
                        <span class="text-base font-black text-white">{{ auth()->user()->initials }}</span>
                    @endif
                </div>
                <div class="min-w-0">
                    <h2 class="text-sm font-extrabold text-white truncate leading-snug">{{ $user->clean_full_name }}</h2>
                    <p class="text-[11px] text-emerald-200 truncate capitalize">Clinical Triage Nurse</p>
                </div>
            </div>
            <div class="pt-3 border-t border-white/15 flex items-center justify-between text-xs relative z-10">
                <div class="flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 shadow-[0_0_8px_rgba(52,211,153,0.9)] animate-pulse"></span>
                    <span class="font-bold text-emerald-100 text-[11px]">{{ auth()->user()->status ?? 'Online' }}</span>
                </div>
                <span class="text-[11px] text-emerald-200/90 font-medium">{{ \Carbon\Carbon::today()->format('M d, Y') }}</span>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="p-4 rounded-2xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800/60 flex items-center gap-3 text-xs sm:text-sm font-bold text-emerald-800 dark:text-emerald-300 shadow-2xs">
            <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    {{-- Interactive Queue Section --}}
    <div class="space-y-4">
        {{-- Section Header & Filter Toolbar --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pt-2">
            <div>
                <h3 class="font-display text-xl sm:text-2xl font-extrabold text-slate-900 dark:text-white flex items-center gap-2.5 tracking-tight">
                    <span class="w-8 h-8 rounded-xl bg-emerald-100 dark:bg-emerald-900/50 text-emerald-700 dark:text-emerald-300 flex items-center justify-center text-sm">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    </span>
                    <span>Patient Triage Queue</span>
                </h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Live clinical intake prioritized by triage protocol (2:1 priority ratio)</p>
            </div>

            {{-- Controls: Search + Filter Chips --}}
            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2.5">
                {{-- Search Input --}}
                <div class="relative min-w-[200px] sm:min-w-[240px]">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </div>
                    <input type="text"
                           x-model="searchFilter"
                           placeholder="Search queue or patient..."
                           class="w-full pl-9 pr-8 py-1.5 rounded-xl border border-slate-200 dark:border-slate-700/80 bg-white dark:bg-slate-900 text-slate-900 dark:text-white text-xs placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all shadow-2xs no-uppercase">
                    <button type="button"
                            x-show="searchFilter.length > 0"
                            @click="searchFilter = ''"
                            class="absolute inset-y-0 right-0 pr-2.5 flex items-center text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 cursor-pointer">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                {{-- Filter Chips --}}
                <div class="inline-flex items-center p-1 rounded-2xl bg-slate-100 dark:bg-slate-800/80 border border-slate-200/80 dark:border-slate-700/70 overflow-x-auto max-w-full">
                    <button type="button" 
                            @click="categoryFilter = 'all'"
                            class="px-3 py-1.5 rounded-xl text-xs font-bold transition-all cursor-pointer whitespace-nowrap"
                            :class="categoryFilter === 'all' ? 'bg-white dark:bg-slate-900 text-emerald-700 dark:text-emerald-400 shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white'">
                        All ({{ count($queue) }})
                    </button>
                    <button type="button" 
                            @click="categoryFilter = 'active'"
                            class="px-3 py-1.5 rounded-xl text-xs font-bold transition-all cursor-pointer whitespace-nowrap"
                            :class="categoryFilter === 'active' ? 'bg-white dark:bg-slate-900 text-yellow-600 dark:text-yellow-400 shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white'">
                        In Progress ({{ $activeCount }})
                    </button>
                    <button type="button" 
                            @click="categoryFilter = 'priority'"
                            class="px-3 py-1.5 rounded-xl text-xs font-bold transition-all cursor-pointer whitespace-nowrap"
                            :class="categoryFilter === 'priority' ? 'bg-white dark:bg-slate-900 text-amber-600 dark:text-amber-400 shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white'">
                        Priority ({{ $priorityCount }})
                    </button>
                    <button type="button" 
                            @click="categoryFilter = 'regular'"
                            class="px-3 py-1.5 rounded-xl text-xs font-bold transition-all cursor-pointer whitespace-nowrap"
                            :class="categoryFilter === 'regular' ? 'bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-200 shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white'">
                        Regular ({{ count($queue) - $priorityCount }})
                    </button>
                </div>
            </div>
        </div>

        {{-- Live Queue Container --}}
        <div id="patient-queue-section" data-dynamic-block="true">
            @if(count($queue) > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                    @foreach($queue as $index => $consultation)
                        @php
                            $isEmergency = \Illuminate\Support\Str::startsWith($consultation->queue_number, 'PED-E');
                            $isActive = $consultation->status === 'active';
                            $isPriority = in_array($consultation->patient->classification, ['Senior Citizen', 'PWD']) || $isEmergency;
                            $tempVal = floatval($consultation->preTriage?->temperature ?? 0);
                            $isFebrile = $tempVal > 37.5;
                        @endphp
                        
                        <div class="group flex flex-col rounded-3xl bg-white dark:bg-slate-900 border transition-all duration-300 relative overflow-hidden shadow-xs hover:shadow-md"
                             :class="{
                                 'border-yellow-400 dark:border-yellow-500/80 ring-2 ring-yellow-400/30': '{{ $isActive }}' === '1',
                                 'border-rose-400 dark:border-rose-500/80 ring-2 ring-rose-400/30': '{{ $isEmergency }}' === '1',
                                 'border-amber-300 dark:border-amber-600/70': '{{ !$isActive && !$isEmergency && $isPriority }}' === '1',
                                 'border-slate-200/90 dark:border-slate-800 hover:border-emerald-300 dark:hover:border-emerald-700': '{{ !$isActive && !$isEmergency && !$isPriority }}' === '1'
                             }"
                             x-show="matches({
                                 name: '{{ addslashes($consultation->patient->full_name) }}',
                                 queueNum: '{{ $consultation->queue_number }}',
                                 patientId: '{{ $consultation->patient->patient_id }}',
                                 symptoms: '{{ addslashes($consultation->preTriage?->symptoms ?? '') }}',
                                 classification: '{{ addslashes($consultation->patient->classification ?? '') }}',
                                 status: '{{ $consultation->status }}',
                                 isPriority: {{ $isPriority ? 'true' : 'false' }}
                             })"
                             x-transition>

                            {{-- Card Header: Position, Queue Number & Priority Badge --}}
                            <div class="flex items-center justify-between p-4 border-b {{ $isActive ? 'border-yellow-200/80 dark:border-yellow-900/40 bg-yellow-50/70 dark:bg-yellow-950/30' : ($isEmergency ? 'border-rose-200/80 dark:border-rose-900/40 bg-rose-50/70 dark:bg-rose-950/30' : 'border-slate-100 dark:border-slate-800/80 bg-slate-50/60 dark:bg-slate-800/40') }}">
                                <div class="flex items-center gap-2.5">
                                    <span class="flex items-center justify-center w-7 h-7 rounded-xl {{ $isActive ? 'bg-yellow-500 text-white' : ($isEmergency ? 'bg-rose-600 text-white' : 'bg-slate-800 text-white dark:bg-slate-700') }} text-xs font-extrabold shadow-2xs">
                                        #{{ $index + 1 }}
                                    </span>
                                    <span class="font-mono text-xs font-black px-2.5 py-1 rounded-lg bg-white dark:bg-slate-800 text-slate-900 dark:text-white border border-slate-200 dark:border-slate-700 shadow-2xs">
                                        {{ $consultation->queue_number }}
                                    </span>
                                    @include('partials.queue-wait-indicator', ['createdAt' => $consultation->created_at])
                                </div>

                                <div>
                                    @if($isEmergency)
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-black uppercase tracking-wider bg-rose-600 text-white shadow-2xs animate-pulse">
                                            <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                            Emergency
                                        </span>
                                    @elseif($isActive)
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-black uppercase tracking-wider bg-yellow-400 text-yellow-900 shadow-2xs">
                                            <span class="w-1.5 h-1.5 rounded-full bg-yellow-800 animate-ping"></span>
                                            In Progress
                                        </span>
                                    @elseif($isPriority)
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-black uppercase tracking-wider bg-amber-100 text-amber-800 dark:bg-amber-950/70 dark:text-amber-300 border border-amber-300 dark:border-amber-700/80 shadow-2xs">
                                            Priority
                                        </span>
                                    @endif
                                </div>
                            </div>

                            {{-- Card Body --}}
                            <div class="p-5 flex-1 flex flex-col justify-between space-y-4">
                                <div>
                                    <div class="flex items-start justify-between gap-2">
                                        <div class="min-w-0 flex-1">
                                            <h4 class="text-lg font-extrabold text-slate-900 dark:text-white leading-snug truncate" title="{{ $consultation->patient->full_name }}">
                                                {{ $consultation->patient->full_name }}
                                            </h4>
                                            <div class="flex items-center gap-1.5 text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                                                <span class="font-semibold text-emerald-700 dark:text-emerald-400">{{ $consultation->patient->patient_id }}</span>
                                                <span>•</span>
                                                <span>{{ $consultation->patient->dob ? \Carbon\Carbon::parse($consultation->patient->dob)->age . 'y' : 'N/A' }}</span>
                                                <span>•</span>
                                                <span>{{ $consultation->patient->sex }}</span>
                                            </div>
                                        </div>

                                        <span class="text-[10px] font-bold px-2 py-0.5 rounded-md border shrink-0 {{ in_array($consultation->patient->classification, ['Senior Citizen', 'PWD']) ? 'bg-amber-50 text-amber-800 dark:bg-amber-950/60 dark:text-amber-300 border-amber-200 dark:border-amber-800' : 'bg-slate-50 text-slate-700 dark:bg-slate-800 dark:text-slate-300 border-slate-200 dark:border-slate-700' }}">
                                            {{ $consultation->patient->classification }}
                                        </span>
                                    </div>

                                    {{-- Vitals Capsule --}}
                                    <div class="grid grid-cols-4 gap-1.5 mt-3.5 p-2.5 rounded-2xl bg-slate-50/80 dark:bg-slate-800/60 border border-slate-200/60 dark:border-slate-700/60 text-center">
                                        <div>
                                            <span class="block text-[9px] font-bold uppercase tracking-wider text-slate-400">BP</span>
                                            <span class="text-xs font-extrabold text-slate-800 dark:text-slate-200">{{ $consultation->preTriage?->blood_pressure ?? '--' }}</span>
                                        </div>
                                        <div class="border-l border-slate-200/80 dark:border-slate-700/70">
                                            <span class="block text-[9px] font-bold uppercase tracking-wider text-slate-400">Temp</span>
                                            <span class="text-xs font-extrabold {{ $isFebrile ? 'text-rose-600 dark:text-rose-400 font-black' : 'text-slate-800 dark:text-slate-200' }}">
                                                {{ $consultation->preTriage?->temperature ? $consultation->preTriage->temperature . '°C' : '--' }}
                                            </span>
                                        </div>
                                        <div class="border-l border-slate-200/80 dark:border-slate-700/70">
                                            <span class="block text-[9px] font-bold uppercase tracking-wider text-slate-400">HR</span>
                                            <span class="text-xs font-extrabold text-slate-800 dark:text-slate-200">{{ $consultation->preTriage?->heart_rate ? $consultation->preTriage->heart_rate . ' bpm' : '--' }}</span>
                                        </div>
                                        <div class="border-l border-slate-200/80 dark:border-slate-700/70">
                                            <span class="block text-[9px] font-bold uppercase tracking-wider text-slate-400">SpO2</span>
                                            <span class="text-xs font-extrabold text-slate-800 dark:text-slate-200">
                                                {{ $consultation->preTriage?->spo2 ?? $consultation->preTriage?->oxygen_saturation ?? '--' }}{{ ($consultation->preTriage?->spo2 || $consultation->preTriage?->oxygen_saturation) ? '%' : '' }}
                                            </span>
                                        </div>
                                    </div>

                                    {{-- Chief Complaint Box --}}
                                    <div class="mt-3 p-3 rounded-2xl bg-white dark:bg-slate-800/80 border border-slate-100 dark:border-slate-800 text-left">
                                        <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 flex items-center gap-1 mb-1">
                                            <svg class="w-3 h-3 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/></svg>
                                            Chief Complaint
                                        </span>
                                        <p class="text-xs text-slate-700 dark:text-slate-300 italic line-clamp-2 leading-relaxed">
                                            "{{ $consultation->preTriage?->symptoms ?: 'Standard consultation requested.' }}"
                                        </p>
                                    </div>
                                </div>

                                {{-- Action Buttons --}}
                                <div class="pt-2 flex items-center gap-2">
                                    <a href="{{ route('nurse.consultation.start', $consultation->id) }}"
                                       class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold text-white shadow-sm transition-all cursor-pointer {{ $isActive ? 'bg-gradient-to-r from-yellow-500 to-amber-600 hover:from-yellow-600 hover:to-amber-700 text-yellow-950 font-black' : 'bg-gradient-to-r from-teal-600 to-emerald-700 hover:from-teal-700 hover:to-emerald-800' }}">
                                        <span>{{ $isActive ? 'Resume Triage' : 'Admit Patient' }}</span>
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                    </a>
                                    
                                    <button type="button" 
                                            @click="forwardModalOpen = true; forwardTargetId = {{ $consultation->id }}; forwardTargetName = '{{ addslashes($consultation->patient->full_name) }}'; forwardTargetQueue = '{{ $consultation->queue_number }}'; forwardActionUrl = '{{ route('nurse.forward', $consultation->id) }}'"
                                            class="px-3.5 py-2.5 rounded-xl text-slate-700 dark:text-slate-300 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 border border-slate-200 dark:border-slate-700 text-xs font-bold transition-all cursor-pointer flex items-center gap-1.5 shadow-2xs"
                                            title="Forward patient to a physician">
                                        <svg class="w-3.5 h-3.5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                                        <span>Forward</span>
                                    </button>

                                    <button type="button" 
                                            @click="cancelModalOpen = true; cancelTargetId = {{ $consultation->id }}; cancelTargetName = '{{ addslashes($consultation->patient->full_name) }}'; cancelTargetQueue = '{{ $consultation->queue_number }}'; cancelActionUrl = '{{ route('nurse.consultation.cancel', $consultation->id) }}'"
                                            title="Mark as Walkout / Cancel Consultation"
                                            class="p-2.5 rounded-xl text-rose-600 hover:text-white hover:bg-rose-600 border border-rose-200 dark:border-rose-800/80 transition-all cursor-pointer shrink-0 shadow-2xs">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="rounded-3xl border border-dashed border-slate-200 dark:border-slate-800 bg-white/70 dark:bg-slate-900/60 p-12 sm:p-16 text-center">
                    <div class="w-16 h-16 rounded-2xl bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center mx-auto mb-4 shadow-inner">
                        <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    </div>
                    <h4 class="font-display text-xl font-bold text-slate-900 dark:text-white">Active Queue is Clear</h4>
                    <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 max-w-sm mx-auto mt-1">There are no waiting patients in your triage queue at the moment.</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Awaiting Labs / Diagnostics Section -->
    @if($awaitingLabs->count() > 0)
    <div class="pt-6">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h3 class="font-display text-lg sm:text-xl font-extrabold text-slate-900 dark:text-white flex items-center gap-2.5 tracking-tight">
                    <span class="w-8 h-8 rounded-xl bg-indigo-100 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400 flex items-center justify-center text-sm border border-indigo-500/20 shadow-2xs">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/></svg>
                    </span>
                    <span>Awaiting Diagnostic Results</span>
                </h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Patients paused in triage while lab specimens or x-ray imaging are being processed.</p>
            </div>
            <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-indigo-100 dark:bg-indigo-950/70 text-indigo-700 dark:text-indigo-300 border border-indigo-200 dark:border-indigo-800">
                {{ $awaitingLabs->count() }} On Hold
            </span>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
            @foreach($awaitingLabs as $consultation)
                <div class="group flex flex-col rounded-3xl bg-white dark:bg-slate-900 border border-indigo-200/90 dark:border-indigo-800/60 p-5 shadow-xs hover:shadow-md hover:border-indigo-400 transition-all duration-300 relative overflow-hidden">
                    <div class="flex items-center justify-between mb-3">
                        <span class="font-mono text-xs font-black px-2.5 py-1 rounded-lg bg-indigo-50 dark:bg-indigo-950/60 text-indigo-700 dark:text-indigo-300 border border-indigo-200 dark:border-indigo-800/60 shadow-2xs">
                            {{ $consultation->queue_number }}
                        </span>
                        <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-indigo-100 dark:bg-indigo-950/80 text-indigo-700 dark:text-indigo-300 uppercase tracking-wider flex items-center gap-1">
                            <span class="w-1.5 h-1.5 rounded-full bg-indigo-500 animate-pulse"></span>
                            Lab Paused
                        </span>
                    </div>

                    <h4 class="text-base font-extrabold text-slate-900 dark:text-white leading-tight mb-1 truncate" title="{{ $consultation->patient->full_name }}">
                        {{ $consultation->patient->full_name }}
                    </h4>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mb-4">
                        {{ $consultation->patient->patient_id }} • {{ $consultation->patient->classification }}
                    </p>

                    <div class="mt-auto flex items-center gap-2">
                        <a href="{{ route('nurse.consultation.start', $consultation->id) }}" class="flex-1 inline-flex items-center justify-center gap-1.5 px-3 py-2 rounded-xl bg-slate-900 dark:bg-slate-800 hover:bg-emerald-600 dark:hover:bg-emerald-600 text-white text-xs font-bold shadow-2xs transition-colors">
                            <span>Resume</span>
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                        </a>
                        <button type="button" 
                                @click="forwardModalOpen = true; forwardTargetId = {{ $consultation->id }}; forwardTargetName = '{{ addslashes($consultation->patient->full_name) }}'; forwardTargetQueue = '{{ $consultation->queue_number }}'; forwardActionUrl = '{{ route('nurse.forward', $consultation->id) }}'"
                                class="px-3 py-2 rounded-xl bg-white dark:bg-slate-800 hover:bg-slate-100 dark:hover:bg-slate-700 border border-slate-200 dark:border-slate-700 text-xs font-bold text-slate-700 dark:text-slate-300 transition-colors shadow-2xs">
                            Forward
                        </button>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Completed Today Section -->
    <div class="pt-6 pb-4">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h3 class="font-display text-lg sm:text-xl font-extrabold text-slate-900 dark:text-white flex items-center gap-2.5 tracking-tight">
                    <span class="w-8 h-8 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 flex items-center justify-center text-sm border border-slate-200 dark:border-slate-700 shadow-2xs">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </span>
                    <span>Completed Consultations Today</span>
                </h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Summary of patients triaged, discharged, or referred today.</p>
            </div>
            <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-700">
                {{ $handledCount ?? count($handledPatients) }} Total
            </span>
        </div>
        
        @if(count($handledPatients) > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                @foreach($handledPatients as $handled)
                    <div class="rounded-2xl bg-white dark:bg-slate-900 p-4 border border-slate-200/80 dark:border-slate-800 shadow-xs hover:shadow-md transition-shadow flex flex-col justify-between">
                        <div>
                            <div class="flex items-start gap-3 mb-3">
                                <div class="w-9 h-9 rounded-xl bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-400 font-extrabold flex items-center justify-center shrink-0 border border-emerald-200/60 dark:border-emerald-800/60 shadow-2xs text-xs">
                                    {{ substr($handled->patient->first_name, 0, 1) }}
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="font-extrabold text-sm text-slate-900 dark:text-white truncate">{{ $handled->patient->full_name }}</p>
                                    <p class="text-[10px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">{{ $handled->patient->classification }}</p>
                                </div>
                            </div>
                            
                            <div class="p-2.5 rounded-xl bg-slate-50 dark:bg-slate-800/50 border border-slate-100 dark:border-slate-800 mb-3">
                                <p class="text-xs text-slate-600 dark:text-slate-300 font-medium line-clamp-2 leading-relaxed" title="{{ $handled->diagnosis ?: 'No notes provided' }}">
                                    <span class="text-slate-400 font-bold mr-1">Notes:</span>{{ $handled->diagnosis ?: 'Assessment recorded' }}
                                </p>
                            </div>

                            @if($handled->prescriptionRecord)
                                <div class="pt-2 mb-2 border-t border-slate-100 dark:border-slate-800/80 flex items-center justify-between gap-2 text-[10px] font-bold text-emerald-600 dark:text-emerald-400">
                                    <span class="flex items-center gap-1">
                                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        <span>Prescription Issued</span>
                                    </span>
                                    @if($handled->prescriptionRecord->status === 'pending' || $handled->prescriptionRecord->status === 'partially_dispensed')
                                        <form action="{{ route('nurse.prescription.cancel', $handled->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to cancel this prescription? This action cannot be undone.')">
                                            @csrf
                                            <input type="hidden" name="cancellation_reason" value="Cancelled by prescriber - clinical decision / patient no longer needs">
                                            <button type="submit" class="px-2 py-0.5 text-[9px] font-bold text-rose-700 dark:text-rose-300 bg-rose-100 dark:bg-rose-950/40 hover:bg-rose-200 dark:hover:bg-rose-950/60 rounded border border-rose-300 dark:border-rose-700/80 cursor-pointer transition-colors">
                                                Cancel Rx
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            @endif
                        </div>

                        <div class="flex justify-between items-center text-[10px] text-slate-400 font-semibold pt-2.5 border-t border-slate-100 dark:border-slate-800/80">
                            <span>{{ \Carbon\Carbon::parse($handled->consultation_date)->format('M d') }}</span>
                            <span class="bg-slate-100 dark:bg-slate-800 px-1.5 py-0.5 rounded text-slate-500 dark:text-slate-400">{{ $handled->updated_at->format('h:i A') }}</span>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="rounded-2xl bg-white/70 dark:bg-slate-900/60 border border-dashed border-slate-200 dark:border-slate-800 p-8 text-center">
                <p class="text-xs text-slate-500 dark:text-slate-400 font-medium">You have not completed any patients yet today.</p>
            </div>
        @endif
    </div>

    {{-- Reusable Forward Patient Modal --}}
    <div x-show="forwardModalOpen"
         x-cloak
         style="display: none;"
         class="fixed inset-0 z-50 overflow-y-auto"
         role="dialog"
         aria-modal="true">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
            <div x-show="forwardModalOpen"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 @click="forwardModalOpen = false"
                 class="fixed inset-0 bg-slate-950/70 backdrop-blur-sm transition-opacity"></div>

            <div x-show="forwardModalOpen"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-95"
                 class="relative z-10 inline-block w-full max-w-lg p-6 my-8 text-left align-middle bg-white dark:bg-slate-900 border border-slate-200/90 dark:border-slate-800 rounded-3xl shadow-2xl transition-all">
                
                <form :action="forwardActionUrl" method="POST">
                    @csrf
                    <div class="flex items-center gap-3.5 mb-5">
                        <span class="w-11 h-11 rounded-2xl bg-emerald-100 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0 border border-emerald-500/20 shadow-2xs">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                        </span>
                        <div>
                            <h3 class="text-lg font-black text-slate-900 dark:text-white">Forward Patient to Doctor</h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400">Refer patient directly to an active physician on duty.</p>
                        </div>
                    </div>

                    <div class="p-3.5 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200/70 dark:border-slate-700/60 mb-4 text-xs space-y-1">
                        <div class="flex items-center justify-between">
                            <span class="text-slate-500 dark:text-slate-400 font-medium">Patient:</span>
                            <span class="font-bold text-slate-900 dark:text-white" x-text="forwardTargetName"></span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-slate-500 dark:text-slate-400 font-medium">Queue Number:</span>
                            <span class="font-mono font-bold text-emerald-600 dark:text-emerald-400" x-text="forwardTargetQueue"></span>
                        </div>
                    </div>

                    <div class="space-y-1.5 mb-6">
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider">Select Physician <span class="text-rose-500">*</span></label>
                        <select name="doctor_id" required class="w-full text-xs font-semibold rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-white p-2.5 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500">
                            <option value="" disabled selected>-- Choose an Attending Doctor --</option>
                            @foreach($doctors as $doc)
                                <option value="{{ $doc->id }}">Dr. {{ $doc->clean_full_name }} ({{ ucwords(str_replace('_', ' ', $doc->role)) }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex items-center justify-end gap-2.5 pt-4 border-t border-slate-200/70 dark:border-slate-800">
                        <button type="button" 
                                @click="forwardModalOpen = false"
                                class="px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 text-xs font-bold text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors cursor-pointer">
                            Cancel
                        </button>
                        <button type="submit"
                                class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-700 hover:from-emerald-700 hover:to-teal-800 text-white text-xs font-bold shadow-md shadow-emerald-600/20 transition-all cursor-pointer flex items-center gap-2">
                            <span>Forward Patient</span>
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Reusable Walkout / Cancel Modal --}}
    <div x-show="cancelModalOpen"
         x-cloak
         style="display: none;"
         class="fixed inset-0 z-50 overflow-y-auto"
         role="dialog"
         aria-modal="true">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
            <div x-show="cancelModalOpen"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 @click="cancelModalOpen = false"
                 class="fixed inset-0 bg-slate-950/70 backdrop-blur-sm transition-opacity"></div>

            <div x-show="cancelModalOpen"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-95"
                 class="relative z-10 inline-block w-full max-w-lg p-6 my-8 text-left align-middle bg-white dark:bg-slate-900 border border-slate-200/90 dark:border-slate-800 rounded-3xl shadow-2xl transition-all">
                
                <form :action="cancelActionUrl" method="POST">
                    @csrf
                    <div class="flex items-center gap-3.5 mb-5">
                        <span class="w-11 h-11 rounded-2xl bg-rose-100 dark:bg-rose-950/60 text-rose-600 dark:text-rose-400 flex items-center justify-center shrink-0 border border-rose-500/20 shadow-2xs">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        </span>
                        <div>
                            <h3 class="text-lg font-black text-slate-900 dark:text-white">Cancel Consultation / Walkout</h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400">Mark patient as walkout, transferred, or cancelled.</p>
                        </div>
                    </div>

                    <div class="p-3.5 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200/70 dark:border-slate-700/60 mb-4 text-xs space-y-1">
                        <div class="flex items-center justify-between">
                            <span class="text-slate-500 dark:text-slate-400 font-medium">Patient:</span>
                            <span class="font-bold text-slate-900 dark:text-white" x-text="cancelTargetName"></span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-slate-500 dark:text-slate-400 font-medium">Queue Number:</span>
                            <span class="font-mono font-bold text-rose-600 dark:text-rose-400" x-text="cancelTargetQueue"></span>
                        </div>
                    </div>

                    <div class="space-y-3.5 mb-6">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">Reason for Cancellation <span class="text-rose-500">*</span></label>
                            <select name="cancellation_reason" required class="w-full text-xs font-semibold rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-white p-2.5 focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500">
                                <option value="" disabled selected>-- Select Official Reason --</option>
                                <option value="Patient Walked Out / Left Premises">Patient Walked Out / Left Premises</option>
                                <option value="Patient Refused Consultation / Treatment">Patient Refused Consultation / Treatment</option>
                                <option value="Emergency Hospital Transfer / Endorsement">Emergency Hospital Transfer / Endorsement</option>
                                <option value="Patient Unresponsive / Called Multiple Times">Patient Unresponsive / Called Multiple Times</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">Additional Notes / Remarks <span class="text-slate-400 font-normal lowercase">(optional)</span></label>
                            <textarea name="notes" rows="3" placeholder="Provide clinical details, transfer hospital, or reason for walkout..." class="w-full text-xs rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-white p-2.5 focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 no-uppercase"></textarea>
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-2.5 pt-4 border-t border-slate-200/70 dark:border-slate-800">
                        <button type="button" 
                                @click="cancelModalOpen = false"
                                class="px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 text-xs font-bold text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors cursor-pointer">
                            Back
                        </button>
                        <button type="submit"
                                class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-rose-600 to-red-700 hover:from-rose-700 hover:to-red-800 text-white text-xs font-bold shadow-md shadow-rose-600/20 transition-all cursor-pointer flex items-center gap-2">
                            <span>Confirm Cancellation</span>
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>
@endsection