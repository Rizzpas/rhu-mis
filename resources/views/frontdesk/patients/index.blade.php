@extends('layouts.frontdesk')

@section('header', 'Patient Master List')

@section('content')
<div class="max-w-7xl mx-auto" x-data="{
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
        this.$refs.filterForm.querySelector('[name=classification]').value = 'all';
        this.submitForm();
    }
}">

    <!-- Filters -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 mb-6 mt-4">
        <div class="p-4 border-b border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 rounded-t-xl">
            <h2 class="text-sm font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Advanced Filtering</h2>
        </div>
        <div class="p-6">
            <form x-ref="filterForm" action="{{ route('frontdesk.patients.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-5 items-end">
                
                <!-- Search -->
                <div class="md:col-span-1">
                    <label class="block text-[11px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Search Patient</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </div>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Name, PhilHealth..." 
                            @input.debounce.500ms="submitForm"
                            class="w-full pl-10 rounded-lg border-gray-300 dark:border-gray-600 shadow-sm focus:border-teal-500 focus:ring-teal-500 text-sm py-2.5 transition-colors">
                    </div>
                </div>

                <!-- Classification -->
                <div>
                    <label class="block text-[11px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Classification</label>
                    <select name="classification" @change="submitForm" class="w-full rounded-lg border-gray-300 dark:border-gray-600 shadow-sm focus:border-teal-500 focus:ring-teal-500 text-sm py-2.5 transition-colors bg-gray-50 hover:bg-white dark:bg-gray-800 cursor-pointer">
                        <option value="all">All Classifications</option>
                        <option value="Pediatric" {{ request('classification') == 'Pediatric' ? 'selected' : '' }}>Pediatric</option>
                        <option value="Regular Adult" {{ request('classification') == 'Regular Adult' ? 'selected' : '' }}>Regular Adult</option>
                        <option value="Senior Citizen" {{ request('classification') == 'Senior Citizen' ? 'selected' : '' }}>Senior Citizen</option>
                        <option value="PWD" {{ request('classification') == 'PWD' ? 'selected' : '' }}>PWD</option>
                    </select>
                </div>

                <!-- Date Range -->
                <div>
                    <label class="block text-[11px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Date Registered (From)</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        </div>
                        <input type="text" id="date_from" name="date_from" value="{{ request('date_from') }}" placeholder="Start Date"
                            @change="submitForm"
                            class="flatpickr-date w-full pl-10 rounded-lg border-gray-300 dark:border-gray-600 shadow-sm focus:border-teal-500 focus:ring-teal-500 text-sm py-2.5 transition-colors bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 dark:bg-gray-900 cursor-pointer text-gray-700 dark:text-gray-300">
                    </div>
                </div>
                <div>
                    <label class="block text-[11px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Date Registered (To)</label>
                    <div class="flex gap-2 relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none z-10">
                            <svg class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        </div>
                        <input type="text" id="date_to" name="date_to" value="{{ request('date_to') }}" placeholder="End Date"
                            @change="submitForm"
                            class="flatpickr-date w-full pl-10 rounded-lg border-gray-300 dark:border-gray-600 shadow-sm focus:border-teal-500 focus:ring-teal-500 text-sm py-2.5 transition-colors bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 dark:bg-gray-900 cursor-pointer text-gray-700 dark:text-gray-300 relative z-0">
                        <button type="button" @click="clearFilters()" title="Clear all filters" class="bg-gray-100 dark:bg-gray-900 text-gray-700 border border-gray-300 dark:border-gray-600 px-4 py-2.5 rounded-lg hover:bg-gray-200 hover:text-gray-900 dark:hover:text-white dark:text-white transition shadow-sm font-bold text-xs whitespace-nowrap shrink-0 flex items-center justify-center">
                            Clear Filter
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Data Table -->
    <div id="patient-table-container" class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden relative min-h-[400px]">
        <!-- Loading Overlay -->
        <div x-show="loading" class="absolute inset-0 bg-white dark:bg-gray-800/70 z-50 flex flex-col items-center justify-center backdrop-blur-[1px] transition-opacity duration-300" style="display: none;">
            <div class="w-8 h-8 rounded-full border-4 border-teal-200 border-t-teal-600 animate-spin mb-2"></div>
            <span class="text-sm font-bold text-teal-800">Updating...</span>
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
                        <th class="p-4">Date Registered</th>
                        <th class="p-4 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-sm">
                    @forelse($patients as $patient)
                        <tr class="hover:bg-teal-50/30 transition">
                            <td class="p-4">
                                <div class="font-bold text-gray-900 dark:text-white text-base">{{ $patient->full_name }}</div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">ID: {{ $patient->patient_id }}</div>
                            </td>
                            <td class="p-4">
                                <div class="text-gray-900 dark:text-white">{{ $patient->sex ?? 'N/A' }}, {{ $patient->dob ? $patient->dob->age . ' yrs' : 'N/A' }}</div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">{{ $patient->blood_type ? 'Type '.$patient->blood_type : '' }} | {{ $patient->civil_status ?? '' }}</div>
                            </td>
                            <td class="p-4">
                                <span class="bg-teal-100 text-teal-800 text-xs px-2.5 py-1 rounded-full font-bold border border-teal-200">
                                    {{ $patient->classification ?? 'N/A' }}
                                </span>
                            </td>
                            <td class="p-4 text-center">
                                <span class="text-sm font-bold text-gray-900 dark:text-white bg-gray-100 dark:bg-gray-900 px-3 py-1 rounded-lg">
                                    {{ $patient->consultations_count }}
                                </span>
                            </td>
                            <td class="p-4 text-gray-600 dark:text-gray-400">
                                {{ $patient->created_at->format('M d, Y h:i A') }}
                            </td>
                            <td class="p-4 text-center">
                                <a href="{{ route('frontdesk.patients.show', $patient) }}" class="inline-flex items-center gap-1 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 hover:border-teal-500 hover:text-teal-700 hover:bg-teal-50 text-gray-700 dark:text-gray-300 px-3 py-1.5 rounded-md text-sm shadow-sm transition font-medium">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                    View details
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="p-8 text-center text-gray-500 dark:text-gray-400">
                                <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
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

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const dateFromInput = document.getElementById('date_from');
        const dateToInput = document.getElementById('date_to');

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
                dateFromInput.dispatchEvent(new Event('change', { bubbles: true }));
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
                dateToInput.dispatchEvent(new Event('change', { bubbles: true }));
            }
        });
        
        // Initial setup for existing values to enforce logic on page load
        if (dateFromInput.value) {
            dateToPicker.set('minDate', dateFromInput.value);
        }
        if (dateToInput.value) {
            dateFromPicker.set('maxDate', dateToInput.value);
        }
    });
</script>
@endpush
@endsection
