@extends('layouts.master')

@section('contents')
    <div class="container-fluid py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="font-bold text-lg">🚌 Transport Fee Management</h4>
            <a href="{{ route('transports.create') }}" class="btn btn-primary">
                + Assign Transport Fee
            </a>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-gradient-primary text-white py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0 font-weight-bold text-white">
                        <i class="fas fa-filter mr-2"></i>Filter Students
                    </h4>
                </div>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('transports.index') }}">
                    @include('transports.filter', ['isIndex' => true])
                </form>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead style="background:#f8fafc">
                            <tr>
                                <th class="px-4 py-3">Student ID</th>
                                <th class="px-4 py-3">Student Info</th>
                                <th class="px-4 py-3">Academic Info</th>
                                <th class="px-4 py-3">Fee Category</th>
                                <th class="px-4 py-3">Amount</th>
                                <th class="px-4 py-3">Session</th>
                                <th class="px-4 py-3 text-center">Active/Inactive</th>
                                <th class="px-4 py-3 text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($transports as $transport)
                                <tr>
                                    <td class="px-4 py-3">
                                        <code style="background:#f1f5f9;padding:3px 8px;border-radius:6px">
                                            {{ $transport->student->student_cid ?? 'N/A'}}
                                        </code>
                                    </td>
                                    <td class="px-4 py-3">
                                        <strong>{{ $transport->student->full_name_en }}</strong><br>
                                        <small class="text-muted">{{ $transport->student->full_name_bn ?? 'N/A' }}</small><br>
                                        <small class="text-muted">DOB: {{ optional($transport->student->date_of_birth)->format('d M, Y') ?? 'N/A' }}</small>
                                    </td>
                                    <td class="px-4 py-3">
                                        @if($transport->studentAcademicInformation)
                                            <small><strong>Class:</strong> {{ $transport->studentAcademicInformation->schoolClass->name_en ?? '—' }}</small><br>
                                            <small><strong>Section:</strong> {{ $transport->studentAcademicInformation->section->name_en ?? '—' }}</small><br>
                                            <small><strong>Group:</strong> {{ $transport->studentAcademicInformation->group->name_en ?? '—' }}</small><br>
                                            <small><strong>Roll:</strong> {{ $transport->studentAcademicInformation->roll ?? '—' }}</small>
                                        @else
                                            <small class="text-muted">No academic info</small>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3">{{ $transport->feeCategory->name }}</td>
                                    <td class="px-4 py-3">
                                        <span class="fw-bold" style="color:#4338ca">
                                            ৳{{ number_format($transport->amount, 2) }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3">{{ $transport->academicSession->name_en }}</td>
                                    <td class="px-4 py-3 text-center">
                                        <form action="{{ route('transports.toggle-status', $transport->id) }}" method="POST">
                                            @csrf
                                            <div class="custom-control custom-switch">
                                                <input type="checkbox" class="custom-control-input" id="transportSwitch{{ $transport->id }}" onchange="this.form.submit()" {{ $transport->status === 'active' ? 'checked' : '' }}>
                                                <label class="custom-control-label" for="transportSwitch{{ $transport->id }}"></label>
                                            </div>
                                        </form>
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <a href="{{ route('transports.edit', $transport->id) }}" class="btn btn-sm btn-warning me-1"><i class="fas fa-edit"></i></a>
                                        <form action="{{ route('transports.destroy', $transport->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" 
                                                onclick="return confirm('Remove this transport fee?')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-5 text-muted">
                                        <div style="font-size:48px;opacity:.2">🚌</div>
                                        <p class="mt-3">No transport fees assigned yet</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if($transports->hasPages())
                <div class="card-footer bg-white">
                    {{ $transports->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection

@section('scripts')
    @include('scripts.common.load_academic_information')
@endsection
