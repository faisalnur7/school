<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssetIssue extends Model
{
    protected $fillable = [
        'asset_id', 'issued_to','department_id', 'issued_to_type',
        'quantity', 'issue_date', 'return_date',
        'status', 'notes', 'recorded_by',
    ];

    protected $casts = [
        'issue_date'  => 'date',
        'return_date' => 'date',
    ];

    public function asset()
    {
        return $this->belongsTo(Asset::class);
    }

    public function recorder()
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }
}
