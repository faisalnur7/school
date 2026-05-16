@extends('layouts.master')

@section('contents')
<div class="container-fluid">
    <div class="card">
        <div class="card-header text-white rounded-top d-flex justify-content-between align-items-center shadow p-3">
            <h3 class="card-title mb-0 text-white text-lg">Shareholders</h3>
            <a href="{{ route('shareholders.create') }}" class="btn btn-primary btn-sm ml-auto text-bold">
                + Add Shareholder
            </a>
        </div>

        <div class="card-body px-0 pb-4 pt-0">

            @if (session('success'))
                <div class="alert alert-success mx-3 mt-3">{{ session('success') }}</div>
            @endif

            <div class="table-responsive mt-3">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Phone</th>
                            <th>Email</th>
                            <th class="text-right">Capital In</th>
                            <th class="text-right">Withdrawn</th>
                            <th class="text-right">Net Balance</th>
                            <th width="200">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($shareholders as $sh)
                            @php
                                $capital    = $sh->capital_sum ?? 0;
                                $withdrawal = $sh->withdrawal_sum ?? 0;
                                $net        = $capital - $withdrawal;
                            @endphp
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td class="fw-bold">{{ $sh->name }}</td>
                                <td>{{ $sh->phone ?? '—' }}</td>
                                <td>{{ $sh->email ?? '—' }}</td>
                                <td class="text-right" style="color:#16a34a;font-weight:600">
                                    {{ number_format($capital, 2) }}
                                </td>
                                <td class="text-right" style="color:#e11d48;font-weight:600">
                                    {{ number_format($withdrawal, 2) }}
                                </td>
                                <td class="text-right">
                                    <span class="badge"
                                        style="background:{{ $net >= 0 ? '#f0fdf4' : '#fff1f2' }};
                                               color:{{ $net >= 0 ? '#16a34a' : '#e11d48' }};
                                               border:1px solid {{ $net >= 0 ? '#bbf7d0' : '#fecdd3' }};
                                               font-size:12px;padding:5px 10px">
                                        {{ number_format($net, 2) }}
                                    </span>
                                </td>
<td>
    <div class="d-flex align-items-center flex-nowrap" style="gap:8px; white-space: nowrap;">

        {{-- Add Capital --}}
        <button type="button"
            onclick="openTxnModal({{ $sh->id }}, '{{ addslashes($sh->name) }}', 'capital')"
            class="px-3 py-2 text-xs font-semibold text-white bg-green-600 border border-green-600 rounded-lg shadow-sm hover:bg-green-700 transition">

            + Capital
        </button>

        {{-- Withdraw --}}
        <button type="button"
            onclick="openTxnModal({{ $sh->id }}, '{{ addslashes($sh->name) }}', 'withdrawal')"
            class="px-3 py-2 text-xs font-semibold text-white bg-amber-500 border border-amber-500 rounded-lg shadow-sm hover:bg-amber-600 transition">

            − Withdraw
        </button>

        {{-- Edit --}}
        <a href="{{ route('shareholders.edit', $sh->id) }}"
            class="d-flex align-items-center justify-content-center text-gray-700 bg-gray-100 border border-gray-200 rounded-lg shadow-sm hover:bg-gray-200 transition"
            style="width:36px; height:36px;">

            <i class="fas fa-edit text-sm"></i>
        </a>

        {{-- Delete --}}
        <form action="{{ route('shareholders.destroy', $sh->id) }}"
            method="POST"
            class="m-0 p-0 d-inline"
            onsubmit="return confirm('Delete this shareholder?')">

            @csrf
            @method('DELETE')

            <button type="submit"
                class="d-flex align-items-center justify-content-center text-white bg-red-500 border border-red-500 rounded-lg shadow-sm hover:bg-red-600 transition"
                style="width:36px; height:36px;">

                <i class="fas fa-trash text-sm"></i>
            </button>

        </form>

    </div>
</td>
                            </tr>
                        @endforeach

                        @if ($shareholders->isEmpty())
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">No shareholders found</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>

            <div class="px-3 pt-3">
                {{ $shareholders->links() }}
            </div>
        </div>
    </div>
</div>

{{-- ── Capital / Withdrawal Modal ─────────────────────────────── --}}
<div class="modal fade" id="txnModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header card-header" id="txnModalHeader">
                <h5 class="modal-title" id="txnModalTitle">Add Transaction</h5>
                <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <form method="POST" action="{{ route('shareholder-transactions.store') }}">
                @csrf
                <input type="hidden" name="shareholder_id" id="modalShareholderId">
                <input type="hidden" name="type"           id="modalType">
                <input type="hidden" name="account_type"   id="modalAccountType">

                <div class="modal-body">

                    <div class="form-group">
                        <label class="font-weight-bold">Shareholder</label>
                        <input type="text" id="modalShareholderName" class="form-control" disabled>
                    </div>

                    <div class="form-group">
                        <label>Amount <span class="text-danger">*</span></label>
                        <input type="number" name="amount" step="0.01" min="0.01"
                               class="form-control" required placeholder="0.00">
                    </div>

                    <div class="form-group">
                        <label>Date <span class="text-danger">*</span></label>
                        <input type="text" name="transaction_date" datepicker datepicker-format="dd/mm/yyyy"
                               class="form-control"
                               value="{{ now()->format('d/m/Y') }}"
                               placeholder="dd/mm/yyyy" autocomplete="off" required>
                    </div>

                    <div class="form-group">
                        <label>Payment Method <span class="text-danger">*</span></label>
                        <select name="payment_method" id="modalPaymentMethod" class="form-control" required>
                            @foreach (['Cash', 'Bank Transfer', 'Cheque', 'Mobile Banking', 'Other'] as $method)
                                <option value="{{ $method }}">{{ $method }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group" id="modalAccountWrapper" style="display:none">
                        <label>Account</label>
                        <select name="account_id" id="modalAccountSelect" class="form-control">
                            <option value="">Select Account</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Description</label>
                        <textarea name="description" class="form-control" rows="2"
                                  id="modalDescription" placeholder="e.g. Initial investment, Monthly drawing..."></textarea>
                    </div>

                </div>
                <div class="modal-footer card-footer bg-light border-top py-2 px-3">
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-sm" id="modalSubmitBtn">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
const accountsUrl = '{{ route('accounts.index') }}';

const methodTypeMap = {
    'Cash':           'hand_cash',
    'Bank Transfer':  'bank',
    'Mobile Banking': 'mobile',
};
const accountTypeMap = {
    'Cash':           'App\\Models\\HandCash',
    'Bank Transfer':  'App\\Models\\BankAccount',
    'Mobile Banking': 'App\\Models\\MobileBankingAccount',
};

function loadModalAccounts(method) {
    const type        = methodTypeMap[method];
    const accountType = accountTypeMap[method];
    const $wrapper    = $('#modalAccountWrapper');
    const $select     = $('#modalAccountSelect');

    if (!type) {
        $wrapper.hide();
        $('#modalAccountType').val('');
        $select.html('<option value="">Select Account</option>');
        return;
    }

    $('#modalAccountType').val(accountType);

    $.ajax({
        url: accountsUrl, method: 'GET', dataType: 'json', data: { type: type },
        success: function (accounts) {
            $select.html('<option value="">Select Account</option>');
            accounts.forEach(a => $select.append(`<option value="${a.id}">${a.label}</option>`));
            $wrapper.toggle(accounts.length > 0);
        },
        error: function () { $wrapper.hide(); }
    });
}

function openTxnModal(shareholderId, shareholderName, type) {
    $('#modalShareholderId').val(shareholderId);
    $('#modalShareholderName').val(shareholderName);
    $('#modalType').val(type);

    const isCapital = type === 'capital';
    $('#txnModalTitle').text((isCapital ? '+ Add Capital' : '− Withdraw') + ' — ' + shareholderName);
    $('#txnModalHeader').css('background', isCapital ? '#16a34a' : '#e11d48').addClass('text-white');
    $('#modalSubmitBtn').removeClass('btn-success btn-danger').addClass(isCapital ? 'btn-success' : 'btn-danger')
                        .text(isCapital ? 'Add Capital' : 'Withdraw');
    $('#modalDescription').attr('placeholder', isCapital ? 'e.g. Initial investment, Q1 contribution...' : 'e.g. Monthly drawing, Personal withdrawal...');

    // reset amount & description
    $('input[name="amount"]').val('');
    $('#modalDescription').val('');

    // load accounts for current method
    loadModalAccounts($('#modalPaymentMethod').val());

    $('#txnModal').modal('show');
}

$('#modalPaymentMethod').on('change', function () {
    loadModalAccounts($(this).val());
});
</script>
@endsection
