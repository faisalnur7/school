@extends('layouts.master')

@section('contents')
<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title mb-0 text-white text-lg">Fee Due Report — Class &amp; Section Wise</h3>
        </div>
        <div class="card-body">

            <form method="GET" action="{{ route('fees.due-report') }}">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="font-weight-bold">Academic Year <span class="text-danger">*</span></label>
                            <select name="session_id" class="form-control form-control-sm" required onchange="this.form.submit()">
                                <option value="">— Select Year —</option>
                                @foreach($sessions as $s)
                                    <option value="{{ $s->id }}" {{ request('session_id') == $s->id ? 'selected' : '' }}>{{ $s->name_en }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Month <small class="text-muted">(leave blank for full year)</small></label>
                            <select name="month" class="form-control form-control-sm">
                                <option value="">All Year</option>
                                @foreach(range(1,12) as $m)
                                    <option value="{{ $m }}" {{ request('month') == $m ? 'selected' : '' }}>{{ date('F', mktime(0,0,0,$m,1)) }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6 d-flex align-items-center">
                        <div class="form-group mb-0">
                            <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-search"></i> Generate</button>
                            <a href="{{ route('fees.due-report') }}" class="btn btn-secondary btn-sm ml-1"><i class="fas fa-times"></i> Reset</a>
                            @if(request('session_id') && $classSections->isNotEmpty())
                                <button type="button" class="btn btn-success btn-sm ml-1" onclick="window.print()"><i class="fas fa-print"></i> Print</button>
                                <a href="{{ route('fees.due-report.pdf', request()->query()) }}" class="btn btn-danger btn-sm ml-1"><i class="fas fa-file-pdf"></i> Export PDF</a>
                            @endif
                        </div>
                    </div>
                </div>
            </form>

            <hr>

            @if(!request('session_id'))
                <div class="text-center py-5 text-muted">
                    <i class="fas fa-filter fa-2x mb-2"></i>
                    <p class="mb-0">Select an Academic Year to generate the report.</p>
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

<style>
@media print {
    .main-sidebar, .main-header, .content-header, form, hr, .info-box, button, a.btn { display: none !important; }
    .content-wrapper { margin-left: 0 !important; }
    .table-danger { background-color: #ffe0e0 !important; -webkit-print-color-adjust: exact; }
    .table-success { background-color: #e0ffe0 !important; -webkit-print-color-adjust: exact; }
    .card { break-inside: avoid; }
}
</style>
@endsection
