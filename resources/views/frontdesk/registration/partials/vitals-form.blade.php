{{--
    Vitals Verification + Severity Triage Form
    Used for BOTH returning patients (storeVisit) and new patients (registerAndQueue)
    Requires: $preTriageRecord (PreTriage model) to be passed from the parent view
--}}
@php
    $pt = $preTriageRecord ?? null;
    $patientClassification = $selectedPatient->classification ?? ($newFromTriage->classification ?? '');
    // Normalize triage classification → patient classification
    $classMap = ['Adult' => 'Regular Adult', 'Senior' => 'Senior Citizen', 'Pediatric' => 'Pediatric', 'PWD' => 'PWD'];
    $normalizedClass = $classMap[$patientClassification] ?? $patientClassification;
    $isPediatric = in_array($normalizedClass, ['Pediatric']);

    $isFollowUp = false;
    if (isset($selectedPatient) && $selectedPatient && $selectedPatient->is_follow_up) {
        $isFollowUp = true;
    }
    if (isset($prefillApt) && $prefillApt && $prefillApt->is_follow_up) {
        $isFollowUp = true;
    }
@endphp

@if($pt)
<div class="space-y-6">
    {{-- Hidden fields --}}
    <input type="hidden" name="pre_triage_id" value="{{ $pt->id }}">
    <input type="hidden" name="consultation_date" value="{{ date('Y-m-d') }}">

    {{-- ═══════════════════════════════════════════════════════════════
         STEP: VITALS VERIFICATION (Read-Only)
         ═══════════════════════════════════════════════════════════════ --}}
    <div>
        <div class="flex items-center gap-2 mb-3">
            <svg class="w-5 h-5 text-teal-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
            <h4 class="text-sm font-bold text-gray-900 dark:text-white uppercase tracking-wide">Verify Vitals from Triage Station</h4>
        </div>
        <p class="text-xs text-gray-500 dark:text-gray-400 mb-4">Review the vitals recorded by the nurse. If there are errors, ask the vitals nurse to correct them.</p>

        {{-- Vitals Grid --}}
        <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-8 gap-2 mb-4">
            <div class="bg-gray-50 dark:bg-gray-900 rounded-lg p-3 text-center border border-gray-200 dark:border-gray-700">
                <div class="text-[10px] text-gray-500 font-bold uppercase tracking-wider mb-1">BP</div>
                <div class="font-bold text-gray-800 dark:text-white text-sm">{{ $pt->blood_pressure ?: '--' }}</div>
            </div>
            <div class="bg-gray-50 dark:bg-gray-900 rounded-lg p-3 text-center border border-gray-200 dark:border-gray-700">
                <div class="text-[10px] text-gray-500 font-bold uppercase tracking-wider mb-1">TEMP</div>
                <div class="font-bold text-gray-800 dark:text-white text-sm">{{ $pt->temperature ? $pt->temperature . '°C' : '--' }}</div>
            </div>
            <div class="bg-gray-50 dark:bg-gray-900 rounded-lg p-3 text-center border border-gray-200 dark:border-gray-700">
                <div class="text-[10px] text-gray-500 font-bold uppercase tracking-wider mb-1">HR</div>
                <div class="font-bold text-gray-800 dark:text-white text-sm">{{ $pt->heart_rate ?: '--' }}</div>
            </div>
            <div class="bg-gray-50 dark:bg-gray-900 rounded-lg p-3 text-center border border-gray-200 dark:border-gray-700">
                <div class="text-[10px] text-gray-500 font-bold uppercase tracking-wider mb-1">RR</div>
                <div class="font-bold text-gray-800 dark:text-white text-sm">{{ $pt->respiratory_rate ?: '--' }}</div>
            </div>
            <div class="bg-gray-50 dark:bg-gray-900 rounded-lg p-3 text-center border border-gray-200 dark:border-gray-700">
                <div class="text-[10px] text-gray-500 font-bold uppercase tracking-wider mb-1">PR</div>
                <div class="font-bold text-gray-800 dark:text-white text-sm">{{ $pt->pulse_rate ?: '--' }}</div>
            </div>
            <div class="bg-gray-50 dark:bg-gray-900 rounded-lg p-3 text-center border border-gray-200 dark:border-gray-700">
                <div class="text-[10px] text-gray-500 font-bold uppercase tracking-wider mb-1">SpO₂</div>
                <div class="font-bold text-gray-800 dark:text-white text-sm">{{ ($pt->spo2 ?? $pt->oxygen_saturation) ? ($pt->spo2 ?? $pt->oxygen_saturation) . '%' : '--' }}</div>
            </div>
            <div class="bg-gray-50 dark:bg-gray-900 rounded-lg p-3 text-center border border-gray-200 dark:border-gray-700">
                <div class="text-[10px] text-gray-500 font-bold uppercase tracking-wider mb-1">WT</div>
                <div class="font-bold text-gray-800 dark:text-white text-sm">{{ $pt->weight ? $pt->weight . 'kg' : '--' }}</div>
            </div>
            <div class="bg-gray-50 dark:bg-gray-900 rounded-lg p-3 text-center border border-gray-200 dark:border-gray-700">
                <div class="text-[10px] text-gray-500 font-bold uppercase tracking-wider mb-1">HT</div>
                <div class="font-bold text-gray-800 dark:text-white text-sm">{{ $pt->height ? $pt->height . 'cm' : '--' }}</div>
            </div>
        </div>

        {{-- Clinical Notes --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
            <div class="bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg p-3">
                <div class="text-[10px] text-gray-500 font-bold uppercase tracking-wider mb-1">Symptoms / Chief Complaint</div>
                <div class="text-sm font-medium text-gray-800 dark:text-white">{{ $pt->symptoms ?: ($pt->chief_complaint ?: 'None recorded') }}</div>
            </div>
            <div class="bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg p-3">
                <div class="text-[10px] text-gray-500 font-bold uppercase tracking-wider mb-1">Past Medical History</div>
                <div class="text-sm font-medium text-gray-800 dark:text-white">{{ $pt->past_medical_history ?: 'N/A' }}</div>
            </div>
            <div class="bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg p-3">
                <div class="text-[10px] text-gray-500 font-bold uppercase tracking-wider mb-1">Medicine Taken</div>
                <div class="text-sm font-medium text-gray-800 dark:text-white">{{ $pt->medicine_taken ?: 'N/A' }}</div>
            </div>
            <div class="bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg p-3">
                <div class="text-[10px] text-gray-500 font-bold uppercase tracking-wider mb-1">Known Allergies</div>
                <div class="text-sm font-medium {{ ($pt->known_allergies && $pt->known_allergies !== 'N/A') ? 'text-red-600 dark:text-red-400' : 'text-gray-800 dark:text-white' }}">{{ $pt->known_allergies ?: 'N/A' }}</div>
            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════════════
         STEP: TRIAGE — SEVERITY SELECTION
         ═══════════════════════════════════════════════════════════════ --}}
    <div>
        <div class="flex items-center gap-2 mb-3">
            <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
            <h4 class="text-sm font-bold text-gray-900 dark:text-white uppercase tracking-wide">Triage — Assign Severity</h4>
        </div>

        @if($isFollowUp)
            {{-- Follow Up: auto-assign to previous doctor --}}
            <input type="hidden" name="symptom_severity" value="mild">
            <div class="bg-amber-50 dark:bg-amber-900/20 border-2 border-amber-300 dark:border-amber-700 rounded-xl p-4 flex items-start gap-3">
                <svg class="w-6 h-6 text-amber-600 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <div>
                    <p class="font-bold text-amber-800 dark:text-amber-200 text-sm">Follow-up Patient — Routed to Attending Doctor</p>
                    <p class="text-xs text-amber-600 dark:text-amber-300 mt-1">This patient is a follow-up and will be automatically routed to the doctor from their last consultation to ensure continuity of care. No severity selection needed.</p>
                </div>
            </div>
        @elseif($isPediatric)
            {{-- Pediatric: auto-assign, no severity needed --}}
            <input type="hidden" name="symptom_severity" value="light">
            <div class="bg-purple-50 dark:bg-purple-900/20 border-2 border-purple-300 dark:border-purple-700 rounded-xl p-4 flex items-start gap-3">
                <svg class="w-6 h-6 text-purple-600 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                <div>
                    <p class="font-bold text-purple-800 dark:text-purple-200 text-sm">Pediatric Patient — Auto-Assigned to Pediatrician</p>
                    <p class="text-xs text-purple-600 dark:text-purple-300 mt-1">All pediatric patients are automatically routed to the available Pediatrician. No severity selection needed.</p>
                </div>
            </div>
        @else
            {{-- Non-Pediatric: severity selection cards --}}
            <p class="text-xs text-gray-500 dark:text-gray-400 mb-4">Based on the symptoms, select the appropriate severity level. The system will automatically assign the patient.</p>

            <div x-data="{ severity: '' }" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                    {{-- Light --}}
                    <label @click="severity = 'light'"
                           :class="severity === 'light' ? 'border-green-500 bg-green-50 dark:bg-green-900/30 ring-2 ring-green-300' : 'border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 hover:border-green-300 hover:bg-green-50/50'"
                           class="relative border-2 rounded-xl p-4 cursor-pointer transition-all duration-200 block">
                        <input type="radio" name="symptom_severity" value="light" x-model="severity" class="sr-only" required>
                        <div class="text-center">
                            <div class="text-2xl mb-1">🟢</div>
                            <div class="font-bold text-gray-900 dark:text-white text-sm">LIGHT</div>
                            <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">Routine checkup, meds refill</div>
                            <div class="text-xs font-bold text-green-700 dark:text-green-400 mt-2 bg-green-100 dark:bg-green-900/40 rounded-full px-2 py-0.5 inline-block">→ Nurse</div>
                        </div>
                        <div x-show="severity === 'light'" class="absolute top-2 right-2">
                            <svg class="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                        </div>
                    </label>

                    {{-- Mild --}}
                    <label @click="severity = 'mild'"
                           :class="severity === 'mild' ? 'border-yellow-500 bg-yellow-50 dark:bg-yellow-900/30 ring-2 ring-yellow-300' : 'border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 hover:border-yellow-300 hover:bg-yellow-50/50'"
                           class="relative border-2 rounded-xl p-4 cursor-pointer transition-all duration-200 block">
                        <input type="radio" name="symptom_severity" value="mild" x-model="severity" class="sr-only" required>
                        <div class="text-center">
                            <div class="text-2xl mb-1">🟡</div>
                            <div class="font-bold text-gray-900 dark:text-white text-sm">MILD</div>
                            <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">Colds, slight fever, moderate pain</div>
                            <div class="text-xs font-bold text-yellow-700 dark:text-yellow-400 mt-2 bg-yellow-100 dark:bg-yellow-900/40 rounded-full px-2 py-0.5 inline-block">→ Nurse / Doctor</div>
                        </div>
                        <div x-show="severity === 'mild'" class="absolute top-2 right-2">
                            <svg class="w-5 h-5 text-yellow-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                        </div>
                    </label>

                    {{-- Severe --}}
                    <label @click="severity = 'severe'"
                           :class="severity === 'severe' ? 'border-red-500 bg-red-50 dark:bg-red-900/30 ring-2 ring-red-300' : 'border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 hover:border-red-300 hover:bg-red-50/50'"
                           class="relative border-2 rounded-xl p-4 cursor-pointer transition-all duration-200 block">
                        <input type="radio" name="symptom_severity" value="severe" x-model="severity" class="sr-only" required>
                        <div class="text-center">
                            <div class="text-2xl mb-1">🔴</div>
                            <div class="font-bold text-gray-900 dark:text-white text-sm">SEVERE</div>
                            <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">High fever, extreme pain, emergency</div>
                            <div class="text-xs font-bold text-red-700 dark:text-red-400 mt-2 bg-red-100 dark:bg-red-900/40 rounded-full px-2 py-0.5 inline-block">→ Doctor</div>
                        </div>
                        <div x-show="severity === 'severe'" class="absolute top-2 right-2">
                            <svg class="w-5 h-5 text-red-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                        </div>
                    </label>
                </div>

                {{-- Assignment preview --}}
                <div x-show="severity" x-transition class="text-xs text-center py-2 px-4 rounded-lg font-bold"
                     :class="{
                        'bg-green-100 text-green-800 border border-green-200': severity === 'light',
                        'bg-yellow-100 text-yellow-800 border border-yellow-200': severity === 'mild',
                        'bg-red-100 text-red-800 border border-red-200': severity === 'severe',
                     }">
                    <span x-show="severity === 'light'">✓ Will be assigned to Clinical Nurse (auto)</span>
                    <span x-show="severity === 'mild'">✓ Will be load-balanced — assigned to whichever has a shorter queue</span>
                    <span x-show="severity === 'severe'">✓ Will be assigned to Doctor (auto)</span>
                </div>
            </div>
        @endif
    </div>
</div>
@else
    <div class="bg-red-50 border border-red-200 text-red-800 rounded-xl p-4 text-sm">
        <p class="font-bold">No vitals data found.</p>
        <p class="text-xs mt-1">This patient must go through the Vitals Station first before being queued.</p>
    </div>
@endif
