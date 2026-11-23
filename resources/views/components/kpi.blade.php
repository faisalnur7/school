{{-- KPI tile: <x-kpi :label="__('Present')" :value="$present" tone="success|danger|warning" hint="…" /> --}}
@props(['label', 'value', 'hint' => null, 'tone' => null])
<div {{ $attributes->merge(['class' => 'ds-kpi' . ($tone ? ' ds-kpi--' . $tone : '')]) }}>
    <div class="ds-kpi__label">{{ $label }}</div>
    <div class="ds-kpi__value">{{ $value }}</div>
    @if($hint)<div class="ds-kpi__hint">{{ $hint }}</div>@endif
</div>
