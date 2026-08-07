<!-- Filter Section -->
<div class="row mb-4 align-items-end">
    <div class="col-lg-2 col-md-4 mb-3">
        <label class="form-label">{{ __('Student ID') }}</label>
        <input type="text"
               name="student_cid"
               class="form-control"
               value="{{ request('student_cid') }}"
               placeholder="{{ __('Search by ID') }}">
    </div>

    <div class="col-lg-2 col-md-4 mb-3">
        <label class="form-label">{{ __('Academic Session *') }}</label>
        <select id="academic_session_id" name="academic_session_id" class="form-control" required>
            <option value="">{{ __('Select Session') }}</option>
            @foreach($sessions as $session)
                <option value="{{ $session->id }}" {{ request('academic_session_id') == $session->id ? 'selected' : '' }}>{{ $session->name_en }}</option>
            @endforeach
        </select>
    </div>

    {{-- <div class="col-md-3 mb-3">
        <label class="form-label">{{ __('Fee Category *') }}</label>
        <select id="fee_category_id" name="fee_category_id" class="form-control" required>
            <option value="">{{ __('Select Fee Category') }}</option>
            @foreach($feeCategories as $category)
                <option value="{{ $category->id }}" {{ request('fee_category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
            @endforeach
        </select>
    </div> --}}

    <div class="col-lg-2 col-md-4 mb-3">
        <label class="form-label">{{ __('Class') }}</label>
        <select id="classSelect" name="school_class_id" class="form-control">
            <option value="">{{ __('All Classes') }}</option>
            @foreach($classes as $class)
                <option value="{{ $class->id }}" {{ request('school_class_id') == $class->id ? 'selected' : '' }}>{{ $class->name_en }}</option>
            @endforeach
        </select>
    </div>

    <div class="col-lg-2 col-md-4 mb-3">
        <label class="form-label">{{ __('Section') }}</label>
        <select id="sectionSelect" name="section_id" class="form-control">
            <option value="">{{ __('All Sections') }}</option>
        </select>
    </div>

    <div class="col-lg-2 col-md-4 mb-3">
        <label class="form-label">{{ __('Group') }}</label>
        <select id="groupSelect" name="group_id" class="form-control">
            <option value="">{{ __('All Groups') }}</option>
        </select>
    </div>

    <div class="col-lg-2 col-md-4 mb-3">
        <label class="form-label">&nbsp;</label>
        @isset($isIndex)
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary flex-fill">
                    <i class="fas fa-search"></i>
                </button>
                <a href="{{ route('transports.index') }}" class="btn btn-secondary flex-fill" title="Reset" aria-label="Reset">
                    <i class="fas fa-times"></i>
                </a>
            </div>
        @else
            <button type="button" id="loadStudents" class="btn btn-primary btn-block w-100">
                <i class="fas fa-search"></i>
            </button>
        @endisset
    </div>
</div>
