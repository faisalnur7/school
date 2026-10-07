<style>
    * { box-sizing: border-box; }
    @page { size: 210mm 297mm; margin: 12mm; }
    body { margin: 0; color: #0f172a; font-family: Arial, Helvetica, sans-serif; font-size: 14px; }
    .routine-document { width: 100%; }
    .school-header-wrap { border: 1px solid #dbe3ee; border-top: 4px solid #1e3a5f; border-radius: 10px; padding: 10px 12px; margin-bottom: 16px; background: #fff; }
    .school-header-table { width: 100%; border-collapse: collapse; table-layout: fixed; }
    .school-header-table td { border: 0 !important; padding: 0 !important; vertical-align: middle; }
    .school-header-logo-cell { width: 72px; }
    .school-logo-box { width: 62px; height: 62px; border: 1px solid #cbd5e1; border-radius: 12px; text-align: center; vertical-align: middle; line-height: 60px; overflow: hidden; background: #f8fbff; }
    .school-logo-img { max-width: 60px; max-height: 60px; display: inline-block; vertical-align: middle; }
    .school-logo-fallback { color: #1e3a5f; font-size: 18px; font-weight: 700; }
    .school-header-info-cell { padding-left: 10px !important; }
    .school-title { color: #163b70; font-size: 18px; font-weight: 700; line-height: 1.2; }
    .school-line { color: #475569; font-size: 14px; line-height: 1.35; margin-top: 2px; }
    .document-title { margin: 0; color: #0f172a; font-size: 18px; text-align: center; font-weight: 700; }
    .document-subtitle { margin: 3px 0 12px; color: #475569; font-size: 14px; text-align: center; }
    .routine-summary { width: 100%; margin-bottom: 13px; border-collapse: collapse; table-layout: fixed; }
    .routine-summary td { width: 25%; padding: 6px 8px; border: 1px solid #dbe3ee; vertical-align: top; }
    .summary-label { color: #64748b; font-size: 14px; font-weight: 700; text-transform: uppercase; letter-spacing: .02em; }
    .summary-value { color: #0f172a; font-size: 14px; font-weight: 700; }
    .routine-table { width: 100%; border-collapse: collapse; table-layout: fixed; }
    .routine-table thead { display: table-header-group; }
    .routine-table th { padding: 7px 8px; color: #fff; background: #1e3a5f; border: 1px solid #1e3a5f; text-align: left; font-size: 14px; font-weight: 700; }
    .routine-table td { padding: 7px 8px; border: 1px solid #dbe3ee; color: #0f172a; vertical-align: middle; }
    .routine-table tr { page-break-inside: avoid; }
    .routine-table .subject { width: 38%; }
    .routine-table .date { width: 18%; }
    .routine-table .day { width: 19%; }
    .routine-table .time { width: 25%; white-space: nowrap; }
    .subject-name { font-weight: 700; }
    .routine-footer { margin-top: 14px; color: #64748b; font-size: 14px; text-align: right; }
    @if(!($renderForPdf ?? false))
    @media screen {
        body { min-width: 0; background: #eef2f7; padding: 24px; overflow-x: auto; }
        .routine-document { width: 210mm; min-height: 297mm; max-width: none; margin: 0 auto; padding: 12mm; background: #fff; border: 1px solid #cbd5e1; box-shadow: 0 8px 30px rgba(15, 23, 42, .12); }
    }
    @endif
    @media print {
        body { padding: 0; background: #fff; }
        .routine-document { width: 100%; min-height: 0; margin: 0; padding: 0; border: 0; box-shadow: none; }
    }
</style>
