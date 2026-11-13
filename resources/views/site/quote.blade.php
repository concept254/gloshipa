<x-layouts.public :title="$page->title ?? 'Request a quote'" :description="$page->meta_description ?? null">
    <x-page-header :title="$page->title ?? 'Request a quote'" :subtitle="$page->subtitle ?? 'Tell us about your cargo and route.'" :image="$page->header_image ?? null" :crumbs="[['Request a quote', null]]" />
    <section class="mx-auto max-w-3xl px-5 py-16 sm:px-8">
        <form method="POST" action="{{ route('site.quote.store') }}" class="card space-y-5 p-6 sm:p-8">
            @csrf
            <div class="absolute -left-[9999px] h-0 w-0 overflow-hidden" aria-hidden="true"><label>Leave empty<input type="text" name="website" tabindex="-1" autocomplete="off"></label></div>
            <div class="grid gap-5 sm:grid-cols-2">
                <x-field name="name" label="Your name" required />
                <x-field name="email" type="email" label="Email" required />
                <x-field name="phone" label="Phone" />
                <x-field name="company" label="Company" />
                <x-field name="origin" label="Shipping from" required placeholder="City, country" />
                <x-field name="destination" label="Shipping to" required placeholder="City, country" />
                <x-select name="service" label="Service">
                    <option value="">Not sure yet</option>
                    @foreach ($services as $s)<option @selected(old('service') === $s->title)>{{ $s->title }}</option>@endforeach
                </x-select>
                <x-field name="weight" label="Weight or volume" placeholder="e.g. 1 x 20ft container" />
            </div>
            <x-field name="cargo" label="What are you shipping?" />
            <x-field name="message" type="textarea" label="Anything else we should know?" />
            <button class="btn btn-primary">Send quote request</button>
        </form>
    </section>
</x-layouts.public>
