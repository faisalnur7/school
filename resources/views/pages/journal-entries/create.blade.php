@extends('layouts.master')

@section('contents')
<div class="container-fluid">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="card-title mb-0 text-white text-lg">New Journal Entry</h3>
            <a href="{{ route('journal-entries.index') }}" class="btn btn-secondary btn-sm">← Back</a>
        </div>
        <div class="card-body">

            @if($errors->any())
                <div class="alert alert-danger">{{ $errors->first() }}</div>
            @endif

            <form action="{{ route('journal-entries.store') }}" method="POST" id="jeForm">
                @csrf
                <div class="row mb-3">
                    <div class="col-md-4">
                        <label class="form-label">Date <span class="text-danger">*</span></label>
                        <input type="date" name="date" value="{{ old('date', date('Y-m-d')) }}" class="form-control" required>
                    </div>
                    <div class="col-md-8">
                        <label class="form-label">Description</label>
                        <input type="text" name="description" value="{{ old('description') }}" class="form-control" placeholder="Narration...">
                    </div>
                </div>

                <table class="table table-bordered" id="linesTable">
                    <thead class="thead-light">
                        <tr>
                            <th>Account</th>
                            <th width="180">Debit</th>
                            <th width="180">Credit</th>
                            <th width="200">Description</th>
                            <th width="40"></th>
                        </tr>
                    </thead>
                    <tbody id="linesBody">
                        <tr class="line-row">
                            <td>
                                <select name="lines[0][account_id]" class="form-control" required>
                                    <option value="">— Select Account —</option>
                                    @foreach($accounts as $acc)
                                        <option value="{{ $acc->id }}">{{ $acc->name }} ({{ $acc->group?->name }})</option>
                                    @endforeach
                                </select>
                            </td>
                            <td><input type="number" name="lines[0][debit]" value="0" step="0.01" min="0" class="form-control debit-input" oninput="updateTotals()"></td>
                            <td><input type="number" name="lines[0][credit]" value="0" step="0.01" min="0" class="form-control credit-input" oninput="updateTotals()"></td>
                            <td><input type="text" name="lines[0][description]" class="form-control"></td>
                            <td><button type="button" class="btn btn-sm btn-danger remove-line">×</button></td>
                        </tr>
                        <tr class="line-row">
                            <td>
                                <select name="lines[1][account_id]" class="form-control" required>
                                    <option value="">— Select Account —</option>
                                    @foreach($accounts as $acc)
                                        <option value="{{ $acc->id }}">{{ $acc->name }} ({{ $acc->group?->name }})</option>
                                    @endforeach
                                </select>
                            </td>
                            <td><input type="number" name="lines[1][debit]" value="0" step="0.01" min="0" class="form-control debit-input" oninput="updateTotals()"></td>
                            <td><input type="number" name="lines[1][credit]" value="0" step="0.01" min="0" class="form-control credit-input" oninput="updateTotals()"></td>
                            <td><input type="text" name="lines[1][description]" class="form-control"></td>
                            <td><button type="button" class="btn btn-sm btn-danger remove-line">×</button></td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th>Totals</th>
                            <th><span id="totalDebit" class="text-success font-weight-bold">0.00</span></th>
                            <th><span id="totalCredit" class="text-danger font-weight-bold">0.00</span></th>
                            <th colspan="2">
                                <span id="balanceStatus" class="badge badge-secondary">—</span>
                            </th>
                        </tr>
                    </tfoot>
                </table>

                <button type="button" id="addLine" class="btn btn-sm btn-outline-secondary mb-3">+ Add Line</button>

                <div>
                    <button type="submit" class="btn btn-primary" id="submitBtn">Post Journal Entry</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
let lineIndex = 2;
const accountOptions = `{!! $accounts->map(fn($a) => '<option value="'.$a->id.'">'.$a->name.' ('.$a->group?->name.')</option>')->implode('') !!}`;

document.getElementById('addLine').addEventListener('click', function () {
    const tbody = document.getElementById('linesBody');
    const row = document.createElement('tr');
    row.className = 'line-row';
    row.innerHTML = `
        <td><select name="lines[${lineIndex}][account_id]" class="form-control" required>
            <option value="">— Select Account —</option>${accountOptions}</select></td>
        <td><input type="number" name="lines[${lineIndex}][debit]" value="0" step="0.01" min="0" class="form-control debit-input" oninput="updateTotals()"></td>
        <td><input type="number" name="lines[${lineIndex}][credit]" value="0" step="0.01" min="0" class="form-control credit-input" oninput="updateTotals()"></td>
        <td><input type="text" name="lines[${lineIndex}][description]" class="form-control"></td>
        <td><button type="button" class="btn btn-sm btn-danger remove-line">×</button></td>`;
    tbody.appendChild(row);
    lineIndex++;
    bindRemove();
});

function bindRemove() {
    document.querySelectorAll('.remove-line').forEach(btn => {
        btn.onclick = function () {
            const rows = document.querySelectorAll('.line-row');
            if (rows.length > 2) { this.closest('tr').remove(); updateTotals(); }
        };
    });
}

function updateTotals() {
    let debit = 0, credit = 0;
    document.querySelectorAll('.debit-input').forEach(i => debit += parseFloat(i.value) || 0);
    document.querySelectorAll('.credit-input').forEach(i => credit += parseFloat(i.value) || 0);
    document.getElementById('totalDebit').textContent = debit.toFixed(2);
    document.getElementById('totalCredit').textContent = credit.toFixed(2);
    const balanced = Math.abs(debit - credit) < 0.001;
    const badge = document.getElementById('balanceStatus');
    badge.textContent = balanced ? '✓ Balanced' : '✗ Not Balanced';
    badge.className = 'badge ' + (balanced ? 'badge-success' : 'badge-danger');
    document.getElementById('submitBtn').disabled = !balanced;
}

bindRemove();
updateTotals();
</script>
@endsection
