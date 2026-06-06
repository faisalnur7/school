<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AccountTransaction extends Model
{
    protected $fillable = [
        'account_type',
        'account_id',
        'type',
        'amount',
        'balance_after',
        'purpose',
        'reference_no',
        'description',
        'transaction_date',
        'transactionable_type',
        'transactionable_id',
        'recorded_by',
        'is_reversal',
        'reversed_id',
    ];

    public static function record(string $accountType, int $accountId, string $type, float $amount, string $purpose, ?string $referenceNo, ?string $description, ?\DateTimeInterface $transactionDate = null, ?string $transactionableType = null, ?int $transactionableId = null, ?int $recordedBy = null, bool $isReversal = false, ?int $reversedId = null): self
    {
        $date = $transactionDate ? $transactionDate->format('Y-m-d') : now()->format('Y-m-d');

        // Update live balance on the account and use it as balance_after
        if ($type === 'credit') {
            $accountType::where('id', $accountId)->increment('balance', $amount);
        } else {
            $accountType::where('id', $accountId)->decrement('balance', $amount);
        }

        $balanceAfter = (float) $accountType::where('id', $accountId)->value('balance');

        return self::create([
            'account_type'         => $accountType,
            'account_id'           => $accountId,
            'type'                 => $type,
            'amount'               => $amount,
            'balance_after'        => $balanceAfter,
            'purpose'              => $purpose,
            'reference_no'         => $referenceNo,
            'description'          => $description,
            'transaction_date'     => $date,
            'transactionable_type' => $transactionableType,
            'transactionable_id'   => $transactionableId,
            'recorded_by'          => $recordedBy,
            'is_reversal'          => $isReversal,
            'reversed_id'          => $reversedId,
        ]);
    }

    public static function upsertForSource(string $accountType, int $accountId, string $direction, float $amount, string $purpose, ?string $referenceNo, ?string $description, ?\DateTimeInterface $transactionDate, ?string $transactionableType, ?int $transactionableId, ?int $recordedBy = null): self
    {
        // Reverse the old entry's effect on the account balance before deleting
        $old = self::where('transactionable_type', $transactionableType)
            ->where('transactionable_id', $transactionableId)
            ->first();

        if ($old) {
            $reverseType = $old->type === 'credit' ? 'debit' : 'credit';
            if ($reverseType === 'credit') {
                $old->account_type::where('id', $old->account_id)->increment('balance', $old->amount);
            } else {
                $old->account_type::where('id', $old->account_id)->decrement('balance', $old->amount);
            }
            $old->delete();
        }

        return self::record($accountType, $accountId, $direction, $amount, $purpose, $referenceNo, $description, $transactionDate, $transactionableType, $transactionableId, $recordedBy);
    }

    public static function reverseEntry(self $entry, ?int $recordedBy = null, ?string $reason = null): self
    {
        $reverseType = $entry->type === 'credit' ? 'debit' : 'credit';
        $referenceNo = $entry->reference_no ? $entry->reference_no . '-REV' : null;
        $description = $reason ?? 'Reversal of ' . ($entry->description ?? $entry->purpose);

        return self::record(
            $entry->account_type,
            $entry->account_id,
            $reverseType,
            $entry->amount,
            $entry->purpose,
            $referenceNo,
            $description,
            now(),
            $entry->transactionable_type,
            $entry->transactionable_id,
            $recordedBy ?? $entry->recorded_by,
            true,
            $entry->id
        );
    }

    public static function removeSource(string $transactionableType, int $transactionableId): void
    {
        $entries = self::where('transactionable_type', $transactionableType)
            ->where('transactionable_id', $transactionableId)
            ->where('is_reversal', false)
            ->get();

        foreach ($entries as $entry) {
            self::reverseEntry($entry, $entry->recorded_by, 'Reversal of account transaction');
        }
    }
}

