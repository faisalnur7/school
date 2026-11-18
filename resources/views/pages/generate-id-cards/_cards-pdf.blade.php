{{--
    PDF rendering of ID / library / employee cards.

    mPDF understands neither CSS variables, flexbox nor grid, so this template is built from tables and
    literal values only. Every size, colour and switch from the settings page is resolved here in PHP.
    The screen / print version lives in _cards.blade.php.
--}}
@php
    $employeeMode = $employeeMode ?? false;
    $layout = $layout ?? [];
    $cardSettings = $cardSettings ?? null;
    $cardType = $cardType ?? 'id_card';
    $isLibraryCard = $cardType === 'library_card';

    $items = $employeeMode ? ($employees ?? collect()) : ($students ?? collect());
    $perPage = max(1, (int) ($layout['cardsPerPage'] ?? 4));
    $perRow = max(1, min($perPage, (int) ($layout['cardsPerRow'] ?? 2)));
    $cardW = (float) ($layout['cardWidthCm'] ?? 5.4);
    $cardH = (float) ($layout['cardHeightCm'] ?? 8.56);
    $gap = (float) ($layout['gridGapCm'] ?? 0.5);

    $unit = ($layout['cardDimensionUnit'] ?? 'cm') === 'px' ? 'px' : 'cm';
    $toCm = fn ($value) => round($unit === 'px' ? ((float) $value) / 96 * 2.54 : (float) $value, 3);
    $cfg = fn (string $key, $default) => $cardSettings?->{$key} ?? $default;
    $flag = fn (string $key, bool $default = true) => (bool) ($cardSettings?->{$key} ?? $default);
    $color = fn (string $key, string $default) => preg_match('/^#[0-9a-fA-F]{6}$/', (string) $cardSettings?->{$key}) ? $cardSettings->{$key} : $default;
    $align = fn (string $key, string $default) => in_array($cardSettings?->{$key} ?? $default, ['left', 'center', 'right'], true) ? ($cardSettings?->{$key} ?? $default) : $default;

    // ---- theme ----
    $transparent = (bool) $cfg('card_is_transparent', false);
    $solid = $cfg('card_color_type', 'gradient') === 'solid';
    $g1 = $color('card_color_gradient_1', '#1e3a5f');
    $g2 = $color('card_color_gradient_2', '#2563eb');
    $solidColor = $color('card_solid_color', '#1e3a5f');
    $accent = $solid ? $solidColor : $g1;
    $themeBg = $transparent
        ? ''
        : ($solid
            ? "background-color: {$solidColor};"
            : "background-color: {$g1}; background-gradient: linear {$g1} {$g2} 0 0 1 1;");

    // ---- colours ----
    $cSchoolName = $color('card_school_name_text_color', '#ffffff');
    $cSchoolDetail = $color('card_school_detail_text_color', '#e5e7eb');
    $cSlogan = $color('card_slogan_text_color', '#e5e7eb');
    $cTitle = $color('card_title_text_color', '#ffffff');
    $cName = $color('card_name_text_color', $accent === 'transparent' ? '#1e3a5f' : $accent);
    $cDetail = $color('card_student_detail_text_color', '#111827');
    $cNotice = $color('card_back_notice_text_color', '#94a3b8');
    $cFooter = $color('card_footer_text_color', '#e5e7eb');

    // ---- sizes (pt) ----
    $fit = function ($value, float $base, float $min, int $threshold = 22, float $step = 0.08): float {
        $values = is_array($value) ? $value : [$value];
        $longest = 0;
        foreach ($values as $item) {
            $longest = max($longest, mb_strlen(trim(strip_tags((string) $item))));
        }
        if ($longest <= $threshold) {
            return $base;
        }

        return round(max($min, $base - min(($longest - $threshold) * $step, $base - $min)), 2);
    };
    $sSchoolName = (float) $cfg('card_school_name_font_size', 7.2);
    $sSchoolDetail = (float) $cfg('card_school_detail_font_size', 5.4);
    $sSlogan = (float) $cfg('card_slogan_font_size', 4.8);
    $sTitle = (float) $cfg('card_title_font_size', 4.7);
    $sName = (float) $cfg('card_name_font_size', 7.2);
    $sDetail = max(1.0, (float) ($cardSettings?->card_student_detail_font_size ?: 4.6));
    $sDetailLabel = round(max(1.0, $sDetail * 0.9), 2);

    // ---- geometry (cm) ----
    $frontPad = $toCm($cfg('card_front_padding_value', 0.8));
    $backPad = $toCm($cfg('card_back_padding_value', 0.8));
    // Header padding is capped so a large value cannot push the header past its fixed height.
    $headerPad = ['front' => min(0.8, $toCm($cfg('card_front_header_padding_value', 0.3))), 'back' => min(0.8, $toCm($cfg('card_back_header_padding_value', 0.3)))];
    $photoW = $toCm($cfg('card_photo_width_value', 1.8));
    $photoH = $toCm($cfg('card_photo_height_value', 2.7));
    $logoSize = $toCm($cfg('card_logo_size_value', 0.8));
    $photoFit = $cfg('card_photo_fit', 'cover') === 'contain' ? 'contain' : 'cover';
    $logoFit = $cfg('card_logo_fit', 'contain') === 'cover' ? 'cover' : 'contain';
    $frontAlign = $align('card_front_alignment', 'center');
    $backAlign = $align('card_back_alignment', 'center');
    $detailAlign = $align('card_student_detail_alignment', 'left');

    // Footer / header heights are fixed fractions of the card: mPDF cannot "flex" the body to fill.
    $showFooterFront = $flag('card_show_footer_front');
    $showFooterBack = $flag('card_show_footer_back');
    $headerH = round(max(1.6, $cardH * 0.25), 2);
    $footerH = round(max(0.7, $cardH * 0.085), 2);
    $bodyH = fn (bool $footer) => round($cardH - $headerH - ($footer ? $footerH : 0) - 0.1, 2);

    // ---- positions dragged in the settings preview (mm) ----
    $positions = is_array($cardSettings?->card_element_positions ?? null) ? $cardSettings->card_element_positions : [];
    $pos = function (string $key) use ($positions): string {
        $x = (float) data_get($positions, "$key.x", 0);
        $y = (float) data_get($positions, "$key.y", 0);

        return ($x !== 0.0 || $y !== 0.0) ? "position: relative; left: {$x}mm; top: {$y}mm;" : '';
    };

    // ---- school / images ----
    $file = fn (?string $path) => ($path && file_exists(public_path($path))) ? public_path($path) : null;
    $logoFile = $file($cardSettings?->card_logo ?? null) ?? $file($setting?->logo ?? null);
    $logoImg = $logoFile ? \App\Support\CardImage::fit($logoFile, $logoSize, $logoSize, $logoFit) : null;
    $signatureFile = $file($cardSettings?->card_principal_signature ?? null);
    $signatureImg = $signatureFile ? \App\Support\CardImage::fit($signatureFile, 3.4, 0.85, 'contain') : null;
    $qrFile = $file($setting?->whatsapp_qr ?? null);
    $qrImg = $qrFile ? \App\Support\CardImage::fit($qrFile, 1.1, 1.1, 'contain') : null;

    $schoolName = $setting?->name ?? 'School Name';
    $address = $setting?->address;
    $slogan = $setting?->slogan;
    $principalLabel = $setting?->principal_designation ?: 'Principal';
    $frontTitle = $employeeMode ? 'EMPLOYEE ID' : ($isLibraryCard ? 'LIBRARY CARD' : 'STUDENT ID');
    $backBadge = $isLibraryCard ? 'LIBRARY' : 'BACK';
    $backSectionTitle = $isLibraryCard ? 'Student Details' : ($employeeMode ? 'Employee Details' : 'Parent / Guardian');
    $contactTitle = $isLibraryCard ? 'Library / School Contact' : 'School Contact';
    $phones = implode(', ', array_filter([$setting?->contact_number_1, $setting?->contact_number_2]));

    // ---- small HTML builders (everything user-supplied is escaped) ----
    $h = fn ($value) => e((string) $value);

    // One label/value line. Left aligned lines keep a fixed label column like the screen version.
    $line = function (string $label, $value, float $size, float $labelSize, string $textColor, string $a, float $labelW = 0.9) use ($h): string {
        $label = '<span style="font-size:' . $labelSize . 'pt; font-weight:bold; text-transform:uppercase; letter-spacing:0.04em; color:' . $textColor . ';">' . $h($label) . '</span>';
        $value = '<span style="font-size:' . $size . 'pt; font-weight:bold; color:' . $textColor . ';">' . $h($value) . '</span>';
        if ($a === 'left') {
            return '<table cellspacing="0" cellpadding="0" style="width:100%; margin-bottom:0.06cm;"><tr>'
                . '<td style="width:' . $labelW . 'cm; vertical-align:top; text-align:left;">' . $label . '</td>'
                . '<td style="vertical-align:top; text-align:left;">' . $value . '</td></tr></table>';
        }

        return '<div style="text-align:' . $a . '; margin-bottom:0.06cm;">' . $label . ' &nbsp;' . $value . '</div>';
    };

    // Width of the label column (cm): long labels such as SESSION / WHATSAPP need more room than ID / DOB.
    $labelCol = fn (array $rows, float $size) => round(max(0.9, collect($rows)->map(fn ($r) => mb_strlen((string) $r[0]))->max() * $size * 0.0353 * 0.78 + 0.15), 2);

    // mPDF only draws a complete border on a table cell, not on an inline span.
    $badge = fn (string $text, float $size, string $textColor) => '<table align="center" cellspacing="0" cellpadding="0" style="margin-top:0.12cm; border-collapse:separate;"><tr><td style="border:0.03cm solid ' . $textColor . '; padding:0.04cm 0.22cm; font-size:' . $size . 'pt; font-weight:bold; letter-spacing:0.08em; color:' . $textColor . '; text-align:center; white-space:nowrap;">' . $h($text) . '</td></tr></table>';

    $headerBlock = function (string $side, string $align, string $badgeText, float $badgeSize) use (
        $transparent, $themeBg, $headerH, $flag, $logoImg, $logoSize, $pos, $h, $schoolName, $address, $slogan, $badge, $cTitle,
        $cSchoolName, $cSchoolDetail, $cSlogan, $sSchoolName, $sSchoolDetail, $sSlogan, $fit, $cardW, $headerPad
    ): string {
        $out = '<tr><td style="height:' . $headerH . 'cm; ' . $themeBg . ' text-align:' . $align . '; vertical-align:middle; padding:' . $headerPad[$side] . 'cm;">';
        if ($flag("card_show_logo_{$side}") && $logoImg) {
            $out .= '<div style="' . $pos("{$side}_logo") . '"><img src="' . $logoImg . '" style="width:' . $logoSize . 'cm; height:' . $logoSize . 'cm;"></div>';
        }
        $out .= '<div style="font-size:' . $fit($schoolName, $sSchoolName, 5.2, 20, 0.08) . 'pt; font-weight:bold; text-transform:uppercase; letter-spacing:0.03em; color:' . $cSchoolName . '; ' . $pos("{$side}_school_name") . '">' . $h($schoolName) . '</div>';
        if ($side === 'front' && $flag('card_show_school_detail_front', false) && $address) {
            $out .= '<div style="font-size:' . $fit($address, $sSchoolDetail, 4.1, 30, 0.05) . 'pt; color:' . $cSchoolDetail . '; ' . $pos('front_school_detail') . '">' . $h($address) . '</div>';
        }
        if ($flag("card_show_slogan_{$side}") && $slogan) {
            $out .= '<div style="font-size:' . $fit($slogan, $sSlogan, 3.8, 26, 0.04) . 'pt; color:' . $cSlogan . '; ' . $pos("{$side}_slogan") . '">' . $h($slogan) . '</div>';
        }
        if ($flag("card_show_title_{$side}")) {
            $out .= '<div style="' . $pos("{$side}_title") . '">' . $badge($badgeText, $badgeSize, $cTitle) . '</div>';
        }

        return $out . '</td></tr>';
    };

    $footerRow = function (string $side, bool $on, string $content) use ($footerH, $themeBg, $cFooter, $pos): string {
        if (!$on) {
            return '';
        }

        return '<tr><td style="height:' . $footerH . 'cm; ' . $themeBg . ' text-align:center; vertical-align:middle; font-size:4.4pt; color:' . $cFooter . '; padding:0.05cm 0.18cm;"><div style="' . $pos("{$side}_footer") . '">' . $content . '</div></td></tr>';
    };

    $cardStyle = 'width:' . $cardW . 'cm; height:' . $cardH . 'cm; border:0.035cm solid #cbd5e1; background-color:#ffffff;';
@endphp
<div style="text-align:center;">
@foreach($items->chunk($perPage) as $pageChunk)
    @if(!$loop->first)<pagebreak />@endif
    <table align="center" cellspacing="0" cellpadding="0" style="border-collapse:separate;">
    @foreach($pageChunk->chunk($perRow) as $rowItems)
        <tr>
        @foreach($rowItems as $item)
            @php
                $ai = $employeeMode ? null : $item->academicInformations->first();
                $displayName = $employeeMode ? $item->name : $item->full_name_en;
                $displayId = $employeeMode ? $item->employee_id : $item->student_cid;
                $bnName = (!$employeeMode && $item->full_name_bn) ? $item->full_name_bn : null;

                $photoSource = $employeeMode ? $item->photo : $item->image;
                $placeholder = (!$employeeMode && $item->gender == \App\Models\Student::FEMALE) ? 'assets/img/female-placeholder.png' : 'assets/img/male-placeholder.png';
                $photoFile = $file($photoSource) ?? public_path($placeholder);
                $photoImg = \App\Support\CardImage::fit($photoFile, $photoW, $photoH, $photoFit);

                // front rows: [label, value]
                $frontRows = [['ID', $displayId]];
                if ($employeeMode) {
                    $frontRows[] = ['Role', $item->designation?->name ?? '—'];
                    $frontRows[] = ['Dept.', is_string($item->department) ? $item->department : ($item->department?->name ?? '—')];
                    $frontRows[] = ['Type', ucfirst((string) $item->employee_type)];
                    if ($item->joining_date) { $frontRows[] = ['Joined', $item->joining_date->format('d M Y')]; }
                    if ($item->phone) { $frontRows[] = ['Phone', $item->phone]; }
                } elseif ($ai) {
                    $frontRows[] = ['Class', ($ai->schoolClass?->name_en ?? '—') . ($ai->section ? ' / ' . $ai->section->name_en : '')];
                    if ($ai->roll) { $frontRows[] = ['Roll', $ai->roll]; }
                    if ($ai->group) { $frontRows[] = ['Group', $ai->group->name_en]; }
                    $frontRows[] = ['Session', $ai->academicSession?->name_en ?? '—'];
                }
                if (!$employeeMode && $item->date_of_birth) { $frontRows[] = ['DOB', $item->date_of_birth->format('d M Y')]; }
                if (!$employeeMode && $item->blood_group) { $frontRows[] = ['Blood', $item->blood_group_text]; }

                // back: personal block
                $detailRows = [];
                if ($employeeMode) {
                    $detailRows = array_values(array_filter([
                        ['Name', $displayName], ['ID', $displayId],
                        $item->designation ? ['Role', $item->designation->name] : null,
                        $item->department ? ['Dept.', is_string($item->department) ? $item->department : $item->department->name] : null,
                        ['Type', ucfirst((string) $item->employee_type)],
                        $item->joining_date ? ['Joined', $item->joining_date->format('d M Y')] : null,
                        $item->phone ? ['Phone', $item->phone] : null,
                    ]));
                } elseif ($isLibraryCard) {
                    $detailRows = array_values(array_filter([
                        ['Name', $displayName],
                        $displayId ? ['ID', $displayId] : null,
                        $ai?->schoolClass ? ['Class', $ai->schoolClass->name_en . ($ai->section ? ' / ' . $ai->section->name_en : '')] : null,
                        $ai?->section ? ['Section', $ai->section->name_en] : null,
                        $ai?->roll ? ['Roll', $ai->roll] : null,
                        $ai ? ['Session', $ai->academicSession?->name_en ?? '—'] : null,
                    ]));
                } else {
                    $detailRows = array_values(array_filter([
                        $item->father_name ? ['Father', $item->father_name] : null,
                        $item->mother_name ? ['Mother', $item->mother_name] : null,
                        ($item->father_phone || $item->mother_phone) ? ['Contact', implode(', ', array_filter([$item->father_phone, $item->mother_phone]))] : null,
                        $item->present_address ? ['Address', \Illuminate\Support\Str::limit($item->present_address, 46)] : null,
                    ]));
                }

                $contactRows = [];
                if ($flag('card_show_school_detail_back') && $address) { $contactRows[] = ['Address', \Illuminate\Support\Str::limit($address, 52)]; }
                if ($phones) { $contactRows[] = ['Contact', $phones]; }
                if ($setting?->whatsapp_number) { $contactRows[] = ['WhatsApp', $setting->whatsapp_number]; }
                if ($setting?->email) { $contactRows[] = ['Email', $setting->email]; }
                if ($setting?->website) { $contactRows[] = ['Web', $setting->website]; }

                $backValues = array_merge(
                    $flag('card_show_back_student_details') ? array_column($detailRows, 1) : [],
                    $flag('card_show_back_school_contact') ? array_column($contactRows, 1) : []
                );
                $backSize = $fit($backValues, $sDetail, 3.6, 26, 0.05);
                $backLabelSize = round(max(1.0, $backSize * 0.9), 2);
                $nameSize = $fit($displayName ?? '', $sName, 5.4, 20, 0.08);

                $frontFooterText = trim(implode(' &nbsp; ', array_filter([
                    $setting?->contact_number_1 ? 'Tel: ' . $h($setting->contact_number_1) : null,
                    $setting?->contact_number_2 ? 'Tel: ' . $h($setting->contact_number_2) : null,
                    $setting?->website ? 'Web: ' . $h($setting->website) : null,
                ])));
                $backFooterText = $setting?->whatsapp_number ? 'WhatsApp: ' . $h($setting->whatsapp_number) : '';

                // mPDF cannot clip overflowing content, so a card whose content is too tall would grow past its
                // fixed height. Estimate the content height and shrink spacing, photo and text until it fits.
                $ptCm = 0.03528;
                $lineH = fn (float $size) => $size * $ptCm * 1.15 + 0.065; // calibrated against rendered row pitch
                $vPad = $frontPad;
                $ph = $photoH;
                $pw = $photoW;
                $ds = $sDetail;
                $dl = $sDetailLabel;
                $availFront = $bodyH($showFooterFront);
                $needFront = function () use (&$vPad, &$ph, &$ds, $flag, $nameSize, $bnName, $frontRows, $ptCm, $lineH): float {
                    return 2 * $vPad + ($flag('card_show_photo_front') ? $ph : 0)
                        + ($nameSize * $ptCm * 1.2 + 0.1 + ($bnName ? 0.2 : 0)) + 0.28
                        + count($frontRows) * $lineH($ds);
                };
                if ($needFront() > $availFront) {
                    $vPad = min($vPad, 0.25);
                }
                for ($i = 0; $i < 14 && $needFront() > $availFront; $i++) {
                    if ($ph > $photoH * 0.7) {
                        $ph *= 0.94;
                        $pw *= 0.94;
                    } else {
                        $ds = max(2.6, $ds * 0.94);
                        $dl = max(2.4, $dl * 0.94);
                    }
                }
                $photoImg = \App\Support\CardImage::fit($photoFile, round($pw, 2), round($ph, 2), $photoFit);

                $backPadV = $backPad;
                $availBack = $bodyH($showFooterBack);
                $needBack = function () use (&$backPadV, &$backSize, $flag, $detailRows, $contactRows, $qrImg, $signatureImg, $lineH): float {
                    return 2 * $backPadV
                        + ($flag('card_show_back_student_details') && $detailRows ? 0.55 + count($detailRows) * $lineH($backSize) : 0)
                        + ($flag('card_show_back_school_contact') && $contactRows ? 0.65 + count($contactRows) * $lineH($backSize) : 0)
                        + ($flag('card_show_back_qr') && $qrImg ? 1.3 : 0)
                        + ($flag('card_show_back_notice') ? 0.9 : 0)
                        + ($flag('card_show_back_signature') ? ($signatureImg ? 1.5 : 0.7) : 0);
                };
                if ($needBack() > $availBack) {
                    $backPadV = min($backPadV, 0.25);
                }
                for ($i = 0; $i < 14 && $needBack() > $availBack; $i++) {
                    $backSize = max(3.0, $backSize * 0.94);
                }
                $backLabelSize = round(max(1.0, $backSize * 0.9), 2);
                $isLast = $loop->last;
            @endphp
            <td style="vertical-align:top; padding:0 {{ $isLast ? 0 : $gap }}cm {{ $gap }}cm 0;">
                <table cellspacing="0" cellpadding="0" style="border-collapse:separate;"><tr>
                    {{-- ================= FRONT ================= --}}
                    <td style="vertical-align:top; padding-right:{{ $gap }}cm;">
                        <table cellspacing="0" cellpadding="0" style="{{ $cardStyle }}">
                            {!! $headerBlock('front', $frontAlign, $frontTitle, $fit($frontTitle, $sTitle, 4.0, 14, 0.05)) !!}
                            <tr><td style="height:{{ $bodyH($showFooterFront) }}cm; vertical-align:top; text-align:{{ $frontAlign }}; padding:{{ $vPad }}cm {{ $frontPad }}cm;">
                                <table cellspacing="0" cellpadding="0" style="width:100%;">
                                    @if($flag('card_show_photo_front'))
                                        <tr><td style="text-align:center; {{ $pos('front_photo') }}"><img src="{{ $photoImg }}" style="width:{{ round($pw, 2) }}cm; height:{{ round($ph, 2) }}cm; border:0.03cm solid #e2e8f0;"></td></tr>
                                    @endif
                                    <tr><td style="text-align:center; padding-top:0.18cm; {{ $pos('front_name') }}">
                                        <span style="font-size:{{ $nameSize }}pt; font-weight:bold; color:{{ $cName }};">{{ $displayName }}</span>
                                        @if($bnName)<br><span style="font-size:5.4pt; color:{{ $cDetail }};">{{ $bnName }}</span>@endif
                                    </td></tr>
                                </table>
                                <table cellspacing="0" cellpadding="0" style="width:100%; margin:0.14cm 0 0.16cm;"><tr><td style="height:0.02cm; font-size:1pt; line-height:1pt; border-top:0.04cm solid {{ $accent === 'transparent' ? '#cbd5e1' : $accent }};">&nbsp;</td></tr></table>
                                <div style="{{ $pos('front_details') }}">
                                    @foreach($frontRows as [$label, $value])
                                        {!! $line($label, $value, $ds, $dl, $cDetail, $detailAlign, $labelCol($frontRows, $dl)) !!}
                                    @endforeach
                                </div>
                            </td></tr>
                            {!! $footerRow('front', $showFooterFront, $frontFooterText) !!}
                        </table>
                    </td>
                    {{-- ================= BACK ================= --}}
                    <td style="vertical-align:top;">
                        <table cellspacing="0" cellpadding="0" style="{{ $cardStyle }} border-color:#333333;">
                            {!! $headerBlock('back', $backAlign, $backBadge, $fit($backBadge, $sTitle, 4.0, 18, 0.04)) !!}
                            <tr><td style="height:{{ $bodyH($showFooterBack) }}cm; vertical-align:top; text-align:{{ $backAlign }}; padding:{{ $backPadV }}cm {{ $backPad }}cm;">
                                @if($flag('card_show_back_student_details') && $detailRows)
                                    <div style="{{ $pos('back_details') }}">
                                        <div style="text-align:center; font-size:{{ $fit([$backSectionTitle], $sTitle, 4.0, 18, 0.04) }}pt; font-weight:bold; text-transform:uppercase; letter-spacing:0.05em; color:{{ $transparent ? $cTitle : $accent }}; margin-bottom:0.1cm;">{{ $backSectionTitle }}</div>
                                        @foreach($detailRows as [$label, $value])
                                            {!! $line($label, $value, $backSize, $backLabelSize, $cDetail, $detailAlign, $labelCol($detailRows, $backLabelSize)) !!}
                                        @endforeach
                                    </div>
                                @endif
                                @if($flag('card_show_back_school_contact') && $contactRows)
                                    <div style="margin-top:0.14cm; {{ $pos('back_contact') }}">
                                        <div style="text-align:center; font-size:{{ $fit([$contactTitle], $sTitle, 4.0, 18, 0.04) }}pt; font-weight:bold; text-transform:uppercase; letter-spacing:0.05em; color:{{ $transparent ? $cTitle : $accent }}; margin-bottom:0.1cm;">{{ $contactTitle }}</div>
                                        @foreach($contactRows as [$label, $value])
                                            {!! $line($label, $value, $backSize, $backLabelSize, $cDetail, $detailAlign, $labelCol($contactRows, $backLabelSize)) !!}
                                        @endforeach
                                    </div>
                                @endif
                                @if($flag('card_show_back_qr') && $qrImg)
                                    <div style="text-align:center; margin-top:0.1cm; {{ $pos('back_qr') }}"><img src="{{ $qrImg }}" style="width:1.1cm; height:1.1cm;"></div>
                                @endif
                                @if($flag('card_show_back_notice'))
                                    <table cellspacing="0" cellpadding="0" style="width:100%; margin-top:0.18cm; {{ $pos('back_notice') }}"><tr><td style="text-align:center; border-top:0.025cm dashed #cbd5e1; padding-top:0.12cm; font-size:{{ $fit($address ?: $slogan ?: '', $sSchoolDetail, 4.1, 30, 0.05) }}pt; font-style:italic; color:{{ $cNotice }};">If found, please return to the school.</td></tr></table>
                                @endif
                                @if($flag('card_show_back_signature'))
                                    <table cellspacing="0" cellpadding="0" align="center" style="margin-top:0.2cm; {{ $pos('back_signature') }}">
                                        @if($signatureImg)<tr><td style="text-align:center;"><img src="{{ $signatureImg }}" style="width:3.4cm; height:0.85cm;"></td></tr>@endif
                                        <tr><td style="width:3.4cm; text-align:center; border-top:0.025cm solid #525252; padding-top:0.05cm; font-size:4.5pt; font-weight:bold; color:#3f3f46;">{{ $principalLabel }}</td></tr>
                                    </table>
                                @endif
                            </td></tr>
                            {!! $footerRow('back', $showFooterBack, $backFooterText) !!}
                        </table>
                    </td>
                </tr></table>
            </td>
        @endforeach
        </tr>
    @endforeach
    </table>
@endforeach
</div>
