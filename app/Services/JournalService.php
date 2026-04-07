<?php

namespace App\Services;

use App\Models\AccountingPeriod;
use App\Models\JournalEntry;
use App\Models\JournalEntryLine;
use Illuminate\Support\Facades\DB;

class JournalService
{
    /**
     * Create a balanced journal entry.
     *
     * $lines = [
     *   ['account_id' => 1, 'debit' => 500, 'credit' => 0, 'description' => '...'],
     *   ['account_id' => 2, 'debit' => 0,   'credit' => 500],
     * ]
     */
    public static function post(
        string $date,
        string $description,
        array  $lines,
        ?string $sourceType = null,
        ?int    $sourceId   = null,
        ?int    $userId     = null,
    ): JournalEntry {
        // Period lock check
        if (AccountingPeriod::isLocked($date)) {
            throw new \RuntimeException("The accounting period for {$date} is closed.");
        }

        // Balance check
        $totalDebit  = collect($lines)->sum('debit');
        $totalCredit = collect($lines)->sum('credit');

        if (abs($totalDebit - $totalCredit) > 0.001) {
            throw new \InvalidArgumentException(
                "Journal entry is not balanced. Debit: {$totalDebit}, Credit: {$totalCredit}"
            );
        }

        if (count($lines) < 2) {
            throw new \InvalidArgumentException('A journal entry must have at least 2 lines.');
        }

        return DB::transaction(function () use ($date, $description, $lines, $sourceType, $sourceId, $userId) {
            $je = JournalEntry::create([
                'reference_no' => JournalEntry::generateReference(),
                'date'         => $date,
                'description'  => $description,
                'source_type'  => $sourceType,
                'source_id'    => $sourceId,
                'created_by'   => $userId,
                'updated_by'   => $userId,
            ]);

            foreach ($lines as $line) {
                JournalEntryLine::create([
                    'journal_entry_id' => $je->id,
                    'account_id'       => $line['account_id'],
                    'debit'            => $line['debit']  ?? 0,
                    'credit'           => $line['credit'] ?? 0,
                    'description'      => $line['description'] ?? null,
                ]);
            }

            return $je;
        });
    }

    /**
     * Like post() but silently returns null if any account_id is null.
     * Use this when journal posting is best-effort (accounts may not be mapped yet).
     */
    public static function postSafe(
        string  $date,
        string  $description,
        array   $lines,
        ?string $sourceType = null,
        ?int    $sourceId   = null,
        ?int    $userId     = null,
    ): ?JournalEntry {
        foreach ($lines as $line) {
            if (empty($line['account_id'])) {
                return null;
            }
        }

        try {
            return self::post($date, $description, $lines, $sourceType, $sourceId, $userId);
        } catch (\Exception) {
            return null;
        }
    }

    /**
     * Reverse an existing journal entry (creates a mirror entry).
     */
    public static function reverse(JournalEntry $original, ?int $userId = null): JournalEntry
    {
        $lines = $original->lines->map(fn ($l) => [
            'account_id'  => $l->account_id,
            'debit'       => $l->credit,   // swap
            'credit'      => $l->debit,
            'description' => 'Reversal: ' . ($l->description ?? ''),
        ])->toArray();

        return self::post(
            now()->toDateString(),
            'Reversal of ' . $original->reference_no,
            $lines,
            null,
            null,
            $userId,
        );
    }

    /**
     * Shorthand: income transaction journal entry.
     * Dr Cash/Bank account, Cr Income account
     */
    public static function postIncome(int $cashAccountId, int $incomeAccountId, float $amount, string $date, string $description, ?string $sourceType = null, ?int $sourceId = null, ?int $userId = null): JournalEntry
    {
        return self::post($date, $description, [
            ['account_id' => $cashAccountId,   'debit' => $amount, 'credit' => 0],
            ['account_id' => $incomeAccountId, 'debit' => 0, 'credit' => $amount],
        ], $sourceType, $sourceId, $userId);
    }

    /**
     * Shorthand: expense transaction journal entry.
     * Dr Expense account, Cr Cash/Bank account
     */
    public static function postExpense(int $expenseAccountId, int $cashAccountId, float $amount, string $date, string $description, ?string $sourceType = null, ?int $sourceId = null, ?int $userId = null): JournalEntry
    {
        return self::post($date, $description, [
            ['account_id' => $expenseAccountId, 'debit' => $amount, 'credit' => 0],
            ['account_id' => $cashAccountId,    'debit' => 0, 'credit' => $amount],
        ], $sourceType, $sourceId, $userId);
    }

    /**
     * Shorthand: AR invoice creation.
     * Dr Accounts Receivable, Cr Income account
     */
    public static function postInvoice(int $arAccountId, int $incomeAccountId, float $amount, string $date, string $description, ?string $sourceType = null, ?int $sourceId = null, ?int $userId = null): JournalEntry
    {
        return self::post($date, $description, [
            ['account_id' => $arAccountId,     'debit' => $amount, 'credit' => 0],
            ['account_id' => $incomeAccountId, 'debit' => 0, 'credit' => $amount],
        ], $sourceType, $sourceId, $userId);
    }

    /**
     * Shorthand: AR payment received.
     * Dr Cash/Bank, Cr Accounts Receivable
     */
    public static function postInvoicePayment(int $cashAccountId, int $arAccountId, float $amount, string $date, string $description, ?string $sourceType = null, ?int $sourceId = null, ?int $userId = null): JournalEntry
    {
        return self::post($date, $description, [
            ['account_id' => $cashAccountId, 'debit' => $amount, 'credit' => 0],
            ['account_id' => $arAccountId,   'debit' => 0, 'credit' => $amount],
        ], $sourceType, $sourceId, $userId);
    }

    /**
     * Shorthand: AP bill creation.
     * Dr Expense account, Cr Accounts Payable
     */
    public static function postBill(int $expenseAccountId, int $apAccountId, float $amount, string $date, string $description, ?string $sourceType = null, ?int $sourceId = null, ?int $userId = null): JournalEntry
    {
        return self::post($date, $description, [
            ['account_id' => $expenseAccountId, 'debit' => $amount, 'credit' => 0],
            ['account_id' => $apAccountId,      'debit' => 0, 'credit' => $amount],
        ], $sourceType, $sourceId, $userId);
    }

    /**
     * Shorthand: AP vendor payment.
     * Dr Accounts Payable, Cr Cash/Bank
     */
    public static function postVendorPayment(int $apAccountId, int $cashAccountId, float $amount, string $date, string $description, ?string $sourceType = null, ?int $sourceId = null, ?int $userId = null): JournalEntry
    {
        return self::post($date, $description, [
            ['account_id' => $apAccountId,   'debit' => $amount, 'credit' => 0],
            ['account_id' => $cashAccountId, 'debit' => 0, 'credit' => $amount],
        ], $sourceType, $sourceId, $userId);
    }
}
