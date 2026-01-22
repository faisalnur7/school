@extends('layouts.master')

@section('title', 'District List')
@section('page_title', 'District List')

@section('contents')
    <div class="container-fluid">
        <div class="card border-0 shadow-sm rounded mb-4">
            {{-- <div
                class="card-header text-white rounded-top d-flex justify-content-between align-items-center shadow p-3"
                    style="background: linear-gradient(90deg, #343a40, #212529);">
                <h4 class="mb-0">Filter District</h4>
            </div> --}}
            <div class="card-body">
                @include('pages.district._filter')
            </div>
        </div>
        <div class="card">
            <div class="card-header text-white rounded-top d-flex justify-content-between align-items-center shadow p-3"
                style="background: linear-gradient(90deg, #343a40, #212529);">
                <h3 class="card-title mb-0">All Districts</h3>
                <a href="{{ route('district.create') }}" class="btn btn-primary btn-sm ml-auto text-bold">
                    <i class="fas fa-plus"></i> Add District
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
                                <th>Division</th>
                                <th>Name</th>
                                <th>Bangla Name</th>
                                <th>Website</th>
                                <th>Status</th>
                                <th style="width: 150px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($districts as $district)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $district->division->name ?? '-' }}</td>
                                    <td>{{ $district->name }}</td>
                                    <td>{{ $district->bn_name }}</td>
                                    <td>{{ $district->url }}</td>
                                    <td>
                                        @if ($district->status)
                                            <span class="badge bg-success">Active</span>
                                        @else
                                            <span class="badge bg-secondary">Inactive</span>
                                        @endif
                                    </td>
                                    <td style="display: flex; justify-content: center; align-items: self-start; gap: 5px;">
                                        <a href="{{ route('district.edit', $district->id) }}" class="btn btn-sm btn-dark"
                                            title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('district.destroy', $district->id) }}" method="POST"
                                            style="display:inline;">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger"
                                                onclick="return confirm('Are you sure you want to delete this district?')"
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
                    {{ $districts->links('vendor.pagination.tailwind') }}
                </div>

            </div>
        </div>
    </div>
@endsection
