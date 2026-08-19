@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-transparent flex flex-col justify-center py-12 sm:px-6 lg:px-8">
    
    {{-- Header with 21st.dev Glass Styling --}}
    <div class="sm:mx-auto sm:w-full sm:max-w-md text-center" data-reveal>
        <span class="inline-flex items-center gap-2 px-3.5 py-1.5 bg-emerald-500/10 dark:bg-emerald-500/20 text-emerald-800 dark:text-emerald-300 text-xs font-bold rounded-full mb-4 uppercase tracking-wider border border-emerald-500/20 backdrop-blur-md shadow-sm">
            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
            Patient Portal
        </span>
        <h1 class="font-display text-3xl sm:text-4xl font-extrabold text-gray-900 dark:text-white tracking-tight leading-tight">
            Manage <span class="text-emerald-600 dark:text-emerald-400">Appointment</span>
        </h1>
        <p class="mt-2 text-sm text-gray-600 dark:text-gray-400 max-w-sm mx-auto">
            Enter your appointment reference number and email to view, reschedule, or cancel your booking.
        </p>
    </div>

    {{-- Frosted Glass Login Card --}}
    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md px-4" data-reveal style="transition-delay: 80ms">
        <div class="relative rounded-3xl p-8 sm:p-10 bg-white/75 dark:bg-gray-900/75 border border-white/90 dark:border-gray-800/80 backdrop-blur-xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] dark:shadow-[0_8px_30px_rgb(0,0,0,0.25)] overflow-hidden">
            
            {{-- Ambient radial background glow --}}
            <div class="absolute -top-12 -right-12 w-36 h-36 rounded-full blur-3xl pointer-events-none opacity-30 bg-emerald-500"></div>

            <form action="{{ route('appointment.login') }}" method="POST" class="space-y-5 relative z-10">
                @csrf
                 
                <div>
                    <label for="reference_number" class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1.5">
                        {{ __('Reference Number') }} <span class="text-emerald-600">*</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"/></svg>
                        </div>
                        <input id="reference_number" name="reference_number" type="text" required 
                            class="block w-full pl-10 pr-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50/80 dark:bg-gray-800/80 text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500 text-sm uppercase tracking-wider font-mono shadow-xs transition"
                            placeholder="APT-XXXXXXX">
                    </div>
                </div>

                <div>
                    <label for="email" class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1.5">
                        {{ __('Email Address') }} <span class="text-emerald-600">*</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        </div>
                        <input id="email" name="email" type="email" required 
                            class="block w-full pl-10 pr-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50/80 dark:bg-gray-800/80 text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500 text-sm shadow-xs transition"
                            placeholder="patient@gmail.com">
                    </div>
                    @error('email')
                        <p class="mt-1.5 text-xs font-medium text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div class="pt-2">
                    <button type="submit" 
                            class="w-full inline-flex items-center justify-center gap-2 py-3 px-4 rounded-xl text-sm font-bold text-white shadow-md transition-all duration-200 hover:scale-[1.02] active:scale-[0.98] cursor-pointer"
                            style="background: linear-gradient(135deg, #10b981, #0F3D3E);">
                        <span>{{ __('Access Appointment') }}</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </button>
                </div>
            </form>
            
            <div class="mt-6 pt-5 border-t border-gray-100 dark:border-gray-800 text-center relative z-10">
                <a href="{{ route('welcome') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-emerald-700 dark:text-emerald-400 hover:text-emerald-800 transition">
                    &larr; {{ __('Back to Home') }}
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
