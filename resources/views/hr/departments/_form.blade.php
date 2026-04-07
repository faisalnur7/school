@php $department = $department ?? null; @endphp
<div class="row">
    <div class="col-md-5 form-group">
        <label class="font-weight-bold">Name <span class="text-danger">*</span></label>
        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $department?->name) }}" required>
        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-4 form-group">
        <label class="font-weight-bold">Employee Type <span class="text-danger">*</span></label>
        <select name="employee_type" class="form-control @error('employee_type') is-invalid @enderror" required>
            <option value="">Select Type</option>
            <option value="teacher" {{ old('employee_type', $department?->employee_type) === 'teacher' ? 'selected' : '' }}>Teacher</option>
            <option value="staff" {{ old('employee_type', $department?->employee_type) === 'staff' ? 'selected' : '' }}>Staff</option>
        </select>
        @error('employee_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-3 form-group">
        <label class="font-weight-bold">Status</label>
        <select name="status" class="form-control @error('status') is-invalid @enderror">
            <option value="active" {{ old('status', $department?->status ?? 'active') === 'active' ? 'selected' : '' }}>Active</option>
            <option value="inactive" {{ old('status', $department?->status) === 'inactive' ? 'selected' : '' }}>Inactive</option>
        </select>
        @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
</div>