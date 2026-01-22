@extends('layouts.master')

@section('title', 'Edit District')
@section('page_title', 'Edit District')

@section('contents')
<div class="container-fluid">
    <div class="card">
        <div class="card-header bg-gradient-dark text-white rounded-top">
            <h3 class="card-title">Edit District</h3>
        </div>
        <div class="card-body">
            <form action="{{ route('district.update', $district->id) }}" method="POST">
                @csrf

                <div class="form-group">
                    <label for="division_id">Division</label>
                    <select name="division_id" class="form-control @error('division_id') is-invalid @enderror" required>
                        @foreach ($divisions as $division)
                            <option value="{{ $division->id }}" {{ $district->division_id == $division->id ? 'selected' : '' }}>
                                {{ $division->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('division_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="form-group">
                    <label for="name">District Name (English)</label>
                    <input type="text" name="name" value="{{ old('name', $district->name) }}" class="form-control @error('name') is-invalid @enderror" required>
                    @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="form-group">
                    <label for="bn_name">District Name (Bangla)</label>
                    <input type="text" name="bn_name" value="{{ old('bn_name', $district->bn_name) }}" class="form-control @error('bn_name') is-invalid @enderror">
                    @error('bn_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="form-group">
                    <label for="lat">Latitude</label>
                    <input type="text" name="lat" value="{{ old('lat', $district->lat) }}" class="form-control @error('lat') is-invalid @enderror">
                    @error('lat') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="form-group">
                    <label for="lon">Longitude</label>
                    <input type="text" name="lon" value="{{ old('lon', $district->lon) }}" class="form-control @error('lon') is-invalid @enderror">
                    @error('lon') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="form-group">
                    <label for="url">URL</label>
                    <input type="text" name="url" value="{{ old('url', $district->url) }}" class="form-control @error('url') is-invalid @enderror">
                    @error('url') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="form-group">
                    <label for="status">Status</label>
                    <select name="status" class="form-control @error('status') is-invalid @enderror">
                        <option value="1" {{ $district->status == 1 ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ $district->status == 0 ? 'selected' : '' }}>Inactive</option>
                    </select>
                    @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <button type="submit" class="btn btn-success">Update</button>
                <a href="{{ route('district.index') }}" class="btn btn-secondary">Cancel</a>
            </form>
        </div>
    </div>
</div>
@endsection
