<x-layouts.app title="Website settings" heading="Website settings">
    @include('admin.content._tabs')
    <form method="POST" action="{{ route('admin.website.settings.update') }}" class="mx-auto max-w-3xl space-y-8">
        @csrf @method('PUT')
        <p class="text-sm text-ink-500">Leave a box empty to use the default shown under it.</p>
        @foreach ($groups as $group => $items)
            <fieldset class="card space-y-5 p-6 sm:p-8">
                <legend class="sr-only">{{ $group }}</legend>
                <h2 class="font-display text-xl font-extrabold">{{ $group }}</h2>
                @foreach ($items as [$key, $label, $type, $hint])
                    <x-field :name="$key" :label="$label" :type="$type" :value="$values[$key] ?? null" :hint="$hint" />
                @endforeach
            </fieldset>
        @endforeach
        <button class="btn btn-primary">Save settings</button>
    </form>
</x-layouts.app>
