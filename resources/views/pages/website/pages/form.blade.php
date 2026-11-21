@csrf
<div class="card-body row">
    <div class="form-group col-md-6">
        <label for="pages-form-title">Title</label>
        <input id="pages-form-title" type="text" name="title" value="{{ old('title', $page->title ?? '') }}" class="form-control" required>
    </div>
    <div class="form-group col-md-6">
        <label for="pages-form-slug">Slug</label>
        <input id="pages-form-slug" type="text" name="slug" value="{{ old('slug', $page->slug ?? '') }}" class="form-control" placeholder="about-us">
    </div>
    <div class="form-group col-md-4">
        <label for="pages-form-status">Status</label>
        <select id="pages-form-status" name="status" class="form-control">
            <option value="draft" @selected(old('status', $page->status ?? 'draft') === 'draft')>Draft</option>
            <option value="published" @selected(old('status', $page->status ?? '') === 'published')>Published</option>
        </select>
    </div>
    <div class="form-group col-md-8">
        <label for="pages-form-published-at">Publish At</label>
        <input id="pages-form-published-at" type="datetime-local" name="published_at" value="{{ old('published_at', isset($page) && $page->published_at ? $page->published_at->format('Y-m-d\\TH:i') : '') }}" class="form-control">
    </div>
    <div class="form-group col-12">
        <label for="pages-form-excerpt">Excerpt</label>
        <textarea id="pages-form-excerpt" name="excerpt" rows="2" class="form-control">{{ old('excerpt', $page->excerpt ?? '') }}</textarea>
    </div>
    <div class="form-group col-12">
        <label for="pages-form-content">Content</label>
        <textarea id="pages-form-content" name="content" rows="10" class="form-control">{{ old('content', $page->content ?? '') }}</textarea>
    </div>
</div>
<div class="card-footer">
    <button type="submit" class="btn btn-primary">Save</button>
    <a href="{{ route('website.pages.index') }}" class="btn btn-secondary">Cancel</a>
</div>
