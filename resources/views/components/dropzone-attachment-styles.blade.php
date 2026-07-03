<style>
    .attachment-dropzone {
        min-height: 220px;
        border: 2px dashed #cbd5e1;
        border-radius: 0.9rem;
        background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
        padding: 1rem;
        transition: border-color .15s ease, background-color .15s ease, box-shadow .15s ease;
        cursor: pointer;
    }

    .attachment-dropzone:hover {
        border-color: #2563eb;
        background-color: #eff6ff;
        box-shadow: 0 10px 24px rgba(37, 99, 235, 0.08);
    }

    .attachment-dropzone.dz-started .dz-message {
        display: none;
    }

    .attachment-dropzone .dz-preview {
        margin: 0;
        width: 100%;
        max-width: 100%;
    }

    .attachment-dropzone .dz-preview .dz-image {
        width: 100%;
        height: 160px;
        border-radius: 0.75rem;
        overflow: hidden;
        background: #fff;
    }

    .attachment-dropzone .dz-preview .dz-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .attachment-dropzone .dz-preview .dz-details {
        padding: 0.75rem 0.25rem 0;
    }

    .attachment-dropzone .dz-preview .dz-size,
    .attachment-dropzone .dz-preview .dz-filename {
        color: #334155;
        font-weight: 600;
    }

    .attachment-dropzone .dz-preview .dz-remove {
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

    .attachment-dropzone .dz-preview .dz-remove:hover {
        background: #fecaca;
        color: #991b1b;
    }

    .attachment-dropzone .dz-message {
        margin: 0;
    }

    html[data-theme='dark'] .attachment-dropzone {
        background: #0f172a;
        border-color: rgba(148, 163, 184, 0.32);
    }

    html[data-theme='dark'] .attachment-dropzone:hover {
        background: #111827;
        border-color: rgba(96, 165, 250, 0.55);
    }

    html[data-theme='dark'] .attachment-dropzone .dz-preview .dz-image,
    html[data-theme='dark'] .attachment-dropzone .dz-preview .dz-details {
        background: #0f172a;
    }

    html[data-theme='dark'] .attachment-dropzone .dz-preview .dz-size,
    html[data-theme='dark'] .attachment-dropzone .dz-preview .dz-filename {
        color: #e2e8f0;
    }
</style>
