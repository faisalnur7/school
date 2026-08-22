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
            grid-template-columns: repeat(4, minmax(120px, 1fr)) auto;
        }

        .fees-report-page .fees-report-advanced-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 0.75rem;
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
            align-self: end;
        }

        .fees-report-page .fees-report-filter-actions--submit {
            justify-content: flex-end;
        }

        .fees-report-page .fees-report-more-filters {
            display: flex;
            align-items: center;
            gap: 0.45rem;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            background: #fff;
            color: #374151;
            font-size: 0.9rem;
            font-weight: 600;
            padding: 0.7rem 0.95rem;
            white-space: nowrap;
        }

        .fees-report-page .fees-report-more-filters:hover {
            background: #f8fafc;
            color: #111827;
        }

        .fees-report-page .fees-report-filter-count {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 1.35rem;
            height: 1.35rem;
            padding: 0 0.35rem;
            border-radius: 999px;
            background: #111827;
            color: #fff;
            font-size: 0.72rem;
            font-weight: 700;
        }

        .fees-report-page .fees-report-advanced-filters {
            display: none;
            border-top: 1px solid #f1f5f9;
            padding-top: 0.9rem;
        }

        .fees-report-page .fees-report-advanced-filters:not(.hidden) {
            display: block;
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
            .fees-report-page .fees-report-grid--primary,
            .fees-report-page .fees-report-advanced-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 767.98px) {
            .fees-report-page .fees-report-grid--primary,
            .fees-report-page .fees-report-advanced-grid {
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

        html[data-theme='dark'] .fees-report-page .fees-report-more-filters {
            border-color: rgba(148, 163, 184, 0.18);
            background: rgba(15, 23, 42, 0.96);
            color: #e2e8f0;
        }

        html[data-theme='dark'] .fees-report-page .fees-report-more-filters:hover {
            background: #1e293b;
            color: #f8fafc;
        }

        html[data-theme='dark'] .fees-report-page .fees-report-filter-count {
            background: #1e293b;
            color: #f8fafc;
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
    </style>
@endsection

@section('contents')
<div class="container-fluid fees-report-page">
    @php
        $reportTitle = 'Student Due Report';
    @endphp
    @include('partials.report-header')
    <div class="fees-report-shell">
        <div class="fees-report-card fees-report-no-print">
            <form method="GET" action="{{ route('fees.student-due-report') }}" id="filterForm" class="fees-report-form">
                <div class="fees-report-grid fees-report-grid--primary">
                    <div class="fees-report-field">
                        <label>Student ID</label>
                        <input type="text" name="student_id" class="form-control fees-report-input report-filter-control" value="{{ request('student_id') }}" placeholder="Search specific student">
                    </div>
                    <div class="fees-report-field">
                        <label class="font-weight-bold">Academic Year <span class="text-danger">*</span></label>
                        <select name="session_id" class="form-control fees-report-select report-filter-control">
                            <option value="">— Select Year —</option>
                            @foreach($sessions as $s)
                                <option value="{{ $s->id }}" {{ request('session_id') == $s->id ? 'selected' : '' }}>{{ $s->name_en }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="fees-report-field">
                        <label>Class</label>
                        <select name="class_id" class="form-control fees-report-select report-filter-control" id="classSelect">
                            <option value="">All Classes</option>
                            @foreach($classes as $c)
                                <option value="{{ $c->id }}" {{ request('class_id') == $c->id ? 'selected' : '' }}>{{ $c->name_en }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="fees-report-field">
                        <label>Section</label>
                        <select name="section_id" class="form-control fees-report-select report-filter-control" id="sectionSelect">
                            <option value="">All Sections</option>
                            @foreach($sections as $sec)
                                <option value="{{ $sec->id }}" {{ request('section_id') == $sec->id ? 'selected' : '' }}>{{ $sec->name_en }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="fees-report-filter-actions fees-report-filter-actions--submit">
                        <button type="submit" class="btn btn-dark fees-report-action-btn" title="Generate"><i class="fas fa-search"></i></button>
                        <a href="{{ route('fees.student-due-report') }}" class="btn btn-outline-secondary fees-report-action-btn" title="Reset"><i class="fas fa-times"></i></a>
                        @if(request('session_id') && $rows->isNotEmpty())
                            <button type="button" class="btn btn-success fees-report-action-btn" onclick="window.print()" title="Print"><i class="fas fa-print"></i></button>
                            <a href="{{ route('fees.student-due-report.pdf', request()->query()) }}" class="btn btn-danger fees-report-action-btn" title="Export PDF"><i class="fas fa-file-pdf"></i></a>
                        @endif
                    </div>
                </div>
            </form>
        </div>

        <div class="fees-report-card">
            <hr>

            @if(!request('session_id'))
                <div class="text-center py-5 text-muted">
                    <i class="fas fa-filter fa-2x mb-2"></i>
                    <p class="mb-0">Select an Academic Year to generate the report.</p>
                </div>
            @elseif($rows->isEmpty())
                <div class="text-center py-5 text-muted">
                    <i class="fas fa-inbox fa-2x mb-2"></i>
                    <p class="mb-0">No dues found for the selected filters.</p>
                </div>
            @else
                <div class="row mb-4">
                    <div class="col-md-4">
                        <div class="info-box bg-light">
                            <span class="info-box-icon bg-primary"><i class="fas fa-file-invoice-dollar"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Fee Due</span>
                                <span class="info-box-number">{{ number_format($totals['fees']['due'], 2) }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="info-box bg-light">
                            <span class="info-box-icon bg-success"><i class="fas fa-check-circle"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Inventory Due</span>
                                <span class="info-box-number">{{ number_format($totals['inventory']['due'], 2) }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="info-box bg-light">
                            <span class="info-box-icon bg-danger"><i class="fas fa-exclamation-circle"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Total Due</span>
                                <span class="info-box-number">{{ number_format($totals['due'], 2) }}</span>
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
                                <th>Type</th>
                                <th>Description</th>
                                <th class="text-right">Amount</th>
                                <th class="text-right">Paid Amount</th>
                                <th class="text-right">Due</th>
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($rows as $index => $student)
                                    @foreach($student->lines as $lineIndex => $line)
                                        <tr>
                                            <td class="text-center">{{ $lineIndex === 0 ? $index + 1 : '' }}</td>
                                            <td>{{ $lineIndex === 0 ? ($student->cid ?? '—') : '' }}</td>
                                            <td>{{ $lineIndex === 0 ? $student->name : '' }}</td>
                                            <td>{{ $lineIndex === 0 ? $student->class_name : '' }}</td>
                                            <td>{{ $lineIndex === 0 ? $student->section_name : '' }}</td>
                                            <td>
                                                <span class="badge badge-{{ $line->type === 'inventory' ? 'info' : 'primary' }}">
                                                    {{ ucfirst($line->type) }}
                                                </span>
                                            </td>
                                            <td>{{ $line->description }}</td>
                                            <td class="text-right">{{ number_format($line->amount, 2) }}</td>
                                            <td class="text-right">{{ number_format($line->paid, 2) }}</td>
                                            <td class="text-right">{{ number_format($line->due, 2) }}</td>
                                            <td class="text-center">
                                                @if($lineIndex === 0)
                                                    <a href="{{ route('fees.collect_payment', ['student_id' => $student->student_id]) }}" class="btn btn-sm btn-primary">
                                                        Collect
                                                    </a>
                                                    <a href="{{ route('fees.student-due-report.monthwise-pdf', [
                                                        'student_id' => $student->student_id,
                                                        'session_id' => request('session_id'),
                                                        'class_id' => request('class_id'),
                                                        'section_id' => request('section_id'),
                                                    ]) }}" class="btn btn-sm btn-danger" title="Download monthwise payment report">
                                                        <i class="fas fa-file-pdf"></i>
                                                    </a>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                <tr class="font-weight-bold" style="background:#f0f4ff;border-top:2px solid #aaa">
                                    <td colspan="8" class="text-right">Student Total</td>
                                    <td class="text-right">{{ number_format($student->fees_total + $student->inventory_total, 2) }}</td>
                                    <td class="text-right">{{ number_format($student->paid_amount, 2) }}</td>
                                    <td class="text-right">{{ number_format($student->due, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="font-weight-bold bg-dark text-white">
                                <td colspan="8" class="text-right">Grand Total</td>
                                <td class="text-right">{{ number_format($totals['amount'], 2) }}</td>
                                <td class="text-right">{{ number_format($totals['paid'], 2) }}</td>
                                <td class="text-right">{{ number_format($totals['due'], 2) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            @endif

        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const toggleButton = document.getElementById('report-toggle-filters');
    const advancedFilters = document.getElementById('reportAdvancedFilters');
    const countBadge = document.querySelector('[data-filter-count]');
    const filterFields = Array.from(document.querySelectorAll('.report-filter-control'));

    function setFilterCount() {
        let count = 0;

        filterFields.forEach((field) => {
            const value = field.value;
            if (Array.isArray(value) ? value.length : value !== null && value !== '') {
                count++;
            }
        });

        if (countBadge) {
            countBadge.textContent = count;
        }

        return count;
    }

    function refreshAdvancedPanelState() {
        if (!advancedFilters || !toggleButton) return;

        const activeCount = setFilterCount();
        if (activeCount > 0) {
            advancedFilters.classList.remove('hidden');
            toggleButton.setAttribute('aria-expanded', 'true');
        } else {
            toggleButton.setAttribute('aria-expanded', advancedFilters.classList.contains('hidden') ? 'false' : 'true');
        }
    }

    if (toggleButton && advancedFilters) {
        toggleButton.addEventListener('click', function () {
            advancedFilters.classList.toggle('hidden');
            toggleButton.setAttribute('aria-expanded', advancedFilters.classList.contains('hidden') ? 'false' : 'true');
        });
    }

    filterFields.forEach((field) => {
        field.addEventListener('change', refreshAdvancedPanelState);
        field.addEventListener('keyup', refreshAdvancedPanelState);
    });

    refreshAdvancedPanelState();
});
</script>

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
    .fees-report-no-print { display: none !important; }
    .fees-report-card { box-shadow: none !important; border-color: #d1d5db !important; break-inside: avoid; page-break-inside: avoid; }
    table { page-break-inside: avoid; }
    tr, td, th { page-break-inside: avoid; }
}
</style>
@endsection
