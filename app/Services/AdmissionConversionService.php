<?php

namespace App\Services;

use App\Models\AdmissionApplication;
use App\Models\AdmissionConversion;
use App\Models\Fee;
use App\Models\FeeSet;
use App\Models\Student;
use App\Models\StudentAcademicInformation;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;

class AdmissionConversionService
{
    public function convert(AdmissionApplication $application, ?int $userId = null): AdmissionConversion
    {
        return DB::transaction(function () use ($application, $userId) {
            $application = AdmissionApplication::query()->lockForUpdate()->findOrFail($application->id);
            if ($application->review_status !== 'approved') throw ValidationException::withMessages(['application' => 'Only approved applications can proceed to admission.']);
            if ($application->conversion_status === 'converted' || $application->converted_student_id) throw ValidationException::withMessages(['application' => 'This application has already been converted.']);

            $data = $application->applicant_data ?? [];
            $fields = ['full_name_en','full_name_bn','date_of_birth','gender','religion','blood_group','disable','father_name','father_nid_number','fathers_profession_id','father_phone','father_email','mother_name','mother_nid_number','mothers_profession_id','mother_phone','mother_email','annual_income','present_address','present_division_id','present_district_id','present_police_station_id','present_post_office_id','permanent_address','permanent_division_id','permanent_district_id','permanent_police_station_id','permanent_post_office_id','guardian_type','guardian_name','guardian_relation','guardian_profession_id','guardian_address','guardian_phone','guardian_email','previous_school','previous_class_appeared','tc_number','image'];
            $student = Student::create(array_intersect_key($data, array_flip($fields)) + ['student_cid' => Student::generateNextCid(), 'status' => 1]);
            $roll = $this->nextRoll($application->academic_session_id, $application->school_class_id, $data['section_id'] ?? null, $data['group_id'] ?? null);
            StudentAcademicInformation::create(['student_id' => $student->id, 'academic_session_id' => $application->academic_session_id, 'school_class_id' => $application->school_class_id, 'section_id' => $data['section_id'] ?? null, 'group_id' => $data['group_id'] ?? null, 'roll' => $roll, 'academic_status' => 'active', 'promotion_status' => 'new_admission', 'is_current' => true]);
            $this->applyFeeSets($student, $application->school_class_id, $application->academic_session_id);
            $conversion = AdmissionConversion::create(['admission_application_id' => $application->id, 'student_id' => $student->id, 'academic_session_id' => $application->academic_session_id, 'school_class_id' => $application->school_class_id, 'roll' => $roll, 'converted_by' => $userId, 'converted_at' => now()]);
            $application->update(['conversion_status' => 'converted', 'converted_student_id' => $student->id]);
            return $conversion;
        });
    }

    private function nextRoll(int $sessionId, int $classId, ?int $sectionId = null, ?int $groupId = null): string
    {
        $rolls = StudentAcademicInformation::query()->where('academic_session_id', $sessionId)->where('school_class_id', $classId)->when($sectionId, fn ($query) => $query->where('section_id', $sectionId))->when($groupId, fn ($query) => $query->where('group_id', $groupId))->lockForUpdate()->pluck('roll');
        return (string) (($rolls->filter(fn ($roll) => is_numeric($roll))->map(fn ($roll) => (int) $roll)->max() ?? 0) + 1);
    }

    private function applyFeeSets(Student $student, int $classId, int $sessionId): void
    {
        $feeSets = FeeSet::with('items.category')->where('school_class_id', $classId)->where('academic_session_id', $sessionId)->get();
        foreach ($feeSets as $feeSet) {
            $amount = $feeSet->items->filter(fn ($item) => in_array($item->category->student_type ?? 'both', ['both', 'new']))->sum('amount');
            if ($amount <= 0) continue;
            $dates = match ($feeSet->frequency) {
                'monthly' => collect(range(1, 12))->map(fn ($month) => Carbon::create(now()->year, $month, 1)->endOfMonth()),
                'yearly' => collect([$feeSet->due_date ? Carbon::parse($feeSet->due_date) : Carbon::create(now()->year, 12, 31)]),
                default => collect([$feeSet->due_date ? Carbon::parse($feeSet->due_date) : now()->toDateString()]),
            };
            foreach ($dates as $dueDate) Fee::create(['student_id' => $student->id, 'fee_set_id' => $feeSet->id, 'amount' => $amount, 'due_date' => $dueDate, 'status' => 'pending']);
        }
    }
}
