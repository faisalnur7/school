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
    @endunless

    @if(!empty($rows))
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
        margin: 0 auto 0px;
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
