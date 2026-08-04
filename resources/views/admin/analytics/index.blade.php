@extends('layouts.admin')

@section('header', 'Detailed Analytics')

@section('content')
<div class="mb-6 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
    <div>
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Epidemiological & Operational Analytics</h2>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Deep-dive into facility metrics and patient trends.</p>
    </div>

    <div class="flex flex-wrap items-center gap-3">
        <button onclick="window.print()" class="hidden md:flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-2 px-4 rounded-xl shadow-sm transition-colors text-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
            Export Full Report
        </button>
        <form action="{{ route('admin.analytics') }}" method="GET" class="flex items-center gap-2 bg-white dark:bg-slate-800 p-2 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700">
            <label for="time_filter" class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest pl-2">Global Timeframe:</label>
            <select name="time_filter" id="time_filter" onchange="this.form.submit()" class="border-0 bg-transparent text-sm font-bold text-slate-800 dark:text-white  py-1 pl-2 pr-8 cursor-pointer dark:bg-slate-800 dark:text-white">
                <option value="today" {{ $timeFilter == 'today' ? 'selected' : '' }}>Today</option>
                <option value="weekly" {{ $timeFilter == 'weekly' ? 'selected' : '' }}>This Week</option>
                <option value="monthly" {{ $timeFilter == 'monthly' ? 'selected' : '' }}>This Month</option>
                <option value="yearly" {{ $timeFilter == 'yearly' ? 'selected' : '' }}>This Year</option>
                <option value="all" {{ $timeFilter == 'all' ? 'selected' : '' }}>All Time</option>
            </select>
        </form>
    </div>
</div>

<!-- Alpine component to manage individual chart data fetching -->
<div x-data="analyticsDashboard()" class="space-y-8">
    
    <!-- Row 1: Visit Volume (Full Width) -->
    <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-800 p-6 flex flex-col w-full h-[500px]">
        <div class="flex justify-between items-center mb-6">
            <div>
                <h3 x-data="{ showTooltip: false }" @mouseenter="showTooltip = true" @mouseleave="showTooltip = false" 
                    class="text-lg font-bold text-gray-800 dark:text-white flex items-center gap-2 relative">
                    Visit Volume
                    <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    
                    <!-- Custom Tooltip -->
                    <div x-show="showTooltip" x-transition class="absolute z-50 bottom-full mb-2 left-0 w-48 p-2 bg-slate-900 text-white text-[10px] rounded-lg shadow-xl pointer-events-none font-medium leading-tight" style="display: none;">
                        Tracks the total number of consultations recorded over the selected timeframe.
                        <div class="absolute top-full left-4 border-[4px] border-transparent border-t-slate-900"></div>
                    </div>
                </h3>
                <p class="text-xs text-gray-500 dark:text-gray-400">Total consultations over time</p>
            </div>
            <div class="flex gap-2">
                <select @change="updateChart('volume', $event.target.value)" class="text-xs border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-gray-700 dark:text-gray-300 rounded-lg focus:ring-emerald-500">
                    <option value="today" {{ $timeFilter == 'today' ? 'selected' : '' }}>Today</option>
                    <option value="weekly" {{ $timeFilter == 'weekly' ? 'selected' : '' }}>This Week</option>
                    <option value="monthly" {{ $timeFilter == 'monthly' ? 'selected' : '' }}>This Month</option>
                <option value="yearly" {{ $timeFilter == 'yearly' ? 'selected' : '' }}>This Year</option>
                </select>
                <div class="relative" x-data="{ openExport: false }">
                    <button @click="openExport = !openExport" @click.away="openExport = false" title="Export Chart" class="p-2 bg-gray-100 dark:bg-slate-700 text-gray-600 dark:text-gray-300 hover:bg-emerald-100 hover:text-emerald-600 rounded-lg transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                    </button>
                    <div x-show="openExport" style="display: none;" class="absolute right-0 mt-2 w-36 bg-white dark:bg-slate-800 rounded-lg shadow-lg border border-slate-200 dark:border-slate-800 py-1 z-20">
                        <a href="#" @click.prevent="exportChart('visitVolumeChart'); openExport = false" class="block px-4 py-2 text-xs text-gray-700 dark:text-gray-300 hover:bg-emerald-50 dark:hover:bg-slate-700">Download PNG</a>
                        <a href="#" @click.prevent="exportCSV('volume'); openExport = false" class="block px-4 py-2 text-xs text-gray-700 dark:text-gray-300 hover:bg-emerald-50 dark:hover:bg-slate-700">Download CSV</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="relative flex-1 w-full min-h-0">
            <canvas id="visitVolumeChart"></canvas>
        </div>
    </div>

    <!-- Row 2: Peak Hours & Workload (Half/Half) -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-800 p-6 flex flex-col h-[400px]">
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h3 x-data="{ showTooltip: false }" @mouseenter="showTooltip = true" @mouseleave="showTooltip = false"
                        class="text-lg font-bold text-gray-800 dark:text-white flex items-center gap-2 relative">
                        Peak Hours
                        <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        
                        <!-- Custom Tooltip -->
                        <div x-show="showTooltip" x-transition class="absolute z-50 bottom-full mb-2 left-0 w-48 p-2 bg-slate-900 text-white text-[10px] rounded-lg shadow-xl pointer-events-none font-medium leading-tight" style="display: none;">
                            Visualizes the busiest times of day based on consultation start times.
                            <div class="absolute top-full left-4 border-[4px] border-transparent border-t-slate-900"></div>
                        </div>
                    </h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Consultation distribution by hour</p>
                </div>
                <div class="flex gap-2">
                    <select @change="updateChart('peak', $event.target.value)" class="text-xs border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-gray-700 dark:text-gray-300 rounded-lg focus:ring-emerald-500">
                        <option value="today" {{ $timeFilter == 'today' ? 'selected' : '' }}>Today</option>
                        <option value="weekly" {{ $timeFilter == 'weekly' ? 'selected' : '' }}>This Week</option>
                        <option value="monthly" {{ $timeFilter == 'monthly' ? 'selected' : '' }}>This Month</option>
                <option value="yearly" {{ $timeFilter == 'yearly' ? 'selected' : '' }}>This Year</option>
                        <option value="all" {{ $timeFilter == 'all' ? 'selected' : '' }}>All Time</option>
                    </select>
                    <div class="relative" x-data="{ openExport: false }">
                        <button @click="openExport = !openExport" @click.away="openExport = false" title="Export Chart" class="p-2 bg-gray-100 dark:bg-slate-700 text-gray-600 dark:text-gray-300 hover:bg-emerald-100 hover:text-emerald-600 rounded-lg transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                        </button>
                        <div x-show="openExport" style="display: none;" class="absolute right-0 mt-2 w-36 bg-white dark:bg-slate-800 rounded-lg shadow-lg border border-slate-200 dark:border-slate-800 py-1 z-20">
                            <a href="#" @click.prevent="exportChart('peakHoursChart'); openExport = false" class="block px-4 py-2 text-xs text-gray-700 dark:text-gray-300 hover:bg-emerald-50 dark:hover:bg-slate-700">Download PNG</a>
                            <a href="#" @click.prevent="exportCSV('peak'); openExport = false" class="block px-4 py-2 text-xs text-gray-700 dark:text-gray-300 hover:bg-emerald-50 dark:hover:bg-slate-700">Download CSV</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="relative flex-1 w-full min-h-0"><canvas id="peakHoursChart"></canvas></div>
        </div>

        <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-800 p-6 flex flex-col h-[400px]">
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h3 x-data="{ showTooltip: false }" @mouseenter="showTooltip = true" @mouseleave="showTooltip = false"
                        class="text-lg font-bold text-gray-800 dark:text-white flex items-center gap-2 relative">
                        Staff Workload
                        <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        
                        <!-- Custom Tooltip -->
                        <div x-show="showTooltip" x-transition class="absolute z-50 bottom-full mb-2 left-0 w-48 p-2 bg-slate-900 text-white text-[10px] rounded-lg shadow-xl pointer-events-none font-medium leading-tight" style="display: none;">
                            Shows the distribution of patient cases across different doctors and nurses.
                            <div class="absolute top-full left-4 border-[4px] border-transparent border-t-slate-900"></div>
                        </div>
                    </h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Consultations per doctor/nurse</p>
                </div>
                <div class="flex gap-2">
                    <select @change="updateChart('workload', $event.target.value)" class="text-xs border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-gray-700 dark:text-gray-300 rounded-lg focus:ring-emerald-500">
                        <option value="today" {{ $timeFilter == 'today' ? 'selected' : '' }}>Today</option>
                        <option value="weekly" {{ $timeFilter == 'weekly' ? 'selected' : '' }}>This Week</option>
                        <option value="monthly" {{ $timeFilter == 'monthly' ? 'selected' : '' }}>This Month</option>
                <option value="yearly" {{ $timeFilter == 'yearly' ? 'selected' : '' }}>This Year</option>
                        <option value="all" {{ $timeFilter == 'all' ? 'selected' : '' }}>All Time</option>
                    </select>
                    <div class="relative" x-data="{ openExport: false }">
                        <button @click="openExport = !openExport" @click.away="openExport = false" title="Export Chart" class="p-2 bg-gray-100 dark:bg-slate-700 text-gray-600 dark:text-gray-300 hover:bg-emerald-100 hover:text-emerald-600 rounded-lg transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                        </button>
                        <div x-show="openExport" style="display: none;" class="absolute right-0 mt-2 w-36 bg-white dark:bg-slate-800 rounded-lg shadow-lg border border-slate-200 dark:border-slate-800 py-1 z-20">
                            <a href="#" @click.prevent="exportChart('workloadChart'); openExport = false" class="block px-4 py-2 text-xs text-gray-700 dark:text-gray-300 hover:bg-emerald-50 dark:hover:bg-slate-700">Download PNG</a>
                            <a href="#" @click.prevent="exportCSV('workload'); openExport = false" class="block px-4 py-2 text-xs text-gray-700 dark:text-gray-300 hover:bg-emerald-50 dark:hover:bg-slate-700">Download CSV</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="relative flex-1 w-full min-h-0"><canvas id="workloadChart"></canvas></div>
        </div>
    </div>

    <!-- Row 3: Age-Sex Pyramid (Full Width) -->
    <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-800 p-6 flex flex-col w-full h-[500px]">
        <div class="flex justify-between items-center mb-6">
            <div>
                <h3 x-data="{ showTooltip: false }" @mouseenter="showTooltip = true" @mouseleave="showTooltip = false"
                    class="text-lg font-bold text-gray-800 dark:text-white flex items-center gap-2 relative">
                    Patient Demographics (Age-Sex Pyramid)
                    <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    
                    <!-- Custom Tooltip -->
                    <div x-show="showTooltip" x-transition class="absolute z-50 bottom-full mb-2 left-0 w-48 p-2 bg-slate-900 text-white text-[10px] rounded-lg shadow-xl pointer-events-none font-medium leading-tight" style="display: none;">
                        Age and sex distribution of the patient population.
                        <div class="absolute top-full left-4 border-[4px] border-transparent border-t-slate-900"></div>
                    </div>
                </h3>
                <p class="text-xs text-gray-500 dark:text-gray-400">Age distribution among patients</p>
            </div>
            <div class="flex gap-2">
                <select @change="updateChart('demographics', $event.target.value)" class="text-xs border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-gray-700 dark:text-gray-300 rounded-lg focus:ring-emerald-500">
                    <option value="today" {{ $timeFilter == 'today' ? 'selected' : '' }}>Today</option>
                    <option value="weekly" {{ $timeFilter == 'weekly' ? 'selected' : '' }}>This Week</option>
                    <option value="monthly" {{ $timeFilter == 'monthly' ? 'selected' : '' }}>This Month</option>
                <option value="yearly" {{ $timeFilter == 'yearly' ? 'selected' : '' }}>This Year</option>
                    <option value="all" {{ $timeFilter == 'all' ? 'selected' : '' }}>All Time</option>
                </select>
                <div class="relative" x-data="{ openExport: false }">
                    <button @click="openExport = !openExport" @click.away="openExport = false" title="Export Chart" class="p-2 bg-gray-100 dark:bg-slate-700 text-gray-600 dark:text-gray-300 hover:bg-emerald-100 hover:text-emerald-600 rounded-lg transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                    </button>
                    <div x-show="openExport" style="display: none;" class="absolute right-0 mt-2 w-36 bg-white dark:bg-slate-800 rounded-lg shadow-lg border border-slate-200 dark:border-slate-800 py-1 z-20">
                        <a href="#" @click.prevent="exportChart('ageSexChart'); openExport = false" class="block px-4 py-2 text-xs text-gray-700 dark:text-gray-300 hover:bg-emerald-50 dark:hover:bg-slate-700">Download PNG</a>
                        <a href="#" @click.prevent="exportCSV('demographics'); openExport = false" class="block px-4 py-2 text-xs text-gray-700 dark:text-gray-300 hover:bg-emerald-50 dark:hover:bg-slate-700">Download CSV</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="relative flex-1 w-full min-h-0"><canvas id="ageSexChart"></canvas></div>
    </div>

    <!-- Row 4: Classification & Severity (Half/Half Doughnuts) -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-800 p-6 flex flex-col h-[400px]">
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h3 x-data="{ showTooltip: false }" @mouseenter="showTooltip = true" @mouseleave="showTooltip = false"
                        class="text-lg font-bold text-gray-800 dark:text-white flex items-center gap-2 relative">
                        Patient Classification
                        <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        
                        <!-- Custom Tooltip -->
                        <div x-show="showTooltip" x-transition class="absolute z-50 bottom-full mb-2 left-0 w-48 p-2 bg-slate-900 text-white text-[10px] rounded-lg shadow-xl pointer-events-none font-medium leading-tight" style="display: none;">
                            Breakdown of patients by age group (Pediatric, Adult, etc.).
                            <div class="absolute top-full left-4 border-[4px] border-transparent border-t-slate-900"></div>
                        </div>
                    </h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Distribution by sector</p>
                </div>
                <div class="flex gap-2">
                    <select @change="updateChart('classification', $event.target.value)" class="text-xs border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-gray-700 dark:text-gray-300 rounded-lg focus:ring-emerald-500">
                        <option value="today" {{ $timeFilter == 'today' ? 'selected' : '' }}>Today</option>
                        <option value="weekly" {{ $timeFilter == 'weekly' ? 'selected' : '' }}>This Week</option>
                        <option value="monthly" {{ $timeFilter == 'monthly' ? 'selected' : '' }}>This Month</option>
                <option value="yearly" {{ $timeFilter == 'yearly' ? 'selected' : '' }}>This Year</option>
                        <option value="all" {{ $timeFilter == 'all' ? 'selected' : '' }}>All Time</option>
                    </select>
                    <div class="relative" x-data="{ openExport: false }">
                        <button @click="openExport = !openExport" @click.away="openExport = false" title="Export Chart" class="p-2 bg-gray-100 dark:bg-slate-700 text-gray-600 dark:text-gray-300 hover:bg-emerald-100 hover:text-emerald-600 rounded-lg transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                        </button>
                        <div x-show="openExport" style="display: none;" class="absolute right-0 mt-2 w-36 bg-white dark:bg-slate-800 rounded-lg shadow-lg border border-slate-200 dark:border-slate-800 py-1 z-20">
                            <a href="#" @click.prevent="exportChart('classificationChart'); openExport = false" class="block px-4 py-2 text-xs text-gray-700 dark:text-gray-300 hover:bg-emerald-50 dark:hover:bg-slate-700">Download PNG</a>
                            <a href="#" @click.prevent="exportCSV('classification'); openExport = false" class="block px-4 py-2 text-xs text-gray-700 dark:text-gray-300 hover:bg-emerald-50 dark:hover:bg-slate-700">Download CSV</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="relative flex-1 w-full min-h-0"><canvas id="classificationChart"></canvas></div>
        </div>

        <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-800 p-6 flex flex-col h-[400px]">
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h3 x-data="{ showTooltip: false }" @mouseenter="showTooltip = true" @mouseleave="showTooltip = false"
                        class="text-lg font-bold text-gray-800 dark:text-white flex items-center gap-2 relative">
                        Triage Severity
                        <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        
                        <!-- Custom Tooltip -->
                        <div x-show="showTooltip" x-transition class="absolute z-50 bottom-full mb-2 left-0 w-48 p-2 bg-slate-900 text-white text-[10px] rounded-lg shadow-xl pointer-events-none font-medium leading-tight" style="display: none;">
                            Percentage of patients categorized by urgency level (Mild, Moderate, Severe).
                            <div class="absolute top-full left-4 border-[4px] border-transparent border-t-slate-900"></div>
                        </div>
                    </h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Case priority breakdown</p>
                </div>
                <div class="flex gap-2">
                    <select @change="updateChart('severity', $event.target.value)" class="text-xs border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-gray-700 dark:text-gray-300 rounded-lg focus:ring-emerald-500">
                        <option value="today" {{ $timeFilter == 'today' ? 'selected' : '' }}>Today</option>
                        <option value="weekly" {{ $timeFilter == 'weekly' ? 'selected' : '' }}>This Week</option>
                        <option value="monthly" {{ $timeFilter == 'monthly' ? 'selected' : '' }}>This Month</option>
                <option value="yearly" {{ $timeFilter == 'yearly' ? 'selected' : '' }}>This Year</option>
                        <option value="all" {{ $timeFilter == 'all' ? 'selected' : '' }}>All Time</option>
                    </select>
                    <div class="relative" x-data="{ openExport: false }">
                        <button @click="openExport = !openExport" @click.away="openExport = false" title="Export Chart" class="p-2 bg-gray-100 dark:bg-slate-700 text-gray-600 dark:text-gray-300 hover:bg-emerald-100 hover:text-emerald-600 rounded-lg transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                        </button>
                        <div x-show="openExport" style="display: none;" class="absolute right-0 mt-2 w-36 bg-white dark:bg-slate-800 rounded-lg shadow-lg border border-slate-200 dark:border-slate-800 py-1 z-20">
                            <a href="#" @click.prevent="exportChart('severityChart'); openExport = false" class="block px-4 py-2 text-xs text-gray-700 dark:text-gray-300 hover:bg-emerald-50 dark:hover:bg-slate-700">Download PNG</a>
                            <a href="#" @click.prevent="exportCSV('severity'); openExport = false" class="block px-4 py-2 text-xs text-gray-700 dark:text-gray-300 hover:bg-emerald-50 dark:hover:bg-slate-700">Download CSV</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="relative flex-1 w-full min-h-0"><canvas id="severityChart"></canvas></div>
        </div>
    </div>

    <!-- Row 5: Barangay Heatmap (Full Width) -->
    <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-800 p-6 flex flex-col w-full" style="min-height: 400px;">
        <div class="flex justify-between items-center mb-6">
            <div>
                <h3 x-data="{ showTooltip: false }" @mouseenter="showTooltip = true" @mouseleave="showTooltip = false"
                    class="text-lg font-bold text-gray-800 dark:text-white flex items-center gap-2 relative">
                    Barangay Heatmap
                    <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    
                    <!-- Custom Tooltip -->
                    <div x-show="showTooltip" x-transition class="absolute z-50 bottom-full mb-2 left-0 w-48 p-2 bg-slate-900 text-white text-[10px] rounded-lg shadow-xl pointer-events-none font-medium leading-tight" style="display: none;">
                        Distribution of patients by their residence location within Silang.
                        <div class="absolute top-full left-4 border-[4px] border-transparent border-t-slate-900"></div>
                    </div>
                </h3>
                <p class="text-xs text-gray-500 dark:text-gray-400">Patient distribution across Silang</p>
            </div>
            <div class="flex gap-2">
                <select @change="updateChart('barangay', $event.target.value)" class="text-xs border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-gray-700 dark:text-gray-300 rounded-lg focus:ring-emerald-500">
                    <option value="today" {{ $timeFilter == 'today' ? 'selected' : '' }}>Today</option>
                    <option value="weekly" {{ $timeFilter == 'weekly' ? 'selected' : '' }}>This Week</option>
                    <option value="monthly" {{ $timeFilter == 'monthly' ? 'selected' : '' }}>This Month</option>
                <option value="yearly" {{ $timeFilter == 'yearly' ? 'selected' : '' }}>This Year</option>
                    <option value="all" {{ $timeFilter == 'all' ? 'selected' : '' }}>All Time</option>
                </select>
                <div class="relative" x-data="{ openExport: false }">
                    <button @click="openExport = !openExport" @click.away="openExport = false" title="Export Chart" class="p-2 bg-gray-100 dark:bg-slate-700 text-gray-600 dark:text-gray-300 hover:bg-emerald-100 hover:text-emerald-600 rounded-lg transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                    </button>
                    <div x-show="openExport" style="display: none;" class="absolute right-0 mt-2 w-36 bg-white dark:bg-slate-800 rounded-lg shadow-lg border border-slate-200 dark:border-slate-800 py-1 z-20">
                        <a href="#" @click.prevent="exportChart('barangayChart'); openExport = false" class="block px-4 py-2 text-xs text-gray-700 dark:text-gray-300 hover:bg-emerald-50 dark:hover:bg-slate-700">Download PNG</a>
                        <a href="#" @click.prevent="exportCSV('barangay'); openExport = false" class="block px-4 py-2 text-xs text-gray-700 dark:text-gray-300 hover:bg-emerald-50 dark:hover:bg-slate-700">Download CSV</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="relative flex-1 w-full min-h-0 overflow-y-auto" id="barangayChartContainer"><canvas id="barangayChart"></canvas></div>
    </div>

    <!-- Row 6: Staff Productivity -->
    <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-800 p-6 flex flex-col w-full mt-8">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
            <div>
                <h3 x-data="{ showTooltip: false }" @mouseenter="showTooltip = true" @mouseleave="showTooltip = false"
                    class="text-xl font-bold text-gray-800 dark:text-white flex items-center gap-2 relative">
                    <svg class="w-6 h-6 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                    Staff Productivity
                    <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    
                    <!-- Custom Tooltip -->
                    <div x-show="showTooltip" x-transition class="absolute z-50 bottom-full mb-2 left-0 w-64 p-3 bg-slate-900 text-white text-[10px] rounded-xl shadow-xl pointer-events-none font-medium leading-relaxed" style="display: none;">
                        Aggregate performance data measuring how quickly and effectively staff process patient records and consultations.
                        <div class="absolute top-full left-10 border-[6px] border-transparent border-t-slate-900"></div>
                    </div>
                </h3>
                <p class="text-sm text-gray-500 dark:text-gray-400">Performance metrics and throughput</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <select x-model="productivityStaff" @change="fetchProductivity()" class="text-sm border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-gray-700 dark:text-gray-300 rounded-lg focus:ring-emerald-500">
                    <option value="all">All Staff</option>
                    @foreach($staffList as $staff)
                        <option value="{{ $staff->id }}">{{ $staff->formatted_name }}</option>
                    @endforeach
                </select>
                <select x-model="productivityTime" @change="fetchProductivity()" class="text-sm border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-gray-700 dark:text-gray-300 rounded-lg focus:ring-emerald-500">
                    <option value="today" {{ $timeFilter == 'today' ? 'selected' : '' }}>Today</option>
                    <option value="weekly" {{ $timeFilter == 'weekly' ? 'selected' : '' }}>This Week</option>
                    <option value="monthly" {{ $timeFilter == 'monthly' ? 'selected' : '' }}>This Month</option>
                <option value="yearly" {{ $timeFilter == 'yearly' ? 'selected' : '' }}>This Year</option>
                    <option value="all" {{ $timeFilter == 'all' ? 'selected' : '' }}>All Time</option>
                </select>
            </div>
        </div>

        <!-- Tabs -->
        <div class="flex border-b border-gray-200 dark:border-slate-700 mb-6 overflow-x-auto">
            <button @click="productivityTab = 'clinical'" :class="{'border-emerald-500 text-emerald-600 dark:text-emerald-400': productivityTab === 'clinical', 'border-transparent text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300': productivityTab !== 'clinical'}" class="whitespace-nowrap py-3 px-4 border-b-2 font-bold text-sm transition-colors">
                Clinical Performance (Doctors/Nurses)
            </button>
            <button @click="productivityTab = 'triage'" :class="{'border-emerald-500 text-emerald-600 dark:text-emerald-400': productivityTab === 'triage', 'border-transparent text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300': productivityTab !== 'triage'}" class="whitespace-nowrap py-3 px-4 border-b-2 font-bold text-sm transition-colors">
                Front-Line Efficiency (Triage/Info Desk)
            </button>
        </div>

        <!-- KPI Cards -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
            <!-- Clinical KPI Cards -->
            <template x-if="productivityTab === 'clinical'">
                <div x-data="{ showTooltip: false }" @mouseenter="showTooltip = true" @mouseleave="showTooltip = false"
                    class="bg-emerald-50 dark:bg-slate-700/50 p-4 rounded-xl border border-emerald-100 dark:border-slate-700 text-center col-span-1 relative">
                    
                    <!-- Custom Tooltip -->
                    <div x-show="showTooltip" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 translate-y-1" class="absolute z-50 bottom-full mb-3 left-1/2 -translate-x-1/2 w-48 p-3 bg-slate-900 text-white text-[10px] rounded-xl shadow-xl pointer-events-none text-center font-medium leading-relaxed" style="display: none;">
                        The mean time spent in actual consultation per patient for the selected period.
                        <div class="absolute top-full left-1/2 -ml-1 border-[6px] border-transparent border-t-slate-900"></div>
                    </div>

                    <p class="text-xs text-emerald-600 dark:text-emerald-400 font-bold uppercase tracking-wider mb-1 flex items-center justify-center gap-1">
                        Avg Duration
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </p>
                    <p class="text-2xl font-black text-gray-800 dark:text-white" x-text="productivityData.avgDuration + ' min'">{{ $staffProductivity['avgDuration'] }} min</p>
                </div>
            </template>
            <template x-if="productivityTab === 'clinical'">
                <div x-data="{ showTooltip: false }" @mouseenter="showTooltip = true" @mouseleave="showTooltip = false"
                    class="bg-blue-50 dark:bg-slate-700/50 p-4 rounded-xl border border-blue-100 dark:border-slate-700 text-center col-span-1 relative">
                    
                    <!-- Custom Tooltip -->
                    <div x-show="showTooltip" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 translate-y-1" class="absolute z-50 bottom-full mb-3 left-1/2 -translate-x-1/2 w-48 p-3 bg-slate-900 text-white text-[10px] rounded-xl shadow-xl pointer-events-none text-center font-medium leading-relaxed" style="display: none;">
                        Total count of unique patients successfully treated and completed.
                        <div class="absolute top-full left-1/2 -ml-1 border-[6px] border-transparent border-t-slate-900"></div>
                    </div>

                    <p class="text-xs text-blue-600 dark:text-blue-400 font-bold uppercase tracking-wider mb-1 flex items-center justify-center gap-1">
                        Total Patients
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </p>
                    <p class="text-2xl font-black text-gray-800 dark:text-white" x-text="productivityData.totalPatients">{{ $staffProductivity['totalPatients'] }}</p>
                </div>
            </template>
            <template x-if="productivityTab === 'clinical'">
                <div x-data="{ showTooltip: false }" @mouseenter="showTooltip = true" @mouseleave="showTooltip = false"
                    class="bg-purple-50 dark:bg-slate-700/50 p-4 rounded-xl border border-purple-100 dark:border-slate-700 text-center col-span-1 relative">
                    
                    <!-- Custom Tooltip -->
                    <div x-show="showTooltip" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 translate-y-1" class="absolute z-50 bottom-full mb-3 left-1/2 -translate-x-1/2 w-48 p-3 bg-slate-900 text-white text-[10px] rounded-xl shadow-xl pointer-events-none text-center font-medium leading-relaxed" style="display: none;">
                        Average number of patients processed per hour of active clinical work.
                        <div class="absolute top-full left-1/2 -ml-1 border-[6px] border-transparent border-t-slate-900"></div>
                    </div>

                    <p class="text-xs text-purple-600 dark:text-purple-400 font-bold uppercase tracking-wider mb-1 flex items-center justify-center gap-1">
                        Throughput (Per Hr)
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </p>
                    <p class="text-2xl font-black text-gray-800 dark:text-white" x-text="productivityData.patientsPerHour">{{ $staffProductivity['patientsPerHour'] }}</p>
                </div>
            </template>
            <template x-if="productivityTab === 'clinical'">
                <div x-data="{ showTooltip: false }" @mouseenter="showTooltip = true" @mouseleave="showTooltip = false"
                    class="bg-rose-50 dark:bg-slate-700/50 p-4 rounded-xl border border-rose-100 dark:border-slate-700 text-center col-span-1 relative">
                    
                    <!-- Custom Tooltip -->
                    <div x-show="showTooltip" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 translate-y-1" class="absolute z-50 bottom-full mb-3 left-1/2 -translate-x-1/2 w-48 p-3 bg-slate-900 text-white text-[10px] rounded-xl shadow-xl pointer-events-none text-center font-medium leading-relaxed" style="display: none;">
                        Percentage of queued consultations that were cancelled or did not proceed.
                        <div class="absolute top-full left-1/2 -ml-1 border-[6px] border-transparent border-t-slate-900"></div>
                    </div>

                    <p class="text-xs text-rose-600 dark:text-rose-400 font-bold uppercase tracking-wider mb-1 flex items-center justify-center gap-1">
                        Cancellation Rate
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </p>
                    <p class="text-2xl font-black text-gray-800 dark:text-white" x-text="productivityData.cancellationRate + '%'">{{ $staffProductivity['cancellationRate'] }}%</p>
                </div>
            </template>

            <!-- Triage KPI Cards -->
            <template x-if="productivityTab === 'triage'">
                <div x-data="{ showTooltip: false }" @mouseenter="showTooltip = true" @mouseleave="showTooltip = false"
                    class="bg-amber-50 dark:bg-slate-700/50 p-4 rounded-xl border border-amber-100 dark:border-slate-700 text-center col-span-1 relative">
                    
                    <!-- Custom Tooltip -->
                    <div x-show="showTooltip" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 translate-y-1" class="absolute z-50 bottom-full mb-3 left-1/2 -translate-x-1/2 w-48 p-3 bg-slate-900 text-white text-[10px] rounded-xl shadow-xl pointer-events-none text-center font-medium leading-relaxed" style="display: none;">
                        Average duration patients spend in the queue before being seen by a provider.
                        <div class="absolute top-full left-1/2 -ml-1 border-[6px] border-transparent border-t-slate-900"></div>
                    </div>

                    <p class="text-xs text-amber-600 dark:text-amber-400 font-bold uppercase tracking-wider mb-1 flex items-center justify-center gap-1">
                        Avg Wait Time
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </p>
                    <p class="text-2xl font-black text-gray-800 dark:text-white" x-text="productivityData.avgWaitTime + ' min'">{{ $staffProductivity['avgWaitTime'] }} min</p>
                </div>
            </template>
        </div>

        <!-- Charts -->
        <div class="h-[400px]">
            <!-- Clinical Chart -->
            <div x-show="productivityTab === 'clinical'" class="flex flex-col h-full w-full">
                <div class="flex justify-between items-center mb-4">
                    <h4 x-data="{ showTooltip: false }" @mouseenter="showTooltip = true" @mouseleave="showTooltip = false"
                        class="text-sm font-bold text-gray-700 dark:text-gray-300 flex items-center gap-2 relative">
                        Avg Consultation Duration (mins) per Provider
                        <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        
                        <!-- Custom Tooltip -->
                        <div x-show="showTooltip" x-transition class="absolute z-50 bottom-full mb-2 left-0 w-48 p-2 bg-slate-900 text-white text-[10px] rounded-lg shadow-xl pointer-events-none font-medium leading-tight" style="display: none;">
                            The mean time spent in actual consultation per patient.
                            <div class="absolute top-full left-4 border-[4px] border-transparent border-t-slate-900"></div>
                        </div>
                    </h4>
                    <div class="relative" x-data="{ openExport: false }">
                        <button @click="openExport = !openExport" @click.away="openExport = false" title="Export Chart" class="p-1 bg-gray-100 dark:bg-slate-700 text-gray-600 dark:text-gray-300 hover:bg-emerald-100 hover:text-emerald-600 rounded-lg transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                        </button>
                        <div x-show="openExport" style="display: none;" class="absolute right-0 mt-2 w-36 bg-white dark:bg-slate-800 rounded-lg shadow-lg border border-slate-200 dark:border-slate-800 py-1 z-20">
                            <a href="#" @click.prevent="exportChart('durationStaffChart'); openExport = false" class="block px-4 py-2 text-xs text-gray-700 dark:text-gray-300 hover:bg-emerald-50 dark:hover:bg-slate-700">Download PNG</a>
                        </div>
                    </div>
                </div>
                <div class="relative flex-1 w-full min-h-0"><canvas id="durationStaffChart"></canvas></div>
            </div>

            <!-- Triage Chart -->
            <div x-show="productivityTab === 'triage'" class="flex flex-col h-full w-full">
                <div class="flex justify-between items-center mb-4">
                    <h4 x-data="{ showTooltip: false }" @mouseenter="showTooltip = true" @mouseleave="showTooltip = false"
                        class="text-sm font-bold text-gray-700 dark:text-gray-300 flex items-center gap-2 relative">
                        Triage Encoding Speed (seconds) per Nurse
                        <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        
                        <!-- Custom Tooltip -->
                        <div x-show="showTooltip" x-transition class="absolute z-50 bottom-full mb-2 left-0 w-48 p-2 bg-slate-900 text-white text-[10px] rounded-lg shadow-xl pointer-events-none font-medium leading-tight" style="display: none;">
                            Average time taken by triage nurses to input patient vital signs and symptoms.
                            <div class="absolute top-full left-4 border-[4px] border-transparent border-t-slate-900"></div>
                        </div>
                    </h4>
                    <div class="relative" x-data="{ openExport: false }">
                        <button @click="openExport = !openExport" @click.away="openExport = false" title="Export Chart" class="p-1 bg-gray-100 dark:bg-slate-700 text-gray-600 dark:text-gray-300 hover:bg-emerald-100 hover:text-emerald-600 rounded-lg transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                        </button>
                        <div x-show="openExport" style="display: none;" class="absolute right-0 mt-2 w-36 bg-white dark:bg-slate-800 rounded-lg shadow-lg border border-slate-200 dark:border-slate-800 py-1 z-20">
                            <a href="#" @click.prevent="exportChart('encodingSpeedChart'); openExport = false" class="block px-4 py-2 text-xs text-gray-700 dark:text-gray-300 hover:bg-emerald-50 dark:hover:bg-slate-700">Download PNG</a>
                        </div>
                    </div>
                </div>
                <div class="relative flex-1 w-full min-h-0"><canvas id="encodingSpeedChart"></canvas></div>
            </div>
        </div>
    </div>

</div>

<!-- Chart.js Setup -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.2.0"></script>
<script>
    // Global Chart Configuration
    Chart.register(ChartDataLabels);
    Chart.defaults.font.family = "'Inter', 'sans-serif'";
    Chart.defaults.color = '#64748b';
    Chart.defaults.plugins.tooltip.backgroundColor = 'rgba(15, 23, 42, 0.9)';
    Chart.defaults.plugins.tooltip.titleFont = { size: 14, weight: 'bold' };
    Chart.defaults.plugins.tooltip.padding = 12;
    Chart.defaults.plugins.tooltip.cornerRadius = 8;
    
    Chart.defaults.plugins.datalabels.color = '#ffffff';
    Chart.defaults.plugins.datalabels.font = { weight: 'bold', size: 12 };
    Chart.defaults.plugins.datalabels.formatter = Math.round;
    Chart.defaults.plugins.datalabels.display = function(context) { return context.dataset.data[context.dataIndex] > 0; };

    // Chart Instances Store
    const charts = {};

    document.addEventListener("DOMContentLoaded", function () {
        // 1. Visit Volume
        const volumeCtx = document.getElementById('visitVolumeChart').getContext('2d');
        const volumeData = {!! json_encode($visitVolumeData) !!};
        let gradient = volumeCtx.createLinearGradient(0, 0, 0, 400);
        gradient.addColorStop(0, 'rgba(16, 185, 129, 0.4)');
        gradient.addColorStop(1, 'rgba(16, 185, 129, 0.0)');

        charts['volume'] = new Chart(volumeCtx, {
            type: 'line',
            data: {
                labels: volumeData.labels,
                datasets: [{
                    label: 'Patient Visits', data: volumeData.data,
                    borderColor: '#10b981', backgroundColor: gradient, borderWidth: 3,
                    tension: 0.4, fill: true, pointBackgroundColor: '#ffffff', pointBorderColor: '#10b981', pointBorderWidth: 2, pointRadius: 5, pointHoverRadius: 7
                }]
            },
            options: {
                responsive: true, maintainAspectRatio: false, layout: { padding: { top: 25 } },
                plugins: { legend: { display: false }, datalabels: { align: 'top', anchor: 'end', color: '#10b981', backgroundColor: 'rgba(255, 255, 255, 0.9)', borderRadius: 4, padding: 4 } },
                scales: { y: { beginAtZero: true, suggestedMax: 5, ticks: { precision: 0, stepSize: 1 }, grid: { borderDash: [4, 4] } }, x: { grid: { display: false } } }
            }
        });

        // 2. Peak Hours
        const peakCtx = document.getElementById('peakHoursChart').getContext('2d');
        const peakData = {!! json_encode($peakHoursData) !!};
        charts['peak'] = new Chart(peakCtx, {
            type: 'bar',
            data: {
                labels: peakData.labels,
                datasets: [{ label: 'Consultations', data: peakData.data, backgroundColor: '#8b5cf6', borderRadius: 4 }]
            },
            options: {
                responsive: true, maintainAspectRatio: false, layout: { padding: { top: 25 } },
                plugins: { legend: { display: false }, datalabels: { align: 'top', anchor: 'end', color: '#8b5cf6' } },
                scales: { y: { beginAtZero: true, ticks: { precision: 0 }, grid: { borderDash: [4, 4] } }, x: { grid: { display: false } } }
            }
        });

        // 3. Workload
        const workCtx = document.getElementById('workloadChart').getContext('2d');
        const workDataRaw = {!! json_encode($workloadFormatted) !!};
        charts['workload'] = new Chart(workCtx, {
            type: 'bar',
            data: {
                labels: Object.keys(workDataRaw),
                datasets: [{ label: 'Consultations', data: Object.values(workDataRaw), backgroundColor: '#f59e0b', borderRadius: 4 }]
            },
            options: {
                indexAxis: 'y', responsive: true, maintainAspectRatio: false, layout: { padding: { right: 40 } },
                plugins: { legend: { display: false }, datalabels: { align: 'right', anchor: 'end', color: '#f59e0b' } },
                scales: { x: { beginAtZero: true, ticks: { precision: 0 } }, y: { grid: { display: false } } }
            }
        });

        // 4. Age-Sex Pyramid
        const ageSexCtx = document.getElementById('ageSexChart').getContext('2d');
        const demoData = {!! json_encode($demoData) !!};
        charts['demographics'] = new Chart(ageSexCtx, {
            type: 'bar',
            data: {
                labels: demoData.labels,
                datasets: [
                    { label: 'Male', data: demoData.Male.map(v => -Math.abs(v)), backgroundColor: '#3b82f6', borderRadius: { topLeft: 4, bottomLeft: 4 } },
                    { label: 'Female', data: demoData.Female, backgroundColor: '#ec4899', borderRadius: { topRight: 4, bottomRight: 4 } }
                ]
            },
            options: {
                indexAxis: 'y', responsive: true, maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'top', labels: { boxWidth: 14, usePointStyle: true, font: { size: 13 } } },
                    datalabels: { formatter: (value) => Math.abs(value) },
                    tooltip: { callbacks: { label: function (context) { return context.dataset.label + ': ' + Math.abs(context.raw); } } }
                },
                scales: { x: { stacked: true, ticks: { callback: function (value) { return Math.abs(value); }, precision: 0 } }, y: { stacked: true, grid: { display: false } } }
            }
        });

        // 5. Classification Doughnut
        const classCtx = document.getElementById('classificationChart').getContext('2d');
        const classDataRaw = {!! json_encode($classificationData) !!};
        charts['classification'] = new Chart(classCtx, {
            type: 'doughnut',
            data: {
                labels: Object.keys(classDataRaw),
                datasets: [{ data: Object.values(classDataRaw), backgroundColor: ['#0ea5e9', '#8b5cf6', '#f43f5e', '#10b981', '#f59e0b', '#64748b'], borderWidth: 0, hoverOffset: 4 }]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                plugins: { 
                    legend: { 
                        position: window.innerWidth < 768 ? 'bottom' : 'right', 
                        labels: { boxWidth: 12, padding: 15, font: { size: 11, weight: '600' }, usePointStyle: true } 
                    } 
                }, 
                cutout: '70%',
                onResize: function(chart, size) {
                    chart.options.plugins.legend.position = size.width < 500 ? 'bottom' : 'right';
                }
            }
        });

        // 6. Severity Doughnut
        const sevCtx = document.getElementById('severityChart').getContext('2d');
        const sevDataRaw = {!! json_encode($severityData) !!};
        const sevColors = Object.keys(sevDataRaw).map(l => {
            const lower = l.toLowerCase();
            if (lower === 'light') return '#10b981';
            if (lower === 'mild') return '#f59e0b';
            if (lower === 'severe') return '#ef4444';
            return '#64748b';
        });
        charts['severity'] = new Chart(sevCtx, {
            type: 'doughnut',
            data: {
                labels: Object.keys(sevDataRaw).map(l => l.charAt(0).toUpperCase() + l.slice(1)),
                datasets: [{ data: Object.values(sevDataRaw), backgroundColor: sevColors, borderWidth: 0, hoverOffset: 4 }]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                plugins: { 
                    legend: { 
                        position: window.innerWidth < 768 ? 'bottom' : 'right', 
                        labels: { boxWidth: 12, padding: 15, font: { size: 11, weight: '600' }, usePointStyle: true } 
                    } 
                }, 
                cutout: '70%',
                onResize: function(chart, size) {
                    chart.options.plugins.legend.position = size.width < 500 ? 'bottom' : 'right';
                }
            }
        });

        // 7. Barangay Heatmap (Dynamic height based on count)
        const brgyCtx = document.getElementById('barangayChart').getContext('2d');
        const brgyDataRaw = {!! json_encode($barangayData) !!};
        const brgyCount = Object.keys(brgyDataRaw).length;
        // Dynamic height: 45px per barangay, minimum 300px
        const brgyHeight = Math.max(300, brgyCount * 45);
        document.getElementById('barangayChartContainer').style.height = brgyHeight + 'px';
        // Generate a gradient color palette based on value intensity
        const brgyValues = Object.values(brgyDataRaw);
        const brgyMax = Math.max(...brgyValues, 1);
        const brgyColors = brgyValues.map(v => {
            const intensity = v / brgyMax;
            const r = Math.round(20 + (0 - 20) * intensity);
            const g = Math.round(184 + (180 - 184) * intensity);
            const b = Math.round(166 + (100 - 166) * intensity);
            return `rgba(${r}, ${g}, ${b}, ${0.5 + intensity * 0.5})`;
        });
        charts['barangay'] = new Chart(brgyCtx, {
            type: 'bar',
            data: {
                labels: Object.keys(brgyDataRaw),
                datasets: [{ label: 'Visits', data: brgyValues, backgroundColor: brgyColors, borderRadius: 6, borderSkipped: false, barPercentage: 0.7, categoryPercentage: 0.85 }]
            },
            options: {
                indexAxis: 'y', responsive: true, maintainAspectRatio: false, layout: { padding: { right: 40 } },
                plugins: { legend: { display: false }, datalabels: { align: 'right', anchor: 'end', color: '#0d9488', font: { weight: '600', size: 12 } } },
                scales: { x: { beginAtZero: true, ticks: { precision: 0, font: { size: 11 } }, grid: { color: 'rgba(148,163,184,0.08)' } }, y: { grid: { display: false }, ticks: { font: { size: 12, weight: '500' } } } }
            }
        });

        // 8. Staff Productivity: Duration Chart
        const durCtx = document.getElementById('durationStaffChart').getContext('2d');
        const durDataRaw = {!! json_encode($staffProductivity) !!};
        charts['duration'] = new Chart(durCtx, {
            type: 'bar',
            data: {
                labels: durDataRaw.durationChartLabels,
                datasets: [{ label: 'Minutes', data: durDataRaw.durationChartData, backgroundColor: '#3b82f6', borderRadius: 4 }]
            },
            options: {
                indexAxis: 'y', responsive: true, maintainAspectRatio: false, layout: { padding: { right: 40 } },
                plugins: { legend: { display: false }, datalabels: { align: 'right', anchor: 'end', color: '#3b82f6', formatter: function(value) { return value + 'm'; } } },
                scales: { x: { beginAtZero: true, ticks: { precision: 0 } }, y: { grid: { display: false } } }
            }
        });

        // 9. Staff Productivity: Encoding Speed Chart
        const encCtx = document.getElementById('encodingSpeedChart').getContext('2d');
        charts['encoding'] = new Chart(encCtx, {
            type: 'bar',
            data: {
                labels: durDataRaw.encodingChartLabels,
                datasets: [{ label: 'Seconds', data: durDataRaw.encodingChartData, backgroundColor: '#8b5cf6', borderRadius: 4 }]
            },
            options: {
                indexAxis: 'y', responsive: true, maintainAspectRatio: false, layout: { padding: { right: 40 } },
                plugins: { legend: { display: false }, datalabels: { align: 'right', anchor: 'end', color: '#8b5cf6', formatter: function(value) { return value + 's'; } } },
                scales: { x: { beginAtZero: true, ticks: { precision: 0 } }, y: { grid: { display: false } } }
            }
        });
    });

    function analyticsDashboard() {
        return {
            productivityTab: 'clinical',
            productivityStaff: 'all',
            productivityTime: '{{ $timeFilter }}',
            productivityData: {!! json_encode($staffProductivity) !!},
            async fetchProductivity() {
                try {
                    const response = await fetch(`/admin/analytics/staff-productivity?time_filter=${this.productivityTime}&staff_id=${this.productivityStaff}`);
                    const data = await response.json();
                    this.productivityData = data;
                    
                    // Update Duration Chart
                    const durChart = charts['duration'];
                    if (durChart) {
                        durChart.data.labels = data.durationChartLabels;
                        durChart.data.datasets[0].data = data.durationChartData;
                        durChart.update();
                    }

                    // Update Encoding Chart
                    const encChart = charts['encoding'];
                    if (encChart) {
                        encChart.data.labels = data.encodingChartLabels;
                        encChart.data.datasets[0].data = data.encodingChartData;
                        encChart.update();
                    }
                } catch (e) {
                    console.error("Failed to fetch productivity", e);
                }
            },
            async updateChart(chartId, filterValue) {
                try {
                    const response = await fetch(`/admin/analytics/chart/${chartId}?time_filter=${filterValue}`);
                    const json = await response.json();
                    const chart = charts[chartId];
                    if (chart) {
                        if (chartId === 'demographics') {
                            chart.data.labels = json.labels;
                            chart.data.datasets[0].data = json.Male.map(v => -Math.abs(v));
                            chart.data.datasets[1].data = json.Female;
                        } else {
                            chart.data.labels = json.labels;
                            chart.data.datasets[0].data = json.data;
                            
                            // Adjust colors if severity dynamically changes length
                            if (chartId === 'severity') {
                                chart.data.datasets[0].backgroundColor = json.labels.map(l => {
                                    const lower = l.toLowerCase();
                                    if (lower === 'light') return '#10b981';
                                    if (lower === 'mild') return '#f59e0b';
                                    if (lower === 'severe') return '#ef4444';
                                    return '#64748b';
                                });
                            }
                        }
                        chart.update();
                    }
                } catch (e) {
                    console.error("Failed to update chart", e);
                }
            },
            exportChart(canvasId) {
                const canvas = document.getElementById(canvasId);
                const image = canvas.toDataURL("image/png").replace("image/png", "image/octet-stream");
                const link = document.createElement('a');
                link.download = canvasId + '-' + new Date().toISOString().split('T')[0] + '.png';
                link.href = image;
                link.click();
            },
            exportCSV(chartKey) {
                const chart = charts[chartKey];
                if (!chart) return;
                
                let csvContent = "data:text/csv;charset=utf-8,";
                
                if (chartKey === 'demographics') {
                    csvContent += "Age Group,Male,Female\n";
                    const labels = chart.data.labels;
                    const males = chart.data.datasets[0].data.map(v => Math.abs(v));
                    const females = chart.data.datasets[1].data;
                    
                    for (let i = 0; i < labels.length; i++) {
                        csvContent += `"${labels[i]}",${males[i]},${females[i]}\n`;
                    }
                } else {
                    csvContent += "Label,Value\n";
                    const labels = chart.data.labels;
                    const data = chart.data.datasets[0].data;
                    
                    for (let i = 0; i < labels.length; i++) {
                        csvContent += `"${labels[i]}",${data[i]}\n`;
                    }
                }
                
                const encodedUri = encodeURI(csvContent);
                const link = document.createElement("a");
                link.setAttribute("href", encodedUri);
                link.setAttribute("download", chartKey + "_data_" + new Date().toISOString().split('T')[0] + ".csv");
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
            }
        }
    }
</script>
@endsection
