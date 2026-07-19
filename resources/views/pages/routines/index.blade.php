@extends('layouts.master')

@section('contents')
<div class="container-fluid">
    <div class="card shadow-sm border-0">
        <div class="card-header bg-gradient-primary text-white py-3">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div>
                    <h4 class="card-title mb-0 font-weight-bold text-white">
                        <i class="fas fa-clock mr-2"></i>Class Routines
                    </h4>
                    <small class="text-white-50">Manage weekly class schedules.</small>
                </div>
                <a href="{{ route('routines.create') }}" class="btn btn-light btn-sm">
                    <i class="fas fa-plus mr-1"></i>Add Routine
                </a>
            </div>
        </div>

        <div class="card-body">
            @include('hr._alerts')

            <form method="GET" class="mb-3">
                <div class="row">
                    <div class="col-md-4 mb-2">
                        <input type="text" name="search" class="form-control" value="{{ request('search') }}" placeholder="Search class, section, subject, teacher">
                    </div>
                    <div class="col-md-3 mb-2">
                        <select name="school_class_id" class="form-control">
                            <option value="">All classes</option>
                            @foreach ($classes as $class)
                                <option value="{{ $class->id }}" @selected((string) request('school_class_id') === (string) $class->id)>
                                    {{ $class->name_en }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 mb-2">
                        <select name="section_id" class="form-control">
                            <option value="">All sections</option>
                            @foreach ($sections as $section)
                                <option value="{{ $section->id }}" @selected((string) request('section_id') === (string) $section->id)>
                                    {{ $section->name_en }} @if($section->schoolClass) ({{ $section->schoolClass->name_en }}) @endif
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2 mb-2">
                        <select name="day" class="form-control">
                            <option value="">All days</option>
                            @foreach ($days as $day)
                                <option value="{{ $day }}" @selected(request('day') === $day)>{{ $day }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12 d-flex justify-content-end gap-2">
                        <button class="btn btn-primary btn-sm" type="submit">
                            <i class="fas fa-search mr-1"></i>Filter
                        </button>
                        <a href="{{ route('routines.index') }}" class="btn btn-outline-secondary btn-sm">
                            <i class="fas fa-undo mr-1"></i>Reset
                        </a>
                    </div>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-bordered table-hover table-sm">
                    <thead class="thead-dark">
                        <tr>
                            <th>#</th>
                            <th>Class</th>
                            <th>Section</th>
                            <th>Subject</th>
                            <th>Teacher</th>
                            <th>Room</th>
                            <th>Day</th>
                            <th>Time</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($routines as $routine)
                            <tr>
                                <td>{{ $routines->firstItem() + $loop->index }}</td>
                                <td>{{ $routine->schoolClass?->name_en ?? '—' }}</td>
                                <td>{{ $routine->section?->name_en ?? '—' }}</td>
                                <td>
                                    <div class="font-weight-bold">{{ $routine->subject?->name ?? '—' }}</div>
                                    @if($routine->subject?->code)
                                        <small class="text-muted">{{ $routine->subject->code }}</small>
                                    @endif
                                </td>
                                <td>{{ $routine->teacher?->name ?? '—' }}</td>
                                <td>{{ $routine->classroom?->name_en ?? '—' }}</td>
                                <td>{{ $routine->day }}</td>
                                <td>{{ substr($routine->start_time, 0, 5) }} - {{ substr($routine->end_time, 0, 5) }}</td>
                                <td class="text-center">
                                    <a href="{{ route('routines.show', $routine->id) }}" class="btn btn-xs btn-info">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('routines.edit', $routine->id) }}" class="btn btn-xs btn-warning">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('routines.delete', $routine->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this routine?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-xs btn-danger">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center text-muted py-4">No routines found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{ $routines->links() }}
        </div>
    </div>
</div>
@endsection
