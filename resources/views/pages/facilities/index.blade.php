@extends('layouts.master')

@section('contents')
<div class="container-fluid px-3 py-3">
    <div class="card shadow-sm border-0">
        <div class="card-header bg-gradient-primary text-white py-3">
            <div class="d-flex justify-content-between align-items-center">
                <h4 class="card-title mb-0 font-weight-bold">
                    <i class="fas fa-building mr-2"></i>Facility Bookings
                </h4>
                <a href="{{ route('facilities.bookings.create') }}" class="btn btn-light btn-sm">
                    <i class="fas fa-plus mr-1"></i> New Booking
                </a>
            </div>
        </div>
        <div class="card-body p-0">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show m-3 border-0" role="alert">
                    {{ session('success') }}
                    <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                </div>
            @endif

            <div class="p-3 border-bottom bg-light d-flex justify-content-between align-items-center">
                <span class="text-muted small">Total Confirmed Income: <strong>{{ number_format($total, 2) }}</strong></span>
            </div>

            <div class="table-responsive">
                <table class="table table-hover table-sm mb-0">
                    <thead class="thead-light">
                        <tr>
                            <th>#</th>
                            <th>Title</th>
                            <th>Facility</th>
                            <th>Date</th>
                            <th>Booked By</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($bookings as $booking)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $booking->title }}</td>
                            <td>{{ $booking->facility_name }}</td>
                            <td>{{ $booking->booking_date->format('d/m/Y') }}</td>
                            <td>{{ $booking->booked_by ?? '—' }}</td>
                            <td>{{ number_format($booking->amount, 2) }}</td>
                            <td>
                                <span class="badge badge-{{ $booking->status === 'confirmed' ? 'success' : ($booking->status === 'pending' ? 'warning' : 'danger') }}">
                                    {{ ucfirst($booking->status) }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('facilities.bookings.show', $booking) }}" class="btn btn-xs btn-info"><i class="fas fa-eye"></i></a>
                                <a href="{{ route('facilities.bookings.edit', $booking) }}" class="btn btn-xs btn-warning"><i class="fas fa-edit"></i></a>
                                <form method="POST" action="{{ route('facilities.bookings.destroy', $booking) }}" class="d-inline"
                                      onsubmit="return confirm('Delete this booking?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-xs btn-danger"><i class="fas fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="8" class="text-center text-muted py-4">No bookings found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="p-3">{{ $bookings->links() }}</div>
        </div>
    </div>
</div>
@endsection
