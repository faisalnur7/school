@csrf
<div class="card-body row">
    <div class="form-group col-12">
        <label for="notices-form-title">Title</label>
        <input id="notices-form-title" type="text" name="title" value="{{ old('title', $notice->title ?? '') }}" class="form-control" required>
    </div>
    <div class="form-group col-12">
        <label for="notices-form-content">Content</label>
        <textarea id="notices-form-content" name="content" rows="8" class="form-control">{{ old('content', $notice->content ?? '') }}</textarea>
    </div>
    <div class="form-group col-md-4">
        <label for="notices-form-published-at">Publish At</label>
        <input id="notices-form-published-at" type="datetime-local" name="published_at" value="{{ old('published_at', isset($notice) && $notice->published_at ? $notice->published_at->format('Y-m-d\\TH:i') : '') }}" class="form-control">
    </div>
    <div class="form-group col-md-4 d-flex align-items-end">
        <div class="custom-control custom-switch">
            <input type="checkbox" class="custom-control-input" id="is_published" name="is_published" value="1" @checked(old('is_published', $notice->is_published ?? true))>
            <label class="custom-control-label" for="is_published">Published</label>
        </div>
    </div>
</div>
<div class="card-footer">
    <button type="submit" class="btn btn-primary">Save</button>
    <a href="{{ route('notice.index') }}" class="btn btn-secondary">Cancel</a>
</div>
