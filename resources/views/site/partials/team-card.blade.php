<article class="card h-full overflow-hidden text-center">
    @if ($m->photo)
        <img src="{{ Cms::image($m->photo) }}" alt="{{ $m->name }}" class="aspect-square w-full object-cover" loading="lazy">
    @else
        <div class="container-stripes flex aspect-square w-full items-center justify-center bg-ink-900 font-display text-6xl font-extrabold text-beacon-500">{{ mb_strtoupper(mb_substr($m->name, 0, 1)) }}</div>
    @endif
    <div class="p-6">
        <h3 class="font-display text-lg font-bold text-ink-900">{{ $m->name }}</h3>
        <p class="text-sm font-medium text-tide-700">{{ $m->role }}</p>
        @if ($m->bio)<p class="mt-3 text-sm leading-relaxed text-ink-500">{{ $m->bio }}</p>@endif
        @if ($m->linkedin_url)<a href="{{ $m->linkedin_url }}" target="_blank" rel="noopener noreferrer" class="mt-3 inline-block text-xs font-semibold text-ink-900 underline decoration-beacon-500 decoration-2 underline-offset-4">LinkedIn</a>@endif
    </div>
</article>
