<div class="card">
    <div class="card-header text-white rounded-top d-flex justify-content-between align-items-center shadow p-3">
        <h3 class="card-title mb-0">Hand Cash</h3>
        <a href="{{ route('hand-cash.create') }}" class="btn btn-primary btn-sm ml-auto text-bold">
            + Add Hand Cash
        </a>
    </div>

    <div class="card-body px-0 pb-4 pt-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Label</th>
                        <th>Opening Amount</th>
                        <th>Opening Date</th>
                        <th>Recorded By</th>
                        <th>Status</th>
                        <th>Notes</th>
                        <th width="120">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($handCashes as $cash)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td class="fw-semibold">{{ $cash->label }}</td>
                            <td class="mono fw-bold" style="color:#4338ca">{{ number_format($cash->opening_amount, 2) }}</td>
                            <td class="mono" style="font-size:13px">{{ $cash->opening_date->format('d/m/Y') }}</td>
                            <td class="text-muted" style="font-size:13px">{{ $cash->recorder->name ?? '—' }}</td>
                            <td>
                                <span class="badge badge-{{ $cash->is_active ? 'success' : 'secondary' }}">
                                    {{ $cash->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td class="text-muted" style="font-size:13px">{{ Str::limit($cash->notes, 40) ?? '—' }}</td>
                            <td style="display:flex;gap:5px;align-items:center">
                                <a href="{{ route('hand-cash.edit', $cash->id) }}" class="btn btn-sm btn-dark">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('hand-cash.destroy', $cash->id) }}" method="POST"
                                      class="btn btn-sm btn-danger d-inline"
                                      onsubmit="return confirm('Delete this hand cash entry?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" style="background:none;border:none;padding:0;color:inherit">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach

                    @if ($handCashes->isEmpty())
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">No hand cash entries found</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
        <div class="px-3 pt-3">{{ $handCashes->links() }}</div>
    </div>
</div>