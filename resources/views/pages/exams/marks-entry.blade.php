@extends('layouts.master')

@section('contents')
<div class="container-fluid">
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
        <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
    </div>
    @endif

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h4 class="mb-0 font-weight-bold">
                <i class="fas fa-keyboard text-success mr-2"></i>Marks Entry
            </h4>
            <small class="text-muted">
                {{ $exam->name }} &mdash;
                <span class="badge badge-{{ $exam->type === 'term' ? 'danger' : 'info' }}">{{ $exam->type_label }}</span>
                &mdash; {{ $exam->academicSession->name_en ?? $exam->academicSession->name_bn ?? '' }}
            </small>
        </div>
        <a href="{{ route('exams.show', $exam) }}" class="btn btn-sm btn-secondary">
            <i class="fas fa-arrow-left mr-1"></i>Back
        </a>
    </div>

    {{-- Class Selector --}}
    <div class="card card-outline card-primary mb-3">
        <div class="card-body py-2">
            <form method="GET" class="form-inline" id="classForm">
                <input type="hidden" name="subject_id" id="hidden_subject_id" value="{{ $subjectId }}">
                <label class="mr-2 font-weight-bold">Select Class:</label>
                @foreach($classes as $class)
                <a href="{{ route('exams.marks-entry', ['exam' => $exam->id, 'class_id' => $class->id]) }}"
                    class="btn btn-sm mr-1 mb-1 {{ $classId == $class->id ? 'btn-primary' : 'btn-outline-primary' }}">
                    {{ $class->name_en }}
                </a>
                @endforeach
            </form>
        </div>
    </div>

    @if($classId)
    <div class="row">
        {{-- Subject Sidebar --}}
        <div class="col-md-2">
            <div class="card">
                <div class="card-header py-2 bg-light">
                    <strong><i class="fas fa-book mr-1"></i>Subjects</strong>
                    <br><small class="text-muted">{{ $selectedClass->name_en }}</small>
                </div>
                <div class="list-group list-group-flush">
                    @forelse($subjects as $s)
                    <a href="{{ route('exams.marks-entry', ['exam' => $exam->id, 'class_id' => $classId, 'subject_id' => $s->id]) }}"
                        class="list-group-item list-group-item-action py-2 px-3 {{ $subject && $subject->id === $s->id ? 'active' : '' }}">
                        <small>{{ $s->name }}</small>
                    </a>
                    @empty
                    <div class="list-group-item text-muted small">No subjects assigned.</div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Marks Table --}}
        <div class="col-md-10">
            @if($subject)
            @php
                $hasCq        = ($subjectConfig['creative_marks'] ?? 0) > 0;
                $hasMcq       = ($subjectConfig['mcq_marks'] ?? 0) > 0;
                $hasPractical = ($subjectConfig['practical_marks'] ?? 0) > 0;
                $hasViva      = ($subjectConfig['viva_marks'] ?? 0) > 0;
                $fullMarks    = $subjectConfig['total_marks'] ?? 100;
                $passMark     = $subjectConfig['pass_mark'] ?? 33;
            @endphp

            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <strong>{{ $subject->name }}</strong>
                        <span class="badge badge-light ml-2">Full: {{ $fullMarks }}</span>
                        <span class="badge badge-warning ml-1">Pass: {{ $passMark }}</span>
                    </div>
                    <small class="text-muted">
                        <kbd>Tab</kbd> / <kbd>Enter</kbd> to move between cells
                    </small>
                </div>

                <form method="POST" action="{{ route('exams.save-marks', $exam) }}">
                    @csrf
                    <input type="hidden" name="class_id"   value="{{ $classId }}">
                    <input type="hidden" name="subject_id" value="{{ $subject->id }}">

                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm mb-0">
                                <thead class="thead-dark">
                                    <tr>
                                        <th class="text-center" style="width:40px">#</th>
                                        <th>Student Name</th>
                                        <th class="text-center" style="width:55px">Roll</th>
                                        <th class="text-center" style="width:70px">Section</th>
                                        @if($hasCq)
                                        <th class="text-center" style="width:85px">CQ<br><small class="text-warning">/{{ $subjectConfig['creative_marks'] }}</small></th>
                                        @endif
                                        @if($hasMcq)
                                        <th class="text-center" style="width:85px">MCQ<br><small class="text-warning">/{{ $subjectConfig['mcq_marks'] }}</small></th>
                                        @endif
                                        @if($hasPractical)
                                        <th class="text-center" style="width:85px">Practical<br><small class="text-warning">/{{ $subjectConfig['practical_marks'] }}</small></th>
                                        @endif
                                        @if($hasViva)
                                        <th class="text-center" style="width:85px">Viva<br><small class="text-warning">/{{ $subjectConfig['viva_marks'] }}</small></th>
                                        @endif
                                        <th class="text-center" style="width:75px">Total</th>
                                        <th class="text-center" style="width:65px">Grade</th>
                                        <th class="text-center" style="width:65px">Absent</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($students as $i => $student)
                                    @php
                                        $mark     = $existingMarks[$student->id] ?? null;
                                        $info     = $student->academicInformations->first();
                                        $roll     = $info?->roll ?? '—';
                                        $section  = $info?->section?->name_en ?? '—';
                                        $isAbsent = $mark?->is_absent ?? false;
                                    @endphp
                                    <tr class="mark-row {{ $isAbsent ? 'table-secondary' : '' }}" data-student="{{ $student->id }}">
                                        <td class="text-center text-muted">{{ $i + 1 }}</td>
                                        <td>
                                            <strong>{{ $student->full_name_en }}</strong>
                                            @if($student->full_name_bn)
                                            <br><small class="text-muted">{{ $student->full_name_bn }}</small>
                                            @endif
                                            <input type="hidden" name="marks[{{ $i }}][student_id]" value="{{ $student->id }}">
                                        </td>
                                        <td class="text-center">{{ $roll }}</td>
                                        <td class="text-center"><small>{{ $section }}</small></td>
                                        @if($hasCq)
                                        <td class="p-1">
                                            <input type="number" name="marks[{{ $i }}][cq_marks]"
                                                class="form-control form-control-sm text-center mark-input"
                                                value="{{ $mark?->cq_marks ?? '' }}"
                                                min="0" max="{{ $subjectConfig['creative_marks'] }}" step="0.5"
                                                {{ $isAbsent ? 'disabled' : '' }}>
                                        </td>
                                        @endif
                                        @if($hasMcq)
                                        <td class="p-1">
                                            <input type="number" name="marks[{{ $i }}][mcq_marks]"
                                                class="form-control form-control-sm text-center mark-input"
                                                value="{{ $mark?->mcq_marks ?? '' }}"
                                                min="0" max="{{ $subjectConfig['mcq_marks'] }}" step="0.5"
                                                {{ $isAbsent ? 'disabled' : '' }}>
                                        </td>
                                        @endif
                                        @if($hasPractical)
                                        <td class="p-1">
                                            <input type="number" name="marks[{{ $i }}][practical_marks]"
                                                class="form-control form-control-sm text-center mark-input"
                                                value="{{ $mark?->practical_marks ?? '' }}"
                                                min="0" max="{{ $subjectConfig['practical_marks'] }}" step="0.5"
                                                {{ $isAbsent ? 'disabled' : '' }}>
                                        </td>
                                        @endif
                                        @if($hasViva)
                                        <td class="p-1">
                                            <input type="number" name="marks[{{ $i }}][viva_marks]"
                                                class="form-control form-control-sm text-center mark-input"
                                                value="{{ $mark?->viva_marks ?? '' }}"
                                                min="0" max="{{ $subjectConfig['viva_marks'] }}" step="0.5"
                                                {{ $isAbsent ? 'disabled' : '' }}>
                                        </td>
                                        @endif
                                        <td class="text-center">
                                            <strong class="total-display {{ ($mark && !$isAbsent && $mark->total < $passMark) ? 'text-danger' : 'text-success' }}">
                                                {{ $mark && !$isAbsent ? number_format($mark->total, 1) : ($isAbsent ? 'AB' : '—') }}
                                            </strong>
                                        </td>
                                        <td class="text-center">
                                            <span class="grade-badge badge badge-{{ $mark && !$isAbsent ? ($mark->letter_grade === 'F' ? 'danger' : 'success') : 'secondary' }}">
                                                {{ $mark ? ($isAbsent ? 'AB' : $mark->letter_grade) : '—' }}
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <input type="checkbox" name="marks[{{ $i }}][is_absent]"
                                                class="absent-checkbox" value="1"
                                                {{ $isAbsent ? 'checked' : '' }}>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr><td colspan="12" class="text-center text-muted py-4">No students found for this class/session.</td></tr>
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
                            <a href="{{ route('exams.preview', ['exam' => $exam->id, 'class_id' => $classId, 'subject_id' => $subject->id]) }}"
                                class="btn btn-info ml-2">
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
const FULL_MARKS = {{ $fullMarks ?? 100 }};
const PASS_MARK  = {{ $passMark ?? 33 }};
const GRADES = [
    {min:80,letter:'A+',cls:'success'},{min:70,letter:'A',cls:'success'},
    {min:60,letter:'A-',cls:'success'},{min:50,letter:'B',cls:'primary'},
    {min:40,letter:'C',cls:'info'},{min:33,letter:'D',cls:'warning'},
    {min:0,letter:'F',cls:'danger'},
];

function getGrade(total) {
    const pct = (total / FULL_MARKS) * 100;
    return GRADES.find(g => pct >= g.min) ?? GRADES[GRADES.length - 1];
}

function recalcRow(row) {
    let total = 0;
    row.querySelectorAll('.mark-input:not([disabled])').forEach(i => total += parseFloat(i.value) || 0);
    const g = getGrade(total);
    const totalEl = row.querySelector('.total-display');
    const gradeEl = row.querySelector('.grade-badge');
    totalEl.textContent = total > 0 ? total.toFixed(1) : '—';
    totalEl.className   = `total-display font-weight-bold ${total < PASS_MARK ? 'text-danger' : 'text-success'}`;
    gradeEl.textContent = total > 0 ? g.letter : '—';
    gradeEl.className   = `grade-badge badge badge-${total > 0 ? g.cls : 'secondary'}`;
}

document.querySelectorAll('.mark-input').forEach(inp => {
    inp.addEventListener('input', () => recalcRow(inp.closest('tr')));
    inp.addEventListener('blur', function () {
        const max = parseFloat(this.max);
        if (!isNaN(max) && parseFloat(this.value) > max) { this.value = max; recalcRow(this.closest('tr')); }
    });
    inp.addEventListener('keydown', function (e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            const all = [...document.querySelectorAll('.mark-input:not([disabled])')];
            const idx = all.indexOf(this);
            if (idx < all.length - 1) all[idx + 1].focus();
        }
    });
});

document.querySelectorAll('.absent-checkbox').forEach(cb => {
    cb.addEventListener('change', function () {
        const row = this.closest('tr');
        row.querySelectorAll('.mark-input').forEach(i => { i.disabled = this.checked; if (this.checked) i.value = ''; });
        row.classList.toggle('table-secondary', this.checked);
        const totalEl = row.querySelector('.total-display');
        const gradeEl = row.querySelector('.grade-badge');
        if (this.checked) {
            totalEl.textContent = 'AB'; totalEl.className = 'total-display text-muted';
            gradeEl.textContent = 'AB'; gradeEl.className = 'grade-badge badge badge-secondary';
        } else { recalcRow(row); }
    });
});
</script>
@endsection
