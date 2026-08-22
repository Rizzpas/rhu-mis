@extends('layouts.app')

@section('content')
<div class="relative z-10 bg-transparent min-h-screen pt-4 pb-12 sm:pt-6 sm:pb-16"
     x-data="{
        search: '{{ addslashes($initialSearch) }}',
        category: '{{ $initialCategory }}',
        dateFilter: '{{ $initialDate }}',
        monthFilter: '{{ $initialMonth }}',
        sortOrder: 'newest',
        items: {{ Js::from($items) }},

        get filteredItems() {
            return this.items.filter(item => {
                // Category Filter
                if (this.category !== 'all' && item.category !== this.category) {
                    return false;
                }

                // Date Filter (Exact day YYYY-MM-DD)
                if (this.dateFilter && item.created_date_key !== this.dateFilter) {
                    return false;
                }

                // Month Filter (YYYY-MM)
                if (this.monthFilter && item.created_month_key !== this.monthFilter) {
                    return false;
                }

                // Keyword Search Filter
                if (this.search.trim() !== '') {
                    const q = this.search.toLowerCase().trim();
                    const titleMatch = (item.title || '').toLowerCase().includes(q);
                    const subMatch = (item.subheading || '').toLowerCase().includes(q);
                    const contentMatch = (item.content_raw || '').toLowerCase().includes(q);
                    if (!titleMatch && !subMatch && !contentMatch) {
                        return false;
                    }
                }

                return true;
            }).sort((a, b) => {
                if (this.sortOrder === 'oldest') {
                    return (a.id > b.id) ? 1 : -1;
                }
                return (a.id < b.id) ? 1 : -1;
            });
        },

        setCategory(cat) {
            this.category = cat;
        },

        setDate(date) {
            this.dateFilter = (this.dateFilter === date) ? '' : date;
            if (this.dateFilter) this.monthFilter = '';
        },

        setMonth(month) {
            this.monthFilter = (this.monthFilter === month) ? '' : month;
            if (this.monthFilter) this.dateFilter = '';
        },

        resetFilters() {
            this.search = '';
            this.category = 'all';
            this.dateFilter = '';
            this.monthFilter = '';
            this.sortOrder = 'newest';
        },

        hasActiveFilters() {
            return this.search.trim() !== '' || this.category !== 'all' || this.dateFilter !== '' || this.monthFilter !== '';
        }
     }">

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Top Navigation & Breadcrumb --}}
        <div class="flex flex-wrap items-center justify-between gap-4 mb-8">
            <a href="{{ route('welcome') }}"
               class="inline-flex items-center gap-2 text-sm font-semibold text-green-700 dark:text-green-400 hover:text-green-800 dark:hover:text-green-300 transition group">
                <span class="p-1.5 rounded-lg bg-green-100/70 dark:bg-green-900/40 text-green-700 dark:text-green-400 group-hover:bg-green-600 group-hover:text-white transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                </span>
                Back to Home
            </a>

            <div class="flex items-center gap-2 text-xs text-gray-500 dark:text-gray-400">
                <a href="{{ route('welcome') }}" class="hover:underline">Home</a>
                <span>/</span>
                <span class="text-gray-900 dark:text-white font-medium">Announcements</span>
            </div>
        </div>

        {{-- Page Header — 2-Column Split Hero --}}
        <div class="mb-12" data-reveal>
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 lg:gap-12 items-end">
                {{-- Left Column: Kicker + Heading --}}
                <div>
                    <span class="inline-flex items-center gap-2 px-3.5 py-1.5 bg-emerald-500/10 dark:bg-emerald-500/20 text-emerald-800 dark:text-emerald-300 text-xs font-bold rounded-full mb-5 uppercase tracking-wider border border-emerald-500/20 backdrop-blur-md shadow-xs">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                        Official Health Advisories & Updates
                    </span>
                    <h1 class="font-display text-4xl sm:text-5xl lg:text-[3.5rem] font-extrabold text-gray-900 dark:text-white tracking-tight leading-[1.1]">
                        Public Bulletins &<br class="hidden sm:block"> <span class="text-emerald-600 dark:text-emerald-400">Announcements</span>
                    </h1>
                </div>

                {{-- Right Column: Description (bottom-aligned to heading) --}}
                <div class="lg:pb-1">
                    <p class="text-gray-600 dark:text-gray-300 text-base sm:text-lg leading-relaxed max-w-xl">
                        Stay updated with community health advisories, vaccination drives, clinic schedules, and municipal announcements from the Rural Health Unit of Silang.
                    </p>
                </div>
            </div>
        </div>

        {{-- Main 2-Column Responsive Layout --}}
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">

            {{-- Left / Main Column (8 cols on lg) --}}
            <div class="lg:col-span-8 space-y-6">

                {{-- Interactive Search & Filters Bar --}}
                <div class="bg-white/90 dark:bg-gray-800/90 backdrop-blur-md rounded-2xl p-4 sm:p-5 border border-gray-200/80 dark:border-gray-700/80 shadow-sm space-y-4">
                    
                    {{-- Search Input and Sort --}}
                    <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
                        <div class="relative flex-1">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                            </div>
                            <input type="text"
                                   x-model="search"
                                   placeholder="Search by keywords, title, or topic..."
                                   class="w-full pl-10 pr-10 py-2.5 bg-gray-50 dark:bg-gray-900/60 border border-gray-200 dark:border-gray-700 rounded-xl text-sm text-gray-900 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-green-500 focus:border-green-500 transition">
                            <button type="button"
                                    x-show="search.length > 0"
                                    @click="search = ''"
                                    class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>

                        <div class="flex items-center gap-2 shrink-0">
                            <label class="text-xs font-semibold text-gray-500 dark:text-gray-400 whitespace-nowrap">Sort:</label>
                            <select x-model="sortOrder"
                                    class="py-2.5 px-3 bg-gray-50 dark:bg-gray-900/60 border border-gray-200 dark:border-gray-700 rounded-xl text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-green-500 focus:border-green-500 transition">
                                <option value="newest">Newest First</option>
                                <option value="oldest">Oldest First</option>
                            </select>
                        </div>
                    </div>

                    {{-- Category Tabs --}}
                    <div class="flex items-center gap-2 overflow-x-auto pb-1 scrollbar-none">
                        <button type="button"
                                @click="setCategory('all')"
                                :class="category === 'all' 
                                    ? 'bg-green-600 text-white shadow-xs' 
                                    : 'bg-gray-100 dark:bg-gray-700/60 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700'"
                                class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-xl text-xs font-semibold transition whitespace-nowrap">
                            All Topics
                            <span class="px-1.5 py-0.5 rounded-full text-[10px]"
                                  :class="category === 'all' ? 'bg-white/20 text-white' : 'bg-gray-200 dark:bg-gray-600 text-gray-700 dark:text-gray-300'">
                                {{ $categoryCounts['all'] ?? 0 }}
                            </span>
                        </button>

                        <button type="button"
                                @click="setCategory('health-alert')"
                                :class="category === 'health-alert' 
                                    ? 'bg-red-600 text-white shadow-xs' 
                                    : 'bg-red-50 dark:bg-red-950/30 text-red-700 dark:text-red-300 hover:bg-red-100 dark:hover:bg-red-900/40'"
                                class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-xl text-xs font-semibold transition whitespace-nowrap border border-red-200/50 dark:border-red-800/30">
                            <span class="w-2 h-2 rounded-full bg-red-500" :class="category === 'health-alert' ? 'bg-white' : ''"></span>
                            Health Alerts
                            <span class="px-1.5 py-0.5 rounded-full text-[10px]"
                                  :class="category === 'health-alert' ? 'bg-white/20 text-white' : 'bg-red-100 dark:bg-red-900/50 text-red-700 dark:text-red-300'">
                                {{ $categoryCounts['health-alert'] ?? 0 }}
                            </span>
                        </button>

                        <button type="button"
                                @click="setCategory('advisory')"
                                :class="category === 'advisory' 
                                    ? 'bg-amber-600 text-white shadow-xs' 
                                    : 'bg-amber-50 dark:bg-amber-950/30 text-amber-800 dark:text-amber-300 hover:bg-amber-100 dark:hover:bg-amber-900/40'"
                                class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-xl text-xs font-semibold transition whitespace-nowrap border border-amber-200/50 dark:border-amber-800/30">
                            <span class="w-2 h-2 rounded-full bg-amber-500" :class="category === 'advisory' ? 'bg-white' : ''"></span>
                            Advisories
                            <span class="px-1.5 py-0.5 rounded-full text-[10px]"
                                  :class="category === 'advisory' ? 'bg-white/20 text-white' : 'bg-amber-100 dark:bg-amber-900/50 text-amber-800 dark:text-amber-300'">
                                {{ $categoryCounts['advisory'] ?? 0 }}
                            </span>
                        </button>

                        <button type="button"
                                @click="setCategory('event')"
                                :class="category === 'event' 
                                    ? 'bg-blue-600 text-white shadow-xs' 
                                    : 'bg-blue-50 dark:bg-blue-950/30 text-blue-700 dark:text-blue-300 hover:bg-blue-100 dark:hover:bg-blue-900/40'"
                                class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-xl text-xs font-semibold transition whitespace-nowrap border border-blue-200/50 dark:border-blue-800/30">
                            <span class="w-2 h-2 rounded-full bg-blue-500" :class="category === 'event' ? 'bg-white' : ''"></span>
                            Events
                            <span class="px-1.5 py-0.5 rounded-full text-[10px]"
                                  :class="category === 'event' ? 'bg-white/20 text-white' : 'bg-blue-100 dark:bg-blue-900/50 text-blue-700 dark:text-blue-300'">
                                {{ $categoryCounts['event'] ?? 0 }}
                            </span>
                        </button>

                        <button type="button"
                                @click="setCategory('general')"
                                :class="category === 'general' 
                                    ? 'bg-emerald-600 text-white shadow-xs' 
                                    : 'bg-emerald-50 dark:bg-emerald-950/30 text-emerald-700 dark:text-emerald-300 hover:bg-emerald-100 dark:hover:bg-emerald-900/40'"
                                class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-xl text-xs font-semibold transition whitespace-nowrap border border-emerald-200/50 dark:border-emerald-800/30">
                            <span class="w-2 h-2 rounded-full bg-emerald-500" :class="category === 'general' ? 'bg-white' : ''"></span>
                            Announcements
                            <span class="px-1.5 py-0.5 rounded-full text-[10px]"
                                  :class="category === 'general' ? 'bg-white/20 text-white' : 'bg-emerald-100 dark:bg-emerald-900/50 text-emerald-700 dark:text-emerald-300'">
                                {{ $categoryCounts['general'] ?? 0 }}
                            </span>
                        </button>
                    </div>

                    {{-- Active Filter Pills summary bar --}}
                    <div x-show="hasActiveFilters()"
                         x-transition
                         class="pt-3 border-t border-gray-100 dark:border-gray-700/60 flex flex-wrap items-center justify-between gap-2">
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="text-xs font-medium text-gray-500 dark:text-gray-400">Active filters:</span>

                            <template x-if="search.trim() !== ''">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-green-50 dark:bg-green-900/30 text-green-700 dark:text-green-300 border border-green-200 dark:border-green-800/40 rounded-lg text-xs font-medium">
                                    Keyword: <strong x-text="search"></strong>
                                    <button type="button" @click="search = ''" class="hover:text-green-900 dark:hover:text-white">✕</button>
                                </span>
                            </template>

                            <template x-if="category !== 'all'">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-green-50 dark:bg-green-900/30 text-green-700 dark:text-green-300 border border-green-200 dark:border-green-800/40 rounded-lg text-xs font-medium capitalize">
                                    Category: <strong x-text="category.replace('-', ' ')"></strong>
                                    <button type="button" @click="category = 'all'" class="hover:text-green-900 dark:hover:text-white">✕</button>
                                </span>
                            </template>

                            <template x-if="dateFilter !== ''">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-teal-50 dark:bg-teal-900/30 text-teal-700 dark:text-teal-300 border border-teal-200 dark:border-teal-800/40 rounded-lg text-xs font-medium">
                                    Date: <strong x-text="dateFilter"></strong>
                                    <button type="button" @click="dateFilter = ''" class="hover:text-teal-900 dark:hover:text-white">✕</button>
                                </span>
                            </template>

                            <template x-if="monthFilter !== ''">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-teal-50 dark:bg-teal-900/30 text-teal-700 dark:text-teal-300 border border-teal-200 dark:border-teal-800/40 rounded-lg text-xs font-medium">
                                    Month: <strong x-text="monthFilter"></strong>
                                    <button type="button" @click="monthFilter = ''" class="hover:text-teal-900 dark:hover:text-white">✕</button>
                                </span>
                            </template>
                        </div>

                        <button type="button"
                                @click="resetFilters()"
                                class="text-xs font-bold text-red-600 dark:text-red-400 hover:underline">
                            Reset all
                        </button>
                    </div>
                </div>

                {{-- Status / Counter Header --}}
                <div class="flex items-center justify-between px-1">
                    <p class="text-xs font-semibold text-gray-500 dark:text-gray-400">
                        Showing <span class="text-gray-900 dark:text-white font-bold" x-text="filteredItems.length"></span> of <span x-text="items.length"></span> announcements
                    </p>
                </div>

                {{-- Vertical List View (Stacked Horizontal Cards) --}}
                <div class="space-y-4 sm:space-y-5">
                    <template x-for="(item, idx) in filteredItems" :key="item.id">
                        <article class="bg-white/90 dark:bg-gray-800/90 backdrop-blur-md rounded-2xl border border-gray-200/70 dark:border-gray-700/60 overflow-hidden shadow-xs hover:shadow-xl hover:border-green-500/40 transition-all duration-300 group flex flex-col md:flex-row">
                            
                            {{-- Media / Thumbnail side (Desktop left, Mobile top) --}}
                            <div class="md:w-64 lg:w-72 md:shrink-0 relative overflow-hidden bg-slate-100 dark:bg-slate-900 min-h-[190px] md:min-h-full">
                                <template x-if="item.has_image && !item.is_video">
                                    <img :src="item.image_url"
                                         :alt="item.title"
                                         class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105"
                                         loading="lazy">
                                </template>

                                <template x-if="item.has_image && item.is_video">
                                    <video :src="item.image_url"
                                           class="w-full h-full object-cover"
                                           muted loop autoplay playsinline></video>
                                </template>

                                <template x-if="!item.has_image">
                                    <div class="w-full h-full bg-gradient-to-br from-emerald-50 via-teal-50 to-green-100 dark:from-emerald-950/40 dark:via-teal-950/30 dark:to-gray-900 flex flex-col items-center justify-center p-6 text-center">
                                        <div class="w-12 h-12 rounded-2xl bg-white/80 dark:bg-gray-800/80 shadow-xs flex items-center justify-center text-emerald-600 dark:text-emerald-400 mb-2">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10.34 15.84c-.063.046-.129.088-.198.127a3.75 3.75 0 01-4.088-.288L3.25 13.5A2.25 2.25 0 012.25 11.75v-1.5a2.25 2.25 0 011-1.75l2.804-2.179a3.75 3.75 0 014.088-.288c.069.04.135.081.198.127m0 9.68l4.41 4.41a1.5 1.5 0 002.122 0l1.414-1.414a1.5 1.5 0 000-2.122L12 14.004m-1.66 1.836V7.996m0 0a3.75 3.75 0 013.75-3.75h1.5a2.25 2.25 0 012.25 2.25v6a2.25 2.25 0 01-2.25 2.25h-1.5a3.75 3.75 0 01-3.75-3.75z"/>
                                            </svg>
                                        </div>
                                        <span class="text-[11px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">RHU Bulletin</span>
                                    </div>
                                </template>

                                {{-- Category Tag overlay --}}
                                <div class="absolute top-3 left-3">
                                    <span class="inline-block px-2.5 py-1 text-[10px] font-bold rounded-lg uppercase tracking-wide backdrop-blur-md shadow-xs"
                                          :class="item.category_class"
                                          x-text="item.category_label"></span>
                                </div>
                            </div>

                            {{-- Content details side --}}
                            <div class="p-5 sm:p-6 flex flex-col justify-between flex-1">
                                <div>
                                    {{-- Meta row: Published Date + Event Date --}}
                                    <div class="flex flex-wrap items-center gap-x-3 gap-y-1 text-xs text-gray-500 dark:text-gray-400 mb-2.5">
                                        <span class="inline-flex items-center gap-1.5 font-medium">
                                            <svg class="w-3.5 h-3.5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm.75-13a.75.75 0 00-1.5 0v5c0 .414.336.75.75.75h4a.75.75 0 000-1.5h-3.25V5z" clip-rule="evenodd"/>
                                            </svg>
                                            <span x-text="item.created_at_formatted"></span>
                                            <span class="text-gray-400" x-text="'(' + item.relative_time + ')'"></span>
                                        </span>

                                        <template x-if="item.event_date">
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 font-semibold text-[11px]">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                                </svg>
                                                Event: <span x-text="item.event_date"></span>
                                                <template x-if="item.start_time">
                                                    <span x-text="'@ ' + item.start_time"></span>
                                                </template>
                                            </span>
                                        </template>
                                    </div>

                                    {{-- Title --}}
                                    <h2 class="font-display text-xl sm:text-2xl font-bold text-gray-900 dark:text-white leading-snug mb-2 group-hover:text-green-600 dark:group-hover:text-green-400 transition-colors">
                                        <a :href="item.url" x-text="item.title"></a>
                                    </h2>

                                    {{-- Subheading (if any) --}}
                                    <template x-if="item.subheading">
                                        <p class="text-xs font-semibold text-emerald-700 dark:text-emerald-400 uppercase tracking-wider mb-2" x-text="item.subheading"></p>
                                    </template>

                                    {{-- Description snippet --}}
                                    <p class="text-gray-600 dark:text-gray-300 text-sm leading-relaxed line-clamp-3 mb-4"
                                       x-text="item.content_plain"></p>
                                </div>

                                {{-- Action Footer --}}
                                <div class="pt-4 border-t border-gray-100 dark:border-gray-700/60 flex items-center justify-between">
                                    <a :href="item.url"
                                       class="inline-flex items-center gap-2 text-sm font-bold text-green-600 dark:text-green-400 group-hover:text-green-700 dark:group-hover:text-green-300 group-hover:gap-3 transition-all">
                                        <span>Read Full Announcement</span>
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                                        </svg>
                                    </a>

                                    <a :href="item.url"
                                       class="p-2 rounded-xl bg-gray-50 dark:bg-gray-700/50 text-gray-400 hover:text-green-600 dark:hover:text-green-400 transition">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                                        </svg>
                                    </a>
                                </div>
                            </div>
                        </article>
                    </template>
                </div>

                {{-- Empty State --}}
                <div x-show="filteredItems.length === 0"
                     class="bg-white/80 dark:bg-gray-800/80 backdrop-blur-md rounded-2xl p-10 text-center border border-gray-200 dark:border-gray-700 shadow-sm"
                     style="display: none;">
                    <div class="w-16 h-16 rounded-full bg-gray-100 dark:bg-gray-700 text-gray-400 dark:text-gray-500 mx-auto flex items-center justify-center mb-4">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <h3 class="font-display text-lg font-bold text-gray-900 dark:text-white mb-1">No Announcements Found</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 max-w-md mx-auto mb-5">
                        We couldn't find any announcements matching your current search or date filters.
                    </p>
                    <button type="button"
                            @click="resetFilters()"
                            class="inline-flex items-center gap-2 px-5 py-2.5 bg-green-600 hover:bg-green-700 text-white rounded-xl font-semibold text-sm transition shadow-xs">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                        Clear all filters
                    </button>
                </div>

            </div>

            {{-- Right / Sidebar Column (4 cols on lg) --}}
            <div class="lg:col-span-4 space-y-6 lg:sticky lg:top-24">

                {{-- Date Lookup & Archive Timeline Card --}}
                <div class="bg-white/90 dark:bg-gray-800/90 backdrop-blur-md rounded-2xl p-5 border border-gray-200/80 dark:border-gray-700/80 shadow-sm">
                    <div class="flex items-center gap-3 mb-4 pb-3 border-b border-gray-100 dark:border-gray-700/60">
                        <div class="w-9 h-9 rounded-xl bg-teal-100 dark:bg-teal-900/40 text-teal-600 dark:text-teal-400 flex items-center justify-center">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-display font-bold text-gray-900 dark:text-white text-base">Look Back by Date</h3>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Inspect announcements by day or month</p>
                        </div>
                    </div>

                    {{-- Pick Exact Day --}}
                    <div class="mb-5">
                        <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1.5">
                            Filter by Specific Date
                        </label>
                        <div class="flex items-center gap-2">
                            <input type="date"
                                   x-model="dateFilter"
                                   class="w-full px-3 py-2 bg-gray-50 dark:bg-gray-900/60 border border-gray-200 dark:border-gray-700 rounded-xl text-xs text-gray-900 dark:text-white focus:ring-2 focus:ring-green-500 focus:border-green-500">
                            <button type="button"
                                    x-show="dateFilter !== ''"
                                    @click="dateFilter = ''"
                                    class="p-2 rounded-xl bg-gray-100 dark:bg-gray-700 text-gray-500 hover:text-red-600 text-xs font-bold transition"
                                    title="Clear date">
                                ✕
                            </button>
                        </div>
                    </div>

                    {{-- Timeline Dates List --}}
                    @if(!empty($datesArchive))
                    <div class="mb-5">
                        <span class="block text-xs font-bold uppercase tracking-wider text-gray-400 dark:text-gray-500 mb-2.5">
                            Recent Announcement Dates
                        </span>
                        <div class="space-y-1.5 max-h-56 overflow-y-auto pr-1">
                            @foreach(array_slice($datesArchive, 0, 8) as $dateItem)
                            <button type="button"
                                    @click="setDate('{{ $dateItem['date'] }}')"
                                    :class="dateFilter === '{{ $dateItem['date'] }}'
                                        ? 'bg-teal-600 text-white shadow-xs'
                                        : 'bg-gray-50 dark:bg-gray-900/40 text-gray-700 dark:text-gray-300 hover:bg-teal-50 dark:hover:bg-teal-950/30 hover:text-teal-700 dark:hover:text-teal-300'"
                                    class="w-full flex items-center justify-between p-2.5 rounded-xl text-xs font-medium transition text-left">
                                <div class="flex items-center gap-2.5">
                                    <span class="w-7 h-7 rounded-lg flex flex-col items-center justify-center font-bold text-[10px]"
                                          :class="dateFilter === '{{ $dateItem['date'] }}' ? 'bg-white/20 text-white' : 'bg-teal-100/70 dark:bg-teal-900/40 text-teal-700 dark:text-teal-400'">
                                        <span>{{ $dateItem['day'] }}</span>
                                    </span>
                                    <span>{{ $dateItem['display'] }}</span>
                                </div>
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold"
                                      :class="dateFilter === '{{ $dateItem['date'] }}' ? 'bg-white/20 text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-600 dark:text-gray-400'">
                                    {{ $dateItem['count'] }}
                                </span>
                            </button>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    {{-- Monthly Archive --}}
                    @if(!empty($monthsArchive))
                    <div class="pt-4 border-t border-gray-100 dark:border-gray-700/60">
                        <span class="block text-xs font-bold uppercase tracking-wider text-gray-400 dark:text-gray-500 mb-2">
                            Monthly Archive
                        </span>
                        <div class="flex flex-wrap gap-1.5">
                            @foreach($monthsArchive as $monthItem)
                            <button type="button"
                                    @click="setMonth('{{ $monthItem['month'] }}')"
                                    :class="monthFilter === '{{ $monthItem['month'] }}'
                                        ? 'bg-green-600 text-white shadow-xs'
                                        : 'bg-gray-100 dark:bg-gray-700/50 text-gray-700 dark:text-gray-300 hover:bg-green-50 hover:text-green-700 dark:hover:bg-green-900/30 dark:hover:text-green-300'"
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium transition">
                                <span>{{ $monthItem['display'] }}</span>
                                <span class="opacity-75 text-[10px]">({{ $monthItem['count'] }})</span>
                            </button>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>

                {{-- Topic Categories Widget --}}
                <div class="bg-white/90 dark:bg-gray-800/90 backdrop-blur-md rounded-2xl p-5 border border-gray-200/80 dark:border-gray-700/80 shadow-sm">
                    <h3 class="font-display font-bold text-gray-900 dark:text-white text-sm mb-3">Filter by Category</h3>
                    <div class="space-y-1.5">
                        <button type="button"
                                @click="setCategory('all')"
                                :class="category === 'all' ? 'bg-green-50 dark:bg-green-950/40 text-green-700 dark:text-green-300 font-bold' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700/40'"
                                class="w-full flex items-center justify-between p-2 rounded-xl text-xs transition text-left">
                            <span class="flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-gray-400"></span>
                                All Announcements
                            </span>
                            <span class="font-semibold">{{ $categoryCounts['all'] ?? 0 }}</span>
                        </button>

                        <button type="button"
                                @click="setCategory('health-alert')"
                                :class="category === 'health-alert' ? 'bg-red-50 dark:bg-red-950/40 text-red-700 dark:text-red-300 font-bold' : 'text-gray-600 dark:text-gray-400 hover:bg-red-50/50 dark:hover:bg-red-950/20'"
                                class="w-full flex items-center justify-between p-2 rounded-xl text-xs transition text-left">
                            <span class="flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-red-500"></span>
                                Health Alerts
                            </span>
                            <span class="font-semibold">{{ $categoryCounts['health-alert'] ?? 0 }}</span>
                        </button>

                        <button type="button"
                                @click="setCategory('advisory')"
                                :class="category === 'advisory' ? 'bg-amber-50 dark:bg-amber-950/40 text-amber-800 dark:text-amber-300 font-bold' : 'text-gray-600 dark:text-gray-400 hover:bg-amber-50/50 dark:hover:bg-amber-950/20'"
                                class="w-full flex items-center justify-between p-2 rounded-xl text-xs transition text-left">
                            <span class="flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                                Public Advisories
                            </span>
                            <span class="font-semibold">{{ $categoryCounts['advisory'] ?? 0 }}</span>
                        </button>

                        <button type="button"
                                @click="setCategory('event')"
                                :class="category === 'event' ? 'bg-blue-50 dark:bg-blue-950/40 text-blue-700 dark:text-blue-300 font-bold' : 'text-gray-600 dark:text-gray-400 hover:bg-blue-50/50 dark:hover:bg-blue-950/20'"
                                class="w-full flex items-center justify-between p-2 rounded-xl text-xs transition text-left">
                            <span class="flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-blue-500"></span>
                                Events & Programs
                            </span>
                            <span class="font-semibold">{{ $categoryCounts['event'] ?? 0 }}</span>
                        </button>

                        <button type="button"
                                @click="setCategory('general')"
                                :class="category === 'general' ? 'bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-300 font-bold' : 'text-gray-600 dark:text-gray-400 hover:bg-emerald-50/50 dark:hover:bg-emerald-950/20'"
                                class="w-full flex items-center justify-between p-2 rounded-xl text-xs transition text-left">
                            <span class="flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                General Notices
                            </span>
                            <span class="font-semibold">{{ $categoryCounts['general'] ?? 0 }}</span>
                        </button>
                    </div>
                </div>

                {{-- Emergency & Clinic Info Mini Card --}}
                <div class="rounded-2xl p-5 bg-gradient-to-br from-green-900 to-teal-950 text-white shadow-md relative overflow-hidden">
                    <div class="relative z-10">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-green-300">Need Assistance?</span>
                        <h4 class="font-display font-bold text-base mt-0.5 mb-2">RHU Emergency Support</h4>
                        <p class="text-xs text-green-100/90 leading-relaxed mb-4">
                            For urgent medical conditions, our Lying-in & Emergency station is available 24/7.
                        </p>
                        <div class="space-y-2 text-xs">
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-green-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                <span>Hotline: <strong>911 | (046) 432-1234</strong></span>
                            </div>
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-green-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <span>Clinic: <strong>Mon - Fri | 8:00 AM - 5:00 PM</strong></span>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

        </div>

    </div>
</div>
@endsection
