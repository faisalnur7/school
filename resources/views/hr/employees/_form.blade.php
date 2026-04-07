@php $emp = $employee ?? null; @endphp
<div class="row">
    <div class="col-md-4 form-group">
        <label class="font-weight-bold">Full Name <span class="text-danger">*</span></label>
        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $emp?->name) }}" required>
        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-4 form-group">
        <label class="font-weight-bold">Email <span class="text-danger">*</span></label>
        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $emp?->user?->email) }}" required>
        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    @if(!$emp)
    <div class="col-md-4 form-group">
        <label class="font-weight-bold">Password <span class="text-danger">*</span></label>
        <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" required>
        @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    @endif
    <div class="col-md-3 form-group">
        <label class="font-weight-bold">Employee Type <span class="text-danger">*</span></label>
        <select name="employee_type" id="employeeType" class="form-control @error('employee_type') is-invalid @enderror" required>
            <option value="">— Select —</option>
            <option value="teacher" {{ old('employee_type', $emp?->employee_type) === 'teacher' ? 'selected' : '' }}>Teacher</option>
            <option value="staff"   {{ old('employee_type', $emp?->employee_type) === 'staff'   ? 'selected' : '' }}>Staff</option>
        </select>
        @error('employee_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-3 form-group">
        <label class="font-weight-bold">Designation <span class="text-danger">*</span></label>
        <select name="designation_id" id="designationSelect" class="form-control @error('designation_id') is-invalid @enderror" required>
            <option value="">— Select Type First —</option>
            @foreach($designations as $d)
                <option value="{{ $d->id }}" data-type="{{ $d->employee_type }}"
                    {{ old('designation_id', $emp?->designation_id) == $d->id ? 'selected' : '' }}>
                    {{ $d->name }} ({{ ucfirst($d->employee_type) }})
                </option>
            @endforeach
        </select>
        @error('designation_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-3 form-group">
        <label>Department</label>
        <select name="department_id" class="form-control @error('department_id') is-invalid @enderror">
            <option value="">— Select —</option>
            @foreach($departments as $department)
                <option value="{{ $department->id }}" {{ old('department_id', $emp?->department_id) == $department->id ? 'selected' : '' }}>
                    {{ $department->name }}
                </option>
            @endforeach
        </select>
        @error('department_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-3 form-group">
        <label>Reporting Manager</label>
        <select name="reporting_manager_id" class="form-control">
            <option value="">— None —</option>
            @foreach($managers as $m)
                <option value="{{ $m->id }}" {{ old('reporting_manager_id', $emp?->reporting_manager_id) == $m->id ? 'selected' : '' }}>
                    {{ $m->name }} ({{ $m->designation->name ?? '' }})
                </option>
            @endforeach
        </select>
    </div>
    <div class="col-md-2 form-group">
        <label class="font-weight-bold">Gender <span class="text-danger">*</span></label>
        <select name="gender" class="form-control @error('gender') is-invalid @enderror" required>
            <option value="male"   {{ old('gender', $emp?->gender) === 'male'   ? 'selected' : '' }}>Male</option>
            <option value="female" {{ old('gender', $emp?->gender) === 'female' ? 'selected' : '' }}>Female</option>
            <option value="other"  {{ old('gender', $emp?->gender) === 'other'  ? 'selected' : '' }}>Other</option>
        </select>
        @error('gender')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-2 form-group">
        <label>Date of Birth</label>
        <input type="date" name="dob" class="form-control" value="{{ old('dob', $emp?->dob?->format('Y-m-d')) }}">
    </div>
    <div class="col-md-3 form-group">
        <label>Phone</label>
        <input type="text" name="phone" class="form-control" value="{{ old('phone', $emp?->phone) }}">
    </div>
    <div class="col-md-2 form-group">
        <label class="font-weight-bold">Joining Date <span class="text-danger">*</span></label>
        <input type="date" name="joining_date" class="form-control @error('joining_date') is-invalid @enderror" value="{{ old('joining_date', $emp?->joining_date?->format('Y-m-d')) }}" required>
        @error('joining_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    @if($emp)
    <div class="col-md-2 form-group">
        <label>Status</label>
        <select name="status" class="form-control">
            <option value="active"   {{ old('status', $emp->status) === 'active'   ? 'selected' : '' }}>Active</option>
            <option value="inactive" {{ old('status', $emp->status) === 'inactive' ? 'selected' : '' }}>Inactive</option>
        </select>
    </div>
    @endif
    <div class="col-md-12 form-group">
        <label>Address</label>
        <textarea name="address" class="form-control" rows="2">{{ old('address', $emp?->address) }}</textarea>
    </div>
    <div class="col-md-4 form-group">
        <label>Photo</label>
        @if($emp?->photo)
            <div class="mb-2"><img src="{{ asset($emp->photo) }}" class="img-thumbnail" style="max-height:80px"></div>
        @endif
        <input type="file" name="photo" class="form-control-file" accept="image/*" onchange="previewPhoto(this)">
        <img id="photoPreview" src="#" class="img-thumbnail mt-2 d-none" style="max-height:80px">
        @error('photo')<div class="text-danger small">{{ $message }}</div>@enderror
    </div>
</div>

<script>
function filterDesignations() {
    const type = document.getElementById('employeeType').value;
    document.querySelectorAll('#designationSelect option').forEach(opt => {
        if (!opt.value) return;
        opt.style.display = (!type || opt.dataset.type === type) ? '' : 'none';
    });
}
document.getElementById('employeeType').addEventListener('change', filterDesignations);
filterDesignations();

function previewPhoto(input) {
    const preview = document.getElementById('photoPreview');
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => { preview.src = e.target.result; preview.classList.remove('d-none'); };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
