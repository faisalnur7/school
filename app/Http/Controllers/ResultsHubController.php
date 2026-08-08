<?php

namespace App\Http\Controllers;

class ResultsHubController extends Controller
{
    public function index()
    {
        $cards = [
            ['icon' => 'fa-file-alt',      'title' => __('All Exams'),           'subtitle' => __('View & manage exams'),          'route' => 'exams.index', 'permission' => 'view_card_all_exams', 'from' => '#4f46e5', 'to' => '#7c3aed'],
            ['icon' => 'fa-id-card',      'title' => __('Admit and Seat Cards'), 'subtitle' => __('Print admit or seat cards'),     'route' => 'results.admit-seat-cards.index', 'permission' => 'view_results', 'from' => '#1a6b3c', 'to' => '#2d9e5f'],
            ['icon' => 'fa-book-reader',   'title' => __('Subject Assignment'),  'subtitle' => __('Assign subjects to students'),  'route' => 'student-subjects.index', 'permission' => 'view_card_subject_assignment',  'from' => '#d97706', 'to' => '#b45309'],
            ['icon' => 'fa-file-invoice',  'title' => __('Terminal Report'),     'subtitle' => __('Student progress reports'),     'route' => 'result.progress-report.index', 'permission' => 'view_card_terminal_report', 'from' => '#7c3aed', 'to' => '#4f46e5'],
            ['icon' => 'fa-chart-line',   'title' => __('Yearly Final Report'), 'subtitle' => __('Annual pair-based summary'), 'route' => 'result.yearly-final-report.index', 'permission' => 'view_card_yearly_final_report', 'from' => '#059669', 'to' => '#047857'],
            ['icon' => 'fa-clipboard-list','title' => __('Tutorial Exam Report'),'subtitle' => __('Obtained marks only'),          'route' => 'result.tutorial-report.index', 'permission' => 'view_card_tutorial_exam_report', 'from' => '#0891b2', 'to' => '#0e7490'],
        ];
        $cards = array_values(array_filter($cards, fn ($card) => auth()->user()?->hasPermission($card['permission'])));

        return view('pages.results.hub', compact('cards'));
    }
}
