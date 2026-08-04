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
                    @if ($subject)
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
                const g = getGrade(total);
                const gradeEl = row.querySelector('.grade-badge');
                totalEl.className = `total-display font-weight-bold ${total < PASS_MARK ? 'text-danger' : 'text-success'}`;
                gradeEl.textContent = total > 0 ? g.letter : '—';
                gradeEl.className = `grade-badge badge badge-${total > 0 ? g.cls : 'secondary'}`;
            }
        }

        document.querySelectorAll('.mark-input').forEach(inp => {
            inp.addEventListener('input', () => recalcRow(inp.closest('tr')));
            inp.addEventListener('blur', function() {
                const max = parseFloat(this.max);
                if (!isNaN(max) && parseFloat(this.value) > max) {
                    this.value = max;
                    recalcRow(this.closest('tr'));
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
                row.querySelectorAll('.mark-input').forEach(i => {
                    i.disabled = this.checked;
                    if (this.checked) i.value = '';
                });
                row.classList.toggle('table-secondary', this.checked);
                const totalEl = row.querySelector('.total-display');
                if (this.checked) {
                    totalEl.textContent = 'AB';
                    totalEl.className = 'total-display text-muted';
                    if (!IS_TUTORIAL) {
                        const gradeEl = row.querySelector('.grade-badge');
                        gradeEl.textContent = 'AB';
                        gradeEl.className = 'grade-badge badge badge-secondary';
                    }
                } else {
                    recalcRow(row);
                }
            });
        });
    </script>
@endsection
