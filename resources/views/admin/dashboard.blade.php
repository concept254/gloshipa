<x-layouts.app title="Admin overview" heading="Overview">
    <x-slot:actions>
        <a href="{{ route('admin.shipments.create') }}" class="btn btn-primary">New shipment</a>
    </x-slot:actions>

    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        @foreach ([
            ['Active clients', $stats['clients'], 'text-ink-900'],
            ['Shipments on the way', $stats['open'], 'text-tide-700'],
            ['Delayed', $stats['delayed'], 'text-bad-600'],
            ['Delivered', $stats['delivered'], 'text-ok-600'],
        ] as [$label, $value, $color])
            <div class="card p-5">
                <p class="text-xs font-semibold uppercase tracking-wider text-ink-400">{{ $label }}</p>
                <p class="mt-2 font-display text-4xl font-extrabold {{ $color }}">{{ $value }}</p>
            </div>
        @endforeach
    </div>

    <div class="mt-8 grid gap-8 lg:grid-cols-2">

        <section aria-labelledby="approvals-h">
            <div class="mb-4 flex items-center justify-between">
                <h2 id="approvals-h" class="font-display text-xl font-bold">Awaiting approval
                    <span class="ml-1 rounded-full bg-beacon-100 px-2.5 py-0.5 text-sm text-beacon-700">{{ $awaitingApproval->count() }}</span></h2>
                <a href="{{ route('admin.users.index') }}" class="text-sm font-semibold text-tide-700 hover:underline">Manage clients</a>
            </div>

            <div class="card divide-y divide-ink-100">
                @forelse ($awaitingApproval as $client)
                    <div class="flex flex-wrap items-center justify-between gap-3 p-4">
                        <div>
                            <p class="font-semibold text-ink-900">{{ $client->name }}</p>
                            <p class="text-sm text-ink-500">{{ $client->email }}@if ($client->company) · {{ $client->company }}@endif</p>
                        </div>
                        <form method="POST" action="{{ route('admin.users.status', $client) }}" class="flex gap-2">
                            @csrf @method('PATCH')
                            <button name="status" value="active" class="btn btn-primary btn-sm">Approve</button>
                            <button name="status" value="suspended" class="btn btn-ghost btn-sm" onclick="return confirm('Decline this registration?')">Decline</button>
                        </form>
                    </div>
                @empty
                    <p class="p-8 text-center text-sm text-ink-400">No registrations are waiting. All caught up.</p>
                @endforelse
            </div>
        </section>

        <section aria-labelledby="recent-h">
            <div class="mb-4 flex items-center justify-between">
                <h2 id="recent-h" class="font-display text-xl font-bold">Latest shipments</h2>
                <a href="{{ route('admin.shipments.index') }}" class="text-sm font-semibold text-tide-700 hover:underline">View all</a>
            </div>

            <div class="card divide-y divide-ink-100">
                @forelse ($recent as $shipment)
                    <a href="{{ route('admin.shipments.show', $shipment) }}" class="flex flex-wrap items-center justify-between gap-3 p-4 hover:bg-ink-50">
                        <div>
                            <p class="font-semibold text-ink-900">{{ $shipment->tracking_id }}</p>
                            <p class="text-sm text-ink-500">{{ $shipment->user->name }} · {{ $shipment->origin }} → {{ $shipment->destination }}</p>
                        </div>
                        <x-status-badge :status="$shipment->status" />
                    </a>
                @empty
                    <p class="p-8 text-center text-sm text-ink-400">No shipments yet. Create the first one.</p>
                @endforelse
            </div>
        </section>
    </div>
</x-layouts.app>
