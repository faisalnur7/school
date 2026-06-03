@csrf
<div class="card-body row">
    <div class="form-group col-md-6">
        <label>Title</label>
        <input type="text" name="title" class="form-control" value="{{ old('title', $item->title ?? '') }}" required>
    </div>
    <div class="form-group col-md-6">
        <label>Sort Order</label>
        <input type="number" name="sort_order" class="form-control" min="0" value="{{ old('sort_order', $item->sort_order ?? 0) }}">
    </div>
    <div class="form-group col-12">
        <label>Caption</label>
        <textarea name="caption" rows="3" class="form-control">{{ old('caption', $item->caption ?? '') }}</textarea>
    </div>
    <div class="form-group col-md-6">
        <label>Image</label>
        <input type="file" name="image" class="form-control-file" accept="image/*" {{ isset($item) ? '' : 'required' }}>
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
