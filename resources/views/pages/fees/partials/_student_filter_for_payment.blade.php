<div class="card mb-3">
    <div class="card-body">
        <form action="{{ route('fees.collect') }}" method="GET">
            <div class="row">

                {{-- Student ID --}}
                <div class="col-md-2">
                    <label>Student ID</label>
                    <input type="text"
                           name="student_id"
                           class="form-control"
                           value="{{ request('student_id') }}">
                </div>

                <div class="col-md-2">
                    <label>Session</label>
                    <select name="academic_session_id" class="form-control">
                        <option value="">All</option>
                        @foreach($sessions as $session)
                        <option value="{{ $session->id }}"
                            {{ request('academic_session_id')==$session->id ? 'selected':'' }}>
                            {{ $session->name_en }}
                        </option>
                        @endforeach
                    </select>
                </div>

                {{-- Class --}}
                <div class="col-md-2">
                    <label>Class</label>
                    <select name="school_class_id" id="classSelect" class="form-control school_class_id">
                        <option value="">All</option>
                        @foreach($classes as $class)
                        <option value="{{ $class->id }}"
                          {{ request('school_class_id')==$class->id ? 'selected':'' }}>
                          {{ $class->name_en }}
                        </option>
                        @endforeach
                    </select>
                </div>

                {{-- Section --}}
                <div class="col-md-2">
                    <label>Section</label>
                    <select name="section_id" id="sectionSelect" class="form-control section_id" >
                        <option value="">All</option>
                        @foreach($sections as $section)
                        <option value="{{ $section->id }}"
                          {{ request('section_id')==$section->id ? 'selected':'' }}>
                          {{ $section->name_en }}
                        </option>
                        @endforeach
                    </select>
                </div>

                {{-- Group --}}
                <div class="col-md-2">
                    <label>Group</label>
                    <select name="group_id" id="groupSelect" class="form-control group_id">
                        <option value="">All</option>
                        @foreach($groups as $group)
                        <option value="{{ $group->id }}"
                          {{ request('group_id')==$group->id ? 'selected':'' }}>
                          {{ $group->name_en }}
                        </option>
                        @endforeach
                    </select>
                </div>

                {{-- Button --}}
                <div class="col-md-2 mt-3 d-flex align-items-end">
                    <button class="btn btn-primary w-100" title="Search" aria-label="Search">
                        <i class="fas fa-search"></i>
                    </button>
                </div>

            </div>
        </form>
    </div>
</div>
