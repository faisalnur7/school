<?php

namespace App\Http\Controllers;

use App\Models\TutorialReportSetting;
use App\Models\SchoolClass;
use Illuminate\Http\Request;

class TutorialReportSettingController extends Controller
{
    public function edit(Request $request)
    {
        $classId = $request->filled('class_id') ? (int) $request->input('class_id') : null;
        if ($classId !== null) {
            SchoolClass::findOrFail($classId);
        }

        return view('pages.tutorial-report.settings', [
            'settings' => TutorialReportSetting::current($classId),
            'classes' => SchoolClass::where('status', 1)->orderBy('order')->orderBy('id')->get(),
            'selectedClassId' => $classId,
            'elements' => TutorialReportSetting::ELEMENTS,
        ]);
    }

    public function update(Request $request)
    {
        $defaults = TutorialReportSetting::defaults();
        $submitted = $request->all();
        $request->merge(array_merge($defaults, $submitted, [
            'element_positions' => array_replace_recursive($defaults['element_positions'], $request->input('element_positions', [])),
            'element_sizes' => array_replace_recursive($defaults['element_sizes'], $request->input('element_sizes', [])),
            'element_visibility' => array_replace($defaults['element_visibility'], $request->input('element_visibility', [])),
            'element_font_sizes' => array_replace($defaults['element_font_sizes'], $request->input('element_font_sizes', [])),
            'signature_settings' => array_replace($defaults['signature_settings'], $request->input('signature_settings', [])),
        ]));
        $data = $request->validate([
            'results_per_page' => ['required', 'integer', 'in:1,2,3,4'],
            'class_id' => ['nullable', 'integer', 'exists:school_classes,id'],
            'page_width_mm' => ['required', 'numeric', 'min:100', 'max:500'],
            'page_height_mm' => ['required', 'numeric', 'min:100', 'max:500'],
            'page_margin_top_mm' => ['required', 'numeric', 'min:0', 'max:40'],
            'page_margin_right_mm' => ['required', 'numeric', 'min:0', 'max:40'],
            'page_margin_bottom_mm' => ['required', 'numeric', 'min:0', 'max:40'],
            'page_margin_left_mm' => ['required', 'numeric', 'min:0', 'max:40'],
            'card_gap_mm' => ['required', 'numeric', 'min:0', 'max:20'],
            'font_scale_percent' => ['required', 'integer', 'min:50', 'max:180'],
            'content_width_percent' => ['required', 'integer', 'min:60', 'max:100'],
            'table_padding_mm' => ['required', 'numeric', 'min:0', 'max:8'],
            'card_border_width_mm' => ['required', 'numeric', 'min:0', 'max:3'],
            'card_border_color' => ['required', 'regex:/^#[0-9a-fA-F]{6}$/'],
            'card_radius_mm' => ['required', 'integer', 'min:0', 'max:12'],
            'element_order' => ['required', 'array', 'size:4'],
            'element_order.*' => ['required', 'in:header,student,marks,footer'],
            'element_positions' => ['required', 'array'],
            'element_positions.*.x' => ['required', 'numeric', 'min:-30', 'max:30'],
            'element_positions.*.y' => ['required', 'numeric', 'min:-100', 'max:100'],
            'element_sizes' => ['required', 'array'],
            'element_sizes.*.width' => ['required', 'numeric', 'min:20', 'max:100'],
            'element_sizes.*.height' => ['required', 'numeric', 'min:4', 'max:100'],
            'element_visibility' => ['nullable', 'array'],
            'element_font_sizes' => ['required', 'array'],
            'element_font_sizes.*' => ['required', 'numeric', 'min:5', 'max:40'],
            'signature_settings' => ['required', 'array'],
            'signature_settings.visible' => ['nullable', 'boolean'],
            'signature_settings.width_mm' => ['required', 'numeric', 'min:10', 'max:200'],
            'signature_settings.height_mm' => ['required', 'numeric', 'min:5', 'max:100'],
            'signature_settings.space_mm' => ['required', 'numeric', 'min:0', 'max:100'],
            'signature_settings.offset_x_percent' => ['required', 'numeric', 'min:-30', 'max:30'],
            'signature_settings.offset_y_percent' => ['required', 'numeric', 'min:-30', 'max:30'],
            'signature_settings.show_name' => ['nullable', 'boolean'],
            'signature_settings.show_designation' => ['nullable', 'boolean'],
            'signature_settings.show_school_name' => ['nullable', 'boolean'],
            'marks_column_widths' => ['required', 'array'],
            'marks_column_widths.subject' => ['required', 'numeric', 'min:5', 'max:80'],
            'marks_column_widths.full_marks' => ['required', 'numeric', 'min:5', 'max:80'],
            'marks_column_widths.obtained_marks' => ['required', 'numeric', 'min:5', 'max:80'],
            'marks_column_widths.highest_marks' => ['required', 'numeric', 'min:5', 'max:80'],
        ]);

        $columnWidths = collect($data['marks_column_widths'])
            ->map(fn ($width) => round((float) $width, 2));
        if (abs($columnWidths->sum() - 100) > 0.01) {
            return back()->withInput()->withErrors(['marks_column_widths' => 'Marks table column widths must total 100%.']);
        }
        $data['marks_column_widths'] = $columnWidths->all();

        $elements = TutorialReportSetting::ELEMENTS;
        $data['element_order'] = array_values(array_unique(array_intersect($data['element_order'], $elements)));
        foreach ($elements as $element) {
            if (!in_array($element, $data['element_order'], true)) {
                $data['element_order'][] = $element;
            }
            $data['element_positions'][$element] = [
                'x' => round((float) data_get($data, "element_positions.$element.x", 0), 2),
                'y' => round((float) data_get($data, "element_positions.$element.y", 0), 2),
            ];
            $data['element_sizes'][$element] = [
                'width' => round((float) data_get($data, "element_sizes.$element.width", 96), 2),
                'height' => round((float) data_get($data, "element_sizes.$element.height", 12), 2),
            ];
        }
        $data['element_visibility'] = collect($elements)->mapWithKeys(fn ($element) => [
            $element => (bool) data_get($data, "element_visibility.$element", true),
        ])->all();
        $data['element_font_sizes'] = collect($elements)->mapWithKeys(fn ($element) => [
            $element => round((float) data_get($data, "element_font_sizes.$element", 12), 2),
        ])->all();
        $signatureDefaults = $defaults['signature_settings'];
        $data['signature_settings'] = [
            'visible' => (bool) data_get($data, 'signature_settings.visible', $signatureDefaults['visible']),
            'width_mm' => round((float) data_get($data, 'signature_settings.width_mm', $signatureDefaults['width_mm']), 2),
            'height_mm' => round((float) data_get($data, 'signature_settings.height_mm', $signatureDefaults['height_mm']), 2),
            'space_mm' => round((float) data_get($data, 'signature_settings.space_mm', $signatureDefaults['space_mm']), 2),
            'offset_x_percent' => round((float) data_get($data, 'signature_settings.offset_x_percent', $signatureDefaults['offset_x_percent']), 2),
            'offset_y_percent' => round((float) data_get($data, 'signature_settings.offset_y_percent', $signatureDefaults['offset_y_percent']), 2),
            'show_name' => (bool) data_get($data, 'signature_settings.show_name', $signatureDefaults['show_name']),
            'show_designation' => (bool) data_get($data, 'signature_settings.show_designation', $signatureDefaults['show_designation']),
            'show_school_name' => (bool) data_get($data, 'signature_settings.show_school_name', $signatureDefaults['show_school_name']),
        ];

        $classId = $data['class_id'] ?? null;
        unset($data['class_id']);
        $settingQuery = TutorialReportSetting::query();
        $setting = $classId === null
            ? $settingQuery->whereNull('school_class_id')->firstOrNew()
            : $settingQuery->firstOrNew(['school_class_id' => $classId]);
        $setting->fill($data)->save();

        return redirect()->route('result.tutorial-report.settings.edit', $classId ? ['class_id' => $classId] : [])
            ->with('success', 'Tutorial report settings saved.');
    }
}
