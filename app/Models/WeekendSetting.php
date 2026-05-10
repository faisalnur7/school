<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WeekendSetting extends Model
{
    protected $fillable = ['weekend_days'];

    protected $casts = ['weekend_days' => 'array'];

    /**
     * Get the single global instance (or a default with Fri+Sat).
     */
    public static function current(): self
    {
        return static::first() ?? new self(['weekend_days' => [5, 6]]);
    }

    /**
     * Returns array of weekday integers that are weekends.
     * 0=Sunday, 1=Monday, ..., 6=Saturday
     */
    public function days(): array
    {
        return $this->weekend_days ?? [5, 6];
    }
}
