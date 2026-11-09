<x-layouts.app title="Edit shipment" :heading="'Edit ' . $shipment->tracking_id">
    <div class="mx-auto max-w-3xl">
        <form method="POST" action="{{ route('admin.shipments.update', $shipment) }}" class="card p-6 sm:p-8">
            @method('PUT')
            @include('admin.shipments._form')

            <p class="mt-6 rounded-xl bg-sand-100 px-4 py-3 text-sm text-ink-600">Status and location changes are made by adding an update on the shipment page. Changing the client here emails the new owner.</p>

            <div class="mt-6 flex gap-3">
                <button class="btn btn-primary">Save changes</button>
                <a href="{{ route('admin.shipments.show', $shipment) }}" class="btn btn-ghost">Cancel</a>
            </div>
        </form>
    </div>
</x-layouts.app>
