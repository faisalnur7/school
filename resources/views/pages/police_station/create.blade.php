@extends('layouts.master')

@section('title', 'Add New Police Station')
@section('page_title', 'Add New Police Station')

@section('contents')
<div class="container-fluid">
    <div class="card">
        <div class="card-header bg-gradient-dark text-white rounded-top">
            <h3 class="card-title">Add New Police Station</h3>
        </div>
        <div class="card-body">
            <form action="{{ route('police-station.store') }}" method="POST">
                @csrf

                <div class="form-group">
                    <label for="district_id">District</label>
                    <select name="district_id" class="form-control @error('district_id') is-invalid @enderror" required>
                        <option value="">Select District</option>
                        @foreach ($districts as $district)
                            <option value="{{ $district->id }}">{{ $district->name }}</option>
                        @endforeach
                    </select>
                    @error('district_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="form-group">
                    <label for="name">Police Station Name (English)</label>
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" required>
                    @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="form-group">
                    <label for="bn_name">Police Station Name (Bangla)</label>
                    <input type="text" name="bn_name" class="form-control @error('bn_name') is-invalid @enderror">
                    @error('bn_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="form-group">
                    <label for="url">URL (optional)</label>
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
                <a href="{{ route('police-station.index') }}" class="btn btn-secondary">Cancel</a>
            </form>
        </div>
    </div>
</div>
@endsection
