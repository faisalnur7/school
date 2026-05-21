@php
    $inputClasses =
        'bg-white peer block w-full rounded-lg border border-gray-300 bg-transparent px-3 text-gray-900';
    $labelClasses =
        'absolute left-3 -top-2 !text-gray-700 transition-all text-sm bg-white px-2 pointer-events-none !font-medium';

    $date_of_birth = '';
    if(!empty($student)){
        $date_of_birth = Carbon\Carbon::parse($student->date_of_birth)->format('Y-m-d');
    }

    // Auto-generated values for create
    $autoRoll = old('roll', (isset($academicInfo) ? $academicInfo->roll : ''));
    $autoStudentCid = old('student_cid', $nextStudentCid ?? (isset($student) ? $student->student_cid : ''));
@endphp
<div class="bg-white shadow rounded-lg overflow-hidden">
    <div class="card-header bg-blue-600 text-white p-3 flex justify-between items-center shadow">
        <h3 class="text-lg font-semibold">{{ isset($student) ? 'Edit Student' : 'Create Student' }}</h3>
        <div class="flex gap-2 ml-auto">
            <button type="button" id="savePresetBtn"
                class="px-3 py-1 bg-green-500 hover:bg-green-600 text-white rounded text-sm">Save Preset</button>
            <button type="button" id="clearPresetBtn"
                class="px-3 py-1 bg-red-500 hover:bg-red-600 text-white rounded text-sm">Clear Preset</button>
        </div>
    </div>

    <form method="POST"
        action="{{ isset($student) ? route('students.update', $student->id) : route('students.store') }}"
        enctype="multipart/form-data" class="p-6 space-y-2">
        @csrf
        @if ($errors->any())
            <div class="mb-4 rounded-lg border border-red-200 bg-red-50 p-3 text-red-700">
                <p class="font-semibold">Please fix the following errors:</p>
                <ul class="list-disc pl-5 mt-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- ================= ACADEMIC INFORMATION ================= --}}
        @php
            $academic = $academicInfo ?? null;
        @endphp

        <div class="bg-white p-4 rounded-lg shadow-sm">
            <h5 class="text-gray-700 text-lg font-semibold mb-4 border-b pb-1">
                Academic Information
            </h5>

            <div class="grid grid-cols-1 md:grid-cols-6 gap-4">

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
                <select name="academic_session_id" class="border-gray-300 rounded-lg p-2 w-full" id="academicSessionSelect">
                    <option value="">Academic Session</option>
                    @foreach ($academicSessions as $session)
                        <option value="{{ $session->id }}"
                            {{ old('academic_session_id', optional($academic)->academic_session_id) == $session->id ? 'selected' : '' }}>
                            {{ $session->name_en }}
                        </option>
                    @endforeach
                </select>

                {{-- Class --}}
                <select name="school_class_id" id="classSelect" class="border-gray-300 rounded-lg p-2 w-full">
                    <option value="">Class</option>
                    @foreach ($classes as $class)
                        <option value="{{ $class->id }}"
                            {{ old('school_class_id', optional($academic)->school_class_id) == $class->id ? 'selected' : '' }}>
                            {{ $class->name_en }}
                        </option>
                    @endforeach
                </select>

                {{-- Section --}}
                <select name="section_id" id="sectionSelect" class="border-gray-300 rounded-lg p-2 w-full">
                    <option value="">Section</option>
                    @foreach ($sections as $section)
                        <option value="{{ $section->id }}"
                            {{ old('section_id', optional($academic)->section_id) == $section->id ? 'selected' : '' }}>
                            {{ $section->name_en }}
                        </option>
                    @endforeach
                </select>

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

        {{-- ================= BASIC INFO ================= --}}
        <div class="bg-white p-4 rounded-lg shadow-sm">
            <h5 class="text-gray-700 text-lg font-semibold mb-4 border-b pb-1">Basic Information</h5>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="relative w-full">
                    <input type="text" name="full_name_en"
                        value="{{ old('full_name_en', $student->full_name_en ?? '') }}" class="{{ $inputClasses }}"
                        required>
                    <label for="full_name_en" class="{{ $labelClasses }}"> Full Name (English) </label>
                </div>

                <div class="relative w-full">
                    <input type="text" name="full_name_bn"
                        value="{{ old('full_name_bn', $student->full_name_bn ?? '') }}" class="{{ $inputClasses }}">
                    <label for="full_name_bn" class="{{ $labelClasses }}">Full Name (Bangla)</label>
                </div>

                <div class="relative w-full">
                    <input type="text" name="date_of_birth"
                        value="{{ old('date_of_birth', $date_of_birth ?? '') }}"
                        class="{{ $inputClasses }} datepicker">
                    <label for="date_of_birth" class="{{ $labelClasses }}">Date of Birth</label>
                </div>

                <div class="relative w-full">
                    <input type="text" name="birth_certificate_number"
                        value="{{ old('birth_certificate_number', $student->birth_certificate_number ?? '') }}"
                        class="{{ $inputClasses }}">
                    <label for="birth_certificate_number" class="{{ $labelClasses }}">Birth Certificate Number</label>
                </div>

                <select name="gender" class="border-gray-300 rounded-lg p-2 w-full">
                    <option value="">Select Gender</option>
                    @foreach (\App\Models\Student::GENDERS as $k => $v)
                        <option value="{{ $k }}" @selected(old('gender', $student->gender ?? '') == $k)>{{ $v }}</option>
                    @endforeach
                </select>
                <select name="religion" class="border-gray-300 rounded-lg p-2 w-full">
                    <option value="">Select Religion</option>
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
                <input type="file" class="border-gray-300 rounded-lg p-2 w-full" name="image">
            </div>
        </div>

        {{-- ================= ADDRESS ================= --}}
        <div class="bg-white p-4 rounded-lg shadow-sm">
            <h5 class="text-gray-700 text-lg font-semibold mb-4 border-b pb-1">Address</h5>

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
                        <select name="present_division_id" class="border-gray-300 rounded-lg p-2 w-full">
                            <option value="">Division</option>
                            @foreach ($divisions as $division)
                                <option value="{{ $division->id }}" @selected(old('present_division_id', $student->present_division_id ?? '') == $division->id)>
                                    {{ $division->name }} - {{ $division->bn_name }}
                                </option>
                            @endforeach
                        </select>

                        <select name="present_district_id" class="border-gray-300 rounded-lg p-2 w-full">
                            <option value="">District</option>
                            @foreach ($districts as $district)
                                <option value="{{ $district->id }}" @selected(old('present_district_id', $student->present_district_id ?? '') == $district->id)>
                                    {{ $district->name }} - {{ $district->bn_name }}
                                </option>
                            @endforeach
                        </select>

                        <select name="present_police_station_id" class="border-gray-300 rounded-lg p-2 w-full">
                            <option value="">Police Station</option>
                            @foreach ($policeStations as $ps)
                                <option value="{{ $ps->id }}" @selected(old('present_police_station_id', $student->present_police_station_id ?? '') == $ps->id)>
                                    {{ $ps->name }} - {{ $ps->bn_name }}
                                </option>
                            @endforeach
                        </select>

                        <select name="present_post_office_id" class="border-gray-300 rounded-lg p-2 w-full">
                            <option value="">Post Office</option>
                            @foreach ($postOffices as $po)
                                <option value="{{ $po->id }}" @selected(old('present_post_office_id', $student->present_post_office_id ?? '') == $po->id)>
                                    {{ $po->name }} - {{ $po->bn_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <textarea name="present_address" rows="3" class="border-gray-300 rounded-lg p-2 w-full mt-2"
                        placeholder="Present Address">{{ old('present_address') }}</textarea>
                </div>

                <!-- Permanent Address -->
                <div id="permanent_address_section">
                    <h6 class="font-semibold text-gray-600 mb-2">Permanent Address</h6>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <select name="permanent_division_id" class="border-gray-300 rounded-lg p-2 w-full">
                            <option value="">Division</option>
                            @foreach ($divisions as $division)
                                <option value="{{ $division->id }}" @selected(old('permanent_division_id', $student->permanent_division_id ?? '') == $division->id)>
                                    {{ $division->name }} - {{ $division->bn_name }}
                                </option>
                            @endforeach
                        </select>

                        <select name="permanent_district_id" class="border-gray-300 rounded-lg p-2 w-full">
                            <option value="">District</option>
                            @foreach ($districts as $district)
                                <option value="{{ $district->id }}" @selected(old('permanent_district_id', $student->permanent_district_id ?? '') == $district->id)>
                                    {{ $district->name }} - {{ $district->bn_name }}
                                </option>
                            @endforeach
                        </select>

                        <select name="permanent_police_station_id" class="border-gray-300 rounded-lg p-2 w-full">
                            <option value="">Police Station</option>
                            @foreach ($policeStations as $ps)
                                <option value="{{ $ps->id }}" @selected(old('permanent_police_station_id', $student->permanent_police_station_id ?? '') == $ps->id)>
                                    {{ $ps->name }} - {{ $ps->bn_name }}
                                </option>
                            @endforeach
                        </select>

                        <select name="permanent_post_office_id" class="border-gray-300 rounded-lg p-2 w-full">
                            <option value="">Post Office</option>
                            @foreach ($postOffices as $po)
                                <option value="{{ $po->id }}" @selected(old('permanent_post_office_id', $student->permanent_post_office_id ?? '') == $po->id)>
                                    {{ $po->name }} - {{ $po->bn_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <textarea class="border-gray-300 rounded-lg p-2 w-full mt-2" name="permanent_address" rows="3"
                        placeholder="Permanent Address">{{ old('permanent_address', $student->permanent_address ?? '') }}</textarea>
                </div>

            </div>
        </div>

        {{-- ================= PARENTS INFO ================= --}}
        <div class="bg-white p-4 rounded-lg shadow-sm">
            <h5 class="text-gray-700 text-lg font-semibold mb-4 border-b pb-1">Parents Information</h5>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                <div class="relative w-full">
                    <input type="text" name="father_name"
                        value="{{ old('father_name', $student->father_name ?? '') }}" class="{{ $inputClasses }}"
                        id="father_name">
                    <label for="father_name" class="{{ $labelClasses }}">Father Name</label>
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
                        id="father_phone">
                    <label for="father_phone" class="{{ $labelClasses }}">Father Phone</label>
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
                        id="mother_name">
                    <label for="mother_name" class="{{ $labelClasses }}">Mother Name</label>
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
                        id="mother_phone">
                    <label for="mother_phone" class="{{ $labelClasses }}">Mother Phone</label>
                </div>

                <div class="relative w-full">
                    <input type="text" name="mother_email"
                        value="{{ old('mother_email', $student->mother_email ?? '') }}" class="{{ $inputClasses }}"
                        id="mother_email">
                    <label for="mother_email" class="{{ $labelClasses }}">Mother Email</label>
                </div>

            </div>
        </div>

        {{-- ================= GUARDIAN TYPE ================= --}}
        <div class="bg-white p-4 rounded-lg shadow-sm">
            <h5 class="text-gray-700 text-lg font-semibold mb-4 border-b pb-1">Guardian Type</h5>
            <div class="flex gap-6">
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
        </div>

        {{-- ================= GUARDIAN INFO ================= --}}
        <div id="guardianInfo" class="bg-white p-4 rounded-lg shadow-sm hidden">
            <h5 class="text-gray-700 text-lg font-semibold mb-4 border-b pb-1">Guardian Information</h5>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
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

        {{-- ================= FAMILY INCOME ================= --}}
        <div class="bg-white p-4 rounded-lg shadow-sm">
            <h5 class="text-gray-700 text-lg font-semibold mb-4 border-b pb-1">Family Annual Income</h5>
            <div class="relative w-full md:w-1/2">
                <input type="text" name="annual_income"
                    value="{{ old('annual_income', $student->annual_income ?? '') }}" placeholder=" " class="{{ $inputClasses }}"
                    id="annual_income">
                <label for="annual_income" class="{{ $labelClasses }}">Annual Income</label>
            </div>

        </div>

        {{-- ================= PREVIOUS ACADEMIC ================= --}}
        <div class="bg-white p-4 rounded-lg shadow-sm">
            <h5 class="text-gray-700 text-lg font-semibold mb-4 border-b pb-1">Previous Academic History</h5>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
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

        <!-- Footer Buttons -->
        <div class="flex gap-3 pt-4">
            <button class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded flex items-center gap-2">
                <i class="fas fa-save"></i> Save
            </button>
            <a href="{{ route('students.index') }}"
                class="bg-white0 hover:bg-gray-600 text-white px-4 py-2 rounded">Back</a>
        </div>
    </form>
</div>
