@extends('layouts.master')
@section('contents')
<div class="container-fluid">
    <div class="card">
        <div class="card-header bg-gradient-primary text-white py-3">
            <div class="d-flex justify-content-between align-items-center">
                <h4 class="card-title mb-0 font-weight-bold text-white">
                    <i class="fas fa-sliders-h mr-2"></i>Designation Salary Defaults
                </h4>
            </div>
        </div>
        <div class="card-body">
            @include('hr._alerts')
            @foreach($designations as $d)
            @php $def = $d->salaryDefault; @endphp
            <div class="card mb-3 border">
                <div class="card-header bg-gradient-primary text-white py-2">
                    <div class="d-flex justify-content-between align-items-center">
                        <strong>{{ $d->name }}</strong>
                        <span class="badge badge-light ml-auto">{{ ucfirst($d->employee_type) }} — Level {{ $d->hierarchy_level }}</span>
                    </div>
                </div>
                <div class="card-body py-2">
                    <form action="{{ route('hr.salary.defaults.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="designation_id" value="{{ $d->id }}">
                        <div class="row">
                            @foreach(['basic_salary'=>'Basic','house_rent'=>'House Rent','medical_allowance'=>'Medical','transport_allowance'=>'Transport','special_allowance'=>'Special','bonus'=>'Bonus','other_deductions'=>'Deductions'] as $field => $label)
                            <div class="col-md-1 form-group mb-1">
                                <label class="small">{{ $label }}</label>
                                <input type="number" name="{{ $field }}" step="0.01" min="0" class="form-control form-control-sm" value="{{ $def?->$field ?? 0 }}">
                            </div>
                            @endforeach
                            <div class="col-md-2 d-flex align-items-center form-group mb-1">
                                <button type="submit" class="btn btn-primary btn-sm w-100"><i class="fas fa-save"></i> Save</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
