@extends('layouts.admin')

@section('header', 'System Audit Trail')

@section('content')
<div class="max-w-7xl mx-auto space-y-6 pb-12" x-data="{ 
    loading: false,
    selectedLog: null,
    showModal: false,
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
            const newTable = doc.getElementById('audit-table-contents').innerHTML;
            document.getElementById('audit-table-contents').innerHTML = newTable;

            window.history.pushState({}, '', url);
        } catch (error) {
            console.error('Error fetching data:', error);
            window.location.href = url.toString();
        } finally {
            this.loading = false;
        }
    },
    openModal(logData) {
        this.selectedLog = logData;
        this.showModal = true;
    },
    formatKey(key) {
        const map = {
            'patient_id': 'Patient Record',
            'doctor_id': 'Assigned Doctor',
            'nurse_id': 'Assigned Nurse',
            'consultation_start_time': 'Start Time',
            'consultation_end_time': 'End Time',
            'preferred_date': 'Preferred Date',
            'preferred_time': 'Preferred Time',
            'is_follow_up': 'Follow-Up Appointment',
            'vitals_id': 'Vitals Record',
            'diagnosis': 'Diagnosis',
            'prescription': 'Prescription',
            'severity': 'Triage Severity',
            'status': 'Status',
            'created_at': 'Created At',
            'updated_at': 'Updated At',
            'deleted_at': 'Deleted At'
        };
        if (map[key]) return map[key];
        return key.split('_').map(word => word.charAt(0).toUpperCase() + word.slice(1)).join(' ');
    },
    formatValue(val, key = '') {
        if (val === null || val === undefined || val === '') return '—';
        
        // Format duration/seconds to hour, minutes, seconds
        if (key && (key.toLowerCase().includes('duration') || key.toLowerCase().includes('seconds') || key.toLowerCase().includes('time_spent'))) {
            let num = parseFloat(val);
            if (!isNaN(num)) {
                num = Math.abs(num);
                const h = Math.floor(num / 3600);
                const m = Math.floor((num % 3600) / 60);
                const s = Math.floor(num % 60);
                
                let parts = [];
                if (h > 0) parts.push(`${h}h`);
                if (m > 0 || h > 0) parts.push(`${m}m`);
                parts.push(`${s}s`);
                
                return parts.join(' ');
            }
        }

        if (typeof val === 'boolean') return val ? 'Yes' : 'No';
        if (typeof val === 'number') return val;
        if (typeof val === 'string' && val.match(/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}/)) {
            return new Date(val).toLocaleString('en-US', { month: 'short', day: 'numeric', year: 'numeric', hour: 'numeric', minute: '2-digit', hour12: true });
        }
        if (typeof val === 'string' && val.match(/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/)) {
            return new Date(val.replace(' ', 'T')).toLocaleString('en-US', { month: 'short', day: 'numeric', year: 'numeric', hour: 'numeric', minute: '2-digit', hour12: true });
        }
        if (typeof val === 'string' && val.match(/^\d{4}-\d{2}-\d{2}$/)) {
            return new Date(val).toLocaleString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
        }
        if (typeof val === 'object') return JSON.stringify(val);
        if (typeof val === 'string' && !val.includes(' ')) return val.charAt(0).toUpperCase() + val.slice(1).toLowerCase();
        return val;
    },
    shouldShow(key) {
        const hiddenKeys = ['id', 'created_at', 'updated_at', 'deleted_at', 'remember_token', 'password', 'email_verified_at'];
        return !hiddenKeys.includes(key);
    }
}">
    <!-- Breadcrumb Navigation -->
    <nav class="flex items-center gap-2 text-xs font-medium text-slate-500 dark:text-slate-400 py-2 border-b border-slate-200/60 dark:border-slate-800" aria-label="Breadcrumb">
        <a href="{{ route('admin.dashboard') }}" class="hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors flex items-center gap-1.5 shrink-0">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
            <span>Home</span>
        </a>
        <span class="text-slate-300 dark:text-slate-700">/</span>
        <span class="text-slate-700 dark:text-slate-300 font-semibold">Security Audit Trail</span>
    </nav>

    <!-- Header Section (Content Management Style) -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 pb-4 border-b border-slate-200/80 dark:border-slate-800">
        <div>
            <h1 class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white tracking-tight flex items-center gap-2.5">
                <span class="w-9 h-9 sm:w-10 sm:h-10 rounded-2xl bg-emerald-100/80 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0 border border-emerald-500/20 shadow-2xs">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </span>
                <span>Security & Activity Audit Trail</span>
            </h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Immutable record of all municipal healthcare actions, clinical edits, patient access, and administrative changes.</p>
        </div>

        <div class="flex items-center gap-2.5">
            <span class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 text-slate-600 dark:text-slate-400 text-xs font-semibold shadow-2xs">
                <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                </svg>
                <span>Encrypted Audit Stream</span>
            </span>
        </div>
    </div>

    <!-- Filters Toolbar (Floating Container with Styled <option> Tags) -->
    <div class="bg-white/80 dark:bg-slate-900/80 backdrop-blur-md rounded-2xl border border-slate-200/80 dark:border-slate-800 p-4 sm:p-5 shadow-xs">
        <form id="filterForm" x-ref="filterForm" action="{{ route('admin.audit.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-12 gap-3.5 items-end">
            <!-- Search -->
            <div class="md:col-span-6">
                <label class="block text-[11px] font-extrabold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1.5">Search Audit Logs</label>
                <div class="relative group">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Search action description, personnel name, or entity ID..." @input.debounce.300ms="submitForm"
                        class="h-11 pl-10 pr-4 block w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50/70 dark:bg-slate-800/60 text-slate-900 dark:text-white text-sm font-semibold focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 shadow-2xs transition-all placeholder:text-slate-400">
                </div>
            </div>

            <!-- Action Category Custom Listbox -->
            <div class="md:col-span-3">
                <label class="block text-[11px] font-extrabold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1.5">Action Category</label>
                <x-select 
                    name="type" 
                    :options="[
                        'all' => 'All Audit Events',
                        'Created' => 'Creation Events',
                        'Updated' => 'Update & Modifications',
                        'Deleted' => 'Deletion & Archive',
                        'Accessed' => 'Record Inspection'
                    ]" 
                    :value="request('type', 'all')"
                    @change="submitForm"
                    class="!h-11 !py-0 flex items-center font-semibold text-xs sm:text-sm bg-slate-50/70 dark:bg-slate-800/60"
                />
            </div>

            <!-- CSV Export Button -->
            <div class="md:col-span-3">
                <button type="button"
                    class="w-full h-11 flex items-center justify-center gap-2 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white font-bold px-4 rounded-xl transition-all shadow-md shadow-emerald-600/20 active:scale-95 text-xs cursor-pointer">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <span>Export Audit Trail (CSV)</span>
                </button>
            </div>
            <input type="hidden" name="per_page" x-ref="perPageInput" value="{{ request('per_page', 10) }}">
        </form>
    </div>

    <!-- Log Table Container -->
    <div class="bg-white dark:bg-slate-900 rounded-3xl shadow-xs border border-slate-200/90 dark:border-slate-800 overflow-hidden relative min-h-[400px]">
        <!-- Loading Overlay -->
        <div x-show="loading" class="absolute inset-0 bg-white/70 dark:bg-slate-900/70 z-50 flex flex-col items-center justify-center backdrop-blur-[1px] transition-opacity duration-300" style="display: none;">
            <div class="w-8 h-8 rounded-full border-4 border-emerald-200 border-t-emerald-600 animate-spin mb-2"></div>
            <span class="text-xs font-bold text-emerald-800 dark:text-emerald-400">Filtering audit log...</span>
        </div>

        <div id="audit-table-contents" data-dynamic-block="true">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50/90 dark:bg-slate-950/80 border-b border-slate-200/80 dark:border-slate-800 text-[11px] uppercase tracking-wider text-slate-500 dark:text-slate-400 font-extrabold">
                            <th class="px-6 py-4">Timestamp</th>
                            <th class="px-6 py-4">Authenticated User</th>
                            <th class="px-6 py-4">Action Performed</th>
                            <th class="px-6 py-4">Target Entity / Resource</th>
                            <th class="px-6 py-4 text-right">Audit Diff</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800 text-sm">
                        @forelse($logs as $log)
                            <tr class="hover:bg-emerald-50/30 dark:hover:bg-slate-800/40 transition-colors">
                                <td class="px-6 py-4">
                                    <p class="font-bold text-slate-900 dark:text-white text-xs sm:text-sm">
                                        {{ $log->created_at->format('M d, Y') }}
                                    </p>
                                    <p class="text-[11px] font-mono text-slate-500 dark:text-slate-400 mt-0.5">
                                        {{ $log->created_at->format('h:i:s A') }}
                                    </p>
                                </td>
                                <td class="px-6 py-4">
                                    @if($log->user)
                                        <div class="flex items-center gap-3">
                                            <div class="w-9 h-9 rounded-2xl bg-emerald-100/80 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 font-black flex items-center justify-center border border-emerald-500/20 shadow-2xs shrink-0 text-xs">
                                                {{ strtoupper(substr($log->user->name, 0, 1)) }}
                                            </div>
                                            <div>
                                                <p class="font-bold text-slate-900 dark:text-white text-xs sm:text-sm">{{ $log->user->name }}</p>
                                                <p class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider mt-0.5">
                                                    {{ str_replace('_', ' ', $log->user->role) }}
                                                </p>
                                            </div>
                                        </div>
                                    @else
                                        <span class="text-xs text-slate-400 italic">System Automation</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    @php
                                        $color = 'slate';
                                        if (str_contains($log->action, 'Created'))
                                            $color = 'emerald';
                                        elseif (str_contains($log->action, 'Updated'))
                                            $color = 'blue';
                                        elseif (str_contains($log->action, 'Deleted'))
                                            $color = 'rose';
                                        elseif (str_contains($log->action, 'Accessed') || str_contains($log->action, 'Viewed'))
                                            $color = 'indigo';
                                        elseif (str_contains($log->action, 'Completed'))
                                            $color = 'teal';
                                        elseif (str_contains($log->action, 'Queued'))
                                            $color = 'amber';
                                    @endphp
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold uppercase tracking-wider bg-{{ $color }}-50 text-{{ $color }}-700 border border-{{ $color }}-200 dark:bg-{{ $color }}-950/50 dark:text-{{ $color }}-300 dark:border-{{ $color }}-800/60 shadow-2xs">
                                        {{ $log->action }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    @if($log->model_id)
                                        <span class="text-xs font-mono font-bold text-slate-700 dark:text-slate-300 bg-slate-100 dark:bg-slate-800 px-2 py-0.5 rounded-md border border-slate-200 dark:border-slate-700">
                                            #{{ $log->model_id }}
                                        </span>
                                        <p class="text-[11px] text-slate-400 dark:text-slate-500 font-medium mt-1">
                                            {{ class_basename($log->model_type) }}
                                        </p>
                                    @else
                                        <span class="text-xs text-slate-400 dark:text-slate-600">—</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right">
                                    @if($log->changes && count($log->changes) > 0)
                                        <button type="button" @click="openModal(@js($log->changes))"
                                            class="h-8 px-3 rounded-xl inline-flex items-center gap-1.5 text-xs font-bold text-emerald-700 dark:text-emerald-300 bg-emerald-50 dark:bg-emerald-950/40 hover:bg-emerald-100 dark:hover:bg-emerald-900/50 border border-emerald-200 dark:border-emerald-800/60 shadow-2xs transition-all cursor-pointer active:scale-95">
                                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                            <span>View Diff</span>
                                        </button>
                                    @else
                                        <span class="text-xs text-slate-400 dark:text-slate-600 italic">No delta</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="p-16 text-center text-slate-500 dark:text-slate-400">
                                    <div class="w-14 h-14 rounded-2xl bg-slate-100 dark:bg-slate-800 text-slate-400 dark:text-slate-500 mx-auto flex items-center justify-center mb-3 shadow-xs">
                                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                    </div>
                                    <p class="text-base font-bold text-slate-900 dark:text-white">No audit records found</p>
                                    <p class="text-xs text-slate-400 mt-1">Try adjusting your filters or search criteria.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination Controls with Styled <option> Tags -->
            @if($logs->hasPages() || $logs->total() > 10)
                <div class="px-6 py-4 border-t border-slate-100 dark:border-slate-800/80 bg-slate-50/70 dark:bg-slate-900/60 flex flex-col sm:flex-row items-center justify-between gap-4">
                    <div class="flex items-center gap-3">
                        <label class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Items per page</label>
                        <x-select 
                            :options="[
                                '10' => '10 per page',
                                '20' => '20 per page',
                                '30' => '30 per page',
                                '50' => '50 per page'
                            ]" 
                            :value="request('per_page', 10)"
                            size="sm"
                            containerClass="w-36"
                            :dropUp="true"
                            @change="$refs.perPageInput.value = $event.detail; submitForm()"
                        />
                    </div>
                    
                    <div class="w-full sm:w-auto">
                        @if($logs->hasPages())
                            {{ $logs->appends(request()->query())->links('vendor.pagination.shadcn') }}
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- JSON Changes Diff Modal (Content Management Style) -->
    <div x-show="showModal" style="display: none;" class="fixed inset-0 z-[999] overflow-y-auto"
        aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen px-4 py-8">
            <div x-show="showModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                class="fixed inset-0 bg-slate-950/70 backdrop-blur-sm transition-opacity" @click="showModal = false"
                aria-hidden="true"></div>

            <div x-show="showModal" x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                class="inline-block bg-white dark:bg-slate-900 rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 max-w-2xl w-full border border-slate-200/90 dark:border-slate-800 relative z-10">

                <div class="px-6 py-5 border-b border-slate-100 dark:border-slate-800 flex justify-between items-center bg-slate-50/70 dark:bg-slate-900/60">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-emerald-100/80 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center border border-emerald-500/20 shadow-2xs">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-slate-900 dark:text-white" id="modal-title">Detailed Audit Comparison</h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400">Granular property delta recorded during this action</p>
                        </div>
                    </div>
                    <button type="button" @click="showModal = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 p-1.5 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <div class="px-6 py-6 max-h-[60vh] overflow-y-auto">
                    <template x-if="selectedLog && Object.keys(selectedLog).length > 0">
                        <div>
                            <template x-if="selectedLog.old !== undefined || selectedLog.new !== undefined">
                                <div class="overflow-hidden bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/90 dark:border-slate-800 shadow-2xs">
                                    <table class="w-full text-left border-collapse text-xs text-slate-700 dark:text-slate-300">
                                        <thead>
                                            <tr class="border-b border-slate-200 dark:border-slate-800 bg-slate-50/90 dark:bg-slate-950/80">
                                                <th class="px-4 py-3 font-extrabold text-slate-500 dark:text-slate-400 uppercase tracking-wider text-[10px]">Field</th>
                                                <th class="px-4 py-3 font-extrabold text-slate-500 dark:text-slate-400 uppercase tracking-wider text-[10px]">Previous Value</th>
                                                <th class="px-4 py-3 font-extrabold text-slate-500 dark:text-slate-400 uppercase tracking-wider text-[10px]">Updated Value</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                                            <template x-for="key in [...new Set([...Object.keys(selectedLog.old || {}), ...Object.keys(selectedLog.new || {})])]" :key="key">
                                                <template x-if="shouldShow(key)">
                                                    <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/40 transition-colors">
                                                        <td class="px-4 py-3 font-mono text-xs text-slate-900 dark:text-white font-bold" x-text="formatKey(key)"></td>
                                                        <td class="px-4 py-3 text-xs">
                                                            <template x-if="selectedLog.old && selectedLog.old[key] !== undefined">
                                                                <span :class="{'text-rose-700 bg-rose-50 dark:text-rose-400 dark:bg-rose-950/40 line-through px-1.5 py-0.5 rounded': selectedLog.new && JSON.stringify(selectedLog.old[key]) !== JSON.stringify(selectedLog.new[key])}" x-text="formatValue(selectedLog.old[key], key)"></span>
                                                            </template>
                                                            <template x-if="!selectedLog.old || selectedLog.old[key] === undefined">
                                                                <span class="text-slate-400">—</span>
                                                            </template>
                                                        </td>
                                                        <td class="px-4 py-3 text-xs">
                                                            <template x-if="selectedLog.new && selectedLog.new[key] !== undefined">
                                                                <span :class="{'text-emerald-700 bg-emerald-50 dark:text-emerald-400 dark:bg-emerald-950/40 font-bold px-1.5 py-0.5 rounded': selectedLog.old && JSON.stringify(selectedLog.old[key]) !== JSON.stringify(selectedLog.new[key])}" x-text="formatValue(selectedLog.new[key], key)"></span>
                                                            </template>
                                                            <template x-if="!selectedLog.new || selectedLog.new[key] === undefined">
                                                                <span class="text-slate-400">—</span>
                                                            </template>
                                                        </td>
                                                    </tr>
                                                </template>
                                            </template>
                                        </tbody>
                                    </table>
                                </div>
                            </template>
                            
                            <!-- Fallback for standard key-value logs -->
                            <template x-if="selectedLog.old === undefined && selectedLog.new === undefined">
                                <div class="overflow-hidden bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/90 dark:border-slate-800 shadow-2xs">
                                    <table class="w-full text-left border-collapse text-xs text-slate-700 dark:text-slate-300">
                                        <thead>
                                            <tr class="border-b border-slate-200 dark:border-slate-800 bg-slate-50/90 dark:bg-slate-950/80">
                                                <th class="px-4 py-3 font-extrabold text-slate-500 dark:text-slate-400 uppercase tracking-wider text-[10px]">Property</th>
                                                <th class="px-4 py-3 font-extrabold text-slate-500 dark:text-slate-400 uppercase tracking-wider text-[10px]">Logged Detail</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                                            <template x-for="[key, value] in Object.entries(selectedLog)" :key="key">
                                                <template x-if="shouldShow(key)">
                                                    <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/40 transition-colors">
                                                        <td class="px-4 py-3 font-mono text-xs text-slate-900 dark:text-white font-bold" x-text="formatKey(key)"></td>
                                                        <td class="px-4 py-3 text-xs">
                                                            <template x-if="typeof value !== 'object' || value === null">
                                                                <span class="font-medium text-slate-900 dark:text-white" x-text="formatValue(value, key)"></span>
                                                            </template>
                                                            <template x-if="typeof value === 'object' && value !== null">
                                                                <div class="space-y-1 bg-slate-50 dark:bg-slate-800/40 p-2 rounded-xl border border-slate-200/80 dark:border-slate-800">
                                                                    <template x-for="[subKey, subVal] in Object.entries(value)" :key="subKey">
                                                                        <template x-if="shouldShow(subKey)">
                                                                            <div class="text-xs flex gap-2">
                                                                                <span class="text-slate-400 font-medium" x-text="formatKey(subKey) + ': '"></span>
                                                                                <span class="text-slate-900 dark:text-white font-medium" x-text="formatValue(subVal, subKey)"></span>
                                                                            </div>
                                                                        </template>
                                                                    </template>
                                                                </div>
                                                            </template>
                                                        </td>
                                                    </tr>
                                                </template>
                                            </template>
                                        </tbody>
                                    </table>
                                </div>
                            </template>
                        </div>
                    </template>
                    <template x-if="!selectedLog || Object.keys(selectedLog).length === 0">
                        <div class="text-center py-8">
                            <p class="text-slate-500 dark:text-slate-400 text-xs font-medium">No detailed properties available for this record.</p>
                        </div>
                    </template>
                </div>

                <div class="px-6 py-4 bg-slate-50/70 dark:bg-slate-900/60 border-t border-slate-100 dark:border-slate-800 flex justify-end">
                    <button type="button" @click="showModal = false"
                        class="px-5 py-2.5 rounded-xl text-xs font-bold text-slate-700 dark:text-slate-300 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 transition-colors cursor-pointer">
                        Dismiss
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection