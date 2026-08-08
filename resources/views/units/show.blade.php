@extends('layouts.app')

@section('content')
<!-- Back button container -->
<div class="bg-transparent pt-8 pb-4">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <a href="{{ route('units.index') }}" class="inline-flex items-center text-sm font-medium text-green-700 dark:text-green-400 hover:text-green-800 dark:text-green-400 transition">
            <svg class="mr-2 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Back to Departments
        </a>
    </div>
</div>

<div class="bg-transparent pb-24 min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Unit Header -->
        <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-700 p-10 md:p-14 mb-16 relative overflow-hidden flex flex-col items-center text-center" data-reveal>
            <div class="absolute top-0 right-0 -mt-10 -mr-10 text-green-50 opacity-50">
                <svg class="w-64 h-64" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V7z" clip-rule="evenodd"></path></svg>
            </div>
            
            <div class="relative z-10 w-24 h-24 bg-green-100 dark:bg-green-900/40 text-green-600 rounded-full flex items-center justify-center shadow-inner mb-6">
                 <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
            </div>
            <h1 class="font-display text-4xl md:text-5xl font-extrabold text-gray-900 dark:text-white mb-4">{{ $unit['name'] }}</h1>
            <p class="text-xl text-gray-500 dark:text-gray-400 max-w-2xl font-medium">{{ $unit['desc'] }}</p>
        </div>

        <!-- Dynamic Step-by-Step Guide for this unit -->
        <section id="guide" class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-700 p-8 md:p-12 mb-12" data-reveal>
            <div class="text-center mb-14">
                <h2 class="font-display text-2xl md:text-3xl font-bold mb-2 text-gray-900 dark:text-white">Step-by-Step Process</h2>
                <p class="text-gray-500 dark:text-gray-400 text-sm max-w-2xl mx-auto">Navigate your visit with ease. Follow these stages to minimize waiting times and prioritize your care at the {{ $unit['name'] }}.</p>
                <div class="section-accent mx-auto mt-6"></div>
            </div>

            @php
                $steps = \App\Models\SiteSetting::getJson('steps_data_' . $unit['slug'], [
                    ['title' => 'Check-in & Enrollment', 'description' => 'Visit the Information Desk to check in. New patients are enrolled in the system, while existing records are retrieved instantly.', 'time' => '~5 mins', 'tip' => 'Bring a valid ID and any previous medical records.'],
                    ['title' => 'Vitals & Screening', 'description' => 'Staff will record your weight, BP, and temperature. Results are encoded directly into your record for the doctor\'s review.', 'time' => '~5 mins', 'tip' => 'Wear comfortable clothing for easier vitals check.'],
                    ['title' => 'Evaluation', 'description' => 'Once called, meet your Doctor or Nurse. They will assess your history, provide a diagnosis, and issue an e-prescription.', 'time' => '~10-15 mins', 'tip' => 'List your symptoms and current medications beforehand.'],
                    ['title' => 'Pharmacy / Exit', 'description' => 'Receive referrals for lab tests if needed. Finally, proceed to the RHU Pharmacy to claim your prescribed medication.', 'time' => '~5-10 mins', 'tip' => 'Check if your prescription is for free or requires purchase.']
                ]);

                // Icons for each step (check-in, vitals, doctor, pharmacy)
                $stepIcons = [
                    '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/></svg>',
                    '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z"/></svg>',
                    '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.75 3.104v5.714a2.25 2.25 0 01-.659 1.591L5 14.5M9.75 3.104c-.251.023-.501.05-.75.082m.75-.082a24.301 24.301 0 014.5 0m0 0v5.714c0 .597.237 1.17.659 1.591L19.8 15.3M14.25 3.104c.251.023.501.05.75.082M19.8 15.3l-1.57.393A9.065 9.065 0 0112 15a9.065 9.065 0 00-6.23.693L5 14.5m14.8.8l1.402 1.402c1.232 1.232.65 3.318-1.067 3.611A48.309 48.309 0 0112 21c-2.773 0-5.491-.235-8.135-.687-1.718-.293-2.3-2.379-1.067-3.61L5 14.5"/></svg>',
                    '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/></svg>',
                ];
            @endphp

            <!-- Single-column vertical timeline -->
            <div class="relative max-w-2xl mx-auto">
                <!-- Vertical line -->
                <div class="timeline-line"></div>

                <div class="space-y-0">
                    @foreach($steps as $index => $step)
                        @php $hasImage = !empty($step['image']); @endphp
                        <div class="relative flex items-start gap-5 md:gap-7 {{ !$loop->last ? 'pb-10 md:pb-12' : '' }}" data-reveal style="transition-delay: {{ $index * 100 }}ms">
                            
                            {{-- Timeline node with icon + number --}}
                            <div class="relative z-10 shrink-0 flex flex-col items-center">
                                <div class="timeline-node">
                                    <div class="text-green-600 dark:text-green-400">
                                        {!! $stepIcons[$index] ?? $stepIcons[0] !!}
                                    </div>
                                </div>
                                <span class="mt-1.5 text-[10px] font-bold text-green-700 dark:text-green-400 bg-green-50 dark:bg-green-900/30 px-2 py-0.5 rounded-full">Step {{ $index + 1 }}</span>
                            </div>
                            
                            {{-- Content card --}}
                            <div class="flex-1 bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-2xl p-5 md:p-6 shadow-sm card-hover group">
                                <div class="flex items-start justify-between gap-3 mb-2">
                                    <h4 class="font-bold text-gray-900 dark:text-white text-lg group-hover:text-green-700 dark:group-hover:text-green-400 transition-colors">{{ $step['title'] ?? '' }}</h4>
                                    @if(!empty($step['time']))
                                        <span class="shrink-0 inline-flex items-center gap-1 px-2.5 py-1 bg-green-50 dark:bg-green-900/30 text-green-700 dark:text-green-400 text-[11px] font-semibold rounded-lg">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                            {{ $step['time'] }}
                                        </span>
                                    @endif
                                </div>
                                <p class="text-sm text-gray-600 dark:text-gray-400 leading-relaxed mb-3">{{ $step['description'] ?? '' }}</p>
                                
                                @if(!empty($step['tip']))
                                    <div class="flex items-start gap-2 text-xs text-gray-500 dark:text-gray-400 bg-gray-50 dark:bg-gray-700/50 rounded-lg p-3">
                                        <svg class="w-4 h-4 shrink-0 text-green-500 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a.75.75 0 000 1.5h.253a.25.25 0 01.244.304l-.459 2.066A1.75 1.75 0 0010.747 15H11a.75.75 0 000-1.5h-.253a.25.25 0 01-.244-.304l.459-2.066A1.75 1.75 0 009.253 9H9z" clip-rule="evenodd"/></svg>
                                        <span><strong class="text-gray-700 dark:text-gray-300">Tip:</strong> {{ $step['tip'] }}</span>
                                    </div>
                                @endif

                                {{-- Image if present --}}
                                @if($hasImage)
                                    <div class="mt-4 rounded-xl overflow-hidden border border-gray-100 dark:border-gray-700 shadow-sm">
                                        <img src="{{ asset('uploads/' . $step['image']) }}" alt="{{ $step['title'] ?? 'Step image' }}" class="w-full h-40 md:h-48 object-cover" loading="lazy">
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        <!-- Closing Section — Facility info + CTA -->
        <section class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden" data-reveal>
            <div class="grid grid-cols-1 md:grid-cols-2">
                {{-- Left: info --}}
                <div class="p-8 md:p-12 flex flex-col justify-center">
                    <h3 class="font-display text-2xl font-bold text-gray-900 dark:text-white mb-6">Visit {{ $unit['name'] }}</h3>
                    
                    <div class="space-y-4 mb-8">
                        {{-- Hours --}}
                        <div class="flex items-start gap-3">
                            <div class="shrink-0 w-10 h-10 rounded-lg bg-green-50 dark:bg-green-900/30 flex items-center justify-center">
                                <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-900 dark:text-white text-sm">Clinic Hours</p>
                                <p class="text-gray-500 dark:text-gray-400 text-sm">{{ \App\Models\SiteSetting::get('clinic_hours', 'Mon - Fri | 8:00 AM - 5:00 PM') }}</p>
                            </div>
                        </div>

                        {{-- Location --}}
                        <div class="flex items-start gap-3">
                            <div class="shrink-0 w-10 h-10 rounded-lg bg-green-50 dark:bg-green-900/30 flex items-center justify-center">
                                <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-900 dark:text-white text-sm">Location</p>
                                <p class="text-gray-500 dark:text-gray-400 text-sm">{{ \App\Models\SiteSetting::get('footer_address_line1', 'M.H del Pilar St.') }}, {{ \App\Models\SiteSetting::get('footer_address_line2', 'Silang, Cavite') }}</p>
                            </div>
                        </div>

                        {{-- Contact --}}
                        <div class="flex items-start gap-3">
                            <div class="shrink-0 w-10 h-10 rounded-lg bg-green-50 dark:bg-green-900/30 flex items-center justify-center">
                                <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-900 dark:text-white text-sm">Contact</p>
                                <p class="text-gray-500 dark:text-gray-400 text-sm">{{ \App\Models\SiteSetting::get('emergency_hotlines', '911 | (046) 432-1234') }}</p>
                            </div>
                        </div>
                    </div>

                    {{-- CTA --}}
                    <a href="{{ route('appointment.create') }}" class="btn-cta btn-cta-primary w-full sm:w-auto" id="facility-book-cta">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
                        </svg>
                        Book Appointment Here
                    </a>
                </div>

                {{-- Right: decorative/map area --}}
                <div class="bg-gradient-to-br from-green-50 to-teal-50 dark:from-green-900/20 dark:to-teal-900/10 flex items-center justify-center p-8 md:p-12 min-h-[280px]">
                    <div class="text-center">
                        <div class="w-20 h-20 mx-auto mb-4 rounded-2xl bg-white dark:bg-gray-800 shadow-sm flex items-center justify-center">
                            <svg class="w-10 h-10 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                        </div>
                        <p class="font-display text-xl font-bold text-gray-900 dark:text-white mb-1">{{ $unit['name'] }}</p>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Rural Health Unit · Silang, Cavite</p>
                    </div>
                </div>
            </div>
        </section>

    </div>
</div>
@endsection
