<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Mpdf\Mpdf;

class AllInOneReportController extends Controller
{
    public function index(
        Request $request,
        StudentPaymentReportController $paymentReportController,
        StudentReceivableReportController $receivableReportController,
    ) {
        [$sessions, $classes, $sections, $receiveRows, $receiveMonths, $receiveTotals, $fromDate, $toDate] = $paymentReportController->buildReceiveData($request);
        [$paymentCategories, $paymentAvailableCategories, $paymentRows, $paymentDateLabel] = $paymentReportController->buildData($request);
        [$receivableSessions, $receivableClasses, $receivableSections, $receivableRows, $receivableMonths, $receivableCategories, $receivableTotals, $receivableFromDate, $receivableToDate] = $receivableReportController->buildData($request);

        return view('pages.all-in-one-report.index', compact(
            'sessions',
            'classes',
            'sections',
            'paymentCategories',
            'paymentAvailableCategories',
            'paymentRows',
            'paymentDateLabel',
            'receiveRows',
            'receiveMonths',
            'receiveTotals',
            'fromDate',
            'toDate',
            'receivableRows',
            'receivableMonths',
            'receivableCategories',
            'receivableTotals',
            'receivableFromDate',
            'receivableToDate'
        ));
    }

    public function pdf(
        Request $request,
        StudentPaymentReportController $paymentReportController,
        StudentReceivableReportController $receivableReportController,
    ) {
        [$sessions, $classes, $sections, $receiveRows, $receiveMonths, $receiveTotals, $fromDate, $toDate] = $paymentReportController->buildReceiveData($request);
        [$paymentCategories, $paymentAvailableCategories, $paymentRows, $paymentDateLabel] = $paymentReportController->buildData($request);
        [$receivableSessions, $receivableClasses, $receivableSections, $receivableRows, $receivableMonths, $receivableCategories, $receivableTotals, $receivableFromDate, $receivableToDate] = $receivableReportController->buildData($request);

        $html = view('pages.all-in-one-report.pdf', compact(
            'paymentCategories',
            'paymentRows',
            'paymentDateLabel',
            'receiveRows',
            'receiveMonths',
            'receiveTotals',
            'fromDate',
            'toDate',
            'receivableRows',
            'receivableMonths',
            'receivableCategories',
            'receivableTotals',
            'receivableFromDate',
            'receivableToDate'
        ))->render();

        $mpdf = new Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4-L',
            'margin_top' => 10,
            'margin_bottom' => 10,
            'margin_left' => 8,
            'margin_right' => 8,
        ]);

        $mpdf->WriteHTML($html);
        $mpdf->Output('all-in-one-report.pdf', 'D');
    }
}
