<x-layouts.public :title="$page->title ?? 'Clients & partners'" :description="$page->meta_description ?? null">
    <x-page-header :title="$page->title ?? 'Clients & partners'" :subtitle="$page->subtitle ?? 'The businesses and carriers we work with.'" :image="$page->header_image ?? null" :crumbs="[['About us', route('site.about')], ['Clients & partners', null]]" />
    <section class="mx-auto max-w-7xl px-5 py-16 sm:px-8">
        @if ($partners->isEmpty())
            <p class="card p-8 text-center text-ink-500">Our clients and partners will be shown here soon.</p>
        @else
            <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-4">@foreach ($partners as $p)@include('site.partials.partner-logo')@endforeach</div>
        @endif
    </section>
    @if ($testimonials->isNotEmpty())
        <section class="bg-sand-100 py-16"><div class="mx-auto max-w-7xl px-5 sm:px-8">
            <x-section-heading eyebrow="In their words" title="What clients say" />
            <div class="mt-10 grid gap-6 md:grid-cols-2 lg:grid-cols-3">@foreach ($testimonials as $t)@include('site.partials.testimonial-card')@endforeach</div>
        </div></section>
    @endif
</x-layouts.public>
