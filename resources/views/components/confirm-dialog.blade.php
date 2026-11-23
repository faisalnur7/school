{{--
    Shared confirm dialog (F-29). Replaces the browser's OK/Cancel box.
    Put data-confirm on any <form> (or a submit button / link):
        <form … data-confirm="Delete class “Six”?" data-confirm-action="Delete class" data-confirm-cancel="Keep class">
    data-confirm-text adds an optional line of detail. data-confirm-tone="primary" uses a non-destructive button.
--}}
<div class="modal fade" id="dsConfirmDialog" tabindex="-1" role="dialog" aria-modal="true" aria-labelledby="dsConfirmTitle" aria-describedby="dsConfirmText">
    <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
        <div class="modal-content ds-confirm">
            <div class="modal-body">
                <h2 class="ds-confirm__title" id="dsConfirmTitle"></h2>
                <p class="ds-confirm__text" id="dsConfirmText"></p>
            </div>
            <div class="modal-footer ds-confirm__actions">
                <button type="button" class="ds-btn ds-btn--secondary" data-dismiss="modal" data-confirm-cancel-btn></button>
                <button type="button" class="ds-btn ds-btn--danger" data-confirm-ok-btn></button>
            </div>
        </div>
    </div>
</div>
<script>
(function () {
    var pending = null;
    var $dialog = null;
    var T = {
        title: @json(__('Are you sure?')),
        action: @json(__('Confirm')),
        cancel: @json(__('Cancel')),
    };

    function open(el, proceed) {
        $dialog = $dialog || $('#dsConfirmDialog');
        var d = el.dataset;
        $('#dsConfirmTitle').text(d.confirm || T.title);
        $('#dsConfirmText').text(d.confirmText || '').toggle(!!d.confirmText);
        $dialog.find('[data-confirm-cancel-btn]').text(d.confirmCancel || T.cancel);
        $dialog.find('[data-confirm-ok-btn]').text(d.confirmAction || T.action)
            .toggleClass('ds-btn--danger', d.confirmTone !== 'primary')
            .toggleClass('ds-btn--primary', d.confirmTone === 'primary');
        pending = proceed;
        $dialog.modal('show');
    }

    document.addEventListener('submit', function (e) {
        var form = e.target;
        if (!form.matches || !form.matches('form[data-confirm]') || form.dataset.confirmed === '1') { return; }
        e.preventDefault();
        e.stopImmediatePropagation();
        var submitter = e.submitter || null;
        open(form, function () {
            // Re-dispatch a normal submit so the form's own handlers still run; the flag lets it through once.
            form.dataset.confirmed = '1';
            if (form.requestSubmit) { form.requestSubmit(submitter && submitter.form === form ? submitter : undefined); }
            else { HTMLFormElement.prototype.submit.call(form); }
            delete form.dataset.confirmed;
        });
    }, true);

    document.addEventListener('click', function (e) {
        var el = e.target.closest && e.target.closest('a[data-confirm], button[data-confirm]');
        if (!el || el.dataset.confirmed === '1') { return; }
        e.preventDefault();
        e.stopImmediatePropagation();
        open(el, function () { el.dataset.confirmed = '1'; el.click(); delete el.dataset.confirmed; });
    }, true);

    $(document).on('click', '#dsConfirmDialog [data-confirm-ok-btn]', function () {
        var go = pending; pending = null;
        $('#dsConfirmDialog').modal('hide');
        if (go) { go(); }
    });
    $(document).on('shown.bs.modal', '#dsConfirmDialog', function () {
        $(this).find('[data-confirm-cancel-btn]').trigger('focus');
    });
})();
</script>
