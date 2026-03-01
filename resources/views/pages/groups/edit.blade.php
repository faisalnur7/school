@extends('layouts.master')

@section('contents')
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-4">
                <div class="card card-primary">
                    <div class="card-header text-white rounded-top d-flex justify-content-between align-items-center shadow p-3">
                        <h3 class="card-title">Edit Group</h3>
                    </div>

                    <form method="POST" action="{{ route('groups.update', $group->id) }}">
                        @csrf

                        <div class="card-body">
                            <div class="form-group">
                                <label>Class</label>
                                <select name="school_class_id" class="form-control" required>
                                    @foreach ($classes as $class)
                                        <option value="{{ $class->id }}"
                                            {{ $group->school_class_id == $class->id ? 'selected' : '' }}>
                                            {{ $class->name_en }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group">
                                <label>Name (English)</label>
                                <input type="text" name="name_en" class="form-control" value="{{ $group->name_en }}"
                                    required>
                            </div>

                            <div class="form-group">
                                <label>Name (Bangla)</label>
                                <input type="text" name="name_bn" class="form-control" value="{{ $group->name_bn }}"
                                    required>
                            </div>
                        </div>

                        <div class="card-footer">
                            <button class="btn btn-success">Update</button>
                            <a href="{{ route('groups.index') }}" class="btn btn-secondary">Back</a>
                        </div>
                    </form>
                </div>
            </div>
            {{-- Group List --}}
            <div class="col-md-8">
                @include('pages.groups.table')
            </div>

        </div>
    </div>
@endsection
