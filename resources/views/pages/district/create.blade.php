@extends('layouts.master')

@section('title', 'Add New District')
@section('page_title', 'Add New District')

@section('contents')
<div class="container-fluid">
    <div class="card">
        <div class="card-header bg-gradient-dark text-white rounded-top">
            <h3 class="card-title">Add New District</h3>
        </div>
        <div class="card-body">
            <form action="{{ route('district.store') }}" method="POST">
                @csrf

                <div class="form-group">
                    <label for="division_id">Division</label>
                    <select name="division_id" class="form-control @error('division_id') is-invalid @enderror" required>
                        <option value="">Select Division</option>
                        @foreach ($divisions as $division)
                            <option value="{{ $division->id }}">{{ $division->name }}</option>
                        @endforeach
                    </select>
                    @error('division_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="form-group">
                    <label for="name">District Name (English)</label>
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" required>
                    @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="form-group">
                    <label for="bn_name">District Name (Bangla)</label>
                    <input type="text" name="bn_name" class="form-control @error('bn_name') is-invalid @enderror">
                    @error('bn_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="form-group">
                    <label for="lat">Latitude</label>
                    <input type="text" name="lat" class="form-control @error('lat') is-invalid @enderror">
                    @error('lat') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="form-group">
                    <label for="lon">Longitude</label>
                    <input type="text" name="lon" class="form-control @error('lon') is-invalid @enderror">
                    @error('lon') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="form-group">
                    <label for="url">URL</label>
                    <input type="text" name="url" class="form-control @error('url') is-invalid @enderror">
                    @error('url') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="form-group">
                    <label for="status">Status</label>
                    <select name="status" class="form-control @error('status') is-invalid @enderror">
                        <option value="1">Active</option>
                        <option value="0">Inactive</option>
                    </select>
                    @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <button type="submit" class="btn btn-success">Save</button>
                <a href="{{ route('district.index') }}" class="btn btn-secondary">Cancel</a>
            </form>
        </div>
    </div>
</div>
@endsection
