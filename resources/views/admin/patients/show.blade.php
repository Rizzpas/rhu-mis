@extends('layouts.admin')

@section('header', 'Comprehensive Patient Record')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8" x-data="{ 
    selectedCases: [],
    medicalCaseIds: {{ json_encode($patient->medicalCases->pluck('id')->map(fn($id) => (string)$id)) }},
    showOverrideModal: false
}">

    
    <!-- Top Action Bar -->
    <div class="flex items-center justify-between mb-8 print:hidden">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.patients.index') }}" class="p-2 rounded-xl bg-white dark:bg-gray-800 shadow-sm border border-gray-200 dark:border-gray-700 hover:bg-gray-50 transition">
                <svg class="w-6 h-6 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </a>
            <div>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $patient->full_name }}</h2>
                <p class="text-sm text-gray-500 font-mono tracking-tight">{{ $patient->patient_id }}</p>
            </div>
        </div>
        <div class="flex items-center gap-3">
            @if($isUnmasked)
                <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xl bg-amber-100 dark:bg-amber-950/60 border border-amber-300 dark:border-amber-700 text-amber-800 dark:text-amber-300 text-xs font-bold">
                    <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                    <span>PHI Override Active</span>
                    <a href="{{ route('admin.patients.show', $patient) }}" class="underline text-[11px] ml-1 text-amber-900 dark:text-amber-200">Re-mask</a>
                </div>
            @else
                <button type="button" @click="showOverrideModal = true" class="px-4 py-2 rounded-xl bg-indigo-50 hover:bg-indigo-100 border border-indigo-200 dark:border-indigo-800 text-indigo-700 dark:text-indigo-300 text-xs font-bold transition flex items-center gap-1.5 cursor-pointer">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                    <span>Authorized PHI Override</span>
                </button>
            @endif

            <template x-if="selectedCases.length > 0">
                <button @click="window.open(`{{ route('admin.patients.print', $patient) }}?cases=${selectedCases.join(',')}`, '_blank')" class="px-5 py-2 rounded-xl bg-teal-600 text-white font-bold shadow-lg hover:bg-teal-700 transition flex items-center gap-2 animate-pulse">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    Print Selected (<span x-text="selectedCases.length"></span>)
                </button>
            </template>
            <button @click="window.open('{{ route('admin.patients.print', $patient) }}', '_blank')" x-show="selectedCases.length === 0" class="px-5 py-2 rounded-xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-200 font-bold shadow-sm hover:bg-gray-50 transition flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                Print All Records
            </button>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
        
        <!-- Sidebar: Demographics -->
        <div class="lg:col-span-1 space-y-6 print:hidden">
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                <div class="p-6 bg-gradient-to-br from-blue-600 to-indigo-700 text-white">
                    <div class="w-20 h-20 rounded-2xl bg-white/20 backdrop-blur-md flex items-center justify-center text-3xl font-bold mb-4 border border-white/30">
                        {{ substr($patient->first_name, 0, 1) }}{{ substr($patient->last_name, 0, 1) }}
                    </div>
                    <h3 class="text-lg font-bold">Profile Details</h3>
                    <p class="text-blue-100 text-xs mt-1 uppercase font-bold tracking-wider">{{ $patient->classification }}</p>
                </div>
                
                <div class="p-6 space-y-6">
                    <div>
                        <p class="text-[10px] uppercase font-bold text-gray-400 dark:text-gray-500 mb-1">Contact Information</p>
                        <p class="text-sm font-bold text-gray-900 dark:text-white">{{ $patient->contact_number ?: 'None provided' }}</p>
                        <p class="text-xs text-gray-500 mt-1">{{ $patient->address ?: 'No address on file' }}</p>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-[10px] uppercase font-bold text-gray-400 mb-1">Sex</p>
                            <p class="text-sm font-bold text-gray-900 dark:text-white">{{ $patient->sex }}</p>
                        </div>
                        <div>
                            <p class="text-[10px] uppercase font-bold text-gray-400 mb-1">Age</p>
                            <p class="text-sm font-bold text-gray-900 dark:text-white">{{ $patient->dob ? $patient->dob->age . ' yrs' : 'N/A' }}</p>
                        </div>
                    </div>

                    <div>
                        <p class="text-[10px] uppercase font-bold text-gray-400 mb-1">PhilHealth Number</p>
                        <p class="text-sm font-bold text-gray-900 dark:text-white">{{ $patient->philhealth_number ?: 'Not Registered' }}</p>
                    </div>

                    <div class="pt-4 border-t border-gray-100 dark:border-gray-700">
                        <p class="text-[10px] uppercase font-bold text-gray-400 mb-2">Internal Metadata</p>
                        <div class="space-y-2">
                            <div class="flex justify-between text-xs">
                                <span class="text-gray-500">Registered</span>
                                <span class="font-medium text-gray-900 dark:text-white">{{ $patient->created_at->format('M d, Y') }}</span>
                            </div>
                            <div class="flex justify-between text-xs">
                                <span class="text-gray-500">Total Visits</span>
                                <span class="font-medium text-gray-900 dark:text-white">{{ $patient->consultations->count() }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content: History & Cases -->
        <div class="lg:col-span-3 space-y-8">
            
            <!-- Statistics Row -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 print:hidden">
                @php
                    $latestVitals = $patient->consultations->whereNotNull('blood_pressure')->first();
                @endphp
                <div class="bg-rose-50 dark:bg-rose-900/20 p-4 rounded-2xl border border-rose-100 dark:border-rose-800">
                    <p class="text-[10px] font-bold text-rose-600 dark:text-rose-400 uppercase tracking-widest mb-1">Latest BP</p>
                    <p class="text-xl font-black text-rose-900 dark:text-rose-200">{{ $isUnmasked ? ($latestVitals->blood_pressure ?? '--/--') : '••/••' }}</p>
                </div>
                <div class="bg-emerald-50 dark:bg-emerald-900/20 p-4 rounded-2xl border border-emerald-100 dark:border-emerald-800">
                    <p class="text-[10px] font-bold text-emerald-600 dark:text-emerald-400 uppercase tracking-widest mb-1">Latest Temp</p>
                    <p class="text-xl font-black text-emerald-900 dark:text-emerald-200">{{ $isUnmasked ? (($latestVitals->temperature ?? '--') . '°C') : '••°C' }}</p>
                </div>
                <div class="bg-blue-50 dark:bg-blue-900/20 p-4 rounded-2xl border border-blue-100 dark:border-blue-800">
                    <p class="text-[10px] font-bold text-blue-600 dark:text-blue-400 uppercase tracking-widest mb-1">Total Cases</p>
                    <p class="text-xl font-black text-blue-900 dark:text-blue-200">{{ $patient->medicalCases->count() }}</p>
                </div>
                <div class="bg-purple-50 dark:bg-purple-900/20 p-4 rounded-2xl border border-purple-100 dark:border-purple-800">
                    <p class="text-[10px] font-bold text-purple-600 dark:text-purple-400 uppercase tracking-widest mb-1">Last Visit</p>
                    <p class="text-xl font-black text-purple-900 dark:text-purple-200">{{ $patient->consultations->first() ? $patient->consultations->first()->created_at->diffForHumans() : 'Never' }}</p>
                </div>
            </div>

            <!-- Detailed Medical Cases -->
            <div class="space-y-6">
                @if(!$isUnmasked)
                <!-- DPA Compliance Banner -->
                <div class="p-4 rounded-2xl bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-800 flex items-start gap-3 print:hidden">
                    <div class="p-2 rounded-xl bg-amber-100 dark:bg-amber-900/60 text-amber-700 dark:text-amber-300 shrink-0">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                    </div>
                    <div class="text-xs">
                        <h4 class="font-extrabold text-amber-900 dark:text-amber-200">Data Privacy Act (RA 10173) Protection Active</h4>
                        <p class="text-amber-700 dark:text-amber-400 mt-0.5">
                            Clinical diagnoses, physician notes, prescription specifics, and laboratory analyte values are masked for non-clinical administrative accounts. Use <strong>Authorized PHI Override</strong> only when conducting an official audit or emergency investigation.
                        </p>
                    </div>
                </div>
                @endif

                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white flex items-center gap-2">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        Full Medical Case History
                    </h3>
                    <div class="flex items-center gap-2 print:hidden">
                        <button @click="selectedCases = medicalCaseIds" class="text-xs font-bold text-blue-600 hover:underline">Select All</button>
                        <span class="text-gray-300">|</span>
                        <button @click="selectedCases = []" class="text-xs font-bold text-gray-500 hover:underline">Clear</button>
                    </div>
                </div>

                <!-- Scrollable Cases Container with Vertical Limit -->
                <div class="space-y-4 max-h-[820px] overflow-y-auto pr-1.5 custom-scrollbar print:max-h-none print:overflow-visible">
                @forelse($patient->medicalCases as $index => $case)
                <div x-data="{ expanded: {{ $index === 0 ? 'true' : 'false' }} }" 
                    class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden transition-all duration-300"
                    :class="selectedCases.length > 0 && !selectedCases.includes('{{ $case->id }}') ? 'print:hidden' : ''">
                    <!-- Case Header (Clickable) -->
                    <div class="p-6 border-b border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 flex flex-col md:flex-row md:items-center justify-between gap-4 transition">
                        <div class="flex items-center gap-4">
                            <!-- Checkbox for Printing -->
                            <div class="print:hidden">
                                <input type="checkbox" value="{{ $case->id }}" x-model="selectedCases" 
                                    class="w-5 h-5 text-teal-600 border-gray-300 rounded focus:ring-teal-500 cursor-pointer">
                            </div>
                            <div @click="expanded = !expanded" class="flex items-center gap-3 cursor-pointer group">
                                <div class="p-2 bg-blue-100 dark:bg-blue-900/40 rounded-lg transform transition-transform duration-300" :class="expanded ? 'rotate-90' : ''">
                                    <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                </div>
                                <div>
                                    <h4 class="font-bold text-gray-900 dark:text-white group-hover:text-blue-600 transition">Case Reference: {{ $case->case_number }}</h4>
                                    <p class="text-xs text-gray-500">{{ $case->closed_at->format('F d, Y @ h:i A') }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center gap-4">
                            <div class="text-xs text-gray-500 hidden md:block">
                                Managed by: <span class="font-bold text-gray-800 dark:text-gray-200">{{ $case->consultation->doctor->name ?? 'N/A' }}</span>
                            </div>
                            <span class="text-xs font-bold px-2 py-1 bg-gray-200 dark:bg-gray-700 text-gray-600 dark:text-gray-300 rounded print:hidden" x-text="expanded ? 'Collapse' : 'Expand'"></span>
                        </div>
                    </div>
                    
                    <!-- Expandable Content -->
                    <div x-show="expanded" x-collapse x-cloak>
                        <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div>
                                <p class="text-[10px] uppercase font-bold text-blue-500 tracking-widest mb-3">Clinical Findings</p>
                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-xs font-bold text-gray-400 mb-1">Diagnosis</label>
                                        @if($isUnmasked)
                                            <div class="p-4 bg-gray-50 dark:bg-gray-900 rounded-xl text-sm text-gray-800 dark:text-gray-200 border border-gray-100 dark:border-gray-800 italic">
                                                {{ $case->diagnosis }}
                                            </div>
                                        @else
                                            <div class="p-3 bg-slate-100 dark:bg-slate-800/80 rounded-xl text-xs font-mono text-slate-500 dark:text-slate-400 border border-slate-200 dark:border-slate-700 flex items-center gap-2">
                                                <svg class="w-4 h-4 text-slate-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                                <span>[RESTRICTED — CLINICAL DIAGNOSIS MASKED UNDER RA 10173]</span>
                                            </div>
                                        @endif
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold text-gray-400 mb-1">Medical Notes</label>
                                        @if($isUnmasked)
                                            <p class="text-sm text-gray-700 dark:text-gray-300">{{ $case->consultation->medical_notes ?? 'No additional notes provided.' }}</p>
                                        @else
                                            <p class="text-xs font-mono text-slate-400 italic">[Protected Clinical Notes — Licensed Healthcare Personnel Only]</p>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div>
                                <p class="text-[10px] uppercase font-bold text-emerald-500 tracking-widest mb-3">Prescription & Plan</p>
                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-xs font-bold text-gray-400 mb-2">Medications Prescribed</label>
                                        @php
                                            $parsedPrescriptions = null;
                                            $prescriptionText = $case->prescription;
                                            
                                            // Try to parse as JSON first
                                            if ($prescriptionText && !in_array(trim($prescriptionText), ['', '[]', 'null'])) {
                                                $decoded = json_decode($prescriptionText, true);
                                                if (json_last_error() === JSON_ERROR_NONE && is_array($decoded) && count($decoded) > 0) {
                                                    $parsedPrescriptions = $decoded;
                                                }
                                            }

                                            // Fallback: check structured prescription items from the Prescription model
                                            if (!$parsedPrescriptions && $case->consultation && $case->consultation->prescriptionRecord) {
                                                $items = $case->consultation->prescriptionRecord->items;
                                                if ($items && $items->count() > 0) {
                                                    $parsedPrescriptions = $items->map(function($item) {
                                                        return [
                                                            'medicine' => $item->medicine_name,
                                                            'instruction' => trim(($item->dosage ?? '') . ' ' . ($item->frequency ?? '') . ' ' . ($item->duration ?? '')),
                                                            'amount' => $item->quantity ? $item->quantity . ' pcs' : '',
                                                        ];
                                                    })->toArray();
                                                }
                                            }

                                            // Determine if there's any meaningful text prescription (not empty/brackets)
                                            $hasTextPrescription = $prescriptionText 
                                                && !in_array(trim($prescriptionText), ['', '[]', 'null', '{}']) 
                                                && !$parsedPrescriptions;
                                        @endphp
                                        
                                        @if($isUnmasked)
                                            @if($parsedPrescriptions)
                                                <div class="space-y-2">
                                                    @foreach($parsedPrescriptions as $med)
                                                        <div class="p-3 bg-emerald-50 dark:bg-emerald-900/20 rounded-xl border border-emerald-100 dark:border-emerald-800 flex justify-between items-center">
                                                            <div>
                                                                <p class="text-sm font-bold text-emerald-900 dark:text-emerald-200">{{ $med['medicine'] ?? 'Unknown Medicine' }}</p>
                                                                <p class="text-xs text-emerald-700 dark:text-emerald-400 mt-0.5">{{ $med['instruction'] ?? '' }}</p>
                                                            </div>
                                                            <span class="text-xs font-bold px-2 py-1 bg-emerald-200 dark:bg-emerald-800 text-emerald-900 dark:text-emerald-100 rounded">{{ $med['amount'] ?? '' }}</span>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @elseif($hasTextPrescription)
                                                <div class="p-4 bg-emerald-50 dark:bg-emerald-900/20 rounded-xl text-sm text-emerald-900 dark:text-emerald-200 border border-emerald-100 dark:border-emerald-800 font-mono">
                                                    {!! nl2br(e($prescriptionText)) !!}
                                                </div>
                                            @else
                                                <div class="p-4 bg-gray-50 dark:bg-gray-800/50 rounded-xl text-sm text-gray-500 dark:text-gray-400 border border-gray-100 dark:border-gray-700 italic">
                                                    No medication prescribed.
                                                </div>
                                            @endif
                                        @else
                                            <div class="p-3 bg-slate-100 dark:bg-slate-800/80 rounded-xl text-xs font-mono text-slate-500 dark:text-slate-400 border border-slate-200 dark:border-slate-700 flex items-center justify-between">
                                                <span class="flex items-center gap-2">
                                                    <svg class="w-4 h-4 text-slate-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                                    <span>[Confidential Prescription Record Masked]</span>
                                                </span>
                                                <span class="text-[10px] uppercase font-bold text-slate-400">Clinical Data</span>
                                            </div>
                                        @endif
                                    </div>
                                    @if($case->consultation->is_followup_needed)
                                    <div class="p-4 bg-amber-50 dark:bg-amber-900/20 rounded-xl border border-amber-100 dark:border-amber-800">
                                        <p class="text-xs font-bold text-amber-800 dark:text-amber-400 flex items-center gap-1">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                            Follow-up Scheduled
                                        </p>
                                        <p class="text-sm font-bold text-amber-900 dark:text-amber-200 mt-1">{{ \Carbon\Carbon::parse($case->consultation->followup_date)->format('M d, Y') }}</p>
                                        <p class="text-xs text-amber-700 dark:text-amber-500 mt-1">{{ $case->consultation->followup_reason }}</p>
                                    </div>
                                    @endif
                                </div>
                            </div>

                            <div class="md:col-span-2 pt-4 border-t border-gray-100 dark:border-gray-700">
                                <p class="text-[10px] uppercase font-bold text-gray-400 tracking-widest mb-3">Vitals Snapshot at Time of Case</p>
                                @if($isUnmasked)
                                    <div class="grid grid-cols-4 md:grid-cols-8 gap-4 text-center">
                                        @foreach($case->vitals_snapshot as $key => $val)
                                        <div>
                                            <p class="text-[9px] font-bold text-gray-400 uppercase">{{ $key }}</p>
                                            <p class="text-sm font-bold text-gray-800 dark:text-white">{{ $val ?: '--' }}</p>
                                        </div>
                                        @endforeach
                                    </div>
                                @else
                                    <p class="text-xs font-mono text-slate-400 italic">[Vitals recorded by clinical staff at triage — Clinical clearance required to view]</p>
                                @endif
                            </div>
                            
                            <!-- Ancillary / Laboratory Results Section -->
                            @php
                                $doneRequests = $case->consultation ? $case->consultation->ancillaryRequests->where('status', 'Done') : collect();
                                $doneRequestIds = $doneRequests->pluck('id')->map(fn($id) => (string)$id)->values();
                            @endphp

                            @if($doneRequests->count() > 0)
                            <div class="md:col-span-2 pt-4 border-t border-gray-100 dark:border-gray-700" 
                                 x-data="{ 
                                     selectedLabs: {{ json_encode($doneRequestIds) }},
                                     allDoneIds: {{ json_encode($doneRequestIds) }},
                                     toggleAllLabs() {
                                         if (this.selectedLabs.length === this.allDoneIds.length) {
                                             this.selectedLabs = [];
                                         } else {
                                             this.selectedLabs = [...this.allDoneIds];
                                         }
                                     },
                                     printSelectedLabs() {
                                         if (this.selectedLabs.length === 0) return;
                                         window.open('{{ route('admin.patients.ancillary.print', $patient) }}?ids=' + this.selectedLabs.join(','), '_blank');
                                     }
                                 }">
                                <div class="flex flex-wrap items-center justify-between gap-2 mb-3">
                                    <div class="flex items-center gap-2">
                                        <svg class="w-4 h-4 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/>
                                        </svg>
                                        <p class="text-[10px] uppercase font-bold text-indigo-600 dark:text-indigo-400 tracking-widest">
                                            Diagnostic Results (Lab / Radiology) • {{ $doneRequests->count() }} {{ \Illuminate\Support\Str::plural('Test', $doneRequests->count()) }}
                                        </p>
                                    </div>

                                    @if($doneRequests->count() > 1)
                                    <div class="flex items-center gap-2 print:hidden">
                                        <button type="button" @click="toggleAllLabs()" class="text-xs font-bold text-indigo-600 dark:text-indigo-400 hover:underline cursor-pointer">
                                            <span x-text="selectedLabs.length === allDoneIds.length ? 'Deselect All' : 'Select All'"></span>
                                        </button>
                                        <span class="text-gray-300">|</span>
                                        <button type="button" @click="printSelectedLabs()" :disabled="selectedLabs.length === 0" 
                                                class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-700 disabled:opacity-40 disabled:cursor-not-allowed shadow-xs transition cursor-pointer">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                                            <span>Print Selected Results (<span x-text="selectedLabs.length"></span>)</span>
                                        </button>
                                    </div>
                                    @elseif($doneRequests->count() === 1)
                                    <div class="print:hidden">
                                        <button type="button" @click="window.open('{{ route('admin.ancillary.print', $doneRequests->first()->id) }}', '_blank')" 
                                                class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-700 shadow-xs transition cursor-pointer">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                                            <span>Print Diagnostic Report</span>
                                        </button>
                                    </div>
                                    @endif
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    @foreach($doneRequests as $req)
                                    <div class="p-4 bg-indigo-50 dark:bg-indigo-900/20 border border-indigo-100 dark:border-indigo-800 rounded-xl relative transition hover:border-indigo-300">
                                        <div class="flex justify-between items-start mb-2 gap-2">
                                            <div class="flex items-start gap-2.5">
                                                <input type="checkbox" value="{{ $req->id }}" x-model="selectedLabs" 
                                                       class="mt-1 w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500 cursor-pointer print:hidden">
                                                <div>
                                                    <div class="flex items-center gap-1.5">
                                                        <span class="text-[10px] uppercase font-bold px-2 py-0.5 rounded {{ $req->type === 'Radiology' ? 'bg-indigo-200 text-indigo-900' : 'bg-purple-200 text-purple-900' }}">
                                                            {{ $req->type }}
                                                        </span>
                                                        <span class="text-[10px] text-gray-500">Ref: #{{ $req->id }}</span>
                                                    </div>
                                                    <h4 class="font-bold text-sm text-indigo-950 dark:text-indigo-200 mt-1">{{ $req->test_name }}</h4>
                                                </div>
                                            </div>
                                            <span class="text-xs px-2 py-0.5 rounded font-bold bg-emerald-100 text-emerald-800 border border-emerald-300">
                                                Done
                                            </span>
                                        </div>

                                        @if($req->result_data)
                                            @php
                                                $testLower = strtolower($req->test_name);
                                                $isRad = $req->type === 'Radiology' || str_contains($testLower, 'x-ray') || str_contains($testLower, 'xray') || isset($req->result_data['findings']);
                                            @endphp
                                            <div class="mt-2 space-y-1.5 text-xs bg-white/70 dark:bg-slate-900/50 p-2.5 rounded-lg border border-indigo-100 dark:border-indigo-900/40">
                                                @if($isRad && isset($req->result_data['findings']))
                                                    <div>
                                                        <span class="font-bold text-slate-700 dark:text-slate-300 block text-[10px] uppercase">Findings:</span>
                                                        <p class="text-slate-900 dark:text-white leading-relaxed line-clamp-3">{{ $req->result_data['findings'] }}</p>
                                                    </div>
                                                    @if(isset($req->result_data['impression']))
                                                    <div class="pt-1 border-t border-indigo-100 dark:border-indigo-900/30">
                                                        <span class="font-bold text-emerald-800 dark:text-emerald-400 block text-[10px] uppercase">Impression:</span>
                                                        <p class="font-bold text-slate-900 dark:text-white">{{ $req->result_data['impression'] }}</p>
                                                    </div>
                                                    @endif
                                                @else
                                                    @foreach($req->result_data as $key => $val)
                                                        @if(is_string($val) && !in_array($key, ['remarks', 'exam_view']) && !empty($val))
                                                            <div class="flex justify-between border-b border-indigo-50 dark:border-indigo-900/20 pb-0.5">
                                                                <span class="text-indigo-700/80 dark:text-indigo-400">{{ ucwords(str_replace('_', ' ', $key)) }}:</span>
                                                                <span class="font-bold text-slate-900 dark:text-white">{{ $val }}</span>
                                                            </div>
                                                        @endif
                                                    @endforeach
                                                @endif
                                            </div>
                                        @endif

                                        {{-- Action buttons for this specific test --}}
                                        <div class="mt-3 print:hidden flex flex-wrap items-center gap-2">
                                            <button type="button" @click="window.open('{{ route('admin.ancillary.print', $req->id) }}', '_blank')" 
                                                    class="inline-flex items-center gap-1.5 text-xs font-bold text-indigo-700 bg-white dark:bg-slate-800 hover:bg-indigo-50 border border-indigo-300 dark:border-indigo-700 px-3 py-1.5 rounded-lg shadow-2xs transition cursor-pointer">
                                                <svg class="w-3.5 h-3.5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                                                Print Official {{ $req->type === 'Radiology' ? 'X-Ray' : 'Lab' }} Report
                                            </button>

                                            @if($req->result_file_path)
                                                <a href="{{ Storage::url($req->result_file_path) }}" target="_blank" class="inline-flex items-center gap-1 text-xs font-bold text-slate-700 dark:text-slate-300 bg-white dark:bg-slate-800 hover:bg-slate-50 border border-slate-300 dark:border-slate-700 px-2.5 py-1.5 rounded-lg transition">
                                                    <svg class="w-3.5 h-3.5 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" /></svg>
                                                    View Attachment Scan
                                                </a>
                                                <button type="button" onclick="printAttachment('{{ Storage::url($req->result_file_path) }}')" class="inline-flex items-center gap-1 text-xs font-bold text-slate-700 dark:text-slate-300 bg-white dark:bg-slate-800 hover:bg-slate-50 border border-slate-300 dark:border-slate-700 px-2.5 py-1.5 rounded-lg transition cursor-pointer">
                                                    <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                                                    Print Scan
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
                @empty
                <div class="bg-white dark:bg-gray-800 rounded-2xl p-12 text-center border border-dashed border-gray-300 dark:border-gray-700">
                    <p class="text-gray-500">No medical cases have been finalized for this patient yet.</p>
                </div>
                @endforelse
                </div> <!-- Closes scrollable cases container -->
            </div>
        </div>
    </div>

    <!-- Authorized PHI Override Modal -->
    <div x-show="showOverrideModal" x-cloak class="fixed inset-0 z-[120] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm"
         x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
        <div @click.away="showOverrideModal = false" class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-md overflow-hidden border border-slate-200 dark:border-slate-700"
             x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100">
            <form action="{{ route('admin.patients.show', $patient) }}" method="GET" class="p-6">
                <input type="hidden" name="unmask" value="1">
                <div class="flex items-start gap-3 mb-4">
                    <div class="p-2.5 bg-amber-100 dark:bg-amber-900/50 text-amber-700 dark:text-amber-300 rounded-xl shrink-0">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-base font-extrabold text-slate-900 dark:text-white">Authorized PHI Access Request</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Under RA 10173, viewing unmasked patient clinical records is tracked and logged permanently in the system audit trail.</p>
                    </div>
                </div>

                <div class="space-y-3 mb-5">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Audit Justification / Purpose <span class="text-rose-500">*</span></label>
                        <select name="override_reason" required class="w-full text-xs rounded-xl border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 text-slate-900 dark:text-white p-2.5 focus:ring-amber-500 focus:border-amber-500">
                            <option value="">-- Select Purpose --</option>
                            <option value="Official DOH / Regulatory Compliance Audit">Official DOH / Regulatory Compliance Audit</option>
                            <option value="Legal Subpoena / Authorized Law Enforcement Inquiry">Legal Subpoena / Authorized Law Enforcement Inquiry</option>
                            <option value="Technical Database Troubleshooting / System QA Audit">Technical Database Troubleshooting / System QA Audit</option>
                            <option value="Medical Director Direct Written Authorization">Medical Director Direct Written Authorization</option>
                        </select>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100 dark:border-slate-700">
                    <button type="button" @click="showOverrideModal = false" class="px-4 py-2 rounded-xl bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-200 text-xs font-bold cursor-pointer">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-2 rounded-xl bg-amber-600 hover:bg-amber-700 text-white text-xs font-bold shadow-sm transition cursor-pointer">
                        Acknowledge & Access PHI
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function printAttachment(url) {
    const extension = url.split('.').pop().toLowerCase();
    if (['jpg', 'jpeg', 'png', 'gif', 'webp'].includes(extension)) {
        window.printIsolated(`
            <div style="text-align: center; padding: 12px 0;">
                <h3 style="font-size: 13pt; font-weight: 800; margin-bottom: 12px; color: #0f172a; text-transform: uppercase;">Diagnostic Attachment Scan</h3>
                <p style="font-size: 9pt; color: #64748b; margin-bottom: 16px;">Patient: {{ e($patient->full_name) }} ({{ e($patient->patient_id) }})</p>
                <img src="${url}" style="max-width: 100%; max-height: 220mm; object-fit: contain; border: 1px solid #cbd5e1; border-radius: 4px; display: block; margin: 0 auto;" alt="Diagnostic Attachment">
            </div>
        `, { title: 'Diagnostic Attachment — {{ $patient->full_name }}' });
    } else {
        // Fallback for PDFs or external documents
        const printWindow = window.open(url, '_blank');
        if (printWindow) {
            printWindow.onload = function() {
                setTimeout(() => { printWindow.print(); }, 500);
            };
        }
    }
}
</script>
@endpush
