@extends('layouts.master')

@section('contents')
    <div class="container-fluid">


        {{-- Class Selector --}}
        <div class="card card-outline card-warning mb-3">
            <div class="card-header d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h4 class="mb-0 font-weight-bold text-white"><i class="fas fa-trophy text-warning mr-2"></i>Terminal
                        Result</h4>
                    <small class="text-muted">
                        {{ $exam->name }} &mdash;
                        {{ $exam->academicSession->name_en ?? ($exam->academicSession->name_bn ?? '') }}
                    </small>
                </div>
                <div>
                    @if ($classId)
                        <a href="{{ route('exams.terminal-result-pdf', array_filter([
                            'exam' => $exam->id,
                            'class_id' => $classId,
                            'section_id' => $sectionId,
                            'group_id' => $groupId,
                            'filter' => $filter,
                        ], fn($value) => ! is_null($value))) }}"
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
                    <a href="{{ route('exams.terminal-result', ['exam' => $exam->id, 'class_id' => $class->id]) }}"
                        class="btn btn-sm mr-1 mb-1 {{ $classId == $class->id ? 'btn-warning' : 'btn-outline-warning' }}">
                        {{ $class->name_en }}
                    </a>
                @endforeach
            </div>
        </div>

        @if ($classId && $selectedClass)
            {{-- Stats --}}
            @php
                $totalStudents = count($results);
                $passedCount = count(array_filter($results, fn($r) => !$r['has_failed']));
                $failedCount = $totalStudents - $passedCount;
                $avgGpa = $totalStudents > 0 ? round(array_sum(array_column($results, 'gpa')) / $totalStudents, 2) : 0;
            @endphp
            <div class="row mb-3">
                <div class="col-md-3">
                <div class="info-box bg-primary">
                    <span class="info-box-icon"><i class="fas fa-users"></i></span>
                    <div class="info-box-content">
                            <span class="info-box-text">Total — {{ $selectedClass?->name_en ?? '' }}</span>
                            <span class="info-box-number">{{ $totalStudents }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="info-box bg-success">
                        <span class="info-box-icon"><i class="fas fa-check-circle"></i></span>
                        <div class="info-box-content"><span class="info-box-text">Passed</span><span
                                class="info-box-number">{{ $passedCount }}</span></div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="info-box bg-danger">
                        <span class="info-box-icon"><i class="fas fa-times-circle"></i></span>
                        <div class="info-box-content"><span class="info-box-text">Failed</span><span
                                class="info-box-number">{{ $failedCount }}</span></div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="info-box bg-info">
                        <span class="info-box-icon"><i class="fas fa-star"></i></span>
                        <div class="info-box-content"><span class="info-box-text">Class Avg GPA</span><span
                                class="info-box-number">{{ $avgGpa }}</span></div>
                    </div>
                </div>
            </div>

            {{-- Filter + Grading Scale --}}
            <div class="card mb-3">
                <div class="card-body py-2 d-flex align-items-center flex-wrap">
                    <div class="btn-group btn-group-sm mr-4">
                        @foreach (['all' => "All ($totalStudents)", 'passed' => "Passed ($passedCount)", 'failed' => "Failed ($failedCount)"] as $key => $label)
                            <a href="{{ route('exams.terminal-result', array_filter([
                                'exam' => $exam->id,
                                'class_id' => $classId,
                                'section_id' => $sectionId,
                                'group_id' => $groupId,
                                'filter' => $key,
                            ], fn($value) => ! is_null($value))) }}"
                                class="btn {{ $filter === $key ? 'btn-primary' : 'btn-outline-primary' }}">
                                {{ $label }}
                            </a>
                        @endforeach
                    </div>
                    <div class="d-flex flex-wrap">
                        @foreach (\App\Services\GradingService::allGrades() as $g)
                            <span
                                class="badge badge-{{ $g['letter'] === 'F' ? 'danger' : ($g['gpa'] >= 4 ? 'success' : ($g['gpa'] >= 3 ? 'primary' : ($g['gpa'] >= 2 ? 'info' : 'warning'))) }} mr-1 p-1"
                                style="font-size:10px">
                                {{ $g['letter'] }}: {{ $g['min'] }}–{{ $g['max'] }}%
                            </span>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Result Table --}}
            <div class="card">
                <div class="card-header">
                    <strong>Result Sheet — {{ $selectedClass?->name_en ?? '' }}</strong>
                    <span class="badge badge-light ml-2">{{ count($displayResults) }} students</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm mb-0">
                            <thead class="thead-dark">
                                <tr>
                                    <th class="text-center" rowspan="2">Rank</th>
                                    <th rowspan="2">Student</th>
                                    <th class="text-center" rowspan="2">Section</th>
                                    @foreach ($subjects as $subject)
                                        <th class="text-center" style="min-width:65px">
                                            <small>{{ Str::limit($subject->name, 10) }}</small>
                                        </th>
                                    @endforeach
                                    <th class="text-center" rowspan="2">Total</th>
                                    <th class="text-center" rowspan="2">%</th>
                                    <th class="text-center" rowspan="2">GPA</th>
                                    <th class="text-center" rowspan="2">Grade</th>
                                    <th class="text-center" rowspan="2">Status</th>
                                </tr>
                                <tr>
                                    @foreach ($subjects as $subject)
                                        @php $cfg = $subject->getEffectiveMarksForClass($classId); @endphp
                                        <th class="text-center text-warning" style="font-size:10px">
                                            /{{ $cfg['total_marks'] }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($displayResults as $row)
                                    @php $info = $row['student']->academicInformations->first(); @endphp
                                    <tr class="{{ $row['has_failed'] ? 'table-danger' : '' }}">
                                        <td class="text-center font-weight-bold">
                                            @if ($row['rank'] <= 3)
                                                <span
                                                    class="badge badge-{{ $row['rank'] == 1 ? 'warning' : ($row['rank'] == 2 ? 'secondary' : 'info') }}">
                                                    {{ $row['rank'] }}{{ ['', 'st', 'nd', 'rd'][$row['rank']] ?? 'th' }}
                                                </span>
                                            @else
                                                {{ $row['rank'] }}
                                            @endif
                                        </td>
                                        <td>
                                            <strong>{{ $row['student']->full_name_en }}</strong>
                                            @if ($row['student']->full_name_bn)
                                                <br><small class="text-muted">{{ $row['student']->full_name_bn }}</small>
                                            @endif
                                        </td>
                                        <td class="text-center"><small>{{ $info?->section?->name_en ?? '—' }}</small></td>
                                        @foreach ($subjects as $subject)
                                            @php $sr = $row['subject_results'][$subject->id] ?? null; @endphp
                                            <td class="text-center {{ $sr && !($sr['passed'] ?? true) ? 'bg-danger text-white' : '' }}"
                                                style="font-size:12px">
                                                @if ($sr)
                                                    {{ $sr['is_absent'] ? 'AB' : number_format($sr['obtained'], 0) }}
                                                    <br><small>{{ $sr['is_absent'] ? '' : $sr['letter_grade'] }}</small>
                                                @else
                                                    <span class="text-muted">0</span>
                                                @endif
                                            </td>
                                        @endforeach
                                        <td class="text-center font-weight-bold">
                                            {{ number_format($row['total_obtained'], 0) }}</td>
                                        <td class="text-center">{{ $row['percentage'] }}%</td>
                                        <td class="text-center font-weight-bold">{{ $row['gpa'] }}</td>
                                        <td class="text-center">
                                            <span
                                                class="badge badge-{{ $row['gpa_label'] === 'F' ? 'danger' : ($row['gpa'] >= 4 ? 'success' : 'primary') }} badge-pill">
                                                {{ $row['gpa_label'] }}
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge badge-{{ $row['has_failed'] ? 'danger' : 'success' }}">
                                                {{ $row['status'] }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="{{ 7 + count($subjects) }}" class="text-center text-muted py-4">No
                                            results found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @else
            <div class="card card-body text-center text-muted py-5">
                <i class="fas fa-th-large fa-3x mb-3"></i>
                <p>Select a class above to view the terminal result.</p>
            </div>
        @endif
    </div>
@endsection
