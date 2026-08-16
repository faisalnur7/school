<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<style>
    body { font-family: sans-serif; font-size: 11px; color: #222; }
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
    h2 { text-align: center; margin-bottom: 2px; font-size: 14px; }
    h4 { font-size: 12px; margin: 15px 0 8px 0; border-bottom: 2px solid #333; padding-bottom: 4px; }
    p.sub { text-align: center; margin: 0 0 10px; font-size: 11px; color: #555; }
    table { width: 100%; border-collapse: collapse; margin-top: 8px; }
    th { background: #2d3748; color: #fff; padding: 6px 8px; text-align: left; }
    td { padding: 5px 8px; border-bottom: 1px solid #ddd; }
    th:nth-child(3), td:nth-child(3) { white-space: nowrap; }
    th.payment-report-column-header { white-space: normal; line-height: 1.15; text-align: center; }
    tfoot td { font-weight: bold; background: #f0f0f0; border-top: 2px solid #aaa; }
    .text-right { text-align: right; }
    .grand-total-box { margin: 15px 0; font-size: 12px; font-weight: 700; color: #0f172a; }
    .grand-total-box .amount { font-size: 12px; font-weight: 700; color: #0f172a; }
    .category-summary-box { margin: 12px 0 15px; }
    .category-summary-title { font-size: 12px; font-weight: 700; color: #0f172a; margin-bottom: 6px; }
    .category-summary-table { width: 100%; border-collapse: collapse; table-layout: fixed; }
    .category-summary-table th { font-size: 9px; line-height: 1.15; text-align: center; }
    .category-summary-table td { font-size: 9px; }
</style>
</head>
<body>
@include('partials.report-pdf-header')
<h2>Student Payment Report</h2>
<p class="sub">{{ $dateLabel ?? 'All Dates' }} | Generated: {{ now()->format('d M Y, h:i A') }}</p>

@php
    $totalGrandTotal = $rows->sum(fn($g) => $g->students->sum('grand_total'));
    $categoryTotals = $categories->mapWithKeys(function ($category) use ($rows) {
        return [
            $category->column_key => $rows->sum(function ($group) use ($category) {
                return $group->students->sum(fn ($student) => (float) ($student->{$category->column_key} ?? 0));
            }),
        ];
    });
    $categoryGrandTotal = $categoryTotals->sum();
@endphp

@if($categories->isEmpty())
    <p style="text-align:center; font-size:12px; margin-top:20px;">No columns selected.</p>
@else
    <div class="grand-total-box"><strong>Grand Total: {{ number_format($totalGrandTotal, 2) }}</strong></div>

    <div class="category-summary-box">
        <div class="category-summary-title">Categorywise Totals</div>
        <table class="category-summary-table">
            <thead>
                <tr>
                    @foreach($categories as $category)
                        <th>{!! $category->display_name_html ?? e($category->name) !!}</th>
                    @endforeach
                    <th>Grand<br>Total</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    @foreach($categories as $category)
                        <td class="text-right">{{ number_format($categoryTotals[$category->column_key] ?? 0, 2) }}</td>
                    @endforeach
                    <td class="text-right">{{ number_format($categoryGrandTotal, 2) }}</td>
                </tr>
            </tbody>
        </table>
    </div>

    @foreach($rows as $group)
        <h4>Class: {{ $group->class_name }} | Section: {{ $group->section_name }}</h4>

        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Student ID</th>
                    <th>Name</th>
                    @foreach($categories as $category)
                        <th class="payment-report-column-header">{!! $category->display_name_html ?? e($category->name) !!}</th>
                    @endforeach
                    <th class="payment-report-column-header">Grand<br>Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($group->students as $index => $row)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $row->student_cid }}</td>
                        <td>{{ $row->student_name }}</td>
                        @foreach($categories as $category)
                            <td class="text-right">{{ number_format($row->{$category->column_key}, 2) }}</td>
                        @endforeach
                        <td class="text-right">{{ number_format($row->grand_total, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="3">Subtotal</td>
                    @foreach($categories as $category)
                        <td class="text-right">{{ number_format($group->students->sum(fn($r) => $r->{$category->column_key}), 2) }}</td>
                    @endforeach
                    <td class="text-right">{{ number_format($group->students->sum('grand_total'), 2) }}</td>
                </tr>
            </tfoot>
        </table>
    @endforeach
@endif
</body>
</html>
