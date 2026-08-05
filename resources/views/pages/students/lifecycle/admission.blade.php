@extends('layouts.master')

@section('contents')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                {{-- Override the form action to point to admission.store --}}
                @php
                    // Trick: the form.blade.php uses isset($student) to decide action.
                    // We leave $student unset so it renders as "Create Student" form,
                    // but we need to override the action. We do this via a wrapper that
                    // intercepts the form submit via JS and changes the action.
                    $admissionMode = true;
                @endphp
                @include('pages.students.form')
            </div>
        </div>
    </div>
@endsection

@section('styles')
    @include('components.form-styles')
    @include('pages.students._modern-styles')
    <link rel="stylesheet" href="{{ asset('assets/plugins/dropzone/min/dropzone.min.css') }}">
@endsection

@section('scripts')
    @include('scripts.common.load_location')
    @include('scripts.common.load_academic_information')
    @include('scripts.student.main_script')
    <script src="{{ asset('assets/plugins/dropzone/min/dropzone.min.js') }}"></script>
    <script>
        (function () {
            const form = document.querySelector('form[method="POST"]');
            if (form) {
                form.action = "{{ route('students.admission.store') }}";
            }

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
                url: form ? form.action : window.location.href,
                method: 'post',
                autoProcessQueue: false,
                clickable: true,
                maxFiles: 1,
                acceptedFiles: 'image/*',
                previewsContainer: dropzoneElement,
                addRemoveLinks: true,
                dictDefaultMessage: 'Drop student photo here or click to browse',
                init: function () {
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
