@extends('layouts.app')

@section('content')
<div class="py-10 sm:py-16 px-4 sm:px-6 lg:px-8 max-w-2xl mx-auto">
    <x-breadcrumb :items="['Error 401 (Unauthorized)' => '']" />

    <div class="rounded-3xl bg-white/90 dark:bg-slate-900/90 border border-slate-200/90 dark:border-slate-800 shadow-xl backdrop-blur-md p-8 sm:p-12 relative overflow-hidden text-center">
    
        {{-- Big Graphic Number with Icon --}}
        <div class="relative mb-6">
            <span class="font-display text-8xl sm:text-9xl font-black text-indigo-500/15 dark:text-indigo-400/10 tracking-tighter select-none">
                401
            </span>
            <div class="absolute inset-0 flex items-center justify-center">
                <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-2xl bg-indigo-50 dark:bg-indigo-950/60 border border-indigo-200/80 dark:border-indigo-800/60 flex items-center justify-center text-indigo-600 dark:text-indigo-400 shadow-inner">
                    <svg class="w-8 h-8 sm:w-10 sm:h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                </div>
            </div>
        </div>

        {{-- Titles & Description --}}
        <h1 class="font-display text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-white tracking-tight">
            Authentication Required
        </h1>

        <p class="mt-2 text-xs sm:text-sm text-slate-500 dark:text-slate-400 font-medium italic">
            "Kinakailangan pong mag-login upang ma-access ang pahinang ito."
        </p>

        <p class="mt-3 text-xs sm:text-sm text-slate-600 dark:text-slate-300 leading-relaxed max-w-md mx-auto">
            You are attempting to access a secured resource without an active or valid login session. Please sign in to verify your identity.
        </p>

        {{-- Helpful Quick Access Cards --}}
        <div class="mt-6 pt-5 border-t border-slate-100 dark:border-slate-800/80 text-left">
            <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500 mb-3 text-center">
                Popular Public Services
            </p>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                <a href="{{ route('appointment.create') }}" class="p-3.5 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200/70 dark:border-slate-700/60 flex items-center gap-3 hover:border-emerald-400 dark:hover:border-emerald-500 hover:bg-emerald-50/40 dark:hover:bg-emerald-950/30 transition group">
                    <span class="w-8 h-8 rounded-lg bg-emerald-100 dark:bg-emerald-900/50 text-emerald-700 dark:text-emerald-300 flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    </span>
                    <div>
                        <div class="text-xs font-bold text-slate-800 dark:text-white group-hover:text-emerald-600 dark:group-hover:text-emerald-400">Book Appointment</div>
                        <div class="text-[11px] text-slate-500 dark:text-slate-400">Consultation schedule</div>
                    </div>
                </a>

                <a href="{{ route('appointment.manage') }}" class="p-3.5 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200/70 dark:border-slate-700/60 flex items-center gap-3 hover:border-teal-400 dark:hover:border-teal-500 hover:bg-teal-50/40 dark:hover:bg-teal-950/30 transition group">
                    <span class="w-8 h-8 rounded-lg bg-teal-100 dark:bg-teal-900/50 text-teal-700 dark:text-teal-300 flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
                    </span>
                    <div>
                        <div class="text-xs font-bold text-slate-800 dark:text-white group-hover:text-teal-600 dark:group-hover:text-teal-400">Patient Lookup</div>
                        <div class="text-[11px] text-slate-500 dark:text-slate-400">Reference code & contact no.</div>
                    </div>
                </a>
            </div>
        </div>

        {{-- Action Buttons --}}
        <div class="mt-8 flex flex-col sm:flex-row items-center justify-center gap-3">
            <button onclick="window.history.back()"
                    class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-bold text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 transition cursor-pointer shadow-2xs">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                <span>Go Back</span>
            </button>

            <a href="{{ route('welcome') }}"
               class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-2.5 rounded-xl text-xs font-bold text-white bg-gradient-to-r from-emerald-600 to-teal-700 hover:from-emerald-700 hover:to-teal-800 shadow-md shadow-emerald-900/10 transition cursor-pointer">
                <span>Back to Home</span>
            </a>
        </div>
    </div>
</div>
@endsection
