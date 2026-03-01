<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IncomeCategory extends Model
{
    protected $fillable = ['name', 'slug', 'description', 'is_active'];

    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'income_category_id');
    }

    public function totalIncome()
    {
        return $this->transactions()->sum('amount');
    }
}