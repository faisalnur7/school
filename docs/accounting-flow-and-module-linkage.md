# Accounting Flow and Module Linkage Guide

## Objective

This document explains how financial data moves through the application and how related modules (salary, payroll, shareholder, asset purchase, student payment, income/expense) are linked to the accounting module.

It also captures the current structure and required practices for pure accounting discipline.

---

## 1. Accounting Core Structure

## 1.1 Primary accounting tables

- `accounts`
- `account_groups`
- `journal_entries`
- `journal_entry_lines`
- `transactions` (operational summary ledger)
- `account_transactions` (cash/bank/mobile running balance movements)
- `accounting_periods`

## 1.2 Supporting financial tables

- `incomes`, `income_categories`
- `expenses`, `expense_categories`
- `payments`, `payment_items` (student collections)
- `hr_payrolls`, `hr_transactions`, `salary_structures`
- `shareholders`
- `asset_purchases`, `asset_purchase_items`
- `bank_accounts`, `mobile_banking_accounts`, `hand_cashes`

## 1.3 Posting engine

- `App\Services\JournalService` is the double-entry posting service.
- `post()` enforces balance (`total_debit == total_credit`) and minimum 2 lines.
- `postSafe()` is best-effort mode and may skip posting when account mapping is missing.

---

## 2. Pure Accounting Practice Rules

To keep the system in proper accounting mode, follow these rules:

1. Every financial event must create balanced journal lines.
2. Debit and credit sides must represent the correct account type behavior.
3. Cash/Bank/Mobile movement must be linked through `account_type + account_id`.
4. Income/Expense categories used in posting should have mapped accounts in `accounts`.
5. Do not bypass mapped accounts when recording major transactions.
6. Close accounting periods only after reconciliation and report review.

---

## 3. Module Linkage Matrix

## 3.1 Income module

Flow:
1. `incomes` row created.
2. `transactions` row created (`type=income`).
3. `account_transactions` credit movement recorded (if account source provided).
4. Journal entry posted (Dr Cash/Bank/Mobile, Cr Income Head).

Controllers/services involved:
- `IncomeController`
- `JournalService`
- `AccountTransaction`

## 3.2 Expense module

Flow:
1. `expenses` row created.
2. `transactions` row created (`type=expense`).
3. `account_transactions` debit movement recorded (if account source provided).
4. Journal entry posted (Dr Expense Head, Cr Cash/Bank/Mobile).

Controllers/services involved:
- `ExpenseController`
- `JournalService`
- `AccountTransaction`

## 3.3 Student Payments module

Flow:
1. `payments` and `payment_items` are created from fee collection.
2. Fee rows (`fees`) are updated (paid/partial).
3. Payment is converted to accounting income using `recordIncome()`.
4. Income posting creates:
   - `incomes`
   - `transactions`
   - `account_transactions` (through account mapping)
   - journal lines

Important implementation note:
- Payment method is normalized to accounting-friendly values:
  - `cash` -> `Cash`
  - `bank` -> `Bank Transfer`
  - `mobile_wallet` -> `Mobile Banking`

## 3.4 Salary Structure + Payroll module

### Salary structures
- `salary_structures` stores compensation components and effective date.
- Employee latest salary is used in payroll generation.

### Payroll generation
Flow:
1. `hr_payrolls` row created per employee/month/year.
2. `hr_transactions` row created.
3. Salary expense posted to:
   - `expenses`
   - `transactions`
4. Journal entry posted:
   - Dr Salary Expense
   - Cr payout source account (Cash/Bank/Mobile)

Current implementation maps payroll method to valid expense payment enums and links payout source account in expense posting.

## 3.5 Shareholder Capital/Withdrawal module

Flow:
1. `transactions` created as `capital` or `withdrawal`.
2. `account_transactions` recorded for cash/bank movement.
3. Journal entry posted:
   - Capital: Dr Cash/Bank, Cr Equity (Shareholder)
   - Withdrawal: Dr Drawings/Equity, Cr Cash/Bank

## 3.6 Asset Purchases module

Flow:
1. `asset_purchases` and `asset_purchase_items` created.
2. Asset stock quantity increased.
3. Expense posting created via trait-driven flow.
4. `account_transactions` movement recorded.
5. Journal entry attempted through expense posting flow.

---

## 4. Sales module status

There is no dedicated standalone `sales` module entity in the current codebase. Revenue recognition currently enters accounting primarily through:

- Student payments (fee collection)
- General income entries

If a formal sales module is required (invoice -> delivery -> receipt), it should be implemented as a separate operational module and linked to journal posting like the others.

---

## 5. Seeder Status for Accounting Modules

Accounting seeders are already present. No duplicate seeder creation is required.

Existing seeders include:

- `IncomeCategorySeeder`
- `ExpenseCategorySeeder`
- `HandCashSeeder`
- `BankAccountSeeder`
- `ShareHolderSeeder`
- `DemoAccountingSeeder`
- `BudgetAllocationSeeder`
- `SalaryStructureForAllEmployeesSeeder`

Use:

```bash
php artisan db:seed
```

or targeted:

```bash
php artisan db:seed --class=DemoAccountingSeeder
php artisan db:seed --class=SalaryStructureForAllEmployeesSeeder
```

---

## 6. Required Master Data Before Live Operations

1. Account Groups
2. Accounts (with proper type and linkage)
3. Income Categories and Expense Categories
4. Cash/Bank/Mobile accounts
5. Shareholder mapped equity accounts
6. Salary categories and payroll payout source readiness
7. Accounting periods

---

## 7. Reconciliation Checklist

Daily/Weekly:

1. Compare `transactions` totals with income/expense source tables.
2. Verify `account_transactions` balances against cashbook/bank statements.
3. Verify journal debit/credit balance consistency for sampled entries.
4. Review missing account mappings if expected journal entries are absent.

Month-end:

1. Run Trial Balance, Income/Expenditure, Cash Flow, Balance Sheet reports.
2. Validate payroll postings for month.
3. Validate shareholder movement postings.
4. Validate student payment postings and fee status updates.
5. Close accounting period.

---

## 8. Implementation Notes (Recent Fixes)

Recent accounting linkage corrections applied:

1. Payroll expense payment method mapping now uses valid expense enum values.
2. Payroll salary journal credit uses payout source account mapping (cash/bank/mobile).
3. Student payment posting no longer duplicates account transaction movement.
4. Student payment posting now forwards account source mapping into accounting income flow.

These changes improve consistency between operational modules and accounting postings.

---

## 9. Recommended Next Improvements

1. Replace best-effort `postSafe()` with strict posting for critical modules.
2. Add automated mapping validation dashboard (unmapped income/expense/shareholder heads).
3. Add explicit General Ledger report from `journal_entry_lines` by account.
4. Add formal sales/invoice accounting workflow if required by business.
5. Implement a full audit trail model for accounting mutations.
