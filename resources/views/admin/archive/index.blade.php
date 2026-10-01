@extends('layouts.admin')

@section('header', __('System Archive'))

@section('content')
<div class="max-w-7xl mx-auto space-y-6" x-data="{ showTruncateAllModal: false }">

    <!-- Breadcrumb Navigation -->
    <nav class="flex items-center gap-2 text-xs font-medium text-slate-500 dark:text-slate-400 py-2 border-b border-slate-200/60 dark:border-slate-800" aria-label="Breadcrumb">
        <a href="{{ route('admin.dashboard') }}" class="hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors flex items-center gap-1.5 shrink-0">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
            <span>Home</span>
        </a>
        <span class="text-slate-300 dark:text-slate-700">/</span>
        <span class="text-slate-700 dark:text-slate-300 font-semibold">System Archive</span>
    </nav>

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 pb-4 border-b border-slate-200/80 dark:border-slate-800">
        <div>
            <h1 class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white tracking-tight flex items-center gap-2.5">
                <span class="w-9 h-9 sm:w-10 sm:h-10 rounded-2xl bg-emerald-100/80 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0 border border-emerald-500/20 shadow-2xs">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
                    </svg>
                </span>
                <span>System Archive Directory</span>
            </h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Review, restore, or permanently purge archived, rejected, and soft-deleted municipal records to eliminate storage bloat.</p>
        </div>

        <div class="flex items-center gap-2.5 flex-wrap">
            <span class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 text-slate-600 dark:text-slate-400 text-xs font-semibold shadow-2xs">
                <svg class="w-4 h-4 text-amber-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>Automated Retention Policy</span>
            </span>

            @can('truncate-archive')
            <button type="button" @click="showTruncateAllModal = true" 
                    class="h-10 px-4 rounded-xl bg-gradient-to-r from-rose-600 to-amber-600 hover:from-rose-700 hover:to-amber-700 text-white font-bold text-xs shadow-md shadow-rose-600/20 transition-all flex items-center gap-2 cursor-pointer active:scale-95">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
                <span>Truncate Records &gt; 1 Year (All)</span>
                @if($totalOneYearRecords > 0)
                    <span class="px-1.5 py-0.5 rounded-full bg-white/20 text-[10px] font-black">{{ $totalOneYearRecords }}</span>
                @endif
            </button>
            @endcan
        </div>
    </div>

    <!-- Alert / Toast Messages -->
    @if(session('success'))
        <div class="p-4 rounded-2xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800/80 text-sm text-emerald-800 dark:text-emerald-300 font-bold shadow-xs flex items-center gap-3">
            <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <!-- Category Grid Container -->
    <div class="bg-white dark:bg-slate-900 rounded-3xl shadow-xs border border-slate-200/90 dark:border-slate-800 p-6 sm:p-8">
        <div class="border-b border-slate-100 dark:border-slate-800/80 pb-4 mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-base font-bold text-slate-900 dark:text-white">Archived System Categories</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Select a category below to inspect archived, rejected, or unfinished records and execute restorations or permanent purges.</p>
                </div>
                <span class="text-xs font-semibold text-slate-400 dark:text-slate-500">
                    {{ count($categories) }} Vaults Monitored
                </span>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($categories as $key => $category)
                <a href="{{ route('admin.archive.show', $key) }}" class="group rounded-2xl border border-slate-200/90 dark:border-slate-800 p-6 hover:shadow-xl hover:border-emerald-500/40 dark:hover:border-emerald-500/40 transition-all duration-300 bg-slate-50/50 hover:bg-white dark:bg-slate-900/60 dark:hover:bg-slate-800/80 relative overflow-hidden flex flex-col justify-between">
                    <div class="absolute -right-8 -top-8 w-24 h-24 bg-emerald-500/5 rounded-full blur-xl group-hover:bg-emerald-500/10 transition-all"></div>
                    
                    <div>
                        <div class="flex items-center justify-between mb-4">
                            <div class="w-12 h-12 rounded-2xl bg-emerald-100/80 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center group-hover:bg-emerald-600 group-hover:text-white transition-all duration-200 shadow-2xs border border-emerald-500/20">
                                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    {!! $category['icon'] !!}
                                </svg>
                            </div>
                            <div class="flex flex-col items-end gap-1">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-black {{ $category['count'] > 0 ? 'bg-amber-100 dark:bg-amber-950/60 text-amber-800 dark:text-amber-300 border border-amber-200 dark:border-amber-800/60' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 border border-slate-200 dark:border-slate-700' }} shadow-2xs">
                                    {{ $category['count'] }} Archived
                                </span>
                                @if(!empty($category['one_year_count']) && $category['one_year_count'] > 0)
                                    <span class="text-[10px] font-bold text-rose-600 dark:text-rose-400">
                                        {{ $category['one_year_count'] }} older than 1 yr
                                    </span>
                                @endif
                            </div>
                        </div>
                        <h4 class="text-lg font-bold text-slate-900 dark:text-white mb-1.5 truncate group-hover:text-emerald-600 dark:group-hover:text-emerald-400 transition-colors">
                            {{ $category['name'] }}
                        </h4>
                        <p class="text-xs text-slate-500 dark:text-slate-400 line-clamp-2 leading-relaxed">
                            {{ $category['description'] }}
                        </p>
                    </div>

                    <div class="mt-5 pt-3.5 border-t border-slate-200/80 dark:border-slate-800 flex items-center justify-between text-xs font-bold text-emerald-600 dark:text-emerald-400 group-hover:text-teal-700 dark:group-hover:text-teal-300 transition-colors">
                        <span>Browse Records</span> 
                        <svg class="w-4 h-4 transition-transform duration-200 group-hover:translate-x-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </div>
                </a>
            @endforeach
        </div>
    </div>

    <!-- Super Admin Global Truncate Modal -->
    @can('truncate-archive')
    <template x-teleport="body">
        <div x-show="showTruncateAllModal" x-cloak style="display: none;" class="fixed inset-0 z-[100] overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-slate-950/80 backdrop-blur-sm transition-opacity" @click="showTruncateAllModal = false"></div>
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
                                <h3 class="text-xl font-bold text-slate-900 dark:text-white" id="modal-title">System-wide 1-Year Archive Truncation</h3>
                                <div class="mt-2 text-xs text-slate-500 dark:text-slate-400 space-y-2">
                                    <p>
                                        You are about to execute a permanent purge of all archived, rejected, cancelled, and unfinished records older than 1 year across all system vaults.
                                    </p>
                                    <div class="p-3 bg-rose-50 dark:bg-rose-950/40 rounded-xl border border-rose-200 dark:border-rose-900/50 text-rose-800 dark:text-rose-300">
                                        <p class="font-bold">Important Data Disposal Notice:</p>
                                        <ul class="list-disc pl-4 mt-1 space-y-1">
                                            <li>Physical files (diagnostic scan uploads, avatar photos, banner images) will be permanently purged from server disk storage.</li>
                                            <li>Database records will be permanently removed. This cannot be undone.</li>
                                            <li>Current eligible records: <strong class="font-black text-rose-950 dark:text-white">{{ $totalOneYearRecords }} items</strong> older than 1 year.</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-slate-50 dark:bg-slate-950/50 px-6 py-4 flex flex-row-reverse gap-3">
                        <form method="POST" action="{{ route('admin.archive.truncate-all-year') }}">
                            @csrf
                            <button type="submit" class="w-full inline-flex justify-center rounded-xl border border-transparent shadow-lg px-6 py-2.5 bg-gradient-to-r from-rose-600 to-red-600 text-sm font-bold text-white hover:from-rose-700 hover:to-red-700 transition-all cursor-pointer">
                                Confirm &amp; Truncate 1-Year Records
                            </button>
                        </form>
                        <button type="button" class="inline-flex justify-center rounded-xl border border-slate-300 dark:border-slate-700 shadow-sm px-6 py-2.5 bg-white dark:bg-slate-800 text-sm font-bold text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 transition-all cursor-pointer" @click="showTruncateAllModal = false">
                            Cancel
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </template>
    @endcan
</div>
@endsection
