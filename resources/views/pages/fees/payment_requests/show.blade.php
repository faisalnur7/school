@extends('layouts.master')

@section('contents')
<style>
    .payment-request-detail-actions {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: .5rem;
    }

    .payment-request-detail-actions form {
        margin: 0;
    }

    .payment-request-detail-action {
        min-width: 96px;
        height: 42px;
        padding: .45rem .9rem;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 3px;
        font-size: .9rem;
        font-weight: 700;
        line-height: 1;
    }
</style>
<div class="container-fluid py-3">
    <div class="d-flex align-items-center justify-content-between mb-3">
        <div><h3 class="mb-1">{{ __('Payment Request') }}</h3><p class="text-muted mb-0">{{ __('Immutable request submitted from the mobile application.') }}</p></div>
        <a href="{{ route('payment-requests.index') }}" class="btn btn-outline-secondary btn-sm">{{ __('Back') }}</a>
    </div>
    <div class="alert alert-info"><i class="fas fa-lock mr-1"></i>{{ __('Immutable request: employee edits are not allowed. Cancel it to continue with normal collection.') }}</div>
    <div class="card mb-3"><div class="card-body"><div class="row"><div class="col-md-3"><strong>{{ __('Student ID') }}</strong><div>{{ $paymentRequest->student?->student_cid }}</div></div><div class="col-md-3"><strong>{{ __('Student') }}</strong><div>{{ $paymentRequest->student?->full_name_en }}</div></div><div class="col-md-2"><strong>{{ __('Class') }}</strong><div>{{ $paymentRequest->schoolClass?->name_en ?? '—' }}</div></div><div class="col-md-2"><strong>{{ __('Section') }}</strong><div>{{ $paymentRequest->section?->name_en ?? '—' }}</div></div><div class="col-md-2"><strong>{{ __('Expires') }}</strong><div>{{ optional($paymentRequest->expires_at)->format('d M, Y h:i A') }}</div></div></div></div></div>
    <div class="card"><div class="table-responsive"><table class="table mb-0"><thead><tr><th>{{ __('Type') }}</th><th>{{ __('Item') }}</th><th>{{ __('Reference') }}</th><th>{{ __('Quantity') }}</th><th class="text-right">{{ __('Amount') }}</th></tr></thead><tbody>@foreach ($paymentRequest->items as $item)<tr><td>{{ ucfirst($item->item_type) }}</td><td>{{ $item->display_name }}</td><td>{{ $item->display_reference ?? '—' }}</td><td>{{ $item->quantity }}</td><td class="text-right">৳{{ number_format((float) $item->subtotal, 2) }}</td></tr>@endforeach</tbody><tfoot><tr><th colspan="4" class="text-right">{{ __('Total') }}</th><th class="text-right">৳{{ number_format((float) $paymentRequest->total_amount, 2) }}</th></tr></tfoot></table></div><div class="card-footer"><div class="payment-request-detail-actions">@if (auth()->user()?->hasPermission('collect_payment_requests'))<a href="{{ route('payment-requests.collect', $paymentRequest) }}" class="btn btn-success payment-request-detail-action">{{ __('Get Payment') }}</a>@endif @if (auth()->user()?->hasPermission('cancel_payment_requests'))<form method="POST" action="{{ route('payment-requests.cancel', $paymentRequest) }}">@csrf<button type="submit" class="btn btn-outline-danger payment-request-detail-action" onclick="return confirm('{{ __('Cancel this request and continue with normal collection?') }}')">{{ __('Cancel') }}</button></form>@endif</div></div></div>
</div>
@endsection
