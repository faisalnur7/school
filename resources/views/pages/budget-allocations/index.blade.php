@extends('layouts.master')
@section('contents')
<div class="container-fluid">
    <div class="row">

        {{-- Create / Edit Form --}}
        <div class="col-md-4">
            <div class="card">
                <div class="card-header shadow p-3"><h3 class="card-title text-white">Add Allocation</h3></div>
                <form method="POST" action="{{ route('budget-allocations.store') }}">
                    @csrf
                    <div class="card-body">
                        @if(session('success'))<div class="alert alert-success py-2">{{ session('success') }}</div>@endif
                        @if($errors->any())<div class="alert alert-danger py-2">{{ $errors->first() }}</div>@endif

                        <div class="form-group">
                            <label>Account (Budget Head) <span class="text-danger">*</span></label>
                            <select name="account_id" class="form-control @error('account_id') is-invalid @enderror" required>
                                <option value="">— Select Account —</option>
                                @foreach($accountGroups as $group)
                                    @if($group->accounts->count())
                                    <optgroup label="{{ $group->name }}">
                                        @foreach($group->accounts as $acc)
                                            <option value="{{ $acc->id }}" {{ old('account_id') == $acc->id ? 'selected' : '' }}>
                                                {{ $acc->name }}
                                            </option>
                                        @endforeach
                                    </optgroup>
                                    @endif
                                @endforeach
                            </select>
                            @error('account_id')<span class="invalid-feedback">{{ $message }}</span>@enderror
                        </div>

                        <div class="form-group">
                            <label>Expense Category <small class="text-muted">(optional override)</small></label>
                            <select name="expense_category_id" class="form-control">
                                <option value="">Auto from Account</option>
                                @foreach($categories as $c)
                                    <option value="{{ $c->id }}" {{ old('expense_category_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Amount <span class="text-danger">*</span></label>
                            <input type="number" name="amount" step="0.01" min="0.01"
                                   class="form-control @error('amount') is-invalid @enderror"
                                   value="{{ old('amount') }}" required>
                            @error('amount')<span class="invalid-feedback">{{ $message }}</span>@enderror
                        </div>

                        <div class="row">
                            <div class="col-6">
                                <div class="form-group">
                                    <label>Period</label>
                                    <select name="period" class="form-control" id="periodSelect">
                                        <option value="yearly"  {{ old('period','yearly') === 'yearly'  ? 'selected' : '' }}>Yearly</option>
                                        <option value="monthly" {{ old('period') === 'monthly' ? 'selected' : '' }}>Monthly</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <label>Fiscal Year <span class="text-danger">*</span></label>
                                    <input type="number" name="fiscal_year" class="form-control"
                                           value="{{ old('fiscal_year', now()->year) }}" min="2000" max="2100" required>
                                </div>
                            </div>
                        </div>

                        <div class="form-group" id="monthWrapper" style="{{ old('period') === 'monthly' ? '' : 'display:none' }}">
                            <label>Month</label>
                            <select name="fiscal_month" class="form-control">
                                <option value="">—</option>
                                @foreach(range(1,12) as $m)
                                    <option value="{{ $m }}" {{ old('fiscal_month') == $m ? 'selected' : '' }}>
                                        {{ date('F', mktime(0,0,0,$m,1)) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Notes</label>
                            <textarea name="notes" class="form-control" rows="2">{{ old('notes') }}</textarea>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button class="btn btn-success">Save</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- List --}}
        <div class="col-md-8">
            <div class="card">
                <div class="card-header shadow p-0 flex justify-between items-center">
                    <h3 class="card-title flex text-white pl-3 text-medium">Budget Allocations — {{ $year }}</h3>
                    <div class="flex gap-2 pr-3 py-2 items-end ml-auto">
                        <form method="GET" class="flex gap-2 items-end">
                            <div>
                                <label style="font-size:12px;color:#FFF">Year</label>
                                <input type="number" name="year" class="form-control form-control-sm" value="{{ $year }}" style="width:100px">
                            </div>
                            <button class="btn btn-sm btn-dark" style="margin-top:18px">Go</button>
                        </form>
                        <a href="{{ route('budget-allocations.report', ['year' => $year]) }}"
                           class="btn btn-sm btn-info" style="margin-top:18px">
                            <i class="fas fa-chart-bar"></i> Report
                        </a>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="px-3 pt-2 pb-1">
                        <span class="badge" style="background:#eff6ff;color:#2563eb;border:1px solid #bfdbfe;font-size:12px;padding:5px 12px">
                            Total Budget: {{ number_format($totalBudget, 2) }}
                        </span>
                    </div>
                    <table class="table table-hover mb-0" style="font-size:13px">
                        <thead style="background:#f8fafc">
                            <tr>
                                <th>Account</th>
                                <th>Group</th>
                                <th>Category</th>
                                <th>Period</th>
                                <th class="text-right">Amount</th>
                                <th width="80">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($allocations as $a)
                            <tr>
                                <td class="fw-bold">{{ $a->account?->name ?? '—' }}</td>
                                <td style="color:#64748b;font-size:12px">{{ $a->account?->group?->name ?? '—' }}</td>
                                <td style="font-size:12px">{{ $a->expenseCategory?->name ?? 'Auto' }}</td>
                                <td>
                                    <span class="badge" style="background:#f1f5f9;color:#475569;border:1px solid #e2e8f0;font-size:11px">
                                        {{ ucfirst($a->period) }}{{ $a->fiscal_month ? ' / ' . date('M', mktime(0,0,0,$a->fiscal_month,1)) : '' }}
                                    </span>
                                </td>
                                <td class="text-right fw-bold">{{ number_format($a->amount, 2) }}</td>
                                <td style="display:flex;gap:4px">
                                    <button class="btn btn-sm btn-dark"
                                            data-toggle="modal" data-target="#editModal{{ $a->id }}">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <form action="{{ route('budget-allocations.destroy', $a->id) }}" method="POST"
                                          class="m-0" onsubmit="return confirm('Delete?')">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                                    </form>
                                </td>
                            </tr>

                            {{-- Edit Modal --}}
                            <div class="modal fade" id="editModal{{ $a->id }}" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Edit Allocation</h5>
                                            <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                                        </div>
                                        <form method="POST" action="{{ route('budget-allocations.update', $a->id) }}">
                                            @csrf @method('PUT')
                                            <div class="modal-body">
                                                <div class="form-group">
                                                    <label>Account (Budget Head)</label>
                                                    <select name="account_id" class="form-control" required>
                                                        @foreach($accountGroups as $group)
                                                            @if($group->accounts->count())
                                                            <optgroup label="{{ $group->name }}">
                                                                @foreach($group->accounts as $acc)
                                                                    <option value="{{ $acc->id }}" {{ $a->account_id == $acc->id ? 'selected' : '' }}>
                                                                        {{ $acc->name }}
                                                                    </option>
                                                                @endforeach
                                                            </optgroup>
                                                            @endif
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="form-group">
                                                    <label>Expense Category</label>
                                                    <select name="expense_category_id" class="form-control">
                                                        <option value="">Auto from Account</option>
                                                        @foreach($categories as $c)
                                                            <option value="{{ $c->id }}" {{ $a->expense_category_id == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="form-group">
                                                    <label>Amount</label>
                                                    <input type="number" name="amount" step="0.01" class="form-control" value="{{ $a->amount }}" required>
                                                </div>
                                                <div class="row">
                                                    <div class="col-6">
                                                        <div class="form-group">
                                                            <label>Period</label>
                                                            <select name="period" class="form-control">
                                                                <option value="yearly"  {{ $a->period === 'yearly'  ? 'selected' : '' }}>Yearly</option>
                                                                <option value="monthly" {{ $a->period === 'monthly' ? 'selected' : '' }}>Monthly</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-6">
                                                        <div class="form-group">
                                                            <label>Fiscal Year</label>
                                                            <input type="number" name="fiscal_year" class="form-control" value="{{ $a->fiscal_year }}" required>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label>Month</label>
                                                    <select name="fiscal_month" class="form-control">
                                                        <option value="">—</option>
                                                        @foreach(range(1,12) as $m)
                                                            <option value="{{ $m }}" {{ $a->fiscal_month == $m ? 'selected' : '' }}>
                                                                {{ date('F', mktime(0,0,0,$m,1)) }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="form-group">
                                                    <label>Notes</label>
                                                    <textarea name="notes" class="form-control" rows="2">{{ $a->notes }}</textarea>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button class="btn btn-success">Update</button>
                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            @empty
                            <tr><td colspan="6" class="text-center text-muted py-4">No allocations for {{ $year }}</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="card-footer">{{ $allocations->links() }}</div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.getElementById('periodSelect').addEventListener('change', function () {
    document.getElementById('monthWrapper').style.display = this.value === 'monthly' ? '' : 'none';
});
</script>
@endsection
