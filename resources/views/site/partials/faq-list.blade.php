<div class="divide-y divide-ink-100 overflow-hidden rounded-2xl border border-ink-100 bg-white shadow-card">
    @foreach ($faqs as $faq)
        <details class="group p-6" @if ($loop->first && ($open ?? false)) open @endif>
            <summary class="flex cursor-pointer list-none items-center justify-between gap-4 font-display text-lg font-bold text-ink-900">
                {{ $faq->question }}
                <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-sand-100 text-ink-700 transition group-open:rotate-45 group-open:bg-beacon-500" aria-hidden="true">+</span>
            </summary>
            <p class="mt-3 leading-relaxed text-ink-600">{!! nl2br(e($faq->answer)) !!}</p>
        </details>
    @endforeach
</div>
