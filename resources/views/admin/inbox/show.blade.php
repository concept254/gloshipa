<x-layouts.app title="Message" :heading="$inquiry->subject ?: 'Message from ' . $inquiry->name">
    @include('admin.content._tabs')
    <div class="card mx-auto max-w-3xl space-y-4 p-6 text-sm sm:p-8">
        <p><strong>{{ $inquiry->name }}</strong> &middot; <a class="text-tide-700 underline" href="mailto:{{ $inquiry->email }}">{{ $inquiry->email }}</a>@if ($inquiry->phone) &middot; {{ $inquiry->phone }}@endif</p>
        <p class="text-xs text-ink-400">{{ $inquiry->created_at->format('j F Y, H:i') }} &middot; {{ $inquiry->type }}</p>
        @foreach (($inquiry->meta ?? []) as $label => $value)
            @if ($value)<p><span class="font-semibold">{{ $label }}:</span> {{ $value }}</p>@endif
        @endforeach
        @if ($inquiry->message)<p class="whitespace-pre-line rounded-xl bg-sand-50 p-4 leading-relaxed text-ink-700">{{ $inquiry->message }}</p>@endif
        <div class="flex gap-3 pt-2">
            <a href="mailto:{{ $inquiry->email }}?subject=Re: {{ rawurlencode($inquiry->subject ?? 'Your enquiry') }}" class="btn btn-primary">Reply by email</a>
            <form method="POST" action="{{ route('admin.website.inbox.destroy', $inquiry) }}" onsubmit="return confirm('Delete this message?')">@csrf @method('DELETE')<button class="btn btn-danger">Delete</button></form>
        </div>
    </div>
</x-layouts.app>
