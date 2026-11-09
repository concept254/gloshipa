@props(['name', 'label', 'type' => 'text', 'value' => null, 'required' => false, 'hint' => null])
<div {{ $attributes->only('class') }}>
    <label for="{{ $name }}" class="label">{{ $label }}@if ($required)<span class="text-beacon-700"> *</span>@endif</label>
    @if ($type === 'textarea')
        <textarea id="{{ $name }}" name="{{ $name }}" rows="3" @required($required)
            {{ $attributes->except('class')->merge(['class' => 'input' . ($errors->has($name) ? ' border-bad-600' : '')]) }}>{{ old($name, $value) }}</textarea>
    @else
        <input id="{{ $name }}" name="{{ $name }}" type="{{ $type }}"
            @if ($type !== 'password') value="{{ old($name, $value) }}" @endif
            @required($required)
            {{ $attributes->except('class')->merge(['class' => 'input' . ($errors->has($name) ? ' border-bad-600' : '')]) }}>
    @endif
    @if ($hint)<p class="mt-1 text-xs text-ink-400">{{ $hint }}</p>@endif
    @error($name)<p class="mt-1.5 text-xs font-medium text-bad-600">{{ $message }}</p>@enderror
</div>
