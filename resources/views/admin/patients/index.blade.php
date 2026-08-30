@extends('layouts.admin')

@section('header', 'Patient Master Records')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8" x-data="{
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
        this.$refs.filterForm.querySelectorAll('input[type=text]').forEach(el => el.value = '');
        this.$refs.filterForm.querySelector('[name=classification]').value = 'all';
        this.submitForm();
    }
}">
    
    <!-- Stats Overview -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-sm border border-gray-100 dark:border-gray-700">
            <div class="flex items-center gap-4">
                <div class="p-3 bg-blue-50 dark:bg-blue-900/30 rounded-xl">
                    <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 005.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Registered Patients</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ \App\Models\Patient::count() }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-sm border border-gray-100 dark:border-gray-700">
            <div class="flex items-center gap-4">
                <div class="p-3 bg-emerald-50 dark:bg-emerald-900/30 rounded-xl">
                    <svg class="w-6 h-6 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Consultations</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ \App\Models\Consultation::count() }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-sm border border-gray-100 dark:border-gray-700">
            <div class="flex items-center gap-4">
                <div class="p-3 bg-amber-50 dark:bg-amber-900/30 rounded-xl">
                    <svg class="w-6 h-6 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">New Patients (Last 30 Days)</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ \App\Models\Patient::where('created_at', '>=', now()->subDays(30))->count() }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-800 mb-6 overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-800 bg-slate-50/80 dark:bg-slate-800/40">
            <h2 class="text-xs font-extrabold text-slate-700 dark:text-slate-300 uppercase tracking-wider">Search & Filter</h2>
        </div>
        <div class="p-6">
            <form x-ref="filterForm" action="{{ route('admin.patients.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-3.5 items-end">
                
                <!-- Search -->
                <div class="sm:col-span-2 lg:col-span-7">
                    <label class="block text-[11px] font-extrabold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1.5">Search Patient</label>
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400 group-focus-within:text-blue-500 transition-colors">
                            <svg class="h-4.5 w-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </div>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by patient name, ID, or contact..." 
                            @input.debounce.300ms="submitForm"
                            class="h-11 pl-10 pr-4 block w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-white shadow-2xs focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 text-sm font-medium transition-all placeholder:text-slate-400">
                    </div>
                </div>

                <!-- Classification -->
                <div class="sm:col-span-1 lg:col-span-3">
                    <label class="block text-[11px] font-extrabold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1.5">Classification</label>
                    <x-select 
                        name="classification" 
                        :options="[
                            'all' => 'All Classifications',
                            'Pediatric' => 'Pediatric',
                            'Regular Adult' => 'Regular Adult',
                            'Senior Citizen' => 'Senior Citizen',
                            'PWD' => 'PWD'
                        ]" 
                        :value="request('classification', 'all')"
                        @change="submitForm"
                        class="!h-11 !py-0 flex items-center font-semibold"
                    />
                </div>

                <!-- Clear -->
                <div class="sm:col-span-1 lg:col-span-2">
                    <button type="button" @click="clearFilters()" title="Clear all filters" class="w-full h-11 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-300 border border-slate-300 dark:border-slate-700 px-4 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-700 transition-all shadow-2xs font-bold text-sm shrink-0 flex items-center justify-center gap-2 active:scale-95 cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        <span>Clear</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Main List Container -->
    <div id="patient-table-container" class="bg-white dark:bg-slate-900 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-800 overflow-hidden relative min-h-[400px]">
        <!-- Loading Overlay -->
        <div x-show="loading" class="absolute inset-0 bg-white/70 dark:bg-slate-900/70 z-50 flex flex-col items-center justify-center backdrop-blur-[1px] transition-opacity duration-300" style="display: none;">
            <div class="w-8 h-8 rounded-full border-4 border-blue-200 border-t-blue-600 animate-spin mb-2"></div>
            <span class="text-sm font-bold text-blue-800 dark:text-blue-400">Updating...</span>
        </div>

        <div id="patient-table-contents">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50/90 dark:bg-slate-900/90 border-b border-slate-200 dark:border-slate-800 text-[11px] uppercase tracking-wider text-slate-500 dark:text-slate-400 font-extrabold">
                            <th class="px-6 py-4">Patient Name</th>
                            <th class="px-6 py-4">Demographics</th>
                            <th class="px-6 py-4">Classification</th>
                            <th class="px-6 py-4 text-center">Total Visits</th>
                            <th class="px-6 py-4">Last Activity</th>
                            <th class="px-6 py-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800 text-sm">
                        @forelse($patients as $patient)
                        <tr class="hover:bg-blue-50/30 dark:hover:bg-slate-800/40 transition">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full bg-blue-100 dark:bg-blue-900/40 flex items-center justify-center text-blue-700 dark:text-blue-300 font-bold border border-blue-200 dark:border-blue-800 shadow-2xs">
                                        {{ substr($patient->first_name, 0, 1) }}{{ substr($patient->last_name, 0, 1) }}
                                    </div>
                                    <div>
                                        <div class="font-bold text-slate-900 dark:text-white text-sm">{{ $patient->full_name }}</div>
                                        <div class="text-xs text-slate-500 dark:text-slate-400">ID: {{ $patient->patient_id }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-slate-900 dark:text-white font-medium">{{ $patient->sex ?? 'N/A' }}, {{ $patient->dob ? $patient->dob->age . ' yrs' : 'N/A' }}</div>
                                <div class="text-xs text-slate-500 dark:text-slate-400">{{ $patient->blood_type ? 'Type '.$patient->blood_type : '' }} | {{ $patient->civil_status ?? '' }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold border shadow-2xs
                                    @if($patient->classification == 'Pediatric') bg-orange-50 text-orange-800 border-orange-200 dark:bg-orange-950/50 dark:text-orange-300 dark:border-orange-800/60
                                    @elseif($patient->classification == 'Senior Citizen') bg-purple-50 text-purple-800 border-purple-200 dark:bg-purple-950/50 dark:text-purple-300 dark:border-purple-800/60
                                    @elseif($patient->classification == 'PWD') bg-blue-50 text-blue-800 border-blue-200 dark:bg-blue-950/50 dark:text-blue-300 dark:border-blue-800/60
                                    @else bg-slate-50 text-slate-700 border-slate-200 dark:bg-slate-800 dark:text-slate-300 dark:border-slate-700 @endif">
                                    {{ $patient->classification }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="text-xs font-bold text-slate-900 dark:text-white bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 px-3 py-1 rounded-xl shadow-2xs">
                                    {{ $patient->consultations_count }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-slate-600 dark:text-slate-400 font-medium">
                                {{ $patient->updated_at->diffForHumans() }}
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex justify-end">
                                    <a href="{{ route('admin.patients.show', $patient) }}" class="h-9 px-3.5 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 hover:border-blue-500 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-blue-50/50 dark:hover:bg-blue-950/40 text-slate-700 dark:text-slate-300 rounded-xl text-xs font-bold shadow-2xs transition-all inline-flex items-center gap-1.5 cursor-pointer">
                                        <span>View Records</span>
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="p-8 text-center text-gray-500 dark:text-gray-400">
                                <svg class="w-12 h-12 text-gray-300 dark:text-gray-600 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                                <p class="text-base font-medium">No patient records found.</p>
                                <p class="text-sm mt-1">Try adjusting your filters or search query.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($patients->hasPages())
            <div class="p-4 border-t border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 flex justify-center">
                {{ $patients->links('pagination::tailwind') }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
