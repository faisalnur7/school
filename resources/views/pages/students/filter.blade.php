
<div class="students-toolbar">
    <form action="{{ route('students.index') }}" method="GET" class="students-filter-form">
        <div class="students-filter-row">
            <div class="students-search-field">
                <i class="fas fa-search"></i>
                <input
                    type="text"
                    name="search"
                    aria-label="{{ __('Search students') }}"
                    class="form-control students-search-input"
                    placeholder="{{ __('Search by name, roll, CID, birth certificate...') }}"
                    value="{{ request('search') }}"
                >
            </div>

            <select name="academic_session_id" aria-label="{{ __('Academic session') }}" class="form-control students-filter-select">
                <option value="">{{ __('All Sessions') }}</option>
                @foreach ($academicSessions as $session)
                    <option value="{{ $session->id }}" {{ request('academic_session_id') == $session->id ? 'selected' : '' }}>
                        {{ $session->name_en }}
                    </option>
                @endforeach
            </select>

            <select name="school_class_id" id="classSelect" aria-label="{{ __('Class') }}" class="form-control students-filter-select">
                <option value="">{{ __('All Classes') }}</option>
                @foreach ($classes as $class)
                    <option value="{{ $class->id }}" {{ request('school_class_id') == $class->id ? 'selected' : '' }}>
                        {{ $class->name_en }}
                    </option>
                @endforeach
            </select>

            <select name="section_id" id="sectionSelect" aria-label="{{ __('Section') }}" class="form-control students-filter-select">
                <option value="">{{ __('All Sections') }}</option>
                @foreach ($sections as $section)
                    <option value="{{ $section->id }}" {{ request('section_id') == $section->id ? 'selected' : '' }}>
                        {{ $section->name_en }}
                    </option>
                @endforeach
            </select>

            <select name="status" aria-label="{{ __('Status') }}" class="form-control students-filter-select">
                <option value="">{{ __('All Status') }}</option>
                <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>{{ __('Active') }}</option>
                <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>{{ __('Inactive') }}</option>
            </select>

            <select name="per_page" aria-label="{{ __('Rows per page') }}" class="form-control students-filter-select" onchange="this.form.submit()">
                <option value="10" {{ (int) request('per_page', 10) === 10 ? 'selected' : '' }}>{{ __('10 / page') }}</option>
                <option value="20" {{ (int) request('per_page') === 20 ? 'selected' : '' }}>{{ __('20 / page') }}</option>
                <option value="30" {{ (int) request('per_page') === 30 ? 'selected' : '' }}>{{ __('30 / page') }}</option>
                <option value="40" {{ (int) request('per_page') === 40 ? 'selected' : '' }}>{{ __('40 / page') }}</option>
                <option value="50" {{ (int) request('per_page') === 50 ? 'selected' : '' }}>{{ __('50 / page') }}</option>
            </select>

            <div class="students-filter-actions">
                <button class="students-more-filters filter_button" type="button" title="{{ __('More Filters') }}" aria-label="{{ __('More Filters') }}" aria-controls="filterCollapse" data-students-filters-toggle>
                    <i class="fas fa-sliders-h"></i>
                    <span class="students-filter-count" data-filter-count>0</span>
                </button>
            </div>

            <div class="students-filter-actions students-filter-actions--submit" id="students-export-reset">
                <button type="submit" class="btn btn-dark students-action-btn" title="{{ __('Apply Filters') }}" aria-label="{{ __('Apply Filters') }}">
                    <i class="fas fa-search"></i>
                </button>
                <a href="{{ route('students.index') }}" class="btn btn-outline-secondary students-action-btn" title="{{ __('Reset') }}" aria-label="{{ __('Reset') }}">
                    <i class="fas fa-undo-alt"></i>
                </a>
            </div>
        </div>

        <div class="students-advanced-filters hidden" id="filterCollapse">
            <div class="students-advanced-grid">
                <div class="students-filter-group">
                    <label for="groupSelect">{{ __('Group') }}</label>
                    <select name="group_id" id="groupSelect" class="form-control students-filter-select">
                        <option value="">{{ __('All Groups') }}</option>
                        @foreach ($groups as $group)
                            <option value="{{ $group->id }}" {{ request('group_id') == $group->id ? 'selected' : '' }}>
                                {{ $group->name_en }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="students-filter-group">
                    <label for="students-phone-filter">{{ __('Phone Number') }}</label>
                    <input
                        type="text"
                        id="students-phone-filter"
                        name="phone"
                        class="form-control students-filter-input"
                        placeholder="{{ __('Father / Mother / Guardian') }}"
                        value="{{ request('phone') }}"
                    >
                </div>

                <div class="students-filter-group">
                    <label for="students-gender-filter">{{ __('Gender') }}</label>
                    <select name="gender" id="students-gender-filter" class="form-control students-filter-select">
                        <option value="">{{ __('All Genders') }}</option>
                        @foreach (\App\Models\Student::GENDERS as $key => $value)
                            <option value="{{ $key }}" {{ request('gender') == $key ? 'selected' : '' }}>
                                {{ $value }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

        </div>
    </form>
</div>
