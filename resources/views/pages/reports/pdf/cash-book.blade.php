@php $content = ''; ob_start(); @endphp
<table style="margin-bottom:8px;width:100%">
    <tr>
        <td style="padding:4px 10px;background:#f0fdf4;border:1px solid #bbf7d0;color:#000;font-weight:700; display:flex;">Cash In: {{ number_format($totalIn, 2) }}</td>
        <td style="padding:4px 10px;background:#fff1f2;border:1px solid #fecdd3;color:#000;font-weight:700; display:flex;">Cash Out: {{ number_format($totalOut, 2) }}</td>
        <td style="padding:4px 10px;background:#f1f5f9;border:1px solid #e2e8f0;font-weight:700; display:flex;">Balance: {{ number_format($totalIn - $totalOut, 2) }}</td>
    </tr>
</table>
<table>
    <thead>
        <tr>
            @if($reportType === 'summary')
                <th>Category / Head</th><th class="text-right">Cash In</th><th class="text-right">Cash Out</th>
            @else
                <th>Date</th><th>Reference</th><th>Type</th><th>Description</th><th class="text-right">Cash In</th><th class="text-right">Cash Out</th>
            @endif
        </tr>
    </thead>
    <tbody>
        @if($reportType === 'summary')
            <tr style="background:#eff6ff;font-weight:700">
                <td>Opening Balance</td>
                <td class="text-right">{{ number_format(abs($openingBalance), 2) }}</td>
                <td class="text-right">{{ number_format(abs($openingBalance), 2) }}</td>
            </tr>
            @forelse($summaryRows as $group)
                <tr>
                    <td>{{ $group['label'] }}</td>
                    <td class="text-right">{{ number_format($group['totalIn'], 2) }}</td>
                    <td class="text-right">{{ number_format($group['totalOut'], 2) }}</td>
                </tr>
            @empty
                <tr><td colspan="3" class="text-center muted">No cash transactions in this period</td></tr>
            @endforelse
            <tr style="background:#eff6ff;font-weight:700">
                <td>Closing Balance</td>
                <td class="text-right">{{ number_format(abs($closingBalance), 2) }}</td>
                <td class="text-right">{{ number_format(abs($closingBalance), 2) }}</td>
            </tr>
        @else
            @forelse($transactions as $transaction)
                <tr>
                    <td>{{ $transaction->transaction_date?->format('d/m/Y') }}</td>
                    <td>{{ $transaction->reference_no ?? '-' }}</td>
                    <td>{{ ucfirst($transaction->type) }}</td>
                    <td>{{ $transaction->description ?: '-' }}</td>
                    <td class="text-right">{{ in_array($transaction->type, ['income', 'capital']) ? number_format($transaction->amount, 2) : '0.00' }}</td>
                    <td class="text-right">{{ in_array($transaction->type, ['expense', 'withdrawal']) ? number_format($transaction->amount, 2) : '0.00' }}</td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-center muted">No cash transactions in this period</td></tr>
            @endforelse
        @endif
    </tbody>
</table>
@php $content = ob_get_clean(); @endphp
@include('pages.reports.pdf.layout', ['title' => 'Cash Book', 'subtitle' => $subtitle ?? ($from->format('d/m/Y') . ' — ' . $to->format('d/m/Y'))])
