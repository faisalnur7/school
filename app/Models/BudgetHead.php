<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BudgetHead extends Model
{
    protected $fillable = ['name', 'parent_id', 'description'];

    public function parent()
    {
        return $this->belongsTo(BudgetHead::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(BudgetHead::class, 'parent_id');
    }

    public function allocations()
    {
        return $this->hasMany(BudgetAllocation::class);
    }

    public function totalAllocated(int $year): float
    {
        return (float) $this->allocations()->where('fiscal_year', $year)->sum('amount');
    }
}
