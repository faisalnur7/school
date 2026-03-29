<!-- Filter Section -->
<div class="row mb-4">
    <div class="col-md-3 mb-3">
        <label class="form-label">Academic Session *</label>
        <select id="academic_session_id" name="academic_session_id" class="form-control" required>
            <option value="">Select Session</option>
            @foreach($sessions as $session)
                <option value="{{ $session->id }}">{{ $session->name_en }}</option>
            @endforeach
        </select>
    </div>

    <div class="col-md-3 mb-3">
        <label class="form-label">Fee Category *</label>
        <select id="fee_category_id" name="fee_category_id" class="form-control" required>
            <option value="">Select Fee Category</option>
            @foreach($feeCategories as $category)
                <option value="{{ $category->id }}">{{ $category->name }}</option>
            @endforeach
        </select>
    </div>

    <div class="col-md-2 mb-3">
        <label class="form-label">Class</label>
        <select id="classSelect" name="school_class_id" class="form-control">
            <option value="">All Classes</option>
            @foreach($classes as $class)
                <option value="{{ $class->id }}">{{ $class->name_en }}</option>
            @endforeach
        </select>
    </div>

    <div class="col-md-2 mb-3">
        <label class="form-label">Section</label>
        <select id="sectionSelect" name="section_id" class="form-control">
            <option value="">All Sections</option>
        </select>
    </div>

    <div class="col-md-2 mb-3">
        <label class="form-label">Group</label>
        <select id="groupSelect" name="group_id" class="form-control">
            <option value="">All Groups</option>
        </select>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-3">
        <button type="button" id="loadStudents" class="btn btn-primary btn-block">
            <i class="fas fa-search"></i> Load Students
        </button>
    </div>
</div>
