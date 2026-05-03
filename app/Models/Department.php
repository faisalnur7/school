<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    protected $fillable = ['name', 'code', 'description', 'employee_type', 'status', 'is_active'];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function scopeActive($q)   { return $q->where('status', 'active'); }
    public function scopeTeachers($q) { return $q->where('employee_type', 'teacher'); }
    public function scopeStaff($q)    { return $q->where('employee_type', 'staff'); }

    public function employees()       { return $this->hasMany(Employee::class); }
    public function rooms()           { return $this->hasMany(Room::class); }
}
