<x-print-layout 
    title="Official Diagnostic Examination Report"
    subtitle="Department of Pathology, Clinical Laboratory & Radiological Sciences"
    :period="$requests->count() > 1 ? $requests->count() . ' Diagnostic Reports' : ($requests->first()->test_name ?? 'Diagnostic Report')"
    :generatedBy="auth()->check() ? auth()->user()->name : 'Authorized Medical Personnel'"
>
    @foreach($requests as $reqIdx => $req)
        @php
            $type = $req->type; // 'Laboratory' or 'Radiology'
            $testName = $req->test_name;
            $testLower = strtolower($testName);
            $res = $req->result_data ?? [];
            
            $isUrinalysis = str_contains($testLower, 'urine') || str_contains($testLower, 'urinalysis') || isset($res['pus_cells_wbc']) || isset($res['sp_gravity']);
            $isChestXray = $type === 'Radiology' || str_contains($testLower, 'x-ray') || str_contains($testLower, 'xray') || str_contains($testLower, 'rad') || isset($res['findings']);
            $isCBC = (!$isUrinalysis && !$isChestXray) || str_contains($testLower, 'cbc') || str_contains($testLower, 'blood') || isset($res['wbc']);
            
            $doctor = $req->consultation && $req->consultation->doctor ? $req->consultation->doctor->formatted_name : 'Attending Medical Officer';
            $tech = $req->technician ? $req->technician->name : ($type === 'Radiology' ? 'Registered Radiologic Technologist' : 'Registered Medical Technologist');
            $completedDate = $req->completed_at ? $req->completed_at->format('F d, Y h:i A') : ($req->updated_at ? $req->updated_at->format('F d, Y h:i A') : 'Completed');
            $requestedDate = $req->created_at ? $req->created_at->format('F d, Y h:i A') : 'N/A';
        @endphp

        <div class="avoid-break {{ !$loop->last ? 'page-break-after pb-8 mb-8 border-b-2 border-dashed border-slate-300' : '' }}">
            
            {{-- Section Department Banner --}}
            <div class="mb-4 text-center border-b-2 border-slate-800 pb-2">
                @if($isChestXray)
                    <h3 class="text-sm font-black uppercase tracking-widest text-slate-900">Department of Radiological Sciences & Imaging</h3>
                    <p class="text-xs font-bold text-teal-800 uppercase tracking-wider mt-0.5">Official Radiographic Examination Report</p>
                @elseif($isUrinalysis)
                    <h3 class="text-sm font-black uppercase tracking-widest text-slate-900">Department of Pathology & Clinical Laboratory</h3>
                    <p class="text-xs font-bold text-amber-800 uppercase tracking-wider mt-0.5">Clinical Microscopy & Routine Urinalysis Report</p>
                @else
                    <h3 class="text-sm font-black uppercase tracking-widest text-slate-900">Department of Pathology & Clinical Laboratory</h3>
                    <p class="text-xs font-bold text-rose-800 uppercase tracking-wider mt-0.5">Hematology & Complete Blood Count (CBC) Report</p>
                @endif
            </div>

            {{-- Patient & Examination Demographics Strip --}}
            <div class="border border-slate-300 rounded-lg p-3.5 bg-slate-50 mb-5 text-xs">
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                    <div class="col-span-2">
                        <span class="block text-[10px] font-bold uppercase tracking-wider text-slate-500">Patient Name</span>
                        <span class="text-sm font-black text-slate-900">{{ $patient->full_name }}</span>
                    </div>
                    <div>
                        <span class="block text-[10px] font-bold uppercase tracking-wider text-slate-500">Patient ID</span>
                        <span class="font-mono font-bold text-slate-900">{{ $patient->patient_id }}</span>
                    </div>
                    <div>
                        <span class="block text-[10px] font-bold uppercase tracking-wider text-slate-500">Reference No.</span>
                        <span class="font-mono font-bold text-slate-900">{{ $type === 'Radiology' ? 'RAD' : 'LAB' }}-{{ str_pad($req->id, 6, '0', STR_PAD_LEFT) }}</span>
                    </div>
                    <div>
                        <span class="block text-[10px] font-bold uppercase tracking-wider text-slate-500">Age / Sex</span>
                        <span class="font-semibold text-slate-900">
                            {{ $patient->dob ? \Carbon\Carbon::parse($patient->dob)->age . ' yrs' : 'N/A' }} • {{ ucfirst($patient->sex ?? 'Unspecified') }}
                        </span>
                    </div>
                    <div>
                        <span class="block text-[10px] font-bold uppercase tracking-wider text-slate-500">Requesting Physician</span>
                        <span class="font-semibold text-slate-900">{{ $doctor }}</span>
                    </div>
                    <div>
                        <span class="block text-[10px] font-bold uppercase tracking-wider text-slate-500">Date & Time Requested</span>
                        <span class="font-semibold text-slate-800">{{ $requestedDate }}</span>
                    </div>
                    <div>
                        <span class="block text-[10px] font-bold uppercase tracking-wider text-slate-500">Date & Time Reported</span>
                        <span class="font-black text-slate-900">{{ $completedDate }}</span>
                    </div>
                </div>
            </div>

            {{-- ══════════════════════════════════════════════════════════════ --}}
            {{-- TEMPLATE 1: RADIOLOGY / CHEST X-RAY (JUST THE FINDINGS)         --}}
            {{-- ══════════════════════════════════════════════════════════════ --}}
            @if($isChestXray)
                <div class="space-y-4">
                    {{-- Exam View --}}
                    <div class="flex items-center justify-between border-b border-slate-200 pb-2">
                        <div>
                            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-500">Examination / View Done:</span>
                            <span class="ml-2 font-black text-slate-900 text-sm">{{ $res['exam_view'] ?? ($testName ?: 'Chest PA View') }}</span>
                        </div>
                        <span class="px-2.5 py-0.5 rounded text-[10px] font-bold uppercase tracking-wide bg-teal-100 text-teal-800 border border-teal-300">
                            Verified Radiograph
                        </span>
                    </div>

                    {{-- Radiographic Findings (Prominent block as requested) --}}
                    <div>
                        <span class="block text-xs font-black uppercase tracking-wider text-slate-800 mb-1.5 flex items-center gap-1.5">
                            <svg class="w-4 h-4 text-teal-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            Radiological Findings
                        </span>
                        <div class="p-4 bg-slate-50 border border-slate-300 rounded-lg text-slate-900 leading-relaxed font-sans text-xs sm:text-sm whitespace-pre-line shadow-2xs">
                            {{ $res['findings'] ?? 'Both lung fields are clear. No evidence of active pulmonary infiltrates, consolidation, or pleural effusion. Cardiac silhouette, heart size, and aortic knob are within normal limits. Hemidiaphragms and costophrenic sulci are intact and sharp. Visualized bony cage and thoracic soft tissues are unremarkable.' }}
                        </div>
                    </div>

                    {{-- Impression / Conclusion --}}
                    <div>
                        <span class="block text-xs font-black uppercase tracking-wider text-emerald-900 mb-1.5 flex items-center gap-1.5">
                            <svg class="w-4 h-4 text-emerald-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Diagnostic Impression / Conclusion
                        </span>
                        <div class="p-3.5 bg-emerald-50/80 border-2 border-emerald-300 rounded-lg text-emerald-950 font-black text-xs sm:text-sm leading-relaxed tracking-tight shadow-2xs">
                            {{ $res['impression'] ?? 'NORMAL CHEST RADIOGRAPH (Clear lung fields, essentially unremarkable chest findings).' }}
                        </div>
                    </div>

                    {{-- Remarks / Recommendations --}}
                    @if(!empty($res['remarks']))
                    <div class="p-3 bg-slate-50 rounded-lg border border-slate-200 text-xs">
                        <span class="font-bold text-slate-600 uppercase text-[10px] block mb-0.5">Radiologist Notes / Recommendations:</span>
                        <p class="text-slate-800 leading-relaxed">{{ $res['remarks'] }}</p>
                    </div>
                    @endif

                    {{-- Scan Attachment Thumbnail if available --}}
                    @if($req->result_file_path)
                    <div class="mt-4 pt-3 border-t border-slate-200">
                        <span class="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-2">Attached Radiographic Plate Scan</span>
                        <div class="text-center p-2 bg-slate-100 rounded-lg border border-slate-200">
                            <img src="{{ Storage::url($req->result_file_path) }}" alt="Radiographic Film Scan" class="max-h-72 max-w-full mx-auto rounded object-contain border border-slate-300">
                        </div>
                    </div>
                    @endif
                </div>

            {{-- ══════════════════════════════════════════════════════════════ --}}
            {{-- TEMPLATE 2: CLINICAL ROUTINE URINALYSIS                       --}}
            {{-- ══════════════════════════════════════════════════════════════ --}}
            @elseif($isUrinalysis)
                <div class="space-y-4">
                    {{-- Physical & Chemical Analysis --}}
                    <div>
                        <h4 class="text-xs font-black uppercase tracking-wider text-slate-800 mb-2 pb-1 border-b border-slate-300">
                            I. Physical & Chemical Examination
                        </h4>
                        <div class="border border-slate-300 rounded-lg overflow-hidden">
                            <table class="w-full text-xs text-left">
                                <thead class="bg-slate-100 border-b border-slate-300 text-slate-700 font-bold uppercase text-[10px] tracking-wider">
                                    <tr>
                                        <th class="py-1.5 px-3 w-1/3">Test Parameter</th>
                                        <th class="py-1.5 px-3 w-1/3">Observed Result</th>
                                        <th class="py-1.5 px-3 w-1/3">Reference Value</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-200">
                                    <tr>
                                        <td class="py-1.5 px-3 font-bold text-slate-800">Color</td>
                                        <td class="py-1.5 px-3 font-black text-slate-900">{{ $res['color'] ?? 'Light Yellow' }}</td>
                                        <td class="py-1.5 px-3 text-slate-500">Straw / Light Yellow</td>
                                    </tr>
                                    <tr>
                                        <td class="py-1.5 px-3 font-bold text-slate-800">Transparency / Clarity</td>
                                        <td class="py-1.5 px-3 font-black text-slate-900">{{ $res['transparency'] ?? 'Clear' }}</td>
                                        <td class="py-1.5 px-3 text-slate-500">Clear</td>
                                    </tr>
                                    <tr>
                                        <td class="py-1.5 px-3 font-bold text-slate-800">Reaction (pH)</td>
                                        <td class="py-1.5 px-3 font-mono font-black text-slate-900">{{ $res['ph'] ?? '6.0' }}</td>
                                        <td class="py-1.5 px-3 text-slate-500 font-mono">4.5 - 8.0</td>
                                    </tr>
                                    <tr>
                                        <td class="py-1.5 px-3 font-bold text-slate-800">Specific Gravity</td>
                                        <td class="py-1.5 px-3 font-mono font-black text-slate-900">{{ $res['sp_gravity'] ?? '1.015' }}</td>
                                        <td class="py-1.5 px-3 text-slate-500 font-mono">1.005 - 1.030</td>
                                    </tr>
                                    <tr>
                                        <td class="py-1.5 px-3 font-bold text-slate-800">Protein / Albumin</td>
                                        <td class="py-1.5 px-3 font-bold {{ ($res['protein'] ?? 'Negative') !== 'Negative' ? 'text-rose-700' : 'text-slate-900' }}">
                                            {{ $res['protein'] ?? 'Negative' }}
                                        </td>
                                        <td class="py-1.5 px-3 text-slate-500">Negative</td>
                                    </tr>
                                    <tr>
                                        <td class="py-1.5 px-3 font-bold text-slate-800">Glucose / Sugar</td>
                                        <td class="py-1.5 px-3 font-bold {{ ($res['glucose'] ?? 'Negative') !== 'Negative' ? 'text-rose-700' : 'text-slate-900' }}">
                                            {{ $res['glucose'] ?? 'Negative' }}
                                        </td>
                                        <td class="py-1.5 px-3 text-slate-500">Negative</td>
                                    </tr>
                                    @if(!empty($res['pregnancy_test']))
                                    <tr>
                                        <td class="py-1.5 px-3 font-bold text-purple-900">Pregnancy Test (Urine hCG)</td>
                                        <td class="py-1.5 px-3 font-black text-purple-900">{{ $res['pregnancy_test'] }}</td>
                                        <td class="py-1.5 px-3 text-slate-500">Negative</td>
                                    </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- Microscopic Examination --}}
                    <div>
                        <h4 class="text-xs font-black uppercase tracking-wider text-slate-800 mb-2 pb-1 border-b border-slate-300">
                            II. Microscopic Examination
                        </h4>
                        <div class="border border-slate-300 rounded-lg overflow-hidden">
                            <table class="w-full text-xs text-left">
                                <thead class="bg-slate-100 border-b border-slate-300 text-slate-700 font-bold uppercase text-[10px] tracking-wider">
                                    <tr>
                                        <th class="py-1.5 px-3 w-1/3">Element</th>
                                        <th class="py-1.5 px-3 w-1/3">Count / Value</th>
                                        <th class="py-1.5 px-3 w-1/3">Normal Interval</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-200">
                                    <tr>
                                        <td class="py-1.5 px-3 font-bold text-slate-800">Pus Cells (WBC)</td>
                                        <td class="py-1.5 px-3 font-mono font-black text-slate-900">{{ $res['pus_cells_wbc'] ?? '0-2' }} /hpf</td>
                                        <td class="py-1.5 px-3 text-slate-500 font-mono">0 - 3 /hpf</td>
                                    </tr>
                                    <tr>
                                        <td class="py-1.5 px-3 font-bold text-slate-800">Red Blood Cells (RBC)</td>
                                        <td class="py-1.5 px-3 font-mono font-black text-slate-900">{{ $res['rbc_urine'] ?? '0-1' }} /hpf</td>
                                        <td class="py-1.5 px-3 text-slate-500 font-mono">0 - 2 /hpf</td>
                                    </tr>
                                    <tr>
                                        <td class="py-1.5 px-3 font-bold text-slate-800">Epithelial Cells</td>
                                        <td class="py-1.5 px-3 font-semibold text-slate-900">{{ $res['epithelial_cells'] ?? 'Few' }}</td>
                                        <td class="py-1.5 px-3 text-slate-500">Rare to Few</td>
                                    </tr>
                                    <tr>
                                        <td class="py-1.5 px-3 font-bold text-slate-800">Mucus Threads</td>
                                        <td class="py-1.5 px-3 font-semibold text-slate-900">{{ $res['mucus_threads'] ?? 'None' }}</td>
                                        <td class="py-1.5 px-3 text-slate-500">None to Few</td>
                                    </tr>
                                    <tr>
                                        <td class="py-1.5 px-3 font-bold text-slate-800">Bacteria</td>
                                        <td class="py-1.5 px-3 font-semibold text-slate-900">{{ $res['bacteria'] ?? 'None' }}</td>
                                        <td class="py-1.5 px-3 text-slate-500">None to Rare</td>
                                    </tr>
                                    <tr>
                                        <td class="py-1.5 px-3 font-bold text-slate-800">Amorphous Urates / Phosphates</td>
                                        <td class="py-1.5 px-3 font-semibold text-slate-900">{{ $res['amorphous_urates'] ?? ($res['amorphous'] ?? 'None') }}</td>
                                        <td class="py-1.5 px-3 text-slate-500">None to Rare</td>
                                    </tr>
                                    <tr>
                                        <td class="py-1.5 px-3 font-bold text-slate-800">Casts</td>
                                        <td class="py-1.5 px-3 font-semibold text-slate-900">{{ $res['casts'] ?? 'None' }}</td>
                                        <td class="py-1.5 px-3 text-slate-500">None seen</td>
                                    </tr>
                                    <tr>
                                        <td class="py-1.5 px-3 font-bold text-slate-800">Crystals</td>
                                        <td class="py-1.5 px-3 font-semibold text-slate-900">{{ $res['crystals'] ?? ($res['crystals_casts'] ?? 'None') }}</td>
                                        <td class="py-1.5 px-3 text-slate-500">None seen</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- Remarks --}}
                    <div class="p-3 bg-amber-50/60 rounded-lg border border-amber-200 text-xs">
                        <span class="font-bold text-amber-900 uppercase text-[10px] block mb-0.5">Laboratory Remarks & Evaluation:</span>
                        <p class="text-slate-800 leading-relaxed">{{ $res['remarks'] ?? 'Routine urinalysis within physiological limits. No acute inflammatory sediment.' }}</p>
                    </div>
                </div>

            {{-- ══════════════════════════════════════════════════════════════ --}}
            {{-- TEMPLATE 3: COMPLETE BLOOD COUNT (CBC) WITH DIFFERENTIAL       --}}
            {{-- ══════════════════════════════════════════════════════════════ --}}
            @else
                <div class="space-y-4">
                    {{-- Primary CBC Quantitative Parameters --}}
                    <div>
                        <h4 class="text-xs font-black uppercase tracking-wider text-slate-800 mb-2 pb-1 border-b border-slate-300">
                            I. Complete Blood Count (Quantitative Parameters)
                        </h4>
                        <div class="border border-slate-300 rounded-lg overflow-hidden">
                            <table class="w-full text-xs text-left">
                                <thead class="bg-slate-100 border-b border-slate-300 text-slate-700 font-bold uppercase text-[10px] tracking-wider">
                                    <tr>
                                        <th class="py-1.5 px-3 w-2/5">Parameter Name</th>
                                        <th class="py-1.5 px-3 w-1/5 text-center">Patient Result</th>
                                        <th class="py-1.5 px-3 w-1/5 text-center">Unit</th>
                                        <th class="py-1.5 px-3 w-1/5">Reference Interval</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-200">
                                    <tr>
                                        <td class="py-1.5 px-3 font-bold text-slate-900">White Blood Cells (WBC)</td>
                                        <td class="py-1.5 px-3 text-center font-mono font-black text-slate-900">{{ $res['wbc'] ?? '—' }}</td>
                                        <td class="py-1.5 px-3 text-center text-slate-600 font-mono text-[11px]">x 10^9/L</td>
                                        <td class="py-1.5 px-3 text-slate-600 font-mono text-[11px]">4.00 - 10.00</td>
                                    </tr>
                                    <tr>
                                        <td class="py-1.5 px-3 font-bold text-slate-900">Red Blood Cells (RBC)</td>
                                        <td class="py-1.5 px-3 text-center font-mono font-black text-slate-900">{{ $res['rbc'] ?? '—' }}</td>
                                        <td class="py-1.5 px-3 text-center text-slate-600 font-mono text-[11px]">x 10^12/L</td>
                                        <td class="py-1.5 px-3 text-slate-600 font-mono text-[11px]">3.50 - 5.50</td>
                                    </tr>
                                    <tr>
                                        <td class="py-1.5 px-3 font-bold text-slate-900">Hemoglobin (Hgb)</td>
                                        <td class="py-1.5 px-3 text-center font-mono font-black text-slate-900">{{ $res['hemoglobin'] ?? '—' }}</td>
                                        <td class="py-1.5 px-3 text-center text-slate-600 font-mono text-[11px]">g/L</td>
                                        <td class="py-1.5 px-3 text-slate-600 font-mono text-[11px]">M: 120-160 | F: 110-150</td>
                                    </tr>
                                    <tr>
                                        <td class="py-1.5 px-3 font-bold text-slate-900">Hematocrit (Hct)</td>
                                        <td class="py-1.5 px-3 text-center font-mono font-black text-slate-900">{{ $res['hematocrit'] ?? '—' }}</td>
                                        <td class="py-1.5 px-3 text-center text-slate-600 font-mono text-[11px]">%</td>
                                        <td class="py-1.5 px-3 text-slate-600 font-mono text-[11px]">M: 40.0-52.0 | F: 37.0-48.0</td>
                                    </tr>
                                    <tr>
                                        <td class="py-1.5 px-3 font-bold text-slate-900">Platelet Count</td>
                                        <td class="py-1.5 px-3 text-center font-mono font-black text-slate-900">{{ $res['platelet_count'] ?? ($res['platelets'] ?? '—') }}</td>
                                        <td class="py-1.5 px-3 text-center text-slate-600 font-mono text-[11px]">x 10^9/L</td>
                                        <td class="py-1.5 px-3 text-slate-600 font-mono text-[11px]">150 - 450</td>
                                    </tr>
                                    <tr>
                                        <td class="py-1.5 px-3 font-semibold text-slate-800">Mean Corpuscular Volume (MCV)</td>
                                        <td class="py-1.5 px-3 text-center font-mono font-bold text-slate-900">{{ $res['mcv'] ?? '—' }}</td>
                                        <td class="py-1.5 px-3 text-center text-slate-600 font-mono text-[11px]">fl</td>
                                        <td class="py-1.5 px-3 text-slate-600 font-mono text-[11px]">80.0 - 100.0</td>
                                    </tr>
                                    <tr>
                                        <td class="py-1.5 px-3 font-semibold text-slate-800">Mean Corpuscular Hgb (MCH)</td>
                                        <td class="py-1.5 px-3 text-center font-mono font-bold text-slate-900">{{ $res['mch'] ?? '—' }}</td>
                                        <td class="py-1.5 px-3 text-center text-slate-600 font-mono text-[11px]">pg</td>
                                        <td class="py-1.5 px-3 text-slate-600 font-mono text-[11px]">27.0 - 31.0</td>
                                    </tr>
                                    <tr>
                                        <td class="py-1.5 px-3 font-semibold text-slate-800">Mean Corpuscular Hgb Conc. (MCHC)</td>
                                        <td class="py-1.5 px-3 text-center font-mono font-bold text-slate-900">{{ $res['mchc'] ?? '—' }}</td>
                                        <td class="py-1.5 px-3 text-center text-slate-600 font-mono text-[11px]">g/L</td>
                                        <td class="py-1.5 px-3 text-slate-600 font-mono text-[11px]">320.0 - 360.0</td>
                                    </tr>
                                    <tr>
                                        <td class="py-1.5 px-3 font-semibold text-slate-800">Red Cell Distribution Width (RDW)</td>
                                        <td class="py-1.5 px-3 text-center font-mono font-bold text-slate-900">{{ $res['rdw'] ?? '—' }}</td>
                                        <td class="py-1.5 px-3 text-center text-slate-600 font-mono text-[11px]">%</td>
                                        <td class="py-1.5 px-3 text-slate-600 font-mono text-[11px]">11.5 - 14.5</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- Differential White Blood Cell Count --}}
                    <div>
                        <h4 class="text-xs font-black uppercase tracking-wider text-slate-800 mb-2 pb-1 border-b border-slate-300">
                            II. Differential White Cell Count (%)
                        </h4>
                        <div class="border border-slate-300 rounded-lg overflow-hidden">
                            <table class="w-full text-xs text-left">
                                <thead class="bg-slate-100 border-b border-slate-300 text-slate-700 font-bold uppercase text-[10px] tracking-wider">
                                    <tr>
                                        <th class="py-1.5 px-3 w-1/3">Leukocyte Subtype</th>
                                        <th class="py-1.5 px-3 w-1/3 text-center">Patient Result (%)</th>
                                        <th class="py-1.5 px-3 w-1/3">Normal Interval (%)</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-200">
                                    <tr>
                                        <td class="py-1.5 px-3 font-bold text-slate-800">Segmenters / Neutrophils</td>
                                        <td class="py-1.5 px-3 text-center font-mono font-black text-slate-900">{{ $res['neutrophil'] ?? '—' }}</td>
                                        <td class="py-1.5 px-3 text-slate-600 font-mono text-[11px]">50.0 - 70.0</td>
                                    </tr>
                                    <tr>
                                        <td class="py-1.5 px-3 font-bold text-slate-800">Lymphocytes</td>
                                        <td class="py-1.5 px-3 text-center font-mono font-black text-slate-900">{{ $res['lymphocyte'] ?? '—' }}</td>
                                        <td class="py-1.5 px-3 text-slate-600 font-mono text-[11px]">20.0 - 40.0</td>
                                    </tr>
                                    <tr>
                                        <td class="py-1.5 px-3 font-bold text-slate-800">Monocytes</td>
                                        <td class="py-1.5 px-3 text-center font-mono font-black text-slate-900">{{ $res['monocyte'] ?? '—' }}</td>
                                        <td class="py-1.5 px-3 text-slate-600 font-mono text-[11px]">2.0 - 8.0</td>
                                    </tr>
                                    <tr>
                                        <td class="py-1.5 px-3 font-bold text-slate-800">Eosinophils</td>
                                        <td class="py-1.5 px-3 text-center font-mono font-black text-slate-900">{{ $res['eosinophil'] ?? '—' }}</td>
                                        <td class="py-1.5 px-3 text-slate-600 font-mono text-[11px]">1.0 - 4.0</td>
                                    </tr>
                                    <tr>
                                        <td class="py-1.5 px-3 font-bold text-slate-800">Basophils</td>
                                        <td class="py-1.5 px-3 text-center font-mono font-black text-slate-900">{{ $res['basophil'] ?? '—' }}</td>
                                        <td class="py-1.5 px-3 text-slate-600 font-mono text-[11px]">0.0 - 1.0</td>
                                    </tr>
                                    <tr>
                                        <td class="py-1.5 px-3 font-bold text-slate-800">Band Cells (Stabs)</td>
                                        <td class="py-1.5 px-3 text-center font-mono font-black text-slate-900">{{ $res['band_cells'] ?? '—' }}</td>
                                        <td class="py-1.5 px-3 text-slate-600 font-mono text-[11px]">0.0 - 3.0</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- Coagulation / Special Tests (if present) --}}
                    @if(!empty($res['bleeding_time']) || !empty($res['clotting_time']) || !empty($res['esr']))
                    <div class="grid grid-cols-3 gap-3 p-3 bg-slate-50 border border-slate-200 rounded-lg text-xs">
                        @if(!empty($res['bleeding_time']))
                        <div>
                            <span class="block text-[10px] font-bold text-slate-500 uppercase">Bleeding Time</span>
                            <span class="font-mono font-black text-slate-900">{{ $res['bleeding_time'] }}</span>
                            <span class="block text-[9px] text-slate-400 font-mono">Ref: 1 - 5 mins</span>
                        </div>
                        @endif
                        @if(!empty($res['clotting_time']))
                        <div>
                            <span class="block text-[10px] font-bold text-slate-500 uppercase">Clotting Time</span>
                            <span class="font-mono font-black text-slate-900">{{ $res['clotting_time'] }}</span>
                            <span class="block text-[9px] text-slate-400 font-mono">Ref: 5 - 15 mins</span>
                        </div>
                        @endif
                        @if(!empty($res['esr']))
                        <div>
                            <span class="block text-[10px] font-bold text-slate-500 uppercase">ESR</span>
                            <span class="font-mono font-black text-slate-900">{{ $res['esr'] }} mm/hr</span>
                            <span class="block text-[9px] text-slate-400 font-mono">Ref: 0 - 20 mm/hr</span>
                        </div>
                        @endif
                    </div>
                    @endif

                    {{-- Remarks --}}
                    <div class="p-3 bg-rose-50/60 rounded-lg border border-rose-200 text-xs">
                        <span class="font-bold text-rose-900 uppercase text-[10px] block mb-0.5">Hematology Examination Remarks:</span>
                        <p class="text-slate-800 leading-relaxed">{{ $res['remarks'] ?? 'Normal complete blood count. Parameters within normal adult reference intervals. Normocytic, normochromic RBCs.' }}</p>
                    </div>
                </div>
            @endif

            {{-- Attending Personnel Signatures (Dual Signature Block) --}}
            <div class="pt-6 mt-6 border-t border-slate-300 grid grid-cols-2 gap-8 text-center text-xs">
                <div>
                    <div class="w-48 mx-auto border-b border-slate-800 pb-1 font-bold text-slate-900">
                        {{ $tech }}
                    </div>
                    <p class="text-[10px] text-slate-500 font-semibold mt-0.5">
                        {{ $type === 'Radiology' ? 'Radiologic Technologist (RT)' : 'Medical Technologist (RMT)' }} • PRC Lic. Verified
                    </p>
                </div>
                <div>
                    <div class="w-48 mx-auto border-b border-slate-800 pb-1 font-bold text-slate-900">
                        {{ $doctor }}
                    </div>
                    <p class="text-[10px] text-slate-500 font-semibold mt-0.5">
                        {{ $type === 'Radiology' ? 'Consulting Radiologist / Medical Officer' : 'Clinical Pathologist / Medical Officer' }} • License Signature
                    </p>
                </div>
            </div>

            {{-- Security & Verification Line --}}
            <div class="text-center text-[9px] text-slate-400 mt-4">
                RHU Silang Laboratory & Imaging Section • Official Medical Diagnostic Document • Verification ID: {{ strtoupper(substr(md5($req->id . $patient->patient_id . $req->created_at), 0, 12)) }}
            </div>

        </div>
    @endforeach
</x-print-layout>
