@extends('layouts.master')

@section('contents')
@php
    $school = \App\Models\SchoolSetting::current();
    $templateSettings = $templateSettings ?? \App\Models\YearlyFinalReportTemplateSetting::current();
    $sessionId = $filters['session_id'] ?? null;
    $classId = $filters['class_id'] ?? null;
    $sectionId = $filters['section_id'] ?? null;
    $session = !empty($sessionId) ? \App\Models\AcademicSession::find($sessionId) : null;
    $class = !empty($classId) ? \App\Models\SchoolClass::find($classId) : null;
    $section = !empty($sectionId) ? \App\Models\Section::find($sectionId) : null;
    $schoolName = $school->name ?? 'GREEN CHARTERED SCHOOL & COLLEGE';
    $schoolAddress = $school->address ?? 'CIP Tower, Hazari-digir-phar, Dohajari, Chandanish, Chattogram';
    $logoPath = !empty($school->logo) ? asset($school->logo) : null;
    $sessionLabel = $session?->name_en ?? $session?->name_bn ?? '—';
    $classLabel = $class?->name_en ?? $class?->name_bn ?? '—';
    $sectionLabel = $section?->name_en ?? $section?->name_bn ?? 'All';
    $columnWidths = $templateSettings->subject_column_widths ?? [];
@endphp

<div class="container-fluid px-3 py-3 yearly-report-page">
    <div class="card shadow-sm border-0 mb-4 no-print">
        <div class="card-header bg-gradient-primary text-white py-3">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                <h4 class="card-title mb-0 font-weight-bold text-white">
                    <i class="fas fa-file-alt mr-2"></i>Yearly Final Report
                </h4>
                <a href="{{ route('results.hub') }}" class="btn btn-light btn-sm">
                    <i class="fas fa-arrow-left mr-1"></i>Back to Hub
                </a>
                <a href="{{ route('result.yearly-final-report.template-settings.edit') }}" class="btn btn-outline-light btn-sm">
                    <i class="fas fa-sliders-h mr-1"></i>Template Settings
                </a>
            </div>
        </div>

        <div class="card-body p-3">
            <form method="POST" action="{{ route('result.yearly-final-report.show') }}">
                @csrf
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Academic Session</label>
                            <select name="session_id" class="form-control" required>
                                <option value="">Select Session</option>
                                @foreach($sessions as $sessionItem)
                                <option value="{{ $sessionItem->id }}" {{ ($filters['session_id'] ?? null) == $sessionItem->id ? 'selected' : '' }}>
                                    {{ $sessionItem->name_en ?? $sessionItem->name_bn }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Class</label>
                            <select name="class_id" id="classSelect" class="form-control" required>
                                <option value="">Select Class</option>
                                @foreach($classes as $classItem)
                                <option value="{{ $classItem->id }}" {{ ($filters['class_id'] ?? null) == $classItem->id ? 'selected' : '' }}>
                                    {{ $classItem->name_en ?? $classItem->name_bn }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Section</label>
                            <select name="section_id" class="form-control" id="sectionSelect">
                                <option value="">All Sections</option>
                                @foreach(App\Models\Section::where('school_class_id', $filters['class_id'] ?? null)->get() as $sectionItem)
                                <option value="{{ $sectionItem->id }}" {{ ($filters['section_id'] ?? null) == $sectionItem->id ? 'selected' : '' }}>
                                    {{ $sectionItem->name_en ?? $sectionItem->name_bn }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Student ID</label>
                            <input type="text" name="student_id" class="form-control"
                                value="{{ $filters['student_id'] ?? '' }}" placeholder="Optional">
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="fas fa-search mr-1"></i>Generate Report
                    </button>
                    @if(!empty($rows))
                    <button type="button" onclick="window.print()" class="btn btn-dark btn-sm">
                        <i class="fas fa-print mr-1"></i>Print
                    </button>
                    @endif
                </div>
            </form>
        </div>
    </div>

    @if(!empty($rows))
@foreach($rows as $row)
    @php
                $pair1 = data_get($row, 'totals.1', []);
                $pair2 = data_get($row, 'totals.2', []);
                $pair3 = data_get($row, 'totals.3', []);
                $tutorial1 = data_get($row, 'totals.1.tutorial', 0);
                $terminal1 = data_get($row, 'totals.1.terminal', 0);
                $total1 = data_get($row, 'totals.1.total', 0);
                $weight1 = data_get($row, 'totals.1.weighted', 0);
                $tutorial2 = data_get($row, 'totals.2.tutorial', 0);
                $terminal2 = data_get($row, 'totals.2.terminal', 0);
                $total2 = data_get($row, 'totals.2.total', 0);
                $weight2 = data_get($row, 'totals.2.weighted', 0);
                $tutorial3 = data_get($row, 'totals.3.tutorial', 0);
                $terminal3 = data_get($row, 'totals.3.terminal', 0);
                $total3 = data_get($row, 'totals.3.total', 0);
                $weight3 = data_get($row, 'totals.3.weighted', 0);
                $position = $row['position'] ?? '-';
                $grandTotal = $row['grand_total'] ?? 0;
                $highestTotal = $highest ?: 1;
                $rankRatio = $grandTotal / $highestTotal;
                if ($rankRatio >= 0.9) {
                    $remarkKey = 'excellent';
                    $remarkLabel = $templateSettings->remark_excellent_text;
                } elseif ($rankRatio >= 0.75) {
                    $remarkKey = 'good';
                    $remarkLabel = $templateSettings->remark_good_text;
                } elseif ($rankRatio >= 0.6) {
                    $remarkKey = 'satisfactory';
                    $remarkLabel = $templateSettings->remark_satisfactory_text;
                } else {
                    $remarkKey = 'improve';
                    $remarkLabel = $templateSettings->remark_improve_text;
                }
            @endphp
            <div class="report-card shadow-sm mb-4" style="border-color: {{ $templateSettings->table_border_color }};">
                @if($templateSettings->show_watermark && !empty($logoPath))
                    <div class="report-card__watermark">
                        <img src="{{ $logoPath }}" alt="Watermark" style="opacity: {{ $templateSettings->watermark_opacity }}; width: {{ $templateSettings->watermark_scale }}%;">
                    </div>
                @endif
                <div class="report-card__top">
                    <div class="report-card__identity">
                        @if(!empty($school->logo))
                            <div class="report-card__logo">
                                <img src="{{ asset($school->logo) }}" alt="{{ $schoolName }} logo">
                            </div>
                        @endif
                        <div class="report-card__school">
                            <div class="report-card__school-block">
                                <div class="report-card__school-name" style="font-size: {{ $templateSettings->school_name_font_size }}px; color: {{ $templateSettings->school_name_color }};">{{ $schoolName }}</div>
                            </div>
                            <div class="report-card__school-address" style="font-size: {{ $templateSettings->school_address_font_size }}px; color: {{ $templateSettings->school_address_color }};">{{ $schoolAddress }}</div>
                        </div>
                    </div>

                    @if($templateSettings->show_grade_scale)
                    <table class="report-card__grades" style="border-color: {{ $templateSettings->grade_border_color }};">
                        <thead style="background: {{ $templateSettings->table_header_bg_color }}; color: {{ $templateSettings->table_header_text_color }};">
                            <tr><th colspan="3">{{ $templateSettings->grade_scale_title }}</th></tr>
                        </thead>
                        <tbody>
                            <tr><td>0-32</td><td>F</td><td>0.0</td></tr>
                            <tr><td>33-39</td><td>D</td><td>1.0</td></tr>
                            <tr><td>40-49</td><td>C</td><td>2.0</td></tr>
                            <tr><td>50-59</td><td>B</td><td>3.0</td></tr>
                            <tr><td>60-69</td><td>A-</td><td>3.5</td></tr>
                            <tr><td>70-79</td><td>A</td><td>4.0</td></tr>
                            <tr><td>80-100</td><td>A+</td><td>5.0</td></tr>
                        </tbody>
                    </table>
                    @endif
                </div>

                <div class="report-card__title" style="font-size: {{ $templateSettings->report_title_font_size }}px; color: {{ $templateSettings->report_title_color }};">{{ $templateSettings->report_title_text }}</div>

                <div class="report-card__meta">
                    <div class="report-card__annual" style="color: {{ $templateSettings->annual_report_color }};">{{ $templateSettings->annual_report_label }}: {{ $sessionLabel }}</div>
                    @if($templateSettings->show_student_info)
                    <table class="report-card__student">
                        <tr>
                            <td>Name</td>
                            <td>:</td>
                            <td>{{ $row['student']->full_name_en ?? $row['student']->full_name_bn }}</td>
                        </tr>
                        <tr>
                            <td>Class</td>
                            <td>:</td>
                            <td>{{ $classLabel }}</td>
                        </tr>
                        <tr>
                            <td>ID</td>
                            <td>:</td>
                            <td>{{ $row['student']->student_cid ?? $row['student']->id }}</td>
                        </tr>
                        <tr>
                            <td>Section</td>
                            <td>:</td>
                            <td>{{ $sectionLabel }}</td>
                        </tr>
                    </table>
                    @endif
                </div>

                @if($templateSettings->show_table)
                <div class="report-card__table-wrap">
                    <table class="report-card__table">
                        <thead>
                            <tr class="group-row">
                                <th colspan="4">{{ $templateSettings->pair_heading_1 }}</th>
                                <th colspan="4">{{ $templateSettings->pair_heading_2 }}</th>
                                <th colspan="4">{{ $templateSettings->pair_heading_3 }}</th>
                                <th rowspan="2" style="width:{{ $columnWidths['grand_total'] ?? 8 }}%;">{{ $templateSettings->grand_total_label }}<br><span>(20%+20%+60%)</span></th>
                                <th rowspan="2" style="width:{{ $columnWidths['highest'] ?? 7 }}%;">{{ $templateSettings->highest_total_label }}</th>
                            </tr>
                            <tr class="sub-row">
                                <th style="width:{{ $columnWidths['pair1_tutorial'] ?? 8 }}%;">1<sup>st</sup><br>Tutorial</th>
                                <th style="width:{{ $columnWidths['pair1_terminal'] ?? 8 }}%;">1<sup>st</sup><br>Term</th>
                                <th style="width:{{ $columnWidths['pair1_total'] ?? 6 }}%;">Total</th>
                                <th style="width:{{ $columnWidths['pair1_weight'] ?? 6 }}%;">{{ data_get($pair1, 'weight', data_get($pairWeights, 1, 0)) }}%</th>
                                <th style="width:{{ $columnWidths['pair2_tutorial'] ?? 8 }}%;">2<sup>nd</sup><br>Tutorial</th>
                                <th style="width:{{ $columnWidths['pair2_terminal'] ?? 8 }}%;">2<sup>nd</sup><br>Term</th>
                                <th style="width:{{ $columnWidths['pair2_total'] ?? 6 }}%;">Total</th>
                                <th style="width:{{ $columnWidths['pair2_weight'] ?? 6 }}%;">{{ data_get($pair2, 'weight', data_get($pairWeights, 2, 0)) }}%</th>
                                <th style="width:{{ $columnWidths['pair3_tutorial'] ?? 8 }}%;">3<sup>rd</sup><br>Tutorial</th>
                                <th style="width:{{ $columnWidths['pair3_terminal'] ?? 8 }}%;">3<sup>rd</sup><br>Term</th>
                                <th style="width:{{ $columnWidths['pair3_total'] ?? 6 }}%;">Total</th>
                                <th style="width:{{ $columnWidths['pair3_weight'] ?? 6 }}%;">{{ data_get($pair3, 'weight', data_get($pairWeights, 3, 0)) }}%</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>{{ number_format($tutorial1, 0) }}</td>
                                <td>{{ number_format($terminal1, 0) }}</td>
                                <td>{{ number_format($total1, 0) }}</td>
                                <td>{{ number_format($weight1, 0) }}</td>
                                <td>{{ number_format($tutorial2, 0) }}</td>
                                <td>{{ number_format($terminal2, 0) }}</td>
                                <td>{{ number_format($total2, 0) }}</td>
                                <td>{{ number_format($weight2, 0) }}</td>
                                <td>{{ number_format($tutorial3, 0) }}</td>
                                <td>{{ number_format($terminal3, 0) }}</td>
                                <td>{{ number_format($total3, 0) }}</td>
                                <td>{{ number_format($weight3, 0) }}</td>
                                <td class="grand-total">{{ number_format($grandTotal, 2) }}</td>
                                <td>{{ number_format($highest, 2) }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                @endif

                @if($templateSettings->show_summary)
                <div class="report-card__summary">
                    <div class="report-card__position-box">
                        <div class="report-card__position-label">{{ $templateSettings->position_label_text }}</div>
                        <div class="report-card__position-value">{{ $position }}</div>
                    </div>
                    <div class="report-card__promo-box">{{ $templateSettings->promoted_text }}</div>
                </div>
                @endif

                @if($templateSettings->show_remarks)
                <div class="report-card__remarks">
                    <div class="report-card__remarks-title">REMARKS:</div>
                    <div class="report-card__remarks-list">
                        <div class="{{ $remarkKey === 'excellent' ? 'is-active' : '' }}">(i) {{ $templateSettings->remark_excellent_text }}</div>
                        <div class="{{ $remarkKey === 'good' ? 'is-active' : '' }}">(ii) {{ $templateSettings->remark_good_text }}</div>
                        <div class="{{ $remarkKey === 'satisfactory' ? 'is-active' : '' }}">(iii) {{ $templateSettings->remark_satisfactory_text }}</div>
                        <div class="{{ $remarkKey === 'improve' ? 'is-active' : '' }}">(iv) {{ $templateSettings->remark_improve_text }}</div>
                    </div>
                    <div class="report-card__remarks-note">{{ $remarkLabel }}</div>
                </div>
                @endif

                @if($templateSettings->show_comments)
                <div class="report-card__comments" style="border-color: {{ $templateSettings->comments_border_color }}; color: {{ $templateSettings->comments_text_color }};">
                    @php
                        $commentLine1 = match ($remarkKey) {
                            'excellent' => $templateSettings->comments_excellent_text,
                            'good' => $templateSettings->comments_good_text,
                            default => $templateSettings->comments_default_text,
                        };
                    @endphp
                    <ul>
                        <li>{{ $commentLine1 }}</li>
                        <li>{{ $row['student']->full_name_en ?? $row['student']->full_name_bn }} ranked {{ $position }} out of {{ count($rows) }} students.</li>
                        <li>Grand total: {{ number_format($grandTotal, 2) }} out of {{ number_format($highest, 2) }} highest.</li>
                    </ul>
                </div>
                @endif

                @if($templateSettings->show_signature || $templateSettings->show_print_date)
                <div class="report-card__footer">
                    <div class="report-card__published">
                        @if($templateSettings->show_print_date)
                            Published Date: {{ now()->format('d-m-Y') }}
                        @endif
                    </div>
                    @if($templateSettings->show_signature)
                    <div class="report-card__signatures">
                        <div class="report-card__signature">
                            <div class="report-card__signature-line" style="border-top-color: {{ $templateSettings->signature_line_color }};"></div>
                            <div>{{ $templateSettings->class_teacher_label }}</div>
                        </div>
                        <div class="report-card__signature report-card__signature--principal">
                            <div class="report-card__signature-line" style="border-top-color: {{ $templateSettings->signature_line_color }};"></div>
                            <div>{{ $templateSettings->principal_label }}</div>
                        </div>
                    </div>
                    @endif
                </div>
                @endif
            </div>
        @endforeach
    @endif
</div>
@endsection

@section('styles')
<style>
    .yearly-report-page .card-header {
        background: linear-gradient(90deg, #1d4ed8, #1e3a8a) !important;
    }

    .report-card {
        background: #fff;
        border: 1px solid #d1d5db;
        padding: 14px 16px 12px;
        position: relative;
        width: 100%;
        max-width: 281mm;
        min-height: 194mm;
        margin: 0 auto 18px;
        break-after: page;
        page-break-after: always;
    }

    .report-card:last-child {
        break-after: auto;
        page-break-after: auto;
    }

    .report-card__top {
        display: grid;
        grid-template-columns: 1fr auto 1fr;
        gap: 16px;
        align-items: flex-start;
        position: relative;
        z-index: 1;
    }

    .report-card__watermark {
        position: absolute;
        inset: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        pointer-events: none;
        z-index: 0;
        opacity: 0.14;
    }

    .report-card__watermark img {
        width: 680px;
        max-width: 96%;
        max-height: 96%;
        object-fit: contain;
        filter: grayscale(100%);
    }

    .report-card__identity {
        display: flex;
        align-items: center;
        gap: 12px;
        min-width: 0;
        text-align: center;
        justify-self: center;
        grid-column: 2;
    }

    .report-card__logo {
        width: 62px;
        height: 62px;
        border: 1px solid #cbd5e1;
        border-radius: 8px;
        overflow: hidden;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #fff;
        flex-shrink: 0;
    }

    .report-card__logo img {
        max-width: 100%;
        max-height: 100%;
        object-fit: contain;
    }

    .report-card__school {
        min-width: 0;
        text-align: center;
    }

    .report-card__school-block {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
    }

    .report-card__school-name {
        color: #5b8f42;
        font-size: 24px;
        font-weight: 800;
        letter-spacing: .3px;
        line-height: 1.1;
        text-transform: uppercase;
    }

    .report-card__school-address {
        color: #5b8f42;
        font-size: 12px;
        font-weight: 700;
        text-align: center;
    }

    .report-card__grades {
        width: 150px;
        border-collapse: collapse;
        font-size: 10px;
        flex-shrink: 0;
        justify-self: end;
        grid-column: 3;
    }

    .report-card__grades th,
    .report-card__grades td {
        border: 1px solid #7b7b7b;
        padding: 2px 4px;
        text-align: center;
        line-height: 1.1;
    }

    .report-card__grades th {
        font-weight: 700;
    }

    .report-card__title {
        text-align: center;
        font-size: 22px;
        font-weight: 800;
        font-style: italic;
        letter-spacing: .5px;
        margin: 6px 0 8px;
        color: #2f2f2f;
    }

    .report-card__meta {
        display: block;
        margin-bottom: 10px;
    }

    .report-card__annual {
        font-size: 18px;
        font-weight: 800;
        text-decoration: underline;
        color: #333;
        margin-bottom: 12px;
    }

    .report-card__student {
        border-collapse: collapse;
        font-size: 11px;
        margin-right: auto;
    }

    .report-card__student td {
        padding: 2px 7px 2px 0;
        vertical-align: top;
        white-space: nowrap;
    }

    .report-card__student td:first-child {
        font-weight: 700;
        width: 60px;
    }

    .report-card__table-wrap {
        margin-top: 6px;
    }

    .report-card__table {
        width: 100%;
        border-collapse: collapse;
        table-layout: fixed;
        font-size: 11px;
    }

    .report-card__table th,
    .report-card__table td {
        border: 1px solid #7b7b7b;
        padding: 5px 4px;
        text-align: center;
        vertical-align: middle;
        line-height: 1.1;
    }

    .report-card__table thead th {
        font-weight: 700;
    }

    .report-card__table .group-row th {
        font-size: 14px;
        padding: 6px 4px;
    }

    .report-card__table .group-row th span {
        font-size: 11px;
    }

    .report-card__table .sub-row th {
        font-size: 11px;
    }

    .report-card__table .grand-total {
        font-size: 16px;
        font-weight: 800;
    }

    .report-card__summary {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 14px;
        margin-top: 10px;
    }

    .report-card__position-box {
        display: inline-flex;
        align-items: center;
        border: 1px solid #8ca06e;
    }

    .report-card__position-label {
        background: #9ccf63;
        color: #1f3a1d;
        padding: 2px 10px;
        font-weight: 800;
        font-size: 14px;
        border-right: 1px solid #8ca06e;
    }

    .report-card__position-value {
        padding: 2px 14px;
        font-size: 18px;
        font-weight: 800;
        min-width: 52px;
        text-align: center;
        color: #243524;
    }

    .report-card__promo-box {
        background: #7fbf3a;
        color: #17310e;
        border: 1px solid #557f27;
        padding: 4px 28px;
        font-size: 22px;
        font-weight: 800;
        align-self: flex-end;
        margin-left: auto;
    }

    .report-card__remarks {
        margin-top: 10px;
        display: table;
        width: 100%;
        border-collapse: collapse;
    }

    .report-card__remarks > div {
        display: table-cell;
        vertical-align: top;
    }

    .report-card__remarks > div:first-child {
        width: 150px;
        padding-right: 12px;
    }

    .report-card__remarks-title {
        font-size: 15px;
        font-weight: 800;
        text-decoration: underline;
        color: #444;
    }

    .report-card__remarks-list {
        font-size: 12px;
        line-height: 1.55;
        color: #3f3f3f;
    }

    .report-card__remarks-list .is-active {
        display: inline-block;
        background: #9ccf63;
        padding: 0 4px;
        font-weight: 800;
    }

    .report-card__remarks-note {
        margin-top: 6px;
        font-size: 13px;
        font-weight: 700;
        color: #1f4d1a;
    }

    .report-card__comments {
        margin-top: 10px;
        border: 1px solid #a3a3a3;
        min-height: 56px;
        padding: 10px 16px;
        font-size: 12px;
        color: #333;
    }

    .report-card__comments ul {
        margin: 0;
        padding-left: 20px;
    }

    .report-card__comments li {
        margin-bottom: 5px;
    }

    .report-card__footer {
        margin-top: 40px;
        display: flex;
        justify-content: space-between;
        align-items: flex-end;
        gap: 20px;
    }

    .report-card__published {
        font-size: 14px;
        font-style: italic;
        color: #333;
    }

    .report-card__signatures {
        display: flex;
        gap: 44px;
        align-items: flex-end;
        padding-top: 30px;
    }

    .report-card__signature {
        text-align: center;
        min-width: 110px;
        font-size: 12px;
        color: #333;
    }

    .report-card__signature-line {
        border-top: 1px solid #111;
        width: 110px;
        margin-bottom: 4px;
    }

    .report-card__signature--principal {
        min-width: 120px;
    }

    .report-card__signature--principal .report-card__signature-line {
        width: 120px;
    }

    @media print {
        @page {
            size: A4 landscape;
            margin: 8mm;
        }

        body {
            background: #fff !important;
        }

        .no-print,
        .main-sidebar,
        .main-header,
        .content-header,
        .main-footer {
            display: none !important;
        }

        .content-wrapper {
            margin-left: 0 !important;
        }

        .report-card {
            box-shadow: none !important;
            border: 1px solid #000;
            width: 281mm;
            max-width: 281mm;
            min-height: 194mm;
            margin: 0 auto;
            break-inside: avoid;
            page-break-inside: avoid;
        }

        .report-card__promo-box {
            color: #17310e !important;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
    }

    .yearly-report-page,
    .yearly-report-page .report-card,
    .yearly-report-page .btn,
    .yearly-report-page label,
    .yearly-report-page input,
    .yearly-report-page select,
    .yearly-report-page table {
        font-family: "Aptos", "Segoe UI", "Helvetica Neue", Arial, sans-serif;
    }
</style>
@endsection

@section('scripts')
    @include('scripts.common.load_academic_information')
    <script>
        const sectionSelect = document.getElementById('sectionSelect');
        document.getElementById('classSelect').addEventListener('change', function() {
            const classId = this.value;
            sectionSelect.innerHTML = '<option value="">All Sections</option>';
            if (!classId) return;
            fetch(`{{ route('load_section_groups') }}?school_class_id=${classId}`)
                .then(async r => {
                    if (!r.ok) throw new Error('Failed to load sections');
                    return r.json();
                })
                .then(data => {
                    const sections = Array.isArray(data?.sections) ? data.sections : [];
                    sections.forEach(s => {
                        sectionSelect.insertAdjacentHTML('beforeend',
                            `<option value="${s.id}">${s.name_en}</option>`);
                    });
                })
                .catch(() => {
                    sectionSelect.innerHTML = '<option value="">All Sections</option>';
                });
        });
    </script>
@endsection
