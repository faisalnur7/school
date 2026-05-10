@extends('layouts.master')

@section('contents')
<div class="col-md-8">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Edit Category</h3>
            <div class="card-tools">
                <a href="{{ route('inventory.categories.index') }}" class="btn btn-secondary btn-sm">
                    <i class="fas fa-list"></i> Back to List
                </a>
            </div>
        </div>
        <form method="POST" action="{{ route('inventory.categories.update', $category->id) }}">
            @csrf
            @method('PUT')
            <div class="card-body">
                @if($errors->any())
                    <div class="alert alert-danger">Please fix the errors below.</div>
                @endif

                <div class="form-group">
                    <label>Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" value="{{ old('name', $category->name) }}" class="form-control" required>
                    @error('name')<small class="text-danger">{{ $message }}</small>@enderror
                </div>

                <div class="form-group">
                    <label>Status</label>
                    <select name="is_active" class="form-control">
                        <option value="1" {{ old('is_active', $category->is_active ? '1' : '0') == '1' ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ old('is_active', $category->is_active ? '1' : '0') == '0' ? 'selected' : '' }}>Inactive</option>
                    </select>
                    @error('is_active')<small class="text-danger">{{ $message }}</small>@enderror
                </div>
            </div>
            <div class="card-footer">
                <button class="btn btn-primary">Update</button>
            </div>
        </form>
    </div>
</div>
@endsection

