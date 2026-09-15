@extends('layouts.master')

@section('contents')
    @php
        $isCreate = $isCreate ?? false;
        $activeAssignments = $isCreate ? collect() : $subject->classAssignments->where('is_active', true);
        $selectedClassIds = array_map('strval', (array) old('school_class_ids', $activeAssignments->pluck('school_class_id')->all()));
        $currentAssignment = $activeAssignments->first() ?? ($isCreate ? null : $subject->classAssignments->first());
        $selectedGroupId = old('group_id', $currentAssignment?->group_id);
        $selectedGender = old('gender', $currentAssignment?->gender ?? 'all');
        $selectedReligion = old('religion', $currentAssignment?->religion ?? 'all');
        $selectedIsOptional = old('is_optional', $currentAssignment?->is_optional);
        $selectedExclusiveGroupKey = old('exclusive_group_key', $currentAssignment?->exclusive_group_key);
    @endphp
    <div class="container-fluid px-3 py-3 subject-edit-page">
        <div class="card subject-edit-card border-0">
        <div class="card-header subject-edit-header text-white py-3">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="subject-edit-eyebrow">{{ __('Subject settings') }}</div>
                    <h4 class="card-title mb-1 font-weight-bold text-white">
                        <i class="fas fa-{{ $isCreate ? 'plus-circle' : 'edit' }} mr-2"></i>{{ $isCreate ? __('Create Subject') : __('Edit Subject') }}
                    </h4>
                    <div class="subject-edit-subtitle">
                        {{ $isCreate ? __('Set up a new subject') : $subject->name . ($subject->code ? ' · ' . $subject->code : '') }}
                    </div>
                </div>
                <a href="{{ route('subjects.index') }}" class="btn btn-light btn-sm">
                    <i class="fas fa-arrow-left mr-1"></i> {{ __('Back') }}
                </a>
            </div>
        </div>

        <form method="POST" action="{{ $isCreate ? route('subjects.store') : route('subjects.update', $subject->id) }}" id="modernForm">
            @csrf
            @if(!$isCreate)
                @method('PUT')
            @endif
            <input type="hidden" name="assign_to_class" value="0">
            <input type="hidden" name="school_class_ids[]" value="">

            <div class="card-body p-3">
                @if($errors->any())
                    <div id="form-feedback" class="alert alert-danger alert-dismissible fade show border-0 mb-3" role="alert" tabindex="-1">
                        <i class="fas fa-exclamation-circle mr-2"></i>
                        <strong>{{ __('Errors:') }}</strong>
                        <ul class="mb-0 mt-1 ml-4">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

                @if(!$isCreate && session('unassignment_warning'))
                    <div id="unassignment-warning" class="alert alert-warning subject-warning border-0 mb-3" role="alert">
                        <i class="fas fa-exclamation-triangle mr-2"></i>
                        <strong>{{ __('Existing exam marks found.') }}</strong>
                        <p class="mb-2 mt-1">
                            {{ __('This subject has saved exam marks for:') }}
                            {{ collect(session('unassignment_warning'))->pluck('class_name')->join(', ', ' and ') }}.
                            {{ __('The class assignment will be archived, but the marks will be preserved.') }}
                        </p>
                        <button type="submit" name="confirm_unassignment_with_marks" value="1" class="btn btn-warning btn-sm">
                            <i class="fas fa-check mr-1"></i>{{ __('Continue and preserve marks') }}
                        </button>
                    </div>
                @endif

                <div class="subject-help-panel" role="note">
                    <div class="subject-help-panel-title"><i class="fas fa-info-circle mr-2"></i>{{ $isCreate ? __('How to create this subject') : __('How to update this subject') }}</div>
                    <p>{{ $isCreate ? __('Enter the details below, then click Create Subject. The subject will be available according to the classes and settings you choose.') : __('Review each section below, then click Save changes. Your changes will apply to the selected classes and students.') }}</p>
                    <div class="subject-help-steps">
                        <span><strong>{{ __('Basic information:') }}</strong> {{ __('Enter the subject details.') }}</span>
                        <span><strong>{{ __('Marks distribution:') }}</strong> {{ __('Enter the maximum and pass marks.') }}</span>
                        <span><strong>{{ __('Class assignment:') }}</strong> {{ __('Choose which classes use this subject.') }}</span>
                        <span><strong>{{ __('Student options:') }}</strong> {{ __('Use filters and optional settings only when they apply.') }}</span>
                    </div>
                </div>

<div class="subject-section subject-section--basic">
                        <div class="subject-section-heading">
                            <div>
                                <h5 class="subject-section-title">{{ __('Basic information') }}</h5>
                                <p class="subject-section-help">{{ __('Set the subject name, short code, type, and current status.') }}</p>
                            </div>
                        </div>
                        <div class="row subject-basic-fields">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="name">{{ __('Subject Name') }} <span class="text-danger">*</span></label>
                                    <input type="text" name="name" id="name" class="form-control" 
                                        value="{{ old('name', $subject->name) }}" required>
                                    <small class="form-text text-muted">{{ __('This name appears in class lists, mark entry, and reports.') }}</small>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="code">{{ __('Subject Code') }}</label>
                                    <input type="text" name="code" id="code" class="form-control" 
                                        value="{{ old('code', $subject->code) }}" placeholder="Optional unique code">
                                    <small class="form-text text-muted">{{ __('Use a short unique code to identify this subject.') }}</small>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="type">{{ __('Type') }} <span class="text-danger">*</span></label>
                                    <select name="type" id="type" class="form-control" required>
                                        <option value="mandatory" {{ old('type', $subject->type) == 'mandatory' ? 'selected' : '' }}>{{ __('Mandatory') }}</option>
                                        <option value="optional" {{ old('type', $subject->type) == 'optional' ? 'selected' : '' }}>{{ __('Optional') }}</option>
                                    </select>
                                    <small class="form-text text-muted">{{ __('Mandatory subjects are automatically included; optional subjects must be selected for students.') }}</small>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="is_active">{{ __('Status') }}</label>
                                    <select name="is_active" id="is_active" class="form-control">
                                        <option value="1" {{ old('is_active', $isCreate ? '1' : $subject->is_active) == '1' ? 'selected' : '' }}>{{ __('Active') }}</option>
                                        <option value="0" {{ old('is_active', $isCreate ? '1' : $subject->is_active) == '0' ? 'selected' : '' }}>{{ __('Inactive') }}</option>
                                    </select>
                                    <small class="form-text text-muted">{{ __('Use Active for a subject currently in use. Use Inactive when it should no longer be used.') }}</small>
                                </div>
                            </div>
                        </div>

                        <div class="row subject-option-grid">
                            <div class="col-md-6">
                                <div class="form-group subject-option-card">
                                    <div class="form-check">
                                        <input type="checkbox" name="has_multiple_papers" id="has_multiple_papers" 
                                            class="form-check-input" value="1" {{ old('has_multiple_papers', $subject->has_multiple_papers) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="has_multiple_papers">
                                            {{ __('Has Multiple Papers (Combined Subject)') }}
                                        </label>
                                        <small class="d-block text-muted">{{ __('Check this when the subject has separate papers, such as First Paper and Second Paper. Add each paper in the section that appears below.') }}</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group subject-option-card">
                                    <div class="form-check">
                                        <input type="checkbox" name="combine_papers_for_result" id="combine_papers_for_result" 
                                            class="form-check-input" value="1" {{ old('combine_papers_for_result', $isCreate ? '1' : $subject->combine_papers_for_result) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="combine_papers_for_result">
                                            {{ __('Combine Papers for Result') }}
                                        </label>
                                        <small class="d-block text-muted">{{ __('Use this when the separate papers should be added together in the final result.') }}</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        </div>

                        <!-- Papers Management Section -->
                        <div id="papers_section" class="subject-section subject-section--nested" style="display: none;">
                            <hr>
                            <div class="subject-section-heading">
                                <div>
                                    <h5 class="subject-section-title">{{ __('Papers configuration') }}</h5>
                                    <p class="subject-section-help">{{ __('Add and set the marks for each paper that makes up this combined subject.') }}</p>
                                </div>
                                <span class="subject-count-badge" id="paper_count">{{ trans_choice(':count papers', $subject->papers->count(), ['count' => $subject->papers->count()]) }}</span>
                            </div>
                            
                            <div id="papers_container">
                                @foreach($subject->papers as $index => $paper)
                                <div class="paper-item card card-body mb-2" style="border-left: 4px solid #007bff;">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>{{ __('Paper Name') }} <span class="text-danger">*</span></label>
                                                <input type="text" name="papers[{{ $index }}][id]" value="{{ $paper->id }}" hidden>
                                                <input type="text" name="papers[{{ $index }}][name]" class="form-control" 
                                                    value="{{ old('papers.'.$index.'.name', $paper->name) }}" required 
                                                    placeholder="e.g., Bangla 1st Paper">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>{{ __('Paper Code') }}</label>
                                                <input type="text" name="papers[{{ $index }}][code]" class="form-control" 
                                                    value="{{ old('papers.'.$index.'.code', $paper->code) }}" placeholder="Optional">
                                            </div>
                                        </div>
                                        <div class="col-md-5">
                                            <div class="form-group">
                                                <label>&nbsp;</label>
                                                <div>
                                                    <button type="button" class="btn btn-danger btn-sm remove-paper-btn">
                                                        <i class="fas fa-trash"></i> {{ __('Remove') }}
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>{{ __('Creative Marks') }}</label>
                                                <input type="number" name="papers[{{ $index }}][creative_marks]" class="form-control paper-marks" 
                                                    value="{{ old('papers.'.$index.'.creative_marks', $paper->creative_marks) }}" min="0" step="0.01">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>{{ __('MCQ Marks') }}</label>
                                                <input type="number" name="papers[{{ $index }}][mcq_marks]" class="form-control paper-marks" 
                                                    value="{{ old('papers.'.$index.'.mcq_marks', $paper->mcq_marks) }}" min="0" step="0.01">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>{{ __('Practical Marks') }}</label>
                                                <input type="number" name="papers[{{ $index }}][practical_marks]" class="form-control paper-marks" 
                                                    value="{{ old('papers.'.$index.'.practical_marks', $paper->practical_marks) }}" min="0" step="0.01">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>{{ __('Viva Marks') }}</label>
                                                <input type="number" name="papers[{{ $index }}][viva_marks]" class="form-control paper-marks" 
                                                    value="{{ old('papers.'.$index.'.viva_marks', $paper->viva_marks) }}" min="0" step="0.01">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>{{ __('Tutorial Marks') }}</label>
                                                <input type="number" name="papers[{{ $index }}][tutorial_marks]" class="form-control paper-marks" 
                                                    value="{{ old('papers.'.$index.'.tutorial_marks', $paper->tutorial_marks) }}" min="0" step="0.01">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>{{ __('Pass Mark') }}</label>
                                                <input type="number" name="papers[{{ $index }}][pass_mark]" class="form-control" 
                                                    value="{{ old('papers.'.$index.'.pass_mark', $paper->pass_mark) }}" min="0" step="0.01">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                            
                            <button type="button" id="add_paper_btn" class="btn btn-sm btn-success">
                                <i class="fas fa-plus"></i> {{ __('Add Paper') }}
                            </button>
                        </div>

                        <div class="subject-section">
                        <hr>
                        <div class="subject-section-heading">
                            <div>
                                <h5 class="subject-section-title">{{ __('Marks distribution') }}</h5>
                                <p class="subject-section-help">{{ __('Enter the maximum marks for each part and the minimum mark required to pass.') }}</p>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="creative_marks">{{ __('Creative Marks (CQ)') }}</label>
                                    <input type="number" name="creative_marks" id="creative_marks" class="form-control marks-input" 
                                        value="{{ old('creative_marks', $subject->creative_marks) }}" min="0" step="0.01">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="mcq_marks">{{ __('MCQ Marks') }}</label>
                                    <input type="number" name="mcq_marks" id="mcq_marks" class="form-control marks-input" 
                                        value="{{ old('mcq_marks', $subject->mcq_marks) }}" min="0" step="0.01">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="practical_marks">{{ __('Practical Marks') }}</label>
                                    <input type="number" name="practical_marks" id="practical_marks" class="form-control marks-input" 
                                        value="{{ old('practical_marks', $subject->practical_marks) }}" min="0" step="0.01">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="viva_marks">{{ __('Viva Marks') }}</label>
                                    <input type="number" name="viva_marks" id="viva_marks" class="form-control marks-input" 
                                        value="{{ old('viva_marks', $subject->viva_marks) }}" min="0" step="0.01">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="tutorial_marks">{{ __('Tutorial Marks') }}</label>
                                    <input type="number" name="tutorial_marks" id="tutorial_marks" class="form-control"
                                        value="{{ old('tutorial_marks', $subject->tutorial_marks ?? 0) }}" min="0" step="0.01"
                                        placeholder="Used for tutorial exams">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="pass_mark">{{ __('Pass Mark') }}</label>
                                    <input type="number" name="pass_mark" id="pass_mark" class="form-control" 
                                        value="{{ old('pass_mark', $subject->pass_mark) }}" min="0" step="0.01">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="total_marks">{{ __('Total marks') }} <span class="field-hint">({{ __('calculated automatically') }})</span></label>
                                    <input type="text" class="form-control marks-total" id="total_marks" readonly
                                        value="{{ number_format($subject->total_marks, 2) }}" disabled>
                                    <small class="form-text text-muted" id="marks_summary" aria-live="polite">{{ __('Based on the marks entered above.') }}</small>
                                </div>
                            </div>
                        </div>
                        </div>

                        <!-- Class-wise Marks Configuration -->
                        <div class="subject-section">
                        <hr>
                        <div class="subject-section-heading">
                            <div>
                                <h5 class="subject-section-title">{{ __('Class-wise marks') }}</h5>
                                <p class="subject-section-help">{{ __('Use this only when a class needs different marks from the subject default. These values override the general marks above.') }}</p>
                            </div>
                        </div>
                        
                        @if($subject->classConfigs->count() > 0)
                        <div class="table-responsive mb-3 subject-config-table-wrap">
                            <table class="table subject-config-table mb-0">
                                <thead>
                                    <tr>
                                        <th scope="col">Class</th>
                                        <th scope="col">Creative</th>
                                        <th scope="col">MCQ</th>
                                        <th scope="col">Practical</th>
                                        <th scope="col">Viva</th>
                                        <th scope="col">Tutorial</th>
                                        <th scope="col">Total</th>
                                        <th scope="col">Pass</th>
                                        <th scope="col" class="text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($subject->classConfigs as $config)
                                    <tr>
                                        <td>
                                            <span class="config-class-name">
                                                <span class="config-class-dot" aria-hidden="true"></span>
                                                {{ $config->schoolClass->name_en ?? 'Class #'.$config->school_class_id }}
                                            </span>
                                        </td>
                                        <td>{{ number_format((float) $config->creative_marks, 2) }}</td>
                                        <td>{{ number_format((float) $config->mcq_marks, 2) }}</td>
                                        <td>{{ number_format((float) $config->practical_marks, 2) }}</td>
                                        <td>{{ number_format((float) $config->viva_marks, 2) }}</td>
                                        <td>{{ number_format((float) ($config->tutorial_marks ?? 0), 2) }}</td>
                                        <td><strong class="config-total-value">{{ number_format((float) $config->total_marks, 2) }}</strong></td>
                                        <td><span class="config-pass-value">{{ number_format((float) $config->pass_mark, 2) }}</span></td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-sm btn-danger config-remove-btn" onclick="deleteClassConfig({{ $config->id }})" aria-label="Remove {{ $config->schoolClass->name_en ?? 'class configuration' }}" title="Remove class configuration">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @endif

                        <div id="class_configs_container">
                            <!-- Dynamic class configs for editing/adding -->
                        </div>
                        
                        <button type="button" id="add_class_config_btn" class="btn btn-sm btn-outline-primary">
                            <i class="fas fa-plus"></i> {{ __('Add Class Configuration') }}
                        </button>
                        </div>

                        <div class="subject-section subject-assignment-section">
                        <hr>
                        <div class="subject-section-heading">
                            <div>
                                <h5 class="subject-section-title">{{ __('Class assignment') }}</h5>
                                <p class="subject-section-help">{{ __('Choose which classes can use this subject. Existing marks remain safe when an assignment is removed.') }}</p>
                            </div>
                            <span class="subject-count-badge" id="selected_class_count" aria-live="polite">{{ __(':count of :total classes selected', ['count' => count($selectedClassIds), 'total' => count($classes)]) }}</span>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-check mb-2">
                                    <input type="checkbox" name="assign_to_class" id="assign_to_class" 
                                        class="form-check-input" value="1" {{ $activeAssignments->count() > 0 ? 'checked' : '' }}>
                                    <label class="form-check-label" for="assign_to_class">
                                        {{ __('Assign this subject to classes') }}
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div id="class_assignment_fields" style="display: {{ $activeAssignments->count() > 0 ? 'block' : 'none' }};">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <span class="subject-field-label">{{ __('Select classes') }}</span>
                                            <div class="subject-selection-actions">
                                                <button type="button" class="btn btn-link btn-sm p-0" id="select_all_classes">{{ __('Select all') }}</button>
                                                <span aria-hidden="true">·</span>
                                                <button type="button" class="btn btn-link btn-sm p-0" id="clear_all_classes">{{ __('Clear all') }}</button>
                                            </div>
                                        </div>
                                        <div class="subject-class-grid" role="group" aria-label="Select classes">
                                            @foreach($classes as $id => $name)
                                                <label class="subject-class-item">
                                                    <input type="checkbox" id="school_class_{{ $id }}" name="school_class_ids[]" value="{{ $id }}"
                                                        {{ in_array((string) $id, $selectedClassIds, true) ? 'checked' : '' }}>
                                                    <span class="subject-class-item__icon">
                                                        <i class="fas fa-check"></i>
                                                    </span>
                                                    <span class="subject-class-item__label">{{ $name }}</span>
                                                </label>
                                            @endforeach
                                        </div>
                                        <small class="text-muted">{{ __('Choose every class that should use this subject. Unchecked classes will be unassigned after saving.') }}</small>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="group_id">{{ __('Apply to group') }} <span class="field-hint">({{ __('optional') }})</span></label>
                                        <select name="group_id" id="group_id" class="form-control">
                                            <option value="">{{ __('All Groups') }}</option>
                                            @foreach($groups as $id => $name)
                                                <option value="{{ $id }}" {{ (string) $selectedGroupId === (string) $id ? 'selected' : '' }}>
                                                    {{ $name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <small class="text-muted">{{ __('Choose a group to limit this subject to students in that group. Leave All Groups to include every group.') }}</small>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="gender">{{ __('Apply to gender') }}</label>
                                        <select name="gender" id="gender" class="form-control">
                                            <option value="all" {{ $selectedGender === 'all' ? 'selected' : '' }}>{{ __('All Students') }}</option>
                                            <option value="male" {{ $selectedGender === 'male' ? 'selected' : '' }}>{{ __('Male Only') }}</option>
                                            <option value="female" {{ $selectedGender === 'female' ? 'selected' : '' }}>{{ __('Female Only') }}</option>
                                        </select>
                                        <small class="form-text text-muted">{{ __('Choose All Students unless this subject is only for male or female students.') }}</small>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="religion">{{ __('Apply to religion') }}</label>
                                        <select name="religion" id="religion" class="form-control">
                                            <option value="all" {{ $selectedReligion === 'all' ? 'selected' : '' }}>{{ __('All Religions') }}</option>
                                            <option value="islam" {{ $selectedReligion === 'islam' ? 'selected' : '' }}>{{ __('Islam') }}</option>
                                            <option value="hindu" {{ $selectedReligion === 'hindu' ? 'selected' : '' }}>{{ __('Hindu') }}</option>
                                            <option value="christian" {{ $selectedReligion === 'christian' ? 'selected' : '' }}>{{ __('Christian') }}</option>
                                            <option value="buddhist" {{ $selectedReligion === 'buddhist' ? 'selected' : '' }}>{{ __('Buddhist') }}</option>
                                            <option value="other" {{ $selectedReligion === 'other' ? 'selected' : '' }}>{{ __('Other') }}</option>
                                        </select>
                                        <small class="form-text text-muted">{{ __('Choose All Religions unless this subject is only for students of a specific religion.') }}</small>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>&nbsp;</label>
                                        <div class="form-check mt-2">
                                            <input type="checkbox" name="is_optional" id="is_optional" 
                                                class="form-check-input" value="1" {{ $selectedIsOptional ? 'checked' : '' }}>
                                            <label class="form-check-label" for="is_optional">
                                                {{ __('Optional subject') }}
                                            </label>
                                        </div>
                                        <small class="form-text text-muted">{{ __('Check this when students should choose the subject instead of receiving it automatically.') }}</small>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="exclusive_group_key">{{ __('Choice group') }} <span class="field-hint">({{ __('optional') }})</span></label>
                                        <input type="text" name="exclusive_group_key" id="exclusive_group_key" class="form-control"
                                            value="{{ $selectedExclusiveGroupKey }}" placeholder="e.g., science_core_choice">
                                        <small class="text-muted">
                                            <strong>{{ __('How to use it:') }}</strong> {{ __('Use this only for optional subjects where students must choose one subject from a set of subjects.') }}
                                            {{ __('Enter the exact same group name for every subject in that set.') }}
                                            <br>
                                            <strong>{{ __('Example:') }}</strong> {{ __('Enter the same group name, :group, for Biology and Higher Math.', ['group' => 'Science']) }}
                                            {{ __('When both subjects are assigned to the same class, the student will see them together and can choose Biology, Higher Math, or None.') }}
                                            <br>
                                            {{ __('Leave this field blank when the subject can be selected independently from other optional subjects.') }}
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        </div>
            </div>

            <div class="card-footer subject-edit-footer bg-light border-top py-2 px-3">
                <div class="d-flex justify-content-between gap-2">
                    <a href="{{ route('subjects.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-times mr-1"></i>{{ __('Cancel') }}
                    </a>
                    <button type="submit" class="btn btn-primary btn-sm" id="save_subject_btn">
                        <i class="fas fa-save mr-1"></i>{{ $isCreate ? __('Create Subject') : __('Save changes') }}
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@section('styles')
@include('components.form-styles')
<style>
    .subject-edit-page {
        flex: 0 0 100%;
        width: 100%;
        max-width: none;
        margin: 0;
    }

    .subject-edit-page .form-control,
    .subject-edit-page .form-select,
    .subject-edit-page .custom-select,
    .subject-edit-page select,
    .subject-edit-page input[type="text"],
    .subject-edit-page input[type="number"],
    .subject-edit-page input[type="search"] {
        width: 100%;
        height: 40px;
        min-height: 40px;
        padding: .55rem .75rem;
        border: 1px solid #cbd5e1;
        border-radius: .5rem;
        font-size: .875rem;
        line-height: 1.4;
        box-sizing: border-box;
    }

    .subject-edit-page textarea.form-control {
        height: auto;
        min-height: 96px;
    }

    .subject-edit-page .form-check-input {
        width: 16px;
        height: 16px;
        min-height: 16px;
        padding: 0;
    }

    .subject-edit-page .form-control:focus,
    .subject-edit-page .form-select:focus,
    .subject-edit-page select:focus,
    .subject-edit-page input:focus {
        border-color: #0d9488;
        box-shadow: 0 0 0 .2rem rgba(13, 148, 136, .12);
    }

    .subject-edit-card {
        overflow: hidden;
        border: 1px solid #e2e8f0 !important;
        box-shadow: 0 8px 30px rgba(15, 23, 42, 0.06);
    }

    .subject-edit-header {
        background: linear-gradient(120deg, #0f766e 0%, #155e75 100%);
    }

    .subject-edit-eyebrow {
        margin-bottom: .25rem;
        color: rgba(255, 255, 255, .72);
        font-size: .72rem;
        font-weight: 700;
        letter-spacing: .08em;
        text-transform: uppercase;
    }

    .subject-edit-subtitle {
        color: rgba(255, 255, 255, .82);
        font-size: .86rem;
    }

    .subject-section {
        padding: .25rem 0 .75rem;
    }

    .subject-section--basic {
        padding-top: .25rem;
    }

    .subject-section-heading {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 1rem;
        margin: .75rem 0 1rem;
    }

    .subject-section-title {
        margin: 0;
        color: #0f172a;
        font-size: 1.05rem;
        font-weight: 700;
    }

    .subject-section-help {
        margin: .25rem 0 0;
        color: #64748b;
        font-size: .82rem;
    }

    .subject-count-badge {
        display: inline-flex;
        align-items: center;
        min-height: 30px;
        padding: .3rem .65rem;
        border: 1px solid #bae6fd;
        border-radius: 999px;
        background: #f0f9ff;
        color: #0369a1;
        font-size: .76rem;
        font-weight: 700;
        white-space: nowrap;
    }

    .subject-option-grid {
        margin-top: .25rem;
    }

    .subject-option-card {
        height: 100%;
        padding: .85rem 1rem;
        border: 1px solid #e2e8f0;
        border-radius: .65rem;
        background: #f8fafc;
    }

    .subject-option-card .form-group,
    .subject-option-card .form-check {
        margin-bottom: 0;
    }

    .subject-section hr {
        margin: 1.35rem 0;
        border-top-color: #e2e8f0;
    }

    .field-hint {
        color: #94a3b8;
        font-size: .76rem;
        font-weight: 500;
    }

    .marks-total {
        background: #f0fdfa !important;
        border-color: #99f6e4 !important;
        color: #115e59 !important;
        font-size: 1rem !important;
        font-weight: 700;
    }

    .subject-warning {
        border-left: 4px solid #f59e0b !important;
        background: #fffbeb;
        color: #78350f;
    }

    .subject-help-panel {
        margin-bottom: 1.25rem;
        padding: 1rem 1.1rem;
        border: 1px solid #bae6fd;
        border-radius: .75rem;
        background: #f0f9ff;
        color: #334155;
    }

    .subject-help-panel-title {
        color: #075985;
        font-size: .95rem;
        font-weight: 800;
    }

    .subject-help-panel p {
        margin: .35rem 0 .75rem;
        font-size: .84rem;
    }

    .subject-help-steps {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: .5rem 1rem;
        font-size: .78rem;
        line-height: 1.45;
    }

    .subject-selection-actions {
        display: inline-flex;
        align-items: center;
        gap: .4rem;
        font-size: .78rem;
    }

    .subject-field-label {
        color: #334155;
        font-size: .8rem;
        font-weight: 600;
    }

    .subject-selection-actions .btn-link {
        color: #0f766e;
        font-weight: 700;
        text-decoration: none;
    }

    .subject-selection-actions .btn-link:hover {
        color: #115e59;
        text-decoration: underline;
    }

    .subject-class-grid {
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: .65rem;
        padding: .15rem;
    }

    .subject-class-item {
        min-height: 48px;
        padding: .7rem .8rem;
        border: 1px solid #e2e8f0;
        border-radius: .6rem;
        background: #fff;
        transition: border-color .15s ease, background-color .15s ease, box-shadow .15s ease;
    }

    .subject-class-item:hover {
        border-color: #5eead4;
        background: #f0fdfa;
        box-shadow: 0 4px 12px rgba(15, 118, 110, .08);
    }

    .subject-class-item:has(input:checked) {
        border-color: #14b8a6;
        background: #f0fdfa;
    }

    .subject-class-item__icon {
        background: #0f766e;
    }

    .subject-class-item input:not(:checked) + .subject-class-item__icon {
        border: 2px solid #cbd5e1;
        background: #fff;
    }

    .subject-class-item input:focus-visible + .subject-class-item__icon {
        outline-color: rgba(13, 148, 136, .35);
    }

    .paper-item,
    .class-config-item {
        border: 1px solid #e2e8f0 !important;
        border-left: 4px solid #0d9488 !important;
        box-shadow: none !important;
    }

    .subject-config-table-wrap {
        overflow-x: auto;
        border: 1px solid #e2e8f0;
        border-radius: 1rem;
        background: #fff;
        box-shadow: 0 4px 14px rgba(15, 23, 42, .04);
    }

    .subject-config-table {
        min-width: 920px;
        border-collapse: separate;
        border-spacing: 0;
    }

    .subject-config-table thead th {
        padding: .85rem 1rem;
        border: 0;
        border-bottom: 1px solid #e2e8f0;
        background: #f8fafc;
        color: #334155;
        font-size: .74rem;
        font-weight: 800;
        letter-spacing: .02em;
        text-transform: uppercase;
        white-space: nowrap;
    }

    .subject-config-table tbody td {
        padding: .9rem 1rem;
        border: 0;
        border-bottom: 1px solid #f1f5f9;
        color: #475569;
        font-size: .84rem;
        vertical-align: middle;
        white-space: nowrap;
    }

    .subject-config-table tbody tr:last-child td {
        border-bottom: 0;
    }

    .subject-config-table tbody tr {
        transition: background-color .15s ease;
    }

    .subject-config-table tbody tr:hover {
        background: #f8fafc;
    }

    .subject-config-table th:not(:first-child):not(:last-child),
    .subject-config-table td:not(:first-child):not(:last-child) {
        text-align: right;
    }

    .config-class-name {
        display: inline-flex;
        align-items: center;
        gap: .55rem;
        color: #0f172a;
        font-weight: 700;
    }

    .config-class-dot {
        width: .55rem;
        height: .55rem;
        flex: 0 0 .55rem;
        border-radius: 50%;
        background: #0d9488;
        box-shadow: 0 0 0 4px #ccfbf1;
    }

    .config-total-value {
        display: inline-block;
        min-width: 4.2rem;
        padding: .3rem .5rem;
        border-radius: .45rem;
        background: #f0fdfa;
        color: #0f766e;
        text-align: right;
    }

    .config-pass-value {
        color: #334155;
        font-weight: 700;
    }

    .config-remove-btn {
        width: 36px;
        min-width: 36px !important;
        height: 36px;
        min-height: 36px !important;
        padding: 0 !important;
        border-radius: .55rem;
        box-shadow: none !important;
    }

    .subject-edit-footer {
        position: sticky;
        bottom: 0;
        z-index: 5;
        box-shadow: 0 -5px 16px rgba(15, 23, 42, .06);
    }

    @media (max-width: 991.98px) {
        .subject-class-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 576px) {
        .subject-help-steps {
            grid-template-columns: 1fr;
        }

        .subject-section-heading {
            flex-direction: column;
            gap: .5rem;
        }

        .subject-class-grid {
            grid-template-columns: 1fr;
        }

        .subject-selection-actions {
            width: 100%;
            justify-content: flex-end;
        }

        .subject-edit-footer .d-flex {
            flex-direction: row !important;
        }

        .subject-edit-footer .btn {
            width: auto;
            flex: 1 1 0;
        }
    }

    html[data-theme='dark'] .subject-edit-card,
    html.dark .subject-edit-card {
        border-color: rgba(148, 163, 184, .18) !important;
        box-shadow: 0 8px 30px rgba(2, 6, 23, .22);
    }

    html[data-theme='dark'] .subject-edit-page .form-control,
    html[data-theme='dark'] .subject-edit-page .form-select,
    html[data-theme='dark'] .subject-edit-page select,
    html[data-theme='dark'] .subject-edit-page input[type="text"],
    html[data-theme='dark'] .subject-edit-page input[type="number"],
    html[data-theme='dark'] .subject-edit-page input[type="search"],
    html.dark .subject-edit-page .form-control,
    html.dark .subject-edit-page .form-select,
    html.dark .subject-edit-page select,
    html.dark .subject-edit-page input[type="text"],
    html.dark .subject-edit-page input[type="number"],
    html.dark .subject-edit-page input[type="search"] {
        border-color: rgba(148, 163, 184, .28);
        background: #0f172a;
        color: #e2e8f0;
    }

    html[data-theme='dark'] .subject-section-title,
    html.dark .subject-section-title {
        color: #f8fafc;
    }

    html[data-theme='dark'] .subject-section-help,
    html.dark .subject-section-help {
        color: #94a3b8;
    }

    html[data-theme='dark'] .subject-help-panel,
    html.dark .subject-help-panel {
        border-color: rgba(56, 189, 248, .28);
        background: rgba(14, 116, 144, .14);
        color: #cbd5e1;
    }

    html[data-theme='dark'] .subject-help-panel-title,
    html.dark .subject-help-panel-title {
        color: #7dd3fc;
    }

    html[data-theme='dark'] .subject-field-label,
    html.dark .subject-field-label {
        color: #cbd5e1;
    }

    html[data-theme='dark'] .subject-option-card,
    html.dark .subject-option-card,
    html[data-theme='dark'] .subject-class-item,
    html.dark .subject-class-item {
        border-color: rgba(148, 163, 184, .2);
        background: #0f172a;
    }

    html[data-theme='dark'] .subject-class-item:hover,
    html[data-theme='dark'] .subject-class-item:has(input:checked),
    html.dark .subject-class-item:hover,
    html.dark .subject-class-item:has(input:checked) {
        border-color: #2dd4bf;
        background: rgba(13, 148, 136, .14);
    }

    html[data-theme='dark'] .subject-section hr,
    html.dark .subject-section hr {
        border-top-color: rgba(148, 163, 184, .18);
    }

    html[data-theme='dark'] .marks-total,
    html.dark .marks-total {
        background: rgba(13, 148, 136, .16) !important;
        border-color: rgba(45, 212, 191, .38) !important;
        color: #99f6e4 !important;
    }

    html[data-theme='dark'] .subject-config-table-wrap,
    html.dark .subject-config-table-wrap {
        border-color: rgba(148, 163, 184, .2);
        background: #0f172a;
        box-shadow: none;
    }

    html[data-theme='dark'] .subject-config-table thead th,
    html.dark .subject-config-table thead th {
        border-bottom-color: rgba(148, 163, 184, .18);
        background: #111827;
        color: #cbd5e1;
    }

    html[data-theme='dark'] .subject-config-table tbody td,
    html.dark .subject-config-table tbody td {
        border-bottom-color: rgba(148, 163, 184, .12);
        color: #cbd5e1;
    }

    html[data-theme='dark'] .subject-config-table tbody tr:hover,
    html.dark .subject-config-table tbody tr:hover {
        background: rgba(30, 41, 59, .72);
    }

    html[data-theme='dark'] .config-class-name,
    html.dark .config-class-name {
        color: #f8fafc;
    }

    html[data-theme='dark'] .config-class-dot,
    html.dark .config-class-dot {
        box-shadow: 0 0 0 4px rgba(45, 212, 191, .14);
    }

    html[data-theme='dark'] .config-total-value,
    html.dark .config-total-value {
        background: rgba(13, 148, 136, .16);
        color: #99f6e4;
    }

    html[data-theme='dark'] .config-pass-value,
    html.dark .config-pass-value {
        color: #cbd5e1;
    }
</style>
@endsection

@section('scripts')
<script>
    $(function () {
        const $assignToClass = $('#assign_to_class');
        const $classAssignmentFields = $('#class_assignment_fields');
        const $classInputs = $('input[type="checkbox"][name="school_class_ids[]"]');
        const $papersSection = $('#papers_section');
        const $papersContainer = $('#papers_container');
        const $saveButton = $('#save_subject_btn');
        const translations = {
            classesSelected: @json(__(':count of :total classes selected')),
            paper: @json(__('paper')),
            papers: @json(__('papers')),
            totalPreview: @json(__('Total marks preview: :total')),
            saving: @json(__('Saving...')),
            removePaperConfirm: @json(__('Remove this existing paper? Any saved marks for it should be reviewed first.')),
        };

        function syncClassAssignmentFields() {
            const isVisible = $assignToClass.is(':checked');
            $classAssignmentFields.toggle(isVisible).attr('aria-hidden', isVisible ? 'false' : 'true');
        }

        $assignToClass.on('change', syncClassAssignmentFields);
        syncClassAssignmentFields();

        function updateClassCount() {
            const count = $classInputs.filter(':checked').length;
            const total = $classInputs.length;
            $('#selected_class_count').text(translations.classesSelected.replace(':count', count).replace(':total', total));
        }

        $classInputs.on('change', updateClassCount);
        updateClassCount();

        $('#select_all_classes').on('click', function () {
            $classInputs.prop('checked', true).trigger('change');
        });

        $('#clear_all_classes').on('click', function () {
            $classInputs.prop('checked', false).trigger('change');
        });

        const $hasMultiplePapers = $('#has_multiple_papers');

        function updatePaperCount() {
            const count = $papersContainer.children('.paper-item').length;
            $('#paper_count').text(count + ' ' + (count === 1 ? translations.paper : translations.papers));
        }

        function syncPaperSection() {
            const isVisible = $hasMultiplePapers.is(':checked');
            $papersSection.toggle(isVisible).attr('aria-hidden', isVisible ? 'false' : 'true');
            updatePaperCount();
        }

        $hasMultiplePapers.on('change', syncPaperSection);
        syncPaperSection();

        let nextPaperIndex = $papersContainer.children('.paper-item').length;
        $('#add_paper_btn').on('click', function () {
            const index = nextPaperIndex++;
            $papersContainer.append(`
                <div class="paper-item card card-body mb-2">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <strong class="paper-title">Paper ${index + 1}</strong>
                        <button type="button" class="btn btn-danger btn-sm remove-paper-btn">
                            <i class="fas fa-trash mr-1"></i>Remove
                        </button>
                    </div>
                    <div class="row">
                        <div class="col-md-4"><div class="form-group">
                            <label for="paper_${index}_name">Paper name <span class="text-danger">*</span></label>
                            <input type="text" id="paper_${index}_name" name="papers[${index}][name]" class="form-control" required placeholder="e.g., Bangla 1st Paper">
                        </div></div>
                        <div class="col-md-4"><div class="form-group">
                            <label for="paper_${index}_code">Paper code</label>
                            <input type="text" id="paper_${index}_code" name="papers[${index}][code]" class="form-control" placeholder="Optional">
                        </div></div>
                        <div class="col-md-4"><div class="form-group">
                            <label for="paper_${index}_pass">Pass mark</label>
                            <input type="number" id="paper_${index}_pass" name="papers[${index}][pass_mark]" class="form-control" min="0" step="0.01" value="0">
                        </div></div>
                        <div class="col-md-3"><div class="form-group">
                            <label for="paper_${index}_creative">Creative marks</label>
                            <input type="number" id="paper_${index}_creative" name="papers[${index}][creative_marks]" class="form-control paper-marks" min="0" step="0.01" value="0">
                        </div></div>
                        <div class="col-md-3"><div class="form-group">
                            <label for="paper_${index}_mcq">MCQ marks</label>
                            <input type="number" id="paper_${index}_mcq" name="papers[${index}][mcq_marks]" class="form-control paper-marks" min="0" step="0.01" value="0">
                        </div></div>
                        <div class="col-md-3"><div class="form-group">
                            <label for="paper_${index}_practical">Practical marks</label>
                            <input type="number" id="paper_${index}_practical" name="papers[${index}][practical_marks]" class="form-control paper-marks" min="0" step="0.01" value="0">
                        </div></div>
                        <div class="col-md-3"><div class="form-group">
                            <label for="paper_${index}_viva">Viva marks</label>
                            <input type="number" id="paper_${index}_viva" name="papers[${index}][viva_marks]" class="form-control paper-marks" min="0" step="0.01" value="0">
                        </div></div>
                        <div class="col-md-3"><div class="form-group">
                            <label for="paper_${index}_tutorial">Tutorial marks</label>
                            <input type="number" id="paper_${index}_tutorial" name="papers[${index}][tutorial_marks]" class="form-control paper-marks" min="0" step="0.01" value="0">
                        </div></div>
                    </div>
                </div>
            `);
            updatePaperCount();
        });

        $(document).on('click', '.remove-paper-btn', function () {
            const $paper = $(this).closest('.paper-item');
            const existingPaperId = $paper.find('input[name$="[id]"]').val();
            if (existingPaperId && !window.confirm(translations.removePaperConfirm)) {
                return;
            }
            $paper.remove();
            updatePaperCount();
        });

        function updateTotalMarks() {
            let total = 0;
            $('.marks-input, #tutorial_marks').each(function () {
                total += parseFloat($(this).val()) || 0;
            });
            $('#total_marks').val(total.toFixed(2));
            $('#marks_summary').text(translations.totalPreview.replace(':total', total.toFixed(2)));
        }

        $(document).on('input', '.marks-input, #tutorial_marks', updateTotalMarks);
        updateTotalMarks();

        if ($('.is-invalid').length > 0) {
            $('html, body').animate({
                scrollTop: $('.is-invalid').first().offset().top - 50
            }, 300);
        }

        // Class config management (ported from gcsc_old)
        const $classConfigsContainer = $('#class_configs_container');
        let nextClassConfigIndex = $classConfigsContainer.children('.class-config-item').length;
        $('#add_class_config_btn').on('click', function() {
            const index = nextClassConfigIndex++;
            const configHtml = `
                <div class="class-config-item card card-body mb-2">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Class *</label>
                                <select name="class_configs[${index}][class_id]" class="form-control" required>
                                    <option value="">Select Class</option>
                                    @foreach($classes as $id => $name)
                                        <option value="{{ $id }}">{{ $name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Creative Marks</label>
                                <input type="number" name="class_configs[${index}][creative_marks]" class="form-control"
                                    value="0" min="0" step="0.01" placeholder="Default: {{ $subject->creative_marks ?? 0 }}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>MCQ Marks</label>
                                <input type="number" name="class_configs[${index}][mcq_marks]" class="form-control"
                                    value="0" min="0" step="0.01" placeholder="Default: {{ $subject->mcq_marks ?? 0 }}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Practical Marks</label>
                                <input type="number" name="class_configs[${index}][practical_marks]" class="form-control"
                                    value="0" min="0" step="0.01" placeholder="Default: {{ $subject->practical_marks ?? 0 }}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Viva Marks</label>
                                <input type="number" name="class_configs[${index}][viva_marks]" class="form-control"
                                    value="0" min="0" step="0.01" placeholder="Default: {{ $subject->viva_marks ?? 0 }}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Tutorial Marks</label>
                                <input type="number" name="class_configs[${index}][tutorial_marks]" class="form-control"
                                    value="0" min="0" step="0.01" placeholder="Default: {{ $subject->tutorial_marks ?? 0 }}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Pass Mark</label>
                                <input type="number" name="class_configs[${index}][pass_mark]" class="form-control"
                                    value="0" min="0" step="0.01" placeholder="Default: {{ $subject->pass_mark ?? 0 }}">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>&nbsp;</label>
                                <button type="button" class="btn btn-danger btn-sm remove-class-config-btn">
                                    <i class="fas fa-trash"></i> Remove
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            $classConfigsContainer.append(configHtml);
        });

        $(document).on('click', '.remove-class-config-btn', function() {
            $(this).closest('.class-config-item').remove();
        });

        let formChanged = false;
        $('#modernForm').on('input change', 'input, select, textarea', function () {
            formChanged = true;
        }).on('submit', function () {
            formChanged = false;
            $saveButton.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i>' + translations.saving);
        });

        window.addEventListener('beforeunload', function (event) {
            if (formChanged) {
                event.preventDefault();
                event.returnValue = '';
            }
        });
    });
</script>
@endsection
