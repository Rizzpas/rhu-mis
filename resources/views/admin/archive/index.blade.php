@extends('layouts.admin')

@section('header')
<div class="flex justify-between items-center">
    <h2 class="text-xl font-bold text-white">
        {{ __('System Archive') }}
    </h2>
</div>
@endsection

@section('content')
<div class="bg-white dark:bg-slate-900 rounded-2xl shadow-xl border border-slate-200 dark:border-slate-800 overflow-hidden mb-6">
    <div class="p-6 border-b border-slate-100 dark:border-slate-800 bg-slate-50 dark:bg-slate-950 flex justify-between items-center">
        <div>
            <h3 class="text-lg font-bold text-gray-900 dark:text-white">Archived Categories</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Select a category to view soft-deleted records. Records older than 6 months are automatically permanently deleted.</p>
        </div>
    </div>
    <div class="p-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($categories as $key => $category)
            <a href="{{ route('admin.archive.show', $key) }}" class="block group rounded-xl border border-gray-100 dark:border-gray-700 p-6 hover:shadow-lg transition-all transform hover:-translate-y-1 bg-white dark:bg-gray-800 relative overflow-hidden">
                <div class="absolute top-0 right-0 w-24 h-24 bg-{{ $category['color'] }}-50 rounded-bl-full -z-10 transition-transform group-hover:scale-110"></div>
                <div class="flex items-center justify-between mb-4">
                    <div class="p-3 bg-{{ $category['color'] }}-100 text-{{ $category['color'] }}-600 rounded-lg group-hover:bg-{{ $category['color'] }}-600 group-hover:text-white transition-colors">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            {!! $category['icon'] !!}
                        </svg>
                    </div>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-{{ $category['color'] }}-100 text-{{ $category['color'] }}-800">
                        {{ $category['count'] }} Items
                    </span>
                </div>
                <h4 class="text-xl font-bold text-gray-900 dark:text-white mb-2 truncate">{{ $category['name'] }}</h4>
                <p class="text-sm text-gray-500 dark:text-gray-400 line-clamp-2">{{ $category['description'] }}</p>
                
                <div class="mt-4 flex items-center text-sm font-medium text-{{ $category['color'] }}-600 group-hover:text-{{ $category['color'] }}-800 transition-colors">
                    View Archive 
                    <svg class="w-4 h-4 ml-1 transition-transform group-hover:translate-x-1" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd" />
                    </svg>
                </div>
            </a>
        @endforeach
    </div>
</div>
@endsection
