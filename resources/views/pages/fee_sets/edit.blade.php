@extends('layouts.master')

@section('contents')
<div class="container-fluid">
    <div class="row justify-content-center">

        {{-- EDIT FEE SET --}}
        <div class="col-md-5">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white p-3">
                    <h3 class="card-title mb-0">Edit Fee Set</h3>
                </div>

                <form method="POST" action="{{ route('fee-sets.update', $feeSet->id) }}">
                    @csrf
                    @method('PUT')

                    <div class="card-body">

                        {{-- Fee Set Info --}}
                        <div class="form-group">
                            <label>Name (English) <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" value="{{ $feeSet->name }}" required>
                        </div>

                        <div class="form-group">
                            <label>Name (Bangla)</label>
                            <input type="text" name="bn_name" class="form-control" value="{{ $feeSet->bn_name }}">
                        </div>

                        <div class="form-group">
                            <label>Academic Session <span class="text-danger">*</span></label>
                            <select name="academic_session_id" class="form-control" required>
                                <option value="">Select Session</option>
                                @foreach ($sessions as $session)
                                    <option value="{{ $session->id }}" {{ $feeSet->academic_session_id == $session->id ? 'selected' : '' }}>
                                        {{ $session->name_en }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Class</label>
                            <select name="school_class_id" id="schoolClass" class="form-control">
                                <option value="">Select Class</option>
                                @foreach ($classes as $class)
                                    <option value="{{ $class->id }}" {{ $feeSet->school_class_id == $class->id ? 'selected' : '' }}>
                                        {{ $class->name_en }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Group</label>
                            <select name="group_id" id="groupSelect" class="form-control" disabled>
                                <option value="">Select Group</option>
                                @foreach ($groups as $group)
                                    <option value="{{ $group->id }}" {{ $feeSet->group_id == $group->id ? 'selected' : '' }}>
                                        {{ $group->name_en }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Frequency <span class="text-danger">*</span></label>
                            <select name="frequency" id="frequencySelect" class="form-control" required>
                                <option value="monthly" {{ $feeSet->frequency == 'monthly' ? 'selected' : '' }}>Monthly</option>
                                <option value="yearly"  {{ $feeSet->frequency == 'yearly'  ? 'selected' : '' }}>Yearly</option>
                                <option value="others"  {{ $feeSet->frequency == 'others'  ? 'selected' : '' }}>Others</option>
                            </select>
                        </div>

                        <div class="form-group" id="monthSelector" style="display:none;">
                            <label>Select Months</label>
                            @php
                                $months = [
                                    1=>'January',  2=>'February', 3=>'March',    4=>'April',
                                    5=>'May',      6=>'June',     7=>'July',      8=>'August',
                                    9=>'September',10=>'October', 11=>'November', 12=>'December'
                                ];
                            @endphp
                            <select name="month" class="form-control" >
                                @foreach($months as $num => $name)
                                    <option value="{{ $num }}" {{ ($num == $feeSet->month) ? 'selected' : '' }}>
                                        {{ $name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group" id="yearSelector" style="display:none;">
                            <label>Select Year</label>
                            <select name="year" class="form-control">
                                @php $currentYear = now()->year; @endphp
                                @for($y = $currentYear - 50; $y <= $currentYear + 100; $y++)
                                    <option value="{{ $y }}" {{ ($feeSet->year ?? $currentYear) == $y ? 'selected' : '' }}>
                                        {{ $y }}
                                    </option>
                                @endfor
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Description</label>
                            <textarea name="description" class="form-control" rows="2">{{ $feeSet->description }}</textarea>
                        </div>

                        <hr>

                        {{-- FEE SET ITEMS --}}
                        <h5 class="mb-3">Fee Categories & Amounts</h5>

                        <table class="table table-bordered" id="feeItemsTable">
                            <thead class="thead-light">
                                <tr>
                                    <th>Fee Category</th>
                                    <th width="30%">Amount</th>
                                    <th width="10%">Action</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach ($feeSet->items as $item)
                                <tr>
                                    <td>
                                        <select name="fee_category_id[]" class="form-control" required>
                                            <option value="">Select Category</option>
                                            @foreach ($feeCategories as $category)
                                                <option value="{{ $category->id }}" {{ $item->fee_category_id == $category->id ? 'selected' : '' }}>
                                                    {{ $category->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>

                                    <td>
                                        <input type="number" step="0.01" name="amount[]" class="form-control"
                                               value="{{ $item->amount }}" required>
                                    </td>

                                    <td class="text-center">
                                        <button type="button" class="btn btn-sm btn-danger removeRow">-</button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>

                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">Update Fee Set</button>
                        <a href="{{ route('fee-sets.index') }}" class="btn btn-secondary">Back</a>
                    </div>
                </form>
            </div>
        </div>

        {{-- FEE SET TABLE --}}
        <div class="col-md-7">
            @include('pages.fee_sets.table')
        </div>

    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {

        function applyFrequencyUI(frequency, clearMonths) {
            if (clearMonths) {
                $('#monthSelector select').val('');
                $('#yearSelector select').val('');
            }

            if (frequency === 'monthly') {
                $('#monthSelector').hide();
                $('#yearSelector').hide();
            } 
            else if (frequency === 'yearly') {
                $('#monthSelector').hide();
                $('#yearSelector').show();
            } 
            else {
                // others
                $('#monthSelector').show();
                $('#yearSelector').show();
            }
        }

        $('#frequencySelect').change(function() {
            applyFrequencyUI($(this).val(), true);
        });

        applyFrequencyUI($('#frequencySelect').val(), false);

        $(document).on('click', '.addRow', function() {
            let row = `
                <tr>
                    <td>
                        <select name="fee_category_id[]" class="form-control" required>
                            <option value="">Select Category</option>
                            @foreach ($feeCategories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </td>

                    <td>
                        <input type="number" step="0.01" name="amount[]" class="form-control" required>
                    </td>

                    <td class="text-center">
                        <button type="button" class="btn btn-sm btn-danger removeRow">-</button>
                    </td>
                </tr>
            `;
            $('#feeItemsTable tbody').append(row);
        });

        // Remove Row
        $(document).on('click', '.removeRow', function() {
            $(this).closest('tr').remove();
        });

        // Fetch Groups based on Class
        $('#schoolClass').change(function() {
            let classId = $(this).val();
            let $groupSelect = $('#groupSelect');

            $groupSelect.prop('disabled', true).html('<option>Loading...</option>');

            if (classId) {
                $.ajax({
                    url: "{{ route('load_groups') }}",
                    type: 'GET',
                    data: { school_class_id: classId },
                    success: function(data) {
                        let options = '<option value="">Select Group</option>';

                        if (data.groups && data.groups.length > 0) {
                            $.each(data.groups, function(index, group) {
                                options += `<option value="${group.id}">${group.name_en}</option>`;
                            });
                        }

                        $groupSelect.html(options).prop('disabled', false);
                    },
                    error: function() {
                        $groupSelect.html('<option value="">Failed to load groups</option>');
                    }
                });
            } else {
                $groupSelect.html('<option value="">Select Group</option>').prop('disabled', true);
            }
        });

        // Enable group select if class is already selected on page load
        if ($('#schoolClass').val()) {
            $('#groupSelect').prop('disabled', false);
        }

    });
</script>
@endsection