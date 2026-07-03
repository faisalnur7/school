<script>
    (function () {
        if (typeof Dropzone === 'undefined') {
            return;
        }

        Dropzone.autoDiscover = false;

        const initAttachmentDropzone = (dropzoneElement) => {
            const fileInput = document.getElementById(dropzoneElement.dataset.inputId || '');
            const errorBox = document.getElementById(dropzoneElement.dataset.errorId || '');

            if (!fileInput) {
                return;
            }

            const existingUrl = dropzoneElement.dataset.existingUrl || '';
            const existingName = dropzoneElement.dataset.existingName || 'attachment';
            const acceptedFiles = dropzoneElement.dataset.acceptedFiles || '.jpg,.jpeg,.png,.pdf';

            const setError = (message) => {
                if (errorBox) {
                    errorBox.textContent = message || '';
                }
            };

            const syncInput = (file) => {
                const dataTransfer = new DataTransfer();
                dataTransfer.items.add(file);
                fileInput.files = dataTransfer.files;
            };

            const attachmentDropzone = new Dropzone(dropzoneElement, {
                url: window.location.href,
                method: 'post',
                autoProcessQueue: false,
                clickable: true,
                maxFiles: 1,
                acceptedFiles: acceptedFiles,
                previewsContainer: dropzoneElement,
                addRemoveLinks: true,
                dictDefaultMessage: dropzoneElement.dataset.message || 'Drop file here or click to browse',
                init: function () {
                    if (existingUrl) {
                        const mockFile = {
                            name: existingName,
                            size: 0,
                            accepted: true,
                        };

                        this.emit('addedfile', mockFile);
                        this.emit('complete', mockFile);
                        this.files.push(mockFile);
                    }

                    this.on('addedfile', function (file) {
                        setError('');
                        syncInput(file);
                    });

                    this.on('removedfile', function () {
                        if (this.files.length === 0) {
                            fileInput.value = '';
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
                const existing = attachmentDropzone.files.slice();
                existing.forEach((item) => attachmentDropzone.removeFile(item));
                attachmentDropzone.addFile(file);
            });
        };

        document.querySelectorAll('[data-attachment-dropzone]').forEach(initAttachmentDropzone);
    })();
</script>
