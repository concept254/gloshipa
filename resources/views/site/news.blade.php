<x-layouts.public :title="$page->title ?? 'News'" :description="$page->meta_description ?? null">
    <x-page-header :title="$page->title ?? 'News & updates'" :subtitle="$page->subtitle ?? 'Port updates, industry news and shipping tips.'" :image="$page->header_image ?? null" :crumbs="[['News', null]]" />
    <section class="mx-auto max-w-7xl px-5 py-16 sm:px-8">
        @if ($posts->isEmpty())
            <p class="card p-8 text-center text-ink-500">No articles yet. Check back soon.</p>
        @else
            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">@foreach ($posts as $post)@include('site.partials.post-card')@endforeach</div>
            <div class="mt-10">{{ $posts->links() }}</div>
        @endif
    </section>
</x-layouts.public>
