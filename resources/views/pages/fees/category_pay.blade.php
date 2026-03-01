@extends('layouts.master')

@section('contents')
<div class="container-fluid">

<div class="card mb-3">
<div class="card-body">

<div class="row">

<div class="col-md-3">
    <label>Student ID</label>
    <input type="text" id="student_id" class="form-control">
</div>

<div class="col-md-3">
    <label>Frequency</label>
    <select id="frequency" class="form-control">
        <option value="monthly">Monthly</option>
        <option value="quarterly">Quarterly</option>
        <option value="yearly">Yearly</option>
    </select>
</div>

<div class="col-md-3">
    <label>Month</label>
    <input type="month" id="month" class="form-control">
</div>

<div class="col-md-3">
    <label>&nbsp;</label><br>
    <button id="loadFees" class="btn btn-primary">
        Load Fees
    </button>
</div>

</div>

</div>
</div>

<div class="card">
<div class="card-body p-0">

<table class="table table-bordered mb-0">
<thead>
<tr>
    <th>Category</th>
    <th>Amount</th>
    <th>Status</th>
    <th width="120">Action</th>
</tr>
</thead>

<tbody id="feeRows">
    <tr>
        <td colspan="4" class="text-center text-muted">
            Search student & month
        </td>
    </tr>
</tbody>

</table>

</div>
</div>

</div>
@endsection


@section('scripts')
<script>

$('#loadFees').click(function(){

    $.get("{{ route('fees.load.category') }}",{
        student_id: $('#student_id').val(),
        month: $('#month').val(),
        frequency: $('#frequency').val()
    }, function(data){
        $('#feeRows').html(data);
    });

});

</script>
@endsection
