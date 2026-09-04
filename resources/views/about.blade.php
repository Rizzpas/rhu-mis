@extends('layouts.app')

@section('content')
<div class="min-h-screen pb-24 bg-transparent">

    {{-- ============================================================
         INSTITUTIONAL HEADER — Municipal Public Health Authority
    ============================================================ --}}
    <section class="pt-12 sm:pt-16 pb-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            {{-- Single Institutional Section Kicker --}}
            <div class="mb-4">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-emerald-100/80 dark:bg-emerald-950/60 text-emerald-900 dark:text-emerald-300 text-[11px] font-bold uppercase tracking-widest rounded-md border border-emerald-300/50 dark:border-emerald-700/50">
                    <svg class="w-3.5 h-3.5 text-emerald-700 dark:text-emerald-400 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd"/>
                    </svg>
                    Municipality of Silang
                </span>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 lg:gap-12 items-end">
                {{-- Left Column: Headline --}}
                <div class="lg:col-span-7">
                    <h1 class="font-display text-3xl sm:text-4xl lg:text-[3.25rem] font-extrabold text-slate-900 dark:text-white tracking-tight leading-[1.12]">
                        Advancing Municipal Health with <span class="text-emerald-700 dark:text-emerald-400">Integrity & Care</span>
                    </h1>
                </div>

                {{-- Right Column: Institutional Mandate Description --}}
                <div class="lg:col-span-5 lg:pb-1">
                    <p class="text-base sm:text-lg text-slate-600 dark:text-slate-300 leading-relaxed font-normal">
                        The primary public healthcare authority of the Municipality of Silang, Cavite — dedicated to providing responsive, equitable, and professional medical services to every constituent across all 64 barangays.
                    </p>
                </div>
            </div>
        </div>
    </section>

    {{-- ============================================================
         INSTITUTIONAL METRIC STRIP — Differentiated Treatments
    ============================================================ --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-16">
        <div class="bg-white dark:bg-slate-800/95 rounded-2xl border border-slate-200/90 dark:border-slate-700/80 shadow-xs overflow-hidden">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 divide-y sm:divide-y-0 sm:divide-x divide-slate-100 dark:divide-slate-700/70">
                
                {{-- Metric 1: Clinical Facilities --}}
                <div class="p-6 sm:p-7 flex items-start gap-4 group">
                    <div class="w-12 h-12 rounded-xl bg-emerald-700 text-white flex items-center justify-center shrink-0 shadow-xs group-hover:scale-105 transition-transform">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    </div>
                    <div>
                        <div class="font-display text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-white tracking-tight">5 Units</div>
                        <div class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mt-1">Specialized Facilities</div>
                        <p class="text-[11px] text-slate-400 dark:text-slate-500 mt-0.5">Primary care, maternal, dental & labs</p>
                    </div>
                </div>

                {{-- Metric 2: 24/7 Maternal Care --}}
                <div class="p-6 sm:p-7 flex items-start gap-4 group">
                    <div class="w-12 h-12 rounded-xl bg-emerald-50 dark:bg-emerald-950/70 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800/60 flex items-center justify-center shrink-0 group-hover:scale-105 transition-transform">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <div>
                        <div class="font-display text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-white tracking-tight">24/7 Care</div>
                        <div class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mt-1">Maternal Birthing</div>
                        <p class="text-[11px] text-slate-400 dark:text-slate-500 mt-0.5">Continuous newborn & maternal service</p>
                    </div>
                </div>

                {{-- Metric 3: Population Served --}}
                <div class="p-6 sm:p-7 flex items-start gap-4 group">
                    <div class="w-12 h-12 rounded-xl bg-slate-900 dark:bg-slate-700 text-white flex items-center justify-center shrink-0 shadow-xs group-hover:scale-105 transition-transform">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    </div>
                    <div>
                        <div class="font-display text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-white tracking-tight">100k+</div>
                        <div class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mt-1">Citizens Served</div>
                        <p class="text-[11px] text-slate-400 dark:text-slate-500 mt-0.5">Across all Silang barangays</p>
                    </div>
                </div>

                {{-- Metric 4: Free Consultation --}}
                <div class="p-6 sm:p-7 flex items-start gap-4 group">
                    <div class="w-12 h-12 rounded-xl bg-emerald-100/80 dark:bg-emerald-900/40 text-emerald-800 dark:text-emerald-200 border border-emerald-300/60 dark:border-emerald-700/60 flex items-center justify-center shrink-0 group-hover:scale-105 transition-transform">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    </div>
                    <div>
                        <div class="font-display text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-white tracking-tight">100% Free</div>
                        <div class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mt-1">Primary Triage</div>
                        <p class="text-[11px] text-slate-400 dark:text-slate-500 mt-0.5">No user fee for basic consultation</p>
                    </div>
                </div>

            </div>
        </div>
    </div>

    {{-- ============================================================
         MISSION SECTION — Documentary Photo & Structured Narrative
    ============================================================ --}}
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-16">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-center">
            
            {{-- Left Column: Framed Documentary Photography --}}
            <div class="lg:col-span-6">
                <div class="bg-slate-100 dark:bg-slate-900 p-2.5 sm:p-3.5 rounded-2xl border border-slate-200/80 dark:border-slate-700/80 shadow-md">
                    <div class="relative rounded-xl overflow-hidden aspect-[16/11]">
                        <img src="{{ asset('assets/images/rhu-consultation.jpg') }}" 
                             alt="Silang RHU Healthcare Consultation" 
                             class="w-full h-full object-cover">
                    </div>
                    {{-- Physical Caption Line --}}
                    <div class="pt-3 px-2 pb-1 flex items-center justify-between text-[11px] text-slate-500 dark:text-slate-400 border-t border-slate-200/60 dark:border-slate-800 mt-2.5">
                        <span class="font-medium">RHU Silang Medical Consultation Wing</span>
                    </div>
                </div>
            </div>

            {{-- Right Column: Structured Mission Content --}}
            <div class="lg:col-span-6 flex flex-col justify-center">
                <div class="inline-flex items-center gap-2 mb-2">
                    <span class="w-6 h-0.5 bg-emerald-600 dark:bg-emerald-400"></span>
                    <span class="text-xs font-bold text-emerald-800 dark:text-emerald-400 uppercase tracking-widest">Our Mission</span>
                </div>
                <h2 class="font-display text-2xl sm:text-3xl lg:text-4xl font-extrabold text-slate-900 dark:text-white tracking-tight leading-tight mb-4">
                    Delivering Responsive, Equitable Public Healthcare
                </h2>
                <p class="text-slate-600 dark:text-slate-300 text-sm sm:text-base leading-relaxed mb-6 font-normal">
                    {{ \App\Models\SiteSetting::get('mission_statement', 'To provide responsive, equitable, and quality primary healthcare services to all citizens of Silang. We commit to transparency and excellence by utilizing modern management systems to eliminate barriers to health access and pharmaceutical needs.') }}
                </p>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5 pt-4 border-t border-slate-100 dark:border-slate-700/70">
                    <div class="flex items-start gap-3">
                        <div class="w-6 h-6 rounded-lg bg-emerald-100 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-400 flex items-center justify-center shrink-0 mt-0.5">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        </div>
                        <span class="text-xs text-slate-700 dark:text-slate-300 font-medium">Equitable access across 64 Silang barangays</span>
                    </div>
                    <div class="flex items-start gap-3">
                        <div class="w-6 h-6 rounded-lg bg-emerald-100 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-400 flex items-center justify-center shrink-0 mt-0.5">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        </div>
                        <span class="text-xs text-slate-700 dark:text-slate-300 font-medium">Digital queue & clinical record tracking</span>
                    </div>
                    <div class="flex items-start gap-3">
                        <div class="w-6 h-6 rounded-lg bg-emerald-100 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-400 flex items-center justify-center shrink-0 mt-0.5">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        </div>
                        <span class="text-xs text-slate-700 dark:text-slate-300 font-medium">Continuous inventory & pharmacy transparency</span>
                    </div>
                    <div class="flex items-start gap-3">
                        <div class="w-6 h-6 rounded-lg bg-emerald-100 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-400 flex items-center justify-center shrink-0 mt-0.5">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        </div>
                        <span class="text-xs text-slate-700 dark:text-slate-300 font-medium">24/7 dedicated maternal emergency care</span>
                    </div>
                </div>
            </div>

        </div>
    </section>

    {{-- ============================================================
         VISION SECTION — Distinct Civic Manifesto / Aspiration Block
    ============================================================ --}}
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-16">
        <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-emerald-900 via-emerald-950 to-slate-900 text-white p-8 sm:p-12 lg:p-16 border border-emerald-800/50 shadow-xl">
            {{-- Background Municipal Watermark --}}
            <div class="absolute -right-12 -bottom-12 w-80 h-80 opacity-5 pointer-events-none">
                <img src="{{ asset('assets/images/logo.png') }}" alt="" class="w-full h-full object-contain filter invert">
            </div>

            <div class="relative z-10 max-w-3xl">
                <div class="flex items-center gap-2 mb-3">
                    <span class="w-8 h-0.5 bg-emerald-400"></span>
                    <span class="text-xs font-bold text-emerald-300 uppercase tracking-widest">Our Vision</span>
                </div>
                
                <h2 class="font-display text-2xl sm:text-3xl lg:text-4xl font-extrabold tracking-tight leading-tight text-white mb-6">
                    "A healthy, resilient, and empowered Silang served by modern healthcare integrity."
                </h2>

                <p class="text-emerald-100/90 text-sm sm:text-base leading-relaxed mb-8 font-normal">
                    {{ \App\Models\SiteSetting::get('vision_statement', 'A healthy, resilient, and empowered community served by a world-class Rural Health Unit that champions technological advancement and medical integrity for the well-being of every family, ensuring that quality healthcare is a reliable right for every citizen.') }}
                </p>

                {{-- 3 Pillar Badges --}}
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 pt-6 border-t border-white/10">
                    <div class="bg-white/10 backdrop-blur-xs rounded-xl p-4 border border-white/10 hover:bg-white/15 transition-colors">
                        <div class="w-7 h-7 rounded-lg bg-emerald-400/20 flex items-center justify-center text-emerald-300 mb-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                        </div>
                        <p class="text-xs font-bold text-white">Universal Care</p>
                        <p class="text-[11px] text-emerald-200/80 mt-0.5">A reliable, dignified right for every family in Silang</p>
                    </div>
                    <div class="bg-white/10 backdrop-blur-xs rounded-xl p-4 border border-white/10 hover:bg-white/15 transition-colors">
                        <div class="w-7 h-7 rounded-lg bg-emerald-400/20 flex items-center justify-center text-emerald-300 mb-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        </div>
                        <p class="text-xs font-bold text-white">Technological Leap</p>
                        <p class="text-[11px] text-emerald-200/80 mt-0.5">Integrated health information MIS and online queues</p>
                    </div>
                    <div class="bg-white/10 backdrop-blur-xs rounded-xl p-4 border border-white/10 hover:bg-white/15 transition-colors">
                        <div class="w-7 h-7 rounded-lg bg-emerald-400/20 flex items-center justify-center text-emerald-300 mb-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        </div>
                        <p class="text-xs font-bold text-white">Medical Integrity</p>
                        <p class="text-[11px] text-emerald-200/80 mt-0.5">Ethical diagnostics, certified medicine dispensing</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ============================================================
         CORE VALUES & GUIDING MANDATE — Numbered Principles
    ============================================================ --}}
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-16">
        <div class="mb-8">
            <span class="text-xs font-bold text-emerald-800 dark:text-emerald-400 uppercase tracking-widest">Public Service Charter</span>
            <h3 class="font-display text-xl sm:text-2xl lg:text-3xl font-bold text-slate-900 dark:text-white mt-1">Guiding Principles</h3>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1 max-w-2xl">The standards and ethics that govern every patient consultation, medical diagnosis, and administrative procedure.</p>
        </div>

        @php
            $guidingPrinciples = \App\Models\SiteSetting::getJson('guiding_principles', [
                ['number' => '01', 'title' => 'Compassionate Care', 'description' => 'Treating every patient with dignity, empathy, and dedicated professional attention.'],
                ['number' => '02', 'title' => 'Digital Innovation', 'description' => 'Streamlining triage, clinical schedules, and patient records with modern MIS solutions.'],
                ['number' => '03', 'title' => 'Transparency & Ethics', 'description' => 'Upholding absolute accountability in pharmacy inventories and healthcare governance.'],
                ['number' => '04', 'title' => 'Universal Inclusivity', 'description' => 'Guaranteeing barrier-free medical access for all constituents regardless of status.']
            ]);
        @endphp

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            @foreach($guidingPrinciples as $index => $principle)
                <div class="bg-white dark:bg-slate-800/90 rounded-2xl p-6 border border-slate-200/80 dark:border-slate-700/70 shadow-2xs hover:border-emerald-300 dark:hover:border-emerald-700 transition-colors">
                    <span class="font-display text-emerald-700 dark:text-emerald-400 font-extrabold text-xl block mb-2">{{ $principle['number'] ?? str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}</span>
                    <h4 class="font-bold text-slate-900 dark:text-white text-sm mb-1.5">{{ $principle['title'] ?? '' }}</h4>
                    <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">{{ $principle['description'] ?? '' }}</p>
                </div>
            @endforeach
        </div>
    </section>

    {{-- ============================================================
         PHYSICAL ADDRESS & INTERACTIVE GOOGLE MAP SECTION
    ============================================================ --}}
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-16">
        <div class="bg-white dark:bg-slate-800/95 rounded-3xl border border-slate-200/90 dark:border-slate-700/80 p-6 sm:p-10 lg:p-12 shadow-sm">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-center">
                
                {{-- Left Column: Physical Location & Contact Details --}}
                <div class="lg:col-span-5 flex flex-col justify-between h-full">
                    <div>
                        <div class="inline-flex items-center gap-2 mb-2">
                            <span class="w-6 h-0.5 bg-emerald-600 dark:bg-emerald-400"></span>
                            <span class="text-xs font-bold text-emerald-800 dark:text-emerald-400 uppercase tracking-widest">Our Location</span>
                        </div>
                        <h3 class="font-display text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-white tracking-tight leading-tight mb-3">
                            Visit Rural Health Unit
                        </h3>
                        <p class="text-slate-600 dark:text-slate-300 text-sm leading-relaxed mb-6">
                            Centrally situated in the municipal proper of Silang, easily accessible via major provincial transit routes and local public utility vehicles.
                        </p>

                        {{-- Details List --}}
                        <div class="space-y-4 mb-6">
                            {{-- Address Item --}}
                            <div class="flex items-start gap-3.5">
                                <div class="w-9 h-9 rounded-xl bg-emerald-100 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-400 flex items-center justify-center shrink-0 shadow-2xs">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                </div>
                                <div>
                                    <span class="block text-xs font-bold text-slate-900 dark:text-white uppercase tracking-wider">Physical Address</span>
                                    <p class="text-sm font-semibold text-slate-700 dark:text-slate-200 mt-0.5">
                                        {{ \App\Models\SiteSetting::get('footer_address_line1', 'M.H del Pilar St.') }}, 
                                        {{ \App\Models\SiteSetting::get('footer_address_line2', 'Silang, Cavite') }}
                                    </p>
                                    <div class="flex items-center gap-2 mt-1 flex-wrap">
                                        <span class="inline-flex items-center gap-1 text-[11px] font-mono text-emerald-700 dark:text-emerald-300 bg-emerald-50 dark:bg-emerald-950/70 px-2 py-0.5 rounded-md border border-emerald-200/80 dark:border-emerald-800/60 font-semibold">
                                            📍 6XRH+3F6, Silang, Cavite
                                        </span>
                                        <span class="text-xs text-slate-500 dark:text-slate-400">14.2398° N, 120.9784° E</span>
                                    </div>
                                </div>
                            </div>

                            {{-- Operating Hours --}}
                            <div class="flex items-start gap-3.5">
                                <div class="w-9 h-9 rounded-xl bg-emerald-100 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-400 flex items-center justify-center shrink-0 shadow-2xs">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                </div>
                                <div>
                                    <span class="block text-xs font-bold text-slate-900 dark:text-white uppercase tracking-wider">Operating Schedule</span>
                                    <p class="text-sm font-semibold text-slate-700 dark:text-slate-200 mt-0.5">
                                        {{ \App\Models\SiteSetting::get('clinic_hours', 'Mon - Fri | 8:00 AM - 5:00 PM') }}
                                    </p>
                                    <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded text-[10px] font-bold bg-teal-100 dark:bg-teal-950/70 text-teal-800 dark:text-teal-300 mt-1">
                                        <span class="w-1.5 h-1.5 rounded-full bg-teal-500 animate-pulse"></span>
                                        24/7 Maternity Birthing Unit Open
                                    </span>
                                </div>
                            </div>

                            {{-- Hotlines & Phone --}}
                            <div class="flex items-start gap-3.5">
                                <div class="w-9 h-9 rounded-xl bg-emerald-100 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-400 flex items-center justify-center shrink-0 shadow-2xs">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                </div>
                                <div>
                                    <span class="block text-xs font-bold text-slate-900 dark:text-white uppercase tracking-wider">Hotlines & Telephone</span>
                                    <div class="flex flex-wrap gap-x-3 gap-y-1 mt-0.5">
                                        <a href="tel:{{ \App\Models\SiteSetting::get('footer_phone', '(046) 414-0209') }}" class="text-sm font-semibold text-emerald-700 dark:text-emerald-400 hover:underline">
                                            {{ \App\Models\SiteSetting::get('footer_phone', '(046) 414-0209') }}
                                        </a>
                                        <span class="text-slate-300 dark:text-slate-600">•</span>
                                        <a href="mailto:{{ \App\Models\SiteSetting::get('footer_email', 'contact@silang.gov.ph') }}" class="text-sm text-slate-600 dark:text-slate-300 hover:underline">
                                            {{ \App\Models\SiteSetting::get('footer_email', 'contact@silang.gov.ph') }}
                                        </a>
                                    </div>
                                    <p class="text-[11px] text-rose-600 dark:text-rose-400 font-bold mt-1">
                                        Emergency: {{ \App\Models\SiteSetting::get('emergency_hotlines', '911 | (046) 432-1234') }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Get Directions Link Button --}}
                    <div>
                        <a href="https://www.google.com/maps/place/Silang+Rural+Health+Unit/@14.2400104,120.9775881,265m/data=!3m1!1e3!4m9!1m2!29m1!1b1!3m5!1s0x33bd7f00011c2f8d:0xc519fa69cd93063b!8m2!3d14.2398345!4d120.9783859!16s%2Fg%2F11w4ttmt_0?hl=en-US" 
                           target="_blank" 
                           rel="noopener noreferrer"
                           class="inline-flex items-center justify-center gap-2 w-full sm:w-auto px-5 py-2.5 rounded-xl font-bold text-sm text-white bg-emerald-700 hover:bg-emerald-800 active:bg-emerald-900 transition-all shadow-xs group">
                            <svg class="w-4 h-4 text-emerald-200 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                            <span>Open in Google Maps / Get Directions</span>
                        </a>
                    </div>
                </div>

                {{-- Right Column: Interactive Google Map Embed --}}
                <div class="lg:col-span-7">
                    <div class="relative rounded-2xl overflow-hidden border border-slate-200/90 dark:border-slate-700/80 shadow-md bg-slate-100 dark:bg-slate-900 aspect-[4/3] sm:aspect-[16/10] lg:aspect-auto lg:h-[420px]">
                        {{-- Interactive Google Map Iframe --}}
                        <iframe 
                            src="https://maps.google.com/maps?q=14.2398345,120.9783859+(Silang+Rural+Health+Unit)&t=&z=18&ie=UTF8&iwloc=B&output=embed"
                            class="w-full h-full border-0" 
                            allowfullscreen="" 
                            loading="lazy" 
                            referrerpolicy="no-referrer-when-downgrade"
                            title="Google Map showing Silang Rural Health Unit Location">
                        </iframe>

                        {{-- Floating Location Pin Pill --}}
                        <div class="absolute bottom-3 left-3 right-3 sm:right-auto bg-white/95 dark:bg-slate-900/95 backdrop-blur-md px-3.5 py-2 rounded-xl border border-slate-200/80 dark:border-slate-700/80 shadow-lg flex items-center gap-2.5 pointer-events-none">
                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 shrink-0 animate-ping"></span>
                            <div class="text-xs font-bold text-slate-800 dark:text-slate-100 truncate">
                                <span>Silang Rural Health Unit</span>
                                <span class="text-slate-400 font-normal ml-1">(6XRH+3F6)</span>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

    {{-- ============================================================
         ORGANIZATIONAL STRUCTURE
    ============================================================ --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-16">
        @include('partials.orgchart')
    </div>

    {{-- ============================================================
         ACTION-ORIENTED CALL TO ACTION (CTA) SECTION
    ============================================================ --}}
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-16">
        <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-slate-50 via-white to-emerald-50/40 dark:from-slate-900 dark:via-slate-900 dark:to-slate-950 text-slate-900 dark:text-white p-8 sm:p-12 lg:p-14 border border-slate-200/90 dark:border-slate-800 shadow-sm dark:shadow-xl">
            {{-- Ambient aura glow --}}
            <div class="absolute top-0 right-1/4 w-96 h-96 bg-emerald-500/10 dark:bg-emerald-500/15 rounded-full blur-3xl pointer-events-none"></div>

            <div class="relative z-10 max-w-3xl mb-10">
                <span class="inline-flex items-center gap-2 px-3 py-1 bg-emerald-100/80 dark:bg-emerald-500/20 text-emerald-800 dark:text-emerald-300 text-xs font-bold uppercase tracking-wider rounded-full border border-emerald-200/80 dark:border-emerald-500/30 mb-3">
                    Next Steps
                </span>
                <h3 class="font-display text-2xl sm:text-3xl lg:text-4xl font-extrabold tracking-tight text-slate-900 dark:text-white mb-3">
                    Connect With Municipal Healthcare Today
                </h3>
                <p class="text-slate-600 dark:text-slate-300 text-sm sm:text-base leading-relaxed">
                    Whether you need to book an online consultation, review latest municipal advisories, or find specialized unit services, we're here to assist you.
                </p>
            </div>

            {{-- 3 Action Pathway Cards --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-5 relative z-10">
                
                {{-- Pathway 1: Book Appointment --}}
                <div class="bg-white/95 dark:bg-slate-800/80 hover:bg-white dark:hover:bg-slate-800 border border-slate-200/90 dark:border-slate-700/70 rounded-2xl p-6 flex flex-col justify-between transition-all duration-200 shadow-xs hover:shadow-md hover:border-emerald-400/80 dark:hover:border-emerald-600/80 group">
                    <div>
                        <div class="w-12 h-12 rounded-xl bg-emerald-100/80 dark:bg-emerald-500/20 text-emerald-700 dark:text-emerald-300 flex items-center justify-center mb-4 group-hover:scale-105 transition-transform shadow-2xs">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        </div>
                        <h4 class="text-lg font-bold text-slate-900 dark:text-white mb-1.5">Book Appointment</h4>
                        <p class="text-xs text-slate-600 dark:text-slate-300 leading-relaxed mb-6">
                            Schedule a primary care doctor consultation or follow-up checkup online to avoid queues.
                        </p>
                    </div>
                    <a href="{{ route('appointment.create') }}" 
                       class="inline-flex items-center justify-center gap-2 w-full py-2.5 px-4 rounded-xl font-bold text-xs sm:text-sm text-white bg-emerald-700 hover:bg-emerald-800 active:bg-emerald-900 transition-all shadow-xs">
                        <span>Book Online Now</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                    </a>
                </div>

                {{-- Pathway 2: Public Bulletins & Announcements --}}
                <div class="bg-white/95 dark:bg-slate-800/80 hover:bg-white dark:hover:bg-slate-800 border border-slate-200/90 dark:border-slate-700/70 rounded-2xl p-6 flex flex-col justify-between transition-all duration-200 shadow-xs hover:shadow-md hover:border-teal-400/80 dark:hover:border-teal-600/80 group">
                    <div>
                        <div class="w-12 h-12 rounded-xl bg-teal-100/80 dark:bg-teal-500/20 text-teal-700 dark:text-teal-300 flex items-center justify-center mb-4 group-hover:scale-105 transition-transform shadow-2xs">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10.34 15.84c-.063.046-.129.088-.198.127a3.75 3.75 0 01-4.088-.288L3.25 13.5A2.25 2.25 0 012.25 11.75v-1.5a2.25 2.25 0 011-1.75l2.804-2.179a3.75 3.75 0 014.088-.288c.069.04.135.081.198.127m0 9.68l4.41 4.41a1.5 1.5 0 002.122 0l1.414-1.414a1.5 1.5 0 000-2.122L12 14.004m-1.66 1.836V7.996m0 0a3.75 3.75 0 013.75-3.75h1.5a2.25 2.25 0 012.25 2.25v6a2.25 2.25 0 01-2.25 2.25h-1.5a3.75 3.75 0 01-3.75-3.75z"/></svg>
                        </div>
                        <h4 class="text-lg font-bold text-slate-900 dark:text-white mb-1.5">News & Advisories</h4>
                        <p class="text-xs text-slate-600 dark:text-slate-300 leading-relaxed mb-6">
                            Stay up-to-date with immunization drives, health alerts, and municipal medical programs.
                        </p>
                    </div>
                    <a href="{{ route('announcements.index') }}" 
                       class="inline-flex items-center justify-center gap-2 w-full py-2.5 px-4 rounded-xl font-bold text-xs sm:text-sm text-slate-700 dark:text-slate-200 bg-slate-100/90 dark:bg-slate-700/60 hover:bg-slate-200/90 dark:hover:bg-slate-700 border border-slate-200 dark:border-slate-600 transition-all">
                        <span>Read Announcements</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                    </a>
                </div>

                {{-- Pathway 3: Specialized Facilities --}}
                <div class="bg-white/95 dark:bg-slate-800/80 hover:bg-white dark:hover:bg-slate-800 border border-slate-200/90 dark:border-slate-700/70 rounded-2xl p-6 flex flex-col justify-between transition-all duration-200 shadow-xs hover:shadow-md hover:border-cyan-400/80 dark:hover:border-cyan-600/80 group">
                    <div>
                        <div class="w-12 h-12 rounded-xl bg-cyan-100/80 dark:bg-cyan-500/20 text-cyan-700 dark:text-cyan-300 flex items-center justify-center mb-4 group-hover:scale-105 transition-transform shadow-2xs">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                        </div>
                        <h4 class="text-lg font-bold text-slate-900 dark:text-white mb-1.5">Our Health Facilities</h4>
                        <p class="text-xs text-slate-600 dark:text-slate-300 leading-relaxed mb-6">
                            Explore specialized units including Lying-in, Dental, TB DOTS, and Animal Bite Centers.
                        </p>
                    </div>
                    <a href="{{ route('units.index') }}" 
                       class="inline-flex items-center justify-center gap-2 w-full py-2.5 px-4 rounded-xl font-bold text-xs sm:text-sm text-slate-700 dark:text-slate-200 bg-slate-100/90 dark:bg-slate-700/60 hover:bg-slate-200/90 dark:hover:bg-slate-700 border border-slate-200 dark:border-slate-600 transition-all">
                        <span>Explore Facilities</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                    </a>
                </div>

            </div>
        </div>
    </section>

</div>
@endsection
