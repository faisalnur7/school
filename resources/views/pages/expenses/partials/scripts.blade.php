<script>
    $(function () {
        const $accountSelect = $('#expenseAccountSelect');
        const $accountType = $('#expenseAccountType');

        function syncAccountType() {
            const selectedType = $accountSelect.find('option:selected').data('account-type') || '';
            $accountType.val(selectedType);
        }

        syncAccountType();
        $accountSelect.on('change', syncAccountType);

        if ($('.is-invalid').length > 0) {
            $('html, body').animate({
                scrollTop: $('.is-invalid').first().offset().top - 50
            }, 300);
        }
    });
</script>
