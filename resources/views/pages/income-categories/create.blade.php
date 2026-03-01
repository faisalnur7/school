@extends('layouts.master')

@section('contents')
<div class="container-fluid">
    <div class="row justify-content-center">

        {{-- Create Form --}}
        <div class="col-md-4">
            <div class="card">
                <div class="card-header text-white rounded-top d-flex justify-content-between align-items-center shadow p-3">
                    <h3 class="card-title">Create Income Category</h3>
                </div>

                <form method="POST" action="{{ route('income-categories.store') }}">
                    @csrf

                    <div class="card-body">

                        <div class="form-group">
                            <label>Name</label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                   value="{{ old('name') }}" required>
                            @error('name') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>

                        <div class="form-group">
                            <label>Slug <small class="text-muted">(auto-generated if empty)</small></label>
                            <input type="text" name="slug" class="form-control @error('slug') is-invalid @enderror"
                                   value="{{ old('slug') }}">
                            @error('slug') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>

                        <div class="form-group">
                            <label>Description</label>
                            <textarea name="description" class="form-control @error('description') is-invalid @enderror"
                                      rows="3">{{ old('description') }}</textarea>
                            @error('description') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>

                        <div class="form-group">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" id="is_active"
                                       name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                                <label class="custom-control-label" for="is_active">Active</label>
                            </div>
                        </div>

                    </div>

                    <div class="card-footer">
                        <button class="btn btn-success">Save</button>
                        <a href="{{ route('income-categories.index') }}" class="btn btn-secondary">Back</a>
                    </div>
                </form>
            </div>
        </div>

        {{-- Table --}}
        <div class="col-md-8">
            @include('pages.income-categories.table')
        </div>

    </div>
</div>
@endsection