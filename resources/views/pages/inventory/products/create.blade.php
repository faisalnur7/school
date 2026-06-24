@extends('layouts.master')

@section('contents')
<div class="container-fluid px-3 py-3">
    <div class="card shadow-sm border-0">
        <div class="card-header bg-gradient-primary text-white py-3">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="card-title mb-0 font-weight-bold text-white">
                        <i class="fas fa-plus-circle mr-2"></i>Create Product
                    </h4>
                </div>
                <a href="{{ route('inventory.products.index') }}" class="btn btn-light btn-sm">
                    <i class="fas fa-arrow-left mr-1"></i> Back
                </a>
            </div>
        </div>

        <form method="POST" action="{{ route('inventory.products.store') }}" id="productForm">
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

                <!-- Row 1: Category & Item Type -->
                <div class="row mb-2">
                    <div class="col-md-4">
                        <label class="form-label small font-weight-600 mb-1">Category <span class="text-danger">*</span></label>
                        <select name="category_id" id="category_id" class="form-control form-control-sm @error('category_id') is-invalid @enderror" required>
                            <option value="">Select</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                            @endforeach
                        </select>
                        @error('category_id')<small class="text-danger">{{ $message }}</small>@enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small font-weight-600 mb-1">Product Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" value="{{ old('name') }}" class="form-control form-control-sm @error('name') is-invalid @enderror" placeholder="Enter product name" required>
                        @error('name')<small class="text-danger">{{ $message }}</small>@enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small font-weight-600 mb-1">Item Type <span class="text-danger">*</span></label>
                        <div class="btn-group btn-group-sm w-100" data-toggle="buttons" role="group">
                            <label class="btn btn-outline-primary active" style="flex: 1;">
                                <input type="radio" name="item_type" value="common" {{ old('item_type', 'common') == 'common' ? 'checked' : '' }} autocomplete="off">
                                <i class="fas fa-cube mr-1"></i>Common
                            </label>
                            <label class="btn btn-outline-primary" style="flex: 1;">
                                <input type="radio" name="item_type" value="classwise" {{ old('item_type') == 'classwise' ? 'checked' : '' }} autocomplete="off">
                                <i class="fas fa-book mr-1"></i>Classwise
                            </label>
                        </div>
                        @error('item_type')<small class="text-danger d-block mt-1">{{ $message }}</small>@enderror
                    </div>
                </div>

                <!-- Row 2: Unit | Purchase Price | Selling Price -->
                <div class="row mb-2">
                    <div class="col-md-4">
                        <label class="form-label small font-weight-600 mb-1">Unit</label>
                        <input type="text" name="unit" value="{{ old('unit') }}" class="form-control form-control-sm @error('unit') is-invalid @enderror" placeholder="pcs, box, kg">
                        @error('unit')<small class="text-danger">{{ $message }}</small>@enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small font-weight-600 mb-1">Purchase Price</label>
                        <div class="input-group input-group-sm">
                            <div class="input-group-prepend"><span class="input-group-text">৳</span></div>
                            <input type="number" step="0.01" min="0" name="purchase_price" value="{{ old('purchase_price', 0) }}" class="form-control form-control-sm @error('purchase_price') is-invalid @enderror" placeholder="0.00">
                        </div>
                        @error('purchase_price')<small class="text-danger">{{ $message }}</small>@enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small font-weight-600 mb-1">Selling Price</label>
                        <div class="input-group input-group-sm">
                            <div class="input-group-prepend"><span class="input-group-text">৳</span></div>
                            <input type="number" step="0.01" min="0" name="selling_price" value="{{ old('selling_price', 0) }}" class="form-control form-control-sm @error('selling_price') is-invalid @enderror" placeholder="0.00">
                        </div>
                        @error('selling_price')<small class="text-danger">{{ $message }}</small>@enderror
                    </div>
                </div>

                <div class="row mb-2">
                    <div class="col-12">
                        <div class="form-check">
                            <input type="checkbox" name="is_flexible_price" value="1" class="form-check-input" id="isFlexiblePrice" {{ old('is_flexible_price') ? 'checked' : '' }}>
                            <label class="form-check-label font-weight-600" for="isFlexiblePrice">Flexible price</label>
                        </div>
                        <small class="text-muted d-block mt-1">Allow the final unit price to be adjusted during Collect Payment for student-specific cases.</small>
                        @error('is_flexible_price')<small class="text-danger">{{ $message }}</small>@enderror
                    </div>
                </div>

                <div class="row mb-2">
                    <div class="col-md-4">
                        <label class="form-label small font-weight-600 mb-1">Stock Type</label>
                        <select name="stock_type" class="form-control form-control-sm @error('stock_type') is-invalid @enderror">
                            <option value="stocked" {{ old('stock_type', 'stocked') === 'stocked' ? 'selected' : '' }}>Stocked</option>
                            <option value="made_to_order" {{ old('stock_type') === 'made_to_order' ? 'selected' : '' }}>Made to order</option>
                        </select>
                        @error('stock_type')<small class="text-danger">{{ $message }}</small>@enderror
                    </div>
                    <div class="col-md-8">
                        <div class="alert alert-light border mb-0 py-2 px-3">
                            <small class="text-muted">
                                Use <strong>Made to order</strong> for uniforms and similar items that are produced after customer order. These can be sold without stock.
                            </small>
                        </div>
                    </div>
                </div>

                <!-- Row 3: Min Stock Alert | Status | Description -->
                <div class="row mb-2">
                    <div class="col-md-4">
                        <label class="form-label small font-weight-600 mb-1">Min Stock Alert</label>
                        <div class="input-group input-group-sm">
                            <input type="number" min="0" name="minimum_stock_alert" value="{{ old('minimum_stock_alert', 0) }}" class="form-control form-control-sm @error('minimum_stock_alert') is-invalid @enderror" placeholder="0">
                            <div class="input-group-append"><span class="input-group-text">units</span></div>
                        </div>
                        @error('minimum_stock_alert')<small class="text-danger">{{ $message }}</small>@enderror
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small font-weight-600 mb-1">Status</label>
                        <select name="is_active" class="form-control form-control-sm @error('is_active') is-invalid @enderror">
                            <option value="1" {{ old('is_active', '1') == '1' ? 'selected' : '' }}>Active</option>
                            <option value="0" {{ old('is_active') == '0' ? 'selected' : '' }}>Inactive</option>
                        </select>
                        @error('is_active')<small class="text-danger">{{ $message }}</small>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small font-weight-600 mb-1">Description</label>
                        <textarea name="description" class="form-control form-control-sm @error('description') is-invalid @enderror" rows="2" placeholder="Enter product description...">{{ old('description') }}</textarea>
                        @error('description')<small class="text-danger">{{ $message }}</small>@enderror
                    </div>
                </div>

                <!-- Classwise Section -->
                <div id="classwise_section" style="display:none;">
                    <hr class="my-3">
                    <div class="alert alert-info alert-sm border-0 mb-3 py-2 px-3">
                        <i class="fas fa-info-circle mr-2"></i>
                        <small><strong>Classwise Configuration</strong> - Specify class and group</small>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small font-weight-600 mb-1">Class</label>
                            <select name="school_class_id" class="form-control form-control-sm @error('school_class_id') is-invalid @enderror">
                                <option value="">Select</option>
                                @foreach($classes as $c)
                                    <option value="{{ $c->id }}" {{ old('school_class_id') == $c->id ? 'selected' : '' }}>
                                        {{ $c->name_en }}
                                    </option>
                                @endforeach
                            </select>
                            @error('school_class_id')<small class="text-danger">{{ $message }}</small>@enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small font-weight-600 mb-1">Group</label>
                            <select name="group_id" class="form-control form-control-sm @error('group_id') is-invalid @enderror">
                                <option value="">Select</option>
                                @foreach($groups as $g)
                                    <option value="{{ $g->id }}" {{ old('group_id') == $g->id ? 'selected' : '' }}>
                                        {{ $g->name_en }}
                                    </option>
                                @endforeach
                            </select>
                            @error('group_id')<small class="text-danger">{{ $message }}</small>@enderror
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-footer bg-light border-top py-2 px-3">
                <div class="d-flex justify-content-between gap-2">
                    <a href="{{ route('inventory.products.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-times mr-1"></i>Cancel
                    </a>
                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="fas fa-save mr-1"></i>Create Product
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

    .btn-group-sm .btn {
        padding: 0.375rem 0.75rem;
        font-size: 0.875rem;
        font-weight: 600;
        transition: all 0.2s ease;
    }

    .btn-group-sm .btn.active {
        background-color: #667eea;
        border-color: #667eea;
        color: white;
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

    .card {
        transition: all 0.2s ease;
    }

    .alert-sm {
        font-size: 0.875rem;
    }

    .input-group-text {
        border: 1px solid #dee2e6;
        background-color: #f8f9fa;
        color: #6c757d;
        font-weight: 600;
        font-size: 0.875rem;
    }

    .is-invalid {
        border-color: #dc3545 !important;
    }

    .invalid-feedback {
        color: #dc3545;
        font-size: 0.75rem;
        margin-top: 0.15rem;
    }

    .gap-2 {
        gap: 0.5rem;
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

@section('scripts')
<script>
    $(function () {
        function toggleClasswiseFields() {
            const itemType = $('input[name="item_type"]:checked').val();
            const section = $('#classwise_section');

            if (itemType === 'classwise') {
                section.slideDown(200);
            } else {
                section.slideUp(200, function() {
                    section.find('select').val('');
                });
            }
        }

        toggleClasswiseFields();
        $('input[name="item_type"]').on('change', toggleClasswiseFields);

        if ($('.is-invalid').length > 0) {
            $('html, body').animate({
                scrollTop: $('.is-invalid').first().offset().top - 50
            }, 300);
        }
    });
</script>
@endsection
