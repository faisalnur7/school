@php
    $content = '';
    ob_start();
@endphp
<div class="summary-box">
    <div class="label">Supplier Due Report</div>
    <div class="value">{{ $subtitle ?? 'All records' }}</div>
</div>

<table>
    <thead>
        <tr>
            <th style="width:5%">#</th>
            <th style="width:12%">Date</th>
            <th>Supplier</th>
            <th>Reference</th>
            <th class="text-right" style="width:11%">Total</th>
            <th class="text-right" style="width:11%">Paid</th>
            <th class="text-right" style="width:11%">Due</th>
            <th style="width:8%">Status</th>
        </tr>
    </thead>
    <tbody>
        @forelse($purchases as $i => $purchase)
            @php
                $status = strtolower($purchase->status ?? 'unpaid');
                $statusLabel = ucfirst($status);
                $statusColor = match ($status) {
                    'paid' => 'green',
                    'partial' => 'red',
                    default => 'muted',
                };
                $rowNo = $i + 1;
            @endphp
            <tr>
                <td>{{ $rowNo }}</td>
                <td>{{ $purchase->purchase_date?->format('d/m/Y') ?? '—' }}</td>
                <td>{{ $purchase->supplier?->name ?? '—' }}</td>
                <td>{{ $purchase->reference_no ?? '—' }}</td>
                <td class="text-right">{{ number_format((float) $purchase->total_amount, 2) }}</td>
                <td class="text-right">{{ number_format((float) ($purchase->paid_amount ?? 0), 2) }}</td>
                <td class="text-right">{{ number_format((float) ($purchase->due_amount ?? 0), 2) }}</td>
                <td class="{{ $statusColor }}">{{ $statusLabel }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="8" class="text-center muted">No supplier dues found.</td>
            </tr>
        @endforelse
    </tbody>
    <tfoot>
        <tr>
            <td class="bold" colspan="4">Total</td>
            <td class="text-right bold">{{ number_format($totals['amount'] ?? 0, 2) }}</td>
            <td class="text-right bold">{{ number_format($totals['paid'] ?? 0, 2) }}</td>
            <td class="text-right bold">{{ number_format($totals['due'] ?? 0, 2) }}</td>
            <td></td>
        </tr>
    </tfoot>
</table>
@php $content = ob_get_clean(); @endphp
@include('pages.reports.pdf.layout', [
    'title' => 'Supplier Due Report',
    'subtitle' => $subtitle ?? '',
])
