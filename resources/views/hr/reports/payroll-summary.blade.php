@extends('layouts.master')
@section('contents')
<div class="container-fluid">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="card-title mb-0 text-white text-lg">Payroll Summary — {{ date('F', mktime(0,0,0,$month,1)) }} {{ $year }}</h3>
            <button onclick="window.print()" class="btn btn-success btn-sm no-print ml-auto"><i class="fas fa-print"></i></button>
        </div>
        <div class="card-body">
            <form method="GET" class="mb-3 no-print">
                <div class="row">
                    <div class="col-md-2 form-group mb-0">
                        <select name="month" class="form-control form-control-sm">
                            @foreach(range(1,12) as $m)
                                <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>{{ date('F', mktime(0,0,0,$m,1)) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2 form-group mb-0">
                        <input type="number" name="year" class="form-control form-control-sm" value="{{ $year }}" min="2000">
                    </div>
                    <div class="col-md-2 form-group mb-0">
                        <button type="submit" class="btn btn-primary btn-sm ml-auto"><i class="fas fa-search"></i></button>
                    </div>
                </div>
            </form>

            <div class="row mb-3">
                <div class="col-md-3"><div class="info-box bg-light"><span class="info-box-icon bg-primary"><i class="fas fa-users"></i></span><div class="info-box-content"><span class="info-box-text">Total</span><span class="info-box-number">{{ $summary['count'] }}</span></div></div></div>
                <div class="col-md-3"><div class="info-box bg-light"><span class="info-box-icon bg-success"><i class="fas fa-money-bill"></i></span><div class="info-box-content"><span class="info-box-text">Total Gross</span><span class="info-box-number">৳{{ number_format($summary['total_gross'], 2) }}</span></div></div></div>
                <div class="col-md-3"><div class="info-box bg-light"><span class="info-box-icon bg-info"><i class="fas fa-wallet"></i></span><div class="info-box-content"><span class="info-box-text">Total Net</span><span class="info-box-number">৳{{ number_format($summary['total_net'], 2) }}</span></div></div></div>
                <div class="col-md-3"><div class="info-box bg-light"><span class="info-box-icon bg-warning"><i class="fas fa-clock"></i></span><div class="info-box-content"><span class="info-box-text">Pending</span><span class="info-box-number">{{ $summary['pending'] }}</span></div></div></div>
            </div>

            @if($payrolls->isEmpty())
                <div class="text-center text-muted py-4"><i class="fas fa-inbox fa-2x mb-2 d-block"></i>No payroll for this period.</div>
            @else
            <div class="table-responsive">
                <table class="table table-sm table-bordered">
                    <thead class="thead-dark"><tr><th>#</th><th>Employee</th><th>Designation</th><th>Gross</th><th>Deductions</th><th>Net</th><th>Method</th><th>Status</th></tr></thead>
                    <tbody>
                        @foreach($payrolls as $i => $p)
                        <tr>
                            <td>{{ $i+1 }}</td>
                            <td>{{ $p->employee->name }}<br><small class="text-muted">{{ $p->employee->employee_id }}</small></td>
                            <td>{{ $p->employee->designation->name ?? '—' }}</td>
                            <td>৳{{ number_format($p->gross_salary, 2) }}</td>
                            <td>৳{{ number_format($p->other_deductions, 2) }}</td>
                            <td class="font-weight-bold text-success">৳{{ number_format($p->net_salary, 2) }}</td>
                            <td>{{ ucfirst(str_replace('_',' ',$p->payment_method)) }}</td>
                            <td><span class="badge badge-{{ $p->status === 'paid' ? 'success' : 'warning' }}">{{ ucfirst($p->status) }}</span></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif
        </div>
    </div>
</div>
<style>@media print { .no-print,.main-sidebar,.main-header,.content-header{display:none!important} .content-wrapper{margin-left:0!important} table{font-size:11px} }</style>
@endsection
