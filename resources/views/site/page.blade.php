<x-layouts.public :title="$page->title" :description="$page->meta_description">
    <x-page-header :title="$page->title" :subtitle="$page->subtitle" :image="$page->header_image" :crumbs="[[$page->title, null]]" />
    <section class="mx-auto max-w-4xl px-5 py-16 sm:px-8">
        <article class="prose-gs">{!! Cms::markdown($page->body) !!}</article>
        <p class="mt-10 text-xs text-ink-400">Last updated {{ $page->updated_at->format('j F Y') }}</p>
    </section>
</x-layouts.public>
