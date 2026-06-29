<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdmitSeatCardSetting extends Model
{
    protected $fillable = [
        'card_type',
        'cards_per_page',
        'cards_per_row',
        'card_width_value',
        'card_height_value',
        'grid_gap_value',
        'card_dimension_unit',
    ];

    public static function current(int $cardType = 1): self
    {
        $cardType = in_array($cardType, [1, 2, 3, 4], true) ? $cardType : 1;

        return static::firstOrNew(['card_type' => $cardType]);
    }
}
