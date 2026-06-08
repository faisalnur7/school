@extends('layouts.master')

@section('contents')
<div class="container-fluid px-3 py-3">
    <div class="card shadow-sm border-0">
        <div class="card-header bg-gradient-primary text-white py-3">
            <div class="d-flex justify-content-between align-items-center">
                <h4 class="card-title mb-0 font-weight-bold text-white">
                    <i class="fas fa-edit mr-2"></i>Edit Expense
                </h4>
                <a href="{{ route('expenses.index') }}" class="btn btn-light btn-sm">
                    <i class="fas fa-arrow-left mr-1"></i> Back
                </a>
            </div>
        </div>

        <form method="POST" action="{{ route('expenses.update', $expense->id) }}" id="modernForm" enctype="multipart/form-data">
            @csrf
            @method('PUT')

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
                                <label>Category</label>
                                <select name="expense_category_id" class="form-control" required>
                                    <option value="">Select Category</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}"
                                            {{ old('expense_category_id', $expense->expense_category_id) == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group">
                                <label>Title</label>
                                <input type="text" name="title" class="form-control @error('title') is-invalid @enderror"
                                       value="{{ old('title', $expense->title) }}" required>
                                @error('title') <span class="invalid-feedback">{{ $message }}</span> @enderror
                            </div>

                            <div class="form-group">
                                <label>Amount (BDT)</label>
                                <input type="number" name="amount" step="0.01" min="0"
                                       class="form-control @error('amount') is-invalid @enderror"
                                       value="{{ old('amount', $expense->amount) }}" required>
                                @error('amount') <span class="invalid-feedback">{{ $message }}</span> @enderror
                            </div>

                            <div class="form-group">
                                <label>Expense Date</label>
                                <input type="text" name="expense_date" datepicker datepicker-format="dd/mm/yyyy"
                                       class="form-control @error('expense_date') is-invalid @enderror"
                                       value="{{ old('expense_date', $expense->expense_date->format('d/m/Y')) }}"
                                       placeholder="dd/mm/yyyy" autocomplete="off" required>
                                @error('expense_date') <span class="invalid-feedback">{{ $message }}</span> @enderror
                            </div>

                            <div class="form-group">
                                <label>Payment Method</label>
                                <select id="expensePaymentMethod" name="payment_method" class="form-control" required>
                                    @foreach (['Cash', 'Bank Transfer', 'Cheque', 'Mobile Banking', 'Other'] as $method)
                                        <option value="{{ $method }}"
                                            {{ old('payment_method', $expense->payment_method) == $method ? 'selected' : '' }}>
                                            {{ $method }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <input type="hidden" name="account_type" id="expenseAccountType" value="{{ old('account_type', $expense->account_type ?? '') }}">

                            <div class="form-group" id="expenseAccountWrapper" style="display: none;">
                                <label>Account <small class="text-muted">(optional for Cash/Bank/Mobile)</small></label>
                                <select name="account_id" id="expenseAccountSelect" class="form-control @error('account_id') is-invalid @enderror">
                                    <option value="">Select Account</option>
                                </select>
                                @error('account_id') <span class="invalid-feedback">{{ $message }}</span> @enderror
                            </div>

                            <div class="form-group">
                                <label>Reference No <small class="text-muted">(optional)</small></label>
                                <input type="text" name="reference_no" class="form-control"
                                       value="{{ old('reference_no', $expense->reference_no) }}">
                            </div>

                            <div class="form-group">
                                <label>Description <small class="text-muted">(optional)</small></label>
                                <textarea name="description" class="form-control" rows="3">{{ old('description', $expense->description) }}</textarea>
                            </div>

                            <div class="form-group">
                                <label>Attachment</label>
                                @if ($expense->attachment)
                                    <div class="mb-2">
                                        <a href="{{ asset('storage/' . $expense->attachment) }}" target="_blank"
                                           class="btn btn-sm btn-outline-secondary">
                                            <i class="fas fa-paperclip"></i> View Current
                                        </a>
                                    </div>
                                @endif
                                <input type="file" name="attachment" class="form-control @error('attachment') is-invalid @enderror"
                                       accept=".jpg,.jpeg,.png,.pdf">
                                <small class="text-muted">Leave empty to keep existing attachment</small>
                                @error('attachment') <span class="invalid-feedback">{{ $message }}</span> @enderror
                            </div>
            </div>

            <div class="card-footer bg-light border-top py-2 px-3">
                <div class="d-flex justify-content-between gap-2">
                    <a href="{{ route('expenses.index') }}" class="btn btn-secondary btn-sm">
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
        const accountsUrl = '{{ route('accounts.index') }}';
        const methodTypeMap = {
            'Cash': 'hand_cash',
            'Bank Transfer': 'bank',
            'Mobile Banking': 'mobile',
        };
        const accountTypeMap = {
            'Cash': 'App\\Models\\HandCash',
            'Bank Transfer': 'App\\Models\\BankAccount',
            'Mobile Banking': 'App\\Models\\MobileBankingAccount',
        };

        const $accountSelect = $('#expenseAccountSelect');
        $accountSelect.attr('data-selected', '{{ old('account_id', $expense->account_id ?? '') }}');

        function loadExpenseAccounts(method) {
            const type = methodTypeMap[method];
            const accountType = accountTypeMap[method];
            const $wrapper = $('#expenseAccountWrapper');
            const selectedId = $accountSelect.data('selected');

            if (!type) {
                $('#expenseAccountType').val('');
                $accountSelect.html('<option value="">Select Account</option>');
                $wrapper.hide();
                return;
            }

            $('#expenseAccountType').val(accountType);

            $.ajax({
                url: accountsUrl,
                method: 'GET',
                dataType: 'json',
                data: {
                    type: type
                },
                success: function (accounts) {
                    $accountSelect.html('<option value="">Select Account</option>');
                    accounts.forEach(function (a) {
                        const isSelected = selectedId && String(selectedId) === String(a.id);
                        $accountSelect.append(`<option value="${a.id}" ${isSelected ? 'selected' : ''}>${a.label}</option>`);
                    });
                    $wrapper.toggle(accounts.length > 0);
                    $accountSelect.data('selected', '');
                },
                error: function () {
                    $wrapper.hide();
                }
            });
        }

        $('#expensePaymentMethod').on('change', function () {
            $accountSelect.data('selected', '');
            loadExpenseAccounts($(this).val());
        });

        loadExpenseAccounts($('#expensePaymentMethod').val());

        if ($('.is-invalid').length > 0) {
            $('html, body').animate({
                scrollTop: $('.is-invalid').first().offset().top - 50
            }, 300);
        }
    });
</script>
@endsection
