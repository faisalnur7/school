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
    color: #111827;
}

@include('pages.generate-id-cards._styles', ['setting' => $setting, 'renderForPdf' => true])
</style>
</head>
<body>
    @include('pages.generate-id-cards._cards', ['students' => $students, 'employees' => $employees ?? collect(), 'employeeMode' => $employeeMode ?? false, 'setting' => $setting, 'cardSettings' => $cardSettings ?? null, 'renderForPdf' => true, 'cardType' => $cardType ?? 'id_card', 'layout' => $layout ?? []])
</body>
</html>
