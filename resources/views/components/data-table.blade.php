{{--
    Data table: <x-data-table :empty="$rows->isEmpty()" empty-text="No payments yet" :stack="true">
    stack: on phones each row becomes a card; give each <td> a data-label="Column name".
        <x-slot:head><th>…</th></x-slot:head>
        …<tr>…</tr>
        <x-slot:footer>{{ $rows->links() }}</x-slot:footer>
    </x-data-table>
--}}
@props(['empty' => false, 'emptyText' => null, 'caption' => null, 'stack' => false])
<div {{ $attributes->merge(['class' => 'ds-table-wrap']) }}>
    <table class="ds-table {{ $stack ? 'ds-table--stack' : '' }}">
        @if($caption)<caption class="sr-only">{{ $caption }}</caption>@endif
        @isset($head)
            <thead><tr>{{ $head }}</tr></thead>
        @endisset
        <tbody>
            @if($empty)
                <tr><td colspan="99" class="ds-table__empty">{{ $emptyText ?? __('Nothing to show yet.') }}</td></tr>
            @else
                {{ $slot }}
            @endif
        </tbody>
    </table>
</div>
@isset($footer)
    @if(trim($footer) !== '')
        <div class="ds-table__footer">{{ $footer }}</div>
    @endif
@endisset
