@extends('layouts.master')

@section('contents')
@php
    $logoUrl = !empty($school->logo) ? asset($school->logo) : null;
    $previewRow = data_get($previewReport, 'rows.0');
    $previewHighest = (float) data_get($previewReport, 'highest', 0);
    $previewStudent = [
        'full_name' => data_get($previewRow, 'student.full_name_en') ?? data_get($previewRow, 'student.full_name_bn') ?? 'Student Name',
        'student_cid' => data_get($previewRow, 'student.student_cid') ?? '0001',
        'class_name' => data_get($previewRow, 'student.class_name') ?? 'Class Name',
        'section_name' => data_get($previewRow, 'student.section_name') ?? 'Section',
    ];
    $previewTotals = data_get($previewRow, 'totals', [
        1 => ['tutorial' => 18, 'terminal' => 36, 'total' => 54, 'weight' => 20, 'weighted' => 10.8],
        2 => ['tutorial' => 17, 'terminal' => 35, 'total' => 52, 'weight' => 20, 'weighted' => 10.4],
        3 => ['tutorial' => 19, 'terminal' => 38, 'total' => 57, 'weight' => 60, 'weighted' => 34.2],
    ]);
    $previewGrandTotal = (float) data_get($previewRow, 'grand_total', 55.4);
    $previewPosition = (int) data_get($previewRow, 'position', 1);
    $previewRankRatio = $previewHighest > 0 ? ($previewGrandTotal / $previewHighest) : 0;
    $previewRemarkKey = $previewRankRatio >= 0.9 ? 'excellent' : ($previewRankRatio >= 0.75 ? 'good' : ($previewRankRatio >= 0.6 ? 'satisfactory' : 'improve'));
    $widthDefaults = [
        'pair1_tutorial' => 8,
        'pair1_terminal' => 8,
        'pair1_total' => 6,
        'pair1_weight' => 6,
        'pair2_tutorial' => 8,
        'pair2_terminal' => 8,
        'pair2_total' => 6,
        'pair2_weight' => 6,
        'pair3_tutorial' => 8,
        'pair3_terminal' => 8,
        'pair3_total' => 6,
        'pair3_weight' => 6,
        'grand_total' => 8,
        'highest' => 7,
    ];
    $previewRemarkText = match ($previewRemarkKey) {
        'excellent' => $setting->remark_excellent_text,
        'good' => $setting->remark_good_text,
        'satisfactory' => $setting->remark_satisfactory_text,
        default => $setting->remark_improve_text,
    };
    $previewCommentText = match ($previewRemarkKey) {
        'excellent' => $setting->comments_excellent_text,
        'good' => $setting->comments_good_text,
        default => $setting->comments_default_text,
    };
@endphp

<div class="container-fluid yearly-template-page py-3">
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body d-flex justify-content-between align-items-start flex-wrap gap-3">
            <div>
                <h3 class="mb-1 font-weight-bold">Yearly Final Report Template Settings</h3>
                <p class="mb-0 text-muted">Configure the yearly final result layout, labels, colors, and visibility without opening a modal.</p>
            </div>
            <div class="d-flex gap-2 flex-wrap">
                <a href="{{ route('result.yearly-final-report.index') }}" class="btn btn-secondary btn-sm">
                    <i class="fas fa-arrow-left mr-1"></i> Back to Report
                </a>
                <a href="{{ route('results.hub') }}" class="btn btn-light btn-sm">
                    <i class="fas fa-th-large mr-1"></i> Results Hub
                </a>
            </div>
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
        <div class="card-header bg-white font-weight-bold">Live Preview</div>
        <div class="card-body" style="background: #f8fafc;">
            <div class="yearly-template-preview-shell yearly-template-preview-shell--fit flex justify-center">
                @include('pages.yearly-final-report._report-card', [
                    'row' => [
                        'student' => (object) [
                            'full_name_en' => $previewStudent['full_name'],
                            'full_name_bn' => $previewStudent['full_name'],
                            'student_cid' => $previewStudent['student_cid'],
                            'id' => $previewStudent['student_cid'],
                        ],
                        'totals' => $previewTotals,
                        'grand_total' => $previewGrandTotal,
                        'position' => $previewPosition,
                    ],
                    'highest' => $previewHighest,
                    'rows' => [$previewStudent],
                    'schoolName' => $school->name ?? 'GREEN CHARTERED SCHOOL & COLLEGE',
                    'schoolAddress' => $school->address ?? 'CIP Tower, Hazari-digir-phar, Dohajari, Chandanish, Chattogram',
                    'logoPath' => $logoUrl,
                    'sessionLabel' => 'Preview Session',
                    'classLabel' => $previewStudent['class_name'],
                    'sectionLabel' => $previewStudent['section_name'],
                    'columnWidths' => $widthDefaults,
                    'templateSettings' => $setting,
                    'pairWeights' => [1 => 20, 2 => 20, 3 => 60],
                ])
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <form method="POST" action="{{ route('result.yearly-final-report.template-settings.update') }}">
                @csrf

                <div class="card shadow-sm mb-4">
                    <div class="card-header font-weight-bold bg-dark text-white">Layout</div>
                    <div class="card-body">
                        <div class="form-row">
                            <div class="form-group col-md-3">
                                <label>Margin Top</label>
                                <input type="number" step="0.1" min="0" max="50" name="margin_top_mm" class="form-control" value="{{ old('margin_top_mm', $setting->margin_top_mm) }}">
                            </div>
                            <div class="form-group col-md-3">
                                <label>Margin Right</label>
                                <input type="number" step="0.1" min="0" max="50" name="margin_right_mm" class="form-control" value="{{ old('margin_right_mm', $setting->margin_right_mm) }}">
                            </div>
                            <div class="form-group col-md-3">
                                <label>Margin Bottom</label>
                                <input type="number" step="0.1" min="0" max="50" name="margin_bottom_mm" class="form-control" value="{{ old('margin_bottom_mm', $setting->margin_bottom_mm) }}">
                            </div>
                            <div class="form-group col-md-3">
                                <label>Margin Left</label>
                                <input type="number" step="0.1" min="0" max="50" name="margin_left_mm" class="form-control" value="{{ old('margin_left_mm', $setting->margin_left_mm) }}">
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
                                <label>School Name Size</label>
                                <input type="number" step="0.1" min="8" max="40" name="school_name_font_size" class="form-control" value="{{ old('school_name_font_size', $setting->school_name_font_size) }}">
                            </div>
                            <div class="form-group col-md-3">
                                <label>School Address Size</label>
                                <input type="number" step="0.1" min="6" max="28" name="school_address_font_size" class="form-control" value="{{ old('school_address_font_size', $setting->school_address_font_size) }}">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-6 col-12 mb-4">
                        <div class="card shadow-sm h-100">
                            <div class="card-header font-weight-bold bg-dark text-white">Header</div>
                            <div class="card-body">
                                <div class="form-row">
                                    <div class="form-group yearly-col-md-20">
                                        <label>Report Title</label>
                                        <input type="text" name="report_title_text" class="form-control" value="{{ old('report_title_text', $setting->report_title_text) }}">
                                    </div>
                                    <div class="form-group yearly-col-md-20">
                                        <label>Title Size</label>
                                        <input type="number" step="0.1" min="8" max="40" name="report_title_font_size" class="form-control" value="{{ old('report_title_font_size', $setting->report_title_font_size) }}">
                                    </div>
                                    <div class="form-group yearly-col-md-20">
                                        <label>Grade Scale Title</label>
                                        <input type="text" name="grade_scale_title" class="form-control" value="{{ old('grade_scale_title', $setting->grade_scale_title) }}">
                                    </div>
                                    <div class="form-group yearly-col-md-20">
                                        <label>Annual Label</label>
                                        <input type="text" name="annual_report_label" class="form-control" value="{{ old('annual_report_label', $setting->annual_report_label) }}">
                                    </div>
                                    <div class="form-group yearly-col-md-20">
                                        <label>School Name</label>
                                        <input type="color" name="school_name_color" class="form-control form-control-color" value="{{ old('school_name_color', $setting->school_name_color) }}">
                                    </div>
                                </div>

                                <div class="form-row">
                                    <div class="form-group yearly-col-md-20">
                                        <label>School Address</label>
                                        <input type="color" name="school_address_color" class="form-control form-control-color" value="{{ old('school_address_color', $setting->school_address_color) }}">
                                    </div>
                                    <div class="form-group yearly-col-md-20">
                                        <label>Title</label>
                                        <input type="color" name="report_title_color" class="form-control form-control-color" value="{{ old('report_title_color', $setting->report_title_color) }}">
                                    </div>
                                    <div class="form-group yearly-col-md-20">
                                        <label>Annual Label</label>
                                        <input type="color" name="annual_report_color" class="form-control form-control-color" value="{{ old('annual_report_color', $setting->annual_report_color) }}">
                                    </div>
                                    <div class="form-group yearly-col-md-20">
                                        <label>Grade Border</label>
                                        <input type="color" name="grade_border_color" class="form-control form-control-color" value="{{ old('grade_border_color', $setting->grade_border_color) }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-6 col-12 mb-4">
                        <div class="card shadow-sm h-100">
                            <div class="card-header font-weight-bold bg-dark text-white">Table Colors</div>
                            <div class="card-body">
                                <div class="form-row">
                                    <div class="form-group col-md-3">
                                        <label>Table Border</label>
                                        <input type="color" name="table_border_color" class="form-control form-control-color" value="{{ old('table_border_color', $setting->table_border_color) }}">
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label>Header Background</label>
                                        <input type="color" name="table_header_bg_color" class="form-control form-control-color" value="{{ old('table_header_bg_color', $setting->table_header_bg_color) }}">
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label>Header Text</label>
                                        <input type="color" name="table_header_text_color" class="form-control form-control-color" value="{{ old('table_header_text_color', $setting->table_header_text_color) }}">
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label>Body Background</label>
                                        <input type="color" name="table_body_bg_color" class="form-control form-control-color" value="{{ old('table_body_bg_color', $setting->table_body_bg_color) }}">
                                    </div>
                                </div>

                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label>Body Text</label>
                                        <input type="color" name="table_body_text_color" class="form-control form-control-color" value="{{ old('table_body_text_color', $setting->table_body_text_color) }}">
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label>Position Border</label>
                                        <input type="color" name="position_border_color" class="form-control form-control-color" value="{{ old('position_border_color', $setting->position_border_color) }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-6 col-12 mb-4">
                        <div class="card shadow-sm h-100">
                            <div class="card-header font-weight-bold bg-dark text-white">Labels and Text</div>
                            <div class="card-body">
                                <div class="form-row">
                                    <div class="form-group yearly-col-md-20">
                                        <label>Pair Heading 1</label>
                                        <input type="text" name="pair_heading_1" class="form-control" value="{{ old('pair_heading_1', $setting->pair_heading_1) }}">
                                    </div>
                                    <div class="form-group yearly-col-md-20">
                                        <label>Pair Heading 2</label>
                                        <input type="text" name="pair_heading_2" class="form-control" value="{{ old('pair_heading_2', $setting->pair_heading_2) }}">
                                    </div>
                                    <div class="form-group yearly-col-md-20">
                                        <label>Pair Heading 3</label>
                                        <input type="text" name="pair_heading_3" class="form-control" value="{{ old('pair_heading_3', $setting->pair_heading_3) }}">
                                    </div>
                                    <div class="form-group yearly-col-md-20">
                                        <label>Grand Total Label</label>
                                        <input type="text" name="grand_total_label" class="form-control" value="{{ old('grand_total_label', $setting->grand_total_label) }}">
                                    </div>
                                    <div class="form-group yearly-col-md-20">
                                        <label>Highest Total Label</label>
                                        <input type="text" name="highest_total_label" class="form-control" value="{{ old('highest_total_label', $setting->highest_total_label) }}">
                                    </div>
                                </div>

                                <div class="form-row">
                                    <div class="form-group yearly-col-md-20">
                                        <label>Position Label</label>
                                        <input type="text" name="position_label_text" class="form-control" value="{{ old('position_label_text', $setting->position_label_text) }}">
                                    </div>
                                    <div class="form-group yearly-col-md-20">
                                        <label>Promoted Text</label>
                                        <input type="text" name="promoted_text" class="form-control" value="{{ old('promoted_text', $setting->promoted_text) }}">
                                    </div>
                                    <div class="form-group yearly-col-md-20">
                                        <label>Class Teacher Label</label>
                                        <input type="text" name="class_teacher_label" class="form-control" value="{{ old('class_teacher_label', $setting->class_teacher_label) }}">
                                    </div>
                                    <div class="form-group yearly-col-md-20">
                                        <label>Principal Label</label>
                                        <input type="text" name="principal_label" class="form-control" value="{{ old('principal_label', $setting->principal_label) }}">
                                    </div>
                                    <div class="form-group yearly-col-md-20">
                                        <label>Excellent Remark</label>
                                        <input type="text" name="remark_excellent_text" class="form-control" value="{{ old('remark_excellent_text', $setting->remark_excellent_text) }}">
                                    </div>
                                </div>

                                <div class="form-row">
                                    <div class="form-group yearly-col-md-20">
                                        <label>Good Remark</label>
                                        <input type="text" name="remark_good_text" class="form-control" value="{{ old('remark_good_text', $setting->remark_good_text) }}">
                                    </div>
                                    <div class="form-group yearly-col-md-20">
                                        <label>Satisfactory Remark</label>
                                        <input type="text" name="remark_satisfactory_text" class="form-control" value="{{ old('remark_satisfactory_text', $setting->remark_satisfactory_text) }}">
                                    </div>
                                    <div class="form-group yearly-col-md-20">
                                        <label>Improve Remark</label>
                                        <input type="text" name="remark_improve_text" class="form-control" value="{{ old('remark_improve_text', $setting->remark_improve_text) }}">
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

                    <div class="col-lg-6 col-12 mb-4">
                        <div class="card shadow-sm h-100">
                            <div class="card-header font-weight-bold bg-dark text-white">Visibility</div>
                            <div class="card-body">
                                <div class="form-row">
                                    <div class="form-group col-md-3">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" id="showWatermark" name="show_watermark" value="1" @checked(old('show_watermark', $setting->show_watermark))>
                                            <label class="custom-control-label" for="showWatermark">Show Watermark</label>
                                        </div>
                                    </div>
                                    <div class="form-group col-md-3">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" id="showGradeScale" name="show_grade_scale" value="1" @checked(old('show_grade_scale', $setting->show_grade_scale))>
                                            <label class="custom-control-label" for="showGradeScale">Show Grade Scale</label>
                                        </div>
                                    </div>
                                    <div class="form-group col-md-3">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" id="showStudentInfo" name="show_student_info" value="1" @checked(old('show_student_info', $setting->show_student_info))>
                                            <label class="custom-control-label" for="showStudentInfo">Show Student Info</label>
                                        </div>
                                    </div>
                                    <div class="form-group col-md-3">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" id="showTable" name="show_table" value="1" @checked(old('show_table', $setting->show_table))>
                                            <label class="custom-control-label" for="showTable">Show Table</label>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-row">
                                    <div class="form-group col-md-3">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" id="showSummary" name="show_summary" value="1" @checked(old('show_summary', $setting->show_summary))>
                                            <label class="custom-control-label" for="showSummary">Show Summary</label>
                                        </div>
                                    </div>
                                    <div class="form-group col-md-3">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" id="showPosition" name="show_position" value="1" @checked(old('show_position', $setting->show_position))>
                                            <label class="custom-control-label" for="showPosition">Show Position</label>
                                        </div>
                                    </div>
                                    <div class="form-group col-md-3">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" id="showRemarks" name="show_remarks" value="1" @checked(old('show_remarks', $setting->show_remarks))>
                                            <label class="custom-control-label" for="showRemarks">Show Remarks</label>
                                        </div>
                                    </div>
                                    <div class="form-group col-md-3">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" id="showComments" name="show_comments" value="1" @checked(old('show_comments', $setting->show_comments))>
                                            <label class="custom-control-label" for="showComments">Show Comments</label>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-row">
                                    <div class="form-group col-md-3">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" id="showSignature" name="show_signature" value="1" @checked(old('show_signature', $setting->show_signature))>
                                            <label class="custom-control-label" for="showSignature">Show Signature</label>
                                        </div>
                                    </div>
                                    <div class="form-group col-md-3">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" id="showPrintDate" name="show_print_date" value="1" @checked(old('show_print_date', $setting->show_print_date))>
                                            <label class="custom-control-label" for="showPrintDate">Show Print Date</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-6 col-12 mb-4">
                        <div class="card shadow-sm h-100">
                            <div class="card-header font-weight-bold bg-dark text-white">Accent Colors</div>
                            <div class="card-body">
                                <div class="form-row">
                                    <div class="form-group yearly-col-md-16">
                                        <label>Position Label Bg</label>
                                        <input type="color" name="position_label_bg_color" class="form-control form-control-color" value="{{ old('position_label_bg_color', $setting->position_label_bg_color) }}">
                                    </div>
                                    <div class="form-group yearly-col-md-16">
                                        <label>Position Label Text</label>
                                        <input type="color" name="position_label_text_color" class="form-control form-control-color" value="{{ old('position_label_text_color', $setting->position_label_text_color) }}">
                                    </div>
                                    <div class="form-group yearly-col-md-16">
                                        <label>Position Value Text</label>
                                        <input type="color" name="position_value_text_color" class="form-control form-control-color" value="{{ old('position_value_text_color', $setting->position_value_text_color) }}">
                                    </div>
                                    <div class="form-group yearly-col-md-16">
                                        <label>Promo Box Bg</label>
                                        <input type="color" name="promo_box_bg_color" class="form-control form-control-color" value="{{ old('promo_box_bg_color', $setting->promo_box_bg_color) }}">
                                    </div>
                                    <div class="form-group yearly-col-md-16">
                                        <label>Promo Box Text</label>
                                        <input type="color" name="promo_box_text_color" class="form-control form-control-color" value="{{ old('promo_box_text_color', $setting->promo_box_text_color) }}">
                                    </div>
                                    <div class="form-group yearly-col-md-16">
                                        <label>Signature Line</label>
                                        <input type="color" name="signature_line_color" class="form-control form-control-color" value="{{ old('signature_line_color', $setting->signature_line_color) }}">
                                    </div>
                                </div>

                                <div class="form-row">
                                    <div class="form-group yearly-col-md-16">
                                        <label>Remarks Title</label>
                                        <input type="color" name="remarks_title_color" class="form-control form-control-color" value="{{ old('remarks_title_color', $setting->remarks_title_color) }}">
                                    </div>
                                    <div class="form-group yearly-col-md-16">
                                        <label>Remarks Text</label>
                                        <input type="color" name="remarks_text_color" class="form-control form-control-color" value="{{ old('remarks_text_color', $setting->remarks_text_color) }}">
                                    </div>
                                    <div class="form-group yearly-col-md-16">
                                        <label>Remarks Note</label>
                                        <input type="color" name="remarks_note_color" class="form-control form-control-color" value="{{ old('remarks_note_color', $setting->remarks_note_color) }}">
                                    </div>
                                    <div class="form-group yearly-col-md-16">
                                        <label>Comments Border</label>
                                        <input type="color" name="comments_border_color" class="form-control form-control-color" value="{{ old('comments_border_color', $setting->comments_border_color) }}">
                                    </div>
                                    <div class="form-group yearly-col-md-16">
                                        <label>Comments Text</label>
                                        <input type="color" name="comments_text_color" class="form-control form-control-color" value="{{ old('comments_text_color', $setting->comments_text_color) }}">
                                    </div>
                                    <div class="form-group yearly-col-md-16">
                                        <label>Grade Border</label>
                                        <input type="color" name="grade_border_color" class="form-control form-control-color" value="{{ old('grade_border_color', $setting->grade_border_color) }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-6 col-12 mb-4">
                        <div class="card shadow-sm h-100">
                            <div class="card-header font-weight-bold bg-dark text-white">Subject Column Widths</div>
                            <div class="card-body">
                                <div class="form-row">
                                    @foreach([
                                        'pair1_tutorial' => '1st Tutorial',
                                        'pair1_terminal' => '1st Term',
                                        'pair1_total' => '1st Total',
                                        'pair1_weight' => '1st Weight',
                                        'pair2_tutorial' => '2nd Tutorial',
                                        'pair2_terminal' => '2nd Term',
                                        'pair2_total' => '2nd Total',
                                        'pair2_weight' => '2nd Weight',
                                        'pair3_tutorial' => '3rd Tutorial',
                                        'pair3_terminal' => '3rd Term',
                                        'pair3_total' => '3rd Total',
                                        'pair3_weight' => '3rd Weight',
                                        'grand_total' => 'Grand Total',
                                        'highest' => 'Highest',
                                    ] as $key => $label)
                                        <div class="form-group yearly-col-md-16">
                                            <label>{{ $label }} (%)</label>
                                            <input type="number" step="0.1" min="1" max="50" name="subject_column_widths[{{ $key }}]" class="form-control" value="{{ old('subject_column_widths.' . $key, data_get($setting->subject_column_widths, $key, $widthDefaults[$key])) }}">
                                        </div>
                                    @endforeach
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
@endsection

@section('styles')
<style>
    .yearly-report-page .card-header {
        background: linear-gradient(90deg, #1d4ed8, #1e3a8a) !important;
    }

    .yearly-col-md-20,
    .yearly-col-md-16 {
        position: relative;
        width: 100%;
        min-height: 1px;
        padding-right: 12px;
        padding-left: 12px;
    }

    @media (min-width: 768px) {
        .yearly-col-md-20 {
            flex: 0 0 20%;
            max-width: 20%;
        }

        .yearly-col-md-16 {
            flex: 0 0 16.666667%;
            max-width: 16.666667%;
        }
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

    .yearly-template-page .yearly-template-preview-shell {
        width: 100%;
        max-width: none;
        position: relative;
        overflow: visible;
        margin: 0 auto;
    }

    .yearly-template-page .yearly-template-preview-shell--fit .report-card {
        min-height: 0;
        margin: 0;
        break-after: auto;
        page-break-after: auto;
    }

    .yearly-template-page .yearly-template-preview-shell--fit .report-card__footer {
        margin-top: 18px;
    }

    .yearly-template-page .yearly-template-preview-shell--fit .report-card__signatures {
        padding-top: 18px;
    }

    .yearly-template-page .yearly-template-preview-panel {
        width: 100%;
        height: auto;
        box-sizing: border-box;
        overflow: visible;
        background: #fff;
    }

    @media (max-width: 767.98px) {
        .yearly-template-page .yearly-template-preview-shell {
            max-width: 100%;
        }
    }
</style>
@endsection
