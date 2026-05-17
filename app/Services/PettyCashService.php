<?php

namespace App\Services;

use App\Models\AccountTransaction;
use App\Models\BankAccount;
use App\Models\HandCash;
use Illuminate\Database\Eloquent\Model;

class PettyCashService
{
    /** Returns the first active HandCash (petty cash) record. */
    public static function account(): ?HandCash
    {
        return HandCash::where('is_active', true)->orderBy('id')->first();
    }

    public static function credit(float $amount, string $purpose, ?string $ref, ?string $description, ?\DateTimeInterface $date = null, ?string $sourceType = null, ?int $sourceId = null): void
    {
        $account = self::account();
        if (!$account || $amount <= 0) return;

        AccountTransaction::upsertForSource(
            HandCash::class, $account->id,
            'credit', $amount, $purpose, $ref, $description,
            $date ?? now(), $sourceType, $sourceId, auth()->id()
        );
    }

    public static function debit(float $amount, string $purpose, ?string $ref, ?string $description, ?\DateTimeInterface $date = null, ?string $sourceType = null, ?int $sourceId = null): void
    {
        $account = self::account();
        if (!$account || $amount <= 0) return;

        AccountTransaction::upsertForSource(
            HandCash::class, $account->id,
            'debit', $amount, $purpose, $ref, $description,
            $date ?? now(), $sourceType, $sourceId, auth()->id()
        );
    }

    public static function transferToBank(int $bankAccountId, float $amount, ?string $description = null): void
    {
        $petty = self::account();
        $bank  = BankAccount::findOrFail($bankAccountId);

        if ($amount <= 0) throw new \InvalidArgumentException('Amount must be greater than zero.');
        if ((float) $petty->balance < $amount) throw new \InvalidArgumentException('Insufficient petty cash balance.');

        $ref = 'TRF-' . now()->format('Ymd') . '-' . str_pad(
            AccountTransaction::whereDate('created_at', today())->count() + 1, 4, '0', STR_PAD_LEFT
        );

        // Debit petty cash
        AccountTransaction::record(
            HandCash::class, $petty->id,
            'debit', $amount, 'bank_transfer', $ref,
            $description ?? "Transfer to {$bank->bank_name}", now(),
            null, null, auth()->id()
        );

        // Credit bank account
        AccountTransaction::record(
            BankAccount::class, $bank->id,
            'credit', $amount, 'bank_transfer', $ref,
            $description ?? "Transfer from Petty Cash", now(),
            null, null, auth()->id()
        );
    }
}
