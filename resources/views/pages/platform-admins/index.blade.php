@extends('layouts.master')

@section('contents')
<div class="container-fluid px-3 py-3">
    <div class="card shadow-sm border-0 mb-3">
        <div class="card-header bg-gradient-primary text-white d-flex justify-content-between align-items-center">
            <h4 class="mb-0 text-white"><i class="fas fa-user-shield mr-2"></i>Platform Admins</h4>
            <a href="{{ route('platform-admins.create') }}" class="btn btn-light btn-sm">Add Platform Admin</a>
        </div>
        <div class="card-body px-0">
            <div class="table-responsive"><table class="table table-hover mb-0">
                <thead><tr><th>Name</th><th>Email</th><th>Status</th><th>Actions</th></tr></thead>
                <tbody>
                @forelse($platformAdmins as $admin)
                    <tr><td>{{ $admin->name }}</td><td>{{ $admin->email }}</td>
                        <td><span class="badge badge-{{ $admin->is_active ? 'success' : 'secondary' }}">{{ $admin->is_active ? 'Active' : 'Inactive' }}</span></td>
                        <td class="d-flex gap-1">
                            <a href="{{ route('platform-admins.edit', $admin) }}" class="btn btn-sm btn-dark"><i class="fas fa-edit"></i></a>
                            @if(auth()->id() !== $admin->id)
                            <form method="POST" action="{{ route('platform-admins.destroy', $admin) }}" onsubmit="return confirm('Delete this platform admin?')">
                                @csrf @method('DELETE') <button class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                            </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="text-center text-muted py-4">No platform admins found.</td></tr>
                @endforelse
                </tbody>
            </table></div>
        </div>
    </div>

    <div class="card shadow-sm border-0 role-permissions-group">
        <div class="card-header"><h5 class="mb-0">Permissions visible in institute Roles CRUD</h5></div>
        <form method="POST" action="{{ route('platform-admins.permissions.update') }}">
            @csrf
            <div class="card-body p-3">
                @include('pages.roles._permission-cards')
            </div>
        </form>
    </div>
</div>
@endsection

@section('styles')
<style>
    .permission-card-grid { display:grid; grid-template-columns:repeat(4,minmax(0,1fr)); gap:.85rem; }
    .permission-card-tile { appearance:none; border:1px solid #dbe4ee; border-radius:1rem; background:#fff; padding:1rem; min-height:86px; display:flex; align-items:center; justify-content:space-between; gap:.75rem; box-shadow:0 8px 22px rgba(15,23,42,.05); text-align:left; width:100%; transition:transform .15s ease,box-shadow .15s ease,border-color .15s ease; }
    .permission-card-tile:hover,.permission-card-tile:focus { border-color:#2563eb; box-shadow:0 12px 26px rgba(37,99,235,.12); transform:translateY(-1px); outline:none; }
    .permission-card-tile__copy { min-width:0; display:flex; flex-direction:column; gap:.3rem; }
    .permission-card-tile__title { font-size:1rem; font-weight:800; color:#0f172a; line-height:1.25; }
    .permission-card-tile__subtitle { font-size:.8rem; color:#64748b; }
    .permission-card-tile__icon { color:#94a3b8; font-size:1.2rem; }
    .permission-modal__content { border:0; border-radius:1rem; overflow:hidden; }
    .permission-modal__header { background:#f8fafc; border-bottom:1px solid #e2e8f0; }
    .permission-modal__body { padding:0; }
    .permission-modal__footer { background:#f8fafc; border-top:1px solid #e2e8f0; }
    .permission-matrix-wrap { max-height:65vh; overflow:auto; }
    .permission-matrix { min-width:720px; }
    .permission-matrix th,.permission-matrix td { padding:.65rem .75rem; vertical-align:middle; border-bottom:1px solid #eef2f7; }
    .permission-matrix thead th { position:sticky; top:0; z-index:2; background:#fff; color:#64748b; font-size:.72rem; text-transform:uppercase; letter-spacing:.04em; }
    .permission-row-label { font-weight:700; color:#1e293b; }
    @media (max-width:1200px) { .permission-card-grid { grid-template-columns:repeat(3,minmax(0,1fr)); } }
    @media (max-width:768px) { .permission-card-grid { grid-template-columns:repeat(2,minmax(0,1fr)); gap:.65rem; } .permission-card-tile { min-height:78px; padding:.8rem; } }
    @media (max-width:480px) { .permission-card-grid { grid-template-columns:1fr; } }
</style>
@endsection
