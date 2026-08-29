@extends('layouts.master')

@section('contents')
<div class="container-fluid py-3">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
        <div>
            <h2 class="mb-1">Applications</h2>
            <p class="text-muted mb-0">Review applications and collect admission form payments.</p>
        </div>
        <a class="btn btn-outline-primary" href="{{ route('admissions.payments') }}">Admission Payments</a>
    </div>

    <div class="card border-0 shadow-sm mb-3">
        <div class="card-body">
            <form method="GET" action="{{ route('admissions.applications') }}" class="form-row align-items-end">
                <div class="form-group col-lg-7 mb-lg-0">
                    <label for="applicationSearch">Search applications</label>
                    <input id="applicationSearch" type="search" name="search" value="{{ $search }}"
                        class="form-control"
                        placeholder="Student name, father/mother/guardian mobile, or birth certificate number">
                </div>
                <div class="form-group col-lg-3 mb-lg-0">
                    <label for="applicationClass">Class</label>
                    <select id="applicationClass" name="school_class_id" class="form-control">
                        <option value="">All classes</option>
                        @foreach($classes as $class)
                            <option value="{{ $class->id }}" @selected($classId == $class->id)>{{ $class->name_en }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-lg-2 mb-0">
                    <button class="btn btn-primary btn-block" type="submit">
                        <i class="fas fa-search mr-1"></i> Search
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Application</th>
                        <th>Applicant</th>
                        <th>Class</th>
                        <th>Payment Status</th>
                        <th>Result</th>
                        <th class="text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($applications as $application)
                        <tr>
                            <td>
                                <strong>{{ $application->application_number }}</strong><br>
                                <small class="text-muted">{{ $application->exam?->name }}</small>
                            </td>
                            <td>
                                {{ $application->applicant_data['full_name_en'] ?? $application->full_name_en ?? '-' }}<br>
                                <small class="text-muted">
                                    {{ $application->father_phone ?: ($application->mother_phone ?: $application->guardian_phone) }}
                                </small>
                            </td>
                            <td>{{ $application->schoolClass?->name_en }}</td>
                            <td>
                                <span class="badge badge-{{ $application->payment_status === 'paid' ? 'success' : 'warning' }}">
                                    {{ ucfirst(str_replace('_', ' ', $application->payment_status)) }}
                                </span>
                            </td>
                            <td>
                                {{ ucfirst(str_replace('_', ' ', $application->result_status)) }}
                                @if($application->total_marks !== null)
                                    <br><small>{{ $application->total_marks }} / {{ $application->pass_mark_snapshot }}</small>
                                @endif
                            </td>
                            <td class="text-right text-nowrap">
                                @if($application->payment_status === 'paid')
                                    <a class="btn btn-sm btn-outline-success" href="{{ route('admissions.applications.admit-card', $application) }}">
                                        <i class="fas fa-id-card mr-1"></i> Print Admit Card
                                    </a>
                                @else
                                    <button type="button" class="btn btn-sm btn-success" data-toggle="modal"
                                        data-target="#paymentModal{{ $application->id }}">
                                        <i class="fas fa-money-bill-wave mr-1"></i> Get Payment
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">No applications found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer bg-white">{{ $applications->links() }}</div>
    </div>
</div>

@foreach($applications as $application)
    @if($application->payment_status !== 'paid')
        <div class="modal fade" id="paymentModal{{ $application->id }}" tabindex="-1" role="dialog"
            aria-labelledby="paymentModalLabel{{ $application->id }}" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content border-0 shadow">
                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title" id="paymentModalLabel{{ $application->id }}">Get Admission Form Payment</h5>
                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form method="POST" action="{{ route('admissions.applications.collect-payment', $application) }}">
                        @csrf
                        <div class="modal-body">
                            <div class="d-flex justify-content-between border-bottom pb-3 mb-3">
                                <div>
                                    <small class="text-muted d-block">Applicant</small>
                                    <strong>{{ $application->applicant_data['full_name_en'] ?? $application->full_name_en ?? '-' }}</strong>
                                </div>
                                <div class="text-right">
                                    <small class="text-muted d-block">Application</small>
                                    <strong>{{ $application->application_number }}</strong>
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Admission Form Price</label>
                                <div class="input-group">
                                    <div class="input-group-prepend"><span class="input-group-text">৳</span></div>
                                    <input type="text" class="form-control font-weight-bold js-payment-price" value="{{ number_format((float) $application->exam?->form_fee, 2, '.', '') }}" readonly>
                                </div>
                                <small class="form-text text-muted">Discounts reduce the amount recorded under the Admission Form income category.</small>
                            </div>
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label for="paymentDiscount{{ $application->id }}">Discount</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend"><span class="input-group-text">৳</span></div>
                                        <input id="paymentDiscount{{ $application->id }}" type="number" name="discount_amount"
                                            class="form-control js-payment-discount" value="0" min="0" max="{{ (int) floor((float) $application->exam?->form_fee) }}" step="1" inputmode="numeric">
                                    </div>
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="paymentTotal{{ $application->id }}">Total to pay</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend"><span class="input-group-text">৳</span></div>
                                        <input id="paymentTotal{{ $application->id }}" type="text" class="form-control font-weight-bold js-payment-total" value="{{ number_format((float) $application->exam?->form_fee, 2, '.', '') }}" readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="paymentReference{{ $application->id }}">Payment reference</label>
                                <input id="paymentReference{{ $application->id }}" type="text" name="payment_reference"
                                    class="form-control" placeholder="Receipt or transaction number">
                            </div>
                            <div class="form-group mb-0">
                                <label for="paymentRemarks{{ $application->id }}">Remarks</label>
                                <textarea id="paymentRemarks{{ $application->id }}" name="remarks" rows="2"
                                    class="form-control" placeholder="Optional note"></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-light" data-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-check mr-1"></i> Confirm Payment
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
@endforeach

<script>
    document.querySelectorAll('.modal').forEach(function (modal) {
        var price = modal.querySelector('.js-payment-price');
        var discount = modal.querySelector('.js-payment-discount');
        var total = modal.querySelector('.js-payment-total');

        if (!price || !discount || !total) return;

        var updateTotal = function () {
            var gross = parseFloat(price.value) || 0;
            var deduction = Math.min(Math.max(parseInt(discount.value, 10) || 0, 0), Math.floor(gross));
            discount.value = deduction;
            total.value = (gross - deduction).toFixed(2);
        };

        discount.addEventListener('input', updateTotal);
        discount.addEventListener('change', updateTotal);
    });
</script>
@endsection
