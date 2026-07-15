@extends('layouts.app')

@section('content')
<div class="bg-teal-600 py-12 md:py-20 relative overflow-hidden">
    <div class="absolute inset-0 bg-teal-900 opacity-20 pattern-grid-lg"></div>
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center text-white">
        <h1 class="text-4xl md:text-5xl font-bold mb-4">{{ $service->name }}</h1>
        <p class="text-xl opacity-90 max-w-2xl mx-auto">{{ $service->description }}</p>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">
        <!-- Main Content -->
        <div class="lg:col-span-2">
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6 flex items-center">
                <span class="w-10 h-10 rounded-full bg-teal-100 text-teal-600 flex items-center justify-center mr-3 text-lg">
                    📋
                </span>
                Standard Procedure
            </h2>
            
            <div class="space-y-8">
                @if($service->steps && is_array($service->steps))
                    @foreach($service->steps as $index => $step)
                        <div class="flex">
                            <div class="flex-shrink-0 mr-4">
                                <div class="flex items-center justify-center w-8 h-8 rounded-full border-2 border-teal-500 font-bold text-teal-600">
                                    {{ $index + 1 }}
                                </div>
                                @if(!$loop->last)
                                    <div class="h-full w-0.5 bg-gray-200 mx-auto my-1"></div>
                                @endif
                            </div>
                            <div class="pb-8">
                                <h3 class="text-lg font-bold text-gray-900 dark:text-white">{{ $step['title'] ?? 'Step' }}</h3>
                                <p class="text-gray-600 dark:text-gray-400 mt-1">{{ $step['description'] ?? '' }}</p>
                            </div>
                        </div>
                    @endforeach
                @else
                    <p class="text-gray-500 dark:text-gray-400 italic">No specific steps listed for this service.</p>
                @endif
            </div>

             <div class="mt-12 p-6 bg-teal-50 rounded-xl border border-teal-100">
                <h3 class="font-bold text-teal-800 mb-2">Ready to visit?</h3>
                <p class="text-teal-700 mb-4">You can walk in for this service or check availability for related consultations.</p>
                <a href="{{ route('appointment.create') }}" class="inline-block bg-teal-600 text-white font-medium px-6 py-2 rounded-lg hover:bg-teal-700 transition">
                    Check Appointments
                </a>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="lg:col-span-1">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6 sticky top-24 border border-gray-100 dark:border-gray-700">
                <h3 class="font-bold text-gray-900 dark:text-white mb-4">Other Services</h3>
                <ul class="space-y-3">
                    @foreach($globalServices as $s)
                        <li>
                            <a href="{{ route('services.show', $s) }}" class="flex items-center text-gray-600 dark:text-gray-400 hover:text-teal-600 transition group">
                                <span class="w-2 h-2 rounded-full bg-gray-300 mr-3 group-hover:bg-teal-500 transition"></span>
                                {{ $s->name }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
