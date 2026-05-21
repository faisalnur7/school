@extends('layouts.master')

@section('contents')
<div class="container-fluid px-3 py-3">
    <div class="card shadow-sm border-0">
        <div class="card-header bg-gradient-primary text-white py-3">
            <div class="d-flex justify-content-between align-items-center">
                <h4 class="card-title mb-0 font-weight-bold text-white">
                    <i class="fas fa-plus-circle mr-2"></i>Record Expense
                </h4>
                <a href="{{ route('expenses.index') }}" class="btn btn-light btn-sm">
                    <i class="fas fa-arrow-left mr-1"></i> Back
                </a>
            </div>
        </div>

        <form method="POST" action="{{ route('expenses.store') }}" id="modernForm">
            @csrf

            <div class="card-body p-3">
                @if($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show border-0 mb-2 py-2" role="alert">
                        <i class="fas fa-exclamation-circle mr-1"></i><strong>Errors:</strong>
                        <ul class="mb-0 mt-1 ml-4 small">
                            @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                        </ul>
                        <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                    </div>
                @endif

                <div class="row">
                    {{-- Row 1: Category | Title | Amount --}}
                    <div class="col-md-4">
                        <div class="form-group mb-2">
                            <label class="small mb-1">Category</label>
                            <select name="expense_category_id" class="form-control form-control-sm" required>
                                <option value="">Select Category</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('expense_category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group mb-2">
                            <label class="small mb-1">Title</label>
                            <input type="text" name="title" class="form-control form-control-sm @error('title') is-invalid @enderror" value="{{ old('title') }}" required>
                            @error('title')<span class="invalid-feedback">{{ $message }}</span>@enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group mb-2">
                            <label class="small mb-1">Amount (BDT)</label>
                            <input type="number" name="amount" step="0.01" min="0" class="form-control form-control-sm @error('amount') is-invalid @enderror" value="{{ old('amount') }}" required>
                            @error('amount')<span class="invalid-feedback">{{ $message }}</span>@enderror
                        </div>
                    </div>

                    {{-- Row 2: Date | Payment Method | Reference No --}}
                    <div class="col-md-4">
                        <div class="form-group mb-2">
                            <label class="small mb-1">Expense Date</label>
                            <input type="text" name="expense_date" class="form-control form-control-sm datepicker @error('expense_date') is-invalid @enderror" value="{{ old('expense_date') }}" placeholder="dd/mm/yyyy" autocomplete="off" required>
                            @error('expense_date')<span class="invalid-feedback">{{ $message }}</span>@enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group mb-2">
                            <label class="small mb-1">Payment Method</label>
                            <select id="expensePaymentMethod" name="payment_method" class="form-control form-control-sm" required>
                                @foreach (['Cash', 'Bank Transfer', 'Cheque', 'Mobile Banking', 'Other'] as $method)
                                    <option value="{{ $method }}" {{ old('payment_method') == $method ? 'selected' : '' }}>{{ $method }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group mb-2">
                            <label class="small mb-1">Reference No <span class="text-muted">(optional)</span></label>
                            <input type="text" name="reference_no" class="form-control form-control-sm" value="{{ old('reference_no') }}">
                        </div>
                    </div>

                    {{-- Row 3: Description | Attachment --}}
                    <input type="hidden" name="account_type" id="expenseAccountType" value="{{ old('account_type') }}">

                    <div class="col-md-4" id="expenseAccountWrapper" style="display: none;">
                        <div class="form-group mb-2">
                            <label class="small mb-1">Account <span class="text-muted">(optional)</span></label>
                            <select name="account_id" id="expenseAccountSelect" class="form-control form-control-sm @error('account_id') is-invalid @enderror" data-selected="{{ old('account_id') }}">
                                <option value="">Select Account</option>
                            </select>
                            @error('account_id')<span class="invalid-feedback">{{ $message }}</span>@enderror
                        </div>
                    </div>

                    <div class="col-md-8">
                        <div class="form-group mb-2">
                            <label class="small mb-1">Description <span class="text-muted">(optional)</span></label>
                            <textarea name="description" class="form-control form-control-sm" rows="2">{{ old('description') }}</textarea>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group mb-2">
                            <label class="small mb-1">Attachment <span class="text-muted">(jpg, png, pdf — max 100KB)</span></label>
                            <input type="file" name="attachment" class="form-control form-control-sm @error('attachment') is-invalid @enderror" accept=".jpg,.jpeg,.png,.pdf">
                            @error('attachment')<span class="invalid-feedback">{{ $message }}</span>@enderror
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-footer bg-light border-top py-2 px-3">
                <div class="d-flex justify-content-between gap-2">
                    <a href="{{ route('expenses.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-times mr-1"></i>Cancel
                    </a>
                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="fas fa-save mr-1"></i>Create
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

        function loadExpenseAccounts(method) {
            const type = methodTypeMap[method];
            const accountType = accountTypeMap[method];
            const $wrapper = $('#expenseAccountWrapper');
            const $select = $('#expenseAccountSelect');
            const selectedId = $select.data('selected');

            if (!type) {
                $('#expenseAccountType').val('');
                $select.html('<option value="">Select Account</option>');
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
                    $select.html('<option value="">Select Account</option>');
                    accounts.forEach(function (a) {
                        const isSelected = selectedId && String(selectedId) === String(a.id);
                        $select.append(`<option value="${a.id}" ${isSelected ? 'selected' : ''}>${a.label}</option>`);
                    });
                    $wrapper.toggle(accounts.length > 0);
                    $select.data('selected', '');
                },
                error: function () {
                    $wrapper.hide();
                }
            });
        }

        $('#expensePaymentMethod').on('change', function () {
            $('#expenseAccountSelect').data('selected', '');
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
