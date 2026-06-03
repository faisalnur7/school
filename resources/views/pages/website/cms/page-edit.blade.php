@extends('layouts.master')
@section('title', 'Edit ' . ucfirst($type) . ' Page')
@section('contents')
<div class="col-12">

    @php
    $pageLabels = [
        'home'     => ['label'=>'Home Page',             'icon'=>'fa-home',         'color'=>'primary'],
        'about'    => ['label'=>'About Us Page',         'icon'=>'fa-info-circle',  'color'=>'purple'],
        'academics'=> ['label'=>'Academics Page',        'icon'=>'fa-book-open',    'color'=>'indigo'],
        'admission'=> ['label'=>'Admission Page',        'icon'=>'fa-user-plus',    'color'=>'success'],
        'contact'  => ['label'=>'Contact Page',          'icon'=>'fa-envelope',     'color'=>'success'],
        'notices'  => ['label'=>'Notices Page',          'icon'=>'fa-bell',         'color'=>'warning'],
        'events'   => ['label'=>'Events Page',           'icon'=>'fa-calendar',     'color'=>'danger'],
        'news-events'=> ['label'=>'News / Events Page',  'icon'=>'fa-calendar-day', 'color'=>'danger'],
        'calendar' => ['label'=>'Academic Calendar Page','icon'=>'fa-calendar-alt', 'color'=>'info'],
        'results'  => ['label'=>'Results / Exam Information Page','icon'=>'fa-clipboard-check', 'color'=>'teal'],
        'gallery'  => ['label'=>'Gallery Page',          'icon'=>'fa-images',      'color'=>'primary'],
        'teachers-staff' => ['label'=>'Teachers / Staff Page', 'icon'=>'fa-chalkboard-teacher', 'color'=>'secondary'],
        'downloads' => ['label'=>'Downloads / Forms Page','icon'=>'fa-file-download','color'=>'dark'],
        'facilities' => ['label'=>'Facilities Page',     'icon'=>'fa-building',    'color'=>'info'],
        'policies' => ['label'=>'Policies / Rules Page',  'icon'=>'fa-gavel',       'color'=>'warning'],
    ];
    $meta = $pageLabels[$type] ?? ['label'=>ucfirst($type).' Page','icon'=>'fa-file','color'=>'secondary'];
    @endphp

    {{-- Breadcrumb --}}
    <div class="d-flex align-items-center gap-2 mb-3">
        <a href="{{ route('website.cms.hub') }}" class="btn btn-sm btn-outline-secondary">
            <i class="fas fa-arrow-left mr-1"></i> Website Management
        </a>
        <span class="text-muted">/</span>
        <span class="font-weight-bold"><i class="fas {{ $meta['icon'] }} mr-1"></i> {{ $meta['label'] }}</span>
        @php
            $liveUrl = match ($type) {
                'home' => route('website.home'),
                'about' => route('website.about'),
                'contact' => route('website.contact'),
                'notices' => route('website.notices'),
                'events' => route('website.events'),
                'calendar' => route('website.academic-calendar'),
                default => ($page->exists && $page->slug ? route('website.page.show', $page->slug) : '#'),
            };
        @endphp
        <a href="{{ $liveUrl }}" target="_blank" class="btn btn-sm btn-outline-info ml-auto {{ $liveUrl === '#' ? 'disabled' : '' }}">
            <i class="fas fa-external-link-alt mr-1"></i> View Live
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            {{ session('success') }}
        </div>
    @endif

    <div class="row">

        {{-- Left: Page Settings --}}
        <div class="col-lg-5">
            <form action="{{ route('website.cms.page.update', $type) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="card card-outline card-{{ $meta['color'] }}">
                    <div class="card-header"><h3 class="card-title font-bold">Page Settings</h3></div>
                    <div class="card-body">

                        <div class="form-group">
                            <label class="font-weight-bold">Page Title <span class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control @error('title') is-invalid @enderror"
                                   value="{{ old('title', $page->title ?? ucfirst($type)) }}" required>
                            @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="form-group">
                            <label class="font-weight-bold">Short Description / Subtitle</label>
                            <textarea name="excerpt" rows="2" class="form-control" placeholder="Shown as subtitle on the page header...">{{ old('excerpt', $page->excerpt ?? '') }}</textarea>
                        </div>

                        <div class="form-group">
                            <label class="font-weight-bold">Main Content</label>
                            <textarea name="content" rows="6" class="form-control" placeholder="Main body text for this page...">{{ old('content', $page->content ?? '') }}</textarea>
                        </div>

                        <div class="form-group">
                            <label class="font-weight-bold">Cover / Banner Image</label>
                            @if(!empty($page->cover_image))
                                <div class="mb-2">
                                    <img src="{{ asset($page->cover_image) }}" class="img-fluid rounded" style="max-height:120px;">
                                </div>
                            @endif
                            <div class="custom-file">
                                <input type="file" name="cover_image" class="custom-file-input" id="coverImage" accept="image/*">
                                <label class="custom-file-label" for="coverImage">Choose image (max 2MB)</label>
                            </div>
                            <small class="text-muted">Recommended: 1440×500px. Used as the page hero background.</small>
                        </div>

                        <div class="form-group">
                            <label class="font-weight-bold">Status</label>
                            <select name="status" class="form-control">
                                <option value="published" @selected(old('status', $page->status ?? 'published') === 'published')>Published</option>
                                <option value="draft" @selected(old('status', $page->status ?? '') === 'draft')>Draft</option>
                            </select>
                        </div>

                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-{{ $meta['color'] }}">
                            <i class="fas fa-save mr-1"></i> Save Page Settings
                        </button>
                    </div>
                </div>
            </form>
        </div>

        {{-- Right: Sections --}}
        <div class="col-lg-7">

            {{-- Existing Sections --}}
            @if($sections->isNotEmpty())
            <div class="card card-outline card-dark mb-3">
                <div class="card-header"><h3 class="card-title font-bold">Content Sections ({{ $sections->count() }})</h3></div>
                <div class="card-body p-0">
                    @foreach($sections as $section)
                    <div class="border-bottom p-3">
                        <form action="{{ route('website.cms.section.update', [$type, $section]) }}" method="POST" enctype="multipart/form-data">
                            @csrf @method('PUT')
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <strong class="text-sm">{{ $section->title }}</strong>
                                <div class="d-flex gap-1">
                                    <button type="button" class="btn btn-xs btn-outline-secondary" onclick="toggleSection({{ $section->id }})">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <form action="{{ route('website.cms.section.destroy', [$type, $section]) }}" method="POST" class="d-inline"
                                          onsubmit="return confirm('Delete this section?')">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-xs btn-outline-danger"><i class="fas fa-trash"></i></button>
                                    </form>
                                </div>
                            </div>

                            {{-- Section image preview --}}
                            @if($section->image)
                                <img src="{{ asset($section->image) }}" class="img-fluid rounded mb-2" style="max-height:80px;">
                            @endif

                            <div id="section-form-{{ $section->id }}" style="display:none;">
                                <div class="form-group mb-2">
                                    <input type="text" name="title" class="form-control form-control-sm" value="{{ $section->title }}" required placeholder="Section title">
                                </div>
                                <div class="form-group mb-2">
                                    <textarea name="content" rows="3" class="form-control form-control-sm" placeholder="Section content...">{{ $section->content }}</textarea>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-6">
                                        <label class="text-xs font-weight-bold">Photo</label>
                                        <input type="file" name="image" class="form-control form-control-sm" accept="image/*">
                                    </div>
                                    <div class="col-3">
                                        <label class="text-xs font-weight-bold">Position</label>
                                        <select name="image_position" class="form-control form-control-sm">
                                            <option value="right" @selected($section->image_position==='right')>Right</option>
                                            <option value="left" @selected($section->image_position==='left')>Left</option>
                                            <option value="top" @selected($section->image_position==='top')>Top</option>
                                            <option value="background" @selected($section->image_position==='background')>BG</option>
                                        </select>
                                    </div>
                                    <div class="col-3">
                                        <label class="text-xs font-weight-bold">Order</label>
                                        <input type="number" name="sort_order" class="form-control form-control-sm" value="{{ $section->sort_order }}" min="0">
                                    </div>
                                </div>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input" id="active_{{ $section->id }}"
                                               name="is_active" value="1" @checked($section->is_active)>
                                        <label class="custom-control-label" for="active_{{ $section->id }}">Active</label>
                                    </div>
                                    <button type="submit" class="btn btn-sm btn-success ml-auto">
                                        <i class="fas fa-save mr-1"></i> Update
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Add New Section --}}
            @if($page->exists)
            <div class="card card-outline card-success">
                <div class="card-header"><h3 class="card-title font-bold"><i class="fas fa-plus mr-1"></i> Add New Section</h3></div>
                <form action="{{ route('website.cms.section.store', $type) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="card-body">
                        <div class="form-group">
                            <label class="font-weight-bold">Section Title <span class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control" placeholder="e.g. Our Mission, Vision, History..." required>
                        </div>
                        <div class="form-group">
                            <label class="font-weight-bold">Content</label>
                            <textarea name="content" rows="4" class="form-control" placeholder="Section body text..."></textarea>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="font-weight-bold">Photo <small class="text-muted">(max 2MB)</small></label>
                                    <div class="custom-file">
                                        <input type="file" name="image" class="custom-file-input" id="sectionImage" accept="image/*">
                                        <label class="custom-file-label" for="sectionImage">Choose photo</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="font-weight-bold">Image Position</label>
                                    <select name="image_position" class="form-control">
                                        <option value="right">Right</option>
                                        <option value="left">Left</option>
                                        <option value="top">Top</option>
                                        <option value="background">Background</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="font-weight-bold">Sort Order</label>
                                    <input type="number" name="sort_order" class="form-control" value="{{ $sections->count() }}" min="0">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-plus mr-1"></i> Add Section
                        </button>
                    </div>
                </form>
            </div>
            @else
                <div class="alert alert-info">
                    <i class="fas fa-info-circle mr-1"></i> Save the page settings first, then you can add sections.
                </div>
            @endif

        </div>
    </div>
</div>

<script>
function toggleSection(id) {
    const el = document.getElementById('section-form-' + id);
    el.style.display = el.style.display === 'none' ? 'block' : 'none';
}
// Bootstrap custom file input label
document.querySelectorAll('.custom-file-input').forEach(input => {
    input.addEventListener('change', function() {
        const label = this.nextElementSibling;
        label.textContent = this.files[0]?.name || 'Choose file';
    });
});
</script>
@endsection
