@extends('layouts.admin')

@section('header')
<div class="flex items-center gap-2">
    <a href="{{ route('admin.archive.index') }}" class="text-white/80 hover:text-white transition-colors mr-1 inline-flex items-center" title="Back to System Archive">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
    </a>
    <span class="text-white font-bold text-xl tracking-tight">{{ $title }}</span>
</div>
@endsection

@section('content')
<div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden mb-6" x-data="{
        selectedIds: [],
        selectAll: false,
        showBulkRestoreModal: false,
        showBulkDeleteModal: false,
        toggleAll() {
            if (this.selectAll) {
                this.selectedIds = [...document.querySelectorAll('.rowCheckbox')].map(cb => cb.value);
            } else {
                this.selectedIds = [];
            }
        }
    }">
    <div class="p-6 border-b border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 flex justify-between items-center">
        <div>
            <h3 class="text-lg font-bold text-gray-900 dark:text-white">Deleted Records</h3>
            <span class="text-sm text-gray-500 dark:text-gray-400">{{ $records->count() }} total {{ strtolower($title) }}</span>
        </div>
        <div class="flex items-center gap-3" x-show="selectedIds.length > 0" x-cloak>
            <button @click="showBulkRestoreModal = true" class="bg-teal-600 text-white px-4 py-2 rounded font-medium shadow-sm hover:bg-teal-700 transition-colors flex items-center gap-2 text-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"></path></svg>
                Restore Selected (<span x-text="selectedIds.length"></span>)
            </button>
            @can('force-delete')
            <button @click="showBulkDeleteModal = true" class="bg-red-600 text-white px-4 py-2 rounded font-medium shadow-sm hover:bg-red-700 transition-colors flex items-center gap-2 text-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                Delete Selected (<span x-text="selectedIds.length"></span>)
            </button>
            @endcan
        </div>
    </div>
    
    @if(session('success'))
        <div class="p-4 mx-6 mt-6 rounded-md bg-green-50 dark:bg-green-900/30 border-l-4 border-green-400 text-sm text-green-700 dark:text-green-400 font-medium">
            {{ session('success') }}
        </div>
    @endif
    
    <div class="p-6 overflow-x-auto">
        @if($records->isEmpty())
            <div class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-300 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Archive is empty</h3>
                <p class="mt-1 text-gray-500 dark:text-gray-400">No deleted {{ strtolower($type) }} found.</p>
            </div>
        @else
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 dark:bg-gray-900/50 text-gray-500 dark:text-gray-400 text-xs uppercase tracking-wider border-b border-gray-200 dark:border-gray-700">
                        <th class="p-4 w-12">
                            <input type="checkbox" x-model="selectAll" @change="toggleAll" class="rounded border-gray-300 text-teal-600 shadow-sm focus:border-teal-300 focus:ring focus:ring-teal-200 focus:ring-opacity-50">
                        </th>
                        <th class="p-4 font-semibold">ID</th>
                        <th class="p-4 font-semibold">Name / Title</th>
                        <th class="p-4 font-semibold">Deleted At</th>
                        <th class="p-4 font-semibold">Prune Date</th>
                        <th class="p-4 font-semibold text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($records as $record)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 dark:bg-gray-900/50 transition">
                            <td class="p-4">
                                <input type="checkbox" value="{{ $record->id }}" x-model="selectedIds" @change="if(!selectedIds.includes('{{ $record->id }}')) selectAll = false" class="rowCheckbox rounded border-gray-300 text-teal-600 shadow-sm focus:border-teal-300 focus:ring focus:ring-teal-200 focus:ring-opacity-50">
                            </td>
                            <td class="p-4 text-sm text-gray-600 dark:text-gray-400 font-mono">#{{ $record->id }}</td>
                            <td class="p-4 text-sm text-gray-900 dark:text-white font-medium whitespace-nowrap">
                                {{ Str::limit($record->{$nameField}, 40) }}
                            </td>
                            <td class="p-4 text-sm text-gray-500 dark:text-gray-400">
                                {{ $record->deleted_at->format('M d, Y h:i A') }}
                            </td>
                            <td class="p-4 text-sm text-gray-500 dark:text-gray-400">
                                <span class="text-xs px-2 py-1 rounded bg-yellow-100 text-yellow-800 font-medium" title="Record will be permanently deleted on this date.">
                                    {{ $record->deleted_at->copy()->addMonths(6)->format('M d, Y') }}
                                </span>
                            </td>
                            <td class="p-4 text-right space-x-2">
                                <!-- Restore Form -->
                                <button type="button" 
                                    @click="$dispatch('open-confirmation', {
                                        action: '{{ route('admin.archive.restore', ['type' => $type, 'id' => $record->id]) }}',
                                        method: 'POST',
                                        title: 'Restore Record?',
                                        message: 'Are you sure you want to restore this record? It will be moved back to the active list.',
                                        confirmText: 'Yes, Restore',
                                        type: 'info'
                                    })"
                                    class="inline-flex items-center px-3 py-1.5 bg-white dark:bg-gray-800 border border-teal-200 rounded-lg text-xs font-medium text-teal-600 hover:bg-teal-50 hover:text-teal-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500 transition-colors shadow-sm">
                                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"></path></svg>
                                    Restore
                                </button>
                                <!-- Force Delete Form -->
                                @can('force-delete')
                                <button type="button" 
                                    @click="$dispatch('open-confirmation', {
                                        action: '{{ route('admin.archive.force-delete', ['type' => $type, 'id' => $record->id]) }}',
                                        method: 'DELETE',
                                        title: 'Permanently Delete?',
                                        message: 'Are you sure you want to permanently delete record #{{ $record->id }}? This action cannot be undone.',
                                        confirmText: 'Delete Permanently',
                                        type: 'danger'
                                    })"
                                    class="inline-flex items-center px-3 py-1.5 bg-white dark:bg-gray-800 border border-red-200 rounded-lg text-xs font-medium text-red-600 hover:bg-red-50 hover:text-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors shadow-sm">
                                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    Delete
                                </button>
                                @endcan
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>

    <!-- Bulk Restore Modal -->
    <template x-if="showBulkRestoreModal">
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="showBulkRestoreModal = false"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full">
                    <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-teal-100 sm:mx-0 sm:h-10 sm:w-10">
                                <svg class="h-6 w-6 text-teal-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"></path>
                                </svg>
                            </div>
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                                <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white" id="modal-title">Bulk Restore Confirmation</h3>
                                <div class="mt-2">
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Are you sure you want to restore <span class="font-bold text-teal-600" x-text="selectedIds.length"></span> selected records?</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-900 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <form method="POST" action="{{ route('admin.archive.bulk-restore', $type) }}">
                            @csrf
                            <template x-for="id in selectedIds">
                                <input type="hidden" name="ids[]" :value="id">
                            </template>
                            <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-teal-600 text-base font-medium text-white hover:bg-teal-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500 sm:ml-3 sm:w-auto sm:text-sm">
                                Restore Selected
                            </button>
                        </form>
                        <button type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 dark:border-gray-600 shadow-sm px-4 py-2 bg-white dark:bg-gray-800 text-base font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm" @click="showBulkRestoreModal = false">
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
                            <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                                <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                            </div>
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                                <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white" id="modal-title">Bulk Permanent Deletion</h3>
                                <div class="mt-2">
                                    <p class="text-sm text-gray-500 dark:text-gray-400">CRITICAL WARNING: Are you sure you want to permanently delete <span class="font-bold text-red-600" x-text="selectedIds.length"></span> selected records and their associated files? This action cannot be undone.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-900 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <form method="POST" action="{{ route('admin.archive.bulk-force-delete', $type) }}">
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
