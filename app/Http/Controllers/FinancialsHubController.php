<?php

namespace App\Http\Controllers;

class FinancialsHubController extends Controller
{
    public function index()
    {
        $cards = [
            ['icon' => 'fa-arrow-circle-down', 'title' => 'Incomes',             'subtitle' => 'Manage income records',       'route' => 'incomes.index',            'from' => '#059669', 'to' => '#047857'],
            ['icon' => 'fa-arrow-circle-up',   'title' => 'Expenses',            'subtitle' => 'Manage expense records',      'route' => 'expenses.index',           'from' => '#dc2626', 'to' => '#b91c1c'],
            ['icon' => 'fa-coins',             'title' => 'Capital',             'subtitle' => 'Shareholder capital entries', 'route' => 'shareholder-transactions.create', 'from' => '#d97706', 'to' => '#b45309'],
            ['icon' => 'fa-exchange-alt',      'title' => 'Transactions',        'subtitle' => 'View all transactions',       'route' => 'transactions.index',       'from' => '#4f46e5', 'to' => '#7c3aed'],
            ['icon' => 'fa-folder-plus',       'title' => 'Income Categories',   'subtitle' => 'Manage income categories',    'route' => 'income-categories.index',  'from' => '#0891b2', 'to' => '#0e7490'],
            ['icon' => 'fa-folder-minus',      'title' => 'Expense Categories',  'subtitle' => 'Manage expense categories',   'route' => 'expense-categories.index', 'from' => '#7c3aed', 'to' => '#6d28d9'],
        ];

        return view('pages.financials.hub', compact('cards'));
    }
}
