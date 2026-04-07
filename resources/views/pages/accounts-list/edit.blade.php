@extends('layouts.master')
@section('contents')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header shadow p-3"><h3 class="card-title">Edit Account</h3></div>
                <form method="POST" action="{{ route('accounts-list.update', $accountsList->id) }}">
                    @csrf @method('PUT')
                    <div class="card-body">
                        <div class="form-group">
                            <label>Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" value="{{ old('name', $accountsList->name) }}" required>
                        </div>
                        <div class="form-group">
                            <label>Account Group</label>
                            <select name="account_group_id" class="form-control">
                                <option value="">— None —</option>
                                @foreach($groups as $g)
                                    <option value="{{ $g->id }}" {{ old('account_group_id', $accountsList->account_group_id) == $g->id ? 'selected' : '' }}>{{ $g->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Link to Physical Account</label>
                            <select name="reference_type" id="refType" class="form-control">
                                <option value="">— None —</option>
                                <option value="App\Models\BankAccount" {{ old('reference_type', $accountsList->reference_type) === 'App\Models\BankAccount' ? 'selected' : '' }}>Bank Account</option>
                                <option value="App\Models\HandCash" {{ old('reference_type', $accountsList->reference_type) === 'App\Models\HandCash' ? 'selected' : '' }}>Hand Cash</option>
                                <option value="App\Models\MobileBankingAccount" {{ old('reference_type', $accountsList->reference_type) === 'App\Models\MobileBankingAccount' ? 'selected' : '' }}>Mobile Banking</option>
                            </select>
                        </div>
                        <div class="form-group" id="refIdWrapper">
                            <label>Select Account</label>
                            <select name="reference_id" class="form-control">
                                <option value="">Select</option>
                                @foreach($bankAccounts as $b)
                                    <option value="{{ $b->id }}" data-type="App\Models\BankAccount" {{ old('reference_id', $accountsList->reference_id) == $b->id ? 'selected' : '' }}>{{ $b->bank_name }} — {{ $b->account_number }}</option>
                                @endforeach
                                @foreach($handCashes as $h)
                                    <option value="{{ $h->id }}" data-type="App\Models\HandCash" {{ old('reference_id', $accountsList->reference_id) == $h->id ? 'selected' : '' }}>{{ $h->label }}</option>
                                @endforeach
                                @foreach($mobileAccounts as $m)
                                    <option value="{{ $m->id }}" data-type="App\Models\MobileBankingAccount" {{ old('reference_id', $accountsList->reference_id) == $m->id ? 'selected' : '' }}>{{ $m->provider }} — {{ $m->account_number }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Notes</label>
                            <textarea name="notes" class="form-control" rows="2">{{ old('notes', $accountsList->notes) }}</textarea>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button class="btn btn-success">Update</button>
                        <a href="{{ route('accounts-list.index') }}" class="btn btn-secondary">Back</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
