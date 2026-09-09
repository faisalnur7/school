@extends('layouts.master')

@section('contents')
    <div class="container-fluid">
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
            <div class="row">
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

                <div class="col-md-10">
                    @if ($entryMode === 'student')
                        @php
                            $isTutorial = $exam->type === \App\Models\Exam::TYPE_TUTORIAL;
                            $studentWiseColumns = $subjects->map(function ($studentWiseSubject) use ($isTutorial) {
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
                            $studentWiseColumnCount = $studentWiseColumns->sum(fn ($column) => count($column['components']) + 2);
                        @endphp
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <div>
                                    <strong>All Subjects</strong>
                                    <span class="badge badge-light ml-2">{{ $isTutorial ? 'Tutorial Exam' : 'Terminal Exam' }}</span>
                                </div>
                                <small class="text-muted"><kbd>Tab</kbd> / <kbd>Enter</kbd> to move between cells</small>
                            </div>
                            <form method="POST" action="{{ route('exams.save-student-wise-marks', $exam) }}">
                                @csrf
                                <input type="hidden" name="class_id" value="{{ $classId }}">
                                <input type="hidden" name="section_id" value="{{ $sectionId }}">
                                <input type="hidden" name="group_id" value="{{ $groupId }}">
                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-sm mb-0 student-wise-table">
                                            <thead class="thead-dark">
                                                <tr>
                                                    <th rowspan="2" class="text-center sticky-col" style="width:90px; min-width:90px;">Roll</th>
                                                    <th rowspan="2" class="sticky-col student-name-col">Student Name</th>
                                                    <th rowspan="2" class="text-center" style="width:70px; min-width:70px;">Section</th>
                                                    @foreach ($studentWiseColumns as $column)
                                                        <th colspan="{{ count($column['components']) + 2 }}" class="text-center subject-group-header">{{ $column['subject']->name }}</th>
                                                    @endforeach
                                                    <th rowspan="2" class="text-center" style="width:80px; min-width:80px;">Overall<br>Total</th>
                                                </tr>
                                                <tr>
                                                    @foreach ($studentWiseColumns as $column)
                                                        @foreach ($column['components'] as $component)
                                                            <th class="text-center component-header" style="min-width:85px;">{{ $component['label'] }}<br><small class="text-warning">/{{ $component['max'] }}</small></th>
                                                        @endforeach
                                                        <th class="text-center" style="min-width:75px;">Subject<br>Total</th>
                                                        <th class="text-center" style="min-width:60px;">Absent</th>
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
                                                        <td class="text-center"><small>{{ $info?->section?->name_en ?? '—' }}</small></td>
                                                        @foreach ($studentWiseColumns as $column)
                                                            @php
                                                                $studentWiseSubject = $column['subject'];
                                                                $mark = $studentMarks[$studentWiseSubject->id] ?? null;
                                                                $eligible = in_array($student->id, $studentSubjectEligibility[$studentWiseSubject->id] ?? [], true);
                                                                $isAbsent = (bool) ($mark?->is_absent ?? false);
                                                            @endphp
                                                            @foreach ($column['components'] as $component)
                                                                <td class="p-1 {{ $eligible ? '' : 'table-light' }} student-subject-group" data-subject-id="{{ $studentWiseSubject->id }}">
                                                                    <input type="number" name="marks[{{ $student->id }}][{{ $studentWiseSubject->id }}][{{ $component['field'] }}]" class="form-control form-control-sm text-center mark-input student-component-input" value="{{ $eligible ? $mark?->{$component['field']} : '' }}" min="0" max="{{ $component['max'] }}" step="0.5" {{ (!$eligible || $isAbsent) ? 'disabled' : '' }}>
                                                                </td>
                                                            @endforeach
                                                            <td class="text-center px-1 {{ $eligible ? '' : 'table-light' }} student-subject-group" data-subject-id="{{ $studentWiseSubject->id }}">
                                                                <strong class="subject-total-display text-success">{{ $eligible && $mark && ! $isAbsent ? number_format($mark->total, 1) : ($isAbsent ? 'AB' : '—') }}</strong>
                                                            </td>
                                                            <td class="text-center px-1 {{ $eligible ? '' : 'table-light' }} student-subject-group" data-subject-id="{{ $studentWiseSubject->id }}">
                                                                <input type="checkbox" name="marks[{{ $student->id }}][{{ $studentWiseSubject->id }}][is_absent]" class="absent-checkbox student-absent-checkbox" value="1" {{ $isAbsent ? 'checked' : '' }} {{ !$eligible ? 'disabled' : '' }}>
                                                            </td>
                                                        @endforeach
                                                        <td class="text-center"><strong class="overall-total-display text-success">—</strong></td>
                                                    </tr>
                                                @empty
                                                    <tr><td colspan="{{ $studentWiseColumnCount + 4 }}" class="text-center text-muted py-4">No students found for this cohort.</td></tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="card-footer d-flex justify-content-between align-items-center">
                                    <span class="text-muted"><i class="fas fa-users mr-1"></i>{{ $students->count() }} students</span>
                                    <button type="submit" class="btn btn-success btn-lg"><i class="fas fa-save mr-2"></i>Save All Student Marks</button>
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
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-sm mb-0">
                                            <thead class="thead-dark">
                                                <tr>
                                                    <th class="text-center" style="width:90px; min-width:90px;">Roll</th>
                                                    <th>Student Name</th>
                                                    <th class="text-center" style="width:70px">Section</th>
                                                    @if ($isTutorial)
                                                        <th class="text-center" style="width:110px">Tutorial<br><small class="text-warning">/{{ $fullMarks }}</small></th>
                                                    @else
                                                        @if ($hasCq)
                                                            <th class="text-center" style="width:85px">CQ<br><small class="text-warning">/{{ $subjectConfig['creative_marks'] }}</small></th>
                                                        @endif
                                                        @if ($hasMcq)
                                                            <th class="text-center" style="width:85px">MCQ<br><small class="text-warning">/{{ $subjectConfig['mcq_marks'] }}</small></th>
                                                        @endif
                                                        @if ($hasPractical)
                                                            <th class="text-center" style="width:85px">Practical<br><small class="text-warning">/{{ $subjectConfig['practical_marks'] }}</small></th>
                                                        @endif
                                                        @if ($hasViva)
                                                            <th class="text-center" style="width:85px">Viva<br><small class="text-warning">/{{ $subjectConfig['viva_marks'] }}</small></th>
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
                                                        $section = $info?->section?->name_en ?? '—';
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
                                                        <td class="text-center"><small>{{ $section }}</small></td>
                                                        @if ($isTutorial)
                                                            <td class="p-1">
                                                                <input type="number" name="marks[{{ $i }}][tutorial_marks]" class="form-control form-control-sm text-center mark-input" value="{{ old($rowKey.'.tutorial_marks', $mark?->tutorial_marks) }}" min="0" max="{{ $fullMarks }}" step="0.5" {{ $isAbsent ? 'disabled' : '' }}>
                                                            </td>
                                                        @else
                                                            @if ($hasCq)
                                                                <td class="p-1">
                                                                    <input type="number" name="marks[{{ $i }}][cq_marks]" class="form-control form-control-sm text-center mark-input" value="{{ old($rowKey.'.cq_marks', $mark?->cq_marks) }}" min="0" max="{{ $subjectConfig['creative_marks'] }}" step="0.5" {{ $isAbsent ? 'disabled' : '' }}>
                                                                </td>
                                                            @endif
                                                            @if ($hasMcq)
                                                                <td class="p-1">
                                                                    <input type="number" name="marks[{{ $i }}][mcq_marks]" class="form-control form-control-sm text-center mark-input" value="{{ old($rowKey.'.mcq_marks', $mark?->mcq_marks) }}" min="0" max="{{ $subjectConfig['mcq_marks'] }}" step="0.5" {{ $isAbsent ? 'disabled' : '' }}>
                                                                </td>
                                                            @endif
                                                            @if ($hasPractical)
                                                                <td class="p-1">
                                                                    <input type="number" name="marks[{{ $i }}][practical_marks]" class="form-control form-control-sm text-center mark-input" value="{{ old($rowKey.'.practical_marks', $mark?->practical_marks) }}" min="0" max="{{ $subjectConfig['practical_marks'] }}" step="0.5" {{ $isAbsent ? 'disabled' : '' }}>
                                                                </td>
                                                            @endif
                                                            @if ($hasViva)
                                                                <td class="p-1">
                                                                    <input type="number" name="marks[{{ $i }}][viva_marks]" class="form-control form-control-sm text-center mark-input" value="{{ old($rowKey.'.viva_marks', $mark?->viva_marks) }}" min="0" max="{{ $subjectConfig['viva_marks'] }}" step="0.5" {{ $isAbsent ? 'disabled' : '' }}>
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
                                                        <td colspan="11" class="text-center text-muted py-4">No students found for this cohort.</td>
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
        .student-wise-table { min-width: max-content; }
        .student-wise-table .sticky-col { position: sticky; left: 0; z-index: 2; background: #343a40; }
        .student-wise-table tbody .sticky-col { background: #fff; }
        .student-wise-table .student-name-col { left: 90px; min-width: 220px; }
        .student-wise-table .subject-group-header { min-width: 180px; }
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
                const absent = cells.some(cell => cell.querySelector('.student-absent-checkbox')?.checked);
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
            });
        });

        document.querySelectorAll('.student-mark-row').forEach(recalcStudentRow);
    </script>
@endsection
