@extends('layouts.frontdesk')

@section('header', 'Follow-Up Tracker')

@section('content')
<div class="max-w-7xl mx-auto space-y-6 pb-12 cursor-default" x-data="followupTracker()">

    {{-- Breadcrumb Navigation --}}
    <nav class="flex items-center gap-2 text-xs font-medium text-slate-500 dark:text-slate-400 py-1.5 border-b border-slate-200/60 dark:border-slate-800" aria-label="Breadcrumb">
        <a href="{{ route('frontdesk.dashboard') }}" class="hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors flex items-center gap-1.5 shrink-0">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
            <span>Home</span>
        </a>
        <span class="text-slate-300 dark:text-slate-700">/</span>
        <span class="hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors">Front Desk</span>
        <span class="text-slate-300 dark:text-slate-700">/</span>
        <span class="text-slate-700 dark:text-slate-300 font-semibold">Follow-Up Tracker</span>
    </nav>

    {{-- Header Section --}}
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 pb-2 border-b border-slate-200/80 dark:border-slate-800">
        <div>
            <h1 class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white tracking-tight flex items-center gap-2.5">
                <span class="w-9 h-9 sm:w-10 sm:h-10 rounded-2xl bg-emerald-100/80 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0 border border-emerald-500/20 shadow-2xs">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </span>
                <span>Patient Follow-Up Tracker</span>
            </h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Monitor scheduled patient return visits, track overdue checkups, and process prompt follow-up check-ins.</p>
        </div>

        <div class="flex items-center gap-2.5">
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 text-slate-600 dark:text-slate-400 text-xs font-semibold shadow-2xs">
                <span class="w-2 h-2 rounded-full {{ $overdueCount > 0 ? 'bg-rose-500 animate-ping' : 'bg-emerald-500' }}"></span>
                <span>{{ $overdueCount > 0 ? "{$overdueCount} Overdue Action(s)" : 'All Returns On Track' }}</span>
            </span>
        </div>
    </div>

    {{-- KPI Summary Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        {{-- Overdue --}}
        <a href="{{ route('frontdesk.followups.index', ['tab' => 'overdue']) }}"
            class="bg-white dark:bg-slate-900 rounded-2xl border {{ $tab === 'overdue' ? 'border-rose-500 ring-2 ring-rose-500/20' : 'border-slate-200/80 dark:border-slate-800 hover:border-rose-300' }} p-5 shadow-xs transition-all cursor-pointer group">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-rose-100/80 dark:bg-rose-950/60 text-rose-600 dark:text-rose-400 flex items-center justify-center border border-rose-500/20 shadow-2xs shrink-0 group-hover:scale-105 transition-transform">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    </div>
                    <div>
                        <p class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">Overdue Returns</p>
                        <p class="text-2xl font-black text-rose-600 dark:text-rose-400">{{ $overdueCount }}</p>
                    </div>
                </div>
                @if($overdueCount > 0)
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-100 text-rose-800 dark:bg-rose-950/60 dark:text-rose-300 border border-rose-200 animate-pulse">Needs Outreach</span>
                @endif
            </div>
            <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-2.5">Missed scheduled return date</p>
        </a>

        {{-- Due Today --}}
        <a href="{{ route('frontdesk.followups.index', ['tab' => 'due_today']) }}"
            class="bg-white dark:bg-slate-900 rounded-2xl border {{ $tab === 'due_today' ? 'border-amber-500 ring-2 ring-amber-500/20' : 'border-slate-200/80 dark:border-slate-800 hover:border-amber-300' }} p-5 shadow-xs transition-all cursor-pointer group">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-amber-100/80 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400 flex items-center justify-center border border-amber-500/20 shadow-2xs shrink-0 group-hover:scale-105 transition-transform">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                    </div>
                    <div>
                        <p class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">Due Today</p>
                        <p class="text-2xl font-black text-amber-600 dark:text-amber-400">{{ $dueTodayCount }}</p>
                    </div>
                </div>
                @if($dueTodayCount > 0)
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800 dark:bg-amber-950/60 dark:text-amber-300 border border-amber-200">Expected</span>
                @endif
            </div>
            <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-2.5">Scheduled to return today</p>
        </a>

        {{-- Next 7 Days --}}
        <a href="{{ route('frontdesk.followups.index', ['tab' => 'upcoming_7']) }}"
            class="bg-white dark:bg-slate-900 rounded-2xl border {{ $tab === 'upcoming_7' ? 'border-sky-500 ring-2 ring-sky-500/20' : 'border-slate-200/80 dark:border-slate-800 hover:border-sky-300' }} p-5 shadow-xs transition-all cursor-pointer group">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-sky-100/80 dark:bg-sky-950/60 text-sky-600 dark:text-sky-400 flex items-center justify-center border border-sky-500/20 shadow-2xs shrink-0 group-hover:scale-105 transition-transform">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                </div>
                <div>
                    <p class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">Next 7 Days</p>
                    <p class="text-2xl font-black text-sky-600 dark:text-sky-400">{{ $upcoming7Count }}</p>
                </div>
            </div>
            <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-2.5">Upcoming scheduled returns</p>
        </a>

        {{-- Fulfilled --}}
        <a href="{{ route('frontdesk.followups.index', ['tab' => 'fulfilled']) }}"
            class="bg-white dark:bg-slate-900 rounded-2xl border {{ $tab === 'fulfilled' ? 'border-emerald-500 ring-2 ring-emerald-500/20' : 'border-slate-200/80 dark:border-slate-800 hover:border-emerald-300' }} p-5 shadow-xs transition-all cursor-pointer group">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-emerald-100/80 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center border border-emerald-500/20 shadow-2xs shrink-0 group-hover:scale-105 transition-transform">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    </div>
                    <div>
                        <p class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">Fulfilled</p>
                        <p class="text-2xl font-black text-emerald-600 dark:text-emerald-400">{{ $fulfilledCount }}</p>
                    </div>
                </div>
                @if($totalFollowupsCount > 0)
                    @php
                        $rate = round(($fulfilledCount / $totalFollowupsCount) * 100);
                    @endphp
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300 border border-emerald-200">{{ $rate }}% Return</span>
                @endif
            </div>
            <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-2.5">Completed return consultations</p>
        </a>
    </div>

    {{-- Filter Toolbar & Status Tabs --}}
    <div class="bg-white/80 dark:bg-slate-900/80 backdrop-blur-md rounded-2xl border border-slate-200/80 dark:border-slate-800 p-4 sm:p-5 shadow-xs space-y-4">
        {{-- Navigation Tabs --}}
        <div class="flex flex-wrap items-center gap-1.5 border-b border-slate-200/80 dark:border-slate-800 pb-3">
            @php
                $tabs = [
                    'all' => ['label' => 'All Records', 'count' => $totalFollowupsCount],
                    'overdue' => ['label' => 'Overdue', 'count' => $overdueCount, 'color' => 'rose'],
                    'due_today' => ['label' => 'Due Today', 'count' => $dueTodayCount, 'color' => 'amber'],
                    'upcoming_7' => ['label' => 'Next 7 Days', 'count' => $upcoming7Count, 'color' => 'sky'],
                    'upcoming_all' => ['label' => 'All Future', 'count' => null],
                    'fulfilled' => ['label' => 'Fulfilled', 'count' => $fulfilledCount, 'color' => 'emerald'],
                ];
            @endphp
            @foreach($tabs as $tabKey => $tabData)
                @php
                    $isActive = $tab === $tabKey;
                    $badgeBg = match($tabData['color'] ?? '') {
                        'rose' => 'bg-rose-500 text-white',
                        'amber' => 'bg-amber-500 text-white',
                        'sky' => 'bg-sky-500 text-white',
                        'emerald' => 'bg-emerald-500 text-white',
                        default => 'bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-300',
                    };
                @endphp
                <a href="{{ route('frontdesk.followups.index', array_merge(request()->except('page'), ['tab' => $tabKey])) }}"
                    class="h-9 px-3.5 rounded-xl text-xs font-bold inline-flex items-center gap-2 transition-all cursor-pointer {{ $isActive ? 'bg-gradient-to-r from-emerald-600 to-emerald-800 text-white shadow-xs' : 'bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700' }}">
                    <span>{{ $tabData['label'] }}</span>
                    @if(isset($tabData['count']) && $tabData['count'] !== null)
                        <span class="px-1.5 py-0.2 rounded-full text-[10px] font-black {{ $isActive ? 'bg-white/20 text-white' : $badgeBg }}">
                            {{ $tabData['count'] }}
                        </span>
                    @endif
                </a>
            @endforeach
        </div>

        {{-- Search & Refinement Form --}}
        <form id="followupFilterForm" action="{{ route('frontdesk.followups.index') }}" method="GET" class="space-y-3">
            <input type="hidden" name="tab" value="{{ $tab }}">
            <input type="hidden" name="per_page" id="fo_per_page" value="{{ request('per_page', 15) }}">

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-3">
                {{-- Search Bar --}}
                <div class="sm:col-span-2 lg:col-span-5 relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                    </div>
                    <input type="text" name="search" value="{{ request('search') }}" 
                        placeholder="Search by patient name, ID, PhilHealth, phone, or reason..."
                        oninput="clearTimeout(this.timer); this.timer = setTimeout(() => { this.form.submit(); }, 500);"
                        class="h-11 pl-10 pr-4 block w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50/70 dark:bg-slate-800/60 text-slate-900 dark:text-white text-xs font-semibold focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 shadow-2xs placeholder:text-slate-400 transition-all">
                </div>

                {{-- Doctor Filter --}}
                <div class="sm:col-span-1 lg:col-span-3">
                    <select name="doctor_id" onchange="this.form.submit()"
                        class="h-11 px-3.5 block w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50/70 dark:bg-slate-800/60 text-slate-900 dark:text-white text-xs font-semibold focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 shadow-2xs cursor-pointer transition-all">
                        <option value="">All Attending Doctors</option>
                        @foreach($doctors as $doc)
                            <option value="{{ $doc->id }}" @selected(request('doctor_id') == $doc->id)>
                                {{ $doc->formatted_name ?? $doc->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Date From --}}
                <div class="sm:col-span-1 lg:col-span-2">
                    <input type="date" name="date_from" value="{{ request('date_from') }}" onchange="this.form.submit()"
                        class="h-11 px-3.5 block w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50/70 dark:bg-slate-800/60 text-slate-900 dark:text-white text-xs font-semibold focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 shadow-2xs cursor-pointer transition-all"
                        title="Follow-up Date From">
                </div>

                {{-- Date To --}}
                <div class="sm:col-span-1 lg:col-span-2 flex items-center gap-2">
                    <input type="date" name="date_to" value="{{ request('date_to') }}" onchange="this.form.submit()"
                        class="h-11 px-3.5 block w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50/70 dark:bg-slate-800/60 text-slate-900 dark:text-white text-xs font-semibold focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 shadow-2xs cursor-pointer transition-all"
                        title="Follow-up Date To">

                    @if(request('search') || request('doctor_id') || request('date_from') || request('date_to') || (request('tab') && request('tab') !== 'all'))
                        <a href="{{ route('frontdesk.followups.index') }}" 
                            class="h-11 px-3 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700 text-xs font-bold transition-colors flex items-center justify-center shadow-2xs shrink-0 cursor-pointer"
                            title="Reset All Filters">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </a>
                    @endif
                </div>
            </div>
        </form>
    </div>

    {{-- Main Follow-Up Records Table --}}
    <div class="bg-white dark:bg-slate-900 rounded-3xl shadow-xs border border-slate-200/90 dark:border-slate-800 overflow-hidden">
        <div class="px-6 py-5 border-b border-slate-100 dark:border-slate-800/80 bg-slate-50/70 dark:bg-slate-900/60 flex items-center justify-between">
            <div>
                <h3 class="text-base font-bold text-slate-900 dark:text-white">Scheduled Follow-Ups</h3>
                <span class="text-xs text-slate-500 dark:text-slate-400 font-medium">{{ $followups->total() }} record(s) matching current criteria</span>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/90 dark:bg-slate-950/80 text-slate-500 dark:text-slate-400 text-[11px] font-extrabold uppercase tracking-wider border-b border-slate-200/80 dark:border-slate-800">
                        <th class="p-4 sm:p-5">Patient Details</th>
                        <th class="p-4 sm:p-5">Prior Visit & Diagnosis</th>
                        <th class="p-4 sm:p-5">Follow-Up Reason</th>
                        <th class="p-4 sm:p-5">Target Return & Status</th>
                        <th class="p-4 sm:p-5">Attending Doctor</th>
                        <th class="p-4 sm:p-5 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800 text-sm">
                    @forelse($followups as $followup)
                        @php
                            $patient = $followup->patient;
                            $doctor = $followup->followupDoctor ?? $followup->doctor;
                            $isFulfilled = !is_null($followup->followup_completed_at);
                            $targetDate = $followup->followup_date ? \Carbon\Carbon::parse($followup->followup_date)->startOfDay() : null;
                            $today = now()->startOfDay();
                            
                            $isOverdue = !$isFulfilled && $targetDate && $targetDate->lt($today);
                            $isDueToday = !$isFulfilled && $targetDate && $targetDate->eq($today);
                            $isUpcoming = !$isFulfilled && $targetDate && $targetDate->gt($today);
                            $daysDiff = $targetDate ? abs((int) $targetDate->diffInDays($today, false)) : 0;
                        @endphp
                        <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/40 transition-colors {{ $isOverdue ? 'border-l-4 border-rose-500 bg-rose-50/20' : ($isDueToday ? 'border-l-4 border-amber-500 bg-amber-50/20' : '') }}">
                            {{-- Patient Details --}}
                            <td class="p-4 sm:p-5 align-top">
                                <div class="flex items-start gap-3">
                                    <div class="w-10 h-10 rounded-2xl bg-emerald-100/80 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-400 font-black flex items-center justify-center border border-emerald-500/20 shadow-2xs shrink-0 text-xs mt-0.5">
                                        {{ $patient ? substr($patient->first_name, 0, 1) . substr($patient->last_name, 0, 1) : 'PT' }}
                                    </div>
                                    <div class="min-w-0">
                                        <div class="flex items-center gap-2">
                                            @if($patient)
                                                <a href="{{ route('frontdesk.patients.show', $patient->id) }}" class="font-extrabold text-slate-900 dark:text-white text-sm hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors truncate">
                                                    {{ $patient->full_name }}
                                                </a>
                                            @else
                                                <span class="font-extrabold text-slate-900 dark:text-white text-sm">Unknown Citizen</span>
                                            @endif
                                            @if($patient && $patient->age)
                                                <span class="text-[10px] px-1.5 py-0.2 rounded bg-slate-100 dark:bg-slate-800 text-slate-500 font-semibold border border-slate-200/60 dark:border-slate-700">
                                                    {{ $patient->age }}y, {{ $patient->gender ?? $patient->sex ?? '—' }}
                                                </span>
                                            @endif
                                        </div>

                                        <div class="text-[11px] text-slate-500 dark:text-slate-400 space-y-0.5 mt-1 font-medium">
                                            <p class="font-mono text-emerald-700 dark:text-emerald-400 font-bold">ID: {{ $followup->patient_id }}</p>
                                            @if($patient && $patient->contact_number)
                                                <p class="flex items-center gap-1">
                                                    <svg class="w-3 h-3 text-slate-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                                    <span class="font-semibold">{{ $patient->contact_number }}</span>
                                                </p>
                                            @endif
                                            @if($patient && $patient->barangay)
                                                <p class="text-[10px] text-slate-400 truncate">Brgy. {{ $patient->barangay }}</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </td>

                            {{-- Prior Visit & Diagnosis --}}
                            <td class="p-4 sm:p-5 align-top">
                                <p class="text-xs font-bold text-slate-800 dark:text-slate-200">
                                    {{ $followup->created_at->format('M d, Y') }}
                                </p>
                                <p class="text-[10px] text-slate-400 mt-0.5">{{ $followup->created_at->diffForHumans() }}</p>

                                @if($followup->diagnosis)
                                    <div class="mt-1.5">
                                        <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block">Diagnosis</span>
                                        <p class="text-xs font-semibold text-slate-700 dark:text-slate-300 leading-snug line-clamp-2 mt-0.5" title="{{ $followup->diagnosis }}">
                                            {{ $followup->diagnosis }}
                                        </p>
                                    </div>
                                @endif
                            </td>

                            {{-- Follow-Up Reason --}}
                            <td class="p-4 sm:p-5 align-top">
                                <div class="bg-slate-50 dark:bg-slate-800/60 rounded-xl p-2.5 border border-slate-200/70 dark:border-slate-700/60 max-w-xs">
                                    <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block">Clinical Instruction</span>
                                    <p class="text-xs font-semibold text-slate-800 dark:text-slate-200 mt-1 leading-relaxed">
                                        {{ $followup->followup_reason ?? 'Routine Medical Re-evaluation' }}
                                    </p>
                                </div>
                            </td>

                            {{-- Target Return & Status --}}
                            <td class="p-4 sm:p-5 align-top">
                                <div class="space-y-1.5">
                                    <p class="text-sm font-black text-slate-900 dark:text-white">
                                        {{ $targetDate ? $targetDate->format('M d, Y') : '—' }}
                                    </p>

                                    @if($isFulfilled)
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-emerald-100 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800/60 shadow-2xs">
                                            <svg class="w-3 h-3 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                            <span>Fulfilled {{ $followup->followup_completed_at->format('M d') }}</span>
                                        </span>
                                    @elseif($isOverdue)
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-black bg-rose-100 text-rose-800 dark:bg-rose-950/60 dark:text-rose-300 border border-rose-200 dark:border-rose-800/60 shadow-2xs animate-pulse">
                                            <span class="w-1.5 h-1.5 rounded-full bg-rose-600"></span>
                                            <span>{{ $daysDiff }} Day{{ $daysDiff === 1 ? '' : 's' }} Overdue</span>
                                        </span>
                                    @elseif($isDueToday)
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-black bg-amber-100 text-amber-800 dark:bg-amber-950/60 dark:text-amber-300 border border-amber-200 dark:border-amber-800/60 shadow-2xs animate-pulse">
                                            <span class="w-1.5 h-1.5 rounded-full bg-amber-600"></span>
                                            <span>Due Today</span>
                                        </span>
                                    @elseif($isUpcoming)
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-sky-100 text-sky-800 dark:bg-sky-950/60 dark:text-sky-300 border border-sky-200 dark:border-sky-800/60 shadow-2xs">
                                            <svg class="w-3 h-3 text-sky-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3"/></svg>
                                            <span>In {{ $daysDiff }} Day{{ $daysDiff === 1 ? '' : 's' }}</span>
                                        </span>
                                    @else
                                        <span class="text-xs text-slate-400">—</span>
                                    @endif
                                </div>
                            </td>

                            {{-- Attending Doctor --}}
                            <td class="p-4 sm:p-5 align-top">
                                @if($doctor)
                                    <div class="flex items-center gap-2">
                                        <div class="w-7 h-7 rounded-lg bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-xs font-bold text-slate-600 dark:text-slate-400 border border-slate-200 dark:border-slate-700 shrink-0">
                                            {{ substr($doctor->name, 0, 1) }}
                                        </div>
                                        <div class="min-w-0">
                                            <p class="text-xs font-bold text-slate-800 dark:text-slate-200 truncate">{{ $doctor->formatted_name ?? $doctor->name }}</p>
                                            <p class="text-[10px] text-slate-400 capitalize">{{ str_replace('_', ' ', $doctor->role) }}</p>
                                        </div>
                                    </div>
                                @else
                                    <span class="text-xs text-slate-400 font-semibold">Any Attending MD</span>
                                @endif
                            </td>

                            {{-- Actions --}}
                            <td class="p-4 sm:p-5 align-top text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    {{-- Quick Check-In / Queue --}}
                                    @if(!$isFulfilled && $patient)
                                        <a href="{{ route('frontdesk.registration.index', ['patient_search' => $patient->patient_id, 'followup' => 1]) }}"
                                            class="h-8 px-2.5 rounded-xl inline-flex items-center gap-1 text-xs font-bold text-white bg-gradient-to-r from-emerald-600 to-emerald-700 hover:from-emerald-700 hover:to-emerald-800 shadow-2xs transition-all cursor-pointer active:scale-95"
                                            title="Check-In Patient for Follow-up Consultation">
                                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                            <span>Check-In</span>
                                        </a>
                                    @endif

                                    {{-- Reschedule Date --}}
                                    <button type="button"
                                        @click="openRescheduleModal({{ $followup->id }}, '{{ addslashes($patient->full_name ?? 'Citizen') }}', '{{ $followup->followup_date ? $followup->followup_date->format('Y-m-d') : '' }}', '{{ $followup->followup_doctor_id ?? $followup->doctor_id }}', '{{ addslashes($followup->followup_reason ?? '') }}')"
                                        class="h-8 px-2.5 rounded-xl inline-flex items-center gap-1 text-xs font-bold text-slate-700 dark:text-slate-300 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 border border-slate-200 dark:border-slate-700 shadow-2xs transition-all cursor-pointer active:scale-95"
                                        title="Reschedule Follow-Up Date">
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                        <span>Reschedule</span>
                                    </button>

                                    {{-- Mark Fulfilled (if unfulfilled) --}}
                                    @if(!$isFulfilled)
                                        <button type="button"
                                            @click="openFulfillModal({{ $followup->id }}, '{{ addslashes($patient->full_name ?? 'Citizen') }}')"
                                            class="h-8 w-8 rounded-xl inline-flex items-center justify-center text-emerald-700 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-950/40 hover:bg-emerald-100 dark:hover:bg-emerald-900/60 border border-emerald-200 dark:border-emerald-800/60 shadow-2xs transition-all cursor-pointer active:scale-95"
                                            title="Mark as Fulfilled / Resolved">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-16 text-center text-slate-500 dark:text-slate-400">
                                <div class="w-14 h-14 rounded-2xl bg-slate-100 dark:bg-slate-800 text-slate-400 dark:text-slate-500 mx-auto flex items-center justify-center mb-3 shadow-xs">
                                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                </div>
                                <h3 class="text-base font-bold text-slate-900 dark:text-white mb-1">No follow-ups found</h3>
                                <p class="text-xs text-slate-500 dark:text-slate-400">There are no patient follow-up records matching your selected tab or filter criteria.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($followups->hasPages() || $followups->total() > 15)
            <div class="px-6 py-4 border-t border-slate-100 dark:border-slate-800/80 bg-slate-50/70 dark:bg-slate-900/60 flex flex-col sm:flex-row items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <label class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Per Page</label>
                    <select onchange="document.getElementById('fo_per_page').value = this.value; document.getElementById('followupFilterForm').submit()"
                        class="h-9 px-3 pr-8 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-white text-xs font-bold shadow-2xs cursor-pointer focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500">
                        @foreach([10, 15, 25, 50, 100] as $size)
                            <option value="{{ $size }}" @selected(request('per_page', 15) == $size)>{{ $size }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="w-full sm:w-auto">
                    @if($followups->hasPages())
                        {{ $followups->appends(request()->query())->links('vendor.pagination.shadcn') }}
                    @endif
                </div>
            </div>
        @endif
    </div>

    {{-- ═══════════════════════════════════════════════════════════ --}}
    {{-- Alpine Modals (Reschedule & Fulfill) --}}
    {{-- ═══════════════════════════════════════════════════════════ --}}

    {{-- Reschedule Modal --}}
    <div x-show="rescheduleModalOpen" 
        style="display: none;" 
        class="fixed inset-0 z-[999] overflow-y-auto" 
        aria-labelledby="modal-reschedule-title" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
            <div x-show="rescheduleModalOpen" 
                x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" 
                x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" 
                class="fixed inset-0 bg-slate-950/70 backdrop-blur-sm transition-opacity" 
                @click="rescheduleModalOpen = false" aria-hidden="true"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div x-show="rescheduleModalOpen" 
                x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" 
                x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                @click.stop
                class="relative z-50 inline-block align-bottom bg-white dark:bg-slate-900 rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full border border-slate-200/90 dark:border-slate-800">
                
                {{-- Modal Header --}}
                <div class="px-6 py-5 border-b border-slate-100 dark:border-slate-800 flex justify-between items-center bg-slate-50/70 dark:bg-slate-900/60">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-emerald-100/80 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center border border-emerald-500/20 shadow-2xs">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-slate-900 dark:text-white" id="modal-reschedule-title">Reschedule Follow-Up Date</h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400" x-text="activePatientName"></p>
                        </div>
                    </div>
                    <button type="button" @click="rescheduleModalOpen = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 p-1.5 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                {{-- Modal Form --}}
                <form :action="'/frontdesk/followups/' + activeConsultationId + '/reschedule'" method="POST" class="p-6 sm:p-7 space-y-4">
                    @csrf

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">New Return Date <span class="text-rose-500">*</span></label>
                        <input type="date" name="followup_date" x-model="rescheduleDate" min="{{ now()->toDateString() }}" required
                            class="h-11 px-3.5 block w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50/70 dark:bg-slate-800/60 text-slate-900 dark:text-white text-xs font-bold focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 shadow-2xs">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Assigned Attending Doctor</label>
                        <select name="followup_doctor_id" x-model="rescheduleDoctorId"
                            class="h-11 px-3.5 block w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50/70 dark:bg-slate-800/60 text-slate-900 dark:text-white text-xs font-semibold focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 shadow-2xs cursor-pointer">
                            <option value="">Keep Original Attending Doctor</option>
                            @foreach($doctors as $doc)
                                <option value="{{ $doc->id }}">{{ $doc->formatted_name ?? $doc->name }} ({{ ucfirst(str_replace('_', ' ', $doc->role)) }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Reason for Rescheduling / Clinical Notes</label>
                        <textarea name="followup_reason" x-model="rescheduleReason" rows="3"
                            placeholder="e.g. Patient called to postpone due to work conflict, requested return next Tuesday..."
                            class="w-full p-3 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50/70 dark:bg-slate-800/60 text-slate-900 dark:text-white text-xs font-semibold focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 shadow-2xs placeholder:text-slate-400 resize-none"></textarea>
                    </div>

                    <div class="pt-2 flex justify-end gap-2.5">
                        <button type="button" @click="rescheduleModalOpen = false" class="px-4 py-2.5 rounded-xl text-xs font-bold text-slate-600 dark:text-slate-400 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 transition cursor-pointer">
                            Cancel
                        </button>
                        <button type="submit" class="px-5 py-2.5 rounded-xl text-xs font-bold text-white bg-gradient-to-r from-emerald-600 to-emerald-700 hover:from-emerald-700 hover:to-emerald-800 shadow-xs transition cursor-pointer active:scale-95">
                            Save Rescheduled Date
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Mark Fulfilled Modal --}}
    <div x-show="fulfillModalOpen" 
        style="display: none;" 
        class="fixed inset-0 z-[999] overflow-y-auto" 
        aria-labelledby="modal-fulfill-title" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
            <div x-show="fulfillModalOpen" 
                x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" 
                x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" 
                class="fixed inset-0 bg-slate-950/70 backdrop-blur-sm transition-opacity" 
                @click="fulfillModalOpen = false" aria-hidden="true"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div x-show="fulfillModalOpen" 
                x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" 
                x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                @click.stop
                class="relative z-50 inline-block align-bottom bg-white dark:bg-slate-900 rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md w-full border border-slate-200/90 dark:border-slate-800">
                
                {{-- Modal Header --}}
                <div class="px-6 py-5 border-b border-slate-100 dark:border-slate-800 flex justify-between items-center bg-slate-50/70 dark:bg-slate-900/60">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-emerald-100/80 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center border border-emerald-500/20 shadow-2xs">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-slate-900 dark:text-white" id="modal-fulfill-title">Complete Follow-Up</h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400" x-text="activePatientName"></p>
                        </div>
                    </div>
                    <button type="button" @click="fulfillModalOpen = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 p-1.5 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                {{-- Modal Form --}}
                <form :action="'/frontdesk/followups/' + activeConsultationId + '/fulfill'" method="POST" class="p-6 sm:p-7 space-y-4">
                    @csrf

                    <p class="text-xs text-slate-600 dark:text-slate-300 leading-relaxed">
                        Are you sure you want to mark this follow-up as fulfilled? This will mark the return consultation as satisfied and clear active overdue alerts for this record.
                    </p>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Resolution Notes (Optional)</label>
                        <textarea name="resolution_notes" rows="3"
                            placeholder="e.g. Patient contacted by phone, reported full recovery and medication compliance; no further visit needed."
                            class="w-full p-3 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50/70 dark:bg-slate-800/60 text-slate-900 dark:text-white text-xs font-semibold focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 shadow-2xs placeholder:text-slate-400 resize-none"></textarea>
                    </div>

                    <div class="pt-2 flex justify-end gap-2.5">
                        <button type="button" @click="fulfillModalOpen = false" class="px-4 py-2.5 rounded-xl text-xs font-bold text-slate-600 dark:text-slate-400 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 transition cursor-pointer">
                            Cancel
                        </button>
                        <button type="submit" class="px-5 py-2.5 rounded-xl text-xs font-bold text-white bg-gradient-to-r from-emerald-600 to-emerald-700 hover:from-emerald-700 hover:to-emerald-800 shadow-xs transition cursor-pointer active:scale-95">
                            Mark Fulfilled
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function followupTracker() {
        return {
            rescheduleModalOpen: false,
            fulfillModalOpen: false,
            activeConsultationId: null,
            activePatientName: '',
            rescheduleDate: '',
            rescheduleDoctorId: '',
            rescheduleReason: '',

            openRescheduleModal(id, name, date, doctorId, reason) {
                this.activeConsultationId = id;
                this.activePatientName = name;
                this.rescheduleDate = date || '';
                this.rescheduleDoctorId = doctorId || '';
                this.rescheduleReason = reason || '';
                this.rescheduleModalOpen = true;
            },

            openFulfillModal(id, name) {
                this.activeConsultationId = id;
                this.activePatientName = name;
                this.fulfillModalOpen = true;
            }
        };
    }
</script>
@endpush
@endsection
