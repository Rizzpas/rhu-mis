@extends('layouts.app')

@push('head')
    @php
        $heroImage = \App\Models\SiteSetting::get('hero_image', 'assets/images/rhu-facility.jpg');
        $heroImageUrl = asset($heroImage);
    @endphp
    <link rel="preload" as="image" href="{{ $heroImageUrl }}">
@endpush

@section('content')
    <div x-data="{ 
            lastAnnouncementModified: '{{ \App\Models\Announcement::where("status", "published")->latest("updated_at")->first()?->updated_at?->toDateTimeString() ?? "" }}',
            lastDoctorModified: '{{ \App\Models\User::where("role", "doctor")->latest("updated_at")->first()?->updated_at?->toDateTimeString() ?? "" }}',
            announcementCount: {{ \App\Models\Announcement::where("status", "published")->count() }},
            doctorCount: {{ \App\Models\User::where("role", "doctor")->count() }},

            checkForUpdates() {
                fetch('{{ route('welcome.check-updates') }}')
                    .then(response => response.json())
                    .then(data => {
                        let needsUpdate = false;

                        if (data.last_announcement_modified !== this.lastAnnouncementModified) needsUpdate = true;
                        if (data.last_doctor_modified !== this.lastDoctorModified) needsUpdate = true;
                        if (data.announcement_count !== this.announcementCount) needsUpdate = true;
                        if (data.doctor_count !== this.doctorCount) needsUpdate = true;

                        if (needsUpdate) {
                            console.log('Update detected (Schedule/Announcement). Silently refreshing...');
                            this.lastAnnouncementModified = data.last_announcement_modified || '';
                            this.lastDoctorModified = data.last_doctor_modified || '';
                            this.announcementCount = data.announcement_count;
                            this.doctorCount = data.doctor_count;
                            this.refreshContent();
                        }
                    })
                    .catch(error => console.error('Error checking updates:', error));
            },
            refreshContent() {
                fetch(window.location.href)
                    .then(response => response.text())
                    .then(html => {
                        let parser = new DOMParser();
                        let doc = parser.parseFromString(html, 'text/html');

                        let newAnnouncements = doc.querySelector('#announcements-container');
                        let currentAnnouncements = document.querySelector('#announcements-container');
                        if (newAnnouncements && currentAnnouncements) {
                            currentAnnouncements.innerHTML = newAnnouncements.innerHTML;
                        }

                        let newSchedule = doc.querySelector('#schedule .grid');
                        let currentSchedule = document.querySelector('#schedule .grid');
                        if (newSchedule && currentSchedule) {
                            currentSchedule.innerHTML = newSchedule.innerHTML;
                        }
                    });
            },
            init() {
                setInterval(() => {
                    this.checkForUpdates();
                }, 5000);
            }
        }" x-init="init()">

        {{-- ============================================================
             HERO SECTION — Government Institutional Hero + Photography
        ============================================================ --}}
        <section class="hero w-full bg-transparent">
            <div class="hero-content max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 sm:py-16 lg:py-20">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-center">

                    {{-- Left Column (lg:col-span-6) — Image Carousel & Real Photography --}}
                    <div class="lg:col-span-6 order-1 lg:order-1" 
                         x-data="{ 
                            activeSlide: 0,
                            slidesCount: {{ count($announcements) + 1 }},
                            touchStartX: 0,
                            touchEndX: 0,
                            nextSlide() { this.activeSlide = (this.activeSlide + 1) % this.slidesCount },
                            prevSlide() { this.activeSlide = (this.activeSlide - 1 + this.slidesCount) % this.slidesCount },
                            autoPlay() { this.interval = setInterval(() => this.nextSlide(), 6000); },
                            stopAutoPlay() { clearInterval(this.interval); },
                            handleTouchStart(e) { this.touchStartX = e.changedTouches[0].screenX; },
                            handleTouchEnd(e) {
                                this.touchEndX = e.changedTouches[0].screenX;
                                const diff = this.touchStartX - this.touchEndX;
                                if (Math.abs(diff) > 50) {
                                    diff > 0 ? this.nextSlide() : this.prevSlide();
                                }
                            }
                         }"
                         @mouseenter="stopAutoPlay" @mouseleave="autoPlay" @touchstart="handleTouchStart" @touchend="handleTouchEnd"
                         x-init="autoPlay">
                        
                        <div class="relative w-full aspect-[4/3] rounded-2xl sm:rounded-3xl overflow-hidden shadow-xl border border-slate-200/90 dark:border-slate-700/80 bg-slate-900">
                            {{-- Base Image Slide --}}
                            <div class="absolute inset-0 transition-opacity duration-1000" :class="activeSlide === 0 ? 'opacity-100 z-10' : 'opacity-0 z-0'">
                                <img src="{{ $heroImageUrl }}" alt="RHU Silang Main Health Facility" class="w-full h-full object-cover">
                                <div class="absolute inset-0 bg-gradient-to-t from-slate-950/85 via-slate-950/25 to-transparent"></div>
                                <div class="absolute bottom-0 left-0 right-0 p-5 sm:p-6 pb-9 sm:pb-10 text-white">
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 bg-emerald-600/90 text-white text-[10px] font-bold uppercase tracking-wider rounded-sm mb-1.5 backdrop-blur-xs">
                                        Municipal Facility
                                    </span>
                                    <h3 class="font-display text-lg sm:text-2xl font-bold leading-tight mb-1 text-white">{{ \App\Models\SiteSetting::get('carousel_hero_title', 'RHU Silang, Cavite') }}</h3>
                                    <p class="text-slate-200 text-xs sm:text-sm font-normal leading-snug">{{ \App\Models\SiteSetting::get('carousel_hero_subtitle', 'Providing Responsive & Quality Healthcare for All Constituents') }}</p>
                                </div>
                            </div>

                            {{-- Announcement Slides --}}
                            @foreach($announcements as $index => $announcement)
                            @php
                                $slideImg = $announcement->image_path;
                                if (!$slideImg && $announcement->images->count() > 0) {
                                    $slideImg = $announcement->images->first()->image_path;
                                }
                                $slideImgUrl = $slideImg 
                                    ? (\Illuminate\Support\Str::startsWith($slideImg, ['uploads/', 'http']) ? asset($slideImg) : asset('uploads/' . $slideImg))
                                    : null;
                            @endphp
                            <div class="absolute inset-0 transition-opacity duration-1000 bg-slate-900" :class="activeSlide === {{ $index + 1 }} ? 'opacity-100 z-10' : 'opacity-0 z-0'">
                                @if($slideImgUrl)
                                    @if(\Illuminate\Support\Str::endsWith($slideImg, '.mp4'))
                                        <video src="{{ $slideImgUrl }}" class="w-full h-full object-cover" muted loop autoplay></video>
                                    @else
                                        <img src="{{ $slideImgUrl }}" alt="{{ $announcement->title }}" class="w-full h-full object-cover">
                                    @endif
                                @endif
                                <div class="absolute inset-0 bg-gradient-to-t from-slate-950/85 via-slate-950/30 to-transparent"></div>
                                <div class="absolute bottom-0 left-0 right-0 p-5 sm:p-6 pb-9 sm:pb-10 text-white">
                                    <span class="inline-flex items-center px-2.5 py-0.5 bg-emerald-600/90 text-white text-[10px] font-bold uppercase tracking-wider rounded-sm mb-1.5 backdrop-blur-xs">Public Bulletin</span>
                                    <h3 class="font-display text-lg sm:text-xl md:text-2xl font-bold leading-tight mb-1 text-white">{{ $announcement->title }}</h3>
                                    <p class="text-slate-200 text-xs sm:text-sm leading-snug line-clamp-2 max-w-lg">{{ $announcement->content }}</p>
                                    <a href="{{ route('announcements.show', $announcement) }}" class="inline-flex items-center gap-1.5 mt-2 text-xs sm:text-sm text-emerald-300 hover:text-white font-semibold transition-colors">
                                        Read full notice <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                    </a>
                                </div>
                            </div>
                            @endforeach

                            {{-- Carousel Navigation Dots --}}
                            <div class="absolute bottom-3 left-1/2 -translate-x-1/2 flex items-center gap-2 z-20">
                                <template x-for="i in slidesCount">
                                    <button @click="activeSlide = i - 1; stopAutoPlay(); autoPlay()" class="h-1.5 rounded-full transition-all duration-300" :class="activeSlide === i - 1 ? 'w-6 bg-white' : 'w-1.5 bg-white/40 hover:bg-white/70'" aria-label="Go to slide"></button>
                                </template>
                            </div>
                        </div>
                    </div>

                    {{-- Right Column (lg:col-span-6) — Text & Municipal Healthcare CTAs --}}
                    <div class="lg:col-span-6 order-2 lg:order-2">
                        {{-- Institutional Kicker --}}
                        <div class="hero-animate hero-animate-delay-1 flex items-center gap-2 mb-4">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-emerald-100/90 dark:bg-emerald-950/70 text-emerald-900 dark:text-emerald-300 text-[11px] font-bold uppercase tracking-widest rounded-md border border-emerald-300/60 dark:border-emerald-700/60 shadow-2xs">
                                <svg class="w-3.5 h-3.5 text-emerald-700 dark:text-emerald-400 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd"/>
                                </svg>
                                {{ \App\Models\SiteSetting::get('hero_badge_text', 'Municipality of Silang') }}
                            </span>
                        </div>

                        {{-- Primary Display H1 --}}
                        <h1 class="hero-animate hero-animate-delay-2 font-display text-3xl sm:text-4xl lg:text-[3.25rem] font-extrabold text-slate-900 dark:text-white mb-4 tracking-tight leading-[1.12]">
                            {{ \App\Models\SiteSetting::get('hero_title_line1', 'Accessible') }}
                            <span class="text-emerald-700 dark:text-emerald-400">{{ \App\Models\SiteSetting::get('hero_title_highlight', 'Public Healthcare') }}</span><br>
                            {{ \App\Models\SiteSetting::get('hero_title_line2', 'for Every Silang Constituent.') }}
                        </h1>

                        {{-- Restrained Body Copy --}}
                        <p class="hero-animate hero-animate-delay-3 text-base sm:text-lg text-slate-600 dark:text-slate-300 mb-8 max-w-xl leading-relaxed font-normal">
                            {{ \App\Models\SiteSetting::get('hero_description', 'The Rural Health Unit is the official municipal healthcare gateway of Silang, Cavite. We provide online appointments, digital triage, doctor consultations, and primary diagnostic referrals.') }}
                        </p>

                        {{-- Action CTAs --}}
                        <div class="hero-animate hero-animate-delay-4 flex flex-col sm:flex-row gap-4 sm:gap-5">
                            {{-- Primary: Book Appointment --}}
                            <div class="flex flex-col items-center sm:items-start">
                                <a href="{{ route('appointment.create') }}" class="btn-cta btn-cta-primary w-full sm:w-auto" id="hero-get-started">
                                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
                                    </svg>
                                    Book an Appointment
                                </a>
                                <span class="text-xs text-slate-500 dark:text-slate-400 mt-2 pl-1">Online appointment scheduling</span>
                            </div>

                            {{-- Secondary: Manage Appointment --}}
                            <div class="flex flex-col items-center sm:items-start">
                                <a href="{{ route('appointment.manage') }}" class="btn-cta btn-cta-secondary w-full sm:w-auto" id="hero-manage-appointment">
                                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z"/>
                                    </svg>
                                    Manage Booking
                                </a>
                                <span class="text-xs text-slate-500 dark:text-slate-400 mt-2 pl-1">Check status or reschedule</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- ============================================================
             ANNOUNCEMENTS SECTION — Crisp Light Neutral Section Background
        ============================================================ --}}
        @php
            $displayAnnouncements = $announcements->take(6);
            $totalPublished = \App\Models\Announcement::where('status', 'published')->count();

            // Semantic category tag inference
            function inferAnnouncementTag($announcement) {
                $text = strtolower(($announcement->title ?? '') . ' ' . strip_tags($announcement->content ?? ''));
                if (preg_match('/health alert|outbreak|dengue|covid|virus|disease|warning/i', $text)) {
                    return ['label' => 'Health Alert', 'class' => 'bg-rose-50 text-rose-700 dark:bg-rose-950/70 dark:text-rose-300 border-rose-200 dark:border-rose-800/60', 'dot' => 'bg-rose-500'];
                }
                if (preg_match('/event|celebration|program|fiesta|activity|campaign|drive|mission/i', $text)) {
                    return ['label' => 'Health Program', 'class' => 'bg-blue-50 text-blue-700 dark:bg-blue-950/70 dark:text-blue-300 border-blue-200 dark:border-blue-800/60', 'dot' => 'bg-blue-500'];
                }
                if (preg_match('/advisory|notice|schedule|closure|suspend|update|memo/i', $text)) {
                    return ['label' => 'Advisory', 'class' => 'bg-amber-50 text-amber-800 dark:bg-amber-950/70 dark:text-amber-300 border-amber-200 dark:border-amber-800/60', 'dot' => 'bg-amber-500'];
                }
                return ['label' => 'Public Bulletin', 'class' => 'bg-emerald-50 text-emerald-800 dark:bg-emerald-950/70 dark:text-emerald-300 border-emerald-200 dark:border-emerald-800/60', 'dot' => 'bg-emerald-500'];
            }
        @endphp

        <div id="announcements" class="bg-slate-50/80 dark:bg-slate-900/60 border-y border-slate-200/80 dark:border-slate-800/80 px-4 sm:px-6 lg:px-8 py-16 sm:py-20">
            <div class="max-w-7xl mx-auto">
                {{-- Section Header --}}
                <div class="text-center mb-10 sm:mb-12" data-reveal>
                    <span class="text-xs font-bold text-emerald-800 dark:text-emerald-400 uppercase tracking-widest">Public Information</span>
                    <h2 class="font-display text-3xl sm:text-4xl font-extrabold text-slate-900 dark:text-white tracking-tight mt-1">{{ __('Municipal Health Bulletins') }}</h2>
                    <p class="text-slate-500 dark:text-slate-400 mt-2 text-base sm:text-lg max-w-xl mx-auto">{{ __('Official advisories, immunization drives, and healthcare notices from RHU Silang') }}</p>
                </div>

                <div id="announcements-container">
                    @if($displayAnnouncements->isNotEmpty())
                        {{-- Uniform, balanced 3-column grid pattern --}}
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                            @foreach($displayAnnouncements as $index => $event)
                                @php
                                    $cardImage = $event->image_path;
                                    if (!$cardImage && $event->images && $event->images->count() > 0) {
                                        $cardImage = $event->images->first()->image_path;
                                    }
                                    $cardImageUrl = null;
                                    if ($cardImage) {
                                        $cardImageUrl = \Illuminate\Support\Str::startsWith($cardImage, ['http://', 'https://', '/']) 
                                            ? $cardImage 
                                            : (\Illuminate\Support\Str::startsWith($cardImage, 'uploads/') ? asset($cardImage) : asset('uploads/' . $cardImage));
                                    }
                                    $isVideo = $cardImage && \Illuminate\Support\Str::endsWith(strtolower($cardImage), ['.mp4', '.webm', '.ogg']);
                                    $tag = inferAnnouncementTag($event);
                                    $rawContent = strip_tags($event->content ?? '');
                                @endphp
                                <div data-reveal style="transition-delay: {{ $index * 50 }}ms" class="h-full">
                                    <a href="{{ route('announcements.show', $event) }}"
                                       class="flex flex-col h-full bg-white dark:bg-slate-800/95 rounded-2xl overflow-hidden border border-slate-200/90 dark:border-slate-700/80 card-hover group shadow-2xs transition-all duration-300">
                                        
                                        {{-- Standard 16:9 Thumbnail --}}
                                        <div class="relative overflow-hidden aspect-[16/9] w-full bg-slate-900 shrink-0">
                                            @if($cardImageUrl)
                                                @if($isVideo)
                                                    <video src="{{ $cardImageUrl }}" class="w-full h-full object-cover" muted loop autoplay playsinline></video>
                                                @else
                                                    <img src="{{ $cardImageUrl }}" alt="{{ $event->title }}"
                                                         class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105" loading="lazy">
                                                @endif
                                            @else
                                                <div class="w-full h-full bg-gradient-to-br from-slate-100 via-slate-50 to-emerald-50/40 dark:from-slate-900 dark:via-slate-850 dark:to-slate-800 flex flex-col items-center justify-center p-4 text-center">
                                                    <div class="w-10 h-10 rounded-xl bg-white dark:bg-slate-800 shadow-2xs border border-slate-200/80 dark:border-slate-700 flex items-center justify-center text-slate-500 dark:text-slate-400 mb-1.5">
                                                        <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/>
                                                        </svg>
                                                    </div>
                                                    <span class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest">RHU Bulletin</span>
                                                </div>
                                            @endif

                                            {{-- Subtle vignette --}}
                                            <div class="absolute inset-0 bg-gradient-to-t from-slate-950/75 via-slate-950/15 to-transparent pointer-events-none"></div>

                                            {{-- Category Tag Pill --}}
                                            <div class="absolute top-3 left-3 z-10">
                                                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 {{ $tag['class'] }} text-[10px] font-bold rounded-md uppercase tracking-wider border shadow-2xs backdrop-blur-md">
                                                    <span class="w-1.5 h-1.5 rounded-full {{ $tag['dot'] }}"></span>
                                                    {{ $tag['label'] }}
                                                </span>
                                            </div>

                                            {{-- Date / Relative Tag on image --}}
                                            <div class="absolute bottom-2.5 left-3 z-10 text-[10px] font-semibold text-slate-200 tracking-wide">
                                                {{ $event->created_at ? $event->created_at->diffForHumans() : '' }}
                                            </div>
                                        </div>

                                        {{-- Card body with uniform height & line-clamps --}}
                                        <div class="p-5 flex flex-col flex-1 justify-between">
                                            <div>
                                                <div class="flex items-center gap-2 text-xs text-slate-500 dark:text-slate-400 mb-2">
                                                    <span class="inline-flex items-center gap-1 font-medium">
                                                        <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                                        {{ $event->created_at ? $event->created_at->format('M d, Y') : '' }}
                                                    </span>
                                                    @if($event->event_date)
                                                        <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded text-[10px] font-bold bg-blue-50 dark:bg-blue-950/40 text-blue-700 dark:text-blue-300">
                                                            Event: {{ $event->event_date->format('M d') }}
                                                        </span>
                                                    @endif
                                                </div>

                                                <h3 class="font-display text-base font-bold text-slate-900 dark:text-white leading-snug mb-1.5 group-hover:text-emerald-700 dark:group-hover:text-emerald-400 transition-colors line-clamp-2">
                                                    {{ $event->title }}
                                                </h3>

                                                <p class="text-slate-600 dark:text-slate-300 text-xs sm:text-sm line-clamp-2 leading-relaxed font-normal">
                                                    {{ \Illuminate\Support\Str::limit($rawContent, 130) }}
                                                </p>
                                            </div>

                                            <div class="mt-4 pt-3.5 border-t border-slate-100 dark:border-slate-700/60 flex items-center justify-between">
                                                <span class="inline-flex items-center gap-1 text-xs font-bold text-emerald-700 dark:text-emerald-400 group-hover:gap-2 transition-all">
                                                    {{ __('Read details') }}
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                                                </span>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-12 bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-8">
                            <p class="text-sm text-slate-500 dark:text-slate-400">No announcements published at this time.</p>
                        </div>
                    @endif
                </div>

                {{-- View All button --}}
                @if($totalPublished > 6)
                    <div class="text-center mt-10" data-reveal>
                        <a href="{{ route('announcements.index') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 rounded-xl font-semibold text-sm hover:bg-slate-100 dark:hover:bg-slate-700 border border-slate-200 dark:border-slate-700 shadow-2xs transition min-h-[48px]">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                            {{ __('View All Municipal Bulletins') }}
                        </a>
                    </div>
                @endif
            </div>
        </div>

        {{-- ============================================================
             LIVE SCHEDULE SECTION — Doctor Directory with Real Availability
        ============================================================ --}}
        <div id="schedule" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 sm:py-20">
            <div class="text-center mb-12" data-reveal>
                <span class="text-xs font-bold text-emerald-800 dark:text-emerald-400 uppercase tracking-widest">Medical Staff Availability</span>
                <h2 class="font-display text-3xl sm:text-4xl font-extrabold text-slate-900 dark:text-white tracking-tight mt-1">{{ __("Doctor's Daily Schedule") }}</h2>
                <p class="text-slate-500 dark:text-slate-400 mt-2 text-base sm:text-lg max-w-xl mx-auto">
                    {{ __('Real-time duty and consultation status of our municipal medical professionals') }}
                </p>
            </div>

            @if(count($doctors) > 3)
                {{-- Horizontal scroll carousel for 4+ doctors --}}
                <div class="relative" x-data="{
                    scrollEl: null,
                    canLeft: false,
                    canRight: false,
                    init() {
                        this.scrollEl = this.$refs.doctorScroll;
                        this.check();
                        this.scrollEl.addEventListener('scroll', () => this.check());
                        window.addEventListener('resize', () => this.check());
                    },
                    check() {
                        if (!this.scrollEl) return;
                        this.canLeft = this.scrollEl.scrollLeft > 10;
                        this.canRight = this.scrollEl.scrollLeft < (this.scrollEl.scrollWidth - this.scrollEl.clientWidth - 10);
                    },
                    goLeft() { this.scrollEl.scrollBy({ left: -380, behavior: 'smooth' }); },
                    goRight() { this.scrollEl.scrollBy({ left: 380, behavior: 'smooth' }); }
                }">
                    {{-- Scroll buttons --}}
                    <button x-show="canLeft" x-transition @click="goLeft()" aria-label="Scroll left"
                        class="absolute left-0 top-1/2 -translate-y-1/2 -translate-x-3 z-10 w-10 h-10 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-full shadow-lg flex items-center justify-center text-slate-600 dark:text-slate-300 hover:bg-emerald-50 dark:hover:bg-emerald-950/40 hover:text-emerald-700 transition-all">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    </button>

                    <div x-ref="doctorScroll" class="flex gap-5 overflow-x-auto pb-4 snap-x snap-mandatory scroll-smooth doctor-scroll">
                        @foreach($doctors as $index => $doctor)
                            @php
                                $displayStatus = $doctor->is_present ? 'Present' : (in_array(strtolower($doctor->status ?? ''), ['seminar']) ? 'Seminar' : 'Unavailable');
                                $statusDotClass = match (strtolower($displayStatus)) {
                                    'present' => 'status-dot-available',
                                    'seminar' => 'status-dot-away',
                                    'unavailable' => 'status-dot-offline',
                                    default => 'status-dot-offline',
                                };
                                $statusBadge = match (strtolower($displayStatus)) {
                                    'present' => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300 border-emerald-300 dark:border-emerald-800',
                                    'seminar' => 'bg-amber-100 text-amber-900 dark:bg-amber-950/60 dark:text-amber-300 border-amber-300 dark:border-amber-800',
                                    'out of office' => 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300 border-slate-300 dark:border-slate-700',
                                    default => 'bg-slate-100 dark:bg-slate-800 dark:text-slate-300',
                                };
                                $roleLabel = match (true) {
                                    str_contains($doctor->role ?? '', 'pedia') => 'Pediatrician',
                                    str_contains($doctor->role ?? '', 'doctor') => 'Municipal Physician',
                                    default => 'Medical Specialist',
                                };
                            @endphp
                            <div class="shrink-0 w-[320px] sm:w-[340px] snap-start" data-reveal style="transition-delay: {{ $index * 60 }}ms">
                                <div class="bg-white dark:bg-slate-800/95 rounded-2xl border border-slate-200/90 dark:border-slate-700/80 p-6 card-hover h-full shadow-2xs">
                                    <div class="flex items-start gap-4 mb-4">
                                        {{-- Avatar with status dot --}}
                                        <div class="relative shrink-0">
                                            <div class="h-14 w-14 rounded-full overflow-hidden bg-slate-100 dark:bg-slate-700 flex items-center justify-center border-2 border-white dark:border-slate-700 shadow-xs">
                                                @if($doctor->avatar_url)
                                                    <img src="{{ $doctor->avatar_url }}" alt="{{ $doctor->name }}" class="w-full h-full object-cover" loading="lazy">
                                                @else
                                                    <span class="text-emerald-700 dark:text-emerald-400 font-bold text-lg">{{ $doctor->initials }}</span>
                                                @endif
                                            </div>
                                            <span class="status-dot {{ $statusDotClass }} absolute -bottom-0.5 -right-0.5 border-2 border-white dark:border-slate-800"></span>
                                        </div>
                                        {{-- Name & role --}}
                                        <div class="min-w-0 flex-1">
                                            <h3 class="text-base font-bold text-slate-900 dark:text-white truncate">{{ $doctor->formatted_name }}</h3>
                                            <p class="text-emerald-700 dark:text-emerald-400 font-medium text-xs">{{ $roleLabel }}</p>
                                        </div>
                                    </div>

                                    {{-- Status badge + schedule --}}
                                    <div class="flex items-center gap-2 mb-3">
                                        <span class="px-2.5 py-0.5 rounded-md text-[10px] font-bold uppercase border {{ $statusBadge }}">{{ $displayStatus }}</span>
                                        @if(strtolower($displayStatus) === 'present')
                                            <span class="text-[10px] text-emerald-700 dark:text-emerald-400 font-semibold">• In Clinic Now</span>
                                        @endif
                                    </div>

                                    <p class="text-slate-500 dark:text-slate-400 text-xs sm:text-sm line-clamp-2 font-normal">
                                        @if($doctor->formatted_schedule)
                                            <svg class="w-3.5 h-3.5 inline-block mr-1 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                            {{ $doctor->formatted_schedule }}
                                        @else
                                            Available for standard municipal consultation.
                                        @endif
                                    </p>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- Scroll right button --}}
                    <button x-show="canRight" x-transition @click="goRight()" aria-label="Scroll right"
                        class="absolute right-0 top-1/2 -translate-y-1/2 translate-x-3 z-10 w-10 h-10 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-full shadow-lg flex items-center justify-center text-slate-600 dark:text-slate-300 hover:bg-emerald-50 dark:hover:bg-emerald-950/40 hover:text-emerald-700 transition-all">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </button>
                </div>
            @else
                {{-- Standard grid for 3 or fewer doctors --}}
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($doctors as $index => $doctor)
                        @php
                            $displayStatus = $doctor->is_present ? 'Present' : (in_array(strtolower($doctor->status ?? ''), ['seminar']) ? 'Seminar' : 'Unavailable');
                            $statusDotClass = match (strtolower($displayStatus)) {
                                'present' => 'status-dot-available',
                                'seminar' => 'status-dot-away',
                                'unavailable' => 'status-dot-offline',
                                default => 'status-dot-offline',
                            };
                            $statusBadge = match (strtolower($displayStatus)) {
                                'present' => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300 border-emerald-300 dark:border-emerald-800',
                                'seminar' => 'bg-amber-100 text-amber-900 dark:bg-amber-950/60 dark:text-amber-300 border-amber-300 dark:border-amber-800',
                                'out of office' => 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300 border-slate-300 dark:border-slate-700',
                                default => 'bg-slate-100 dark:bg-slate-800 dark:text-slate-300',
                            };
                            $roleLabel = match (true) {
                                str_contains($doctor->role ?? '', 'pedia') => 'Pediatrician',
                                str_contains($doctor->role ?? '', 'doctor') => 'Municipal Physician',
                                default => 'Medical Specialist',
                            };
                        @endphp
                        <div class="bg-white dark:bg-slate-800/95 rounded-2xl border border-slate-200/90 dark:border-slate-700/80 p-6 card-hover shadow-2xs"
                             data-reveal style="transition-delay: {{ $index * 60 }}ms">
                            <div class="flex items-start gap-4 mb-4">
                                <div class="relative shrink-0">
                                    <div class="h-14 w-14 rounded-full overflow-hidden bg-slate-100 dark:bg-slate-700 flex items-center justify-center border-2 border-white dark:border-slate-700 shadow-xs">
                                        @if($doctor->avatar_url)
                                            <img src="{{ $doctor->avatar_url }}" alt="{{ $doctor->name }}" class="w-full h-full object-cover" loading="lazy">
                                        @else
                                            <span class="text-emerald-700 dark:text-emerald-400 font-bold text-lg">{{ $doctor->initials }}</span>
                                        @endif
                                    </div>
                                    <span class="status-dot {{ $statusDotClass }} absolute -bottom-0.5 -right-0.5 border-2 border-white dark:border-slate-800"></span>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <h3 class="text-base font-bold text-slate-900 dark:text-white truncate">{{ $doctor->formatted_name }}</h3>
                                    <p class="text-emerald-700 dark:text-emerald-400 font-medium text-xs">{{ $roleLabel }}</p>
                                </div>
                            </div>

                            <div class="flex items-center gap-2 mb-3">
                                <span class="px-2.5 py-0.5 rounded-md text-[10px] font-bold uppercase border {{ $statusBadge }}">{{ $displayStatus }}</span>
                                @if(strtolower($displayStatus) === 'present')
                                    <span class="text-[10px] text-emerald-700 dark:text-emerald-400 font-semibold">• In Clinic Now</span>
                                @endif
                            </div>

                            <p class="text-slate-500 dark:text-slate-400 text-xs sm:text-sm line-clamp-2 font-normal">
                                @if($doctor->formatted_schedule)
                                    <svg class="w-3.5 h-3.5 inline-block mr-1 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    {{ $doctor->formatted_schedule }}
                                @else
                                    Available for standard municipal consultation.
                                @endif
                            </p>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- ============================================================
             CIVIC CONTACT & ACCESS DIRECTORY — High Contrast Structure
        ============================================================ --}}
        <div class="px-4 sm:px-6 lg:px-8 py-14 sm:py-18 bg-slate-50/80 dark:bg-slate-900/60 border-t border-slate-200/80 dark:border-slate-800/80">
            <div class="max-w-7xl mx-auto">

                {{-- Civic Information Cards --}}
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 mb-12" data-reveal>
                    <div class="bg-white dark:bg-slate-800/95 rounded-2xl p-6 border border-slate-200/90 dark:border-slate-700/80 shadow-2xs flex items-start gap-4">
                        <div class="w-11 h-11 shrink-0 rounded-xl bg-emerald-50 dark:bg-emerald-950/50 text-emerald-700 dark:text-emerald-400 flex items-center justify-center border border-emerald-100 dark:border-emerald-800/40">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                        </div>
                        <div>
                            <p class="font-bold text-slate-900 dark:text-white text-sm">{{ __('Emergency Hotlines') }}</p>
                            <p class="text-emerald-700 dark:text-emerald-400 text-sm font-bold mt-0.5">{{ \App\Models\SiteSetting::get('emergency_hotlines', '911 | (046) 432-1234') }}</p>
                            <p class="text-xs text-slate-400 dark:text-slate-500 mt-1">24/7 Municipal First Response</p>
                        </div>
                    </div>

                    <div class="bg-white dark:bg-slate-800/95 rounded-2xl p-6 border border-slate-200/90 dark:border-slate-700/80 shadow-2xs flex items-start gap-4">
                        <div class="w-11 h-11 shrink-0 rounded-xl bg-emerald-50 dark:bg-emerald-950/50 text-emerald-700 dark:text-emerald-400 flex items-center justify-center border border-emerald-100 dark:border-emerald-800/40">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        </div>
                        <div>
                            <p class="font-bold text-slate-900 dark:text-white text-sm">{{ __('Main Health Facility') }}</p>
                            <p class="text-slate-600 dark:text-slate-300 text-sm font-medium mt-0.5">{{ \App\Models\SiteSetting::get('footer_address_line1', 'M.H del Pilar St.') }}, {{ \App\Models\SiteSetting::get('footer_address_line2', 'Silang, Cavite') }}</p>
                            <p class="text-xs text-slate-400 dark:text-slate-500 mt-1">Municipal Health Complex</p>
                        </div>
                    </div>

                    <div class="bg-white dark:bg-slate-800/95 rounded-2xl p-6 border border-slate-200/90 dark:border-slate-700/80 shadow-2xs flex items-start gap-4">
                        <div class="w-11 h-11 shrink-0 rounded-xl bg-emerald-50 dark:bg-emerald-950/50 text-emerald-700 dark:text-emerald-400 flex items-center justify-center border border-emerald-100 dark:border-emerald-800/40">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <div>
                            <p class="font-bold text-slate-900 dark:text-white text-sm">{{ __('Official Clinic Hours') }}</p>
                            <p class="text-slate-600 dark:text-slate-300 text-sm font-medium mt-0.5">{{ \App\Models\SiteSetting::get('clinic_hours', 'Mon - Fri | 8:00 AM - 5:00 PM') }}</p>
                            <p class="text-xs text-emerald-700 dark:text-emerald-400 font-semibold mt-1">Birthing Unit: 24/7 Service</p>
                        </div>
                    </div>
                </div>

                {{-- Action Banner --}}
                <div class="text-center" data-reveal>
                    <div class="bg-white dark:bg-slate-800/95 rounded-3xl p-8 sm:p-10 border border-slate-200/90 dark:border-slate-700/80 shadow-sm max-w-4xl mx-auto">
                        <span class="text-xs font-bold text-emerald-800 dark:text-emerald-400 uppercase tracking-widest">Public Healthcare Access</span>
                        <h3 class="font-display text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-white mt-1 mb-2">{{ __('Schedule Your Clinic Visit Online') }}</h3>
                        <p class="text-slate-500 dark:text-slate-400 text-sm sm:text-base mb-6 max-w-lg mx-auto font-normal">{{ __('Book your consultation ahead to reduce queue times. Walk-in consultations remain welcome during official operating hours.') }}</p>
                        <div class="flex flex-col sm:flex-row gap-3.5 justify-center">
                            <a href="{{ route('appointment.create') }}" class="btn-cta btn-cta-primary" id="closing-get-started">
                                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                                Book an Appointment
                            </a>
                            <a href="{{ route('units.index') }}" class="btn-cta btn-cta-secondary" id="closing-explore">
                                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21m-3.75 7.5h.008v.008h-.008v-.008zm0 3h.008v.008h-.008v-.008z"/></svg>
                                Explore Health Facilities
                            </a>
                        </div>
                    </div>
                </div>

            </div>
        </div>

    </div>
@endsection