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
    .summary { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
    .summary td { padding: 5px 10px; border: 1px solid #e2e8f0; text-align: center; }
    .summary .label { font-size: 9px; color: #64748b; }
    .summary .value { font-size: 13px; font-weight: 700; }
    table { width: 100%; border-collapse: collapse; font-size: 10px; }
    th { background: #f1f5f9; color: #334155; padding: 5px 6px; text-align: left; border-bottom: 2px solid #e2e8f0; font-weight: 600; }
    td { padding: 4px 6px; border-bottom: 1px solid #e2e8f0; }
    tfoot td { background: #f8fafc; font-weight: 700; border-top: 2px solid #cbd5e1; }
    .text-right { text-align: right; }
    .green { color: #16a34a; } .red { color: #e11d48; } .blue { color: #2563eb; }
    .over { background: #fff1f2; }
    .bar-wrap { background: #e2e8f0; border-radius: 3px; height: 8px; width: 80px; display: inline-block; vertical-align: middle; }
    .bar-fill { height: 8px; border-radius: 3px; display: inline-block; }
</style>
</head>
<body>
<div class="pdf-header">
    <h1>Budget vs Actual — {{ $year }}</h1>
    <div class="meta">Generated: {{ now()->format('d M Y, h:i A') }}</div>
</div>

<table class="summary" style="margin-bottom:10px">
    <tr>
        <td><div class="label">Total Budget</div><div class="value blue">{{ number_format($totalBudget, 2) }}</div></td>
        <td><div class="label">Total Actual</div><div class="value red">{{ number_format($totalActual, 2) }}</div></td>
        @php $rem = $totalBudget - $totalActual; @endphp
        <td><div class="label">{{ $rem >= 0 ? 'Remaining' : 'Over Budget' }}</div>
            <div class="value {{ $rem >= 0 ? 'green' : 'red' }}">{{ number_format(abs($rem), 2) }}</div></td>
        @php $util = $totalBudget > 0 ? round(($totalActual / $totalBudget) * 100, 1) : 0; @endphp
        <td><div class="label">Overall Utilization</div><div class="value">{{ $util }}%</div></td>
    </tr>
</table>

<table>
    <thead>
        <tr>
            <th>#</th>
            <th>Account</th>
            <th>Group</th>
            <th>Category</th>
            <th>Period</th>
            <th class="text-right">Budget</th>
            <th class="text-right">Actual</th>
            <th class="text-right">Remaining</th>
            <th>Utilization</th>
        </tr>
    </thead>
    <tbody>
        @foreach($allocations as $i => $a)
        @php $over = $a['actual'] > $a['budget']; @endphp
        <tr class="{{ $over ? 'over' : '' }}">
            <td>{{ $i + 1 }}</td>
            <td><strong>{{ $a['account'] }}</strong></td>
            <td style="color:#64748b">{{ $a['group'] }}</td>
            <td>{{ $a['category'] }}</td>
            <td>{{ ucfirst($a['period']) }}{{ $a['month'] ? ' / ' . $a['month'] : '' }}</td>
            <td class="text-right blue">{{ number_format($a['budget'], 2) }}</td>
            <td class="text-right {{ $over ? 'red' : '' }}">{{ number_format($a['actual'], 2) }}</td>
            <td class="text-right {{ $over ? 'red' : 'green' }}">
                {{ $over ? '(' . number_format($a['actual'] - $a['budget'], 2) . ')' : number_format($a['remaining'], 2) }}
            </td>
            <td>
                <span class="bar-wrap">
                    <span class="bar-fill" style="width:{{ min($a['utilization'], 100) }}%;background:{{ $over ? '#e11d48' : ($a['utilization'] > 80 ? '#f59e0b' : '#16a34a') }}"></span>
                </span>
                <span style="margin-left:4px;color:{{ $over ? '#e11d48' : '#475569' }}">{{ $a['utilization'] }}%</span>
            </td>
        </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <td colspan="5">Total</td>
            <td class="text-right blue">{{ number_format($totalBudget, 2) }}</td>
            <td class="text-right {{ $totalActual > $totalBudget ? 'red' : '' }}">{{ number_format($totalActual, 2) }}</td>
            <td class="text-right {{ $rem >= 0 ? 'green' : 'red' }}">
                {{ $rem >= 0 ? number_format($rem, 2) : '(' . number_format(abs($rem), 2) . ')' }}
            </td>
            <td></td>
        </tr>
    </tfoot>
</table>
</body>
</html>
