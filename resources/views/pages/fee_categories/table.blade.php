<div class="card">
    <div class="card-header text-white rounded-top d-flex justify-content-between align-items-center shadow p-3">
        <h3 class="card-title mb-0">Fee Categories</h3>
        <a href="{{ route('fee-categories.create') }}" class="btn btn-primary btn-sm ml-auto text-bold">
            + Add Fee Category
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
                        <th>Description</th>
                        <th>Status</th>
                        <th width="180">Action</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach ($feeCategories as $feeCategory)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $feeCategory->name }}</td>
                            <td>{{ $feeCategory->bn_name }}</td>
                            <td>
                                {{ Str::limit($feeCategory->description, 40) }}
                            </td>

                            <td>
                                <form action="{{ route('fee-categories.toggle-status', $feeCategory->id) }}"
                                    method="POST">
                                    @csrf
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input"
                                            id="statusSwitch{{ $feeCategory->id }}" onchange="this.form.submit()"
                                            {{ $feeCategory->status ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="statusSwitch{{ $feeCategory->id }}">
                                        </label>
                                    </div>
                                </form>
                            </td>

                            <td style="display: flex; justify-content: center; align-items: self-start; gap: 5px;">
                                <a href="{{ route('fee-categories.edit', $feeCategory->id) }}"
                                    class="btn btn-sm btn-dark">
                                    <i class="fas fa-edit"></i>
                                </a>

                                <form action="{{ route('fee-categories.delete', $feeCategory->id) }}" method="POST"
                                    class="btn btn-sm btn-danger d-inline m-0"
                                    onsubmit="return confirm('Delete this fee category?')">
                                    @csrf
                                    @method('DELETE')
                                    <i class="fas fa-trash"></i>
                                </form>
                            </td>
                        </tr>
                    @endforeach

                    @if ($feeCategories->isEmpty())
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">
                                No fee categories found
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>
