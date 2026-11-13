{{-- Scroll-snap carousel with arrows and optional autoplay. Wrap each slide in <div data-slide>. --}}
@props(['label' => 'Carousel', 'autoplay' => true, 'per' => 3])
@php
    $basis = match ((int) $per) {
        1 => 'w-full',
        2 => 'w-[88%] sm:w-[calc(50%-0.75rem)]',
        4 => 'w-[70%] sm:w-[calc(33.333%-1rem)] lg:w-[calc(25%-1.125rem)]',
        5 => 'w-[46%] sm:w-[calc(33.333%-1rem)] lg:w-[calc(20%-1.2rem)]',
        default => 'w-[88%] sm:w-[calc(50%-0.75rem)] lg:w-[calc(33.333%-1rem)]',
    };
@endphp
<div data-carousel data-autoplay="{{ $autoplay ? '1' : '0' }}" role="region" aria-roledescription="carousel" aria-label="{{ $label }}" class="relative">
    <div data-track class="no-scrollbar flex snap-x snap-mandatory gap-6 overflow-x-auto scroll-smooth pb-2">
        {{ $slot }}
    </div>
    <div class="mt-6 flex items-center justify-end gap-2">
        <button type="button" data-prev class="flex h-10 w-10 items-center justify-center rounded-full border border-ink-200 bg-white text-ink-700 transition hover:bg-ink-900 hover:text-white" aria-label="Previous">
            <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M12.7 5.3a1 1 0 010 1.4L9.4 10l3.3 3.3a1 1 0 11-1.4 1.4l-4-4a1 1 0 010-1.4l4-4a1 1 0 011.4 0z" clip-rule="evenodd"/></svg>
        </button>
        <button type="button" data-next class="flex h-10 w-10 items-center justify-center rounded-full border border-ink-200 bg-white text-ink-700 transition hover:bg-ink-900 hover:text-white" aria-label="Next">
            <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M7.3 14.7a1 1 0 010-1.4L10.6 10 7.3 6.7a1 1 0 111.4-1.4l4 4a1 1 0 010 1.4l-4 4a1 1 0 01-1.4 0z" clip-rule="evenodd"/></svg>
        </button>
    </div>
    <style>[data-carousel] [data-slide]{flex:0 0 auto}</style>
</div>
