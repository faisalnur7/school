@extends('layouts.master')

@section('title', 'Add New Post Office')
@section('page_title', 'Add New Post Office')

@section('contents')
<div class="container-fluid">
    <div class="card">
        <div class="card-header bg-gradient-dark text-white rounded-top">
            <h3 class="card-title">Add New Post Office</h3>
        </div>
        <div class="card-body">
            <form action="{{ route('post-office.store') }}" method="POST">
                @csrf

                <div class="form-group">
                    <label for="police_station_id">Police Station</label>
                    <select name="police_station_id" class="form-control @error('police_station_id') is-invalid @enderror" required>
                        <option value="">Select Police Station</option>
                        @foreach ($policeStations as $station)
                            <option value="{{ $station->id }}">{{ $station->name }}</option>
                        @endforeach
                    </select>
                    @error('police_station_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="form-group">
                    <label for="name">Post Office Name (English)</label>
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" required>
                    @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="form-group">
                    <label for="bn_name">Post Office Name (Bangla)</label>
                    <input type="text" name="bn_name" class="form-control @error('bn_name') is-invalid @enderror">
                    @error('bn_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="form-group">
                    <label for="postcode">Post Code</label>
                    <input type="text" name="postcode" class="form-control @error('postcode') is-invalid @enderror">
                    @error('postcode') <div class="invalid-feedback">{{ $message }}</div> @enderror
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
                <a href="{{ route('post-office.index') }}" class="btn btn-secondary">Cancel</a>
            </form>
        </div>
    </div>
</div>
@endsection
