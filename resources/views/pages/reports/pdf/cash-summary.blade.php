@php $content = ''; ob_start(); @endphp
<table>
    <thead>
        <tr>
            <th>Account</th>
            <th class="text-right">Opening Balance</th>
            <th class="text-right">Total In (Credit)</th>
            <th class="text-right">Total Out (Debit)</th>
            <th class="text-right">Closing Balance</th>
        </tr>
    </thead>
    <tbody>
        @forelse($accounts as $acc)
        <tr>
            <td class="bold">{{ $acc['label'] }}</td>
            <td class="text-right">{{ number_format($acc['openingBalance'], 2) }}</td>
            <td class="text-right green">{{ number_format($acc['totalIn'], 2) }}</td>
            <td class="text-right red">{{ number_format($acc['totalOut'], 2) }}</td>
            <td class="text-right bold {{ $acc['closingBalance'] >= 0 ? 'green' : 'red' }}">{{ number_format($acc['closingBalance'], 2) }}</td>
        </tr>
        @empty
        <tr><td colspan="5" class="text-center muted">No active accounts found</td></tr>
        @endforelse
    </tbody>
    @if($accounts->count())
    <tfoot>
        <tr>
            <td class="bold">Total</td>
            <td class="text-right bold">{{ number_format($accounts->sum('openingBalance'), 2) }}</td>
            <td class="text-right green bold">{{ number_format($accounts->sum('totalIn'), 2) }}</td>
            <td class="text-right red bold">{{ number_format($accounts->sum('totalOut'), 2) }}</td>
            <td class="text-right bold {{ $accounts->sum('closingBalance') >= 0 ? 'green' : 'red' }}">{{ number_format($accounts->sum('closingBalance'), 2) }}</td>
        </tr>
    </tfoot>
    @endif
</table>
@php $content = ob_get_clean(); @endphp
@include('pages.reports.pdf.layout', ['title' => 'Cash Summary', 'subtitle' => $from->format('d/m/Y') . ' — ' . $to->format('d/m/Y')])
