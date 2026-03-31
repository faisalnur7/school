<div class="card">
    <div class="card-header text-white rounded-top d-flex justify-content-between align-items-center shadow p-3"
       >
        <h3 class="card-title">Classes</h3>
        <a href="{{ route('classes.create') }}" class="btn btn-primary btn-sm ml-auto text-bold">
            + Add Class
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
                    @foreach ($classes as $class)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $class->name_en }}</td>
                            <td>{{ $class->name_bn }}</td>
                            <td class="text-center">
                                <form action="{{ route('classes.toggle-status', $class->id) }}" method="POST">
                                    @csrf
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input"
                                            id="statusSwitch{{ $class->id }}" onchange="this.form.submit()"
                                            {{ $class->status ? 'checked' : '' }}>
                                        <label class="custom-control-label"
                                            for="statusSwitch{{ $class->id }}">
                                        </label>
                                    </div>
                                </form>
                            </td>
                            <td style="display: flex; justify-content: center; align-items: self-start; gap: 5px;">
                                <a href="{{ route('classes.edit', $class->id) }}"
                                    class="btn btn-sm btn-dark">
                                    <i class="fas fa-edit"></i>
                                </a>

                                <form action="{{ route('classes.delete', $class->id) }}" method="POST"
                                    class="btn btn-sm btn-danger d-inline m-0"
                                    onsubmit="return confirm('Delete this class?')">
                                    @csrf @method('DELETE')
                                    <i class="fas fa-trash"></i>
                                </form>
                            </td>
                        </tr>
                    @endforeach

                    @if($classes->isEmpty())
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">
                                No Class found
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>
