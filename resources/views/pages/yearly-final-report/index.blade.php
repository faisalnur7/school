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
    @unless($isPreview ?? false)
    <div class="card shadow-sm border-0 mb-4 no-print">
        <div class="card-header bg-gradient-primary text-white py-3">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                <h4 class="card-title mb-0 font-weight-bold text-white">
                    <i class="fas fa-file-alt mr-2"></i>Yearly Final Report
                </h4>
                <div class="d-flex gap-2 flex-wrap" role="group" aria-label="Yearly report actions">
                    <a href="{{ route('results.hub') }}" class="btn btn-light">
                        <i class="fas fa-arrow-left mr-1"></i>Back to Hub
                    </a>
                    <a href="{{ route('result.yearly-final-report.template-settings.edit') }}" class="btn btn-outline-light">
                        <i class="fas fa-sliders-h mr-1"></i>Template Settings
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm border-0 mb-4 no-print progress-report-toolbar yearly-report-toolbar">
        <div class="card-body p-3 yearly-report-toolbar__body">
            <form method="GET" action="{{ route('result.yearly-final-report.show') }}" class="progress-report-filter-form">
                <div class="progress-report-filter-row yearly-report-filter-row">
                    <div class="progress-report-filter-group yearly-report-filter-group">
                        <label for="yearlySessionSelect">Academic Session <span class="text-danger">*</span></label>
                        <select name="session_id" id="yearlySessionSelect" class="form-control progress-report-filter-select yearly-report-filter-select" required>
                            <option value="">— Select Session —</option>
                            @foreach($sessions as $sessionItem)
                                <option value="{{ $sessionItem->id }}" {{ (string) ($filters['session_id'] ?? '') === (string) $sessionItem->id ? 'selected' : '' }}>
                                    {{ $sessionItem->name_en ?? $sessionItem->name_bn }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="progress-report-filter-group yearly-report-filter-group">
                        <label for="yearlyClassSelect">Class <span class="text-danger">*</span></label>
                        <select name="class_id" id="classSelect" class="form-control progress-report-filter-select yearly-report-filter-select" required>
                            <option value="">— Select Class —</option>
                            @foreach($classes as $classItem)
                                <option value="{{ $classItem->id }}" {{ (string) ($filters['class_id'] ?? '') === (string) $classItem->id ? 'selected' : '' }}>
                                    {{ $classItem->name_en ?? $classItem->name_bn }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="progress-report-filter-group yearly-report-filter-group">
                        <label for="yearlySectionSelect">Section <span class="text-danger">*</span></label>
                        <select name="section_id" id="sectionSelect" class="form-control progress-report-filter-select yearly-report-filter-select" required>
                            <option value="">— Select Section —</option>
                            @foreach(App\Models\Section::where('school_class_id', $filters['class_id'] ?? null)->get() as $sectionItem)
                                <option value="{{ $sectionItem->id }}" {{ (string) ($filters['section_id'] ?? '') === (string) $sectionItem->id ? 'selected' : '' }}>
                                    {{ $sectionItem->name_en ?? $sectionItem->name_bn }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="progress-report-filter-group yearly-report-filter-group">
                        <label for="yearlyStudentInput">Student ID <small class="text-muted">(optional)</small></label>
                        <input type="text" name="student_id" id="yearlyStudentInput" class="form-control progress-report-filter-input yearly-report-filter-input"
                            value="{{ $filters['student_id'] ?? '' }}" placeholder="Leave blank for all students">
                    </div>

                    <div class="progress-report-filter-actions yearly-report-filter-actions">
                        <button type="submit" class="btn progress-report-action-btn progress-report-action-btn--primary yearly-report-action-btn yearly-report-action-btn--primary" title="View Report" aria-label="View Report">
                            <i class="fas fa-eye"></i>
                        </button>
                        @if(!empty($rows))
                            <button type="button" onclick="window.print()" class="btn btn-danger progress-report-action-btn yearly-report-action-btn" title="Print" aria-label="Print">
                                <i class="fas fa-file-pdf"></i>
                            </button>
                        @endif
                        <a href="{{ route('result.yearly-final-report.index') }}" class="btn progress-report-action-btn progress-report-action-btn--ghost yearly-report-action-btn yearly-report-action-btn--ghost" title="Reset" aria-label="Reset">
                            <i class="fas fa-undo-alt"></i>
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
    @endunless

    @if(count($rows ?? []) > 0)
@foreach($rows as $row)
    @include('pages.yearly-final-report._report-card', [
        'row' => $row,
        'highest' => $highest,
        'rows' => $rows,
        'schoolName' => $schoolName,
        'schoolAddress' => $schoolAddress,
        'logoPath' => $logoPath,
        'sessionLabel' => $sessionLabel,
        'classLabel' => $classLabel,
        'sectionLabel' => $sectionLabel,
        'columnWidths' => $columnWidths,
        'templateSettings' => $templateSettings,
        'pairWeights' => $pairWeights,
    ])
@endforeach
    @else
    @unless($isPreview ?? false)
    <div class="card card-body text-center text-muted py-5 no-print">
        <i class="fas fa-filter fa-3x mb-3 text-success"></i>
        <p class="mb-0">Choose the filters above to view the report.</p>
    </div>
    @endunless
    @endif
</div>
@endsection

@section('styles')
<style>
    .yearly-report-page .card-header {
        background: linear-gradient(90deg, #1d4ed8, #1e3a8a) !important;
    }

    .yearly-report-toolbar {
        background: #ffffff;
        border: 1px solid #e7e5e4;
        border-radius: 18px;
        box-shadow: 0 10px 30px rgba(15, 23, 42, 0.04);
    }

    .yearly-report-toolbar__body {
        background: #fff;
        border-bottom-left-radius: 18px;
        border-bottom-right-radius: 18px;
    }

    .yearly-report-filter-row {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr)) auto;
        gap: 0.75rem;
        align-items: end;
    }

    .yearly-report-filter-group label {
        display: block;
        margin-bottom: 0.35rem;
        font-size: 0.77rem;
        font-weight: 700;
        color: #6b7280;
    }

    .yearly-report-filter-select,
    .yearly-report-filter-input {
        width: 100%;
        min-height: 46px;
        border-radius: 12px;
        border: 1px solid #e5e7eb;
        background: #fff;
        color: #111827;
        font-size: 0.92rem;
        box-shadow: none;
    }

    .yearly-report-filter-select:focus,
    .yearly-report-filter-input:focus {
        border-color: #cbd5e1;
        box-shadow: 0 0 0 4px rgba(15, 23, 42, 0.05);
    }

    .yearly-report-filter-actions {
        display: inline-flex;
        align-items: center;
        justify-content: flex-end;
        gap: 0.65rem;
        flex-wrap: wrap;
    }

    .yearly-report-action-btn {
        min-width: 46px;
        min-height: 46px;
        border-radius: 12px;
    }

    .yearly-report-action-btn--primary {
        background: #111827;
        border-color: #111827;
        color: #fff;
    }

    .yearly-report-action-btn--primary:hover {
        background: #0f172a;
        border-color: #0f172a;
        color: #fff;
    }

    .yearly-report-action-btn--ghost {
        border: 1px solid #e5e7eb;
        background: #fff;
        color: #374151;
    }

    .yearly-report-action-btn--ghost:hover {
        background: #f8fafc;
        color: #111827;
    }

    @media (max-width: 1280px) {
        .yearly-report-filter-row {
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }

        .yearly-report-filter-actions {
            justify-content: flex-start;
        }
    }

    @media (max-width: 768px) {
        .yearly-report-filter-row {
            grid-template-columns: 1fr;
        }
    }

    @media print {
        .no-print {
            display: none !important;
        }

        .yearly-report-page {
            padding-top: 0 !important;
        }

        .report-card {
            margin-bottom: 4mm !important;
        }
    }

    .report-card {
        background: #fff;
        border: 1px solid {{ $templateSettings->table_border_color }};
        padding: 14px 16px 12px;
        position: relative;
        width: 100%;
        max-width: 28.1cm;
        min-height: 19.4cm;
        margin: 0 auto 1.25rem;
        break-after: page;
        page-break-after: always;
    }

    .report-card:last-child {
        break-after: auto;
        page-break-after: auto;
        margin-bottom: 0;
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
        opacity: {{ $templateSettings->watermark_opacity }};
    }

    .report-card__watermark img {
        width: {{ $templateSettings->watermark_scale }}%;
        max-width: {{ $templateSettings->watermark_scale }}%;
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
        color: {{ $templateSettings->school_name_color }};
        font-size: 24px;
        font-weight: 800;
        letter-spacing: .3px;
        line-height: 1.1;
        text-transform: uppercase;
    }

    .report-card__school-address {
        color: {{ $templateSettings->school_address_color }};
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
        border: 1px solid {{ $templateSettings->grade_border_color }};
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
        color: {{ $templateSettings->report_title_color }};
    }

    .report-card__meta {
        display: block;
        margin-bottom: 10px;
    }

    .report-card__annual {
        font-size: 18px;
        font-weight: 800;
        text-decoration: underline;
        color: {{ $templateSettings->annual_report_color }};
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
        border: 1px solid {{ $templateSettings->table_border_color }};
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

    .report-card__table thead th {
        background: {{ $templateSettings->table_header_bg_color }};
        color: {{ $templateSettings->table_header_text_color }};
    }

    .report-card__table tbody {
        background: {{ $templateSettings->table_body_bg_color }};
        color: {{ $templateSettings->table_body_text_color }};
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
        border: 1px solid {{ $templateSettings->position_border_color }};
    }

    .report-card__position-label {
        background: {{ $templateSettings->position_label_bg_color }};
        color: {{ $templateSettings->position_label_text_color }};
        padding: 2px 10px;
        font-weight: 800;
        font-size: 14px;
        border-right: 1px solid {{ $templateSettings->position_border_color }};
    }

    .report-card__position-value {
        padding: 2px 14px;
        font-size: 18px;
        font-weight: 800;
        min-width: 52px;
        text-align: center;
        color: {{ $templateSettings->position_value_text_color }};
    }

    .report-card__promo-box {
        background: {{ $templateSettings->promo_box_bg_color }};
        color: {{ $templateSettings->promo_box_text_color }};
        border: 1px solid {{ $templateSettings->promo_box_text_color }};
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
        color: {{ $templateSettings->remarks_title_color }};
    }

    .report-card__remarks-list {
        font-size: 12px;
        line-height: 1.55;
        color: {{ $templateSettings->remarks_text_color }};
    }

    .report-card__remarks-list .is-active {
        display: inline-block;
        background: {{ $templateSettings->position_label_bg_color }};
        padding: 0 4px;
        font-weight: 800;
    }

    .report-card__remarks-note {
        margin-top: 6px;
        font-size: 13px;
        font-weight: 700;
        color: {{ $templateSettings->remarks_note_color }};
    }

    .report-card__comments {
        margin-top: 10px;
        border: 1px solid {{ $templateSettings->comments_border_color }};
        min-height: 56px;
        padding: 10px 16px;
        font-size: 12px;
        color: {{ $templateSettings->comments_text_color }};
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
        border-top: 1px solid {{ $templateSettings->signature_line_color }};
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
            margin: {{ $templateSettings->margin_top_mm }}cm {{ $templateSettings->margin_right_mm }}cm {{ $templateSettings->margin_bottom_mm }}cm {{ $templateSettings->margin_left_mm }}cm;
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
            border: 1px solid {{ $templateSettings->table_border_color }};
            width: 28.1cm;
            max-width: 28.1cm;
            min-height: 19.4cm;
            margin: 0 auto;
            break-inside: avoid;
            page-break-inside: avoid;
        }

        .report-card__promo-box {
            color: {{ $templateSettings->promo_box_text_color }} !important;
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
