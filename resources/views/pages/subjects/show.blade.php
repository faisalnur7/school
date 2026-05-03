@extends('layouts.master')

@section('contents')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Subject Details</h3>
                    <div class="card-tools">
                        <a href="{{ route('subjects.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-list"></i> Back to List
                        </a>
                        <a href="{{ route('subjects.edit', $subject->id) }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <th width="40%">Subject Name</th>
                                    <td>{{ $subject->name }}</td>
                                </tr>
                                <tr>
                                    <th>Subject Code</th>
                                    <td>{{ $subject->code ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Type</th>
                                    <td>
                                        <span class="badge badge-{{ $subject->type == 'mandatory' ? 'primary' : 'info' }}">
                                            {{ ucfirst($subject->type) }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Status</th>
                                    <td>
                                        <span class="badge badge-{{ $subject->is_active ? 'success' : 'danger' }}">
                                            {{ $subject->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                </tr>
                                @if($subject->parent)
                                <tr>
                                    <th>Parent Subject</th>
                                    <td>
                                        <a href="{{ route('subjects.show', $subject->parent->id) }}">
                                            {{ $subject->parent->name }}
                                        </a>
                                    </td>
                                </tr>
                                @endif
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <th>Has Multiple Papers</th>
                                    <td>{{ $subject->has_multiple_papers ? 'Yes' : 'No' }}</td>
                                </tr>
                                <tr>
                                    <th>Combine Papers for Result</th>
                                    <td>{{ $subject->combine_papers_for_result ? 'Yes' : 'No' }}</td>
                                </tr>
                                <tr>
                                    <th>Total Marks</th>
                                    <td><strong>{{ number_format($subject->total_marks, 2) }}</strong></td>
                                </tr>
                                <tr>
                                    <th>Pass Mark</th>
                                    <td>{{ number_format($subject->pass_mark, 2) }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <!-- Papers Section -->
                    @if($subject->is_parent || $subject->papers()->count() > 0)
                    <hr>
                    <h5>Papers</h5>
                    @if($subject->papers()->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
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
                                    <td>{{ $paper->creative_marks }}</td>
                                    <td>{{ $paper->mcq_marks }}</td>
                                    <td>{{ $paper->practical_marks }}</td>
                                    <td>{{ $paper->viva_marks }}</td>
                                    <td><strong>{{ number_format($paper->total_marks, 2) }}</strong></td>
                                    <td>{{ $paper->pass_mark }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                        <p class="text-muted">This subject has no papers defined.</p>
                    @endif
                    @endif

                    <!-- Class-wise Configs Section -->
                    @if($subject->classConfigs()->count() > 0)
                    <hr>
                    <h5>Class-wise Marks Configuration</h5>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Class</th>
                                    <th>Creative</th>
                                    <th>MCQ</th>
                                    <th>Practical</th>
                                    <th>Viva</th>
                                    <th>Total</th>
                                    <th>Pass</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($subject->classConfigs as $config)
                                <tr>
                                    <td>{{ $config->schoolClass->name_en ?? 'Class #'.$config->school_class_id }}</td>
                                    <td>{{ $config->creative_marks }}</td>
                                    <td>{{ $config->mcq_marks }}</td>
                                    <td>{{ $config->practical_marks }}</td>
                                    <td>{{ $config->viva_marks }}</td>
                                    <td><strong>{{ number_format($config->total_marks, 2) }}</strong></td>
                                    <td>{{ $config->pass_mark }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @endif

                    <hr>
                    <h5>Marks Distribution</h5>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="info-box">
                                <span class="info-box-text">Creative (CQ)</span>
                                <span class="info-box-number">{{ number_format($subject->creative_marks, 2) }}</span>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-box">
                                <span class="info-box-text">MCQ</span>
                                <span class="info-box-number">{{ number_format($subject->mcq_marks, 2) }}</span>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-box">
                                <span class="info-box-text">Practical</span>
                                <span class="info-box-number">{{ number_format($subject->practical_marks, 2) }}</span>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-box">
                                <span class="info-box-text">Viva</span>
                                <span class="info-box-number">{{ number_format($subject->viva_marks, 2) }}</span>
                            </div>
                        </div>
                    </div>

                    <hr>
                    <h5>Class Assignments</h5>
                    @if($subject->classAssignments->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
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
                        <p class="text-muted">This subject is not assigned to any class yet.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
