@extends('layouts.master')
@section('contents')
<div class="container-fluid">
    @include('hr._alerts')

    {{-- Profile Header --}}
    <div class="card mb-3">
        <div class="card-body">
            <div class="d-flex align-items-center gap-3" style="gap:16px">
                <img src="{{ $employee->photo_url }}" class="img-circle elevation-2" style="width:80px;height:80px;object-fit:cover">
                <div class="flex-grow-1">
                    <h4 class="mb-0">{{ $employee->name }}</h4>
                    <p class="text-muted mb-1">{{ $employee->designation->name ?? '—' }} &nbsp;|&nbsp; <code>{{ $employee->employee_id }}</code></p>
                    <span class="badge badge-{{ $employee->employee_type === 'teacher' ? 'primary' : 'info' }}">{{ ucfirst($employee->employee_type) }}</span>
                    <span class="badge badge-{{ $employee->status === 'active' ? 'success' : 'secondary' }} ml-1">{{ ucfirst($employee->status) }}</span>
                </div>
                <div>
                    <a href="{{ route('hr.employees.edit', $employee) }}" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i> Edit</a>
                    <a href="{{ route('hr.salary-structures.create', ['employee_id' => $employee->id]) }}" class="btn btn-success btn-sm ml-1"><i class="fas fa-money-bill"></i> Salary</a>
                </div>
            </div>
        </div>
    </div>

    {{-- Tabs --}}
    <ul class="nav nav-tabs mb-3" id="empTabs">
        <li class="nav-item"><a class="nav-link active" data-bs-toggle="tab" href="#tabInfo">Profile</a></li>
        <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tabSalary">Salary History</a></li>
        <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tabLeave">Leave Balances</a></li>
        <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tabDocs">Documents</a></li>
        <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tabPayment">Payment Info</a></li>
    </ul>

    <div class="tab-content">
        {{-- Profile Tab --}}
        <div class="tab-pane fade show active" id="tabInfo">
            <div class="card"><div class="card-body">
                <div class="row">
                    @foreach([
                        'Department' => is_string($employee->department)
                            ? $employee->department
                            : $employee->department?->name,
                        'Gender'     => ucfirst($employee->gender),
                        'DOB'        => $employee->dob?->format('d M Y'),
                        'Phone'      => $employee->phone,
                        'Joining'    => $employee->joining_date?->format('d M Y'),
                        'Manager'    => $employee->manager?->name,
                        'Address'    => $employee->address,
                        'Email'      => $employee->user?->email,
                    ] as $label => $value)
                    <div class="col-md-4 mb-2">
                        <small class="text-muted text-uppercase font-weight-bold">{{ $label }}</small>
                        <div>{{ $value ?? '—' }}</div>
                    </div>
                    @endforeach
                </div>
            </div></div>
        </div>

        {{-- Salary Tab --}}
        <div class="tab-pane fade" id="tabSalary">
            <div class="card"><div class="card-body">
                <a href="{{ route('hr.salary-structures.create', ['employee_id' => $employee->id]) }}" class="btn btn-sm btn-primary mb-3"><i class="fas fa-plus"></i> Add Salary Structure</a>
                <table class="table table-sm table-bordered">
                    <thead class="thead-light"><tr><th>Effective From</th><th>Basic</th><th>Gross</th><th>Deductions</th><th>Net</th><th>Actions</th></tr></thead>
                    <tbody>
                        @forelse($employee->salaryStructures->sortByDesc('effective_from') as $s)
                        <tr>
                            <td>{{ $s->effective_from->format('d M Y') }}</td>
                            <td>৳{{ number_format($s->basic_salary, 2) }}</td>
                            <td class="font-weight-bold">৳{{ number_format($s->gross_salary, 2) }}</td>
                            <td class="text-danger">৳{{ number_format($s->other_deductions, 2) }}</td>
                            <td class="font-weight-bold text-success">৳{{ number_format($s->net_salary, 2) }}</td>
                            <td><a href="{{ route('hr.salary-structures.edit', $s) }}" class="btn btn-xs btn-warning"><i class="fas fa-edit"></i></a></td>
                        </tr>
                        @empty
                        <tr><td colspan="6" class="text-center text-muted">No salary structure assigned.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div></div>
        </div>

        {{-- Leave Tab --}}
        <div class="tab-pane fade" id="tabLeave">
            <div class="card"><div class="card-body">
                <table class="table table-sm table-bordered">
                    <thead class="thead-light"><tr><th>Leave Type</th><th>Total</th><th>Used</th><th>Remaining</th></tr></thead>
                    <tbody>
                        @forelse($employee->leaveBalances as $b)
                        <tr>
                            <td>{{ ucfirst($b->leave_type) }}</td>
                            <td>{{ $b->total_leave }}</td>
                            <td>{{ $b->used_leave }}</td>
                            <td><span class="badge badge-{{ $b->remaining_leave > 0 ? 'success' : 'danger' }}">{{ $b->remaining_leave }}</span></td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="text-center text-muted">No leave balances set.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div></div>
        </div>

        {{-- Documents Tab --}}
        <div class="tab-pane fade" id="tabDocs">
            <div class="card"><div class="card-body">
                <form action="{{ route('hr.employees.documents.store', $employee->id) }}" method="POST" enctype="multipart/form-data" class="mb-3">
                    @csrf
                    <div class="row">
                        <div class="col-md-3">
                            <select name="document_type" class="form-control form-control-sm" required>
                                @foreach(['nid','passport','certificate','contract','photo_id','other'] as $t)
                                    <option value="{{ $t }}">{{ ucfirst(str_replace('_',' ',$t)) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-5">
                            <input type="file" name="file" class="form-control-file" required>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary btn-sm ml-auto"><i class="fas fa-upload"></i> Upload</button>
                        </div>
                    </div>
                </form>
                <table class="table table-sm table-bordered">
                    <thead class="thead-light"><tr><th>Type</th><th>File</th><th>Size</th><th>Uploaded</th><th>Actions</th></tr></thead>
                    <tbody>
                        @forelse($employee->documents as $doc)
                        <tr>
                            <td><span class="badge badge-secondary">{{ ucfirst(str_replace('_',' ',$doc->document_type)) }}</span></td>
                            <td>{{ $doc->original_name }}</td>
                            <td>{{ $doc->file_size }}</td>
                            <td>{{ $doc->uploaded_at?->format('d M Y') }}</td>
                            <td>
                                <a href="{{ $doc->file_url }}" target="_blank" class="btn btn-xs btn-info"><i class="fas fa-download"></i></a>
                                <form action="{{ route('hr.documents.destroy', $doc) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-xs btn-danger"><i class="fas fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="text-center text-muted">No documents uploaded.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div></div>
        </div>

        {{-- Payment Info Tab --}}
        <div class="tab-pane fade" id="tabPayment">
            <div class="card"><div class="card-body">
                @php $pi = $employee->paymentInformation; @endphp
                <form action="{{ route('hr.employees.payment.store', $employee->id) }}" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-md-3 form-group">
                            <label class="font-weight-bold">Payment Method</label>
                            <select name="payment_method" id="payMethod" class="form-control" onchange="togglePayFields()">
                                <option value="cash"          {{ ($pi?->payment_method ?? 'cash') === 'cash'          ? 'selected' : '' }}>Cash</option>
                                <option value="bank"          {{ ($pi?->payment_method) === 'bank'          ? 'selected' : '' }}>Bank</option>
                                <option value="mobile_wallet" {{ ($pi?->payment_method) === 'mobile_wallet' ? 'selected' : '' }}>Mobile Wallet</option>
                            </select>
                        </div>
                        <div class="col-md-3 form-group" id="bankFields" style="display:none">
                            <label>Bank Name</label>
                            <input type="text" name="bank_name" class="form-control" value="{{ $pi?->bank_name }}">
                        </div>
                        <div class="col-md-3 form-group" id="accountField" style="display:none">
                            <label>Account Number</label>
                            <input type="text" name="account_number" class="form-control" value="{{ $pi?->account_number }}">
                        </div>
                        <div class="col-md-3 form-group" id="walletField" style="display:none">
                            <label>Wallet Number</label>
                            <input type="text" name="mobile_wallet_number" class="form-control" value="{{ $pi?->mobile_wallet_number }}">
                        </div>
                        <div class="col-md-3 form-group" id="walletProvider" style="display:none">
                            <label>Provider</label>
                            <input type="text" name="mobile_wallet_provider" class="form-control" value="{{ $pi?->mobile_wallet_provider }}" placeholder="bKash, Nagad...">
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary btn-sm ml-auto"><i class="fas fa-save"></i> Save Payment Info</button>
                </form>
            </div></div>
        </div>
    </div>
</div>

<script>
function togglePayFields() {
    const m = document.getElementById('payMethod').value;
    document.getElementById('bankFields').style.display    = m === 'bank' ? '' : 'none';
    document.getElementById('accountField').style.display  = m === 'bank' ? '' : 'none';
    document.getElementById('walletField').style.display   = m === 'mobile_wallet' ? '' : 'none';
    document.getElementById('walletProvider').style.display= m === 'mobile_wallet' ? '' : 'none';
}
togglePayFields();
</script>
@endsection
