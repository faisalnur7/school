<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AccountingPeriod extends Model
{
    protected $fillable = ['name', 'start_date', 'end_date', 'is_closed', 'closed_by', 'closed_at'];

    protected $casts = [
        'start_date' => 'date',
        'end_date'   => 'date',
        'is_closed'  => 'boolean',
        'closed_at'  => 'datetime',
    ];

    public function closedBy()
    {
        return $this->belongsTo(User::class, 'closed_by');
    }

    /** Check if a given date falls inside a closed period */
    public static function isLocked(\DateTimeInterface|string $date): bool
    {
        return static::where('start_date', '<=', $date)
            ->where('end_date', '>=', $date)
            ->where('is_closed', true)
            ->exists();
    }

    public function close(int $userId): void
    {
        $this->update([
            'is_closed' => true,
            'closed_by' => $userId,
            'closed_at' => now(),
        ]);
    }
}
