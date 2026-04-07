@extends('layouts.master')
@section('contents')
<div class="container-fluid">

    @if($missingSalary > 0)
    <div class="alert alert-warning d-flex align-items-center justify-content-between">
        <span><i class="fas fa-exclamation-triangle mr-2"></i> <strong>{{ $missingSalary }}</strong> active employees have no salary structure.</span>
        <a href="{{ route('hr.employees.index', ['missing_salary' => 1]) }}" class="btn btn-sm btn-warning">Fix Now →</a>
    </div>
    @endif

    {{-- Metric Cards --}}
    <div class="row">
        <div class="col-md-3">
            <div class="info-box bg-light">
                <span class="info-box-icon bg-primary"><i class="fas fa-users"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Active Employees</span>
                    <span class="info-box-number">{{ $totalEmployees }}</span>
                    <span class="progress-description">Teachers: {{ $totalTeachers }} | Staff: {{ $totalStaff }}</span>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="info-box bg-light">
                <span class="info-box-icon bg-success"><i class="fas fa-money-bill-wave"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">This Month Net Payroll</span>
                    <span class="info-box-number">৳{{ number_format($currentPayroll['total_net'], 2) }}</span>
                    <span class="progress-description">{{ $currentPayroll['count'] }} employees processed</span>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="info-box bg-light">
                <span class="info-box-icon bg-warning"><i class="fas fa-calendar-times"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Pending Leave Requests</span>
                    <span class="info-box-number">{{ $pendingLeaves }}</span>
                    <span class="progress-description">Today submitted: {{ $leaveToday }}</span>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="info-box bg-light {{ $missingSalary > 0 ? 'border border-danger' : '' }}">
                <span class="info-box-icon {{ $missingSalary > 0 ? 'bg-danger' : 'bg-secondary' }}"><i class="fas fa-exclamation-circle"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Missing Salary Structures</span>
                    <span class="info-box-number">{{ $missingSalary }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Quick Actions --}}
    <div class="row mb-3">
        <div class="col-12">
            <div class="card">
                <div class="card-body py-2">
                    <a href="{{ route('hr.employees.create') }}" class="btn btn-primary btn-sm mr-2"><i class="fas fa-user-plus"></i> Add Employee</a>
                    <a href="{{ route('hr.payroll.index') }}" class="btn btn-success btn-sm mr-2"><i class="fas fa-money-check-alt"></i> Generate Payroll</a>
                    <a href="{{ route('hr.leave.index', ['status' => 'pending']) }}" class="btn btn-warning btn-sm mr-2"><i class="fas fa-check-circle"></i> Approve Leaves</a>
                    <a href="{{ route('hr.reports.salary-sheet') }}" class="btn btn-info btn-sm"><i class="fas fa-chart-bar"></i> View Reports</a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        {{-- Recent Employees --}}
        <div class="col-md-7">
            <div class="card">
                <div class="card-header"><h3 class="card-title">Recent Employees</h3></div>
                <div class="card-body p-0">
                    <table class="table table-sm table-hover mb-0">
                        <thead class="thead-light"><tr><th>Photo</th><th>Name</th><th>Designation</th><th>Joined</th></tr></thead>
                        <tbody>
                            @forelse($recentEmployees as $emp)
                            <tr>
                                <td><img src="{{ $emp->photo_url }}" class="img-circle" style="width:32px;height:32px;object-fit:cover"></td>
                                <td><a href="{{ route('hr.employees.show', $emp) }}">{{ $emp->name }}</a><br><small class="text-muted">{{ $emp->employee_id }}</small></td>
                                <td>{{ $emp->designation->name ?? '—' }}</td>
                                <td>{{ $emp->joining_date?->format('d M Y') }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="4" class="text-center text-muted py-3">No employees yet.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Payroll History --}}
        <div class="col-md-5">
            <div class="card">
                <div class="card-header"><h3 class="card-title">Recent Payroll</h3></div>
                <div class="card-body p-0">
                    <table class="table table-sm mb-0">
                        <thead class="thead-light"><tr><th>Period</th><th>Employees</th><th>Net Total</th></tr></thead>
                        <tbody>
                            @forelse($payrollHistory as $p)
                            <tr>
                                <td><a href="{{ route('hr.payroll.show', [$p->payroll_month, $p->payroll_year]) }}">{{ date('F', mktime(0,0,0,$p->payroll_month,1)) }} {{ $p->payroll_year }}</a></td>
                                <td>{{ $p->count }}</td>
                                <td class="font-weight-bold text-success">৳{{ number_format($p->total_net, 2) }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="3" class="text-center text-muted py-3">No payroll generated yet.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
