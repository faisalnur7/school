<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<style>
    body { font-family: sans-serif; font-size: 10px; color: #222; background: #fff; }
    h2 { text-align: center; margin-bottom: 2px; font-size: 14px; }
    p.sub { text-align: center; margin: 0 0 10px; font-size: 10px; color: #555; }
    .summary { width: 100%; border-collapse: collapse; margin-bottom: 14px; }
    .summary td { width: 33%; text-align: center; padding: 7px 10px; border: 1px solid #000; background: #fff; }
    .summary .label { font-size: 9px; color: #555; display: block; }
    .summary .value { font-size: 13px; font-weight: bold; display: block; }
    h4.class-title { background: #fff; color: #000; border: 1px solid #000; padding: 5px 8px; margin: 12px 0 0; font-size: 11px; }
    table { width: 100%; border-collapse: collapse; margin-bottom: 4px; }
    th, td { border: 1px solid #000; padding: 4px 6px; font-size: 9px; }
    th { background: #fff; color: #000; text-align: center; }
    th.left { text-align: left; }
    tr.due { background: #fff; }
    tr.paid { background: #fff; }
    tfoot td { font-weight: bold; background: #fff; }
    .text-right { text-align: right; }
    .text-center { text-align: center; }
</style>
</head>
<body>
<h2>Fee Due Report — Class &amp; Section Wise</h2>
<p class="sub">
    Academic Year: {{ $session?->name_en ?? '—' }}
    @if($month) &nbsp;|&nbsp; Month: {{ $month }} @endif
    &nbsp;|&nbsp; Generated: {{ now()->format('d M Y, h:i A') }}
</p>

<table class="summary">
    <tr>
        <td>
            <span class="label">Total Fees</span>
            <span class="value">{{ number_format($grandTotals['fees'], 2) }}</span>
        </td>
        <td>
            <span class="label">Total Paid</span>
            <span class="value">{{ number_format($grandTotals['paid'], 2) }}</span>
        </td>
        <td>
            <span class="label">Total Due</span>
            <span class="value">{{ number_format($grandTotals['due'], 2) }}</span>
        </td>
    </tr>
</table>

@php $byClass = $classSections->groupBy('class_id'); @endphp

@foreach($byClass as $classId => $sections)
    @php
        $className = $sections->first()->class_name;
        $classFees = $sections->sum('total_fees');
        $classPaid = $sections->sum('total_paid');
        $classDue  = $sections->sum('due');
        $activeCategories = $categories->filter(function($cat) use ($sections) {
            foreach ($sections as $sec) {
                if (($sec->cat_totals[$cat->id]['fees'] ?? 0) > 0) return true;
            }
            return false;
        });
    @endphp

    <h4 class="class-title">
        {{ $className }}
        &nbsp;&nbsp; Fees: {{ number_format($classFees, 2) }}
        &nbsp; Paid: {{ number_format($classPaid, 2) }}
        &nbsp; Due: {{ number_format($classDue, 2) }}
    </h4>

    <table>
        <thead>
            <tr>
                <th class="left" rowspan="2">#</th>
                <th class="left" rowspan="2">Section</th>
                @foreach($activeCategories as $cat)
                    <th colspan="3">{{ $cat->name }}</th>
                @endforeach
                <th colspan="3">Total</th>
            </tr>
            <tr>
                @foreach($activeCategories as $cat)
                    <th>Fees</th><th>Paid</th><th>Due</th>
                @endforeach
                <th>Fees</th><th>Paid</th><th>Due</th>
            </tr>
        </thead>
        <tbody>
            @foreach($sections as $i => $row)
                <tr class="{{ $row->due > 0 ? 'due' : 'paid' }}">
                    <td>{{ $i + 1 }}</td>
                    <td><strong>{{ $row->section_name }}</strong></td>
                    @foreach($activeCategories as $cat)
                        @php $ct = $row->cat_totals[$cat->id] ?? ['fees'=>0,'paid'=>0,'due'=>0]; @endphp
                        <td class="text-right">{{ $ct['fees'] > 0 ? number_format($ct['fees'], 2) : '—' }}</td>
                        <td class="text-right">{{ $ct['paid'] > 0 ? number_format($ct['paid'], 2) : '—' }}</td>
                        <td class="text-right">{{ $ct['fees'] > 0 ? number_format($ct['due'], 2) : '—' }}</td>
                    @endforeach
                    <td class="text-right"><strong>{{ number_format($row->total_fees, 2) }}</strong></td>
                    <td class="text-right">{{ number_format($row->total_paid, 2) }}</td>
                    <td class="text-right"><strong>{{ number_format($row->due, 2) }}</strong></td>
                </tr>
            @endforeach
        </tbody>
        @if($sections->count() > 1)
        <tfoot>
            <tr>
                <td colspan="2">Class Total</td>
                @foreach($activeCategories as $cat)
                    @php
                        $cf = $sections->sum(fn($s) => $s->cat_totals[$cat->id]['fees'] ?? 0);
                        $cp = $sections->sum(fn($s) => $s->cat_totals[$cat->id]['paid'] ?? 0);
                        $cd = $sections->sum(fn($s) => $s->cat_totals[$cat->id]['due'] ?? 0);
                    @endphp
                    <td class="text-right">{{ number_format($cf, 2) }}</td>
                    <td class="text-right">{{ number_format($cp, 2) }}</td>
                    <td class="text-right">{{ number_format($cd, 2) }}</td>
                @endforeach
                <td class="text-right">{{ number_format($classFees, 2) }}</td>
                <td class="text-right">{{ number_format($classPaid, 2) }}</td>
                <td class="text-right">{{ number_format($classDue, 2) }}</td>
            </tr>
        </tfoot>
        @endif
    </table>
@endforeach
</body>
</html>
