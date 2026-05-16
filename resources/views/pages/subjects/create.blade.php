@extends('layouts.master')

@section('contents')
<div class="container-fluid px-3 py-3">
    <div class="card shadow-sm border-0">
        <div class="card-header bg-gradient-primary text-white py-3">
            <div class="d-flex justify-content-between align-items-center">
                <h4 class="card-title mb-0 font-weight-bold text-white">
                    <i class="fas fa-plus-circle mr-2"></i>Create Subject
                </h4>
                <a href="{{ route('subjects.index') }}" class="btn btn-light btn-sm">
                    <i class="fas fa-arrow-left mr-1"></i> Back
                </a>
            </div>
        </div>

        <form method="POST" action="{{ route('subjects.store') }}" id="modernForm">
            @csrf

            <div class="card-body p-3">
                @if($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show border-0 mb-3" role="alert">
                        <i class="fas fa-exclamation-circle mr-2"></i>
                        <strong>Errors:</strong>
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

<div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name">Subject Name <span class="text-danger">*</span></label>
                                    <input type="text" name="name" id="name" class="form-control" 
                                        value="{{ old('name') }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="code">Subject Code</label>
                                    <input type="text" name="code" id="code" class="form-control" 
                                        value="{{ old('code') }}" placeholder="Optional unique code">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="type">Type <span class="text-danger">*</span></label>
                                    <select name="type" id="type" class="form-control" required>
                                        <option value="mandatory" {{ old('type') == 'mandatory' ? 'selected' : '' }}>Mandatory</option>
                                        <option value="optional" {{ old('type') == 'optional' ? 'selected' : '' }}>Optional</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="is_active">Status</label>
                                    <select name="is_active" id="is_active" class="form-control">
                                        <option value="1" {{ old('is_active', '1') == '1' ? 'selected' : '' }}>Active</option>
                                        <option value="0" {{ old('is_active') == '0' ? 'selected' : '' }}>Inactive</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <div class="form-check">
                                        <input type="checkbox" name="has_multiple_papers" id="has_multiple_papers" 
                                            class="form-check-input" value="1" {{ old('has_multiple_papers') ? 'checked' : '' }}>
                                        <label class="form-check-label" for="has_multiple_papers">
                                            Has Multiple Papers (Combined Subject)
                                        </label>
                                        <small class="d-block text-muted">Check if this subject consists of multiple papers (e.g., Bangla 1st Paper, 2nd Paper)</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <div class="form-check">
                                        <input type="checkbox" name="combine_papers_for_result" id="combine_papers_for_result" 
                                            class="form-check-input" value="1" {{ old('combine_papers_for_result', '1') == '1' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="combine_papers_for_result">
                                            Combine Papers for Result
                                        </label>
                                        <small class="d-block text-muted">If checked, total marks = sum of all papers' marks</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Papers Management Section -->
                        <div id="papers_section" style="display: none;">
                            <hr>
                            <h5>Papers Configuration</h5>
                            <p class="text-muted">Define individual papers for this combined subject</p>
                            
                            <div id="papers_container">
                                <!-- Dynamic papers will be added here -->
                            </div>
                            
                            <button type="button" id="add_paper_btn" class="btn btn-sm btn-success">
                                <i class="fas fa-plus"></i> Add Paper
                            </button>
                        </div>

                        <hr>
                        <h5>Marks Distribution</h5>
                        <p class="text-muted">Define marks for this subject. If class-wise configuration is needed, use the Class Configs section below.</p>

                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="creative_marks">Creative Marks (CQ)</label>
                                    <input type="number" name="creative_marks" id="creative_marks" class="form-control marks-input" 
                                        value="{{ old('creative_marks', 0) }}" min="0" step="0.01">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="mcq_marks">MCQ Marks</label>
                                    <input type="number" name="mcq_marks" id="mcq_marks" class="form-control marks-input" 
                                        value="{{ old('mcq_marks', 0) }}" min="0" step="0.01">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="practical_marks">Practical Marks</label>
                                    <input type="number" name="practical_marks" id="practical_marks" class="form-control marks-input" 
                                        value="{{ old('practical_marks', 0) }}" min="0" step="0.01">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="viva_marks">Viva Marks</label>
                                    <input type="number" name="viva_marks" id="viva_marks" class="form-control marks-input" 
                                        value="{{ old('viva_marks', 0) }}" min="0" step="0.01">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="pass_mark">Pass Mark</label>
                                    <input type="number" name="pass_mark" id="pass_mark" class="form-control" 
                                        value="{{ old('pass_mark', 0) }}" min="0" step="0.01">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Total Marks (Auto Calculated)</label>
                                    <input type="text" class="form-control" id="total_marks" readonly 
                                        value="0.00" disabled>
                                </div>
                            </div>
                        </div>

                        <!-- Class-wise Marks Configuration -->
                        <hr>
                        <h5>Class-wise Marks Configuration (Optional)</h5>
                        <p class="text-muted">Override default marks for specific classes. Leave empty to use subject defaults.</p>
                        
                        <div id="class_configs_container">
                            <!-- Dynamic class configs will be added here -->
                        </div>
                        
                        <button type="button" id="add_class_config_btn" class="btn btn-sm btn-info">
                            <i class="fas fa-plus"></i> Add Class Configuration
                        </button>

                        <hr>
                        <h5>Assign to Classes (Optional)</h5>
                        <p class="text-muted">Select multiple classes to assign this subject. Compulsory subjects will be auto-assigned to all students in the class.</p>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-check mb-2">
                                    <input type="checkbox" name="assign_to_class" id="assign_to_class" 
                                        class="form-check-input" value="1">
                                    <label class="form-check-label" for="assign_to_class">
                                        Assign this subject to classes
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div id="class_assignment_fields" style="display: none;">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="school_class_ids">Select Classes</label>
                                        <select name="school_class_ids[]" id="school_class_ids" class="form-control" multiple>
                                            @foreach($classes as $id => $name)
                                                <option value="{{ $id }}">{{ $name }}</option>
                                            @endforeach
                                        </select>
                                        <small class="text-muted">Hold Ctrl/Cmd to select multiple</small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="group_id">Filter by Group (Optional)</label>
                                        <select name="group_id" id="group_id" class="form-control">
                                            <option value="">All Groups</option>
                                            @foreach($groups as $id => $name)
                                                <option value="{{ $id }}">{{ $name }}</option>
                                            @endforeach
                                        </select>
                                        <small class="text-muted">Applies selected group filter to all selected classes</small>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="gender">Gender Filter</label>
                                        <select name="gender" id="gender" class="form-control">
                                            <option value="all">All Students</option>
                                            <option value="male">Male Only</option>
                                            <option value="female">Female Only</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="religion">Religion Filter</label>
                                        <select name="religion" id="religion" class="form-control">
                                            <option value="all">All Religions</option>
                                            <option value="islam">Islam</option>
                                            <option value="hindu">Hindu</option>
                                            <option value="christian">Christian</option>
                                            <option value="buddhist">Buddhist</option>
                                            <option value="other">Other</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>&nbsp;</label>
                                        <div class="form-check mt-2">
                                            <input type="checkbox" name="is_optional" id="is_optional" 
                                                class="form-check-input" value="1">
                                            <label class="form-check-label" for="is_optional">
                                                Optional Subject
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="exclusive_group_key">Exclusive Group Key (Optional)</label>
                                        <input type="text" name="exclusive_group_key" id="exclusive_group_key" class="form-control"
                                            value="{{ old('exclusive_group_key') }}" placeholder="e.g., science_core_choice">
                                        <small class="text-muted">Used for mutually exclusive subject groups (e.g., Biology vs Higher Math)</small>
                                    </div>
                                </div>
                            </div>
                        </div>
            </div>

            <div class="card-footer bg-light border-top py-2 px-3">
                <div class="d-flex justify-content-between gap-2">
                    <a href="{{ route('subjects.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-times mr-1"></i>Cancel
                    </a>
                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="fas fa-save mr-1"></i>Create
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@section('styles')
@include('components.form-styles')
@endsection

@section('scripts')
<script>
    $(function () {
        if ($('.is-invalid').length > 0) {
            $('html, body').animate({
                scrollTop: $('.is-invalid').first().offset().top - 50
            }, 300);
        }
    });
</script>
@endsection