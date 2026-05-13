@extends('layouts.master')

@section('contents')
<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title mb-0 text-white text-lg">Student Payment Report</h3>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('fees.payment-report') }}">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="font-weight-bold">Filter Mode <span class="text-danger">*</span></label>
                            <select name="mode" class="form-control form-control-sm" required>
                                <option value="">— Select Mode —</option>
                                <option value="daily" {{ request('mode') === 'daily' ? 'selected' : '' }}>Daily</option>
                                <option value="monthly" {{ request('mode') === 'monthly' ? 'selected' : '' }}>Monthly</option>
                                <option value="yearly" {{ request('mode') === 'yearly' ? 'selected' : '' }}>Yearly</option>
                                <option value="range" {{ request('mode') === 'range' ? 'selected' : '' }}>Date Range</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-9">
                        <div class="row">
                            <div class="col-md-3 filter-mode filter-daily d-none">
                                <div class="form-group">
                                    <label class="font-weight-bold">Date</label>
                                    <input type="date" name="date" value="{{ request('date') }}" class="form-control form-control-sm">
                                </div>
                            </div>

                            <div class="col-md-3 filter-mode filter-monthly d-none">
                                <div class="form-group">
                                    <label class="font-weight-bold">Month</label>
                                    <select name="month" class="form-control form-control-sm">
                                        <option value="">— Select Month —</option>
                                        @foreach(range(1,12) as $m)
                                            <option value="{{ $m }}" {{ request('month') == $m ? 'selected' : '' }}>{{ date('F', mktime(0,0,0,$m,1)) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3 filter-mode filter-monthly d-none">
                                <div class="form-group">
                                    <label class="font-weight-bold">Year</label>
                                    <input type="number" name="year" value="{{ request('year') }}" class="form-control form-control-sm" min="2000" max="2099">
                                </div>
                            </div>

                            <div class="col-md-3 filter-mode filter-yearly d-none">
                                <div class="form-group">
                                    <label class="font-weight-bold">Academic Session</label>
                                    <select name="session_id" class="form-control form-control-sm">
                                        <option value="">— Select Session —</option>
                                        @foreach($sessions as $session)
                                            <option value="{{ $session->id }}" {{ request('session_id') == $session->id ? 'selected' : '' }}>{{ $session->name_en }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-3 filter-mode filter-range d-none">
                                <div class="form-group">
                                    <label class="font-weight-bold">From Date</label>
                                    <input type="date" name="from_date" value="{{ request('from_date') }}" class="form-control form-control-sm">
                                </div>
                            </div>
                            <div class="col-md-3 filter-mode filter-range d-none">
                                <div class="form-group">
                                    <label class="font-weight-bold">To Date</label>
                                    <input type="date" name="to_date" value="{{ request('to_date') }}" class="form-control form-control-sm">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mt-2">
                    <div class="col-md-12 d-flex align-items-center">
                        <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-search"></i> Generate</button>
                        <a href="{{ route('fees.payment-report') }}" class="btn btn-secondary btn-sm ml-1"><i class="fas fa-times"></i> Reset</a>
                        @if($rows->isNotEmpty())
                            <button type="button" class="btn btn-success btn-sm ml-1" onclick="window.print()"><i class="fas fa-print"></i> Print</button>
                            <a href="{{ route('fees.payment-report.pdf', request()->query()) }}" class="btn btn-danger btn-sm ml-1"><i class="fas fa-file-pdf"></i> Export PDF</a>
                        @endif
                    </div>
                </div>
            </form>

            <hr>

            @if(!$mode)
                <div class="text-center py-5 text-muted">
                    <i class="fas fa-filter fa-2x mb-2"></i>
                    <p class="mb-0">Select a filter mode to generate the student payment report.</p>
                </div>
            @elseif($rows->isEmpty())
                <div class="text-center py-5 text-muted">
                    <i class="fas fa-inbox fa-2x mb-2"></i>
                    <p class="mb-0">No payment records found for the selected period.</p>
                </div>
            @else
                <div class="row mb-3">
                    <div class="col-md-4">
                        <div class="info-box bg-light">
                            <span class="info-box-icon bg-info"><i class="fas fa-user-graduate"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Students</span>
                                <span class="info-box-number">{{ $rows->sum(fn($g) => $g->students->count()) }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="info-box bg-light">
                            <span class="info-box-icon bg-success"><i class="fas fa-check-circle"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Grand Total Paid</span>
                                <span class="info-box-number">{{ number_format($rows->sum(fn($g) => $g->students->sum('grand_total')), 2) }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="info-box bg-light">
                            <span class="info-box-icon bg-secondary"><i class="fas fa-calendar-alt"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Filter</span>
                                <span class="info-box-number">{{ $dateLabel }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                @foreach($rows as $group)
                    <div class="mb-4">
                        <h5 class="mb-2 bg-light p-2 rounded">
                            <strong>Class:</strong> {{ $group->class_name }} | <strong>Section:</strong> {{ $group->section_name }}
                        </h5>
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover table-sm">
                                <thead class="thead-dark">
                                    <tr>
                                        <th>#</th>
                                        <th>Student ID</th>
                                        <th>Name</th>
                                        @foreach($feeCategories as $category)
                                            <th class="text-right">{{ $category->name }}</th>
                                        @endforeach
                                        @foreach($invCategories as $category)
                                            <th class="text-right">{{ $category->name }}</th>
                                        @endforeach
                                        <th class="text-right">Grand Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($group->students as $index => $row)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $row->student_cid }}</td>
                                            <td>{{ $row->student_name }}</td>
                                            @foreach($feeCategories as $category)
                                                <td class="text-right">{{ number_format($row->{'fee_' . $category->id}, 2) }}</td>
                                            @endforeach
                                            @foreach($invCategories as $category)
                                                <td class="text-right">{{ number_format($row->{'inv_' . $category->id}, 2) }}</td>
                                            @endforeach
                                            <td class="text-right font-weight-bold">{{ number_format($row->grand_total, 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr class="font-weight-bold bg-light">
                                        <td colspan="3">Subtotal</td>
                                        @foreach($feeCategories as $category)
                                            <td class="text-right">{{ number_format($group->students->sum(fn($r) => $r->{'fee_' . $category->id}), 2) }}</td>
                                        @endforeach
                                        @foreach($invCategories as $category)
                                            <td class="text-right">{{ number_format($group->students->sum(fn($r) => $r->{'inv_' . $category->id}), 2) }}</td>
                                        @endforeach
                                        <td class="text-right">{{ number_format($group->students->sum('grand_total'), 2) }}</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                @endforeach
            @endif
        </div>
    </div>
</div>

<style>
@media print {
    .main-sidebar, .main-header, .content-header, form, hr, .info-box, button, a.btn { display: none !important; }
    .content-wrapper { margin-left: 0 !important; }
}
</style>

<script>
    function togglePaymentReportFilters() {
        const mode = document.querySelector('[name="mode"]').value;
        document.querySelectorAll('.filter-mode').forEach(el => el.classList.add('d-none'));
        if (mode) {
            document.querySelectorAll('.filter-' + mode).forEach(el => el.classList.remove('d-none'));
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        togglePaymentReportFilters();
        document.querySelector('[name="mode"]').addEventListener('change', togglePaymentReportFilters);
    });
</script>
@endsection
