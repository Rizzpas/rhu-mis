@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-transparent flex flex-col justify-center py-12 sm:px-6 lg:px-8">
    
    {{-- Header --}}
    <div class="sm:mx-auto sm:w-full sm:max-w-md text-center">
        <div class="mb-3">
            <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-emerald-100/80 dark:bg-emerald-950/60 text-emerald-900 dark:text-emerald-300 text-[11px] font-bold uppercase tracking-widest rounded-md border border-emerald-300/50 dark:border-emerald-700/50">
                <svg class="w-3.5 h-3.5 text-emerald-700 dark:text-emerald-400 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd"/>
                </svg>
                Patient Portal • RHU Silang
            </span>
        </div>
        <h1 class="font-display text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-white tracking-tight leading-tight">
            Manage Your <span class="text-emerald-700 dark:text-emerald-400">Appointment</span>
        </h1>
        <p class="mt-2 text-xs sm:text-sm text-slate-600 dark:text-slate-400 max-w-sm mx-auto font-normal">
            Enter your appointment reference number and email to check status, reschedule, or cancel your booking.
        </p>
    </div>

    {{-- Clean Patient Portal Card --}}
    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md px-4">
        <div class="rounded-3xl p-7 sm:p-9 bg-white dark:bg-slate-800/95 border border-slate-200/90 dark:border-slate-700/80 shadow-xs">
            
            <form action="{{ route('appointment.login') }}" method="POST" class="space-y-5">
                @csrf
                 
                <div>
                    <label for="reference_number" class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">
                        {{ __('Reference Number') }} <span class="text-emerald-700 dark:text-emerald-400">*</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"/></svg>
                        </div>
                        <input id="reference_number" name="reference_number" type="text" required 
                            class="block w-full pl-10 pr-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-600 bg-slate-50/80 dark:bg-slate-900/60 text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-600 text-sm uppercase tracking-wider font-mono transition"
                            placeholder="APT-XXXXXXX">
                    </div>
                </div>

                <div>
                    <label for="email" class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">
                        {{ __('Email Address') }} <span class="text-emerald-700 dark:text-emerald-400">*</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        </div>
                        <input id="email" name="email" type="email" required 
                            class="block w-full pl-10 pr-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-600 bg-slate-50/80 dark:bg-slate-900/60 text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-600 text-sm transition"
                            placeholder="patient@email.com">
                    </div>
                    @error('email')
                        <p class="mt-1.5 text-xs font-medium text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div class="pt-2">
                    <button type="submit" 
                            class="w-full inline-flex items-center justify-center gap-2 py-3 px-4 rounded-xl text-sm font-bold text-white bg-emerald-700 hover:bg-emerald-800 shadow-xs transition-colors cursor-pointer">
                        <span>{{ __('Access Appointment Record') }}</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </button>
                </div>
            </form>
            
            <div class="mt-6 pt-5 border-t border-slate-100 dark:border-slate-700/70 text-center">
                <a href="{{ route('welcome') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-emerald-700 dark:text-emerald-400 hover:text-emerald-800 transition">
                    &larr; {{ __('Back to Home') }}
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
