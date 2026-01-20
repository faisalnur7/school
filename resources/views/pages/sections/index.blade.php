@extends('layouts.master')

@section('contents')
    <div class="container-fluid">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Sections</h3>
                <a href="{{ route('sections.create') }}" class="btn btn-primary float-right">
                    + Add Section
                </a>
            </div>

            <div class="card-body">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Class</th>
                            <th>Name (EN)</th>
                            <th>Name (BN)</th>
                            <th width="150">Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($sections as $section)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $section->schoolClass->name_en }}</td>
                                <td>{{ $section->name_en }}</td>
                                <td>{{ $section->name_bn }}</td>
                                <td>
                                    <a href="{{ route('sections.edit', $section->id) }}" class="btn btn-sm btn-info">Edit</a>

                                    <form action="{{ route('sections.delete', $section->id) }}" method="POST"
                                        class="d-inline" onsubmit="return confirm('Delete this section?')">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-danger">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
