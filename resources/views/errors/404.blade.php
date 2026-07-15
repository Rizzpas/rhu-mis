@extends('layouts.app')

@section('content')
<div class="min-h-[70vh] flex items-center justify-center px-4">
    <div class="text-center">
        <div class="mb-6 relative">
            <span class="text-9xl font-black text-teal-600/20 dark:text-teal-400/10">404</span>
            <div class="absolute inset-0 flex items-center justify-center">
                <svg class="w-20 h-20 text-teal-600 dark:text-teal-400 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 9.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            </div>
        </div>
        <h1 class="text-4xl font-black text-gray-900 dark:text-white mb-4">Page Not Found</h1>
        <p class="text-gray-600 dark:text-gray-400 mb-8 max-w-md mx-auto italic">
            "Para pong nawawala ang hinahanap ninyong pahina."<br>
            Sorry, we couldn't find the page you're looking for. It might have been moved or deleted.
        </p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <button onclick="window.history.back()" class="bg-white dark:bg-gray-800 border-2 border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-300 px-8 py-3 rounded-xl font-bold hover:bg-gray-50 dark:hover:bg-gray-700 transition shadow-sm">
                Go Back
            </button>
            <a href="{{ route('welcome') }}" class="bg-teal-600 text-white px-8 py-3 rounded-xl font-bold hover:bg-teal-700 transition shadow-lg">
                Back to Home
            </a>
        </div>
    </div>
</div>
@endsection
