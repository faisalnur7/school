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

        .fees-report-page .fees-report-filter-card {
            position: relative;
        }

        .fees-report-page .report-header-body {
            display: flex;
            align-items: center;
            gap: 14px;
            width: 100%;
        }

        .fees-report-page .report-header-copy {
            min-width: 0;
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
        .fees-report-category-card .form-check-input,
        .payment-report-pdf-panel .form-check-input {
            appearance: none;
            -webkit-appearance: none;
            width: 1.05rem;
            height: 1.05rem;
            margin-top: 0.1rem;
            border: 2px solid #111111;
            border-radius: 50%;
            background: #ffffff center / 0.72rem 0.72rem no-repeat;
            box-shadow: none;
            cursor: pointer;
            transition: background-color .15s ease, border-color .15s ease, box-shadow .15s ease;
        }

        .fees-report-category-card .form-check-input:checked,
        .payment-report-pdf-panel .form-check-input:checked {
            background-color: #111111;
            border-color: #111111;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16' fill='none'%3E%3Cpath d='M6.2 11.2 2.9 8l-1.1 1.1 4.4 4.4L14.2 5.5 13.1 4.4 6.2 11.2Z' fill='%23ffffff'/%3E%3C/svg%3E");
        }

        .fees-report-category-card .form-check-input:indeterminate {
            background-color: #111111;
            border-color: #111111;
            background-image: linear-gradient(#ffffff, #ffffff);
            background-size: .6rem 2px;
        }

        .fees-report-category-card .form-check-input:focus,
        .payment-report-pdf-panel .form-check-input:focus {
            outline: none;
            box-shadow: 0 0 0 4px rgba(17, 17, 17, .12);
        }
    </style>
@endsection

@section('contents')
<div class="container-fluid fees-report-page">
    @php
        $reportTitle = 'Student Receive Report';
    @endphp
    @include('partials.report-header')
    <div class="fees-report-shell">
        <div class="fees-report-card fees-report-filter-card">
            <form method="GET" action="{{ route('fees.student-receive-report') }}" class="fees-report-form">
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
                        <label class="font-weight-bold">From Date <span class="text-danger">*</span></label>
                        <input type="date" name="from_date" value="{{ request('from_date') }}" class="form-control fees-report-input report-filter-control" required>
                    </div>
                    <div class="fees-report-field">
                        <label class="font-weight-bold">To Date <span class="text-danger">*</span></label>
                        <input type="date" name="to_date" value="{{ request('to_date') }}" class="form-control fees-report-input report-filter-control" required>
                    </div>
                    <div class="fees-report-filter-actions fees-report-filter-actions--submit">
                        <button type="submit" class="btn btn-dark fees-report-action-btn" title="Generate"><i class="fas fa-search"></i></button>
                        <a href="{{ route('fees.student-receive-report') }}" class="btn btn-outline-secondary fees-report-action-btn" title="Reset"><i class="fas fa-times"></i></a>
                        @if(request('from_date') && request('to_date') && $rows->isNotEmpty())
                            <button type="button" class="btn btn-success fees-report-action-btn" onclick="window.print()" title="Print"><i class="fas fa-print"></i></button>
                            <a href="{{ route('fees.student-receive-report.pdf', request()->query()) }}" class="btn btn-danger fees-report-action-btn" title="Export PDF"><i class="fas fa-file-pdf"></i></a>
                        @endif
                    </div>
                </div>
                @if($availableCategories->isNotEmpty())
                    <div class="fees-report-category-card mt-3 p-3" style="border:1px solid #e5e7eb;border-radius:12px;">
                        <div class="d-flex justify-content-between align-items-center mb-2 flex-wrap" style="gap:.75rem;"><div><p class="mb-0 font-weight-bold">PDF Column Selection</p><small class="text-muted">Choose which categories should appear in the student receive report and PDF.</small></div><div class="form-check mb-0"><input class="form-check-input" type="checkbox" id="student-receive-toggle-all" {{ count($selectedCategoryKeys) === count($availableCategories) ? 'checked' : '' }}><label class="form-check-label font-weight-bold" for="student-receive-toggle-all">Select all</label></div></div>
                        <div class="row">
                            @foreach($availableCategories as $category)
                                <div class="col-md-3 col-sm-6 mb-2"><div class="form-check"><input class="form-check-input student-receive-category-checkbox" type="checkbox" name="columns[]" value="{{ $category->key }}" id="student-receive-category-{{ $category->key }}" {{ in_array($category->key, $selectedCategoryKeys, true) ? 'checked' : '' }}><label class="form-check-label font-weight-bold" for="student-receive-category-{{ $category->key }}">{{ $category->name }}</label></div></div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </form>
        </div>

        <div class="fees-report-card">
            <hr>

            @if(!request('from_date') || !request('to_date'))
                <div class="text-center py-5 text-muted">
                    <i class="fas fa-calendar-alt fa-2x mb-2"></i>
                    <p class="mb-0">Select a date range to generate the student receive report.</p>
                </div>
            @elseif($rows->isEmpty())
                <div class="text-center py-5 text-muted">
                    <i class="fas fa-inbox fa-2x mb-2"></i>
                    <p class="mb-0">No records found for the selected date range.</p>
                </div>
            @else
                <div class="row mb-4">
                    <div class="col-md-4">
                        <div class="info-box bg-light">
                            <span class="info-box-icon bg-success"><i class="fas fa-receipt"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Grand Total Received</span>
                                <span class="info-box-number">{{ number_format($totals['total'], 2) }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-8">
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
                                <th>#</th>
                                <th>Student ID</th>
                                <th>Student Name</th>
                                <th>Class</th>
                                <th>Section</th>
                                <th>Description</th>
                                @foreach($months as $monthLabel)
                                    <th class="text-right">{{ $monthLabel }}</th>
                                @endforeach
                                <th class="text-right">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($rows as $index => $student)
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
                                        @foreach($months as $monthKey => $monthLabel)
                                            <td class="text-right">{{ number_format($line->monthTotals[$monthKey] ?? 0, 2) }}</td>
                                        @endforeach
                                        <td class="text-right font-weight-bold">{{ number_format($line->total, 2) }}</td>
                                    </tr>
                                @endforeach
                                <tr class="font-weight-bold bg-light">
                                    <td>TOTAL</td>
                                    @foreach($months as $monthKey => $monthLabel)
                                        <td class="text-right">{{ number_format($student->monthTotals[$monthKey] ?? 0, 2) }}</td>
                                    @endforeach
                                    <td class="text-right">{{ number_format($student->student_total, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="font-weight-bold bg-light">
                                <td colspan="6">Grand Total</td>
                                @foreach($months as $monthKey => $monthLabel)
                                    <td class="text-right">{{ number_format($totals['months'][$monthKey] ?? 0, 2) }}</td>
                                @endforeach
                                <td class="text-right">{{ number_format($totals['total'], 2) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>

<style>
@media print {
    @page {
        size: A4 landscape;
        margin: 8mm;
    }

    html, body {
        width: 100% !important;
        height: auto !important;
        overflow: visible !important;
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }

    .main-sidebar, .main-header, .content-header, hr, .info-box, button, a.btn { display: none !important; }
    .content-wrapper { margin-left: 0 !important; padding: 0 !important; overflow: visible !important; }
    .container-fluid.fees-report-page { max-width: none !important; padding: 0 !important; }
    .fees-report-shell { padding: 0 !important; }
    .fees-report-filter-card { display: none !important; }
    .fees-report-card { box-shadow: none !important; border-color: #d1d5db !important; break-inside: avoid; page-break-inside: avoid; }
    .report-header-card .report-header-body {
        display: table !important;
        width: 100% !important;
        table-layout: fixed !important;
    }
    .report-header-card .report-header-logo,
    .report-header-card .report-header-copy {
        display: table-cell !important;
        vertical-align: middle !important;
    }
    .report-header-card .report-header-logo {
        width: 58px !important;
        padding-right: 12px !important;
    }
    .report-header-card .report-header-copy {
        min-width: 0 !important;
    }
    table { page-break-inside: avoid; }
    tr, td, th { page-break-inside: avoid; }
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

<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.querySelector('form[action*="student-receive-report"]');
    if (form) form.addEventListener('submit', function () {
        const marker = document.createElement('input'); marker.type = 'hidden'; marker.name = 'columns_present'; marker.value = '1'; form.appendChild(marker);
    });
    const toggle = document.getElementById('student-receive-toggle-all');
    const checks = Array.from(document.querySelectorAll('.student-receive-category-checkbox'));
    if (!toggle) return;
    const sync = () => { toggle.checked = checks.length > 0 && checks.every(check => check.checked); toggle.indeterminate = checks.some(check => check.checked) && !toggle.checked; };
    toggle.addEventListener('change', () => checks.forEach(check => check.checked = toggle.checked));
    checks.forEach(check => check.addEventListener('change', sync));
    sync();
});
</script>
@endsection
