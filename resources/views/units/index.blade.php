@extends('layouts.app')

@section('content')
<div class="bg-[#FAF9F6] dark:bg-gray-900 min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 sm:py-20">
        
        {{-- Header --}}
        <div class="text-center mb-14" data-reveal>
            <span class="inline-flex items-center gap-2 px-3.5 py-1.5 bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300 text-xs font-bold rounded-full mb-5 uppercase tracking-wider">
                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4 4a2 2 0 012-2h8a2 2 0 012 2v12a1 1 0 110 2h-3a1 1 0 01-1-1v-2a1 1 0 00-1-1H9a1 1 0 00-1 1v2a1 1 0 01-1 1H4a1 1 0 110-2V4zm3 1h2v2H7V5zm2 4H7v2h2V9zm2-4h2v2h-2V5zm2 4h-2v2h2V9z" clip-rule="evenodd"/></svg>
                Healthcare Facilities
            </span>
            <h1 class="font-display text-4xl sm:text-5xl font-extrabold text-gray-900 dark:text-white tracking-tight">
                Our Sub-Units & Facilities
            </h1>
            <p class="mt-4 text-xl text-gray-500 dark:text-gray-400 max-w-2xl mx-auto">
                Discover the specialized health sectors operating within and alongside the main Rural Health Unit.
            </p>
            <div class="section-accent mx-auto mt-6"></div>
        </div>

        @php
            $unitIcons = [
                'main-health-center' => [
                    'icon' => '<svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>',
                    'color' => 'green',
                ],
                'lying-in-clinic' => [
                    'icon' => '<svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z"/></svg>',
                    'color' => 'pink',
                ],
                'dental-clinic' => [
                    'icon' => '<svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15.182 15.182a4.5 4.5 0 01-6.364 0M21 12a9 9 0 11-18 0 9 9 0 0118 0zM9.75 9.75c0 .414-.168.75-.375.75S9 10.164 9 9.75 9.168 9 9.375 9s.375.336.375.75zm-.375 0h.008v.015h-.008V9.75zm5.625 0c0 .414-.168.75-.375.75s-.375-.336-.375-.75.168-.75.375-.75.375.336.375.75zm-.375 0h.008v.015h-.008V9.75z"/></svg>',
                    'color' => 'sky',
                ],
                'tb-dots-facility' => [
                    'icon' => '<svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.75 3.104v5.714a2.25 2.25 0 01-.659 1.591L5 14.5M9.75 3.104c-.251.023-.501.05-.75.082m.75-.082a24.301 24.301 0 014.5 0m0 0v5.714c0 .597.237 1.17.659 1.591L19.8 15.3M14.25 3.104c.251.023.501.05.75.082M19.8 15.3l-1.57.393A9.065 9.065 0 0112 15a9.065 9.065 0 00-6.23.693L5 14.5m14.8.8l1.402 1.402c1.232 1.232.65 3.318-1.067 3.611A48.309 48.309 0 0112 21c-2.773 0-5.491-.235-8.135-.687-1.718-.293-2.3-2.379-1.067-3.61L5 14.5"/></svg>',
                    'color' => 'amber',
                ],
                'animal-bite-center' => [
                    'icon' => '<svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v3.75m0-10.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285zm0 13.036h.008v.008H12v-.008z"/></svg>',
                    'color' => 'red',
                ],
            ];
        @endphp

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-7">
            @foreach($units as $index => $unit)
                @php
                    $meta = $unitIcons[$unit['slug']] ?? ['icon' => '<svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>', 'color' => 'green'];
                    $c = $meta['color'];
                @endphp
                <div class="bg-white dark:bg-gray-800 rounded-2xl p-7 sm:p-8 shadow-sm border border-gray-100 dark:border-gray-700 flex flex-col items-center text-center group card-hover"
                     data-reveal style="transition-delay: {{ $index * 80 }}ms">
                    
                    {{-- Icon circle --}}
                    <div class="h-18 w-18 bg-{{ $c }}-50 dark:bg-{{ $c }}-900/30 text-{{ $c }}-600 dark:text-{{ $c }}-400 rounded-2xl flex items-center justify-center mb-5 shadow-sm group-hover:bg-{{ $c }}-600 group-hover:text-white dark:group-hover:bg-{{ $c }}-600 transition-colors duration-300">
                        {!! $meta['icon'] !!}
                    </div>

                    <h3 class="font-display text-xl font-bold text-gray-900 dark:text-white mb-2 group-hover:text-{{ $c }}-700 dark:group-hover:text-{{ $c }}-400 transition-colors">{{ $unit['name'] }}</h3>
                    <p class="text-gray-500 dark:text-gray-400 text-sm leading-relaxed mb-6 flex-grow">{{ $unit['desc'] }}</p>
                    
                    <a href="{{ route('units.show', $unit['slug']) }}" 
                       class="w-full inline-flex justify-center items-center gap-2 px-6 py-3 text-sm font-semibold rounded-xl text-{{ $c }}-700 dark:text-{{ $c }}-400 bg-{{ $c }}-50 dark:bg-{{ $c }}-900/20 hover:bg-{{ $c }}-600 hover:text-white dark:hover:bg-{{ $c }}-600 dark:hover:text-white transition-all duration-200 min-h-[48px] border border-{{ $c }}-100 dark:border-{{ $c }}-800/30 hover:border-transparent group/btn">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                        View Guide & Details
                    </a>
                </div>
            @endforeach
        </div>

    </div>
</div>
@endsection
