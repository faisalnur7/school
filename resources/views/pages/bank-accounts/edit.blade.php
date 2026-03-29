@extends('layouts.master')

@section('contents')
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-4">
                <div class="card card-primary">
                    <div class="card-header text-white rounded-top d-flex justify-content-between align-items-center shadow p-3">
                        <h3 class="card-title">Edit Bank Account</h3>
                    </div>

                    <form method="POST" action="{{ route('bank-accounts.update', $bankAccount->id) }}">
                        @csrf
                        @method('PUT')

                        <div class="card-body">

                            <div class="form-group">
                                <label>Bank Name</label>
                                <input type="text" name="bank_name" class="form-control @error('bank_name') is-invalid @enderror"
                                       value="{{ old('bank_name', $bankAccount->bank_name) }}" required>
                                @error('bank_name') <span class="invalid-feedback">{{ $message }}</span> @enderror
                            </div>

                            <div class="form-group">
                                <label>Account Name</label>
                                <input type="text" name="account_name" class="form-control @error('account_name') is-invalid @enderror"
                                       value="{{ old('account_name', $bankAccount->account_name) }}" required>
                                @error('account_name') <span class="invalid-feedback">{{ $message }}</span> @enderror
                            </div>

                            <div class="form-group">
                                <label>Account Number</label>
                                <input type="text" name="account_number" class="form-control @error('account_number') is-invalid @enderror"
                                       value="{{ old('account_number', $bankAccount->account_number) }}" required>
                                @error('account_number') <span class="invalid-feedback">{{ $message }}</span> @enderror
                            </div>

                            <div class="form-group">
                                <label>Branch Name <small class="text-muted">(optional)</small></label>
                                <input type="text" name="branch_name" class="form-control"
                                       value="{{ old('branch_name', $bankAccount->branch_name) }}">
                            </div>

                            <div class="form-group">
                                <label>Routing Number <small class="text-muted">(optional)</small></label>
                                <input type="text" name="routing_number" class="form-control"
                                       value="{{ old('routing_number', $bankAccount->routing_number) }}">
                            </div>

                            <div class="form-group">
                                <label>Opening Balance (BDT)</label>
                                <input type="number" name="opening_balance" step="0.01" min="0"
                                       class="form-control @error('opening_balance') is-invalid @enderror"
                                       value="{{ old('opening_balance', $bankAccount->opening_balance) }}" required>
                                @error('opening_balance') <span class="invalid-feedback">{{ $message }}</span> @enderror
                            </div>

                            <div class="form-group">
                                <label>Opening Date</label>
                                <input type="text" name="opening_date" datepicker datepicker-format="dd/mm/yyyy"
                                       class="form-control @error('opening_date') is-invalid @enderror"
                                       value="{{ old('opening_date', $bankAccount->opening_date->format('d/m/Y')) }}"
                                       placeholder="dd/mm/yyyy" autocomplete="off" required>
                                @error('opening_date') <span class="invalid-feedback">{{ $message }}</span> @enderror
                            </div>

                            <div class="form-group">
                                <label>Notes <small class="text-muted">(optional)</small></label>
                                <textarea name="notes" class="form-control" rows="3">{{ old('notes', $bankAccount->notes) }}</textarea>
                            </div>

                            <div class="form-group">
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input" id="is_active"
                                           name="is_active" value="1"
                                           {{ old('is_active', $bankAccount->is_active) ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="is_active">Active</label>
                                </div>
                            </div>

                        </div>

                        <div class="card-footer">
                            <button class="btn btn-success">Update</button>
                            <a href="{{ route('bank-accounts.index') }}" class="btn btn-secondary">Back</a>
                        </div>
                    </form>
                </div>
            </div>

            <div class="col-md-8">
                @include('pages.bank-accounts.table')
            </div>
        </div>
    </div>
@endsection