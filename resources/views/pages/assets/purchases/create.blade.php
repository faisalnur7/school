@extends('layouts.master')

@section('contents')
    <div class="container-fluid px-3 py-3">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-gradient-primary text-white py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0 font-weight-bold text-white">
                        <i class="fas fa-plus-circle mr-2"></i>New Asset Purchase
                    </h4>
                    <a href="{{ route('asset-purchases.index') }}" class="btn btn-light btn-sm">
                        <i class="fas fa-arrow-left mr-1"></i> Back
                    </a>
                </div>
            </div>

            <form method="POST" action="{{ route('asset-purchases.store') }}" id="assetPurchaseForm">
                @csrf

                <div class="card-body p-3">
                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show border-0 mb-3" role="alert">
                            <i class="fas fa-exclamation-circle mr-2"></i>
                            <strong>There are validation errors.</strong>
                            <ul class="mb-0 mt-1 ml-4">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Purchase Date <span class="text-danger">*</span></label>
                            <input type="date" name="purchase_date"
                                class="form-control @error('purchase_date') is-invalid @enderror"
                                value="{{ old('purchase_date', now()->format('Y-m-d')) }}" required>
                            @error('purchase_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label">Payment Type <span class="text-danger">*</span></label>
                            <select name="payment_type" id="payment_type"
                                class="form-control @error('payment_type') is-invalid @enderror" required>
                                <option value="">Select payment type</option>
                                <option value="hand_cash" {{ old('payment_type') === 'hand_cash' ? 'selected' : '' }}>Cash
                                </option>
                                <option value="bank" {{ old('payment_type') === 'bank' ? 'selected' : '' }}>Bank</option>
                                <option value="mobile" {{ old('payment_type') === 'mobile' ? 'selected' : '' }}>Mobile
                                    Banking</option>
                            </select>
                            @error('payment_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label">Account <span class="text-danger">*</span></label>
                            <select name="account_id" id="account_id"
                                class="form-control @error('account_id') is-invalid @enderror" required>
                                <option value="">Select account</option>
                            </select>
                            @error('account_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Notes</label>
                        <textarea name="notes" class="form-control @error('notes') is-invalid @enderror" rows="3">{{ old('notes') }}</textarea>
                        @error('notes')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-lg-8 mb-3">
                            <div class="card h-100">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h5 class="mb-0">Purchase Cart</h5>
                                    <button type="button" class="btn btn-sm btn-success" id="add_item_btn">
                                        <i class="fas fa-plus mr-1"></i>Add Manual Row
                                    </button>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-bordered table-sm mb-0" id="items_table">
                                        <thead class="thead-light">
                                            <tr>
                                                <th style="width:45%;">Asset <span class="text-danger">*</span></th>
                                                <th style="width:15%;" class="text-center">Quantity</th>
                                                <th style="width:20%;" class="text-center">Unit Price</th>
                                                <th style="width:15%;" class="text-center">Total</th>
                                                <th style="width:5%;"></th>
                                            </tr>
                                        </thead>
                                        <tbody id="items_body">
                                            @php
                                                $oldItems = old('items', [
                                                    ['asset_id' => '', 'quantity' => 1, 'unit_price' => ''],
                                                ]);
                                            @endphp
                                            @foreach ($oldItems as $index => $item)
                                                <tr class="item-row">
                                                    <td>
                                                        <select name="items[{{ $index }}][asset_id]"
                                                            class="form-control asset-select @error('items.' . $index . '.asset_id') is-invalid @enderror"
                                                            required>
                                                            <option value="">Select asset</option>
                                                            @foreach ($assets as $asset)
                                                                <option value="{{ $asset->id }}"
                                                                    data-price="{{ $asset->purchase_price }}"
                                                                    {{ old('items.' . $index . '.asset_id', $item['asset_id']) == $asset->id ? 'selected' : '' }}>
                                                                    {{ $asset->name }}@if ($asset->category)
                                                                        ({{ $asset->category->name }})
                                                                    @endif
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                        @error('items.' . $index . '.asset_id')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </td>
                                                    <td>
                                                        <input type="number" min="1"
                                                            name="items[{{ $index }}][quantity]"
                                                            class="form-control text-center qty @error('items.' . $index . '.quantity') is-invalid @enderror"
                                                            value="{{ old('items.' . $index . '.quantity', $item['quantity']) }}"
                                                            required>
                                                        @error('items.' . $index . '.quantity')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </td>
                                                    <td>
                                                        <input type="number" min="0" step="0.01"
                                                            name="items[{{ $index }}][unit_price]"
                                                            class="form-control text-center unit-price @error('items.' . $index . '.unit_price') is-invalid @enderror"
                                                            value="{{ old('items.' . $index . '.unit_price', $item['unit_price']) }}"
                                                            required>
                                                        @error('items.' . $index . '.unit_price')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </td>
                                                    <td class="text-center align-middle">
                                                        <span class="line-total">0.00</span>
                                                    </td>
                                                    <td class="text-center align-middle">
                                                        <button type="button" class="btn btn-sm btn-danger remove-row"
                                                            title="Remove">
                                                            <i class="fas fa-times"></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <td colspan="3" class="text-right font-weight-bold">Grand Total</td>
                                                <td class="text-center font-weight-bold" id="grand_total">0.00</td>
                                                <td></td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 mb-3">
                            <div class="card h-100">
                                <div class="card-header">
                                    <h5 class="mb-0">Assets</h5>
                                </div>
                                <div class="card-body p-2">
                                    <div class="mb-2">
                                        <input type="text" class="form-control form-control-sm" id="asset_search"
                                            placeholder="Search asset...">
                                    </div>
                                    <div id="asset_list" style="max-height: 420px; overflow-y: auto;">
                                        @foreach ($assets as $asset)
                                            <div class="border rounded p-2 mb-2 asset-card"
                                                data-label="{{ strtolower($asset->name . ' ' . ($asset->category->name ?? '')) }}">
                                                <div class="d-flex justify-content-between align-items-start">
                                                    <div>
                                                        <div class="font-weight-bold">{{ $asset->name }}</div>
                                                        <small class="text-muted">
                                                            {{ $asset->category->name ?? 'No Category' }}
                                                        </small>
                                                    </div>
                                                    <button type="button" class="btn btn-sm btn-primary add-asset-btn"
                                                        data-id="{{ $asset->id }}"
                                                        data-price="{{ $asset->purchase_price ?? 0 }}">
                                                        Add
                                                    </button>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-footer bg-light border-top py-2 px-3">
                    <div class="d-flex justify-content-between gap-2">
                        <a href="{{ route('asset-purchases.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-times mr-1"></i>Cancel
                        </a>
                        <button type="submit" class="btn btn-primary btn-sm">
                            <i class="fas fa-save mr-1"></i>Create Purchase
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
        @php
            $assetOptions = $assets
                ->map(function ($asset) {
                    return [
                        'id' => $asset->id,
                        'label' => $asset->name . ($asset->category ? ' (' . $asset->category->name . ')' : ''),
                        'price' => (float) ($asset->purchase_price ?? 0),
                    ];
                })
                ->values();
        @endphp
        const ASSETS = @json($assetOptions);

        const INITIAL_PAYMENT_TYPE = @json(old('payment_type', 'hand_cash'));
        const INITIAL_ACCOUNT_ID = @json(old('account_id'));

        $(function() {
            const $itemsBody = $('#items_body');

            function formatCurrency(value) {
                return parseFloat(value || 0).toFixed(2);
            }

            function updateRowTotal($row) {
                const qty = parseFloat($row.find('.qty').val() || 0);
                const unitPrice = parseFloat($row.find('.unit-price').val() || 0);
                const total = qty * unitPrice;
                $row.find('.line-total').text(formatCurrency(total));
            }

            function updateGrandTotal() {
                let sum = 0;
                $itemsBody.find('tr').each(function() {
                    sum += parseFloat($(this).find('.line-total').text() || 0);
                });
                $('#grand_total').text(formatCurrency(sum));
            }

            function renumberRows() {
                $itemsBody.find('tr').each(function(index) {
                    $(this).find('.asset-select').attr('name', `items[${index}][asset_id]`);
                    $(this).find('.qty').attr('name', `items[${index}][quantity]`);
                    $(this).find('.unit-price').attr('name', `items[${index}][unit_price]`);
                });
            }

            function bindRowEvents($row) {
                $row.on('change', '.asset-select', function() {
                    const price = parseFloat($(this).find('option:selected').data('price') || 0);
                    if (!isNaN(price)) {
                        $(this).closest('tr').find('.unit-price').val(formatCurrency(price));
                    }
                    updateRowTotal($row);
                    updateGrandTotal();
                });

                $row.on('input', '.qty, .unit-price', function() {
                    updateRowTotal($row);
                    updateGrandTotal();
                });

                $row.find('.remove-row').on('click', function() {
                    $row.remove();
                    renumberRows();
                    updateGrandTotal();
                });
            }

            function buildAssetOptions(selectedId) {
                return ASSETS.map(function(asset) {
                    return `<option value="${asset.id}" data-price="${asset.price}" ${asset.id == selectedId ? 'selected' : ''}>${asset.label}</option>`;
                }).join('');
            }

            function addRow(item = {}) {
                const selectedId = item.asset_id || '';
                const quantity = item.quantity || 1;
                const unitPrice = item.unit_price || 0;
                const $row = $(
                    `<tr class="item-row">
                    <td>
                        <select name="" class="form-control asset-select" required>
                            <option value="">Select asset</option>
                            ${buildAssetOptions(selectedId)}
                        </select>
                    </td>
                    <td>
                        <input type="number" min="1" class="form-control text-center qty" value="${quantity}" required>
                    </td>
                    <td>
                        <input type="number" min="0" step="0.01" class="form-control text-center unit-price" value="${formatCurrency(unitPrice)}" required>
                    </td>
                    <td class="text-center align-middle"><span class="line-total">0.00</span></td>
                    <td class="text-center align-middle">
                        <button type="button" class="btn btn-sm btn-danger remove-row" title="Remove">
                            <i class="fas fa-times"></i>
                        </button>
                    </td>
                </tr>`
                );

                $itemsBody.append($row);
                bindRowEvents($row);
                renumberRows();
                updateRowTotal($row);
                updateGrandTotal();
            }

            function findRowByAssetId(assetId) {
                return $itemsBody.find('.asset-select').filter(function() {
                    return String($(this).val()) === String(assetId);
                }).closest('tr');
            }

            function addAssetToCart(assetId, assetPrice) {
                const $existingRow = findRowByAssetId(assetId);
                if ($existingRow.length) {
                    const $qty = $existingRow.find('.qty');
                    $qty.val(parseInt($qty.val() || 0, 10) + 1).trigger('input');
                    return;
                }

                addRow({
                    asset_id: String(assetId),
                    quantity: 1,
                    unit_price: assetPrice || 0
                });
            }

            $('#add_item_btn').on('click', function() {
                addRow();
            });

            $('.add-asset-btn').on('click', function() {
                addAssetToCart($(this).data('id'), $(this).data('price'));
            });

            $('#asset_search').on('input', function() {
                const query = ($(this).val() || '').toLowerCase().trim();
                $('.asset-card').each(function() {
                    const label = $(this).data('label') || '';
                    $(this).toggle(label.includes(query));
                });
            });

            $itemsBody.find('tr').each(function() {
                const $row = $(this);
                bindRowEvents($row);
                updateRowTotal($row);
            });

            updateGrandTotal();

            function loadAccounts(paymentType) {
                const $account = $('#account_id');
                $account.html('<option value="">Loading accounts...</option>');

                if (!paymentType) {
                    $account.html('<option value="">Select payment type first</option>');
                    return;
                }

                $.get('{{ route('asset-purchases.accounts') }}', {
                        type: paymentType
                    })
                    .done(function(accounts) {
                        let html = '<option value="">Select account</option>';
                        if (accounts.length === 0) {
                            html = '<option value="">No active accounts found</option>';
                        }
                        accounts.forEach(function(account) {
                            html +=
                                `<option value="${account.id}" ${account.id == INITIAL_ACCOUNT_ID ? 'selected' : ''}>${account.label}</option>`;
                        });
                        $account.html(html);
                    })
                    .fail(function() {
                        $account.html('<option value="">Unable to load accounts</option>');
                    });
            }

            $('#payment_type').on('change', function() {
                loadAccounts($(this).val());
            });

            loadAccounts(INITIAL_PAYMENT_TYPE);

            if ($('.is-invalid').length > 0) {
                $('html, body').animate({
                    scrollTop: $('.is-invalid').first().offset().top - 50
                }, 300);
            }
        });
    </script>
@endsection
