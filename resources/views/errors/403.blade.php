@extends('layouts.app')

@section('content')
<div class="py-10 sm:py-16 px-4 sm:px-6 lg:px-8 max-w-2xl mx-auto">
    <x-breadcrumb :items="['Error 403 (Access Denied)' => '']" />

    <div class="rounded-3xl bg-white/90 dark:bg-slate-900/90 border border-slate-200/90 dark:border-slate-800 shadow-xl backdrop-blur-md p-8 sm:p-12 relative overflow-hidden text-center">
        {{-- Decorative Gradient Bar --}}
        <div class="absolute top-0 inset-x-0 h-1.5 bg-gradient-to-r from-rose-500 via-amber-500 to-rose-600"></div>

        {{-- Big Graphic Number with Icon --}}
        <div class="relative mb-6">
            <span class="font-display text-8xl sm:text-9xl font-black text-rose-500/15 dark:text-rose-400/10 tracking-tighter select-none">
                403
            </span>
            <div class="absolute inset-0 flex items-center justify-center">
                <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-2xl bg-rose-50 dark:bg-rose-950/60 border border-rose-200/80 dark:border-rose-800/60 flex items-center justify-center text-rose-600 dark:text-rose-400 shadow-inner">
                    <svg class="w-8 h-8 sm:w-10 sm:h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                    </svg>
                </div>
            </div>
        </div>

        {{-- Titles & Description --}}
        <h1 class="font-display text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-white tracking-tight">
            Access Restricted
        </h1>

        <p class="mt-2 text-xs sm:text-sm text-slate-500 dark:text-slate-400 font-medium italic">
            "Ipagpaumanhin po, wala kayong pahintulot na buksan ang bahaging ito."
        </p>

        <p class="mt-3 text-xs sm:text-sm text-slate-600 dark:text-slate-300 leading-relaxed max-w-md mx-auto">
            You do not have permission to view this medical record or portal view. If you believe this is an error, please contact your unit administrator or log in with authorized role credentials.
        </p>

        {{-- Security Notice Callout --}}
        <div class="mt-6 p-4 rounded-2xl bg-rose-50/60 dark:bg-rose-950/30 border border-rose-200/60 dark:border-rose-800/40 text-left flex items-start gap-3">
            <div class="w-6 h-6 rounded-lg bg-rose-100 dark:bg-rose-900/60 text-rose-700 dark:text-rose-300 flex items-center justify-center shrink-0 mt-0.5">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                </svg>
            </div>
            <p class="text-[11px] text-slate-600 dark:text-slate-300 leading-relaxed">
                <strong class="text-slate-800 dark:text-white">Strict Compliance:</strong> In accordance with Republic Act No. 10173 (Data Privacy Act), clinical records are restricted to designated on-duty medical personnel only.
            </p>
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
                <span>Return to Home</span>
            </a>

            <a href="{{ route('login') }}"
               class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-2.5 rounded-xl border border-emerald-300 dark:border-emerald-700/80 bg-emerald-50 dark:bg-emerald-950/40 text-xs font-bold text-emerald-700 dark:text-emerald-300 hover:bg-emerald-100 dark:hover:bg-emerald-900/60 transition cursor-pointer">
                <span>Staff Portal</span>
            </a>
        </div>
    </div>
</div>
@endsection
