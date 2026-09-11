@extends('layouts.doctor')

@section('header', 'Doctor Clinical Queue')

@section('content')
<div class="max-w-7xl mx-auto space-y-6 pb-16"
     x-data="{
         searchFilter: '',
         categoryFilter: 'all',
         init() {
             window.addEventListener('doctor-global-search', (e) => {
                 this.searchFilter = e.detail;
             });
         },
         matches(card) {
             const q = this.searchFilter.toLowerCase().trim();
             const matchesSearch = !q || 
                 card.name.toLowerCase().includes(q) || 
                 card.queueNum.toLowerCase().includes(q) || 
                 card.patientId.toLowerCase().includes(q) || 
                 card.symptoms.toLowerCase().includes(q);

             if (!matchesSearch) return false;

             if (this.categoryFilter === 'all') return true;
             if (this.categoryFilter === 'active') return card.status === 'active';
             if (this.categoryFilter === 'results_ready') return card.status === 'results_ready';
             if (this.categoryFilter === 'priority') return card.isPriority;
             if (this.categoryFilter === 'regular') return !card.isPriority && card.status !== 'results_ready' && card.status !== 'active';
             return true;
         }
     }">

    {{-- Breadcrumb --}}
    <x-breadcrumb :items="['Clinical Dashboard' => '', 'Active Patient Queue' => '']" />

    {{-- Top Metrics Bento Grid --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-4 sm:gap-5">
        
        {{-- Card 1: Active Queue --}}
        <div class="lg:col-span-3 rounded-3xl bg-white/90 dark:bg-slate-900/90 border border-slate-200/90 dark:border-slate-800 p-6 shadow-xs backdrop-blur-xs relative overflow-hidden flex flex-col justify-between group hover:border-emerald-300 dark:hover:border-emerald-700/60 transition-all">
            <div class="absolute top-0 inset-x-0 h-1.5 bg-gradient-to-r from-emerald-500 to-teal-600"></div>
            <div class="flex items-center justify-between mb-3">
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Waiting Patients</span>
                <span class="w-9 h-9 rounded-xl bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                </span>
            </div>
            <div>
                <div class="flex items-baseline gap-2">
                    <span class="font-display text-4xl sm:text-5xl font-extrabold text-slate-900 dark:text-white">{{ count($queue) }}</span>
                    <span class="text-xs font-semibold text-slate-500 dark:text-slate-400">in queue today</span>
                </div>
                <div class="mt-3 pt-3 border-t border-slate-100 dark:border-slate-800/80 flex items-center gap-2 text-[11px] font-bold text-slate-600 dark:text-slate-400">
                    <span class="inline-flex items-center gap-1 text-emerald-700 dark:text-emerald-400">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                        {{ $queue->where('status', 'active')->count() }} in progress
                    </span>
                    <span>•</span>
                    <span class="text-teal-700 dark:text-teal-400">{{ $queue->where('status', 'results_ready')->count() }} results ready</span>
                </div>
            </div>
        </div>

        {{-- Card 2: Waiting for Results --}}
        <a href="{{ route('doctor.waiting-results') }}" 
           class="lg:col-span-3 rounded-3xl bg-white/90 dark:bg-slate-900/90 border border-slate-200/90 dark:border-slate-800 p-6 shadow-xs backdrop-blur-xs relative overflow-hidden flex flex-col justify-between group hover:border-amber-400 dark:hover:border-amber-600 transition-all cursor-pointer">
            <div class="absolute top-0 inset-x-0 h-1.5 bg-gradient-to-r from-amber-500 to-orange-500"></div>
            <div class="flex items-center justify-between mb-3">
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Pending Diagnostics</span>
                <span class="w-9 h-9 rounded-xl bg-amber-50 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400 flex items-center justify-center group-hover:scale-110 transition-transform">
                    <svg class="w-5 h-5 animate-pulse" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/></svg>
                </span>
            </div>
            <div>
                <div class="flex items-baseline gap-2">
                    <span class="font-display text-4xl sm:text-5xl font-extrabold text-amber-600 dark:text-amber-400">{{ $awaitingLabs->count() }}</span>
                    <span class="text-xs font-semibold text-slate-500 dark:text-slate-400">awaiting labs</span>
                </div>
                <div class="mt-3 pt-3 border-t border-slate-100 dark:border-slate-800/80 flex items-center justify-between text-[11px] font-bold text-amber-700 dark:text-amber-400 group-hover:translate-x-0.5 transition-transform">
                    <span>View Diagnostic Tracker</span>
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </div>
            </div>
        </a>

        {{-- Card 3: Completed Consultations --}}
        <div class="lg:col-span-3 rounded-3xl bg-white/90 dark:bg-slate-900/90 border border-slate-200/90 dark:border-slate-800 p-6 shadow-xs backdrop-blur-xs relative overflow-hidden flex flex-col justify-between group hover:border-teal-300 dark:hover:border-teal-700/60 transition-all">
            <div class="absolute top-0 inset-x-0 h-1.5 bg-gradient-to-r from-teal-500 to-emerald-600"></div>
            <div class="flex items-center justify-between mb-3">
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Completed Consultations</span>
                <span class="w-9 h-9 rounded-xl bg-teal-50 dark:bg-teal-950/60 text-teal-600 dark:text-teal-400 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </span>
            </div>
            <div>
                <div class="flex items-baseline gap-2">
                    <span class="font-display text-4xl sm:text-5xl font-extrabold text-slate-900 dark:text-white">{{ count($handledPatients) }}</span>
                    <span class="text-xs font-semibold text-slate-500 dark:text-slate-400">patients finished</span>
                </div>
                <div class="mt-3 pt-3 border-t border-slate-100 dark:border-slate-800/80 text-[11px] font-medium text-slate-500 dark:text-slate-400 truncate">
                    Prescriptions & medical notes filed
                </div>
            </div>
        </div>

        {{-- Card 4: Doctor On-Duty Banner --}}
        <div class="lg:col-span-3 rounded-3xl bg-gradient-to-br from-emerald-700 via-emerald-800 to-teal-900 text-white p-6 shadow-md relative overflow-hidden flex flex-col justify-between">
            <div class="absolute -top-12 -right-12 w-32 h-32 bg-white/10 rounded-full blur-2xl pointer-events-none"></div>
            <div class="flex items-center gap-3 mb-3">
                <div class="w-12 h-12 rounded-2xl bg-white/15 backdrop-blur-md border border-white/20 flex items-center justify-center overflow-hidden shrink-0 shadow-inner">
                    @if(auth()->user()->avatar_url)
                        <img src="{{ auth()->user()->avatar_url }}" alt="{{ auth()->user()->name }}" class="w-full h-full object-cover">
                    @else
                        <span class="text-base font-black text-white">{{ auth()->user()->initials }}</span>
                    @endif
                </div>
                <div class="min-w-0">
                    <h2 class="text-sm font-extrabold text-white truncate leading-snug">{{ $user->formatted_name }}</h2>
                    <p class="text-[11px] text-emerald-200 truncate capitalize">{{ str_replace('_', ' ', $user->role) }}</p>
                </div>
            </div>
            <div class="pt-3 border-t border-white/15 flex items-center justify-between text-xs">
                <div class="flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 shadow-[0_0_8px_rgba(52,211,153,0.9)] animate-pulse"></span>
                    <span class="font-bold text-emerald-100 text-[11px]">{{ auth()->user()->status ?? 'Online' }}</span>
                </div>
                <span class="text-[11px] text-emerald-200/90 font-medium">{{ \Carbon\Carbon::today()->format('M d, Y') }}</span>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="p-4 rounded-2xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800/60 flex items-center gap-3 text-xs sm:text-sm font-bold text-emerald-800 dark:text-emerald-300">
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
                    <span>Patient Consultation Queue</span>
                </h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Live queue prioritized by triage protocol, emergency triage, and diagnostic test readiness</p>
            </div>

            {{-- Filter Chips --}}
            <div class="inline-flex items-center p-1 rounded-2xl bg-slate-100 dark:bg-slate-800/80 border border-slate-200/80 dark:border-slate-700/70 overflow-x-auto max-w-full">
                <button type="button" 
                        @click="categoryFilter = 'all'"
                        class="px-3 py-1.5 rounded-xl text-xs font-bold transition-all cursor-pointer whitespace-nowrap"
                        :class="categoryFilter === 'all' ? 'bg-white dark:bg-slate-900 text-emerald-700 dark:text-emerald-400 shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900'">
                    All ({{ count($queue) }})
                </button>
                <button type="button" 
                        @click="categoryFilter = 'active'"
                        class="px-3 py-1.5 rounded-xl text-xs font-bold transition-all cursor-pointer whitespace-nowrap"
                        :class="categoryFilter === 'active' ? 'bg-white dark:bg-slate-900 text-yellow-600 dark:text-yellow-400 shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900'">
                    In Progress ({{ $queue->where('status', 'active')->count() }})
                </button>
                <button type="button" 
                        @click="categoryFilter = 'results_ready'"
                        class="px-3 py-1.5 rounded-xl text-xs font-bold transition-all cursor-pointer whitespace-nowrap"
                        :class="categoryFilter === 'results_ready' ? 'bg-white dark:bg-slate-900 text-teal-600 dark:text-teal-400 shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900'">
                    Results Ready ({{ $queue->where('status', 'results_ready')->count() }})
                </button>
                <button type="button" 
                        @click="categoryFilter = 'priority'"
                        class="px-3 py-1.5 rounded-xl text-xs font-bold transition-all cursor-pointer whitespace-nowrap"
                        :class="categoryFilter === 'priority' ? 'bg-white dark:bg-slate-900 text-amber-600 dark:text-amber-400 shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900'">
                    Priority ({{ $queue->filter(fn($c) => in_array($c->classification ?? $c->patient?->classification, ['Senior Citizen', 'PWD']) || str_starts_with($c->queue_number, 'PED-E'))->count() }})
                </button>
            </div>
        </div>

        {{-- Live Queue Container --}}
        <div id="patient-queue-section" data-dynamic-block="true">
            @if(count($queue) > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                    @foreach($queue as $index => $consultation)
                        @php
                            $isEmergency = \Illuminate\Support\Str::startsWith($consultation->queue_number, 'PED-E');
                            $isResultsReady = $consultation->status === 'results_ready';
                            $isActive = $consultation->status === 'active';
                            $isPriority = in_array($consultation->patient->classification, ['Senior Citizen', 'PWD']) || $isEmergency;
                            $tempVal = floatval($consultation->preTriage?->temperature ?? 0);
                            $isFebrile = $tempVal > 37.5;
                        @endphp
                        
                        <div class="group flex flex-col rounded-3xl bg-white dark:bg-slate-900 border transition-all duration-300 relative overflow-hidden shadow-xs hover:shadow-md"
                             :class="{
                                 'border-emerald-400 dark:border-emerald-500/80 ring-2 ring-emerald-400/30': '{{ $isResultsReady }}' === '1',
                                 'border-yellow-400 dark:border-yellow-500/80 ring-2 ring-yellow-400/30': '{{ $isActive }}' === '1',
                                 'border-rose-400 dark:border-rose-500/80 ring-2 ring-rose-400/30': '{{ $isEmergency }}' === '1',
                                 'border-slate-200/90 dark:border-slate-800 hover:border-emerald-300 dark:hover:border-emerald-700': '{{ !$isResultsReady && !$isActive && !$isEmergency }}' === '1'
                             }"
                             x-show="matches({
                                 name: '{{ addslashes($consultation->patient->full_name) }}',
                                 queueNum: '{{ $consultation->queue_number }}',
                                 patientId: '{{ $consultation->patient->patient_id }}',
                                 symptoms: '{{ addslashes($consultation->preTriage?->symptoms ?? '') }}',
                                 status: '{{ $consultation->status }}',
                                 isPriority: {{ $isPriority ? 'true' : 'false' }}
                             })"
                             x-transition>

                            {{-- Card Header: Position, Queue Number & Priority Badge --}}
                            <div class="flex items-center justify-between p-4 border-b {{ $isResultsReady ? 'border-emerald-200/80 dark:border-emerald-900/40 bg-emerald-50/70 dark:bg-emerald-950/30' : ($isActive ? 'border-yellow-200/80 dark:border-yellow-900/40 bg-yellow-50/70 dark:bg-yellow-950/30' : ($isEmergency ? 'border-rose-200/80 dark:border-rose-900/40 bg-rose-50/70 dark:bg-rose-950/30' : 'border-slate-100 dark:border-slate-800/80 bg-slate-50/60 dark:bg-slate-800/40')) }}">
                                <div class="flex items-center gap-2.5">
                                    <span class="flex items-center justify-center w-7 h-7 rounded-xl {{ $isResultsReady ? 'bg-emerald-600 text-white' : ($isActive ? 'bg-yellow-500 text-white' : ($isEmergency ? 'bg-rose-600 text-white' : 'bg-slate-800 text-white dark:bg-slate-700')) }} text-xs font-extrabold shadow-2xs">
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
                                    @elseif($isResultsReady)
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-black uppercase tracking-wider bg-emerald-600 text-white shadow-2xs animate-pulse">
                                            <span class="w-1.5 h-1.5 rounded-full bg-white"></span>
                                            Results Ready
                                        </span>
                                    @elseif($isActive)
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-black uppercase tracking-wider bg-yellow-500 text-white shadow-2xs">
                                            <span class="w-1.5 h-1.5 rounded-full bg-yellow-900 animate-ping"></span>
                                            In Consultation
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

                                        <span class="text-[10px] font-bold px-2 py-0.5 rounded-md border shrink-0 {{ in_array($consultation->patient->classification, ['Senior Citizen', 'PWD']) ? 'bg-amber-50 text-amber-800 dark:bg-amber-950/60 dark:text-amber-300 border-amber-200' : 'bg-slate-50 text-slate-700 dark:bg-slate-800 dark:text-slate-300 border-slate-200' }}">
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
                                                {{ $consultation->preTriage?->spo2 ?? $consultation->preTriage?->oxygen_saturation ?? '--' }}{{ $consultation->preTriage?->spo2 ? '%' : '' }}
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
                                <div class="pt-2">
                                    <a href="{{ route('doctor.consultation.start', $consultation->id) }}"
                                       class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold text-white shadow-sm transition-all cursor-pointer {{ $isActive ? 'bg-gradient-to-r from-yellow-500 to-amber-600 hover:from-yellow-600 hover:to-amber-700' : ($isResultsReady ? 'bg-gradient-to-r from-teal-600 to-emerald-700 hover:from-teal-700 hover:to-emerald-800' : 'bg-gradient-to-r from-slate-900 to-slate-800 hover:from-emerald-700 hover:to-teal-800 dark:from-emerald-600 dark:to-teal-700') }}">
                                        <span>{{ $isActive ? 'Resume Consultation' : ($isResultsReady ? 'Review Results & Consult' : 'Start Consultation') }}</span>
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                    </a>
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
                    <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 max-w-sm mx-auto mt-1">There are no waiting patients in your clinical queue at the moment.</p>
                </div>
            @endif
        </div>
    </div>

    {{-- Awaiting Diagnostic Labs Drawer / Section --}}
    @if($awaitingLabs->count() > 0)
        <div class="rounded-3xl bg-amber-50/40 dark:bg-amber-950/20 border border-amber-200/70 dark:border-amber-800/40 p-6 space-y-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2.5">
                    <span class="w-8 h-8 rounded-xl bg-amber-100 dark:bg-amber-900/60 text-amber-700 dark:text-amber-300 flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/></svg>
                    </span>
                    <div>
                        <h4 class="font-bold text-slate-900 dark:text-white text-sm sm:text-base">Patients Undergoing Diagnostic Procedures</h4>
                        <p class="text-xs text-slate-500 dark:text-slate-400">Laboratory & Radiology diagnostic requests currently on hold</p>
                    </div>
                </div>

                <a href="{{ route('doctor.waiting-results') }}" class="text-xs font-bold text-amber-700 dark:text-amber-400 hover:underline flex items-center gap-1">
                    <span>Full Diagnostic Tracker</span>
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </a>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                @foreach($awaitingLabs as $consultation)
                    <div class="p-4 rounded-2xl bg-white dark:bg-slate-900 border border-amber-200/80 dark:border-amber-800/60 shadow-2xs flex items-center justify-between gap-3">
                        <div class="min-w-0">
                            <span class="font-mono text-[10px] font-black px-2 py-0.5 rounded bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300">
                                {{ $consultation->queue_number }}
                            </span>
                            <p class="font-bold text-slate-900 dark:text-white text-xs mt-1 truncate">{{ $consultation->patient->full_name }}</p>
                            <p class="text-[10px] text-amber-600 dark:text-amber-400 font-semibold">Diagnostic in progress</p>
                        </div>
                        <a href="{{ route('doctor.consultation.start', $consultation->id) }}"
                           class="shrink-0 px-3 py-1.5 rounded-xl text-[11px] font-bold bg-amber-100 dark:bg-amber-900/50 text-amber-800 dark:text-amber-200 hover:bg-amber-200 transition">
                            Resume
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Completed Today History --}}
    <div class="space-y-4 pt-4">
        <div class="flex items-center justify-between">
            <h4 class="font-display text-lg font-bold text-slate-900 dark:text-white flex items-center gap-2">
                <span class="w-7 h-7 rounded-lg bg-teal-100 dark:bg-teal-950/60 text-teal-700 dark:text-teal-400 flex items-center justify-center text-xs">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </span>
                <span>Finished Today ({{ count($handledPatients) }})</span>
            </h4>
        </div>

        @if(count($handledPatients) > 0)
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3.5">
                @foreach($handledPatients as $handled)
                    <div class="p-4 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-2xs flex flex-col justify-between">
                        <div>
                            <div class="flex items-center justify-between gap-2 mb-2">
                                <span class="font-bold text-xs text-slate-900 dark:text-white truncate">{{ $handled->patient->full_name }}</span>
                                <span class="text-[10px] text-slate-400 shrink-0 font-medium">{{ $handled->updated_at->format('h:i A') }}</span>
                            </div>
                            <p class="text-[11px] text-slate-500 dark:text-slate-400 line-clamp-2 leading-relaxed">
                                <strong class="text-slate-700 dark:text-slate-300">Dx:</strong> {{ $handled->diagnosis ?: 'Routine Consultation' }}
                            </p>
                        </div>
                        @if($handled->prescription)
                            <div class="mt-2 pt-2 border-t border-slate-100 dark:border-slate-800/80 flex items-center gap-1 text-[10px] font-bold text-emerald-600 dark:text-emerald-400">
                                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <span>Prescription Issued</span>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        @else
            <div class="p-6 rounded-2xl bg-slate-50 dark:bg-slate-900/40 border border-slate-200/60 dark:border-slate-800 text-center text-xs text-slate-500">
                No consultations completed yet today.
            </div>
        @endif
    </div>

</div>
@endsection
