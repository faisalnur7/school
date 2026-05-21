@extends('layouts.master')
@section('contents')
<div class="container-fluid">
    @include('partials.report-header')

    <div class="card">
        <div class="card-header shadow p-0 flex justify-between items-center">
            <h3 class="card-title flex text-white pl-3 text-medium">Headwise Transaction List</h3>
            <div class="flex gap-2 pr-3 py-2 items-center justify-center ml-auto">
                <form method="GET" class="flex gap-2 items-end">
                    <div>
                        <label style="font-size:12px;color:#FFF">From</label>
                        <input type="text" name="from" datepicker datepicker-format="dd/mm/yyyy"
                               class="form-control form-control-sm" value="{{ request('from', $from->format('d/m/Y')) }}" autocomplete="off">
                    </div>
                    <div>
                        <label style="font-size:12px;color:#FFF">To</label>
                        <input type="text" name="to" datepicker datepicker-format="dd/mm/yyyy"
                               class="form-control form-control-sm" value="{{ request('to', $to->format('d/m/Y')) }}" autocomplete="off">
                    </div>
                    <button class="btn btn-sm btn-dark" style="margin-top:10px">Filter</button>
                </form>
                <a href="{{ route('reports.headwise-transactions.pdf', request()->query()) }}" class="btn btn-sm btn-danger" style="margin-top:10px"><i class="fas fa-file-pdf"></i> PDF</a>
            </div>
        </div>
        <div class="card-body p-0">

            {{-- Income Heads --}}
            <div class="px-3 py-2" style="background:#f0fdf4;border-bottom:1px solid #bbf7d0">
                <strong style="color:#16a34a;font-size:13px">Income Heads</strong>
            </div>

            @forelse($incomeHeads as $head)
            <div class="px-3 py-2 d-flex justify-content-between align-items-center"
                 style="background:#f8fafc;border-bottom:1px solid #e2e8f0;cursor:pointer"
                 onclick="toggleHead('income-{{ $head->id }}')">
                <span class="fw-bold" style="font-size:13px">{{ $head->name }}</span>
                <span style="color:#16a34a;font-weight:700">{{ number_format($head->total, 2) }}</span>
            </div>
            <div id="income-{{ $head->id }}" style="display:none">
                <table class="table table-sm mb-0" style="font-size:12px">
                    <thead style="background:#f8fafc">
                        <tr>
                            <th style="padding-left:32px">Date</th>
                            <th>Reference</th>
                            <th>Description</th>
                            <th>Method</th>
                            <th class="text-right">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($head->transactions as $txn)
                        <tr>
                            <td style="padding-left:32px;color:#64748b">{{ $txn->transaction_date->format('d/m/Y') }}</td>
                            <td style="font-family:monospace;font-size:11px">{{ $txn->reference_no }}</td>
                            <td>{{ $txn->description ?? '—' }}</td>
                            <td>{{ $txn->payment_method }}</td>
                            <td class="text-right" style="color:#16a34a">{{ number_format($txn->amount, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @empty
            <div class="px-4 py-3 text-muted" style="font-size:13px">No income transactions in this period</div>
            @endforelse

            @if($incomeHeads->count())
            <div class="px-3 py-2 d-flex justify-content-between" style="background:#dcfce7;border-bottom:2px solid #bbf7d0">
                <strong style="color:#16a34a">Total Income</strong>
                <strong style="color:#16a34a">{{ number_format($incomeHeads->sum('total'), 2) }}</strong>
            </div>
            @endif

            {{-- Expense Heads --}}
            <div class="px-3 py-2 mt-2" style="background:#fff1f2;border-bottom:1px solid #fecdd3">
                <strong style="color:#e11d48;font-size:13px">Expense Heads</strong>
            </div>

            @forelse($expenseHeads as $head)
            <div class="px-3 py-2 d-flex justify-content-between align-items-center"
                 style="background:#f8fafc;border-bottom:1px solid #e2e8f0;cursor:pointer"
                 onclick="toggleHead('expense-{{ $head->id }}')">
                <span class="fw-bold" style="font-size:13px">{{ $head->name }}</span>
                <span style="color:#e11d48;font-weight:700">{{ number_format($head->total, 2) }}</span>
            </div>
            <div id="expense-{{ $head->id }}" style="display:none">
                <table class="table table-sm mb-0" style="font-size:12px">
                    <thead style="background:#f8fafc">
                        <tr>
                            <th style="padding-left:32px">Date</th>
                            <th>Reference</th>
                            <th>Description</th>
                            <th>Method</th>
                            <th class="text-right">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($head->transactions as $txn)
                        <tr>
                            <td style="padding-left:32px;color:#64748b">{{ $txn->transaction_date->format('d/m/Y') }}</td>
                            <td style="font-family:monospace;font-size:11px">{{ $txn->reference_no }}</td>
                            <td>{{ $txn->description ?? '—' }}</td>
                            <td>{{ $txn->payment_method }}</td>
                            <td class="text-right" style="color:#e11d48">{{ number_format($txn->amount, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @empty
            <div class="px-4 py-3 text-muted" style="font-size:13px">No expense transactions in this period</div>
            @endforelse

            @if($expenseHeads->count())
            <div class="px-3 py-2 d-flex justify-content-between" style="background:#fee2e2;border-bottom:2px solid #fecdd3">
                <strong style="color:#e11d48">Total Expenses</strong>
                <strong style="color:#e11d48">{{ number_format($expenseHeads->sum('total'), 2) }}</strong>
            </div>
            @endif

            {{-- Net --}}
            @php $net = $incomeHeads->sum('total') - $expenseHeads->sum('total'); @endphp
            <div class="px-3 py-3 d-flex justify-content-between" style="background:#f1f5f9;border-top:2px solid #e2e8f0">
                <strong>Net Surplus / (Deficit)</strong>
                <strong style="font-size:15px;color:{{ $net >= 0 ? '#16a34a' : '#e11d48' }}">
                    {{ $net >= 0 ? number_format($net, 2) : '(' . number_format(abs($net), 2) . ')' }}
                </strong>
            </div>

        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
function toggleHead(id) {
    const el = document.getElementById(id);
    el.style.display = el.style.display === 'none' ? 'table' : 'none';
}
</script>
@endsection
