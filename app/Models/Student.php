<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        // ================= BASIC INFO =================
        'student_cid',
        'full_name_bn',
        'full_name_en',
        'image',
        'date_of_birth',
        'gender',
        'birth_certificate_number',
        'religion',
        'blood_group',
        'disable',

        // ================= FATHER =================
        'father_name',
        'father_nid_number',
        'fathers_profession_id',
        'father_phone',
        'father_email',

        // ================= MOTHER =================
        'mother_name',
        'mother_nid_number',
        'mothers_profession_id',
        'mother_phone',
        'mother_email',

        // ================= INCOME =================
        'annual_income',

        // ================= PRESENT ADDRESS =================
        'present_address',
        'present_division_id',
        'present_district_id',
        'present_police_station_id',
        'present_post_office_id',

        // ================= PERMANENT ADDRESS =================
        'permanent_address',
        'permanent_division_id',
        'permanent_district_id',
        'permanent_police_station_id',
        'permanent_post_office_id',

        // ================= GUARDIAN =================
        'guardian_type',
        'guardian_name',
        'guardian_relation',
        'guardian_profession_id',
        'guardian_address',
        'guardian_phone',
        'guardian_email',

        // ================= ACADEMIC HISTORY =================
        'previous_school',
        'previous_class_appeared',
        'tc_number',

        'status',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'gender' => 'integer',
        'religion' => 'integer',
        'blood_group' => 'integer',
        'disable' => 'boolean',
    ];

    const A_POSITIVE = 1;
    const B_POSITIVE = 2;
    const O_POSITIVE = 3;
    const AB_POSITIVE = 4;
    const A_NEGATIVE = 5;
    const B_NEGATIVE = 6;
    const O_NEGATIVE = 7;
    const AB_NEGATIVE = 8;

    const BLOOD_GROUPS = [
        self::A_POSITIVE => 'A+',
        self::B_POSITIVE => 'B+',
        self::O_POSITIVE => 'O+',
        self::AB_POSITIVE => 'AB+',
        self::A_NEGATIVE => 'A-',
        self::B_NEGATIVE => 'B-',
        self::O_NEGATIVE => 'O-',
        self::AB_NEGATIVE => 'AB-',
    ];

    const MALE = 1;
    const FEMALE = 2;

    const GENDERS = [
        self::MALE => 'Male',
        self::FEMALE => 'Female',
    ];

    const ISLAM = 1;
    const HINDU = 2;
    const CHRISTIAN = 3;
    const BUDDHIST = 4;

    const RELIGIONS = [
        self::ISLAM => 'Islam',
        self::HINDU => 'Hindu',
        self::CHRISTIAN => 'Christian',
        self::BUDDHIST => 'Buddhist',
    ];

    public function getGenderTextAttribute(): string
    {
        return self::GENDERS[$this->gender] ?? 'N/A';
    }

    public function getReligionTextAttribute(): string
    {
        return self::RELIGIONS[$this->religion] ?? 'N/A';
    }

    public function getBloodGroupTextAttribute(): string
    {
        return self::BLOOD_GROUPS[$this->blood_group] ?? 'N/A';
    }

    public function fathersProfession()
    {
        return $this->belongsTo(Profession::class, 'fathers_profession_id');
    }

    public function mothersProfession()
    {
        return $this->belongsTo(Profession::class, 'mothers_profession_id');
    }

    public function guardianProfession()
    {
        return $this->belongsTo(Profession::class, 'guardian_profession_id');
    }

    public function division()
    {
        return $this->belongsTo(Division::class);
    }

    public function presentDivision()
    {
        return $this->belongsTo(Division::class, 'present_division_id');
    }

    public function presentDistrict()
    {
        return $this->belongsTo(District::class, 'present_district_id');
    }

    public function presentPoliceStation()
    {
        return $this->belongsTo(PoliceStation::class, 'present_police_station_id');
    }

    public function presentPostOffice()
    {
        return $this->belongsTo(PostOffice::class, 'present_post_office_id');
    }

    public function permanentDivision()
    {
        return $this->belongsTo(Division::class, 'permanent_division_id');
    }

    public function permanentDistrict()
    {
        return $this->belongsTo(District::class, 'permanent_district_id');
    }

    public function permanentPoliceStation()
    {
        return $this->belongsTo(PoliceStation::class, 'permanent_police_station_id');
    }

    public function permanentPostOffice()
    {
        return $this->belongsTo(PostOffice::class, 'permanent_post_office_id');
    }

    public function district()
    {
        return $this->belongsTo(District::class);
    }

    public function policeStation()
    {
        return $this->belongsTo(PoliceStation::class);
    }

    public function postOffice()
    {
        return $this->belongsTo(PostOffice::class);
    }

    public function academicInformations()
    {
        return $this->hasMany(StudentAcademicInformation::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function inventorySales()
    {
        return $this->hasMany(InventorySale::class);
    }

    public function fees()
    {
        return $this->hasMany(\App\Models\Fee::class);
    }

    public function getPhotoUrlAttribute(): string
    {
        if ($this->image && file_exists(public_path($this->image))) {
            return asset($this->image);
        }

        return $this->gender == self::FEMALE
            ? asset('assets/img/female-placeholder.png')
            : asset('assets/img/male-placeholder.png');
    }

    public function scholarships()
    {
        return $this->hasMany(Scholarship::class);
    }

    public function subjects(): BelongsToMany
    {
        return $this->belongsToMany(Subject::class, 'student_subjects', 'student_id', 'subject_id')
            ->withPivot(['school_class_id', 'academic_session_id', 'is_optional', 'is_mandatory'])
            ->withTimestamps();
    }

    public function studentSubjects()
    {
        return $this->hasMany(StudentSubject::class);
    }

    /**
     * Generate the next 6-digit student CID based on the latest ID in the database.
     */
    public static function generateNextCid()
    {
        $latest = self::orderBy('id', 'desc')->first();

        if ($latest && $latest->student_cid) {
            $nextNumber = (int) $latest->student_cid + 1;
        } else {
            $nextNumber = 1;
        }

        return str_pad($nextNumber, 6, '0', STR_PAD_LEFT);
    }
}
