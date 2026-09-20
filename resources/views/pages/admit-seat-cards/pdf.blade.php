<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
@php
    $renderForPdf = true;
    $pdfPageWidthMm = $layout['pageWidthMm'] ?? 210;
    $pdfPageHeightMm = $layout['pageHeightMm'] ?? 297;
    $pdfMarginTopMm = $layout['marginTopMm'] ?? 10;
    $pdfMarginRightMm = $layout['marginRightMm'] ?? 6.35;
    $pdfMarginBottomMm = $layout['marginBottomMm'] ?? 4;
    $pdfMarginLeftMm = $layout['marginLeftMm'] ?? 6.35;
@endphp
<style>
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: Arial, Helvetica, sans-serif;
    background: #fff;
    color: #111;
}

@include('pages.admit-seat-cards._styles')
@page {
    size: {{ $pdfPageWidthMm }}mm {{ $pdfPageHeightMm }}mm;
    margin: {{ $pdfMarginTopMm }}mm {{ $pdfMarginRightMm }}mm {{ $pdfMarginBottomMm }}mm {{ $pdfMarginLeftMm }}mm;
}
</style>
</head>
<body>
    @include('pages.admit-seat-cards._cards', [
        'students' => $students,
        'setting' => $setting,
        'cardSettings' => $cardSettings ?? null,
        'renderForPdf' => true,
        'cardType' => $cardType ?? 'admit_card',
        'examType' => $examType ?? null,
        'selectedExam' => $selectedExam ?? null,
        'layout' => $layout ?? [],
    ])
</body>
</html>
