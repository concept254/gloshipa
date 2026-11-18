{{-- Global Shipping Alliance logo (transparent SVG). Only ONE image is ever shown:
     phones get the compact wordmark, screens 640px and wider get the full logo with its tagline.
     :dark="true"     => white wordmark, for dark backgrounds
     :compact="true"  => always the wordmark only
     height="3.25rem" => logo height on desktop (phones use 2.25rem) --}}
@props(['dark' => false, 'compact' => false, 'height' => '3.25rem'])
@php $suffix = $dark ? '-light' : ''; @endphp
@once
    <style>.gs-logo{display:block;width:auto;height:2.25rem}@media (min-width:640px){.gs-logo{height:var(--gs-logo-h,3.25rem)}}</style>
@endonce
<span {{ $attributes->class(['inline-flex items-center']) }}>
    <picture>
        @unless ($compact)
            <source media="(min-width: 640px)" srcset="{{ asset('images/logo/logo' . $suffix . '.svg') }}">
        @endunless
        <img src="{{ asset('images/logo/logo-compact' . $suffix . '.svg') }}" alt="Global Shipping Alliance" class="gs-logo" style="--gs-logo-h: {{ $compact ? '2.25rem' : $height }}">
    </picture>
</span>