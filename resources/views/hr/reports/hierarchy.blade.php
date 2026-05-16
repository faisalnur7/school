@extends('layouts.master')
@section('contents')
<div class="container-fluid">
    <div class="card">
        <div class="card-header bg-gradient-primary text-white py-3">
            <div class="d-flex justify-content-between align-items-center">
                <h4 class="card-title mb-0 font-weight-bold text-white">
                    <i class="fas fa-sitemap mr-2"></i>Designation Hierarchy Report
                </h4>
                <button onclick="window.print()" class="btn btn-light btn-sm no-print">
                    <i class="fas fa-print mr-1"></i> Print
                </button>
            </div>
        </div>
        <div class="card-body">
            @if($designations->isEmpty())
                <div class="text-center text-muted py-4"><i class="fas fa-inbox fa-2x mb-2 d-block"></i>No designations found.</div>
            @else
            @foreach($designations->groupBy('employee_type') as $type => $group)
            <h5 class="text-uppercase font-weight-bold mb-2 mt-3" style="color:#1e3a5f">
                <i class="fas fa-{{ $type === 'teacher' ? 'chalkboard-teacher' : 'user-tie' }} mr-2"></i>{{ ucfirst($type) }}s
            </h5>
            <div class="table-responsive mb-4">
                <table class="table table-sm table-bordered">
                    <thead class="thead-dark"><tr><th>Level</th><th>Designation</th><th>Active Employees</th><th>Employee Names</th></tr></thead>
                    <tbody>
                        @foreach($group->sortBy('hierarchy_level') as $d)
                        <tr>
                            <td>
                                <span class="badge badge-primary" style="font-size:13px;width:32px;height:32px;line-height:24px;border-radius:50%;display:inline-block;text-align:center">{{ $d->hierarchy_level }}</span>
                            </td>
                            <td class="font-weight-bold">{{ $d->name }}</td>
                            <td>
                                <span class="badge badge-{{ $d->employees_count > 0 ? 'success' : 'secondary' }} px-3">{{ $d->employees_count }}</span>
                            </td>
                            <td>
                                @forelse($d->employees as $e)
                                    <span class="badge badge-light border mr-1">{{ $e->name }}</span>
                                @empty
                                    <span class="text-muted">—</span>
                                @endforelse
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endforeach
            @endif
        </div>
    </div>
</div>
<style>@media print { .no-print,.main-sidebar,.main-header,.content-header{display:none!important} .content-wrapper{margin-left:0!important} table{font-size:11px} th{background:#f5f5f5!important} }</style>
@endsection
