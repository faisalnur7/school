@extends('layouts.master')

@section('contents')
@php
    $elementLabels = [
        'header' => 'School header',
        'student' => 'Student details',
        'marks' => 'Marks table',
        'footer' => 'Comments and signature',
    ];
    $school = \App\Models\SchoolSetting::current();
    $schoolName = $school->name ?: 'School Name';
    $schoolAddress = $school->address ?: '';
    $schoolLogo = $school->logo ? (filter_var($school->logo, FILTER_VALIDATE_URL) ? $school->logo : asset(ltrim($school->logo, '/'))) : null;
    $principalSignature = $school->principal_signature ? (filter_var($school->principal_signature, FILTER_VALIDATE_URL) ? $school->principal_signature : asset(ltrim($school->principal_signature, '/'))) : null;
    $principalName = $school->principal_name ?: 'Principal';
    $principalDesignation = $school->principal_designation ?: 'Principal';
    $principalSchoolName = $school->principal_school_name ?: $schoolName;
    $order = old('element_order', $settings->element_order ?? []);
@endphp

<div class="col-12 tutorial-report-settings-page">
    <div class="d-flex align-items-center justify-content-between mb-3">
        <div>
            <a href="{{ route('result.tutorial-report.index') }}" class="btn btn-sm btn-secondary mb-2"><i class="fas fa-arrow-left mr-1"></i>Back</a>
            <h4 class="mb-1 font-weight-bold">Tutorial Report Settings</h4>
            <p class="text-muted mb-0">Arrange report blocks, resize them, and tune the A4 density used by the PDF.</p>
        </div>
        <i class="fas fa-file-alt fa-3x text-info d-none d-md-block" aria-hidden="true"></i>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(($errors ?? null)?->any())
        <div class="alert alert-danger">Please correct the highlighted settings and try again.</div>
    @endif

    <form method="POST" action="{{ route('result.tutorial-report.settings.update') }}" id="tutorialReportSettingsForm">
        @csrf
        <div class="row">
            <div class="col-xl-7 mb-3">
                <div class="card card-outline card-primary h-100">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <strong><i class="fas fa-arrows-alt mr-1"></i>Drag and arrange report blocks</strong>
                        <small class="text-muted">Drag a block to move it. Use the order list to place it before/after another block.</small>
                    </div>
                    <div class="card-body">
                        <div class="tutorial-report-canvas" id="tutorialReportCanvas" aria-label="Tutorial report layout preview">
                            <div class="tutorial-report-preview-cards" data-preview-cards>
                                <div class="tutorial-report-preview-card" data-preview-card>
                                    @foreach($order as $element)
                                        @if(in_array($element, $elements, true))
                                            <div class="tutorial-report-element tutorial-report-element--{{ $element }}" id="tutorialReportElement-{{ $element }}" data-element="{{ $element }}" draggable="true" style="left: calc(2% + {{ data_get($settings->element_positions, "$element.x", 0) }}%); top: calc({{ $element === 'header' ? 2 : ($element === 'student' ? 22 : ($element === 'marks' ? 36 : 83)) }}% + {{ data_get($settings->element_positions, "$element.y", 0) }}%); width: {{ data_get($settings->element_sizes, "$element.width", 96) }}%; height: {{ data_get($settings->element_sizes, "$element.height", 12) }}%;">
                                                <span class="tutorial-report-element__handle"><i class="fas fa-grip-vertical mr-1"></i>{{ $elementLabels[$element] }}</span>
                                                @if($element === 'header')<div class="tutorial-print-header"><table class="tutorial-print-header-table"><tr><td class="tutorial-print-header-logo-cell">@if($schoolLogo)<img class="tutorial-print-header-logo" src="{{ $schoolLogo }}" alt="School logo">@endif</td><td class="tutorial-print-header-text-cell"><div class="tutorial-print-school">{{ $schoolName }}</div>@if($schoolAddress)<div class="tutorial-print-address">{{ $schoolAddress }}</div>@endif<div class="tutorial-print-exam">FINAL TUTORIAL EXAM · 2026</div><div class="tutorial-print-title">Result Card</div></td><td class="tutorial-print-header-spacer-cell"></td></tr></table></div>
                                                @elseif($element === 'student')<table class="tutorial-print-summary"><tr><td><div class="tutorial-print-summary-label">Student</div><div class="tutorial-print-summary-value">Student Name</div></td><td><div class="tutorial-print-summary-label">Class</div><div class="tutorial-print-summary-value">Nursery</div></td><td><div class="tutorial-print-summary-label">ID</div><div class="tutorial-print-summary-value">0001</div></td></tr></table>
                                                @elseif($element === 'marks')<table class="tutorial-print-marks-table tutorial-print-student"><thead><tr><th class="subject-column">Subject</th><th class="marks-column full-marks-column">Full Marks</th><th class="marks-column obtained-marks-column">Obtained Marks</th><th class="marks-column highest-marks-column">Highest Marks</th></tr></thead><tbody><tr><td class="subject-column">Bangla</td><td class="marks-column full-marks-column">20</td><td class="marks-column obtained-marks-column">20</td><td class="marks-column highest-marks-column">20</td></tr><tr><td class="subject-column">English</td><td class="marks-column full-marks-column">20</td><td class="marks-column obtained-marks-column">20</td><td class="marks-column highest-marks-column">20</td></tr><tr class="tutorial-print-total"><td class="subject-column">Total</td><td class="marks-column full-marks-column">40</td><td class="marks-column obtained-marks-column">40</td><td class="marks-column highest-marks-column">100%</td></tr></tbody></table>
                                                @else<table class="tutorial-print-footer"><tr><td class="tutorial-print-comment-cell"><div class="tutorial-print-comment"><span class="tutorial-print-comment-label">Comments -</span><span>Excellent</span></div></td></tr><tr class="tutorial-print-signature-row"><td><table class="tutorial-print-signature-table" align="right"><tr><td><div class="tutorial-print-signature-space"></div></td></tr><tr><td><div class="tutorial-print-signature">@if($principalSignature)<img class="tutorial-print-signature-image" src="{{ $principalSignature }}" alt="Principal signature">@endif<span class="tutorial-print-signature__name">{{ $principalName }}</span>@if($principalDesignation !== $principalName)<small class="tutorial-print-signature__details">{{ $principalDesignation }}</small>@endif<small class="tutorial-print-signature__school">{{ str_replace(' ', "\u{00A0}", $principalSchoolName) }}</small></div></td></tr></table></td></tr></table>@endif
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        <div class="small text-muted mt-2" id="tutorialReportPreviewCount" aria-live="polite"></div>
                        <div class="alert alert-warning d-none mt-2 mb-0" id="tutorialReportOverflowAlert" role="alert" aria-live="polite"></div>
                        <div class="small text-muted mt-2"><i class="fas fa-info-circle mr-1"></i>Positions and sizes are stored as percentages, so the layout remains responsive when the page or density changes.</div>
                        <ol class="list-group mt-3" id="tutorialReportElementOrder" aria-label="Report block order">
                            @foreach($order as $element)
                                @if(in_array($element, $elements, true))
                                    <li class="list-group-item d-flex align-items-center py-2" data-order-element="{{ $element }}">
                                        <i class="fas fa-grip-vertical text-muted mr-2"></i><span>{{ $elementLabels[$element] }}</span>
                                        <label class="ml-auto mb-0 small"><input type="checkbox" name="element_visibility[{{ $element }}]" value="1" {{ old("element_visibility.$element", data_get($settings->element_visibility, $element, true)) ? 'checked' : '' }}> Visible</label>
                                    </li>
                                @endif
                            @endforeach
                        </ol>
                        <div id="tutorialReportOrderInputs">
                            @foreach($order as $position => $element)
                                <input type="hidden" name="element_order[]" value="{{ $element }}" data-order-input>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-5 mb-3">
                <div class="card card-outline card-info mb-3">
                    <div class="card-header"><strong>Page and density</strong></div>
                    <div class="card-body">
                        <div class="form-group">
                            <label for="tutorialReportClassSelect">Classwise settings</label>
                            <select id="tutorialReportClassSelect" name="class_id" class="form-control" onchange="window.location='{{ route('result.tutorial-report.settings.edit') }}' + (this.value ? '?class_id=' + encodeURIComponent(this.value) : '')">
                                <option value="">Global default</option>
                                @foreach($classes as $class)
                                    <option value="{{ $class->id }}" {{ (int) ($selectedClassId ?? 0) === (int) $class->id ? 'selected' : '' }}>{{ $class->name_en ?? $class->name_bn }}</option>
                                @endforeach
                            </select>
                            <small class="form-text text-muted">Choose a class to save a separate layout for its subject count. Classes without a saved profile use the global default.</small>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-6"><label>Results per page</label><select name="results_per_page" class="form-control">@foreach([1,2,3,4] as $value)<option value="{{ $value }}" {{ (int) old('results_per_page', $settings->results_per_page) === $value ? 'selected' : '' }}>{{ $value }} {{ $value === 1 ? 'result' : 'results' }}</option>@endforeach</select></div>
                            <div class="form-group col-6"><label>Font scale (%)</label><input type="number" name="font_scale_percent" min="50" max="180" class="form-control" value="{{ old('font_scale_percent', $settings->font_scale_percent) }}"></div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-6"><label>Content width (%)</label><input type="number" name="content_width_percent" min="60" max="100" class="form-control" value="{{ old('content_width_percent', $settings->content_width_percent) }}"></div>
                            <div class="form-group col-6"><label>Table padding (mm)</label><input type="number" step="0.01" name="table_padding_mm" min="0" max="8" class="form-control" value="{{ old('table_padding_mm', $settings->table_padding_mm) }}"></div>
                        </div>
                        <div class="form-row">
                            @foreach(['subject' => 'Subject', 'full_marks' => 'Full Marks', 'obtained_marks' => 'Obtained Marks', 'highest_marks' => 'Highest Marks'] as $column => $label)
                                <div class="form-group col-6"><label>{{ $label }} width (%)</label><input type="number" step="0.01" name="marks_column_widths[{{ $column }}]" min="5" max="80" class="form-control" value="{{ old("marks_column_widths.$column", data_get($settings->marks_column_widths, $column, data_get(\App\Models\TutorialReportSetting::defaults(), "marks_column_widths.$column"))) }}"></div>
                            @endforeach
                        </div>
                        <small class="form-text text-muted mb-2">Marks table column widths must total 100%.</small>
                        <div class="form-row">
                            <div class="form-group col-6"><label>Card gap (mm)</label><input type="number" step="0.01" name="card_gap_mm" min="0" max="20" class="form-control" value="{{ old('card_gap_mm', $settings->card_gap_mm) }}"></div>
                            <div class="form-group col-6"><label>Corner radius (mm)</label><input type="number" name="card_radius_mm" min="0" max="12" class="form-control" value="{{ old('card_radius_mm', $settings->card_radius_mm) }}"></div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-6"><label>Border width (mm)</label><input type="number" step="0.01" name="card_border_width_mm" min="0" max="3" class="form-control" value="{{ old('card_border_width_mm', $settings->card_border_width_mm) }}"></div>
                            <div class="form-group col-6"><label>Border color</label><input type="color" name="card_border_color" class="form-control p-1" value="{{ old('card_border_color', $settings->card_border_color) }}"></div>
                        </div>
                    </div>
                </div>

                <div class="card card-outline card-secondary mb-3">
                    <div class="card-header"><strong>A4 page size and margins (mm)</strong></div>
                    <div class="card-body">
                        <div class="form-row">
                            @foreach(['page_width_mm' => 'Width', 'page_height_mm' => 'Height', 'page_margin_top_mm' => 'Top', 'page_margin_right_mm' => 'Right', 'page_margin_bottom_mm' => 'Bottom', 'page_margin_left_mm' => 'Left'] as $field => $label)
                                <div class="form-group col-6"><label>{{ $label }}</label><input type="number" step="0.01" name="{{ $field }}" class="form-control" value="{{ old($field, $settings->{$field}) }}"></div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="card card-outline card-warning">
                    <div class="card-header"><strong>Block size and position</strong></div>
                    <div class="card-body">
                        @foreach($elements as $element)
                            <div class="border rounded p-2 mb-2">
                                <div class="font-weight-bold small mb-2">{{ $elementLabels[$element] }}</div>
                                <div class="form-row mb-0">
                                    <div class="form-group col-3 mb-1"><label class="small">X %</label><input type="number" step="0.01" name="element_positions[{{ $element }}][x]" value="{{ old("element_positions.$element.x", data_get($settings->element_positions, "$element.x", 0)) }}" class="form-control form-control-sm"></div>
                                    <div class="form-group col-3 mb-1"><label class="small">Y %</label><input type="number" step="0.01" name="element_positions[{{ $element }}][y]" value="{{ old("element_positions.$element.y", data_get($settings->element_positions, "$element.y", 0)) }}" class="form-control form-control-sm"></div>
                                    <div class="form-group col-3 mb-1"><label class="small">Width %</label><input type="number" step="0.01" name="element_sizes[{{ $element }}][width]" value="{{ old("element_sizes.$element.width", data_get($settings->element_sizes, "$element.width", 96)) }}" class="form-control form-control-sm"></div>
                                    <div class="form-group col-3 mb-1"><label class="small">Height %</label><input type="number" step="0.01" name="element_sizes[{{ $element }}][height]" value="{{ old("element_sizes.$element.height", data_get($settings->element_sizes, "$element.height", 12)) }}" class="form-control form-control-sm"></div>
                                    <div class="form-group col-3 mb-1"><label class="small">Font size (pt)</label><input type="number" step="0.01" name="element_font_sizes[{{ $element }}]" value="{{ old("element_font_sizes.$element", data_get($settings->element_font_sizes, $element, 12)) }}" min="5" max="40" class="form-control form-control-sm"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                @php($signature = $settings->signature_settings ?? \App\Models\TutorialReportSetting::defaults()['signature_settings'])
                <div class="card card-outline card-success mt-3">
                    <div class="card-header"><strong>Principal signature</strong></div>
                    <div class="card-body">
                        <div class="form-row">
                            <div class="form-group col-6"><label>Image width (mm)</label><input type="number" step="0.01" name="signature_settings[width_mm]" min="10" max="200" class="form-control" value="{{ old('signature_settings.width_mm', data_get($signature, 'width_mm', 55)) }}"></div>
                            <div class="form-group col-6"><label>Image height (mm)</label><input type="number" step="0.01" name="signature_settings[height_mm]" min="5" max="100" class="form-control" value="{{ old('signature_settings.height_mm', data_get($signature, 'height_mm', 18)) }}"></div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-6"><label>Signature spacing (mm)</label><input type="number" step="0.01" name="signature_settings[space_mm]" min="0" max="100" class="form-control" value="{{ old('signature_settings.space_mm', data_get($signature, 'space_mm', 12)) }}"></div>
                            <div class="form-group col-3"><label>X offset (%)</label><input type="number" step="0.01" name="signature_settings[offset_x_percent]" min="-30" max="30" class="form-control" value="{{ old('signature_settings.offset_x_percent', data_get($signature, 'offset_x_percent', 0)) }}"></div>
                            <div class="form-group col-3"><label>Y offset (%)</label><input type="number" step="0.01" name="signature_settings[offset_y_percent]" min="-30" max="30" class="form-control" value="{{ old('signature_settings.offset_y_percent', data_get($signature, 'offset_y_percent', 0)) }}"></div>
                        </div>
                        <div class="form-row mb-0">
                            <div class="form-group col-6 mb-0"><label><input type="hidden" name="signature_settings[visible]" value="0"><input type="checkbox" name="signature_settings[visible]" value="1" {{ old('signature_settings.visible', data_get($signature, 'visible', true)) ? 'checked' : '' }}> Show signature</label></div>
                            <div class="form-group col-6 mb-0"><label><input type="hidden" name="signature_settings[show_name]" value="0"><input type="checkbox" name="signature_settings[show_name]" value="1" {{ old('signature_settings.show_name', data_get($signature, 'show_name', true)) ? 'checked' : '' }}> Show principal name</label></div>
                        </div>
                        <div class="form-row mb-0">
                            <div class="form-group col-4 mb-0"><label><input type="hidden" name="signature_settings[show_designation]" value="0"><input type="checkbox" name="signature_settings[show_designation]" value="1" {{ old('signature_settings.show_designation', data_get($signature, 'show_designation', true)) ? 'checked' : '' }}> Designation</label></div>
                            <div class="form-group col-4 mb-0"><label><input type="hidden" name="signature_settings[show_school_name]" value="0"><input type="checkbox" name="signature_settings[show_school_name]" value="1" {{ old('signature_settings.show_school_name', data_get($signature, 'show_school_name', true)) ? 'checked' : '' }}> School name</label></div>
                        </div>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary btn-block mt-3"><i class="fas fa-save mr-1"></i>Save Tutorial Report Settings</button>
            </div>
        </div>
    </form>
</div>

<style>
    .tutorial-report-canvas { position:relative; width:100%; max-width:650px; aspect-ratio:210/297; margin:auto; overflow:hidden; border:1px solid #b9c8c2; background:#fff; box-shadow:0 3px 12px rgba(0,0,0,.12); }
    .tutorial-report-preview-cards { width:100%; height:100%; box-sizing:border-box; display:flex; flex-direction:column; overflow:hidden; }
    .tutorial-report-preview-card { position:relative; width:100%; min-height:0; overflow:hidden; box-sizing:border-box; background:#fff; }
    .tutorial-report-element { position:absolute; left:2%; width:96%; border:1px dashed #20a4a7; background:rgba(231,250,250,.78); color:#17202a; cursor:move; padding:7px; text-align:center; font-size:clamp(7px,1.2vw,12px); user-select:none; }
    .tutorial-report-element__handle { display:block; text-align:left; color:#087f83; font-size:10px; font-weight:700; }
    .tutorial-report-element--header { top:2%; height:18%; border-bottom:3px solid #c9a13b; }
    .tutorial-report-element--student { top:22%; height:12%; text-align:left; }
    .tutorial-report-element--marks { top:36%; height:45%; padding:4px; }
    .tutorial-report-element--footer { top:83%; height:12%; text-align:left; }
    .tutorial-report-element table { border-collapse:collapse; font-size:inherit; }
    .tutorial-report-preview-card .tutorial-print-header { width:100%; height:100%; box-sizing:border-box; padding:4px 8px 0; background:transparent; border-bottom:3px solid #c9a13b; color:#000; font-size:var(--preview-font-size,12px); overflow:hidden; }
    .tutorial-report-preview-card .tutorial-print-header-table { width:100%; height:100%; table-layout:fixed; }
    .tutorial-report-preview-card .tutorial-print-header-table td { border:0; padding:0; vertical-align:middle; }
    .tutorial-report-preview-card .tutorial-print-header-logo-cell,.tutorial-report-preview-card .tutorial-print-header-spacer-cell { width:15%; }
    .tutorial-report-preview-card .tutorial-print-header-text-cell { width:70%; text-align:center; }
    .tutorial-report-preview-card .tutorial-print-header-logo { display:block; width:38px; height:38px; object-fit:contain; }
    .tutorial-report-preview-card .tutorial-print-school { color:#000; font-family:Georgia,'Times New Roman',serif; font-size:1em !important; font-weight:700; line-height:1.05; white-space:nowrap; }
    .tutorial-report-preview-card .tutorial-print-address { color:#000; font-size:.55em !important; line-height:1.05; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
    .tutorial-report-preview-card .tutorial-print-exam,.tutorial-report-preview-card .tutorial-print-title { color:#000; line-height:1.05; }
    .tutorial-report-preview-card .tutorial-print-exam { font-size:.65em !important; }
    .tutorial-report-preview-card .tutorial-print-title { margin-top:2px; font-size:.8em !important; }
    .tutorial-report-preview-card .tutorial-print-title { font-family:Georgia,'Times New Roman',serif; font-weight:700; }
    .tutorial-report-preview-card .tutorial-print-summary { width:100%; height:100%; margin:0 auto; border:1px solid #d5ddda; border-radius:6px; border-collapse:separate; border-spacing:0; table-layout:fixed; }
    .tutorial-report-preview-card .tutorial-print-summary td { width:33.333%; padding:var(--preview-table-padding,2px) calc(var(--preview-table-padding,2px) * 2); border:0; vertical-align:middle; }
    .tutorial-report-preview-card .tutorial-print-summary td + td { border-left:1px solid #d5ddda; }
    .tutorial-report-preview-card .tutorial-print-summary-label,.tutorial-report-preview-card .tutorial-print-summary-value { color:#000; font-size:var(--preview-font-size,12px) !important; line-height:1.05; }
    .tutorial-report-preview-card .tutorial-print-summary-label { letter-spacing:1px; text-transform:uppercase; }
    .tutorial-report-preview-card .tutorial-print-summary-value { margin-top:2px; font-weight:700; }
    .tutorial-report-preview-card .tutorial-print-marks-table { width:100%; height:auto !important; margin:0 auto; border:1px solid #d5ddda; border-radius:6px; overflow:hidden; table-layout:fixed; }
    .tutorial-report-preview-card .tutorial-print-student th,.tutorial-report-preview-card .tutorial-print-student td { border:1px solid #e3e8e6; padding:var(--preview-table-padding,2px) calc(var(--preview-table-padding,2px) * 1.5); line-height:1.05; vertical-align:middle; }
    .tutorial-report-preview-card .tutorial-print-student th { color:#000; font-size:var(--preview-font-size,12px) !important; font-weight:700; letter-spacing:.6px; text-align:left; text-transform:uppercase; white-space:nowrap; }
    .tutorial-report-preview-card .tutorial-print-student td,.tutorial-report-preview-card .tutorial-print-total td { color:#000; font-size:var(--preview-font-size,12px) !important; font-weight:700; }
    .tutorial-report-preview-card .tutorial-print-student .subject-column { width:var(--preview-subject-width,25%); font-weight:600; }
    .tutorial-report-preview-card .tutorial-print-student .full-marks-column { width:var(--preview-full-marks-width,15%); }
    .tutorial-report-preview-card .tutorial-print-student .obtained-marks-column { width:var(--preview-obtained-marks-width,35%); }
    .tutorial-report-preview-card .tutorial-print-student .highest-marks-column { width:var(--preview-highest-marks-width,25%); }
    .tutorial-report-preview-card .tutorial-print-student .marks-column { text-align:center; white-space:nowrap; }
    .tutorial-report-preview-card .tutorial-print-footer { width:100%; height:auto !important; margin:0 auto; table-layout:fixed; }
    .tutorial-report-preview-card .tutorial-report-element--footer { overflow:visible; z-index:4; }
    .tutorial-report-preview-card .tutorial-print-footer td { border:0; padding:0; vertical-align:bottom; }
    .tutorial-report-preview-card .tutorial-print-comment { color:#000; font-size:var(--preview-font-size,12px) !important; font-weight:700; line-height:1.1; white-space:nowrap; }
    .tutorial-report-preview-card .tutorial-print-comment-label { margin-right:10px; color:#000; font-size:inherit; font-weight:700; letter-spacing:1px; text-transform:uppercase; }
    .tutorial-report-preview-card .tutorial-print-signature-row td { padding-top:10px; text-align:right; }
    .tutorial-report-preview-card .tutorial-print-signature-table { position:absolute; right:0; bottom:0; width:var(--preview-signature-width,55mm); transform:translate(var(--preview-signature-x,0%), var(--preview-signature-y,0%)); }
    .tutorial-report-preview-card .tutorial-print-signature-table td { text-align:center; }
    .tutorial-report-preview-card .tutorial-print-signature-space { height:0; }
    .tutorial-report-preview-card .tutorial-print-signature { width:100%; height:auto !important; min-height:var(--preview-signature-height,18mm); padding-top:2mm; border-top:0; color:#000; font-size:var(--preview-font-size,12px) !important; line-height:1.05; overflow:visible; }
    .tutorial-report-preview-card .tutorial-print-signature img { display:block; width:var(--preview-signature-width,55mm); height:var(--preview-signature-height,18mm); object-fit:contain; margin:0 auto 1px; }
    .tutorial-report-preview-card .tutorial-print-signature span { display:block; }
    .tutorial-report-preview-card .tutorial-print-signature small { display:block; font-size:.72em; line-height:1.05; }
    .tutorial-report-preview-card .tutorial-print-signature__school { white-space:nowrap; }
    /* Keep the block drag target interactive without showing editor labels in the report preview. */
    .tutorial-report-preview-card .tutorial-report-element__handle { position:absolute; inset:0; z-index:5; height:100%; margin:0; padding:0; opacity:0; font-size:0; }
    .tutorial-report-preview-card .tutorial-report-element__handle i { display:none; }
    .tutorial-report-preview-card .tutorial-report-element { border:0; background:transparent; padding:0; text-align:initial; }
    .tutorial-report-preview-card .tutorial-report-element--header { border-bottom:0; }
    .tutorial-report-preview-card .tutorial-report-element--marks { padding:0; }
    .tutorial-report-preview-card .tutorial-report-element--header small,
    .tutorial-report-preview-card .tutorial-report-element--student,
    .tutorial-report-preview-card .tutorial-report-element--marks,
    .tutorial-report-preview-card .tutorial-report-element--footer { font-size:var(--preview-font-size, 12px) !important; }
    .tutorial-report-preview-card .tutorial-report-element--student .preview-grid span,
    .tutorial-report-preview-card .tutorial-report-element--footer { line-height:1.15; }
    .tutorial-report-settings-page .form-group label { font-size:.82rem; margin-bottom:3px; }
</style>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const canvas = document.getElementById('tutorialReportCanvas');
    const orderList = document.getElementById('tutorialReportElementOrder');
    const orderInputs = document.getElementById('tutorialReportOrderInputs');
    const form = document.getElementById('tutorialReportSettingsForm');
    const previewCards = canvas ? canvas.querySelector('[data-preview-cards]') : null;
    const previewCount = document.getElementById('tutorialReportPreviewCount');
    const overflowAlert = document.getElementById('tutorialReportOverflowAlert');
    if (!canvas || !orderList || !orderInputs) return;
    let dragged = null;
    const baseTop = { header: 2, student: 22, marks: 36, footer: 83 };
    const densityFontBases = {
        1: { header: 26, student: 18, marks: 18, footer: 18 },
        2: { header: 17, student: 14, marks: 12, footer: 12 },
        3: { header: 15, student: 10, marks: 10, footer: 10 },
        4: { header: 12, student: 8, marks: 8, footer: 8 }
    };
    const clamp = function (value, min, max) { return Math.max(min, Math.min(max, value)); };
    const numericValue = function (name, fallback) {
        const field = form && form.elements.namedItem(name);
        const value = parseFloat(field && field.value);
        return Number.isFinite(value) ? value : fallback;
    };
    const fieldValue = function (name, fallback) {
        const field = form && form.elements.namedItem(name);
        return field ? field.value : fallback;
    };
    const checkboxValue = function (name, fallback) {
        const field = form && form.querySelector('input[type="checkbox"][name="' + name + '"]');
        return field ? field.checked : fallback;
    };
    const refreshPreview = function () {
        if (!form || !previewCards) return;
        const perPage = clamp(parseInt(fieldValue('results_per_page', '2'), 10) || 2, 1, 4);
        const scale = clamp(numericValue('font_scale_percent', 100), 50, 180) / 100;
        const tablePadding = clamp(numericValue('table_padding_mm', 1.2), 0, 8);
        const marksColumnWidths = {
            subject: clamp(numericValue('marks_column_widths[subject]', 25), 5, 80),
            fullMarks: clamp(numericValue('marks_column_widths[full_marks]', 15), 5, 80),
            obtainedMarks: clamp(numericValue('marks_column_widths[obtained_marks]', 35), 5, 80),
            highestMarks: clamp(numericValue('marks_column_widths[highest_marks]', 25), 5, 80)
        };
        const contentWidth = clamp(numericValue('content_width_percent', 96), 60, 100);
        const cardGap = Math.max(0, numericValue('card_gap_mm', 2));
        const pageWidth = Math.max(100, numericValue('page_width_mm', 210));
        const pageHeight = Math.max(100, numericValue('page_height_mm', 297));
        const marginTop = Math.max(0, numericValue('page_margin_top_mm', 10));
        const marginRight = Math.max(0, numericValue('page_margin_right_mm', 12));
        const marginBottom = Math.max(0, numericValue('page_margin_bottom_mm', 10));
        const marginLeft = Math.max(0, numericValue('page_margin_left_mm', 12));
        const borderWidth = clamp(numericValue('card_border_width_mm', .3), 0, 3);
        const radius = clamp(numericValue('card_radius_mm', 2), 0, 12);
        const borderColor = fieldValue('card_border_color', '#cbd6d2');
        const signatureVisible = checkboxValue('signature_settings[visible]', true);
        const signatureWidth = clamp(numericValue('signature_settings[width_mm]', 55), 10, 200);
        const signatureHeight = clamp(numericValue('signature_settings[height_mm]', 18), 5, 100);
        const signatureSpace = clamp(numericValue('signature_settings[space_mm]', 12), 0, 100);
        const signatureX = clamp(numericValue('signature_settings[offset_x_percent]', 0), -30, 30);
        const signatureY = clamp(numericValue('signature_settings[offset_y_percent]', 0), -30, 30);
        const showName = checkboxValue('signature_settings[show_name]', true);
        const showDesignation = checkboxValue('signature_settings[show_designation]', true);
        const showSchoolName = checkboxValue('signature_settings[show_school_name]', true);
        const primary = previewCards.querySelector('[data-preview-card]');
        if (!primary) return;

        canvas.style.aspectRatio = pageWidth + ' / ' + pageHeight;
        previewCards.style.padding = marginTop + 'mm ' + marginRight + 'mm ' + marginBottom + 'mm ' + marginLeft + 'mm';

        previewCards.querySelectorAll('[data-preview-card][data-preview-copy]').forEach(function (copy) { copy.remove(); });
        for (let index = 1; index < perPage; index += 1) {
            const copy = primary.cloneNode(true);
            copy.dataset.previewCopy = 'true';
            copy.querySelectorAll('[id]').forEach(function (node) { node.removeAttribute('id'); });
            previewCards.appendChild(copy);
        }

        const cards = previewCards.querySelectorAll('[data-preview-card]');
        cards.forEach(function (card) {
            const cardIndex = indexSafe(card);
            card.style.height = 'calc((100% - ' + ((perPage - 1) * cardGap) + 'mm) / ' + perPage + ')';
            card.style.marginBottom = cardIndex < perPage - 1 ? cardGap + 'mm' : '0';
            card.style.border = borderWidth + 'px solid ' + borderColor;
            card.style.borderRadius = radius + 'px';
            card.style.setProperty('--preview-signature-space', ({1: 35, 2: 24, 3: 14, 4: 8}[perPage] || 14) + 'px');
            card.style.setProperty('--preview-signature-width', signatureWidth + 'mm');
            card.style.setProperty('--preview-signature-height', signatureHeight + 'mm');
            card.style.setProperty('--preview-signature-x', signatureX + '%');
            card.style.setProperty('--preview-signature-y', signatureY + '%');
            card.style.setProperty('--preview-subject-width', marksColumnWidths.subject + '%');
            card.style.setProperty('--preview-full-marks-width', marksColumnWidths.fullMarks + '%');
            card.style.setProperty('--preview-obtained-marks-width', marksColumnWidths.obtainedMarks + '%');
            card.style.setProperty('--preview-highest-marks-width', marksColumnWidths.highestMarks + '%');
            const signatureTable = card.querySelector('.tutorial-print-signature-table');
            if (signatureTable) signatureTable.style.display = signatureVisible ? 'table' : 'none';
            const signatureName = card.querySelector('.tutorial-print-signature__name');
            const signatureDesignation = card.querySelector('.tutorial-print-signature__details');
            const signatureSchool = card.querySelector('.tutorial-print-signature__school');
            if (signatureName) signatureName.style.display = showName ? 'block' : 'none';
            if (signatureDesignation) signatureDesignation.style.display = showDesignation ? 'block' : 'none';
            if (signatureSchool) signatureSchool.style.display = showSchoolName ? 'block' : 'none';
            card.querySelectorAll('[data-element]').forEach(function (element) {
                const key = element.dataset.element;
                const x = numericValue('element_positions[' + key + '][x]', 0);
                const y = numericValue('element_positions[' + key + '][y]', 0);
                const configuredWidth = numericValue('element_sizes[' + key + '][width]', 96);
                const width = ['student', 'marks', 'footer'].includes(key)
                    ? Math.min(configuredWidth, contentWidth)
                    : configuredWidth;
                const height = numericValue('element_sizes[' + key + '][height]', 12);
                const fontDefault = key === 'header' ? 14 : 12;
                const fontSize = numericValue('element_font_sizes[' + key + ']', fontDefault);
                const visibleField = form.elements.namedItem('element_visibility[' + key + ']');
                const visible = !visibleField || visibleField.checked;
                const fontBase = (densityFontBases[perPage] || densityFontBases[2])[key] || 12;
                element.style.display = visible ? 'block' : 'none';
                element.style.left = 'calc(2% + ' + x + '%)';
                element.style.top = 'calc(' + (baseTop[key] || 0) + '% + ' + y + '%)';
                element.style.width = width + '%';
                element.style.height = height + '%';
                element.style.setProperty('--preview-font-size', (fontBase * (fontSize / fontDefault) * scale) + 'px');
                element.style.setProperty('--preview-table-padding', tablePadding + 'mm');
                element.style.setProperty('--preview-content-width', contentWidth + '%');
            });
        });
        if (overflowAlert) {
            const overflowItems = [];
            const tolerance = 2;
            cards.forEach(function (card, cardIndex) {
                const cardRect = card.getBoundingClientRect();
                card.querySelectorAll('[data-element]').forEach(function (element) {
                    if (getComputedStyle(element).display === 'none') return;
                    const rect = element.getBoundingClientRect();
                    const overflowsCard = rect.top < cardRect.top - tolerance
                        || rect.left < cardRect.left - tolerance
                        || rect.right > cardRect.right + tolerance
                        || rect.bottom > cardRect.bottom + tolerance;
                    const overflowsContent = element.scrollHeight > element.clientHeight + tolerance
                        || element.scrollWidth > element.clientWidth + tolerance;
                    if (overflowsCard || overflowsContent) {
                        overflowItems.push('card ' + (cardIndex + 1) + ' ' + (element.dataset.element || 'content'));
                    }
                });
            });
            const uniqueOverflowItems = Array.from(new Set(overflowItems));
            if (uniqueOverflowItems.length) {
                overflowAlert.classList.remove('d-none');
                overflowAlert.innerHTML = '<i class="fas fa-exclamation-triangle mr-1"></i><strong>Possible overflow:</strong> the selected cards cannot fit all visible content at the current density. Reduce font scale, padding, block height, or signature size before exporting. (' + uniqueOverflowItems.join(', ') + ')';
            } else {
                overflowAlert.classList.add('d-none');
                overflowAlert.textContent = '';
            }
        }
        if (previewCount) previewCount.textContent = 'Showing ' + perPage + ' result ' + (perPage === 1 ? 'card' : 'cards') + ' per page.';
    };
    const indexSafe = function (card) { return Array.prototype.indexOf.call(previewCards.querySelectorAll('[data-preview-card]'), card); };
    const syncOrder = function () {
        orderInputs.innerHTML = '';
        orderList.querySelectorAll('[data-order-element]').forEach(function (item) {
            const input = document.createElement('input');
            input.type = 'hidden'; input.name = 'element_order[]'; input.value = item.dataset.orderElement; input.dataset.orderInput = 'true';
            orderInputs.appendChild(input);
        });
    };
    const primaryCard = canvas.querySelector('[data-preview-card]');
    primaryCard.querySelectorAll('[data-element]').forEach(function (element) {
        const handle = element.querySelector('.tutorial-report-element__handle');
        if (handle) {
            handle.addEventListener('pointerdown', function (event) {
                event.preventDefault();
                const rect = canvas.getBoundingClientRect();
                const startX = event.clientX;
                const startY = event.clientY;
                const initialX = numericValue('element_positions[' + element.dataset.element + '][x]', 0);
                const initialY = numericValue('element_positions[' + element.dataset.element + '][y]', 0);
                element.setPointerCapture(event.pointerId);
                const move = function (moveEvent) {
                    const x = Math.max(-20, Math.min(30, initialX + ((moveEvent.clientX - startX) / rect.width) * 100));
                    const y = Math.max(-20, Math.min(30, initialY + ((moveEvent.clientY - startY) / rect.height) * 100));
                    element.style.left = x + '%';
                    element.style.top = y + '%';
                    const xInput = document.querySelector('[name="element_positions[' + element.dataset.element + '][x]"]');
                    const yInput = document.querySelector('[name="element_positions[' + element.dataset.element + '][y]"]');
                    if (xInput) xInput.value = x.toFixed(2);
                    if (yInput) yInput.value = y.toFixed(2);
                    refreshPreview();
                };
                const stop = function () { element.removeEventListener('pointermove', move); element.removeEventListener('pointerup', stop); };
                element.addEventListener('pointermove', move);
                element.addEventListener('pointerup', stop);
            });
        }
        element.addEventListener('dragstart', function () { dragged = element; element.classList.add('opacity-50'); });
        element.addEventListener('dragend', function () { element.classList.remove('opacity-50'); dragged = null; });
        element.addEventListener('dragover', function (event) { event.preventDefault(); });
        element.addEventListener('drop', function (event) {
            event.preventDefault();
            if (!dragged || dragged === element) return;
            const rect = element.getBoundingClientRect();
            element.parentNode.insertBefore(dragged, event.clientY < rect.top + rect.height / 2 ? element : element.nextSibling);
            const target = orderList.querySelector('[data-order-element="' + dragged.dataset.element + '"]');
            const reference = orderList.querySelector('[data-order-element="' + element.dataset.element + '"]');
            orderList.insertBefore(target, event.clientY < rect.top + rect.height / 2 ? reference : reference.nextSibling);
            syncOrder();
        });
    });
    orderList.querySelectorAll('[data-order-element]').forEach(function (item) {
        item.draggable = true;
        item.addEventListener('dragstart', function () { dragged = primaryCard.querySelector('[data-element="' + item.dataset.orderElement + '"]'); });
        item.addEventListener('dragover', function (event) { event.preventDefault(); });
        item.addEventListener('drop', function (event) { event.preventDefault(); syncOrder(); });
    });
    syncOrder();
    if (form) {
        form.querySelectorAll('input, select').forEach(function (field) {
            field.addEventListener('input', refreshPreview);
            field.addEventListener('change', refreshPreview);
        });
    }
    refreshPreview();
});
</script>
@endsection
