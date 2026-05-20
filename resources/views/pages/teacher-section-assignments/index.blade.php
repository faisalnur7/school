@extends('layouts.master')

@section('contents')
    <div class="container-fluid">
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
                <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show">
                <i class="fas fa-times-circle mr-2"></i>{{ session('error') }}
                <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
            </div>
        @endif



        <div class="card card-outline card-primary mb-3">
            <div class="card-header d-flex justify-content-between align-items-center mb-3">
                <h4 class="mb-0 font-weight-bold text-white">
                    <i class="fas fa-chalkboard-teacher text-primary mr-2"></i>Assign Teacher to Section
                </h4>
            </div>

            <div class="card-body py-2">
                <form method="POST" action="{{ route('teacher-section-assignments.store') }}"
                    class="form-inline d-flex align-items-center flex-wrap flex-md-nowrap tsa-assign-form">
                    @csrf
                    <select name="user_id" class="form-control form-control-sm mr-2 mb-1 tsa-teacher" required>
                        <option value="">Select Teacher</option>
                        @foreach ($teachers as $t)
                            <option value="{{ $t->id }}">
                                {{ $t->name }}@if ($t->email)
                                    ({{ $t->email }})
                                @endif
                            </option>
                        @endforeach
                    </select>

                    <select name="session_id" class="form-control form-control-sm mr-2 mb-1 tsa-session" required>
                        <option value="">Select Session</option>
                        @foreach ($sessions as $session)
                            <option value="{{ $session->id }}">{{ $session->name_en }}</option>
                        @endforeach
                    </select>

                    <select name="class_id" id="classSelect" class="form-control form-control-sm mr-2 mb-1 tsa-class"
                        required>
                        <option value="">Select Class</option>
                        @foreach ($classes as $class)
                            <option value="{{ $class->id }}">{{ $class->name_en }}</option>
                        @endforeach
                    </select>

                    <select name="section_id" id="sectionSelect" class="form-control form-control-sm mr-2 mb-1 tsa-section"
                        required>
                        <option value="">Select Section</option>
                    </select>

                    <button type="submit" class="btn btn-sm btn-success mb-1">
                        <i class="fas fa-save mr-1"></i>Assign
                    </button>
                </form>
            </div>
        </div>

        <div class="card card-outline card-info">
            <div class="card-header">
                <h6 class="mb-0 font-weight-bold text-white">
                    <i class="fas fa-list mr-2"></i>Assignments
                </h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-sm table-hover mb-0">
                        <thead class="thead-light">
                            <tr>
                                <th>Teacher</th>
                                <th style="width: 180px;">Session</th>
                                <th style="width: 160px;">Class</th>
                                <th style="width: 160px;">Section</th>
                                <th style="width: 90px;" class="text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($assignments as $a)
                                <tr>
                                    <td>{{ $a->user?->name ?? 'User#' . $a->user_id }}</td>
                                    <td>
                                        {{ $a->session?->name_en ?? 'Session#' . $a->session_id }}
                                        @if ($a->session?->name_bn)
                                            <br><small class="text-muted">{{ $a->session->name_bn }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        {{ $a->schoolClass?->name_en ?? 'Class#' . $a->class_id }}
                                        @if ($a->schoolClass?->name_bn)
                                            <br><small class="text-muted">{{ $a->schoolClass->name_bn }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        {{ $a->section?->name_en ?? 'Section#' . $a->section_id }}
                                        @if ($a->section?->name_bn)
                                            <br><small class="text-muted">{{ $a->section->name_bn }}</small>
                                        @endif
                                    </td>
                                    <td class="text-right">
                                        <form method="POST"
                                            action="{{ route('teacher-section-assignments.destroy', $a) }}"
                                            onsubmit="return confirm('Remove this assignment?')">
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
                                    <td colspan="5" class="text-center text-muted py-4">No assignments found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if ($assignments->hasPages())
                <div class="card-footer">{{ $assignments->links() }}</div>
            @endif
        </div>
    </div>
@endsection

@section('scripts')
    <style>
        @media (min-width: 768px) {
            .tsa-assign-form {
                flex-wrap: nowrap !important;
                gap: 0.35rem;
            }

            .tsa-assign-form .form-control {
                margin-right: 0 !important;
                margin-bottom: 0 !important;
            }

            .tsa-assign-form .tsa-teacher {
                width: 36%;
            }

            .tsa-assign-form .tsa-session {
                width: 20%;
            }

            .tsa-assign-form .tsa-class {
                width: 18%;
            }

            .tsa-assign-form .tsa-section {
                width: 18%;
            }

            .tsa-assign-form .btn {
                white-space: nowrap;
                margin-bottom: 0 !important;
            }
        }
    </style>
    <script>
        $(function() {
            $('#classSelect').on('change', function() {
                const classId = $(this).val();
                const $sectionSelect = $('#sectionSelect');
                $sectionSelect.html('<option value="">Select Section</option>');
                if (window.refreshSelect2) window.refreshSelect2($sectionSelect);
                if (!classId) return;

                $.ajax({
                    url: `{{ route('load_section_groups') }}`,
                    type: 'GET',
                    data: {
                        school_class_id: classId
                    },
                dataType: 'json',
                success: function(data) {
                    const sections = (data && Array.isArray(data.sections)) ? data.sections : [];
                    sections.forEach(function(s) {
                        $sectionSelect.append('<option value="' + s.id + '">' + s.name_en + '</option>');
                    });
                    if (window.refreshSelect2) window.refreshSelect2($sectionSelect);
                },
                    error: function() {
                        $sectionSelect.html('<option value="">Select Section</option>');
                        if (window.refreshSelect2) window.refreshSelect2($sectionSelect);
                    }
                });
            });
        });
    </script>
@endsection
