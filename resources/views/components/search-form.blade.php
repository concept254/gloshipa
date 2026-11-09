@props(['value' => '', 'big' => false])
<form method="GET" action="{{ route('track') }}" {{ $attributes->class(['flex flex-col gap-3 sm:flex-row']) }}>
    <label for="q" class="sr-only">Tracking ID or container number</label>
    <div class="relative flex-1">
        <svg class="pointer-events-none absolute left-4 top-1/2 h-5 w-5 -translate-y-1/2 text-ink-300" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.9 3.5l4.3 4.3a1 1 0 01-1.4 1.4l-4.3-4.3A6 6 0 012 8z" clip-rule="evenodd"/></svg>
        <input id="q" name="q" value="{{ $value }}" required maxlength="50" autocomplete="off" autocapitalize="characters"
            placeholder="Tracking ID or container number"
            class="input {{ $big ? 'py-4 text-base' : '' }} pl-11 uppercase placeholder:normal-case">
    </div>
    <button class="btn btn-primary {{ $big ? 'px-7 py-4 text-base' : '' }}">Track shipment</button>
</form>
