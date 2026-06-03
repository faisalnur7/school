@csrf
<div class="card-body row">
    <div class="form-group col-12">
        <label>Title</label>
        <input type="text" name="title" value="{{ old('title', $event->title ?? '') }}" class="form-control" required>
    </div>
    <div class="form-group col-12">
        <label>Description</label>
        <textarea name="description" rows="8" class="form-control">{{ old('description', $event->description ?? '') }}</textarea>
    </div>
    <div class="form-group col-md-4">
        <label>Event Date</label>
        <input type="datetime-local" name="event_date" value="{{ old('event_date', isset($event) && $event->event_date ? $event->event_date->format('Y-m-d\\TH:i') : '') }}" class="form-control">
    </div>
    <div class="form-group col-md-4">
        <label>Location</label>
        <input type="text" name="location" value="{{ old('location', $event->location ?? '') }}" class="form-control" placeholder="School Auditorium">
    </div>
    <div class="form-group col-md-4">
        <label>Sort Order</label>
        <input type="number" min="0" name="sort_order" value="{{ old('sort_order', $event->sort_order ?? 0) }}" class="form-control">
    </div>
    <div class="form-group col-md-4">
        <label>Publish At</label>
        <input type="datetime-local" name="published_at" value="{{ old('published_at', isset($event) && $event->published_at ? $event->published_at->format('Y-m-d\\TH:i') : '') }}" class="form-control">
    </div>
    <div class="form-group col-md-4 d-flex align-items-end">
        <div class="custom-control custom-checkbox">
            <input class="custom-control-input" type="checkbox" id="is_published" name="is_published" value="1" @checked(old('is_published', $event->is_published ?? true))>
            <label class="custom-control-label" for="is_published">Published</label>
        </div>
    </div>
    <div class="form-group col-12">
        <label>Image</label>
        @if(!empty($event->image))
            <div class="mb-2">
                <img src="{{ asset($event->image) }}" alt="{{ $event->title ?? 'Event' }}" class="img-fluid rounded" style="max-height:160px;">
            </div>
        @endif
        <input type="file" name="image" class="form-control" accept="image/*">
    </div>
</div>
<div class="card-footer">
    <button class="btn btn-primary">Save</button>
    <a href="{{ route('events.index') }}" class="btn btn-secondary">Cancel</a>
</div>
