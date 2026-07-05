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
<div class="report-card shadow-sm" style="border-color: {{ $templateSettings->table_border_color }};">
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
