@extends('layouts.master')

@section('title', __('Payments'))

@section('contents')
    <div class="container-fluid py-3">
        <x-page-header :title="__('Payments')" :count="$payments->total()" :subtitle="__('Fee and inventory payments, newest first.')">
            @if(auth()->user()?->hasAnyPermission(['view_fees', 'view_card_collect_fees']))
                <x-btn :href="route('fees.collect')" variant="primary" icon="fas fa-plus">{{ __('Collect payment') }}</x-btn>
            @endif
        </x-page-header>

        <x-card>
            <x-filter-bar :action="route('payments.index')" :reset="route('payments.index')">
                <label class="ds-field">
                    <span class="ds-field__label">{{ __('Receipt, CID or name') }}</span>
                    <input type="search" name="q" value="{{ $q }}" class="ds-input" placeholder="{{ __('e.g. R-20261121-0015') }}">
                </label>
                <label class="ds-field">
                    <span class="ds-field__label">{{ __('From date') }}</span>
                    <input type="date" name="from" value="{{ $from ? \Carbon\Carbon::parse($from)->format('Y-m-d') : '' }}" class="ds-input">
                </label>
                <label class="ds-field">
                    <span class="ds-field__label">{{ __('To date') }}</span>
                    <input type="date" name="to" value="{{ $to ? \Carbon\Carbon::parse($to)->format('Y-m-d') : '' }}" class="ds-input">
                </label>
            </x-filter-bar>
        </x-card>

        <x-card :flush="true">
            <x-data-table :stack="true" :empty="$payments->isEmpty()" :empty-text="__('No payments match these filters.')" :caption="__('Payments')">
                <x-slot:head>
                    <th>{{ __('Student') }}</th>
                    <th>{{ __('Receipt no') }}</th>
                    <th>{{ __('Date') }}</th>
                    <th>{{ __('Items') }}</th>
                    <th>{{ __('Method') }}</th>
                    <th class="ds-num">{{ __('Amount (BDT)') }}</th>
                    <th>{{ __('Collected by') }}</th>
                    <th><span class="sr-only">{{ __('Actions') }}</span></th>
                </x-slot:head>

                @foreach ($payments as $payment)
                    @php
                        $student = $payment->student;
                        $academic = $student?->academicInformations->last();
                        $validDueItems = $payment->validInventoryDueItems();
                    @endphp
                    <tr>
                        <td>
                            <div class="d-flex justify-content-between" style="gap: var(--sp-3);">
                                <span class="font-weight-bold">{{ $student?->full_name_en ?? __('Unknown student') }}</span>
                                {{-- phones: amount sits next to the name (the Amount column is hidden) --}}
                                <span class="ds-show-sm ds-num font-weight-bold">৳{{ number_format($payment->calculated_amount, 2) }}</span>
                            </div>
                            <div class="ds-muted">
                                {{ $student?->student_cid }}
                                @if($academic)
                                    · {{ $academic->schoolClass?->name_en }}{{ $academic->section ? ' / ' . $academic->section->name_en : '' }}{{ $academic->group ? ' / ' . $academic->group->name_en : '' }}
                                @endif
                                <span class="ds-show-sm">· {{ $payment->receipt_no }} · {{ \Carbon\Carbon::parse($payment->payment_date)->format('d M Y') }}</span>
                            </div>
                        </td>
                        <td class="ds-nowrap ds-hide-sm" data-label="{{ __('Receipt no') }}">{{ $payment->receipt_no }}</td>
                        <td class="ds-nowrap ds-hide-sm" data-label="{{ __('Date') }}">{{ \Carbon\Carbon::parse($payment->payment_date)->format('d M Y') }}</td>
                        <td class="ds-table__tags">
                            @foreach ($payment->items as $item)
                                <span class="ds-tag ds-tag--primary">{{ $item->fee->feeSet->name ?? __('Fee') }}</span>
                            @endforeach
                            @foreach ($payment->inventorySale?->items ?? [] as $saleItem)
                                <span class="ds-tag ds-tag--info">{{ $saleItem->inventoryItem->name ?? __('Inventory') }}</span>
                            @endforeach
                            @foreach ($validDueItems as $dueItem)
                                <span class="ds-tag ds-tag--warning">{{ __('Due') }}: {{ $dueItem->inventorySaleItem?->inventoryItem?->name ?? __('Inventory') }}</span>
                            @endforeach
                        </td>
                        <td class="ds-hide-sm" data-label="{{ __('Method') }}"><span class="ds-tag">{{ ucfirst(str_replace('_', ' ', (string) $payment->payment_method)) }}</span></td>
                        <td class="ds-num font-weight-bold ds-hide-sm" data-label="{{ __('Amount (BDT)') }}">{{ number_format($payment->calculated_amount, 2) }}</td>
                        <td class="ds-hide-sm" data-label="{{ __('Collected by') }}">{{ $payment->collector->name ?? '—' }}</td>
                        <td class="ds-nowrap ds-table__actions">
                            <x-btn :href="route('payments.receipt', $payment->id)" target="_blank" rel="noopener" size="sm" icon="fas fa-print">{{ __('Receipt') }}</x-btn>
                        </td>
                    </tr>
                @endforeach

                <x-slot:footer>
                    @if($payments->hasPages()){{ $payments->links() }}@endif
                </x-slot:footer>
            </x-data-table>
        </x-card>
    </div>
@endsection
