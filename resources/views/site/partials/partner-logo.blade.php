@php $tag = $p->url ? 'a' : 'div'; @endphp
<{{ $tag }} @if ($p->url) href="{{ $p->url }}" target="_blank" rel="noopener noreferrer" @endif class="flex h-24 items-center justify-center rounded-2xl border border-ink-100 bg-white px-6 grayscale transition hover:grayscale-0">
    @if ($p->logo)<img src="{{ Cms::image($p->logo) }}" alt="{{ $p->name }}" class="max-h-14 w-auto object-contain" loading="lazy">
    @else<span class="font-display text-lg font-bold text-ink-500">{{ $p->name }}</span>@endif
</{{ $tag }}>
