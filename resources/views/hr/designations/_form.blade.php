@php $d = $designation ?? null; @endphp
<div class="row">
    <div class="col-md-6 form-group">
        <label class="font-weight-bold">Name <span class="text-danger">*</span></label>
        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $d?->name) }}" required>
        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-3 form-group">
        <label class="font-weight-bold">Employee Type <span class="text-danger">*</span></label>
        <select name="employee_type" class="form-control @error('employee_type') is-invalid @enderror" required>
            <option value="teacher" {{ old('employee_type', $d?->employee_type) === 'teacher' ? 'selected' : '' }}>Teacher</option>
            <option value="staff"   {{ old('employee_type', $d?->employee_type) === 'staff'   ? 'selected' : '' }}>Staff</option>
        </select>
        @error('employee_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-3 form-group">
        <label class="font-weight-bold">Hierarchy Level <span class="text-danger">*</span></label>
        <input type="number" name="hierarchy_level" class="form-control @error('hierarchy_level') is-invalid @enderror" value="{{ old('hierarchy_level', $d?->hierarchy_level) }}" min="1" max="10" required>
        <small class="text-muted">Lower = higher authority</small>
        @error('hierarchy_level')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-3 form-group">
        <label class="font-weight-bold">Status</label>
        <select name="status" class="form-control">
            <option value="active"   {{ old('status', $d?->status ?? 'active') === 'active'   ? 'selected' : '' }}>Active</option>
            <option value="inactive" {{ old('status', $d?->status) === 'inactive' ? 'selected' : '' }}>Inactive</option>
        </select>
    </div>
</div>
