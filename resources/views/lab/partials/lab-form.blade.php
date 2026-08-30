@php
    $testLower = strtolower($req->test_name ?? '');
    $isHematology = str_contains($testLower, 'complete blood') || str_contains($testLower, 'cbc') || str_contains($testLower, 'hematology') || str_contains($testLower, 'blood count');
    $isBloodTyping = str_contains($testLower, 'type') || str_contains($testLower, 'typing') || str_contains($testLower, 'blood group');
    $isUrinalysis = str_contains($testLower, 'urine') || str_contains($testLower, 'urinalysis');
    $isFecalysis = str_contains($testLower, 'fecal') || str_contains($testLower, 'stool') || str_contains($testLower, 'fecalysis');
    $isBloodChemistry = str_contains($testLower, 'chemistry') || str_contains($testLower, 'fbs') || str_contains($testLower, 'glucose') || str_contains($testLower, 'cholesterol') || str_contains($testLower, 'lipid') || str_contains($testLower, 'uric') || str_contains($testLower, 'creatinine');
@endphp

@if($isHematology)
    <!-- ═══════════════════ HEMATOLOGY / CBC FORM ═══════════════════ -->
    <div class="space-y-4">
        <div class="bg-rose-50 dark:bg-rose-950/30 border border-rose-200 dark:border-rose-800/50 p-3 rounded-xl">
            <h4 class="text-xs font-black text-rose-800 dark:text-rose-400 uppercase tracking-wider flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path></svg>
                Hematology / Complete Blood Count (CBC)
            </h4>
        </div>

        <div class="overflow-x-auto border border-slate-200 dark:border-slate-700 rounded-xl">
            <table class="w-full text-xs text-left">
                <thead class="bg-slate-100 dark:bg-slate-700/60 text-slate-700 dark:text-slate-300 font-extrabold uppercase text-[10px] tracking-wider border-b border-slate-200 dark:border-slate-700">
                    <tr>
                        <th class="p-2.5 w-1/3">Test Parameter</th>
                        <th class="p-2.5 w-1/3">Result</th>
                        <th class="p-2.5 w-1/3">Reference Interval</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-700/50">
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/40">
                        <td class="p-2.5 font-bold text-slate-800 dark:text-slate-200">RBC (Red Blood Cells)</td>
                        <td class="p-2">
                            <input type="text" name="results[rbc]" placeholder="e.g. 4.50" class="w-full bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-600 rounded-lg px-2.5 py-1 text-xs text-slate-900 dark:text-white font-medium focus:ring-1 focus:ring-blue-500">
                        </td>
                        <td class="p-2.5 text-slate-500 dark:text-slate-400 font-mono text-[11px]">3.50 - 5.50 x 10^12/L</td>
                    </tr>
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/40">
                        <td class="p-2.5 font-bold text-slate-800 dark:text-slate-200">Hemoglobin</td>
                        <td class="p-2">
                            <input type="text" name="results[hemoglobin]" placeholder="e.g. 135" class="w-full bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-600 rounded-lg px-2.5 py-1 text-xs text-slate-900 dark:text-white font-medium focus:ring-1 focus:ring-blue-500">
                        </td>
                        <td class="p-2.5 text-slate-500 dark:text-slate-400 font-mono text-[11px]">M: 120-160 | F: 110-150 g/L</td>
                    </tr>
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/40">
                        <td class="p-2.5 font-bold text-slate-800 dark:text-slate-200">Hematocrit</td>
                        <td class="p-2">
                            <input type="text" name="results[hematocrit]" placeholder="e.g. 0.42" class="w-full bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-600 rounded-lg px-2.5 py-1 text-xs text-slate-900 dark:text-white font-medium focus:ring-1 focus:ring-blue-500">
                        </td>
                        <td class="p-2.5 text-slate-500 dark:text-slate-400 font-mono text-[11px]">M: 0.40-0.50 | F: 0.37-0.48 /L</td>
                    </tr>
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/40">
                        <td class="p-2.5 font-bold text-slate-800 dark:text-slate-200">MCV</td>
                        <td class="p-2">
                            <input type="text" name="results[mcv]" placeholder="e.g. 85.0" class="w-full bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-600 rounded-lg px-2.5 py-1 text-xs text-slate-900 dark:text-white font-medium focus:ring-1 focus:ring-blue-500">
                        </td>
                        <td class="p-2.5 text-slate-500 dark:text-slate-400 font-mono text-[11px]">80 - 100 fl</td>
                    </tr>
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/40">
                        <td class="p-2.5 font-bold text-slate-800 dark:text-slate-200">MCH</td>
                        <td class="p-2">
                            <input type="text" name="results[mch]" placeholder="e.g. 28.5" class="w-full bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-600 rounded-lg px-2.5 py-1 text-xs text-slate-900 dark:text-white font-medium focus:ring-1 focus:ring-blue-500">
                        </td>
                        <td class="p-2.5 text-slate-500 dark:text-slate-400 font-mono text-[11px]">27 - 31 pg</td>
                    </tr>
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/40">
                        <td class="p-2.5 font-bold text-slate-800 dark:text-slate-200">MCHC</td>
                        <td class="p-2">
                            <input type="text" name="results[mchc]" placeholder="e.g. 335" class="w-full bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-600 rounded-lg px-2.5 py-1 text-xs text-slate-900 dark:text-white font-medium focus:ring-1 focus:ring-blue-500">
                        </td>
                        <td class="p-2.5 text-slate-500 dark:text-slate-400 font-mono text-[11px]">320 - 360 g/L</td>
                    </tr>
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/40">
                        <td class="p-2.5 font-bold text-slate-800 dark:text-slate-200">WBC (White Blood Cells)</td>
                        <td class="p-2">
                            <input type="text" name="results[wbc]" placeholder="e.g. 6.5" class="w-full bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-600 rounded-lg px-2.5 py-1 text-xs text-slate-900 dark:text-white font-medium focus:ring-1 focus:ring-blue-500">
                        </td>
                        <td class="p-2.5 text-slate-500 dark:text-slate-400 font-mono text-[11px]">4.0 - 10.0 x 10^9/L</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- DIFFERENTIAL COUNT SECTION -->
        <div class="pt-2">
            <h5 class="text-[11px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">Differential Count (%)</h5>
            <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                <div class="bg-slate-50 dark:bg-slate-900/60 p-2.5 rounded-xl border border-slate-200 dark:border-slate-700">
                    <label class="block text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase">Neutrophil (40-75%)</label>
                    <input type="text" name="results[neutrophil]" placeholder="%" class="mt-1 w-full bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-600 rounded-lg px-2 py-1 text-xs text-slate-900 dark:text-white font-semibold">
                </div>
                <div class="bg-slate-50 dark:bg-slate-900/60 p-2.5 rounded-xl border border-slate-200 dark:border-slate-700">
                    <label class="block text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase">Lymphocyte (20-40%)</label>
                    <input type="text" name="results[lymphocyte]" placeholder="%" class="mt-1 w-full bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-600 rounded-lg px-2 py-1 text-xs text-slate-900 dark:text-white font-semibold">
                </div>
                <div class="bg-slate-50 dark:bg-slate-900/60 p-2.5 rounded-xl border border-slate-200 dark:border-slate-700">
                    <label class="block text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase">Monocyte (0-6%)</label>
                    <input type="text" name="results[monocyte]" placeholder="%" class="mt-1 w-full bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-600 rounded-lg px-2 py-1 text-xs text-slate-900 dark:text-white font-semibold">
                </div>
                <div class="bg-slate-50 dark:bg-slate-900/60 p-2.5 rounded-xl border border-slate-200 dark:border-slate-700">
                    <label class="block text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase">Eosinophil (0-4%)</label>
                    <input type="text" name="results[eosinophil]" placeholder="%" class="mt-1 w-full bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-600 rounded-lg px-2 py-1 text-xs text-slate-900 dark:text-white font-semibold">
                </div>
                <div class="bg-slate-50 dark:bg-slate-900/60 p-2.5 rounded-xl border border-slate-200 dark:border-slate-700">
                    <label class="block text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase">Basophil (0-4%)</label>
                    <input type="text" name="results[basophil]" placeholder="%" class="mt-1 w-full bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-600 rounded-lg px-2 py-1 text-xs text-slate-900 dark:text-white font-semibold">
                </div>
                <div class="bg-slate-50 dark:bg-slate-900/60 p-2.5 rounded-xl border border-slate-200 dark:border-slate-700">
                    <label class="block text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase">Nucleated RBC (0-0.5)</label>
                    <input type="text" name="results[nucleated_rbc]" placeholder="ratio" class="mt-1 w-full bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-600 rounded-lg px-2 py-1 text-xs text-slate-900 dark:text-white font-semibold">
                </div>
            </div>
        </div>

        <!-- COAGULATION & PLATELETS -->
        <div class="pt-2">
            <h5 class="text-[11px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">Platelet & Coagulation</h5>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                <div class="bg-slate-50 dark:bg-slate-900/60 p-2.5 rounded-xl border border-slate-200 dark:border-slate-700">
                    <label class="block text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase">Platelet Count</label>
                    <input type="text" name="results[platelet_count]" placeholder="150-450 x 10^9/L" class="mt-1 w-full bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-600 rounded-lg px-2 py-1 text-xs text-slate-900 dark:text-white font-semibold">
                </div>
                <div class="bg-slate-50 dark:bg-slate-900/60 p-2.5 rounded-xl border border-slate-200 dark:border-slate-700">
                    <label class="block text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase">ESR</label>
                    <input type="text" name="results[esr]" placeholder="mm/hr" class="mt-1 w-full bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-600 rounded-lg px-2 py-1 text-xs text-slate-900 dark:text-white font-semibold">
                </div>
                <div class="bg-slate-50 dark:bg-slate-900/60 p-2.5 rounded-xl border border-slate-200 dark:border-slate-700">
                    <label class="block text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase">Bleeding Time</label>
                    <input type="text" name="results[bleeding_time]" placeholder="1 - 5 mins" class="mt-1 w-full bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-600 rounded-lg px-2 py-1 text-xs text-slate-900 dark:text-white font-semibold">
                </div>
                <div class="bg-slate-50 dark:bg-slate-900/60 p-2.5 rounded-xl border border-slate-200 dark:border-slate-700">
                    <label class="block text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase">Clotting Time</label>
                    <input type="text" name="results[clotting_time]" placeholder="< 15 mins" class="mt-1 w-full bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-600 rounded-lg px-2 py-1 text-xs text-slate-900 dark:text-white font-semibold">
                </div>
            </div>
        </div>

        <div>
            <label class="block text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1">Pathologist / MedTech Remarks</label>
            <textarea name="results[remarks]" rows="2" placeholder="e.g. Suggest ff up testing for monitoring, please correlate clinically." class="w-full bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-600 rounded-xl p-2.5 text-xs text-slate-900 dark:text-white"></textarea>
        </div>
    </div>

@elseif($isBloodTyping)
    <!-- ═══════════════════ BLOOD TYPING FORM ═══════════════════ -->
    <div class="space-y-4">
        <div class="bg-red-50 dark:bg-red-950/30 border border-red-200 dark:border-red-800/50 p-3 rounded-xl">
            <h4 class="text-xs font-black text-red-800 dark:text-red-400 uppercase tracking-wider">Blood Typing & Rh Determination</h4>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">ABO Blood Group <span class="text-red-500">*</span></label>
                <select name="results[abo_group]" required class="w-full bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-600 rounded-xl px-3 py-2 text-sm text-slate-900 dark:text-white font-bold focus:ring-2 focus:ring-blue-500">
                    <option value="">Select Blood Group</option>
                    <option value="Type A">Type A</option>
                    <option value="Type B">Type B</option>
                    <option value="Type AB">Type AB</option>
                    <option value="Type O">Type O</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Rh Factor / Type <span class="text-red-500">*</span></label>
                <select name="results[rh_factor]" required class="w-full bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-600 rounded-xl px-3 py-2 text-sm text-slate-900 dark:text-white font-bold focus:ring-2 focus:ring-blue-500">
                    <option value="">Select Rh Factor</option>
                    <option value="Rh Positive (+)">Rh Positive (+)</option>
                    <option value="Rh Negative (-)">Rh Negative (-)</option>
                </select>
            </div>
        </div>

        <div>
            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Remarks / Subtype</label>
            <input type="text" name="results[remarks]" placeholder="e.g. Du / Weak D test negative" class="w-full bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-600 rounded-xl px-3 py-2 text-sm text-slate-900 dark:text-white">
        </div>
    </div>

@elseif($isUrinalysis)
    <!-- ═══════════════════ URINALYSIS FORM ═══════════════════ -->
    <div class="space-y-4">
        <div class="bg-amber-50 dark:bg-amber-950/30 border border-amber-200 dark:border-amber-800/50 p-3 rounded-xl">
            <h4 class="text-xs font-black text-amber-800 dark:text-amber-400 uppercase tracking-wider">Clinical Routine Urinalysis</h4>
        </div>

        <!-- Physical / Chemical Examination -->
        <div>
            <h5 class="text-[11px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">Physical / Chemical</h5>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                <div>
                    <label class="block text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase">Color</label>
                    <select name="results[color]" class="mt-1 w-full bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-600 rounded-lg px-2 py-1 text-xs text-slate-900 dark:text-white font-semibold">
                        <option value="Straw">Straw</option>
                        <option value="Light Yellow" selected>Light Yellow</option>
                        <option value="Yellow">Yellow</option>
                        <option value="Dark Yellow / Amber">Dark Yellow</option>
                        <option value="Red / Bloody">Red / Bloody</option>
                    </select>
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase">Transparency</label>
                    <select name="results[transparency]" class="mt-1 w-full bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-600 rounded-lg px-2 py-1 text-xs text-slate-900 dark:text-white font-semibold">
                        <option value="Clear" selected>Clear</option>
                        <option value="Slightly Hazy">Slightly Hazy</option>
                        <option value="Hazy">Hazy</option>
                        <option value="Turbid / Cloudy">Turbid</option>
                    </select>
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase">pH</label>
                    <input type="text" name="results[ph]" placeholder="e.g. 6.0 (4.5-8.0)" class="mt-1 w-full bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-600 rounded-lg px-2 py-1 text-xs text-slate-900 dark:text-white font-semibold">
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase">Specific Gravity</label>
                    <input type="text" name="results[sp_gravity]" placeholder="1.005 - 1.030" class="mt-1 w-full bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-600 rounded-lg px-2 py-1 text-xs text-slate-900 dark:text-white font-semibold">
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase">Protein / Albumin</label>
                    <select name="results[protein]" class="mt-1 w-full bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-600 rounded-lg px-2 py-1 text-xs text-slate-900 dark:text-white font-semibold">
                        <option value="Negative" selected>Negative</option>
                        <option value="Trace">Trace</option>
                        <option value="1+ (30 mg/dL)">1+ (30 mg/dL)</option>
                        <option value="2+ (100 mg/dL)">2+ (100 mg/dL)</option>
                        <option value="3+ (300 mg/dL)">3+ (300 mg/dL)</option>
                        <option value="4+ (1000 mg/dL)">4+ (1000 mg/dL)</option>
                    </select>
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase">Glucose / Sugar</label>
                    <select name="results[glucose]" class="mt-1 w-full bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-600 rounded-lg px-2 py-1 text-xs text-slate-900 dark:text-white font-semibold">
                        <option value="Negative" selected>Negative</option>
                        <option value="Trace">Trace</option>
                        <option value="1+ (100 mg/dL)">1+ (100 mg/dL)</option>
                        <option value="2+ (250 mg/dL)">2+ (250 mg/dL)</option>
                        <option value="3+ (500 mg/dL)">3+ (500 mg/dL)</option>
                        <option value="4+ (1000 mg/dL)">4+ (1000 mg/dL)</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Microscopic Examination -->
        <div>
            <h5 class="text-[11px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">Microscopic Examination (hpf / lpf)</h5>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                <div>
                    <label class="block text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase">Pus Cells (WBC)</label>
                    <input type="text" name="results[pus_cells_wbc]" placeholder="0-2 / hpf" class="mt-1 w-full bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-600 rounded-lg px-2 py-1 text-xs text-slate-900 dark:text-white font-semibold">
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase">Red Blood Cells (RBC)</label>
                    <input type="text" name="results[rbc_urine]" placeholder="0-2 / hpf" class="mt-1 w-full bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-600 rounded-lg px-2 py-1 text-xs text-slate-900 dark:text-white font-semibold">
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase">Epithelial Cells</label>
                    <select name="results[epithelial_cells]" class="mt-1 w-full bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-600 rounded-lg px-2 py-1 text-xs text-slate-900 dark:text-white font-semibold">
                        <option value="None">None</option>
                        <option value="Few" selected>Few</option>
                        <option value="Moderate">Moderate</option>
                        <option value="Many">Many</option>
                    </select>
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase">Mucus Threads</label>
                    <select name="results[mucus_threads]" class="mt-1 w-full bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-600 rounded-lg px-2 py-1 text-xs text-slate-900 dark:text-white font-semibold">
                        <option value="None">None</option>
                        <option value="Few" selected>Few</option>
                        <option value="Moderate">Moderate</option>
                        <option value="Many">Many</option>
                    </select>
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase">Bacteria</label>
                    <select name="results[bacteria]" class="mt-1 w-full bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-600 rounded-lg px-2 py-1 text-xs text-slate-900 dark:text-white font-semibold">
                        <option value="None" selected>None</option>
                        <option value="Few">Few</option>
                        <option value="Moderate">Moderate</option>
                        <option value="Many">Many</option>
                    </select>
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase">Amorphous Urates / Phos</label>
                    <select name="results[amorphous]" class="mt-1 w-full bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-600 rounded-lg px-2 py-1 text-xs text-slate-900 dark:text-white font-semibold">
                        <option value="None" selected>None</option>
                        <option value="Few">Few</option>
                        <option value="Moderate">Moderate</option>
                        <option value="Many">Many</option>
                    </select>
                </div>
                <div class="col-span-2">
                    <label class="block text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase">Casts / Crystals (if any)</label>
                    <input type="text" name="results[crystals_casts]" placeholder="e.g. Calcium Oxalate: Rare, Hyaline Casts: None" class="mt-1 w-full bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-600 rounded-lg px-2 py-1 text-xs text-slate-900 dark:text-white font-semibold">
                </div>
            </div>
        </div>

        <div>
            <label class="block text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1">Remarks</label>
            <input type="text" name="results[remarks]" placeholder="Additional findings / notes" class="w-full bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-600 rounded-xl px-3 py-2 text-xs text-slate-900 dark:text-white">
        </div>
    </div>

@elseif($isFecalysis)
    <!-- ═══════════════════ FECALYSIS FORM ═══════════════════ -->
    <div class="space-y-4">
        <div class="bg-emerald-50 dark:bg-emerald-950/30 border border-emerald-200 dark:border-emerald-800/50 p-3 rounded-xl">
            <h4 class="text-xs font-black text-emerald-800 dark:text-emerald-400 uppercase tracking-wider">Routine Fecalysis / Stool Examination</h4>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
            <div>
                <label class="block text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase">Color</label>
                <select name="results[color]" class="mt-1 w-full bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-600 rounded-lg px-2 py-1 text-xs text-slate-900 dark:text-white font-semibold">
                    <option value="Brown" selected>Brown</option>
                    <option value="Dark Brown">Dark Brown</option>
                    <option value="Yellow">Yellow</option>
                    <option value="Green">Green</option>
                    <option value="Clay / Pale">Clay / Pale</option>
                    <option value="Black / Tar-like">Black (Melena)</option>
                </select>
            </div>
            <div>
                <label class="block text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase">Consistency</label>
                <select name="results[consistency]" class="mt-1 w-full bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-600 rounded-lg px-2 py-1 text-xs text-slate-900 dark:text-white font-semibold">
                    <option value="Formed" selected>Formed</option>
                    <option value="Semi-formed">Semi-formed</option>
                    <option value="Soft">Soft</option>
                    <option value="Loose / Watery">Loose / Watery</option>
                    <option value="Mucoid">Mucoid</option>
                </select>
            </div>
            <div>
                <label class="block text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase">Pus Cells (WBC)</label>
                <input type="text" name="results[pus_cells]" placeholder="0-2 / hpf" class="mt-1 w-full bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-600 rounded-lg px-2 py-1 text-xs text-slate-900 dark:text-white font-semibold">
            </div>
            <div>
                <label class="block text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase">Red Blood Cells (RBC)</label>
                <input type="text" name="results[rbc_stool]" placeholder="0-1 / hpf" class="mt-1 w-full bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-600 rounded-lg px-2 py-1 text-xs text-slate-900 dark:text-white font-semibold">
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Ova / Parasites Found</label>
                <input type="text" name="results[parasites]" placeholder="e.g. No Ova or Parasite Seen (NOPS)" value="No Ova or Parasite Seen (NOPS)" class="w-full bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-600 rounded-lg px-3 py-2 text-xs text-slate-900 dark:text-white font-semibold">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Occult Blood (if requested)</label>
                <select name="results[occult_blood]" class="w-full bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-600 rounded-lg px-3 py-2 text-xs text-slate-900 dark:text-white font-semibold">
                    <option value="Not Requested" selected>Not Requested</option>
                    <option value="Negative">Negative</option>
                    <option value="Positive">Positive</option>
                </select>
            </div>
        </div>

        <div>
            <label class="block text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1">Remarks</label>
            <input type="text" name="results[remarks]" placeholder="Additional findings" class="w-full bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-600 rounded-xl px-3 py-2 text-xs text-slate-900 dark:text-white">
        </div>
    </div>

@elseif($isBloodChemistry)
    <!-- ═══════════════════ BLOOD CHEMISTRY FORM ═══════════════════ -->
    <div class="space-y-4">
        <div class="bg-cyan-50 dark:bg-cyan-950/30 border border-cyan-200 dark:border-cyan-800/50 p-3 rounded-xl">
            <h4 class="text-xs font-black text-cyan-800 dark:text-cyan-400 uppercase tracking-wider">Clinical Blood Chemistry Profile</h4>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
            <div class="bg-slate-50 dark:bg-slate-900/60 p-2.5 rounded-xl border border-slate-200 dark:border-slate-700">
                <label class="block text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase">Fasting Blood Sugar (FBS)</label>
                <input type="text" name="results[fbs]" placeholder="70 - 100 mg/dL" class="mt-1 w-full bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-600 rounded-lg px-2 py-1 text-xs text-slate-900 dark:text-white font-semibold">
            </div>
            <div class="bg-slate-50 dark:bg-slate-900/60 p-2.5 rounded-xl border border-slate-200 dark:border-slate-700">
                <label class="block text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase">Total Cholesterol</label>
                <input type="text" name="results[cholesterol]" placeholder="< 200 mg/dL" class="mt-1 w-full bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-600 rounded-lg px-2 py-1 text-xs text-slate-900 dark:text-white font-semibold">
            </div>
            <div class="bg-slate-50 dark:bg-slate-900/60 p-2.5 rounded-xl border border-slate-200 dark:border-slate-700">
                <label class="block text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase">Triglycerides</label>
                <input type="text" name="results[triglycerides]" placeholder="< 150 mg/dL" class="mt-1 w-full bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-600 rounded-lg px-2 py-1 text-xs text-slate-900 dark:text-white font-semibold">
            </div>
            <div class="bg-slate-50 dark:bg-slate-900/60 p-2.5 rounded-xl border border-slate-200 dark:border-slate-700">
                <label class="block text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase">Uric Acid</label>
                <input type="text" name="results[uric_acid]" placeholder="3.5 - 7.2 mg/dL" class="mt-1 w-full bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-600 rounded-lg px-2 py-1 text-xs text-slate-900 dark:text-white font-semibold">
            </div>
            <div class="bg-slate-50 dark:bg-slate-900/60 p-2.5 rounded-xl border border-slate-200 dark:border-slate-700">
                <label class="block text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase">Creatinine</label>
                <input type="text" name="results[creatinine]" placeholder="0.6 - 1.2 mg/dL" class="mt-1 w-full bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-600 rounded-lg px-2 py-1 text-xs text-slate-900 dark:text-white font-semibold">
            </div>
            <div class="bg-slate-50 dark:bg-slate-900/60 p-2.5 rounded-xl border border-slate-200 dark:border-slate-700">
                <label class="block text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase">SGPT / ALT</label>
                <input type="text" name="results[sgpt_alt]" placeholder="0 - 45 U/L" class="mt-1 w-full bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-600 rounded-lg px-2 py-1 text-xs text-slate-900 dark:text-white font-semibold">
            </div>
        </div>

        <div>
            <label class="block text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1">Remarks</label>
            <input type="text" name="results[remarks]" placeholder="Additional remarks" class="w-full bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-600 rounded-xl px-3 py-2 text-xs text-slate-900 dark:text-white">
        </div>
    </div>

@else
    <!-- ═══════════════════ GENERIC LABORATORY TEST FORM ═══════════════════ -->
    <div class="space-y-4">
        <div>
            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Test Result / Finding <span class="text-red-500">*</span></label>
            <textarea name="results[findings]" required rows="4" placeholder="Enter test results, values, or findings..." class="w-full bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-600 rounded-xl p-3 text-sm text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500"></textarea>
        </div>
        <div>
            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Reference Range / Normal Value</label>
            <input type="text" name="results[reference_range]" placeholder="e.g. Negative, Non-Reactive, or numerical interval" class="w-full bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-600 rounded-xl px-3 py-2 text-sm text-slate-900 dark:text-white">
        </div>
        <div>
            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Remarks / Recommendation</label>
            <input type="text" name="results[remarks]" placeholder="Additional remarks" class="w-full bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-600 rounded-xl px-3 py-2 text-sm text-slate-900 dark:text-white">
        </div>
    </div>
@endif
