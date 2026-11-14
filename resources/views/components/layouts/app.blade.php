@props(['title' => null, 'heading' => null])
@php
    $user = auth()->user();
    $admin = $user->isAdmin();

    $links = $admin
        ? [
            ['route' => 'admin.dashboard', 'match' => 'admin.dashboard', 'label' => 'Overview'],
            ['route' => 'admin.shipments.index', 'match' => 'admin.shipments.*', 'label' => 'Shipments'],
            ['route' => 'admin.users.index', 'match' => 'admin.users.*', 'label' => 'Clients'],
            ['route' => 'admin.website.hub', 'match' => 'admin.website.*', 'label' => 'Website'],
            ['route' => 'track', 'match' => 'track', 'label' => 'Track'],
        ]
        : [
            ['route' => 'dashboard', 'match' => 'dashboard', 'label' => 'Dashboard'],
            ['route' => 'shipments.index', 'match' => 'shipments.*', 'label' => 'My shipments'],
            ['route' => 'track', 'match' => 'track', 'label' => 'Track'],
            ['route' => 'profile.edit', 'match' => 'profile.*', 'label' => 'Profile'],
        ];

    $unreadInquiries = $admin ? Cms::guard(fn () => \App\Models\Inquiry::whereNull('read_at')->count(), 0) : 0;
    $pendingApprovals = $admin ? \App\Models\User::clients()->where('status', \App\Models\User::STATUS_PENDING)->count() : 0;
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>@include('partials.head', ['title' => $title])</head>
<body class="flex min-h-screen flex-col">

<header class="bg-ink-900 text-white">
    <div class="mx-auto flex max-w-7xl items-center justify-between px-5 py-3.5 sm:px-8">
        <div class="flex items-center gap-10">
            <a href="{{ $admin ? route('admin.dashboard') : route('dashboard') }}" aria-label="Global Shipping Alliance" class="flex items-center">
                <img src="{{ asset('images/logo-compact-light.svg') }}" alt="Global Shipping Alliance" class="h-9 w-auto sm:hidden">
                <img src="{{ asset('images/logo-light.svg') }}" alt="Global Shipping Alliance" class="hidden h-11 w-auto sm:block">
            </a>
            <nav class="hidden items-center gap-1 md:flex" aria-label="Main">
                @foreach ($links as $link)
                    <a href="{{ route($link['route']) }}"
                       class="rounded-lg px-3.5 py-2 text-sm font-medium transition {{ request()->routeIs($link['match']) ? 'bg-ink-700 text-white' : 'text-ink-200 hover:bg-ink-800 hover:text-white' }}">
                        {{ $link['label'] }}
                        @if ($admin && $link['route'] === 'admin.users.index' && $pendingApprovals > 0)
                            <span class="ml-1.5 inline-flex min-w-5 justify-center rounded-full bg-beacon-500 px-1.5 text-xs font-bold text-ink-950">{{ $pendingApprovals }}</span>
                        @endif
                        @if ($admin && $link['route'] === 'admin.website.hub' && $unreadInquiries > 0)
                            <span class="ml-1.5 inline-flex min-w-5 justify-center rounded-full bg-beacon-500 px-1.5 text-xs font-bold text-ink-950">{{ $unreadInquiries }}</span>
                        @endif
                    </a>
                @endforeach
            </nav>
        </div>

        <div class="flex items-center gap-3">
            <div class="hidden text-right leading-tight sm:block">
                <p class="text-sm font-semibold">{{ $user->name }}</p>
                <p class="text-xs text-ink-300">{{ $admin ? 'Super admin' : '@' . $user->username }}</p>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="rounded-lg border border-ink-600 px-3 py-1.5 text-xs font-semibold text-ink-100 hover:bg-ink-800">Sign out</button>
            </form>
            <button type="button" id="menu-toggle" class="rounded-lg p-2 hover:bg-ink-800 md:hidden" aria-label="Open menu" aria-expanded="false" aria-controls="mobile-menu">
                <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M4 7h16M4 12h16M4 17h16"/></svg>
            </button>
        </div>
    </div>

    <nav id="mobile-menu" class="hidden border-t border-ink-800 px-5 py-3 md:hidden" aria-label="Mobile">
        @foreach ($links as $link)
            <a href="{{ route($link['route']) }}" class="block rounded-lg px-3 py-2.5 text-sm font-medium {{ request()->routeIs($link['match']) ? 'bg-ink-700 text-white' : 'text-ink-200' }}">{{ $link['label'] }}</a>
        @endforeach
    </nav>
</header>

@if ($heading)
    <div class="border-b border-ink-100 bg-white">
        <div class="mx-auto flex max-w-7xl flex-wrap items-center justify-between gap-4 px-5 py-6 sm:px-8">
            <h1 class="font-display text-2xl font-extrabold text-ink-900 sm:text-3xl">{{ $heading }}</h1>
            @isset($actions)<div class="flex flex-wrap items-center gap-3">{{ $actions }}</div>@endisset
        </div>
    </div>
@endif

<main class="mx-auto w-full max-w-7xl flex-1 px-5 py-8 sm:px-8">
    <x-flash />
    {{ $slot }}
</main>

<x-site-footer />

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
