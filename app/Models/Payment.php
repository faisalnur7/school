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
        'collected_by'
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
}