@extends('layouts.master')

@section('contents')
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header text-white rounded-top d-flex justify-content-between align-items-center shadow p-3">
                        <h3 class="card-title">Add Mobile Banking Account</h3>
                    </div>

                    <form method="POST" action="{{ route('mobile-banking-accounts.store') }}">
                        @csrf

                        <div class="card-body">

                            <div class="form-group">
                                <label>Provider</label>
                                <select name="provider" class="form-control @error('provider') is-invalid @enderror" required>
                                    <option value="">Select Provider</option>
                                    @foreach ($providers as $provider)
                                        <option value="{{ $provider }}" {{ old('provider') == $provider ? 'selected' : '' }}>
                                            {{ $provider }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('provider') <span class="invalid-feedback">{{ $message }}</span> @enderror
                            </div>

                            <div class="form-group">
                                <label>Account Name</label>
                                <input type="text" name="account_name" class="form-control @error('account_name') is-invalid @enderror"
                                       value="{{ old('account_name') }}" required>
                                @error('account_name') <span class="invalid-feedback">{{ $message }}</span> @enderror
                            </div>

                            <div class="form-group">
                                <label>Account Number</label>
                                <input type="text" name="account_number" class="form-control @error('account_number') is-invalid @enderror"
                                       value="{{ old('account_number') }}" required>
                                @error('account_number') <span class="invalid-feedback">{{ $message }}</span> @enderror
                            </div>

                            <div class="form-group">
                                <label>Account Type</label>
                                <select name="account_type" class="form-control" required>
                                    @foreach (['Personal', 'Agent', 'Merchant'] as $type)
                                        <option value="{{ $type }}" {{ old('account_type') == $type ? 'selected' : '' }}>
                                            {{ $type }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group">
                                <label>Opening Balance (BDT)</label>
                                <input type="number" name="opening_balance" step="0.01" min="0"
                                       class="form-control @error('opening_balance') is-invalid @enderror"
                                       value="{{ old('opening_balance', 0) }}" required>
                                @error('opening_balance') <span class="invalid-feedback">{{ $message }}</span> @enderror
                            </div>

                            <div class="form-group">
                                <label>Opening Date</label>
                                <input type="text" name="opening_date" datepicker datepicker-format="dd/mm/yyyy"
                                       class="form-control @error('opening_date') is-invalid @enderror"
                                       value="{{ old('opening_date') }}" placeholder="dd/mm/yyyy" autocomplete="off" required>
                                @error('opening_date') <span class="invalid-feedback">{{ $message }}</span> @enderror
                            </div>

                            <div class="form-group">
                                <label>Notes <small class="text-muted">(optional)</small></label>
                                <textarea name="notes" class="form-control" rows="3">{{ old('notes') }}</textarea>
                            </div>

                        </div>

                        <div class="card-footer">
                            <button class="btn btn-success">Save</button>
                            <a href="{{ route('mobile-banking-accounts.index') }}" class="btn btn-secondary">Back</a>
                        </div>
                    </form>
                </div>
            </div>

            <div class="col-md-8">
                @include('pages.mobile-banking-accounts.table')
            </div>
        </div>
    </div>
@endsection