{{-- Floating WhatsApp chat bubble and back-to-top button (on every layout). --}}
@php $wa = Cms::whatsappUrl(); @endphp
<div class="fixed bottom-5 right-5 z-40 flex flex-col items-end gap-3">
    <button type="button" id="back-to-top" class="pointer-events-none flex h-11 w-11 translate-y-2 items-center justify-center rounded-full bg-ink-900 text-white opacity-0 shadow-lift transition hover:bg-ink-700" aria-label="Back to top">
        <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M14.7 12.7a1 1 0 01-1.4 0L10 9.4l-3.3 3.3a1 1 0 01-1.4-1.4l4-4a1 1 0 011.4 0l4 4a1 1 0 010 1.4z" clip-rule="evenodd"/></svg>
    </button>
    @if ($wa)
        <a href="{{ $wa }}" target="_blank" rel="noopener noreferrer" class="flex items-center gap-2 rounded-full bg-tide-600 py-3 pl-3.5 pr-4 text-sm font-semibold text-white shadow-lift transition hover:bg-tide-700" aria-label="Chat with us on WhatsApp">
            <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 12a8 8 0 01-11.7 7.1L4 20l1-4.6A8 8 0 1121 12zM9 10h6M9 14h4"/></svg>
            <span class="hidden sm:inline">Chat on WhatsApp</span>
        </a>
    @endif
</div>
