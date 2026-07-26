@php
    $studentName = data_get($previewStudent, 'full_name_en', 'Student Name');
    $studentCid = data_get($previewStudent, 'student_cid', '0001');
    $studentClass = data_get($previewStudent, 'class_name', 'Class Name');
    $studentRoll = data_get($previewStudent, 'roll', '12');
    $studentRank = data_get($previewStudent, 'rank', '—');
    $studentSession = data_get($previewStudent, 'session', now()->format('Y') . '-' . (now()->format('Y') + 1));
    $studentDob = data_get($previewStudent, 'dob', '20-05-2010');
    $remarksText = $summary['gpa'] >= 4.0
        ? $templateSettings->remark_excellent_text
        : ($summary['gpa'] >= 3.0
            ? $templateSettings->remark_good_text
            : ($summary['gpa'] >= 2.0
                ? $templateSettings->remark_satisfactory_text
                : $templateSettings->remark_improve_text));
    $commentText = $summary['gpa'] >= 4.0
        ? $templateSettings->comments_excellent_text
        : ($summary['gpa'] >= 3.0
            ? $templateSettings->comments_good_text
            : $templateSettings->comments_default_text);
@endphp

<div class="progress-modern-preview">
    <style>
        .progress-modern-preview {
            position: relative;
            background: #fff;
            border: 1px solid {{ $templateSettings->card_border_color }};
            border-top: 3px solid {{ $templateSettings->header_border_color }};
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 4px 18px rgba(15, 23, 42, 0.08);
            padding: 24px;
        }

        .progress-modern-preview__header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
            border-bottom: 1px solid {{ $templateSettings->header_border_color }};
            padding-bottom: 14px;
            margin-bottom: 18px;
        }

        .progress-modern-preview__brand {
            display: flex;
            align-items: center;
            gap: 14px;
            min-width: 0;
        }

        .progress-modern-preview__logo {
            width: 56px;
            height: 56px;
            border-radius: 12px;
            border: 1px solid {{ $templateSettings->card_border_color }};
            background: #fff;
            overflow: hidden;
            flex-shrink: 0;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .progress-modern-preview__logo img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
        }

        .progress-modern-preview__name {
            font-size: {{ $templateSettings->school_name_font_size }}px;
            font-weight: 800;
            text-transform: uppercase;
            color: {{ $templateSettings->school_name_color }};
            line-height: 1.1;
        }

        .progress-modern-preview__address {
            margin-top: 4px;
            font-size: {{ $templateSettings->school_address_font_size }}px;
            color: {{ $templateSettings->school_address_color }};
            font-weight: 600;
        }

        .progress-modern-preview__title {
            text-align: center;
            font-size: {{ $templateSettings->report_title_font_size }}px;
            font-weight: 800;
            font-style: italic;
            color: {{ $templateSettings->report_title_color }};
            margin: 4px 0 0;
        }

        .progress-modern-preview__scale {
            width: 165px;
            border-collapse: collapse;
            font-size: 10px;
            flex-shrink: 0;
        }

        .progress-modern-preview__scale th,
        .progress-modern-preview__scale td {
            border: 1px solid {{ $templateSettings->table_border_color }};
            padding: 2px 4px;
            text-align: center;
            line-height: 1.05;
        }

        .progress-modern-preview__scale thead th {
            background: {{ $templateSettings->table_header_bg_color }};
            color: {{ $templateSettings->table_header_text_color }};
        }

        .progress-modern-preview__student {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            padding: 16px 18px;
            background: {{ $templateSettings->table_body_bg_color }};
            border: 1px solid {{ $templateSettings->table_border_color }};
            border-radius: 14px;
            margin-bottom: 18px;
        }

        .progress-modern-preview__meta {
            display: grid;
            gap: 6px;
        }

        .progress-modern-preview__meta strong {
            color: {{ $templateSettings->student_label_color }};
        }

        .progress-modern-preview__meta span {
            color: {{ $templateSettings->student_value_color }};
        }

        .progress-modern-preview__summary-bar {
            display: flex;
            gap: 12px;
            align-items: stretch;
            background: {{ $templateSettings->summary_bg_color }};
            color: {{ $templateSettings->summary_text_color }};
            border-radius: 14px;
            padding: 14px 16px;
            margin: 18px 0;
        }

        .progress-modern-preview__summary-item {
            flex: 1;
            text-align: center;
            border-left: 1px solid rgba(255, 255, 255, .18);
            padding-left: 10px;
        }

        .progress-modern-preview__summary-item:first-child {
            border-left: 0;
            padding-left: 0;
        }

        .progress-modern-preview__summary-value {
            display: block;
            font-size: 20px;
            font-weight: 800;
            line-height: 1;
        }

        .progress-modern-preview__summary-label {
            display: block;
            margin-top: 4px;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: .08em;
            opacity: .8;
        }

        .progress-modern-preview__content {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
        }

        .progress-modern-preview__panel {
            border: 1px solid {{ $templateSettings->table_border_color }};
            background: {{ $templateSettings->table_body_bg_color }};
            border-radius: 14px;
            padding: 14px 16px;
            min-height: 124px;
        }

        .progress-modern-preview__panel-title {
            font-size: 11px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: .1em;
            color: {{ $templateSettings->remarks_title_color }};
            margin-bottom: 10px;
        }

        .progress-modern-preview__remark {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 5px 12px;
            border-radius: 999px;
            font-weight: 800;
            margin-bottom: 10px;
            background: {{ $templateSettings->position_label_bg_color }};
            color: {{ $templateSettings->position_label_text_color }};
        }

        .progress-modern-preview__remark-text,
        .progress-modern-preview__comment {
            color: {{ $templateSettings->remarks_text_color }};
            line-height: 1.5;
            font-size: 12px;
        }

        .progress-modern-preview__table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
            font-size: 11px;
            border: 1px solid {{ $templateSettings->table_border_color }};
        }

        .progress-modern-preview__table th,
        .progress-modern-preview__table td {
            border: 1px solid {{ $templateSettings->table_border_color }};
            padding: 5px 6px;
            text-align: center;
        }

        .progress-modern-preview__table thead th {
            background: {{ $templateSettings->table_header_bg_color }};
            color: {{ $templateSettings->table_header_text_color }};
        }

        .progress-modern-preview__table tbody tr:nth-child(even) {
            background: {{ $templateSettings->table_row_alt_bg_color }};
        }

        .progress-modern-preview__footer {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            gap: 20px;
            margin-top: 16px;
            padding-top: 12px;
            border-top: 1px solid {{ $templateSettings->signature_line_color }};
        }

        .progress-modern-preview__signature {
            text-align: center;
            min-width: 120px;
        }

        .progress-modern-preview__signature-line {
            border-top: 1px solid {{ $templateSettings->signature_line_color }};
            width: 120px;
            margin-bottom: 6px;
        }
    </style>

    <div class="progress-modern-preview__header">
        <div class="progress-modern-preview__brand">
            @if(!empty($logoUrl))
                <div class="progress-modern-preview__logo">
                    <img src="{{ $logoUrl }}" alt="{{ $schoolName }} logo">
                </div>
            @endif
            <div>
                <div class="progress-modern-preview__name">{{ $schoolName }}</div>
                <div class="progress-modern-preview__address">{{ $schoolAddress }}</div>
            </div>
        </div>

        <div class="text-center flex-shrink-0">
            <div class="small text-uppercase text-muted font-weight-bold mb-1">Official Academic Document</div>
            <div class="progress-modern-preview__title">{{ $templateSettings->report_title_text }}</div>
        </div>

        <table class="progress-modern-preview__scale">
            <thead>
                <tr><th colspan="3">{{ $templateSettings->grade_scale_title }}</th></tr>
                <tr><th>Range</th><th>Grade</th><th>GP</th></tr>
            </thead>
            <tbody>
                @foreach ($gradeScale as $grade)
                    <tr>
                        <td>{{ $grade['min'] }}-{{ $grade['max'] }}</td>
                        <td>{{ $grade['letter'] }}</td>
                        <td>{{ number_format($grade['gpa'], 1) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="progress-modern-preview__student">
        <div class="progress-modern-preview__meta">
            <div><strong>Name</strong> : <span>{{ $studentName }}</span></div>
            <div><strong>Class</strong> : <span>{{ $studentClass }}</span></div>
            <div><strong>ID</strong> : <span>{{ $studentCid }}</span></div>
            <div><strong>Rank</strong> : <span>{{ $studentRank }}</span></div>
            <div><strong>Roll</strong> : <span>{{ $studentRoll }}</span></div>
        </div>
        <div class="progress-modern-preview__meta text-right">
            <div><strong>Session</strong> : <span>{{ $studentSession }}</span></div>
            <div><strong>DOB</strong> : <span>{{ $studentDob }}</span></div>
            <div><strong>Attendance</strong> : <span>{{ $attendancePresent }}/{{ $attendanceTotal }}</span></div>
        </div>
    </div>

    <table class="progress-modern-preview__table">
        <thead>
            <tr>
                <th class="text-left">Subject</th>
                <th>Full Marks</th>
                <th>Obtained</th>
                <th>Highest</th>
                <th>Grade</th>
                <th>GP</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($sampleRows as $row)
                <tr>
                    <td class="text-left">{{ $row['subject_name'] }}</td>
                    <td>{{ number_format($row['full_marks'], 0) }}</td>
                    <td>{{ number_format($row['obtained'], 0) }}</td>
                    <td>{{ number_format($row['highest'], 0) }}</td>
                    <td>{{ $row['grade'] }}</td>
                    <td>{{ number_format($row['gpa'], 1) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="progress-modern-preview__summary-bar">
        <div class="progress-modern-preview__summary-item">
            <span class="progress-modern-preview__summary-value">{{ number_format($summary['fullMarks'], 0) }}</span>
            <span class="progress-modern-preview__summary-label">Total Marks</span>
        </div>
        <div class="progress-modern-preview__summary-item">
            <span class="progress-modern-preview__summary-value">{{ number_format($summary['obtained'], 0) }}</span>
            <span class="progress-modern-preview__summary-label">Obtained</span>
        </div>
        <div class="progress-modern-preview__summary-item">
            <span class="progress-modern-preview__summary-value">{{ number_format($summary['gpa'], 2) }}</span>
            <span class="progress-modern-preview__summary-label">GPA</span>
        </div>
        <div class="progress-modern-preview__summary-item">
            <span class="progress-modern-preview__summary-value">{{ $summary['grade'] }}</span>
            <span class="progress-modern-preview__summary-label">Letter Grade</span>
        </div>
    </div>

    @if (! is_null(data_get($previewStudent, 'rank')))
    <div class="progress-modern-preview__position" style="display:flex; align-items:center; justify-content:space-between; gap:16px; margin: 0 0 18px; padding: 12px 16px; border-radius: 14px; border: 1px solid {{ $templateSettings->table_border_color }}; background: {{ $templateSettings->table_body_bg_color }};">
        <div style="font-size: 11px; text-transform: uppercase; letter-spacing: .12em; font-weight: 800; color: {{ $templateSettings->student_label_color }};">Position</div>
        <div style="font-size: 24px; font-weight: 800; color: {{ $templateSettings->student_value_color }};">#{{ data_get($previewStudent, 'rank') }}</div>
    </div>
    @endif

    <div class="progress-modern-preview__content">
        <div class="progress-modern-preview__panel">
            <div class="progress-modern-preview__panel-title">Remarks</div>
            <div class="progress-modern-preview__remark">{{ $remarksText }}</div>
            <div class="progress-modern-preview__remark-text">The saved settings drive this preview.</div>
        </div>
        <div class="progress-modern-preview__panel">
            <div class="progress-modern-preview__panel-title">Comments</div>
            <div class="progress-modern-preview__comment">{{ $commentText }}</div>
        </div>
    </div>

    <div class="progress-modern-preview__footer">
        <div class="small text-muted font-italic">Published: {{ now()->format('d M Y') }}</div>
        <div class="progress-modern-preview__signature">
            <div class="progress-modern-preview__signature-line"></div>
            <div class="small text-uppercase text-muted font-weight-bold">Class Teacher</div>
        </div>
        <div class="progress-modern-preview__signature">
            <div class="progress-modern-preview__signature-line"></div>
            <div class="small text-uppercase text-muted font-weight-bold">Principal</div>
        </div>
    </div>
</div>
