@php $editing = $item->exists; @endphp
<x-layouts.app :title="($editing ? 'Edit ' : 'Add ') . $def['singular']" :heading="($editing ? 'Edit ' : 'Add ') . $def['singular']">
    @include('admin.content._tabs')
    <form method="POST" enctype="multipart/form-data"
          action="{{ $editing ? route('admin.website.update', [$resource, $item->id]) : route('admin.website.store', $resource) }}"
          class="card mx-auto max-w-3xl space-y-5 p-6 sm:p-8">
        @csrf
        @if ($editing) @method('PUT') @endif

        @foreach ($fields as $f)
            @php
                $name = $f['name'];
                $locked = $name === 'slug' && $editing && ($resource === 'pages') && $item->isSystem();
                $val = $editing ? $item->{$name} : $f['default'];
            @endphp
            @switch($f['type'])
                @case('markdown')
                    <div>
                        <label for="{{ $name }}" class="label">{{ $f['label'] }}</label>
                        <textarea id="{{ $name }}" name="{{ $name }}" rows="16" class="input font-mono text-[13px] leading-relaxed">{{ old($name, $val) }}</textarea>
                        <p class="mt-1 text-xs text-ink-400">Formatting: <code>## Heading</code>, <code>**bold**</code>, <code>- list item</code>, <code>[link text](https://...)</code>. A blank line starts a new paragraph.</p>
                        @error($name)<p class="mt-1.5 text-xs font-medium text-bad-600">{{ $message }}</p>@enderror
                    </div>
                    @break
                @case('textarea')
                    <x-field :name="$name" :label="$f['label']" type="textarea" rows="5" :value="$val" :hint="$f['hint']" />
                    @break
                @case('checkbox')
                    <label class="flex items-center gap-3 text-sm font-medium text-ink-700">
                        <input type="hidden" name="{{ $name }}" value="0">
                        <input type="checkbox" name="{{ $name }}" value="1" class="h-4 w-4 rounded border-ink-300 accent-beacon-500" @checked(old($name, $val))> {{ $f['label'] }}
                    </label>
                    @break
                @case('select')
                    <x-select :name="$name" :label="$f['label']">
                        @foreach ($f['options'] as $o)<option value="{{ $o }}" @selected(old($name, $val) === $o)>{{ ucfirst($o) }}</option>@endforeach
                    </x-select>
                    @break
                @case('image')
                    <div>
                        <label for="{{ $name }}" class="label">{{ $f['label'] }}</label>
                        @if ($editing && $val)
                            <div class="mb-3 flex items-center gap-4">
                                <img src="{{ Cms::image($val) }}" alt="" class="h-20 w-32 rounded-lg object-cover ring-1 ring-ink-100">
                                <label class="flex items-center gap-2 text-sm text-ink-600"><input type="checkbox" name="remove_{{ $name }}" value="1" class="accent-beacon-500"> Remove this picture</label>
                            </div>
                        @endif
                        <input id="{{ $name }}" type="file" name="{{ $name }}" accept="image/jpeg,image/png,image/webp,image/gif" class="input">
                        <p class="mt-1 text-xs text-ink-400">{{ $f['hint'] ? $f['hint'] . ' ' : '' }}JPG, PNG, WebP or GIF, up to 4 MB.</p>
                        @error($name)<p class="mt-1.5 text-xs font-medium text-bad-600">{{ $message }}</p>@enderror
                    </div>
                    @break
                @case('date')
                    <x-field :name="$name" :label="$f['label']" type="date" :value="$val ? \Illuminate\Support\Carbon::parse($val)->format('Y-m-d') : null" :hint="$f['hint']" />
                    @break
                @default
                    <x-field :name="$name" :label="$f['label']" :type="$f['type'] === 'number' ? 'number' : ($f['type'] === 'url' ? 'url' : 'text')" :value="$val" :hint="$f['hint']" :disabled="$locked" />
            @endswitch
        @endforeach

        @if ($editing && $resource === 'pages' && $item->kind === 'content' && $item->is_published)
            <p class="text-sm"><a class="font-medium text-tide-700 underline" target="_blank" href="{{ $item->url() }}">View this page on the site</a></p>
        @endif

        <div class="flex gap-3 pt-2">
            <button class="btn btn-primary">{{ $editing ? 'Save changes' : 'Create' }}</button>
            <a href="{{ route('admin.website.index', $resource) }}" class="btn btn-ghost">Cancel</a>
        </div>
    </form>
</x-layouts.app>
