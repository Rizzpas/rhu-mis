@extends('layouts.app')

@section('content')
<!-- Back button container -->
<div class="bg-transparent pt-8 pb-4">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <a href="{{ route('units.index') }}" class="inline-flex items-center text-sm font-medium text-green-700 dark:text-green-400 hover:text-green-800 dark:text-green-400 transition">
            <svg class="mr-2 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Back to Departments
        </a>
    </div>
</div>

<div class="bg-transparent pb-24 min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Unit Header -->
        <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-700 p-10 md:p-14 mb-16 relative overflow-hidden flex flex-col items-center text-center">
            <div class="absolute top-0 right-0 -mt-10 -mr-10 text-green-50 opacity-50">
                <svg class="w-64 h-64" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V7z" clip-rule="evenodd"></path></svg>
            </div>
            
            <div class="relative z-10 w-24 h-24 bg-green-100 dark:bg-green-900/40 text-green-600 rounded-full flex items-center justify-center shadow-inner mb-6">
                 <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
            </div>
            <h1 class="text-4xl md:text-5xl font-extrabold text-gray-900 dark:text-white mb-4">{{ $unit['name'] }}</h1>
            <p class="text-xl text-gray-500 dark:text-gray-400 max-w-2xl font-medium">{{ $unit['desc'] }}</p>
        </div>

        <!-- Dynamic Step-by-Step Guide for this unit -->
        <section id="guide" class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-700 p-8 md:p-12 mb-46">
            <div class="text-center mb-12">
                <h2 class="text-2xl md:text-3xl font-bold mb-2 text-gray-900 dark:text-white">Step-by-Step Process</h2>
                <p class="text-gray-500 dark:text-gray-400 text-sm max-w-2xl mx-auto">Navigate your visit with ease. Follow these four simple stages to minimize waiting times and prioritize your care directly at the {{ $unit['name'] }}.</p>
                <div class="h-1 w-30 bg-green-600 mx-auto mt-6 mb-26 rounded-full"></div>
            </div>

            <!-- Alternating Timeline Layout -->
            @php
                $steps = \App\Models\SiteSetting::getJson('steps_data_' . $unit['slug'], [
                    ['title' => 'Check-in & Enrollment', 'description' => 'Visit the Information Desk to check in. New patients are enrolled in the system, while existing records are retrieved instantly.'],
                    ['title' => 'Vitals & Screening', 'description' => 'Staff will record your weight, BP, and temperature. Results are encoded directly into your record for the doctor\'s review.'],
                    ['title' => 'Evaluation', 'description' => 'Once called, meet your Doctor or Nurse. They will assess your history, provide a diagnosis, and issue an e-prescription.'],
                    ['title' => 'Pharmacy / Exit', 'description' => 'Receive referrals for lab tests if needed. Finally, proceed to the RHU Pharmacy to claim your prescribed medication.']
                ]);
            @endphp
            <div class="relative wrap overflow-hidden py-10 h-full max-w-5xl mx-auto">
                <!-- Vertical Line -->
                <div class="absolute border-opacity-20 border-green-500 h-full border-l-4 left-4 md:left-1/2 md:-translate-x-1/2 z-0"></div>
                
                <div class="space-y-6 md:space-y-8 relative z-10">
                @foreach($steps as $index => $step)
                    @php $hasImage = !empty($step['image']); @endphp
                    <div class="flex items-stretch w-full {{ $index % 2 == 0 ? 'md:flex-row-reverse flex-row' : 'flex-row' }}">
                        
                        <!-- Opposite Side: Image or empty -->
                        <div class="w-0 md:w-[48%] hidden md:flex items-center justify-center">
                            @if($hasImage)
                                <div class="w-full max-w-sm rounded-2xl overflow-hidden shadow-md border border-gray-100 dark:border-gray-700 hover:shadow-lg transition">
                                    <img src="{{ asset('uploads/' . $step['image']) }}" alt="{{ $step['title'] ?? 'Step image' }}" class="w-full h-48 object-cover">
                                </div>
                            @endif
                        </div>
                        
                        <!-- Content Card overlapping the timeline -->
                        <div class="w-full md:w-[52%] px-6 py-6 rounded-2xl bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 shadow-lg hover:shadow-xl transition z-10 ml-2 md:ml-0 group {{ $hasImage ? 'min-h-[200px] flex items-center' : '' }}">
                            <div class="flex items-start gap-4 w-full">
                                <!-- Number attached to card -->
                                <div class="flex items-center justify-center bg-green-100 dark:bg-green-900 w-12 h-12 rounded-xl shrink-0 group-hover:bg-green-200 dark:group-hover:bg-green-800 transition-colors shadow-sm border border-white dark:border-gray-800">
                                    <h1 class="font-black text-xl text-green-700 dark:text-green-400">{{ $index + 1 }}</h1>
                                </div>
                                <div>
                                    <h4 class="font-bold text-gray-900 dark:text-white text-lg mb-1">{{ $step['title'] ?? '' }}</h4>
                                    <p class="text-sm text-gray-600 dark:text-gray-400 leading-relaxed">{{ $step['description'] ?? '' }}</p>
                                </div>
                            </div>

                            <!-- Mobile-only image (below card content) -->
                            @if($hasImage)
                                <div class="mt-4 rounded-xl overflow-hidden border border-gray-100 dark:border-gray-700 shadow-sm md:hidden">
                                    <img src="{{ asset('uploads/' . $step['image']) }}" alt="{{ $step['title'] ?? 'Step image' }}" class="w-full h-40 object-cover">
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
                </div>
            </div>
            
        </section>

    </div>
</div>
@endsection
