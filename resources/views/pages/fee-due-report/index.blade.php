@extends('layouts.master')

@section('contents')
<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title mb-0 text-white text-lg">Fee Due Report — Class / Section / Group Summary</h3>
        </div>
        <div class="card-body">

            <form method="GET" action="{{ route('fees.due-report') }}">
                <div class="row">
                    <div class="col-md-3">
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
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Month <small class="text-muted">(leave blank for yearly)</small></label>
                            <select name="month" class="form-control form-control-sm">
                                <option value="">All Year</option>
                                @foreach(range(1,12) as $m)
                                    <option value="{{ $m }}" {{ request('month') == $m ? 'selected' : '' }}>{{ date('F', mktime(0,0,0,$m,1)) }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6 d-flex align-items-center">
                        <div class="form-group mb-0">
                            <button type="submit" class="btn btn-primary btn-sm ml-auto"><i class="fas fa-search"></i> Generate</button>
                            <a href="{{ route('fees.due-report') }}" class="btn btn-secondary btn-sm ml-1"><i class="fas fa-times"></i> Reset</a>
                            @if(request('session_id') && $rows->isNotEmpty())
                                <button type="button" class="btn btn-success btn-sm ml-1" onclick="window.print()"><i class="fas fa-print"></i> Print</button>
                                <a href="{{ route('fees.due-report.pdf', request()->query()) }}" class="btn btn-danger btn-sm ml-1"><i class="fas fa-file-pdf"></i> Export PDF</a>
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
                    <p class="mb-0">No fee records found.</p>
                </div>
            @else
                @php
                    $sumFees = $rows->sum('total_fees');
                    $sumPaid = $rows->sum('total_paid');
                    $sumDue  = $rows->sum('due');
                @endphp
                <div class="row mb-3">
                    <div class="col-md-4">
                        <div class="info-box bg-light">
                            <span class="info-box-icon bg-info"><i class="fas fa-file-invoice-dollar"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Total Fees</span>
                                <span class="info-box-number">{{ number_format($sumFees, 2) }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="info-box bg-light">
                            <span class="info-box-icon bg-success"><i class="fas fa-check-circle"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Total Paid</span>
                                <span class="info-box-number">{{ number_format($sumPaid, 2) }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="info-box bg-light">
                            <span class="info-box-icon bg-danger"><i class="fas fa-exclamation-circle"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Total Due</span>
                                <span class="info-box-number">{{ number_format($sumDue, 2) }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered table-hover table-sm">
                        <thead class="thead-dark">
                            <tr>
                                <th>#</th>
                                <th>Class</th>
                                @if($mode === 'monthly')
                                    <th>Section</th>
                                    <th>Group</th>
                                @endif
                                <th class="text-right">Total Fees</th>
                                <th class="text-right">Total Paid</th>
                                <th class="text-right">Due</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($rows as $i => $row)
                                @php $isDue = $row->due > 0; @endphp
                                <tr class="{{ $isDue ? 'table-danger' : 'table-success' }}">
                                    <td>{{ $i + 1 }}</td>
                                    <td class="font-weight-bold">{{ $row->class_name }}</td>
                                    @if($mode === 'monthly')
                                        <td>{{ $row->section_name }}</td>
                                        <td>{{ $row->group_name }}</td>
                                    @endif
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
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="font-weight-bold bg-light">
                                <td colspan="{{ $mode === 'monthly' ? 4 : 2 }}">Total</td>
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

<style>
@media print {
    .main-sidebar, .main-header, .content-header, form, hr, .info-box, button, a.btn { display: none !important; }
    .content-wrapper { margin-left: 0 !important; }
    .table-danger { background-color: #ffe0e0 !important; -webkit-print-color-adjust: exact; }
    .table-success { background-color: #e0ffe0 !important; -webkit-print-color-adjust: exact; }
}
</style>
@endsection
