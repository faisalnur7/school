@extends('layouts.master')
@section('contents')
<div class="container-fluid">
    @include('partials.report-header')

    <div class="card">
        <div class="card-header shadow p-0 flex justify-between items-center">
            <h3 class="card-title flex text-white pl-3 text-medium">Day Book — {{ $date->format('d/m/Y') }}</h3>
            <div class="flex gap-2 pr-3 py-2 items-center justify-center ml-auto">
                <form method="GET" class="flex gap-2 items-end">
                    <div>
                        <label style="font-size:12px;color:#FFF">Date</label>
                        <input type="text" name="date" datepicker datepicker-format="dd/mm/yyyy"
                               class="form-control form-control-sm" value="{{ request('date', $date->format('d/m/Y')) }}" placeholder="dd/mm/yyyy" autocomplete="off">
                    </div>
                    <button class="btn btn-sm btn-dark" style="margin-top:10px">Go</button>
                </form>
                <a href="{{ route('reports.day-book.pdf', request()->query()) }}" class="btn btn-sm btn-danger" style="margin-top:10px"><i class="fas fa-file-pdf"></i> PDF</a>
            </div>
        </div>
        <div class="card-body px-0 pb-0 pt-0">
            <div class="px-3 pt-3 pb-2 d-flex gap-2">
                <span class="badge" style="background:#fff1f2;color:#e11d48;border:1px solid #fecdd3;font-size:12px;padding:6px 14px">Total Debit: {{ number_format($totalDebit, 2) }}</span>
                <span class="badge" style="background:#f0fdf4;color:#16a34a;border:1px solid #bbf7d0;font-size:12px;padding:6px 14px">Total Credit: {{ number_format($totalCredit, 2) }}</span>
            </div>
            <div class="table-responsive">
                <table class="table table-hover mb-0" style="font-size:13px">
                    <thead style="background:#f8fafc">
                        <tr><th>Date</th><th>Reference</th><th>Description</th><th>Type</th><th>Debit Account</th><th>Credit Account</th><th class="text-right">Amount</th></tr>
                    </thead>
                    <tbody>
                        @forelse($transactions as $txn)
                        <tr>
                            <td style="font-size:11px;color:#64748b">{{ $txn->created_at->format('d/m/Y') }}</td>
                            <td style="font-family:monospace;font-size:11px">{{ $txn->reference_no }}</td>
                            <td>{{ $txn->description ?? '—' }}</td>
                            <td>
                                @php $sc = match($txn->type){ 'income'=>'success','expense'=>'danger','capital'=>'primary','withdrawal'=>'warning',default=>'secondary' }; @endphp
                                <span class="badge badge-{{ $sc }}">{{ ucfirst($txn->type) }}</span>
                            </td>
                            <td style="color:#b91c1c">{{ $txn->debit_account_name }}</td>
                            <td style="color:#15803d">{{ $txn->credit_account_name }}</td>
                            <td class="text-right fw-bold">{{ number_format($txn->amount, 2) }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="6" class="text-center text-muted py-4">No transactions on this date</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
