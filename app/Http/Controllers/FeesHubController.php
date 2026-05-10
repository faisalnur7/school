<?php

namespace App\Http\Controllers;

class FeesHubController extends Controller
{
    public function index()
    {
        $cards = [
            ['icon' => 'fa-tags',           'title' => 'Fee Categories',     'subtitle' => 'Manage fee categories',       'route' => 'fee-categories.index',       'from' => '#4f46e5', 'to' => '#7c3aed'],
            ['icon' => 'fa-layer-group',    'title' => 'Fee Sets',           'subtitle' => 'Manage fee sets',             'route' => 'fee-sets.index',             'from' => '#0891b2', 'to' => '#0e7490'],
            ['icon' => 'fa-award',          'title' => 'Scholarships',       'subtitle' => 'Manage scholarships',         'route' => 'scholarships.index',         'from' => '#059669', 'to' => '#047857'],
            ['icon' => 'fa-bus',            'title' => 'Transport Fees',     'subtitle' => 'Manage transport fees',       'route' => 'transports.index',           'from' => '#d97706', 'to' => '#b45309'],
            ['icon' => 'fa-hand-holding-usd', 'title' => 'Collect Payments', 'subtitle' => 'Collect student payments',   'route' => 'fees.collect',               'from' => '#dc2626', 'to' => '#b91c1c'],
            ['icon' => 'fa-receipt',        'title' => 'Student Payments',   'subtitle' => 'View payment history',        'route' => 'payments.index',             'from' => '#7c3aed', 'to' => '#6d28d9'],
            ['icon' => 'fa-file-invoice',   'title' => 'Classwise Due',      'subtitle' => 'Classwise due report',        'route' => 'fees.due-report',            'from' => '#0f766e', 'to' => '#0d9488'],
            ['icon' => 'fa-user-clock',     'title' => 'Student Due',        'subtitle' => 'Student due report',          'route' => 'fees.student-due-report',    'from' => '#b45309', 'to' => '#92400e'],
            ['icon' => 'fa-percentage',     'title' => 'Discount List',      'subtitle' => 'View discount list',          'route' => 'fees.discount-list',         'from' => '#be185d', 'to' => '#9d174d'],
            ['icon' => 'fa-university',     'title' => 'Bank Accounts',      'subtitle' => 'Manage bank accounts',        'route' => 'bank-accounts.index',        'from' => '#1d4ed8', 'to' => '#1e40af'],
            ['icon' => 'fa-mobile-alt',     'title' => 'Mobile Banking',     'subtitle' => 'Manage mobile banking',       'route' => 'mobile-banking-accounts.index', 'from' => '#0369a1', 'to' => '#075985'],
            ['icon' => 'fa-money-bill-wave','title' => 'Hand Cash',          'subtitle' => 'Manage hand cash',            'route' => 'hand-cash.index',            'from' => '#15803d', 'to' => '#166534'],
        ];

        return view('pages.fees.hub', compact('cards'));
    }
}
