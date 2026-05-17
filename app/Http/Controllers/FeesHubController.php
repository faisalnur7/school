<?php

namespace App\Http\Controllers;

class FeesHubController extends Controller
{
    public function index()
    {
        $sections = [
            'Fee Settings & Payment Collection' => [
                ['icon' => 'fa-hand-holding-usd', 'title' => 'Collect Payments', 'subtitle' => 'Collect student payments',   'route' => 'fees.collect', 'permission' => 'view_card_collect_payments', 'from' => '#dc2626', 'to' => '#b91c1c'],
                ['icon' => 'fa-tags',           'title' => 'Fee Categories',     'subtitle' => 'Manage fee categories',       'route' => 'fee-categories.index', 'permission' => 'view_card_fee_categories', 'from' => '#4f46e5', 'to' => '#7c3aed'],
                ['icon' => 'fa-layer-group',    'title' => 'Fee Sets',           'subtitle' => 'Manage fee sets',             'route' => 'fee-sets.index', 'permission' => 'view_card_fee_sets', 'from' => '#0891b2', 'to' => '#0e7490'],
                ['icon' => 'fa-award',          'title' => 'Scholarships',       'subtitle' => 'Manage scholarships',         'route' => 'scholarships.index', 'permission' => 'view_card_scholarships', 'from' => '#059669', 'to' => '#047857'],
                ['icon' => 'fa-graduation-cap', 'title' => 'Free Studentship',   'subtitle' => 'Manage free studentships',    'route' => 'free-studentships.index', 'permission' => 'view_card_free_studentships', 'from' => '#7c3aed', 'to' => '#6d28d9'],
                ['icon' => 'fa-bus',            'title' => 'Transport Fees',     'subtitle' => 'Manage transport fees',       'route' => 'transports.index', 'permission' => 'view_card_transport_fees', 'from' => '#d97706', 'to' => '#b45309'],
            ],
            'Reports' => [
                ['icon' => 'fa-chart-pie',      'title' =>'Student Payment Report',     'subtitle' => 'Category-wise payment report','route' => 'fees.payment-report', 'permission' => 'view_card_student_payment_report', 'from' => '#0ea5e9', 'to' => '#0369a1'],
                ['icon' => 'fa-receipt',        'title' => 'Student Receive Report',     'subtitle' => 'Monthwise receive report',        'route' => 'fees.student-receive-report', 'permission' => 'view_card_student_receive_report', 'from' => '#10b981', 'to' => '#047857'],
                ['icon' => 'fa-file-invoice-dollar', 'title' => 'Student Receivable Report', 'subtitle' => 'Category-wise assigned fees by month', 'route' => 'fees.student-receivable-report', 'permission' => 'view_card_student_receivable_report', 'from' => '#f59e0b', 'to' => '#d97706'],
                ['icon' => 'fa-file-invoice',   'title' => 'Classwise Due',      'subtitle' => 'Classwise due report',        'route' => 'fees.due-report', 'permission' => 'view_card_classwise_due_report', 'from' => '#0f766e', 'to' => '#0d9488'],
                ['icon' => 'fa-user-clock',     'title' => 'Student Due',        'subtitle' => 'Student due report',          'route' => 'fees.student-due-report', 'permission' => 'view_card_student_due_report', 'from' => '#b45309', 'to' => '#92400e'],
                ['icon' => 'fa-percentage',     'title' => 'Discount List',      'subtitle' => 'View discount list',          'route' => 'fees.discount-list', 'permission' => 'view_card_discount_list', 'from' => '#be185d', 'to' => '#9d174d'],
            ],
        ];
        foreach ($sections as $sectionName => $cards) {
            $sections[$sectionName] = array_values(array_filter($cards, fn ($card) => auth()->user()?->hasPermission($card['permission'])));
        }

        return view('pages.fees.hub', compact('sections'));
    }
}
