@extends('layouts.master')

@section('styles')
    <style>
        .fees-report-page {
            width: 100%;
        }

        .fees-report-page .fees-report-shell {
            width: 100%;
            padding: 0.25rem 0 1.5rem;
        }

        .fees-report-page .fees-report-card {
            background: #ffffff;
            border: 1px solid #e7e5e4;
            border-radius: 18px;
            box-shadow: 0 10px 30px rgba(15, 23, 42, 0.04);
            padding: 0.95rem;
            margin-bottom: 1rem;
        }

        .fees-report-page .fees-report-form {
            display: flex;
            flex-direction: column;
            gap: 0.9rem;
        }

        .fees-report-page .fees-report-grid {
            display: grid;
            gap: 0.75rem;
        }

        .fees-report-page .fees-report-grid--primary {
            grid-template-columns: repeat(4, minmax(120px, 1fr)) auto;
        }

        .fees-report-page .fees-report-advanced-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 0.75rem;
        }

        .fees-report-page .fees-report-field label {
            display: block;
            margin-bottom: 0.35rem;
            font-size: 0.77rem;
            font-weight: 700;
            color: #6b7280;
        }

        .fees-report-page .fees-report-input,
        .fees-report-page .fees-report-select {
            width: 100%;
            min-height: 46px;
            border-radius: 12px;
            border: 1px solid #e5e7eb;
            background: #fff;
            color: #111827;
            font-size: 0.92rem;
            box-shadow: none;
        }

        .fees-report-page .fees-report-input:focus,
        .fees-report-page .fees-report-select:focus {
            border-color: #cbd5e1;
            box-shadow: 0 0 0 4px rgba(15, 23, 42, 0.05);
        }

        .fees-report-page .fees-report-filter-actions {
            display: inline-flex;
            align-items: center;
            justify-content: flex-end;
            gap: 0.65rem;
            flex-wrap: wrap;
            justify-self: end;
            align-self: end;
        }

        .fees-report-page .fees-report-filter-actions--submit {
            justify-content: flex-end;
        }

        .fees-report-page .fees-report-more-filters {
            display: flex;
            align-items: center;
            gap: 0.45rem;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            background: #fff;
            color: #374151;
            font-size: 0.9rem;
            font-weight: 600;
            padding: 0.7rem 0.95rem;
            white-space: nowrap;
        }

        .fees-report-page .fees-report-more-filters:hover {
            background: #f8fafc;
            color: #111827;
        }

        .fees-report-page .fees-report-filter-count {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 1.35rem;
            height: 1.35rem;
            padding: 0 0.35rem;
            border-radius: 999px;
            background: #111827;
            color: #fff;
            font-size: 0.72rem;
            font-weight: 700;
        }

        .fees-report-page .fees-report-advanced-filters {
            display: none;
            border-top: 1px solid #f1f5f9;
            padding-top: 0.9rem;
        }

        .fees-report-page .fees-report-advanced-filters:not(.hidden) {
            display: block;
        }

        .fees-report-page .fees-report-action-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            min-height: 44px;
            border-radius: 12px;
            font-weight: 600;
            font-size: 0.88rem;
            padding: 0.7rem 1rem;
            box-shadow: none;
        }

        .fees-report-page .fees-report-action-btn.btn-dark {
            background: #111111;
            border-color: #111111;
        }

        .fees-report-page .fees-report-action-btn.btn-outline-secondary {
            border-color: #d6d3d1;
            color: #374151;
            background: #fff;
        }

        .fees-report-page .fees-report-action-btn.btn-outline-secondary:hover {
            background: #f8fafc;
            color: #111827;
        }

        .fees-report-page .fees-report-print-header {
            display: none;
        }

        @media (max-width: 1199.98px) {
            .fees-report-page .fees-report-grid--primary,
            .fees-report-page .fees-report-advanced-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 767.98px) {
            .fees-report-page .fees-report-grid--primary,
            .fees-report-page .fees-report-advanced-grid {
                grid-template-columns: 1fr;
            }

            .fees-report-page .fees-report-filter-actions,
            .fees-report-page .fees-report-filter-actions--submit {
                width: 100%;
            }

            .fees-report-page .fees-report-filter-actions > *,
            .fees-report-page .fees-report-filter-actions--submit > * {
                width: 100%;
                justify-content: center;
            }
        }

        html[data-theme='dark'] .fees-report-page .fees-report-card {
            background: linear-gradient(180deg, rgba(17, 24, 39, 0.98) 0%, rgba(15, 23, 42, 0.96) 100%);
            border-color: rgba(148, 163, 184, 0.18);
            box-shadow: 0 10px 24px rgba(2, 6, 23, 0.26);
        }

        html[data-theme='dark'] .fees-report-page .fees-report-field label {
            color: #cbd5e1;
        }

        html[data-theme='dark'] .fees-report-page .fees-report-input,
        html[data-theme='dark'] .fees-report-page .fees-report-select {
            border-color: rgba(148, 163, 184, 0.2);
            background: rgba(15, 23, 42, 0.96);
            color: #e2e8f0;
        }

        html[data-theme='dark'] .fees-report-page .fees-report-input:focus,
        html[data-theme='dark'] .fees-report-page .fees-report-select:focus {
            border-color: rgba(96, 165, 250, 0.35);
            box-shadow: 0 0 0 4px rgba(96, 165, 250, 0.12);
        }

        html[data-theme='dark'] .fees-report-page .fees-report-more-filters {
            border-color: rgba(148, 163, 184, 0.18);
            background: rgba(15, 23, 42, 0.96);
            color: #e2e8f0;
        }

        html[data-theme='dark'] .fees-report-page .fees-report-more-filters:hover {
            background: #1e293b;
            color: #f8fafc;
        }

        html[data-theme='dark'] .fees-report-page .fees-report-filter-count {
            background: #1e293b;
            color: #f8fafc;
        }

        html[data-theme='dark'] .fees-report-page .fees-report-action-btn.btn-outline-secondary {
            border-color: rgba(148, 163, 184, 0.18);
            background: rgba(15, 23, 42, 0.96);
            color: #e2e8f0;
        }

        html[data-theme='dark'] .fees-report-page .fees-report-action-btn.btn-outline-secondary:hover {
            background: #1e293b;
            color: #f8fafc;
        }
    </style>
@endsection

@section('contents')
<div class="container-fluid fees-report-page">
    @include('partials.report-header')
    <div class="fees-report-shell">
        <div class="fees-report-card">
            <form method="GET" action="{{ route('fees.due-report') }}" class="fees-report-form">
                <div class="fees-report-grid fees-report-grid--primary">
                    <div class="fees-report-field">
                        <label class="font-weight-bold">Academic Year</label>
                        <select name="session_id" class="form-control fees-report-select report-filter-control">
                            <option value="">— Select Year —</option>
                            @foreach($sessions as $s)
                                <option value="{{ $s->id }}" {{ request('session_id') == $s->id ? 'selected' : '' }}>{{ $s->name_en }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="fees-report-field">
                        <label>Month <small class="text-muted">(leave blank for full year)</small></label>
                        <select name="month" class="form-control fees-report-select report-filter-control">
                            <option value="">All Year</option>
                            @foreach(range(1,12) as $m)
                                <option value="{{ $m }}" {{ request('month') == $m ? 'selected' : '' }}>{{ date('F', mktime(0,0,0,$m,1)) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="fees-report-field">
                        <label class="font-weight-bold">From Date</label>
                        <input type="date" name="from_date" value="{{ request('from_date') }}" class="form-control fees-report-input report-filter-control">
                    </div>
                    <div class="fees-report-field">
                        <label class="font-weight-bold">To Date</label>
                        <input type="date" name="to_date" value="{{ request('to_date') }}" class="form-control fees-report-input report-filter-control">
                    </div>
                    <div class="fees-report-filter-actions fees-report-filter-actions--submit">
                        <button type="submit" class="btn btn-dark fees-report-action-btn" title="Generate"><i class="fas fa-search"></i></button>
                        <a href="{{ route('fees.due-report') }}" class="btn btn-outline-secondary fees-report-action-btn" title="Reset"><i class="fas fa-times"></i></a>
                        @if((request('session_id') || (request('from_date') && request('to_date'))) && $classSections->isNotEmpty())
                            <button type="button" class="btn btn-success fees-report-action-btn" onclick="window.print()" title="Print"><i class="fas fa-print"></i></button>
                            <a href="{{ route('fees.due-report.pdf', request()->query()) }}" class="btn btn-danger fees-report-action-btn" title="Export PDF"><i class="fas fa-file-pdf"></i></a>
                        @endif
                    </div>
                </div>
            </form>
        </div>

        <div class="fees-report-card">
            <hr>

            @php
                $selectedSessionName = optional($sessions->firstWhere('id', request('session_id')))->name_en;
                $selectedMonthName = request('month') ? date('F', mktime(0, 0, 0, (int) request('month'), 1)) : null;
            @endphp

            <div class="fees-report-print-header">
                <div>
                    <h2 class="fees-report-print-title">Fee Due Report</h2>
                    <p class="fees-report-print-subtitle">
                        {{ $selectedSessionName ? 'Academic Year: '.$selectedSessionName : 'Academic Year: All' }}
                    </p>
                </div>
                <div class="fees-report-print-meta">
                    <div>Month: {{ $selectedMonthName ?? 'All Year' }}</div>
                    <div>
                        @if(request('from_date') && request('to_date'))
                            Date Range: {{ request('from_date') }} to {{ request('to_date') }}
                        @else
                            Date Range: All
                        @endif
                    </div>
                </div>
            </div>

            @if(!request('session_id') && !(request('from_date') && request('to_date')))
                <div class="text-center py-5 text-muted">
                    <i class="fas fa-filter fa-2x mb-2"></i>
                    <p class="mb-0">Select an Academic Year or a date range to generate the report.</p>
                </div>
            @elseif($classSections->isEmpty())
                <div class="text-center py-5 text-muted">
                    <i class="fas fa-inbox fa-2x mb-2"></i>
                    <p class="mb-0">No fee records found.</p>
                </div>
            @else
                {{-- Grand Summary Cards --}}
                <div class="row mb-4">
                    <div class="col-md-4">
                        <div class="info-box bg-light">
                            <span class="info-box-icon bg-info"><i class="fas fa-file-invoice-dollar"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Total Fees</span>
                                <span class="info-box-number">{{ number_format($grandTotals['fees'], 2) }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="info-box bg-light">
                            <span class="info-box-icon bg-success"><i class="fas fa-check-circle"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Total Paid</span>
                                <span class="info-box-number">{{ number_format($grandTotals['paid'], 2) }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="info-box bg-light">
                            <span class="info-box-icon bg-danger"><i class="fas fa-exclamation-circle"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Total Due</span>
                                <span class="info-box-number">{{ number_format($grandTotals['due'], 2) }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Group by class, then show one table per class with sections as rows --}}
                @php
                    $byClass = $classSections->groupBy('class_id');
                @endphp

                @foreach($byClass as $classId => $sections)
                    @php
                        $className   = $sections->first()->class_name;
                        $classFees   = $sections->sum('total_fees');
                        $classPaid   = $sections->sum('total_paid');
                        $classDue    = $sections->sum('due');

                        // Only show categories that have any fees in this class
                        $activeCategories = $categories->filter(function($cat) use ($sections) {
                            foreach ($sections as $sec) {
                                if (($sec->cat_totals[$cat->id]['fees'] ?? 0) > 0) return true;
                            }
                            return false;
                        });
                    @endphp

                    <div class="card mb-4 shadow-sm">
                        <div class="card-header py-2 d-flex justify-content-between align-items-center" style="background:#1a3c5e;">
                            <h5 class="mb-0 text-white"><i class="fas fa-school mr-1"></i> {{ $className }}</h5>
                            <div>
                                <span class="badge badge-light mr-1">Fees: {{ number_format($classFees, 2) }}</span>
                                <span class="badge badge-success mr-1">Paid: {{ number_format($classPaid, 2) }}</span>
                                <span class="badge badge-danger">Due: {{ number_format($classDue, 2) }}</span>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-bordered table-sm mb-0">
                                    <thead class="thead-dark">
                                        <tr>
                                            <th rowspan="2" class="align-middle">#</th>
                                            <th rowspan="2" class="align-middle">Section</th>
                                            @foreach($activeCategories as $cat)
                                                <th colspan="3" class="text-center">{{ $cat->name }}</th>
                                            @endforeach
                                            <th colspan="3" class="text-center bg-secondary">Total</th>
                                        </tr>
                                        <tr>
                                            @foreach($activeCategories as $cat)
                                                <th class="text-right text-nowrap small">Fees</th>
                                                <th class="text-right text-nowrap small">Paid</th>
                                                <th class="text-right text-nowrap small">Due</th>
                                            @endforeach
                                            <th class="text-right text-nowrap small bg-secondary">Fees</th>
                                            <th class="text-right text-nowrap small bg-secondary">Paid</th>
                                            <th class="text-right text-nowrap small bg-secondary">Due</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($sections as $i => $row)
                                            <tr class="{{ $row->due > 0 ? 'table-danger' : 'table-success' }}">
                                                <td>{{ $i + 1 }}</td>
                                                <td class="font-weight-bold">{{ $row->section_name }}</td>
                                                @foreach($activeCategories as $cat)
                                                    @php $ct = $row->cat_totals[$cat->id] ?? ['fees'=>0,'paid'=>0,'due'=>0]; @endphp
                                                    <td class="text-right">{{ $ct['fees'] > 0 ? number_format($ct['fees'], 2) : '—' }}</td>
                                                    <td class="text-right text-success">{{ $ct['paid'] > 0 ? number_format($ct['paid'], 2) : '—' }}</td>
                                                    <td class="text-right {{ $ct['due'] > 0 ? 'text-danger font-weight-bold' : 'text-success' }}">{{ $ct['fees'] > 0 ? number_format($ct['due'], 2) : '—' }}</td>
                                                @endforeach
                                                <td class="text-right font-weight-bold">{{ number_format($row->total_fees, 2) }}</td>
                                                <td class="text-right text-success font-weight-bold">{{ number_format($row->total_paid, 2) }}</td>
                                                <td class="text-right {{ $row->due > 0 ? 'text-danger font-weight-bold' : 'text-success font-weight-bold' }}">{{ number_format($row->due, 2) }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    @if($sections->count() > 1)
                                    <tfoot>
                                        <tr class="font-weight-bold" style="background:#e9ecef;">
                                            <td colspan="2">Class Total</td>
                                            @foreach($activeCategories as $cat)
                                                @php
                                                    $cf = $sections->sum(fn($s) => $s->cat_totals[$cat->id]['fees'] ?? 0);
                                                    $cp = $sections->sum(fn($s) => $s->cat_totals[$cat->id]['paid'] ?? 0);
                                                    $cd = $sections->sum(fn($s) => $s->cat_totals[$cat->id]['due'] ?? 0);
                                                @endphp
                                                <td class="text-right">{{ number_format($cf, 2) }}</td>
                                                <td class="text-right text-success">{{ number_format($cp, 2) }}</td>
                                                <td class="text-right text-danger">{{ number_format($cd, 2) }}</td>
                                            @endforeach
                                            <td class="text-right">{{ number_format($classFees, 2) }}</td>
                                            <td class="text-right text-success">{{ number_format($classPaid, 2) }}</td>
                                            <td class="text-right text-danger">{{ number_format($classDue, 2) }}</td>
                                        </tr>
                                    </tfoot>
                                    @endif
                                </table>
                            </div>
                        </div>
                    </div>
                @endforeach
            @endif

        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const toggleButton = document.getElementById('report-toggle-filters');
    const advancedFilters = document.getElementById('reportAdvancedFilters');
    const countBadge = document.querySelector('[data-filter-count]');
    const filterFields = Array.from(document.querySelectorAll('.report-filter-control'));

    function setFilterCount() {
        let count = 0;

        filterFields.forEach((field) => {
            const value = field.value;
            if (Array.isArray(value) ? value.length : value !== null && value !== '') {
                count++;
            }
        });

        if (countBadge) {
            countBadge.textContent = count;
        }

        return count;
    }

    function refreshAdvancedPanelState() {
        if (!advancedFilters || !toggleButton) return;

        const activeCount = setFilterCount();
        if (activeCount > 0) {
            advancedFilters.classList.remove('hidden');
            toggleButton.setAttribute('aria-expanded', 'true');
        } else {
            toggleButton.setAttribute('aria-expanded', advancedFilters.classList.contains('hidden') ? 'false' : 'true');
        }
    }

    if (toggleButton && advancedFilters) {
        toggleButton.addEventListener('click', function () {
            advancedFilters.classList.toggle('hidden');
            toggleButton.setAttribute('aria-expanded', advancedFilters.classList.contains('hidden') ? 'false' : 'true');
        });
    }

    filterFields.forEach((field) => {
        field.addEventListener('change', refreshAdvancedPanelState);
        field.addEventListener('keyup', refreshAdvancedPanelState);
    });

    refreshAdvancedPanelState();
});
</script>

<style>
        @media print {
            @page {
                size: A4 landscape;
                margin: 8mm;
            }

            html, body {
                width: 100% !important;
                height: auto !important;
                overflow: visible !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .fees-report-page .fees-report-card:first-of-type {
                display: none !important;
            }

            .main-sidebar, .main-header, .content-header, form, hr, .info-box, button, a.btn { display: none !important; }
            .content-wrapper { margin-left: 0 !important; padding: 0 !important; overflow: visible !important; }
            .fees-report-page { padding: 0 !important; }
            .fees-report-page .fees-report-shell { padding: 0 !important; }
            .fees-report-page .fees-report-card { box-shadow: none !important; border-color: #d1d5db !important; break-inside: avoid; page-break-inside: avoid; }
            .fees-report-page .fees-report-print-header {
                display: flex !important;
                align-items: flex-start;
                justify-content: space-between;
                gap: 1rem;
                margin: 0 0 0.9rem;
                padding: 0 0 0.6rem;
                border-bottom: 1px solid #d1d5db;
            }
            .fees-report-page .fees-report-print-title { margin: 0; font-size: 18px; font-weight: 700; color: #111827; }
            .fees-report-page .fees-report-print-subtitle { margin: 0.2rem 0 0; font-size: 10px; color: #6b7280; }
            .fees-report-page .fees-report-print-meta { text-align: right; font-size: 10px; color: #374151; white-space: nowrap; }
            .table-danger { background-color: #ffe0e0 !important; -webkit-print-color-adjust: exact; }
            .table-success { background-color: #e0ffe0 !important; -webkit-print-color-adjust: exact; }
            .card { break-inside: avoid; }
        }
</style>
@endsection
