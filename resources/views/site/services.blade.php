<x-layouts.public :title="$page->title ?? 'Services'" :description="$page->meta_description ?? null">
    <x-page-header :title="$page->title ?? 'Our services'" :subtitle="$page->subtitle ?? 'Sea, air and road freight, customs and warehousing under one roof.'" :image="$page->header_image ?? null" :crumbs="[['Services', null]]" />
    @if ($page?->body)
        <section class="mx-auto max-w-4xl px-5 pt-14 sm:px-8"><div class="prose-gs">{!! Cms::markdown($page->body) !!}</div></section>
    @endif
    <section class="mx-auto max-w-7xl px-5 py-16 sm:px-8">
        @if ($services->isEmpty())
            <p class="card p-8 text-center text-ink-500">Our services will be listed here shortly.</p>
        @else
            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($services as $service)@include('site.partials.service-card')@endforeach
            </div>
        @endif
    </section>
    @include('site.partials.quote-cta')
</x-layouts.public>
