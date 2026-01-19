@extends('layouts.master')

@section('contents')
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-4">
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title">Edit Academic Session</h3>
                    </div>

                    <form method="POST" action="{{ route('sessions.update', $session->id) }}">
                        @csrf

                        <div class="card-body">
                            <div class="form-group">
                                <label>Name (English)</label>
                                <input type="text" name="name_en"
                                    class="form-control @error('name_en') is-invalid @enderror"
                                    value="{{ old('name_en', $session->name_en) }}" required>
                                @error('name_en')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label>Name (Bangla)</label>
                                <input type="text" name="name_bn"
                                    class="form-control @error('name_bn') is-invalid @enderror"
                                    value="{{ old('name_bn', $session->name_bn) }}" required>
                                @error('name_bn')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label>Status</label>
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input" id="statusSwitch" name="status"
                                        value="1" {{ $session->status ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="statusSwitch">
                                        Active
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="card-footer">
                            <button class="btn btn-success">
                                <i class="fas fa-save"></i> Update
                            </button>
                            <a href="{{ route('sessions.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Back
                            </a>
                        </div>
                    </form>
                </div>
            </div>
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h2 class="card-title">Academic Sessions</h2>
                    </div>

                    <div class="card-body">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Name (EN)</th>
                                    <th>Name (BN)</th>
                                    <th class="text-center">Status</th>
                                    <th width="180">Action</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach ($sessions as $session)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $session->name_en }}</td>
                                        <td>{{ $session->name_bn }}</td>
                                        <td class="text-center">
                                            <form action="{{ route('sessions.toggle-status', $session->id) }}"
                                                method="POST">
                                                @csrf
                                                <div class="custom-control custom-switch">
                                                    <input type="checkbox" class="custom-control-input"
                                                        id="statusSwitch{{ $session->id }}" onchange="this.form.submit()"
                                                        {{ $session->status ? 'checked' : '' }}>
                                                    <label class="custom-control-label"
                                                        for="statusSwitch{{ $session->id }}">
                                                    </label>
                                                </div>
                                            </form>
                                        </td>
                                        <td>
                                            <a href="{{ route('sessions.edit', $session->id) }}"
                                                class="btn btn-sm btn-info">Edit</a>

                                            <form action="{{ route('sessions.delete', $session->id) }}" method="POST"
                                                class="d-inline" onsubmit="return confirm('Delete this session?')">
                                                @csrf @method('DELETE')
                                                <button class="btn btn-sm btn-danger">Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
