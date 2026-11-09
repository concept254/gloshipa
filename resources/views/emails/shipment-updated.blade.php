@extends('emails.layout')
@php
    $delivered = $shipment->status === 'delivered';
    $newLocation = $event->location;
    $moved = $newLocation && $previousLocation && strcasecmp(trim($newLocation), trim($previousLocation)) !== 0;
    $statusChanged = $previousStatus && $previousStatus !== $shipment->status;

    $headline = $delivered
        ? 'Your shipment has been delivered'
        : ($moved ? 'Your shipment has moved' : 'There is an update on your shipment');
@endphp
@section('title', $headline)
@section('preheader', $shipment->tracking_id . ': ' . $shipment->statusLabel() . ($newLocation ? ' at ' . $newLocation : ''))
@section('content')
<h1 style="margin:0 0 14px;font-size:24px;line-height:1.25;color:#0e2033;">{{ $headline }}</h1>
<p style="margin:0 0 18px;">Hi {{ $shipment->user->name }}, here is the latest on shipment <strong style="color:#0e2033;">{{ $shipment->tracking_id }}</strong>@if ($shipment->container_number) (container {{ $shipment->container_number }})@endif.</p>

@if ($moved)
<table role="presentation" cellpadding="0" cellspacing="0" width="100%" style="margin:0 0 16px;background:#e8f7f5;border-radius:10px;">
    <tr>
        <td style="padding:16px 18px;font-size:14px;color:#0c6a61;">
            <span style="display:block;font-size:11px;font-weight:bold;letter-spacing:1px;text-transform:uppercase;">Location change</span>
            <span style="display:block;margin-top:6px;font-size:17px;font-weight:bold;color:#0e2033;">{{ $previousLocation }} &rarr; {{ $newLocation }}</span>
        </td>
    </tr>
</table>
@elseif ($newLocation)
<p style="margin:0 0 16px;font-size:15px;"><strong style="color:#0e2033;">Current location:</strong> {{ $newLocation }}</p>
@endif

<table role="presentation" cellpadding="0" cellspacing="0" width="100%" style="font-size:14px;border:1px solid #e1e8ef;border-radius:10px;">
    <tr>
        <td style="padding:9px 14px;color:#627d97;width:150px;border-bottom:1px solid #e1e8ef;">Status</td>
        <td style="padding:9px 14px;color:#0e2033;font-weight:bold;border-bottom:1px solid #e1e8ef;">
            @if ($statusChanged){{ \App\Models\Shipment::statusLabelFor($previousStatus) }} &rarr; @endif{{ $shipment->statusLabel() }}
        </td>
    </tr>
    <tr>
        <td style="padding:9px 14px;color:#627d97;border-bottom:1px solid #e1e8ef;">Updated</td>
        <td style="padding:9px 14px;color:#0e2033;font-weight:bold;border-bottom:1px solid #e1e8ef;">{{ $event->occurred_at->format('d M Y, H:i') }}</td>
    </tr>
    <tr>
        <td style="padding:9px 14px;color:#627d97;">{{ $delivered ? 'Delivered to' : 'Heading to' }}</td>
        <td style="padding:9px 14px;color:#0e2033;font-weight:bold;">{{ $shipment->destination }}</td>
    </tr>
</table>

@if ($event->note)
<p style="margin:16px 0 0;padding:12px 14px;border-left:3px solid #ff7a2f;background:#fbf8f2;color:#223a52;">{{ $event->note }}</p>
@endif

@unless ($delivered)
<p style="margin:16px 0 0;"><strong style="color:#0e2033;">Estimated arrival:</strong> {{ $shipment->eta?->format('d M Y') ?? 'To be confirmed' }}</p>
@endunless

<x-mail.button :url="route('track', ['q' => $shipment->tracking_id])">View full tracking history</x-mail.button>
@endsection
