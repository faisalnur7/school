@extends('layouts.master')

@section('contents')
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-4">
                <div class="card card-primary">
                    <div class="card-header text-white rounded-top d-flex justify-content-between align-items-center shadow p-3" style="background: linear-gradient(90deg, #343a40, #212529);">
                        <h3 class="card-title">Edit Class</h3>
                    </div>

                    <form method="POST" action="{{ route('classes.update', $class->id) }}">
                        @csrf

                        <div class="card-body">
                            <div class="form-group">
                                <label>Name (English)</label>
                                <input type="text" name="name_en" class="form-control" value="{{ $class->name_en }}"
                                    required>
                            </div>

                            <div class="form-group">
                                <label>Name (Bangla)</label>
                                <input type="text" name="name_bn" class="form-control" value="{{ $class->name_bn }}"
                                    required>
                            </div>

                            <div class="form-group">
                                <label>Status</label>
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input" id="statusSwitch" name="status" value="{{ $class->status }}"
                                        {{ $class->status ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="statusSwitch">Active</label>
                                </div>
                            </div>
                        </div>

                        <div class="card-footer">
                            <button class="btn btn-success">Update</button>
                            <a href="{{ route('classes.index') }}" class="btn btn-secondary">Back</a>
                        </div>
                    </form>
                </div>
            </div>
            <div class="col-md-8">
                @include('pages.classes.table')
            </div>
        </div>
    </div>
@endsection
