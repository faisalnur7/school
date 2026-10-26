@extends('layouts.master')

@section('contents')
    <div class="container-fluid qualifications-page">
        <div class="card">
            <div class="card-header text-white rounded-top d-flex flex-column flex-sm-row justify-content-between align-items-stretch align-items-sm-center shadow p-3">
                <div>
                    <h3 class="card-title mb-1"><i class="fas fa-graduation-cap mr-2" aria-hidden="true"></i>{{ __('Qualifications') }}</h3>
                    <small class="d-block text-white-50">{{ __('Manage educational qualification names and abbreviations.') }}</small>
                </div>
                <a href="{{ route('educational-qualifications.create') }}" class="btn btn-primary btn-sm ml-sm-auto mt-2 mt-sm-0 text-bold">
                    <i class="fas fa-plus mr-1" aria-hidden="true"></i>{{ __('Add Qualification') }}
                </a>
            </div>

            <div class="card-body px-0 pb-4 pt-0">
                @include('hr._alerts')
                <form method="GET" class="qualifications-filter p-3 mb-0">
                    <div class="input-group input-group-sm qualifications-search">
                        <input type="search" name="search" value="{{ request('search') }}" class="form-control" placeholder="{{ __('Search name or abbreviation') }}" aria-label="{{ __('Search qualifications') }}">
                        <div class="input-group-append">
                            <button type="submit" class="btn btn-primary" title="{{ __('Search') }}" aria-label="{{ __('Search') }}"><i class="fas fa-search" aria-hidden="true"></i></button>
                        </div>
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 qualifications-table">
                        <thead>
                            <tr><th>#</th><th>{{ __('Name') }}</th><th>{{ __('Abbreviation') }}</th><th>{{ __('Status') }}</th><th class="text-center">{{ __('Actions') }}</th></tr>
                        </thead>
                        <tbody>
                            @forelse($qualifications as $qualification)
                                <tr>
                                    <td>{{ $qualifications->firstItem() + $loop->index }}</td>
                                    <td class="font-weight-bold">{{ $qualification->name }}</td>
                                    <td>{{ $qualification->abbreviation }}</td>
                                    <td><span class="badge badge-pill badge-{{ $qualification->status === 'active' ? 'success' : 'secondary' }}">{{ ucfirst($qualification->status) }}</span></td>
                                    <td class="text-center">
                                        <a href="{{ route('educational-qualifications.edit', $qualification) }}" class="qualifications-action-btn qualifications-action-edit" title="{{ __('Edit') }}" aria-label="{{ __('Edit') }}"><i class="fas fa-edit" aria-hidden="true"></i></a>
                                        <form action="{{ route('educational-qualifications.destroy', $qualification) }}" method="POST" class="qualifications-action-form" onsubmit="return confirm('{{ __('Delete this qualification?') }}')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="qualifications-action-btn qualifications-action-delete" title="{{ __('Delete') }}" aria-label="{{ __('Delete') }}"><i class="fas fa-trash" aria-hidden="true"></i></button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="text-center text-muted py-4">{{ __('No qualifications found.') }}</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($qualifications->hasPages())
                    <div class="px-3 pt-3">{{ $qualifications->links() }}</div>
                @endif
            </div>
        </div>
    </div>
@endsection

@section('styles')
    <style>
        .qualifications-page .qualifications-filter { border-bottom: 1px solid #e5e7eb; background: #f8fafc; }
        .qualifications-page .qualifications-search { max-width: 420px; }
        .qualifications-page .qualifications-search .form-control,
        .qualifications-page .qualifications-search .btn { min-height: 2.35rem; }
        .qualifications-page .qualifications-table thead th { white-space: nowrap; font-size: .78rem; text-transform: uppercase; letter-spacing: .02em; color: #475569; background: #f8fafc; border-top: 0; }
        .qualifications-page .qualifications-table td,
        .qualifications-page .qualifications-table th { padding: .85rem 1rem; vertical-align: middle; }
        .qualifications-page .qualifications-action-btn { width: 2.15rem; height: 2.15rem; display: inline-flex; align-items: center; justify-content: center; padding: 0; margin: 0 0 0 .25rem; border-radius: .55rem; color: #fff !important; font-size: .85rem; line-height: 1; text-decoration: none; border: 0; transition: transform .15s ease, background-color .15s ease; }
        .qualifications-page .qualifications-action-form { display: inline-block; margin: 0; }
        .qualifications-page .qualifications-action-edit { background: #334155 !important; border: 1px solid #475569; }
        .qualifications-page .qualifications-action-delete { background: #ef3340 !important; }
        .qualifications-page .qualifications-action-btn:hover,
        .qualifications-page .qualifications-action-btn:focus { color: #fff !important; transform: translateY(-1px); }
        .qualifications-page .qualifications-action-edit:hover,
        .qualifications-page .qualifications-action-edit:focus { background: #1e293b !important; }
        .qualifications-page .qualifications-action-delete:hover,
        .qualifications-page .qualifications-action-delete:focus { background: #dc2636 !important; }
        @media (max-width: 575.98px) {
            .qualifications-page .qualifications-search { max-width: none; }
            .qualifications-page .qualifications-table td,
            .qualifications-page .qualifications-table th { padding: .7rem .75rem; }
        }
    </style>
@endsection
