@csrf
<div class="card-body row">
    <div class="form-group col-md-6">
        <label>Section Title</label>
        <input type="text" name="title" value="{{ old('title', $section->title ?? '') }}" class="form-control" required>
    </div>
    <div class="form-group col-md-4">
        <label>Section Key</label>
        <input type="text" name="section_key" value="{{ old('section_key', $section->section_key ?? '') }}" class="form-control" placeholder="hero" required>
    </div>
    <div class="form-group col-md-2">
        <label>Sort</label>
        <input type="number" min="0" name="sort_order" value="{{ old('sort_order', $section->sort_order ?? 0) }}" class="form-control">
    </div>
    <div class="form-group col-12">
        <label>Content</label>
        <textarea name="content" rows="8" class="form-control">{{ old('content', $section->content ?? '') }}</textarea>
    </div>
</div>
<div class="card-footer">
    <button class="btn btn-primary">Save</button>
    <a href="{{ route('website.pages.sections.index', $page) }}" class="btn btn-secondary">Cancel</a>
</div>
