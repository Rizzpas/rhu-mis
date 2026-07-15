@extends('layouts.app')

@section('content')
<div class="bg-transparent py-16 min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-10 mb-34">
        
        <div class="text-center mb-16">
            <h1 class="text-4xl font-extrabold text-gray-900 dark:text-white tracking-tight sm:text-5xl">
                Our Sub-Units & Facilities
            </h1>
            <p class="mt-4 text-xl text-gray-500 dark:text-gray-400 max-w-2xl mx-auto">
                Discover the specialized health sectors operating within and alongside the main Rural Health Unit.
            </p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
            @foreach($units as $unit)
            <div class="bg-white dark:bg-gray-800 rounded-3xl p-8 shadow-sm hover:shadow-xl hover:-translate-y-1 transition duration-300 border border-gray-100 dark:border-gray-700 flex flex-col items-center text-center group">
                <div class="h-20 w-20 bg-green-50 dark:bg-green-900/30 text-green-600 rounded-full flex items-center justify-center mb-6 shadow-inner group-hover:bg-green-600 group-hover:text-white transition duration-300">
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                </div>
                <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-3">{{ $unit['name'] }}</h3>
                <p class="text-gray-500 dark:text-gray-400 mb-8 flex-grow">{{ $unit['desc'] }}</p>
                
                <a href="{{ route('units.show', $unit['slug']) }}" class="w-full inline-flex justify-center items-center px-6 py-3 border border-transparent text-base font-medium rounded-xl text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition shadow-md">
                    View Guide & Details
                </a>
            </div>
            @endforeach
        </div>

    </div>
</div>
@endsection
