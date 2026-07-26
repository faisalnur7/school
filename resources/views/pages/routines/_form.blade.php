@php
    $routine = $routine ?? null;
    $selectedClassId = old('school_class_id', $routine?->school_class_id);
    $selectedSectionId = old('section_id', $routine?->section_id);
    $selectedSubjectId = old('subject_id', $routine?->subject_id);
    $selectedTeacherId = old('teacher_id', $routine?->teacher_id);
    $selectedClassroomId = old('classroom_id', $routine?->classroom_id);
    $selectedDay = old('day', $routine?->day);
@endphp

<div class="row">
    <div class="col-md-6 mb-3">
        <label class="font-weight-bold">Class <span class="text-danger">*</span></label>
        <select
            name="school_class_id"
            id="routine_class_id"
            class="form-control"
            data-sections-url="{{ route('ajax.sections-by-class') }}"
            data-subjects-url="{{ route('subjects.by-class') }}"
            required
        >
            <option value="">Select class</option>
            @foreach ($classes as $class)
                <option value="{{ $class->id }}" @selected((string) $selectedClassId === (string) $class->id)>
                    {{ $class->name_en }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="col-md-6 mb-3">
        <label class="font-weight-bold">Section <span class="text-danger">*</span></label>
        <select
            name="section_id"
            id="routine_section_id"
            class="form-control"
            data-sections-url="{{ route('ajax.sections-by-class') }}"
            required
        >
            <option value="">Select section</option>
        </select>
    </div>

    <div class="col-md-6 mb-3">
        <label class="font-weight-bold">Subject <span class="text-danger">*</span></label>
        <select
            name="subject_id"
            id="routine_subject_id"
            class="form-control"
            data-subjects-url="{{ route('subjects.by-class') }}"
            required
        >
            <option value="">Select subject</option>
        </select>
    </div>

    <div class="col-md-6 mb-3">
        <label class="font-weight-bold">Teacher</label>
        <select name="teacher_id" id="routine_teacher_id" class="form-control">
            <option value="">Select teacher</option>
            @foreach ($teachers as $teacher)
                <option value="{{ $teacher->id }}" @selected((string) $selectedTeacherId === (string) $teacher->id)>
                    {{ $teacher->name }} @if($teacher->designation) ({{ $teacher->designation->name ?? $teacher->designation }}) @endif
                </option>
            @endforeach
        </select>
    </div>

    <div class="col-md-6 mb-3">
        <label class="font-weight-bold">Classroom</label>
        <select name="classroom_id" id="routine_classroom_id" class="form-control">
            <option value="">Select classroom</option>
            @foreach ($classrooms as $classroom)
                <option value="{{ $classroom->id }}" @selected((string) $selectedClassroomId === (string) $classroom->id)>
                    {{ $classroom->name_en }} @if($classroom->location) ({{ $classroom->location }}) @endif
                </option>
            @endforeach
        </select>
    </div>

    <div class="col-md-6 mb-3">
        <label class="font-weight-bold">Day <span class="text-danger">*</span></label>
        <select name="day" id="routine_day" class="form-control" required>
            <option value="">Select day</option>
            @foreach ($days as $day)
                <option value="{{ $day }}" @selected($selectedDay === $day)>{{ $day }}</option>
            @endforeach
        </select>
    </div>

    <div class="col-md-3 mb-3">
        <label class="font-weight-bold">Start Time <span class="text-danger">*</span></label>
        <input type="time" name="start_time" id="routine_start_time" class="form-control" value="{{ old('start_time', $routine?->start_time ? \Illuminate\Support\Str::substr($routine->start_time, 0, 5) : '') }}" required>
    </div>

    <div class="col-md-3 mb-3">
        <label class="font-weight-bold">End Time <span class="text-danger">*</span></label>
        <input type="time" name="end_time" id="routine_end_time" class="form-control" value="{{ old('end_time', $routine?->end_time ? \Illuminate\Support\Str::substr($routine->end_time, 0, 5) : '') }}" required>
    </div>
</div>

<div class="alert alert-info mb-0">
    Select a class first. Sections and subjects will be filtered to that class.
</div>
