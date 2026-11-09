<x-layouts.auth title="Sign in" heading="Welcome back" subheading="Sign in with your username or email address.">

    @if (str_contains((string) session('url.intended'), '/track'))
        <div class="mb-6 rounded-xl border border-beacon-200 bg-beacon-100 px-4 py-3 text-sm text-ink-800">
            Please sign in to view the tracking details for your shipment.
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}" class="space-y-5">
        @csrf
        <x-field name="login" label="Username or email" required autofocus autocomplete="username" />
        <x-field name="password" type="password" label="Password" required autocomplete="current-password" />

        <div class="flex items-center justify-between text-sm">
            <label class="flex items-center gap-2 text-ink-600">
                <input type="checkbox" name="remember" class="h-4 w-4 rounded border-ink-300 accent-beacon-500"> Remember me
            </label>
            <a href="{{ route('password.request') }}" class="font-medium text-tide-700 hover:underline">Forgot password?</a>
        </div>

        <button class="btn btn-primary w-full py-3">Sign in</button>
    </form>

    <p class="mt-8 text-center text-sm text-ink-500">
        Don't have an account?
        <a href="{{ route('register') }}" class="font-semibold text-ink-900 underline decoration-beacon-500 decoration-2 underline-offset-4">Request one</a>
    </p>
</x-layouts.auth>
