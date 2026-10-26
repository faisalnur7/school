<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Models\AdmitSeatCardSetting;
use App\Models\Employee;
use App\Models\SchoolSetting;
use Illuminate\Http\Request;
use Mpdf\Mpdf;

class EmployeeIdCardController extends Controller
{
    public function index(Request $request)
    {
        [$employees, $setting, $cardSettings, $layout, $schoolLogoUrl, $cardSettingsPayload] = $this->buildData($request);

        return view('pages.generate-id-cards.index', [
            'employeeMode' => true,
            'employees' => $employees,
            'students' => collect(),
            'sessions' => collect(),
            'classes' => collect(),
            'sections' => collect(),
            'groups' => collect(),
            'setting' => $setting,
            'cardType' => 'id_card',
            'cardSettings' => $cardSettings,
            'cardSettingsMap' => collect([5 => $cardSettings]),
            'layout' => $layout,
            'schoolLogoUrl' => $schoolLogoUrl,
            'currentCardLogoUrl' => $this->resolveImageUrl($cardSettings->card_logo) ?: $schoolLogoUrl,
            'currentCardPrincipalSignatureUrl' => $this->resolveImageUrl($cardSettings->card_principal_signature),
            'cardSettingsPayload' => $cardSettingsPayload,
        ]);
    }

    public function pdf(Request $request)
    {
        [$employees, $setting, $cardSettings, $layout] = $this->buildData($request);

        if ($employees->isEmpty()) {
            return redirect()->route('hr.employee-id-cards')->with('error', 'No employees found for the selected filters.');
        }

        $html = view('pages.employee-id-cards.pdf', [
            'employeeMode' => true,
            'employees' => $employees,
            'students' => collect(),
            'setting' => $setting,
            'cardSettings' => $cardSettings,
            'cardType' => 'id_card',
            'layout' => $layout,
        ])->render();

        $mpdf = new Mpdf([
            'format' => 'A4-L',
            'margin_top' => 10,
            'margin_bottom' => 8,
            'margin_left' => 8,
            'margin_right' => 8,
            'img_dpi' => 150,
            'allow_charset_conversion' => false,
        ]);
        $mpdf->showImageErrors = true;
        $mpdf->WriteHTML($html);
        return response($mpdf->Output('', 'S'), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="employee-id-cards.pdf"',
        ]);
    }

    public function saveSettings(Request $request)
    {
        $validated = $request->validate([
            'cards_per_page' => ['required', 'integer', 'min:1', 'max:12'],
            'cards_per_row' => ['required', 'integer', 'min:1', 'max:10'],
            'card_width_value' => ['required', 'numeric', 'min:0.1'],
            'card_height_value' => ['required', 'numeric', 'min:0.1'],
            'grid_gap_value' => ['required', 'numeric', 'min:0.1'],
            'card_dimension_unit' => ['required', 'in:cm,px'],
            'card_color_type' => ['required', 'in:gradient,solid'],
            'card_logo' => ['nullable', 'image', 'max:100'],
            'card_principal_signature' => ['nullable', 'file', 'mimes:png,jpg,jpeg', 'max:100'],
        ]);

        $setting = AdmitSeatCardSetting::current(5);
        $payload = $request->only($setting->getFillable());
        $payload['card_type'] = 5;

        foreach ([
            'card_is_transparent', 'card_show_logo_front', 'card_show_logo_back', 'card_show_photo_front',
            'card_show_footer_front', 'card_show_footer_back', 'card_show_back_student_details',
            'card_show_back_school_contact', 'card_show_back_qr', 'card_show_back_signature',
            'card_show_school_detail_front', 'card_show_school_detail_back', 'card_show_slogan_front',
            'card_show_slogan_back', 'card_show_title_front', 'card_show_title_back', 'card_show_back_notice',
        ] as $checkbox) {
            $payload[$checkbox] = $request->boolean($checkbox);
        }

        $payload['cards_per_page'] = $validated['cards_per_page'];
        $payload['cards_per_row'] = $validated['cards_per_row'];
        $payload['card_width_value'] = $validated['card_width_value'];
        $payload['card_height_value'] = $validated['card_height_value'];
        $payload['grid_gap_value'] = $validated['grid_gap_value'];
        $payload['card_dimension_unit'] = $validated['card_dimension_unit'];
        $payload['card_color_type'] = $validated['card_color_type'];

        foreach (['card_logo', 'card_principal_signature'] as $fileField) {
            if ($request->hasFile($fileField)) {
                $file = $request->file($fileField);
                $directory = public_path('uploads/card_settings');
                if (!is_dir($directory)) {
                    mkdir($directory, 0755, true);
                }
                $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $file->move($directory, $filename);
                $payload[$fileField] = 'uploads/card_settings/' . $filename;
            }
        }

        $setting->fill($payload)->save();

        return back()->with('success', 'Employee ID-card settings saved.');
    }

    private function buildData(Request $request): array
    {
        $query = Employee::query()
            ->active()
            ->with(['designation', 'department'])
            ->orderBy('name');

        if ($request->filled('employee_type') && in_array($request->employee_type, ['teacher', 'staff'], true)) {
            $query->where('employee_type', $request->employee_type);
        }

        if ($request->filled('employee_search')) {
            $search = trim((string) $request->employee_search);
            $query->where(function ($employeeQuery) use ($search) {
                $employeeQuery->where('name', 'like', "%{$search}%")
                    ->orWhere('employee_id', 'like', "%{$search}%");
            });
        }

        $employees = ($request->filled('employee_type') || $request->filled('employee_search'))
            ? $query->get()
            : collect();
        $setting = SchoolSetting::current();
        $cardSettings = AdmitSeatCardSetting::current(5);

        return [
            $employees,
            $setting,
            $cardSettings,
            $this->buildLayout($cardSettings),
            $this->resolveImageUrl($setting?->logo),
            ['5' => array_merge($cardSettings->toArray(), [
                'card_logo_url' => $this->resolveImageUrl($cardSettings->card_logo) ?: $this->resolveImageUrl($setting?->logo),
                'card_principal_signature_url' => $this->resolveImageUrl($cardSettings->card_principal_signature),
            ])],
        ];
    }

    private function buildLayout(AdmitSeatCardSetting $settings): array
    {
        $unit = in_array($settings->card_dimension_unit, ['cm', 'px'], true) ? $settings->card_dimension_unit : 'cm';
        $gap = (float) ($settings->grid_gap_value ?: 0.5);
        $width = (float) ($settings->card_width_value ?: 5.4);
        $height = (float) ($settings->card_height_value ?: 8.4);
        if ($unit === 'px') {
            $gap = $gap / 96 * 2.54;
            $width = $width / 96 * 2.54;
            $height = $height / 96 * 2.54;
        }

        $cardsPerPage = max(1, min(12, (int) ($settings->cards_per_page ?: 4)));
        $cardsPerRow = max(1, min($cardsPerPage, (int) ($settings->cards_per_row ?: 2)));
        $pageWidth = 28.1;
        $pageHeight = 19.2;
        $maxCardsPerRow = max(1, (int) floor(($pageWidth + $gap) / ($width + $gap)));
        $maxPageRows = max(1, (int) floor(($pageHeight + $gap) / ($height + $gap)));
        $cardsPerRow = min($cardsPerRow, $maxCardsPerRow);
        $pageRows = min((int) ceil($cardsPerPage / $cardsPerRow), $maxPageRows);
        $cardsPerPage = min($cardsPerPage, $cardsPerRow * $pageRows);

        return [
            'cardsPerPage' => $cardsPerPage,
            'cardsPerRow' => $cardsPerRow,
            'pageRows' => $pageRows,
            'cardWidthCm' => $width,
            'cardHeightCm' => $height,
            'gridGapCm' => $gap,
        ];
    }

    private function resolveImageUrl(?string $path): ?string
    {
        return $path && file_exists(public_path($path)) ? asset($path) : null;
    }
}
