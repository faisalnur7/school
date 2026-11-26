{{--
    Filter bar (GET form): <x-filter-bar :action="route('payments.index')" :reset="route('payments.index')">
        <label class="ds-field"><span class="ds-field__label">From</span><input class="ds-input" …></label>
    </x-filter-bar>
--}}
@props(['action', 'reset' => null, 'submitLabel' => null])
<form method="GET" action="{{ $action }}" {{ $attributes->merge(['class' => 'ds-filter-bar']) }}>
    {{ $slot }}
    <div class="ds-filter-bar__actions">
        <x-btn type="submit" variant="primary" icon="fas fa-search">{{ $submitLabel ?? __('Search') }}</x-btn>
        @if($reset)
            <x-btn :href="$reset" variant="secondary" icon="fas fa-undo" :label="__('Reset filters')" />
        @endif
    </div>
</form>
