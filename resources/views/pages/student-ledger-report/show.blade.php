@extends('layouts.master')

@section('contents')
@php
    $ai = $student->academicInformations->first();
@endphp
<div class="container-fluid">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="card-title mb-0 text-white text-lg">Student Payment Ledger</h3>
            <div>
                <a href="{{ route('fees.student-due-report', request()->only('session_id')) }}"
                   class="btn btn-secondary btn-sm"><i class="fas fa-arrow-left"></i> Back</a>
                <a href="{{ route('fees.student-ledger.pdf', ['student' => $student->id, 'session_id' => $session->id]) }}"
                   class="btn btn-danger btn-sm ml-1"><i class="fas fa-file-pdf"></i> Export PDF</a>
                <button onclick="window.print()" class="btn btn-success btn-sm ml-1"><i class="fas fa-print"></i> Print</button>
            </div>
        </div>
        <div class="card-body">

            {{-- Student Info --}}
            <div class="row mb-3">
                <div class="col-md-6">
                    <table class="table table-sm table-borderless mb-0">
                        <tr><th width="140">Student Name</th><td>{{ $student->full_name_en }}</td></tr>
                        <tr><th>Student CID</th><td>{{ $student->student_cid }}</td></tr>
                        <tr><th>Roll</th><td>{{ $ai?->roll ?? '—' }}</td></tr>
                        <tr><th>Class</th><td>{{ $ai?->schoolClass?->name_en ?? '—' }}</td></tr>
                        <tr><th>Section</th><td>{{ $ai?->section?->name_en ?? '—' }}</td></tr>
                        <tr><th>Group</th><td>{{ $ai?->group?->name_en ?? '—' }}</td></tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <table class="table table-sm table-borderless mb-0">
                        <tr><th width="140">Parent Name</th><td>{{ $student->guardian_name ?? $student->father_name ?? '—' }}</td></tr>
                        <tr><th>Mobile</th><td>{{ $student->guardian_phone ?? $student->father_phone ?? '—' }}</td></tr>
                        <tr><th>Session</th><td>{{ $session->name_en }}</td></tr>
                        <tr><th>Total Dues</th><td class="text-danger font-weight-bold">{{ number_format($total_dues, 2) }}</td></tr>
                        <tr><th>Total Received</th><td class="text-success font-weight-bold">{{ number_format($total_received, 2) }}</td></tr>
                        <tr>
                            <th>Balance</th>
                            <td class="{{ $closing_balance > 0 ? 'text-danger' : 'text-success' }} font-weight-bold">
                                {{ $closing_balance > 0 ? 'Due: ' : 'Advance: ' }}{{ number_format(abs($closing_balance), 2) }}
                            </td>
                        </tr>
                    </table>
                </div>
            </div>

            @if($months->isEmpty())
                <div class="text-center py-5 text-muted">
                    <i class="fas fa-inbox fa-2x mb-2"></i>
                    <p class="mb-0">No transactions found for this session.</p>
                </div>
            @else
                @php $runningBalance = 0.0; @endphp
                @foreach($months as $month)
                    <h6 class="bg-light p-2 rounded mb-1 mt-3 font-weight-bold">{{ $month->label }}</h6>
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm mb-1">
                            <thead class="thead-dark">
                                <tr>
                                    <th>Voucher</th>
                                    <th>Date</th>
                                    <th>Code</th>
                                    <th>Description</th>
                                    <th class="text-right">Dues</th>
                                    <th class="text-right">Received</th>
                                    <th class="text-right">Balance</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($month->rows as $row)
                                    @php $runningBalance = $runningBalance + $row['dues'] - $row['received']; @endphp
                                    <tr>
                                        <td>{{ $row['voucher'] }}</td>
                                        <td>{{ $row['date'] }}</td>
                                        <td><span class="badge badge-secondary">{{ $row['code'] }}</span></td>
                                        <td>{{ $row['description'] }}</td>
                                        <td class="text-right">{{ $row['dues'] > 0 ? number_format($row['dues'], 2) : '—' }}</td>
                                        <td class="text-right text-success">{{ $row['received'] > 0 ? number_format($row['received'], 2) : '—' }}</td>
                                        <td class="text-right {{ $runningBalance > 0 ? 'text-danger' : 'text-success' }}">
                                            {{ number_format(abs($runningBalance), 2) }}
                                            {{ $runningBalance > 0 ? 'Dr' : 'Cr' }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr class="font-weight-bold bg-light">
                                    <td colspan="4">Month Total</td>
                                    <td class="text-right">{{ number_format($month->month_dues, 2) }}</td>
                                    <td class="text-right text-success">{{ number_format($month->month_recv, 2) }}</td>
                                    <td class="text-right {{ $runningBalance > 0 ? 'text-danger' : 'text-success' }}">
                                        {{ number_format(abs($runningBalance), 2) }}
                                        {{ $runningBalance > 0 ? 'Dr' : 'Cr' }}
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                @endforeach

                <div class="row mt-3">
                    <div class="col-md-4 offset-md-8">
                        <table class="table table-sm table-bordered">
                            <tr><th>Total Dues</th><td class="text-right text-danger">{{ number_format($total_dues, 2) }}</td></tr>
                            <tr><th>Total Received</th><td class="text-right text-success">{{ number_format($total_received, 2) }}</td></tr>
                            <tr class="font-weight-bold">
                                <th>{{ $closing_balance > 0 ? 'Net Due' : 'Advance' }}</th>
                                <td class="text-right {{ $closing_balance > 0 ? 'text-danger' : 'text-success' }}">
                                    {{ number_format(abs($closing_balance), 2) }}
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            @endif

        </div>
    </div>
</div>

<style>
@media print {
    .main-sidebar, .main-header, .content-header, .card-header .btn { display: none !important; }
    .content-wrapper { margin-left: 0 !important; }
}
</style>
@endsection
