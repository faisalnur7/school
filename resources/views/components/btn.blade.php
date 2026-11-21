{{--
    Button: <x-btn variant="primary|secondary|danger|ghost" size="sm" icon="fas fa-plus" href="…">Label</x-btn>
    Icon-only: <x-btn icon="fas fa-trash" label="Delete class" variant="danger" type="submit" />  (label becomes aria-label + title)
--}}
@props(['variant' => 'secondary', 'size' => null, 'href' => null, 'icon' => null, 'label' => null, 'type' => 'button'])
@php
    $iconOnly = trim($slot) === '' && $icon;
    $classes = 'ds-btn ds-btn--' . $variant . ($size === 'sm' ? ' ds-btn--sm' : '') . ($iconOnly ? ' ds-btn--icon' : '');
    $a11y = $label ? ['aria-label' => $label, 'title' => $label] : [];
@endphp
@if($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes] + $a11y) }}>
        @if($icon)<i class="{{ $icon }}" aria-hidden="true"></i>@endif{{ $slot }}
    </a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => $classes] + $a11y) }}>
        @if($icon)<i class="{{ $icon }}" aria-hidden="true"></i>@endif{{ $slot }}
    </button>
@endif
