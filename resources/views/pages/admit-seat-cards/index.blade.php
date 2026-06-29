@extends('layouts.master')

@section('contents')
<div class="container-fluid admit-seat-cards-page">
    <div class="card card-outline no-print result-filter-panel admit-seat-cards-filter-panel">
        <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2 admit-seat-cards-filter-header">
            <div class="flex flex-col">
                <h3 class="card-title mb-0 text-dark">
                    <i class="fas fa-filter mr-2 text-info"></i>Filter Options
                </h3>
                <small class="text-muted">Generate admit or seat cards by year, class, exam, and layout.</small>
            </div>
        </div>
        <div class="card-body admit-seat-cards-filter-body">
            <form method="GET" action="{{ route('results.admit-seat-cards.index') }}" id="filterForm" class="admit-seat-cards-filter-form">
                <div class="admit-seat-cards-filter-grid">
                    <div class="admit-seat-cards-filter-group">
                        <label class="font-weight-bold admit-seat-cards-filter-label">Academic Year <span class="text-danger">*</span></label>
                        <select name="session_id" class="form-control form-control-sm admit-seat-cards-filter-control">
                            <option value="">— Select Year —</option>
                            @foreach($sessions as $s)
                                <option value="{{ $s->id }}" {{ request('session_id') == $s->id ? 'selected' : '' }}>{{ $s->name_en }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="admit-seat-cards-filter-group">
                        <label class="font-weight-bold admit-seat-cards-filter-label">Class</label>
                        <select name="class_id" id="classSelect" class="form-control form-control-sm admit-seat-cards-filter-control">
                            <option value="">All Classes</option>
                            @foreach($classes as $c)
                                <option value="{{ $c->id }}" {{ request('class_id') == $c->id ? 'selected' : '' }}>{{ $c->name_en }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="admit-seat-cards-filter-group">
                        <label class="font-weight-bold admit-seat-cards-filter-label">Section</label>
                        <select name="section_id" id="sectionSelect" class="form-control form-control-sm admit-seat-cards-filter-control">
                            <option value="">All Sections</option>
                            @foreach($sections as $sec)
                                <option value="{{ $sec->id }}" {{ request('section_id') == $sec->id ? 'selected' : '' }}>{{ $sec->name_en }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="admit-seat-cards-filter-group">
                        <label class="font-weight-bold admit-seat-cards-filter-label">Group</label>
                        <select name="group_id" class="form-control form-control-sm admit-seat-cards-filter-control">
                            <option value="">All Groups</option>
                            @foreach($groups as $g)
                                <option value="{{ $g->id }}" {{ request('group_id') == $g->id ? 'selected' : '' }}>{{ $g->name_en }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="admit-seat-cards-filter-group">
                        <label class="font-weight-bold admit-seat-cards-filter-label">Exam Type</label>
                        <select name="exam_type" class="form-control form-control-sm admit-seat-cards-filter-control" id="examTypeSelect">
                            <option value="">All Types</option>
                            <option value="tutorial" {{ ($examType ?? '') === 'tutorial' ? 'selected' : '' }}>Tutorial Exam</option>
                            <option value="term" {{ ($examType ?? '') === 'term' ? 'selected' : '' }}>Terminal Exam</option>
                        </select>
                    </div>
                    <div class="admit-seat-cards-filter-group">
                        <label class="font-weight-bold admit-seat-cards-filter-label">Exam Name</label>
                        <select name="exam_id" class="form-control form-control-sm admit-seat-cards-filter-control" id="examSelect">
                            <option value="">All Exams</option>
                            @foreach($exams as $exam)
                                <option value="{{ $exam->id }}" {{ request('exam_id') == $exam->id ? 'selected' : '' }}>
                                    {{ $exam->name }} ({{ $exam->type_label }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="admit-seat-cards-filter-group">
                        <label class="font-weight-bold admit-seat-cards-filter-label">Card Type</label>
                        <select name="card_type" class="form-control form-control-sm admit-seat-cards-filter-control">
                            <option value="admit_card" {{ ($cardType ?? 'admit_card') === 'admit_card' ? 'selected' : '' }}>Admit Card</option>
                            <option value="seat_card" {{ ($cardType ?? 'admit_card') === 'seat_card' ? 'selected' : '' }}>Seat Card</option>
                        </select>
                    </div>
                    <div class="admit-seat-cards-filter-group">
                        <label class="font-weight-bold admit-seat-cards-filter-label">Student ID</label>
                        <input
                            type="text"
                            name="student_cid"
                            class="form-control form-control-sm admit-seat-cards-filter-control"
                            value="{{ request('student_cid') }}"
                            placeholder="Enter Student ID"
                            autocomplete="off">
                    </div>
                    <div class="admit-seat-cards-filter-actions">
                        <button type="button" class="btn btn-outline-primary btn-sm result-filter-icon-btn" data-toggle="modal" data-target="#cardSettingsModal" title="Card settings" aria-label="Card settings">
                            <i class="fas fa-sliders-h"></i>
                        </button>
                        <button type="submit" class="btn btn-dark btn-sm result-filter-icon-btn" title="Generate" aria-label="Generate">
                            <i class="fas fa-id-card"></i>
                        </button>
                        <a href="{{ route('results.admit-seat-cards.index') }}" class="btn btn-outline-secondary btn-sm result-filter-icon-btn" title="Reset" aria-label="Reset">
                            <i class="fas fa-times"></i>
                        </a>
                        @if($students->isNotEmpty())
                            <button type="button" class="btn btn-success btn-sm result-filter-icon-btn" onclick="window.print()" title="Print" aria-label="Print">
                                <i class="fas fa-print"></i>
                            </button>
                            <a href="{{ route('results.admit-seat-cards.pdf', request()->query()) }}" class="btn btn-danger btn-sm result-filter-icon-btn" target="_blank" title="PDF" aria-label="Download PDF">
                                <i class="fas fa-file-pdf"></i>
                            </a>
                        @endif
                    </div>
                </div>
            </form>
        </div>
    </div>

    @if(!request('session_id') && !request('student_cid'))
        <div class="text-center py-5 text-muted no-print">
            <i class="fas fa-id-card fa-3x mb-3 d-block" style="opacity:.3"></i>
            <p class="mb-1">Select Academic Year or enter a Student ID to generate admit or seat cards.</p>
        </div>
    @elseif($students->isEmpty())
        <div class="text-center py-5 text-muted no-print">
            <i class="fas fa-inbox fa-2x mb-2 d-block" style="opacity:.3"></i>
            <p>No students found for the selected filters.</p>
        </div>
    @else
        <div class="no-print mb-3 d-flex align-items-center" style="gap:8px; flex-wrap: wrap;">
            <span class="badge badge-light border px-3 py-2" style="font-size:12px">{{ $students->count() }} Students</span>
            <span class="badge badge-light border px-3 py-2" style="font-size:12px">
                {{ $layout['cardsPerPage'] ?? 8 }} cards/page
            </span>
            <span class="badge badge-light border px-3 py-2" style="font-size:12px">
                {{ $layout['cardsPerRow'] ?? 2 }} cards/row
            </span>
            <span class="badge badge-light border px-3 py-2" style="font-size:12px">
                {{ number_format($layout['cardWidthMm'] ?? 0, 1) }}mm × {{ number_format($layout['cardHeightMm'] ?? 0, 1) }}mm
            </span>
            <span class="badge badge-light border px-3 py-2" style="font-size:12px">
                {{ number_format($layout['gridGapMm'] ?? 0, 1) }}mm gap
            </span>
        </div>

        @include('pages.admit-seat-cards._cards', [
            'students' => $students,
            'setting' => $setting,
            'cardSettings' => $cardSettings ?? null,
            'renderForPdf' => false,
            'cardType' => $cardType ?? 'admit_card',
            'examType' => $examType ?? null,
            'selectedExam' => $selectedExam ?? null,
            'layout' => $layout ?? [],
        ])
    @endif
</div>

<div class="modal fade" id="cardSettingsModal" tabindex="-1" role="dialog" aria-labelledby="cardSettingsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered card-settings-modal-dialog" role="document">
        <div class="modal-content card-settings-modal-content">
            <form method="POST" action="{{ route('results.admit-seat-cards.settings') }}" enctype="multipart/form-data">
                @csrf
                <div class="modal-header card-settings-modal-header">
                    <div>
                        <h5 class="modal-title mb-1" id="cardSettingsModalLabel">Card Settings</h5>
                        <small class="text-muted d-block" id="cardSettingsModalTypeLabel">{{ (old('card_type', $cardType ?? 'admit_card') === 'seat_card') ? 'Seat Card Settings' : 'Admit Card Settings' }}</small>
                        <small class="text-muted d-block">Save a single layout profile for search, print, and PDF output.</small>
                    </div>
                    <button type="button" class="close card-settings-modal-close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body card-settings-modal-body">
                    <input type="hidden" name="card_type" value="{{ old('card_type', $cardType ?? 'admit_card') }}">
                    @php
                        $selectedColorType = old('card_color_type', $cardSettings?->card_color_type ?? 'gradient');
                        $selectedTransparent = old('card_is_transparent', $cardSettings?->card_is_transparent ?? false);
                        $resolveLogoUrl = function (?string $path) {
                            if (!$path) {
                                return null;
                            }

                            return file_exists(public_path($path)) ? asset($path) : null;
                        };
                        $schoolLogoUrl = $resolveLogoUrl($setting?->logo ?? null);
                        $currentCardLogoUrl = $resolveLogoUrl($cardSettings?->card_logo ?? null) ?: $schoolLogoUrl;
                    @endphp
                    <div class="row">
                        <div class="col-12 col-md-4 mb-3">
                            <div class="admit-seat-cards-filter-group admit-seat-cards-modal-field mb-0">
                                <label class="font-weight-bold admit-seat-cards-filter-label">Cards / Page</label>
                                <input
                                    type="number"
                                    name="cards_per_page"
                                    class="form-control form-control-sm admit-seat-cards-filter-control"
                                    min="1"
                                    max="12"
                                    value="{{ old('cards_per_page', $cardSettings?->cards_per_page ?? 8) }}">
                            </div>
                        </div>
                        <div class="col-12 col-md-4 mb-3">
                            <div class="admit-seat-cards-filter-group admit-seat-cards-modal-field mb-0">
                                <label class="font-weight-bold admit-seat-cards-filter-label">Cards / Row</label>
                                <input
                                    type="number"
                                    name="cards_per_row"
                                    class="form-control form-control-sm admit-seat-cards-filter-control"
                                    min="1"
                                    max="10"
                                    value="{{ old('cards_per_row', $cardSettings?->cards_per_row ?? 2) }}">
                            </div>
                        </div>
                        <div class="col-12 col-md-4 mb-3">
                            <div class="admit-seat-cards-filter-group admit-seat-cards-modal-field mb-0">
                                <label class="font-weight-bold admit-seat-cards-filter-label">Card Width</label>
                                <input
                                    type="number"
                                    name="card_width_value"
                                    class="form-control form-control-sm admit-seat-cards-filter-control"
                                    min="0.1"
                                    step="0.1"
                                    value="{{ old('card_width_value', $cardSettings?->card_width_value ?? 9.4) }}">
                            </div>
                        </div>
                        <div class="col-12 col-md-4 mb-3">
                            <div class="admit-seat-cards-filter-group admit-seat-cards-modal-field mb-0">
                                <label class="font-weight-bold admit-seat-cards-filter-label">Card Height</label>
                                <input
                                    type="number"
                                    name="card_height_value"
                                    class="form-control form-control-sm admit-seat-cards-filter-control"
                                    min="0.1"
                                    step="0.1"
                                    value="{{ old('card_height_value', $cardSettings?->card_height_value ?? 6.6) }}">
                            </div>
                        </div>
                        <div class="col-12 col-md-4 mb-3">
                            <div class="admit-seat-cards-filter-group admit-seat-cards-modal-field mb-0">
                                <label class="font-weight-bold admit-seat-cards-filter-label">Unit</label>
                                <select name="card_dimension_unit" class="form-control form-control-sm admit-seat-cards-filter-control">
                                    <option value="cm" {{ old('card_dimension_unit', $cardSettings?->card_dimension_unit ?? 'cm') === 'cm' ? 'selected' : '' }}>Centimeter (cm)</option>
                                    <option value="px" {{ old('card_dimension_unit', $cardSettings?->card_dimension_unit ?? 'cm') === 'px' ? 'selected' : '' }}>Pixel (px)</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-12 col-md-4 mb-3">
                            <div class="admit-seat-cards-filter-group admit-seat-cards-modal-field mb-0">
                                <label class="font-weight-bold admit-seat-cards-filter-label">Grid Gap</label>
                                <input
                                    type="number"
                                    name="grid_gap_value"
                                    class="form-control form-control-sm admit-seat-cards-filter-control"
                                    min="0.1"
                                    step="0.1"
                                    value="{{ old('grid_gap_value', $cardSettings?->grid_gap_value ?? 0.85) }}">
                            </div>
                        </div>
                        <div class="col-12 col-md-6 mb-3">
                            <div class="admit-seat-cards-filter-group admit-seat-cards-modal-field mb-0">
                                <label class="font-weight-bold admit-seat-cards-filter-label">Card Logo</label>
                                <input type="file" name="card_logo" id="admitSeatCardLogoInput" class="form-control form-control-sm admit-seat-cards-filter-control" accept="image/*">
                                <small class="text-muted d-block mt-2">Leave blank to use the school logo.</small>
                            </div>
                        </div>
                        <div class="col-12 col-md-6 mb-3">
                            <div class="admit-seat-cards-filter-group admit-seat-cards-modal-field mb-0">
                                <label class="font-weight-bold admit-seat-cards-filter-label">Logo Preview</label>
                                <div class="d-flex align-items-center" style="gap:10px">
                                    <img
                                        id="admitSeatCardLogoPreview"
                                        src="{{ $currentCardLogoUrl ?: '' }}"
                                        alt="Card logo preview"
                                        class="rounded"
                                        style="width:52px;height:52px;object-fit:contain;border:1px solid #dbe4ee;background:#fff;padding:4px;">
                                    <span class="text-muted small">Current logo used by this card type.</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-12 col-md-4 mb-3">
                            <div class="admit-seat-cards-filter-group admit-seat-cards-modal-field mb-0">
                                <label class="font-weight-bold admit-seat-cards-filter-label">Transparent</label>
                                <select name="card_is_transparent" id="admitSeatCardIsTransparent" class="form-control form-control-sm admit-seat-cards-filter-control">
                                    <option value="0" {{ !$selectedTransparent ? 'selected' : '' }}>No</option>
                                    <option value="1" {{ $selectedTransparent ? 'selected' : '' }}>Yes</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-12 col-md-4 mb-3">
                            <div class="admit-seat-cards-filter-group admit-seat-cards-modal-field mb-0">
                                <label class="font-weight-bold admit-seat-cards-filter-label">School Name Color</label>
                                <div class="d-flex align-items-center" style="gap:10px">
                                    <input type="color" name="card_school_name_text_color" id="admitSeatSchoolNameColor"
                                        class="form-control form-control-color p-1"
                                        style="width:48px;height:38px;cursor:pointer"
                                        value="{{ old('card_school_name_text_color', $cardSettings?->card_school_name_text_color ?? '#ffffff') }}">
                                    <div id="admitSeatSchoolNameColorPreview" class="rounded"
                                        style="width:32px;height:32px;border:1px solid #ddd;"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-md-4 mb-3">
                            <div class="admit-seat-cards-filter-group admit-seat-cards-modal-field mb-0">
                                <label class="font-weight-bold admit-seat-cards-filter-label">School Details Color</label>
                                <div class="d-flex align-items-center" style="gap:10px">
                                    <input type="color" name="card_school_detail_text_color" id="admitSeatSchoolDetailColor"
                                        class="form-control form-control-color p-1"
                                        style="width:48px;height:38px;cursor:pointer"
                                        value="{{ old('card_school_detail_text_color', $cardSettings?->card_school_detail_text_color ?? '#e5e7eb') }}">
                                    <div id="admitSeatSchoolDetailColorPreview" class="rounded"
                                        style="width:32px;height:32px;border:1px solid #ddd;"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-md-4 mb-3">
                            <div class="admit-seat-cards-filter-group admit-seat-cards-modal-field mb-0">
                                <label class="font-weight-bold admit-seat-cards-filter-label">Card Title Color</label>
                                <div class="d-flex align-items-center" style="gap:10px">
                                    <input type="color" name="card_title_text_color" id="admitSeatTitleColor"
                                        class="form-control form-control-color p-1"
                                        style="width:48px;height:38px;cursor:pointer"
                                        value="{{ old('card_title_text_color', $cardSettings?->card_title_text_color ?? '#ffffff') }}">
                                    <div id="admitSeatTitleColorPreview" class="rounded"
                                        style="width:32px;height:32px;border:1px solid #ddd;"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-md-4 mb-3">
                            <div class="admit-seat-cards-filter-group admit-seat-cards-modal-field mb-0">
                                <label class="font-weight-bold admit-seat-cards-filter-label">Exam Type Color</label>
                                <div class="d-flex align-items-center" style="gap:10px">
                                    <input type="color" name="card_exam_type_text_color" id="admitSeatExamTypeColor"
                                        class="form-control form-control-color p-1"
                                        style="width:48px;height:38px;cursor:pointer"
                                        value="{{ old('card_exam_type_text_color', $cardSettings?->card_exam_type_text_color ?? '#ffffff') }}">
                                    <div id="admitSeatExamTypeColorPreview" class="rounded"
                                        style="width:32px;height:32px;border:1px solid #ddd;"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-md-4 mb-3">
                            <div class="admit-seat-cards-filter-group admit-seat-cards-modal-field mb-0">
                                <label class="font-weight-bold admit-seat-cards-filter-label">Exam Name Color</label>
                                <div class="d-flex align-items-center" style="gap:10px">
                                    <input type="color" name="card_exam_name_text_color" id="admitSeatExamNameColor"
                                        class="form-control form-control-color p-1"
                                        style="width:48px;height:38px;cursor:pointer"
                                        value="{{ old('card_exam_name_text_color', $cardSettings?->card_exam_name_text_color ?? '#e5e7eb') }}">
                                    <div id="admitSeatExamNameColorPreview" class="rounded"
                                        style="width:32px;height:32px;border:1px solid #ddd;"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-2 admit-seat-theme-color-fields">
                        <div class="col-12 col-md-4 mb-3">
                            <div class="admit-seat-cards-filter-group admit-seat-cards-modal-field mb-0">
                                <label class="font-weight-bold admit-seat-cards-filter-label">Color Type</label>
                                <select name="card_color_type" id="admitSeatCardColorType" class="form-control form-control-sm admit-seat-cards-filter-control">
                                    <option value="gradient" {{ $selectedColorType === 'gradient' ? 'selected' : '' }}>Gradient</option>
                                    <option value="solid" {{ $selectedColorType === 'solid' ? 'selected' : '' }}>Solid</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-12 col-md-4 mb-3 admit-seat-card-gradient-field {{ $selectedColorType === 'solid' ? 'd-none' : '' }}">
                            <div class="admit-seat-cards-filter-group admit-seat-cards-modal-field mb-0">
                                <label class="font-weight-bold admit-seat-cards-filter-label">Gradient Color 1</label>
                                <div class="d-flex align-items-center" style="gap:10px">
                                    <input type="color" name="card_color_gradient_1" id="admitSeatCardColorGradient1"
                                        class="form-control form-control-color p-1"
                                        style="width:48px;height:38px;cursor:pointer"
                                        value="{{ old('card_color_gradient_1', $cardSettings?->card_color_gradient_1 ?? '#1e3a5f') }}">
                                    <div id="admitSeatCardColorGradient1Preview" class="rounded"
                                        style="width:32px;height:32px;border:1px solid #ddd;"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-md-4 mb-3 admit-seat-card-gradient-field {{ $selectedColorType === 'solid' ? 'd-none' : '' }}">
                            <div class="admit-seat-cards-filter-group admit-seat-cards-modal-field mb-0">
                                <label class="font-weight-bold admit-seat-cards-filter-label">Gradient Color 2</label>
                                <div class="d-flex align-items-center" style="gap:10px">
                                    <input type="color" name="card_color_gradient_2" id="admitSeatCardColorGradient2"
                                        class="form-control form-control-color p-1"
                                        style="width:48px;height:38px;cursor:pointer"
                                        value="{{ old('card_color_gradient_2', $cardSettings?->card_color_gradient_2 ?? '#2563eb') }}">
                                    <div id="admitSeatCardColorGradient2Preview" class="rounded"
                                        style="width:32px;height:32px;border:1px solid #ddd;"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-md-4 mb-3 admit-seat-card-solid-field {{ $selectedColorType === 'solid' ? '' : 'd-none' }}">
                            <div class="admit-seat-cards-filter-group admit-seat-cards-modal-field mb-0">
                                <label class="font-weight-bold admit-seat-cards-filter-label">Solid Color</label>
                                <div class="d-flex align-items-center" style="gap:10px">
                                    <input type="color" name="card_solid_color" id="admitSeatCardSolidColor"
                                        class="form-control form-control-color p-1"
                                        style="width:48px;height:38px;cursor:pointer"
                                        value="{{ old('card_solid_color', $cardSettings?->card_solid_color ?? '#1e3a5f') }}">
                                    <div id="admitSeatCardSolidColorPreview" class="rounded"
                                        style="width:32px;height:32px;border:1px solid #ddd;"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-md-8 mb-3">
                            <div class="admit-seat-cards-filter-group admit-seat-cards-modal-field mb-0">
                                <label class="font-weight-bold admit-seat-cards-filter-label">Theme Preview</label>
                                <div id="admitSeatCardThemePreview" class="rounded" style="height:44px;border:1px solid #dbe4ee;"></div>
                            </div>
                        </div>
                    </div>
                    <div class="card-settings-help">
                        These settings are saved once and used by search and PDF output.
                    </div>
                </div>
                <div class="modal-footer card-settings-modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save Settings</button>
                </div>
            </form>
        </div>
    </div>
</div>

@php
    $cardSettingsPayload = $cardSettingsMap->mapWithKeys(function ($setting) {
        return [
            (string) $setting->card_type => [
                'cards_per_page' => $setting->cards_per_page,
                'cards_per_row' => $setting->cards_per_row,
                'card_width_value' => $setting->card_width_value,
                'card_height_value' => $setting->card_height_value,
                'grid_gap_value' => $setting->grid_gap_value,
                'card_dimension_unit' => $setting->card_dimension_unit,
                'card_is_transparent' => $setting->card_is_transparent,
                'card_color_type' => $setting->card_color_type,
                'card_color_gradient_1' => $setting->card_color_gradient_1,
                'card_color_gradient_2' => $setting->card_color_gradient_2,
                'card_solid_color' => $setting->card_solid_color,
                'card_school_name_text_color' => $setting->card_school_name_text_color,
                'card_school_detail_text_color' => $setting->card_school_detail_text_color,
                'card_title_text_color' => $setting->card_title_text_color,
                'card_exam_type_text_color' => $setting->card_exam_type_text_color,
                'card_exam_name_text_color' => $setting->card_exam_name_text_color,
                'card_logo_url' => ($setting->card_logo && file_exists(public_path($setting->card_logo))) ? asset($setting->card_logo) : null,
            ],
        ];
    })->toArray();
@endphp

<script>
document.addEventListener('DOMContentLoaded', function () {
    const classSelect = document.getElementById('classSelect');
    const sectionSelect = document.getElementById('sectionSelect');
    const cardTypeSelect = document.querySelector('#filterForm select[name="card_type"]');
    const cardSettingsModal = document.getElementById('cardSettingsModal');
    const cardSettingsForm = cardSettingsModal?.querySelector('form');
    const cardSettingsTypeLabel = document.getElementById('cardSettingsModalTypeLabel');
    const admitSeatCardIsTransparent = document.getElementById('admitSeatCardIsTransparent');
    const admitSeatCardThemePreview = document.getElementById('admitSeatCardThemePreview');
    const admitSeatCardColorType = document.getElementById('admitSeatCardColorType');
    const admitSeatCardColorGradient1 = document.getElementById('admitSeatCardColorGradient1');
    const admitSeatCardColorGradient2 = document.getElementById('admitSeatCardColorGradient2');
    const admitSeatCardSolidColor = document.getElementById('admitSeatCardSolidColor');
    const admitSeatSchoolNameColor = document.getElementById('admitSeatSchoolNameColor');
    const admitSeatSchoolDetailColor = document.getElementById('admitSeatSchoolDetailColor');
    const admitSeatTitleColor = document.getElementById('admitSeatTitleColor');
    const admitSeatExamTypeColor = document.getElementById('admitSeatExamTypeColor');
    const admitSeatExamNameColor = document.getElementById('admitSeatExamNameColor');
    const admitSeatCardColorGradient1Preview = document.getElementById('admitSeatCardColorGradient1Preview');
    const admitSeatCardColorGradient2Preview = document.getElementById('admitSeatCardColorGradient2Preview');
    const admitSeatCardSolidColorPreview = document.getElementById('admitSeatCardSolidColorPreview');
    const admitSeatSchoolNameColorPreview = document.getElementById('admitSeatSchoolNameColorPreview');
    const admitSeatSchoolDetailColorPreview = document.getElementById('admitSeatSchoolDetailColorPreview');
    const admitSeatTitleColorPreview = document.getElementById('admitSeatTitleColorPreview');
    const admitSeatExamTypeColorPreview = document.getElementById('admitSeatExamTypeColorPreview');
    const admitSeatExamNameColorPreview = document.getElementById('admitSeatExamNameColorPreview');
    const admitSeatCardLogoInput = document.getElementById('admitSeatCardLogoInput');
    const admitSeatCardLogoPreview = document.getElementById('admitSeatCardLogoPreview');
    const selectedSection = @json(request('section_id'));
    const hasValidationErrors = @json($errors->any());
    const cardSettingsMap = @json($cardSettingsPayload);
    const defaultCardType = @json($cardType ?? 'admit_card');
    const fallbackSchoolLogo = @json($schoolLogoUrl);
    const defaultThemeSettings = {
        card_is_transparent: false,
        card_color_type: 'gradient',
        card_color_gradient_1: '#1e3a5f',
        card_color_gradient_2: '#2563eb',
        card_solid_color: '#1e3a5f',
        card_school_name_text_color: '#ffffff',
        card_school_detail_text_color: '#e5e7eb',
        card_title_text_color: '#ffffff',
        card_exam_type_text_color: '#ffffff',
        card_exam_name_text_color: '#e5e7eb',
    };

    function settingKeyFromCardType(cardType) {
        return cardType === 'seat_card' ? '2' : '1';
    }

    function settingLabelFromCardType(cardType) {
        return cardType === 'seat_card' ? 'Seat Card Settings' : 'Admit Card Settings';
    }

    function applyCardSettings(cardType) {
        if (!cardSettingsForm) return;

        const key = settingKeyFromCardType(cardType || defaultCardType);
        const settings = cardSettingsMap[key] || cardSettingsMap['1'] || {};

        const fields = [
            'cards_per_page',
            'cards_per_row',
            'card_width_value',
            'card_height_value',
            'grid_gap_value',
            'card_dimension_unit',
            'card_is_transparent',
            'card_color_type',
            'card_color_gradient_1',
            'card_color_gradient_2',
            'card_solid_color',
            'card_school_name_text_color',
            'card_school_detail_text_color',
            'card_title_text_color',
            'card_exam_type_text_color',
            'card_exam_name_text_color',
        ];
        fields.forEach((field) => {
            const input = cardSettingsForm.elements.namedItem(field);
            const value = field === 'card_is_transparent'
                ? ((settings[field] ?? defaultThemeSettings[field]) ? '1' : '0')
                : (settings[field] ?? defaultThemeSettings[field]);
            if (input && value !== undefined && value !== null) {
                input.value = value;
            }
        });

        const cardTypeInput = cardSettingsForm.elements.namedItem('card_type');
        if (cardTypeInput) {
            cardTypeInput.value = cardType || defaultCardType;
        }

        if (cardSettingsTypeLabel) {
            cardSettingsTypeLabel.textContent = settingLabelFromCardType(cardType || defaultCardType);
        }

        refreshCardThemeControls();
    }

    function refreshCardThemeControls() {
        if (!cardSettingsForm) return;

        const isTransparent = admitSeatCardIsTransparent?.value === '1' || admitSeatCardIsTransparent?.value === 'true' || admitSeatCardIsTransparent?.checked === true;
        const colorType = admitSeatCardColorType?.value || 'gradient';
        const gradient1 = admitSeatCardColorGradient1?.value || '#1e3a5f';
        const gradient2 = admitSeatCardColorGradient2?.value || '#2563eb';
        const solid = admitSeatCardSolidColor?.value || gradient1;
        const theme = isTransparent
            ? 'transparent'
            : (colorType === 'solid'
                ? solid
                : `linear-gradient(135deg, ${gradient1}, ${gradient2})`);

        if (isTransparent) {
            if (admitSeatSchoolNameColor && admitSeatSchoolNameColor.value === '#ffffff') admitSeatSchoolNameColor.value = '#111827';
            if (admitSeatSchoolDetailColor && admitSeatSchoolDetailColor.value === '#e5e7eb') admitSeatSchoolDetailColor.value = '#334155';
            if (admitSeatTitleColor && admitSeatTitleColor.value === '#ffffff') admitSeatTitleColor.value = '#111827';
            if (admitSeatExamTypeColor && admitSeatExamTypeColor.value === '#ffffff') admitSeatExamTypeColor.value = '#111827';
            if (admitSeatExamNameColor && admitSeatExamNameColor.value === '#e5e7eb') admitSeatExamNameColor.value = '#334155';
        }

        if (isTransparent) {
            $('.admit-seat-theme-color-fields').hide();
        } else {
            $('.admit-seat-theme-color-fields').show();
            if (colorType === 'solid') {
                $('.admit-seat-card-gradient-field').hide();
                $('.admit-seat-card-solid-field').show();
            } else {
                $('.admit-seat-card-gradient-field').show();
                $('.admit-seat-card-solid-field').hide();
            }
        }

        if (admitSeatCardThemePreview) {
            admitSeatCardThemePreview.style.background = theme;
            admitSeatCardThemePreview.style.borderStyle = isTransparent ? 'dashed' : 'solid';
        }

        if (admitSeatSchoolNameColorPreview) {
            admitSeatSchoolNameColorPreview.style.background = admitSeatSchoolNameColor?.value || '#ffffff';
        }

        if (admitSeatSchoolDetailColorPreview) {
            admitSeatSchoolDetailColorPreview.style.background = admitSeatSchoolDetailColor?.value || '#e5e7eb';
        }

        if (admitSeatTitleColorPreview) {
            admitSeatTitleColorPreview.style.background = admitSeatTitleColor?.value || '#ffffff';
        }

        if (admitSeatExamTypeColorPreview) {
            admitSeatExamTypeColorPreview.style.background = admitSeatExamTypeColor?.value || '#ffffff';
        }

        if (admitSeatExamNameColorPreview) {
            admitSeatExamNameColorPreview.style.background = admitSeatExamNameColor?.value || '#e5e7eb';
        }

        if (admitSeatCardColorGradient1Preview) {
            admitSeatCardColorGradient1Preview.style.background = gradient1;
        }

        if (admitSeatCardColorGradient2Preview) {
            admitSeatCardColorGradient2Preview.style.background = gradient2;
        }

        if (admitSeatCardSolidColorPreview) {
            admitSeatCardSolidColorPreview.style.background = solid;
        }

        if (admitSeatCardLogoPreview) {
            const currentUrl = cardSettingsMap[settingKeyFromCardType(cardTypeSelect?.value || defaultCardType)]?.card_logo_url || fallbackSchoolLogo;
            admitSeatCardLogoPreview.src = currentUrl || '';
            admitSeatCardLogoPreview.classList.toggle('d-none', !currentUrl);
        }
    }

    function loadSections(classId, selectedSectionId = null) {
        if (!sectionSelect) return;

        if (!classId) {
            sectionSelect.innerHTML = '<option value="">All Sections</option>';
            if (window.refreshSelect2) window.refreshSelect2($(sectionSelect));
            return;
        }

        sectionSelect.innerHTML = '<option value="">Loading...</option>';
        if (window.refreshSelect2) window.refreshSelect2($(sectionSelect));

        fetch(`{{ route('load_section_groups') }}?school_class_id=${encodeURIComponent(classId)}`)
            .then(response => {
                if (!response.ok) throw new Error('Failed to load sections');
                return response.json();
            })
            .then(data => {
                const sections = Array.isArray(data?.sections) ? data.sections : [];
                let html = '<option value="">All Sections</option>';

                sections.forEach(section => {
                    const selected = String(selectedSectionId) === String(section.id) ? 'selected' : '';
                    html += `<option value="${section.id}" ${selected}>${section.name_en}</option>`;
                });

                sectionSelect.innerHTML = html;
                if (window.refreshSelect2) window.refreshSelect2($(sectionSelect));
            })
            .catch(() => {
                sectionSelect.innerHTML = '<option value="">All Sections</option>';
                if (window.refreshSelect2) window.refreshSelect2($(sectionSelect));
            });
    }

    $(document).on('change', '#classSelect', function () {
        loadSections(this.value);
    });

    $(document).on('change', '#admitSeatCardIsTransparent, #admitSeatCardColorType, #admitSeatCardColorGradient1, #admitSeatCardColorGradient2, #admitSeatCardSolidColor, #admitSeatSchoolNameColor, #admitSeatSchoolDetailColor, #admitSeatTitleColor, #admitSeatExamTypeColor, #admitSeatExamNameColor', function () {
        refreshCardThemeControls();
    });

    if (admitSeatCardLogoInput && admitSeatCardLogoPreview) {
        admitSeatCardLogoInput.addEventListener('change', function () {
            const file = this.files && this.files[0];
            if (!file) {
                admitSeatCardLogoPreview.src = fallbackSchoolLogo || '';
                admitSeatCardLogoPreview.classList.toggle('d-none', !fallbackSchoolLogo);
                return;
            }

            const reader = new FileReader();
            reader.onload = function (event) {
                admitSeatCardLogoPreview.src = event.target.result;
                admitSeatCardLogoPreview.classList.remove('d-none');
            };
            reader.readAsDataURL(file);
        });
    }

    if (classSelect && classSelect.value) {
        loadSections(classSelect.value, selectedSection);
    }

    $('#cardSettingsModal').on('show.bs.modal', function () {
        if (!hasValidationErrors) {
            applyCardSettings(cardTypeSelect?.value || defaultCardType);
        }
    });

    $('#cardSettingsModal').on('shown.bs.modal', function () {
        refreshCardThemeControls();
    });

    @if($errors->any())
        $('#cardSettingsModal').modal('show');
    @endif
});
</script>

<script>
const form = document.getElementById('filterForm');
document.getElementById('examTypeSelect')?.addEventListener('change', function () {
    const examSelect = document.getElementById('examSelect');
    if (examSelect) {
        examSelect.value = '';
    }
    form?.submit();
});
document.getElementById('examSelect')?.addEventListener('change', function () {
    form?.submit();
});
</script>

<style>
@include('pages.admit-seat-cards._styles')

.admit-seat-cards-page .admit-seat-cards-filter-panel {
    background: #ffffff;
    border: 1px solid #e7e5e4;
    border-radius: 18px;
    box-shadow: 0 10px 30px rgba(15, 23, 42, 0.04);
    overflow: hidden;
}

.admit-seat-cards-page .admit-seat-cards-filter-header {
    background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
    border-bottom: 1px solid #eef2f7;
    padding: 0.95rem 1rem;
}

.admit-seat-cards-page .admit-seat-cards-filter-body {
    padding: 1rem;
}

.admit-seat-cards-page .admit-seat-cards-filter-form {
    display: flex;
    flex-direction: column;
    gap: 0.85rem;
}

.admit-seat-cards-page .admit-seat-cards-filter-grid {
    display: grid;
    grid-template-columns: repeat(6, minmax(0, 1fr));
    gap: 0.75rem;
    align-items: end;
}

.admit-seat-cards-page .admit-seat-cards-filter-label {
    display: block;
    margin-bottom: 0.35rem;
    font-size: 0.77rem;
    font-weight: 700;
    color: #6b7280;
}

.admit-seat-cards-page .admit-seat-cards-filter-control {
    min-height: 46px;
    border-radius: 12px;
    border: 1px solid #e5e7eb;
    background: #fff;
    color: #111827;
    font-size: 0.92rem;
    box-shadow: none;
}

.admit-seat-cards-page .admit-seat-cards-filter-control:focus {
    border-color: #cbd5e1;
    box-shadow: 0 0 0 4px rgba(15, 23, 42, 0.05);
}

.admit-seat-cards-page .card-settings-modal-dialog {
    max-width: 860px;
}

.admit-seat-cards-page .card-settings-modal-content {
    border: 1px solid #e5e7eb;
    border-radius: 20px;
    overflow: hidden;
    box-shadow: 0 24px 60px rgba(15, 23, 42, 0.16);
}

.admit-seat-cards-page .card-settings-modal-header {
    padding: 1rem 1.25rem;
    border-bottom: 1px solid #eaeef4;
    background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
    align-items: flex-start;
}

.admit-seat-cards-page .card-settings-modal-close {
    opacity: 0.7;
    font-size: 1.8rem;
    line-height: 1;
    text-shadow: none;
}

.admit-seat-cards-page .card-settings-modal-close:hover {
    opacity: 1;
}

.admit-seat-cards-page .card-settings-modal-body {
    padding: 1.25rem;
    background: #fff;
}

.admit-seat-cards-page .admit-seat-cards-modal-field {
    padding: 0.95rem;
    border: 1px solid #e5e7eb;
    border-radius: 16px;
    background: linear-gradient(180deg, #ffffff 0%, #fbfdff 100%);
}

.admit-seat-cards-page .card-settings-help {
    margin-top: 0.75rem;
    padding: 0.85rem 1rem;
    border-radius: 14px;
    background: #f8fafc;
    color: #64748b;
    font-size: 0.88rem;
    border: 1px solid #e5e7eb;
}

.admit-seat-cards-page .card-settings-modal-footer {
    padding: 1rem 1.25rem 1.2rem;
    border-top: 1px solid #eaeef4;
    background: #fff;
}

.admit-seat-cards-page .admit-seat-cards-filter-actions {
    display: inline-flex;
    align-items: center;
    justify-content: flex-end;
    gap: 0.65rem;
    flex-wrap: wrap;
    grid-column: 1 / -1;
}

.admit-seat-cards-page .result-filter-icon-btn {
    min-width: 46px;
    min-height: 46px;
    border-radius: 12px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 0.7rem 1rem;
}

@media (max-width: 1199.98px) {
    .admit-seat-cards-page .admit-seat-cards-filter-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }
}

@media (max-width: 767.98px) {
    .admit-seat-cards-page .admit-seat-cards-filter-grid {
        grid-template-columns: 1fr;
    }

    .admit-seat-cards-page .admit-seat-cards-filter-actions {
        width: 100%;
        justify-content: flex-start;
    }

    .admit-seat-cards-page .admit-seat-cards-filter-actions > * {
        width: 100%;
        justify-content: center;
    }
}

@media print {
    .card {
        border: none;
        box-shadow: none;
    }
}
</style>
@endsection
