<div class="card">
    <div class="card-header text-white rounded-top d-flex justify-content-between align-items-center shadow p-3">
        <h3 class="card-title">Groups</h3>
        <a href="{{ route('groups.create') }}" class="btn btn-primary btn-sm ml-auto text-bold">
            + Add Group
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
                        <th>Status</th>
                        <th width="160">Action</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach ($groups as $group)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $group->name_en }}</td>
                            <td>{{ $group->name_bn }}</td>
                            <td>
                                <form action="{{ route('groups.toggle-status', $group->id) }}" method="POST">
                                    @csrf
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input"
                                            id="statusSwitch{{ $group->id }}" onchange="this.form.submit()"
                                            {{ $group->status ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="statusSwitch{{ $group->id }}">
                                        </label>
                                    </div>
                                </form>
                            </td>
                            <td style="display: flex; justify-content: center; align-items: self-start; gap: 5px;">
                                <a href="{{ route('groups.edit', $group->id) }}" class="btn btn-sm btn-dark">
                                    <i class="fas fa-edit"></i>
                                </a>

                                <form action="{{ route('groups.delete', $group->id) }}" method="POST"
                                    class="btn btn-sm btn-danger d-inline m-0"
                                    onsubmit="return confirm('Delete this group?')">
                                    @csrf
                                    @method('DELETE')
                                    <i class="fas fa-trash"></i>
                                </form>
                            </td>
                        </tr>
                    @endforeach

                    @if ($groups->isEmpty())
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">
                                No Group found
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>
