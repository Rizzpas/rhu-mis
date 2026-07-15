@extends('layouts.app')

@section('content')
<div class="bg-transparent pt-16 pb-24 min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="text-center mb-16">
            <h1 class="text-4xl font-extrabold text-gray-900 dark:text-white tracking-tight sm:text-5xl border-b-4 border-green-600 inline-block pb-4">
                About the Rural Health Unit
            </h1>
            <p class="mt-6 text-xl text-gray-500 dark:text-gray-400 max-w-3xl mx-auto">
                Dedicated to providing responsive, equitable, and quality primary healthcare services to the citizens of Silang, Cavite.
            </p>
        </div>

        <!-- Mission & Vision -->
        <section class="bg-white dark:bg-gray-800 rounded-3xl shadow-lg border border-gray-100 dark:border-gray-700 overflow-hidden mb-20">
            <div class="grid grid-cols-1 md:grid-cols-2">
                <div class="p-12 border-b md:border-b-0 md:border-r border-gray-100 dark:border-gray-700 bg-green-50 dark:bg-green-900/30 backdrop-blur-sm relative overflow-hidden group hover:bg-green-100 dark:bg-green-900/40 transition duration-500">
                    <div class="absolute -right-10 -top-10 text-green-200 opacity-20 transform group-hover:scale-110 transition duration-500">
                        <svg class="w-64 h-64" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                    </div>
                    <div class="relative z-10">
                        <h2 class="text-3xl font-bold text-green-800 dark:text-green-400 mb-6 flex items-center">
                            <svg class="w-8 h-8 mr-3 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                            Our Mission
                        </h2>
                        <p class="text-lg text-gray-700 dark:text-gray-300 leading-relaxed font-medium whitespace-pre-wrap">
                            {{ \App\Models\SiteSetting::get('mission_statement', '"To provide responsive, equitable, and quality primary healthcare services to all citizens. We commit to transparency and excellence by utilizing modern management systems to eliminate barriers to health access and pharmaceutical needs."') }}
                        </p>
                    </div>
                </div>
                <div class="p-12 relative overflow-hidden group hover:bg-gray-50 dark:hover:bg-gray-700 dark:bg-gray-900 transition duration-500">
                    <div class="absolute -right-10 -top-10 text-gray-100 opacity-50 transform group-hover:scale-110 transition duration-500">
                        <svg class="w-64 h-64" fill="currentColor" viewBox="0 0 20 20"><path d="M15 8a3 3 0 10-2.977-2.63l-4.94 2.47a3 3 0 100 4.319l4.94 2.47a3 3 0 10.895-1.789l-4.94-2.47a3.027 3.027 0 000-.74l4.94-2.47C13.456 7.68 14.19 8 15 8z"></path></svg>
                    </div>
                    <div class="relative z-10">
                        <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-6 flex items-center">
                            <svg class="w-8 h-8 mr-3 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                            Our Vision
                        </h2>
                        <p class="text-lg text-gray-600 dark:text-gray-400 leading-relaxed whitespace-pre-wrap">
                            {{ \App\Models\SiteSetting::get('vision_statement', '"A healthy and empowered community served by a world-class Rural Health Unit that champions technological advancement and medical integrity for the well-being of every family, ensuring that quality healthcare is a reliable and efficient right for every citizen."') }}
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Organizational Chart -->
        @include('partials.orgchart')

    </div>
</div>
@endsection
