@extends('layouts.master')
@section('contents')
<div class="container-fluid">
    @include('hr._alerts')

    {{-- Generate Form --}}
    <div class="card mb-3">
        <div class="card-header"><h3 class="card-title mb-0 text-white text-lg">Generate Payroll</h3></div>
        <div class="card-body">
            <form action="{{ route('hr.payroll.preview') }}" method="POST" class="row align-items-end">
                @csrf
                <div class="col-md-2 form-group mb-0">
                    <label class="font-weight-bold">Month</label>
                    <select name="month" class="form-control form-control-sm" required>
                        @foreach(range(1,12) as $m)
                            <option value="{{ $m }}" {{ now()->month == $m ? 'selected' : '' }}>{{ date('F', mktime(0,0,0,$m,1)) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 form-group mb-0">
                    <label class="font-weight-bold">Year</label>
                    <input type="number" name="year" class="form-control form-control-sm" value="{{ now()->year }}" min="2000" required>
                </div>
                <div class="col-md-2 form-group mb-0">
                    <button type="submit" class="btn btn-primary btn-sm ml-auto"><i class="fas fa-eye"></i> Preview</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Processed Months --}}
    <div class="card">
        <div class="card-header"><h3 class="card-title mb-0 text-white text-lg">Payroll History</h3></div>
        <div class="card-body p-0">
            <table class="table table-bordered table-hover table-sm mb-0">
                <thead class="thead-dark">
                    <tr><th>Period</th><th>Employees</th><th>Total Gross</th><th>Total Net</th><th>Paid</th><th>Actions</th></tr>
                </thead>
                <tbody>
                    @forelse($months as $m)
                    <tr>
                        <td class="font-weight-bold">{{ date('F', mktime(0,0,0,$m->payroll_month,1)) }} {{ $m->payroll_year }}</td>
                        <td>{{ $m->count }}</td>
                        <td>৳{{ number_format($m->total_gross, 2) }}</td>
                        <td class="text-success font-weight-bold">৳{{ number_format($m->total_net, 2) }}</td>
                        <td><span class="badge badge-success">{{ $m->paid_count }}</span> / {{ $m->count }}</td>
                        <td><a href="{{ route('hr.payroll.show', [$m->payroll_month, $m->payroll_year]) }}" class="btn btn-xs btn-info"><i class="fas fa-eye"></i> View</a></td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="text-center text-muted py-4">No payroll generated yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
