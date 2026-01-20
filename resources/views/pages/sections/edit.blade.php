@extends('layouts.master')

@section('contents')
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-4">
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title">Edit Section</h3>
                    </div>

                    <form method="POST" action="{{ route('sections.update', $section->id) }}">
                        @csrf

                        <div class="card-body">
                            <div class="form-group">
                                <label>Class</label>
                                <select name="school_class_id" class="form-control" required>
                                    @foreach ($classes as $class)
                                        <option value="{{ $class->id }}"
                                            {{ $section->school_class_id == $class->id ? 'selected' : '' }}>
                                            {{ $class->name_en }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group">
                                <label>Name (English)</label>
                                <input type="text" name="name_en" class="form-control" value="{{ $section->name_en }}"
                                    required>
                            </div>

                            <div class="form-group">
                                <label>Name (Bangla)</label>
                                <input type="text" name="name_bn" class="form-control" value="{{ $section->name_bn }}"
                                    required>
                            </div>
                        </div>

                        <div class="card-footer">
                            <button class="btn btn-success">Update</button>
                            <a href="{{ route('sections.index') }}" class="btn btn-secondary">Back</a>
                        </div>
                    </form>
                </div>
            </div>
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Sections</h3>
                        <a href="{{ route('sections.create') }}" class="btn btn-primary float-right">
                            + Add Section
                        </a>
                    </div>

                    <div class="card-body">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Class</th>
                                    <th>Name (EN)</th>
                                    <th>Name (BN)</th>
                                    <th width="150">Action</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach ($sections as $section)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $section->schoolClass->name_en }}</td>
                                        <td>{{ $section->name_en }}</td>
                                        <td>{{ $section->name_bn }}</td>
                                        <td>
                                            <a href="{{ route('sections.edit', $section->id) }}"
                                                class="btn btn-sm btn-info">Edit</a>

                                            <form action="{{ route('sections.delete', $section->id) }}" method="POST"
                                                class="d-inline" onsubmit="return confirm('Delete this section?')">
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
