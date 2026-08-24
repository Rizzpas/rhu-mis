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
                    Republic of the Philippines • Municipality of Silang
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
                        The primary public healthcare authority of the Municipality of Silang, Cavite — dedicated to providing responsive, equitable, and professional medical services to every constituent.
                    </p>
                </div>
            </div>
        </div>
    </section>

    {{-- ============================================================
         INSTITUTIONAL METRIC STRIP — Differentiated Icon Treatments
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
                        <p class="text-[11px] text-slate-400 dark:text-slate-500 mt-0.5">Primary care & diagnostics</p>
                    </div>
                </div>

                {{-- Metric 2: 24/7 Maternal Care --}}
                <div class="p-6 sm:p-7 flex items-start gap-4 group">
                    <div class="w-12 h-12 rounded-xl bg-emerald-50 dark:bg-emerald-950/70 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800/60 flex items-center justify-center shrink-0 group-hover:scale-105 transition-transform">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <div>
                        <div class="font-display text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-white tracking-tight">24/7 Care</div>
                        <div class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mt-1">Maternal Clinic</div>
                        <p class="text-[11px] text-slate-400 dark:text-slate-500 mt-0.5">Continuous birthing service</p>
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
         MISSION SECTION — Editorial Photo & Narrative Layout
    ============================================================ --}}
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-16">
        <div class="bg-white dark:bg-slate-800/95 rounded-3xl border border-slate-200/90 dark:border-slate-700/80 p-6 sm:p-10 lg:p-12 shadow-sm">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-center">
                
                {{-- Left Column: Framed Documentary Photography with Real Caption --}}
                <div class="lg:col-span-6">
                    <div class="bg-slate-100 dark:bg-slate-900 p-2 sm:p-3 rounded-2xl border border-slate-200/80 dark:border-slate-700/80 shadow-md">
                        <div class="relative rounded-xl overflow-hidden aspect-[16/11]">
                            <img src="{{ asset('assets/images/rhu-consultation.jpg') }}" 
                                 alt="Silang RHU Healthcare Consultation" 
                                 class="w-full h-full object-cover">
                        </div>
                        {{-- Physical Caption Line --}}
                        <div class="pt-2.5 px-2 pb-1 flex items-center justify-between text-[11px] text-slate-500 dark:text-slate-400 border-t border-slate-200/60 dark:border-slate-800 mt-2">
                            <span class="font-medium">RHU Silang Medical Consultation Wing, 2025</span>
                            <span class="font-semibold text-emerald-700 dark:text-emerald-400">Doc. Record #SLG-HC</span>
                        </div>
                    </div>
                </div>

                {{-- Right Column: Structured Mission Content --}}
                <div class="lg:col-span-6 flex flex-col justify-center">
                    <span class="text-xs font-bold text-emerald-800 dark:text-emerald-400 uppercase tracking-widest mb-2">Institutional Mission</span>
                    <h2 class="font-display text-2xl sm:text-3xl lg:text-4xl font-extrabold text-slate-900 dark:text-white tracking-tight leading-tight mb-4">
                        Delivering Responsive, Equitable Public Healthcare
                    </h2>
                    <p class="text-slate-600 dark:text-slate-300 text-sm sm:text-base leading-relaxed mb-6 font-normal">
                        {{ \App\Models\SiteSetting::get('mission_statement', 'To provide responsive, equitable, and quality primary healthcare services to all citizens of Silang. We commit to transparency and excellence by utilizing modern management systems to eliminate barriers to health access and pharmaceutical needs.') }}
                    </p>

                    <div class="grid grid-cols-2 gap-4 pt-4 border-t border-slate-100 dark:border-slate-700/70">
                        <div class="flex items-start gap-2.5">
                            <svg class="w-5 h-5 text-emerald-700 dark:text-emerald-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            <span class="text-xs text-slate-700 dark:text-slate-300 font-medium">Equitable access across 64 barangays</span>
                        </div>
                        <div class="flex items-start gap-2.5">
                            <svg class="w-5 h-5 text-emerald-700 dark:text-emerald-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            <span class="text-xs text-slate-700 dark:text-slate-300 font-medium">Digital queue & clinical record tracking</span>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

    {{-- ============================================================
         VISION SECTION — Distinct Civic Manifesto / Aspiration Block (Not Identical to Mission)
    ============================================================ --}}
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-16">
        <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-emerald-900 via-emerald-950 to-slate-900 text-white p-8 sm:p-12 lg:p-16 border border-emerald-800/50 shadow-lg">
            {{-- Background Municipal Watermark --}}
            <div class="absolute -right-12 -bottom-12 w-80 h-80 opacity-5 pointer-events-none">
                <img src="{{ asset('assets/images/logo.png') }}" alt="" class="w-full h-full object-contain filter invert">
            </div>

            <div class="relative z-10 max-w-3xl">
                <div class="flex items-center gap-2 mb-3">
                    <span class="w-8 h-0.5 bg-emerald-400"></span>
                    <span class="text-xs font-bold text-emerald-300 uppercase tracking-widest">Institutional Vision</span>
                </div>
                
                <h2 class="font-display text-2xl sm:text-3xl lg:text-4xl font-extrabold tracking-tight leading-tight text-white mb-6">
                    "A healthy, resilient, and empowered Silang served by modern healthcare integrity."
                </h2>

                <p class="text-emerald-100/90 text-sm sm:text-base leading-relaxed mb-8 font-normal">
                    {{ \App\Models\SiteSetting::get('vision_statement', 'A healthy, resilient, and empowered community served by a world-class Rural Health Unit that champions technological advancement and medical integrity for the well-being of every family, ensuring that quality healthcare is a reliable right for every citizen.') }}
                </p>

                {{-- 3 Pillar Badges --}}
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 pt-6 border-t border-white/10">
                    <div class="bg-white/10 backdrop-blur-xs rounded-xl p-3.5 border border-white/10">
                        <p class="text-xs font-bold text-white">Universal Care</p>
                        <p class="text-[11px] text-emerald-200 mt-0.5">Reliable right for every citizen</p>
                    </div>
                    <div class="bg-white/10 backdrop-blur-xs rounded-xl p-3.5 border border-white/10">
                        <p class="text-xs font-bold text-white">Technological Leap</p>
                        <p class="text-[11px] text-emerald-200 mt-0.5">Integrated health information MIS</p>
                    </div>
                    <div class="bg-white/10 backdrop-blur-xs rounded-xl p-3.5 border border-white/10">
                        <p class="text-xs font-bold text-white">Medical Integrity</p>
                        <p class="text-[11px] text-emerald-200 mt-0.5">Ethical diagnostics & dispensing</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ============================================================
         CORE VALUES & GUIDING MANDATE — Numbered Institutional Principles
    ============================================================ --}}
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-16">
        <div class="bg-slate-50/80 dark:bg-slate-900/60 rounded-2xl border border-slate-200/80 dark:border-slate-800/80 p-6 sm:p-8">
            <div class="mb-6">
                <span class="text-xs font-bold text-emerald-800 dark:text-emerald-400 uppercase tracking-widest">Public Service Charter</span>
                <h3 class="font-display text-xl sm:text-2xl font-bold text-slate-900 dark:text-white mt-1">Guiding Principles</h3>
                <p class="text-sm text-slate-500 dark:text-slate-400 mt-1 max-w-2xl">The standards and ethics that govern every patient consultation, medical diagnosis, and administrative procedure.</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="bg-white dark:bg-slate-800/90 rounded-xl p-5 border border-slate-200/80 dark:border-slate-700/70 shadow-2xs">
                    <span class="font-display text-emerald-700 dark:text-emerald-400 font-extrabold text-lg block mb-1">01</span>
                    <h4 class="font-bold text-slate-900 dark:text-white text-sm mb-1.5">Compassionate Care</h4>
                    <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">Treating every patient with dignity, empathy, and professional attention.</p>
                </div>

                <div class="bg-white dark:bg-slate-800/90 rounded-xl p-5 border border-slate-200/80 dark:border-slate-700/70 shadow-2xs">
                    <span class="font-display text-emerald-700 dark:text-emerald-400 font-extrabold text-lg block mb-1">02</span>
                    <h4 class="font-bold text-slate-900 dark:text-white text-sm mb-1.5">Digital Innovation</h4>
                    <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">Streamlining triage, clinical schedules, and patient records with secure systems.</p>
                </div>

                <div class="bg-white dark:bg-slate-800/90 rounded-xl p-5 border border-slate-200/80 dark:border-slate-700/70 shadow-2xs">
                    <span class="font-display text-emerald-700 dark:text-emerald-400 font-extrabold text-lg block mb-1">03</span>
                    <h4 class="font-bold text-slate-900 dark:text-white text-sm mb-1.5">Transparency & Ethics</h4>
                    <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">Upholding accountability in pharmacy inventories and public healthcare governance.</p>
                </div>

                <div class="bg-white dark:bg-slate-800/90 rounded-xl p-5 border border-slate-200/80 dark:border-slate-700/70 shadow-2xs">
                    <span class="font-display text-emerald-700 dark:text-emerald-400 font-extrabold text-lg block mb-1">04</span>
                    <h4 class="font-bold text-slate-900 dark:text-white text-sm mb-1.5">Universal Inclusivity</h4>
                    <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">Guaranteeing barrier-free medical access for all constituents across Silang barangays.</p>
                </div>
            </div>
        </div>
    </section>

    {{-- Section Divider --}}
    <div class="max-w-4xl mx-auto px-8 mb-14">
        <div class="section-divider"><span class="section-divider-dot"></span></div>
    </div>

    {{-- ============================================================
         ORGANIZATIONAL STRUCTURE
    ============================================================ --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        @include('partials.orgchart')
    </div>

</div>
@endsection
