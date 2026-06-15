@extends('layouts.master')

@section('contents')
    <div class="container-fluid">
        <div class="card">
            <div class="card-header bg-gradient-primary text-white py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0 font-weight-bold text-white">
                        <i class="fas fa-award mr-2"></i>Scholarships
                    </h4>
                    <a href="{{ route('scholarships.create') }}" class="btn btn-light btn-sm">
                        <i class="fas fa-plus mr-1"></i> Assign Scholarships
                    </a>
                </div>
            </div>
            <div class="card-body">

                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show">
                        {{ session('success') }}
                        <button type="button" class="close" data-dismiss="alert">&times;</button>
                    </div>
                @endif

                {{-- Filters --}}
                <form method="GET" action="{{ route('scholarships.index') }}" id="filterForm">
                    <div class="row">
                        <div class="col-md-2">
                            <div class="form-group">
                                <label class="font-weight-bold">Academic Year</label>
                                <select name="session_id" class="form-control form-control-sm"
                                    onchange="this.form.submit()">
                                    <option value="">All Sessions</option>
                                    @foreach ($sessions as $s)
                                        <option value="{{ $s->id }}"
                                            {{ request('session_id') == $s->id ? 'selected' : '' }}>{{ $s->name_en }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Class</label>
                                <select name="class_id" class="form-control form-control-sm" id="classSelect">
                                    <option value="">All Classes</option>
                                    @foreach ($classes as $c)
                                        <option value="{{ $c->id }}"
                                            {{ request('class_id') == $c->id ? 'selected' : '' }}>{{ $c->name_en }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Section</label>
                                <select name="section_id" id="sectionSelect" class="form-control form-control-sm">
                                    <option value="">All Sections</option>
                                    @foreach ($sections as $sec)
                                        <option value="{{ $sec->id }}"
                                            {{ request('section_id') == $sec->id ? 'selected' : '' }}>{{ $sec->name_en }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Group</label>
                                <select name="group_id" class="form-control form-control-sm">
                                    <option value="">All Groups</option>
                                    @foreach ($groups as $g)
                                        <option value="{{ $g->id }}"
                                            {{ request('group_id') == $g->id ? 'selected' : '' }}>{{ $g->name_en }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4 d-flex align-items-center">
                            <div class="form-group mb-0">
                                <button type="submit" class="btn btn-primary btn-sm" title="Search"><i
                                        class="fas fa-search"></i></button>
                                <a href="{{ route('scholarships.index') }}" class="btn btn-secondary btn-sm ml-1"
                                    title="Reset"><i class="fas fa-times"></i></a>
                                @if (request()->hasAny(['session_id', 'class_id', 'section_id', 'group_id']) && $scholarships->total() > 0)
                                    <button type="button" class="btn btn-success btn-sm ml-1" onclick="window.print()"
                                        title="Print"><i class="fas fa-print"></i></button>
                                    <a href="{{ route('scholarships.pdf', request()->query()) }}"
                                        class="btn btn-danger btn-sm ml-1" title="Export PDF"><i
                                            class="fas fa-file-pdf"></i></a>
                                @endif
                            </div>
                        </div>
                    </div>
                </form>

                <hr>

                <div class="table-responsive">
                    <table class="table table-bordered table-hover table-sm">
                        <thead class="thead-dark">
                            <tr>
                                <th>#</th>
                                <th>Student ID</th>
                                <th>Roll</th>
                                <th>Student Name</th>
                                <th>Class</th>
                                <th>Section</th>
                                <th>Group</th>
                                <th>Fee Category</th>
                                <th>Type</th>
                                <th>Value</th>
                                <th>Session</th>
                                <th>Status</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($scholarships as $i => $scholarship)
                                @php $ai = $scholarship->studentAcademicInformation; @endphp
                                <tr>
                                    <td>{{ $scholarships->firstItem() + $i }}</td>
                                    <td>{{ $scholarship->student->student_cid ?? '—' }}</td>
                                    <td>{{ $ai?->roll ?? '—' }}</td>
                                    <td class="font-weight-bold">{{ $scholarship->student->full_name_en ?? '—' }}</td>
                                    <td>{{ $ai?->schoolClass?->name_en ?? '—' }}</td>
                                    <td>{{ $ai?->section?->name_en ?? '—' }}</td>
                                    <td>{{ $ai?->group?->name_en ?? '—' }}</td>
                                    <td>{{ $scholarship->feeCategory->name ?? '—' }}</td>
                                    <td>
                                        <span
                                            class="badge badge-{{ $scholarship->type === 'fixed' ? 'info' : 'warning' }}">
                                            {{ ucfirst($scholarship->type) }}
                                        </span>
                                    </td>
                                    <td class="font-weight-bold">
                                        @if ($scholarship->type === 'fixed')
                                            ৳{{ number_format($scholarship->amount, 2) }}
                                        @else
                                            {{ $scholarship->percentage }}%
                                        @endif
                                    </td>
                                    <td>{{ $scholarship->academicSession->name_en ?? '—' }}</td>
                                    <td>
                                        <span
                                            class="badge badge-{{ $scholarship->status === 'active' ? 'success' : 'secondary' }}">
                                            {{ ucfirst($scholarship->status) }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <form action="{{ route('scholarships.destroy', $scholarship) }}" method="POST"
                                            class="d-inline" onsubmit="return confirm('Delete this scholarship?')">
                                            @csrf @method('DELETE')
                                            <button class="btn btn-xs btn-danger" title="Delete"><i
                                                    class="fas fa-trash"></i></button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="13" class="text-center text-muted py-4">No scholarships found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    {{ $scholarships->links() }}
                </div>

            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const classSelect = document.getElementById('classSelect');
            const sectionSelect = document.getElementById('sectionSelect');
            const selectedSection = @json(request('section_id'));

            function refreshSectionSelect() {
                if (window.refreshSelect2) {
                    window.refreshSelect2($(sectionSelect));
                }
            }

            function replaceSectionOptions(html) {
                const $section = $(sectionSelect);
                if ($section.hasClass('select2-hidden-accessible')) {
                    $section.select2('destroy');
                }
                sectionSelect.innerHTML = html;
                if (window.reinitSelect2) {
                    window.reinitSelect2(sectionSelect.parentElement);
                } else {
                    refreshSectionSelect();
                }
            }

            function loadSections(classId, selectedSectionId = null) {
                if (!sectionSelect) return;

                replaceSectionOptions('<option value="">Loading...</option>');

                if (!classId) {
                    replaceSectionOptions('<option value="">All Sections</option>');
                    return;
                }

                fetch(`{{ route('load_section_groups') }}?school_class_id=${encodeURIComponent(classId)}`)
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Failed to load sections');
                        }
                        return response.json();
                    })
                    .then(data => {
                        const sections = Array.isArray(data?.sections) ? data.sections : [];
                        let html = '<option value="">All Sections</option>';

                        sections.forEach(section => {
                            const selected = String(selectedSectionId) === String(section.id) ? 'selected' : '';
                            html += `<option value="${section.id}" ${selected}>${section.name_en}</option>`;
                        });

                        replaceSectionOptions(html);
                    })
                    .catch(() => {
                        replaceSectionOptions('<option value="">All Sections</option>');
                    });
            }

            $(document).on('change', '#classSelect', function() {
                loadSections(this.value);
            });

            if (classSelect && classSelect.value) {
                loadSections(classSelect.value, selectedSection);
            }
        });
    </script>

    <style>
        @media print {

            .main-sidebar,
            .main-header,
            .content-header,
            form,
            hr,
            button,
            a.btn {
                display: none !important;
            }

            .content-wrapper {
                margin-left: 0 !important;
            }
        }
    </style>
@endsection
