<div class="card">
    <div class="card-header text-white rounded-top d-flex justify-content-between align-items-center shadow p-3">
        <h3 class="card-title mb-0 text-white text-lg">Fee Categories</h3>
        <a href="{{ route('fee-categories.create') }}" class="btn btn-primary btn-sm ml-auto text-bold">
            + Add Fee Category
        </a>
    </div>

    <div class="card-body px-0 pb-4 pt-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 ds-table--stack">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Name (EN)</th>
                        <th>Name (BN)</th>
                        <th>Description</th>
                        <th>Applicable For</th>
                        <th>Status</th>
                        <th width="180">Action</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach ($feeCategories as $feeCategory)
                        <tr>
                            <td class="ds-hide-sm">{{ $loop->iteration }}</td>
                            <td class="ds-stack-title">{{ $feeCategory->name }}</td>
                            <td data-label="{{ __('Name (BN)') }}">{{ $feeCategory->bn_name }}</td>
                            <td class="ds-hide-sm">
                                {{ Str::limit($feeCategory->description, 40) }}
                            </td>

                            <td data-label="{{ __('Applicable For') }}">
                                @php
                                    $badgeClass = match($feeCategory->student_type) {
                                        'new'  => 'badge-success',
                                        'old'  => 'badge-warning',
                                        default => 'badge-secondary',
                                    };
                                    $badgeLabel = match($feeCategory->student_type) {
                                        'new'  => 'New Only',
                                        'old'  => 'Returning Only',
                                        default => 'All Students',
                                    };
                                @endphp
                                <span class="badge {{ $badgeClass }}">{{ $badgeLabel }}</span>
                            </td>

                            <td data-label="{{ __('Status') }}">
                                <form action="{{ route('fee-categories.toggle-status', $feeCategory->id) }}"
                                    method="POST">
                                    @csrf
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input"
                                            id="statusSwitch{{ $feeCategory->id }}" onchange="this.form.submit()"
                                            {{ $feeCategory->status ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="statusSwitch{{ $feeCategory->id }}"><span class="sr-only">{{ __('Active') }}</span></label>
                                    </div>
                                </form>
                            </td>

                            <td class="ds-stack-actions" style="display: flex; justify-content: center; align-items: self-start; gap: 5px;">
                                <a href="{{ route('fee-categories.edit', $feeCategory->id) }}"
                                    class="btn btn-sm btn-dark" aria-label="{{ __('Edit') }}" title="{{ __('Edit') }}">
                                    <i class="fas fa-edit" aria-hidden="true"></i>
                                </a>

                                <form action="{{ route('fee-categories.delete', $feeCategory->id) }}" method="POST"
                                    class="d-inline m-0"
                                    data-confirm="{{ __('Delete fee category “:name”?', ['name' => $feeCategory->name]) }}" data-confirm-action="{{ __('Delete fee category') }}" data-confirm-cancel="{{ __('Keep fee category') }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" aria-label="{{ __('Delete') }}" title="{{ __('Delete') }}"><i class="fas fa-trash" aria-hidden="true"></i></button>
                                </form>
                            </td>
                        </tr>
                    @endforeach

                    @if ($feeCategories->isEmpty())
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">
                                No fee categories found
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>
