<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Terminal Progress Report</title>
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: 'Times New Roman', Times, serif;
            font-size: 12px;
            color: #2c3e50;
            background: #fff;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        .report-card {
            position: relative;
            width: 100%;
            min-height: 0;
            padding: 0.4cm 0.4cm 0.4cm;
            border: 1px solid #b8d4e8;
            border-radius: 8px;
            background: linear-gradient(180deg, #f8fcff 0%, #ffffff 20%);
            overflow: hidden;
        }

        .report-card__content {
            position: relative;
            z-index: 1;
        }

        /* ---------- Header ---------- */
        .report-card__header {
            width: 100%;
            border-collapse: collapse;
            border-radius: 6px;
            background: linear-gradient(135deg, #e8f4fc 0%, #d6ebf7 100%);
            box-shadow: 0 1px 0 rgba(111, 184, 224, 0.35);
        }

        .report-card__school-name {
            font-size: {{ min((float) $templateSettings->school_name_font_size + 0.5, 17) }}px;
            line-height: 1.2;
            font-weight: 800;
            color: #0d6e9a;
            text-transform: uppercase;
            letter-spacing: 0.45px;
            font-weight: 800;
        }

        .report-card__school-address {
            margin-top: 0.06cm;
            font-size: {{ (float) $templateSettings->school_address_font_size + 0.5 }}px;
            font-weight: 600;
            font-style: italic;
            color: #3d8fad;
            line-height: 1.3;
        }

        .report-card__header>tbody>tr>td {
            padding: 0.18cm 0.22cm 0.16cm;
            text-align: center;
            vertical-align: middle;
        }

        .report-card__header-inner {
            display: table;
            width: 100%;
            table-layout: fixed;
        }

        .report-card__logo-cell {
            display: table-cell;
            vertical-align: middle;
            width: 1.6cm;
            text-align: center;
            padding-right: 0.18cm;
        }

        .report-card__logo-cell img {
            width: 1.45cm !important;
            height: 1.45cm !important;
            max-width: 1.45cm !important;
            max-height: 1.45cm !important;
            object-fit: contain;
            display: block;
            margin: 0 auto;
        }

        .report-card__school-cell {
            display: table-cell;
            vertical-align: middle;
            text-align: center;
        }

        .report-card__school-cell.is-centered {
            text-align: center;
        }

        .report-card__school-name {
            font-family: 'Times New Roman', Times, serif;
            font-size: 24px;
            line-height: 1.15;
            font-weight: 800;
            color: #0d6e9a;
            text-transform: uppercase;
            letter-spacing: 0.45px;
        }

        .report-card__school-address {
            font-family: 'Times New Roman', Times, serif;
            margin-top: 0.05cm;
            font-size: 17px;
            font-weight: 600;
            font-style: italic;
            color: #3d8fad;
            line-height: 1.25;
        }

        /* ---------- Title ---------- */
        .report-card__title {
            font-family: 'Times New Roman', Times, serif;
            font-size: {{ min((float) $templateSettings->report_title_font_size + 1.5, 20) }}px;
            font-weight: 900;
            font-style: italic;
            color: #a56a91;
            text-transform: uppercase;
            margin: 0.28cm 0 0.2cm;
            text-align: center;
            letter-spacing: 0.7px;
            position: relative;
            padding-bottom: 0.12cm;
        }

        .report-card__title::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 34%;
            height: 2.5px;
            background: linear-gradient(90deg, transparent, #e91e8c, transparent);
            border-radius: 2px;
        }

        /* ---------- Details grid ---------- */
        .report-card__details-grid {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
            margin-top: 0.12cm;
        }

        .report-card__details-grid>tbody>tr>td {
            vertical-align: top;
            border: 0;
            padding: 0;
        }

        .report-card__details-main {
            width: 67%;
            padding-right: 0.3cm !important;
        }

        .report-card__details-scale {
            width: 33%;
            padding-left: 0.1cm !important;
        }

        .report-card__scale {
            width: 100%;
            border-collapse: collapse;
            font-size: 9px;
            table-layout: fixed;
            border-radius: 4px;
            overflow: hidden;
        }

        .report-card__scale th,
        .report-card__scale td {
            border: 1px solid #a8c8d8;
            padding: 0.055cm 0.06cm;
            text-align: center;
            line-height: 1.25;
            white-space: nowrap;
        }

        .report-card__scale thead th {
            background: linear-gradient(180deg, #d4eaf6 0%, #c5e2f2 100%);
            color: #1a5270;
            font-weight: 700;
        }

        .report-card__scale tbody tr:nth-child(even) {
            background: #f4fafd;
        }

        .report-card__scale th:nth-child(1),
        .report-card__scale td:nth-child(1) {
            width: 42%;
        }

        .report-card__scale th:nth-child(2),
        .report-card__scale td:nth-child(2) {
            width: 30%;
        }

        .report-card__scale th:nth-child(3),
        .report-card__scale td:nth-child(3) {
            width: 28%;
        }

        .report-card__section {
            margin-top: 0.24cm;
        }

        .report-card__details-grid .report-card__section {
            margin-top: 0;
        }

        .report-card__exam {
            font-size: 18px;
            font-weight: 900;
            color: #0d6e9a;
            margin-bottom: 0.20cm;
            letter-spacing: 1;
            display: flex;
            padding-bottom: 0.2cm;
            border-bottom: 2px solid #7ec8e8;
        }

        .report-card__student {
            border-collapse: collapse;
            font-size: 14px;
            font-weight: 700;
            margin-top: 0.2cm;
        }

        .report-card__student td {
            border: 0;
            padding: 0.055cm 0.1cm 0.055cm 0;
            white-space: nowrap;
        }

        .report-card__student td:first-child {
            font-weight: 800;
            width: 1.65cm;
            color: #34495e;
        }

        .report-card__student td:last-child {
            color: #1a5270;
            font-weight: 700;
        }

        /* ---------- Subjects table ---------- */
        .report-card__table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
            font-size: 12px;
            border-radius: 5px;
            overflow: hidden;
        }

        .report-card__table thead tr {
            background: linear-gradient(180deg, #d4eaf6 0%, #bfe0f2 100%);
            color: #000000;
        }

        .report-card__table tbody tr:nth-child(even) {
            background: #f5fafd;
        }

        .report-card__table th,
        .report-card__table td {
            border: 1px solid #a8c8d8;
            padding: 0.085cm 0.06cm;
            text-align: center;
            vertical-align: middle;
            line-height: 1.2;
            word-wrap: break-word;
        }

        .report-card__table th {
            font-family: 'Times New Roman', Times, serif;
            font-weight: 800;
            color: #000000;
            letter-spacing: 0.12px;
        }

        .report-card__table tbody td {
            color: #000000;
        }

        .report-card__table tbody td:first-child {
            font-weight: 600;
        }

        .report-card__table th:first-child,
        .report-card__table td:first-child {
            text-align: left;
            padding-left: 0.12cm;
        }

        /* ---------- Summary ---------- */
        .report-card__summary-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
            font-size: 13px;
            border-radius: 5px;
            overflow: hidden;
        }

        .report-card__summary-table th,
        .report-card__summary-table td {
            border: 1px solid #a8c8d8;
            padding: 0.11cm 0.06cm;
            text-align: center;
            line-height: 1.2;
        }

        .report-card__summary-table thead tr {
            background: linear-gradient(180deg, #d4eaf6 0%, #bfe0f2 100%);
            color: #000000;
        }

        .report-card__summary-table thead th {
            font-weight: 700;
        }

        .report-card__summary-table thead th:first-child {
            background: linear-gradient(180deg, #a8d5ec 0%, #8ec8e4 100%);
            color: #000000;
            font-weight: 800;
            letter-spacing: 0.04em;
        }

        .report-card__summary-table tbody td {
            font-weight: 700;
            color: #000000;
            background: #f0f8fc;
        }

        /* ---------- Remarks + Position ---------- */
        .report-card__remarks-row {
            width: 100%;
            table-layout: fixed;
            border-collapse: collapse;
            margin-top: 0.24cm;
        }

        .report-card__remarks-col {
            vertical-align: top;
            width: 70%;
            padding-right: 0.3cm;
            padding-left: 0;
        }

        .report-card__position-col {
            vertical-align: top;
            width: 30%;
            padding: 0;
        }

        .report-card__remarks-title {
            font-size: 15px;
            font-weight: 800;
            color: #0d6e9a;
            margin-bottom: 0.1cm;
            letter-spacing: 0.3px;
        }

        .report-card__remarks-list {
            list-style: none;
            margin: 0;
            padding: 0;
            font-size: 14px;
        }

        .report-card__remarks-list li {
            padding: 0.06cm 0;
            color: #7a8a9a;
            line-height: 1.35;
        }

        .report-card__remarks-list li.is-active {
            display: block !important;
            width: 100% !important;
            box-sizing: border-box;
            background-color: {{ $templateSettings->remarks_text_color ?? '#2e7d32' }} !important;
            color: #ffffff !important;
            padding: 0.05cm 0.18cm;
            border-radius: 14px;
            font-weight: 600;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.12);
        }

        .report-card__remarks-list li span.is-active {
            display: inline-block;
            background-color: {{ $templateSettings->remarks_text_color ?? '#2e7d32' }};
            color: #ffffff;
            padding: 0.05cm 0.18cm;
            border-radius: 14px;
            font-weight: 600;
        }

        .report-card__position {
            width: 100%;
            border-collapse: collapse;
            border-radius: 5px;
            overflow: hidden;
        }

        .report-card__position th,
        .report-card__position td {
            border: 1px solid #a8c8d8;
            padding: 0.14cm 0.1cm;
            text-align: center;
        }

        .report-card__position th {
            background: linear-gradient(180deg, #d4eaf6 0%, #bfe0f2 100%);
            color: #1a5270;
            font-size: 14px;
            letter-spacing: 0.1em;
            font-weight: 800;
        }

        .report-card__position td {
            color: #0d6e9a;
            font-size: 18px;
            font-weight: 800;
            background: #f0f8fc;
        }

        /* ---------- Comments ---------- */
        .report-card__comments {
            margin-top: 0.22cm;
            border: 1px solid #a8c8d8;
            background: linear-gradient(135deg, #eef7fc 0%, #e4f2fa 100%);
            border-radius: 6px;
            padding: 0.16cm 0.22cm;
            font-size: 14px;
            color: #2c3e50;
        }

        .report-card__comments ul {
            margin: 0;
            padding-left: 0.42cm;
        }

        .report-card__comments li {
            list-style-type: disc;
            line-height: 1.45;
        }

        .report-card__comments li+li {
            margin-top: 0.1cm;
        }

        /* ---------- Footer ---------- */
        .report-card__footer {
            margin-top: 2cm;
            width: 100%;
        }

        .report-card__signature-table {
            width: 100%;
            margin-top: 0.22cm;
            table-layout: fixed;
            border-collapse: collapse;
        }

        .report-card__signature-cell {
            width: 50%;
            vertical-align: bottom;
            padding: 0;
        }

        .report-card__signature-cell--right {
            text-align: right;
        }

        .report-card__signature {
            margin-top: 0;
            width: 2cm;
            border-top: 1.5px solid {{ $templateSettings->signature_line_color ?? '#5a7a8a' }};
            padding-top: 0.04cm;
        }

        .report-card__signature-line {
            display: none;
        }

        .report-card__signature-cell--right .report-card__signature-line {
            margin-left: auto;
        }

        .report-card__signature-cell--right .report-card__signature {
            margin-left: auto;
        }

        .report-card__signature div:last-child {
            font-size: 10.5px;
            font-weight: 600;
            color: #34495e;
            letter-spacing: 0.2px;
        }

        .report-card__published {
            font-weight: 700;
            font-style: italic;
            color: #3d5a6c;
            font-size: 10.5px;
            letter-spacing: 0.15px;
        }

        /* ---------- Green theme ---------- */
        .report-card {
            border-color: #b7cdb7;
            background: linear-gradient(180deg, #fbfefb 0%, #ffffff 20%);
        }

        .report-card__header {
            background: linear-gradient(135deg, #e6f2e6 0%, #d5e9d5 100%);
            box-shadow: 0 1px 0 rgba(76, 132, 76, 0.35);
        }

        .report-card__school-name,
        .report-card__remarks-title {
            color: #2e6f35;
        }

        .report-card__school-address {
            color: #56805a;
        }

        .report-card__title {
            color: #4f7d55;
        }

        .report-card__title::after {
            background: linear-gradient(90deg, transparent, #5b985f, transparent);
        }

        .report-card__scale th,
        .report-card__table th,
        .report-card__summary-table th,
        .report-card__position th {
            background: linear-gradient(180deg, #dceedd 0%, #c7e3c8 100%);
        }

        .report-card__scale th,
        .report-card__scale td,
        .report-card__table th,
        .report-card__table td,
        .report-card__summary-table th,
        .report-card__summary-table td,
        .report-card__position th,
        .report-card__position td {
            border-color: #9fbe9f;
        }

        .report-card__scale tbody tr:nth-child(even),
        .report-card__table tbody tr:nth-child(even) {
            background: #f4faf4;
        }

        .report-card__summary-table tbody td,
        .report-card__position td {
            background: #f0f8f0;
        }

        .report-card__comments {
            border-color: #9fbe9f;
            background: linear-gradient(135deg, #f0f9f0 0%, #e4f3e4 100%);
        }

        .report-card__exam {
            color: #000000 !important;
            border-bottom-color: #9fbe9f !important;
        }

        .report-card__scale,
        .report-card__scale th,
        .report-card__scale td,
        .report-card__scale thead th {
            color: #000000 !important;
        }

        .report-card__student td:first-child {
            color: #355e3b;
        }

        .report-card__student td:last-child {
            color: #000000;
        }

        .report-card__position th {
            color: #2e6f35;
        }

        .report-card__position td {
            color: #2e6f35;
        }

        .report-card__remarks-list li span.is-active {
            background-color: #4f7d55 !important;
        }

        /* Keep PDF print typography aligned with the A4 webview */
        .report-card {
            font-size: 14px;
        }

        .report-card__school-name {
            font-size: 23px !important;
        }

        .report-card__school-address {
            font-size: 15px !important;
        }

        .report-card__title {
            font-size: 19px !important;
        }

        .report-card__scale {
            font-size: 12px;
        }

        .report-card__exam {
            font-size: 18px;
        }

        .report-card__student {
            font-size: 16px;
        }

        .report-card__table {
            font-size: 14.5px;
        }

        .report-card__summary-table {
            font-size: 14.5px;
        }

        .report-card__remarks-title {
            font-size: 16px;
        }

        .report-card__remarks-list {
            font-size: 14.5px;
        }

        .report-card__position th {
            font-size: 13px;
        }

        .report-card__position td {
            font-size: 17px;
        }

        .report-card__comments {
            font-size: 15px;
        }

        /* Compact PDF typography for a balanced A4 layout. Header typography is preserved. */
        .report-card {
            font-size: 13px;
        }

        .report-card__school-name {
            font-size: 23px !important;
        }

        .report-card__school-address {
            font-size: 15px !important;
        }

        .report-card__title {
            font-size: 19px !important;
        }

        .report-card__scale {
            font-size: 11px;
        }

        .report-card__exam {
            font-size: 17px;
        }

        .report-card__student {
            font-size: 15px;
        }

        .report-card__table,
        .report-card__summary-table {
            font-size: 13.5px;
        }

        .report-card__remarks-title {
            font-size: 15px;
        }

        .report-card__remarks-list,
        .report-card__comments {
            font-size: 13.5px;
        }

        .report-card__position th {
            font-size: 12px;
        }

        .report-card__position td {
            font-size: 16px;
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

        <div class="report-card"
            style="border-color: #b8d4e8; page-break-after: {{ $loop->last ? 'auto' : 'always' }}; break-after: {{ $loop->last ? 'auto' : 'page' }};">

            <div class="report-card__content">
                {{-- Header with Logo + School Name --}}
                <table class="report-card__header">
                    <tr>
                        <td style="padding: 0.2cm 0.25cm;">
                            <table style="width:100%; border-collapse:collapse;">
                                <tr>
                                    @if($hasLogo)
                                        <td style="width:1.7cm; vertical-align:middle; padding-right:0.25cm;">
                                            <img src="{{ $logoPath }}"
                                                alt="School Logo"
                                                width="58"
                                                height="58"
                                                style="width:58px; height:58px; max-width:58px; max-height:58px; display:block;">
                                        </td>
                                        <td style="vertical-align:middle; text-align:center;">
                                            <div class="report-card__school-name">{{ $schoolName }}</div>
                                            <div class="report-card__school-address">{{ $schoolAddress }}</div>
                                        </td>
                                    @else
                                        <td style="vertical-align:middle; text-align:center;">
                                            <div class="report-card__school-name">{{ $schoolName }}</div>
                                            <div class="report-card__school-address">{{ $schoolAddress }}</div>
                                        </td>
                                    @endif
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>

                <div class="report-card__title">{{ $templateSettings->report_title_text }}</div>

                @if ($templateSettings->show_student_info)
                    <table class="report-card__details-grid">
                        <tr>
                            <td class="report-card__details-main">
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
                            </td>
                            @if ($templateSettings->show_grade_scale)
                                <td class="report-card__details-scale">
                                    <table class="report-card__scale">
                                            <thead>
                                                <tr>
                                                    <th>Range</th>
                                                    <th>Letter Grade</th>
                                                    <th>Point</th>
                                                </tr>
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
                                </td>
                            @endif
                        </tr>
                    </table>
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
                            @foreach ($subjectRows as $row)
                                @if (!empty($row['papers']))
                                    @foreach ($row['papers'] as $paperIndex => $paper)
                                        <tr>
                                            <td>{{ $paper['subject_name'] }}</td>
                                            <td>{{ number_format($paper['full_marks'], 0) }}</td>
                                            <td>{{ $paper['obtained'] ? number_format($paper['obtained'], 0) : '—' }}
                                            </td>
                                            <td>{{ number_format($paper['highest'], 0) }}</td>
                                            @if ($paperIndex === 0)
                                                <td rowspan="{{ count($row['papers']) }}">
                                                    {{ is_null($row['obtained']) ? '—' : number_format($row['obtained'], 0) }}
                                                </td>
                                                <td rowspan="{{ count($row['papers']) }}">{{ $row['grade'] }}</td>
                                                <td rowspan="{{ count($row['papers']) }}">
                                                    {{ number_format($row['gpa'], 1) }}</td>
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

                @if ($templateSettings->show_summary)
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
                                    <td>{{ number_format($summary['obtained'], 0) }} /
                                        {{ number_format($summary['percentage'], 2) }}%</td>
                                    <td>{{ number_format($summary['gpa'], 2) }}</td>
                                    <td>{{ $summary['grade'] }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                @endif

                @if ($templateSettings->show_remarks || !is_null($rank))
                    <table class="report-card__remarks-row">
                        <tr>
                        <td class="report-card__remarks-col">
                            @if ($templateSettings->show_remarks)
                                <div class="report-card__remarks-title">Remarks:</div>
                                <ul class="report-card__remarks-list">
                                    <li><span class="{{ $summary['gpa'] >= 4.0 ? 'is-active' : '' }}"
                                            style="{{ $summary['gpa'] >= 4.0 ? 'background-color:' . ($templateSettings->remarks_text_color ?? '#2e7d32') . ';color:#ffffff;display:inline-block;' : '' }}">(i)
                                            {{ $templateSettings->remark_excellent_text }}</span></li>
                                    <li><span
                                            class="{{ $summary['gpa'] >= 3.0 && $summary['gpa'] < 4.0 ? 'is-active' : '' }}"
                                            style="{{ $summary['gpa'] >= 3.0 && $summary['gpa'] < 4.0 ? 'background-color:' . ($templateSettings->remarks_text_color ?? '#2e7d32') . ';color:#ffffff;display:inline-block;' : '' }}">(ii)
                                            {{ $templateSettings->remark_good_text }}</span></li>
                                    <li><span
                                            class="{{ $summary['gpa'] >= 2.0 && $summary['gpa'] < 3.0 ? 'is-active' : '' }}"
                                            style="{{ $summary['gpa'] >= 2.0 && $summary['gpa'] < 3.0 ? 'background-color:' . ($templateSettings->remarks_text_color ?? '#2e7d32') . ';color:#ffffff;display:inline-block;' : '' }}">(iii)
                                            {{ $templateSettings->remark_satisfactory_text }}</span></li>
                                    <li><span class="{{ $summary['gpa'] < 2.0 ? 'is-active' : '' }}"
                                            style="{{ $summary['gpa'] < 2.0 ? 'background-color:' . ($templateSettings->remarks_text_color ?? '#2e7d32') . ';color:#ffffff;display:inline-block;' : '' }}">(iv)
                                            {{ $templateSettings->remark_improve_text }}</span></li>
                                </ul>
                            @endif
                        </td>
                        @if (!is_null($rank))
                            <td class="report-card__position-col">
                                <table class="report-card__position">
                                    <tr>
                                        <th>POSITION</th>
                                    </tr>
                                    <tr>
                                        <td>{{ $rank }}{{ $rank == 1 ? 'st' : ($rank == 2 ? 'nd' : ($rank == 3 ? 'rd' : 'th')) }}
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        @endif
                        </tr>
                    </table>
                @endif

                @if ($templateSettings->show_comments)
                    <div class="report-card__comments">
                        <ul>
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

                @if ($templateSettings->show_signature || $templateSettings->show_print_date)
                    <div class="report-card__footer">
                        @if ($templateSettings->show_signature)
                            <table class="report-card__signature-table">
                                <tr>
                                <td class="report-card__signature-cell">
                                    <div class="report-card__signature">
                                        <div class="report-card__signature-line"
                                            style="border-top-color: {{ $templateSettings->signature_line_color ?? '#5a7a8a' }};"></div>
                                        <div>Class Teacher</div>
                                    </div>
                                </td>
                                <td class="report-card__signature-cell report-card__signature-cell--right">
                                    <div class="report-card__signature">
                                        <div class="report-card__signature-line"
                                            style="border-top-color: {{ $templateSettings->signature_line_color ?? '#5a7a8a' }};"></div>
                                        <div>Principal</div>
                                    </div>
                                </td>
                                </tr>
                            </table>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    @endforeach
</body>

</html>
