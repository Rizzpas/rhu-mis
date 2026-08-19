@extends('layouts.app')

@section('content')
@php
    $unitMeta = [
        'main-health-center' => [
            'icon' => '<svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>',
            'accent_hex' => '#10b981',
            'badge' => 'Primary Healthcare & Diagnostics',
            'status' => 'Mon - Fri | 8:00 AM - 5:00 PM',
            'coverage' => 'PhilHealth & Free General Consult',
            'requirements' => ['Valid Government ID or Barangay ID', 'PhilHealth ID / MDR (if available)', 'Previous Medical / Lab Records'],
        ],
        'lying-in-clinic' => [
            'icon' => '<svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z"/></svg>',
            'accent_hex' => '#ec4899',
            'badge' => '24/7 Maternal & Birthing Center',
            'status' => '24/7 Continuous Emergency Care',
            'coverage' => 'PhilHealth Maternity Care Package (MCP)',
            'requirements' => ['Prenatal / Mother-Child Record Book (Pink Book)', 'Valid Government ID of Mother & Spouse', 'PhilHealth Member Data Record (MDR)', 'Birth Registration Details'],
        ],
        'dental-clinic' => [
            'icon' => '<svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15.182 15.182a4.5 4.5 0 01-6.364 0M21 12a9 9 0 11-18 0 9 9 0 0118 0zM9.75 9.75c0 .414-.168.75-.375.75S9 10.164 9 9.75 9.168 9 9.375 9s.375.336.375.75zm-.375 0h.008v.015h-.008V9.75zm5.625 0c0 .414-.168.75-.375.75s-.375-.336-.375-.75.168-.75.375-.75.375.336.375.75zm-.375 0h.008v.015h-.008V9.75z"/></svg>',
            'accent_hex' => '#0ea5e9',
            'badge' => 'Community Oral Healthcare',
            'status' => 'Mon - Fri | 8:00 AM - 5:00 PM',
            'coverage' => 'Free Extraction & Dental Consultation',
            'requirements' => ['Valid ID', 'Medical History (especially BP & allergies)', 'Guardian Consent for Minors'],
        ],
        'tb-dots-facility' => [
            'icon' => '<svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.75 3.104v5.714a2.25 2.25 0 01-.659 1.591L5 14.5M9.75 3.104c-.251.023-.501.05-.75.082m.75-.082a24.301 24.301 0 014.5 0m0 0v5.714c0 .597.237 1.17.659 1.591L19.8 15.3M14.25 3.104c.251.023.501.05.75.082M19.8 15.3l-1.57.393A9.065 9.065 0 0112 15a9.065 9.065 0 00-6.23.693L5 14.5m14.8.8l1.402 1.402c1.232 1.232.65 3.318-1.067 3.611A48.309 48.309 0 0112 21c-2.773 0-5.491-.235-8.135-.687-1.718-.293-2.3-2.379-1.067-3.61L5 14.5"/></svg>',
            'accent_hex' => '#f59e0b',
            'badge' => 'National Tuberculosis Elimination Center',
            'status' => 'Mon - Fri | 8:00 AM - 5:00 PM',
            'coverage' => '100% Free Medication & GeneXpert Screening',
            'requirements' => ['Valid ID', 'Prior Chest X-ray (if available)', 'Sputum Sample Container (provided on-site)'],
        ],
        'animal-bite-center' => [
            'icon' => '<svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v3.75m0-10.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285zm0 13.036h.008v.008H12v-.008z"/></svg>',
            'accent_hex' => '#ef4444',
            'badge' => 'Rabies Treatment & Post-Exposure Center',
            'status' => 'Urgent Care & Vaccination',
            'coverage' => 'Free Public Health Rabies Vaccine Shots',
            'requirements' => ['Patient Valid ID', 'Details of Biting Animal (dog/cat status, observation day)', 'Vaccination History Card'],
        ],
    ];

    $m = $unitMeta[$unit['slug']] ?? [
        'icon' => '<svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>',
        'accent_hex' => '#10b981',
        'badge' => 'Silang RHU Facility',
        'status' => 'Mon - Fri | 8:00 AM - 5:00 PM',
        'coverage' => 'Public Healthcare Service',
        'requirements' => ['Valid ID', 'Medical Documents'],
    ];

    $steps = \App\Models\SiteSetting::getJson('steps_data_' . $unit['slug'], [
        ['title' => 'Admission & Triage Check-in', 'description' => 'Present your records or valid ID at the admission desk. Initial health assessment and digital record retrieval are completed promptly.', 'time' => '~5 mins', 'tip' => 'Bring a valid ID and any previous medical records.'],
        ['title' => 'Clinical Monitoring & Vitals', 'description' => 'Our registered nurses and medical staff will record your vital signs, history, and perform preliminary clinical screening.', 'time' => '~5-10 mins', 'tip' => 'Wear comfortable clothing for quick and accurate examination.'],
        ['title' => 'Doctor / Practitioner Consultation', 'description' => 'Meet with the attending physician or specialist for dedicated diagnosis, treatment execution, and e-prescription issuance.', 'time' => '~15 mins', 'tip' => 'List your current symptoms and maintenance medications beforehand.'],
        ['title' => 'Post-Care & Pharmacy Release', 'description' => 'Receive clear aftercare instructions, schedule follow-up appointments, and claim prescribed medications from the RHU pharmacy.', 'time' => '~5 mins', 'tip' => 'Review all prescription dosage instructions before departing.']
    ]);

    $stepIcons = [
        '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>',
        '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>',
        '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>',
        '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>',
    ];
@endphp

<div class="bg-transparent min-h-screen pb-24">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-8 sm:pt-10">
        
        {{-- Floating Frosted Back Breadcrumb --}}
        <div class="mb-8" data-reveal>
            <a href="{{ route('units.index') }}" 
               class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-white/70 dark:bg-gray-900/70 border border-white/80 dark:border-gray-800 backdrop-blur-xl shadow-xs text-xs font-bold text-gray-700 dark:text-gray-300 hover:text-emerald-600 dark:hover:text-emerald-400 hover:scale-105 transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Back to All Facilities
            </a>
        </div>

        {{-- Facility Hero Showcase (21st.dev Glass Bento Banner) --}}
        <div class="relative rounded-3xl p-8 sm:p-12 mb-12 bg-white/75 dark:bg-gray-900/75 border border-white/90 dark:border-gray-800/80 backdrop-blur-xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] dark:shadow-[0_8px_30px_rgb(0,0,0,0.25)] overflow-hidden" data-reveal>
            
            {{-- Ambient radial background glow --}}
            <div class="absolute -top-24 -right-24 w-80 h-80 rounded-full blur-3xl pointer-events-none opacity-30 dark:opacity-40"
                 style="background: {{ $m['accent_hex'] }};"></div>

            <div class="relative z-10 grid grid-cols-1 lg:grid-cols-12 gap-8 items-center">
                
                {{-- Left: Details --}}
                <div class="lg:col-span-8">
                    <div class="flex flex-wrap items-center gap-2.5 mb-5">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-white/90 dark:bg-gray-800/90 text-gray-800 dark:text-gray-200 border border-gray-200/80 dark:border-gray-700/80 shadow-xs">
                            <span class="w-2 h-2 rounded-full" style="background: {{ $m['accent_hex'] }};"></span>
                            {{ $m['badge'] }}
                        </span>
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-500/10 text-emerald-800 dark:text-emerald-300 border border-emerald-500/20">
                            {{ $m['coverage'] }}
                        </span>
                    </div>

                    <div class="flex items-center gap-5 mb-4">
                        <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-2xl flex items-center justify-center text-white shrink-0 shadow-lg"
                             style="background: linear-gradient(135deg, {{ $m['accent_hex'] }}, #0F3D3E);">
                            {!! $m['icon'] !!}
                        </div>
                        <div>
                            <h1 class="font-display text-3xl sm:text-4xl lg:text-5xl font-extrabold text-gray-900 dark:text-white tracking-tight leading-tight">
                                {{ $unit['name'] }}
                            </h1>
                            <p class="text-xs sm:text-sm font-semibold text-gray-400 dark:text-gray-500 mt-1">
                                Rural Health Unit · Silang, Cavite
                            </p>
                        </div>
                    </div>

                    <p class="text-base sm:text-lg text-gray-600 dark:text-gray-300 font-normal leading-relaxed max-w-3xl">
                        {{ $unit['desc'] }}
                    </p>
                </div>

                {{-- Right: Quick Action Widget --}}
                <div class="lg:col-span-4 flex flex-col gap-3.5 bg-gray-50/80 dark:bg-gray-800/60 p-6 rounded-2xl border border-gray-200/60 dark:border-gray-700/60">
                    <div class="flex items-center justify-between text-xs text-gray-500 dark:text-gray-400 font-medium">
                        <span>Status</span>
                        <span class="font-bold text-emerald-600 dark:text-emerald-400 flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                            Accepting Patients
                        </span>
                    </div>
                    <div class="text-xs text-gray-600 dark:text-gray-300 flex items-center gap-2">
                        <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>{{ $m['status'] }}</span>
                    </div>

                    <a href="{{ route('appointment.create') }}" 
                       class="mt-2 w-full inline-flex items-center justify-center gap-2 px-5 py-3 rounded-xl text-sm font-bold text-white shadow-md transition-all duration-200 hover:scale-[1.02] active:scale-[0.98] cursor-pointer"
                       style="background: linear-gradient(135deg, {{ $m['accent_hex'] }}, #15803d);">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                        Book Appointment
                    </a>

                    <a href="{{ route('appointment.manage') }}" 
                       class="w-full inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl text-xs font-semibold text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700/80 hover:bg-gray-100 dark:hover:bg-gray-700 border border-gray-200 dark:border-gray-600 transition">
                        Manage Existing Booking
                    </a>
                </div>

            </div>
        </div>

        {{-- 2-Column Bento Grid: Journey Guide (Left) + Facility Snapshot Directory (Right) --}}
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            
            {{-- Left Column: Step-by-Step Patient Process Timeline (8 cols) --}}
            <div class="lg:col-span-8">
                <div class="rounded-3xl p-7 sm:p-10 bg-white/75 dark:bg-gray-900/75 border border-white/90 dark:border-gray-800/80 backdrop-blur-xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] dark:shadow-[0_8px_30px_rgb(0,0,0,0.25)]" data-reveal>
                    
                    <div class="flex items-center justify-between gap-4 mb-8 pb-6 border-b border-gray-100 dark:border-gray-800">
                        <div>
                            <h2 class="font-display text-2xl sm:text-3xl font-extrabold text-gray-900 dark:text-white tracking-tight">
                                Step-by-Step Care Guide
                            </h2>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                Follow this clinical workflow for a smooth visit at {{ $unit['name'] }}.
                            </p>
                        </div>
                        <span class="shrink-0 px-3 py-1 rounded-full text-xs font-bold bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800">
                            {{ count($steps) }} Steps Total
                        </span>
                    </div>

                    {{-- Connected Timeline Process --}}
                    <div class="relative space-y-6">
                        @foreach($steps as $index => $step)
                            @php $hasImage = !empty($step['image']); @endphp
                            
                            <div class="relative flex items-start gap-4 sm:gap-6 group" data-reveal style="transition-delay: {{ $index * 60 }}ms">
                                
                                {{-- Step Number & Glow Indicator --}}
                                <div class="shrink-0 flex flex-col items-center">
                                    <div class="w-11 h-11 sm:w-12 sm:h-12 rounded-2xl flex items-center justify-center font-display font-extrabold text-sm sm:text-base text-white shadow-md transition-transform duration-300 group-hover:scale-105"
                                         style="background: linear-gradient(135deg, {{ $m['accent_hex'] }}, #0F3D3E);">
                                        0{{ $index + 1 }}
                                    </div>
                                    @if(!$loop->last)
                                        <div class="w-0.5 h-full min-h-[48px] my-2 bg-gradient-to-b from-emerald-500/50 to-transparent"></div>
                                    @endif
                                </div>

                                {{-- Step Card --}}
                                <div class="flex-1 rounded-2xl p-5 sm:p-6 bg-gray-50/70 dark:bg-gray-800/50 border border-gray-200/60 dark:border-gray-700/60 transition-all duration-200 group-hover:border-emerald-500/40 group-hover:bg-white dark:group-hover:bg-gray-800 group-hover:shadow-md">
                                    <div class="flex flex-wrap items-center justify-between gap-2 mb-2">
                                        <div class="flex items-center gap-2">
                                            <span class="text-emerald-600 dark:text-emerald-400">
                                                {!! $stepIcons[$index % count($stepIcons)] !!}
                                            </span>
                                            <h3 class="font-bold text-gray-900 dark:text-white text-base sm:text-lg">
                                                {{ $step['title'] ?? '' }}
                                            </h3>
                                        </div>
                                        @if(!empty($step['time']))
                                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-100/70 dark:bg-emerald-900/40 text-emerald-800 dark:text-emerald-300">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                {{ $step['time'] }}
                                            </span>
                                        @endif
                                    </div>

                                    <p class="text-sm text-gray-600 dark:text-gray-300 leading-relaxed mb-3">
                                        {{ $step['description'] ?? '' }}
                                    </p>

                                    @if(!empty($step['tip']))
                                        <div class="flex items-start gap-2.5 p-3 rounded-xl bg-emerald-500/10 dark:bg-emerald-500/15 border border-emerald-500/20 text-xs text-emerald-900 dark:text-emerald-200 font-medium">
                                            <svg class="w-4 h-4 shrink-0 text-emerald-600 dark:text-emerald-400 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a.75.75 0 000 1.5h.253a.25.25 0 01.244.304l-.459 2.066A1.75 1.75 0 0010.747 15H11a.75.75 0 000-1.5h-.253a.25.25 0 01-.244-.304l.459-2.066A1.75 1.75 0 009.253 9H9z" clip-rule="evenodd"/></svg>
                                            <span><strong>Helpful Tip:</strong> {{ $step['tip'] }}</span>
                                        </div>
                                    @endif

                                    @if($hasImage)
                                        <div class="mt-4 rounded-xl overflow-hidden border border-gray-200 dark:border-gray-700 shadow-sm">
                                            <img src="{{ asset('uploads/' . $step['image']) }}" alt="{{ $step['title'] ?? 'Process Step' }}" class="w-full h-44 object-cover" loading="lazy">
                                        </div>
                                    @endif
                                </div>

                            </div>
                        @endforeach
                    </div>

                </div>
            </div>

            {{-- Right Column: Facility Snapshot & Directory (4 cols) --}}
            <div class="lg:col-span-4 space-y-6">
                
                {{-- Facility Operating Snapshot Card --}}
                <div class="rounded-3xl p-6 sm:p-7 bg-white/75 dark:bg-gray-900/75 border border-white/90 dark:border-gray-800/80 backdrop-blur-xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] dark:shadow-[0_8px_30px_rgb(0,0,0,0.25)]" data-reveal>
                    <h3 class="font-display text-xl font-bold text-gray-900 dark:text-white mb-5 flex items-center gap-2">
                        <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                        Facility Details
                    </h3>

                    <div class="space-y-4 text-sm">
                        <div class="flex items-start gap-3 p-3 rounded-xl bg-gray-50/80 dark:bg-gray-800/60 border border-gray-200/50 dark:border-gray-700/50">
                            <svg class="w-5 h-5 text-emerald-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <div>
                                <p class="font-bold text-gray-900 dark:text-white text-xs uppercase tracking-wide">Hours of Service</p>
                                <p class="text-gray-600 dark:text-gray-300 text-xs mt-0.5">{{ \App\Models\SiteSetting::get('clinic_hours', 'Mon - Fri | 8:00 AM - 5:00 PM') }}</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-3 p-3 rounded-xl bg-gray-50/80 dark:bg-gray-800/60 border border-gray-200/50 dark:border-gray-700/50">
                            <svg class="w-5 h-5 text-emerald-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            <div>
                                <p class="font-bold text-gray-900 dark:text-white text-xs uppercase tracking-wide">Location</p>
                                <p class="text-gray-600 dark:text-gray-300 text-xs mt-0.5">{{ \App\Models\SiteSetting::get('footer_address_line1', 'M.H del Pilar St.') }}, {{ \App\Models\SiteSetting::get('footer_address_line2', 'Silang, Cavite') }}</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-3 p-3 rounded-xl bg-gray-50/80 dark:bg-gray-800/60 border border-gray-200/50 dark:border-gray-700/50">
                            <svg class="w-5 h-5 text-emerald-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                            <div>
                                <p class="font-bold text-gray-900 dark:text-white text-xs uppercase tracking-wide">Direct Helpline</p>
                                <a href="tel:{{ \App\Models\SiteSetting::get('footer_phone', '(046) 414-0209') }}" class="text-emerald-700 dark:text-emerald-400 font-semibold text-xs mt-0.5 block hover:underline">
                                    {{ \App\Models\SiteSetting::get('footer_phone', '(046) 414-0209') }}
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Requirements / What to Bring Card --}}
                <div class="rounded-3xl p-6 sm:p-7 bg-white/75 dark:bg-gray-900/75 border border-white/90 dark:border-gray-800/80 backdrop-blur-xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] dark:shadow-[0_8px_30px_rgb(0,0,0,0.25)]" data-reveal>
                    <h3 class="font-display text-lg font-bold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        What to Bring
                    </h3>

                    <ul class="space-y-2.5">
                        @foreach($m['requirements'] as $req)
                            <li class="flex items-start gap-2.5 text-xs text-gray-700 dark:text-gray-300 font-medium">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 shrink-0 mt-1.5"></span>
                                <span>{{ $req }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>

                {{-- Emergency Hotline Banner --}}
                <div class="rounded-3xl p-6 bg-gradient-to-br from-emerald-600 to-teal-800 text-white shadow-lg relative overflow-hidden" data-reveal>
                    <div class="relative z-10">
                        <span class="text-[10px] uppercase tracking-wider font-bold text-emerald-200">24/7 Emergency Response</span>
                        <h4 class="font-display text-xl font-bold mt-1 mb-2">Emergency Services</h4>
                        <p class="text-xs text-emerald-100 mb-4 leading-relaxed">
                            For urgent obstetric deliveries, severe animal bite cases, and acute triage emergency assistance.
                        </p>
                        <a href="tel:911" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-white text-emerald-900 font-bold text-xs shadow-md hover:bg-emerald-50 transition">
                            <svg class="w-4 h-4 text-red-500" fill="currentColor" viewBox="0 0 20 20"><path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 4V3z"/></svg>
                            Call 911 Hotline
                        </a>
                    </div>
                </div>

            </div>

        </div>

    </div>
</div>
@endsection
