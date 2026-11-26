<style>
    .report-toolbar {
        display: flex;
        align-items: stretch;
        justify-content: space-between;
        gap: 1rem;
        margin-bottom: 1rem;
        flex-wrap: wrap;
    }

    .report-toolbar .supplier-dues-filters {
        flex: 1 1 0;
        margin-bottom: 0 !important;
    }

    .report-toolbar .report-toolbar-actions {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        flex: 0 0 auto;
    }

    @media (max-width: 767.98px) {
        .report-toolbar .report-toolbar-actions {
            width: 100%;
        }

        .report-toolbar .report-toolbar-actions .btn {
            flex: 1 1 0;
        }
    }

    .supplier-dues-filters {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 0.75rem;
        padding: 1rem;
    }

    /* F-31: fields and buttons share one baseline; the button group never wraps under the inputs */
    .supplier-dues-filters .row { align-items: flex-end; }
    .supplier-dues-filters .row > .d-flex {
        flex: 0 0 auto;
        width: auto;
        max-width: none;
        flex-wrap: nowrap;
        align-items: flex-end;
        gap: 0.5rem;
    }
    .supplier-dues-filters .btn {
        min-width: 0;
        height: calc(1.5em + 0.75rem + 2px);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.35rem;
        white-space: nowrap;
    }
    .supplier-dues-filters .btn-light { width: calc(1.5em + 0.75rem + 2px); padding: 0; }

    html[data-theme='dark'] .supplier-dues-filters {
        background: #0f172a;
        border-color: rgba(148, 163, 184, 0.22);
    }
</style>
