@extends('layouts.master')

@section('contents')
<div class="col-12">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Create Purchase</h3>
            <div class="card-tools">
                <a href="{{ route('inventory.purchases.index') }}" class="btn btn-secondary btn-sm">
                    <i class="fas fa-list"></i> Back to List
                </a>
            </div>
        </div>
        <form method="POST" action="{{ route('inventory.purchases.store') }}">
            @csrf
            <div class="card-body">
                @if($errors->any())
                    <div class="alert alert-danger">Please fix the errors below.</div>
                @endif

                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Supplier <span class="text-danger">*</span></label>
                            <select name="supplier_id" class="form-control" required>
                                <option value="">Select</option>
                                @foreach($suppliers as $s)
                                    <option value="{{ $s->id }}" {{ old('supplier_id') == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                                @endforeach
                            </select>
                            @error('supplier_id')<small class="text-danger">{{ $message }}</small>@enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Purchase Date <span class="text-danger">*</span></label>
                            <input type="date" name="purchase_date" value="{{ old('purchase_date', date('Y-m-d')) }}" class="form-control" required>
                            @error('purchase_date')<small class="text-danger">{{ $message }}</small>@enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Reference No</label>
                            <input name="reference_no" value="{{ old('reference_no') }}" class="form-control">
                            @error('reference_no')<small class="text-danger">{{ $message }}</small>@enderror
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label>Notes</label>
                    <textarea name="notes" class="form-control" rows="2">{{ old('notes') }}</textarea>
                    @error('notes')<small class="text-danger">{{ $message }}</small>@enderror
                </div>

                <hr>
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h5 class="mb-0">Products</h5>
                    <button type="button" class="btn btn-sm btn-info" id="add_row_btn"><i class="fas fa-plus"></i> Add Row</button>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered" id="items_table">
                        <thead>
                            <tr>
                                <th width="40%">Product</th>
                                <th width="15%">Last Purchase Price</th>
                                <th width="15%">Qty</th>
                                <th width="15%">Unit Price</th>
                                <th width="15%">Line Total</th>
                                <th width="5%"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="item-row">
                                <td>
                                    <select name="items[0][inventory_item_id]" class="form-control product-select" required>
                                        <option value="">Select</option>
                                        @foreach($products as $p)
                                            <option value="{{ $p->id }}"
                                                data-last-price="{{ (float)$p->purchase_price }}"
                                                {{ old('items.0.inventory_item_id') == $p->id ? 'selected' : '' }}>
                                                {{ $p->name }} ({{ $p->category?->name }}) {{ $p->sku ? '- '.$p->sku : '' }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('items.0.inventory_item_id')<small class="text-danger">{{ $message }}</small>@enderror
                                </td>
                                <td class="last-price text-muted">—</td>
                                <td>
                                    <input type="number" min="1" name="items[0][quantity]" class="form-control qty" value="{{ old('items.0.quantity', 1) }}" required>
                                    @error('items.0.quantity')<small class="text-danger">{{ $message }}</small>@enderror
                                </td>
                                <td>
                                    <input type="number" step="0.01" min="0" name="items[0][unit_price]" class="form-control unit-price" value="{{ old('items.0.unit_price', 0) }}" required>
                                    @error('items.0.unit_price')<small class="text-danger">{{ $message }}</small>@enderror
                                </td>
                                <td class="line-total">0.00</td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-sm btn-danger remove-row" title="Remove"><i class="fas fa-times"></i></button>
                                </td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="4" class="text-right">Grand Total</th>
                                <th id="grand_total">0.00</th>
                                <th></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            <div class="card-footer">
                <button class="btn btn-primary">Save Purchase</button>
            </div>
        </form>
    </div>
</div>
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

