<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<style>
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body { font-family: sans-serif; font-size: 11px; color: #1e293b; }

    .pdf-header { background: #1e293b; color: #fff; padding: 10px 14px; margin-bottom: 12px; }
    .pdf-header h1 { font-size: 15px; font-weight: 700; margin: 0; }
    .pdf-header .meta { font-size: 10px; color: #94a3b8; margin-top: 2px; }

    table { width: 100%; border-collapse: collapse; font-size: 11px; }
    th { background: #f1f5f9; color: #334155; padding: 6px 8px; text-align: left; border-bottom: 2px solid #e2e8f0; font-weight: 600; }
    td { padding: 5px 8px; border-bottom: 1px solid #e2e8f0; }
    tfoot td { background: #f8fafc; font-weight: 700; border-top: 2px solid #cbd5e1; }
    .text-right { text-align: right; }
    .text-center { text-align: center; }

    .green  { color: #16a34a; }
    .red    { color: #e11d48; }
    .muted  { color: #94a3b8; }
    .bold   { font-weight: 700; }

    .section-title { background: #f1f5f9; padding: 5px 8px; font-weight: 700; font-size: 11px;
                     color: #334155; border-left: 3px solid #1e293b; margin: 10px 0 0; }
    .section-title.green-bar { border-left-color: #16a34a; background: #f0fdf4; color: #166534; }
    .section-title.red-bar   { border-left-color: #e11d48; background: #fff1f2; color: #991b1b; }
    .section-title.blue-bar  { border-left-color: #2563eb; background: #eff6ff; color: #1d4ed8; }
    .section-title.purple-bar{ border-left-color: #7c3aed; background: #f5f3ff; color: #5b21b6; }

    .summary-box { border: 1px solid #e2e8f0; padding: 6px 10px; margin-bottom: 6px; }
    .summary-box .label { font-size: 10px; color: #64748b; }
    .summary-box .value { font-size: 16px; font-weight: 700; }

    .net-bar { background: #f1f5f9; border-top: 2px solid #e2e8f0; padding: 6px 10px;
               display: flex; justify-content: space-between; font-weight: 700; margin-top: 8px; }

    .two-col { display: table; width: 100%; }
    .two-col .col { display: table-cell; width: 50%; vertical-align: top; padding-right: 8px; }
    .two-col .col:last-child { padding-right: 0; padding-left: 8px; border-left: 1px solid #e2e8f0; }
</style>
</head>
<body>
<div class="pdf-header">
    <h1>{{ $title }}</h1>
    <div class="meta">{{ $subtitle ?? '' }} &nbsp;|&nbsp; Generated: {{ now()->format('d M Y, h:i A') }}</div>
</div>
{!! $content !!}
</body>
</html>
