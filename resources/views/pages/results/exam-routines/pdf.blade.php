<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    @php($renderForPdf = true)
    @include('pages.results.exam-routines._document-styles')
</head>
<body>
    @include('pages.results.exam-routines._document', ['renderForPdf' => true])
</body>
</html>
