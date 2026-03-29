<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
            'father_occupation',
            'father_phone',
            'father_email',

            // ================= MOTHER =================
            'mother_name',
            'mother_nid_number',
            'mother_occupation',
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
            'guardian_occupation',
            'guardian_address',
            'guardian_phone',
            'guardian_email',

            // ================= ACADEMIC HISTORY =================
            'previous_school',
            'previous_class_appeared',
            'tc_number',

            'status'
        ];


    protected $casts = [
        'date_of_birth' => 'date',
        'gender'        => 'integer',
        'religion'      => 'integer',
        'blood_group'   => 'integer',
        'disable'       => 'boolean',
    ];


    const A_POSITIVE    = 1;
    const B_POSITIVE    = 2;
    const O_POSITIVE    = 3;
    const AB_POSITIVE   = 4;
    const A_NEGATIVE    = 5;
    const B_NEGATIVE    = 6;
    const O_NEGATIVE    = 7;
    const AB_NEGATIVE   = 8;

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

    const MALE   = 1;
    const FEMALE = 2;

    const GENDERS = [
        self::MALE   => 'Male',
        self::FEMALE => 'Female',
    ];

    const ISLAM     = 1;
    const HINDU     = 2;
    const CHRISTIAN = 3;
    const BUDDHIST  = 4;

    const RELIGIONS = [
        self::ISLAM     => 'Islam',
        self::HINDU     => 'Hindu',
        self::CHRISTIAN => 'Christian',
        self::BUDDHIST  => 'Buddhist',
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


    public function division()
    {
        return $this->belongsTo(Division::class);
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

    public function payments(){
        return $this->hasMany(Payment::class);
    }

    public function scholarships()
    {
        return $this->hasMany(Scholarship::class);
    }

}
