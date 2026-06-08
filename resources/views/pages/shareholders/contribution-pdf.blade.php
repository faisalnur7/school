<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<style>
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body { font-family: sans-serif; font-size: 11px; color: #1e293b; }
    .school-header-wrap { border: 1px solid #cbd5e1; border-radius: 8px; padding: 8px 10px; margin-bottom: 10px; }
    .school-header-table { width: 100%; border-collapse: collapse; table-layout: fixed; }
    .school-header-table td { border: 0 !important; padding: 0 !important; vertical-align: middle; }
    .school-header-logo-cell { width: 62px; }
    .school-header-info-cell { padding-left: 10px !important; }
    .school-logo-box { width: 52px; height: 52px; border: 1px solid #cbd5e1; border-radius: 8px; text-align: center; vertical-align: middle; line-height: 50px; overflow: hidden; background: #fff; }
    .school-logo-img { max-width: 50px; max-height: 50px; display: inline-block; vertical-align: middle; }
    .school-logo-fallback { font-size: 20px; font-weight: 700; color: #334155; }
    .school-title { font-size: 16px; font-weight: 700; color: #0f172a; margin-top: 1px; }
    .school-line { font-size: 10px; color: #334155; margin-top: 2px; }
    .report-title { font-size: 13px; font-weight: 700; color: #0e7490; }
    .report-date { font-size: 9px; color: #64748b; margin-top: 3px; text-align: right; }
    table { width: 100%; border-collapse: collapse; margin-top: 10px; }
    th { background: #fef3c7; color: #78350f; padding: 6px 8px; text-align: left; border-bottom: 2px solid #fcd34d; font-weight: 700; font-size: 10px; }
    th.right { text-align: right; }
    td { padding: 5px 8px; border-bottom: 1px solid #e2e8f0; font-size: 10px; }
    td.right { text-align: right; }
    tfoot td { background: #fef9c3; font-weight: 700; border-top: 2px solid #fcd34d; }
</style>
</head>
<body>

@include('partials.report-pdf-header')
<div style="margin-bottom:12px">
    <div class="report-title">Shareholder Contribution Report</div>
    <div class="report-date">Generated: {{ now()->format('d M Y, h:i A') }}</div>
</div>

<table>
    <thead>
        <tr>
            <th>#</th>
            <th>Shareholder Name</th>
            <th class="right">Investment (৳)</th>
            <th class="right">Share (%)</th>
        </tr>
    </thead>
    <tbody>
        @forelse($shareholders as $i => $s)
        @php $pct = $totalCapital > 0 ? ($s['capital'] / $totalCapital) * 100 : 0; @endphp
        <tr>
            <td>{{ $i + 1 }}</td>
            <td>{{ $s['name'] }}</td>
            <td class="right">{{ number_format($s['capital'], 2) }}</td>
            <td class="right">{{ number_format($pct, 1) }}%</td>
        </tr>
        @empty
        <tr><td colspan="4" style="text-align:center;color:#94a3b8;padding:12px">No shareholders found</td></tr>
        @endforelse
    </tbody>
    @if($shareholders->isNotEmpty())
    <tfoot>
        <tr>
            <td colspan="2">Total</td>
            <td class="right">{{ number_format($totalCapital, 2) }}</td>
            <td class="right">100.0%</td>
        </tr>
    </tfoot>
    @endif
</table>

</body>
</html>
