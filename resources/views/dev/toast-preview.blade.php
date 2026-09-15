@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-16">
    <div class="bg-white/80 dark:bg-slate-900/80 backdrop-blur-md rounded-3xl border border-slate-200/80 dark:border-slate-800 p-8 shadow-xl">
        <div class="flex items-center justify-between border-b border-slate-200/60 dark:border-slate-800 pb-6 mb-8">
            <div>
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 border border-emerald-200/60 dark:border-emerald-800/60">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                    UI Design System
                </span>
                <h1 class="text-2xl sm:text-3xl font-black text-slate-900 dark:text-white mt-2 tracking-tight">
                    Modern Clean Toast Notification System
                </h1>
                <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
                    Showcase of glassmorphic surfaces, dynamic themed progress timers, ambient glows, and hover-to-pause interactions.
                </p>
            </div>
            
            <button onclick="document.documentElement.classList.toggle('dark')" class="px-4 py-2 text-xs font-bold rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-700 dark:text-slate-200 hover:bg-slate-100 transition-colors cursor-pointer">
                Toggle Dark Mode
            </button>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-8">
            <!-- Success Toast Trigger -->
            <button onclick="window.toast.success('Patient record saved and synchronized with database.', 'Record Saved')" 
                class="flex items-center gap-3 p-4 rounded-2xl border border-emerald-200/80 dark:border-emerald-800/60 bg-emerald-50/50 dark:bg-emerald-950/20 hover:bg-emerald-50 dark:hover:bg-emerald-950/40 text-left transition-all hover:scale-[1.01] cursor-pointer group">
                <div class="w-10 h-10 rounded-xl bg-emerald-100 dark:bg-emerald-900/50 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-emerald-900 dark:text-emerald-200">Trigger Success Toast</h3>
                    <p class="text-xs text-emerald-700/80 dark:text-emerald-400/80">Confirms positive operations and saved states</p>
                </div>
            </button>

            <!-- Error Toast Trigger -->
            <button onclick="window.toast.error('Unable to connect to the central laboratory information service.', 'Sync Error')" 
                class="flex items-center gap-3 p-4 rounded-2xl border border-rose-200/80 dark:border-rose-800/60 bg-rose-50/50 dark:bg-rose-950/20 hover:bg-rose-50 dark:hover:bg-rose-950/40 text-left transition-all hover:scale-[1.01] cursor-pointer group">
                <div class="w-10 h-10 rounded-xl bg-rose-100 dark:bg-rose-900/50 text-rose-600 dark:text-rose-400 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 7.5h.01"/></svg>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-rose-900 dark:text-rose-200">Trigger Error Toast</h3>
                    <p class="text-xs text-rose-700/80 dark:text-rose-400/80">Alerts user to failures, network drops, or invalid data</p>
                </div>
            </button>

            <!-- Warning Toast Trigger -->
            <button onclick="window.toast.warning('Amoxicillin 500mg capsules are below the 50-unit threshold.', 'Low Inventory')" 
                class="flex items-center gap-3 p-4 rounded-2xl border border-amber-200/80 dark:border-amber-800/60 bg-amber-50/50 dark:bg-amber-950/20 hover:bg-amber-50 dark:hover:bg-amber-950/40 text-left transition-all hover:scale-[1.01] cursor-pointer group">
                <div class="w-10 h-10 rounded-xl bg-amber-100 dark:bg-amber-900/50 text-amber-600 dark:text-amber-400 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 4.878c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 18.75h.01"/></svg>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-amber-900 dark:text-amber-200">Trigger Warning Toast</h3>
                    <p class="text-xs text-amber-700/80 dark:text-amber-400/80">Highlights cautionary notices and low thresholds</p>
                </div>
            </button>

            <!-- Info Toast Trigger -->
            <button onclick="window.toast.info('New clinical guidelines have been published for maternal triage.', 'Clinical Notice')" 
                class="flex items-center gap-3 p-4 rounded-2xl border border-sky-200/80 dark:border-sky-800/60 bg-sky-50/50 dark:bg-sky-950/20 hover:bg-sky-50 dark:hover:bg-sky-950/40 text-left transition-all hover:scale-[1.01] cursor-pointer group">
                <div class="w-10 h-10 rounded-xl bg-sky-100 dark:bg-sky-900/50 text-sky-600 dark:text-sky-400 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z"/></svg>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-sky-900 dark:text-sky-200">Trigger Info Toast</h3>
                    <p class="text-xs text-sky-700/80 dark:text-sky-400/80">Provides general information and schedule updates</p>
                </div>
            </button>
        </div>

        <!-- Multi-trigger demo -->
        <div class="flex flex-wrap items-center gap-3 pt-4 border-t border-slate-200/60 dark:border-slate-800">
            <button onclick="triggerAll()" class="px-5 py-2.5 rounded-xl font-bold text-xs text-white bg-slate-900 hover:bg-slate-800 dark:bg-emerald-600 dark:hover:bg-emerald-700 transition-all shadow-md cursor-pointer">
                Trigger All 4 Toasts Simultaneously
            </button>
            <button onclick="window.toast.clear()" class="px-4 py-2.5 rounded-xl font-bold text-xs text-slate-600 dark:text-slate-300 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 transition-colors cursor-pointer">
                Dismiss All Active
            </button>
        </div>
    </div>
</div>

<script>
function triggerAll() {
    window.toast.success('Patient consultation record was successfully created and committed.', 'Record Created', 8000);
    setTimeout(() => {
        window.toast.error('Failed to dispatch SMS notification to patient mobile number.', 'SMS Delivery Failed', 8000);
    }, 150);
    setTimeout(() => {
        window.toast.warning('Blood pressure reading (145/95 mmHg) exceeds standard baseline.', 'Vital Sign Alert', 8000);
    }, 300);
    setTimeout(() => {
        window.toast.info('Laboratory specimen is now queued for biochemistry analysis.', 'Specimen Queued', 8000);
    }, 450);
}

// Auto trigger if requested
if (new URLSearchParams(window.location.search).get('auto') === '1') {
    document.addEventListener('DOMContentLoaded', () => {
        setTimeout(triggerAll, 300);
    });
}
</script>
@endsection
