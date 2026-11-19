@props(['title' => null, 'description' => null])
@php
    $menu = Cms::menu();
    $current = request()->path();
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    @include('partials.head', ['title' => $title])
    @if ($description)<meta name="description" content="{{ $description }}">@endif
    <link rel="canonical" href="{{ url()->current() }}">
</head>
<body class="flex min-h-screen flex-col">

<header class="sticky top-0 z-30 border-b border-ink-100 bg-sand-50/90 backdrop-blur">
    <div class="mx-auto flex max-w-7xl items-center justify-between px-5 py-3.5 sm:px-8">
        <a href="{{ route('home') }}" aria-label="Global Shipping Alliance home"><x-logo /></a>

        <nav class="hidden items-center gap-1 text-sm font-medium text-ink-700 lg:flex" aria-label="Main">
            <a href="{{ route('home') }}" class="rounded-lg px-3 py-2 hover:bg-sand-100 hover:text-ink-950 {{ request()->routeIs('home') ? 'text-ink-950' : '' }}">Home</a>
            @foreach ($menu as $item)
                <a href="{{ $item['url'] }}" class="rounded-lg px-3 py-2 hover:bg-sand-100 hover:text-ink-950 {{ rtrim(parse_url($item['url'], PHP_URL_PATH) ?: '', '/') === '/' . $current ? 'text-ink-950 underline decoration-beacon-500 decoration-2 underline-offset-8' : '' }}">{{ $item['label'] }}</a>
            @endforeach
            <a href="{{ route('track') }}" class="rounded-lg px-3 py-2 hover:bg-sand-100 hover:text-ink-950">Track</a>
        </nav>

        <div class="flex items-center gap-3">
            @auth
                <a href="{{ route('dashboard') }}" class="btn btn-dark btn-sm">My dashboard</a>
            @else
                <a href="{{ route('login') }}" class="hidden text-sm font-semibold text-ink-800 hover:text-ink-950 sm:inline">Sign in</a>
                <a href="{{ route('register') }}" class="btn btn-dark btn-sm">Register</a>
            @endauth
            <button type="button" id="menu-toggle" class="rounded-lg p-2 hover:bg-sand-100 lg:hidden" aria-label="Open menu" aria-expanded="false" aria-controls="mobile-menu">
                <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M4 7h16M4 12h16M4 17h16"/></svg>
            </button>
        </div>
    </div>
    <nav id="mobile-menu" class="hidden border-t border-ink-100 bg-sand-50 px-5 py-3 lg:hidden" aria-label="Mobile">
        <a href="{{ route('home') }}" class="block rounded-lg px-3 py-2.5 text-sm font-medium text-ink-800">Home</a>
        @foreach ($menu as $item)
            <a href="{{ $item['url'] }}" class="block rounded-lg px-3 py-2.5 text-sm font-medium text-ink-800">{{ $item['label'] }}</a>
        @endforeach
        <a href="{{ route('track') }}" class="block rounded-lg px-3 py-2.5 text-sm font-medium text-ink-800">Track</a>
        @guest<a href="{{ route('login') }}" class="block rounded-lg px-3 py-2.5 text-sm font-medium text-ink-800">Sign in</a>@endguest
    </nav>
</header>

<main class="flex-1">
    @if (session('status') || session('warning'))
        <div class="mx-auto max-w-7xl px-5 pt-6 sm:px-8"><x-flash /></div>
    @endif
    {{ $slot }}
</main>

<x-site-footer :newsletter="true" />

<script>
    (function () {
        var btn = document.getElementById('menu-toggle'), menu = document.getElementById('mobile-menu');
        if (!btn || !menu) return;
        btn.addEventListener('click', function () {
            var open = menu.classList.toggle('hidden') === false;
            btn.setAttribute('aria-expanded', open ? 'true' : 'false');
        });
    })();
</script>
</body>
</html>
