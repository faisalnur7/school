@extends('layouts.master')

@section('contents')
<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title mb-0 text-white text-lg">Students Due Report</h3>
        </div>
        <div class="card-body">

            <form method="GET" action="{{ route('fees.student-due-report') }}" id="filterForm">
                <div class="row">
                    <div class="col-md-2">
                        <div class="form-group">
                            <label class="font-weight-bold">Academic Year <span class="text-danger">*</span></label>
                            <select name="session_id" class="form-control form-control-sm" required onchange="this.form.submit()">
                                <option value="">— Select Year —</option>
                                @foreach($sessions as $s)
                                    <option value="{{ $s->id }}" {{ request('session_id') == $s->id ? 'selected' : '' }}>{{ $s->name_en }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>Month</label>
                            <select name="month" class="form-control form-control-sm">
                                <option value="">All Months</option>
                                @foreach(range(1,12) as $m)
                                    <option value="{{ $m }}" {{ request('month') == $m ? 'selected' : '' }}>{{ date('F', mktime(0,0,0,$m,1)) }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
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
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>Section</label>
                            <select name="section_id" class="form-control form-control-sm">
                                <option value="">All Sections</option>
                                @foreach($sections as $sec)
                                    <option value="{{ $sec->id }}" {{ request('section_id') == $sec->id ? 'selected' : '' }}>{{ $sec->name_en }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>Group</label>
                            <select name="group_id" class="form-control form-control-sm">
                                <option value="">All Groups</option>
                                @foreach($groups as $g)
                                    <option value="{{ $g->id }}" {{ request('group_id') == $g->id ? 'selected' : '' }}>{{ $g->name_en }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2 d-flex align-items-center">
                        <div class="form-group mb-0">
                            <button type="submit" class="btn btn-primary btn-sm" title="Search"><i class="fas fa-search"></i></button>
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
                    <p class="mb-0">No students found for the selected filters.</p>
                </div>
            @else
                @php
                    $sumFees = $rows->sum('total_fees');
                    $sumPaid = $rows->sum('total_paid');
                    $sumDue  = $rows->sum('due');
                    $countDue     = $rows->where('due', '>', 0)->count();
                    $countPaid    = $rows->where('due', '<=', 0)->where('total_fees', '>', 0)->count();
                    $countPartial = $rows->filter(fn($r) => $r->due > 0 && $r->total_paid > 0)->count();
                @endphp
                <div class="row mb-3">
                    <div class="col-md-3">
                        <div class="info-box bg-light">
                            <span class="info-box-icon bg-info"><i class="fas fa-file-invoice-dollar"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Total Fees</span>
                                <span class="info-box-number">{{ number_format($sumFees, 2) }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="info-box bg-light">
                            <span class="info-box-icon bg-success"><i class="fas fa-check-circle"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Total Paid</span>
                                <span class="info-box-number">{{ number_format($sumPaid, 2) }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="info-box bg-light">
                            <span class="info-box-icon bg-danger"><i class="fas fa-exclamation-circle"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Total Due</span>
                                <span class="info-box-number">{{ number_format($sumDue, 2) }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="info-box bg-light">
                            <span class="info-box-icon bg-warning"><i class="fas fa-users"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Students</span>
                                <span class="info-box-number">
                                    {{ $rows->count() }} total &nbsp;
                                    <small class="text-danger">{{ $countDue }} due</small>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered table-hover table-sm">
                        <thead class="thead-dark">
                            <tr>
                                <th>#</th>
                                <th>Roll</th>
                                <th>Student ID</th>
                                <th>Student Name</th>
                                <th>Class</th>
                                <th>Section</th>
                                <th>Group</th>
                                <th class="text-right">Total Fees</th>
                                <th class="text-right">Paid</th>
                                <th class="text-right">Due</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                        
                            @foreach($rows as $i => $row)
                                @php $isDue = $row->due > 0; @endphp
                                <tr class="}}">
                                    <td>{{ $i + 1 }}</td>
                                    <td>{{ $row->roll ?? '—' }}</td>
                                    <td>{{ $row->cid ?? '—' }}</td>
                                    <td class="font-weight-bold">{{ $row->name }}</td>
                                    <td>{{ $row->class_name }}</td>
                                    <td>{{ $row->section_name }}</td>
                                    <td>{{ $row->group_name }}</td>
                                    <td class="text-right">{{ number_format($row->total_fees, 2) }}</td>
                                    <td class="text-right text-success font-weight-bold">{{ number_format($row->total_paid, 2) }}</td>
                                    <td class="text-right {{ $isDue ? 'text-danger font-weight-bold' : 'text-success' }}">{{ number_format($row->due, 2) }}</td>
                                    <td>
                                        @if($row->due <= 0)
                                            <span class="badge badge-success">Paid</span>
                                        @elseif($row->total_paid > 0)
                                            <span class="badge badge-warning">Partial</span>
                                        @else
                                            <span class="badge badge-danger">Unpaid</span>
                                        @endif
                                    </td>
                                    <td><a href="{{ route('students.show', $row->student_id) }}" class="btn btn-xs btn-info"><i class="fas fa-eye"></i> Details</a></td>

                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="font-weight-bold bg-light">
                                <td colspan="8">Total ({{ $rows->count() }} students)</td>
                                <td class="text-right">{{ number_format($sumFees, 2) }}</td>
                                <td class="text-right text-success">{{ number_format($sumPaid, 2) }}</td>
                                <td class="text-right text-danger">{{ number_format($sumDue, 2) }}</td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            @endif

        </div>
    </div>
</div>

<script>
document.getElementById('classSelect').addEventListener('change', function () {
    const url = new URL(window.location.href);
    url.searchParams.set('class_id', this.value);
    url.searchParams.delete('section_id');
    url.searchParams.delete('group_id');
    if (document.querySelector('[name="session_id"]').value)
        url.searchParams.set('session_id', document.querySelector('[name="session_id"]').value);
    window.location.href = url.toString();
});
</script>

<style>
@media print {
    .main-sidebar, .main-header, .content-header, form, hr, .info-box, button, a.btn { display: none !important; }
    .content-wrapper { margin-left: 0 !important; }
    .table-danger { background-color: #ffe0e0 !important; -webkit-print-color-adjust: exact; }
    .table-success { background-color: #e0ffe0 !important; -webkit-print-color-adjust: exact; }
}
</style>
@endsection
