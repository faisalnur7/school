@extends('layouts.master')

@section('contents')
@php
    $schoolName = $school->name ?? 'Green Chartered School & College';
    $schoolAddress = $school->address ?? 'CIP Tower, Hazari-digir-phar, Dohajari, Chandanish, Chattogram';
    $logoUrl = !empty($school->logo) ? asset($school->logo) : null;
    $templateSettings = $templateSettings ?? \App\Models\ProgressReportTemplateSetting::current();
    $subjectWidths = $templateSettings->subject_column_widths ?? [];
    $mixColor = static function (string $hex, string $mix = '#ffffff', float $ratio = 0.8): string {
        $normalize = static function (string $color): string {
            $color = ltrim(trim($color), '#');
            if (strlen($color) === 3) {
                $color = $color[0] . $color[0] . $color[1] . $color[1] . $color[2] . $color[2];
            }

            return preg_match('/^[0-9a-fA-F]{6}$/', $color) ? strtolower($color) : '000000';
        };

        $hex = $normalize($hex);
        $mix = $normalize($mix);
        $ratio = max(0, min(1, $ratio));
        $inverse = 1 - $ratio;

        [$r1, $g1, $b1] = array_map('hexdec', str_split($hex, 2));
        [$r2, $g2, $b2] = array_map('hexdec', str_split($mix, 2));

        $r = (int) round(($r1 * $ratio) + ($r2 * $inverse));
        $g = (int) round(($g1 * $ratio) + ($g2 * $inverse));
        $b = (int) round(($b1 * $ratio) + ($b2 * $inverse));

        return sprintf('#%02x%02x%02x', $r, $g, $b);
    };

    $modernPalette = [
        'green' => $templateSettings->school_name_color,
        'green_light' => $mixColor($templateSettings->school_name_color, '#ffffff', 0.84),
        'green_dark' => $mixColor($templateSettings->school_name_color, '#000000', 0.78),
        'amber' => $templateSettings->report_title_color,
        'amber_light' => $mixColor($templateSettings->report_title_color, '#ffffff', 0.84),
        'red' => $templateSettings->remarks_title_color,
        'red_light' => $mixColor($templateSettings->remarks_title_color, '#ffffff', 0.84),
        'blue' => $templateSettings->student_label_color,
        'blue_light' => $mixColor($templateSettings->student_label_color, '#ffffff', 0.84),
        'ink' => $templateSettings->summary_bg_color,
        'muted' => $templateSettings->remarks_text_color,
        'border' => $templateSettings->table_border_color,
        'surface' => $templateSettings->table_body_bg_color,
        'white' => $templateSettings->table_body_bg_color,
    ];
@endphp
    <div class="col-12">
        @unless($isPreview ?? false)
        @include('pages.progress-report._filter')
        @endunless

        @unless($isPreview ?? false)
            {{-- ══ Top Action Bar ══ --}}
            <div class="d-flex justify-content-between align-items-center mb-4 no-print progress-report-legacy-action-bar">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center shadow"
                        style="width:52px;height:52px;background:linear-gradient(135deg,#1a6b3c,#2d9e5f);flex-shrink:0">
                        <i class="fas fa-file-invoice text-white fa-lg"></i>
                    </div>
                    <div>
                        <h4 class="mb-0 font-weight-bold text-white">Progress Report</h4>
                        <small class="text-muted">{{ $exam->name }} &mdash;
                            {{ $exam->academicSession->name_en ?? ($exam->academicSession->name_bn ?? '') }}</small>
                    </div>
                </div>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-dark btn-sm result-filter-icon-btn" data-toggle="modal" data-target="#progressSubjectSettingsModal" title="Subject Settings" aria-label="Subject Settings">
                        <i class="fas fa-cog"></i>
                    </button>
                    <button onclick="window.print()" class="btn btn-info btn-sm no-print result-filter-icon-btn" title="Print" aria-label="Print">
                        <i class="fas fa-print"></i>
                    </button>
                    <a href="{{ route('result.progress-report.index') }}" class="btn btn-secondary btn-sm no-print result-filter-icon-btn" title="Back" aria-label="Back">
                        <i class="fas fa-arrow-left"></i>
                    </a>
                    <a href="{{ route('result.progress-report.template-settings.edit') }}" class="btn btn-outline-light btn-sm no-print result-filter-icon-btn" title="Template Settings" aria-label="Template Settings">
                        <i class="fas fa-sliders-h"></i>
                    </a>
                </div>
            </div>
        @endunless

        @unless($isPreview ?? false)
            <div class="modal fade" id="progressSubjectSettingsModal" tabindex="-1" role="dialog" aria-labelledby="progressSubjectSettingsTitle" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                    <div class="modal-content">
                        <form method="GET" action="{{ route('result.progress-report.index') }}">
                            <div class="modal-header">
                                <div>
                                    <h5 class="modal-title mb-1" id="progressSubjectSettingsTitle"><i class="fas fa-sliders-h mr-2"></i>Report Subjects</h5>
                                    <small class="text-muted">Unchecked subjects will be hidden and ignored in totals, GPA, grade, and ranking.</small>
                                </div>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            </div>
                            <div class="modal-body">
                                @foreach(['session_id', 'class_id', 'section_id', 'exam_id', 'student_id'] as $filterKey)
                                    <input type="hidden" name="{{ $filterKey }}" value="{{ $filters[$filterKey] ?? '' }}">
                                @endforeach
                                <input type="hidden" name="subject_settings_applied" value="1">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <strong>Select subjects</strong>
                                    <div class="btn-group btn-group-sm">
                                        <button type="button" class="btn btn-outline-secondary" id="progressSubjectsSelectAll">All</button>
                                        <button type="button" class="btn btn-outline-secondary" id="progressSubjectsClearAll">None</button>
                                    </div>
                                </div>
                                <div class="row">
                                    @foreach($availableSubjects as $subject)
                                        <div class="col-md-6 mb-2">
                                            <label class="progress-subject-option d-flex align-items-center mb-0">
                                                <input type="checkbox" name="subject_ids[]" value="{{ $subject->id }}" class="mr-2 progress-subject-check"
                                                    @checked(in_array((int) $subject->id, $filters['subject_ids'] ?? [], true))>
                                                <span>{{ $subject->name }}</span>
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-light" data-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-dark"><i class="fas fa-check mr-1"></i>Apply Subjects</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endunless

        {{-- ══ Alert + Design Toggle Row ══ --}}
        @unless($isPreview ?? false)
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
        @endunless

        {{-- ═══════════════════════════════════════════════════════
         REPORTS LOOP
    ═══════════════════════════════════════════════════════ --}}
        @foreach ($studentsData as $data)
            @php
                $student = $data['student'];
                $info = $data['academicInfo'];
                $subjectRows = $data['subjectRows'];
                $summary = $data['summary'];
                $rank = $data['rank'] ?? null;
                $attendancePresent = $data['attendancePresent'];
                $attendanceTotal = $data['attendanceTotal'];
            @endphp

            @unless($isPreview ?? false)
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
            @endunless

            {{-- ╔══════════════════════════════════════════════╗
         ║  CLASSIC DESIGN (design-a)                  ║
         ╚══════════════════════════════════════════════╝ --}}
            <div class="design-a report-card-classic" style="
                border-color: {{ $templateSettings->card_border_color }};
                border-top-color: {{ $templateSettings->header_border_color }};
                --pr-header-border-color: {{ $templateSettings->header_border_color }};
                --pr-table-border: {{ $templateSettings->table_border_color }};
                --pr-watermark-opacity: {{ $templateSettings->watermark_opacity }};
                --pr-watermark-scale: {{ $templateSettings->watermark_scale }}%;
            ">
                @if($templateSettings->show_watermark && !empty($logoUrl))
                    <div class="report-card-watermark">
                        <img src="{{ $logoUrl }}" alt="" class="report-card-watermark__img" style="opacity: {{ $templateSettings->watermark_opacity }}; width: {{ $templateSettings->watermark_scale }}%;">
                    </div>
                @endif

                <div class="classic-header-inner">
                    <div class="classic-header-top">
                        <div class="classic-header-brand">
                            @if(!empty($logoUrl))
                                <div class="classic-header-logo">
                                    <img src="{{ $logoUrl }}" alt="{{ $schoolName }} logo" style="max-width: {{ $templateSettings->school_logo_max_width_mm }}cm;">
                                </div>
                            @endif
                            <div class="classic-header-copy">
                                <h1 class="text-3xl font-bold uppercase tracking-wide mb-0" style="font-size: {{ $templateSettings->school_name_font_size }}px; color: {{ $templateSettings->school_name_color }};">
                                    {{ $schoolName }}
                                </h1>
                                <p class="text-sm mt-1 mb-0" style="font-size: {{ $templateSettings->school_address_font_size }}px; color: {{ $templateSettings->school_address_color }};">
                                    {{ $schoolAddress }}
                                </p>
                            </div>
                        </div>
                    </div>
                    <h2 class="text-2xl font-bold italic mt-5 uppercase text-center" style="font-size: {{ $templateSettings->report_title_font_size }}px; color: {{ $templateSettings->report_title_color }};">
                        {{ $templateSettings->report_title_text }}
                    </h2>
                </div>

                @if($templateSettings->show_student_info)
                <div class="classic-student-row">
                    <div>
                        <h3 class="font-bold text-xl underline" style="color: {{ $templateSettings->student_value_color }};">{{ $exam->name }}</h3>
                        <div class="mt-4 space-y-1 text-sm">
                            <p><span class="font-semibold" style="color: {{ $templateSettings->student_label_color }};">Name</span> : <span style="color: {{ $templateSettings->student_value_color }};">{{ $student->full_name_en }}</span></p>
                            <p><span class="font-semibold" style="color: {{ $templateSettings->student_label_color }};">Class</span> : <span style="color: {{ $templateSettings->student_value_color }};">{{ $info?->schoolClass?->name_en ?? '—' }}</span></p>
                            <p><span class="font-semibold" style="color: {{ $templateSettings->student_label_color }};">ID</span> : <span style="color: {{ $templateSettings->student_value_color }};">{{ $student->student_cid ?? $student->id }}</span></p>
                        </div>
                    </div>
                    @if($templateSettings->show_grade_scale)
                    <div class="classic-grade-table">
                        <table class="text-xs border border-gray-700" style="border-color: {{ $templateSettings->table_border_color }};">
                            <thead style="background: {{ $templateSettings->table_header_bg_color }}; color: #000000;">
                                <tr>
                                    <th class="px-3 py-1 text-center">Range</th>
                                    <th class="px-1 py-1 text-center">Letter Grade</th>
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
                    @endif
                </div>
                @endif

                <div class="mt-6 overflow-x-auto">
                    <table class="w-full text-sm border border-gray-700" style="border-color: {{ $templateSettings->table_border_color }};">
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
                                            <td class="px-3 py-2 font-medium text-base">{{ $paper['subject_name'] }}</td>
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
                                        <td class="px-3 py-2  font-medium text-base">{{ $row['subject_name'] }}</td>
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

                @if($templateSettings->show_summary)
                <div class="mt-6">
                    <table class="w-full text-sm border border-gray-700" style="border-color: {{ $templateSettings->table_border_color }};">
                        <thead style="background: {{ $templateSettings->summary_bg_color }}; color: {{ $templateSettings->summary_text_color }};">
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
                @endif

                @if($templateSettings->show_remarks || !is_null($rank))
                <div class="classic-results-meta">
                    @if($templateSettings->show_remarks)
                    <div class="classic-remarks">
                        <h4>Remarks:</h4>
                        <div>
                            @if ($summary['gpa'] >= 4.0)
                                <p class="is-active">{{ $templateSettings->remark_excellent_text }}</p>
                            @elseif($summary['gpa'] >= 3.0)
                                <p class="is-active">{{ $templateSettings->remark_good_text }}</p>
                            @elseif($summary['gpa'] >= 2.0)
                                <p>{{ $templateSettings->remark_satisfactory_text }}</p>
                            @else
                                <p>{{ $templateSettings->remark_improve_text }}</p>
                            @endif
                        </div>
                    </div>
                    @endif
                    @if(!is_null($rank))
                    <div class="classic-position">
                        <div class="classic-position-label">Position</div>
                        <div class="classic-position-value">#{{ $rank }}</div>
                    </div>
                    @endif
                </div>
                @endif

                @if($templateSettings->show_comments)
                <div class="mt-6 border border-gray-400 p-4 text-sm">
                    <ul class="list-disc pl-5 space-y-2">
                        <li>{{ $student->full_name_en }} was present {{ $attendancePresent }} days out of
                            {{ $attendanceTotal }} days.</li>
                        @if ($summary['gpa'] >= 4.0)
                            <li>{{ $templateSettings->comments_excellent_text }}</li>
                        @elseif($summary['gpa'] >= 3.0)
                            <li>{{ $templateSettings->comments_good_text }}</li>
                        @else
                            <li>{{ $templateSettings->comments_default_text }}</li>
                        @endif
                    </ul>
                </div>
                @endif

                @if($templateSettings->show_signature || $templateSettings->show_print_date)
                <div class="mt-20 flex justify-between items-end text-sm">
                    <div>
                        @if($templateSettings->show_print_date)
                            <p class="font-semibold">Published Date: {{ now()->format('d-m-Y') }}</p>
                        @endif
                        @if($templateSettings->show_signature)
                            <div class="mt-12">
                                <div class="border-t w-40" style="border-top-color: {{ $templateSettings->signature_line_color }};"></div>
                                <p>Class Teacher</p>
                            </div>
                        @endif
                    </div>
                    @if($templateSettings->show_signature)
                        <div class="text-right">
                            <div class="border-t w-40 ml-auto" style="border-top-color: {{ $templateSettings->signature_line_color }};"></div>
                            <p>Principal</p>
                        </div>
                    @endif
                </div>
                @endif

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
            <div class="design-b rc-wrap" style="display:none; --rc-green: {{ $modernPalette['green'] }}; --rc-green-light: {{ $modernPalette['green_light'] }}; --rc-green-dark: {{ $modernPalette['green_dark'] }}; --rc-amber: {{ $modernPalette['amber'] }}; --rc-amber-light: {{ $modernPalette['amber_light'] }}; --rc-red: {{ $modernPalette['red'] }}; --rc-red-light: {{ $modernPalette['red_light'] }}; --rc-blue: {{ $modernPalette['blue'] }}; --rc-blue-light: {{ $modernPalette['blue_light'] }}; --rc-ink: {{ $modernPalette['ink'] }}; --rc-muted: {{ $modernPalette['muted'] }}; --rc-border: {{ $modernPalette['border'] }}; --rc-surface: {{ $modernPalette['surface'] }}; --rc-white: {{ $modernPalette['white'] }};">
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
                        <div class="rc-field">
                            <span class="rc-field-label">Rank</span>
                            <span class="rc-field-value">{{ $rank ? '#'.$rank : '—' }}</span>
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

                @if(!is_null($rank))
                    <div class="rc-position-strip" style="margin: 0 0 1rem; padding: 0.9rem 1.1rem; border: 1px solid var(--rc-border, #d1d5db); border-radius: 14px; background: #f8fafc; display: flex; align-items: center; justify-content: space-between; gap: 1rem;">
                        <div style="font-size: 0.85rem; font-weight: 700; text-transform: uppercase; letter-spacing: .08em; color: var(--rc-green, #1a6b3c);">Position</div>
                        <div style="font-size: 1.35rem; font-weight: 800; color: var(--rc-ink, #111827);">#{{ $rank }}</div>
                    </div>
                @endif

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
            border-bottom: 1px solid var(--pr-header-border-color, #e5e7eb);
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
            padding: 1px 3px !important;
            line-height: 1.05;
            white-space: nowrap;
            word-break: normal;
            border-color: var(--pr-table-border, #555);
        }

        .classic-grade-table th:nth-child(1),
        .classic-grade-table td:nth-child(1) { width: 44%; }
        .classic-grade-table th:nth-child(2),
        .classic-grade-table td:nth-child(2) { width: 28%; }
        .classic-grade-table th:nth-child(3),
        .classic-grade-table td:nth-child(3) { width: 28%; }

        .report-card-classic {
            font-family: 'Times New Roman', Times, serif;
            max-width: 680px;
            padding: 1rem;
            border: 1px solid #b7cdb7;
            border-top: 3px solid #4f7d55;
            border-radius: 5px;
            background: #ffffff;
            box-shadow: 0 2px 10px rgba(52, 93, 58, .08);
            color: #000;
        }

        .report-card-classic,
        .design-b.rc-wrap {
            width: 210mm;
            max-width: 210mm;
            min-height: 297mm;
            height: 297mm;
            margin-left: auto;
            margin-right: auto;
            page-break-after: always;
            break-after: page;
        }

        @media (max-width: 520px) {
            .report-card-classic,
            .design-b.rc-wrap {
                width: calc(100vw - 2rem);
                max-width: calc(100vw - 2rem);
                min-height: auto;
                height: auto;
            }
        }

        /* A4 typography scale */
        .report-card-classic {
            font-size: 11px;
        }

        .classic-header-copy h1 {
            font-size: 23px !important;
        }

        .classic-header-copy p {
            font-size: 15px !important;
        }

        .classic-header-inner > h2 {
            font-size: 19px !important;
        }

        .classic-student-row h3 {
            font-size: 18px !important;
        }

        .classic-student-row p {
            font-size: 16px;
        }

        .classic-grade-table table {
            font-size: 8.5px;
        }

        .classic-grade-table th,
        .classic-grade-table td {
            white-space: normal;
            overflow-wrap: normal;
        }

        .classic-grade-table th:nth-child(1),
        .classic-grade-table td:nth-child(1) {
            width: 38%;
        }

        .classic-grade-table th:nth-child(2),
        .classic-grade-table td:nth-child(2) {
            width: 38%;
        }

        .classic-grade-table th:nth-child(3),
        .classic-grade-table td:nth-child(3) {
            width: 24%;
        }

        .report-card-classic > .mt-6 table {
            font-size: 12px;
        }

        .report-card-classic > .mt-6 table th,
        .report-card-classic > .mt-6 table td {
            padding: .17rem .2rem !important;
        }

        .classic-remarks h4 {
            font-size: 16px;
        }

        .classic-remarks p {
            font-size: 12px;
        }

        .classic-position-label {
            font-size: 10px;
        }

        .classic-position-value {
            font-size: 14px;
        }

        .report-card-classic .border-gray-400 {
            font-size: 15px;
        }

        .classic-header-inner {
            border: 0;
            border-radius: 3px;
            padding: .65rem .75rem .45rem;
            background: linear-gradient(135deg, #e6f2e6 0%, #d5e9d5 100%);
        }

        .classic-header-top {
            display: block;
        }

        .classic-header-brand {
            justify-content: center;
            gap: 12px;
        }

        .classic-header-logo {
            width: 52px;
            height: 52px;
            border-color: #a8c5a9;
            border-radius: 5px;
        }

        .classic-header-copy {
            text-align: center;
        }

        .classic-header-copy h1 {
            font-family: 'Times New Roman', Times, serif;
            font-size: 22px !important;
            font-weight: 700;
            color: #2e6f35 !important;
            line-height: 1.1;
        }

        .classic-header-copy p {
            font-family: 'Times New Roman', Times, serif;
            font-size: 14px !important;
            font-style: italic;
            color: #56805a !important;
        }

        .classic-header-inner > h2 {
            margin: .55rem 0 .1rem !important;
            font-family: 'Times New Roman', Times, serif;
            font-size: 18px !important;
            font-weight: 700;
            color: #4f7d55 !important;
        }

        .classic-student-row {
            display: grid;
            grid-template-columns: minmax(0, 1fr) 172px;
            gap: 1rem;
            align-items: start;
            margin-top: 1rem;
        }

        .classic-student-row h3 {
            font-family: 'Times New Roman', Times, serif;
            margin: 0 0 .55rem;
            font-size: 16px !important;
            color: #000 !important;
        }

        .classic-student-row p {
            margin: .18rem 0;
            font-size: 14px;
            color: #000 !important;
        }

        .classic-grade-table {
            width: 172px;
        }

        .classic-grade-table table,
        .report-card-classic .classic-grade-table th,
        .report-card-classic .classic-grade-table td {
            font-family: 'Times New Roman', Times, serif;
            color: #000 !important;
            border-color: #9fbe9f !important;
            border-width: 1px !important;
            border-style: solid !important;
        }

        .classic-grade-table table {
            font-size: 9px;
        }

        .classic-grade-table thead {
            background: #dceedd !important;
        }

        .report-card-classic > .mt-6 {
            margin-top: .75rem !important;
        }

        .report-card-classic > .mt-6 table {
            width: 100%;
            font-family: 'Times New Roman', Times, serif;
            font-size: 13px;
            color: #000;
            border-collapse: collapse;
        }

        .report-card-classic > .mt-6 table th,
        .report-card-classic > .mt-6 table td {
            border-color: #9fbe9f !important;
            border-width: 1px !important;
            border-style: solid !important;
            padding: .23rem .28rem !important;
            color: #000 !important;
            line-height: 1.1;
        }

        .report-card-classic > .mt-6 table thead {
            background: #dceedd !important;
        }

        .classic-results-meta {
            display: grid;
            grid-template-columns: minmax(0, 1fr) 172px;
            gap: 1rem;
            align-items: start;
            margin-top: .55rem;
        }

        .classic-remarks h4 {
            margin: 0 0 .2rem;
            font-size: 15px;
            font-weight: 700;
            color: #2e6f35;
        }

        .classic-remarks p {
            margin: .08rem 0;
            font-size: 13px;
            color: #000;
        }

        .classic-remarks p.is-active {
            display: inline-block;
            margin: 0;
            padding: .05rem .22rem;
            border-radius: 3px;
            background: #4f7d55;
            color: #fff;
        }

        .classic-position {
            border: 1px solid #9fbe9f;
            text-align: center;
        }

        .classic-position-label {
            padding: .28rem;
            background: #dceedd;
            color: #000;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .08em;
        }

        .classic-position-value {
            padding: .28rem;
            background: #f0f8f0;
            color: #000;
            font-size: 16px;
            font-weight: 700;
        }

        .report-card-classic .border-gray-400 {
            border-color: #9fbe9f !important;
            background: #f0f9f0;
        }

        .report-card-classic .mt-10 {
            margin-top: 1.5rem !important;
        }

        .report-card-watermark,
        .rc-watermark {
            position: absolute;
            inset: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            pointer-events: none;
            z-index: 0;
            opacity: var(--pr-watermark-opacity, 0.08);
        }

        /* Keep the classic watermark elegant and safely behind every report element. */
        .report-card-classic {
            isolation: isolate;
        }

        .report-card-classic > .report-card-watermark {
            z-index: 0;
            overflow: hidden;
        }

        .report-card-classic > .report-card-watermark ~ * {
            position: relative;
            z-index: 1;
        }

        .report-card-watermark__img,
        .rc-watermark__img {
            width: min(560px, var(--pr-watermark-scale, 78%));
            max-width: var(--pr-watermark-scale, 78%);
            max-height: 78%;
            object-fit: contain;
            filter: grayscale(100%);
        }

        .report-card-classic > .report-card-watermark .report-card-watermark__img {
            width: min(62%, 430px) !important;
            max-width: 62% !important;
            max-height: 62%;
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
            background: linear-gradient(135deg, var(--rc-green-dark) 0%, var(--rc-green) 55%, var(--rc-green-light) 100%);
            color: var(--rc-white);
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
            color: var(--rc-white);
        }

        .rc-school-addr {
            font-size: 11px;
            opacity: .75;
            margin-top: 3px;
            line-height: 1.4;
            color: var(--rc-white);
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
            color: var(--rc-white);
        }

        .rc-title-main {
            font-family: var(--rc-ff-display);
            font-size: 26px;
            font-weight: 700;
            color: var(--rc-amber);
            line-height: 1;
        }

        .rc-title-sub {
            font-size: 12px;
            opacity: .85;
            margin-top: 5px;
            font-weight: 500;
            color: var(--rc-white);
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
            background: var(--rc-white);
            border-radius: 6px;
            overflow: hidden;
            border: 1px solid var(--rc-border);
        }

        .rc-scale-table th,
        .rc-scale-table td {
            padding: 3px 9px;
            border: 1px solid var(--rc-border);
            text-align: center;
        }

        .rc-scale-table th {
            background: var(--rc-ink);
            font-weight: 600;
            font-size: 9.5px;
            color: var(--rc-white);
        }

        .rc-scale-letter {
            font-weight: 700;
            color: var(--rc-amber);
        }

        .rc-student-strip {
            display: flex;
            align-items: center;
            gap: 1.5rem;
            padding: 1.25rem 2rem;
            background: var(--rc-green-light);
            border-bottom: 1px solid var(--rc-border);
        }

        .rc-student-avatar {
            width: 52px;
            height: 52px;
            border-radius: 50%;
            background: var(--rc-green);
            color: var(--rc-white);
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
            border: 1px solid var(--rc-border);
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
            stroke: var(--rc-green-light);
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
            color: var(--rc-white);
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
            background: var(--rc-green-light);
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
            color: var(--rc-white);
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
            background: var(--rc-border);
            flex-shrink: 0;
        }

        .rc-stat-val {
            font-family: var(--rc-ff-mono);
            font-size: 22px;
            font-weight: 500;
            line-height: 1;
            color: var(--rc-white);
        }

        .rc-stat-lbl {
            font-size: 10px;
            letter-spacing: .08em;
            text-transform: uppercase;
            opacity: .55;
            margin-top: 4px;
        }

        .rc-stat--highlight .rc-stat-val {
            color: var(--rc-amber);
        }

        .rc-stat--grade .rc-stat-val {
            font-family: var(--rc-ff-display);
            font-size: 26px;
            color: var(--rc-green);
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
            background: var(--rc-green-light);
            color: var(--rc-green);
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
            color: var(--rc-muted);
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
            @page {
                size: A4 portrait;
                margin: 9mm;
            }

            body {
                background: white !important;
            }

            .no-print,
            #designToggleWrap {
                display: none !important;
            }

            .report-card-classic {
                width: 100% !important;
                max-width: 100% !important;
                height: 279mm !important;
                min-height: 279mm !important;
                margin: 0 auto !important;
                padding: 5mm !important;
                box-shadow: none !important;
                border: 1px solid #b7cdb7 !important;
                page-break-after: always;
                break-after: page;
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

        /* Final A4 refinement pass */
        .report-card-classic {
            display: flex;
            flex-direction: column;
            min-height: 297mm;
            font-size: 14px;
        }

        .classic-header-inner {
            padding: .8rem .9rem .55rem;
            border: 1px solid #c4dec5;
        }

        .classic-header-brand {
            min-height: 58px;
        }

        .classic-grade-table {
            width: 180px;
        }

        .classic-grade-table table {
            font-size: 12px;
        }

        .classic-student-row {
            margin-top: .9rem;
            grid-template-columns: minmax(0, 1fr) 180px;
        }

        .report-card-classic > .mt-6 table {
            font-size: 14.5px;
        }

        .report-card-classic > .mt-6 table th,
        .report-card-classic > .mt-6 table td {
            padding: .2rem .24rem !important;
        }

        .classic-results-meta {
            grid-template-columns: minmax(0, 1fr) 180px;
            margin-top: .7rem;
        }

        .classic-remarks p {
            font-size: 14.5px;
        }

        .classic-remarks p.is-active {
            padding: .08rem .28rem;
        }

        .classic-position-label {
            padding: .32rem;
            font-size: 13px;
        }

        .classic-position-value {
            padding: .38rem;
            font-size: 17px;
        }

        .report-card-classic .border-gray-400 {
            margin-top: .8rem !important;
            padding: .75rem 1rem !important;
            border-radius: 4px;
        }

        .report-card-classic > .mt-10 {
            margin-top: auto !important;
            padding-top: 1.25rem;
            display: flex;
            align-items: flex-end;
        }

        .report-card-classic > .mt-10 .mt-12 {
            margin-top: .85rem !important;
        }

        .report-card-classic > .mt-10 .w-40 {
            width: 20mm !important;
        }

        .report-card-classic > .mt-10 p {
            margin-bottom: 0;
            font-size: 14px;
        }

        /* User-requested +4px typography scale */
        .report-card-classic {
            font-size: 18px;
        }

        .classic-header-copy h1 {
            font-size: 23px !important;
        }

        .classic-header-copy p {
            font-size: 15px !important;
        }

        .classic-header-inner > h2 {
            font-size: 19px !important;
        }

        .classic-student-row h3 {
            font-size: 22px !important;
        }

        .classic-student-row p {
            font-size: 20px;
        }

        .classic-grade-table table {
            font-size: 16px;
        }

        .report-card-classic > .mt-6 table {
            font-size: 21.5px;
        }

        .classic-remarks h4 {
            font-size: 20px;
        }

        .classic-remarks p {
            font-size: 18.5px;
        }

        .classic-position-label {
            font-size: 17px;
        }

        .classic-position-value {
            font-size: 21px;
        }

        .report-card-classic .border-gray-400 {
            font-size: 19px;
        }

        .report-card-classic > .mt-10 p {
            font-size: 18px;
        }

        @media (max-width: 520px) {
            .report-card-classic {
                min-height: auto;
                height: auto;
            }

            .classic-student-row,
            .classic-results-meta {
                grid-template-columns: minmax(0, 1fr) 150px;
                gap: .6rem;
            }

            .classic-grade-table {
                width: 150px;
            }
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
            color: var(--rc-muted);
            font-weight: 600;
        }

        .rc-att-count {
            font-size: 13px;
            font-weight: 600;
            color: var(--rc-ink);
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
            background: var(--rc-ink);
            color: var(--rc-white);
        }

        .rc-table th {
            padding: 10px 14px;
            font-size: 11px;
            font-weight: 600;
            letter-spacing: .06em;
            text-transform: uppercase;
            text-align: center;
            border: none;
            color: var(--rc-white);
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
            background: var(--rc-green-light);
        }

        .rc-table tbody tr.rc-row-fail {
            background: var(--rc-red-light) !important;
        }

        .rc-table td {
            padding: 10px 14px;
            border: none;
            vertical-align: middle;
            color: var(--rc-ink);
        }

        .rc-td-subject {
            font-weight: 500;
            padding-left: 14px;
            color: var(--rc-ink);
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
            background: var(--rc-green-light);
            color: var(--rc-green);
        }

        .rc-grade-a-minus {
            background: var(--rc-green-light);
            color: var(--rc-green);
        }

        .rc-grade-b-plus,
        .rc-grade-b {
            background: var(--rc-blue-light);
            color: var(--rc-blue);
        }

        .rc-grade-b-minus {
            background: var(--rc-blue-light);
            color: var(--rc-blue);
        }

        .rc-grade-c-plus,
        .rc-grade-c {
            background: var(--rc-amber-light);
            color: var(--rc-amber);
        }

        .rc-grade-d {
            background: var(--rc-red-light);
            color: var(--rc-red);
        }

        .rc-grade-f {
            background: var(--rc-red-light);
            color: var(--rc-red);
        }

        /* ── Summary bar (was dark/black, now light) ── */
        .rc-summary-bar {
            display: flex;
            align-items: center;
            gap: 1.5rem;
            margin: 1.25rem 2rem;
            background: var(--rc-surface);
            border: 1px solid var(--rc-border);
            border-radius: 10px;
            padding: 1rem 1.5rem;
            color: var(--rc-ink);
        }

        .rc-summary-label {
            font-size: 10px;
            font-weight: 700;
            letter-spacing: .12em;
            text-transform: uppercase;
            color: var(--rc-muted);
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
            background: var(--rc-border);
            flex-shrink: 0;
        }

        .rc-stat-val {
            font-family: var(--rc-ff-mono);
            font-size: 22px;
            font-weight: 500;
            line-height: 1;
            color: var(--rc-ink);
        }

        .rc-stat-lbl {
            font-size: 10px;
            letter-spacing: .08em;
            text-transform: uppercase;
            color: var(--rc-muted);
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
            background: var(--rc-green-light);
            color: var(--rc-green);
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
            color: var(--rc-muted);
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
            border-top: 1px solid var(--rc-border);
        }

        .rc-published {
            font-size: 12px;
            color: var(--rc-muted);
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
            border-top: 1.5px solid var(--rc-border);
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

document.getElementById('progressSubjectsSelectAll')?.addEventListener('click', () => {
    document.querySelectorAll('.progress-subject-check').forEach((input) => input.checked = true);
});
document.getElementById('progressSubjectsClearAll')?.addEventListener('click', () => {
    document.querySelectorAll('.progress-subject-check').forEach((input) => input.checked = false);
});

</script>
@endsection
