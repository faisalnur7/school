@extends('layouts.master')

@section('contents')
<div class="container-fluid px-3 py-3">
    <div class="card shadow-sm border-0">
        <div class="card-header bg-gradient-primary text-white py-3">
            <div class="d-flex justify-content-between align-items-center">
                <h4 class="card-title mb-0 font-weight-bold text-white">
                    <i class="fas fa-edit mr-2"></i>Edit Capital Transaction
                </h4>
                <a href="{{ route('shareholder-transactions.index') }}" class="btn btn-light btn-sm">
                    <i class="fas fa-arrow-left mr-1"></i> Back
                </a>
            </div>
        </div>

        <form method="POST" action="{{ route('shareholder-transactions.update', $transaction->id) }}" id="modernForm">
            @csrf

            <div class="card-body p-3">
                @if($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show border-0 mb-3" role="alert">
                        <i class="fas fa-exclamation-circle mr-2"></i>
                        <strong>Errors:</strong>
                        <ul class="mb-0 mt-1 ml-4">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

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

            <div class="card-footer bg-light border-top py-2 px-3">
                <div class="d-flex justify-content-between gap-2">
                    <a href="{{ route('shareholder-transactions.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-times mr-1"></i>Cancel
                    </a>
                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="fas fa-save mr-1"></i>Update
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@section('styles')
@include('components.form-styles')
@endsection

@section('scripts')
<script>
    $(function () {
        if ($('.is-invalid').length > 0) {
            $('html, body').animate({
                scrollTop: $('.is-invalid').first().offset().top - 50
            }, 300);
        }
    });

    const accountsUrl = '{{ route('accounts.index') }}';

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

    function loadTransactionAccounts(method) {
        const type        = methodTypeMap[method];
        const accountType = accountTypeMap[method];
        const $wrapper    = $('#transactionAccountWrapper');
        const $select     = $('#transactionAccountSelect');

        if (!type) {
            $wrapper.hide();
            $('#transactionAccountType').val('');
            $select.html('<option value="">Select Account</option>');
            return;
        }

        $('#transactionAccountType').val(accountType);

        $.ajax({
            url: accountsUrl, method: 'GET', dataType: 'json', data: { type: type },
            success: function (accounts) {
                $select.html('<option value="">Select Account</option>');
                accounts.forEach(a => $select.append(`<option value="${a.id}">${a.label}</option>`));
                $wrapper.toggle(accounts.length > 0);
            },
            error: function () { $wrapper.hide(); }
        });
    }

    $('#paymentMethod').on('change', function () {
        loadTransactionAccounts($(this).val());
    });

    $(function () {
        loadTransactionAccounts($('#paymentMethod').val());
    });
</script>
@endsection