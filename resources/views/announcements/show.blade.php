@extends('layouts.app')

@section('content')
<div x-data="{
        lastUpdated: '{{ $announcement->updated_at->toIso8601String() }}',
        checkForUpdates() {
            fetch('{{ route('announcements.check-update', $announcement) }}')
                .then(response => response.json())
                .then(data => {
                    if (data.updated_at !== this.lastUpdated) {
                        console.log('Update detected! Refreshing content...');
                        this.lastUpdated = data.updated_at;
                        this.refreshContent();
                    }
                })
                .catch(error => console.error('Error checking for updates:', error));
        },
        refreshContent() {
             fetch(window.location.href)
                .then(response => response.text())
                .then(html => {
                    let parser = new DOMParser();
                    let doc = parser.parseFromString(html, 'text/html');
                    let newContent = doc.getElementById('announcement-container');
                    let currentContainer = document.getElementById('announcement-container');
                    
                    if (newContent && currentContainer) {
                        // Swap HTML
                        currentContainer.innerHTML = newContent.innerHTML;
                        
                        // Re-initialize Alpine on the new content if present
                        // This checks if the window has Alpine loaded and if there's x-data inside
                        if (window.Alpine) {
                            // Small delay to ensure DOM is ready then re-init
                            setTimeout(() => {
                                // Find any new elements with x-data inside the container and init them
                                currentContainer.querySelectorAll('[x-data]').forEach(el => {
                                    Alpine.initTree(el);
                                });
                            }, 50);
                        }
                    }
                });
        },
        initPolling() {
            setInterval(() => {
                this.checkForUpdates();
            }, 5000); // Poll every 5 seconds
        }
     }"
     x-init="initPolling()">
    <!-- Breadcrumb / Back Navigation -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-8 mt-6">
        <a href="{{ url('/#announcements') }}" class="inline-flex items-center text-gray-500 dark:text-gray-400 hover:text-teal-600 transition group font-medium">
            <div class="w-8 h-8 rounded-full bg-white dark:bg-gray-800 shadow-sm flex items-center justify-center mr-3 group-hover:scale-110 transition-transform">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
            </div>
            Back to Events & Announcements
        </a>
    </div>

    <!-- Dynamic Content Container -->
    <div id="announcement-container" class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        @php
            // Separate Slides (for top carousel) and Sections (for content body)
            // Note: The main image (announcement->image_path) is always Slide #1 if in Carousel mode.
            $slides = $announcement->images->where('type', 'slide'); // Extra main images
            $sections = $announcement->images->where('type', 'section'); // Content sections with layout
            $totalSlides = 1 + $slides->count(); // Main Image + Extra Slides
        @endphp

        <!-- Main Article Card -->
        <article class="bg-white dark:bg-gray-800 rounded-3xl shadow-xl overflow-hidden border border-gray-100 dark:border-gray-700">
            
            <!-- Hero Section (Static Image, Video, or Carousel) -->
            <div class="relative w-full group {{ $announcement->display_mode === 'infographic' ? 'min-h-[800px] h-auto bg-gray-100 dark:bg-gray-900' : 'h-96' }}" 
                 x-data="{ 
                    activeSlide: 0, 
                    total: {{ $totalSlides }},
                    interval: null,
                    startAutoScroll() {
                        this.interval = setInterval(() => {
                            this.activeSlide = this.activeSlide === this.total - 1 ? 0 : this.activeSlide + 1;
                        }, 5000);
                    },
                    stopAutoScroll() {
                        clearInterval(this.interval);
                    }
                 }" 
                 x-init="startAutoScroll()"
                 @mouseenter="stopAutoScroll()"
                 @mouseleave="startAutoScroll()">
                
                @if($announcement->display_type === 'carousel')
                    <!-- Carousel Mode -->
                    <div class="absolute inset-0 bg-gray-900">
                        <!-- Slide 1: Main Announcement Media -->
                        <div x-show="activeSlide === 0" 
                             class="absolute inset-0 transition-opacity duration-700 ease-in-out"
                             x-transition:enter-start="opacity-0"
                             x-transition:enter-end="opacity-100"
                             x-transition:leave-start="opacity-100"
                             x-transition:leave-end="opacity-0">
                            @if($announcement->image_path)
                                @if(Str::endsWith($announcement->image_path, '.mp4'))
                                    <video src="{{ asset('uploads/' . $announcement->image_path) }}" 
                                           class="w-full h-full {{ $announcement->display_mode === 'infographic' ? 'object-contain' : 'object-cover' }}" 
                                           autoplay muted loop playsinline></video>
                                @else
                                    <!-- Blurred background fill -->
                                    <img src="{{ asset('uploads/' . $announcement->image_path) }}" alt="" aria-hidden="true"
                                         class="absolute inset-0 w-full h-full object-cover scale-110 blur-2xl opacity-80">
                                    <!-- Actual full image -->
                                    <img src="{{ asset('uploads/' . $announcement->image_path) }}" alt="{{ $announcement->title }}" 
                                         class="relative w-full h-full object-contain z-[1]">
                                @endif
                            @else
                                <div class="w-full h-full bg-linear-to-br from-teal-600 to-cyan-700"></div>
                            @endif
                        </div>

                        <!-- Additional Slides -->
                        @foreach($slides as $index => $slide)
                             <div x-show="activeSlide === {{ $index + 1 }}" 
                                  class="absolute inset-0 transition-opacity duration-700 ease-in-out"
                                  x-transition:enter-start="opacity-0"
                                  x-transition:enter-end="opacity-100"
                                  x-transition:leave-start="opacity-100"
                                  x-transition:leave-end="opacity-0"
                                  style="display: none;">
                                @if($slide->media_type === 'video_upload' || Str::endsWith($slide->image_path, ['.mp4']))
                                     <video src="{{ asset('uploads/' . $slide->image_path) }}" 
                                            class="w-full h-full {{ $announcement->display_mode === 'infographic' ? 'object-contain' : 'object-cover' }}" 
                                            autoplay muted loop playsinline></video>
                                @else
                                    <!-- Blurred background fill -->
                                    <img src="{{ asset('uploads/' . $slide->image_path) }}" alt="" aria-hidden="true"
                                         class="absolute inset-0 w-full h-full object-cover scale-110 blur-2xl opacity-80">
                                    <!-- Actual full image -->
                                    <img src="{{ asset('uploads/' . $slide->image_path) }}" 
                                         class="relative w-full h-full object-contain z-[1]">
                                @endif
                            </div>
                        @endforeach
                    </div>

                    <!-- Carousel Controls -->
                    <div class="absolute inset-0 flex items-center justify-between px-4 z-20 pointer-events-none">
                        <button @click="activeSlide = activeSlide === 0 ? total - 1 : activeSlide - 1" class="pointer-events-auto bg-black/20 hover:bg-black/40 backdrop-blur-md text-white p-2 rounded-full transition focus:outline-none opacity-0 group-hover:opacity-100 duration-300">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                        </button>
                        <button @click="activeSlide = activeSlide === total - 1 ? 0 : activeSlide + 1" class="pointer-events-auto bg-black/20 hover:bg-black/40 backdrop-blur-md text-white p-2 rounded-full transition focus:outline-none opacity-0 group-hover:opacity-100 duration-300">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                        </button>
                    </div>

                    <!-- Indicators -->
                    <div class="absolute bottom-4 right-4 z-20 flex gap-2">
                        <template x-for="i in total">
                            <button @click="activeSlide = i - 1" :class="activeSlide === i - 1 ? 'bg-teal-500 w-6' : 'bg-white dark:bg-gray-800/50 w-2 hover:bg-white dark:bg-gray-800'" class="h-1.5 rounded-full transition-all duration-300 shadow-sm"></button>
                        </template>
                    </div>

                @else
                    <!-- Static Mode (Single Media) -->
                    @if($announcement->image_path)
                         @if(Str::endsWith($announcement->image_path, '.mp4'))
                            <video src="{{ asset('uploads/' . $announcement->image_path) }}" 
                                   class="w-full h-full {{ $announcement->display_mode === 'infographic' ? 'object-contain bg-gray-900' : 'object-cover' }}" 
                                   autoplay muted loop playsinline></video>
                         @else
                            <!-- Blurred background fill -->
                            <img src="{{ asset('uploads/' . $announcement->image_path) }}" alt="" aria-hidden="true"
                                 class="absolute inset-0 w-full h-full object-cover scale-110 blur-2xl opacity-80">
                            <!-- Actual full image -->
                            <img src="{{ asset('uploads/' . $announcement->image_path) }}" alt="{{ $announcement->title }}" 
                                 class="relative w-full h-full object-contain z-[1]">
                         @endif
                    @else
                        <div class="w-full h-full bg-linear-to-br from-teal-600 to-cyan-700"></div>
                    @endif
                @endif

                 <!-- Overlay Gradient for Text (Only for standard mode or if caption needed) -->
                 @if($announcement->display_mode !== 'infographic')
                     <div class="absolute inset-0 bg-linear-to-t from-gray-900/90 via-gray-900/40 to-transparent pointer-events-none z-10"></div>

                     <!-- Content Overlay -->
                     <div class="absolute bottom-0 left-0 right-0 p-8 sm:p-12 text-white z-20 pointer-events-none">
                        <div class="flex items-center gap-4 mb-4 text-sm font-semibold tracking-wider uppercase opacity-90">
                            <span class="bg-teal-500/80 backdrop-blur-sm px-3 py-1 rounded-full text-white shadow-sm">
                                {{ $announcement->event_date ? 'Appt Event' : 'Update' }}
                            </span>
                            <span>{{ $announcement->created_at->format('M d, Y') }}</span>
                        </div>
                        
                        <h1 class="text-3xl sm:text-5xl font-bold mb-3 leading-tight text-white shadow-sm drop-shadow-md">
                            {{ $announcement->title }}
                        </h1>
                        
                        @if($announcement->subheading)
                            <p class="text-xl sm:text-2xl text-teal-50 font-light max-w-3xl drop-shadow-sm">
                                {{ $announcement->subheading }}
                            </p>
                        @endif

                        @if($announcement->event_date)
                            <div class="mt-6 flex items-center gap-3 bg-black/30 backdrop-blur-md w-fit px-4 py-2 rounded-lg border border-white/20">
                                <svg class="w-5 h-5 text-teal-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                <span class="font-medium text-white">Event Date: {{ $announcement->event_date->format('l, F j, Y') }}</span>
                            </div>
                        @endif
                     </div>
                 @endif
            </div>

            <!-- Title Section for Infographic Mode (Below Image) -->
            @if($announcement->display_mode === 'infographic')
                <div class="p-8 border-b border-gray-100 dark:border-gray-700 bg-teal-50 dark:bg-teal-900/20">
                    <div class="flex items-center gap-4 mb-3 text-sm font-semibold tracking-wider uppercase text-teal-700">
                        <span class="bg-teal-100 px-3 py-1 rounded-full border border-teal-200">
                            {{ $announcement->event_date ? 'Appt Event' : 'Update' }}
                        </span>
                        <span>{{ $announcement->created_at->format('M d, Y') }}</span>
                    </div>
                    <h1 class="text-3xl font-bold text-gray-800 dark:text-white mb-2">{{ $announcement->title }}</h1>
                    @if($announcement->subheading)
                        <p class="text-xl text-gray-600 dark:text-gray-400">{{ $announcement->subheading }}</p>
                    @endif
                     @if($announcement->event_date)
                        <div class="mt-4 flex items-center gap-2 text-teal-700 font-bold bg-white dark:bg-gray-800 w-fit px-4 py-2 rounded-lg border border-teal-100 shadow-sm">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <span>Event Date: {{ $announcement->event_date->format('l, F j, Y') }}</span>
                        </div>
                    @endif
                </div>
            @endif

            <!-- Main Content Body -->
            <div class="p-8 sm:p-12">
                <div class="prose prose-lg prose-teal max-w-none text-gray-600 dark:text-gray-400 leading-relaxed">
                    {!! nl2br(e($announcement->content)) !!}
                </div>
            </div>

            <!-- Regular Sections (List Layout) -->
            <!-- Rendered for BOTH Carousel and List modes -->
            @if($sections->count() > 0)
                <div class="bg-white dark:bg-gray-800 border-t border-gray-100 dark:border-gray-700 p-8 sm:p-12">
                     <h3 class="text-2xl font-bold text-gray-800 dark:text-white mb-8 border-l-4 border-teal-500 pl-4">Additional Details</h3>
                    <div class="space-y-12">
                        @foreach($sections as $index => $section)
                            @php 
                                $layout = $section->layout ?? 'left';
                                $containerClass = 'flex-col md:flex-row'; 
                                $imageContainerWidth = 'w-full md:w-1/2';
                                $contentContainerWidth = 'w-full md:w-1/2 text-left';
                                $imageHeight = 'h-64 sm:h-80 md:h-96'; 
                                
                                if($layout === 'middle') { 
                                    $containerClass = 'flex-col';
                                    $imageContainerWidth = 'w-full md:w-4/5 mx-auto'; 
                                    $contentContainerWidth = 'w-full md:w-4/5 mx-auto text-center mt-6';
                                    $imageHeight = 'h-64 sm:h-96 md:h-[500px]'; 
                                } elseif($layout === 'right') { 
                                    $containerClass = 'flex-col md:flex-row-reverse';
                                }
                            @endphp
                        
                            <div class="flex {{ $containerClass }} gap-8 md:gap-12 items-center group py-8 border-b last:border-0 border-gray-100 dark:border-gray-700 last:pb-0">
                                <div class="{{ $imageContainerWidth }}">
                                    @if($section->media_type == 'video_link' && $section->video_url)
                                        <div class="aspect-w-16 aspect-h-9 rounded-2xl overflow-hidden shadow-xl border border-gray-100 dark:border-gray-700 bg-black">
                                            @php
                                                // Simple Youtube Embed Logic
                                                $embedUrl = $section->video_url;
                                                if(Str::contains($embedUrl, 'youtube.com/watch?v=')) {
                                                    $videoId = Str::after($embedUrl, 'v=');
                                                    $videoId = explode('&', $videoId)[0];
                                                    $embedUrl = "https://www.youtube.com/embed/" . $videoId;
                                                } elseif(Str::contains($embedUrl, 'youtu.be/')) {
                                                    $videoId = Str::after($embedUrl, 'youtu.be/');
                                                    $embedUrl = "https://www.youtube.com/embed/" . $videoId;
                                                }
                                            @endphp
                                            <iframe src="{{ $embedUrl }}" class="w-full h-full" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                                        </div>
                                    @elseif($section->image_path)
                                        <div class="rounded-2xl overflow-hidden shadow-xl transform transition duration-500 group-hover:scale-[1.01] border border-gray-100 dark:border-gray-700 bg-white dark:bg-gray-800">
                                        @if($section->media_type == 'video_upload' || Str::endsWith($section->image_path, '.mp4'))
                                                <video src="{{ asset('uploads/' . $section->image_path) }}" controls class="w-full {{ $imageHeight }} object-cover bg-black"></video>
                                            @else
                                                <img src="{{ asset('uploads/' . $section->image_path) }}" class="w-full {{ $imageHeight }} object-cover">
                                            @endif
                                        </div>
                                    @else
                                        <!-- No Media -->
                                    @endif
                                </div>
                                <div class="{{ $contentContainerWidth }} flex flex-col justify-center">
                                    @if($layout !== 'middle')
                                        <div class="h-1.5 w-20 bg-teal-500 rounded-full mb-6 {{ $layout === 'right' ? 'ml-auto md:ml-0' : 'mr-auto' }}"></div>
                                    @else
                                            <div class="h-1.5 w-24 bg-teal-500 rounded-full mb-6 mx-auto"></div>
                                    @endif
                                    
                                    <div class="prose prose-lg prose-teal text-gray-600 dark:text-gray-400 {{ $layout === 'middle' ? 'mx-auto' : '' }}">
                                        <p class="leading-relaxed whitespace-pre-line">{{ $section->content }}</p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </article>
        
        <!-- Bottom Actions -->
        <div class="mt-8 mb-16 flex justify-center">
            <a href="{{ url('/#announcements') }}" class="bg-teal-600 text-white font-bold py-3 px-8 rounded-full shadow-lg hover:bg-teal-700 hover:shadow-xl transition transform hover:-translate-y-1">
                View All Events & Updates
            </a>
        </div>
    </div>
    </div>
</div>
@endsection
