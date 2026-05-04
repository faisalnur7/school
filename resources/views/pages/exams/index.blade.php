@extends('layouts.master')

@section('contents')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <h4 class="font-weight-bold mb-0"><i class="fas fa-file-alt text-primary mr-2"></i>Exams</h4>
            <a href="{{ route('exams.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus mr-1"></i> New Exam
            </a>
        </div>
    </div>

    <div class="card card-outline card-primary mb-3">
        <div class="card-body py-2">
            <form method="GET" class="form-inline flex-wrap gap-2">
                <select name="type" class="form-control form-control-sm mr-2 mb-1">
                    <option value="">All Types</option>
                    <option value="term"     {{ request('type') == 'term'     ? 'selected' : '' }}>Terminal</option>
                    <option value="tutorial" {{ request('type') == 'tutorial' ? 'selected' : '' }}>Tutorial</option>
                </select>
                <select name="session_id" class="form-control form-control-sm mr-2 mb-1">
                    <option value="">All Sessions</option>
                    @foreach($sessions as $session)
                    <option value="{{ $session->id }}" {{ request('session_id') == $session->id ? 'selected' : '' }}>
                        {{ $session->name_en ?? $session->name_bn }}
                    </option>
                    @endforeach
                </select>
                <button class="btn btn-sm btn-secondary mb-1"><i class="fas fa-search mr-1"></i>Filter</button>
                <a href="{{ route('exams.index') }}" class="btn btn-sm btn-light mb-1">Reset</a>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-body p-0">
            <table class="table table-hover table-sm mb-0">
                <thead class="thead-light">
                    <tr>
                        <th>#</th>
                        <th>Exam Name</th>
                        <th>Type</th>
                        <th>Session</th>
                        <th>Year</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($exams as $exam)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td><strong>{{ $exam->name }}</strong></td>
                        <td>
                            <span class="badge badge-{{ $exam->type === 'term' ? 'danger' : 'info' }}">
                                {{ $exam->type_label }}
                            </span>
                        </td>
                        <td>{{ $exam->academicSession->name_en ?? $exam->academicSession->name_bn ?? '-' }}</td>
                        <td>{{ $exam->year }}</td>
                        <td>
                            @if($exam->start_date)
                                {{ $exam->start_date->format('d M') }}
                                @if($exam->end_date) – {{ $exam->end_date->format('d M Y') }} @endif
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge badge-{{ $exam->status === 'published' ? 'success' : 'secondary' }}">
                                {{ ucfirst($exam->status) }}
                            </span>
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('exams.show', $exam) }}"            class="btn btn-outline-primary"   title="View"><i class="fas fa-eye"></i></a>
                                <a href="{{ route('exams.marks-entry', $exam) }}"     class="btn btn-outline-success"   title="Enter Marks"><i class="fas fa-keyboard"></i></a>
                                @if($exam->type === 'term')
                                <a href="{{ route('exams.terminal-result', $exam) }}" class="btn btn-outline-warning"   title="Terminal Result"><i class="fas fa-trophy"></i></a>
                                @else
                                <a href="{{ route('exams.preview', $exam) }}"         class="btn btn-outline-info"      title="Preview"><i class="fas fa-chart-bar"></i></a>
                                @endif
                                <a href="{{ route('exams.edit', $exam) }}"            class="btn btn-outline-secondary" title="Edit"><i class="fas fa-edit"></i></a>
                                <form method="POST" action="{{ route('exams.destroy', $exam) }}" class="d-inline"
                                    onsubmit="return confirm('Delete this exam?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-outline-danger" title="Delete"><i class="fas fa-trash"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="8" class="text-center text-muted py-4">No exams found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($exams->hasPages())
        <div class="card-footer">{{ $exams->links() }}</div>
        @endif
    </div>
</div>
@endsection
