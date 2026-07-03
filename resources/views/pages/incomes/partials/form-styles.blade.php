<style>
    .income-form-shell {
        padding: 1rem;
    }

    .income-form-hero {
        background: linear-gradient(135deg, #0f172a 0%, #1e293b 55%, #2563eb 100%);
        color: #fff;
        border-radius: 1rem;
        padding: 1.25rem 1.5rem;
        box-shadow: 0 18px 40px rgba(15, 23, 42, 0.18);
        margin-bottom: 1rem;
    }

    .income-form-hero .badge {
        background: rgba(255, 255, 255, 0.12);
        color: #e2e8f0;
        border: 1px solid rgba(255, 255, 255, 0.16);
        font-weight: 600;
        letter-spacing: 0.01em;
    }

    .income-form-card {
        border: 0;
        border-radius: 1rem;
        overflow: hidden;
        box-shadow: 0 12px 30px rgba(15, 23, 42, 0.08);
    }

    .income-form-card .card-header {
        background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
        border-bottom: 0;
        color: #fff;
        padding: 1rem 1.25rem;
    }

    .income-form-card .card-body {
        padding: 1.25rem;
        background: #fff;
    }

    .income-section {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 0.9rem;
        padding: 1rem;
        margin-bottom: 1rem;
    }

    .income-section-title {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        margin-bottom: 0.85rem;
        font-size: 0.82rem;
        font-weight: 700;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        color: #475569;
    }

    .income-section-title i {
        width: 1.7rem;
        height: 1.7rem;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 999px;
        background: #fff;
        color: #2563eb;
        box-shadow: 0 4px 12px rgba(37, 99, 235, 0.14);
    }

    .income-side-card {
        border: 1px solid #e2e8f0;
        border-radius: 1rem;
        background: linear-gradient(180deg, #fff 0%, #f8fafc 100%);
        box-shadow: 0 12px 30px rgba(15, 23, 42, 0.06);
        overflow: hidden;
    }

    .income-side-card .side-head {
        padding: 1rem 1rem 0.75rem;
        border-bottom: 1px solid #e2e8f0;
        background: #fff;
    }

    .income-side-card .side-body {
        padding: 1rem;
    }

    .income-summary-list {
        display: grid;
        gap: 0.75rem;
    }

    .income-summary-item {
        border: 1px solid #e2e8f0;
        background: #fff;
        border-radius: 0.85rem;
        padding: 0.8rem 0.9rem;
    }

    .income-summary-item .label {
        font-size: 0.72rem;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        color: #64748b;
        margin-bottom: 0.2rem;
        font-weight: 700;
    }

    .income-summary-item .value {
        font-size: 0.95rem;
        color: #0f172a;
        font-weight: 700;
        word-break: break-word;
    }

    .income-upload {
        border: 1px dashed #cbd5e1;
        border-radius: 0.9rem;
        background: #f8fafc;
        padding: 1rem;
    }

    .income-upload .current-attachment {
        margin-bottom: 0.75rem;
        padding: 0.75rem;
        border-radius: 0.75rem;
        background: #fff;
        border: 1px solid #e2e8f0;
    }

    .income-actions {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 0.75rem;
        flex-wrap: wrap;
    }

    .income-actions .btn {
        min-width: 120px;
    }

    html[data-theme='dark'] .income-form-card .card-body,
    html[data-theme='dark'] .income-side-card,
    html[data-theme='dark'] .income-summary-item,
    html[data-theme='dark'] .income-upload .current-attachment {
        background: #0f172a;
        color: #e2e8f0;
    }

    html[data-theme='dark'] .income-section,
    html[data-theme='dark'] .income-side-card,
    html[data-theme='dark'] .income-summary-item,
    html[data-theme='dark'] .income-upload {
        border-color: rgba(148, 163, 184, 0.22);
    }

    html[data-theme='dark'] .income-section-title,
    html[data-theme='dark'] .income-summary-item .label {
        color: #cbd5e1;
    }

    html[data-theme='dark'] .income-summary-item .value,
    html[data-theme='dark'] .income-form-hero,
    html[data-theme='dark'] .income-form-card .card-header {
        color: #fff;
    }
</style>
