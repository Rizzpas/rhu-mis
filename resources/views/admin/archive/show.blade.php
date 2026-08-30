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
    <div class="px-6 py-5 border-b border-slate-200 dark:border-slate-800 bg-slate-50/80 dark:bg-slate-900/80 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h3 class="text-lg font-bold text-slate-900 dark:text-white">Deleted Records</h3>
            <span class="text-xs text-slate-500 dark:text-slate-400 font-medium">{{ $records->count() }} total archived {{ strtolower($title) }}</span>
        </div>
        <div class="flex items-center gap-2.5" x-show="selectedIds.length > 0" x-cloak>
            <button @click="showBulkRestoreModal = true" class="h-11 px-4 rounded-xl bg-teal-600 hover:bg-teal-700 text-white font-bold text-sm shadow-md shadow-teal-600/20 transition-all flex items-center gap-2 active:scale-95 cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"></path></svg>
                <span>Restore Selected (<span x-text="selectedIds.length"></span>)</span>
            </button>
            @can('force-delete')
            <button @click="showBulkDeleteModal = true" class="h-11 px-4 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-bold text-sm shadow-md shadow-rose-600/20 transition-all flex items-center gap-2 active:scale-95 cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                <span>Delete Selected (<span x-text="selectedIds.length"></span>)</span>
            </button>
            @endcan
        </div>
    </div>
    
    @if(session('success'))
        <div class="p-4 mx-6 mt-6 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-sm text-emerald-800 dark:text-emerald-300 font-bold shadow-2xs">
            {{ session('success') }}
        </div>
    @endif
    
    <div class="overflow-x-auto">
        @if($records->isEmpty())
            <div class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-slate-300 dark:text-slate-600 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <h3 class="text-base font-bold text-slate-900 dark:text-white">Archive is empty</h3>
                <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">No deleted {{ strtolower($type) }} found.</p>
            </div>
        @else
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/90 dark:bg-slate-900/90 text-slate-500 dark:text-slate-400 text-[11px] font-extrabold uppercase tracking-wider border-b border-slate-200 dark:border-slate-800">
                        <th class="px-6 py-4 w-12">
                            <input type="checkbox" x-model="selectAll" @change="toggleAll" class="rounded border-slate-300 dark:border-slate-700 text-teal-600 shadow-sm focus:border-teal-300 focus:ring focus:ring-teal-200 focus:ring-opacity-50">
                        </th>
                        <th class="px-6 py-4">ID</th>
                        <th class="px-6 py-4">Name / Title</th>
                        <th class="px-6 py-4">Deleted At</th>
                        <th class="px-6 py-4">Prune Date</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800 text-sm">
                    @foreach($records as $record)
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/40 transition-colors">
                            <td class="px-6 py-4">
                                <input type="checkbox" value="{{ $record->id }}" x-model="selectedIds" @change="if(!selectedIds.includes('{{ $record->id }}')) selectAll = false" class="rowCheckbox rounded border-slate-300 dark:border-slate-700 text-teal-600 shadow-sm focus:border-teal-300 focus:ring focus:ring-teal-200 focus:ring-opacity-50">
                            </td>
                            <td class="px-6 py-4 text-sm text-slate-600 dark:text-slate-400 font-mono font-bold">#{{ $record->id }}</td>
                            <td class="px-6 py-4 text-sm text-slate-900 dark:text-white font-semibold whitespace-nowrap">
                                {{ Str::limit($record->{$nameField}, 40) }}
                            </td>
                            <td class="px-6 py-4 text-xs text-slate-500 dark:text-slate-400 font-medium">
                                {{ $record->deleted_at->format('M d, Y h:i A') }}
                            </td>
                            <td class="px-6 py-4 text-xs text-slate-500 dark:text-slate-400">
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-50 dark:bg-amber-950/50 text-amber-700 dark:text-amber-300 border border-amber-200 dark:border-amber-800/60 shadow-2xs" title="Record will be permanently deleted on this date.">
                                    {{ $record->deleted_at->copy()->addMonths(6)->format('M d, Y') }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex justify-end items-center gap-1.5">
                                    <!-- Restore Button -->
                                    <button type="button" 
                                        @click="$dispatch('open-confirmation', {
                                            action: '{{ route('admin.archive.restore', ['type' => $type, 'id' => $record->id]) }}',
                                            method: 'POST',
                                            title: 'Restore Record?',
                                            message: 'Are you sure you want to restore this record? It will be moved back to the active list.',
                                            confirmText: 'Yes, Restore',
                                            type: 'info'
                                        })"
                                        class="h-8 px-3 rounded-lg inline-flex items-center gap-1 text-xs font-bold text-teal-700 dark:text-teal-300 bg-teal-50 dark:bg-teal-950/40 hover:bg-teal-100 dark:hover:bg-teal-900/50 border border-teal-200 dark:border-teal-800/60 shadow-2xs transition-all cursor-pointer">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"></path></svg>
                                        <span>Restore</span>
                                    </button>
                                    <!-- Force Delete Button -->
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
                                        class="h-8 px-3 rounded-lg inline-flex items-center gap-1 text-xs font-bold text-rose-700 dark:text-rose-300 bg-rose-50 dark:bg-rose-950/40 hover:bg-rose-100 dark:hover:bg-rose-900/50 border border-rose-200 dark:border-rose-800/60 shadow-2xs transition-all cursor-pointer">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        <span>Delete</span>
                                    </button>
                                    @endcan
                                </div>
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
