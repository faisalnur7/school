@extends('layouts.master')

@section('contents')
    <div class="container-fluid py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="font-bold text-lg">🚌 Transport Fee Management</h4>
            <a href="{{ route('transports.create') }}" class="btn btn-primary">
                + Assign Transport Fee
            </a>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-gradient-primary text-white py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0 font-weight-bold text-white">
                        <i class="fas fa-filter mr-2"></i>Filter Students
                    </h4>
                </div>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('transports.index') }}">
                    @include('transports.filter', ['isIndex' => true])
                </form>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead style="background:#f8fafc">
                            <tr>
                                <th class="px-4 py-3">Student ID</th>
                                <th class="px-4 py-3">Student Info</th>
                                <th class="px-4 py-3">Academic Info</th>
                                <th class="px-4 py-3">Fee Category</th>
                                <th class="px-4 py-3">Amount</th>
                                <th class="px-4 py-3">Session</th>
                                <th class="px-4 py-3 text-center">Active/Inactive</th>
                                <th class="px-4 py-3 text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($transports as $transport)
                                @php($isRowEditing = old('editing_transport_id') == $transport->id)
                                <tr data-transport-id="{{ $transport->id }}">
                                    <td class="px-4 py-3">
                                        <code style="background:#f1f5f9;padding:3px 8px;border-radius:6px">
                                            {{ $transport->student->student_cid ?? 'N/A'}}
                                        </code>
                                    </td>
                                    <td class="px-4 py-3">
                                        <strong>{{ $transport->student->full_name_en }}</strong><br>
                                        <small class="text-muted">{{ $transport->student->full_name_bn ?? 'N/A' }}</small><br>
                                        <small class="text-muted">DOB: {{ optional($transport->student->date_of_birth)->format('d M, Y') ?? 'N/A' }}</small>
                                    </td>
                                    <td class="px-4 py-3">
                                        @if($transport->studentAcademicInformation)
                                            <small><strong>Class:</strong> {{ $transport->studentAcademicInformation->schoolClass->name_en ?? '—' }}</small><br>
                                            <small><strong>Section:</strong> {{ $transport->studentAcademicInformation->section->name_en ?? '—' }}</small><br>
                                            <small><strong>Group:</strong> {{ $transport->studentAcademicInformation->group->name_en ?? '—' }}</small><br>
                                            <small><strong>Roll:</strong> {{ $transport->studentAcademicInformation->roll ?? '—' }}</small>
                                        @else
                                            <small class="text-muted">No academic info</small>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3">{{ $transport->feeCategory->name }}</td>
                                    <td class="px-4 py-3">
                                        <form id="transportUpdateForm{{ $transport->id }}"
                                              action="{{ route('transports.update', $transport->id) }}"
                                              method="POST"
                                              class="transport-inline-form"
                                              data-original-amount="{{ $transport->amount }}">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="status" value="{{ $transport->status }}">
                                            <input type="hidden" name="remarks" value="{{ $transport->remarks }}">
                                            <input type="hidden" name="editing_transport_id" value="{{ $transport->id }}">

                                            <span class="fw-bold transport-amount-display {{ $isRowEditing ? 'd-none' : '' }}" style="color:#4338ca">
                                                ৳{{ number_format($transport->amount, 2) }}
                                            </span>
                                            <input
                                                type="number"
                                                name="amount"
                                                class="form-control form-control-sm transport-amount-input {{ $isRowEditing ? '' : 'd-none' }}"
                                                value="{{ old('amount', $transport->amount) }}"
                                                step="0.01"
                                                min="0"
                                            >
                                            @error('amount')
                                                <small class="text-danger d-block mt-1">{{ $message }}</small>
                                            @enderror
                                        </form>
                                    </td>
                                    <td class="px-4 py-3">{{ $transport->academicSession->name_en }}</td>
                                    <td class="px-4 py-3 text-center">
                                        <form action="{{ route('transports.toggle-status', $transport->id) }}" method="POST">
                                            @csrf
                                            <div class="custom-control custom-switch">
                                                <input type="checkbox" class="custom-control-input" id="transportSwitch{{ $transport->id }}" onchange="this.form.submit()" {{ $transport->status === 'active' ? 'checked' : '' }}>
                                                <label class="custom-control-label" for="transportSwitch{{ $transport->id }}"></label>
                                            </div>
                                        </form>
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <div class="transport-row-actions">
                                            <button type="button" class="btn btn-sm btn-warning me-1 transport-edit-btn {{ $isRowEditing ? 'd-none' : '' }}">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button type="submit"
                                                    form="transportUpdateForm{{ $transport->id }}"
                                                    class="btn btn-sm btn-success me-1 transport-update-btn {{ $isRowEditing ? '' : 'd-none' }}">
                                                Update
                                            </button>
                                            <button type="button" class="btn btn-sm btn-secondary me-1 transport-cancel-btn {{ $isRowEditing ? '' : 'd-none' }}">
                                                Cancel
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-5 text-muted">
                                        <div style="font-size:48px;opacity:.2">🚌</div>
                                        <p class="mt-3">No transport fees assigned yet</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if($transports->hasPages())
                <div class="card-footer bg-white">
                    {{ $transports->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection

@section('scripts')
    @include('scripts.common.load_academic_information')
    <script>
        $(document).on('click', '.transport-edit-btn', function () {
            const $row = $(this).closest('tr');
            const $form = $row.find('.transport-inline-form');

            $row.addClass('is-editing');
            $row.find('.transport-amount-display').addClass('d-none');
            $row.find('.transport-amount-input').removeClass('d-none').trigger('focus').trigger('select');
            $row.find('.transport-edit-btn').addClass('d-none');
            $row.find('.transport-update-btn, .transport-cancel-btn').removeClass('d-none');
            $form.find('input[name="editing_transport_id"]').val($row.data('transport-id'));
        });

        $(document).on('click', '.transport-cancel-btn', function () {
            const $row = $(this).closest('tr');
            const $form = $row.find('.transport-inline-form');
            const originalAmount = $form.data('original-amount');

            $row.removeClass('is-editing');
            $row.find('.transport-amount-input').val(originalAmount).addClass('d-none');
            $row.find('.transport-amount-display')
                .text('৳' + parseFloat(originalAmount).toFixed(2))
                .removeClass('d-none');
            $row.find('.transport-edit-btn').removeClass('d-none');
            $row.find('.transport-update-btn, .transport-cancel-btn').addClass('d-none');
        });
    </script>
@endsection
