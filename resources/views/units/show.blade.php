@extends('layouts.app')

@section('content')
@php
    $unitMeta = [
        'main-health-center' => [
            'icon' => '<svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>',
            'badge' => 'Primary Healthcare & Diagnostics',
            'status' => 'Mon - Fri | 8:00 AM - 5:00 PM',
            'coverage' => 'PhilHealth & Free Consultation',
            'gradient' => 'from-emerald-500 to-teal-600',
            'glowColor' => 'bg-emerald-500/20',
            'requirements' => ['Valid Government ID or Barangay ID', 'PhilHealth ID / MDR (if available)', 'Previous Medical / Lab Records'],
        ],
        'lying-in-clinic' => [
            'icon' => '<svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z"/></svg>',
            'badge' => '24/7 Maternal & Birthing Center',
            'status' => '24/7 Continuous Care',
            'coverage' => 'PhilHealth Maternity Care Package (MCP)',
            'gradient' => 'from-rose-500 to-pink-600',
            'glowColor' => 'bg-rose-500/20',
            'requirements' => ['Prenatal / Mother-Child Record Book (Pink Book)', 'Valid Government ID of Mother & Spouse', 'PhilHealth Member Data Record (MDR)', 'Birth Registration Details'],
        ],
        'ob-gyn-unit' => [
            'icon' => '<svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>',
            'badge' => 'Obstetrics & Women\'s Health',
            'status' => 'Mon - Fri | 8:00 AM - 4:00 PM',
            'coverage' => 'Specialized Maternal Care',
            'gradient' => 'from-fuchsia-500 to-purple-600',
            'glowColor' => 'bg-fuchsia-500/20',
            'requirements' => ['Valid ID', 'Previous Ultrasound / Lab Results', 'Maternity Record (if pregnant)'],
        ],
        'dental-clinic' => [
            'icon' => '<svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.182 15.182a4.5 4.5 0 01-6.364 0M21 12a9 9 0 11-18 0 9 9 0 0118 0zM9.75 9.75c0 .414-.168.75-.375.75S9 10.164 9 9.75 9.168 9 9.375 9s.375.336.375.75zm-.375 0h.008v.015h-.008V9.75zm5.625 0c0 .414-.168.75-.375.75s-.375-.336-.375-.75.168-.75.375-.75.375.336.375.75zm-.375 0h.008v.015h-.008V9.75z"/></svg>',
            'badge' => 'Community Oral Healthcare',
            'status' => 'Mon - Fri | 8:00 AM - 5:00 PM',
            'coverage' => 'Free Extraction & Dental Consult',
            'gradient' => 'from-cyan-500 to-blue-600',
            'glowColor' => 'bg-cyan-500/20',
            'requirements' => ['Valid ID', 'Medical History (BP & allergies)', 'Guardian Consent for Minors'],
        ],
        'tb-dots-facility' => [
            'icon' => '<svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 3.104v5.714a2.25 2.25 0 01-.659 1.591L5 14.5M9.75 3.104c-.251.023-.501.05-.75.082m.75-.082a24.301 24.301 0 014.5 0m0 0v5.714c0 .597.237 1.17.659 1.591L19.8 15.3M14.25 3.104c.251.023.501.05.75.082M19.8 15.3l-1.57.393A9.065 9.065 0 0112 15a9.065 9.065 0 00-6.23.693L5 14.5m14.8.8l1.402 1.402c1.232 1.232.65 3.318-1.067 3.611A48.309 48.309 0 0112 21c-2.773 0-5.491-.235-8.135-.687-1.718-.293-2.3-2.379-1.067-3.61L5 14.5"/></svg>',
            'badge' => 'National Tuberculosis Elimination Center',
            'status' => 'Mon - Fri | 8:00 AM - 5:00 PM',
            'coverage' => '100% Free Medication & Screening',
            'gradient' => 'from-amber-500 to-orange-600',
            'glowColor' => 'bg-amber-500/20',
            'requirements' => ['Valid ID', 'Prior Chest X-ray (if available)', 'Sputum Sample Container (provided on-site)'],
        ],
        'animal-bite-center' => [
            'icon' => '<svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3.75m0-10.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285zm0 13.036h.008v.008H12v-.008z"/></svg>',
            'badge' => 'Rabies Treatment & Post-Exposure Center',
            'status' => 'Urgent Care & Vaccination',
            'coverage' => 'Free Public Health Rabies Vaccines',
            'gradient' => 'from-emerald-600 to-teal-700',
            'glowColor' => 'bg-teal-500/20',
            'requirements' => ['Patient Valid ID', 'Details of Biting Animal (dog/cat status)', 'Vaccination History Card'],
        ],
    ];

    $uSlug = is_array($unit) ? $unit['slug'] : $unit->slug;
    $uName = is_array($unit) ? $unit['name'] : $unit->name;
    $uDesc = is_array($unit) ? ($unit['desc'] ?? $unit['description'] ?? '') : ($unit->description ?? '');
    $uCategory = is_array($unit) ? ($unit['category'] ?? null) : $unit->category;
    $uHours = is_array($unit) ? ($unit['operating_hours'] ?? null) : $unit->operating_hours;
    $uContact = is_array($unit) ? ($unit['contact_number'] ?? null) : $unit->contact_number;
    $uLocation = is_array($unit) ? ($unit['location'] ?? null) : $unit->location;
    $uServices = is_array($unit) ? ($unit['services_offered'] ?? []) : ($unit->services_offered ?? []);
    $uImage = is_array($unit) ? ($unit['image_url'] ?? asset('assets/images/facilities/' . $uSlug . '.jpg')) : $unit->image_url;

    $m = $unitMeta[$uSlug] ?? [
        'icon' => '<svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>',
        'badge' => $uCategory ?: 'Silang RHU Facility',
        'status' => $uHours ?: 'Mon - Fri | 8:00 AM - 5:00 PM',
        'coverage' => 'Public Healthcare Service',
        'gradient' => 'from-emerald-500 to-teal-600',
        'glowColor' => 'bg-emerald-500/20',
        'requirements' => ['Valid ID', 'Medical Documents'],
    ];

    if ($uCategory) {
        $m['badge'] = $uCategory;
    }
    if ($uHours) {
        $m['status'] = $uHours;
    }

    $steps = \App\Models\SiteSetting::getJson('steps_data_' . $uSlug, [
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
        
        {{-- Breadcrumb Navigation --}}
        <div class="mb-6 flex items-center justify-between">
            <a href="{{ route('units.index') }}" 
               class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-lg bg-white dark:bg-slate-800 border border-slate-200/90 dark:border-slate-700 shadow-2xs text-xs font-bold text-slate-700 dark:text-slate-300 hover:text-emerald-700 dark:hover:text-emerald-400 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Back to All Facilities
            </a>

            <div class="hidden sm:flex items-center gap-2 text-xs font-semibold text-slate-500 dark:text-slate-400">
                <span>Rural Health Unit</span>
                <span class="text-slate-300 dark:text-slate-600">/</span>
                <span>Facilities</span>
                <span class="text-slate-300 dark:text-slate-600">/</span>
                <span class="text-slate-900 dark:text-white font-bold">{{ $uName }}</span>
            </div>
        </div>

        {{-- Facility Showcase Header Card with Atmospheric Background Photography --}}
        <div class="relative rounded-3xl overflow-hidden mb-10 bg-slate-950 border border-slate-800 shadow-2xl">
            <!-- Background Image & Atmospheric Modern Overlays -->
            <div class="absolute inset-0 z-0">
                <img src="{{ $uImage }}" alt="{{ $uName }}" class="w-full h-full object-cover object-center filter blur-[1px] brightness-75 scale-105 transition-transform duration-1000">
                <!-- Multi-layer Rich Modern Gradient -->
                <div class="absolute inset-0 bg-gradient-to-r from-slate-950/98 via-slate-950/92 to-slate-900/65"></div>
                <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-transparent to-slate-950/40"></div>
                <!-- Subtle Accent Color Glow -->
                <div class="absolute -top-32 -left-32 w-96 h-96 {{ $m['glowColor'] ?? 'bg-emerald-500/20' }} rounded-full blur-3xl pointer-events-none"></div>
                <div class="absolute -bottom-32 right-1/4 w-96 h-96 {{ $m['glowColor'] ?? 'bg-teal-500/15' }} rounded-full blur-3xl pointer-events-none"></div>
            </div>

            <!-- Content Grid over Background -->
            <div class="relative z-10 p-6 sm:p-10 lg:p-12">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-10 items-center">
                    
                    {{-- Left: Details & Municipal Authority Header (7 cols) --}}
                    <div class="lg:col-span-7 text-white space-y-4">
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-white/10 backdrop-blur-md text-emerald-300 text-[11px] font-bold uppercase tracking-widest rounded-lg border border-white/15 shadow-sm">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                                Municipality of Silang
                            </span>
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-white/10 backdrop-blur-md text-slate-200 border border-white/10">
                                {{ $m['badge'] }}
                            </span>
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">
                                {{ $m['coverage'] }}
                            </span>
                        </div>

                        <div class="flex items-start sm:items-center gap-4">
                            <div class="w-14 h-14 sm:w-16 sm:h-16 rounded-2xl bg-gradient-to-br {{ $m['gradient'] ?? 'from-emerald-500 to-teal-600' }} text-white flex items-center justify-center shrink-0 shadow-lg shadow-emerald-950/40 border border-white/20">
                                {!! $m['icon'] !!}
                            </div>
                            <div>
                                <h1 class="font-display text-2xl sm:text-3xl lg:text-4xl font-black text-white tracking-tight leading-tight">
                                    {{ $uName }}
                                </h1>
                                <p class="text-xs sm:text-sm font-semibold text-slate-300 mt-1 flex items-center gap-1.5">
                                    <svg class="w-4 h-4 text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                    {{ $uLocation ?: 'Main RHU Complex, Silang, Cavite' }}
                                </p>
                            </div>
                        </div>

                        <p class="text-sm sm:text-base text-slate-300 font-normal leading-relaxed max-w-2xl">
                            {{ $uDesc }}
                        </p>

                        <!-- Services Highlights Pills -->
                        @if(count($uServices) > 0)
                            <div class="flex flex-wrap gap-2 pt-2 border-t border-white/10">
                                @foreach(array_slice($uServices, 0, 4) as $serv)
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg bg-white/5 backdrop-blur-sm text-slate-200 text-xs font-medium border border-white/10">
                                        <svg class="w-3.5 h-3.5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                        {{ $serv }}
                                    </span>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    {{-- Right: High-Res Facility Visual Showcase Card & Quick Actions (5 cols) --}}
                    <div class="lg:col-span-5">
                        <div class="rounded-2xl p-4 bg-slate-900/85 backdrop-blur-xl border border-white/15 shadow-2xl space-y-4">
                            <!-- Crisp High-Res Visual Photo -->
                            <div class="relative w-full h-48 sm:h-56 rounded-xl overflow-hidden border border-white/10 group shadow-inner">
                                <img src="{{ $uImage }}" alt="{{ $uName }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700">
                                <div class="absolute inset-0 bg-gradient-to-t from-black/75 via-transparent to-transparent"></div>
                                <div class="absolute bottom-3 left-3 right-3 flex items-center justify-between text-white">
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-[10px] font-bold bg-emerald-500/90 backdrop-blur-sm text-slate-950 uppercase tracking-wide">
                                        <span class="w-1.5 h-1.5 rounded-full bg-slate-950"></span>
                                        Clinical Unit
                                    </span>
                                    <span class="text-xs font-semibold text-slate-200 flex items-center gap-1">
                                        <svg class="w-3.5 h-3.5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        {{ $m['status'] }}
                                    </span>
                                </div>
                            </div>

                            <!-- Operating Stats & Action Buttons -->
                            <div class="space-y-2.5 pt-1">
                                @if(!is_array($unit) ? $unit->is_main_center : ($uSlug === 'main-health-center'))
                                    <a href="{{ route('appointment.create') }}" 
                                       class="w-full inline-flex items-center justify-center gap-2 px-5 py-3 rounded-xl text-xs sm:text-sm font-extrabold text-slate-950 bg-gradient-to-r from-emerald-400 to-teal-400 hover:from-emerald-300 hover:to-teal-300 shadow-lg shadow-emerald-500/25 transition-all hover:scale-[1.01] active:scale-95 cursor-pointer">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                                        Book Online Appointment
                                    </a>

                                    <a href="{{ route('appointment.manage') }}" 
                                       class="w-full inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl text-xs font-semibold text-white bg-white/10 hover:bg-white/15 border border-white/10 transition-colors">
                                        Manage Existing Booking
                                    </a>
                                @else
                                    <div class="p-3 rounded-xl bg-slate-950/80 border border-emerald-500/30 text-xs">
                                        <div class="flex items-center gap-1.5 font-bold text-emerald-400 mb-1">
                                            <svg class="w-4 h-4 text-emerald-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                            Walk-in & Specialized Clinical Unit
                                        </div>
                                        <p class="text-[11px] leading-relaxed text-slate-300">
                                            Online appointments are processed exclusively for the Main Health Center. This facility serves walk-ins, emergency admissions, and direct physician referrals.
                                        </p>
                                    </div>

                                    @if(!empty($uContact))
                                        @php
                                            $telDigits = preg_replace('/[^\d+]/', '', $uContact);
                                        @endphp
                                        <a href="tel:{{ $telDigits }}" 
                                           class="w-full inline-flex items-center justify-center gap-2 px-5 py-3 rounded-xl text-xs sm:text-sm font-extrabold text-white bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 shadow-md shadow-emerald-950/40 transition-all hover:scale-[1.01] active:scale-95 cursor-pointer">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                            Call Facility Line: {{ $uContact }}
                                        </a>
                                    @endif

                                    <a href="{{ route('appointment.create') }}" 
                                       class="w-full inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl text-xs font-semibold text-slate-200 bg-white/10 hover:bg-white/15 border border-white/10 transition-colors">
                                        <svg class="w-3.5 h-3.5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                        Book Appointment at Main Health Center
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        {{-- 2-Column Bento: Workflow Guide (Left) + Directory / Requirements (Right) --}}
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            
            {{-- Left Column: Clinical Workflow Timeline (8 cols) --}}
            <div class="lg:col-span-8">
                <div class="rounded-3xl p-6 sm:p-8 lg:p-10 bg-white dark:bg-slate-800/95 border border-slate-200/90 dark:border-slate-700/80 shadow-xs">
                    
                    <div class="flex items-center justify-between gap-4 mb-8 pb-5 border-b border-slate-100 dark:border-slate-700/70">
                        <div>
                            <h2 class="font-display text-xl sm:text-2xl font-extrabold text-slate-900 dark:text-white tracking-tight">
                                Step-by-Step Clinical Care Guide
                            </h2>
                            <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-1">
                                Follow this municipal workflow for a seamless visit at {{ $unit['name'] }}.
                            </p>
                        </div>
                        <span class="shrink-0 px-3 py-1 rounded-md text-xs font-bold bg-emerald-100/80 dark:bg-emerald-950/60 text-emerald-800 dark:text-emerald-300 border border-emerald-300/50 dark:border-emerald-700/50">
                            {{ count($steps) }} Steps Total
                        </span>
                    </div>

                    {{-- Process Timeline --}}
                    <div class="relative space-y-6">
                        @foreach($steps as $index => $step)
                            @php $hasImage = !empty($step['image']); @endphp
                            
                            <div class="relative flex items-start gap-4 sm:gap-5 group">
                                
                                {{-- Step Number Indicator --}}
                                <div class="shrink-0 flex flex-col items-center">
                                    <div class="w-10 h-10 rounded-xl bg-emerald-700 text-white flex items-center justify-center font-display font-extrabold text-sm shadow-xs">
                                        0{{ $index + 1 }}
                                    </div>
                                    @if(!$loop->last)
                                        <div class="w-0.5 h-full min-h-[48px] my-2 bg-slate-200 dark:bg-slate-700"></div>
                                    @endif
                                </div>

                                {{-- Step Card --}}
                                <div class="flex-1 rounded-2xl p-5 bg-slate-50/80 dark:bg-slate-900/60 border border-slate-200/80 dark:border-slate-800/80">
                                    <div class="flex flex-wrap items-center justify-between gap-2 mb-2">
                                        <div class="flex items-center gap-2">
                                            <span class="text-emerald-700 dark:text-emerald-400">
                                                {!! $stepIcons[$index % count($stepIcons)] !!}
                                            </span>
                                            <h3 class="font-bold text-slate-900 dark:text-white text-base">
                                                {{ $step['title'] ?? '' }}
                                            </h3>
                                        </div>
                                        @if(!empty($step['time']))
                                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-md text-xs font-semibold bg-slate-200/70 dark:bg-slate-700/60 text-slate-700 dark:text-slate-300">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                {{ $step['time'] }}
                                            </span>
                                        @endif
                                    </div>

                                    <p class="text-xs sm:text-sm text-slate-600 dark:text-slate-300 leading-relaxed mb-3 font-normal">
                                        {{ $step['description'] ?? '' }}
                                    </p>

                                    @if(!empty($step['tip']))
                                        <div class="flex items-start gap-2 p-2.5 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200/80 dark:border-emerald-800/60 text-xs text-emerald-900 dark:text-emerald-300 font-medium">
                                            <svg class="w-4 h-4 shrink-0 text-emerald-700 dark:text-emerald-400 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a.75.75 0 000 1.5h.253a.25.25 0 01.244.304l-.459 2.066A1.75 1.75 0 0010.747 15H11a.75.75 0 000-1.5h-.253a.25.25 0 01-.244-.304l.459-2.066A1.75 1.75 0 009.253 9H9z" clip-rule="evenodd"/></svg>
                                            <span><strong>Note:</strong> {{ $step['tip'] }}</span>
                                        </div>
                                    @endif

                                    @if($hasImage)
                                        <div class="mt-3 rounded-xl overflow-hidden border border-slate-200 dark:border-slate-700">
                                            <img src="{{ asset('uploads/' . $step['image']) }}" alt="{{ $step['title'] ?? 'Process Step' }}" class="w-full h-44 object-cover" loading="lazy">
                                        </div>
                                    @endif
                                </div>

                            </div>
                        @endforeach
                    </div>

                </div>
            </div>

            {{-- Right Column: Directory & Requirements Sidebar (4 cols) --}}
            <div class="lg:col-span-4 space-y-6">
                
                {{-- Operating Details Card --}}
                <div class="rounded-3xl p-6 bg-white dark:bg-slate-800/95 border border-slate-200/90 dark:border-slate-700/80 shadow-xs">
                    <h3 class="font-display text-lg font-bold text-slate-900 dark:text-white mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-emerald-700 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                        Facility Details
                    </h3>

                    <div class="space-y-3.5 text-xs">
                        <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-900/60 border border-slate-200/70 dark:border-slate-800/70">
                            <p class="font-bold text-slate-900 dark:text-white uppercase tracking-wider text-[11px]">Hours of Service</p>
                            <p class="text-slate-600 dark:text-slate-300 mt-1 font-medium">{{ $uHours }}</p>
                        </div>

                        <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-900/60 border border-slate-200/70 dark:border-slate-800/70">
                            <p class="font-bold text-slate-900 dark:text-white uppercase tracking-wider text-[11px]">Location</p>
                            <p class="text-slate-600 dark:text-slate-300 mt-1 font-medium">{{ $uLocation ?: 'Main RHU Complex, Silang, Cavite' }}</p>
                        </div>

                        <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-900/60 border border-slate-200/70 dark:border-slate-800/70">
                            <p class="font-bold text-slate-900 dark:text-white uppercase tracking-wider text-[11px]">Direct Contact Lines</p>
                            @php
                                $contactsList = !is_array($unit) ? $unit->contact_list : [];
                            @endphp
                            @if(count($contactsList) > 0)
                                <div class="mt-1.5 space-y-1.5">
                                    @foreach($contactsList as $c)
                                        <div class="flex items-center justify-between gap-2">
                                            <span class="text-slate-500 dark:text-slate-400 font-medium text-[11px]">{{ $c['label'] }}:</span>
                                            <a href="tel:{{ $c['tel'] }}" class="text-emerald-700 dark:text-emerald-400 font-bold hover:underline flex items-center gap-1">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                                {{ $c['number'] }}
                                            </a>
                                        </div>
                                    @endforeach
                                </div>
                            @elseif(!empty($uContact))
                                @php
                                    $digits = preg_replace('/[^\d+]/', '', $uContact);
                                @endphp
                                <a href="tel:{{ $digits }}" class="text-emerald-700 dark:text-emerald-400 font-bold mt-1 block hover:underline flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                    {{ $uContact }}
                                </a>
                            @else
                                <p class="text-slate-500 mt-1 italic">Contact Main Health Center</p>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Requirements Checklist Card --}}
                <div class="rounded-3xl p-6 bg-white dark:bg-slate-800/95 border border-slate-200/90 dark:border-slate-700/80 shadow-xs">
                    <h3 class="font-display text-base font-bold text-slate-900 dark:text-white mb-3 flex items-center gap-2">
                        <svg class="w-4 h-4 text-emerald-700 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        What to Bring
                    </h3>

                    <ul class="space-y-2">
                        @foreach($m['requirements'] as $req)
                            <li class="flex items-start gap-2 text-xs text-slate-700 dark:text-slate-300 font-medium">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-600 shrink-0 mt-1.5"></span>
                                <span>{{ $req }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>

                {{-- 24/7 Emergency Hotline Banner --}}
                <div class="rounded-3xl p-6 bg-gradient-to-br from-emerald-900 to-slate-900 text-white shadow-sm border border-emerald-800/50">
                    <span class="text-[10px] uppercase tracking-widest font-bold text-emerald-300">24/7 Emergency Response</span>
                    <h4 class="font-display text-lg font-bold mt-1 mb-1.5">Emergency Assistance</h4>
                    <p class="text-xs text-slate-300 mb-4 leading-relaxed font-normal">
                        For acute trauma, urgent obstetric labor, or critical animal bite emergency response.
                    </p>
                    <a href="tel:{{ preg_replace('/[^0-9+]/', '', explode('|', \App\Models\SiteSetting::get('emergency_hotlines', '911'))[0]) }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-white text-slate-900 font-bold text-xs shadow-xs hover:bg-slate-100 transition">
                        <svg class="w-4 h-4 text-red-600" fill="currentColor" viewBox="0 0 20 20"><path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 4V3z"/></svg>
                        Hotlines: {{ \App\Models\SiteSetting::get('emergency_hotlines', '911 | (046) 432-1234') }}
                    </a>
                </div>

            </div>

        </div>

    </div>
</div>
@endsection
