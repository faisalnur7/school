@extends('layouts.master')

@section('contents')
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-4">
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title">Create Class</h3>
                    </div>

                    <form method="POST" action="{{ route('classes.store') }}">
                        @csrf

                        <div class="card-body">
                            <div class="form-group">
                                <label>Name (English)</label>
                                <input type="text" name="name_en" class="form-control" required>
                            </div>

                            <div class="form-group">
                                <label>Name (Bangla)</label>
                                <input type="text" name="name_bn" class="form-control" required>
                            </div>

                            <div class="form-group">
                                <label>Status</label>
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input" id="statusSwitch" name="status" value="1"
                                        checked>
                                    <label class="custom-control-label" for="statusSwitch">Active</label>
                                </div>
                            </div>
                        </div>

                        <div class="card-footer">
                            <button class="btn btn-success">Save</button>
                            <a href="{{ route('classes.index') }}" class="btn btn-secondary">Back</a>
                        </div>
                    </form>
                </div>
            </div>

            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Classes</h3>
                        <a href="{{ route('classes.create') }}" class="btn btn-primary float-right">
                            + Add Class
                        </a>
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
                                @foreach ($classes as $class)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $class->name_en }}</td>
                                        <td>{{ $class->name_bn }}</td>
                                        <td class="text-center">
                                            <form action="{{ route('classes.toggle-status', $class->id) }}" method="POST">
                                                @csrf
                                                <div class="custom-control custom-switch">
                                                    <input type="checkbox" class="custom-control-input"
                                                        id="statusSwitch{{ $class->id }}" onchange="this.form.submit()"
                                                        {{ $class->status ? 'checked' : '' }}>
                                                    <label class="custom-control-label"
                                                        for="statusSwitch{{ $class->id }}">
                                                    </label>
                                                </div>
                                            </form>
                                        </td>
                                        <td>
                                            <a href="{{ route('classes.edit', $class->id) }}"
                                                class="btn btn-sm btn-info">Edit</a>

                                            <form action="{{ route('classes.delete', $class->id) }}" method="POST"
                                                class="d-inline" onsubmit="return confirm('Delete this class?')">
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
