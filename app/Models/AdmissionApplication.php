<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdmissionApplication extends Model
{
    protected $fillable = ['admission_exam_id', 'application_number', 'application_no', 'academic_session_id', 'school_class_id', 'class_id', 'applicant_data', 'full_name_en', 'full_name_bn', 'date_of_birth', 'sex', 'gender', 'religion', 'blood_group', 'birth_certificate_number', 'disable', 'father_name', 'father_nid_number', 'fathers_profession_id', 'father_phone', 'father_email', 'mother_name', 'mother_nid_number', 'mothers_profession_id', 'mother_phone', 'mother_email', 'annual_income', 'guardian_type', 'guardian_name', 'guardian_relation', 'guardian_profession_id', 'guardian_phone', 'guardian_email', 'present_address', 'present_division_id', 'present_district_id', 'present_police_station_id', 'present_post_office_id', 'permanent_address', 'permanent_division_id', 'permanent_district_id', 'permanent_police_station_id', 'permanent_post_office_id', 'previous_school', 'previous_class_appeared', 'tc_number', 'image', 'status', 'submitted_at', 'payment_status', 'application_status', 'result_status', 'review_status', 'conversion_status', 'total_marks', 'pass_mark_snapshot', 'approved_by', 'approved_at', 'converted_student_id', 'admin_notes'];
    protected $casts = ['applicant_data' => 'array', 'total_marks' => 'decimal:2', 'pass_mark_snapshot' => 'decimal:2', 'approved_at' => 'datetime'];
    public function exam() { return $this->belongsTo(AdmissionExam::class, 'admission_exam_id'); }
    public function academicSession() { return $this->belongsTo(AcademicSession::class); }
    public function schoolClass() { return $this->belongsTo(SchoolClass::class); }
    public function payment() { return $this->hasOne(AdmissionPayment::class); }
    public function admitCard() { return $this->hasOne(AdmissionAdmitCard::class); }
    public function reviews() { return $this->hasMany(AdmissionApplicationReview::class); }
    public function convertedStudent() { return $this->belongsTo(Student::class, 'converted_student_id'); }
}
