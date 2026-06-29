<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
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
