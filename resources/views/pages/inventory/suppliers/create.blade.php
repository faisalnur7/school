@extends('layouts.master')

@section('contents')
<div class="container-fluid px-3 py-3">
    <div class="card shadow-sm border-0">
        <div class="card-header bg-gradient-primary text-white py-3">
            <div class="d-flex justify-content-between align-items-center">
                <h4 class="card-title mb-0 font-weight-bold">
                    <i class="fas fa-plus-circle mr-2"></i>Create Supplier
                </h4>
                <a href="{{ route('inventory.suppliers.index') }}" class="btn btn-light btn-sm">
                    <i class="fas fa-arrow-left mr-1"></i> Back
                </a>
            </div>
        </div>

        <form method="POST" action="{{ route('inventory.suppliers.store') }}" id="supplierForm">
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

                <!-- Row 1: Name & Company -->
                <div class="row g-2 mb-3">
                    <div class="col-md-6">
                        <label class="form-label small font-weight-600 mb-1">Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" value="{{ old('name') }}" class="form-control form-control-sm @error('name') is-invalid @enderror" placeholder="Supplier name" required>
                        @error('name')<small class="text-danger">{{ $message }}</small>@enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label small font-weight-600 mb-1">Company Name</label>
                        <input type="text" name="company_name" value="{{ old('company_name') }}" class="form-control form-control-sm @error('company_name') is-invalid @enderror" placeholder="Company name">
                        @error('company_name')<small class="text-danger">{{ $message }}</small>@enderror
                    </div>
                </div>

                <!-- Row 2: Phone & Email -->
                <div class="row g-2 mb-3">
                    <div class="col-md-6">
                        <label class="form-label small font-weight-600 mb-1">Phone</label>
                        <input type="text" name="phone" value="{{ old('phone') }}" class="form-control form-control-sm @error('phone') is-invalid @enderror" placeholder="Phone number">
                        @error('phone')<small class="text-danger">{{ $message }}</small>@enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label small font-weight-600 mb-1">Email</label>
                        <input type="email" name="email" value="{{ old('email') }}" class="form-control form-control-sm @error('email') is-invalid @enderror" placeholder="Email address">
                        @error('email')<small class="text-danger">{{ $message }}</small>@enderror
                    </div>
                </div>

                <!-- Row 3: Address -->
                <div class="row g-2 mb-3">
                    <div class="col-12">
                        <label class="form-label small font-weight-600 mb-1">Address</label>
                        <textarea name="address" class="form-control form-control-sm @error('address') is-invalid @enderror" rows="2" placeholder="Enter address...">{{ old('address') }}</textarea>
                        @error('address')<small class="text-danger">{{ $message }}</small>@enderror
                    </div>
                </div>

                <!-- Row 4: Status -->
                <div class="row g-2 mb-3">
                    <div class="col-md-4">
                        <label class="form-label small font-weight-600 mb-1">Status</label>
                        <select name="status" class="form-control form-control-sm @error('status') is-invalid @enderror">
                            <option value="1" {{ old('status', '1') == '1' ? 'selected' : '' }}>Active</option>
                            <option value="0" {{ old('status') == '0' ? 'selected' : '' }}>Inactive</option>
                        </select>
                        @error('status')<small class="text-danger">{{ $message }}</small>@enderror
                    </div>
                </div>
            </div>

            <div class="card-footer bg-light border-top py-2 px-3">
                <div class="d-flex justify-content-between gap-2">
                    <a href="{{ route('inventory.suppliers.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-times mr-1"></i>Cancel
                    </a>
                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="fas fa-save mr-1"></i>Create Supplier
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@section('styles')
<style>
    .bg-gradient-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }

    .form-control-sm {
        border-radius: 0.375rem;
        border: 1px solid #dee2e6;
        transition: all 0.2s ease;
    }

    .form-control-sm:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 0.15rem rgba(102, 126, 234, 0.15);
    }

    .form-label {
        color: #2e3338;
        font-size: 0.8rem;
        margin-bottom: 0.25rem;
        display: block;
    }

    .btn-sm {
        padding: 0.375rem 0.75rem;
        font-size: 0.875rem;
        font-weight: 600;
        border-radius: 0.375rem;
        transition: all 0.2s ease;
    }

    .btn-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none;
    }

    .btn-primary:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
    }

    .btn-secondary {
        background-color: #6c757d;
        border: none;
    }

    .btn-secondary:hover {
        background-color: #5a6268;
        transform: translateY(-1px);
    }

    .gap-2 {
        gap: 0.5rem;
    }

    .is-invalid {
        border-color: #dc3545 !important;
    }

    .invalid-feedback {
        color: #dc3545;
        font-size: 0.75rem;
        margin-top: 0.15rem;
    }

    @media (max-width: 576px) {
        .container-fluid {
            padding-left: 0.5rem !important;
            padding-right: 0.5rem !important;
        }

        .card-body {
            padding: 0.75rem !important;
        }

        .row.g-2 {
            margin-right: -0.5rem;
            margin-left: -0.5rem;
        }

        .row.g-2 > [class*="col-"] {
            padding-right: 0.5rem;
            padding-left: 0.5rem;
        }
    }
</style>
@endsection
