@php $content = ''; ob_start(); @endphp
<table style="margin-bottom:8px;width:100%">
    <tr>
        <td style="padding:4px 10px;background:#f8fafc;border:1px solid #e2e8f0;color:#334155;font-weight:700">Opening Balance: {{ number_format($openingBalance, 2) }}</td>
        <td style="padding:4px 10px;background:#f8fafc;border:1px solid #e2e8f0;color:#334155;font-weight:700">Closing Balance: {{ number_format($closingBalance, 2) }}</td>
    </tr>
</table>

<div class="section-title green-bar">Income Heads</div>
@forelse($incomeHeads as $head)
<div style="background:#f8fafc;padding:4px 8px;border-bottom:1px solid #e2e8f0;font-weight:700;display:flex;justify-content:space-between">
    <span>{{ $head->name }}</span>
    <span class="green">{{ number_format($head->total, 2) }}</span>
</div>
<table style="margin-bottom:0">
    <thead>
        <tr>
            <th style="padding-left:20px">Date</th>
            <th>Reference</th>
            <th>Description</th>
            <th>Method</th>
            <th class="text-right">Amount</th>
        </tr>
    </thead>
    <tbody>
        @foreach($head->transactions as $txn)
        <tr>
            <td style="padding-left:20px;color:#64748b">{{ $txn->transaction_date->format('d/m/Y') }}</td>
            <td style="font-size:10px">{{ $txn->reference_no }}</td>
            <td>{{ $txn->description ?? '—' }}</td>
            <td>{{ $txn->payment_method }}</td>
            <td class="text-right green">{{ number_format($txn->amount, 2) }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@empty
<p style="padding:4px 8px;color:#94a3b8">No income transactions in this period</p>
@endforelse
@if($incomeHeads->count())
<div style="background:#dcfce7;padding:5px 8px;display:flex;justify-content:space-between;font-weight:700;border-top:2px solid #bbf7d0;margin-bottom:12px">
    <span class="green">Total Income</span><span class="green">{{ number_format($incomeHeads->sum('total'), 2) }}</span>
</div>
@endif

<div class="section-title red-bar" style="margin-top:10px">Expense Heads</div>
@forelse($expenseHeads as $head)
<div style="background:#f8fafc;padding:4px 8px;border-bottom:1px solid #e2e8f0;font-weight:700;display:flex;justify-content:space-between">
    <span>{{ $head->name }}</span>
    <span class="red">{{ number_format($head->total, 2) }}</span>
</div>
<table style="margin-bottom:0">
    <thead>
        <tr>
            <th style="padding-left:20px">Date</th>
            <th>Reference</th>
            <th>Description</th>
            <th>Method</th>
            <th class="text-right">Amount</th>
        </tr>
    </thead>
    <tbody>
        @foreach($head->transactions as $txn)
        <tr>
            <td style="padding-left:20px;color:#64748b">{{ $txn->transaction_date->format('d/m/Y') }}</td>
            <td style="font-size:10px">{{ $txn->reference_no }}</td>
            <td>{{ $txn->description ?? '—' }}</td>
            <td>{{ $txn->payment_method }}</td>
            <td class="text-right red">{{ number_format($txn->amount, 2) }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@empty
<p style="padding:4px 8px;color:#94a3b8">No expense transactions in this period</p>
@endforelse
@if($expenseHeads->count())
<div style="background:#fee2e2;padding:5px 8px;display:flex;justify-content:space-between;font-weight:700;border-top:2px solid #fecdd3;margin-bottom:12px">
    <span class="red">Total Expenses</span><span class="red">{{ number_format($expenseHeads->sum('total'), 2) }}</span>
</div>
@endif

@php $net = $incomeHeads->sum('total') - $expenseHeads->sum('total'); @endphp
<div class="net-bar" style="background:#f1f5f9;border-top:2px solid #e2e8f0">
    <span class="bold">Net Surplus / (Deficit)</span>
    <span class="bold {{ $net >= 0 ? 'green' : 'red' }}" style="font-size:13px">
        {{ $net >= 0 ? number_format($net, 2) : '(' . number_format(abs($net), 2) . ')' }}
    </span>
</div>
@php $content = ob_get_clean(); @endphp
@include('pages.reports.pdf.layout', ['title' => 'Headwise Transaction List', 'subtitle' => $from->format('d/m/Y') . ' — ' . $to->format('d/m/Y')])
