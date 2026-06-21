@extends('layouts.master')

@section('contents')
<div class="container-fluid">
    <div class="bg-gradient-to-br from-red-600 to-red-800 rounded-2xl p-8 mb-6 flex items-center gap-5">
        <i class="fas fa-sign-out-alt text-white text-5xl opacity-80"></i>
        <div>
            <h3 class="text-white text-3xl font-bold m-0">Student Checkout</h3>
            <p class="text-red-200 text-sm mt-1 mb-0">Transfer, graduate, withdraw, or expel a student</p>
        </div>
    </div>

    @if(session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-xl mb-4 flex items-center gap-2">
        <i class="fas fa-check-circle"></i> {{ session('success') }}
    </div>
    @endif

    {{-- Filter --}}
    <div class="bg-white rounded-2xl shadow p-5 mb-5">
        <form method="GET" action="{{ route('students.checkout') }}">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                <div>
                    <label class="form-label text-sm font-medium text-slate-600">Session</label>
                    <select name="academic_session_id" class="form-control form-control-sm">
                        <option value="">Select Session</option>
                        @foreach($sessions as $s)
                        <option value="{{ $s->id }}" @selected(request('academic_session_id') == $s->id)>{{ $s->name_en }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="form-label text-sm font-medium text-slate-600">Class</label>
                    <select name="school_class_id" class="form-control form-control-sm">
                        <option value="">Select Class</option>
                        @foreach($classes as $c)
                        <option value="{{ $c->id }}" @selected(request('school_class_id') == $c->id)>{{ $c->name_en }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="form-label text-sm font-medium text-slate-600">Student ID</label>
                    <input
                        type="text"
                        name="student_cid"
                        value="{{ request('student_cid') }}"
                        class="form-control form-control-sm"
                        placeholder="Enter Student ID">
                </div>
                <div>
                    <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg w-full font-medium">
                        <i class="fas fa-search mr-1"></i> Load Students
                    </button>
                </div>
            </div>
        </form>
    </div>

    @if($students->isNotEmpty())
    <div class="space-y-4">
        @foreach($students as $rec)
        @php $hasDues = $rec->totalDue > 0; @endphp
        <div class="bg-white rounded-2xl shadow overflow-hidden border {{ $hasDues ? 'border-red-200' : 'border-transparent' }}">
            {{-- Student Row --}}
            <div class="flex items-center justify-between p-4 gap-4 flex-wrap">
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 rounded-full bg-slate-100 flex items-center justify-center text-slate-500 font-bold text-sm flex-shrink-0">
                        {{ strtoupper(substr($rec->student->full_name_en, 0, 2)) }}
                    </div>
                    <div>
                        <p class="font-semibold text-slate-800 mb-0">{{ $rec->student->full_name_en }}</p>
                        <p class="text-xs text-slate-400 mb-0">CID: {{ $rec->student->student_cid }} &bull; Roll: {{ $rec->roll }} &bull; Section: {{ $rec->section->name_en ?? '—' }}</p>
                    </div>
                </div>
                <div class="flex items-center gap-3 flex-wrap">
                    @if($hasDues)
                    <span class="inline-flex items-center gap-1 bg-red-100 text-red-700 text-xs font-semibold px-3 py-1 rounded-full">
                        <i class="fas fa-exclamation-triangle"></i>
                        Due: ৳{{ number_format($rec->totalDue, 2) }}
                    </span>
                    <a href="{{ route('fees.collect_payment', ['student_id' => $rec->student_id]) }}" class="btn btn-sm btn-warning">
                        <i class="fas fa-money-bill mr-1"></i> Pay Fees
                    </a>
                    @else
                    <span class="inline-flex items-center gap-1 bg-green-100 text-green-700 text-xs font-semibold px-3 py-1 rounded-full">
                        <i class="fas fa-check-circle"></i> No Dues
                    </span>
                    @endif
                    <button type="button"
                        class="btn btn-sm btn-danger"
                        onclick="toggleCheckout({{ $rec->id }})">
                        <i class="fas fa-sign-out-alt mr-1"></i> Checkout
                    </button>
                </div>
            </div>

            {{-- Pending Fees Detail --}}
            @if($hasDues)
            <div class="border-t border-red-100 bg-red-50 px-4 py-3">
                <p class="text-xs font-semibold text-red-700 mb-2"><i class="fas fa-list mr-1"></i> Pending Fees (must be cleared before checkout)</p>
                <div class="overflow-x-auto">
                    <table class="table table-sm mb-0 text-xs">
                        <thead>
                            <tr class="bg-red-100">
                                <th>Fee</th>
                                <th>Due Date</th>
                                <th class="text-right">Amount</th>
                                <th class="text-right">Paid</th>
                                <th class="text-right">Balance</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($rec->pendingFees as $fee)
                            @php $balance = max(0, $fee->amount - ($fee->scholarship_discount ?? 0) - ($fee->paid_amount ?? 0)); @endphp
                            <tr>
                                <td>{{ $fee->feeSet->name ?? '—' }}</td>
                                <td>{{ $fee->due_date ? $fee->due_date->format('d M Y') : '—' }}</td>
                                <td class="text-right">৳{{ number_format($fee->amount, 2) }}</td>
                                <td class="text-right">৳{{ number_format($fee->paid_amount ?? 0, 2) }}</td>
                                <td class="text-right font-semibold text-red-600">৳{{ number_format($balance, 2) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="bg-red-100 font-bold">
                                <td colspan="4" class="text-right">Total Due</td>
                                <td class="text-right text-red-700">৳{{ number_format($rec->totalDue, 2) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            @endif

            <div id="checkout-{{ $rec->id }}" class="hidden border-t border-slate-100 bg-slate-50 px-4 py-4">
                <form method="POST" action="{{ route('students.checkout.store', $rec->id) }}">
                    @csrf
                    @if($errors->any() && old('_rec_id') == $rec->id)
                    <div class="bg-red-100 border border-red-300 text-red-700 px-3 py-2 rounded mb-3 text-sm">
                        @foreach($errors->all() as $e)<p class="mb-0">{{ $e }}</p>@endforeach
                    </div>
                    @endif
                    <input type="hidden" name="_rec_id" value="{{ $rec->id }}">
                    @if($hasDues)
                    <div class="bg-yellow-50 border border-yellow-300 text-yellow-800 px-3 py-2 rounded mb-3 text-sm">
                        This student has pending dues (৳{{ number_format($rec->totalDue, 2) }}). To continue checkout now, enable Immediate Checkout below. Remaining unpaid fees will be unassigned.
                    </div>
                    @endif
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
                        <div>
                            <label class="form-label text-xs font-semibold">Checkout Type <span class="text-red-500">*</span></label>
                            <select name="checkout_type" class="form-control form-control-sm" required>
                                <option value="">Select Type</option>
                                <option value="transferred">Transferred</option>
                                <option value="graduated">Graduated</option>
                                <option value="withdrawn">Withdrawn</option>
                                <option value="expelled">Expelled</option>
                            </select>
                        </div>
                        <div>
                            <label class="form-label text-xs font-semibold">Checkout Date <span class="text-red-500">*</span></label>
                            <input type="date" name="checkout_date" value="{{ date('Y-m-d') }}" class="form-control form-control-sm" required>
                        </div>
                        <div>
                            <label class="form-label text-xs font-semibold">Notes</label>
                            <input type="text" name="notes" class="form-control form-control-sm" placeholder="Optional notes">
                        </div>
                    </div>
                    @if($hasDues)
                    <div class="mt-3">
                        <label class="inline-flex items-center gap-2 text-sm text-slate-700 cursor-pointer">
                            <input type="checkbox" name="immediate_checkout" value="1">
                            <span>Immediate Checkout (unassign all remaining unpaid fees)</span>
                        </label>
                    </div>
                    @endif
                    <div class="flex gap-2 mt-3">
                        <button type="submit" class="btn btn-sm btn-danger"
                            onclick="return confirm('Confirm checkout for {{ addslashes($rec->student->full_name_en) }}?')">
                            <i class="fas fa-check mr-1"></i> Confirm Checkout
                        </button>
                        <button type="button" class="btn btn-sm btn-secondary" onclick="toggleCheckout({{ $rec->id }})">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
        @endforeach
    </div>

    @elseif(request()->hasAny(['academic_session_id', 'school_class_id', 'student_cid']))
    <div class="bg-white rounded-2xl shadow p-8 text-center text-slate-400">
        <i class="fas fa-users text-4xl mb-3 opacity-40"></i>
        <p>No active students found for the selected filters.</p>
    </div>
    @endif
</div>
@endsection

@section('scripts')
<script>
function toggleCheckout(id) {
    const row = document.getElementById('checkout-' + id);
    row.classList.toggle('hidden');
}
</script>
@endsection
