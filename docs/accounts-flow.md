# Accounts Module — How It Actually Works & What Needs to Change

## The Problem with the Current Design

The system has **two parallel, disconnected accounting layers** that don't talk to each other:

### Layer 1 — `transactions` + `account_transactions` (what actually runs the app)
Every real money event (income, expense, fee payment, capital, withdrawal) writes to:
- `transactions` — the business-level record (type, category, amount, date)
- `account_transactions` — the cash/bank balance ledger (credit/debit on HandCash, BankAccount, MobileBankingAccount)

This layer is **fully automatic**. The user never touches debit/credit.

### Layer 2 — `journal_entries` + `journal_entry_lines` (the accounting module)
This is a separate double-entry system linked to `accounts` (Chart of Accounts). It has a `JournalService` with shorthand methods (`postIncome`, `postExpense`, etc.) but **nothing calls them**. The journal tables are empty in practice.

### The result
- The Ledger, Trial Balance, Balance Sheet, Cash Book, Day Book, and Income & Expenditure reports all read from `transactions` — not from `journal_entries`.
- The Chart of Accounts (`accounts` table) and Journal Entries are orphaned — they exist but have no data flowing into them.
- Manual journal entry is exposed to users, which means users must know debit/credit rules — which you don't want.

---

## The Correct Flow (What Should Happen)

```
User Action
    │
    ▼
Income / Expense / Fee Payment / Capital / Withdrawal recorded
    │
    ├──► transactions (auto)          ← already works
    ├──► account_transactions (auto)  ← already works (cash/bank balance)
    └──► journal_entries (MISSING)    ← needs to be wired up automatically
              │
              └──► journal_entry_lines (debit/credit auto-determined by type)
```

The user should **never** see or touch debit/credit. The system should auto-post journal entries using `JournalService` based on the transaction type.

---

## Auto Journal Entry Rules (by Transaction Type)

| Transaction Type | Debit Account | Credit Account |
|---|---|---|
| Income | Cash/Bank account selected | Income category account |
| Expense | Expense category account | Cash/Bank account selected |
| Fee Payment | Cash/Bank account selected | Student Fees Receivable account |
| Capital (Shareholder) | Cash/Bank account selected | Capital — Shareholder account |
| Withdrawal (Shareholder) | Drawings — Shareholder account | Cash/Bank account selected |
| Asset Purchase | Asset account | Cash/Bank account selected |
| Payroll / Salary | Salary Expense account | Cash/Bank account selected |

These rules are already coded in `JournalService` — they just need to be called.

---

## What Needs to Be Done

### Step 1 — Wire `JournalService` into existing controllers

Each controller that creates a transaction must also call `JournalService::post()`. The account IDs need to be resolved from the Chart of Accounts.

**IncomeController@store** — add after `Income::create()`:
```
JournalService::postIncome(
    cashAccountId:   resolveAccount($income->account_type, $income->account_id),
    incomeAccountId: resolveAccount('income_category', $income->income_category_id),
    amount:          $income->amount,
    date:            $income->income_date,
    description:     $income->title,
    sourceType:      Income::class,
    sourceId:        $income->id,
    userId:          auth()->id(),
);
```

Same pattern for `ExpenseController`, `PaymentController`, `ShareholderTransactionController`.

### Step 2 — Link Chart of Accounts to real entities

Each `Account` record has `reference_type` and `reference_id` columns. These should be populated to map:

| Account Name | reference_type | reference_id |
|---|---|---|
| Cash in Hand | `App\Models\HandCash` | hand_cash.id |
| BRAC Bank | `App\Models\BankAccount` | bank_accounts.id |
| bKash | `App\Models\MobileBankingAccount` | mobile_banking_accounts.id |
| Tuition Fee Income | `App\Models\IncomeCategory` | income_categories.id |
| Salary Expense | `App\Models\ExpenseCategory` | expense_categories.id |

A helper method `Account::resolveForSource($type, $id)` can look up the correct account ID automatically.

### Step 3 — Remove manual Journal Entry from the UI

The `/journal-entries/create` page should be removed or restricted to admin-only for correction entries. Users should never manually post journal entries.

### Step 4 — Keep the Accounts module menu as read-only views

| Menu Item | Purpose after fix |
|---|---|
| Account Groups | Setup only — organise accounts into groups |
| Chart of Accounts | Setup only — map accounts to real cash/bank/category entities |
| Accounting Periods | Setup + close periods |
| Ledger | Read-only — shows `transactions` (already works) |
| Trial Balance | Read-only — reads `transactions` (already works) |
| Balance Sheet | Read-only — reads `transactions` (already works) |
| Cash Book | Read-only — reads `transactions` (already works) |
| Day Book | Read-only — reads `transactions` (already works) |
| Income & Expenditure | Read-only — reads `transactions` (already works) |

---

## Recommended Setup Order (One-Time)

```
1. Create Account Groups
   Examples: Assets, Liabilities, Income, Expenses, Equity

2. Create Accounts in Chart of Accounts
   - Link each cash/bank account to its HandCash / BankAccount / MobileBankingAccount record
   - Link each income account to its IncomeCategory record
   - Link each expense account to its ExpenseCategory record
   - Create equity accounts: Capital, Drawings

3. Create an Accounting Period
   Example: FY 2025-26 → 01/07/2025 to 30/06/2026

4. Start recording transactions normally
   (Income, Expenses, Fee Payments, Capital, Withdrawals)
   → Journal entries post automatically, no debit/credit input needed

5. Use reports to review
   Ledger → Cash Book → Day Book → Trial Balance → Balance Sheet → Income & Expenditure

6. Close the period at year-end
```

---

## Current State Summary

| Feature | Status |
|---|---|
| Income recording | ✅ Works — auto-posts to `transactions` + `account_transactions` |
| Expense recording | ✅ Works — auto-posts to `transactions` + `account_transactions` |
| Fee payment recording | ✅ Works — auto-posts to `transactions` + `account_transactions` |
| Capital / Withdrawal | ✅ Works — auto-posts to `transactions` + `account_transactions` |
| Cash/Bank balance tracking | ✅ Works via `account_transactions` |
| Ledger view | ✅ Works — reads `transactions` |
| Trial Balance | ✅ Works — reads `transactions` |
| Balance Sheet | ✅ Works — reads `transactions` |
| Cash Book | ✅ Works — reads `transactions` |
| Day Book | ✅ Works — reads `transactions` |
| Income & Expenditure | ✅ Works — reads `transactions` |
| Chart of Accounts | ⚠️ Exists but not linked to transaction flow |
| Journal Entries (auto) | ❌ `JournalService` exists but is never called |
| Journal Entries (manual) | ❌ Should be removed from user-facing UI |
| Account Groups | ⚠️ Exists but only useful once journal entries are wired |
| Accounting Period lock | ⚠️ Only enforced on manual journal entries, not on `transactions` |
