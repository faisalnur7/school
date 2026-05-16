@extends('layouts.master')
@section('contents')
<div class="container-fluid">
    <div class="card">
        <div class="card-header shadow p-3 d-flex justify-content-between align-items-center">
            <h3 class="card-title mb-0 text-white text-lg">Accounts</h3>
            <a href="{{ route('accounts-list.create') }}" class="btn btn-primary btn-sm ml-auto">+ Add Account</a>
        </div>
        <div class="card-body p-0">
            @if(session('success'))<div class="alert alert-success m-3">{{ session('success') }}</div>@endif

            {{-- Filters --}}
            <form method="GET" action="{{ route('accounts-list.index') }}" class="px-3 pt-3 pb-2">
                <div class="row g-2 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label mb-1" style="font-size:12px">Name</label>
                        <input type="text" name="name" class="form-control form-control-sm" value="{{ request('name') }}" placeholder="Search by name">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label mb-1" style="font-size:12px">Group</label>
                        <select name="group" class="form-control form-control-sm">
                            <option value="">All Groups</option>
                            @foreach ($groups as $group)
                                <option value="{{ $group->id }}" {{ request('group') == $group->id ? 'selected' : '' }}>{{ $group->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label mb-1" style="font-size:12px">Linked Account</label>
                        <select name="linked" class="form-control form-control-sm">
                            <option value="">All</option>
                            <option value="App\Models\BankAccount" {{ request('linked') == 'App\Models\BankAccount' ? 'selected' : '' }}>Bank Account</option>
                            <option value="App\Models\HandCash" {{ request('linked') == 'App\Models\HandCash' ? 'selected' : '' }}>Hand Cash</option>
                            <option value="App\Models\MobileBankingAccount" {{ request('linked') == 'App\Models\MobileBankingAccount' ? 'selected' : '' }}>Mobile Banking</option>
                        </select>
                    </div>
                    <div class="col-md-3 d-flex gap-2">
                        <button type="submit" class="btn btn-sm btn-dark">Filter</button>
                        <a href="{{ route('accounts-list.index') }}" class="btn btn-sm btn-secondary">Reset</a>
                    </div>
                </div>
            </form>

            <table class="table table-hover mb-0">
                <thead><tr><th>#</th><th>Name</th><th>Group</th><th>Linked Account</th><th>Notes</th><th width="100">Actions</th></tr></thead>
                <tbody>
                    @forelse($accounts as $a)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td class="fw-bold">{{ $a->name }}</td>
                        <td>{{ $a->group?->name ?? '—' }}</td>
                        <td style="font-size:12px;color:#475569">{{ $a->reference_label }}</td>
                        <td style="font-size:12px">{{ $a->notes ?? '—' }}</td>
                        <td style="display:flex;gap:5px; justify-content: center; align-items: center;">
                            <a href="{{ route('accounts-list.edit', $a->id) }}" class="btn btn-sm btn-dark"><i class="fas fa-edit"></i></a>
                            <form action="{{ route('accounts-list.destroy', $a->id) }}"  class="m-0" method="POST" onsubmit="return confirm('Delete?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="text-center text-muted py-4">No accounts yet</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer">{{ $accounts->links() }}</div>
    </div>
</div>
@endsection
