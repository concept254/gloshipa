<x-layouts.auth title="Request an account" heading="Request an account" subheading="Your account will be activated after a quick review by our team. We'll email you when it's ready.">

    <form method="POST" action="{{ route('register') }}" class="space-y-5">
        @csrf
        <x-field name="name" label="Full name" required autofocus autocomplete="name" />
        <div class="grid gap-5 sm:grid-cols-2">
            <x-field name="username" label="Username" required autocomplete="username" hint="Letters, numbers, dashes and underscores." />
            <x-field name="phone" label="Phone" type="tel" autocomplete="tel" />
        </div>
        <x-field name="email" type="email" label="Email address" required autocomplete="email" />
        <x-field name="company" label="Company" autocomplete="organization" />
        <div class="grid gap-5 sm:grid-cols-2">
            <x-field name="password" type="password" label="Password" required autocomplete="new-password" hint="At least 8 characters." />
            <x-field name="password_confirmation" type="password" label="Confirm password" required autocomplete="new-password" />
        </div>

        <button class="btn btn-primary w-full py-3">Submit registration</button>
    </form>

    <p class="mt-8 text-center text-sm text-ink-500">
        Already approved?
        <a href="{{ route('login') }}" class="font-semibold text-ink-900 underline decoration-beacon-500 decoration-2 underline-offset-4">Sign in</a>
    </p>
</x-layouts.auth>
