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
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 mb-6">
        <div class="p-4 border-b border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 rounded-t-xl">
            <h2 class="text-sm font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Search & Filter</h2>
        </div>
        <div class="p-6">
            <form x-ref="filterForm" action="{{ route('admin.patients.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-5 items-end">
                
                <!-- Search -->
                <div class="md:col-span-2">
                    <label class="block text-[11px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Search Patient</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </div>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Name, Patient ID..." 
                            @input.debounce.300ms="submitForm"
                            class="w-full pl-10 rounded-lg border-gray-300 dark:border-gray-600 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm py-2.5 transition-colors dark:bg-gray-900 dark:text-white">
                    </div>
                </div>

                <!-- Classification -->
                <div class="md:col-span-1">
                    <label class="block text-[11px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Classification</label>
                    <select name="classification" @change="submitForm" class="w-full rounded-lg border-gray-300 dark:border-gray-600 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm py-2.5 transition-colors bg-gray-50 hover:bg-white dark:bg-gray-900 dark:text-white cursor-pointer">
                        <option value="all">All Classifications</option>
                        <option value="Pediatric" {{ request('classification') == 'Pediatric' ? 'selected' : '' }}>Pediatric</option>
                        <option value="Regular Adult" {{ request('classification') == 'Regular Adult' ? 'selected' : '' }}>Regular Adult</option>
                        <option value="Senior Citizen" {{ request('classification') == 'Senior Citizen' ? 'selected' : '' }}>Senior Citizen</option>
                        <option value="PWD" {{ request('classification') == 'PWD' ? 'selected' : '' }}>PWD</option>
                    </select>
                </div>

                <!-- Clear -->
                <div class="md:col-span-1">
                    <button type="button" @click="clearFilters()" title="Clear all filters" class="w-full bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 border border-gray-300 dark:border-gray-600 px-4 py-2.5 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition shadow-sm font-bold text-sm shrink-0 flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        Clear Filter
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Main List Container -->
    <div id="patient-table-container" class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden relative min-h-[400px]">
        <!-- Loading Overlay -->
        <div x-show="loading" class="absolute inset-0 bg-white/70 dark:bg-gray-800/70 z-50 flex flex-col items-center justify-center backdrop-blur-[1px] transition-opacity duration-300" style="display: none;">
            <div class="w-8 h-8 rounded-full border-4 border-blue-200 border-t-blue-600 animate-spin mb-2"></div>
            <span class="text-sm font-bold text-blue-800 dark:text-blue-400">Updating...</span>
        </div>

        <div id="patient-table-contents">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50 dark:bg-gray-900 border-b border-gray-200 dark:border-gray-700 text-xs uppercase tracking-wider text-gray-500 dark:text-gray-400 font-semibold">
                            <th class="p-4">Patient Name</th>
                            <th class="p-4">Demographics</th>
                            <th class="p-4">Classification</th>
                            <th class="p-4 text-center">Total Visits</th>
                            <th class="p-4">Last Activity</th>
                            <th class="p-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700 text-sm">
                        @forelse($patients as $patient)
                        <tr class="hover:bg-blue-50/30 dark:hover:bg-gray-700/30 transition">
                            <td class="p-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full bg-blue-100 dark:bg-blue-900/40 flex items-center justify-center text-blue-700 dark:text-blue-300 font-bold">
                                        {{ substr($patient->first_name, 0, 1) }}{{ substr($patient->last_name, 0, 1) }}
                                    </div>
                                    <div>
                                        <div class="font-bold text-gray-900 dark:text-white text-base">{{ $patient->full_name }}</div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400">ID: {{ $patient->patient_id }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="p-4">
                                <div class="text-gray-900 dark:text-white">{{ $patient->sex ?? 'N/A' }}, {{ $patient->dob ? $patient->dob->age . ' yrs' : 'N/A' }}</div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">{{ $patient->blood_type ? 'Type '.$patient->blood_type : '' }} | {{ $patient->civil_status ?? '' }}</div>
                            </td>
                            <td class="p-4">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold border
                                    @if($patient->classification == 'Pediatric') bg-orange-50 text-orange-800 border-orange-200 dark:bg-orange-900/30 dark:text-orange-400 dark:border-orange-800
                                    @elseif($patient->classification == 'Senior Citizen') bg-purple-50 text-purple-800 border-purple-200 dark:bg-purple-900/30 dark:text-purple-400 dark:border-purple-800
                                    @elseif($patient->classification == 'PWD') bg-blue-50 text-blue-800 border-blue-200 dark:bg-blue-900/30 dark:text-blue-400 dark:border-blue-800
                                    @else bg-gray-50 text-gray-800 border-gray-200 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 @endif">
                                    {{ $patient->classification }}
                                </span>
                            </td>
                            <td class="p-4 text-center">
                                <span class="text-sm font-bold text-gray-900 dark:text-white bg-gray-100 dark:bg-gray-900 px-3 py-1 rounded-lg">
                                    {{ $patient->consultations_count }}
                                </span>
                            </td>
                            <td class="p-4 text-gray-600 dark:text-gray-400">
                                {{ $patient->updated_at->diffForHumans() }}
                            </td>
                            <td class="p-4 text-right">
                                <a href="{{ route('admin.patients.show', $patient) }}" class="inline-flex items-center gap-1 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 hover:border-blue-500 hover:text-blue-700 hover:bg-blue-50 dark:hover:bg-blue-900/20 text-gray-700 dark:text-gray-300 px-3 py-1.5 rounded-md text-sm shadow-sm transition font-medium">
                                    View Full Records
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                </a>
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
