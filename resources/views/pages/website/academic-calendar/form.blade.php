@csrf
<div class="card-body row">
    <div class="form-group col-md-8">
        <label>Title</label>
        <input name="title" value="{{ old('title', $item->title ?? '') }}" class="form-control" required>
    </div>
    <div class="form-group col-md-2">
        <label>Sort</label>
        <input type="number" min="0" name="sort_order" value="{{ old('sort_order', $item->sort_order ?? 0) }}" class="form-control">
    </div>
    <div class="form-group col-md-2 d-flex align-items-end">
        <div class="custom-control custom-checkbox">
            <input class="custom-control-input" type="checkbox" id="is_published" name="is_published" value="1" @checked(old('is_published', $item->is_published ?? false))>
            <label class="custom-control-label" for="is_published">Published</label>
        </div>
    </div>
    <div class="form-group col-md-6">
        <label>Start Date</label>
        <input type="date" name="start_date" value="{{ old('start_date', isset($item) && $item->start_date ? $item->start_date->format('Y-m-d') : '') }}" class="form-control" required>
    </div>
    <div class="form-group col-md-6">
        <label>End Date</label>
        <input type="date" name="end_date" value="{{ old('end_date', isset($item) && $item->end_date ? $item->end_date->format('Y-m-d') : '') }}" class="form-control">
    </div>
    <div class="form-group col-12">
        <label>Description</label>
        <textarea name="description" rows="6" class="form-control">{{ old('description', $item->description ?? '') }}</textarea>
    </div>
</div>
<div class="card-footer">
    <button class="btn btn-primary">Save</button>
    <a href="{{ route('website.academic-calendar.index') }}" class="btn btn-secondary">Cancel</a>
</div>
