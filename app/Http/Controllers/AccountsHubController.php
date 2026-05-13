<?php

namespace App\Http\Controllers;

class AccountsHubController extends Controller
{
    public function index()
    {
        $sections = [
            'Cash Management' => [
                ['icon' => 'fa-university',     'title' => 'Bank Accounts',      'subtitle' => 'Manage bank accounts',        'route' => 'bank-accounts.index',        'from' => '#1d4ed8', 'to' => '#1e40af'],
                ['icon' => 'fa-mobile-alt',     'title' => 'Mobile Banking',     'subtitle' => 'Manage mobile banking',       'route' => 'mobile-banking-accounts.index', 'from' => '#0369a1', 'to' => '#075985'],
                ['icon' => 'fa-money-bill-wave','title' => 'Hand Cash',          'subtitle' => 'Manage hand cash',            'route' => 'hand-cash.index',            'from' => '#15803d', 'to' => '#166534'],
            ],
            'Accounting Setup' => [
                ['icon' => 'fa-layer-group',    'title' => 'Account Groups',       'subtitle' => 'Manage account groups',        'route' => 'account-groups.index',           'from' => '#4f46e5', 'to' => '#7c3aed'],
                ['icon' => 'fa-sitemap',        'title' => 'Chart of Accounts',    'subtitle' => 'Manage chart of accounts',     'route' => 'accounts-list.index',            'from' => '#0891b2', 'to' => '#0e7490'],
                ['icon' => 'fa-calendar-alt',   'title' => 'Accounting Periods',   'subtitle' => 'Manage accounting periods',    'route' => 'accounting-periods.index',       'from' => '#059669', 'to' => '#047857'],
            ],
            'Reports' => [
                ['icon' => 'fa-book',           'title' => 'Ledger',               'subtitle' => 'View account ledger',          'route' => 'ledger.index',                   'from' => '#d97706', 'to' => '#b45309'],
                ['icon' => 'fa-balance-scale',  'title' => 'Trial Balance',        'subtitle' => 'View trial balance',           'route' => 'reports.trial-balance',          'from' => '#dc2626', 'to' => '#b91c1c'],
                ['icon' => 'fa-file-alt',       'title' => 'Balance Sheet',        'subtitle' => 'View balance sheet',           'route' => 'reports.balance-sheet',          'from' => '#7c3aed', 'to' => '#6d28d9'],
                ['icon' => 'fa-money-bill',     'title' => 'Cash Book',            'subtitle' => 'View cash book',               'route' => 'reports.cash-book',              'from' => '#0f766e', 'to' => '#0d9488'],
                ['icon' => 'fa-calendar-day',   'title' => 'Day Book',             'subtitle' => 'View day book',                'route' => 'reports.day-book',               'from' => '#b45309', 'to' => '#92400e'],
                ['icon' => 'fa-chart-line',     'title' => 'Income & Expenditure', 'subtitle' => 'Income & expenditure report',  'route' => 'reports.income-expenditure',     'from' => '#be185d', 'to' => '#9d174d'],
                ['icon' => 'fa-coins',          'title' => 'Cash Summary',         'subtitle' => 'View cash summary',            'route' => 'reports.cash-summary',           'from' => '#1d4ed8', 'to' => '#1e40af'],
                ['icon' => 'fa-file-invoice',   'title' => 'Receipt & Payment',    'subtitle' => 'Receipt & payment report',     'route' => 'reports.receipt-payment',        'from' => '#0369a1', 'to' => '#075985'],
                ['icon' => 'fa-water',          'title' => 'Cash Flow',            'subtitle' => 'View cash flow report',        'route' => 'reports.cash-flow',              'from' => '#15803d', 'to' => '#166534'],
            ],
        ];

        return view('pages.accounts.hub', compact('sections'));
    }
}
