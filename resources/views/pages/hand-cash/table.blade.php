<div class="card">
    <div class="card-header text-white rounded-top d-flex flex-column flex-sm-row justify-content-between align-items-stretch align-items-sm-center shadow p-3">
        <h3 class="card-title mb-0 text-white text-lg">Hand Cash</h3>
        <div class="d-flex flex-column flex-sm-row gap-2 ml-sm-auto w-100 w-sm-auto">
            @if($pettyCash)
            <button type="button" class="btn btn-warning btn-sm w-100 w-sm-auto" data-toggle="modal" data-target="#transferModal">
                <i class="fas fa-exchange-alt"></i> Transfer to Bank
            </button>
            @endif
            <a href="{{ route('hand-cash.create') }}" class="btn btn-primary btn-sm text-bold w-100 w-sm-auto">
                + Add Hand Cash
            </a>
        </div>
    </div>

    @if($pettyCash)
    <div class="px-3 pt-3">
        <div class="alert alert-info mb-0 d-flex justify-content-between align-items-center">
            <span><strong>Petty Cash Balance ({{ $pettyCash->label }}):</strong></span>
            <span class="font-weight-bold" style="font-size:1.1rem">{{ number_format($pettyCash->balance, 2) }}</span>
        </div>
    </div>
    @endif

    <div class="card-body px-0 pb-4 pt-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Label</th>
                        <th>Opening Amount</th>
                        <th>Opening Date</th>
                        <th>Recorded By</th>
                        <th>Status</th>
                        <th>Notes</th>
                        <th width="120">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($handCashes as $cash)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td class="fw-semibold">{{ $cash->label }}</td>
                            <td class="mono fw-bold" style="color:#4338ca">{{ number_format($cash->opening_amount, 2) }}</td>
                            <td class="mono" style="font-size:13px">{{ $cash->opening_date->format('d/m/Y') }}</td>
                            <td class="text-muted" style="font-size:13px">{{ $cash->recorder->name ?? '—' }}</td>
                            <td>
                                <span class="badge badge-{{ $cash->is_active ? 'success' : 'secondary' }}">
                                    {{ $cash->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td class="text-muted" style="font-size:13px">{{ Str::limit($cash->notes, 40) ?? '—' }}</td>
                            <td class="d-flex flex-column flex-sm-row justify-content-center align-items-stretch align-items-sm-center gap-1">
                                <a href="{{ route('hand-cash.edit', $cash->id) }}" class="btn btn-sm btn-dark">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('hand-cash.destroy', $cash->id) }}" method="POST"
                                      class="btn btn-sm btn-danger d-inline m-0"
                                      onsubmit="return confirm('Delete this hand cash entry?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" style="background:none;border:none;padding:0;color:inherit">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach

                    @if ($handCashes->isEmpty())
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">No hand cash entries found</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
        <div class="px-3 pt-3">{{ $handCashes->links() }}</div>
    </div>
</div>

@if($pettyCash && $bankAccounts->isNotEmpty())
<div class="modal fade" id="transferModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Transfer from Petty Cash to Bank</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <form action="{{ route('hand-cash.transfer-to-bank') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label>Bank Account</label>
                        <select name="bank_account_id" class="form-control" required>
                            <option value="">— Select Bank —</option>
                            @foreach($bankAccounts as $bank)
                            <option value="{{ $bank->id }}">{{ $bank->bank_name }} — {{ $bank->account_number }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Amount <small class="text-muted">(Available: {{ number_format($pettyCash->balance, 2) }})</small></label>
                        <input type="number" name="amount" class="form-control" step="0.01" min="0.01" max="{{ $pettyCash->balance }}" required>
                    </div>
                    <div class="form-group">
                        <label>Description <small class="text-muted">(optional)</small></label>
                        <input type="text" name="description" class="form-control" maxlength="255">
                    </div>
                </div>
                <div class="modal-footer flex-column flex-sm-row">
                    <button type="button" class="btn btn-secondary w-100 w-sm-auto" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning w-100 w-sm-auto">Transfer</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
