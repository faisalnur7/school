@extends('layouts.master')

@section('contents')
    <div class="container-fluid">


        {{-- Class Selector --}}
        <div class="card card-outline card-primary mb-3">
            <div class="card-header d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h4 class="mb-0 font-weight-bold text-white"><i class="fas fa-chart-bar text-info mr-2"></i>Marks Preview
                    </h4>
                    <small class="text-muted">{{ $exam->name }}</small>
                </div>
                <div>
                    @if ($subject && $classId)
                        <a href="{{ route('exams.preview-pdf', ['exam' => $exam->id, 'class_id' => $classId, 'subject_id' => $subject->id, 'filter' => $filter]) }}"
                            class="btn btn-sm btn-danger mr-2">
                            <i class="fas fa-file-pdf mr-1"></i>PDF
                        </a>
                    @endif
                    <a href="{{ route('exams.show', $exam) }}" class="btn btn-sm btn-secondary">
                        <i class="fas fa-arrow-left mr-1"></i>Back
                    </a>
                </div>
            </div>
            <div class="card-body py-2">
                <label class="mr-2 font-weight-bold">Select Class:</label>
                @foreach ($classes as $class)
                    <a href="{{ route('exams.preview', ['exam' => $exam->id, 'class_id' => $class->id]) }}"
                        class="btn btn-sm mr-1 mb-1 {{ $classId == $class->id ? 'btn-primary' : 'btn-outline-primary' }}">
                        {{ $class->name_en }}
                    </a>
                @endforeach
            </div>
        </div>

        @if ($classId)
            <div class="row">
                {{-- Subject Sidebar --}}
                <div class="col-md-2">
                    <div class="card">
                        <div class="card-header py-2 bg-light">
                            <strong>Subjects</strong>
                            <br><small class="text-muted">{{ $selectedClass->name_en }}</small>
                        </div>
                        <div class="list-group list-group-flush">
                            @forelse($subjects as $s)
                                <a href="{{ route('exams.preview', ['exam' => $exam->id, 'class_id' => $classId, 'subject_id' => $s->id, 'filter' => $filter]) }}"
                                    class="list-group-item list-group-item-action py-2 px-3 {{ $subject && $subject->id === $s->id ? 'active' : '' }}">
                                    <small>{{ $s->name }}</small>
                                </a>
                            @empty
                                <div class="list-group-item text-muted small">No subjects.</div>
                            @endforelse
                        </div>
                    </div>
                </div>

                <div class="col-md-10">
                    @if ($subject)
                        {{-- Filter Tabs --}}
                        <div class="card mb-3">
                            <div class="card-body py-2">
                                <div class="btn-group btn-group-sm">
                                    @foreach (['all' => ['All', 'primary'], 'highest' => ['Highest First', 'success'], 'passed' => ['Passed', 'info'], 'failed' => ['Failed', 'danger']] as $key => [$label, $color])
                                        <a href="{{ route('exams.preview', ['exam' => $exam->id, 'class_id' => $classId, 'subject_id' => $subject->id, 'filter' => $key]) }}"
                                            class="btn {{ $filter === $key ? "btn-$color" : "btn-outline-$color" }}">
                                            {{ $label }}
                                        </a>
                                    @endforeach
                                </div>
                                <span class="ml-3 text-muted">
                                    Showing <strong>{{ $marks->count() }}</strong> &mdash; Pass Mark:
                                    <strong>{{ $passMark }}</strong>
                                </span>
                            </div>
                        </div>

                        {{-- Stats --}}
                        @php
                            $allM = \App\Models\ExamMark::where('exam_id', $exam->id)
                                ->where('subject_id', $subject->id)
                                ->whereIn(
                                    'student_id',
                                    \App\Models\Student::where('status', 1)
                                        ->whereHas(
                                            'academicInformations',
                                            fn($q) => $q
                                                ->where('school_class_id', $classId)
                                                ->when(
                                                    $exam->academic_session_id,
                                                    fn($q2) => $q2->where(
                                                        'academic_session_id',
                                                        $exam->academic_session_id,
                                                    ),
                                                ),
                                        )
                                        ->pluck('id'),
                                )
                                ->get();
                            $passedCount = $allM->filter(fn($m) => !$m->is_absent && $m->total >= $passMark)->count();
                            $failedCount = $allM->filter(fn($m) => $m->is_absent || $m->total < $passMark)->count();
                            $absentCount = $allM->where('is_absent', true)->count();
                            $avgMarks = $allM->where('is_absent', false)->avg('total');
                            $highest = $allM->where('is_absent', false)->max('total');
                        @endphp
                        <div class="row mb-3">
                            @foreach ([['Total', $allM->count(), 'primary', 'users'], ['Passed', $passedCount, 'success', 'check'], ['Failed', $failedCount, 'danger', 'times'], ['Absent', $absentCount, 'warning', 'user-slash'], ['Average', number_format($avgMarks, 1), 'info', 'chart-line'], ['Highest', number_format($highest, 1), 'teal', 'star']] as [$label, $val, $color, $icon])
                                <div class="col-md-2">
                                    <div class="info-box bg-{{ $color }}">
                                        <span class="info-box-icon"><i class="fas fa-{{ $icon }}"></i></span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">{{ $label }}</span>
                                            <span class="info-box-number">{{ $val }}</span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        {{-- Table --}}
                        <div class="card">
                            <div class="card-header">
                                <strong>{{ $subject->name }}</strong>
                                <span class="badge badge-light ml-2">Full:
                                    {{ $subject->getEffectiveMarksForClass($classId)['total_marks'] }}</span>
                            </div>
                            <div class="card-body p-0">
                                <table class="table table-sm table-hover mb-0">
                                    <thead class="thead-dark">
                                        <tr>
                                            <th>#</th>
                                            <th>Student</th>
                                            <th class="text-center">Obtained</th>
                                            <th class="text-center">Grade</th>
                                            <th class="text-center">GPA</th>
                                            <th class="text-center">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($marks as $i => $mark)
                                            <tr
                                                class="{{ $mark->is_absent ? 'table-secondary' : ($mark->total < $passMark ? 'table-danger' : '') }}">
                                                <td>{{ $i + 1 }}</td>
                                                <td>
                                                    <strong>{{ $mark->student->full_name_en }}</strong>
                                                    @if ($mark->student->full_name_bn)
                                                        <br><small
                                                            class="text-muted">{{ $mark->student->full_name_bn }}</small>
                                                    @endif
                                                </td>
                                                <td class="text-center">
                                                    {{ $mark->is_absent ? 'Absent' : number_format($mark->total, 1) }}
                                                </td>
                                                <td class="text-center">
                                                    <span
                                                        class="badge badge-{{ $mark->is_absent ? 'secondary' : ($mark->letter_grade === 'F' ? 'danger' : 'success') }} badge-pill">
                                                        {{ $mark->is_absent ? 'AB' : $mark->letter_grade }}
                                                    </span>
                                                </td>
                                                <td class="text-center">{{ $mark->is_absent ? '—' : $mark->gpa }}</td>
                                                <td class="text-center">
                                                    @if ($mark->is_absent)
                                                        <span class="badge badge-secondary">Absent</span>
                                                    @elseif($mark->total >= $passMark)
                                                        <span class="badge badge-success">Passed</span>
                                                    @else
                                                        <span class="badge badge-danger">Failed</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="text-center text-muted py-4">No marks entered yet.
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @else
                        <div class="card card-body text-center text-muted py-5">
                            <i class="fas fa-book fa-3x mb-3"></i>
                            <p>Select a subject from the left.</p>
                        </div>
                    @endif
                </div>
            </div>
        @else
            <div class="card card-body text-center text-muted py-5">
                <i class="fas fa-th-large fa-3x mb-3"></i>
                <p>Select a class above to preview marks.</p>
            </div>
        @endif
    </div>
@endsection
