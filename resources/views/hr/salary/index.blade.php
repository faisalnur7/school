@extends('layouts.master')
@section('contents')
<div class="container-fluid">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="card-title mb-0 text-white text-lg">Salary Structures</h3>
            <a href="{{ route('hr.employees.index') }}" class="btn btn-primary btn-sm ml-auto"><i class="fas fa-user"></i> Select Employee</a>
        </div>
        <div class="card-body">
            @include('hr._alerts')
            <div class="table-responsive">
                <table class="table table-bordered table-hover table-sm">
                    <thead class="thead-dark">
                        <tr><th>#</th><th>Employee</th><th>Designation</th><th>Basic</th><th>Gross</th><th>Deductions</th><th>Net</th><th>Effective From</th><th class="text-center">Actions</th></tr>
                    </thead>
                    <tbody>
                        @forelse($structures as $s)
                        <tr>
                            <td>{{ $structures->firstItem() + $loop->index }}</td>
                            <td><a href="{{ route('hr.employees.show', $s->employee) }}">{{ $s->employee->name }}</a><br><small class="text-muted">{{ $s->employee->employee_id }}</small></td>
                            <td>{{ $s->employee->designation->name ?? '—' }}</td>
                            <td>৳{{ number_format($s->basic_salary, 2) }}</td>
                            <td class="font-weight-bold">৳{{ number_format($s->gross_salary, 2) }}</td>
                            <td class="text-danger">৳{{ number_format($s->other_deductions, 2) }}</td>
                            <td class="font-weight-bold text-success">৳{{ number_format($s->net_salary, 2) }}</td>
                            <td>{{ $s->effective_from->format('d M Y') }}</td>
                            <td class="text-center">
                                <a href="{{ route('hr.salary-structures.edit', $s) }}" class="btn btn-xs btn-warning"><i class="fas fa-edit"></i></a>
                                <form action="{{ route('hr.salary-structures.destroy', $s) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-xs btn-danger"><i class="fas fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="9" class="text-center text-muted py-4">No salary structures found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{ $structures->links() }}
        </div>
    </div>
</div>
@endsection
