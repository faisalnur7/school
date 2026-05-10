<?php

namespace App\Http\Controllers;

class BudgetHubController extends Controller
{
    public function index()
    {
        $cards = [
            ['icon' => 'fa-sliders-h',  'title' => 'Budget Allocation', 'subtitle' => 'Manage budget allocations', 'route' => 'budget-allocations.index',  'from' => '#4f46e5', 'to' => '#7c3aed'],
            ['icon' => 'fa-chart-bar',  'title' => 'Budget vs Actual',  'subtitle' => 'Compare budget vs actual',  'route' => 'budget-allocations.report', 'from' => '#059669', 'to' => '#047857'],
        ];

        return view('pages.budget.hub', compact('cards'));
    }
}
