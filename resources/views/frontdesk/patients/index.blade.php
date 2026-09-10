@extends('layouts.frontdesk')

@section('title', 'Patient Master Records')
@section('header', 'Patient Master Records')

@section('content')
<div class="max-w-7xl mx-auto space-y-6 pb-12" x-data="{
    loading: false,
    async submitForm() {
        this.loading = true;
        const form = this.$refs.filterForm;
        const url = new URL(form.action);
        const formData = new FormData(form);
        formData.forEach((value, key) => url.searchParams.append(key, value));
        
        try {
            const response = await fetch(url, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            const html = await response.text();
            
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            const newTable = doc.getElementById('patient-table-contents').innerHTML;
            document.getElementById('patient-table-contents').innerHTML = newTable;
            
            window.history.pushState({}, '', url);
        } catch (error) {
            console.error('Error fetching data:', error);
            window.location.href = url.toString();
        } finally {
            this.loading = false;
        }
    },
    clearFilters() {
        this.$refs.filterForm.reset();
        if (document.getElementById('date_from')._flatpickr) {
            document.getElementById('date_from')._flatpickr.clear();
            document.getElementById('date_from')._flatpickr.set('maxDate', 'today');
        }
        if (document.getElementById('date_to')._flatpickr) {
            document.getElementById('date_to')._flatpickr.clear();
            document.getElementById('date_to')._flatpickr.set('minDate', null);
        }
        this.$refs.filterForm.querySelectorAll('input[type=text]').forEach(el => el.value = '');
        const classSelect = this.$refs.filterForm.querySelector('[name=classification]');
        if (classSelect) classSelect.value = 'all';
        this.submitForm();
    }
}">
    
    <!-- Breadcrumb Navigation -->
    <nav class="flex items-center gap-2 text-xs font-semibold text-slate-500 dark:text-slate-400 py-1" aria-label="Breadcrumb">
        <a href="{{ route('frontdesk.dashboard') }}" class="hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors flex items-center gap-1.5 shrink-0">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
            <span>Home</span>
        </a>
        <span class="text-slate-300 dark:text-slate-700">/</span>
        <span class="hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors">Clinical Records</span>
        <span class="text-slate-300 dark:text-slate-700">/</span>
        <span class="text-slate-800 dark:text-slate-200 font-bold">Patient Master Directory</span>
    </nav>

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 pb-4 border-b border-slate-200/80 dark:border-slate-800">
        <div>
            <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-lg bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 text-[11px] font-bold uppercase tracking-wider mb-2 border border-emerald-500/20">
                Municipal Clinical Registry
            </div>
            <h1 class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white tracking-tight flex items-center gap-2.5">
                <span class="w-9 h-9 sm:w-10 sm:h-10 rounded-2xl bg-emerald-100/80 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0 border border-emerald-500/20 shadow-2xs">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </span>
                <span>Patient Master Records</span>
            </h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Centralized citizen directory with past consultation encounters, vitals histories, and rapid intake routing.</p>
        </div>

        <div class="flex items-center gap-2.5">
            <a href="{{ route('frontdesk.registration.index', ['new' => 1]) }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-gradient-to-r from-emerald-600 to-emerald-700 hover:from-emerald-700 hover:to-emerald-800 text-white text-xs font-bold shadow-md shadow-emerald-600/20 active:scale-[0.98] transition cursor-pointer">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                <span>Register New Patient</span>
            </a>
        </div>
    </div>
    
    <!-- Stats Overview Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
        <!-- Total Registered Patients -->
        <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 border border-slate-200/90 dark:border-slate-800 shadow-[0_8px_30px_rgb(0,0,0,0.04)] dark:shadow-none flex items-center justify-between">
            <div>
                <p class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Registered Citizens</p>
                <p class="text-3xl sm:text-4xl font-black text-slate-900 dark:text-white mt-1.5 tracking-tight">
                    {{ \App\Models\Patient::count() }}
                </p>
                <p class="text-xs text-slate-400 mt-1 font-medium">Master medical records</p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-blue-100/80 dark:bg-blue-950/60 text-blue-600 dark:text-blue-400 flex items-center justify-center shrink-0 border border-blue-500/20 shadow-2xs">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
            </div>
        </div>
        
        <!-- Total Consultations -->
        <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 border border-slate-200/90 dark:border-slate-800 shadow-[0_8px_30px_rgb(0,0,0,0.04)] dark:shadow-none flex items-center justify-between">
            <div>
                <p class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Total Consultations</p>
                <p class="text-3xl sm:text-4xl font-black text-slate-900 dark:text-white mt-1.5 tracking-tight">
                    {{ \App\Models\Consultation::count() }}
                </p>
                <p class="text-xs text-slate-400 mt-1 font-medium">Encounter history</p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-emerald-100/80 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0 border border-emerald-500/20 shadow-2xs">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
            </div>
        </div>

        <!-- New Patients (30 Days) -->
        <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 border border-slate-200/90 dark:border-slate-800 shadow-[0_8px_30px_rgb(0,0,0,0.04)] dark:shadow-none flex items-center justify-between">
            <div>
                <p class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Recent Registrations</p>
                <p class="text-3xl sm:text-4xl font-black text-slate-900 dark:text-white mt-1.5 tracking-tight">
                    {{ \App\Models\Patient::where('created_at', '>=', now()->subDays(30))->count() }}
                </p>
                <p class="text-xs text-slate-400 mt-1 font-medium">Past 30 days intake</p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-amber-100/80 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400 flex items-center justify-center shrink-0 border border-amber-500/20 shadow-2xs">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
        </div>
    </div>

    <!-- Search & Filter Bar -->
    <div class="bg-white/80 dark:bg-slate-900/80 backdrop-blur-md rounded-3xl border border-slate-200/80 dark:border-slate-800 p-4 sm:p-5 shadow-2xs">
        <form x-ref="filterForm" action="{{ route('frontdesk.patients.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-3.5 items-end">
            <!-- Search -->
            <div class="sm:col-span-2 lg:col-span-4">
                <label class="block text-[11px] font-extrabold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1.5">Search Citizen Directory</label>
                <div class="relative group">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </div>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name, ID, contact..." 
                        @input.debounce.300ms="submitForm"
                        class="h-11 pl-10 pr-4 block w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50/70 dark:bg-slate-800/60 text-slate-900 dark:text-white text-xs font-semibold focus:border-emerald-500 shadow-2xs transition-all placeholder:text-slate-400">
                </div>
            </div>

            <!-- Classification Filter -->
            <div class="sm:col-span-1 lg:col-span-3">
                <label class="block text-[11px] font-extrabold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1.5">Demographic Category</label>
                <x-select 
                    name="classification" 
                    :options="[
                        'all' => 'All Classifications',
                        'Pediatric' => 'Pediatric (Children)',
                        'Regular Adult' => 'Regular Adult',
                        'Senior Citizen' => 'Senior Citizen',
                        'PWD' => 'PWD'
                    ]" 
                    :value="request('classification', 'all')"
                    @change="submitForm"
                    class="!h-11 !py-0 flex items-center font-semibold text-xs bg-slate-50/70 dark:bg-slate-800/60"
                />
            </div>

            <!-- Date Registered (From) -->
            <div class="sm:col-span-1 lg:col-span-2">
                <label class="block text-[11px] font-extrabold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1.5">Registered From</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    </div>
                    <input type="text" id="date_from" name="date_from" value="{{ request('date_from') }}" placeholder="Start Date"
                        @change="submitForm"
                        class="flatpickr-date h-11 w-full pl-9 pr-3 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50/70 dark:bg-slate-800/60 text-slate-900 dark:text-white text-xs font-semibold focus:border-emerald-500 shadow-2xs cursor-pointer">
                </div>
            </div>

            <!-- Date Registered (To) -->
            <div class="sm:col-span-1 lg:col-span-2">
                <label class="block text-[11px] font-extrabold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1.5">Registered To</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    </div>
                    <input type="text" id="date_to" name="date_to" value="{{ request('date_to') }}" placeholder="End Date"
                        @change="submitForm"
                        class="flatpickr-date h-11 w-full pl-9 pr-3 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50/70 dark:bg-slate-800/60 text-slate-900 dark:text-white text-xs font-semibold focus:border-emerald-500 shadow-2xs cursor-pointer">
                </div>
            </div>

            <!-- Reset Filters -->
            <div class="sm:col-span-1 lg:col-span-1">
                <button type="button" @click="clearFilters()" title="Reset search and filters" 
                    class="w-full h-11 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-700 px-3 rounded-xl transition-all shadow-2xs font-bold text-xs flex items-center justify-center gap-1.5 active:scale-95 cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    <span>Reset</span>
                </button>
            </div>
        </form>
    </div>

    <!-- Main Table Container -->
    <div id="patient-table-container" class="bg-white dark:bg-slate-900 rounded-3xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] dark:shadow-none border border-slate-200/90 dark:border-slate-800 overflow-hidden relative min-h-[400px]">
        <!-- Loading Overlay -->
        <div x-show="loading" class="absolute inset-0 bg-white/70 dark:bg-slate-900/70 z-50 flex flex-col items-center justify-center backdrop-blur-[1px] transition-opacity duration-300" style="display: none;">
            <div class="w-8 h-8 rounded-full border-4 border-emerald-200 border-t-emerald-600 animate-spin mb-2"></div>
            <span class="text-xs font-bold text-emerald-800 dark:text-emerald-400">Updating records...</span>
        </div>

        <div id="patient-table-contents">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50/90 dark:bg-slate-950/80 border-b border-slate-200/80 dark:border-slate-800 text-[11px] uppercase tracking-wider text-slate-500 dark:text-slate-400 font-extrabold">
                            <th class="px-6 py-4">Citizen Patient</th>
                            <th class="px-6 py-4">Demographics</th>
                            <th class="px-6 py-4">Classification</th>
                            <th class="px-6 py-4 text-center">Encounters</th>
                            <th class="px-6 py-4">Registration Date</th>
                            <th class="px-6 py-4 text-right">Desk Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800 text-sm">
                        @forelse($patients as $patient)
                        <tr class="hover:bg-emerald-50/30 dark:hover:bg-slate-800/40 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-2xl bg-emerald-100/80 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 font-black flex items-center justify-center border border-emerald-500/20 shadow-2xs shrink-0 text-xs">
                                        {{ $patient->initials ?? strtoupper(substr($patient->first_name, 0, 1) . substr($patient->last_name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="font-bold text-slate-900 dark:text-white text-sm">{{ $patient->full_name }}</div>
                                        <div class="text-xs font-mono font-medium text-slate-500 dark:text-slate-400">ID: {{ $patient->patient_id }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-slate-900 dark:text-white font-semibold text-xs sm:text-sm">{{ $patient->sex ?? 'N/A' }}, {{ $patient->dob ? $patient->dob->age . ' yrs' : 'N/A' }}</div>
                                <div class="text-xs text-slate-400 mt-0.5">{{ $patient->blood_type ? 'Type '.$patient->blood_type : '' }} {{ $patient->civil_status ? '| ' . $patient->civil_status : '' }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold border shadow-2xs
                                    @if($patient->classification == 'Pediatric') bg-orange-50 text-orange-800 border-orange-200 dark:bg-orange-950/50 dark:text-orange-300 dark:border-orange-800/60
                                    @elseif($patient->classification == 'Senior Citizen') bg-purple-50 text-purple-800 border-purple-200 dark:bg-purple-950/50 dark:text-purple-300 dark:border-purple-800/60
                                    @elseif($patient->classification == 'PWD') bg-blue-50 text-blue-800 border-blue-200 dark:bg-blue-950/50 dark:text-blue-300 dark:border-blue-800/60
                                    @else bg-slate-100 text-slate-700 border-slate-200 dark:bg-slate-800 dark:text-slate-300 dark:border-slate-700 @endif">
                                    {{ $patient->classification ?? 'Regular Adult' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="text-xs font-black text-slate-900 dark:text-white bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 px-3 py-1 rounded-xl shadow-2xs">
                                    {{ $patient->consultations_count }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-xs font-semibold text-slate-500 dark:text-slate-400">
                                <div>{{ $patient->created_at->format('M d, Y') }}</div>
                                <div class="text-[11px] text-slate-400">{{ $patient->created_at->format('h:i A') }}</div>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <!-- Quick Queue Intake Link -->
                                    <a href="{{ route('frontdesk.registration.index', ['selected_id' => $patient->id]) }}" title="Begin triage and generate queue ticket" class="h-8 px-3 bg-emerald-50 hover:bg-emerald-100 dark:bg-emerald-950/50 dark:hover:bg-emerald-900/60 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800 rounded-xl text-xs font-bold shadow-2xs transition-all inline-flex items-center gap-1.5 cursor-pointer active:scale-95">
                                        <svg class="w-3.5 h-3.5 text-emerald-600 dark:text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                        <span>Intake / Queue</span>
                                    </a>

                                    <!-- View Profile Record -->
                                    <a href="{{ route('frontdesk.patients.show', $patient) }}" class="h-8 px-3 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 hover:text-slate-900 rounded-xl text-xs font-bold shadow-2xs transition-all inline-flex items-center gap-1.5 cursor-pointer active:scale-95">
                                        <span>Details</span>
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="p-16 text-center text-slate-500 dark:text-slate-400">
                                <div class="w-14 h-14 rounded-2xl bg-slate-100 dark:bg-slate-800 text-slate-400 dark:text-slate-500 mx-auto flex items-center justify-center mb-3 shadow-xs">
                                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                                </div>
                                <h3 class="text-base font-bold text-slate-900 dark:text-white mb-1">No citizen records found</h3>
                                <p class="text-xs text-slate-500 dark:text-slate-400">Try adjusting your search criteria or demographic filters.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($patients->hasPages())
            <div class="p-4 border-t border-slate-100 dark:border-slate-800/80 bg-slate-50/70 dark:bg-slate-900/60 flex justify-center">
                {{ $patients->links('vendor.pagination.shadcn') }}
            </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const dateFromInput = document.getElementById('date_from');
        const dateToInput = document.getElementById('date_to');

        if (dateFromInput && dateToInput) {
            const dateFromPicker = flatpickr(dateFromInput, {
                dateFormat: "Y-m-d",
                altInput: true,
                altFormat: "F j, Y",
                allowInput: true,
                maxDate: "today",
                onChange: function(selectedDates, dateStr, instance) {
                    if (dateToPicker) {
                        dateToPicker.set('minDate', dateStr);
                    }
                    dateFromInput.value = dateStr;
                    setTimeout(() => dateFromInput.dispatchEvent(new Event('change', { bubbles: true })), 10);
                }
            });

            const dateToPicker = flatpickr(dateToInput, {
                dateFormat: "Y-m-d",
                altInput: true,
                altFormat: "F j, Y",
                allowInput: true,
                maxDate: "today",
                onChange: function(selectedDates, dateStr, instance) {
                    if (dateFromPicker) {
                        dateFromPicker.set('maxDate', dateStr || "today");
                    }
                    dateToInput.value = dateStr;
                    setTimeout(() => dateToInput.dispatchEvent(new Event('change', { bubbles: true })), 10);
                }
            });
            
            if (dateFromInput.value) {
                dateToPicker.set('minDate', dateFromInput.value);
            }
            if (dateToInput.value) {
                dateFromPicker.set('maxDate', dateToInput.value);
            }
        }
    });
</script>
@endpush
@endsection
