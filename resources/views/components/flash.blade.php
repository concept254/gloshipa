@if (session('status'))
    <div role="status" class="mb-6 flex items-start gap-3 rounded-xl border border-tide-200 bg-tide-50 px-4 py-3 text-sm text-tide-700">
        <svg class="mt-0.5 h-4 w-4 shrink-0" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.7-9.3a1 1 0 00-1.4-1.4L9 10.6 7.7 9.3a1 1 0 00-1.4 1.4l2 2a1 1 0 001.4 0l4-4z" clip-rule="evenodd"/></svg>
        <span>{{ session('status') }}</span>
    </div>
@endif
@if (session('warning'))
    <div role="alert" class="mb-6 flex items-start gap-3 rounded-xl border border-warn-100 bg-warn-100 px-4 py-3 text-sm text-warn-700">
        <svg class="mt-0.5 h-4 w-4 shrink-0" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M8.3 3.2a2 2 0 013.4 0l6 10.5A2 2 0 0116 16.7H4a2 2 0 01-1.7-3L8.3 3.2zM10 7a1 1 0 00-1 1v3a1 1 0 002 0V8a1 1 0 00-1-1zm0 7.5a1.1 1.1 0 100-2.2 1.1 1.1 0 000 2.2z" clip-rule="evenodd"/></svg>
        <span>{{ session('warning') }}</span>
    </div>
@endif
