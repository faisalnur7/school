<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class StudentAcademicInformation extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'academic_session_id',
        'school_class_id',
        'section_id',
        'group_id',
        'roll',
        'academic_status',
        'promotion_status',
        'is_current',
        'previous_academic_information_id',
        'checkout_date',
        'notes',
    ];

    protected $casts = [
        'student_id'          => 'integer',
        'academic_session_id' => 'integer',
        'school_class_id'     => 'integer',
        'section_id'          => 'integer',
        'group_id'            => 'integer',
        'is_current'          => 'boolean',
        'checkout_date'       => 'date',
    ];

    const ACADEMIC_STATUSES = ['active', 'graduated', 'withdrawn', 'transferred', 'expelled'];
    const PROMOTION_STATUSES = ['promoted', 'retained', 'new_admission'];

    /* =========================
     | Relationships
     ========================= */

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function academicSession()
    {
        return $this->belongsTo(AcademicSession::class);
    }

    public function schoolClass()
    {
        return $this->belongsTo(SchoolClass::class);
    }

    public function section()
    {
        return $this->belongsTo(Section::class);
    }

    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    public function previousRecord()
    {
        return $this->belongsTo(StudentAcademicInformation::class, 'previous_academic_information_id');
    }

    public function nextRecords()
    {
        return $this->hasMany(StudentAcademicInformation::class, 'previous_academic_information_id');
    }

    /**
     * Get the next available roll number for a given academic session, class, section, and group.
     * Roll is calculated as: count of existing records + 1.
     */
    public static function getNextRoll($academicSessionId, $schoolClassId, $sectionId, $groupId = null)
    {
        $query = self::where('academic_session_id', $academicSessionId)
                      ->where('school_class_id', $schoolClassId)
                      ->where('section_id', $sectionId);

        if ($groupId) {
            $query->where('group_id', $groupId);
        }

        $count = $query->count();

        return $count + 1;
    }
}
