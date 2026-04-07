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
