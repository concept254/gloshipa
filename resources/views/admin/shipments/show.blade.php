<x-layouts.app :title="$shipment->tracking_id" :heading="$shipment->tracking_id">
    <x-slot:actions>
        <x-status-badge :status="$shipment->status" />
        <a href="{{ route('admin.shipments.edit', $shipment) }}" class="btn btn-ghost">Edit</a>
        <form method="POST" action="{{ route('admin.shipments.destroy', $shipment) }}" onsubmit="return confirm('Delete shipment {{ $shipment->tracking_id }} and its whole timeline?')">
            @csrf @method('DELETE')
            <button class="btn btn-danger">Delete</button>
        </form>
    </x-slot:actions>

    <div class="grid gap-8 lg:grid-cols-[minmax(0,5fr)_minmax(0,4fr)]">

        <div class="space-y-8">
            <section class="card p-6 sm:p-8" aria-labelledby="details-h">
                <h2 id="details-h" class="font-display text-xl font-bold">Shipment details</h2>
                @php
                    $facts = [
                        'Client' => $shipment->user->name . ' (@' . $shipment->user->username . ')',
                        'Client email' => $shipment->user->email,
                        'Container' => $shipment->container_number ?: '-',
                        'Cargo' => $shipment->cargo_description ?: '-',
                        'Route' => $shipment->origin . ' → ' . $shipment->destination,
                        'Currently at' => $shipment->current_location ?: '-',
                        'Weight' => $shipment->weight_kg ? number_format((float) $shipment->weight_kg, 0, '.', ' ') . ' kg' : '-',
                        'ETA' => $shipment->eta?->format('d M Y') ?? 'To be confirmed',
                    ];
                @endphp
                <dl class="mt-6 grid gap-x-8 gap-y-5 text-sm sm:grid-cols-2">
                    @foreach ($facts as $label => $value)
                        <div><dt class="text-xs font-semibold uppercase tracking-wider text-ink-400">{{ $label }}</dt><dd class="mt-1 break-words text-ink-900">{{ $value }}</dd></div>
                    @endforeach
                </dl>
                <div class="mt-8 border-t border-ink-100 pt-6">
                    <x-shipment-progress :shipment="$shipment" />
                </div>
            </section>

            <section class="card p-6 sm:p-8" aria-labelledby="update-h">
                <h2 id="update-h" class="font-display text-xl font-bold">Add an update</h2>
                <p class="mt-1 text-sm text-ink-500">Move the shipment to a new status or location. Tick the box to email the client.</p>

                <form method="POST" action="{{ route('admin.shipments.events.store', $shipment) }}" class="mt-6 grid gap-5 sm:grid-cols-2">
                    @csrf
                    <x-select name="status" label="Status" required>
                        @foreach ($statuses as $value => $label)
                            <option value="{{ $value }}" @selected(old('status', $shipment->status) === $value)>{{ $label }}</option>
                        @endforeach
                    </x-select>
                    <x-field name="location" label="Location" :value="$shipment->current_location" placeholder="Port of Durban" />
                    <x-field name="occurred_at" type="datetime-local" label="When" :value="now()->format('Y-m-d\TH:i')" required />
                    <x-field name="note" label="Note for the client" placeholder="Optional" />

                    <label class="flex items-center gap-2.5 text-sm text-ink-700 sm:col-span-2">
                        <input type="checkbox" name="notify" value="1" class="h-4 w-4 rounded border-ink-300 accent-beacon-500" @checked(old('notify', true))>
                        Email {{ $shipment->user->name }} about this update
                    </label>

                    <div class="sm:col-span-2"><button class="btn btn-primary">Save update</button></div>
                </form>
            </section>
        </div>

        <section class="card h-fit p-6 sm:p-8" aria-labelledby="timeline-h">
            <h2 id="timeline-h" class="mb-6 font-display text-xl font-bold">Timeline</h2>
            <x-shipment-timeline :shipment="$shipment" :deletable="true" />
        </section>
    </div>
</x-layouts.app>
