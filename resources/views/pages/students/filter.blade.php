@php
    $selectedPdfColumns = request()->input('pdf_columns', array_keys($pdfColumnOptions ?? []));
@endphp

<div class="students-toolbar">
    <form action="{{ route('students.index') }}" method="GET" class="students-filter-form">
        <div class="students-filter-row">
            <div class="students-search-field">
                <i class="fas fa-search"></i>
                <input
                    type="text"
                    name="search"
                    class="form-control students-search-input"
                    placeholder="Search by name, roll, CID, birth certificate..."
                    value="{{ request('search') }}"
                >
            </div>

            <select name="academic_session_id" class="form-control students-filter-select">
                <option value="">All Sessions</option>
                @foreach ($academicSessions as $session)
                    <option value="{{ $session->id }}" {{ request('academic_session_id') == $session->id ? 'selected' : '' }}>
                        {{ $session->name_en }}
                    </option>
                @endforeach
            </select>

            <select name="school_class_id" id="classSelect" class="form-control students-filter-select">
                <option value="">All Classes</option>
                @foreach ($classes as $class)
                    <option value="{{ $class->id }}" {{ request('school_class_id') == $class->id ? 'selected' : '' }}>
                        {{ $class->name_en }}
                    </option>
                @endforeach
            </select>

            <select name="section_id" id="sectionSelect" class="form-control students-filter-select">
                <option value="">All Sections</option>
                @foreach ($sections as $section)
                    <option value="{{ $section->id }}" {{ request('section_id') == $section->id ? 'selected' : '' }}>
                        {{ $section->name_en }}
                    </option>
                @endforeach
            </select>

            <select name="status" class="form-control students-filter-select">
                <option value="">All Status</option>
                <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Active</option>
                <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Inactive</option>
            </select>

            <select name="per_page" class="form-control students-filter-select" onchange="this.form.submit()">
                <option value="10" {{ (int) request('per_page', 10) === 10 ? 'selected' : '' }}>10 / page</option>
                <option value="20" {{ (int) request('per_page') === 20 ? 'selected' : '' }}>20 / page</option>
                <option value="30" {{ (int) request('per_page') === 30 ? 'selected' : '' }}>30 / page</option>
                <option value="40" {{ (int) request('per_page') === 40 ? 'selected' : '' }}>40 / page</option>
                <option value="50" {{ (int) request('per_page') === 50 ? 'selected' : '' }}>50 / page</option>
            </select>

            <div class="students-filter-actions">
                <button class="students-more-filters filter_button" type="button" title="More Filters" aria-label="More Filters">
                    <i class="fas fa-sliders-h"></i>
                    <span class="students-filter-count" data-filter-count>0</span>
                </button>
            </div>

            <div class="students-filter-actions students-filter-actions--submit" id="students-export-reset">
                <button type="submit" class="btn btn-dark students-action-btn" title="Apply Filters" aria-label="Apply Filters">
                    <i class="fas fa-search"></i>
                </button>
                <a href="{{ route('students.index') }}" class="btn btn-outline-secondary students-action-btn" title="Reset" aria-label="Reset">
                    <i class="fas fa-undo-alt"></i>
                </a>
            </div>
        </div>

        <div class="students-advanced-filters" id="filterCollapse">
            <div class="students-advanced-grid">
                <div class="students-filter-group">
                    <label for="groupSelect">Group</label>
                    <select name="group_id" id="groupSelect" class="form-control students-filter-select">
                        <option value="">All Groups</option>
                        @foreach ($groups as $group)
                            <option value="{{ $group->id }}" {{ request('group_id') == $group->id ? 'selected' : '' }}>
                                {{ $group->name_en }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="students-filter-group">
                    <label for="students-phone-filter">Phone Number</label>
                    <input
                        type="text"
                        id="students-phone-filter"
                        name="phone"
                        class="form-control students-filter-input"
                        placeholder="Father / Mother / Guardian"
                        value="{{ request('phone') }}"
                    >
                </div>

                <div class="students-filter-group">
                    <label for="students-gender-filter">Gender</label>
                    <select name="gender" id="students-gender-filter" class="form-control students-filter-select">
                        <option value="">All Genders</option>
                        @foreach (\App\Models\Student::GENDERS as $key => $value)
                            <option value="{{ $key }}" {{ request('gender') == $key ? 'selected' : '' }}>
                                {{ $value }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="students-pdf-panel">
                <div class="students-pdf-header">
                    <div class="students-pdf-copy">
                        <p class="students-pdf-title">PDF Column Selection</p>
                        <small class="students-pdf-subtitle text-muted">Choose which columns are included in the exported PDF list.</small>
                    </div>
                    <div class="form-check students-pdf-toggle mb-0">
                        <input class="form-check-input" type="checkbox" id="pdf-columns-toggle-all" checked>
                        <label class="form-check-label" for="pdf-columns-toggle-all">Select all</label>
                    </div>
                </div>

                <div class="students-pdf-checks">
                    @foreach ($pdfColumnOptions as $columnKey => $columnLabel)
                        <div class="form-check students-pdf-option">
                            <input
                                class="form-check-input pdf-column-checkbox"
                                type="checkbox"
                                name="pdf_columns[]"
                                value="{{ $columnKey }}"
                                id="pdf-column-{{ $columnKey }}"
                                {{ in_array($columnKey, $selectedPdfColumns, true) ? 'checked' : '' }}
                            >
                            <label class="form-check-label ml-4" for="pdf-column-{{ $columnKey }}">
                                {{ $columnLabel }}
                            </label>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </form>
</div>
