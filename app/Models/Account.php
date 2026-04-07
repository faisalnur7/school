<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    protected $fillable = ['name', 'account_group_id', 'reference_type', 'reference_id', 'notes', 'type', 'opening_balance', 'description'];

    public function group()
    {
        return $this->belongsTo(AccountGroup::class, 'account_group_id');
    }

    public function reference()
    {
        return $this->morphTo('reference', 'reference_type', 'reference_id');
    }

    public function journalLines()
    {
        return $this->hasMany(JournalEntryLine::class);
    }

    /**
     * Derive balance from journal entry lines using the account's normal balance side.
     * Assets and expenses increase with debit, while liabilities, equity and income increase with credit.
     */
    public function getBalanceAttribute(): float
    {
        $debit   = (float) $this->journalLines()->sum('debit');
        $credit  = (float) $this->journalLines()->sum('credit');
        $opening = (float) ($this->opening_balance ?? 0);

        return match ($this->type) {
            'liability', 'equity', 'income' => $opening + $credit - $debit,
            default => $opening + $debit - $credit,
        };
    }

    public function getReferenceLabelAttribute(): string
    {
        if (!$this->reference_type || !$this->reference_id) {
            return $this->name;
        }

        $ref = $this->reference_type::find($this->reference_id);

        if (!$ref) return $this->name;

        return match ($this->reference_type) {
            BankAccount::class          => $ref->bank_name . ' — ' . $ref->account_number,
            MobileBankingAccount::class => $ref->provider  . ' — ' . $ref->account_number,
            HandCash::class             => $ref->label,
            default                     => $this->name,
        };
    }

    /**
     * Find the Account ID mapped to a given source model type + id.
     * Returns null if no account is mapped yet (journal posting is skipped gracefully).
     */
    public static function resolveForSource(string $type, int $id): ?int
    {
        return static::where('reference_type', $type)
            ->where('reference_id', $id)
            ->value('id');
    }
}
