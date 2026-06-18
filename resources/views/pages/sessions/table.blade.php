<div class="card">
    <div class="card-header text-white rounded-top d-flex flex-column flex-sm-row justify-content-between align-items-stretch align-items-sm-center shadow p-3"
       >
        <h3 class="card-title">Academic Sessions</h3>
        <a href="{{ route('sessions.create') }}" class="btn btn-primary btn-sm ml-sm-auto text-bold w-100 w-sm-auto">
            + Add Session
        </a>
    </div>

    <div class="card-body px-0 pb-4 pt-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
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
                                <form action="{{ route('sessions.toggle-status', $session->id) }}" method="POST">
                                    @csrf
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input"
                                            id="statusSwitch{{ $session->id }}" onchange="this.form.submit()"
                                            {{ $session->status ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="statusSwitch{{ $session->id }}">
                                        </label>
                                    </div>
                                </form>
                            </td>
                            <td class="d-flex flex-column flex-sm-row justify-content-center align-items-stretch align-items-sm-start gap-1">
                                <a href="{{ route('sessions.edit', $session->id) }}"
                                    class="btn btn-sm btn-dark">
                                    <i class="fas fa-edit"></i>
                                </a>

                                {{-- <form action="{{ route('sessions.delete', $session->id) }}" method="POST"
                                    class="btn btn-sm btn-danger" style="display:inline;"
                                    onsubmit="return confirm('Delete this session?')">
                                    @csrf @method('DELETE')
                                    <i class="fas fa-trash"></i>
                                </form> --}}
                            </td>
                        </tr>
                    @endforeach

                    @if ($sessions->isEmpty())
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">
                                No Session found
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>
