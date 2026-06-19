@extends('layouts.master')

@section('contents')
<div class="container-fluid">
    @include('partials.report-header')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title mb-0 text-white text-lg">Fees & Inventory Due Report</h3>
        </div>
        <div class="card-body">

            <form method="GET" action="{{ route('fees.student-due-report') }}" id="filterForm">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Student ID</label>
                            <input type="text" name="student_id" class="form-control form-control-sm" value="{{ request('student_id') }}" placeholder="Search specific student">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="font-weight-bold">Academic Year <span class="text-danger">*</span></label>
                            <select name="session_id" class="form-control form-control-sm" onchange="this.form.submit()">
                                <option value="">— Select Year —</option>
                                @foreach($sessions as $s)
                                    <option value="{{ $s->id }}" {{ request('session_id') == $s->id ? 'selected' : '' }}>{{ $s->name_en }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Class</label>
                            <select name="class_id" class="form-control form-control-sm" id="classSelect">
                                <option value="">All Classes</option>
                                @foreach($classes as $c)
                                    <option value="{{ $c->id }}" {{ request('class_id') == $c->id ? 'selected' : '' }}>{{ $c->name_en }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Section</label>
                            <select name="section_id" class="form-control form-control-sm" id="sectionSelect">
                                <option value="">All Sections</option>
                                @foreach($sections as $sec)
                                    <option value="{{ $sec->id }}" {{ request('section_id') == $sec->id ? 'selected' : '' }}>{{ $sec->name_en }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3 d-flex align-items-center">
                        <div class="form-group mb-0">
                            <button type="submit" class="btn btn-primary btn-sm" title="Generate"><i class="fas fa-search"></i></button>
                            <a href="{{ route('fees.student-due-report') }}" class="btn btn-secondary btn-sm ml-1" title="Reset"><i class="fas fa-times"></i></a>
                            @if(request('session_id') && $rows->isNotEmpty())
                                <button type="button" class="btn btn-success btn-sm ml-1" onclick="window.print()" title="Print"><i class="fas fa-print"></i></button>
                                <a href="{{ route('fees.student-due-report.pdf', request()->query()) }}" class="btn btn-danger btn-sm ml-1" title="Export PDF"><i class="fas fa-file-pdf"></i></a>
                            @endif
                        </div>
                    </div>
                </div>
            </form>

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
                                                    <a href="{{ route('fees.collect_payment', $student->student_id) }}" class="btn btn-sm btn-primary">
                                                        Collect
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
    .main-sidebar, .main-header, .content-header, form, hr, .info-box, button, a.btn { display: none !important; }
    .content-wrapper { margin-left: 0 !important; }
    table { page-break-inside: avoid; }
    tr, td, th { page-break-inside: avoid; }
}
</style>
@endsection
