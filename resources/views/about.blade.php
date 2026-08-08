@extends('layouts.app')

@section('content')
<div class="min-h-screen">

    {{-- ============================================================
         PAGE HEADER — Mesh gradient background (matches homepage hero)
    ============================================================ --}}
    <section class="hero-bg-pattern bg-[#FAF9F6] dark:bg-gray-900">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-16 pb-12 relative z-10">
            <div class="text-center" data-reveal>
                <span class="inline-flex items-center gap-2 px-3.5 py-1.5 bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300 text-xs font-bold rounded-full mb-5 uppercase tracking-wider">
                    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4 4a2 2 0 012-2h8a2 2 0 012 2v12a1 1 0 110 2h-3a1 1 0 01-1-1v-2a1 1 0 00-1-1H9a1 1 0 00-1 1v2a1 1 0 01-1 1H4a1 1 0 110-2V4zm3 1h2v2H7V5zm2 4H7v2h2V9zm2-4h2v2h-2V5zm2 4h-2v2h2V9z" clip-rule="evenodd"/></svg>
                    About Us
                </span>
                <h1 class="font-display text-4xl sm:text-5xl font-extrabold text-gray-900 dark:text-white tracking-tight" style="line-height: 1.08;">
                    About the Rural Health Unit
                </h1>
                <p class="mt-5 text-xl text-gray-500 dark:text-gray-400 max-w-3xl mx-auto leading-relaxed">
                    Dedicated to providing responsive, equitable, and quality primary healthcare services to the citizens of Silang, Cavite.
                </p>
                <div class="section-accent mx-auto mt-6"></div>
            </div>
        </div>
    </section>

    {{-- Section Divider --}}
    <div class="max-w-4xl mx-auto px-8">
        <div class="section-divider"><span class="section-divider-dot"></span></div>
    </div>

    {{-- ============================================================
         MISSION & VISION — Premium dual cards
    ============================================================ --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 sm:py-20">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8" data-reveal>

            {{-- Mission Card — Filled soft sage background --}}
            <div class="relative rounded-2xl overflow-hidden border border-green-100 dark:border-green-800/30 bg-gradient-to-br from-green-50/80 to-emerald-50/40 dark:from-green-900/20 dark:to-emerald-900/10 p-8 sm:p-10 group"
                 style="box-shadow: 0 8px 32px -8px rgba(22, 163, 74, 0.12), 0 2px 8px -2px rgba(0,0,0,0.04);">
                {{-- Subtle watermark icon --}}
                <div class="absolute -right-6 -top-6 text-green-200/30 dark:text-green-700/15 transform group-hover:scale-110 transition-transform duration-700">
                    <svg class="w-40 h-40" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="0.5" d="M3.75 13.5l10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75z"/></svg>
                </div>
                <div class="relative z-10">
                    <div class="flex items-center gap-3 mb-5">
                        <div class="w-11 h-11 rounded-xl bg-green-600 flex items-center justify-center shadow-sm">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.75 13.5l10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75z"/></svg>
                        </div>
                        <h2 class="font-display text-2xl font-bold text-green-800 dark:text-green-400" style="line-height: 1.1;">Our Mission</h2>
                    </div>
                    <p class="text-gray-700 dark:text-gray-300 leading-relaxed text-[15px]">
                        {{ \App\Models\SiteSetting::get('mission_statement', '"To provide responsive, equitable, and quality primary healthcare services to all citizens. We commit to transparency and excellence by utilizing modern management systems to eliminate barriers to health access and pharmaceutical needs."') }}
                    </p>
                </div>
            </div>

            {{-- Vision Card — Outlined/bordered style (differentiated) --}}
            <div class="relative rounded-2xl overflow-hidden border-2 border-teal-100 dark:border-teal-800/30 bg-white dark:bg-gray-800 p-8 sm:p-10 group"
                 style="box-shadow: 0 8px 32px -8px rgba(15, 61, 62, 0.1), 0 2px 8px -2px rgba(0,0,0,0.04);">
                {{-- Subtle watermark icon --}}
                <div class="absolute -right-6 -top-6 text-teal-100/40 dark:text-teal-800/15 transform group-hover:scale-110 transition-transform duration-700">
                    <svg class="w-40 h-40" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="0.5" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="0.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </div>
                <div class="relative z-10">
                    <div class="flex items-center gap-3 mb-5">
                        <div class="w-11 h-11 rounded-xl bg-gradient-to-br from-teal-600 to-cyan-700 flex items-center justify-center shadow-sm">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        </div>
                        <h2 class="font-display text-2xl font-bold text-gray-900 dark:text-white" style="line-height: 1.1;">Our Vision</h2>
                    </div>
                    <p class="text-gray-600 dark:text-gray-400 leading-relaxed text-[15px]">
                        {{ \App\Models\SiteSetting::get('vision_statement', '"A healthy and empowered community served by a world-class Rural Health Unit that champions technological advancement and medical integrity for the well-being of every family, ensuring that quality healthcare is a reliable and efficient right for every citizen."') }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    {{-- Section Divider --}}
    <div class="max-w-4xl mx-auto px-8">
        <div class="section-divider"><span class="section-divider-dot"></span></div>
    </div>

    {{-- ============================================================
         ORGANIZATIONAL CHART
    ============================================================ --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 sm:py-20">
        @include('partials.orgchart')
    </div>

</div>
@endsection
