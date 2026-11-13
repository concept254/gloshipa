{{-- Inner page banner: header image, title, subtitle and breadcrumbs.
     $crumbs = [['Label', url|null], ...]  (Home is added automatically) --}}
@props(['title', 'subtitle' => null, 'image' => null, 'crumbs' => []])
@php $src = Cms::headerImage($image, \Illuminate\Support\Str::slug($title)); @endphp
<section class="relative isolate overflow-hidden bg-ink-900 text-white">
    <img src="{{ $src }}" alt="" class="absolute inset-0 -z-20 h-full w-full object-cover" loading="eager"
         onerror="this.onerror=null;this.src='https://picsum.photos/seed/{{ rawurlencode(\Illuminate\Support\Str::slug($title)) }}/1600/600'">
    <div class="absolute inset-0 -z-10 bg-linear-to-r from-ink-950/90 via-ink-900/75 to-ink-900/40"></div>
    <div class="container-stripes absolute inset-0 -z-10"></div>
    <div class="mx-auto max-w-7xl px-5 py-14 sm:px-8 sm:py-20">
        <nav aria-label="Breadcrumb" class="text-sm text-ink-200">
            <ol class="flex flex-wrap items-center gap-x-2 gap-y-1">
                <li><a href="{{ route('home') }}" class="hover:text-white">Home</a></li>
                @foreach ($crumbs as [$label, $url])
                    <li aria-hidden="true" class="text-ink-400">/</li>
                    <li>@if ($url)<a href="{{ $url }}" class="hover:text-white">{{ $label }}</a>@else<span class="text-white" aria-current="page">{{ $label }}</span>@endif</li>
                @endforeach
            </ol>
        </nav>
        <h1 class="mt-4 max-w-3xl font-display text-4xl font-extrabold leading-tight sm:text-5xl">{{ $title }}</h1>
        @if ($subtitle)<p class="mt-4 max-w-2xl text-lg leading-relaxed text-ink-100">{{ $subtitle }}</p>@endif
    </div>
</section>
