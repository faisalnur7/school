@php $content = ''; ob_start(); @endphp
<table style="margin-bottom:8px;width:100%">
    <tr>
        <td style="padding:4px 10px;background:#f8fafc;border:1px solid #e2e8f0;color:#334155;font-weight:700">Opening Balance: {{ number_format($openingBalance, 2) }}</td>
        <td style="padding:4px 10px;background:#f8fafc;border:1px solid #e2e8f0;color:#334155;font-weight:700">Closing Balance: {{ number_format($closingBalance, 2) }}</td>
    </tr>
</table>
<div class="two-col">
    <div class="col">
        <div class="section-title green-bar">Receipts</div>
        <table>
            <thead><tr><th>Head</th><th class="text-right">Amount</th></tr></thead>
            <tbody>
                @forelse($receipts as $r)
                <tr><td>{{ $r['head'] }}</td><td class="text-right">{{ number_format($r['amount'], 2) }}</td></tr>
                @empty
                <tr><td colspan="2" class="text-center muted">No receipts</td></tr>
                @endforelse
                <tr style="background:#ecfeff">
                    <td class="bold" style="color:#0f766e">Total Inventory Sales</td>
                    <td class="text-right bold" style="color:#0f766e">{{ number_format($totalInventoryReceipts, 2) }}</td>
                </tr>
            </tbody>
            <tfoot>
                <tr><td class="bold">Total Receipts</td><td class="text-right green bold">{{ number_format($grandTotalReceipts, 2) }}</td></tr>
            </tfoot>
        </table>
    </div>
    <div class="col">
        <div class="section-title red-bar">Payments</div>
        <table>
            <thead><tr><th>Head</th><th class="text-right">Amount</th></tr></thead>
            <tbody>
                @forelse($payments as $p)
                <tr><td>{{ $p['head'] }}</td><td class="text-right">{{ number_format($p['amount'], 2) }}</td></tr>
                @empty
                <tr><td colspan="2" class="text-center muted">No payments</td></tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr><td class="bold">Total Payments</td><td class="text-right red bold">{{ number_format($totalPayments, 2) }}</td></tr>
            </tfoot>
        </table>
    </div>
</div>
@php $net = $grandTotalReceipts - $totalPayments; @endphp
<div class="net-bar" style="background:{{ $net >= 0 ? '#f0fdf4' : '#fff1f2' }};border-top:2px solid {{ $net >= 0 ? '#bbf7d0' : '#fecdd3' }}">
    <span>Net Surplus / (Deficit)</span>
    <span class="{{ $net >= 0 ? 'green' : 'red' }}" style="font-size:13px">
        {{ $net >= 0 ? number_format($net, 2) : '(' . number_format(abs($net), 2) . ')' }}
    </span>
</div>
@php $content = ob_get_clean(); @endphp
@include('pages.reports.pdf.layout', ['title' => 'Receipt & Payment Statement', 'subtitle' => $from->format('d/m/Y') . ' — ' . $to->format('d/m/Y')])
