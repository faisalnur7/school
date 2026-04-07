@extends('layouts.master')
@section('contents')
<div class="container-fluid">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="card-title mb-0 text-white text-lg">Leave Requests</h3>
            <a href="{{ route('hr.leave.create') }}" class="btn btn-primary btn-sm ml-auto"><i class="fas fa-plus"></i> New Request</a>
        </div>
        <div class="card-body">
            @include('hr._alerts')
            <form method="GET" class="mb-3">
                <div class="row">
                    <div class="col-md-3 form-group mb-0">
                        <select name="employee_id" class="form-control form-control-sm">
                            <option value="">All Employees</option>
                            @foreach($employees as $e)
                                <option value="{{ $e->id }}" {{ request('employee_id') == $e->id ? 'selected' : '' }}>{{ $e->name }}</option>
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
                    <div class="col-md-2 form-group mb-0" style="gap:6px;display:flex">
                        <button type="submit" class="btn btn-primary btn-sm ml-auto"><i class="fas fa-search"></i></button>
                        <a href="{{ route('hr.leave.index') }}" class="btn btn-secondary btn-sm"><i class="fas fa-times"></i></a>
                    </div>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-sm table-bordered">
                    <thead class="thead-dark"><tr><th>#</th><th>Employee</th><th>Type</th><th>From</th><th>To</th><th>Days</th><th>Status</th><th>Approver</th><th class="text-center">Actions</th></tr></thead>
                    <tbody>
                        @forelse($requests as $i => $r)
                        <tr>
                            <td>{{ $requests->firstItem() + $i }}</td>
                            <td>{{ $r->employee->name }}<br><small class="text-muted">{{ $r->employee->designation->name ?? '' }}</small></td>
                            <td><span class="badge badge-secondary">{{ ucfirst($r->leave_type) }}</span></td>
                            <td>{{ $r->date_from->format('d M Y') }}</td>
                            <td>{{ $r->date_to->format('d M Y') }}</td>
                            <td>{{ $r->total_days }}</td>
                            <td>
                                <span class="badge badge-{{ $r->status === 'approved' ? 'success' : ($r->status === 'rejected' ? 'danger' : 'warning') }}">
                                    {{ ucfirst($r->status) }}
                                </span>
                            </td>
                            <td>{{ $r->approver?->name ?? '—' }}</td>
                            <td class="text-center">
                                @if($r->status === 'pending')
                                <button class="btn btn-xs btn-success" data-toggle="modal" data-target="#approveModal{{ $r->id }}"><i class="fas fa-check"></i></button>
                                <button class="btn btn-xs btn-danger"  data-toggle="modal" data-target="#rejectModal{{ $r->id }}"><i class="fas fa-times"></i></button>

                                {{-- Approve Modal --}}
                                <div class="modal fade" id="approveModal{{ $r->id }}" tabindex="-1">
                                    <div class="modal-dialog"><div class="modal-content">
                                        <div class="modal-header"><h5 class="modal-title">Approve Leave</h5><button type="button" class="close" data-dismiss="modal">&times;</button></div>
                                        <form action="{{ route('hr.leave.approve', $r->id) }}" method="POST">
                                            @csrf @method('PATCH')
                                            <div class="modal-body">
                                                <div class="form-group">
                                                    <label>Approver</label>
                                                    <select name="approver_id" class="form-control" required>
                                                        @foreach($employees as $e)
                                                            <option value="{{ $e->id }}">{{ $e->name }} ({{ $e->designation->name ?? '' }})</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="modal-footer"><button type="submit" class="btn btn-success btn-sm">Approve</button></div>
                                        </form>
                                    </div></div>
                                </div>

                                {{-- Reject Modal --}}
                                <div class="modal fade" id="rejectModal{{ $r->id }}" tabindex="-1">
                                    <div class="modal-dialog"><div class="modal-content">
                                        <div class="modal-header"><h5 class="modal-title">Reject Leave</h5><button type="button" class="close" data-dismiss="modal">&times;</button></div>
                                        <form action="{{ route('hr.leave.reject', $r->id) }}" method="POST">
                                            @csrf @method('PATCH')
                                            <div class="modal-body">
                                                <div class="form-group">
                                                    <label>Approver</label>
                                                    <select name="approver_id" class="form-control" required>
                                                        @foreach($employees as $e)
                                                            <option value="{{ $e->id }}">{{ $e->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="form-group">
                                                    <label>Rejection Reason</label>
                                                    <textarea name="rejection_reason" class="form-control" rows="2" required></textarea>
                                                </div>
                                            </div>
                                            <div class="modal-footer"><button type="submit" class="btn btn-danger btn-sm">Reject</button></div>
                                        </form>
                                    </div></div>
                                </div>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="9" class="text-center text-muted py-4">No leave requests found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{ $requests->links() }}
        </div>
    </div>
</div>
@endsection
