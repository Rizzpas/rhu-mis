@extends('layouts.frontdesk')

@section('header', 'Patient Information')

@section('content')
<div class="max-w-7xl mx-auto pb-10">

    <!-- Header Actions -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <a href="{{ route('frontdesk.patients.index') }}" class="text-teal-600 hover:text-teal-800 flex items-center gap-1 font-medium text-sm transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Back to Patient List
            </a>
            <h2 class="text-2xl font-extrabold text-gray-900 dark:text-white mt-2">{{ $patient->full_name }}</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">Patient ID: <span class="font-mono bg-gray-100 dark:bg-gray-900 px-1.5 py-0.5 rounded">{{ $patient->patient_id }}</span></p>
        </div>
        <div class="flex gap-3">
            {{-- "New Visit" button removed as the workflow now begins at the Vitals/Triage station for walk-ins --}}
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Left Column: Demographics -->
        <div class="lg:col-span-1 space-y-6">
            
            <!-- Basic Demographics Card -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="p-4 border-b border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 flex items-center justify-between">
                    <h3 class="font-bold text-gray-800 dark:text-white">Demographics</h3>
                    <span class="bg-teal-100 text-teal-800 text-xs px-2.5 py-1 rounded-full font-bold border border-teal-200 uppercase tracking-wider">
                        {{ $patient->classification ?? 'Regular' }}
                    </span>
                </div>
                <div class="p-5 space-y-4">
                    <div class="grid grid-cols-2 gap-y-4 gap-x-4 text-sm">
                        
                        <div class="col-span-2 md:col-span-1">
                            <span class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Date of Birth</span>
                            <span class="font-medium text-gray-900 dark:text-white">{{ $patient->dob ? $patient->dob->format('M d, Y') : 'N/A' }} 
                                @if($patient->dob)<span class="text-gray-500 dark:text-gray-400">({{ $patient->dob->age }} yrs)</span>@endif
                            </span>
                        </div>
                        
                        <div class="col-span-2 md:col-span-1">
                            <span class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Sex</span>
                            <span class="font-medium text-gray-900 dark:text-white">{{ $patient->sex ?? 'N/A' }}</span>
                        </div>
                        
                        <div class="col-span-2 md:col-span-1">
                            <span class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Civil Status</span>
                            <span class="font-medium text-gray-900 dark:text-white">{{ $patient->civil_status ?? 'N/A' }}</span>
                        </div>
                        
                        <div class="col-span-2 md:col-span-1">
                            <span class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Blood Type</span>
                            <span class="font-medium text-gray-900 dark:text-white">
                                @if($patient->blood_type)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold bg-red-100 text-red-800 border border-red-200">
                                        {{ $patient->blood_type }}
                                    </span>
                                @else
                                    N/A
                                @endif
                            </span>
                        </div>
                        
                        <div class="col-span-2">
                            <span class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">
                                {{ $patient->classification === 'Pediatric' ? "Guardian's PhilHealth No." : "PhilHealth No." }}
                            </span>
                            <span class="font-medium text-gray-900 dark:text-white">{{ $patient->masked_philhealth_number ?: 'Not Provided' }}</span>
                        </div>
                        
                    </div>
                </div>
            </div>

            <!-- Contact & Background Card -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="p-4 border-b border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-900">
                    <h3 class="font-bold text-gray-800 dark:text-white">Contact & Background</h3>
                </div>
                <div class="p-5 space-y-4">
                    <div class="grid grid-cols-1 gap-y-4 text-sm">
                        
                        <div>
                            <span class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Contact Number</span>
                            <a href="tel:{{ $patient->contact_number }}" class="font-medium text-teal-600 hover:text-teal-800">{{ $patient->contact_number ?: 'N/A' }}</a>
                        </div>
                        
                        <div>
                            <span class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Physical Address</span>
                            <span class="font-medium text-gray-900 dark:text-white">{{ $patient->address ?: 'N/A' }}</span>
                        </div>

                        <div class="pt-3 border-t border-gray-100 dark:border-gray-700">
                            <span class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase mb-1">Education</span>
                            <span class="font-medium text-gray-900 dark:text-white">{{ $patient->education ?: 'N/A' }}</span>
                        </div>

                        <div>
                            <span class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase mb-1">Occupation</span>
                            <span class="font-medium text-gray-900 dark:text-white">{{ $patient->occupation ?: 'N/A' }}</span>
                        </div>
                        
                        <div>
                            <span class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase mb-1">Religion</span>
                            <span class="font-medium text-gray-900 dark:text-white">{{ $patient->religion ?: 'N/A' }}</span>
                        </div>
                        
                    </div>
                </div>
            </div>

        </div>

        <!-- Right Column: Consultation History Outline -->
        <div class="lg:col-span-2 flex flex-col h-full">
            <div class="bg-white dark:bg-gray-800 md:rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 flex-1 overflow-hidden flex flex-col">
                <div class="p-4 md:p-6 border-b border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 flex items-center justify-between shrink-0">
                    <div>
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">Consultation History</h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Previous visits, symptoms, and vital signs.</p>
                    </div>
                    <div class="bg-teal-100 text-teal-800 font-bold px-3 py-1 rounded-lg text-sm shadow-sm border border-teal-200">
                        {{ $patient->consultations->count() }} Visits
                    </div>
                </div>
                
                <div class="p-0 overflow-y-auto flex-1">
                    @forelse($patient->consultations as $consultation)
                        <div class="p-6 border-b border-gray-100 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 dark:bg-gray-900 transition relative">
                            <!-- Timeline Dot Connector -->
                            <div class="hidden md:block absolute left-6 top-8 bottom-[-24px] w-0.5 bg-gray-200 z-0 {{ $loop->last ? 'hidden' : '' }}"></div>
                            <div class="hidden md:block absolute left-5 top-7 w-2.5 h-2.5 rounded-full bg-teal-500 ring-4 ring-teal-50 z-10"></div>
                            
                            <div class="md:pl-6 relative z-10 w-full">
                                <div class="flex flex-col sm:flex-row sm:items-center justify-between mb-3 w-full gap-2">
                                    <h4 class="font-bold text-gray-900 dark:text-white text-base">Wait Queued: {{ \Carbon\Carbon::parse($consultation->consultation_date)->format('F d, Y') }}</h4>
                                    <span class="text-xs font-bold px-2 py-1 rounded w-fit {{ $consultation->status == 'done' ? 'bg-green-100 dark:bg-green-900/40 text-green-800 dark:text-green-400' : 'bg-yellow-100 text-yellow-800' }}">
                                        {{ ucfirst(str_replace('_', ' ', $consultation->status)) }}
                                    </span>
                                </div>
                                
                                <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-8 gap-3 bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-lg p-4 shadow-sm mb-4 w-full">
                                    <div>
                                        <p class="text-[10px] uppercase font-bold text-gray-400 tracking-wider">BP</p>
                                        <p class="font-bold text-slate-700 dark:text-white text-sm">{{ $consultation->blood_pressure ?: ($consultation->preTriage?->blood_pressure ?: '--') }}</p>
                                    </div>
                                    <div>
                                        <p class="text-[10px] uppercase font-bold text-gray-400 tracking-wider">Temp</p>
                                        <p class="font-bold text-slate-700 dark:text-white text-sm">{{ $consultation->temperature ?: ($consultation->preTriage?->temperature ?: '--') }}°C</p>
                                    </div>
                                    <div>
                                        <p class="text-[10px] uppercase font-bold text-gray-400 tracking-wider">Heart Rate</p>
                                        <p class="font-bold text-slate-700 dark:text-white text-sm">{{ $consultation->heart_rate ?: ($consultation->preTriage?->heart_rate ?: '--') }}</p>
                                    </div>
                                    <div>
                                        <p class="text-[10px] uppercase font-bold text-gray-400 tracking-wider">Resp Rate</p>
                                        <p class="font-bold text-slate-700 dark:text-white text-sm">{{ $consultation->respiratory_rate ?: ($consultation->preTriage?->respiratory_rate ?: '--') }}</p>
                                    </div>
                                    <div>
                                        <p class="text-[10px] uppercase font-bold text-gray-400 tracking-wider">Pulse Rate</p>
                                        <p class="font-bold text-slate-700 dark:text-white text-sm">{{ $consultation->pulse_rate ?: ($consultation->preTriage?->pulse_rate ?: '--') }}</p>
                                    </div>
                                    <div>
                                        <p class="text-[10px] uppercase font-bold text-gray-400 tracking-wider">SpO2</p>
                                        <p class="font-bold text-slate-700 dark:text-white text-sm">{{ $consultation->spo2 ?: ($consultation->preTriage?->spo2 ?: ($consultation->preTriage?->oxygen_saturation ?: '--')) }}%</p>
                                    </div>
                                    <div>
                                        <p class="text-[10px] uppercase font-bold text-gray-400 tracking-wider">Weight</p>
                                        <p class="font-bold text-slate-700 dark:text-white text-sm">{{ $consultation->weight ?: ($consultation->preTriage?->weight ?: '--') }}kg</p>
                                    </div>
                                    <div>
                                        <p class="text-[10px] uppercase font-bold text-gray-400 tracking-wider">Height</p>
                                        <p class="font-bold text-slate-700 dark:text-white text-sm">{{ $consultation->height ?: ($consultation->preTriage?->height ?: '--') }}cm</p>
                                    </div>
                                </div>
                                
                            </div>
                        </div>
                    @empty
                        <div class="p-12 text-center text-gray-500 dark:text-gray-400 w-full h-full flex flex-col justify-center items-center">
                            <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                            <p class="text-base font-medium">No consultations recorded.</p>
                            <p class="text-sm mt-1">This patient does not have any recorded visits yet.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
        
    </div>

</div>
@endsection
