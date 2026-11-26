{{-- Page header: <x-page-header title="Payments" :count="$payments->total()" subtitle="…"> actions </x-page-header> --}}
@props(['title', 'subtitle' => null, 'count' => null])
<header {{ $attributes->merge(['class' => 'ds-page-header']) }}>
    <div>
        <h1 class="ds-page-header__title">
            {{ $title }}
            @if(! is_null($count))
                <span class="ds-page-header__count">{{ localized_number($count) }}</span>
            @endif
        </h1>
        @if($subtitle)
            <p class="ds-page-header__subtitle">{{ $subtitle }}</p>
        @endif
    </div>
    @if(trim($slot) !== '')
        <div class="ds-page-header__actions">{{ $slot }}</div>
    @endif
</header>
