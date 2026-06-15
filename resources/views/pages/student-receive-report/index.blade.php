@extends('layouts.master')

@section('contents')
<div class="container-fluid">
    @include('partials.report-header')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title mb-0 text-white text-lg">Student Receive Report</h3>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('fees.student-receive-report') }}">
                <div class="row">
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
                <div class="row mt-3">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="font-weight-bold">From Date <span class="text-danger">*</span></label>
                            <input type="date" name="from_date" value="{{ request('from_date') }}" class="form-control form-control-sm" required>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="font-weight-bold">To Date <span class="text-danger">*</span></label>
                            <input type="date" name="to_date" value="{{ request('to_date') }}" class="form-control form-control-sm" required>
                        </div>
                    </div>
                    <div class="col-md-6 d-flex align-items-end">
                        <div class="form-group mb-0">
                            <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-search"></i> Generate</button>
                            <a href="{{ route('fees.student-receive-report') }}" class="btn btn-secondary btn-sm ml-1"><i class="fas fa-times"></i> Reset</a>
                            @if(request('from_date') && request('to_date') && $rows->isNotEmpty())
                                <button type="button" class="btn btn-success btn-sm ml-1" onclick="window.print()"><i class="fas fa-print"></i> Print</button>
                                <a href="{{ route('fees.student-receive-report.pdf', request()->query()) }}" class="btn btn-danger btn-sm ml-1"><i class="fas fa-file-pdf"></i> Export PDF</a>
                            @endif
                        </div>
                    </div>
                </div>
            </form>

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
    .main-sidebar, .main-header, .content-header, form, hr, .info-box, button, a.btn { display: none !important; }
    .content-wrapper { margin-left: 0 !important; }
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
@endsection
