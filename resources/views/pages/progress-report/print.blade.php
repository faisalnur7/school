<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Terminal Progress Report</title>
    <style>
        @page {
            size: A4 {{ $templateSettings->paper_orientation }};
            margin: {{ $templateSettings->margin_top_mm }}cm {{ $templateSettings->margin_right_mm }}cm {{ $templateSettings->margin_bottom_mm }}cm {{ $templateSettings->margin_left_mm }}cm;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: Arial, Helvetica, sans-serif;
            font-size: 10.5px;
            color: #111827;
            background: #fff;
        }

        .report-card {
            position: relative;
            width: 100%;
            min-height: 28cm;
            padding: 1cm 1cm 0.8cm;
            border: 1px solid #111;
            overflow: hidden;
            page-break-after: always;
            break-after: page;
        }

        .report-card:last-child {
            page-break-after: auto;
            break-after: auto;
        }

        .report-card__watermark {
            position: absolute;
            inset: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: {{ $templateSettings->watermark_opacity }};
            pointer-events: none;
            z-index: 0;
        }

        .report-card__watermark img {
            width: {{ $templateSettings->watermark_scale }}%;
            max-width: {{ $templateSettings->watermark_scale }}%;
            max-height: 82%;
            object-fit: contain;
            filter: grayscale(100%);
        }

        .report-card__content {
            position: relative;
            z-index: 1;
        }

        .report-card__header {
            display: table;
            width: 100%;
            table-layout: fixed;
            border-collapse: collapse;
        }

        .report-card__header-row {
            display: table-row;
        }

        .report-card__identity,
        .report-card__scale-cell {
            display: table-cell;
            vertical-align: top;
            border: 0;
            padding: 0;
        }

        .report-card__identity {
            width: 74%;
            padding-right: 0.5cm;
        }

        .report-card__scale-cell { width: 26%; }

        .report-card__identity-wrap {
            display: table;
            width: 100%;
            table-layout: fixed;
        }

        .report-card__logo-cell {
            display: table-cell;
            width: 1.8cm;
            vertical-align: middle;
        }

        .report-card__logo {
            width: 1.6cm;
            height: 1.6cm;
            border: 1px solid #cbd5e1;
            border-radius: 0.2cm;
            overflow: hidden;
            background: #fff;
            text-align: center;
        }

        .report-card__logo img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        .report-card__school-cell {
            display: table-cell;
            vertical-align: middle;
            padding-left: 0.3cm;
            min-width: 0;
        }

        .report-card__school-name {
            font-size: {{ $templateSettings->school_name_font_size }}px;
            line-height: 1.08;
            font-weight: 800;
            color: {{ $templateSettings->school_name_color }};
            text-transform: uppercase;
            letter-spacing: .2px;
            overflow-wrap: anywhere;
            word-break: break-word;
        }

        .report-card__school-address {
            margin-top: 0.15cm;
            font-size: {{ $templateSettings->school_address_font_size }}px;
            font-weight: 700;
            color: {{ $templateSettings->school_address_color }};
            line-height: 1.25;
        }

        .report-card__title {
            font-size: {{ $templateSettings->report_title_font_size }}px;
            font-weight: 800;
            font-style: italic;
            color: {{ $templateSettings->report_title_color }};
            text-transform: uppercase;
            margin: 0.2cm 0 0.3cm;
            text-align: center;
        }

        .report-card__scale {
            width: 100%;
            border-collapse: collapse;
            font-size: 7.5px;
            table-layout: fixed;
        }

        .report-card__scale th,
        .report-card__scale td {
            border: 1px solid {{ $templateSettings->table_border_color }};
            padding: 0.1cm 0.15cm;
            text-align: center;
            line-height: 1.1;
            white-space: nowrap;
            word-break: normal;
        }

        .report-card__scale th:nth-child(1),
        .report-card__scale td:nth-child(1) { width: 44%; }
        .report-card__scale th:nth-child(2),
        .report-card__scale td:nth-child(2) { width: 28%; }
        .report-card__scale th:nth-child(3),
        .report-card__scale td:nth-child(3) { width: 28%; }

        .report-card__section {
            margin-top: 0.4cm;
        }

        .report-card__exam {
            font-size: 14px;
            font-weight: 800;
            text-decoration: underline;
            margin-bottom: 0.3cm;
        }

        .report-card__student {
            border-collapse: collapse;
            font-size: 10px;
        }

        .report-card__student td {
            border: 0;
            padding: 0.1cm 0.2cm 0.1cm 0;
            white-space: nowrap;
        }

        .report-card__student td:first-child {
            font-weight: 700;
            width: 1.6cm;
            color: {{ $templateSettings->student_label_color }};
        }

        .report-card__table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
            font-size: 9px;
        }

        .report-card__table thead tr {
            background: {{ $templateSettings->table_header_bg_color }};
            color: {{ $templateSettings->table_header_text_color }};
        }

        .report-card__table tbody tr:nth-child(even) {
            background: {{ $templateSettings->table_row_alt_bg_color }};
        }

        .report-card__table th,
        .report-card__table td {
            border: 1px solid {{ $templateSettings->table_border_color }};
            padding: 0.12cm 0.1cm;
            text-align: center;
            vertical-align: middle;
            line-height: 1.08;
            word-wrap: break-word;
        }

        .report-card__table th:first-child,
        .report-card__table td:first-child {
            text-align: left;
        }

        .report-card__summary-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
            font-size: 9px;
        }

        .report-card__summary-table th,
        .report-card__summary-table td {
            border: 1px solid {{ $templateSettings->table_border_color }};
            padding: 0.12cm 0.1cm;
            text-align: center;
            line-height: 1.1;
        }

        .report-card__summary-table thead tr {
            background: {{ $templateSettings->summary_bg_color }};
            color: {{ $templateSettings->summary_text_color }};
        }

        .report-card__remarks {
            margin-top: 0.4cm;
            font-size: 9.5px;
        }

        .report-card__remarks-title {
            font-size: 12px;
            font-weight: 800;
            text-decoration: underline;
            margin-bottom: 0.15cm;
            color: {{ $templateSettings->remarks_title_color }};
        }

        .report-card__comments {
            margin-top: 0.4cm;
            border: 1px solid {{ $templateSettings->table_border_color }};
            padding: 0.3cm;
            font-size: 9px;
        }

        .report-card__comments ul {
            margin: 0;
            padding-left: 0.45cm;
        }

        .report-card__comments li + li {
            margin-top: 0.15cm;
        }

        .report-card__footer {
            margin-top: 0.7cm;
            display: table;
            width: 100%;
        }

        .report-card__footer-left,
        .report-card__footer-right {
            display: table-cell;
            vertical-align: bottom;
            width: 50%;
        }

        .report-card__footer-right {
            text-align: right;
        }

        .report-card__signature {
            margin-top: 1.6cm;
        }

        .report-card__signature-line {
            border-top: 1px solid {{ $templateSettings->signature_line_color }};
            width: 4cm;
            margin-bottom: 0.2cm;
        }

        .report-card__published {
            font-weight: 700;
        }
    </style>
</head>
<body>
@php
    $schoolName = $school->name ?? 'Green Chartered School & College';
    $schoolAddress = $school->address ?? 'CIP Tower, Hazari-digir-phar, Dohajari, Chandanish, Chattogram';
    $logoPath = !empty($school->logo) ? public_path($school->logo) : null;
    $hasLogo = $logoPath && file_exists($logoPath);
    $templateSettings = $templateSettings ?? \App\Models\ProgressReportTemplateSetting::current();
    $subjectWidths = $templateSettings->subject_column_widths ?? [];
@endphp

@foreach($studentsData as $data)
    @php
        $student = $data['student'];
        $info = $data['academicInfo'];
        $subjectRows = $data['subjectRows'];
        $summary = $data['summary'];
        $attendancePresent = $data['attendancePresent'];
        $attendanceTotal = $data['attendanceTotal'];
    @endphp

    <div class="report-card" style="border-color: {{ $templateSettings->card_border_color }}; border-top-color: {{ $templateSettings->header_border_color }};">
        @if($templateSettings->show_watermark && $hasLogo)
            <div class="report-card__watermark">
                <img src="{{ $logoPath }}" alt="">
            </div>
        @endif

        <div class="report-card__content">
            <div class="report-card__header">
                <div class="report-card__header-row">
                    <div class="report-card__identity">
                        <div class="report-card__identity-wrap">
                            <div class="report-card__logo-cell">
                                @if($hasLogo)
                                    <div class="report-card__logo">
                                        <img src="{{ $logoPath }}" alt="{{ $schoolName }} logo">
                                    </div>
                                @endif
                            </div>
                            <div class="report-card__school-cell">
                                <div class="report-card__school-name">{{ $schoolName }}</div>
                                <div class="report-card__school-address">{{ $schoolAddress }}</div>
                            </div>
                        </div>
                    </div>
                    @if($templateSettings->show_grade_scale)
                    <div class="report-card__scale-cell">
                        <table class="report-card__scale">
                            <thead>
                                <tr>
                                    <th>Letter Grade</th>
                                    <th>Point</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($gradeScale as $grade)
                                    <tr>
                                        <td>{{ $grade['min'] }}-{{ $grade['max'] }} = {{ $grade['letter'] }}</td>
                                        <td>{{ number_format($grade['gpa'], 1) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @endif
                </div>
            </div>

            <div class="report-card__title">{{ $templateSettings->report_title_text }}</div>

            @if($templateSettings->show_student_info)
            <div class="report-card__section">
                <div class="report-card__exam">{{ $exam->name }}</div>
                <table class="report-card__student">
                    <tr>
                        <td>Name</td>
                        <td>:</td>
                        <td>{{ $student->full_name_en }}</td>
                    </tr>
                    <tr>
                        <td>Class</td>
                        <td>:</td>
                        <td>{{ $info?->schoolClass?->name_en ?? '—' }}</td>
                    </tr>
                    <tr>
                        <td>ID</td>
                        <td>:</td>
                        <td>{{ $student->student_cid ?? $student->id }}</td>
                    </tr>
                </table>
            </div>
            @endif

            <div class="report-card__section">
                <table class="report-card__table">
                    <thead>
                        <tr>
                            <th style="width: {{ $subjectWidths['subject'] ?? 30 }}%;">Subjects</th>
                            <th style="width: {{ $subjectWidths['full_marks'] ?? 10 }}%;">Full Marks</th>
                            <th style="width: {{ $subjectWidths['obtained_marks'] ?? 12 }}%;">Obtained Marks</th>
                            <th style="width: {{ $subjectWidths['highest_marks'] ?? 12 }}%;">Highest Marks</th>
                            <th style="width: {{ $subjectWidths['total_marks'] ?? 12 }}%;">Total Marks</th>
                            <th style="width: {{ $subjectWidths['letter_grade'] ?? 12 }}%;">Letter Grade</th>
                            <th style="width: {{ $subjectWidths['grade_point'] ?? 12 }}%;">Grade Point</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($subjectRows as $row)
                            @if(!empty($row['papers']))
                                @foreach($row['papers'] as $paperIndex => $paper)
                                    <tr>
                                        <td>{{ $paper['subject_name'] }}</td>
                                        <td>{{ number_format($paper['full_marks'], 0) }}</td>
                                        <td>{{ $paper['obtained'] ? number_format($paper['obtained'], 0) : '—' }}</td>
                                        <td>{{ number_format($paper['highest'], 0) }}</td>
                                        @if($paperIndex === 0)
                                            <td rowspan="{{ count($row['papers']) }}">{{ is_null($row['obtained']) ? '—' : number_format($row['obtained'], 0) }}</td>
                                            <td rowspan="{{ count($row['papers']) }}">{{ $row['grade'] }}</td>
                                            <td rowspan="{{ count($row['papers']) }}">{{ number_format($row['gpa'], 1) }}</td>
                                        @endif
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td>{{ $row['subject_name'] }}</td>
                                    <td>{{ number_format($row['full_marks'], 0) }}</td>
                                    <td>{{ $row['obtained'] ? number_format($row['obtained'], 0) : '—' }}</td>
                                    <td>{{ number_format($row['highest'], 0) }}</td>
                                    <td>{{ $row['obtained'] ? number_format($row['obtained'], 0) : '—' }}</td>
                                    <td>{{ $row['grade'] }}</td>
                                    <td>{{ number_format($row['gpa'], 1) }}</td>
                                </tr>
                            @endif
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($templateSettings->show_summary)
            <div class="report-card__section">
                <table class="report-card__summary-table">
                    <thead>
                        <tr>
                            <th>Summary</th>
                            <th>Total Exam Marks</th>
                            <th>Obtained Total Marks/Percent</th>
                            <th>GPA</th>
                            <th>Letter Grade</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td></td>
                            <td>{{ number_format($summary['fullMarks'], 0) }}</td>
                            <td>{{ number_format($summary['obtained'], 0) }} / {{ number_format($summary['percentage'], 2) }}%</td>
                            <td>{{ number_format($summary['gpa'], 2) }}</td>
                            <td>{{ $summary['grade'] }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            @endif

            @if($templateSettings->show_remarks)
            <div class="report-card__remarks">
                <div class="report-card__remarks-title">Remarks:</div>
                @if($summary['gpa'] >= 4.0)
                    <div style="color: {{ $templateSettings->remarks_text_color }};">{{ $templateSettings->remark_excellent_text }}</div>
                @elseif($summary['gpa'] >= 3.0)
                    <div style="color: {{ $templateSettings->remarks_text_color }};">{{ $templateSettings->remark_good_text }}</div>
                @elseif($summary['gpa'] >= 2.0)
                    <div style="color: {{ $templateSettings->remarks_text_color }};">{{ $templateSettings->remark_satisfactory_text }}</div>
                @else
                    <div style="color: {{ $templateSettings->remarks_text_color }};">{{ $templateSettings->remark_improve_text }}</div>
                @endif
            </div>
            @endif

            @if($templateSettings->show_comments)
            <div class="report-card__comments">
                <ul>
                    <li>{{ $student->full_name_en }} was present {{ $attendancePresent }} days out of {{ $attendanceTotal }} days.</li>
                    @if($summary['gpa'] >= 4.0)
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
            <div class="report-card__footer">
                <div class="report-card__footer-left">
                    @if($templateSettings->show_print_date)
                        <div class="report-card__published">Published Date: {{ now()->format('d-m-Y') }}</div>
                    @endif
                    @if($templateSettings->show_signature)
                        <div class="report-card__signature">
                            <div class="report-card__signature-line" style="border-top-color: {{ $templateSettings->signature_line_color }};"></div>
                            <div>Class Teacher</div>
                        </div>
                    @endif
                </div>
                @if($templateSettings->show_signature)
                    <div class="report-card__footer-right">
                        <div class="report-card__signature">
                            <div class="report-card__signature-line" style="margin-left:auto; border-top-color: {{ $templateSettings->signature_line_color }};"></div>
                            <div>Principal</div>
                        </div>
                    </div>
                @endif
            </div>
            @endif
        </div>
    </div>
@endforeach
</body>
</html>
