@props(['title' => null])
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>@include('partials.head', ['title' => $title])</head>
<body class="min-h-screen">

<header class="absolute inset-x-0 top-0 z-20">
    <div class="mx-auto flex max-w-7xl items-center justify-between px-5 py-5 sm:px-8">
        <a href="{{ route('home') }}" aria-label="Glo Shipa home"><x-logo /></a>

        <nav class="hidden items-center gap-8 text-sm font-medium text-ink-700 md:flex" aria-label="Main">
            <a href="{{ route('home') }}#how" class="hover:text-ink-950">How it works</a>
            <a href="{{ route('home') }}#why" class="hover:text-ink-950">Why Glo Shipa</a>
            <a href="{{ route('home') }}#contact" class="hover:text-ink-950">Contact</a>
        </nav>

        <div class="flex items-center gap-3">
            @auth
                <a href="{{ route('dashboard') }}" class="btn btn-dark btn-sm">My dashboard</a>
            @else
                <a href="{{ route('login') }}" class="hidden text-sm font-semibold text-ink-800 hover:text-ink-950 sm:inline">Sign in</a>
                <a href="{{ route('register') }}" class="btn btn-dark btn-sm">Register</a>
            @endauth
        </div>
    </div>
</header>

<main>{{ $slot }}</main>

<footer id="contact" class="bg-ink-950 text-ink-200">
    <div class="mx-auto grid max-w-7xl gap-10 px-5 py-14 sm:px-8 md:grid-cols-3">
        <div>
            <x-logo :dark="true" />
            <p class="mt-4 max-w-xs text-sm leading-relaxed text-ink-300">Clear, reliable tracking for the cargo you move, from the port of origin to your door.</p>
        </div>
        <div class="text-sm">
            <h2 class="font-display text-base font-bold text-white">Contact</h2>
            <ul class="mt-4 space-y-2">
                <li><a class="hover:text-white" href="mailto:{{ config('gloshipa.contact.email') }}">{{ config('gloshipa.contact.email') }}</a></li>
                <li>{{ config('gloshipa.contact.phone') }}</li>
                <li>{{ config('gloshipa.contact.address') }}</li>
            </ul>
        </div>
        <div class="text-sm">
            <h2 class="font-display text-base font-bold text-white">Clients</h2>
            <ul class="mt-4 space-y-2">
                <li><a class="hover:text-white" href="{{ route('login') }}">Sign in</a></li>
                <li><a class="hover:text-white" href="{{ route('register') }}">Request an account</a></li>
            </ul>
        </div>
    </div>
    <div class="border-t border-ink-800 py-5 text-center text-xs text-ink-400">&copy; {{ date('Y') }} Glo Shipa. All rights reserved.</div>
</footer>

</body>
</html>
