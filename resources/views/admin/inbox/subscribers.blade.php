<x-layouts.app title="Subscribers" heading="Newsletter subscribers">
    <x-slot:actions><a href="{{ route('admin.website.subscribers.export') }}" class="btn btn-ghost">Download CSV</a></x-slot:actions>
    @include('admin.content._tabs')
    <p class="mb-4 text-sm text-ink-500">{{ $active }} active subscribers. Download the CSV to import them into your mailing tool.</p>
    <div class="card overflow-x-auto">
        <table class="w-full">
            <thead class="border-b border-ink-100"><tr><th class="th">Email</th><th class="th">Subscribed</th><th class="th">Status</th><th class="th"></th></tr></thead>
            <tbody class="divide-y divide-ink-100">
                @forelse ($rows as $r)
                    <tr>
                        <td class="td">{{ $r->email }}</td>
                        <td class="td">{{ optional($r->subscribed_at)->format('j M Y') }}</td>
                        <td class="td">{{ $r->unsubscribed_at ? 'Unsubscribed' : 'Active' }}</td>
                        <td class="td text-right"><form method="POST" action="{{ route('admin.website.subscribers.destroy', $r) }}" onsubmit="return confirm('Remove this subscriber?')">@csrf @method('DELETE')<button class="btn btn-danger btn-sm">Remove</button></form></td>
                    </tr>
                @empty
                    <tr><td class="td py-10 text-center text-ink-400" colspan="4">No subscribers yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-6">{{ $rows->links() }}</div>
</x-layouts.app>
