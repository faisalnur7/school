@extends('layouts.master')
@section('contents')
<div class="container-fluid">
    <div class="card">
        <div class="card-header bg-gradient-primary text-white py-3">
            <div class="d-flex justify-content-between align-items-center">
                <h4 class="card-title mb-0 font-weight-bold text-white">
                    <i class="fas fa-eye mr-2"></i>Payroll Preview — {{ date('F', mktime(0,0,0,$month,1)) }} {{ $year }}
                </h4>
                <a href="{{ route('hr.payroll.index') }}" class="btn btn-light btn-sm">
                    <i class="fas fa-arrow-left mr-1"></i> Back
                </a>
            </div>
        </div>
        <div class="card-body">
            @php
                $valid   = collect($rows)->where('missing_salary', false)->where('already_exists', false);
                $missing = collect($rows)->where('missing_salary', true);
                $exists  = collect($rows)->where('already_exists', true);
            @endphp

            @if($missing->count())
            <div class="alert alert-warning"><i class="fas fa-exclamation-triangle"></i> <strong>{{ $missing->count() }}</strong> employees have no salary structure and will be skipped.</div>
            @endif
            @if($exists->count())
            <div class="alert alert-info"><i class="fas fa-info-circle"></i> <strong>{{ $exists->count() }}</strong> employees already have payroll for this period and will be skipped.</div>
            @endif

            <div class="row mb-3">
                <div class="col-md-3"><div class="info-box bg-light"><span class="info-box-icon bg-primary"><i class="fas fa-users"></i></span><div class="info-box-content"><span class="info-box-text">Will Generate</span><span class="info-box-number">{{ $valid->count() }}</span></div></div></div>
                <div class="col-md-3"><div class="info-box bg-light"><span class="info-box-icon bg-success"><i class="fas fa-money-bill"></i></span><div class="info-box-content"><span class="info-box-text">Total Gross</span><span class="info-box-number">৳{{ number_format($valid->sum('gross'), 2) }}</span></div></div></div>
                <div class="col-md-3"><div class="info-box bg-light"><span class="info-box-icon bg-info"><i class="fas fa-wallet"></i></span><div class="info-box-content"><span class="info-box-text">Total Net</span><span class="info-box-number">৳{{ number_format($valid->sum('net'), 2) }}</span></div></div></div>
            </div>

            <div class="table-responsive">
                <table class="table table-sm table-bordered">
                    <thead class="thead-dark"><tr><th>#</th><th>Employee</th><th>Designation</th><th>Gross</th><th>Deductions</th><th>Net</th><th>Status</th></tr></thead>
                    <tbody>
                        @foreach($rows as $i => $row)
                        <tr class="{{ $row['missing_salary'] || $row['already_exists'] ? 'table-warning' : '' }}">
                            <td>{{ $i+1 }}</td>
                            <td>{{ $row['employee']->name }}<br><small class="text-muted">{{ $row['employee']->employee_id }}</small></td>
                            <td>{{ $row['employee']->designation->name ?? '—' }}</td>
                            <td>৳{{ number_format($row['gross'], 2) }}</td>
                            <td>৳{{ number_format($row['deductions'], 2) }}</td>
                            <td class="font-weight-bold">৳{{ number_format($row['net'], 2) }}</td>
                            <td>
                                @if($row['missing_salary']) <span class="badge badge-warning">No Salary</span>
                                @elseif($row['already_exists']) <span class="badge badge-info">Already Exists</span>
                                @else <span class="badge badge-success">Ready</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($valid->count())
            <form action="{{ route('hr.payroll.generate') }}" method="POST" onsubmit="return confirm('Generate payroll for {{ $valid->count() }} employees?')">
                @csrf
                <input type="hidden" name="month" value="{{ $month }}">
                <input type="hidden" name="year"  value="{{ $year }}">
                <button type="submit" class="btn btn-success"><i class="fas fa-cog"></i> Generate {{ $valid->count() }} Payrolls</button>
            </form>
            @endif
        </div>
    </div>
</div>
@endsection
