@csrf
<div class="card-body row">
    <div class="form-group col-md-6">
        <label for="gallery-form-title">Title</label>
        <input id="gallery-form-title" type="text" name="title" class="form-control" value="{{ old('title', $item->title ?? '') }}" required>
    </div>
    <div class="form-group col-md-6">
        <label for="gallery-form-sort-order">Sort Order</label>
        <input id="gallery-form-sort-order" type="number" name="sort_order" class="form-control" min="0" value="{{ old('sort_order', $item->sort_order ?? 0) }}">
    </div>
    <div class="form-group col-12">
        <label for="gallery-form-caption">Caption</label>
        <textarea id="gallery-form-caption" name="caption" rows="3" class="form-control">{{ old('caption', $item->caption ?? '') }}</textarea>
    </div>
    <div class="form-group col-md-6">
        <label for="gallery-form-image">Image</label>
        <input id="gallery-form-image" type="file" name="image" class="form-control-file" accept="image/*" {{ isset($item) ? '' : 'required' }}>
    </div>
    <div class="form-group col-md-6 d-flex align-items-end">
        <div class="custom-control custom-checkbox">
            <input type="checkbox" class="custom-control-input" id="is_active" name="is_active" value="1" @checked(old('is_active', $item->is_active ?? true))>
            <label class="custom-control-label" for="is_active">Active</label>
        </div>
    </div>
</div>
<div class="card-footer d-flex justify-content-between">
    <a href="{{ route('website.gallery.index') }}" class="btn btn-light border">Cancel</a>
    <button class="btn btn-primary">Save</button>
</div>
