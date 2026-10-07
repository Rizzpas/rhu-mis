@extends('layouts.doctor')

@section('header', 'Consultation Room')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">
    <!-- Header Section (Content Management Style) -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 pb-4 border-b border-slate-200/80 dark:border-slate-800">
        <div class="flex items-center gap-3">
            <a href="{{ route('doctor.dashboard') }}" class="p-2.5 bg-white dark:bg-slate-800 rounded-xl text-slate-500 dark:text-slate-400 hover:text-emerald-600 dark:hover:text-emerald-400 hover:bg-emerald-50 dark:hover:bg-slate-700/60 shadow-2xs border border-slate-200 dark:border-slate-700 transition-all active:scale-95" title="Back to Clinical Queue">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            </a>
            <div>
                <h1 class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white tracking-tight flex items-center gap-2">
                    <span>Active Clinical Consultation</span>
                </h1>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Recording patient diagnosis, clinical notes, laboratory requests, and medicine prescription.</p>
            </div>
        </div>
        <div class="flex items-center gap-2.5">
            <div class="bg-emerald-100 dark:bg-emerald-950/60 text-emerald-800 dark:text-emerald-300 px-4 py-2 rounded-xl font-bold text-xs sm:text-sm shadow-2xs border border-emerald-500/20 flex items-center gap-2">
                <span class="w-2.5 h-2.5 bg-emerald-500 rounded-full animate-pulse"></span>
                <span>Queue #{{ $consultation->queue_number }}</span>
            </div>
        </div>
    </div>

    @if($errors->any())
        <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-md shadow-sm">
            <div class="flex">
                <div class="shrink-0">
                    <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" /></svg>
                </div>
                <div class="ml-3 text-sm text-red-700">
                    <ul class="list-disc pl-5 space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Left Column: Patient Info & Vitals -->
        <div class="lg:col-span-1 space-y-6">
            <!-- Patient Demographics -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
                <div class="bg-slate-50 dark:bg-slate-800/90 border-b border-slate-200 dark:border-slate-700 p-4">
                    <div class="flex items-start justify-between gap-2">
                        <div class="flex items-center space-x-4 flex-1 min-w-0">
                        <div class="h-16 w-16 bg-teal-100 dark:bg-teal-900/60 text-teal-600 dark:text-teal-400 rounded-full flex items-center justify-center text-2xl font-bold border-2 border-white dark:border-slate-700 shadow-sm">
                            {{ substr($patient->first_name, 0, 1) }}{{ substr($patient->last_name, 0, 1) }}
                        </div>
                        <div class="flex-1 min-w-0">
                                <div class="flex items-start justify-between gap-2">
                                    <h3 class="text-lg font-extrabold text-slate-800 dark:text-white leading-tight" title="{{ $patient->full_name }}">{{ $patient->full_name }}</h3>
                                    <span class="bg-indigo-100 dark:bg-indigo-950/70 text-indigo-700 dark:text-indigo-300 text-[10px] uppercase tracking-wider font-black px-2 py-1 rounded shrink-0">{{ $patient->classification }}</span>
                                </div>
                                <p class="text-sm text-slate-500 dark:text-slate-400 font-medium mt-1">{{ \Carbon\Carbon::parse($patient->dob)->age }} yrs &bull; {{ $patient->sex }}</p>
                                @if($patient->blood_type)
                                    <p class="text-xs text-red-500 dark:text-red-400 font-bold mt-1 inline-flex items-center gap-1 bg-red-50 dark:bg-red-950/50 px-2 py-0.5 rounded border border-red-100 dark:border-red-900/50">
                                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path></svg>
                                        {{ $patient->blood_type }}
                                    </p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                <div class="p-4 space-y-3 text-sm bg-white dark:bg-gray-800">
                    <div class="grid grid-cols-3 gap-1">
                        <span class="text-slate-500 dark:text-slate-400 font-medium col-span-1">DOB:</span>
                        <span class="text-slate-800 dark:text-slate-100 font-semibold col-span-2">{{ \Carbon\Carbon::parse($patient->dob)->format('M d, Y') }}</span>
                    </div>
                    <div class="grid grid-cols-3 gap-1">
                        <span class="text-slate-500 dark:text-slate-400 font-medium col-span-1">Contact:</span>
                        <span class="text-slate-800 dark:text-slate-100 font-semibold col-span-2">{{ $patient->contact_number ?: 'N/A' }}</span>
                    </div>
                    <div class="grid grid-cols-3 gap-1">
                        <span class="text-slate-500 dark:text-slate-400 font-medium col-span-1">Address:</span>
                        <span class="text-slate-800 dark:text-slate-100 font-semibold col-span-2 line-clamp-2">{{ $patient->address }}</span>
                    </div>
                </div>
            </div>

            <!-- Triage Vitals -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-0 overflow-hidden">
                <div class="bg-slate-800 dark:bg-slate-900 text-white p-3 border-b border-slate-700 flex justify-between items-center">
                    <h3 class="font-bold flex items-center gap-2">
                        <svg class="w-4 h-4 text-rose-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg>
                        Triage Vitals
                    </h3>
                    <span class="text-xs text-slate-300 dark:text-slate-400">{{ $consultation->created_at->diffForHumans() }}</span>
                </div>
                
                <div class="p-4 bg-slate-50 dark:bg-slate-900/50 space-y-4">
                    <!-- Chief Complaint / Symptoms -->
                    <div>
                        <h4 class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest mb-1.5 border-b border-slate-200 dark:border-slate-700 pb-1">Chief Complaint / Symptoms</h4>
                        <p class="text-slate-800 dark:text-slate-100 text-sm font-medium bg-white dark:bg-slate-800 p-2.5 rounded border border-slate-200 dark:border-slate-700 shadow-sm leading-relaxed whitespace-pre-line">{{ $consultation->preTriage?->symptoms ?: 'None recorded.' }}</p>
                    </div>

                    @if($consultation->preTriage?->past_medical_history && $consultation->preTriage?->past_medical_history !== 'N/A')
                        <div>
                            <h4 class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest mb-1.5">Past Medical History</h4>
                            <p class="text-slate-700 dark:text-rose-200 text-sm bg-rose-50 dark:bg-rose-950/40 p-2 rounded border border-rose-100 dark:border-rose-900/50">{{ $consultation->preTriage->past_medical_history }}</p>
                        </div>
                    @endif

                    @if($consultation->preTriage?->medicine_taken && $consultation->preTriage?->medicine_taken !== 'N/A')
                        <div>
                            <h4 class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest mb-1.5">Medicine Taken</h4>
                            <p class="text-slate-700 dark:text-blue-200 text-sm bg-blue-50 dark:bg-blue-950/40 p-2 rounded border border-blue-100 dark:border-blue-900/50">{{ $consultation->preTriage->medicine_taken }}</p>
                        </div>
                    @endif

                    @if($consultation->preTriage?->known_allergies && $consultation->preTriage?->known_allergies !== 'N/A' && $consultation->preTriage?->known_allergies !== 'None')
                        <div>
                            <h4 class="text-xs font-bold text-red-500 dark:text-red-400 uppercase tracking-widest mb-1.5 flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                Known Allergies
                            </h4>
                            <p class="text-red-700 dark:text-red-300 text-sm bg-red-50 dark:bg-red-950/50 p-2 rounded border border-red-200 dark:border-red-900/60 font-semibold">{{ $consultation->preTriage->known_allergies }}</p>
                        </div>
                    @endif
                </div>

                <!-- Vital Stats Grid — All 8 vitals -->
                <div class="grid grid-cols-2 gap-px bg-slate-200 dark:bg-slate-700">
                    <div class="bg-white dark:bg-gray-800 p-3">
                        <span class="block text-xs text-slate-500 dark:text-slate-400 font-semibold mb-0.5">Blood Pressure</span>
                        <span class="block text-lg font-bold text-slate-800 dark:text-white">{{ $consultation->preTriage?->blood_pressure ?: '--/--' }} <span class="text-xs font-normal text-slate-400 dark:text-slate-500">mmHg</span></span>
                    </div>
                    <div class="bg-white dark:bg-gray-800 p-3">
                        <span class="block text-xs text-slate-500 dark:text-slate-400 font-semibold mb-0.5">Temperature</span>
                        <span class="block text-lg font-bold {{ floatval($consultation->preTriage?->temperature ?? 0) > 37.5 ? 'text-red-500 dark:text-red-400' : 'text-slate-800 dark:text-white' }}">{{ $consultation->preTriage?->temperature ?: '--' }} <span class="text-xs font-normal text-slate-400 dark:text-slate-500">°C</span></span>
                    </div>
                    <div class="bg-white dark:bg-gray-800 p-3">
                        <span class="block text-xs text-slate-500 dark:text-slate-400 font-semibold mb-0.5">Heart Rate</span>
                        <span class="block text-lg font-bold text-slate-800 dark:text-white">{{ $consultation->preTriage?->heart_rate ?: '--' }} <span class="text-xs font-normal text-slate-400 dark:text-slate-500">bpm</span></span>
                    </div>
                    <div class="bg-white dark:bg-gray-800 p-3">
                        <span class="block text-xs text-slate-500 dark:text-slate-400 font-semibold mb-0.5">Respiratory Rate</span>
                        <span class="block text-lg font-bold text-slate-800 dark:text-white">{{ $consultation->preTriage?->respiratory_rate ?: '--' }} <span class="text-xs font-normal text-slate-400 dark:text-slate-500">cpm</span></span>
                    </div>
                    <div class="bg-white dark:bg-gray-800 p-3">
                        <span class="block text-xs text-slate-500 dark:text-slate-400 font-semibold mb-0.5">Pulse Rate</span>
                        <span class="block text-lg font-bold text-slate-800 dark:text-white">{{ $consultation->preTriage?->pulse_rate ?: '--' }} <span class="text-xs font-normal text-slate-400 dark:text-slate-500">bpm</span></span>
                    </div>
                    <div class="bg-white dark:bg-gray-800 p-3">
                        <span class="block text-xs text-slate-500 dark:text-slate-400 font-semibold mb-0.5">SpO2 / O₂ Sat</span>
                        <span class="block text-lg font-bold text-slate-800 dark:text-white">{{ $consultation->preTriage?->spo2 ?: ($consultation->preTriage?->oxygen_saturation ?: '--') }} <span class="text-xs font-normal text-slate-400 dark:text-slate-500">%</span></span>
                    </div>
                    <div class="bg-white dark:bg-gray-800 p-3">
                        <span class="block text-xs text-slate-500 dark:text-slate-400 font-semibold mb-0.5">Weight</span>
                        <span class="block text-lg font-bold text-slate-800 dark:text-white">{{ $consultation->preTriage?->weight ?: '--' }} <span class="text-xs font-normal text-slate-400 dark:text-slate-500">kg</span></span>
                    </div>
                    <div class="bg-white dark:bg-gray-800 p-3">
                        <span class="block text-xs text-slate-500 dark:text-slate-400 font-semibold mb-0.5">Height</span>
                        <span class="block text-lg font-bold text-slate-800 dark:text-white">{{ $consultation->preTriage?->height ?: '--' }} <span class="text-xs font-normal text-slate-400 dark:text-slate-500">cm</span></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column: Medical Folder & Tabs -->
        <div class="lg:col-span-2 flex flex-col h-full" x-data="{ activeTab: 'current' }">
            
            <!-- Folder Tab Bar -->
            <div class="flex overflow-x-auto gap-1 px-4 items-end border-b-2 border-teal-600 relative z-10 bottom-[-2px] scrollbar-hide">
                <!-- Current Case Tab -->
                <button type="button" @click="activeTab = 'current'" 
                        :class="activeTab === 'current' ? 'bg-white dark:bg-gray-800 text-teal-800 dark:text-teal-400 border-teal-600 border-2 border-b-white dark:border-b-gray-800 font-extrabold pb-3 pt-2 shadow-[0_-4px_6px_-2px_rgba(20,184,166,0.1)] z-20 relative' : 'bg-slate-50 dark:bg-gray-800/70 text-slate-500 dark:text-slate-400 border-slate-300 dark:border-slate-700 border border-b-0 hover:bg-slate-100 dark:hover:bg-gray-700 pb-2 pt-1.5 mt-1 font-semibold hover:text-slate-700 dark:hover:text-slate-200'"
                        class="px-5 rounded-t-xl transition-all whitespace-nowrap shrink-0 text-sm">
                    <span class="flex items-center gap-2">
                        <svg class="w-4 h-4" x-show="activeTab === 'current'" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                        Current Case
                    </span>
                </button>
                
                <!-- Past Case Tabs -->
                @forelse($pastConsultations as $past)
                    <button type="button" @click="activeTab = 'past_{{ $past->id }}'" 
                            :class="activeTab === 'past_{{ $past->id }}' ? 'bg-white dark:bg-gray-800 text-teal-800 dark:text-teal-400 border-teal-600 border-2 border-b-white dark:border-b-gray-800 font-bold pb-3 pt-2 shadow-[0_-4px_6px_-2px_rgba(20,184,166,0.1)] z-20 relative' : 'bg-slate-100 dark:bg-gray-800/70 text-slate-400 dark:text-slate-400 border-slate-200 dark:border-slate-700 border border-b-0 hover:bg-slate-50 dark:hover:bg-gray-700 pb-2 pt-1.5 mt-1 hover:text-slate-600 dark:hover:text-slate-200 font-medium'"
                            class="px-4 rounded-t-xl transition-all whitespace-nowrap shrink-0 text-sm">
                        Past: {{ \Carbon\Carbon::parse($past->consultation_date)->format('M d, Y') }}
                    </button>
                @empty
                    <span class="pb-2 pt-2 px-3 text-xs text-slate-400 dark:text-slate-500 italic">No past history.</span>
                @endforelse
            </div>

            <!-- Tab Content Container -->
            <div class="bg-white dark:bg-gray-800 rounded-b-xl rounded-tr-xl shadow-lg border-2 border-teal-600 grow flex flex-col z-0 relative">
                
                <!-- Tab 1: Current Case Form -->
                <div x-show="activeTab === 'current'" 
                     x-transition:enter="transition ease-out duration-200" 
                     x-transition:enter-start="opacity-0 translate-y-2" 
                     x-transition:enter-end="opacity-100 translate-y-0"
                     class="grow flex flex-col h-full"
                     x-data="{ showCancelWalkout: false }">
                    
                    <div class="p-5 border-b border-slate-100 dark:border-slate-700 bg-white dark:bg-gray-800 rounded-tr-lg">
                        <h3 class="text-lg font-extrabold text-slate-800 dark:text-white">Physician's Assessment</h3>
                        <p class="text-sm text-slate-500 dark:text-slate-400">Document your diagnosis, prescribe treatments, and add clinical notes.</p>
                    </div>

                    <div x-data="{ showAncillary: false }" class="p-6 pb-0">
                        <button type="button" @click="showAncillary = !showAncillary" class="w-full bg-slate-50 dark:bg-slate-850 dark:bg-slate-800/90 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-200 hover:bg-indigo-50 dark:hover:bg-slate-700 hover:text-indigo-700 dark:hover:text-indigo-300 hover:border-indigo-200 dark:hover:border-slate-600 font-bold py-3 px-4 rounded-lg flex justify-between items-center transition-colors">
                            <span class="flex items-center gap-2">
                                <svg class="w-5 h-5 text-indigo-500 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path></svg>
                                Issue Laboratory / Radiology Request
                            </span>
                            <svg class="w-5 h-5 transform transition-transform" :class="showAncillary ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>
                        
                        <div x-show="showAncillary" x-collapse class="mt-3">
                            <div class="bg-indigo-50 border border-indigo-200 p-4 rounded-lg shadow-inner">
                                <p class="text-xs text-indigo-700 mb-3 font-medium">Forward this patient to the Laboratory or Radiology queue. They will appear on the staff dashboard.</p>
                                
                                @if(!$isLabOnline || !$isRadOnline)
                                <div class="mb-3 bg-rose-50 border-l-4 border-rose-500 p-3 rounded-md">
                                    <div class="flex items-start">
                                        <div class="shrink-0">
                                            <svg class="h-5 w-5 text-rose-400" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                            </svg>
                                        </div>
                                        <div class="ml-3">
                                            <p class="text-sm text-rose-700 font-bold">
                                                Attention:
                                                @if(!$isLabOnline && !$isRadOnline) Both Laboratory and Radiology departments
                                                @elseif(!$isLabOnline) Laboratory department
                                                @else Radiology department
                                                @endif
                                                are currently offline (Unavailable).
                                            </p>
                                            <p class="text-xs text-rose-600 mt-1">Requests will still be queued, but will not be processed until staff logs in.</p>
                                        </div>
                                    </div>
                                </div>
                                @endif

                                @php
                                    $activeTestNames = $consultation->ancillaryRequests
                                        ? $consultation->ancillaryRequests->whereIn('status', ['Pending', 'Specimen Collected', 'In Progress'])->pluck('test_name')->toArray()
                                        : [];
                                    $isCbcActive = in_array('Complete Blood Count (CBC)', $activeTestNames);
                                    $isUrinalysisActive = in_array('Urinalysis', $activeTestNames);
                                    $isChestXrayActive = in_array('Chest X-Ray', $activeTestNames);
                                @endphp

                                <form action="{{ route('doctor.ancillary.store', $consultation->id) }}" method="POST" 
                                      class="space-y-3" 
                                      x-data="{ requestType: 'Laboratory', testName: '' }"
                                      @change="if($event.target.tagName==='INPUT'){
                                          if($event.target.name==='type'){requestType=$event.target.value;testName=''}
                                          else if($event.target.name==='lab_test_name'||$event.target.name==='rad_test_name'){testName=$event.target.value}
                                      }">
                                    @csrf
                                    <input type="hidden" name="test_name" :value="testName">

                                    <div class="flex flex-col md:flex-row gap-3 items-end">
                                        <div class="w-full md:w-auto md:min-w-[180px]">
                                            <x-select name="type" size="sm"
                                                :options="['Laboratory' => 'Laboratory Request', 'Radiology' => 'Radiology Request']"
                                                value="Laboratory" />
                                        </div>

                                        <div class="flex-1" x-show="requestType === 'Laboratory'">
                                            <x-select name="lab_test_name" size="sm"
                                                placeholder="-- Select Laboratory Test --"
                                                :options="array_filter([
                                                    !$isCbcActive ? ['value' => 'Complete Blood Count (CBC)', 'label' => 'CBC (Complete Blood Count)'] : null,
                                                    !$isUrinalysisActive ? ['value' => 'Urinalysis', 'label' => 'Urinalysis'] : null,
                                                ])" />
                                        </div>

                                        <div class="flex-1" x-show="requestType === 'Radiology'" x-cloak>
                                            <x-select name="rad_test_name" size="sm"
                                                placeholder="-- Select Radiology Test --"
                                                :options="array_filter([
                                                    !$isChestXrayActive ? ['value' => 'Chest X-Ray', 'label' => 'Chest X-Ray'] : null,
                                                ])" />
                                        </div>

                                        <button type="submit" class="bg-indigo-600 text-white px-5 py-2 flex items-center justify-center gap-2 rounded-xl font-bold text-sm hover:bg-indigo-700 shadow-sm transition shrink-0">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                                            Send Request
                                        </button>
                                    </div>

                                    <div id="consultation-ancillary-badges" data-dynamic-block="true">
                                        @if($isCbcActive || $isUrinalysisActive || $isChestXrayActive)
                                            <div class="flex flex-wrap gap-1.5 mt-1">
                                                @if($isCbcActive)
                                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-purple-50 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400 border border-purple-200 dark:border-purple-800/50">
                                                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                                        CBC — In Progress
                                                    </span>
                                                @endif
                                                @if($isUrinalysisActive)
                                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-purple-50 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400 border border-purple-200 dark:border-purple-800/50">
                                                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                                        Urinalysis — In Progress
                                                    </span>
                                                @endif
                                                @if($isChestXrayActive)
                                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 border border-indigo-200 dark:border-indigo-800/50">
                                                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                                        Chest X-Ray — In Progress
                                                    </span>
                                                @endif
                                            </div>
                                        @endif
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!-- Display Ancillary Results (Auto-Updating) -->
                        <div id="consultation-ancillary-section" data-dynamic-block="true">
                        @if($consultation->ancillaryRequests && $consultation->ancillaryRequests->count() > 0)
                            @php
                                $cancelledRejectedCount = $consultation->ancillaryRequests->whereIn('status', ['Cancelled', 'Rejected'])->count();
                            @endphp
                            <div class="mt-4 space-y-3 max-w-full overflow-hidden" x-data="{ showDismissed: false }">
                                <div class="flex items-center justify-between border-b border-slate-200 dark:border-slate-700 pb-2">
                                    <h4 class="text-sm font-bold text-slate-700 dark:text-slate-300 uppercase tracking-widest">Laboratory / Radiology Results</h4>
                                    @if($cancelledRejectedCount > 0)
                                        <button type="button" @click="showDismissed = !showDismissed" 
                                                class="inline-flex items-center gap-1.5 text-[11px] font-bold px-2.5 py-1 rounded-lg border transition-colors"
                                                :class="showDismissed 
                                                    ? 'bg-rose-50 dark:bg-rose-950/40 text-rose-700 dark:text-rose-300 border-rose-200 dark:border-rose-800' 
                                                    : 'bg-slate-50 dark:bg-slate-800 text-slate-500 dark:text-slate-400 border-slate-200 dark:border-slate-700 hover:bg-slate-100 dark:hover:bg-slate-700'">
                                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                            <span x-text="showDismissed ? 'Hide Cancelled/Rejected' : 'Show Cancelled/Rejected ({{ $cancelledRejectedCount }})'" ></span>
                                        </button>
                                    @endif
                                </div>
                                <div class="space-y-3 max-h-[460px] overflow-y-auto pr-1.5 scrollbar-thin">
                                @foreach($consultation->ancillaryRequests as $ancillary)
                                    <div class="bg-white dark:bg-slate-800 border rounded-xl p-4 shadow-sm {{ $ancillary->status === 'Done' ? 'border-green-200 dark:border-green-800' : ($ancillary->status === 'Rejected' ? 'border-rose-300 dark:border-rose-800 ring-2 ring-rose-200/50' : ($ancillary->status === 'Cancelled' ? 'border-slate-200 dark:border-slate-700' : 'border-amber-200 dark:border-amber-800')) }}"
                                         x-data="{ showPrev: false, showCancel: false }"
                                         @if(in_array($ancillary->status, ['Cancelled', 'Rejected'])) x-show="showDismissed" x-transition.opacity.duration.200ms @endif>
                                        <div class="flex items-start justify-between mb-2">
                                            <div>
                                                <div class="flex items-center gap-1.5 flex-wrap">
                                                    <span class="inline-block px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider {{ $ancillary->type === 'Laboratory' ? 'bg-purple-100 text-purple-700 dark:bg-purple-900/40 dark:text-purple-400' : 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900/40 dark:text-indigo-400' }}">
                                                        {{ $ancillary->type }}
                                                    </span>
                                                    @if($ancillary->is_repeat || $ancillary->parent_id)
                                                        <span class="inline-block px-2 py-0.5 rounded text-[10px] font-extrabold uppercase bg-purple-100 text-purple-800 dark:bg-purple-900/50 dark:text-purple-300">
                                                            Repeat Test
                                                        </span>
                                                    @endif
                                                </div>
                                                <h5 class="font-bold text-slate-800 dark:text-white mt-1">{{ $ancillary->test_name }}</h5>
                                            </div>
                                            <div>
                                                @if($ancillary->status === 'Done')
                                                    @if($ancillary->amended_at)
                                                        <span class="inline-flex items-center gap-1 text-xs font-bold text-amber-700 bg-amber-50 dark:bg-amber-900/30 dark:text-amber-300 px-2 py-1 rounded-md border border-amber-300 dark:border-amber-800">
                                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                                            Completed (Amended)
                                                        </span>
                                                    @else
                                                        <span class="inline-flex items-center gap-1 text-xs font-bold text-green-700 bg-green-50 dark:bg-green-900/30 dark:text-green-400 px-2 py-1 rounded-md border border-green-200 dark:border-green-800">
                                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                                            Completed
                                                        </span>
                                                    @endif
                                                @elseif($ancillary->status === 'Specimen Collected')
                                                    <span class="inline-flex items-center gap-1 text-xs font-bold text-sky-700 bg-sky-50 dark:bg-sky-900/30 dark:text-sky-300 px-2 py-1 rounded-md border border-sky-300 dark:border-sky-800">
                                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                                        Specimen Collected
                                                    </span>
                                                @elseif($ancillary->status === 'In Progress')
                                                    <span class="inline-flex items-center gap-1 text-xs font-bold text-indigo-700 bg-indigo-50 dark:bg-indigo-900/30 dark:text-indigo-300 px-2 py-1 rounded-md border border-indigo-300 dark:border-indigo-800 animate-pulse">
                                                        <svg class="w-3.5 h-3.5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                                        Processing
                                                    </span>
                                                @elseif($ancillary->status === 'Rejected')
                                                    <span class="inline-flex items-center gap-1 text-xs font-bold text-rose-700 bg-rose-50 dark:bg-rose-900/30 dark:text-rose-400 px-2 py-1 rounded-md border border-rose-300 dark:border-rose-800">
                                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                                        Sample Rejected
                                                    </span>
                                                @elseif($ancillary->status === 'Cancelled')
                                                    <span class="inline-flex items-center gap-1 text-xs font-bold text-slate-600 bg-slate-100 dark:bg-slate-700 dark:text-slate-300 px-2 py-1 rounded-md border border-slate-300 dark:border-slate-600">
                                                        Cancelled
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center gap-1 text-xs font-bold text-amber-700 bg-amber-50 dark:bg-amber-900/30 dark:text-amber-400 px-2 py-1 rounded-md border border-amber-200 dark:border-amber-800">
                                                        <svg class="w-3.5 h-3.5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                                        Pending Sample
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                        
                                        @if($ancillary->remarks)
                                            <p class="text-xs text-slate-500 dark:text-slate-400 mb-2 italic">Remarks/Instructions: {{ $ancillary->remarks }}</p>
                                        @endif

                                        {{-- REJECTION ALERT & 1-CLICK RE-ORDER BUTTON --}}
                                        @if($ancillary->status === 'Rejected')
                                            <div class="mt-3 bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-900 rounded-xl p-3.5 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                                                <div>
                                                    <p class="text-xs font-extrabold text-rose-800 dark:text-rose-300 flex items-center gap-1.5">
                                                        <svg class="w-4 h-4 text-rose-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                                        Sample Compromised: {{ ucwords(str_replace('_', ' ', $ancillary->rejection_reason ?? 'Rejected')) }}
                                                    </p>
                                                    @if($ancillary->rejection_notes)
                                                        <p class="text-xs text-rose-700 dark:text-rose-400 mt-1 italic">"{{ $ancillary->rejection_notes }}"</p>
                                                    @endif
                                                    <p class="text-[10px] text-rose-500 mt-0.5">Rejected by {{ $ancillary->rejector?->formatted_name ?? 'Laboratory Staff' }} &bull; {{ $ancillary->rejected_at?->format('M d, h:i A') }}</p>
                                                </div>
                                                <form action="{{ route('doctor.ancillary.repeat', $ancillary->id) }}" method="POST" class="shrink-0">
                                                    @csrf
                                                    <button type="submit" class="w-full sm:w-auto inline-flex items-center justify-center gap-1.5 bg-rose-600 hover:bg-rose-700 text-white font-extrabold px-3 py-2 rounded-lg text-xs shadow-xs transition">
                                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                                        Re-order / Repeat Test
                                                    </button>
                                                </form>
                                            </div>
                                        @endif

                                        {{-- AMENDMENT AUDIT ALERT --}}
                                        @if($ancillary->status === 'Done' && $ancillary->amended_at)
                                            <div class="mt-3 bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-800 rounded-xl p-3 text-xs">
                                                <p class="font-bold text-amber-900 dark:text-amber-200 flex items-center gap-1.5">
                                                    <svg class="w-4 h-4 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                                    Results officially amended on {{ $ancillary->amended_at->format('M d, Y h:i A') }}
                                                </p>
                                                <p class="text-amber-800 dark:text-amber-300 mt-0.5"><strong>Reason:</strong> {{ $ancillary->amendment_reason }}</p>
                                                <p class="text-[10px] text-amber-600 dark:text-amber-400 mt-0.5">Amended by {{ $ancillary->amender?->formatted_name ?? 'Technician' }}</p>
                                                @if($ancillary->previous_result_data)
                                                    <button type="button" @click="showPrev = !showPrev" class="mt-1.5 text-[11px] font-bold text-amber-800 dark:text-amber-300 underline">
                                                        <span x-text="showPrev ? 'Hide Previous Values' : 'View Prior Unamended Values'"></span>
                                                    </button>
                                                    <div x-show="showPrev" class="mt-2 p-2 bg-white/70 dark:bg-slate-900/70 rounded-lg border border-amber-200 dark:border-amber-900 space-y-1">
                                                        @foreach($ancillary->previous_result_data as $pk => $pv)
                                                            <div class="flex justify-between text-[11px]">
                                                                <span class="text-slate-500 font-bold uppercase">{{ str_replace('_', ' ', $pk) }}:</span>
                                                                <span class="text-slate-700 dark:text-slate-300 font-mono">{{ $pv }}</span>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                @endif
                                            </div>
                                        @endif

                                        <div class="mt-3 bg-slate-50 dark:bg-slate-900/50 rounded-md p-3 border border-slate-100 dark:border-slate-700">
                                            <p class="text-[11px] font-bold text-slate-400 uppercase tracking-widest mb-1">Results / Findings</p>
                                            @if($ancillary->status === 'Done' && $ancillary->result_data)
                                                <div class="grid grid-cols-2 gap-2 mt-2">
                                                    @foreach($ancillary->result_data as $key => $value)
                                                        @if($value)
                                                        <div class="bg-white dark:bg-slate-800 p-2 rounded-lg border border-slate-200 dark:border-slate-600">
                                                            <span class="block text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">{{ str_replace('_', ' ', $key) }}</span>
                                                            <span class="text-sm font-extrabold text-slate-900 dark:text-white">{{ $value }}</span>
                                                        </div>
                                                        @endif
                                                    @endforeach
                                                </div>
                                                
                                                @if($ancillary->result_file_path)
                                                    <div class="mt-3">
                                                        <a href="{{ route('ancillary.file', $ancillary) }}" target="_blank" class="inline-flex items-center gap-2 text-sm font-bold text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 bg-indigo-50 dark:bg-indigo-900/30 px-3 py-1.5 rounded-lg border border-indigo-200 dark:border-indigo-800/50 transition">
                                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" /></svg>
                                                            View Attachment
                                                        </a>
                                                    </div>
                                                @endif

                                                @if($ancillary->technician)
                                                    <p class="text-[10px] text-slate-400 dark:text-slate-500 mt-3 font-medium">Processed by {{ $ancillary->technician->formatted_name }} &bull; {{ $ancillary->completed_at->format('M d, Y h:i A') }}</p>
                                                @endif
                                            @elseif($ancillary->status === 'Done')
                                                <p class="text-sm text-slate-800 dark:text-slate-200 font-medium">Results submitted (no structured data).</p>
                                            @elseif($ancillary->status === 'Rejected')
                                                <p class="text-xs text-rose-600 dark:text-rose-400 font-medium">No results produced due to sample rejection.</p>
                                            @elseif($ancillary->status === 'Cancelled')
                                                <p class="text-xs text-slate-500 font-medium italic">Request cancelled: "{{ $ancillary->cancellation_reason }}"</p>
                                            @else
                                                <div class="flex items-center justify-between">
                                                    <p class="text-sm text-slate-500 dark:text-slate-400 italic">Waiting for laboratory/radiology to submit results...</p>
                                                    {{-- Doctor Cancel Option --}}
                                                    <button type="button" @click="showCancel = true" class="text-xs font-bold text-slate-500 hover:text-rose-600 underline">
                                                        Cancel Request
                                                    </button>
                                                </div>

                                                {{-- Inline Cancel Modal for Doctor --}}
                                                <div x-show="showCancel" style="display:none;" class="fixed inset-0 z-50 flex items-center justify-center p-4">
                                                    <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm" @click="showCancel = false"></div>
                                                    <div class="relative bg-white dark:bg-slate-800 rounded-2xl shadow-xl max-w-sm w-full p-5 border border-slate-200 dark:border-slate-700 z-10 text-left">
                                                        <h4 class="font-extrabold text-slate-900 dark:text-white mb-2">Cancel {{ $ancillary->test_name }}?</h4>
                                                        <form action="{{ route('doctor.ancillary.cancel', $ancillary->id) }}" method="POST">
                                                            @csrf
                                                            <label class="block text-xs font-bold text-slate-600 dark:text-slate-400 mb-1">Reason for Cancellation <span class="text-rose-500">*</span></label>
                                                            <textarea name="cancellation_reason" required rows="2" placeholder="e.g. Ordered in error, patient declined..." class="w-full bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-600 rounded-lg p-2 text-xs text-slate-900 dark:text-white mb-3"></textarea>
                                                            <div class="flex justify-end gap-2">
                                                                <button type="button" @click="showCancel = false" class="px-3 py-1.5 bg-slate-100 rounded-lg text-xs font-bold text-slate-600">Back</button>
                                                                <button type="submit" class="px-3 py-1.5 bg-rose-600 text-white rounded-lg text-xs font-bold">Confirm Cancel</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                    </div>

                    <form action="{{ route('doctor.consultation.complete', $consultation->id) }}" method="POST" class="grow flex flex-col p-6 space-y-6" x-data="{ showConfirm: false }" @submit.prevent="showConfirm = true">
                        @csrf
                        
                        <div>
                            <label for="diagnosis" class="block text-sm font-bold text-slate-700 dark:text-slate-200 mb-2">Primary Diagnosis <span class="text-rose-500">*</span></label>
                            <textarea id="diagnosis" name="diagnosis" rows="3" required
                                class="w-full rounded-lg border-2 border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 text-slate-800 dark:text-white shadow-sm focus:border-teal-500 focus:ring-teal-500 text-sm p-3 transition" 
                                placeholder="Enter conclusive medical diagnosis...">{{ old('diagnosis') }}</textarea>
                        </div>

                        <!-- Dynamic Prescription Builder -->
                        <div x-data="prescriptionBuilder('teal')" class="mb-4 relative">
                            <div class="flex justify-between items-center mb-3">
                                <label class="block text-sm font-bold text-slate-700 dark:text-slate-200">Prescription / Treatment Plan <span class="text-slate-400 font-normal ml-1">(Optional)</span></label>
                                <span x-show="prescriptions.length > 0" x-cloak class="text-xs font-bold bg-teal-100 dark:bg-teal-900/60 text-teal-700 dark:text-teal-300 px-2.5 py-1 rounded-full" x-text="prescriptions.length + ' item(s)'"></span>
                            </div>
                            
                            @if(!$isPharmacyOnline)
                            <div class="mb-3 bg-amber-50 border-l-4 border-amber-500 p-3 rounded-md">
                                <div class="flex items-start">
                                    <div class="shrink-0">
                                        <svg class="h-5 w-5 text-amber-400" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm text-amber-700 font-bold">
                                            Attention: Pharmacy department is currently offline (Unavailable).
                                        </p>
                                        <p class="text-xs text-amber-600 mt-1">Prescriptions will be queued, but will not be dispensed until a Pharmacist logs in.</p>
                                    </div>
                                </div>
                            </div>
                            @endif

                            <!-- Visual List of added medicines -->
                            <div class="space-y-2 mb-4" x-show="prescriptions.length > 0" x-cloak>
                                <template x-for="(item, index) in prescriptions" :key="index">
                                    <div class="flex items-start gap-3 p-3 rounded-xl border shadow-sm transition-all"
                                         :class="item.isOtc ? 'bg-slate-50 dark:bg-slate-800/80 border-slate-200 dark:border-slate-700' : 'bg-teal-50 dark:bg-teal-950/40 border-teal-200 dark:border-teal-800'">
                                        <div class="pt-0.5 shrink-0">
                                            <span class="inline-flex items-center justify-center w-7 h-7 rounded-full text-xs font-black"
                                                  :class="item.isOtc ? 'bg-slate-200 dark:bg-slate-700 text-slate-600 dark:text-slate-300' : 'bg-teal-200 dark:bg-teal-900 text-teal-800 dark:text-teal-200'"
                                                  x-text="index + 1"></span>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center gap-2 flex-wrap">
                                                <p class="font-bold text-sm text-slate-900 dark:text-white" x-text="item.medicine"></p>
                                                <span x-show="item.isOtc" class="text-[9px] font-bold uppercase tracking-wider bg-slate-200 dark:bg-slate-700 text-slate-600 dark:text-slate-300 px-1.5 py-0.5 rounded">OTC / External</span>
                                                <span x-show="!item.isOtc" class="text-[9px] font-bold uppercase tracking-wider bg-teal-200 dark:bg-teal-900/80 text-teal-700 dark:text-teal-300 px-1.5 py-0.5 rounded">RHU Inventory</span>
                                            </div>
                                            <div class="flex items-center gap-3 mt-1 text-xs text-slate-600 dark:text-slate-400">
                                                <span class="flex items-center gap-1">
                                                    <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path></svg>
                                                    <span x-text="item.amount"></span>
                                                </span>
                                                <span class="flex items-center gap-1">
                                                    <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                    <span x-text="item.instruction"></span>
                                                </span>
                                                <span x-show="item.quantity" class="flex items-center gap-1">
                                                    <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                                                    Qty: <span x-text="item.quantity"></span>
                                                </span>
                                            </div>
                                        </div>
                                        <button type="button" @click="removePrescription(index)" class="text-rose-400 hover:text-rose-600 p-1.5 hover:bg-rose-50 dark:hover:bg-rose-950/40 rounded-lg transition shrink-0" title="Remove">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                        </button>
                                    </div>
                                </template>
                            </div>

                            <!-- Builder Form -->
                            <div class="bg-slate-50 dark:bg-slate-900/60 border border-slate-200 dark:border-slate-700 rounded-xl p-5 relative">
                                <!-- OTC Toggle -->
                                <div class="flex items-center justify-between mb-4 pb-3 border-b border-slate-200 dark:border-slate-700">
                                    <div class="flex items-center gap-3">
                                        <button type="button" @click="isOtcMode = false" 
                                                class="px-3 py-1.5 rounded-lg text-xs font-bold transition-all"
                                                :class="!isOtcMode ? 'bg-teal-600 text-white shadow-sm' : 'bg-white dark:bg-slate-800 text-slate-500 dark:text-slate-400 border border-slate-200 dark:border-slate-700 hover:border-teal-300'">
                                            <span class="flex items-center gap-1.5">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                                                From RHU Inventory
                                            </span>
                                        </button>
                                        <button type="button" @click="isOtcMode = true" 
                                                class="px-3 py-1.5 rounded-lg text-xs font-bold transition-all"
                                                :class="isOtcMode ? 'bg-slate-700 dark:bg-slate-600 text-white shadow-sm' : 'bg-white dark:bg-slate-800 text-slate-500 dark:text-slate-400 border border-slate-200 dark:border-slate-700 hover:border-slate-400'">
                                            <span class="flex items-center gap-1.5">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                Custom / OTC Medicine
                                            </span>
                                        </button>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                    <!-- Medicine Name Field -->
                                    <div class="relative">
                                        <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1.5">
                                            <span x-text="isOtcMode ? 'Medicine Name (type manually)' : 'Search RHU Inventory'"></span>
                                            <span class="text-rose-400">*</span>
                                        </label>
                                        <div class="relative">
                                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path x-show="!isOtcMode" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                                    <path x-show="isOtcMode" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                                                </svg>
                                            </div>
                                            <input type="text" x-model="searchQuery" 
                                                   @input.debounce.300ms="!isOtcMode && searchMedicine()" 
                                                   @keydown.escape="showSuggestions = false" 
                                                   @click.away="showSuggestions = false"
                                                   x-ref="medicineInput"
                                                   class="w-full text-sm rounded-lg border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:border-teal-500 focus:ring-teal-500 pl-9 py-2.5"
                                                   :placeholder="isOtcMode ? 'e.g. Biogesic, Neozep, Dolfenal...' : 'Type to search (e.g. Paracetamol)...'">
                                        </div>
                                        
                                        <!-- Suggestions Dropdown (only in inventory mode) -->
                                        <div x-show="!isOtcMode && showSuggestions && suggestions.length > 0" 
                                             x-transition:enter="transition ease-out duration-150"
                                             x-transition:enter-start="opacity-0 -translate-y-1"
                                             x-transition:enter-end="opacity-100 translate-y-0"
                                             class="absolute z-50 w-full bg-white dark:bg-slate-800 mt-1 border border-slate-200 dark:border-slate-700 rounded-xl shadow-xl max-h-56 overflow-y-auto" x-cloak>
                                            <template x-for="med in suggestions" :key="med.id">
                                                <div @click="selectMedicine(med)" class="px-4 py-3 hover:bg-teal-50 dark:hover:bg-teal-950/40 cursor-pointer border-b border-slate-100 dark:border-slate-700 last:border-0 transition group">
                                                    <div class="flex justify-between items-start">
                                                        <div>
                                                            <p class="font-bold text-sm text-slate-800 dark:text-white group-hover:text-teal-800 dark:group-hover:text-teal-300" x-text="med.name"></p>
                                                            <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5" x-text="(med.generic_name || 'No generic name') + (med.form ? ' · ' + med.form : '')"></p>
                                                        </div>
                                                        <div class="shrink-0 ml-3">
                                                            <span class="text-[10px] font-bold px-2 py-1 rounded-full"
                                                                  :class="med.stock > 10 ? 'bg-emerald-100 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300' : (med.stock > 0 ? 'bg-amber-100 dark:bg-amber-950/60 text-amber-700 dark:text-amber-300' : 'bg-rose-100 dark:bg-rose-950/60 text-rose-700 dark:text-rose-300')"
                                                                  x-text="med.stock > 0 ? med.stock + ' in stock' : 'Out of stock'"></span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </template>
                                            <!-- No results hint -->
                                            <div x-show="suggestions.length === 0 && searchQuery.length > 1" class="px-4 py-3 text-center text-slate-400 dark:text-slate-500 text-sm">
                                                No medicine found. Try <button type="button" @click="isOtcMode = true" class="text-teal-600 dark:text-teal-400 font-bold underline">Custom / OTC</button> mode.
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Dosage / Amount Field -->
                                    <div>
                                        <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1.5">Dosage / Amount</label>
                                        <input type="text" x-model="currentAmount" x-ref="amountInput"
                                               @keydown.enter.prevent="$refs.instInput.focus()" 
                                               placeholder="e.g. 500mg, 10 tablets, 1 bottle"
                                               class="w-full text-sm rounded-lg border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:border-teal-500 focus:ring-teal-500 py-2.5">
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-4">
                                    <!-- Instructions Field -->
                                    <div class="md:col-span-2">
                                        <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1.5">Instructions / Frequency</label>
                                        <input type="text" x-ref="instInput" x-model="currentInstruction" 
                                               @keydown.enter.prevent="addPrescription()"
                                               placeholder="e.g. 3x a day after meals for 5 days"
                                               class="w-full text-sm rounded-lg border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:border-teal-500 focus:ring-teal-500 py-2.5">
                                    </div>

                                    <!-- Quantity Field -->
                                    <div>
                                        <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1.5">Quantity to Dispense</label>
                                        <input type="number" x-model="currentQuantity" min="1"
                                               @keydown.enter.prevent="addPrescription()"
                                               placeholder="e.g. 30"
                                               class="w-full text-sm rounded-lg border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:border-teal-500 focus:ring-teal-500 py-2.5">
                                    </div>

                                    <!-- Duration Field -->
                                    <div>
                                        <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1.5">Duration (days)</label>
                                        <input type="number" x-model="currentDuration" min="1"
                                               @keydown.enter.prevent="addPrescription()"
                                               placeholder="e.g. 7"
                                               class="w-full text-sm rounded-lg border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:border-teal-500 focus:ring-teal-500 py-2.5">
                                    </div>
                                </div>

                                <!-- Quick Clinical Presets -->
                                <div class="mb-4 pb-3 border-b border-slate-200 dark:border-slate-700 space-y-3">
                                    @php
                                        $isPediaRole = (auth()->user()->role === 'pedia_doctor') || ($consultation->doctor_type === 'pediatrician') || ($patient->classification === 'Pediatric');
                                    @endphp

                                    @if($isPediaRole)
                                        <div>
                                            <span class="block text-[10px] font-bold text-amber-700 uppercase tracking-wider mb-1.5 flex items-center gap-1">
                                                🍼 Pediatric Formulations (Liquid / mL & Drops):
                                            </span>
                                            <div class="flex flex-wrap gap-1.5">
                                                <button type="button" @click="applyPreset({ name: 'Paracetamol 250mg/5mL Syrup', amount: '250mg/5mL', instruction: '5 mL every 4-6 hours as needed for fever', quantity: 1, isOtc: true })" 
                                                        class="text-xs font-bold bg-amber-50 border border-amber-200 text-amber-800 hover:bg-amber-100 px-2.5 py-1 rounded-lg transition flex items-center gap-1">
                                                    <span>🍼 Paracetamol 250mg/5mL</span>
                                                </button>
                                                <button type="button" @click="applyPreset({ name: 'Paracetamol 120mg/5mL Syrup', amount: '120mg/5mL', instruction: '5 mL every 4-6 hours as needed for fever', quantity: 1, isOtc: true })" 
                                                        class="text-xs font-bold bg-amber-50 border border-amber-200 text-amber-800 hover:bg-amber-100 px-2.5 py-1 rounded-lg transition flex items-center gap-1">
                                                    <span>🍼 Paracetamol 120mg/5mL</span>
                                                </button>
                                                <button type="button" @click="applyPreset({ name: 'Paracetamol 100mg/mL Drops', amount: '100mg/mL', instruction: '1 mL every 4-6 hours as needed for infant fever', quantity: 1, isOtc: true })" 
                                                        class="text-xs font-bold bg-rose-50 border border-rose-200 text-rose-800 hover:bg-rose-100 px-2.5 py-1 rounded-lg transition flex items-center gap-1">
                                                    <span>💧 Paracetamol Drops 100mg/mL</span>
                                                </button>
                                                <button type="button" @click="applyPreset({ name: 'Ibuprofen 100mg/5mL Syrup', amount: '100mg/5mL', instruction: '5 mL 3x daily after meals for 5 days', quantity: 1, isOtc: true })" 
                                                        class="text-xs font-bold bg-indigo-50 border border-indigo-200 text-indigo-800 hover:bg-indigo-100 px-2.5 py-1 rounded-lg transition flex items-center gap-1">
                                                    <span>🍼 Ibuprofen 100mg/5mL</span>
                                                </button>
                                                <button type="button" @click="applyPreset({ name: 'Amoxicillin 250mg/5mL Oral Suspension', amount: '250mg/5mL', instruction: '5 mL every 8 hours for 7 days', quantity: 1, isOtc: true })" 
                                                        class="text-xs font-bold bg-emerald-50 border border-emerald-200 text-emerald-800 hover:bg-emerald-100 px-2.5 py-1 rounded-lg transition flex items-center gap-1">
                                                    <span>🍼 Amoxicillin Susp 250mg/5mL</span>
                                                </button>
                                                <button type="button" @click="applyPreset({ name: 'Cetirizine 2.5mg/5mL Syrup', amount: '2.5mg/5mL', instruction: '5 mL once daily at bedtime', quantity: 1, isOtc: true })" 
                                                        class="text-xs font-bold bg-teal-50 border border-teal-200 text-teal-800 hover:bg-teal-100 px-2.5 py-1 rounded-lg transition flex items-center gap-1">
                                                    <span>🍼 Cetirizine Syrup 2.5mg/5mL</span>
                                                </button>
                                                <button type="button" @click="applyPreset({ name: 'Zinc Sulfate 55mg/5mL Syrup', amount: '55mg/5mL', instruction: '5 mL once daily for 14 days', quantity: 1, isOtc: true })" 
                                                        class="text-xs font-bold bg-sky-50 border border-sky-200 text-sky-800 hover:bg-sky-100 px-2.5 py-1 rounded-lg transition flex items-center gap-1">
                                                    <span>🍼 Zinc Sulfate Syrup</span>
                                                </button>
                                                <button type="button" @click="applyPreset({ name: 'Oral Rehydration Salts (ORS)', amount: '1 sachet', instruction: 'Dissolve 1 sachet in 1 liter clean water, drink after each loose stool', quantity: 3, isOtc: true })" 
                                                        class="text-xs font-bold bg-cyan-50 border border-cyan-200 text-cyan-800 hover:bg-cyan-100 px-2.5 py-1 rounded-lg transition flex items-center gap-1">
                                                    <span>💧 ORS Sachet</span>
                                                </button>
                                            </div>
                                        </div>
                                    @else
                                        <div class="space-y-2">
                                            <span class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1 flex items-center gap-1">
                                                ⚡ Adult Clinical Presets:
                                            </span>
                                            <div class="flex flex-wrap gap-1.5">
                                                <!-- Analgesic / Anti-inflammatory -->
                                                <button type="button" @click="applyPreset({ name: 'Paracetamol 500mg (Tablet)', amount: '500mg', instruction: '1 tab every 4-6 hours as needed for fever/pain', quantity: 10, isOtc: true })" 
                                                        class="text-xs font-bold bg-teal-50 border border-teal-200 text-teal-800 hover:bg-teal-100 px-2.5 py-1 rounded-lg transition flex items-center gap-1">
                                                    <span>💊 Paracetamol 500mg</span>
                                                </button>
                                                <button type="button" @click="applyPreset({ name: 'Ibuprofen 400mg (Tablet)', amount: '400mg', instruction: '1 tab 3x daily after meals for 5 days', quantity: 15, isOtc: true })" 
                                                        class="text-xs font-bold bg-blue-50 border border-blue-200 text-blue-800 hover:bg-blue-100 px-2.5 py-1 rounded-lg transition flex items-center gap-1">
                                                    <span>💊 Ibuprofen 400mg</span>
                                                </button>
                                                <button type="button" @click="applyPreset({ name: 'Mefenamic Acid 500mg (Capsule)', amount: '500mg', instruction: '1 cap 3x daily after meals for pain', quantity: 9, isOtc: true })" 
                                                        class="text-xs font-bold bg-purple-50 border border-purple-200 text-purple-800 hover:bg-purple-100 px-2.5 py-1 rounded-lg transition flex items-center gap-1">
                                                    <span>💊 Mefenamic Acid 500mg</span>
                                                </button>

                                                <!-- Antibiotics -->
                                                <button type="button" @click="applyPreset({ name: 'Amoxicillin 500mg (Capsule)', amount: '500mg', instruction: '1 cap 3x daily (every 8 hrs) for 7 days', quantity: 21, isOtc: true })" 
                                                        class="text-xs font-bold bg-emerald-50 border border-emerald-200 text-emerald-800 hover:bg-emerald-100 px-2.5 py-1 rounded-lg transition flex items-center gap-1">
                                                    <span>💊 Amoxicillin 500mg</span>
                                                </button>
                                                <button type="button" @click="applyPreset({ name: 'Co-Amoxiclav 625mg (Tablet)', amount: '625mg', instruction: '1 tab 2x daily (every 12 hrs) with meals for 7 days', quantity: 14, isOtc: true })" 
                                                        class="text-xs font-bold bg-emerald-50 border border-emerald-200 text-emerald-800 hover:bg-emerald-100 px-2.5 py-1 rounded-lg transition flex items-center gap-1">
                                                    <span>💊 Co-Amoxiclav 625mg</span>
                                                </button>
                                                <button type="button" @click="applyPreset({ name: 'Cefalexin 500mg (Capsule)', amount: '500mg', instruction: '1 cap 3x daily for 7 days', quantity: 21, isOtc: true })" 
                                                        class="text-xs font-bold bg-emerald-50 border border-emerald-200 text-emerald-800 hover:bg-emerald-100 px-2.5 py-1 rounded-lg transition flex items-center gap-1">
                                                    <span>💊 Cefalexin 500mg</span>
                                                </button>

                                                <!-- Maintenance / Cardiovascular / Metabolic -->
                                                <button type="button" @click="applyPreset({ name: 'Amlodipine 5mg (Tablet)', amount: '5mg', instruction: '1 tab once daily in the morning', quantity: 30, isOtc: true })" 
                                                        class="text-xs font-bold bg-rose-50 border border-rose-200 text-rose-800 hover:bg-rose-100 px-2.5 py-1 rounded-lg transition flex items-center gap-1">
                                                    <span>❤️ Amlodipine 5mg</span>
                                                </button>
                                                <button type="button" @click="applyPreset({ name: 'Losartan 50mg (Tablet)', amount: '50mg', instruction: '1 tab once daily in the morning', quantity: 30, isOtc: true })" 
                                                        class="text-xs font-bold bg-rose-50 border border-rose-200 text-rose-800 hover:bg-rose-100 px-2.5 py-1 rounded-lg transition flex items-center gap-1">
                                                    <span>❤️ Losartan 50mg</span>
                                                </button>
                                                <button type="button" @click="applyPreset({ name: 'Metformin 500mg (Tablet)', amount: '500mg', instruction: '1 tab 2x daily with meals', quantity: 60, isOtc: true })" 
                                                        class="text-xs font-bold bg-amber-50 border border-amber-200 text-amber-800 hover:bg-amber-100 px-2.5 py-1 rounded-lg transition flex items-center gap-1">
                                                    <span>🩺 Metformin 500mg</span>
                                                </button>

                                                <!-- Gastrointestinal & Allergy -->
                                                <button type="button" @click="applyPreset({ name: 'Omeprazole 20mg (Capsule)', amount: '20mg', instruction: '1 cap once daily 30 minutes before breakfast for 14 days', quantity: 14, isOtc: true })" 
                                                        class="text-xs font-bold bg-indigo-50 border border-indigo-200 text-indigo-800 hover:bg-indigo-100 px-2.5 py-1 rounded-lg transition flex items-center gap-1">
                                                    <span>💊 Omeprazole 20mg</span>
                                                </button>
                                                <button type="button" @click="applyPreset({ name: 'Cetirizine 10mg (Tablet)', amount: '10mg', instruction: '1 tab once daily at bedtime', quantity: 10, isOtc: true })" 
                                                        class="text-xs font-bold bg-sky-50 border border-sky-200 text-sky-800 hover:bg-sky-100 px-2.5 py-1 rounded-lg transition flex items-center gap-1">
                                                    <span>💊 Cetirizine 10mg</span>
                                                </button>
                                                <button type="button" @click="applyPreset({ name: 'Oral Rehydration Salts (ORS)', amount: '1 sachet', instruction: 'Dissolve 1 sachet in 1 liter clean water, drink after each loose stool', quantity: 4, isOtc: true })" 
                                                        class="text-xs font-bold bg-cyan-50 border border-cyan-200 text-cyan-800 hover:bg-cyan-100 px-2.5 py-1 rounded-lg transition flex items-center gap-1">
                                                    <span>💧 ORS Sachet</span>
                                                </button>
                                            </div>
                                        </div>
                                    @endif
                                </div>

                                <!-- Quick Instruction Buttons -->
                                <div class="flex flex-wrap gap-1.5 mb-4">
                                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mr-1 self-center">Frequency:</span>
                                    <template x-for="qi in quickInstructions" :key="qi">
                                        <button type="button" @click="currentInstruction = qi" 
                                                class="text-[10px] font-bold bg-white border border-slate-200 hover:border-teal-300 hover:bg-teal-50 text-slate-600 hover:text-teal-700 px-2 py-1 rounded-md transition"
                                                x-text="qi"></button>
                                    </template>
                                </div>

                                <button type="button" @click="addPrescription()" 
                                        class="w-full bg-teal-600 text-white py-2.5 rounded-lg font-bold text-sm hover:bg-teal-700 shadow-sm transition flex items-center justify-center gap-2"
                                        :disabled="!searchQuery.trim()"
                                        :class="!searchQuery.trim() ? 'opacity-50 cursor-not-allowed' : ''">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                                    Add to Prescription
                                </button>
                            </div>
                            
                            <!-- Hidden inputs for Laravel array validation -->
                            <template x-for="(item, index) in prescriptions" :key="'hidden_'+index">
                                <div>
                                    <input type="hidden" :name="`prescriptions_list[${index}][medicine_name]`" :value="item.medicine">
                                    <input type="hidden" :name="`prescriptions_list[${index}][dosage]`" :value="item.amount">
                                    <input type="hidden" :name="`prescriptions_list[${index}][frequency]`" :value="item.instruction">
                                    <input type="hidden" :name="`prescriptions_list[${index}][duration]`" :value="item.duration || ''">
                                    <input type="hidden" :name="`prescriptions_list[${index}][quantity]`" :value="item.quantity">
                                    <input type="hidden" :name="`prescriptions_list[${index}][is_otc]`" :value="item.isOtc ? '1' : '0'">
                                    <input type="hidden" :name="`prescriptions_list[${index}][medicine_id]`" :value="item.medicineId || ''">
                                </div>
                            </template>
                        </div>

                        <div class="grow mb-4" x-data="{ 
                            notes: `{{ old('medical_notes') }}`,
                            appendNote(text) {
                                if(this.notes.length > 0 && !this.notes.endsWith('\n')) {
                                    this.notes += '\n';
                                }
                                this.notes += text + '\n';
                            }
                        }">
                            <div class="flex items-center justify-between mb-2">
                                <label for="medical_notes" class="block text-sm font-bold text-slate-700 dark:text-slate-200">Detailed Clinical Notes <span class="text-slate-400 font-normal ml-1">(Optional)</span></label>
                                <div class="flex flex-wrap gap-2">
                                    <button type="button" @click="appendNote('[FOLLOW-UP: Fasting required before next visit]')" class="text-[10px] uppercase font-bold bg-slate-100 dark:bg-slate-800 hover:bg-teal-50 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-300 hover:text-teal-700 dark:hover:text-teal-300 border border-slate-200 dark:border-slate-700 rounded px-2 py-1 transition">
                                        + Fasting Required
                                    </button>
                                    <button type="button" @click="appendNote('[FOLLOW-UP: Bring previous medical records]')" class="text-[10px] uppercase font-bold bg-slate-100 dark:bg-slate-800 hover:bg-teal-50 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-300 hover:text-teal-700 dark:hover:text-teal-300 border border-slate-200 dark:border-slate-700 rounded px-2 py-1 transition">
                                        + Bring Records
                                    </button>
                                    <button type="button" @click="appendNote('[INSTRUCTION: Continue current medication]')" class="text-[10px] uppercase font-bold bg-slate-100 dark:bg-slate-800 hover:bg-teal-50 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-300 hover:text-teal-700 dark:hover:text-teal-300 border border-slate-200 dark:border-slate-700 rounded px-2 py-1 transition">
                                        + Continue Medication
                                    </button>
                                    <button type="button" @click="appendNote('[INSTRUCTION: Return immediately if symptoms persist or worsen]')" class="text-[10px] uppercase font-bold bg-slate-100 dark:bg-slate-800 hover:bg-teal-50 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-300 hover:text-teal-700 dark:hover:text-teal-300 border border-slate-200 dark:border-slate-700 rounded px-2 py-1 transition">
                                        + Return if Persists
                                    </button>
                                </div>
                            </div>
                            <textarea id="medical_notes" name="medical_notes" rows="4" x-model="notes"
                                class="w-full rounded-lg border-2 border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 text-slate-800 dark:text-white shadow-sm focus:border-teal-500 focus:ring-teal-500 text-sm p-3 transition" 
                                placeholder="Add observations, patient counseling notes..."></textarea>
                        </div>

                        @php
                            $hasPendingDiagnostics = $consultation->ancillaryRequests && $consultation->ancillaryRequests->whereIn('status', ['Pending', 'Specimen Collected', 'In Progress'])->count() > 0;
                        @endphp

                        @if($hasPendingDiagnostics)
                            <div class="mb-4 bg-sky-50 dark:bg-sky-950/40 border border-sky-200 dark:border-sky-800 rounded-xl p-4 flex items-start gap-3">
                                <div class="p-2 bg-sky-100 dark:bg-sky-900/60 text-sky-700 dark:text-sky-300 rounded-lg shrink-0">
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                </div>
                                <div class="text-xs">
                                    <p class="font-extrabold text-sky-900 dark:text-sky-200">Patient has pending Laboratory / Radiology tests.</p>
                                    <p class="text-sky-700 dark:text-sky-400 mt-0.5">Ending or completing this consultation now is allowed. The patient will automatically be flagged for a <strong>Follow-up Visit</strong> to review results once they are ready.</p>
                                </div>
                            </div>
                        @endif

                        <div class="bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-800 rounded-lg p-4 mb-2" x-data="{ isFollowUp: {{ $hasPendingDiagnostics ? 'true' : 'false' }} }">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h4 class="font-black text-amber-900 dark:text-amber-200 text-sm">Require Follow-up Visit?</h4>
                                    <p class="text-[11px] text-amber-700 dark:text-amber-300 mt-1">Toggle this if the patient needs to return. Allows them to book a Follow-up appointment online.</p>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer ml-4 shrink-0">
                                    <input type="checkbox" name="is_followup_needed" value="1" x-model="isFollowUp" class="sr-only peer">
                                    <div class="w-11 h-6 bg-amber-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-amber-500 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white dark:bg-gray-800 after:border-gray-300 dark:border-gray-600 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-amber-600 shadow-inner"></div>
                                </label>
                            </div>
                            
                            <div x-show="isFollowUp" x-collapse class="mt-4 pt-4 border-t border-amber-200 dark:border-amber-800">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div x-data="{
                                        showDatepicker: false,
                                        currentDate: new Date(),
                                        selectedDate: '{{ old('followup_date', '') }}',
                                        monthNames: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
                                        get daysInMonth() { return new Date(this.currentDate.getFullYear(), this.currentDate.getMonth() + 1, 0).getDate(); },
                                        get startDay() { return new Date(this.currentDate.getFullYear(), this.currentDate.getMonth(), 1).getDay(); },
                                        setMonth(monthIndex) { this.currentDate = new Date(this.currentDate.getFullYear(), monthIndex, 1); },
                                        setYear(year) { this.currentDate = new Date(year, this.currentDate.getMonth(), 1); },
                                        isPastDate(day) {
                                            let dateToCheck = new Date(this.currentDate.getFullYear(), this.currentDate.getMonth(), day);
                                            let today = new Date(); today.setHours(0,0,0,0);
                                            return dateToCheck < today;
                                        },
                                        selectDate(day) {
                                            if (this.isPastDate(day)) return;
                                            let date = new Date(this.currentDate.getFullYear(), this.currentDate.getMonth(), day);
                                            let offset = date.getTimezoneOffset();
                                            date = new Date(date.getTime() - (offset*60*1000));
                                            this.selectedDate = date.toISOString().split('T')[0];
                                            this.showDatepicker = false;
                                        },
                                        isSelected(day) {
                                            if(!this.selectedDate) return false;
                                            let date = new Date(this.currentDate.getFullYear(), this.currentDate.getMonth(), day);
                                            let offset = date.getTimezoneOffset();
                                            date = new Date(date.getTime() - (offset*60*1000));
                                            return this.selectedDate === date.toISOString().split('T')[0];
                                        },
                                        init() { if (this.selectedDate) { this.currentDate = new Date(this.selectedDate); } }
                                    }" class="relative">
                                        <label for="followup_date" class="block text-xs font-bold text-amber-900 mb-1">Target Return Date <span class="text-rose-500">*</span></label>
                                        <input type="hidden" name="followup_date" id="followup_date" x-model="selectedDate" :required="isFollowUp">
                                        
                                        <div @click="showDatepicker = !showDatepicker" class="w-full text-sm rounded-md border-amber-300 bg-white focus-within:border-amber-500 focus-within:ring-1 focus-within:ring-amber-500 p-2 cursor-pointer flex justify-between items-center text-amber-900 border">
                                            <span x-text="selectedDate ? selectedDate : 'Select Target Date'" :class="selectedDate ? 'text-amber-900' : 'text-amber-600/50'"></span>
                                            <svg class="h-4 w-4 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                        </div>

                                        <!-- Datepicker Popup -->
                                        <div x-show="showDatepicker" @click.away="showDatepicker = false" style="display: none;"
                                            class="absolute z-50 mt-1 w-[300px] p-4 bg-white border border-amber-200 rounded-lg shadow-xl outline-none">
                                            <div class="flex justify-between items-center mb-4 gap-2">
                                                <select @change="setMonth($event.target.value)" class="w-1/2 flex-1 rounded-md border-amber-300 text-sm font-medium text-amber-900 bg-amber-50 focus:border-amber-500 p-1">
                                                    <template x-for="(month, index) in monthNames" :key="index">
                                                        <option :value="index" x-text="month" :selected="index === currentDate.getMonth()"></option>
                                                    </template>
                                                </select>
                                                <select @change="setYear($event.target.value)" class="w-1/2 flex-1 rounded-md border-amber-300 text-sm font-medium text-amber-900 bg-amber-50 focus:border-amber-500 p-1">
                                                    <template x-for="year in Array.from({length: 5}, (_, i) => new Date().getFullYear() + i)" :key="year">
                                                        <option :value="year" x-text="year" :selected="year === currentDate.getFullYear()"></option>
                                                    </template>
                                                </select>
                                            </div>
                                            <div class="grid grid-cols-7 gap-1 mb-2">
                                                <template x-for="day in ['Su', 'Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa']">
                                                    <div class="text-center text-xs font-bold text-amber-700/50" x-text="day"></div>
                                                </template>
                                            </div>
                                            <div class="grid grid-cols-7 gap-1">
                                                <template x-for="blank in startDay"><div class="p-1"></div></template>
                                                <template x-for="day in daysInMonth" :key="day">
                                                    <div @click="selectDate(day)"
                                                        class="w-8 h-8 flex items-center justify-center rounded-full text-sm cursor-pointer transition-colors"
                                                        :class="{
                                                            'bg-amber-600 text-white font-bold shadow-md': isSelected(day),
                                                            'hover:bg-amber-100 text-amber-900': !isSelected(day) && !isPastDate(day),
                                                            'text-amber-300 cursor-not-allowed': isPastDate(day),
                                                            'bg-amber-50 text-amber-600 font-medium': !isSelected(day) && new Date().getDate() === day && new Date().getMonth() === currentDate.getMonth() && new Date().getFullYear() === currentDate.getFullYear()
                                                        }" x-text="day">
                                                    </div>
                                                </template>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="md:col-span-2" x-data="{ 
                                        reason: '{{ old('followup_reason') }}',
                                        appendReason(text) {
                                            if(this.reason.length > 0 && !this.reason.endsWith(', ')) {
                                                this.reason += ', ';
                                            }
                                            this.reason += text;
                                        }
                                    }">
                                        <div class="flex items-center justify-between mb-1.5">
                                         <label for="followup_reason" class="block text-xs font-bold text-amber-900 dark:text-amber-200">Reason for Return <span class="text-rose-500">*</span></label>
                                            <div class="flex flex-wrap gap-1.5">
                                                <button type="button" @click="appendReason('Check Laboratory results')" class="text-[9px] font-bold bg-amber-100 dark:bg-amber-900/60 text-amber-800 dark:text-amber-200 border border-amber-200 dark:border-amber-700 rounded px-1.5 py-0.5 hover:bg-amber-200 dark:hover:bg-amber-800">+ Check Labs</button>
                                                <button type="button" @click="appendReason('Monitor Vital Signs')" class="text-[9px] font-bold bg-amber-100 dark:bg-amber-900/60 text-amber-800 dark:text-amber-200 border border-amber-200 dark:border-amber-700 rounded px-1.5 py-0.5 hover:bg-amber-200 dark:hover:bg-amber-800">+ Monitor Vitals</button>
                                                <button type="button" @click="appendReason('Follow-up Checkup')" class="text-[9px] font-bold bg-amber-100 dark:bg-amber-900/60 text-amber-800 dark:text-amber-200 border border-amber-200 dark:border-amber-700 rounded px-1.5 py-0.5 hover:bg-amber-200 dark:hover:bg-amber-800">+ Routine Checkup</button>
                                                <button type="button" @click="appendReason('Medication Adjustment')" class="text-[9px] font-bold bg-amber-100 dark:bg-amber-900/60 text-amber-800 dark:text-amber-200 border border-amber-200 dark:border-amber-700 rounded px-1.5 py-0.5 hover:bg-amber-200 dark:hover:bg-amber-800">+ Med Adjustment</button>
                                            </div>
                                        </div>
                                        <textarea name="followup_reason" id="followup_reason" :required="isFollowUp" x-model="reason" rows="4" placeholder="e.g. Check lab results, monitor BP" class="w-full text-sm rounded-md border-amber-300 dark:border-amber-700 bg-white dark:bg-slate-900 focus:border-amber-500 focus:ring-amber-500 text-amber-900 dark:text-amber-100 shadow-inner"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="pt-4 border-t border-slate-100 dark:border-slate-700 mt-auto flex items-center justify-between gap-3">
                            <button type="button" @click="showCancelWalkout = true" class="px-4 py-2.5 bg-rose-50 dark:bg-rose-950/40 text-rose-700 dark:text-rose-300 hover:bg-rose-100 dark:hover:bg-rose-900/50 border border-rose-200 dark:border-rose-800 rounded-lg text-xs font-bold transition flex items-center gap-1.5 cursor-pointer">
                                <svg class="w-4 h-4 text-rose-600 dark:text-rose-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                </svg>
                                <span>Patient Walked Out / Cancel</span>
                            </button>

                            <button type="button" @click="showConfirm = true" class="px-6 py-3 bg-teal-600 text-white border border-transparent rounded-lg font-extrabold shadow-lg shadow-teal-600/30 hover:bg-teal-700 hover:shadow-teal-700/40 transition flex items-center gap-2 cursor-pointer">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                Complete Consultation
                            </button>

                            <!-- Custom Confirmation Modal -->
                            <div x-show="showConfirm" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm"
                                 x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
                                <div @click.away="showConfirm = false" class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-md overflow-hidden border border-slate-200"
                                     x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100">
                                    <div class="p-6 text-center">
                                        <div class="w-16 h-16 bg-teal-100 text-teal-600 rounded-full flex items-center justify-center mx-auto mb-4">
                                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        </div>
                                        <h3 class="text-xl font-black text-slate-800 mb-2">Ready to Complete?</h3>
                                        <p class="text-slate-500 text-sm mb-6">This will finalize the case and save it to the patient's medical history. Please ensure all diagnosis and instructions are correct.</p>
                                        <div class="flex flex-col gap-2">
                                            <button type="button" @click="$el.closest('form').submit()" class="w-full py-3 bg-teal-600 text-white rounded-xl font-black hover:bg-teal-700 transition shadow-lg shadow-teal-600/20">Yes, Finalize Case</button>
                                            <button type="button" @click="showConfirm = false" class="w-full py-3 bg-slate-100 text-slate-600 rounded-xl font-bold hover:bg-slate-200 transition">Go Back & Edit</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>

                    <!-- Patient Walkout / Cancel Modal -->
                    <div x-show="showCancelWalkout" x-cloak class="fixed inset-0 z-[120] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm"
                         x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
                        <div @click.away="showCancelWalkout = false" class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden border border-slate-200"
                             x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100">
                            <form action="{{ route('doctor.consultation.cancel', $consultation->id) }}" method="POST" class="p-6">
                                @csrf
                                <div class="flex items-start gap-4 mb-4">
                                    <div class="w-12 h-12 bg-rose-100 text-rose-600 rounded-2xl flex items-center justify-center shrink-0">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <h3 class="text-lg font-black text-slate-900 dark:text-white">Cancel Consultation / Patient Walkout</h3>
                                        <p class="text-xs text-slate-500 mt-1">Record patient walkout, consultation refusal, or urgent hospital referral. This will close the encounter and update queue records.</p>
                                    </div>
                                </div>

                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Reason for Cancellation <span class="text-rose-500">*</span></label>
                                        <select name="cancellation_reason" required class="w-full text-xs font-semibold rounded-xl border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 text-slate-900 dark:text-white p-2.5 focus:ring-rose-500 focus:border-rose-500">
                                            <option value="">-- Select Reason --</option>
                                            <option value="Patient Walked Out / Left Premises">Patient Walked Out / Left Premises</option>
                                            <option value="Patient Refused Consultation / Treatment">Patient Refused Consultation / Treatment</option>
                                            <option value="Emergency Hospital Transfer / Endorsement">Emergency Hospital Transfer / Endorsement</option>
                                            <option value="Patient Unresponsive / Called Multiple Times">Patient Unresponsive / Called Multiple Times</option>
                                            <option value="Other">Other</option>
                                        </select>
                                    </div>

                                    <div>
                                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Additional Notes / Remarks <span class="text-slate-400 font-normal">(Optional)</span></label>
                                        <textarea name="notes" rows="3" placeholder="Provide details, vitals recorded prior to leaving, or transfer destination..." class="w-full text-xs rounded-xl border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 text-slate-900 dark:text-white p-2.5 focus:ring-rose-500 focus:border-rose-500"></textarea>
                                    </div>
                                </div>

                                <div class="flex items-center justify-end gap-3 mt-6 pt-4 border-t border-slate-100 dark:border-slate-700">
                                    <button type="button" @click="showCancelWalkout = false" class="px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition cursor-pointer">
                                        Keep Encounter Open
                                    </button>
                                    <button type="submit" class="px-5 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-black text-xs shadow-md shadow-rose-600/30 transition flex items-center gap-1.5 cursor-pointer">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                        Confirm Cancellation
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Past Cases -->
                @foreach($pastConsultations as $past)
                    <div x-show="activeTab === 'past_{{ $past->id }}'" x-cloak
                         x-transition:enter="transition ease-out duration-200" 
                         x-transition:enter-start="opacity-0 translate-y-2" 
                         x-transition:enter-end="opacity-100 translate-y-0"
                         class="grow flex flex-col p-6 bg-slate-50 dark:bg-slate-900/90 rounded-b-xl rounded-tr-xl overflow-y-auto">
                        
                        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-slate-200 dark:border-slate-700 p-6 space-y-6 relative overflow-hidden">
                            <!-- Header ribbon for past cases -->
                            <div class="absolute top-0 left-0 w-1.5 h-full bg-teal-500"></div>

                            <div class="flex justify-between items-center border-b border-slate-100 dark:border-slate-700 pb-4">
                                <div>
                                    <h2 class="text-xl font-black text-slate-800 dark:text-white">Historical Record</h2>
                                    <p class="text-sm font-bold text-teal-600 dark:text-teal-400">{{ \Carbon\Carbon::parse($past->consultation_date)->format('l, F d, Y') }}</p>
                                </div>
                                <div class="text-right">
                                    <span class="block text-[10px] uppercase tracking-widest text-slate-400 dark:text-slate-400 font-bold mb-1">Attending Provider</span>
                                    <span class="inline-block font-semibold text-slate-700 dark:text-slate-200 bg-slate-100 dark:bg-slate-700 px-3 py-1 rounded border border-slate-200 dark:border-slate-600">{{ $past->doctor->name ?? ($past->nurse->name ?? 'Unknown') }}</span>
                                </div>
                            </div>

                            <!-- Triaged Vitals Grid -->
                            <div>
                                <h3 class="text-[11px] font-bold text-slate-400 dark:text-slate-400 uppercase tracking-widest mb-3 border-b border-slate-100 dark:border-slate-700 pb-1">Triaged Vitals</h3>
                                <div class="grid grid-cols-2 md:grid-cols-4 gap-3 text-sm">
                                    <div class="bg-slate-50 dark:bg-slate-700/50 p-2.5 rounded border border-slate-100 dark:border-slate-700 flex flex-col shadow-sm">
                                        <span class="text-xs text-slate-400 dark:text-slate-400 mb-1">BP</span>
                                        <span class="font-bold text-slate-700 dark:text-white text-sm">{{ $past->blood_pressure ?: ($past->preTriage?->blood_pressure ?: '--') }}</span>
                                    </div>
                                    <div class="bg-slate-50 dark:bg-slate-700/50 p-2.5 rounded border border-slate-100 dark:border-slate-700 flex flex-col shadow-sm">
                                        <span class="text-xs text-slate-400 dark:text-slate-400 mb-1">Temp</span>
                                        <span class="font-bold text-sm {{ floatval($past->temperature ?: ($past->preTriage?->temperature ?? 0)) > 37.5 ? 'text-red-600 dark:text-red-400' : 'text-slate-700 dark:text-white' }}">{{ $past->temperature ?: ($past->preTriage?->temperature ?: '--') }}°C</span>
                                    </div>
                                    <div class="bg-slate-50 dark:bg-slate-700/50 p-2.5 rounded border border-slate-100 dark:border-slate-700 flex flex-col shadow-sm">
                                        <span class="text-xs text-slate-400 dark:text-slate-400 mb-1">Heart Rate</span>
                                        <span class="font-bold text-slate-700 dark:text-white text-sm">{{ $past->heart_rate ?: ($past->preTriage?->heart_rate ?: '--') }}</span>
                                    </div>
                                    <div class="bg-slate-50 dark:bg-slate-700/50 p-2.5 rounded border border-slate-100 dark:border-slate-700 flex flex-col shadow-sm">
                                        <span class="text-xs text-slate-400 dark:text-slate-400 mb-1">Resp Rate</span>
                                        <span class="font-bold text-slate-700 dark:text-white text-sm">{{ $past->respiratory_rate ?: ($past->preTriage?->respiratory_rate ?: '--') }}</span>
                                    </div>
                                    <div class="bg-slate-50 dark:bg-slate-700/50 p-2.5 rounded border border-slate-100 dark:border-slate-700 flex flex-col shadow-sm">
                                        <span class="text-xs text-slate-400 dark:text-slate-400 mb-1">Pulse Rate</span>
                                        <span class="font-bold text-slate-700 dark:text-white text-sm">{{ $past->pulse_rate ?: ($past->preTriage?->pulse_rate ?: '--') }}</span>
                                    </div>
                                    <div class="bg-slate-50 dark:bg-slate-700/50 p-2.5 rounded border border-slate-100 dark:border-slate-700 flex flex-col shadow-sm">
                                        <span class="text-xs text-slate-400 dark:text-slate-400 mb-1">SpO2</span>
                                        <span class="font-bold text-slate-700 dark:text-white text-sm">{{ $past->spo2 ?: ($past->preTriage?->spo2 ?: ($past->preTriage?->oxygen_saturation ?: '--')) }}%</span>
                                    </div>
                                    <div class="bg-slate-50 dark:bg-slate-700/50 p-2.5 rounded border border-slate-100 dark:border-slate-700 flex flex-col shadow-sm">
                                        <span class="text-xs text-slate-400 dark:text-slate-400 mb-1">Weight</span>
                                        <span class="font-bold text-slate-700 dark:text-white text-sm">{{ $past->weight ?: ($past->preTriage?->weight ?: '--') }}kg</span>
                                    </div>
                                    <div class="bg-slate-50 dark:bg-slate-700/50 p-2.5 rounded border border-slate-100 dark:border-slate-700 flex flex-col shadow-sm">
                                        <span class="text-xs text-slate-400 dark:text-slate-400 mb-1">Height</span>
                                        <span class="font-bold text-slate-700 dark:text-white text-sm">{{ $past->height ?: ($past->preTriage?->height ?: '--') }}cm</span>
                                    </div>
                                </div>
                            </div>

                            @if($past->preTriage?->symptoms)
                                <div>
                                    <h3 class="text-[11px] font-bold text-slate-400 dark:text-slate-400 uppercase tracking-widest mb-1.5 border-b border-slate-100 dark:border-slate-700 pb-1">Chief Complaint</h3>
                                    <p class="text-slate-700 dark:text-slate-200 bg-slate-50 dark:bg-slate-700/50 p-3 rounded-lg text-sm italic border border-slate-100 dark:border-slate-700 shadow-inner">{{ $past->preTriage->symptoms }}</p>
                                </div>
                            @endif

                            <div>
                                <h3 class="text-[11px] font-bold text-teal-600 dark:text-teal-400 uppercase tracking-widest mb-1.5 border-b border-slate-100 dark:border-slate-700 pb-1">Primary Diagnosis</h3>
                                <p class="text-slate-800 dark:text-white font-semibold p-3 text-base border-l-4 border-teal-500 bg-teal-50/30 dark:bg-teal-950/30 rounded-r-lg">{{ $past->diagnosis ?: 'No diagnosis recorded.' }}</p>
                            </div>
                            
                            @if($past->prescription)
                                <div>
                                    <h3 class="text-[11px] font-bold text-slate-400 dark:text-slate-400 uppercase tracking-widest mb-1.5 border-b border-slate-100 dark:border-slate-700 pb-1">Prescription</h3>
                                    <p class="text-slate-700 dark:text-slate-200 font-mono text-sm bg-slate-50 dark:bg-slate-900/60 p-4 rounded-lg border border-slate-200 dark:border-slate-700 whitespace-pre-wrap">{{ $past->prescription }}</p>
                                </div>
                            @endif

                            @if($past->medical_notes)
                                <div>
                                    <h3 class="text-[11px] font-bold text-slate-400 dark:text-slate-400 uppercase tracking-widest mb-1.5 border-b border-slate-100 dark:border-slate-700 pb-1">Clinical Notes</h3>
                                    <p class="text-slate-600 dark:text-slate-300 text-sm p-3 bg-slate-50/50 dark:bg-slate-900/40 rounded-lg border border-slate-100 dark:border-slate-700 whitespace-pre-wrap">{{ $past->medical_notes }}</p>
                                </div>
                            @endif
                            
                            @if($past->is_followup_needed)
                                <div class="bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-800 rounded p-3 mt-2">
                                    <h3 class="text-[11px] font-bold text-amber-700 dark:text-amber-400 uppercase tracking-widest mb-1">Follow-up Instructed</h3>
                                    <p class="text-amber-800 dark:text-amber-200 text-sm font-semibold">Scheduled: {{ \Carbon\Carbon::parse($past->followup_date)->format('M d, Y') }}</p>
                                    @if($past->followup_reason)
                                        <p class="text-amber-700 dark:text-amber-300 text-xs italic mt-1">Reason: {{ $past->followup_reason }}</p>
                                    @endif
                                </div>
                            @endif

                            @if(in_array($past->status, ['completed', 'done']) && ($past->doctor_id === auth()->id() || in_array(auth()->user()->role, ['admin', 'super_admin'])))
                                <div x-data="{ showPastAddendum: false }" class="pt-4 border-t border-slate-100 dark:border-slate-700">
                                    <button type="button" @click="showPastAddendum = !showPastAddendum" class="text-xs font-bold text-amber-700 dark:text-amber-400 hover:text-amber-800 dark:hover:text-amber-300 flex items-center gap-1.5 transition">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                        <span x-text="showPastAddendum ? 'Close Addendum Box' : '+ Append Official Clinical Addendum'"></span>
                                    </button>

                                    <div x-show="showPastAddendum" x-collapse class="mt-2.5">
                                        <form action="{{ route('doctor.consultation.addendum', $past->id) }}" method="POST" class="bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-800 rounded-xl p-3.5">
                                            @csrf
                                            <label class="block text-xs font-bold text-amber-900 dark:text-amber-200 mb-1">Signed Clinical Addendum</label>
                                            <p class="text-[11px] text-amber-700 dark:text-amber-300 mb-2">Appends an official, timestamped clinical note under {{ auth()->user()->formatted_name ?? auth()->user()->name }} to this historical encounter.</p>
                                            <textarea name="addendum_text" required rows="2" placeholder="Record clinical clarification, telephone follow-up notes, or late findings..." class="w-full text-xs rounded-lg border-amber-300 dark:border-amber-700 bg-white dark:bg-slate-900 text-slate-800 dark:text-white p-2.5 focus:ring-amber-500 focus:border-amber-500"></textarea>
                                            <div class="flex justify-end gap-2 mt-2">
                                                <button type="button" @click="showPastAddendum = false" class="px-3 py-1.5 text-xs text-slate-600 dark:text-slate-300 hover:text-slate-800 dark:hover:text-white font-semibold">Cancel</button>
                                                <button type="submit" class="px-3 py-1.5 bg-amber-600 hover:bg-amber-700 text-white text-xs font-bold rounded-lg shadow-sm transition">Sign & Append</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            @endif

                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('prescriptionBuilder', (themeColor) => ({
        prescriptions: [],
        searchQuery: '',
        currentAmount: '',
        currentInstruction: '',
        currentQuantity: '',
        currentDuration: '',
        suggestions: [],
        showSuggestions: false,
        isOtcMode: false,
        selectedMedicineId: '',
        quickInstructions: [
            '1x a day',
            '2x a day',
            '3x a day',
            'Every 4 hours as needed',
            'Once daily before meals',
            'After meals',
            'Before bedtime',
            'As needed for pain',
            'Apply topically 2x a day',
        ],
        
        async searchMedicine() {
            if (this.searchQuery.length < 1) {
                this.suggestions = [];
                this.showSuggestions = false;
                return;
            }
            try {
                const res = await fetch(`/api/medicines/search?q=${encodeURIComponent(this.searchQuery)}`);
                this.suggestions = await res.json();
                this.showSuggestions = true;
            } catch (e) {
                console.error(e);
            }
        },
        
        selectMedicine(med) {
            this.searchQuery = med.name + (med.form ? ` (${med.form})` : '');
            this.showSuggestions = false;
            
            // Store medicine_id for server-side validation
            this.selectedMedicineId = med.id;
            
            // Focus on amount input
            setTimeout(() => {
                if (this.$refs.amountInput) this.$refs.amountInput.focus();
            }, 50);
        },

        applyPreset(preset) {
            this.searchQuery = preset.name;
            this.currentAmount = preset.amount;
            this.currentInstruction = preset.instruction;
            this.currentQuantity = preset.quantity;
            this.isOtcMode = preset.isOtc;
            this.selectedMedicineId = preset.medicineId || '';
        },
        
        addPrescription() {
            if (!this.searchQuery.trim()) return;
            this.prescriptions.push({
                medicine: this.searchQuery,
                amount: this.currentAmount || 'As prescribed',
                instruction: this.currentInstruction || 'As directed',
                quantity: this.currentQuantity || '',
                duration: this.currentDuration || '',
                isOtc: this.isOtcMode,
                medicineId: this.selectedMedicineId || '',
            });
            this.searchQuery = '';
            this.currentAmount = '';
            this.currentInstruction = '';
            this.currentQuantity = '';
            this.currentDuration = '';
            this.isOtcMode = false;
            this.selectedMedicineId = '';
            this.suggestions = [];
            
            // Focus back on search
            setTimeout(() => {
                if (this.$refs.medicineInput) this.$refs.medicineInput.focus();
            }, 50);
        },
        
        removePrescription(index) {
            this.prescriptions.splice(index, 1);
        }
    }));
});
</script>
@endpush
