@extends('layouts.master')

@section('contents')
@php
    $schoolName = $school->name ?? 'Green Chartered School & College';
    $schoolAddress = $school->address ?? 'CIP Tower, Hazari-digir-phar, Dohajari, Chandanish, Chattogram';
    $logoUrl = !empty($school->logo) ? asset($school->logo) : null;
    $previewTitle = $setting->report_title_text ?? 'Progress Report';
@endphp

<div class="container-fluid">
    <div class="bg-white rounded-2xl shadow p-5 mb-4">
        <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
            <div>
                <h3 class="mb-1 font-weight-bold text-slate-900">Progress Report Template Settings</h3>
                <p class="mb-0 text-muted">Customize the classic progress report layout, then use the saved settings in print and PDF output.</p>
            </div>
            <a href="{{ route('result.progress-report.index') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left mr-1"></i> Back to Report
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0 pl-3">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card shadow-sm mb-4">
        <div class="card-header bg-white font-weight-bold d-flex justify-content-between align-items-center flex-wrap gap-3">
            <span>Live Preview</span>
            <div class="preview-switch-wrap">
                <span class="preview-switch-label active" id="previewLabelClassic">
                    <i class="fas fa-scroll mr-1"></i>Classic
                </span>
                <label class="preview-switch" title="Switch preview">
                    <input type="checkbox" id="templatePreviewToggle" onchange="switchTemplatePreview(this.checked)">
                    <span class="preview-switch-slider">
                        <span class="preview-switch-knob"></span>
                    </span>
                </label>
                <span class="preview-switch-label" id="previewLabelModern">
                    <i class="fas fa-layer-group mr-1"></i>Modern
                </span>
            </div>
        </div>
        <div class="card-body" style="background: #f8fafc;">
            <div class="progress-template-preview-shell">
                <div class="progress-template-preview-panel progress-template-preview-panel--classic is-active" data-preview-panel="classic">
                    @include('pages.progress-report._classic-preview', [
                        'schoolName' => $schoolName,
                        'schoolAddress' => $schoolAddress,
                        'logoUrl' => $logoUrl,
                        'templateSettings' => $setting,
                        'gradeScale' => $gradeScale,
                        'previewStudent' => $previewStudent,
                        'sampleRows' => $sampleRows,
                        'summary' => $summary,
                        'attendancePresent' => $attendancePresent,
                        'attendanceTotal' => $attendanceTotal,
                    ])
                </div>
                <div class="progress-template-preview-panel progress-template-preview-panel--modern" data-preview-panel="modern" style="display:none;">
                    @include('pages.progress-report._modern-preview', [
                        'schoolName' => $schoolName,
                        'schoolAddress' => $schoolAddress,
                        'logoUrl' => $logoUrl,
                        'templateSettings' => $setting,
                        'gradeScale' => $gradeScale,
                        'previewStudent' => $previewStudent,
                        'sampleRows' => $sampleRows,
                        'summary' => $summary,
                        'attendancePresent' => $attendancePresent,
                        'attendanceTotal' => $attendanceTotal,
                    ])
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <form method="POST" action="{{ route('result.progress-report.template-settings.update') }}">
                @csrf

                <ul class="nav nav-tabs mb-4 yearly-template-tabs" id="progressTemplateSettingsTabs" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="progressTemplateLayoutTab" data-toggle="tab" href="#progressTemplateLayoutPane" role="tab" aria-controls="progressTemplateLayoutPane" aria-selected="true">Layout</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="progressTemplateHeaderTab" data-toggle="tab" href="#progressTemplateHeaderPane" role="tab" aria-controls="progressTemplateHeaderPane" aria-selected="false">Header</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="progressTemplateTableTab" data-toggle="tab" href="#progressTemplateTablePane" role="tab" aria-controls="progressTemplateTablePane" aria-selected="false">Table & Colors</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="progressTemplateWidthsTab" data-toggle="tab" href="#progressTemplateWidthsPane" role="tab" aria-controls="progressTemplateWidthsPane" aria-selected="false">Subject Column Widths</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="progressTemplateVisibilityTab" data-toggle="tab" href="#progressTemplateVisibilityPane" role="tab" aria-controls="progressTemplateVisibilityPane" aria-selected="false">Visibility & Copy</a>
                    </li>
                </ul>

                <div class="tab-content">
                    <div class="tab-pane fade show active" id="progressTemplateLayoutPane" role="tabpanel" aria-labelledby="progressTemplateLayoutTab">
                        <div class="card mb-4">
                            <div class="card-header bg-dark text-white font-weight-bold">Layout</div>
                            <div class="card-body">
                                <div class="form-row">
                                    <div class="form-group col-md-4">
                                        <label>Paper Orientation</label>
                                        <select name="paper_orientation" class="form-control">
                                            <option value="portrait" @selected(old('paper_orientation', $setting->paper_orientation) === 'portrait')>Portrait</option>
                                            <option value="landscape" @selected(old('paper_orientation', $setting->paper_orientation) === 'landscape')>Landscape</option>
                                        </select>
                                    </div>
                                    <div class="form-group col-md-2">
                                        <label>Margin Top (cm)</label>
                                        <input type="number" step="0.1" name="margin_top_mm" class="form-control" value="{{ old('margin_top_mm', $setting->margin_top_mm) }}">
                                    </div>
                                    <div class="form-group col-md-2">
                                        <label>Margin Right (cm)</label>
                                        <input type="number" step="0.1" name="margin_right_mm" class="form-control" value="{{ old('margin_right_mm', $setting->margin_right_mm) }}">
                                    </div>
                                    <div class="form-group col-md-2">
                                        <label>Margin Bottom (cm)</label>
                                        <input type="number" step="0.1" name="margin_bottom_mm" class="form-control" value="{{ old('margin_bottom_mm', $setting->margin_bottom_mm) }}">
                                    </div>
                                    <div class="form-group col-md-2">
                                        <label>Margin Left (cm)</label>
                                        <input type="number" step="0.1" name="margin_left_mm" class="form-control" value="{{ old('margin_left_mm', $setting->margin_left_mm) }}">
                                    </div>
                                </div>

                                <div class="form-row">
                                    <div class="form-group col-md-3">
                                        <label>Watermark Opacity</label>
                                        <input type="number" step="0.01" min="0" max="1" name="watermark_opacity" class="form-control" value="{{ old('watermark_opacity', $setting->watermark_opacity) }}">
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label>Watermark Scale %</label>
                                        <input type="number" step="0.1" min="10" max="100" name="watermark_scale" class="form-control" value="{{ old('watermark_scale', $setting->watermark_scale) }}">
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label>Logo Max Width (cm)</label>
                                        <input type="number" step="0.1" min="0.8" max="4" name="school_logo_max_width_mm" class="form-control" value="{{ old('school_logo_max_width_mm', $setting->school_logo_max_width_mm) }}">
                                    </div>
                                    <div class="form-group col-md-3 d-flex align-items-end">
                                        <div class="custom-control custom-checkbox mr-3">
                                            <input type="checkbox" class="custom-control-input" id="progressShowWatermark" name="show_watermark" value="1" @checked(old('show_watermark', $setting->show_watermark))>
                                            <label class="custom-control-label" for="progressShowWatermark">Watermark</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="progressTemplateHeaderPane" role="tabpanel" aria-labelledby="progressTemplateHeaderTab">
                        <div class="card mb-4">
                            <div class="card-header bg-dark text-white font-weight-bold">Header</div>
                            <div class="card-body">
                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label>Report Title</label>
                                        <input type="text" name="report_title_text" class="form-control" value="{{ old('report_title_text', $setting->report_title_text) }}">
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label>Title Size</label>
                                        <input type="number" step="0.1" name="report_title_font_size" class="form-control" value="{{ old('report_title_font_size', $setting->report_title_font_size) }}">
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label>Title</label>
                                        <input type="color" name="report_title_color" class="form-control form-control-color" value="{{ old('report_title_color', $setting->report_title_color) }}">
                                    </div>
                                </div>

                                <div class="form-row">
                                    <div class="form-group col-md-4">
                                        <label>School Name Size</label>
                                        <input type="number" step="0.1" name="school_name_font_size" class="form-control" value="{{ old('school_name_font_size', $setting->school_name_font_size) }}">
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label>School Name</label>
                                        <input type="color" name="school_name_color" class="form-control form-control-color" value="{{ old('school_name_color', $setting->school_name_color) }}">
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label>School Address Size</label>
                                        <input type="number" step="0.1" name="school_address_font_size" class="form-control" value="{{ old('school_address_font_size', $setting->school_address_font_size) }}">
                                    </div>
                                </div>

                                <div class="form-row">
                                    <div class="form-group col-md-4">
                                        <label>School Address</label>
                                        <input type="color" name="school_address_color" class="form-control form-control-color" value="{{ old('school_address_color', $setting->school_address_color) }}">
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label>Header Border</label>
                                        <input type="color" name="header_border_color" class="form-control form-control-color" value="{{ old('header_border_color', $setting->header_border_color) }}">
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label>Card Border</label>
                                        <input type="color" name="card_border_color" class="form-control form-control-color" value="{{ old('card_border_color', $setting->card_border_color) }}">
                                    </div>
                                </div>

                                <div class="form-row">
                                    <div class="form-group col-md-4">
                                        <label>Show Grade Scale</label>
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" id="progressShowGradeScale" name="show_grade_scale" value="1" @checked(old('show_grade_scale', $setting->show_grade_scale))>
                                            <label class="custom-control-label" for="progressShowGradeScale">Enabled</label>
                                        </div>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label>Show Student Info</label>
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" id="progressShowStudentInfo" name="show_student_info" value="1" @checked(old('show_student_info', $setting->show_student_info))>
                                            <label class="custom-control-label" for="progressShowStudentInfo">Enabled</label>
                                        </div>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label>Show Print Date</label>
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" id="progressShowPrintDate" name="show_print_date" value="1" @checked(old('show_print_date', $setting->show_print_date))>
                                            <label class="custom-control-label" for="progressShowPrintDate">Enabled</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="progressTemplateTablePane" role="tabpanel" aria-labelledby="progressTemplateTableTab">
                        <div class="card mb-4">
                            <div class="card-header bg-dark text-white font-weight-bold">Table & Colors</div>
                            <div class="card-body">
                                <div class="form-row">
                                    <div class="form-group col-md-4">
                                        <label>Header Background</label>
                                        <input type="color" name="table_header_bg_color" class="form-control form-control-color" value="{{ old('table_header_bg_color', $setting->table_header_bg_color) }}">
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label>Header Text</label>
                                        <input type="color" name="table_header_text_color" class="form-control form-control-color" value="{{ old('table_header_text_color', $setting->table_header_text_color) }}">
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label>Table Border</label>
                                        <input type="color" name="table_border_color" class="form-control form-control-color" value="{{ old('table_border_color', $setting->table_border_color) }}">
                                    </div>
                                </div>

                                <div class="form-row">
                                    <div class="form-group col-md-4">
                                        <label>Alternate Row</label>
                                        <input type="color" name="table_row_alt_bg_color" class="form-control form-control-color" value="{{ old('table_row_alt_bg_color', $setting->table_row_alt_bg_color) }}">
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label>Student Label</label>
                                        <input type="color" name="student_label_color" class="form-control form-control-color" value="{{ old('student_label_color', $setting->student_label_color) }}">
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label>Student Value</label>
                                        <input type="color" name="student_value_color" class="form-control form-control-color" value="{{ old('student_value_color', $setting->student_value_color) }}">
                                    </div>
                                </div>

                                <div class="form-row">
                                    <div class="form-group col-md-4">
                                        <label>Summary Background</label>
                                        <input type="color" name="summary_bg_color" class="form-control form-control-color" value="{{ old('summary_bg_color', $setting->summary_bg_color) }}">
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label>Summary Text</label>
                                        <input type="color" name="summary_text_color" class="form-control form-control-color" value="{{ old('summary_text_color', $setting->summary_text_color) }}">
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label>Signature Line</label>
                                        <input type="color" name="signature_line_color" class="form-control form-control-color" value="{{ old('signature_line_color', $setting->signature_line_color) }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="progressTemplateWidthsPane" role="tabpanel" aria-labelledby="progressTemplateWidthsTab">
                        <div class="card mb-4">
                            <div class="card-header bg-dark text-white font-weight-bold">Subject Column Widths</div>
                            <div class="card-body">
                                <div class="form-row">
                                    <div class="form-group col-md-2">
                                        <label>Subject</label>
                                        <input type="number" step="0.1" min="5" max="50" name="subject_column_widths[subject]" class="form-control" value="{{ old('subject_column_widths.subject', data_get($setting->subject_column_widths, 'subject', 30)) }}">
                                    </div>
                                    <div class="form-group col-md-2">
                                        <label>Full Marks</label>
                                        <input type="number" step="0.1" min="5" max="50" name="subject_column_widths[full_marks]" class="form-control" value="{{ old('subject_column_widths.full_marks', data_get($setting->subject_column_widths, 'full_marks', 10)) }}">
                                    </div>
                                    <div class="form-group col-md-2">
                                        <label>Obtained</label>
                                        <input type="number" step="0.1" min="5" max="50" name="subject_column_widths[obtained_marks]" class="form-control" value="{{ old('subject_column_widths.obtained_marks', data_get($setting->subject_column_widths, 'obtained_marks', 12)) }}">
                                    </div>
                                    <div class="form-group col-md-2">
                                        <label>Highest</label>
                                        <input type="number" step="0.1" min="5" max="50" name="subject_column_widths[highest_marks]" class="form-control" value="{{ old('subject_column_widths.highest_marks', data_get($setting->subject_column_widths, 'highest_marks', 12)) }}">
                                    </div>
                                    <div class="form-group col-md-2">
                                        <label>Total</label>
                                        <input type="number" step="0.1" min="5" max="50" name="subject_column_widths[total_marks]" class="form-control" value="{{ old('subject_column_widths.total_marks', data_get($setting->subject_column_widths, 'total_marks', 12)) }}">
                                    </div>
                                    <div class="form-group col-md-2">
                                        <label>Grade</label>
                                        <input type="number" step="0.1" min="5" max="50" name="subject_column_widths[letter_grade]" class="form-control" value="{{ old('subject_column_widths.letter_grade', data_get($setting->subject_column_widths, 'letter_grade', 12)) }}">
                                    </div>
                                    <div class="form-group col-md-2">
                                        <label>GP</label>
                                        <input type="number" step="0.1" min="5" max="50" name="subject_column_widths[grade_point]" class="form-control" value="{{ old('subject_column_widths.grade_point', data_get($setting->subject_column_widths, 'grade_point', 12)) }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="progressTemplateVisibilityPane" role="tabpanel" aria-labelledby="progressTemplateVisibilityTab">
                        <div class="card mb-4">
                            <div class="card-header bg-dark text-white font-weight-bold">Visibility & Copy</div>
                            <div class="card-body">
                                <div class="form-row">
                                    <div class="form-group col-md-3">
                                        <label>Show Summary</label>
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" id="progressShowSummary" name="show_summary" value="1" @checked(old('show_summary', $setting->show_summary))>
                                            <label class="custom-control-label" for="progressShowSummary">Enabled</label>
                                        </div>
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label>Show Remarks</label>
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" id="progressShowRemarks" name="show_remarks" value="1" @checked(old('show_remarks', $setting->show_remarks))>
                                            <label class="custom-control-label" for="progressShowRemarks">Enabled</label>
                                        </div>
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label>Show Comments</label>
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" id="progressShowComments" name="show_comments" value="1" @checked(old('show_comments', $setting->show_comments))>
                                            <label class="custom-control-label" for="progressShowComments">Enabled</label>
                                        </div>
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label>Show Signature</label>
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" id="progressShowSignature" name="show_signature" value="1" @checked(old('show_signature', $setting->show_signature))>
                                            <label class="custom-control-label" for="progressShowSignature">Enabled</label>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-row">
                                    <div class="form-group col-md-3">
                                        <label>Excellent Remark</label>
                                        <input type="text" name="remark_excellent_text" class="form-control" value="{{ old('remark_excellent_text', $setting->remark_excellent_text) }}">
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label>Good Remark</label>
                                        <input type="text" name="remark_good_text" class="form-control" value="{{ old('remark_good_text', $setting->remark_good_text) }}">
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label>Satisfactory Remark</label>
                                        <input type="text" name="remark_satisfactory_text" class="form-control" value="{{ old('remark_satisfactory_text', $setting->remark_satisfactory_text) }}">
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label>Improve Remark</label>
                                        <input type="text" name="remark_improve_text" class="form-control" value="{{ old('remark_improve_text', $setting->remark_improve_text) }}">
                                    </div>
                                </div>

                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label>Remarks Title</label>
                                        <input type="color" name="remarks_title_color" class="form-control form-control-color" value="{{ old('remarks_title_color', $setting->remarks_title_color) }}">
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label>Remarks Text</label>
                                        <input type="color" name="remarks_text_color" class="form-control form-control-color" value="{{ old('remarks_text_color', $setting->remarks_text_color) }}">
                                    </div>
                                </div>

                                <div class="form-row">
                                    <div class="form-group col-md-4">
                                        <label>Excellent Comment</label>
                                        <textarea name="comments_excellent_text" rows="2" class="form-control">{{ old('comments_excellent_text', $setting->comments_excellent_text) }}</textarea>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label>Good Comment</label>
                                        <textarea name="comments_good_text" rows="2" class="form-control">{{ old('comments_good_text', $setting->comments_good_text) }}</textarea>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label>Default Comment</label>
                                        <textarea name="comments_default_text" rows="2" class="form-control">{{ old('comments_default_text', $setting->comments_default_text) }}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary btn-lg mb-4">
                    <i class="fas fa-save mr-1"></i> Save Settings
                </button>
            </form>
        </div>

    </div>
</div>
@endsection

@section('styles')
<style>
    .progress-template-preview-shell {
        width: 100%;
        overflow: visible;
        background: #fff;
    }

    .progress-template-preview-panel.is-active {
        display: block;
    }

    .preview-switch-wrap {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 999px;
        padding: 8px 16px;
        box-shadow: 0 2px 10px rgba(15, 23, 42, 0.06);
        user-select: none;
    }

    .preview-switch-label {
        font-size: 14px;
        font-weight: 700;
        color: #9ca3af;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: color .25s ease;
    }

    .preview-switch-label.active {
        color: #166534;
    }

    .preview-switch {
        position: relative;
        display: inline-block;
        width: 52px;
        height: 28px;
        margin: 0;
        cursor: pointer;
    }

    .preview-switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }

    .preview-switch-slider {
        position: absolute;
        inset: 0;
        background: #e5e7eb;
        border-radius: 999px;
        transition: background .25s ease;
    }

    .preview-switch-knob {
        position: absolute;
        top: 3px;
        left: 3px;
        width: 22px;
        height: 22px;
        border-radius: 50%;
        background: #fff;
        box-shadow: 0 1px 4px rgba(0, 0, 0, .18);
        transition: transform .25s ease;
    }

    .preview-switch input:checked ~ .preview-switch-slider {
        background: #16a34a;
    }

    .preview-switch input:checked ~ .preview-switch-slider .preview-switch-knob {
        transform: translateX(24px);
    }

    .yearly-template-tabs {
        position: sticky;
        top: 0;
        z-index: 10;
        background: #f8fafc;
        padding: 0.5rem 0 0;
    }

    @media (max-width: 991.98px) {
        .progress-template-preview-shell {
            overflow-x: auto;
        }
    }

    .report-card-classic {
        position: relative;
        overflow: hidden;
        font-family: 'Inter', 'Segoe UI', 'Helvetica Neue', Arial, sans-serif;
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

    .report-card-watermark {
        position: absolute;
        inset: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        pointer-events: none;
        z-index: 0;
        opacity: var(--pr-watermark-opacity, 0.08);
    }

    .report-card-watermark__img {
        width: min(560px, var(--pr-watermark-scale, 78%));
        max-width: var(--pr-watermark-scale, 78%);
        max-height: 78%;
        object-fit: contain;
        filter: grayscale(100%);
    }
</style>
@endsection

@section('scripts')
<script>
    (function () {
        const STORAGE_KEY = 'progress-template-preview';

        function setPreviewMode(useModern) {
            const classicPanel = document.querySelector('[data-preview-panel="classic"]');
            const modernPanel = document.querySelector('[data-preview-panel="modern"]');
            const labelClassic = document.getElementById('previewLabelClassic');
            const labelModern = document.getElementById('previewLabelModern');
            const toggle = document.getElementById('templatePreviewToggle');

            if (!classicPanel || !modernPanel || !labelClassic || !labelModern || !toggle) {
                return;
            }

            classicPanel.style.display = useModern ? 'none' : 'block';
            modernPanel.style.display = useModern ? 'block' : 'none';
            labelClassic.classList.toggle('active', !useModern);
            labelModern.classList.toggle('active', useModern);
            toggle.checked = useModern;

            try {
                localStorage.setItem(STORAGE_KEY, useModern ? 'modern' : 'classic');
            } catch (e) {
                // ignore storage failures
            }
        }

        window.switchTemplatePreview = function (checked) {
            setPreviewMode(checked);
        };

        document.addEventListener('DOMContentLoaded', function () {
            let pref = 'classic';
            try {
                pref = localStorage.getItem(STORAGE_KEY) || 'classic';
            } catch (e) {
                pref = 'classic';
            }
            setPreviewMode(pref === 'modern');
        });
    })();
</script>
@endsection
