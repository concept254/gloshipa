<x-layouts.app title="Clients" heading="Clients">

    <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
        <div class="flex flex-wrap gap-2">
            @foreach (['pending' => 'Awaiting approval', 'active' => 'Active', 'suspended' => 'Switched off'] as $key => $label)
                <a href="{{ route('admin.users.index', ['status' => $key]) }}"
                   class="rounded-full px-4 py-2 text-sm font-semibold {{ $tab === $key ? 'bg-ink-900 text-white' : 'bg-white text-ink-700 ring-1 ring-ink-200 hover:bg-ink-50' }}">
                    {{ $label }} <span class="ml-1 opacity-70">{{ $counts[$key] ?? 0 }}</span>
                </a>
            @endforeach
        </div>

        <form method="GET" action="{{ route('admin.users.index') }}" class="flex gap-2">
            <input type="hidden" name="status" value="{{ $tab }}">
            <input name="q" value="{{ request('q') }}" placeholder="Search name, email, company" class="input w-64">
            <button class="btn btn-dark">Search</button>
        </form>
    </div>

    <div class="card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-ink-100">
                <thead class="bg-ink-50">
                    <tr><th class="th">Client</th><th class="th">Contact</th><th class="th">Registered</th><th class="th">Shipments</th><th class="th text-right">Action</th></tr>
                </thead>
                <tbody class="divide-y divide-ink-100">
                    @forelse ($users as $client)
                        <tr>
                            <td class="td">
                                <p class="font-semibold text-ink-900">{{ $client->name }}</p>
                                <p class="text-xs text-ink-400">{{ '@' . $client->username }}@if ($client->company) · {{ $client->company }}@endif</p>
                            </td>
                            <td class="td">{{ $client->email }}<br><span class="text-xs text-ink-400">{{ $client->phone ?: 'No phone' }}</span></td>
                            <td class="td whitespace-nowrap">{{ $client->created_at->format('d M Y') }}</td>
                            <td class="td">{{ $client->shipments_count }}</td>
                            <td class="td">
                                <form method="POST" action="{{ route('admin.users.status', $client) }}" class="flex justify-end gap-2">
                                    @csrf @method('PATCH')
                                    @if ($client->status !== 'active')
                                        <button name="status" value="active" class="btn btn-primary btn-sm">{{ $client->status === 'pending' ? 'Approve' : 'Reactivate' }}</button>
                                    @endif
                                    @if ($client->status !== 'suspended')
                                        <button name="status" value="suspended" class="btn btn-ghost btn-sm" onclick="return confirm('{{ $client->status === 'pending' ? 'Decline' : 'Switch off' }} {{ addslashes($client->name) }}?')">{{ $client->status === 'pending' ? 'Decline' : 'Switch off' }}</button>
                                    @endif
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-4 py-12 text-center text-sm text-ink-400">No clients in this list.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-6">{{ $users->links('pagination.glo') }}</div>
</x-layouts.app>
