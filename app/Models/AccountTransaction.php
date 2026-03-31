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
    ];

    public static function record(string $accountType, int $accountId, string $type, float $amount, string $purpose, ?string $referenceNo, ?string $description, ?\DateTimeInterface $transactionDate = null, ?string $transactionableType = null, ?int $transactionableId = null, ?int $recordedBy = null): self
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

    public static function removeSource(string $transactionableType, int $transactionableId): void
    {
        $entries = self::where('transactionable_type', $transactionableType)
            ->where('transactionable_id', $transactionableId)
            ->get();

        foreach ($entries as $entry) {
            $reverseType = $entry->type === 'credit' ? 'debit' : 'credit';
            if ($reverseType === 'credit') {
                $entry->account_type::where('id', $entry->account_id)->increment('balance', $entry->amount);
            } else {
                $entry->account_type::where('id', $entry->account_id)->decrement('balance', $entry->amount);
            }
            $entry->delete();
        }
    }
}

