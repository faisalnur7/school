<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Salary Slip — {{ $payroll->employee->name }}</title>
<style>
    * { margin:0; padding:0; box-sizing:border-box; }
    body { font-family: Arial, sans-serif; font-size: 12px; padding: 20px; }
    .no-print { margin-bottom: 16px; }
    .slip { max-width: 600px; margin: 0 auto; border: 1px solid #ccc; }
    .slip-header { background: #1e3a5f; color: #fff; padding: 16px; text-align: center; }
    .slip-header h2 { font-size: 16px; margin-bottom: 4px; }
    .slip-header p  { font-size: 11px; opacity: .8; }
    .slip-body { padding: 16px; }
    .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 8px; margin-bottom: 16px; }
    .info-cell .lbl { font-size: 10px; color: #888; text-transform: uppercase; font-weight: bold; }
    .info-cell .val { font-size: 12px; font-weight: bold; }
    table { width: 100%; border-collapse: collapse; margin-bottom: 12px; }
    th { background: #f5f5f5; padding: 6px 8px; text-align: left; font-size: 11px; border: 1px solid #ddd; }
    td { padding: 5px 8px; border: 1px solid #ddd; font-size: 11px; }
    .total-row td { font-weight: bold; background: #f0f0f0; }
    .net-row td { font-weight: bold; font-size: 14px; background: #1e3a5f; color: #fff; }
    .footer { text-align: center; font-size: 10px; color: #aaa; padding: 10px; border-top: 1px dashed #ddd; }
    @media print { .no-print { display: none; } body { padding: 0; } }
</style>
</head>
<body>
<div class="no-print">
    <button onclick="window.print()" style="padding:8px 20px;background:#1e3a5f;color:#fff;border:none;border-radius:4px;cursor:pointer">🖨 Print Slip</button>
    <button onclick="window.close()" style="padding:8px 16px;background:#eee;border:1px solid #ccc;border-radius:4px;cursor:pointer;margin-left:8px">✕ Close</button>
</div>

<div class="slip">
    <div class="slip-header">
        <h2>SALARY SLIP</h2>
        <p>{{ date('F', mktime(0,0,0,$payroll->payroll_month,1)) }} {{ $payroll->payroll_year }}</p>
    </div>
    <div class="slip-body">
        <div class="info-grid">
            <div class="info-cell"><div class="lbl">Employee Name</div><div class="val">{{ $payroll->employee->name }}</div></div>
            <div class="info-cell"><div class="lbl">Employee ID</div><div class="val">{{ $payroll->employee->employee_id }}</div></div>
            <div class="info-cell"><div class="lbl">Designation</div><div class="val">{{ $payroll->employee->designation->name ?? '—' }}</div></div>
            <div class="info-cell"><div class="lbl">Department</div><div class="val">{{ $payroll->employee->department ?? '—' }}</div></div>
            <div class="info-cell"><div class="lbl">Payment Method</div><div class="val">{{ ucfirst(str_replace('_',' ',$payroll->payment_method)) }}</div></div>
            <div class="info-cell"><div class="lbl">Status</div><div class="val">{{ ucfirst($payroll->status) }}</div></div>
        </div>

        @php $s = $payroll->employee->salaryStructure; @endphp
        <table>
            <thead><tr><th>Earnings</th><th style="text-align:right">Amount (৳)</th></tr></thead>
            <tbody>
                <tr><td>Basic Salary</td><td style="text-align:right">{{ number_format($s?->basic_salary ?? 0, 2) }}</td></tr>
                <tr><td>House Rent</td><td style="text-align:right">{{ number_format($s?->house_rent ?? 0, 2) }}</td></tr>
                <tr><td>Medical Allowance</td><td style="text-align:right">{{ number_format($s?->medical_allowance ?? 0, 2) }}</td></tr>
                <tr><td>Transport Allowance</td><td style="text-align:right">{{ number_format($s?->transport_allowance ?? 0, 2) }}</td></tr>
                <tr><td>Special Allowance</td><td style="text-align:right">{{ number_format($s?->special_allowance ?? 0, 2) }}</td></tr>
                <tr><td>Bonus</td><td style="text-align:right">{{ number_format($s?->bonus ?? 0, 2) }}</td></tr>
                <tr class="total-row"><td>Gross Salary</td><td style="text-align:right">{{ number_format($payroll->gross_salary, 2) }}</td></tr>
                <tr><td>Other Deductions</td><td style="text-align:right; color:#dc2626">- {{ number_format($payroll->other_deductions, 2) }}</td></tr>
                <tr class="net-row"><td>NET SALARY</td><td style="text-align:right">৳ {{ number_format($payroll->net_salary, 2) }}</td></tr>
            </tbody>
        </table>
    </div>
    <div class="footer">This is a computer-generated salary slip. No signature required.</div>
</div>
</body>
</html>
