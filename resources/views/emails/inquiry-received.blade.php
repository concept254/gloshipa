@extends('emails.layout')
@section('title', 'New website enquiry')
@section('content')
<h1 style="margin:0 0 14px;font-size:22px;color:#0e2033;">{{ $inquiry->type === 'quote' ? 'New quote request' : 'New message from the website' }}</h1>
<p><strong>{{ $inquiry->name }}</strong> &lt;{{ $inquiry->email }}&gt;@if ($inquiry->phone) &middot; {{ $inquiry->phone }}@endif</p>
@if ($inquiry->subject)<p><strong>Subject:</strong> {{ $inquiry->subject }}</p>@endif
@foreach (($inquiry->meta ?? []) as $label => $value)
    @if ($value)<p style="margin:2px 0;"><strong>{{ $label }}:</strong> {{ $value }}</p>@endif
@endforeach
@if ($inquiry->message)<p style="margin-top:14px;white-space:pre-line;">{{ $inquiry->message }}</p>@endif
<p style="margin-top:22px;"><a href="{{ route('admin.website.inbox.show', $inquiry) }}" style="background:#ff7a2f;color:#08141f;text-decoration:none;font-weight:bold;padding:11px 20px;border-radius:10px;display:inline-block;">Open in the admin inbox</a></p>
<p style="color:#627d97;font-size:13px;">Reply to this email to answer {{ $inquiry->name }} directly.</p>
@endsection
