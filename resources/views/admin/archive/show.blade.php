@extends('layouts.admin')

@section('header', $title)

@section('content')
<div class="max-w-7xl mx-auto space-y-6" x-data="{
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

    <!-- Breadcrumb Navigation -->
    <nav class="flex items-center gap-2 text-xs font-medium text-slate-500 dark:text-slate-400 py-2 border-b border-slate-200/60 dark:border-slate-800" aria-label="Breadcrumb">
        <a href="{{ route('admin.dashboard') }}" class="hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors flex items-center gap-1.5 shrink-0">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
            <span>Home</span>
        </a>
        <span class="text-slate-300 dark:text-slate-700">/</span>
        <a href="{{ route('admin.archive.index') }}" class="hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors shrink-0">System Archive</a>
        <span class="text-slate-300 dark:text-slate-700">/</span>
        <span class="text-slate-700 dark:text-slate-300 font-semibold">{{ $title }}</span>
    </nav>

    <!-- Header Section (Content Management Style) -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 pb-4 border-b border-slate-200/80 dark:border-slate-800">
        <div>
            <h1 class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white tracking-tight flex items-center gap-2.5">
                <span class="w-9 h-9 sm:w-10 sm:h-10 rounded-2xl bg-emerald-100/80 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0 border border-emerald-500/20 shadow-2xs">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                </span>
                <span>{{ $title }}</span>
            </h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Manage deleted records for this category. Records older than 6 months are automatically purged.</p>
        </div>

        <div class="flex items-center gap-2.5">
            <a href="{{ route('admin.archive.index') }}" class="px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-300 text-xs font-semibold shadow-2xs flex items-center gap-1.5 transition-all cursor-pointer">
                <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                <span>Back to All Categories</span>
            </a>
        </div>
    </div>

    <!-- Alert / Toast Messages -->
    @if(session('success'))
        <div class="p-4 rounded-2xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800/80 text-sm text-emerald-800 dark:text-emerald-300 font-bold shadow-xs flex items-center gap-3">
            <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <!-- Table Container -->
    <div class="bg-white dark:bg-slate-900 rounded-3xl shadow-xs border border-slate-200/90 dark:border-slate-800 overflow-hidden">
        <div class="px-6 py-5 border-b border-slate-100 dark:border-slate-800/80 bg-slate-50/70 dark:bg-slate-900/60 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h3 class="text-base font-bold text-slate-900 dark:text-white">Deleted {{ $title }}</h3>
                <span class="text-xs text-slate-500 dark:text-slate-400 font-medium">{{ $records->count() }} archived item(s) available</span>
            </div>
            
            <div class="flex items-center gap-2.5" x-show="selectedIds.length > 0" x-cloak>
                <button @click="showBulkRestoreModal = true" class="h-10 px-4 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white font-bold text-xs shadow-md shadow-emerald-600/20 transition-all flex items-center gap-2 active:scale-95 cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"></path></svg>
                    <span>Restore Selected (<span x-text="selectedIds.length"></span>)</span>
                </button>
                @can('force-delete')
                <button @click="showBulkDeleteModal = true" class="h-10 px-4 rounded-xl bg-gradient-to-r from-rose-600 to-red-600 hover:from-rose-700 hover:to-red-700 text-white font-bold text-xs shadow-md shadow-rose-600/20 transition-all flex items-center gap-2 active:scale-95 cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                    <span>Purge Selected (<span x-text="selectedIds.length"></span>)</span>
                </button>
                @endcan
            </div>
        </div>
        
        <div class="overflow-x-auto">
            @if($records->isEmpty())
                <div class="text-center py-16 px-6">
                    <div class="w-14 h-14 rounded-2xl bg-slate-100 dark:bg-slate-800 text-slate-400 dark:text-slate-500 mx-auto flex items-center justify-center mb-3 shadow-xs">
                        <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
                        </svg>
                    </div>
                    <h3 class="text-base font-bold text-slate-900 dark:text-white">Archive is empty</h3>
                    <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">No deleted {{ strtolower($type) }} records found in this vault.</p>
                </div>
            @else
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50/90 dark:bg-slate-950/80 text-slate-500 dark:text-slate-400 text-[11px] font-extrabold uppercase tracking-wider border-b border-slate-200/80 dark:border-slate-800">
                            <th class="px-6 py-4 w-12">
                                <input type="checkbox" x-model="selectAll" @change="toggleAll" class="rounded border-slate-300 dark:border-slate-700 text-emerald-600 shadow-sm focus:ring-0 focus:ring-offset-0">
                            </th>
                            <th class="px-6 py-4">ID</th>
                            <th class="px-6 py-4">Name / Record Title</th>
                            <th class="px-6 py-4">Deleted At</th>
                            <th class="px-6 py-4">Prune Countdown</th>
                            <th class="px-6 py-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800 text-sm">
                        @foreach($records as $record)
                            <tr class="hover:bg-emerald-50/30 dark:hover:bg-slate-800/40 transition-colors">
                                <td class="px-6 py-4">
                                    <input type="checkbox" value="{{ $record->id }}" x-model="selectedIds" @change="if(!selectedIds.includes('{{ $record->id }}')) selectAll = false" class="rowCheckbox rounded border-slate-300 dark:border-slate-700 text-emerald-600 shadow-sm focus:ring-0 focus:ring-offset-0">
                                </td>
                                <td class="px-6 py-4 text-xs font-mono font-bold text-slate-500 dark:text-slate-400">
                                    <span class="px-2.5 py-1 rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-700">
                                        #{{ $record->id }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-slate-900 dark:text-white font-bold">
                                    {{ Str::limit($record->{$nameField}, 50) }}
                                </td>
                                <td class="px-6 py-4 text-xs text-slate-500 dark:text-slate-400 font-medium whitespace-nowrap">
                                    {{ $record->deleted_at->format('M d, Y h:i A') }}
                                </td>
                                <td class="px-6 py-4 text-xs text-slate-500 dark:text-slate-400 whitespace-nowrap">
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-50 dark:bg-amber-950/50 text-amber-700 dark:text-amber-300 border border-amber-200 dark:border-amber-800/60 shadow-2xs" title="Record will be permanently purged after this date.">
                                        {{ $record->deleted_at->copy()->addMonths(6)->format('M d, Y') }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex justify-end items-center gap-2">
                                        <!-- Restore Button -->
                                        <button type="button" 
                                            @click="$dispatch('open-confirmation', {
                                                action: '{{ route('admin.archive.restore', ['type' => $type, 'id' => $record->id]) }}',
                                                method: 'POST',
                                                title: 'Restore Record?',
                                                message: 'Are you sure you want to restore this record? It will be moved back to active status.',
                                                confirmText: 'Yes, Restore',
                                                type: 'info'
                                            })"
                                            class="h-8 px-3 rounded-xl inline-flex items-center gap-1.5 text-xs font-bold text-emerald-700 dark:text-emerald-300 bg-emerald-50 dark:bg-emerald-950/40 hover:bg-emerald-100 dark:hover:bg-emerald-900/50 border border-emerald-200 dark:border-emerald-800/60 shadow-2xs transition-all cursor-pointer active:scale-95">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"></path></svg>
                                            <span>Restore</span>
                                        </button>
                                        <!-- Force Delete Button -->
                                        @can('force-delete')
                                        <button type="button" 
                                            @click="$dispatch('open-confirmation', {
                                                action: '{{ route('admin.archive.force-delete', ['type' => $type, 'id' => $record->id]) }}',
                                                method: 'DELETE',
                                                title: 'Permanently Purge Record?',
                                                message: 'Are you sure you want to permanently delete record #{{ $record->id }}? This action cannot be undone.',
                                                confirmText: 'Delete Permanently',
                                                type: 'danger'
                                            })"
                                            class="h-8 px-3 rounded-xl inline-flex items-center gap-1.5 text-xs font-bold text-rose-700 dark:text-rose-300 bg-rose-50 dark:bg-rose-950/40 hover:bg-rose-100 dark:hover:bg-rose-900/50 border border-rose-200 dark:border-rose-800/60 shadow-2xs transition-all cursor-pointer active:scale-95">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            <span>Purge</span>
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
    </div>

    <!-- Bulk Restore Modal (Content Management Style) -->
    <template x-if="showBulkRestoreModal">
        <div class="fixed inset-0 z-[999] flex items-center justify-center bg-slate-950/70 backdrop-blur-sm px-4" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div @click.away="showBulkRestoreModal = false" class="bg-white dark:bg-slate-900 rounded-3xl shadow-2xl max-w-md w-full p-6 sm:p-7 border border-slate-200/90 dark:border-slate-800">
                <div class="flex items-center gap-3.5 mb-3">
                    <div class="w-11 h-11 rounded-2xl bg-emerald-100/80 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0 border border-emerald-500/20 shadow-xs">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-slate-900 dark:text-white" id="modal-title">Bulk Restore Confirmation</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Move selected archived items back to active records.</p>
                    </div>
                </div>
                <p class="text-xs text-slate-600 dark:text-slate-300 leading-relaxed mt-2.5">
                    Are you sure you want to restore <span class="font-bold text-emerald-600 dark:text-emerald-400" x-text="selectedIds.length"></span> selected records?
                </p>
                <div class="mt-6 flex justify-end gap-2.5">
                    <button type="button" @click="showBulkRestoreModal = false" class="px-4 py-2.5 rounded-xl text-xs font-semibold text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition border border-slate-300 dark:border-slate-700 cursor-pointer">
                        Cancel
                    </button>
                    <form method="POST" action="{{ route('admin.archive.bulk-restore', $type) }}">
                        @csrf
                        <template x-for="id in selectedIds">
                            <input type="hidden" name="ids[]" :value="id">
                        </template>
                        <button type="submit" class="px-5 py-2.5 rounded-xl text-xs font-bold bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white transition shadow-sm cursor-pointer active:scale-95">
                            Restore Records
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </template>

    <!-- Bulk Delete Modal (Content Management Style) -->
    <template x-if="showBulkDeleteModal">
        <div class="fixed inset-0 z-[999] flex items-center justify-center bg-slate-950/70 backdrop-blur-sm px-4" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div @click.away="showBulkDeleteModal = false" class="bg-white dark:bg-slate-900 rounded-3xl shadow-2xl max-w-md w-full p-6 sm:p-7 border border-slate-200/90 dark:border-slate-800">
                <div class="flex items-center gap-3.5 mb-3">
                    <div class="w-11 h-11 rounded-2xl bg-rose-100/80 dark:bg-rose-950/60 text-rose-600 dark:text-rose-400 flex items-center justify-center shrink-0 border border-rose-500/20 shadow-xs">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-slate-900 dark:text-white" id="modal-title">Bulk Permanent Purge</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Permanent destruction of soft-deleted records.</p>
                    </div>
                </div>
                <p class="text-xs text-slate-600 dark:text-slate-300 leading-relaxed mt-2.5">
                    <span class="font-bold text-rose-600 dark:text-rose-400 uppercase">Warning:</span> Are you sure you want to permanently purge <span class="font-bold text-rose-600 dark:text-rose-400" x-text="selectedIds.length"></span> selected records and associated data? This operation cannot be reversed.
                </p>
                <div class="mt-6 flex justify-end gap-2.5">
                    <button type="button" @click="showBulkDeleteModal = false" class="px-4 py-2.5 rounded-xl text-xs font-semibold text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition border border-slate-300 dark:border-slate-700 cursor-pointer">
                        Cancel
                    </button>
                    <form method="POST" action="{{ route('admin.archive.bulk-force-delete', $type) }}">
                        @csrf
                        @method('DELETE')
                        <template x-for="id in selectedIds">
                            <input type="hidden" name="ids[]" :value="id">
                        </template>
                        <button type="submit" class="px-5 py-2.5 rounded-xl text-xs font-bold bg-gradient-to-r from-rose-600 to-red-600 hover:from-rose-700 hover:to-red-700 text-white transition shadow-sm cursor-pointer active:scale-95">
                            Purge Permanently
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </template>
</div>
@endsection
