@php
    $emp = $employee ?? null;
    $pi = $emp?->paymentInformation;
    $paymentMethod = old('payment_method', $pi?->payment_method ?? 'cash');
    $paymentMethod = $paymentMethod === 'bank' ? 'bank_transfer' : $paymentMethod;
    $selectedEmployeeType = old('employee_type', $emp?->employee_type);
    $assignedSessionIds = old('academic_session_ids', $emp?->academicSessions?->pluck('id')->all() ?? ($activeAcademicSessionId ? [$activeAcademicSessionId] : []));
    $qualificationRows = old('qualifications');
    if ($qualificationRows === null) {
        $qualificationRows = $emp?->qualifications?->map(fn ($item) => [
            'id' => $item->id,
            'qualification_id' => $item->qualification_id,
            'institution_name' => $item->institution_name,
            'passing_year' => $item->passing_year,
            'result' => $item->result,
            'document_original_name' => $item->document_original_name,
        ])->values()->all() ?? [];
    }
    if (empty($qualificationRows)) {
        $qualificationRows = [['id' => null, 'qualification_id' => null, 'institution_name' => null, 'passing_year' => null, 'result' => null]];
    }
@endphp

<style>
    .employee-form-page { flex: 0 0 100%; width: 100% !important; max-width: 100%; margin: 0; background: #f3f5fa; color: #0f172a; font-family: 'Plus Jakarta Sans', 'Nunito', system-ui, sans-serif; }
    .employee-form-wrap { width: 100%; max-width: none; margin: 0; padding: 1.25rem 1rem 1.25rem; }
    .employee-form-page__header { display: flex; justify-content: space-between; align-items: flex-end; gap: 1rem; flex-wrap: wrap; margin-bottom: 1rem; }
    .employee-form-page__header h1 { margin: 0; color: #0f172a; font-size: 1.875rem; font-weight: 800; letter-spacing: -.02em; }
    .employee-form-page__header p { margin: .35rem 0 0; color: #64748b; font-size: .875rem; }
    .employee-form-required { color: #e11d48; font-style: normal; }
    .employee-form-back { display: inline-flex; align-items: center; gap: .5rem; height: 42px; padding: 0 18px; border: 1px solid #dde2ef; border-radius: 12px; background: #fff; color: #0f172a; font-size: .875rem; font-weight: 700; text-decoration: none; }
    .employee-form-back:hover { color: #4338ca; text-decoration: none; }
    .employee-form-page form { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 1rem; }
    .employee-form-section { order: 1; margin-bottom: 0; overflow: hidden; border: 1px solid #e7eaf3; border-radius: 14px; background: #fff; box-shadow: 0 1px 2px rgba(15,23,42,.04), 0 8px 22px -16px rgba(15,23,42,.12); }
    .employee-form-section[aria-labelledby="employee-personal-heading"] { order: 1; }
    .employee-form-section[aria-labelledby="employee-employment-heading"] { order: 2; }
    .employee-form-section[aria-labelledby="employee-hr-heading"] { order: 3; grid-column: 1 / -1; }
    .employee-form-section[aria-labelledby="employee-qualifications-heading"] { order: 4; }
    .employee-form-section__heading { display: flex; align-items: center; gap: .65rem; min-height: 66px; padding: .85rem 1rem; border-bottom: 1px solid #eef0f6; background: linear-gradient(180deg, #fafbff, #fff); }
    .employee-form-section__heading > i { display: grid; place-items: center; flex: 0 0 34px; width: 34px; height: 34px; border-radius: 10px; color: #fff; background: linear-gradient(135deg, #4f46e5, #7c3aed); box-shadow: 0 6px 14px -6px rgba(79,70,229,.6); }
    .employee-form-section__title { margin: 0; color: #0f172a; font-size: 1.0625rem; font-weight: 700; }
    .employee-form-section__hint { margin: .2rem 0 0; color: #64748b; font-size: .8125rem; }
    .employee-form-section__step { margin-left: auto; padding: 5px 11px; border-radius: 999px; color: #4f46e5; background: #eef2ff; font-size: .75rem; font-weight: 700; white-space: nowrap; }
    .employee-form-section > .row, .employee-form-section > #employeeQualifications, .employee-form-section > .form-row { padding: 1rem; }
    .employee-form-section > #employeeQualifications { padding-bottom: 0; }
    .employee-form-section > .row { margin: 0; }
    .employee-form-section .form-group { margin-bottom: .75rem; }
    .employee-form-section .form-group:last-child { margin-bottom: 0; }
    .employee-form-section label.small { color: #1e293b; font-size: .8rem; font-weight: 700; letter-spacing: .01em; margin-bottom: .35rem; }
    .employee-form-section .form-control, .employee-form-section .form-control-file { min-height: 42px; border: 1px solid #d7dcef; border-radius: 10px; background: #fbfcff; color: #1e293b; font-size: .875rem; line-height: 1.4; transition: border-color .18s ease, box-shadow .18s ease, background-color .18s ease; }
    .employee-form-section .form-control { padding: .55rem .8rem; }
    .employee-form-section .form-control::placeholder { color: #94a3b8; opacity: 1; }
    .employee-form-section textarea.form-control { min-height: 96px; resize: vertical; }
    .employee-form-section .form-control:hover { border-color: #b9c3e1; background: #fff; }
    .employee-form-section .form-control:focus { border-color: #6366f1; background: #fff; box-shadow: 0 0 0 3px rgba(99,102,241,.14); outline: none; }
    .employee-form-section .input-group .form-control { border-radius: 10px 0 0 10px; }
    .employee-form-section .input-group-append .btn { min-height: 42px; border-color: #d7dcef; border-radius: 0 10px 10px 0; background: #f4f6ff; color: #475569; }
    .employee-form-section .input-group-append .btn:hover { border-color: #b9c3e1; background: #eef2ff; color: #4338ca; }
    .employee-form-page .select2-container { width: 100% !important; }
    .employee-form-page .select2-container--default .select2-selection--single { height: 42px; border: 1px solid #d7dcef; border-radius: 10px; background: #fbfcff; display: flex; align-items: center; padding: 0 .8rem; transition: border-color .18s ease, box-shadow .18s ease, background-color .18s ease; }
    .employee-form-page .select2-container--default.select2-sm .select2-selection--single { height: 42px; border-radius: 10px; padding: 0 .8rem; }
    .employee-form-page .select2-container--default .select2-selection--single .select2-selection__rendered { color: #1e293b; font-size: .875rem; line-height: 42px; padding: 0; }
    .employee-form-page .select2-container--default.select2-sm .select2-selection--single .select2-selection__rendered { font-size: .875rem; line-height: 42px; }
    .employee-form-page .select2-container--default .select2-selection--single .select2-selection__placeholder { color: #94a3b8; }
    .employee-form-page .select2-container--default .select2-selection--single .select2-selection__arrow { height: 42px; right: .6rem; }
    .employee-form-page .select2-container--default.select2-sm .select2-selection--single .select2-selection__arrow { height: 42px; right: .6rem; }
    .employee-form-page .select2-container--default.select2-container--open .select2-selection--single, .employee-form-page .select2-container--default.select2-container--focus .select2-selection--single { border-color: #6366f1; background: #fff; box-shadow: 0 0 0 3px rgba(99,102,241,.14); }
    .employee-form-page .select2-container--default .select2-dropdown { border: 1px solid #d7dcef; border-radius: 10px; box-shadow: 0 10px 26px rgba(15,23,42,.12); overflow: hidden; }
    .employee-form-page .select2-container--default .select2-search--dropdown .select2-search__field { border: 1px solid #d7dcef; border-radius: 8px; padding: .5rem .65rem; font-size: .875rem; }
    .employee-form-page .select2-container--default .select2-results__option { padding: .55rem .8rem; color: #334155; font-size: .875rem; }
    .employee-form-page .select2-container--default .select2-results__option--highlighted { background: #4f46e5; color: #fff; }
    .employee-form-actions { order: 5; grid-column: 1 / -1; display: flex; justify-content: space-between; align-items: center; gap: 1rem; margin: 0; padding: .75rem 0 0; border-top: 1px solid #e7eaf3; }
    .employee-form-actions > span { color: #64748b; font-size: .8125rem; }
    .employee-form-cancel, .employee-form-save { display: inline-flex; align-items: center; height: 46px; padding: 0 22px; border-radius: 12px; font-size: .875rem; font-weight: 700; text-decoration: none; }
    .employee-form-cancel { margin-right: .5rem; border: 1px solid #dde2ef; background: #fff; color: #334155; }
    .employee-form-save { border: 0; background: linear-gradient(135deg, #4f46e5, #6d5bf6); color: #fff; box-shadow: 0 12px 24px -8px rgba(79,70,229,.7); }
    .employee-form-save:hover, .employee-form-cancel:hover { text-decoration: none; }
    .employee-form-section[aria-labelledby="employee-qualifications-heading"] { grid-column: 1 / -1; }
    .employee-qualification-row { margin-bottom: .75rem !important; padding: 1rem !important; border: 1.5px dashed #cbd2e6 !important; border-radius: 12px !important; background: #f7f8fd; }
    .employee-qualification-row { position: relative; }
    .remove-qualification-row { position: absolute; top: .75rem; right: .75rem; z-index: 2; width: 30px; height: 30px; padding: 0; border: 0; border-radius: 8px; color: #64748b; background: transparent; display: inline-flex; align-items: center; justify-content: center; }
    .remove-qualification-row:hover { color: #dc2626; background: #fff1f2; }
    @media (min-width: 1200px) {
        .employee-qualification-row > .row { display: grid; grid-template-columns: repeat(12, minmax(0, 1fr)); gap: .75rem; margin: 0; }
        .employee-qualification-row > .row > [class*="col-"] { width: auto; max-width: none; flex: none; padding: 0; }
        .employee-qualification-row > .row .form-group { margin-bottom: 0; }
        .employee-qualification-row > .row > :nth-child(1) { grid-column: span 3; }
        .employee-qualification-row > .row > :nth-child(2) { grid-column: span 3; }
        .employee-qualification-row > .row > :nth-child(3) { grid-column: span 2; }
        .employee-qualification-row > .row > :nth-child(4) { grid-column: span 4; }
        .employee-qualification-row > .row > :nth-child(5) { grid-column: 1 / 12; grid-row: 2; }
        .employee-qualification-row > .row > :nth-child(6) { grid-column: 12; grid-row: 2; align-self: end; }
        .employee-qualification-row > .row .remove-qualification-row { width: 40px; height: 40px; min-height: 40px; padding: 0; display: inline-flex; align-items: center; justify-content: center; white-space: nowrap; }
    }
    #addQualificationRow { margin: 0 1rem 1rem; height: 38px; padding: 0 14px; border: 1.5px solid #c7d2fe; border-radius: 10px; color: #4338ca; background: #fff; font-weight: 700; }
    .employee-photo-upload { margin: 1rem; padding: 1rem; border: 2px dashed #c7d2fe; border-radius: 12px; background: #f5f7ff; text-align: center; }
    .employee-photo-upload input[type=file] { display: inline-block; max-width: 100%; color: #64748b; font-size: .8125rem; }
    .employee-photo-upload input[type=file]::file-selector-button { margin-right: .75rem; padding: .5rem .875rem; border: 0; border-radius: 8px; color: #3730a3; background: #eef2ff; font-weight: 700; cursor: pointer; }
    .employee-photo-upload__hint { color: #64748b; font-size: .75rem; }
    .employee-photo-upload__error { margin-top: .5rem; color: #dc2626; font-size: .8rem; font-weight: 600; }
    .employee-employment-photo { margin: 0; padding: 0 1rem 1rem; }
    .employee-employment-photo .employee-photo-upload { margin: .35rem 0 0; padding: 0; border: 0; background: transparent; text-align: left; }
    .employee-employment-photo .employee-photo-upload .employee-file-dropzone__surface { min-height: 58px; }
    .employee-employment-photo .employee-file-dropzone__filename, .employee-employment-photo .employee-photo-upload__hint { text-align: center; }
    .qualification-document-hint { margin-top: .35rem; color: #64748b; font-size: .7rem; }
    .qualification-document-error { margin-top: .35rem; color: #dc2626; font-size: .75rem; font-weight: 600; }
    .employee-file-dropzone { width: 100%; }
    .employee-file-dropzone__input { position: absolute; width: 1px; height: 1px; overflow: hidden; clip: rect(0 0 0 0); white-space: nowrap; clip-path: inset(50%); }
    .employee-file-dropzone__surface { display: flex; align-items: center; justify-content: center; gap: .6rem; min-height: 58px; padding: .65rem .8rem; border: 1.5px dashed #c7d2fe; border-radius: 10px; color: #475569; background: #f8faff; cursor: pointer; transition: border-color .18s ease, background-color .18s ease, box-shadow .18s ease; }
    .employee-file-dropzone__surface:hover, .employee-file-dropzone.is-dragging .employee-file-dropzone__surface, .employee-file-dropzone__input:focus-visible + .employee-file-dropzone__surface { border-color: #6366f1; background: #eef2ff; box-shadow: 0 0 0 3px rgba(99,102,241,.12); }
    .employee-file-dropzone__surface i { color: #4f46e5; }
    .employee-file-dropzone__surface strong { color: #334155; font-size: .78rem; }
    .employee-file-dropzone__surface small { display: block; color: #64748b; font-size: .68rem; }
    .employee-file-dropzone__filename { margin-top: .3rem; overflow: hidden; color: #64748b; font-size: .7rem; text-overflow: ellipsis; white-space: nowrap; }
    .employee-file-dropzone__preview { display: none; align-items: center; gap: .5rem; margin-top: .5rem; padding: .4rem .55rem; border: 1px solid #dbe2f1; border-radius: 8px; color: #475569; background: #fff; font-size: .72rem; }
    .employee-file-dropzone__preview.is-visible { display: flex; }
    .employee-file-dropzone__preview img { width: 42px; height: 42px; border-radius: 6px; object-fit: cover; }
    .employee-file-dropzone__preview i { color: #4f46e5; font-size: 1rem; }
    .employee-file-dropzone__remove { margin-left: auto; padding: .2rem .45rem; border: 0; border-radius: 6px; color: #dc2626; background: #fff1f2; font-size: .7rem; cursor: pointer; }
    .employee-file-dropzone__remove:hover { background: #ffe4e6; }
    .qualification-document-dropzone .employee-file-dropzone__surface { min-height: 42px; justify-content: flex-start; padding: .45rem .65rem; }
    .qualification-document-dropzone .employee-file-dropzone__surface strong { font-size: .75rem; }
    .qualification-document-dropzone .employee-file-dropzone__surface small { display: none; }
    @media (max-width: 900px) { .employee-form-wrap { padding: 1.25rem 1rem 2rem; } .employee-form-page form { grid-template-columns: 1fr; } .employee-form-section[aria-labelledby="employee-qualifications-heading"], .employee-form-actions { grid-column: auto; } }
    @media (max-width: 576px) { .employee-form-actions { align-items: flex-start; flex-direction: column; } .employee-form-actions > div { width: 100%; display: flex; } .employee-form-cancel, .employee-form-save { flex: 1; justify-content: center; } .employee-form-section__heading { padding-left: 1rem; padding-right: 1rem; } .employee-form-section > .row, .employee-form-section > #employeeQualifications { padding-left: 1rem; padding-right: 1rem; } }
</style>

<section class="employee-form-section" aria-labelledby="employee-personal-heading">
    <div class="employee-form-section__heading"><i class="fas fa-user" aria-hidden="true"></i><div><h5 id="employee-personal-heading" class="employee-form-section__title">Personal Information</h5><p class="employee-form-section__hint">Basic identity and account details.</p></div><span class="employee-form-section__step">1 / 4</span></div>
    <div class="row">
        <div class="col-md-6 form-group"><label class="small mb-1 font-weight-bold">Full Name <span class="text-danger">*</span></label><input type="text" name="name" class="form-control form-control-sm @error('name') is-invalid @enderror" value="{{ old('name', $emp?->name) }}" required>@error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
        <div class="col-md-6 form-group"><label class="small mb-1 font-weight-bold">Email <span class="text-danger">*</span></label><input type="email" name="email" class="form-control form-control-sm @error('email') is-invalid @enderror" value="{{ old('email', $emp?->user?->email) }}" required>@error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
        @if(!$emp)
        <div class="col-md-6 form-group"><label class="small mb-1 font-weight-bold" for="employeePassword">Password <span class="text-danger">*</span></label><div class="input-group input-group-sm"><input id="employeePassword" type="password" name="password" class="form-control @error('password') is-invalid @enderror" required><div class="input-group-append"><button type="button" class="btn btn-outline-secondary toggle-password" data-target="employeePassword" aria-label="Show password"><i class="fas fa-eye"></i></button></div></div>@error('password')<div class="text-danger small">{{ $message }}</div>@enderror</div>
        <div class="col-md-6 form-group"><label class="small mb-1 font-weight-bold" for="employeePasswordConfirmation">Confirm Password <span class="text-danger">*</span></label><div class="input-group input-group-sm"><input id="employeePasswordConfirmation" type="password" name="password_confirmation" class="form-control @error('password_confirmation') is-invalid @enderror" required><div class="input-group-append"><button type="button" class="btn btn-outline-secondary toggle-password" data-target="employeePasswordConfirmation" aria-label="Show password"><i class="fas fa-eye"></i></button></div></div>@error('password_confirmation')<div class="text-danger small">{{ $message }}</div>@enderror</div>
        @endif
        <div class="col-md-6 form-group"><label class="small mb-1 font-weight-bold" for="employeeGender">Gender <span class="text-danger">*</span></label><select id="employeeGender" name="gender" class="form-control form-control-sm @error('gender') is-invalid @enderror" required><option value="">— Select gender —</option><option value="male" {{ old('gender', $emp?->gender) === 'male' ? 'selected' : '' }}>Male</option><option value="female" {{ old('gender', $emp?->gender) === 'female' ? 'selected' : '' }}>Female</option><option value="other" {{ old('gender', $emp?->gender) === 'other' ? 'selected' : '' }}>Other</option></select>@error('gender')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
        <div class="col-md-6 form-group"><label class="small mb-1" for="employeeDob">Date of Birth</label><input id="employeeDob" type="date" name="dob" class="form-control form-control-sm" value="{{ old('dob', $emp?->dob?->format('Y-m-d')) }}"></div>
    </div>
</section>

<section class="employee-form-section" aria-labelledby="employee-employment-heading">
    <div class="employee-form-section__heading"><i class="fas fa-briefcase" aria-hidden="true"></i><div><h5 id="employee-employment-heading" class="employee-form-section__title">Employment Information</h5><p class="employee-form-section__hint">Role, reporting structure, teaching-session access, payment, and profile photo.</p></div><span class="employee-form-section__step">2 / 4</span></div>
    <div class="row">
        <div class="col-md-6 form-group"><label class="small mb-1 font-weight-bold" for="employeeType">Employee Type <span class="text-danger">*</span></label><select name="employee_type" id="employeeType" class="form-control form-control-sm @error('employee_type') is-invalid @enderror" required><option value="">— Select —</option><option value="teacher" {{ old('employee_type', $emp?->employee_type) === 'teacher' ? 'selected' : '' }}>Teacher</option><option value="staff" {{ old('employee_type', $emp?->employee_type) === 'staff' ? 'selected' : '' }}>Staff</option></select>@error('employee_type')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
        <div class="col-md-6 form-group"><label class="small mb-1 font-weight-bold" for="designationSelect">Designation <span class="text-danger">*</span></label><select name="designation_id" id="designationSelect" class="form-control form-control-sm @error('designation_id') is-invalid @enderror" required><option value="">— Select Type First —</option>@foreach($designations as $d)<option value="{{ $d->id }}" data-type="{{ $d->employee_type }}" {{ old('designation_id', $emp?->designation_id) == $d->id ? 'selected' : '' }}>{{ $d->name }} ({{ ucfirst($d->employee_type) }})</option>@endforeach</select>@error('designation_id')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
        <div class="col-md-6 form-group"><label class="small mb-1" for="employeeDepartment">Department</label><select id="employeeDepartment" name="department_id" class="form-control form-control-sm @error('department_id') is-invalid @enderror"><option value="">— Select —</option>@foreach($departments as $department)<option value="{{ $department->id }}" {{ old('department_id', $emp?->department_id) == $department->id ? 'selected' : '' }}>{{ $department->name }}</option>@endforeach</select>@error('department_id')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
        <div class="col-md-6 form-group"><label class="small mb-1" for="employeeManager">Reporting Manager</label><select id="employeeManager" name="reporting_manager_id" class="form-control form-control-sm @error('reporting_manager_id') is-invalid @enderror"><option value="">— None —</option>@foreach($managers as $m)<option value="{{ $m->id }}" {{ old('reporting_manager_id', $emp?->reporting_manager_id) == $m->id ? 'selected' : '' }}>{{ $m->name }} ({{ $m->designation->name ?? '' }})</option>@endforeach</select>@error('reporting_manager_id')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
        <div class="col-md-6 form-group"><label class="small mb-1 font-weight-bold" for="employeeJoiningDate">Joining Date <span class="text-danger">*</span></label><input id="employeeJoiningDate" type="date" name="joining_date" class="form-control form-control-sm @error('joining_date') is-invalid @enderror" value="{{ old('joining_date', $emp?->joining_date?->format('Y-m-d')) }}" required>@error('joining_date')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
        <div class="col-md-6 form-group"><label class="small mb-1 font-weight-bold" for="paymentMethod">Payment Method <span class="text-danger">*</span></label><select name="payment_method" id="paymentMethod" class="form-control form-control-sm @error('payment_method') is-invalid @enderror" required><option value="cash" {{ $paymentMethod === 'cash' ? 'selected' : '' }}>Cash</option><option value="bank_transfer" {{ $paymentMethod === 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option><option value="mobile_wallet" {{ $paymentMethod === 'mobile_wallet' ? 'selected' : '' }}>Mobile Wallet</option></select>@error('payment_method')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
        <div id="bankFields" class="col-12 row mx-0 px-0"><div class="col-md-6 form-group"><label class="small mb-1">Bank Name</label><input type="text" name="bank_name" class="form-control form-control-sm @error('bank_name') is-invalid @enderror" value="{{ old('bank_name', $pi?->bank_name) }}">@error('bank_name')<div class="invalid-feedback">{{ $message }}</div>@enderror</div><div class="col-md-6 form-group"><label class="small mb-1">Account Number</label><input type="text" name="account_number" class="form-control form-control-sm @error('account_number') is-invalid @enderror" value="{{ old('account_number', $pi?->account_number) }}">@error('account_number')<div class="invalid-feedback">{{ $message }}</div>@enderror</div></div>
        <div id="walletFields" class="col-12 row mx-0 px-0"><div class="col-md-6 form-group"><label class="small mb-1">Mobile Wallet Provider</label><input type="text" name="mobile_wallet_provider" class="form-control form-control-sm @error('mobile_wallet_provider') is-invalid @enderror" value="{{ old('mobile_wallet_provider', $pi?->mobile_wallet_provider) }}" placeholder="e.g. bKash, Nagad">@error('mobile_wallet_provider')<div class="invalid-feedback">{{ $message }}</div>@enderror</div><div class="col-md-6 form-group"><label class="small mb-1">Mobile Wallet Number</label><input type="text" name="mobile_wallet_number" class="form-control form-control-sm @error('mobile_wallet_number') is-invalid @enderror" value="{{ old('mobile_wallet_number', $pi?->mobile_wallet_number) }}">@error('mobile_wallet_number')<div class="invalid-feedback">{{ $message }}</div>@enderror</div></div>
        @if($emp)<div class="col-md-6 form-group"><label class="small mb-1">Status</label><select name="status" class="form-control form-control-sm"><option value="active" {{ old('status', $emp->status) === 'active' ? 'selected' : '' }}>Active</option><option value="inactive" {{ old('status', $emp->status) === 'inactive' ? 'selected' : '' }}>Inactive</option></select></div>@endif
        <div class="col-12 form-group" id="teacherAcademicSessions" style="display: {{ $selectedEmployeeType === 'teacher' ? '' : 'none' }}"><label class="small mb-1 font-weight-bold">Teaching Academic Sessions <span class="text-danger">*</span></label><div class="d-flex flex-wrap">@foreach($academicSessions as $session)<label class="mr-3 mb-1 font-weight-normal"><input type="checkbox" name="academic_session_ids[]" value="{{ $session->id }}" {{ in_array($session->id, $assignedSessionIds) ? 'checked' : '' }}> {{ $session->name_en }} @if($session->status)<small class="text-success">(Active)</small>@endif</label>@endforeach</div><small class="form-text text-muted">Teachers are selectable in routines only for the sessions selected here.</small>@error('academic_session_ids')<div class="text-danger small">{{ $message }}</div>@enderror @error('academic_session_ids.*')<div class="text-danger small">{{ $message }}</div>@enderror</div>
    </div>
    <div class="employee-employment-photo">
        @if($emp?->photo)<div class="mb-2"><img src="{{ asset($emp->photo) }}" class="img-thumbnail" style="max-height:80px" alt="Current employee photo"></div>@endif
        <label class="small mb-1" for="employeePhoto">Profile Photo</label>
        <div class="employee-photo-upload employee-file-dropzone" data-file-dropzone><input id="employeePhoto" type="file" name="photo" class="employee-file-dropzone__input" accept="image/jpeg,image/png,image/webp" onchange="validateAndPreviewPhoto(this)"><label class="employee-file-dropzone__surface" for="employeePhoto"><i class="fas fa-cloud-upload-alt" aria-hidden="true"></i><span><strong>Drop a profile photo or choose one</strong><small>JPEG, PNG, or WebP · 2 MB max</small></span></label><div class="employee-file-dropzone__filename">No file chosen</div><small class="employee-photo-upload__hint d-block mt-2">Maximum 2 MB. JPEG, PNG, or WebP only.</small><div id="employeePhotoError" class="employee-photo-upload__error" role="alert" aria-live="polite"></div><div id="photoPreview" class="employee-file-dropzone__preview" data-file-preview></div></div>
        @error('photo')<div class="text-danger small mt-2">{{ $message }}</div>@enderror
    </div>
</section>

<section class="employee-form-section" aria-labelledby="employee-qualifications-heading">
    <div class="employee-form-section__heading"><i class="fas fa-graduation-cap" aria-hidden="true"></i><div><h5 id="employee-qualifications-heading" class="employee-form-section__title">Educational Qualifications</h5><p class="employee-form-section__hint">Select from the Qualifications catalog and add the employee’s academic details.</p></div><span class="employee-form-section__step">4 / 4</span></div>
    <div id="employeeQualifications">
        @foreach($qualificationRows as $index => $row)
        <div class="employee-qualification-row border rounded p-3 mb-3" data-qualification-row>
            <input type="hidden" name="qualifications[{{ $index }}][id]" value="{{ $row['id'] ?? '' }}">
            <button type="button" class="remove-qualification-row" aria-label="Remove qualification" title="Remove qualification"><i class="fas fa-times" aria-hidden="true"></i></button>
            <div class="row align-items-end">
                <div class="col-lg-4 form-group"><label class="small mb-1 font-weight-bold" for="qualification_{{ $index }}">Qualification <span class="text-danger">*</span></label><select id="qualification_{{ $index }}" name="qualifications[{{ $index }}][qualification_id]" class="form-control form-control-sm @error('qualifications.'.$index.'.qualification_id') is-invalid @enderror"><option value="">— Select qualification —</option>@foreach($qualificationOptions as $qualification)<option value="{{ $qualification->id }}" {{ (string)($row['qualification_id'] ?? '') === (string)$qualification->id ? 'selected' : '' }}>{{ $qualification->display_name }}</option>@endforeach</select>@error('qualifications.'.$index.'.qualification_id')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                <div class="col-lg-4 form-group"><label class="small mb-1" for="qualification_institution_{{ $index }}">Institution</label><input id="qualification_institution_{{ $index }}" type="text" name="qualifications[{{ $index }}][institution_name]" class="form-control form-control-sm" value="{{ $row['institution_name'] ?? '' }}" placeholder="School, college, or university"></div>
                <div class="col-md-4 col-lg-2 form-group"><label class="small mb-1" for="qualification_year_{{ $index }}">Passing Year</label><input id="qualification_year_{{ $index }}" type="number" name="qualifications[{{ $index }}][passing_year]" class="form-control form-control-sm" min="1900" max="{{ now()->year }}" value="{{ $row['passing_year'] ?? '' }}"></div>
                <div class="col-md-4 col-lg-2 form-group"><label class="small mb-1" for="qualification_result_{{ $index }}">Result / Grade</label><input id="qualification_result_{{ $index }}" type="text" name="qualifications[{{ $index }}][result]" class="form-control form-control-sm" value="{{ $row['result'] ?? '' }}" placeholder="e.g. First Class"></div>
                <div class="col-lg-8 form-group mb-lg-0 qualification-document-dropzone"><label class="small mb-1" for="qualification_document_{{ $index }}">Certificate / Document</label><div class="employee-file-dropzone" data-file-dropzone><input id="qualification_document_{{ $index }}" type="file" name="qualifications[{{ $index }}][document]" class="employee-file-dropzone__input qualification-document-input" accept=".pdf,.jpg,.jpeg,.png,.webp" onchange="validateQualificationDocument(this)"><label class="employee-file-dropzone__surface" for="qualification_document_{{ $index }}"><i class="fas fa-cloud-upload-alt" aria-hidden="true"></i><span><strong>Drop file or choose</strong><small>PDF, JPEG, PNG, or WebP · 2 MB max</small></span></label><div class="employee-file-dropzone__filename">No file chosen</div><div class="employee-file-dropzone__preview" data-file-preview></div><small class="qualification-document-hint d-block">Maximum 2 MB. PDF, JPEG, PNG, or WebP only.</small><div class="qualification-document-error" role="alert" aria-live="polite"></div>@if(!empty($row['document_original_name']))<small class="form-text text-muted">Current: {{ $row['document_original_name'] }} <label class="ml-2"><input type="checkbox" name="qualifications[{{ $index }}][remove_document]" value="1"> Remove</label></small>@endif</div></div>
            </div>
        </div>
        @endforeach
    </div>
    <button type="button" id="addQualificationRow" class="btn btn-outline-primary btn-sm"><i class="fas fa-plus mr-1"></i>Add Qualification</button>
    @error('qualifications')<div class="text-danger small mt-2">{{ $message }}</div>@enderror
</section>

<section class="employee-form-section" aria-labelledby="employee-hr-heading">
    <div class="employee-form-section__heading"><i class="fas fa-id-card" aria-hidden="true"></i><div><h5 id="employee-hr-heading" class="employee-form-section__title">HR &amp; Contact Information</h5><p class="employee-form-section__hint">Identification, contact details, emergency contact, and internal notes.</p></div><span class="employee-form-section__step">3 / 4</span></div>
    <div class="row">
        <div class="col-md-6 form-group"><label class="small mb-1" for="employeeNationalId">National ID</label><input id="employeeNationalId" type="text" name="national_id" class="form-control form-control-sm @error('national_id') is-invalid @enderror" value="{{ old('national_id', $emp?->national_id) }}">@error('national_id')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
        <div class="col-md-6 form-group"><label class="small mb-1" for="employeePassportNumber">Passport Number</label><input id="employeePassportNumber" type="text" name="passport_number" class="form-control form-control-sm @error('passport_number') is-invalid @enderror" value="{{ old('passport_number', $emp?->passport_number) }}">@error('passport_number')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
        <div class="col-md-6 form-group"><label class="small mb-1" for="employeePhone">Phone</label><input id="employeePhone" type="text" name="phone" class="form-control form-control-sm" value="{{ old('phone', $emp?->phone) }}"></div>
        <div class="col-md-6 form-group"><label class="small mb-1" for="emergencyContactName">Emergency Contact</label><input id="emergencyContactName" type="text" name="emergency_contact_name" class="form-control form-control-sm @error('emergency_contact_name') is-invalid @enderror" value="{{ old('emergency_contact_name', $emp?->emergency_contact_name) }}" placeholder="Full name">@error('emergency_contact_name')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
        <div class="col-md-6 form-group"><label class="small mb-1" for="emergencyContactPhone">Emergency Phone</label><input id="emergencyContactPhone" type="text" name="emergency_contact_phone" class="form-control form-control-sm @error('emergency_contact_phone') is-invalid @enderror" value="{{ old('emergency_contact_phone', $emp?->emergency_contact_phone) }}">@error('emergency_contact_phone')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
        <div class="col-md-6 form-group"><label class="small mb-1" for="emergencyContactRelation">Relationship</label><input id="emergencyContactRelation" type="text" name="emergency_contact_relation" class="form-control form-control-sm @error('emergency_contact_relation') is-invalid @enderror" value="{{ old('emergency_contact_relation', $emp?->emergency_contact_relation) }}" placeholder="e.g. Brother">@error('emergency_contact_relation')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
        <div class="col-12 form-group"><label class="small mb-1" for="employeeAddress">Address</label><textarea id="employeeAddress" name="address" class="form-control form-control-sm" rows="2">{{ old('address', $emp?->address) }}</textarea></div>
    </div>
</section>

<script>
function filterDesignations() {
    const type = document.getElementById('employeeType').value;
    document.querySelectorAll('#designationSelect option').forEach(opt => { if (!opt.value) return; opt.style.display = (!type || opt.dataset.type === type) ? '' : 'none'; });
}
document.getElementById('employeeType').addEventListener('change', filterDesignations);
document.getElementById('employeeType').addEventListener('change', function () {
    const selected = document.querySelector('#designationSelect option:checked');
    if (selected && selected.value && selected.dataset.type !== this.value) {
        document.getElementById('designationSelect').value = '';
    }
});
filterDesignations();
function toggleTeacherAcademicSessions() {
    const select = document.getElementById('employeeType');
    const select2Value = window.jQuery && jQuery.fn.select2 && jQuery(select).hasClass('select2-hidden-accessible')
        ? jQuery(select).select2('data')[0]?.id
        : null;
    const isTeacher = String(select2Value || select.value || '').toLowerCase() === 'teacher';
    const group = document.getElementById('teacherAcademicSessions');
    if (!group) return;
    group.style.display = isTeacher ? '' : 'none';
    group.querySelectorAll('input[name="academic_session_ids[]"]').forEach(input => { input.disabled = !isTeacher; });
}
document.getElementById('employeeType').addEventListener('change', toggleTeacherAcademicSessions);
if (window.jQuery) {
    jQuery('#employeeType').on('select2:select select2:clear', toggleTeacherAcademicSessions);
}
toggleTeacherAcademicSessions();
window.addEventListener('load', toggleTeacherAcademicSessions);
function previewPhoto(input) {
    updateFilePreview(input);
}
function clearFilePreview(input) {
    const preview = input.closest('[data-file-dropzone]')?.querySelector('[data-file-preview]');
    if (!preview) return;
    preview.innerHTML = '';
    preview.classList.remove('is-visible');
    preview.classList.add('d-none');
}
function updateFilePreview(input) {
    const file = input.files && input.files[0];
    const preview = input.closest('[data-file-dropzone]')?.querySelector('[data-file-preview]');
    if (!preview) return;
    if (!file) { clearFilePreview(input); return; }
    preview.classList.remove('d-none');
    preview.classList.add('is-visible');
    preview.replaceChildren();
    if (file.type.startsWith('image/')) {
        const reader = new FileReader();
        reader.onload = event => {
            const image = document.createElement('img');
            image.src = event.target.result;
            image.alt = 'Selected file preview';
            const name = document.createElement('span');
            name.textContent = file.name;
            preview.replaceChildren(image, name, createFileRemoveButton());
        };
        reader.readAsDataURL(file);
    } else {
        const icon = document.createElement('i');
        icon.className = 'fas fa-file-pdf';
        icon.setAttribute('aria-hidden', 'true');
        const name = document.createElement('span');
        name.textContent = file.name;
        preview.replaceChildren(icon, name, createFileRemoveButton());
    }
}
function createFileRemoveButton() {
    const button = document.createElement('button');
    button.type = 'button';
    button.className = 'employee-file-dropzone__remove';
    button.setAttribute('aria-label', 'Remove selected file');
    button.title = 'Remove selected file';
    button.innerHTML = '<i class="fas fa-times" aria-hidden="true"></i>';
    return button;
}
function validateAndPreviewPhoto(input) {
    const error = document.getElementById('employeePhotoError');
    const file = input.files && input.files[0];
    const allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
    const maxBytes = 2 * 1024 * 1024;
    error.textContent = '';
    input.setCustomValidity('');
    if (!file) {
        previewPhoto(input);
        return;
    }
    if (!allowedTypes.includes(file.type) || file.size > maxBytes) {
        const reason = !allowedTypes.includes(file.type) ? 'Only JPEG, PNG, or WebP images are allowed.' : 'The photo must be 2 MB or smaller.';
        error.textContent = reason;
        input.setCustomValidity(reason);
        input.value = '';
        clearFilePreview(input);
        return;
    }
    previewPhoto(input);
}
function validateQualificationDocument(input) {
    const error = input.parentElement.querySelector('.qualification-document-error');
    const file = input.files && input.files[0];
    const allowedTypes = ['application/pdf', 'image/jpeg', 'image/png', 'image/webp'];
    const allowedExtensions = ['pdf', 'jpg', 'jpeg', 'png', 'webp'];
    const maxBytes = 2 * 1024 * 1024;
    const extension = file ? file.name.split('.').pop().toLowerCase() : '';
    error.textContent = '';
    input.setCustomValidity('');
    if (!file) return;
    if ((!allowedTypes.includes(file.type) && !allowedExtensions.includes(extension)) || file.size > maxBytes) {
        const reason = ((!allowedTypes.includes(file.type) && !allowedExtensions.includes(extension)))
            ? 'Only PDF, JPEG, PNG, or WebP files are allowed.'
            : 'The certificate or document must be 2 MB or smaller.';
        error.textContent = reason;
        input.setCustomValidity(reason);
        input.value = '';
        clearFilePreview(input);
        return;
    }
    updateFilePreview(input);
}
function initEmployeeFileDropzone(dropzone) {
    if (dropzone.dataset.dropzoneReady === '1') return;
    const input = dropzone.querySelector('input[type="file"]');
    const filename = dropzone.querySelector('.employee-file-dropzone__filename');
    if (!input) return;
    const updateFilename = () => { if (filename) filename.textContent = input.files && input.files[0] ? input.files[0].name : 'No file chosen'; };
    input.addEventListener('change', updateFilename);
    dropzone.addEventListener('click', event => {
        if (!event.target.closest('.employee-file-dropzone__remove')) return;
        event.preventDefault();
        input.value = '';
        input.setCustomValidity('');
        const error = dropzone.querySelector('.qualification-document-error, .employee-photo-upload__error');
        if (error) error.textContent = '';
        clearFilePreview(input);
        updateFilename();
    });
    ['dragenter', 'dragover'].forEach(eventName => dropzone.addEventListener(eventName, event => { event.preventDefault(); dropzone.classList.add('is-dragging'); }));
    ['dragleave', 'drop'].forEach(eventName => dropzone.addEventListener(eventName, event => { event.preventDefault(); dropzone.classList.remove('is-dragging'); }));
    dropzone.addEventListener('drop', event => {
        if (!event.dataTransfer.files.length) return;
        input.files = event.dataTransfer.files;
        input.dispatchEvent(new Event('change', { bubbles: true }));
    });
    dropzone.dataset.dropzoneReady = '1';
}
document.querySelectorAll('[data-file-dropzone]').forEach(initEmployeeFileDropzone);
function togglePaymentFields() {
    const method = document.getElementById('paymentMethod').value;
    document.getElementById('bankFields').style.display = method === 'bank_transfer' ? '' : 'none';
    document.getElementById('walletFields').style.display = method === 'mobile_wallet' ? '' : 'none';
    document.querySelectorAll('#bankFields input').forEach(input => { input.disabled = method !== 'bank_transfer'; });
    document.querySelectorAll('#walletFields input').forEach(input => { input.disabled = method !== 'mobile_wallet'; });
}
document.getElementById('paymentMethod').addEventListener('change', togglePaymentFields);
togglePaymentFields();

document.querySelectorAll('.toggle-password').forEach(button => {
    button.addEventListener('click', () => {
        const input = document.getElementById(button.dataset.target);
        const showing = input.type === 'text';
        input.type = showing ? 'password' : 'text';
        button.setAttribute('aria-label', showing ? 'Show password' : 'Hide password');
        button.querySelector('i').className = showing ? 'fas fa-eye' : 'fas fa-eye-slash';
    });
});

let qualificationIndex = document.querySelectorAll('[data-qualification-row]').length;
document.getElementById('addQualificationRow').addEventListener('click', () => {
    const template = document.querySelector('[data-qualification-row]').cloneNode(true);
    template.querySelectorAll('.select2-container').forEach(element => element.remove());
    template.querySelectorAll('[data-file-dropzone]').forEach(dropzone => {
        delete dropzone.dataset.dropzoneReady;
    });
    template.querySelectorAll('input[type="file"]').forEach(input => {
        const cleanInput = document.createElement('input');
        Array.from(input.attributes).forEach(attribute => cleanInput.setAttribute(attribute.name, attribute.value));
        cleanInput.value = '';
        input.replaceWith(cleanInput);
    });
    template.querySelectorAll('.employee-file-dropzone__filename').forEach(filename => {
        filename.textContent = 'No file chosen';
    });
    template.querySelectorAll('[data-file-preview]').forEach(preview => {
        preview.innerHTML = '';
        preview.classList.remove('is-visible');
        preview.classList.add('d-none');
    });
    template.querySelectorAll('.qualification-document-error, .employee-photo-upload__error').forEach(error => {
        error.textContent = '';
    });
    template.querySelectorAll('select.select2-hidden-accessible').forEach(select => {
        select.classList.remove('select2-hidden-accessible');
        select.removeAttribute('data-select2-id');
        select.removeAttribute('style');
        select.removeAttribute('aria-hidden');
        select.querySelectorAll('[data-select2-id]').forEach(option => option.removeAttribute('data-select2-id'));
    });
    template.querySelectorAll('input, select').forEach(input => {
        input.name = input.name.replace(/qualifications\[\d+\]/, `qualifications[${qualificationIndex}]`);
        input.id = input.id.replace(/_\d+$/, `_${qualificationIndex}`);
        if (input.type === 'hidden' || input.type !== 'file') input.value = '';
        if (input.type === 'file') input.value = '';
        if (input.type === 'checkbox') input.checked = false;
    });
    template.querySelectorAll('label[for]').forEach(label => { label.htmlFor = label.htmlFor.replace(/_\d+$/, `_${qualificationIndex}`); });
    template.querySelectorAll('small.form-text').forEach(element => element.remove());
    document.getElementById('employeeQualifications').appendChild(template);
    template.querySelectorAll('[data-file-dropzone]').forEach(initEmployeeFileDropzone);
    if (window.jQuery && typeof jQuery.fn.select2 === 'function') {
        jQuery(template).find('select').select2({
            width: '100%',
            allowClear: true,
            placeholder: '— Select qualification —',
            dropdownParent: jQuery(document.body)
        });
    }
    qualificationIndex++;
});
document.getElementById('employeeQualifications').addEventListener('click', event => {
    const button = event.target.closest('.remove-qualification-row');
    if (!button) return;
    const row = button.closest('[data-qualification-row]');
    const hasData = Array.from(row.querySelectorAll('input, select, textarea')).some(input => {
        if (input.type === 'file') return input.files.length > 0;
        if (input.type === 'checkbox') return input.checked;
        return input.value !== '';
    });
    const isEditForm = @json((bool) $emp);
    const removeRow = () => {
        const rows = document.querySelectorAll('[data-qualification-row]');
        if (rows.length === 1) {
            row.querySelector('input[type="hidden"]').value = '';
            row.querySelectorAll('input:not([type="hidden"]), textarea').forEach(input => { input.value = ''; if (input.type === 'checkbox') input.checked = false; });
            row.querySelectorAll('select').forEach(select => { select.value = ''; if (window.jQuery && jQuery.fn.select2) jQuery(select).val(null).trigger('change'); });
            row.querySelectorAll('[data-file-dropzone] input[type="file"]').forEach(input => { input.value = ''; clearFilePreview(input); });
            row.querySelectorAll('.employee-file-dropzone__filename').forEach(element => { element.textContent = 'No file chosen'; });
            return;
        }
        row.remove();
    };
    if (!isEditForm || !hasData) {
        removeRow();
        return;
    }
    const dialog = window.Swal ? Swal.fire({
        title: 'Remove qualification?',
        text: 'This qualification will be removed when you save the employee.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Remove',
        cancelButtonText: 'Keep it',
        reverseButtons: true
    }) : Promise.resolve({ isConfirmed: window.confirm('Remove this qualification?') });
    dialog.then(result => { if (result.isConfirmed) removeRow(); });
});
</script>
