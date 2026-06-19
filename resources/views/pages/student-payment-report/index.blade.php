@extends('layouts.master')

@section('contents')
<div class="container-fluid">
    @include('partials.report-header')

    <div class="card">
        <div class="card-header">
            <h3 class="card-title mb-0 text-white text-lg">Student Payment Report</h3>
        </div>
        <div class="card-body">
            @php
                $selectedCategoryKeys = $selectedCategoryKeys ?? ($availableCategories->pluck('column_key')->all() ?? []);
                $reportPdfQuery = collect([
                    'student_id' => request('student_id'),
                    'session_id' => request('session_id'),
                    'class_id' => request('class_id'),
                    'section_id' => request('section_id'),
                    'from_date' => request('from_date'),
                    'to_date' => request('to_date'),
                    'date' => request('date'),
                    'columns_present' => request()->has('columns_present') ? 1 : null,
                    'columns' => request('columns'),
                ])->filter(function ($value) {
                    return is_array($value) ? ! empty($value) : filled($value);
                })->all();
            @endphp

            <form method="GET" action="{{ route('fees.payment-report') }}">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="font-weight-bold">Student ID</label>
                            <input type="text" name="student_id" value="{{ old('student_id', request('student_id')) }}" class="form-control form-control-sm" placeholder="Search specific student">
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="font-weight-bold">Academic Session</label>
                            <select name="session_id" class="form-control form-control-sm">
                                <option value="">All Sessions</option>
                                @foreach($sessions as $session)
                                    <option value="{{ $session->id }}" {{ request('session_id') == $session->id ? 'selected' : '' }}>{{ $session->name_en }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="font-weight-bold">Class</label>
                            <select name="class_id" id="classSelect" class="form-control form-control-sm">
                                <option value="">All Classes</option>
                                @foreach($classes as $class)
                                    <option value="{{ $class->id }}" {{ request('class_id') == $class->id ? 'selected' : '' }}>{{ $class->name_en }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="font-weight-bold">Section</label>
                            <select name="section_id" id="sectionSelect" class="form-control form-control-sm">
                                <option value="">All Sections</option>
                                @foreach($sections as $section)
                                    <option value="{{ $section->id }}" {{ request('section_id') == $section->id ? 'selected' : '' }}>{{ $section->name_en }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="font-weight-bold">From Date <span class="text-danger">*</span></label>
                            <input type="date" name="from_date" value="{{ old('from_date', $fromDate ?? request('from_date', request('date'))) }}" class="form-control form-control-sm">
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="font-weight-bold">To Date <span class="text-danger">*</span></label>
                            <input type="date" name="to_date" value="{{ old('to_date', $toDate ?? request('to_date', request('date'))) }}" class="form-control form-control-sm">
                        </div>
                    </div>
                </div>

                <input type="hidden" name="columns_present" value="1">

                <div class="border rounded bg-light p-3 mb-3">
                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-2">
                        <div>
                            <div class="font-weight-bold text-dark">Column Selection</div>
                            <small class="text-muted">Choose which fee columns should appear in the table and PDF.</small>
                        </div>
                        <div class="form-check mb-0">
                            <input class="form-check-input" type="checkbox" id="payment-report-toggle-all" {{ count($selectedCategoryKeys) === count($availableCategories) ? 'checked' : '' }}>
                            <label class="form-check-label" for="payment-report-toggle-all">Select all</label>
                        </div>
                    </div>

                    <div class="row">
                        @foreach($availableCategories as $category)
                            <div class="col-6 col-md-4 col-lg-3 mb-2">
                                <div class="form-check">
                                    <input
                                        class="form-check-input payment-report-column-checkbox"
                                        type="checkbox"
                                        name="columns[]"
                                        value="{{ $category->column_key }}"
                                        id="payment-report-column-{{ $category->column_key }}"
                                        {{ in_array($category->column_key, $selectedCategoryKeys, true) ? 'checked' : '' }}
                                    >
                                    <label class="form-check-label" for="payment-report-column-{{ $category->column_key }}">
                                        {{ $category->name }}
                                    </label>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="d-flex flex-wrap gap-2 mt-2 mb-3">
                    <button type="submit" class="btn btn-primary btn-sm" title="Generate">
                        <i class="fas fa-search"></i>
                    </button>
                    <a href="{{ route('fees.payment-report') }}" class="btn btn-secondary btn-sm" title="Reset">
                        <i class="fas fa-times"></i>
                    </a>
                    <button type="button" class="btn btn-success btn-sm" onclick="window.print()" title="Print">
                        <i class="fas fa-print"></i>
                    </button>
                    <a href="{{ route('fees.payment-report.pdf', $reportPdfQuery) }}" class="btn btn-danger btn-sm" title="Export PDF">
                        <i class="fas fa-file-pdf"></i>
                    </a>
                </div>
            </form>

            <script>
                (function () {
                    const toggleAll = document.getElementById('payment-report-toggle-all');
                    const checks = Array.from(document.querySelectorAll('.payment-report-column-checkbox'));
                    if (!toggleAll || !checks.length) return;

                    const syncToggle = () => {
                        toggleAll.checked = checks.every((checkbox) => checkbox.checked);
                    };

                    toggleAll.addEventListener('change', function () {
                        checks.forEach((checkbox) => {
                            checkbox.checked = toggleAll.checked;
                        });
                    });

                    checks.forEach((checkbox) => checkbox.addEventListener('change', syncToggle));
                    syncToggle();
                })();
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

            <hr>

            @if(!$dateLabel)
                <div class="text-center py-5 text-muted">
                    <i class="fas fa-filter fa-2x mb-2"></i>
                    <p class="mb-0">Select a date range to generate the student payment report.</p>
                </div>
            @elseif($categories->isEmpty())
                <div class="text-center py-5 text-muted">
                    <i class="fas fa-columns fa-2x mb-2"></i>
                    <p class="mb-0">Select at least one column to generate the report.</p>
                </div>
            @elseif($rows->isEmpty())
                <div class="text-center py-5 text-muted">
                    <i class="fas fa-inbox fa-2x mb-2"></i>
                    <p class="mb-0">No payment records found for the selected date range.</p>
                </div>
            @else
                <div class="row mb-3">
                    <div class="col-md-4">
                        <div class="info-box bg-light">
                            <span class="info-box-icon bg-info"><i class="fas fa-user-graduate"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Students</span>
                                <span class="info-box-number">{{ $rows->sum(fn($g) => $g->students->count()) }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="info-box bg-light">
                            <span class="info-box-icon bg-success"><i class="fas fa-check-circle"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Grand Total Paid</span>
                                <span class="info-box-number">{{ number_format($rows->sum(fn($g) => $g->students->sum('grand_total')), 2) }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="info-box bg-light">
                            <span class="info-box-icon bg-secondary"><i class="fas fa-calendar-alt"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Filter</span>
                                <span class="info-box-number">{{ $dateLabel }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                @foreach($rows as $group)
                    <div class="mb-4">
                        <h5 class="mb-2 bg-light p-2 rounded">
                            <strong>Class:</strong> {{ $group->class_name }} | <strong>Section:</strong> {{ $group->section_name }}
                        </h5>
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover table-sm">
                                <thead class="thead-dark">
                                    <tr>
                                        <th>#</th>
                                        <th>Student ID</th>
                                        <th>Name</th>
                                        @foreach($categories as $category)
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
                                            @foreach($categories as $category)
                                                <td class="text-right">{{ number_format($row->{$category->column_key}, 2) }}</td>
                                            @endforeach
                                            <td class="text-right font-weight-bold">{{ number_format($row->grand_total, 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr class="font-weight-bold bg-light">
                                        <td colspan="3">Subtotal</td>
                                        @foreach($categories as $category)
                                            <td class="text-right">{{ number_format($group->students->sum(fn($r) => $r->{$category->column_key}), 2) }}</td>
                                        @endforeach
                                        <td class="text-right">{{ number_format($group->students->sum('grand_total'), 2) }}</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                @endforeach
            @endif
        </div>
    </div>
</div>

<style>
@media print {
    .main-sidebar, .main-header, .content-header, form, hr, .info-box, button, a.btn { display: none !important; }
    .content-wrapper { margin-left: 0 !important; }
}
</style>
@endsection
