@extends('layouts.master')

@section('contents')
@php
    $schoolName = $school->name ?? 'Green Chartered School & College';
    $schoolAddress = $school->address ?? 'CIP Tower, Hazari-digir-phar, Dohajari, Chandanish, Chattogram';
    $logoUrl = !empty($school->logo) ? asset($school->logo) : null;
@endphp
    <div class="col-12">

        <div class="card card-outline mb-4 no-print result-filter-panel">
            <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                <h3 class="card-title text-white mb-0"><i class="fas fa-filter mr-2 text-success"></i>Filter Options</h3>
                <small class="text-muted">{{ $exam->name }} &mdash; {{ $exam->academicSession->name_en ?? ($exam->academicSession->name_bn ?? '') }}</small>
            </div>
            <div class="card-body">
                <form id="reportFormTop" method="POST" action="{{ route('result.progress-report.show') }}">
                    @csrf
                    <div class="row">
                        <div class="col-md-6 col-lg-3 mb-3">
                            <label class="font-weight-bold">Academic Session <span class="text-danger">*</span></label>
                            <select name="session_id" class="form-control" required>
                                <option value="">— Select Session —</option>
                                @foreach($sessions as $s)
                                    <option value="{{ $s->id }}" {{ (string)($filters['session_id'] ?? '') === (string)$s->id ? 'selected' : '' }}>
                                        {{ $s->name_en ?? $s->name_bn }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 col-lg-3 mb-3">
                            <label class="font-weight-bold">Class <span class="text-danger">*</span></label>
                            <select name="class_id" id="classSelectTop" class="form-control" required>
                                <option value="">— Select Class —</option>
                                @foreach($classes as $c)
                                    <option value="{{ $c->id }}" {{ (string)($filters['class_id'] ?? '') === (string)$c->id ? 'selected' : '' }}>
                                        {{ $c->name_en }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 col-lg-3 mb-3">
                            <label class="font-weight-bold">Section <span class="text-danger">*</span></label>
                            <select name="section_id" id="sectionSelectTop" class="form-control" required>
                                <option value="">— Select Section —</option>
                                @foreach($sections as $section)
                                    <option value="{{ $section->id }}" {{ (string)($filters['section_id'] ?? '') === (string)$section->id ? 'selected' : '' }}>
                                        {{ $section->name_en ?? $section->name_bn }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 col-lg-3 mb-3">
                            <label class="font-weight-bold">Exam <span class="text-danger">*</span></label>
                            <select name="exam_id" class="form-control" required>
                                <option value="">— Select Exam —</option>
                                @foreach($exams as $e)
                                    <option value="{{ $e->id }}" {{ (string)($filters['exam_id'] ?? '') === (string)$e->id ? 'selected' : '' }}>
                                        {{ $e->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 col-lg-3 mb-3">
                            <label class="font-weight-bold">Student ID <small class="text-muted">(optional)</small></label>
                            <input type="text" name="student_id" class="form-control" value="{{ $filters['student_id'] ?? '' }}" placeholder="Leave blank for all students">
                        </div>
                    </div>
                    <div class="result-filter-actions mt-2">
                        <button type="submit" class="btn btn-success result-filter-icon-btn" title="View Report" aria-label="View Report">
                            <i class="fas fa-eye"></i>
                        </button>
                        <button type="button" id="pdfBtnTop" class="btn btn-danger result-filter-icon-btn" title="Download PDF" aria-label="Download PDF">
                            <i class="fas fa-file-pdf"></i>
                        </button>
                        <a href="{{ route('result.progress-report.index') }}" class="btn btn-secondary result-filter-icon-btn" title="Back" aria-label="Back">
                            <i class="fas fa-arrow-left"></i>
                        </a>
                    </div>
                </form>
            </div>
        </div>

        {{-- ══ Top Action Bar ══ --}}
        <div class="d-flex justify-content-between align-items-center mb-4 no-print">
            <div class="d-flex align-items-center gap-3">
                <div class="rounded-circle d-flex align-items-center justify-content-center shadow"
                    style="width:52px;height:52px;background:linear-gradient(135deg,#1a6b3c,#2d9e5f);flex-shrink:0">
                    <i class="fas fa-file-invoice text-white fa-lg"></i>
                </div>
                <div>
                    <h4 class="mb-0 font-weight-bold text-white">Terminal Exam Report</h4>
                    <small class="text-muted">{{ $exam->name }} &mdash;
                        {{ $exam->academicSession->name_en ?? ($exam->academicSession->name_bn ?? '') }}</small>
                </div>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('result.progress-report.pdf', $filters) }}" target="_blank" class="btn btn-danger btn-sm result-filter-icon-btn" title="PDF" aria-label="PDF">
                    <i class="fas fa-file-pdf"></i>
                </a>
                <button onclick="window.print()" class="btn btn-info btn-sm no-print result-filter-icon-btn" title="Print" aria-label="Print">
                    <i class="fas fa-print"></i>
                </button>
                <a href="{{ route('result.progress-report.index') }}" class="btn btn-secondary btn-sm no-print result-filter-icon-btn" title="Back" aria-label="Back">
                    <i class="fas fa-arrow-left"></i>
                </a>
            </div>
        </div>

        {{-- ══ Alert + Design Toggle Row ══ --}}
        <div class="d-flex justify-content-between align-items-center mb-3 no-print">
            <div class="alert alert-success d-flex align-items-center mb-0" style="flex:1">
                <i class="fas fa-users mr-2"></i>
                Showing <strong class="mx-1">{{ count($studentsData) }}</strong> student report(s)
            </div>

            {{-- DESIGN TOGGLE SWITCH --}}
            <div class="ds-toggle-wrap" id="designToggleWrap">
                <span class="ds-toggle-label" id="dsLabelClassic">
                    <i class="fas fa-scroll"></i> Classic
                </span>
                <label class="ds-switch" title="Switch design">
                    <input type="checkbox" id="designToggle" onchange="switchDesign(this.checked)">
                    <span class="ds-slider">
                        <span class="ds-knob"></span>
                    </span>
                </label>
                <span class="ds-toggle-label" id="dsLabelModern">
                    <i class="fas fa-layer-group"></i> Modern
                </span>
            </div>
        </div>

        {{-- ═══════════════════════════════════════════════════════
         REPORTS LOOP
    ═══════════════════════════════════════════════════════ --}}
        @foreach ($studentsData as $data)
            @php
                $student = $data['student'];
                $info = $data['academicInfo'];
                $subjectRows = $data['subjectRows'];
                $summary = $data['summary'];
                $attendancePresent = $data['attendancePresent'];
                $attendanceTotal = $data['attendanceTotal'];
            @endphp

            <div class="d-flex justify-content-end mb-2 no-print">
                {{-- <span class="badge mr-2 js-email-status {{ !empty($statusMap[$student->id]) ? 'badge-success' : 'badge-secondary' }}"
                    id="progress-email-status-{{ $student->id }}">
                    {{ !empty($statusMap[$student->id]) ? 'Email Sent' : 'Not Sent' }}
                </span> --}}
                {{-- <button type="button"
                    class="btn btn-sm btn-success js-send-result-email"
                    data-url="{{ route('result.progress-report.email') }}"
                    data-student-id="{{ $student->id }}"
                    data-session-id="{{ $filters['session_id'] }}"
                    data-class-id="{{ $filters['class_id'] }}"
                    data-section-id="{{ $filters['section_id'] }}"
                    data-exam-id="{{ $filters['exam_id'] }}"
                    data-status-id="progress-email-status-{{ $student->id }}">
                    <i class="fas fa-envelope mr-1"></i> Send to Parents
                </button> --}}
            </div>

            {{-- ╔══════════════════════════════════════════════╗
         ║  CLASSIC DESIGN (design-a)                  ║
         ╚══════════════════════════════════════════════╝ --}}
            <div class="design-a report-card-classic">
                @if(!empty($logoUrl))
                    <div class="report-card-watermark">
                        <img src="{{ $logoUrl }}" alt="" class="report-card-watermark__img">
                    </div>
                @endif

                <div class="classic-header-inner">
                    <div class="classic-header-top">
                        <div class="classic-header-brand">
                            @if(!empty($logoUrl))
                                <div class="classic-header-logo">
                                    <img src="{{ $logoUrl }}" alt="{{ $schoolName }} logo">
                                </div>
                            @endif
                            <div class="classic-header-copy">
                                <h1 class="text-3xl font-bold text-green-700 uppercase tracking-wide mb-0">
                                    {{ $schoolName }}
                                </h1>
                                <p class="text-sm text-gray-700 mt-1 mb-0">
                                    {{ $schoolAddress }}
                                </p>
                            </div>
                        </div>

                        <div class="classic-grade-table">
                            <table class="text-xs border border-gray-700">
                                <thead class="bg-gray-100">
                                    <tr>
                                        <th class="px-3 py-1 text-center">Range</th>
                                        <th class="px-1 py-1 text-center">Grade</th>
                                        <th class="px-1 py-1 text-center">Point</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($gradeScale as $grade)
                                        <tr>
                                            <td class="px-3 py-0 text-center">{{ $grade['min'] }}-{{ $grade['max'] }}</td>
                                            <td class="px-1 py-0 text-center">{{ $grade['letter'] }}</td>
                                            <td class="px-1 py-0 text-center">{{ number_format($grade['gpa'], 1) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <h2 class="text-2xl font-bold text-orange-700 italic mt-5 uppercase text-center">
                        Progress Report
                    </h2>
                </div>

                <div class="mt-6 flex justify-between items-start">
                    <div>
                        <h3 class="font-bold text-xl underline">{{ $exam->name }}</h3>
                        <div class="mt-4 space-y-1 text-sm">
                            <p><span class="font-semibold">Name</span> : {{ $student->full_name_en }}</p>
                            <p><span class="font-semibold">Class</span> : {{ $info?->schoolClass?->name_en ?? '—' }}</p>
                            <p><span class="font-semibold">ID</span> : {{ $student->student_cid ?? $student->id }}</p>
                        </div>
                    </div>
                </div>

                <div class="mt-6 overflow-x-auto">
                    <table class="w-full text-sm border border-gray-700">
                        <thead class="bg-gray-100 text-center">
                            <tr>
                                <th class="px-3 py-2 text-left">Subjects</th>
                                <th class="px-3 py-2">Full Marks</th>
                                <th class="px-3 py-2">Obtained Marks</th>
                                <th class="px-3 py-2">Highest Marks</th>
                                <th class="px-3 py-2">Total Marks</th>
                                <th class="px-3 py-2">Letter Grade</th>
                                <th class="px-3 py-2">Grade Point</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($subjectRows as $row)
                                @if (!empty($row['papers']))
                                    @foreach ($row['papers'] as $paperIndex => $paper)
                                        <tr class="{{ $paper['paper_fail'] ?? false ? 'table-danger' : '' }}">
                                            <td class="px-3 py-2 font-medium">{{ $paper['subject_name'] }}</td>
                                            <td class="text-center">{{ number_format($paper['full_marks'], 0) }}</td>
                                            <td class="text-center">
                                                {{ $paper['obtained'] ? number_format($paper['obtained'], 0) : '—' }}</td>
                                            <td class="text-center">{{ number_format($paper['highest'], 0) }}</td>
                                            @if ($paperIndex === 0)
                                                <td rowspan="{{ count($row['papers']) }}"
                                                    class="text-center align-middle font-semibold">
                                                    {{ is_null($row['obtained']) ? '—' : number_format($row['obtained'], 0) }}
                                                </td>
                                                <td rowspan="{{ count($row['papers']) }}"
                                                    class="text-center align-middle font-semibold">
                                                    {{ $row['grade'] }}
                                                </td>
                                                <td rowspan="{{ count($row['papers']) }}"
                                                    class="text-center align-middle font-semibold">
                                                    {{ number_format($row['gpa'], 1) }}
                                                </td>
                                            @endif
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td class="px-3 py-2 font-medium">{{ $row['subject_name'] }}</td>
                                        <td class="text-center">{{ number_format($row['full_marks'], 0) }}</td>
                                        <td class="text-center">
                                            {{ $row['obtained'] ? number_format($row['obtained'], 0) : '—' }}</td>
                                        <td class="text-center">{{ number_format($row['highest'], 0) }}</td>
                                        <td class="text-center">
                                            {{ $row['obtained'] ? number_format($row['obtained'], 0) : '—' }}</td>
                                        <td class="text-center">{{ $row['grade'] }}</td>
                                        <td class="text-center">{{ number_format($row['gpa'], 1) }}</td>
                                    </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-6">
                    <table class="w-full text-sm border border-gray-700">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="px-3 py-2">Summary</th>
                                <th class="px-3 py-2">Total Exam Marks</th>
                                <th class="px-3 py-2">Obtained Total Marks/Percent</th>
                                <th class="px-3 py-2">GPA</th>
                                <th class="px-3 py-2">Letter Grade</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="text-center">
                                <td class="py-2"></td>
                                <td>{{ number_format($summary['fullMarks'], 0) }}</td>
                                <td>{{ number_format($summary['obtained'], 0) }} /
                                    {{ number_format($summary['percentage'], 2) }}%</td>
                                <td>{{ number_format($summary['gpa'], 2) }}</td>
                                <td>{{ $summary['grade'] }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="mt-6 text-sm">
                    <h4 class="font-bold underline mb-2">Remarks:</h4>
                    <div class="space-y-1">
                        @if ($summary['gpa'] >= 4.0)
                            <p class="inline-block bg-green-200 px-2 rounded">Excellent</p>
                        @elseif($summary['gpa'] >= 3.0)
                            <p class="inline-block bg-green-200 px-2 rounded">Good</p>
                        @elseif($summary['gpa'] >= 2.0)
                            <p>Satisfactory</p>
                        @else
                            <p>Need to be improved</p>
                        @endif
                    </div>
                </div>

                <div class="mt-6 border border-gray-400 p-4 text-sm">
                    <ul class="list-disc pl-5 space-y-2">
                        <li>{{ $student->full_name_en }} was present {{ $attendancePresent }} days out of
                            {{ $attendanceTotal }} days.</li>
                        @if ($summary['gpa'] >= 4.0)
                            <li>Excellent results! You faithfully perform classroom tasks.</li>
                        @elseif($summary['gpa'] >= 3.0)
                            <li>Good results! Keep up the good work.</li>
                        @else
                            <li>Need to improve performance.</li>
                        @endif
                    </ul>
                </div>

                <div class="mt-10 flex justify-between items-end text-sm">
                    <div>
                        <p class="font-semibold">Published Date: {{ now()->format('d-m-Y') }}</p>
                        <div class="mt-12">
                            <div class="border-t border-black w-40"></div>
                            <p>Class Teacher</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <div class="border-t border-black w-40 ml-auto"></div>
                        <p>Principal</p>
                    </div>
                </div>

            </div>


            {{--
    ══════════════════════════════════════════════════
    PATCH: Modern Design (design-b) — Light Version
    ══════════════════════════════════════════════════
    Changes:
      1. All backgrounds white, all text black/grey
      2. Subjects with papers sorted first
    ══════════════════════════════════════════════════
--}}

            {{-- ╔══════════════════════════════════════════════╗
     ║  MODERN DESIGN (design-b)                   ║
     ╚══════════════════════════════════════════════╝ --}}
            <div class="design-b rc-wrap" style="display:none">
                @if(!empty($logoUrl))
                    <div class="rc-watermark">
                        <img src="{{ $logoUrl }}" alt="" class="rc-watermark__img">
                    </div>
                @endif

                <div class="rc-header">
                    <div class="rc-header-identity">
                        @if(!empty($logoUrl))
                            <div class="rc-school-logo">
                                <img src="{{ $logoUrl }}" alt="{{ $schoolName }} logo">
                            </div>
                        @endif
                        <div>
                            <div class="rc-school-name">{{ $schoolName }}</div>
                            <div class="rc-school-addr">
                                {{ $schoolAddress }}
                            </div>
                        </div>
                    </div>
                    <div class="rc-header-title">
                        <div class="rc-title-eyebrow">Official Academic Document</div>
                        <div class="rc-title-main">Progress Report</div>
                        <div class="rc-title-sub">{{ $exam->name }}</div>
                    </div>
                    <div class="rc-grade-scale">
                        <div class="rc-scale-label">Grade Scale</div>
                        <table class="rc-scale-table">
                            <thead>
                                <tr>
                                    <th>Range</th>
                                    <th>Grade</th>
                                    <th>GP</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($gradeScale as $grade)
                                    <tr>
                                        <td>{{ $grade['min'] }}–{{ $grade['max'] }}</td>
                                        <td class="rc-scale-letter">{{ $grade['letter'] }}</td>
                                        <td>{{ number_format($grade['gpa'], 1) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="rc-student-strip">
                    <div class="rc-student-avatar">{{ mb_strtoupper(mb_substr($student->full_name_en, 0, 1)) }}</div>
                    <div class="rc-student-fields">
                        <div class="rc-field">
                            <span class="rc-field-label">Student Name</span>
                            <span class="rc-field-value">{{ $student->full_name_en }}</span>
                        </div>
                        <div class="rc-field">
                            <span class="rc-field-label">Class</span>
                            <span class="rc-field-value">{{ $info?->schoolClass?->name_en ?? '—' }}</span>
                        </div>
                        <div class="rc-field">
                            <span class="rc-field-label">Student ID</span>
                            <span class="rc-field-value rc-mono">{{ $student->student_cid ?? $student->id }}</span>
                        </div>
                    </div>
                    <div class="rc-attendance-pill">
                        <div class="rc-att-ring">
                            <svg viewBox="0 0 44 44" class="rc-att-svg">
                                <circle cx="22" cy="22" r="18" class="rc-att-track" />
                                <circle cx="22" cy="22" r="18" class="rc-att-fill"
                                    style="stroke-dasharray: {{ $attendanceTotal > 0 ? round(($attendancePresent / $attendanceTotal) * 113, 1) : 0 }} 113" />
                            </svg>
                            <span
                                class="rc-att-pct">{{ $attendanceTotal > 0 ? round(($attendancePresent / $attendanceTotal) * 100) : 0 }}%</span>
                        </div>
                        <div class="rc-att-text">
                            <span class="rc-att-label">Attendance</span>
                            <span class="rc-att-count">{{ $attendancePresent }}/{{ $attendanceTotal }} days</span>
                        </div>
                    </div>
                </div>

                <div class="rc-table-wrap">
                    <table class="rc-table">
                        <thead>
                            <tr>
                                <th class="rc-th-left">Subjects</th>
                                <th>Full Marks</th>
                                <th>Obtained Marks</th>
                                <th>Highest Marks</th>
                                <th>Total Marks</th>
                                <th>Letter Grade</th>
                                <th>Grade Point</th>
                            </tr>
                        </thead>
                        <tbody>
                            {{-- ── Subjects WITH papers first, then simple subjects ── --}}
                            @php
                                $withPapers = array_filter($subjectRows, fn($r) => !empty($r['papers']));
                                $withoutPapers = array_filter($subjectRows, fn($r) => empty($r['papers']));
                                $sortedRows = array_merge(array_values($withPapers), array_values($withoutPapers));
                            @endphp

                            @foreach ($sortedRows as $row)
                                @if (!empty($row['papers']))
                                    @foreach ($row['papers'] as $paperIndex => $paper)
                                        <tr class="{{ $paper['paper_fail'] ?? false ? 'rc-row-fail' : '' }}">
                                            <td class="rc-td-subject">
                                                @if ($paper['paper_fail'] ?? false)
                                                    <span class="rc-fail-dot"></span>
                                                @endif
                                                {{ $paper['subject_name'] }}
                                            </td>
                                            <td class="rc-td-num">{{ number_format($paper['full_marks'], 0) }}</td>
                                            <td class="rc-td-num rc-td-obtained">
                                                {{ $paper['obtained'] ? number_format($paper['obtained'], 0) : '—' }}</td>
                                            <td class="rc-td-num">{{ number_format($paper['highest'], 0) }}</td>
                                            @if ($paperIndex === 0)
                                                <td rowspan="{{ count($row['papers']) }}" class="rc-td-num rc-td-total">
                                                    {{ is_null($row['obtained']) ? '—' : number_format($row['obtained'], 0) }}
                                                </td>
                                                <td rowspan="{{ count($row['papers']) }}" class="rc-td-grade">
                                                    <span
                                                        class="rc-grade-chip rc-grade-{{ strtolower(str_replace('+', '-plus', str_replace('-', '-minus', $row['grade']))) }}">
                                                        {{ $row['grade'] }}
                                                    </span>
                                                </td>
                                                <td rowspan="{{ count($row['papers']) }}" class="rc-td-num rc-td-gp">
                                                    {{ number_format($row['gpa'], 1) }}
                                                </td>
                                            @endif
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td class="rc-td-subject">{{ $row['subject_name'] }}</td>
                                        <td class="rc-td-num">{{ number_format($row['full_marks'], 0) }}</td>
                                        <td class="rc-td-num rc-td-obtained">
                                            {{ $row['obtained'] ? number_format($row['obtained'], 0) : '—' }}</td>
                                        <td class="rc-td-num">{{ number_format($row['highest'], 0) }}</td>
                                        <td class="rc-td-num rc-td-total">
                                            {{ $row['obtained'] ? number_format($row['obtained'], 0) : '—' }}</td>
                                        <td class="rc-td-grade">
                                            <span
                                                class="rc-grade-chip rc-grade-{{ strtolower(str_replace('+', '-plus', str_replace('-', '-minus', $row['grade']))) }}">
                                                {{ $row['grade'] }}
                                            </span>
                                        </td>
                                        <td class="rc-td-num rc-td-gp">{{ number_format($row['gpa'], 1) }}</td>
                                    </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="rc-summary-bar">
                    <div class="rc-summary-label">Summary</div>
                    <div class="rc-summary-stats">
                        <div class="rc-stat">
                            <div class="rc-stat-val">{{ number_format($summary['fullMarks'], 0) }}</div>
                            <div class="rc-stat-lbl">Total Exam Marks</div>
                        </div>
                        <div class="rc-stat-sep"></div>
                        <div class="rc-stat">
                            <div class="rc-stat-val">{{ number_format($summary['obtained'], 0) }}</div>
                            <div class="rc-stat-lbl">Marks Obtained</div>
                        </div>
                        <div class="rc-stat-sep"></div>
                        <div class="rc-stat">
                            <div class="rc-stat-val">{{ number_format($summary['percentage'], 1) }}%</div>
                            <div class="rc-stat-lbl">Percentage</div>
                        </div>
                        <div class="rc-stat-sep"></div>
                        <div class="rc-stat rc-stat--highlight">
                            <div class="rc-stat-val">{{ number_format($summary['gpa'], 2) }}</div>
                            <div class="rc-stat-lbl">GPA</div>
                        </div>
                        <div class="rc-stat-sep"></div>
                        <div class="rc-stat rc-stat--grade">
                            <div class="rc-stat-val">{{ $summary['grade'] }}</div>
                            <div class="rc-stat-lbl">Letter Grade</div>
                        </div>
                    </div>
                </div>

                <div class="rc-bottom-row">
                    <div class="rc-remarks-block">
                        <div class="rc-block-label">Remarks</div>
                        @if ($summary['gpa'] >= 4.0)
                            <div class="rc-remark-tag rc-remark-excellent"><span class="rc-remark-icon">★</span> Excellent
                            </div>
                            <p class="rc-remark-desc">Outstanding academic performance. Keep it up!</p>
                        @elseif($summary['gpa'] >= 3.0)
                            <div class="rc-remark-tag rc-remark-good"><span class="rc-remark-icon">✦</span> Good</div>
                            <p class="rc-remark-desc">Solid performance. A little more effort goes a long way.</p>
                        @elseif($summary['gpa'] >= 2.0)
                            <div class="rc-remark-tag rc-remark-satisfactory"><span class="rc-remark-icon">◆</span>
                                Satisfactory</div>
                            <p class="rc-remark-desc">Acceptable results. Consistent study will improve scores.</p>
                        @else
                            <div class="rc-remark-tag rc-remark-improve"><span class="rc-remark-icon">▲</span> Needs
                                Improvement</div>
                            <p class="rc-remark-desc">More dedication is needed. Seek teacher guidance.</p>
                        @endif
                    </div>
                    <div class="rc-comments-block">
                        <div class="rc-block-label">Comments</div>
                        <ul class="rc-comments-list">
                            <li>
                                <span class="rc-comment-bullet">◉</span>
                                <strong>{{ $student->full_name_en }}</strong> was present
                                <strong>{{ $attendancePresent }}</strong> out of <strong>{{ $attendanceTotal }}</strong>
                                school days.
                            </li>
                            @if ($summary['gpa'] >= 4.0)
                                <li><span class="rc-comment-bullet">◉</span> Excellent results! Faithfully performing all
                                    classroom tasks.</li>
                            @elseif($summary['gpa'] >= 3.0)
                                <li><span class="rc-comment-bullet">◉</span> Good results! Keep up the good work.</li>
                            @else
                                <li><span class="rc-comment-bullet">◉</span> Needs to improve overall academic performance.
                                </li>
                            @endif
                        </ul>
                    </div>
                </div>

                <div class="rc-footer">
                    <div class="rc-published">
                        <span class="rc-pub-icon">📅</span>
                        Published: <strong>{{ now()->format('d M Y') }}</strong>
                    </div>
                    <div class="rc-signatures">
                        <div class="rc-sig">
                            <div class="rc-sig-line"></div>
                            <div class="rc-sig-name">Class Teacher</div>
                        </div>
                        <div class="rc-sig">
                            <div class="rc-sig-line"></div>
                            <div class="rc-sig-name">Principal</div>
                        </div>
                    </div>
                </div>

            </div>{{-- /.design-b --}}
        @endforeach

    </div>{{-- /.col-12 --}}


    {{-- ═══════════════════════════════════════════════════════════════════
     STYLES
═══════════════════════════════════════════════════════════════════ --}}
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap');

        /* ── Google Fonts ─────────────────────────────── */
        @import url('https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=DM+Sans:wght@300;400;500;600&family=DM+Mono:wght@400;500&display=swap');

        /* ── Tokens ───────────────────────────────────── */
        :root {
            --rc-green: #1a6b3c;
            --rc-green-light: #e8f5ee;
            --rc-amber: #d97706;
            --rc-amber-light: #fef3c7;
            --rc-red: #dc2626;
            --rc-red-light: #fef2f2;
            --rc-blue: #1d4ed8;
            --rc-blue-light: #eff6ff;
            --rc-ink: #1a1a1a;
            --rc-muted: #6b7280;
            --rc-border: #e5e7eb;
            --rc-surface: #f9fafb;
            --rc-white: #ffffff;
            --rc-radius: 12px;
            --rc-shadow: 0 4px 24px rgba(26, 107, 60, .10), 0 1px 4px rgba(0, 0, 0, .06);
            --rc-ff-modern: 'Inter', 'Segoe UI', 'Helvetica Neue', Arial, sans-serif;
            --rc-ff-display: 'Playfair Display', Georgia, serif;
            --rc-ff-body: 'DM Sans', sans-serif;
            --rc-ff-mono: 'DM Mono', monospace;
        }

        /* ════ DESIGN TOGGLE ════════════════════════════ */
        .ds-toggle-wrap {
            display: flex;
            align-items: center;
            gap: 10px;
            background: #fff;
            border: 1.5px solid #e5e7eb;
            border-radius: 40px;
            padding: 7px 18px;
            margin-left: 16px;
            box-shadow: 0 2px 12px rgba(26, 107, 60, .10);
            flex-shrink: 0;
            user-select: none;
        }

        .ds-toggle-label {
            font-size: 12.5px;
            font-weight: 600;
            color: #9ca3af;
            letter-spacing: .03em;
            transition: color .25s;
            display: flex;
            align-items: center;
            gap: 5px;
            white-space: nowrap;
        }

        .ds-toggle-label.active {
            color: var(--rc-green);
        }

        /* pill switch */
        .ds-switch {
            position: relative;
            display: inline-block;
            width: 52px;
            height: 28px;
            cursor: pointer;
        }

        .ds-switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .ds-slider {
            position: absolute;
            inset: 0;
            background: #e5e7eb;
            border-radius: 28px;
            transition: background .3s;
        }

        .ds-switch input:checked~.ds-slider {
            background: var(--rc-green);
        }

        .ds-knob {
            position: absolute;
            top: 3px;
            left: 3px;
            width: 22px;
            height: 22px;
            background: #fff;
            border-radius: 50%;
            box-shadow: 0 1px 4px rgba(0, 0, 0, .18);
            transition: transform .3s cubic-bezier(.4, 0, .2, 1);
        }

        .ds-switch input:checked~.ds-slider .ds-knob {
            transform: translateX(24px);
        }

        /* ════ CLASSIC DESIGN (design-a) ════════════════ */
        .report-card-classic {
            position: relative;
            overflow: hidden;
            font-family: var(--rc-ff-modern);
            max-width: 64rem;
            margin: 0 auto 1.5rem;
            background: #fff;
            padding: 2rem;
            box-shadow: 0 4px 20px rgba(0, 0, 0, .08);
            border: 1px solid #d1d5db;
            border-top: 3px solid #1a6b3c;
            page-break-after: always;
        }

        .classic-header-inner {
            position: relative;
            border-bottom: 1px solid #e5e7eb;
            padding-bottom: 1rem;
        }

        .classic-header-top {
            position: relative;
            z-index: 1;
            display: grid;
            grid-template-columns: minmax(0, 1fr) 176px;
            gap: 16px;
            align-items: start;
        }

        .classic-header-brand {
            display: flex;
            align-items: center;
            gap: 14px;
            min-width: 0;
        }

        .classic-header-copy {
            min-width: 0;
            flex: 1;
        }

        .classic-header-logo {
            width: 64px;
            height: 64px;
            border: 1px solid #d1d5db;
            border-radius: 10px;
            background: #fff;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .classic-header-logo img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
        }

        .classic-header-copy h1 {
            font-size: 1.6rem;
            line-height: 1.05;
        }

        .classic-header-copy p {
            font-size: .8rem;
            line-height: 1.2;
        }

        .classic-grade-table {
            width: 176px;
            justify-self: end;
        }

        .classic-grade-table table {
            width: 100%;
            table-layout: fixed;
            background: #fff;
            font-size: 10px;
        }

        .classic-grade-table th,
        .classic-grade-table td {
            padding: 2px 4px;
            line-height: 1.05;
            white-space: nowrap;
            word-break: normal;
        }

        .classic-grade-table th:nth-child(1),
        .classic-grade-table td:nth-child(1) { width: 44%; }
        .classic-grade-table th:nth-child(2),
        .classic-grade-table td:nth-child(2) { width: 28%; }
        .classic-grade-table th:nth-child(3),
        .classic-grade-table td:nth-child(3) { width: 28%; }

        .report-card-watermark,
        .rc-watermark {
            position: absolute;
            inset: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            pointer-events: none;
            z-index: 0;
            opacity: 0.08;
        }

        .report-card-watermark__img,
        .rc-watermark__img {
            width: min(560px, 78%);
            max-width: 78%;
            max-height: 78%;
            object-fit: contain;
            filter: grayscale(100%);
        }

        /* ════ MODERN DESIGN (design-b) ════════════════ */
        .rc-wrap {
            position: relative;
            overflow: hidden;
            font-family: var(--rc-ff-modern);
            color: var(--rc-ink);
            background: var(--rc-white);
            border-radius: var(--rc-radius);
            box-shadow: var(--rc-shadow);
            border-top: 4px solid var(--rc-green);
            margin-bottom: 2rem;
            page-break-after: always;
        }

        .rc-header {
            display: flex;
            align-items: center;
            gap: 1.5rem;
            padding: 1.5rem 2rem;
            background: linear-gradient(135deg, #0f4023 0%, #1a6b3c 55%, #22863d 100%);
            color: #fff;
        }

        .rc-header-identity {
            display: flex;
            align-items: center;
            gap: 14px;
            flex: 1;
            min-width: 0;
        }

        .rc-school-logo {
            width: 54px;
            height: 54px;
            border-radius: 12px;
            background: rgba(255, 255, 255, .14);
            border: 1px solid rgba(255, 255, 255, .22);
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .rc-school-logo img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
        }

        .rc-school-name {
            font-family: var(--rc-ff-display);
            font-size: 15px;
            font-weight: 700;
            line-height: 1.2;
            letter-spacing: .02em;
        }

        .rc-school-addr {
            font-size: 11px;
            opacity: .75;
            margin-top: 3px;
            line-height: 1.4;
        }

        .rc-header-title {
            text-align: center;
            flex-shrink: 0;
        }

        .rc-title-eyebrow {
            font-size: 9px;
            letter-spacing: .15em;
            text-transform: uppercase;
            opacity: .7;
            margin-bottom: 4px;
        }

        .rc-title-main {
            font-family: var(--rc-ff-display);
            font-size: 26px;
            font-weight: 700;
            color: #fbbf24;
            line-height: 1;
        }

        .rc-title-sub {
            font-size: 12px;
            opacity: .85;
            margin-top: 5px;
            font-weight: 500;
        }

        .rc-grade-scale {
            flex-shrink: 0;
        }

        .rc-scale-label {
            font-size: 9px;
            letter-spacing: .12em;
            text-transform: uppercase;
            opacity: .7;
            margin-bottom: 5px;
            text-align: center;
        }

        .rc-scale-table {
            border-collapse: collapse;
            font-size: 10.5px;
            background: rgba(255, 255, 255, .1);
            border-radius: 6px;
            overflow: hidden;
        }

        .rc-scale-table th,
        .rc-scale-table td {
            padding: 3px 9px;
            border: 1px solid rgba(255, 255, 255, .15);
            text-align: center;
        }

        .rc-scale-table th {
            background: rgba(255, 255, 255, .18);
            font-weight: 600;
            font-size: 9.5px;
        }

        .rc-scale-letter {
            font-weight: 700;
            color: #fbbf24;
        }

        .rc-student-strip {
            display: flex;
            align-items: center;
            gap: 1.5rem;
            padding: 1.25rem 2rem;
            background: var(--rc-green-light);
            border-bottom: 1px solid #c6e8d5;
        }

        .rc-student-avatar {
            width: 52px;
            height: 52px;
            border-radius: 50%;
            background: var(--rc-green);
            color: #fff;
            font-family: var(--rc-ff-display);
            font-size: 22px;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            box-shadow: 0 2px 8px rgba(26, 107, 60, .3);
        }

        .rc-student-fields {
            display: flex;
            gap: 2rem;
            flex: 1;
            flex-wrap: wrap;
        }

        .rc-field {
            display: flex;
            flex-direction: column;
            gap: 2px;
        }

        .rc-field-label {
            font-size: 10px;
            letter-spacing: .1em;
            text-transform: uppercase;
            color: var(--rc-green);
            font-weight: 600;
        }

        .rc-field-value {
            font-size: 14px;
            font-weight: 500;
            color: var(--rc-ink);
        }

        .rc-mono {
            font-family: var(--rc-ff-mono);
            font-size: 13px;
        }

        .rc-attendance-pill {
            display: flex;
            align-items: center;
            gap: 10px;
            background: var(--rc-white);
            border: 1px solid #c6e8d5;
            border-radius: 40px;
            padding: 8px 16px 8px 8px;
            flex-shrink: 0;
        }

        .rc-att-ring {
            position: relative;
            width: 44px;
            height: 44px;
        }

        .rc-att-svg {
            width: 44px;
            height: 44px;
            transform: rotate(-90deg);
        }

        .rc-att-track {
            fill: none;
            stroke: #d1fae5;
            stroke-width: 4;
        }

        .rc-att-fill {
            fill: none;
            stroke: var(--rc-green);
            stroke-width: 4;
            stroke-linecap: round;
            stroke-dashoffset: 0;
        }

        .rc-att-pct {
            position: absolute;
            inset: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 9px;
            font-weight: 700;
            color: var(--rc-green);
        }

        .rc-att-text {
            display: flex;
            flex-direction: column;
            gap: 1px;
        }

        .rc-att-label {
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: .08em;
            color: var(--rc-muted);
            font-weight: 600;
        }

        .rc-att-count {
            font-size: 13px;
            font-weight: 600;
            color: var(--rc-ink);
        }

        .rc-table-wrap {
            padding: 1.5rem 2rem 0;
            overflow-x: auto;
        }

        .rc-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
        }

        .rc-table thead tr {
            background: var(--rc-ink);
            color: #fff;
        }

        .rc-table th {
            padding: 10px 14px;
            font-size: 11px;
            font-weight: 600;
            letter-spacing: .06em;
            text-transform: uppercase;
            text-align: center;
            border: none;
        }

        .rc-th-left {
            text-align: left !important;
        }

        .rc-table tbody tr {
            border-bottom: 1px solid var(--rc-border);
            transition: background .15s;
        }

        .rc-table tbody tr:nth-child(even) {
            background: var(--rc-surface);
        }

        .rc-table tbody tr:hover {
            background: #f0fdf4;
        }

        .rc-table tbody tr.rc-row-fail {
            background: var(--rc-red-light) !important;
        }

        .rc-table td {
            padding: 10px 14px;
            border: none;
            vertical-align: middle;
        }

        .rc-td-subject {
            font-weight: 500;
            padding-left: 14px;
        }

        .rc-fail-dot {
            display: inline-block;
            width: 7px;
            height: 7px;
            background: var(--rc-red);
            border-radius: 50%;
            margin-right: 6px;
            vertical-align: middle;
        }

        .rc-td-num {
            text-align: center;
            color: var(--rc-muted);
        }

        .rc-td-obtained {
            font-weight: 600;
            color: var(--rc-ink) !important;
        }

        .rc-td-total {
            font-weight: 700;
            color: var(--rc-green) !important;
        }

        .rc-td-gp {
            font-family: var(--rc-ff-mono);
            font-weight: 600;
            color: var(--rc-ink) !important;
        }

        .rc-td-grade {
            text-align: center;
        }

        .rc-grade-chip {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: .04em;
        }

        .rc-grade-a-plus,
        .rc-grade-a {
            background: #dcfce7;
            color: #166534;
        }

        .rc-grade-a-minus {
            background: #d1fae5;
            color: #065f46;
        }

        .rc-grade-b-plus,
        .rc-grade-b {
            background: var(--rc-blue-light);
            color: var(--rc-blue);
        }

        .rc-grade-b-minus {
            background: #e0e7ff;
            color: #3730a3;
        }

        .rc-grade-c-plus,
        .rc-grade-c {
            background: var(--rc-amber-light);
            color: var(--rc-amber);
        }

        .rc-grade-d {
            background: #fee2e2;
            color: #991b1b;
        }

        .rc-grade-f {
            background: #fecaca;
            color: var(--rc-red);
        }

        .rc-summary-bar {
            display: flex;
            align-items: center;
            gap: 1.5rem;
            margin: 1.25rem 2rem;
            background: var(--rc-ink);
            border-radius: 10px;
            padding: 1rem 1.5rem;
            color: #fff;
        }

        .rc-summary-label {
            font-size: 10px;
            font-weight: 700;
            letter-spacing: .12em;
            text-transform: uppercase;
            opacity: .55;
            writing-mode: vertical-rl;
            transform: rotate(180deg);
            flex-shrink: 0;
        }

        .rc-summary-stats {
            display: flex;
            align-items: center;
            flex: 1;
        }

        .rc-stat {
            flex: 1;
            text-align: center;
            padding: 0 1rem;
        }

        .rc-stat-sep {
            width: 1px;
            height: 36px;
            background: rgba(255, 255, 255, .15);
            flex-shrink: 0;
        }

        .rc-stat-val {
            font-family: var(--rc-ff-mono);
            font-size: 22px;
            font-weight: 500;
            line-height: 1;
            color: #fff;
        }

        .rc-stat-lbl {
            font-size: 10px;
            letter-spacing: .08em;
            text-transform: uppercase;
            opacity: .55;
            margin-top: 4px;
        }

        .rc-stat--highlight .rc-stat-val {
            color: #fbbf24;
        }

        .rc-stat--grade .rc-stat-val {
            font-family: var(--rc-ff-display);
            font-size: 26px;
            color: #6ee7b7;
        }

        .rc-bottom-row {
            display: flex;
            gap: 1.25rem;
            padding: 0 2rem 1.5rem;
        }

        .rc-remarks-block,
        .rc-comments-block {
            flex: 1;
            background: var(--rc-surface);
            border: 1px solid var(--rc-border);
            border-radius: 10px;
            padding: 1rem 1.25rem;
        }

        .rc-block-label {
            font-size: 10px;
            font-weight: 700;
            letter-spacing: .12em;
            text-transform: uppercase;
            color: var(--rc-muted);
            margin-bottom: 10px;
        }

        .rc-remark-tag {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 5px 14px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 700;
            margin-bottom: 8px;
        }

        .rc-remark-excellent {
            background: #dcfce7;
            color: #166534;
        }

        .rc-remark-good {
            background: var(--rc-blue-light);
            color: var(--rc-blue);
        }

        .rc-remark-satisfactory {
            background: var(--rc-amber-light);
            color: var(--rc-amber);
        }

        .rc-remark-improve {
            background: var(--rc-red-light);
            color: var(--rc-red);
        }

        .rc-remark-desc {
            font-size: 12px;
            color: var(--rc-muted);
            line-height: 1.5;
        }

        .rc-comments-list {
            list-style: none;
            padding: 0;
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .rc-comments-list li {
            font-size: 13px;
            color: var(--rc-ink);
            line-height: 1.5;
            display: flex;
            align-items: flex-start;
            gap: 8px;
        }

        .rc-comment-bullet {
            color: var(--rc-green);
            flex-shrink: 0;
            margin-top: 1px;
            font-size: 10px;
        }

        .rc-footer {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            padding: 1rem 2rem 1.5rem;
            border-top: 1px solid var(--rc-border);
        }

        .rc-published {
            font-size: 12px;
            color: var(--rc-muted);
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .rc-signatures {
            display: flex;
            gap: 3rem;
        }

        .rc-sig {
            text-align: center;
        }

        .rc-sig-line {
            width: 130px;
            border-top: 1.5px solid var(--rc-ink);
            margin-bottom: 5px;
        }

        .rc-sig-name {
            font-size: 11px;
            font-weight: 600;
            letter-spacing: .06em;
            text-transform: uppercase;
            color: var(--rc-muted);
        }

        /* ════ PRINT ════════════════════════════════════ */
        @media print {
            body {
                background: white !important;
            }

            .no-print,
            #designToggleWrap {
                display: none !important;
            }

            .report-card-classic {
                box-shadow: none !important;
                border: none !important;
            }

            .rc-wrap {
                box-shadow: none !important;
                border-radius: 0 !important;
            }

            .rc-header,
            .rc-summary-bar {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }

        table {
            border-collapse: collapse;
        }
    </style>


    {{-- ═══════════════════════════════════════════════════════════════════
     MODERN DESIGN CSS — replace the design-b section in your <style>
═══════════════════════════════════════════════════════════════════ --}}
    {{--
PASTE THIS inside your existing <style> block,
replacing all rules from "/* ════ MODERN DESIGN (design-b)" through "table { border-collapse: collapse; }"
--}}
    <style>
        /* ════ MODERN DESIGN (design-b) — LIGHT ════════════════════════════ */
        .rc-wrap {
            font-family: var(--rc-ff-body);
            color: #111827;
            background: #ffffff;
            border-radius: var(--rc-radius);
            box-shadow: 0 1px 4px rgba(0, 0, 0, .07), 0 0 0 1px #e5e7eb;
            border-top: 3px solid var(--rc-green);
            overflow: hidden;
            margin-bottom: 2rem;
            page-break-after: always;
        }

        /* ── Header (was dark green, now white) ─────── */
        .rc-header {
            display: flex;
            align-items: center;
            gap: 1.5rem;
            padding: 1.5rem 2rem;
            background: #ffffff;
            border-bottom: 1px solid #e5e7eb;
            color: #111827;
        }

        .rc-header-identity {
            display: flex;
            align-items: center;
            gap: 14px;
            flex: 1;
            min-width: 0;
        }

        .rc-school-emblem {
            position: relative;
            width: 48px;
            height: 48px;
            flex-shrink: 0;
        }

        .rc-emblem-core {
            position: absolute;
            inset: 12px;
            background: var(--rc-green);
            border-radius: 50%;
        }

        .rc-emblem-leaf {
            position: absolute;
            width: 18px;
            height: 30px;
            background: #a7f3c1;
            border-radius: 50% 0 50% 0;
            top: 4px;
        }

        .rc-emblem-leaf--l {
            left: 2px;
            transform: rotate(-20deg);
        }

        .rc-emblem-leaf--r {
            right: 2px;
            transform: rotate(20deg) scaleX(-1);
        }

        .rc-school-name {
            font-family: var(--rc-ff-display);
            font-size: 15px;
            font-weight: 700;
            color: var(--rc-green);
            line-height: 1.2;
            letter-spacing: .01em;
        }

        .rc-school-addr {
            font-size: 11px;
            color: #6b7280;
            margin-top: 3px;
            line-height: 1.4;
        }

        .rc-header-title {
            text-align: center;
            flex-shrink: 0;
        }

        .rc-title-eyebrow {
            font-size: 9px;
            letter-spacing: .15em;
            text-transform: uppercase;
            color: #9ca3af;
            margin-bottom: 4px;
        }

        .rc-title-main {
            font-family: var(--rc-ff-display);
            font-size: 26px;
            font-weight: 700;
            color: var(--rc-green);
            line-height: 1;
        }

        .rc-title-sub {
            font-size: 12px;
            color: #4b5563;
            margin-top: 5px;
            font-weight: 500;
        }

        .rc-grade-scale {
            flex-shrink: 0;
        }

        .rc-scale-label {
            font-size: 9px;
            letter-spacing: .12em;
            text-transform: uppercase;
            color: #9ca3af;
            margin-bottom: 5px;
            text-align: center;
        }

        .rc-scale-table {
            border-collapse: collapse;
            font-size: 10.5px;
            background: #f9fafb;
            border-radius: 6px;
            overflow: hidden;
            border: 1px solid #e5e7eb;
        }

        .rc-scale-table th,
        .rc-scale-table td {
            padding: 3px 9px;
            border: 1px solid #e5e7eb;
            text-align: center;
            color: #374151;
        }

        .rc-scale-table th {
            background: #f3f4f6;
            font-weight: 600;
            font-size: 9.5px;
            color: #374151;
        }

        .rc-scale-letter {
            font-weight: 700;
            color: var(--rc-green);
        }

        /* ── Student strip ──────────────────────────── */
        .rc-student-strip {
            display: flex;
            align-items: center;
            gap: 1.5rem;
            padding: 1.25rem 2rem;
            background: #f0fdf4;
            border-bottom: 1px solid #d1fae5;
        }

        .rc-student-avatar {
            width: 52px;
            height: 52px;
            border-radius: 50%;
            background: var(--rc-green);
            color: #fff;
            font-family: var(--rc-ff-display);
            font-size: 22px;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            box-shadow: 0 2px 8px rgba(26, 107, 60, .25);
        }

        .rc-student-fields {
            display: flex;
            gap: 2rem;
            flex: 1;
            flex-wrap: wrap;
        }

        .rc-field {
            display: flex;
            flex-direction: column;
            gap: 2px;
        }

        .rc-field-label {
            font-size: 10px;
            letter-spacing: .1em;
            text-transform: uppercase;
            color: var(--rc-green);
            font-weight: 600;
        }

        .rc-field-value {
            font-size: 14px;
            font-weight: 500;
            color: #111827;
        }

        .rc-mono {
            font-family: var(--rc-ff-mono);
            font-size: 13px;
        }

        .rc-attendance-pill {
            display: flex;
            align-items: center;
            gap: 10px;
            background: #ffffff;
            border: 1px solid #d1fae5;
            border-radius: 40px;
            padding: 8px 16px 8px 8px;
            flex-shrink: 0;
        }

        .rc-att-ring {
            position: relative;
            width: 44px;
            height: 44px;
        }

        .rc-att-svg {
            width: 44px;
            height: 44px;
            transform: rotate(-90deg);
        }

        .rc-att-track {
            fill: none;
            stroke: #d1fae5;
            stroke-width: 4;
        }

        .rc-att-fill {
            fill: none;
            stroke: var(--rc-green);
            stroke-width: 4;
            stroke-linecap: round;
            stroke-dashoffset: 0;
        }

        .rc-att-pct {
            position: absolute;
            inset: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 9px;
            font-weight: 700;
            color: var(--rc-green);
        }

        .rc-att-text {
            display: flex;
            flex-direction: column;
            gap: 1px;
        }

        .rc-att-label {
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: .08em;
            color: #6b7280;
            font-weight: 600;
        }

        .rc-att-count {
            font-size: 13px;
            font-weight: 600;
            color: #111827;
        }

        /* ── Subjects table ─────────────────────────── */
        .rc-table-wrap {
            padding: 1.5rem 2rem 0;
            overflow-x: auto;
        }

        .rc-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
        }

        .rc-table thead tr {
            background: #1f2937;
            color: #fff;
        }

        .rc-table th {
            padding: 10px 14px;
            font-size: 11px;
            font-weight: 600;
            letter-spacing: .06em;
            text-transform: uppercase;
            text-align: center;
            border: none;
            color: #f9fafb;
        }

        .rc-th-left {
            text-align: left !important;
        }

        .rc-table tbody tr {
            border-bottom: 1px solid #f3f4f6;
            transition: background .15s;
        }

        .rc-table tbody tr:nth-child(even) {
            background: #f9fafb;
        }

        .rc-table tbody tr:hover {
            background: #f0fdf4;
        }

        .rc-table tbody tr.rc-row-fail {
            background: #fef2f2 !important;
        }

        .rc-table td {
            padding: 10px 14px;
            border: none;
            vertical-align: middle;
            color: #111827;
        }

        .rc-td-subject {
            font-weight: 500;
            padding-left: 14px;
            color: #111827;
        }

        .rc-fail-dot {
            display: inline-block;
            width: 7px;
            height: 7px;
            background: var(--rc-red);
            border-radius: 50%;
            margin-right: 6px;
            vertical-align: middle;
        }

        .rc-td-num {
            text-align: center;
            color: #6b7280;
        }

        .rc-td-obtained {
            font-weight: 600;
            color: #111827 !important;
        }

        .rc-td-total {
            font-weight: 700;
            color: var(--rc-green) !important;
        }

        .rc-td-gp {
            font-family: var(--rc-ff-mono);
            font-weight: 600;
            color: #111827 !important;
        }

        .rc-td-grade {
            text-align: center;
        }

        /* Grade chips — unchanged */
        .rc-grade-chip {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: .04em;
        }

        .rc-grade-a-plus,
        .rc-grade-a {
            background: #dcfce7;
            color: #166534;
        }

        .rc-grade-a-minus {
            background: #d1fae5;
            color: #065f46;
        }

        .rc-grade-b-plus,
        .rc-grade-b {
            background: #eff6ff;
            color: #1d4ed8;
        }

        .rc-grade-b-minus {
            background: #e0e7ff;
            color: #3730a3;
        }

        .rc-grade-c-plus,
        .rc-grade-c {
            background: #fef3c7;
            color: #d97706;
        }

        .rc-grade-d {
            background: #fee2e2;
            color: #991b1b;
        }

        .rc-grade-f {
            background: #fecaca;
            color: #dc2626;
        }

        /* ── Summary bar (was dark/black, now light) ── */
        .rc-summary-bar {
            display: flex;
            align-items: center;
            gap: 1.5rem;
            margin: 1.25rem 2rem;
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 10px;
            padding: 1rem 1.5rem;
            color: #111827;
        }

        .rc-summary-label {
            font-size: 10px;
            font-weight: 700;
            letter-spacing: .12em;
            text-transform: uppercase;
            color: #9ca3af;
            writing-mode: vertical-rl;
            transform: rotate(180deg);
            flex-shrink: 0;
        }

        .rc-summary-stats {
            display: flex;
            align-items: center;
            flex: 1;
        }

        .rc-stat {
            flex: 1;
            text-align: center;
            padding: 0 1rem;
        }

        .rc-stat-sep {
            width: 1px;
            height: 36px;
            background: #e5e7eb;
            flex-shrink: 0;
        }

        .rc-stat-val {
            font-family: var(--rc-ff-mono);
            font-size: 22px;
            font-weight: 500;
            line-height: 1;
            color: #111827;
        }

        .rc-stat-lbl {
            font-size: 10px;
            letter-spacing: .08em;
            text-transform: uppercase;
            color: #9ca3af;
            margin-top: 4px;
        }

        .rc-stat--highlight .rc-stat-val {
            color: var(--rc-green) !important;
            font-weight: 700;
        }

        .rc-stat--grade .rc-stat-val {
            font-family: var(--rc-ff-display);
            font-size: 26px;
            color: var(--rc-green) !important;
        }

        /* ── Bottom row ─────────────────────────────── */
        .rc-bottom-row {
            display: flex;
            gap: 1.25rem;
            padding: 0 2rem 1.5rem;
        }

        .rc-remarks-block,
        .rc-comments-block {
            flex: 1;
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 10px;
            padding: 1rem 1.25rem;
        }

        .rc-block-label {
            font-size: 10px;
            font-weight: 700;
            letter-spacing: .12em;
            text-transform: uppercase;
            color: #9ca3af;
            margin-bottom: 10px;
        }

        .rc-remark-tag {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 5px 14px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 700;
            margin-bottom: 8px;
        }

        .rc-remark-excellent {
            background: #dcfce7;
            color: #166534;
        }

        .rc-remark-good {
            background: #eff6ff;
            color: #1d4ed8;
        }

        .rc-remark-satisfactory {
            background: #fef3c7;
            color: #d97706;
        }

        .rc-remark-improve {
            background: #fef2f2;
            color: #dc2626;
        }

        .rc-remark-desc {
            font-size: 12px;
            color: #6b7280;
            line-height: 1.5;
            margin: 0;
        }

        .rc-comments-list {
            list-style: none;
            padding: 0;
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .rc-comments-list li {
            font-size: 13px;
            color: #374151;
            line-height: 1.5;
            display: flex;
            align-items: flex-start;
            gap: 8px;
        }

        .rc-comment-bullet {
            color: var(--rc-green);
            flex-shrink: 0;
            margin-top: 1px;
            font-size: 10px;
        }

        /* ── Footer ─────────────────────────────────── */
        .rc-footer {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            padding: 1rem 2rem 1.5rem;
            border-top: 1px solid #e5e7eb;
        }

        .rc-published {
            font-size: 12px;
            color: #6b7280;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        html[data-theme='dark'] .report-card-classic {
            background: linear-gradient(180deg, #0f172a 0%, #111827 100%);
            border-color: rgba(148, 163, 184, 0.2);
            box-shadow: 0 18px 40px rgba(2, 6, 23, 0.36);
            color: #e2e8f0;
        }

        html[data-theme='dark'] .report-card-classic .classic-header-inner {
            border-bottom-color: rgba(148, 163, 184, 0.18);
        }

        html[data-theme='dark'] .report-card-classic .classic-header-logo {
            background: #0f172a;
            border-color: rgba(148, 163, 184, 0.2);
        }

        html[data-theme='dark'] .report-card-classic .classic-grade-table table {
            background: #0f172a;
            color: #e2e8f0;
        }

        html[data-theme='dark'] .report-card-classic .classic-grade-table th {
            background: #1e293b;
            color: #f8fafc;
        }

        html[data-theme='dark'] .report-card-classic .classic-grade-table td {
            color: #cbd5e1;
        }

        html[data-theme='dark'] .report-card-classic .text-green-700 {
            color: #86efac !important;
        }

        html[data-theme='dark'] .report-card-classic .text-orange-700 {
            color: #fdba74 !important;
        }

        html[data-theme='dark'] .report-card-classic .text-gray-800,
        html[data-theme='dark'] .report-card-classic .text-gray-700,
        html[data-theme='dark'] .report-card-classic .text-gray-600,
        html[data-theme='dark'] .report-card-classic .text-gray-500,
        html[data-theme='dark'] .report-card-classic .text-black,
        html[data-theme='dark'] .report-card-classic .text-muted {
            color: #cbd5e1 !important;
        }

        html[data-theme='dark'] .rc-wrap {
            color: #e2e8f0;
            background: linear-gradient(180deg, #0f172a 0%, #111827 100%);
            border-color: rgba(148, 163, 184, 0.2);
            box-shadow: 0 18px 40px rgba(2, 6, 23, 0.32);
        }

        html[data-theme='dark'] .rc-header {
            background: linear-gradient(135deg, #0f4023 0%, #124a2a 55%, #166534 100%);
        }

        html[data-theme='dark'] .rc-student-strip {
            background: rgba(15, 23, 42, 0.94);
            border-bottom-color: rgba(148, 163, 184, 0.18);
        }

        html[data-theme='dark'] .rc-attendance-pill {
            background: #0f172a;
            border-color: rgba(148, 163, 184, 0.18);
        }

        html[data-theme='dark'] .rc-table thead tr {
            background: #1e293b;
            color: #f8fafc;
        }

        html[data-theme='dark'] .rc-table tbody tr {
            border-bottom-color: rgba(148, 163, 184, 0.16);
        }

        html[data-theme='dark'] .rc-table tbody tr:nth-child(even) {
            background: rgba(15, 23, 42, 0.94);
        }

        html[data-theme='dark'] .rc-table tbody tr:hover {
            background: rgba(30, 41, 59, 0.94);
        }

        html[data-theme='dark'] .rc-table tbody tr.rc-row-fail {
            background: rgba(127, 29, 29, 0.28) !important;
        }

        html[data-theme='dark'] .rc-field-label,
        html[data-theme='dark'] .rc-att-label,
        html[data-theme='dark'] .rc-published {
            color: #94a3b8;
        }

        html[data-theme='dark'] .rc-field-value,
        html[data-theme='dark'] .rc-att-count,
        html[data-theme='dark'] .rc-title-main,
        html[data-theme='dark'] .rc-school-name {
            color: #f8fafc;
        }

        html[data-theme='dark'] .rc-school-addr,
        html[data-theme='dark'] .rc-title-sub {
            color: #cbd5e1;
            opacity: 1;
        }

        html[data-theme='dark'] .rc-scale-table {
            background: rgba(15, 23, 42, 0.96);
            color: #e2e8f0;
        }

        html[data-theme='dark'] .rc-scale-table th,
        html[data-theme='dark'] .rc-scale-table td {
            border-color: rgba(148, 163, 184, 0.18);
        }

        .rc-signatures {
            display: flex;
            gap: 3rem;
        }

        .rc-sig {
            text-align: center;
        }

        .rc-sig-line {
            width: 130px;
            border-top: 1.5px solid #374151;
            margin-bottom: 5px;
        }

        .rc-sig-name {
            font-size: 11px;
            font-weight: 600;
            letter-spacing: .06em;
            text-transform: uppercase;
            color: #6b7280;
        }

        /* ════ PRINT ════════════════════════════════════ */
        @media print {
            body {
                background: white !important;
            }

            .no-print,
            #designToggleWrap {
                display: none !important;
            }

            .report-card-classic {
                box-shadow: none !important;
                border: none !important;
            }

            .rc-wrap {
                box-shadow: none !important;
                border-radius: 0 !important;
            }
        }

        table {
            border-collapse: collapse;
        }
    </style>

    {{-- ═══════════════════════════════════════════════════════════════════
     SCRIPT — design switcher (pure JS, no deps)
═══════════════════════════════════════════════════════════════════ --}}
    <script>
        (function() {
            var STORAGE_KEY = 'rc_design_pref';

            function switchDesign(useModern, animate) {
                var classics = document.querySelectorAll('.design-a');
                var moderns = document.querySelectorAll('.design-b');
                var lblC = document.getElementById('dsLabelClassic');
                var lblM = document.getElementById('dsLabelModern');

                /* fade targets in */
                var entering = useModern ? moderns : classics;
                var leaving = useModern ? classics : moderns;

                leaving.forEach(function(el) {
                    el.style.display = 'none';
                });

                entering.forEach(function(el) {
                    el.style.display = 'block';
                    if (animate) {
                        el.style.opacity = '0';
                        el.style.transform = 'translateY(8px)';
                        el.style.transition = 'opacity .3s ease, transform .3s ease';
                        requestAnimationFrame(function() {
                            requestAnimationFrame(function() {
                                el.style.opacity = '1';
                                el.style.transform = 'translateY(0)';
                            });
                        });
                    }
                });

                if (lblC) {
                    lblC.classList.toggle('active', !useModern);
                }
                if (lblM) {
                    lblM.classList.toggle('active', useModern);
                }

                try {
                    localStorage.setItem(STORAGE_KEY, useModern ? 'modern' : 'classic');
                } catch (e) {}
            }

            /* restore preference on load */
            window.addEventListener('DOMContentLoaded', function() {
                var pref = 'classic';
                try {
                    pref = localStorage.getItem(STORAGE_KEY) || 'classic';
                } catch (e) {}
                var toggle = document.getElementById('designToggle');
                if (toggle) {
                    toggle.checked = (pref === 'modern');
                }
                switchDesign(pref === 'modern', false);
            });

            /* expose to onclick handler */
            window.switchDesign = function(checked) {
                switchDesign(checked, true);
            };
        })();
    </script>
@endsection

@section('scripts')
<script>
document.querySelectorAll('.js-send-result-email').forEach((btn) => {
    btn.addEventListener('click', async () => {
        if (btn.dataset.sending === '1') return;
        btn.dataset.sending = '1';
        btn.disabled = true;
        const originalHtml = btn.innerHTML;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Sending...';

        const payload = {
            session_id: btn.dataset.sessionId,
            class_id: btn.dataset.classId,
            section_id: btn.dataset.sectionId,
            exam_id: btn.dataset.examId,
            student_id: btn.dataset.studentId,
        };

        try {
            const res = await fetch(btn.dataset.url, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(payload)
            });
            const data = await res.json();
            if (!res.ok || !data.ok) {
                throw new Error(data.message || 'Failed to send email.');
            }

            const statusEl = document.getElementById(btn.dataset.statusId);
            if (statusEl) {
                statusEl.classList.remove('badge-secondary');
                statusEl.classList.add('badge-success');
                statusEl.textContent = 'Email Sent';
            }
            btn.innerHTML = '<i class="fas fa-check mr-1"></i> Sent';
        } catch (e) {
            alert(e.message || 'Failed to send email.');
            btn.innerHTML = originalHtml;
            btn.disabled = false;
        } finally {
            btn.dataset.sending = '0';
        }
    });
});

$('#classSelectTop').on('change', function () {
    var classId = $(this).val();
    var $section = $('#sectionSelectTop');
    $section.html('<option value="">Loading...</option>');
    if (!classId) {
        $section.html('<option value="">— Select Section —</option>');
        return;
    }
    $.get('/ajax/sections-by-class', { class_id: classId }, function (data) {
        var opts = '<option value="">— Select Section —</option>';
        $.each(data, function (i, s) {
            opts += '<option value="' + s.id + '">' + (s.name_en || s.name_bn) + '</option>';
        });
        $section.html(opts);
        if (window.refreshSelect2) refreshSelect2($section);
    });
});

$('#pdfBtnTop').on('click', function () {
    var form = $('#reportFormTop');
    var params = form.serialize().replace('_token=' + $('input[name=_token]').val() + '&', '');
    window.open('{{ route('result.progress-report.pdf') }}?' + params, '_blank');
});
</script>
@endsection
