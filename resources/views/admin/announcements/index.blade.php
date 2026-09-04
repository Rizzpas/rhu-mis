@extends('layouts.admin')

@section('header', 'Manage Announcements')

@section('content')
<div class="bg-white dark:bg-slate-900 rounded-2xl shadow-xl border border-slate-200 dark:border-slate-800 overflow-hidden" x-data="{ selectedAnnouncements: [], showBulkModal: false }">
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
            <div x-show="selectedAnnouncements.length > 0" x-cloak x-transition>
                <button @click="showBulkModal = true" class="h-11 px-4 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-bold text-sm shadow-md shadow-rose-600/20 transition-all flex items-center gap-2 active:scale-95 cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                    <span>Archive Selected (<span x-text="selectedAnnouncements.length"></span>)</span>
                </button>
            </div>
            <a href="{{ route('admin.announcements.create') }}" class="h-11 px-5 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white font-bold text-sm shadow-md shadow-emerald-600/25 hover:shadow-emerald-600/35 transition-all flex items-center gap-2 active:scale-95 cursor-pointer">
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
        </form>
    </div>

    <!-- Table Section -->
    <div class="overflow-x-auto">
        <table class="w-full border-collapse">
            <thead>
                <tr class="bg-slate-50/90 dark:bg-slate-900/90 border-b border-slate-200 dark:border-slate-800">
                    <th class="px-6 py-4 text-left w-12">
                        <input type="checkbox" @change="if($event.target.checked) { selectedAnnouncements = Array.from(document.querySelectorAll('.announcement-checkbox')).map(cb => cb.value) } else { selectedAnnouncements = [] }" class="rounded border-slate-300 dark:border-slate-700 text-emerald-600 focus:ring-emerald-500 bg-white dark:bg-slate-800">
                    </th>
                    <th class="px-6 py-4 text-left text-[11px] font-extrabold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Title & Content</th>
                    <th class="px-6 py-4 text-center text-[11px] font-extrabold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Engagement</th>
                    <th class="px-6 py-4 text-center text-[11px] font-extrabold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-4 text-right text-[11px] font-extrabold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Manage</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                @forelse($announcements as $announcement)
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/30 transition-colors group">
                        <td class="px-6 py-4">
                            <input type="checkbox" value="{{ $announcement->id }}" x-model="selectedAnnouncements" class="announcement-checkbox rounded border-slate-300 dark:border-slate-700 text-emerald-600 focus:ring-emerald-500 bg-white dark:bg-slate-800">
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
</div>

<!-- Bulk Archive Modal -->
<template x-if="showBulkModal">
    <div class="fixed inset-0 z-[60] overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-slate-950/80 backdrop-blur-sm transition-opacity" @click="showBulkModal = false"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-bottom bg-white dark:bg-slate-900 rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full border border-slate-200 dark:border-slate-800">
                <div class="bg-white dark:bg-slate-900 px-6 pt-6 pb-6">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-2xl bg-red-100 dark:bg-red-900/30 sm:mx-0 sm:h-10 sm:w-10">
                            <svg class="h-6 w-6 text-red-600 dark:text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                            <h3 class="text-xl font-bold text-slate-900 dark:text-white" id="modal-title">Bulk Archive Announcements</h3>
                            <div class="mt-2">
                                <p class="text-sm text-slate-500 dark:text-slate-400">Are you sure you want to archive <span class="font-black text-red-600 dark:text-red-400" x-text="selectedAnnouncements.length"></span> selected announcements? They will be moved to the archive module.</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-slate-50 dark:bg-slate-950/50 px-6 py-4 flex flex-row-reverse gap-3">
                    <form method="POST" action="{{ route('admin.announcements.bulk-delete') }}">
                        @csrf
                        @method('DELETE')
                        <template x-for="id in selectedAnnouncements">
                            <input type="hidden" name="ids[]" :value="id">
                        </template>
                        <button type="submit" class="w-full inline-flex justify-center rounded-xl border border-transparent shadow-lg px-6 py-2 bg-red-600 text-sm font-bold text-white hover:bg-red-700 transition-all">
                            Yes, Archive All
                        </button>
                    </form>
                    <button type="button" @click="showBulkModal = false" class="inline-flex justify-center rounded-xl border border-slate-300 dark:border-slate-700 shadow-sm px-6 py-2 bg-white dark:bg-slate-800 text-sm font-bold text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 transition-all">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>
@endsection
