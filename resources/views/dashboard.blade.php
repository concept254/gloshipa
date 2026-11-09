<x-layouts.app title="Dashboard" :heading="'Hello, ' . \Illuminate\Support\Str::of($user->name)->before(' ')">
    <x-slot:actions>
        <a href="{{ route('shipments.index') }}" class="btn btn-ghost">All shipments</a>
    </x-slot:actions>

    <div class="card p-4 sm:p-5">
        <p class="mb-3 text-sm font-semibold text-ink-700">Track a shipment</p>
        <x-search-form />
    </div>

    <div class="mt-8 grid gap-8 lg:grid-cols-[minmax(0,1fr)_minmax(0,2fr)]">

        {{-- Profile --}}
        <section class="card h-fit p-6" aria-labelledby="profile-h">
            <div class="flex items-start justify-between gap-3">
                <h2 id="profile-h" class="font-display text-lg font-bold">Your details</h2>
                <a href="{{ route('profile.edit') }}" class="text-sm font-semibold text-tide-700 hover:underline">Edit</a>
            </div>
            <dl class="mt-5 space-y-4 text-sm">
                @foreach ([
                    'Name' => $user->name,
                    'Username' => '@' . $user->username,
                    'Email' => $user->email,
                    'Phone' => $user->phone,
                    'Company' => $user->company,
                    'Address' => $user->address,
                ] as $label => $value)
                    <div>
                        <dt class="text-xs font-semibold uppercase tracking-wider text-ink-400">{{ $label }}</dt>
                        <dd class="mt-0.5 break-words text-ink-800">{{ $value ?: 'Not provided' }}</dd>
                    </div>
                @endforeach
            </dl>
        </section>

        {{-- Pending shipments --}}
        <section aria-labelledby="pending-h">
            <div class="mb-4 flex items-end justify-between">
                <h2 id="pending-h" class="font-display text-xl font-bold">Pending shipments <span class="ml-1 rounded-full bg-beacon-100 px-2.5 py-0.5 text-sm text-beacon-700">{{ $open->count() }}</span></h2>
                <p class="text-sm text-ink-500">{{ $deliveredCount }} delivered so far</p>
            </div>

            <div class="space-y-4">
                @forelse ($open as $shipment)
                    <a href="{{ route('track', ['q' => $shipment->tracking_id]) }}" class="card block p-5 transition hover:-translate-y-0.5 hover:shadow-lift">
                        <div class="flex flex-wrap items-center justify-between gap-3">
                            <div>
                                <p class="font-display text-lg font-bold text-ink-900">{{ $shipment->tracking_id }}</p>
                                <p class="text-sm text-ink-500">Container {{ $shipment->container_number ?: 'not yet assigned' }}</p>
                            </div>
                            <x-status-badge :status="$shipment->status" />
                        </div>
                        <div class="mt-4 grid gap-4 text-sm sm:grid-cols-3">
                            <div><p class="text-xs font-semibold uppercase tracking-wider text-ink-400">Route</p><p class="mt-0.5 text-ink-800">{{ $shipment->origin }} → {{ $shipment->destination }}</p></div>
                            <div><p class="text-xs font-semibold uppercase tracking-wider text-ink-400">Now at</p><p class="mt-0.5 text-ink-800">{{ $shipment->current_location ?: 'Awaiting update' }}</p></div>
                            <div><p class="text-xs font-semibold uppercase tracking-wider text-ink-400">Estimated arrival</p><p class="mt-0.5 text-ink-800">{{ $shipment->eta?->format('d M Y') ?? 'To be confirmed' }}</p></div>
                        </div>
                    </a>
                @empty
                    <div class="card p-10 text-center">
                        <p class="font-display text-lg font-bold text-ink-900">Nothing on its way right now</p>
                        <p class="mt-1 text-sm text-ink-500">New shipments will appear here as soon as Glo Shipa assigns them to you.</p>
                    </div>
                @endforelse
            </div>

            @if ($recentDelivered->isNotEmpty())
                <h2 class="mb-3 mt-10 font-display text-lg font-bold">Recently delivered</h2>
                <ul class="card divide-y divide-ink-100">
                    @foreach ($recentDelivered as $shipment)
                        <li>
                            <a href="{{ route('track', ['q' => $shipment->tracking_id]) }}" class="flex flex-wrap items-center justify-between gap-2 px-5 py-3.5 text-sm hover:bg-ink-50">
                                <span class="font-semibold text-ink-900">{{ $shipment->tracking_id }}</span>
                                <span class="text-ink-500">{{ $shipment->origin }} → {{ $shipment->destination }}</span>
                                <span class="text-ink-400">{{ $shipment->delivered_at?->format('d M Y') }}</span>
                            </a>
                        </li>
                    @endforeach
                </ul>
            @endif
        </section>
    </div>
</x-layouts.app>
