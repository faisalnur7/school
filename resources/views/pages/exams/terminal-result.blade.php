@extends('layouts.master')

@section('contents')
    <style>
        .terminal-result-page { padding: 12px 16px 24px; color: #1f2937; }
        .terminal-result-page .card { border: 1px solid #e5e7eb; border-radius: 14px; box-shadow: 0 4px 16px rgba(15, 23, 42, .05); overflow: hidden; }
        .terminal-result-page .card-header { padding: 12px 16px; background: #fff; border-bottom: 1px solid #eef0f3; }
        .terminal-result-page .card-body { padding: 14px 16px; }
        .terminal-result-page .card-outline.card-primary { border-top: 3px solid #2563eb; }
        .terminal-result-page .card-outline.card-primary > .card-header { padding: 10px 16px; }
        .terminal-result-page .card-outline.card-primary > .card-body { padding: 12px 16px; }
        .terminal-result-page h4 { font-size: 18px; letter-spacing: -.01em; }
        .terminal-result-page .btn { border-radius: 7px; font-weight: 600; }
        .terminal-result-page .btn-sm { padding: 5px 10px; }
        .terminal-result-page .terminal-result-back { margin-right: 16px; }
        .terminal-result-page .terminal-result-meta { display: inline-block; margin-top: 2px; }
        .terminal-result-page .terminal-result-cohort-panel { border-color: #e5e7eb !important; }
        .terminal-result-page .info-box {
            min-height: 82px;
            margin-bottom: 14px;
            border: 1px solid rgba(148, 163, 184, .18);
            border-radius: 12px;
            box-shadow: 0 5px 14px rgba(15, 23, 42, .07);
            overflow: hidden;
        }
        .terminal-result-page .info-box-icon { width: 58px; font-size: 22px; }
        .terminal-result-page .info-box-content { padding: 10px 12px; }
        .terminal-result-page .info-box-text { font-weight: 700; opacity: .9; }
        .terminal-result-page .info-box-number { font-size: 20px; font-weight: 800; }
        .terminal-result-page .terminal-result-filter,
        .terminal-result-page .terminal-result-sheet { border-color: #dfe7f1; }
        .terminal-result-page .terminal-result-filter .card-body { background: #f8fafc; }
        .terminal-result-page .terminal-result-sheet > .card-header {
            padding: 11px 15px;
            color: #172554;
            background: linear-gradient(135deg, #eff6ff 0%, #f8fafc 100%);
            border-bottom: 1px solid #dbeafe;
        }
        .terminal-result-page .terminal-result-sheet > .card-body { background: #fff; }
        .terminal-result-page .terminal-result-table {
            border: 1px solid #dbe4ef;
            border-radius: 10px;
            border-collapse: separate;
            border-spacing: 0;
            overflow: hidden;
        }
        .terminal-result-table,
        .terminal-result-table * {
            font-size: 12px !important;
            font-weight: 700 !important;
        }
        .terminal-result-table th,
        .terminal-result-table td {
            padding: 4px 5px !important;
            line-height: 1.1 !important;
            vertical-align: middle !important;
        }
        .terminal-result-table thead th {
            color: #1e3a8a !important;
            background: #eff6ff !important;
            border-color: #dbe4ef !important;
            border-bottom: 2px solid #bfdbfe !important;
            white-space: normal;
        }
        .terminal-result-table tbody td {
            color: #1e293b;
            border-color: #e2e8f0 !important;
        }
        .terminal-result-table tbody tr:nth-child(even) td { background: #f8fafc; }
        .terminal-result-table tbody tr:hover td { background: #eff6ff !important; }
        .terminal-result-table tbody tr.table-danger:hover td { background: #fee2e2 !important; }
        .terminal-result-table thead tr:first-child th:first-child { border-top-left-radius: 9px; }
        .terminal-result-table thead tr:first-child th:last-child { border-top-right-radius: 9px; }
        .terminal-result-page .table-responsive { scrollbar-width: thin; scrollbar-color: #94a3b8 transparent; }
        .terminal-result-page .table-responsive::-webkit-scrollbar { height: 8px; }
        .terminal-result-page .table-responsive::-webkit-scrollbar-thumb { background: #94a3b8; border-radius: 8px; }
        .terminal-result-table thead th[style*="min-width:65px"] {
            min-width: 52px !important;
        }
        .terminal-result-table .terminal-mark-value {
            font-size: 15px !important;
        }
        .terminal-result-table .terminal-student-name,
        .terminal-result-table .terminal-student-name * {
            font-size: 15px !important;
        }
        .terminal-result-table .terminal-student-name {
            white-space: nowrap;
        }
        .terminal-result-table .terminal-summary-cell,
        .terminal-result-table .terminal-summary-cell * {
            font-size: 15px !important;
        }
    </style>

    <div class="container-fluid terminal-result-page">
        <div class="card card-outline card-primary mb-3 terminal-result-hero">
            <div class="card-header d-flex justify-content-start align-items-center">
                <a href="{{ route('exams.show', $exam) }}" class="btn btn-sm btn-secondary terminal-result-back">
                    <i class="fas fa-arrow-left mr-1"></i>Back
                </a>
                <div>
                    <h4 class="mb-0 font-weight-bold text-dark terminal-result-title">
                        <i class="fas fa-trophy text-warning mr-2"></i>Terminal Result
                    </h4>
                    <small class="text-muted terminal-result-meta">
                        {{ $exam->name }} &mdash;
                        <span class="badge badge-danger">{{ $exam->type_label }}</span>
                        &mdash; {{ $exam->academicSession->name_en ?? ($exam->academicSession->name_bn ?? '') }}
                    </small>
                </div>
                @if ($classId && $cohortReady)
                    <a href="{{ route('exams.terminal-result-pdf', array_filter([
                        'exam' => $exam->id,
                        'class_id' => $classId,
                        'section_id' => $sectionId,
                        'group_id' => $groupId,
                        'filter' => $filter,
                    ], fn($value) => ! is_null($value))) }}" class="btn btn-sm btn-danger ml-auto mr-2">
                        <i class="fas fa-file-pdf mr-1"></i>PDF
                    </a>
                @endif
            </div>

            <div class="card-body">
                <div class="form-group mb-0">
                    <label class="font-weight-bold mr-2">Select Class:</label>
                    <div class="d-flex flex-wrap">
                        @foreach ($classes as $class)
                            <a href="{{ route('exams.terminal-result', ['exam' => $exam->id, 'class_id' => $class->id]) }}"
                               class="btn btn-sm mr-1 mb-1 {{ $classId == $class->id ? 'btn-primary' : 'btn-outline-primary' }}">
                                {{ $class->name_en }}
                            </a>
                        @endforeach
                    </div>
                </div>

                @if ($classId && $selectedClass)
                    <div class="mt-3 p-3 border rounded bg-light terminal-result-cohort-panel">
                        <div class="d-flex flex-wrap align-items-center">
                            <strong class="mr-2 mb-2">Selected Cohort:</strong>
                            <span class="badge badge-primary mr-2 mb-2">{{ $selectedClass->name_en }}</span>
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
                                        <a href="{{ route('exams.terminal-result', array_filter([
                                            'exam' => $exam->id,
                                            'class_id' => $classId,
                                            'section_id' => $section->id,
                                        ], fn($value) => ! is_null($value))) }}"
                                           class="btn btn-sm mr-1 mb-1 btn-outline-info">
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
                                        <a href="{{ route('exams.terminal-result', array_filter([
                                            'exam' => $exam->id,
                                            'class_id' => $classId,
                                            'section_id' => $sectionId,
                                            'group_id' => $group->id,
                                        ], fn($value) => ! is_null($value))) }}"
                                           class="btn btn-sm mr-1 mb-1 btn-outline-warning">
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

        @if ($classId && $selectedClass && $cohortReady)
            {{-- Stats --}}
            @php
                $totalStudents = count($results);
                $passedCount = count(array_filter($results, fn($r) => !$r['has_failed']));
                $failedCount = $totalStudents - $passedCount;
                $avgGpa = $totalStudents > 0 ? round(array_sum(array_column($results, 'gpa')) / $totalStudents, 2) : 0;
            @endphp
            <div class="row mb-3">
                <div class="col-12 col-sm-6 col-lg">
                <div class="info-box bg-primary">
                    <span class="info-box-icon"><i class="fas fa-users"></i></span>
                    <div class="info-box-content">
                            <span class="info-box-text">Total — {{ $selectedClass?->name_en ?? '' }}</span>
                            <span class="info-box-number">{{ $totalStudents }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-lg">
                    <div class="info-box bg-success">
                        <span class="info-box-icon"><i class="fas fa-check-circle"></i></span>
                        <div class="info-box-content"><span class="info-box-text">Passed</span><span
                                class="info-box-number">{{ $passedCount }}</span></div>
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-lg">
                    <div class="info-box bg-danger">
                        <span class="info-box-icon"><i class="fas fa-times-circle"></i></span>
                        <div class="info-box-content"><span class="info-box-text">Failed</span><span
                                class="info-box-number">{{ $failedCount }}</span></div>
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-lg">
                    <div class="info-box bg-info">
                        <span class="info-box-icon"><i class="fas fa-star"></i></span>
                        <div class="info-box-content"><span class="info-box-text">Class Avg GPA</span><span
                                class="info-box-number">{{ $avgGpa }}</span></div>
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-lg">
                    <div class="info-box bg-secondary">
                        <span class="info-box-icon"><i class="fas fa-calendar-check"></i></span>
                        <div class="info-box-content"><span class="info-box-text">Total Working Days</span><span
                                class="info-box-number">{{ $totalWorkingDays }}</span></div>
                    </div>
                </div>
            </div>

            {{-- Filter + Grading Scale --}}
            <div class="card mb-3 terminal-result-filter">
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
            <div class="card terminal-result-sheet">
                <div class="card-header">
                    <strong>Result Sheet — {{ $selectedClass?->name_en ?? '' }}</strong>
                    <span class="badge badge-light ml-2">{{ count($displayResults) }} students</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm mb-0 terminal-result-table">
                            <thead class="thead-dark">
                                <tr>
                                    <th class="text-center" rowspan="2">Rank</th>
                                    <th class="text-center" rowspan="2">Student ID</th>
                                    <th rowspan="2">Student</th>
                                    @foreach ($displaySubjects as $subject)
                                        @php $cfg = $subject->getEffectiveMarksForClass($classId); @endphp
                                        <th class="text-center" style="min-width:65px">
                                            <small>{!! preg_replace('/\s+/', '<br>', e($subject->name)) !!}</small>
                                        </th>
                                    @endforeach
                                    <th class="text-center terminal-summary-cell" rowspan="2">Total</th>
                                    <th class="text-center terminal-summary-cell" rowspan="2">%</th>
                                    <th class="text-center terminal-summary-cell" rowspan="2">GPA</th>
                                    <th class="text-center terminal-summary-cell" rowspan="2">Grade</th>
                                    <th class="text-center terminal-summary-cell" rowspan="2">Status</th>
                                </tr>
                                <tr>
                                    @foreach ($displaySubjects as $subject)
                                        @php $cfg = $subject->getEffectiveMarksForClass($classId); @endphp
                                        <th class="text-center text-warning" style="font-size:10px; line-height:1.15;">
                                            {{ $cfg['total_marks'] }} <span class="text-muted">| {{ $cfg['pass_mark'] }}</span>
                                        </th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($displayResults as $row)
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
                                        <td class="text-center font-weight-bold">
                                            {{ $row['student']->student_cid ?? $row['student']->id }}
                                        </td>
                                        <td class="terminal-student-name">
                                            <strong>{{ $row['student']->full_name_en }}</strong>
                                            @if ($row['student']->full_name_bn)
                                                <br><small class="text-muted">{{ $row['student']->full_name_bn }}</small>
                                            @endif
                                        </td>
                                        @foreach ($displaySubjects as $subject)
                                            @php $sr = $row['subject_results'][$subject->id] ?? null; @endphp
                                            <td class="text-center {{ $sr && !($sr['passed'] ?? true) ? 'bg-danger text-white' : '' }}"
                                                style="font-size:12px">
                                                @if ($sr)
                                                    <span class="terminal-mark-value">{{ $sr['is_absent'] ? 'AB' : number_format($sr['obtained'], 0) }}@if (! $sr['is_absent']) ({{ $sr['letter_grade'] }})@endif</span>
                                                @else
                                                    <span class="text-muted">—</span>
                                                @endif
                                            </td>
                                        @endforeach
                                        <td class="text-center font-weight-bold terminal-summary-cell">
                                            {{ number_format($row['total_obtained'], 0) }}</td>
                                        <td class="text-center terminal-summary-cell">{{ $row['percentage'] }}%</td>
                                        <td class="text-center font-weight-bold terminal-summary-cell">{{ $row['gpa'] }}</td>
                                        <td class="text-center terminal-summary-cell">
                                            <strong>
                                                {{ $row['gpa_label'] }}
                                            </strong>
                                        </td>
                                        <td class="text-center terminal-summary-cell">
                                            <strong>
                                                {{ $row['status'] }}
                                            </strong>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="{{ 7 + count($displaySubjects) }}" class="text-center text-muted py-4">No
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
