<?php

namespace App\Http\Controllers;

class StudentsHubController extends Controller
{
    public function index()
    {
        $cards = [
            ['icon' => 'fa-list',           'title' => 'Student List',    'subtitle' => 'View all students',           'route' => 'students.index',                    'from' => '#4f46e5', 'to' => '#7c3aed'],
            ['icon' => 'fa-user-plus',      'title' => 'Add Student',     'subtitle' => 'Enrol a new student',         'route' => 'students.create',                   'from' => '#0891b2', 'to' => '#0e7490'],
            ['icon' => 'fa-chalkboard-teacher', 'title' => 'Assign Teacher', 'subtitle' => 'Assign teachers to sections', 'route' => 'teacher-section-assignments.index', 'from' => '#059669', 'to' => '#047857'],
            ['icon' => 'fa-id-card',        'title' => 'Generate ID Cards', 'subtitle' => 'Print student ID cards',    'route' => 'students.id-cards',                 'from' => '#d97706', 'to' => '#b45309'],
            ['icon' => 'fa-birthday-cake',   'title' => 'Birthdays',       'subtitle' => 'Find students by birthday',   'route' => 'students.birthdays',                'from' => '#db2777', 'to' => '#9d174d'],
        ];

        return view('pages.students.hub', compact('cards'));
    }
}
