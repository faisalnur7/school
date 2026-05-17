<?php

namespace App\Http\Controllers;

class BudgetHubController extends Controller
{
    public function index()
    {
        $cards = [
            ['icon' => 'fa-sliders-h',  'title' => 'Budget Allocation', 'subtitle' => 'Manage budget allocations', 'route' => 'budget-allocations.index', 'permission' => 'view_card_budget_allocation', 'from' => '#4f46e5', 'to' => '#7c3aed'],
            ['icon' => 'fa-chart-bar',  'title' => 'Budget vs Actual',  'subtitle' => 'Compare budget vs actual',  'route' => 'budget-allocations.report', 'permission' => 'view_card_budget_vs_actual', 'from' => '#059669', 'to' => '#047857'],
        ];
        $cards = array_values(array_filter($cards, fn ($card) => auth()->user()?->hasPermission($card['permission'])));

        return view('pages.budget.hub', compact('cards'));
    }
}
