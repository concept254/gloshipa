<x-layouts.public :title="$post->title" :description="$post->excerpt">
    <x-page-header :title="$post->title" :subtitle="optional($post->published_at ?? $post->created_at)->format('j F Y')" :image="$post->image" :crumbs="[['News', route('site.news')], [\Illuminate\Support\Str::limit($post->title, 40), null]]" />
    <section class="mx-auto grid max-w-7xl gap-12 px-5 py-16 sm:px-8 lg:grid-cols-[minmax(0,2fr)_minmax(0,1fr)]">
        <article class="prose-gs">{!! Cms::markdown($post->body ?: $post->excerpt) !!}</article>
        <aside class="space-y-4 self-start">
            <h2 class="font-display text-lg font-bold">Recent articles</h2>
            @foreach ($recent as $r)
                <a href="{{ route('site.post', $r->slug) }}" class="card block p-5 hover:shadow-lift"><span class="text-xs text-tide-600">{{ optional($r->published_at ?? $r->created_at)->format('j M Y') }}</span><span class="mt-1 block font-display font-bold text-ink-900">{{ $r->title }}</span></a>
            @endforeach
        </aside>
    </section>
</x-layouts.public>
