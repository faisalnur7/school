@extends('layouts.master')
@section('contents')
<div class="container-fluid">
    <div class="row">
        {{-- Create Form --}}
        <div class="col-md-4">
            <div class="card">
                <div class="card-header shadow p-3"><h3 class="card-title text-white">{{ __('Add Account Group') }}</h3></div>
                <form method="POST" action="{{ route('account-groups.store') }}">
                    @csrf
                    <div class="card-body">
                        @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
                        <div class="form-group">
                            <label>{{ __('Name') }} <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
                            @error('name')<span class="invalid-feedback">{{ $message }}</span>@enderror
                        </div>
                        <div class="form-group">
                            <label>{{ __('Parent Group') }} <small class="text-muted">({{ __('optional') }})</small></label>
                            <select name="parent_id" class="form-control">
                                <option value="">{{ __('— None (Top Level) —') }}</option>
                                @foreach($parents as $p)
                                    <option value="{{ $p->id }}" {{ old('parent_id') == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="card-footer"><button class="btn btn-success">{{ __('Save') }}</button></div>
                </form>
            </div>
        </div>

        {{-- List --}}
        <div class="col-md-8">
            <div class="card">
                <div class="card-header shadow p-3"><h3 class="card-title text-white">{{ __('Account Groups') }}</h3></div>
                <div class="card-body p-0">
                    <table class="table table-hover mb-0">
                        <thead><tr><th>#</th><th>{{ __('Name') }}</th><th>{{ __('Parent') }}</th><th width="120">{{ __('Actions') }}</th></tr></thead>
                        <tbody>
                            @forelse($groups as $g)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $g->name }}</td>
                                <td>{{ $g->parent?->name ?? '—' }}</td>
                                <td style="display:flex;gap:5px; justify-content: center; align-items: center;">
                                    <button class="btn btn-sm btn-dark p-2" data-toggle="modal" data-target="#editGroup{{ $g->id }}"><i class="fas fa-edit"></i></button>
                                    <form action="{{ route('account-groups.destroy', $g->id) }}" class="m-0" method="POST" onsubmit="return confirm('{{ __('Delete?') }}')">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-danger py-2"><i class="fas fa-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                            {{-- Edit Modal --}}
                            <div class="modal fade" id="editGroup{{ $g->id }}">
                                <div class="modal-dialog"><div class="modal-content">
                                    <div class="modal-header"><h5 class="modal-title">{{ __('Edit Group') }}</h5></div>
                                    <form method="POST" action="{{ route('account-groups.update', $g->id) }}">
                                        @csrf @method('PUT')
                                        <div class="modal-body">
                                            <div class="form-group">
                                                <label>{{ __('Name') }}</label>
                                                <input type="text" name="name" class="form-control" value="{{ $g->name }}" required>
                                            </div>
                                            <div class="form-group">
                                                <label>{{ __('Parent') }}</label>
                                                <select name="parent_id" class="form-control">
                                                    <option value="">{{ __('— None —') }}</option>
                                                    @foreach($parents as $p)
                                                        <option value="{{ $p->id }}" {{ $g->parent_id == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button class="btn btn-success">{{ __('Update') }}</button>
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Cancel') }}</button>
                                        </div>
                                    </form>
                                </div></div>
                            </div>
                            @empty
                            <tr><td colspan="4" class="text-center text-muted py-4">{{ __('No groups yet') }}</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="card-footer">{{ $groups->links() }}</div>
            </div>
        </div>
    </div>
</div>
@endsection
