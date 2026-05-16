@extends('layouts.master')
@section('contents')
<div class="container-fluid">
    @include('hr._alerts')
    <div class="card">
        <div class="card-header bg-gradient-primary text-white py-3">
            <div class="d-flex justify-content-between align-items-center">
                <h4 class="card-title mb-0 font-weight-bold text-white">
                    <i class="fas fa-money-bill-wave mr-2"></i>Payroll — {{ date('F', mktime(0,0,0,$month,1)) }} {{ $year }}
                </h4>
                <div>
                    <form action="{{ route('hr.payroll.lock') }}" method="POST" class="d-inline" onsubmit="return confirm('Lock all payrolls for this period?')">
                        @csrf
                        <input type="hidden" name="month" value="{{ $month }}">
                        <input type="hidden" name="year"  value="{{ $year }}">
                        <button class="btn btn-warning btn-sm"><i class="fas fa-lock mr-1"></i> Lock Period</button>
                    </form>
                    <a href="{{ route('hr.payroll.index') }}" class="btn btn-light btn-sm ml-1">
                        <i class="fas fa-arrow-left mr-1"></i> Back
                    </a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-3"><div class="info-box bg-light"><span class="info-box-icon bg-primary"><i class="fas fa-users"></i></span><div class="info-box-content"><span class="info-box-text">Total</span><span class="info-box-number">{{ $summary['count'] }}</span></div></div></div>
                <div class="col-md-3"><div class="info-box bg-light"><span class="info-box-icon bg-success"><i class="fas fa-check"></i></span><div class="info-box-content"><span class="info-box-text">Paid</span><span class="info-box-number">{{ $summary['paid_count'] }}</span></div></div></div>
                <div class="col-md-3"><div class="info-box bg-light"><span class="info-box-icon bg-warning"><i class="fas fa-clock"></i></span><div class="info-box-content"><span class="info-box-text">Pending</span><span class="info-box-number">{{ $summary['pending_count'] }}</span></div></div></div>
                <div class="col-md-3"><div class="info-box bg-light"><span class="info-box-icon bg-info"><i class="fas fa-wallet"></i></span><div class="info-box-content"><span class="info-box-text">Net Total</span><span class="info-box-number">৳{{ number_format($summary['total_net'], 2) }}</span></div></div></div>
            </div>

            <div class="table-responsive">
                <table class="table table-sm table-bordered">
                    <thead class="thead-dark"><tr><th>#</th><th>Employee</th><th>Designation</th><th>Gross</th><th>Deductions</th><th>Net</th><th>Method</th><th>Status</th><th>Locked</th><th class="text-center">Actions</th></tr></thead>
                    <tbody>
                        @forelse($payrolls as $i => $p)
                        <tr class="{{ $p->status === 'paid' ? 'table-success' : '' }}">
                            <td>{{ $i+1 }}</td>
                            <td>{{ $p->employee->name }}<br><small class="text-muted">{{ $p->employee->employee_id }}</small></td>
                            <td>{{ $p->employee->designation->name ?? '—' }}</td>
                            <td>৳{{ number_format($p->gross_salary, 2) }}</td>
                            <td>৳{{ number_format($p->other_deductions, 2) }}</td>
                            <td class="font-weight-bold text-success">৳{{ number_format($p->net_salary, 2) }}</td>
                            <td>{{ ucfirst(str_replace('_',' ',$p->payment_method)) }}</td>
                            <td><span class="badge badge-{{ $p->status === 'paid' ? 'success' : 'warning' }}">{{ ucfirst($p->status) }}</span></td>
                            <td>@if($p->is_locked)<i class="fas fa-lock text-danger"></i>@else<i class="fas fa-lock-open text-muted"></i>@endif</td>
                            <td class="text-center">
                                <a href="{{ route('hr.payroll.slip', $p->id) }}" class="btn btn-xs btn-info" target="_blank"><i class="fas fa-file-alt"></i></a>
                                @if(!$p->is_locked && $p->status === 'pending')
                                <form action="{{ route('hr.payroll.paid', $p->id) }}" method="POST" class="d-inline">
                                    @csrf @method('PATCH')
                                    <button class="btn btn-xs btn-success" title="Mark Paid"><i class="fas fa-check"></i></button>
                                </form>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="10" class="text-center text-muted py-4">No payrolls found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
