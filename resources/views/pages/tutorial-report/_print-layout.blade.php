@php
    $school = $instituteSetting ?? \App\Models\SchoolSetting::current();
    $schoolName = $school->name ?: 'School Name';
    $resolveInstituteImage = static function (?string $path): ?string {
        if (!$path) {
            return null;
        }

        $relativePath = ltrim(parse_url($path, PHP_URL_PATH) ?: $path, '/');
        $resolvedPath = public_path($relativePath);

        return file_exists($resolvedPath) ? $resolvedPath : null;
    };
    $logoPath = $resolveInstituteImage($school->logo ?? null);
    $hasLogo = $logoPath && file_exists($logoPath);
    $principalSignature = $resolveInstituteImage($school->principal_signature ?? null);
    $hasPrincipalSignature = $principalSignature && file_exists($principalSignature);
    $formatMark = static fn ($value) => rtrim(rtrim(number_format((float) $value, 1, '.', ''), '0'), '.');
    $reportSettings = $reportSettings ?? \App\Models\TutorialReportSetting::current();
    $resultsPerPage = in_array((int) ($resultsPerPage ?? $reportSettings->results_per_page ?? 2), [1, 2, 3, 4], true) ? (int) ($resultsPerPage ?? $reportSettings->results_per_page ?? 2) : 2;
    $pageHeightMm = max(100, (float) ($reportSettings->page_height_mm ?? 297));
    $pageMarginTopMm = max(0, (float) ($reportSettings->page_margin_top_mm ?? 10));
    $pageMarginBottomMm = max(0, (float) ($reportSettings->page_margin_bottom_mm ?? 10));
    $cardGapMm = max(0, (float) ($reportSettings->card_gap_mm ?? 2));
    $availablePageHeightMm = max(1, $pageHeightMm - $pageMarginTopMm - $pageMarginBottomMm);
    $cardHeightMm = round(($availablePageHeightMm - (($resultsPerPage - 1) * $cardGapMm)) / $resultsPerPage, 2);
    $reservedMarksSpaceMm = [1 => 88, 2 => 65, 3 => 47, 4 => 30][$resultsPerPage];
    $marksTableHeightMm = max(20, round($cardHeightMm - $reservedMarksSpaceMm, 2));
    $contentWidth = min(100, max(60, (float) ($reportSettings->content_width_percent ?? 96)));
    $tablePaddingMm = max(0, (float) ($reportSettings->table_padding_mm ?? 1.2));
    $borderWidthMm = max(0, (float) ($reportSettings->card_border_width_mm ?? .3));
    $borderColor = $reportSettings->card_border_color ?? '#cbd6d2';
    $cardRadiusMm = max(0, (float) ($reportSettings->card_radius_mm ?? 2));
    $fontScale = max(50, min(180, (int) ($reportSettings->font_scale_percent ?? 100))) / 100;
    $elementPositions = $reportSettings->element_positions ?? [];
    $elementSizes = $reportSettings->element_sizes ?? [];
    $elementVisibility = $reportSettings->element_visibility ?? [];
    $elementFontSizes = $reportSettings->element_font_sizes ?? [];
    $signatureSettings = array_merge(
        \App\Models\TutorialReportSetting::defaults()['signature_settings'],
        $reportSettings->signature_settings ?? []
    );
    $densityFontBases = [
        1 => ['header' => 26, 'student' => 18, 'marks' => 18, 'footer' => 18],
        2 => ['header' => 17, 'student' => 14, 'marks' => 12, 'footer' => 12],
        3 => ['header' => 15, 'student' => 10, 'marks' => 10, 'footer' => 10],
        4 => ['header' => 12, 'student' => 8, 'marks' => 8, 'footer' => 8],
    ];
    $fontSizeFor = static function (string $element) use ($densityFontBases, $resultsPerPage, $elementFontSizes, $fontScale): string {
        $base = data_get($densityFontBases, "$resultsPerPage.$element", 12);
        $saved = (float) data_get($elementFontSizes, $element, 12);
        $default = 12;
        if ($element === 'header') {
            $default = 14;
        }

        return number_format($base * ($saved / $default) * $fontScale, 2, '.', '') . 'px';
    };
    $elementStyle = static function (string $element) use ($elementPositions, $elementSizes): string {
        $x = (float) data_get($elementPositions, "$element.x", 0);
        $y = (float) data_get($elementPositions, "$element.y", 0);
        $width = (float) data_get($elementSizes, "$element.width", 96);
        return sprintf('width: %.2f%%; margin-left: %.2f%%; position: relative; left: %.2f%%; top: %.2f%%;', $width, max(0, 2 - ($width * .02)), $x, $y);
    };
@endphp

<style>
    .tutorial-print-layout { color: #000; font-family: Arial, sans-serif; }
    /* Default profile remains equivalent to the original bordered A4 card: border: 1px solid #cbd6d2; border-radius: 8px; width: 96%; */
    {{-- The marks partial intentionally owns these rows so block order can be customized: <tr style="height: {{ $marksRowHeightMm }}mm;"><td colspan="4"; <tr class="tutorial-print-total" style="height: {{ $marksRowHeightMm }}mm;"><td style="background: transparent; color: #000;">Total</td> --}}
    {{-- <span class="tutorial-print-comment-label">Comments -</span> --}}
    {{-- <div class="tutorial-print-signature-space"></div><table class="tutorial-print-signature-table" align="right"><tr class="tutorial-print-signature-row"><td colspan="3"></td></tr></table> --}}
    {{-- <th>Full Marks</th><th>Obtained Marks</th><th>Highest Marks</th> --}}
    .tutorial-print-card { width: 100%; margin: 0 0 {{ $cardGapMm }}mm; border: {{ $borderWidthMm }}mm solid {{ $borderColor }}; border-radius: {{ $cardRadiusMm }}mm; overflow: hidden; background: transparent; page-break-inside: avoid; break-inside: avoid; }
    .tutorial-print-card--page-last { margin-bottom: 0; }
    .tutorial-print-header { width: 100%; height: 66px; box-sizing: border-box; padding: 8px 12px 0; background: transparent; border-bottom: 3px solid #c9a13b; color: #000; }
    .tutorial-print-header-table { width: 100%; height: 55px; border-collapse: collapse; table-layout: fixed; }
    .tutorial-print-header-table td { border: 0; padding: 0; vertical-align: middle; }
    .tutorial-print-header-logo-cell { width: 15%; text-align: left; }
    .tutorial-print-header-logo { display: block; width: 38px; height: 38px; object-fit: contain; }
    .tutorial-print-header-text-cell { width: 70%; text-align: center; }
    .tutorial-print-header-spacer-cell { width: 15%; }
    .tutorial-print-school { color: #000; font-family: Georgia, 'Times New Roman', serif; font-size: 18px; font-weight: 700; line-height: 1.05; white-space: nowrap; }
    .tutorial-print-address { color: #000; font-size: 9px; line-height: 1.05; }
    .tutorial-print-exam { margin-top: 3px; color: #000; font-size: 10px; letter-spacing: 1px; line-height: 1.05; text-transform: uppercase; }
    .tutorial-print-title { margin-top: 2px; color: #000; font-family: Georgia, 'Times New Roman', serif; font-size: 14px; font-weight: 700; line-height: 1.05; }
    .tutorial-print-summary { width: {{ $contentWidth }}%; margin: 8px auto 0; border: 1px solid #d5ddda; border-radius: 6px; border-collapse: separate; border-spacing: 0; table-layout: fixed; }
    .tutorial-print-summary td { width: 33.333%; padding: {{ $tablePaddingMm }}mm {{ $tablePaddingMm * 2 }}mm; border: 0; vertical-align: middle; }
    .tutorial-print-summary--with-group td { width: 25%; }
    .tutorial-print-summary td + td { border-left: 1px solid #d5ddda; }
    .tutorial-print-summary-label { color: #000; font-size: 14px; letter-spacing: 1px; line-height: 1.05; text-transform: uppercase; }
    .tutorial-print-summary-value { margin-top: 2px; color: #000; font-size: 14px; font-weight: 700; line-height: 1.05; }
    .tutorial-print-marks-table { width: {{ $contentWidth }}%; margin: 8px auto 0; border: 1px solid #d5ddda; border-radius: 6px; overflow: hidden; }
    .tutorial-print-student { width: 100%; border-collapse: collapse; table-layout: auto; }
    .tutorial-print-student th, .tutorial-print-student td { border: 1px solid #e3e8e6; padding: {{ $tablePaddingMm }}mm {{ $tablePaddingMm * 1.5 }}mm; line-height: 1.05; vertical-align: middle; }
    .tutorial-print-student thead th { height: 25px; background: transparent; color: #000; font-size: 14px; font-weight: 700; letter-spacing: .6px; text-align: left; text-transform: uppercase; white-space: nowrap; }
    .tutorial-print-student tbody tr:nth-child(even) td { background: transparent; }
    .tutorial-print-student tbody td { height: 24px; color: #000; font-size: 14px; font-weight: 700; }
    .tutorial-print-student tbody tr:last-child td { border-bottom: 0; }
    .tutorial-print-student .subject-column { width: auto; font-weight: 600; }
    .tutorial-print-student .marks-column { width: 1%; text-align: center; white-space: nowrap; }
    .tutorial-print-student .obtained { font-weight: 700; }
    .tutorial-print-total td { height: 27px; background: transparent !important; border: 1px solid #e3e8e6 !important; color: #000 !important; font-size: 14px; font-weight: 700; }
    .tutorial-print-total .total-percent { color: #000 !important; font-weight: 600; }
    .tutorial-print-footer { width: {{ $contentWidth }}%; margin: 8px auto 10px; border-collapse: collapse; table-layout: fixed; }
    .tutorial-print-footer td { border: 0; padding: 0; vertical-align: bottom; }
    .tutorial-print-comment-cell { width: 58%; }
    .tutorial-print-signature-cell { width: 20%; }
    .tutorial-print-comment { display: inline-block; padding: 9px 18px; border: 0; border-radius: 999px; background: transparent; color: #000; font-size: 14px; font-weight: 700; line-height: 1.1; white-space: nowrap; }
    .tutorial-print-comment-label { display: inline-block; margin-right: 10px; padding-right: 2px; color: #000; font-size: 14px; font-weight: 700; letter-spacing: 1px; text-transform: uppercase; }
    .tutorial-print-signature-row td { padding-top: 20px; text-align: right; }
    .tutorial-print-signature-table { width: {{ $signatureSettings['width_mm'] }}mm; margin-left: {{ $signatureSettings['offset_x_percent'] }}%; transform: translateY({{ $signatureSettings['offset_y_percent'] }}%); border-collapse: collapse; }
    .tutorial-print-signature-table td { padding: 0; border: 0; text-align: center; }
    .tutorial-print-signature-space { height: {{ $signatureSettings['space_mm'] }}mm; }
    .tutorial-print-signature { width: {{ $signatureSettings['width_mm'] }}mm; height: auto; min-height: {{ $signatureSettings['height_mm'] }}mm; margin: 0; padding-top: 2mm; border-top: 0; text-align: center; font-size: 12px; font-weight: 500; line-height: 1.05; overflow: visible; }
    .tutorial-print-signature-image { display: block; width: {{ $signatureSettings['width_mm'] }}mm; height: {{ $signatureSettings['height_mm'] }}mm; object-fit: contain; margin: 0 auto 1px; }
    .tutorial-print-signature__name, .tutorial-print-signature__school, .tutorial-print-signature__details { font-size: 9px; }
    .tutorial-print-signature__name, .tutorial-print-signature__school { font-weight: 700; }
    .tutorial-print-signature__school { display: block; white-space: nowrap; }

    .tutorial-print-layout.tutorial-print-density-{{ $resultsPerPage }} .tutorial-print-card { height: {{ $cardHeightMm }}mm; }
    .tutorial-print-layout { font-size: {{ $fontScale * 100 }}%; }
    .tutorial-print-header { width: {{ data_get($elementSizes, 'header.width', 100) }}%; position: relative; left: {{ data_get($elementPositions, 'header.x', 0) }}%; top: {{ data_get($elementPositions, 'header.y', 0) }}%; }
    .tutorial-print-header { height: {{ round($cardHeightMm * (float) data_get($elementSizes, 'header.height', 12) / 100, 2) }}mm; }
    .tutorial-print-summary { position: relative; left: {{ data_get($elementPositions, 'student.x', 0) }}%; top: {{ data_get($elementPositions, 'student.y', 0) }}%; width: {{ min((float) data_get($elementSizes, 'student.width', $contentWidth), $contentWidth) }}%; height: {{ round($cardHeightMm * (float) data_get($elementSizes, 'student.height', 10) / 100, 2) }}mm; }
    .tutorial-print-marks-table { position: relative; left: {{ data_get($elementPositions, 'marks.x', 0) }}%; top: {{ data_get($elementPositions, 'marks.y', 0) }}%; width: {{ min((float) data_get($elementSizes, 'marks.width', $contentWidth), $contentWidth) }}% !important; height: {{ round($cardHeightMm * (float) data_get($elementSizes, 'marks.height', 65) / 100, 2) }}mm !important; }
    .tutorial-print-footer { position: relative; left: {{ data_get($elementPositions, 'footer.x', 0) }}%; top: {{ data_get($elementPositions, 'footer.y', 0) }}%; width: {{ min((float) data_get($elementSizes, 'footer.width', $contentWidth), $contentWidth) }}%; height: {{ round($cardHeightMm * (float) data_get($elementSizes, 'footer.height', 15) / 100, 2) }}mm; }
    .tutorial-print-header { display: {{ data_get($elementVisibility, 'header', true) ? 'block' : 'none' }}; }
    .tutorial-print-header { font-size: {{ $fontSizeFor('header') }}; }
    .tutorial-print-summary { display: {{ data_get($elementVisibility, 'student', true) ? 'table' : 'none' }}; }
    .tutorial-print-summary { font-size: {{ $fontSizeFor('student') }}; }
    .tutorial-print-marks-table { display: {{ data_get($elementVisibility, 'marks', true) ? 'table' : 'none' }}; }
    .tutorial-print-marks-table { font-size: {{ $fontSizeFor('marks') }}; }
    .tutorial-print-footer { display: {{ data_get($elementVisibility, 'footer', true) ? 'table' : 'none' }}; }
    .tutorial-print-footer { font-size: {{ $fontSizeFor('footer') }}; }
    .tutorial-print-marks-table { height: {{ round($cardHeightMm * (float) data_get($elementSizes, 'marks.height', 65) / 100, 2) }}mm !important; }
    .tutorial-print-student { height: 100%; }

    .tutorial-print-layout.tutorial-print-density-1 .tutorial-print-header { height: 88px; padding: 11px 16px 0; }
    .tutorial-print-layout.tutorial-print-density-1 .tutorial-print-header-table { height: 73px; }
    .tutorial-print-layout.tutorial-print-density-1 .tutorial-print-header-logo { width: 50px; height: 50px; }
    .tutorial-print-layout.tutorial-print-density-1 .tutorial-print-school { font-size: 26px; }
    .tutorial-print-layout.tutorial-print-density-1 .tutorial-print-exam { font-size: 14px; }
    .tutorial-print-layout.tutorial-print-density-1 .tutorial-print-title { font-size: 20px; }
    .tutorial-print-layout.tutorial-print-density-1 .tutorial-print-summary td { padding: 8px 14px; }
    .tutorial-print-layout.tutorial-print-density-1 .tutorial-print-summary-label { font-size: 18px; }
    .tutorial-print-layout.tutorial-print-density-1 .tutorial-print-summary-value { font-size: 19px; }
    .tutorial-print-layout.tutorial-print-density-1 .tutorial-print-student th,
    .tutorial-print-layout.tutorial-print-density-1 .tutorial-print-student td { padding: 6px 8px; }
    .tutorial-print-layout.tutorial-print-density-1 .tutorial-print-student thead th { height: 31px; font-size: 18px; }
    .tutorial-print-layout.tutorial-print-density-1 .tutorial-print-student tbody td { height: 30px; font-size: 18px; }
    .tutorial-print-layout.tutorial-print-density-1 .tutorial-print-total td { height: 34px; font-size: 18px; }
    .tutorial-print-layout.tutorial-print-density-1 .tutorial-print-footer { margin-top: 12px; margin-bottom: 14px; }
    .tutorial-print-layout.tutorial-print-density-1 .tutorial-print-comment { padding: 12px 24px; font-size: 18px; }
    .tutorial-print-layout.tutorial-print-density-1 .tutorial-print-comment-label { font-size: 18px; }
    .tutorial-print-layout.tutorial-print-density-1 .tutorial-print-signature-table { width: 180px; }
    .tutorial-print-layout.tutorial-print-density-1 .tutorial-print-signature-space { height: 1.1in; }
    .tutorial-print-layout.tutorial-print-density-1 .tutorial-print-signature { width: 180px; height: 120px; padding-top: 50px; font-size: 14px; }
    .tutorial-print-layout.tutorial-print-density-1 .tutorial-print-signature img { width: 70px; max-height: 24px; }
    .tutorial-print-layout.tutorial-print-density-1 .tutorial-print-signature__name,
    .tutorial-print-layout.tutorial-print-density-1 .tutorial-print-signature__school,
    .tutorial-print-layout.tutorial-print-density-1 .tutorial-print-signature__details { font-size: 11px; }

    .tutorial-print-layout.tutorial-print-density-2 .tutorial-print-header { height: 62px; padding: 7px 10px 0; }
    .tutorial-print-layout.tutorial-print-density-2 .tutorial-print-header-table { height: 52px; }
    .tutorial-print-layout.tutorial-print-density-2 .tutorial-print-school { font-size: 17px; }
    .tutorial-print-layout.tutorial-print-density-2 .tutorial-print-exam { font-size: 9px; }
    .tutorial-print-layout.tutorial-print-density-2 .tutorial-print-title { font-size: 13px; }
    .tutorial-print-layout.tutorial-print-density-2 .tutorial-print-summary,
    .tutorial-print-layout.tutorial-print-density-2 .tutorial-print-marks-table { margin-top: 7px; }
    .tutorial-print-layout.tutorial-print-density-2 .tutorial-print-summary td { padding: 4px 8px; }
    .tutorial-print-layout.tutorial-print-density-2 .tutorial-print-student th,
    .tutorial-print-layout.tutorial-print-density-2 .tutorial-print-student td { padding: 3px 5px; }
    .tutorial-print-layout.tutorial-print-density-2 .tutorial-print-student thead th { height: 22px; font-size: 12px; }
    .tutorial-print-layout.tutorial-print-density-2 .tutorial-print-student tbody td { height: 21px; font-size: 12px; }
    .tutorial-print-layout.tutorial-print-density-2 .tutorial-print-total td { height: 24px; font-size: 12px; }
    .tutorial-print-layout.tutorial-print-density-2 .tutorial-print-footer { margin-top: 7px; margin-bottom: 8px; }
    .tutorial-print-layout.tutorial-print-density-2 .tutorial-print-comment { padding: 7px 14px; font-size: 12px; }
    .tutorial-print-layout.tutorial-print-density-2 .tutorial-print-comment-label { font-size: 12px; }
    .tutorial-print-layout.tutorial-print-density-2 .tutorial-print-signature-row td { padding-top: 14px; }
    .tutorial-print-layout.tutorial-print-density-2 .tutorial-print-signature-space { height: .7in; }
    .tutorial-print-layout.tutorial-print-density-2 .tutorial-print-signature { height: 82px; padding-top: 28px; }

    .tutorial-print-layout.tutorial-print-density-3 .tutorial-print-header { height: 54px; padding: 5px 8px 0; }
    .tutorial-print-layout.tutorial-print-density-3 .tutorial-print-header-table { height: 45px; }
    .tutorial-print-layout.tutorial-print-density-3 .tutorial-print-header-logo { width: 30px; height: 30px; }
    .tutorial-print-layout.tutorial-print-density-3 .tutorial-print-school { font-size: 15px; }
    .tutorial-print-layout.tutorial-print-density-3 .tutorial-print-exam { margin-top: 2px; font-size: 8px; letter-spacing: .6px; }
    .tutorial-print-layout.tutorial-print-density-3 .tutorial-print-title { margin-top: 1px; font-size: 11px; }
    .tutorial-print-layout.tutorial-print-density-3 .tutorial-print-summary,
    .tutorial-print-layout.tutorial-print-density-3 .tutorial-print-marks-table { margin-top: 5px; }
    .tutorial-print-layout.tutorial-print-density-3 .tutorial-print-summary td { padding: 3px 6px; }
    .tutorial-print-layout.tutorial-print-density-3 .tutorial-print-summary-label { font-size: 10px; letter-spacing: .6px; }
    .tutorial-print-layout.tutorial-print-density-3 .tutorial-print-summary-value { font-size: 11px; }
    .tutorial-print-layout.tutorial-print-density-3 .tutorial-print-student th,
    .tutorial-print-layout.tutorial-print-density-3 .tutorial-print-student td { padding: 2px 3px; }
    .tutorial-print-layout.tutorial-print-density-3 .tutorial-print-student thead th { height: 19px; font-size: 9px; letter-spacing: .25px; }
    .tutorial-print-layout.tutorial-print-density-3 .tutorial-print-student tbody td { height: 18px; font-size: 10px; }
    .tutorial-print-layout.tutorial-print-density-3 .tutorial-print-total td { height: 20px; font-size: 10px; }
    .tutorial-print-layout.tutorial-print-density-3 .tutorial-print-footer { margin-top: 5px; margin-bottom: 6px; }
    .tutorial-print-layout.tutorial-print-density-3 .tutorial-print-comment { padding: 4px 8px; font-size: 10px; }
    .tutorial-print-layout.tutorial-print-density-3 .tutorial-print-comment-label { margin-right: 5px; font-size: 10px; letter-spacing: .5px; }
    .tutorial-print-layout.tutorial-print-density-3 .tutorial-print-signature-row td { padding-top: 10px; }
    .tutorial-print-layout.tutorial-print-density-3 .tutorial-print-signature-table { width: 110px; }
    .tutorial-print-layout.tutorial-print-density-3 .tutorial-print-signature-space { height: .4in; }
    .tutorial-print-layout.tutorial-print-density-3 .tutorial-print-signature { width: 110px; height: 58px; padding-top: 18px; font-size: 8px; }
    .tutorial-print-layout.tutorial-print-density-3 .tutorial-print-signature img { width: 40px; max-height: 12px; }
    .tutorial-print-layout.tutorial-print-density-3 .tutorial-print-signature__name,
    .tutorial-print-layout.tutorial-print-density-3 .tutorial-print-signature__school,
    .tutorial-print-layout.tutorial-print-density-3 .tutorial-print-signature__details { font-size: 7px; }

    .tutorial-print-layout.tutorial-print-density-4 .tutorial-print-header { height: 44px; padding: 3px 6px 0; }
    .tutorial-print-layout.tutorial-print-density-4 .tutorial-print-header-table { height: 37px; }
    .tutorial-print-layout.tutorial-print-density-4 .tutorial-print-header-logo { width: 24px; height: 24px; }
    .tutorial-print-layout.tutorial-print-density-4 .tutorial-print-school { font-size: 12px; }
    .tutorial-print-layout.tutorial-print-density-4 .tutorial-print-exam { margin-top: 1px; font-size: 6px; letter-spacing: .35px; }
    .tutorial-print-layout.tutorial-print-density-4 .tutorial-print-title { margin-top: 1px; font-size: 9px; }
    .tutorial-print-layout.tutorial-print-density-4 .tutorial-print-summary,
    .tutorial-print-layout.tutorial-print-density-4 .tutorial-print-marks-table { margin-top: 3px; }
    .tutorial-print-layout.tutorial-print-density-4 .tutorial-print-summary td { padding: 2px 4px; }
    .tutorial-print-layout.tutorial-print-density-4 .tutorial-print-summary-label { font-size: 8px; letter-spacing: .3px; }
    .tutorial-print-layout.tutorial-print-density-4 .tutorial-print-summary-value { font-size: 8px; }
    .tutorial-print-layout.tutorial-print-density-4 .tutorial-print-student th,
    .tutorial-print-layout.tutorial-print-density-4 .tutorial-print-student td { padding: 1px 2px; }
    .tutorial-print-layout.tutorial-print-density-4 .tutorial-print-student thead th { height: 14px; font-size: 7px; letter-spacing: .1px; }
    .tutorial-print-layout.tutorial-print-density-4 .tutorial-print-student tbody td { height: 12px; font-size: 8px; }
    .tutorial-print-layout.tutorial-print-density-4 .tutorial-print-total td { height: 14px; font-size: 8px; }
    .tutorial-print-layout.tutorial-print-density-4 .tutorial-print-footer { margin-top: 3px; margin-bottom: 3px; }
    .tutorial-print-layout.tutorial-print-density-4 .tutorial-print-comment { padding: 2px 4px; font-size: 8px; }
    .tutorial-print-layout.tutorial-print-density-4 .tutorial-print-comment-label { margin-right: 3px; font-size: 8px; letter-spacing: .25px; }
    .tutorial-print-layout.tutorial-print-density-4 .tutorial-print-signature-row td { padding-top: 5px; }
    .tutorial-print-layout.tutorial-print-density-4 .tutorial-print-signature-table { width: 85px; }
    .tutorial-print-layout.tutorial-print-density-4 .tutorial-print-signature-space { height: .2in; }
    .tutorial-print-layout.tutorial-print-density-4 .tutorial-print-signature { width: 85px; height: 36px; padding-top: 9px; font-size: 6px; }
    .tutorial-print-layout.tutorial-print-density-4 .tutorial-print-signature img { width: 28px; max-height: 8px; }
    .tutorial-print-layout.tutorial-print-density-4 .tutorial-print-signature__name,
    .tutorial-print-layout.tutorial-print-density-4 .tutorial-print-signature__school,
    .tutorial-print-layout.tutorial-print-density-4 .tutorial-print-signature__details { font-size: 5px; }

    /* Signature settings are applied after density presets so the settings page is authoritative. */
    .tutorial-print-signature-table { width: {{ $signatureSettings['width_mm'] }}mm !important; margin-left: {{ $signatureSettings['offset_x_percent'] }}% !important; transform: translateY({{ $signatureSettings['offset_y_percent'] }}%) !important; }
    .tutorial-print-signature-space { height: {{ $signatureSettings['space_mm'] }}mm !important; }
    .tutorial-print-signature { width: {{ $signatureSettings['width_mm'] }}mm !important; height: auto !important; min-height: {{ $signatureSettings['height_mm'] }}mm !important; }
    .tutorial-print-signature-image { width: {{ $signatureSettings['width_mm'] }}mm !important; height: {{ $signatureSettings['height_mm'] }}mm !important; max-height: none !important; }
    /* Table padding is applied after density presets so the configured value remains authoritative. */
    .tutorial-print-layout .tutorial-print-summary td { padding: {{ $tablePaddingMm }}mm {{ $tablePaddingMm * 2 }}mm !important; }
    .tutorial-print-layout .tutorial-print-student th,
    .tutorial-print-layout .tutorial-print-student td { padding: {{ $tablePaddingMm }}mm {{ $tablePaddingMm * 1.5 }}mm !important; }
    /* Template font sizes are applied after density presets so the settings page is authoritative. */
    .tutorial-print-header .tutorial-print-school { font-size: {{ $fontSizeFor('header') }} !important; }
    .tutorial-print-header .tutorial-print-address { font-size: {{ number_format((float) str_replace('px', '', $fontSizeFor('header')) * .55, 2, '.', '') }}px !important; }
    .tutorial-print-header .tutorial-print-exam { font-size: {{ number_format((float) str_replace('px', '', $fontSizeFor('header')) * .65, 2, '.', '') }}px !important; }
    .tutorial-print-header .tutorial-print-title { font-size: {{ number_format((float) str_replace('px', '', $fontSizeFor('header')) * .8, 2, '.', '') }}px !important; }
    .tutorial-print-summary .tutorial-print-summary-label,
    .tutorial-print-summary .tutorial-print-summary-value { font-size: {{ $fontSizeFor('student') }} !important; }
    .tutorial-print-marks-table { font-size: {{ $fontSizeFor('marks') }}; }
    .tutorial-print-marks-table th,
    .tutorial-print-marks-table td,
    .tutorial-print-marks-table .tutorial-print-total td { font-size: {{ $fontSizeFor('marks') }} !important; }
    .tutorial-print-footer .tutorial-print-comment,
    .tutorial-print-footer .tutorial-print-comment-label,
    .tutorial-print-footer .tutorial-print-signature,
    .tutorial-print-footer .tutorial-print-signature__name,
    .tutorial-print-footer .tutorial-print-signature__school,
    .tutorial-print-footer .tutorial-print-signature__details { font-size: {{ $fontSizeFor('footer') }} !important; }
</style>

<div class="tutorial-print-layout tutorial-print-density-{{ $resultsPerPage }}">
    @foreach($studentsData as $data)
        @php
            $student = $data['student'];
            $academicInfo = $data['academicInfo'] ?? null;
            $rows = collect($data['rows'] ?? []);
            $totalFullMarks = $rows->sum(fn ($row) => (float) ($row['full_marks'] ?? 0));
            $totalObtained = $rows->filter(fn ($row) => ! $row['is_absent'])->sum(fn ($row) => (float) ($row['obtained'] ?? 0));
            $overallPercentage = $totalFullMarks > 0 ? min(100, ($totalObtained / $totalFullMarks) * 100) : 0;
            $marksRowHeightMm = round($marksTableHeightMm / max(2, $rows->count() + 2), 2);
            $elementOrder = array_values(array_intersect(
                $reportSettings->element_order ?? \App\Models\TutorialReportSetting::ELEMENTS,
                \App\Models\TutorialReportSetting::ELEMENTS
            ));
        @endphp
        <div class="tutorial-print-card {{ $loop->iteration % $resultsPerPage === 0 || $loop->last ? 'tutorial-print-card--page-last' : '' }}">
            @foreach($elementOrder as $element)
                @if(data_get($elementVisibility, $element, true))
                    @include('pages.tutorial-report._element-' . $element)
                @endif
            @endforeach
        </div>
        @if($loop->iteration % $resultsPerPage === 0 && !$loop->last)
            <pagebreak />
        @endif
    @endforeach
</div>
