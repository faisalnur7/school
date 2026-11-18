<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<style>
body {
    font-family: Arial, Helvetica, sans-serif;
    color: #111827;
    background: #ffffff;
}
</style>
</head>
<body>
    @include('pages.generate-id-cards._cards-pdf', ['students' => $students, 'employees' => $employees ?? collect(), 'employeeMode' => $employeeMode ?? false, 'setting' => $setting, 'cardSettings' => $cardSettings ?? null, 'cardType' => $cardType ?? 'id_card', 'layout' => $layout ?? []])
</body>
</html>
