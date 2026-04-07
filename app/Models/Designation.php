<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Designation extends Model
{
    protected $fillable = ['name', 'employee_type', 'hierarchy_level', 'status'];
    protected $casts    = ['hierarchy_level' => 'integer'];

    public function scopeActive($q)    { return $q->where('status', 'active'); }
    public function scopeTeachers($q)  { return $q->where('employee_type', 'teacher'); }
    public function scopeStaff($q)     { return $q->where('employee_type', 'staff'); }

    public function employees()        { return $this->hasMany(Employee::class); }
    public function salaryDefault()    { return $this->hasOne(DesignationSalaryDefault::class); }
}
