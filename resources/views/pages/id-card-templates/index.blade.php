@extends('layouts.master')

@section('contents')
<div class="container-fluid">
    <div class="card">
        <div class="card-header bg-gradient-primary text-white py-3">
            <div class="d-flex justify-content-between align-items-center">
                <h4 class="card-title mb-0 font-weight-bold text-white">
                    <i class="fas fa-id-card mr-2"></i>ID Card Templates
                </h4>
                <a href="{{ route('id-card-templates.create') }}" class="btn btn-light btn-sm">
                    <i class="fas fa-plus mr-1"></i> Add Template
                </a>
            </div>
        </div>
        <div class="card-body p-0">

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show m-3">
                    {{ session('success') }}
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                </div>
            @endif

            <div class="table-responsive">
                <table class="table table-bordered table-hover table-sm mb-0">
                    <thead class="thead-dark">
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Orientation</th>
                            <th>Background</th>
                            <th>Created</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($templates as $i => $t)
                            <tr>
                                <td>{{ $i + 1 }}</td>
                                <td class="font-weight-bold">{{ $t->name }}</td>
                                <td>
                                    <span class="badge badge-{{ $t->orientation === 'portrait' ? 'info' : 'warning' }}">
                                        {{ ucfirst($t->orientation) }}
                                    </span>
                                </td>
                                <td>
                                    @if($t->background_image)
                                        <img src="{{ asset($t->background_image) }}" alt="bg"
                                            class="img-thumbnail" style="max-height:50px;max-width:80px">
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td class="text-muted">{{ $t->created_at->format('d M Y') }}</td>
                                <td class="text-center">
                                    <a href="{{ route('students.id-cards', ['template_id' => $t->id]) }}" class="btn btn-xs btn-success" title="Generate ID Cards">
                                        <i class="fas fa-id-card"></i>
                                    </a>
                                    <a href="{{ route('id-card-templates.edit', $t) }}" class="btn btn-xs btn-warning" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('id-card-templates.destroy', $t) }}" method="POST" class="d-inline"
                                        onsubmit="return confirm('Delete this template?')">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-xs btn-danger" title="Delete"><i class="fas fa-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">No templates found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
