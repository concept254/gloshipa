<x-layouts.app title="New shipment" heading="New shipment">
    <div class="mx-auto max-w-3xl">
        <form method="POST" action="{{ route('admin.shipments.store') }}" class="card p-6 sm:p-8">
            @include('admin.shipments._form')

            <p class="mt-6 rounded-xl bg-tide-50 px-4 py-3 text-sm text-tide-700">A tracking ID is generated automatically, and the client is emailed as soon as you save.</p>

            <div class="mt-6 flex gap-3">
                <button class="btn btn-primary">Create and notify client</button>
                <a href="{{ route('admin.shipments.index') }}" class="btn btn-ghost">Cancel</a>
            </div>
        </form>
    </div>
</x-layouts.app>
