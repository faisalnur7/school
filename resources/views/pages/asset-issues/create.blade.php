@extends('layouts.master')
@section('contents')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header shadow p-3"><h3 class="card-title">Issue Asset</h3></div>
                <form method="POST" action="{{ route('asset-issues.store') }}">
                    @csrf
                    <div class="card-body">
                        @if($errors->any())<div class="alert alert-danger">{{ $errors->first() }}</div>@endif

                        <div class="form-group">
                            <label>Asset <span class="text-danger">*</span></label>
                            <select name="asset_id" class="form-control @error('asset_id') is-invalid @enderror" required>
                                <option value="">Select Asset</option>
                                @foreach($assets as $a)
                                    <option value="{{ $a->id }}" {{ old('asset_id') == $a->id ? 'selected' : '' }}>
                                        {{ $a->name }} (Available: {{ $a->available_stock }})
                                    </option>
                                @endforeach
                            </select>
                            @error('asset_id')<span class="invalid-feedback">{{ $message }}</span>@enderror
                        </div>

                        <div class="form-group">
                            <label>Issued To <span class="text-danger">*</span></label>
                            <input type="text" name="issued_to" class="form-control @error('issued_to') is-invalid @enderror"
                                   value="{{ old('issued_to') }}" placeholder="Name / Department" required>
                            @error('issued_to')<span class="invalid-feedback">{{ $message }}</span>@enderror
                        </div>

                        <div class="form-group">
                            <label>Type <small class="text-muted">(optional)</small></label>
                            <select name="issued_to_type" class="form-control">
                                <option value="">— Select —</option>
                                @foreach(['Student','Teacher','Staff','Department','Other'] as $t)
                                    <option value="{{ $t }}" {{ old('issued_to_type') === $t ? 'selected' : '' }}>{{ $t }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Quantity <span class="text-danger">*</span></label>
                            <input type="number" name="quantity" class="form-control @error('quantity') is-invalid @enderror"
                                   value="{{ old('quantity', 1) }}" min="1" required>
                            @error('quantity')<span class="invalid-feedback">{{ $message }}</span>@enderror
                        </div>

                        <div class="form-group">
                            <label>Issue Date <span class="text-danger">*</span></label>
                            <input type="text" name="issue_date" datepicker datepicker-format="dd/mm/yyyy"
                                   class="form-control @error('issue_date') is-invalid @enderror"
                                   value="{{ old('issue_date', now()->format('d/m/Y')) }}" placeholder="dd/mm/yyyy" autocomplete="off" required>
                            @error('issue_date')<span class="invalid-feedback">{{ $message }}</span>@enderror
                        </div>

                        <div class="form-group">
                            <label>Notes</label>
                            <textarea name="notes" class="form-control" rows="2">{{ old('notes') }}</textarea>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button class="btn btn-success">Issue</button>
                        <a href="{{ route('asset-issues.index') }}" class="btn btn-secondary">Back</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
