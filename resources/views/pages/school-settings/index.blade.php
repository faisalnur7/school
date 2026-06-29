@extends('layouts.master')

@section('contents')
    <div class="container-fluid">
        <div class="card">
            <div class="card-header bg-gradient-primary text-white py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0 font-weight-bold text-white">
                        <i class="fas fa-cog mr-2"></i>School Settings
                    </h4>
                </div>
            </div>
            <div class="card-body">

                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show">
                        {{ session('success') }}
                        <button type="button" class="close" data-dismiss="alert">&times;</button>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $e)
                                <li>{{ $e }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('school-settings.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        {{-- Basic Info --}}
                        <div class="col-md-8">
                            <h5 class="mb-3 text-muted border-bottom pb-2">Basic Information</h5>
                            <div class="row">
                                <div class="col-md-6 form-group">
                                    <label class="font-weight-bold">School Name <span class="text-danger">*</span></label>
                                    <input type="text" name="name"
                                        class="form-control @error('name') is-invalid @enderror"
                                        value="{{ old('name', $setting->name) }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6 form-group">
                                    <label class="font-weight-bold">School Short Name</label>
                                    <input type="text" name="short_name"
                                        class="form-control @error('short_name') is-invalid @enderror"
                                        value="{{ old('short_name', $setting->short_name) }}"
                                        placeholder="e.g. GCSC">
                                    @error('short_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-12 form-group">
                                    <label class="font-weight-bold">Address <span class="text-danger">*</span></label>
                                    <textarea name="address" rows="2" class="form-control @error('address') is-invalid @enderror" required>{{ old('address', $setting->address) }}</textarea>
                                    @error('address')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-12 form-group">
                                    <label>Slogan</label>
                                    <input type="text" name="slogan"
                                        class="form-control @error('slogan') is-invalid @enderror"
                                        value="{{ old('slogan', $setting->slogan) }}">
                                    @error('slogan')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <h5 class="mb-3 mt-2 text-muted border-bottom pb-2">Class Range</h5>
                            <div class="row">
                                <div class="col-md-6 form-group">
                                    <label>From Class</label>
                                    <select name="from_class"
                                        class="form-control @error('from_class') is-invalid @enderror">
                                        <option value="">— Select —</option>
                                        @foreach ($classes as $c)
                                            <option value="{{ $c->id }}"
                                                {{ old('from_class', $setting->from_class) == $c->id ? 'selected' : '' }}>
                                                {{ $c->name_en }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('from_class')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6 form-group">
                                    <label>To Class</label>
                                    <select name="to_class" class="form-control @error('to_class') is-invalid @enderror">
                                        <option value="">— Select —</option>
                                        @foreach ($classes as $c)
                                            <option value="{{ $c->id }}"
                                                {{ old('to_class', $setting->to_class) == $c->id ? 'selected' : '' }}>
                                                {{ $c->name_en }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('to_class')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <h5 class="mb-3 mt-2 text-muted border-bottom pb-2">Contact & Social</h5>
                            <div class="row">
                                <div class="col-md-4 form-group">
                                    <label>Website</label>
                                    <input type="url" name="website"
                                        class="form-control @error('website') is-invalid @enderror"
                                        value="{{ old('website', $setting->website) }}" placeholder="https://...">
                                    @error('website')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4 form-group">
                                    <label>Email</label>
                                    <input type="email" name="email"
                                        class="form-control @error('email') is-invalid @enderror"
                                        value="{{ old('email', $setting->email) }}" placeholder="info@school.edu.bd">
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4 form-group">
                                    <label>Facebook Page</label>
                                    <input type="url" name="facebook_page"
                                        class="form-control @error('facebook_page') is-invalid @enderror"
                                        value="{{ old('facebook_page', $setting->facebook_page) }}"
                                        placeholder="https://facebook.com/...">
                                    @error('facebook_page')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4 form-group">
                                    <label>WhatsApp Number</label>
                                    <input type="text" name="whatsapp_number"
                                        class="form-control @error('whatsapp_number') is-invalid @enderror"
                                        value="{{ old('whatsapp_number', $setting->whatsapp_number) }}"
                                        placeholder="+880...">
                                    @error('whatsapp_number')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4 form-group">
                                    <label>Contact Number 1</label>
                                    <input type="text" name="contact_number_1"
                                        class="form-control @error('contact_number_1') is-invalid @enderror"
                                        value="{{ old('contact_number_1', $setting->contact_number_1) }}"
                                        placeholder="+880...">
                                    @error('contact_number_1')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4 form-group">
                                    <label>Contact Number 2</label>
                                    <input type="text" name="contact_number_2"
                                        class="form-control @error('contact_number_2') is-invalid @enderror"
                                        value="{{ old('contact_number_2', $setting->contact_number_2) }}"
                                        placeholder="+880...">
                                    @error('contact_number_2')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>


                        {{-- File Uploads --}}
                        <div class="col-md-4">
                            <h5 class="mb-3 text-muted border-bottom pb-2">Logo, Favicon, Letter Head & WhatsApp QR</h5>

                            <div class="form-group">
                                <label class="font-weight-bold">Logo</label>
                                @if ($setting->logo)
                                    <div class="mb-2">
                                        <img src="{{ asset($setting->logo) }}" alt="Logo" class="img-thumbnail"
                                            style="max-height:100px">
                                    </div>
                                @endif
                                <input type="file" name="logo"
                                    class="form-control-file @error('logo') is-invalid @enderror" accept="image/*"
                                    onchange="previewImage(this,'logoPreview')">
                                <img id="logoPreview" src="#" alt="Preview" class="img-thumbnail mt-2 d-none"
                                    style="max-height:100px">
                                @error('logo')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Max 100KB. JPG, PNG, GIF.</small>
                            </div>

                            <div class="form-group mt-3">
                                <label class="font-weight-bold">Favicon</label>
                                @if ($setting->favicon)
                                    <div class="mb-2">
                                        @php $faviconExt = pathinfo($setting->favicon, PATHINFO_EXTENSION); @endphp
                                        @if (in_array(strtolower($faviconExt), ['jpg', 'jpeg', 'png', 'gif', 'webp', 'ico']))
                                            <img src="{{ asset($setting->favicon) }}" alt="Favicon" class="img-thumbnail"
                                                style="max-height:64px">
                                        @else
                                            <a href="{{ asset($setting->favicon) }}" target="_blank"
                                                class="btn btn-sm btn-outline-secondary">
                                                <i class="fas fa-file"></i> View Current
                                            </a>
                                        @endif
                                    </div>
                                @endif
                                <input type="file" name="favicon"
                                    class="form-control-file @error('favicon') is-invalid @enderror"
                                    accept="image/*,.ico" onchange="previewImage(this,'faviconPreview')">
                                <img id="faviconPreview" src="#" alt="Preview" class="img-thumbnail mt-2 d-none"
                                    style="max-height:64px">
                                @error('favicon')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Recommended 32x32 PNG or ICO. Max 100KB.</small>
                            </div>

                            <div class="form-group mt-3">
                                <label class="font-weight-bold">Letter Head</label>
                                @if ($setting->letter_head)
                                    <div class="mb-2">
                                        @php $ext = pathinfo($setting->letter_head, PATHINFO_EXTENSION); @endphp
                                        @if (in_array(strtolower($ext), ['jpg', 'jpeg', 'png', 'gif']))
                                            <img src="{{ asset($setting->letter_head) }}" alt="Letter Head"
                                                class="img-thumbnail" style="max-height:100px">
                                        @else
                                            <a href="{{ asset($setting->letter_head) }}" target="_blank"
                                                class="btn btn-sm btn-outline-secondary">
                                                <i class="fas fa-file-pdf"></i> View Current
                                            </a>
                                        @endif
                                    </div>
                                @endif
                                <input type="file" name="letter_head"
                                    class="form-control-file @error('letter_head') is-invalid @enderror"
                                    accept="image/*,.pdf" onchange="previewImage(this,'letterHeadPreview')">
                                <img id="letterHeadPreview" src="#" alt="Preview"
                                    class="img-thumbnail mt-2 d-none" style="max-height:100px">
                                @error('letter_head')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Max 100KB. JPG, PNG, PDF.</small>
                            </div>

                            <div class="form-group mt-3">
                                <label class="font-weight-bold">WhatsApp QR Code</label>
                                @if($setting->whatsapp_qr)
                                    <div class="mb-2">
                                        <img src="{{ asset($setting->whatsapp_qr) }}" alt="WhatsApp QR"
                                            class="img-thumbnail" style="max-height:100px">
                                    </div>
                                @endif
                                <input type="file" name="whatsapp_qr"
                                    class="form-control-file @error('whatsapp_qr') is-invalid @enderror"
                                    accept="image/*" onchange="previewImage(this,'whatsappQrPreview')">
                                <img id="whatsappQrPreview" src="#" alt="Preview"
                                    class="img-thumbnail mt-2 d-none" style="max-height:100px">
                                @error('whatsapp_qr')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Upload QR code image. Max 100KB.</small>
                            </div>

                            <div class="card border shadow-sm mb-3" id="principal-signature">
                                <div class="card-header bg-light py-2">
                                    <h5 class="mb-0 text-dark">
                                        <i class="fas fa-signature mr-1"></i> Principal Signature
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="form-group">
                                        <label class="font-weight-bold">Designation</label>
                                        <input type="text" name="principal_designation"
                                            class="form-control @error('principal_designation') is-invalid @enderror"
                                            value="{{ old('principal_designation', $setting->principal_designation ?? 'Principal') }}"
                                            placeholder="Principal">
                                        @error('principal_designation')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label class="font-weight-bold">Name</label>
                                        <input type="text" name="principal_name"
                                            class="form-control @error('principal_name') is-invalid @enderror"
                                            value="{{ old('principal_name', $setting->principal_name) }}"
                                            placeholder="Md. Raqib Hossain">
                                        @error('principal_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label class="font-weight-bold">School Name</label>
                                        <input type="text" name="principal_school_name"
                                            class="form-control @error('principal_school_name') is-invalid @enderror"
                                            value="{{ old('principal_school_name', $setting->principal_school_name ?? $setting->name) }}"
                                            placeholder="Green Chartered School & College">
                                        @error('principal_school_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="form-group mb-0">
                                        <label class="font-weight-bold">Phone</label>
                                        <input type="text" name="principal_phone"
                                            class="form-control @error('principal_phone') is-invalid @enderror"
                                            value="{{ old('principal_phone', $setting->principal_phone) }}"
                                            placeholder="Phone-01886-780641, 01886-780642">
                                        @error('principal_phone')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <h5 class="mb-3 mt-3 text-muted border-bottom pb-2">Theme & ID Card Colors</h5>
                            <div class="row">
                                <div class="col-md-12 form-group">
                                    <label class="font-weight-bold">Primary Color</label>
                                    <div class="d-flex align-items-center gap-2" style="gap:8px">
                                        <input type="color" name="primary_color" id="primaryColor"
                                            class="form-control form-control-color p-1"
                                            style="width:48px;height:38px;cursor:pointer"
                                            value="{{ old('primary_color', $setting->primary_color ?? '#1e3a5f') }}">
                                        <input type="text" id="primaryColorHex" class="form-control form-control-sm"
                                            style="width:110px;font-family:monospace"
                                            value="{{ old('primary_color', $setting->primary_color ?? '#1e3a5f') }}"
                                            oninput="syncColor(this,'primaryColor')">
                                        <div id="primaryPreview" class="rounded"
                                            style="width:32px;height:32px;border:1px solid #ddd;background:{{ $setting->primary_color ?? '#1e3a5f' }}">
                                        </div>
                                    </div>
                                    @error('primary_color')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-12 form-group">
                                    <label class="font-weight-bold">Secondary Color</label>
                                    <div class="d-flex align-items-center gap-2" style="gap:8px">
                                        <input type="color" name="secondary_color" id="secondaryColor"
                                            class="form-control form-control-color p-1"
                                            style="width:48px;height:38px;cursor:pointer"
                                            value="{{ old('secondary_color', $setting->secondary_color ?? '#2563eb') }}">
                                        <input type="text" id="secondaryColorHex" class="form-control form-control-sm"
                                            style="width:110px;font-family:monospace"
                                            value="{{ old('secondary_color', $setting->secondary_color ?? '#2563eb') }}"
                                            oninput="syncColor(this,'secondaryColor')">
                                        <div id="secondaryPreview" class="rounded"
                                            style="width:32px;height:32px;border:1px solid #ddd;background:{{ $setting->secondary_color ?? '#2563eb' }}">
                                        </div>
                                    </div>
                                    @error('secondary_color')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                        </div>
                    </div>

                    <div class="mt-3">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Save Settings
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>

    <script>
        function previewImage(input, previewId) {
            const preview = document.getElementById(previewId);
            if (input.files && input.files[0]) {
                const file = input.files[0];
                if (file.type.startsWith('image/')) {
                    const reader = new FileReader();
                    reader.onload = e => {
                        preview.src = e.target.result;
                        preview.classList.remove('d-none');
                    };
                    reader.readAsDataURL(file);
                } else {
                    preview.classList.add('d-none');
                }
            }
        }

        // Color picker sync
        const colorMap = {
            primaryColor: {
                hex: 'primaryColorHex',
                preview: 'primaryPreview'
            },
            secondaryColor: {
                hex: 'secondaryColorHex',
                preview: 'secondaryPreview'
            },
            idCardColor: {
                hex: 'idCardColorHex',
                preview: 'idCardPreview'
            },
        };

        Object.keys(colorMap).forEach(id => {
            const picker = document.getElementById(id);
            const hexEl = document.getElementById(colorMap[id].hex);
            const preview = document.getElementById(colorMap[id].preview);
            if (!picker) return;
            picker.addEventListener('input', () => {
                hexEl.value = picker.value;
                preview.style.background = picker.value;
            });
        });

        function syncColor(hexInput, pickerId) {
            const val = hexInput.value.trim();
            if (/^#[0-9a-fA-F]{6}$/.test(val)) {
                document.getElementById(pickerId).value = val;
                const previewId = colorMap[pickerId]?.preview;
                if (previewId) document.getElementById(previewId).style.background = val;
            }
        }

    </script>
@endsection
