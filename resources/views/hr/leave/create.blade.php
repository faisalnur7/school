@extends('layouts.master')
@section('contents')
<div class="container-fluid">
    <div class="card">
        <div class="card-header bg-gradient-primary text-white py-3">
            <div class="d-flex justify-content-between align-items-center">
                <h4 class="card-title mb-0 font-weight-bold text-white">
                    <i class="fas fa-calendar-plus mr-2"></i>New Leave Request
                </h4>
                <a href="{{ route('hr.leave.index') }}" class="btn btn-light btn-sm">
                    <i class="fas fa-arrow-left mr-1"></i> Back
                </a>
            </div>
        </div>
        <div class="card-body">
            @include('hr._alerts')
            <form action="{{ route('hr.leave.store') }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-md-4 form-group">
                        <label class="font-weight-bold">Employee <span class="text-danger">*</span></label>
                        <select name="employee_id" id="empSelect" class="form-control @error('employee_id') is-invalid @enderror" required onchange="showBalances()">
                            <option value="">— Select Employee —</option>
                            @foreach($employees as $e)
                                <option value="{{ $e->id }}" {{ old('employee_id') == $e->id ? 'selected' : '' }}
                                    data-balances="{{ json_encode($e->leaveBalances->pluck('remaining_leave','leave_type')) }}">
                                    {{ $e->name }} ({{ $e->employee_id }})
                                </option>
                            @endforeach
                        </select>
                        @error('employee_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-3 form-group">
                        <label class="font-weight-bold">Leave Type <span class="text-danger">*</span></label>
                        <select name="leave_type" id="leaveType" class="form-control @error('leave_type') is-invalid @enderror" required onchange="showBalances()">
                            @foreach(['casual','sick','annual','maternity','other'] as $t)
                                <option value="{{ $t }}" {{ old('leave_type') === $t ? 'selected' : '' }}>{{ ucfirst($t) }}</option>
                            @endforeach
                        </select>
                        <small id="balanceInfo" class="text-muted"></small>
                        @error('leave_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-2 form-group">
                        <label class="font-weight-bold">From <span class="text-danger">*</span></label>
                        <input type="date" name="date_from" class="form-control @error('date_from') is-invalid @enderror" value="{{ old('date_from') }}" required>
                        @error('date_from')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-2 form-group">
                        <label class="font-weight-bold">To <span class="text-danger">*</span></label>
                        <input type="date" name="date_to" class="form-control @error('date_to') is-invalid @enderror" value="{{ old('date_to') }}" required>
                        @error('date_to')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-12 form-group">
                        <label class="font-weight-bold">Reason <span class="text-danger">*</span></label>
                        <textarea name="reason" class="form-control @error('reason') is-invalid @enderror" rows="3" required>{{ old('reason') }}</textarea>
                        @error('reason')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
                <button type="submit" class="btn btn-primary"><i class="fas fa-paper-plane"></i> Submit Request</button>
            </form>
        </div>
    </div>
</div>
<script>
function showBalances() {
    const opt = document.getElementById('empSelect').selectedOptions[0];
    const type = document.getElementById('leaveType').value;
    if (!opt?.dataset.balances) return;
    const balances = JSON.parse(opt.dataset.balances);
    const rem = balances[type] ?? 0;
    document.getElementById('balanceInfo').textContent = `Remaining: ${rem} days`;
}
showBalances();
</script>
@endsection
