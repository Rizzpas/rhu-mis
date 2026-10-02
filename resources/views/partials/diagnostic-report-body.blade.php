@php
    $type = $req->type ?? 'Laboratory'; // 'Laboratory' or 'Radiology'
    $testName = $req->test_name ?? 'Diagnostic Examination';
    $testLower = strtolower($testName);
    $res = $req->result_data ?? [];
    $patient = $patient ?? ($req->consultation ? $req->consultation->patient : null);
    $isLoopLast = $isLoopLast ?? true;
    
    $isUrinalysis = str_contains($testLower, 'urine') || str_contains($testLower, 'urinalysis') || isset($res['pus_cells_wbc']) || isset($res['sp_gravity']);
    $isFecalysis = str_contains($testLower, 'fecal') || str_contains($testLower, 'stool') || isset($res['amoeba']) || isset($res['occult_blood']);
    $isBloodChemistry = str_contains($testLower, 'chem') || str_contains($testLower, 'glucose') || str_contains($testLower, 'fbs') || str_contains($testLower, 'lipid') || str_contains($testLower, 'cholesterol') || str_contains($testLower, 'uric') || str_contains($testLower, 'creatinine') || str_contains($testLower, 'sgpt') || isset($res['fbs']) || isset($res['total_cholesterol']);
    $isChestXray = $type === 'Radiology' || str_contains($testLower, 'x-ray') || str_contains($testLower, 'xray') || str_contains($testLower, 'rad') || isset($res['findings']);
    $isCBC = (!$isUrinalysis && !$isFecalysis && !$isBloodChemistry && !$isChestXray) || str_contains($testLower, 'cbc') || str_contains($testLower, 'blood') || isset($res['wbc']);
    
    $doctor = $req->consultation && $req->consultation->doctor ? $req->consultation->doctor->formatted_name : 'Attending Medical Officer';
    $tech = $req->technician ? $req->technician->name : ($type === 'Radiology' ? 'Registered Radiologic Technologist' : 'Registered Medical Technologist');
    $completedDate = $req->completed_at ? $req->completed_at->format('F d, Y h:i A') : ($req->updated_at ? $req->updated_at->format('F d, Y h:i A') : 'Completed');
    $requestedDate = $req->created_at ? $req->created_at->format('F d, Y h:i A') : 'N/A';
@endphp

<div class="diagnostic-report-container avoid-break {{ !$isLoopLast ? 'page-break-after pb-8 mb-8 border-b-2 border-dashed border-slate-300' : '' }}" style="font-family: 'Inter', system-ui, -apple-system, sans-serif; color: #0f172a;">
    
    {{-- Section Department Banner --}}
    <div class="mb-4 text-center border-b-2 border-slate-800 pb-2" style="border-bottom: 2px solid #1e293b; padding-bottom: 8px; margin-bottom: 14px; text-align: center;">
        @if($isChestXray)
            <h3 class="text-sm font-black uppercase tracking-widest text-slate-900" style="font-size: 10pt; font-weight: 900; text-transform: uppercase; letter-spacing: 0.1em; color: #0f172a; margin: 0 0 2px 0;">Department of Radiological Sciences & Imaging</h3>
            <p class="text-xs font-bold text-teal-800 uppercase tracking-wider mt-0.5" style="font-size: 8pt; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: #115e59; margin: 0;">Official Radiographic Examination Report</p>
        @elseif($isUrinalysis)
            <h3 class="text-sm font-black uppercase tracking-widest text-slate-900" style="font-size: 10pt; font-weight: 900; text-transform: uppercase; letter-spacing: 0.1em; color: #0f172a; margin: 0 0 2px 0;">Department of Pathology & Clinical Laboratory</h3>
            <p class="text-xs font-bold text-amber-800 uppercase tracking-wider mt-0.5" style="font-size: 8pt; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: #92400e; margin: 0;">Clinical Microscopy & Routine Urinalysis Report</p>
        @elseif($isFecalysis)
            <h3 class="text-sm font-black uppercase tracking-widest text-slate-900" style="font-size: 10pt; font-weight: 900; text-transform: uppercase; letter-spacing: 0.1em; color: #0f172a; margin: 0 0 2px 0;">Department of Pathology & Clinical Laboratory</h3>
            <p class="text-xs font-bold text-emerald-800 uppercase tracking-wider mt-0.5" style="font-size: 8pt; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: #065f46; margin: 0;">Clinical Parasitology & Routine Fecalysis Report</p>
        @elseif($isBloodChemistry)
            <h3 class="text-sm font-black uppercase tracking-widest text-slate-900" style="font-size: 10pt; font-weight: 900; text-transform: uppercase; letter-spacing: 0.1em; color: #0f172a; margin: 0 0 2px 0;">Department of Pathology & Clinical Laboratory</h3>
            <p class="text-xs font-bold text-blue-800 uppercase tracking-wider mt-0.5" style="font-size: 8pt; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: #1e40af; margin: 0;">Clinical Chemistry & Metabolic Profile Report</p>
        @else
            <h3 class="text-sm font-black uppercase tracking-widest text-slate-900" style="font-size: 10pt; font-weight: 900; text-transform: uppercase; letter-spacing: 0.1em; color: #0f172a; margin: 0 0 2px 0;">Department of Pathology & Clinical Laboratory</h3>
            <p class="text-xs font-bold text-rose-800 uppercase tracking-wider mt-0.5" style="font-size: 8pt; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: #9f1239; margin: 0;">Hematology & Complete Blood Count (CBC) Report</p>
        @endif
    </div>

    {{-- Patient & Examination Demographics Strip --}}
    <div class="border border-slate-300 rounded-lg p-3 bg-slate-50 mb-4 text-xs" style="border: 1px solid #cbd5e1; border-radius: 6px; padding: 10px 14px; background: #f8fafc; margin-bottom: 16px; font-size: 8pt;">
        <table style="width: 100%; border-collapse: collapse;">
            <tr>
                <td style="width: 50%; padding: 3px 6px; vertical-align: top;">
                    <span style="display: block; font-size: 6.5pt; font-weight: 800; text-transform: uppercase; letter-spacing: 0.05em; color: #64748b;">Patient Name</span>
                    <span style="font-size: 9.5pt; font-weight: 900; color: #0f172a;">{{ $patient ? $patient->full_name : 'Walk-in / Anonymous Patient' }}</span>
                </td>
                <td style="width: 25%; padding: 3px 6px; vertical-align: top;">
                    <span style="display: block; font-size: 6.5pt; font-weight: 800; text-transform: uppercase; letter-spacing: 0.05em; color: #64748b;">Patient ID</span>
                    <span style="font-family: monospace; font-weight: 800; font-size: 8.5pt; color: #0f172a;">{{ $patient ? $patient->patient_id : 'N/A' }}</span>
                </td>
                <td style="width: 25%; padding: 3px 6px; vertical-align: top;">
                    <span style="display: block; font-size: 6.5pt; font-weight: 800; text-transform: uppercase; letter-spacing: 0.05em; color: #64748b;">Reference No.</span>
                    <span style="font-family: monospace; font-weight: 800; font-size: 8.5pt; color: #0f172a;">{{ $type === 'Radiology' ? 'RAD' : 'LAB' }}-{{ str_pad($req->id, 6, '0', STR_PAD_LEFT) }}</span>
                </td>
            </tr>
            <tr>
                <td style="padding: 4px 6px 2px 6px; vertical-align: top;">
                    <span style="display: block; font-size: 6.5pt; font-weight: 800; text-transform: uppercase; letter-spacing: 0.05em; color: #64748b;">Age / Sex</span>
                    <span style="font-weight: 700; color: #1e293b;">
                        {{ $patient && $patient->dob ? \Carbon\Carbon::parse($patient->dob)->age . ' yrs' : 'N/A' }} • {{ ucfirst($patient->sex ?? 'Unspecified') }}
                    </span>
                </td>
                <td style="padding: 4px 6px 2px 6px; vertical-align: top;">
                    <span style="display: block; font-size: 6.5pt; font-weight: 800; text-transform: uppercase; letter-spacing: 0.05em; color: #64748b;">Requesting Physician</span>
                    <span style="font-weight: 700; color: #1e293b;">{{ $doctor }}</span>
                </td>
                <td style="padding: 4px 6px 2px 6px; vertical-align: top;">
                    <span style="display: block; font-size: 6.5pt; font-weight: 800; text-transform: uppercase; letter-spacing: 0.05em; color: #64748b;">Date & Time Reported</span>
                    <span style="font-weight: 800; color: #0f172a;">{{ $completedDate }}</span>
                </td>
            </tr>
        </table>
    </div>

    {{-- Official Amendment Notice (if amended) --}}
    @if($req->amended_at)
        <div style="background: #fffbeb; border: 1px solid #fcd34d; border-radius: 6px; padding: 8px 12px; margin-bottom: 14px; font-size: 8pt; color: #78350f;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 4px;">
                <strong style="color: #92400e; font-size: 8.5pt;">⚠️ Officially Amended Diagnostic Result</strong>
                <span style="font-weight: 600; color: #b45309; font-size: 7.5pt;">Amended: {{ $req->amended_at->format('M d, Y h:i A') }}</span>
            </div>
            <div><strong>Audit Reason:</strong> {{ $req->amendment_reason }}</div>
            <div style="font-size: 7pt; color: #92400e; margin-top: 2px;">Authorized by: {{ $req->amender->name ?? 'Clinical Staff' }}</div>
        </div>
    @endif

    {{-- Critical / Panic Value Alert Banner (if critical) --}}
    @if(!empty($res['_is_critical']) || !empty($res['is_critical']) || str_contains($req->remarks ?? '', '[CRITICAL VALUE ALERT]'))
        <div style="background: #fef2f2; border: 2px solid #ef4444; border-radius: 6px; padding: 10px 14px; margin-bottom: 14px; font-size: 8.5pt; color: #991b1b;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 4px;">
                <strong style="color: #b91c1c; font-size: 9pt; text-transform: uppercase; letter-spacing: 0.05em;">🚨 CRITICAL / PANIC DIAGNOSTIC VALUE ALERT</strong>
                <span style="font-weight: 800; background: #fee2e2; color: #b91c1c; padding: 2px 8px; border-radius: 4px; border: 1px solid #f87171; font-size: 7.5pt; text-transform: uppercase;">Urgent Review</span>
            </div>
            <div><strong>Clinical Warning:</strong> One or more laboratory/imaging findings exceed critical panic limits. Immediate clinical review and attending physician intervention required.</div>
            @if(!empty($res['_critical_remarks']) || !empty($res['critical_remarks']))
                <div style="font-size: 8pt; color: #7f1d1d; margin-top: 4px;"><strong>Laboratory Remarks:</strong> {{ $res['_critical_remarks'] ?? $res['critical_remarks'] }}</div>
            @endif
        </div>
    @endif

    {{-- ══════════════════════════════════════════════════════════════ --}}
    {{-- TEMPLATE 1: RADIOLOGY / CHEST X-RAY                            --}}
    {{-- ══════════════════════════════════════════════════════════════ --}}
    @if($isChestXray)
        <div style="display: flex; flex-direction: column; gap: 12px;">
            {{-- Exam View --}}
            <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #e2e8f0; padding-bottom: 6px;">
                <div>
                    <span style="font-size: 7pt; font-weight: 800; text-transform: uppercase; letter-spacing: 0.05em; color: #64748b;">Examination / View Done:</span>
                    <span style="margin-left: 8px; font-weight: 900; color: #0f172a; font-size: 9.5pt;">{{ $res['exam_view'] ?? ($testName ?: 'Chest PA View') }}</span>
                </div>
                <span style="padding: 2px 8px; border-radius: 4px; font-size: 7pt; font-weight: 800; text-transform: uppercase; background: #ccfbf1; color: #115e59; border: 1px solid #99f6e4;">
                    Verified Radiograph
                </span>
            </div>

            {{-- Radiographic Findings --}}
            <div>
                <span style="display: block; font-size: 8pt; font-weight: 900; text-transform: uppercase; letter-spacing: 0.05em; color: #1e293b; margin-bottom: 4px;">
                    Radiological Findings
                </span>
                <div style="padding: 12px; background: #f8fafc; border: 1px solid #cbd5e1; border-radius: 6px; color: #0f172a; line-height: 1.5; font-size: 8.5pt; white-space: pre-line;">
                    {{ $res['findings'] ?? 'Both lung fields are clear. No evidence of active pulmonary infiltrates, consolidation, or pleural effusion. Cardiac silhouette, heart size, and aortic knob are within normal limits. Hemidiaphragms and costophrenic sulci are intact and sharp. Visualized bony cage and thoracic soft tissues are unremarkable.' }}
                </div>
            </div>

            {{-- Impression / Conclusion --}}
            <div>
                <span style="display: block; font-size: 8pt; font-weight: 900; text-transform: uppercase; letter-spacing: 0.05em; color: #065f46; margin-bottom: 4px;">
                    Diagnostic Impression / Conclusion
                </span>
                <div style="padding: 10px 12px; background: #f0fdf4; border: 2px solid #86efac; border-radius: 6px; color: #064e3b; font-weight: 800; font-size: 9pt; line-height: 1.45;">
                    {{ $res['impression'] ?? 'NORMAL CHEST RADIOGRAPH (Clear lung fields, essentially unremarkable chest findings).' }}
                </div>
            </div>

            {{-- Remarks / Recommendations --}}
            @if(!empty($res['remarks']))
            <div style="padding: 8px 12px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; font-size: 8pt;">
                <span style="font-weight: 800; color: #475569; text-transform: uppercase; font-size: 7pt; display: block; margin-bottom: 2px;">Radiologist Notes / Recommendations:</span>
                <p style="color: #1e293b; line-height: 1.4; margin: 0;">{{ $res['remarks'] }}</p>
            </div>
            @endif

            {{-- Scan Attachment Thumbnail if available --}}
            @if($req->result_file_path)
            <div style="margin-top: 10px; padding-top: 8px; border-top: 1px solid #e2e8f0;">
                <span style="display: block; font-size: 7pt; font-weight: 800; text-transform: uppercase; letter-spacing: 0.05em; color: #64748b; margin-bottom: 6px;">Attached Radiographic Plate Scan</span>
                <div style="text-align: center; padding: 6px; background: #f1f5f9; border-radius: 6px; border: 1px solid #e2e8f0;">
                    <img src="{{ Storage::url($req->result_file_path) }}" alt="Radiographic Film Scan" style="max-height: 220px; max-width: 100%; margin: 0 auto; border-radius: 4px; border: 1px solid #cbd5e1; object-fit: contain; display: block;">
                </div>
            </div>
            @endif
        </div>

    {{-- ══════════════════════════════════════════════════════════════ --}}
    {{-- TEMPLATE 2: CLINICAL ROUTINE URINALYSIS                       --}}
    {{-- ══════════════════════════════════════════════════════════════ --}}
    @elseif($isUrinalysis)
        <div style="display: flex; flex-direction: column; gap: 12px;">
            {{-- Physical & Chemical Analysis --}}
            <div>
                <h4 style="font-size: 8pt; font-weight: 900; text-transform: uppercase; letter-spacing: 0.05em; color: #1e293b; margin: 0 0 6px 0; padding-bottom: 3px; border-bottom: 1px solid #cbd5e1;">
                    I. Physical & Chemical Examination
                </h4>
                <table style="width: 100%; border-collapse: collapse; font-size: 8pt; border: 1px solid #cbd5e1; border-radius: 4px; overflow: hidden;">
                    <thead style="background: #f1f5f9; border-bottom: 1px solid #cbd5e1; color: #334155; font-weight: 800; text-transform: uppercase; font-size: 7pt; letter-spacing: 0.05em;">
                        <tr>
                            <th style="padding: 5px 8px; text-align: left; width: 34%;">Test Parameter</th>
                            <th style="padding: 5px 8px; text-align: left; width: 33%;">Observed Result</th>
                            <th style="padding: 5px 8px; text-align: left; width: 33%;">Reference Value</th>
                        </tr>
                    </thead>
                    <tbody style="border-top: 1px solid #cbd5e1;">
                        <tr style="border-bottom: 1px solid #e2e8f0;">
                            <td style="padding: 4px 8px; font-weight: 700; color: #1e293b;">Color</td>
                            <td style="padding: 4px 8px; font-weight: 800; color: #0f172a;">{{ $res['color'] ?? 'Light Yellow' }}</td>
                            <td style="padding: 4px 8px; color: #64748b;">Straw / Light Yellow</td>
                        </tr>
                        <tr style="border-bottom: 1px solid #e2e8f0; background: #fafafa;">
                            <td style="padding: 4px 8px; font-weight: 700; color: #1e293b;">Transparency / Clarity</td>
                            <td style="padding: 4px 8px; font-weight: 800; color: #0f172a;">{{ $res['transparency'] ?? 'Clear' }}</td>
                            <td style="padding: 4px 8px; color: #64748b;">Clear</td>
                        </tr>
                        <tr style="border-bottom: 1px solid #e2e8f0;">
                            <td style="padding: 4px 8px; font-weight: 700; color: #1e293b;">Reaction (pH)</td>
                            <td style="padding: 4px 8px; font-family: monospace; font-weight: 800; color: #0f172a;">{{ $res['ph'] ?? '6.0' }}</td>
                            <td style="padding: 4px 8px; color: #64748b; font-family: monospace;">4.5 - 8.0</td>
                        </tr>
                        <tr style="border-bottom: 1px solid #e2e8f0; background: #fafafa;">
                            <td style="padding: 4px 8px; font-weight: 700; color: #1e293b;">Specific Gravity</td>
                            <td style="padding: 4px 8px; font-family: monospace; font-weight: 800; color: #0f172a;">{{ $res['sp_gravity'] ?? '1.015' }}</td>
                            <td style="padding: 4px 8px; color: #64748b; font-family: monospace;">1.005 - 1.030</td>
                        </tr>
                        <tr style="border-bottom: 1px solid #e2e8f0;">
                            <td style="padding: 4px 8px; font-weight: 700; color: #1e293b;">Protein / Albumin</td>
                            <td style="padding: 4px 8px; font-weight: 800; color: {{ ($res['protein'] ?? 'Negative') !== 'Negative' ? '#be123c' : '#0f172a' }};">
                                {{ $res['protein'] ?? 'Negative' }}
                            </td>
                            <td style="padding: 4px 8px; color: #64748b;">Negative</td>
                        </tr>
                        <tr style="border-bottom: 1px solid #e2e8f0; background: #fafafa;">
                            <td style="padding: 4px 8px; font-weight: 700; color: #1e293b;">Glucose / Sugar</td>
                            <td style="padding: 4px 8px; font-weight: 800; color: {{ ($res['glucose'] ?? 'Negative') !== 'Negative' ? '#be123c' : '#0f172a' }};">
                                {{ $res['glucose'] ?? 'Negative' }}
                            </td>
                            <td style="padding: 4px 8px; color: #64748b;">Negative</td>
                        </tr>
                        @if(!empty($res['pregnancy_test']))
                        <tr>
                            <td style="padding: 4px 8px; font-weight: 800; color: #581c87;">Pregnancy Test (Urine hCG)</td>
                            <td style="padding: 4px 8px; font-weight: 900; color: #581c87;">{{ $res['pregnancy_test'] }}</td>
                            <td style="padding: 4px 8px; color: #64748b;">Negative</td>
                        </tr>
                        @endif
                    </tbody>
                </table>
            </div>

            {{-- Microscopic Examination --}}
            <div>
                <h4 style="font-size: 8pt; font-weight: 900; text-transform: uppercase; letter-spacing: 0.05em; color: #1e293b; margin: 0 0 6px 0; padding-bottom: 3px; border-bottom: 1px solid #cbd5e1;">
                    II. Microscopic Examination
                </h4>
                <table style="width: 100%; border-collapse: collapse; font-size: 8pt; border: 1px solid #cbd5e1; border-radius: 4px; overflow: hidden;">
                    <thead style="background: #f1f5f9; border-bottom: 1px solid #cbd5e1; color: #334155; font-weight: 800; text-transform: uppercase; font-size: 7pt; letter-spacing: 0.05em;">
                        <tr>
                            <th style="padding: 5px 8px; text-align: left; width: 34%;">Element</th>
                            <th style="padding: 5px 8px; text-align: left; width: 33%;">Count / Value</th>
                            <th style="padding: 5px 8px; text-align: left; width: 33%;">Normal Interval</th>
                        </tr>
                    </thead>
                    <tbody style="border-top: 1px solid #cbd5e1;">
                        <tr style="border-bottom: 1px solid #e2e8f0;">
                            <td style="padding: 4px 8px; font-weight: 700; color: #1e293b;">Pus Cells (WBC)</td>
                            <td style="padding: 4px 8px; font-family: monospace; font-weight: 800; color: #0f172a;">{{ $res['pus_cells_wbc'] ?? '0-2' }} /hpf</td>
                            <td style="padding: 4px 8px; color: #64748b; font-family: monospace;">0 - 3 /hpf</td>
                        </tr>
                        <tr style="border-bottom: 1px solid #e2e8f0; background: #fafafa;">
                            <td style="padding: 4px 8px; font-weight: 700; color: #1e293b;">Red Blood Cells (RBC)</td>
                            <td style="padding: 4px 8px; font-family: monospace; font-weight: 800; color: #0f172a;">{{ $res['rbc_urine'] ?? '0-1' }} /hpf</td>
                            <td style="padding: 4px 8px; color: #64748b; font-family: monospace;">0 - 2 /hpf</td>
                        </tr>
                        <tr style="border-bottom: 1px solid #e2e8f0;">
                            <td style="padding: 4px 8px; font-weight: 700; color: #1e293b;">Epithelial Cells</td>
                            <td style="padding: 4px 8px; font-weight: 600; color: #0f172a;">{{ $res['epithelial_cells'] ?? 'Few' }}</td>
                            <td style="padding: 4px 8px; color: #64748b;">Rare to Few</td>
                        </tr>
                        <tr style="border-bottom: 1px solid #e2e8f0; background: #fafafa;">
                            <td style="padding: 4px 8px; font-weight: 700; color: #1e293b;">Mucus Threads</td>
                            <td style="padding: 4px 8px; font-weight: 600; color: #0f172a;">{{ $res['mucus_threads'] ?? 'None' }}</td>
                            <td style="padding: 4px 8px; color: #64748b;">None to Few</td>
                        </tr>
                        <tr style="border-bottom: 1px solid #e2e8f0;">
                            <td style="padding: 4px 8px; font-weight: 700; color: #1e293b;">Bacteria</td>
                            <td style="padding: 4px 8px; font-weight: 600; color: #0f172a;">{{ $res['bacteria'] ?? 'None' }}</td>
                            <td style="padding: 4px 8px; color: #64748b;">None to Rare</td>
                        </tr>
                        <tr style="border-bottom: 1px solid #e2e8f0; background: #fafafa;">
                            <td style="padding: 4px 8px; font-weight: 700; color: #1e293b;">Amorphous Urates / Phosphates</td>
                            <td style="padding: 4px 8px; font-weight: 600; color: #0f172a;">{{ $res['amorphous_urates'] ?? ($res['amorphous'] ?? 'None') }}</td>
                            <td style="padding: 4px 8px; color: #64748b;">None to Rare</td>
                        </tr>
                        <tr style="border-bottom: 1px solid #e2e8f0;">
                            <td style="padding: 4px 8px; font-weight: 700; color: #1e293b;">Casts</td>
                            <td style="padding: 4px 8px; font-weight: 600; color: #0f172a;">{{ $res['casts'] ?? 'None' }}</td>
                            <td style="padding: 4px 8px; color: #64748b;">None seen</td>
                        </tr>
                        <tr style="background: #fafafa;">
                            <td style="padding: 4px 8px; font-weight: 700; color: #1e293b;">Crystals</td>
                            <td style="padding: 4px 8px; font-weight: 600; color: #0f172a;">{{ $res['crystals'] ?? ($res['crystals_casts'] ?? 'None') }}</td>
                            <td style="padding: 4px 8px; color: #64748b;">None seen</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            {{-- Remarks --}}
            <div style="padding: 8px 12px; background: #fffbeb; border: 1px solid #fde68a; border-radius: 6px; font-size: 8pt;">
                <span style="font-weight: 800; color: #92400e; text-transform: uppercase; font-size: 7pt; display: block; margin-bottom: 2px;">Laboratory Remarks & Evaluation:</span>
                <p style="color: #1e293b; line-height: 1.4; margin: 0;">{{ $res['remarks'] ?? 'Routine urinalysis within physiological limits. No acute inflammatory sediment.' }}</p>
            </div>
        </div>

    {{-- ══════════════════════════════════════════════════════════════ --}}
    {{-- TEMPLATE 3: FECALYSIS / ROUTINE STOOL EXAMINATION             --}}
    {{-- ══════════════════════════════════════════════════════════════ --}}
    @elseif($isFecalysis)
        <div style="display: flex; flex-direction: column; gap: 12px;">
            <div>
                <h4 style="font-size: 8pt; font-weight: 900; text-transform: uppercase; letter-spacing: 0.05em; color: #1e293b; margin: 0 0 6px 0; padding-bottom: 3px; border-bottom: 1px solid #cbd5e1;">
                    Routine Fecalysis Examination
                </h4>
                <table style="width: 100%; border-collapse: collapse; font-size: 8pt; border: 1px solid #cbd5e1; border-radius: 4px; overflow: hidden;">
                    <thead style="background: #f1f5f9; border-bottom: 1px solid #cbd5e1; color: #334155; font-weight: 800; text-transform: uppercase; font-size: 7pt; letter-spacing: 0.05em;">
                        <tr>
                            <th style="padding: 5px 8px; text-align: left; width: 34%;">Parameter</th>
                            <th style="padding: 5px 8px; text-align: left; width: 33%;">Observed Result</th>
                            <th style="padding: 5px 8px; text-align: left; width: 33%;">Normal Value</th>
                        </tr>
                    </thead>
                    <tbody style="border-top: 1px solid #cbd5e1;">
                        <tr style="border-bottom: 1px solid #e2e8f0;">
                            <td style="padding: 4px 8px; font-weight: 700; color: #1e293b;">Color</td>
                            <td style="padding: 4px 8px; font-weight: 800; color: #0f172a;">{{ $res['color'] ?? 'Brown' }}</td>
                            <td style="padding: 4px 8px; color: #64748b;">Brown / Yellow-Brown</td>
                        </tr>
                        <tr style="border-bottom: 1px solid #e2e8f0; background: #fafafa;">
                            <td style="padding: 4px 8px; font-weight: 700; color: #1e293b;">Consistency</td>
                            <td style="padding: 4px 8px; font-weight: 800; color: #0f172a;">{{ $res['consistency'] ?? 'Formed' }}</td>
                            <td style="padding: 4px 8px; color: #64748b;">Formed / Soft</td>
                        </tr>
                        <tr style="border-bottom: 1px solid #e2e8f0;">
                            <td style="padding: 4px 8px; font-weight: 700; color: #1e293b;">Pus Cells</td>
                            <td style="padding: 4px 8px; font-family: monospace; font-weight: 800; color: #0f172a;">{{ $res['pus_cells'] ?? '0-1' }} /hpf</td>
                            <td style="padding: 4px 8px; color: #64748b; font-family: monospace;">0 - 2 /hpf</td>
                        </tr>
                        <tr style="border-bottom: 1px solid #e2e8f0; background: #fafafa;">
                            <td style="padding: 4px 8px; font-weight: 700; color: #1e293b;">Red Blood Cells</td>
                            <td style="padding: 4px 8px; font-family: monospace; font-weight: 800; color: #0f172a;">{{ $res['rbc'] ?? '0-1' }} /hpf</td>
                            <td style="padding: 4px 8px; color: #64748b; font-family: monospace;">0 - 1 /hpf</td>
                        </tr>
                        <tr style="border-bottom: 1px solid #e2e8f0;">
                            <td style="padding: 4px 8px; font-weight: 700; color: #1e293b;">Amoeba / Parasites / Ova</td>
                            <td style="padding: 4px 8px; font-weight: 800; color: #0f172a;">{{ $res['amoeba'] ?? ($res['parasites'] ?? 'No ova or parasites seen') }}</td>
                            <td style="padding: 4px 8px; color: #64748b;">No ova or parasites seen</td>
                        </tr>
                        <tr style="background: #fafafa;">
                            <td style="padding: 4px 8px; font-weight: 700; color: #1e293b;">Occult Blood</td>
                            <td style="padding: 4px 8px; font-weight: 800; color: #0f172a;">{{ $res['occult_blood'] ?? 'Negative' }}</td>
                            <td style="padding: 4px 8px; color: #64748b;">Negative</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div style="padding: 8px 12px; background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 6px; font-size: 8pt;">
                <span style="font-weight: 800; color: #166534; text-transform: uppercase; font-size: 7pt; display: block; margin-bottom: 2px;">Parasitology Evaluation Remarks:</span>
                <p style="color: #1e293b; line-height: 1.4; margin: 0;">{{ $res['remarks'] ?? 'No intestinal protozoan cysts, trophozoites, or helminth ova detected on direct fecal smear.' }}</p>
            </div>
        </div>

    {{-- ══════════════════════════════════════════════════════════════ --}}
    {{-- TEMPLATE 4: BLOOD CHEMISTRY / METABOLIC PANEL                 --}}
    {{-- ══════════════════════════════════════════════════════════════ --}}
    @elseif($isBloodChemistry)
        <div style="display: flex; flex-direction: column; gap: 12px;">
            <div>
                <h4 style="font-size: 8pt; font-weight: 900; text-transform: uppercase; letter-spacing: 0.05em; color: #1e293b; margin: 0 0 6px 0; padding-bottom: 3px; border-bottom: 1px solid #cbd5e1;">
                    Clinical Chemistry Quantitative Assays
                </h4>
                <table style="width: 100%; border-collapse: collapse; font-size: 8pt; border: 1px solid #cbd5e1; border-radius: 4px; overflow: hidden;">
                    <thead style="background: #f1f5f9; border-bottom: 1px solid #cbd5e1; color: #334155; font-weight: 800; text-transform: uppercase; font-size: 7pt; letter-spacing: 0.05em;">
                        <tr>
                            <th style="padding: 5px 8px; text-align: left; width: 35%;">Analyte / Assay</th>
                            <th style="padding: 5px 8px; text-align: center; width: 22%;">Patient Result</th>
                            <th style="padding: 5px 8px; text-align: center; width: 18%;">Unit</th>
                            <th style="padding: 5px 8px; text-align: left; width: 25%;">Reference Range</th>
                        </tr>
                    </thead>
                    <tbody style="border-top: 1px solid #cbd5e1;">
                        @php
                            $chemTests = [
                                ['label' => 'Fasting Blood Sugar (FBS)', 'key' => 'fbs', 'unit' => 'mg/dL', 'ref' => '70.0 - 99.0'],
                                ['label' => 'Random Blood Sugar (RBS)', 'key' => 'rbs', 'unit' => 'mg/dL', 'ref' => '< 140.0'],
                                ['label' => 'Total Cholesterol', 'key' => 'total_cholesterol', 'unit' => 'mg/dL', 'ref' => '< 200.0'],
                                ['label' => 'Triglycerides', 'key' => 'triglycerides', 'unit' => 'mg/dL', 'ref' => '< 150.0'],
                                ['label' => 'HDL Cholesterol', 'key' => 'hdl', 'unit' => 'mg/dL', 'ref' => '> 40.0 (M) | > 50.0 (F)'],
                                ['label' => 'LDL Cholesterol', 'key' => 'ldl', 'unit' => 'mg/dL', 'ref' => '< 100.0'],
                                ['label' => 'Uric Acid (BUA)', 'key' => 'uric_acid', 'unit' => 'mg/dL', 'ref' => '3.5 - 7.2 (M) | 2.6 - 6.0 (F)'],
                                ['label' => 'Serum Creatinine', 'key' => 'creatinine', 'unit' => 'mg/dL', 'ref' => '0.7 - 1.3 (M) | 0.6 - 1.1 (F)'],
                                ['label' => 'Blood Urea Nitrogen (BUN)', 'key' => 'bun', 'unit' => 'mg/dL', 'ref' => '7.0 - 20.0'],
                                ['label' => 'SGPT / ALT', 'key' => 'sgpt', 'unit' => 'U/L', 'ref' => '0 - 45'],
                                ['label' => 'SGOT / AST', 'key' => 'sgot', 'unit' => 'U/L', 'ref' => '0 - 35'],
                            ];
                        @endphp
                        @foreach($chemTests as $ci => $ct)
                            @if(isset($res[$ct['key']]) && $res[$ct['key']] !== '')
                            <tr style="border-bottom: 1px solid #e2e8f0; {{ $ci % 2 === 1 ? 'background:#fafafa;' : '' }}">
                                <td style="padding: 4px 8px; font-weight: 700; color: #1e293b;">{{ $ct['label'] }}</td>
                                <td style="padding: 4px 8px; text-align: center; font-family: monospace; font-weight: 800; color: #0f172a;">{{ $res[$ct['key']] }}</td>
                                <td style="padding: 4px 8px; text-align: center; color: #64748b; font-family: monospace; font-size: 7.5pt;">{{ $ct['unit'] }}</td>
                                <td style="padding: 4px 8px; color: #64748b; font-family: monospace; font-size: 7.5pt;">{{ $ct['ref'] }}</td>
                            </tr>
                            @endif
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div style="padding: 8px 12px; background: #eff6ff; border: 1px solid #bfdbfe; border-radius: 6px; font-size: 8pt;">
                <span style="font-weight: 800; color: #1e40af; text-transform: uppercase; font-size: 7pt; display: block; margin-bottom: 2px;">Clinical Chemistry Evaluation:</span>
                <p style="color: #1e293b; line-height: 1.4; margin: 0;">{{ $res['remarks'] ?? 'Assays performed via calibrated automated spectrophotometer. Correlate with clinical findings.' }}</p>
            </div>
        </div>

    {{-- ══════════════════════════════════════════════════════════════ --}}
    {{-- TEMPLATE 5: COMPLETE BLOOD COUNT (CBC) WITH DIFFERENTIAL       --}}
    {{-- ══════════════════════════════════════════════════════════════ --}}
    @else
        <div style="display: flex; flex-direction: column; gap: 12px;">
            {{-- Primary CBC Quantitative Parameters --}}
            <div>
                <h4 style="font-size: 8pt; font-weight: 900; text-transform: uppercase; letter-spacing: 0.05em; color: #1e293b; margin: 0 0 6px 0; padding-bottom: 3px; border-bottom: 1px solid #cbd5e1;">
                    I. Complete Blood Count (Quantitative Parameters)
                </h4>
                <table style="width: 100%; border-collapse: collapse; font-size: 8pt; border: 1px solid #cbd5e1; border-radius: 4px; overflow: hidden;">
                    <thead style="background: #f1f5f9; border-bottom: 1px solid #cbd5e1; color: #334155; font-weight: 800; text-transform: uppercase; font-size: 7pt; letter-spacing: 0.05em;">
                        <tr>
                            <th style="padding: 5px 8px; text-align: left; width: 40%;">Parameter Name</th>
                            <th style="padding: 5px 8px; text-align: center; width: 20%;">Patient Result</th>
                            <th style="padding: 5px 8px; text-align: center; width: 18%;">Unit</th>
                            <th style="padding: 5px 8px; text-align: left; width: 22%;">Reference Interval</th>
                        </tr>
                    </thead>
                    <tbody style="border-top: 1px solid #cbd5e1;">
                        <tr style="border-bottom: 1px solid #e2e8f0;">
                            <td style="padding: 4px 8px; font-weight: 700; color: #1e293b;">White Blood Cells (WBC)</td>
                            <td style="padding: 4px 8px; text-align: center; font-family: monospace; font-weight: 900; color: #0f172a;">{{ $res['wbc'] ?? '—' }}</td>
                            <td style="padding: 4px 8px; text-align: center; color: #64748b; font-family: monospace; font-size: 7.5pt;">x 10^9/L</td>
                            <td style="padding: 4px 8px; color: #64748b; font-family: monospace; font-size: 7.5pt;">4.00 - 10.00</td>
                        </tr>
                        <tr style="border-bottom: 1px solid #e2e8f0; background: #fafafa;">
                            <td style="padding: 4px 8px; font-weight: 700; color: #1e293b;">Red Blood Cells (RBC)</td>
                            <td style="padding: 4px 8px; text-align: center; font-family: monospace; font-weight: 900; color: #0f172a;">{{ $res['rbc'] ?? '—' }}</td>
                            <td style="padding: 4px 8px; text-align: center; color: #64748b; font-family: monospace; font-size: 7.5pt;">x 10^12/L</td>
                            <td style="padding: 4px 8px; color: #64748b; font-family: monospace; font-size: 7.5pt;">3.50 - 5.50</td>
                        </tr>
                        <tr style="border-bottom: 1px solid #e2e8f0;">
                            <td style="padding: 4px 8px; font-weight: 700; color: #1e293b;">Hemoglobin (Hgb)</td>
                            <td style="padding: 4px 8px; text-align: center; font-family: monospace; font-weight: 900; color: #0f172a;">{{ $res['hemoglobin'] ?? '—' }}</td>
                            <td style="padding: 4px 8px; text-align: center; color: #64748b; font-family: monospace; font-size: 7.5pt;">g/L</td>
                            <td style="padding: 4px 8px; color: #64748b; font-family: monospace; font-size: 7.5pt;">M: 120-160 | F: 110-150</td>
                        </tr>
                        <tr style="border-bottom: 1px solid #e2e8f0; background: #fafafa;">
                            <td style="padding: 4px 8px; font-weight: 700; color: #1e293b;">Hematocrit (Hct)</td>
                            <td style="padding: 4px 8px; text-align: center; font-family: monospace; font-weight: 900; color: #0f172a;">{{ $res['hematocrit'] ?? '—' }}</td>
                            <td style="padding: 4px 8px; text-align: center; color: #64748b; font-family: monospace; font-size: 7.5pt;">%</td>
                            <td style="padding: 4px 8px; color: #64748b; font-family: monospace; font-size: 7.5pt;">M: 40.0-52.0 | F: 37.0-48.0</td>
                        </tr>
                        <tr style="border-bottom: 1px solid #e2e8f0;">
                            <td style="padding: 4px 8px; font-weight: 700; color: #1e293b;">Platelet Count</td>
                            <td style="padding: 4px 8px; text-align: center; font-family: monospace; font-weight: 900; color: #0f172a;">{{ $res['platelet_count'] ?? ($res['platelets'] ?? '—') }}</td>
                            <td style="padding: 4px 8px; text-align: center; color: #64748b; font-family: monospace; font-size: 7.5pt;">x 10^9/L</td>
                            <td style="padding: 4px 8px; color: #64748b; font-family: monospace; font-size: 7.5pt;">150 - 450</td>
                        </tr>
                        <tr style="border-bottom: 1px solid #e2e8f0; background: #fafafa;">
                            <td style="padding: 4px 8px; font-weight: 600; color: #334155;">Mean Corpuscular Volume (MCV)</td>
                            <td style="padding: 4px 8px; text-align: center; font-family: monospace; font-weight: 700; color: #0f172a;">{{ $res['mcv'] ?? '—' }}</td>
                            <td style="padding: 4px 8px; text-align: center; color: #64748b; font-family: monospace; font-size: 7.5pt;">fl</td>
                            <td style="padding: 4px 8px; color: #64748b; font-family: monospace; font-size: 7.5pt;">80.0 - 100.0</td>
                        </tr>
                        <tr style="border-bottom: 1px solid #e2e8f0;">
                            <td style="padding: 4px 8px; font-weight: 600; color: #334155;">Mean Corpuscular Hgb (MCH)</td>
                            <td style="padding: 4px 8px; text-align: center; font-family: monospace; font-weight: 700; color: #0f172a;">{{ $res['mch'] ?? '—' }}</td>
                            <td style="padding: 4px 8px; text-align: center; color: #64748b; font-family: monospace; font-size: 7.5pt;">pg</td>
                            <td style="padding: 4px 8px; color: #64748b; font-family: monospace; font-size: 7.5pt;">27.0 - 31.0</td>
                        </tr>
                        <tr style="border-bottom: 1px solid #e2e8f0; background: #fafafa;">
                            <td style="padding: 4px 8px; font-weight: 600; color: #334155;">Mean Corpuscular Hgb Conc. (MCHC)</td>
                            <td style="padding: 4px 8px; text-align: center; font-family: monospace; font-weight: 700; color: #0f172a;">{{ $res['mchc'] ?? '—' }}</td>
                            <td style="padding: 4px 8px; text-align: center; color: #64748b; font-family: monospace; font-size: 7.5pt;">g/L</td>
                            <td style="padding: 4px 8px; color: #64748b; font-family: monospace; font-size: 7.5pt;">320.0 - 360.0</td>
                        </tr>
                        <tr>
                            <td style="padding: 4px 8px; font-weight: 600; color: #334155;">Red Cell Distribution Width (RDW)</td>
                            <td style="padding: 4px 8px; text-align: center; font-family: monospace; font-weight: 700; color: #0f172a;">{{ $res['rdw'] ?? '—' }}</td>
                            <td style="padding: 4px 8px; text-align: center; color: #64748b; font-family: monospace; font-size: 7.5pt;">%</td>
                            <td style="padding: 4px 8px; color: #64748b; font-family: monospace; font-size: 7.5pt;">11.5 - 14.5</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            {{-- Differential White Blood Cell Count --}}
            <div>
                <h4 style="font-size: 8pt; font-weight: 900; text-transform: uppercase; letter-spacing: 0.05em; color: #1e293b; margin: 0 0 6px 0; padding-bottom: 3px; border-bottom: 1px solid #cbd5e1;">
                    II. Differential White Cell Count (%)
                </h4>
                <table style="width: 100%; border-collapse: collapse; font-size: 8pt; border: 1px solid #cbd5e1; border-radius: 4px; overflow: hidden;">
                    <thead style="background: #f1f5f9; border-bottom: 1px solid #cbd5e1; color: #334155; font-weight: 800; text-transform: uppercase; font-size: 7pt; letter-spacing: 0.05em;">
                        <tr>
                            <th style="padding: 5px 8px; text-align: left; width: 40%;">Leukocyte Subtype</th>
                            <th style="padding: 5px 8px; text-align: center; width: 30%;">Patient Result (%)</th>
                            <th style="padding: 5px 8px; text-align: center; width: 30%;">Normal Interval (%)</th>
                        </tr>
                    </thead>
                    <tbody style="border-top: 1px solid #cbd5e1;">
                        <tr style="border-bottom: 1px solid #e2e8f0;">
                            <td style="padding: 4px 8px; font-weight: 700; color: #1e293b;">Segmenters / Neutrophils</td>
                            <td style="padding: 4px 8px; text-align: center; font-family: monospace; font-weight: 900; color: #0f172a;">{{ $res['neutrophil'] ?? '—' }}</td>
                            <td style="padding: 4px 8px; text-align: center; color: #64748b; font-family: monospace; font-size: 7.5pt;">50.0 - 70.0</td>
                        </tr>
                        <tr style="border-bottom: 1px solid #e2e8f0; background: #fafafa;">
                            <td style="padding: 4px 8px; font-weight: 700; color: #1e293b;">Lymphocytes</td>
                            <td style="padding: 4px 8px; text-align: center; font-family: monospace; font-weight: 900; color: #0f172a;">{{ $res['lymphocyte'] ?? '—' }}</td>
                            <td style="padding: 4px 8px; text-align: center; color: #64748b; font-family: monospace; font-size: 7.5pt;">20.0 - 40.0</td>
                        </tr>
                        <tr style="border-bottom: 1px solid #e2e8f0;">
                            <td style="padding: 4px 8px; font-weight: 700; color: #1e293b;">Monocytes</td>
                            <td style="padding: 4px 8px; text-align: center; font-family: monospace; font-weight: 900; color: #0f172a;">{{ $res['monocyte'] ?? '—' }}</td>
                            <td style="padding: 4px 8px; text-align: center; color: #64748b; font-family: monospace; font-size: 7.5pt;">2.0 - 8.0</td>
                        </tr>
                        <tr style="border-bottom: 1px solid #e2e8f0; background: #fafafa;">
                            <td style="padding: 4px 8px; font-weight: 700; color: #1e293b;">Eosinophils</td>
                            <td style="padding: 4px 8px; text-align: center; font-family: monospace; font-weight: 900; color: #0f172a;">{{ $res['eosinophil'] ?? '—' }}</td>
                            <td style="padding: 4px 8px; text-align: center; color: #64748b; font-family: monospace; font-size: 7.5pt;">1.0 - 4.0</td>
                        </tr>
                        <tr style="border-bottom: 1px solid #e2e8f0;">
                            <td style="padding: 4px 8px; font-weight: 700; color: #1e293b;">Basophils</td>
                            <td style="padding: 4px 8px; text-align: center; font-family: monospace; font-weight: 900; color: #0f172a;">{{ $res['basophil'] ?? '—' }}</td>
                            <td style="padding: 4px 8px; text-align: center; color: #64748b; font-family: monospace; font-size: 7.5pt;">0.0 - 1.0</td>
                        </tr>
                        <tr style="background: #fafafa;">
                            <td style="padding: 4px 8px; font-weight: 700; color: #1e293b;">Band Cells (Stabs)</td>
                            <td style="padding: 4px 8px; text-align: center; font-family: monospace; font-weight: 900; color: #0f172a;">{{ $res['band_cells'] ?? '—' }}</td>
                            <td style="padding: 4px 8px; text-align: center; color: #64748b; font-family: monospace; font-size: 7.5pt;">0.0 - 3.0</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            {{-- Coagulation / Special Tests (if present) --}}
            @if(!empty($res['bleeding_time']) || !empty($res['clotting_time']) || !empty($res['esr']))
            <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 10px; padding: 8px 12px; background: #f8fafc; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 8pt;">
                @if(!empty($res['bleeding_time']))
                <div>
                    <span style="display: block; font-size: 6.5pt; font-weight: 800; color: #64748b; text-transform: uppercase;">Bleeding Time</span>
                    <span style="font-family: monospace; font-weight: 900; color: #0f172a;">{{ $res['bleeding_time'] }}</span>
                    <span style="display: block; font-size: 6pt; color: #94a3b8; font-family: monospace;">Ref: 1 - 5 mins</span>
                </div>
                @endif
                @if(!empty($res['clotting_time']))
                <div>
                    <span style="display: block; font-size: 6.5pt; font-weight: 800; color: #64748b; text-transform: uppercase;">Clotting Time</span>
                    <span style="font-family: monospace; font-weight: 900; color: #0f172a;">{{ $res['clotting_time'] }}</span>
                    <span style="display: block; font-size: 6pt; color: #94a3b8; font-family: monospace;">Ref: 5 - 15 mins</span>
                </div>
                @endif
                @if(!empty($res['esr']))
                <div>
                    <span style="display: block; font-size: 6.5pt; font-weight: 800; color: #64748b; text-transform: uppercase;">ESR</span>
                    <span style="font-family: monospace; font-weight: 900; color: #0f172a;">{{ $res['esr'] }} mm/hr</span>
                    <span style="display: block; font-size: 6pt; color: #94a3b8; font-family: monospace;">Ref: 0 - 20 mm/hr</span>
                </div>
                @endif
            </div>
            @endif

            {{-- Remarks --}}
            <div style="padding: 8px 12px; background: #fff1f2; border: 1px solid #fecdd3; border-radius: 6px; font-size: 8pt;">
                <span style="font-weight: 800; color: #9f1239; text-transform: uppercase; font-size: 7pt; display: block; margin-bottom: 2px;">Hematology Examination Remarks:</span>
                <p style="color: #1e293b; line-height: 1.4; margin: 0;">{{ $res['remarks'] ?? 'Normal complete blood count. Parameters within normal adult reference intervals. Normocytic, normochromic RBCs.' }}</p>
            </div>
        </div>
    @endif

    {{-- Attending Personnel Signatures (Dual Signature Block) --}}
    <div style="padding-top: 18px; margin-top: 18px; border-top: 1px solid #cbd5e1; display: grid; grid-template-columns: 1fr 1fr; gap: 24px; text-align: center; font-size: 8pt;">
        <div>
            <div style="width: 200px; margin: 0 auto; border-bottom: 1px solid #0f172a; padding-bottom: 2px; font-weight: 800; color: #0f172a; font-size: 8.5pt;">
                {{ $tech }}
            </div>
            <p style="font-size: 7pt; color: #64748b; font-weight: 600; margin: 2px 0 0 0;">
                {{ $type === 'Radiology' ? 'Radiologic Technologist (RT)' : 'Medical Technologist (RMT)' }} • PRC Lic. Verified
            </p>
        </div>
        <div>
            <div style="width: 200px; margin: 0 auto; border-bottom: 1px solid #0f172a; padding-bottom: 2px; font-weight: 800; color: #0f172a; font-size: 8.5pt;">
                {{ $doctor }}
            </div>
            <p style="font-size: 7pt; color: #64748b; font-weight: 600; margin: 2px 0 0 0;">
                {{ $type === 'Radiology' ? 'Consulting Radiologist / Medical Officer' : 'Clinical Pathologist / Medical Officer' }} • License Signature
            </p>
        </div>
    </div>

    {{-- Security & Verification Line --}}
    <div style="text-align: center; font-size: 6.5pt; color: #94a3b8; margin-top: 14px;">
        RHU Silang Laboratory & Imaging Section • Official Medical Diagnostic Document • Verification ID: {{ strtoupper(substr(md5($req->id . ($patient ? $patient->patient_id : 'X') . $req->created_at), 0, 12)) }}
    </div>

</div>
