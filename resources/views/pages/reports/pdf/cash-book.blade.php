@php $content = ''; ob_start(); @endphp
<table style="margin-bottom:8px;width:100%">
    <tr>
        <td style="padding:4px 10px;background:#f0fdf4;border:1px solid #bbf7d0;color:#16a34a;font-weight:700; display:flex;">Cash In: {{ number_format($totalIn, 2) }}</td>
        <td style="padding:4px 10px;background:#fff1f2;border:1px solid #fecdd3;color:#e11d48;font-weight:700; display:flex;">Cash Out: {{ number_format($totalOut, 2) }}</td>
        <td style="padding:4px 10px;background:#f1f5f9;border:1px solid #e2e8f0;font-weight:700; display:flex;">Balance: {{ number_format($totalIn - $totalOut, 2) }}</td>
    </tr>
</table>
<table>
    <thead>
        <tr><th>Date</th><th>Reference</th><th>Description</th><th>Type</th><th class="text-right">Cash In</th><th class="text-right">Cash Out</th></tr>
    </thead>
    <tbody>
        @forelse($transactions as $txn)
        @php $isIn = in_array($txn->type, ['income','capital']); @endphp
        <tr>
            <td>{{ $txn->transaction_date->format('d/m/Y') }}</td>
            <td style="font-size:10px">{{ $txn->reference_no }}</td>
            <td>{{ $txn->description ?? '—' }}</td>
            <td>{{ ucfirst($txn->type) }}</td>
            <td class="text-right green">{{ $isIn ? number_format($txn->amount, 2) : '—' }}</td>
            <td class="text-right red">{{ !$isIn ? number_format($txn->amount, 2) : '—' }}</td>
        </tr>
        @empty
        <tr><td colspan="6" class="text-center muted">No cash transactions in this period</td></tr>
        @endforelse
    </tbody>
    <tfoot>
        <tr>
            <td colspan="4" class="bold">Total</td>
            <td class="text-right green bold">{{ number_format($totalIn, 2) }}</td>
            <td class="text-right red bold">{{ number_format($totalOut, 2) }}</td>
        </tr>
    </tfoot>
</table>
@php $content = ob_get_clean(); @endphp
@include('pages.reports.pdf.layout', ['title' => 'Cash Book', 'subtitle' => $from->format('d/m/Y') . ' — ' . $to->format('d/m/Y')])
