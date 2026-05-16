@extends('layouts.master')

@section('contents')
<div class="container-fluid">
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
        <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
    </div>
    @endif
    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show">
        <i class="fas fa-times-circle mr-2"></i>{{ session('error') }}
        <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
    </div>
    @endif

    <h4 class="mb-3 font-weight-bold">
        <i class="fas fa-cog text-primary mr-2"></i>Attendance Settings
    </h4>

    {{-- Weekend Settings --}}
    <div class="card card-outline card-primary mb-4">
        <div class="card-header"><h6 class="mb-0 font-weight-bold text-white">Weekend Days</h6></div>
        <div class="card-body">
            <form method="POST" action="{{ route('attendance.settings.weekends') }}">
                @csrf
                @php
                    $days = $weekendSetting->days();
                    $dayNames = [0=>'Sunday',1=>'Monday',2=>'Tuesday',3=>'Wednesday',4=>'Thursday',5=>'Friday',6=>'Saturday'];
                @endphp
                <div class="d-flex flex-wrap">
                    @foreach($dayNames as $num => $name)
                    <div class="form-check mr-4 mb-2">
                        <input class="form-check-input" type="checkbox" name="weekend_days[]"
                            value="{{ $num }}" id="day_{{ $num }}"
                            {{ in_array($num, $days) ? 'checked' : '' }}>
                        <label class="form-check-label" for="day_{{ $num }}">{{ $name }}</label>
                    </div>
                    @endforeach
                </div>
                <button type="submit" class="btn btn-sm btn-primary mt-2">
                    <i class="fas fa-save mr-1"></i>Save Weekend Settings
                </button>
            </form>
        </div>
    </div>

    {{-- Holiday Management --}}
    <div class="card card-outline card-warning mb-4">
        <div class="card-header"><h6 class="mb-0 font-weight-bold text-white">Holidays</h6></div>
        <div class="card-body">

            {{-- Add Holiday Form --}}
            <form method="POST" action="{{ route('attendance.settings.holidays.store') }}" id="holidayForm">
                @csrf
                <input type="hidden" name="date_start" id="date_start">
                <input type="hidden" name="date_end"   id="date_end">

                <div class="row align-items-end mb-3">
                    <div class="col-md-2 mb-2">
                        <label class="small font-weight-bold mb-1">Type</label>
                        <select id="holidayType" class="form-control form-control-sm">
                            <option value="single">Single Day</option>
                            <option value="range">Multiple Days</option>
                        </select>
                    </div>
                    <div class="col-md-3 mb-2" id="singleDateWrap">
                        <label class="small font-weight-bold mb-1">Date</label>
                        <div class="input-group input-group-sm">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                            </div>
                            <input type="text" id="singleDatePicker"
                                class="form-control form-control-sm"
                                placeholder="Select date"
                                autocomplete="off" readonly />
                        </div>
                    </div>
                    <div class="col-md-3 mb-2 d-none" id="rangeDateWrap">
                        <label class="small font-weight-bold mb-1">Date Range</label>
                        <div class="input-group input-group-sm">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                            </div>
                            <input type="text" id="rangeDatePicker"
                                class="form-control form-control-sm"
                                placeholder="Start date → End date"
                                autocomplete="off" readonly />
                        </div>
                        <small id="dateRangePreview" class="text-muted"></small>
                    </div>
                    <div class="col-md-3 mb-2">
                        <label class="small font-weight-bold mb-1">Title <span class="text-muted">(optional)</span></label>
                        <input type="text" name="title" class="form-control form-control-sm"
                            placeholder="e.g. Eid ul-Fitr" maxlength="255" />
                    </div>
                    <div class="col-md-2 mb-2">
                        <label class="small font-weight-bold mb-1">Description <span class="text-muted">(optional)</span></label>
                        <input type="text" name="description" class="form-control form-control-sm"
                            placeholder="Optional note" />
                    </div>
                    <div class="col-md-2 mb-2">
                        <button type="submit" class="btn btn-sm btn-warning btn-block">
                            <i class="fas fa-plus mr-1"></i>Add Holiday
                        </button>
                    </div>
                </div>
            </form>

            {{-- Holiday List --}}
            @if($holidays->isEmpty())
            <p class="text-muted mb-0">No holidays configured.</p>
            @else
            <div class="table-responsive">
                <table class="table table-sm table-hover mb-0">
                    <thead class="thead-light">
                        <tr>
                            <th>Date</th>
                            <th>Day</th>
                            <th>Title</th>
                            <th>Description</th>
                            <th style="width:60px;"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($holidays as $holiday)
                        <tr>
                            <td>{{ $holiday->date->format('d M Y') }}</td>
                            <td><span class="badge badge-secondary">{{ $holiday->date->format('D') }}</span></td>
                            <td>{{ $holiday->title ?? '-' }}</td>
                            <td>{{ $holiday->description ?? '-' }}</td>
                            <td>
                                <form method="POST" action="{{ route('attendance.settings.holidays.destroy', $holiday) }}"
                                    onsubmit="return confirm('Delete this holiday?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-xs btn-danger">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(function () {

    // --- Single date picker ---
    $('#singleDatePicker').daterangepicker({
        singleDatePicker: true,
        autoUpdateInput: false,
        showDropdowns: true,
        locale: { format: 'DD MMM YYYY' },
        opens: 'left',
    });

    $('#singleDatePicker').on('apply.daterangepicker', function (ev, picker) {
        var d = picker.startDate;
        $(this).val(d.format('DD MMM YYYY'));
        $('#date_start').val(d.format('YYYY-MM-DD'));
        $('#date_end').val(d.format('YYYY-MM-DD'));
    });

    // --- Range date picker ---
    $('#rangeDatePicker').daterangepicker({
        singleDatePicker: false,
        autoUpdateInput: false,
        showDropdowns: true,
        linkedCalendars: false,
        locale: { format: 'DD MMM YYYY', applyLabel: 'Apply', cancelLabel: 'Cancel' },
        opens: 'left',
    });

    $('#rangeDatePicker').on('apply.daterangepicker', function (ev, picker) {
        var s = picker.startDate, e = picker.endDate;
        $(this).val(s.format('DD MMM YYYY') + '  →  ' + e.format('DD MMM YYYY'));
        $('#date_start').val(s.format('YYYY-MM-DD'));
        $('#date_end').val(e.format('YYYY-MM-DD'));
        var days = e.diff(s, 'days') + 1;
        $('#dateRangePreview').text(days + ' day' + (days > 1 ? 's' : '') + ' selected');
    });

    // --- Type dropdown toggle ---
    $('#holidayType').on('change', function () {
        var isSingle = $(this).val() === 'single';
        $('#singleDateWrap').toggleClass('d-none', !isSingle);
        $('#rangeDateWrap').toggleClass('d-none', isSingle);
        // Clear hidden inputs on switch
        $('#date_start, #date_end').val('');
        $('#singleDatePicker, #rangeDatePicker').val('');
        $('#dateRangePreview').text('');
    });

    // --- Validate before submit ---
    $('#holidayForm').on('submit', function (e) {
        if (!$('#date_start').val()) {
            e.preventDefault();
            var isSingle = $('#holidayType').val() === 'single';
            alert('Please select a ' + (isSingle ? 'date' : 'date range') + ' first.');
            (isSingle ? $('#singleDatePicker') : $('#rangeDatePicker')).focus();
        }
    });

});
</script>
@endsection
