@php
    $readonly = $readonly ?? false;
    $testLower = strtolower($req->test_name ?? '');
    $isUrinalysis = str_contains($testLower, 'urine') || str_contains($testLower, 'urinalysis');
    $isFecalysis = str_contains($testLower, 'fecal') || str_contains($testLower, 'stool');
    $isBloodChemistry = str_contains($testLower, 'chem') || str_contains($testLower, 'glucose') || str_contains($testLower, 'lipid') || str_contains($testLower, 'fbs') || str_contains($testLower, 'cholesterol') || str_contains($testLower, 'uric') || str_contains($testLower, 'creatinine') || str_contains($testLower, 'sgpt');
    $isHematology = str_contains($testLower, 'cbc') || str_contains($testLower, 'blood') || str_contains($testLower, 'hem') || (!$isUrinalysis && !$isFecalysis && !$isBloodChemistry);
    $res = $req->result_data ?? [];
@endphp

@if($isHematology)
    <!-- ═══════════════════ COMPLETE BLOOD COUNT (CBC) FORM ═══════════════════ -->
    <div class="space-y-4" @if(!$readonly) x-data="{
        neutrophil: '{{ $res['neutrophil'] ?? '' }}',
        lymphocyte: '{{ $res['lymphocyte'] ?? '' }}',
        monocyte: '{{ $res['monocyte'] ?? '' }}',
        eosinophil: '{{ $res['eosinophil'] ?? '' }}',
        basophil: '{{ $res['basophil'] ?? '' }}',
        band_cells: '{{ $res['band_cells'] ?? '' }}',
        get diffTotal() {
            let sum = (parseFloat(this.neutrophil) || 0) +
                      (parseFloat(this.lymphocyte) || 0) +
                      (parseFloat(this.monocyte) || 0) +
                      (parseFloat(this.eosinophil) || 0) +
                      (parseFloat(this.basophil) || 0) +
                      (parseFloat(this.band_cells) || 0);
            return Math.round(sum * 10) / 10;
        },
        fillNormalCBC() {
            const normal = {
                'wbc': '6.50',
                'rbc': '4.70',
                'hemoglobin': '14.0',
                'hematocrit': '42.0',
                'platelets': '250',
                'mcv': '88.0',
                'mch': '29.5',
                'mchc': '33.5',
                'rdw': '13.0',
                'neutrophil': '60',
                'lymphocyte': '30',
                'monocyte': '6',
                'eosinophil': '3',
                'basophil': '1',
                'band_cells': '0',
                'bleeding_time': '2 mins',
                'clotting_time': '7 mins',
                'remarks': 'Normal complete blood count. Parameters within normal adult reference intervals.'
            };
            this.neutrophil = normal.neutrophil;
            this.lymphocyte = normal.lymphocyte;
            this.monocyte = normal.monocyte;
            this.eosinophil = normal.eosinophil;
            this.basophil = normal.basophil;
            this.band_cells = normal.band_cells;

            const form = $el.closest('form') || $el;
            for (const [key, val] of Object.entries(normal)) {
                const input = form.querySelector(`[name=\'results[${key}]\']`);
                if (input) {
                    input.value = val;
                    input.dispatchEvent(new Event('input', { bubbles: true }));
                    input.dispatchEvent(new Event('change', { bubbles: true }));
                }
            }
        }
    }" @endif>
        <div class="bg-rose-50 dark:bg-rose-950/30 border border-rose-200 dark:border-rose-800/50 p-3 rounded-xl flex items-center justify-between flex-wrap gap-2">
            <h4 class="text-xs font-black text-rose-800 dark:text-rose-400 uppercase tracking-wider flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path></svg>
                Complete Blood Count (CBC) with Differential
            </h4>
            <div class="flex items-center gap-2">
                @if(!$readonly)
                    <button type="button" @click="fillNormalCBC()" class="inline-flex items-center gap-1.5 bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold px-3 py-1.5 rounded-lg text-xs shadow-xs transition cursor-pointer">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        ⚡ Auto-Fill Normal CBC Values
                    </button>
                    <span class="text-[10px] text-rose-700 dark:text-rose-300 font-bold bg-rose-100 dark:bg-rose-900/60 px-2 py-0.5 rounded">Auto-Formatted</span>
                @else
                    <span class="inline-flex items-center gap-1 text-[11px] font-bold text-rose-700 dark:text-rose-300 bg-rose-100/70 dark:bg-rose-900/60 px-2.5 py-0.5 rounded-full border border-rose-200 dark:border-rose-800">
                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        Verified Hematology Report
                    </span>
                @endif
            </div>
        </div>

        <div class="overflow-x-auto border border-slate-200 dark:border-slate-700 rounded-xl">
            <table class="w-full text-xs text-left">
                <thead class="bg-slate-100 dark:bg-slate-700/60 text-slate-700 dark:text-slate-300 font-extrabold uppercase text-[10px] tracking-wider border-b border-slate-200 dark:border-slate-700">
                    <tr>
                        <th class="p-2.5 w-1/3">Test Parameter</th>
                        <th class="p-2.5 w-1/3">Result {{ !$readonly ? '(Auto-Formatted)' : '' }}</th>
                        <th class="p-2.5 w-1/3">Reference Interval</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-700/50">
                    @php
                        $cbcFields = [
                            ['label' => 'WBC (White Blood Cells)', 'key' => 'wbc', 'step' => '0.01', 'min' => 0, 'max' => 100, 'placeholder' => 'e.g. 6.50', 'decimals' => 2, 'range' => '4.00 - 10.00 x 10^9/L'],
                            ['label' => 'RBC (Red Blood Cells)', 'key' => 'rbc', 'step' => '0.01', 'min' => 0, 'max' => 20, 'placeholder' => 'e.g. 4.50', 'decimals' => 2, 'range' => '3.50 - 5.50 x 10^12/L'],
                            ['label' => 'Hemoglobin', 'key' => 'hemoglobin', 'step' => '0.1', 'min' => 0, 'max' => 300, 'placeholder' => 'e.g. 135.0', 'decimals' => 1, 'range' => 'M: 120-160 | F: 110-150 g/L'],
                            ['label' => 'Hematocrit', 'key' => 'hematocrit', 'step' => '0.1', 'min' => 0, 'max' => 100, 'placeholder' => 'e.g. 42.0', 'decimals' => 1, 'range' => 'M: 40.0 - 52.0% | F: 37.0 - 48.0%'],
                            ['label' => 'MCV', 'key' => 'mcv', 'step' => '0.1', 'min' => 0, 'max' => 200, 'placeholder' => 'e.g. 85.0', 'decimals' => 1, 'range' => '80 - 100 fl'],
                            ['label' => 'MCH', 'key' => 'mch', 'step' => '0.1', 'min' => 0, 'max' => 100, 'placeholder' => 'e.g. 28.5', 'decimals' => 1, 'range' => '27 - 31 pg'],
                            ['label' => 'MCHC', 'key' => 'mchc', 'step' => '0.1', 'min' => 0, 'max' => 500, 'placeholder' => 'e.g. 335.0', 'decimals' => 1, 'range' => '320 - 360 g/L'],
                            ['label' => 'RDW (Red Cell Dist. Width)', 'key' => 'rdw', 'step' => '0.1', 'min' => 0, 'max' => 50, 'placeholder' => 'e.g. 13.0', 'decimals' => 1, 'range' => '11.5 - 14.5 %'],
                            ['label' => 'Platelet Count', 'key' => 'platelet_count', 'step' => '1', 'min' => 0, 'max' => 2000, 'placeholder' => 'e.g. 250', 'decimals' => null, 'range' => '150 - 450 x 10^9/L'],
                        ];
                    @endphp

                    @foreach($cbcFields as $field)
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/40">
                            <td class="p-2.5 font-bold text-slate-800 dark:text-slate-200">{{ $field['label'] }}</td>
                            <td class="p-2">
                                @if($readonly)
                                    <div class="px-2.5 py-1 text-xs font-extrabold text-slate-900 dark:text-white font-mono select-text">
                                        {{ !empty($res[$field['key']]) ? $res[$field['key']] : '—' }}
                                    </div>
                                @else
                                    <input type="number" 
                                           step="{{ $field['step'] }}" 
                                           min="{{ $field['min'] }}" 
                                           max="{{ $field['max'] }}" 
                                           name="results[{{ $field['key'] }}]" 
                                           value="{{ $res[$field['key']] ?? '' }}" 
                                           placeholder="{{ $field['placeholder'] }}" 
                                           @if($field['decimals'] !== null)
                                               @blur="if($el.value && !isNaN($el.value)) $el.value = parseFloat($el.value).toFixed({{ $field['decimals'] }})"
                                           @endif
                                           class="w-full bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-600 rounded-lg px-2.5 py-1 text-xs text-slate-900 dark:text-white font-medium focus:ring-1 focus:ring-rose-500">
                                @endif
                            </td>
                            <td class="p-2.5 text-slate-500 dark:text-slate-400 font-mono text-[11px]">{{ $field['range'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- DIFFERENTIAL COUNT SECTION -->
        <div class="pt-2">
            <div class="flex items-center justify-between mb-2">
                <h5 class="text-[11px] font-black text-slate-700 dark:text-slate-300 uppercase tracking-wider">Differential Count (%)</h5>
                @if(!$readonly)
                    <div class="text-xs font-bold px-2.5 py-1 rounded-md transition"
                         :class="diffTotal === 100 ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/50 dark:text-emerald-300' : 'bg-amber-100 text-amber-800 dark:bg-amber-900/50 dark:text-amber-300'">
                        Total: <span x-text="diffTotal"></span>% 
                        <span x-show="diffTotal === 100" class="text-[10px] font-normal">(Balanced 100%)</span>
                        <span x-show="diffTotal !== 100 && diffTotal > 0" class="text-[10px] font-normal">(Must equal 100%)</span>
                    </div>
                @else
                    @php
                        $dSum = (float)($res['neutrophil'] ?? 0) + (float)($res['lymphocyte'] ?? 0) + (float)($res['monocyte'] ?? 0) + (float)($res['eosinophil'] ?? 0) + (float)($res['basophil'] ?? 0) + (float)($res['band_cells'] ?? 0);
                    @endphp
                    <div class="text-xs font-bold px-2.5 py-1 rounded-md bg-emerald-100 text-emerald-800 dark:bg-emerald-900/50 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800">
                        Total: {{ round($dSum, 1) }}% @if(round($dSum, 1) == 100)<span class="text-[10px] font-normal">(Balanced)</span>@endif
                    </div>
                @endif
            </div>

            @php
                $diffItems = [
                    ['label' => 'Neutrophil (40-75%)', 'key' => 'neutrophil', 'model' => 'neutrophil'],
                    ['label' => 'Lymphocyte (20-40%)', 'key' => 'lymphocyte', 'model' => 'lymphocyte'],
                    ['label' => 'Monocyte (2-8%)', 'key' => 'monocyte', 'model' => 'monocyte'],
                    ['label' => 'Eosinophil (1-4%)', 'key' => 'eosinophil', 'model' => 'eosinophil'],
                    ['label' => 'Basophil (0-1%)', 'key' => 'basophil', 'model' => 'basophil'],
                    ['label' => 'Band Cells / Stabs (0-3%)', 'key' => 'band_cells', 'model' => 'band_cells'],
                ];
            @endphp

            <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                @foreach($diffItems as $item)
                    <div class="bg-slate-50 dark:bg-slate-900/60 p-2.5 rounded-xl border border-slate-200 dark:border-slate-700">
                        <label class="block text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase">{{ $item['label'] }}</label>
                        @if($readonly)
                            <div class="mt-1 text-sm font-extrabold text-slate-900 dark:text-white font-mono select-text">
                                {{ !empty($res[$item['key']]) ? $res[$item['key']] . '%' : '—' }}
                            </div>
                        @else
                            <input type="number" step="0.1" min="0" max="100" x-model="{{ $item['model'] }}" name="results[{{ $item['key'] }}]" placeholder="%" 
                                @blur="if($el.value && !isNaN($el.value)) $el.value = parseFloat($el.value).toFixed(1); {{ $item['model'] }} = $el.value"
                                class="mt-1 w-full bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-600 rounded-lg px-2 py-1 text-xs text-slate-900 dark:text-white font-semibold">
                        @endif
                    </div>
                @endforeach
            </div>
        </div>

        <!-- COAGULATION / ADDITIONAL -->
        <div class="pt-2">
            <h5 class="text-[11px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">Coagulation & Erythrocyte Indices</h5>
            <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                <div class="bg-slate-50 dark:bg-slate-900/60 p-2.5 rounded-xl border border-slate-200 dark:border-slate-700">
                    <label class="block text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase">ESR (0-20 mm/hr)</label>
                    @if($readonly)
                        <div class="mt-1 text-xs font-bold text-slate-900 dark:text-white font-mono select-text">
                            {{ !empty($res['esr']) ? $res['esr'] . ' mm/hr' : 'Not Requested' }}
                        </div>
                    @else
                        <input type="number" step="1" min="0" max="150" name="results[esr]" value="{{ $res['esr'] ?? '' }}" placeholder="mm/hr" class="mt-1 w-full bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-600 rounded-lg px-2 py-1 text-xs text-slate-900 dark:text-white font-semibold">
                    @endif
                </div>
                <div class="bg-slate-50 dark:bg-slate-900/60 p-2.5 rounded-xl border border-slate-200 dark:border-slate-700">
                    <label class="block text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase">Bleeding Time (1-5 mins)</label>
                    @if($readonly)
                        <div class="mt-1 text-xs font-bold text-slate-900 dark:text-white font-mono select-text">
                            {{ !empty($res['bleeding_time']) ? $res['bleeding_time'] : 'Not Requested' }}
                        </div>
                    @else
                        <input type="text" name="results[bleeding_time]" value="{{ $res['bleeding_time'] ?? '' }}" placeholder="e.g. 2 mins" class="mt-1 w-full bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-600 rounded-lg px-2 py-1 text-xs text-slate-900 dark:text-white font-semibold">
                    @endif
                </div>
                <div class="bg-slate-50 dark:bg-slate-900/60 p-2.5 rounded-xl border border-slate-200 dark:border-slate-700">
                    <label class="block text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase">Clotting Time (5-15 mins)</label>
                    @if($readonly)
                        <div class="mt-1 text-xs font-bold text-slate-900 dark:text-white font-mono select-text">
                            {{ !empty($res['clotting_time']) ? $res['clotting_time'] : 'Not Requested' }}
                        </div>
                    @else
                        <input type="text" name="results[clotting_time]" value="{{ $res['clotting_time'] ?? '' }}" placeholder="e.g. 8 mins" class="mt-1 w-full bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-600 rounded-lg px-2 py-1 text-xs text-slate-900 dark:text-white font-semibold">
                    @endif
                </div>
            </div>
        </div>

        <div>
            <label class="block text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1">Pathologist / Medical Technologist Remarks</label>
            @if($readonly)
                <div class="w-full bg-slate-50 dark:bg-slate-900/60 border border-slate-200 dark:border-slate-700 rounded-xl p-3 text-xs text-slate-800 dark:text-slate-200 font-medium select-text">
                    {{ !empty($res['remarks']) ? $res['remarks'] : 'No special remarks recorded.' }}
                </div>
            @else
                <textarea name="results[remarks]" rows="2" placeholder="e.g. Normocytic, normochromic. Please correlate clinically." class="w-full bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-600 rounded-xl p-2.5 text-xs text-slate-900 dark:text-white">{{ $res['remarks'] ?? '' }}</textarea>
            @endif
        </div>
    </div>

@elseif($isUrinalysis)
    <!-- ═══════════════════ URINALYSIS FORM ═══════════════════ -->
    <div class="space-y-4" @if(!$readonly) x-data="{
        pusCells: '{{ $res['pus_cells_wbc'] ?? '' }}',
        rbcCells: '{{ $res['rbc_urine'] ?? '' }}',
        fillNormalUrinalysis() {
            const normal = {
                'color': 'Light Yellow',
                'transparency': 'Clear',
                'ph': '6.0',
                'sp_gravity': '1.015',
                'protein': 'Negative',
                'glucose': 'Negative',
                'pus_cells_wbc': '0-2',
                'rbc_urine': '0-1',
                'epithelial_cells': 'Few',
                'mucus_threads': 'None',
                'bacteria': 'None',
                'amorphous_urates': 'None',
                'casts': 'None',
                'crystals': 'None',
                'pregnancy_test': 'Negative',
                'remarks': 'Routine urinalysis within normal physiologic limits.'
            };
            this.pusCells = normal.pus_cells_wbc;
            this.rbcCells = normal.rbc_urine;

            const form = $el.closest('form') || $el;
            for (const [key, val] of Object.entries(normal)) {
                const input = form.querySelector(`[name=\'results[${key}]\']`);
                if (input) {
                    input.value = val;
                    input.dispatchEvent(new Event('input', { bubbles: true }));
                    input.dispatchEvent(new Event('change', { bubbles: true }));
                }
            }
        }
    }" @endif>
        <div class="bg-amber-50 dark:bg-amber-950/30 border border-amber-200 dark:border-amber-800/50 p-3 rounded-xl flex items-center justify-between flex-wrap gap-2">
            <h4 class="text-xs font-black text-amber-800 dark:text-amber-400 uppercase tracking-wider flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path></svg>
                Clinical Routine Urinalysis
            </h4>
            <div class="flex items-center gap-2">
                @if(!$readonly)
                    <button type="button" @click="fillNormalUrinalysis()" class="inline-flex items-center gap-1.5 bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold px-3 py-1.5 rounded-lg text-xs shadow-xs transition cursor-pointer">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        ⚡ Auto-Fill Normal Urinalysis
                    </button>
                    <span class="text-[10px] text-amber-700 dark:text-amber-300 font-bold bg-amber-100 dark:bg-amber-900/60 px-2 py-0.5 rounded">Auto-Formatted</span>
                @else
                    <span class="inline-flex items-center gap-1 text-[11px] font-bold text-amber-700 dark:text-amber-300 bg-amber-100/70 dark:bg-amber-900/60 px-2.5 py-0.5 rounded-full border border-amber-200 dark:border-amber-800">
                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        Verified Urinalysis Record
                    </span>
                @endif
            </div>
        </div>

        <!-- Physical / Chemical Examination -->
        <div>
            <h5 class="text-[11px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">Physical & Chemical Examination</h5>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                <div>
                    <label class="block text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase">Color</label>
                    @if($readonly)
                        <div class="mt-1 bg-slate-50 dark:bg-slate-900/60 border border-slate-200 dark:border-slate-700 rounded-lg px-2.5 py-1.5 text-xs text-slate-900 dark:text-white font-bold select-text">
                            {{ $res['color'] ?? 'Light Yellow' }}
                        </div>
                    @else
                        <select name="results[color]" class="mt-1 w-full bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-600 rounded-lg px-2 py-1 text-xs text-slate-900 dark:text-white font-semibold">
                            @foreach(['Straw', 'Light Yellow', 'Yellow', 'Dark Yellow / Amber', 'Red / Bloody'] as $c)
                                <option value="{{ $c }}" {{ ($res['color'] ?? 'Light Yellow') === $c ? 'selected' : '' }}>{{ $c }}</option>
                            @endforeach
                        </select>
                    @endif
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase">Transparency</label>
                    @if($readonly)
                        <div class="mt-1 bg-slate-50 dark:bg-slate-900/60 border border-slate-200 dark:border-slate-700 rounded-lg px-2.5 py-1.5 text-xs text-slate-900 dark:text-white font-bold select-text">
                            {{ $res['transparency'] ?? 'Clear' }}
                        </div>
                    @else
                        <select name="results[transparency]" class="mt-1 w-full bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-600 rounded-lg px-2 py-1 text-xs text-slate-900 dark:text-white font-semibold">
                            @foreach(['Clear', 'Slightly Hazy', 'Hazy', 'Turbid / Cloudy'] as $t)
                                <option value="{{ $t }}" {{ ($res['transparency'] ?? 'Clear') === $t ? 'selected' : '' }}>{{ $t }}</option>
                            @endforeach
                        </select>
                    @endif
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase">pH (4.5 - 8.5)</label>
                    @if($readonly)
                        <div class="mt-1 bg-slate-50 dark:bg-slate-900/60 border border-slate-200 dark:border-slate-700 rounded-lg px-2.5 py-1.5 text-xs text-slate-900 dark:text-white font-bold font-mono select-text">
                            {{ $res['ph'] ?? '6.0' }}
                        </div>
                    @else
                        <input type="number" step="0.1" min="4.0" max="9.0" name="results[ph]" value="{{ $res['ph'] ?? '' }}" placeholder="e.g. 6.0" 
                            @blur="if($el.value && !isNaN($el.value)) $el.value = parseFloat($el.value).toFixed(1)"
                            class="mt-1 w-full bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-600 rounded-lg px-2 py-1 text-xs text-slate-900 dark:text-white font-semibold focus:ring-1 focus:ring-amber-500">
                    @endif
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase">Specific Gravity</label>
                    @if($readonly)
                        <div class="mt-1 bg-slate-50 dark:bg-slate-900/60 border border-slate-200 dark:border-slate-700 rounded-lg px-2.5 py-1.5 text-xs text-slate-900 dark:text-white font-bold font-mono select-text">
                            {{ $res['sp_gravity'] ?? '1.015' }}
                        </div>
                    @else
                        <input type="number" step="0.001" min="1.000" max="1.050" name="results[sp_gravity]" value="{{ $res['sp_gravity'] ?? '' }}" placeholder="e.g. 1.015"
                            @blur="if($el.value && !isNaN($el.value)) $el.value = parseFloat($el.value).toFixed(3)"
                            class="mt-1 w-full bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-600 rounded-lg px-2 py-1 text-xs text-slate-900 dark:text-white font-semibold focus:ring-1 focus:ring-amber-500">
                    @endif
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase">Protein / Albumin</label>
                    @if($readonly)
                        <div class="mt-1 bg-slate-50 dark:bg-slate-900/60 border border-slate-200 dark:border-slate-700 rounded-lg px-2.5 py-1.5 text-xs text-slate-900 dark:text-white font-bold select-text">
                            {{ $res['protein'] ?? 'Negative' }}
                        </div>
                    @else
                        <select name="results[protein]" class="mt-1 w-full bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-600 rounded-lg px-2 py-1 text-xs text-slate-900 dark:text-white font-semibold">
                            @foreach(['Negative', 'Trace', '1+ (30 mg/dL)', '2+ (100 mg/dL)', '3+ (300 mg/dL)', '4+ (1000 mg/dL)'] as $p)
                                <option value="{{ $p }}" {{ ($res['protein'] ?? 'Negative') === $p ? 'selected' : '' }}>{{ $p }}</option>
                            @endforeach
                        </select>
                    @endif
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase">Glucose / Sugar</label>
                    @if($readonly)
                        <div class="mt-1 bg-slate-50 dark:bg-slate-900/60 border border-slate-200 dark:border-slate-700 rounded-lg px-2.5 py-1.5 text-xs text-slate-900 dark:text-white font-bold select-text">
                            {{ $res['glucose'] ?? 'Negative' }}
                        </div>
                    @else
                        <select name="results[glucose]" class="mt-1 w-full bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-600 rounded-lg px-2 py-1 text-xs text-slate-900 dark:text-white font-semibold">
                            @foreach(['Negative', 'Trace', '1+ (100 mg/dL)', '2+ (250 mg/dL)', '3+ (500 mg/dL)', '4+ (1000 mg/dL)'] as $g)
                                <option value="{{ $g }}" {{ ($res['glucose'] ?? 'Negative') === $g ? 'selected' : '' }}>{{ $g }}</option>
                            @endforeach
                        </select>
                    @endif
                </div>
            </div>
        </div>

        <!-- Microscopic Examination -->
        <div>
            <h5 class="text-[11px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">Microscopic Examination (hpf / lpf)</h5>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                <div class="sm:col-span-2 bg-slate-50 dark:bg-slate-900/40 p-2.5 rounded-xl border border-slate-200 dark:border-slate-700">
                    <label class="block text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase">Pus Cells (WBC) / hpf</label>
                    @if($readonly)
                        <div class="mt-1 text-xs font-bold text-slate-900 dark:text-white font-mono select-text">
                            {{ !empty($res['pus_cells_wbc']) ? $res['pus_cells_wbc'] : '0-2' }}
                        </div>
                    @else
                        <input type="text" x-model="pusCells" name="results[pus_cells_wbc]" 
                            @input="$el.value = $el.value.replace(/[^0-9\-\s]/g, ''); pusCells = $el.value"
                            placeholder="e.g. 0-2 (digits only)" 
                            class="mt-1 w-full bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-600 rounded-lg px-2 py-1 text-xs text-slate-900 dark:text-white font-semibold">
                    @endif
                </div>

                <div class="sm:col-span-2 bg-slate-50 dark:bg-slate-900/40 p-2.5 rounded-xl border border-slate-200 dark:border-slate-700">
                    <label class="block text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase">Red Blood Cells (RBC) / hpf</label>
                    @if($readonly)
                        <div class="mt-1 text-xs font-bold text-slate-900 dark:text-white font-mono select-text">
                            {{ !empty($res['rbc_urine']) ? $res['rbc_urine'] : '0-1' }}
                        </div>
                    @else
                        <input type="text" x-model="rbcCells" name="results[rbc_urine]" 
                            @input="$el.value = $el.value.replace(/[^0-9\-\s]/g, ''); rbcCells = $el.value"
                            placeholder="e.g. 0-2 (digits only)" 
                            class="mt-1 w-full bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-600 rounded-lg px-2 py-1 text-xs text-slate-900 dark:text-white font-semibold">
                    @endif
                </div>

                @php
                    $microSelects = [
                        ['label' => 'Epithelial Cells', 'key' => 'epithelial_cells', 'options' => ['None', 'Few', 'Moderate', 'Many'], 'default' => 'Few'],
                        ['label' => 'Mucus Threads', 'key' => 'mucus_threads', 'options' => ['None', 'Few', 'Moderate', 'Many'], 'default' => 'Few'],
                        ['label' => 'Bacteria', 'key' => 'bacteria', 'options' => ['None', 'Few', 'Moderate', 'Many'], 'default' => 'None'],
                        ['label' => 'Amorphous Urates', 'key' => 'amorphous', 'options' => ['None', 'Few', 'Moderate', 'Many'], 'default' => 'None'],
                    ];
                @endphp

                @foreach($microSelects as $ms)
                    <div>
                        <label class="block text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase">{{ $ms['label'] }}</label>
                        @if($readonly)
                            <div class="mt-1 bg-slate-50 dark:bg-slate-900/60 border border-slate-200 dark:border-slate-700 rounded-lg px-2.5 py-1.5 text-xs text-slate-900 dark:text-white font-bold select-text">
                                {{ $res[$ms['key']] ?? $ms['default'] }}
                            </div>
                        @else
                            <select name="results[{{ $ms['key'] }}]" class="mt-1 w-full bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-600 rounded-lg px-2 py-1 text-xs text-slate-900 dark:text-white font-semibold">
                                @foreach($ms['options'] as $v)
                                    <option value="{{ $v }}" {{ ($res[$ms['key']] ?? $ms['default']) === $v ? 'selected' : '' }}>{{ $v }}</option>
                                @endforeach
                            </select>
                        @endif
                    </div>
                @endforeach

                <div class="col-span-2">
                    <label class="block text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase">Casts / Crystals (if any)</label>
                    @if($readonly)
                        <div class="mt-1 bg-slate-50 dark:bg-slate-900/60 border border-slate-200 dark:border-slate-700 rounded-lg px-2.5 py-1.5 text-xs text-slate-900 dark:text-white font-medium select-text">
                            {{ !empty($res['crystals_casts']) ? $res['crystals_casts'] : 'None Seen' }}
                        </div>
                    @else
                        <input type="text" name="results[crystals_casts]" value="{{ $res['crystals_casts'] ?? '' }}" placeholder="e.g. Calcium Oxalate: Rare, Hyaline Casts: None" class="mt-1 w-full bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-600 rounded-lg px-2 py-1 text-xs text-slate-900 dark:text-white font-semibold">
                    @endif
                </div>
            </div>
        </div>

        <div>
            <label class="block text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1">Remarks</label>
            @if($readonly)
                <div class="w-full bg-slate-50 dark:bg-slate-900/60 border border-slate-200 dark:border-slate-700 rounded-xl p-3 text-xs text-slate-800 dark:text-slate-200 font-medium select-text">
                    {{ !empty($res['remarks']) ? $res['remarks'] : 'No special remarks recorded.' }}
                </div>
            @else
                <input type="text" name="results[remarks]" value="{{ $res['remarks'] ?? '' }}" placeholder="Additional findings / notes" class="w-full bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-600 rounded-xl px-3 py-2 text-xs text-slate-900 dark:text-white">
            @endif
        </div>
    </div>

@elseif($isFecalysis)
    <!-- ═══════════════════ FECALYSIS FORM ═══════════════════ -->
    <div class="space-y-4">
        <div class="bg-emerald-50 dark:bg-emerald-950/30 border border-emerald-200 dark:border-emerald-800/50 p-3 rounded-xl flex items-center justify-between">
            <h4 class="text-xs font-black text-emerald-800 dark:text-emerald-400 uppercase tracking-wider">Routine Fecalysis / Stool Examination</h4>
            @if($readonly)
                <span class="inline-flex items-center gap-1 text-[11px] font-bold text-emerald-700 dark:text-emerald-300 bg-emerald-100/70 dark:bg-emerald-900/60 px-2.5 py-0.5 rounded-full border border-emerald-200 dark:border-emerald-800">
                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    Verified Fecalysis Record
                </span>
            @endif
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
            <div>
                <label class="block text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase">Color</label>
                @if($readonly)
                    <div class="mt-1 bg-slate-50 dark:bg-slate-900/60 border border-slate-200 dark:border-slate-700 rounded-lg px-2.5 py-1.5 text-xs text-slate-900 dark:text-white font-bold select-text">
                        {{ $res['color'] ?? 'Brown' }}
                    </div>
                @else
                    <select name="results[color]" class="mt-1 w-full bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-600 rounded-lg px-2 py-1 text-xs text-slate-900 dark:text-white font-semibold">
                        @foreach(['Brown', 'Dark Brown', 'Yellow', 'Green', 'Clay / Pale', 'Black / Tar-like'] as $c)
                            <option value="{{ $c }}" {{ ($res['color'] ?? 'Brown') === $c ? 'selected' : '' }}>{{ $c }}</option>
                        @endforeach
                    </select>
                @endif
            </div>
            <div>
                <label class="block text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase">Consistency</label>
                @if($readonly)
                    <div class="mt-1 bg-slate-50 dark:bg-slate-900/60 border border-slate-200 dark:border-slate-700 rounded-lg px-2.5 py-1.5 text-xs text-slate-900 dark:text-white font-bold select-text">
                        {{ $res['consistency'] ?? 'Formed' }}
                    </div>
                @else
                    <select name="results[consistency]" class="mt-1 w-full bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-600 rounded-lg px-2 py-1 text-xs text-slate-900 dark:text-white font-semibold">
                        @foreach(['Formed', 'Semi-formed', 'Soft', 'Loose / Watery', 'Mucoid'] as $cs)
                            <option value="{{ $cs }}" {{ ($res['consistency'] ?? 'Formed') === $cs ? 'selected' : '' }}>{{ $cs }}</option>
                        @endforeach
                    </select>
                @endif
            </div>
            <div>
                <label class="block text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase">Pus Cells (WBC)</label>
                @if($readonly)
                    <div class="mt-1 text-xs font-bold text-slate-900 dark:text-white font-mono select-text">
                        {{ !empty($res['pus_cells']) ? $res['pus_cells'] : '0-2 / hpf' }}
                    </div>
                @else
                    <input type="text" name="results[pus_cells]" value="{{ $res['pus_cells'] ?? '' }}" placeholder="0-2 / hpf" class="mt-1 w-full bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-600 rounded-lg px-2 py-1 text-xs text-slate-900 dark:text-white font-semibold">
                @endif
            </div>
            <div>
                <label class="block text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase">Red Blood Cells (RBC)</label>
                @if($readonly)
                    <div class="mt-1 text-xs font-bold text-slate-900 dark:text-white font-mono select-text">
                        {{ !empty($res['rbc_stool']) ? $res['rbc_stool'] : '0-1 / hpf' }}
                    </div>
                @else
                    <input type="text" name="results[rbc_stool]" value="{{ $res['rbc_stool'] ?? '' }}" placeholder="0-1 / hpf" class="mt-1 w-full bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-600 rounded-lg px-2 py-1 text-xs text-slate-900 dark:text-white font-semibold">
                @endif
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Ova / Parasites Found</label>
                @if($readonly)
                    <div class="w-full bg-slate-50 dark:bg-slate-900/60 border border-slate-200 dark:border-slate-700 rounded-lg px-3 py-2 text-xs text-slate-900 dark:text-white font-bold select-text">
                        {{ $res['parasites'] ?? 'No Ova or Parasite Seen (NOPS)' }}
                    </div>
                @else
                    <input type="text" name="results[parasites]" value="{{ $res['parasites'] ?? 'No Ova or Parasite Seen (NOPS)' }}" placeholder="e.g. No Ova or Parasite Seen (NOPS)" class="w-full bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-600 rounded-lg px-3 py-2 text-xs text-slate-900 dark:text-white font-semibold">
                @endif
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Occult Blood</label>
                @if($readonly)
                    <div class="w-full bg-slate-50 dark:bg-slate-900/60 border border-slate-200 dark:border-slate-700 rounded-lg px-3 py-2 text-xs text-slate-900 dark:text-white font-bold select-text">
                        {{ $res['occult_blood'] ?? 'Not Requested' }}
                    </div>
                @else
                    <select name="results[occult_blood]" class="w-full bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-600 rounded-lg px-3 py-2 text-xs text-slate-900 dark:text-white font-semibold">
                        @foreach(['Not Requested', 'Negative', 'Positive'] as $ob)
                            <option value="{{ $ob }}" {{ ($res['occult_blood'] ?? 'Not Requested') === $ob ? 'selected' : '' }}>{{ $ob }}</option>
                        @endforeach
                    </select>
                @endif
            </div>
        </div>

        <div>
            <label class="block text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1">Remarks</label>
            @if($readonly)
                <div class="w-full bg-slate-50 dark:bg-slate-900/60 border border-slate-200 dark:border-slate-700 rounded-xl p-3 text-xs text-slate-800 dark:text-slate-200 font-medium select-text">
                    {{ !empty($res['remarks']) ? $res['remarks'] : 'No special remarks recorded.' }}
                </div>
            @else
                <input type="text" name="results[remarks]" value="{{ $res['remarks'] ?? '' }}" placeholder="Additional findings" class="w-full bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-600 rounded-xl px-3 py-2 text-xs text-slate-900 dark:text-white">
            @endif
        </div>
    </div>

@elseif($isBloodChemistry)
    <!-- ═══════════════════ BLOOD CHEMISTRY FORM ═══════════════════ -->
    <div class="space-y-4">
        <div class="bg-cyan-50 dark:bg-cyan-950/30 border border-cyan-200 dark:border-cyan-800/50 p-3 rounded-xl flex items-center justify-between">
            <h4 class="text-xs font-black text-cyan-800 dark:text-cyan-400 uppercase tracking-wider">Clinical Blood Chemistry Profile</h4>
            @if($readonly)
                <span class="inline-flex items-center gap-1 text-[11px] font-bold text-cyan-700 dark:text-cyan-300 bg-cyan-100/70 dark:bg-cyan-900/60 px-2.5 py-0.5 rounded-full border border-cyan-200 dark:border-cyan-800">
                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    Verified Chemistry Profile
                </span>
            @endif
        </div>

        @php
            $chemFields = [
                ['label' => 'Fasting Blood Sugar (FBS)', 'key' => 'fbs', 'placeholder' => '70 - 100 mg/dL'],
                ['label' => 'Total Cholesterol', 'key' => 'cholesterol', 'placeholder' => '< 200 mg/dL'],
                ['label' => 'Triglycerides', 'key' => 'triglycerides', 'placeholder' => '< 150 mg/dL'],
                ['label' => 'Uric Acid', 'key' => 'uric_acid', 'placeholder' => '3.5 - 7.2 mg/dL'],
                ['label' => 'Creatinine', 'key' => 'creatinine', 'placeholder' => '0.6 - 1.2 mg/dL'],
                ['label' => 'SGPT / ALT', 'key' => 'sgpt_alt', 'placeholder' => '0 - 45 U/L'],
            ];
        @endphp

        <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
            @foreach($chemFields as $cf)
                <div class="bg-slate-50 dark:bg-slate-900/60 p-2.5 rounded-xl border border-slate-200 dark:border-slate-700">
                    <label class="block text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase">{{ $cf['label'] }}</label>
                    @if($readonly)
                        <div class="mt-1 text-sm font-extrabold text-slate-900 dark:text-white font-mono select-text">
                            {{ !empty($res[$cf['key']]) ? $res[$cf['key']] : '—' }}
                        </div>
                    @else
                        <input type="text" name="results[{{ $cf['key'] }}]" value="{{ $res[$cf['key']] ?? '' }}" placeholder="{{ $cf['placeholder'] }}" class="mt-1 w-full bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-600 rounded-lg px-2 py-1 text-xs text-slate-900 dark:text-white font-semibold">
                    @endif
                </div>
            @endforeach
        </div>

        <div>
            <label class="block text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1">Remarks</label>
            @if($readonly)
                <div class="w-full bg-slate-50 dark:bg-slate-900/60 border border-slate-200 dark:border-slate-700 rounded-xl p-3 text-xs text-slate-800 dark:text-slate-200 font-medium select-text">
                    {{ !empty($res['remarks']) ? $res['remarks'] : 'No special remarks recorded.' }}
                </div>
            @else
                <input type="text" name="results[remarks]" value="{{ $res['remarks'] ?? '' }}" placeholder="Additional remarks" class="w-full bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-600 rounded-xl px-3 py-2 text-xs text-slate-900 dark:text-white">
            @endif
        </div>
    </div>

@else
    <!-- ═══════════════════ GENERIC LABORATORY TEST FORM ═══════════════════ -->
    <div class="space-y-4">
        <div>
            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Test Result / Finding @if(!$readonly)<span class="text-red-500">*</span>@endif</label>
            @if($readonly)
                <div class="w-full bg-slate-50 dark:bg-slate-900/60 border border-slate-200 dark:border-slate-700 rounded-xl p-4 text-sm text-slate-900 dark:text-white font-mono leading-relaxed whitespace-pre-wrap select-text">
                    {{ $res['findings'] ?? 'No findings recorded.' }}
                </div>
            @else
                <textarea name="results[findings]" required rows="4" placeholder="Enter test results, values, or findings..." class="w-full bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-600 rounded-xl p-3 text-sm text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500">{{ $res['findings'] ?? '' }}</textarea>
            @endif
        </div>
        <div>
            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Reference Range / Normal Value</label>
            @if($readonly)
                <div class="w-full bg-slate-50 dark:bg-slate-900/60 border border-slate-200 dark:border-slate-700 rounded-xl px-3 py-2 text-sm text-slate-800 dark:text-slate-200 select-text">
                    {{ !empty($res['reference_range']) ? $res['reference_range'] : 'Not Specified' }}
                </div>
            @else
                <input type="text" name="results[reference_range]" value="{{ $res['reference_range'] ?? '' }}" placeholder="e.g. Negative, Non-Reactive, or numerical interval" class="w-full bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-600 rounded-xl px-3 py-2 text-sm text-slate-900 dark:text-white">
            @endif
        </div>
        <div>
            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Remarks / Recommendation</label>
            @if($readonly)
                <div class="w-full bg-slate-50 dark:bg-slate-900/60 border border-slate-200 dark:border-slate-700 rounded-xl px-3 py-2 text-sm text-slate-800 dark:text-slate-200 select-text">
                    {{ !empty($res['remarks']) ? $res['remarks'] : 'None' }}
                </div>
            @else
                <input type="text" name="results[remarks]" value="{{ $res['remarks'] ?? '' }}" placeholder="Additional remarks" class="w-full bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-600 rounded-xl px-3 py-2 text-sm text-slate-900 dark:text-white">
            @endif
        </div>
    </div>
@endif
