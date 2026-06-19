@extends('layouts.master')

@section('contents')
<div class="container-fluid">
    @include('partials.report-header')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title mb-0 text-white text-lg">Student Receivable Report</h3>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('fees.student-receivable-report') }}">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="font-weight-bold">Student ID</label>
                            <input type="text" name="student_id" value="{{ request('student_id') }}" class="form-control form-control-sm" placeholder="Search specific student">
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
                    <div class="col-md-3"></div>
                </div>
                <div class="row mt-2">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="font-weight-bold">From Date</label>
                            <input type="date" name="from_date" value="{{ request('from_date') }}" class="form-control form-control-sm">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="font-weight-bold">To Date</label>
                            <input type="date" name="to_date" value="{{ request('to_date') }}" class="form-control form-control-sm">
                        </div>
                    </div>
                    <div class="col-md-6 d-flex align-items-end">
                        <div class="form-group mb-0">
                            <button type="submit" class="btn btn-primary btn-sm" title="Generate"><i class="fas fa-search"></i></button>
                            <a href="{{ route('fees.student-receivable-report') }}" class="btn btn-secondary btn-sm ml-1" title="Reset"><i class="fas fa-times"></i></a>
                            @if(request('from_date') && request('to_date') && $rows->isNotEmpty())
                                <button type="button" class="btn btn-success btn-sm ml-1" onclick="window.print()" title="Print"><i class="fas fa-print"></i></button>
                                <a href="{{ route('fees.student-receivable-report.pdf', request()->query()) }}" class="btn btn-danger btn-sm ml-1" title="Export PDF"><i class="fas fa-file-pdf"></i></a>
                            @endif
                        </div>
                    </div>
                </div>
            </form>

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
