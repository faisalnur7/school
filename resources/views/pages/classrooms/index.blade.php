@extends('layouts.master')

@section('contents')
<div class="container-fluid">
    <div class="card">
        <div class="card-header bg-gradient-primary text-white py-3">
            <div class="d-flex justify-content-between align-items-center">
                <h4 class="card-title mb-0 font-weight-bold text-white">
                    <i class="fas fa-door-open mr-2"></i>Classrooms
                </h4>
                <a href="{{ route('classrooms.create') }}" class="btn btn-light btn-sm">
                    <i class="fas fa-plus mr-1"></i> Add Classroom
                </a>
            </div>
        </div>

        <div class="card-body">
            @include('hr._alerts')

            <form method="GET" class="classrooms-filter-panel mb-3" role="search">
                <div class="classrooms-filter-row">
                    <div class="classrooms-filter-search">
                        <label class="sr-only" for="classroom-search">Search name or location</label>
                        <div class="input-group classrooms-filter-input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text classrooms-filter-icon" aria-hidden="true">
                                    <i class="fas fa-search"></i>
                                </span>
                            </div>
                            <input
                                type="text"
                                id="classroom-search"
                                name="search"
                                class="form-control classrooms-filter-input"
                                value="{{ request('search') }}"
                                placeholder="Search name or location"
                            >
                        </div>
                    </div>
                    <div class="classrooms-filter-actions">
                        <button type="submit" class="btn btn-primary classrooms-filter-btn" title="Search" aria-label="Search">
                            <i class="fas fa-search"></i>
                        </button>
                        <a href="{{ route('classrooms.index') }}" class="btn btn-outline-secondary classrooms-filter-btn" title="Reset" aria-label="Reset">
                            <i class="fas fa-times"></i>
                        </a>
                    </div>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-bordered table-hover table-sm">
                    <thead class="thead-dark">
                        <tr>
                            <th>#</th>
                            <th>Name (English)</th>
                            <th>Name (Bangla)</th>
                            <th>Capacity</th>
                            <th>Location</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($classrooms as $classroom)
                            <tr>
                                <td>{{ $classrooms->firstItem() + $loop->index }}</td>
                                <td class="font-weight-bold">{{ $classroom->name_en }}</td>
                                <td>{{ $classroom->name_bn }}</td>
                                <td>{{ $classroom->capacity ?? '—' }}</td>
                                <td>{{ $classroom->location ?: '—' }}</td>
                                <td class="text-center">
                                    <a href="{{ route('classrooms.edit', $classroom->id) }}" class="btn btn-xs btn-warning">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('classrooms.delete', $classroom->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this classroom?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-xs btn-danger">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">No classrooms found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{ $classrooms->links() }}
        </div>
    </div>
</div>
@endsection
