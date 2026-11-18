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
    {{-- Same table-based, mPDF-safe card template as the student PDF, so every setting applies. --}}
    @include('pages.generate-id-cards._cards-pdf', ['students' => collect(), 'employees' => $employees, 'employeeMode' => true, 'setting' => $setting, 'cardSettings' => $cardSettings, 'cardType' => 'id_card', 'layout' => $layout])
</body>
</html>
