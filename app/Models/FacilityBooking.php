<?php

namespace App\Models;

use App\Traits\HasTransactions;
use Illuminate\Database\Eloquent\Model;

class FacilityBooking extends Model
{
    use HasTransactions;

    protected $fillable = [
        'title', 'facility_name', 'booking_date', 'start_time', 'end_time',
        'booked_by', 'amount', 'payment_method', 'account_type', 'account_id',
        'status', 'reference_no', 'notes', 'recorded_by',
    ];

    protected $casts = ['booking_date' => 'date'];

    public function recorder()
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }
}
