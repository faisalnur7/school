@extends('layouts.master')

@section('contents')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header text-white rounded-top d-flex justify-content-between align-items-center shadow p-3">
                    <h3 class="card-title">Edit Capital Transaction</h3>
                </div>

                <form method="POST" action="{{ route('shareholder-transactions.update', $transaction->id) }}">
                    @csrf @method('PUT')
                    <div class="card-body">

                        <div class="form-group">
                            <label>Shareholder <span class="text-danger">*</span></label>
                            <select name="shareholder_id" class="form-control @error('shareholder_id') is-invalid @enderror" required>
                                <option value="">Select Shareholder</option>
                                @foreach ($shareholders as $sh)
                                    <option value="{{ $sh->id }}" {{ old('shareholder_id', $transaction->shareholder_id) == $sh->id ? 'selected' : '' }}>
                                        {{ $sh->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('shareholder_id') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>

                        <div class="form-group">
                            <label>Type <span class="text-danger">*</span></label>
                            <select name="type" class="form-control @error('type') is-invalid @enderror" required>
                                <option value="capital" {{ old('type', $transaction->type) === 'capital' ? 'selected' : '' }}>Capital (Investment)</option>
                                <option value="withdrawal" {{ old('type', $transaction->type) === 'withdrawal' ? 'selected' : '' }}>Withdrawal (Drawing)</option>
                            </select>
                            @error('type') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>

                        <div class="form-group">
                            <label>Amount <span class="text-danger">*</span></label>
                            <input type="number" name="amount" step="0.01" min="0.01"
                                   class="form-control @error('amount') is-invalid @enderror"
                                   value="{{ old('amount', $transaction->amount) }}" required>
                            @error('amount') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>

                        <div class="form-group">
                            <label>Transaction Date <span class="text-danger">*</span></label>
                            <input type="text" name="transaction_date" datepicker datepicker-format="dd/mm/yyyy"
                                   class="form-control @error('transaction_date') is-invalid @enderror"
                                   value="{{ old('transaction_date', $transaction->transaction_date->format('d/m/Y')) }}"
                                   placeholder="dd/mm/yyyy" autocomplete="off" required>
                            @error('transaction_date') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>

                        <div class="form-group">
                            <label>Payment Method <span class="text-danger">*</span></label>
                            <select name="payment_method" id="paymentMethod" class="form-control" required>
                                @foreach (['Cash', 'Bank Transfer', 'Cheque', 'Mobile Banking', 'Other'] as $method)
                                    <option value="{{ $method }}" {{ old('payment_method', $transaction->payment_method) === $method ? 'selected' : '' }}>
                                        {{ $method }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <input type="hidden" name="account_type" id="transactionAccountType" value="{{ old('account_type', $transaction->account_type ?? '') }}">

                        <div class="form-group" id="transactionAccountWrapper" style="display:none">
                            <label>Account</label>
                            <select name="account_id" id="transactionAccountSelect" class="form-control">
                                <option value="">Select Account</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Description <small class="text-muted">(optional — defaults to type name if blank)</small></label>
                            <textarea name="description" class="form-control" rows="2" placeholder="e.g. Initial investment, Monthly drawing...">{{ old('description', $transaction->description) }}</textarea>
                        </div>

                        <div class="form-group">
                            <label class="text-muted" style="font-size:12px">Reference No</label>
                            <input type="text" class="form-control form-control-sm" value="{{ $transaction->reference_no }}" disabled>
                        </div>

                    </div>
                    <div class="card-footer">
                        <button class="btn btn-success">Update</button>
                        <a href="{{ route('shareholder-transactions.index') }}" class="btn btn-secondary">Back</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@section('scripts')
<script>
    const accountsUrl  = '{{ route('accounts.index') }}';
    const savedMethod  = '{{ old('payment_method', $transaction->payment_method) }}';
    const savedAccount = '{{ old('account_id', $transaction->account_id) }}';

    const methodTypeMap = {
        'Cash':           'hand_cash',
        'Bank Transfer':  'bank',
        'Mobile Banking': 'mobile',
    };

    const accountTypeMap = {
        'Cash':           'App\\Models\\HandCash',
        'Bank Transfer':  'App\\Models\\BankAccount',
        'Mobile Banking': 'App\\Models\\MobileBankingAccount',
    };

    function loadAccounts(method, preselectId) {
        const type = methodTypeMap[method];
        const accountType = accountTypeMap[method];
        const $wrapper = $('#transactionAccountWrapper');
        const $select  = $('#transactionAccountSelect');

        if (!type) {
            $wrapper.hide();
            $('#transactionAccountType').val('');
            $select.html('<option value="">Select Account</option>');
            return;
        }

        $('#transactionAccountType').val(accountType);

        $.ajax({
            url: accountsUrl,
            method: 'GET',
            dataType: 'json',
            data: { type: type },
            success: function (accounts) {
                $select.html('<option value="">Select Account</option>');
                accounts.forEach(a => {
                    const selected = preselectId && String(a.id) === String(preselectId) ? 'selected' : '';
                    $select.append(`<option value="${a.id}" ${selected}>${a.label}</option>`);
                });
                if (accounts.length > 0) {
                    $wrapper.show();
                } else {
                    $wrapper.hide();
                }
            },
            error: function () {
                $wrapper.hide();
            }
        });
    }

    // Pre-load on page open if a method with accounts is already selected
    if (methodTypeMap[savedMethod]) {
        loadAccounts(savedMethod, savedAccount);
    }

    $('#paymentMethod').on('change', function () {
        loadAccounts($(this).val(), null);
    });
</script>
@endsection
@endsection
