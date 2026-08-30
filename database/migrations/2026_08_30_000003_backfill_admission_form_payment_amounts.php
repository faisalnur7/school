<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (! DB::getSchemaBuilder()->hasTable('admission_payments')) {
            return;
        }

        $categoryId = DB::table('income_categories')
            ->where('slug', 'admission-form')
            ->value('id');

        if (! $categoryId) {
            return;
        }

        $transactions = DB::table('transactions')
            ->where('type', 'income')
            ->where('income_category_id', $categoryId)
            ->where('amount', 0)
            ->where('transactionable_type', 'App\\Models\\Income')
            ->get(['id', 'transactionable_id']);

        foreach ($transactions as $transaction) {
            $income = DB::table('incomes')->where('id', $transaction->transactionable_id)->first([
                'id', 'title', 'amount',
            ]);

            if (! $income || ! preg_match('/ - (.+)$/', $income->title, $matches)) {
                continue;
            }

            $fee = DB::table('admission_applications as applications')
                ->join('admission_exams as exams', 'exams.id', '=', 'applications.admission_exam_id')
                ->where('applications.application_number', $matches[1])
                ->value('exams.form_fee');

            $amount = round((float) $fee, 2);
            if ($amount <= 0) {
                continue;
            }

            DB::table('transactions')->where('id', $transaction->id)->update(['amount' => $amount]);
            DB::table('incomes')->where('id', $income->id)->update(['amount' => $amount]);

            $applicationId = DB::table('admission_applications')
                ->where('application_number', $matches[1])
                ->value('id');

            if ($applicationId) {
                DB::table('admission_payments')
                    ->where('admission_application_id', $applicationId)
                    ->where('amount', 0)
                    ->update([
                        'amount' => $amount,
                        'gross_amount' => $amount,
                        'total_amount' => $amount,
                    ]);
            }

            $journalEntryIds = DB::table('journal_entries')
                ->where('source_type', 'App\\Models\\Income')
                ->where('source_id', $income->id)
                ->pluck('id');

            $journalLines = DB::table('journal_entry_lines')
                ->whereIn('journal_entry_id', $journalEntryIds)
                ->get(['id', 'debit', 'credit']);

            foreach ($journalLines as $line) {
                DB::table('journal_entry_lines')->where('id', $line->id)->update([
                    'debit' => (float) $line->debit > 0 ? $amount : 0,
                    'credit' => (float) $line->credit > 0 ? $amount : 0,
                ]);
            }

            DB::table('account_transactions')
                ->where('transactionable_type', 'App\\Models\\Income')
                ->where('transactionable_id', $income->id)
                ->where('amount', 0)
                ->update(['amount' => $amount]);
        }

        $this->rebuildCashBalances();
    }

    private function rebuildCashBalances(): void
    {
        $accounts = DB::table('hand_cashes')->pluck('id');

        foreach ($accounts as $accountId) {
            $balance = (float) DB::table('hand_cashes')->where('id', $accountId)->value('opening_amount');
            $entries = DB::table('account_transactions')
                ->where('account_type', 'App\\Models\\HandCash')
                ->where('account_id', $accountId)
                ->whereNull('deleted_at')
                ->orderBy('id')
                ->get(['id', 'type', 'amount']);

            foreach ($entries as $entry) {
                $balance += $entry->type === 'credit' ? (float) $entry->amount : -(float) $entry->amount;
                DB::table('account_transactions')->where('id', $entry->id)->update(['balance_after' => $balance]);
            }

            DB::table('hand_cashes')->where('id', $accountId)->update(['balance' => $balance]);
        }
    }
};
