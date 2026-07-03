@php
    $isEdit = isset($transaction);
    $transactionData = $isEdit ? $transaction : null;
    $pageTitle = $pageTitle ?? ($isEdit ? 'Edit Capital Transaction' : 'Add Capital Transaction');
    $pageIcon = $pageIcon ?? ($isEdit ? 'fa-edit' : 'fa-plus-circle');
    $submitLabel = $submitLabel ?? ($isEdit ? 'Update Transaction' : 'Create Transaction');
    $submitIcon = $submitIcon ?? 'fa-save';
    $backRoute = $backRoute ?? route('shareholder-transactions.index');
    $typeValue = old('type', data_get($transactionData, 'type', 'capital'));
    $methodValue = old('payment_method', data_get($transactionData, 'payment_method', 'Cash'));
    $referenceValue = old('reference_no', data_get($transactionData, 'reference_no', 'Auto-generated on save'));
    $transactionDateValue = old('transaction_date', $isEdit ? $transaction->transaction_date->format('d/m/Y') : now()->format('d/m/Y'));
    $selectedAccountId = old('account_id', $accountId ?? data_get($transactionData, 'account_id', ''));
@endphp

<div class="container-fluid capital-form-shell">
    <div class="capital-form-hero">
        <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
            <div>
                <div class="badge mb-2">
                    <i class="fas {{ $pageIcon }} mr-1"></i>{{ $isEdit ? 'Modify shareholder transaction' : 'Create shareholder transaction' }}
                </div>
                <h3 class="mb-1 font-weight-bold text-white">{{ $pageTitle }}</h3>
                <div style="color:#cbd5e1;font-size:0.92rem">
                    Keep capital and withdrawal entries consistent with the same modern form layout.
                </div>
            </div>
            <a href="{{ $backRoute }}" class="btn btn-light btn-sm">
                <i class="fas fa-arrow-left mr-1"></i> Back
            </a>
        </div>
    </div>

    <form method="POST" action="{{ $formAction }}" id="modernForm">
        @csrf
        @if($formMethod !== 'POST')
            @method($formMethod)
        @endif

        <div class="row">
            <div class="col-lg-8">
                <div class="card capital-form-card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                            <div>
                                <h4 class="card-title mb-0 font-weight-bold text-white">
                                    <i class="fas {{ $pageIcon }} mr-2"></i>{{ $pageTitle }}
                                </h4>
                                <div style="color:rgba(255,255,255,0.78);font-size:0.85rem">
                                    The same layout is used for create and edit.
                                </div>
                            </div>
                            <span class="badge">
                                <i class="fas fa-coins mr-1"></i>Capital Entry
                            </span>
                        </div>
                    </div>

                    <div class="card-body">
                        @if($errors->any())
                            <div class="alert alert-danger alert-dismissible fade show border-0 mb-3" role="alert">
                                <i class="fas fa-exclamation-circle mr-1"></i><strong>Errors:</strong>
                                <ul class="mb-0 mt-1 ml-4 small">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                                <button type="button" class="close" data-dismiss="alert">
                                    <span>&times;</span>
                                </button>
                            </div>
                        @endif

                        <div class="capital-section">
                            <div class="capital-section-title">
                                <i class="fas fa-user-friends"></i>
                                <span>Transaction Details</span>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group mb-2">
                                        <label class="small mb-1">Shareholder <span class="text-danger">*</span></label>
                                        <select name="shareholder_id" class="form-control form-control-sm @error('shareholder_id') is-invalid @enderror" required>
                                            <option value="">Select Shareholder</option>
                                            @foreach ($shareholders as $sh)
                                                <option value="{{ $sh->id }}" {{ old('shareholder_id', data_get($transactionData, 'shareholder_id', '')) == $sh->id ? 'selected' : '' }}>
                                                    {{ $sh->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('shareholder_id')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group mb-2">
                                        <label class="small mb-1">Type <span class="text-danger">*</span></label>
                                        <select name="type" class="form-control form-control-sm @error('type') is-invalid @enderror" required>
                                            <option value="">Select Type</option>
                                            <option value="capital" {{ $typeValue === 'capital' ? 'selected' : '' }}>Capital (Investment)</option>
                                            <option value="withdrawal" {{ $typeValue === 'withdrawal' ? 'selected' : '' }}>Withdrawal (Drawing)</option>
                                        </select>
                                        @error('type')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group mb-2">
                                        <label class="small mb-1">Amount <span class="text-danger">*</span></label>
                                        <input type="number" name="amount" step="0.01" min="0.01" class="form-control form-control-sm @error('amount') is-invalid @enderror" value="{{ old('amount', data_get($transactionData, 'amount', '')) }}" required>
                                        @error('amount')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group mb-2">
                                        <label class="small mb-1">Transaction Date <span class="text-danger">*</span></label>
                                        <input type="text" name="transaction_date" class="form-control form-control-sm datepicker @error('transaction_date') is-invalid @enderror" value="{{ $transactionDateValue }}" placeholder="dd/mm/yyyy" autocomplete="off" required>
                                        @error('transaction_date')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group mb-2">
                                        <label class="small mb-1">Payment Method <span class="text-danger">*</span></label>
                                        <select name="payment_method" id="paymentMethod" class="form-control form-control-sm @error('payment_method') is-invalid @enderror" required>
                                            @foreach (['Cash', 'Bank Transfer', 'Cheque', 'Mobile Banking', 'Other'] as $method)
                                                <option value="{{ $method }}" {{ $methodValue === $method ? 'selected' : '' }}>{{ $method }}</option>
                                            @endforeach
                                        </select>
                                        @error('payment_method')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group mb-2">
                                        <label class="small mb-1">Reference No</label>
                                        <input type="text" class="form-control form-control-sm" value="{{ $referenceValue }}" disabled>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="capital-section">
                            <div class="capital-section-title">
                                <i class="fas fa-wallet"></i>
                                <span>Account & Notes</span>
                            </div>
                            <input type="hidden" name="account_type" id="transactionAccountType" value="{{ old('account_type', $accountType ?? data_get($transactionData, 'account_type', '')) }}">

                            <div class="row">
                                <div class="col-md-4" id="transactionAccountWrapper" style="display:none">
                                    <div class="form-group mb-2">
                                        <label class="small mb-1">Account <span class="text-muted">(optional)</span></label>
                                        <select name="account_id" id="transactionAccountSelect" class="form-control form-control-sm @error('account_id') is-invalid @enderror" data-selected="{{ $selectedAccountId }}">
                                            <option value="">Select Account</option>
                                        </select>
                                        @error('account_id')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-8">
                                    <div class="form-group mb-2">
                                        <label class="small mb-1">Description <span class="text-muted">(optional)</span></label>
                                        <textarea name="description" class="form-control form-control-sm @error('description') is-invalid @enderror" rows="4" placeholder="e.g. Initial investment, Monthly drawing...">{{ old('description', data_get($transactionData, 'description', '')) }}</textarea>
                                        @error('description')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer">
                        <div class="capital-actions">
                            <a href="{{ $backRoute }}" class="btn btn-secondary btn-sm">
                                <i class="fas fa-times mr-1"></i>Cancel
                            </a>
                            <button type="submit" class="btn btn-primary btn-sm">
                                <i class="fas {{ $submitIcon }} mr-1"></i>{{ $submitLabel }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="capital-side-card">
                    <div class="side-head">
                        <div class="d-flex align-items-center justify-content-between gap-2">
                            <div>
                                <div class="text-muted" style="font-size:0.72rem;letter-spacing:0.08em;text-transform:uppercase;font-weight:700">Guidance</div>
                                <h5 class="mb-0" style="font-size:1rem;font-weight:700;color:#111827">Transaction snapshot</h5>
                            </div>
                            <span class="badge" style="background:#eff6ff;color:#1d4ed8;border:1px solid #bfdbfe">
                                Capital / Withdrawal
                            </span>
                        </div>
                    </div>
                    <div class="side-body">
                        <div class="capital-summary-list">
                            <div class="capital-summary-item">
                                <div class="label">Type</div>
                                <div class="value">{{ $typeValue ? ucfirst($typeValue) : 'Select a type' }}</div>
                            </div>
                            <div class="capital-summary-item">
                                <div class="label">Payment method</div>
                                <div class="value">{{ $methodValue }}</div>
                            </div>
                            <div class="capital-summary-item">
                                <div class="label">Reference</div>
                                <div class="value">{{ $referenceValue }}</div>
                            </div>
                            <div class="capital-summary-item">
                                <div class="label">Mode</div>
                                <div class="value">{{ $isEdit ? 'Editing existing record' : 'Creating new record' }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
