<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label for="name_en">Name (English) <span class="text-danger">*</span></label>
            <input
                type="text"
                id="name_en"
                name="name_en"
                class="form-control @error('name_en') is-invalid @enderror"
                value="{{ old('name_en', $classroom->name_en ?? '') }}"
                required
            >
            @error('name_en')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
    </div>

    <div class="col-md-6">
        <div class="form-group">
            <label for="name_bn">Name (Bangla) <span class="text-danger">*</span></label>
            <input
                type="text"
                id="name_bn"
                name="name_bn"
                class="form-control @error('name_bn') is-invalid @enderror"
                value="{{ old('name_bn', $classroom->name_bn ?? '') }}"
                required
            >
            @error('name_bn')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-4">
        <div class="form-group">
            <label for="capacity">Capacity</label>
            <input
                type="number"
                id="capacity"
                name="capacity"
                class="form-control @error('capacity') is-invalid @enderror"
                value="{{ old('capacity', $classroom->capacity ?? '') }}"
                min="0"
                placeholder="Optional"
            >
            @error('capacity')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
    </div>

    <div class="col-md-8">
        <div class="form-group">
            <label for="location">Location</label>
            <input
                type="text"
                id="location"
                name="location"
                class="form-control @error('location') is-invalid @enderror"
                value="{{ old('location', $classroom->location ?? '') }}"
                placeholder="Building / floor / note"
            >
            @error('location')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
    </div>
</div>
