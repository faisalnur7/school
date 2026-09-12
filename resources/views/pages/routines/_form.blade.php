@php
    $routine = $routine ?? null;
    $selectedClassId = old('school_class_id', $routine?->school_class_id);
    $selectedSectionId = old('section_id', $routine?->section_id);
    $selectedSubjectId = old('subject_id', $routine?->subject_id);
    $selectedTeacherId = old('teacher_id', $routine?->teacher_id);
    $selectedClassroomId = old('classroom_id', $routine?->classroom_id);
    $selectedScheduleId = old('time_schedule_id', $routine?->time_schedule_id);
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

    <div class="col-md-6 mb-3">
        <label class="font-weight-bold">Time Schedule <span class="text-danger">*</span></label>
        <select name="time_schedule_id" id="routine_time_schedule_id" class="form-control" required>
            <option value="">Select period</option>
            @foreach ($schedules as $schedule)
                <option value="{{ $schedule->id }}" @selected((string) $selectedScheduleId === (string) $schedule->id)>
                    {{ $schedule->name }} ({{ $schedule->formatted_start_time }} - {{ $schedule->formatted_end_time }})
                </option>
            @endforeach
        </select>
    </div>
</div>

<div class="alert alert-info mb-0">
    Select a class first. Sections and subjects will be filtered to that class. Tiffin, prayer, and assembly slots are not available for class routines.
</div>
