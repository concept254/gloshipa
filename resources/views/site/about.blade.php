<x-layouts.public :title="$page->title ?? 'About us'" :description="$page->meta_description ?? null">
    <x-page-header :title="$page->title ?? 'About us'" :subtitle="$page->subtitle ?? 'Who we are and how we move your cargo.'" :image="$page->header_image ?? null" :crumbs="[['About us', null]]" />

    <section class="mx-auto grid max-w-7xl gap-12 px-5 py-16 sm:px-8 lg:grid-cols-[minmax(0,3fr)_minmax(0,2fr)]">
        <div class="prose-gs">{!! Cms::markdown($page->body ?? '') !!}</div>
        <aside class="space-y-4 self-start">
            <div class="card p-6">
                <h2 class="font-display text-lg font-bold">Explore</h2>
                <ul class="mt-3 space-y-2 text-sm font-medium text-tide-700">
                    <li><a class="hover:underline" href="{{ route('site.services') }}">Our services</a></li>
                    <li><a class="hover:underline" href="{{ route('site.team') }}">Meet the team</a></li>
                    <li><a class="hover:underline" href="{{ route('site.clients') }}">Clients &amp; partners</a></li>
                    <li><a class="hover:underline" href="{{ route('site.testimonials') }}">Testimonials</a></li>
                    <li><a class="hover:underline" href="{{ route('site.faqs') }}">FAQs</a></li>
                </ul>
            </div>
            <a href="{{ route('site.quote') }}" class="btn btn-primary w-full">Request a quote</a>
        </aside>
    </section>

    @if ($profile)
        <section class="bg-sand-100 py-16">
            <div class="mx-auto max-w-7xl px-5 sm:px-8">
                <x-section-heading eyebrow="Company profile" :title="$profile->title" :text="$profile->subtitle" />
                <div class="prose-gs mt-8 max-w-4xl">{!! Cms::markdown($profile->body) !!}</div>
            </div>
        </section>
    @endif

    @if ($team->isNotEmpty())
        <section class="mx-auto max-w-7xl px-5 py-16 sm:px-8">
            <x-section-heading eyebrow="Our team" title="The people behind your shipment" />
            <div class="mt-10 grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
                @foreach ($team as $m)@include('site.partials.team-card')@endforeach
            </div>
        </section>
    @endif

    @if ($testimonials->isNotEmpty())
        <section class="bg-sand-100 py-16">
            <div class="mx-auto max-w-7xl px-5 sm:px-8">
                <x-section-heading eyebrow="Testimonials" title="What our clients say" />
                <div class="mt-10">
                    <x-carousel label="Testimonials">
                        @foreach ($testimonials as $t)<div data-slide class="w-[88%] sm:w-[calc(50%-0.75rem)] lg:w-[calc(33.333%-1rem)] snap-start">@include('site.partials.testimonial-card')</div>@endforeach
                    </x-carousel>
                </div>
            </div>
        </section>
    @endif

    @if ($partners->isNotEmpty())
        <section class="mx-auto max-w-7xl px-5 py-16 sm:px-8">
            <x-section-heading eyebrow="Clients &amp; partners" title="Trusted by businesses that ship" />
            <div class="mt-10 grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-5">
                @foreach ($partners as $p)@include('site.partials.partner-logo')@endforeach
            </div>
        </section>
    @endif

    @if ($faqs->isNotEmpty())
        <section class="bg-sand-100 py-16">
            <div class="mx-auto max-w-4xl px-5 sm:px-8">
                <x-section-heading eyebrow="FAQs" title="Common questions" center />
                <div class="mt-10">@include('site.partials.faq-list', ['faqs' => $faqs, 'open' => true])</div>
                <p class="mt-6 text-center"><a href="{{ route('site.faqs') }}" class="btn btn-ghost">See all FAQs</a></p>
            </div>
        </section>
    @endif

    @include('site.partials.quote-cta')
</x-layouts.public>
