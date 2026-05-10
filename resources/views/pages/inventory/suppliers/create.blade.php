@extends('layouts.master')

@section('contents')
<div class="col-md-10">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Create Supplier</h3>
            <div class="card-tools">
                <a href="{{ route('inventory.suppliers.index') }}" class="btn btn-secondary btn-sm">
                    <i class="fas fa-list"></i> Back to List
                </a>
            </div>
        </div>
        <form method="POST" action="{{ route('inventory.suppliers.store') }}">
            @csrf
            <div class="card-body">
                @if($errors->any())
                    <div class="alert alert-danger">Please fix the errors below.</div>
                @endif

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Name <span class="text-danger">*</span></label>
                            <input name="name" value="{{ old('name') }}" class="form-control" required>
                            @error('name')<small class="text-danger">{{ $message }}</small>@enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Company Name</label>
                            <input name="company_name" value="{{ old('company_name') }}" class="form-control">
                            @error('company_name')<small class="text-danger">{{ $message }}</small>@enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Phone</label>
                            <input name="phone" value="{{ old('phone') }}" class="form-control">
                            @error('phone')<small class="text-danger">{{ $message }}</small>@enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" name="email" value="{{ old('email') }}" class="form-control">
                            @error('email')<small class="text-danger">{{ $message }}</small>@enderror
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label>Address</label>
                    <textarea name="address" class="form-control" rows="2">{{ old('address') }}</textarea>
                    @error('address')<small class="text-danger">{{ $message }}</small>@enderror
                </div>

                <div class="form-group">
                    <label>Status</label>
                    <select name="status" class="form-control">
                        <option value="1" {{ old('status', '1') == '1' ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ old('status') == '0' ? 'selected' : '' }}>Inactive</option>
                    </select>
                    @error('status')<small class="text-danger">{{ $message }}</small>@enderror
                </div>
            </div>
            <div class="card-footer">
                <button class="btn btn-primary">Save</button>
            </div>
        </form>
    </div>
</div>
@endsection

