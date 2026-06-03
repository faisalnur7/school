@extends('layouts.master')
@section('title', 'Home Slider / Banners')
@section('contents')
<div class="col-12">

    <div class="d-flex align-items-center gap-2 mb-3">
        <a href="{{ route('website.cms.hub') }}" class="btn btn-sm btn-outline-secondary">
            <i class="fas fa-arrow-left mr-1"></i> Website Management
        </a>
        <span class="text-muted">/</span>
        <span class="font-weight-bold"><i class="fas fa-images mr-1"></i> Home Slider / Banners</span>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            {{ session('success') }}
        </div>
    @endif

    <div class="row">

        {{-- Existing Banners --}}
        <div class="col-lg-8">
            <div class="card card-outline card-warning">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title font-bold">Slides ({{ $banners->count() }})</h3>
                    <small class="text-muted">Drag to reorder — use Sort Order field</small>
                </div>
                <div class="card-body p-0">
                    @forelse($banners as $banner)
                    <div class="border-bottom p-3 {{ !$banner->is_active ? 'bg-light' : '' }}">
                        <div class="d-flex gap-3">
                            {{-- Thumbnail --}}
                            <div class="flex-shrink-0">
                                @if($banner->image_path)
                                    <img src="{{ asset($banner->image_path) }}" class="rounded" style="width:100px;height:65px;object-fit:cover;">
                                @else
                                    <div class="rounded bg-gradient-to-br from-sky-500 to-indigo-600 d-flex align-items-center justify-content-center" style="width:100px;height:65px;">
                                        <i class="fas fa-image text-white text-xl"></i>
                                    </div>
                                @endif
                            </div>
                            {{-- Info --}}
                            <div class="flex-grow-1 min-w-0">
                                <div class="d-flex align-items-start justify-content-between">
                                    <div>
                                        <strong>{{ $banner->title }}</strong>
                                        @if(!$banner->is_active)
                                            <span class="badge badge-secondary ml-1">Hidden</span>
                                        @else
                                            <span class="badge badge-success ml-1">Active</span>
                                        @endif
                                        <p class="text-muted text-sm mb-0">{{ Str::limit($banner->subtitle, 60) }}</p>
                                        @if($banner->cta_text)
                                            <small class="text-info">CTA: {{ $banner->cta_text }}</small>
                                        @endif
                                    </div>
                                    <div class="d-flex gap-1 ml-2 flex-shrink-0">
                                        <button class="btn btn-xs btn-outline-secondary" onclick="toggleBannerForm({{ $banner->id }})">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <form action="{{ route('website.cms.banner.toggle', $banner) }}" method="POST" class="d-inline">
                                            @csrf @method('PATCH')
                                            <button class="btn btn-xs {{ $banner->is_active ? 'btn-outline-warning' : 'btn-outline-success' }}"
                                                    title="{{ $banner->is_active ? 'Hide' : 'Show' }}">
                                                <i class="fas {{ $banner->is_active ? 'fa-eye-slash' : 'fa-eye' }}"></i>
                                            </button>
                                        </form>
                                        <form action="{{ route('website.cms.banner.destroy', $banner) }}" method="POST" class="d-inline"
                                              onsubmit="return confirm('Delete this slide?')">
                                            @csrf @method('DELETE')
                                            <button class="btn btn-xs btn-outline-danger"><i class="fas fa-trash"></i></button>
                                        </form>
                                    </div>
                                </div>

                                {{-- Inline edit form --}}
                                <div id="banner-form-{{ $banner->id }}" style="display:none;" class="mt-3 border-top pt-3">
                                    <form action="{{ route('website.cms.banner.update', $banner) }}" method="POST" enctype="multipart/form-data">
                                        @csrf @method('PUT')
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group mb-2">
                                                    <label class="text-xs font-weight-bold">Title *</label>
                                                    <input type="text" name="title" class="form-control form-control-sm" value="{{ $banner->title }}" required>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group mb-2">
                                                    <label class="text-xs font-weight-bold">Subtitle</label>
                                                    <input type="text" name="subtitle" class="form-control form-control-sm" value="{{ $banner->subtitle }}">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group mb-2">
                                                    <label class="text-xs font-weight-bold">New Image <small>(max 3MB)</small></label>
                                                    <input type="file" name="image" class="form-control form-control-sm" accept="image/*">
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group mb-2">
                                                    <label class="text-xs font-weight-bold">CTA Text</label>
                                                    <input type="text" name="cta_text" class="form-control form-control-sm" value="{{ $banner->cta_text }}" placeholder="Learn More">
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group mb-2">
                                                    <label class="text-xs font-weight-bold">Sort Order</label>
                                                    <input type="number" name="sort_order" class="form-control form-control-sm" value="{{ $banner->sort_order }}" min="0">
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="form-group mb-2">
                                                    <label class="text-xs font-weight-bold">CTA URL</label>
                                                    <input type="text" name="cta_url" class="form-control form-control-sm" value="{{ $banner->cta_url }}" placeholder="/about">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="custom-control custom-switch">
                                                <input type="checkbox" class="custom-control-input" id="bactive_{{ $banner->id }}"
                                                       name="is_active" value="1" @checked($banner->is_active)>
                                                <label class="custom-control-label" for="bactive_{{ $banner->id }}">Active</label>
                                            </div>
                                            <button type="submit" class="btn btn-sm btn-warning ml-auto">
                                                <i class="fas fa-save mr-1"></i> Update Slide
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="p-4 text-center text-muted">
                        <i class="fas fa-images fa-2x mb-2 d-block"></i>
                        No slides yet. Add your first slide →
                    </div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Add New Banner --}}
        <div class="col-lg-4">
            <div class="card card-outline card-success sticky-top" style="top:80px;">
                <div class="card-header"><h3 class="card-title font-bold"><i class="fas fa-plus mr-1"></i> Add New Slide</h3></div>
                <form action="{{ route('website.cms.banner.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="card-body">
                        <div class="form-group">
                            <label class="font-weight-bold">Title <span class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control" placeholder="Welcome to Our School" required>
                        </div>
                        <div class="form-group">
                            <label class="font-weight-bold">Subtitle</label>
                            <textarea name="subtitle" rows="2" class="form-control" placeholder="A short tagline for this slide..."></textarea>
                        </div>
                        <div class="form-group">
                            <label class="font-weight-bold">Image <small class="text-muted">(max 3MB, 1440×560px recommended)</small></label>
                            <div class="custom-file">
                                <input type="file" name="image" class="custom-file-input" id="bannerImage" accept="image/*">
                                <label class="custom-file-label" for="bannerImage">Choose image</label>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-7">
                                <div class="form-group">
                                    <label class="font-weight-bold">CTA Button Text</label>
                                    <input type="text" name="cta_text" class="form-control" placeholder="Learn More">
                                </div>
                            </div>
                            <div class="col-5">
                                <div class="form-group">
                                    <label class="font-weight-bold">Button Style</label>
                                    <select name="button_style" class="form-control">
                                        <option value="white">White</option>
                                        <option value="outline">Outline</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="font-weight-bold">CTA URL</label>
                            <input type="text" name="cta_url" class="form-control" placeholder="/about">
                        </div>
                        <div class="row">
                            <div class="col-6">
                                <div class="form-group">
                                    <label class="font-weight-bold">Sort Order</label>
                                    <input type="number" name="sort_order" class="form-control" value="{{ $banners->count() }}" min="0">
                                </div>
                            </div>
                            <div class="col-6 d-flex align-items-end pb-3">
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input" id="newBannerActive" name="is_active" value="1" checked>
                                    <label class="custom-control-label" for="newBannerActive">Active</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-success btn-block">
                            <i class="fas fa-plus mr-1"></i> Add Slide
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</div>

<script>
function toggleBannerForm(id) {
    const el = document.getElementById('banner-form-' + id);
    el.style.display = el.style.display === 'none' ? 'block' : 'none';
}
document.querySelectorAll('.custom-file-input').forEach(input => {
    input.addEventListener('change', function() {
        this.nextElementSibling.textContent = this.files[0]?.name || 'Choose file';
    });
});
</script>
@endsection
