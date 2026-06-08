<!-- Filter Section -->
<div class="card mb-3">
    <div class="card-header bg-light">
        <h5 class="mb-0 text-white">
            <i class="fas fa-filter"></i> Filter Students
            <button class="btn btn-sm btn-link float-right filter_button" type="button">
                <i class="fas fa-chevron-down"></i>
            </button>
        </h5>
    </div>
    <div class="" id="filterCollapse">
        <div class="card-body">
            <form action="{{ route('students.index') }}" method="GET">
                <div class="row">
                    <!-- Academic Filters -->
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Academic Session</label>
                        <select name="academic_session_id" class="form-control form-control-sm">
                            <option value="">All Sessions</option>
                            @foreach ($academicSessions as $session)
                                <option value="{{ $session->id }}"
                                    {{ request('academic_session_id') == $session->id ? 'selected' : '' }}>
                                    {{ $session->name_en }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3 mb-3">
                        <label class="form-label">Class</label>
                        <select name="school_class_id" id="classSelect" class="form-control form-control-sm">
                            <option value="">All Classes</option>
                            @foreach ($classes as $class)
                                <option value="{{ $class->id }}"
                                    {{ request('school_class_id') == $class->id ? 'selected' : '' }}>
                                    {{ $class->name_en }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3 mb-3">
                        <label class="form-label">Section</label>
                        <select name="section_id" id="sectionSelect" class="form-control form-control-sm">
                            <option value="">All Sections</option>
                            @foreach ($sections as $section)
                                <option value="{{ $section->id }}"
                                    {{ request('section_id') == $section->id ? 'selected' : '' }}>
                                    {{ $section->name_en }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3 mb-3">
                        <label class="form-label">Group</label>
                        <select name="group_id" id="groupSelect" class="form-control form-control-sm">
                            <option value="">All Groups</option>
                            @foreach ($groups as $group)
                                <option value="{{ $group->id }}"
                                    {{ request('group_id') == $group->id ? 'selected' : '' }}>
                                    {{ $group->name_en }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- <!-- Location Filters -->
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Division</label>
                        <select name="present_division_id" class="form-control form-control-sm">
                            <option value="">All Divisions</option>
                            @foreach ($divisions as $division)
                                <option value="{{ $division->id }}"
                                    {{ request('present_division_id') == $division->id ? 'selected' : '' }}>
                                    {{ $division->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3 mb-3">
                        <label class="form-label">District</label>
                        <select name="present_district_id" class="form-control form-control-sm">
                            <option value="">All Districts</option>
                            @foreach ($districts as $district)
                                <option value="{{ $district->id }}"
                                    {{ request('present_district_id') == $district->id ? 'selected' : '' }}>
                                    {{ $district->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3 mb-3">
                        <label class="form-label">Police Station</label>
                        <select name="present_police_station_id" class="form-control form-control-sm">
                            <option value="">All Police Stations</option>
                            @foreach ($policeStations as $ps)
                                <option value="{{ $ps->id }}"
                                    {{ request('present_police_station_id') == $ps->id ? 'selected' : '' }}>
                                    {{ $ps->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3 mb-3">
                        <label class="form-label">Post Office</label>
                        <select name="present_post_office_id" class="form-control form-control-sm">
                            <option value="">All Post Offices</option>
                            @foreach ($postOffices as $po)
                                <option value="{{ $po->id }}"
                                    {{ request('present_post_office_id') == $po->id ? 'selected' : '' }}>
                                    {{ $po->name }}
                                </option>
                            @endforeach
                        </select>
                    </div> --}}

                    <!-- Contact & Age Filters -->
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Phone Number</label>
                        <input type="text" name="phone" class="form-control form-control-sm"
                            placeholder="Father/Mother/Guardian" value="{{ request('phone') }}">
                    </div>

                    {{-- <div class="col-md-3 mb-3">
                        <label class="form-label">Age From</label>
                        <input type="number" name="age_from" class="form-control form-control-sm" placeholder="Min Age"
                            value="{{ request('age_from') }}" min="0" max="100">
                    </div>

                    <div class="col-md-3 mb-3">
                        <label class="form-label">Age To</label>
                        <input type="number" name="age_to" class="form-control form-control-sm" placeholder="Max Age"
                            value="{{ request('age_to') }}" min="0" max="100">
                    </div> --}}

                    <div class="col-md-3 mb-3">
                        <label class="form-label">Gender</label>
                        <select name="gender" class="form-control form-control-sm">
                            <option value="">All Genders</option>
                            @foreach (\App\Models\Student::GENDERS as $key => $value)
                                <option value="{{ $key }}" {{ request('gender') == $key ? 'selected' : '' }}>
                                    {{ $value }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Search -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Search</label>
                        <input type="text" name="search" class="form-control form-control-sm"
                            placeholder="Name, Roll, Birth Certificate..." value="{{ request('search') }}">
                    </div>

                    <div class="col-md-3 mb-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-control form-control-sm">
                            <option value="">All Status</option>
                            <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Active</option>
                            <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>

                    <!-- Action Buttons -->
                    <div class="col-md-3 mb-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary btn-sm mr-2">
                            <i class="fas fa-search"></i> Filter
                        </button>
                        <a href="{{ route('students.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-redo"></i> Reset
                        </a>
                    </div>

                    <div class="col-12 mt-2">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <label class="form-label d-block mb-0">PDF Columns</label>
                            <div class="form-check mb-0">
                                <input
                                    class="form-check-input"
                                    type="checkbox"
                                    id="pdf-columns-toggle-all"
                                    checked
                                >
                                <label class="form-check-label" for="pdf-columns-toggle-all">
                                    Select/Deselect All
                                </label>
                            </div>
                        </div>
                        @php
                            $selectedPdfColumns = request()->input('pdf_columns', array_keys($pdfColumnOptions ?? []));
                        @endphp
                        <div class="d-flex flex-wrap" style="gap: 12px 18px;">
                            @foreach ($pdfColumnOptions as $columnKey => $columnLabel)
                                <div class="form-check mr-2">
                                    <input
                                        class="form-check-input pdf-column-checkbox"
                                        type="checkbox"
                                        name="pdf_columns[]"
                                        value="{{ $columnKey }}"
                                        id="pdf-column-{{ $columnKey }}"
                                        {{ in_array($columnKey, $selectedPdfColumns, true) ? 'checked' : '' }}
                                    >
                                    <label class="form-check-label" for="pdf-column-{{ $columnKey }}">
                                        {{ $columnLabel }}
                                    </label>
                                </div>
                            @endforeach
                        </div>
                        <small class="text-muted d-block mt-2">
                            The selected columns will be used when you click <strong>Export PDF</strong>. If none are selected, the PDF will include all columns.
                        </small>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
