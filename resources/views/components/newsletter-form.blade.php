@props(['dark' => false])
<form method="POST" action="{{ route('newsletter.subscribe') }}" {{ $attributes->class(['flex flex-col gap-3 sm:flex-row']) }}>
    @csrf
    {{-- Honeypot: real visitors never see or fill this --}}
    <div class="absolute -left-[9999px] h-0 w-0 overflow-hidden" aria-hidden="true"><label>Leave empty<input type="text" name="website" tabindex="-1" autocomplete="off"></label></div>
    <label for="nl-email" class="sr-only">Email address</label>
    <input id="nl-email" type="email" name="email" required maxlength="190" placeholder="Your email address" autocomplete="email" class="input flex-1 py-3">
    <button class="btn btn-primary py-3">Subscribe</button>
</form>
