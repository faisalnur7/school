@extends('layouts.master')

@section('contents')
<div class="container-fluid px-3 py-3">
    <div class="card shadow-sm border-0">
        <div class="card-header bg-gradient-primary text-white py-3">
            <div class="d-flex justify-content-between align-items-center">
                <h4 class="card-title mb-0 font-weight-bold text-white">
                    <i class="fas fa-edit mr-2"></i>Edit Booking
                </h4>
                <a href="{{ route('facilities.bookings.index') }}" class="btn btn-light btn-sm">
                    <i class="fas fa-arrow-left mr-1"></i> Back
                </a>
            </div>
        </div>

        <form method="POST" action="{{ route('facilities.bookings.update', $booking) }}">
            @csrf @method('PUT')
            <div class="card-body p-3">
                @if($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show border-0 mb-3">
                        <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                        <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                    </div>
                @endif

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Title <span class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control" value="{{ old('title', $booking->title) }}" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Facility Name <span class="text-danger">*</span></label>
                            <input type="text" name="facility_name" class="form-control" value="{{ old('facility_name', $booking->facility_name) }}" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Booking Date <span class="text-danger">*</span></label>
                            <input type="text" name="booking_date" datepicker datepicker-format="dd/mm/yyyy"
                                   class="form-control" value="{{ old('booking_date', $booking->booking_date->format('d/m/Y')) }}"
                                   placeholder="dd/mm/yyyy" autocomplete="off" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Start Time</label>
                            <input type="time" name="start_time" class="form-control" value="{{ old('start_time', $booking->start_time) }}">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>End Time</label>
                            <input type="time" name="end_time" class="form-control" value="{{ old('end_time', $booking->end_time) }}">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Booked By</label>
                            <input type="text" name="booked_by" class="form-control" value="{{ old('booked_by', $booking->booked_by) }}">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Amount (BDT) <span class="text-danger">*</span></label>
                            <input type="number" name="amount" step="0.01" min="0" class="form-control"
                                   value="{{ old('amount', $booking->amount) }}" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Payment Method <span class="text-danger">*</span></label>
                            <select name="payment_method" id="paymentMethod" class="form-control" required>
                                @foreach(['Cash','Bank Transfer','Cheque','Mobile Banking','Other'] as $m)
                                    <option value="{{ $m }}" {{ old('payment_method', $booking->payment_method) == $m ? 'selected' : '' }}>{{ $m }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Status <span class="text-danger">*</span></label>
                            <select name="status" class="form-control" required>
                                @foreach(['pending','confirmed','cancelled'] as $s)
                                    <option value="{{ $s }}" {{ old('status', $booking->status) == $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="form-group">
                            <label>Notes</label>
                            <textarea name="notes" class="form-control" rows="2">{{ old('notes', $booking->notes) }}</textarea>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer bg-light border-top py-2 px-3 d-flex justify-content-between">
                <a href="{{ route('facilities.bookings.index') }}" class="btn btn-secondary btn-sm">Cancel</a>
                <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-save mr-1"></i>Update</button>
            </div>
        </form>
    </div>
</div>
@endsection
