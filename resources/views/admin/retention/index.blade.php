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
    <div class="p-6 border-b border-red-100 dark:border-red-900/30 bg-red-50 dark:bg-red-900/10 flex justify-between items-center">
        <div>
            <h3 class="text-lg font-bold text-red-900 dark:text-red-400">Records Pending Deletion</h3>
            <p class="text-sm text-red-700 dark:text-red-300 mt-1">The following patient records have reached their data expiration policy benchmark. You must either extend their retention or permanently delete them.</p>
        </div>
        <div class="flex items-center gap-3">
            <div x-show="selectedIds.length > 0" x-cloak class="flex items-center gap-2">
                <button @click="showBulkExtendModal = true" class="bg-blue-600 text-white px-4 py-2 rounded font-medium shadow-sm hover:bg-blue-700 transition flex items-center gap-2 text-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    Extend (<span x-text="selectedIds.length"></span>)
                </button>
                @can('force-delete')
                <button @click="showBulkDeleteModal = true" class="bg-red-600 text-white px-4 py-2 rounded font-medium shadow-sm hover:bg-red-700 transition flex items-center gap-2 text-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                    Delete (<span x-text="selectedIds.length"></span>)
                </button>
                @endcan
            </div>
            <span class="bg-red-200 dark:bg-red-900 text-red-800 dark:text-red-200 font-bold px-3 py-1 rounded-full text-sm">
                {{ $inactivePatients->total() }} Records
            </span>
        </div>
    </div>
    
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50 dark:bg-gray-900 border-b border-gray-100 dark:border-gray-700 text-xs uppercase text-gray-500 dark:text-gray-400 font-bold tracking-wider">
                    <th class="p-4 w-12">
                        <input type="checkbox" x-model="selectAll" @change="toggleAll" class="rounded border-gray-300 text-teal-600 shadow-sm focus:border-teal-300 focus:ring focus:ring-teal-200 focus:ring-opacity-50">
                    </th>
                    <th class="p-4">Patient Name</th>
                    <th class="p-4">Classification</th>
                    <th class="p-4">Expiration Date</th>
                    <th class="p-4 text-center">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 text-sm">
                @forelse($inactivePatients as $patient)
                    <tr class="hover:bg-red-50 dark:hover:bg-red-900/20 transition">
                        <td class="p-4">
                            <input type="checkbox" value="{{ $patient->patient_id }}" x-model="selectedIds" @change="if(!selectedIds.includes('{{ $patient->patient_id }}')) selectAll = false" class="rowCheckbox rounded border-gray-300 text-teal-600 shadow-sm focus:border-teal-300 focus:ring focus:ring-teal-200 focus:ring-opacity-50">
                        </td>
                        <td class="p-4">
                            <div class="flex items-center">
                                <div class="bg-red-100 dark:bg-red-900 text-red-600 dark:text-red-400 rounded-full w-10 h-10 flex items-center justify-center font-bold mr-3 flex-shrink-0">
                                    {{ substr($patient->first_name, 0, 1) }}{{ substr($patient->last_name, 0, 1) }}
                                </div>
                                <div>
                                    <p class="font-bold text-gray-900 dark:text-white">{{ $patient->full_name }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $patient->patient_id ?? 'No ID' }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="p-4">
                            <span class="px-2 py-1 bg-{{ $patient->classification == 'Pediatric' ? 'pink' : ($patient->classification == 'Senior Citizen' ? 'purple' : 'gray') }}-100 text-{{ $patient->classification == 'Pediatric' ? 'pink' : ($patient->classification == 'Senior Citizen' ? 'purple' : 'gray') }}-800 text-xs font-bold rounded">
                                {{ $patient->classification }}
                            </span>
                        </td>
                        <td class="p-4">
                            <p class="font-semibold text-red-600">{{ $patient->expires_at->format('M d, Y') }}</p>
                            <p class="text-xs text-red-400">{{ $patient->expires_at->diffForHumans() }}</p>
                        </td>
                        <td class="p-4">
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
                                    class="bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400 border border-blue-200 dark:border-blue-800 hover:bg-blue-100 dark:hover:bg-blue-900/50 font-bold px-3 py-1.5 rounded text-xs transition flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    Extend 1yr
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
                                    class="bg-red-600 text-white hover:bg-red-700 font-bold px-3 py-1.5 rounded text-xs transition flex items-center gap-1 shadow-sm">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    Permanently Delete
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
    <template x-if="showBulkExtendModal">
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="showBulkExtendModal = false"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full">
                    <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-blue-100 dark:bg-blue-900/40 sm:mx-0 sm:h-10 sm:w-10">
                                <svg class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                                <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white" id="modal-title">Bulk Extend Retention</h3>
                                <div class="mt-2">
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Are you sure you want to extend the data retention for <span class="font-bold text-blue-600" x-text="selectedIds.length"></span> selected patient records? This will push their expiration date by another 10 years.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-900 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <form method="POST" action="{{ route('admin.retention.bulk-extend') }}">
                            @csrf
                            <template x-for="id in selectedIds">
                                <input type="hidden" name="ids[]" :value="id">
                            </template>
                            <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">
                                Extend Retention
                            </button>
                        </form>
                        <button type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 dark:border-gray-600 shadow-sm px-4 py-2 bg-white dark:bg-gray-800 text-base font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm" @click="showBulkExtendModal = false">
                            Cancel
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </template>

    <!-- Bulk Delete Modal -->
    <template x-if="showBulkDeleteModal">
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="showBulkDeleteModal = false"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full">
                    <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 dark:bg-red-900/40 sm:mx-0 sm:h-10 sm:w-10">
                                <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                            </div>
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                                <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white" id="modal-title">Bulk Permanent Deletion</h3>
                                <div class="mt-2">
                                    <p class="text-sm text-gray-500 dark:text-gray-400">CRITICAL WARNING: Are you sure you want to permanently delete <span class="font-bold text-red-600" x-text="selectedIds.length"></span> selected patient records? This action cannot be undone and data will be lost forever.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-900 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <form method="POST" action="{{ route('admin.retention.bulk-delete') }}">
                            @csrf
                            @method('DELETE')
                            <template x-for="id in selectedIds">
                                <input type="hidden" name="ids[]" :value="id">
                            </template>
                            <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
                                Permanently Delete
                            </button>
                        </form>
                        <button type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 dark:border-gray-600 shadow-sm px-4 py-2 bg-white dark:bg-gray-800 text-base font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm" @click="showBulkDeleteModal = false">
                            Cancel
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </template>
</div>
@endsection
