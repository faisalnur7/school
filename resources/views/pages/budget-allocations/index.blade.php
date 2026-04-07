@extends('layouts.master')
@section('contents')
<div class="container-fluid">
    <div class="row">
        {{-- Create Form --}}
        <div class="col-md-4">
            <div class="card">
                <div class="card-header shadow p-3"><h3 class="card-title">Add Allocation</h3></div>
                <form method="POST" action="{{ route('budget-allocations.store') }}">
                    @csrf
                    <div class="card-body">
                        @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
                        <div class="form-group">
                            <label>Budget Head <span class="text-danger">*</span></label>
                            <select name="budget_head_id" class="form-control @error('budget_head_id') is-invalid @enderror" required>
                                <option value="">Select</option>
                                @foreach($heads as $h)
                                    <option value="{{ $h->id }}" {{ old('budget_head_id') == $h->id ? 'selected' : '' }}>{{ $h->name }}</option>
                                @endforeach
                            </select>
                            @error('budget_head_id')<span class="invalid-feedback">{{ $message }}</span>@enderror
                        </div>
                        <div class="form-group">
                            <label>Expense Category <small class="text-muted">(optional)</small></label>
                            <select name="expense_category_id" class="form-control">
                                <option value="">All Categories</option>
                                @foreach($categories as $c)
                                    <option value="{{ $c->id }}" {{ old('expense_category_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Amount <span class="text-danger">*</span></label>
                            <input type="number" name="amount" step="0.01" min="0.01" class="form-control @error('amount') is-invalid @enderror" value="{{ old('amount') }}" required>
                            @error('amount')<span class="invalid-feedback">{{ $message }}</span>@enderror
                        </div>
                        <div class="form-group">
                            <label>Period</label>
                            <select name="period" class="form-control">
                                <option value="yearly" {{ old('period') === 'yearly' ? 'selected' : '' }}>Yearly</option>
                                <option value="monthly" {{ old('period') === 'monthly' ? 'selected' : '' }}>Monthly</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Fiscal Year <span class="text-danger">*</span></label>
                            <input type="number" name="fiscal_year" class="form-control" value="{{ old('fiscal_year', now()->year) }}" min="2000" max="2100" required>
                        </div>
                        <div class="form-group">
                            <label>Month <small class="text-muted">(if monthly)</small></label>
                            <select name="fiscal_month" class="form-control">
                                <option value="">—</option>
                                @foreach(range(1,12) as $m)
                                    <option value="{{ $m }}" {{ old('fiscal_month') == $m ? 'selected' : '' }}>{{ date('F', mktime(0,0,0,$m,1)) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Notes</label>
                            <textarea name="notes" class="form-control" rows="2">{{ old('notes') }}</textarea>
                        </div>
                    </div>
                    <div class="card-footer"><button class="btn btn-success">Save</button></div>
                </form>
            </div>
        </div>

        {{-- List --}}
        <div class="col-md-8">
            <div class="card">
                <div class="card-header shadow p-3 d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0 text-white text-lg">Allocations — {{ $year }}</h3>
                    <div class="d-flex gap-2 align-items-center">
                        <form method="GET" class="d-flex gap-2">
                            <input type="number" name="year" class="form-control form-control-sm" value="{{ $year }}" style="width:90px">
                            <button class="btn btn-sm btn-dark">Go</button>
                        </form>
                        <a href="{{ route('budget-allocations.report', ['year' => $year]) }}" class="btn btn-sm btn-info">View Report</a>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="px-3 pt-2 pb-1">
                        <span class="badge" style="background:#eff6ff;color:#2563eb;border:1px solid #bfdbfe;font-size:12px;padding:5px 12px">
                            Total Budget: {{ number_format($totalBudget, 2) }}
                        </span>
                    </div>
                    <table class="table table-hover mb-0" style="font-size:13px">
                        <thead><tr><th>Head</th><th>Category</th><th>Period</th><th class="text-right">Amount</th><th width="60">Del</th></tr></thead>
                        <tbody>
                            @forelse($allocations as $a)
                            <tr>
                                <td>{{ $a->budgetHead->name }}</td>
                                <td>{{ $a->expenseCategory?->name ?? 'All' }}</td>
                                <td>{{ ucfirst($a->period) }}{{ $a->fiscal_month ? ' / ' . date('M', mktime(0,0,0,$a->fiscal_month,1)) : '' }}</td>
                                <td class="text-right fw-bold">{{ number_format($a->amount, 2) }}</td>
                                <td>
                                    <form action="{{ route('budget-allocations.destroy', $a->id) }}"  class="m-0" method="POST" onsubmit="return confirm('Delete?')">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="5" class="text-center text-muted py-4">No allocations for {{ $year }}</td></tr>
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
