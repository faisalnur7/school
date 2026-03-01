@forelse($fees as $fee)
<tr>
    <td>{{ $fee->feeSet->name }}</td>
    <td>{{ number_format($fee->amount,2) }}</td>
    <td>
        <span class="badge badge-warning">Pending</span>
    </td>
    <td>
        <form method="POST" action="{{ route('fees.category.store') }}">
            @csrf
            <input type="hidden" name="fee_id" value="{{ $fee->id }}">
            <button class="btn btn-success btn-sm">
                Pay
            </button>
        </form>
    </td>
</tr>
@empty
<tr>
    <td colspan="4" class="text-center text-muted">
        No unpaid fees
    </td>
</tr>
@endforelse
