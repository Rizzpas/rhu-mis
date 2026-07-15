@extends('layouts.admin')

@section('header', 'Manage Announcements')

@section('content')
<div class="bg-white dark:bg-slate-900 rounded-2xl shadow-xl border border-slate-200 dark:border-slate-800 overflow-hidden" x-data="{ selectedAnnouncements: [], showBulkModal: false }">
    <!-- Premium Header -->
    <div class="px-6 py-5 border-b border-slate-200 dark:border-slate-800 flex flex-col md:flex-row justify-between items-start md:items-center gap-6 bg-white dark:bg-slate-900 relative overflow-hidden">
        <!-- Subtle background pattern -->
        <div class="absolute inset-0 opacity-5 dark:opacity-10 pointer-events-none">
            <svg class="h-full w-full" fill="none" viewBox="0 0 100 100" preserveAspectRatio="none">
                <path d="M0 100 L100 0" stroke="currentColor" class="text-slate-900 dark:text-white" stroke-width="0.1" />
                <path d="M0 0 L100 100" stroke="currentColor" class="text-slate-900 dark:text-white" stroke-width="0.1" />
            </svg>
        </div>

        <div class="relative z-10">
            <h3 class="text-xl font-bold text-slate-900 dark:text-white tracking-tight">System Announcements</h3>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Broadcast important news and events to all staff and portals.</p>
        </div>

        <div class="flex items-center gap-3 relative z-10">
            <div x-show="selectedAnnouncements.length > 0" x-cloak x-transition>
                <button @click="showBulkModal = true" class="bg-red-500/10 hover:bg-red-500/20 text-red-500 border border-red-500/20 px-4 py-2 rounded-xl font-bold text-sm transition-all flex items-center gap-2 backdrop-blur-md">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                    Archive Selected (<span x-text="selectedAnnouncements.length"></span>)
                </button>
            </div>
            <a href="{{ route('admin.announcements.create') }}" class="bg-emerald-600 hover:bg-emerald-700 text-white px-6 py-2.5 rounded-xl font-bold shadow-lg shadow-emerald-900/20 transition-all transform hover:-translate-y-0.5 flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Post New
            </a>
        </div>
    </div>
    
    <!-- Advanced Search and Filter Bar -->
    <div class="px-8 py-6 bg-slate-50 dark:bg-slate-800/40 border-b border-slate-200 dark:border-slate-800">
        <form method="GET" action="{{ route('admin.announcements.index') }}" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-5 items-end">
            <!-- Search Keyword -->
            <div class="xl:col-span-2">
                <label class="block text-[10px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-widest mb-2">Search Keyword</label>
                <div class="relative group">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400 group-focus-within:text-emerald-500 transition-colors">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </div>
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Enter keywords..." 
                        class="pl-12 pr-4 block w-full rounded-xl border border-slate-300 dark:border-slate-700 dark:bg-slate-900 dark:text-white shadow-sm focus:border-emerald-500 focus:ring-emerald-500 py-3.5 text-sm transition-all font-medium">
                </div>
            </div>

            <!-- Search In -->
            <div>
                <label class="block text-[10px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-widest mb-2">Search In</label>
                <div class="relative">
                    <select name="search_by" class="block w-full rounded-xl border border-slate-300 dark:border-slate-700 dark:bg-slate-900 dark:text-white shadow-sm focus:border-emerald-500 focus:ring-emerald-500 py-3.5 px-5 text-sm appearance-none font-bold">
                        <option value="all" {{ request('search_by') == 'all' ? 'selected' : '' }}>All Fields</option>
                        <option value="title" {{ request('search_by') == 'title' ? 'selected' : '' }}>Title</option>
                        <option value="subheading" {{ request('search_by') == 'subheading' ? 'selected' : '' }}>Subheading</option>
                        <option value="content" {{ request('search_by') == 'content' ? 'selected' : '' }}>Main Content</option>
                    </select>
                    <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none text-slate-400">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </div>
                </div>
            </div>

            <!-- Date Posted -->
            <div>
                <label class="block text-[10px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-widest mb-2">Date Posted</label>
                <input type="date" name="date_posted" value="{{ request('date_posted') }}" 
                    class="block w-full rounded-xl border border-slate-300 dark:border-slate-700 dark:bg-slate-900 dark:text-white shadow-sm focus:border-emerald-500 focus:ring-emerald-500 py-3.5 px-4 text-sm transition-all font-medium">
            </div>

            <!-- Status -->
            <div>
                <label class="block text-[10px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-widest mb-2">Status</label>
                <div class="relative">
                    <select name="status" class="block w-full rounded-xl border border-slate-300 dark:border-slate-700 dark:bg-slate-900 dark:text-white shadow-sm focus:border-emerald-500 focus:ring-emerald-500 py-3.5 px-5 text-sm appearance-none font-bold">
                        <option value="all">All Status</option>
                        <option value="published" {{ request('status') === 'published' ? 'selected' : '' }}>Published</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                    </select>
                    <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none text-slate-400">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="col-span-full pt-4 mt-2 border-t border-slate-200 dark:border-slate-800/50 flex flex-col md:flex-row justify-end items-center gap-3">
                @if(request()->anyFilled(['q', 'search_by', 'date_posted', 'status']) && (request('q') || request('search_by', 'all') !== 'all' || request('date_posted') || request('status', 'all') !== 'all'))
                    <a href="{{ route('admin.announcements.index') }}" class="w-full md:w-auto px-6 py-3 bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-300 rounded-xl font-bold text-sm text-center hover:bg-slate-50 dark:hover:bg-slate-700 transition-all border border-slate-300 dark:border-slate-700">
                        Clear All Filters
                    </a>
                @endif
                <button type="submit" class="w-full md:w-auto px-8 py-3 bg-emerald-600 text-white rounded-xl font-bold text-sm shadow-lg shadow-emerald-900/20 hover:bg-emerald-700 hover:-translate-y-0.5 transition-all active:scale-95 flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path></svg>
                    Apply Filters
                </button>
            </div>
        </form>
    </div>

    <!-- Table Section -->
    <div class="overflow-x-auto">
        <table class="w-full border-collapse">
            <thead>
                <tr class="bg-slate-50 dark:bg-slate-900/50 border-b border-slate-200 dark:border-slate-800">
                    <th class="px-6 py-4 text-left w-12">
                        <input type="checkbox" @change="if($event.target.checked) { selectedAnnouncements = Array.from(document.querySelectorAll('.announcement-checkbox')).map(cb => cb.value) } else { selectedAnnouncements = [] }" class="rounded border-slate-300 dark:border-slate-700 text-emerald-600 focus:ring-emerald-500 bg-white dark:bg-slate-800">
                    </th>
                    <th class="px-6 py-4 text-left text-xs font-black text-slate-500 dark:text-slate-400 uppercase tracking-widest">Title & Content</th>
                    <th class="px-6 py-4 text-left text-xs font-black text-slate-500 dark:text-slate-400 uppercase tracking-widest text-center">Engagement</th>
                    <th class="px-6 py-4 text-left text-xs font-black text-slate-500 dark:text-slate-400 uppercase tracking-widest text-center">Status</th>
                    <th class="px-6 py-4 text-right text-xs font-black text-slate-500 dark:text-slate-400 uppercase tracking-widest">Manage</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                @forelse($announcements as $announcement)
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/30 transition-colors group">
                        <td class="px-6 py-4">
                            <input type="checkbox" value="{{ $announcement->id }}" x-model="selectedAnnouncements" class="announcement-checkbox rounded border-slate-300 dark:border-slate-700 text-emerald-600 focus:ring-emerald-500 bg-white dark:bg-slate-800">
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-4">
                                @if($announcement->image_path)
                                    <div class="h-14 w-14 rounded-xl border border-slate-200 dark:border-slate-700 overflow-hidden shrink-0 shadow-sm group-hover:scale-110 transition-transform duration-300">
                                        <img src="{{ asset('uploads/' . $announcement->image_path) }}" class="h-full w-full object-cover">
                                    </div>
                                @else
                                    <div class="h-14 w-14 rounded-xl bg-slate-100 dark:bg-slate-800 flex items-center justify-center shrink-0">
                                        <svg class="w-6 h-6 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path></svg>
                                    </div>
                                @endif
                                <div class="min-w-0">
                                    <div class="text-sm font-bold text-slate-900 dark:text-white truncate max-w-md">{{ $announcement->title }}</div>
                                    <div class="text-[11px] text-slate-500 dark:text-slate-400 line-clamp-1 mt-0.5">{{ Str::limit(strip_tags($announcement->content), 80) }}</div>
                                    <div class="flex items-center gap-2 mt-1.5">
                                        <span class="text-[10px] font-black uppercase tracking-tighter text-slate-400 bg-slate-100 dark:bg-slate-800 px-1.5 py-0.5 rounded border border-slate-200 dark:border-slate-700">Posted {{ $announcement->created_at->diffForHumans() }}</span>
                                        @if($announcement->event_date)
                                            <span class="text-[10px] font-black uppercase tracking-tighter text-emerald-600 bg-emerald-50 dark:bg-emerald-900/20 px-1.5 py-0.5 rounded border border-emerald-100 dark:border-emerald-800/50">Event: {{ $announcement->event_date->format('M d, Y') }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex flex-col items-center">
                                <div class="text-xs font-black text-slate-900 dark:text-white">{{ $announcement->images->count() }}</div>
                                <div class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Sections</div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-center">
                            @php
                                $statusColors = [
                                    'published' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400 border-emerald-200 dark:border-emerald-800',
                                    'pending' => 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400 border-amber-200 dark:border-amber-800',
                                    'draft' => 'bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-400 border-slate-200 dark:border-slate-700'
                                ];
                                $color = $statusColors[$announcement->status] ?? $statusColors['draft'];
                            @endphp
                            <span class="px-2.5 py-1 text-[10px] font-black uppercase tracking-widest rounded-full border {{ $color }}">
                                {{ $announcement->status }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex justify-end items-center gap-2">
                                <a href="{{ route('admin.announcements.edit', $announcement) }}" class="p-2 text-slate-400 hover:text-emerald-500 bg-slate-50 dark:bg-slate-800 rounded-xl transition-all border border-transparent hover:border-emerald-100 dark:hover:border-emerald-900/50" title="Edit Announcement">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                </a>
                                <button type="button" @click="$dispatch('open-confirmation', {
                                    action: '{{ route('admin.announcements.destroy', $announcement) }}',
                                    method: 'DELETE',
                                    title: 'Archive Announcement?',
                                    message: 'This will move the announcement to the system archive.',
                                    confirmText: 'Yes, Archive',
                                    type: 'danger'
                                })" class="p-2 text-slate-400 hover:text-red-500 bg-slate-50 dark:bg-slate-800 rounded-xl transition-all border border-transparent hover:border-red-100 dark:hover:border-red-900/50" title="Archive">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-20 text-center">
                            <div class="flex flex-col items-center">
                                <div class="p-4 bg-slate-50 dark:bg-slate-800 rounded-full mb-4">
                                    <svg class="w-12 h-12 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path></svg>
                                </div>
                                <h4 class="text-lg font-bold text-slate-900 dark:text-white">No Announcements Yet</h4>
                                <p class="text-sm text-slate-500 dark:text-slate-400 mt-1 max-w-xs mx-auto">Get started by broadcasting your first news or event to the facility.</p>
                                <a href="{{ route('admin.announcements.create') }}" class="mt-6 inline-flex items-center gap-2 bg-emerald-600 text-white px-6 py-2.5 rounded-xl font-bold transition-all hover:bg-emerald-700">
                                    Create First Post
                                </a>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Bulk Archive Modal -->
<template x-if="showBulkModal">
    <div class="fixed inset-0 z-[60] overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-slate-950/80 backdrop-blur-sm transition-opacity" @click="showBulkModal = false"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-bottom bg-white dark:bg-slate-900 rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full border border-slate-200 dark:border-slate-800">
                <div class="bg-white dark:bg-slate-900 px-6 pt-6 pb-6">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-2xl bg-red-100 dark:bg-red-900/30 sm:mx-0 sm:h-10 sm:w-10">
                            <svg class="h-6 w-6 text-red-600 dark:text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                            <h3 class="text-xl font-bold text-slate-900 dark:text-white" id="modal-title">Bulk Archive Announcements</h3>
                            <div class="mt-2">
                                <p class="text-sm text-slate-500 dark:text-slate-400">Are you sure you want to archive <span class="font-black text-red-600 dark:text-red-400" x-text="selectedAnnouncements.length"></span> selected announcements? They will be moved to the archive module.</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-slate-50 dark:bg-slate-950/50 px-6 py-4 flex flex-row-reverse gap-3">
                    <form method="POST" action="{{ route('admin.announcements.bulk-delete') }}">
                        @csrf
                        @method('DELETE')
                        <template x-for="id in selectedAnnouncements">
                            <input type="hidden" name="ids[]" :value="id">
                        </template>
                        <button type="submit" class="w-full inline-flex justify-center rounded-xl border border-transparent shadow-lg px-6 py-2 bg-red-600 text-sm font-bold text-white hover:bg-red-700 transition-all">
                            Yes, Archive All
                        </button>
                    </form>
                    <button type="button" @click="showBulkModal = false" class="inline-flex justify-center rounded-xl border border-slate-300 dark:border-slate-700 shadow-sm px-6 py-2 bg-white dark:bg-slate-800 text-sm font-bold text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 transition-all">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>
@endsection
