<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentInformation extends Model
{
    protected $table    = 'payment_informations';
    protected $fillable = [
        'employee_id', 'payment_method', 'bank_name', 'account_number',
        'mobile_wallet_number', 'mobile_wallet_provider',
    ];

    public function employee() { return $this->belongsTo(Employee::class); }
}
