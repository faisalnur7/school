@extends('layouts.master')

@section('contents')
<div class="col-12 col-md-7">
    <div class="card card-outline card-success">
        <div class="card-header"><h3 class="card-title">Apply for Leave</h3></div>
        <div class="card-body">
            @include('hr._alerts')
            <form action="{{ route('account.leave.store') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label>Leave Type</label>
                    <select name="leave_type" class="form-control" required>
                        @foreach(['casual','sick','annual','maternity','other'] as $t)
                            <option value="{{ $t }}" {{ old('leave_type') === $t ? 'selected' : '' }}>{{ ucfirst($t) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="row">
                    <div class="col-md-6 form-group">
                        <label>From Date</label>
                        <input type="date" name="date_from" class="form-control" value="{{ old('date_from') }}" required>
                    </div>
                    <div class="col-md-6 form-group">
                        <label>To Date</label>
                        <input type="date" name="date_to" class="form-control" value="{{ old('date_to') }}" required>
                    </div>
                </div>
                <div class="form-group">
                    <label>Reason</label>
                    <textarea name="reason" class="form-control" rows="3" required>{{ old('reason') }}</textarea>
                </div>
                <button class="btn btn-success">Submit Leave Request</button>
            </form>
        </div>
    </div>
</div>

<div class="col-12 col-md-5">
    <div class="card card-outline card-info">
        <div class="card-header"><h3 class="card-title">My Leave Balance</h3></div>
        <div class="card-body p-0">
            <table class="table table-sm mb-0">
                <thead><tr><th>Type</th><th>Remaining</th></tr></thead>
                <tbody>
                    @forelse($leaveBalances as $balance)
                        <tr><td>{{ ucfirst($balance->leave_type) }}</td><td>{{ $balance->remaining_leave }}</td></tr>
                    @empty
                        <tr><td colspan="2" class="text-muted text-center">No leave balance set.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="card card-outline card-secondary">
        <div class="card-header"><h3 class="card-title">Recent Requests</h3></div>
        <div class="card-body p-0">
            <table class="table table-sm mb-0">
                <thead><tr><th>Type</th><th>Days</th><th>Status</th></tr></thead>
                <tbody>
                    @forelse($leaveRequests as $leave)
                        <tr>
                            <td>{{ ucfirst($leave->leave_type) }}</td>
                            <td>{{ $leave->total_days }}</td>
                            <td><span class="badge badge-{{ $leave->status === 'approved' ? 'success' : ($leave->status === 'rejected' ? 'danger' : 'warning') }}">{{ ucfirst($leave->status) }}</span></td>
                        </tr>
                    @empty
                        <tr><td colspan="3" class="text-muted text-center">No leave request found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
