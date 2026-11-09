<x-layouts.app title="My shipments" heading="My shipments">
    <div class="mb-6 flex flex-wrap gap-2" role="tablist" aria-label="Filter shipments">
        @foreach (['all' => 'All', 'pending' => 'Pending', 'delivered' => 'Delivered'] as $key => $label)
            <a href="{{ route('shipments.index', ['filter' => $key]) }}"
               class="rounded-full px-4 py-2 text-sm font-semibold {{ $filter === $key ? 'bg-ink-900 text-white' : 'bg-white text-ink-700 ring-1 ring-ink-200 hover:bg-ink-50' }}">{{ $label }}</a>
        @endforeach
    </div>

    <div class="card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-ink-100">
                <thead class="bg-ink-50">
                    <tr>
                        <th class="th">Tracking ID</th><th class="th">Container</th><th class="th">Route</th>
                        <th class="th">Status</th><th class="th">ETA</th><th class="th"><span class="sr-only">View</span></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-ink-100">
                    @forelse ($shipments as $shipment)
                        <tr class="hover:bg-ink-50/60">
                            <td class="td font-semibold text-ink-900">{{ $shipment->tracking_id }}</td>
                            <td class="td">{{ $shipment->container_number ?: '-' }}</td>
                            <td class="td">{{ $shipment->origin }} → {{ $shipment->destination }}</td>
                            <td class="td"><x-status-badge :status="$shipment->status" /></td>
                            <td class="td whitespace-nowrap">{{ $shipment->eta?->format('d M Y') ?? 'TBC' }}</td>
                            <td class="td text-right"><a class="font-semibold text-tide-700 hover:underline" href="{{ route('track', ['q' => $shipment->tracking_id]) }}">Track</a></td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-4 py-12 text-center text-sm text-ink-400">No shipments to show here yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-6">{{ $shipments->links('pagination.glo') }}</div>
</x-layouts.app>
