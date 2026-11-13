<a href="{{ route('site.service', $service->slug) }}" class="card group flex h-full flex-col overflow-hidden transition hover:-translate-y-1 hover:shadow-lift">
    @if ($service->image)
        <img src="{{ Cms::image($service->image) }}" alt="" class="h-44 w-full object-cover" loading="lazy">
    @endif
    <div class="flex flex-1 flex-col p-7">
        <span class="flex h-12 w-12 items-center justify-center rounded-xl bg-tide-50 text-tide-600 transition group-hover:bg-beacon-500 group-hover:text-ink-950"><x-icon :name="$service->icon" /></span>
        <h3 class="mt-5 font-display text-xl font-bold text-ink-900">{{ $service->title }}</h3>
        <p class="mt-2 flex-1 text-sm leading-relaxed text-ink-500">{{ $service->summary }}</p>
        <span class="mt-5 text-sm font-semibold text-ink-900 underline decoration-beacon-500 decoration-2 underline-offset-4">Learn more</span>
    </div>
</a>
