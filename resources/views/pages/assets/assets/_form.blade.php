<div class="mb-3">
    <label class="form-label">Category *</label>
    <select name="asset_category_id" class="form-control" required>
        <option value="">Select Category</option>
        @foreach($categories as $cat)
            <option value="{{ $cat->id }}" {{ old('asset_category_id', $asset->asset_category_id ?? '') == $cat->id ? 'selected' : '' }}>
                {{ $cat->name }}
            </option>
        @endforeach
    </select>
</div>

<div class="mb-3">
    <label class="form-label">Name *</label>
    <input type="text" name="name" class="form-control" value="{{ old('name', $asset->name ?? '') }}" required>
</div>

<div class="mb-3">
    <label class="form-label">Description</label>
    <textarea name="description" class="form-control" rows="3">{{ old('description', $asset->description ?? '') }}</textarea>
</div>

<div class="mb-3">
    <label class="form-label">Status *</label>
    <select name="status" class="form-control" required>
        @foreach(['active', 'inactive', 'disposed'] as $s)
            <option value="{{ $s }}" {{ old('status', $asset->status ?? 'active') === $s ? 'selected' : '' }}>
                {{ ucfirst($s) }}
            </option>
        @endforeach
    </select>
</div>
