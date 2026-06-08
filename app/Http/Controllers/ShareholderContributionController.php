<?php

namespace App\Http\Controllers;

use App\Models\Shareholder;
use Mpdf\Mpdf;

class ShareholderContributionController extends Controller
{
    private function getData(): array
    {
        $shareholders = Shareholder::orderBy('name')->get()->map(fn ($s) => [
            'name'    => $s->name,
            'capital' => $s->capitalBalance(),
        ]);
        $totalCapital = $shareholders->sum('capital');
        return [$shareholders, $totalCapital];
    }

    public function index()
    {
        [$shareholders, $totalCapital] = $this->getData();
        return view('pages.shareholders.contribution', compact('shareholders', 'totalCapital'));
    }

    public function pdf()
    {
        [$shareholders, $totalCapital] = $this->getData();

        $html = view('pages.shareholders.contribution-pdf', compact('shareholders', 'totalCapital'))->render();

        $mpdf = new Mpdf(['mode' => 'utf-8', 'format' => 'A4', 'margin_top' => 10, 'margin_bottom' => 10]);
        $mpdf->WriteHTML($html);
        $mpdf->Output('shareholder-contribution.pdf', 'D');
    }
}
