@extends('layouts.frontdesk')

@section('title', $patient->full_name . ' - Medical File')
@section('header', 'Patient File')

@section('content')
<div class="max-w-7xl mx-auto pb-12 space-y-6">

    <!-- Breadcrumbs -->
    <nav class="flex items-center gap-2 text-xs font-semibold text-slate-500 dark:text-slate-400 py-1" aria-label="Breadcrumb">
        <a href="{{ route('frontdesk.dashboard') }}" class="hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors flex items-center gap-1.5 shrink-0">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
            <span>Home</span>
        </a>
        <span class="text-slate-300 dark:text-slate-700">/</span>
        <a href="{{ route('frontdesk.patients.index') }}" class="hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors">Patient Master Directory</a>
        <span class="text-slate-300 dark:text-slate-700">/</span>
        <span class="text-slate-800 dark:text-slate-200 font-bold truncate max-w-[200px]">{{ $patient->full_name }}</span>
    </nav>

    <!-- Header Banner -->
    <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 border border-slate-200/80 dark:border-slate-800/80 shadow-[0_8px_30px_rgb(0,0,0,0.04)] dark:shadow-none flex flex-col sm:flex-row sm:items-center justify-between gap-5">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 rounded-2xl bg-gradient-to-tr from-emerald-600 to-emerald-800 text-white font-black text-lg flex items-center justify-center shadow-md shadow-emerald-600/20 shrink-0">
                {{ $patient->initials ?? strtoupper(substr($patient->first_name, 0, 1) . substr($patient->last_name, 0, 1)) }}
            </div>
            <div>
                <div class="flex items-center gap-2 flex-wrap">
                    <h1 class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white tracking-tight">{{ $patient->full_name }}</h1>
                    <span class="font-mono text-xs font-bold bg-emerald-50 dark:bg-emerald-950/50 text-emerald-700 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800 px-2.5 py-0.5 rounded-lg">
                        {{ $patient->patient_id }}
                    </span>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold border shadow-2xs
                        @if($patient->classification == 'Pediatric') bg-orange-50 text-orange-800 border-orange-200 dark:bg-orange-950/50 dark:text-orange-300 dark:border-orange-800/60
                        @elseif($patient->classification == 'Senior Citizen') bg-purple-50 text-purple-800 border-purple-200 dark:bg-purple-950/50 dark:text-purple-300 dark:border-purple-800/60
                        @elseif($patient->classification == 'PWD') bg-blue-50 text-blue-800 border-blue-200 dark:bg-blue-950/50 dark:text-blue-300 dark:border-blue-800/60
                        @else bg-slate-100 text-slate-700 border-slate-200 dark:bg-slate-800 dark:text-slate-300 dark:border-slate-700 @endif">
                        {{ $patient->classification ?? 'Regular Adult' }}
                    </span>
                </div>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 flex items-center gap-2 flex-wrap">
                    <span>{{ $patient->dob ? $patient->dob->format('M d, Y') . ' (' . $patient->dob->age . ' yrs)' : 'Age N/A' }}</span>
                    <span>&bull;</span>
                    <span>{{ $patient->sex ?? 'N/A' }}</span>
                    <span>&bull;</span>
                    <span>Registered {{ $patient->created_at->format('M d, Y') }}</span>
                </p>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('frontdesk.registration.index', ['selected_id' => $patient->id]) }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-gradient-to-r from-emerald-600 to-emerald-700 hover:from-emerald-700 hover:to-emerald-800 text-white text-xs font-bold shadow-md shadow-emerald-600/20 active:scale-[0.98] transition cursor-pointer">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span>Process Intake / Queue</span>
            </a>
            <a href="{{ route('frontdesk.patients.index') }}" class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700 border border-slate-200 dark:border-slate-700 text-xs font-bold transition">
                <span>Back</span>
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Left Column: Demographics & Contact -->
        <div class="lg:col-span-1 space-y-6">
            
            <!-- Basic Demographics Card -->
            <div class="bg-white dark:bg-slate-900 rounded-3xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] dark:shadow-none border border-slate-200/80 dark:border-slate-800/80 overflow-hidden">
                <div class="p-4 sm:p-5 border-b border-slate-100 dark:border-slate-800 bg-slate-50/70 dark:bg-slate-900/70 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <div class="w-7 h-7 rounded-lg bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center border border-emerald-500/20">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                        </div>
                        <h3 class="text-xs font-black text-slate-900 dark:text-white uppercase tracking-wider">Demographics</h3>
                    </div>
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Permanent</span>
                </div>
                <div class="p-5 space-y-4">
                    <div class="grid grid-cols-2 gap-4 text-xs">
                        <div>
                            <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider">Date of Birth</span>
                            <span class="font-bold text-slate-900 dark:text-white mt-0.5 block">{{ $patient->dob ? $patient->dob->format('M d, Y') : 'N/A' }}</span>
                        </div>
                        
                        <div>
                            <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider">Sex</span>
                            <span class="font-bold text-slate-900 dark:text-white mt-0.5 block">{{ $patient->sex ?? 'N/A' }}</span>
                        </div>
                        
                        <div>
                            <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider">Civil Status</span>
                            <span class="font-bold text-slate-900 dark:text-white mt-0.5 block">{{ $patient->civil_status ?? 'N/A' }}</span>
                        </div>
                        
                        <div>
                            <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider">Blood Type</span>
                            <span class="font-bold text-slate-900 dark:text-white mt-0.5 block">
                                @if($patient->blood_type)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-lg font-bold bg-rose-50 dark:bg-rose-950/40 text-rose-700 dark:text-rose-300 border border-rose-200 dark:border-rose-800 text-[10px]">
                                        Type {{ $patient->blood_type }}
                                    </span>
                                @else
                                    N/A
                                @endif
                            </span>
                        </div>
                        
                        <div class="col-span-2 pt-2 border-t border-slate-100 dark:border-slate-800">
                            <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider">
                                {{ $patient->classification === 'Pediatric' ? "Guardian's PhilHealth No." : "PhilHealth No." }}
                            </span>
                            <span class="font-mono font-bold text-slate-900 dark:text-white mt-0.5 block">{{ $patient->masked_philhealth_number ?: 'Not Provided' }}</span>
                        </div>

                        <div class="col-span-2 pt-2 border-t border-slate-100 dark:border-slate-800">
                            <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider">Mother's Maiden Name</span>
                            <span class="font-bold text-slate-900 dark:text-white mt-0.5 block">{{ $patient->mothers_maiden_name ?: 'N/A' }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Contact & Background Card -->
            <div class="bg-white dark:bg-slate-900 rounded-3xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] dark:shadow-none border border-slate-200/80 dark:border-slate-800/80 overflow-hidden">
                <div class="p-4 sm:p-5 border-b border-slate-100 dark:border-slate-800 bg-slate-50/70 dark:bg-slate-900/70 flex items-center gap-2">
                    <div class="w-7 h-7 rounded-lg bg-blue-50 dark:bg-blue-950/60 text-blue-600 dark:text-blue-400 flex items-center justify-center border border-blue-500/20">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" /></svg>
                    </div>
                    <h3 class="text-xs font-black text-slate-900 dark:text-white uppercase tracking-wider">Contact &amp; Background</h3>
                </div>
                <div class="p-5 space-y-4 text-xs">
                    <div>
                        <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider">Contact Number</span>
                        <a href="tel:{{ $patient->contact_number }}" class="font-bold text-emerald-600 dark:text-emerald-400 hover:underline mt-0.5 block">{{ $patient->contact_number ?: 'N/A' }}</a>
                    </div>
                    
                    <div>
                        <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider">Physical Address</span>
                        <span class="font-semibold text-slate-800 dark:text-slate-200 mt-0.5 block">{{ $patient->address ?: 'N/A' }}</span>
                    </div>

                    <div class="pt-2 border-t border-slate-100 dark:border-slate-800">
                        <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-0.5">Educational Attainment</span>
                        <span class="font-semibold text-slate-800 dark:text-slate-200 block">{{ $patient->education ?: 'N/A' }}</span>
                    </div>

                    <div>
                        <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-0.5">Occupation</span>
                        <span class="font-semibold text-slate-800 dark:text-slate-200 block">{{ $patient->occupation ?: 'N/A' }}</span>
                    </div>
                    
                    <div>
                        <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-0.5">Religion</span>
                        <span class="font-semibold text-slate-800 dark:text-slate-200 block">{{ $patient->religion ?: 'N/A' }}</span>
                    </div>
                </div>
            </div>

        </div>

        <!-- Right Column: Consultation History Outline -->
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white dark:bg-slate-900 rounded-3xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] dark:shadow-none border border-slate-200/80 dark:border-slate-800/80 overflow-hidden flex flex-col">
                <div class="p-5 border-b border-slate-100 dark:border-slate-800 bg-slate-50/70 dark:bg-slate-900/70 flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-black text-slate-900 dark:text-white uppercase tracking-wider">Consultation History &amp; Vitals</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Previous clinic visits, nurse pre-triages, and vital statistics.</p>
                    </div>
                    <div class="bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 font-bold px-3 py-1 rounded-xl text-xs border border-emerald-200 dark:border-emerald-800">
                        {{ $patient->consultations->count() }} Visits
                    </div>
                </div>

                @if($vitalsChartData->count() > 0)
                <div class="p-5 border-b border-slate-100 dark:border-slate-800 bg-white dark:bg-slate-900" x-data="vitalsChart()">
                    <div class="flex items-center justify-between mb-4">
                        <h4 class="text-xs font-black text-slate-700 dark:text-slate-300 uppercase tracking-wider flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                            Vitals Longitudinal Trend
                        </h4>
                        <span class="text-[11px] font-semibold text-slate-400">Systolic, Diastolic &amp; Weight</span>
                    </div>
                    <div class="relative h-64 w-full">
                        <canvas id="vitalsChartCanvas"></canvas>
                    </div>
                </div>
                @endif
                
                <div class="p-0 overflow-y-auto divide-y divide-slate-100 dark:divide-slate-800/80">
                    @forelse($patient->consultations as $consultation)
                        <div class="p-5 sm:p-6 hover:bg-slate-50/70 dark:hover:bg-slate-800/40 transition relative">
                            <!-- Timeline Dot Connector -->
                            <div class="hidden md:block absolute left-6 top-8 bottom-[-24px] w-0.5 bg-slate-200 dark:bg-slate-800 z-0 {{ $loop->last ? 'hidden' : '' }}"></div>
                            <div class="hidden md:block absolute left-5 top-7 w-2.5 h-2.5 rounded-full bg-emerald-500 ring-4 ring-emerald-50 dark:ring-emerald-950 z-10"></div>
                            
                            <div class="md:pl-6 relative z-10 w-full">
                                <div class="flex flex-col sm:flex-row sm:items-center justify-between mb-3 w-full gap-2">
                                    <div>
                                        <h4 class="font-bold text-slate-900 dark:text-white text-sm">Consultation Visit: {{ \Carbon\Carbon::parse($consultation->consultation_date)->format('F d, Y') }}</h4>
                                        <p class="text-xs text-slate-500 dark:text-slate-400">Logged at {{ $consultation->created_at->format('h:i A') }}</p>
                                    </div>
                                    <span class="text-[11px] font-bold px-2.5 py-0.5 rounded-full border shadow-2xs w-fit {{ $consultation->status == 'done' ? 'bg-emerald-50 dark:bg-emerald-950/50 text-emerald-700 dark:text-emerald-300 border-emerald-200 dark:border-emerald-800' : 'bg-amber-50 dark:bg-amber-950/50 text-amber-700 dark:text-amber-300 border-amber-200 dark:border-amber-800' }}">
                                        {{ ucfirst(str_replace('_', ' ', $consultation->status)) }}
                                    </span>
                                </div>
                                
                                <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-8 gap-2.5 bg-slate-50 dark:bg-slate-800/60 border border-slate-200/80 dark:border-slate-700/80 rounded-2xl p-3.5 shadow-2xs mb-3 w-full">
                                    <div class="text-center">
                                        <p class="text-[9px] uppercase font-bold text-slate-400 tracking-wider">BP</p>
                                        <p class="font-bold text-slate-800 dark:text-white text-xs mt-0.5">{{ $consultation->blood_pressure ?: ($consultation->preTriage?->blood_pressure ?: '--') }}</p>
                                    </div>
                                    <div class="text-center">
                                        <p class="text-[9px] uppercase font-bold text-slate-400 tracking-wider">Temp</p>
                                        <p class="font-bold text-slate-800 dark:text-white text-xs mt-0.5">{{ $consultation->temperature ?: ($consultation->preTriage?->temperature ?: '--') }}°C</p>
                                    </div>
                                    <div class="text-center">
                                        <p class="text-[9px] uppercase font-bold text-slate-400 tracking-wider">Heart Rate</p>
                                        <p class="font-bold text-slate-800 dark:text-white text-xs mt-0.5">{{ $consultation->heart_rate ?: ($consultation->preTriage?->heart_rate ?: '--') }}</p>
                                    </div>
                                    <div class="text-center">
                                        <p class="text-[9px] uppercase font-bold text-slate-400 tracking-wider">Resp Rate</p>
                                        <p class="font-bold text-slate-800 dark:text-white text-xs mt-0.5">{{ $consultation->respiratory_rate ?: ($consultation->preTriage?->respiratory_rate ?: '--') }}</p>
                                    </div>
                                    <div class="text-center">
                                        <p class="text-[9px] uppercase font-bold text-slate-400 tracking-wider">Pulse Rate</p>
                                        <p class="font-bold text-slate-800 dark:text-white text-xs mt-0.5">{{ $consultation->pulse_rate ?: ($consultation->preTriage?->pulse_rate ?: '--') }}</p>
                                    </div>
                                    <div class="text-center">
                                        <p class="text-[9px] uppercase font-bold text-slate-400 tracking-wider">SpO2</p>
                                        <p class="font-bold text-slate-800 dark:text-white text-xs mt-0.5">{{ $consultation->spo2 ?: ($consultation->preTriage?->spo2 ?: ($consultation->preTriage?->oxygen_saturation ?: '--')) }}%</p>
                                    </div>
                                    <div class="text-center">
                                        <p class="text-[9px] uppercase font-bold text-slate-400 tracking-wider">Weight</p>
                                        <p class="font-bold text-slate-800 dark:text-white text-xs mt-0.5">{{ $consultation->weight ?: ($consultation->preTriage?->weight ?: '--') }}kg</p>
                                    </div>
                                    <div class="text-center">
                                        <p class="text-[9px] uppercase font-bold text-slate-400 tracking-wider">Height</p>
                                        <p class="font-bold text-slate-800 dark:text-white text-xs mt-0.5">{{ $consultation->height ?: ($consultation->preTriage?->height ?: '--') }}cm</p>
                                    </div>
                                </div>

                                @if($consultation->symptoms || $consultation->preTriage?->symptoms)
                                <div class="bg-white dark:bg-slate-800 border border-slate-200/80 dark:border-slate-700/80 rounded-xl p-3 text-xs">
                                    <span class="font-bold text-slate-500 dark:text-slate-400">Recorded Symptoms:</span>
                                    <span class="text-slate-800 dark:text-slate-200 ml-1">{{ $consultation->symptoms ?: $consultation->preTriage?->symptoms }}</span>
                                </div>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="p-12 text-center text-slate-400 w-full flex flex-col justify-center items-center">
                            <div class="w-14 h-14 rounded-2xl bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-slate-400 mb-3">
                                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                            </div>
                            <p class="text-sm font-bold text-slate-700 dark:text-slate-300">No consultations recorded</p>
                            <p class="text-xs text-slate-400 mt-0.5">This citizen does not have any recorded clinic visits yet.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
        
    </div>

</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('vitalsChart', () => ({
        init() {
            const rawData = {!! json_encode($vitalsChartData ?? []) !!};
            if (rawData.length === 0) return;

            const labels = rawData.map(d => d.date);
            const systolicData = rawData.map(d => d.systolic);
            const diastolicData = rawData.map(d => d.diastolic);
            const weightData = rawData.map(d => d.weight);

            const ctx = document.getElementById('vitalsChartCanvas');
            if (!ctx) return;

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [
                        {
                            label: 'Systolic BP',
                            data: systolicData,
                            borderColor: 'rgba(239, 68, 68, 1)',
                            backgroundColor: 'rgba(239, 68, 68, 0.1)',
                            borderWidth: 2,
                            tension: 0.3,
                            yAxisID: 'y'
                        },
                        {
                            label: 'Diastolic BP',
                            data: diastolicData,
                            borderColor: 'rgba(245, 158, 11, 1)',
                            backgroundColor: 'rgba(245, 158, 11, 0.1)',
                            borderWidth: 2,
                            tension: 0.3,
                            yAxisID: 'y'
                        },
                        {
                            label: 'Weight (kg)',
                            data: weightData,
                            borderColor: 'rgba(16, 185, 129, 1)',
                            backgroundColor: 'rgba(16, 185, 129, 0.1)',
                            borderWidth: 2,
                            tension: 0.3,
                            yAxisID: 'y1'
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: {
                        mode: 'index',
                        intersect: false,
                    },
                    scales: {
                        y: {
                            type: 'linear',
                            display: true,
                            position: 'left',
                            title: {
                                display: true,
                                text: 'Blood Pressure (mmHg)'
                            },
                            suggestedMin: 60,
                            suggestedMax: 180
                        },
                        y1: {
                            type: 'linear',
                            display: true,
                            position: 'right',
                            title: {
                                display: true,
                                text: 'Weight (kg)'
                            },
                            grid: {
                                drawOnChartArea: false
                            }
                        }
                    }
                }
            });
        }
    }));
});
</script>
@endpush
