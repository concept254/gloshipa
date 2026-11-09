<x-layouts.app title="My profile" heading="My profile">
    <div class="grid gap-8 lg:grid-cols-2">

        <section class="card p-6 sm:p-8" aria-labelledby="details-h">
            <h2 id="details-h" class="font-display text-xl font-bold">Your details</h2>
            <p class="mt-1 text-sm text-ink-500">These details are used for your account and for shipment emails.</p>

            <form method="POST" action="{{ route('profile.update') }}" class="mt-6 space-y-5">
                @csrf @method('PATCH')
                <x-field name="name" label="Full name" :value="$user->name" required autocomplete="name" />
                <div class="grid gap-5 sm:grid-cols-2">
                    <x-field name="username" label="Username" :value="$user->username" required autocomplete="username" />
                    <x-field name="phone" type="tel" label="Phone" :value="$user->phone" autocomplete="tel" />
                </div>
                <x-field name="email" type="email" label="Email address" :value="$user->email" required autocomplete="email" />
                <x-field name="company" label="Company" :value="$user->company" autocomplete="organization" />
                <x-field name="address" type="textarea" label="Delivery address" :value="$user->address" />
                <button class="btn btn-primary">Save changes</button>
            </form>
        </section>

        <section class="card h-fit p-6 sm:p-8" aria-labelledby="password-h">
            <h2 id="password-h" class="font-display text-xl font-bold">Change password</h2>
            <p class="mt-1 text-sm text-ink-500">Use at least 8 characters.</p>

            <form method="POST" action="{{ route('profile.password') }}" class="mt-6 space-y-5">
                @csrf @method('PUT')
                <x-field name="current_password" type="password" label="Current password" required autocomplete="current-password" />
                <x-field name="password" type="password" label="New password" required autocomplete="new-password" />
                <x-field name="password_confirmation" type="password" label="Confirm new password" required autocomplete="new-password" />
                <button class="btn btn-dark">Update password</button>
            </form>
        </section>
    </div>
</x-layouts.app>
