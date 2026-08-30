@extends('layouts.admin')

@section('header', __('System Archive'))

@section('content')
<div class="bg-white dark:bg-slate-900 rounded-2xl shadow-xl border border-slate-200 dark:border-slate-800 overflow-hidden mb-6">
    <div class="px-8 py-6 border-b border-slate-100 dark:border-slate-800 bg-slate-50/80 dark:bg-slate-950/80 flex justify-between items-center">
        <div>
            <h3 class="text-xl font-bold text-slate-900 dark:text-white tracking-tight">Archived Categories</h3>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Select a category to view soft-deleted records. Records older than 6 months are permanently purged.</p>
        </div>
    </div>
    <div class="p-8 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($categories as $key => $category)
            <a href="{{ route('admin.archive.show', $key) }}" class="block group rounded-2xl border border-slate-200 dark:border-slate-800 p-6 hover:shadow-lg hover:border-slate-300 dark:hover:border-slate-700 transition-all duration-200 bg-white dark:bg-slate-900/60 relative overflow-hidden">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-3 bg-teal-50 dark:bg-teal-950/50 text-teal-600 dark:text-teal-400 rounded-xl group-hover:bg-teal-600 group-hover:text-white transition-colors duration-200">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            {!! $category['icon'] !!}
                        </svg>
                    </div>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-700 shadow-2xs">
                        {{ $category['count'] }} Items
                    </span>
                </div>
                <h4 class="text-lg font-bold text-slate-900 dark:text-white mb-1.5 truncate group-hover:text-teal-600 dark:group-hover:text-teal-400 transition-colors">{{ $category['name'] }}</h4>
                <p class="text-xs text-slate-500 dark:text-slate-400 line-clamp-2 leading-relaxed">{{ $category['description'] }}</p>
                
                <div class="mt-4 pt-3 border-t border-slate-100 dark:border-slate-800/80 flex items-center justify-between text-xs font-bold text-teal-600 dark:text-teal-400 group-hover:text-teal-700 dark:group-hover:text-teal-300 transition-colors">
                    <span>View Archive</span> 
                    <svg class="w-4 h-4 transition-transform duration-200 group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </div>
            </a>
        @endforeach
    </div>
</div>
@endsection
