@extends('layouts.master')

@section('contents')
<div class="container-fluid px-3 py-3">
    <div class="card shadow-sm border-0">
        <div class="card-header bg-gradient-primary text-white py-3">
            <div class="d-flex justify-content-between align-items-center">
                <h4 class="card-title mb-0 font-weight-bold text-white">
                    <i class="fas fa-building mr-2"></i>Booking Details
                </h4>
                <div>
                    <a href="{{ route('facilities.bookings.edit', $booking) }}" class="btn btn-warning btn-sm mr-1">
                        <i class="fas fa-edit mr-1"></i>Edit
                    </a>
                    <a href="{{ route('facilities.bookings.index') }}" class="btn btn-light btn-sm">
                        <i class="fas fa-arrow-left mr-1"></i>Back
                    </a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <table class="table table-bordered table-sm">
                <tr><th width="200">Title</th><td>{{ $booking->title }}</td></tr>
                <tr><th>Facility</th><td>{{ $booking->facility_name }}</td></tr>
                <tr><th>Date</th><td>{{ $booking->booking_date->format('d/m/Y') }}</td></tr>
                <tr><th>Time</th><td>{{ $booking->start_time ?? '—' }} – {{ $booking->end_time ?? '—' }}</td></tr>
                <tr><th>Booked By</th><td>{{ $booking->booked_by ?? '—' }}</td></tr>
                <tr><th>Amount</th><td>{{ number_format($booking->amount, 2) }} BDT</td></tr>
                <tr><th>Payment Method</th><td>{{ $booking->payment_method }}</td></tr>
                <tr><th>Status</th>
                    <td><span class="badge badge-{{ $booking->status === 'confirmed' ? 'success' : ($booking->status === 'pending' ? 'warning' : 'danger') }}">{{ ucfirst($booking->status) }}</span></td>
                </tr>
                <tr><th>Notes</th><td>{{ $booking->notes ?? '—' }}</td></tr>
                <tr><th>Recorded By</th><td>{{ $booking->recorder?->name ?? '—' }}</td></tr>
                <tr><th>Created At</th><td>{{ $booking->created_at->format('d/m/Y H:i') }}</td></tr>
            </table>
        </div>
    </div>
</div>
@endsection
