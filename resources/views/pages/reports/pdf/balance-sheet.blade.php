@php $content = ''; ob_start(); @endphp
<div class="two-col">
    <div class="col">
        <div class="section-title">Equity</div>
        <table>
            <tbody>
                <tr><td>Capital Contributions</td><td class="text-right green bold">{{ number_format($capital, 2) }}</td></tr>
                <tr><td>Less: Withdrawals</td><td class="text-right red">({{ number_format($withdrawals, 2) }})</td></tr>
                <tr><td>Net Income / (Loss)</td>
                    <td class="text-right {{ $netIncome >= 0 ? 'green' : 'red' }}">
                        {{ $netIncome >= 0 ? number_format($netIncome, 2) : '(' . number_format(abs($netIncome), 2) . ')' }}
                    </td>
                </tr>
            </tbody>
            <tfoot>
                <tr>
                    <td class="bold">Total Equity</td>
                    <td class="text-right bold {{ $equity >= 0 ? 'green' : 'red' }}" style="font-size:13px">{{ number_format($equity, 2) }}</td>
                </tr>
            </tfoot>
        </table>
    </div>
    <div class="col">
        <div class="section-title">Summary</div>
        <div class="summary-box" style="margin-top:6px">
            <div class="label">Total Income</div>
            <div class="value green">{{ number_format($capital + max(0, $netIncome), 2) }}</div>
        </div>
        <div class="summary-box">
            <div class="label">Total Expenses</div>
            <div class="value red">{{ number_format($withdrawals + abs(min(0, $netIncome)), 2) }}</div>
        </div>
    </div>
</div>
@php $content = ob_get_clean(); @endphp
@include('pages.reports.pdf.layout', ['title' => 'Balance Sheet', 'subtitle' => 'Year: ' . $year])
