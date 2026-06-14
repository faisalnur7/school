@extends('layouts.master')

@section('contents')
<div class="container-fluid">
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-violet-700 via-purple-600 to-indigo-500 p-8 mb-6">
        <div class="absolute -right-20 -top-20 w-64 h-64 rounded-full bg-white/10 blur-3xl"></div>
        <div class="absolute -left-20 -bottom-20 w-72 h-72 rounded-full bg-fuchsia-500/20 blur-3xl"></div>
        <div class="relative z-10 flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
            <div class="flex items-center gap-5">
                <div class="flex h-18 w-18 items-center justify-center rounded-2xl bg-white/10 backdrop-blur-sm">
                    <i class="fas fa-certificate text-white text-4xl"></i>
                </div>
                <div>
                    <h3 class="text-white text-3xl font-bold m-0">Certificate Types</h3>
                    <p class="text-violet-100 text-base mt-1 mb-0">Create certificate types, assign templates, and pick the active template.</p>
                </div>
            </div>
            <div>
                <a href="{{ route('certificates.create') }}"
                   class="inline-flex items-center rounded-lg bg-white/10 px-3 py-2 text-white text-xs font-semibold no-underline hover:bg-white/20">
                    <i class="fas fa-plus mr-2"></i> Add Certificate Type
                </a>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card shadow overflow-hidden">
        <div class="card-header text-white rounded-top d-flex justify-content-between align-items-center shadow p-3">
            <div>
                <h3 class="card-title mb-0 text-white text-lg">All certificate types</h3>
            </div>
        </div>
        <div class="card-body px-0 pb-4 pt-0">
            <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-slate-100">
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Slug</th>
                        <th>Templates</th>
                        <th>Active Template</th>
                        <th>Status</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($certificates as $certificate)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td class="font-semibold">{{ $certificate->name }}</td>
                            <td class="font-mono text-sm">{{ $certificate->slug }}</td>
                            <td>{{ $certificate->templates->count() }}</td>
                            <td>{{ $certificate->activeTemplate?->name ?? 'None' }}</td>
                            <td>
                                <span class="badge badge-{{ $certificate->is_active ? 'success' : 'secondary' }}">
                                    {{ $certificate->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td class="text-center">
                                <a href="{{ route('certificates.edit', $certificate) }}" class="btn btn-xs btn-warning">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('certificates.destroy', $certificate) }}" method="POST" class="d-inline"
                                    onsubmit="return confirm('Delete this certificate type?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-xs btn-danger"><i class="fas fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">No certificate types found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            </div>
        </div>
    </div>
</div>
@endsection
