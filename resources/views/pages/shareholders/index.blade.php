@extends('layouts.master')

@section('contents')
<div class="container-fluid">
    <div class="card">
        <div class="card-header text-white rounded-top d-flex justify-content-between align-items-center shadow p-3">
            <h3 class="card-title mb-0">Shareholders</h3>
            <a href="{{ route('shareholders.create') }}" class="btn btn-primary btn-sm ml-auto text-bold">
                + Add Shareholder
            </a>
        </div>

        <div class="card-body px-0 pb-4 pt-0">

            @if (session('success'))
                <div class="alert alert-success mx-3 mt-3">{{ session('success') }}</div>
            @endif

            <div class="table-responsive mt-3">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Phone</th>
                            <th>Email</th>
                            <th>Address</th>
                            <th>Total Capital</th>
                            <th width="120">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($shareholders as $sh)
                            @php
                                $capital    = $sh->capital_sum ?? 0;
                                $withdrawal = $sh->withdrawal_sum ?? 0;
                                $net        = $capital - $withdrawal;
                            @endphp
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td class="fw-bold">{{ $sh->name }}</td>
                                <td>{{ $sh->phone ?? '—' }}</td>
                                <td>{{ $sh->email ?? '—' }}</td>
                                <td>{{ $sh->address ?? '—' }}</td>
                                <td>
                                    <span class="badge"
                                        style="background:{{ $net >= 0 ? '#f0fdf4' : '#fff1f2' }};
                                               color:{{ $net >= 0 ? '#16a34a' : '#e11d48' }};
                                               border:1px solid {{ $net >= 0 ? '#bbf7d0' : '#fecdd3' }};
                                               font-size:12px;padding:5px 10px">
                                        {{ number_format($net, 2) }}
                                    </span>
                                </td>
                                <td style="display:flex;gap:5px;align-items:center">
                                    <a href="{{ route('shareholders.edit', $sh->id) }}" class="btn btn-sm btn-dark">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('shareholders.destroy', $sh->id) }}" method="POST"
                                          class="btn btn-sm btn-danger d-inline m-0 p-0"
                                          onsubmit="return confirm('Delete this shareholder? All their transactions will also be affected.')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach

                        @if ($shareholders->isEmpty())
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">No shareholders found</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>

            <div class="px-3 pt-3">
                {{ $shareholders->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
