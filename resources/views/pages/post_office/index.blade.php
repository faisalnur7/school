@extends('layouts.master')

@section('title', 'Post Office List')
@section('page_title', 'Post Office List')

@section('contents')
    <div class="container-fluid">
        <div class="card border-0 shadow-sm rounded mb-4">
            {{-- <div
                class="card-header text-white rounded-top d-flex justify-content-between align-items-center shadow p-3"
                   >
                <h4 class="mb-0">Filter Post Offices</h4>
            </div> --}}
            <div class="card-body">
                @include('pages.post_office.filter')
            </div>
        </div>

        <div class="card">
            <div class="card-header text-white rounded-top d-flex justify-content-between align-items-center shadow p-3"
                   >
                <h3 class="card-title mb-0 text-white text-lg">All Post Offices</h3>
                <a href="{{ route('post-office.create') }}" class="btn btn-primary btn-sm ml-auto text-bold">
                    <i class="fas fa-plus"></i> Add Post Office
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
                                <th>District</th>
                                <th>Police Station</th>
                                <th>Post Office</th>
                                <th>Bangla Name</th>
                                <th>Post Code</th>
                                <th>Status</th>
                                <th style="width: 150px;">Actions</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($postOffices as $postOffice)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $postOffice->policeStation->district->division->name ?? '-' }}</td>
                                    <td>{{ $postOffice->policeStation->district->name ?? '-' }}</td>
                                    <td>{{ $postOffice->policeStation->name ?? '-' }}</td>
                                    <td>{{ $postOffice->name }}</td>
                                    <td>{{ $postOffice->bn_name }}</td>
                                    <td>{{ $postOffice->postcode }}</td>
                                    <td>
                                        @if ($postOffice->status)
                                            <span class="badge bg-success">Active</span>
                                        @else
                                            <span class="badge bg-secondary">Inactive</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('post-office.edit', $postOffice->id) }}"
                                            class="btn btn-sm btn-dark" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('post-office.destroy', $postOffice->id) }}" method="POST"
                                            style="display:inline;">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger"
                                                onclick="return confirm('Are you sure you want to delete this post office?')"
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
                    {{ $postOffices->links('vendor.pagination.tailwind') }}
                </div>
            </div>
        </div>
    </div>
@endsection
