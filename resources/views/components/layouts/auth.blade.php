@props(['title' => null, 'heading' => null, 'subheading' => null])
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>@include('partials.head', ['title' => $title])</head>
<body class="min-h-screen">
<div class="grid min-h-screen lg:grid-cols-[minmax(0,5fr)_minmax(0,6fr)]">

    {{-- Brand panel --}}
    <aside class="container-stripes relative hidden overflow-hidden bg-ink-900 lg:block">
        <img src="{{ asset('images/hero-ship.svg') }}" alt="" class="absolute inset-0 h-full w-full object-cover opacity-30">
        <div class="absolute inset-0 bg-linear-to-t from-ink-950 via-ink-900/70 to-ink-900/30"></div>
        <div class="relative flex h-full flex-col justify-between p-12">
            {{-- Dark brand panel (large screens) --}}
            <a href="{{ route('home') }}"><img src="{{ asset('images/logo-light.svg') }}" alt="Global Shipping Alliance" class="h-14 w-auto"></a>
            <div>
                <p class="font-display text-4xl font-extrabold leading-tight text-white">Every container,<br>every port,<br><span class="text-beacon-400">one clear view.</span></p>
                <p class="mt-5 max-w-sm text-sm leading-relaxed text-ink-200">Follow your shipments, update your details and get an email whenever your cargo moves.</p>
            </div>
        </div>
    </aside>

    <section class="flex items-center justify-center px-5 py-12 sm:px-8">
        <div class="w-full max-w-md">
            {{-- Light, above the form (phones) --}}
            <a href="{{ route('home') }}" class="mb-10 inline-block lg:hidden"><img src="{{ asset('images/logo.svg') }}" alt="Global Shipping Alliance" class="h-12 w-auto"></a>
            @if ($heading)
                <h1 class="font-display text-3xl font-extrabold text-ink-900">{{ $heading }}</h1>
            @endif
            @if ($subheading)
                <p class="mt-2 text-sm text-ink-500">{{ $subheading }}</p>
            @endif
            <div class="mt-8">
                <x-flash />
                {{ $slot }}
            </div>
        </div>
    </section>
</div>

<x-site-footer />
</body>
</html>
