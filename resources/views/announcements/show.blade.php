@extends('layouts.app')

@section('content')
<div x-data="{
        lastUpdated: '{{ $announcement->updated_at->toIso8601String() }}',
        copied: false,
        activeSlide: 0,
        totalSlides: {{ 1 + $announcement->images->where('type', 'slide')->count() }},
        fullscreenMedia: null,
        
        copyLink() {
            navigator.clipboard.writeText(window.location.href);
            this.copied = true;
            setTimeout(() => this.copied = false, 2500);
        },
        checkForUpdates() {
            fetch('{{ route('announcements.check-update', $announcement) }}')
                .then(response => response.json())
                .then(data => {
                    if (data.updated_at !== this.lastUpdated) {
                        this.lastUpdated = data.updated_at;
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
                    let newContent = doc.getElementById('announcement-container');
                    let currentContainer = document.getElementById('announcement-container');
                    
                    if (newContent && currentContainer) {
                        currentContainer.innerHTML = newContent.innerHTML;
                        if (window.Alpine) {
                            setTimeout(() => {
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
            }, 5000);
        }
     }"
     x-init="initPolling()"
     class="relative z-10 min-h-screen pt-4 pb-12 sm:pt-6 sm:pb-16 bg-transparent">

    <!-- Top Breadcrumb & Share Actions -->
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 mb-4 sm:mb-6">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <a href="{{ route('announcements.index') }}" 
               class="inline-flex items-center gap-2 text-xs font-bold uppercase tracking-wider text-slate-600 dark:text-slate-400 hover:text-emerald-600 dark:hover:text-emerald-400 transition group">
                <span class="p-2 rounded-xl bg-white dark:bg-slate-800 shadow-sm border border-slate-200/80 dark:border-slate-700/80 group-hover:border-emerald-500/40 group-hover:bg-emerald-50 dark:group-hover:bg-emerald-950/40 group-hover:text-emerald-600 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                </span>
                Back to Announcements
            </a>

            <div class="flex items-center gap-2">
                <button type="button" 
                        @click="copyLink()" 
                        class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl bg-white dark:bg-slate-800 border border-slate-200/80 dark:border-slate-700/80 text-xs font-bold text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700/60 shadow-xs transition-all">
                    <svg x-show="!copied" class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                    </svg>
                    <svg x-show="copied" class="w-3.5 h-3.5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display: none;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    <span x-text="copied ? 'Link Copied!' : 'Share Post'"></span>
                </button>

                <button type="button" 
                        onclick="window.print()" 
                        class="p-1.5 rounded-xl bg-white dark:bg-slate-800 border border-slate-200/80 dark:border-slate-700/80 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 shadow-xs transition-all"
                        title="Print this announcement">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Main Container -->
    <div id="announcement-container" class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        @php
            $slides = $announcement->images->where('type', 'slide');
            $sections = $announcement->images->where('type', 'section');
            $wordCount = str_word_count(strip_tags($announcement->content));
            $readTime = max(1, ceil($wordCount / 180));
        @endphp

        <!-- Main Article Card -->
        <article class="bg-white dark:bg-slate-900 rounded-3xl shadow-xl shadow-slate-200/50 dark:shadow-slate-950/50 border border-slate-200/80 dark:border-slate-800 overflow-hidden">
            
            {{-- 1. Structured Article Header (Clean, High Contrast, Separated from Image) --}}
            <header class="p-6 sm:p-10 lg:p-12 border-b border-slate-100 dark:border-slate-800/80">
                
                {{-- Metadata Pill Badges Row --}}
                <div class="flex flex-wrap items-center gap-2 sm:gap-3 mb-5">
                    @if($announcement->event_date)
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-black uppercase tracking-wider bg-blue-600 text-white shadow-xs">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            Event Schedule
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-black uppercase tracking-wider bg-emerald-600 text-white shadow-xs">
                            <span class="w-1.5 h-1.5 rounded-full bg-white animate-pulse"></span>
                            Official Advisory
                        </span>
                    @endif

                    <span class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-500 dark:text-slate-400 bg-slate-100 dark:bg-slate-800 px-3 py-1 rounded-full">
                        <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        Published {{ $announcement->created_at->format('M d, Y') }}
                    </span>

                    <span class="inline-flex items-center gap-1 text-xs font-medium text-slate-400 dark:text-slate-500 bg-slate-50 dark:bg-slate-800/40 px-2.5 py-1 rounded-full border border-slate-200/50 dark:border-slate-700/50">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        {{ $readTime }} min read
                    </span>
                </div>

                {{-- Heading Title --}}
                <h1 class="text-2xl sm:text-4xl lg:text-5xl font-black text-slate-900 dark:text-white tracking-tight leading-tight sm:leading-tight mb-4">
                    {{ $announcement->title }}
                </h1>

                {{-- Subheading --}}
                @if($announcement->subheading)
                    <p class="text-base sm:text-xl font-semibold text-emerald-700 dark:text-emerald-400 leading-relaxed max-w-4xl">
                        {{ $announcement->subheading }}
                    </p>
                @endif

                {{-- Event Schedule Highlight Card (if event date is provided) --}}
                @if($announcement->event_date)
                    <div class="mt-6 p-4 sm:p-5 rounded-2xl bg-gradient-to-r from-emerald-50 to-teal-50 dark:from-emerald-950/30 dark:to-teal-950/20 border border-emerald-200/70 dark:border-emerald-800/50 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                        <div class="flex items-center gap-3.5">
                            <div class="w-12 h-12 rounded-xl bg-emerald-600 text-white flex items-center justify-center shadow-md shadow-emerald-600/25 shrink-0">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                            <div>
                                <span class="text-[10px] font-black uppercase tracking-widest text-emerald-800 dark:text-emerald-300">Scheduled Event Date</span>
                                <h4 class="text-base sm:text-lg font-bold text-slate-900 dark:text-white">
                                    {{ $announcement->event_date->format('l, F j, Y') }}
                                    @if($announcement->start_time)
                                        <span class="text-emerald-700 dark:text-emerald-400 font-semibold">at {{ \Carbon\Carbon::parse($announcement->start_time)->format('g:i A') }}</span>
                                    @endif
                                </h4>
                            </div>
                        </div>

                        <span class="text-xs font-bold text-emerald-700 dark:text-emerald-300 px-3.5 py-1.5 rounded-xl bg-white dark:bg-slate-800 border border-emerald-200 dark:border-emerald-800 shadow-xs">
                            RHU Activity
                        </span>
                    </div>
                @endif
            </header>

            {{-- 2. Standalone Media Section (Clean, Uncut, Contained, No Overlaid Text) --}}
            @if($announcement->image_path || $slides->count() > 0)
                <div class="p-4 sm:p-8 lg:p-10 bg-slate-50/70 dark:bg-slate-950/40 border-b border-slate-100 dark:border-slate-800/80">
                    
                    @if($announcement->display_type === 'carousel' && $slides->count() > 0)
                        {{-- Carousel Presentation --}}
                        <div class="relative rounded-2xl overflow-hidden bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-lg"
                             x-data="{ 
                                activeSlide: 0, 
                                total: {{ 1 + $slides->count() }} 
                             }">
                            <div class="relative min-h-[350px] max-h-[620px] flex items-center justify-center bg-black/90">
                                {{-- Slide 1: Main Announcement Image/Video --}}
                                <div x-show="activeSlide === 0" class="w-full flex items-center justify-center p-2">
                                    @if(Str::endsWith($announcement->image_path, ['.mp4']))
                                        <video src="{{ asset('uploads/' . $announcement->image_path) }}" 
                                               controls class="max-h-[580px] w-auto max-w-full rounded-xl object-contain"></video>
                                    @else
                                        <img src="{{ asset('uploads/' . $announcement->image_path) }}" 
                                             alt="{{ $announcement->title }}" 
                                             class="max-h-[580px] w-auto max-w-full rounded-xl object-contain shadow-md cursor-zoom-in"
                                             @click="fullscreenMedia = '{{ asset('uploads/' . $announcement->image_path) }}'">
                                    @endif
                                </div>

                                {{-- Additional Slides --}}
                                @foreach($slides as $index => $slide)
                                    <div x-show="activeSlide === {{ $index + 1 }}" class="w-full flex items-center justify-center p-2" style="display: none;">
                                        @if($slide->media_type === 'video_upload' || Str::endsWith($slide->image_path, ['.mp4']))
                                            <video src="{{ asset('uploads/' . $slide->image_path) }}" 
                                                   controls class="max-h-[580px] w-auto max-w-full rounded-xl object-contain"></video>
                                        @else
                                            <img src="{{ asset('uploads/' . $slide->image_path) }}" 
                                                 alt="" 
                                                 class="max-h-[580px] w-auto max-w-full rounded-xl object-contain shadow-md cursor-zoom-in"
                                                 @click="fullscreenMedia = '{{ asset('uploads/' . $slide->image_path) }}'">
                                        @endif
                                    </div>
                                @endforeach
                            </div>

                            {{-- Carousel Controls --}}
                            <div class="absolute inset-0 flex items-center justify-between px-4 pointer-events-none">
                                <button @click="activeSlide = activeSlide === 0 ? total - 1 : activeSlide - 1" 
                                        class="pointer-events-auto p-2.5 rounded-full bg-black/60 hover:bg-black/90 text-white shadow-lg backdrop-blur-md transition-all hover:scale-110">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                                </button>
                                <button @click="activeSlide = activeSlide === total - 1 ? 0 : activeSlide + 1" 
                                        class="pointer-events-auto p-2.5 rounded-full bg-black/60 hover:bg-black/90 text-white shadow-lg backdrop-blur-md transition-all hover:scale-110">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                                </button>
                            </div>

                            {{-- Indicator Pill --}}
                            <div class="absolute bottom-4 left-1/2 -translate-x-1/2 bg-black/70 backdrop-blur-md px-3.5 py-1 rounded-full text-white text-xs font-bold flex items-center gap-1.5 shadow-md">
                                <span x-text="activeSlide + 1"></span> / <span x-text="total"></span>
                            </div>
                        </div>
                    @else
                        {{-- Standalone Single Image / Video Presentation --}}
                        <div class="rounded-2xl overflow-hidden bg-slate-100 dark:bg-slate-950 border border-slate-200/80 dark:border-slate-800 shadow-md flex items-center justify-center p-2 sm:p-4">
                            @if(Str::endsWith($announcement->image_path, ['.mp4']))
                                <video src="{{ asset('uploads/' . $announcement->image_path) }}" 
                                       controls class="max-h-[640px] w-auto max-w-full rounded-xl object-contain bg-black"></video>
                            @else
                                <img src="{{ asset('uploads/' . $announcement->image_path) }}" 
                                     alt="{{ $announcement->title }}" 
                                     class="max-h-[640px] w-auto max-w-full rounded-xl object-contain shadow-sm cursor-zoom-in transition-transform duration-300 hover:scale-[1.01]"
                                     @click="fullscreenMedia = '{{ asset('uploads/' . $announcement->image_path) }}'">
                            @endif
                        </div>
                        <p class="text-center text-xs text-slate-400 dark:text-slate-500 mt-2 font-medium">Click image to view full size</p>
                    @endif

                </div>
            @endif

            {{-- 3. Main Content Body (High-Contrast, Clean Typography) --}}
            <div class="p-6 sm:p-10 lg:p-12">
                <div class="text-slate-800 dark:text-slate-100 text-base sm:text-lg leading-relaxed sm:leading-8 space-y-4 font-normal font-sans">
                    {!! nl2br(e($announcement->content)) !!}
                </div>
            </div>

            {{-- 4. Additional Content Sections (if any) --}}
            @if($sections->count() > 0)
                <div class="border-t border-slate-200/80 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-950/30 p-6 sm:p-10 lg:p-12">
                    <div class="flex items-center gap-3 mb-8">
                        <span class="w-2.5 h-6 bg-emerald-600 rounded-full"></span>
                        <h3 class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white tracking-tight">Additional Details & Guidelines</h3>
                    </div>

                    <div class="space-y-10">
                        @foreach($sections as $index => $section)
                            @php 
                                $layout = $section->layout ?? 'left';
                                $isCenter = ($layout === 'middle');
                                $isRight = ($layout === 'right');
                            @endphp

                            <div class="p-6 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-sm transition-all hover:shadow-md">
                                <div class="grid grid-cols-1 {{ $isCenter ? 'gap-6' : 'md:grid-cols-12 gap-8' }} items-center">
                                    
                                    {{-- Section Media --}}
                                    @if($section->image_path || $section->video_url)
                                        <div class="{{ $isCenter ? 'w-full max-w-2xl mx-auto' : ($isRight ? 'md:col-span-6 md:order-2' : 'md:col-span-6 md:order-1') }}">
                                            @if($section->media_type == 'video_link' && $section->video_url)
                                                @php
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
                                                <div class="aspect-video rounded-xl overflow-hidden shadow-md bg-black">
                                                    <iframe src="{{ $embedUrl }}" class="w-full h-full" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                                                </div>
                                            @elseif($section->image_path)
                                                <div class="rounded-xl overflow-hidden bg-slate-100 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 flex items-center justify-center p-2">
                                                    @if($section->media_type == 'video_upload' || Str::endsWith($section->image_path, '.mp4'))
                                                        <video src="{{ asset('uploads/' . $section->image_path) }}" controls class="max-h-[400px] w-full object-contain rounded-lg"></video>
                                                    @else
                                                        <img src="{{ asset('uploads/' . $section->image_path) }}" 
                                                             class="max-h-[400px] w-auto max-w-full rounded-lg object-contain shadow-xs cursor-zoom-in"
                                                             @click="fullscreenMedia = '{{ asset('uploads/' . $section->image_path) }}'">
                                                    @endif
                                                </div>
                                            @endif
                                        </div>
                                    @endif

                                    {{-- Section Text Content --}}
                                    <div class="{{ ($section->image_path || $section->video_url) ? ($isCenter ? 'w-full text-center' : ($isRight ? 'md:col-span-6 md:order-1' : 'md:col-span-6 md:order-2')) : 'col-span-12' }}">
                                        @if(!empty($section->content))
                                            <div class="text-slate-800 dark:text-slate-200 text-base sm:text-lg leading-relaxed whitespace-pre-line font-normal">
                                                {{ $section->content }}
                                            </div>
                                        @endif
                                    </div>

                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- 5. Article Footer / Meta & Contact info --}}
            <footer class="px-6 py-6 sm:px-12 bg-slate-50 dark:bg-slate-950/60 border-t border-slate-100 dark:border-slate-800/80 flex flex-col sm:flex-row items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <img src="{{ asset('assets/images/logo.png') }}" class="w-10 h-10 rounded-full bg-white p-1 border border-slate-200 shadow-xs" alt="RHU Logo">
                    <div>
                        <h5 class="text-xs font-black uppercase tracking-wider text-slate-900 dark:text-white">Rural Health Unit of Silang</h5>
                        <p class="text-[11px] text-slate-500 dark:text-slate-400">Official Municipal Health Information System</p>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <a href="{{ route('announcements.index') }}" 
                       class="px-5 py-2.5 rounded-xl bg-slate-200 dark:bg-slate-800 text-slate-800 dark:text-slate-200 text-xs font-bold hover:bg-slate-300 dark:hover:bg-slate-700 transition-all">
                        More Announcements
                    </a>
                </div>
            </footer>

        </article>

        <!-- Bottom Action CTA -->
        <div class="mt-8 mb-12 flex justify-center">
            <a href="{{ route('announcements.index') }}" class="inline-flex items-center gap-2 px-8 py-3.5 rounded-full bg-emerald-600 text-white font-bold text-sm shadow-lg shadow-emerald-900/20 hover:bg-emerald-700 hover:shadow-xl hover:-translate-y-0.5 transition-all active:scale-95">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                View All Public Announcements
            </a>
        </div>

    </div>

    <!-- Fullscreen Image Lightbox Modal -->
    <div x-show="fullscreenMedia" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-[99999] bg-black/90 backdrop-blur-md flex items-center justify-center p-4"
         @keydown.escape.window="fullscreenMedia = null"
         style="display: none;">
        
        <button type="button" 
                @click="fullscreenMedia = null" 
                class="absolute top-4 right-4 p-3 rounded-full bg-white/10 hover:bg-white/20 text-white transition-colors">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
        </button>

        <img :src="fullscreenMedia" class="max-h-[90vh] max-w-[90vw] object-contain rounded-xl shadow-2xl" @click.stop>
    </div>

</div>
@endsection
