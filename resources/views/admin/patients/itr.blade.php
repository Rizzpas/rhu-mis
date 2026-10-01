<x-print-layout 
    title="Individual Treatment Record (ITR)"
    subtitle="Official Clinical Encounter & Patient Diagnostic History"
    :period="$cases->isNotEmpty() ? $cases->last()->created_at->format('M d, Y') . ' — ' . $cases->first()->created_at->format('M d, Y') : 'Current Record'"
    :generatedBy="auth()->check() ? auth()->user()->name : 'Authorized Medical Officer'"
>
    {{-- Patient Demographics Block --}}
    <section class="avoid-break mb-6 border border-slate-300 rounded-lg overflow-hidden bg-white shadow-xs">
        <div class="bg-slate-100 border-b border-slate-300 px-4 py-2 flex items-center justify-between">
            <h3 class="text-xs font-black uppercase tracking-wider text-slate-800 flex items-center gap-2">
                <svg class="w-4 h-4 text-teal-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
                Patient Demographics & Identification
            </h3>
            <span class="font-mono text-xs font-bold text-teal-800 bg-teal-50 border border-teal-200 px-2 py-0.5 rounded">
                {{ $patient->patient_id }}
            </span>
        </div>
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 p-4 text-xs">
            <div class="col-span-2">
                <span class="block text-[10px] font-bold uppercase tracking-wider text-slate-500">Full Name</span>
                <span class="text-sm font-black text-slate-900">{{ $patient->full_name }}</span>
            </div>
            <div>
                <span class="block text-[10px] font-bold uppercase tracking-wider text-slate-500">Classification</span>
                <span class="font-bold text-slate-900">{{ $patient->classification ?? 'General Public' }}</span>
            </div>
            <div>
                <span class="block text-[10px] font-bold uppercase tracking-wider text-slate-500">Civil Status / Blood Type</span>
                <span class="font-semibold text-slate-900">{{ $patient->civil_status ?? 'Single' }} • {{ $patient->blood_type ?? 'N/A' }}</span>
            </div>
            <div>
                <span class="block text-[10px] font-bold uppercase tracking-wider text-slate-500">Date of Birth / Age</span>
                <span class="font-semibold text-slate-900">
                    {{ $patient->dob ? \Carbon\Carbon::parse($patient->dob)->format('M d, Y') : 'N/A' }} 
                    ({{ $patient->dob ? \Carbon\Carbon::parse($patient->dob)->age : '—' }} yrs)
                </span>
            </div>
            <div>
                <span class="block text-[10px] font-bold uppercase tracking-wider text-slate-500">Sex</span>
                <span class="font-semibold text-slate-900">{{ ucfirst($patient->sex ?? 'Unspecified') }}</span>
            </div>
            <div>
                <span class="block text-[10px] font-bold uppercase tracking-wider text-slate-500">Contact Number</span>
                <span class="font-semibold text-slate-900">{{ $patient->contact_number ?: 'None provided' }}</span>
            </div>
            <div>
                <span class="block text-[10px] font-bold uppercase tracking-wider text-slate-500">PhilHealth Number</span>
                <span class="font-semibold text-slate-900">{{ $patient->philhealth_number ?: ($patient->guardian_philhealth ?: 'Not Registered') }}</span>
            </div>
            <div class="col-span-2">
                <span class="block text-[10px] font-bold uppercase tracking-wider text-slate-500">Complete Address</span>
                <span class="font-semibold text-slate-900">{{ $patient->address ?: 'Silang, Cavite' }}</span>
            </div>
            @if($patient->guardian_name || $patient->guardian_first_name)
            <div class="col-span-2">
                <span class="block text-[10px] font-bold uppercase tracking-wider text-slate-500">Guardian / Emergency Contact</span>
                <span class="font-semibold text-slate-900">
                    {{ $patient->guardian_full_name }} ({{ $patient->guardian_relation ?? 'Guardian' }}) 
                    @if($patient->guardian_contact) • {{ $patient->guardian_contact }} @endif
                </span>
            </div>
            @endif
        </div>
    </section>

    {{-- Clinical Encounters & Medical Cases Loop --}}
    <section class="space-y-6">
        <div class="flex items-center justify-between pb-1.5 border-b border-slate-300 mb-4">
            <h3 class="text-xs font-black uppercase tracking-wider text-slate-800 flex items-center gap-2">
                <svg class="w-4 h-4 text-teal-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                </svg>
                Clinical Encounters ({{ $cases->count() }} Recorded {{ $cases->count() === 1 ? 'Case' : 'Cases' }})
            </h3>
            <span class="text-[10px] text-slate-500 font-semibold">Chronological Order (Latest First)</span>
        </div>

        @forelse($cases as $index => $case)
            @php
                // Vital signs extraction from snapshot or relations
                $vitals = is_array($case->vitals_snapshot) ? $case->vitals_snapshot : (json_decode($case->vitals_snapshot, true) ?? []);
                $bp = $vitals['bp'] ?? ($case->preTriage->blood_pressure ?? ($case->consultation->blood_pressure ?? null));
                $temp = $vitals['temp'] ?? ($case->preTriage->temperature ?? ($case->consultation->temperature ?? null));
                $wt = $vitals['wt'] ?? ($case->preTriage->weight ?? ($case->consultation->weight ?? null));
                $ht = $vitals['ht'] ?? ($case->preTriage->height ?? ($case->consultation->height ?? null));
                $hr = $vitals['hr'] ?? ($case->preTriage->heart_rate ?? ($case->consultation->heart_rate ?? null));
                $rr = $vitals['rr'] ?? ($case->preTriage->respiratory_rate ?? ($case->consultation->respiratory_rate ?? null));
                $pr = $vitals['pr'] ?? ($case->preTriage->pulse_rate ?? ($case->consultation->pulse_rate ?? null));
                $spo2 = $vitals['spo2'] ?? ($case->preTriage->spo2 ?? ($case->consultation->spo2 ?? null));

                // Doctor / Clinician name
                $attendingDoctor = $case->consultation && $case->consultation->doctor 
                    ? $case->consultation->doctor->formatted_name 
                    : ($case->consultation && $case->consultation->nurse ? $case->consultation->nurse->name . ' (Clinical Nurse)' : 'Attending Medical Officer');

                // Complaint / Symptoms
                $complaint = $case->preTriage->symptoms 
                    ?? ($case->consultation && $case->consultation->preTriage ? $case->consultation->preTriage->symptoms : null);

                // Prescription parsing
                $rawPrescription = $case->prescription;
                $parsedMedications = [];
                $hasStructuredPrescription = false;
                $plainPrescriptionText = '';

                if ($rawPrescription && !in_array(trim($rawPrescription), ['', '[]', 'null', '{}'])) {
                    $decoded = json_decode($rawPrescription, true);
                    if (json_last_error() === JSON_ERROR_NONE && is_array($decoded) && count($decoded) > 0) {
                        $parsedMedications = $decoded;
                        $hasStructuredPrescription = true;
                    } else {
                        $plainPrescriptionText = $rawPrescription;
                    }
                }

                // Fallback to Consultation Prescription Record items
                if (!$hasStructuredPrescription && empty($plainPrescriptionText) && $case->consultation && $case->consultation->prescriptionRecord) {
                    $recordItems = $case->consultation->prescriptionRecord->items;
                    if ($recordItems && $recordItems->count() > 0) {
                        $parsedMedications = $recordItems->map(function($item) {
                            return [
                                'medicine' => $item->medicine_name,
                                'amount' => $item->quantity ? $item->quantity . ' pcs' : ($item->dosage ?? ''),
                                'instruction' => trim(($item->dosage ?? '') . ' • ' . ($item->frequency ?? '') . ' • ' . ($item->duration ?? '')),
                                'quantity' => $item->quantity ?? '',
                                'isOtc' => false,
                            ];
                        })->toArray();
                        $hasStructuredPrescription = true;
                    }
                }
            @endphp

            <article class="avoid-break border border-slate-300 rounded-lg bg-white overflow-hidden mb-6 shadow-xs">
                
                {{-- Case Header Strip --}}
                <div class="bg-slate-100 border-b border-slate-300 px-4 py-2.5 flex flex-wrap items-center justify-between gap-2 text-xs">
                    <div class="flex items-center gap-3">
                        <span class="font-black text-slate-900 text-sm">{{ $case->case_number }}</span>
                        <span class="text-slate-400">•</span>
                        <span class="font-semibold text-slate-700">Date: {{ $case->created_at->format('F d, Y — h:i A') }}</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="px-2.5 py-0.5 rounded text-[10px] font-bold uppercase tracking-wide bg-emerald-100 text-emerald-800 border border-emerald-300">
                            {{ $case->consultation && $case->consultation->queue_number ? 'Queue: ' . $case->consultation->queue_number : 'Encounter #' . ($case->consultation_id ?? $case->id) }}
                        </span>
                        <span class="px-2.5 py-0.5 rounded text-[10px] font-bold uppercase tracking-wide bg-slate-200 text-slate-800 border border-slate-300">
                            Completed Visit
                        </span>
                    </div>
                </div>

                <div class="p-4 space-y-4 text-xs">
                    
                    {{-- Vital Signs Strip --}}
                    @if($bp || $temp || $hr || $rr || $spo2 || $wt || $ht)
                    <div class="bg-slate-50 border border-slate-200 rounded-lg p-3">
                        <div class="text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-2 flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5 text-rose-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                            </svg>
                            Triage & Vital Signs Snapshot
                        </div>
                        <div class="grid grid-cols-4 sm:grid-cols-7 gap-2 text-center text-slate-800">
                            <div class="bg-white p-2 rounded border border-slate-200 shadow-2xs">
                                <span class="block text-[9px] text-slate-500 font-bold uppercase">Blood Press.</span>
                                <span class="font-mono font-black text-xs text-slate-900">{{ $bp ?: '—' }}</span>
                            </div>
                            <div class="bg-white p-2 rounded border border-slate-200 shadow-2xs">
                                <span class="block text-[9px] text-slate-500 font-bold uppercase">Temp</span>
                                <span class="font-mono font-black text-xs text-slate-900">{{ $temp ? $temp . '°C' : '—' }}</span>
                            </div>
                            <div class="bg-white p-2 rounded border border-slate-200 shadow-2xs">
                                <span class="block text-[9px] text-slate-500 font-bold uppercase">Heart Rate</span>
                                <span class="font-mono font-black text-xs text-slate-900">{{ $hr ? $hr . ' bpm' : '—' }}</span>
                            </div>
                            <div class="bg-white p-2 rounded border border-slate-200 shadow-2xs">
                                <span class="block text-[9px] text-slate-500 font-bold uppercase">Resp. Rate</span>
                                <span class="font-mono font-black text-xs text-slate-900">{{ $rr ? $rr . ' cpm' : '—' }}</span>
                            </div>
                            <div class="bg-white p-2 rounded border border-slate-200 shadow-2xs">
                                <span class="block text-[9px] text-slate-500 font-bold uppercase">Oxygen (SpO2)</span>
                                <span class="font-mono font-black text-xs text-slate-900">{{ $spo2 ? $spo2 . '%' : '—' }}</span>
                            </div>
                            <div class="bg-white p-2 rounded border border-slate-200 shadow-2xs">
                                <span class="block text-[9px] text-slate-500 font-bold uppercase">Weight</span>
                                <span class="font-mono font-black text-xs text-slate-900">{{ $wt ? $wt . ' kg' : '—' }}</span>
                            </div>
                            <div class="bg-white p-2 rounded border border-slate-200 shadow-2xs">
                                <span class="block text-[9px] text-slate-500 font-bold uppercase">Height</span>
                                <span class="font-mono font-black text-xs text-slate-900">{{ $ht ? $ht . ' cm' : '—' }}</span>
                            </div>
                        </div>
                    </div>
                    @endif

                    {{-- Chief Complaint / Presenting Symptoms --}}
                    <div>
                        <span class="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-0.5">Chief Complaint / Presenting Symptoms</span>
                        <div class="p-2.5 rounded bg-slate-50 border border-slate-200 text-slate-800 leading-relaxed font-medium">
                            {{ $complaint ?: 'No primary complaint or symptom notes recorded at triage.' }}
                        </div>
                    </div>

                    {{-- Diagnosis --}}
                    <div class="p-3 rounded-lg bg-emerald-50/70 border border-emerald-300">
                        <span class="block text-[10px] font-black uppercase tracking-wider text-emerald-900 mb-1">Primary Clinical Diagnosis / Impression</span>
                        <p class="text-slate-900 font-black text-sm uppercase tracking-tight">{{ $case->diagnosis ?: 'Clinical Follow-up & Evaluation' }}</p>
                    </div>

                    {{-- Clinical Notes --}}
                    @if($case->consultation && $case->consultation->medical_notes && !in_array(trim($case->consultation->medical_notes), ['Nothing New', '[Auto-Closed At End Of Day]', '[Auto-closed at End of Day]']))
                    <div>
                        <span class="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-0.5">Physician Examination & Progress Notes</span>
                        <p class="text-slate-700 p-2.5 bg-slate-50 rounded border border-slate-200 leading-relaxed">{{ $case->consultation->medical_notes }}</p>
                    </div>
                    @endif

                    {{-- Formatted Treatment Plan & Medications Table (NO RAW JSON) --}}
                    <div>
                        <span class="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1.5">Treatment Plan & Prescribed Medications</span>
                        
                        @if($hasStructuredPrescription && count($parsedMedications) > 0)
                            <div class="border border-slate-300 rounded-lg overflow-hidden">
                                <table class="w-full text-xs text-left">
                                    <thead class="bg-slate-100 border-b border-slate-300 text-slate-700 font-bold uppercase text-[10px] tracking-wider">
                                        <tr>
                                            <th class="py-1.5 px-3 w-8 text-center">#</th>
                                            <th class="py-1.5 px-3">Medication & Formulation</th>
                                            <th class="py-1.5 px-3">Dosage / Strength</th>
                                            <th class="py-1.5 px-3">Instructions / Sig</th>
                                            <th class="py-1.5 px-3 w-20 text-center">Quantity</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-200">
                                        @foreach($parsedMedications as $idx => $med)
                                        <tr class="hover:bg-slate-50">
                                            <td class="py-2 px-3 text-center font-bold text-slate-400">{{ $idx + 1 }}</td>
                                            <td class="py-2 px-3 font-bold text-slate-900">
                                                {{ $med['medicine'] ?? ($med['name'] ?? 'Prescribed Medicine') }}
                                                @if(!empty($med['isOtc']))
                                                    <span class="ml-1 text-[9px] px-1.5 py-0.2 rounded bg-amber-100 text-amber-800 font-bold">OTC</span>
                                                @endif
                                            </td>
                                            <td class="py-2 px-3 font-semibold text-slate-700">
                                                {{ $med['amount'] ?? ($med['dosage'] ?? '—') }}
                                            </td>
                                            <td class="py-2 px-3 text-slate-700 leading-relaxed">
                                                {{ $med['instruction'] ?? ($med['sig'] ?? 'As directed by physician') }}
                                            </td>
                                            <td class="py-2 px-3 text-center font-mono font-bold text-slate-800">
                                                {{ !empty($med['quantity']) ? $med['quantity'] : '—' }}
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @elseif(!empty($plainPrescriptionText))
                            <div class="p-3 bg-slate-50 rounded-lg border border-slate-200 font-mono text-xs text-slate-800 whitespace-pre-line leading-relaxed">
                                {!! nl2br(e($plainPrescriptionText)) !!}
                            </div>
                        @else
                            <div class="p-3 bg-slate-50 rounded-lg border border-slate-200 text-slate-500 italic text-xs">
                                No prescription medications ordered for this encounter.
                            </div>
                        @endif
                    </div>

                    {{-- Diagnostic & Laboratory Results Summary --}}
                    @php
                        $doneAncillary = $case->consultation ? $case->consultation->ancillaryRequests->where('status', 'Done') : collect();
                    @endphp
                    @if($doneAncillary->count() > 0)
                    <div>
                        <span class="block text-[10px] font-bold uppercase tracking-wider text-indigo-700 mb-1.5">Diagnostic & Ancillary Examinations</span>
                        <div class="space-y-2">
                            @foreach($doneAncillary as $anc)
                                <div class="p-2.5 bg-indigo-50/60 rounded-lg border border-indigo-200 text-xs">
                                    <div class="flex items-center justify-between mb-1">
                                        <div class="flex items-center gap-2">
                                            <span class="px-2 py-0.5 rounded text-[9px] font-bold uppercase tracking-wider bg-indigo-200 text-indigo-800">
                                                {{ $anc->type }}
                                            </span>
                                            <span class="font-black text-slate-900">{{ $anc->test_name }}</span>
                                        </div>
                                        <span class="font-bold text-[10px] text-emerald-700">
                                            Status: Done
                                        </span>
                                    </div>

                                    @if($anc->result_data)
                                        @php
                                            $rd = $anc->result_data;
                                        @endphp
                                        @if(isset($rd['findings']) || isset($rd['impression']))
                                            {{-- Radiology Chest X-Ray findings --}}
                                            <div class="mt-1 text-slate-800 bg-white p-2 rounded border border-indigo-100 space-y-1">
                                                @if(!empty($rd['findings']))
                                                    <p class="text-[11px] leading-relaxed"><strong class="text-slate-700">Findings:</strong> {{ $rd['findings'] }}</p>
                                                @endif
                                                @if(!empty($rd['impression']))
                                                    <p class="text-[11px] font-bold text-emerald-900"><strong class="text-slate-700">Impression:</strong> {{ $rd['impression'] }}</p>
                                                @endif
                                            </div>
                                        @elseif(isset($rd['wbc']) || isset($rd['hemoglobin']))
                                            {{-- CBC summary --}}
                                            <div class="mt-1 text-[11px] text-slate-800 bg-white p-2 rounded border border-indigo-100 flex flex-wrap gap-x-4 gap-y-1">
                                                <span><strong>WBC:</strong> {{ $rd['wbc'] ?? '—' }} x10^9/L</span>
                                                <span><strong>RBC:</strong> {{ $rd['rbc'] ?? '—' }} x10^12/L</span>
                                                <span><strong>Hgb:</strong> {{ $rd['hemoglobin'] ?? '—' }} g/L</span>
                                                <span><strong>Hct:</strong> {{ $rd['hematocrit'] ?? '—' }}%</span>
                                                @if(!empty($rd['platelet_count']))
                                                    <span><strong>Platelets:</strong> {{ $rd['platelet_count'] }} x10^9/L</span>
                                                @endif
                                            </div>
                                        @elseif(isset($rd['color']) || isset($rd['pus_cells_wbc']))
                                            {{-- Urinalysis summary --}}
                                            <div class="mt-1 text-[11px] text-slate-800 bg-white p-2 rounded border border-indigo-100 flex flex-wrap gap-x-4 gap-y-1">
                                                <span><strong>Color:</strong> {{ $rd['color'] ?? '—' }}</span>
                                                <span><strong>Transparency:</strong> {{ $rd['transparency'] ?? '—' }}</span>
                                                <span><strong>pH:</strong> {{ $rd['ph'] ?? '—' }}</span>
                                                <span><strong>Protein:</strong> {{ $rd['protein'] ?? 'Negative' }}</span>
                                                <span><strong>Pus Cells:</strong> {{ $rd['pus_cells_wbc'] ?? '—' }}/hpf</span>
                                                <span><strong>RBC:</strong> {{ $rd['rbc_urine'] ?? '—' }}/hpf</span>
                                            </div>
                                        @endif
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    {{-- Follow-Up Note (if scheduled) --}}
                    @if($case->consultation && $case->consultation->is_followup_needed)
                    <div class="p-2.5 rounded-lg bg-amber-50 border border-amber-200 flex items-center justify-between text-xs">
                        <div>
                            <span class="font-bold text-amber-900">Scheduled Follow-up Date:</span>
                            <span class="font-black text-amber-950 ml-1">
                                {{ $case->consultation->followup_date ? \Carbon\Carbon::parse($case->consultation->followup_date)->format('F d, Y') : 'As needed' }}
                            </span>
                            @if($case->consultation->followup_reason)
                                <span class="text-amber-800 ml-2">({{ $case->consultation->followup_reason }})</span>
                            @endif
                        </div>
                        @if($case->consultation->followup_completed_at)
                            <span class="text-[10px] font-bold text-emerald-700 bg-emerald-100 border border-emerald-300 px-2 py-0.5 rounded">
                                Fulfilled on {{ $case->consultation->followup_completed_at->format('M d, Y') }}
                            </span>
                        @endif
                    </div>
                    @endif

                    {{-- Attending Physician Signature Block --}}
                    <div class="pt-4 mt-2 border-t border-slate-200 flex justify-end">
                        <div class="rhu-signature-box text-center w-64">
                            <div class="rhu-signature-line"></div>
                            <div class="rhu-signature-name text-xs font-black text-slate-900">{{ $attendingDoctor }}</div>
                            <div class="rhu-signature-role text-[10px] text-slate-500">License / Signature over Printed Name</div>
                        </div>
                    </div>

                </div>
            </article>
        @empty
            <div class="p-12 text-center text-slate-500 bg-slate-50 border border-slate-200 rounded-lg">
                <svg class="w-10 h-10 mx-auto text-slate-300 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <p class="font-bold">No clinical encounter records found for this patient.</p>
                <p class="text-xs text-slate-400 mt-1">Encounters are recorded when consultations are concluded by clinical staff.</p>
            </div>
        @endforelse
    </section>

    {{-- Official End Of Record Marker --}}
    <div class="avoid-break text-center text-[10px] text-slate-400 mt-8 pt-4 border-t border-slate-200">
        <p class="font-bold tracking-widest uppercase">*** END OF OFFICIAL TREATMENT RECORD ***</p>
        <p class="mt-0.5">Rural Health Unit — Silang, Cavite • Document Verification ID: {{ strtoupper(substr(md5($patient->patient_id . now()->toDateString()), 0, 12)) }}</p>
    </div>

</x-print-layout>
