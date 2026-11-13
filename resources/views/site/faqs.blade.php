<x-layouts.public :title="$page->title ?? 'FAQs'" :description="$page->meta_description ?? null">
    <x-page-header :title="$page->title ?? 'Frequently asked questions'" :subtitle="$page->subtitle ?? 'Quick answers about shipping, documents, tracking and your account.'" :image="$page->header_image ?? null" :crumbs="[['FAQs', null]]" />
    <section class="mx-auto max-w-4xl space-y-12 px-5 py-16 sm:px-8">
        @forelse ($groups as $group => $faqs)
            <div>
                <h2 class="mb-5 font-display text-2xl font-extrabold text-ink-900">{{ $group }}</h2>
                @include('site.partials.faq-list', ['faqs' => $faqs])
            </div>
        @empty
            <p class="card p-8 text-center text-ink-500">Questions and answers will be added here shortly.</p>
        @endforelse
        <div class="card flex flex-col items-start justify-between gap-4 p-7 sm:flex-row sm:items-center">
            <div><h2 class="font-display text-xl font-bold">Still have a question?</h2><p class="text-sm text-ink-500">Our team is happy to help.</p></div>
            <a href="{{ route('site.contact') }}" class="btn btn-primary">Contact us</a>
        </div>
    </section>
</x-layouts.public>
