<x-layouts.auth title="Forgot password" heading="Reset your password" subheading="Enter the email address on your account and we'll send you a reset link.">
    <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
        @csrf
        <x-field name="email" type="email" label="Email address" required autofocus autocomplete="email" />
        <button class="btn btn-primary w-full py-3">Email me a reset link</button>
    </form>
    <p class="mt-8 text-center text-sm text-ink-500"><a href="{{ route('login') }}" class="font-semibold text-ink-900 underline decoration-beacon-500 decoration-2 underline-offset-4">Back to sign in</a></p>
</x-layouts.auth>
