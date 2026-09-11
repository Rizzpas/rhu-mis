@extends('layouts.app')

@section('content')
<div class="py-10 sm:py-16 px-4 sm:px-6 lg:px-8 max-w-2xl mx-auto">
    <x-breadcrumb :items="['Error 500 (Server Error)' => '']" />

    <div class="rounded-3xl bg-white/90 dark:bg-slate-900/90 border border-slate-200/90 dark:border-slate-800 shadow-xl backdrop-blur-md p-8 sm:p-12 relative overflow-hidden text-center">
        {{-- Decorative Gradient Bar --}}
        <div class="absolute top-0 inset-x-0 h-1.5 bg-gradient-to-r from-red-500 via-rose-500 to-pink-600"></div>

        {{-- Big Graphic Number with Icon --}}
        <div class="relative mb-6">
            <span class="font-display text-8xl sm:text-9xl font-black text-red-500/15 dark:text-red-400/10 tracking-tighter select-none">
                500
            </span>
            <div class="absolute inset-0 flex items-center justify-center">
                <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-2xl bg-red-50 dark:bg-red-950/60 border border-red-200/80 dark:border-red-800/60 flex items-center justify-center text-red-600 dark:text-red-400 shadow-inner">
                    <svg class="w-8 h-8 sm:w-10 sm:h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
            </div>
        </div>

        {{-- Titles & Description --}}
        <h1 class="font-display text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-white tracking-tight">
            Internal Server Error
        </h1>

        <p class="mt-2 text-xs sm:text-sm text-slate-500 dark:text-slate-400 font-medium italic">
            "Nagkaroon po ng hindi inaasahang aberya sa aming system."
        </p>

        <p class="mt-3 text-xs sm:text-sm text-slate-600 dark:text-slate-300 leading-relaxed max-w-md mx-auto">
            An internal server error occurred while fulfilling your request. Rest assured your existing records remain secure, and our technical staff is addressing the issue.
        </p>

        {{-- Technical Support Helpdesk Callout --}}
        <div class="mt-6 p-4 rounded-2xl bg-red-50/50 dark:bg-red-950/30 border border-red-200/60 dark:border-red-800/40 text-left flex items-start gap-3">
            <div class="w-6 h-6 rounded-lg bg-red-100 dark:bg-red-900/60 text-red-700 dark:text-red-300 flex items-center justify-center shrink-0 mt-0.5">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z" />
                </svg>
            </div>
            <div class="text-[11px] text-slate-600 dark:text-slate-300 leading-relaxed">
                <strong class="text-slate-800 dark:text-white">Need Urgent Assistance?</strong> For medical emergency or immediate booking verification, please visit the Rural Health Unit of Silang frontdesk or call the Municipal Health Hotline.
            </div>
        </div>

        {{-- Action Buttons --}}
        <div class="mt-8 flex flex-col sm:flex-row items-center justify-center gap-3">
            <button onclick="window.location.reload()"
                    class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-2.5 rounded-xl text-xs font-bold text-white bg-gradient-to-r from-red-600 to-rose-700 hover:from-red-700 hover:to-rose-800 shadow-md shadow-red-900/10 transition cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                </svg>
                <span>Reload Page</span>
            </button>

            <button onclick="window.history.back()"
                    class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-bold text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 transition cursor-pointer shadow-2xs">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                <span>Go Back</span>
            </button>

            <a href="{{ route('welcome') }}"
               class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/80 text-xs font-bold text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700 transition cursor-pointer">
                <span>Return Home</span>
            </a>
        </div>
    </div>
</div>
@endsection
