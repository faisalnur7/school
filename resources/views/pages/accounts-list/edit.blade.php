@extends('layouts.master')

@section('contents')
<div class="container-fluid px-3 py-3">
    <div class="card shadow-sm border-0">
        <div class="card-header bg-gradient-primary text-white py-3">
            <div class="d-flex justify-content-between align-items-center">
                <h4 class="card-title mb-0 font-weight-bold text-white">
                    <i class="fas fa-edit mr-2"></i>Edit Account
                </h4>
                <a href="{{ route('accounts-list.index') }}" class="btn btn-light btn-sm">
                    <i class="fas fa-arrow-left mr-1"></i> Back
                </a>
            </div>
        </div>

        <form method="POST" action="{{ route('accounts-list.update', $accountsList->id) }}" id="modernForm">
            @csrf
            @method('PUT')

            <div class="card-body p-3">
                @if($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show border-0 mb-3" role="alert">
                        <i class="fas fa-exclamation-circle mr-2"></i>
                        <strong>Errors:</strong>
                        <ul class="mb-0 mt-1 ml-4">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

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

            <div class="card-footer bg-light border-top py-2 px-3">
                <div class="d-flex justify-content-between gap-2">
                    <a href="{{ route('accounts-list.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-times mr-1"></i>Cancel
                    </a>
                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="fas fa-save mr-1"></i>Update
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@section('styles')
@include('components.form-styles')
@endsection

@section('scripts')
<script>
    $(function () {
        if ($('.is-invalid').length > 0) {
            $('html, body').animate({
                scrollTop: $('.is-invalid').first().offset().top - 50
            }, 300);
        }
    });
</script>
@endsection
