@extends('layouts.admin')

@section('header', 'System Audit Trail')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8" x-data="{ 
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
                    num = Math.abs(num); // Ensure positive
                    const h = Math.floor(num / 3600);
                    const m = Math.floor((num % 3600) / 60);
                    const s = Math.floor(num % 60);
                    
                    let parts = [];
                    if (h > 0) parts.push(`${h}h`);
                    if (m > 0 || h > 0) parts.push(`${m}m`); // Include minutes if there are hours
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
            // specific fallback for strings that are entirely uppercase/lowercase and single words
            if (typeof val === 'string' && !val.includes(' ')) return val.charAt(0).toUpperCase() + val.slice(1).toLowerCase();
            return val;
        },
        shouldShow(key) {
            const hiddenKeys = ['id', 'created_at', 'updated_at', 'deleted_at', 'remember_token', 'password', 'email_verified_at'];
            return !hiddenKeys.includes(key);
        }
    }">
        <div class="mb-8">
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Security & Activity Logs</h2>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Accountability trail for all sensitive medical and
                system actions.</p>
        </div>

        <!-- Filters -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-800 p-6 mb-8">
            <form id="filterForm" x-ref="filterForm" action="{{ route('admin.audit.index') }}" method="GET"
                class="grid grid-cols-1 md:grid-cols-12 gap-3.5 items-end">
                <div class="md:col-span-6">
                    <label
                        class="block text-[11px] font-extrabold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1.5">Search
                        Logs</label>
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400 group-focus-within:text-teal-500 transition-colors">
                            <svg class="h-4.5 w-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                        <input type="text" name="search" value="{{ request('search') }}"
                            placeholder="Search action, user, or resource ID..." @input.debounce.300ms="submitForm"
                            class="h-11 pl-10 pr-4 block w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-white shadow-2xs focus:outline-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 text-sm font-medium transition-all placeholder:text-slate-400">
                    </div>
                </div>
                <div class="md:col-span-3">
                    <label
                        class="block text-[11px] font-extrabold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1.5">Action
                        Category</label>
                    <x-select 
                        name="type" 
                        :options="[
                            'all' => 'All Actions',
                            'Created' => 'Creation',
                            'Updated' => 'Updates',
                            'Deleted' => 'Deletions',
                            'Accessed' => 'History Access'
                        ]" 
                        :value="request('type', 'all')"
                        @change="submitForm"
                        class="!h-11 !py-0 flex items-center font-semibold"
                    />
                </div>
                <div class="md:col-span-3">
                    <button type="button"
                        class="w-full h-11 flex items-center justify-center gap-2 bg-slate-900 dark:bg-teal-600 hover:bg-slate-800 dark:hover:bg-teal-700 text-white font-bold px-4 rounded-xl transition-all shadow-sm active:scale-95 text-sm cursor-pointer">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <span>Export as CSV</span>
                    </button>
                </div>
            </form>
        </div>

        <!-- Log Table Container -->
        <div
            class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-100 dark:border-gray-700 overflow-hidden relative min-h-[400px]">

            <!-- Loading Overlay -->
            <div x-show="loading"
                class="absolute inset-0 bg-white/70 dark:bg-gray-800/70 z-50 flex flex-col items-center justify-center backdrop-blur-[1px] transition-opacity duration-300"
                style="display: none;">
                <div class="w-8 h-8 rounded-full border-4 border-teal-200 border-t-teal-600 animate-spin mb-2"></div>
                <span class="text-sm font-bold text-teal-800 dark:text-teal-400">Updating...</span>
            </div>

            <div id="audit-table-contents" data-dynamic-block="true">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50 dark:bg-gray-900/80 border-b border-gray-200 dark:border-gray-700">
                                <th
                                    class="px-6 py-4 text-[11px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-widest">
                                    Timestamp</th>
                                <th
                                    class="px-6 py-4 text-[11px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-widest">
                                    User</th>
                                <th
                                    class="px-6 py-4 text-[11px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-widest">
                                    Action</th>
                                <th
                                    class="px-6 py-4 text-[11px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-widest">
                                    Patient / Resource</th>
                                <th
                                    class="px-6 py-4 text-[11px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-widest text-right">
                                    Details</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-800 text-sm">
                            @forelse($logs as $log)
                                <tr class="hover:bg-slate-50 dark:hover:bg-slate-900/50 transition-all">
                                    <td class="px-6 py-4">
                                        <p class="font-bold text-gray-900 dark:text-white">
                                            {{ $log->created_at->format('M d, Y') }}</p>
                                        <p class="text-xs font-mono text-gray-500 dark:text-gray-400 mt-0.5">
                                            {{ $log->created_at->format('h:i:s A') }}</p>
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($log->user)
                                            <div class="flex items-center gap-3">
                                                <div
                                                    class="w-8 h-8 rounded-full bg-teal-100 dark:bg-teal-900/40 flex items-center justify-center border border-teal-200 dark:border-teal-800">
                                                    <span
                                                        class="text-xs font-bold text-teal-700 dark:text-teal-300">{{ strtoupper(substr($log->user->name, 0, 1)) }}</span>
                                                </div>
                                                <div>
                                                    <p class="font-bold text-gray-900 dark:text-white">{{ $log->user->name }}</p>
                                                    <p
                                                        class="text-[10px] text-gray-500 dark:text-gray-400 capitalize uppercase tracking-wide mt-0.5">
                                                        {{ str_replace('_', ' ', $log->user->role) }}</p>
                                                </div>
                                            </div>
                                        @else
                                            <span class="text-xs text-gray-400 italic">System / Anonymous</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        @php
                                            $color = 'gray';
                                            if (str_contains($log->action, 'Created'))
                                                $color = 'emerald';
                                            elseif (str_contains($log->action, 'Updated'))
                                                $color = 'blue';
                                            elseif (str_contains($log->action, 'Deleted'))
                                                $color = 'red';
                                            elseif (str_contains($log->action, 'Accessed') || str_contains($log->action, 'Viewed'))
                                                $color = 'indigo';
                                            elseif (str_contains($log->action, 'Completed'))
                                                $color = 'green';
                                            elseif (str_contains($log->action, 'Queued'))
                                                $color = 'orange';
                                        @endphp
                                        <span
                                            class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider bg-{{ $color }}-50 text-{{ $color }}-700 border border-{{ $color }}-200 dark:bg-{{ $color }}-900/30 dark:text-{{ $color }}-400 dark:border-{{ $color }}-800">
                                            {{ $log->action }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($log->model_id)
                                            <span
                                                class="text-sm font-mono font-bold text-slate-700 dark:text-slate-300">{{ $log->model_id }}</span>
                                            <p class="text-[10px] text-gray-500 dark:text-gray-400 mt-0.5">
                                                {{ class_basename($log->model_type) }}</p>
                                        @else
                                            <span class="text-xs text-gray-400 dark:text-gray-600">—</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        @if($log->changes && count($log->changes) > 0)
                                            <button type="button" @click="openModal(@js($log->changes))"
                                                class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-bold text-teal-700 dark:text-teal-400 bg-teal-50 dark:bg-teal-900/30 hover:bg-teal-100 dark:hover:bg-teal-900/50 rounded-lg transition-colors border border-teal-200 dark:border-teal-800">
                                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                                View Changes
                                            </button>
                                        @else
                                            <span class="text-xs text-gray-400 dark:text-gray-600 italic">No detailed changes</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
                                        <svg class="w-12 h-12 text-gray-300 dark:text-gray-600 mx-auto mb-3" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                            </path>
                                        </svg>
                                        <p class="text-base font-bold">No logs found matching your criteria.</p>
                                        <p class="text-sm mt-1">Try adjusting your filters or search query.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($logs->hasPages() || $logs->total() > 10)
                    <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50 flex flex-col sm:flex-row items-center justify-between gap-4">
                        <div class="flex items-center gap-3">
                            <label class="text-sm font-medium text-gray-500 dark:text-gray-400">Items per page</label>
                            <select name="per_page" form="filterForm" @change="submitForm"
                                class="rounded-lg border-gray-200 dark:border-gray-700 dark:bg-gray-800 dark:text-white focus:ring-teal-500 focus:border-teal-500 py-1.5 px-3 text-sm shadow-sm transition-all cursor-pointer">
                                <option value="10" {{ request('per_page', 10) == 10 ? 'selected' : '' }}>10</option>
                                <option value="20" {{ request('per_page') == 20 ? 'selected' : '' }}>20</option>
                                <option value="30" {{ request('per_page') == 30 ? 'selected' : '' }}>30</option>
                                <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                            </select>
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

        <!-- JSON Modal -->
        <div x-show="showModal" style="display: none;" class="fixed inset-0 z-[100] overflow-y-auto"
            aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <!-- Background overlay -->
                <div x-show="showModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200"
                    x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                    class="fixed inset-0 bg-slate-900/75 backdrop-blur-sm transition-opacity" @click="showModal = false"
                    aria-hidden="true"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <!-- Modal panel -->
                <div x-show="showModal" x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave="ease-in duration-200"
                    x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl w-full border border-gray-200 dark:border-gray-700">

                    <div
                        class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 flex justify-between items-center">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2"
                            id="modal-title">
                            <svg class="w-5 h-5 text-teal-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4" />
                            </svg>
                            Detailed Changes
                        </h3>
                        <button type="button" @click="showModal = false"
                            class="text-gray-400 hover:text-gray-500 dark:hover:text-gray-300 focus:outline-none transition-colors">
                            <span class="sr-only">Close</span>
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <div class="px-6 py-6 bg-gray-50 dark:bg-gray-900 max-h-[60vh] overflow-y-auto custom-scrollbar">
                        <template x-if="selectedLog && Object.keys(selectedLog).length > 0">
                            <div>
                                <!-- If we have old and new properties (Update action typically) -->
                                <template x-if="selectedLog.old !== undefined || selectedLog.new !== undefined">
                                    <div class="overflow-x-auto bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm">
                                        <table class="w-full text-left border-collapse text-sm text-gray-700 dark:text-gray-300">
                                            <thead>
                                                <tr class="border-b border-gray-200 dark:border-gray-700 bg-gray-100 dark:bg-gray-800/80">
                                                    <th class="px-4 py-3 font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider text-[10px]">Field</th>
                                                    <th class="px-4 py-3 font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider text-[10px]">Previous Value</th>
                                                    <th class="px-4 py-3 font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider text-[10px]">New Value</th>
                                                </tr>
                                            </thead>
                                            <tbody class="divide-y divide-gray-100 dark:divide-gray-700/50">
                                                <!-- Get all unique keys from both old and new -->
                                                <template x-for="key in [...new Set([...Object.keys(selectedLog.old || {}), ...Object.keys(selectedLog.new || {})])]" :key="key">
                                                    <template x-if="shouldShow(key)">
                                                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors">
                                                            <td class="px-4 py-3 font-mono text-xs text-gray-900 dark:text-white font-bold" x-text="formatKey(key)"></td>
                                                            <td class="px-4 py-3 text-sm">
                                                                <template x-if="selectedLog.old && selectedLog.old[key] !== undefined">
                                                                    <span :class="{'text-red-700 bg-red-50 dark:text-red-400 dark:bg-red-900/20 line-through px-1.5 py-0.5 rounded': selectedLog.new && JSON.stringify(selectedLog.old[key]) !== JSON.stringify(selectedLog.new[key])}" x-text="formatValue(selectedLog.old[key], key)"></span>
                                                                </template>
                                                                <template x-if="!selectedLog.old || selectedLog.old[key] === undefined">
                                                                    <span class="text-gray-400 dark:text-gray-600">—</span>
                                                                </template>
                                                            </td>
                                                            <td class="px-4 py-3 text-sm">
                                                                <template x-if="selectedLog.new && selectedLog.new[key] !== undefined">
                                                                    <span :class="{'text-green-700 bg-green-50 dark:text-green-400 dark:bg-green-900/20 font-bold px-1.5 py-0.5 rounded': selectedLog.old && JSON.stringify(selectedLog.old[key]) !== JSON.stringify(selectedLog.new[key])}" x-text="formatValue(selectedLog.new[key], key)"></span>
                                                                </template>
                                                                <template x-if="!selectedLog.new || selectedLog.new[key] === undefined">
                                                                    <span class="text-gray-400 dark:text-gray-600">—</span>
                                                                </template>
                                                            </td>
                                                        </tr>
                                                    </template>
                                                </template>
                                            </tbody>
                                        </table>
                                    </div>
                                </template>
                                
                                <!-- Fallback for standard key-value pairs (not old/new structure) -->
                                <template x-if="selectedLog.old === undefined && selectedLog.new === undefined">
                                    <div class="overflow-x-auto bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm">
                                        <table class="w-full text-left border-collapse text-sm text-gray-700 dark:text-gray-300">
                                            <thead>
                                                <tr class="border-b border-gray-200 dark:border-gray-700 bg-gray-100 dark:bg-gray-800/80">
                                                    <th class="px-4 py-3 font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider text-[10px]">Detail</th>
                                                    <th class="px-4 py-3 font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider text-[10px]">Value</th>
                                                </tr>
                                            </thead>
                                            <tbody class="divide-y divide-gray-100 dark:divide-gray-700/50">
                                                <template x-for="[key, value] in Object.entries(selectedLog)" :key="key">
                                                    <template x-if="shouldShow(key)">
                                                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors">
                                                            <td class="px-4 py-3 font-mono text-xs text-gray-900 dark:text-white font-bold" x-text="formatKey(key)"></td>
                                                            <td class="px-4 py-3 text-sm">
                                                                <template x-if="typeof value !== 'object' || value === null">
                                                                    <span class="font-medium text-gray-900 dark:text-gray-100" x-text="formatValue(value, key)"></span>
                                                                </template>
                                                                <template x-if="typeof value === 'object' && value !== null">
                                                                    <div class="space-y-1 bg-gray-50 dark:bg-gray-900/50 p-2 rounded-lg border border-gray-100 dark:border-gray-700/50">
                                                                        <template x-for="[subKey, subVal] in Object.entries(value)" :key="subKey">
                                                                            <template x-if="shouldShow(subKey)">
                                                                                <div class="text-xs flex gap-2">
                                                                                    <span class="text-gray-500 dark:text-gray-400 font-medium" x-text="formatKey(subKey) + ': '"></span>
                                                                                    <span class="text-gray-900 dark:text-gray-100 font-medium" x-text="formatValue(subVal, subKey)"></span>
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
                                <p class="text-gray-500 dark:text-gray-400 text-sm">No detailed properties available.</p>
                            </div>
                        </template>
                    </div>

                    <div
                        class="px-6 py-4 bg-gray-50 dark:bg-gray-900 border-t border-gray-100 dark:border-gray-700 sm:flex sm:flex-row-reverse">
                        <button type="button" @click="showModal = false"
                            class="w-full inline-flex justify-center rounded-xl border border-transparent shadow-sm px-4 py-2 bg-slate-800 text-base font-bold text-white hover:bg-slate-900 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm transition-colors">
                            Close
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection