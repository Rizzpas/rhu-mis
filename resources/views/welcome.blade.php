@extends('layouts.app')

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
                                                            // Fetch the current page content silently
                                                            fetch(window.location.href)
                                                                .then(response => response.text())
                                                                .then(html => {
                                                                    let parser = new DOMParser();
                                                                    let doc = parser.parseFromString(html, 'text/html');

                                                                    // Update Announcements Grid
                                                                    let newAnnouncements = doc.querySelector('#announcements-container');
                                                                    let currentAnnouncements = document.querySelector('#announcements-container');
                                                                    if (newAnnouncements && currentAnnouncements) {
                                                                        currentAnnouncements.innerHTML = newAnnouncements.innerHTML;
                                                                    }

                                                                    // Update Schedule Grid (in case doctors status changed)
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
                                                            }, 5000); // Poll every 5 seconds
                                                        }
                                                    }" x-init="init()">
        <!-- Announcement Carousel -->
        <section class="w-full pt-5 pb-12 relative overflow-hidden" 
                 x-data="{ 
                    activeSlide: 0,
                    slidesCount: {{ count($announcements) + 1 }},
                    nextSlide() { this.activeSlide = (this.activeSlide + 1) % this.slidesCount },
                    prevSlide() { this.activeSlide = (this.activeSlide - 1 + this.slidesCount) % this.slidesCount },
                    autoPlay() { 
                        this.interval = setInterval(() => this.nextSlide(), 6000);
                    },
                    stopAutoPlay() { clearInterval(this.interval); }
                 }" x-init="autoPlay()">
            <div class="max-w-7xl mx-auto px-4 md:px-8 relative z-10">
                <div class="w-full shadow-lg flex flex-col relative overflow-hidden group">
                    
                    <!-- Slides Wrapper -->
                    <div class="relative h-[250px] md:h-[450px] w-full overflow-hidden rounded-t-sm">
                        
                        <!-- Slide 1: Main Hero -->
                        <div x-show="activeSlide === 0" 
                             x-transition:enter="transition ease-out duration-500"
                             x-transition:enter-start="opacity-0 transform translate-x-8"
                             x-transition:enter-end="opacity-100 transform translate-x-0"
                             class="absolute inset-0">
                             @php
                                $heroImage = \App\Models\SiteSetting::get('hero_image', 'assets/images/hero.jpg');
                                $heroImageUrl = asset($heroImage);
                             @endphp
                            <img src="{{ $heroImageUrl }}" alt="RHU Main Hero"
                                 class="w-full h-full object-cover">
                            <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/30 to-black/10 z-[1]"></div>
                            <div class="absolute inset-0 flex items-center justify-center z-[2]">
                                <div class="text-center text-white px-4">
                                    <h2 class="text-3xl md:text-5xl font-black mb-2 drop-shadow-lg">{{ \App\Models\SiteSetting::get('carousel_hero_title', 'RHU Silang, Cavite') }}</h2>
                                    <p class="text-sm md:text-lg font-medium drop-shadow-md">{{ \App\Models\SiteSetting::get('carousel_hero_subtitle', 'Providing Quality Healthcare for All Citizens') }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Dynamic Announcement Slides -->
                        @foreach($announcements as $index => $event)
                            <div x-show="activeSlide === {{ $index + 1 }}" 
                                 x-transition:enter="transition ease-out duration-500"
                                 x-transition:enter-start="opacity-0 transform translate-x-8"
                                 x-transition:enter-end="opacity-100 transform translate-x-0"
                                 class="absolute inset-0">
                                 <a href="{{ route('announcements.show', $event) }}" class="block w-full h-full relative">
                                    @php
                                        $imagePath = $event->image_path;
                                        // Fallback to first gallery image if main image is missing
                                        if (!$imagePath && $event->images->count() > 0) {
                                            $imagePath = $event->images->first()->image_path;
                                        }
                                    @endphp

                                    @if($imagePath)
                                        @if(\Illuminate\Support\Str::endsWith($imagePath, '.mp4'))
                                            <video src="{{ asset('uploads/' . $imagePath) }}" class="w-full h-full object-cover" muted loop autoplay></video>
                                        @else
                                            <!-- Blurred background fill -->
                                            <img src="{{ asset('uploads/' . $imagePath) }}" alt="" aria-hidden="true"
                                                 class="absolute inset-0 w-full h-full object-cover scale-110 blur-2xl opacity-80">
                                            <!-- Actual full image -->
                                            <img src="{{ asset('uploads/' . $imagePath) }}" alt="{{ $event->title }}"
                                                 class="relative w-full h-full object-contain z-[1]">
                                        @endif
                                    @else
                                        <div class="w-full h-full bg-gradient-to-br from-teal-500 to-green-600 flex items-center justify-center p-10 text-center">
                                            <div class="max-w-md">
                                                <svg class="w-20 h-20 text-white/30 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"></path></svg>
                                                <h4 class="text-white text-2xl md:text-4xl font-black drop-shadow-md mb-2">{{ $event->title }}</h4>
                                                <p class="text-white/80 text-sm font-medium line-clamp-2">{{ strip_tags($event->content) }}</p>
                                            </div>
                                        </div>
                                    @endif
                                    
                                    <!-- Overlay Info -->
                                    <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/80 to-transparent p-6 md:p-10 z-10">
                                        <span class="inline-block px-2 py-1 bg-green-600 text-white text-[10px] font-bold rounded mb-2 uppercase tracking-tighter">Announcement</span>
                                        <h3 class="text-xl md:text-3xl font-bold text-white mb-1 line-clamp-1">{{ $event->title }}</h3>
                                        <p class="text-gray-200 text-xs md:text-sm line-clamp-2 max-w-2xl">{{ strip_tags($event->content) }}</p>
                                    </div>
                                </a>
                            </div>
                        @endforeach
                    </div>

                    <!-- Navigation Arrows -->
                    <button @click="prevSlide" class="absolute left-4 top-1/2 -translate-y-1/2 bg-dark/50 hover:bg-dark/40 backdrop-blur-sm text-white p-2 rounded-full opacity-0 group-hover:opacity-100 transition-opacity z-20">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                    </button>
                    <button @click="nextSlide" class="absolute right-4 top-1/2 -translate-y-1/2 bg-dark/50 hover:bg-dark/40 backdrop-blur-sm text-white p-2 rounded-full opacity-0 group-hover:opacity-100 transition-opacity z-20">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                    </button>

                    <!-- Indicators -->
                    <div class="absolute bottom-16 left-0 right-0 flex justify-center gap-2 z-20">
                        <template x-for="i in Array.from({length: slidesCount}, (_, i) => i)" :key="i">
                            <button @click="activeSlide = i" 
                                    class="h-1.5 transition-all duration-300 rounded-full"
                                    :class="activeSlide === i ? 'w-8 bg-white' : 'w-2 bg-white/50 hover:bg-white/50'"></button>
                        </template>
                    </div>
                    
                    <!-- Marquee -->
                    <div class="bg-[#13b351] text-white py-3 px-4 md:px-6 flex items-center gap-3 font-medium rounded-sm mt-4">
                        <marquee class="text-sm tracking-wide">
                            @foreach($announcements as $index => $ann)
                                <span class="font-bold">{{ $ann->title }}</span>@if($ann->subheading) <span class="opacity-80">- {{ $ann->subheading }}</span> @endif
                                @if(!$loop->last)
                                    <span class="mx-12 opacity-50 font-light">|</span>
                                @endif
                            @endforeach
                        </marquee>
                    </div>
                </div>
            </div>
        </section>

        <!-- Hero Section -->
        <section class="w-full py-54 relative overflow-hidden">
            <div class="max-w-7xl mx-auto px-8 grid grid-cols-1 md:grid-cols-2 gap-12 items-center relative z-10">
                <div>
                    <span
                        class="inline-block px-3 py-1 bg-green-200 border-2 border-green-700 text-green-800 dark:text-green-800 text-xs font-black rounded-full mb-4 uppercase tracking-wider">{{ \App\Models\SiteSetting::get('hero_badge_text', 'Welcome to RHU Portal') }}</span>
                    <h2 class="text-5xl leading-13 font-black mb-6 text-gray-900 dark:text-white">{{ \App\Models\SiteSetting::get('hero_title_line1', 'Accessible') }}
                        <span class="text-green-500 font-bold">{{ \App\Models\SiteSetting::get('hero_title_highlight', 'Healthcare') }}</span><br>{{ \App\Models\SiteSetting::get('hero_title_line2', 'for Every Citizen.') }}
                    </h2>
                    <p class="text-gray-800 dark:text-gray-300 mb-8 text-xl">{{ \App\Models\SiteSetting::get('hero_description', 'The Rural Health Unit is the primary gateway for medical services in our city. We provide digital triage, scheduling, and diagnostic referrals.') }}</p>
                    <div class="flex gap-4">
                        <a href="{{ route('appointment.create') }}"
                            class="bg-green-600 text-white px-6 py-2 rounded font-medium hover:bg-green-700 transition inline-block">Get
                            Started</a>
                        <a href="{{ route('appointment.manage') }}"
                            class="bg-transparent border border-green-600 text-green-600 px-6 py-2 rounded font-medium hover:bg-green-50 dark:bg-green-900/10 dark:hover:bg-slate-800 transition inline-block">
                            Manage Appointment
                        </a>
                    </div>
                </div>
                <div
                    class="bg-white dark:bg-gray-800 aspect-video hidden md:flex items-center justify-center relative shadow-lg">
                    @php
                        $heroImage = \App\Models\SiteSetting::get('hero_image', 'assets/images/hero.jpg');
                        $heroImageUrl = asset($heroImage);
                    @endphp
                    <img src="{{ $heroImageUrl }}" alt="Rural Health Unit Hero Image"
                        class="w-full h-full object-cover rounded-sm">
                </div>
            </div>
        </section>

        <!-- Announcements Section -->
        <div id="announcements" class="bg-white dark:bg-slate-800/80 px-4 sm:px-6 lg:px-8 py-16">
            <div class="max-w-7xl mx-auto">
                <div class="text-center mb-12">
                    <h2 class="text-3xl font-bold text-gray-900 dark:text-white">{{ __('Announcement') }}</h2>
                    <p class="text-gray-500 dark:text-gray-400 mt-2">{{ __('Latest updates and events from your PHO') }}</p>
                    <div class="h-1 w-50 bg-green-600 mx-auto mt-4 mb-12 rounded-full"></div>
                </div>

                <div class="relative" x-data="{
                    scrollContainer: null,
                    canScrollLeft: false,
                    canScrollRight: false,
                    init() {
                        this.scrollContainer = this.$refs.announcementsScroll;
                        this.checkScroll();
                        this.scrollContainer.addEventListener('scroll', () => this.checkScroll());
                        window.addEventListener('resize', () => this.checkScroll());
                    },
                    checkScroll() {
                        if (!this.scrollContainer) return;
                        this.canScrollLeft = this.scrollContainer.scrollLeft > 10;
                        this.canScrollRight = this.scrollContainer.scrollLeft < (this.scrollContainer.scrollWidth - this.scrollContainer.clientWidth - 10);
                    },
                    scrollLeft() { this.scrollContainer.scrollBy({ left: -370, behavior: 'smooth' }); },
                    scrollRight() { this.scrollContainer.scrollBy({ left: 370, behavior: 'smooth' }); }
                }">
                    <!-- Scroll Left Button -->
                    <button x-show="canScrollLeft" x-transition @click="scrollLeft()"
                        class="absolute left-0 top-1/2 -translate-y-1/2 -translate-x-4 z-10 w-10 h-10 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-full shadow-lg flex items-center justify-center text-gray-600 dark:text-gray-300 hover:bg-green-50 dark:hover:bg-green-900/30 hover:text-green-600 transition-all">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                    </button>

                    <div id="announcements-container" x-ref="announcementsScroll"
                        class="flex items-stretch overflow-x-auto gap-6 pb-8 snap-x snap-mandatory scroll-smooth"
                        style="scrollbar-width: none; -ms-overflow-style: none;">
                        @foreach($announcements as $event)
                            @php
                                $cardImage = $event->image_path;
                                if (!$cardImage && $event->images->count() > 0) {
                                    $cardImage = $event->images->first()->image_path;
                                }
                            @endphp
                            <div class="shrink-0 w-[300px] md:w-[350px] snap-start flex">
                                <a href="{{ route('announcements.show', $event) }}"
                                    class="w-full bg-white dark:bg-gray-800 rounded-2xl overflow-hidden shadow-sm border border-gray-100 dark:border-gray-700 transition hover:shadow-md flex flex-col group">
                                    @if($cardImage)
                                        <div class="h-48 overflow-hidden relative shrink-0">
                                            @if(\Illuminate\Support\Str::endsWith($cardImage, '.mp4'))
                                                <video src="{{ asset('uploads/' . $cardImage) }}" class="w-full h-full object-cover" muted
                                                    loop autoplay></video>
                                            @else
                                                <img src="{{ asset('uploads/' . $cardImage) }}" alt="{{ $event->title }}"
                                                    class="w-full h-full object-cover transition duration-500 group-hover:scale-105">
                                            @endif
                                        </div>
                                    @else
                                        <div class="h-48 bg-green-50 dark:bg-green-900/30 flex items-center justify-center shrink-0">
                                            <svg class="w-12 h-12 text-green-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z">
                                                </path>
                                            </svg>
                                        </div>
                                    @endif
                                    <div class="p-6 flex flex-col flex-1">
                                        <h3
                                            class="font-bold text-xl text-gray-900 dark:text-white mb-2 group-hover:text-green-700 dark:text-green-400 transition">
                                            {{ $event->title }}
                                        </h3>
                                        <p class="text-gray-600 dark:text-gray-400 line-clamp-3 text-sm">
                                            {{ Str::limit(strip_tags($event->content), 150) }}
                                        </p>
                                        <div class="mt-auto pt-4">
                                            <span
                                                class="inline-block text-green-600 font-bold text-sm tracking-wide group-hover:underline">{{ __('Read full details') }}
                                                &rarr;</span>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        @endforeach
                    </div>

                    <!-- Scroll Right Button -->
                    <button x-show="canScrollRight" x-transition @click="scrollRight()"
                        class="absolute right-0 top-1/2 -translate-y-1/2 translate-x-4 z-10 w-10 h-10 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-full shadow-lg flex items-center justify-center text-gray-600 dark:text-gray-300 hover:bg-green-50 dark:hover:bg-green-900/30 hover:text-green-600 transition-all">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                    </button>
                </div>

                <style>
                    #announcements-container::-webkit-scrollbar {
                        display: none;
                    }
                </style>
            </div>
        </div>

        <!-- Live Schedule Section -->
        <div id="schedule" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 rounded-3xl my-12">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-gray-900 dark:text-white">{{ __("Doctor's Schedule") }}</h2>
                <p class="text-gray-500 dark:text-gray-400 mt-2">
                    {{ __('Real-time availability of our medical professionals') }}
                </p>
                <div class="h-1 w-50 bg-green-600 mx-auto mt-4 mb-12 rounded-full"></div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($doctors as $doctor)
                    @php
                        $displayStatus = $doctor->is_present ? 'Present' : (in_array(strtolower($doctor->status), ['seminar']) ? 'Seminar' : 'Out of Office');
                        $statusColor = match (strtolower($displayStatus)) {
                            'present' => 'border-green-500 shadow-green-100 dark:shadow-none',
                            'seminar' => 'border-yellow-400 shadow-yellow-50 dark:shadow-none',
                            'out of office' => 'border-red-500 shadow-red-50 bg-red-50 dark:bg-red-900/10 dark:shadow-none',
                            default => 'border-gray-200 dark:border-gray-700',
                        };
                        $statusBadge = match (strtolower($displayStatus)) {
                            'present' => 'bg-green-100 text-green-800 dark:bg-green-900/40 dark:text-green-300',
                            'seminar' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/40 dark:text-yellow-300',
                            'out of office' => 'bg-red-100 text-red-800 dark:bg-red-900/40 dark:text-red-300',
                            default => 'bg-gray-100 dark:bg-gray-700 dark:text-gray-300',
                        };
                        $pulse = strtolower($displayStatus) === 'seminar' ? 'animate-pulse' : '';
                    @endphp
                    <div
                        class="bg-white dark:bg-gray-800 rounded-xl border-2 {{ $statusColor }} p-6 transition {{ $pulse }}">
                        <div class="flex items-center justify-between mb-4">
                            <div class="h-12 w-12 rounded-full overflow-hidden bg-gray-100 dark:bg-gray-900 flex items-center justify-center border border-gray-200 dark:border-gray-700">
                                @if($doctor->avatar_url)
                                    <img src="{{ $doctor->avatar_url }}" alt="{{ $doctor->name }}" class="w-full h-full object-cover">
                                @else
                                    <span class="text-gray-500 font-bold">{{ $doctor->initials }}</span>
                                @endif
                            </div>
                            <span class="px-3 py-1 rounded-full text-xs font-semibold uppercase {{ $statusBadge }}">
                                {{ $displayStatus }}
                            </span>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white">{{ $doctor->formatted_name }}</h3>
                        <p class="text-green-600 font-medium">{{ $doctor->specialty }}</p>
                        <p class="text-gray-500 dark:text-gray-400 text-sm mt-3 line-clamp-2">
                            @if($doctor->formatted_schedule)
                                <strong class="text-gray-700 dark:text-gray-300">Schedule:</strong> {{ $doctor->formatted_schedule }}
                            @else
                                Available for general consultation.
                            @endif
                        </p>
                    </div>
                @endforeach
            </div>
        </div>

    </div>



    </div>
@endsection