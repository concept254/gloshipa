@props(['status'])
@php
    $styles = [
        'pending' => 'bg-sand-200 text-ink-700',
        'in_transit' => 'bg-tide-100 text-tide-700',
        'at_port' => 'bg-tide-100 text-tide-700',
        'customs' => 'bg-warn-100 text-warn-700',
        'out_for_delivery' => 'bg-beacon-100 text-beacon-700',
        'delivered' => 'bg-ok-100 text-ok-600',
        'delayed' => 'bg-bad-100 text-bad-700',
        'active' => 'bg-ok-100 text-ok-600',
        'suspended' => 'bg-bad-100 text-bad-700',
    ];
    $label = \App\Models\Shipment::STATUSES[$status] ?? \Illuminate\Support\Str::headline($status);
@endphp
<span {{ $attributes->class(['inline-flex items-center gap-1.5 whitespace-nowrap rounded-full px-2.5 py-1 text-xs font-semibold', $styles[$status] ?? 'bg-ink-100 text-ink-700']) }}>
    <span class="h-1.5 w-1.5 rounded-full bg-current"></span>{{ $label }}
</span>
