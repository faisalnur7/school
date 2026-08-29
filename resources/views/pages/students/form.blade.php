@php
    $errors = $errors ?? new \Illuminate\Support\ViewErrorBag();
    $inputClasses =
        'student-floating-input peer block w-full rounded-lg border border-gray-300 bg-white px-3 text-gray-900';
    $labelClasses =
        'student-floating-label absolute left-3 -top-2 bg-white px-2 pointer-events-none !font-medium';
    $useAdmissionDropzone = !empty($admissionMode) || !empty($editMode);
    $existingImageUrl = !empty($student?->image) ? asset($student->image) : null;
    $existingImageName = !empty($student?->image) ? basename($student->image) : null;

    $date_of_birth = '';
    if(!empty($student)){
        $date_of_birth = Carbon\Carbon::parse($student->date_of_birth)->format('Y-m-d');
    }

    // Auto-generated values for create
    $autoRoll = old('roll', (isset($academicInfo) ? $academicInfo->roll : ''));
    $autoStudentCid = old('student_cid', $nextStudentCid ?? (isset($student) ? $student->student_cid : ''));
@endphp
<div class="student-form-page">
    <div class="student-form-shell">
        <div class="student-form-header">
            <div>
                <h3 class="student-form-title">{{ !empty($publicAdmissionMode) ? 'Application details' : (isset($student) ? 'Edit Student' : 'Create Student') }}</h3>
                <p class="student-form-subtitle">{{ !empty($publicAdmissionMode) ? 'Fill in the required details below. You can review everything before submitting.' : 'A cleaner workspace for managing student identity, academic placement, family details, and contact information.' }}</p>
            </div>
            <div class="student-form-header-actions">
                <div class="student-form-view-switcher" role="group" aria-label="Form view mode">
                    <button type="button" class="student-view-toggle is-active" data-student-view-toggle="accordion" aria-pressed="true">
                        <i class="fas fa-list"></i>
                        <span>Accordion</span>
                    </button>
                    <button type="button" class="student-view-toggle" data-student-view-toggle="tabs" aria-pressed="false">
                        <i class="fas fa-th-large"></i>
                        <span>Tabbed</span>
                    </button>
                </div>
                <button type="button" id="savePresetBtn" class="btn btn-light btn-sm student-action-btn">
                    <i class="fas fa-bookmark"></i> Save Preset
                </button>
                <button type="button" id="clearPresetBtn" class="btn btn-outline-light btn-sm student-action-btn">
                    <i class="fas fa-broom"></i> Clear Preset
                </button>
            </div>
        </div>

        <form method="POST"
            action="{{ !empty($publicAdmissionMode) ? route('public.admission.store') : (isset($student) ? route('students.update', $student->id) : route('students.store')) }}"
            enctype="multipart/form-data" class="student-form-body space-y-4">
        @csrf
        @if(!empty($publicAdmissionMode))
            <input type="hidden" name="academic_session_id" value="{{ $exam->academic_session_id }}">
        @endif
        @if ($errors->any())
            <div class="rounded-xl border border-red-200 bg-red-50 p-4 text-red-700 shadow-sm">
                <p class="font-semibold">Please fix the following errors:</p>
                <ul class="list-disc pl-5 mt-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="student-tabbar" data-student-tabs hidden>
            @if(empty($publicAdmissionMode))
                <button type="button" class="student-tab-button is-active" data-student-tab="academic">Academic Information</button>
            @endif
            <button type="button" class="student-tab-button" data-student-tab="basic">Basic Information</button>
            <button type="button" class="student-tab-button" data-student-tab="address">Address</button>
            <button type="button" class="student-tab-button" data-student-tab="parents">Parents Information</button>
            <button type="button" class="student-tab-button" data-student-tab="guardian">Guardian Information</button>
            <button type="button" class="student-tab-button" data-student-tab="previous">Previous Academic History</button>
        </div>

        @if(empty($publicAdmissionMode))
            {{-- ================= ACADEMIC INFORMATION ================= --}}
            @php
                $academic = $academicInfo ?? null;
            @endphp

        <details class="student-section" data-student-panel="academic" open>
            <summary class="student-section__head">
                <div>
                    <h5>Academic Information</h5>
                    <p>Core registration, class placement, and roll details.</p>
                </div>
                <div class="flex items-center gap-2">
                    <span class="student-section__badge">Required</span>
                    <span class="student-section__chevron"><i class="fas fa-chevron-down"></i></span>
                </div>
            </summary>

            <div class="student-section__body">
                <div class="grid grid-cols-1 md:grid-cols-6 student-field-grid">

                {{-- Student ID (CID) --}}
                <div class="relative w-full">
                    <input type="text"
                        name="student_cid"
                        id="student_cid"
                        value="{{ $autoStudentCid }}"
                        class="{{ $inputClasses }}"
                        @if(!isset($student)) readonly @endif>
                    <label for="student_cid" class="{{ $labelClasses }}">
                        Student ID
                    </label>
                </div>

                {{-- Academic Session --}}
                <div class="relative w-full">
                    <select name="academic_session_id" class="border-gray-300 rounded-lg p-2 w-full" id="academicSessionSelect" required>
                        <option value="">Academic Session</option>
                        @foreach ($academicSessions as $session)
                            <option value="{{ $session->id }}"
                                {{ old('academic_session_id', optional($academic)->academic_session_id) == $session->id ? 'selected' : '' }}>
                                {{ $session->name_en }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Class --}}
                <div class="relative w-full">
                    <select name="school_class_id" id="classSelect" class="border-gray-300 rounded-lg p-2 w-full" required>
                        <option value="">Class</option>
                        @foreach ($classes as $class)
                            <option value="{{ $class->id }}"
                                {{ old('school_class_id', optional($academic)->school_class_id) == $class->id ? 'selected' : '' }}>
                                {{ $class->name_en }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Section --}}
                <div class="relative w-full">
                    <select name="section_id" id="sectionSelect" class="border-gray-300 rounded-lg p-2 w-full" required>
                        <option value="">Section</option>
                        @foreach ($sections as $section)
                            <option value="{{ $section->id }}"
                                {{ old('section_id', optional($academic)->section_id) == $section->id ? 'selected' : '' }}>
                                {{ $section->name_en }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Group --}}
                <select name="group_id" id="groupSelect" class="border-gray-300 rounded-lg p-2 w-full">
                    <option value="">Group</option>
                    @foreach ($groups as $group)
                        <option value="{{ $group->id }}"
                            {{ old('group_id', optional($academic)->group_id) == $group->id ? 'selected' : '' }}>
                            {{ $group->name_en }}
                        </option>
                    @endforeach
                </select>

                {{-- Roll --}}
                <div class="relative w-full">
                    <input type="text"
                        name="roll"
                        id="roll"
                        value="{{ $autoRoll }}"
                        class="{{ $inputClasses }}"
                        @if(!isset($student)) readonly @endif>
                    <label for="roll" class="{{ $labelClasses }}">
                        Roll Number
                    </label>
                </div>

                </div>
            </div>
            </details>
        @endif

        {{-- ================= BASIC INFO ================= --}}
        <details class="student-section" data-student-panel="basic" open>
            <summary class="student-section__head">
                <div>
                    <h5>Basic Information</h5>
                    <p>Identity and personal details for the student record.</p>
                </div>
                <div class="flex items-center gap-2">
                    <span class="student-section__chevron"><i class="fas fa-chevron-down"></i></span>
                </div>
            </summary>

            <div class="student-section__body">
                <div class="student-basic-layout">
                    <div class="student-basic-fields">
                        <div class="grid grid-cols-1 student-field-grid">
                            @if(!empty($publicAdmissionMode))
                                <div class="public-applied-class-field relative w-full">
                                    <select name="school_class_id" id="publicClassSelect" class="border-gray-300 rounded-lg p-2 w-full" required>
                                        <option value="">Class</option>
                                        @foreach ($classes as $class)
                                            @if($exam->classSettings->contains('school_class_id', (int) $class->id))
                                                <option value="{{ $class->id }}" @selected(old('school_class_id') == $class->id)>{{ $class->name_en }}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                    <label for="publicClassSelect" class="{{ $labelClasses }}">Applied Class <span class="text-danger">*</span></label>
                                </div>
                            @endif

                            <div class="relative w-full">
                                <input type="text" name="full_name_en"
                                    value="{{ old('full_name_en', $student->full_name_en ?? '') }}" class="{{ $inputClasses }}"
                                    required>
                                <label for="full_name_en" class="{{ $labelClasses }}"> Full Name (English) <span class="text-danger">*</span> </label>
                            </div>

                            <div class="relative w-full">
                                <input type="text" name="full_name_bn"
                                    value="{{ old('full_name_bn', $student->full_name_bn ?? '') }}" class="{{ $inputClasses }}">
                                <label for="full_name_bn" class="{{ $labelClasses }}">Full Name (Bangla)</label>
                            </div>

                            <div class="relative w-full">
                                <input type="text" name="date_of_birth"
                                    @if(!empty($publicAdmissionMode)) id="date_of_birth" @endif
                                    value="{{ old('date_of_birth', $date_of_birth ?? '') }}"
                                    class="{{ $inputClasses }} datepicker"
                                    @if(!empty($publicAdmissionMode)) placeholder="dd/mm/yyyy" autocomplete="bday" data-date-format="dd/mm/yyyy" @endif>
                                <label for="date_of_birth" class="{{ $labelClasses }}">Date of Birth</label>
                            </div>

                            <div class="relative w-full">
                                <input type="text" name="birth_certificate_number"
                                    value="{{ old('birth_certificate_number', $student->birth_certificate_number ?? '') }}"
                                    class="{{ $inputClasses }}">
                                <label for="birth_certificate_number" class="{{ $labelClasses }}">Birth Certificate Number</label>
                            </div>

                            @if(!empty($publicAdmissionMode))
                                <div class="relative w-full">
                                    <select name="gender" id="gender" class="border-gray-300 rounded-lg p-2 w-full" required>
                                        <option value="">Gender</option>
                                        @foreach (\App\Models\Student::GENDERS as $k => $v)
                                            <option value="{{ $k }}" @selected(old('gender', $student->gender ?? '') == $k)>{{ $v }}</option>
                                        @endforeach
                                    </select>
                                    <label for="gender" class="{{ $labelClasses }}">Gender <span class="text-danger">*</span></label>
                                </div>
                                <div class="relative w-full">
                                    <select name="religion" id="religion" class="border-gray-300 rounded-lg p-2 w-full" required>
                                        <option value="">Religion</option>
                                        @foreach (\App\Models\Student::RELIGIONS as $k => $v)
                                            <option value="{{ $k }}" @selected(old('religion', $student->religion ?? '') == $k)>{{ $v }}</option>
                                        @endforeach
                                    </select>
                                    <label for="religion" class="{{ $labelClasses }}">Religion <span class="text-danger">*</span></label>
                                </div>
                                <div class="relative w-full">
                                    <select name="blood_group" class="border-gray-300 rounded-lg p-2 w-full">
                                        <option value="">Select Blood Group</option>
                                        @foreach (\App\Models\Student::BLOOD_GROUPS as $k => $v)
                                            <option value="{{ $k }}" @selected(old('blood_group', $student->blood_group ?? '') == $k)>{{ $v }}</option>
                                        @endforeach
                                    </select>
                                    <label class="{{ $labelClasses }}">Blood Group</label>
                                </div>
                                <div class="relative w-full">
                                    <select name="disable" class="border-gray-300 rounded-lg p-2 w-full">
                                        <option value="0">Not Disabled</option>
                                        <option value="1" @selected(old('disable', $student->disable ?? 0) == 1)>Disabled</option>
                                    </select>
                                    <label class="{{ $labelClasses }}">Disability Status</label>
                                </div>
                            @else
                                <select name="gender" id="gender" class="border-gray-300 rounded-lg p-2 w-full" required>
                                    <option value="">Gender</option>
                                    @foreach (\App\Models\Student::GENDERS as $k => $v)
                                        <option value="{{ $k }}" @selected(old('gender', $student->gender ?? '') == $k)>{{ $v }}</option>
                                    @endforeach
                                </select>
                                <select name="religion" id="religion" class="border-gray-300 rounded-lg p-2 w-full" required>
                                    <option value="">Religion</option>
                                    @foreach (\App\Models\Student::RELIGIONS as $k => $v)
                                        <option value="{{ $k }}" @selected(old('religion', $student->religion ?? '') == $k)>{{ $v }}</option>
                                    @endforeach
                                </select>
                                <select name="blood_group" class="border-gray-300 rounded-lg p-2 w-full">
                                    <option value="">Select Blood Group</option>
                                    @foreach (\App\Models\Student::BLOOD_GROUPS as $k => $v)
                                        <option value="{{ $k }}" @selected(old('blood_group', $student->blood_group ?? '') == $k)>{{ $v }}</option>
                                    @endforeach
                                </select>
                                <select name="disable" class="border-gray-300 rounded-lg p-2 w-full">
                                    <option value="0">Not Disabled</option>
                                    <option value="1" @selected(old('disable', $student->disable ?? 0) == 1)>Disabled</option>
                                </select>
                            @endif
                        </div>
                    </div>

                    @if ($useAdmissionDropzone)
                        <div class="student-basic-media">
                            <div class="student-basic-media__card">
                                <label class="student-basic-media__title">Student Image</label>
                                <div
                                    id="studentImageDropzone"
                                    class="student-image-dropzone dropzone rounded-lg border-2 border-dashed border-slate-300 bg-slate-50 p-3"
                                    data-min-width="290"
                                    data-max-width="300"
                                    data-min-height="440"
                                    data-max-height="450"
                                    @if ($existingImageUrl)
                                        data-existing-image-url="{{ $existingImageUrl }}"
                                        data-existing-image-name="{{ $existingImageName }}"
                                    @endif
                                >
                                    <div class="dz-message needsclick">
                                        <div class="text-sm font-semibold text-slate-700">Drop student photo here or click to browse</div>
                                        <div class="mt-1 text-xs text-slate-500">Allowed size: 290-300 px wide and 440-450 px tall.</div>
                                    </div>
                                </div>
                                <p class="student-basic-media__note">Use a clear portrait photo. The preview stays compact and aligned with the student identity fields.</p>
                                <input type="file" id="studentImageInput" name="image" class="d-none" accept="image/*">
                                <div id="studentImageValidationError" class="mt-2 text-sm text-danger"></div>
                                @error('image')
                                    <div class="mt-2 text-sm text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    @else
                        <div class="student-basic-media">
                            <div class="student-basic-media__card">
                                <label class="student-basic-media__title">Student Image</label>
                                <div class="relative w-full">
                                    <input type="file" class="border-gray-300 rounded-lg p-2 w-full" name="image" accept="image/*">
                                </div>
                                <p class="student-basic-media__note">Use a clear portrait photo. The preview stays compact and aligned with the student identity fields.</p>
                                @error('image')
                                    <div class="mt-2 text-sm text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </details>

        {{-- ================= ADDRESS ================= --}}
        <details class="student-section" data-student-panel="address" @if(!empty($publicAdmissionMode)) open @endif>
            <summary class="student-section__head">
                <div>
                    <h5>Address</h5>
                    <p>Present and permanent address information.</p>
                </div>
                <span class="student-section__chevron"><i class="fas fa-chevron-down"></i></span>
            </summary>

            <div class="student-section__body">
                <!-- SAME ADDRESS CHECKBOX -->
                <div class="flex items-center mb-3">
                    <input type="checkbox" id="same_address" class="mr-2 rounded border-gray-300">
                    <label for="same_address" class="text-sm font-medium text-gray-700">Present & Permanent Address are
                        same</label>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Present Address -->
                    <div>
                        <h6 class="font-semibold text-gray-600 mb-2">Present Address</h6>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @if(!empty($publicAdmissionMode))<div class="relative w-full">@endif
                            <select name="present_division_id" class="border-gray-300 rounded-lg p-2 w-full">
                                <option value="">Division</option>
                                @foreach ($divisions as $division)
                                    <option value="{{ $division->id }}" @selected(old('present_division_id', $student->present_division_id ?? '') == $division->id)>
                                        {{ $division->name }} - {{ $division->bn_name }}
                                    </option>
                                @endforeach
                            </select>@if(!empty($publicAdmissionMode))<label class="{{ $labelClasses }}">Division</label></div>@endif

                            @if(!empty($publicAdmissionMode))<div class="relative w-full">@endif
                            <select name="present_district_id" class="border-gray-300 rounded-lg p-2 w-full">
                                <option value="">District</option>
                                @foreach ($districts as $district)
                                    <option value="{{ $district->id }}" @selected(old('present_district_id', $student->present_district_id ?? '') == $district->id)>
                                        {{ $district->name }} - {{ $district->bn_name }}
                                    </option>
                                @endforeach
                            </select>@if(!empty($publicAdmissionMode))<label class="{{ $labelClasses }}">District</label></div>@endif

                            @if(!empty($publicAdmissionMode))<div class="relative w-full">@endif
                            <select name="present_police_station_id" class="border-gray-300 rounded-lg p-2 w-full">
                                <option value="">Police Station</option>
                                @foreach ($policeStations as $ps)
                                    <option value="{{ $ps->id }}" @selected(old('present_police_station_id', $student->present_police_station_id ?? '') == $ps->id)>
                                        {{ $ps->name }} - {{ $ps->bn_name }}
                                    </option>
                                @endforeach
                            </select>@if(!empty($publicAdmissionMode))<label class="{{ $labelClasses }}">Police Station</label></div>@endif

                            @if(!empty($publicAdmissionMode))<div class="relative w-full">@endif
                            <select name="present_post_office_id" class="border-gray-300 rounded-lg p-2 w-full">
                                <option value="">Post Office</option>
                                @foreach ($postOffices as $po)
                                    <option value="{{ $po->id }}" @selected(old('present_post_office_id', $student->present_post_office_id ?? '') == $po->id)>
                                        {{ $po->name }} - {{ $po->bn_name }}
                                    </option>
                                @endforeach
                            </select>@if(!empty($publicAdmissionMode))<label class="{{ $labelClasses }}">Post Office</label></div>@endif
                        </div>

                        <textarea name="present_address" rows="3" class="border-gray-300 rounded-lg p-2 w-full mt-2"
                            placeholder="Present Address">{{ old('present_address', $student->present_address ?? '') }}</textarea>
                    </div>

                    <!-- Permanent Address -->
                    <div id="permanent_address_section">
                        <h6 class="font-semibold text-gray-600 mb-2">Permanent Address</h6>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @if(!empty($publicAdmissionMode))<div class="relative w-full">@endif
                            <select name="permanent_division_id" class="border-gray-300 rounded-lg p-2 w-full">
                                <option value="">Division</option>
                                @foreach ($divisions as $division)
                                    <option value="{{ $division->id }}" @selected(old('permanent_division_id', $student->permanent_division_id ?? '') == $division->id)>
                                        {{ $division->name }} - {{ $division->bn_name }}
                                    </option>
                                @endforeach
                            </select>@if(!empty($publicAdmissionMode))<label class="{{ $labelClasses }}">Division</label></div>@endif

                            @if(!empty($publicAdmissionMode))<div class="relative w-full">@endif
                            <select name="permanent_district_id" class="border-gray-300 rounded-lg p-2 w-full">
                                <option value="">District</option>
                                @foreach ($districts as $district)
                                    <option value="{{ $district->id }}" @selected(old('permanent_district_id', $student->permanent_district_id ?? '') == $district->id)>
                                        {{ $district->name }} - {{ $district->bn_name }}
                                    </option>
                                @endforeach
                            </select>@if(!empty($publicAdmissionMode))<label class="{{ $labelClasses }}">District</label></div>@endif

                            @if(!empty($publicAdmissionMode))<div class="relative w-full">@endif
                            <select name="permanent_police_station_id" class="border-gray-300 rounded-lg p-2 w-full">
                                <option value="">Police Station</option>
                                @foreach ($policeStations as $ps)
                                    <option value="{{ $ps->id }}" @selected(old('permanent_police_station_id', $student->permanent_police_station_id ?? '') == $ps->id)>
                                        {{ $ps->name }} - {{ $ps->bn_name }}
                                    </option>
                                @endforeach
                            </select>@if(!empty($publicAdmissionMode))<label class="{{ $labelClasses }}">Police Station</label></div>@endif

                            @if(!empty($publicAdmissionMode))<div class="relative w-full">@endif
                            <select name="permanent_post_office_id" class="border-gray-300 rounded-lg p-2 w-full">
                                <option value="">Post Office</option>
                                @foreach ($postOffices as $po)
                                    <option value="{{ $po->id }}" @selected(old('permanent_post_office_id', $student->permanent_post_office_id ?? '') == $po->id)>
                                        {{ $po->name }} - {{ $po->bn_name }}
                                    </option>
                                @endforeach
                            </select>@if(!empty($publicAdmissionMode))<label class="{{ $labelClasses }}">Post Office</label></div>@endif
                        </div>

                        <textarea class="border-gray-300 rounded-lg p-2 w-full mt-2" name="permanent_address" rows="3"
                            placeholder="Permanent Address">{{ old('permanent_address', $student->permanent_address ?? '') }}</textarea>
                    </div>

                </div>
            </div>
        </details>

        {{-- ================= PARENTS INFO ================= --}}
        <details class="student-section" data-student-panel="parents" @if(!empty($publicAdmissionMode)) open @endif>
            <summary class="student-section__head">
                <div>
                    <h5>Parents Information</h5>
                    <p>Parent identities, jobs, and contact details.</p>
                </div>
                <span class="student-section__chevron"><i class="fas fa-chevron-down"></i></span>
            </summary>
            <div class="student-section__body">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 student-field-grid">
                <div class="relative w-full">
                    <input type="text" name="father_name"
                        value="{{ old('father_name', $student->father_name ?? '') }}" class="{{ $inputClasses }}"
                        id="father_name" required>
                    <label for="father_name" class="{{ $labelClasses }}">Father Name <span class="text-danger">*</span></label>
                </div>

                <div class="relative w-full">
                    <input type="text" name="father_nid_number"
                        value="{{ old('father_nid_number', $student->father_nid_number ?? '') }}"
                        class="{{ $inputClasses }}" id="father_nid_number">
                    <label for="father_nid_number" class="{{ $labelClasses }}">Father NID</label>
                </div>

                <div class="relative w-full">
                    <select name="fathers_profession_id" id="fathers_profession_id" class="{{ $inputClasses }}">
                        <option value="">— Select Profession —</option>
                        @foreach($professions as $p)
                            <option value="{{ $p->id }}"
                                {{ old('fathers_profession_id', $student->fathers_profession_id ?? '') == $p->id ? 'selected' : '' }}>
                                {{ $p->name }}
                            </option>
                        @endforeach
                    </select>
                    <label for="fathers_profession_id" class="{{ $labelClasses }}">Father Profession</label>
                </div>

                <div class="relative w-full">
                    <input type="text" name="father_phone"
                        value="{{ old('father_phone', $student->father_phone ?? '') }}" class="{{ $inputClasses }}"
                        id="father_phone" {{ !empty($publicAdmissionMode) ? 'aria-required=true' : '' }}>
                    <label for="father_phone" class="{{ $labelClasses }}">Father Phone @if(!empty($publicAdmissionMode)) <span class="public-parent-phone-note">(at least one required)</span> @endif</label>
                </div>

                <div class="relative w-full">
                    <input type="text" name="father_email"
                        value="{{ old('father_email', $student->father_email ?? '') }}" class="{{ $inputClasses }}"
                        id="father_email">
                    <label for="father_email" class="{{ $labelClasses }}">Father Email</label>
                </div>

                <div class="relative w-full">
                    <input type="text" name="mother_name"
                        value="{{ old('mother_name', $student->mother_name ?? '') }}" class="{{ $inputClasses }}"
                        id="mother_name" required>
                    <label for="mother_name" class="{{ $labelClasses }}">Mother Name <span class="text-danger">*</span></label>
                </div>

                <div class="relative w-full">
                    <input type="text" name="mother_nid_number"
                        value="{{ old('mother_nid_number', $student->mother_nid_number ?? '') }}"
                        class="{{ $inputClasses }}" id="mother_nid_number">
                    <label for="mother_nid_number" class="{{ $labelClasses }}">Mother NID</label>
                </div>

                <div class="relative w-full">
                    <select name="mothers_profession_id" id="mothers_profession_id" class="{{ $inputClasses }}">
                        <option value="">— Select Profession —</option>
                        @foreach($professions as $p)
                            <option value="{{ $p->id }}"
                                {{ old('mothers_profession_id', $student->mothers_profession_id ?? '') == $p->id ? 'selected' : '' }}>
                                {{ $p->name }}
                            </option>
                        @endforeach
                    </select>
                    <label for="mothers_profession_id" class="{{ $labelClasses }}">Mother Profession</label>
                </div>

                <div class="relative w-full">
                    <input type="text" name="mother_phone"
                        value="{{ old('mother_phone', $student->mother_phone ?? '') }}" class="{{ $inputClasses }}"
                        id="mother_phone" {{ !empty($publicAdmissionMode) ? 'aria-required=true' : '' }}>
                    <label for="mother_phone" class="{{ $labelClasses }}">Mother Phone @if(!empty($publicAdmissionMode)) <span class="public-parent-phone-note">(at least one required)</span> @endif</label>
                </div>

                <div class="relative w-full">
                    <input type="text" name="mother_email"
                        value="{{ old('mother_email', $student->mother_email ?? '') }}" class="{{ $inputClasses }}"
                        id="mother_email">
                    <label for="mother_email" class="{{ $labelClasses }}">Mother Email</label>
                </div>

                <div class="relative w-full md:col-span-2 lg:col-span-2">
                    <input type="text" name="annual_income"
                        value="{{ old('annual_income', $student->annual_income ?? '') }}" class="{{ $inputClasses }}"
                        id="annual_income">
                    <label for="annual_income" class="{{ $labelClasses }}">Family Annual Income</label>
                </div>

            </div>
            </div>
        </details>

        {{-- ================= GUARDIAN INFO ================= --}}
        <details class="student-section" data-student-panel="guardian" @if(!empty($publicAdmissionMode)) open @endif>
            <summary class="student-section__head">
                <div>
                    <h5>Guardian Information</h5>
                    <p>Select the guardian relationship and enter dependent contact details when applicable.</p>
                </div>
                <span class="student-section__chevron"><i class="fas fa-chevron-down"></i></span>
            </summary>
            <div class="student-section__body">
                <div class="flex gap-6 flex-wrap">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="guardian_type" value="1" class="form-radio"
                            @checked(old('guardian_type', $student->guardian_type ?? 1) == 1)>
                        <span>Father</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="guardian_type" value="2" class="form-radio"
                            @checked(old('guardian_type', $student->guardian_type ?? 1) == 2)>
                        <span>Mother</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="guardian_type" value="3" class="form-radio"
                            @checked(old('guardian_type', $student->guardian_type ?? 1) == 3)>
                        <span>Other</span>
                    </label>
                </div>

                <div id="guardianInfoFields" class="mt-6 hidden">
                    <div class="grid grid-cols-1 md:grid-cols-4 student-field-grid" style="padding-top: .5rem;">
                        <div class="relative w-full">
                            <input type="text" name="guardian_name"
                                value="{{ old('guardian_name', $student->guardian_name ?? '') }}"
                                class="{{ $inputClasses }}" id="guardian_name">
                            <label for="guardian_name" class="{{ $labelClasses }}">Guardian Name</label>
                        </div>

                        <div class="relative w-full">
                            <input type="text" name="guardian_relation"
                                value="{{ old('guardian_relation', $student->guardian_relation ?? '') }}"
                                class="{{ $inputClasses }}" id="guardian_relation">
                            <label for="guardian_relation" class="{{ $labelClasses }}">Relation</label>
                        </div>

                        <div class="relative w-full">
                            <select name="guardian_profession_id" id="guardian_profession_id" class="{{ $inputClasses }}">
                                <option value="">— Select Profession —</option>
                                @foreach($professions as $p)
                                    <option value="{{ $p->id }}"
                                        {{ old('guardian_profession_id', $student->guardian_profession_id ?? '') == $p->id ? 'selected' : '' }}>
                                        {{ $p->name }}
                                    </option>
                                @endforeach
                            </select>
                            <label for="guardian_profession_id" class="{{ $labelClasses }}">Guardian Profession</label>
                        </div>

                        <div class="relative w-full">
                            <input type="text" name="guardian_phone"
                                value="{{ old('guardian_phone', $student->guardian_phone ?? '') }}"
                                class="{{ $inputClasses }}" id="guardian_phone">
                            <label for="guardian_phone" class="{{ $labelClasses }}">Phone</label>
                        </div>

                        <div class="relative w-full">
                            <input type="text" name="guardian_email"
                                value="{{ old('guardian_email', $student->guardian_email ?? '') }}"
                                class="{{ $inputClasses }}" id="guardian_email">
                            <label for="guardian_email" class="{{ $labelClasses }}">Email</label>
                        </div>
                    </div>
                    <textarea class="border-gray-300 rounded-lg p-2 w-full mt-2" name="guardian_address" rows="2"
                        placeholder="Guardian Address"></textarea>
                </div>
            </div>
        </details>

        {{-- ================= PREVIOUS ACADEMIC ================= --}}
        <details class="student-section" data-student-panel="previous" @if(!empty($publicAdmissionMode)) open @endif>
            <summary class="student-section__head">
                <div>
                    <h5>Previous Academic History</h5>
                    <p>Prior school and transfer certificate references.</p>
                </div>
                <span class="student-section__chevron"><i class="fas fa-chevron-down"></i></span>
            </summary>
            <div class="student-section__body">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 student-field-grid">
                <div class="relative w-full">
                    <input type="text" name="previous_school"
                        value="{{ old('previous_school', $student->previous_school ?? '') }}"
                        class="{{ $inputClasses }}" id="previous_school">
                    <label for="previous_school" class="{{ $labelClasses }}">Previous School</label>
                </div>

                <div class="relative w-full">
                    <input type="text" name="previous_class_appeared"
                        value="{{ old('previous_class_appeared', $student->previous_class_appeared ?? '') }}"
                        class="{{ $inputClasses }}" id="previous_class_appeared">
                    <label for="previous_class_appeared" class="{{ $labelClasses }}">Previous Class</label>
                </div>

                <div class="relative w-full">
                    <input type="text" name="tc_number"
                        value="{{ old('tc_number', $student->tc_number ?? '') }}" class="{{ $inputClasses }}"
                        id="tc_number">
                    <label for="tc_number" class="{{ $labelClasses }}">TC Number</label>
                </div>

            </div>
            </div>
        </details>

        <!-- Footer Buttons -->
        <div class="student-form-actions">
            <button class="btn btn-success btn-sm student-action-btn">
                <i class="fas fa-save"></i> Save
            </button>
            <a href="{{ route('students.index') }}" class="btn btn-secondary btn-sm student-action-btn">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const root = document.querySelector('.student-form-page');
        if (!root) {
            return;
        }

        const viewButtons = Array.from(root.querySelectorAll('[data-student-view-toggle]'));
        const tabbar = root.querySelector('[data-student-tabs]');
        const tabButtons = Array.from(root.querySelectorAll('[data-student-tab]'));
        const panels = Array.from(root.querySelectorAll('[data-student-panel]'));
        const accordionKey = 'student-form-view-mode';
        const panelKey = 'student-form-active-panel';
        const isSmallScreen = () => window.matchMedia('(max-width: 991.98px)').matches;
        const form = root.querySelector('form');

        let accordionState = new Map(
            panels.map((panel) => [panel.dataset.studentPanel, panel.open])
        );

        const setView = (mode, persist = true) => {
            const resolvedMode = mode;
            root.dataset.studentView = resolvedMode;

            viewButtons.forEach((button) => {
                const active = button.dataset.studentViewToggle === resolvedMode;
                button.classList.toggle('is-active', active);
                button.setAttribute('aria-pressed', active ? 'true' : 'false');
            });

            if (resolvedMode === 'tabs') {
                if (persist) {
                    accordionState = new Map(panels.map((panel) => [panel.dataset.studentPanel, panel.open]));
                }

                if (tabbar) {
                    tabbar.hidden = false;
                }

                const storedTabKey = localStorage.getItem(panelKey);
                const activeTabKey = panels.some((panel) => panel.dataset.studentPanel === storedTabKey)
                    ? storedTabKey
                    : panels[0]?.dataset.studentPanel;
                panels.forEach((panel) => {
                    const active = panel.dataset.studentPanel === activeTabKey;
                    panel.classList.toggle('is-active', active);
                    panel.hidden = !active;
                    panel.open = active;
                });

                tabButtons.forEach((button) => {
                    const active = button.dataset.studentTab === activeTabKey;
                    button.classList.toggle('is-active', active);
                    button.setAttribute('aria-selected', active ? 'true' : 'false');
                });
                return;
            }

            if (tabbar) {
                tabbar.hidden = true;
            }

            panels.forEach((panel, index) => {
                const key = panel.dataset.studentPanel;
                const shouldOpen = accordionState.has(key) ? accordionState.get(key) : index === 0;
                panel.hidden = false;
                panel.classList.remove('is-active');
                panel.open = shouldOpen;
            });

            if (!panels.some((panel) => panel.open) && panels[0]) {
                panels[0].open = true;
            }
        };

        const activatePanel = (panelKeyToShow) => {
            if (!panelKeyToShow) {
                return;
            }

            const targetPanel = panels.find((panel) => panel.dataset.studentPanel === panelKeyToShow);
            if (!targetPanel) {
                return;
            }

            if (root.dataset.studentView === 'tabs') {
                if (tabbar) {
                    tabbar.hidden = false;
                }

                panels.forEach((panel) => {
                    const active = panel === targetPanel;
                    panel.hidden = !active;
                    panel.classList.toggle('is-active', active);
                    panel.open = active;
                });

                tabButtons.forEach((button) => {
                    const active = button.dataset.studentTab === panelKeyToShow;
                    button.classList.toggle('is-active', active);
                    button.setAttribute('aria-selected', active ? 'true' : 'false');
                });
            } else {
                targetPanel.open = true;
            }

            localStorage.setItem(panelKey, panelKeyToShow);
        };

        viewButtons.forEach((button) => {
            button.addEventListener('click', () => {
                const mode = button.dataset.studentViewToggle;
                localStorage.setItem(accordionKey, mode);
                setView(mode);
            });
        });

        if (form) {
            form.addEventListener('invalid', (event) => {
                const field = event.target;
                const panel = field?.closest?.('[data-student-panel]');
                if (!panel) {
                    return;
                }

                activatePanel(panel.dataset.studentPanel);
            }, true);
        }

        tabButtons.forEach((button) => {
            button.addEventListener('click', () => {
                const key = button.dataset.studentTab;
                localStorage.setItem(panelKey, key);
                setView('tabs');
            });
        });

        panels.forEach((panel) => {
            panel.addEventListener('toggle', () => {
                if (root.dataset.studentView !== 'accordion' || !panel.open) {
                    return;
                }

                accordionState = new Map(
                    panels.map((candidate) => [candidate.dataset.studentPanel, candidate === panel])
                );

                panels.forEach((candidate) => {
                    if (candidate !== panel) {
                        candidate.open = false;
                    }
                });
            });
        });

        const savedMode = localStorage.getItem(accordionKey);
        const initialMode = savedMode || (isSmallScreen() ? 'accordion' : 'tabs');
        setView(initialMode, false);
    });
</script>
