<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasTransactions;
class Payment extends Model
{
    use HasTransactions;
    protected $fillable = [
        'student_id',
        'amount',
        'payment_date',
        'payment_method',
        'transaction_id',
        'remarks',
        'receipt_no',
        'collected_by',
        'account_type',
        'account_id',
    ];

    public function items()
    {
        return $this->hasMany(PaymentItem::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function collector()
    {
        return $this->belongsTo(User::class,'collected_by');
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