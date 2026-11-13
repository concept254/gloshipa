<x-layouts.app title="Website" heading="Website content">
    @include('admin.content._tabs')

    @unless ($ready)
        <div class="mb-6 rounded-xl border border-warn-100 bg-warn-100 px-4 py-3 text-sm text-warn-700">
            The website tables are not in the database yet. Run <code>php artisan migrate --force</code> and then <code>php artisan db:seed --class=ContentSeeder --force</code>.
        </div>
    @endunless

    <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
        @foreach ($cards as $c)
            <a href="{{ route('admin.website.index', $c['key']) }}" class="card p-6 transition hover:-translate-y-0.5 hover:shadow-lift">
                <p class="text-xs font-semibold uppercase tracking-wider text-ink-400">{{ $c['label'] }}</p>
                <p class="mt-2 font-display text-3xl font-extrabold text-ink-900">{{ $c['count'] ?? '-' }}</p>
                <p class="mt-1 text-sm font-medium text-tide-700">Manage</p>
            </a>
        @endforeach
        <a href="{{ route('admin.website.inbox') }}" class="card p-6 hover:shadow-lift">
            <p class="text-xs font-semibold uppercase tracking-wider text-ink-400">Unread messages</p>
            <p class="mt-2 font-display text-3xl font-extrabold text-ink-900">{{ $unread }}</p>
            <p class="mt-1 text-sm font-medium text-tide-700">Open inbox</p>
        </a>
        <a href="{{ route('admin.website.subscribers') }}" class="card p-6 hover:shadow-lift">
            <p class="text-xs font-semibold uppercase tracking-wider text-ink-400">Newsletter subscribers</p>
            <p class="mt-2 font-display text-3xl font-extrabold text-ink-900">{{ $subscribers }}</p>
            <p class="mt-1 text-sm font-medium text-tide-700">View and export</p>
        </a>
        <a href="{{ route('admin.website.settings') }}" class="card p-6 hover:shadow-lift">
            <p class="text-xs font-semibold uppercase tracking-wider text-ink-400">Settings</p>
            <p class="mt-2 font-display text-lg font-extrabold text-ink-900">Contact, WhatsApp, map, social, home page</p>
            <p class="mt-1 text-sm font-medium text-tide-700">Edit</p>
        </a>
    </div>
</x-layouts.app>
