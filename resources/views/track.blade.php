<x-layouts.app title="Track a shipment" heading="Track a shipment">

    <div class="card p-4 sm:p-5">
        <x-search-form :value="$term" />
        <p class="mt-3 text-xs text-ink-400">Search by tracking ID (for example GS4K9X2M7QPA) or by container number (for example MSKU1234567).</p>
    </div>

    <div class="mt-8 space-y-8">
        @forelse ($results as $shipment)
            <article class="card overflow-hidden">
                <div class="flex flex-wrap items-center justify-between gap-4 bg-ink-900 px-6 py-5 text-white sm:px-8">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider text-ink-300">Tracking ID</p>
                        <h2 class="font-display text-2xl font-extrabold">{{ $shipment->tracking_id }}</h2>
                    </div>
                    <x-status-badge :status="$shipment->status" class="bg-white!" />
                </div>

                <div class="grid gap-x-8 gap-y-5 border-b border-ink-100 px-6 py-6 text-sm sm:grid-cols-2 sm:px-8 lg:grid-cols-4">
                    @php
                        $delivered = $shipment->status === 'delivered';
                        $facts = [
                            'Container' => $shipment->container_number ?: 'Not yet assigned',
                            'Route' => $shipment->origin . ' → ' . $shipment->destination,
                            'Currently at' => $shipment->current_location ?: 'Awaiting update',
                            ($delivered ? 'Delivered on' : 'Estimated arrival') => $delivered
                                ? ($shipment->delivered_at?->format('d M Y') ?? '-')
                                : ($shipment->eta?->format('d M Y') ?? 'To be confirmed'),
                            'Cargo' => $shipment->cargo_description ?: '-',
                            'Weight' => $shipment->weight_kg ? number_format((float) $shipment->weight_kg, 0, '.', ' ') . ' kg' : '-',
                        ];
                    @endphp
                    @foreach ($facts as $label => $value)
                        <div><p class="text-xs font-semibold uppercase tracking-wider text-ink-400">{{ $label }}</p><p class="mt-1 text-ink-900">{{ $value }}</p></div>
                    @endforeach
                    @if (auth()->user()->isAdmin())
                        <div><p class="text-xs font-semibold uppercase tracking-wider text-ink-400">Client</p>
                            <p class="mt-1"><a class="font-semibold text-tide-700 hover:underline" href="{{ route('admin.shipments.show', $shipment) }}">{{ $shipment->user->name }}</a></p></div>
                    @endif
                </div>

                <div class="grid gap-10 px-6 py-8 sm:px-8 lg:grid-cols-[minmax(0,5fr)_minmax(0,4fr)]">
                    <div>
                        <h3 class="mb-5 font-display text-lg font-bold">Progress</h3>
                        <x-shipment-progress :shipment="$shipment" />
                    </div>
                    <div>
                        <h3 class="mb-5 font-display text-lg font-bold">Journey so far</h3>
                        <x-shipment-timeline :shipment="$shipment" />
                    </div>
                </div>
            </article>
        @empty
            @if ($searched)
                <div class="card p-10 text-center">
                    <p class="font-display text-xl font-bold text-ink-900">No shipment found for “{{ $term }}”</p>
                    <p class="mx-auto mt-2 max-w-md text-sm text-ink-500">Check the tracking ID or container number and try again. You can only look up shipments assigned to your account. If something looks wrong, please contact us.</p>
                </div>
            @endif
        @endforelse
    </div>
</x-layouts.app>
