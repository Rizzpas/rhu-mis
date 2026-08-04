@extends('layouts.doctor')

@section('header', 'Doctor Triage Queue')

@section('content')
<div class="max-w-7xl mx-auto space-y-6 pb-12">
    
    <!-- Stats / Header -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] dark:shadow-[0_8px_30px_rgb(0,0,0,0.2)] border border-slate-100 dark:border-slate-800 p-6 flex flex-col justify-center transition-all duration-300">
            <h3 class="text-sm font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1">My Active Queue</h3>
            <p class="text-4xl font-extrabold text-emerald-600 dark:text-emerald-500">{{ count($queue) }} <span class="text-lg font-medium text-slate-400 dark:text-slate-500">Patients</span></p>
        </div>
        <a href="{{ route('doctor.waiting-results') }}" class="bg-white dark:bg-slate-900 rounded-2xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] dark:shadow-[0_8px_30px_rgb(0,0,0,0.2)] border border-amber-200 dark:border-amber-800/50 p-6 flex flex-col justify-center transition-all duration-300 hover:shadow-lg hover:border-amber-300 group">
            <h3 class="text-sm font-bold text-amber-600 dark:text-amber-400 uppercase tracking-wider mb-1 flex items-center gap-1.5">
                <svg class="w-4 h-4 animate-spin-slow" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path></svg>
                Waiting for Results
            </h3>
            <p class="text-4xl font-extrabold text-amber-600 dark:text-amber-500">{{ $awaitingLabsCount }} <span class="text-lg font-medium text-slate-400 dark:text-slate-500">Pending</span></p>
            <p class="text-xs text-slate-400 mt-1 group-hover:text-amber-600 transition">Click to view →</p>
        </a>
        <div class="md:col-span-2 relative overflow-hidden bg-gradient-to-r from-emerald-600 to-green-700 dark:from-emerald-800 dark:to-green-900 rounded-2xl shadow-[0_8px_30px_rgb(0,0,0,0.1)] border border-emerald-700 dark:border-emerald-900 p-8 flex items-center justify-between text-white transition-colors duration-300">
            <!-- Glassmorphism decorative elements -->
            <div class="absolute -top-24 -right-24 w-64 h-64 bg-white opacity-5 rounded-full blur-3xl"></div>
            <div class="absolute -bottom-24 -left-24 w-64 h-64 bg-green-400 opacity-10 rounded-full blur-3xl"></div>
            
            <div class="relative z-10 flex items-center gap-6">
                <div class="shrink-0">
                    <div class="w-20 h-20 rounded-2xl bg-white/20 backdrop-blur-md border border-white/30 flex items-center justify-center overflow-hidden shadow-lg">
                        @if(auth()->user()->avatar_url)
                            <img src="{{ auth()->user()->avatar_url }}" alt="{{ auth()->user()->name }}" class="w-full h-full object-cover">
                        @else
                            <span class="text-3xl font-black text-white">
                                {{ auth()->user()->initials }}
                            </span>
                        @endif
                    </div>
                </div>
                <div>
                    <h2 class="text-3xl font-extrabold mb-2 drop-shadow-sm tracking-tight">Welcome back, {{ $user->formatted_name }}</h2>
                    <p class="text-emerald-50 dark:text-emerald-100/80 text-sm font-medium max-w-lg">Review your assigned patients, examine their triaged vitals, and start their consultations.</p>
                </div>
            </div>
            <div class="hidden sm:block relative z-10 bg-white/20 dark:bg-black/20 p-4 rounded-2xl backdrop-blur-md shadow-[0_8px_30px_rgb(0,0,0,0.1)] border border-white/10 transition-transform duration-500 hover:rotate-3">
                <svg class="w-10 h-10 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                </svg>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-emerald-50 dark:bg-emerald-900/30 border border-emerald-200 dark:border-emerald-800/50 p-4 rounded-xl shadow-sm animate-fade-in-up">
            <div class="flex">
                <div class="shrink-0">
                    <svg class="h-5 w-5 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-emerald-800 dark:text-emerald-300 font-bold">{{ session('success') }}</p>
                </div>
            </div>
        </div>
    @endif

    <!-- Patient Queue Grid Section -->
    <div class="pt-4">
        <div class="flex justify-between items-end mb-6">
            <div>
                <h3 class="font-extrabold text-slate-900 dark:text-white text-2xl flex items-center gap-2">
                    <span class="bg-emerald-100 dark:bg-emerald-900/50 text-emerald-600 dark:text-emerald-400 p-1.5 rounded-lg">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    </span>
                    Patient Queue
                </h3>
                <p class="text-sm text-slate-500 dark:text-slate-400 mt-1 font-medium">Patients waiting for consultation today, {{ \Carbon\Carbon::today()->format('F d, Y') }}</p>
            </div>
        </div>

        <div id="patient-queue-section" data-dynamic-block="true">
        @if(count($queue) > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($queue as $index => $consultation)
                    <div class="group flex flex-col bg-white dark:bg-slate-900 rounded-2xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] dark:shadow-[0_8px_30px_rgb(0,0,0,0.2)] border {{ $consultation->status === 'results_ready' ? 'border-emerald-400 dark:border-emerald-500/50 ring-4 ring-emerald-400/30 animate-pulse' : ($consultation->status === 'active' ? 'border-yellow-400 dark:border-yellow-500/50 ring-4 ring-yellow-400/20' : 'border-slate-100 dark:border-slate-800 hover:border-emerald-300 dark:hover:border-emerald-700/50 hover:shadow-xl') }} transition-all duration-300 overflow-hidden relative">
                        
                        <!-- Card Header: Position & Queue Number -->
                        <div class="flex justify-between items-center p-4 border-b {{ $consultation->status === 'results_ready' ? 'border-emerald-200 dark:border-emerald-900/30 bg-emerald-50 dark:bg-emerald-900/20' : ($consultation->status === 'active' ? 'border-yellow-100 dark:border-yellow-900/30 bg-yellow-50/50 dark:bg-yellow-900/10' : 'border-slate-100 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/30 group-hover:bg-emerald-50/50 dark:group-hover:bg-emerald-900/10') }} transition-colors">
                            <div class="flex items-center gap-2">
                                <span class="flex items-center justify-center w-8 h-8 rounded-full {{ $consultation->status === 'results_ready' ? 'bg-emerald-500 text-white' : ($consultation->status === 'active' ? 'bg-yellow-400 text-yellow-900' : 'bg-emerald-600 text-white') }} font-black text-sm shadow-sm">{{ $index + 1 }}</span>
                                <span class="bg-black dark:bg-slate-700 text-white font-black px-2.5 py-1 rounded-md text-sm shadow-sm tracking-wide">{{ $consultation->queue_number }}</span>
                                @include('partials.queue-wait-indicator', ['createdAt' => $consultation->created_at])
                            </div>
                            
                            @if(\Illuminate\Support\Str::startsWith($consultation->queue_number, 'PED-E'))
                                <span class="text-[10px] font-black px-2.5 py-1 rounded-full bg-rose-600 text-white uppercase tracking-wider shadow-sm flex items-center gap-1 animate-pulse">
                                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg> 
                                    Emergency
                                </span>
                            @elseif($consultation->status === 'results_ready')
                                <span class="text-[10px] font-black px-2.5 py-1 rounded-full bg-emerald-500 text-white uppercase tracking-wider shadow-sm flex items-center gap-1 animate-pulse">
                                    <span class="w-2 h-2 rounded-full bg-white animate-ping mr-0.5"></span>
                                    ✓ Results Ready
                                </span>
                            @elseif($consultation->status === 'active')
                                <span class="text-[10px] font-black px-2.5 py-1 rounded-full bg-yellow-400 text-yellow-900 uppercase tracking-wider shadow-sm flex items-center gap-1">
                                    <span class="w-2 h-2 rounded-full bg-yellow-700 animate-ping mr-0.5"></span>
                                    In Progress
                                </span>
                            @endif
                        </div>

                        <!-- Card Body: Patient Details -->
                        <div class="p-5 flex-1 flex flex-col">
                            <div class="mb-4">
                                <h4 class="text-xl font-extrabold text-slate-900 dark:text-white leading-tight mb-1 truncate" title="{{ $consultation->patient->full_name }}">{{ $consultation->patient->full_name }}</h4>
                                <div class="flex items-center gap-2 text-xs font-semibold text-slate-500 dark:text-slate-400">
                                    <span class="text-teal-700 dark:text-teal-400 font-bold">#{{ $consultation->patient->patient_id }}</span>
                                    <span>&bull;</span>
                                    <span class="{{ in_array($consultation->patient->classification, ['Senior Citizen', 'PWD']) ? 'text-yellow-600 dark:text-yellow-500' : 'text-emerald-600 dark:text-emerald-400' }} bg-slate-100 dark:bg-slate-800 px-2 py-0.5 rounded">{{ $consultation->patient->classification }}</span>
                                    <span>&bull;</span>
                                    <span>{{ $consultation->patient->dob ? \Carbon\Carbon::parse($consultation->patient->dob)->format('M d, Y') . ' (' . \Carbon\Carbon::parse($consultation->patient->dob)->age . ' yrs)' : 'DOB: N/A' }}</span>
                                    <span>&bull;</span>
                                    <span>{{ $consultation->patient->sex }}</span>
                                </div>
                            </div>

                            <!-- Vitals Grid -->
                            <div class="grid grid-cols-4 gap-2 mb-4 bg-slate-50 dark:bg-slate-800/50 p-3 rounded-xl border border-slate-100 dark:border-slate-700/50">
                                <div class="text-center">
                                    <span class="block text-slate-400 font-bold uppercase text-[9px] tracking-wider mb-0.5">BP</span>
                                    <span class="font-black text-slate-800 dark:text-slate-200 text-xs">{{ $consultation->preTriage?->blood_pressure ?? '--' }}</span>
                                </div>
                                <div class="text-center border-l border-slate-200 dark:border-slate-700">
                                    <span class="block text-slate-400 font-bold uppercase text-[9px] tracking-wider mb-0.5">Temp</span>
                                    <span class="font-black text-xs {{ floatval($consultation->preTriage?->temperature ?? 0) > 37.5 ? 'text-rose-500' : 'text-slate-800 dark:text-slate-200' }}">{{ $consultation->preTriage?->temperature ?? '--' }}</span>
                                </div>
                                <div class="text-center border-l border-slate-200 dark:border-slate-700">
                                    <span class="block text-slate-400 font-bold uppercase text-[9px] tracking-wider mb-0.5">HR</span>
                                    <span class="font-black text-slate-800 dark:text-slate-200 text-xs">{{ $consultation->preTriage?->heart_rate ?? '--' }}</span>
                                </div>
                                <div class="text-center border-l border-slate-200 dark:border-slate-700">
                                    <span class="block text-slate-400 font-bold uppercase text-[9px] tracking-wider mb-0.5">SpO2</span>
                                    <span class="font-black text-slate-800 dark:text-slate-200 text-xs">{{ $consultation->preTriage?->spo2 ?? $consultation->preTriage?->oxygen_saturation ?? '--' }}{{ $consultation->preTriage?->spo2 ? '%' : '' }}</span>
                                </div>
                            </div>

                            <!-- Chief Complaint -->
                            <div class="flex-1 mb-5">
                                <p class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-1 flex items-center gap-1">
                                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"></path></svg>
                                    Chief Complaint
                                </p>
                                <p class="text-sm text-slate-700 dark:text-slate-300 italic font-medium leading-relaxed line-clamp-2">"{{ $consultation->preTriage?->symptoms ?? 'N/A' }}"</p>
                            </div>

                            <!-- Action Button -->
                            <a href="{{ route('doctor.consultation.start', $consultation->id) }}" class="mt-auto w-full flex items-center justify-center gap-2 {{ $consultation->status === 'active' ? 'bg-yellow-400 hover:bg-yellow-500 text-yellow-900 ring-4 ring-yellow-400/30' : 'bg-slate-900 dark:bg-white hover:bg-emerald-600 dark:hover:bg-emerald-500 text-white dark:text-slate-900 group-hover:ring-4 group-hover:ring-emerald-500/20' }} font-bold py-3 px-4 rounded-xl shadow-sm transition-all duration-300 text-sm">
                                <span>{{ $consultation->status === 'active' ? 'Resume Consultation' : 'View & Consult' }}</span>
                                <svg class="w-4 h-4 transition-transform group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                            </a>
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
                <p class="text-slate-500 dark:text-slate-400 max-w-sm mx-auto">You have no patients waiting. Take a break or check your pending lab results below.</p>
            </div>
        @endif
    </div>
    </div>

    <!-- Awaiting Labs Section -->
    @if($awaitingLabs->count() > 0)
    <div class="pt-8">
        <h3 class="font-extrabold text-indigo-900 dark:text-indigo-400 text-xl flex items-center gap-2 mb-6">
            <span class="bg-indigo-100 dark:bg-indigo-900/50 text-indigo-600 dark:text-indigo-400 p-1.5 rounded-lg">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path></svg>
            </span>
            Awaiting Lab Results
            <span class="ml-2 text-sm font-bold bg-indigo-100 text-indigo-700 dark:bg-indigo-900 dark:text-indigo-300 py-0.5 px-2.5 rounded-full">{{ $awaitingLabs->count() }} On Hold</span>
        </h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($awaitingLabs as $consultation)
                <div class="group flex flex-col bg-indigo-50/50 dark:bg-slate-900 rounded-2xl shadow-sm border border-indigo-200 dark:border-indigo-800/50 hover:border-indigo-400 dark:hover:border-indigo-500 transition-all duration-300 overflow-hidden relative">
                    <div class="absolute inset-0 bg-gradient-to-br from-indigo-100/30 to-transparent dark:from-indigo-900/10 pointer-events-none"></div>
                    <div class="p-5 flex-1 flex flex-col relative z-10">
                        <div class="flex justify-between items-start mb-3">
                            <span class="bg-indigo-600 text-white font-black px-2.5 py-1 rounded-md text-sm shadow-sm">{{ $consultation->queue_number }}</span>
                            <span class="text-[10px] font-black px-2 py-1 rounded-full bg-indigo-200 dark:bg-indigo-900/50 text-indigo-800 dark:text-indigo-300 uppercase tracking-widest flex items-center gap-1">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                Paused
                            </span>
                        </div>
                        <h4 class="text-lg font-extrabold text-slate-900 dark:text-white leading-tight mb-4">{{ $consultation->patient->full_name }}</h4>
                        <a href="{{ route('doctor.consultation.start', $consultation->id) }}" class="mt-auto w-full flex items-center justify-center gap-2 bg-white dark:bg-slate-800 border-2 border-indigo-200 dark:border-indigo-700 hover:border-indigo-600 dark:hover:border-indigo-400 text-indigo-700 dark:text-indigo-300 hover:bg-indigo-50 dark:hover:bg-indigo-900/30 font-bold py-2.5 px-4 rounded-xl shadow-sm transition-colors text-sm">
                            <span>Check Results & Resume</span>
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Handled Patients Cards -->
    <div class="pt-8 pb-4">
        <h3 class="font-extrabold text-slate-900 dark:text-white text-xl flex items-center gap-2 mb-6">
            <span class="bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 p-1.5 rounded-lg">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </span>
            Completed Today
            <span class="ml-2 text-sm font-bold bg-slate-200 dark:bg-slate-800 text-slate-700 dark:text-slate-300 py-0.5 px-2.5 rounded-full">{{ count($handledPatients) }}</span>
        </h3>
        
        @if(count($handledPatients) > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                @foreach($handledPatients as $handled)
                    <div class="bg-white dark:bg-slate-900 rounded-xl p-4 border border-slate-100 dark:border-slate-800 shadow-sm hover:shadow-md transition-shadow group flex flex-col h-full">
                        <div class="flex items-start gap-3 mb-3">
                            <div class="bg-emerald-50 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 font-extrabold rounded-full w-10 h-10 flex items-center justify-center shrink-0 border border-emerald-100 dark:border-emerald-800">
                                {{ substr(str_replace('Dr. ', '', $handled->patient->first_name), 0, 1) }}
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="font-bold text-slate-900 dark:text-white truncate">{{ $handled->patient->full_name }}</p>
                                <p class="text-[10px] font-semibold text-slate-500 uppercase">{{ $handled->patient->classification }}</p>
                            </div>
                        </div>
                        <div class="bg-slate-50 dark:bg-slate-800/50 rounded-lg p-2.5 mb-3 flex-1 border border-slate-100 dark:border-slate-800">
                            <p class="text-xs text-slate-600 dark:text-slate-300 font-medium line-clamp-2" title="{{ $handled->diagnosis ?: 'No diagnosis provided' }}">
                                <span class="text-slate-400 font-bold mr-1">Dx:</span>{{ $handled->diagnosis ?: 'No diagnosis' }}
                            </p>
                            @if($handled->prescription)
                                <p class="text-[10px] text-emerald-600 dark:text-emerald-400 mt-1.5 font-semibold line-clamp-1 flex items-center gap-1">
                                    <svg class="w-3 h-3 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                                    Rx Added
                                </p>
                            @endif
                        </div>
                        <div class="flex justify-between items-center text-[10px] text-slate-400 font-semibold mt-auto pt-2 border-t border-slate-100 dark:border-slate-800">
                            <span>{{ \Carbon\Carbon::parse($handled->consultation_date)->format('M d') }}</span>
                            <span class="bg-slate-100 dark:bg-slate-800 px-1.5 py-0.5 rounded text-slate-500">{{ $handled->updated_at->format('h:i A') }}</span>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="bg-slate-50 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-800 rounded-2xl p-8 text-center">
                <p class="text-sm text-slate-500 dark:text-slate-400 font-medium">You have not completed any consultations today.</p>
            </div>
        @endif
    </div>

</div>
@endsection
