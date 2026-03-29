<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BankAccount extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'bank_name', 'account_name', 'account_number',
        'branch_name', 'routing_number',
        'opening_balance', 'opening_date', 'is_active', 'notes',
    ];

    protected $casts = [
        'opening_date'    => 'date',
        'opening_balance' => 'decimal:2',
        'is_active'       => 'boolean',
    ];
}