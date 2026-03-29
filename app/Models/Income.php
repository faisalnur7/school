<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Income extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'income_category_id',
        'title',
        'amount',
        'income_date',
        'payment_method',
        'reference_no',
        'description',
        'attachment',
        'recorded_by',
        'account_type',
        'account_id', 
    ];

    protected $casts = [
        'income_date' => 'date',
        'amount'      => 'decimal:2',
    ];

    public function category()
    {
        return $this->belongsTo(IncomeCategory::class, 'income_category_id');
    }

    public function recorder()
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    public function account()
    {
        return $this->morphTo('account', 'account_type', 'account_id');
    }

    public function getAccountModelAttribute()
    {
        return match($this->account_type) {
            'App\Models\BankAccount'          => \App\Models\BankAccount::find($this->account_id),
            'App\Models\MobileBankingAccount' => \App\Models\MobileBankingAccount::find($this->account_id),
            'App\Models\HandCash'             => \App\Models\HandCash::find($this->account_id),
            default                           => null,
        };
    }
}