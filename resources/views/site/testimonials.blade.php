<x-layouts.public :title="$page->title ?? 'Testimonials'" :description="$page->meta_description ?? null">
    <x-page-header :title="$page->title ?? 'Client testimonials'" :subtitle="$page->subtitle ?? 'What importers and exporters say about working with us.'" :image="$page->header_image ?? null" :crumbs="[['Testimonials', null]]" />
    <section class="mx-auto max-w-7xl px-5 py-16 sm:px-8">
        @if ($items->isEmpty())
            <p class="card p-8 text-center text-ink-500">Client reviews will appear here soon.</p>
        @else
            <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">@foreach ($items as $t)@include('site.partials.testimonial-card')@endforeach</div>
        @endif
    </section>
    @include('site.partials.quote-cta')
</x-layouts.public>
