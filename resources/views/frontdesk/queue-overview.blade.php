@extends('layouts.frontdesk')

@section('title', 'Live Queue Overview')
@section('header', 'Queue Overview')

@section('content')
<div class="space-y-6">
    <!-- Breadcrumb & Top Bar -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1">
                <span>Frontdesk Portal</span>
                <svg class="w-3 h-3 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                <span class="text-emerald-600 dark:text-emerald-400 font-bold">Live Queue Overview</span>
            </div>
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-2xl bg-emerald-100 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 flex items-center justify-center font-black text-sm border border-emerald-500/20 shadow-2xs">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" /></svg>
                </div>
                <div>
                    <h1 class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white tracking-tight">Real-Time Clinical Queue</h1>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Continuous live monitoring of physician rooms, triage holding, and ancillary desks.</p>
                </div>
            </div>
        </div>
        <div class="flex items-center gap-2.5">
            <div class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 text-xs font-bold text-slate-600 dark:text-slate-300 shadow-2xs">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                <span>Auto-refreshes every 30s</span>
            </div>
            <button onclick="window.location.reload()" class="inline-flex items-center gap-2 bg-white dark:bg-slate-900 text-slate-700 dark:text-slate-200 border border-slate-200/80 dark:border-slate-800 px-4 py-2 rounded-xl text-xs font-bold shadow-2xs hover:bg-slate-50 dark:hover:bg-slate-800 transition active:scale-[0.98] cursor-pointer">
                <svg class="w-3.5 h-3.5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                Refresh Board
            </button>
        </div>
    </div>

    @php
        $totalWaitingPatients = collect($queuesByStaff)->sum(fn($q) => count($q['patients']));
        $inConsultCount = collect($queuesByStaff)->sum(fn($q) => collect($q['patients'])->where('status', 'active')->count());
        $waitingCount = collect($queuesByStaff)->sum(fn($q) => collect($q['patients'])->where('status', 'queued')->count());
        $calledCount = collect($queuesByStaff)->sum(fn($q) => collect($q['patients'])->where('status', 'called')->count());
        $unassignedCount = count($unassigned ?? []);
        $ancillaryTotal = count($labQueue ?? []) + count($radQueue ?? []) + count($pharmacyQueue ?? []);
    @endphp

    <!-- KPI Summary Row -->
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3.5">
        <div class="bg-white dark:bg-slate-900 rounded-3xl p-4 border border-slate-200/80 dark:border-slate-800/80 shadow-[0_8px_30px_rgb(0,0,0,0.04)] dark:shadow-none flex items-center gap-3.5">
            <div class="w-10 h-10 rounded-2xl bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400 flex items-center justify-center font-black shrink-0 border border-indigo-500/20">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
            </div>
            <div>
                <p class="text-[10px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Total in Queue</p>
                <p class="text-xl font-black text-slate-900 dark:text-white">{{ $totalWaitingPatients }}</p>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-900 rounded-3xl p-4 border border-slate-200/80 dark:border-slate-800/80 shadow-[0_8px_30px_rgb(0,0,0,0.04)] dark:shadow-none flex items-center gap-3.5">
            <div class="w-10 h-10 rounded-2xl bg-amber-50 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400 flex items-center justify-center font-black shrink-0 border border-amber-500/20">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
            </div>
            <div>
                <p class="text-[10px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">In Consult</p>
                <p class="text-xl font-black text-amber-600 dark:text-amber-400">{{ $inConsultCount }}</p>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-900 rounded-3xl p-4 border border-slate-200/80 dark:border-slate-800/80 shadow-[0_8px_30px_rgb(0,0,0,0.04)] dark:shadow-none flex items-center gap-3.5">
            <div class="w-10 h-10 rounded-2xl bg-blue-50 dark:bg-blue-950/60 text-blue-600 dark:text-blue-400 flex items-center justify-center font-black shrink-0 border border-blue-500/20">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            </div>
            <div>
                <p class="text-[10px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Waiting in Lobby</p>
                <p class="text-xl font-black text-blue-600 dark:text-blue-400">{{ $waitingCount + $calledCount }}</p>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-900 rounded-3xl p-4 border border-slate-200/80 dark:border-slate-800/80 shadow-[0_8px_30px_rgb(0,0,0,0.04)] dark:shadow-none flex items-center gap-3.5">
            <div class="w-10 h-10 rounded-2xl bg-teal-50 dark:bg-teal-950/60 text-teal-600 dark:text-teal-400 flex items-center justify-center font-black shrink-0 border border-teal-500/20">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" /></svg>
            </div>
            <div>
                <p class="text-[10px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Ancillary Labs</p>
                <p class="text-xl font-black text-teal-600 dark:text-teal-400">{{ $ancillaryTotal }}</p>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-900 rounded-3xl p-4 border border-slate-200/80 dark:border-slate-800/80 shadow-[0_8px_30px_rgb(0,0,0,0.04)] dark:shadow-none flex items-center gap-3.5 col-span-2 sm:col-span-1">
            <div class="w-10 h-10 rounded-2xl {{ $unassignedCount > 0 ? 'bg-rose-50 dark:bg-rose-950/60 text-rose-600 dark:text-rose-400 border-rose-500/20' : 'bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 border-emerald-500/20' }} flex items-center justify-center font-black shrink-0 border">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
            </div>
            <div>
                <p class="text-[10px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Pending Triage</p>
                <p class="text-xl font-black {{ $unassignedCount > 0 ? 'text-rose-600 dark:text-rose-400' : 'text-slate-900 dark:text-white' }}">{{ $unassignedCount }}</p>
            </div>
        </div>
    </div>

    @if(count($unassigned) > 0)
    <!-- Urgent Unassigned Banner -->
    <div class="bg-rose-50/70 dark:bg-rose-950/30 rounded-3xl border border-rose-200 dark:border-rose-900/60 shadow-[0_8px_30px_rgb(0,0,0,0.04)] dark:shadow-none overflow-hidden">
        <div class="p-5 border-b border-rose-200/80 dark:border-rose-900/60 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-rose-600 text-white flex items-center justify-center font-black text-sm shadow-md shadow-rose-600/20">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                </div>
                <div>
                    <h3 class="text-sm font-black text-rose-900 dark:text-rose-200">Pending Practitioner Assignment</h3>
                    <p class="text-xs text-rose-700/80 dark:text-rose-300">{{ count($unassigned) }} patient(s) need immediate triage assessment and room routing.</p>
                </div>
            </div>
            <span class="px-3 py-1 rounded-full text-xs font-black bg-rose-100 dark:bg-rose-900/50 text-rose-700 dark:text-rose-300 border border-rose-300/60 dark:border-rose-800">
                Action Required
            </span>
        </div>
        
        <ul class="divide-y divide-rose-100 dark:divide-rose-900/40">
            @foreach($unassigned as $consultation)
            <li class="p-4 sm:p-5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 hover:bg-rose-100/40 dark:hover:bg-rose-900/20 transition">
                <div class="flex items-center gap-3.5">
                    <div class="w-10 h-10 rounded-2xl bg-white dark:bg-slate-800 text-rose-700 dark:text-rose-300 flex items-center justify-center font-black text-xs shrink-0 border border-rose-200 dark:border-rose-800">
                        {{ $consultation->patient->initials }}
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <h4 class="font-bold text-slate-900 dark:text-white text-sm">{{ $consultation->patient->full_name }}</h4>
                            <span class="text-[10px] font-mono font-bold bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-700 px-2 py-0.5 rounded-lg">{{ $consultation->patient->patient_id }}</span>
                        </div>
                        <p class="text-xs text-rose-700 dark:text-rose-400 mt-0.5 flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            Queued {{ $consultation->created_at->diffForHumans() }} &bull; {{ $consultation->patient->classification ?? 'Regular Adult' }}
                        </p>
                    </div>
                </div>
                <div class="shrink-0">
                    <a href="{{ route('frontdesk.registration.index', ['selected_id' => $consultation->patient_id]) }}" class="inline-flex items-center justify-center gap-1.5 text-xs font-bold text-white bg-rose-600 hover:bg-rose-700 px-4 py-2.5 rounded-xl transition shadow-md shadow-rose-600/20 active:scale-[0.98] w-full sm:w-auto">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                        Resolve Assignment
                    </a>
                </div>
            </li>
            @endforeach
        </ul>
    </div>
    @endif

    <!-- Practitioner Queue Cards Grid -->
    <div>
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-sm font-black uppercase tracking-wider text-slate-700 dark:text-slate-300 flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                Attending Physicians &amp; Clinical Stations
            </h2>
            <span class="text-xs font-semibold text-slate-500 dark:text-slate-400">{{ count($queuesByStaff) }} Active Station(s)</span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
            @forelse($queuesByStaff as $staffId => $data)
                @php
                    $staff = $data['staff'];
                    $role = $data['role'];
                    $patients = $data['patients'];
                @endphp
                <div class="bg-white dark:bg-slate-900 rounded-3xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] dark:shadow-none border border-slate-200/80 dark:border-slate-800/80 overflow-hidden flex flex-col h-full transition hover:shadow-md">
                    <!-- Station Header -->
                    <div class="p-4 sm:p-5 {{ $role === 'Doctor' ? 'bg-indigo-50/70 dark:bg-indigo-950/30 border-b border-indigo-100 dark:border-indigo-900/50' : 'bg-blue-50/70 dark:bg-blue-950/30 border-b border-blue-100 dark:border-blue-900/50' }}">
                        <div class="flex justify-between items-center gap-3">
                            <div class="flex items-center gap-3 min-w-0">
                                <div class="w-10 h-10 rounded-2xl {{ $role === 'Doctor' ? 'bg-indigo-600 text-white' : 'bg-blue-600 text-white' }} flex items-center justify-center font-black text-xs shrink-0 shadow-md {{ $role === 'Doctor' ? 'shadow-indigo-600/20' : 'shadow-blue-600/20' }}">
                                    {{ $staff->initials ?? substr($staff->formatted_name, 0, 2) }}
                                </div>
                                <div class="min-w-0">
                                    <h3 class="font-black text-slate-900 dark:text-white text-sm truncate">{{ $staff->formatted_name }}</h3>
                                    <p class="text-xs font-bold {{ $role === 'Doctor' ? 'text-indigo-600 dark:text-indigo-400' : 'text-blue-600 dark:text-blue-400' }} mt-0.5 truncate">
                                        {{ $staff->specialization ?? $role }}
                                    </p>
                                </div>
                            </div>
                            <div class="bg-white dark:bg-slate-800 px-3 py-1.5 rounded-2xl shadow-2xs border border-slate-200/80 dark:border-slate-700 text-center shrink-0">
                                <span class="text-base font-black text-slate-900 dark:text-white block leading-none">{{ count($patients) }}</span>
                                <span class="text-[9px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 block mt-0.5">Waiting</span>
                            </div>
                        </div>
                    </div>

                    <!-- Station Patients List -->
                    <div class="p-0 flex-1 overflow-y-auto max-h-[28rem] min-h-[12rem] divide-y divide-slate-100 dark:divide-slate-800/80">
                        @if(count($patients) > 0)
                            <ul class="divide-y divide-slate-100 dark:divide-slate-800/80">
                                @foreach($patients as $index => $consultation)
                                <li class="p-4 hover:bg-slate-50/80 dark:hover:bg-slate-800/50 transition relative overflow-hidden group">
                                    <!-- Left Accent Line -->
                                    <div class="absolute left-0 top-0 bottom-0 w-1 {{ $consultation->status === 'queued' ? 'bg-amber-400' : ($consultation->status === 'called' ? 'bg-indigo-500' : 'bg-emerald-500') }}"></div>
                                    
                                    <div class="flex justify-between items-start pl-2.5">
                                        <div class="min-w-0 pr-2">
                                            <p class="font-bold text-slate-900 dark:text-white text-xs sm:text-sm group-hover:text-emerald-600 dark:group-hover:text-emerald-400 transition truncate">
                                                <span class="text-slate-400 mr-1 font-mono text-xs">#{{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}</span>
                                                {{ $consultation->patient->full_name }}
                                            </p>
                                            <div class="mt-1.5 flex items-center gap-1.5 flex-wrap">
                                                <span class="text-[9px] uppercase font-bold tracking-wider px-2 py-0.5 rounded-lg border
                                                    {{ $consultation->patient->classification === 'Senior Citizen' ? 'bg-blue-50 dark:bg-blue-950/40 border-blue-200 dark:border-blue-800 text-blue-700 dark:text-blue-300' : 
                                                       ($consultation->patient->classification === 'Pediatric' ? 'bg-purple-50 dark:bg-purple-950/40 border-purple-200 dark:border-purple-800 text-purple-700 dark:text-purple-300' : 
                                                       ($consultation->patient->classification === 'PWD' ? 'bg-amber-50 dark:bg-amber-950/40 border-amber-200 dark:border-amber-800 text-amber-700 dark:text-amber-300' : 'bg-slate-100 dark:bg-slate-800 border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-400')) }}">
                                                    {{ $consultation->patient->classification }}
                                                </span>
                                                <span class="text-[11px] font-semibold text-slate-400 flex items-center gap-1">
                                                    <svg class="w-3 h-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                    {{ $consultation->created_at->format('h:i A') }}
                                                </span>
                                            </div>
                                            
                                            <!-- Vitals Pill Strip -->
                                            <div class="mt-2 text-[11px] text-slate-500 dark:text-slate-400 flex flex-wrap gap-x-2 gap-y-1">
                                                @if($consultation->preTriage)
                                                    @php $pt = $consultation->preTriage; @endphp
                                                    @if($pt->blood_pressure) <span class="bg-slate-100 dark:bg-slate-800 px-1.5 py-0.5 rounded-md font-mono">BP: {{ $pt->blood_pressure }}</span> @endif
                                                    @if($pt->temperature) <span class="bg-slate-100 dark:bg-slate-800 px-1.5 py-0.5 rounded-md font-mono">T: {{ $pt->temperature }}°C</span> @endif
                                                    @php $oxy = $pt->oxygen_saturation ?: $pt->spo2; @endphp
                                                    @if($oxy) <span class="bg-slate-100 dark:bg-slate-800 px-1.5 py-0.5 rounded-md font-mono">SpO2: {{ $oxy }}%</span> @endif
                                                @elseif($consultation->temperature || $consultation->blood_pressure)
                                                    @if($consultation->blood_pressure) <span class="bg-slate-100 dark:bg-slate-800 px-1.5 py-0.5 rounded-md font-mono">BP: {{ $consultation->blood_pressure }}</span> @endif
                                                    @if($consultation->temperature) <span class="bg-slate-100 dark:bg-slate-800 px-1.5 py-0.5 rounded-md font-mono">T: {{ $consultation->temperature }}°C</span> @endif
                                                @else
                                                    <span class="italic text-slate-400">No vitals logged</span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="text-right shrink-0">
                                            <span class="whitespace-nowrap text-[10px] font-bold uppercase tracking-wider px-2 py-1 rounded-xl border shadow-2xs 
                                                {{ $consultation->status === 'queued' ? 'text-amber-700 dark:text-amber-300 bg-amber-50 dark:bg-amber-950/40 border-amber-200 dark:border-amber-800' : 
                                                   ($consultation->status === 'called' ? 'text-indigo-700 dark:text-indigo-300 bg-indigo-50 dark:bg-indigo-950/40 border-indigo-200 dark:border-indigo-800' : 
                                                   ($consultation->status === 'active' ? 'text-emerald-700 dark:text-emerald-300 bg-emerald-50 dark:bg-emerald-950/40 border-emerald-200 dark:border-emerald-800' : 'text-slate-700 dark:text-slate-300 bg-slate-50 dark:bg-slate-800 border-slate-200 dark:border-slate-700')) }}">
                                                {{ $consultation->status === 'active' ? 'In Consult' : $consultation->status }}
                                            </span>
                                        </div>
                                    </div>
                                </li>
                                @endforeach
                            </ul>
                        @else
                            <div class="h-full flex flex-col items-center justify-center p-8 text-center text-slate-400">
                                <div class="w-12 h-12 rounded-2xl bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-slate-400 mb-2.5">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                </div>
                                <p class="text-xs font-bold text-slate-700 dark:text-slate-300">Queue is Clear</p>
                                <p class="text-[11px] text-slate-400 mt-0.5">No patients currently waiting at this station</p>
                            </div>
                        @endif
                    </div>
                </div>
            @empty
                <div class="col-span-1 md:col-span-2 lg:col-span-3 bg-white dark:bg-slate-900 p-12 rounded-3xl border border-slate-200/80 dark:border-slate-800/80 text-center shadow-2xs">
                    <div class="w-16 h-16 rounded-3xl bg-slate-100 dark:bg-slate-800 text-slate-400 mx-auto flex items-center justify-center mb-4">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    </div>
                    <h3 class="text-sm font-black text-slate-800 dark:text-white">No active queues logged today</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">There are currently no patients assigned to any doctor or nurse stations.</p>
                </div>
            @endforelse
        </div>
    </div>

    <!-- Ancillary Departments Row -->
    <div>
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-sm font-black uppercase tracking-wider text-slate-700 dark:text-slate-300 flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-teal-500"></span>
                Ancillary Services &amp; Diagnostic Desks
            </h2>
            <span class="text-xs font-semibold text-slate-500 dark:text-slate-400">3 Core Facilities</span>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
            <!-- Laboratory Queue -->
            <div class="bg-white dark:bg-slate-900 rounded-3xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] dark:shadow-none border border-slate-200/80 dark:border-slate-800/80 flex flex-col h-[480px] overflow-hidden">
                <div class="bg-gradient-to-r from-blue-600 to-blue-700 p-4 sm:p-5 shrink-0 flex justify-between items-center text-white shadow-md shadow-blue-600/10">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-2xl bg-white/15 backdrop-blur-xs flex items-center justify-center border border-white/20">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path></svg>
                        </div>
                        <div>
                            <h3 class="text-sm font-black">Laboratory</h3>
                            <p class="text-blue-100 text-[11px]">{{ count($labQueue) }} Pending Test(s)</p>
                        </div>
                    </div>
                    <span class="text-xs font-mono font-bold bg-white/20 px-2.5 py-1 rounded-xl">{{ count($labQueue) }} waiting</span>
                </div>
                <div class="p-4 overflow-y-auto flex-1 bg-slate-50/50 dark:bg-slate-900/50 divide-y divide-slate-100 dark:divide-slate-800/80 space-y-2.5">
                    @forelse($labQueue as $lab)
                        <div class="bg-white dark:bg-slate-800 border-l-4 border-blue-500 rounded-2xl p-3.5 shadow-2xs border border-slate-200/80 dark:border-slate-700">
                            <div class="flex justify-between items-start mb-1.5">
                                <h4 class="font-black text-slate-800 dark:text-white text-xs">{{ $lab->queue_number ?? $lab->p_id }}</h4>
                                @if(in_array($lab->classification, ['Senior Citizen', 'PWD']))
                                    <span class="bg-amber-100 dark:bg-amber-950/60 text-amber-700 dark:text-amber-300 text-[10px] font-bold px-2 py-0.5 rounded-full border border-amber-200 dark:border-amber-800">Priority</span>
                                @else
                                    <span class="bg-blue-50 dark:bg-blue-950/60 text-blue-700 dark:text-blue-300 text-[10px] font-bold px-2 py-0.5 rounded-full border border-blue-200 dark:border-blue-800">Queued</span>
                                @endif
                            </div>
                            <p class="text-xs font-bold text-slate-900 dark:text-white">{{ $lab->first_name }} {{ $lab->last_name }}</p>
                            <p class="text-[10px] text-slate-500 dark:text-slate-400 mt-1 flex items-center gap-1">
                                <span class="capitalize">{{ $lab->status }}</span> &bull; <span>{{ $lab->created_at->diffForHumans() }}</span>
                            </p>
                        </div>
                    @empty
                        <div class="h-full flex flex-col items-center justify-center p-8 text-center text-slate-400">
                            <div class="w-10 h-10 rounded-2xl bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-slate-400 mb-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            </div>
                            <p class="text-xs font-bold text-slate-700 dark:text-slate-300">Lab Queue Clear</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Radiology Queue -->
            <div class="bg-white dark:bg-slate-900 rounded-3xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] dark:shadow-none border border-slate-200/80 dark:border-slate-800/80 flex flex-col h-[480px] overflow-hidden">
                <div class="bg-gradient-to-r from-purple-600 to-purple-700 p-4 sm:p-5 shrink-0 flex justify-between items-center text-white shadow-md shadow-purple-600/10">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-2xl bg-white/15 backdrop-blur-xs flex items-center justify-center border border-white/20">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z"></path></svg>
                        </div>
                        <div>
                            <h3 class="text-sm font-black">Radiology &amp; Imaging</h3>
                            <p class="text-purple-100 text-[11px]">{{ count($radQueue) }} Pending Scan(s)</p>
                        </div>
                    </div>
                    <span class="text-xs font-mono font-bold bg-white/20 px-2.5 py-1 rounded-xl">{{ count($radQueue) }} waiting</span>
                </div>
                <div class="p-4 overflow-y-auto flex-1 bg-slate-50/50 dark:bg-slate-900/50 divide-y divide-slate-100 dark:divide-slate-800/80 space-y-2.5">
                    @forelse($radQueue as $rad)
                        <div class="bg-white dark:bg-slate-800 border-l-4 border-purple-500 rounded-2xl p-3.5 shadow-2xs border border-slate-200/80 dark:border-slate-700">
                            <div class="flex justify-between items-start mb-1.5">
                                <h4 class="font-black text-slate-800 dark:text-white text-xs">{{ $rad->queue_number ?? $rad->p_id }}</h4>
                                @if(in_array($rad->classification, ['Senior Citizen', 'PWD']))
                                    <span class="bg-amber-100 dark:bg-amber-950/60 text-amber-700 dark:text-amber-300 text-[10px] font-bold px-2 py-0.5 rounded-full border border-amber-200 dark:border-amber-800">Priority</span>
                                @else
                                    <span class="bg-purple-50 dark:bg-purple-950/60 text-purple-700 dark:text-purple-300 text-[10px] font-bold px-2 py-0.5 rounded-full border border-purple-200 dark:border-purple-800">Queued</span>
                                @endif
                            </div>
                            <p class="text-xs font-bold text-slate-900 dark:text-white">{{ $rad->first_name }} {{ $rad->last_name }}</p>
                            <p class="text-[10px] text-slate-500 dark:text-slate-400 mt-1 flex items-center gap-1">
                                <span class="capitalize">{{ $rad->status }}</span> &bull; <span>{{ $rad->created_at->diffForHumans() }}</span>
                            </p>
                        </div>
                    @empty
                        <div class="h-full flex flex-col items-center justify-center p-8 text-center text-slate-400">
                            <div class="w-10 h-10 rounded-2xl bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-slate-400 mb-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            </div>
                            <p class="text-xs font-bold text-slate-700 dark:text-slate-300">Radiology Queue Clear</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Pharmacy Queue -->
            <div class="bg-white dark:bg-slate-900 rounded-3xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] dark:shadow-none border border-slate-200/80 dark:border-slate-800/80 flex flex-col h-[480px] overflow-hidden">
                <div class="bg-gradient-to-r from-teal-600 to-emerald-700 p-4 sm:p-5 shrink-0 flex justify-between items-center text-white shadow-md shadow-teal-600/10">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-2xl bg-white/15 backdrop-blur-xs flex items-center justify-center border border-white/20">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path></svg>
                        </div>
                        <div>
                            <h3 class="text-sm font-black">Dispensary Pharmacy</h3>
                            <p class="text-teal-100 text-[11px]">{{ count($pharmacyQueue) }} Pending Dispense(s)</p>
                        </div>
                    </div>
                    <span class="text-xs font-mono font-bold bg-white/20 px-2.5 py-1 rounded-xl">{{ count($pharmacyQueue) }} waiting</span>
                </div>
                <div class="p-4 overflow-y-auto flex-1 bg-slate-50/50 dark:bg-slate-900/50 divide-y divide-slate-100 dark:divide-slate-800/80 space-y-2.5">
                    @forelse($pharmacyQueue as $pha)
                        <div class="bg-white dark:bg-slate-800 border-l-4 border-emerald-500 rounded-2xl p-3.5 shadow-2xs border border-slate-200/80 dark:border-slate-700">
                            <div class="flex justify-between items-start mb-1.5">
                                <h4 class="font-black text-slate-800 dark:text-white text-xs">{{ $pha->queue_number ?? $pha->p_id }}</h4>
                                @if(in_array($pha->classification, ['Senior Citizen', 'PWD']))
                                    <span class="bg-amber-100 dark:bg-amber-950/60 text-amber-700 dark:text-amber-300 text-[10px] font-bold px-2 py-0.5 rounded-full border border-amber-200 dark:border-amber-800">Priority</span>
                                @else
                                    <span class="bg-teal-50 dark:bg-teal-950/60 text-teal-700 dark:text-teal-300 text-[10px] font-bold px-2 py-0.5 rounded-full border border-teal-200 dark:border-teal-800">Queued</span>
                                @endif
                            </div>
                            <p class="text-xs font-bold text-slate-900 dark:text-white">{{ $pha->first_name }} {{ $pha->last_name }}</p>
                            <p class="text-[10px] text-slate-500 dark:text-slate-400 mt-1 flex items-center gap-1">
                                <span class="capitalize">{{ $pha->status }}</span> &bull; <span>{{ $pha->created_at->diffForHumans() }}</span>
                            </p>
                        </div>
                    @empty
                        <div class="h-full flex flex-col items-center justify-center p-8 text-center text-slate-400">
                            <div class="w-10 h-10 rounded-2xl bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-slate-400 mb-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            </div>
                            <p class="text-xs font-bold text-slate-700 dark:text-slate-300">Pharmacy Queue Clear</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
