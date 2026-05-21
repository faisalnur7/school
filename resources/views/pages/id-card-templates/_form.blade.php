@php $template = $template ?? null; @endphp

<div class="row">
    <div class="col-md-6 form-group">
        <label class="font-weight-bold">Template Name <span class="text-danger">*</span></label>
        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
            value="{{ old('name', $template?->name) }}" required>
        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-6 form-group">
        <label class="font-weight-bold">Orientation <span class="text-danger">*</span></label>
        <select name="orientation" class="form-control @error('orientation') is-invalid @enderror" required>
            <option value="portrait"  {{ old('orientation', $template?->orientation) === 'portrait'  ? 'selected' : '' }}>Portrait</option>
            <option value="landscape" {{ old('orientation', $template?->orientation) === 'landscape' ? 'selected' : '' }}>Landscape</option>
        </select>
        @error('orientation')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-12 form-group">
        <label class="font-weight-bold">Background Image</label>
        @if($template?->background_image)
            <div class="mb-2">
                <img src="{{ asset($template->background_image) }}" alt="Background"
                    class="img-thumbnail" style="max-height:120px">
                <small class="text-muted d-block mt-1">Current background — upload a new one to replace.</small>
            </div>
        @endif
        <input type="file" name="background_image"
            class="form-control-file @error('background_image') is-invalid @enderror"
            accept="image/*" onchange="previewBg(this)">
        <img id="bgPreview" src="#" alt="Preview" class="img-thumbnail mt-2 d-none" style="max-height:120px">
        @error('background_image')<div class="text-danger small">{{ $message }}</div>@enderror
        <small class="text-muted">Max 100KB. JPG, PNG, GIF.</small>
    </div>
</div>

<script>
function previewBg(input) {
    const preview = document.getElementById('bgPreview');
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => { preview.src = e.target.result; preview.classList.remove('d-none'); };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
