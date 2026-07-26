@extends('layouts.master')

@section('styles')
    <style>
        .transactions-report .transactions-pdf-panel {
            margin-top: 0.35rem;
            border: 1px solid #dbe4f0;
            border-radius: 12px;
            padding: 1rem 1rem 1.1rem;
            background: linear-gradient(180deg, #ffffff 0%, #fbfcfe 100%);
            box-shadow: 0 1px 2px rgba(15, 23, 42, 0.03);
        }

        .transactions-report .transactions-pdf-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 1rem;
            margin-bottom: 1rem;
        }

        .transactions-report .transactions-pdf-copy {
            min-width: 0;
        }

        .transactions-report .transactions-pdf-title {
            margin: 0;
            font-size: 0.98rem;
            font-weight: 700;
            color: #111827;
        }

        .transactions-report .transactions-pdf-subtitle {
            display: block;
            margin-top: 0.25rem;
            font-size: 0.83rem;
            line-height: 1.45;
            color: #6b7280;
        }

        .transactions-report .transactions-pdf-toggle {
            flex: 0 0 auto;
            display: inline-flex;
            align-items: center;
            gap: 0.6rem;
            padding-top: 0.1rem;
            white-space: nowrap;
            font-size: 0.9rem;
            font-weight: 700;
            color: #334155;
        }

        .transactions-report .transactions-pdf-toggle .form-check-input {
            margin-top: 0;
        }

        .transactions-report .transactions-pdf-checks {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 0.95rem 1rem;
        }

        .transactions-report .transactions-pdf-option {
            display: flex;
            align-items: center;
            gap: 0.55rem;
            margin: 0;
            min-width: 0;
        }

        .transactions-report .transactions-pdf-option .form-check-input {
            flex: 0 0 auto;
            margin-top: 0.1rem;
            margin-left: 0;
        }

        .transactions-report .transactions-pdf-panel .form-check-input {
            appearance: none;
            -webkit-appearance: none;
            float: none;
            width: 1.05rem;
            height: 1.05rem;
            border: 2px solid #111111;
            border-radius: 999px;
            background-color: #ffffff;
            background-repeat: no-repeat;
            background-position: center;
            background-size: 0.72rem 0.72rem;
            box-shadow: none;
            cursor: pointer;
            transition: background-color 0.15s ease, border-color 0.15s ease, box-shadow 0.15s ease;
        }

        .transactions-report .transactions-pdf-panel .form-check-input:checked {
            background-color: #111111;
            border-color: #111111;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16' fill='none'%3E%3Cpath d='M6.2 11.2 2.9 8l-1.1 1.1 4.4 4.4L14.2 5.5 13.1 4.4 6.2 11.2Z' fill='%23ffffff'/%3E%3C/svg%3E");
        }

        .transactions-report .transactions-pdf-panel .form-check-input:focus {
            outline: none;
            box-shadow: 0 0 0 4px rgba(17, 17, 17, 0.12);
        }

        .transactions-report .transactions-pdf-panel .form-check-input:hover {
            border-color: #000000;
        }

        .transactions-report .transactions-pdf-option .form-check-label {
            display: inline-block;
            margin-bottom: 0;
            font-size: 0.88rem;
            font-weight: 600;
            color: #1f2937;
            margin-left: 2rem;
        }

        .transactions-report .transactions-filter-row {
            display: grid;
            grid-template-columns:
                minmax(240px, 2.4fr)
                minmax(130px, 1fr)
                minmax(130px, 1fr)
                minmax(140px, 1.1fr)
                minmax(150px, 1.2fr)
                minmax(120px, 0.8fr)
                minmax(120px, 0.8fr)
                auto;
            gap: 0.75rem;
            align-items: end;
            width: 100%;
        }

        .transactions-report .transactions-filter-row > * {
            min-width: 0;
        }

        .transactions-report .transactions-filter-row .transactions-filter-actions {
            justify-content: flex-end;
        }

        .transactions-report .transactions-filter-row .transactions-pdf-panel {
            grid-column: 1 / -1;
        }

        .transactions-report .transactions-filter-row > .transactions-pdf-panel-wrap {
            grid-column: 1 / -1;
        }

        @media (max-width: 1199.98px) {
            .transactions-report .transactions-filter-row {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .transactions-report .transactions-filter-row .transactions-pdf-panel {
                grid-column: 1 / -1;
            }

            .transactions-report .transactions-pdf-checks {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 767.98px) {
            .transactions-report .transactions-filter-row {
                grid-template-columns: 1fr;
            }

            .transactions-report .transactions-pdf-header {
                flex-direction: column;
                align-items: stretch;
            }

            .transactions-report .transactions-pdf-checks {
                grid-template-columns: 1fr;
            }
        }
    </style>
@endsection

@section('contents')
<div class="container-fluid transactions-report">
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
                @php
                    $allPdfTypes = ['income', 'expense', 'capital', 'withdrawal'];
                    $selectedPdfTypes = request()->boolean('pdf_desc_custom')
                        ? (array) request('pdf_description_types', [])
                        : $allPdfTypes;
                @endphp
                <div class="transactions-filter-row">
                    <div>
                        <label class="form-label mb-1" style="font-size:12px">Search</label>
                        <input type="text" name="search" class="form-control form-control-sm"
                               value="{{ request('search') }}" placeholder="Ref / description...">
                    </div>
                    <div>
                        <label class="form-label mb-1" style="font-size:12px">Type</label>
                        <select name="type" class="form-control form-control-sm">
                            <option value="">All Types</option>
                            @foreach(['income','expense','capital','withdrawal'] as $t)
                                <option value="{{ $t }}" {{ request('type') === $t ? 'selected' : '' }}>{{ ucfirst($t) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="form-label mb-1" style="font-size:12px">View Type</label>
                        <select name="view_type" class="form-control form-control-sm">
                            <option value="detailed" {{ request('view_type', 'detailed') === 'detailed' ? 'selected' : '' }}>Detailed</option>
                            <option value="summary" {{ request('view_type') === 'summary' ? 'selected' : '' }}>Summary</option>
                        </select>
                    </div>
                    <div>
                        <label class="form-label mb-1" style="font-size:12px">Shareholder</label>
                        <select name="shareholder_id" class="form-control form-control-sm">
                            <option value="">All</option>
                            @foreach($shareholders as $sh)
                                <option value="{{ $sh->id }}" {{ request('shareholder_id') == $sh->id ? 'selected' : '' }}>{{ $sh->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
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
                    <div>
                        <label class="form-label mb-1" style="font-size:12px">From</label>
                        <input type="text" name="from" datepicker datepicker-format="dd/mm/yyyy"
                               class="form-control form-control-sm" value="{{ request('from') }}" placeholder="dd/mm/yyyy" autocomplete="off">
                    </div>
                    <div>
                        <label class="form-label mb-1" style="font-size:12px">To</label>
                        <input type="text" name="to" datepicker datepicker-format="dd/mm/yyyy"
                               class="form-control form-control-sm" value="{{ request('to') }}" placeholder="dd/mm/yyyy" autocomplete="off">
                    </div>
                    <div class="transactions-filter-actions d-flex gap-2 align-items-end">
                        <button type="submit" class="btn btn-sm btn-dark" title="Filter" aria-label="Filter">
                            <i class="fas fa-search"></i>
                        </button>
                        <a href="{{ route('transactions.index') }}" class="btn btn-sm btn-secondary" title="Reset" aria-label="Reset">
                            <i class="fas fa-undo-alt"></i>
                        </a>
                    </div>
                    <div class="transactions-pdf-panel-wrap">
                        <div class="transactions-pdf-panel">
                            <div class="transactions-pdf-header">
                                <div class="transactions-pdf-copy">
                                    <p class="transactions-pdf-title">PDF Description Selection</p>
                                    <small class="transactions-pdf-subtitle">Choose which transaction types will show descriptions in the exported PDF list.</small>
                                </div>
                                <div class="form-check transactions-pdf-toggle mb-0">
                                    <input class="form-check-input" type="checkbox" id="pdf-description-toggle-all" checked>
                                    <label class="form-check-label" for="pdf-description-toggle-all">Select all</label>
                                </div>
                            </div>

                            <input type="hidden" name="pdf_desc_custom" value="1">

                            <div class="transactions-pdf-checks">
                                @foreach($allPdfTypes as $pdfType)
                                    <div class="form-check transactions-pdf-option">
                                        <input class="form-check-input pdf-description-checkbox" type="checkbox" name="pdf_description_types[]" value="{{ $pdfType }}"
                                               id="pdf-description-{{ $pdfType }}" {{ in_array($pdfType, $selectedPdfTypes, true) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="pdf-description-{{ $pdfType }}">
                                            {{ ucfirst($pdfType) }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
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

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const toggleAll = document.getElementById('pdf-description-toggle-all');
            const checkboxes = Array.from(document.querySelectorAll('.pdf-description-checkbox'));

            if (!toggleAll || !checkboxes.length) {
                return;
            }

            const syncToggleState = () => {
                toggleAll.checked = checkboxes.every((checkbox) => checkbox.checked);
            };

            toggleAll.addEventListener('change', function () {
                checkboxes.forEach((checkbox) => {
                    checkbox.checked = toggleAll.checked;
                });
            });

            checkboxes.forEach((checkbox) => {
                checkbox.addEventListener('change', syncToggleState);
            });

            syncToggleState();
        });
    </script>
@endsection
