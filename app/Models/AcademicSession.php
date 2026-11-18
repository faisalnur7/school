<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AcademicSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'name_bn',
        'name_en',
        'status',
    ];

    /**
     * The calendar year the session starts in, read from its name
     * ("2027" → 2027, "2026-27" → 2026). Falls back to the current year.
     */
    public function year(): int
    {
        return preg_match('/\b(20\d{2})\b/', (string) $this->name_en, $matches)
            ? (int) $matches[1]
            : now()->year;
    }

    public function teachers()
    {
        return $this->belongsToMany(Employee::class, 'employee_academic_session')
            ->where('employee_type', 'teacher')
            ->withTimestamps();
    }
}
