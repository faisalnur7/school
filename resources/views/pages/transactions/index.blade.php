@extends('layouts.master')
@section('contents')
<div class="container-fluid">
    <div class="card">

        {{-- Header --}}
        <div class="card-header shadow p-0 flex justify-between items-center">
            <h3 class="card-title flex text-white pl-3 text-medium">Transaction Report</h3>
            <div class="flex gap-2 pr-3 py-2 items-end ml-auto">
                <a href="{{ route('transactions.pdf', request()->query()) }}" class="btn btn-sm btn-danger" style="margin-top:10px">
                    <i class="fas fa-file-pdf"></i> PDF
                </a>
            </div>
        </div>

        <div class="card-body px-0 pb-0 pt-0">

            @if(session('success'))
                <div class="alert alert-success mx-3 mt-3">{{ session('success') }}</div>
            @endif

            {{-- Filters --}}
            <form method="GET" action="{{ route('transactions.index') }}" class="px-3 pt-3 pb-2">
                <div class="row g-2 align-items-end flex-nowrap overflow-auto">
                    <div class="col" style="min-width:220px">
                        <label class="form-label mb-1" style="font-size:12px">Search</label>
                        <input type="text" name="search" class="form-control form-control-sm"
                               value="{{ request('search') }}" placeholder="Ref / description...">
                    </div>
                    <div class="col" style="min-width:150px">
                        <label class="form-label mb-1" style="font-size:12px">Type</label>
                        <select name="type" class="form-control form-control-sm">
                            <option value="">All Types</option>
                            @foreach(['income','expense','capital','withdrawal'] as $t)
                                <option value="{{ $t }}" {{ request('type') === $t ? 'selected' : '' }}>{{ ucfirst($t) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col" style="min-width:150px">
                        <label class="form-label mb-1" style="font-size:12px">View Type</label>
                        <select name="view_type" class="form-control form-control-sm">
                            <option value="detailed" {{ request('view_type', 'detailed') === 'detailed' ? 'selected' : '' }}>Detailed</option>
                            <option value="summary" {{ request('view_type') === 'summary' ? 'selected' : '' }}>Summary</option>
                        </select>
                    </div>
                    <div class="col" style="min-width:170px">
                        <label class="form-label mb-1" style="font-size:12px">Shareholder</label>
                        <select name="shareholder_id" class="form-control form-control-sm">
                            <option value="">All</option>
                            @foreach($shareholders as $sh)
                                <option value="{{ $sh->id }}" {{ request('shareholder_id') == $sh->id ? 'selected' : '' }}>{{ $sh->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col" style="min-width:180px">
                        <label class="form-label mb-1" style="font-size:12px">Category</label>
                        <select name="category_id" class="form-control form-control-sm">
                            <option value="">All Categories</option>
                            <optgroup label="Income">
                                @foreach($incomeCategories as $c)
                                    <option value="{{ $c->id }}" {{ request('category_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                                @endforeach
                            </optgroup>
                            <optgroup label="Expense">
                                @foreach($expenseCategories as $c)
                                    <option value="{{ $c->id }}" {{ request('category_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                                @endforeach
                            </optgroup>
                        </select>
                    </div>
                    <div class="col" style="min-width:140px">
                        <label class="form-label mb-1" style="font-size:12px">From</label>
                        <input type="text" name="from" datepicker datepicker-format="dd/mm/yyyy"
                               class="form-control form-control-sm" value="{{ request('from') }}" placeholder="dd/mm/yyyy" autocomplete="off">
                    </div>
                    <div class="col" style="min-width:140px">
                        <label class="form-label mb-1" style="font-size:12px">To</label>
                        <input type="text" name="to" datepicker datepicker-format="dd/mm/yyyy"
                               class="form-control form-control-sm" value="{{ request('to') }}" placeholder="dd/mm/yyyy" autocomplete="off">
                    </div>
                    <div class="col-auto d-flex gap-2 align-items-end">
                        <button type="submit" class="btn btn-sm btn-dark" title="Filter" aria-label="Filter">
                            <i class="fas fa-search"></i>
                        </button>
                        <a href="{{ route('transactions.index') }}" class="btn btn-sm btn-secondary" title="Reset" aria-label="Reset">
                            <i class="fas fa-undo-alt"></i>
                        </a>
                    </div>
                </div>
            </form>

            {{-- Summary Badges --}}
            <div class="px-3 pb-3 d-flex flex-wrap gap-2">
                @php $net = ($totalIncome + $totalCapital) - ($totalExpense + $totalWithdrawal); @endphp
                <span class="badge" style="background:#f0fdf4;color:#16a34a;border:1px solid #bbf7d0;font-size:12px;padding:6px 14px">
                    Income: {{ number_format($totalIncome, 2) }}
                </span>
                <span class="badge" style="background:#fff1f2;color:#e11d48;border:1px solid #fecdd3;font-size:12px;padding:6px 14px">
                    Expense: {{ number_format($totalExpense, 2) }}
                </span>
                <span class="badge" style="background:#eff6ff;color:#2563eb;border:1px solid #bfdbfe;font-size:12px;padding:6px 14px">
                    Capital: {{ number_format($totalCapital, 2) }}
                </span>
                <span class="badge" style="background:#fefce8;color:#ca8a04;border:1px solid #fde68a;font-size:12px;padding:6px 14px">
                    Withdrawal: {{ number_format($totalWithdrawal, 2) }}
                </span>
                <span class="badge" style="background:#f1f5f9;color:#334155;border:1px solid #e2e8f0;font-size:12px;padding:6px 14px">
                    Net: <strong style="color:{{ $net >= 0 ? '#16a34a' : '#e11d48' }}">{{ number_format($net, 2) }}</strong>
                </span>
            </div>

            {{-- Table --}}
            <div class="table-responsive">
                @php $showDescription = $viewType === 'detailed'; @endphp
                <table class="table table-hover align-middle mb-0" style="font-size:13px">
                    <thead style="background:#f8fafc">
                        <tr>
                            <th>#</th>
                            <th>Date</th>
                            <th>Reference</th>
                            <th>Type</th>
                            <th>Category / Shareholder</th>
                            @if($showDescription)
                                <th style="width:220px; max-width:220px;">Description</th>
                            @endif
                            <th>Method</th>
                            <th class="text-right">Debit</th>
                            <th class="text-right">Credit</th>
                            <th>Recorded By</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $rowNumber = $transactions->firstItem() ? $transactions->firstItem() - 1 : 0; @endphp
                        @forelse($transactionGroups as $group)
                            @php
                                $badgeColor = match($group['type']) {
                                    'income'     => 'background:#f0fdf4;color:#16a34a;border:1px solid #bbf7d0',
                                    'expense'    => 'background:#fff1f2;color:#e11d48;border:1px solid #fecdd3',
                                    'capital'    => 'background:#eff6ff;color:#2563eb;border:1px solid #bfdbfe',
                                    'withdrawal' => 'background:#fefce8;color:#ca8a04;border:1px solid #fde68a',
                                    default      => 'background:#f1f5f9;color:#475569',
                                };
                            @endphp
                            <tr>
                                <td colspan="{{ $showDescription ? 10 : 9 }}" style="background:#eef2ff;color:#1e293b;font-weight:700">
                                    <span class="badge" style="{{ $badgeColor }};font-size:13px;padding:4px 10px;margin-right:8px">
                                        {{ $group['label'] }}
                                    </span>
                                </td>
                            </tr>
                            @foreach($group['rows'] as $txn)
                                @php
                                    $rowNumber++;
                                    $isCredit = in_array($txn->type, ['income', 'capital']);
                                @endphp
                                <tr>
                                    <td>{{ $rowNumber }}</td>
                                    <td style="white-space:nowrap;color:#64748b">{{ $txn->transaction_date->format('d/m/Y') }}</td>
                                    <td style="font-family:monospace;font-size:11px;color:#475569">{{ $txn->reference_no ?? '—' }}</td>
                                    <td>
                                        <span class="badge" style="{{ $badgeColor }};font-size:10px;padding:3px 7px">
                                            {{ ucfirst($txn->type) }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($txn->type === 'income')     {{ $txn->incomeCategory?->name ?? '—' }}
                                        @elseif($txn->type === 'expense') {{ $txn->expenseCategory?->name ?? '—' }}
                                        @else                             {{ $txn->shareholder?->name ?? '—' }}
                                        @endif
                                    </td>
                                    @if($showDescription)
                                        <td style="color:#334155; width:220px; max-width:220px; word-break:break-word;">{{ $txn->description ?? '—' }}</td>
                                    @endif
                                    <td>
                                        <span class="badge" style="background:#fff7ed;color:#c2410c;border:1px solid #fed7aa;font-size:10px;padding:3px 7px">
                                            {{ $txn->payment_method ?? '—' }}
                                        </span>
                                    </td>
                                    <td class="text-right" style="color:#e11d48;font-family:monospace">
                                        {{ !$isCredit ? number_format($txn->amount, 2) : '—' }}
                                    </td>
                                    <td class="text-right" style="color:#16a34a;font-family:monospace">
                                        {{ $isCredit ? number_format($txn->amount, 2) : '—' }}
                                    </td>
                                    <td style="font-size:12px;color:#64748b">{{ $txn->recorder?->name ?? '—' }}</td>
                                </tr>
                            @endforeach
                            <tr>
                                <td colspan="{{ $showDescription ? 7 : 6 }}" style="background:#f8fafc;font-weight:700;padding:6px 8px">Total</td>
                                <td class="text-right" style="background:#f8fafc;font-weight:700">{{ number_format($group['totalDebit'], 2) }}</td>
                                <td class="text-right" style="background:#f8fafc;font-weight:700">{{ number_format($group['totalCredit'], 2) }}</td>
                                <td style="background:#f8fafc"></td>
                            </tr>
                        @empty
                            <tr><td colspan="{{ $showDescription ? 10 : 9 }}" class="text-center text-muted py-5">No transactions found</td></tr>
                        @endforelse
                    </tbody>
                    @if($transactions->count())
                        <tfoot style="background:#f8fafc;font-weight:700">
                            <tr>
                                <td colspan="{{ $showDescription ? 7 : 6 }}">Total ({{ $transactions->total() }} records)</td>
                                <td class="text-right" style="color:#e11d48">{{ number_format($totalExpense + $totalWithdrawal, 2) }}</td>
                                <td class="text-right" style="color:#16a34a">{{ number_format($totalIncome + $totalCapital, 2) }}</td>
                                <td></td>
                            </tr>
                        </tfoot>
                    @endif
                </table>
            </div>

            @if($transactions->hasPages())
                <div class="p-3">
                    {{ $transactions->links() }}
                </div>
            @endif

        </div>
    </div>
</div>
@endsection
