<script>
    $(function () {
        const accountsUrl = '{{ route('accounts.index') }}';

        const methodTypeMap = {
            'Cash': 'hand_cash',
            'Bank Transfer': 'bank',
            'Mobile Banking': 'mobile',
        };
        const accountTypeMap = {
            'Cash': 'App\\Models\\HandCash',
            'Bank Transfer': 'App\\Models\\BankAccount',
            'Mobile Banking': 'App\\Models\\MobileBankingAccount',
        };

        const $accountSelect = $('#transactionAccountSelect');
        if ($accountSelect.length) {
            $accountSelect.attr('data-selected', $accountSelect.data('selected') || '');
        }

        function loadTransactionAccounts(method) {
            const type = methodTypeMap[method];
            const accountType = accountTypeMap[method];
            const $wrapper = $('#transactionAccountWrapper');
            const selectedId = $accountSelect.data('selected');

            if (!type) {
                $wrapper.hide();
                $('#transactionAccountType').val('');
                $accountSelect.html('<option value="">Select Account</option>');
                return;
            }

            $('#transactionAccountType').val(accountType);

            $.ajax({
                url: accountsUrl,
                method: 'GET',
                dataType: 'json',
                data: { type: type },
                success: function (accounts) {
                    $accountSelect.html('<option value="">Select Account</option>');
                    accounts.forEach(function (a) {
                        const isSelected = selectedId && String(selectedId) === String(a.id);
                        $accountSelect.append(`<option value="${a.id}" ${isSelected ? 'selected' : ''}>${a.label}</option>`);
                    });
                    $wrapper.toggle(accounts.length > 0);
                    $accountSelect.data('selected', '');
                },
                error: function () {
                    $wrapper.hide();
                }
            });
        }

        $('#paymentMethod').on('change', function () {
            loadTransactionAccounts($(this).val());
        });

        loadTransactionAccounts($('#paymentMethod').val());

        if ($('.is-invalid').length > 0) {
            $('html, body').animate({
                scrollTop: $('.is-invalid').first().offset().top - 50
            }, 300);
        }
    });
</script>
