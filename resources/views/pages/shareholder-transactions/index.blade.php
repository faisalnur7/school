@extends('layouts.master')

@section('contents')
<div class="container-fluid">
    <div class="card">
        <div class="card-header text-white rounded-top d-flex justify-content-between align-items-center shadow p-3">
            <h3 class="card-title mb-0 text-white text-lg">{{ request('type') === 'capital' ? 'Capital Transactions' : 'All Transactions' }}</h3>
            <a href="{{ route('shareholder-transactions.create') }}" class="btn btn-primary btn-sm ml-auto text-bold">
                + Add Capital
            </a>
        </div>

        <div class="card-body px-0 pb-4 pt-0">

            @if (session('success'))
                <div class="alert alert-success mx-3 mt-3">{{ session('success') }}</div>
            @endif

            {{-- Filters --}}
            <form method="GET" action="{{ route('shareholder-transactions.index') }}" class="px-3 pt-3 pb-2">
                <div class="row g-2 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label mb-1" style="font-size:12px">Type</label>
                        @if(request('type') === 'capital')
                            <input type="hidden" name="type" value="capital">
                            <input class="form-control form-control-sm" value="Capital" disabled>
                        @else
                            <select name="type" class="form-control form-control-sm">
                                <option value="">All Types</option>
                                <option value="income" {{ request('type') === 'income' ? 'selected' : '' }}>Income</option>
                                <option value="expense" {{ request('type') === 'expense' ? 'selected' : '' }}>Expense</option>
                                <option value="capital" {{ request('type') === 'capital' ? 'selected' : '' }}>Capital</option>
                                <option value="withdrawal" {{ request('type') === 'withdrawal' ? 'selected' : '' }}>Withdrawal</option>
                            </select>
                        @endif
                    </div>
                    <div class="col-md-3">
                        <label class="form-label mb-1" style="font-size:12px">Shareholder</label>
                        <select name="shareholder_id" class="form-control form-control-sm">
                            <option value="">All Shareholders</option>
                            @foreach ($shareholders as $sh)
                                <option value="{{ $sh->id }}" {{ request('shareholder_id') == $sh->id ? 'selected' : '' }}>
                                    {{ $sh->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="col-md-3 d-flex gap-2">
                        <button type="submit" class="btn btn-sm btn-dark">Filter</button>
                        <a href="{{ route('shareholder-transactions.index') }}" class="btn btn-sm btn-secondary">Reset</a>
                    </div>
                </div>
            </form>

            {{-- Summary badges --}}
            <div class="px-3 pb-2 d-flex gap-2 flex-wrap">
                <span class="badge" style="background:#dcfce7;color:#166534;border:1px solid #bbf7d0;font-size:12px;padding:6px 12px">
                    Income: {{ number_format($totalIncome, 2) }}
                </span>
                <span class="badge" style="background:#fef2f2;color:#991b1b;border:1px solid #fecaca;font-size:12px;padding:6px 12px">
                    Expense: {{ number_format($totalExpense, 2) }}
                </span>
                <span class="badge" style="background:#f0fdf4;color:#166534;border:1px solid #bbf7d0;font-size:12px;padding:6px 12px">
                    Capital: {{ number_format($totalCapital, 2) }}
                </span>
                <span class="badge" style="background:#fff1f2;color:#991b1b;border:1px solid #fecaca;font-size:12px;padding:6px 12px">
                    Withdrawal: {{ number_format($totalWithdrawal, 2) }}
                </span>
                <span class="badge" style="background:#f1f5f9;color:#334155;border:1px solid #e2e8f0;font-size:12px;padding:6px 12px">
                    Net: {{ number_format(($totalIncome + $totalCapital) - ($totalExpense + $totalWithdrawal), 2) }}
                </span>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Date</th>
                            <th>Type</th>
                            <th>Source</th>
                            <th>Category / Shareholder</th>
                            <th>Amount</th>
                            <th>Method</th>
                            <th>Description</th>
                            <th>Recorded By</th>
                            <th width="100">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($transactions as $txn)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td style="font-size:13px">{{ optional($txn->transaction_date)->format('d/m/Y') ?: '—' }}</td>
                                <td>
                                    <span class="badge" style="background:{{ in_array($txn->type,['income','capital']) ? '#dcfce7' : '#fee2e2' }};color:{{ in_array($txn->type,['income','capital']) ? '#166534' : '#991b1b' }};border:1px solid {{ in_array($txn->type,['income','capital']) ? '#bbf7d0' : '#fecaca' }};font-size:11px">
                                        {{ ucfirst($txn->type) }}
                                    </span>
                                </td>
                                <td>
                                    @if($txn->type === 'income')
                                        {{ $txn->incomeCategory->name ?? '—' }}
                                    @elseif($txn->type === 'expense')
                                        {{ $txn->expenseCategory->name ?? '—' }}
                                    @else
                                        {{ $txn->shareholder->name ?? '—' }}
                                    @endif
                                </td>
                                <td>
                                    @if($txn->transactionable)
                                        {{ class_basename($txn->transactionable_type) }} #{{ $txn->transactionable_id }}
                                    @else
                                        —
                                    @endif
                                </td>
                                <td class="fw-bold" style="color:{{ in_array($txn->type,['income','capital']) ? '#16a34a' : '#e11d48' }}">
                                    {{ number_format($txn->amount, 2) }}
                                </td>
                                <td>
                                    <span class="badge" style="background:#fff7ed;color:#c2410c;border:1px solid #fed7aa;font-size:11px">
                                        {{ $txn->payment_method ?? '—' }}
                                    </span>
                                </td>
                                <td>{{ $txn->description ?? '—' }}</td>
                                <td>{{ $txn->recorder->name ?? '—' }}</td>
                                <td style="display:flex;gap:5px; justify-content: center; align-items: center;;align-items:center">
                                    <a href="{{ route('shareholder-transactions.voucher', $txn->id) }}" class="btn btn-sm btn-secondary" title="Print Voucher" target="_blank">
                                        <i class="fas fa-print"></i>
                                    </a>
                                    <a href="{{ route('shareholder-transactions.edit', $txn->id) }}" class="btn btn-sm btn-dark">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('shareholder-transactions.destroy', $txn->id) }}" method="POST"
                                          class="btn btn-sm btn-danger d-inline m-0 p-0"
                                          onsubmit="return confirm('Delete this transaction?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach

                        @if ($transactions->isEmpty())
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">No transactions found</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>

            <div class="px-3 pt-3">
                {{ $transactions->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
