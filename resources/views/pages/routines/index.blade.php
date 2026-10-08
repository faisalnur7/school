@extends('layouts.master')

@section('contents')
<div class="container-fluid routines-page">
    <div class="card shadow-sm border-0">
        <div class="card-header bg-gradient-primary text-white py-3">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div>
                    <h4 class="card-title mb-0 font-weight-bold text-white">
                        <i class="fas fa-clock mr-2"></i>Class Routines
                    </h4>
                    <small class="text-white-50">Manage weekly class schedules.</small>
                </div>
                <div class="d-flex align-items-center flex-wrap gap-2">
                    <div class="routine-view-switcher" role="group" aria-label="Routine view type">
                        <a href="{{ request()->fullUrlWithQuery(['view' => 'list']) }}" class="btn {{ $viewType === 'list' ? 'btn-light' : 'btn-outline-light' }}" title="List view">
                            <i class="fas fa-list mr-1"></i>List
                        </a>
                        <a href="{{ request()->fullUrlWithQuery(['view' => 'timetable']) }}" class="btn {{ $viewType === 'timetable' ? 'btn-light' : 'btn-outline-light' }}" title="Timetable view">
                            <i class="fas fa-table mr-1"></i>Timetable
                        </a>
                        <a href="{{ request()->fullUrlWithQuery(['view' => 'teacherwise']) }}" class="btn {{ $viewType === 'teacherwise' ? 'btn-light' : 'btn-outline-light' }}" title="Teacherwise view">
                            <i class="fas fa-user-tie mr-1"></i>Teacherwise
                        </a>
                        <a href="{{ request()->fullUrlWithQuery(['view' => 'playground']) }}" class="btn {{ $viewType === 'playground' ? 'btn-light' : 'btn-outline-light' }}" title="Routine playground">
                            <i class="fas fa-hand-pointer mr-1"></i>Playground
                        </a>
                        <a href="{{ request()->fullUrlWithQuery(['view' => 'teacherwise-playground']) }}" class="btn {{ $viewType === 'teacherwise-playground' ? 'btn-light' : 'btn-outline-light' }}" title="Teacherwise playground">
                            <i class="fas fa-users-gear mr-1"></i>Teacherwise playground
                        </a>
                    </div>
                    @if(auth()->user()?->hasPermission('create_routines'))
                        <a href="{{ route('routines.create') }}" class="btn btn-light btn-sm">
                            <i class="fas fa-plus mr-1"></i>Add Routine
                        </a>
                    @endif
                </div>
            </div>
        </div>

        <div class="card-body">
            @include('hr._alerts')

            <form method="GET" class="routines-filter-form mb-3">
                <div class="routines-filter-row">
                    <div class="routines-filter-field routines-filter-search">
                        <input type="text" name="search" class="form-control" value="{{ request('search') }}" placeholder="Search class, section, subject, teacher">
                    </div>
                    <div class="routines-filter-field routines-filter-session">
                        <select name="academic_session_id" class="form-control">
                            <option value="">All academic sessions</option>
                            @foreach ($academicSessions as $academicSession)
                                <option value="{{ $academicSession->id }}" @selected((string) $selectedAcademicSessionId === (string) $academicSession->id)>
                                    {{ $academicSession->name_en }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="routines-filter-field routines-filter-class">
                        <select name="school_class_id" class="form-control">
                            <option value="">All classes</option>
                            @foreach ($classes as $class)
                                <option value="{{ $class->id }}" @selected((string) request('school_class_id') === (string) $class->id)>
                                    {{ $class->name_en }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="routines-filter-field routines-filter-section">
                        <select name="section_id" id="routine-section-filter" class="form-control" @disabled(!request()->filled('school_class_id'))>
                            <option value="">{{ request()->filled('school_class_id') ? 'Loading sections...' : 'Choose a class first' }}</option>
                        </select>
                    </div>
                    <div class="routines-filter-field routines-filter-day">
                        <select name="day" class="form-control">
                            <option value="">All days</option>
                            @foreach ($days as $day)
                                <option value="{{ $day }}" @selected(request('day') === $day)>{{ $day }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="routines-filter-actions">
                        <button class="btn btn-primary btn-sm" type="submit" title="Filter" aria-label="Filter">
                            <i class="fas fa-search"></i>
                        </button>
                        <a href="{{ route('routines.index') }}" class="btn btn-outline-secondary btn-sm" title="Reset" aria-label="Reset">
                            <i class="fas fa-undo"></i>
                        </a>
                    </div>
                    <input type="hidden" name="view" value="{{ $viewType }}">
                </div>
            </form>

            @if($viewType === 'playground')
                @if(!request()->filled('academic_session_id') || !request()->filled('school_class_id') || !request()->filled('section_id'))
                    <div class="routine-playground-empty">
                        <div class="routine-playground-empty-icon"><i class="fas fa-sliders-h"></i></div>
                        <h5 class="mb-2">Choose a class section to open the playground</h5>
                        <p class="text-muted mb-0">Select an academic session, class, and section above. The live weekly grid will load with the periods configured in Time Schedule.</p>
                    </div>
                @else
                    <div class="routine-playground-toolbar">
                        <div>
                            <div class="routine-playground-kicker"><i class="fas fa-wand-magic-sparkles mr-1"></i> Routine playground</div>
                            <h5 class="mb-1">Build the weekly routine visually</h5>
                            <p class="text-muted mb-0">Drag a subject into a period, choose a teacher, then save all changes together.</p>
                        </div>
                        <div class="routine-playground-actions">
                            @php
                                $routineExportQuery = request()->only(['academic_session_id', 'school_class_id', 'section_id']);
                            @endphp
                            <a href="{{ route('routines.print', $routineExportQuery) }}" target="_blank" rel="noopener" class="btn btn-outline-secondary btn-sm routine-export-link routine-export-icon-btn" title="Print routine" aria-label="Print routine">
                                <i class="fas fa-print" aria-hidden="true"></i>
                            </a>
                            <a href="{{ route('routines.pdf', $routineExportQuery) }}" class="btn btn-outline-danger btn-sm routine-export-link routine-export-icon-btn" title="Download PDF" aria-label="Download PDF">
                                <i class="fas fa-file-pdf" aria-hidden="true"></i>
                            </a>
                            <span class="routine-unsaved-indicator" id="routine-unsaved-indicator" hidden><i class="fas fa-circle mr-1"></i> Unsaved changes</span>
                            <button type="button" class="btn btn-outline-secondary btn-sm" id="routine-reset-btn"><i class="fas fa-rotate-left mr-1"></i>Reset</button>
                            @if(auth()->user()?->hasPermission('edit_routines'))
                                <button type="button" class="btn btn-primary btn-sm" id="routine-save-btn"><i class="fas fa-cloud-arrow-up mr-1"></i>Save changes</button>
                            @endif
                        </div>
                    </div>

                    <div class="routine-playground-layout" id="routine-playground"
                        data-save-url="{{ route('routines.playground.save') }}"
                        data-session-id="{{ request('academic_session_id') }}"
                        data-class-id="{{ request('school_class_id') }}"
                        data-section-id="{{ request('section_id') }}">
                        <aside class="routine-playground-palette">
                            <div class="routine-palette-heading">
                                <div>
                                    <span class="routine-playground-kicker">Lesson cards</span>
                                    <h6 class="mb-0">Subjects</h6>
                                </div>
                                <span class="badge badge-light" id="routine-subject-count">{{ $playgroundSubjects->count() }}</span>
                            </div>
                            <div class="input-group input-group-sm routine-palette-search">
                                <div class="input-group-prepend"><span class="input-group-text"><i class="fas fa-search"></i></span></div>
                                <input type="search" class="form-control" id="routine-palette-search" placeholder="Find a subject..." aria-label="Find a subject">
                            </div>
                            <div class="routine-palette-list" id="routine-palette-list">
                                @forelse($playgroundSubjects as $subject)
                                    <div class="routine-subject-source" draggable="true" data-subject-id="{{ $subject->id }}" data-subject-name="{{ $subject->name }}" data-subject-code="{{ $subject->code }}">
                                        <span class="routine-subject-source-icon"><i class="fas fa-grip-vertical"></i></span>
                                        <span class="routine-subject-source-copy">
                                            <strong>{{ $subject->name }}</strong>
                                            @if($subject->code)<small>{{ $subject->code }}</small>@endif
                                        </span>
                                        <i class="fas fa-arrow-right routine-source-arrow"></i>
                                    </div>
                                @empty
                                    <div class="routine-palette-empty">No active subjects are assigned to this class.</div>
                                @endforelse
                            </div>
                            <div class="routine-palette-tip"><i class="fas fa-lightbulb mr-1"></i> Drop a subject into an empty cell. You can choose any active teacher.</div>
                        </aside>

                        <div class="routine-playground-board-wrap">
                            <div class="routine-board-scroll">
                                <table class="routine-playground-board">
                                    <thead>
                                        <tr>
                                            <th class="routine-board-day-header">Day</th>
                                            @foreach($periods as $period)
                                                <th class="routine-board-period-header">
                                                    <span>{{ $period->name }}</span>
                                                    <small>{{ substr($period->start_time, 0, 5) }} – {{ substr($period->end_time, 0, 5) }}</small>
                                                </th>
                                            @endforeach
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($days as $day)
                                            <tr>
                                                <th class="routine-board-day-cell"><span>{{ $day }}</span><small>{{ $loop->iteration }} / {{ count($days) }}</small></th>
                                                @foreach($periods as $period)
                                                    @php
                                                        $cellRoutine = $playgroundRoutineMap[$day . ':id:' . $period->id]
                                                            ?? $playgroundRoutineMap[$day . ':start:' . $period->start_time]
                                                            ?? null;
                                                    @endphp
                                                    <td class="routine-board-dropzone {{ $cellRoutine ? 'has-routine' : '' }}" data-day="{{ $day }}" data-period-id="{{ $period->id }}" data-period-name="{{ $period->name }}" aria-label="{{ $day }}, {{ $period->name }}">
                                                        @if($cellRoutine)
                                                            <div class="routine-board-card" draggable="true"
                                                                data-routine-id="{{ $cellRoutine->id }}"
                                                                data-subject-id="{{ $cellRoutine->subject_id }}"
                                                                data-subject-name="{{ $cellRoutine->subject?->name ?? 'Subject' }}"
                                                                data-teacher-id="{{ $cellRoutine->teacher_id }}"
                                                                data-teacher-name="{{ $cellRoutine->teacher?->name ?? '' }}"
                                                                data-classroom-id="{{ $cellRoutine->classroom_id }}"
                                                                data-classroom-name="{{ $cellRoutine->classroom?->name_en ?? '' }}">
                                                                <div class="routine-board-card-top">
                                                                    <strong>{{ $cellRoutine->subject?->name ?? '—' }}</strong>
                                                                    <span class="routine-card-actions"><button type="button" class="routine-card-edit" title="Edit lesson" aria-label="Edit {{ $cellRoutine->subject?->name }}"><i class="fas fa-pen"></i></button><button type="button" class="routine-card-remove" title="Remove from draft" aria-label="Remove {{ $cellRoutine->subject?->name }}"><i class="fas fa-trash"></i></button></span>
                                                                </div>
                                                                <span class="routine-board-card-teacher"><i class="fas fa-user-tie mr-1"></i>{{ $cellRoutine->teacher?->name ?? 'Teacher not selected' }}</span>
                                                                @if($cellRoutine->classroom)
                                                                    <span class="routine-board-card-room"><i class="fas fa-location-dot mr-1"></i>{{ $cellRoutine->classroom->name_en }}</span>
                                                                @endif
                                                            </div>
                                                        @else
                                                            <span class="routine-board-drop-hint"><i class="fas fa-plus"></i></span>
                                                        @endif
                                                    </td>
                                                @endforeach
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="routine-board-legend"><span><i class="fas fa-circle text-success mr-1"></i> Drop target</span><span><i class="fas fa-arrows-up-down-left-right mr-1"></i> Drag to move</span><span><i class="fas fa-shield-halved mr-1"></i> Conflicts are checked on save</span></div>
                        </div>
                    </div>

                    <div class="modal fade" id="routine-card-modal" tabindex="-1" role="dialog" aria-labelledby="routine-card-modal-title" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered" role="document">
                            <div class="modal-content routine-card-modal-content">
                                <div class="modal-header">
                                    <div><span class="routine-playground-kicker">Assign lesson</span><h5 class="modal-title" id="routine-card-modal-title">Choose a teacher</h5></div>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                </div>
                                <div class="modal-body">
                                    <div class="routine-modal-subject" id="routine-modal-subject"></div>
                                    <div class="form-group mb-3">
                                        <label>Days <small class="text-muted font-weight-normal">(select one or more)</small></label>
                                        <div class="routine-modal-days" id="routine-modal-days" role="group" aria-label="Routine days">
                                            @foreach($days as $day)
                                                <button type="button" class="routine-day-toggle" data-day="{{ $day }}" aria-pressed="false">{{ $day }}</button>
                                            @endforeach
                                        </div>
                                        <small class="form-text text-muted">The lesson will be placed in this period on every selected day.</small>
                                    </div>
                                    <div class="form-group mb-3"><label for="routine-modal-teacher">Teacher</label><select class="form-control" id="routine-modal-teacher"><option value="">Select teacher</option>@foreach($playgroundTeachers as $teacher)<option value="{{ $teacher->id }}">{{ $teacher->name }}</option>@endforeach</select></div>
                                    <div class="form-group mb-0"><label for="routine-modal-classroom">Room <span class="text-muted font-weight-normal">(optional)</span></label><select class="form-control" id="routine-modal-classroom"><option value="">No room selected</option>@foreach($playgroundClassrooms as $classroom)<option value="{{ $classroom->id }}">{{ $classroom->name_en }}@if($classroom->location) — {{ $classroom->location }}@endif</option>@endforeach</select></div>
                                </div>
                                <div class="modal-footer"><button type="button" class="btn btn-light" data-dismiss="modal">Cancel</button><button type="button" class="btn btn-primary" id="routine-modal-add"><i class="fas fa-check mr-1"></i>Place lesson</button></div>
                            </div>
                        </div>
                    </div>
                @endif
            @elseif($viewType === 'teacherwise-playground')
                @php
                    $shortDays = ['Saturday' => 'Sat', 'Sunday' => 'Sun', 'Monday' => 'Mon', 'Tuesday' => 'Tue', 'Wednesday' => 'Wed', 'Thursday' => 'Thu'];
                @endphp
                <div class="routine-playground-toolbar">
                    <div>
                        <div class="routine-playground-kicker"><i class="fas fa-users-gear mr-1"></i> Teacherwise playground</div>
                        <h5 class="mb-1">Arrange the active-session routine by teacher</h5>
                        <p class="text-muted mb-0">Drag an existing lesson between teachers and periods, then save all changes.</p>
                    </div>
                    <div class="routine-playground-actions">
                        <button type="button" class="btn btn-outline-secondary btn-sm" id="teacherwise-reset-btn"><i class="fas fa-rotate-left mr-1"></i>Reset</button>
                        @if(auth()->user()?->hasPermission('edit_routines'))
                            <button type="button" class="btn btn-primary btn-sm" id="teacherwise-save-btn"><i class="fas fa-cloud-arrow-up mr-1"></i>Save changes</button>
                        @endif
                    </div>
                </div>
                <div class="teacherwise-playground-scope"><i class="fas fa-circle-info mr-1"></i>Showing all classes and sections for the selected academic session. The class and section filters above do not limit this playground.</div>
                <div class="teacherwise-subject-palette" id="teacherwise-subject-palette">
                    <div class="teacherwise-palette-tabs" role="tablist" aria-label="Classes">
                        @foreach($teacherwisePalette as $classId => $assignments)
                            @php $paletteClass = $assignments->first()?->schoolClass; @endphp
                            @if($paletteClass)
                                <button type="button" class="teacherwise-class-tab" data-class-id="{{ $classId }}" aria-selected="false">{{ $paletteClass->name_en }}</button>
                            @endif
                        @endforeach
                    </div>
                    <div class="teacherwise-palette-sections">
                        <span class="teacherwise-palette-label">Sections</span>
                        @foreach($teacherwisePalette as $classId => $assignments)
                            <div class="teacherwise-section-options" data-class-id="{{ $classId }}" hidden>
                                @foreach($teacherwiseSections->where('school_class_id', $classId) as $section)
                                    <button type="button" class="teacherwise-section-tab" data-class-id="{{ $classId }}" data-section-id="{{ $section->id }}">{{ $section->name_en }}</button>
                                @endforeach
                            </div>
                        @endforeach
                    </div>
                    <div class="teacherwise-subject-palette-list">
                        <div class="teacherwise-palette-label">Subjects</div>
                        @foreach($teacherwisePalette as $classId => $assignments)
                            <div class="teacherwise-subject-options" data-class-id="{{ $classId }}" hidden>
                                @foreach($assignments->unique('subject_id')->sortBy(fn ($assignment) => $assignment->subject?->name) as $assignment)
                                    <div class="teacherwise-subject-source" draggable="true" data-class-id="{{ $classId }}" data-class-name="{{ $assignment->schoolClass?->name_en }}" data-subject-id="{{ $assignment->subject_id }}" data-subject-name="{{ $assignment->subject?->name }}">
                                        <i class="fas fa-grip-vertical"></i>
                                        <strong>{{ $assignment->subject?->name ?? 'Subject' }}</strong>
                                    </div>
                                @endforeach
                            </div>
                        @endforeach
                        <div class="teacherwise-palette-empty" id="teacherwise-palette-empty">Select a class and section to load subjects.</div>
                    </div>
                </div>
                <div class="routine-board-scroll teacherwise-playground-scroll" id="teacherwise-playground" data-save-url="{{ route('routines.teacherwise-playground.save') }}" data-session-id="{{ $selectedAcademicSessionId }}">
                    <table class="routine-playground-board teacherwise-playground-board">
                        <thead>
                            <tr>
                                <th class="routine-board-day-header">Teacher</th>
                                @foreach($periods as $period)
                                    <th class="routine-board-period-header"><span>{{ $period->name }}</span><small>{{ substr($period->start_time, 0, 5) }} – {{ substr($period->end_time, 0, 5) }}</small></th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($teacherwisePlaygroundRows as $row)
                                <tr>
                                    <th class="routine-board-day-cell">{{ $row['teacher'] }}</th>
                                    @foreach($periods as $period)
                                        @php $periodKey = $period->id ? 'id:' . $period->id : 'start:' . $period->start_time; $cellRoutines = $row['periods'][$periodKey] ?? []; @endphp
                                        <td class="routine-board-dropzone teacherwise-playground-dropzone" data-teacher-id="{{ $row['teacher_id'] }}" data-period-id="{{ $period->id }}">
                                            @foreach($cellRoutines as $routine)
                                                <div class="routine-board-card teacherwise-playground-card" draggable="true" data-routine-id="{{ $routine->id }}" data-day="{{ $routine->day }}" data-period-id="{{ $period->id }}" data-subject-id="{{ $routine->subject_id }}" data-class-id="{{ $routine->school_class_id }}" data-section-id="{{ $routine->section_id }}">
                                                    <button type="button" class="teacherwise-card-remove" title="Remove lesson" aria-label="Remove {{ $routine->subject?->name ?? 'lesson' }}"><i class="fas fa-trash" aria-hidden="true"></i></button>
                                                    <strong>{{ $routine->subject?->name ?? '—' }}</strong>
                                                    <span>{{ $routine->schoolClass?->name_en ?? '—' }} - {{ $routine->section?->name_en ?? '—' }}</span>
                                                    <small>{{ $shortDays[$routine->day] ?? $routine->day }}</small>
                                                </div>
                                            @endforeach
                                            @if(!$cellRoutines)<span class="routine-board-drop-hint"><i class="fas fa-plus"></i></span>@endif
                                        </td>
                                    @endforeach
                                </tr>
                            @empty
                                <tr><td colspan="{{ $periods->count() + 1 }}" class="text-center text-muted py-4">No routines found for the selected session.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="modal fade" id="teacherwise-subject-modal" tabindex="-1" role="dialog" aria-labelledby="teacherwise-subject-modal-title" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered" role="document">
                        <div class="modal-content routine-card-modal-content">
                            <div class="modal-header">
                                <div><span class="routine-playground-kicker">Place subject</span><h5 class="modal-title" id="teacherwise-subject-modal-title">Select teaching days</h5></div>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            </div>
                            <div class="modal-body">
                                <div class="routine-modal-subject" id="teacherwise-modal-subject"></div>
                                <div class="text-muted small mb-2" id="teacherwise-modal-context"></div>
                                <div class="routine-modal-days" id="teacherwise-modal-days" role="group" aria-label="Teaching days">
                                    @foreach(['Saturday' => 'Sat', 'Sunday' => 'Sun', 'Monday' => 'Mon', 'Tuesday' => 'Tue', 'Wednesday' => 'Wed', 'Thursday' => 'Thu'] as $day => $shortDay)
                                        <button type="button" class="routine-day-toggle teacherwise-modal-day" data-day="{{ $day }}" aria-pressed="false">{{ $shortDay }}</button>
                                    @endforeach
                                </div>
                            </div>
                            <div class="modal-footer"><button type="button" class="btn btn-light" data-dismiss="modal">Cancel</button><button type="button" class="btn btn-primary" id="teacherwise-modal-add"><i class="fas fa-check mr-1"></i>Place subject</button></div>
                        </div>
                    </div>
                </div>
            @elseif($viewType === 'timetable')
                @php
                    $routineExportQuery = request()->only(['academic_session_id', 'school_class_id', 'section_id']);
                    $routineExportQuery['view'] = 'timetable';
                @endphp
                <div class="routine-export-actions mb-3">
                    <a href="{{ route('routines.print', $routineExportQuery) }}" target="_blank" rel="noopener" class="btn btn-outline-secondary btn-sm routine-export-link routine-export-icon-btn" title="Print routine" aria-label="Print routine">
                        <i class="fas fa-print" aria-hidden="true"></i>
                    </a>
                    <a href="{{ route('routines.pdf', $routineExportQuery) }}" class="btn btn-outline-danger btn-sm routine-export-link routine-export-icon-btn" title="Download PDF" aria-label="Download PDF">
                        <i class="fas fa-file-pdf" aria-hidden="true"></i>
                    </a>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover routines-timetable mb-0">
                        <thead class="thead-dark">
                            <tr>
                                <th class="routines-day-column">Day</th>
                                @foreach($periods as $period)
                                    <th class="text-center routines-period-column">
                                        <div>{{ $period->name }}</div>
                                        <small class="font-weight-normal">{{ substr($period->start_time, 0, 5) }} - {{ substr($period->end_time, 0, 5) }}</small>
                                    </th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($days as $day)
                                <tr>
                                    <td class="font-weight-bold routines-day-cell">{{ $day }}</td>
                                    @foreach($periods as $period)
                                        @php
                                            $periodRoutines = collect();
                                            foreach ($routines as $routine) {
                                                if ($routine->day === $day && ((int) $routine->time_schedule_id === (int) $period->id || (!$routine->time_schedule_id && $routine->start_time === $period->start_time))) {
                                                    $periodRoutines->push($routine);
                                                }
                                            }
                                        @endphp
                                        <td class="routines-period-cell">
                                            @forelse($periodRoutines as $routine)
                                                <div class="routines-timetable-entry">
                                                    <div class="font-weight-bold">{{ $routine->subject?->name ?? '—' }}</div>
                                                    <div class="routines-timetable-teacher">{{ $routine->teacher?->name ?? '—' }}</div>
                                                    @if(!request()->filled('school_class_id') || !request()->filled('section_id'))
                                                        <small class="text-muted">{{ $routine->schoolClass?->name_en ?? '—' }} - {{ $routine->section?->name_en ?? '—' }}</small>
                                                    @endif
                                                </div>
                                            @empty
                                                <span class="text-muted">—</span>
                                            @endforelse
                                        </td>
                                    @endforeach
                                </tr>
                            @empty
                                <tr><td colspan="{{ $periods->count() + 1 }}" class="text-center text-muted py-4">No working days configured.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            @elseif($viewType === 'teacherwise')
                @php
                    $routineExportQuery = request()->only(['academic_session_id', 'school_class_id', 'section_id']);
                    $routineExportQuery['view'] = 'teacherwise';
                @endphp
                <div class="routine-export-actions mb-3">
                    <a href="{{ route('routines.print', $routineExportQuery) }}" target="_blank" rel="noopener" class="btn btn-outline-secondary btn-sm routine-export-link routine-export-icon-btn" title="Print teacherwise routine" aria-label="Print teacherwise routine">
                        <i class="fas fa-print" aria-hidden="true"></i>
                    </a>
                    <a href="{{ route('routines.pdf', $routineExportQuery) }}" class="btn btn-outline-danger btn-sm routine-export-link routine-export-icon-btn" title="Download teacherwise PDF" aria-label="Download teacherwise PDF">
                        <i class="fas fa-file-pdf" aria-hidden="true"></i>
                    </a>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover routines-timetable routines-teacherwise mb-0">
                        <thead class="thead-dark">
                            <tr>
                                <th class="routines-day-column">Teacher</th>
                                @foreach($periods as $period)
                                    <th class="text-center routines-period-column">
                                        <div>{{ $period->name }}</div>
                                        <small class="font-weight-normal">{{ substr($period->start_time, 0, 5) }} - {{ substr($period->end_time, 0, 5) }}</small>
                                    </th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($teacherwiseRows as $row)
                                <tr>
                                    <td class="font-weight-bold routines-day-cell">
                                        <div class="teacherwise-teacher-name">{{ $row['teacher'] }}</div>
                                        <span class="teacherwise-class-count">{{ $row['class_count'] }} {{ $row['class_count'] === 1 ? 'class' : 'classes' }}</span>
                                    </td>
                                    @foreach($periods as $period)
                                        @php
                                            $periodKey = $period->id ? 'id:' . $period->id : 'start:' . $period->start_time;
                                            $lessons = $row['periods'][$periodKey] ?? [];
                                        @endphp
                                        <td class="routines-period-cell">
                                            @forelse($lessons as $lesson)
                                                @php $routine = $lesson['routine']; @endphp
                                                <div class="routines-timetable-entry">
                                                    <div class="font-weight-bold">{{ $routine->subject?->name ?? '—' }}</div>
                                                    <small class="text-muted">{{ $routine->schoolClass?->name_en ?? '—' }} - {{ $routine->section?->name_en ?? '—' }} ({{ $lesson['day_range'] }})</small>
                                                </div>
                                            @empty
                                                <span class="text-muted">—</span>
                                            @endforelse
                                        </td>
                                    @endforeach
                                </tr>
                            @empty
                                <tr><td colspan="{{ $periods->count() + 1 }}" class="text-center text-muted py-4">No teacher routines found.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            @else
            <div class="table-responsive">
                <table class="table table-bordered table-hover table-sm">
                    <thead class="thead-dark">
                        <tr>
                            <th>#</th>
                            <th>Academic session</th>
                            <th>Class</th>
                            <th>Section</th>
                            <th>Subject</th>
                            <th>Teacher</th>
                            <th>Room</th>
                            <th>Day</th>
                            <th>Time</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($routines as $routine)
                            <tr>
                                <td>{{ $routines->firstItem() + $loop->index }}</td>
                                <td>{{ $routine->academicSession?->name_en ?? '—' }}</td>
                                <td>{{ $routine->schoolClass?->name_en ?? '—' }}</td>
                                <td>{{ $routine->section?->name_en ?? '—' }}</td>
                                <td>
                                    <div class="font-weight-bold">{{ $routine->subject?->name ?? '—' }}</div>
                                    @if($routine->subject?->code)
                                        <small class="text-muted">{{ $routine->subject->code }}</small>
                                    @endif
                                </td>
                                <td>{{ $routine->teacher?->name ?? '—' }}</td>
                                <td>{{ $routine->classroom?->name_en ?? '—' }}</td>
                                <td>{{ $routine->day }}</td>
                                <td>{{ $routine->timeSchedule?->name ?? '—' }}<br><small class="text-muted">{{ substr($routine->start_time, 0, 5) }} - {{ substr($routine->end_time, 0, 5) }}</small></td>
                                <td class="text-center">
                                    @if(auth()->user()?->hasPermission('view_routines'))
                                        <a href="{{ route('routines.show', $routine->id) }}" class="btn btn-xs routines-action-btn routines-action-view" title="View" aria-label="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    @endif
                                    @if(auth()->user()?->hasPermission('edit_routines'))
                                        <a href="{{ route('routines.edit', $routine->id) }}" class="btn btn-xs routines-action-btn routines-action-edit" title="Edit" aria-label="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    @endif
                                    @if(auth()->user()?->hasPermission('delete_routines'))
                                        <form action="{{ route('routines.delete', $routine->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this routine?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-xs routines-action-btn routines-action-delete" title="Delete" aria-label="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center text-muted py-4">No routines found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{ $routines->links() }}
            @endif
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
    html[data-theme='dark'] .routines-page .card,
    html.dark .routines-page .card,
    html[data-theme='dark'] .routines-page .card-body,
    html.dark .routines-page .card-body {
        background: #111827 !important;
        border-color: #334155 !important;
        color: #e2e8f0 !important;
    }

    html[data-theme='dark'] .routines-page .card-header,
    html.dark .routines-page .card-header {
        background: linear-gradient(135deg, #172554 0%, #111827 100%) !important;
        border-color: #334155 !important;
    }

    html[data-theme='dark'] .routines-page form,
    html.dark .routines-page form {
        background: #111827 !important;
    }

    html[data-theme='dark'] .routines-page .form-control,
    html.dark .routines-page .form-control {
        background-color: #0f172a !important;
        border-color: #334155 !important;
        color: #e2e8f0 !important;
    }

    html[data-theme='dark'] .routines-page .form-control::placeholder,
    html.dark .routines-page .form-control::placeholder {
        color: #94a3b8 !important;
    }

    html[data-theme='dark'] .routines-page .form-control option,
    html.dark .routines-page .form-control option {
        background: #0f172a;
        color: #e2e8f0;
    }

    html[data-theme='dark'] .routines-page .table,
    html.dark .routines-page .table {
        --bs-table-bg: #111827;
        --bs-table-color: #e2e8f0;
        --bs-table-hover-bg: #1e293b;
        --bs-table-hover-color: #f8fafc;
        border-color: #334155 !important;
    }

    html[data-theme='dark'] .routines-page .table thead,
    html.dark .routines-page .table thead,
    html[data-theme='dark'] .routines-page .table thead th,
    html.dark .routines-page .table thead th {
        background: #1e293b !important;
        border-color: #475569 !important;
        color: #e2e8f0 !important;
    }

    html[data-theme='dark'] .routines-page .table td,
    html.dark .routines-page .table td {
        border-color: #334155 !important;
        color: #e2e8f0 !important;
    }

    html[data-theme='dark'] .routines-page .text-muted,
    html.dark .routines-page .text-muted {
        color: #94a3b8 !important;
    }

    html[data-theme='dark'] .routines-page .btn-outline-secondary,
    html.dark .routines-page .btn-outline-secondary {
        background: #111827 !important;
        border-color: #475569 !important;
        color: #cbd5e1 !important;
    }

    .routines-filter-form {
        padding: 0.8rem;
        border: 1px solid #dbe3ef;
        border-radius: 1rem;
    }

    .routines-filter-row {
        display: flex;
        align-items: center;
        gap: 0.7rem;
        flex-wrap: nowrap;
    }

    .routines-filter-field {
        min-width: 0;
        flex: 1 1 0;
    }

    .routines-filter-search {
        flex: 1.35 1 0;
    }

    .routines-filter-actions {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        flex: 0 0 auto;
    }

    .routines-filter-actions .btn {
        width: 2.8rem;
        height: 2.8rem;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 0;
        border-radius: 0.7rem;
    }

    .routines-page .routines-action-btn {
        width: 2.15rem;
        height: 2.15rem;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 0;
        border: 0;
        border-radius: 0.55rem;
        color: #fff !important;
        box-shadow: none;
    }

    .routines-page .routines-action-view {
        background: #16a6b9 !important;
    }

    .routines-page .routines-action-edit {
        background: #334155 !important;
        border: 1px solid #475569 !important;
        color: #fff !important;
    }

    .routines-page .routines-action-delete {
        background: #ef3340 !important;
    }

    .routines-page .routines-action-btn:hover,
    .routines-page .routines-action-btn:focus {
        filter: brightness(1.08);
        transform: translateY(-1px);
    }

    .routines-timetable {
        min-width: 980px;
    }

    .routines-timetable .routines-day-column {
        width: 9rem;
        vertical-align: middle;
    }

    .routines-timetable .routines-period-column {
        min-width: 10rem;
        vertical-align: middle;
    }

    .routines-period-column small {
        display: block;
        margin-top: .2rem;
        color: #64748b;
        font-size: .75rem;
        font-weight: 700;
        line-height: 1.2;
    }

    html[data-theme='dark'] .routines-period-column small,
    html.dark .routines-period-column small { color: #e2e8f0; font-weight: 700; }

    .routines-day-cell {
        background: #f8fafc;
        vertical-align: middle !important;
    }

    .routines-period-cell {
        min-width: 10rem;
        height: 5.5rem;
        vertical-align: top !important;
    }

    .routines-timetable-entry + .routines-timetable-entry {
        margin-top: 0.5rem;
        padding-top: 0.5rem;
        border-top: 1px solid #dbe3ef;
    }

    .routines-page .routine-export-icon-btn {
        width: 2.2rem;
        height: 2.2rem;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 0 !important;
        border-radius: .55rem;
        background: #fff;
        box-shadow: 0 1px 2px rgba(15, 23, 42, .05);
        line-height: 1;
        transition: background .16s ease, border-color .16s ease, color .16s ease, box-shadow .16s ease, transform .16s ease;
    }

    .routines-page .routine-export-actions {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: .5rem;
    }

    .routines-page .routine-export-icon-btn:hover,
    .routines-page .routine-export-icon-btn:focus {
        transform: translateY(-1px);
        box-shadow: 0 4px 10px rgba(15, 23, 42, .12);
    }

    .routines-page .routine-export-icon-btn:active {
        transform: translateY(0);
        box-shadow: 0 1px 2px rgba(15, 23, 42, .08);
    }

    .routines-page .routine-export-icon-btn i { font-size: .9rem; }

    .routines-page .routine-export-icon-btn.btn-outline-secondary:hover,
    .routines-page .routine-export-icon-btn.btn-outline-secondary:focus {
        background: #f1f5f9;
        border-color: #94a3b8;
        color: #0f172a;
    }

    .routines-page .routine-export-icon-btn.btn-outline-danger:hover,
    .routines-page .routine-export-icon-btn.btn-outline-danger:focus {
        background: #fef2f2;
        border-color: #f87171;
        color: #b91c1c;
    }

    html[data-theme='dark'] .routines-page .routine-export-icon-btn.btn-outline-secondary,
    html.dark .routines-page .routine-export-icon-btn.btn-outline-secondary {
        background: #111827;
        border-color: #475569;
        color: #cbd5e1;
        box-shadow: 0 1px 2px rgba(0, 0, 0, .2);
    }

    html[data-theme='dark'] .routines-page .routine-export-icon-btn.btn-outline-secondary:hover,
    html.dark .routines-page .routine-export-icon-btn.btn-outline-secondary:hover,
    html[data-theme='dark'] .routines-page .routine-export-icon-btn.btn-outline-secondary:focus,
    html.dark .routines-page .routine-export-icon-btn.btn-outline-secondary:focus {
        background: #1e293b;
        color: #f8fafc;
    }

    html[data-theme='dark'] .routines-page .routine-export-icon-btn.btn-outline-danger,
    html.dark .routines-page .routine-export-icon-btn.btn-outline-danger {
        background: #111827;
        border-color: #7f1d1d;
        color: #fca5a5;
        box-shadow: 0 1px 2px rgba(0, 0, 0, .2);
    }

    html[data-theme='dark'] .routines-page .routine-export-icon-btn.btn-outline-danger:hover,
    html.dark .routines-page .routine-export-icon-btn.btn-outline-danger:hover,
    html[data-theme='dark'] .routines-page .routine-export-icon-btn.btn-outline-danger:focus,
    html.dark .routines-page .routine-export-icon-btn.btn-outline-danger:focus {
        background: #450a0a;
        color: #fecaca;
    }

    .routines-timetable-teacher {
        color: #475569;
        font-size: 0.86rem;
    }

    .routines-teacherwise .routines-timetable-entry .font-weight-bold {
        color: #111827 !important;
        font-size: 1rem;
        line-height: 1.25;
    }

    .routines-teacherwise .routines-timetable-entry small,
    .routines-teacherwise .routines-timetable-entry .text-muted {
        color: #111827 !important;
        font-size: .88rem;
        line-height: 1.25;
    }

    .routines-teacherwise-days {
        display: inline-flex;
        align-items: center;
        min-height: 1.35rem;
        padding: .1rem .4rem;
        margin-bottom: .2rem;
        border-radius: .35rem;
        background: #dbeafe;
        color: #1d4ed8;
        font-size: .72rem;
        font-weight: 800;
        line-height: 1;
    }

    .teacherwise-teacher-name { line-height: 1.25; }
    .teacherwise-class-count { display: inline-block; margin-top: .45rem; padding: .18rem .45rem; border-radius: .35rem; background: #dbeafe; color: #1d4ed8; font-size: .7rem; font-weight: 800; line-height: 1.1; }

    html[data-theme='dark'] .routines-day-cell,
    html.dark .routines-day-cell {
        background: #0f172a !important;
    }

    html[data-theme='dark'] .routines-timetable-entry + .routines-timetable-entry,
    html.dark .routines-timetable-entry + .routines-timetable-entry {
        border-color: #334155;
    }

    html[data-theme='dark'] .routines-timetable-teacher,
    html.dark .routines-timetable-teacher {
        color: #cbd5e1;
    }

    html[data-theme='dark'] .routines-teacherwise-days,
    html.dark .routines-teacherwise-days {
        background: #172554;
        color: #bfdbfe;
    }
    html[data-theme='dark'] .teacherwise-class-count,
    html.dark .teacherwise-class-count { background: #172554; color: #bfdbfe; }

    html[data-theme='dark'] .routines-filter-form,
    html.dark .routines-filter-form {
        background: #111827 !important;
        border-color: #334155 !important;
    }

    .routine-playground-empty {
        min-height: 18rem;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-direction: column;
        text-align: center;
        padding: 2rem;
        border: 1px dashed #cbd5e1;
        border-radius: 1.25rem;
        background: linear-gradient(145deg, #f8fbff, #f1f5f9);
    }

    .routine-playground-empty-icon {
        width: 3.4rem;
        height: 3.4rem;
        display: grid;
        place-items: center;
        margin-bottom: 1rem;
        color: #2563eb;
        background: #dbeafe;
        border-radius: 1rem;
        font-size: 1.35rem;
    }

    .routine-playground-toolbar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 1rem;
        margin-bottom: 1rem;
    }

    .routine-playground-kicker {
        color: #2563eb;
        font-size: .7rem;
        font-weight: 800;
        letter-spacing: .08em;
        text-transform: uppercase;
    }

    .routine-playground-actions {
        display: flex;
        align-items: center;
        gap: .55rem;
        flex-wrap: wrap;
        justify-content: flex-end;
    }

    .routines-page .routine-playground-actions .btn {
        min-height: 2.55rem;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: .4rem;
        padding: .55rem .9rem;
        border-radius: .7rem;
        font-size: .78rem;
        font-weight: 800;
        letter-spacing: .01em;
        transition: transform .16s ease, box-shadow .16s ease, background .16s ease, border-color .16s ease;
    }

    .routines-page .routine-playground-actions .btn-outline-secondary {
        border: 1px solid #cbd5e1;
        background: #fff;
        color: #475569;
    }

    .routines-page .routine-playground-actions .btn-outline-secondary:hover,
    .routines-page .routine-playground-actions .btn-outline-secondary:focus {
        border-color: #94a3b8;
        background: #f8fafc;
        color: #0f172a;
        box-shadow: 0 .3rem .7rem rgba(15, 23, 42, .08);
        transform: translateY(-1px);
    }

    .routines-page .routine-playground-actions .btn-outline-danger {
        border: 1px solid #fecaca;
        background: #fff;
        color: #dc2626;
    }

    .routines-page .routine-playground-actions .btn-outline-danger:hover,
    .routines-page .routine-playground-actions .btn-outline-danger:focus {
        border-color: #f87171;
        background: #fef2f2;
        color: #b91c1c;
        box-shadow: 0 .3rem .7rem rgba(220, 38, 38, .1);
        transform: translateY(-1px);
    }

    .routines-page .routine-playground-actions .btn-primary {
        border: 1px solid #2563eb;
        background: linear-gradient(135deg, #2563eb, #1d4ed8);
        color: #fff;
        box-shadow: 0 .35rem .8rem rgba(37, 99, 235, .22);
    }

    .routines-page .routine-playground-actions .btn-primary:hover,
    .routines-page .routine-playground-actions .btn-primary:focus {
        border-color: #1d4ed8;
        background: linear-gradient(135deg, #1d4ed8, #1e40af);
        box-shadow: 0 .5rem 1rem rgba(37, 99, 235, .3);
        transform: translateY(-1px);
    }

    .routines-page .routine-playground-actions .btn:active { transform: translateY(0); }
    .routines-page .routine-playground-actions .btn i { font-size: .78rem; }

    .routine-unsaved-indicator {
        color: #b45309;
        font-size: .78rem;
        font-weight: 700;
        white-space: nowrap;
    }

    .routine-unsaved-indicator i { font-size: .45rem; vertical-align: middle; }

    .routine-view-switcher {
        display: inline-flex;
        align-items: center;
        gap: .35rem;
        padding: .2rem;
        border: 1px solid rgba(255, 255, 255, .28);
        border-radius: .85rem;
        background: rgba(15, 23, 42, .14);
    }

    .routines-page .routine-view-switcher .btn {
        min-height: 2.35rem;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: .35rem;
        padding: .45rem .8rem;
        border: 1px solid transparent !important;
        border-radius: .65rem !important;
        font-size: .78rem;
        font-weight: 700;
        line-height: 1;
        transition: background .16s ease, border-color .16s ease, color .16s ease, transform .16s ease, box-shadow .16s ease;
    }

    .routines-page .routine-view-switcher .btn.btn-light {
        background: #0f172a !important;
        border-color: #0f172a !important;
        color: #fff !important;
        box-shadow: 0 .25rem .6rem rgba(15, 23, 42, .2);
    }

    .routines-page .routine-view-switcher .btn.btn-outline-light {
        background: rgba(255, 255, 255, .92) !important;
        border-color: #cbd5e1 !important;
        color: #94a3b8 !important;
    }

    .routines-page .routine-view-switcher .btn.btn-outline-light:hover,
    .routines-page .routine-view-switcher .btn.btn-outline-light:focus {
        background: #fff !important;
        border-color: #93c5fd !important;
        color: #2563eb !important;
        transform: translateY(-1px);
    }

    .routines-page .routine-view-switcher .btn i { font-size: .78rem; }

    .routines-page .card-header .routine-view-switcher + .btn-light {
        border-radius: .7rem;
        font-weight: 700;
    }

    @media (max-width: 575.98px) {
        .routine-view-switcher { width: 100%; }
        .routines-page .routine-view-switcher .btn { flex: 1 1 0; padding-left: .55rem; padding-right: .55rem; }
        .routines-page .routine-view-switcher .btn i { margin-right: 0 !important; }
    }

    .routine-playground-layout {
        display: grid;
        grid-template-columns: minmax(15rem, 18rem) minmax(0, 1fr);
        gap: 1rem;
        align-items: start;
    }

    .routine-playground-palette,
    .routine-playground-board-wrap {
        border: 1px solid #e2e8f0;
        border-radius: 1rem;
        background: #fff;
        box-shadow: 0 .5rem 1.5rem rgba(15, 23, 42, .05);
    }

    .routine-playground-palette { padding: 1rem; position: sticky; top: 1rem; }

    .routine-palette-heading { display: flex; align-items: center; justify-content: space-between; margin-bottom: .85rem; }
    .routine-palette-heading h6 { font-weight: 800; color: #0f172a; }
    .routine-palette-search { margin-bottom: .75rem; }
    .routine-palette-search .input-group-text { background: #f8fafc; border-color: #e2e8f0; color: #64748b; }
    .routine-palette-list { display: grid; gap: .55rem; max-height: 34rem; overflow: auto; padding: .15rem; }

    .routine-subject-source {
        display: flex;
        align-items: center;
        gap: .55rem;
        min-height: 3.2rem;
        padding: .65rem .7rem;
        border: 1px solid #e2e8f0;
        border-radius: .8rem;
        background: #f8fafc;
        cursor: grab;
        transition: transform .16s ease, box-shadow .16s ease, border-color .16s ease;
    }

    .routine-subject-source:hover { transform: translateY(-1px); border-color: #93c5fd; box-shadow: 0 .3rem .8rem rgba(37, 99, 235, .1); }
    .routine-subject-source:active { cursor: grabbing; }
    .routine-subject-source-icon { color: #94a3b8; }
    .routine-subject-source-copy { min-width: 0; flex: 1; }
    .routine-subject-source-copy strong { display: block; overflow: hidden; color: #0f172a; font-size: .84rem; text-overflow: ellipsis; white-space: nowrap; }
    .routine-subject-source-copy small { display: block; color: #64748b; font-size: .7rem; }
    .routine-source-arrow { color: #2563eb; font-size: .72rem; }
    .routine-palette-empty { padding: 1rem .5rem; color: #64748b; font-size: .82rem; text-align: center; }
    .routine-palette-tip { margin-top: .85rem; padding: .7rem; border-radius: .7rem; background: #eff6ff; color: #1d4ed8; font-size: .74rem; line-height: 1.45; }

    .routine-board-scroll { overflow: auto; border-radius: 1rem; }
    .routine-playground-board { width: 100%; min-width: 920px; border-collapse: separate; border-spacing: 0; table-layout: fixed; }
    .routine-playground-board th, .routine-playground-board td { border-right: 1px solid #e2e8f0; border-bottom: 1px solid #e2e8f0; }
    .routine-playground-board tr > :last-child { border-right: 0; }
    .routine-playground-board tbody tr:last-child > * { border-bottom: 0; }
    .routine-board-day-header { width: 8.5rem; padding: 1rem; color: #fff; background: #0f172a; text-align: left; }
    .routine-board-period-header { min-width: 10rem; padding: .85rem .7rem; color: #fff; background: #0f172a; text-align: center; vertical-align: middle; }
    .routine-board-period-header span, .routine-board-period-header small { display: block; }
    .routine-board-period-header span { font-size: .8rem; font-weight: 800; }
    .routine-board-period-header small { margin-top: .25rem; color: #94a3b8; font-size: .68rem; font-weight: 500; }
    .routine-board-day-cell { padding: .8rem; background: #f8fafc; color: #0f172a; text-align: left; vertical-align: top; }
    .routine-board-day-cell span, .routine-board-day-cell small { display: block; }
    .routine-board-day-cell span { font-size: .85rem; font-weight: 800; }
    .routine-board-day-cell small { margin-top: .2rem; color: #94a3b8; font-size: .68rem; font-weight: 500; }
    .routine-playground-board tbody td,
    .routine-board-dropzone { position: relative; height: 7.7rem; padding: .5rem; background: #fff; vertical-align: top !important; transition: background .16s ease, box-shadow .16s ease; }
    .routine-board-dropzone.is-over { background: #eff6ff; box-shadow: inset 0 0 0 2px #60a5fa; }
    .routine-board-dropzone.has-routine { background: #fbfdff; }
    .routine-board-drop-hint { display: grid; place-items: center; height: 100%; color: #cbd5e1; font-size: .78rem; opacity: 0; transition: opacity .16s ease; }
    .routine-board-dropzone:hover .routine-board-drop-hint, .routine-board-dropzone.is-over .routine-board-drop-hint { opacity: 1; }

    .routine-board-card { position: absolute; inset: .5rem; min-height: 0; padding: .7rem; border: 1px solid #bfdbfe; border-left: 3px solid #2563eb; border-radius: .75rem; background: linear-gradient(145deg, #eff6ff, #fff); cursor: grab; box-shadow: 0 .25rem .65rem rgba(37, 99, 235, .08); }
    .routine-board-card:active { cursor: grabbing; }
    .routine-board-card-top { display: flex; align-items: flex-start; gap: .35rem; justify-content: space-between; }
    .routine-board-card strong { color: #1e3a8a; font-size: .78rem; line-height: 1.25; }
    .routine-board-card-teacher, .routine-board-card-room { display: block; margin-top: .45rem; color: #475569; font-size: .7rem; line-height: 1.25; }
    .routine-board-card-room { margin-top: .3rem; color: #64748b; }

    .teacherwise-playground-board .teacherwise-playground-dropzone {
        height: auto;
        min-height: 7.7rem;
    }

    .teacherwise-playground-board .teacherwise-playground-card {
        position: relative;
        inset: auto;
        min-height: 0;
        margin-bottom: .45rem;
    }

    .teacherwise-playground-board .teacherwise-playground-card:last-child { margin-bottom: 0; }
    .teacherwise-playground-card { padding-right: 2rem; }
    .teacherwise-card-remove { position: absolute; top: .45rem; right: .45rem; width: 1.35rem; height: 1.35rem; display: grid; place-items: center; padding: 0; border: 0; border-radius: .35rem; background: transparent; color: #ef4444; font-size: .65rem; opacity: .72; cursor: pointer; }
    .teacherwise-card-remove:hover, .teacherwise-card-remove:focus { background: #fee2e2; color: #b91c1c; opacity: 1; outline: 0; }
    .teacherwise-playground-card span,
    .teacherwise-playground-card small { display: block; margin-top: .35rem; color: #475569; font-size: .68rem; line-height: 1.2; }
    .teacherwise-playground-card small { color: #2563eb; font-weight: 800; }

    .teacherwise-subject-palette {
        margin-bottom: 1rem;
        padding: .8rem;
        border: 1px solid #dbe3ef;
        border-radius: 1rem;
        background: #f8fafc;
    }

    .teacherwise-playground-scope { margin: -.35rem 0 .8rem; color: #64748b; font-size: .76rem; }

    .teacherwise-palette-tabs,
    .teacherwise-section-options,
    .teacherwise-subject-options { display: flex; flex-wrap: wrap; gap: .45rem; }
    .teacherwise-palette-tabs { margin-bottom: .75rem; }
    .teacherwise-class-tab,
    .teacherwise-section-tab { padding: .45rem .75rem; border: 1px solid #cbd5e1; border-radius: .55rem; background: #fff; color: #475569; font-size: .75rem; font-weight: 700; cursor: pointer; }
    .teacherwise-class-tab[aria-selected='true'],
    .teacherwise-section-tab.is-selected { border-color: #2563eb; background: #2563eb; color: #fff; }
    .teacherwise-palette-label { margin-right: .6rem; color: #64748b; font-size: .7rem; font-weight: 800; letter-spacing: .04em; text-transform: uppercase; }
    .teacherwise-palette-sections { display: flex; align-items: center; gap: .5rem; min-height: 2rem; margin-bottom: .7rem; }
    .teacherwise-subject-palette-list { display: flex; align-items: flex-start; gap: .5rem; }
    .teacherwise-subject-source { display: inline-flex; align-items: center; gap: .45rem; padding: .5rem .65rem; border: 1px solid #bfdbfe; border-radius: .55rem; background: #fff; color: #1e3a8a; cursor: grab; font-size: .76rem; }
    .teacherwise-subject-source i { color: #60a5fa; }
    .teacherwise-subject-source:active { cursor: grabbing; }
    .teacherwise-palette-empty { color: #94a3b8; font-size: .76rem; }
    .teacherwise-modal-day.is-unavailable,
    .teacherwise-modal-day:disabled { border-color: #e2e8f0; background: #f1f5f9; color: #94a3b8; cursor: not-allowed; opacity: .7; }
    .routine-card-actions { display: inline-flex; gap: .15rem; }
    .routine-card-edit, .routine-card-remove { width: 1.4rem; height: 1.4rem; display: grid; place-items: center; padding: 0; border: 0; border-radius: .4rem; background: transparent; color: #64748b; font-size: .67rem; }
    .routine-card-edit:hover { background: #dbeafe; color: #2563eb; }
    .routine-card-remove { color: #dc2626; }
    .routine-card-remove:hover, .routine-card-remove:focus { background: #fee2e2; color: #b91c1c; outline: 0; }
    .routine-board-legend { display: flex; gap: 1rem; flex-wrap: wrap; padding: .75rem 1rem; color: #64748b; font-size: .72rem; }
    .routine-card-modal-content { border: 0; border-radius: 1rem; box-shadow: 0 1.5rem 4rem rgba(15, 23, 42, .2); }
    .routine-modal-subject { margin-bottom: 1rem; padding: .8rem 1rem; border-radius: .75rem; background: #eff6ff; color: #1e40af; font-weight: 800; }
    .routine-modal-days { display: flex; flex-wrap: wrap; gap: .45rem; }
    .routine-day-toggle { min-width: 4.7rem; padding: .45rem .7rem; border: 1px solid #cbd5e1; border-radius: .55rem; background: #fff; color: #475569; font-size: .75rem; font-weight: 700; transition: color .15s ease, background .15s ease, border-color .15s ease, box-shadow .15s ease; }
    .routine-day-toggle:hover, .routine-day-toggle:focus { border-color: #60a5fa; color: #1d4ed8; outline: 0; box-shadow: 0 0 0 .15rem rgba(59, 130, 246, .12); }
    .routine-day-toggle.is-selected { border-color: #2563eb; background: #2563eb; color: #fff; box-shadow: 0 .2rem .45rem rgba(37, 99, 235, .2); }

    html[data-theme='dark'] .routine-playground-empty,
    html.dark .routine-playground-empty { background: linear-gradient(145deg, #111827, #0f172a); border-color: #475569; }
    html[data-theme='dark'] .routine-playground-palette,
    html[data-theme='dark'] .routine-playground-board-wrap,
    html.dark .routine-playground-palette,
    html.dark .routine-playground-board-wrap { background: #111827; border-color: #334155; }
    html[data-theme='dark'] .routine-palette-heading h6,
    html.dark .routine-palette-heading h6,
    html[data-theme='dark'] .routine-board-day-cell,
    html.dark .routine-board-day-cell { color: #e2e8f0; }
    html[data-theme='dark'] .routine-subject-source,
    html.dark .routine-subject-source,
    html[data-theme='dark'] .routine-board-day-cell,
    html.dark .routine-board-day-cell { background: #0f172a; }
    html[data-theme='dark'] .routine-subject-source-copy strong,
    html.dark .routine-subject-source-copy strong { color: #e2e8f0; }
    html[data-theme='dark'] .routine-playground-board th,
    html[data-theme='dark'] .routine-playground-board td,
    html.dark .routine-playground-board th,
    html.dark .routine-playground-board td { border-color: #334155; }
    html[data-theme='dark'] .routine-board-dropzone,
    html.dark .routine-board-dropzone { background: #111827; }
    html[data-theme='dark'] .routine-board-dropzone.has-routine,
    html.dark .routine-board-dropzone.has-routine { background: #172033; }
    html[data-theme='dark'] .routine-board-card,
    html.dark .routine-board-card { border-color: #1d4ed8; background: linear-gradient(145deg, #172554, #111827); }
    html[data-theme='dark'] .routine-board-card strong,
    html.dark .routine-board-card strong { color: #bfdbfe; }

    html[data-theme='dark'] .teacherwise-playground-card span,
    html.dark .teacherwise-playground-card span { color: #cbd5e1; }
    html[data-theme='dark'] .teacherwise-playground-card small,
    html.dark .teacherwise-playground-card small { color: #93c5fd; }
    html[data-theme='dark'] .teacherwise-card-remove,
    html.dark .teacherwise-card-remove { color: #fca5a5; }
    html[data-theme='dark'] .teacherwise-card-remove:hover,
    html[data-theme='dark'] .teacherwise-card-remove:focus,
    html.dark .teacherwise-card-remove:hover,
    html.dark .teacherwise-card-remove:focus { background: #450a0a; color: #fecaca; }
    html[data-theme='dark'] .teacherwise-subject-palette,
    html.dark .teacherwise-subject-palette { border-color: #334155; background: #111827; }
    html[data-theme='dark'] .teacherwise-playground-scope,
    html.dark .teacherwise-playground-scope { color: #94a3b8; }
    html[data-theme='dark'] .teacherwise-class-tab,
    html[data-theme='dark'] .teacherwise-section-tab,
    html[data-theme='dark'] .teacherwise-subject-source,
    html.dark .teacherwise-class-tab,
    html.dark .teacherwise-section-tab,
    html.dark .teacherwise-subject-source { border-color: #475569; background: #1e293b; color: #e2e8f0; }
    html[data-theme='dark'] .teacherwise-modal-day.is-unavailable,
    html[data-theme='dark'] .teacherwise-modal-day:disabled,
    html.dark .teacherwise-modal-day.is-unavailable,
    html.dark .teacherwise-modal-day:disabled { border-color: #334155; background: #0f172a; color: #64748b; }
    html[data-theme='dark'] .teacherwise-class-tab[aria-selected='true'],
    html[data-theme='dark'] .teacherwise-section-tab.is-selected,
    html.dark .teacherwise-class-tab[aria-selected='true'],
    html.dark .teacherwise-section-tab.is-selected { border-color: #60a5fa; background: #2563eb; color: #fff; }
    html[data-theme='dark'] .routine-day-toggle,
    html.dark .routine-day-toggle { border-color: #475569; background: #1e293b; color: #cbd5e1; }
    html[data-theme='dark'] .routine-day-toggle.is-selected,
    html.dark .routine-day-toggle.is-selected { border-color: #60a5fa; background: #2563eb; color: #fff; }
    html[data-theme='dark'] .routine-board-card-teacher,
    html.dark .routine-board-card-teacher,
    html[data-theme='dark'] .routine-board-card-room,
    html.dark .routine-board-card-room { color: #cbd5e1; }
    html[data-theme='dark'] .routines-page .routine-playground-actions .btn-outline-secondary,
    html.dark .routines-page .routine-playground-actions .btn-outline-secondary { background: #111827; border-color: #475569; color: #cbd5e1; }
    html[data-theme='dark'] .routines-page .routine-playground-actions .btn-outline-secondary:hover,
    html.dark .routines-page .routine-playground-actions .btn-outline-secondary:hover { background: #1e293b; color: #f8fafc; }
    html[data-theme='dark'] .routines-page .routine-playground-actions .btn-outline-danger,
    html.dark .routines-page .routine-playground-actions .btn-outline-danger { background: #111827; border-color: #7f1d1d; color: #fca5a5; }
    html[data-theme='dark'] .routines-page .routine-playground-actions .btn-outline-danger:hover,
    html.dark .routines-page .routine-playground-actions .btn-outline-danger:hover { background: #450a0a; color: #fecaca; }

    @media (max-width: 991.98px) {
        .routine-playground-layout { grid-template-columns: 1fr; }
        .routine-playground-palette { position: static; }
        .routine-palette-list { max-height: 15rem; grid-template-columns: repeat(2, minmax(0, 1fr)); }
    }

    @media (max-width: 575.98px) {
        .routine-playground-toolbar { align-items: flex-start; flex-direction: column; }
        .routine-playground-actions { width: 100%; justify-content: flex-start; }
        .routine-palette-list { grid-template-columns: 1fr; }
    }

    @media (max-width: 1100px) {
        .routines-filter-row {
            flex-wrap: wrap;
        }

        .routines-filter-field {
            flex: 1 1 calc(33.333% - 0.7rem);
        }

        .routines-filter-actions {
            margin-left: auto;
        }
    }

    @media (max-width: 767.98px) {
        .routines-filter-field {
            flex: 1 1 100%;
        }

        .routines-filter-actions {
            width: 100%;
            justify-content: flex-end;
        }
    }
</style>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const classSelect = document.querySelector('select[name="school_class_id"]');
    const sectionSelect = document.getElementById('routine-section-filter');
    if (!classSelect || !sectionSelect) return;

    const selectedClassId = () => {
        const select2Value = window.jQuery ? $(classSelect).val() : '';
        const select2Data = window.jQuery && $(classSelect).hasClass('select2-hidden-accessible')
            ? ($(classSelect).select2('data')[0]?.id || '')
            : '';

        return String(classSelect.value || select2Value || select2Data || classSelect.options[classSelect.selectedIndex]?.value || '');
    };

    const refreshSelect2 = () => {
        if (window.jQuery && typeof window.refreshSelect2 === 'function') {
            window.refreshSelect2($(sectionSelect));
        }
    };

    const loadSections = (classId, selectedSectionId = '') => {
        if (!classId) {
            sectionSelect.innerHTML = '<option value="">Choose a class first</option>';
            sectionSelect.value = '';
            sectionSelect.disabled = true;
            refreshSelect2();
            return;
        }

        sectionSelect.innerHTML = '<option value="">Loading sections...</option>';
        sectionSelect.value = '';
        sectionSelect.disabled = true;
        refreshSelect2();

        fetch(`{{ route('load_section_groups') }}?school_class_id=${encodeURIComponent(classId)}`, {
            headers: { 'Accept': 'application/json' },
        })
            .then((response) => {
                if (!response.ok) throw new Error('Failed to load sections');
                return response.json();
            })
            .then((data) => {
                const sections = Array.isArray(data?.sections) ? data.sections : [];
                sectionSelect.innerHTML = '<option value="">All sections</option>';
                sections.forEach((section) => {
                    const option = document.createElement('option');
                    option.value = section.id;
                    option.textContent = section.name_en;
                    if (String(selectedSectionId) === String(section.id)) option.selected = true;
                    sectionSelect.appendChild(option);
                });
                sectionSelect.disabled = false;
                refreshSelect2();
            })
            .catch(() => {
                sectionSelect.innerHTML = '<option value="">Unable to load sections</option>';
                sectionSelect.disabled = true;
                refreshSelect2();
                if (window.toastr) toastr.error('Sections could not be loaded for this class.');
            });
    };

    const initialClassId = selectedClassId();
    const initialSectionId = @json(request('section_id'));

    if (window.jQuery) {
        $(document).on('change.routineSections select2:select.routineSections select2:clear.routineSections', 'select[name="school_class_id"]', function () {
            loadSections(selectedClassId(), '');
        });
    } else {
        classSelect.addEventListener('change', () => loadSections(selectedClassId(), ''));
    }

    loadSections(initialClassId, initialSectionId);
});
</script>
@if($viewType === 'teacherwise-playground')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const board = document.getElementById('teacherwise-playground');
    if (!board) return;

    const csrf = document.querySelector('meta[name="csrf-token"]')?.content;
    const saveButton = document.getElementById('teacherwise-save-btn');
    const resetButton = document.getElementById('teacherwise-reset-btn');
    const subjectModal = $('#teacherwise-subject-modal');
    const modalSubject = document.getElementById('teacherwise-modal-subject');
    const modalContext = document.getElementById('teacherwise-modal-context');
    const modalDays = [...document.querySelectorAll('.teacherwise-modal-day')];
    const paletteEmpty = document.getElementById('teacherwise-palette-empty');
    let pendingSubject = null;
    let dirty = false;

    const markDirty = () => { dirty = true; };

    const notify = (message, type = 'warning') => {
        if (window.toastr) toastr[type](message);
        else window.alert(message);
    };

    const selectedDays = () => modalDays.filter((button) => !button.disabled && button.classList.contains('is-selected')).map((button) => button.dataset.day);
    const setSelectedDays = (days) => {
        const selected = new Set(days);
        modalDays.forEach((button) => {
            const active = selected.has(button.dataset.day);
            button.classList.toggle('is-selected', active);
            button.setAttribute('aria-pressed', active ? 'true' : 'false');
        });
    };

    const setOccupiedDays = (occupiedDays) => {
        modalDays.forEach((button) => {
            const occupied = occupiedDays.has(button.dataset.day);
            button.disabled = occupied;
            button.classList.toggle('is-unavailable', occupied);
            if (occupied) {
                button.classList.remove('is-selected');
                button.setAttribute('aria-pressed', 'false');
                button.title = 'This teacher already has a routine on this day in this period';
            } else {
                button.removeAttribute('title');
            }
        });
    };

    const cardTemplate = (data, day) => {
        const card = document.createElement('div');
        card.className = 'routine-board-card teacherwise-playground-card';
        card.draggable = true;
        card.dataset.routineId = `new-${Date.now()}-${Math.random().toString(36).slice(2)}`;
        card.dataset.clientOnly = '1';
        card.dataset.day = day;
        card.dataset.periodId = data.periodId;
        card.dataset.subjectId = data.subjectId;
        card.dataset.classId = data.classId;
        card.dataset.sectionId = data.sectionId;
        card.innerHTML = `<button type="button" class="teacherwise-card-remove" title="Remove lesson" aria-label="Remove lesson"><i class="fas fa-trash" aria-hidden="true"></i></button><strong></strong><span></span><small></small>`;
        card.querySelector('strong').textContent = data.subjectName;
        card.querySelector('span').textContent = `${data.className} - ${data.sectionName}`;
        card.querySelector('small').textContent = day.slice(0, 3);
        bindCardDrag(card);
        return card;
    };

    const bindCardDrag = (card) => {
        card.addEventListener('dragstart', (event) => {
            if (event.target.closest('button')) { event.preventDefault(); return; }
            event.dataTransfer.effectAllowed = 'move';
            event.dataTransfer.setData('text/plain', card.dataset.routineId);
            card.classList.add('is-dragging');
        });
        card.addEventListener('dragend', () => card.classList.remove('is-dragging'));
    };

    document.querySelectorAll('.teacherwise-playground-card').forEach(bindCardDrag);

    board.addEventListener('click', (event) => {
        const removeButton = event.target.closest('.teacherwise-card-remove');
        if (!removeButton) return;
        event.preventDefault();
        removeButton.closest('.teacherwise-playground-card')?.remove();
        markDirty();
    });

    document.querySelectorAll('.teacherwise-class-tab').forEach((tab) => {
        tab.addEventListener('click', () => {
            const classId = tab.dataset.classId;
            document.querySelectorAll('.teacherwise-class-tab').forEach((item) => item.setAttribute('aria-selected', item === tab ? 'true' : 'false'));
            document.querySelectorAll('.teacherwise-section-options, .teacherwise-subject-options').forEach((panel) => { panel.hidden = panel.dataset.classId !== classId; });
            document.querySelectorAll('.teacherwise-section-tab').forEach((item) => item.classList.remove('is-selected'));
            document.querySelectorAll('.teacherwise-subject-options').forEach((panel) => { panel.hidden = true; });
            paletteEmpty.hidden = false;
        });
    });

    document.querySelectorAll('.teacherwise-section-tab').forEach((tab) => {
        tab.addEventListener('click', () => {
            document.querySelectorAll('.teacherwise-section-tab').forEach((item) => item.classList.toggle('is-selected', item === tab));
            document.querySelectorAll('.teacherwise-subject-options').forEach((panel) => { panel.hidden = panel.dataset.classId !== tab.dataset.classId; });
            paletteEmpty.hidden = true;
        });
    });

    document.querySelector('.teacherwise-class-tab')?.click();

    document.querySelectorAll('.teacherwise-subject-source').forEach((source) => {
        source.addEventListener('dragstart', (event) => {
            const section = document.querySelector('.teacherwise-section-tab.is-selected');
            if (!section) {
                event.preventDefault();
                notify('Select a section before placing a subject.');
                return;
            }
            event.dataTransfer.effectAllowed = 'copy';
            event.dataTransfer.setData('text/plain', JSON.stringify({
                type: 'subject',
                subjectId: source.dataset.subjectId,
                subjectName: source.dataset.subjectName,
                classId: source.dataset.classId,
                className: source.dataset.className,
                sectionId: section.dataset.sectionId,
                sectionName: section.textContent.trim(),
            }));
        });
    });

    document.querySelectorAll('.teacherwise-playground-dropzone').forEach((cell) => {
        cell.addEventListener('dragover', (event) => {
            event.preventDefault();
            cell.classList.add('is-over');
        });
        cell.addEventListener('dragleave', (event) => {
            if (!cell.contains(event.relatedTarget)) cell.classList.remove('is-over');
        });
        cell.addEventListener('drop', (event) => {
            event.preventDefault();
            cell.classList.remove('is-over');
            const payload = event.dataTransfer.getData('text/plain');
            let data;
            try { data = JSON.parse(payload); } catch { data = null; }
            if (data?.type === 'subject') {
                const occupiedDays = new Set([...cell.querySelectorAll('.teacherwise-playground-card')].map((card) => card.dataset.day));
                if (occupiedDays.size >= modalDays.length) {
                    notify('This teacher already has routines on all available days in this period.');
                    return;
                }
                pendingSubject = {...data, periodId: cell.dataset.periodId, cell};
                modalSubject.textContent = data.subjectName;
                modalContext.textContent = `${data.sectionName} · ${cell.closest('tr')?.querySelector('.routine-board-day-cell')?.textContent.trim() || 'Teacher'}`;
                setSelectedDays([]);
                setOccupiedDays(occupiedDays);
                subjectModal.modal('show');
                return;
            }
            const card = document.querySelector(`.teacherwise-playground-card[data-routine-id="${CSS.escape(payload)}"]`);
            if (card) { cell.appendChild(card); markDirty(); }
        });
    });

    modalDays.forEach((button) => button.addEventListener('click', () => {
        button.classList.toggle('is-selected');
        button.setAttribute('aria-pressed', button.classList.contains('is-selected') ? 'true' : 'false');
    }));

    document.getElementById('teacherwise-modal-add')?.addEventListener('click', () => {
        if (!pendingSubject) return;
        const days = selectedDays();
        if (!days.length) { notify('Select at least one teaching day.'); return; }
        days.forEach((day) => pendingSubject.cell.appendChild(cardTemplate(pendingSubject, day)));
        markDirty();
        pendingSubject = null;
        subjectModal.modal('hide');
    });

    saveButton?.addEventListener('click', async () => {
        const originalHtml = saveButton.innerHTML;
        saveButton.disabled = true;
        saveButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i>Saving...';
        const items = [...document.querySelectorAll('.teacherwise-playground-card')].map((card) => {
            const cell = card.closest('.teacherwise-playground-dropzone');
            return {
                id: card.dataset.clientOnly === '1' ? null : Number(card.dataset.routineId),
                teacher_id: cell.dataset.teacherId || null,
                time_schedule_id: Number(cell.dataset.periodId),
                subject_id: Number(card.dataset.subjectId),
                school_class_id: Number(card.dataset.classId),
                section_id: Number(card.dataset.sectionId),
                day: card.dataset.day,
            };
        });

        try {
            const response = await fetch(board.dataset.saveUrl, {
                method: 'POST',
                headers: {'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf},
                body: JSON.stringify({academic_session_id: Number(board.dataset.sessionId), items}),
            });
            const result = await response.json().catch(() => ({}));
            if (!response.ok) throw new Error(result.message || Object.values(result.errors || {}).flat()[0] || 'The teacherwise routine could not be saved.');
            dirty = false;
            if (window.toastr) toastr.success(result.message || 'Teacherwise routine saved successfully.');
            window.setTimeout(() => window.location.reload(), 450);
        } catch (error) {
            if (window.toastr) toastr.error(error.message);
            else window.alert(error.message);
        } finally {
            saveButton.disabled = false;
            saveButton.innerHTML = originalHtml;
        }
    });

    resetButton?.addEventListener('click', () => {
        if (!dirty || window.confirm('Discard all unsaved teacherwise changes?')) window.location.reload();
    });

    window.addEventListener('beforeunload', (event) => {
        if (!dirty) return;
        event.preventDefault();
        event.returnValue = '';
    });
});
</script>
@endif
@if($viewType === 'playground' && request()->filled('academic_session_id') && request()->filled('school_class_id') && request()->filled('section_id'))
<script>
document.addEventListener('DOMContentLoaded', function () {
    const playground = document.getElementById('routine-playground');
    if (!playground) return;

    const csrf = document.querySelector('meta[name="csrf-token"]')?.content;
    const modal = $('#routine-card-modal');
    const subjectModal = document.getElementById('routine-modal-subject');
    const dayButtons = [...document.querySelectorAll('.routine-day-toggle')];
    const teacherSelect = document.getElementById('routine-modal-teacher');
    const classroomSelect = document.getElementById('routine-modal-classroom');
    const saveButton = document.getElementById('routine-save-btn');
    const resetButton = document.getElementById('routine-reset-btn');
    const unsavedIndicator = document.getElementById('routine-unsaved-indicator');
    let pending = null;
    let dirty = false;

    document.querySelectorAll('.routine-export-link').forEach((link) => {
        link.addEventListener('click', (event) => {
            if (!dirty) return;
            event.preventDefault();
            notify('Save your playground changes before printing or downloading the PDF.', 'warning');
        });
    });

    const notify = (message, type = 'info') => {
        if (window.toastr) toastr[type](message);
        else window.alert(message);
    };

    const markDirty = () => {
        dirty = true;
        if (unsavedIndicator) unsavedIndicator.hidden = false;
    };

    const setCellState = (cell) => cell?.classList.toggle('has-routine', !!cell.querySelector('.routine-board-card'));

    const selectedDays = () => dayButtons
        .filter((button) => button.classList.contains('is-selected'))
        .map((button) => button.dataset.day);

    const setSelectedDays = (days) => {
        const selected = new Set(days);
        dayButtons.forEach((button) => {
            const active = selected.has(button.dataset.day);
            button.classList.toggle('is-selected', active);
            button.setAttribute('aria-pressed', active ? 'true' : 'false');
        });
    };

    dayButtons.forEach((button) => {
        button.addEventListener('click', () => {
            button.classList.toggle('is-selected');
            button.setAttribute('aria-pressed', button.classList.contains('is-selected') ? 'true' : 'false');
        });
    });

    const findCell = (day, periodId) => [...document.querySelectorAll('.routine-board-dropzone')]
        .find((cell) => cell.dataset.day === day && String(cell.dataset.periodId) === String(periodId));

    const selectedTeacherId = () => {
        const select2Value = window.jQuery ? $(teacherSelect).val() : '';
        const select2Data = window.jQuery && $(teacherSelect).hasClass('select2-hidden-accessible')
            ? ($(teacherSelect).select2('data')[0]?.id || '')
            : '';

        return String(teacherSelect.value || select2Value || select2Data || teacherSelect.options[teacherSelect.selectedIndex]?.value || '');
    };

    const cardTemplate = (data) => {
        const card = document.createElement('div');
        const clientId = data.routineId || `new-${Date.now()}-${Math.random().toString(36).slice(2)}`;
        card.className = 'routine-board-card';
        card.draggable = true;
        card.dataset.routineId = clientId;
        card.dataset.clientOnly = data.routineId ? '0' : '1';
        card.dataset.subjectId = data.subjectId;
        card.dataset.subjectName = data.subjectName;
        card.dataset.teacherId = data.teacherId || '';
        card.dataset.teacherName = data.teacherName || '';
        card.dataset.classroomId = data.classroomId || '';
        card.dataset.classroomName = data.classroomName || '';
        card.innerHTML = `
            <div class="routine-board-card-top">
                <strong></strong>
                <span class="routine-card-actions">
                    <button type="button" class="routine-card-edit" title="Edit lesson" aria-label="Edit lesson"><i class="fas fa-pen"></i></button>
                    <button type="button" class="routine-card-remove" title="Remove from draft" aria-label="Remove lesson"><i class="fas fa-trash"></i></button>
                </span>
            </div>
            <span class="routine-board-card-teacher"><i class="fas fa-user-tie mr-1"></i></span>
            <span class="routine-board-card-room"><i class="fas fa-location-dot mr-1"></i></span>`;
        card.querySelector('strong').textContent = data.subjectName || 'Subject';
        card.querySelector('.routine-board-card-teacher').append(document.createTextNode(data.teacherName || 'Teacher not selected'));
        const room = card.querySelector('.routine-board-card-room');
        if (data.classroomName) room.append(document.createTextNode(data.classroomName));
        else room.remove();
        return card;
    };

    const openCardModal = (data, targetCell, existingCard = null) => {
        pending = { data, targetCell, existingCard };
        subjectModal.textContent = data.subjectName || 'Subject';
        setSelectedDays([targetCell?.dataset.day].filter(Boolean));
        teacherSelect.value = data.teacherId || '';
        classroomSelect.value = data.classroomId || '';
        if (window.jQuery && $(teacherSelect).hasClass('select2-hidden-accessible')) $(teacherSelect).trigger('change');
        if (window.jQuery && $(classroomSelect).hasClass('select2-hidden-accessible')) $(classroomSelect).trigger('change');
        modal.modal('show');
    };

    document.querySelectorAll('.routine-subject-source').forEach((source) => {
        source.addEventListener('dragstart', (event) => {
            event.dataTransfer.effectAllowed = 'copy';
            event.dataTransfer.setData('text/plain', JSON.stringify({
                type: 'subject',
                subjectId: source.dataset.subjectId,
                subjectName: source.dataset.subjectName,
            }));
            source.classList.add('is-dragging');
        });
        source.addEventListener('dragend', () => source.classList.remove('is-dragging'));
    });

    const bindCardDrag = (card) => {
        card.addEventListener('dragstart', (event) => {
            if (event.target.closest('button')) { event.preventDefault(); return; }
            event.dataTransfer.effectAllowed = 'move';
            event.dataTransfer.setData('text/plain', JSON.stringify({ type: 'routine', cardId: card.dataset.routineId || '' }));
            card.classList.add('is-dragging');
        });
        card.addEventListener('dragend', () => card.classList.remove('is-dragging'));
    };

    document.querySelectorAll('.routine-board-card').forEach(bindCardDrag);

    document.querySelectorAll('.routine-board-dropzone').forEach((cell) => {
        cell.addEventListener('dragover', (event) => {
            event.preventDefault();
            cell.classList.add('is-over');
        });
        cell.addEventListener('dragleave', (event) => {
            if (!cell.contains(event.relatedTarget)) cell.classList.remove('is-over');
        });
        cell.addEventListener('drop', (event) => {
            event.preventDefault();
            cell.classList.remove('is-over');
            if (cell.querySelector('.routine-board-card')) {
                notify('This period already has a lesson. Move or remove it first.', 'warning');
                return;
            }
            let payload;
            try { payload = JSON.parse(event.dataTransfer.getData('text/plain')); } catch { return; }

            if (payload.type === 'subject') {
                openCardModal(payload, cell);
                return;
            }

            const card = document.querySelector(`.routine-board-card[data-routine-id="${CSS.escape(payload.cardId)}"]`);
            if (!card) return;
            const oldCell = card.closest('.routine-board-dropzone');
            cell.appendChild(card);
            setCellState(oldCell);
            setCellState(cell);
            markDirty();
        });
    });

    playground.addEventListener('click', (event) => {
        const remove = event.target.closest('.routine-card-remove');
        if (remove) {
            event.preventDefault();
            const cell = remove.closest('.routine-board-dropzone');
            remove.closest('.routine-board-card')?.remove();
            setCellState(cell);
            markDirty();
            return;
        }

        const edit = event.target.closest('.routine-card-edit');
        if (edit) {
            event.preventDefault();
            const card = edit.closest('.routine-board-card');
            openCardModal({
                routineId: card.dataset.routineId,
                subjectId: card.dataset.subjectId,
                subjectName: card.dataset.subjectName,
                teacherId: card.dataset.teacherId,
                teacherName: card.dataset.teacherName,
                classroomId: card.dataset.classroomId,
                classroomName: card.dataset.classroomName,
            }, card.closest('.routine-board-dropzone'), card);
        }
    });

    const updateCardDetails = (card, data) => {
        card.dataset.teacherId = data.teacherId;
        card.dataset.teacherName = data.teacherName;
        card.dataset.classroomId = data.classroomId;
        card.dataset.classroomName = data.classroomName;

        const teacherNode = card.querySelector('.routine-board-card-teacher');
        teacherNode.innerHTML = '<i class="fas fa-user-tie mr-1"></i>';
        teacherNode.append(document.createTextNode(data.teacherName));

        const room = card.querySelector('.routine-board-card-room');
        if (data.classroomName) {
            if (room) {
                room.innerHTML = '<i class="fas fa-location-dot mr-1"></i>';
                room.append(document.createTextNode(data.classroomName));
            } else {
                const roomNode = document.createElement('span');
                roomNode.className = 'routine-board-card-room';
                roomNode.innerHTML = '<i class="fas fa-location-dot mr-1"></i>';
                roomNode.append(document.createTextNode(data.classroomName));
                card.appendChild(roomNode);
            }
        } else {
            room?.remove();
        }
    };

    document.getElementById('routine-modal-add')?.addEventListener('click', () => {
        if (!pending) return;

        const teacherId = selectedTeacherId();
        const teacherOption = [...teacherSelect.options].find((option) => String(option.value) === teacherId);
        const teacherData = window.jQuery && $(teacherSelect).hasClass('select2-hidden-accessible') ? $(teacherSelect).select2('data')[0] : null;
        const roomOption = classroomSelect.options[classroomSelect.selectedIndex];
        const days = selectedDays();

        if (!days.length) {
            notify('Please select at least one day.', 'warning');
            return;
        }

        if (!teacherId) {
            notify('Please choose a teacher.', 'warning');
            return;
        }

        const data = {
            ...pending.data,
            teacherId,
            teacherName: teacherOption?.text || teacherData?.text || '',
            classroomId: classroomSelect.value,
            classroomName: classroomSelect.value ? (roomOption?.text || '') : '',
        };
        const periodId = pending.targetCell.dataset.periodId;
        const targetCells = days.map((day) => findCell(day, periodId)).filter(Boolean);
        const occupiedCells = targetCells.filter((cell) => {
            const card = cell.querySelector('.routine-board-card');
            return card && card !== pending.existingCard;
        });

        if (occupiedCells.length) {
            notify('One or more selected days already have a lesson in this period.', 'warning');
            return;
        }

        const originalCell = pending.existingCard?.closest('.routine-board-dropzone');
        const orderedCells = pending.existingCard && targetCells.includes(originalCell)
            ? [originalCell, ...targetCells.filter((cell) => cell !== originalCell)]
            : targetCells;

        if (pending.existingCard) {
            const card = pending.existingCard;
            const destination = orderedCells[0];
            if (destination !== originalCell) {
                destination.appendChild(card);
            }
            updateCardDetails(card, data);
            setCellState(originalCell);
            setCellState(destination);

            orderedCells.slice(1).forEach((cell) => {
                const clone = cardTemplate({ ...data, routineId: null });
                cell.appendChild(clone);
                bindCardDrag(clone);
                setCellState(cell);
            });
        } else {
            orderedCells.forEach((cell) => {
                const card = cardTemplate({ ...data, routineId: null });
                cell.appendChild(card);
                bindCardDrag(card);
                setCellState(cell);
            });
        }

        markDirty();
        pending = null;
        modal.modal('hide');
    });

    document.getElementById('routine-palette-search')?.addEventListener('input', (event) => {
        const query = event.target.value.toLowerCase().trim();
        document.querySelectorAll('.routine-subject-source').forEach((source) => {
            source.hidden = query && !source.dataset.subjectName.toLowerCase().includes(query) && !source.dataset.subjectCode.toLowerCase().includes(query);
        });
    });

    saveButton?.addEventListener('click', async () => {
        const originalHtml = saveButton.innerHTML;
        saveButton.disabled = true;
        saveButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i>Saving...';
        const items = [...document.querySelectorAll('.routine-board-dropzone .routine-board-card')].map((card) => {
            const cell = card.closest('.routine-board-dropzone');
            return {
                id: card.dataset.clientOnly === '1' ? null : (card.dataset.routineId || null),
                subject_id: Number(card.dataset.subjectId),
                teacher_id: card.dataset.teacherId || null,
                classroom_id: card.dataset.classroomId || null,
                day: cell.dataset.day,
                time_schedule_id: Number(cell.dataset.periodId),
            };
        });
        try {
            const response = await fetch(playground.dataset.saveUrl, {
                method: 'POST',
                headers: {'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf},
                body: JSON.stringify({
                    academic_session_id: Number(playground.dataset.sessionId),
                    school_class_id: Number(playground.dataset.classId),
                    section_id: Number(playground.dataset.sectionId),
                    items,
                }),
            });
            const result = await response.json().catch(() => ({}));
            if (!response.ok) throw new Error(result.message || Object.values(result.errors || {}).flat()[0] || 'The routine could not be saved.');
            notify(result.message || 'Routine saved successfully.', 'success');
            dirty = false;
            if (unsavedIndicator) unsavedIndicator.hidden = true;
            window.setTimeout(() => window.location.reload(), 450);
        } catch (error) {
            notify(error.message, 'error');
        } finally {
            saveButton.disabled = false;
            saveButton.innerHTML = originalHtml;
        }
    });

    resetButton?.addEventListener('click', () => {
        if (!dirty || window.confirm('Discard all unsaved playground changes?')) window.location.reload();
    });

    window.addEventListener('beforeunload', (event) => {
        if (!dirty) return;
        event.preventDefault();
        event.returnValue = '';
    });
});
</script>
@endif
@endsection
