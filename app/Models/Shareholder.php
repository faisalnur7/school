<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Shareholder extends Model
{
    protected $fillable = ['name', 'phone', 'email', 'address'];

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function capitalBalance(): float
    {
        return (float) $this->transactions()
            ->selectRaw("SUM(CASE WHEN type='capital' THEN amount WHEN type='withdrawal' THEN -amount ELSE 0 END) as balance")
            ->value('balance');
    }
}
