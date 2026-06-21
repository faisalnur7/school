@extends('layouts.master')

@section('contents')
    <div class="container-fluid py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold">🚌 Edit Transport Fee</h4>
            <a href="{{ route('transports.index') }}" class="btn btn-secondary">
                ← Back to List
            </a>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-header bg-gradient-primary text-white py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0 font-weight-bold text-white">
                        <i class="fas fa-user-edit mr-2"></i>Update Student Transport
                    </h4>
                    <a href="{{ route('transports.index') }}" class="btn btn-light btn-sm">
                        <i class="fas fa-arrow-left mr-1"></i> Back
                    </a>
                </div>
            </div>

            <div class="card-body">
                <form method="POST" action="{{ route('transports.update', $transport->id) }}">
                    @csrf
                    @method('PUT')

                    <div class="row mb-4">
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Academic Session</label>
                            <input type="text" class="form-control" value="{{ $transport->academicSession?->name_en ?? 'N/A' }}" disabled>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Class</label>
                            <input type="text" class="form-control" value="{{ $transport->studentAcademicInformation?->schoolClass?->name_en ?? 'N/A' }}" disabled>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Section</label>
                            <input type="text" class="form-control" value="{{ $transport->studentAcademicInformation?->section?->name_en ?? 'N/A' }}" disabled>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Group</label>
                            <input type="text" class="form-control" value="{{ $transport->studentAcademicInformation?->group?->name_en ?? 'N/A' }}" disabled>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered align-middle">
                            <thead style="background:#f8fafc">
                                <tr>
                                    <th width="12%">Student ID</th>
                                    <th width="10%">Roll</th>
                                    <th width="28%">Student Name</th>
                                    <th width="16%">Amount (৳)</th>
                                    <th width="14%">Status</th>
                                    <th width="20%">Current Transport</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>{{ $transport->student?->student_cid ?? 'N/A' }}</td>
                                    <td>{{ $transport->studentAcademicInformation?->roll ?? 'N/A' }}</td>
                                    <td>
                                        <div class="fw-bold">{{ $transport->student?->full_name_en ?? 'N/A' }}</div>
                                    </td>
                                    <td>
                                        <input
                                            type="number"
                                            name="amount"
                                            class="form-control"
                                            value="{{ old('amount', $transport->amount) }}"
                                            step="0.01"
                                            min="0"
                                            required
                                        >
                                        @error('amount')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </td>
                                    <td>
                                        <select name="status" class="form-control" required>
                                            <option value="active" @selected(old('status', $transport->status) === 'active')>Active</option>
                                            <option value="inactive" @selected(old('status', $transport->status) === 'inactive')>Inactive</option>
                                        </select>
                                        @error('status')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </td>
                                    <td class="text-center">
                                        <span class="badge badge-info">
                                            ৳{{ number_format((float) $transport->amount, 2) }}
                                        </span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-12">
                            <label class="form-label">Remarks</label>
                            <textarea name="remarks" class="form-control" rows="3" placeholder="Optional remarks">{{ old('remarks', $transport->remarks) }}</textarea>
                            @error('remarks')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>

                    <div class="text-right mt-4">
                        <button type="submit" class="btn btn-primary">
                            Update Transport Fee
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
