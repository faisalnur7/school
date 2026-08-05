@extends('layouts.master')

@section('contents')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header flex justify-between items-center">
                        <h3 class="card-title font-bold text-white">Subjects (Classwise & Groupwise)</h3>
                        <div class="card-tools ml-auto">
                            <a href="{{ route('subjects.index') }}" class="btn btn-info btn-sm mr-2">
                                <i class="fas fa-list"></i> Default View
                            </a>
                            @if(auth()->user()?->hasPermission('create_subjects'))
                                <a href="{{ route('subjects.create') }}" class="btn btn-primary btn-sm">
                                    <i class="fas fa-plus"></i> Add Subject
                                </a>
                            @endif
                        </div>
                    </div>
                    <div class="card-body">
                        @include('pages.subjects.filter')

                        @forelse ($classwiseSubjects as $classData)
                            <div class="card mb-3">
                                <div class="card-header bg-secondary text-white">
                                    <strong>{{ $classData['class']->name_en ?? 'Unnamed Class' }}</strong>
                                </div>
                                <div class="card-body p-0">
                                    @forelse ($classData['groups'] as $groupData)
                                        <div class="p-3 border-bottom">
                                            <h6 class="mb-2">
                                                Group:
                                                {{ $groupData['group']->name_en ?? 'Common' }}
                                            </h6>

                                            <div class="table-responsive">
                                                <table class="table table-sm table-bordered mb-0">
                                                    <thead>
                                                        <tr>
                                                            <th>Name</th>
                                                            <th>Code</th>
                                                            <th>Type</th>
                                                            <th>Status</th>
                                                            <th>Papers</th>
                                                            <th width="150">Action</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @forelse ($groupData['subjects'] as $subject)
                                                            <tr>
                                                                <td>{{ $subject->name }}</td>
                                                                <td>{{ $subject->code }}</td>
                                                                <td>{{ ucfirst($subject->type) }}</td>
                                                                <td>
                                                                    @if($subject->is_active)
                                                                        <span class="badge badge-success">Active</span>
                                                                    @else
                                                                        <span class="badge badge-danger">Inactive</span>
                                                                    @endif
                                                                </td>
                                                                <td>{{ $subject->papers_count }}</td>
                                                                <td>
                                                                    <div class="d-inline-flex align-items-center gap-1">
                                                                        @if(auth()->user()?->hasPermission('view_subjects'))
                                                                            <a href="{{ route('subjects.show', $subject->id) }}"
                                                                                class="btn btn-info btn-xs p-1 d-inline-flex align-items-center justify-content-center"
                                                                                title="View" aria-label="View subject">
                                                                                <i class="fas fa-eye"></i>
                                                                            </a>
                                                                        @endif
                                                                        @if(auth()->user()?->hasPermission('edit_subjects'))
                                                                            <a href="{{ route('subjects.edit', $subject->id) }}"
                                                                                class="btn btn-warning btn-xs p-1 d-inline-flex align-items-center justify-content-center"
                                                                                title="Edit" aria-label="Edit subject">
                                                                                <i class="fas fa-pen"></i>
                                                                            </a>
                                                                        @endif
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                        @empty
                                                            <tr>
                                                                <td colspan="6" class="text-center">No subjects found</td>
                                                            </tr>
                                                        @endforelse
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="p-3 text-muted">No groups/subjects found for this class.</div>
                                    @endforelse
                                </div>
                            </div>
                        @empty
                            <div class="alert alert-info mb-0">No subjects found.</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    @include('scripts.common.load_academic_information')
@endsection
