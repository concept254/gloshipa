@props(['name', 'label', 'required' => false])
<div {{ $attributes->only('class') }}>
    <label for="{{ $name }}" class="label">{{ $label }}@if ($required)<span class="text-beacon-700"> *</span>@endif</label>
    <select id="{{ $name }}" name="{{ $name }}" @required($required)
        {{ $attributes->except('class')->merge(['class' => 'input' . ($errors->has($name) ? ' border-bad-600' : '')]) }}>
        {{ $slot }}
    </select>
    @error($name)<p class="mt-1.5 text-xs font-medium text-bad-600">{{ $message }}</p>@enderror
</div>
