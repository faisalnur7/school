@extends('layouts.master')
@section('contents')
<div class="container-fluid">
    <div class="card">
        <div class="card-header bg-gradient-primary text-white py-3">
            <div class="d-flex justify-content-between align-items-center">
                <h4 class="card-title mb-0 font-weight-bold text-white">
                    <i class="fas fa-file-alt mr-2"></i>Leave Report
                </h4>
                <button onclick="window.print()" class="btn btn-light btn-sm no-print">
                    <i class="fas fa-print mr-1"></i> Print
                </button>
            </div>
        </div>
        <div class="card-body">
            <form method="GET" class="mb-3 no-print">
                <div class="row">
                    <div class="col-md-2 form-group mb-0">
                        <select name="employee_id" class="form-control form-control-sm">
                            <option value="">All Employees</option>
                            @foreach($employees as $e)
                                <option value="{{ $e->id }}" {{ request('employee_id') == $e->id ? 'selected' : '' }}>{{ $e->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2 form-group mb-0">
                        <select name="leave_type" class="form-control form-control-sm">
                            <option value="">All Types</option>
                            @foreach(['casual','sick','annual','maternity','other'] as $t)
                                <option value="{{ $t }}" {{ request('leave_type') === $t ? 'selected' : '' }}>{{ ucfirst($t) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2 form-group mb-0">
                        <select name="status" class="form-control form-control-sm">
                            <option value="">All Status</option>
                            <option value="pending"  {{ request('status') === 'pending'  ? 'selected' : '' }}>Pending</option>
                            <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
                            <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                        </select>
                    </div>
                    <div class="col-md-2 form-group mb-0">
                        <input type="date" name="date_from" class="form-control form-control-sm" value="{{ request('date_from') }}" placeholder="From">
                    </div>
                    <div class="col-md-2 form-group mb-0">
                        <input type="date" name="date_to" class="form-control form-control-sm" value="{{ request('date_to') }}" placeholder="To">
                    </div>
                    <div class="col-md-2 form-group mb-0" style="display:flex;gap:6px">
                        <button type="submit" class="btn btn-primary btn-sm ml-auto"><i class="fas fa-search"></i></button>
                        <a href="{{ route('hr.reports.leave') }}" class="btn btn-secondary btn-sm"><i class="fas fa-times"></i></a>
                    </div>
                </div>
            </form>

            @if($requests->isEmpty())
                <div class="text-center text-muted py-4"><i class="fas fa-inbox fa-2x mb-2 d-block"></i>No records found.</div>
            @else
            @php
                $approvedDays = $requests->where('status','approved')->sum('total_days');
                $pendingCount = $requests->where('status','pending')->count();
            @endphp
            <div class="mb-2">
                <span class="badge badge-success px-3 py-2">Approved Days: {{ $approvedDays }}</span>
                <span class="badge badge-warning px-3 py-2 ml-1">Pending: {{ $pendingCount }}</span>
                <span class="badge badge-secondary px-3 py-2 ml-1">Total: {{ $requests->count() }}</span>
            </div>
            <div class="table-responsive">
                <table class="table table-sm table-bordered">
                    <thead class="thead-dark"><tr><th>#</th><th>Employee</th><th>Designation</th><th>Type</th><th>From</th><th>To</th><th>Days</th><th>Status</th><th>Approved By</th></tr></thead>
                    <tbody>
                        @foreach($requests as $i => $r)
                        <tr>
                            <td>{{ $i+1 }}</td>
                            <td>{{ $r->employee->name }}</td>
                            <td>{{ $r->employee->designation->name ?? '—' }}</td>
                            <td><span class="badge badge-secondary">{{ ucfirst($r->leave_type) }}</span></td>
                            <td>{{ $r->date_from->format('d M Y') }}</td>
                            <td>{{ $r->date_to->format('d M Y') }}</td>
                            <td>{{ $r->total_days }}</td>
                            <td><span class="badge badge-{{ $r->status === 'approved' ? 'success' : ($r->status === 'rejected' ? 'danger' : 'warning') }}">{{ ucfirst($r->status) }}</span></td>
                            <td>{{ $r->approver?->name ?? '—' }}</td>
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
