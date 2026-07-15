@extends('layouts.app')

@section('content')
<div class="min-h-[70vh] flex items-center justify-center px-4">
    <div class="text-center">
        <div class="mb-6 relative">
            <span class="text-9xl font-black text-teal-600/20 dark:text-teal-400/10">401</span>
            <div class="absolute inset-0 flex items-center justify-center">
                <svg class="w-20 h-20 text-teal-600 dark:text-teal-400 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" /></svg>
            </div>
        </div>
        <h1 class="text-4xl font-black text-gray-900 dark:text-white mb-4">Unauthorized</h1>
        <p class="text-gray-600 dark:text-gray-400 mb-8 max-w-md mx-auto italic">
            "Hindi po kayo awtorisadong pumasok dito."<br>
            You don't have permission to access this section. Please log in with the appropriate credentials.
        </p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="{{ route('login') }}" class="bg-teal-600 text-white px-8 py-3 rounded-xl font-bold hover:bg-teal-700 transition shadow-lg">
                Go to Login
            </a>
            <a href="{{ route('welcome') }}" class="bg-white dark:bg-gray-800 border-2 border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-300 px-8 py-3 rounded-xl font-bold hover:bg-gray-50 dark:hover:bg-gray-700 transition shadow-sm">
                Back to Home
            </a>
        </div>
    </div>
</div>
@endsection
