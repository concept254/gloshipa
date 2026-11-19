@extends('emails.layout')
@section('title', 'New registration')
@section('preheader', $user->name . ' is waiting for approval.')
@section('content')
<h1 style="margin:0 0 14px;font-size:24px;line-height:1.25;color:#0e2033;">New registration to approve</h1>
<p style="margin:0 0 14px;">Someone has asked for a Global Shipping Alliance account and cannot sign in until you approve them.</p>
<table role="presentation" cellpadding="0" cellspacing="0" width="100%" style="margin:0 0 6px;font-size:14px;border:1px solid #e1e8ef;border-radius:10px;">
    @foreach (['Name' => $user->name, 'Username' => $user->username, 'Email' => $user->email, 'Phone' => $user->phone ?: '-', 'Company' => $user->company ?: '-'] as $label => $value)
        <tr>
            <td style="padding:9px 14px;color:#627d97;width:120px;border-bottom:1px solid #e1e8ef;">{{ $label }}</td>
            <td style="padding:9px 14px;color:#0e2033;font-weight:bold;border-bottom:1px solid #e1e8ef;">{{ $value }}</td>
        </tr>
    @endforeach
</table>
<x-mail.button :url="route('admin.users.index', ['status' => 'pending'])">Review registration</x-mail.button>
@endsection
