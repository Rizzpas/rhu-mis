@extends('layouts.frontdesk')

@section('header', 'Front Desk Dashboard')

@section('content')
<div class="max-w-7xl mx-auto space-y-8 pb-12 cursor-default" x-data="dashboardCalendar()">
    
    <!-- Hero Operational Banner -->
    <div class="relative overflow-hidden bg-gradient-to-r from-emerald-600 to-emerald-800 rounded-3xl p-6 sm:p-8 shadow-[0_20px_50px_rgba(16,185,129,0.15)] text-white">
        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="flex items-center gap-5 sm:gap-6">
                <div class="shrink-0">
                    <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-2xl bg-white/20 backdrop-blur-md border border-white/30 flex items-center justify-center overflow-hidden shadow-lg">
                        @if(auth()->user()->avatar_url)
                            <img src="{{ auth()->user()->avatar_url }}" alt="{{ auth()->user()->name }}" class="w-full h-full object-cover">
                        @else
                            <span class="text-2xl sm:text-3xl font-black text-white">
                                {{ auth()->user()->initials }}
                            </span>
                        @endif
                    </div>
                </div>
                <div>
                    <h1 class="text-2xl sm:text-3xl font-black tracking-tight drop-shadow-xs">Welcome back, {{ auth()->user()->formatted_name }}!</h1>
                    <p class="text-emerald-50 text-xs sm:text-sm font-medium opacity-90 mt-1 max-w-xl">
                        Information Desk is currently <span class="px-2 py-0.5 bg-emerald-400/30 rounded-lg font-bold">Active</span>. Monitor today's scheduled consultations, walk-in arrivals, and queue distribution in real time.
                    </p>
                </div>
            </div>
            
            <div class="flex flex-wrap items-center gap-3 sm:gap-4">
                <!-- Live Time Card -->
                <div class="bg-white/10 backdrop-blur-md rounded-2xl p-3.5 border border-white/20 min-w-[120px]"
                    x-data="{ time: '{{ now()->format('h:i A') }}' }"
                    x-init="setInterval(() => { time = new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: true }) }, 1000)">
                    <p class="text-[10px] uppercase font-bold tracking-widest text-emerald-200 opacity-80 mb-0.5">Local Time</p>
                    <p class="text-base sm:text-lg font-bold tabular-nums" x-text="time">{{ now()->format('h:i A') }}</p>
                </div>
                <!-- Date Card -->
                <div class="bg-white/10 backdrop-blur-md rounded-2xl p-3.5 border border-white/20 min-w-[120px]">
                    <p class="text-[10px] uppercase font-bold tracking-widest text-emerald-200 opacity-80 mb-0.5">System Date</p>
                    <p class="text-base sm:text-lg font-bold">{{ now()->format('M d, Y') }}</p>
                </div>
                <!-- Active Practitioners Card -->
                <div class="bg-white/10 backdrop-blur-md rounded-2xl p-3.5 border border-white/20 min-w-[120px]">
                    <p class="text-[10px] uppercase font-bold tracking-widest text-emerald-200 opacity-80 mb-0.5">Duty Staff</p>
                    <p class="text-base sm:text-lg font-bold">{{ ($staffGroups['Doctors']->count() + $staffGroups['Clinical Nurses']->count() + $staffGroups['Vitals Nurses']->count()) }} Personnel</p>
                </div>
            </div>
        </div>

        <!-- Quick Action Buttons -->
        <div class="relative z-10 mt-6 pt-5 border-t border-white/15 flex flex-wrap items-center justify-between gap-4">
            <div class="flex items-center gap-2 text-xs font-semibold text-emerald-100">
                <span class="w-2 h-2 rounded-full bg-emerald-300 animate-pulse"></span>
                <span>All systems operational · Live Calendar synced with appointment bookings</span>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('frontdesk.queue-overview') }}" class="bg-white/10 hover:bg-white/20 text-white border border-white/20 px-4 py-2 rounded-xl text-xs sm:text-sm font-bold shadow-xs backdrop-blur-sm transition-all flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16" /></svg>
                    <span>View Queue</span>
                </a>
                <a href="{{ route('frontdesk.registration.index') }}" class="bg-white text-emerald-800 hover:bg-emerald-50 px-4 py-2 rounded-xl text-xs sm:text-sm font-black shadow-md transition-all duration-200 hover:-translate-y-0.5 flex items-center gap-2">
                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    <span>Walk-in Registration</span>
                </a>
            </div>
        </div>

        <!-- Decorative background elements -->
        <div class="absolute top-0 right-0 -mr-20 -mt-20 w-64 h-64 bg-white/10 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute bottom-0 left-0 -ml-20 -mb-20 w-64 h-64 bg-emerald-400/20 rounded-full blur-3xl pointer-events-none"></div>
    </div>

    <!-- Key Operational Metric Cards (KPI Strip) -->
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-4 sm:gap-6">
        <!-- Today's Appointments -->
        <div class="bg-white dark:bg-slate-900 rounded-3xl p-5 shadow-[0_8px_30px_rgb(0,0,0,0.04)] dark:shadow-none border border-slate-100 dark:border-slate-800/80 flex items-center justify-between">
            <div>
                <p class="text-[10px] text-slate-500 dark:text-slate-400 uppercase font-bold tracking-wider mb-1">Today's Visits</p>
                <p class="text-2xl sm:text-3xl font-black text-slate-900 dark:text-white" x-text="stats.today">0</p>
                <p class="text-[11px] font-medium text-emerald-600 dark:text-emerald-400 mt-0.5">Scheduled for today</p>
            </div>
            <div class="w-11 h-11 rounded-2xl bg-emerald-100/80 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0 border border-emerald-500/20 shadow-2xs">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
            </div>
        </div>

        <!-- Approved / Pending Check-in -->
        <div class="bg-white dark:bg-slate-900 rounded-3xl p-5 shadow-[0_8px_30px_rgb(0,0,0,0.04)] dark:shadow-none border border-slate-100 dark:border-slate-800/80 flex items-center justify-between">
            <div>
                <p class="text-[10px] text-slate-500 dark:text-slate-400 uppercase font-bold tracking-wider mb-1">Approved Bookings</p>
                <p class="text-2xl sm:text-3xl font-black text-teal-600 dark:text-teal-400" x-text="stats.approved">0</p>
                <p class="text-[11px] font-medium text-slate-400 mt-0.5">Confirmed appointments</p>
            </div>
            <div class="w-11 h-11 rounded-2xl bg-teal-100/80 dark:bg-teal-950/60 text-teal-600 dark:text-teal-400 flex items-center justify-center shrink-0 border border-teal-500/20 shadow-2xs">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
        </div>

        <!-- Registered / In Process -->
        <div class="bg-white dark:bg-slate-900 rounded-3xl p-5 shadow-[0_8px_30px_rgb(0,0,0,0.04)] dark:shadow-none border border-slate-100 dark:border-slate-800/80 flex items-center justify-between">
            <div>
                <p class="text-[10px] text-slate-500 dark:text-slate-400 uppercase font-bold tracking-wider mb-1">Queued / Triaged</p>
                <p class="text-2xl sm:text-3xl font-black text-amber-600 dark:text-amber-400" x-text="stats.registered">0</p>
                <p class="text-[11px] font-medium text-slate-400 mt-0.5">Waiting for consult</p>
            </div>
            <div class="w-11 h-11 rounded-2xl bg-amber-100/80 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400 flex items-center justify-center shrink-0 border border-amber-500/20 shadow-2xs">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
        </div>

        <!-- Completed Encounters -->
        <div class="bg-white dark:bg-slate-900 rounded-3xl p-5 shadow-[0_8px_30px_rgb(0,0,0,0.04)] dark:shadow-none border border-slate-100 dark:border-slate-800/80 flex items-center justify-between">
            <div>
                <p class="text-[10px] text-slate-500 dark:text-slate-400 uppercase font-bold tracking-wider mb-1">Completed</p>
                <p class="text-2xl sm:text-3xl font-black text-emerald-600 dark:text-emerald-400" x-text="stats.done">0</p>
                <p class="text-[11px] font-medium text-slate-400 mt-0.5">Discharged patients</p>
            </div>
            <div class="w-11 h-11 rounded-2xl bg-emerald-100/80 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0 border border-emerald-500/20 shadow-2xs">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            </div>
        </div>

        <!-- Total System Bookings -->
        <div class="col-span-2 sm:col-span-1 bg-white dark:bg-slate-900 rounded-3xl p-5 shadow-[0_8px_30px_rgb(0,0,0,0.04)] dark:shadow-none border border-slate-100 dark:border-slate-800/80 flex items-center justify-between">
            <div>
                <p class="text-[10px] text-slate-500 dark:text-slate-400 uppercase font-bold tracking-wider mb-1">Total Scheduled</p>
                <p class="text-2xl sm:text-3xl font-black text-indigo-600 dark:text-indigo-400" x-text="stats.total">0</p>
                <p class="text-[11px] font-medium text-slate-400 mt-0.5">All month calendar</p>
            </div>
            <div class="w-11 h-11 rounded-2xl bg-indigo-100/80 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400 flex items-center justify-center shrink-0 border border-indigo-500/20 shadow-2xs">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
            </div>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
        
        <!-- Left Column: Staff Present Widget -->
        <div class="lg:col-span-1 space-y-6">
            <div class="bg-white dark:bg-slate-900 rounded-3xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] dark:shadow-none border border-slate-200/80 dark:border-slate-800/80 overflow-hidden transition-all">
                <div class="p-5 border-b border-slate-100 dark:border-slate-800 bg-slate-50/60 dark:bg-slate-900/60 flex items-center justify-between">
                    <div class="flex items-center gap-2.5">
                        <span class="w-8 h-8 rounded-xl bg-emerald-100/80 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0 border border-emerald-500/20">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                        </span>
                        <div>
                            <h2 class="text-sm font-black text-slate-900 dark:text-white tracking-tight">Clinical Staff</h2>
                            <p class="text-[10px] font-medium text-slate-400">On duty today</p>
                        </div>
                    </div>
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-emerald-50 dark:bg-emerald-950/50 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                        Active
                    </span>
                </div>
                
                <div class="p-5 space-y-6 max-h-[640px] overflow-y-auto custom-scrollbar">
                    @php
                        $hasAnyStaff = false;
                    @endphp
                    @foreach($staffGroups as $groupName => $staffMembers)
                        @if($staffMembers->count() > 0)
                            @php $hasAnyStaff = true; @endphp
                            <div>
                                <div class="flex items-center justify-between mb-3">
                                    <h3 class="text-[11px] font-extrabold text-slate-400 dark:text-slate-500 uppercase tracking-widest">{{ $groupName }}</h3>
                                    <span class="text-[10px] font-bold text-slate-400 bg-slate-100 dark:bg-slate-800 px-1.5 py-0.5 rounded-md">{{ $staffMembers->count() }}</span>
                                </div>
                                <div class="space-y-2.5">
                                    @foreach($staffMembers as $staff)
                                        <div class="flex items-center gap-3 p-2.5 rounded-2xl hover:bg-slate-50 dark:hover:bg-slate-800/60 border border-transparent hover:border-slate-200/60 dark:hover:border-slate-700/60 transition-all group">
                                            <div class="relative shrink-0">
                                                <div class="w-10 h-10 rounded-xl bg-emerald-100 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 flex items-center justify-center font-black text-xs shrink-0 shadow-2xs overflow-hidden border border-emerald-500/20">
                                                    @if($staff->avatar_url)
                                                        <img src="{{ $staff->avatar_url }}" alt="{{ $staff->name }}" class="w-full h-full object-cover">
                                                    @else
                                                        {{ $staff->initials }}
                                                    @endif
                                                </div>
                                                <span class="absolute -bottom-0.5 -right-0.5 w-3 h-3 bg-emerald-500 border-2 border-white dark:border-slate-900 rounded-full"></span>
                                            </div>
                                            <div class="min-w-0 flex-1">
                                                <h4 class="text-xs font-bold text-slate-800 dark:text-white group-hover:text-emerald-600 dark:group-hover:text-emerald-400 truncate transition-colors">{{ $staff->formatted_name }}</h4>
                                                <p class="text-[10px] uppercase font-bold tracking-wider text-slate-400 dark:text-slate-500 truncate mt-0.5">
                                                    {{ $staff->specialization ?: ucwords(str_replace('_', ' ', $staff->role)) }}
                                                </p>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    @endforeach

                    @if(!$hasAnyStaff)
                        <div class="text-center py-8">
                            <div class="w-12 h-12 rounded-2xl bg-slate-100 dark:bg-slate-800 text-slate-400 flex items-center justify-center mx-auto mb-2">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                            </div>
                            <p class="text-xs font-semibold text-slate-500 dark:text-slate-400">No staff currently clocked in.</p>
                            <p class="text-[10px] text-slate-400 mt-0.5">Personnel status updates upon check-in.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Right Column: Calendar Card -->
        <div class="lg:col-span-3">
            <div class="bg-white dark:bg-slate-900 rounded-3xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] dark:shadow-none border border-slate-200/80 dark:border-slate-800/80 overflow-hidden h-full flex flex-col transition-all">
                <div class="p-5 border-b border-slate-100 dark:border-slate-800 bg-slate-50/60 dark:bg-slate-900/60 flex flex-col sm:flex-row sm:items-center justify-between shrink-0 gap-4">
                    <div class="flex items-center gap-3">
                        <span class="w-9 h-9 rounded-2xl bg-emerald-100/80 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0 border border-emerald-500/20 shadow-2xs">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        </span>
                        <div>
                            <h2 class="text-base font-black text-slate-900 dark:text-white tracking-tight">Consultation Calendar</h2>
                            <p class="text-xs text-slate-400">Click any date or appointment to view full schedule</p>
                        </div>
                    </div>
                    
                    <!-- Modern Legend -->
                    <div class="flex flex-wrap items-center gap-2 sm:gap-3 text-xs font-bold">
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800 text-[11px]">
                            <span class="w-2 h-2 rounded-full bg-emerald-600"></span> Approved
                        </span>
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-amber-50 dark:bg-amber-950/40 text-amber-700 dark:text-amber-400 border border-amber-200 dark:border-amber-800 text-[11px]">
                            <span class="w-2 h-2 rounded-full bg-amber-600"></span> Registered / Queued
                        </span>
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-green-50 dark:bg-green-950/40 text-green-700 dark:text-green-400 border border-green-200 dark:border-green-800 text-[11px]">
                            <span class="w-2 h-2 rounded-full bg-green-600"></span> Done
                        </span>
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-purple-50 dark:bg-purple-950/40 text-purple-700 dark:text-purple-400 border border-purple-200 dark:border-purple-800 text-[11px]">
                            <span class="w-2 h-2 rounded-full bg-purple-600"></span> Rescheduled
                        </span>
                    </div>
                </div>
                
                <div class="p-4 sm:p-6 grow">
                    <!-- FullCalendar Container -->
                    <div id="calendar" class="min-h-[680px]"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Day View Modal (Frosted Floating Dialog) -->
    <div x-show="isModalOpen" 
         class="fixed inset-0 z-50 overflow-y-auto" 
         aria-labelledby="modal-title" role="dialog" aria-modal="true"
         style="display: none;">
        
        <!-- Background overlay -->
        <div x-show="isModalOpen" 
             x-transition:enter="ease-out duration-300" 
             x-transition:enter-start="opacity-0" 
             x-transition:enter-end="opacity-100" 
             x-transition:leave="ease-in duration-200" 
             x-transition:leave-start="opacity-100" 
             x-transition:leave-end="opacity-0" 
             class="fixed inset-0 bg-slate-950/80 backdrop-blur-sm transition-opacity" 
             @click="closeModal()"></div>

        <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
            <!-- Modal panel -->
            <div x-show="isModalOpen" 
                 x-transition:enter="ease-out duration-300" 
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" 
                 x-transition:leave="ease-in duration-200" 
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" 
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                 class="relative transform overflow-hidden rounded-3xl bg-white dark:bg-slate-900 text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-3xl border border-slate-200 dark:border-slate-800 flex flex-col max-h-[85vh]">
                
                <!-- Modal Header -->
                <div class="px-6 py-5 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between shrink-0 bg-slate-50/60 dark:bg-slate-900/60">
                    <div>
                        <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-lg bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 text-[10px] font-bold uppercase tracking-wider mb-1 border border-emerald-500/20">
                            Daily Schedule Roster
                        </div>
                        <h3 class="text-xl font-black text-slate-900 dark:text-white tracking-tight" id="modal-title">
                            Appointments for <span x-text="selectedDateText" class="text-emerald-600 dark:text-emerald-400"></span>
                        </h3>
                        <p class="text-xs font-medium text-slate-400 mt-0.5" x-text="selectedEvents.length + ' scheduled consultation(s)'"></p>
                    </div>
                    <button type="button" @click="closeModal()" class="rounded-xl bg-white dark:bg-slate-800 p-2 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors border border-slate-200 dark:border-slate-700 shadow-2xs cursor-pointer">
                        <span class="sr-only">Close</span>
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>

                <!-- Modal Body (List of Appointments) -->
                <div class="px-6 py-5 overflow-y-auto grow bg-slate-50/30 dark:bg-slate-950/30 custom-scrollbar">
                    
                    <template x-if="selectedEvents.length === 0">
                        <div class="text-center py-12">
                            <div class="w-14 h-14 rounded-2xl bg-slate-100 dark:bg-slate-800 text-slate-400 flex items-center justify-center mx-auto mb-3 shadow-2xs">
                                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            </div>
                            <h3 class="text-sm font-bold text-slate-800 dark:text-white">No Consultations Scheduled</h3>
                            <p class="text-xs text-slate-400 mt-1">There are no patient appointments booked for this specific date.</p>
                        </div>
                    </template>

                    <div class="space-y-3.5" x-show="selectedEvents.length > 0">
                        <template x-for="event in selectedEvents" :key="event.id">
                            <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200/80 dark:border-slate-700/80 p-4 sm:p-5 shadow-xs hover:shadow-md transition-all group">
                                <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4">
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center gap-2 mb-1.5 flex-wrap">
                                            <span class="text-xs font-extrabold text-slate-900 dark:text-white font-mono" x-text="event.extendedProps.time"></span>
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold uppercase tracking-wider text-white" 
                                                  :style="`background-color: ${event.backgroundColor}`"
                                                  x-text="event.extendedProps.status"></span>
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-slate-100 dark:bg-slate-900 text-slate-600 dark:text-slate-400 border border-slate-200 dark:border-slate-700" 
                                                  x-text="event.extendedProps.type"></span>
                                        </div>
                                        <h4 class="text-base font-black text-slate-900 dark:text-white group-hover:text-emerald-600 dark:group-hover:text-emerald-400 transition-colors" x-text="event.extendedProps.patient_name"></h4>
                                        <p class="text-xs font-semibold text-slate-500 dark:text-slate-400 mt-0.5" x-text="event.extendedProps.classification || 'General Constituent'"></p>
                                        
                                        <div class="mt-3 grid grid-cols-1 sm:grid-cols-2 gap-y-2 gap-x-4 text-xs">
                                            <div class="flex items-center gap-1.5 text-slate-600 dark:text-slate-400">
                                                <svg class="w-3.5 h-3.5 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                                                <span x-text="event.extendedProps.contact || 'No contact provided'"></span>
                                            </div>
                                            <div class="flex items-center gap-1.5 text-slate-600 dark:text-slate-400 truncate">
                                                <svg class="w-3.5 h-3.5 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                                                <span x-text="event.extendedProps.email || 'No email provided'" class="truncate"></span>
                                            </div>
                                            <div class="sm:col-span-2 flex items-start gap-1.5 text-slate-600 dark:text-slate-400 pt-1 border-t border-slate-100 dark:border-slate-700/60">
                                                <span class="font-bold text-slate-500 dark:text-slate-400">Chief Complaint:</span>
                                                <span class="italic text-slate-700 dark:text-slate-300" x-text="event.extendedProps.reason"></span>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Actions for the specific appointment -->
                                    <div class="flex sm:flex-col gap-2 shrink-0 sm:items-end sm:justify-center pt-3 sm:pt-0 border-t sm:border-t-0 border-slate-100 dark:border-slate-700/60">
                                        <template x-if="event.extendedProps.status === 'Approved'">
                                            <form :action="`/frontdesk/appointments/${event.id}/check-in`" method="POST" class="w-full sm:w-auto">
                                                @csrf
                                                <button type="submit" class="w-full sm:w-auto bg-gradient-to-r from-emerald-600 to-emerald-700 hover:from-emerald-700 hover:to-emerald-800 text-white px-4 py-2 rounded-xl text-xs font-bold shadow-md shadow-emerald-600/20 transition-all flex items-center justify-center gap-1.5 cursor-pointer">
                                                    <span>Check In Patient</span>
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                                                </button>
                                            </form>
                                        </template>
                                        <template x-if="event.extendedProps.status === 'Registered' || event.extendedProps.status === 'Triaged'">
                                            <span class="w-full sm:w-auto bg-slate-100 dark:bg-slate-950 text-slate-500 dark:text-slate-400 px-3 py-1.5 rounded-xl text-xs font-bold border border-slate-200 dark:border-slate-800 flex items-center justify-center gap-1.5">
                                                <svg class="w-3.5 h-3.5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                                <span>Active in Queue</span>
                                            </span>
                                        </template>
                                        <template x-if="event.extendedProps.status === 'Done'">
                                            <span class="w-full sm:w-auto bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-400 px-3 py-1.5 rounded-xl text-xs font-bold border border-emerald-200 dark:border-emerald-800 flex items-center justify-center gap-1.5">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                                <span>Visit Finished</span>
                                            </span>
                                        </template>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>

                </div>
                
                <!-- Modal Footer -->
                <div class="bg-white dark:bg-slate-900 px-6 py-4 border-t border-slate-100 dark:border-slate-800 sm:flex sm:flex-row-reverse shrink-0">
                    <button type="button" @click="closeModal()" class="w-full sm:w-auto inline-flex justify-center rounded-xl border border-slate-200 dark:border-slate-700 shadow-xs px-5 py-2.5 bg-white dark:bg-slate-800 text-xs font-bold text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors cursor-pointer">
                        Close Window
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js'></script>
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('dashboardCalendar', () => ({
            isModalOpen: false,
            selectedDateText: '',
            selectedDateIsToday: false,
            selectedEvents: [],
            calendar: null,
            stats: { total: 0, today: 0, approved: 0, registered: 0, done: 0, rescheduled: 0 },
            activeFilter: 'all',
            isRefreshing: false,

            init() {
                var calendarEl = document.getElementById('calendar');
                
                this.calendar = new FullCalendar.Calendar(calendarEl, {
                    timeZone: 'local',
                    initialView: 'dayGridMonth',
                    themeSystem: 'standard',
                    headerToolbar: {
                        left: 'prev,next today',
                        center: 'title',
                        right: 'dayGridMonth,timeGridWeek,listWeek'
                    },
                    events: '/frontdesk/api/appointments',
                    eventColor: '#059669', 
                    eventDisplay: 'block',
                    eventTimeFormat: {
                        hour: 'numeric',
                        minute: '2-digit',
                        meridiem: 'short'
                    },
                    dayMaxEvents: 3,

                    eventContent: (arg) => {
                        const status = (arg.event.extendedProps?.status || '').toLowerCase();
                        const time = arg.event.extendedProps?.time || '';
                        const name = arg.event.extendedProps?.patient_name || arg.event.title;

                        let dotColor = '#059669';
                        let badgeBg = 'bg-emerald-500/10 text-emerald-800 dark:text-emerald-200 border-emerald-500/30';

                        if (status.includes('approved')) {
                            dotColor = '#059669';
                            badgeBg = 'bg-emerald-500/10 text-emerald-800 dark:text-emerald-200 border-emerald-500/30';
                        } else if (status.includes('registered') || status.includes('triaged')) {
                            dotColor = '#d97706';
                            badgeBg = 'bg-amber-500/10 text-amber-800 dark:text-amber-200 border-amber-500/30';
                        } else if (status.includes('done')) {
                            dotColor = '#16a34a';
                            badgeBg = 'bg-green-500/10 text-green-800 dark:text-green-200 border-green-500/30';
                        } else if (status.includes('rescheduled')) {
                            dotColor = '#7c3aed';
                            badgeBg = 'bg-purple-500/10 text-purple-800 dark:text-purple-200 border-purple-500/30';
                        }

                        const container = document.createElement('div');
                        container.className = `group/evt flex items-center gap-1.5 w-full px-2 py-1 rounded-lg border text-left text-[11px] leading-tight font-medium shadow-2xs backdrop-blur-xs transition-all overflow-hidden cursor-pointer ${badgeBg}`;
                        container.innerHTML = `
                            <span class="w-1.5 h-1.5 rounded-full shrink-0" style="background-color: ${dotColor}"></span>
                            <span class="font-bold shrink-0 opacity-75 font-mono text-[10px]">${time}</span>
                            <span class="truncate font-semibold text-slate-800 dark:text-slate-100">${name}</span>
                        `;
                        return { domNodes: [container] };
                    },

                    eventsSet: (events) => {
                        this.updateStats(events);
                    },
                    
                    dateClick: (info) => {
                        this.openDayModal(info.dateStr);
                    },
                    
                    eventClick: (info) => {
                        info.jsEvent.preventDefault();
                        const date = info.event.start;
                        const pad = n => String(n).padStart(2, '0');
                        const dateStr = `${date.getFullYear()}-${pad(date.getMonth() + 1)}-${pad(date.getDate())}`;
                        this.openDayModal(dateStr);
                    }
                });
                
                this.calendar.render();
            },

            updateStats(events) {
                const pad = n => String(n).padStart(2, '0');
                const now = new Date();
                const todayStr = `${now.getFullYear()}-${pad(now.getMonth() + 1)}-${pad(now.getDate())}`;

                let total = events.length;
                let today = 0;
                let approved = 0;
                let registered = 0;
                let done = 0;
                let rescheduled = 0;

                events.forEach(evt => {
                    const status = (evt.extendedProps?.status || '').toLowerCase();
                    const date = evt.start;
                    if (date) {
                        const evtDateStr = `${date.getFullYear()}-${pad(date.getMonth() + 1)}-${pad(date.getDate())}`;
                        if (evtDateStr === todayStr) {
                            today++;
                        }
                    }

                    if (status.includes('approved')) approved++;
                    else if (status.includes('registered') || status.includes('triaged')) registered++;
                    else if (status.includes('done')) done++;
                    else if (status.includes('rescheduled')) rescheduled++;
                });

                this.stats = { total, today, approved, registered, done, rescheduled };
            },

            openDayModal(dateStr) {
                const dateObj = new Date(dateStr + 'T00:00:00');
                this.selectedDateText = dateObj.toLocaleDateString('en-US', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });
                
                const pad = n => String(n).padStart(2, '0');
                const now = new Date();
                const todayStr = `${now.getFullYear()}-${pad(now.getMonth() + 1)}-${pad(now.getDate())}`;
                this.selectedDateIsToday = (dateStr === todayStr);

                const allEvents = this.calendar.getEvents();
                
                this.selectedEvents = allEvents.filter(event => {
                    const date = event.start;
                    const eventDateStr = `${date.getFullYear()}-${pad(date.getMonth() + 1)}-${pad(date.getDate())}`;
                    return eventDateStr === dateStr;
                }).map(event => {
                    return {
                        id: event.id,
                        title: event.title,
                        backgroundColor: event.backgroundColor,
                        extendedProps: event.extendedProps
                    };
                });
                
                this.selectedEvents.sort((a, b) => {
                    return new Date('1970/01/01 ' + a.extendedProps.time) - new Date('1970/01/01 ' + b.extendedProps.time);
                });

                this.isModalOpen = true;
                document.body.style.overflow = 'hidden';
            },

            closeModal() {
                this.isModalOpen = false;
                setTimeout(() => {
                    document.body.style.overflow = '';
                }, 300);
            }
        }));
    });
</script>

<style>
    /* FullCalendar Professional Tailoring */
    .fc {
        font-family: inherit;
        --fc-border-color: #f1f5f9;
        --fc-today-bg-color: rgba(5, 150, 105, 0.04);
    }
    
    .dark .fc {
        --fc-border-color: #1e293b;
        --fc-today-bg-color: rgba(16, 185, 129, 0.08);
    }

    .fc .fc-toolbar {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        margin-bottom: 1.25rem !important;
        padding-bottom: 1rem;
        border-bottom: 1px solid #f1f5f9;
    }
    .dark .fc .fc-toolbar {
        border-bottom-color: #1e293b;
    }

    .fc .fc-toolbar-title {
        font-size: 1.25rem !important;
        font-weight: 900 !important;
        letter-spacing: -0.025em;
        color: #0f172a !important;
    }
    .dark .fc .fc-toolbar-title {
        color: #f8fafc !important;
    }

    .fc .fc-button {
        border-radius: 0.75rem !important;
        font-size: 0.75rem !important;
        font-weight: 700 !important;
        padding: 0.45rem 0.85rem !important;
        text-transform: capitalize !important;
        transition: all 0.2s ease !important;
        border: 1px solid #e2e8f0 !important;
        background: #ffffff !important;
        color: #475569 !important;
        box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.04) !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
    }
    .dark .fc .fc-button {
        background: #1e293b !important;
        border-color: #334155 !important;
        color: #cbd5e1 !important;
    }
    .fc .fc-button:hover {
        background: #f8fafc !important;
        color: #0f172a !important;
        border-color: #cbd5e1 !important;
    }
    .dark .fc .fc-button:hover {
        background: #334155 !important;
        color: #ffffff !important;
    }
    .fc .fc-button-primary:not(:disabled).fc-button-active, 
    .fc .fc-button-primary:not(:disabled):active {
        background: #059669 !important;
        border-color: #059669 !important;
        color: #ffffff !important;
        box-shadow: 0 4px 6px -1px rgba(5, 150, 105, 0.25) !important;
    }
    .dark .fc .fc-button-primary:not(:disabled).fc-button-active, 
    .dark .fc .fc-button-primary:not(:disabled):active {
        background: #059669 !important;
        border-color: #059669 !important;
        color: #ffffff !important;
    }

    .fc .fc-button-group {
        background: #f1f5f9;
        padding: 0.25rem;
        border-radius: 0.85rem;
        display: inline-flex;
        gap: 0.2rem;
    }
    .dark .fc .fc-button-group {
        background: #0f172a;
        border: 1px solid #1e293b;
    }
    .fc .fc-button-group .fc-button {
        border: none !important;
        box-shadow: none !important;
        border-radius: 0.65rem !important;
        background: transparent !important;
    }
    .fc .fc-button-group .fc-button-active {
        background: #ffffff !important;
        color: #065f46 !important;
        box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1) !important;
        font-weight: 800 !important;
    }
    .dark .fc .fc-button-group .fc-button-active {
        background: #1e293b !important;
        color: #34d399 !important;
    }

    .fc-theme-standard th {
        background: #f8fafc;
        border-color: #f1f5f9 !important;
        padding: 0.65rem 0 !important;
    }
    .dark .fc-theme-standard th {
        background: #090e1a;
        border-color: #1e293b !important;
    }
    .fc-col-header-cell-cushion {
        font-size: 0.7rem !important;
        font-weight: 800 !important;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        color: #64748b !important;
    }
    .dark .fc-col-header-cell-cushion {
        color: #94a3b8 !important;
    }

    .fc-theme-standard td, .fc-theme-standard .fc-scrollgrid {
        border-color: #f1f5f9 !important;
    }
    .dark .fc-theme-standard td, .dark .fc-theme-standard .fc-scrollgrid {
        border-color: #1e293b !important;
    }

    .fc-daygrid-day-frame {
        min-height: 105px !important;
        padding: 4px !important;
        transition: background-color 0.15s ease;
    }
    .fc-daygrid-day-top {
        flex-direction: row !important;
        justify-content: space-between !important;
        align-items: center !important;
        padding: 2px 4px !important;
    }
    .fc-daygrid-day-number {
        font-size: 0.75rem !important;
        font-weight: 800 !important;
        color: #475569 !important;
        width: 24px;
        height: 24px;
        display: inline-flex !important;
        align-items: center;
        justify-content: center;
        border-radius: 9999px;
        transition: all 0.2s;
    }
    .dark .fc-daygrid-day-number {
        color: #94a3b8 !important;
    }
    .fc-daygrid-day:hover {
        background-color: #f8fafc;
        cursor: pointer;
    }
    .dark .fc-daygrid-day:hover {
        background-color: rgba(30, 41, 59, 0.6) !important;
    }
    .fc-daygrid-day:hover .fc-daygrid-day-number {
        background: #e2e8f0;
        color: #0f172a !important;
    }
    .dark .fc-daygrid-day:hover .fc-daygrid-day-number {
        background: #334155;
        color: #ffffff !important;
    }

    .fc-day-today {
        background: rgba(5, 150, 105, 0.04) !important;
    }
    .dark .fc-day-today {
        background: rgba(16, 185, 129, 0.08) !important;
    }
    .fc-day-today .fc-daygrid-day-number {
        background: #059669 !important;
        color: #ffffff !important;
        font-weight: 900 !important;
        box-shadow: 0 2px 4px 0 rgba(5, 150, 105, 0.35) !important;
    }

    .fc-day-other {
        background: #fafbfc;
        opacity: 0.45;
    }
    .dark .fc-day-other {
        background: #0b1120;
        opacity: 0.25;
    }

    .fc-daygrid-event-harness {
        margin-bottom: 3px !important;
    }
    .fc-event {
        background: transparent !important;
        border: none !important;
        box-shadow: none !important;
        padding: 0 !important;
        margin: 0 !important;
    }
    .fc-event:hover {
        transform: translateY(-1px);
    }

    .fc-daygrid-more-link {
        font-size: 0.7rem !important;
        font-weight: 800 !important;
        color: #059669 !important;
        padding: 2px 6px !important;
        border-radius: 6px !important;
        background: #ecfdf5 !important;
        border: 1px solid #a7f3d0 !important;
        transition: all 0.15s;
    }
    .dark .fc-daygrid-more-link {
        background: rgba(5, 150, 105, 0.2) !important;
        border-color: rgba(16, 185, 129, 0.4) !important;
        color: #34d399 !important;
    }
    .fc-daygrid-more-link:hover {
        background: #059669 !important;
        color: #ffffff !important;
    }
</style>
@endpush
@endsection
