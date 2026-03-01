<div class="card">
    <div class="card-header text-white rounded-top d-flex justify-content-between align-items-center shadow p-3"
       >
        <h3 class="card-title mb-0">Sections</h3>
        <a href="{{ route('sections.create') }}" class="btn btn-primary btn-sm ml-auto text-bold">
            + Add Section
        </a>
    </div>

    <div class="card-body px-0 pb-4 pt-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Class</th>
                        <th>Name (EN)</th>
                        <th>Name (BN)</th>
                        <th>Status</th>
                        <th width="180">Action</th>
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
                                <form action="{{ route('sections.toggle-status', $section->id) }}" method="POST">
                                    @csrf
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input"
                                            id="statusSwitch{{ $section->id }}" onchange="this.form.submit()"
                                            {{ $section->status ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="statusSwitch{{ $section->id }}">
                                        </label>
                                    </div>
                                </form>

                            </td>
                            <td style="display: flex; justify-content: center; align-items: self-start; gap: 5px;">
                                <a href="{{ route('sections.edit', $section->id) }}" class="btn btn-sm btn-dark">
                                    <i class="fas fa-edit"></i>
                                </a>

                                <form action="{{ route('sections.delete', $section->id) }}" method="POST"
                                    class="btn btn-sm btn-danger d-inline"
                                    onsubmit="return confirm('Delete this section?')">
                                    @csrf
                                    @method('DELETE')
                                    <i class="fas fa-trash"></i>
                                </form>
                            </td>
                        </tr>
                    @endforeach

                    @if ($sections->isEmpty())
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">
                                No sections found
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>
