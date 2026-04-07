@extends('layouts.master')
@section('contents')
<div class="container-fluid">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="card-title mb-0 text-white text-lg">Salary Sheet</h3>
            <button onclick="window.print()" class="btn btn-success btn-sm no-print ml-auto"><i class="fas fa-print"></i></button>
        </div>
        <div class="card-body">
            <form method="GET" class="mb-3 no-print">
                <div class="row">
                    <div class="col-md-2 form-group mb-0">
                        <select name="employee_type" class="form-control form-control-sm">
                            <option value="">All Types</option>
                            <option value="teacher" {{ request('employee_type') === 'teacher' ? 'selected' : '' }}>Teacher</option>
                            <option value="staff"   {{ request('employee_type') === 'staff'   ? 'selected' : '' }}>Staff</option>
                        </select>
                    </div>
                    <div class="col-md-2 form-group mb-0">
                        <select name="designation_id" class="form-control form-control-sm">
                            <option value="">All Designations</option>
                            @foreach($designations as $d)
                                <option value="{{ $d->id }}" {{ request('designation_id') == $d->id ? 'selected' : '' }}>{{ $d->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2 form-group mb-0">
                        <select name="department" class="form-control form-control-sm">
                            <option value="">All Departments</option>
                            @foreach($departments as $dep)
                                <option value="{{ $dep }}" {{ request('department') === $dep ? 'selected' : '' }}>{{ $dep }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2 form-group mb-0" style="gap:6px;display:flex">
                        <button type="submit" class="btn btn-primary btn-sm ml-auto"><i class="fas fa-search"></i></button>
                        <a href="{{ route('hr.reports.salary-sheet') }}" class="btn btn-secondary btn-sm"><i class="fas fa-times"></i></a>
                    </div>
                </div>
            </form>

            @if($employees->isEmpty())
                <div class="text-center text-muted py-4"><i class="fas fa-inbox fa-2x mb-2 d-block"></i>No records found.</div>
            @else
            @php
                $totalGross = $employees->sum(fn($e) => $e->salaryStructure?->gross_salary ?? 0);
                $totalNet   = $employees->sum(fn($e) => $e->salaryStructure?->net_salary ?? 0);
            @endphp
            <div class="table-responsive">
                <table class="table table-sm table-bordered">
                    <thead class="thead-dark"><tr><th>#</th><th>Employee ID</th><th>Name</th><th>Designation</th><th>Dept</th><th>Basic</th><th>Allowances</th><th>Gross</th><th>Deductions</th><th>Net</th></tr></thead>
                    <tbody>
                        @foreach($employees as $i => $e)
                        @php $s = $e->salaryStructure; @endphp
                        <tr>
                            <td>{{ $i+1 }}</td>
                            <td><code>{{ $e->employee_id }}</code></td>
                            <td>{{ $e->name }}</td>
                            <td>{{ $e->designation->name ?? '—' }}</td>
                            <td>{{ $e->department ?? '—' }}</td>
                            <td>৳{{ number_format($s?->basic_salary ?? 0, 2) }}</td>
                            <td>৳{{ number_format(($s?->house_rent ?? 0) + ($s?->medical_allowance ?? 0) + ($s?->transport_allowance ?? 0) + ($s?->special_allowance ?? 0) + ($s?->bonus ?? 0), 2) }}</td>
                            <td class="font-weight-bold">৳{{ number_format($s?->gross_salary ?? 0, 2) }}</td>
                            <td class="text-danger">৳{{ number_format($s?->other_deductions ?? 0, 2) }}</td>
                            <td class="font-weight-bold text-success">৳{{ number_format($s?->net_salary ?? 0, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="font-weight-bold" style="background:#f5f5f5">
                            <td colspan="7">Total ({{ $employees->count() }} employees)</td>
                            <td>৳{{ number_format($totalGross, 2) }}</td>
                            <td></td>
                            <td>৳{{ number_format($totalNet, 2) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            @endif
        </div>
    </div>
</div>
<style>@media print { .no-print,.main-sidebar,.main-header,.content-header{display:none!important} .content-wrapper{margin-left:0!important} table{font-size:11px} th{background:#f5f5f5!important} }</style>
@endsection
