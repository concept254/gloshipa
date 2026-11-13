@props(['eyebrow' => null, 'title', 'text' => null, 'center' => false, 'dark' => false])
<div {{ $attributes->class(['max-w-2xl', 'mx-auto text-center' => $center]) }}>
    @if ($eyebrow)<p class="eyebrow {{ $dark ? 'text-tide-200!' : '' }}">{{ $eyebrow }}</p>@endif
    <h2 class="mt-3 font-display text-3xl font-extrabold sm:text-4xl {{ $dark ? 'text-white' : 'text-ink-900' }}">{{ $title }}</h2>
    @if ($text)<p class="mt-4 leading-relaxed {{ $dark ? 'text-ink-200' : 'text-ink-500' }}">{{ $text }}</p>@endif
</div>
