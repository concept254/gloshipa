<x-layouts.app :title="$def['label']" :heading="$def['label']">
    <x-slot:actions><a href="{{ route('admin.website.create', $resource) }}" class="btn btn-primary">Add {{ $def['singular'] }}</a></x-slot:actions>
    @include('admin.content._tabs')
    @if (! empty($def['help']))<p class="mb-5 text-sm text-ink-500">{{ $def['help'] }}</p>@endif

    <div class="card overflow-x-auto">
        <table class="w-full">
            <thead class="border-b border-ink-100"><tr>
                @foreach ($def['columns'] as $c)<th class="th">{{ $c[1] }}</th>@endforeach
                <th class="th text-right">Actions</th>
            </tr></thead>
            <tbody class="divide-y divide-ink-100">
                @forelse ($rows as $row)
                    <tr>
                        @foreach ($def['columns'] as $c)
                            @php $v = $row->{$c[0]}; $kind = $c[2] ?? 'text'; @endphp
                            <td class="td">
                                @if ($kind === 'bool')
                                    <span class="rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $v ? 'bg-ok-100 text-ok-600' : 'bg-ink-100 text-ink-500' }}">{{ $v ? 'Yes' : 'No' }}</span>
                                @elseif ($kind === 'date')
                                    {{ optional($v)->format('j M Y') }}
                                @else
                                    {{ \Illuminate\Support\Str::limit((string) $v, 70) }}
                                @endif
                            </td>
                        @endforeach
                        <td class="td whitespace-nowrap text-right">
                            <a href="{{ route('admin.website.edit', [$resource, $row->id]) }}" class="btn btn-ghost btn-sm">Edit</a>
                            @unless ($resource === 'pages' && $row->isSystem())
                                <form method="POST" action="{{ route('admin.website.destroy', [$resource, $row->id]) }}" class="inline" onsubmit="return confirm('Delete this {{ $def['singular'] }}?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-danger btn-sm">Delete</button>
                                </form>
                            @endunless
                        </td>
                    </tr>
                @empty
                    <tr><td class="td py-10 text-center text-ink-400" colspan="{{ count($def['columns']) + 1 }}">Nothing here yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-6">{{ $rows->links() }}</div>
</x-layouts.app>
