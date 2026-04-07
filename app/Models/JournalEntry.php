<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class JournalEntry extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'reference_no', 'date', 'description',
        'source_type', 'source_id',
        'created_by', 'updated_by',
    ];

    protected $casts = ['date' => 'date'];

    // ── Relationships ──────────────────────────────────────────

    public function lines()
    {
        return $this->hasMany(JournalEntryLine::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function source()
    {
        return $this->morphTo('source', 'source_type', 'source_id');
    }

    // ── Helpers ────────────────────────────────────────────────

    public static function generateReference(): string
    {
        $date     = now()->format('Ymd');
        $sequence = str_pad(static::whereDate('created_at', today())->count() + 1, 4, '0', STR_PAD_LEFT);
        return "JE-{$date}-{$sequence}";
    }

    /** Total debit across all lines */
    public function getTotalDebitAttribute(): float
    {
        return (float) $this->lines->sum('debit');
    }

    /** Total credit across all lines */
    public function getTotalCreditAttribute(): float
    {
        return (float) $this->lines->sum('credit');
    }

    /** True when debits == credits */
    public function getIsBalancedAttribute(): bool
    {
        return abs($this->total_debit - $this->total_credit) < 0.001;
    }

    // ── Booted ────────────────────────────────────────────────

    protected static function booted(): void
    {
        static::saving(function (JournalEntry $je) {
            $period = AccountingPeriod::where('start_date', '<=', $je->date)
                ->where('end_date', '>=', $je->date)
                ->where('is_closed', true)
                ->first();

            if ($period) {
                throw new \RuntimeException("Period '{$period->name}' is closed. Cannot save journal entry.");
            }
        });
    }
}
