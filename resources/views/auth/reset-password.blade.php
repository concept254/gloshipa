<x-layouts.auth title="Choose a new password" heading="Choose a new password" subheading="Pick something you haven't used before.">
    <form method="POST" action="{{ route('password.update') }}" class="space-y-5">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">
        <x-field name="email" type="email" label="Email address" :value="$email" required autocomplete="email" />
        <x-field name="password" type="password" label="New password" required autofocus autocomplete="new-password" hint="At least 8 characters." />
        <x-field name="password_confirmation" type="password" label="Confirm new password" required autocomplete="new-password" />
        <button class="btn btn-primary w-full py-3">Reset password</button>
    </form>
</x-layouts.auth>
