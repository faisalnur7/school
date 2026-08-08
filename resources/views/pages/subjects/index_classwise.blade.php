@extends('layouts.master')

@section('contents')
    @php
        $tabClassIds = array_keys($classes ?? []);
        foreach (array_keys($classwiseSubjects ?? []) as $classId) {
            if (!in_array($classId, $tabClassIds, true)) {
                $tabClassIds[] = $classId;
            }
        }

        $activeClassId = request('school_class_id') ?: ($tabClassIds[0] ?? null);
    @endphp

    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card classwise-subjects-card">
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

                        @if(!empty($tabClassIds))
                            <ul class="nav nav-tabs classwise-tabs" id="classwiseSubjectsTabs" role="tablist">
                                @foreach ($tabClassIds as $classId)
                                    @php
                                        $classData = $classwiseSubjects[$classId] ?? null;
                                        $className = $classes[$classId] ?? ($classData['class']->name_en ?? 'Unnamed Class');
                                        $isActive = (string) $activeClassId === (string) $classId;
                                    @endphp
                                    <li class="nav-item" role="presentation">
                                        <a
                                            class="nav-link {{ $isActive ? 'active' : '' }}"
                                            id="class-tab-{{ $classId }}"
                                            data-toggle="tab"
                                            href="#class-pane-{{ $classId }}"
                                            role="tab"
                                            aria-controls="class-pane-{{ $classId }}"
                                            aria-selected="{{ $isActive ? 'true' : 'false' }}"
                                        >
                                            {{ $className }}
                                        </a>
                                    </li>
                                @endforeach
                            </ul>

                            <div class="tab-content pt-3" id="classwiseSubjectsTabContent">
                                @foreach ($tabClassIds as $classId)
                                    @php
                                        $classData = $classwiseSubjects[$classId] ?? null;
                                        $className = $classes[$classId] ?? ($classData['class']->name_en ?? 'Unnamed Class');
                                        $isActive = (string) $activeClassId === (string) $classId;
                                    @endphp
                                    <div
                                        class="tab-pane fade {{ $isActive ? 'show active' : '' }}"
                                        id="class-pane-{{ $classId }}"
                                        role="tabpanel"
                                        aria-labelledby="class-tab-{{ $classId }}"
                                    >
                                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
                                            <div>
                                                <h5 class="mb-1 font-weight-bold">{{ $className }}</h5>
                                                <div class="text-muted small">
                                                    {{ $classData ? collect($classData['groups'])->sum(fn ($group) => $group['subjects']->count()) : 0 }} subjects
                                                </div>
                                            </div>
                                        </div>

                                        @if($classData && !empty($classData['groups']))
                                            @forelse ($classData['groups'] as $groupData)
                                                <div class="card mb-3 shadow-sm">
                                                    <div class="card-header bg-secondary text-white">
                                                        <strong>Group: {{ $groupData['group']->name_en ?? 'Common' }}</strong>
                                                    </div>
                                                    <div class="card-body p-0">
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
                                                </div>
                                            @empty
                                                <div class="alert alert-info mb-0">No groups/subjects found for this class.</div>
                                            @endforelse
                                        @else
                                            <div class="alert alert-info mb-0">No subjects found for this class.</div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="alert alert-info mb-0">No subjects found.</div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('styles')
    <style>
        .classwise-subjects-card .nav-tabs {
            display: flex;
            flex-wrap: nowrap;
            gap: 0.35rem;
            overflow-x: auto;
            overflow-y: hidden;
            padding-bottom: 0.25rem;
            white-space: nowrap;
        }

        .classwise-subjects-card .nav-tabs .nav-item {
            flex: 0 0 auto;
        }

        .classwise-subjects-card .nav-tabs .nav-link {
            border-radius: 999px;
            border: 1px solid #cbd5e1;
            color: #2563eb;
            background: #fff;
            padding: 0.4rem 0.85rem;
            font-size: 0.92rem;
        }

        .classwise-subjects-card .nav-tabs .nav-link.active {
            background: #2563eb;
            color: #fff;
            border-color: #2563eb;
        }

        .classwise-subjects-card .nav-tabs::-webkit-scrollbar {
            height: 6px;
        }

        .classwise-subjects-card .nav-tabs::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 999px;
        }
    </style>
@endsection

@section('scripts')
    @include('scripts.common.load_academic_information')
@endsection
