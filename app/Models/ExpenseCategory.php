<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExpenseCategory extends Model
{
    protected $fillable = ['name', 'slug', 'description', 'is_active'];

    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'expense_category_id');
    }

    public function totalExpense()
    {
        return $this->transactions()->sum('amount');
    }
}