@extends('layouts.master')

@section('contents')
    <div class="container-fluid py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="text-bold text-lg">🛒 New Asset Purchase</h4>
            <a href="{{ route('asset-purchases.index') }}" class="btn btn-secondary">← Back</a>
        </div>

        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <form action="{{ route('asset-purchases.store') }}" method="POST">
            @csrf
            <div class="row">
                <!-- Left: Cart -->
                <div class="col-md-8">
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header text-white fw-bold">Cart Items</div>
                        <div class="card-body">
                            <table class="table table-bordered" id="cartTable">
                                <thead style="background:#f8fafc">
                                    <tr>
                                        <th>Asset</th>
                                        <th width="100">Qty</th>
                                        <th width="130">Unit Price (৳)</th>
                                        <th width="130">Total (৳)</th>
                                        <th width="50"></th>
                                    </tr>
                                </thead>
                                <tbody id="cartBody">
                                    <tr id="emptyRow">
                                        <td colspan="5" class="text-center text-muted py-3">No items added yet</td>
                                    </tr>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="3" class="text-end fw-bold">Grand Total:</td>
                                        <td class="fw-bold" style="color:#4338ca" id="grandTotal">৳0.00</td>
                                        <td></td>
                                    </tr>
                                </tfoot>
                            </table>

                            <!-- Add Item Row -->
                            <div class="row g-2 mt-2">
                                <div class="col-md-5">
                                    <select id="assetSelect" class="form-control">
                                        <option value="">Select Asset</option>
                                        @foreach ($assets as $asset)
                                            <option value="{{ $asset->id }}" data-name="{{ $asset->name }}">
                                                {{ $asset->name }} ({{ $asset->category->name ?? '' }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <input type="number" id="qtyInput" class="form-control" placeholder="Qty"
                                        min="1" value="1">
                                </div>
                                <div class="col-md-3">
                                    <input type="number" id="priceInput" class="form-control" placeholder="Unit Price"
                                        min="0" step="0.01">
                                </div>
                                <div class="col-md-2">
                                    <button type="button" class="btn btn-success w-100" onclick="addToCart()">+
                                        Add</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right: Payment -->
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header text-white fw-bold">Payment Details</div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label">Purchase Date *</label>
                                <input type="date" name="purchase_date" class="form-control" value="{{ date('Y-m-d') }}"
                                    required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Payment Type *</label>
                                <select name="payment_type" id="paymentType" class="form-control" required>
                                    <option value="">Select Type</option>
                                    <option value="hand_cash">Hand Cash</option>
                                    <option value="bank">Bank</option>
                                    <option value="mobile">Mobile Banking</option>
                                </select>
                            </div>

                            <div class="mb-3" id="accountWrapper" style="display:none">
                                <label class="form-label">Account *</label>
                                <select name="account_id" id="accountSelect" class="form-control">
                                    <option value="">Select Account</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Notes</label>
                                <textarea name="notes" class="form-control" rows="3"></textarea>
                            </div>

                            <button type="submit" class="btn btn-primary w-100" id="submitBtn" disabled>
                                💾 Save Purchase
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Hidden cart inputs rendered by JS -->
            <div id="hiddenInputs"></div>
        </form>
    </div>
@endsection

@section('scripts')
    <script>
        let cart = [];

        function addToCart() {
            const $assetSelect = $('#assetSelect');
            const assetId = $assetSelect.val();
            const assetName = $assetSelect.find(':selected').data('name');
            const qty = parseInt($('#qtyInput').val(), 10);
            const price = parseFloat($('#priceInput').val());

            if (!assetId || !qty || !price) {
                alert('Please select asset, quantity and unit price.');
                return;
            }

            const existing = cart.find(i => i.asset_id === assetId);
            if (existing) {
                existing.quantity += qty;
                existing.total = existing.quantity * existing.unit_price;
            } else {
                cart.push({
                    asset_id: assetId,
                    name: assetName,
                    quantity: qty,
                    unit_price: price,
                    total: qty * price
                });
            }

            renderCart();
            $assetSelect.val('');
            $('#qtyInput').val(1);
            $('#priceInput').val('');
        }

        function removeFromCart(index) {
            cart.splice(index, 1);
            renderCart();
        }

        function renderCart() {
            const $tbody = $('#cartBody');
            const $hiddenInputs = $('#hiddenInputs');

            if (cart.length === 0) {
                $tbody.html('<tr id="emptyRow"><td colspan="5" class="text-center text-muted py-3">No items added yet</td></tr>');
                $hiddenInputs.empty();
                $('#grandTotal').text('৳0.00');
                $('#submitBtn').prop('disabled', true);
                return;
            }

            let rows = '';
            let inputs = '';
            let grand = 0;

            cart.forEach((item, i) => {
                grand += item.total;
                rows += `<tr>
                <td>${item.name}</td>
                <td>${item.quantity}</td>
                <td>৳${item.unit_price.toFixed(2)}</td>
                <td>৳${item.total.toFixed(2)}</td>
                <td><button type="button" class="btn btn-sm btn-danger" onclick="removeFromCart(${i})">✕</button></td>
            </tr>`;
                inputs += `<input type="hidden" name="items[${i}][asset_id]" value="${item.asset_id}">
                       <input type="hidden" name="items[${i}][quantity]" value="${item.quantity}">
                       <input type="hidden" name="items[${i}][unit_price]" value="${item.unit_price}">`;
            });

            $tbody.html(rows);
            $hiddenInputs.html(inputs);
            $('#grandTotal').text('৳' + grand.toFixed(2));
            $('#submitBtn').prop('disabled', false);
        }

        // Load accounts on payment type change
        $('#paymentType').on('change', function() {
            const type = $(this).val();
            const $wrapper = $('#accountWrapper');
            const $select = $('#accountSelect');

            if (!type) {
                $wrapper.hide();
                $select.html('<option value="">Select Account</option>');
                return;
            }

            $.ajax({
                url: '{{ route('asset-purchases.accounts') }}',
                method: 'GET',
                dataType: 'json',
                data: { type: type },
                success: function(accounts) {
                    $select.html('<option value="">Select Account</option>');
                    accounts.forEach(a => {
                        $select.append(`<option value="${a.id}">${a.label}</option>`);
                    });
                    $wrapper.show();
                },
                error: function(xhr, status, error) {
                    console.error('Account load failed:', status, error);
                    alert('Failed to load accounts. Please try again.');
                    $wrapper.hide();
                }
            });
        });
    </script>
@endsection
