@extends('layouts.admin')

@section('header', __('System Archive'))

@section('content')
<div class="max-w-7xl mx-auto space-y-6">

    <!-- Breadcrumb Navigation -->
    <nav class="flex items-center gap-2 text-xs font-medium text-slate-500 dark:text-slate-400 py-2 border-b border-slate-200/60 dark:border-slate-800" aria-label="Breadcrumb">
        <a href="{{ route('admin.dashboard') }}" class="hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors flex items-center gap-1.5 shrink-0">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
            <span>Home</span>
        </a>
        <span class="text-slate-300 dark:text-slate-700">/</span>
        <span class="text-slate-700 dark:text-slate-300 font-semibold">System Archive</span>
    </nav>

    <!-- Header Section (Content Management Style) -->
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
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Review, restore, or permanently purge soft-deleted municipal records and system entities.</p>
        </div>

        <div class="flex items-center gap-2.5">
            <span class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 text-slate-600 dark:text-slate-400 text-xs font-semibold shadow-2xs">
                <svg class="w-4 h-4 text-amber-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>Retention: 6 Months Auto-Prune</span>
            </span>
        </div>
    </div>

    <!-- Category Grid Container -->
    <div class="bg-white dark:bg-slate-900 rounded-3xl shadow-xs border border-slate-200/90 dark:border-slate-800 p-6 sm:p-8">
        <div class="border-b border-slate-100 dark:border-slate-800/80 pb-4 mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-base font-bold text-slate-900 dark:text-white">Archived Categories</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Select a category below to inspect archived records and bulk restore or purge items.</p>
                </div>
                <span class="text-xs font-semibold text-slate-400 dark:text-slate-500">
                    {{ count($categories) }} Categories Monitored
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
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-black {{ $category['count'] > 0 ? 'bg-amber-100 dark:bg-amber-950/60 text-amber-800 dark:text-amber-300 border border-amber-200 dark:border-amber-800/60' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 border border-slate-200 dark:border-slate-700' }} shadow-2xs">
                                {{ $category['count'] }} Archived
                            </span>
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
</div>
@endsection
