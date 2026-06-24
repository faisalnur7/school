@extends('layouts.master')

@section('contents')
<div class="container-fluid px-3 py-3 product-form-page">
    <div class="card shadow-sm border-0 product-form-card">
        <div class="card-header bg-white border-bottom py-3 px-4">
            <div class="d-flex flex-wrap justify-content-between align-items-start gap-2">
                <div class="flex flex-col">
                    <div class="text-uppercase small font-weight-bold text-muted mb-1">Inventory</div>
                    <h4 class="card-title mb-1 font-weight-bold text-slate-900">
                        <i class="fas fa-edit mr-2 text-primary"></i>Edit Product
                    </h4>
                    <p class="mb-0 text-muted product-form-subtitle">Update pricing, stock behavior, and class assignment without changing the product workflow.</p>
                </div>
                <a href="{{ route('inventory.products.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-arrow-left mr-1"></i> Back
                </a>
            </div>
        </div>

        <form method="POST" action="{{ route('inventory.products.update', $item->id) }}" id="productForm">
            @csrf
            @method('PUT')
            <div class="card-body p-4 p-md-4 product-form-body">
                @if($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show border-0 mb-4" role="alert">
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

                <div class="product-form-section">
                    <div class="product-form-section__head">
                        <div>
                            <div class="product-form-section__eyebrow">Basic Information</div>
                            <h5 class="product-form-section__title mb-1">Product identity</h5>
                            <p class="product-form-section__subtitle mb-0">Keep the product name, category, and type aligned with the inventory structure.</p>
                        </div>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label product-form-label">Category <span class="text-danger">*</span></label>
                            <select name="category_id" id="category_id" class="form-control product-form-control @error('category_id') is-invalid @enderror" required>
                                <option value="">Select category</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}" {{ old('category_id', $item->category_id) == $cat->id ? 'selected' : '' }}>
                                        {{ $cat->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category_id')<small class="text-danger">{{ $message }}</small>@enderror
                        </div>

                        <div class="col-md-5">
                            <label class="form-label product-form-label">Product Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" value="{{ old('name', $item->name) }}" class="form-control product-form-control @error('name') is-invalid @enderror" placeholder="Enter product name" required>
                            @error('name')<small class="text-danger">{{ $message }}</small>@enderror
                        </div>

                        <div class="col-md-3">
                            <label class="form-label product-form-label">Item Type <span class="text-danger">*</span></label>
                            <div class="btn-group btn-group-toggle product-toggle w-100" data-toggle="buttons" role="group">
                                <label class="btn btn-outline-primary {{ old('item_type', $item->item_type) == 'common' ? 'active' : '' }}">
                                    <input type="radio" name="item_type" value="common" {{ old('item_type', $item->item_type) == 'common' ? 'checked' : '' }} autocomplete="off">
                                    Common
                                </label>
                                <label class="btn btn-outline-primary {{ old('item_type', $item->item_type) == 'classwise' ? 'active' : '' }}">
                                    <input type="radio" name="item_type" value="classwise" {{ old('item_type', $item->item_type) == 'classwise' ? 'checked' : '' }} autocomplete="off">
                                    Classwise
                                </label>
                            </div>
                            @error('item_type')<small class="text-danger d-block mt-1">{{ $message }}</small>@enderror
                        </div>
                    </div>
                </div>

                <div class="product-form-section">
                    <div class="product-form-section__head">
                        <div>
                            <div class="product-form-section__eyebrow">Pricing & Stock</div>
                            <h5 class="product-form-section__title mb-1">Sales behavior</h5>
                            <p class="product-form-section__subtitle mb-0">Control default pricing, flexibility, and how the item behaves against stock.</p>
                        </div>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label product-form-label">Unit</label>
                            <input type="text" name="unit" value="{{ old('unit', $item->unit) }}" class="form-control product-form-control @error('unit') is-invalid @enderror" placeholder="pcs, box, kg">
                            @error('unit')<small class="text-danger">{{ $message }}</small>@enderror
                        </div>
                        <div class="col-md-3">
                            <label class="form-label product-form-label">Purchase Price</label>
                            <div class="input-group product-input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">৳</span>
                                </div>
                                <input type="number" step="0.01" min="0" name="purchase_price" value="{{ old('purchase_price', $item->purchase_price) }}" class="form-control product-form-control @error('purchase_price') is-invalid @enderror" placeholder="0.00">
                            </div>
                            @error('purchase_price')<small class="text-danger">{{ $message }}</small>@enderror
                        </div>
                        <div class="col-md-3">
                            <label class="form-label product-form-label">Selling Price</label>
                            <div class="input-group product-input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">৳</span>
                                </div>
                                <input type="number" step="0.01" min="0" name="selling_price" value="{{ old('selling_price', $item->selling_price) }}" class="form-control product-form-control @error('selling_price') is-invalid @enderror" placeholder="0.00">
                            </div>
                            @error('selling_price')<small class="text-danger">{{ $message }}</small>@enderror
                        </div>
                        <div class="col-md-3">
                            <label class="form-label product-form-label">Stock Type</label>
                            <select name="stock_type" class="form-control product-form-control @error('stock_type') is-invalid @enderror">
                                <option value="stocked" {{ old('stock_type', $item->stock_type ?? 'stocked') === 'stocked' ? 'selected' : '' }}>Stocked</option>
                                <option value="made_to_order" {{ old('stock_type', $item->stock_type ?? 'stocked') === 'made_to_order' ? 'selected' : '' }}>Made to order</option>
                            </select>
                            @error('stock_type')<small class="text-danger">{{ $message }}</small>@enderror
                        </div>
                    </div>

                    <div class="row g-3 mt-1">
                        <div class="col-md-6">
                            <div class="product-option-box h-100">
                                <div class="form-check m-0">
                                    <input type="checkbox" name="is_flexible_price" value="1" class="form-check-input" id="isFlexiblePrice" {{ old('is_flexible_price', $item->is_flexible_price ? '1' : '0') == '1' ? 'checked' : '' }}>
                                    <label class="form-check-label font-weight-600" for="isFlexiblePrice">Flexible price</label>
                                </div>
                                <small class="text-muted d-block mt-2">Allow the final unit price to be adjusted during Collect Payment for student-specific cases.</small>
                                @error('is_flexible_price')<small class="text-danger">{{ $message }}</small>@enderror
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label product-form-label">Min Stock Alert</label>
                            <div class="input-group product-input-group">
                                <input type="number" min="0" name="minimum_stock_alert" value="{{ old('minimum_stock_alert', $item->minimum_stock_alert) }}" class="form-control product-form-control @error('minimum_stock_alert') is-invalid @enderror" placeholder="0">
                                <div class="input-group-append">
                                    <span class="input-group-text">units</span>
                                </div>
                            </div>
                            @error('minimum_stock_alert')<small class="text-danger">{{ $message }}</small>@enderror
                        </div>
                        <div class="col-md-3">
                            <label class="form-label product-form-label">Status</label>
                            <select name="is_active" class="form-control product-form-control @error('is_active') is-invalid @enderror">
                                <option value="1" {{ old('is_active', $item->is_active ? '1' : '0') == '1' ? 'selected' : '' }}>Active</option>
                                <option value="0" {{ old('is_active', $item->is_active ? '1' : '0') == '0' ? 'selected' : '' }}>Inactive</option>
                            </select>
                            @error('is_active')<small class="text-danger">{{ $message }}</small>@enderror
                        </div>
                    </div>

                    <div class="mt-3">
                        <div class="alert alert-info border-0 mb-0 py-2 px-3 product-stock-summary">
                            <i class="fas fa-info-circle mr-2"></i>
                            <small><strong>Current Stock:</strong> <span class="badge badge-primary">{{ $item->current_stock }} units</span></small>
                        </div>
                    </div>

                    <div class="mt-3">
                        <div class="alert alert-light border mb-0 py-2 px-3 product-note">
                            <small class="text-muted">
                                Use <strong>Made to order</strong> for uniforms and similar items that are produced after customer order. These can be sold without stock.
                            </small>
                        </div>
                    </div>
                </div>

                <div class="product-form-section mb-0">
                    <div class="product-form-section__head">
                        <div>
                            <div class="product-form-section__eyebrow">Details</div>
                            <h5 class="product-form-section__title mb-1">Description and class rules</h5>
                            <p class="product-form-section__subtitle mb-0">Add a note, then set classwise access only when the product requires it.</p>
                        </div>
                    </div>
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label product-form-label">Description</label>
                            <textarea name="description" class="form-control product-form-control @error('description') is-invalid @enderror" rows="3" placeholder="Enter product description...">{{ old('description', $item->description) }}</textarea>
                            @error('description')<small class="text-danger">{{ $message }}</small>@enderror
                        </div>
                    </div>

                    <div id="classwise_section" class="product-classwise-box" style="display:none;">
                        <div class="row g-3">
                            <div class="col-12">
                                <div class="product-classwise-box__head">
                                    <div>
                                        <div class="product-form-section__eyebrow">Classwise Configuration</div>
                                        <h6 class="mb-1 font-weight-bold">Applicable only for classwise products</h6>
                                        <p class="mb-0 text-muted">Specify the class and group where this product should be available.</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label product-form-label">Class</label>
                                <select name="school_class_id" class="form-control product-form-control @error('school_class_id') is-invalid @enderror">
                                    <option value="">Select class</option>
                                    @foreach($classes as $c)
                                        <option value="{{ $c->id }}" {{ old('school_class_id', $item->school_class_id) == $c->id ? 'selected' : '' }}>
                                            {{ $c->name_en }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('school_class_id')<small class="text-danger">{{ $message }}</small>@enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label product-form-label">Group</label>
                                <select name="group_id" class="form-control product-form-control @error('group_id') is-invalid @enderror">
                                    <option value="">Select group</option>
                                    @foreach($groups as $g)
                                        <option value="{{ $g->id }}" {{ old('group_id', $item->group_id) == $g->id ? 'selected' : '' }}>
                                            {{ $g->name_en }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('group_id')<small class="text-danger">{{ $message }}</small>@enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-footer bg-white border-top py-3 px-4">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <a href="{{ route('inventory.products.index') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="fas fa-times mr-1"></i>Cancel
                    </a>
                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="fas fa-save mr-1"></i>Update Product
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@section('styles')
<style>
    .product-form-page {
        width: 100%;
        max-width: none;
    }

    .product-form-card {
        border-radius: 1rem;
        overflow: hidden;
    }

    .product-form-subtitle {
        font-size: 0.92rem;
    }

    .product-form-body {
        background: linear-gradient(180deg, #ffffff 0%, #fbfdff 100%);
    }

    .product-form-section {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 1rem;
        padding: 1rem;
        margin-bottom: 1rem;
    }

    .product-form-section__head {
        margin-bottom: 1rem;
    }

    .product-form-section__eyebrow {
        font-size: 0.72rem;
        font-weight: 600;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        color: #64748b;
        margin-bottom: 0.35rem;
    }

    .product-form-section__title {
        font-size: 1rem;
        color: #0f172a;
    }

    .product-form-section__subtitle {
        color: #64748b;
        font-size: 0.92rem;
    }

    .product-form-label {
        font-size: 0.8rem;
        font-weight: 600;
        color: #475569;
        margin-bottom: 0.35rem;
    }

    .product-form-control,
    .product-input-group .input-group-text {
        min-height: 2.8rem;
        border-radius: 0.75rem;
        border-color: #dbe4ee;
    }

    .product-form-control:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 0.15rem rgba(102, 126, 234, 0.12);
    }

    .product-input-group .input-group-prepend .input-group-text,
    .product-input-group .input-group-append .input-group-text {
        background: #f8fafc;
        color: #64748b;
        font-weight: 600;
    }

    .product-toggle .btn {
        min-height: 2.8rem;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        border-radius: 0.75rem;
    }

    .product-toggle .btn.active {
        background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
        border-color: #2563eb;
        color: #fff;
    }

    .product-option-box {
        background: #fff;
        border: 1px solid #dbe4ee;
        border-radius: 0.875rem;
        padding: 0.9rem 1rem;
    }

    .product-classwise-box {
        margin-top: 1rem;
        background: #fff;
        border: 1px dashed #cbd5e1;
        border-radius: 0.875rem;
        padding: 1rem;
    }

    .product-classwise-box__head {
        margin-bottom: 1rem;
    }

    .product-stock-summary,
    .product-note {
        background: #fff;
    }

    .btn-sm {
        border-radius: 0.75rem;
        font-weight: 600;
    }

    .alert-sm {
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

    .badge-primary {
        background-color: #667eea;
    }

    @media (max-width: 576px) {
        .container-fluid {
            padding-left: 0.5rem !important;
            padding-right: 0.5rem !important;
        }

        .product-form-section {
            padding: 0.85rem;
        }

        .product-form-body {
            padding: 1rem !important;
        }

        .row.g-3 {
            margin-right: -0.5rem;
            margin-left: -0.5rem;
        }

        .row.g-3 > [class*="col-"] {
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
