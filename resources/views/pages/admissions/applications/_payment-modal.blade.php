{{-- Admission form-fee payment modal. Used on the applications list and on the application review page. --}}
<div class="modal fade js-admission-payment-modal" id="paymentModal{{ $application->id }}" tabindex="-1" role="dialog"
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

@once
<script>
    // Delegated so it works for every payment modal on the page, including ones rendered after this script.
    (function () {
        var updateTotal = function (discount) {
            var modal = discount.closest('.js-admission-payment-modal');
            if (!modal) return;
            var price = modal.querySelector('.js-payment-price');
            var total = modal.querySelector('.js-payment-total');
            if (!price || !total) return;
            var gross = parseFloat(price.value) || 0;
            var deduction = Math.min(Math.max(parseInt(discount.value, 10) || 0, 0), Math.floor(gross));
            discount.value = deduction;
            total.value = (gross - deduction).toFixed(2);
        };
        ['input', 'change'].forEach(function (type) {
            document.addEventListener(type, function (event) {
                if (event.target.matches && event.target.matches('.js-admission-payment-modal .js-payment-discount')) {
                    updateTotal(event.target);
                }
            });
        });
    })();
</script>
@endonce
