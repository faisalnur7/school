@php $emp = $employee ?? null; $pi = $emp?->paymentInformation; @endphp
<div class="row">
    {{-- Name --}}
    <div class="col-md-6 form-group mb-2">
        <label class="small mb-1 font-weight-bold">Full Name <span class="text-danger">*</span></label>
        <input type="text" name="name" class="form-control form-control-sm @error('name') is-invalid @enderror" value="{{ old('name', $emp?->name) }}" required>
        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    {{-- Email --}}
    <div class="col-md-6 form-group mb-2">
        <label class="small mb-1 font-weight-bold">Email <span class="text-danger">*</span></label>
        <input type="email" name="email" class="form-control form-control-sm @error('email') is-invalid @enderror" value="{{ old('email', $emp?->user?->email) }}" required>
        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    {{-- Password (create only) --}}
    @if(!$emp)
    <div class="col-md-6 form-group mb-2">
        <label class="small mb-1 font-weight-bold">Password <span class="text-danger">*</span></label>
        <input type="password" name="password" class="form-control form-control-sm @error('password') is-invalid @enderror" required>
        @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-6 form-group mb-2">
        <label class="small mb-1 font-weight-bold">Gender <span class="text-danger">*</span></label>
        <select name="gender" class="form-control form-control-sm @error('gender') is-invalid @enderror" required>
            <option value="male"   {{ old('gender', $emp?->gender) === 'male'   ? 'selected' : '' }}>Male</option>
            <option value="female" {{ old('gender', $emp?->gender) === 'female' ? 'selected' : '' }}>Female</option>
            <option value="other"  {{ old('gender', $emp?->gender) === 'other'  ? 'selected' : '' }}>Other</option>
        </select>
        @error('gender')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    @endif

    {{-- Employee Type --}}
    <div class="col-md-6 form-group mb-2">
        <label class="small mb-1 font-weight-bold">Employee Type <span class="text-danger">*</span></label>
        <select name="employee_type" id="employeeType" class="form-control form-control-sm @error('employee_type') is-invalid @enderror" required>
            <option value="">— Select —</option>
            <option value="teacher" {{ old('employee_type', $emp?->employee_type) === 'teacher' ? 'selected' : '' }}>Teacher</option>
            <option value="staff"   {{ old('employee_type', $emp?->employee_type) === 'staff'   ? 'selected' : '' }}>Staff</option>
        </select>
        @error('employee_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    {{-- Designation --}}
    <div class="col-md-6 form-group mb-2">
        <label class="small mb-1 font-weight-bold">Designation <span class="text-danger">*</span></label>
        <select name="designation_id" id="designationSelect" class="form-control form-control-sm @error('designation_id') is-invalid @enderror" required>
            <option value="">— Select Type First —</option>
            @foreach($designations as $d)
                <option value="{{ $d->id }}" data-type="{{ $d->employee_type }}" {{ old('designation_id', $emp?->designation_id) == $d->id ? 'selected' : '' }}>
                    {{ $d->name }} ({{ ucfirst($d->employee_type) }})
                </option>
            @endforeach
        </select>
        @error('designation_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    {{-- Department --}}
    <div class="col-md-6 form-group mb-2">
        <label class="small mb-1">Department</label>
        <select name="department_id" class="form-control form-control-sm @error('department_id') is-invalid @enderror">
            <option value="">— Select —</option>
            @foreach($departments as $department)
                <option value="{{ $department->id }}" {{ old('department_id', $emp?->department_id) == $department->id ? 'selected' : '' }}>{{ $department->name }}</option>
            @endforeach
        </select>
        @error('department_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    {{-- Reporting Manager --}}
    <div class="col-md-6 form-group mb-2">
        <label class="small mb-1">Reporting Manager</label>
        <select name="reporting_manager_id" class="form-control form-control-sm">
            <option value="">— None —</option>
            @foreach($managers as $m)
                <option value="{{ $m->id }}" {{ old('reporting_manager_id', $emp?->reporting_manager_id) == $m->id ? 'selected' : '' }}>
                    {{ $m->name }} ({{ $m->designation->name ?? '' }})
                </option>
            @endforeach
        </select>
    </div>

    {{-- Gender (edit only, since create has it above) --}}
    @if($emp)
    <div class="col-md-6 form-group mb-2">
        <label class="small mb-1 font-weight-bold">Gender <span class="text-danger">*</span></label>
        <select name="gender" class="form-control form-control-sm @error('gender') is-invalid @enderror" required>
            <option value="male"   {{ old('gender', $emp->gender) === 'male'   ? 'selected' : '' }}>Male</option>
            <option value="female" {{ old('gender', $emp->gender) === 'female' ? 'selected' : '' }}>Female</option>
            <option value="other"  {{ old('gender', $emp->gender) === 'other'  ? 'selected' : '' }}>Other</option>
        </select>
        @error('gender')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    @endif

    {{-- Date of Birth --}}
    <div class="col-md-6 form-group mb-2">
        <label class="small mb-1">Date of Birth</label>
        <input type="date" name="dob" class="form-control form-control-sm" value="{{ old('dob', $emp?->dob?->format('Y-m-d')) }}">
    </div>

    {{-- Phone --}}
    <div class="col-md-6 form-group mb-2">
        <label class="small mb-1">Phone</label>
        <input type="text" name="phone" class="form-control form-control-sm" value="{{ old('phone', $emp?->phone) }}">
    </div>

    {{-- Joining Date --}}
    <div class="col-md-6 form-group mb-2">
        <label class="small mb-1 font-weight-bold">Joining Date <span class="text-danger">*</span></label>
        <input type="date" name="joining_date" class="form-control form-control-sm @error('joining_date') is-invalid @enderror" value="{{ old('joining_date', $emp?->joining_date?->format('Y-m-d')) }}" required>
        @error('joining_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    {{-- Status (edit only) --}}
    @if($emp)
    <div class="col-md-6 form-group mb-2">
        <label class="small mb-1">Status</label>
        <select name="status" class="form-control form-control-sm">
            <option value="active"   {{ old('status', $emp->status) === 'active'   ? 'selected' : '' }}>Active</option>
            <option value="inactive" {{ old('status', $emp->status) === 'inactive' ? 'selected' : '' }}>Inactive</option>
        </select>
    </div>
    @endif

    {{-- Address --}}
    <div class="col-md-6 form-group mb-2">
        <label class="small mb-1">Address</label>
        <textarea name="address" class="form-control form-control-sm" rows="2">{{ old('address', $emp?->address) }}</textarea>
    </div>

    {{-- Photo --}}
    <div class="col-md-6 form-group mb-2">
        <label class="small mb-1">Photo</label>
        @if($emp?->photo)
            <div class="mb-1"><img src="{{ asset($emp->photo) }}" class="img-thumbnail" style="max-height:60px"></div>
        @endif
        <input type="file" name="photo" class="form-control-file" accept="image/*" onchange="previewPhoto(this)">
        <img id="photoPreview" src="#" class="img-thumbnail mt-1 d-none" style="max-height:60px">
        @error('photo')<div class="text-danger small">{{ $message }}</div>@enderror
    </div>
</div>

{{-- Payment Information --}}
<hr class="my-3">
<h6 class="font-weight-bold mb-3"><i class="fas fa-money-check-alt mr-1"></i> Payment Information</h6>
<div class="row">
    {{-- Payment Method --}}
    <div class="col-md-6 form-group mb-2">
        <label class="small mb-1 font-weight-bold">Payment Method</label>
        <select name="payment_method" id="paymentMethod" class="form-control form-control-sm @error('payment_method') is-invalid @enderror">
            <option value="cash"            {{ old('payment_method', $pi?->payment_method ?? 'cash') === 'cash'            ? 'selected' : '' }}>Cash</option>
            <option value="bank_transfer"   {{ old('payment_method', $pi?->payment_method) === 'bank_transfer'   ? 'selected' : '' }}>Bank Transfer</option>
            <option value="mobile_wallet"   {{ old('payment_method', $pi?->payment_method) === 'mobile_wallet'   ? 'selected' : '' }}>Mobile Wallet</option>
        </select>
        @error('payment_method')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    {{-- Bank fields --}}
    <div id="bankFields" class="col-12 row mx-0 px-0">
        <div class="col-md-6 form-group mb-2">
            <label class="small mb-1">Bank Name</label>
            <input type="text" name="bank_name" class="form-control form-control-sm @error('bank_name') is-invalid @enderror" value="{{ old('bank_name', $pi?->bank_name) }}">
            @error('bank_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-6 form-group mb-2">
            <label class="small mb-1">Account Number</label>
            <input type="text" name="account_number" class="form-control form-control-sm @error('account_number') is-invalid @enderror" value="{{ old('account_number', $pi?->account_number) }}">
            @error('account_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
    </div>

    {{-- Mobile Wallet fields --}}
    <div id="walletFields" class="col-12 row mx-0 px-0">
        <div class="col-md-6 form-group mb-2">
            <label class="small mb-1">Mobile Wallet Provider</label>
            <input type="text" name="mobile_wallet_provider" class="form-control form-control-sm @error('mobile_wallet_provider') is-invalid @enderror" value="{{ old('mobile_wallet_provider', $pi?->mobile_wallet_provider) }}" placeholder="e.g. bKash, Nagad">
            @error('mobile_wallet_provider')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-6 form-group mb-2">
            <label class="small mb-1">Mobile Wallet Number</label>
            <input type="text" name="mobile_wallet_number" class="form-control form-control-sm @error('mobile_wallet_number') is-invalid @enderror" value="{{ old('mobile_wallet_number', $pi?->mobile_wallet_number) }}">
            @error('mobile_wallet_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
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

function togglePaymentFields() {
    const method = document.getElementById('paymentMethod').value;
    document.getElementById('bankFields').style.display   = method === 'bank_transfer' ? '' : 'none';
    document.getElementById('walletFields').style.display = method === 'mobile_wallet'  ? '' : 'none';
}
document.getElementById('paymentMethod').addEventListener('change', togglePaymentFields);
togglePaymentFields();
</script>
