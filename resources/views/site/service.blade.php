<x-layouts.public :title="$service->title" :description="$service->summary">
    <x-page-header :title="$service->title" :subtitle="$service->summary" :image="$service->image" :crumbs="[['Services', route('site.services')], [$service->title, null]]" />
    <section class="mx-auto grid max-w-7xl gap-12 px-5 py-16 sm:px-8 lg:grid-cols-[minmax(0,2fr)_minmax(0,1fr)]">
        <article>
            @if ($service->image)<img src="{{ Cms::image($service->image) }}" alt="" class="mb-8 aspect-[16/8] w-full rounded-2xl object-cover">@endif
            <div class="prose-gs">{!! Cms::markdown($service->body ?: $service->summary) !!}</div>
        </article>
        <aside class="space-y-6 self-start">
            <div class="card p-6">
                <h2 class="font-display text-lg font-bold">Get a quote for {{ $service->title }}</h2>
                <p class="mt-2 text-sm text-ink-500">Share your route and cargo and we will reply with pricing.</p>
                <a href="{{ route('site.quote') }}" class="btn btn-primary mt-4 w-full">Request a quote</a>
            </div>
            @if ($others->isNotEmpty())
                <div class="card p-6">
                    <h2 class="font-display text-lg font-bold">Other services</h2>
                    <ul class="mt-3 space-y-2 text-sm font-medium text-tide-700">
                        @foreach ($others as $o)<li><a class="hover:underline" href="{{ route('site.service', $o->slug) }}">{{ $o->title }}</a></li>@endforeach
                    </ul>
                </div>
            @endif
        </aside>
    </section>
</x-layouts.public>
