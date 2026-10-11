@extends('layouts.admin')

@section('header', 'Manage Announcements')

@section('content')
<div class="space-y-6 pb-12">
    <!-- Breadcrumb Navigation -->
    <nav class="flex items-center gap-2 text-xs font-medium text-slate-500 dark:text-slate-400 py-2 border-b border-slate-200/60 dark:border-slate-800" aria-label="Breadcrumb">
        <a href="{{ route('admin.dashboard') }}" class="hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors flex items-center gap-1.5 shrink-0">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
            <span>Home</span>
        </a>
        <span class="text-slate-300 dark:text-slate-700">/</span>
        <span class="hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors">Communications</span>
        <span class="text-slate-300 dark:text-slate-700">/</span>
        <span class="text-slate-700 dark:text-slate-300 font-semibold">Announcements</span>
    </nav>

    <!-- Header Section (Content Management Style) -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 pb-4 border-b border-slate-200/80 dark:border-slate-800">
        <div>
            <h1 class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white tracking-tight flex items-center gap-2.5">
                <span class="w-9 h-9 sm:w-10 sm:h-10 rounded-2xl bg-emerald-100/80 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0 border border-emerald-500/20 shadow-2xs">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z" />
                    </svg>
                </span>
                <span>System Announcements</span>
            </h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Broadcast operational bulletins, schedule updates, and emergency notices across all portals.</p>
        </div>

        <div class="flex items-center gap-2.5">
            <span class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 text-slate-600 dark:text-slate-400 text-xs font-semibold shadow-2xs">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                <span>{{ $announcements->total() }} Bulletins</span>
            </span>
        </div>
    </div>

    <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-xl border border-slate-200 dark:border-slate-800 overflow-hidden" 
         x-data="{ 
             selectedAnnouncements: [], 
             showBulkModal: false, 
             showBulkDeleteModal: false, 
             showBulkStatusModal: false, 
             bulkStatusValue: 'published',
             toggleAll(checked) {
                 if (checked) {
                     this.selectedAnnouncements = Array.from(document.querySelectorAll('.announcement-checkbox')).map(cb => cb.value);
                 } else {
                     this.selectedAnnouncements = [];
                 }
             },
             isSelected(id) {
                 return this.selectedAnnouncements.includes(String(id)) || this.selectedAnnouncements.includes(Number(id));
             }
         }">
    <!-- Premium Header -->
    <div class="px-6 py-5 border-b border-slate-200 dark:border-slate-800 flex flex-col md:flex-row justify-between items-start md:items-center gap-6 bg-white dark:bg-slate-900 relative overflow-hidden">
        <!-- Subtle background pattern -->
        <div class="absolute inset-0 opacity-5 dark:opacity-10 pointer-events-none">
            <svg class="h-full w-full" fill="none" viewBox="0 0 100 100" preserveAspectRatio="none">
                <path d="M0 100 L100 0" stroke="currentColor" class="text-slate-900 dark:text-white" stroke-width="0.1" />
                <path d="M0 0 L100 100" stroke="currentColor" class="text-slate-900 dark:text-white" stroke-width="0.1" />
            </svg>
        </div>

        <div class="relative z-10">
            <h3 class="text-lg font-bold text-slate-900 dark:text-white tracking-tight">System Announcements</h3>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Broadcast important news and events to all staff and portals.</p>
        </div>

        <div class="flex items-center gap-3 relative z-10">
            <div x-show="selectedAnnouncements.length > 0" x-cloak x-transition class="flex items-center gap-2">
                <!-- Selected Count Badge -->
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-teal-50 dark:bg-teal-950/50 border border-teal-200 dark:border-teal-800 text-teal-700 dark:text-teal-300 text-xs font-bold shadow-2xs">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                    <span x-text="selectedAnnouncements.length"></span> selected
                </span>

                <!-- Bulk Change Status -->
                <button @click="showBulkStatusModal = true"
                    class="h-10 px-3.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs shadow-md shadow-blue-600/20 transition-all flex items-center gap-1.5 active:scale-95 cursor-pointer"
                    title="Change Status for Selected">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                    <span>Change Status</span>
                </button>

                <!-- Bulk Delete (Archive) -->
                @can('delete-announcements')
                <button @click="showBulkDeleteModal = true"
                    class="h-10 px-3.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs shadow-md shadow-rose-600/20 transition-all flex items-center gap-1.5 active:scale-95 cursor-pointer"
                    title="Archive Selected">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                    <span>Archive</span>
                </button>
                @endcan
            </div>

            <a href="{{ route('admin.announcements.create') }}" class="h-10 px-5 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white font-bold text-sm shadow-md shadow-emerald-600/25 hover:shadow-emerald-600/35 transition-all flex items-center gap-2 active:scale-95 cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
                <span>Post New</span>
            </a>
        </div>
    </div>
    
    <!-- Advanced Search and Filter Bar -->
    <div class="px-6 py-4 bg-slate-50/80 dark:bg-slate-800/40 border-b border-slate-200/80 dark:border-slate-800">
        <form method="GET" action="{{ route('admin.announcements.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-3.5 items-end">
            <!-- Search Keyword -->
            <div class="sm:col-span-2 lg:col-span-2">
                <label class="block text-[11px] font-extrabold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1.5">Search Keyword</label>
                <div class="relative group">
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Keywords..." 
                        class="h-11 px-3.5 block w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-white shadow-2xs focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 text-xs sm:text-sm font-medium transition-all placeholder:text-slate-400">
                </div>
            </div>

            <!-- Search In -->
            <div class="sm:col-span-1 lg:col-span-2">
                <label class="block text-[11px] font-extrabold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1.5">Search In</label>
                <x-select 
                    name="search_by" 
                    :options="[
                        'all' => 'All Fields',
                        'title' => 'Title',
                        'subheading' => 'Subheading',
                        'content' => 'Main Content'
                    ]" 
                    :value="request('search_by', 'all')"
                    class="!h-11 !py-0 flex items-center font-semibold text-xs sm:text-sm"
                />
            </div>

            <!-- From Date Filter -->
            <div class="sm:col-span-1 lg:col-span-2 relative"
                 x-data="{
                     showPicker: false,
                     selectedDate: '{{ request('date_from', '') }}',
                     currentMonth: 0,
                     currentYear: 2026,
                     monthNames: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
                     days: ['Su', 'Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa'],
                     init() {
                         let d = this.selectedDate ? new Date(this.selectedDate + 'T00:00:00') : new Date();
                         if (isNaN(d.getTime())) d = new Date();
                         this.currentMonth = d.getMonth();
                         this.currentYear = d.getFullYear();
                     },
                     get formattedDate() {
                         if (!this.selectedDate) return '';
                         let d = new Date(this.selectedDate + 'T00:00:00');
                         if (isNaN(d.getTime())) return this.selectedDate;
                         return d.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
                     },
                     get daysInMonth() { return new Date(this.currentYear, this.currentMonth + 1, 0).getDate(); },
                     get startDayOfWeek() { return new Date(this.currentYear, this.currentMonth, 1).getDay(); },
                     prevMonth() { if (this.currentMonth === 0) { this.currentMonth = 11; this.currentYear--; } else { this.currentMonth--; } },
                     nextMonth() { if (this.currentMonth === 11) { this.currentMonth = 0; this.currentYear++; } else { this.currentMonth++; } },
                     selectDay(day) {
                         let m = String(this.currentMonth + 1).padStart(2, '0');
                         let d = String(day).padStart(2, '0');
                         this.selectedDate = `${this.currentYear}-${m}-${d}`;
                         this.showPicker = false;
                     },
                     clearDate() { this.selectedDate = ''; this.showPicker = false; },
                     selectToday() {
                         let today = new Date();
                         this.currentMonth = today.getMonth();
                         this.currentYear = today.getFullYear();
                         let m = String(today.getMonth() + 1).padStart(2, '0');
                         let d = String(today.getDate()).padStart(2, '0');
                         this.selectedDate = `${today.getFullYear()}-${m}-${d}`;
                         this.showPicker = false;
                     },
                     isSelected(day) {
                         if (!this.selectedDate) return false;
                         let m = String(this.currentMonth + 1).padStart(2, '0');
                         let d = String(day).padStart(2, '0');
                         return this.selectedDate === `${this.currentYear}-${m}-${d}`;
                     },
                     isToday(day) {
                         let today = new Date();
                         return today.getFullYear() === this.currentYear && today.getMonth() === this.currentMonth && today.getDate() === day;
                     }
                 }">
                <label class="block text-[11px] font-extrabold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1.5">From Date</label>
                <input type="hidden" name="date_from" :value="selectedDate">

                <div @click="showPicker = !showPicker" 
                     class="h-11 px-3 flex items-center justify-between w-full rounded-xl border bg-white dark:bg-slate-900 shadow-2xs text-xs font-medium transition-all cursor-pointer select-none"
                     :class="showPicker ? 'border-emerald-500 ring-2 ring-emerald-500/20' : 'border-slate-300 dark:border-slate-700 hover:border-slate-400 dark:hover:border-slate-600'">
                    <span x-text="selectedDate ? formattedDate : 'From Date'" 
                          class="truncate font-semibold"
                          :class="selectedDate ? 'text-slate-900 dark:text-white' : 'text-slate-400 dark:text-slate-500'"></span>

                    <div class="flex items-center gap-1">
                        <button type="button" x-show="selectedDate" @click.stop="clearDate()" class="p-0.5 rounded text-slate-400 hover:text-rose-500 transition" title="Clear">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                        <svg class="w-4 h-4 shrink-0 transition-colors text-slate-400" :class="showPicker ? 'text-emerald-600 dark:text-emerald-400' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                </div>

                <div x-show="showPicker" @click.away="showPicker = false" 
                     x-transition:enter="transition ease-out duration-150"
                     x-transition:enter-start="opacity-0 scale-95 -translate-y-1"
                     x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                     style="display: none;"
                     class="absolute z-50 mt-1.5 left-0 sm:left-auto w-[275px] p-3 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl shadow-xl">
                    <div class="flex items-center justify-between gap-1 mb-2.5">
                        <button type="button" @click="prevMonth()" class="p-1 rounded-md text-slate-500 hover:text-slate-900 dark:text-slate-400 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-700 transition"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg></button>
                        <span class="text-xs font-bold text-slate-800 dark:text-slate-200" x-text="monthNames[currentMonth] + ' ' + currentYear"></span>
                        <button type="button" @click="nextMonth()" class="p-1 rounded-md text-slate-500 hover:text-slate-900 dark:text-slate-400 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-700 transition"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg></button>
                    </div>
                    <div class="grid grid-cols-7 gap-1 text-center mb-1 text-[10px] font-bold text-slate-400 dark:text-slate-500">
                        <template x-for="day in days" :key="day"><span x-text="day"></span></template>
                    </div>
                    <div class="grid grid-cols-7 gap-1 text-center text-xs">
                        <template x-for="blank in startDayOfWeek" :key="'blank-' + blank"><span class="p-1"></span></template>
                        <template x-for="day in daysInMonth" :key="'day-' + day">
                            <button type="button" @click="selectDay(day)" 
                                    :class="{
                                        'bg-emerald-600 text-white font-bold shadow-xs': isSelected(day),
                                        'ring-1 ring-emerald-500 font-bold text-emerald-600 dark:text-emerald-400': isToday(day) && !isSelected(day),
                                        'text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700': !isSelected(day) && !isToday(day)
                                    }"
                                    class="p-1 rounded-md text-xs font-medium transition" x-text="day"></button>
                        </template>
                    </div>
                    <div class="flex items-center justify-between pt-2 mt-2 border-t border-slate-100 dark:border-slate-700/60 text-xs">
                        <button type="button" @click="clearDate()" class="text-slate-500 hover:text-rose-600 font-semibold transition">Clear</button>
                        <button type="button" @click="selectToday()" class="text-emerald-600 dark:text-emerald-400 hover:underline font-bold transition">Today</button>
                    </div>
                </div>
            </div>

            <!-- To Date Filter -->
            <div class="sm:col-span-1 lg:col-span-2 relative"
                 x-data="{
                     showPicker: false,
                     selectedDate: '{{ request('date_to', '') }}',
                     currentMonth: 0,
                     currentYear: 2026,
                     monthNames: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
                     days: ['Su', 'Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa'],
                     init() {
                         let d = this.selectedDate ? new Date(this.selectedDate + 'T00:00:00') : new Date();
                         if (isNaN(d.getTime())) d = new Date();
                         this.currentMonth = d.getMonth();
                         this.currentYear = d.getFullYear();
                     },
                     get formattedDate() {
                         if (!this.selectedDate) return '';
                         let d = new Date(this.selectedDate + 'T00:00:00');
                         if (isNaN(d.getTime())) return this.selectedDate;
                         return d.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
                     },
                     get daysInMonth() { return new Date(this.currentYear, this.currentMonth + 1, 0).getDate(); },
                     get startDayOfWeek() { return new Date(this.currentYear, this.currentMonth, 1).getDay(); },
                     prevMonth() { if (this.currentMonth === 0) { this.currentMonth = 11; this.currentYear--; } else { this.currentMonth--; } },
                     nextMonth() { if (this.currentMonth === 11) { this.currentMonth = 0; this.currentYear++; } else { this.currentMonth++; } },
                     selectDay(day) {
                         let m = String(this.currentMonth + 1).padStart(2, '0');
                         let d = String(day).padStart(2, '0');
                         this.selectedDate = `${this.currentYear}-${m}-${d}`;
                         this.showPicker = false;
                     },
                     clearDate() { this.selectedDate = ''; this.showPicker = false; },
                     selectToday() {
                         let today = new Date();
                         this.currentMonth = today.getMonth();
                         this.currentYear = today.getFullYear();
                         let m = String(today.getMonth() + 1).padStart(2, '0');
                         let d = String(today.getDate()).padStart(2, '0');
                         this.selectedDate = `${today.getFullYear()}-${m}-${d}`;
                         this.showPicker = false;
                     },
                     isSelected(day) {
                         if (!this.selectedDate) return false;
                         let m = String(this.currentMonth + 1).padStart(2, '0');
                         let d = String(day).padStart(2, '0');
                         return this.selectedDate === `${this.currentYear}-${m}-${d}`;
                     },
                     isToday(day) {
                         let today = new Date();
                         return today.getFullYear() === this.currentYear && today.getMonth() === this.currentMonth && today.getDate() === day;
                     }
                 }">
                <label class="block text-[11px] font-extrabold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1.5">To Date</label>
                <input type="hidden" name="date_to" :value="selectedDate">

                <div @click="showPicker = !showPicker" 
                     class="h-11 px-3 flex items-center justify-between w-full rounded-xl border bg-white dark:bg-slate-900 shadow-2xs text-xs font-medium transition-all cursor-pointer select-none"
                     :class="showPicker ? 'border-emerald-500 ring-2 ring-emerald-500/20' : 'border-slate-300 dark:border-slate-700 hover:border-slate-400 dark:hover:border-slate-600'">
                    <span x-text="selectedDate ? formattedDate : 'To Date'" 
                          class="truncate font-semibold"
                          :class="selectedDate ? 'text-slate-900 dark:text-white' : 'text-slate-400 dark:text-slate-500'"></span>

                    <div class="flex items-center gap-1">
                        <button type="button" x-show="selectedDate" @click.stop="clearDate()" class="p-0.5 rounded text-slate-400 hover:text-rose-500 transition" title="Clear">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                        <svg class="w-4 h-4 shrink-0 transition-colors text-slate-400" :class="showPicker ? 'text-emerald-600 dark:text-emerald-400' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                </div>

                <div x-show="showPicker" @click.away="showPicker = false" 
                     x-transition:enter="transition ease-out duration-150"
                     x-transition:enter-start="opacity-0 scale-95 -translate-y-1"
                     x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                     style="display: none;"
                     class="absolute z-50 mt-1.5 left-0 sm:left-auto sm:right-0 w-[275px] p-3 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl shadow-xl">
                    <div class="flex items-center justify-between gap-1 mb-2.5">
                        <button type="button" @click="prevMonth()" class="p-1 rounded-md text-slate-500 hover:text-slate-900 dark:text-slate-400 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-700 transition"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg></button>
                        <span class="text-xs font-bold text-slate-800 dark:text-slate-200" x-text="monthNames[currentMonth] + ' ' + currentYear"></span>
                        <button type="button" @click="nextMonth()" class="p-1 rounded-md text-slate-500 hover:text-slate-900 dark:text-slate-400 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-700 transition"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg></button>
                    </div>
                    <div class="grid grid-cols-7 gap-1 text-center mb-1 text-[10px] font-bold text-slate-400 dark:text-slate-500">
                        <template x-for="day in days" :key="day"><span x-text="day"></span></template>
                    </div>
                    <div class="grid grid-cols-7 gap-1 text-center text-xs">
                        <template x-for="blank in startDayOfWeek" :key="'blank-' + blank"><span class="p-1"></span></template>
                        <template x-for="day in daysInMonth" :key="'day-' + day">
                            <button type="button" @click="selectDay(day)" 
                                    :class="{
                                        'bg-emerald-600 text-white font-bold shadow-xs': isSelected(day),
                                        'ring-1 ring-emerald-500 font-bold text-emerald-600 dark:text-emerald-400': isToday(day) && !isSelected(day),
                                        'text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700': !isSelected(day) && !isToday(day)
                                    }"
                                    class="p-1 rounded-md text-xs font-medium transition" x-text="day"></button>
                        </template>
                    </div>
                    <div class="flex items-center justify-between pt-2 mt-2 border-t border-slate-100 dark:border-slate-700/60 text-xs">
                        <button type="button" @click="clearDate()" class="text-slate-500 hover:text-rose-600 font-semibold transition">Clear</button>
                        <button type="button" @click="selectToday()" class="text-emerald-600 dark:text-emerald-400 hover:underline font-bold transition">Today</button>
                    </div>
                </div>
            </div>

            <!-- Status -->
            <div class="sm:col-span-1 lg:col-span-2">
                <label class="block text-[11px] font-extrabold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1.5">Status</label>
                <x-select 
                    name="status" 
                    :options="[
                        'all' => 'All Status',
                        'published' => 'Published',
                        'pending' => 'Pending',
                        'draft' => 'Draft'
                    ]" 
                    :value="request('status', 'all')"
                    class="!h-11 !py-0 flex items-center font-semibold text-xs sm:text-sm"
                />
            </div>

            <!-- Action Buttons -->
            <div class="sm:col-span-2 lg:col-span-2 flex items-center gap-2">
                @if(request()->anyFilled(['q', 'search_by', 'date_posted', 'date_from', 'date_to', 'status']) && (request('q') || request('search_by', 'all') !== 'all' || request('date_posted') || request('date_from') || request('date_to') || request('status', 'all') !== 'all'))
                    <a href="{{ route('admin.announcements.index') }}" 
                        title="Clear All Filters"
                        class="h-11 px-3.5 bg-white dark:bg-slate-800 hover:bg-slate-100 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-300 rounded-xl font-bold text-sm border border-slate-300 dark:border-slate-700 transition-all flex items-center justify-center shrink-0 shadow-2xs cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </a>
                @endif
                <button type="submit" class="flex-1 h-11 px-4 bg-slate-900 dark:bg-emerald-600 hover:bg-slate-800 dark:hover:bg-emerald-700 text-white rounded-xl font-bold text-xs sm:text-sm shadow-sm hover:shadow-md transition-all active:scale-95 flex items-center justify-center gap-2 cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path></svg>
                    <span>Filter</span>
                </button>
            </div>
            <input type="hidden" name="per_page" value="{{ request('per_page', 10) }}">
        </form>
    </div>

    <!-- Table Section -->
    <div class="overflow-x-auto">
        <table class="w-full border-collapse">
            <thead>
                <tr class="bg-slate-50/90 dark:bg-slate-900/90 border-b border-slate-200 dark:border-slate-800">
                    <th class="px-6 py-4 text-left w-12">
                        <input type="checkbox" 
                            :checked="selectedAnnouncements.length > 0 && selectedAnnouncements.length === {{ $announcements->count() }}"
                            @change="toggleAll($event.target.checked)" 
                            class="rounded border-slate-300 dark:border-slate-700 text-emerald-600 focus:ring-emerald-500 bg-white dark:bg-slate-800 cursor-pointer"
                            title="Select all on this page">
                    </th>
                    <th class="px-6 py-4 text-left text-[11px] font-extrabold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Title & Content</th>
                    <th class="px-6 py-4 text-center text-[11px] font-extrabold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Engagement</th>
                    <th class="px-6 py-4 text-center text-[11px] font-extrabold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-4 text-right text-[11px] font-extrabold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Manage</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                @forelse($announcements as $announcement)
                    <tr :class="isSelected({{ $announcement->id }}) ? 'bg-emerald-50/60 dark:bg-emerald-950/20' : 'hover:bg-slate-50 dark:hover:bg-slate-800/30'" class="transition-colors group">
                        <td class="px-6 py-4">
                            <input type="checkbox" value="{{ $announcement->id }}" x-model="selectedAnnouncements" class="announcement-checkbox rounded border-slate-300 dark:border-slate-700 text-emerald-600 focus:ring-emerald-500 bg-white dark:bg-slate-800 cursor-pointer">
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-4">
                                @if($announcement->image_path)
                                    <div class="h-14 w-14 rounded-xl border border-slate-200 dark:border-slate-700 overflow-hidden shrink-0 shadow-sm group-hover:scale-105 transition-transform duration-300">
                                        <img src="{{ asset('uploads/' . $announcement->image_path) }}" class="h-full w-full object-cover">
                                    </div>
                                @else
                                    <div class="h-14 w-14 rounded-xl bg-slate-100 dark:bg-slate-800 flex items-center justify-center shrink-0">
                                        <svg class="w-6 h-6 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path></svg>
                                    </div>
                                @endif
                                <div class="min-w-0">
                                    <div class="text-base font-bold text-slate-900 dark:text-white truncate max-w-md">{{ $announcement->title }}</div>
                                    <div class="text-[11px] text-slate-500 dark:text-slate-400 line-clamp-1 mt-0.5">{{ Str::limit(strip_tags($announcement->content), 80) }}</div>
                                    <div class="flex items-center gap-2 mt-1.5">
                                        <span class="text-[10px] font-black uppercase tracking-tighter text-slate-400 bg-slate-100 dark:bg-slate-800 px-1.5 py-0.5 rounded border border-slate-200 dark:border-slate-700">Posted {{ $announcement->created_at->diffForHumans() }}</span>
                                        @if($announcement->event_date)
                                            <span class="text-[10px] font-black uppercase tracking-tighter text-emerald-600 bg-emerald-50 dark:bg-emerald-900/20 px-1.5 py-0.5 rounded border border-emerald-100 dark:border-emerald-800/50">
                                                Event: {{ $announcement->event_date->format('M d, Y') }}@if($announcement->end_date && $announcement->end_date->format('Y-m-d') !== $announcement->event_date->format('Y-m-d')) – {{ $announcement->end_date->format('M d, Y') }}@endif
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex flex-col items-center">
                                <div class="text-xs font-black text-slate-900 dark:text-white">{{ $announcement->images->count() }}</div>
                                <div class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Sections</div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-center">
                            @if($announcement->status === 'published')
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 text-xs font-bold rounded-full bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800/60 shadow-2xs">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 shadow-[0_0_6px_rgba(16,185,129,0.7)]"></span>
                                    <span>Published</span>
                                </span>
                            @elseif($announcement->status === 'pending')
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 text-xs font-bold rounded-full bg-amber-50 dark:bg-amber-950/60 text-amber-700 dark:text-amber-300 border border-amber-200 dark:border-amber-800/60 shadow-2xs">
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500 shadow-[0_0_6px_rgba(245,158,11,0.7)]"></span>
                                    <span>Pending</span>
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 text-xs font-bold rounded-full bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 border border-slate-200 dark:border-slate-700 shadow-2xs">
                                    <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
                                    <span>Draft</span>
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex justify-end items-center gap-1.5">
                                {{-- View Announcement --}}
                                <a href="{{ route('announcements.show', $announcement) }}" 
                                   target="_blank"
                                   class="w-8 h-8 rounded-lg flex items-center justify-center text-slate-400 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-950/50 border border-slate-200/70 dark:border-slate-700/70 hover:border-blue-300 dark:hover:border-blue-700 transition-all cursor-pointer shadow-2xs" 
                                   title="View Announcement">
                                    <svg class="w-[17px] h-[17px]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                </a>

                                {{-- Edit Announcement --}}
                                <a href="{{ route('admin.announcements.edit', $announcement) }}" 
                                   class="w-8 h-8 rounded-lg flex items-center justify-center text-slate-400 hover:text-emerald-600 dark:hover:text-emerald-400 hover:bg-emerald-50 dark:hover:bg-emerald-950/50 border border-slate-200/70 dark:border-slate-700/70 hover:border-emerald-300 dark:hover:border-emerald-700 transition-all cursor-pointer shadow-2xs" 
                                   title="Edit Announcement">
                                    <svg class="w-[17px] h-[17px]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </a>

                                {{-- Archive Announcement --}}
                                @can('delete-announcements')
                                <button type="button" @click="$dispatch('open-confirmation', {
                                    action: '{{ route('admin.announcements.destroy', $announcement) }}',
                                    method: 'DELETE',
                                    title: 'Archive Announcement?',
                                    message: 'This will move the announcement to the system archive.',
                                    confirmText: 'Yes, Archive',
                                    type: 'danger'
                                })" 
                                class="w-8 h-8 rounded-lg flex items-center justify-center text-slate-400 hover:text-rose-600 dark:hover:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-950/50 border border-slate-200/70 dark:border-slate-700/70 hover:border-rose-300 dark:hover:border-rose-700 transition-all cursor-pointer shadow-2xs" 
                                title="Archive Announcement">
                                    <svg class="w-[17px] h-[17px]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                                @endcan
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-20 text-center">
                            <div class="flex flex-col items-center">
                                <div class="p-4 bg-slate-50 dark:bg-slate-800 rounded-full mb-4">
                                    <svg class="w-12 h-12 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path></svg>
                                </div>
                                <h4 class="text-lg font-bold text-slate-900 dark:text-white">No Announcements Yet</h4>
                                <p class="text-sm text-slate-500 dark:text-slate-400 mt-1 max-w-xs mx-auto">Get started by broadcasting your first news or event to the facility.</p>
                                <a href="{{ route('admin.announcements.create') }}" class="mt-6 inline-flex items-center gap-2 bg-emerald-600 text-white px-6 py-2.5 rounded-xl font-bold transition-all hover:bg-emerald-700">
                                    Create First Post
                                </a>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination Controls -->
    <div class="px-6 py-4 border-t border-slate-200 dark:border-slate-800 bg-slate-50/70 dark:bg-slate-900/60 flex flex-col sm:flex-row items-center justify-between gap-4">
        <div class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 font-medium">
            Showing 
            <span class="font-bold text-slate-900 dark:text-white">{{ $announcements->firstItem() ?? ($announcements->count() > 0 ? 1 : 0) }}</span>
            to 
            <span class="font-bold text-slate-900 dark:text-white">{{ $announcements->lastItem() ?? $announcements->count() }}</span>
            of 
            <span class="font-bold text-slate-900 dark:text-white">{{ $announcements->total() }}</span>
            announcements
        </div>
        
        <div>
            {{ $announcements->appends(request()->query())->links('vendor.pagination.custom') }}
        </div>
    </div>

    <!-- Floating Bulk Action Bar (Glassmorphic, appears when announcements selected) -->
    <div x-show="selectedAnnouncements.length > 0"
         x-cloak
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 translate-y-6 scale-95"
         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 translate-y-0 scale-100"
         x-transition:leave-end="opacity-0 translate-y-6 scale-95"
         class="fixed bottom-6 inset-x-0 mx-auto z-50 w-fit max-w-[92vw] px-4 py-2.5 rounded-2xl bg-slate-900/95 dark:bg-slate-800/95 backdrop-blur-md border border-slate-700/60 shadow-2xl flex items-center gap-3 text-white">
        <div class="flex items-center gap-2 pr-3 border-r border-slate-700">
            <span class="w-2 h-2 rounded-full bg-emerald-400 animate-ping"></span>
            <span class="text-xs font-bold whitespace-nowrap">
                <span x-text="selectedAnnouncements.length"></span> announcement<span x-show="selectedAnnouncements.length !== 1">s</span> selected
            </span>
        </div>

        <div class="flex items-center gap-2">
            <button type="button" @click="showBulkStatusModal = true"
                class="h-9 px-3.5 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-bold text-xs shadow-md shadow-blue-600/30 transition-all flex items-center gap-1.5 active:scale-95 cursor-pointer">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                <span>Change Status</span>
            </button>

            @can('delete-announcements')
            <button type="button" @click="showBulkDeleteModal = true"
                class="h-9 px-3.5 rounded-xl bg-rose-600 hover:bg-rose-500 text-white font-bold text-xs shadow-md shadow-rose-600/30 transition-all flex items-center gap-1.5 active:scale-95 cursor-pointer">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                <span>Archive</span>
            </button>
            @endcan

            <button type="button" @click="selectedAnnouncements = []"
                class="h-9 px-2.5 rounded-xl hover:bg-slate-800 text-slate-400 hover:text-white text-xs font-semibold transition-all flex items-center gap-1 cursor-pointer"
                title="Deselect All">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                <span class="hidden sm:inline">Deselect</span>
            </button>
        </div>
    </div>

    <!-- Bulk Status Change Modal -->
    <template x-teleport="body">
        <div x-show="showBulkStatusModal" x-cloak style="display: none;" class="fixed inset-0 z-[100] overflow-y-auto" aria-labelledby="bulk-status-title" role="dialog" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-slate-950/80 backdrop-blur-sm transition-opacity" @click="showBulkStatusModal = false"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                <div class="inline-block align-bottom bg-white dark:bg-slate-900 rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full border border-slate-200 dark:border-slate-800">
                    <div class="bg-white dark:bg-slate-900 p-6">
                        <!-- Modal Header -->
                        <div class="flex items-start justify-between gap-4 pb-4 border-b border-slate-100 dark:border-slate-800">
                            <div class="flex items-center gap-3.5">
                                <div class="w-10 h-10 rounded-2xl bg-blue-100/80 dark:bg-blue-900/40 text-blue-600 dark:text-blue-400 flex items-center justify-center shrink-0 border border-blue-500/20 shadow-2xs">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-lg font-bold text-slate-900 dark:text-white tracking-tight" id="bulk-status-title">Bulk Change Announcement Status</h3>
                                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                                        Update status for <span class="font-extrabold text-blue-600 dark:text-blue-400" x-text="selectedAnnouncements.length"></span> selected announcement(s).
                                    </p>
                                </div>
                            </div>
                            <button type="button" @click="showBulkStatusModal = false" class="p-1 rounded-xl text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors cursor-pointer" title="Close">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>

                        <!-- Status Selection Options (Full width, flush margins) -->
                        <div class="pt-4 space-y-2.5">
                            <label class="block text-[11px] font-extrabold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-1">Target Status</label>

                            <!-- Published -->
                            <label @click="bulkStatusValue = 'published'"
                                class="flex items-center justify-between p-3.5 rounded-2xl border-2 cursor-pointer transition-all"
                                :class="bulkStatusValue === 'published' ? 'border-emerald-500 bg-emerald-50/70 dark:bg-emerald-950/30 shadow-xs' : 'border-slate-200 dark:border-slate-700 hover:border-slate-300 dark:hover:border-slate-600 bg-white dark:bg-slate-800/40'">
                                <div class="flex items-center gap-3.5 min-w-0">
                                    <div class="w-9 h-9 rounded-xl flex items-center justify-center shrink-0 transition-colors"
                                        :class="bulkStatusValue === 'published' ? 'bg-emerald-600 text-white shadow-xs' : 'bg-emerald-100 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400'">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                    </div>
                                    <div class="min-w-0">
                                        <div class="flex items-center gap-2">
                                            <span class="text-sm font-bold text-slate-900 dark:text-white">Published</span>
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-emerald-100 dark:bg-emerald-900/50 text-emerald-700 dark:text-emerald-300">Live</span>
                                        </div>
                                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5 truncate sm:whitespace-normal">Publicly visible across landing page and notice boards.</p>
                                    </div>
                                </div>
                                <div class="flex items-center justify-center w-5 h-5 rounded-full border-2 shrink-0 ml-3 transition-all"
                                    :class="bulkStatusValue === 'published' ? 'border-emerald-600 bg-emerald-600' : 'border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800'">
                                    <div class="w-2 h-2 rounded-full bg-white transition-transform" :class="bulkStatusValue === 'published' ? 'scale-100' : 'scale-0'"></div>
                                </div>
                            </label>

                            <!-- Pending -->
                            <label @click="bulkStatusValue = 'pending'"
                                class="flex items-center justify-between p-3.5 rounded-2xl border-2 cursor-pointer transition-all"
                                :class="bulkStatusValue === 'pending' ? 'border-amber-500 bg-amber-50/70 dark:bg-amber-950/30 shadow-xs' : 'border-slate-200 dark:border-slate-700 hover:border-slate-300 dark:hover:border-slate-600 bg-white dark:bg-slate-800/40'">
                                <div class="flex items-center gap-3.5 min-w-0">
                                    <div class="w-9 h-9 rounded-xl flex items-center justify-center shrink-0 transition-colors"
                                        :class="bulkStatusValue === 'pending' ? 'bg-amber-500 text-white shadow-xs' : 'bg-amber-100 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400'">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    </div>
                                    <div class="min-w-0">
                                        <div class="flex items-center gap-2">
                                            <span class="text-sm font-bold text-slate-900 dark:text-white">Pending</span>
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-amber-100 dark:bg-amber-950/50 text-amber-700 dark:text-amber-300">Queued</span>
                                        </div>
                                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5 truncate sm:whitespace-normal">Awaiting scheduled release or internal supervisor review.</p>
                                    </div>
                                </div>
                                <div class="flex items-center justify-center w-5 h-5 rounded-full border-2 shrink-0 ml-3 transition-all"
                                    :class="bulkStatusValue === 'pending' ? 'border-amber-500 bg-amber-500' : 'border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800'">
                                    <div class="w-2 h-2 rounded-full bg-white transition-transform" :class="bulkStatusValue === 'pending' ? 'scale-100' : 'scale-0'"></div>
                                </div>
                            </label>

                            <!-- Draft -->
                            <label @click="bulkStatusValue = 'draft'"
                                class="flex items-center justify-between p-3.5 rounded-2xl border-2 cursor-pointer transition-all"
                                :class="bulkStatusValue === 'draft' ? 'border-slate-500 bg-slate-50/80 dark:bg-slate-800/60 shadow-xs' : 'border-slate-200 dark:border-slate-700 hover:border-slate-300 dark:hover:border-slate-600 bg-white dark:bg-slate-800/40'">
                                <div class="flex items-center gap-3.5 min-w-0">
                                    <div class="w-9 h-9 rounded-xl flex items-center justify-center shrink-0 transition-colors"
                                        :class="bulkStatusValue === 'draft' ? 'bg-slate-700 dark:bg-slate-300 text-white dark:text-slate-900 shadow-xs' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400'">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </div>
                                    <div class="min-w-0">
                                        <div class="flex items-center gap-2">
                                            <span class="text-sm font-bold text-slate-900 dark:text-white">Draft</span>
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400">Private</span>
                                        </div>
                                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5 truncate sm:whitespace-normal">Internal work-in-progress, completely hidden from public view.</p>
                                    </div>
                                </div>
                                <div class="flex items-center justify-center w-5 h-5 rounded-full border-2 shrink-0 ml-3 transition-all"
                                    :class="bulkStatusValue === 'draft' ? 'border-slate-500 bg-slate-500' : 'border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800'">
                                    <div class="w-2 h-2 rounded-full bg-white transition-transform" :class="bulkStatusValue === 'draft' ? 'scale-100' : 'scale-0'"></div>
                                </div>
                            </label>
                        </div>
                    </div>
                    <div class="bg-slate-50 dark:bg-slate-950/50 px-6 py-4 flex flex-row-reverse gap-3 border-t border-slate-100 dark:border-slate-800">
                        <form method="POST" action="{{ route('admin.announcements.bulk-status') }}" @submit="
                            $el.querySelectorAll('input[name=\'ids[]\']').forEach(e => e.remove());
                            selectedAnnouncements.forEach(id => {
                                const inp = document.createElement('input');
                                inp.type = 'hidden';
                                inp.name = 'ids[]';
                                inp.value = id;
                                $el.appendChild(inp);
                            });
                        ">
                            @csrf
                            <input type="hidden" name="status" :value="bulkStatusValue">
                            <template x-for="id in selectedAnnouncements" :key="id">
                                <input type="hidden" name="ids[]" :value="id">
                            </template>
                            <button type="submit" 
                                class="w-full inline-flex justify-center items-center gap-2 rounded-xl border border-transparent shadow-md px-6 py-2.5 text-sm font-bold text-white transition-all cursor-pointer active:scale-95"
                                :class="{
                                    'bg-emerald-600 hover:bg-emerald-700 shadow-emerald-600/25': bulkStatusValue === 'published',
                                    'bg-amber-600 hover:bg-amber-700 shadow-amber-600/25': bulkStatusValue === 'pending',
                                    'bg-slate-700 hover:bg-slate-800 shadow-slate-700/25': bulkStatusValue === 'draft'
                                }">
                                <span>Apply Status Change</span>
                            </button>
                        </form>
                        <button type="button" @click="showBulkStatusModal = false" class="inline-flex justify-center rounded-xl border border-slate-300 dark:border-slate-700 shadow-2xs px-6 py-2.5 bg-white dark:bg-slate-800 text-sm font-bold text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 transition-all cursor-pointer">
                            Cancel
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </template>

    <!-- Bulk Archive Modal -->
    @can('delete-announcements')
    <template x-teleport="body">
        <div x-show="showBulkModal || showBulkDeleteModal" x-cloak style="display: none;" class="fixed inset-0 z-[60] overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-slate-950/80 backdrop-blur-sm transition-opacity" @click="showBulkModal = false; showBulkDeleteModal = false"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                <div class="inline-block align-bottom bg-white dark:bg-slate-900 rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full border border-slate-200 dark:border-slate-800">
                    <div class="bg-white dark:bg-slate-900 p-6">
                        <div class="flex items-start justify-between gap-4">
                            <div class="flex items-center gap-3.5">
                                <div class="w-10 h-10 rounded-2xl bg-rose-100/80 dark:bg-rose-900/40 text-rose-600 dark:text-rose-400 flex items-center justify-center shrink-0 border border-rose-500/20 shadow-2xs">
                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-lg font-bold text-slate-900 dark:text-white" id="modal-title">Bulk Archive Announcements</h3>
                                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                                        Are you sure you want to archive <span class="font-extrabold text-rose-600 dark:text-rose-400" x-text="selectedAnnouncements.length"></span> selected announcement(s)? They will be moved to the archive module.
                                    </p>
                                </div>
                            </div>
                            <button type="button" @click="showBulkModal = false; showBulkDeleteModal = false" class="p-1 rounded-xl text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors cursor-pointer" title="Close">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>
                    </div>
                    <div class="bg-slate-50 dark:bg-slate-950/50 px-6 py-4 flex flex-row-reverse gap-3">
                        <form method="POST" action="{{ route('admin.announcements.bulk-delete') }}" @submit="
                            $el.querySelectorAll('input[name=\'ids[]\']').forEach(e => e.remove());
                            selectedAnnouncements.forEach(id => {
                                const inp = document.createElement('input');
                                inp.type = 'hidden';
                                inp.name = 'ids[]';
                                inp.value = id;
                                $el.appendChild(inp);
                            });
                        ">
                            @csrf
                            @method('DELETE')
                            <template x-for="id in selectedAnnouncements" :key="id">
                                <input type="hidden" name="ids[]" :value="id">
                            </template>
                            <button type="submit" class="w-full inline-flex justify-center rounded-xl border border-transparent shadow-lg px-6 py-2 bg-rose-600 text-sm font-bold text-white hover:bg-rose-700 transition-all cursor-pointer">
                                Yes, Archive All
                            </button>
                        </form>
                        <button type="button" @click="showBulkModal = false; showBulkDeleteModal = false" class="inline-flex justify-center rounded-xl border border-slate-300 dark:border-slate-700 shadow-sm px-6 py-2 bg-white dark:bg-slate-800 text-sm font-bold text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 transition-all cursor-pointer">
                            Cancel
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </template>
    @endcan
</div>
</div>
@endsection
