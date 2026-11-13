<a href="{{ route('site.post', $post->slug) }}" class="card group flex h-full flex-col overflow-hidden transition hover:-translate-y-1 hover:shadow-lift">
    <img src="{{ $post->image ? Cms::image($post->image) : asset('images/headers/header-' . (($post->id % 6) + 1) . '.svg') }}" alt="" class="h-48 w-full object-cover" loading="lazy">
    <div class="flex flex-1 flex-col p-6">
        <time class="text-xs font-semibold uppercase tracking-wider text-tide-600">{{ optional($post->published_at ?? $post->created_at)->format('j M Y') }}</time>
        <h3 class="mt-2 font-display text-xl font-bold text-ink-900 group-hover:text-tide-700">{{ $post->title }}</h3>
        <p class="mt-2 flex-1 text-sm leading-relaxed text-ink-500">{{ $post->excerpt }}</p>
    </div>
</a>
