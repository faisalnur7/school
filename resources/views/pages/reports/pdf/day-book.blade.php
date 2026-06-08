@php $content = ''; ob_start(); @endphp
<table style="margin-bottom:8px;width:100%">
    <tr>
        <td style="padding:4px 10px;background:#fff1f2;border:1px solid #fecdd3;color:#e11d48;font-weight:700">Total Debit: {{ number_format($totalDebit, 2) }}</td>
        <td style="padding:4px 10px;background:#f0fdf4;border:1px solid #bbf7d0;color:#16a34a;font-weight:700">Total Credit: {{ number_format($totalCredit, 2) }}</td>
    </tr>
</table>
<table>
    <thead>
        <tr><th>Date</th><th>Reference</th><th>Description</th><th>Type</th><th>Debit Account</th><th>Credit Account</th><th class="text-right">Amount</th></tr>
    </thead>
    <tbody>
        @forelse($transactions as $txn)
        <tr>
            <td style="color:#64748b">{{ $txn->created_at->format('d/m/Y') }}</td>
            <td style="font-size:10px">{{ $txn->reference_no }}</td>
            <td>{{ $txn->description ?? '—' }}</td>
            <td>{{ ucfirst($txn->type) }}</td>
            <td class="red">{{ $txn->debit_account_name }}</td>
            <td class="green">{{ $txn->credit_account_name }}</td>
            <td class="text-right bold">{{ number_format($txn->amount, 2) }}</td>
        </tr>
        @empty
        <tr><td colspan="6" class="text-center muted">No transactions on this date</td></tr>
        @endforelse
    </tbody>
</table>
@php $content = ob_get_clean(); @endphp
@include('pages.reports.pdf.layout', ['title' => 'Day Book', 'subtitle' => $date->format('d/m/Y')])
