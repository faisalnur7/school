<?php

namespace Database\Seeders;

use App\Models\MobileAppFeature;
use Illuminate\Database\Seeder;

class MobileAppFeatureSeeder extends Seeder
{
    public function run(): void
    {
        $features = [
            ['key' => 'dashboard', 'label' => 'Dashboard', 'icon' => 'dashboard_outlined', 'flutter_route' => '/dashboard', 'api_capability' => ['dashboard'], 'sort_order' => 10],
            ['key' => 'class_routine', 'label' => 'Class routine', 'icon' => 'schedule_outlined', 'flutter_route' => '/class-routine', 'api_capability' => ['routine', 'routine_pdf'], 'sort_order' => 20],
            ['key' => 'exam_routine', 'label' => 'Exam routine', 'icon' => 'event_note_outlined', 'flutter_route' => '/exam-routine', 'api_capability' => ['exam_routine'], 'sort_order' => 30],
            ['key' => 'results', 'label' => 'Results', 'icon' => 'assessment_outlined', 'flutter_route' => '/results', 'api_capability' => ['results', 'result_options', 'result_detail'], 'sort_order' => 40],
            ['key' => 'attendance', 'label' => 'Attendance', 'icon' => 'calendar_month_outlined', 'flutter_route' => '/attendance', 'api_capability' => ['attendance'], 'sort_order' => 50],
            ['key' => 'fees', 'label' => 'Fees', 'icon' => 'payments_outlined', 'flutter_route' => '/feature/fees', 'api_capability' => ['fees', 'payments'], 'sort_order' => 60],
            ['key' => 'notices', 'label' => 'Notices', 'icon' => 'campaign_outlined', 'flutter_route' => '/feature/notices', 'api_capability' => ['notices'], 'sort_order' => 70],
            ['key' => 'homework', 'label' => 'Homework', 'icon' => 'menu_book_outlined', 'flutter_route' => '/feature/homework', 'api_capability' => ['homework'], 'sort_order' => 80],
            ['key' => 'notifications', 'label' => 'Notifications', 'icon' => 'notifications_none_outlined', 'flutter_route' => '/feature/notifications', 'api_capability' => ['notifications'], 'sort_order' => 90],
            ['key' => 'holidays', 'label' => 'Holidays', 'icon' => 'event_available_outlined', 'flutter_route' => '/feature/holidays', 'api_capability' => ['holidays'], 'sort_order' => 100],
        ];

        foreach ($features as $feature) {
            MobileAppFeature::firstOrCreate(['key' => $feature['key']], $feature);
        }
    }
}
