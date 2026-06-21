<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<style>
    @page { size: A4 landscape; margin: 8mm; }
    body {
        font-family: DejaVu Sans, sans-serif;
        font-size: 10px;
        color: #111827;
        background: #ffffff;
        margin: 0;
    }
    h1, h2, h3, h4, p { margin: 0; }
    .header {
        text-align: center;
        margin-bottom: 10px;
        padding: 8px 10px 10px;
        border: 1px solid #dbe2ea;
        border-radius: 10px;
        background: linear-gradient(180deg, #f8fafc 0%, #ffffff 100%);
    }
    .header h1 { font-size: 16px; font-weight: 700; letter-spacing: .01em; }
    .header .sub { font-size: 10px; color: #4b5563; margin-top: 4px; }
    .section { margin-bottom: 12px; }
    .section.section-break { page-break-before: always; break-before: page; }
    .section-title {
        font-size: 12px;
        font-weight: 700;
        margin-bottom: 6px;
        padding: 7px 10px;
        border: 1px solid #0f172a;
        border-radius: 8px;
        background: #0f172a;
        color: #fff;
    }
    .summary {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 8px;
        table-layout: fixed;
    }
    .summary td {
        border: 1px solid #dbe2ea;
        padding: 7px 8px;
        text-align: center;
        background: #f8fafc;
    }
    .summary .label {
        display: block;
        font-size: 8.5px;
        color: #64748b;
        text-transform: uppercase;
        margin-bottom: 2px;
        letter-spacing: .04em;
    }
    .summary .value { display: block; font-size: 12px; font-weight: 700; color: #0f172a; }
    .table-wrap { width: 100%; overflow: hidden; }
    table {
        width: 100%;
        border-collapse: collapse;
        table-layout: fixed;
    }
    th, td {
        border: 1px solid #dbe2ea;
        padding: 4px 5px;
        vertical-align: top;
        word-break: break-word;
    }
    thead th { background: #e2e8f0; font-size: 8.7px; color: #0f172a; }
    tbody td { font-size: 8.7px; }
    tbody tr:nth-child(even) { background: #fbfdff; }
    tfoot td { background: #f1f5f9; font-weight: 700; }
    .text-right { text-align: right; }
    .text-center { text-align: center; }
    .muted { color: #6b7280; }
    .group-title {
        font-size: 10px;
        font-weight: 700;
        margin: 8px 0 4px;
        color: #0f172a;
    }
    .subtle { font-size: 9px; color: #6b7280; }
    .badge {
        display: inline-block;
        padding: 1px 5px;
        border: 1px solid #0f172a;
        font-size: 8px;
        line-height: 1.2;
        border-radius: 999px;
        color: #0f172a;
        background: #fff;
    }
    .student-block,
    .class-block {
        page-break-inside: avoid;
        break-inside: avoid-page;
        margin-bottom: 8px;
    }
</style>
</head>
<body>
    @php
        $generatedAt = now()->format('d M Y, h:i A');
    @endphp
    <div class="header">
        <h1>All In One Fee Report</h1>
        <div class="sub">
            @if($paymentDateLabel)
                {{ $paymentDateLabel }}
            @else
                Date Range: {{ $fromDate ?? '—' }} to {{ $toDate ?? '—' }}
            @endif
            | Generated: {{ $generatedAt }}
        </div>
    </div>

    <div class="section">
        <div class="section-title">Student Payment Report</div>
        @php $paymentGrandTotal = $paymentRows->sum(fn($group) => $group->students->sum('grand_total')); @endphp
        <table class="summary">
            <tr>
                <td><span class="label">Grand Total</span><span class="value">{{ number_format($paymentGrandTotal, 2) }}</span></td>
                <td><span class="label">Groups</span><span class="value">{{ $paymentRows->count() }}</span></td>
                <td><span class="label">Report Range</span><span class="value">{{ $paymentDateLabel ?? '—' }}</span></td>
            </tr>
        </table>

        @foreach($paymentRows as $group)
            <div class="class-block">
                <div class="group-title">Class: {{ $group->class_name }} | Section: {{ $group->section_name }}</div>
                <table>
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Student ID</th>
                            <th>Name</th>
                            @foreach($paymentCategories as $category)
                                <th class="text-right">{{ $category->name }}</th>
                            @endforeach
                            <th class="text-right">Grand Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($group->students as $index => $row)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $row->student_cid }}</td>
                                <td>{{ $row->student_name }}</td>
                                @foreach($paymentCategories as $category)
                                    <td class="text-right">{{ number_format($row->{$category->column_key}, 2) }}</td>
                                @endforeach
                                <td class="text-right">{{ number_format($row->grand_total, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="3">Subtotal</td>
                            @foreach($paymentCategories as $category)
                                <td class="text-right">{{ number_format($group->students->sum(fn($r) => $r->{$category->column_key}), 2) }}</td>
                            @endforeach
                            <td class="text-right">{{ number_format($group->students->sum('grand_total'), 2) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        @endforeach
    </div>

    <div class="section section-break">
        <div class="section-title">Student Receive Report</div>
        <table class="summary">
            <tr>
                <td><span class="label">Grand Total Received</span><span class="value">{{ number_format($receiveTotals['total'], 2) }}</span></td>
                <td><span class="label">Students</span><span class="value">{{ $receiveRows->count() }}</span></td>
                <td><span class="label">Date Range</span><span class="value">{{ $fromDate }} to {{ $toDate }}</span></td>
            </tr>
        </table>

        @foreach($receiveRows as $index => $student)
            <div class="student-block">
                <div class="group-title">{{ $student->student_name }} <span class="subtle">({{ $student->class_name }} | {{ $student->section_name }})</span></div>
                <table>
                    <thead>
                        <tr>
                            <th>Student ID</th>
                            <th>Description</th>
                            @foreach($receiveMonths as $monthLabel)
                                <th class="text-right">{{ $monthLabel }}</th>
                            @endforeach
                            <th class="text-right">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($student->lines as $line)
                            <tr>
                                <td>{{ $student->student_cid ?? '—' }}</td>
                                <td>{{ $line->description }}</td>
                                @foreach($receiveMonths as $monthKey => $monthLabel)
                                    <td class="text-right">{{ number_format($line->monthTotals[$monthKey] ?? 0, 2) }}</td>
                                @endforeach
                                <td class="text-right">{{ number_format($line->total, 2) }}</td>
                            </tr>
                        @endforeach
                        <tr>
                            <td colspan="2"><strong>TOTAL</strong></td>
                            @foreach($receiveMonths as $monthKey => $monthLabel)
                                <td class="text-right"><strong>{{ number_format($student->monthTotals[$monthKey] ?? 0, 2) }}</strong></td>
                            @endforeach
                            <td class="text-right"><strong>{{ number_format($student->student_total, 2) }}</strong></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        @endforeach

        <table class="summary" style="margin-top:8px;">
            <tr>
                <td colspan="2"><span class="label">Grand Total</span><span class="value">{{ number_format($receiveTotals['total'], 2) }}</span></td>
                @foreach($receiveMonths as $monthKey => $monthLabel)
                    <td><span class="label">{{ $monthLabel }}</span><span class="value">{{ number_format($receiveTotals['months'][$monthKey] ?? 0, 2) }}</span></td>
                @endforeach
            </tr>
        </table>
    </div>

    <div class="section section-break">
        <div class="section-title">Student Receivable Report</div>
        <table class="summary">
            <tr>
                <td><span class="label">Total Receivable</span><span class="value">{{ number_format($receivableTotals['total'], 2) }}</span></td>
                <td><span class="label">Students</span><span class="value">{{ $receivableRows->count() }}</span></td>
                <td><span class="label">Date Range</span><span class="value">{{ $receivableFromDate }} to {{ $receivableToDate }}</span></td>
            </tr>
        </table>

        @foreach($receivableRows as $index => $student)
            @php
                $visibleCats = $receivableCategories->filter(
                    fn($c) => isset($student->categories[$c->id]) && array_sum($student->categories[$c->id]) > 0
                )->values();
                $rowspan = $visibleCats->count() + 1;
                $firstCatId = $visibleCats->first()?->id;
            @endphp
            @if($visibleCats->isEmpty()) @continue @endif
            <div class="student-block">
                <div class="group-title">{{ $student->student_name }} <span class="subtle">({{ $student->class_name }} | {{ $student->section_name }})</span></div>
                <table>
                    <thead>
                        <tr>
                            <th rowspan="2">#</th>
                            <th rowspan="2">Fee Category</th>
                            @foreach($receivableMonths as $monthLabel)
                                <th class="text-right">{{ $monthLabel }}</th>
                            @endforeach
                            <th class="text-right" rowspan="2">Total</th>
                        </tr>
                        <tr>
                            @foreach($receivableMonths as $monthKey => $monthLabel)
                                <th class="text-right">{{ number_format($receivableTotals['months'][$monthKey] ?? 0, 0) }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($visibleCats as $cat)
                            @php
                                $catMonths = $student->categories[$cat->id];
                                $catTotal  = array_sum($catMonths);
                            @endphp
                            <tr>
                                @if($cat->id === $firstCatId)
                                    <td rowspan="{{ $rowspan }}" class="text-center">{{ $index + 1 }}</td>
                                @endif
                                <td>{{ $cat->name }}</td>
                                @foreach($receivableMonths as $monthKey => $monthLabel)
                                    <td class="text-right">{{ ($catMonths[$monthKey] ?? 0) > 0 ? number_format($catMonths[$monthKey], 2) : '—' }}</td>
                                @endforeach
                                <td class="text-right">{{ number_format($catTotal, 2) }}</td>
                            </tr>
                        @endforeach
                        <tr>
                            <td><strong>TOTAL</strong></td>
                            @foreach($receivableMonths as $monthKey => $monthLabel)
                                <td class="text-right"><strong>{{ number_format($student->months[$monthKey] ?? 0, 2) }}</strong></td>
                            @endforeach
                            <td class="text-right"><strong>{{ number_format($student->total, 2) }}</strong></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        @endforeach

        <table class="summary" style="margin-top:8px;">
            <tr>
                <td colspan="2"><span class="label">Category Total</span><span class="value">{{ number_format($receivableTotals['total'], 2) }}</span></td>
                @foreach($receivableMonths as $monthKey => $monthLabel)
                    <td><span class="label">{{ $monthLabel }}</span><span class="value">{{ number_format($receivableTotals['months'][$monthKey] ?? 0, 2) }}</span></td>
                @endforeach
            </tr>
        </table>
    </div>
</body>
</html>
