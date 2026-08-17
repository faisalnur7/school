@php
    $isEdit = isset($income);
    $incomeData = $isEdit ? $income : null;
    $pageTitle = $pageTitle ?? ($isEdit ? 'Edit Income' : 'Record Income');
    $pageIcon = $pageIcon ?? ($isEdit ? 'fa-edit' : 'fa-plus-circle');
    $submitLabel = $submitLabel ?? ($isEdit ? 'Update Income' : 'Save Income');
    $submitIcon = $submitIcon ?? ($isEdit ? 'fa-save' : 'fa-save');
    $backRoute = $backRoute ?? route('incomes.index');
    $selectedAccountType = old('account_type', data_get($incomeData, 'account_type', \App\Models\HandCash::class));
    $selectedAccountId = old('account_id', data_get($incomeData, 'account_id', ''));
    $referenceValue = old('reference_no', data_get($incomeData, 'reference_no', 'Optional'));
    $incomeDateValue = old('income_date', $isEdit ? $income->income_date->format('d/m/Y') : '');
    $currentAttachmentUrl = $isEdit && filled($income->attachment)
        ? asset('storage/' . ltrim($income->attachment, '/'))
        : null;
    $currentAttachmentName = $isEdit && filled($income->attachment)
        ? basename($income->attachment)
        : 'income-attachment';
@endphp

<div class="container-fluid income-form-shell">
    <div class="income-form-hero">
        <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
            <div>
                <div class="badge mb-2">
                    <i class="fas {{ $pageIcon }} mr-1"></i>{{ $isEdit ? 'Modify existing income' : 'Create a new income entry' }}
                </div>
                <h3 class="mb-1 font-weight-bold text-white">{{ $pageTitle }}</h3>
                <div style="color:#cbd5e1;font-size:0.92rem">
                    Capture the income details, cash account, and attachment in one consistent form.
                </div>
            </div>
            <a href="{{ $backRoute }}" class="btn btn-light btn-sm">
                <i class="fas fa-arrow-left mr-1"></i> Back
            </a>
        </div>
    </div>

    <form method="POST" action="{{ $formAction }}" id="modernForm" enctype="multipart/form-data">
        @csrf
        @if($formMethod !== 'POST')
            @method($formMethod)
        @endif

        <div class="row">
            <div class="col-lg-8">
                <div class="card income-form-card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                            <div>
                                <h4 class="card-title mb-0 font-weight-bold text-white">
                                    <i class="fas {{ $pageIcon }} mr-2"></i>{{ $pageTitle }}
                                </h4>
                                <div style="color:rgba(255,255,255,0.78);font-size:0.85rem">
                                    Use the same layout for create and edit.
                                </div>
                            </div>
                            <span class="badge">
                                <i class="fas fa-receipt mr-1"></i>Income Entry
                            </span>
                        </div>
                    </div>

                    <div class="card-body">
                        @if ($errors->any())
                            <div class="alert alert-danger alert-dismissible fade show border-0 mb-3" role="alert">
                                <i class="fas fa-exclamation-circle mr-1"></i><strong>Errors:</strong>
                                <ul class="mb-0 mt-1 ml-4 small">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                                <button type="button" class="close" data-dismiss="alert">
                                    <span>&times;</span>
                                </button>
                            </div>
                        @endif

                        <div class="income-section">
                            <div class="income-section-title">
                                <i class="fas fa-layer-group"></i>
                                <span>Income Details</span>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group mb-2">
                                        <label class="small mb-1">Category</label>
                                        <select name="income_category_id" class="form-control form-control-sm @error('income_category_id') is-invalid @enderror" required>
                                            <option value="">Select Category</option>
                                            @foreach ($categories as $category)
                                        <option value="{{ $category->id }}" {{ old('income_category_id', data_get($incomeData, 'income_category_id', '')) == $category->id ? 'selected' : '' }}>
                                                    {{ $category->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('income_category_id')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group mb-2">
                                        <label class="small mb-1">Title</label>
                                        <input type="text" name="title" class="form-control form-control-sm @error('title') is-invalid @enderror" value="{{ old('title', data_get($incomeData, 'title', '')) }}" required>
                                        @error('title')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group mb-2">
                                        <label class="small mb-1">Amount (BDT)</label>
                                        <input type="number" name="amount" step="0.01" min="0" class="form-control form-control-sm @error('amount') is-invalid @enderror" value="{{ old('amount', data_get($incomeData, 'amount', '')) }}" required>
                                        @error('amount')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group mb-2">
                                        <label class="small mb-1">Income Date</label>
                                        <input type="text" name="income_date" class="form-control form-control-sm datepicker @error('income_date') is-invalid @enderror" value="{{ $incomeDateValue }}" placeholder="dd/mm/yyyy" autocomplete="off" required>
                                        @error('income_date')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group mb-2">
                                        <label class="small mb-1">Cash Account</label>
                                        <select name="account_id" id="incomeAccountSelect" class="form-control form-control-sm @error('account_id') is-invalid @enderror" required data-selected="{{ $selectedAccountId }}" data-selected-type="{{ $selectedAccountType }}">
                                            <option value="">Select Cash Account</option>
                                            @foreach (($accountGroups ?? []) as $group)
                                                <optgroup label="{{ $group['label'] }}">
                                                    @foreach ($group['accounts'] as $account)
                                                        <option value="{{ $account['id'] }}" data-account-type="{{ $account['type'] }}" {{ (string) $selectedAccountId === (string) $account['id'] ? 'selected' : '' }}>
                                                            {{ $account['label'] }}
                                                        </option>
                                                    @endforeach
                                                </optgroup>
                                            @endforeach
                                        </select>
                                        <input type="hidden" name="account_type" id="incomeAccountType" value="{{ $selectedAccountType }}">
                                        @error('account_id')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                        @error('account_type')
                                            <span class="invalid-feedback d-block">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group mb-2">
                                        <label class="small mb-1">Reference No <span class="text-muted">(optional)</span></label>
                                        <input type="text" name="reference_no" class="form-control form-control-sm @error('reference_no') is-invalid @enderror" value="{{ old('reference_no', data_get($incomeData, 'reference_no', '')) }}" placeholder="Enter reference number">
                                        @error('reference_no')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="income-section">
                            <div class="income-section-title">
                                <i class="fas fa-wallet"></i>
                                <span>Notes</span>
                            </div>

                            <div class="row">
                                <div class="col-md-8">
                                    <div class="form-group mb-2">
                                        <label class="small mb-1">Description <span class="text-muted">(optional)</span></label>
                                        <textarea name="description" class="form-control form-control-sm @error('description') is-invalid @enderror" rows="4" placeholder="Add any note or details">{{ old('description', data_get($incomeData, 'description', '')) }}</textarea>
                                        @error('description')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer">
                        <div class="income-actions">
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
                <div class="income-side-card">
                    <div class="side-head">
                        <div class="d-flex align-items-center justify-content-between gap-2">
                            <div>
                                <div class="text-muted" style="font-size:0.72rem;letter-spacing:0.08em;text-transform:uppercase;font-weight:700">Attachment</div>
                                <h5 class="mb-0" style="font-size:1rem;font-weight:700;color:#0f172a">Upload supporting file</h5>
                            </div>
                            <span class="badge" style="background:#eff6ff;color:#1d4ed8;border:1px solid #bfdbfe">
                                JPG, PNG, PDF
                            </span>
                        </div>
                    </div>
                    <div class="side-body">
                        <div class="income-upload mb-3">
                            @if($currentAttachmentUrl)
                                <div class="current-attachment">
                                    <div class="d-flex justify-content-between align-items-start gap-2">
                                        <div>
                                            <div class="text-muted mb-1" style="font-size:0.72rem;text-transform:uppercase;letter-spacing:0.08em;font-weight:700">Current File</div>
                                            <a href="{{ $currentAttachmentUrl }}" target="_blank" class="btn btn-outline-secondary btn-sm">
                                                <i class="fas fa-paperclip mr-1"></i>View Attachment
                                            </a>
                                        </div>
                                        <i class="fas fa-file-alt" style="font-size:1.25rem;color:#94a3b8"></i>
                                    </div>
                                </div>
                            @endif

                            <label class="small mb-1">Attachment</label>
                            <div
                                id="incomeAttachmentDropzone"
                                class="attachment-dropzone dropzone"
                                data-attachment-dropzone="1"
                                data-input-id="incomeAttachmentInput"
                                data-error-id="incomeAttachmentValidationError"
                                data-message="Drop attachment here or click to browse"
                                data-accepted-files=".jpg,.jpeg,.png,.pdf"
                                @if($currentAttachmentUrl)
                                    data-existing-url="{{ $currentAttachmentUrl }}"
                                    data-existing-name="{{ $currentAttachmentName }}"
                                @endif
                            >
                                <div class="dz-message needsclick">
                                    <div class="text-base font-semibold text-slate-700">Drop attachment here or click to browse</div>
                                    <div class="mt-1 text-sm text-slate-500">Allowed: JPG, PNG, PDF up to 100 KB.</div>
                                </div>
                            </div>
                            <input type="file" id="incomeAttachmentInput" name="attachment" class="d-none" accept=".jpg,.jpeg,.png,.pdf">
                            <div id="incomeAttachmentValidationError" class="mt-2 text-sm text-danger"></div>
                            @error('attachment')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="income-summary-list">
                            <div class="income-summary-item">
                                <div class="label">Cash account</div>
                                <div class="value">{{ old('account_id') ? 'Selected cash account' : ($isEdit ? ($income->account_display_name ?? 'Select cash account') : 'Select cash account') }}</div>
                            </div>
                            <div class="income-summary-item">
                                <div class="label">Reference</div>
                                <div class="value">{{ $referenceValue ?: 'Optional' }}</div>
                            </div>
                            <div class="income-summary-item">
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
