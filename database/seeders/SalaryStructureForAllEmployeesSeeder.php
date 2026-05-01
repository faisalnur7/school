<?php

namespace Database\Seeders;

use App\Models\DesignationSalaryDefault;
use App\Models\Employee;
use App\Models\SalaryStructure;
use Illuminate\Database\Seeder;

class SalaryStructureForAllEmployeesSeeder extends Seeder
{
    public function run(): void
    {
        $created = 0;
        $skipped = 0;

        // Latest existing salary structure per designation (template source)
        $templatesByDesignation = SalaryStructure::query()
            ->whereNotNull('designation_id')
            ->orderByDesc('effective_from')
            ->orderByDesc('id')
            ->get()
            ->unique('designation_id')
            ->keyBy('designation_id');

        // Fallback defaults per designation
        $defaultsByDesignation = DesignationSalaryDefault::query()
            ->get()
            ->keyBy('designation_id');

        // Global fallback from any existing salary structure row
        $globalTemplate = SalaryStructure::query()
            ->orderByDesc('effective_from')
            ->orderByDesc('id')
            ->first();

        Employee::query()->orderBy('id')->chunkById(200, function ($employees) use (&$created, &$skipped, $templatesByDesignation, $defaultsByDesignation, $globalTemplate) {
            foreach ($employees as $employee) {
                $hasStructure = SalaryStructure::where('employee_id', $employee->id)->exists();

                if ($hasStructure) {
                    $skipped++;
                    continue;
                }

                $payload = $this->buildPayload($employee->designation_id, $templatesByDesignation, $defaultsByDesignation, $globalTemplate);

                if ($payload === null) {
                    $skipped++;
                    continue;
                }

                SalaryStructure::create(array_merge([
                    'employee_id'    => $employee->id,
                    'designation_id' => $employee->designation_id,
                    'effective_from' => now()->toDateString(),
                ], $payload));

                $created++;
            }
        });

        $this->command?->info("SalaryStructureForAllEmployeesSeeder: created={$created}, skipped={$skipped}");
    }

    private function buildPayload(
        ?int $designationId,
        $templatesByDesignation,
        $defaultsByDesignation,
        ?SalaryStructure $globalTemplate
    ): ?array {
        if ($designationId && isset($templatesByDesignation[$designationId])) {
            $t = $templatesByDesignation[$designationId];
            return [
                'basic_salary'        => $t->basic_salary,
                'house_rent'          => $t->house_rent,
                'medical_allowance'   => $t->medical_allowance,
                'transport_allowance' => $t->transport_allowance,
                'special_allowance'   => $t->special_allowance,
                'bonus'               => $t->bonus,
                'other_deductions'    => $t->other_deductions,
            ];
        }

        if ($designationId && isset($defaultsByDesignation[$designationId])) {
            $d = $defaultsByDesignation[$designationId];
            return [
                'basic_salary'        => $d->basic_salary,
                'house_rent'          => $d->house_rent,
                'medical_allowance'   => $d->medical_allowance,
                'transport_allowance' => $d->transport_allowance,
                'special_allowance'   => $d->special_allowance,
                'bonus'               => $d->bonus,
                'other_deductions'    => $d->other_deductions,
            ];
        }

        if ($globalTemplate) {
            return [
                'basic_salary'        => $globalTemplate->basic_salary,
                'house_rent'          => $globalTemplate->house_rent,
                'medical_allowance'   => $globalTemplate->medical_allowance,
                'transport_allowance' => $globalTemplate->transport_allowance,
                'special_allowance'   => $globalTemplate->special_allowance,
                'bonus'               => $globalTemplate->bonus,
                'other_deductions'    => $globalTemplate->other_deductions,
            ];
        }

        return null;
    }
}
