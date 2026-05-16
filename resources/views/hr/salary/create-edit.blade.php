@extends('layouts.master')
@section('contents')
<div class="container-fluid">
    @php $s = $salaryStructure ?? null; @endphp
    <div class="card">
        <div class="card-header bg-gradient-primary text-white py-3">
            <div class="d-flex justify-content-between align-items-center">
                <h4 class="card-title mb-0 font-weight-bold text-white">
                    <i class="fas fa-money-check-alt mr-2"></i>{{ $s ? 'Edit' : 'Add' }} Salary Structure — {{ $employee->name }}
                </h4>
                <a href="{{ route('hr.salary-structures.index') }}" class="btn btn-light btn-sm">
                    <i class="fas fa-arrow-left mr-1"></i> Back
                </a>
            </div>
        </div>
        <div class="card-body">
            @include('hr._alerts')

            @if(!$s)
            <div class="mb-3">
                <button type="button" class="btn btn-outline-info btn-sm" onclick="loadDefaults()">
                    <i class="fas fa-magic"></i> Load Designation Defaults
                </button>
            </div>
            @endif

            <form action="{{ $s ? route('hr.salary-structures.update', $s) : route('hr.salary-structures.store') }}" method="POST">
                @csrf
                @if($s) @method('PUT') @endif
                <input type="hidden" name="employee_id" value="{{ $employee->id }}">
                <input type="hidden" name="designation_id" value="{{ $employee->designation_id }}">

                <div class="row">
                    <div class="col-md-12"><h5 class="text-muted border-bottom pb-2 mb-3">Earnings</h5></div>
                    @foreach([
                        'basic_salary'        => 'Basic Salary *',
                        'house_rent'          => 'House Rent',
                        'medical_allowance'   => 'Medical Allowance',
                        'transport_allowance' => 'Transport Allowance',
                        'special_allowance'   => 'Special Allowance',
                        'bonus'               => 'Bonus',
                    ] as $field => $label)
                    <div class="col-md-2 form-group">
                        <label>{{ $label }}</label>
                        <input type="number" name="{{ $field }}" id="{{ $field }}" step="0.01" min="0"
                            class="form-control form-control-sm salary-input @error($field) is-invalid @enderror"
                            value="{{ old($field, $s?->$field ?? 0) }}"
                            {{ $field === 'basic_salary' ? 'required' : '' }}>
                        @error($field)<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    @endforeach

                    <div class="col-md-12"><h5 class="text-muted border-bottom pb-2 mb-3 mt-2">Deductions</h5></div>
                    <div class="col-md-2 form-group">
                        <label>Other Deductions</label>
                        <input type="number" name="other_deductions" id="other_deductions" step="0.01" min="0"
                            class="form-control form-control-sm salary-input @error('other_deductions') is-invalid @enderror"
                            value="{{ old('other_deductions', $s?->other_deductions ?? 0) }}">
                        @error('other_deductions')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-3 form-group">
                        <label class="font-weight-bold">Effective From <span class="text-danger">*</span></label>
                        <input type="date" name="effective_from" class="form-control form-control-sm @error('effective_from') is-invalid @enderror"
                            value="{{ old('effective_from', $s?->effective_from?->format('Y-m-d') ?? today()->format('Y-m-d')) }}" required>
                        @error('effective_from')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

                {{-- Live Preview --}}
                <div class="card bg-light mt-3">
                    <div class="card-body py-2">
                        <div class="row text-center">
                            <div class="col-md-4">
                                <small class="text-muted text-uppercase font-weight-bold">Gross Salary</small>
                                <div class="h4 font-weight-bold text-primary" id="previewGross">৳0.00</div>
                            </div>
                            <div class="col-md-4">
                                <small class="text-muted text-uppercase font-weight-bold">Deductions</small>
                                <div class="h4 font-weight-bold text-danger" id="previewDeductions">৳0.00</div>
                            </div>
                            <div class="col-md-4">
                                <small class="text-muted text-uppercase font-weight-bold">Net Salary</small>
                                <div class="h4 font-weight-bold text-success" id="previewNet">৳0.00</div>
                            </div>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary mt-3"><i class="fas fa-save"></i> {{ $s ? 'Update' : 'Save' }}</button>
            </form>
        </div>
    </div>
</div>

<script>
const earningFields = ['basic_salary','house_rent','medical_allowance','transport_allowance','special_allowance','bonus'];
const fmt = n => '৳' + n.toLocaleString('en-BD', {minimumFractionDigits:2, maximumFractionDigits:2});

function calcSalary() {
    const gross = earningFields.reduce((sum, f) => sum + (parseFloat(document.getElementById(f)?.value) || 0), 0);
    const ded   = parseFloat(document.getElementById('other_deductions')?.value) || 0;
    const net   = Math.max(0, gross - ded);
    document.getElementById('previewGross').textContent      = fmt(gross);
    document.getElementById('previewDeductions').textContent = fmt(ded);
    document.getElementById('previewNet').textContent        = fmt(net);
}

document.querySelectorAll('.salary-input').forEach(el => el.addEventListener('input', calcSalary));
calcSalary();

function loadDefaults() {
    const desigId = {{ $employee->designation_id }};
    fetch(`/hr/salary/load-defaults/${desigId}`)
        .then(r => r.json())
        .then(d => {
            if (!d) return alert('No defaults set for this designation.');
            earningFields.forEach(f => { if (d[f] !== undefined) document.getElementById(f).value = d[f]; });
            document.getElementById('other_deductions').value = d.other_deductions ?? 0;
            calcSalary();
        });
}
</script>
@endsection
