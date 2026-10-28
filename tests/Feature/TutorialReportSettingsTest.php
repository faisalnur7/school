<?php

namespace Tests\Feature;

use App\Models\TutorialReportSetting;
use App\Models\SchoolClass;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TutorialReportSettingsTest extends TestCase
{
    use RefreshDatabase;

    public function test_default_settings_define_the_draggable_report_template(): void
    {
        $defaults = TutorialReportSetting::defaults();

        $this->assertSame(2, $defaults['results_per_page']);
        $this->assertSame(['header', 'student', 'marks', 'footer'], $defaults['element_order']);
        $this->assertArrayHasKey('marks', $defaults['element_positions']);
        $this->assertArrayHasKey('marks', $defaults['element_sizes']);
        $this->assertSame(100, $defaults['font_scale_percent']);
        $this->assertSame(55, $defaults['signature_settings']['width_mm']);
        $this->assertSame(18, $defaults['signature_settings']['height_mm']);
        $this->assertTrue($defaults['signature_settings']['visible']);
    }

    public function test_settings_page_and_controller_expose_customizable_layout_fields(): void
    {
        $view = file_get_contents(resource_path('views/pages/tutorial-report/settings.blade.php'));
        $controller = file_get_contents(app_path('Http/Controllers/TutorialReportSettingController.php'));
        $routes = file_get_contents(base_path('routes/web.php'));

        $this->assertStringContainsString('Tutorial Report Settings', $view);
        $this->assertStringContainsString('draggable="true"', $view);
        $this->assertStringContainsString('element_positions[{{ $element }}][x]', $view);
        $this->assertStringContainsString('element_sizes[{{ $element }}][width]', $view);
        $this->assertStringContainsString('element_font_sizes[{{ $element }}]', $view);
        $this->assertStringContainsString('step="0.01"', $view);
        $this->assertSame(15, substr_count($view, 'step="0.01"'));
        $this->assertStringContainsString('name="results_per_page"', $view);
        $this->assertStringContainsString('name="class_id"', $view);
        $this->assertStringContainsString("SchoolClass::where('status', 1)->orderBy('order')->orderBy('id')", $controller);
        $this->assertStringContainsString('data-preview-cards', $view);
        $this->assertStringContainsString('data-preview-card', $view);
        $this->assertStringContainsString('tutorialReportPreviewCount', $view);
        $this->assertStringContainsString('refreshPreview', $view);
        $this->assertStringContainsString('densityFontBases', $view);
        $this->assertStringContainsString('tutorial-print-address', $view);
        $this->assertStringContainsString('$principalSignature', $view);
        $this->assertStringContainsString('signature_settings', $view);
        $this->assertStringContainsString('signature_settings[width_mm]', $view);
        $this->assertStringContainsString('signature_settings[height_mm]', $view);
        $this->assertStringContainsString('name="content_width_percent"', $view);
        $this->assertStringContainsString("Math.min(configuredWidth, contentWidth)", $view);
        $this->assertStringContainsString('name="table_padding_mm"', $view);
        $this->assertStringContainsString('name="marks_column_widths[{{ $column }}]"', $view);
        $this->assertStringContainsString("'subject' => 'Subject'", $view);
        $this->assertStringContainsString("'full_marks' => 'Full Marks'", $view);
        $this->assertStringContainsString("'obtained_marks' => 'Obtained Marks'", $view);
        $this->assertStringContainsString("'highest_marks' => 'Highest Marks'", $view);
        $this->assertStringContainsString('marks_column_widths', $controller);
        $this->assertStringContainsString('Math.min(configuredWidth, contentWidth)', $view);
        $this->assertStringContainsString('font_scale_percent', $controller);
        $this->assertStringContainsString('(fontSize / fontDefault)', $view);
        $this->assertStringContainsString('font-size:1em !important;', $view);
        $this->assertStringContainsString('font-size:var(--preview-font-size,12px) !important;', $view);
        $this->assertStringContainsString('table-layout:fixed;', $view);
        $this->assertStringContainsString('id="tutorialReportOverflowAlert"', $view);
        $this->assertStringContainsString('overflow', $view);
        $this->assertStringContainsString('element_order', $controller);
        $this->assertStringContainsString('element_font_sizes', $controller);
        $this->assertStringContainsString('array_merge($defaults, $submitted', $controller);
        $this->assertStringContainsString("settings.edit'", $routes);
        $this->assertStringContainsString("settings.update'", $routes);
    }

    public function test_report_settings_are_loaded_for_the_selected_class(): void
    {
        $model = file_get_contents(app_path('Models/TutorialReportSetting.php'));
        $migration = glob(database_path('migrations/*add_class_id_to_tutorial_report_settings_table.php'));

        $this->assertNotEmpty($migration);
        $this->assertStringContainsString('current(?int $classId = null)', $model);
        $this->assertStringContainsString('school_class_id', $model);
    }

    public function test_class_profile_overrides_global_profile_and_unconfigured_classes_inherit_it(): void
    {
        $global = TutorialReportSetting::create(array_merge(TutorialReportSetting::defaults(), [
            'font_scale_percent' => 90,
        ]));
        $class = SchoolClass::create(['name_en' => 'Class 12', 'name_bn' => 'Class 12', 'status' => 1]);
        TutorialReportSetting::create(array_merge(TutorialReportSetting::defaults(), [
            'school_class_id' => $class->id,
            'font_scale_percent' => 125,
        ]));

        $this->assertSame(125, TutorialReportSetting::current($class->id)->font_scale_percent);
        $this->assertSame(90, TutorialReportSetting::current(999999)->font_scale_percent);
        $this->assertSame(90, $global->fresh()->font_scale_percent);
    }
}
