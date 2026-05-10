@extends('layouts.master')

@section('contents')
<div class="col-md-10">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Edit Product</h3>
            <div class="card-tools">
                <a href="{{ route('inventory.products.index') }}" class="btn btn-secondary btn-sm">
                    <i class="fas fa-list"></i> Back to List
                </a>
            </div>
        </div>
        <form method="POST" action="{{ route('inventory.products.update', $item->id) }}">
            @csrf
            @method('PUT')
            <div class="card-body">
                @if($errors->any())
                    <div class="alert alert-danger">Please fix the errors below.</div>
                @endif

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Category <span class="text-danger">*</span></label>
                            <select name="category_id" id="category_id" class="form-control" required>
                                <option value="">Select</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}" data-name="{{ strtolower($cat->name) }}"
                                        {{ old('category_id', $item->category_id) == $cat->id ? 'selected' : '' }}>
                                        {{ $cat->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category_id')<small class="text-danger">{{ $message }}</small>@enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Name <span class="text-danger">*</span></label>
                            <input name="name" value="{{ old('name', $item->name) }}" class="form-control" required>
                            @error('name')<small class="text-danger">{{ $message }}</small>@enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>SKU / Code</label>
                            <input name="sku" value="{{ old('sku', $item->sku) }}" class="form-control">
                            @error('sku')<small class="text-danger">{{ $message }}</small>@enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Barcode</label>
                            <input name="barcode" value="{{ old('barcode', $item->barcode) }}" class="form-control">
                            @error('barcode')<small class="text-danger">{{ $message }}</small>@enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Unit</label>
                            <input name="unit" value="{{ old('unit', $item->unit) }}" class="form-control">
                            @error('unit')<small class="text-danger">{{ $message }}</small>@enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Purchase Price</label>
                            <input type="number" step="0.01" min="0" name="purchase_price" value="{{ old('purchase_price', $item->purchase_price) }}" class="form-control">
                            @error('purchase_price')<small class="text-danger">{{ $message }}</small>@enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Minimum Stock Alert</label>
                            <input type="number" min="0" name="minimum_stock_alert" value="{{ old('minimum_stock_alert', $item->minimum_stock_alert) }}" class="form-control">
                            @error('minimum_stock_alert')<small class="text-danger">{{ $message }}</small>@enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Status</label>
                            <select name="is_active" class="form-control">
                                <option value="1" {{ old('is_active', $item->is_active ? '1' : '0') == '1' ? 'selected' : '' }}>Active</option>
                                <option value="0" {{ old('is_active', $item->is_active ? '1' : '0') == '0' ? 'selected' : '' }}>Inactive</option>
                            </select>
                            @error('is_active')<small class="text-danger">{{ $message }}</small>@enderror
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label>Current Stock</label>
                    <input value="{{ $item->current_stock }}" class="form-control" readonly>
                    <small class="text-muted">Stock updates via Purchases only.</small>
                </div>

                <div class="form-group">
                    <label>Description</label>
                    <textarea name="description" class="form-control" rows="3">{{ old('description', $item->description) }}</textarea>
                    @error('description')<small class="text-danger">{{ $message }}</small>@enderror
                </div>

                <div id="books_fields" style="display:none;">
                    <hr>
                    <h5>Books Fields</h5>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Class</label>
                                <select name="school_class_id" class="form-control">
                                    <option value="">Select</option>
                                    @foreach($classes as $c)
                                        <option value="{{ $c->id }}" {{ old('school_class_id', $item->school_class_id) == $c->id ? 'selected' : '' }}>{{ $c->name_en }}</option>
                                    @endforeach
                                </select>
                                @error('school_class_id')<small class="text-danger">{{ $message }}</small>@enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Section</label>
                                <select name="section_id" class="form-control">
                                    <option value="">Select</option>
                                    @foreach($sections as $s)
                                        <option value="{{ $s->id }}" {{ old('section_id', $item->section_id) == $s->id ? 'selected' : '' }}>{{ $s->name_en }}</option>
                                    @endforeach
                                </select>
                                @error('section_id')<small class="text-danger">{{ $message }}</small>@enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Group</label>
                                <select name="group_id" class="form-control">
                                    <option value="">Select</option>
                                    @foreach($groups as $g)
                                        <option value="{{ $g->id }}" {{ old('group_id', $item->group_id) == $g->id ? 'selected' : '' }}>{{ $g->name_en }}</option>
                                    @endforeach
                                </select>
                                @error('group_id')<small class="text-danger">{{ $message }}</small>@enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <button class="btn btn-primary">Update</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function toggleBooksFields() {
        const selected = $('#category_id option:selected').data('name') || '';
        if (String(selected).toLowerCase() === 'books') {
            $('#books_fields').show();
        } else {
            $('#books_fields').hide();
            $('#books_fields').find('select').val('');
        }
    }
    $(function () {
        toggleBooksFields();
        $('#category_id').on('change', toggleBooksFields);
    });
</script>
@endsection

