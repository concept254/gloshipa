@extends('emails.layout')
@section('title', 'Registration received')
@section('preheader', 'We will email you as soon as your account is approved.')
@section('content')
<h1 style="margin:0 0 14px;font-size:24px;line-height:1.25;color:#0e2033;">Thanks for registering, {{ $user->name }}</h1>
<p style="margin:0 0 14px;">We have received your request for a Glo Shipa account. Our team reviews every registration, and we will email you again as soon as yours is approved.</p>
<p style="margin:0 0 6px;">Your details:</p>
<table role="presentation" cellpadding="0" cellspacing="0" style="margin:0 0 14px;font-size:14px;">
    <tr><td style="padding:2px 16px 2px 0;color:#627d97;">Username</td><td style="padding:2px 0;color:#0e2033;font-weight:bold;">{{ $user->username }}</td></tr>
    <tr><td style="padding:2px 16px 2px 0;color:#627d97;">Email</td><td style="padding:2px 0;color:#0e2033;font-weight:bold;">{{ $user->email }}</td></tr>
</table>
<p style="margin:0;color:#627d97;font-size:13px;">You will not be able to sign in until the account is approved.</p>
@endsection
