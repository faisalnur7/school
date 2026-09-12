@extends('layouts.master')

@section('styles')
<style>
    .student-ledger-page .ledger-card { border: 1px solid #e5e7eb; border-radius: 14px; box-shadow: 0 8px 24px rgba(15,23,42,.04); }
    .student-ledger-page .ledger-summary td { padding: .45rem .65rem; border: 1px solid #e5e7eb; }
    .student-ledger-page .ledger-summary .label { display:block; font-size:.72rem; color:#64748b; }
    .student-ledger-page .ledger-summary .value { display:block; font-weight:700; }
    .student-ledger-page .ledger-table th, .student-ledger-page .ledger-table td { vertical-align:top; }
    .student-ledger-page .ledger-table .number { text-align:right; white-space:nowrap; }
    html[data-theme='dark'] .student-ledger-page .ledger-card,
    html[data-theme='dark'] .student-ledger-page .ledger-summary td { border-color:rgba(148,163,184,.18); }
</style>
@endsection

@section('contents')
<div class="container-fluid student-ledger-page">
    @php($reportTitle = 'Student Ledger')
    @include('partials.report-header')

    <div class="card ledger-card mb-3">
        <div class="card-body">
            <form method="GET" action="{{ route('fees.student-ledger.index') }}">
                <div class="row align-items-end">
                    <div class="col-md-3 mb-2">
                        <label class="font-weight-bold">Student ID <small class="text-muted">(optional)</small></label>
                        <input type="text" name="student_id" value="{{ request('student_id') }}" class="form-control" placeholder="All students">
                    </div>
                    <div class="col-md-3 mb-2">
                        <label class="font-weight-bold">Academic Session <span class="text-danger">*</span></label>
                        <select name="session_id" class="form-control" required>
                            <option value="">Select Session</option>
                            @foreach($sessions as $session)
                                <option value="{{ $session->id }}" @selected(request('session_id') == $session->id)>{{ $session->name_en }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2 mb-2">
                        <label class="font-weight-bold">Class</label>
                        <select name="class_id" id="ledgerClassSelect" class="form-control">
                            <option value="">All Classes</option>
                            @foreach($classes as $class)
                                <option value="{{ $class->id }}" @selected(request('class_id') == $class->id)>{{ $class->name_en }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2 mb-2">
                        <label class="font-weight-bold">Section</label>
                        <select name="section_id" id="ledgerSectionSelect" class="form-control">
                            <option value="">All Sections</option>
                            @foreach($sections as $section)
                                <option value="{{ $section->id }}" @selected(request('section_id') == $section->id)>{{ $section->name_en }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2 mb-2 d-flex" style="gap:.5rem;">
                        <button class="btn btn-dark flex-fill" type="submit" title="Generate"><i class="fas fa-search"></i></button>
                        <a class="btn btn-outline-secondary" href="{{ route('fees.student-ledger.index') }}" title="Reset"><i class="fas fa-times"></i></a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @if(!request('session_id'))
        <div class="card ledger-card"><div class="card-body text-center text-muted py-5"><i class="fas fa-book-open fa-2x mb-2"></i><p class="mb-0">Select an academic session to generate the student ledger.</p></div></div>
    @elseif($reports->isEmpty())
        <div class="card ledger-card"><div class="card-body text-center text-muted py-5"><i class="fas fa-inbox fa-2x mb-2"></i><p class="mb-0">No students or ledger records found for the selected filters.</p></div></div>
    @else
        @foreach($reports as $report)
            @php($student = $report['student'])
            @php($academicInfo = $report['academicInfo'])
            <div class="card ledger-card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center flex-wrap" style="gap:.75rem;">
                    <div><strong>{{ $student->full_name_en }}</strong><span class="text-muted ml-2">({{ $student->student_cid ?? '—' }})</span><div class="small text-muted">{{ $academicInfo->schoolClass?->name_en ?? '—' }} / {{ $academicInfo->section?->name_en ?? '—' }}</div></div>
                    <a href="{{ route('fees.student-due-report.monthwise-pdf', ['student_id' => $student->id, 'session_id' => request('session_id'), 'class_id' => request('class_id'), 'section_id' => request('section_id')]) }}" class="btn btn-sm btn-danger ml-auto" style="white-space:nowrap;"><i class="fas fa-file-pdf mr-1"></i> Export PDF</a>
                </div>
                <div class="card-body">
                    <table class="ledger-summary w-100 mb-3"><tr><td><span class="label">Student ID</span><span class="value">{{ $student->student_cid ?? '—' }}</span></td><td><span class="label">Student Name</span><span class="value">{{ $student->full_name_en ?? '—' }}</span></td><td><span class="label">Class</span><span class="value">{{ $academicInfo->schoolClass?->name_en ?? '—' }}</span></td><td><span class="label">Section</span><span class="value">{{ $academicInfo->section?->name_en ?? '—' }}</span></td></tr></table>
                    <div class="row mb-3"><div class="col-md-4"><div class="alert alert-light mb-0"><strong>Total Amount</strong><div>{{ number_format($report['totals']['amount'], 2) }}</div></div></div><div class="col-md-4"><div class="alert alert-light mb-0"><strong>Total Paid</strong><div class="text-success">{{ number_format($report['totals']['paid'], 2) }}</div></div></div><div class="col-md-4"><div class="alert alert-light mb-0"><strong>Total Due</strong><div class="text-danger">{{ number_format($report['totals']['due'], 2) }}</div></div></div></div>
                    @forelse($report['months'] as $month)
                        <h6 class="bg-light p-2 rounded mb-1 mt-3 font-weight-bold">{{ $month->label }}</h6>
                        <div class="table-responsive"><table class="table table-bordered table-sm ledger-table mb-1"><thead class="thead-dark"><tr><th>Description</th><th class="number">Amount</th><th class="number">Paid</th><th class="number">Due</th></tr></thead><tbody>
                            @foreach($month->rows as $row)<tr><td>{{ $row->description }}</td><td class="number">{{ $row->amount > 0 ? number_format($row->amount, 2) : '—' }}</td><td class="number text-success">{{ $row->paid > 0 ? number_format($row->paid, 2) : '—' }}</td><td class="number text-danger">{{ number_format($row->due, 2) }}</td></tr>@endforeach
                            <tr class="font-weight-bold bg-light"><td>Month Total</td><td class="number">{{ number_format($month->amount, 2) }}</td><td class="number">{{ number_format($month->paid, 2) }}</td><td class="number">{{ number_format($month->due, 2) }}</td></tr>
                        </tbody></table></div>
                    @empty
                        <div class="text-center text-muted py-3">No payment or inventory records found.</div>
                    @endforelse
                </div>
            </div>
        @endforeach
    @endif
</div>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const classSelect = document.getElementById('ledgerClassSelect');
    const sectionSelect = document.getElementById('ledgerSectionSelect');
    const selectedSection = @json(request('section_id'));

    function refreshSectionSelect() {
        if (sectionSelect && window.refreshSelect2) window.refreshSelect2($(sectionSelect));
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
                sectionSelect.innerHTML = '<option value="">All Sections</option>';
                sections.forEach(section => {
                    const option = new Option(section.name_en, section.id, false, String(selectedSectionId) === String(section.id));
                    sectionSelect.add(option);
                });
                refreshSectionSelect();
            })
            .catch(() => {
                sectionSelect.innerHTML = '<option value="">All Sections</option>';
                refreshSectionSelect();
            });
    }

    if (classSelect) {
        classSelect.addEventListener('change', function () { loadSections(this.value); });
        if (classSelect.value) loadSections(classSelect.value, selectedSection);
    }
});
</script>
@endsection
