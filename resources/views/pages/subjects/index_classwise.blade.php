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
                            <a href="{{ route('subjects.index', array_merge(request()->query(), ['view' => 'list'])) }}" class="btn btn-info btn-sm mr-2">
                                <i class="fas fa-list"></i> List View
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
                                        <div class="classwise-class-summary d-flex align-items-center justify-content-between flex-wrap gap-2 mb-2">
                                            <div>
                                                <h5 class="classwise-class-title mb-1 font-weight-bold">{{ $className }}</h5>
                                                <div class="text-muted small">
                                                    {{ $classData ? collect($classData['groups'])->sum(fn ($group) => $group['subjects']->count()) : 0 }} subjects
                                                </div>
                                            </div>
                                        </div>

                                        @if($classData && !empty($classData['groups']))
                                            @forelse ($classData['groups'] as $groupData)
                                                <div class="subject-group mb-2">
                                                    <div class="subject-group-header" data-group-id="{{ $groupData['group']?->id }}">
                                                        <strong>Group: {{ $groupData['group']->name_en ?? 'Common' }}</strong>
                                                    </div>
                                                    <div class="table-responsive">
                                                        <table class="table table-sm table-bordered mb-0">
                                                                <thead>
                                                                    <tr>
                                                                        <th width="70">Order</th>
                                                                        <th>Name</th>
                                                                        <th>Code</th>
                                                                        <th>Type</th>
                                                                        <th>Status</th>
                                                                        <th>Tutorial Exam</th>
                                                                        <th>Papers</th>
                                                                        <th width="150">Action</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    @forelse ($groupData['subjects'] as $subject)
                                                                        @php
                                                                            $assignment = $subject->classAssignments->first(fn ($item) =>
                                                                                (int) $item->school_class_id === (int) $classId
                                                                                && (($groupData['group']?->id === null && $item->group_id === null)
                                                                                    || (int) $item->group_id === (int) $groupData['group']?->id)
                                                                            );
                                                                        @endphp
                                                                        <tr draggable="true" class="subject-order-row" data-assignment-id="{{ $assignment?->id }}">
                                                                            <td class="text-center subject-order-handle" title="Drag to reorder"><i class="fas fa-grip-vertical"></i> {{ $assignment?->sort_order ?? '-' }}</td>
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
                                                                            <td>@include('pages.subjects._tutorial-exam-toggle', ['subject' => $subject])</td>
                                                                            <td>{{ $subject->papers_count }}</td>
                                                                            <td>
                                                                                <div class="d-inline-flex align-items-center gap-1">
                                                                                    @if(auth()->user()?->hasPermission('view_subjects'))
                                                                                        <a href="{{ route('subjects.show', $subject->id) }}"
                                                                                            class="btn btn-xs classwise-action-btn classwise-action-view"
                                                                                            title="View" aria-label="View subject">
                                                                                            <i class="fas fa-eye"></i>
                                                                                        </a>
                                                                                    @endif
                                                                                    @if(auth()->user()?->hasPermission('edit_subjects'))
                                                                                        <a href="{{ route('subjects.edit', $subject->id) }}"
                                                                                            class="btn btn-xs classwise-action-btn classwise-action-edit"
                                                                                            title="Edit" aria-label="Edit subject">
                                                                                            <i class="fas fa-pen"></i>
                                                                                        </a>
                                                                                    @endif
                                                                                </div>
                                                                            </td>
                                                                        </tr>
                                                                    @empty
                                                                        <tr>
                                                                            <td colspan="8" class="text-center">No subjects found</td>
                                                                        </tr>
                                                                    @endforelse
                                                                </tbody>
                                                        </table>
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
            gap: 0;
            overflow-x: auto;
            overflow-y: hidden;
            padding-bottom: 0.25rem;
            white-space: nowrap;
            border-bottom: 1px solid #dbe3ef;
        }

        .classwise-subjects-card .nav-tabs .nav-item {
            flex: 0 0 auto;
        }

        .classwise-subjects-card .nav-tabs .nav-link {
            border-radius: 3px;
            border: 1px solid #cbd5e1;
            color: #2563eb;
            background: #fff;
            padding: 0.32rem 0.75rem;
            font-size: 0.86rem;
            font-weight: 600;
            transition: background-color 160ms ease, color 160ms ease, border-color 160ms ease;
        }

        .classwise-subjects-card .nav-tabs .nav-link:hover {
            background: #eff6ff;
            color: #1d4ed8;
            border-color: #93c5fd;
        }

        .classwise-subjects-card .nav-tabs .nav-link.active {
            background: #2563eb;
            color: #fff;
            border-color: #2563eb;
            box-shadow: 0 2px 0 #1d4ed8;
        }

        .subject-order-row { cursor: grab; }
        .subject-order-row.subject-order-dragging { opacity: 0.45; }
        .subject-order-handle { color: #64748b; white-space: nowrap; }

        .subject-group {
            overflow: hidden;
            border: 1px solid #e2e8f0;
            border-radius: 0.65rem;
            background: #fff;
        }

        .subject-group-header {
            padding: 0.65rem 0.85rem;
            border-bottom: 1px solid #e2e8f0;
            color: #0f172a;
            font-size: 0.9rem;
        }

        .subject-group .table {
            margin-bottom: 0;
            font-size: 0.82rem;
        }

        .subject-group .table th,
        .subject-group .table td {
            padding: 0.55rem 0.75rem;
            vertical-align: middle;
        }

        .classwise-class-title {
            font-size: 1.05rem;
        }

        .classwise-class-summary .text-muted {
            font-size: 0.78rem;
        }

        .classwise-subjects-card .nav-tabs::-webkit-scrollbar {
            height: 6px;
        }

        .classwise-subjects-card .nav-tabs::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 999px;
        }

        .classwise-subjects-card .classwise-action-btn {
            width: 1.9rem;
            height: 1.9rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0;
            border: 0;
            border-radius: 0.55rem;
            color: #fff !important;
            line-height: 1;
            box-shadow: none;
        }

        .classwise-subjects-card .classwise-action-view {
            background: #16a6b9 !important;
        }

        .classwise-subjects-card .classwise-action-edit {
            background: #334155 !important;
            border: 1px solid #475569 !important;
        }

        .classwise-subjects-card .classwise-action-btn:hover,
        .classwise-subjects-card .classwise-action-btn:focus {
            filter: brightness(1.08);
            transform: translateY(-1px);
        }
    </style>
@endsection

@section('scripts')
    <script>
        document.querySelectorAll('.subject-order-row').forEach(function (row) {
            row.addEventListener('dragstart', function () {
                row.classList.add('subject-order-dragging');
            });
            row.addEventListener('dragend', function () {
                row.classList.remove('subject-order-dragging');
                const tbody = row.closest('tbody');
                const ids = Array.from(tbody.querySelectorAll('.subject-order-row'))
                    .map(item => item.dataset.assignmentId)
                    .filter(Boolean);
                if (!ids.length) return;
                fetch('{{ route('subjects.reorder') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: JSON.stringify({
                        school_class_id: row.closest('.tab-pane').id.replace('class-pane-', ''),
                        group_id: row.closest('.subject-group').querySelector('[data-group-id]')?.dataset.groupId || null,
                        assignment_ids: ids,
                    }),
                }).then(response => {
                    if (!response.ok) window.location.reload();
                });
            });
            row.addEventListener('dragover', function (event) {
                event.preventDefault();
                const dragging = row.closest('tbody').querySelector('.subject-order-dragging');
                if (dragging && dragging !== row) {
                    row.parentNode.insertBefore(dragging, row);
                }
            });
        });
    </script>
@endsection

@section('scripts')
    @include('scripts.common.load_academic_information')
@endsection
