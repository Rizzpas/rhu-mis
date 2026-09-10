@extends('layouts.admin')

@section('header')
<div class="flex justify-between items-center">
    <div>
        <h2 class="text-2xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-red-600 to-red-800">
            {{ __('Data Retention Management') }}
        </h2>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Manage inactive patient records that are scheduled for permanent deletion based on the 1-year retention policy.</p>
    </div>
</div>
@endsection

@section('content')
<div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-red-100 overflow-hidden mb-6" x-data="{
        selectedIds: [],
        selectAll: false,
        showBulkExtendModal: false,
        showBulkDeleteModal: false,
        toggleAll() {
            if (this.selectAll) {
                this.selectedIds = [...document.querySelectorAll('.rowCheckbox')].map(cb => cb.value);
            } else {
                this.selectedIds = [];
            }
        }
    }">
    <div class="px-6 py-5 border-b border-rose-100 dark:border-rose-950/60 bg-rose-50/60 dark:bg-rose-950/20 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h3 class="text-lg font-bold text-rose-900 dark:text-rose-400">Records Pending Deletion</h3>
            <p class="text-xs text-rose-700 dark:text-rose-300/80 mt-0.5">Patient records that have reached their data expiration benchmark. Extend retention or permanently delete.</p>
        </div>
        <div class="flex items-center gap-3">
            <div x-show="selectedIds.length > 0" x-cloak class="flex items-center gap-2">
                <button @click="showBulkExtendModal = true" class="h-11 px-4 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-sm shadow-md shadow-blue-600/20 transition-all flex items-center gap-2 active:scale-95 cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <span>Extend (<span x-text="selectedIds.length"></span>)</span>
                </button>
                @can('force-delete')
                <button @click="showBulkDeleteModal = true" class="h-11 px-4 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-bold text-sm shadow-md shadow-rose-600/20 transition-all flex items-center gap-2 active:scale-95 cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                    <span>Delete (<span x-text="selectedIds.length"></span>)</span>
                </button>
                @endcan
            </div>
            <span class="bg-rose-100 dark:bg-rose-900/50 text-rose-800 dark:text-rose-200 border border-rose-200 dark:border-rose-800 font-bold px-3.5 py-1.5 rounded-full text-xs shadow-2xs">
                {{ $inactivePatients->total() }} Records
            </span>
        </div>
    </div>
    
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50/90 dark:bg-slate-900/90 border-b border-slate-200 dark:border-slate-800 text-[11px] uppercase text-slate-500 dark:text-slate-400 font-extrabold tracking-wider">
                    <th class="px-6 py-4 w-12">
                        <input type="checkbox" x-model="selectAll" @change="toggleAll" class="rounded border-slate-300 dark:border-slate-700 text-teal-600 shadow-sm focus:border-teal-300 focus:ring focus:ring-teal-200 focus:ring-opacity-50">
                    </th>
                    <th class="px-6 py-4">Patient Name</th>
                    <th class="px-6 py-4">Classification</th>
                    <th class="px-6 py-4">Expiration Date</th>
                    <th class="px-6 py-4 text-center">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-800 text-sm">
                @forelse($inactivePatients as $patient)
                    <tr class="hover:bg-rose-50/40 dark:hover:bg-rose-950/20 transition-colors">
                        <td class="px-6 py-4">
                            <input type="checkbox" value="{{ $patient->patient_id }}" x-model="selectedIds" @change="if(!selectedIds.includes('{{ $patient->patient_id }}')) selectAll = false" class="rowCheckbox rounded border-slate-300 dark:border-slate-700 text-teal-600 shadow-sm focus:border-teal-300 focus:ring focus:ring-teal-200 focus:ring-opacity-50">
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                <div class="bg-rose-100 dark:bg-rose-950/50 text-rose-600 dark:text-rose-400 border border-rose-200 dark:border-rose-800 rounded-full w-10 h-10 flex items-center justify-center font-bold mr-3 shrink-0 shadow-2xs">
                                    {{ substr($patient->first_name, 0, 1) }}{{ substr($patient->last_name, 0, 1) }}
                                </div>
                                <div>
                                    <p class="font-bold text-slate-900 dark:text-white text-sm">{{ $patient->full_name }}</p>
                                    <p class="text-xs text-slate-500 dark:text-slate-400">ID: {{ $patient->patient_id ?? 'No ID' }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-2.5 py-1 text-xs font-bold rounded-full border shadow-2xs
                                @if($patient->classification == 'Pediatric') bg-pink-50 text-pink-700 border-pink-200 dark:bg-pink-950/50 dark:text-pink-300 dark:border-pink-800/60
                                @elseif($patient->classification == 'Senior Citizen') bg-purple-50 text-purple-700 border-purple-200 dark:bg-purple-950/50 dark:text-purple-300 dark:border-purple-800/60
                                @else bg-slate-50 text-slate-700 border-slate-200 dark:bg-slate-800 dark:text-slate-300 dark:border-slate-700 @endif">
                                {{ $patient->classification }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <p class="font-bold text-rose-600 dark:text-rose-400 text-xs">{{ $patient->expires_at->format('M d, Y') }}</p>
                            <p class="text-[11px] text-slate-400">{{ $patient->expires_at->diffForHumans() }}</p>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center justify-center gap-2">
                                <!-- Extend Retention Form -->
                                <button type="button" 
                                    @click="$dispatch('open-confirmation', {
                                        action: '{{ route('admin.retention.extend', $patient) }}',
                                        method: 'POST',
                                        title: 'Extend Retention?',
                                        message: 'Are you sure you want to extend retention for this patient by 1 year?',
                                        confirmText: 'Yes, Extend',
                                        type: 'info'
                                    })"
                                    class="h-8 px-3 rounded-lg inline-flex items-center gap-1 text-xs font-bold text-blue-700 dark:text-blue-300 bg-blue-50 dark:bg-blue-950/40 hover:bg-blue-100 dark:hover:bg-blue-900/50 border border-blue-200 dark:border-blue-800/60 shadow-2xs transition-all cursor-pointer">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    <span>Extend 1yr</span>
                                </button>

                                <!-- Permanent Delete Form -->
                                @can('force-delete')
                                <button type="button" 
                                    @click="$dispatch('open-confirmation', {
                                        action: '{{ route('admin.retention.delete', $patient) }}',
                                        method: 'DELETE',
                                        title: 'Permanently Delete?',
                                        message: 'CRITICAL WARNING: This will permanently delete the patient record and cannot be undone. Are you absolutely sure?',
                                        confirmText: 'Permanently Delete',
                                        type: 'danger'
                                    })"
                                    class="h-8 px-3 rounded-lg inline-flex items-center gap-1 text-xs font-bold text-rose-700 dark:text-rose-300 bg-rose-50 dark:bg-rose-950/40 hover:bg-rose-100 dark:hover:bg-rose-900/50 border border-rose-200 dark:border-rose-800/60 shadow-2xs transition-all cursor-pointer">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    <span>Delete</span>
                                </button>
                                @endcan
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="p-8 text-center text-gray-500 dark:text-gray-400">
                            <div class="flex flex-col items-center justify-center">
                                <div class="p-3 bg-green-50 dark:bg-green-900/30 text-green-500 rounded-full mb-3">
                                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                </div>
                                <p class="text-lg font-bold text-gray-800 dark:text-white">All Patient Records Compliant</p>
                                <p class="text-sm mt-1">There are no records requiring deletion at this time.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if($inactivePatients->hasPages())
        <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-900">
            {{ $inactivePatients->links() }}
        </div>
    @endif

    <!-- Bulk Extend Modal -->
    <template x-teleport="body">
        <div x-show="showBulkExtendModal" x-cloak style="display: none;" class="fixed inset-0 z-[100] overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-slate-950/80 backdrop-blur-sm transition-opacity" @click="showBulkExtendModal = false"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                <div class="inline-block align-bottom bg-white dark:bg-slate-900 rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full border border-slate-200 dark:border-slate-800">
                    <div class="bg-white dark:bg-slate-900 px-6 pt-6 pb-6">
                        <div class="sm:flex sm:items-start">
                            <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-2xl bg-teal-100 dark:bg-teal-900/30 text-teal-600 dark:text-teal-400 sm:mx-0 sm:h-10 sm:w-10">
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                                <h3 class="text-xl font-bold text-slate-900 dark:text-white" id="modal-title">Bulk Extend Retention</h3>
                                <div class="mt-2">
                                    <p class="text-sm text-slate-500 dark:text-slate-400">Are you sure you want to extend data retention for <span class="font-black text-teal-600 dark:text-teal-400" x-text="selectedIds.length"></span> selected patient records? This will extend their expiration date by another 10 years.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-slate-50 dark:bg-slate-950/50 px-6 py-4 flex flex-row-reverse gap-3">
                        <form method="POST" action="{{ route('admin.retention.bulk-extend') }}" @submit="
                            $el.querySelectorAll('input[name=\'ids[]\']').forEach(e => e.remove());
                            selectedIds.forEach(id => {
                                const inp = document.createElement('input');
                                inp.type = 'hidden';
                                inp.name = 'ids[]';
                                inp.value = id;
                                $el.appendChild(inp);
                            });
                        ">
                            @csrf
                            <template x-for="id in selectedIds" :key="id">
                                <input type="hidden" name="ids[]" :value="id">
                            </template>
                            <button type="submit" class="w-full inline-flex justify-center rounded-xl border border-transparent shadow-lg px-6 py-2 bg-teal-600 text-sm font-bold text-white hover:bg-teal-700 transition-all cursor-pointer">
                                Extend Retention
                            </button>
                        </form>
                        <button type="button" class="inline-flex justify-center rounded-xl border border-slate-300 dark:border-slate-700 shadow-sm px-6 py-2 bg-white dark:bg-slate-800 text-sm font-bold text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 transition-all cursor-pointer" @click="showBulkExtendModal = false">
                            Cancel
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </template>

    <!-- Bulk Delete Modal -->
    <template x-teleport="body">
        <div x-show="showBulkDeleteModal" x-cloak style="display: none;" class="fixed inset-0 z-[100] overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-slate-950/80 backdrop-blur-sm transition-opacity" @click="showBulkDeleteModal = false"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                <div class="inline-block align-bottom bg-white dark:bg-slate-900 rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full border border-slate-200 dark:border-slate-800">
                    <div class="bg-white dark:bg-slate-900 px-6 pt-6 pb-6">
                        <div class="sm:flex sm:items-start">
                            <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-2xl bg-rose-100 dark:bg-rose-900/30 text-rose-600 dark:text-rose-400 sm:mx-0 sm:h-10 sm:w-10">
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                            </div>
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                                <h3 class="text-xl font-bold text-slate-900 dark:text-white" id="modal-title">Bulk Permanent Deletion</h3>
                                <div class="mt-2">
                                    <p class="text-sm text-slate-500 dark:text-slate-400">CRITICAL WARNING: Are you sure you want to permanently delete <span class="font-black text-rose-600 dark:text-rose-400" x-text="selectedIds.length"></span> selected patient records? This action cannot be undone and data will be lost forever.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-slate-50 dark:bg-slate-950/50 px-6 py-4 flex flex-row-reverse gap-3">
                        <form method="POST" action="{{ route('admin.retention.bulk-delete') }}" @submit="
                            $el.querySelectorAll('input[name=\'ids[]\']').forEach(e => e.remove());
                            selectedIds.forEach(id => {
                                const inp = document.createElement('input');
                                inp.type = 'hidden';
                                inp.name = 'ids[]';
                                inp.value = id;
                                $el.appendChild(inp);
                            });
                        ">
                            @csrf
                            @method('DELETE')
                            <template x-for="id in selectedIds" :key="id">
                                <input type="hidden" name="ids[]" :value="id">
                            </template>
                            <button type="submit" class="w-full inline-flex justify-center rounded-xl border border-transparent shadow-lg px-6 py-2 bg-rose-600 text-sm font-bold text-white hover:bg-rose-700 transition-all cursor-pointer">
                                Permanently Delete
                            </button>
                        </form>
                        <button type="button" class="inline-flex justify-center rounded-xl border border-slate-300 dark:border-slate-700 shadow-sm px-6 py-2 bg-white dark:bg-slate-800 text-sm font-bold text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 transition-all cursor-pointer" @click="showBulkDeleteModal = false">
                            Cancel
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </template>
</div>
@endsection
