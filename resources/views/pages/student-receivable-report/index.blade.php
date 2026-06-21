@extends('layouts.master')

@section('styles')
    <style>
        .fees-report-page {
            width: 100%;
        }

        .fees-report-page .fees-report-shell {
            width: 100%;
            padding: 0.25rem 0 1.5rem;
        }

        .fees-report-page .fees-report-card {
            background: #ffffff;
            border: 1px solid #e7e5e4;
            border-radius: 18px;
            box-shadow: 0 10px 30px rgba(15, 23, 42, 0.04);
            padding: 0.95rem;
            margin-bottom: 1rem;
        }

        .fees-report-page .fees-report-form {
            display: flex;
            flex-direction: column;
            gap: 0.9rem;
        }

        .fees-report-page .fees-report-grid {
            display: grid;
            gap: 0.75rem;
        }

        .fees-report-page .fees-report-grid--primary {
            grid-template-columns: minmax(220px, 2fr) repeat(5, minmax(120px, 1fr)) auto auto;
            align-items: center;
        }

        .fees-report-page .fees-report-field label {
            display: block;
            margin-bottom: 0.35rem;
            font-size: 0.77rem;
            font-weight: 700;
            color: #6b7280;
        }

        .fees-report-page .fees-report-input,
        .fees-report-page .fees-report-select {
            width: 100%;
            min-height: 46px;
            border-radius: 12px;
            border: 1px solid #e5e7eb;
            background: #fff;
            color: #111827;
            font-size: 0.92rem;
            box-shadow: none;
        }

        .fees-report-page .fees-report-input:focus,
        .fees-report-page .fees-report-select:focus {
            border-color: #cbd5e1;
            box-shadow: 0 0 0 4px rgba(15, 23, 42, 0.05);
        }

        .fees-report-page .fees-report-filter-actions {
            display: inline-flex;
            align-items: center;
            justify-content: flex-end;
            gap: 0.65rem;
            flex-wrap: wrap;
            justify-self: end;
        }

        .fees-report-page .fees-report-filter-actions--submit {
            justify-content: flex-start;
            justify-self: end;
        }

        .fees-report-page .fees-report-action-btn {
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

        .fees-report-page .fees-report-action-btn.btn-dark {
            background: #111111;
            border-color: #111111;
        }

        .fees-report-page .fees-report-action-btn.btn-outline-secondary {
            border-color: #d6d3d1;
            color: #374151;
            background: #fff;
        }

        .fees-report-page .fees-report-action-btn.btn-outline-secondary:hover {
            background: #f8fafc;
            color: #111827;
        }

        @media (max-width: 1199.98px) {
            .fees-report-page .fees-report-grid--primary {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 767.98px) {
            .fees-report-page .fees-report-grid--primary {
                grid-template-columns: 1fr;
            }

            .fees-report-page .fees-report-filter-actions,
            .fees-report-page .fees-report-filter-actions--submit {
                width: 100%;
            }

            .fees-report-page .fees-report-filter-actions > *,
            .fees-report-page .fees-report-filter-actions--submit > * {
                width: 100%;
                justify-content: center;
            }
        }

        html[data-theme='dark'] .fees-report-page .fees-report-card {
            background: linear-gradient(180deg, rgba(17, 24, 39, 0.98) 0%, rgba(15, 23, 42, 0.96) 100%);
            border-color: rgba(148, 163, 184, 0.18);
            box-shadow: 0 10px 24px rgba(2, 6, 23, 0.26);
        }

        html[data-theme='dark'] .fees-report-page .fees-report-field label {
            color: #cbd5e1;
        }

        html[data-theme='dark'] .fees-report-page .fees-report-input,
        html[data-theme='dark'] .fees-report-page .fees-report-select {
            border-color: rgba(148, 163, 184, 0.2);
            background: rgba(15, 23, 42, 0.96);
            color: #e2e8f0;
        }

        html[data-theme='dark'] .fees-report-page .fees-report-input:focus,
        html[data-theme='dark'] .fees-report-page .fees-report-select:focus {
            border-color: rgba(96, 165, 250, 0.35);
            box-shadow: 0 0 0 4px rgba(96, 165, 250, 0.12);
        }

        html[data-theme='dark'] .fees-report-page .fees-report-action-btn.btn-outline-secondary {
            border-color: rgba(148, 163, 184, 0.18);
            background: rgba(15, 23, 42, 0.96);
            color: #e2e8f0;
        }

        html[data-theme='dark'] .fees-report-page .fees-report-action-btn.btn-outline-secondary:hover {
            background: #1e293b;
            color: #f8fafc;
        }

        .fees-report-page .table-responsive th.text-right,
        .fees-report-page .table-responsive td.text-right {
            white-space: nowrap;
        }
    </style>
@endsection

@section('contents')
<div class="container-fluid fees-report-page">
    @include('partials.report-header')
    <div class="fees-report-shell">
        <div class="fees-report-card">
            <form method="GET" action="{{ route('fees.student-receivable-report') }}" class="fees-report-form">
                <div class="fees-report-grid fees-report-grid--primary">
                    <div class="fees-report-field">
                        <label class="font-weight-bold">Student ID</label>
                        <input type="text" name="student_id" value="{{ request('student_id') }}" class="form-control fees-report-input report-filter-control" placeholder="Search specific student">
                    </div>
                    <div class="fees-report-field">
                        <label class="font-weight-bold">Academic Session</label>
                        <select name="session_id" class="form-control fees-report-select report-filter-control">
                            <option value="">All Sessions</option>
                            @foreach($sessions as $session)
                                <option value="{{ $session->id }}" {{ request('session_id') == $session->id ? 'selected' : '' }}>{{ $session->name_en }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="fees-report-field">
                        <label class="font-weight-bold">Class</label>
                        <select name="class_id" id="classSelect" class="form-control fees-report-select report-filter-control">
                            <option value="">All Classes</option>
                            @foreach($classes as $class)
                                <option value="{{ $class->id }}" {{ request('class_id') == $class->id ? 'selected' : '' }}>{{ $class->name_en }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="fees-report-field">
                        <label class="font-weight-bold">Section</label>
                        <select name="section_id" id="sectionSelect" class="form-control fees-report-select report-filter-control">
                            <option value="">All Sections</option>
                            @foreach($sections as $section)
                                <option value="{{ $section->id }}" {{ request('section_id') == $section->id ? 'selected' : '' }}>{{ $section->name_en }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="fees-report-field">
                        <label class="font-weight-bold">From Date</label>
                        <input type="date" name="from_date" value="{{ request('from_date') }}" class="form-control fees-report-input report-filter-control">
                    </div>
                    <div class="fees-report-field">
                        <label class="font-weight-bold">To Date</label>
                        <input type="date" name="to_date" value="{{ request('to_date') }}" class="form-control fees-report-input report-filter-control">
                    </div>
                    <div class="fees-report-filter-actions fees-report-filter-actions--submit">
                        <button type="submit" class="btn btn-dark fees-report-action-btn" title="Generate"><i class="fas fa-search"></i></button>
                        <a href="{{ route('fees.student-receivable-report') }}" class="btn btn-outline-secondary fees-report-action-btn" title="Reset"><i class="fas fa-times"></i></a>
                        @if(request('from_date') && request('to_date') && $rows->isNotEmpty())
                            <button type="button" class="btn btn-success fees-report-action-btn" onclick="window.print()" title="Print"><i class="fas fa-print"></i></button>
                            <a href="{{ route('fees.student-receivable-report.pdf', request()->query()) }}" class="btn btn-danger fees-report-action-btn" title="Export PDF"><i class="fas fa-file-pdf"></i></a>
                        @endif
                    </div>
                </div>
            </form>
        </div>

        <div class="fees-report-card">
            <hr>

            @if(!request('from_date') || !request('to_date'))
                <div class="text-center py-5 text-muted">
                    <i class="fas fa-calendar-alt fa-2x mb-2"></i>
                    <p class="mb-0">Select a date range to generate the student receivable report.</p>
                </div>
            @elseif($rows->isEmpty())
                <div class="text-center py-5 text-muted">
                    <i class="fas fa-inbox fa-2x mb-2"></i>
                    <p class="mb-0">No assigned fees found for the selected date range.</p>
                </div>
            @else
                <div class="row mb-4">
                    <div class="col-md-4">
                        <div class="info-box bg-light">
                            <span class="info-box-icon bg-warning"><i class="fas fa-file-invoice-dollar"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Total Receivable</span>
                                <span class="info-box-number">{{ number_format($totals['total'], 2) }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="info-box bg-light">
                            <span class="info-box-icon bg-info"><i class="fas fa-users"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Students</span>
                                <span class="info-box-number">{{ $rows->count() }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="info-box bg-light">
                            <span class="info-box-icon bg-secondary"><i class="fas fa-calendar-day"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Date Range</span>
                                <span class="info-box-number">{{ $fromDate }} to {{ $toDate }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered table-sm">
                        <thead class="thead-dark">
                            <tr>
                                <th rowspan="2" class="align-middle">#</th>
                                <th rowspan="2" class="align-middle">Student ID</th>
                                <th rowspan="2" class="align-middle">Student Name</th>
                                <th rowspan="2" class="align-middle">Class</th>
                                <th rowspan="2" class="align-middle">Section</th>
                                <th rowspan="2" class="align-middle">Fee Category</th>
                                @foreach($months as $monthLabel)
                                    <th class="text-right">{{ $monthLabel }}</th>
                                @endforeach
                                <th class="text-right align-middle" rowspan="2">Total</th>
                            </tr>
                            <tr>
                                @foreach($months as $monthKey => $monthLabel)
                                    <th class="text-right text-muted small">
                                        {{ number_format($totals['months'][$monthKey] ?? 0, 0) }}
                                    </th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                                @foreach($rows as $index => $student)
                                    @php
                                        $visibleCats = $categories->filter(
                                            fn($c) => isset($student->categories[$c->id]) && array_sum($student->categories[$c->id]) > 0
                                        )->values();
                                        $rowspan = $visibleCats->count() + 3;
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
                                        @foreach($months as $monthKey => $monthLabel)
                                            <td class="text-right">
                                                {{ ($catMonths[$monthKey] ?? 0) > 0 ? number_format($catMonths[$monthKey], 2) : '—' }}
                                            </td>
                                        @endforeach
                                        <td class="text-right font-weight-bold">{{ number_format($catTotal, 2) }}</td>
                                    </tr>
                                @endforeach
                                <tr class="font-weight-bold bg-light">
                                    <td>TOTAL</td>
                                    @foreach($months as $monthKey => $monthLabel)
                                        <td class="text-right">{{ number_format($student->months[$monthKey] ?? 0, 2) }}</td>
                                    @endforeach
                                    <td class="text-right">{{ number_format($student->total, 2) }}</td>
                                </tr>
                                <tr class="font-weight-bold bg-light">
                                    <td>PAID</td>
                                    @foreach($months as $monthKey => $monthLabel)
                                        <td class="text-right text-success">{{ number_format($student->paidMonths[$monthKey] ?? 0, 2) }}</td>
                                    @endforeach
                                    <td class="text-right text-success">{{ number_format($student->paid_total ?? 0, 2) }}</td>
                                </tr>
                                <tr class="font-weight-bold bg-light">
                                    <td>DUE</td>
                                    @foreach($months as $monthKey => $monthLabel)
                                        <td class="text-right text-danger">{{ number_format($student->dueMonths[$monthKey] ?? 0, 2) }}</td>
                                    @endforeach
                                    <td class="text-right text-danger">{{ number_format($student->due_total ?? 0, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="font-weight-bold" style="background:#e9ecef;">
                                <td colspan="5" class="text-right">Category Total</td>
                                <td></td>
                                @foreach($months as $monthKey => $monthLabel)
                                    <td class="text-right">{{ number_format($totals['months'][$monthKey] ?? 0, 2) }}</td>
                                @endforeach
                                <td class="text-right">{{ number_format($totals['total'], 2) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                {{-- Category-wise monthly summary --}}
                @if($categories->isNotEmpty())
                <div class="mt-4">
                    <h6 class="font-weight-bold mb-2">Category-wise Monthly Summary</h6>
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm">
                            <thead class="thead-dark">
                                <tr>
                                    <th>Fee Category</th>
                                    @foreach($months as $monthLabel)
                                        <th class="text-right">{{ $monthLabel }}</th>
                                    @endforeach
                                    <th class="text-right">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($categories as $cat)
                                    @php $catTotal = array_sum($totals['categories'][$cat->id] ?? []); @endphp
                                    @if($catTotal <= 0) @continue @endif
                                    <tr>
                                        <td>{{ $cat->name }}</td>
                                        @foreach($months as $monthKey => $monthLabel)
                                            <td class="text-right">{{ number_format($totals['categories'][$cat->id][$monthKey] ?? 0, 2) }}</td>
                                        @endforeach
                                        <td class="text-right font-weight-bold">{{ number_format($catTotal, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr class="font-weight-bold" style="background:#e9ecef;">
                                    <td>Grand Total</td>
                                    @foreach($months as $monthKey => $monthLabel)
                                        <td class="text-right">{{ number_format($totals['months'][$monthKey] ?? 0, 2) }}</td>
                                    @endforeach
                                    <td class="text-right">{{ number_format($totals['total'], 2) }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
                @endif
            @endif
        </div>
    </div>
</div>

<style>
@media print {
    .main-sidebar, .main-header, .content-header, form, hr, .info-box, button, a.btn { display: none !important; }
    .content-wrapper { margin-left: 0 !important; }
    table { page-break-inside: avoid; }
}
</style>

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
