<x-layouts.app title="Inbox" heading="Inbox">
    @include('admin.content._tabs')
    <div class="card divide-y divide-ink-100">
        @forelse ($rows as $r)
            <a href="{{ route('admin.website.inbox.show', $r) }}" class="flex items-start justify-between gap-4 p-5 hover:bg-sand-50">
                <div>
                    <p class="text-sm {{ $r->read_at ? 'text-ink-600' : 'font-bold text-ink-900' }}">{{ $r->name }} <span class="font-normal text-ink-400">&lt;{{ $r->email }}&gt;</span></p>
                    <p class="mt-0.5 text-sm text-ink-500">{{ \Illuminate\Support\Str::limit($r->subject ?: $r->message, 90) }}</p>
                </div>
                <div class="shrink-0 text-right text-xs text-ink-400">
                    <span class="rounded-full px-2 py-0.5 font-semibold {{ $r->type === 'quote' ? 'bg-beacon-100 text-beacon-700' : 'bg-tide-50 text-tide-700' }}">{{ $r->type }}</span>
                    <p class="mt-1">{{ $r->created_at->format('j M, H:i') }}</p>
                </div>
            </a>
        @empty
            <p class="p-10 text-center text-sm text-ink-400">No messages yet.</p>
        @endforelse
    </div>
    <div class="mt-6">{{ $rows->links() }}</div>
</x-layouts.app>
