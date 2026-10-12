@extends('layouts.app')

@push('head')
    @php
        $heroImage = \App\Models\SiteSetting::get('hero_image', 'assets/images/rhu-facility.jpg');
        $heroImageUrl = asset($heroImage);
    @endphp
    <link rel="preload" as="image" href="{{ $heroImageUrl }}" fetchpriority="high">
@endpush

@section('content')
    @php
        $homeState = \App\Http\Controllers\PublicController::getHomeUpdateState();
    @endphp
    <div x-data="{ 
            lastAnnouncementModified: '{{ $homeState['last_announcement_modified'] }}',
            lastDoctorModified: '{{ $homeState['last_doctor_modified'] }}',
            doctorSignature: '{{ $homeState['doctor_signature'] }}',
            announcementCount: {{ $homeState['announcement_count'] }},
            doctorCount: {{ $homeState['doctor_count'] }},

            checkForUpdates() {
                fetch('{{ route('welcome.check-updates') }}')
                    .then(response => response.json())
                    .then(data => {
                        let needsUpdate = false;

                        if (data.last_announcement_modified !== this.lastAnnouncementModified) needsUpdate = true;
                        if (data.last_doctor_modified !== this.lastDoctorModified) needsUpdate = true;
                        if (data.doctor_signature && data.doctor_signature !== this.doctorSignature) needsUpdate = true;
                        if (data.announcement_count !== this.announcementCount) needsUpdate = true;
                        if (data.doctor_count !== this.doctorCount) needsUpdate = true;

                        if (needsUpdate) {
                            console.log('Update detected (Schedule/Announcement). Silently refreshing...');
                            this.lastAnnouncementModified = data.last_announcement_modified || '';
                            this.lastDoctorModified = data.last_doctor_modified || '';
                            this.doctorSignature = data.doctor_signature || data.last_doctor_modified || '';
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
                            currentAnnouncements.querySelectorAll('[data-reveal]').forEach(el => el.classList.add('is-visible'));
                        }

                        let newSchedule = doc.querySelector('#schedule-container');
                        let currentSchedule = document.querySelector('#schedule-container');
                        if (newSchedule && currentSchedule) {
                            currentSchedule.innerHTML = newSchedule.innerHTML;
                            currentSchedule.querySelectorAll('[data-reveal]').forEach(el => el.classList.add('is-visible'));
                        }

                        let newBadge = doc.querySelector('#schedule-header-badge');
                        let currentBadge = document.querySelector('#schedule-header-badge');
                        if (newBadge && currentBadge) {
                            currentBadge.innerHTML = newBadge.innerHTML;
                        }
                    });
            },
            init() {
                setTimeout(() => {
                    setInterval(() => {
                        this.checkForUpdates();
                    }, 15000);
                }, 10000);
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
                            interval: null,
                            touchStartX: 0,
                            touchEndX: 0,
                            goToSlide(index) {
                                this.activeSlide = index;
                                this.autoPlay();
                            },
                            nextSlide() {
                                if (this.slidesCount <= 1) return;
                                this.activeSlide = (this.activeSlide + 1) % this.slidesCount;
                            },
                            prevSlide() {
                                if (this.slidesCount <= 1) return;
                                this.activeSlide = (this.activeSlide - 1 + this.slidesCount) % this.slidesCount;
                            },
                            autoPlay() {
                                this.stopAutoPlay();
                                if (this.slidesCount <= 1) return;
                                this.interval = setInterval(() => {
                                    this.nextSlide();
                                }, 6000);
                            },
                            stopAutoPlay() {
                                if (this.interval) {
                                    clearInterval(this.interval);
                                    this.interval = null;
                                }
                            },
                            handleTouchStart(e) {
                                this.touchStartX = e.changedTouches[0].screenX;
                            },
                            handleTouchEnd(e) {
                                this.touchEndX = e.changedTouches[0].screenX;
                                const diff = this.touchStartX - this.touchEndX;
                                if (Math.abs(diff) > 50) {
                                    diff > 0 ? this.nextSlide() : this.prevSlide();
                                    this.autoPlay();
                                }
                            },
                            init() {
                                const start = () => {
                                    if (!this.interval) this.autoPlay();
                                };

                                // Synchronize initial start with skeleton loader dismissal so the user sees the first slide for the full 6 seconds
                                const loader = document.getElementById('global-skeleton-loader');
                                if (loader && loader.style.display !== 'none' && window.getComputedStyle(loader).display !== 'none') {
                                    const checkLoader = setInterval(() => {
                                        if (!loader || loader.style.display === 'none' || window.getComputedStyle(loader).opacity === '0') {
                                            clearInterval(checkLoader);
                                            start();
                                        }
                                    }, 100);
                                    setTimeout(() => {
                                        clearInterval(checkLoader);
                                        start();
                                    }, 3000);
                                } else {
                                    start();
                                }

                                // Pause when browser tab is hidden to prevent queued timer skips
                                document.addEventListener('visibilitychange', () => {
                                    if (document.hidden) {
                                        this.stopAutoPlay();
                                    } else {
                                        this.autoPlay();
                                    }
                                });
                            }
                         }"
                         @mouseenter="stopAutoPlay" @mouseleave="autoPlay" @touchstart="handleTouchStart" @touchend="handleTouchEnd"
                         x-init="init()">
                        
                        <div class="group relative w-full aspect-[4/3] rounded-2xl sm:rounded-3xl overflow-hidden shadow-xl border border-slate-200/90 dark:border-slate-700/80 bg-slate-900">
                            {{-- Base Image Slide --}}
                            <div class="absolute inset-0 transition-opacity duration-500 bg-slate-900" 
                                 :class="activeSlide === 0 ? 'opacity-100 z-10' : 'opacity-0 z-0 pointer-events-none'">
                                <img src="{{ $heroImageUrl }}" alt="RHU Silang Main Health Facility" fetchpriority="high" width="600" height="450" class="w-full h-full object-cover">
                                <div class="absolute inset-0 bg-gradient-to-t from-slate-950/85 via-slate-950/25 to-transparent"></div>
                                <div class="absolute bottom-0 left-0 right-0 p-5 sm:p-6 pb-9 sm:pb-10 text-white">
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 bg-emerald-600/90 text-white text-[10px] font-bold uppercase tracking-wider rounded-sm mb-1.5 backdrop-blur-xs">
                                        Municipal Facility
                                    </span>
                                    <h2 class="font-display text-lg sm:text-2xl font-bold leading-tight mb-1 text-white">{{ \App\Models\SiteSetting::get('carousel_hero_title', 'RHU Silang, Cavite') }}</h2>
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
                            <div class="absolute inset-0 transition-opacity duration-500 bg-slate-900" 
                                 :class="activeSlide === {{ $index + 1 }} ? 'opacity-100 z-10' : 'opacity-0 z-0 pointer-events-none'">
                                @if($slideImgUrl)
                                    @if(\Illuminate\Support\Str::endsWith($slideImg, '.mp4'))
                                        <video src="{{ $slideImgUrl }}" class="w-full h-full object-cover" muted loop autoplay preload="none"></video>
                                    @else
                                        <img src="{{ $slideImgUrl }}" alt="{{ $announcement->title }}" width="600" height="450" loading="lazy" class="w-full h-full object-cover">
                                    @endif
                                @else
                                    {{-- Facility ambient backdrop overlay when announcement has no custom media --}}
                                    <img src="{{ $heroImageUrl }}" alt="{{ $announcement->title }}" width="600" height="450" loading="lazy" class="w-full h-full object-cover opacity-25 filter blur-[0.5px]">
                                @endif
                                <div class="absolute inset-0 bg-gradient-to-t from-slate-950/90 via-slate-950/35 to-transparent"></div>
                                <div class="absolute bottom-0 left-0 right-0 p-5 sm:p-6 pb-9 sm:pb-10 text-white">
                                    <span class="inline-flex items-center px-2.5 py-0.5 bg-emerald-600/90 text-white text-[10px] font-bold uppercase tracking-wider rounded-sm mb-1.5 backdrop-blur-xs">Public Bulletin</span>
                                    <h2 class="font-display text-lg sm:text-xl md:text-2xl font-bold leading-tight mb-1 text-white">{{ $announcement->title }}</h2>
                                    <p class="text-slate-200 text-xs sm:text-sm leading-snug line-clamp-2 max-w-lg">{{ $announcement->content }}</p>
                                    <a href="{{ route('announcements.show', $announcement) }}" class="inline-flex items-center gap-1.5 mt-2 text-xs sm:text-sm text-emerald-300 hover:text-white font-semibold transition-colors">
                                        Read full notice <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                    </a>
                                </div>
                            </div>
                            @endforeach

                            {{-- Left / Right Slide Arrows (Visible on hover) --}}
                            @if(count($announcements) > 0)
                            <button type="button" 
                                    @click="prevSlide(); autoPlay()" 
                                    class="absolute left-2.5 top-1/2 -translate-y-1/2 z-20 w-8 h-8 rounded-full bg-slate-950/40 hover:bg-slate-900/80 text-white/80 hover:text-white flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-200 cursor-pointer backdrop-blur-xs shadow-md"
                                    aria-label="Previous slide">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
                            </button>
                            <button type="button" 
                                    @click="nextSlide(); autoPlay()" 
                                    class="absolute right-2.5 top-1/2 -translate-y-1/2 z-20 w-8 h-8 rounded-full bg-slate-950/40 hover:bg-slate-900/80 text-white/80 hover:text-white flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-200 cursor-pointer backdrop-blur-xs shadow-md"
                                    aria-label="Next slide">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                            </button>
                            @endif

                            {{-- Carousel Navigation Dots --}}
                            <div class="absolute bottom-1 left-1/2 -translate-x-1/2 flex items-center z-20">
                                <template x-for="i in slidesCount">
                                    {{-- 24px hit area around the small visible dot --}}
                                    <button type="button" @click="goToSlide(i - 1)" class="group h-6 min-w-6 px-1 flex items-center justify-center cursor-pointer" :aria-label="'Go to slide ' + i" :aria-current="activeSlide === i - 1 ? 'true' : 'false'">
                                        <span class="block h-1.5 rounded-full transition-all duration-300" :class="activeSlide === i - 1 ? 'w-6 bg-white' : 'w-1.5 bg-white/40 group-hover:bg-white/70'"></span>
                                    </button>
                                </template>
                            </div>
                        </div>
                    </div>

                    {{-- Right Column (lg:col-span-6) — Text & Municipal Healthcare CTAs --}}
                    <div class="lg:col-span-6 order-2 lg:order-2">
                        {{-- Municipal Standards & Coverage Badge --}}
                        <div class="hero-animate hero-animate-delay-1 flex items-center mb-5">
                            <div class="inline-flex items-center gap-2 sm:gap-2.5 p-1 pr-3.5 sm:pr-4 rounded-full bg-white/90 dark:bg-slate-900/85 border border-slate-200/90 dark:border-slate-800 shadow-2xs backdrop-blur-md hover:border-emerald-500/40 hover:shadow-xs transition-all duration-300">
                                {{-- Accreditation Shield Chip --}}
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-emerald-100/90 dark:bg-emerald-950/80 text-emerald-800 dark:text-emerald-300 text-[10px] font-bold uppercase tracking-wider border border-emerald-300/50 dark:border-emerald-700/50 shadow-2xs shrink-0">
                                    <svg class="w-3 h-3 text-emerald-600 dark:text-emerald-400" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                                        <path fill-rule="evenodd" d="M12 1.5l8.5 3.5v7c0 6-4.5 10.5-8.5 12-4-1.5-8.5-6-8.5-12V5L12 1.5zm3.7 7.3a1 1 0 00-1.4-1.4L10.5 11.2 8.7 9.4a1 1 0 10-1.4 1.4l2.5 2.5a1 1 0 001.4 0l4.5-4.5z" clip-rule="evenodd"/>
                                    </svg>
                                    <span>DOH-Accredited</span>
                                </span>

                                {{-- Barangay Coverage --}}
                                <div class="flex items-center gap-1.5 text-xs text-slate-700 dark:text-slate-200 font-medium">
                                    <svg class="w-3.5 h-3.5 text-emerald-600 dark:text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                    <span>Serving <strong class="font-bold text-slate-900 dark:text-white">64 Barangays</strong> of Silang, Cavite</span>
                                </div>
                            </div>
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
            if (!function_exists('inferAnnouncementTag')) {
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
            }
        @endphp

        <div id="announcements" class="bg-slate-50 dark:bg-slate-900 border-y border-slate-200/80 dark:border-slate-800/80 px-4 sm:px-6 lg:px-8 py-10 sm:py-14 transition-colors">
            <div class="max-w-7xl mx-auto">
                {{-- Section Header --}}
                <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-6 sm:mb-8" data-reveal>
                    <div>
                        <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-emerald-100/80 dark:bg-emerald-950/60 text-emerald-800 dark:text-emerald-300 text-[11px] font-bold tracking-wide mb-2 border border-emerald-200/60 dark:border-emerald-800/60">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                            {{ __('News and updates') }}
                        </div>
                        <h2 class="font-display text-2xl sm:text-3xl lg:text-4xl font-extrabold text-slate-900 dark:text-white tracking-tight leading-[1.15]">
                            {{ __('Latest news and insights') }}
                        </h2>
                        <p class="text-slate-500 dark:text-slate-400 mt-1.5 text-sm sm:text-base max-w-lg">
                            {{ __('Official municipal advisories, health drives, and healthcare bulletins from RHU Silang') }}
                        </p>
                    </div>

                    @if($totalPublished > 4)
                        <div class="shrink-0">
                            <a href="{{ route('announcements.index') }}" class="group inline-flex items-center gap-2 text-xs sm:text-sm font-bold text-emerald-700 dark:text-emerald-400 hover:text-emerald-800 dark:hover:text-emerald-300 transition-colors">
                                <span>{{ __('View all bulletins') }}</span>
                                <span class="w-7 h-7 rounded-full bg-emerald-100 dark:bg-emerald-950/60 flex items-center justify-center group-hover:scale-110 group-hover:bg-emerald-200 dark:group-hover:bg-emerald-900/60 transition-all">
                                    <svg class="w-3.5 h-3.5 transform group-hover:translate-x-0.5 group-hover:-translate-y-0.5 transition-transform" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M7 17L17 7M17 7H7M17 7V17"/></svg>
                                </span>
                            </a>
                        </div>
                    @endif
                </div>

                <div id="announcements-container">
                    @if($displayAnnouncements->isNotEmpty())
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-12 gap-3.5 lg:gap-4.5">
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

                                    // Compact Bento Grid Sizing
                                    $colSpanClass = match($index) {
                                        0 => 'col-span-1 md:col-span-2 lg:col-span-6 lg:row-span-2 min-h-[360px] lg:min-h-[390px]',
                                        1 => 'col-span-1 md:col-span-2 lg:col-span-6 min-h-[175px] lg:min-h-[188px]',
                                        2 => 'col-span-1 md:col-span-1 lg:col-span-3 min-h-[175px] lg:min-h-[188px]',
                                        3 => 'col-span-1 md:col-span-1 lg:col-span-3 min-h-[175px] lg:min-h-[188px]',
                                        4 => ($displayAnnouncements->count() === 5) 
                                                ? 'col-span-1 md:col-span-2 lg:col-span-12 min-h-[160px]' 
                                                : 'col-span-1 md:col-span-1 lg:col-span-6 min-h-[175px]',
                                        5 => 'col-span-1 md:col-span-1 lg:col-span-6 min-h-[175px]',
                                        default => 'col-span-1 md:col-span-1 lg:col-span-6 min-h-[175px]',
                                    };

                                    // Reference aesthetic styling per card slot
                                    $isHeroCard = ($index === 0);
                                    
                                    // Base card colors matching reference image
                                    $cardBgClass = match($index) {
                                        0 => 'bg-[#8cd790] dark:bg-emerald-900/90 text-slate-900 dark:text-white',
                                        1 => 'bg-[#22262c] text-white',
                                        2 => 'bg-white dark:bg-slate-850 text-slate-900 dark:text-white border border-slate-200/90 dark:border-slate-700/80',
                                        3 => 'bg-[#b5e59b] dark:bg-emerald-950 text-slate-900 dark:text-white dark:border dark:border-emerald-800/70',
                                        4 => 'bg-[#1e2430] text-white',
                                        5 => 'bg-[#8cd790] dark:bg-emerald-900 text-slate-900 dark:text-white',
                                        default => 'bg-white dark:bg-slate-850 text-slate-900 dark:text-white border border-slate-200/90 dark:border-slate-700/80',
                                    };

                                    // Button color
                                    $btnStyle = match($index) {
                                        1, 4 => 'bg-white text-slate-900 group-hover:bg-emerald-400 group-hover:text-slate-950',
                                        default => 'bg-slate-900 text-white dark:bg-white dark:text-slate-900 group-hover:bg-emerald-600 group-hover:text-white dark:group-hover:bg-emerald-500 dark:group-hover:text-white',
                                    };

                                    // Tag pill color
                                    $tagPillClass = match($index) {
                                        1, 4 => 'bg-white/10 text-slate-200 border-white/15',
                                        2 => 'bg-slate-100 dark:bg-slate-800 text-slate-800 dark:text-slate-200 border-slate-200/80 dark:border-slate-700',
                                        default => 'bg-white/80 dark:bg-slate-900/80 text-slate-900 dark:text-white border-slate-900/10 dark:border-white/10',
                                    };

                                    // Compact Title font sizes
                                    $titleSizeClass = match($index) {
                                        0 => 'text-lg sm:text-xl lg:text-2xl leading-snug',
                                        1 => 'text-base sm:text-lg leading-snug',
                                        default => 'text-sm sm:text-base leading-snug',
                                    };

                                    $dateColorClass = match($index) {
                                        1, 4 => 'text-slate-400',
                                        2 => 'text-slate-500 dark:text-slate-400',
                                        default => 'text-slate-700 dark:text-slate-300',
                                    };
                                @endphp

                                <div data-reveal style="transition-delay: {{ $index * 50 }}ms" class="{{ $colSpanClass }}">
                                    <a href="{{ route('announcements.show', $event) }}"
                                       class="relative rounded-[24px] overflow-hidden flex flex-col justify-between p-4.5 sm:p-5.5 h-full shadow-xs hover:shadow-xl transition-all duration-300 group {{ $cardBgClass }} cursor-pointer">

                                        {{-- Image Layer if card has uploaded media --}}
                                        @if($cardImageUrl)
                                            <div class="absolute inset-0 z-0 overflow-hidden">
                                                @if($isVideo)
                                                    <video src="{{ $cardImageUrl }}" class="w-full h-full object-cover" muted loop autoplay playsinline></video>
                                                @else
                                                    <img src="{{ $cardImageUrl }}" alt="{{ $event->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700" loading="lazy">
                                                @endif
                                                <div class="absolute inset-0 bg-gradient-to-t from-slate-950/95 via-slate-950/50 to-slate-950/20 pointer-events-none"></div>
                                            </div>
                                        @elseif($isHeroCard)
                                            {{-- Reference image geometric wireframe watermark for Card 0 --}}
                                            <div class="absolute inset-0 z-0 flex items-center justify-center overflow-hidden pointer-events-none select-none">
                                                <svg viewBox="0 0 240 240" class="w-56 h-56 sm:w-64 sm:h-64 text-slate-900/10 dark:text-white/10 fill-none">
                                                    <path stroke="currentColor" stroke-width="1.5" d="M 120,20 C 120,80 80,120 20,120 C 80,120 120,160 120,220 C 120,160 160,120 220,120 C 160,120 120,80 120,20 Z" />
                                                    <path stroke="currentColor" stroke-width="1" stroke-dasharray="3 3" d="M 120,35 C 120,85 85,120 35,120 C 85,120 120,155 120,205 C 120,155 155,120 205,120 C 155,120 120,85 120,35 Z" opacity="0.6"/>
                                                    <path stroke="currentColor" stroke-width="0.8" d="M 120,50 C 120,90 90,120 50,120 C 90,120 120,150 120,190 C 120,150 150,120 190,120 C 150,120 120,90 120,50 Z" opacity="0.4"/>
                                                </svg>
                                            </div>
                                        @endif

                                        {{-- Signature Inverted Fillet Cutout Notch SVG (Scaled down proportionally) --}}
                                        <svg viewBox="0 0 72 72" class="w-[58px] h-[58px] absolute -top-[1px] -right-[1px] pointer-events-none fill-slate-50 dark:fill-slate-900 z-10 transition-colors">
                                            <path d="M 0,0 C 16,0 24,6 24,18 L 24,26 C 24,40 32,48 46,48 L 54,48 C 66,48 72,56 72,72 L 72,0 Z"/>
                                        </svg>

                                        {{-- Compact Floating Circular Action Button with ↗ Arrow --}}
                                        <div class="absolute top-1 right-1 sm:top-1.5 sm:right-1.5 z-20">
                                            <div class="w-8.5 h-8.5 sm:w-9 sm:h-9 rounded-full {{ $btnStyle }} flex items-center justify-center shadow-xs group-hover:scale-110 transition-all duration-300">
                                                <svg class="w-3.5 h-3.5 transform group-hover:translate-x-0.5 group-hover:-translate-y-0.5 transition-transform duration-300" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M7 17L17 7M17 7H7M17 7V17"/>
                                                </svg>
                                            </div>
                                        </div>

                                        {{-- Top Content: Category Pill --}}
                                        <div class="relative z-10 pr-14">
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full {{ $cardImageUrl ? 'bg-white/20 text-white border border-white/20 backdrop-blur-md' : $tagPillClass }} text-[11px] font-bold tracking-wide border shadow-2xs backdrop-blur-md">
                                                <span class="w-1.5 h-1.5 rounded-full {{ $tag['dot'] }}"></span>
                                                {{ $tag['label'] }}
                                            </span>
                                        </div>

                                        {{-- Bottom Content: Title & Date --}}
                                        <div class="relative z-10 mt-4 sm:mt-5">
                                            @if($event->event_date)
                                                <div class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-bold mb-1.5 {{ $cardImageUrl || $index === 1 || $index === 4 ? 'bg-white/20 text-white' : 'bg-slate-900/10 dark:bg-white/10 text-slate-800 dark:text-slate-200' }}">
                                                    <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                                    <span>Event: {{ $event->event_date->format('M d, Y') }}</span>
                                                </div>
                                            @endif

                                            <h3 class="font-display font-extrabold tracking-tight {{ $titleSizeClass }} {{ $cardImageUrl ? 'text-white' : '' }} line-clamp-2 mb-1.5 group-hover:opacity-90 transition-opacity">
                                                {{ $event->title }}
                                            </h3>

                                            <div class="flex items-center gap-2.5 text-[11px] sm:text-xs font-medium {{ $cardImageUrl ? 'text-slate-300' : $dateColorClass }}">
                                                <span>{{ $event->created_at ? $event->created_at->format('d F Y') : '' }}</span>
                                                @if($event->created_at)
                                                    <span class="inline-block w-1 h-1 rounded-full bg-current opacity-60"></span>
                                                    <span class="opacity-80">{{ $event->created_at->diffForHumans() }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-12 bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-6 shadow-2xs">
                            <div class="w-12 h-12 mx-auto mb-3 rounded-xl bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center border border-emerald-100 dark:border-emerald-800/50">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/></svg>
                            </div>
                            <h3 class="text-base font-bold text-slate-900 dark:text-white">{{ __('No Bulletins Published') }}</h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 max-w-sm mx-auto">{{ __('No municipal health announcements are currently active. Check back later for updates.') }}</p>
                        </div>
                    @endif
                </div>

                {{-- View All button --}}
                @if($totalPublished > 6)
                    <div class="text-center mt-8" data-reveal>
                        <a href="{{ route('announcements.index') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-200 rounded-xl font-bold text-xs sm:text-sm hover:bg-slate-100 dark:hover:bg-slate-700 border border-slate-200 dark:border-slate-700 shadow-2xs transition group">
                            <svg class="w-3.5 h-3.5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                            <span>{{ __('View All Municipal Bulletins') }}</span>
                            <svg class="w-3.5 h-3.5 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                        </a>
                    </div>
                @endif
            </div>
        </div>

        {{-- ============================================================
             LIVE SCHEDULE SECTION — Doctor Directory with Real Availability
             Displays active/online doctors, or an informative empty state if none are on duty
        ============================================================ --}}
        <div id="schedule" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 sm:py-14">
            {{-- Header --}}
            <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-6 sm:mb-8" data-reveal>
                <div>
                    <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-emerald-100/80 dark:bg-emerald-950/60 text-emerald-800 dark:text-emerald-300 text-[11px] font-bold tracking-wide mb-2 border border-emerald-200/60 dark:border-emerald-800/60">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                        {{ __('Medical Staff Availability') }}
                    </div>
                    <h2 class="font-display text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-white tracking-tight">
                        {{ __("Doctor's Daily Schedule") }}
                    </h2>
                    <p class="text-slate-500 dark:text-slate-400 mt-1.5 text-sm sm:text-base max-w-lg">
                        {{ __('Real-time duty status, medical specialty, and clinic consultation hours at RHU Silang') }}
                    </p>
                </div>

                <div id="schedule-header-badge">
                    @if(count($doctors) > 0)
                        <div class="flex items-center gap-3 shrink-0">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-white dark:bg-slate-800/90 text-slate-700 dark:text-slate-200 text-[11px] font-bold border border-slate-200/80 dark:border-slate-700 shadow-2xs">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-ping"></span>
                                <span>{{ count($doctors) }} {{ count($doctors) === 1 ? __('Doctor Available') : __('Doctors Available') }}</span>
                            </span>
                        </div>
                    @endif
                </div>
            </div>

            <div id="schedule-container">
            @if(count($doctors) > 0)
                @if(count($doctors) > 3)
                    {{-- Carousel for 4+ doctors --}}
                    <div class="relative" x-data="{
                        scrollEl: null,
                        canLeft: false,
                        canRight: false,
                        init() {
                            this.scrollEl = this.$refs.doctorScroll;
                            this.$nextTick(() => this.check());
                            this.scrollEl.addEventListener('scroll', () => this.check(), { passive: true });
                            window.addEventListener('resize', () => this.check(), { passive: true });
                        },
                        check() {
                            if (!this.scrollEl) return;
                            this.canLeft = this.scrollEl.scrollLeft > 10;
                            this.canRight = this.scrollEl.scrollLeft < (this.scrollEl.scrollWidth - this.scrollEl.clientWidth - 10);
                        },
                        goLeft() { this.scrollEl.scrollBy({ left: -290, behavior: 'smooth' }); },
                        goRight() { this.scrollEl.scrollBy({ left: 290, behavior: 'smooth' }); }
                    }">
                        {{-- Carousel Top-Right Navigation Controls --}}
                        <div class="flex items-center justify-end gap-1.5 mb-3">
                            <button type="button" @click="goLeft()" :disabled="!canLeft" aria-label="Previous Doctor"
                                class="w-8.5 h-8.5 rounded-full border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 flex items-center justify-center hover:bg-slate-100 dark:hover:bg-slate-700 disabled:opacity-30 disabled:cursor-not-allowed transition shadow-2xs">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                            </button>
                            <button type="button" @click="goRight()" :disabled="!canRight" aria-label="Next Doctor"
                                class="w-8.5 h-8.5 rounded-full border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 flex items-center justify-center hover:bg-slate-100 dark:hover:bg-slate-700 disabled:opacity-30 disabled:cursor-not-allowed transition shadow-2xs">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                            </button>
                        </div>

                        <div x-ref="doctorScroll" class="flex items-stretch gap-4 sm:gap-5 overflow-x-auto overflow-y-hidden pt-1.5 pb-3.5 snap-x snap-mandatory scroll-smooth doctor-scroll" style="overflow-x: auto; overflow-y: hidden;">
                            @foreach($doctors as $index => $doctor)
                                @php
                                    $displayStatus = $doctor->presence_status ?? $doctor->effective_status;
                                    if ($displayStatus === 'Offline') {
                                        continue;
                                    }
                                    $statusDotClass = match (strtolower($displayStatus)) {
                                        'online' => 'status-dot-available',
                                        'occupied' => 'status-dot-away',
                                        default => 'status-dot-offline',
                                    };
                                    $statusBadge = match (strtolower($displayStatus)) {
                                        'online' => 'bg-emerald-50 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300 border-emerald-200 dark:border-emerald-800/80',
                                        'occupied' => 'bg-amber-50 text-amber-900 dark:bg-amber-950/60 dark:text-amber-300 border-amber-200 dark:border-amber-800/80',
                                        default => 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300 border-slate-200 dark:border-slate-700',
                                    };
                                    $statusIndicatorDot = match (strtolower($displayStatus)) {
                                        'online' => 'bg-emerald-500 animate-pulse',
                                        'occupied' => 'bg-amber-500',
                                        default => 'bg-slate-400',
                                    };
                                    $statusText = match (strtolower($displayStatus)) {
                                        'online' => 'In Clinic',
                                        'occupied' => 'In Consultation',
                                        default => 'Offline',
                                    };
                                    $roleLabel = match (true) {
                                        str_contains($doctor->role ?? '', 'pedia') => 'Pediatrician',
                                        str_contains($doctor->role ?? '', 'doctor') => 'Municipal Physician',
                                        default => 'Medical Specialist',
                                    };
                                @endphp
                                <div class="shrink-0 w-[260px] sm:w-[280px] snap-start flex flex-col" data-reveal style="transition-delay: {{ $index * 50 }}ms">
                                    <div class="bg-white dark:bg-slate-800/95 rounded-2xl border border-slate-200/90 dark:border-slate-700/80 p-4.5 sm:p-5 card-hover shadow-2xs flex flex-col justify-between h-full group transition-all duration-300 hover:border-emerald-500/40 hover:shadow-lg">
                                        <div>
                                            {{-- Avatar & Status Pill --}}
                                            <div class="flex items-start justify-between gap-2.5 mb-3">
                                                <div class="relative shrink-0">
                                                    <div class="h-13 w-13 sm:h-14 sm:w-14 rounded-xl overflow-hidden bg-gradient-to-br from-emerald-50 to-teal-100 dark:from-slate-700 dark:to-slate-650 flex items-center justify-center ring-2 ring-slate-100 dark:ring-slate-700/60 shadow-xs">
                                                        @if($doctor->avatar_url)
                                                            <img src="{{ $doctor->avatar_url }}" alt="{{ $doctor->formatted_name }}" class="w-full h-full object-cover" loading="lazy">
                                                        @else
                                                            <span class="text-emerald-800 dark:text-emerald-300 font-display font-extrabold text-base sm:text-lg">{{ $doctor->initials }}</span>
                                                        @endif
                                                    </div>
                                                    <span class="status-dot {{ $statusDotClass }} absolute -bottom-0.5 -right-0.5 border-2 border-white dark:border-slate-800"></span>
                                                </div>

                                                <div class="shrink-0">
                                                    <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-[9.5px] font-bold uppercase tracking-wider border {{ $statusBadge }}">
                                                        <span class="w-1.5 h-1.5 rounded-full {{ $statusIndicatorDot }}"></span>
                                                        {{ $statusText }}
                                                    </span>
                                                </div>
                                            </div>

                                            {{-- Doctor Name & Role --}}
                                            <div>
                                                <h3 class="text-[15px] font-bold text-slate-900 dark:text-white truncate font-display group-hover:text-emerald-700 dark:group-hover:text-emerald-400 transition-colors">
                                                    {{ $doctor->formatted_name }}
                                                </h3>
                                                <p class="text-emerald-700 dark:text-emerald-400 font-semibold text-[11px] mt-0.5 flex items-center gap-1">
                                                    <svg class="w-3 h-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                                    {{ $roleLabel }}
                                                </p>
                                            </div>

                                            {{-- Consultation Timetable Box --}}
                                            <div class="mt-3 p-2.5 sm:p-3 rounded-xl bg-slate-50 dark:bg-slate-900/60 border border-slate-100 dark:border-slate-800/80">
                                                <div class="flex items-center gap-1 text-[9.5px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-0.5">
                                                    <svg class="w-2.5 h-2.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                    <span>{{ __('Duty Schedule') }}</span>
                                                </div>
                                                <p class="text-[11px] font-semibold text-slate-700 dark:text-slate-200 line-clamp-2 leading-relaxed">
                                                    @if($doctor->formatted_schedule)
                                                        {{ $doctor->formatted_schedule }}
                                                    @else
                                                        {{ __('Mon - Fri | Regular Clinic Consultation') }}
                                                    @endif
                                                </p>
                                            </div>
                                        </div>

                                        {{-- Action Footer --}}
                                        <div class="mt-3 pt-2.5 border-t border-slate-100 dark:border-slate-700/60 flex items-center justify-between">
                                            <a href="{{ route('appointment.create') }}" class="inline-flex items-center gap-1 text-[11px] font-bold text-emerald-700 dark:text-emerald-400 hover:text-emerald-800 dark:hover:text-emerald-300 group/link transition-colors">
                                                <span>{{ __('Book Consultation') }}</span>
                                                <svg class="w-3 h-3 transform group-hover/link:translate-x-1 transition-transform" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                                            </a>
                                            <span class="text-[10px] font-medium text-slate-400 dark:text-slate-500">
                                                RHU Silang
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @else
                    {{-- Standard Grid for 1 - 3 doctors --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-5">
                        @foreach($doctors as $index => $doctor)
                            @php
                                $displayStatus = $doctor->presence_status ?? $doctor->effective_status;
                                if ($displayStatus === 'Offline') {
                                    continue;
                                }
                                $statusDotClass = match (strtolower($displayStatus)) {
                                    'online' => 'status-dot-available',
                                    'occupied' => 'status-dot-away',
                                    default => 'status-dot-offline',
                                };
                                $statusBadge = match (strtolower($displayStatus)) {
                                    'online' => 'bg-emerald-50 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300 border-emerald-200 dark:border-emerald-800/80',
                                    'occupied' => 'bg-amber-50 text-amber-900 dark:bg-amber-950/60 dark:text-amber-300 border-amber-200 dark:border-amber-800/80',
                                    default => 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300 border-slate-200 dark:border-slate-700',
                                };
                                $statusIndicatorDot = match (strtolower($displayStatus)) {
                                    'online' => 'bg-emerald-500 animate-pulse',
                                    'occupied' => 'bg-amber-500',
                                    default => 'bg-slate-400',
                                };
                                $statusText = match (strtolower($displayStatus)) {
                                    'online' => 'In Clinic',
                                    'occupied' => 'In Consultation',
                                    default => 'Offline',
                                };
                                $roleLabel = match (true) {
                                    str_contains($doctor->role ?? '', 'pedia') => 'Pediatrician',
                                    str_contains($doctor->role ?? '', 'doctor') => 'Municipal Physician',
                                    default => 'Medical Specialist',
                                };
                            @endphp
                            <div class="bg-white dark:bg-slate-800/95 rounded-2xl border border-slate-200/90 dark:border-slate-700/80 p-4.5 sm:p-5 card-hover shadow-2xs flex flex-col justify-between group transition-all duration-300 hover:border-emerald-500/40 hover:shadow-lg"
                                 data-reveal style="transition-delay: {{ $index * 50 }}ms">
                                <div>
                                    {{-- Avatar & Status Pill --}}
                                    <div class="flex items-start justify-between gap-2.5 mb-3">
                                        <div class="relative shrink-0">
                                            <div class="h-13 w-13 sm:h-14 sm:w-14 rounded-xl overflow-hidden bg-gradient-to-br from-emerald-50 to-teal-100 dark:from-slate-700 dark:to-slate-650 flex items-center justify-center ring-2 ring-slate-100 dark:ring-slate-700/60 shadow-xs">
                                                @if($doctor->avatar_url)
                                                    <img src="{{ $doctor->avatar_url }}" alt="{{ $doctor->formatted_name }}" class="w-full h-full object-cover" loading="lazy">
                                                @else
                                                    <span class="text-emerald-800 dark:text-emerald-300 font-display font-extrabold text-base sm:text-lg">{{ $doctor->initials }}</span>
                                                @endif
                                            </div>
                                            <span class="status-dot {{ $statusDotClass }} absolute -bottom-0.5 -right-0.5 border-2 border-white dark:border-slate-800"></span>
                                        </div>

                                        <div class="shrink-0">
                                            <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-[9.5px] font-bold uppercase tracking-wider border {{ $statusBadge }}">
                                                <span class="w-1.5 h-1.5 rounded-full {{ $statusIndicatorDot }}"></span>
                                                {{ $statusText }}
                                            </span>
                                        </div>
                                    </div>

                                    {{-- Doctor Name & Role --}}
                                    <div>
                                        <h3 class="text-[15px] font-bold text-slate-900 dark:text-white truncate font-display group-hover:text-emerald-700 dark:group-hover:text-emerald-400 transition-colors">
                                            {{ $doctor->formatted_name }}
                                        </h3>
                                        <p class="text-emerald-700 dark:text-emerald-400 font-semibold text-[11px] mt-0.5 flex items-center gap-1">
                                            <svg class="w-3 h-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                            {{ $roleLabel }}
                                        </p>
                                    </div>

                                    {{-- Consultation Timetable Box --}}
                                    <div class="mt-3 p-2.5 sm:p-3 rounded-xl bg-slate-50 dark:bg-slate-900/60 border border-slate-100 dark:border-slate-800/80">
                                        <div class="flex items-center gap-1 text-[9.5px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-0.5">
                                            <svg class="w-2.5 h-2.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                            <span>{{ __('Duty Schedule') }}</span>
                                        </div>
                                        <p class="text-[11px] font-semibold text-slate-700 dark:text-slate-200 line-clamp-2 leading-relaxed">
                                            @if($doctor->formatted_schedule)
                                                {{ $doctor->formatted_schedule }}
                                            @else
                                                {{ __('Mon - Fri | Regular Clinic Consultation') }}
                                            @endif
                                        </p>
                                    </div>
                                </div>

                                {{-- Action Footer --}}
                                <div class="mt-3 pt-2.5 border-t border-slate-100 dark:border-slate-700/60 flex items-center justify-between">
                                    <a href="{{ route('appointment.create') }}" class="inline-flex items-center gap-1 text-[11px] font-bold text-emerald-700 dark:text-emerald-400 hover:text-emerald-800 dark:hover:text-emerald-300 group/link transition-colors">
                                        <span>{{ __('Book Consultation') }}</span>
                                        <svg class="w-3 h-3 transform group-hover/link:translate-x-1 transition-transform" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                                    </a>
                                    <span class="text-[10px] font-medium text-slate-400 dark:text-slate-500">
                                        RHU Silang
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            @else
                {{-- Empty state: No doctors currently online --}}
                <div class="max-w-xl mx-auto text-center" data-reveal>
                    <div class="bg-white/90 dark:bg-slate-800/90 rounded-2xl p-6 sm:p-8 border border-slate-200/90 dark:border-slate-700/80 shadow-2xs backdrop-blur-sm transition-all">
                        {{-- Icon badge --}}
                        <div class="w-12 h-12 mx-auto mb-3 rounded-xl bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center border border-emerald-100 dark:border-emerald-800/50">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 10h4" />
                            </svg>
                        </div>

                        {{-- Status pill --}}
                        <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-slate-100 dark:bg-slate-700/80 text-slate-600 dark:text-slate-300 text-[11px] font-semibold mb-2.5 border border-slate-200 dark:border-slate-600">
                            <span class="w-1.5 h-1.5 rounded-full bg-slate-400 dark:bg-slate-500"></span>
                            <span>{{ __('Offline / Off Duty') }}</span>
                        </div>

                        {{-- Title --}}
                        <h3 class="text-lg sm:text-xl font-bold text-slate-900 dark:text-white tracking-tight">
                            {{ __('No Doctors Online') }}
                        </h3>

                        {{-- Description --}}
                        <p class="text-slate-500 dark:text-slate-400 text-xs sm:text-sm mt-1.5 max-w-sm mx-auto leading-relaxed">
                            {{ __('There are currently no municipal doctors online or on duty. Our medical professionals will be available during regular clinic consultation hours.') }}
                        </p>

                        {{-- Quick Info Cards --}}
                        <div class="mt-5 pt-5 border-t border-slate-100 dark:border-slate-700/60 grid grid-cols-1 sm:grid-cols-2 gap-2.5 text-left">
                            <div class="flex items-center gap-2.5 p-2.5 sm:p-3 rounded-xl bg-slate-50 dark:bg-slate-900/40 border border-slate-200/60 dark:border-slate-700/40">
                                <div class="w-8 h-8 rounded-lg bg-emerald-100 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-400 flex items-center justify-center shrink-0">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                </div>
                                <div class="min-w-0">
                                    <p class="text-[10px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">{{ __('Regular Clinic Hours') }}</p>
                                    <p class="text-[11px] font-bold text-slate-700 dark:text-slate-200 truncate">{{ \App\Models\SiteSetting::get('clinic_hours', 'Mon - Fri | 8:00 AM - 5:00 PM') }}</p>
                                </div>
                            </div>

                            <div class="flex items-center gap-2.5 p-2.5 sm:p-3 rounded-xl bg-slate-50 dark:bg-slate-900/40 border border-slate-200/60 dark:border-slate-700/40">
                                <div class="w-8 h-8 rounded-lg bg-red-100 dark:bg-red-950/60 text-red-700 dark:text-red-400 flex items-center justify-center shrink-0">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                </div>
                                <div class="min-w-0">
                                    <p class="text-[10px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">{{ __('RHU Contact Number') }}</p>
                                    <p class="text-[11px] font-bold text-emerald-600 dark:text-emerald-400 truncate">{{ \App\Models\SiteSetting::get('emergency_hotlines', '(046) 432-1234') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
            </div>
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
                            <p class="font-bold text-slate-900 dark:text-white text-sm">{{ __('RHU Contact Number') }}</p>
                            <p class="text-emerald-700 dark:text-emerald-400 text-sm font-bold mt-0.5">{{ \App\Models\SiteSetting::get('emergency_hotlines', '(046) 432-1234') }}</p>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Direct Landline & Public Desk</p>
                        </div>
                    </div>

                    <div class="bg-white dark:bg-slate-800/95 rounded-2xl p-6 border border-slate-200/90 dark:border-slate-700/80 shadow-2xs flex items-start gap-4">
                        <div class="w-11 h-11 shrink-0 rounded-xl bg-emerald-50 dark:bg-emerald-950/50 text-emerald-700 dark:text-emerald-400 flex items-center justify-center border border-emerald-100 dark:border-emerald-800/40">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        </div>
                        <div>
                            <p class="font-bold text-slate-900 dark:text-white text-sm">{{ __('Main Health Facility') }}</p>
                            <p class="text-slate-600 dark:text-slate-300 text-sm font-medium mt-0.5">{{ \App\Models\SiteSetting::get('footer_address_line1', 'M.H del Pilar St.') }}, {{ \App\Models\SiteSetting::get('footer_address_line2', 'Silang, Cavite') }}</p>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Municipal Health Complex</p>
                        </div>
                    </div>

                    <div class="bg-white dark:bg-slate-800/95 rounded-2xl p-6 border border-slate-200/90 dark:border-slate-700/80 shadow-2xs flex items-start gap-4">
                        <div class="w-11 h-11 shrink-0 rounded-xl bg-emerald-50 dark:bg-emerald-950/50 text-emerald-700 dark:text-emerald-400 flex items-center justify-center border border-emerald-100 dark:border-emerald-800/40">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <div>
                            <p class="font-bold text-slate-900 dark:text-white text-sm">{{ __('Official Clinic Hours') }}</p>
                            <p class="text-slate-600 dark:text-slate-300 text-sm font-medium mt-0.5">{{ \App\Models\SiteSetting::get('clinic_hours', 'Mon - Fri | 8:00 AM - 5:00 PM') }}</p>
                            <p class="text-xs text-emerald-700 dark:text-emerald-400 font-semibold mt-1">RHU Silang</p>
                        </div>
                    </div>
                </div>

                {{-- Action Banner --}}
                <div class="text-center" data-reveal>
                    <div class="p-8 sm:p-10 max-w-4xl mx-auto">
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