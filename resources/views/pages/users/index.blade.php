@extends('layouts.master')

@section('contents')
<div class="container-fluid">
    <div class="card">
        <div class="card-header text-white rounded-top d-flex justify-content-between align-items-center shadow p-3">
            <h3 class="card-title mb-0 text-white text-lg">Users</h3>
            <a href="{{ route('users.create') }}" class="btn btn-primary btn-sm ml-auto text-bold">
                + Add User
            </a>
        </div>

        <div class="card-body border-bottom py-2 px-3">
            <ul class="nav nav-tabs users-role-tabs flex-nowrap overflow-auto" role="tablist" aria-label="Users by role">
                <li class="nav-item" role="presentation">
                    <a class="nav-link {{ $selectedRole === 'all' ? 'active' : '' }}" href="{{ route('users.index', ['role' => 'all']) }}">
                        All Users <span class="badge badge-light ml-1">{{ $totalUsers }}</span>
                    </a>
                </li>
                @foreach ($roles as $role)
                    <li class="nav-item" role="presentation">
                        <a class="nav-link {{ $selectedRole === (string) $role->id ? 'active' : '' }}" href="{{ route('users.index', ['role' => $role->id]) }}">
                            {{ $role->name }} <span class="badge badge-light ml-1">{{ $role->users_count }}</span>
                        </a>
                    </li>
                @endforeach
                <li class="nav-item ml-auto" role="presentation">
                    <a class="nav-link text-danger {{ $selectedRole === 'make-super-admin' ? 'active' : '' }}" href="{{ route('users.index', ['role' => 'make-super-admin']) }}">
                        <i class="fas fa-user-shield mr-1"></i> Make Super Admin
                    </a>
                </li>
            </ul>
        </div>

        <div class="card-body px-0 pb-4 pt-0">
            @if ($users->isEmpty())
                <div class="text-center text-muted py-4">No users found</div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Image</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Super Admin</th>
                                <th>Active</th>
                                <th width="90">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php($actorIsPlatformAdmin = auth()->user()?->isPlatformAdmin())
                            @foreach ($users as $user)
                                @php($canManage = ! $user->role?->is_reserved || ($actorIsPlatformAdmin && ! $user->role->isPlatformAdmin()))
                                @php($isSuperAdmin = $user->is_super_admin || $user->role?->isInstituteSuperAdmin())
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>
                                        <img src="{{ $user->image_url }}" alt="User Image" class="elevation-1" style="width:64px; height:64px; object-fit:cover; border-radius:12px;">
                                    </td>
                                    <td>{{ $user->name }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td>
                                        @if ($user->role)
                                            <span class="badge badge-info">{{ $user->role->name }}</span>
                                        @else
                                            <span class="badge badge-secondary">No Role</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($user->is_super_admin)
                                            <span class="badge badge-danger">Yes</span>
                                        @else
                                            <span class="badge badge-light">No</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($canManage)
                                        <form action="{{ route('users.toggle-status', $user->id) }}" method="POST" class="mb-0">
                                            @csrf
                                            <div class="custom-control custom-switch">
                                                <input
                                                    type="checkbox"
                                                    class="custom-control-input"
                                                    id="userStatusSwitch{{ $user->id }}"
                                                    onchange="this.form.submit()"
                                                    {{ $user->is_active ? 'checked' : '' }}
                                                >
                                                <label class="custom-control-label" for="userStatusSwitch{{ $user->id }}"><span class="sr-only">{{ __('Active') }}</span></label>
                                            </div>
                                        </form>
                                        @else
                                            <span class="badge {{ $user->is_active ? 'badge-success' : 'badge-secondary' }}">{{ $user->is_active ? 'Active' : 'Inactive' }}</span>
                                        @endif
                                    </td>
                                    <td style="display: flex; justify-content: center; align-items: center; gap: 5px;">
                                        @if ($canManage)
                                            <a href="{{ route('users.edit', $user->id) }}" class="btn btn-sm btn-dark" aria-label="{{ __('Edit') }}" title="{{ __('Edit') }}">
                                                <i class="fas fa-edit" aria-hidden="true"></i>
                                            </a>
                                        @endif
                                        @if ($actorIsPlatformAdmin && $isSuperAdmin && $canManage)
                                            <form action="{{ route('users.revoke-super-admin', $user->id) }}" method="POST" class="mb-0"
                                                data-confirm="{{ __('Withdraw Super Admin from :name?', ['name' => $user->name]) }}" data-confirm-action="{{ __('Withdraw Super Admin') }}" data-confirm-cancel="{{ __('Keep Super Admin') }}">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Withdraw Super Admin">
                                                    <i class="fas fa-user-slash"></i>
                                                </button>
                                            </form>
                                        @endif
                                        @if ($selectedRole === 'make-super-admin' && auth()->user()?->is_super_admin)
                                            <form action="{{ route('users.make-super-admin', $user->id) }}" method="POST" class="mb-0">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-danger" title="Make Super Admin">
                                                    <i class="fas fa-user-shield"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-center mt-3">
                    {{ $users->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
<style>
    .users-role-tabs { border-bottom: 0; gap: .35rem; }
    .users-role-tabs .nav-link { white-space: nowrap; border: 0; border-radius: .45rem; color: #4b5563; }
    .users-role-tabs .nav-link.active { background: #eef2ff; color: #1d4ed8; font-weight: 600; }
    .users-role-tabs .nav-link.text-danger.active { background: #fff1f2; color: #dc2626 !important; }
    @media (max-width: 768px) { .users-role-tabs .nav-item.ml-auto { margin-left: 0 !important; } }
</style>
@endsection
