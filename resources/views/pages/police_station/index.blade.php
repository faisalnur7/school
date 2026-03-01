@extends('layouts.master')

@section('title', 'Police Station List')
@section('page_title', 'Police Station List')

@section('contents')
    <div class="container-fluid">
        <div class="card border-0 shadow-sm rounded mb-4">
            {{-- <div
                class="card-header text-white rounded-top d-flex justify-content-between align-items-center shadow p-3"
                   >
                <h4 class="mb-0">Filter Police Station</h4>
            </div> --}}
            <div class="card-body">
                @include('pages.police_station.filter')
            </div>
        </div>
        <div class="card">
            <div class="card-header text-white rounded-top d-flex justify-content-between align-items-center shadow p-3"
                   >
                <h3 class="card-title mb-0">All Police Stations</h3>
                <a href="{{ route('police-station.create') }}" class="btn btn-primary btn-sm ml-auto text-bold">
                    <i class="fas fa-plus"></i> Add Police Station
                </a>
            </div>
            <div class="card-body px-0 pb-4 pt-0">
                @if (session('success'))
                    <div class="float-right-bottom alert alert-success">{{ session('success') }}</div>
                @endif

                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="thead-light">
                            <tr>
                                <th>#</th>
                                <th>District</th>
                                <th>Name</th>
                                <th>Bangla Name</th>
                                <th>Status</th>
                                <th style="width: 150px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($stations as $station)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $station->district->name ?? '-' }}</td>
                                    <td>{{ $station->name }}</td>
                                    <td>{{ $station->bn_name }}</td>
                                    <td>
                                        @if ($station->status)
                                            <span class="badge bg-success">Active</span>
                                        @else
                                            <span class="badge bg-secondary">Inactive</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('police-station.edit', $station->id) }}"
                                            class="btn btn-sm btn-dark" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('police-station.destroy', $station->id) }}" method="POST"
                                            style="display:inline;">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger"
                                                onclick="return confirm('Are you sure you want to delete this police station?')"
                                                title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-3 px-3">
                    {{ $stations->links('vendor.pagination.tailwind') }}
                </div>
            </div>
        </div>
    </div>
@endsection
