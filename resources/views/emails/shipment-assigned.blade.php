@extends('emails.layout')
@section('title', 'Shipment assigned')
@section('preheader', 'Tracking ID ' . $shipment->tracking_id . ': ' . $shipment->origin . ' to ' . $shipment->destination)
@section('content')
<h1 style="margin:0 0 14px;font-size:24px;line-height:1.25;color:#0e2033;">A shipment has been assigned to you</h1>
<p style="margin:0 0 18px;">Hi {{ $shipment->user->name }}, Glo Shipa has added a new shipment to your account. Here are the details:</p>

@php
    $rows = [
        'Tracking ID' => $shipment->tracking_id,
        'Container' => $shipment->container_number ?: 'Not yet assigned',
        'Route' => $shipment->origin . ' to ' . $shipment->destination,
        'Cargo' => $shipment->cargo_description ?: '-',
        'Weight' => $shipment->weight_kg ? number_format((float) $shipment->weight_kg, 0, '.', ' ') . ' kg' : '-',
        'Status' => $shipment->statusLabel(),
        'Estimated arrival' => $shipment->eta?->format('d M Y') ?? 'To be confirmed',
    ];
@endphp
<table role="presentation" cellpadding="0" cellspacing="0" width="100%" style="font-size:14px;border:1px solid #e1e8ef;border-radius:10px;">
    @foreach ($rows as $label => $value)
        <tr>
            <td style="padding:9px 14px;color:#627d97;width:150px;border-bottom:1px solid #e1e8ef;">{{ $label }}</td>
            <td style="padding:9px 14px;color:#0e2033;font-weight:bold;border-bottom:1px solid #e1e8ef;">{{ $value }}</td>
        </tr>
    @endforeach
</table>

<x-mail.button :url="route('track', ['q' => $shipment->tracking_id])">Track this shipment</x-mail.button>
<p style="margin:0;color:#627d97;font-size:13px;">You will receive another email whenever it moves to a new location.</p>
@endsection
