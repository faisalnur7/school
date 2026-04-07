@extends('layouts.master')
@section('contents')
<div class="container-fluid">
    <div class="card">
        <div class="card-header"><h3 class="card-title mb-0 text-white text-lg">Leave Balances</h3></div>
        <div class="card-body">
            @include('hr._alerts')
            <div class="table-responsive">
                <table class="table table-sm table-bordered">
                    <thead class="thead-dark">
                        <tr><th>Employee</th><th>Casual</th><th>Sick</th><th>Annual</th><th>Maternity</th><th>Other</th><th>Set Balance</th></tr>
                    </thead>
                    <tbody>
                        @foreach($employees as $emp)
                        @php $bal = $emp->leaveBalances->keyBy('leave_type'); @endphp
                        <tr>
                            <td><strong>{{ $emp->name }}</strong><br><small class="text-muted">{{ $emp->employee_id }}</small></td>
                            @foreach(['casual','sick','annual','maternity','other'] as $t)
                            <td class="text-center">
                                @if(isset($bal[$t]))
                                    <span class="badge badge-{{ $bal[$t]->remaining_leave > 0 ? 'success' : 'danger' }}">{{ $bal[$t]->remaining_leave }}</span>
                                    <small class="text-muted d-block">/ {{ $bal[$t]->total_leave }}</small>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            @endforeach
                            <td>
                                <button class="btn btn-xs btn-outline-primary" data-toggle="modal" data-target="#setModal{{ $emp->id }}"><i class="fas fa-edit"></i></button>
                                <div class="modal fade" id="setModal{{ $emp->id }}" tabindex="-1">
                                    <div class="modal-dialog"><div class="modal-content">
                                        <div class="modal-header"><h5 class="modal-title">Set Leave Balance — {{ $emp->name }}</h5><button type="button" class="close" data-dismiss="modal">&times;</button></div>
                                        <form action="{{ route('hr.leave.balances.set') }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="employee_id" value="{{ $emp->id }}">
                                            <div class="modal-body">
                                                <div class="form-group">
                                                    <label>Leave Type</label>
                                                    <select name="leave_type" class="form-control" required>
                                                        @foreach(['casual','sick','annual','maternity','other'] as $t)
                                                            <option value="{{ $t }}">{{ ucfirst($t) }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="form-group">
                                                    <label>Total Days</label>
                                                    <input type="number" name="total_leave" class="form-control" min="0" required>
                                                </div>
                                            </div>
                                            <div class="modal-footer"><button type="submit" class="btn btn-primary btn-sm ml-auto">Save</button></div>
                                        </form>
                                    </div></div>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
