<?php

namespace App\Http\Controllers;

class ResultsHubController extends Controller
{
    public function index()
    {
        $cards = [
            ['icon' => 'fa-file-alt',      'title' => 'All Exams',           'subtitle' => 'View & manage exams',          'route' => 'exams.index', 'permission' => 'view_card_all_exams', 'from' => '#4f46e5', 'to' => '#7c3aed'],
            ['icon' => 'fa-plus-circle',   'title' => 'Create Exam',         'subtitle' => 'Create a new exam',            'route' => 'exams.create', 'permission' => 'view_card_create_exam', 'from' => '#0891b2', 'to' => '#0e7490'],
            ['icon' => 'fa-book-reader',   'title' => 'Subject Assignment',  'subtitle' => 'Assign subjects to students',  'route' => 'student-subjects.index', 'permission' => 'view_card_subject_assignment', 'from' => '#059669', 'to' => '#047857'],
            ['icon' => 'fa-file-invoice',  'title' => 'Terminal Report',     'subtitle' => 'Student progress reports',     'route' => 'result.progress-report.index', 'permission' => 'view_card_terminal_report', 'from' => '#1a6b3c', 'to' => '#2d9e5f'],
            ['icon' => 'fa-chart-line',   'title' => 'Yearly Final Report', 'subtitle' => 'Annual pair-based summary', 'route' => 'result.yearly-final-report.index', 'permission' => 'view_card_yearly_final_report', 'from' => '#d97706', 'to' => '#b45309'],
            ['icon' => 'fa-clipboard-list','title' => 'Tutorial Exam Report','subtitle' => 'Obtained marks only',          'route' => 'result.tutorial-report.index', 'permission' => 'view_card_tutorial_exam_report', 'from' => '#0891b2', 'to' => '#0e7490'],
        ];
        $cards = array_values(array_filter($cards, fn ($card) => auth()->user()?->hasPermission($card['permission'])));

        return view('pages.results.hub', compact('cards'));
    }
}
