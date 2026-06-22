@extends('layouts.master')

@section('styles')
    <style>
        .exam-actions {
            gap: 0.35rem;
        }

        .exam-actions .exam-action-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 36px;
            min-width: 36px;
            height: 36px;
            min-height: 36px;
            padding: 0;
            line-height: 1;
            vertical-align: middle;
            overflow: hidden;
        }

        .exam-actions .exam-action-btn i {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            height: 100%;
            line-height: 1;
            margin: 0;
        }
    </style>
@endsection

@section('contents')
    <div class="container-fluid">

        <div class="card card-outline  mb-3">
            <div class="card-header">
                <div class="col-12 d-flex justify-content-between align-items-center">
                    <h4 class="font-weight-bold mb-0 text-white"><i class="fas fa-file-alt text-primary mr-2"></i>Exams</h4>
                    <a href="{{ route('exams.create') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus mr-1"></i> New Exam
                    </a>
                </div>
            </div>
            <div class="card-body py-2">

                <form method="GET" class="d-flex flex-wrap align-items-center gap-2">

                    {{-- Type --}}
                    <div style="min-width: 160px;">
                        <select name="type" class="form-control form-control-sm border rounded shadow-sm">
                            <option value="">All Types</option>
                            <option value="term" {{ request('type') == 'term' ? 'selected' : '' }}>Terminal</option>
                            <option value="tutorial" {{ request('type') == 'tutorial' ? 'selected' : '' }}>Tutorial</option>
                        </select>
                    </div>

                    {{-- Session --}}
                    <div style="min-width: 200px;">
                        <select name="session_id" class="form-control form-control-sm border rounded shadow-sm">
                            <option value="">All Sessions</option>
                            @foreach ($sessions as $session)
                                <option value="{{ $session->id }}"
                                    {{ request('session_id') == $session->id ? 'selected' : '' }}>
                                    {{ $session->name_en ?? $session->name_bn }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Filter Button --}}
                    <div>
                        <button class="btn btn-sm btn-secondary shadow-sm px-3">
                            Filter
                        </button>
                    </div>

                    {{-- Reset Button --}}
                    <div>
                        <a href="{{ route('exams.index') }}" class="btn btn-sm btn-light border shadow-sm px-3">
                            Reset
                        </a>
                    </div>

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
                                <td>{{ $exam->academicSession->name_en ?? ($exam->academicSession->name_bn ?? '-') }}</td>
                                <td>{{ $exam->year }}</td>
                                <td>
                                    @if ($exam->start_date)
                                        {{ $exam->start_date->format('d M') }}
                                        @if ($exam->end_date)
                                            – {{ $exam->end_date->format('d M Y') }}
                                        @endif
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td>
                                    <span
                                        class="badge badge-{{ $exam->status === 'published' ? 'success' : 'secondary' }}">
                                        {{ ucfirst($exam->status) }}
                                    </span>
                                </td>
                                <td>
                                    <div class="exam-actions d-flex justify-content-center align-items-center flex-wrap">
                                        <a href="{{ route('exams.show', $exam) }}"
                                            class="btn btn-xs btn-info exam-action-btn" title="View" aria-label="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('exams.marks-entry', $exam) }}"
                                            class="btn btn-xs btn-success exam-action-btn" title="Enter Marks"
                                            aria-label="Enter Marks">
                                            <i class="fas fa-keyboard"></i>
                                        </a>
                                        @if ($exam->type === 'term')
                                            <a href="{{ route('exams.terminal-result', $exam) }}"
                                                class="btn btn-xs btn-warning exam-action-btn" title="Terminal Result"
                                                aria-label="Terminal Result">
                                                <i class="fas fa-trophy"></i>
                                            </a>
                                        @else
                                            <a href="{{ route('exams.preview', $exam) }}"
                                                class="btn btn-xs btn-info exam-action-btn" title="Preview"
                                                aria-label="Preview">
                                                <i class="fas fa-chart-bar"></i>
                                            </a>
                                        @endif
                                        <a href="{{ route('exams.edit', $exam) }}"
                                            class="btn btn-xs btn-secondary exam-action-btn" title="Edit"
                                            aria-label="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button class="btn btn-xs btn-danger exam-action-btn"
                                            title="Delete" aria-label="Delete"
                                            onclick="if(confirm('Delete this exam?')){this.closest('form').requestSubmit()}"
                                            form="delete-exam-{{ $exam->id }}">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                    <form id="delete-exam-{{ $exam->id }}" method="POST"
                                        action="{{ route('exams.destroy', $exam) }}" class="d-none">
                                        @csrf @method('DELETE')
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">No exams found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($exams->hasPages())
                <div class="card-footer">{{ $exams->links() }}</div>
            @endif
        </div>
    </div>
@endsection
