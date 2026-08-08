<?php

namespace App\Http\Controllers;

class StudentsHubController extends Controller
{
    public function index()
    {
        $cards = [
            ['icon' => 'fa-list',               'title' => __('Student List'),      'subtitle' => __('View all students'),                    'route' => 'students.index', 'permission' => 'view_card_student_list', 'from' => '#4f46e5', 'to' => '#7c3aed'],
            ['icon' => 'fa-chalkboard-teacher', 'title' => __('Assign Teacher'),    'subtitle' => __('Assign teachers to sections'),          'route' => 'teacher-section-assignments.index', 'permission' => 'view_card_assign_teacher', 'from' => '#059669', 'to' => '#047857'],
            ['icon' => 'fa-id-card',            'title' => __('Generate ID Cards'), 'subtitle' => __('Print student ID cards'),               'route' => 'students.id-cards', 'permission' => 'view_card_generate_id_cards', 'from' => '#d97706', 'to' => '#b45309'],
            ['icon' => 'fa-birthday-cake',      'title' => __('Birthdays'),         'subtitle' => __('Find students by birthday'),            'route' => 'students.birthdays', 'permission' => 'view_card_student_birthdays', 'from' => '#db2777', 'to' => '#9d174d'],
            ['icon' => 'fa-user-plus',          'title' => __('New Admission'),     'subtitle' => __('Enroll a new student for current session'), 'route' => 'students.admission', 'permission' => 'view_card_new_admission', 'from' => '#0891b2', 'to' => '#0e7490'],
            ['icon' => 'fa-arrow-up',           'title' => __('Promote Students'),  'subtitle' => __('Promote or retain students to next session'), 'route' => 'students.promote', 'permission' => 'view_card_promote_students', 'from' => '#059669', 'to' => '#047857'],
            ['icon' => 'fa-edit',               'title' => __('Mid-Year Correction'),'subtitle' => __('Update class/section within same session'), 'route' => 'students.correction', 'permission' => 'view_card_mid_year_correction', 'from' => '#d97706', 'to' => '#b45309'],
            ['icon' => 'fa-sign-out-alt',       'title' => __('Student Checkout'),  'subtitle' => __('Transfer, graduate, withdraw, or expel'),   'route' => 'students.checkout', 'permission' => 'view_card_student_checkout', 'from' => '#dc2626', 'to' => '#b91c1c'],
            ['icon' => 'fa-user-times',         'title' => __('Checked Out List'),  'subtitle' => __('View all checked out students'),            'route' => 'students.checked-out', 'permission' => 'view_card_student_checkout', 'from' => '#7f1d1d', 'to' => '#991b1b'],
            ['icon' => 'fa-history',            'title' => __('Academic History'),   'subtitle' => __('View full session history per student'),    'route' => 'students.history', 'permission' => 'view_card_academic_history', 'from' => '#7c3aed', 'to' => '#6d28d9'],
            ['icon' => 'fa-certificate',        'title' => __('Certificate Hub'),     'subtitle' => __('Transfer Certificate, Testimonial & templates'), 'route' => 'students.certificates', 'permission' => 'view_card_student_certificates', 'from' => '#0f766e', 'to' => '#0d9488'],
        ];
        $cards = array_values(array_filter($cards, fn ($card) => auth()->user()?->hasPermission($card['permission'])));

        return view('pages.students.hub', compact('cards'));
    }
}
