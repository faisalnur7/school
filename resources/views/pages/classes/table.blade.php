<div class="card">
    <div class="card-header text-white rounded-top d-flex justify-content-between align-items-center shadow p-3"
       >
        <h3 class="card-title">Classes</h3>
        @if(auth()->user()?->hasPermission('create_classes'))
            <a href="{{ route('classes.create') }}" class="btn btn-primary btn-sm ml-auto text-bold">
                + Add Class
            </a>
        @endif
    </div>

    <div class="card-body px-0 pb-4 pt-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 ds-table--stack">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Order</th>
                        <th>Name (EN)</th>
                        <th>Name (BN)</th>
                        <th class="text-center">Status</th>
                        <th width="180">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($classes as $class)
                        <tr>
                            <td class="ds-hide-sm">{{ $loop->iteration }}</td>
                            <td class="ds-hide-sm">{{ $class->order }}</td>
                            <td class="ds-stack-title">{{ $class->name_en }}</td>
                            <td data-label="{{ __('Name (BN)') }}">{{ $class->name_bn }}</td>
                            <td class="text-center" data-label="{{ __('Status') }}">
                                @if(auth()->user()?->hasPermission('edit_classes'))
                                    <form action="{{ route('classes.toggle-status', $class->id) }}" method="POST">
                                        @csrf
                                        <div class="custom-control custom-switch">
                                            <input type="checkbox" class="custom-control-input"
                                                id="statusSwitch{{ $class->id }}" onchange="this.form.submit()"
                                                {{ $class->status ? 'checked' : '' }}>
                                            <label class="custom-control-label"
                                                for="statusSwitch{{ $class->id }}"><span class="sr-only">{{ __('Active') }}</span></label>
                                        </div>
                                    </form>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td class="ds-stack-actions" style="display: flex; justify-content: center; align-items: self-start; gap: 5px;">
                                @if(auth()->user()?->hasPermission('edit_classes'))
                                    <a href="{{ route('classes.edit', $class->id) }}"
                                        class="btn btn-sm btn-dark" aria-label="{{ __('Edit') }}" title="{{ __('Edit') }}">
                                        <i class="fas fa-edit" aria-hidden="true"></i>
                                    </a>
                                @endif

                                @if(auth()->user()?->hasPermission('delete_classes'))
                                    <form action="{{ route('classes.delete', $class->id) }}" method="POST"
                                        class="d-inline m-0"
                                        data-confirm="{{ __('Delete class “:name”?', ['name' => $class->name_en]) }}" data-confirm-action="{{ __('Delete class') }}" data-confirm-cancel="{{ __('Keep class') }}">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" aria-label="{{ __('Delete') }}" title="{{ __('Delete') }}"><i class="fas fa-trash" aria-hidden="true"></i></button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @endforeach

                    @if($classes->isEmpty())
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">
                                No Class found
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>
