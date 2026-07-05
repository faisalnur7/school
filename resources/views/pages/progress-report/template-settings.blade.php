@extends('layouts.master')

@section('contents')
@php
    $logoUrl = !empty($school->logo) ? asset($school->logo) : null;
    $previewTitle = $setting->report_title_text ?? 'Progress Report';
@endphp

<div class="container-fluid">
    <div class="bg-white rounded-2xl shadow p-5 mb-4">
        <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
            <div>
                <h3 class="mb-1 font-weight-bold text-slate-900">Terminal Result Template Settings</h3>
                <p class="mb-0 text-muted">Customize the classic terminal result layout, then use the saved settings in print and PDF output.</p>
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

    <div class="row">
        <div class="col-12 col-xl-7">
            <form method="POST" action="{{ route('result.progress-report.template-settings.update') }}">
                @csrf

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
                                <label>Margin Top</label>
                                <input type="number" step="0.1" name="margin_top_mm" class="form-control" value="{{ old('margin_top_mm', $setting->margin_top_mm) }}">
                            </div>
                            <div class="form-group col-md-2">
                                <label>Margin Right</label>
                                <input type="number" step="0.1" name="margin_right_mm" class="form-control" value="{{ old('margin_right_mm', $setting->margin_right_mm) }}">
                            </div>
                            <div class="form-group col-md-2">
                                <label>Margin Bottom</label>
                                <input type="number" step="0.1" name="margin_bottom_mm" class="form-control" value="{{ old('margin_bottom_mm', $setting->margin_bottom_mm) }}">
                            </div>
                            <div class="form-group col-md-2">
                                <label>Margin Left</label>
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
                                <label>Logo Max Width (mm)</label>
                                <input type="number" step="0.1" min="8" max="40" name="school_logo_max_width_mm" class="form-control" value="{{ old('school_logo_max_width_mm', $setting->school_logo_max_width_mm) }}">
                            </div>
                            <div class="form-group col-md-3 d-flex align-items-end">
                                <div class="custom-control custom-checkbox mr-3">
                                    <input type="checkbox" class="custom-control-input" id="showWatermark" name="show_watermark" value="1" @checked(old('show_watermark', $setting->show_watermark))>
                                    <label class="custom-control-label" for="showWatermark">Watermark</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

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
                                <label>Title Color</label>
                                <input type="color" name="report_title_color" class="form-control form-control-color" value="{{ old('report_title_color', $setting->report_title_color) }}">
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group col-md-4">
                                <label>School Name Size</label>
                                <input type="number" step="0.1" name="school_name_font_size" class="form-control" value="{{ old('school_name_font_size', $setting->school_name_font_size) }}">
                            </div>
                            <div class="form-group col-md-4">
                                <label>School Name Color</label>
                                <input type="color" name="school_name_color" class="form-control form-control-color" value="{{ old('school_name_color', $setting->school_name_color) }}">
                            </div>
                            <div class="form-group col-md-4">
                                <label>School Address Size</label>
                                <input type="number" step="0.1" name="school_address_font_size" class="form-control" value="{{ old('school_address_font_size', $setting->school_address_font_size) }}">
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group col-md-4">
                                <label>School Address Color</label>
                                <input type="color" name="school_address_color" class="form-control form-control-color" value="{{ old('school_address_color', $setting->school_address_color) }}">
                            </div>
                            <div class="form-group col-md-4">
                                <label>Header Border Color</label>
                                <input type="color" name="header_border_color" class="form-control form-control-color" value="{{ old('header_border_color', $setting->header_border_color) }}">
                            </div>
                            <div class="form-group col-md-4">
                                <label>Card Border Color</label>
                                <input type="color" name="card_border_color" class="form-control form-control-color" value="{{ old('card_border_color', $setting->card_border_color) }}">
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group col-md-4">
                                <label>Show Grade Scale</label>
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="showGradeScale" name="show_grade_scale" value="1" @checked(old('show_grade_scale', $setting->show_grade_scale))>
                                    <label class="custom-control-label" for="showGradeScale">Enabled</label>
                                </div>
                            </div>
                            <div class="form-group col-md-4">
                                <label>Show Student Info</label>
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="showStudentInfo" name="show_student_info" value="1" @checked(old('show_student_info', $setting->show_student_info))>
                                    <label class="custom-control-label" for="showStudentInfo">Enabled</label>
                                </div>
                            </div>
                            <div class="form-group col-md-4">
                                <label>Show Print Date</label>
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="showPrintDate" name="show_print_date" value="1" @checked(old('show_print_date', $setting->show_print_date))>
                                    <label class="custom-control-label" for="showPrintDate">Enabled</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header bg-dark text-white font-weight-bold">Table & Colors</div>
                    <div class="card-body">
                        <div class="form-row">
                            <div class="form-group col-md-4">
                                <label>Header Background</label>
                                <input type="color" name="table_header_bg_color" class="form-control form-control-color" value="{{ old('table_header_bg_color', $setting->table_header_bg_color) }}">
                            </div>
                            <div class="form-group col-md-4">
                                <label>Header Text Color</label>
                                <input type="color" name="table_header_text_color" class="form-control form-control-color" value="{{ old('table_header_text_color', $setting->table_header_text_color) }}">
                            </div>
                            <div class="form-group col-md-4">
                                <label>Table Border Color</label>
                                <input type="color" name="table_border_color" class="form-control form-control-color" value="{{ old('table_border_color', $setting->table_border_color) }}">
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group col-md-4">
                                <label>Alternate Row Color</label>
                                <input type="color" name="table_row_alt_bg_color" class="form-control form-control-color" value="{{ old('table_row_alt_bg_color', $setting->table_row_alt_bg_color) }}">
                            </div>
                            <div class="form-group col-md-4">
                                <label>Student Label Color</label>
                                <input type="color" name="student_label_color" class="form-control form-control-color" value="{{ old('student_label_color', $setting->student_label_color) }}">
                            </div>
                            <div class="form-group col-md-4">
                                <label>Student Value Color</label>
                                <input type="color" name="student_value_color" class="form-control form-control-color" value="{{ old('student_value_color', $setting->student_value_color) }}">
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group col-md-4">
                                <label>Summary Background</label>
                                <input type="color" name="summary_bg_color" class="form-control form-control-color" value="{{ old('summary_bg_color', $setting->summary_bg_color) }}">
                            </div>
                            <div class="form-group col-md-4">
                                <label>Summary Text Color</label>
                                <input type="color" name="summary_text_color" class="form-control form-control-color" value="{{ old('summary_text_color', $setting->summary_text_color) }}">
                            </div>
                            <div class="form-group col-md-4">
                                <label>Signature Line Color</label>
                                <input type="color" name="signature_line_color" class="form-control form-control-color" value="{{ old('signature_line_color', $setting->signature_line_color) }}">
                            </div>
                        </div>
                    </div>
                </div>

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

                <div class="card mb-4">
                    <div class="card-header bg-dark text-white font-weight-bold">Visibility & Copy</div>
                    <div class="card-body">
                        <div class="form-row">
                            <div class="form-group col-md-3">
                                <label>Show Summary</label>
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="showSummary" name="show_summary" value="1" @checked(old('show_summary', $setting->show_summary))>
                                    <label class="custom-control-label" for="showSummary">Enabled</label>
                                </div>
                            </div>
                            <div class="form-group col-md-3">
                                <label>Show Remarks</label>
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="showRemarks" name="show_remarks" value="1" @checked(old('show_remarks', $setting->show_remarks))>
                                    <label class="custom-control-label" for="showRemarks">Enabled</label>
                                </div>
                            </div>
                            <div class="form-group col-md-3">
                                <label>Show Comments</label>
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="showComments" name="show_comments" value="1" @checked(old('show_comments', $setting->show_comments))>
                                    <label class="custom-control-label" for="showComments">Enabled</label>
                                </div>
                            </div>
                            <div class="form-group col-md-3">
                                <label>Show Signature</label>
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="showSignature" name="show_signature" value="1" @checked(old('show_signature', $setting->show_signature))>
                                    <label class="custom-control-label" for="showSignature">Enabled</label>
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

                        <div class="form-group">
                            <label>Excellent Comment</label>
                            <textarea name="comments_excellent_text" rows="2" class="form-control">{{ old('comments_excellent_text', $setting->comments_excellent_text) }}</textarea>
                        </div>
                        <div class="form-group">
                            <label>Good Comment</label>
                            <textarea name="comments_good_text" rows="2" class="form-control">{{ old('comments_good_text', $setting->comments_good_text) }}</textarea>
                        </div>
                        <div class="form-group mb-0">
                            <label>Default Comment</label>
                            <textarea name="comments_default_text" rows="2" class="form-control">{{ old('comments_default_text', $setting->comments_default_text) }}</textarea>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary btn-lg">
                    <i class="fas fa-save mr-1"></i> Save Settings
                </button>
            </form>
        </div>

        <div class="col-12 col-xl-5">
            <div class="card shadow-sm sticky-top" style="top: 1rem;">
                <div class="card-header bg-white font-weight-bold">Live Preview</div>
                <div class="card-body" id="progressTemplatePreview" style="background: #f8fafc;">
                    <div class="border rounded-lg p-3 bg-white" style="border-color: {{ $setting->card_border_color }};">
                        <div class="d-flex justify-content-between align-items-start mb-3" style="border-bottom: 1px solid {{ $setting->header_border_color }}; padding-bottom: .75rem;">
                            <div class="d-flex align-items-center" style="gap:12px;">
                                @if($logoUrl)
                                    <img src="{{ $logoUrl }}" alt="Logo" style="width: {{ $setting->school_logo_max_width_mm }}mm; height: auto; object-fit: contain;">
                                @endif
                                <div>
                                    <div style="font-size: {{ $setting->school_name_font_size }}px; color: {{ $setting->school_name_color }}; font-weight: 800; text-transform: uppercase;">
                                        {{ $school->name ?? 'School Name' }}
                                    </div>
                                    <div style="font-size: {{ $setting->school_address_font_size }}px; color: {{ $setting->school_address_color }};">
                                        {{ $school->address ?? 'School Address' }}
                                    </div>
                                </div>
                            </div>
                            @if($setting->show_grade_scale)
                                <table class="table table-bordered table-sm mb-0" style="width: 160px; font-size: 10px;">
                                    <thead style="background: {{ $setting->table_header_bg_color }}; color: {{ $setting->table_header_text_color }};">
                                        <tr><th>Range</th><th>Grade</th><th>Point</th></tr>
                                    </thead>
                                    <tbody>
                                        @foreach($gradeScale as $grade)
                                            <tr>
                                                <td>{{ $grade['min'] }}-{{ $grade['max'] }}</td>
                                                <td>{{ $grade['letter'] }}</td>
                                                <td>{{ number_format($grade['gpa'], 1) }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            @endif
                        </div>

                        <div class="text-center font-italic font-weight-bold mb-3" style="font-size: {{ $setting->report_title_font_size }}px; color: {{ $setting->report_title_color }};">
                            {{ $previewTitle }}
                        </div>

                        @if($setting->show_student_info)
                            <div class="mb-3">
                                <div><strong style="color: {{ $setting->student_label_color }};">Name</strong> : <span style="color: {{ $setting->student_value_color }};">{{ $previewStudent['full_name_en'] }}</span></div>
                                <div><strong style="color: {{ $setting->student_label_color }};">Class</strong> : <span style="color: {{ $setting->student_value_color }};">{{ $previewStudent['class_name'] }}</span></div>
                                <div><strong style="color: {{ $setting->student_label_color }};">ID</strong> : <span style="color: {{ $setting->student_value_color }};">{{ $previewStudent['student_cid'] }}</span></div>
                            </div>
                        @endif

                        <table class="table table-bordered table-sm mb-3" style="font-size: 10px;">
                            <thead style="background: {{ $setting->table_header_bg_color }}; color: {{ $setting->table_header_text_color }};">
                                <tr>
                                    <th>Subject</th>
                                    <th>Full</th>
                                    <th>Obt.</th>
                                    <th>High</th>
                                    <th>Total</th>
                                    <th>Grade</th>
                                    <th>GP</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($sampleRows as $row)
                                    <tr>
                                        <td>{{ $row['subject_name'] }}</td>
                                        <td>{{ $row['full_marks'] }}</td>
                                        <td>{{ $row['obtained'] }}</td>
                                        <td>{{ $row['highest'] }}</td>
                                        <td>{{ $row['obtained'] }}</td>
                                        <td>{{ $row['grade'] }}</td>
                                        <td>{{ $row['gpa'] }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                        @if($setting->show_summary)
                            <div class="p-2 mb-3 text-white text-center" style="background: {{ $setting->summary_bg_color }};">
                                Summary: {{ $summary['obtained'] }}/{{ $summary['fullMarks'] }} | {{ number_format($summary['percentage'], 2) }}% | GPA {{ number_format($summary['gpa'], 2) }}
                            </div>
                        @endif

                        @if($setting->show_remarks)
                            <div class="mb-2">
                                <strong style="color: {{ $setting->remarks_title_color }};">Remarks:</strong>
                                <div style="color: {{ $setting->remarks_text_color }};">
                                    {{ $setting->remark_good_text }}
                                </div>
                            </div>
                        @endif

                        @if($setting->show_comments)
                            <ul class="mb-3 pl-3" style="color: {{ $setting->remarks_text_color }};">
                                <li>{{ $setting->comments_good_text }}</li>
                            </ul>
                        @endif

                        @if($setting->show_signature || $setting->show_print_date)
                            <div class="d-flex justify-content-between align-items-end">
                                <div>
                                    @if($setting->show_print_date)
                                        <div class="font-weight-bold">Published Date: {{ now()->format('d-m-Y') }}</div>
                                    @endif
                                    @if($setting->show_signature)
                                        <div class="mt-4 border-top" style="border-top-color: {{ $setting->signature_line_color }}; width: 120px;"></div>
                                        <div>Class Teacher</div>
                                    @endif
                                </div>
                                @if($setting->show_signature)
                                    <div class="text-right">
                                        <div class="border-top ml-auto" style="border-top-color: {{ $setting->signature_line_color }}; width: 120px;"></div>
                                        <div>Principal</div>
                                    </div>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
