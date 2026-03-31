@extends('layouts.master')

@section('contents')
<div class="container-fluid">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4 class="text-white font-bold text-lg">Scholarships</h4>
            <a href="{{ route('scholarships.create') }}" class="btn btn-primary ml-auto">Assign Scholarships</a>
        </div>
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Student ID</th>
                            <th>Student Name</th>
                            <th>Fee Category</th>
                            <th>Type</th>
                            <th>Value</th>
                            <th>Session</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($scholarships as $scholarship)
                            <tr>
                                <td>{{ $scholarship->student->student_cid }}</td>
                                <td>{{ $scholarship->student->full_name_en }}</td>
                                <td>{{ $scholarship->feeCategory->name ?? 'N/A' }}</td>
                                <td>{{ ucfirst($scholarship->type) }}</td>
                                <td>
                                    @if($scholarship->type === 'fixed')
                                        ৳{{ number_format($scholarship->amount, 2) }}
                                    @else
                                        {{ $scholarship->percentage }}%
                                    @endif
                                </td>
                                <td>{{ $scholarship->academicSession->name ?? 'N/A' }}</td>
                                <td>
                                    <span class="badge badge-{{ $scholarship->status === 'active' ? 'success' : 'secondary' }}">
                                        {{ ucfirst($scholarship->status) }}
                                    </span>
                                </td>
                                <td>
                                    <form action="{{ route('scholarships.destroy', $scholarship) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')"><i class="fas fa-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center">No scholarships found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $scholarships->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
