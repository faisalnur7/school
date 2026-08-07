<?php

namespace App\Http\Controllers;

class FinancialsHubController extends Controller
{
    public function index()
    {
        $cards = [
            ['icon' => 'fa-arrow-circle-down', 'title' => __('Incomes'),             'subtitle' => __('Manage income records'),       'route' => 'incomes.index', 'permission' => 'view_card_incomes', 'from' => '#059669', 'to' => '#047857'],
            ['icon' => 'fa-arrow-circle-up',   'title' => __('Expenses'),            'subtitle' => __('Manage expense records'),      'route' => 'expenses.index', 'permission' => 'view_card_expenses', 'from' => '#dc2626', 'to' => '#b91c1c'],
            ['icon' => 'fa-coins',             'title' => __('Capital'),             'subtitle' => __('Shareholder capital entries'), 'route' => 'shareholder-transactions.index', 'href' => route('shareholder-transactions.index', ['type' => 'capital']), 'permission' => 'view_card_capital', 'from' => '#d97706', 'to' => '#b45309'],
            ['icon' => 'fa-folder-plus',       'title' => __('Income Categories'),   'subtitle' => __('Manage income categories'),    'route' => 'income-categories.index', 'permission' => 'view_card_income_categories', 'from' => '#0891b2', 'to' => '#0e7490'],
            ['icon' => 'fa-folder-minus',      'title' => __('Expense Categories'),  'subtitle' => __('Manage expense categories'),   'route' => 'expense-categories.index', 'permission' => 'view_card_expense_categories', 'from' => '#7c3aed', 'to' => '#6d28d9'],
        ];
        $cards = array_values(array_filter($cards, fn ($card) => auth()->user()?->hasPermission($card['permission'])));

        return view('pages.financials.hub', compact('cards'));
    }
}
