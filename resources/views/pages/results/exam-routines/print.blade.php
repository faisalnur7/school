<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Exam Routine - {{ $exam->name }}</title>
    @php($renderForPdf = false)
    @include('pages.results.exam-routines._document-styles')
</head>
<body>
    @include('pages.results.exam-routines._document')
    <script>
        window.addEventListener('load', function () {
            window.setTimeout(function () { window.print(); }, 250);
        });
    </script>
</body>
</html>
