@php
    $showHeader = $showHeader ?? true;
@endphp

<div class="card">
    @if($showHeader)
        <div class="card-header text-white rounded-top d-flex justify-content-between align-items-center shadow p-3">
            <h3 class="card-title mb-0 text-white text-lg">Fee Sets</h3>
            <a href="{{ route('fee-sets.index') }}" class="btn btn-primary btn-sm ml-auto text-bold">
                + Add Fee Set
            </a>
        </div>
    @endif

    <div class="card-body px-0 pb-4 pt-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Class</th>
                        <th>Categories & Amounts</th>
                        <th>Name (EN)</th>
                        <th>Frequency</th>
                        <th>Description</th>
                        <th width="180">Action</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($feeSets as $feeSet)
                        <tr>
                            <td>{{ $loop->iteration }}</td>

                            <td>{{ $feeSet->schoolClass->name_en ?? 'All Classes' }}</td>

                            <td>
                                @if ($feeSet->items->isNotEmpty())
                                    <ul class="mb-0">
                                        @foreach ($feeSet->items as $item)
                                            @php
                                                $isTransport = ($item->category->is_transport ?? 0) || (($item->category->name ?? '') === 'Transport Fee');
                                            @endphp
                                            <li>
                                                {{ $item->category->name ?? '-' }}
                                                @unless($isTransport)
                                                    : {{ number_format($item->amount, 2) }}
                                                @endunless
                                            </li>
                                        @endforeach
                                    </ul>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>

                            <td>{{ $feeSet->name }}</td>

                            <td>
                                <span class="badge badge-info text-capitalize">
                                    {{ $feeSet->frequency }}
                                </span>
                            </td>

                            <td>{{ Str::limit($feeSet->description, 30) }}</td>

                            <td style="display: flex; justify-content: center; align-items: self-start; gap: 5px;">
                                <a href="{{ route('fee-sets.edit', $feeSet->id) }}" class="btn btn-sm btn-dark">
                                    <i class="fas fa-edit"></i>
                                </a>

                                <form action="{{ route('fee-sets.destroy', $feeSet->id) }}" method="POST"
                                      class="d-inline"
                                      onsubmit="return confirm('Delete this fee set?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">
                                No fee sets found
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
