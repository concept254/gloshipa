@php
    $tabs = [['admin.website.hub', 'Overview', route('admin.website.hub')]];
    foreach (config('cms.resources') as $k => $d) { $tabs[] = ['res:' . $k, $d['label'], route('admin.website.index', $k)]; }
    $tabs[] = ['admin.website.inbox*', 'Inbox', route('admin.website.inbox')];
    $tabs[] = ['admin.website.subscribers*', 'Subscribers', route('admin.website.subscribers')];
    $tabs[] = ['admin.website.settings*', 'Settings', route('admin.website.settings')];
    $res = request()->route('resource');
@endphp
<nav class="mb-8 flex flex-wrap gap-1.5" aria-label="Website sections">
    @foreach ($tabs as [$match, $label, $href])
        @php $on = str_starts_with($match, 'res:') ? $res === substr($match, 4) : (request()->routeIs($match) && ! $res); @endphp
        <a href="{{ $href }}" class="rounded-lg px-3.5 py-2 text-sm font-medium {{ $on ? 'bg-ink-900 text-white' : 'bg-white text-ink-600 ring-1 ring-ink-100 hover:bg-ink-50' }}">{{ $label }}</a>
    @endforeach
</nav>
