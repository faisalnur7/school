<div class="row align-items-end mb-3">
    <div class="col-lg-2 col-md-4 mb-2">
        <label class="form-label mb-1">Academic Session *</label>
        <select id="academic_session_id" name="academic_session_id" class="form-control form-control-sm" required>
            <option value="">Select Session</option>
            @foreach($sessions as $session)
                <option value="{{ $session->id }}">{{ $session->name_en }}</option>
            @endforeach
        </select>
    </div>

    <div class="col-lg-2 col-md-4 mb-2">
        <label class="form-label mb-1">Student ID</label>
        <input type="text" id="studentCid" name="student_cid" class="form-control form-control-sm"
            placeholder="Enter Student ID">
    </div>

    <div class="col-lg-2 col-md-4 mb-2">
        <label class="form-label mb-1">Fee Category *</label>
        <select id="fee_category_id" name="fee_category_id" class="form-control form-control-sm" required>
            <option value="">Select Fee Category</option>
            @foreach($feeCategories as $category)
                <option value="{{ $category->id }}">{{ $category->name }}</option>
            @endforeach
        </select>
    </div>

    <div class="col-lg-2 col-md-4 mb-2">
        <label class="form-label mb-1">Class</label>
        <select id="classSelect" name="school_class_id" class="form-control form-control-sm">
            <option value="">All Classes</option>
            @foreach($classes as $class)
                <option value="{{ $class->id }}">{{ $class->name_en }}</option>
            @endforeach
        </select>
    </div>

    <div class="col-lg-2 col-md-4 mb-2">
        <label class="form-label mb-1">Section</label>
        <select id="sectionSelect" name="section_id" class="form-control form-control-sm">
            <option value="">All Sections</option>
        </select>
    </div>

    <div class="col-lg-2 col-md-4 mb-2">
        <label class="form-label mb-1">Group</label>
        <select id="groupSelect" name="group_id" class="form-control form-control-sm">
            <option value="">All Groups</option>
        </select>
    </div>

    <div class="col-12 mt-1">
        <button type="button" id="loadStudents" class="btn btn-primary btn-sm">
            <i class="fas fa-search mr-1"></i>Load Students
        </button>
    </div>
</div>
