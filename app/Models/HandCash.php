<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class HandCash extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'label', 'opening_amount', 'opening_date',
        'is_active', 'notes', 'recorded_by',
    ];

    protected $casts = [
        'opening_date'   => 'date',
        'opening_amount' => 'decimal:2',
        'is_active'      => 'boolean',
    ];

    public function recorder()
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }
}