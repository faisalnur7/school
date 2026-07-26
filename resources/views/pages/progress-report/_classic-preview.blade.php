@php
    $studentName = data_get($previewStudent, 'full_name_en', 'Student Name');
    $studentCid = data_get($previewStudent, 'student_cid', '0001');
    $studentClass = data_get($previewStudent, 'class_name', 'Class Name');
    $studentRoll = data_get($previewStudent, 'roll', '12');
    $studentRank = data_get($previewStudent, 'rank', '—');
    $studentSession = data_get($previewStudent, 'session', now()->format('Y') . '-' . (now()->format('Y') + 1));
    $studentDob = data_get($previewStudent, 'dob', '20-05-2010');
    $remarksText = $summary['gpa'] >= 4.0
        ? $templateSettings->remark_excellent_text
        : ($summary['gpa'] >= 3.0
            ? $templateSettings->remark_good_text
            : ($summary['gpa'] >= 2.0
                ? $templateSettings->remark_satisfactory_text
                : $templateSettings->remark_improve_text));
    $commentText = $summary['gpa'] >= 4.0
        ? $templateSettings->comments_excellent_text
        : ($summary['gpa'] >= 3.0
            ? $templateSettings->comments_good_text
            : $templateSettings->comments_default_text);
@endphp

<div class="design-a report-card-classic" style="
    border-color: {{ $templateSettings->card_border_color }};
    border-top-color: {{ $templateSettings->header_border_color }};
    --pr-header-border-color: {{ $templateSettings->header_border_color }};
    --pr-table-border: {{ $templateSettings->table_border_color }};
    --pr-watermark-opacity: {{ $templateSettings->watermark_opacity }};
    --pr-watermark-scale: {{ $templateSettings->watermark_scale }}%;
">
    @if($templateSettings->show_watermark && !empty($logoUrl))
        <div class="report-card-watermark">
            <img src="{{ $logoUrl }}" alt="" class="report-card-watermark__img" style="opacity: {{ $templateSettings->watermark_opacity }}; width: {{ $templateSettings->watermark_scale }}%;">
        </div>
    @endif

    <div class="classic-header-inner">
        <div class="classic-header-top">
            <div class="classic-header-brand">
                @if(!empty($logoUrl))
                    <div class="classic-header-logo">
                        <img src="{{ $logoUrl }}" alt="{{ $schoolName }} logo" style="max-width: {{ $templateSettings->school_logo_max_width_mm }}cm;">
                    </div>
                @endif
                <div class="classic-header-copy">
                    <h1 class="text-3xl font-bold uppercase tracking-wide mb-0" style="font-size: {{ $templateSettings->school_name_font_size }}px; color: {{ $templateSettings->school_name_color }};">
                        {{ $schoolName }}
                    </h1>
                    <p class="text-sm mt-1 mb-0" style="font-size: {{ $templateSettings->school_address_font_size }}px; color: {{ $templateSettings->school_address_color }};">
                        {{ $schoolAddress }}
                    </p>
                </div>
            </div>

            @if($templateSettings->show_grade_scale)
            <div class="classic-grade-table">
                <table class="text-xs border border-gray-700" style="border-color: {{ $templateSettings->table_border_color }};">
                    <thead style="background: {{ $templateSettings->table_header_bg_color }}; color: {{ $templateSettings->table_header_text_color }};">
                        <tr>
                            <th class="px-3 py-1 text-center">Range</th>
                            <th class="px-1 py-1 text-center">Grade</th>
                            <th class="px-1 py-1 text-center">Point</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($gradeScale as $grade)
                            <tr>
                                <td class="px-3 py-0 text-center">{{ $grade['min'] }}-{{ $grade['max'] }}</td>
                                <td class="px-1 py-0 text-center">{{ $grade['letter'] }}</td>
                                <td class="px-1 py-0 text-center">{{ number_format($grade['gpa'], 1) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif
        </div>

        <h2 class="text-2xl font-bold italic mt-5 uppercase text-center" style="font-size: {{ $templateSettings->report_title_font_size }}px; color: {{ $templateSettings->report_title_color }};">
            {{ $templateSettings->report_title_text }}
        </h2>
    </div>

    @if($templateSettings->show_student_info)
    <div class="mt-6 flex justify-between items-start">
        <div>
            <h3 class="font-bold text-xl underline" style="color: {{ $templateSettings->student_value_color }};">Preview Exam</h3>
            <div class="mt-4 space-y-1 text-sm">
                <p><span class="font-semibold" style="color: {{ $templateSettings->student_label_color }};">Name</span> : <span style="color: {{ $templateSettings->student_value_color }};">{{ $studentName }}</span></p>
                <p><span class="font-semibold" style="color: {{ $templateSettings->student_label_color }};">Class</span> : <span style="color: {{ $templateSettings->student_value_color }};">{{ $studentClass }}</span></p>
                <p><span class="font-semibold" style="color: {{ $templateSettings->student_label_color }};">ID</span> : <span style="color: {{ $templateSettings->student_value_color }};">{{ $studentCid }}</span></p>
                <p><span class="font-semibold" style="color: {{ $templateSettings->student_label_color }};">Rank</span> : <span style="color: {{ $templateSettings->student_value_color }};">{{ $studentRank }}</span></p>
                <p><span class="font-semibold" style="color: {{ $templateSettings->student_label_color }};">Roll</span> : <span style="color: {{ $templateSettings->student_value_color }};">{{ $studentRoll }}</span></p>
                <p><span class="font-semibold" style="color: {{ $templateSettings->student_label_color }};">Session</span> : <span style="color: {{ $templateSettings->student_value_color }};">{{ $studentSession }}</span></p>
                <p><span class="font-semibold" style="color: {{ $templateSettings->student_label_color }};">DOB</span> : <span style="color: {{ $templateSettings->student_value_color }};">{{ $studentDob }}</span></p>
            </div>
        </div>
    </div>
    @endif

    <div class="mt-6 overflow-x-auto">
        <table class="w-full text-sm border border-gray-700" style="border-color: {{ $templateSettings->table_border_color }};">
            <thead class="bg-gray-100 text-center">
                <tr>
                    <th class="px-3 py-2 text-left">Subjects</th>
                    <th class="px-3 py-2">Full Marks</th>
                    <th class="px-3 py-2">Obtained Marks</th>
                    <th class="px-3 py-2">Highest Marks</th>
                    <th class="px-3 py-2">Total Marks</th>
                    <th class="px-3 py-2">Letter Grade</th>
                    <th class="px-3 py-2">Grade Point</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($sampleRows as $row)
                    <tr>
                        <td class="px-3 py-2 font-medium">{{ $row['subject_name'] }}</td>
                        <td class="text-center">{{ number_format($row['full_marks'], 0) }}</td>
                        <td class="text-center">{{ number_format($row['obtained'], 0) }}</td>
                        <td class="text-center">{{ number_format($row['highest'], 0) }}</td>
                        <td class="text-center">{{ number_format($row['obtained'], 0) }}</td>
                        <td class="text-center">{{ $row['grade'] }}</td>
                        <td class="text-center">{{ number_format($row['gpa'], 1) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @if($templateSettings->show_summary)
    <div class="mt-6">
                <table class="w-full text-sm border border-gray-700" style="border-color: {{ $templateSettings->table_border_color }};">
                    <thead style="background: {{ $templateSettings->summary_bg_color }}; color: {{ $templateSettings->summary_text_color }};">
                        <tr>
                            <th class="px-3 py-2">Summary</th>
                    <th class="px-3 py-2">Total Exam Marks</th>
                    <th class="px-3 py-2">Obtained Total Marks/Percent</th>
                    <th class="px-3 py-2">GPA</th>
                    <th class="px-3 py-2">Letter Grade</th>
                </tr>
            </thead>
            <tbody>
                <tr class="text-center">
                    <td class="py-2"></td>
                    <td>{{ number_format($summary['fullMarks'], 0) }}</td>
                    <td>{{ number_format($summary['obtained'], 0) }} / {{ number_format($summary['percentage'], 2) }}%</td>
                    <td>{{ number_format($summary['gpa'], 2) }}</td>
                    <td>{{ $summary['grade'] }}</td>
                </tr>
                    </tbody>
                </table>
            </div>
            @endif

            @if (! is_null(data_get($previewStudent, 'rank')))
            <div class="mt-4 px-4 py-3 rounded-lg d-flex justify-content-between align-items-center" style="border:1px solid {{ $templateSettings->table_border_color }}; background: {{ $templateSettings->table_body_bg_color }};">
                <div class="font-weight-bold text-uppercase" style="letter-spacing:.08em; color: {{ $templateSettings->student_label_color }};">Position</div>
                <div class="font-weight-bold" style="font-size: 1.25rem; color: {{ $templateSettings->student_value_color }};">#{{ data_get($previewStudent, 'rank') }}</div>
            </div>
            @endif

            @if($templateSettings->show_remarks)
            <div class="mt-6 text-sm">
                <h4 class="font-bold underline mb-2" style="color: {{ $templateSettings->remarks_title_color }};">Remarks:</h4>
        <div class="space-y-1">
            <p class="inline-block bg-green-200 px-2 rounded" style="color: {{ $templateSettings->remarks_text_color }};">{{ $remarksText }}</p>
        </div>
    </div>
    @endif

    @if($templateSettings->show_comments)
    <div class="mt-6 border border-gray-400 p-4 text-sm">
        <ul class="list-disc pl-5 space-y-2">
            <li>{{ $studentName }} was present {{ $attendancePresent }} days out of {{ $attendanceTotal }} days.</li>
            <li>{{ $commentText }}</li>
        </ul>
    </div>
    @endif

    @if($templateSettings->show_signature || $templateSettings->show_print_date)
    <div class="mt-10 flex justify-between items-end text-sm">
        <div>
            @if($templateSettings->show_print_date)
                <p class="font-semibold">Published Date: {{ now()->format('d-m-Y') }}</p>
            @endif
            @if($templateSettings->show_signature)
                <div class="mt-12">
                    <div class="border-t w-40" style="border-top-color: {{ $templateSettings->signature_line_color }};"></div>
                    <p>Class Teacher</p>
                </div>
            @endif
        </div>
        @if($templateSettings->show_signature)
            <div class="text-right">
                <div class="border-t w-40 ml-auto" style="border-top-color: {{ $templateSettings->signature_line_color }};"></div>
                <p>Principal</p>
            </div>
        @endif
    </div>
    @endif
</div>
