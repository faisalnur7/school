<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<style>
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body { font-family: sans-serif; font-size: 10px; color: #1e293b; }
    .pdf-header { background: #1e293b; color: #fff; padding: 8px 12px; margin-bottom: 10px; }
    .pdf-header h1 { font-size: 14px; font-weight: 700; }
    .pdf-header .meta { font-size: 9px; color: #94a3b8; margin-top: 2px; }
    .summary { display: table; width: 100%; margin-bottom: 10px; border-collapse: collapse; }
    .summary td { display: table-cell; padding: 5px 10px; border: 1px solid #e2e8f0; text-align: center; }
    .summary .label { font-size: 9px; color: #64748b; }
    .summary .value { font-size: 13px; font-weight: 700; }
    table { width: 100%; border-collapse: collapse; font-size: 10px; }
    th { background: #f1f5f9; color: #334155; padding: 5px 6px; text-align: left; border-bottom: 2px solid #e2e8f0; font-weight: 600; }
    td { padding: 4px 6px; border-bottom: 1px solid #e2e8f0; }
    tfoot td { background: #f8fafc; font-weight: 700; border-top: 2px solid #cbd5e1; }
    .text-right { text-align: right; }
    .green { color: #16a34a; }
    .red   { color: #e11d48; }
    .blue  { color: #2563eb; }
    .amber { color: #ca8a04; }
    .muted { color: #94a3b8; }
    .mono  { font-family: monospace; font-size: 9px; }
    .badge { padding: 2px 5px; border-radius: 3px; font-size: 9px; }
</style>
</head>
<body>

<div class="pdf-header">
    <h1>Transaction Report</h1>
    <div class="meta">Generated: {{ now()->format('d M Y, h:i A') }}</div>
</div>

<table class="summary" style="margin-bottom:10px">
    <tr>
        <td><div class="label">Income</div><div class="value green">{{ number_format($totalIncome, 2) }}</div></td>
        <td><div class="label">Expense</div><div class="value red">{{ number_format($totalExpense, 2) }}</div></td>
        <td><div class="label">Capital</div><div class="value blue">{{ number_format($totalCapital, 2) }}</div></td>
        <td><div class="label">Withdrawal</div><div class="value amber">{{ number_format($totalWithdrawal, 2) }}</div></td>
        @php $net = ($totalIncome + $totalCapital) - ($totalExpense + $totalWithdrawal); @endphp
        <td><div class="label">Net</div><div class="value {{ $net >= 0 ? 'green' : 'red' }}">{{ number_format($net, 2) }}</div></td>
    </tr>
</table>

<table>
    <thead>
        <tr>
            <th>#</th>
            <th>Date</th>
            <th>Reference</th>
            <th>Type</th>
            <th>Category / Shareholder</th>
            <th>Description</th>
            <th>Method</th>
            <th class="text-right">Debit</th>
            <th class="text-right">Credit</th>
            <th>Recorded By</th>
        </tr>
    </thead>
    <tbody>
        @forelse($transactions as $i => $txn)
        @php $isCredit = in_array($txn->type, ['income', 'capital']); @endphp
        <tr>
            <td>{{ $i + 1 }}</td>
            <td class="muted">{{ $txn->transaction_date->format('d/m/Y') }}</td>
            <td class="mono">{{ $txn->reference_no ?? '—' }}</td>
            <td>
                <span class="badge" style="background:{{ match($txn->type){ 'income'=>'#f0fdf4','expense'=>'#fff1f2','capital'=>'#eff6ff','withdrawal'=>'#fefce8',default=>'#f1f5f9' } }};color:{{ match($txn->type){ 'income'=>'#16a34a','expense'=>'#e11d48','capital'=>'#2563eb','withdrawal'=>'#ca8a04',default=>'#475569' } }}">
                    {{ ucfirst($txn->type) }}
                </span>
            </td>
            <td>
                @if($txn->type === 'income')      {{ $txn->incomeCategory?->name ?? '—' }}
                @elseif($txn->type === 'expense')  {{ $txn->expenseCategory?->name ?? '—' }}
                @else                              {{ $txn->shareholder?->name ?? '—' }}
                @endif
            </td>
            <td>{{ $txn->description ?? '—' }}</td>
            <td>{{ $txn->payment_method ?? '—' }}</td>
            <td class="text-right red">{{ !$isCredit ? number_format($txn->amount, 2) : '—' }}</td>
            <td class="text-right green">{{ $isCredit ? number_format($txn->amount, 2) : '—' }}</td>
            <td class="muted">{{ $txn->recorder?->name ?? '—' }}</td>
        </tr>
        @empty
        <tr><td colspan="10" style="text-align:center;color:#94a3b8;padding:12px">No transactions found</td></tr>
        @endforelse
    </tbody>
    <tfoot>
        <tr>
            <td colspan="7">Total ({{ $transactions->count() }} records)</td>
            <td class="text-right red">{{ number_format($totalExpense + $totalWithdrawal, 2) }}</td>
            <td class="text-right green">{{ number_format($totalIncome + $totalCapital, 2) }}</td>
            <td></td>
        </tr>
    </tfoot>
</table>

</body>
</html>
