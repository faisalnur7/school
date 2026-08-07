<?php

namespace App\Http\Controllers;

class FeesHubController extends Controller
{
    public function index()
    {
        $sections = [
            __('Fee Settings & Payment Collection') => [
                ['icon' => 'fa-hand-holding-usd', 'title' => __('Collect Payments'), 'subtitle' => __('Collect student payments'), 'route' => 'fees.collect_payment', 'permission' => 'view_card_collect_payments', 'from' => '#dc2626', 'to' => '#b91c1c'],
                ['icon' => 'fa-tags',           'title' => __('Fee Categories'),     'subtitle' => __('Manage fee categories'),       'route' => 'fee-categories.index', 'permission' => 'view_card_fee_categories', 'from' => '#4f46e5', 'to' => '#7c3aed'],
                ['icon' => 'fa-layer-group',    'title' => __('Fee Sets'),           'subtitle' => __('Manage fee sets'),             'route' => 'fee-sets.index', 'permission' => 'view_card_fee_sets', 'from' => '#0891b2', 'to' => '#0e7490'],
                ['icon' => 'fa-award',          'title' => __('Scholarships'),       'subtitle' => __('Manage scholarships'),         'route' => 'scholarships.index', 'permission' => 'view_card_scholarships', 'from' => '#059669', 'to' => '#047857'],
                ['icon' => 'fa-graduation-cap', 'title' => __('Free Studentship'),   'subtitle' => __('Manage free studentships'),    'route' => 'free-studentships.index', 'permission' => 'view_card_free_studentships', 'from' => '#7c3aed', 'to' => '#6d28d9'],
                ['icon' => 'fa-bus',            'title' => __('Transport Fees'),     'subtitle' => __('Manage transport fees'),       'route' => 'transports.index', 'permission' => 'view_card_transport_fees', 'from' => '#d97706', 'to' => '#b45309'],
            ],
            __('Reports') => [
                ['icon' => 'fa-chart-pie',      'title' => __('Student Payment Report'),     'subtitle' => __('Category-wise payment report'),'route' => 'fees.payment-report', 'permission' => 'view_card_student_payment_report', 'from' => '#0ea5e9', 'to' => '#0369a1'],
                ['icon' => 'fa-receipt',        'title' => __('Student Receive Report'),     'subtitle' => __('Monthwise receive report'),        'route' => 'fees.student-receive-report', 'permission' => 'view_card_student_receive_report', 'from' => '#10b981', 'to' => '#047857'],
                ['icon' => 'fa-file-invoice-dollar', 'title' => __('Student Receivable Report'), 'subtitle' => __('Category-wise assigned fees by month'), 'route' => 'fees.student-receivable-report', 'permission' => 'view_card_student_receivable_report', 'from' => '#f59e0b', 'to' => '#d97706'],
                ['icon' => 'fa-layer-group',    'title' => __('All In One Report'),    'subtitle' => __('Payment, receive and receivable reports'), 'route' => 'fees.all-in-one-report', 'permission' => 'view_card_student_payment_report', 'from' => '#8b5cf6', 'to' => '#4f46e5'],
                ['icon' => 'fa-file-invoice',   'title' => __('Classwise Due'),      'subtitle' => __('Classwise due report'),        'route' => 'fees.due-report', 'permission' => 'view_card_classwise_due_report', 'from' => '#0f766e', 'to' => '#0d9488'],
                ['icon' => 'fa-user-clock',     'title' => __('Student Due'),        'subtitle' => __('Fee and inventory due report'), 'route' => 'fees.student-due-report', 'permission' => 'view_card_student_due_report', 'from' => '#b45309', 'to' => '#92400e'],
                ['icon' => 'fa-percentage',     'title' => __('Discount List'),      'subtitle' => __('View discount list'),          'route' => 'fees.discount-list', 'permission' => 'view_card_discount_list', 'from' => '#be185d', 'to' => '#9d174d'],
            ],
        ];
        foreach ($sections as $sectionName => $cards) {
            $sections[$sectionName] = array_values(array_filter($cards, fn ($card) => auth()->user()?->hasPermission($card['permission'])));
        }

        return view('pages.fees.hub', compact('sections'));
    }
}
