{{-- Card: <x-card title="…" :flush="true"> body <x-slot:actions>…</x-slot:actions> <x-slot:footer>…</x-slot:footer> </x-card> --}}
@props(['title' => null, 'flush' => false])
<section {{ $attributes->merge(['class' => 'ds-card']) }}>
    @if($title || isset($actions))
        <div class="ds-card__header">
            @if($title)<h2 class="ds-card__title">{{ $title }}</h2>@endif
            @isset($actions)<div class="d-flex flex-wrap" style="gap: var(--sp-2);">{{ $actions }}</div>@endisset
        </div>
    @endif
    <div class="ds-card__body {{ $flush ? 'ds-card__body--flush' : '' }}">{{ $slot }}</div>
    @isset($footer)
        <div class="ds-card__footer">{{ $footer }}</div>
    @endisset
</section>
