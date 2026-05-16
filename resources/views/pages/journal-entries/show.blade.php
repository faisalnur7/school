@extends('layouts.master')

@section('contents')
<div class="container-fluid">
    <div class="card">
        <div class="card-header bg-gradient-primary text-white py-3">
            <div class="d-flex justify-content-between align-items-center">
                <h4 class="card-title mb-0 font-weight-bold text-white">
                    <i class="fas fa-book mr-2"></i>Journal Entry — {{ $journalEntry->reference_no }}
                </h4>
                <a href="{{ route('journal-entries.index') }}" class="btn btn-light btn-sm">
                    <i class="fas fa-arrow-left mr-1"></i> Back
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-4"><strong>Reference:</strong> {{ $journalEntry->reference_no }}</div>
                <div class="col-md-4"><strong>Date:</strong> {{ $journalEntry->date->format('d M Y') }}</div>
                <div class="col-md-4"><strong>Posted By:</strong> {{ $journalEntry->createdBy?->name ?? '—' }}</div>
            </div>
            <div class="mb-3"><strong>Description:</strong> {{ $journalEntry->description ?? '—' }}</div>

            <table class="table table-bordered">
                <thead class="thead-light">
                    <tr>
                        <th>Account</th>
                        <th>Group</th>
                        <th class="text-right">Debit</th>
                        <th class="text-right">Credit</th>
                        <th>Description</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($journalEntry->lines as $line)
                        <tr>
                            <td>{{ $line->account->name }}</td>
                            <td>{{ $line->account->group?->name ?? '—' }}</td>
                            <td class="text-right">{{ $line->debit > 0 ? number_format($line->debit, 2) : '—' }}</td>
                            <td class="text-right">{{ $line->credit > 0 ? number_format($line->credit, 2) : '—' }}</td>
                            <td>{{ $line->description ?? '—' }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="font-weight-bold">
                        <td colspan="2">Total</td>
                        <td class="text-right text-success">{{ number_format($journalEntry->total_debit, 2) }}</td>
                        <td class="text-right text-danger">{{ number_format($journalEntry->total_credit, 2) }}</td>
                        <td>
                            @if($journalEntry->is_balanced)
                                <span class="badge badge-success">Balanced</span>
                            @else
                                <span class="badge badge-danger">Not Balanced</span>
                            @endif
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
@endsection
