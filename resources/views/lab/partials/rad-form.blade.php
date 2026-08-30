<!-- ═══════════════════ RADIOLOGY / X-RAY FORM ═══════════════════ -->
<div class="space-y-4">
    <div class="bg-indigo-50 dark:bg-indigo-950/30 border border-indigo-200 dark:border-indigo-800/50 p-3 rounded-xl">
        <h4 class="text-xs font-black text-indigo-800 dark:text-indigo-400 uppercase tracking-wider flex items-center gap-1.5">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
            Radiology / Imaging Report
        </h4>
    </div>

    <div>
        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Examination / View Done <span class="text-red-500">*</span></label>
        <input type="text" name="results[exam_view]" required value="{{ $req->test_name }}" placeholder="e.g. Chest PA, Chest AP/Lateral, Skull AP/Lat, Abdomen Plain" class="w-full bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-600 rounded-xl px-3 py-2 text-sm text-slate-900 dark:text-white font-semibold">
    </div>

    <div>
        <div class="flex justify-between items-center mb-1">
            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300">Radiological Findings <span class="text-red-500">*</span></label>
            <div class="flex gap-1" x-data>
                <button type="button" @click="$dispatch('fill-findings', 'Bilateral lung fields are clear. No evidence of active pulmonary disease. Cardiac silhouette and aortic knob are within normal limits. Diaphragms and costophrenic sulci are intact. Visualized osseous structures are unremarkable.')" class="text-[10px] bg-indigo-50 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-300 px-2 py-0.5 rounded border border-indigo-200 dark:border-indigo-800 hover:bg-indigo-100 transition">
                    + Normal Chest Template
                </button>
            </div>
        </div>
        <textarea name="results[findings]" required rows="4" placeholder="Detailed radiological findings..." class="w-full bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-600 rounded-xl p-3 text-sm text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 font-sans" id="rad-findings-{{ $req->id }}"></textarea>
    </div>

    <div>
        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Radiological Impression / Conclusion <span class="text-red-500">*</span></label>
        <textarea name="results[impression]" required rows="2" placeholder="e.g. Normal chest radiograph / Suggestive of PTB / Cardiomegaly..." class="w-full bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-600 rounded-xl p-3 text-sm text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 font-semibold" id="rad-impression-{{ $req->id }}"></textarea>
    </div>

    <div>
        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Radiologist / Sonologist Remarks</label>
        <input type="text" name="results[remarks]" placeholder="e.g. Suggest clinical correlation and comparison with previous films" class="w-full bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-600 rounded-xl px-3 py-2 text-sm text-slate-900 dark:text-white">
    </div>
</div>
