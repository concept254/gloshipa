@props(['shipment'])
@php
    $stages = ['pending' => 'Booked', 'in_transit' => 'In transit', 'at_port' => 'At port', 'customs' => 'Customs', 'out_for_delivery' => 'Out for delivery', 'delivered' => 'Delivered'];
    $keys = array_keys($stages);

    // A delayed shipment keeps the position it had before it was delayed.
    $effective = $shipment->status;
    if ($effective === 'delayed') {
        $effective = optional($shipment->events->first(fn ($e) => $e->status !== 'delayed'))->status ?? 'pending';
    }
    $current = array_search($effective, $keys, true);
    $current = $current === false ? 0 : $current;
@endphp
<div>
    @if ($shipment->status === 'delayed')
        <p class="mb-4 rounded-xl bg-bad-100 px-4 py-2.5 text-sm font-medium text-bad-700">This shipment is currently delayed. See the latest update below.</p>
    @endif
    <ol class="grid grid-cols-6 gap-1" aria-label="Shipment progress">
        @foreach ($stages as $key => $label)
            @php
                $done = $loop->index < $current || ($shipment->status === 'delivered' && $loop->index <= $current);
                $isNow = $loop->index === $current && $shipment->status !== 'delivered';
            @endphp
            <li class="relative flex flex-col items-center text-center" @if ($isNow) aria-current="step" @endif>
                @unless ($loop->first)
                    <span class="absolute right-1/2 top-3 h-0.5 w-full {{ $loop->index <= $current ? 'bg-tide-600' : 'bg-ink-100' }}"></span>
                @endunless
                <span class="relative z-10 flex h-6 w-6 items-center justify-center rounded-full text-[10px] font-bold
                    {{ $done ? 'bg-tide-600 text-white' : ($isNow ? 'bg-beacon-500 text-ink-950 ring-4 ring-beacon-200' : 'bg-ink-100 text-ink-400') }}">
                    @if ($done)
                        <svg class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M16.7 5.3a1 1 0 010 1.4l-8 8a1 1 0 01-1.4 0l-4-4a1 1 0 011.4-1.4L8 12.6l7.3-7.3a1 1 0 011.4 0z" clip-rule="evenodd"/></svg>
                    @else
                        {{ $loop->iteration }}
                    @endif
                </span>
                <span class="mt-2 text-[11px] font-medium leading-tight sm:text-xs {{ $isNow ? 'text-ink-900' : 'text-ink-400' }}">{{ $label }}</span>
            </li>
        @endforeach
    </ol>
</div>
