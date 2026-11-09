@props(['dark' => false])
<span {{ $attributes->class(['inline-flex items-center gap-2.5']) }}>
    <svg viewBox="0 0 64 64" class="h-9 w-9" aria-hidden="true">
        <rect width="64" height="64" rx="14" class="{{ $dark ? 'fill-white' : 'fill-ink-900' }}"/>
        <path d="M44 22a16 16 0 1 0 2 14h-14" fill="none" class="stroke-beacon-500" stroke-width="6" stroke-linecap="round"/>
        <path d="M12 50h40" class="stroke-tide-500" stroke-width="4" stroke-linecap="round"/>
    </svg>
    <span class="font-display text-xl font-extrabold tracking-tight {{ $dark ? 'text-white' : 'text-ink-900' }}">Glo<span class="text-beacon-500"> Shipa</span></span>
</span>
