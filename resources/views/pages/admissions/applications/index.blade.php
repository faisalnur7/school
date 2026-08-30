@extends('layouts.master')

@section('contents')
<style>
    .admissions-applications-page { color: #172033; }
    .admissions-applications-page .applications-hero {
        background: linear-gradient(120deg, #10233d 0%, #155e75 62%, #0f766e 100%);
        border-radius: 16px;
        color: #fff;
        overflow: hidden;
        position: relative;
    }
    .admissions-applications-page .applications-hero::after {
        background: rgba(255,255,255,.08);
        border-radius: 50%;
        content: '';
        height: 220px;
        position: absolute;
        right: -50px;
        top: -130px;
        width: 220px;
    }
    .admissions-applications-page .applications-hero > * { position: relative; z-index: 1; }
    .admissions-applications-page .stat-card { border: 0; border-radius: 14px; box-shadow: 0 6px 20px rgba(23,32,51,.07); }
    .admissions-applications-page .stat-label { color: #718096; font-size: .72rem; font-weight: 700; letter-spacing: .06em; text-transform: uppercase; }
    .admissions-applications-page .stat-value { color: #172033; font-size: 1.65rem; font-weight: 800; line-height: 1.1; }
    .admissions-applications-page .filter-card, .admissions-applications-page .table-card { border: 1px solid #e5eaf1; border-radius: 14px; box-shadow: 0 6px 20px rgba(23,32,51,.05); }
    .admissions-applications-page .filter-card label { color: #536176; font-size: .78rem; font-weight: 700; }
    .admissions-applications-page .filter-card .form-control { border-color: #d9e1eb; border-radius: 9px; min-height: 42px; }
    .admissions-applications-page .table { margin-bottom: 0; }
    .admissions-applications-page .table thead th { background: #f7f9fc; border-bottom: 1px solid #e5eaf1; color: #667085; font-size: .7rem; letter-spacing: .05em; text-transform: uppercase; white-space: nowrap; }
    .admissions-applications-page .table tbody td { border-top: 1px solid #edf1f5; padding: 14px 12px; vertical-align: middle; }
    .admissions-applications-page .application-number { color: #0f6170; font-weight: 800; }
    .admissions-applications-page .applicant-cell { align-items: center; display: flex; gap: 10px; min-width: 190px; }
    .admissions-applications-page .applicant-avatar { align-items: center; background: #dff7f2; border-radius: 11px; color: #0f766e; display: inline-flex; flex: 0 0 36px; font-weight: 800; height: 36px; justify-content: center; overflow: hidden; width: 36px; }
    .admissions-applications-page .applicant-avatar img { height: 100%; object-fit: cover; width: 100%; }
    .admissions-applications-page .meta { color: #8490a3; display: block; font-size: .72rem; margin-top: 3px; }
    .admissions-applications-page .status-badge { border-radius: 999px; display: inline-block; font-size: .7rem; font-weight: 700; padding: 5px 9px; white-space: nowrap; }
    .admissions-applications-page .status-paid { background: #dcfce7; color: #166534; }
    .admissions-applications-page .status-unpaid { background: #fef3c7; color: #92400e; }
    .admissions-applications-page .status-result { background: #eef2ff; color: #4338ca; }
    .admissions-applications-page .status-pending { background: #f1f5f9; color: #64748b; }
    .admissions-applications-page .action-cell { min-width: 155px; }
    .admissions-applications-page .empty-state { padding: 56px 20px; }
</style>

@php
    $pageApplications = $applications->getCollection();
    $pagePaid = $pageApplications->where('payment_status', 'paid')->count();
    $pageUnpaid = $pageApplications->where('payment_status', '!=', 'paid')->count();
    $pageReviewed = $pageApplications->whereIn('result_status', ['passed', 'failed'])->count();
@endphp

<div class="container-fluid py-3 admissions-applications-page">
    <div class="applications-hero d-flex flex-wrap justify-content-between align-items-center mb-3 p-4">
        <div>
            <div class="small font-weight-bold text-uppercase" style="letter-spacing:.14em;opacity:.72;">Admission management</div>
            <h2 class="mb-1 mt-1 text-white">Application desk</h2>
            <p class="mb-0" style="opacity:.78;">Track applicants, payments, results, and admit-card readiness from one view.</p>
        </div>
        <a class="btn btn-light mt-3 mt-md-0" href="{{ route('admissions.payments') }}"><i class="fas fa-receipt mr-1"></i> Payment register</a>
    </div>

    <div class="row mb-3">
        <div class="col-sm-6 col-xl-3 mb-3 mb-xl-0"><div class="card stat-card h-100"><div class="card-body"><span class="stat-label">Total results</span><div class="d-flex justify-content-between align-items-end mt-2"><span class="stat-value">{{ number_format($applications->total()) }}</span><i class="fas fa-users text-info"></i></div></div></div></div>
        <div class="col-sm-6 col-xl-3 mb-3 mb-xl-0"><div class="card stat-card h-100"><div class="card-body"><span class="stat-label">Paid on page</span><div class="d-flex justify-content-between align-items-end mt-2"><span class="stat-value text-success">{{ $pagePaid }}</span><i class="fas fa-check-circle text-success"></i></div></div></div></div>
        <div class="col-sm-6 col-xl-3 mb-3 mb-sm-0"><div class="card stat-card h-100"><div class="card-body"><span class="stat-label">Payment pending</span><div class="d-flex justify-content-between align-items-end mt-2"><span class="stat-value text-warning">{{ $pageUnpaid }}</span><i class="fas fa-clock text-warning"></i></div></div></div></div>
        <div class="col-sm-6 col-xl-3"><div class="card stat-card h-100"><div class="card-body"><span class="stat-label">Results entered</span><div class="d-flex justify-content-between align-items-end mt-2"><span class="stat-value text-primary">{{ $pageReviewed }}</span><i class="fas fa-chart-line text-primary"></i></div></div></div></div>
    </div>

    <div class="card filter-card mb-3">
        <div class="card-body pb-2">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div><h5 class="mb-1">Find an application</h5><small class="text-muted">Search by applicant identity or narrow the list by class.</small></div>
                @if($search !== '' || $classId)
                    <a href="{{ route('admissions.applications') }}" class="small font-weight-bold text-secondary">Clear filters</a>
                @endif
            </div>
            <form method="GET" action="{{ route('admissions.applications') }}" class="form-row align-items-end">
                <div class="form-group col-lg-7 mb-3 mb-lg-0">
                    <label for="applicationSearch">Search applications</label>
                    <input id="applicationSearch" type="search" name="search" value="{{ $search }}"
                        class="form-control"
                        placeholder="Student name, father/mother/guardian mobile, or birth certificate number">
                </div>
                <div class="form-group col-lg-3 mb-3 mb-lg-0">
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
                        <i class="fas fa-filter mr-1"></i> Apply filters
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="card table-card">
        <div class="card-header bg-white border-0 d-flex flex-wrap justify-content-between align-items-center pt-3">
            <div><h5 class="mb-1">Submitted applications</h5><small class="text-muted">{{ $applications->firstItem() ?? 0 }}-{{ $applications->lastItem() ?? 0 }} of {{ $applications->total() }} applications</small></div>
            <span class="small text-muted"><i class="fas fa-info-circle mr-1"></i>Use Get Payment before printing an admit card</span>
        </div>
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
                                <span class="application-number">{{ $application->application_number }}</span>
                                <small class="meta">{{ $application->exam?->name }}</small>
                            </td>
                            <td>
                                @php
                                    $applicantName = $application->applicant_data['full_name_en'] ?? $application->full_name_en ?? '-';
                                    $applicantImage = $application->image ?? ($application->applicant_data['image'] ?? null);
                                    $applicantPhone = $application->father_phone ?: ($application->mother_phone ?: $application->guardian_phone);
                                @endphp
                                <div class="applicant-cell">
                                    <span class="applicant-avatar">@if($applicantImage)<img src="{{ asset($applicantImage) }}" alt="">@else{{ strtoupper(substr($applicantName, 0, 1)) }}@endif</span>
                                    <span><strong>{{ $applicantName }}</strong><small class="meta">{{ $applicantPhone ?: 'No phone recorded' }}</small></span>
                                </div>
                            </td>
                            <td><span class="badge badge-light px-2 py-2">{{ $application->schoolClass?->name_en ?? 'Unassigned' }}</span></td>
                            <td>
                                <span class="status-badge {{ $application->payment_status === 'paid' ? 'status-paid' : 'status-unpaid' }}">
                                    {{ ucfirst(str_replace('_', ' ', $application->payment_status)) }}
                                </span>
                            </td>
                            <td>
                                <span class="status-badge {{ in_array($application->result_status, ['passed', 'failed']) ? 'status-result' : 'status-pending' }}">
                                    {{ ucfirst(str_replace('_', ' ', $application->result_status)) }}
                                </span>
                                @if($application->total_marks !== null)
                                    <small class="meta">{{ $application->total_marks }} / {{ $application->pass_mark_snapshot }}</small>
                                @endif
                            </td>
                            <td class="text-right text-nowrap action-cell">
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
