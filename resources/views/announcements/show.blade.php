@extends('layouts.app')

@section('content')
<div x-data="{
        lastUpdated: '{{ $announcement->updated_at->toIso8601String() }}',
        copied: false,
        activeSlide: 0,
        totalSlides: {{ 1 + $announcement->images->where('type', 'slide')->count() }},
        fullscreenMedia: null,
        shareMenuOpen: false,
        fontSizeLevel: 1, // 0: sm, 1: base/lg, 2: xl
        scrollProgress: 0,
        
        copyLink() {
            navigator.clipboard.writeText(window.location.href);
            this.copied = true;
            setTimeout(() => this.copied = false, 2500);
        },
        updateScroll() {
            const winScroll = document.documentElement.scrollTop || document.body.scrollTop;
            const height = document.documentElement.scrollHeight - document.documentElement.clientHeight;
            this.scrollProgress = height > 0 ? (winScroll / height) * 100 : 0;
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
     @scroll.window="updateScroll()"
     class="relative min-h-screen pt-3 pb-16 sm:pt-6 sm:pb-24 bg-gradient-to-b from-slate-50 via-white to-slate-50/50 dark:from-slate-950 dark:via-slate-900 dark:to-slate-950 transition-colors">

    {{-- Top Reading Progress Indicator (Fixed) --}}
    <div class="fixed top-0 left-0 right-0 h-1 z-50 bg-transparent pointer-events-none print:hidden">
        <div class="h-full bg-gradient-to-r from-emerald-500 via-teal-500 to-cyan-500 transition-all duration-150"
             :style="'width: ' + scrollProgress + '%'"></div>
    </div>

    @php
        $slides = $announcement->images->where('type', 'slide');
        $sections = $announcement->images->where('type', 'section');
        $wordCount = str_word_count(strip_tags($announcement->content));
        $readTime = max(1, ceil($wordCount / 180));
        
        $cat = $category ?? \App\Http\Controllers\PublicController::getAnnouncementCategory($announcement);

        // Google Calendar Format
        $gCalUrl = null;
        if($announcement->event_date) {
            $eventDateStr = $announcement->event_date->format('Ymd');
            $startTimeStr = $announcement->start_time ? \Carbon\Carbon::parse($announcement->start_time)->format('His') : '080000';
            $endTimeStr = $announcement->end_time ? \Carbon\Carbon::parse($announcement->end_time)->format('His') : '170000';
            $startDateGCal = $eventDateStr . 'T' . $startTimeStr;
            $endDateGCal = ($announcement->end_date ? $announcement->end_date->format('Ymd') : $eventDateStr) . 'T' . $endTimeStr;
            
            $gCalUrl = 'https://calendar.google.com/calendar/render?action=TEMPLATE'
                     . '&text=' . urlencode('RHU Silang: ' . $announcement->title)
                     . '&dates=' . $startDateGCal . '/' . $endDateGCal
                     . '&details=' . urlencode($announcement->subheading ?? substr(strip_tags($announcement->content), 0, 200))
                     . '&location=' . urlencode('Rural Health Unit, M.H. del Pilar St., Silang, Cavite');
        }
    @endphp

    {{-- Breadcrumbs & Citizen Utility Bar --}}
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 mb-6 print:hidden">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 py-2 border-b border-slate-200/60 dark:border-slate-800">
            
            {{-- Breadcrumb trail --}}
            <nav class="flex items-center gap-2 text-xs font-medium text-slate-500 dark:text-slate-400 overflow-x-auto py-1">
                <a href="{{ route('welcome') }}" class="hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors flex items-center gap-1.5 shrink-0">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                    <span>Home</span>
                </a>
                <span class="text-slate-300 dark:text-slate-700">/</span>
                <a href="{{ route('announcements.index') }}" class="hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors shrink-0">
                    Announcements
                </a>
                <span class="text-slate-300 dark:text-slate-700">/</span>
                <span class="px-2 py-0.5 rounded-md text-[11px] font-semibold {{ $cat['bg'] }} shrink-0">
                    {{ $cat['label'] }}
                </span>
                <span class="text-slate-300 dark:text-slate-700">/</span>
                <span class="text-slate-700 dark:text-slate-300 font-semibold truncate max-w-[200px] sm:max-w-xs" title="{{ $announcement->title }}">
                    Bulletin #{{ str_pad($announcement->id, 3, '0', STR_PAD_LEFT) }}
                </span>
            </nav>

            {{-- Action Buttons --}}
            <div class="flex items-center gap-2 self-end sm:self-auto shrink-0">
                {{-- Font Size Controls --}}
                <div class="hidden sm:flex items-center gap-1 p-1 bg-white dark:bg-slate-800 rounded-xl border border-slate-200/80 dark:border-slate-700/80 shadow-2xs text-xs">
                    <button type="button" 
                            @click="fontSizeLevel = Math.max(0, fontSizeLevel - 1)" 
                            :class="fontSizeLevel === 0 ? 'text-emerald-600 font-bold bg-slate-100 dark:bg-slate-700' : 'text-slate-500 hover:text-slate-900 dark:hover:text-white'"
                            class="px-2 py-1 rounded-lg transition" title="Smaller text">
                        A-
                    </button>
                    <button type="button" 
                            @click="fontSizeLevel = 1" 
                            :class="fontSizeLevel === 1 ? 'text-emerald-600 font-bold bg-slate-100 dark:bg-slate-700' : 'text-slate-500 hover:text-slate-900 dark:hover:text-white'"
                            class="px-2 py-1 rounded-lg transition" title="Default text">
                        A
                    </button>
                    <button type="button" 
                            @click="fontSizeLevel = Math.min(2, fontSizeLevel + 1)" 
                            :class="fontSizeLevel === 2 ? 'text-emerald-600 font-bold bg-slate-100 dark:bg-slate-700' : 'text-slate-500 hover:text-slate-900 dark:hover:text-white'"
                            class="px-2 py-1 rounded-lg transition" title="Larger text">
                        A+
                    </button>
                </div>

                {{-- Share Dropdown --}}
                <div class="relative" @click.outside="shareMenuOpen = false">
                    <button type="button"
                            @click="shareMenuOpen = !shareMenuOpen"
                            class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl bg-white dark:bg-slate-800 border border-slate-200/90 dark:border-slate-700/80 text-xs font-bold text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700/60 shadow-2xs transition-all">
                        <svg class="w-3.5 h-3.5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/>
                        </svg>
                        <span>Share</span>
                    </button>

                    <div x-show="shareMenuOpen"
                         x-transition:enter="transition ease-out duration-150"
                         x-transition:enter-start="opacity-0 translate-y-1 scale-95"
                         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                         x-transition:leave="transition ease-in duration-100"
                         x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                         x-transition:leave-end="opacity-0 translate-y-1 scale-95"
                         style="display: none;"
                         class="absolute right-0 mt-2 w-52 rounded-2xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 shadow-xl p-1.5 z-40">
                        
                        <button type="button" 
                                @click="copyLink(); shareMenuOpen = false" 
                                class="w-full flex items-center justify-between px-3 py-2 text-xs font-semibold rounded-xl text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-700/60 transition">
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                                <span>Copy Link</span>
                            </div>
                            <span x-show="copied" class="text-[10px] text-emerald-600 font-bold">Copied!</span>
                        </button>

                        <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(request()->fullUrl()) }}" 
                           target="_blank" rel="noopener noreferrer"
                           class="flex items-center gap-2 px-3 py-2 text-xs font-semibold rounded-xl text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-700/60 transition">
                            <svg class="w-4 h-4 text-blue-600" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                            <span>Facebook</span>
                        </a>

                        <a href="https://twitter.com/intent/tweet?url={{ urlencode(request()->fullUrl()) }}&text={{ urlencode($announcement->title) }}" 
                           target="_blank" rel="noopener noreferrer"
                           class="flex items-center gap-2 px-3 py-2 text-xs font-semibold rounded-xl text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-700/60 transition">
                            <svg class="w-4 h-4 text-slate-800 dark:text-slate-200" fill="currentColor" viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 24.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                            <span>X (Twitter)</span>
                        </a>

                        <a href="https://api.whatsapp.com/send?text={{ urlencode($announcement->title . ' ' . request()->fullUrl()) }}" 
                           target="_blank" rel="noopener noreferrer"
                           class="flex items-center gap-2 px-3 py-2 text-xs font-semibold rounded-xl text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-700/60 transition">
                            <svg class="w-4 h-4 text-emerald-500" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.301-.15-1.777-.878-2.052-.978-.276-.1-.476-.15-.677.15-.201.3-.777.978-.953 1.179-.176.201-.351.226-.652.076-.301-.15-1.27-.468-2.42-1.493-.895-.798-1.5-1.784-1.676-2.085-.176-.3-.019-.463.132-.613.135-.135.301-.351.451-.527.15-.176.201-.301.301-.502.1-.201.05-.377-.025-.527-.075-.15-.677-1.632-.928-2.234-.244-.587-.492-.508-.677-.517-.176-.009-.377-.01-.578-.01-.201 0-.527.075-.803.377-.276.301-1.054 1.03-1.054 2.511 0 1.482 1.079 2.912 1.23 3.113.15.201 2.124 3.242 5.145 4.547.719.311 1.28.497 1.718.636.723.23 1.381.198 1.901.12.58-.088 1.777-.726 2.028-1.428.251-.702.251-1.304.176-1.428-.075-.125-.276-.2-.577-.35zM12.04 2C6.502 2 2.016 6.486 2.016 12.024c0 1.825.49 3.535 1.341 5.02L2 22l5.12-1.34c1.43.782 3.064 1.22 4.92 1.22 5.538 0 10.024-4.486 10.024-10.024C22.064 6.486 17.578 2 12.04 2z"/></svg>
                            <span>WhatsApp</span>
                        </a>
                    </div>
                </div>

                {{-- Print Button --}}
                <button type="button" 
                        onclick="window.print()" 
                        class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl bg-white dark:bg-slate-800 border border-slate-200/90 dark:border-slate-700/80 text-xs font-bold text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700/60 shadow-2xs transition-all"
                        title="Print this official notice">
                    <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                    </svg>
                    <span class="hidden sm:inline">Print</span>
                </button>
            </div>
        </div>
    </div>

    {{-- Main Container Layout --}}
    <div id="announcement-container" class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            
            {{-- Main Article Column (8 Cols on Desktop) --}}
            <main class="lg:col-span-8 space-y-8">
                
                {{-- Primary Article Document Card --}}
                <article class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/90 dark:border-slate-800 shadow-xl shadow-slate-200/40 dark:shadow-slate-950/40 overflow-hidden">
                    
                    {{-- Formal Municipal Masthead Header --}}
                    <header class="p-6 sm:p-10 lg:p-12 border-b border-slate-100 dark:border-slate-800/80 relative">
                        
                        {{-- Subtle background crest decoration --}}
                        <div class="absolute top-0 right-0 w-64 h-64 bg-emerald-500/5 dark:bg-emerald-500/10 rounded-full blur-3xl pointer-events-none -mr-16 -mt-16"></div>

                        {{-- Institutional Authority Bar --}}
                        <div class="flex items-center gap-3 pb-6 mb-6 border-b border-slate-100 dark:border-slate-800/80">
                            <img src="{{ asset('assets/images/logo.png') }}" alt="RHU Silang Seal" class="w-12 h-12 rounded-full p-1 bg-white border border-slate-200 dark:border-slate-700 shadow-xs shrink-0">
                            <div>
                                <div class="flex items-center gap-2 flex-wrap">
                                    <span class="text-sm font-black uppercase tracking-widest text-emerald-700 dark:text-emerald-400">
                                        Republic of the Philippines
                                    </span>
                                    <span class="text-slate-300 dark:text-slate-700">•</span>
                                    <span class="text-sm font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">
                                        Municipality of Silang
                                    </span>
                                </div>
                                <h3 class="text-sm lg:text-lg font-bold text-slate-900 dark:text-white tracking-tight">
                                    Rural Health Unit — Health Communications Division
                                </h3>
                            </div>
                        </div>

                        {{-- Metadata Pills & Publication Tags --}}
                        <div class="flex flex-wrap items-center gap-2 sm:gap-3 mb-6">
                            {{-- Category Badge --}}
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-black uppercase tracking-wider {{ $cat['bg'] }} border shadow-2xs">
                                <span class="w-2 h-2 rounded-full {{ $cat['dot'] }} animate-pulse"></span>
                                {{ $cat['label'] }}
                            </span>

                            {{-- Official Bulletin ID Tag --}}
                            <span class="inline-flex items-center gap-1.5 text-xs font-bold text-slate-700 dark:text-slate-300 bg-slate-100 dark:bg-slate-800/80 px-3 py-1 rounded-full border border-slate-200/60 dark:border-slate-700">
                                <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"/></svg>
                                Bulletin #{{ str_pad($announcement->id, 3, '0', STR_PAD_LEFT) }}
                            </span>

                            {{-- Publication Date --}}
                            <span class="inline-flex items-center gap-1.5 text-xs font-medium text-slate-500 dark:text-slate-400 bg-white dark:bg-slate-900 px-3 py-1 rounded-full border border-slate-200 dark:border-slate-800">
                                <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                {{ $announcement->created_at->format('F j, Y') }}
                            </span>

                            {{-- Reading Time --}}
                            <span class="inline-flex items-center gap-1 text-xs font-medium text-slate-400 dark:text-slate-500 px-2.5 py-1">
                                <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                {{ $readTime }} min read
                            </span>
                        </div>

                        {{-- Announcement Title --}}
                        <h1 class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white tracking-tight leading-[1.2] mb-4">
                            {{ $announcement->title }}
                        </h1>

                        {{-- Executive Subtitle / Key Takeaway --}}
                        @if($announcement->subheading)
                            <div class="p-5 sm:p-6 rounded-2xl bg-emerald-50/70 dark:bg-emerald-950/30 border-l-4 border-emerald-600 dark:border-emerald-500 border-y border-r border-emerald-200/60 dark:border-emerald-900/40">
                                <div class="flex items-center gap-2 mb-1.5 text-[11px] font-black uppercase tracking-widest text-emerald-800 dark:text-emerald-300">
                                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    <span>Summary & Scope</span>
                                </div>
                                <p class="text-base font-semibold text-slate-800 dark:text-slate-100 leading-relaxed">
                                    {{ $announcement->subheading }}
                                </p>
                            </div>
                        @endif

                        {{-- Event Schedule Action Card (If Event Date Present) --}}
                        @if($announcement->event_date)
                            <div class="mt-8 rounded-2xl bg-gradient-to-br from-slate-900 to-slate-800 text-white p-5 sm:p-6 shadow-lg border border-slate-700/60 relative overflow-hidden">
                                <div class="absolute -right-6 -bottom-6 w-36 h-36 bg-emerald-500/20 rounded-full blur-2xl pointer-events-none"></div>

                                <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-5 relative z-10">
                                    {{-- Left: Date Block & Time Details --}}
                                    <div class="flex items-center gap-4">
                                        {{-- Calendar Tile --}}
                                        <div class="w-16 h-18 rounded-2xl bg-white text-slate-900 overflow-hidden shadow-md shrink-0 text-center flex flex-col justify-between">
                                            <div class="bg-rose-600 text-white text-[10px] font-black uppercase py-1 tracking-wider">
                                                {{ $announcement->event_date->format('M') }}
                                            </div>
                                            <div class="text-xl font-black leading-none py-1.5">
                                                {{ $announcement->event_date->format('d') }}
                                            </div>
                                            <div class="bg-slate-100 text-slate-600 text-[9px] font-bold py-0.5 uppercase tracking-wider">
                                                {{ $announcement->event_date->format('D') }}
                                            </div>
                                        </div>

                                        {{-- Text Info --}}
                                        <div>
                                            <div class="flex items-center gap-2 mb-1">
                                                <span class="px-2 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-emerald-500/20 text-emerald-300 border border-emerald-400/30">
                                                    Activity Schedule
                                                </span>
                                                <span class="text-xs text-slate-400">Silang, Cavite</span>
                                            </div>
                                            <h4 class="text-base font-bold text-white leading-tight">
                                                {{ $announcement->event_date->format('l, F j, Y') }}
                                            </h4>
                                            <p class="text-xs text-slate-300 mt-0.5 flex items-center gap-1.5">
                                                <svg class="w-3.5 h-3.5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                @if($announcement->start_time)
                                                    <span class="font-semibold text-emerald-300">{{ \Carbon\Carbon::parse($announcement->start_time)->format('g:i A') }}</span>
                                                @else
                                                    <span>Regular Clinic Hours</span>
                                                @endif

                                                @if($announcement->end_time)
                                                    <span class="text-slate-400">—</span>
                                                    <span class="font-semibold text-emerald-300">{{ \Carbon\Carbon::parse($announcement->end_time)->format('g:i A') }}</span>
                                                @endif

                                                @if($announcement->end_date && $announcement->end_date->format('Y-m-d') !== $announcement->event_date->format('Y-m-d'))
                                                    <span class="text-slate-400">until {{ $announcement->end_date->format('M j, Y') }}</span>
                                                @endif
                                            </p>
                                        </div>
                                    </div>

                                    {{-- Right: Citizen Action (Add to Calendar) --}}
                                    @if($gCalUrl)
                                        <div class="shrink-0 flex items-center gap-2 print:hidden w-full md:w-auto">
                                            <a href="{{ $gCalUrl }}" 
                                               target="_blank" rel="noopener noreferrer"
                                               class="w-full md:w-auto inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold shadow-md shadow-emerald-900/30 transition-all active:scale-95">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                                <span>Add to Google Calendar</span>
                                            </a>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endif

                    </header>

                    {{-- Featured Media & Infographic Viewer --}}
                    @if($announcement->image_path || $slides->count() > 0)
                        <section class="bg-slate-100/70 dark:bg-slate-950/60 p-4 sm:p-8 border-b border-slate-100 dark:border-slate-800">
                            
                            {{-- Carousel Mode --}}
                            @if($announcement->display_type === 'carousel' && $slides->count() > 0)
                                <div class="rounded-3xl overflow-hidden bg-slate-900 border border-slate-300 dark:border-slate-800 shadow-xl relative"
                                     x-data="{ 
                                        activeSlide: 0, 
                                        total: {{ 1 + $slides->count() }} 
                                     }">
                                    
                                    {{-- Slide Stage --}}
                                    <div class="relative min-h-[380px] max-h-[640px] flex items-center justify-center bg-black/95">
                                        
                                        {{-- Main Slide (Index 0) --}}
                                        <div x-show="activeSlide === 0" class="w-full h-full flex flex-col items-center justify-center p-3 sm:p-6">
                                            @if(Str::endsWith($announcement->image_path, ['.mp4']))
                                                <video src="{{ asset('uploads/' . $announcement->image_path) }}" 
                                                       controls class="max-h-[580px] w-auto max-w-full rounded-2xl object-contain shadow-2xl"></video>
                                            @else
                                                <img src="{{ asset('uploads/' . $announcement->image_path) }}" 
                                                     alt="{{ $announcement->title }}" 
                                                     class="max-h-[580px] w-auto max-w-full rounded-2xl object-contain shadow-2xl cursor-zoom-in transition-transform duration-200 hover:scale-[1.01]"
                                                     @click="fullscreenMedia = '{{ asset('uploads/' . $announcement->image_path) }}'">
                                            @endif
                                        </div>

                                        {{-- Additional Slides --}}
                                        @foreach($slides as $index => $slide)
                                            <div x-show="activeSlide === {{ $index + 1 }}" class="w-full h-full flex flex-col items-center justify-center p-3 sm:p-6" style="display: none;">
                                                @if($slide->media_type === 'video_upload' || Str::endsWith($slide->image_path, ['.mp4']))
                                                    <video src="{{ asset('uploads/' . $slide->image_path) }}" 
                                                           controls class="max-h-[580px] w-auto max-w-full rounded-2xl object-contain shadow-2xl"></video>
                                                @else
                                                    <img src="{{ asset('uploads/' . $slide->image_path) }}" 
                                                         alt="" 
                                                         class="max-h-[580px] w-auto max-w-full rounded-2xl object-contain shadow-2xl cursor-zoom-in transition-transform duration-200 hover:scale-[1.01]"
                                                         @click="fullscreenMedia = '{{ asset('uploads/' . $slide->image_path) }}'">
                                                @endif
                                            </div>
                                        @endforeach

                                        {{-- Navigation Arrows --}}
                                        <div class="absolute inset-x-4 top-1/2 -translate-y-1/2 flex items-center justify-between pointer-events-none">
                                            <button type="button" 
                                                    @click="activeSlide = (activeSlide === 0 ? total - 1 : activeSlide - 1)" 
                                                    class="pointer-events-auto p-3 rounded-full bg-black/60 hover:bg-black/90 text-white shadow-xl backdrop-blur-md transition-all hover:scale-110 active:scale-95 focus:outline-none">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
                                            </button>
                                            <button type="button" 
                                                    @click="activeSlide = (activeSlide === total - 1 ? 0 : activeSlide + 1)" 
                                                    class="pointer-events-auto p-3 rounded-full bg-black/60 hover:bg-black/90 text-white shadow-xl backdrop-blur-md transition-all hover:scale-110 active:scale-95 focus:outline-none">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                                            </button>
                                        </div>

                                        {{-- Current Slide Badge --}}
                                        <div class="absolute bottom-4 left-1/2 -translate-x-1/2 px-3.5 py-1 rounded-full bg-black/70 backdrop-blur-md text-white text-xs font-bold shadow-md flex items-center gap-1.5">
                                            <svg class="w-3.5 h-3.5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                            <span>Slide <span x-text="activeSlide + 1"></span> of <span x-text="total"></span></span>
                                        </div>
                                    </div>

                                    {{-- Thumbnail Strip --}}
                                    <div class="p-3 bg-slate-900 border-t border-slate-800 flex items-center gap-2 overflow-x-auto justify-center">
                                        <button type="button" @click="activeSlide = 0" 
                                                class="w-14 h-14 rounded-xl overflow-hidden border-2 transition shrink-0"
                                                :class="activeSlide === 0 ? 'border-emerald-500 scale-105 shadow-md' : 'border-transparent opacity-60 hover:opacity-100'">
                                            <img src="{{ asset('uploads/' . $announcement->image_path) }}" class="w-full h-full object-cover">
                                        </button>
                                        @foreach($slides as $index => $slide)
                                            <button type="button" @click="activeSlide = {{ $index + 1 }}" 
                                                    class="w-14 h-14 rounded-xl overflow-hidden border-2 transition shrink-0"
                                                    :class="activeSlide === {{ $index + 1 }} ? 'border-emerald-500 scale-105 shadow-md' : 'border-transparent opacity-60 hover:opacity-100'">
                                                <img src="{{ asset('uploads/' . $slide->image_path) }}" class="w-full h-full object-cover">
                                            </button>
                                        @endforeach
                                    </div>
                                </div>
                            
                            {{-- Single Media / Infographic Presentation --}}
                            @else
                                <div class="rounded-3xl overflow-hidden bg-slate-900/5 dark:bg-slate-950 border border-slate-200/90 dark:border-slate-800 shadow-md flex flex-col items-center justify-center p-3 sm:p-5 relative group">
                                    
                                    @if(Str::endsWith($announcement->image_path, ['.mp4']))
                                        <video src="{{ asset('uploads/' . $announcement->image_path) }}" 
                                               controls class="max-h-[660px] w-auto max-w-full rounded-2xl object-contain bg-black shadow-lg"></video>
                                    @else
                                        <div class="relative overflow-hidden rounded-2xl max-h-[680px]">
                                            <img src="{{ asset('uploads/' . $announcement->image_path) }}" 
                                                 alt="{{ $announcement->title }}" 
                                                 class="max-h-[660px] w-auto max-w-full object-contain rounded-2xl shadow-md cursor-zoom-in transition-transform duration-300 group-hover:scale-[1.01]"
                                                 @click="fullscreenMedia = '{{ asset('uploads/' . $announcement->image_path) }}'">
                                            
                                            {{-- Hover overlay tools --}}
                                            <div class="absolute bottom-3 right-3 flex items-center gap-2 opacity-90 group-hover:opacity-100 transition print:hidden">
                                                <button type="button" 
                                                        @click="fullscreenMedia = '{{ asset('uploads/' . $announcement->image_path) }}'"
                                                        class="px-3 py-1.5 rounded-xl bg-black/75 hover:bg-black text-white text-xs font-bold backdrop-blur-md shadow-lg flex items-center gap-1.5 transition">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v6m3-3H7"/></svg>
                                                    <span>Enlarge</span>
                                                </button>
                                                <a href="{{ asset('uploads/' . $announcement->image_path) }}" 
                                                   download="{{ Str::slug($announcement->title) }}-official-notice"
                                                   class="px-3 py-1.5 rounded-xl bg-black/75 hover:bg-black text-white text-xs font-bold backdrop-blur-md shadow-lg flex items-center gap-1.5 transition"
                                                   title="Download high-resolution image">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                                    <span>Save</span>
                                                </a>
                                            </div>
                                        </div>
                                    @endif

                                    <div class="w-full flex items-center justify-between pt-3 px-2 text-[11px] text-slate-400 dark:text-slate-500 font-medium">
                                        <span>Official Municipal Visual Advisory</span>
                                        <span class="hidden sm:inline">Click image to inspect in full resolution</span>
                                    </div>
                                </div>
                            @endif

                        </section>
                    @endif

                    {{-- Main Editorial Content Body --}}
                    <div class="p-6 sm:p-10 lg:p-12">
                        <div class="transition-all duration-150"
                             :class="{
                                'text-sm leading-relaxed': fontSizeLevel === 0,
                                'text-base leading-relaxed sm:leading-8': fontSizeLevel === 1,
                                'text-lg leading-relaxed sm:leading-9': fontSizeLevel === 2
                             }">
                            <div class="text-slate-800 dark:text-slate-100 font-normal space-y-6">
                                {!! nl2br(e($announcement->content)) !!}
                            </div>
                        </div>
                    </div>

                    {{-- Additional Content Guidelines & Sections --}}
                    @if($sections->count() > 0)
                        <section class="border-t border-slate-200/90 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-950/40 p-6 sm:p-10 lg:p-12">
                            
                            <div class="flex items-center gap-3 mb-8">
                                <span class="w-2.5 h-7 bg-emerald-600 rounded-full shrink-0"></span>
                                <div>
                                    <h3 class="text-lg sm:text-xl font-black text-slate-900 dark:text-white tracking-tight">
                                        Detailed Guidelines & Program Mechanics
                                    </h3>
                                    <p class="text-xs text-slate-500 dark:text-slate-400">
                                        Sectioned breakdown and visual instructions for citizens
                                    </p>
                                </div>
                            </div>

                            <div class="space-y-8">
                                @foreach($sections as $index => $section)
                                    @php 
                                        $layout = $section->layout ?? 'left';
                                        $isCenter = ($layout === 'middle');
                                        $isRight = ($layout === 'right');
                                        $stepNumber = str_pad($index + 1, 2, '0', STR_PAD_LEFT);
                                    @endphp

                                    <div class="p-6 sm:p-8 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-sm relative overflow-hidden transition-all hover:shadow-md">
                                        
                                        {{-- Step Indicator Badge --}}
                                        <div class="flex items-center justify-between pb-4 mb-6 border-b border-slate-100 dark:border-slate-800/80">
                                            <div class="flex items-center gap-2">
                                                <span class="w-7 h-7 rounded-lg bg-emerald-100 dark:bg-emerald-950/70 text-emerald-700 dark:text-emerald-400 font-black text-xs flex items-center justify-center">
                                                    {{ $stepNumber }}
                                                </span>
                                                <span class="text-xs font-black uppercase tracking-wider text-slate-500 dark:text-slate-400">
                                                    Section {{ $stepNumber }}
                                                </span>
                                            </div>

                                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">
                                                RHU Directive
                                            </span>
                                        </div>

                                        {{-- Section Layout Grid --}}
                                        <div class="grid grid-cols-1 {{ $isCenter ? 'gap-6' : 'md:grid-cols-12 gap-8' }} items-center">
                                            
                                            {{-- Media Column --}}
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
                                                        <div class="aspect-video rounded-2xl overflow-hidden shadow-md bg-black border border-slate-200 dark:border-slate-700">
                                                            <iframe src="{{ $embedUrl }}" class="w-full h-full" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                                                        </div>
                                                    @elseif($section->image_path)
                                                        <div class="rounded-2xl overflow-hidden bg-slate-50 dark:bg-slate-950 border border-slate-200/90 dark:border-slate-800 flex items-center justify-center p-2.5 shadow-2xs group/img">
                                                            @if($section->media_type == 'video_upload' || Str::endsWith($section->image_path, '.mp4'))
                                                                <video src="{{ asset('uploads/' . $section->image_path) }}" controls class="max-h-[420px] w-full object-contain rounded-xl shadow-xs"></video>
                                                            @else
                                                                <img src="{{ asset('uploads/' . $section->image_path) }}" 
                                                                     alt="Guideline Media" 
                                                                     class="max-h-[420px] w-auto max-w-full rounded-xl object-contain shadow-xs cursor-zoom-in transition-transform duration-200 hover:scale-[1.01]"
                                                                     @click="fullscreenMedia = '{{ asset('uploads/' . $section->image_path) }}'">
                                                            @endif
                                                        </div>
                                                    @endif

                                                </div>
                                            @endif

                                            {{-- Text Column --}}
                                            <div class="{{ ($section->image_path || $section->video_url) ? ($isCenter ? 'w-full text-center' : ($isRight ? 'md:col-span-6 md:order-1' : 'md:col-span-6 md:order-2')) : 'col-span-12' }}">
                                                @if(!empty($section->content))
                                                    <div class="text-slate-800 dark:text-slate-200 text-base leading-relaxed whitespace-pre-line font-normal">
                                                        {{ $section->content }}
                                                    </div>
                                                @endif
                                            </div>

                                        </div>
                                    </div>
                                @endforeach
                            </div>

                        </section>
                    @endif

                    {{-- Document Footer / Issuer Verification --}}
                    <footer class="p-6 sm:p-10 bg-slate-50 dark:bg-slate-950/80 border-t border-slate-100 dark:border-slate-800/80 flex flex-col sm:flex-row items-center justify-between gap-6">
                        <div class="flex items-center gap-3.5">
                            <div class="w-10 h-10 rounded-full bg-emerald-100 dark:bg-emerald-950 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                            </div>
                            <div>
                                <h5 class="text-xs font-black uppercase tracking-wider text-slate-900 dark:text-white">
                                    Rural Health Unit
                                </h5>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400">
                                    Issued by the Rural Health Unit of Silang under Municipal Ordinance
                                </p>
                            </div>
                        </div>

                        <div class="flex items-center gap-2.5 print:hidden">
                            <button type="button" 
                                    @click="copyLink()"
                                    class="px-4 py-2 rounded-xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-xs font-bold text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-700/80 shadow-2xs transition">
                                <span x-text="copied ? 'Copied!' : 'Copy Link'"></span>
                            </button>
                            <a href="{{ route('announcements.index') }}" 
                               class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-sm shadow-emerald-900/20 transition">
                                All Bulletins
                            </a>
                        </div>
                    </footer>

                </article>

            </main>

            {{-- Sidebar Column (4 Cols on Desktop): Municipal Contact & Related Bulletins --}}
            <aside class="lg:col-span-4 space-y-6 print:hidden">
                
                {{-- Citizen Assistance & Hotlines Card --}}
                <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/90 dark:border-slate-800 p-6 sm:p-7 shadow-lg shadow-slate-200/30 dark:shadow-slate-950/30 space-y-5">
                    
                    <div class="flex items-center gap-3 pb-4 border-b border-slate-100 dark:border-slate-800">
                        <div class="w-10 h-10 rounded-2xl bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                        </div>
                        <div>
                            <h4 class="text-sm font-black text-slate-900 dark:text-white">Municipal Health Desk</h4>
                            <p class="text-xs text-slate-500 dark:text-slate-400">RHU Silang, Cavite</p>
                        </div>
                    </div>

                    <div class="space-y-3.5 text-xs">
                        <div class="flex items-start gap-3">
                            <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            <div>
                                <span class="font-bold text-slate-900 dark:text-white block">Location</span>
                                <span class="text-slate-500 dark:text-slate-400">M.H. del Pilar St., Poblacion, Silang, Cavite</span>
                            </div>
                        </div>

                        <div class="flex items-start gap-3">
                            <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <div>
                                <span class="font-bold text-slate-900 dark:text-white block">Operating Hours</span>
                                <span class="text-slate-500 dark:text-slate-400">Monday – Friday: 8:00 AM – 5:00 PM</span>
                                <span class="text-emerald-600 dark:text-emerald-400 font-semibold block text-[11px]">24/7 Lying-In & Emergency Services</span>
                            </div>
                        </div>

                        <div class="flex items-start gap-3">
                            <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                            <div>
                                <span class="font-bold text-slate-900 dark:text-white block">Official Hotline</span>
                                <a href="tel:0464140209" class="text-emerald-600 dark:text-emerald-400 font-bold hover:underline">
                                    (046) 414-0209
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="pt-2">
                        <a href="{{ route('appointment.create') }}" 
                           class="w-full inline-flex items-center justify-center gap-2 py-3 px-4 rounded-2xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-md shadow-emerald-900/20 transition active:scale-95">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            <span>Book an Appointment Online</span>
                        </a>
                    </div>
                </div>

                {{-- Related / Recent Announcements Card --}}
                @if(isset($relatedAnnouncements) && $relatedAnnouncements->count() > 0)
                    <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/90 dark:border-slate-800 p-6 sm:p-7 shadow-lg shadow-slate-200/30 dark:shadow-slate-950/30 space-y-5">
                        <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800">
                            <h4 class="text-xs font-black uppercase tracking-wider text-slate-900 dark:text-white flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                <span>Recent Health Bulletins</span>
                            </h4>
                            <a href="{{ route('announcements.index') }}" class="text-[11px] font-bold text-emerald-600 hover:underline">
                                View All
                            </a>
                        </div>

                        <div class="space-y-4">
                            @foreach($relatedAnnouncements as $rel)
                                @php
                                    $relCat = \App\Http\Controllers\PublicController::getAnnouncementCategory($rel);
                                @endphp
                                <a href="{{ route('announcements.show', $rel) }}" 
                                   class="group block p-3 rounded-2xl border border-slate-100 dark:border-slate-800 hover:border-emerald-300 dark:hover:border-emerald-700 bg-slate-50/50 dark:bg-slate-800/40 hover:bg-emerald-50/30 dark:hover:bg-emerald-950/20 transition-all">
                                    <div class="flex items-center gap-2 mb-1.5">
                                        <span class="px-2 py-0.5 rounded-full text-[9px] font-black uppercase tracking-wider {{ $relCat['bg'] }}">
                                            {{ $relCat['label'] }}
                                        </span>
                                        <span class="text-[10px] text-slate-400">
                                            {{ $rel->created_at->format('M d, Y') }}
                                        </span>
                                    </div>
                                    <h5 class="text-xs font-bold text-slate-900 dark:text-white group-hover:text-emerald-600 dark:group-hover:text-emerald-400 transition-colors line-clamp-2">
                                        {{ $rel->title }}
                                    </h5>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif

            </aside>

        </div>

    </div>

    {{-- Fullscreen High-Resolution Lightbox Modal --}}
    <div x-show="fullscreenMedia" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-[99999] bg-black/95 backdrop-blur-md flex items-center justify-center p-4 print:hidden"
         @keydown.escape.window="fullscreenMedia = null"
         style="display: none;">
        
        {{-- Close Button --}}
        <button type="button" 
                @click="fullscreenMedia = null" 
                class="absolute top-5 right-5 p-3 rounded-full bg-white/10 hover:bg-white/20 text-white transition-all hover:scale-105 active:scale-95"
                title="Close fullscreen preview (Esc)">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
        </button>

        {{-- Download from Lightbox --}}
        <a :href="fullscreenMedia" 
           download="rhu-announcement-image"
           class="absolute top-5 left-5 px-4 py-2 rounded-full bg-white/10 hover:bg-white/20 text-white text-xs font-bold transition flex items-center gap-2"
           title="Download this file">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
            <span>Save Image</span>
        </a>

        <img :src="fullscreenMedia" class="max-h-[90vh] max-w-[92vw] object-contain rounded-2xl shadow-2xl" @click.stop>
    </div>

</div>

{{-- Print-Ready Clean Municipal Layout --}}
<style>
@media print {
    nav, footer, aside, .print\:hidden, [class*="fixed"], [class*="absolute"] {
        box-shadow: none !important;
    }
    body {
        background: white !important;
        color: black !important;
    }
    article {
        border: none !important;
        box-shadow: none !important;
    }
}
</style>
@endsection
