@extends('layouts.master')

@section('title', 'Edit Division')
@section('page_title', 'Edit Division')

@section('contents')
<div class="container-fluid">
    <div class="card">
        <div class="card-header bg-gradient-dark text-white rounded-top">
            <h3 class="card-title">Edit Division</h3>
        </div>
        <div class="card-body">
            <form action="{{ route('division.update', $division->id) }}" method="POST">
                @csrf

                <div class="form-group">
                    <label for="name">Division Name (English)</label>
                    <input type="text" name="name" value="{{ old('name', $division->name) }}"
                           class="form-control @error('name') is-invalid @enderror" required>
                    @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="form-group">
                    <label for="bn_name">Division Name (Bangla)</label>
                    <input type="text" name="bn_name" value="{{ old('bn_name', $division->bn_name) }}"
                           class="form-control @error('bn_name') is-invalid @enderror">
                    @error('bn_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="form-group">
                    <label for="status">Status</label>
                    <select name="status" class="form-control @error('status') is-invalid @enderror">
                        <option value="1" {{ $division->status == 1 ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ $division->status == 0 ? 'selected' : '' }}>Inactive</option>
                    </select>
                    @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <button type="submit" class="btn btn-success">Update Division</button>
                <a href="{{ route('division.index') }}" class="btn btn-secondary">Cancel</a>
            </form>
        </div>
    </div>
</div>
@endsection
