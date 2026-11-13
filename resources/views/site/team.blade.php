<x-layouts.public :title="$page->title ?? 'Our team'" :description="$page->meta_description ?? null">
    <x-page-header :title="$page->title ?? 'Meet the team'" :subtitle="$page->subtitle ?? 'The people who look after your cargo.'" :image="$page->header_image ?? null" :crumbs="[['About us', route('site.about')], ['Team', null]]" />
    <section class="mx-auto max-w-7xl px-5 py-16 sm:px-8">
        @if ($members->isEmpty())
            <p class="card p-8 text-center text-ink-500">Our team will be introduced here soon.</p>
        @else
            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-4">@foreach ($members as $m)@include('site.partials.team-card')@endforeach</div>
        @endif
    </section>
</x-layouts.public>
