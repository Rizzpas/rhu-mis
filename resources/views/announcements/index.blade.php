@extends('layouts.app')

@section('content')
<div class="bg-[#FAF9F6] dark:bg-gray-900 min-h-screen">
    <div class="max-w-7xl mx-auto px-5 sm:px-8 py-12 sm:py-16">

        {{-- Back link --}}
        <a href="{{ route('welcome') }}" class="inline-flex items-center text-sm font-medium text-green-700 dark:text-green-400 hover:text-green-800 transition mb-8">
            <svg class="mr-2 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Back to Home
        </a>

        {{-- Header --}}
        <div class="text-center mb-12" data-reveal>
            <h1 class="font-display text-3xl sm:text-4xl font-extrabold text-gray-900 dark:text-white tracking-tight">{{ __('All Announcements') }}</h1>
            <p class="text-gray-500 dark:text-gray-400 mt-3 text-lg max-w-lg mx-auto">{{ __('Browse all updates and events from your health unit') }}</p>
            <div class="section-accent mx-auto mt-5"></div>
        </div>

        {{-- Grid --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 sm:gap-7">
            @foreach($announcements as $index => $event)
                @php
                    $cardImage = $event->image_path;
                    if (!$cardImage && $event->images->count() > 0) {
                        $cardImage = $event->images->first()->image_path;
                    }
                    // Category inference
                    $text = strtolower($event->title . ' ' . strip_tags($event->content));
                    if (preg_match('/health alert|outbreak|dengue|covid|virus|disease|warning/i', $text)) {
                        $tag = ['label' => 'Health Alert', 'class' => 'tag-health-alert'];
                    } elseif (preg_match('/event|celebration|program|fiesta|activity|campaign|drive/i', $text)) {
                        $tag = ['label' => 'Event', 'class' => 'tag-event'];
                    } elseif (preg_match('/advisory|notice|schedule|closure|suspend|update|memo/i', $text)) {
                        $tag = ['label' => 'Advisory', 'class' => 'tag-advisory'];
                    } else {
                        $tag = ['label' => 'Announcement', 'class' => 'tag-general'];
                    }
                @endphp
                <div data-reveal style="transition-delay: {{ ($index % 6) * 80 }}ms">
                    <a href="{{ route('announcements.show', $event) }}"
                       class="block bg-white dark:bg-gray-800 rounded-2xl overflow-hidden border border-gray-100 dark:border-gray-700/50 card-hover group h-full">
                        <div class="relative overflow-hidden" style="aspect-ratio: 16/9;">
                            @if($cardImage)
                                @if(\Illuminate\Support\Str::endsWith($cardImage, '.mp4'))
                                    <video src="{{ asset('uploads/' . $cardImage) }}" class="w-full h-full object-cover" muted loop autoplay></video>
                                @else
                                    <img src="{{ asset('uploads/' . $cardImage) }}" alt="{{ $event->title }}"
                                         class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105" loading="lazy">
                                @endif
                            @else
                                <div class="w-full h-full bg-gradient-to-br from-green-50 to-teal-50 dark:from-green-900/20 dark:to-teal-900/20 flex items-center justify-center">
                                    <svg class="w-12 h-12 text-green-200 dark:text-green-800" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                            @endif
                            <div class="absolute top-3 left-3">
                                <span class="inline-block px-2.5 py-1 {{ $tag['class'] }} text-[10px] font-bold rounded-lg uppercase tracking-wide backdrop-blur-sm">{{ $tag['label'] }}</span>
                            </div>
                        </div>
                        <div class="p-5 sm:p-6 flex flex-col flex-1">
                            <div class="flex items-center gap-2 text-xs text-gray-400 dark:text-gray-500 mb-2">
                                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm.75-13a.75.75 0 00-1.5 0v5c0 .414.336.75.75.75h4a.75.75 0 000-1.5h-3.25V5z" clip-rule="evenodd"/></svg>
                                {{ $event->created_at->diffForHumans() }}
                            </div>
                            <h3 class="font-semibold text-lg text-gray-900 dark:text-white mb-2 group-hover:text-green-700 dark:group-hover:text-green-400 transition-colors line-clamp-2">
                                {{ $event->title }}
                            </h3>
                            <p class="text-gray-500 dark:text-gray-400 text-sm line-clamp-3 leading-relaxed">
                                {{ Str::limit(strip_tags($event->content), 120) }}
                            </p>
                            <div class="mt-auto pt-4">
                                <span class="inline-flex items-center gap-1.5 text-green-600 dark:text-green-400 font-semibold text-sm group-hover:gap-2.5 transition-all">
                                    {{ __('Read details') }}
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                                </span>
                            </div>
                        </div>
                    </a>
                </div>
            @endforeach
        </div>

        {{-- Pagination --}}
        <div class="mt-12">
            {{ $announcements->links() }}
        </div>
    </div>
</div>
@endsection
