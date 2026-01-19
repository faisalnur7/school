@extends('layouts.master')

@section('contents')
    <div class="container-fluid">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Academic Sessions</h3>
                <a href="{{ route('sessions.create') }}" class="btn btn-primary float-right">
                    + Add Session
                </a>
            </div>

            <div class="card-body">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name (EN)</th>
                            <th>Name (BN)</th>
                            <th class="text-center">Status</th>
                            <th width="180">Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($sessions as $session)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $session->name_en }}</td>
                                <td>{{ $session->name_bn }}</td>
                                <td class="text-center">
                                    <form action="{{ route('sessions.toggle-status', $session->id) }}"
                                        method="POST">
                                        @csrf
                                        <div class="custom-control custom-switch">
                                            <input type="checkbox"
                                                class="custom-control-input"
                                                id="statusSwitch{{ $session->id }}"
                                                onchange="this.form.submit()"
                                                {{ $session->status ? 'checked' : '' }}>
                                            <label class="custom-control-label"
                                                for="statusSwitch{{ $session->id }}">
                                            </label>
                                        </div>
                                    </form>
                                </td>
                                <td>
                                    <a href="{{ route('sessions.edit', $session->id) }}"
                                        class="btn btn-sm btn-info">Edit</a>

                                    <form action="{{ route('sessions.delete', $session->id) }}"
                                        method="POST" class="d-inline"
                                        onsubmit="return confirm('Delete this session?')">
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
