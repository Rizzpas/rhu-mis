@extends('layouts.app')

@section('content')
<div class="min-h-screen pb-24 bg-transparent">

    {{-- ============================================================
         PAGE HEADER (21st.dev Style Showcase)
    ============================================================ --}}
    <section class="pt-12 sm:pt-16 pb-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 text-center" data-reveal>
            <span class="inline-flex items-center gap-2 px-3.5 py-1.5 bg-emerald-500/10 dark:bg-emerald-500/20 text-emerald-800 dark:text-emerald-300 text-xs font-bold rounded-full mb-5 uppercase tracking-wider border border-emerald-500/20 backdrop-blur-md shadow-sm">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                About Silang Rural Health Unit
            </span>
            <h1 class="font-display text-4xl sm:text-5xl lg:text-6xl font-extrabold text-gray-900 dark:text-white tracking-tight leading-tight max-w-4xl mx-auto">
                Advancing Community Health with <span class="hero-highlight">Integrity & Care</span>
            </h1>
            <p class="mt-5 text-lg sm:text-xl text-gray-600 dark:text-gray-300 max-w-3xl mx-auto leading-relaxed font-normal">
                The primary public healthcare authority of the Municipality of Silang, Cavite — dedicated to providing responsive, equitable, and world-class medical services to every citizen.
            </p>
            <div class="section-accent mx-auto mt-6"></div>
        </div>
    </section>

    {{-- ============================================================
         IMPACT HIGHLIGHTS (4 Bento Metric Badges)
    ============================================================ --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 mb-8">
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6" data-reveal>
            
            <div class="rounded-2xl p-5 bg-white/70 dark:bg-gray-900/70 border border-white/80 dark:border-gray-800/80 backdrop-blur-xl shadow-xs card-hover flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-emerald-100 dark:bg-emerald-950/50 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                </div>
                <div>
                    <div class="font-display text-2xl font-extrabold text-gray-900 dark:text-white">5 Units</div>
                    <div class="text-xs text-gray-500 dark:text-gray-400 font-medium">Specialized Facilities</div>
                </div>
            </div>

            <div class="rounded-2xl p-5 bg-white/70 dark:bg-gray-900/70 border border-white/80 dark:border-gray-800/80 backdrop-blur-xl shadow-xs card-hover flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-pink-100 dark:bg-pink-950/50 text-pink-600 dark:text-pink-400 flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z"/></svg>
                </div>
                <div>
                    <div class="font-display text-2xl font-extrabold text-gray-900 dark:text-white">24/7 Care</div>
                    <div class="text-xs text-gray-500 dark:text-gray-400 font-medium">Maternal Birthing Clinic</div>
                </div>
            </div>

            <div class="rounded-2xl p-5 bg-white/70 dark:bg-gray-900/70 border border-white/80 dark:border-gray-800/80 backdrop-blur-xl shadow-xs card-hover flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-sky-100 dark:bg-sky-950/50 text-sky-600 dark:text-sky-400 flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                </div>
                <div>
                    <div class="font-display text-2xl font-extrabold text-gray-900 dark:text-white">100k+</div>
                    <div class="text-xs text-gray-500 dark:text-gray-400 font-medium">Silang Citizens Served</div>
                </div>
            </div>

            <div class="rounded-2xl p-5 bg-white/70 dark:bg-gray-900/70 border border-white/80 dark:border-gray-800/80 backdrop-blur-xl shadow-xs card-hover flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-amber-100 dark:bg-amber-950/50 text-amber-600 dark:text-amber-400 flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                </div>
                <div>
                    <div class="font-display text-2xl font-extrabold text-gray-900 dark:text-white">100% Free</div>
                    <div class="text-xs text-gray-500 dark:text-gray-400 font-medium">Primary Public Services</div>
                </div>
            </div>

        </div>
    </div>

    {{-- ============================================================
         MISSION, VISION & CORE VALUES (21st.dev Bento Grid)
    ============================================================ --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 mb-12">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8" data-reveal>

            {{-- Mission Card (6 cols) --}}
            <div class="lg:col-span-6 relative rounded-3xl p-8 sm:p-10 bg-white/75 dark:bg-gray-900/75 border border-white/90 dark:border-gray-800/80 backdrop-blur-xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] dark:shadow-[0_8px_30px_rgb(0,0,0,0.25)] card-hover overflow-hidden flex flex-col justify-between">
                
                {{-- Ambient glow --}}
                <div class="absolute -top-12 -right-12 w-48 h-48 rounded-full blur-3xl pointer-events-none opacity-25 bg-emerald-500"></div>

                <div>
                    <div class="flex items-center justify-between gap-4 mb-6">
                        <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-emerald-500 to-teal-700 text-white flex items-center justify-center shadow-md">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        </div>
                        <span class="px-3 py-1 rounded-full text-xs font-bold bg-emerald-500/10 text-emerald-800 dark:text-emerald-300 border border-emerald-500/20">
                            Our Purpose
                        </span>
                    </div>

                    <h2 class="font-display text-2xl sm:text-3xl font-extrabold text-gray-900 dark:text-white tracking-tight mb-4">
                        Our Mission
                    </h2>

                    <p class="text-gray-600 dark:text-gray-300 text-base leading-relaxed">
                        {{ \App\Models\SiteSetting::get('mission_statement', 'To provide responsive, equitable, and quality primary healthcare services to all citizens of Silang. We commit to transparency and excellence by utilizing modern management systems to eliminate barriers to health access and pharmaceutical needs.') }}
                    </p>
                </div>

                <div class="mt-8 pt-5 border-t border-gray-100 dark:border-gray-800 flex items-center gap-2 text-xs font-bold text-emerald-700 dark:text-emerald-400">
                    <span>Equitable Access for Every Citizen</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </div>
            </div>

            {{-- Vision Card (6 cols) --}}
            <div class="lg:col-span-6 relative rounded-3xl p-8 sm:p-10 bg-white/75 dark:bg-gray-900/75 border border-white/90 dark:border-gray-800/80 backdrop-blur-xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] dark:shadow-[0_8px_30px_rgb(0,0,0,0.25)] card-hover overflow-hidden flex flex-col justify-between">
                
                {{-- Ambient glow --}}
                <div class="absolute -top-12 -right-12 w-48 h-48 rounded-full blur-3xl pointer-events-none opacity-25 bg-cyan-500"></div>

                <div>
                    <div class="flex items-center justify-between gap-4 mb-6">
                        <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-cyan-600 to-teal-800 text-white flex items-center justify-center shadow-md">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        </div>
                        <span class="px-3 py-1 rounded-full text-xs font-bold bg-cyan-500/10 text-cyan-800 dark:text-cyan-300 border border-cyan-500/20">
                            Our Aspiration
                        </span>
                    </div>

                    <h2 class="font-display text-2xl sm:text-3xl font-extrabold text-gray-900 dark:text-white tracking-tight mb-4">
                        Our Vision
                    </h2>

                    <p class="text-gray-600 dark:text-gray-300 text-base leading-relaxed">
                        {{ \App\Models\SiteSetting::get('vision_statement', 'A healthy, resilient, and empowered community served by a world-class Rural Health Unit that champions technological advancement and medical integrity for the well-being of every family, ensuring that quality healthcare is a reliable right for every citizen.') }}
                    </p>
                </div>

                <div class="mt-8 pt-5 border-t border-gray-100 dark:border-gray-800 flex items-center gap-2 text-xs font-bold text-cyan-700 dark:text-cyan-400">
                    <span>Modern Healthcare for a Progressive Silang</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </div>
            </div>

            {{-- Core Mandate & Values Pill Bar (12 cols) --}}
            <div class="lg:col-span-12 rounded-3xl p-6 sm:p-8 bg-white/60 dark:bg-gray-900/60 border border-white/80 dark:border-gray-800/80 backdrop-blur-xl shadow-xs">
                <div class="flex flex-col md:flex-row items-center justify-between gap-6">
                    <div>
                        <h3 class="font-display text-lg font-bold text-gray-900 dark:text-white">Our Core Principles</h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">The values that guide every health consultation, patient record, and clinical procedure.</p>
                    </div>
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl text-xs font-semibold bg-emerald-50 dark:bg-emerald-950/40 text-emerald-800 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Compassionate Care
                        </span>
                        <span class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl text-xs font-semibold bg-blue-50 dark:bg-blue-950/40 text-blue-800 dark:text-blue-300 border border-blue-200 dark:border-blue-800">
                            <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span> Digital Innovation
                        </span>
                        <span class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl text-xs font-semibold bg-purple-50 dark:bg-purple-950/40 text-purple-800 dark:text-purple-300 border border-purple-200 dark:border-purple-800">
                            <span class="w-1.5 h-1.5 rounded-full bg-purple-500"></span> Transparency & Ethics
                        </span>
                        <span class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl text-xs font-semibold bg-amber-50 dark:bg-amber-950/40 text-amber-800 dark:text-amber-300 border border-amber-200 dark:border-amber-800">
                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span> Universal Inclusivity
                        </span>
                    </div>
                </div>
            </div>

        </div>
    </div>

    {{-- Section Divider --}}
    <div class="max-w-4xl mx-auto px-8 mb-12">
        <div class="section-divider"><span class="section-divider-dot"></span></div>
    </div>

    {{-- ============================================================
         ORGANIZATIONAL CHART
    ============================================================ --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        @include('partials.orgchart')
    </div>

</div>
@endsection
