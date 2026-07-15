@extends('layouts.doctor')

@section('header', 'Consultation Room')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">
    <!-- Back button & Header -->
    <div class="flex items-center justify-between">
        <div class="flex items-center space-x-3">
            <a href="{{ route('doctor.dashboard') }}" class="p-2 bg-white dark:bg-gray-800 rounded-full text-slate-500 hover:text-teal-600 hover:bg-teal-50 shadow-sm border border-slate-200 transition">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            </a>
            <h2 class="text-2xl font-bold text-slate-800">Active Consultation</h2>
        </div>
        <div class="bg-teal-100 text-teal-800 px-4 py-1.5 rounded-full font-bold shadow-sm border border-teal-200 flex items-center gap-2">
            <span class="w-2.5 h-2.5 bg-teal-500 rounded-full animate-pulse"></span>
            Queue #{{ $consultation->queue_number }}
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
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="bg-slate-50 border-b border-slate-200 p-4">
                    <div class="flex items-start justify-between gap-2">
                        <div class="flex items-center space-x-4 flex-1 min-w-0">
                        <div class="h-16 w-16 bg-teal-100 text-teal-600 rounded-full flex items-center justify-center text-2xl font-bold border-2 border-white shadow-sm">
                            {{ substr($patient->first_name, 0, 1) }}{{ substr($patient->last_name, 0, 1) }}
                        </div>
                        <div class="flex-1 min-w-0">
                                <div class="flex items-start justify-between gap-2">
                                    <h3 class="text-lg font-extrabold text-slate-800 leading-tight" title="{{ $patient->full_name }}">{{ $patient->full_name }}</h3>
                                    <span class="bg-indigo-100 text-indigo-700 text-[10px] uppercase tracking-wider font-black px-2 py-1 rounded shrink-0">{{ $patient->classification }}</span>
                                </div>
                                <p class="text-sm text-slate-500 font-medium mt-1">{{ \Carbon\Carbon::parse($patient->dob)->age }} yrs &bull; {{ $patient->sex }}</p>
                                @if($patient->blood_type)
                                    <p class="text-xs text-red-500 font-bold mt-1 inline-flex items-center gap-1 bg-red-50 px-2 py-0.5 rounded border border-red-100">
                                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path></svg>
                                        {{ $patient->blood_type }}
                                    </p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                <div class="p-4 space-y-3 text-sm">
                    <div class="grid grid-cols-3 gap-1">
                        <span class="text-slate-500 font-medium col-span-1">DOB:</span>
                        <span class="text-slate-800 font-semibold col-span-2">{{ \Carbon\Carbon::parse($patient->dob)->format('M d, Y') }}</span>
                    </div>
                    <div class="grid grid-cols-3 gap-1">
                        <span class="text-slate-500 font-medium col-span-1">Contact:</span>
                        <span class="text-slate-800 font-semibold col-span-2">{{ $patient->contact_number ?: 'N/A' }}</span>
                    </div>
                    <div class="grid grid-cols-3 gap-1">
                        <span class="text-slate-500 font-medium col-span-1">Address:</span>
                        <span class="text-slate-800 font-semibold col-span-2 line-clamp-2">{{ $patient->address }}</span>
                    </div>
                </div>
            </div>

            <!-- Triage Vitals -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-slate-200 p-0 overflow-hidden">
                <div class="bg-slate-800 text-white p-3 border-b border-slate-700 flex justify-between items-center">
                    <h3 class="font-bold flex items-center gap-2">
                        <svg class="w-4 h-4 text-rose-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg>
                        Triage Vitals
                    </h3>
                    <span class="text-xs text-slate-300">{{ $consultation->created_at->diffForHumans() }}</span>
                </div>
                
                <div class="p-4 bg-slate-50 space-y-4">
                    <!-- Chief Complaint / Symptoms -->
                    <div>
                        <h4 class="text-xs font-bold text-slate-500 uppercase tracking-widest mb-1.5 border-b border-slate-200 pb-1">Chief Complaint / Symptoms</h4>
                        <p class="text-slate-800 text-sm font-medium bg-white dark:bg-gray-800 p-2.5 rounded border border-slate-200 shadow-sm leading-relaxed whitespace-pre-line">{{ $consultation->preTriage?->symptoms ?: 'None recorded.' }}</p>
                    </div>

                    @if($consultation->preTriage?->past_medical_history && $consultation->preTriage?->past_medical_history !== 'N/A')
                        <div>
                            <h4 class="text-xs font-bold text-slate-500 uppercase tracking-widest mb-1.5">Past Medical History</h4>
                            <p class="text-slate-700 text-sm bg-rose-50 p-2 rounded border border-rose-100">{{ $consultation->preTriage->past_medical_history }}</p>
                        </div>
                    @endif

                    @if($consultation->preTriage?->medicine_taken && $consultation->preTriage?->medicine_taken !== 'N/A')
                        <div>
                            <h4 class="text-xs font-bold text-slate-500 uppercase tracking-widest mb-1.5">Medicine Taken</h4>
                            <p class="text-slate-700 text-sm bg-blue-50 p-2 rounded border border-blue-100">{{ $consultation->preTriage->medicine_taken }}</p>
                        </div>
                    @endif

                    @if($consultation->preTriage?->known_allergies && $consultation->preTriage?->known_allergies !== 'N/A' && $consultation->preTriage?->known_allergies !== 'None')
                        <div>
                            <h4 class="text-xs font-bold text-red-500 uppercase tracking-widest mb-1.5 flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                Known Allergies
                            </h4>
                            <p class="text-red-700 text-sm bg-red-50 p-2 rounded border border-red-200 font-semibold">{{ $consultation->preTriage->known_allergies }}</p>
                        </div>
                    @endif
                </div>

                <!-- Vital Stats Grid — All 8 vitals -->
                <div class="grid grid-cols-2 gap-px bg-slate-200">
                    <div class="bg-white dark:bg-gray-800 p-3">
                        <span class="block text-xs text-slate-500 font-semibold mb-0.5">Blood Pressure</span>
                        <span class="block text-lg font-bold text-slate-800">{{ $consultation->preTriage?->blood_pressure ?: '--/--' }} <span class="text-xs font-normal text-slate-400">mmHg</span></span>
                    </div>
                    <div class="bg-white dark:bg-gray-800 p-3">
                        <span class="block text-xs text-slate-500 font-semibold mb-0.5">Temperature</span>
                        <span class="block text-lg font-bold {{ floatval($consultation->preTriage?->temperature ?? 0) > 37.5 ? 'text-red-500' : 'text-slate-800' }}">{{ $consultation->preTriage?->temperature ?: '--' }} <span class="text-xs font-normal text-slate-400">°C</span></span>
                    </div>
                    <div class="bg-white dark:bg-gray-800 p-3">
                        <span class="block text-xs text-slate-500 font-semibold mb-0.5">Heart Rate</span>
                        <span class="block text-lg font-bold text-slate-800">{{ $consultation->preTriage?->heart_rate ?: '--' }} <span class="text-xs font-normal text-slate-400">bpm</span></span>
                    </div>
                    <div class="bg-white dark:bg-gray-800 p-3">
                        <span class="block text-xs text-slate-500 font-semibold mb-0.5">Respiratory Rate</span>
                        <span class="block text-lg font-bold text-slate-800">{{ $consultation->preTriage?->respiratory_rate ?: '--' }} <span class="text-xs font-normal text-slate-400">cpm</span></span>
                    </div>
                    <div class="bg-white dark:bg-gray-800 p-3">
                        <span class="block text-xs text-slate-500 font-semibold mb-0.5">Pulse Rate</span>
                        <span class="block text-lg font-bold text-slate-800">{{ $consultation->preTriage?->pulse_rate ?: '--' }} <span class="text-xs font-normal text-slate-400">bpm</span></span>
                    </div>
                    <div class="bg-white dark:bg-gray-800 p-3">
                        <span class="block text-xs text-slate-500 font-semibold mb-0.5">SpO2 / O₂ Sat</span>
                        <span class="block text-lg font-bold text-slate-800">{{ $consultation->preTriage?->spo2 ?: ($consultation->preTriage?->oxygen_saturation ?: '--') }} <span class="text-xs font-normal text-slate-400">%</span></span>
                    </div>
                    <div class="bg-white dark:bg-gray-800 p-3">
                        <span class="block text-xs text-slate-500 font-semibold mb-0.5">Weight</span>
                        <span class="block text-lg font-bold text-slate-800">{{ $consultation->preTriage?->weight ?: '--' }} <span class="text-xs font-normal text-slate-400">kg</span></span>
                    </div>
                    <div class="bg-white dark:bg-gray-800 p-3">
                        <span class="block text-xs text-slate-500 font-semibold mb-0.5">Height</span>
                        <span class="block text-lg font-bold text-slate-800">{{ $consultation->preTriage?->height ?: '--' }} <span class="text-xs font-normal text-slate-400">cm</span></span>
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
                        :class="activeTab === 'current' ? 'bg-white dark:bg-gray-800 text-teal-800 border-teal-600 border-2 border-b-white font-extrabold pb-3 pt-2 shadow-[0_-4px_6px_-2px_rgba(20,184,166,0.1)] z-20 relative' : 'bg-slate-50 text-slate-500 border-slate-300 border border-b-0 hover:bg-slate-100 pb-2 pt-1.5 mt-1 font-semibold hover:text-slate-700'"
                        class="px-5 rounded-t-xl transition-all whitespace-nowrap shrink-0 text-sm">
                    <span class="flex items-center gap-2">
                        <svg class="w-4 h-4" x-show="activeTab === 'current'" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                        Current Case
                    </span>
                </button>
                
                <!-- Past Case Tabs (show first 3) -->
                @forelse($pastConsultations as $past)
                    @if($loop->index < 3)
                        <button type="button" @click="activeTab = 'past_{{ $past->id }}'" 
                                :class="activeTab === 'past_{{ $past->id }}' ? 'bg-white dark:bg-gray-800 text-teal-800 border-teal-600 border-2 border-b-white font-bold pb-3 pt-2 shadow-[0_-4px_6px_-2px_rgba(20,184,166,0.1)] z-20 relative' : 'bg-slate-100 text-slate-400 border-slate-200 border border-b-0 hover:bg-slate-50 pb-2 pt-1.5 mt-1 hover:text-slate-600 font-medium'"
                                class="px-4 rounded-t-xl transition-all whitespace-nowrap shrink-0 text-sm">
                            Past: {{ \Carbon\Carbon::parse($past->consultation_date)->format('M d, Y') }}
                        </button>
                    @endif
                @empty
                    <span class="pb-2 pt-2 px-3 text-xs text-slate-400 italic">No past history.</span>
                @endforelse

                <!-- "More History" dropdown for 4th+ records -->
                @if($pastConsultations->count() > 3)
                    <div class="relative shrink-0" x-data="{ moreOpen: false }" @click.away="moreOpen = false">
                        <button type="button" @click="moreOpen = !moreOpen"
                                :class="moreOpen || {{ json_encode($pastConsultations->slice(3)->pluck('id')->map(fn($id) => 'past_'.$id)->toArray()) }}.includes(activeTab) ? 'bg-white dark:bg-gray-800 text-teal-800 border-teal-600 border-2 border-b-white font-bold pb-3 pt-2 z-20 relative' : 'bg-slate-100 text-slate-400 border-slate-200 border border-b-0 hover:bg-slate-50 pb-2 pt-1.5 mt-1 hover:text-slate-600 font-medium'"
                                class="px-4 rounded-t-xl transition-all whitespace-nowrap text-sm flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"></path></svg>
                            More History ({{ $pastConsultations->count() - 3 }})
                            <svg class="w-3 h-3 transition-transform" :class="moreOpen ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>
                        <div x-show="moreOpen" x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 -translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" style="display: none;"
                             class="absolute right-0 top-full mt-1 w-64 bg-white dark:bg-gray-800 rounded-xl shadow-xl border border-slate-200 dark:border-gray-700 z-50 max-h-64 overflow-y-auto py-1">
                            @foreach($pastConsultations->slice(3) as $older)
                                <button type="button" @click="activeTab = 'past_{{ $older->id }}'; moreOpen = false"
                                        :class="activeTab === 'past_{{ $older->id }}' ? 'bg-teal-50 text-teal-800 font-bold' : 'text-slate-600 hover:bg-slate-50'"
                                        class="w-full text-left px-4 py-2.5 text-sm transition-colors flex items-center justify-between gap-2 border-b border-slate-100 dark:border-gray-700 last:border-0">
                                    <span>{{ \Carbon\Carbon::parse($older->consultation_date)->format('M d, Y') }}</span>
                                    <span class="text-[10px] text-slate-400 font-mono">{{ $older->diagnosis ? Str::limit($older->diagnosis, 20) : '—' }}</span>
                                </button>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            <!-- Tab Content Container -->
            <div class="bg-white dark:bg-gray-800 rounded-b-xl rounded-tr-xl shadow-lg border-2 border-teal-600 grow flex flex-col z-0 relative">
                
                <!-- Tab 1: Current Case Form -->
                <div x-show="activeTab === 'current'" 
                     x-transition:enter="transition ease-out duration-200" 
                     x-transition:enter-start="opacity-0 translate-y-2" 
                     x-transition:enter-end="opacity-100 translate-y-0"
                     class="grow flex flex-col h-full">
                    
                    <div class="p-5 border-b border-slate-100 bg-white dark:bg-gray-800 rounded-tr-lg">
                        <h3 class="text-lg font-extrabold text-slate-800">Physician's Assessment</h3>
                        <p class="text-sm text-slate-500">Document your diagnosis, prescribe treatments, and add clinical notes.</p>
                    </div>

                    <div x-data="{ showAncillary: false }" class="p-6 pb-0">
                        <button type="button" @click="showAncillary = !showAncillary" class="w-full bg-slate-50 border border-slate-200 text-slate-700 hover:bg-indigo-50 hover:text-indigo-700 hover:border-indigo-200 font-bold py-3 px-4 rounded-lg flex justify-between items-center transition-colors">
                            <span class="flex items-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path></svg>
                                Issue Laboratory / Radiology Request
                            </span>
                            <svg class="w-5 h-5 transform transition-transform" :class="showAncillary ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>
                        
                        <div x-show="showAncillary" x-collapse class="mt-3">
                            <div class="bg-indigo-50 border border-indigo-200 p-4 rounded-lg shadow-inner">
                                <p class="text-xs text-indigo-700 mb-3 font-medium">Forward this patient to the Laboratory or Radiology queue. (Simulation Flow: Does not pause consultation)</p>
                                <form action="{{ route('doctor.ancillary.store', $consultation->id) }}" method="POST" class="flex flex-col md:flex-row gap-3" x-data="{ requestType: 'Laboratory' }">
                                    @csrf
                                    <select name="type" x-model="requestType" class="text-sm rounded-md border-indigo-300 focus:border-indigo-500 focus:ring-indigo-500 bg-white" required>
                                        <option value="Laboratory">Laboratory Request</option>
                                        <option value="Radiology">Radiology Request</option>
                                    </select>
                                    <select name="test_name" x-show="requestType === 'Laboratory'" class="flex-1 text-sm rounded-md border-indigo-300 focus:border-indigo-500 focus:ring-indigo-500 bg-white">
                                        <option value="" disabled selected>-- Select Laboratory Test --</option>
                                        <option value="CBC">CBC (Complete Blood Count)</option>
                                        <option value="Urinalysis">Urinalysis</option>
                                        <option value="Fecalysis">Fecalysis</option>
                                        <option value="Blood Typing">Blood Typing</option>
                                        <option value="Fasting Blood Sugar (FBS)">Fasting Blood Sugar (FBS)</option>
                                        <option value="Other">Other (Specify in notes)</option>
                                    </select>
                                    <select name="test_name" x-show="requestType === 'Radiology'" class="flex-1 text-sm rounded-md border-indigo-300 focus:border-indigo-500 focus:ring-indigo-500 bg-white" x-cloak>
                                        <option value="" disabled selected>-- Select Radiology Test --</option>
                                        <option value="Chest X-Ray PA">Chest X-Ray PA</option>
                                        <option value="Chest X-Ray AP">Chest X-Ray AP</option>
                                        <option value="ECG">ECG (Electrocardiogram)</option>
                                        <option value="Ultrasound">Ultrasound</option>
                                        <option value="Other">Other (Specify in notes)</option>
                                    </select>
                                    <button type="submit" class="bg-indigo-600 text-white px-5 py-2 flex items-center justify-center gap-2 rounded-md font-bold text-sm hover:bg-indigo-700 shadow-sm transition shrink-0">
                                        Send Request
                                    </button>
                                </form>
                            </div>
                        </div>

                        <!-- Display Ancillary Results -->
                        @if($consultation->ancillaryRequests && $consultation->ancillaryRequests->count() > 0)
                            <div class="mt-4 space-y-3">
                                <h4 class="text-sm font-bold text-slate-700 uppercase tracking-widest border-b border-slate-200 pb-2">Laboratory / Radiology Results</h4>
                                @foreach($consultation->ancillaryRequests as $ancillary)
                                    <div class="bg-white border rounded-lg p-4 shadow-sm {{ $ancillary->status === 'completed' ? 'border-green-200' : 'border-amber-200' }}">
                                        <div class="flex items-start justify-between mb-2">
                                            <div>
                                                <span class="inline-block px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider {{ $ancillary->type === 'Laboratory' ? 'bg-purple-100 text-purple-700' : 'bg-indigo-100 text-indigo-700' }}">
                                                    {{ $ancillary->type }}
                                                </span>
                                                <h5 class="font-bold text-slate-800 mt-1">{{ $ancillary->test_name }}</h5>
                                            </div>
                                            <div>
                                                @if($ancillary->status === 'completed')
                                                    <span class="inline-flex items-center gap-1 text-xs font-bold text-green-700 bg-green-50 px-2 py-1 rounded-md border border-green-200">
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                                        Completed
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center gap-1 text-xs font-bold text-amber-700 bg-amber-50 px-2 py-1 rounded-md border border-amber-200">
                                                        <svg class="w-3.5 h-3.5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                                        Pending
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                        
                                        @if($ancillary->remarks)
                                            <p class="text-xs text-slate-500 mb-2 italic">Remarks/Instructions: {{ $ancillary->remarks }}</p>
                                        @endif

                                        <div class="mt-3 bg-slate-50 rounded-md p-3 border border-slate-100">
                                            <p class="text-[11px] font-bold text-slate-400 uppercase tracking-widest mb-1">Results / Findings</p>
                                            @if($ancillary->status === 'completed')
                                                <p class="text-sm text-slate-800 font-medium whitespace-pre-line">{{ $ancillary->result_text }}</p>
                                            @else
                                                <p class="text-sm text-slate-500 italic">Waiting for laboratory/radiology to upload results...</p>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    <form action="{{ route('doctor.consultation.complete', $consultation->id) }}" method="POST" class="grow flex flex-col p-6 space-y-6">
                        @csrf
                        
                        <div>
                            <label for="diagnosis" class="block text-sm font-bold text-slate-700 mb-2">Primary Diagnosis <span class="text-rose-500">*</span></label>
                            <textarea id="diagnosis" name="diagnosis" rows="3" required
                                class="w-full rounded-lg border-2 border-slate-300 shadow-sm focus:border-teal-500 focus:ring-teal-500 text-sm p-3 transition" 
                                placeholder="Enter conclusive medical diagnosis...">{{ old('diagnosis') }}</textarea>
                        </div>

                        <!-- Dynamic Prescription Builder -->
                        <div x-data="prescriptionBuilder('teal')" class="mb-4 relative">
                            <div class="flex justify-between items-center mb-2">
                                <label class="block text-sm font-bold text-slate-700">Prescription / Treatment Plan <span class="text-slate-400 font-normal ml-1">(Optional)</span></label>
                                <span class="text-[10px] font-black text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded border border-emerald-100 uppercase tracking-widest flex items-center gap-1">
                                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                    Simulation Only
                                </span>
                            </div>
                            
                            <!-- Visual List of added medicines -->
                            <div class="space-y-2 mb-3 mt-2" x-show="prescriptions.length > 0" x-cloak>
                                <template x-for="(item, index) in prescriptions" :key="index">
                                    <div class="flex items-center justify-between p-3 bg-teal-50 border border-teal-200 rounded-lg shadow-sm">
                                        <div class="flex-1">
                                            <p class="font-bold text-teal-900 text-sm" x-text="item.medicine"></p>
                                            <p class="text-xs text-teal-700 mt-0.5"><span x-text="item.amount"></span> &bull; <span x-text="item.instruction"></span></p>
                                        </div>
                                        <button type="button" @click="removePrescription(index)" class="text-rose-500 hover:text-rose-700 p-1 opacity-70 hover:opacity-100 transition">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                        </button>
                                    </div>
                                </template>
                            </div>

                            <!-- Builder Form -->
                            <div class="bg-slate-50 border border-slate-200 p-4 rounded-lg relative">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mb-3">
                                    <div class="relative">
                                        <input type="text" x-model="searchQuery" @input.debounce.300ms="searchMedicine" @keydown.escape="showSuggestions = false" @click.away="showSuggestions = false" placeholder="Search medicine (e.g. Paracetamol)..." class="w-full text-sm rounded-md border-slate-300 focus:border-teal-500 focus:ring-teal-500">
                                        
                                        <!-- Suggestions Dropdown -->
                                        <div x-show="showSuggestions && suggestions.length > 0" class="absolute z-50 w-full bg-white dark:bg-gray-800 mt-1 border border-slate-200 rounded-md shadow-lg max-h-48 overflow-y-auto" x-cloak>
                                            <template x-for="med in suggestions" :key="med.id">
                                                <div @click="selectMedicine(med)" class="px-4 py-2 hover:bg-teal-50 cursor-pointer border-b border-slate-100 last:border-0 transition">
                                                    <p class="font-semibold text-sm text-slate-800" x-text="med.name"></p>
                                                    <p class="text-[11px] text-slate-500" x-text="(med.generic_name || '') + (med.form ? ' - ' + med.form : '')"></p>
                                                </div>
                                            </template>
                                        </div>
                                    </div>
                                    <div>
                                        <input type="text" x-model="currentAmount" @keydown.enter.prevent="$refs.instInput.focus()" placeholder="Amount (e.g. 10 tabs, 1 bottle)" class="w-full text-sm rounded-md border-slate-300 focus:border-teal-500 focus:ring-teal-500">
                                    </div>
                                </div>
                                <div class="flex gap-2">
                                    <input type="text" x-ref="instInput" x-model="currentInstruction" @keydown.enter.prevent="addPrescription()" placeholder="Instructions (e.g. 3x a day after meals)" class="flex-1 text-sm rounded-md border-slate-300 focus:border-teal-500 focus:ring-teal-500">
                                    <button type="button" @click="addPrescription()" class="bg-teal-600 text-white px-4 py-2 rounded-md font-bold text-sm hover:bg-teal-700 shadow-sm shrink-0 transition">Add Item</button>
                                </div>
                            </div>
                            
                            <!-- Hidden input to submit the JSON -->
                            <input type="hidden" name="prescription" :value="JSON.stringify(prescriptions)">
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
                                <label for="medical_notes" class="block text-sm font-bold text-slate-700">Detailed Clinical Notes <span class="text-slate-400 font-normal ml-1">(Optional)</span></label>
                                <div class="flex flex-wrap gap-2">
                                    <button type="button" @click="appendNote('[FOLLOW-UP: Fasting required before next visit]')" class="text-[10px] uppercase font-bold bg-slate-100 hover:bg-teal-50 text-slate-600 hover:text-teal-700 border border-slate-200 rounded px-2 py-1 transition">
                                        + Fasting Required
                                    </button>
                                    <button type="button" @click="appendNote('[FOLLOW-UP: Bring previous medical records]')" class="text-[10px] uppercase font-bold bg-slate-100 hover:bg-teal-50 text-slate-600 hover:text-teal-700 border border-slate-200 rounded px-2 py-1 transition">
                                        + Bring Records
                                    </button>
                                    <button type="button" @click="appendNote('[INSTRUCTION: Continue current medication]')" class="text-[10px] uppercase font-bold bg-slate-100 hover:bg-teal-50 text-slate-600 hover:text-teal-700 border border-slate-200 rounded px-2 py-1 transition">
                                        + Continue Medication
                                    </button>
                                    <button type="button" @click="appendNote('[INSTRUCTION: Return immediately if symptoms persist or worsen]')" class="text-[10px] uppercase font-bold bg-slate-100 hover:bg-teal-50 text-slate-600 hover:text-teal-700 border border-slate-200 rounded px-2 py-1 transition">
                                        + Return if Persists
                                    </button>
                                </div>
                            </div>
                            <textarea id="medical_notes" name="medical_notes" rows="4" x-model="notes"
                                class="w-full rounded-lg border-2 border-slate-300 shadow-sm focus:border-teal-500 focus:ring-teal-500 text-sm p-3 transition" 
                                placeholder="Add observations, patient counseling notes..."></textarea>
                        </div>

                        <div class="bg-amber-50 border border-amber-200 rounded-lg p-4 mb-2" x-data="{ isFollowUp: false }">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h4 class="font-black text-amber-900 text-sm">Require Follow-up Visit?</h4>
                                    <p class="text-[11px] text-amber-700 mt-1">Toggle this if the patient needs to return. Allows them to book a Follow-up appointment online.</p>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer ml-4 shrink-0">
                                    <input type="checkbox" name="is_followup_needed" value="1" x-model="isFollowUp" class="sr-only peer">
                                    <div class="w-11 h-6 bg-amber-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-amber-500 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white dark:bg-gray-800 after:border-gray-300 dark:border-gray-600 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-amber-600 shadow-inner"></div>
                                </label>
                            </div>
                            
                            <div x-show="isFollowUp" x-collapse class="mt-4 pt-4 border-t border-amber-200">
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
                                         <label for="followup_reason" class="block text-xs font-bold text-amber-900">Reason for Return <span class="text-rose-500">*</span></label>
                                            <div class="flex flex-wrap gap-1.5">
                                                <button type="button" @click="appendReason('Check Laboratory results')" class="text-[9px] font-bold bg-amber-100 text-amber-800 border border-amber-200 rounded px-1.5 py-0.5 hover:bg-amber-200">+ Check Labs</button>
                                                <button type="button" @click="appendReason('Monitor Vital Signs')" class="text-[9px] font-bold bg-amber-100 text-amber-800 border border-amber-200 rounded px-1.5 py-0.5 hover:bg-amber-200">+ Monitor Vitals</button>
                                                <button type="button" @click="appendReason('Follow-up Checkup')" class="text-[9px] font-bold bg-amber-100 text-amber-800 border border-amber-200 rounded px-1.5 py-0.5 hover:bg-amber-200">+ Routine Checkup</button>
                                                <button type="button" @click="appendReason('Medication Adjustment')" class="text-[9px] font-bold bg-amber-100 text-amber-800 border border-amber-200 rounded px-1.5 py-0.5 hover:bg-amber-200">+ Med Adjustment</button>
                                            </div>
                                        </div>
                                        <textarea name="followup_reason" id="followup_reason" :required="isFollowUp" x-model="reason" rows="4" placeholder="e.g. Check lab results, monitor BP" class="w-full text-sm rounded-md border-amber-300 bg-white focus:border-amber-500 focus:ring-amber-500 text-amber-900 shadow-inner"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="pt-4 border-t border-slate-100 mt-auto flex justify-end gap-3" x-data="{ showConfirm: false }">
                            <button type="button" @click="showConfirm = true" class="px-6 py-3 bg-teal-600 text-white border border-transparent rounded-lg font-extrabold shadow-lg shadow-teal-600/30 hover:bg-teal-700 hover:shadow-teal-700/40 transition flex items-center gap-2">
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
                                            <button type="submit" class="w-full py-3 bg-teal-600 text-white rounded-xl font-black hover:bg-teal-700 transition shadow-lg shadow-teal-600/20">Yes, Finalize Case</button>
                                            <button type="button" @click="showConfirm = false" class="w-full py-3 bg-slate-100 text-slate-600 rounded-xl font-bold hover:bg-slate-200 transition">Go Back & Edit</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Past Cases -->
                @foreach($pastConsultations as $past)
                    <div x-show="activeTab === 'past_{{ $past->id }}'" style="display: none;"
                         x-transition:enter="transition ease-out duration-200" 
                         x-transition:enter-start="opacity-0 translate-y-2" 
                         x-transition:enter-end="opacity-100 translate-y-0"
                         class="grow flex flex-col p-6 bg-slate-50 rounded-b-xl rounded-tr-xl overflow-y-auto">
                        
                        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-slate-200 p-6 space-y-6 relative overflow-hidden">
                            <!-- Header ribbon for past cases -->
                            <div class="absolute top-0 left-0 w-1 h-full bg-slate-300"></div>

                            <div class="flex justify-between items-center border-b border-slate-100 pb-4">
                                <div>
                                    <h2 class="text-xl font-black text-slate-800">Historical Record</h2>
                                    <p class="text-sm font-bold text-teal-600">{{ \Carbon\Carbon::parse($past->consultation_date)->format('l, F d, Y') }}</p>
                                </div>
                                <div class="text-right">
                                    <span class="block text-[10px] uppercase tracking-widest text-slate-400 font-bold mb-1">Attending Provider</span>
                                    <span class="inline-block font-semibold text-slate-700 bg-slate-100 px-3 py-1 rounded border border-slate-200">{{ $past->doctor->name ?? ($past->nurse->name ?? 'Unknown') }}</span>
                                </div>
                            </div>

                            <!-- Triaged Vitals Grid -->
                            <div>
                                <h3 class="text-[11px] font-bold text-slate-400 uppercase tracking-widest mb-3 border-b border-slate-100 pb-1">Triaged Vitals</h3>
                                <div class="grid grid-cols-2 md:grid-cols-4 gap-3 text-sm">
                                    <div class="bg-slate-50 p-2.5 rounded border border-slate-100 flex flex-col shadow-sm">
                                        <span class="text-xs text-slate-400 mb-1">BP</span>
                                        <span class="font-bold text-slate-700 text-sm">{{ $past->blood_pressure ?: ($past->preTriage?->blood_pressure ?: '--') }}</span>
                                    </div>
                                    <div class="bg-slate-50 p-2.5 rounded border border-slate-100 flex flex-col shadow-sm">
                                        <span class="text-xs text-slate-400 mb-1">Temp</span>
                                        <span class="font-bold text-sm {{ floatval($past->temperature ?: ($past->preTriage?->temperature ?? 0)) > 37.5 ? 'text-red-600' : 'text-slate-700' }}">{{ $past->temperature ?: ($past->preTriage?->temperature ?: '--') }}°C</span>
                                    </div>
                                    <div class="bg-slate-50 p-2.5 rounded border border-slate-100 flex flex-col shadow-sm">
                                        <span class="text-xs text-slate-400 mb-1">Heart Rate</span>
                                        <span class="font-bold text-slate-700 text-sm">{{ $past->heart_rate ?: ($past->preTriage?->heart_rate ?: '--') }}</span>
                                    </div>
                                    <div class="bg-slate-50 p-2.5 rounded border border-slate-100 flex flex-col shadow-sm">
                                        <span class="text-xs text-slate-400 mb-1">Resp Rate</span>
                                        <span class="font-bold text-slate-700 text-sm">{{ $past->respiratory_rate ?: ($past->preTriage?->respiratory_rate ?: '--') }}</span>
                                    </div>
                                    <div class="bg-slate-50 p-2.5 rounded border border-slate-100 flex flex-col shadow-sm">
                                        <span class="text-xs text-slate-400 mb-1">Pulse Rate</span>
                                        <span class="font-bold text-slate-700 text-sm">{{ $past->pulse_rate ?: ($past->preTriage?->pulse_rate ?: '--') }}</span>
                                    </div>
                                    <div class="bg-slate-50 p-2.5 rounded border border-slate-100 flex flex-col shadow-sm">
                                        <span class="text-xs text-slate-400 mb-1">SpO2</span>
                                        <span class="font-bold text-slate-700 text-sm">{{ $past->spo2 ?: ($past->preTriage?->spo2 ?: ($past->preTriage?->oxygen_saturation ?: '--')) }}%</span>
                                    </div>
                                    <div class="bg-slate-50 p-2.5 rounded border border-slate-100 flex flex-col shadow-sm">
                                        <span class="text-xs text-slate-400 mb-1">Weight</span>
                                        <span class="font-bold text-slate-700 text-sm">{{ $past->weight ?: ($past->preTriage?->weight ?: '--') }}kg</span>
                                    </div>
                                    <div class="bg-slate-50 p-2.5 rounded border border-slate-100 flex flex-col shadow-sm">
                                        <span class="text-xs text-slate-400 mb-1">Height</span>
                                        <span class="font-bold text-slate-700 text-sm">{{ $past->height ?: ($past->preTriage?->height ?: '--') }}cm</span>
                                    </div>
                                </div>
                            </div>

                            @if($past->preTriage?->symptoms)
                                <div>
                                    <h3 class="text-[11px] font-bold text-slate-400 uppercase tracking-widest mb-1.5 border-b border-slate-100 pb-1">Chief Complaint</h3>
                                    <p class="text-slate-700 bg-slate-50 p-3 rounded-lg text-sm italic border border-slate-100 shadow-inner">{{ $past->preTriage->symptoms }}</p>
                                </div>
                            @endif

                            <div>
                                <h3 class="text-[11px] font-bold text-teal-600 uppercase tracking-widest mb-1.5 border-b border-slate-100 pb-1">Primary Diagnosis</h3>
                                <p class="text-slate-800 font-semibold p-3 text-base border-l-4 border-teal-500 bg-teal-50/30 rounded-r-lg">{{ $past->diagnosis ?: 'No diagnosis recorded.' }}</p>
                            </div>
                            
                            @if($past->prescription)
                                <div>
                                    <h3 class="text-[11px] font-bold text-slate-400 uppercase tracking-widest mb-1.5 border-b border-slate-100 pb-1">Prescription</h3>
                                    <p class="text-slate-700 font-mono text-sm bg-slate-50 p-4 rounded-lg border border-slate-200">{{ $past->prescription }}</p>
                                </div>
                            @endif

                            @if($past->medical_notes)
                                <div>
                                    <h3 class="text-[11px] font-bold text-slate-400 uppercase tracking-widest mb-1.5 border-b border-slate-100 pb-1">Clinical Notes</h3>
                                    <p class="text-slate-600 text-sm p-3">{{ $past->medical_notes }}</p>
                                </div>
                            @endif
                            
                            @if($past->is_followup_needed)
                                <div class="bg-amber-50 border border-amber-200 rounded p-3 mt-2">
                                    <h3 class="text-[11px] font-bold text-amber-700 uppercase tracking-widest mb-1">Follow-up Instructed</h3>
                                    <p class="text-amber-800 text-sm font-semibold">Scheduled: {{ \Carbon\Carbon::parse($past->followup_date)->format('M d, Y') }}</p>
                                    @if($past->followup_reason)
                                        <p class="text-amber-700 text-xs italic mt-1">Reason: {{ $past->followup_reason }}</p>
                                    @endif
                                </div>
                            @endif

                        </div>
                    </div>
                @endforeach
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
        suggestions: [],
        showSuggestions: false,
        
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
            
            // Focus on amount input
            setTimeout(() => {
                const amountInput = this.$el.querySelector('input[placeholder^="Amount"]');
                if(amountInput) amountInput.focus();
            }, 50);
        },
        
        addPrescription() {
            if (!this.searchQuery.trim()) return;
            this.prescriptions.push({
                medicine: this.searchQuery,
                amount: this.currentAmount || '1',
                instruction: this.currentInstruction || 'As directed'
            });
            this.searchQuery = '';
            this.currentAmount = '';
            this.currentInstruction = '';
            this.suggestions = [];
            
            // Focus back on search
            setTimeout(() => {
                const searchInput = this.$el.querySelector('input[placeholder^="Search"]');
                if(searchInput) searchInput.focus();
            }, 50);
        },
        
        removePrescription(index) {
            this.prescriptions.splice(index, 1);
        }
    }));
});
</script>
@endpush
