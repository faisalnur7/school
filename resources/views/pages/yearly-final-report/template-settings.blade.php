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
            <div class="yearly-template-preview-shell">
                <div class="border rounded-lg p-3 bg-white yearly-template-preview-panel" style="border-color: {{ $setting->table_border_color }};">
                    <div class="position-relative">
                        @if($setting->show_watermark && $logoUrl)
                            <div class="position-absolute w-100 h-100 d-flex align-items-center justify-content-center" style="inset: 0; opacity: {{ $setting->watermark_opacity }}; pointer-events:none;">
                                <img src="{{ $logoUrl }}" alt="Watermark" style="max-width: {{ $setting->watermark_scale }}%; max-height: 95%; object-fit: contain; filter: grayscale(100%);">
                            </div>
                        @endif

                        <div class="d-flex justify-content-between align-items-start mb-3 position-relative" style="z-index:1;">
                            <div class="d-flex align-items-center" style="gap: 12px;">
                                @if($logoUrl)
                                    <img src="{{ $logoUrl }}" alt="Logo" style="width: 54px; height: 54px; object-fit: contain;">
                                @endif
                                <div class="text-center">
                                    <div style="font-size: {{ $setting->school_name_font_size }}px; color: {{ $setting->school_name_color }}; font-weight: 800; text-transform: uppercase;">
                                        {{ $school->name ?? 'School Name' }}
                                    </div>
                                    <div style="font-size: {{ $setting->school_address_font_size }}px; color: {{ $setting->school_address_color }};">
                                        {{ $school->address ?? 'School Address' }}
                                    </div>
                                </div>
                            </div>

                            @if($setting->show_grade_scale)
                                <table class="table table-bordered table-sm mb-0" style="width: 145px; font-size: 9px; border-color: {{ $setting->grade_border_color }};">
                                    <thead style="background: {{ $setting->table_header_bg_color }}; color: {{ $setting->table_header_text_color }};">
                                        <tr><th colspan="3">{{ $setting->grade_scale_title }}</th></tr>
                                    </thead>
                                    <tbody style="background: {{ $setting->table_body_bg_color }}; color: {{ $setting->table_body_text_color }};">
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

                        <div class="text-center font-italic font-weight-bold mb-3" style="font-size: {{ $setting->report_title_font_size }}px; color: {{ $setting->report_title_color }};">
                            {{ $setting->report_title_text }}
                        </div>

                        <div class="mb-3">
                            <div class="font-weight-bold" style="color: {{ $setting->annual_report_color }};">{{ $setting->annual_report_label }}: Preview Session</div>
                            @if($setting->show_student_info)
                                <div class="mt-2">
                                    <div><strong>Name</strong> : {{ $previewStudent['full_name'] }}</div>
                                    <div><strong>Class</strong> : {{ $previewStudent['class_name'] }}</div>
                                    <div><strong>ID</strong> : {{ $previewStudent['student_cid'] }}</div>
                                    <div><strong>Section</strong> : {{ $previewStudent['section_name'] }}</div>
                                </div>
                            @endif
                        </div>

                        @if($setting->show_table)
                            <table class="table table-bordered table-sm mb-3" style="font-size: 9px; table-layout: fixed; border-color: {{ $setting->table_border_color }};">
                                <thead style="background: {{ $setting->table_header_bg_color }}; color: {{ $setting->table_header_text_color }};">
                                    <tr>
                                        <th colspan="4">{{ $setting->pair_heading_1 }}</th>
                                        <th colspan="4">{{ $setting->pair_heading_2 }}</th>
                                        <th colspan="4">{{ $setting->pair_heading_3 }}</th>
                                        <th>{{ $setting->grand_total_label }}</th>
                                        <th>{{ $setting->highest_total_label }}</th>
                                    </tr>
                                </thead>
                                <tbody style="background: {{ $setting->table_body_bg_color }}; color: {{ $setting->table_body_text_color }};">
                                    <tr>
                                        <td>{{ data_get($previewTotals, '1.tutorial') }}</td>
                                        <td>{{ data_get($previewTotals, '1.terminal') }}</td>
                                        <td>{{ data_get($previewTotals, '1.total') }}</td>
                                        <td>{{ data_get($previewTotals, '1.weighted') }}</td>
                                        <td>{{ data_get($previewTotals, '2.tutorial') }}</td>
                                        <td>{{ data_get($previewTotals, '2.terminal') }}</td>
                                        <td>{{ data_get($previewTotals, '2.total') }}</td>
                                        <td>{{ data_get($previewTotals, '2.weighted') }}</td>
                                        <td>{{ data_get($previewTotals, '3.tutorial') }}</td>
                                        <td>{{ data_get($previewTotals, '3.terminal') }}</td>
                                        <td>{{ data_get($previewTotals, '3.total') }}</td>
                                        <td>{{ data_get($previewTotals, '3.weighted') }}</td>
                                        <td>{{ number_format($previewGrandTotal, 2) }}</td>
                                        <td>{{ number_format($previewHighest, 2) }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        @endif

                        @if($setting->show_summary)
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                @if($setting->show_position)
                                    <div class="d-inline-flex align-items-center" style="border: 1px solid {{ $setting->position_border_color }};">
                                        <div class="px-3 py-1 font-weight-bold" style="background: {{ $setting->position_label_bg_color }}; color: {{ $setting->position_label_text_color }};">{{ $setting->position_label_text }}</div>
                                        <div class="px-3 py-1 font-weight-bold" style="color: {{ $setting->position_value_text_color }};">{{ $previewPosition }}</div>
                                    </div>
                                @endif
                                <div class="ml-auto px-3 py-1 font-weight-bold" style="background: {{ $setting->promo_box_bg_color }}; color: {{ $setting->promo_box_text_color }};">
                                    {{ $setting->promoted_text }}
                                </div>
                            </div>
                        @endif

                        @if($setting->show_remarks)
                            <div class="mb-2">
                                <div class="font-weight-bold" style="color: {{ $setting->remarks_title_color }};">REMARKS:</div>
                                <div style="color: {{ $setting->remarks_text_color }};">
                                    <div class="{{ $previewRemarkKey === 'excellent' ? 'font-weight-bold' : '' }}">(i) {{ $setting->remark_excellent_text }}</div>
                                    <div class="{{ $previewRemarkKey === 'good' ? 'font-weight-bold' : '' }}">(ii) {{ $setting->remark_good_text }}</div>
                                    <div class="{{ $previewRemarkKey === 'satisfactory' ? 'font-weight-bold' : '' }}">(iii) {{ $setting->remark_satisfactory_text }}</div>
                                    <div class="{{ $previewRemarkKey === 'improve' ? 'font-weight-bold' : '' }}">(iv) {{ $setting->remark_improve_text }}</div>
                                </div>
                                <div class="font-weight-bold" style="color: {{ $setting->remarks_note_color }};">{{ $previewRemarkText }}</div>
                            </div>
                        @endif

                        @if($setting->show_comments)
                            <div class="p-2 mb-2" style="border: 1px solid {{ $setting->comments_border_color }}; color: {{ $setting->comments_text_color }};">
                                <div>{{ $previewCommentText }}</div>
                                <div>{{ $previewStudent['full_name'] }} ranked {{ $previewPosition }} in this preview.</div>
                            </div>
                        @endif

                        @if($setting->show_signature || $setting->show_print_date)
                            <div class="d-flex justify-content-between align-items-end">
                                <div>
                                    @if($setting->show_print_date)
                                        <div class="font-weight-bold">Published Date: {{ now()->format('d-m-Y') }}</div>
                                    @endif
                                </div>
                                @if($setting->show_signature)
                                    <div class="text-center ml-auto">
                                        <div class="border-top mb-1" style="border-top-color: {{ $setting->signature_line_color }}; width: 120px;"></div>
                                        <div>{{ $setting->class_teacher_label }}</div>
                                    </div>
                                    <div class="text-center ml-4">
                                        <div class="border-top mb-1" style="border-top-color: {{ $setting->signature_line_color }}; width: 120px;"></div>
                                        <div>{{ $setting->principal_label }}</div>
                                    </div>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
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
                                <label>Paper Orientation</label>
                                <select name="paper_orientation" class="form-control">
                                    <option value="portrait" @selected(old('paper_orientation', $setting->paper_orientation) === 'portrait')>Portrait</option>
                                    <option value="landscape" @selected(old('paper_orientation', $setting->paper_orientation) === 'landscape')>Landscape</option>
                                </select>
                            </div>
                            <div class="form-group col-md-2">
                                <label>Margin Top</label>
                                <input type="number" step="0.1" min="0" max="50" name="margin_top_mm" class="form-control" value="{{ old('margin_top_mm', $setting->margin_top_mm) }}">
                            </div>
                            <div class="form-group col-md-2">
                                <label>Margin Right</label>
                                <input type="number" step="0.1" min="0" max="50" name="margin_right_mm" class="form-control" value="{{ old('margin_right_mm', $setting->margin_right_mm) }}">
                            </div>
                            <div class="form-group col-md-2">
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

                <div class="card shadow-sm mb-4">
                    <div class="card-header font-weight-bold bg-dark text-white">Header</div>
                    <div class="card-body">
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label>Report Title</label>
                                <input type="text" name="report_title_text" class="form-control" value="{{ old('report_title_text', $setting->report_title_text) }}">
                            </div>
                            <div class="form-group col-md-3">
                                <label>Title Size</label>
                                <input type="number" step="0.1" min="8" max="40" name="report_title_font_size" class="form-control" value="{{ old('report_title_font_size', $setting->report_title_font_size) }}">
                            </div>
                            <div class="form-group col-md-3">
                                <label>Grade Scale Title</label>
                                <input type="text" name="grade_scale_title" class="form-control" value="{{ old('grade_scale_title', $setting->grade_scale_title) }}">
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group col-md-4">
                                <label>Annual Label</label>
                                <input type="text" name="annual_report_label" class="form-control" value="{{ old('annual_report_label', $setting->annual_report_label) }}">
                            </div>
                            <div class="form-group col-md-4">
                                <label>School Name Color</label>
                                <input type="color" name="school_name_color" class="form-control form-control-color" value="{{ old('school_name_color', $setting->school_name_color) }}">
                            </div>
                            <div class="form-group col-md-4">
                                <label>School Address Color</label>
                                <input type="color" name="school_address_color" class="form-control form-control-color" value="{{ old('school_address_color', $setting->school_address_color) }}">
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group col-md-4">
                                <label>Title Color</label>
                                <input type="color" name="report_title_color" class="form-control form-control-color" value="{{ old('report_title_color', $setting->report_title_color) }}">
                            </div>
                            <div class="form-group col-md-4">
                                <label>Annual Label Color</label>
                                <input type="color" name="annual_report_color" class="form-control form-control-color" value="{{ old('annual_report_color', $setting->annual_report_color) }}">
                            </div>
                            <div class="form-group col-md-4">
                                <label>Grade Border Color</label>
                                <input type="color" name="grade_border_color" class="form-control form-control-color" value="{{ old('grade_border_color', $setting->grade_border_color) }}">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card shadow-sm mb-4">
                    <div class="card-header font-weight-bold bg-dark text-white">Table Colors</div>
                    <div class="card-body">
                        <div class="form-row">
                            <div class="form-group col-md-4">
                                <label>Table Border Color</label>
                                <input type="color" name="table_border_color" class="form-control form-control-color" value="{{ old('table_border_color', $setting->table_border_color) }}">
                            </div>
                            <div class="form-group col-md-4">
                                <label>Header Background</label>
                                <input type="color" name="table_header_bg_color" class="form-control form-control-color" value="{{ old('table_header_bg_color', $setting->table_header_bg_color) }}">
                            </div>
                            <div class="form-group col-md-4">
                                <label>Header Text Color</label>
                                <input type="color" name="table_header_text_color" class="form-control form-control-color" value="{{ old('table_header_text_color', $setting->table_header_text_color) }}">
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group col-md-4">
                                <label>Body Background</label>
                                <input type="color" name="table_body_bg_color" class="form-control form-control-color" value="{{ old('table_body_bg_color', $setting->table_body_bg_color) }}">
                            </div>
                            <div class="form-group col-md-4">
                                <label>Body Text Color</label>
                                <input type="color" name="table_body_text_color" class="form-control form-control-color" value="{{ old('table_body_text_color', $setting->table_body_text_color) }}">
                            </div>
                            <div class="form-group col-md-4">
                                <label>Position Border Color</label>
                                <input type="color" name="position_border_color" class="form-control form-control-color" value="{{ old('position_border_color', $setting->position_border_color) }}">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card shadow-sm mb-4">
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

                <div class="card shadow-sm mb-4">
                    <div class="card-header font-weight-bold bg-dark text-white">Labels and Text</div>
                    <div class="card-body">
                        <div class="form-row">
                            <div class="form-group col-md-3">
                                <label>Pair Heading 1</label>
                                <input type="text" name="pair_heading_1" class="form-control" value="{{ old('pair_heading_1', $setting->pair_heading_1) }}">
                            </div>
                            <div class="form-group col-md-3">
                                <label>Pair Heading 2</label>
                                <input type="text" name="pair_heading_2" class="form-control" value="{{ old('pair_heading_2', $setting->pair_heading_2) }}">
                            </div>
                            <div class="form-group col-md-3">
                                <label>Pair Heading 3</label>
                                <input type="text" name="pair_heading_3" class="form-control" value="{{ old('pair_heading_3', $setting->pair_heading_3) }}">
                            </div>
                            <div class="form-group col-md-3">
                                <label>Grand Total Label</label>
                                <input type="text" name="grand_total_label" class="form-control" value="{{ old('grand_total_label', $setting->grand_total_label) }}">
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group col-md-3">
                                <label>Highest Total Label</label>
                                <input type="text" name="highest_total_label" class="form-control" value="{{ old('highest_total_label', $setting->highest_total_label) }}">
                            </div>
                            <div class="form-group col-md-3">
                                <label>Position Label</label>
                                <input type="text" name="position_label_text" class="form-control" value="{{ old('position_label_text', $setting->position_label_text) }}">
                            </div>
                            <div class="form-group col-md-3">
                                <label>Promoted Text</label>
                                <input type="text" name="promoted_text" class="form-control" value="{{ old('promoted_text', $setting->promoted_text) }}">
                            </div>
                            <div class="form-group col-md-3">
                                <label>Class Teacher Label</label>
                                <input type="text" name="class_teacher_label" class="form-control" value="{{ old('class_teacher_label', $setting->class_teacher_label) }}">
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group col-md-3">
                                <label>Principal Label</label>
                                <input type="text" name="principal_label" class="form-control" value="{{ old('principal_label', $setting->principal_label) }}">
                            </div>
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
                        </div>

                        <div class="form-row">
                            <div class="form-group col-md-3">
                                <label>Improve Remark</label>
                                <input type="text" name="remark_improve_text" class="form-control" value="{{ old('remark_improve_text', $setting->remark_improve_text) }}">
                            </div>
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

                <div class="card shadow-sm mb-4">
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
                                <div class="form-group col-md-3">
                                    <label>{{ $label }} (%)</label>
                                    <input type="number" step="0.1" min="1" max="50" name="subject_column_widths[{{ $key }}]" class="form-control" value="{{ old('subject_column_widths.' . $key, data_get($setting->subject_column_widths, $key, $widthDefaults[$key])) }}">
                                </div>
                            @endforeach
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
    .yearly-template-page .yearly-template-preview-shell {
        width: 100%;
        max-width: none;
    }

    .yearly-template-page .yearly-template-preview-panel {
        width: 100%;
    }

    @media (max-width: 767.98px) {
        .yearly-template-page .yearly-template-preview-shell {
            max-width: 100%;
        }
    }
</style>
@endsection
