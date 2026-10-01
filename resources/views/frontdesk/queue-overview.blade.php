@extends('layouts.frontdesk')

@section('title', 'Live Queue Overview')
@section('header', 'Queue Overview')

@section('content')
<div class="space-y-6">
    <!-- Breadcrumb Navigation -->
    <x-breadcrumb :homeUrl="route('frontdesk.dashboard')" homeLabel="Home" :items="[
        'Live Clinical Queue' => ''
    ]" />

    <!-- Top Bar Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 pb-4 border-b border-slate-200/80 dark:border-slate-800">
        <div>
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
        $ancillaryPatientTotal = count($labQueue ?? []) + count($radQueue ?? []) + count($pharmacyQueue ?? []);
        $ancillaryActiveTests = ($labQueue ? $labQueue->sum('active_count') : 0) + ($radQueue ? $radQueue->sum('active_count') : 0);
        $longWaitPatientsCount = collect($queuesByStaff)->flatMap(fn($q) => $q['patients'])->filter(fn($c) => $c->created_at->diffInMinutes(now()) >= 30 && $c->status !== 'active')->count()
            + collect($labQueue ?? [])->filter(fn($p) => $p->wait_minutes >= 30 && $p->status !== 'Done')->count()
            + collect($radQueue ?? [])->filter(fn($p) => $p->wait_minutes >= 30 && $p->status !== 'Done')->count();
    @endphp

    <!-- Real-time Queue Controller State (Alpine.js) -->
    <div x-data="{
        searchQuery: '',
        filterType: 'all',
        viewMode: 'comfortable',
        matches(name, qNumber, pId, classification, waitMins, hasAncillary, status) {
            const q = this.searchQuery.toLowerCase().trim();
            if (q && !name.toLowerCase().includes(q) && !qNumber.toLowerCase().includes(q) && !pId.toLowerCase().includes(q)) {
                return false;
            }
            if (this.filterType === 'priority') {
                return classification.toLowerCase().includes('senior') || classification.toLowerCase().includes('pwd');
            }
            if (this.filterType === 'long_wait') {
                return waitMins >= 30;
            }
            if (this.filterType === 'ancillary') {
                return hasAncillary;
            }
            if (this.filterType === 'in_consult') {
                return status === 'active';
            }
            return true;
        }
    }" class="space-y-6">

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
                    <p class="text-[10px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Ancillary Desks</p>
                    <div class="flex items-baseline gap-1.5">
                        <p class="text-xl font-black text-teal-600 dark:text-teal-400">{{ $ancillaryPatientTotal }}</p>
                        <span class="text-[10px] font-bold text-slate-400">({{ $ancillaryActiveTests }} tests)</span>
                    </div>
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

        <!-- Long Queue Countermeasures & Live Search Toolbar -->
        <div class="bg-white dark:bg-slate-900 rounded-3xl p-4 sm:p-5 border border-slate-200/80 dark:border-slate-800 shadow-[0_8px_30px_rgb(0,0,0,0.03)] dark:shadow-none space-y-4">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                <!-- Search & Filters -->
                <div class="flex flex-wrap items-center gap-2.5 flex-1">
                    <!-- Search Input -->
                    <div class="relative w-full sm:w-64 shrink-0">
                        <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        <input type="text" x-model="searchQuery" placeholder="Filter patient name, #..." class="w-full pl-9 pr-3 py-1.5 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-xs font-semibold text-slate-800 dark:text-slate-200 placeholder:text-slate-400 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition">
                        <button x-show="searchQuery" @click="searchQuery = ''" class="absolute right-2.5 top-2 text-slate-400 hover:text-slate-600 text-xs font-bold">×</button>
                    </div>

                    <!-- Filter Chips -->
                    <button type="button" @click="filterType = 'all'" :class="filterType === 'all' ? 'bg-emerald-600 text-white shadow-xs' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 hover:bg-slate-200 dark:hover:bg-slate-700'" class="px-3 py-1.5 rounded-xl text-xs font-bold transition cursor-pointer">
                        All ({{ $totalWaitingPatients }})
                    </button>
                    <button type="button" @click="filterType = 'priority'" :class="filterType === 'priority' ? 'bg-amber-600 text-white shadow-xs' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 hover:bg-slate-200 dark:hover:bg-slate-700'" class="px-3 py-1.5 rounded-xl text-xs font-bold transition cursor-pointer flex items-center gap-1">
                        <span>⭐ Priority (Senior/PWD)</span>
                    </button>
                    @if($longWaitPatientsCount > 0)
                        <button type="button" @click="filterType = 'long_wait'" :class="filterType === 'long_wait' ? 'bg-rose-600 text-white shadow-xs' : 'bg-rose-50 dark:bg-rose-950/40 text-rose-600 dark:text-rose-400 border border-rose-200 dark:border-rose-900/50'" class="px-3 py-1.5 rounded-xl text-xs font-bold transition cursor-pointer flex items-center gap-1.5">
                            <span>⏱️ Long Wait (>30m)</span>
                            <span class="px-1.5 py-0.2 rounded-full text-[10px] bg-rose-200/60 dark:bg-rose-900/80 text-rose-800 dark:text-rose-200 font-extrabold">{{ $longWaitPatientsCount }}</span>
                        </button>
                    @endif
                    <button type="button" @click="filterType = 'ancillary'" :class="filterType === 'ancillary' ? 'bg-teal-600 text-white shadow-xs' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 hover:bg-slate-200 dark:hover:bg-slate-700'" class="px-3 py-1.5 rounded-xl text-xs font-bold transition cursor-pointer flex items-center gap-1">
                        <span>🧪 With Ancillary Tests</span>
                    </button>
                </div>

                <!-- Density Toggle (Countermeasure for Long Queues) -->
                <div class="flex items-center gap-2 shrink-0 self-end lg:self-auto">
                    <span class="text-[11px] font-bold text-slate-400">View Density:</span>
                    <div class="inline-flex p-1 bg-slate-100 dark:bg-slate-800 rounded-xl border border-slate-200/80 dark:border-slate-700">
                        <button type="button" @click="viewMode = 'comfortable'" :class="viewMode === 'comfortable' ? 'bg-white dark:bg-slate-900 text-slate-900 dark:text-white shadow-2xs font-black' : 'text-slate-500 hover:text-slate-700 font-medium'" class="px-2.5 py-1 rounded-lg text-xs transition cursor-pointer">
                            Standard
                        </button>
                        <button type="button" @click="viewMode = 'compact'" :class="viewMode === 'compact' ? 'bg-white dark:bg-slate-900 text-slate-900 dark:text-white shadow-2xs font-black' : 'text-slate-500 hover:text-slate-700 font-medium'" class="px-2.5 py-1 rounded-lg text-xs transition cursor-pointer flex items-center gap-1" title="High-density mode for long queues">
                            <span>Compact</span>
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Queue Surge Warning Banner (Automatic Countermeasure) -->
            @if($totalWaitingPatients >= 12 || $longWaitPatientsCount >= 3)
                <div class="p-3.5 rounded-2xl bg-amber-500/10 border border-amber-500/30 text-amber-900 dark:text-amber-200 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 text-xs">
                    <div class="flex items-center gap-3">
                        <span class="w-2.5 h-2.5 rounded-full bg-amber-500 animate-ping shrink-0"></span>
                        <div>
                            <span class="font-black">Active Queue Surge Countermeasure:</span>
                            <span class="font-medium text-amber-800 dark:text-amber-300 ml-1">High lobby volume detected ({{ $totalWaitingPatients }} waiting, {{ $longWaitPatientsCount }} with extended wait). Please prioritize fast-tracking Senior/PWD patients.</span>
                        </div>
                    </div>
                    <button type="button" @click="filterType = 'priority'" class="self-start sm:self-auto px-3 py-1 rounded-xl bg-amber-600 hover:bg-amber-700 text-white font-black text-[11px] shadow-sm transition active:scale-95 whitespace-nowrap cursor-pointer">
                        Filter Priority Patients
                    </button>
                </div>
            @endif
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
                        <div class="p-0 flex-1 overflow-y-auto max-h-[30rem] min-h-[12rem] divide-y divide-slate-100 dark:divide-slate-800/80 custom-scrollbar">
                            @if(count($patients) > 0)
                                <ul class="divide-y divide-slate-100 dark:divide-slate-800/80">
                                    @foreach($patients as $index => $consultation)
                                    @php
                                        $patientWaitMins = $consultation->created_at->diffInMinutes(now());
                                        $hasAncillary = $consultation->ancillaryRequests && $consultation->ancillaryRequests->count() > 0;
                                    @endphp
                                    <li class="hover:bg-slate-50/80 dark:hover:bg-slate-800/50 transition relative overflow-hidden group"
                                        :class="viewMode === 'compact' ? 'p-3' : 'p-4'"
                                        x-show="matches('{{ addslashes($consultation->patient->full_name) }}', '{{ addslashes($consultation->queue_number ?? '') }}', '{{ addslashes($consultation->patient->patient_id) }}', '{{ addslashes($consultation->patient->classification ?? '') }}', {{ $patientWaitMins }}, {{ $hasAncillary ? 'true' : 'false' }}, '{{ $consultation->status }}')">
                                        <!-- Left Accent Line -->
                                        <div class="absolute left-0 top-0 bottom-0 w-1 {{ $consultation->status === 'queued' ? 'bg-amber-400' : ($consultation->status === 'called' ? 'bg-indigo-500' : 'bg-emerald-500') }}"></div>
                                        
                                        <div class="flex justify-between items-start pl-2.5">
                                            <div class="min-w-0 pr-2 flex-1">
                                                <div class="flex items-center gap-1.5 flex-wrap">
                                                    <span class="text-slate-400 font-mono text-xs font-bold">#{{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}</span>
                                                    <p class="font-bold text-slate-900 dark:text-white text-xs sm:text-sm group-hover:text-emerald-600 dark:group-hover:text-emerald-400 transition truncate">
                                                        {{ $consultation->patient->full_name }}
                                                    </p>
                                                    <span class="text-[10px] font-mono text-slate-400">({{ $consultation->patient->patient_id }})</span>
                                                </div>

                                                <div class="mt-1 flex items-center gap-1.5 flex-wrap">
                                                    <span class="text-[9px] uppercase font-bold tracking-wider px-2 py-0.5 rounded-lg border
                                                        {{ $consultation->patient->classification === 'Senior Citizen' ? 'bg-blue-50 dark:bg-blue-950/40 border-blue-200 dark:border-blue-800 text-blue-700 dark:text-blue-300' : 
                                                           ($consultation->patient->classification === 'Pediatric' ? 'bg-purple-50 dark:bg-purple-950/40 border-purple-200 dark:border-purple-800 text-purple-700 dark:text-purple-300' : 
                                                           ($consultation->patient->classification === 'PWD' ? 'bg-amber-50 dark:bg-amber-950/40 border-amber-200 dark:border-amber-800 text-amber-700 dark:text-amber-300' : 'bg-slate-100 dark:bg-slate-800 border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-400')) }}">
                                                        {{ $consultation->patient->classification }}
                                                    </span>
                                                    
                                                    <!-- Time in queue & Long Wait Countermeasure Pill -->
                                                    @if($patientWaitMins >= 45 && $consultation->status !== 'active')
                                                        <span class="text-[10px] font-bold px-1.5 py-0.5 rounded-md bg-rose-50 dark:bg-rose-950/50 text-rose-600 dark:text-rose-400 border border-rose-200 dark:border-rose-900/50 animate-pulse">
                                                            ⏱️ {{ $patientWaitMins }}m wait
                                                        </span>
                                                    @elseif($patientWaitMins >= 30 && $consultation->status !== 'active')
                                                        <span class="text-[10px] font-bold px-1.5 py-0.5 rounded-md bg-amber-50 dark:bg-amber-950/50 text-amber-600 dark:text-amber-400 border border-amber-200 dark:border-amber-900/50">
                                                            ⏱️ {{ $patientWaitMins }}m wait
                                                        </span>
                                                    @else
                                                        <span class="text-[11px] font-semibold text-slate-400 flex items-center gap-1">
                                                            <svg class="w-3 h-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                            {{ $consultation->created_at->format('h:i A') }}
                                                        </span>
                                                    @endif
                                                </div>
                                                
                                                <!-- Vitals Pill Strip -->
                                                <div class="mt-1.5 text-[10px] text-slate-500 dark:text-slate-400 flex flex-wrap gap-x-2 gap-y-1">
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

                                                <!-- Ancillary Requests Strip under Patient Name -->
                                                @if($hasAncillary)
                                                    <div class="mt-2 flex flex-wrap gap-1 items-center">
                                                        @foreach($consultation->ancillaryRequests->take(2) as $anc)
                                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-bold border
                                                                {{ $anc->status === 'Done' ? 'bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-300 border-emerald-200 dark:border-emerald-800' : 
                                                                   ($anc->type === 'Laboratory' ? 'bg-purple-50 dark:bg-purple-950/40 text-purple-700 dark:text-purple-300 border-purple-200 dark:border-purple-800' : 'bg-indigo-50 dark:bg-indigo-950/40 text-indigo-700 dark:text-indigo-300 border-indigo-200 dark:border-indigo-800') }}">
                                                                @if($anc->status === 'Done')
                                                                    <svg class="w-2.5 h-2.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                                                @else
                                                                    <span class="w-1.5 h-1.5 rounded-full {{ $anc->type === 'Laboratory' ? 'bg-purple-500' : 'bg-indigo-500' }} animate-pulse"></span>
                                                                @endif
                                                                <span>{{ $anc->test_name }}:</span>
                                                                <span class="font-normal opacity-90">{{ $anc->status }}</span>
                                                            </span>
                                                        @endforeach
                                                        @if($consultation->ancillaryRequests->count() > 2)
                                                            <span class="text-[10px] text-slate-500 dark:text-slate-400 font-bold bg-slate-100 dark:bg-slate-800 px-1.5 py-0.5 rounded border border-slate-200 dark:border-slate-700" title="{{ $consultation->ancillaryRequests->skip(2)->pluck('test_name')->join(', ') }}">+{{ $consultation->ancillaryRequests->count() - 2 }} more</span>
                                                        @endif
                                                    </div>
                                                @endif
                                            </div>

                                            <div class="text-right shrink-0">
                                                <span class="whitespace-nowrap text-[10px] font-bold uppercase tracking-wider px-2 py-1 rounded-xl border shadow-2xs 
                                                    {{ $consultation->status === 'queued' ? 'text-amber-700 dark:text-amber-300 bg-amber-50 dark:bg-amber-950/40 border-amber-200 dark:border-amber-800' : 
                                                       ($consultation->status === 'called' ? 'text-indigo-700 dark:text-indigo-300 bg-indigo-50 dark:bg-indigo-950/40 border-indigo-200 dark:border-indigo-800' : 
                                                       ($consultation->status === 'active' ? 'text-emerald-700 dark:text-emerald-300 bg-emerald-50 dark:bg-emerald-950/40 border-emerald-200 dark:border-emerald-800' : 'text-slate-700 dark:text-slate-300 bg-slate-50 dark:bg-slate-800 border-slate-200 dark:border-slate-700')) }}">
                                                    {{ $consultation->status === 'active' ? 'In Consult' : ($consultation->status === 'awaiting_results' ? 'Awaiting Labs' : $consultation->status) }}
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

        <!-- Ancillary Departments Row (Grouped under Patient Name) -->
        <div>
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-sm font-black uppercase tracking-wider text-slate-700 dark:text-slate-300 flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-teal-500"></span>
                    Ancillary Services &amp; Diagnostic Desks
                </h2>
                <div class="flex items-center gap-2 text-xs font-semibold text-slate-500 dark:text-slate-400">
                    <span>3 Core Facilities</span>
                    <span>&bull;</span>
                    <span class="text-teal-600 dark:text-teal-400 font-bold">Grouped by Patient</span>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
                <!-- Laboratory Queue (Patient-Grouped) -->
                <div class="bg-white dark:bg-slate-900 rounded-3xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] dark:shadow-none border border-slate-200/80 dark:border-slate-800/80 flex flex-col h-[520px] overflow-hidden"
                     x-data="{ labTab: 'active', labSearch: '' }">
                    <div class="bg-gradient-to-r from-blue-600 to-blue-700 p-4 sm:p-5 shrink-0 flex justify-between items-center text-white shadow-md shadow-blue-600/10">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-2xl bg-white/15 backdrop-blur-xs flex items-center justify-center border border-white/20">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path></svg>
                            </div>
                            <div>
                                <h3 class="text-sm font-black">Laboratory</h3>
                                <p class="text-blue-100 text-[11px]">{{ count($labQueue) }} Patient(s) &bull; {{ $labQueue->sum('active_count') }} Pending Test(s)</p>
                            </div>
                        </div>
                        <span class="text-xs font-mono font-bold bg-white/20 px-2.5 py-1 rounded-xl">{{ count($labQueue) }} patients</span>
                    </div>

                    <!-- Column Mini Filter Toolbar -->
                    <div class="px-4 py-2 bg-blue-50/50 dark:bg-slate-800/60 border-b border-blue-100 dark:border-slate-800 flex items-center justify-between gap-2 text-xs">
                        <div class="flex items-center gap-1">
                            <button type="button" @click="labTab = 'active'" :class="labTab === 'active' ? 'bg-blue-600 text-white font-bold' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900'" class="px-2 py-0.5 rounded-lg text-[11px] transition">
                                Active ({{ $labQueue->filter(fn($p) => $p->active_count > 0)->count() }})
                            </button>
                            <button type="button" @click="labTab = 'done'" :class="labTab === 'done' ? 'bg-blue-600 text-white font-bold' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900'" class="px-2 py-0.5 rounded-lg text-[11px] transition">
                                Done ({{ $labQueue->filter(fn($p) => $p->active_count === 0 && $p->done_count > 0)->count() }})
                            </button>
                            <button type="button" @click="labTab = 'all'" :class="labTab === 'all' ? 'bg-blue-600 text-white font-bold' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900'" class="px-2 py-0.5 rounded-lg text-[11px] transition">
                                All ({{ count($labQueue) }})
                            </button>
                        </div>
                        @if(count($labQueue) > 3)
                            <input type="text" x-model="labSearch" placeholder="Filter..." class="w-24 text-[10px] px-2 py-0.5 rounded-lg bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700">
                        @endif
                    </div>

                    <div class="p-3.5 overflow-y-auto flex-1 bg-slate-50/50 dark:bg-slate-900/50 space-y-2.5 custom-scrollbar">
                        @forelse($labQueue as $labPatient)
                            @php
                                $isDoneOnly = $labPatient->active_count === 0 && $labPatient->done_count > 0;
                            @endphp
                            <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-2xs border transition-all relative overflow-hidden group
                                {{ $labPatient->is_priority ? 'border-amber-400/80 dark:border-amber-500/50 bg-gradient-to-r from-amber-50/30 to-white dark:from-amber-950/20 dark:to-slate-800' : 'border-slate-200/80 dark:border-slate-700' }}"
                                :class="viewMode === 'compact' ? 'p-2.5' : 'p-3.5'"
                                x-data="{ showAllTests: false, showDismissed: false }"
                                x-show="(labTab === 'all' || (labTab === 'active' && {{ $labPatient->active_count > 0 ? 'true' : 'false' }}) || (labTab === 'done' && {{ $isDoneOnly ? 'true' : 'false' }})) && (!labSearch || '{{ strtolower($labPatient->first_name.' '.$labPatient->last_name.' '.$labPatient->queue_number) }}'.includes(labSearch.toLowerCase().trim()))">
                                
                                <!-- Left Status Accent Line -->
                                <div class="absolute left-0 top-0 bottom-0 w-1 {{ $labPatient->status === 'Done' ? 'bg-emerald-500' : ($labPatient->status === 'In Progress' ? 'bg-blue-500' : ($labPatient->status === 'Specimen Collected' ? 'bg-indigo-500' : 'bg-amber-400')) }}"></div>

                                <!-- Card Header: Queue # & Priority Badge & Primary Status -->
                                <div class="flex justify-between items-center mb-1.5 pl-2">
                                    <div class="flex items-center gap-1.5 flex-wrap">
                                        <span class="font-mono font-black text-slate-900 dark:text-white text-xs">#{{ $labPatient->queue_number }}</span>
                                        <span class="text-[10px] font-mono text-slate-400">({{ $labPatient->p_id }})</span>
                                        @if($labPatient->is_priority)
                                            <span class="bg-amber-100 dark:bg-amber-950/60 text-amber-700 dark:text-amber-300 text-[9px] font-extrabold px-1.5 py-0.2 rounded-full border border-amber-200 dark:border-amber-800">Priority: {{ $labPatient->classification }}</span>
                                        @endif
                                    </div>
                                    <div class="shrink-0">
                                        @if($labPatient->status === 'Done')
                                            <span class="inline-flex items-center gap-1 bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 text-[10px] font-bold px-2 py-0.5 rounded-full border border-emerald-200 dark:border-emerald-800">✓ Done</span>
                                        @elseif($labPatient->status === 'In Progress')
                                            <span class="inline-flex items-center gap-1 bg-blue-50 dark:bg-blue-950/60 text-blue-700 dark:text-blue-300 text-[10px] font-bold px-2 py-0.5 rounded-full border border-blue-200 dark:border-blue-800"><span class="w-1.5 h-1.5 rounded-full bg-blue-500 animate-pulse"></span>In Progress</span>
                                        @elseif($labPatient->status === 'Specimen Collected')
                                            <span class="inline-flex items-center gap-1 bg-indigo-50 dark:bg-indigo-950/60 text-indigo-700 dark:text-indigo-300 text-[10px] font-bold px-2 py-0.5 rounded-full border border-indigo-200 dark:border-indigo-800">Collected</span>
                                        @else
                                            <span class="inline-flex items-center gap-1 bg-amber-50 dark:bg-amber-950/60 text-amber-700 dark:text-amber-300 text-[10px] font-bold px-2 py-0.5 rounded-full border border-amber-200 dark:border-amber-800">Pending</span>
                                        @endif
                                    </div>
                                </div>

                                <!-- Patient Name & Wait Time -->
                                <div class="flex justify-between items-baseline pl-2 mb-2">
                                    <h4 class="font-black text-slate-900 dark:text-white text-xs sm:text-sm truncate mr-2">{{ $labPatient->first_name }} {{ $labPatient->last_name }}</h4>
                                    <div class="shrink-0 text-[10px]">
                                        @if($labPatient->wait_minutes >= 45 && $labPatient->status !== 'Done')
                                            <span class="text-rose-600 dark:text-rose-400 font-bold bg-rose-50 dark:bg-rose-950/50 px-1.5 py-0.5 rounded border border-rose-200 dark:border-rose-900/50 animate-pulse">⏱️ {{ $labPatient->wait_minutes }}m</span>
                                        @elseif($labPatient->wait_minutes >= 30 && $labPatient->status !== 'Done')
                                            <span class="text-amber-600 dark:text-amber-400 font-bold bg-amber-50 dark:bg-amber-950/50 px-1.5 py-0.5 rounded border border-amber-200 dark:border-amber-900/50">⏱️ {{ $labPatient->wait_minutes }}m</span>
                                        @else
                                            <span class="text-slate-400">{{ $labPatient->created_at->diffForHumans() }}</span>
                                        @endif
                                    </div>
                                </div>

                                <!-- Under Patient Name: Requested Tests (Anti-Congestion / Grouped) -->
                                <div class="pl-2 space-y-1.5">
                                    @php
                                        $visibleTests = $labPatient->all_requests->filter(fn($r) => !in_array($r->status, ['Cancelled', 'Rejected']));
                                    @endphp
                                    <div class="flex flex-wrap gap-1 items-center">
                                        @foreach($visibleTests->take(2) as $test)
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-bold border
                                                {{ $test->status === 'Done' ? 'bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-300 border-emerald-200 dark:border-emerald-800' : 'bg-purple-50 dark:bg-purple-950/40 text-purple-700 dark:text-purple-300 border-purple-200 dark:border-purple-800' }}">
                                                @if($test->status === 'Done')
                                                    <svg class="w-3 h-3 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                                @else
                                                    <span class="w-1.5 h-1.5 rounded-full bg-purple-500 animate-pulse"></span>
                                                @endif
                                                <span>{{ $test->test_name }}</span>
                                                <span class="opacity-75 font-normal">({{ $test->status }})</span>
                                            </span>
                                        @endforeach

                                        <!-- Multi-test overflow countermeasure (+N more) -->
                                        @if($visibleTests->count() > 2)
                                            <template x-if="showAllTests">
                                                <div class="flex flex-wrap gap-1 items-center w-full mt-1">
                                                    @foreach($visibleTests->skip(2) as $test)
                                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-bold border
                                                            {{ $test->status === 'Done' ? 'bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-300 border-emerald-200 dark:border-emerald-800' : 'bg-purple-50 dark:bg-purple-950/40 text-purple-700 dark:text-purple-300 border-purple-200 dark:border-purple-800' }}">
                                                            @if($test->status === 'Done')
                                                                <svg class="w-3 h-3 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                                            @else
                                                                <span class="w-1.5 h-1.5 rounded-full bg-purple-500"></span>
                                                            @endif
                                                            <span>{{ $test->test_name }}</span>
                                                            <span class="opacity-75 font-normal">({{ $test->status }})</span>
                                                        </span>
                                                    @endforeach
                                                </div>
                                            </template>
                                            <button type="button" @click="showAllTests = !showAllTests" class="text-[10px] font-bold text-blue-600 dark:text-blue-400 hover:underline">
                                                <span x-text="showAllTests ? 'Show less' : '+{{ $visibleTests->count() - 2 }} more'"></span>
                                            </button>
                                        @endif
                                    </div>

                                    <!-- Option to show Cancelled/Rejected tests (Prompt 1 requirement) -->
                                    @if($labPatient->dismissed_count > 0)
                                        <div class="pt-1 border-t border-slate-100 dark:border-slate-800">
                                            <button type="button" @click="showDismissed = !showDismissed" class="text-[10px] font-bold text-slate-400 hover:text-rose-600 dark:hover:text-rose-400 flex items-center gap-1 transition cursor-pointer">
                                                <svg class="w-3 h-3 transition-transform" :class="showDismissed ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                                <span x-text="showDismissed ? 'Hide Cancelled/Rejected' : '{{ $labPatient->dismissed_count }} Cancelled/Rejected Test(s)'"></span>
                                            </button>
                                            <div x-show="showDismissed" x-transition class="mt-1 space-y-1">
                                                @foreach($labPatient->dismissed_requests as $dis)
                                                    <div class="flex items-center justify-between text-[10px] bg-rose-50/70 dark:bg-rose-950/30 text-rose-700 dark:text-rose-300 px-2 py-1 rounded border border-rose-200/80 dark:border-rose-900/50">
                                                        <span class="line-through font-medium">{{ $dis->test_name }}</span>
                                                        <span class="font-bold">{{ $dis->status }} {{ $dis->rejection_reason ? '('.$dis->rejection_reason.')' : ($dis->cancellation_reason ? '('.$dis->cancellation_reason.')' : '') }}</span>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="h-full flex flex-col items-center justify-center p-8 text-center text-slate-400">
                                <div class="w-10 h-10 rounded-2xl bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-slate-400 mb-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                </div>
                                <p class="text-xs font-bold text-slate-700 dark:text-slate-300">Lab Queue Clear</p>
                                <p class="text-[11px] text-slate-400 mt-0.5">No patients currently queued for laboratory tests</p>
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- Radiology Queue (Patient-Grouped) -->
                <div class="bg-white dark:bg-slate-900 rounded-3xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] dark:shadow-none border border-slate-200/80 dark:border-slate-800/80 flex flex-col h-[520px] overflow-hidden"
                     x-data="{ radTab: 'active', radSearch: '' }">
                    <div class="bg-gradient-to-r from-purple-600 to-purple-700 p-4 sm:p-5 shrink-0 flex justify-between items-center text-white shadow-md shadow-purple-600/10">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-2xl bg-white/15 backdrop-blur-xs flex items-center justify-center border border-white/20">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z"></path></svg>
                            </div>
                            <div>
                                <h3 class="text-sm font-black">Radiology &amp; Imaging</h3>
                                <p class="text-purple-100 text-[11px]">{{ count($radQueue) }} Patient(s) &bull; {{ $radQueue->sum('active_count') }} Pending Scan(s)</p>
                            </div>
                        </div>
                        <span class="text-xs font-mono font-bold bg-white/20 px-2.5 py-1 rounded-xl">{{ count($radQueue) }} patients</span>
                    </div>

                    <!-- Column Mini Filter Toolbar -->
                    <div class="px-4 py-2 bg-purple-50/50 dark:bg-slate-800/60 border-b border-purple-100 dark:border-slate-800 flex items-center justify-between gap-2 text-xs">
                        <div class="flex items-center gap-1">
                            <button type="button" @click="radTab = 'active'" :class="radTab === 'active' ? 'bg-purple-600 text-white font-bold' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900'" class="px-2 py-0.5 rounded-lg text-[11px] transition">
                                Active ({{ $radQueue->filter(fn($p) => $p->active_count > 0)->count() }})
                            </button>
                            <button type="button" @click="radTab = 'done'" :class="radTab === 'done' ? 'bg-purple-600 text-white font-bold' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900'" class="px-2 py-0.5 rounded-lg text-[11px] transition">
                                Done ({{ $radQueue->filter(fn($p) => $p->active_count === 0 && $p->done_count > 0)->count() }})
                            </button>
                            <button type="button" @click="radTab = 'all'" :class="radTab === 'all' ? 'bg-purple-600 text-white font-bold' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900'" class="px-2 py-0.5 rounded-lg text-[11px] transition">
                                All ({{ count($radQueue) }})
                            </button>
                        </div>
                        @if(count($radQueue) > 3)
                            <input type="text" x-model="radSearch" placeholder="Filter..." class="w-24 text-[10px] px-2 py-0.5 rounded-lg bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700">
                        @endif
                    </div>

                    <div class="p-3.5 overflow-y-auto flex-1 bg-slate-50/50 dark:bg-slate-900/50 space-y-2.5 custom-scrollbar">
                        @forelse($radQueue as $radPatient)
                            @php
                                $isDoneOnly = $radPatient->active_count === 0 && $radPatient->done_count > 0;
                            @endphp
                            <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-2xs border transition-all relative overflow-hidden group
                                {{ $radPatient->is_priority ? 'border-amber-400/80 dark:border-amber-500/50 bg-gradient-to-r from-amber-50/30 to-white dark:from-amber-950/20 dark:to-slate-800' : 'border-slate-200/80 dark:border-slate-700' }}"
                                :class="viewMode === 'compact' ? 'p-2.5' : 'p-3.5'"
                                x-data="{ showAllTests: false, showDismissed: false }"
                                x-show="(radTab === 'all' || (radTab === 'active' && {{ $radPatient->active_count > 0 ? 'true' : 'false' }}) || (radTab === 'done' && {{ $isDoneOnly ? 'true' : 'false' }})) && (!radSearch || '{{ strtolower($radPatient->first_name.' '.$radPatient->last_name.' '.$radPatient->queue_number) }}'.includes(radSearch.toLowerCase().trim()))">
                                
                                <!-- Left Status Accent Line -->
                                <div class="absolute left-0 top-0 bottom-0 w-1 {{ $radPatient->status === 'Done' ? 'bg-emerald-500' : ($radPatient->status === 'In Progress' ? 'bg-purple-500' : 'bg-amber-400') }}"></div>

                                <!-- Card Header: Queue # & Priority Badge & Primary Status -->
                                <div class="flex justify-between items-center mb-1.5 pl-2">
                                    <div class="flex items-center gap-1.5 flex-wrap">
                                        <span class="font-mono font-black text-slate-900 dark:text-white text-xs">#{{ $radPatient->queue_number }}</span>
                                        <span class="text-[10px] font-mono text-slate-400">({{ $radPatient->p_id }})</span>
                                        @if($radPatient->is_priority)
                                            <span class="bg-amber-100 dark:bg-amber-950/60 text-amber-700 dark:text-amber-300 text-[9px] font-extrabold px-1.5 py-0.2 rounded-full border border-amber-200 dark:border-amber-800">Priority: {{ $radPatient->classification }}</span>
                                        @endif
                                    </div>
                                    <div class="shrink-0">
                                        @if($radPatient->status === 'Done')
                                            <span class="inline-flex items-center gap-1 bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 text-[10px] font-bold px-2 py-0.5 rounded-full border border-emerald-200 dark:border-emerald-800">✓ Done</span>
                                        @elseif($radPatient->status === 'In Progress')
                                            <span class="inline-flex items-center gap-1 bg-purple-50 dark:bg-purple-950/60 text-purple-700 dark:text-purple-300 text-[10px] font-bold px-2 py-0.5 rounded-full border border-purple-200 dark:border-purple-800"><span class="w-1.5 h-1.5 rounded-full bg-purple-500 animate-pulse"></span>In Progress</span>
                                        @else
                                            <span class="inline-flex items-center gap-1 bg-amber-50 dark:bg-amber-950/60 text-amber-700 dark:text-amber-300 text-[10px] font-bold px-2 py-0.5 rounded-full border border-amber-200 dark:border-amber-800">Pending</span>
                                        @endif
                                    </div>
                                </div>

                                <!-- Patient Name & Wait Time -->
                                <div class="flex justify-between items-baseline pl-2 mb-2">
                                    <h4 class="font-black text-slate-900 dark:text-white text-xs sm:text-sm truncate mr-2">{{ $radPatient->first_name }} {{ $radPatient->last_name }}</h4>
                                    <div class="shrink-0 text-[10px]">
                                        @if($radPatient->wait_minutes >= 45 && $radPatient->status !== 'Done')
                                            <span class="text-rose-600 dark:text-rose-400 font-bold bg-rose-50 dark:bg-rose-950/50 px-1.5 py-0.5 rounded border border-rose-200 dark:border-rose-900/50 animate-pulse">⏱️ {{ $radPatient->wait_minutes }}m</span>
                                        @elseif($radPatient->wait_minutes >= 30 && $radPatient->status !== 'Done')
                                            <span class="text-amber-600 dark:text-amber-400 font-bold bg-amber-50 dark:bg-amber-950/50 px-1.5 py-0.5 rounded border border-amber-200 dark:border-amber-900/50">⏱️ {{ $radPatient->wait_minutes }}m</span>
                                        @else
                                            <span class="text-slate-400">{{ $radPatient->created_at->diffForHumans() }}</span>
                                        @endif
                                    </div>
                                </div>

                                <!-- Under Patient Name: Requested Tests (Anti-Congestion / Grouped) -->
                                <div class="pl-2 space-y-1.5">
                                    @php
                                        $visibleTests = $radPatient->all_requests->filter(fn($r) => !in_array($r->status, ['Cancelled', 'Rejected']));
                                    @endphp
                                    <div class="flex flex-wrap gap-1 items-center">
                                        @foreach($visibleTests->take(2) as $test)
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-bold border
                                                {{ $test->status === 'Done' ? 'bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-300 border-emerald-200 dark:border-emerald-800' : 'bg-indigo-50 dark:bg-indigo-950/40 text-indigo-700 dark:text-indigo-300 border-indigo-200 dark:border-indigo-800' }}">
                                                @if($test->status === 'Done')
                                                    <svg class="w-3 h-3 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                                @else
                                                    <span class="w-1.5 h-1.5 rounded-full bg-indigo-500 animate-pulse"></span>
                                                @endif
                                                <span>{{ $test->test_name }}</span>
                                                <span class="opacity-75 font-normal">({{ $test->status }})</span>
                                            </span>
                                        @endforeach

                                        <!-- Multi-test overflow countermeasure (+N more) -->
                                        @if($visibleTests->count() > 2)
                                            <template x-if="showAllTests">
                                                <div class="flex flex-wrap gap-1 items-center w-full mt-1">
                                                    @foreach($visibleTests->skip(2) as $test)
                                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-bold border
                                                            {{ $test->status === 'Done' ? 'bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-300 border-emerald-200 dark:border-emerald-800' : 'bg-indigo-50 dark:bg-indigo-950/40 text-indigo-700 dark:text-indigo-300 border-indigo-200 dark:border-indigo-800' }}">
                                                            @if($test->status === 'Done')
                                                                <svg class="w-3 h-3 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                                            @else
                                                                <span class="w-1.5 h-1.5 rounded-full bg-indigo-500"></span>
                                                            @endif
                                                            <span>{{ $test->test_name }}</span>
                                                            <span class="opacity-75 font-normal">({{ $test->status }})</span>
                                                        </span>
                                                    @endforeach
                                                </div>
                                            </template>
                                            <button type="button" @click="showAllTests = !showAllTests" class="text-[10px] font-bold text-indigo-600 dark:text-indigo-400 hover:underline">
                                                <span x-text="showAllTests ? 'Show less' : '+{{ $visibleTests->count() - 2 }} more'"></span>
                                            </button>
                                        @endif
                                    </div>

                                    <!-- Option to show Cancelled/Rejected tests (Prompt 1 requirement) -->
                                    @if($radPatient->dismissed_count > 0)
                                        <div class="pt-1 border-t border-slate-100 dark:border-slate-800">
                                            <button type="button" @click="showDismissed = !showDismissed" class="text-[10px] font-bold text-slate-400 hover:text-rose-600 dark:hover:text-rose-400 flex items-center gap-1 transition cursor-pointer">
                                                <svg class="w-3 h-3 transition-transform" :class="showDismissed ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                                <span x-text="showDismissed ? 'Hide Cancelled/Rejected' : '{{ $radPatient->dismissed_count }} Cancelled/Rejected Scan(s)'"></span>
                                            </button>
                                            <div x-show="showDismissed" x-transition class="mt-1 space-y-1">
                                                @foreach($radPatient->dismissed_requests as $dis)
                                                    <div class="flex items-center justify-between text-[10px] bg-rose-50/70 dark:bg-rose-950/30 text-rose-700 dark:text-rose-300 px-2 py-1 rounded border border-rose-200/80 dark:border-rose-900/50">
                                                        <span class="line-through font-medium">{{ $dis->test_name }}</span>
                                                        <span class="font-bold">{{ $dis->status }} {{ $dis->rejection_reason ? '('.$dis->rejection_reason.')' : ($dis->cancellation_reason ? '('.$dis->cancellation_reason.')' : '') }}</span>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="h-full flex flex-col items-center justify-center p-8 text-center text-slate-400">
                                <div class="w-10 h-10 rounded-2xl bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-slate-400 mb-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                </div>
                                <p class="text-xs font-bold text-slate-700 dark:text-slate-300">Radiology Queue Clear</p>
                                <p class="text-[11px] text-slate-400 mt-0.5">No patients currently queued for radiology or scans</p>
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- Dispensary Pharmacy Queue (Patient-Grouped) -->
                <div class="bg-white dark:bg-slate-900 rounded-3xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] dark:shadow-none border border-slate-200/80 dark:border-slate-800/80 flex flex-col h-[520px] overflow-hidden"
                     x-data="{ phaSearch: '' }">
                    <div class="bg-gradient-to-r from-teal-600 to-emerald-700 p-4 sm:p-5 shrink-0 flex justify-between items-center text-white shadow-md shadow-teal-600/10">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-2xl bg-white/15 backdrop-blur-xs flex items-center justify-center border border-white/20">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path></svg>
                            </div>
                            <div>
                                <h3 class="text-sm font-black">Dispensary Pharmacy</h3>
                                <p class="text-teal-100 text-[11px]">{{ count($pharmacyQueue) }} Patient(s) Waiting for Medication</p>
                            </div>
                        </div>
                        <span class="text-xs font-mono font-bold bg-white/20 px-2.5 py-1 rounded-xl">{{ count($pharmacyQueue) }} waiting</span>
                    </div>

                    <!-- Column Mini Search if needed -->
                    @if(count($pharmacyQueue) > 3)
                        <div class="px-4 py-2 bg-teal-50/50 dark:bg-slate-800/60 border-b border-teal-100 dark:border-slate-800 flex items-center justify-between text-xs">
                            <span class="text-[11px] font-bold text-slate-500">Dispense List</span>
                            <input type="text" x-model="phaSearch" placeholder="Filter patient..." class="w-32 text-[10px] px-2 py-0.5 rounded-lg bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700">
                        </div>
                    @endif

                    <div class="p-3.5 overflow-y-auto flex-1 bg-slate-50/50 dark:bg-slate-900/50 space-y-2.5 custom-scrollbar">
                        @forelse($pharmacyQueue as $phaPatient)
                            <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-2xs border transition-all relative overflow-hidden group
                                {{ $phaPatient->is_priority ? 'border-amber-400/80 dark:border-amber-500/50 bg-gradient-to-r from-amber-50/30 to-white dark:from-amber-950/20 dark:to-slate-800' : 'border-slate-200/80 dark:border-slate-700' }}"
                                :class="viewMode === 'compact' ? 'p-2.5' : 'p-3.5'"
                                x-show="!phaSearch || '{{ strtolower($phaPatient->first_name.' '.$phaPatient->last_name.' '.$phaPatient->queue_number) }}'.includes(phaSearch.toLowerCase().trim())">
                                
                                <!-- Left Status Accent Line -->
                                <div class="absolute left-0 top-0 bottom-0 w-1 {{ $phaPatient->status === 'claimed' ? 'bg-emerald-500' : 'bg-teal-500' }}"></div>

                                <!-- Card Header: Queue # & Priority Badge & Primary Status -->
                                <div class="flex justify-between items-center mb-1.5 pl-2">
                                    <div class="flex items-center gap-1.5 flex-wrap">
                                        <span class="font-mono font-black text-slate-900 dark:text-white text-xs">#{{ $phaPatient->queue_number }}</span>
                                        <span class="text-[10px] font-mono text-slate-400">({{ $phaPatient->p_id }})</span>
                                        @if($phaPatient->is_priority)
                                            <span class="bg-amber-100 dark:bg-amber-950/60 text-amber-700 dark:text-amber-300 text-[9px] font-extrabold px-1.5 py-0.2 rounded-full border border-amber-200 dark:border-amber-800">Priority: {{ $phaPatient->classification }}</span>
                                        @endif
                                    </div>
                                    <div class="shrink-0">
                                        <span class="inline-flex items-center gap-1 bg-teal-50 dark:bg-teal-950/60 text-teal-700 dark:text-teal-300 text-[10px] font-bold px-2 py-0.5 rounded-full border border-teal-200 dark:border-teal-800">
                                            {{ ucfirst($phaPatient->status) }}
                                        </span>
                                    </div>
                                </div>

                                <!-- Patient Name & Wait Time -->
                                <div class="flex justify-between items-baseline pl-2 mb-2">
                                    <h4 class="font-black text-slate-900 dark:text-white text-xs sm:text-sm truncate mr-2">{{ $phaPatient->first_name }} {{ $phaPatient->last_name }}</h4>
                                    <div class="shrink-0 text-[10px]">
                                        @if($phaPatient->wait_minutes >= 45)
                                            <span class="text-rose-600 dark:text-rose-400 font-bold bg-rose-50 dark:bg-rose-950/50 px-1.5 py-0.5 rounded border border-rose-200 dark:border-rose-900/50 animate-pulse">⏱️ {{ $phaPatient->wait_minutes }}m</span>
                                        @elseif($phaPatient->wait_minutes >= 30)
                                            <span class="text-amber-600 dark:text-amber-400 font-bold bg-amber-50 dark:bg-amber-950/50 px-1.5 py-0.5 rounded border border-amber-200 dark:border-amber-900/50">⏱️ {{ $phaPatient->wait_minutes }}m</span>
                                        @else
                                            <span class="text-slate-400">{{ $phaPatient->created_at->diffForHumans() }}</span>
                                        @endif
                                    </div>
                                </div>

                                <!-- Under Patient Name: Medication Prescriptions -->
                                <div class="pl-2">
                                    <div class="flex flex-wrap gap-1 items-center">
                                        @foreach($phaPatient->prescriptions as $rx)
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-bold bg-teal-50 dark:bg-teal-950/40 text-teal-700 dark:text-teal-300 border border-teal-200 dark:border-teal-800">
                                                <svg class="w-3 h-3 text-teal-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/></svg>
                                                <span>{{ $rx->medicines ?? 'Prescription' }}</span>
                                            </span>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="h-full flex flex-col items-center justify-center p-8 text-center text-slate-400">
                                <div class="w-10 h-10 rounded-2xl bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-slate-400 mb-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                </div>
                                <p class="text-xs font-bold text-slate-700 dark:text-slate-300">Pharmacy Queue Clear</p>
                                <p class="text-[11px] text-slate-400 mt-0.5">No patients waiting for medication dispensing</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
