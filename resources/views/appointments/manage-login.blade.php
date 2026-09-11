@extends('layouts.app')

@section('content')
<div class="py-6 sm:py-10">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">

        <x-breadcrumb :items="['Appointments' => route('appointment.create'), 'Manage Appointment' => '']" />

        {{-- Main Two-Column Bento Layout --}}
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-start">

            {{-- Left Column: Hero Showcase & Information Guide --}}
            <div class="lg:col-span-6 flex flex-col justify-between space-y-6">
                <div>
                    {{-- Title --}}
                    <h1 class="font-display text-3xl sm:text-4xl font-extrabold text-slate-900 dark:text-white tracking-tight leading-tight">
                        Manage Your <span class="text-emerald-700 dark:text-emerald-400">Appointment</span>
                    </h1>

                    {{-- Description --}}
                    <p class="mt-3 text-sm sm:text-base text-slate-600 dark:text-slate-300 font-normal leading-relaxed max-w-xl">
                        Access your consultation schedule, track your real-time approval status, or conveniently reschedule your booking without waiting in line at the health center.
                    </p>
                </div>

                {{-- Interactive Features --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 pt-2">
                    {{-- Feature 1 --}}
                    <div>
                        <div class="flex items-center gap-2 mb-1.5">
                            <svg class="w-4 h-4 text-slate-500 dark:text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <h3 class="text-sm font-semibold text-slate-900 dark:text-white">Real-Time Status</h3>
                        </div>
                        <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">
                            Check if your consultation is confirmed, pending triage review, or completed.
                        </p>
                    </div>

                    {{-- Feature 2 --}}
                    <div>
                        <div class="flex items-center gap-2 mb-1.5">
                            <svg class="w-4 h-4 text-slate-500 dark:text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <h3 class="text-sm font-semibold text-slate-900 dark:text-white">Fast Reschedule</h3>
                        </div>
                        <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">
                            Need to change your date? Easily choose another available slot with a few clicks.
                        </p>
                    </div>

                    {{-- Feature 3 --}}
                    <div>
                        <div class="flex items-center gap-2 mb-1.5">
                            <svg class="w-4 h-4 text-slate-500 dark:text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <h3 class="text-sm font-semibold text-slate-900 dark:text-white">Appointment Slips</h3>
                        </div>
                        <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">
                            Download or print your official QR booking slip to present at the triage reception.
                        </p>
                    </div>

                    {{-- Feature 4 --}}
                    <div>
                        <div class="flex items-center gap-2 mb-1.5">
                            <svg class="w-4 h-4 text-slate-500 dark:text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <h3 class="text-sm font-semibold text-slate-900 dark:text-white">Preparation Guides</h3>
                        </div>
                        <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">
                            View required IDs, PhilHealth documents, and laboratory fasting guidelines.
                        </p>
                    </div>
                </div>

                {{-- Reference Number Help Callout --}}
                <div class="p-4 rounded-2xl bg-emerald-50/60 dark:bg-emerald-950/30 border border-emerald-200/60 dark:border-emerald-800/40 flex items-start gap-3">
                    <div class="w-8 h-8 rounded-lg bg-emerald-100 dark:bg-emerald-900/60 text-emerald-700 dark:text-emerald-300 flex items-center justify-center shrink-0 mt-0.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="text-xs text-slate-600 dark:text-slate-300 leading-relaxed">
                        <span class="font-bold text-slate-900 dark:text-white">Can't locate your Reference Number?</span>
                        Please check the SMS notification or confirmation email received upon booking. For immediate assistance, call the RHU Silang hotline at
                        <a href="tel:(046)432-1234" class="font-semibold text-emerald-700 dark:text-emerald-400 hover:underline">(046) 432-1234</a>.
                    </div>
                </div>
            </div>

            {{-- Right Column: Portal Access Form Card --}}
            <div class="lg:col-span-6">
                <div class="rounded-3xl p-6 sm:p-9 bg-white dark:bg-slate-800/95 border border-slate-200/90 dark:border-slate-700/80 shadow-md relative overflow-hidden"
                     x-data="{ isSubmitting: false }">
                    
                    {{-- Subtle top decorative gradient line --}}
                    <div class="absolute top-0 inset-x-0 h-1.5 bg-gradient-to-r from-emerald-500 via-teal-500 to-emerald-600"></div>

                    {{-- Form Card Header --}}
                    <div class="flex items-center gap-3.5 pb-6 mb-6 border-b border-slate-100 dark:border-slate-700/70">
                        <div class="w-11 h-11 rounded-2xl bg-emerald-50 dark:bg-emerald-950/60 border border-emerald-200/70 dark:border-emerald-800/50 flex items-center justify-center text-emerald-700 dark:text-emerald-400 shrink-0 shadow-2xs">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                            </svg>
                        </div>
                        <div>
                            <h2 class="font-display text-lg sm:text-xl font-bold text-slate-900 dark:text-white">
                                Appointment Verification
                            </h2>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                                Enter your booking credentials to open your record
                            </p>
                        </div>
                    </div>

                    {{-- Global Validation Errors Banner --}}
                    @if ($errors->any())
                        <div class="mb-6 p-4 rounded-2xl bg-red-50 dark:bg-red-950/40 border border-red-200 dark:border-red-800/60 flex items-start gap-3">
                            <div class="w-7 h-7 rounded-lg bg-red-100 dark:bg-red-900/60 text-red-600 dark:text-red-400 flex items-center justify-center shrink-0 mt-0.5">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                            </div>
                            <div class="text-xs text-red-700 dark:text-red-300">
                                <p class="font-bold mb-0.5">Verification Failed</p>
                                <ul class="list-disc list-inside space-y-0.5">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    @endif

                    {{-- Form --}}
                    <form action="{{ route('appointment.login') }}" method="POST" class="space-y-5" @submit="isSubmitting = true">
                        @csrf
                         
                        {{-- Reference Number Input --}}
                        <div>
                            <div class="flex items-center justify-between mb-1.5">
                                <label for="reference_number" class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300">
                                    {{ __('Reference Number') }} <span class="text-emerald-700 dark:text-emerald-400">*</span>
                                </label>
                                <span class="text-[11px] text-slate-400 dark:text-slate-500 font-mono">Format: APT-XXXXXXX</span>
                            </div>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400 dark:text-slate-500">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"/>
                                    </svg>
                                </div>
                                <input id="reference_number"
                                       name="reference_number"
                                       type="text"
                                       required
                                       value="{{ old('reference_number') }}"
                                       class="block w-full pl-10 pr-4 py-3 rounded-xl border @error('reference_number') border-red-400 dark:border-red-600 ring-1 ring-red-400 @else border-slate-300 dark:border-slate-600 @enderror bg-slate-50/80 dark:bg-slate-900/60 text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-600 text-sm uppercase tracking-wider font-mono transition"
                                       placeholder="APT-XXXXXXXX"
                                       autocomplete="off"
                                       autofocus>
                            </div>
                            @error('reference_number')
                                <p class="mt-1.5 text-xs font-medium text-red-600 dark:text-red-400 flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                    </svg>
                                    <span>{{ $message }}</span>
                                </p>
                            @enderror
                            <p class="mt-1 text-[11px] text-slate-500 dark:text-slate-400">
                                Enter the unique reference code provided upon appointment confirmation.
                            </p>
                        </div>

                        {{-- Email Address Input --}}
                        <div>
                            <div class="flex items-center justify-between mb-1.5">
                                <label for="email" class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300">
                                    {{ __('Email Address') }} <span class="text-emerald-700 dark:text-emerald-400">*</span>
                                </label>
                                <span class="text-[11px] text-slate-400 dark:text-slate-500">Registered with booking</span>
                            </div>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400 dark:text-slate-500">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                                <input id="email"
                                       name="email"
                                       type="email"
                                       required
                                       value="{{ old('email') }}"
                                       class="block w-full pl-10 pr-4 py-3 rounded-xl border @error('email') border-red-400 dark:border-red-600 ring-1 ring-red-400 @else border-slate-300 dark:border-slate-600 @enderror bg-slate-50/80 dark:bg-slate-900/60 text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-600 text-sm transition"
                                       placeholder="patient@email.com"
                                       autocomplete="email">
                            </div>
                            @error('email')
                                <p class="mt-1.5 text-xs font-medium text-red-600 dark:text-red-400 flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                    </svg>
                                    <span>{{ $message }}</span>
                                </p>
                            @enderror
                            <p class="mt-1 text-[11px] text-slate-500 dark:text-slate-400">
                                Must match the patient or guardian email provided in the booking form.
                            </p>
                        </div>

                        {{-- Action Submit Button --}}
                        <div class="pt-2">
                            <button type="submit"
                                    :disabled="isSubmitting"
                                    class="w-full inline-flex items-center justify-center gap-2.5 py-3.5 px-5 rounded-xl text-sm font-bold text-white bg-gradient-to-r from-emerald-700 to-teal-700 hover:from-emerald-800 hover:to-teal-800 shadow-md shadow-emerald-900/10 active:scale-[0.99] transition-all cursor-pointer disabled:opacity-75 disabled:cursor-wait">
                                <template x-if="!isSubmitting">
                                    <div class="inline-flex items-center gap-2">
                                        <span>{{ __('Access Appointment Record') }}</span>
                                        <svg class="w-4 h-4 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                                        </svg>
                                    </div>
                                </template>
                                <template x-if="isSubmitting">
                                    <div class="inline-flex items-center gap-2">
                                        <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                        <span>{{ __('Verifying Record...') }}</span>
                                    </div>
                                </template>
                            </button>
                        </div>
                    </form>
                    
                    {{-- Bottom Links & Booking Prompt --}}
                    <div class="mt-8 pt-6 border-t border-slate-100 dark:border-slate-700/70 space-y-4 text-center">
                        <div class="flex flex-col sm:flex-row items-center justify-center gap-2 text-xs text-slate-500 dark:text-slate-400">
                            <span>Don't have an appointment yet?</span>
                            <a href="{{ route('appointment.create') }}" class="font-bold text-emerald-700 dark:text-emerald-400 hover:text-emerald-800 dark:hover:text-emerald-300 underline underline-offset-2 transition-colors">
                                Book a Free Consultation &rarr;
                            </a>
                        </div>

                        <div class="flex items-center justify-center gap-2 text-[11px] text-slate-400 dark:text-slate-500">
                            <svg class="w-3.5 h-3.5 text-emerald-600 dark:text-emerald-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                            </svg>
                            <span>Protected by Republic Act No. 10173 (Data Privacy Act of 2012)</span>
                        </div>
                    </div>
                </div>
            </div>

        </div>

    </div>
</div>
@endsection
