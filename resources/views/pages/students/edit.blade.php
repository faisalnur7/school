@extends('layouts.master')

@section('contents')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                @php
                    $editMode = true;
                @endphp
                @include('pages.students.form', ['student' => $student])
            </div>
        </div>
    </div>
@endsection

@section('styles')
    @include('components.form-styles')
    <link rel="stylesheet" href="{{ asset('assets/plugins/dropzone/min/dropzone.min.css') }}">
    <style>
        .student-image-dropzone {
            min-height: 520px;
            cursor: pointer;
            transition: border-color .15s ease, background-color .15s ease, box-shadow .15s ease;
        }

        .student-image-dropzone:hover {
            border-color: #2563eb;
            background-color: #eff6ff;
            box-shadow: 0 10px 24px rgba(37, 99, 235, 0.08);
        }

        .student-image-dropzone.dz-started .dz-message {
            display: none;
        }

        .student-image-dropzone .dz-preview {
            margin: 0;
            width: 100%;
            max-width: 300px;
            margin-left: auto;
            margin-right: auto;
        }

        .student-image-dropzone .dz-preview .dz-image {
            border-radius: 14px;
            width: 300px;
            height: 450px;
        }

        .student-image-dropzone .dz-preview .dz-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .student-image-dropzone .dz-preview .dz-details {
            padding: 1rem;
        }

        .student-image-dropzone .dz-preview .dz-remove {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-top: .75rem;
            padding: .45rem .8rem;
            border-radius: .5rem;
            background: #fee2e2;
            color: #b91c1c;
            text-decoration: none;
            font-size: .8rem;
            font-weight: 600;
        }

        .student-image-dropzone .dz-preview .dz-remove:hover {
            background: #fecaca;
            color: #991b1b;
        }
    </style>
@endsection

@section('scripts')
    @include('scripts.common.load_location')
    @include('scripts.common.load_academic_information')
    @include('scripts.student.main_script')
    <script src="{{ asset('assets/plugins/dropzone/min/dropzone.min.js') }}"></script>
    <script>
        (function () {
            const dropzoneElement = document.getElementById('studentImageDropzone');
            const fileInput = document.getElementById('studentImageInput');
            const errorBox = document.getElementById('studentImageValidationError');

            if (!dropzoneElement || !fileInput || typeof Dropzone === 'undefined') {
                return;
            }

            Dropzone.autoDiscover = false;

            const minWidth = parseInt(dropzoneElement.dataset.minWidth, 10) || 290;
            const maxWidth = parseInt(dropzoneElement.dataset.maxWidth, 10) || 300;
            const minHeight = parseInt(dropzoneElement.dataset.minHeight, 10) || 440;
            const maxHeight = parseInt(dropzoneElement.dataset.maxHeight, 10) || 450;
            const existingImageUrl = dropzoneElement.dataset.existingImageUrl || '';
            const existingImageName = dropzoneElement.dataset.existingImageName || 'student-image.jpg';
            let selectedFile = null;

            const setError = (message) => {
                if (errorBox) {
                    errorBox.textContent = message || '';
                }
            };

            const syncInput = (file) => {
                const dataTransfer = new DataTransfer();
                dataTransfer.items.add(file);
                fileInput.files = dataTransfer.files;
                selectedFile = file;
            };

            const clearInput = () => {
                fileInput.value = '';
                selectedFile = null;
            };

            const validateDimensions = (file) => {
                return new Promise((resolve) => {
                    const image = new Image();
                    const objectUrl = URL.createObjectURL(file);

                    image.onload = function () {
                        URL.revokeObjectURL(objectUrl);
                        const width = image.naturalWidth;
                        const height = image.naturalHeight;
                        const valid = width >= minWidth && width <= maxWidth && height >= minHeight && height <= maxHeight;

                        resolve({
                            valid,
                            width,
                            height,
                        });
                    };

                    image.onerror = function () {
                        URL.revokeObjectURL(objectUrl);
                        resolve({
                            valid: false,
                            width: 0,
                            height: 0,
                        });
                    };

                    image.src = objectUrl;
                });
            };

            const studentImageDropzone = new Dropzone(dropzoneElement, {
                url: window.location.href,
                method: 'post',
                autoProcessQueue: false,
                clickable: true,
                maxFiles: 1,
                acceptedFiles: 'image/*',
                previewsContainer: dropzoneElement,
                addRemoveLinks: true,
                dictDefaultMessage: 'Drop student photo here or click to browse',
                init: function () {
                    if (existingImageUrl) {
                        const mockFile = {
                            name: existingImageName,
                            size: 0,
                            accepted: true,
                        };

                        this.emit('addedfile', mockFile);
                        this.emit('thumbnail', mockFile, existingImageUrl);
                        this.emit('complete', mockFile);
                        this.files.push(mockFile);
                    }

                    this.on('addedfile', async function (file) {
                        setError('');

                        if (selectedFile) {
                            this.removeAllFiles(true);
                        }

                        const result = await validateDimensions(file);
                        if (!result.valid) {
                            this.removeFile(file);
                            clearInput();
                            setError(`Image size must be between ${minWidth}-${maxWidth}px wide and ${minHeight}-${maxHeight}px tall. Selected: ${result.width}x${result.height}px.`);
                            return;
                        }

                        syncInput(file);
                    });

                    this.on('removedfile', function (file) {
                        if (selectedFile && file.name === selectedFile.name && file.size === selectedFile.size) {
                            clearInput();
                        }

                        if (this.files.length === 0) {
                            setError('');
                        }
                    });

                    this.on('error', function (file, message) {
                        if (typeof message === 'string') {
                            setError(message);
                        }
                    });
                },
            });

            fileInput.addEventListener('change', function () {
                setError('');
                if (!this.files || !this.files.length) {
                    return;
                }

                const file = this.files[0];
                const existing = studentImageDropzone.files.slice();
                existing.forEach((item) => studentImageDropzone.removeFile(item));
                studentImageDropzone.addFile(file);
            });

            const form = document.querySelector('form[method="POST"]');
            if (form) {
                form.addEventListener('submit', async function (event) {
                    const file = fileInput.files && fileInput.files[0];
                    if (!file) {
                        return;
                    }

                    const result = await validateDimensions(file);
                    if (!result.valid) {
                        event.preventDefault();
                        setError(`Image size must be between ${minWidth}-${maxWidth}px wide and ${minHeight}-${maxHeight}px tall. Selected: ${result.width}x${result.height}px.`);
                    }
                });
            }
        })();
    </script>
@endsection
