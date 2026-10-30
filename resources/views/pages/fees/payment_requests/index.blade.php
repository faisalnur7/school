@extends('layouts.master')

@section('contents')
<style>
    .payment-request-actions {
        display: inline-flex;
        align-items: center;
        gap: .35rem;
    }

    .payment-request-actions form {
        display: inline-flex;
        margin: 0;
    }

    .payment-request-action {
        width: 34px;
        height: 34px;
        padding: 0;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 3px;
    }
</style>
<div class="container-fluid py-3">
    <div class="d-flex align-items-center justify-content-between mb-3">
        <div>
            <h3 class="mb-1">{{ __('Payment Requests') }}</h3>
            <p class="text-muted mb-0">{{ __('Mobile payment requests expire after 24 hours.') }}</p>
        </div>
        <a href="{{ route('fees.hub') }}" class="btn btn-outline-secondary btn-sm">{{ __('Fees Hub') }}</a>
    </div>

    <form method="GET" class="card card-body mb-3">
        <div class="row align-items-end">
            <div class="col-md-3 mb-2">
                <label for="student_id">{{ __('Student ID') }}</label>
                <input id="student_id" name="student_id" value="{{ request('student_id') }}" class="form-control form-control-sm" placeholder="{{ __('Student ID') }}">
            </div>
            <div class="col-md-3 mb-2">
                <label for="academic_session_id">{{ __('Academic Session') }}</label>
                <select id="academic_session_id" name="academic_session_id" class="form-control form-control-sm">
                    <option value="">{{ __('All sessions') }}</option>
                    @foreach ($sessions as $session)
                        <option value="{{ $session->id }}" @selected((string) request('academic_session_id') === (string) $session->id)>{{ $session->name_en }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3 mb-2">
                <label for="school_class_id">{{ __('Class') }}</label>
                <select id="school_class_id" name="school_class_id" class="form-control form-control-sm">
                    <option value="">{{ __('All classes') }}</option>
                    @foreach ($classes as $class)
                        <option value="{{ $class->id }}" @selected((string) request('school_class_id') === (string) $class->id)>{{ $class->name_en }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3 mb-2">
                <label for="section_id">{{ __('Section') }}</label>
                <select id="section_id" name="section_id" class="form-control form-control-sm">
                    <option value="">{{ __('All sections') }}</option>
                    @foreach ($sections as $section)
                        <option value="{{ $section->id }}" @selected((string) request('section_id') === (string) $section->id)>{{ $section->name_en }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-12 d-flex gap-2">
                <button class="btn btn-primary btn-sm" type="submit"><i class="fas fa-filter mr-1"></i>{{ __('Filter') }}</button>
                <a class="btn btn-outline-secondary btn-sm" href="{{ route('payment-requests.index') }}">{{ __('Reset') }}</a>
            </div>
        </div>
    </form>

    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead><tr><th>{{ __('Student ID') }}</th><th>{{ __('Student') }}</th><th>{{ __('Academic Session') }}</th><th>{{ __('Class') }}</th><th>{{ __('Section') }}</th><th>{{ __('Total') }}</th><th>{{ __('Expires') }}</th><th class="text-right">{{ __('Actions') }}</th></tr></thead>
                <tbody>
                @forelse ($requests as $paymentRequest)
                    <tr>
                        <td>{{ $paymentRequest->student?->student_cid }}</td>
                        <td>{{ $paymentRequest->student?->full_name_en }}</td>
                        <td>{{ $paymentRequest->academicSession?->name_en ?? '—' }}</td>
                        <td>{{ $paymentRequest->schoolClass?->name_en ?? '—' }}</td>
                        <td>{{ $paymentRequest->section?->name_en ?? '—' }}</td>
                        <td>৳{{ number_format((float) $paymentRequest->total_amount, 2) }}</td>
                        <td>{{ optional($paymentRequest->expires_at)->format('d M, Y h:i A') }}</td>
                        <td class="text-right text-nowrap">
                            <div class="payment-request-actions">
                                @if (auth()->user()?->hasPermission('view_payment_requests'))
                                    <a href="{{ route('payment-requests.show', $paymentRequest) }}" class="btn btn-sm btn-outline-primary payment-request-action" data-toggle="tooltip" data-placement="top" title="{{ __('View') }}" aria-label="{{ __('View') }}">
                                        <i class="fas fa-eye" aria-hidden="true"></i>
                                    </a>
                                @endif
                                @if (auth()->user()?->hasPermission('collect_payment_requests'))
                                    <a href="{{ route('payment-requests.collect', $paymentRequest) }}" class="btn btn-sm btn-success payment-request-action" data-toggle="tooltip" data-placement="top" title="{{ __('Get Payment') }}" aria-label="{{ __('Get Payment') }}">
                                        <i class="fas fa-money-bill-wave" aria-hidden="true"></i>
                                    </a>
                                @endif
                                @if (auth()->user()?->hasPermission('cancel_payment_requests'))
                                    <form method="POST" action="{{ route('payment-requests.cancel', $paymentRequest) }}">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-danger payment-request-action" data-toggle="tooltip" data-placement="top" title="{{ __('Cancel') }}" aria-label="{{ __('Cancel') }}" onclick="return confirm('{{ __('Cancel this request and continue with normal collection?') }}')">
                                            <i class="fas fa-times" aria-hidden="true"></i>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="text-center text-muted py-4">{{ __('No active payment requests found.') }}</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        @if ($requests->hasPages())<div class="card-footer">{{ $requests->links() }}</div>@endif
    </div>
</div>
@endsection
