<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FeeAmountHistory extends Model
{
    protected $fillable = [
        'fee_id',
        'edited_by',
        'old_amount',
        'new_amount',
    ];

    protected $casts = [
        'old_amount' => 'decimal:2',
        'new_amount' => 'decimal:2',
    ];

    public function fee()
    {
        return $this->belongsTo(Fee::class);
    }

    public function editor()
    {
        return $this->belongsTo(User::class, 'edited_by');
    }
}
