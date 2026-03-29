@extends('layouts.master')

@section('contents')
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-4">
                <div class="card card-primary">
                    <div class="card-header text-white rounded-top d-flex justify-content-between align-items-center shadow p-3">
                        <h3 class="card-title">Edit Expense</h3>
                    </div>

                    <form method="POST" action="{{ route('expenses.update', $expense->id) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="card-body">

                            <div class="form-group">
                                <label>Category</label>
                                <select name="expense_category_id" class="form-control" required>
                                    <option value="">Select Category</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}"
                                            {{ old('expense_category_id', $expense->expense_category_id) == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group">
                                <label>Title</label>
                                <input type="text" name="title" class="form-control @error('title') is-invalid @enderror"
                                       value="{{ old('title', $expense->title) }}" required>
                                @error('title') <span class="invalid-feedback">{{ $message }}</span> @enderror
                            </div>

                            <div class="form-group">
                                <label>Amount (BDT)</label>
                                <input type="number" name="amount" step="0.01" min="0"
                                       class="form-control @error('amount') is-invalid @enderror"
                                       value="{{ old('amount', $expense->amount) }}" required>
                                @error('amount') <span class="invalid-feedback">{{ $message }}</span> @enderror
                            </div>

                            <div class="form-group">
                                <label>Expense Date</label>
                                <input type="text" name="expense_date" datepicker datepicker-format="dd/mm/yyyy"
                                       class="form-control @error('expense_date') is-invalid @enderror"
                                       value="{{ old('expense_date', $expense->expense_date->format('d/m/Y')) }}"
                                       placeholder="dd/mm/yyyy" autocomplete="off" required>
                                @error('expense_date') <span class="invalid-feedback">{{ $message }}</span> @enderror
                            </div>

                            <div class="form-group">
                                <label>Payment Method</label>
                                <select name="payment_method" class="form-control" required>
                                    @foreach (['Cash', 'Bank Transfer', 'Cheque', 'Mobile Banking', 'Other'] as $method)
                                        <option value="{{ $method }}"
                                            {{ old('payment_method', $expense->payment_method) == $method ? 'selected' : '' }}>
                                            {{ $method }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group">
                                <label>Reference No <small class="text-muted">(optional)</small></label>
                                <input type="text" name="reference_no" class="form-control"
                                       value="{{ old('reference_no', $expense->reference_no) }}">
                            </div>

                            <div class="form-group">
                                <label>Description <small class="text-muted">(optional)</small></label>
                                <textarea name="description" class="form-control" rows="3">{{ old('description', $expense->description) }}</textarea>
                            </div>

                            <div class="form-group">
                                <label>Attachment</label>
                                @if ($expense->attachment)
                                    <div class="mb-2">
                                        <a href="{{ asset('storage/' . $expense->attachment) }}" target="_blank"
                                           class="btn btn-sm btn-outline-secondary">
                                            <i class="fas fa-paperclip"></i> View Current
                                        </a>
                                    </div>
                                @endif
                                <input type="file" name="attachment" class="form-control @error('attachment') is-invalid @enderror"
                                       accept=".jpg,.jpeg,.png,.pdf">
                                <small class="text-muted">Leave empty to keep existing attachment</small>
                                @error('attachment') <span class="invalid-feedback">{{ $message }}</span> @enderror
                            </div>

                        </div>

                        <div class="card-footer">
                            <button class="btn btn-success">Update</button>
                            <a href="{{ route('expenses.index') }}" class="btn btn-secondary">Back</a>
                        </div>
                    </form>
                </div>
            </div>

            <div class="col-md-8">
                @include('pages.expenses.table')
            </div>
        </div>
    </div>
@endsection