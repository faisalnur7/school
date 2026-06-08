@extends('layouts.master')

@section('contents')
<div class="container-fluid py-4 px-4">

    {{-- ── Student Banner ── --}}
    <div class="student-header-card mb-4">
        <div class="card-inner">
            <a href="{{ url()->previous() }}" class="back-btn">
                <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                    <path d="M10 3L5 8L10 13" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"
                        stroke-linejoin="round" />
                </svg>
                Back
            </a>
            <div class="header-divider"></div>
            <div class="student-identity">
                <div class="avatar-ring">
                    <span class="avatar-initials">{{ strtoupper(substr($payment->student->full_name_en, 0, 2)) }}</span>
                </div>
                <div>
                    <p class="label-tag">STUDENT</p>
                    <h5 class="student-name">{{ $payment->student->full_name_en }}</h5>
                </div>
            </div>
            @php $info = $payment->student->academicInformations->last(); @endphp
            <div class="meta-chips ms-auto">
                <div class="chip">
                    <span class="chip-label">RECEIPT NO</span>
                    <span class="chip-value">{{ $payment->receipt_no }}</span>
                </div>
                <div class="chip">
                    <span class="chip-label">DATE</span>
                    <span class="chip-value">{{ \Carbon\Carbon::parse($payment->payment_date)->format('d M Y') }}</span>
                </div>
                <div class="chip">
                    <span class="chip-label">CLASS</span>
                    <span class="chip-value">{{ $info->schoolClass->name_en ?? '—' }}</span>
                </div>
                <div class="chip">
                    <span class="chip-label">SECTION</span>
                    <span class="chip-value">{{ $info->section->name_en ?? '—' }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        {{-- LEFT: Payment Items ── --}}
        <div class="col-md-8">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white border-bottom py-3 px-4" style="border-color:#f1f5f9!important">
                    <div class="d-flex align-items-center gap-2">
                        <span class="fs-5">📋</span>
                        <span class="fw-bold text-white" style="font-size:12px;letter-spacing:.06em">PAYMENT ITEMS</span>
                        <span class="badge rounded-pill ms-auto" style="background:#eef2ff;color:#4338ca;border:1px solid #c7d2fe">
                            {{ $payment->items->count() }} item(s)
                        </span>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div id="paymentItemsList">
                        @if ($payment->inventorySale && $payment->inventorySale->items->isNotEmpty())
                        @foreach ($payment->inventorySale->items as $saleItem)
                        <div class="payment-item-row px-4 py-3 border-bottom" style="border-color:#f1f5f9">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="flex-grow-1">
                                    <div class="mb-2">
                                        <h6 class="fw-bold mb-1" style="font-size:14px">
                                            {{ $saleItem->inventoryItem->name ?? 'Inventory Item' }}
                                        </h6>
                                        <small class="text-muted">
                                            {{ $saleItem->inventoryItem->category->name ?? 'Uncategorized' }}
                                        </small>
                                    </div>

                                    <div class="ms-2" style="font-size:12px;max-width:220px">
                                        <div class="d-flex justify-content-between text-muted mb-1 align-items-center">
                                            <label class="mb-0" style="font-size:12px">Qty</label>
                                            <input type="number" min="0" step="1" name="sale_items[{{ $saleItem->id }}][quantity]" 
                                                value="{{ $saleItem->quantity }}" class="form-control form-control-sm" style="width:90px;border-color:#e2e8f0">
                                        </div>
                                        <div class="d-flex justify-content-between text-muted mb-1 align-items-center">
                                            <label class="mb-0" style="font-size:12px">Unit Price</label>
                                            <input type="number" min="0" step="0.01" name="sale_items[{{ $saleItem->id }}][unit_price]" 
                                                value="{{ number_format($saleItem->unit_price, 2, '.', '') }}" class="form-control form-control-sm" style="width:90px;border-color:#e2e8f0">
                                        </div>
                                    </div>
                                </div>

                                <div class="text-end ms-3">
                                    <div class="mb-3">
                                        <span class="mono fw-bold" style="font-size:16px;color:#4338ca">
                                            {{ number_format($saleItem->subtotal, 2) }}
                                        </span>
                                        <span class="text-muted mono" style="font-size:11px">BDT</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                        @elseif ($payment->items->isNotEmpty())
                        @forelse ($payment->items as $item)
                        <div class="payment-item-row px-4 py-3 border-bottom" style="border-color:#f1f5f9" data-item-id="{{ $item->id }}">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="flex-grow-1">
                                    <div class="mb-2">
                                        <h6 class="fw-bold mb-1" style="font-size:14px">
                                            {{ $item->fee->feeSet->name ?? 'Fee' }}
                                        </h6>
                                        <small class="text-muted">
                                            {{ \Carbon\Carbon::parse($item->fee->due_date)->format('F - Y') }}
                                        </small>
                                    </div>

                                    {{-- Fee Items Breakdown ── --}}
                                    <div class="ms-2" style="font-size:12px">
                                        @foreach ($item->fee->feeSet->items as $feeItem)
                                        <div class="d-flex justify-content-between text-muted mb-1">
                                            <span>• {{ $feeItem->category->name }}</span>
                                            <span class="mono">{{ number_format($feeItem->amount, 2) }}</span>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>

                                <div class="text-end ms-3">
                                    <div class="mb-3" style="max-width:160px">
                                        <div class="input-group input-group-sm">
                                            <span class="input-group-text" style="background:#fff;border-color:#e2e8f0">BDT</span>
                                            <input type="number" step="0.01" name="items[{{ $item->id }}][amount]" class="form-control form-control-sm" 
                                                value="{{ number_format($item->amount, 2, '.', '') }}" required style="border-color:#e2e8f0">
                                        </div>
                                    </div>
                                    <button type="button" class="btn btn-sm btn-outline-danger remove-item-btn rounded-2"
                                        data-item-id="{{ $item->id }}" style="font-size:12px">
                                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                                            <polyline points="3 6 5 6 21 6"></polyline>
                                            <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                            <line x1="10" y1="11" x2="10" y2="17"></line>
                                            <line x1="14" y1="11" x2="14" y2="17"></line>
                                        </svg>
                                    </button>
                                </div>
                            </div>

                            {{-- Fee Status Info ── --}}
                            <div class="mt-2 pt-2 border-top" style="border-color:#f1f5f9">
                                <small class="text-muted d-flex justify-content-between">
                                    <span>Fee Total: {{ number_format($item->fee->amount, 2) }} BDT</span>
                                    <span>Already Paid: {{ number_format($item->fee->paid_amount, 2) }} BDT</span>
                                    <span>Remaining: {{ number_format(max(0, $item->fee->amount - $item->fee->paid_amount), 2) }} BDT</span>
                                </small>
                            </div>
                        </div>
                        @empty
                        <div class="text-center text-muted py-4">
                            <p>No payment items</p>
                        </div>
                        @endforelse
                        @else
                        <div class="text-center text-muted py-4">
                            <p>No payment items</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- RIGHT: Edit Form ── --}}
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white border-bottom py-3 px-4" style="border-color:#f1f5f9!important">
                    <div class="d-flex align-items-center gap-2">
                        <span class="fs-5">✏️</span>
                        <span class="fw-bold text-white" style="font-size:12px;letter-spacing:.06em">EDIT PAYMENT</span>
                    </div>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('payments.update', $payment) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="amount" class="form-label fw-semibold">Payment Amount</label>
                            <div class="input-group">
                                <span class="input-group-text" style="background:#f1f5f9;border-color:#e2e8f0">BDT</span>
                                <input type="number" step="0.01" class="form-control" id="amount" name="amount" 
                                    value="{{ $payment->amount }}" required style="border-color:#e2e8f0">
                            </div>
                            <small class="text-muted d-block mt-1">
                                Original: {{ number_format($payment->amount, 2) }} BDT
                            </small>
                        </div>

                        <div class="mb-3">
                            <label for="payment_date" class="form-label fw-semibold">Payment Date</label>
                            <input type="date" class="form-control" id="payment_date" name="payment_date" 
                                value="{{ \Carbon\Carbon::parse($payment->payment_date)->format('Y-m-d') }}" 
                                required style="border-color:#e2e8f0">
                        </div>

                        <div class="mb-3">
                            <label for="payment_method" class="form-label fw-semibold">Payment Method</label>
                            <select class="form-control" id="payment_method" name="payment_method" required style="border-color:#e2e8f0">
                                <option value="Cash" {{ $payment->payment_method == 'Cash' ? 'selected' : '' }}>Cash</option>
                                <option value="Bank Transfer" {{ $payment->payment_method == 'Bank Transfer' ? 'selected' : '' }}>Bank Transfer</option>
                                <option value="Cheque" {{ $payment->payment_method == 'Cheque' ? 'selected' : '' }}>Cheque</option>
                                <option value="Mobile Banking" {{ $payment->payment_method == 'Mobile Banking' ? 'selected' : '' }}>Mobile Banking</option>
                                <option value="Other" {{ $payment->payment_method == 'Other' ? 'selected' : '' }}>Other</option>
                            </select>
                        </div>

                        <div class="mb-4">
                            <label for="description" class="form-label fw-semibold">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="3" 
                                style="border-color:#e2e8f0">{{ $payment->description }}</textarea>
                        </div>

                        {{-- Summary ── --}}
                        <div class="summary-box p-3 rounded-3 mb-4" style="background:#f8fafc;border:1px solid #e2e8f0">
                            <div class="d-flex justify-content-between mb-2" style="font-size:13px">
                                <span class="text-muted">Gross Amount:</span>
                                <span class="mono fw-semibold">{{ number_format($payment->gross_amount, 2) }} BDT</span>
                            </div>
                            @if ($payment->scholarship_amount > 0)
                            <div class="d-flex justify-content-between mb-2" style="font-size:13px;color:#059669">
                                <span class="text-muted">Scholarship:</span>
                                <span class="mono">-{{ number_format($payment->scholarship_amount, 2) }} BDT</span>
                            </div>
                            @endif
                            @if ($payment->discount_amount > 0)
                            <div class="d-flex justify-content-between mb-2" style="font-size:13px;color:#b45309">
                                <span class="text-muted">Discount:</span>
                                <span class="mono">-{{ number_format($payment->discount_amount, 2) }} BDT</span>
                            </div>
                            @endif
                        </div>

                        <button type="submit" class="btn w-100 fw-bold text-white rounded-3 py-2 mb-2"
                            style="background:linear-gradient(135deg,#6366f1,#4338ca);font-size:14px">
                            ✓ Update Payment
                        </button>
                        <a href="{{ route('fees.collect_payment', $payment->student_id) }}" class="btn btn-outline-secondary w-100 fw-bold rounded-3 py-2"
                            style="font-size:14px">
                            Cancel
                        </a>
                    </form>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(function() {
        // Handle remove payment item
        $(document).on('click', '.remove-item-btn', function() {
            const $btn = $(this);
            const itemId = $btn.data('item-id');
            const $row = $btn.closest('.payment-item-row');

            Swal.fire({
                icon: 'warning',
                title: 'Remove Payment Item?',
                text: 'This will remove the payment from this fee and recalculate the amounts.',
                confirmButtonColor: '#dc3545',
                confirmButtonText: 'Yes, Remove',
                cancelButtonColor: '#6c757d',
                showCancelButton: true,
            }).then((result) => {
                if (!result.isConfirmed) return;

                $btn.prop('disabled', true).html('⏳ Removing...');

                $.ajax({
                    url: '/payment-items/' + itemId,
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': $('input[name="_token"]').val()
                    },
                    dataType: 'json',
                    success: function(res) {
                        // Remove the row with animation
                        $row.fadeOut(300, function() {
                            $(this).remove();

                            // Update badge count
                            const count = $('#paymentItemsList .payment-item-row').length;
                            $('.badge').text(count + ' item(s)');

                            // If no items left, show empty message
                            if (count === 0) {
                                $('#paymentItemsList').html(
                                    '<div class="text-center text-muted py-4"><p>No payment items</p></div>'
                                );
                            }

                            // Show success message
                            Swal.fire({
                                icon: 'success',
                                title: 'Item Removed',
                                text: res.message,
                                confirmButtonColor: '#4338ca',
                                timer: 2000
                            });

                            // Refresh the page to update the payment amount
                            setTimeout(() => {
                                location.reload();
                            }, 500);
                        });
                    },
                    error: function(xhr) {
                        const msg = xhr.responseJSON?.message || 'Failed to remove item';
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: msg,
                            confirmButtonColor: '#4338ca'
                        });
                        $btn.prop('disabled', false).html(
                            '<svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">' +
                            '<polyline points="3 6 5 6 21 6"></polyline>' +
                            '<path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>' +
                            '<line x1="10" y1="11" x2="10" y2="17"></line>' +
                            '<line x1="14" y1="11" x2="14" y2="17"></line>' +
                            '</svg> Remove'
                        );
                    }
                });
            });
        });
    });
</script>
@endsection