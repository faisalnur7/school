<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    protected $fillable = ['name', 'employee_type', 'status'];

    public function scopeActive($q)   { return $q->where('status', 'active'); }
    public function scopeTeachers($q) { return $q->where('employee_type', 'teacher'); }
    public function scopeStaff($q)    { return $q->where('employee_type', 'staff'); }

    public function employees()       { return $this->hasMany(Employee::class); }
}