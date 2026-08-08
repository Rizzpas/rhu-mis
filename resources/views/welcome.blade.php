@extends('layouts.app')

@push('head')
    @php
        $heroImage = \App\Models\SiteSetting::get('hero_image', 'assets/images/hero.jpg');
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
             HERO SECTION — Merged hero + carousel
        ============================================================ --}}
        <section class="hero w-full bg-[#FAF9F6] dark:bg-gray-900">
            <div class="mesh"></div>
            <div class="grain"></div>
            <div class="hero-content max-w-7xl mx-auto px-5 sm:px-8 py-14 md:py-20 lg:py-24">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-10 lg:gap-16 items-center">

                    {{-- Left column (formerly right) — Image carousel (sits over dark green) --}}
                    <div class="order-1 lg:order-1" 
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
                        
                        <div class="relative w-full aspect-[4/3] rounded-3xl overflow-hidden shadow-2xl border border-white/20">
                            {{-- Base Image Slide --}}
                            <div class="absolute inset-0 transition-opacity duration-1000" :class="activeSlide === 0 ? 'opacity-100 z-10' : 'opacity-0 z-0'">
                                <img src="https://images.unsplash.com/photo-1519494026892-80bbd2d6fd0d?q=80&w=2000&auto=format&fit=crop" alt="RHU Silang, Cavite" class="w-full h-full object-cover">
                                <div class="absolute inset-0 bg-gradient-to-t from-gray-900/90 via-gray-900/40 to-transparent"></div>
                                <div class="absolute bottom-0 left-0 p-8 sm:p-10 w-full text-white">
                                    <h3 class="font-display text-3xl font-bold mb-2">RHU Silang, Cavite</h3>
                                    <p class="text-gray-300 font-medium">Providing Quality Healthcare for All Citizens</p>
                                </div>
                            </div>

                            {{-- Announcement Slides --}}
                            @foreach($announcements as $index => $announcement)
                            <div class="absolute inset-0 transition-opacity duration-1000 bg-green-900" :class="activeSlide === {{ $index + 1 }} ? 'opacity-100 z-10' : 'opacity-0 z-0'">
                                @if($announcement->image_path)
                                    <img src="{{ Storage::url($announcement->image_path) }}" alt="{{ $announcement->title }}" class="w-full h-full object-cover opacity-60 mix-blend-overlay">
                                @endif
                                <div class="absolute inset-0 bg-gradient-to-t from-gray-900/90 via-gray-900/40 to-transparent"></div>
                                <div class="absolute bottom-0 left-0 p-8 sm:p-10 w-full text-white">
                                    <span class="inline-block px-3 py-1 bg-green-500/20 text-green-300 text-xs font-bold rounded-full mb-3 border border-green-400/30 backdrop-blur-sm uppercase tracking-wider">Announcement</span>
                                    <h3 class="font-display text-2xl sm:text-3xl font-bold mb-3">{{ $announcement->title }}</h3>
                                    <p class="text-gray-300 line-clamp-2 max-w-lg">{{ $announcement->content }}</p>
                                    @if($announcement->url)
                                    <a href="{{ $announcement->url }}" class="inline-flex items-center gap-1.5 mt-4 text-green-400 hover:text-green-300 font-semibold transition-colors">
                                        Learn more <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                    </a>
                                    @endif
                                </div>
                            </div>
                            @endforeach

                            {{-- Carousel Navigation Dots --}}
                            <div class="absolute bottom-5 left-1/2 -translate-x-1/2 flex items-center gap-2 z-20">
                                <template x-for="i in slidesCount">
                                    <button @click="activeSlide = i - 1; stopAutoPlay(); autoPlay()" class="h-1.5 rounded-full transition-all duration-300" :class="activeSlide === i - 1 ? 'w-6 bg-white' : 'w-1.5 bg-white/40 hover:bg-white/70'" aria-label="Go to slide"></button>
                                </template>
                            </div>
                        </div>
                    </div>

                    {{-- Right column (formerly left) — text + CTAs (sits over light/white side) --}}
                    <div class="order-2 lg:order-2">
                        <span class="hero-animate hero-animate-delay-1 inline-flex items-center gap-2 px-3.5 py-1.5 bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300 text-xs font-bold rounded-full mb-5 uppercase tracking-wider">
                            <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd"/></svg>
                            {{ \App\Models\SiteSetting::get('hero_badge_text', 'Trusted Community Healthcare') }}
                        </span>

                        <h1 class="hero-animate hero-animate-delay-2 font-display text-4xl sm:text-5xl lg:text-[3.5rem] font-extrabold text-gray-900 dark:text-white mb-5 tracking-tight" style="line-height: 1.08;">
                            {{ \App\Models\SiteSetting::get('hero_title_line1', 'Accessible') }}
                            <span class="hero-highlight">{{ \App\Models\SiteSetting::get('hero_title_highlight', 'Healthcare') }}</span><br>
                            {{ \App\Models\SiteSetting::get('hero_title_line2', 'for Every Citizen.') }}
                        </h1>

                        <p class="hero-animate hero-animate-delay-3 text-lg text-gray-600 dark:text-gray-300 mb-10 max-w-xl leading-relaxed">
                            {{ \App\Models\SiteSetting::get('hero_description', 'The Rural Health Unit is the primary gateway for medical services in our city. We provide digital triage, scheduling, and diagnostic referrals.') }}
                        </p>

                        {{-- Dual CTAs with microcopy --}}
                        <div class="hero-animate hero-animate-delay-4 flex flex-col sm:flex-row gap-4 sm:gap-5">
                            {{-- Primary: Get Started --}}
                            <div class="flex flex-col items-center sm:items-start">
                                <a href="{{ route('appointment.create') }}" class="btn-cta btn-cta-primary w-full sm:w-auto" id="hero-get-started">
                                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
                                    </svg>
                                    Get Started
                                </a>
                                <span class="text-xs text-gray-500 dark:text-gray-400 mt-2 pl-1">New here? Book your visit</span>
                            </div>

                            {{-- Secondary: Manage Appointment --}}
                            <div class="flex flex-col items-center sm:items-start">
                                <a href="{{ route('appointment.manage') }}" class="btn-cta btn-cta-secondary w-full sm:w-auto" id="hero-manage-appointment">
                                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z"/>
                                    </svg>
                                    Manage Appointment
                                </a>
                                <span class="text-xs text-gray-500 dark:text-gray-400 mt-2 pl-1">Already booked? Check or change it</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- Section Divider: Hero → Announcements --}}
        <div class="max-w-4xl mx-auto px-8">
            <div class="section-divider"><span class="section-divider-dot"></span></div>
        </div>

        {{-- ============================================================
             ANNOUNCEMENTS SECTION — Featured card + grid, capped at 6
        ============================================================ --}}
        @php
            $displayAnnouncements = $announcements->take(6);
            $featured = $displayAnnouncements->first();
            $remainingAnnouncements = $displayAnnouncements->skip(1);
            $totalPublished = \App\Models\Announcement::where('status', 'published')->count();

            // Category tag inference from title/content keywords
            function inferAnnouncementTag($announcement) {
                $text = strtolower($announcement->title . ' ' . strip_tags($announcement->content));
                if (preg_match('/health alert|outbreak|dengue|covid|virus|disease|warning/i', $text)) {
                    return ['label' => 'Health Alert', 'class' => 'tag-health-alert'];
                }
                if (preg_match('/event|celebration|program|fiesta|activity|campaign|drive/i', $text)) {
                    return ['label' => 'Event', 'class' => 'tag-event'];
                }
                if (preg_match('/advisory|notice|schedule|closure|suspend|update|memo/i', $text)) {
                    return ['label' => 'Advisory', 'class' => 'tag-advisory'];
                }
                return ['label' => 'Announcement', 'class' => 'tag-general'];
            }
        @endphp

        <div id="announcements" class="bg-white dark:bg-gray-800/50 px-5 sm:px-8 py-16 sm:py-20">
            <div class="max-w-7xl mx-auto">
                {{-- Section header --}}
                <div class="text-center mb-12" data-reveal>
                    <h2 class="font-display text-3xl sm:text-4xl font-extrabold text-gray-900 dark:text-white tracking-tight">{{ __('Announcements') }}</h2>
                    <p class="text-gray-500 dark:text-gray-400 mt-3 text-lg max-w-lg mx-auto">{{ __('Latest updates and events from your health unit') }}</p>
                    <div class="section-accent mx-auto mt-5"></div>
                </div>

                <div id="announcements-container">
                    @if($featured)
                        {{-- Featured announcement — large card --}}
                        @php
                            $featuredImage = $featured->image_path;
                            if (!$featuredImage && $featured->images->count() > 0) {
                                $featuredImage = $featured->images->first()->image_path;
                            }
                            $featuredTag = inferAnnouncementTag($featured);
                        @endphp
                        <div data-reveal class="mb-8">
                            <a href="{{ route('announcements.show', $featured) }}"
                               class="block bg-white dark:bg-gray-800 rounded-2xl overflow-hidden border border-gray-100 dark:border-gray-700/50 card-hover group">
                                <div class="grid grid-cols-1 md:grid-cols-2">
                                    {{-- Image side --}}
                                    <div class="relative overflow-hidden" style="aspect-ratio: 16/10;">
                                        @if($featuredImage)
                                            @if(\Illuminate\Support\Str::endsWith($featuredImage, '.mp4'))
                                                <video src="{{ asset('uploads/' . $featuredImage) }}" class="w-full h-full object-cover" muted loop autoplay></video>
                                            @else
                                                <img src="{{ asset('uploads/' . $featuredImage) }}" alt="{{ $featured->title }}"
                                                     class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105" loading="lazy">
                                            @endif
                                        @else
                                            <div class="w-full h-full bg-gradient-to-br from-green-50 to-teal-50 dark:from-green-900/20 dark:to-teal-900/20 flex items-center justify-center min-h-[240px]">
                                                <svg class="w-16 h-16 text-green-200 dark:text-green-800" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                                </svg>
                                            </div>
                                        @endif
                                        <div class="absolute top-4 left-4">
                                            <span class="inline-flex items-center gap-1 px-3 py-1.5 {{ $featuredTag['class'] }} text-[11px] font-bold rounded-lg uppercase tracking-wide backdrop-blur-sm">
                                                {{ $featuredTag['label'] }}
                                            </span>
                                        </div>
                                    </div>

                                    {{-- Text side --}}
                                    <div class="p-7 sm:p-10 flex flex-col justify-center">
                                        <span class="inline-flex items-center gap-1.5 text-green-600 dark:text-green-400 text-xs font-semibold uppercase tracking-wider mb-3">
                                            <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm.75-13a.75.75 0 00-1.5 0v5c0 .414.336.75.75.75h4a.75.75 0 000-1.5h-3.25V5z" clip-rule="evenodd"/></svg>
                                            {{ $featured->created_at->diffForHumans() }}
                                        </span>
                                        <h3 class="font-display text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white mb-3 group-hover:text-green-700 dark:group-hover:text-green-400 transition-colors line-clamp-2">
                                            {{ $featured->title }}
                                        </h3>
                                        @if($featured->subheading)
                                            <p class="text-green-700 dark:text-green-400 font-medium text-sm mb-3">{{ $featured->subheading }}</p>
                                        @endif
                                        <p class="text-gray-500 dark:text-gray-400 text-sm leading-relaxed line-clamp-4 mb-6">
                                            {{ Str::limit(strip_tags($featured->content), 240) }}
                                        </p>
                                        <span class="inline-flex items-center gap-2 text-green-600 dark:text-green-400 font-semibold text-sm group-hover:gap-3 transition-all">
                                            {{ __('Read full details') }}
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                                        </span>
                                    </div>
                                </div>
                            </a>
                        </div>
                    @endif

                    {{-- Remaining announcement cards grid --}}
                    @if($remainingAnnouncements->isNotEmpty())
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 sm:gap-7">
                            @foreach($remainingAnnouncements as $index => $event)
                                @php
                                    $cardImage = $event->image_path;
                                    if (!$cardImage && $event->images->count() > 0) {
                                        $cardImage = $event->images->first()->image_path;
                                    }
                                    $tag = inferAnnouncementTag($event);
                                @endphp
                                <div data-reveal style="transition-delay: {{ $index * 80 }}ms">
                                    <a href="{{ route('announcements.show', $event) }}"
                                       class="block bg-white dark:bg-gray-800 rounded-2xl overflow-hidden border border-gray-100 dark:border-gray-700/50 card-hover group h-full">
                                        
                                        {{-- Image container — fixed 16:9 ratio --}}
                                        <div class="relative overflow-hidden" style="aspect-ratio: 16/9;">
                                            @if($cardImage)
                                                @if(\Illuminate\Support\Str::endsWith($cardImage, '.mp4'))
                                                    <video src="{{ asset('uploads/' . $cardImage) }}" class="w-full h-full object-cover" muted loop autoplay></video>
                                                @else
                                                    <img src="{{ asset('uploads/' . $cardImage) }}" alt="{{ $event->title }}"
                                                         class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105" loading="lazy">
                                                @endif
                                            @else
                                                <div class="w-full h-full bg-gradient-to-br from-green-50 to-teal-50 dark:from-green-900/20 dark:to-teal-900/20 flex items-center justify-center">
                                                    <svg class="w-12 h-12 text-green-200 dark:text-green-800" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                            d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                                    </svg>
                                                </div>
                                            @endif
                                            {{-- Category tag --}}
                                            <div class="absolute top-3 left-3">
                                                <span class="inline-block px-2.5 py-1 {{ $tag['class'] }} text-[10px] font-bold rounded-lg uppercase tracking-wide backdrop-blur-sm">{{ $tag['label'] }}</span>
                                            </div>
                                        </div>

                                        {{-- Card body --}}
                                        <div class="p-5 sm:p-6 flex flex-col flex-1">
                                            <div class="flex items-center gap-2 text-xs text-gray-400 dark:text-gray-500 mb-2">
                                                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm.75-13a.75.75 0 00-1.5 0v5c0 .414.336.75.75.75h4a.75.75 0 000-1.5h-3.25V5z" clip-rule="evenodd"/></svg>
                                                {{ $event->created_at->diffForHumans() }}
                                            </div>
                                            <h3 class="font-semibold text-lg text-gray-900 dark:text-white mb-2 group-hover:text-green-700 dark:group-hover:text-green-400 transition-colors line-clamp-2">
                                                {{ $event->title }}
                                            </h3>
                                            <p class="text-gray-500 dark:text-gray-400 text-sm line-clamp-3 leading-relaxed">
                                                {{ Str::limit(strip_tags($event->content), 120) }}
                                            </p>
                                            <div class="mt-auto pt-4">
                                                <span class="inline-flex items-center gap-1.5 text-green-600 dark:text-green-400 font-semibold text-sm group-hover:gap-2.5 transition-all">
                                                    {{ __('Read details') }}
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                                                </span>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                {{-- View All button --}}
                @if($totalPublished > 6)
                    <div class="text-center mt-10" data-reveal>
                        <a href="{{ route('announcements.index') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 rounded-xl font-semibold text-sm hover:bg-gray-200 dark:hover:bg-gray-600 transition min-h-[48px]">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                            {{ __('View All Announcements') }}
                        </a>
                    </div>
                @endif
            </div>
        </div>

        {{-- Section Divider: Announcements → Doctors --}}
        <div class="max-w-4xl mx-auto px-8">
            <div class="section-divider"><span class="section-divider-dot"></span></div>
        </div>

        {{-- ============================================================
             LIVE SCHEDULE SECTION — Doctor carousel with status dots
        ============================================================ --}}
        <div id="schedule" class="max-w-7xl mx-auto px-5 sm:px-8 py-16 sm:py-20">
            <div class="text-center mb-12" data-reveal>
                <h2 class="font-display text-3xl sm:text-4xl font-extrabold text-gray-900 dark:text-white tracking-tight">{{ __("Doctor's Schedule") }}</h2>
                <p class="text-gray-500 dark:text-gray-400 mt-3 text-lg max-w-lg mx-auto">
                    {{ __('Real-time availability of our medical professionals') }}
                </p>
                <div class="section-accent mx-auto mt-5"></div>
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
                        class="absolute left-0 top-1/2 -translate-y-1/2 -translate-x-3 z-10 w-10 h-10 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-full shadow-lg flex items-center justify-center text-gray-600 dark:text-gray-300 hover:bg-green-50 dark:hover:bg-green-900/30 hover:text-green-600 transition-all">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    </button>

                    <div x-ref="doctorScroll" class="flex gap-5 overflow-x-auto pb-4 snap-x snap-mandatory scroll-smooth doctor-scroll">
                        @foreach($doctors as $index => $doctor)
                            @php
                                $displayStatus = $doctor->is_present ? 'Present' : (in_array(strtolower($doctor->status ?? ''), ['seminar']) ? 'Seminar' : 'Out of Office');
                                $statusDotClass = match (strtolower($displayStatus)) {
                                    'present' => 'status-dot-available',
                                    'seminar' => 'status-dot-away',
                                    'out of office' => 'status-dot-offline',
                                    default => 'status-dot-offline',
                                };
                                $statusBadge = match (strtolower($displayStatus)) {
                                    'present' => 'bg-green-100 text-green-800 dark:bg-green-900/40 dark:text-green-300',
                                    'seminar' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/40 dark:text-yellow-300',
                                    'out of office' => 'bg-red-100 text-red-800 dark:bg-red-900/40 dark:text-red-300',
                                    default => 'bg-gray-100 dark:bg-gray-700 dark:text-gray-300',
                                };
                                $roleLabel = match (true) {
                                    str_contains($doctor->role ?? '', 'pedia') => 'Pediatrics',
                                    str_contains($doctor->role ?? '', 'doctor') => 'General Physician',
                                    default => 'Medical Staff',
                                };
                            @endphp
                            <div class="shrink-0 w-[320px] sm:w-[340px] snap-start" data-reveal style="transition-delay: {{ $index * 60 }}ms">
                                <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700/50 p-6 card-hover h-full">
                                    <div class="flex items-start gap-4 mb-4">
                                        {{-- Avatar with status dot --}}
                                        <div class="relative shrink-0">
                                            <div class="h-14 w-14 rounded-full overflow-hidden bg-gradient-to-br from-green-100 to-teal-50 dark:from-green-900/30 dark:to-teal-900/20 flex items-center justify-center border-2 border-white dark:border-gray-700 shadow-sm">
                                                @if($doctor->avatar_url)
                                                    <img src="{{ $doctor->avatar_url }}" alt="{{ $doctor->name }}" class="w-full h-full object-cover" loading="lazy">
                                                @else
                                                    <span class="text-green-700 dark:text-green-400 font-bold text-lg">{{ $doctor->initials }}</span>
                                                @endif
                                            </div>
                                            <span class="status-dot {{ $statusDotClass }} absolute -bottom-0.5 -right-0.5 border-2 border-white dark:border-gray-800"></span>
                                        </div>
                                        {{-- Name & role --}}
                                        <div class="min-w-0 flex-1">
                                            <h3 class="text-lg font-bold text-gray-900 dark:text-white truncate">{{ $doctor->formatted_name }}</h3>
                                            <p class="text-green-600 dark:text-green-400 font-medium text-sm">{{ $roleLabel }}</p>
                                        </div>
                                    </div>

                                    {{-- Status badge + schedule --}}
                                    <div class="flex items-center gap-2 mb-3">
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-semibold uppercase {{ $statusBadge }}">{{ $displayStatus }}</span>
                                        @if(strtolower($displayStatus) === 'present')
                                            <span class="text-[10px] text-green-600 dark:text-green-400 font-medium">• Available Now</span>
                                        @endif
                                    </div>

                                    <p class="text-gray-500 dark:text-gray-400 text-sm line-clamp-2">
                                        @if($doctor->formatted_schedule)
                                            <svg class="w-3.5 h-3.5 inline-block mr-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                            {{ $doctor->formatted_schedule }}
                                        @else
                                            Available for general consultation.
                                        @endif
                                    </p>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- Scroll right button --}}
                    <button x-show="canRight" x-transition @click="goRight()" aria-label="Scroll right"
                        class="absolute right-0 top-1/2 -translate-y-1/2 translate-x-3 z-10 w-10 h-10 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-full shadow-lg flex items-center justify-center text-gray-600 dark:text-gray-300 hover:bg-green-50 dark:hover:bg-green-900/30 hover:text-green-600 transition-all">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </button>
                </div>
            @else
                {{-- Standard grid for 3 or fewer doctors --}}
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($doctors as $index => $doctor)
                        @php
                            $displayStatus = $doctor->is_present ? 'Present' : (in_array(strtolower($doctor->status ?? ''), ['seminar']) ? 'Seminar' : 'Out of Office');
                            $statusDotClass = match (strtolower($displayStatus)) {
                                'present' => 'status-dot-available',
                                'seminar' => 'status-dot-away',
                                'out of office' => 'status-dot-offline',
                                default => 'status-dot-offline',
                            };
                            $statusBadge = match (strtolower($displayStatus)) {
                                'present' => 'bg-green-100 text-green-800 dark:bg-green-900/40 dark:text-green-300',
                                'seminar' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/40 dark:text-yellow-300',
                                'out of office' => 'bg-red-100 text-red-800 dark:bg-red-900/40 dark:text-red-300',
                                default => 'bg-gray-100 dark:bg-gray-700 dark:text-gray-300',
                            };
                            $roleLabel = match (true) {
                                str_contains($doctor->role ?? '', 'pedia') => 'Pediatrics',
                                str_contains($doctor->role ?? '', 'doctor') => 'General Physician',
                                default => 'Medical Staff',
                            };
                        @endphp
                        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700/50 p-6 card-hover"
                             data-reveal style="transition-delay: {{ $index * 60 }}ms">
                            <div class="flex items-start gap-4 mb-4">
                                <div class="relative shrink-0">
                                    <div class="h-14 w-14 rounded-full overflow-hidden bg-gradient-to-br from-green-100 to-teal-50 dark:from-green-900/30 dark:to-teal-900/20 flex items-center justify-center border-2 border-white dark:border-gray-700 shadow-sm">
                                        @if($doctor->avatar_url)
                                            <img src="{{ $doctor->avatar_url }}" alt="{{ $doctor->name }}" class="w-full h-full object-cover" loading="lazy">
                                        @else
                                            <span class="text-green-700 dark:text-green-400 font-bold text-lg">{{ $doctor->initials }}</span>
                                        @endif
                                    </div>
                                    <span class="status-dot {{ $statusDotClass }} absolute -bottom-0.5 -right-0.5 border-2 border-white dark:border-gray-800"></span>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <h3 class="text-lg font-bold text-gray-900 dark:text-white truncate">{{ $doctor->formatted_name }}</h3>
                                    <p class="text-green-600 dark:text-green-400 font-medium text-sm">{{ $roleLabel }}</p>
                                </div>
                            </div>

                            <div class="flex items-center gap-2 mb-3">
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-semibold uppercase {{ $statusBadge }}">{{ $displayStatus }}</span>
                                @if(strtolower($displayStatus) === 'present')
                                    <span class="text-[10px] text-green-600 dark:text-green-400 font-medium">• Available Now</span>
                                @endif
                            </div>

                            <p class="text-gray-500 dark:text-gray-400 text-sm line-clamp-2">
                                @if($doctor->formatted_schedule)
                                    <svg class="w-3.5 h-3.5 inline-block mr-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    {{ $doctor->formatted_schedule }}
                                @else
                                    Available for general consultation.
                                @endif
                            </p>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Section Divider: Doctors → Closing --}}
        <div class="max-w-4xl mx-auto px-8">
            <div class="section-divider"><span class="section-divider-dot"></span></div>
        </div>

        {{-- ============================================================
             CLOSING SECTION — Services overview + CTA
        ============================================================ --}}
        <div class="bg-white dark:bg-gray-800/50 px-5 sm:px-8 py-16 sm:py-20">
            <div class="max-w-7xl mx-auto">

                {{-- Quick services grid --}}
                <div class="text-center mb-12" data-reveal>
                    <h2 class="font-display text-3xl sm:text-4xl font-extrabold text-gray-900 dark:text-white tracking-tight">{{ __('Our Services') }}</h2>
                    <p class="text-gray-500 dark:text-gray-400 mt-3 text-lg max-w-lg mx-auto">{{ __('Comprehensive healthcare for every stage of life') }}</p>
                    <div class="section-accent mx-auto mt-5"></div>
                </div>

                <div class="services-bento mb-14" data-reveal>
                    {{-- 1. Main Health Center (2x2) --}}
                    <div class="bento-card card-large p-6 flex flex-col" style="transition-delay: 0ms">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="w-12 h-12 rounded-xl bg-green-50 dark:bg-green-900/30 flex items-center justify-center text-green-600 dark:text-green-400 shrink-0">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                            </div>
                            <div>
                                <h3 class="font-display text-xl font-bold text-gray-900 dark:text-white">Main Health Center</h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400">General consultations & primary care</p>
                            </div>
                        </div>
                        
                        {{-- Realistic Mini UI Preview: Appointment Slots + Overlapping Chip --}}
                        <div class="flex-1 bg-gray-50/50 dark:bg-gray-800/30 rounded-2xl border border-gray-100 dark:border-gray-700/50 p-4 relative mt-2 mb-4 overflow-hidden group">
                            <div class="text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider mb-3">Today's Availability</div>
                            <div class="space-y-2">
                                <div class="bg-white dark:bg-gray-800 p-2.5 rounded-lg border border-gray-100 dark:border-gray-700 flex justify-between items-center shadow-sm">
                                    <span class="text-sm font-medium text-gray-700 dark:text-gray-200">09:00 AM</span>
                                    <span class="text-xs font-bold text-green-600 bg-green-50 dark:bg-green-900/20 px-2 py-0.5 rounded text-center w-16">OPEN</span>
                                </div>
                                <div class="bg-white dark:bg-gray-800 p-2.5 rounded-lg border border-gray-100 dark:border-gray-700 flex justify-between items-center shadow-sm">
                                    <span class="text-sm font-medium text-gray-700 dark:text-gray-200">10:30 AM</span>
                                    <span class="text-xs font-bold text-gray-400 bg-gray-50 dark:bg-gray-700 px-2 py-0.5 rounded text-center w-16">BOOKED</span>
                                </div>
                                <div class="bg-white dark:bg-gray-800 p-2.5 rounded-lg border border-gray-100 dark:border-gray-700 flex justify-between items-center shadow-sm opacity-50">
                                    <span class="text-sm font-medium text-gray-700 dark:text-gray-200">01:00 PM</span>
                                    <span class="text-xs font-bold text-green-600 bg-green-50 dark:bg-green-900/20 px-2 py-0.5 rounded text-center w-16">OPEN</span>
                                </div>
                            </div>
                            
                            {{-- Overlapping depth chip --}}
                            <div class="absolute -bottom-4 -right-4 bg-green-600 text-white p-4 pt-5 pl-5 rounded-tl-3xl shadow-lg transition-transform group-hover:-translate-y-2 group-hover:-translate-x-2">
                                <div class="flex items-center gap-2">
                                    <span class="font-bold text-sm">Book Now</span>
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                </div>
                            </div>
                        </div>
                        
                        <a href="{{ route('units.index') }}" class="inline-flex items-center gap-1.5 text-sm font-bold text-green-600 hover:text-green-700 dark:text-green-400 dark:hover:text-green-300 mt-auto">
                            View Guide & Details <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </a>
                    </div>

                    {{-- 2. Lying-in Clinic (2x1) --}}
                    <div class="bento-card card-wide p-6 flex flex-col justify-between" style="transition-delay: 50ms">
                        <div class="flex items-start justify-between mb-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-green-50 dark:bg-green-900/30 flex items-center justify-center text-green-600 dark:text-green-400 shrink-0">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z"/></svg>
                                </div>
                                <div>
                                    <h3 class="font-display text-lg font-bold text-gray-900 dark:text-white">Lying-in Clinic</h3>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">24/7 Maternal care services</p>
                                </div>
                            </div>
                            <a href="{{ route('units.index') }}" class="shrink-0 text-gray-400 hover:text-green-600 dark:hover:text-green-400 transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                            </a>
                        </div>
                        
                        {{-- Realistic Mini UI Preview: Calendar Row --}}
                        <div class="bg-gray-50/50 dark:bg-gray-800/30 rounded-xl border border-gray-100 dark:border-gray-700/50 p-3 flex justify-between items-center px-4">
                            @foreach(['Mon','Tue','Wed','Thu','Fri'] as $day)
                                <div class="text-center">
                                    <div class="text-[10px] text-gray-400 uppercase font-bold mb-1">{{ $day }}</div>
                                    <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold {{ $loop->first ? 'bg-green-600 text-white shadow-md' : 'bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-300 border border-gray-100 dark:border-gray-700' }}">
                                        {{ 12 + $loop->index }}
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- 3. Dental Clinic (1x2) --}}
                    <div class="bento-card card-tall p-6 flex flex-col text-center" style="transition-delay: 100ms">
                        <div class="w-12 h-12 rounded-xl bg-green-50 dark:bg-green-900/30 flex items-center justify-center text-green-600 dark:text-green-400 shrink-0 mx-auto mb-4">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15.182 15.182a4.5 4.5 0 01-6.364 0M21 12a9 9 0 11-18 0 9 9 0 0118 0zM9.75 9.75c0 .414-.168.75-.375.75S9 10.164 9 9.75 9.168 9 9.375 9s.375.336.375.75zm-.375 0h.008v.015h-.008V9.75zm5.625 0c0 .414-.168.75-.375.75s-.375-.336-.375-.75.168-.75.375-.75.375.336.375.75zm-.375 0h.008v.015h-.008V9.75z"/></svg>
                        </div>
                        <h3 class="font-display text-lg font-bold text-gray-900 dark:text-white mb-1">Dental Clinic</h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mb-6">Oral health & extractions</p>
                        
                        {{-- Realistic Mini UI Preview: Tooth Status --}}
                        <div class="flex-1 bg-gray-50/50 dark:bg-gray-800/30 rounded-xl border border-gray-100 dark:border-gray-700/50 p-4 flex flex-col items-center justify-center mb-6">
                            <svg class="w-14 h-14 text-gray-300 dark:text-gray-600 mb-4" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2C8.5 2 6 4.5 6 8.5c0 1.5.5 3 1.5 4l1.5 4.5c.3 1 .8 1.5 1.5 1.5.7 0 1-.5 1.5-1.5V14h0v3c.5 1 .8 1.5 1.5 1.5.7 0 1.2-.5 1.5-1.5l1.5-4.5c1-1 1.5-2.5 1.5-4C18 4.5 15.5 2 12 2zm0 9H9.5c-.3 0-.5-.2-.5-.5s.2-.5.5-.5h2.5c.3 0 .5.2.5.5s-.2.5-.5.5z"/>
                            </svg>
                            <div class="inline-flex items-center gap-1.5 bg-green-50 dark:bg-green-900/30 px-3 py-1.5 rounded-full">
                                <div class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></div>
                                <span class="text-[10px] font-bold text-green-700 dark:text-green-400 uppercase tracking-wider">5 Slots Open</span>
                            </div>
                        </div>
                        
                        <a href="{{ route('units.index') }}" class="inline-flex items-center justify-center gap-1 text-xs font-bold text-green-600 hover:text-green-700 dark:text-green-400 transition-colors mt-auto uppercase tracking-wider">
                            View Details <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </a>
                    </div>

                    {{-- 4. TB DOTS Facility (1x2) --}}
                    <div class="bento-card card-tall p-6 flex flex-col text-center" style="transition-delay: 150ms">
                        <div class="w-12 h-12 rounded-xl bg-green-50 dark:bg-green-900/30 flex items-center justify-center text-green-600 dark:text-green-400 shrink-0 mx-auto mb-4">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/></svg>
                        </div>
                        <h3 class="font-display text-lg font-bold text-gray-900 dark:text-white mb-1">TB DOTS</h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mb-6">Tuberculosis treatment</p>
                        
                        {{-- Realistic Mini UI Preview: Treatment Progress --}}
                        <div class="flex-1 bg-gray-50/50 dark:bg-gray-800/30 rounded-xl border border-gray-100 dark:border-gray-700/50 p-4 flex flex-col justify-center text-left mb-6">
                            <div class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-2">Medication Stock</div>
                            <div class="flex items-end gap-1 mb-1">
                                <span class="text-2xl font-bold text-gray-900 dark:text-white leading-none">94</span>
                                <span class="text-xs font-medium text-gray-500 mb-0.5">%</span>
                            </div>
                            <div class="w-full h-1.5 bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden mt-1">
                                <div class="h-full bg-green-500 w-[94%] rounded-full"></div>
                            </div>
                            <p class="text-[10px] text-gray-500 mt-3 flex items-center gap-1"><svg class="w-3 h-3 text-green-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg> Resupply arrived</p>
                        </div>
                        
                        <a href="{{ route('units.index') }}" class="inline-flex items-center justify-center gap-1 text-xs font-bold text-green-600 hover:text-green-700 dark:text-green-400 transition-colors mt-auto uppercase tracking-wider">
                            View Details <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </a>
                    </div>

                    {{-- 5. Animal Bite Center (2x1) --}}
                    <div class="bento-card card-wide p-6 flex flex-col justify-between" style="transition-delay: 200ms">
                        <div class="flex items-start justify-between mb-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-green-50 dark:bg-green-900/30 flex items-center justify-center text-green-600 dark:text-green-400 shrink-0">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 12a2.25 2.25 0 00-2.25-2.25H15a3 3 0 11-6 0H5.25A2.25 2.25 0 003 12m18 0v6a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 18v-6m18 0V9M3 12V9m18 0a2.25 2.25 0 00-2.25-2.25H5.25A2.25 2.25 0 003 9m18 0V6a2.25 2.25 0 00-2.25-2.25H5.25A2.25 2.25 0 003 6v3"/></svg>
                                </div>
                                <div>
                                    <h3 class="font-display text-lg font-bold text-gray-900 dark:text-white">Animal Bite Center</h3>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">Rabies vaccination program</p>
                                </div>
                            </div>
                            <a href="{{ route('units.index') }}" class="shrink-0 text-gray-400 hover:text-green-600 dark:hover:text-green-400 transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                            </a>
                        </div>
                        
                        {{-- Realistic Mini UI Preview: Vaccine Chip --}}
                        <div class="bg-gray-50/50 dark:bg-gray-800/30 rounded-xl border border-gray-100 dark:border-gray-700/50 p-3 px-4 flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-white dark:bg-gray-800 shadow-sm flex items-center justify-center border border-gray-100 dark:border-gray-700">
                                    <span class="text-green-600"><svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v3.586L7.707 9.293a1 1 0 00-1.414 1.414l3 3a1 1 0 001.414 0l3-3a1 1 0 00-1.414-1.414L11 10.586V7z" clip-rule="evenodd"/></svg></span>
                                </div>
                                <div>
                                    <div class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-0.5">Inventory Status</div>
                                    <div class="text-sm font-semibold text-gray-900 dark:text-white">Anti-Rabies Shots In Stock</div>
                                </div>
                            </div>
                            <div class="px-2.5 py-1 rounded-md bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400 text-[10px] uppercase font-bold border border-green-200 dark:border-green-800 tracking-wider">
                                AVAILABLE
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Trust badges row --}}
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 sm:gap-6 mb-14" data-reveal>
                    <div class="flex items-center gap-4 bg-green-50/50 dark:bg-green-900/10 rounded-2xl p-5 border border-green-100/50 dark:border-green-800/20">
                        <div class="w-11 h-11 shrink-0 rounded-xl bg-green-100 dark:bg-green-900/40 flex items-center justify-center text-green-600 dark:text-green-400">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-900 dark:text-white text-sm">{{ __('Emergency Hotline') }}</p>
                            <p class="text-green-700 dark:text-green-400 text-sm font-medium">{{ \App\Models\SiteSetting::get('emergency_hotlines', '911 | (046) 432-1234') }}</p>
                        </div>
                    </div>

                    <div class="flex items-center gap-4 bg-green-50/50 dark:bg-green-900/10 rounded-2xl p-5 border border-green-100/50 dark:border-green-800/20">
                        <div class="w-11 h-11 shrink-0 rounded-xl bg-green-100 dark:bg-green-900/40 flex items-center justify-center text-green-600 dark:text-green-400">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-900 dark:text-white text-sm">{{ __('Location') }}</p>
                            <p class="text-gray-500 dark:text-gray-400 text-sm">{{ \App\Models\SiteSetting::get('footer_address_line1', 'M.H del Pilar St.') }}, {{ \App\Models\SiteSetting::get('footer_address_line2', 'Silang, Cavite') }}</p>
                        </div>
                    </div>

                    <div class="flex items-center gap-4 bg-green-50/50 dark:bg-green-900/10 rounded-2xl p-5 border border-green-100/50 dark:border-green-800/20">
                        <div class="w-11 h-11 shrink-0 rounded-xl bg-green-100 dark:bg-green-900/40 flex items-center justify-center text-green-600 dark:text-green-400">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-900 dark:text-white text-sm">{{ __('Clinic Hours') }}</p>
                            <p class="text-gray-500 dark:text-gray-400 text-sm">{{ \App\Models\SiteSetting::get('clinic_hours', 'Mon - Fri | 8:00 AM - 5:00 PM') }}</p>
                        </div>
                    </div>
                </div>

                {{-- Final CTA --}}
                <div class="text-center" data-reveal>
                    <div class="inline-block bg-gradient-to-r from-green-50 to-teal-50 dark:from-green-900/20 dark:to-teal-900/10 rounded-2xl p-8 sm:p-10 border border-green-100/50 dark:border-green-800/20">
                        <h3 class="font-display text-2xl font-bold text-gray-900 dark:text-white mb-2">{{ __('Ready to visit?') }}</h3>
                        <p class="text-gray-500 dark:text-gray-400 text-sm mb-6 max-w-md mx-auto">{{ __('Book your appointment online and skip the wait. Walk-ins are also welcome during clinic hours.') }}</p>
                        <div class="flex flex-col sm:flex-row gap-3 justify-center">
                            <a href="{{ route('appointment.create') }}" class="btn-cta btn-cta-primary" id="closing-get-started">
                                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                                Book an Appointment
                            </a>
                            <a href="{{ route('units.index') }}" class="btn-cta btn-cta-secondary" id="closing-explore">
                                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21m-3.75 7.5h.008v.008h-.008v-.008zm0 3h.008v.008h-.008v-.008z"/></svg>
                                Explore Facilities
                            </a>
                        </div>
                    </div>
                </div>

            </div>
        </div>

    </div>
@endsection