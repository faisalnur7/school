@extends('layouts.master')

@section('contents')
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header text-white rounded-top d-flex justify-content-between align-items-center shadow p-3">
                        <h3 class="card-title">Record Income</h3>
                    </div>

                    <form method="POST" action="{{ route('incomes.store') }}" enctype="multipart/form-data">
                        @csrf

                        <div class="card-body">

                            <div class="form-group">
                                <label>Category</label>
                                <select name="income_category_id" class="form-control" required>
                                    <option value="">Select Category</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}" {{ old('income_category_id') == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group">
                                <label>Title</label>
                                <input type="text" name="title" class="form-control @error('title') is-invalid @enderror"
                                       value="{{ old('title') }}" required>
                                @error('title') <span class="invalid-feedback">{{ $message }}</span> @enderror
                            </div>

                            <div class="form-group">
                                <label>Amount (BDT)</label>
                                <input type="number" name="amount" step="0.01" min="0"
                                       class="form-control @error('amount') is-invalid @enderror"
                                       value="{{ old('amount') }}" required>
                                @error('amount') <span class="invalid-feedback">{{ $message }}</span> @enderror
                            </div>

                            <div class="form-group">
                                <label>Income Date</label>
                                <input type="text" name="income_date" datepicker datepicker-format="dd/mm/yyyy"
                                       class="form-control @error('income_date') is-invalid @enderror"
                                       value="{{ old('income_date') }}" placeholder="dd/mm/yyyy" autocomplete="off" required>
                                @error('income_date') <span class="invalid-feedback">{{ $message }}</span> @enderror
                            </div>

                            <div class="form-group">
                                <label>Payment Method</label>
                                <select id="paymentMethod" name="payment_method" class="form-control" required>
                                    @foreach (['Cash', 'Bank Transfer', 'Cheque', 'Mobile Banking', 'Other'] as $method)
                                        <option value="{{ $method }}" {{ old('payment_method') == $method ? 'selected' : '' }}>
                                            {{ $method }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <input type="hidden" name="account_type" id="incomeAccountType" value="{{ old('account_type') }}">

                            <div class="form-group" id="incomeAccountWrapper" style="display: none;">
                                <label>Account <small class="text-muted">(optional for Cash/Bank/Mobile)</small></label>
                                <select name="account_id" id="incomeAccountSelect" class="form-control">
                                    <option value="">Select Account</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label>Reference No <small class="text-muted">(optional)</small></label>
                                <input type="text" name="reference_no" class="form-control"
                                       value="{{ old('reference_no') }}">
                            </div>

                            <div class="form-group">
                                <label>Description <small class="text-muted">(optional)</small></label>
                                <textarea name="description" class="form-control" rows="3">{{ old('description') }}</textarea>
                            </div>

                            <div class="form-group">
                                <label>Attachment <small class="text-muted">(jpg, png, pdf — max 2MB)</small></label>
                                <input type="file" name="attachment" class="form-control @error('attachment') is-invalid @enderror"
                                       accept=".jpg,.jpeg,.png,.pdf">
                                @error('attachment') <span class="invalid-feedback">{{ $message }}</span> @enderror
                            </div>

                        </div>

                        <div class="card-footer">
                            <button class="btn btn-success">Save</button>
                            <a href="{{ route('incomes.index') }}" class="btn btn-secondary">Back</a>
                        </div>
                    </form>
                </div>
            </div>

            <div class="col-md-8">
                @include('pages.incomes.table')
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    const incomeAccountsUrl = '{{ route('accounts.index') }}';

    const incomeMethodTypeMap = {
        'Cash': 'hand_cash',
        'Bank Transfer': 'bank',
        'Mobile Banking': 'mobile',
    };

    const incomeAccountTypeMap = {
        'Cash': '\\App\\Models\\HandCash',
        'Bank Transfer': '\\App\\Models\\BankAccount',
        'Mobile Banking': '\\App\\Models\\MobileBankingAccount',
    };

    function incomeLoadAccounts(method, selectedId = null) {
        const type = incomeMethodTypeMap[method];
        const accountType = incomeAccountTypeMap[method];
        const wrapper = $('#incomeAccountWrapper');
        const select  = $('#incomeAccountSelect');

        if (!type) {
            wrapper.hide();
            $('#incomeAccountType').val('');
            select.html('<option value="">Select Account</option>');
            return;
        }

        $('#incomeAccountType').val(accountType);

        $.ajax({
            url: incomeAccountsUrl,
            method: 'GET',
            dataType: 'json',
            data: { type: type },
            success: function (accounts) {
                select.html('<option value="">Select Account</option>');
                accounts.forEach(a => {
                    const selected = selectedId && String(a.id) === String(selectedId) ? 'selected' : '';
                    select.append(`<option value="${a.id}" ${selected}>${a.label}</option>`);
                });
                if (accounts.length > 0) {
                    wrapper.show();
                } else {
                    wrapper.hide();
                }
            },
            error: function () {
                wrapper.hide();
            }
        });
    }

    $(document).ready(function () {
        const initialMethod = $('#paymentMethod').val();
        const initialAccount = '{{ old('account_id', '') }}';
        const initialType = '{{ old('account_type', '') }}';

        if (initialType && initialAccount) {
            $('#incomeAccountType').val(initialType);
            if (incomeMethodTypeMap[initialMethod]) {
                incomeLoadAccounts(initialMethod, initialAccount);
            }
        } else if (incomeMethodTypeMap[initialMethod]) {
            incomeLoadAccounts(initialMethod, initialAccount);
        }

        $('#paymentMethod').on('change', function () {
            incomeLoadAccounts($(this).val());
        });
    });
</script>
@endsection