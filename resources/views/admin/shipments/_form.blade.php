@csrf
<div class="grid gap-6 sm:grid-cols-2">

    <x-select name="user_id" label="Assign to client" required class="sm:col-span-2">
        <option value="">Select an approved client…</option>
        @foreach ($clients as $client)
            <option value="{{ $client->id }}" @selected((string) old('user_id', $shipment->user_id) === (string) $client->id)>
                {{ $client->name }} ({{ '@' . $client->username }}){{ $client->company ? ' · ' . $client->company : '' }}
            </option>
        @endforeach
    </x-select>

    <x-field name="container_number" label="Container number" :value="$shipment->container_number" placeholder="MSKU1234567" hint="Optional. Spaces and dashes are removed automatically." />
    <x-field name="cargo_description" label="Cargo description" :value="$shipment->cargo_description" placeholder="Auto parts" />

    <x-field name="origin" label="Origin" :value="$shipment->origin" placeholder="Shanghai, CN" required />
    <x-field name="destination" label="Destination" :value="$shipment->destination" placeholder="Durban, ZA" required />

    <x-field name="weight_kg" type="number" step="0.01" min="0" label="Weight (kg)" :value="$shipment->weight_kg" />
    <x-field name="eta" type="date" label="Estimated arrival" :value="$shipment->eta?->format('Y-m-d')" />

    @if (! $shipment->exists)
        <x-select name="status" label="Starting status" required class="sm:col-span-2">
            @foreach ($statuses as $value => $label)
                <option value="{{ $value }}" @selected(old('status', $shipment->status) === $value)>{{ $label }}</option>
            @endforeach
        </x-select>
    @endif
</div>
