<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Terminal Progress Report</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 8mm;
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
            min-height: 280mm;
            padding: 10mm 10mm 8mm;
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
            opacity: 0.08;
            pointer-events: none;
            z-index: 0;
        }

        .report-card__watermark img {
            width: 170mm;
            max-width: 82%;
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
            padding-right: 5mm;
        }

        .report-card__scale-cell { width: 26%; }

        .report-card__identity-wrap {
            display: table;
            width: 100%;
            table-layout: fixed;
        }

        .report-card__logo-cell {
            display: table-cell;
            width: 18mm;
            vertical-align: middle;
        }

        .report-card__logo {
            width: 16mm;
            height: 16mm;
            border: 1px solid #cbd5e1;
            border-radius: 2mm;
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
            padding-left: 3mm;
            min-width: 0;
        }

        .report-card__school-name {
            font-size: 16px;
            line-height: 1.08;
            font-weight: 800;
            color: #5b8f42;
            text-transform: uppercase;
            letter-spacing: .2px;
            overflow-wrap: anywhere;
            word-break: break-word;
        }

        .report-card__school-address {
            margin-top: 1.5mm;
            font-size: 8.5px;
            font-weight: 700;
            color: #5b8f42;
            line-height: 1.25;
        }

        .report-card__title {
            font-size: 17px;
            font-weight: 800;
            font-style: italic;
            color: #d97706;
            text-transform: uppercase;
            margin: 2mm 0 3mm;
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
            border: 1px solid #555;
            padding: 1mm 1.5mm;
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
            margin-top: 4mm;
        }

        .report-card__exam {
            font-size: 14px;
            font-weight: 800;
            text-decoration: underline;
            margin-bottom: 3mm;
        }

        .report-card__student {
            border-collapse: collapse;
            font-size: 10px;
        }

        .report-card__student td {
            border: 0;
            padding: 1mm 2mm 1mm 0;
            white-space: nowrap;
        }

        .report-card__student td:first-child {
            font-weight: 700;
            width: 16mm;
        }

        .report-card__table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
            font-size: 9px;
        }

        .report-card__table th,
        .report-card__table td {
            border: 1px solid #555;
            padding: 1.2mm 1mm;
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
            border: 1px solid #555;
            padding: 1.2mm 1mm;
            text-align: center;
            line-height: 1.1;
        }

        .report-card__remarks {
            margin-top: 4mm;
            font-size: 9.5px;
        }

        .report-card__remarks-title {
            font-size: 12px;
            font-weight: 800;
            text-decoration: underline;
            margin-bottom: 1.5mm;
        }

        .report-card__comments {
            margin-top: 4mm;
            border: 1px solid #555;
            padding: 3mm;
            font-size: 9px;
        }

        .report-card__comments ul {
            margin: 0;
            padding-left: 4.5mm;
        }

        .report-card__comments li + li {
            margin-top: 1.5mm;
        }

        .report-card__footer {
            margin-top: 7mm;
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
            margin-top: 16mm;
        }

        .report-card__signature-line {
            border-top: 1px solid #111;
            width: 40mm;
            margin-bottom: 2mm;
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

    <div class="report-card">
        @if($hasLogo)
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
                </div>
            </div>

            <div class="report-card__title">Progress Report</div>

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

            <div class="report-card__section">
                <table class="report-card__table">
                    <thead>
                        <tr>
                            <th style="width: 30%;">Subjects</th>
                            <th style="width: 10%;">Full Marks</th>
                            <th style="width: 12%;">Obtained Marks</th>
                            <th style="width: 12%;">Highest Marks</th>
                            <th style="width: 12%;">Total Marks</th>
                            <th style="width: 12%;">Letter Grade</th>
                            <th style="width: 12%;">Grade Point</th>
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

            <div class="report-card__remarks">
                <div class="report-card__remarks-title">Remarks:</div>
                @if($summary['gpa'] >= 4.0)
                    <div>(i) Excellent</div>
                @elseif($summary['gpa'] >= 3.0)
                    <div>(ii) Good</div>
                @elseif($summary['gpa'] >= 2.0)
                    <div>(iii) Satisfactory</div>
                @else
                    <div>(iv) Need to be improved</div>
                @endif
            </div>

            <div class="report-card__comments">
                <ul>
                    <li>{{ $student->full_name_en }} was present {{ $attendancePresent }} days out of {{ $attendanceTotal }} days.</li>
                    @if($summary['gpa'] >= 4.0)
                        <li>Excellent results! You faithfully perform classroom tasks.</li>
                    @elseif($summary['gpa'] >= 3.0)
                        <li>Good results! Keep up the good work.</li>
                    @else
                        <li>Need to improve performance.</li>
                    @endif
                </ul>
            </div>

            <div class="report-card__footer">
                <div class="report-card__footer-left">
                    <div class="report-card__published">Published Date: {{ now()->format('d-m-Y') }}</div>
                    <div class="report-card__signature">
                        <div class="report-card__signature-line"></div>
                        <div>Class Teacher</div>
                    </div>
                </div>
                <div class="report-card__footer-right">
                    <div class="report-card__signature">
                        <div class="report-card__signature-line" style="margin-left:auto;"></div>
                        <div>Principal</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endforeach
</body>
</html>
