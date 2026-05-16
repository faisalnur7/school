@extends('layouts.master')

@section('contents')
<div class="container-fluid px-3 py-3">
    <div class="card shadow-sm border-0">
        <div class="card-header bg-gradient-primary text-white py-3">
            <div class="d-flex justify-content-between align-items-center">
                <h4 class="card-title mb-0 font-weight-bold text-white">
                    <i class="fas fa-plus-circle mr-2"></i>Add Capital Transaction
                </h4>
                <a href="{{ route('shareholder-transactions.index') }}" class="btn btn-light btn-sm">
                    <i class="fas fa-arrow-left mr-1"></i> Back
                </a>
            </div>
        </div>

        <form method="POST" action="{{ route('shareholder-transactions.store') }}" id="modernForm">
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
                    {{-- Row 1: Shareholder | Type | Amount --}}
                    <div class="col-md-4">
                        <div class="form-group mb-2">
                            <label class="small mb-1">Shareholder <span class="text-danger">*</span></label>
                            <select name="shareholder_id" class="form-control form-control-sm @error('shareholder_id') is-invalid @enderror" required>
                                <option value="">Select Shareholder</option>
                                @foreach ($shareholders as $sh)
                                    <option value="{{ $sh->id }}" {{ old('shareholder_id') == $sh->id ? 'selected' : '' }}>{{ $sh->name }}</option>
                                @endforeach
                            </select>
                            @error('shareholder_id')<span class="invalid-feedback">{{ $message }}</span>@enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group mb-2">
                            <label class="small mb-1">Type <span class="text-danger">*</span></label>
                            <select name="type" class="form-control form-control-sm @error('type') is-invalid @enderror" required>
                                <option value="">Select Type</option>
                                <option value="capital" {{ old('type') === 'capital' ? 'selected' : '' }}>Capital (Investment)</option>
                                <option value="withdrawal" {{ old('type') === 'withdrawal' ? 'selected' : '' }}>Withdrawal (Drawing)</option>
                            </select>
                            @error('type')<span class="invalid-feedback">{{ $message }}</span>@enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group mb-2">
                            <label class="small mb-1">Amount <span class="text-danger">*</span></label>
                            <input type="number" name="amount" step="0.01" min="0.01" class="form-control form-control-sm @error('amount') is-invalid @enderror" value="{{ old('amount') }}" required>
                            @error('amount')<span class="invalid-feedback">{{ $message }}</span>@enderror
                        </div>
                    </div>

                    {{-- Row 2: Date | Payment Method | Description --}}
                    <div class="col-md-4">
                        <div class="form-group mb-2">
                            <label class="small mb-1">Transaction Date <span class="text-danger">*</span></label>
                            <input type="text" name="transaction_date" datepicker datepicker-format="dd/mm/yyyy" class="form-control form-control-sm @error('transaction_date') is-invalid @enderror" value="{{ old('transaction_date', now()->format('d/m/Y')) }}" placeholder="dd/mm/yyyy" autocomplete="off" required>
                            @error('transaction_date')<span class="invalid-feedback">{{ $message }}</span>@enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group mb-2">
                            <label class="small mb-1">Payment Method <span class="text-danger">*</span></label>
                            <select name="payment_method" id="paymentMethod" class="form-control form-control-sm" required>
                                @foreach (['Cash', 'Bank Transfer', 'Cheque', 'Mobile Banking', 'Other'] as $method)
                                    <option value="{{ $method }}" {{ old('payment_method') === $method ? 'selected' : '' }}>{{ $method }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group mb-2">
                            <label class="small mb-1">Description <span class="text-muted">(optional)</span></label>
                            <textarea name="description" class="form-control form-control-sm" rows="1" placeholder="e.g. Initial investment, Monthly drawing...">{{ old('description') }}</textarea>
                        </div>
                    </div>

                    <input type="hidden" name="account_type" id="transactionAccountType" value="{{ old('account_type') }}">

                    <div class="col-md-4" id="transactionAccountWrapper" style="display:none">
                        <div class="form-group mb-2">
                            <label class="small mb-1">Account <span class="text-muted">(optional)</span></label>
                            <select name="account_id" id="transactionAccountSelect" class="form-control form-control-sm">
                                <option value="">Select Account</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-footer bg-light border-top py-2 px-3">
                <div class="d-flex justify-content-between gap-2">
                    <a href="{{ route('shareholder-transactions.index') }}" class="btn btn-secondary btn-sm">
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
        if ($('.is-invalid').length > 0) {
            $('html, body').animate({
                scrollTop: $('.is-invalid').first().offset().top - 50
            }, 300);
        }
    });
</script>
@endsection