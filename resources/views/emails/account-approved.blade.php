@extends('emails.layout')
@section('title', 'Account approved')
@section('preheader', 'You can now sign in and track your shipments.')
@section('content')
<h1 style="margin:0 0 14px;font-size:24px;line-height:1.25;color:#0e2033;">Your account is approved</h1>
<p style="margin:0 0 14px;">Hi {{ $user->name }}, good news: your Global Shipping Alliance account is now active. You can sign in with your username <strong style="color:#0e2033;">{{ $user->username }}</strong> or your email address.</p>
<p style="margin:0;">Once we assign a shipment to you, it will appear on your dashboard and we will email you.</p>
<x-mail.button :url="route('login')">Sign in to Global Shipping Alliance</x-mail.button>
@endsection
