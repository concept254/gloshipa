<x-layouts.public title="Track your cargo" description="Freight forwarding and live shipment tracking from Global Shipping Alliance.">

    {{-- HERO --}}
    <section class="relative overflow-hidden bg-sand-100">
        <div class="mx-auto grid max-w-7xl items-center gap-12 px-5 pb-20 pt-14 sm:px-8 lg:grid-cols-[minmax(0,1.05fr)_minmax(0,1fr)] lg:pb-28 lg:pt-20">

            <div>
                <p class="eyebrow">{{ Cms::setting('hero_eyebrow', 'Cargo tracking for importers & exporters') }}</p>
                <h1 class="mt-4 font-display text-5xl font-extrabold leading-[1.02] text-ink-900 sm:text-6xl">
                    {{ rtrim(Cms::setting('hero_title', 'Know exactly where your cargo is'), '.') }}<span class="text-beacon-500">.</span>
                </h1>
                <p class="mt-6 max-w-xl text-lg leading-relaxed text-ink-600">
                    {{ Cms::setting('hero_text', 'Follow every container from port to doorstep. Sign in to see the live status, location and delivery estimate of every shipment Global Shipping Alliance is handling for you.') }}
                </p>

                <div class="mt-9 max-w-xl rounded-2xl bg-white p-3 shadow-lift ring-1 ring-ink-100">
                    <x-search-form :big="true" />
                </div>
                <p class="mt-4 text-sm text-ink-500">
                    @auth
                        Enter a tracking ID such as <span class="font-semibold text-ink-700">GS4K9X2M7QPA</span> or a container number such as <span class="font-semibold text-ink-700">MSKU1234567</span>.
                    @else
                        You will be asked to sign in first. New client?
                        <a href="{{ route('register') }}" class="font-semibold text-ink-900 underline decoration-beacon-500 decoration-2 underline-offset-4 hover:text-beacon-700">Request an account</a>.
                    @endauth
                </p>
            </div>

            <div class="relative">
                <div class="absolute -inset-4 rotate-2 rounded-[2rem] bg-beacon-200/60" aria-hidden="true"></div>
                <div class="relative overflow-hidden rounded-[1.75rem] bg-ink-900 shadow-lift ring-1 ring-ink-900/10">
                    <img src="{{ $heroImage }}" alt="Container shipping and cargo logistics" class="aspect-[4/3] w-full object-cover" width="960" height="720">
                </div>

                <div class="absolute -bottom-6 left-4 hidden max-w-[260px] rounded-2xl bg-white p-4 shadow-lift ring-1 ring-ink-100 sm:block lg:-left-8">
                    <p class="text-[11px] font-semibold uppercase tracking-wider text-ink-400">Example update</p>
                    <p class="mt-1 font-display text-base font-bold text-ink-900">Arrived at Durban port</p>
                    <p class="mt-0.5 text-xs text-ink-500">Next: customs clearance</p>
                    <div class="mt-3 flex gap-1" aria-hidden="true">
                        <span class="h-1.5 flex-1 rounded-full bg-tide-600"></span><span class="h-1.5 flex-1 rounded-full bg-tide-600"></span><span class="h-1.5 flex-1 rounded-full bg-beacon-500"></span><span class="h-1.5 flex-1 rounded-full bg-ink-100"></span><span class="h-1.5 flex-1 rounded-full bg-ink-100"></span>
                    </div>
                </div>
            </div>
        </div>
    </section>


    {{-- STATS (hidden until filled in under Website > Settings) --}}
    @php
        $stats = collect([1, 2, 3, 4])->map(fn ($i) => [Cms::setting("stat_{$i}_value"), Cms::setting("stat_{$i}_label")])->filter(fn ($s) => $s[0] && $s[1]);
    @endphp
    @if ($stats->isNotEmpty())
        <section class="container-stripes bg-ink-900 text-white">
            <dl class="mx-auto grid max-w-7xl grid-cols-2 gap-8 px-5 py-10 text-center sm:px-8 {{ $stats->count() >= 4 ? 'lg:grid-cols-4' : ($stats->count() === 3 ? 'lg:grid-cols-3' : 'lg:grid-cols-2') }}">
                @foreach ($stats as [$value, $label])
                    <div><dt class="font-display text-4xl font-extrabold text-beacon-400">{{ $value }}</dt><dd class="mt-1 text-sm text-ink-200">{{ $label }}</dd></div>
                @endforeach
            </dl>
        </section>
    @endif

    {{-- SERVICES --}}
    @if ($services->isNotEmpty())
        <section class="mx-auto max-w-7xl px-5 py-20 sm:px-8">
            <div class="flex flex-wrap items-end justify-between gap-4">
                <x-section-heading eyebrow="What we do" title="Freight solutions for every shipment" text="From a single pallet to a full container load, we plan the route, handle the paperwork and keep you updated." />
                <a href="{{ route('site.services') }}" class="btn btn-ghost">All services</a>
            </div>
            <div class="mt-10">
                <x-carousel label="Services">
                    @foreach ($services as $service)<div data-slide class="w-[88%] snap-start sm:w-[calc(50%-0.75rem)] lg:w-[calc(33.333%-1rem)]">@include('site.partials.service-card')</div>@endforeach
                </x-carousel>
            </div>
        </section>
    @endif

    {{-- ABOUT TEASER --}}
    @if ($about)
        <section class="bg-sand-100 py-20">
            <div class="mx-auto grid max-w-7xl items-center gap-12 px-5 sm:px-8 lg:grid-cols-2">
                <div class="overflow-hidden rounded-3xl shadow-lift">
                    <img src="{{ Cms::headerImage($about->header_image, 'about') }}" alt="" class="aspect-[4/3] w-full object-cover" loading="lazy">
                </div>
                <div>
                    <p class="eyebrow">About Global Shipping Alliance</p>
                    <h2 class="mt-3 font-display text-3xl font-extrabold text-ink-900 sm:text-4xl">{{ $about->subtitle ?: 'A freight partner you can follow every step of the way.' }}</h2>
                    <p class="mt-4 leading-relaxed text-ink-600">{{ \Illuminate\Support\Str::limit(strip_tags(Cms::markdown($about->body)), 320) }}</p>
                    <div class="mt-6 flex flex-wrap gap-3">
                        <a href="{{ route('site.about') }}" class="btn btn-dark">About us</a>
                        <a href="{{ route('site.team') }}" class="btn btn-ghost">Meet the team</a>
                    </div>
                </div>
            </div>
        </section>
    @endif

    {{-- WHY --}}
    <section id="why" class="mx-auto max-w-7xl px-5 py-20 sm:px-8">
        <div class="max-w-2xl">
            <p class="eyebrow">Why Global Shipping Alliance</p>
            <h2 class="mt-3 font-display text-3xl font-extrabold text-ink-900 sm:text-4xl">Built around the questions you ask every day.</h2>
        </div>

        <div class="mt-12 grid gap-6 md:grid-cols-3">
            @foreach ([
                ['One search, any reference', 'Type a tracking ID or a container number. Either one finds your shipment instantly.', 'M21 21l-4.3-4.3M17 10a7 7 0 11-14 0 7 7 0 0114 0z'],
                ['An email at every move', 'When we assign you a shipment or it moves to a new location, you hear about it straight away.', 'M3 8l9 6 9-6M5 5h14a2 2 0 012 2v10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2z'],
                ['Your own dashboard', 'See what is still on its way, review past deliveries and keep your contact details up to date.', 'M4 5h7v7H4zM13 5h7v4h-7zM13 11h7v8h-7zM4 14h7v5H4z'],
            ] as [$title, $text, $icon])
                <article class="card p-7">
                    <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-tide-50 text-tide-600">
                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="{{ $icon }}"/></svg>
                    </span>
                    <h3 class="mt-5 font-display text-xl font-bold text-ink-900">{{ $title }}</h3>
                    <p class="mt-2 text-sm leading-relaxed text-ink-500">{{ $text }}</p>
                </article>
            @endforeach
        </div>
    </section>

    {{-- HOW IT WORKS --}}
    <section id="how" class="container-stripes bg-ink-900 py-20 text-white">
        <div class="mx-auto max-w-7xl px-5 sm:px-8">
            <p class="eyebrow text-tide-200!">How it works</p>
            <h2 class="mt-3 max-w-2xl font-display text-3xl font-extrabold sm:text-4xl">From sign-up to delivery in three steps.</h2>

            <ol class="mt-12 grid gap-6 md:grid-cols-3">
                @foreach ([
                    ['Request an account', 'Register with your details. Our team reviews every request and activates your account once approved.'],
                    ['We assign your shipment', 'When your cargo is booked, we add it to your account and email you the tracking ID and container number.'],
                    ['Track and stay informed', 'Sign in to follow progress, and get an email each time your shipment changes location.'],
                ] as [$title, $text])
                    <li class="rounded-2xl border border-ink-700 bg-ink-800/60 p-7">
                        <span class="flex h-10 w-10 items-center justify-center rounded-full bg-beacon-500 font-display text-lg font-extrabold text-ink-950">{{ $loop->iteration }}</span>
                        <h3 class="mt-5 font-display text-xl font-bold">{{ $title }}</h3>
                        <p class="mt-2 text-sm leading-relaxed text-ink-200">{{ $text }}</p>
                    </li>
                @endforeach
            </ol>
        </div>
    </section>


    {{-- TESTIMONIALS --}}
    @if ($testimonials->isNotEmpty())
        <section class="bg-sand-100 py-20">
            <div class="mx-auto max-w-7xl px-5 sm:px-8">
                <x-section-heading eyebrow="Testimonials" title="Trusted by importers and exporters" center />
                <div class="mt-10">
                    <x-carousel label="Testimonials">
                        @foreach ($testimonials as $t)<div data-slide class="w-[88%] snap-start sm:w-[calc(50%-0.75rem)] lg:w-[calc(33.333%-1rem)]">@include('site.partials.testimonial-card')</div>@endforeach
                    </x-carousel>
                </div>
            </div>
        </section>
    @endif

    {{-- FAQ --}}
    @if ($faqs->isNotEmpty())
        <section class="mx-auto grid max-w-7xl gap-12 px-5 py-20 sm:px-8 lg:grid-cols-[minmax(0,2fr)_minmax(0,3fr)]">
            <div>
                <x-section-heading eyebrow="FAQs" title="Answers before you ask" text="The questions we hear most from new clients." />
                <a href="{{ route('site.faqs') }}" class="btn btn-ghost mt-6">All questions</a>
            </div>
            @include('site.partials.faq-list', ['faqs' => $faqs, 'open' => true])
        </section>
    @endif

    {{-- PARTNERS --}}
    @if ($partners->isNotEmpty())
        <section class="border-y border-ink-100 bg-white py-14">
            <div class="mx-auto max-w-7xl px-5 sm:px-8">
                <p class="text-center text-xs font-semibold uppercase tracking-[0.18em] text-ink-400">Our clients &amp; partners</p>
                <div class="mt-8">
                    <x-carousel label="Clients and partners" :per="5">
                        @foreach ($partners as $p)<div data-slide class="w-[46%] snap-start sm:w-[calc(33.333%-1rem)] lg:w-[calc(20%-1.2rem)]">@include('site.partials.partner-logo')</div>@endforeach
                    </x-carousel>
                </div>
            </div>
        </section>
    @endif

    {{-- NEWS --}}
    @if ($posts->isNotEmpty())
        <section class="mx-auto max-w-7xl px-5 py-20 sm:px-8">
            <div class="flex flex-wrap items-end justify-between gap-4">
                <x-section-heading eyebrow="News" title="Latest from Global Shipping Alliance" />
                <a href="{{ route('site.news') }}" class="btn btn-ghost">All news</a>
            </div>
            <div class="mt-10 grid gap-6 md:grid-cols-3">@foreach ($posts as $post)@include('site.partials.post-card')@endforeach</div>
        </section>
    @endif

    {{-- CTA --}}
    <section class="mx-auto max-w-7xl px-5 py-20 sm:px-8">
        <div class="flex flex-col items-start justify-between gap-6 rounded-3xl bg-beacon-100 p-10 sm:flex-row sm:items-center sm:p-14">
            <div>
                <h2 class="font-display text-3xl font-extrabold text-ink-900">Ready to see where your cargo is?</h2>
                <p class="mt-2 text-ink-600">Request an account or sign in to start tracking.</p>
            </div>
            <div class="flex flex-wrap gap-3">
                @auth
                    <a href="{{ route('dashboard') }}" class="btn btn-dark">Go to my dashboard</a>
                @else
                    <a href="{{ route('register') }}" class="btn btn-dark">Request an account</a>
                    <a href="{{ route('login') }}" class="btn btn-ghost">Sign in</a>
                    <a href="{{ route('site.quote') }}" class="btn btn-ghost">Get a quote</a>
                @endauth
            </div>
        </div>
    </section>

</x-layouts.public>
