@extends('layouts.master')

@section('styles')
    <style>
        .all-in-one-report-page {
            width: 100%;
        }

        .all-in-one-report-page .report-shell {
            width: 100%;
            padding: 0.25rem 0 1.5rem;
        }

        .all-in-one-report-page .report-card,
        .all-in-one-report-page .report-section {
            background: #ffffff;
            border: 1px solid #e7e5e4;
            border-radius: 18px;
            box-shadow: 0 10px 30px rgba(15, 23, 42, 0.04);
        }

        .all-in-one-report-page .report-card {
            padding: 0.95rem;
            margin-bottom: 1rem;
        }

        .all-in-one-report-page .report-form {
            display: flex;
            flex-direction: column;
            gap: 0.9rem;
        }

        .all-in-one-report-page .report-grid {
            display: grid;
            gap: 0.75rem;
        }

        .all-in-one-report-page .report-grid--primary {
            grid-template-columns: minmax(220px, 2fr) repeat(5, minmax(120px, 1fr)) auto;
            align-items: center;
        }

        .all-in-one-report-page .report-field label {
            display: block;
            margin-bottom: 0.35rem;
            font-size: 0.77rem;
            font-weight: 700;
            color: #6b7280;
        }

        .all-in-one-report-page .report-input,
        .all-in-one-report-page .report-select {
            width: 100%;
            min-height: 46px;
            border-radius: 12px;
            border: 1px solid #e5e7eb;
            background: #fff;
            color: #111827;
            font-size: 0.92rem;
            box-shadow: none;
        }

        .all-in-one-report-page .report-input:focus,
        .all-in-one-report-page .report-select:focus {
            border-color: #cbd5e1;
            box-shadow: 0 0 0 4px rgba(15, 23, 42, 0.05);
        }

        .all-in-one-report-page .report-actions {
            display: inline-flex;
            align-items: center;
            justify-content: flex-end;
            gap: 0.65rem;
            flex-wrap: wrap;
            justify-self: end;
        }

        .all-in-one-report-page .report-action-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            min-height: 44px;
            border-radius: 12px;
            font-weight: 600;
            font-size: 0.88rem;
            padding: 0.7rem 1rem;
            box-shadow: none;
        }

        .all-in-one-report-page .report-section {
            overflow: hidden;
            margin-bottom: 1rem;
        }

        .all-in-one-report-page .report-section-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            padding: 1rem 1rem 0.85rem;
            border-bottom: 1px solid #f1f5f9;
        }

        .all-in-one-report-page .report-section-title {
            margin: 0;
            font-size: 1rem;
            font-weight: 700;
            color: #111827;
        }

        .all-in-one-report-page .report-section-subtitle {
            margin: 0;
            font-size: 0.83rem;
            color: #6b7280;
        }

        .all-in-one-report-page .report-section-body {
            padding: 1rem;
        }

        .all-in-one-report-page .report-summary {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 0.75rem;
            margin-bottom: 1rem;
        }

        .all-in-one-report-page .report-summary-card {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 14px;
            padding: 0.9rem 1rem;
        }

        .all-in-one-report-page .report-summary-label {
            display: block;
            font-size: 0.75rem;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: #64748b;
            margin-bottom: 0.25rem;
        }

        .all-in-one-report-page .report-summary-value {
            font-size: 1rem;
            font-weight: 700;
            color: #111827;
        }

        .all-in-one-report-page .report-table-wrap {
            overflow-x: auto;
        }

        .all-in-one-report-page .report-table {
            width: 100%;
            min-width: 900px;
            margin: 0;
            border-collapse: separate;
            border-spacing: 0;
        }

        .all-in-one-report-page .report-table thead th {
            border: 0;
            border-bottom: 1px solid #f1f5f9;
            padding: 0.9rem 1rem;
            background: #fff;
            color: #374151;
            font-size: 0.82rem;
            font-weight: 700;
            white-space: nowrap;
        }

        .all-in-one-report-page .report-table tbody td {
            border: 0;
            border-bottom: 1px solid #f3f4f6;
            padding: 0.9rem 1rem;
            vertical-align: top;
            font-size: 0.9rem;
            color: #111827;
        }

        .all-in-one-report-page .report-table tbody tr:hover {
            background: #fcfcfd;
        }

        .all-in-one-report-page .report-table tfoot td {
            border: 0;
            border-top: 1px solid #f1f5f9;
            padding: 0.9rem 1rem;
            background: #f8fafc;
            color: #111827;
            font-weight: 700;
        }

        .all-in-one-report-page .report-table th.text-right,
        .all-in-one-report-page .report-table td.text-right {
            white-space: nowrap;
        }

        .all-in-one-report-page .report-empty {
            padding: 3rem 1rem;
            text-align: center;
            color: #6b7280;
        }

        @media (max-width: 1199.98px) {
            .all-in-one-report-page .report-grid--primary {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .all-in-one-report-page .report-summary {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 767.98px) {
            .all-in-one-report-page .report-grid--primary,
            .all-in-one-report-page .report-summary {
                grid-template-columns: 1fr;
            }

            .all-in-one-report-page .report-actions {
                width: 100%;
            }

            .all-in-one-report-page .report-actions > * {
                width: 100%;
                justify-content: center;
            }
        }

        html[data-theme='dark'] .all-in-one-report-page {
            color: #e2e8f0;
        }

        html[data-theme='dark'] .all-in-one-report-page .report-card,
        html[data-theme='dark'] .all-in-one-report-page .report-section {
            background: linear-gradient(180deg, rgba(17, 24, 39, 0.98) 0%, rgba(15, 23, 42, 0.96) 100%);
            border-color: rgba(148, 163, 184, 0.18);
            box-shadow: 0 10px 24px rgba(2, 6, 23, 0.26);
        }

        html[data-theme='dark'] .all-in-one-report-page .report-field label,
        html[data-theme='dark'] .all-in-one-report-page .report-section-subtitle,
        html[data-theme='dark'] .all-in-one-report-page .report-summary-label,
        html[data-theme='dark'] .all-in-one-report-page .report-empty {
            color: #cbd5e1;
        }

        html[data-theme='dark'] .all-in-one-report-page .report-input,
        html[data-theme='dark'] .all-in-one-report-page .report-select {
            border-color: rgba(148, 163, 184, 0.2);
            background: rgba(15, 23, 42, 0.96);
            color: #e2e8f0;
        }

        html[data-theme='dark'] .all-in-one-report-page .report-input:focus,
        html[data-theme='dark'] .all-in-one-report-page .report-select:focus {
            border-color: rgba(96, 165, 250, 0.35);
            box-shadow: 0 0 0 4px rgba(96, 165, 250, 0.12);
        }

        html[data-theme='dark'] .all-in-one-report-page .report-action-btn.btn-outline-secondary {
            border-color: rgba(148, 163, 184, 0.18);
            background: rgba(15, 23, 42, 0.96);
            color: #e2e8f0;
        }

        html[data-theme='dark'] .all-in-one-report-page .report-action-btn.btn-outline-secondary:hover {
            background: #1e293b;
            color: #f8fafc;
        }

        html[data-theme='dark'] .all-in-one-report-page .report-section-header,
        html[data-theme='dark'] .all-in-one-report-page .report-table thead th {
            border-color: rgba(148, 163, 184, 0.14);
        }

        html[data-theme='dark'] .all-in-one-report-page .report-section-title,
        html[data-theme='dark'] .all-in-one-report-page .report-summary-value {
            color: #f8fafc;
        }

        html[data-theme='dark'] .all-in-one-report-page .report-summary-card {
            background: rgba(15, 23, 42, 0.96);
            border-color: rgba(148, 163, 184, 0.18);
        }

        html[data-theme='dark'] .all-in-one-report-page .report-table thead th {
            background: #1e293b;
            color: #e2e8f0;
        }

        html[data-theme='dark'] .all-in-one-report-page .report-table tbody td {
            border-bottom-color: rgba(148, 163, 184, 0.14);
            color: #e2e8f0;
        }

        html[data-theme='dark'] .all-in-one-report-page .report-table tbody tr:hover {
            background: rgba(30, 41, 59, 0.78);
        }

        html[data-theme='dark'] .all-in-one-report-page .report-table tfoot td {
            background: rgba(15, 23, 42, 0.96);
            border-top-color: rgba(148, 163, 184, 0.14);
            color: #e2e8f0;
        }
    </style>
@endsection

@section('contents')
    <div class="container-fluid all-in-one-report-page">
        @include('partials.report-header')

        <div class="report-shell">
            <div class="report-card">
                <form method="GET" action="{{ route('fees.all-in-one-report') }}" class="report-form">
                    <div class="report-grid report-grid--primary">
                        <div class="report-field">
                            <label for="all-report-student-id">Student ID</label>
                            <input
                                type="text"
                                id="all-report-student-id"
                                name="student_id"
                                value="{{ request('student_id') }}"
                                class="form-control report-input"
                                placeholder="Search specific student"
                            >
                        </div>

                        <div class="report-field">
                            <label for="all-report-session">Academic Session</label>
                            <select name="session_id" id="all-report-session" class="form-control report-select">
                                <option value="">All Sessions</option>
                                @foreach($sessions as $session)
                                    <option value="{{ $session->id }}" {{ request('session_id') == $session->id ? 'selected' : '' }}>
                                        {{ $session->name_en }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="report-field">
                            <label for="classSelect">Class</label>
                            <select name="class_id" id="classSelect" class="form-control report-select">
                                <option value="">All Classes</option>
                                @foreach($classes as $class)
                                    <option value="{{ $class->id }}" {{ request('class_id') == $class->id ? 'selected' : '' }}>
                                        {{ $class->name_en }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="report-field">
                            <label for="sectionSelect">Section</label>
                            <select name="section_id" id="sectionSelect" class="form-control report-select">
                                <option value="">All Sections</option>
                                @foreach($sections as $section)
                                    <option value="{{ $section->id }}" {{ request('section_id') == $section->id ? 'selected' : '' }}>
                                        {{ $section->name_en }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="report-field">
                            <label for="all-report-from-date">From Date <span class="text-danger">*</span></label>
                            <input type="date" id="all-report-from-date" name="from_date" value="{{ request('from_date') }}" class="form-control report-input">
                        </div>

                        <div class="report-field">
                            <label for="all-report-to-date">To Date <span class="text-danger">*</span></label>
                            <input type="date" id="all-report-to-date" name="to_date" value="{{ request('to_date') }}" class="form-control report-input">
                        </div>

                        <div class="report-actions">
                            <button type="submit" class="btn btn-dark report-action-btn" title="Generate" aria-label="Generate">
                                <i class="fas fa-search"></i>
                            </button>
                            <a href="{{ route('fees.all-in-one-report') }}" class="btn btn-outline-secondary report-action-btn" title="Reset" aria-label="Reset">
                                <i class="fas fa-undo-alt"></i>
                            </a>
                            @if(request('from_date') && request('to_date') && ($paymentRows->isNotEmpty() || $receiveRows->isNotEmpty() || $receivableRows->isNotEmpty()))
                                <button type="button" class="btn btn-success report-action-btn" onclick="window.print()" title="Print" aria-label="Print">
                                    <i class="fas fa-print"></i>
                                </button>
                            @endif
                        </div>
                    </div>
                </form>
            </div>

            @if(!request('from_date') || !request('to_date'))
                <div class="report-card">
                    <div class="report-empty">
                        <i class="fas fa-calendar-alt fa-2x mb-2"></i>
                        <p class="mb-0">Select a date range to generate the all in one report.</p>
                    </div>
                </div>
            @else
                <div class="report-section">
                    <div class="report-section-header">
                        <div>
                            <p class="report-section-title mb-0">Student Payment Report</p>
                            <p class="report-section-subtitle">Category-wise payment report</p>
                        </div>
                    </div>
                    <div class="report-section-body">
                        @php
                            $paymentGrandTotal = $paymentRows->sum(fn($group) => $group->students->sum('grand_total'));
                        @endphp

                        @if($paymentCategories->isEmpty() || $paymentRows->isEmpty())
                            <div class="report-empty">
                                <i class="fas fa-inbox fa-2x mb-2"></i>
                                <p class="mb-0">No payment report data found for the selected filters.</p>
                            </div>
                        @else
                            <div class="report-summary">
                                <div class="report-summary-card">
                                    <span class="report-summary-label">Grand Total</span>
                                    <div class="report-summary-value">{{ number_format($paymentGrandTotal, 2) }}</div>
                                </div>
                                <div class="report-summary-card">
                                    <span class="report-summary-label">Report Range</span>
                                    <div class="report-summary-value">{{ $paymentDateLabel ?? '—' }}</div>
                                </div>
                                <div class="report-summary-card">
                                    <span class="report-summary-label">Groups</span>
                                    <div class="report-summary-value">{{ $paymentRows->count() }}</div>
                                </div>
                            </div>

                            @foreach($paymentRows as $group)
                                <div class="report-table-wrap mb-4">
                                    <p class="report-section-title mb-2">Class: {{ $group->class_name }} | Section: {{ $group->section_name }}</p>
                                    <table class="table report-table">
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
                        @endif
                    </div>
                </div>

                <div class="report-section">
                    <div class="report-section-header">
                        <div>
                            <p class="report-section-title mb-0">Student Receive Report</p>
                            <p class="report-section-subtitle">Monthwise receive report</p>
                        </div>
                    </div>
                    <div class="report-section-body">
                        @if($receiveRows->isEmpty())
                            <div class="report-empty">
                                <i class="fas fa-inbox fa-2x mb-2"></i>
                                <p class="mb-0">No receive report data found for the selected filters.</p>
                            </div>
                        @else
                            <div class="report-summary">
                                <div class="report-summary-card">
                                    <span class="report-summary-label">Grand Total Received</span>
                                    <div class="report-summary-value">{{ number_format($receiveTotals['total'], 2) }}</div>
                                </div>
                                <div class="report-summary-card">
                                    <span class="report-summary-label">Date Range</span>
                                    <div class="report-summary-value">{{ $fromDate }} to {{ $toDate }}</div>
                                </div>
                                <div class="report-summary-card">
                                    <span class="report-summary-label">Students</span>
                                    <div class="report-summary-value">{{ $receiveRows->count() }}</div>
                                </div>
                            </div>

                            <div class="report-table-wrap">
                                <table class="table report-table">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Student ID</th>
                                            <th>Student Name</th>
                                            <th>Class</th>
                                            <th>Section</th>
                                            <th>Description</th>
                                            @foreach($receiveMonths as $monthLabel)
                                                <th class="text-right">{{ $monthLabel }}</th>
                                            @endforeach
                                            <th class="text-right">Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($receiveRows as $index => $student)
                                            @foreach($student->lines as $lineIndex => $line)
                                                <tr>
                                                    @if($lineIndex === 0)
                                                        <td rowspan="{{ $student->lines->count() + 1 }}" class="align-middle text-center">{{ $index + 1 }}</td>
                                                        <td rowspan="{{ $student->lines->count() + 1 }}" class="align-middle">{{ $student->student_cid ?? '—' }}</td>
                                                        <td rowspan="{{ $student->lines->count() + 1 }}" class="align-middle">{{ $student->student_name }}</td>
                                                        <td rowspan="{{ $student->lines->count() + 1 }}" class="align-middle">{{ $student->class_name }}</td>
                                                        <td rowspan="{{ $student->lines->count() + 1 }}" class="align-middle">{{ $student->section_name }}</td>
                                                    @endif
                                                    <td>{{ $line->description }}</td>
                                                    @foreach($receiveMonths as $monthKey => $monthLabel)
                                                        <td class="text-right">{{ number_format($line->monthTotals[$monthKey] ?? 0, 2) }}</td>
                                                    @endforeach
                                                    <td class="text-right font-weight-bold">{{ number_format($line->total, 2) }}</td>
                                                </tr>
                                            @endforeach
                                            <tr class="font-weight-bold bg-light">
                                                <td>TOTAL</td>
                                                @foreach($receiveMonths as $monthKey => $monthLabel)
                                                    <td class="text-right">{{ number_format($student->monthTotals[$monthKey] ?? 0, 2) }}</td>
                                                @endforeach
                                                <td class="text-right">{{ number_format($student->student_total, 2) }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr class="font-weight-bold bg-light">
                                            <td colspan="6">Grand Total</td>
                                            @foreach($receiveMonths as $monthKey => $monthLabel)
                                                <td class="text-right">{{ number_format($receiveTotals['months'][$monthKey] ?? 0, 2) }}</td>
                                            @endforeach
                                            <td class="text-right">{{ number_format($receiveTotals['total'], 2) }}</td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="report-section">
                    <div class="report-section-header">
                        <div>
                            <p class="report-section-title mb-0">Student Receivable Report</p>
                            <p class="report-section-subtitle">Category-wise assigned fees by month</p>
                        </div>
                    </div>
                    <div class="report-section-body">
                        @if($receivableRows->isEmpty())
                            <div class="report-empty">
                                <i class="fas fa-inbox fa-2x mb-2"></i>
                                <p class="mb-0">No receivable report data found for the selected filters.</p>
                            </div>
                        @else
                            <div class="report-summary">
                                <div class="report-summary-card">
                                    <span class="report-summary-label">Total Receivable</span>
                                    <div class="report-summary-value">{{ number_format($receivableTotals['total'], 2) }}</div>
                                </div>
                                <div class="report-summary-card">
                                    <span class="report-summary-label">Students</span>
                                    <div class="report-summary-value">{{ $receivableRows->count() }}</div>
                                </div>
                                <div class="report-summary-card">
                                    <span class="report-summary-label">Date Range</span>
                                    <div class="report-summary-value">{{ $receivableFromDate }} to {{ $receivableToDate }}</div>
                                </div>
                            </div>

                            <div class="report-table-wrap">
                                <table class="table report-table">
                                    <thead>
                                        <tr>
                                            <th rowspan="2" class="align-middle">#</th>
                                            <th rowspan="2" class="align-middle">Student ID</th>
                                            <th rowspan="2" class="align-middle">Student Name</th>
                                            <th rowspan="2" class="align-middle">Class</th>
                                            <th rowspan="2" class="align-middle">Section</th>
                                            <th rowspan="2" class="align-middle">Fee Category</th>
                                            @foreach($receivableMonths as $monthLabel)
                                                <th class="text-right">{{ $monthLabel }}</th>
                                            @endforeach
                                            <th class="text-right align-middle" rowspan="2">Total</th>
                                        </tr>
                                        <tr>
                                            @foreach($receivableMonths as $monthKey => $monthLabel)
                                                <th class="text-right text-muted small">{{ number_format($receivableTotals['months'][$monthKey] ?? 0, 0) }}</th>
                                            @endforeach
                                        </tr>
                                    </thead>
                                    <tbody>
                                            @foreach($receivableRows as $index => $student)
                                                @php
                                                    $visibleCats = $receivableCategories->filter(
                                                        fn($c) => isset($student->categories[$c->id]) && array_sum($student->categories[$c->id]) > 0
                                                    )->values();
                                                $rowspan = $visibleCats->count() + 1;
                                                $firstCatId = $visibleCats->first()?->id;
                                            @endphp
                                            @if($visibleCats->isEmpty()) @continue @endif
                                            @foreach($visibleCats as $cat)
                                                @php
                                                    $catMonths = $student->categories[$cat->id];
                                                    $catTotal  = array_sum($catMonths);
                                                @endphp
                                                <tr>
                                                    @if($cat->id === $firstCatId)
                                                        <td rowspan="{{ $rowspan }}" class="align-middle text-center">{{ $index + 1 }}</td>
                                                        <td rowspan="{{ $rowspan }}" class="align-middle">{{ $student->student_cid ?? '—' }}</td>
                                                        <td rowspan="{{ $rowspan }}" class="align-middle">
                                                            {{ $student->student_name }}
                                                            @if($student->is_new)
                                                                <span class="badge badge-success" style="font-size:9px;">New</span>
                                                            @else
                                                                <span class="badge badge-primary" style="font-size:9px;">Old</span>
                                                            @endif
                                                        </td>
                                                        <td rowspan="{{ $rowspan }}" class="align-middle">{{ $student->class_name }}</td>
                                                        <td rowspan="{{ $rowspan }}" class="align-middle">{{ $student->section_name }}</td>
                                                    @endif
                                                    <td>{{ $cat->name }}</td>
                                                    @foreach($receivableMonths as $monthKey => $monthLabel)
                                                        <td class="text-right">
                                                            {{ ($catMonths[$monthKey] ?? 0) > 0 ? number_format($catMonths[$monthKey], 2) : '—' }}
                                                        </td>
                                                    @endforeach
                                                    <td class="text-right font-weight-bold">{{ number_format($catTotal, 2) }}</td>
                                                </tr>
                                            @endforeach
                                            <tr class="font-weight-bold bg-light">
                                                <td>TOTAL</td>
                                                @foreach($receivableMonths as $monthKey => $monthLabel)
                                                    <td class="text-right">{{ number_format($student->months[$monthKey] ?? 0, 2) }}</td>
                                                @endforeach
                                                <td class="text-right">{{ number_format($student->total, 2) }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr class="font-weight-bold" style="background:#e9ecef;">
                                            <td colspan="5" class="text-right">Category Total</td>
                                            <td></td>
                                            @foreach($receivableMonths as $monthKey => $monthLabel)
                                                <td class="text-right">{{ number_format($receivableTotals['months'][$monthKey] ?? 0, 2) }}</td>
                                            @endforeach
                                            <td class="text-right">{{ number_format($receivableTotals['total'], 2) }}</td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const classSelect = document.getElementById('classSelect');
    const sectionSelect = document.getElementById('sectionSelect');
    const selectedSection = @json(request('section_id'));

    function refreshSectionSelect() {
        if (!sectionSelect) return;
        if (window.refreshSelect2) window.refreshSelect2($(sectionSelect));
    }

    function loadSections(classId, selectedSectionId = null) {
        if (!sectionSelect) return;

        if (!classId) {
            sectionSelect.innerHTML = '<option value="">All Sections</option>';
            refreshSectionSelect();
            return;
        }

        sectionSelect.innerHTML = '<option value="">Loading...</option>';
        refreshSectionSelect();

        fetch(`{{ route('load_section_groups') }}?school_class_id=${encodeURIComponent(classId)}`)
            .then(response => {
                if (!response.ok) throw new Error('Failed to load sections');
                return response.json();
            })
            .then(data => {
                const sections = Array.isArray(data?.sections) ? data.sections : [];
                let html = '<option value="">All Sections</option>';

                sections.forEach(section => {
                    const selected = String(selectedSectionId) === String(section.id) ? 'selected' : '';
                    html += `<option value="${section.id}" ${selected}>${section.name_en}</option>`;
                });

                sectionSelect.innerHTML = html;
                refreshSectionSelect();
            })
            .catch(() => {
                sectionSelect.innerHTML = '<option value="">All Sections</option>';
                refreshSectionSelect();
            });
    }

    $(document).on('change', '#classSelect', function () {
        loadSections(this.value);
    });

    if (classSelect && classSelect.value) {
        loadSections(classSelect.value, selectedSection);
    }
});
</script>
@endsection
