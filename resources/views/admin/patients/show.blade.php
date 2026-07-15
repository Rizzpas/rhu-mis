@extends('layouts.admin')

@section('header', 'Comprehensive Patient Record')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8" x-data="{ 
    selectedCases: [],
    medicalCaseIds: {{ json_encode($patient->medicalCases->pluck('id')->map(fn($id) => (string)$id)) }}
}">
    <style>
        @media print {
            .print\:hidden { display: none !important; }
            body { background: white !important; }
            .bg-gray-50, .bg-emerald-50, .bg-blue-50, .bg-rose-50, .bg-purple-50, .bg-amber-50 { background-color: transparent !important; }
            .border { border-color: #eee !important; }
            .shadow-sm, .shadow-lg { shadow: none !important; }
            [x-show] { display: block !important; } /* Ensure expanded content prints */
            
            /* Medical record styling */
            .medical-header {
                display: block !important;
                border-bottom: 2px solid #333;
                margin-bottom: 2rem;
                padding-bottom: 1rem;
            }
        }
        .medical-header { display: none; }
    </style>

    <!-- Medical Header (Print Only) -->
    <div class="medical-header">
        <div class="flex justify-between items-start mb-6">
            <div>
                <h1 class="text-3xl font-black uppercase text-emerald-700">Rural Health Unit - Silang</h1>
                <p class="text-sm font-bold text-gray-600 uppercase tracking-widest">Medical Record & Clinical History</p>
                <div class="mt-4 flex gap-8">
                    <div>
                        <p class="text-[10px] uppercase font-bold text-gray-400">Patient Name</p>
                        <p class="text-lg font-bold">{{ $patient->full_name }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] uppercase font-bold text-gray-400">Patient ID</p>
                        <p class="text-lg font-mono">{{ $patient->patient_id }}</p>
                    </div>
                </div>
            </div>
            <div class="text-right">
                <img src="{{ asset('assets/images/logo.png') }}" class="w-20 h-20 ml-auto mb-2">
                <p class="text-xs font-bold">{{ now()->format('F d, Y') }}</p>
                <p class="text-[10px] text-gray-500">Authorized System Export</p>
            </div>
        </div>
    </div>
    
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
        <div class="flex gap-3">
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
                    <p class="text-xl font-black text-rose-900 dark:text-rose-200">{{ $latestVitals->blood_pressure ?? '--/--' }}</p>
                </div>
                <div class="bg-emerald-50 dark:bg-emerald-900/20 p-4 rounded-2xl border border-emerald-100 dark:border-emerald-800">
                    <p class="text-[10px] font-bold text-emerald-600 dark:text-emerald-400 uppercase tracking-widest mb-1">Latest Temp</p>
                    <p class="text-xl font-black text-emerald-900 dark:text-emerald-200">{{ $latestVitals->temperature ?? '--' }}°C</p>
                </div>
                <div class="bg-blue-50 dark:bg-blue-900/20 p-4 rounded-2xl border border-blue-100 dark:border-blue-800">
                    <p class="text-[10px] font-bold text-blue-600 dark:text-blue-400 uppercase tracking-widest mb-1">Total Cases</p>
                    <p class="text-xl font-black text-blue-900 dark:text-blue-200">{{ $patient->medicalCases->count() }}</p>
                </div>
                <div class="bg-purple-50 dark:bg-purple-900/20 p-4 rounded-2xl border border-purple-100 dark:border-purple-800">
                    <p class="text-[10px] font-bold text-purple-600 dark:text-purple-400 uppercase tracking-widest mb-1">Last Visit</p>
                    <p class="text-xl font-black text-purple-900 dark:text-purple-200">{{ $patient->consultations->first() ? $patient->consultations->first()->consultation_date->diffForHumans() : 'Never' }}</p>
                </div>
            </div>

            <!-- Detailed Medical Cases -->
            <div class="space-y-6">
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
                                        <div class="p-4 bg-gray-50 dark:bg-gray-900 rounded-xl text-sm text-gray-800 dark:text-gray-200 border border-gray-100 dark:border-gray-800 italic">
                                            {{ $case->diagnosis }}
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold text-gray-400 mb-1">Medical Notes</label>
                                        <p class="text-sm text-gray-700 dark:text-gray-300">{{ $case->consultation->medical_notes ?? 'No additional notes provided.' }}</p>
                                    </div>
                                </div>
                            </div>

                            <div>
                                <p class="text-[10px] uppercase font-bold text-emerald-500 tracking-widest mb-3">Prescription & Plan</p>
                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-xs font-bold text-gray-400 mb-1">Medications</label>
                                        <div class="p-4 bg-emerald-50 dark:bg-emerald-900/20 rounded-xl text-sm text-emerald-900 dark:text-emerald-200 border border-emerald-100 dark:border-emerald-800 font-mono">
                                            {!! nl2br(e($case->prescription ?? 'No medication prescribed.')) !!}
                                        </div>
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
                                <div class="grid grid-cols-4 md:grid-cols-8 gap-4 text-center">
                                    @foreach($case->vitals_snapshot as $key => $val)
                                    <div>
                                        <p class="text-[9px] font-bold text-gray-400 uppercase">{{ $key }}</p>
                                        <p class="text-sm font-bold text-gray-800 dark:text-white">{{ $val ?: '--' }}</p>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                <div class="bg-white dark:bg-gray-800 rounded-2xl p-12 text-center border border-dashed border-gray-300 dark:border-gray-700">
                    <p class="text-gray-500">No medical cases have been finalized for this patient yet.</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
