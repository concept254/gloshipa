<x-layouts.public :title="$page->title ?? 'Contact us'" :description="$page->meta_description ?? null">
    <x-page-header :title="$page->title ?? 'Contact us'" :subtitle="$page->subtitle ?? 'Questions about a shipment or a quote? We reply fast.'" :image="$page->header_image ?? null" :crumbs="[['Contact', null]]" />
    <section class="mx-auto grid max-w-7xl gap-10 px-5 py-16 sm:px-8 lg:grid-cols-[minmax(0,2fr)_minmax(0,3fr)]">
        <div class="space-y-4">
            @foreach ([['Address', Cms::address(), 'warehouse'], ['Phone', Cms::phone(), 'clock'], ['Email', Cms::email(), 'doc']] as [$label, $value, $icon])
                <div class="card flex gap-4 p-5">
                    <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-tide-50 text-tide-600"><x-icon :name="$icon" class="h-5 w-5" /></span>
                    <div class="text-sm"><p class="font-semibold text-ink-900">{{ $label }}</p><p class="whitespace-pre-line text-ink-600">{{ $value }}</p></div>
                </div>
            @endforeach
            @if ($h = Cms::setting('business_hours'))
                <div class="card p-5 text-sm"><p class="font-semibold text-ink-900">Business hours</p><p class="text-ink-600">{{ $h }}</p></div>
            @endif
            @if ($wa = Cms::whatsappUrl())<a href="{{ $wa }}" target="_blank" rel="noopener noreferrer" class="btn btn-dark w-full">Chat on WhatsApp</a>@endif
        </div>

        <form method="POST" action="{{ route('site.contact.store') }}" class="card space-y-5 p-6 sm:p-8">
            @csrf
            <h2 class="font-display text-2xl font-extrabold">Send us a message</h2>
            <div class="absolute -left-[9999px] h-0 w-0 overflow-hidden" aria-hidden="true"><label>Leave empty<input type="text" name="website" tabindex="-1" autocomplete="off"></label></div>
            <div class="grid gap-5 sm:grid-cols-2">
                <x-field name="name" label="Your name" required />
                <x-field name="email" type="email" label="Email" required />
                <x-field name="phone" label="Phone" />
                <x-field name="subject" label="Subject" />
            </div>
            <x-field name="message" type="textarea" rows="6" label="Message" required />
            <button class="btn btn-primary">Send message</button>
        </form>
    </section>

    @if ($map = Cms::mapUrl())
        <section class="mx-auto max-w-7xl px-5 pb-16 sm:px-8">
            <div class="overflow-hidden rounded-2xl border border-ink-100 shadow-card">
                <iframe src="{{ $map }}" title="Map to our office" class="h-96 w-full border-0" loading="lazy" referrerpolicy="no-referrer-when-downgrade" allowfullscreen></iframe>
            </div>
        </section>
    @else
        <section class="mx-auto max-w-7xl px-5 pb-16 sm:px-8">
            <div class="rounded-2xl border border-ink-100 bg-white p-6 shadow-card sm:flex sm:items-center sm:justify-between">
                <p class="text-sm text-ink-600">{{ Cms::address() }}</p>
                <a class="btn btn-ghost mt-4 sm:mt-0" target="_blank" rel="noopener noreferrer" href="https://www.google.com/maps/search/?api=1&query={{ urlencode(Cms::address()) }}">Open in Google Maps</a>
            </div>
        </section>
    @endif
</x-layouts.public>
