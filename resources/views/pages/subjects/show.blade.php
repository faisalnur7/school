@extends('layouts.master')

@section('contents')
    <div class="container-fluid px-3 py-3">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-gradient-primary text-white py-3">
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                    <div>
                        <h4 class="card-title mb-1 font-weight-bold text-white">
                            <i class="fas fa-book mr-2"></i>{{ $subject->name }}
                        </h4>
                        <div class="small text-white-50">
                            Subject details, papers, assignments, and class-wise configuration
                        </div>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('subjects.index') }}" class="btn btn-light btn-sm">
                            <i class="fas fa-list mr-1"></i>Back to List
                        </a>
                        <a href="{{ route('subjects.edit', $subject->id) }}" class="btn btn-warning btn-sm">
                            <i class="fas fa-edit mr-1"></i>Edit
                        </a>
                    </div>
                </div>
            </div>

            <div class="card-body p-3">
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <div class="info-box shadow-sm">
                            <span class="info-box-icon bg-primary">
                                <i class="fas fa-tag"></i>
                            </span>
                            <div class="info-box-content">
                                <span class="info-box-text">Type</span>
                                <span class="info-box-number">{{ ucfirst($subject->type) }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="info-box shadow-sm">
                            <span class="info-box-icon bg-success">
                                <i class="fas fa-toggle-on"></i>
                            </span>
                            <div class="info-box-content">
                                <span class="info-box-text">Status</span>
                                <span class="info-box-number">{{ $subject->is_active ? 'Active' : 'Inactive' }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="info-box shadow-sm">
                            <span class="info-box-icon bg-info">
                                <i class="fas fa-sitemap"></i>
                            </span>
                            <div class="info-box-content">
                                <span class="info-box-text">Assigned Classes</span>
                                <span class="info-box-number">{{ $subject->class_assignments_count ?? $subject->classAssignments->count() }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="info-box shadow-sm">
                            <span class="info-box-icon bg-warning">
                                <i class="fas fa-layer-group"></i>
                            </span>
                            <div class="info-box-content">
                                <span class="info-box-text">Papers</span>
                                <span class="info-box-number">{{ $subject->papers_count ?? $subject->papers->count() }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-6 mb-3">
                        <div class="card card-outline card-primary h-100">
                            <div class="card-header py-2">
                                <h5 class="card-title mb-0">Basic Information</h5>
                            </div>
                            <div class="card-body p-0">
                                <table class="table table-borderless mb-0">
                                    <tbody>
                                        <tr>
                                            <th class="pl-3" style="width: 40%;">Subject Name</th>
                                            <td>{{ $subject->name }}</td>
                                        </tr>
                                        <tr>
                                            <th class="pl-3">Subject Code</th>
                                            <td>{{ $subject->code ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <th class="pl-3">Subject Type</th>
                                            <td>
                                                <span class="badge badge-{{ $subject->type === 'mandatory' ? 'primary' : 'info' }}">
                                                    {{ ucfirst($subject->type) }}
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="pl-3">Status</th>
                                            <td>
                                                <span class="badge badge-{{ $subject->is_active ? 'success' : 'danger' }}">
                                                    {{ $subject->is_active ? 'Active' : 'Inactive' }}
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="pl-3">Parent Subject</th>
                                            <td>
                                                @if($subject->parent)
                                                    <a href="{{ route('subjects.show', $subject->parent->id) }}">
                                                        {{ $subject->parent->name }}
                                                    </a>
                                                @else
                                                    <span class="text-muted">N/A</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="pl-3">Combined Subject</th>
                                            <td>{{ $subject->has_multiple_papers ? 'Yes' : 'No' }}</td>
                                        </tr>
                                        <tr>
                                            <th class="pl-3">Combine Papers for Result</th>
                                            <td>{{ $subject->combine_papers_for_result ? 'Yes' : 'No' }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-6 mb-3">
                        <div class="card card-outline card-info h-100">
                            <div class="card-header py-2">
                                <h5 class="card-title mb-0">Marks Summary</h5>
                            </div>
                            <div class="card-body p-0">
                                <table class="table table-borderless mb-0">
                                    <tbody>
                                        <tr>
                                            <th class="pl-3" style="width: 40%;">Creative (CQ)</th>
                                            <td>{{ number_format($subject->creative_marks, 2) }}</td>
                                        </tr>
                                        <tr>
                                            <th class="pl-3">MCQ</th>
                                            <td>{{ number_format($subject->mcq_marks, 2) }}</td>
                                        </tr>
                                        <tr>
                                            <th class="pl-3">Practical</th>
                                            <td>{{ number_format($subject->practical_marks, 2) }}</td>
                                        </tr>
                                        <tr>
                                            <th class="pl-3">Viva</th>
                                            <td>{{ number_format($subject->viva_marks, 2) }}</td>
                                        </tr>
                                        <tr>
                                            <th class="pl-3">Tutorial</th>
                                            <td>{{ number_format($subject->tutorial_marks, 2) }}</td>
                                        </tr>
                                        <tr>
                                            <th class="pl-3">Total Marks</th>
                                            <td><strong>{{ number_format($subject->total_marks, 2) }}</strong></td>
                                        </tr>
                                        <tr>
                                            <th class="pl-3">Pass Mark</th>
                                            <td>{{ number_format($subject->pass_mark, 2) }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                @if(($subject->papers_count ?? $subject->papers->count()) > 0)
                    <div class="card card-outline card-warning mb-3">
                        <div class="card-header py-2">
                            <h5 class="card-title mb-0">Papers</h5>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped mb-0">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Code</th>
                                            <th>Creative</th>
                                            <th>MCQ</th>
                                            <th>Practical</th>
                                            <th>Viva</th>
                                            <th>Total</th>
                                            <th>Pass</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($subject->papers as $paper)
                                            <tr>
                                                <td>
                                                    <a href="{{ route('subjects.show', $paper->id) }}">
                                                        {{ $paper->name }}
                                                    </a>
                                                </td>
                                                <td>{{ $paper->code ?? 'N/A' }}</td>
                                                <td>{{ number_format($paper->creative_marks, 2) }}</td>
                                                <td>{{ number_format($paper->mcq_marks, 2) }}</td>
                                                <td>{{ number_format($paper->practical_marks, 2) }}</td>
                                                <td>{{ number_format($paper->viva_marks, 2) }}</td>
                                                <td><strong>{{ number_format($paper->total_marks, 2) }}</strong></td>
                                                <td>{{ number_format($paper->pass_mark, 2) }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @endif

                @if(($subject->classConfigs_count ?? $subject->classConfigs->count()) > 0)
                    <div class="card card-outline card-success mb-3">
                        <div class="card-header py-2">
                            <h5 class="card-title mb-0">Class-wise Marks Configuration</h5>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped mb-0">
                                    <thead>
                                        <tr>
                                            <th>Class</th>
                                            <th>Creative</th>
                                            <th>MCQ</th>
                                            <th>Practical</th>
                                            <th>Viva</th>
                                            <th>Tutorial</th>
                                            <th>Total</th>
                                            <th>Pass</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($subject->classConfigs as $config)
                                            <tr>
                                                <td>{{ $config->schoolClass->name_en ?? 'Class #' . $config->school_class_id }}</td>
                                                <td>{{ number_format($config->creative_marks, 2) }}</td>
                                                <td>{{ number_format($config->mcq_marks, 2) }}</td>
                                                <td>{{ number_format($config->practical_marks, 2) }}</td>
                                                <td>{{ number_format($config->viva_marks, 2) }}</td>
                                                <td>{{ number_format($config->tutorial_marks ?? 0, 2) }}</td>
                                                <td><strong>{{ number_format($config->total_marks, 2) }}</strong></td>
                                                <td>{{ number_format($config->pass_mark, 2) }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @endif

                <div class="row">
                    <div class="col-lg-12 mb-3">
                        <div class="card card-outline card-secondary">
                            <div class="card-header py-2">
                                <h5 class="card-title mb-0">Class Assignments</h5>
                            </div>
                            <div class="card-body p-0">
                                @if($subject->classAssignments->count() > 0)
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-striped mb-0">
                                            <thead>
                                                <tr>
                                                    <th>Class</th>
                                                    <th>Group</th>
                                                    <th>Gender</th>
                                                    <th>Religion</th>
                                                    <th>Type</th>
                                                    <th>Exclusive Group</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($subject->classAssignments as $assignment)
                                                    <tr>
                                                        <td>{{ $assignment->schoolClass->name_en ?? 'N/A' }}</td>
                                                        <td>{{ $assignment->group->name_en ?? 'All Groups' }}</td>
                                                        <td>{{ ucfirst($assignment->gender) }}</td>
                                                        <td>{{ ucfirst($assignment->religion) }}</td>
                                                        <td>
                                                            @if($assignment->is_compulsory)
                                                                <span class="badge badge-primary">Compulsory</span>
                                                            @else
                                                                <span class="badge badge-info">Optional</span>
                                                            @endif
                                                        </td>
                                                        <td>{{ $assignment->exclusive_group_key ?? '-' }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @else
                                    <div class="p-3">
                                        <p class="text-muted mb-0">This subject is not assigned to any class yet.</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
