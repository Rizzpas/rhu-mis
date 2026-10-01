@php
    $readonly = $readonly ?? false;
@endphp

<!-- ═══════════════════ RADIOLOGY / X-RAY FORM ═══════════════════ -->
<div class="space-y-4" @if(!$readonly) x-data="{
    fillNormalRad() {
        const form = $el.closest('form') || $el;
        const exam = form.querySelector('[name=\'results[exam_view]\']');
        const findings = form.querySelector('[name=\'results[findings]\']');
        const impression = form.querySelector('[name=\'results[impression]\']');
        const remarks = form.querySelector('[name=\'results[remarks]\']');

        if (exam && (!exam.value || exam.value.trim() === '')) exam.value = 'Chest PA (Standard)';
        if (findings) findings.value = 'Both lung fields are clear. No evidence of active pulmonary infiltrates, consolidation, or pleural effusion. Cardiac silhouette, heart size, and aortic knob are within normal limits. Hemidiaphragms and costophrenic sulci are intact and sharp. Visualized bony cage and thoracic soft tissues are unremarkable.';
        if (impression) impression.value = 'NORMAL CHEST RADIOGRAPH (Clear lung fields, essentially unremarkable chest findings).';
        if (remarks && (!remarks.value || remarks.value.trim() === '')) remarks.value = 'Routine health screening examination. No acute cardiopulmonary findings.';

        [exam, findings, impression, remarks].forEach(el => {
            if (el) {
                el.dispatchEvent(new Event('input', { bubbles: true }));
                el.dispatchEvent(new Event('change', { bubbles: true }));
            }
        });
    }
}" @endif>
    <div class="bg-emerald-50 dark:bg-emerald-950/30 border border-emerald-200 dark:border-emerald-800/50 p-3 rounded-xl flex items-center justify-between flex-wrap gap-2">
        <h4 class="text-xs font-black text-emerald-800 dark:text-emerald-400 uppercase tracking-wider flex items-center gap-1.5">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
            Radiology / Imaging Report
        </h4>
        @if(!$readonly)
            <button type="button" @click="fillNormalRad()" class="inline-flex items-center gap-1.5 bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold px-3 py-1.5 rounded-lg text-xs shadow-xs transition cursor-pointer">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                ⚡ Auto-Fill Normal Chest X-Ray
            </button>
        @else
            <span class="inline-flex items-center gap-1 text-[11px] font-bold text-emerald-700 dark:text-emerald-300 bg-emerald-100/70 dark:bg-emerald-900/60 px-2.5 py-0.5 rounded-full border border-emerald-200 dark:border-emerald-800">
                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                Verified Imaging Record
            </span>
        @endif
    </div>

    <div>
        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Examination / View Done @if(!$readonly)<span class="text-red-500">*</span>@endif</label>
        @if($readonly)
            <div class="w-full bg-slate-50 dark:bg-slate-900/60 border border-slate-200 dark:border-slate-700 rounded-xl px-3.5 py-2.5 text-sm text-slate-900 dark:text-white font-bold select-text">
                {{ $req->result_data['exam_view'] ?? $req->test_name }}
            </div>
        @else
            <input type="text" name="results[exam_view]" required value="{{ $req->result_data['exam_view'] ?? $req->test_name }}" placeholder="e.g. Chest PA, Chest AP/Lateral, Skull AP/Lat, Abdomen Plain" class="w-full bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-600 rounded-xl px-3 py-2 text-sm text-slate-900 dark:text-white font-semibold focus:ring-2 focus:ring-emerald-500">
        @endif
    </div>

    <div>
        <div class="flex justify-between items-center mb-1">
            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300">Radiological Findings @if(!$readonly)<span class="text-red-500">*</span>@endif</label>
        </div>
        @if($readonly)
            <div class="w-full bg-slate-50 dark:bg-slate-900/60 border border-slate-200 dark:border-slate-700 rounded-xl p-4 text-sm text-slate-800 dark:text-slate-200 leading-relaxed font-sans whitespace-pre-wrap select-text">
                {{ $req->result_data['findings'] ?? 'No findings recorded.' }}
            </div>
        @else
            <textarea name="results[findings]" required rows="4" placeholder="Detailed radiological findings..." class="w-full bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-600 rounded-xl p-3 text-sm text-slate-900 dark:text-white focus:ring-2 focus:ring-emerald-500 font-sans" id="rad-findings-{{ $req->id }}">{{ $req->result_data['findings'] ?? '' }}</textarea>
        @endif
    </div>

    <div>
        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Radiological Impression / Conclusion @if(!$readonly)<span class="text-red-500">*</span>@endif</label>
        @if($readonly)
            <div class="w-full bg-emerald-50/70 dark:bg-emerald-950/40 border-2 border-emerald-200 dark:border-emerald-800/60 rounded-xl p-4 text-sm text-emerald-950 dark:text-emerald-200 font-extrabold leading-relaxed select-text shadow-2xs">
                {{ $req->result_data['impression'] ?? 'No impression recorded.' }}
            </div>
        @else
            <textarea name="results[impression]" required rows="2" placeholder="e.g. Normal chest radiograph / Suggestive of PTB / Cardiomegaly..." class="w-full bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-600 rounded-xl p-3 text-sm text-slate-900 dark:text-white focus:ring-2 focus:ring-emerald-500 font-semibold" id="rad-impression-{{ $req->id }}">{{ $req->result_data['impression'] ?? '' }}</textarea>
        @endif
    </div>

    <div>
        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Radiologist / Sonologist Remarks</label>
        @if($readonly)
            <div class="w-full bg-slate-50 dark:bg-slate-900/60 border border-slate-200 dark:border-slate-700 rounded-xl px-3.5 py-2.5 text-sm text-slate-700 dark:text-slate-300 font-medium select-text">
                {{ !empty($req->result_data['remarks']) ? $req->result_data['remarks'] : 'None' }}
            </div>
        @else
            <input type="text" name="results[remarks]" value="{{ $req->result_data['remarks'] ?? '' }}" placeholder="e.g. Suggest clinical correlation and comparison with previous films" class="w-full bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-600 rounded-xl px-3 py-2 text-sm text-slate-900 dark:text-white">
        @endif
    </div>
</div>
