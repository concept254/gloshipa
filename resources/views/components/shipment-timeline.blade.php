@props(['shipment', 'deletable' => false])
@if ($shipment->events->isEmpty())
    <p class="text-sm text-ink-400">No updates have been logged yet.</p>
@else
    <ol class="relative ms-2 border-s-2 border-ink-100">
        @foreach ($shipment->events as $event)
            <li class="relative mb-7 ms-6 last:mb-0">
                <span class="absolute -start-[33px] top-1 flex h-4 w-4 items-center justify-center rounded-full bg-white">
                    <span class="h-3 w-3 rounded-full {{ $loop->first ? 'bg-beacon-500 ring-4 ring-beacon-200' : 'bg-ink-200' }}"></span>
                </span>
                <div class="flex flex-wrap items-center gap-x-3 gap-y-1">
                    <x-status-badge :status="$event->status" />
                    <time class="text-xs text-ink-400" datetime="{{ $event->occurred_at->toIso8601String() }}">{{ $event->occurred_at->format('D, d M Y · H:i') }}</time>
                </div>
                @if ($event->location)
                    <p class="mt-1.5 flex items-center gap-1.5 text-sm font-semibold text-ink-900">
                        <svg class="h-4 w-4 text-tide-600" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M10 18s6-5.2 6-10a6 6 0 10-12 0c0 4.8 6 10 6 10zm0-7.5a2.5 2.5 0 110-5 2.5 2.5 0 010 5z" clip-rule="evenodd"/></svg>
                        {{ $event->location }}
                    </p>
                @endif
                @if ($event->note)
                    <p class="mt-1 text-sm text-ink-500">{{ $event->note }}</p>
                @endif
                @if ($deletable)
                    <form method="POST" action="{{ route('admin.shipments.events.destroy', [$shipment, $event]) }}" class="mt-2" onsubmit="return confirm('Remove this timeline entry?')">
                        @csrf @method('DELETE')
                        <button class="text-xs font-medium text-bad-600 hover:underline">Remove entry</button>
                    </form>
                @endif
            </li>
        @endforeach
    </ol>
@endif
