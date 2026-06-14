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

@include('pages.generate-id-cards._styles', ['setting' => $setting])
</style>
</head>
<body>
    @include('pages.generate-id-cards._cards', ['students' => $students, 'setting' => $setting, 'renderForPdf' => true, 'cardType' => $cardType ?? 'id_card'])
</body>
</html>
