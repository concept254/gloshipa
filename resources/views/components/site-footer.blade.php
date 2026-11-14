{{-- Footer used by every layout: public site, sign-in screens, client dashboard and admin. --}}
@props(['newsletter' => false])
@php
    $services = Cms::guard(fn () => \App\Models\Service::active()->limit(6)->get(['title', 'slug']), collect());
    $pages = Cms::footerPages();
    if (! $pages) {
        $pages = [['label' => 'About us', 'url' => route('site.about')], ['label' => 'FAQs', 'url' => route('site.faqs')], ['label' => 'Contact', 'url' => route('site.contact')]];
    }
    $socials = Cms::socials();
@endphp

@if ($newsletter)
    <section class="bg-beacon-100">
        <div class="mx-auto grid max-w-7xl items-center gap-6 px-5 py-12 sm:px-8 lg:grid-cols-2">
            <div>
                <h2 class="font-display text-2xl font-extrabold text-ink-900 sm:text-3xl">Shipping news, straight to your inbox.</h2>
                <p class="mt-2 text-ink-600">Port updates, rate changes and tips for importers and exporters. No spam, unsubscribe any time.</p>
            </div>
            <x-newsletter-form />
        </div>
    </section>
@endif

<footer class="bg-ink-950 text-ink-200">
    <div class="mx-auto grid max-w-7xl gap-10 px-5 py-14 sm:px-8 sm:grid-cols-2 lg:grid-cols-4">
        <div>
            <a href="{{ route('home') }}" aria-label="Global Shipping Alliance home" class="inline-block">
                <img src="{{ asset('images/logo-light.svg') }}" alt="Global Shipping Alliance" class="h-14 w-auto">
            </a>
            <p class="mt-4 max-w-xs text-sm leading-relaxed text-ink-300">{{ Cms::setting('footer_about', 'Clear, reliable tracking for the cargo you move, from the port of origin to your door.') }}</p>
            @if ($socials)
                <ul class="mt-5 flex flex-wrap gap-2 text-xs">
                    @foreach ($socials as $label => $url)
                        <li><a href="{{ $url }}" target="_blank" rel="noopener noreferrer" class="rounded-full border border-ink-700 px-3 py-1.5 hover:bg-ink-800 hover:text-white">{{ $label }}</a></li>
                    @endforeach
                </ul>
            @endif
        </div>

        <nav class="text-sm" aria-label="Company">
            <h2 class="font-display text-base font-bold text-white">Company</h2>
            <ul class="mt-4 space-y-2">
                @foreach ($pages as $p)<li><a class="hover:text-white" href="{{ $p['url'] }}">{{ $p['label'] }}</a></li>@endforeach
                <li><a class="hover:text-white" href="{{ route('site.news') }}">News</a></li>
            </ul>
        </nav>

        <nav class="text-sm" aria-label="Services">
            <h2 class="font-display text-base font-bold text-white">Services</h2>
            <ul class="mt-4 space-y-2">
                @forelse ($services as $s)
                    <li><a class="hover:text-white" href="{{ route('site.service', $s->slug) }}">{{ $s->title }}</a></li>
                @empty
                    <li><a class="hover:text-white" href="{{ route('site.services') }}">All services</a></li>
                @endforelse
                <li><a class="hover:text-white" href="{{ route('site.quote') }}">Request a quote</a></li>
            </ul>
        </nav>

        <div class="text-sm">
            <h2 class="font-display text-base font-bold text-white">Contact</h2>
            <ul class="mt-4 space-y-2">
                <li><a class="hover:text-white" href="mailto:{{ Cms::email() }}">{{ Cms::email() }}</a></li>
                <li>{{ Cms::phone() }}</li>
                <li class="whitespace-pre-line">{{ Cms::address() }}</li>
                @if ($h = Cms::setting('business_hours'))<li class="text-ink-400">{{ $h }}</li>@endif
            </ul>
            <h2 class="mt-6 font-display text-base font-bold text-white">Client area</h2>
            <ul class="mt-3 space-y-2">
                @auth
                    <li><a class="hover:text-white" href="{{ route('dashboard') }}">My dashboard</a></li>
                @else
                    <li><a class="hover:text-white" href="{{ route('login') }}">Sign in</a></li>
                    <li><a class="hover:text-white" href="{{ route('register') }}">Request an account</a></li>
                @endauth
                <li><a class="hover:text-white" href="{{ route('track') }}">Track a shipment</a></li>
            </ul>
        </div>
    </div>
    <div class="border-t border-ink-800 py-5 text-center text-xs text-ink-400">&copy; {{ date('Y') }} Glo Shipa. All rights reserved.</div>
</footer>

<x-site-chrome />
