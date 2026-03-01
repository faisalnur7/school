<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Fee extends Model
{
    protected $fillable = [
        'student_id',
        'fee_set_id',
        'amount',
        'paid_amount',
        'due_date',
        'status',
        'remarks',
    ];

    public function feeSet(){
        return $this->belongsTo(FeeSet::class,'fee_set_id');
    }

}
