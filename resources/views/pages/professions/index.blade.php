@extends('layouts.master')
@section('contents')
<div class="container-fluid">
    <div class="row">

        {{-- Create Form --}}
        <div class="col-md-4">
            <div class="card">
                <div class="card-header shadow p-3"><h3 class="card-title">Add Profession</h3></div>
                <form method="POST" action="{{ route('professions.store') }}">
                    @csrf
                    <div class="card-body">
                        @if(session('success'))
                            <div class="alert alert-success">{{ session('success') }}</div>
                        @endif
                        @if($errors->any())
                            <div class="alert alert-danger">{{ $errors->first() }}</div>
                        @endif
                        <div class="form-group">
                            <label>Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                   value="{{ old('name') }}" placeholder="e.g. Doctor" required>
                            @error('name')<span class="invalid-feedback">{{ $message }}</span>@enderror
                        </div>
                        <div class="form-group">
                            <label>Bangla Name <span class="text-danger">*</span></label>
                            <input type="text" name="bn_name" class="form-control @error('bn_name') is-invalid @enderror"
                                   value="{{ old('bn_name') }}" placeholder="e.g. ডাক্তার" required>
                            @error('bn_name')<span class="invalid-feedback">{{ $message }}</span>@enderror
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
                <div class="card-header shadow p-3"><h3 class="card-title">Professions</h3></div>
                <div class="card-body p-0">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Bangla Name</th>
                                <th width="120">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($professions as $profession)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $profession->name }}</td>
                                <td>{{ $profession->bn_name }}</td>
                                <td style="display:flex;gap:5px">
                                    <button class="btn btn-sm btn-dark"
                                            data-toggle="modal"
                                            data-target="#editProfession{{ $profession->id }}">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <form action="{{ route('professions.destroy', $profession) }}"
                                          method="POST"
                                          onsubmit="return confirm('Delete this profession?')">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                                    </form>
                                </td>
                            </tr>

                            {{-- Edit Modal --}}
                            <div class="modal fade" id="editProfession{{ $profession->id }}" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Edit Profession</h5>
                                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                                        </div>
                                        <form method="POST" action="{{ route('professions.update', $profession) }}">
                                            @csrf @method('PUT')
                                            <div class="modal-body">
                                                <div class="form-group">
                                                    <label>Name <span class="text-danger">*</span></label>
                                                    <input type="text" name="name" class="form-control"
                                                           value="{{ $profession->name }}" required>
                                                </div>
                                                <div class="form-group">
                                                    <label>Bangla Name <span class="text-danger">*</span></label>
                                                    <input type="text" name="bn_name" class="form-control"
                                                           value="{{ $profession->bn_name }}" required>
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
                            <tr>
                                <td colspan="4" class="text-center text-muted py-4">No professions yet</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="card-footer">{{ $professions->links() }}</div>
            </div>
        </div>

    </div>
</div>
@endsection
