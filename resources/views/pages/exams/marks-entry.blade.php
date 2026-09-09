@extends('layouts.master')

@section('contents')
    <div class="container-fluid marks-entry-page">
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
                <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
            </div>
        @endif

        <div class="card card-outline card-primary mb-3">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="mb-0 font-weight-bold text-dark">
                        <i class="fas fa-keyboard text-success mr-2"></i>Marks Entry
                    </h4>
                    <small class="text-muted">
                        {{ $exam->name }} &mdash;
                        <span class="badge badge-{{ $exam->type === 'term' ? 'danger' : 'info' }}">{{ $exam->type_label }}</span>
                        &mdash; {{ $exam->academicSession->name_en ?? ($exam->academicSession->name_bn ?? '') }}
                    </small>
                </div>
                <a href="{{ route('exams.show', $exam) }}" class="btn btn-sm btn-secondary">
                    <i class="fas fa-arrow-left mr-1"></i>Back
                </a>
            </div>

            <div class="card-body">
                <div class="form-group mb-0">
                    <label class="font-weight-bold mr-2">Select Class:</label>
                    <div class="d-flex flex-wrap">
                        @foreach ($classes as $class)
                            <a href="{{ route('exams.marks-entry', ['exam' => $exam->id, 'class_id' => $class->id]) }}"
                               class="btn btn-sm mr-1 mb-1 {{ $classId == $class->id ? 'btn-primary' : 'btn-outline-primary' }}">
                                {{ $class->name_en }}
                            </a>
                        @endforeach
                    </div>
                </div>

                @if ($classId)
                    <div class="mt-3 p-3 border rounded bg-light">
                        <div class="d-flex flex-wrap align-items-center">
                            <strong class="mr-2 mb-2">Selected Cohort:</strong>
                            <span class="badge badge-primary mr-2 mb-2">{{ $selectedClass?->name_en ?? 'Class' }}</span>
                            @if ($selectedSection)
                                <span class="badge badge-info mr-2 mb-2">{{ $selectedSection->name_en }}</span>
                            @endif
                            @if ($selectedGroup)
                                <span class="badge badge-warning mr-2 mb-2">{{ $selectedGroup->name_en }}</span>
                            @endif
                        </div>

                        @if ($sections->isNotEmpty() && ! $selectedSection)
                            <div class="mt-3">
                                <div class="font-weight-bold mb-2">Select Section:</div>
                                <div class="d-flex flex-wrap">
                                    @foreach ($sections as $section)
                                        <a href="{{ route('exams.marks-entry', array_filter([
                                            'exam' => $exam->id,
                                            'class_id' => $classId,
                                            'section_id' => $section->id,
                                        ], fn($value) => ! is_null($value))) }}"
                                           class="btn btn-sm mr-1 mb-1 {{ $sectionId == $section->id ? 'btn-info' : 'btn-outline-info' }}">
                                            {{ $section->name_en }}
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        @elseif ($groups->isNotEmpty() && ! $selectedGroup)
                            <div class="mt-3">
                                <div class="font-weight-bold mb-2">Select Group:</div>
                                <div class="d-flex flex-wrap">
                                    @foreach ($groups as $group)
                                        <a href="{{ route('exams.marks-entry', array_filter([
                                            'exam' => $exam->id,
                                            'class_id' => $classId,
                                            'section_id' => $sectionId,
                                            'group_id' => $group->id,
                                        ], fn($value) => ! is_null($value))) }}"
                                           class="btn btn-sm mr-1 mb-1 {{ $groupId == $group->id ? 'btn-warning' : 'btn-outline-warning' }}">
                                            {{ $group->name_en }}
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                @endif
            </div>
        </div>

        @if ($classId && $cohortReady)
            <div class="d-flex justify-content-end mb-3">
                <div class="btn-group" role="group" aria-label="Marks entry view">
                    <a href="{{ route('exams.marks-entry', array_filter(['exam' => $exam->id, 'class_id' => $classId, 'section_id' => $sectionId, 'group_id' => $groupId, 'subject_id' => $subjectId, 'entry_mode' => 'subject'], fn($value) => ! is_null($value))) }}"
                       class="btn btn-sm {{ $entryMode === 'subject' ? 'btn-primary' : 'btn-outline-primary' }}">
                        <i class="fas fa-book mr-1"></i>Subject-wise
                    </a>
                    <a href="{{ route('exams.marks-entry', array_filter(['exam' => $exam->id, 'class_id' => $classId, 'section_id' => $sectionId, 'group_id' => $groupId, 'entry_mode' => 'student'], fn($value) => ! is_null($value))) }}"
                       class="btn btn-sm {{ $entryMode === 'student' ? 'btn-primary' : 'btn-outline-primary' }}">
                        <i class="fas fa-user-graduate mr-1"></i>Student-wise
                    </a>
                </div>
            </div>
            @if ($entryMode === 'student')
                <form method="GET" action="{{ route('exams.marks-entry', $exam) }}" class="card card-body py-2 mb-3">
                    <input type="hidden" name="class_id" value="{{ $classId }}">
                    <input type="hidden" name="section_id" value="{{ $sectionId }}">
                    <input type="hidden" name="group_id" value="{{ $groupId }}">
                    <input type="hidden" name="entry_mode" value="student">
                    <div class="d-flex flex-wrap align-items-center">
                        <strong class="mr-3">Column control:</strong>
                                <label class="mb-0 mr-4">
                            <input type="hidden" name="show_absent" value="0">
                            <input type="checkbox" name="show_absent" value="1" {{ $showAbsent ? 'checked' : '' }} onchange="this.form.submit()">
                            Show Absent columns
                        </label>
                        <label class="mb-0 mr-4">
                            <input type="hidden" name="show_subject_total" value="0">
                            <input type="checkbox" name="show_subject_total" value="1" {{ $showSubjectTotal ? 'checked' : '' }} onchange="this.form.submit()">
                            Show Subject Total columns
                        </label>
                    </div>
                </form>
            @endif
            <div class="row">
                @if ($entryMode === 'subject')
                    <div class="col-md-2">
                        <div class="card h-100">
                            <div class="card-header py-2 bg-light">
                                <strong><i class="fas fa-book mr-1"></i>Subjects</strong>
                                <br>
                                <small class="text-muted">{{ $selectedClass?->name_en ?? '' }}</small>
                            </div>
                            <div class="list-group list-group-flush">
                                @forelse($subjects as $s)
                                    <a href="{{ route('exams.marks-entry', array_filter([
                                        'exam' => $exam->id,
                                        'class_id' => $classId,
                                        'section_id' => $sectionId,
                                        'group_id' => $groupId,
                                        'subject_id' => $s->id,
                                    ], fn($value) => ! is_null($value))) }}"
                                       class="list-group-item list-group-item-action py-2 px-3 {{ $subject && $subject->id === $s->id ? 'active' : '' }}">
                                        <small>{{ $s->name }}</small>
                                    </a>
                                @empty
                                    <div class="list-group-item text-muted small">No subjects assigned.</div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                @endif

                <div class="{{ $entryMode === 'student' ? 'col-md-12' : 'col-md-10' }}">
                    @if ($entryMode === 'student')
                        @php
                            $isTutorial = $exam->type === \App\Models\Exam::TYPE_TUTORIAL;
                            $studentWiseColumns = $subjects->filter(fn ($studentWiseSubject) => !empty($studentSubjectEligibility[$studentWiseSubject->id] ?? []))->map(function ($studentWiseSubject) use ($isTutorial, $classId) {
                                $config = $studentWiseSubject->getEffectiveMarksForClass($classId);
                                $components = $isTutorial
                                    ? [['field' => 'tutorial_marks', 'label' => 'Tutorial', 'max' => (float) ($config['tutorial_marks'] ?? $studentWiseSubject->tutorial_marks ?? 0)]]
                                    : array_values(array_filter([
                                        ['field' => 'cq_marks', 'label' => 'CQ', 'max' => (float) ($config['creative_marks'] ?? 0)],
                                        ['field' => 'mcq_marks', 'label' => 'MCQ', 'max' => (float) ($config['mcq_marks'] ?? 0)],
                                        ['field' => 'viva_marks', 'label' => 'Viva', 'max' => (float) ($config['viva_marks'] ?? 0)],
                                        ['field' => 'practical_marks', 'label' => 'Practical', 'max' => (float) ($config['practical_marks'] ?? 0)],
                                    ], fn ($component) => $component['max'] > 0));

                                return ['subject' => $studentWiseSubject, 'config' => $config, 'components' => $components];
                            });
                            $studentWiseColumnCount = $studentWiseColumns->sum(fn ($column) => count($column['components']) + ($showSubjectTotal ? 1 : 0) + ($showAbsent ? 1 : 0));
                        @endphp
                        <div class="card student-wise-card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <div>
                                    <strong>All Subjects</strong>
                                    <span class="badge badge-light ml-2">{{ $isTutorial ? 'Tutorial Exam' : 'Terminal Exam' }}</span>
                                </div>
                                <div class="d-flex align-items-center ml-auto">
                                    <small class="text-muted mr-3"><kbd>Tab</kbd> / <kbd>Enter</kbd> to move between cells</small>
                                    <button type="submit" form="student-wise-marks-form" class="btn btn-success btn-sm">
                                        <i class="fas fa-save mr-1"></i>Save All Student Marks
                                    </button>
                                </div>
                            </div>
                            <form id="student-wise-marks-form" method="POST" action="{{ route('exams.save-student-wise-marks', $exam) }}">
                                @csrf
                                <input type="hidden" name="class_id" value="{{ $classId }}">
                                <input type="hidden" name="section_id" value="{{ $sectionId }}">
                                <input type="hidden" name="group_id" value="{{ $groupId }}">
                                <div class="card-body p-0">
                                    <div class="marks-table-scrollbar-top" aria-label="Top horizontal table scrollbar"><div></div></div>
                                    <div class="table-responsive marks-table-scroll">
                                        <table class="table table-bordered table-sm mb-0 marks-entry-table student-wise-table">
                                            <thead class="thead-dark">
                                                <tr>
                                                    <th rowspan="2" class="text-center sticky-col" style="width:90px; min-width:90px;">Roll</th>
                                                    <th rowspan="2" class="sticky-col student-name-col">Student Name</th>
                                                    @foreach ($studentWiseColumns as $column)
                                                        <th colspan="{{ count($column['components']) + ($showSubjectTotal ? 1 : 0) + ($showAbsent ? 1 : 0) }}" class="text-center subject-group-header">{!! preg_replace('/\s+/', '<br>', e($column['subject']->name)) !!}</th>
                                                    @endforeach
                                                    @if ($showAbsent)
                                                        <th rowspan="2" class="text-center" style="width:75px; min-width:75px;">Absent<br>All</th>
                                                    @endif
                                                    <th rowspan="2" class="text-center" style="width:80px; min-width:80px;">Overall<br>Total</th>
                                                </tr>
                                                <tr>
                                                    @foreach ($studentWiseColumns as $column)
                                                        @foreach ($column['components'] as $component)
                                                            <th class="text-center component-header" style="min-width:85px;">{{ $component['label'] }}<br><small class="mark-full-label">{{ $component['max'] }}</small></th>
                                                        @endforeach
                                                        @if ($showSubjectTotal)
                                                            <th class="text-center" style="min-width:75px;">Subject<br>Total</th>
                                                        @endif
                                                        @if ($showAbsent)
                                                            <th class="text-center" style="min-width:60px;">Absent<br><input type="checkbox" class="subject-present-all" data-subject-id="{{ $column['subject']->id }}" checked aria-label="Mark all students present for {{ $column['subject']->name }}"></th>
                                                        @endif
                                                    @endforeach
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse ($students as $student)
                                                    @php
                                                        $info = $student->academicInformations->first();
                                                        $studentMarks = $studentWiseMarks[$student->id] ?? [];
                                                    @endphp
                                                    <tr class="student-mark-row" data-student="{{ $student->id }}">
                                                        <td class="text-center sticky-col">{{ $info?->roll ?? '—' }}</td>
                                                        <td class="sticky-col student-name-col">
                                                            <strong>{{ $student->full_name_en }}</strong>
                                                            @if ($student->full_name_bn)<br><small class="text-muted">{{ $student->full_name_bn }}</small>@endif
                                                        </td>
                                                        @foreach ($studentWiseColumns as $column)
                                                            @php
                                                                $studentWiseSubject = $column['subject'];
                                                                $mark = $studentMarks[$studentWiseSubject->id] ?? null;
                                                                $eligible = in_array($student->id, $studentSubjectEligibility[$studentWiseSubject->id] ?? [], true);
                                                                $isAbsent = (bool) ($mark?->is_absent ?? false);
                                                            @endphp
                                                            @foreach ($column['components'] as $component)
                                                                <td class="p-1 {{ $eligible ? '' : 'table-light' }} student-subject-group" data-subject-id="{{ $studentWiseSubject->id }}">
                                                                    <input type="number" name="marks[{{ $student->id }}][{{ $studentWiseSubject->id }}][{{ $component['field'] }}]" class="form-control form-control-sm text-center mark-input student-component-input" value="{{ $eligible && $mark?->{$component['field']} !== null ? number_format((float) $mark->{$component['field']}, 1) : '' }}" min="0" max="{{ $component['max'] }}" step="0.5" {{ (!$eligible || $isAbsent) ? 'disabled' : '' }}>
                                                                </td>
                                                            @endforeach
                                                            @if ($showSubjectTotal)
                                                                <td class="text-center px-1 {{ $eligible ? '' : 'table-light' }} student-subject-group" data-subject-id="{{ $studentWiseSubject->id }}">
                                                                    <strong class="subject-total-display text-success">{{ $eligible && $mark && ! $isAbsent ? number_format($mark->total, 1) : ($isAbsent ? 'AB' : '—') }}</strong>
                                                                </td>
                                                            @endif
                                                            @if ($showAbsent)
                                                                <td class="text-center px-1 {{ $eligible ? '' : 'table-light' }} student-subject-group" data-subject-id="{{ $studentWiseSubject->id }}">
                                                                    <input type="checkbox" name="marks[{{ $student->id }}][{{ $studentWiseSubject->id }}][is_present]" class="student-present-checkbox" data-subject-id="{{ $studentWiseSubject->id }}" value="1" {{ !$isAbsent ? 'checked' : '' }} {{ !$eligible ? 'disabled' : '' }}>
                                                                </td>
                                                            @else
                                                                <input type="hidden" name="marks[{{ $student->id }}][{{ $studentWiseSubject->id }}][is_absent]" value="{{ $isAbsent ? 1 : 0 }}">
                                                            @endif
                                                        @endforeach
                                                        @if ($showAbsent)
                                                            <td class="text-center">
                                                                <input type="checkbox" class="row-absent-all" aria-label="Mark all subjects absent for {{ $student->full_name_en }}">
                                                            </td>
                                                        @endif
                                                        <td class="text-center"><strong class="overall-total-display text-success">—</strong></td>
                                                    </tr>
                                                @empty
                                                    <tr><td colspan="{{ $studentWiseColumnCount + ($showAbsent ? 3 : 2) }}" class="text-center text-muted py-4">No students found for this cohort.</td></tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="card-footer student-wise-footer d-flex justify-content-between align-items-center">
                                    <span class="text-muted"><i class="fas fa-users mr-1"></i>{{ $students->count() }} students</span>
                                    <button type="submit" class="btn btn-success btn-sm ml-auto"><i class="fas fa-save mr-1"></i>Save All Student Marks</button>
                                </div>
                            </form>
                        </div>
                    @elseif ($subject)
                        @php
                            $isTutorial = $exam->type === \App\Models\Exam::TYPE_TUTORIAL;
                            $hasCq = ($subjectConfig['creative_marks'] ?? 0) > 0;
                            $hasMcq = ($subjectConfig['mcq_marks'] ?? 0) > 0;
                            $hasPractical = ($subjectConfig['practical_marks'] ?? 0) > 0;
                            $hasViva = ($subjectConfig['viva_marks'] ?? 0) > 0;
                            $fullMarks = $isTutorial ? ($subjectConfig['tutorial_marks'] ?? $subject->tutorial_marks ?? 0) : ($subjectConfig['total_marks'] ?? 100);
                            $passMark = $isTutorial ? 0 : ($subjectConfig['pass_mark'] ?? 33);
                        @endphp

                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <div>
                                    <strong>{{ $subject->name }}</strong>
                                    <span class="badge badge-light ml-2">Full: {{ $fullMarks }}</span>
                                    @if (! $isTutorial)
                                        <span class="badge badge-warning ml-1">Pass: {{ $passMark }}</span>
                                    @endif
                                </div>
                                <small class="text-muted">
                                    <kbd>Tab</kbd> / <kbd>Enter</kbd> to move between cells
                                </small>
                            </div>

                            <form method="POST" action="{{ route('exams.save-marks', $exam) }}">
                                @csrf
                                <input type="hidden" name="class_id" value="{{ $classId }}">
                                <input type="hidden" name="section_id" value="{{ $sectionId }}">
                                <input type="hidden" name="group_id" value="{{ $groupId }}">
                                <input type="hidden" name="subject_id" value="{{ $subject->id }}">

                                <div class="card-body p-0">
                                    <div class="marks-table-scrollbar-top" aria-label="Top horizontal table scrollbar"><div></div></div>
                                    <div class="table-responsive marks-table-scroll">
                                        <table class="table table-bordered table-sm mb-0 marks-entry-table">
                                            <thead class="thead-dark">
                                                <tr>
                                                    <th class="text-center" style="width:90px; min-width:90px;">Roll</th>
                                                    <th>Student Name</th>
                                                    @if ($isTutorial)
                                                        <th class="text-center" style="width:110px">Tutorial<br><small class="mark-full-label">{{ $fullMarks }}</small></th>
                                                    @else
                                                        @if ($hasCq)
                                                            <th class="text-center" style="width:85px">CQ<br><small class="mark-full-label">{{ $subjectConfig['creative_marks'] }}</small></th>
                                                        @endif
                                                        @if ($hasMcq)
                                                            <th class="text-center" style="width:85px">MCQ<br><small class="mark-full-label">{{ $subjectConfig['mcq_marks'] }}</small></th>
                                                        @endif
                                                        @if ($hasPractical)
                                                            <th class="text-center" style="width:85px">Practical<br><small class="mark-full-label">{{ $subjectConfig['practical_marks'] }}</small></th>
                                                        @endif
                                                        @if ($hasViva)
                                                            <th class="text-center" style="width:85px">Viva<br><small class="mark-full-label">{{ $subjectConfig['viva_marks'] }}</small></th>
                                                        @endif
                                                    @endif
                                                    <th class="text-center" style="width:75px">Total</th>
                                                    <th class="text-center" style="width:65px">Absent</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($students as $i => $student)
                                                    @php
                                                        $mark = $existingMarks[$student->id] ?? null;
                                                        $info = $student->academicInformations->first();
                                                        $roll = $info?->roll ?? '—';
                                                        $isAbsent = $mark?->is_absent ?? false;
                                                        $rowKey = "marks.$i";
                                                    @endphp
                                                    <tr class="mark-row {{ $isAbsent ? 'table-secondary' : '' }}" data-student="{{ $student->id }}">
                                                        <td class="text-center" style="width:90px; min-width:90px;">{{ $roll }}</td>
                                                        <td>
                                                            <strong>{{ $student->full_name_en }}</strong>
                                                            @if ($student->full_name_bn)
                                                                <br><small class="text-muted">{{ $student->full_name_bn }}</small>
                                                            @endif
                                                            <input type="hidden" name="marks[{{ $i }}][student_id]" value="{{ $student->id }}">
                                                        </td>
                                                        @if ($isTutorial)
                                                            <td class="p-1">
                                                                <input type="number" name="marks[{{ $i }}][tutorial_marks]" class="form-control form-control-sm text-center mark-input" value="{{ old($rowKey.'.tutorial_marks', $mark?->tutorial_marks) !== null && old($rowKey.'.tutorial_marks', $mark?->tutorial_marks) !== '' ? number_format((float) old($rowKey.'.tutorial_marks', $mark?->tutorial_marks), 1) : '' }}" min="0" max="{{ $fullMarks }}" step="0.5" {{ $isAbsent ? 'disabled' : '' }}>
                                                            </td>
                                                        @else
                                                            @if ($hasCq)
                                                                <td class="p-1">
                                                                    <input type="number" name="marks[{{ $i }}][cq_marks]" class="form-control form-control-sm text-center mark-input" value="{{ old($rowKey.'.cq_marks', $mark?->cq_marks) !== null && old($rowKey.'.cq_marks', $mark?->cq_marks) !== '' ? number_format((float) old($rowKey.'.cq_marks', $mark?->cq_marks), 1) : '' }}" min="0" max="{{ $subjectConfig['creative_marks'] }}" step="0.5" {{ $isAbsent ? 'disabled' : '' }}>
                                                                </td>
                                                            @endif
                                                            @if ($hasMcq)
                                                                <td class="p-1">
                                                                    <input type="number" name="marks[{{ $i }}][mcq_marks]" class="form-control form-control-sm text-center mark-input" value="{{ old($rowKey.'.mcq_marks', $mark?->mcq_marks) !== null && old($rowKey.'.mcq_marks', $mark?->mcq_marks) !== '' ? number_format((float) old($rowKey.'.mcq_marks', $mark?->mcq_marks), 1) : '' }}" min="0" max="{{ $subjectConfig['mcq_marks'] }}" step="0.5" {{ $isAbsent ? 'disabled' : '' }}>
                                                                </td>
                                                            @endif
                                                            @if ($hasPractical)
                                                                <td class="p-1">
                                                                    <input type="number" name="marks[{{ $i }}][practical_marks]" class="form-control form-control-sm text-center mark-input" value="{{ old($rowKey.'.practical_marks', $mark?->practical_marks) !== null && old($rowKey.'.practical_marks', $mark?->practical_marks) !== '' ? number_format((float) old($rowKey.'.practical_marks', $mark?->practical_marks), 1) : '' }}" min="0" max="{{ $subjectConfig['practical_marks'] }}" step="0.5" {{ $isAbsent ? 'disabled' : '' }}>
                                                                </td>
                                                            @endif
                                                            @if ($hasViva)
                                                                <td class="p-1">
                                                                    <input type="number" name="marks[{{ $i }}][viva_marks]" class="form-control form-control-sm text-center mark-input" value="{{ old($rowKey.'.viva_marks', $mark?->viva_marks) !== null && old($rowKey.'.viva_marks', $mark?->viva_marks) !== '' ? number_format((float) old($rowKey.'.viva_marks', $mark?->viva_marks), 1) : '' }}" min="0" max="{{ $subjectConfig['viva_marks'] }}" step="0.5" {{ $isAbsent ? 'disabled' : '' }}>
                                                                </td>
                                                            @endif
                                                        @endif
                                                        <td class="text-center">
                                                            <strong class="total-display {{ (! $isTutorial && $mark && ! $isAbsent && $mark->total < $passMark) ? 'text-danger' : 'text-success' }}">
                                                                {{ $mark && ! $isAbsent ? number_format($mark->total, 1) : ($isAbsent ? 'AB' : '—') }}
                                                            </strong>
                                                        </td>
                                                        <td class="text-center">
                                                            <input type="checkbox" name="marks[{{ $i }}][is_absent]" class="absent-checkbox" value="1" {{ old($rowKey.'.is_absent', $isAbsent) ? 'checked' : '' }}>
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="10" class="text-center text-muted py-4">No students found for this cohort.</td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <div class="card-footer d-flex justify-content-between align-items-center">
                                    <span class="text-muted"><i class="fas fa-users mr-1"></i>{{ $students->count() }} students</span>
                                    <div>
                                        <button type="submit" class="btn btn-success btn-lg">
                                            <i class="fas fa-save mr-2"></i>Save All Marks
                                        </button>
                                        <a href="{{ route('exams.preview', array_filter([
                                            'exam' => $exam->id,
                                            'class_id' => $classId,
                                            'section_id' => $sectionId,
                                            'group_id' => $groupId,
                                            'subject_id' => $subject->id,
                                        ], fn($value) => ! is_null($value))) }}" class="btn btn-info ml-2">
                                            <i class="fas fa-eye mr-1"></i>Preview
                                        </a>
                                    </div>
                                </div>
                            </form>
                        </div>
                    @else
                        <div class="card card-body text-center text-muted py-5">
                            <i class="fas fa-book fa-3x mb-3"></i>
                            <p>Select a subject from the left to enter marks.</p>
                        </div>
                    @endif
                </div>
            </div>
        @elseif($classId)
            <div class="card card-body text-center text-muted py-5">
                <i class="fas fa-layer-group fa-3x mb-3"></i>
                <p>
                    @if ($sections->isNotEmpty() && ! $selectedSection)
                        Select a section to continue.
                    @elseif ($groups->isNotEmpty() && ! $selectedGroup)
                        Select a group to continue.
                    @else
                        Select a subject to continue.
                    @endif
                </p>
            </div>
        @else
            <div class="card card-body text-center text-muted py-5">
                <i class="fas fa-th-large fa-3x mb-3"></i>
                <p>Select a class above to start entering marks.</p>
            </div>
        @endif
    </div>
@endsection

@section('scripts')
    <style>
        .marks-entry-page { padding: 12px 16px 24px; color: #1f2937; }
        .marks-entry-page .card { border: 1px solid #e5e7eb; border-radius: 14px; box-shadow: 0 4px 16px rgba(15, 23, 42, .05); overflow: hidden; }
        .marks-entry-page .card-header { padding: 12px 16px; background: #fff; border-bottom: 1px solid #eef0f3; }
        .marks-entry-page .card-body { padding: 14px 16px; }
        .marks-entry-page .card-footer { padding: 10px 16px; background: #fafbfc; border-top: 1px solid #eef0f3; }
        .marks-entry-page .student-wise-card { overflow: visible; }
        .marks-entry-page .student-wise-footer { position: sticky; bottom: 0; z-index: 8; background: rgba(248, 250, 252, .97); box-shadow: 0 -5px 14px rgba(15, 23, 42, .08); }
        .marks-entry-page .card-outline.card-primary { border-top: 3px solid #2563eb; }
        .marks-entry-page .card-outline.card-primary > .card-header { padding: 10px 16px; }
        .marks-entry-page .card-outline.card-primary > .card-body { padding: 12px 16px; }
        .marks-entry-page h4 { font-size: 18px; letter-spacing: -.01em; }
        .marks-entry-page .btn { border-radius: 7px; font-weight: 600; }
        .marks-entry-page .btn-sm { padding: 5px 10px; }
        .marks-entry-page .list-group-item { border-color: #eef0f3; padding: 9px 12px; }
        .marks-entry-page .list-group-item.active { background: #2563eb; border-color: #2563eb; }
        .marks-entry-page .student-wise-table,
        .marks-entry-page .marks-entry-table { border-color: #e5eaf0; }
        .marks-entry-page .student-wise-table thead th,
        .marks-entry-page .marks-entry-table thead th { padding: 8px 6px; vertical-align: middle; }
        .marks-entry-page .student-wise-table tbody td,
        .marks-entry-page .marks-entry-table tbody td { padding: 5px 6px; vertical-align: middle; }
        .marks-entry-page .student-wise-table thead { position: sticky; top: 0; z-index: 4; }
        .marks-entry-page .student-wise-table .sticky-col { z-index: 5; }
        .marks-entry-page .student-wise-table tbody .sticky-col { z-index: 3; }
        .marks-entry-page .student-wise-table .student-name-col { min-width: 210px; }
        .marks-entry-page .student-wise-table .subject-group-header { background: #172554; color: #fff; font-weight: 700; }
        .marks-entry-page .student-wise-table .component-header { background: #1e3a8a; color: #fff; }
        .marks-entry-page .student-wise-table .component-header,
        .marks-entry-page .student-wise-table .subject-group-header { border-color: #38549b; }
        .marks-entry-page .student-wise-table .form-control,
        .marks-entry-page .marks-entry-table .form-control { height: 34px; padding: 4px 6px; border-radius: 7px; }
        .marks-entry-page .student-wise-table .overall-total-display { font-weight: 800; }
        .marks-entry-page .table-responsive { scrollbar-width: thin; scrollbar-color: #cbd5e1 transparent; }
        .marks-entry-page .marks-table-scroll { cursor: grab; }
        .marks-entry-page .marks-table-scroll.is-grabbing { cursor: grabbing; user-select: none; }
        .marks-entry-page .marks-table-scrollbar-top { height: 13px; overflow-x: auto; overflow-y: hidden; scrollbar-width: thin; scrollbar-color: #cbd5e1 transparent; }
        .marks-entry-page .marks-table-scrollbar-top > div { height: 1px; }
        .marks-entry-page .marks-table-scrollbar-top::-webkit-scrollbar { height: 8px; }
        .marks-entry-page .marks-table-scrollbar-top::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 8px; }
        .marks-entry-page .table-responsive::-webkit-scrollbar { height: 8px; }
        .marks-entry-page .table-responsive::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 8px; }
        .marks-entry-page .marks-entry-table input[type="checkbox"] { accent-color: #2563eb; transform: scale(1.05); }
        .marks-entry-page .marks-entry-table .table-light { background: #f1f5f9 !important; }
        .marks-entry-page .text-muted { color: #64748b !important; }
        .marks-entry-table { font-size: 16px; }
        .marks-entry-table .form-control,
        .marks-entry-table input,
        .marks-entry-table small { font-size: 16px; }
        .mark-full-label { color: #4b5563 !important; font-size: 16px; font-weight: 400; }
        .student-wise-table { width: max-content; min-width: 100%; table-layout: auto; }
        .student-wise-table .sticky-col { position: sticky; left: 0; z-index: 2; background: #343a40; }
        .student-wise-table tbody .sticky-col { background: #fff; }
        .student-wise-table .student-name-col { left: 90px; min-width: 220px; }
        .student-wise-table .subject-group-header { white-space: normal; line-height: 1.25; }
    </style>
    <script>
        const IS_TUTORIAL = {{ ($exam->type === \App\Models\Exam::TYPE_TUTORIAL) ? 'true' : 'false' }};
        const FULL_MARKS = {{ $fullMarks ?? 100 }};
        const PASS_MARK = {{ $passMark ?? 33 }};
        const GRADES = [{
                min: 80,
                letter: 'A+',
                cls: 'success'
            }, {
                min: 70,
                letter: 'A',
                cls: 'success'
            },
            {
                min: 60,
                letter: 'A-',
                cls: 'success'
            }, {
                min: 50,
                letter: 'B',
                cls: 'primary'
            },
            {
                min: 40,
                letter: 'C',
                cls: 'info'
            }, {
                min: 33,
                letter: 'D',
                cls: 'warning'
            },
            {
                min: 0,
                letter: 'F',
                cls: 'danger'
            },
        ];

        function getGrade(total) {
            const pct = (total / FULL_MARKS) * 100;
            return GRADES.find(g => pct >= g.min) ?? GRADES[GRADES.length - 1];
        }

        function recalcRow(row) {
            let total = 0;
            row.querySelectorAll('.mark-input:not([disabled])').forEach(i => total += parseFloat(i.value) || 0);
            const totalEl = row.querySelector('.total-display');
            totalEl.textContent = total > 0 ? total.toFixed(1) : '—';
            if (IS_TUTORIAL) {
                totalEl.className = 'total-display font-weight-bold text-success';
            } else {
                totalEl.className = `total-display font-weight-bold ${total < PASS_MARK ? 'text-danger' : 'text-success'}`;
            }
        }

        function recalcStudentRow(row) {
            let overall = 0;
            const groups = [...row.querySelectorAll('.student-subject-group[data-subject-id]')]
                .reduce((map, cell) => {
                    (map[cell.dataset.subjectId] ??= []).push(cell);
                    return map;
                }, {});

            Object.values(groups).forEach(cells => {
                const total = cells.reduce((sum, cell) => {
                    const input = cell.querySelector('.mark-input');
                    return sum + (input && !input.disabled ? (parseFloat(input.value) || 0) : 0);
                }, 0);
                const totalEl = cells.find(cell => cell.querySelector('.subject-total-display'))?.querySelector('.subject-total-display');
                const absent = cells.some(cell => {
                    const presence = cell.querySelector('.student-present-checkbox');
                    return presence && !presence.checked;
                });
                if (totalEl) totalEl.textContent = absent ? 'AB' : (total > 0 ? total.toFixed(1) : '—');
                if (!absent) overall += total;
            });

            const overallEl = row.querySelector('.overall-total-display');
            if (overallEl) overallEl.textContent = overall > 0 ? overall.toFixed(1) : '—';
        }

        document.querySelectorAll('.mark-input').forEach(inp => {
            inp.addEventListener('input', () => {
                const row = inp.closest('tr');
                row.classList.contains('student-mark-row') ? recalcStudentRow(row) : recalcRow(row);
            });
            inp.addEventListener('blur', function() {
                const max = parseFloat(this.max);
                if (!isNaN(max) && parseFloat(this.value) > max) {
                    this.value = max;
                    const row = this.closest('tr');
                    row.classList.contains('student-mark-row') ? recalcStudentRow(row) : recalcRow(row);
                }
            });
            inp.addEventListener('keydown', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    const all = [...document.querySelectorAll('.mark-input:not([disabled])')];
                    const idx = all.indexOf(this);
                    if (idx < all.length - 1) all[idx + 1].focus();
                }
            });
        });

        document.querySelectorAll('.student-present-checkbox').forEach(cb => {
            cb.addEventListener('change', function() {
                const row = this.closest('tr');
                const subjectId = this.dataset.subjectId;
                row.querySelectorAll(`.student-subject-group[data-subject-id="${subjectId}"] .mark-input`).forEach(input => {
                    input.disabled = !this.checked;
                    if (!this.checked) input.value = '';
                });
                recalcStudentRow(row);

                const subjectInputs = [...document.querySelectorAll(`.student-present-checkbox[data-subject-id="${subjectId}"]:not(:disabled)`)];
                const subjectAll = document.querySelector(`.subject-present-all[data-subject-id="${subjectId}"]`);
                if (subjectAll) subjectAll.checked = subjectInputs.length > 0 && subjectInputs.every(input => input.checked);
            });
        });

        document.querySelectorAll('.subject-present-all').forEach(cb => {
            cb.addEventListener('change', function() {
                const subjectId = this.dataset.subjectId;
                document.querySelectorAll(`.student-present-checkbox[data-subject-id="${subjectId}"]:not(:disabled)`).forEach(studentCheckbox => {
                    studentCheckbox.checked = this.checked;
                    studentCheckbox.dispatchEvent(new Event('change'));
                });
            });
        });

        document.querySelectorAll('.absent-checkbox').forEach(cb => {
            cb.addEventListener('change', function() {
                const row = this.closest('tr');
                const subjectId = this.classList.contains('student-absent-checkbox') ? this.closest('td').dataset.subjectId : null;
                const inputs = subjectId
                    ? [...row.querySelectorAll(`.student-subject-group[data-subject-id="${subjectId}"] .mark-input`)]
                    : [...row.querySelectorAll('.mark-input')];
                inputs.forEach(i => {
                    i.disabled = this.checked;
                    if (this.checked) i.value = '';
                });
                if (row.classList.contains('student-mark-row')) recalcStudentRow(row);
                else {
                    row.classList.toggle('table-secondary', this.checked);
                    const totalEl = row.querySelector('.total-display');
                    if (totalEl) {
                        totalEl.textContent = this.checked ? 'AB' : '—';
                        totalEl.className = this.checked ? 'total-display text-muted' : 'total-display font-weight-bold text-success';
                    }
                    if (!this.checked) recalcRow(row);
                }
                if (row.classList.contains('student-mark-row') && row.dataset.bulkAbsent !== '1') {
                    const subjectCheckboxes = [...row.querySelectorAll('.student-absent-checkbox:not(:disabled)')];
                    const rowAbsent = row.querySelector('.row-absent-all');
                    if (rowAbsent) rowAbsent.checked = subjectCheckboxes.length > 0 && subjectCheckboxes.every(input => input.checked);
                }
            });
        });

        document.querySelectorAll('.row-absent-all').forEach(cb => {
            cb.addEventListener('change', function() {
                const row = this.closest('tr');
                row.dataset.bulkAbsent = '1';
                row.querySelectorAll('.student-present-checkbox:not(:disabled)').forEach(subjectCheckbox => {
                    subjectCheckbox.checked = !this.checked;
                    subjectCheckbox.dispatchEvent(new Event('change'));
                });
                delete row.dataset.bulkAbsent;
                recalcStudentRow(row);
            });
        });

        document.querySelectorAll('.student-mark-row').forEach(row => {
            recalcStudentRow(row);
            const subjectCheckboxes = [...row.querySelectorAll('.student-present-checkbox:not(:disabled)')];
            const rowAbsent = row.querySelector('.row-absent-all');
            if (rowAbsent) rowAbsent.checked = subjectCheckboxes.length > 0 && subjectCheckboxes.every(input => !input.checked);
        });

        document.querySelectorAll('.subject-present-all').forEach(subjectAll => {
            const subjectInputs = [...document.querySelectorAll(`.student-present-checkbox[data-subject-id="${subjectAll.dataset.subjectId}"]:not(:disabled)`)];
            subjectAll.checked = subjectInputs.length > 0 && subjectInputs.every(input => input.checked);
        });

        document.querySelectorAll('.marks-table-scroll').forEach(scroller => {
            const topScrollbar = scroller.previousElementSibling?.classList.contains('marks-table-scrollbar-top')
                ? scroller.previousElementSibling
                : null;
            const topScrollbarContent = topScrollbar?.firstElementChild;
            let syncingScroll = false;

            const syncTopScrollbar = () => {
                if (topScrollbarContent) topScrollbarContent.style.width = `${scroller.scrollWidth}px`;
                if (topScrollbar) topScrollbar.scrollLeft = scroller.scrollLeft;
            };

            syncTopScrollbar();
            window.addEventListener('resize', syncTopScrollbar);
            if (topScrollbar) {
                topScrollbar.addEventListener('scroll', () => {
                    if (syncingScroll) return;
                    syncingScroll = true;
                    scroller.scrollLeft = topScrollbar.scrollLeft;
                    syncingScroll = false;
                });
            }
            scroller.addEventListener('scroll', () => {
                if (syncingScroll || !topScrollbar) return;
                syncingScroll = true;
                topScrollbar.scrollLeft = scroller.scrollLeft;
                syncingScroll = false;
            });

            let dragging = false;
            let startX = 0;
            let startScrollLeft = 0;

            scroller.addEventListener('pointerdown', event => {
                if (event.button !== 0 || event.target.closest('input, button, select, textarea, a, label')) return;
                dragging = true;
                startX = event.clientX;
                startScrollLeft = scroller.scrollLeft;
                scroller.classList.add('is-grabbing');
                scroller.setPointerCapture(event.pointerId);
            });

            scroller.addEventListener('pointermove', event => {
                if (!dragging) return;
                scroller.scrollLeft = startScrollLeft - (event.clientX - startX);
                event.preventDefault();
            });

            const stopDragging = event => {
                if (!dragging) return;
                dragging = false;
                scroller.classList.remove('is-grabbing');
                if (event.pointerId !== undefined && scroller.hasPointerCapture(event.pointerId)) {
                    scroller.releasePointerCapture(event.pointerId);
                }
            };

            scroller.addEventListener('pointerup', stopDragging);
            scroller.addEventListener('pointercancel', stopDragging);
            scroller.addEventListener('pointerleave', event => {
                if (!scroller.hasPointerCapture(event.pointerId)) stopDragging(event);
            });
        });
    </script>
@endsection
