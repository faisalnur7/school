@php $content = ''; ob_start(); @endphp

<div class="section-title blue-bar">A. Operating Activities</div>
<table>
    <tbody>
        <tr><td>Total Income Received</td><td class="text-right green">{{ number_format($operatingIn, 2) }}</td></tr>
        <tr><td>Less: Total Expenses Paid</td><td class="text-right red">{{ number_format($operatingOut, 2) }}</td></tr>
    </tbody>
    <tfoot>
        <tr><td class="bold">Net Cash from Operations</td><td class="text-right bold {{ $netOperating >= 0 ? 'green' : 'red' }}">{{ number_format(abs($netOperating), 2) }}</td></tr>
    </tfoot>
</table>

<div class="section-title purple-bar" style="margin-top:10px">B. Financing Activities</div>
<table>
    <tbody>
        <tr><td>Capital Contributions</td><td class="text-right green">{{ number_format($financingIn, 2) }}</td></tr>
        <tr><td>Less: Withdrawals</td><td class="text-right red">{{ number_format($financingOut, 2) }}</td></tr>
    </tbody>
    <tfoot>
        <tr><td class="bold">Net Cash from Financing</td><td class="text-right bold {{ $netFinancing >= 0 ? 'green' : 'red' }}">{{ number_format(abs($netFinancing), 2) }}</td></tr>
    </tfoot>
</table>

<table style="margin-top:12px;border:2px solid #e2e8f0">
    <tbody>
        <tr style="background:#f8fafc"><td>Opening Cash Balance</td><td class="text-right bold">{{ number_format($openingCash, 2) }}</td></tr>
        <tr><td>Net Change in Cash (A + B)</td><td class="text-right bold {{ $netChange >= 0 ? 'green' : 'red' }}">{{ ($netChange >= 0 ? '+' : '') . number_format($netChange, 2) }}</td></tr>
        <tr style="background:#f0fdf4;border-top:2px solid #bbf7d0">
            <td class="bold">Closing Cash Balance</td>
            <td class="text-right bold {{ $closingCash >= 0 ? 'green' : 'red' }}" style="font-size:13px">{{ number_format($closingCash, 2) }}</td>
        </tr>
    </tbody>
</table>
@php $content = ob_get_clean(); @endphp
@include('pages.reports.pdf.layout', ['title' => 'Cash Flow Statement', 'subtitle' => $from->format('d/m/Y') . ' — ' . $to->format('d/m/Y')])
