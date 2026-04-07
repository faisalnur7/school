@extends('layouts.master')
@section('contents')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header shadow p-3"><h3 class="card-title">Add Account</h3></div>
                <form method="POST" action="{{ route('accounts-list.store') }}">
                    @csrf
                    <div class="card-body">
                        <div class="form-group">
                            <label>Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
                            @error('name')<span class="invalid-feedback">{{ $message }}</span>@enderror
                        </div>
                        <div class="form-group">
                            <label>Account Group</label>
                            <select name="account_group_id" class="form-control">
                                <option value="">— None —</option>
                                @foreach($groups as $g)
                                    <option value="{{ $g->id }}" {{ old('account_group_id') == $g->id ? 'selected' : '' }}>{{ $g->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Link to Physical Account <small class="text-muted">(optional)</small></label>
                            <select name="reference_type" id="refType" class="form-control">
                                <option value="">— None —</option>
                                <option value="App\Models\BankAccount" {{ old('reference_type') === 'App\Models\BankAccount' ? 'selected' : '' }}>Bank Account</option>
                                <option value="App\Models\HandCash" {{ old('reference_type') === 'App\Models\HandCash' ? 'selected' : '' }}>Hand Cash</option>
                                <option value="App\Models\MobileBankingAccount" {{ old('reference_type') === 'App\Models\MobileBankingAccount' ? 'selected' : '' }}>Mobile Banking</option>
                            </select>
                        </div>
                        <div class="form-group" id="refIdWrapper" style="{{ old('reference_type') ? '' : 'display:none' }}">
                            <label>Select Account</label>
                            <select name="reference_id" class="form-control">
                                <option value="">Select</option>
                                @foreach($bankAccounts as $b)
                                    <option value="{{ $b->id }}" data-type="App\Models\BankAccount" {{ old('reference_id') == $b->id ? 'selected' : '' }}>{{ $b->bank_name }} — {{ $b->account_number }}</option>
                                @endforeach
                                @foreach($handCashes as $h)
                                    <option value="{{ $h->id }}" data-type="App\Models\HandCash" {{ old('reference_id') == $h->id ? 'selected' : '' }}>{{ $h->label }}</option>
                                @endforeach
                                @foreach($mobileAccounts as $m)
                                    <option value="{{ $m->id }}" data-type="App\Models\MobileBankingAccount" {{ old('reference_id') == $m->id ? 'selected' : '' }}>{{ $m->provider }} — {{ $m->account_number }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Notes</label>
                            <textarea name="notes" class="form-control" rows="2">{{ old('notes') }}</textarea>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button class="btn btn-success">Save</button>
                        <a href="{{ route('accounts-list.index') }}" class="btn btn-secondary">Back</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@section('scripts')
<script>
$('#refType').on('change', function() {
    const type = $(this).val();
    const $wrapper = $('#refIdWrapper');
    const $opts = $('select[name=reference_id] option');
    if (!type) { $wrapper.hide(); return; }
    $opts.each(function() {
        const t = $(this).data('type');
        $(this).toggle(!t || t === type);
    });
    $wrapper.show();
});
</script>
@endsection
@endsection
