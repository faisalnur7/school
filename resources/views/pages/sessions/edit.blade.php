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
                @include('pages.sessions.table')
            </div>
        </div>
    </div>
@endsection
