@extends('layouts.master')

@section('contents')
<div class="container-fluid px-3 py-3">
    <div class="card shadow-sm border-0">
        <div class="card-header bg-gradient-primary text-white py-3">
            <div class="d-flex justify-content-between align-items-center">
                <h4 class="card-title mb-0 font-weight-bold">
                    <i class="fas fa-plus-circle mr-2"></i>Create Purchase
                </h4>
                <a href="{{ route('inventory.purchases.index') }}" class="btn btn-light btn-sm">
                    <i class="fas fa-arrow-left mr-1"></i> Back
                </a>
            </div>
        </div>

        <form method="POST" action="{{ route('inventory.purchases.store') }}" id="purchaseForm">
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

                <!-- Row 1: Supplier, Date, Reference -->
                <div class="row g-2 mb-3">
                    <div class="col-md-4">
                        <label class="form-label small font-weight-600 mb-1">Supplier <span class="text-danger">*</span></label>
                        <select name="supplier_id" class="form-control form-control-sm @error('supplier_id') is-invalid @enderror" required>
                            <option value="">Select Supplier</option>
                            @foreach($suppliers as $s)
                                <option value="{{ $s->id }}" {{ old('supplier_id') == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                            @endforeach
                        </select>
                        @error('supplier_id')<small class="text-danger">{{ $message }}</small>@enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label small font-weight-600 mb-1">Purchase Date <span class="text-danger">*</span></label>
                        <input type="date" name="purchase_date" value="{{ old('purchase_date', date('Y-m-d')) }}" class="form-control form-control-sm @error('purchase_date') is-invalid @enderror" required>
                        @error('purchase_date')<small class="text-danger">{{ $message }}</small>@enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label small font-weight-600 mb-1">Reference No</label>
                        <input type="text" name="reference_no" value="{{ old('reference_no') }}" class="form-control form-control-sm @error('reference_no') is-invalid @enderror" placeholder="Ref #">
                        @error('reference_no')<small class="text-danger">{{ $message }}</small>@enderror
                    </div>
                </div>

                <!-- Row 2: Notes -->
                <div class="row g-2 mb-3">
                    <div class="col-12">
                        <label class="form-label small font-weight-600 mb-1">Notes</label>
                        <textarea name="notes" class="form-control form-control-sm @error('notes') is-invalid @enderror" rows="2" placeholder="Additional notes...">{{ old('notes') }}</textarea>
                        @error('notes')<small class="text-danger">{{ $message }}</small>@enderror
                    </div>
                </div>

                <hr class="my-3">

                <!-- Products Section -->
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h5 class="mb-0 small font-weight-bold">
                        <i class="fas fa-box mr-2"></i>Products
                    </h5>
                    <button type="button" class="btn btn-sm btn-info" id="add_row_btn">
                        <i class="fas fa-plus mr-1"></i>Add Row
                    </button>
                </div>

                <div class="table-responsive">
                    <table class="table table-sm table-bordered mb-0" id="items_table">
                        <thead class="bg-light">
                            <tr>
                                <th class="small">Product</th>
                                <th class="small text-center" style="width: 80px;">Last Price</th>
                                <th class="small text-center" style="width: 70px;">Qty</th>
                                <th class="small text-center" style="width: 80px;">Unit Price</th>
                                <th class="small text-center" style="width: 80px;">Total</th>
                                <th class="small text-center" style="width: 40px;"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="item-row">
                                <td>
                                    <select name="items[0][inventory_item_id]" class="form-control form-control-sm product-select" required>
                                        <option value="">Select Product</option>
                                        @foreach($products as $p)
                                            <option value="{{ $p->id }}"
                                                data-last-price="{{ (float)$p->purchase_price }}"
                                                {{ old('items.0.inventory_item_id') == $p->id ? 'selected' : '' }}>
                                                {{ $p->name }} ({{ $p->category?->name }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('items.0.inventory_item_id')<small class="text-danger d-block">{{ $message }}</small>@enderror
                                </td>
                                <td class="last-price text-muted text-center small">—</td>
                                <td>
                                    <input type="number" min="1" name="items[0][quantity]" class="form-control form-control-sm qty text-center" value="{{ old('items.0.quantity', 1) }}" required>
                                    @error('items.0.quantity')<small class="text-danger d-block">{{ $message }}</small>@enderror
                                </td>
                                <td>
                                    <input type="number" step="0.01" min="0" name="items[0][unit_price]" class="form-control form-control-sm unit-price text-center" value="{{ old('items.0.unit_price', 0) }}" required>
                                    @error('items.0.unit_price')<small class="text-danger d-block">{{ $message }}</small>@enderror
                                </td>
                                <td class="line-total text-center small font-weight-bold">0.00</td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-sm btn-danger remove-row" title="Remove">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                        <tfoot class="bg-light">
                            <tr>
                                <th colspan="4" class="text-right small">Grand Total</th>
                                <th class="text-center small font-weight-bold" id="grand_total">0.00</th>
                                <th></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <div class="card-footer bg-light border-top py-2 px-3">
                <div class="d-flex justify-content-between gap-2">
                    <a href="{{ route('inventory.purchases.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-times mr-1"></i>Cancel
                    </a>
                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="fas fa-save mr-1"></i>Save Purchase
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
        font-size: 0.875rem;
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

    .btn-info {
        background-color: #17a2b8;
        border: none;
        color: white;
    }

    .btn-info:hover {
        background-color: #138496;
    }

    .btn-danger {
        background-color: #dc3545;
        border: none;
    }

    .btn-danger:hover {
        background-color: #c82333;
    }

    .table-sm {
        font-size: 0.875rem;
    }

    .table-sm th,
    .table-sm td {
        padding: 0.4rem;
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

        .table-responsive {
            font-size: 0.8rem;
        }
    }
</style>
@endsection

@section('scripts')
<script>
    function recalcRow($row) {
        const qty = parseFloat($row.find('.qty').val() || 0);
        const price = parseFloat($row.find('.unit-price').val() || 0);
        const total = qty * price;
        $row.find('.line-total').text(total.toFixed(2));
    }

    function recalcGrand() {
        let grand = 0;
        $('#items_table tbody tr').each(function () {
            const t = parseFloat($(this).find('.line-total').text() || 0);
            grand += t;
        });
        $('#grand_total').text(grand.toFixed(2));
    }

    function refreshLastPrice($row) {
        const last = $row.find('.product-select option:selected').data('last-price');
        if (last === undefined || last === '') {
            $row.find('.last-price').text('—');
            return;
        }
        $row.find('.last-price').text(parseFloat(last).toFixed(2));
    }

    function renumberNames() {
        $('#items_table tbody tr').each(function (idx) {
            $(this).find('select.product-select').attr('name', `items[${idx}][inventory_item_id]`);
            $(this).find('input.qty').attr('name', `items[${idx}][quantity]`);
            $(this).find('input.unit-price').attr('name', `items[${idx}][unit_price]`);
        });
    }

    function bindRowEvents($row) {
        $row.on('change', '.product-select', function () {
            refreshLastPrice($row);
        });
        $row.on('input', '.qty, .unit-price', function () {
            recalcRow($row);
            recalcGrand();
        });
        $row.find('.remove-row').on('click', function () {
            if ($('#items_table tbody tr').length <= 1) return;
            $row.remove();
            renumberNames();
            recalcGrand();
        });
        refreshLastPrice($row);
        recalcRow($row);
        recalcGrand();
    }

    $(function () {
        bindRowEvents($('#items_table tbody tr').first());

        $('#add_row_btn').on('click', function () {
            const $first = $('#items_table tbody tr').first();
            const $clone = $first.clone();
            $clone.find('select').val('');
            $clone.find('input.qty').val(1);
            $clone.find('input.unit-price').val(0);
            $clone.find('.last-price').text('—');
            $clone.find('.line-total').text('0.00');
            $('#items_table tbody').append($clone);
            renumberNames();
            bindRowEvents($clone);
        });
    });
</script>
@endsection
