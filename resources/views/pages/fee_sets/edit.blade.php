@extends('layouts.master')

@section('contents')
<div class="container-fluid px-3 py-3">
    <div class="row">
        <!-- Edit Form on Left -->
        <div class="col-md-8">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-gradient-primary text-white py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0 font-weight-bold text-white">
                            <i class="fas fa-edit mr-2"></i>Edit Fee Set
                        </h4>
                        <a href="{{ route('fee-sets.index') }}" class="btn btn-light btn-sm">
                            <i class="fas fa-arrow-left mr-1"></i> Back
                        </a>
                    </div>
                </div>

                <form method="POST" action="{{ route('fee-sets.update', $feeSet->id) }}" id="modernForm">
                    @csrf
                    @method('PUT')

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

                        {{-- Fee Set Info --}}
                        <div class="form-group">
                            <label class="form-label small font-weight-600 mb-1">Name (English) <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control form-control-sm @error('name') is-invalid @enderror" value="{{ old('name', $feeSet->name) }}" required>
                            @error('name')<small class="text-danger">{{ $message }}</small>@enderror
                        </div>

                        <div class="form-group">
                            <label class="form-label small font-weight-600 mb-1">Name (Bangla)</label>
                            <input type="text" name="bn_name" class="form-control form-control-sm @error('bn_name') is-invalid @enderror" value="{{ old('bn_name', $feeSet->bn_name) }}">
                            @error('bn_name')<small class="text-danger">{{ $message }}</small>@enderror
                        </div>

                        <div class="form-group">
                            <label class="form-label small font-weight-600 mb-1">Academic Session <span class="text-danger">*</span></label>
                            <select name="academic_session_id" class="form-control form-control-sm @error('academic_session_id') is-invalid @enderror" required>
                                <option value="">Select Session</option>
                                @foreach ($sessions as $session)
                                    <option value="{{ $session->id }}" {{ old('academic_session_id', $feeSet->academic_session_id) == $session->id ? 'selected' : '' }}>
                                        {{ $session->name_en }}
                                    </option>
                                @endforeach
                            </select>
                            @error('academic_session_id')<small class="text-danger">{{ $message }}</small>@enderror
                        </div>

                        <div class="row g-2 mb-3">
                            <div class="col-md-6">
                                <label class="form-label small font-weight-600 mb-1">Class</label>
                                <select name="school_class_id" id="schoolClass" class="form-control form-control-sm @error('school_class_id') is-invalid @enderror">
                                    <option value="">Select Class</option>
                                    @foreach ($classes as $class)
                                        <option value="{{ $class->id }}" {{ old('school_class_id', $feeSet->school_class_id) == $class->id ? 'selected' : '' }}>
                                            {{ $class->name_en }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('school_class_id')<small class="text-danger">{{ $message }}</small>@enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small font-weight-600 mb-1">Group</label>
                                <select name="group_id" id="groupSelect" class="form-control form-control-sm @error('group_id') is-invalid @enderror" disabled>
                                    <option value="">Select Group</option>
                                    @foreach ($groups as $group)
                                        <option value="{{ $group->id }}" {{ old('group_id', $feeSet->group_id) == $group->id ? 'selected' : '' }}>
                                            {{ $group->name_en }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('group_id')<small class="text-danger">{{ $message }}</small>@enderror
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label small font-weight-600 mb-1">Frequency <span class="text-danger">*</span></label>
                            <select name="frequency" id="frequencySelect" class="form-control form-control-sm @error('frequency') is-invalid @enderror" required>
                                <option value="monthly" {{ old('frequency', $feeSet->frequency) == 'monthly' ? 'selected' : '' }}>Monthly (Every Month)</option>
                                <option value="yearly" {{ old('frequency', $feeSet->frequency) == 'yearly' ? 'selected' : '' }}>Yearly (Once a Year)</option>
                                <option value="others" {{ old('frequency', $feeSet->frequency) == 'others' ? 'selected' : '' }}>Others (Specific Month)</option>
                            </select>
                            @error('frequency')<small class="text-danger">{{ $message }}</small>@enderror
                        </div>

                        <div class="form-group" id="yearlyDueDateSelector" style="display:none;">
                            <label class="form-label small font-weight-600 mb-1">Yearly Due Date <span class="text-danger">*</span></label>
                            <input type="date" name="due_date" class="form-control form-control-sm @error('due_date') is-invalid @enderror" value="{{ old('due_date', optional($feeSet->due_date)->format('Y-m-d')) }}">
                            @error('due_date')<small class="text-danger">{{ $message }}</small>@enderror
                        </div>

                        <div class="form-group" id="monthSelector" style="display:none;">
                            <label class="form-label small font-weight-600 mb-1">Select Month <span class="text-danger">*</span></label>
                            @php
                                $months = [
                                    1=>'January',  2=>'February', 3=>'March',    4=>'April',
                                    5=>'May',      6=>'June',     7=>'July',      8=>'August',
                                    9=>'September',10=>'October', 11=>'November', 12=>'December'
                                ];
                            @endphp
                            <select name="month" class="form-control form-control-sm">
                                <option value="">Select Month</option>
                                @foreach($months as $num => $name)
                                    <option value="{{ $num }}" {{ old('month', $feeSet->month) == $num ? 'selected' : '' }}>
                                        {{ $name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="form-label small font-weight-600 mb-1">Description</label>
                            <textarea name="description" class="form-control form-control-sm @error('description') is-invalid @enderror" rows="2" placeholder="Enter description...">{{ old('description', $feeSet->description) }}</textarea>
                            @error('description')<small class="text-danger">{{ $message }}</small>@enderror
                        </div>

                        <hr class="my-3">

                        {{-- FEE SET ITEMS --}}
                        <h5 class="mb-3 small font-weight-bold">
                            <i class="fas fa-list mr-2"></i>Fee Categories & Amounts
                        </h5>

                        <div class="table-responsive">
                            <table class="table table-sm table-bordered mb-0" id="feeItemsTable">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="small">Fee Category</th>
                                        <th class="small text-center" style="width: 30%;">Amount</th>
                                        <th class="small text-center" style="width: 10%;">Action</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @foreach ($feeSet->items as $item)
                                    <tr>
                                        <td>
                                            <select name="fee_category_id[]" class="form-control form-control-sm" required>
                                                <option value="">Select Category</option>
                                                @foreach ($feeCategories as $category)
                                                    <option value="{{ $category->id }}" {{ $item->fee_category_id == $category->id ? 'selected' : '' }}>
                                                        {{ $category->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </td>

                                        <td>
                                            <input type="number" step="0.01" name="amount[]" class="form-control form-control-sm" value="{{ $item->amount }}" required>
                                        </td>

                                        <td class="text-center">
                                            <button type="button" class="btn btn-sm btn-danger removeRow">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="card-footer bg-light border-top py-2 px-3">
                        <div class="d-flex justify-content-between gap-2">
                            <a href="{{ route('fee-sets.index') }}" class="btn btn-secondary btn-sm">
                                <i class="fas fa-times mr-1"></i>Cancel
                            </a>
                            <button type="submit" class="btn btn-primary btn-sm">
                                <i class="fas fa-save mr-1"></i>Update Fee Set
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Fee Sets List on Right -->
        <div class="col-md-4 mb-3">
            @include('pages.fee_sets.table')
        </div>
    </div>
</div>
@endsection

@section('styles')
@include('components.form-styles')
<style>
    .row.g-2 {
        margin-right: -0.5rem;
        margin-left: -0.5rem;
    }

    .row.g-2 > [class*="col-"] {
        padding-right: 0.5rem;
        padding-left: 0.5rem;
    }
</style>
@endsection

@section('scripts')
<script>
    $(function () {
        // Frequency selector - Show/hide month field
        $('#frequencySelect').on('change', function() {
            const freq = $(this).val();
            
            // Show month selector only for 'others' frequency
            $('#monthSelector').toggle(freq === 'others');
            $('#yearlyDueDateSelector').toggle(freq === 'yearly');
        }).trigger('change');

        // Add row functionality
        $(document).on('click', '.addRow', function() {
            const newRow = `
                <tr>
                    <td>
                        <select name="fee_category_id[]" class="form-control form-control-sm" required>
                            <option value="">Select Category</option>
                            @foreach ($feeCategories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </td>
                    <td>
                        <input type="number" step="0.01" name="amount[]" class="form-control form-control-sm" required>
                    </td>
                    <td class="text-center">
                        <button type="button" class="btn btn-sm btn-danger removeRow">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>
            `;
            $('#feeItemsTable tbody').append(newRow);
        });

        // Remove row functionality
        $(document).on('click', '.removeRow', function() {
            if ($('#feeItemsTable tbody tr').length > 1) {
                $(this).closest('tr').remove();
            } else {
                alert('At least one fee category is required');
            }
        });

        // Auto-scroll to first error
        if ($('.is-invalid').length > 0) {
            $('html, body').animate({
                scrollTop: $('.is-invalid').first().offset().top - 50
            }, 300);
        }
    });
</script>
@endsection
