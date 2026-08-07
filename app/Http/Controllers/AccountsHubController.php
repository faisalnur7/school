<?php

namespace App\Http\Controllers;

class AccountsHubController extends Controller
{
    public function index()
    {
        $sections = [
            __('Reports') => [
                ['icon' => 'fa-exchange-alt',   'title' => __('Transactions'),        'subtitle' => __('View all transactions'),        'route' => 'transactions.index', 'permission' => 'view_card_transactions', 'from' => '#4f46e5', 'to' => '#7c3aed'],
                ['icon' => 'fa-book',           'title' => __('Ledger'),               'subtitle' => __('View account ledger'),          'route' => 'ledger.index', 'permission' => 'view_card_ledger', 'from' => '#d97706', 'to' => '#b45309'],
                ['icon' => 'fa-balance-scale',  'title' => __('Trial Balance'),        'subtitle' => __('View trial balance'),           'route' => 'reports.trial-balance', 'permission' => 'view_card_trial_balance', 'from' => '#dc2626', 'to' => '#b91c1c'],
                ['icon' => 'fa-list-alt',        'title' => __('Detailed Trial Balance'),'subtitle' => __('Category-wise breakdown of all transactions'), 'route' => 'reports.details-trial-balance', 'permission' => 'view_card_detailed_trial_balance', 'from' => '#7c3aed', 'to' => '#6d28d9'],
                ['icon' => 'fa-file-alt',       'title' => __('Balance Sheet'),        'subtitle' => __('View balance sheet'),           'route' => 'reports.balance-sheet', 'permission' => 'view_card_balance_sheet', 'from' => '#7c3aed', 'to' => '#6d28d9'],
                ['icon' => 'fa-money-bill',     'title' => __('Cash Book'),            'subtitle' => __('View cash book'),               'route' => 'reports.cash-book', 'permission' => 'view_card_cash_book', 'from' => '#0f766e', 'to' => '#0d9488'],
                ['icon' => 'fa-calendar-day',   'title' => __('Day Book'),             'subtitle' => __('View day book'),                'route' => 'reports.day-book', 'permission' => 'view_card_day_book', 'from' => '#b45309', 'to' => '#92400e'],
                ['icon' => 'fa-chart-line',     'title' => __('Income & Expenditure'), 'subtitle' => __('Income & expenditure report'),  'route' => 'reports.income-expenditure', 'permission' => 'view_card_income_expenditure', 'from' => '#be185d', 'to' => '#9d174d'],
                ['icon' => 'fa-coins',          'title' => __('Cash Summary'),         'subtitle' => __('View cash summary'),            'route' => 'reports.cash-summary', 'permission' => 'view_card_cash_summary', 'from' => '#1d4ed8', 'to' => '#1e40af'],
                ['icon' => 'fa-file-invoice',   'title' => __('Receipt & Payment'),    'subtitle' => __('Receipt & payment report'),     'route' => 'reports.receipt-payment', 'permission' => 'view_card_receipt_payment', 'from' => '#0369a1', 'to' => '#075985'],
                ['icon' => 'fa-truck',          'title' => __('Supplier Due'),         'subtitle' => __('Outstanding supplier invoices'),'route' => 'reports.supplier-dues', 'permission' => 'view_supplier_due_report', 'from' => '#ea580c', 'to' => '#c2410c'],
                ['icon' => 'fa-water',          'title' => __('Cash Flow'),            'subtitle' => __('View cash flow report'),        'route' => 'reports.cash-flow', 'permission' => 'view_card_cash_flow', 'from' => '#15803d', 'to' => '#166534'],
            ],
            __('Cash Management') => [
                ['icon' => 'fa-university',     'title' => __('Bank Accounts'),      'subtitle' => __('Manage bank accounts'),        'route' => 'bank-accounts.index', 'permission' => 'view_card_bank_accounts', 'from' => '#1d4ed8', 'to' => '#1e40af'],
                ['icon' => 'fa-mobile-alt',     'title' => __('Mobile Banking'),     'subtitle' => __('Manage mobile banking'),       'route' => 'mobile-banking-accounts.index', 'permission' => 'view_card_mobile_banking', 'from' => '#0369a1', 'to' => '#075985'],
                ['icon' => 'fa-money-bill-wave','title' => __('Hand Cash'),          'subtitle' => __('Manage hand cash'),            'route' => 'hand-cash.index', 'permission' => 'view_card_hand_cash', 'from' => '#15803d', 'to' => '#166534'],
            ],
            __('Accounting Setup') => [
                ['icon' => 'fa-layer-group',    'title' => __('Account Groups'),       'subtitle' => __('Manage account groups'),        'route' => 'account-groups.index', 'permission' => 'view_card_account_groups', 'from' => '#4f46e5', 'to' => '#7c3aed'],
                ['icon' => 'fa-sitemap',        'title' => __('Chart of Accounts'),    'subtitle' => __('Manage chart of accounts'),     'route' => 'accounts-list.index', 'permission' => 'view_card_chart_of_accounts', 'from' => '#0891b2', 'to' => '#0e7490'],
                ['icon' => 'fa-calendar-alt',   'title' => __('Accounting Periods'),   'subtitle' => __('Manage accounting periods'),    'route' => 'accounting-periods.index', 'permission' => 'view_card_accounting_periods', 'from' => '#059669', 'to' => '#047857'],
            ],
            
        ];
        foreach ($sections as $sectionName => $cards) {
            $sections[$sectionName] = array_values(array_filter($cards, fn ($card) => auth()->user()?->hasPermission($card['permission'])));
        }

        return view('pages.accounts.hub', compact('sections'));
    }
}
