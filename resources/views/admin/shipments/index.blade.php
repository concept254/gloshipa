<x-layouts.app title="Shipments" heading="Shipments">
    <x-slot:actions>
        <a href="{{ route('admin.shipments.create') }}" class="btn btn-primary">New shipment</a>
    </x-slot:actions>

    <form method="GET" action="{{ route('admin.shipments.index') }}" class="mb-6 flex flex-col gap-3 sm:flex-row">
        <input name="q" value="{{ request('q') }}" placeholder="Search tracking ID, container or client" class="input sm:max-w-md">
        <select name="status" class="input sm:w-56" onchange="this.form.submit()" aria-label="Filter by status">
            <option value="">All statuses</option>
            @foreach ($statuses as $value => $label)
                <option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>
            @endforeach
        </select>
        <button class="btn btn-dark">Search</button>
    </form>

    <div class="card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-ink-100">
                <thead class="bg-ink-50">
                    <tr><th class="th">Tracking ID</th><th class="th">Client</th><th class="th">Container</th><th class="th">Route</th><th class="th">Status</th><th class="th">ETA</th><th class="th"><span class="sr-only">Open</span></th></tr>
                </thead>
                <tbody class="divide-y divide-ink-100">
                    @forelse ($shipments as $shipment)
                        <tr class="hover:bg-ink-50/60">
                            <td class="td font-semibold text-ink-900">{{ $shipment->tracking_id }}</td>
                            <td class="td">{{ $shipment->user->name }}</td>
                            <td class="td">{{ $shipment->container_number ?: '-' }}</td>
                            <td class="td">{{ $shipment->origin }} → {{ $shipment->destination }}</td>
                            <td class="td"><x-status-badge :status="$shipment->status" /></td>
                            <td class="td whitespace-nowrap">{{ $shipment->eta?->format('d M Y') ?? 'TBC' }}</td>
                            <td class="td text-right"><a class="font-semibold text-tide-700 hover:underline" href="{{ route('admin.shipments.show', $shipment) }}">Open</a></td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="px-4 py-12 text-center text-sm text-ink-400">No shipments match.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-6">{{ $shipments->links('pagination.glo') }}</div>
</x-layouts.app>
