@extends('layouts.master')
@section('contents')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-4">
            <div class="card">
                <div class="card-header shadow p-3"><h3 class="card-title">Add Budget Head</h3></div>
                <form method="POST" action="{{ route('budget-heads.store') }}">
                    @csrf
                    <div class="card-body">
                        @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
                        <div class="form-group">
                            <label>Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
                            @error('name')<span class="invalid-feedback">{{ $message }}</span>@enderror
                        </div>
                        <div class="form-group">
                            <label>Parent Head <small class="text-muted">(optional)</small></label>
                            <select name="parent_id" class="form-control">
                                <option value="">— Top Level —</option>
                                @foreach($parents as $p)
                                    <option value="{{ $p->id }}" {{ old('parent_id') == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Description</label>
                            <textarea name="description" class="form-control" rows="2">{{ old('description') }}</textarea>
                        </div>
                    </div>
                    <div class="card-footer"><button class="btn btn-success">Save</button></div>
                </form>
            </div>
        </div>
        <div class="col-md-8">
            <div class="card">
                <div class="card-header shadow p-3"><h3 class="card-title">Budget Heads</h3></div>
                <div class="card-body p-0">
                    <table class="table table-hover mb-0">
                        <thead><tr><th>#</th><th>Name</th><th>Parent</th><th>Description</th><th width="120">Actions</th></tr></thead>
                        <tbody>
                            @forelse($heads as $h)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td class="fw-bold">{{ $h->name }}</td>
                                <td>{{ $h->parent?->name ?? '—' }}</td>
                                <td style="font-size:12px">{{ $h->description ?? '—' }}</td>
                                <td style="display:flex;gap:5px; justify-content: center; align-items: center;">
                                    <button class="btn btn-sm btn-dark" data-toggle="modal" data-target="#editHead{{ $h->id }}"><i class="fas fa-edit"></i></button>
                                    <form action="{{ route('budget-heads.destroy', $h->id) }}"  class="m-0" method="POST" onsubmit="return confirm('Delete?')">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                            <div class="modal fade" id="editHead{{ $h->id }}">
                                <div class="modal-dialog"><div class="modal-content">
                                    <div class="modal-header"><h5 class="modal-title">Edit Budget Head</h5></div>
                                    <form method="POST" action="{{ route('budget-heads.update', $h->id) }}">
                                        @csrf @method('PUT')
                                        <div class="modal-body">
                                            <div class="form-group"><label>Name</label><input type="text" name="name" class="form-control" value="{{ $h->name }}" required></div>
                                            <div class="form-group">
                                                <label>Parent</label>
                                                <select name="parent_id" class="form-control">
                                                    <option value="">— None —</option>
                                                    @foreach($parents as $p)
                                                        <option value="{{ $p->id }}" {{ $h->parent_id == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="form-group"><label>Description</label><textarea name="description" class="form-control" rows="2">{{ $h->description }}</textarea></div>
                                        </div>
                                        <div class="modal-footer">
                                            <button class="btn btn-success">Update</button>
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                        </div>
                                    </form>
                                </div></div>
                            </div>
                            @empty
                            <tr><td colspan="5" class="text-center text-muted py-4">No budget heads yet</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="card-footer">{{ $heads->links() }}</div>
            </div>
        </div>
    </div>
</div>
@endsection
