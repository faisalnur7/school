@extends('layouts.master')

@section('styles')
    @include('components.form-styles')
    @include('pages.incomes.partials.form-styles')
    @include('components.dropzone-attachment-styles')
    <link rel="stylesheet" href="{{ asset('assets/plugins/dropzone/min/dropzone.min.css') }}">
@endsection

@section('contents')
    @include('pages.incomes.partials.form', [
        'formAction' => route('incomes.store'),
        'formMethod' => 'POST',
        'pageTitle' => 'Record Income',
        'pageIcon' => 'fa-plus-circle',
        'submitLabel' => 'Create Income',
        'submitIcon' => 'fa-save',
        'backRoute' => route('incomes.index'),
    ])
@endsection

@section('scripts')
    <script src="{{ asset('assets/plugins/dropzone/min/dropzone.min.js') }}"></script>
    @include('components.dropzone-attachment-script')
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

            function loadIncomeAccounts(method) {
                const type = methodTypeMap[method];
                const accountType = accountTypeMap[method];
                const $wrapper = $('#incomeAccountWrapper');
                const $select = $('#incomeAccountSelect');
                const selectedId = $select.data('selected');

                if (!type) {
                    $('#incomeAccountType').val('');
                    $select.html('<option value="">Select Account</option>');
                    $wrapper.hide();
                    return;
                }

                $('#incomeAccountType').val(accountType);

                $.ajax({
                    url: accountsUrl,
                    method: 'GET',
                    dataType: 'json',
                    data: {
                        type: type
                    },
                    success: function (accounts) {
                        $select.html('<option value="">Select Account</option>');
                        accounts.forEach(function (a) {
                            const isSelected = selectedId && String(selectedId) === String(a.id);
                            $select.append(`<option value="${a.id}" ${isSelected ? 'selected' : ''}>${a.label}</option>`);
                        });
                        $wrapper.toggle(accounts.length > 0);
                        $select.data('selected', '');
                    },
                    error: function () {
                        $wrapper.hide();
                    }
                });
            }

            $('#paymentMethod').on('change', function () {
                $('#incomeAccountSelect').data('selected', '');
                loadIncomeAccounts($(this).val());
            });

            loadIncomeAccounts($('#paymentMethod').val());

            if ($('.is-invalid').length > 0) {
                $('html, body').animate({
                    scrollTop: $('.is-invalid').first().offset().top - 50
                }, 300);
            }
        });
    </script>
@endsection
