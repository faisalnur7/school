<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeQualification extends Model
{
    protected $fillable = [
        'employee_id',
        'qualification_id',
        'institution_name',
        'passing_year',
        'result',
        'document_path',
        'document_original_name',
    ];

    protected $casts = ['passing_year' => 'integer'];

    public function employee() { return $this->belongsTo(Employee::class); }
    public function qualification() { return $this->belongsTo(EducationalQualification::class, 'qualification_id'); }

    public function getDocumentUrlAttribute(): ?string
    {
        return $this->document_path ? asset($this->document_path) : null;
    }
}
