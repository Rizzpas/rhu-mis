@extends('layouts.doctor')

@section('header', 'Patient Information')

@section('content')
<div class="max-w-7xl mx-auto pb-10">

    <!-- Header Actions -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <a href="{{ route('doctor.dashboard') }}" class="text-teal-600 hover:text-teal-800 flex items-center gap-1 font-medium text-sm transition print:hidden">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Back to Dashboard
            </a>
            <h2 class="text-2xl font-extrabold text-gray-900 dark:text-white mt-2">{{ $patient->full_name }}</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">Patient ID: <span class="font-mono bg-gray-100 dark:bg-gray-900 px-1.5 py-0.5 rounded">{{ $patient->patient_id }}</span></p>
        </div>
        <div class="flex items-center gap-2 print:hidden">
            <button type="button" onclick="window.print()" class="px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 text-gray-700 dark:text-gray-200 text-xs font-bold rounded-xl shadow-xs hover:bg-gray-50 dark:hover:bg-gray-700 flex items-center gap-2 transition cursor-pointer" title="Print Comprehensive Medical Summary">
                <svg class="w-4 h-4 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                <span>Print Medical Summary</span>
            </button>
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
                            <span class="font-medium text-gray-900 dark:text-white">{{ $patient->house_street ? $patient->house_street . ', ' : '' }}{{ $patient->barangay }}{{ $patient->city_province ? ', ' . $patient->city_province : '' }}</span>
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

                @if($vitalsChartData->count() > 0)
                <div class="p-4 md:p-6 border-b border-gray-100 dark:border-gray-700 bg-white" x-data="vitalsChart()">
                    <h4 class="text-sm font-bold text-gray-700 mb-4 uppercase tracking-wider">Vitals Trend</h4>
                    <div class="relative h-64 w-full">
                        <canvas id="vitalsChartCanvas"></canvas>
                    </div>
                </div>
                @endif
                
                <div class="p-0 overflow-y-auto flex-1">
                    @forelse($patient->consultations as $consultation)
                        <div class="p-6 border-b border-gray-100 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 dark:bg-gray-900 transition relative">
                            <!-- Timeline Dot Connector -->
                            <div class="hidden md:block absolute left-6 top-8 bottom-[-24px] w-0.5 bg-gray-200 z-0 {{ $loop->last ? 'hidden' : '' }}"></div>
                            <div class="hidden md:block absolute left-5 top-7 w-2.5 h-2.5 rounded-full bg-teal-500 ring-4 ring-teal-50 z-10"></div>
                            
                            <div class="md:pl-6 relative z-10 w-full space-y-4">
                                <!-- Encounter Header Bar -->
                                <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-3 border-b border-gray-100 dark:border-gray-700 gap-2">
                                    <div>
                                        <h4 class="font-bold text-gray-900 dark:text-white text-base flex items-center gap-2">
                                            <span>Encounter: {{ \Carbon\Carbon::parse($consultation->consultation_date)->format('F d, Y') }}</span>
                                            @if($consultation->consultation_start_time)
                                                <span class="text-xs font-normal text-gray-500">({{ \Carbon\Carbon::parse($consultation->consultation_start_time)->format('h:i A') }})</span>
                                            @endif
                                        </h4>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                                            Attending Clinician: <span class="font-semibold text-gray-700 dark:text-gray-300">{{ $consultation->doctor?->name ?? ($consultation->nurse?->name ?? 'Clinical Provider') }}</span>
                                        </p>
                                    </div>
                                    <span class="text-xs font-bold px-2.5 py-1 rounded-full w-fit {{ in_array($consultation->status, ['completed', 'done']) ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800' : ($consultation->status === 'cancelled' ? 'bg-rose-100 text-rose-800 dark:bg-rose-900/40 dark:text-rose-300' : 'bg-amber-100 text-amber-800') }}">
                                        {{ ucfirst(str_replace('_', ' ', $consultation->status)) }}
                                    </span>
                                </div>
                                
                                <!-- Vitals Grid -->
                                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 bg-gray-50/70 dark:bg-gray-900/40 border border-gray-100 dark:border-gray-700 rounded-xl p-3.5 text-xs">
                                    <div>
                                        <p class="text-[10px] uppercase font-bold text-gray-400 tracking-wider">Queue No.</p>
                                        <p class="font-bold text-teal-700 dark:text-teal-400 text-sm">{{ $consultation->queue_number }}</p>
                                    </div>
                                    <div>
                                        <p class="text-[10px] uppercase font-bold text-gray-400 tracking-wider">BP / Pulse</p>
                                        <p class="font-semibold text-gray-800 dark:text-white text-xs">
                                            {{ $consultation->blood_pressure ?: '--' }}
                                            @if($consultation->pulse_rate) / {{ $consultation->pulse_rate }} bpm @endif
                                        </p>
                                    </div>
                                    <div>
                                        <p class="text-[10px] uppercase font-bold text-gray-400 tracking-wider">Temp / SpO2</p>
                                        <p class="font-semibold text-gray-800 dark:text-white text-xs">
                                            @if($consultation->temperature) {{ $consultation->temperature }}°C @else -- @endif
                                            @if($consultation->spo2) / {{ $consultation->spo2 }}% @endif
                                        </p>
                                    </div>
                                    <div>
                                        <p class="text-[10px] uppercase font-bold text-gray-400 tracking-wider">Weight / BMI</p>
                                        <p class="font-semibold text-gray-800 dark:text-white text-xs flex items-center gap-1.5">
                                            @if($consultation->weight) {{ $consultation->weight }}kg @else -- @endif
                                            @if($consultation->bmi)
                                                <span class="px-1 py-0.2 rounded text-[10px] text-white {{ $consultation->bmi < 18.5 ? 'bg-blue-500' : ($consultation->bmi < 25 ? 'bg-emerald-600' : 'bg-rose-500') }}">
                                                    {{ $consultation->bmi }}
                                                </span>
                                            @endif
                                        </p>
                                    </div>
                                </div>

                                <!-- Chief Complaint -->
                                @if($consultation->preTriage?->symptoms)
                                    <div>
                                        <h5 class="text-[10px] uppercase font-bold text-gray-400 tracking-wider mb-1">Chief Complaint</h5>
                                        <p class="text-xs text-gray-800 dark:text-gray-200 bg-white dark:bg-gray-800 p-2.5 rounded-lg border border-gray-200 dark:border-gray-700 italic">
                                            "{{ $consultation->preTriage->symptoms }}"
                                        </p>
                                    </div>
                                @endif

                                <!-- Primary Diagnosis -->
                                <div>
                                    <h5 class="text-[10px] uppercase font-bold text-teal-700 dark:text-teal-400 tracking-wider mb-1">Primary Diagnosis</h5>
                                    <div class="text-xs font-bold text-gray-900 dark:text-white p-3 rounded-lg border-l-4 border-teal-500 bg-teal-50/50 dark:bg-teal-950/20">
                                        {{ $consultation->diagnosis ?: 'No formal diagnosis recorded.' }}
                                    </div>
                                </div>

                                <!-- Prescriptions -->
                                @if($consultation->prescription)
                                    <div>
                                        <h5 class="text-[10px] uppercase font-bold text-gray-400 tracking-wider mb-1">Prescribed Medications</h5>
                                        <div class="text-xs font-mono text-gray-800 dark:text-gray-200 bg-gray-50 dark:bg-gray-900/60 p-3 rounded-lg border border-gray-200 dark:border-gray-700 whitespace-pre-line">
                                            {{ $consultation->prescription }}
                                        </div>
                                    </div>
                                @endif

                                <!-- Clinical Notes & Signed Addenda -->
                                @if($consultation->medical_notes)
                                    <div>
                                        <h5 class="text-[10px] uppercase font-bold text-gray-400 tracking-wider mb-1">Clinical Notes & Addenda</h5>
                                        <div class="text-xs text-gray-700 dark:text-gray-300 bg-gray-50/80 dark:bg-gray-900/40 p-3 rounded-lg border border-gray-200 dark:border-gray-700 whitespace-pre-line leading-relaxed">
                                            {{ $consultation->medical_notes }}
                                        </div>
                                    </div>
                                @endif

                                <!-- Diagnostic / Ancillary Requests & Results -->
                                @if($consultation->ancillaryRequests && $consultation->ancillaryRequests->count() > 0)
                                    <div>
                                        <h5 class="text-[10px] uppercase font-bold text-indigo-600 dark:text-indigo-400 tracking-wider mb-1.5">Laboratory & Diagnostic Procedures</h5>
                                        <div class="space-y-2">
                                            @foreach($consultation->ancillaryRequests as $ancillary)
                                                <div class="bg-indigo-50/40 dark:bg-indigo-950/20 border border-indigo-100 dark:border-indigo-900/50 rounded-lg p-3 text-xs flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                                                    <div>
                                                        <div class="flex items-center gap-2">
                                                            <span class="font-bold text-gray-900 dark:text-white">{{ $ancillary->test_name }}</span>
                                                            <span class="text-[10px] text-gray-500">({{ $ancillary->type }})</span>
                                                            @if($ancillary->amended_at)
                                                                <span class="text-[10px] font-black bg-amber-100 text-amber-800 px-1.5 py-0.2 rounded border border-amber-300">AMENDED</span>
                                                            @endif
                                                        </div>
                                                        @if($ancillary->amendment_reason)
                                                            <p class="text-[11px] text-amber-800 dark:text-amber-300 mt-0.5"><em>Correction note: {{ $ancillary->amendment_reason }}</em></p>
                                                        @endif
                                                    </div>
                                                    <div class="flex items-center gap-2 shrink-0">
                                                        <span class="px-2 py-0.5 text-[10px] font-bold rounded {{ $ancillary->status === 'Done' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                                                            {{ $ancillary->status }}
                                                        </span>
                                                        @if($ancillary->status === 'Done')
                                                            <a href="{{ route('lab.ancillary.print', $ancillary->id) }}" target="_blank" class="text-indigo-600 dark:text-indigo-400 hover:underline font-bold text-[11px] print:hidden">
                                                                Print Report ↗
                                                            </a>
                                                        @endif
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif

                                <!-- Follow-up Instructions -->
                                @if($consultation->is_followup_needed)
                                    <div class="bg-amber-50/70 dark:bg-amber-950/30 border border-amber-200 dark:border-amber-800/60 rounded-lg p-3 text-xs flex items-center justify-between">
                                        <div>
                                            <span class="font-bold text-amber-800 dark:text-amber-300 uppercase text-[10px] tracking-wider block">Follow-up Scheduled</span>
                                            <span class="font-semibold text-gray-900 dark:text-white">{{ \Carbon\Carbon::parse($consultation->followup_date)->format('F d, Y') }}</span>
                                            @if($consultation->followup_reason)
                                                <span class="text-gray-600 dark:text-gray-400 ml-1">({{ $consultation->followup_reason }})</span>
                                            @endif
                                        </div>
                                    </div>
                                @endif

                                <!-- Append Clinical Addendum (SYS-005 & SYS-017) -->
                                @if(in_array($consultation->status, ['completed', 'done']) && ($consultation->doctor_id === auth()->id() || $consultation->nurse_id === auth()->id() || in_array(auth()->user()->role, ['admin', 'super_admin'])))
                                    <div x-data="{ showAddendumForm: false }" class="pt-3 border-t border-gray-100 dark:border-gray-700 print:hidden">
                                        <button type="button" @click="showAddendumForm = !showAddendumForm" class="text-xs font-bold text-amber-700 dark:text-amber-400 hover:text-amber-800 flex items-center gap-1.5 transition">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                            <span x-text="showAddendumForm ? 'Close Addendum Form' : '+ Append Official Clinical Addendum'"></span>
                                        </button>

                                        <div x-show="showAddendumForm" x-collapse class="mt-2.5">
                                            <form action="{{ route('doctor.consultation.addendum', $consultation->id) }}" method="POST" class="bg-amber-50/80 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-800 rounded-xl p-3.5">
                                                @csrf
                                                <label class="block text-xs font-bold text-amber-900 dark:text-amber-200 mb-1">Sign Clinical Addendum</label>
                                                <p class="text-[11px] text-amber-700 dark:text-amber-300 mb-2">Appends a signed, timestamped entry under {{ auth()->user()->formatted_name ?? auth()->user()->name }} to this finalized chart.</p>
                                                <textarea name="addendum_text" required rows="2" placeholder="Document clinical notes, test review comments, or patient clarification..." class="w-full text-xs rounded-lg border-amber-300 dark:border-amber-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white p-2.5 focus:ring-amber-500 focus:border-amber-500"></textarea>
                                                <div class="flex justify-end gap-2 mt-2">
                                                    <button type="button" @click="showAddendumForm = false" class="px-3 py-1.5 text-xs text-gray-600 dark:text-gray-400 hover:text-gray-800 font-semibold">Cancel</button>
                                                    <button type="submit" class="px-3 py-1.5 bg-amber-600 hover:bg-amber-700 text-white text-xs font-bold rounded-lg shadow-sm transition">Sign & Append</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                @endif
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
                            borderColor: 'rgba(239, 68, 68, 1)', // Red
                            backgroundColor: 'rgba(239, 68, 68, 0.1)',
                            borderWidth: 2,
                            tension: 0.3,
                            yAxisID: 'y'
                        },
                        {
                            label: 'Diastolic BP',
                            data: diastolicData,
                            borderColor: 'rgba(245, 158, 11, 1)', // Amber
                            backgroundColor: 'rgba(245, 158, 11, 0.1)',
                            borderWidth: 2,
                            tension: 0.3,
                            yAxisID: 'y'
                        },
                        {
                            label: 'Weight (kg)',
                            data: weightData,
                            borderColor: 'rgba(14, 165, 233, 1)', // Sky blue
                            backgroundColor: 'rgba(14, 165, 233, 0.1)',
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
