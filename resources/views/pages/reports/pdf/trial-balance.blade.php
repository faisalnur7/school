@php $content = ''; ob_start(); @endphp
<table>
    <thead>
        <tr><th>Account</th><th class="text-right">Debit</th><th class="text-right">Credit</th></tr>
    </thead>
    <tbody>
        @foreach($rows as $row)
        <tr>
            <td>{{ $row['account'] }}</td>
            <td class="text-right {{ $row['debit'] > 0 ? 'red' : 'muted' }}">{{ $row['debit'] > 0 ? number_format($row['debit'], 2) : '—' }}</td>
            <td class="text-right {{ $row['credit'] > 0 ? 'green' : 'muted' }}">{{ $row['credit'] > 0 ? number_format($row['credit'], 2) : '—' }}</td>
        </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <td class="bold">Total</td>
            <td class="text-right red">{{ number_format($totalDebit, 2) }}</td>
            <td class="text-right green">{{ number_format($totalCredit, 2) }}</td>
        </tr>
    </tfoot>
</table>
@php $content = ob_get_clean(); @endphp
@include('pages.reports.pdf.layout', ['title' => 'Trial Balance', 'subtitle' => 'Year: ' . $year])
