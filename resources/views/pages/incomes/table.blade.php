<div class="card">
    <div class="card-header text-white rounded-top d-flex justify-content-between align-items-center shadow p-3">
        <h3 class="card-title mb-0 text-white text-lg">Incomes</h3>
        <a href="{{ route('incomes.create') }}" class="btn btn-primary btn-sm ml-auto text-bold">
            + Add Income
        </a>
    </div>

    <div class="card-body px-0 pb-4 pt-0">

        {{-- Filters --}}
        <form method="GET" action="{{ route('incomes.index') }}" class="px-3 pt-3 pb-2">
            <div class="row g-2 align-items-end">
                <div class="col-md-3">
                    <label class="form-label mb-1" style="font-size:12px">Category</label>
                    <select name="category" class="form-control form-control-sm">
                        <option value="">All Categories</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label mb-1" style="font-size:12px">From Date</label>
                    <input type="text" name="from" datepicker datepicker-format="dd/mm/yyyy"
                           class="form-control form-control-sm"
                           value="{{ request('from') }}" placeholder="dd/mm/yyyy" autocomplete="off">
                </div>
                <div class="col-md-3">
                    <label class="form-label mb-1" style="font-size:12px">To Date</label>
                    <input type="text" name="to" datepicker datepicker-format="dd/mm/yyyy"
                           class="form-control form-control-sm"
                           value="{{ request('to') }}" placeholder="dd/mm/yyyy" autocomplete="off">
                </div>
                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-sm btn-dark">Filter</button>
                    <a href="{{ route('incomes.index') }}" class="btn btn-sm btn-secondary">Reset</a>
                </div>
            </div>
        </form>

        {{-- Total --}}
        <div class="px-3 pb-2">
            <span class="badge" style="background:#eef2ff;color:#4338ca;border:1px solid #c7d2fe;font-size:12px;padding:6px 12px">
                Total: BDT {{ number_format($total, 2) }}
            </span>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Category</th>
                        <th>Title</th>
                        <th>Description</th>
                        <th>Date</th>
                        <th>Method</th>
                        <th>Amount</th>
                        <th>Attachment</th>
                        <th width="120">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($incomes as $income)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>
                                <span class="badge" style="background:#f1f5f9;color:#475569;border:1px solid #e2e8f0;font-size:11px">
                                    {{ $income->category->name ?? '—' }}
                                </span>
                            </td>
                            <td>{{ $income->title }}</td>
                            <td style="color:#64748b;font-size:12px">{{ $income->description ?? '—' }}</td>
                            <td class="mono" style="font-size:13px">{{ $income->income_date->format('d/m/Y') }}</td>
                            <td>
                                <span class="badge" style="background:#ecfdf5;color:#059669;border:1px solid #a7f3d0;font-size:11px">
                                    {{ $income->payment_method }}
                                </span>
                            </td>
                            <td class="mono fw-bold" style="color:#4338ca">
                                {{ number_format($income->amount, 2) }}
                            </td>
                            <td>
                                @if ($income->attachment)
                                    <a href="{{ asset('storage/' . $income->attachment) }}" target="_blank"
                                       class="btn btn-sm btn-outline-secondary" title="View Attachment">
                                        <i class="fas fa-paperclip"></i>
                                    </a>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td style="display:flex;gap:5px; justify-content: center; align-items: center;;align-items:start;">
                                <a href="{{ route('incomes.edit', $income->id) }}" class="btn btn-sm btn-dark">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('incomes.destroy', $income->id) }}" method="POST"
                                      class="btn btn-sm btn-danger d-inline m-0"
                                      onsubmit="return confirm('Delete this income record?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" style="background:none;border:none;padding:0;color:inherit">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach

                    @if ($incomes->isEmpty())
                        <tr>
                            <td colspan="9" class="text-center text-muted py-4">No income records found</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>

        <div class="px-3 pt-3">
            {{ $incomes->links() }}
        </div>
    </div>
</div>