@php($qualification = $qualification ?? null)
<div class="row">
    <div class="col-md-7 form-group">
        <label class="font-weight-bold" for="qualificationName">Name <span class="text-danger">*</span></label>
        <input id="qualificationName" type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $qualification?->name) }}" placeholder="e.g. Bachelor of Education" required>
        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-5 form-group">
        <label class="font-weight-bold" for="qualificationAbbreviation">Abbreviation <span class="text-danger">*</span></label>
        <input id="qualificationAbbreviation" type="text" name="abbreviation" class="form-control @error('abbreviation') is-invalid @enderror" value="{{ old('abbreviation', $qualification?->abbreviation) }}" placeholder="e.g. B.Ed." required>
        @error('abbreviation')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-5 form-group">
        <label class="font-weight-bold" for="qualificationStatus">Status</label>
        <select id="qualificationStatus" name="status" class="form-control @error('status') is-invalid @enderror">
            <option value="active" {{ old('status', $qualification?->status ?? 'active') === 'active' ? 'selected' : '' }}>Active</option>
            <option value="inactive" {{ old('status', $qualification?->status) === 'inactive' ? 'selected' : '' }}>Inactive</option>
        </select>
        @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
</div>
