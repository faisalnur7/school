@php
    $content = '';
    ob_start();
@endphp
<div class="two-col" style="width:100%; overflow:hidden;">

    <!-- Income -->
    <div style="width:48%; float:left;">
        <div class="section-title green-bar">Income</div>

        <table style="width:100%; border-collapse:collapse;">
            <thead>
                <tr>
                    <th>Category</th>
                    <th class="text-right">Amount</th>
                </tr>
            </thead>

            <tbody>
                @foreach ($incomeByCategory as $row)
                    <tr>
                        <td>{{ $row['name'] }}</td>
                        <td class="text-right">
                            {{ number_format($row['amount'], 2) }}
                        </td>
                    </tr>
                @endforeach
            </tbody>

            <tfoot>
                <tr>
                    <td class="bold">Total Income</td>
                    <td class="text-right green bold">
                        {{ number_format($totalIncome, 2) }}
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>

    <!-- Expense -->
    <div style="width:48%; float:right;">
        <div class="section-title red-bar">Expenditure</div>

        <table style="width:100%; border-collapse:collapse;">
            <thead>
                <tr>
                    <th>Category</th>
                    <th class="text-right">Amount</th>
                </tr>
            </thead>

            <tbody>
                @foreach ($expenseByCategory as $row)
                    <tr>
                        <td>{{ $row['name'] }}</td>
                        <td class="text-right">
                            {{ number_format($row['amount'], 2) }}
                        </td>
                    </tr>
                @endforeach
            </tbody>

            <tfoot>
                <tr>
                    <td class="bold">Total Expenditure</td>
                    <td class="text-right red bold">
                        {{ number_format($totalExpense, 2) }}
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>

</div>

<div style="clear:both;"></div>
<div class="net-bar"
    style="margin-top:10px;background:{{ $surplus >= 0 ? '#f0fdf4' : '#fff1f2' }};border-top:2px solid {{ $surplus >= 0 ? '#bbf7d0' : '#fecdd3' }}">
    <span>{{ $surplus >= 0 ? 'Surplus' : 'Deficit' }}</span>
    <span class="{{ $surplus >= 0 ? 'green' : 'red' }}"
        style="font-size:14px">{{ number_format(abs($surplus), 2) }}</span>
</div>
@php $content = ob_get_clean(); @endphp
@include('pages.reports.pdf.layout', [
    'title' => 'Income & Expenditure Statement',
    'subtitle' => 'Year: ' . $year,
])
