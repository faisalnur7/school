@extends('layouts.master')

@section('contents')
<div class="col-12">
    <div class="card shadow-sm border-0 profile-card">
        <div class="card-header bg-gradient-primary text-white py-3">
            <h3 class="card-title text-white mb-0"><i class="fas fa-user-cog mr-2"></i>Profile Settings</h3>
        </div>
        <div class="card-body p-4 p-md-5">
            @include('hr._alerts')

            <form action="{{ route('account.profile.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="row align-items-stretch">
                    <div class="col-lg-4 mb-4 mb-lg-0">
                        <div class="profile-aside h-100 text-center flex flex-col items-center">
                            <img id="profile_photo_preview" src="{{ $user->image_url }}" class="profile-photo mb-3" alt="Profile Photo">
                            <h5 class="font-weight-bold mb-1">{{ $user->name }}</h5>
                            <p class="text-muted mb-3">{{ $user->role->name ?? ($employee?->employee_type ? ucfirst($employee->employee_type) : 'N/A') }}</p>
                            <label class="btn btn-outline-primary btn-sm mb-2">
                                <i class="fas fa-camera mr-1"></i>Change Photo
                                <input type="file" id="profile_photo_input" name="image" class="d-none" accept="image/*">
                            </label>
                            <div class="small text-muted">JPG, PNG, WEBP (max 100KB)</div>
                        </div>
                    </div>

                    <div class="col-lg-8">
                        <div class="profile-form-wrap">
                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label class="small font-weight-bold text-muted mb-1">Name</label>
                                <input type="text" name="name" value="{{ old('name', $user->name) }}" class="form-control form-control-lg" required>
                            </div>
                            <div class="col-md-6 form-group">
                                <label class="small font-weight-bold text-muted mb-1">Email</label>
                                <input type="email" name="email" value="{{ old('email', $user->email) }}" class="form-control form-control-lg" required>
                            </div>
                            <div class="col-md-6 form-group">
                                <label class="small font-weight-bold text-muted mb-1">Phone</label>
                                <input type="text" name="phone" value="{{ old('phone', $employee?->phone) }}" class="form-control form-control-lg">
                            </div>
                            <div class="col-md-6 form-group">
                                <label class="small font-weight-bold text-muted mb-1">Role</label>
                                <input type="text" value="{{ $user->role->name ?? ($employee?->employee_type ? ucfirst($employee->employee_type) : 'N/A') }}" class="form-control form-control-lg bg-light" disabled>
                            </div>
                            <div class="col-md-12 form-group">
                                <label class="small font-weight-bold text-muted mb-1">Address</label>
                                <textarea name="address" class="form-control form-control-lg" rows="4">{{ old('address', $employee?->address) }}</textarea>
                            </div>
                        </div>
                        <div class="d-flex justify-content-end">
                            <button class="btn btn-primary btn-lg px-4"><i class="fas fa-save mr-1"></i> Save Profile</button>
                        </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@if($employee && $employee->employee_type === 'teacher')
<div class="col-12">
    <div class="row">
        <div class="col-md-6">
            <div class="card card-outline card-primary">
                <div class="card-header"><h3 class="card-title">Assigned as Class Teacher</h3></div>
                <div class="card-body p-0">
                    <table class="table table-sm mb-0">
                        <thead><tr><th>Session</th><th>Class</th><th>Section</th></tr></thead>
                        <tbody>
                            @forelse($teacherAssignments as $item)
                                <tr>
                                    <td>{{ $item->session->name ?? '-' }}</td>
                                    <td>{{ $item->schoolClass->name ?? '-' }}</td>
                                    <td>{{ $item->section->name ?? '-' }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="3" class="text-muted text-center">No class-teacher assignment found.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card card-outline card-info">
                <div class="card-header"><h3 class="card-title">Assigned Subjects</h3></div>
                <div class="card-body p-0">
                    <table class="table table-sm mb-0">
                        <thead><tr><th>Class</th><th>Subject</th></tr></thead>
                        <tbody>
                            @forelse($assignedSubjects as $item)
                                <tr>
                                    <td>{{ $item->schoolClass->name ?? '-' }}</td>
                                    <td>{{ $item->subject->name ?? '-' }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="2" class="text-muted text-center">No subject assignment found.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card card-outline card-success">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">Leave Balance</h3>
                    <a href="{{ route('account.leave.create') }}" class="btn btn-success btn-xs">Apply for Leave</a>
                </div>
                <div class="card-body p-0">
                    <table class="table table-sm mb-0">
                        <thead><tr><th>Type</th><th>Total</th><th>Used</th><th>Remaining</th></tr></thead>
                        <tbody>
                            @forelse($leaveBalances as $balance)
                                <tr>
                                    <td>{{ ucfirst($balance->leave_type) }}</td>
                                    <td>{{ $balance->total_leave }}</td>
                                    <td>{{ $balance->used_leave }}</td>
                                    <td><strong>{{ $balance->remaining_leave }}</strong></td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="text-muted text-center">No leave balance found.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card card-outline card-warning">
                <div class="card-header"><h3 class="card-title">Holiday Calendar (Next 30)</h3></div>
                <div class="card-body p-0">
                    <table class="table table-sm mb-0">
                        <thead><tr><th>Date</th><th>Title</th><th>Description</th></tr></thead>
                        <tbody>
                            @forelse($holidays as $holiday)
                                <tr>
                                    <td>{{ $holiday->date->format('d M Y') }}</td>
                                    <td>{{ $holiday->title ?: '-' }}</td>
                                    <td>{{ $holiday->description ?: '-' }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="3" class="text-muted text-center">No upcoming holidays found.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endif
@endsection

@section('scripts')
<style>
    .profile-card {
        border-radius: 16px;
    }
    .profile-aside {
        border: 1px solid #e5e7eb;
        border-radius: 14px;
        padding: 28px 20px;
        background: linear-gradient(180deg, #f8fbff 0%, #ffffff 100%);
    }
    .profile-photo {
        width: 220px;
        height: 220px;
        border-radius: 24px;
        object-fit: cover;
        border: 4px solid #fff;
        box-shadow: 0 8px 24px rgba(15, 23, 42, 0.12);
    }
    .profile-form-wrap {
        border: 1px solid #e5e7eb;
        border-radius: 14px;
        padding: 22px 22px 18px;
        background: #fff;
    }
    .profile-form-wrap .form-control-lg {
        height: calc(2.45em + 0.75rem + 2px);
        border-radius: 10px;
    }
    @media (max-width: 991.98px) {
        .profile-photo {
            width: 180px;
            height: 180px;
            border-radius: 20px;
        }
        .profile-form-wrap {
            padding: 16px;
        }
    }
</style>
<script>
    document.getElementById('profile_photo_input')?.addEventListener('change', function (e) {
        const file = e.target.files?.[0];
        if (!file) return;
        const preview = document.getElementById('profile_photo_preview');
        preview.src = URL.createObjectURL(file);
    });
</script>
@endsection
