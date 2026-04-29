# Accounting Module Tutorial

This tutorial explains how to use the accounting part of this application in the correct operational order.

## 1. What is included in the accounting module

Main areas in this app:

- `Income Categories`
- `Expense Categories`
- `Hand Cash / Bank Accounts / Mobile Banking Accounts`
- `Shareholders` and `Shareholder Transactions` (Capital, Withdrawal)
- `Transactions` (combined accounting transaction list)
- `Ledger` (debit/credit style view)
- `Account Groups` and `Accounts List` (chart/account mapping)
- `Accounting Periods` (open/close periods)
- `Journal Entries` (system-posted entries view)
- `Reports` (Trial Balance, Balance Sheet, Cash Flow, Income Expenditure, etc.)

---

## 2. First-time setup checklist

Complete these once before daily use.

### Step 1: Create income and expense heads

1. Go to `Income Categories` and add all income heads (Tuition, Admission Fee, etc.).
2. Go to `Expense Categories` and add all expense heads (Salary, Utility, Maintenance, etc.).

### Step 2: Create cash and bank sources

1. Go to `Hand Cash` and create cash accounts (e.g., Main Cash, Petty Cash).
2. Go to `Bank Accounts` and create all bank accounts.
3. Go to `Mobile Banking Accounts` and create wallets/accounts if used.

### Step 3: Create shareholders (if capital tracking is needed)

1. Go to `Shareholders`.
2. Add shareholder names and contact info.

### Step 4: Configure account groups and account mappings

1. Go to `Account Groups` and create group hierarchy.
2. Go to `Accounts List` and create accounts.
3. Link accounts to physical accounts where needed:
   - Bank account
   - Hand cash
   - Mobile banking account
4. Also create mapped accounts for income categories, expense categories, and shareholder capital/drawings if you want full journal posting coverage.

### Step 5: Create accounting period

1. Go to `Accounting Periods`.
2. Create the period (for example FY start/end dates).
3. Keep open while posting transactions.
4. Close only after period-end checks are done.

---

## 3. Daily operations

## 3.1 Record income

1. Go to `Incomes`.
2. Click `Create`.
3. Fill:
   - Category
   - Title
   - Amount
   - Date
   - Payment method
   - Account source (cash/bank/mobile) if applicable
4. Save.

Result:
- Income record is created.
- Transaction list entry is created.
- Cash/bank balance movement is recorded.
- Journal posting is attempted based on account mappings.

## 3.2 Record expense

1. Go to `Expenses`.
2. Click `Create`.
3. Fill:
   - Expense category
   - Title
   - Amount
   - Date
   - Payment method
   - Account source (cash/bank/mobile)
4. Save.

Result:
- Expense record is created.
- Transaction list entry is created.
- Cash/bank balance movement is recorded.
- Journal posting is attempted based on account mappings.

## 3.3 Record capital and withdrawals

1. Go to `Shareholder Transactions` (or `Transactions` create route).
2. Choose `capital` or `withdrawal`.
3. Select shareholder.
4. Enter amount, date, payment method, and account source.
5. Save.

Result:
- Capital/withdrawal transaction is created.
- Cash/bank movement is recorded.
- Journal posting is attempted from account mappings.

## 3.4 Record asset purchases

1. Go to `Asset Purchases`.
2. Add items and quantities.
3. Select payment source.
4. Save.

Result:
- Purchase and item records are created.
- Stock quantity is increased.
- Expense/transaction postings are generated based on configured flow.

---

## 4. Review and control process (recommended daily/weekly)

### A. Check transactions

1. Open `Transactions`.
2. Filter by date, type, category, payment method.
3. Validate amount and narration.

### B. Check ledger

1. Open `Ledger`.
2. Review debit account and credit account columns.
3. Check net movement for the period.

### C. Check account balances

1. Review `Hand Cash`, `Bank Accounts`, and `Mobile Banking Accounts` balances.
2. Compare with physical/bank statements.

### D. Check journal entries

1. Open `Journal Entries`.
2. Search by date/reference.
3. Open entry details and confirm debit/credit lines.

---

## 5. Reports usage guide

Go to `Reports` and use period filters before export.

- `Trial Balance`: quick debit/credit summary view.
- `Balance Sheet`: equity-focused snapshot for selected year.
- `Income & Expenditure`: category-wise income vs expense.
- `Cash Flow`: operating + financing movement summary.
- `Cash Book / Day Book / Receipt-Payment`: operational cash movement statements.
- `Chart of Accounts`: grouped account listing with balances.
- `Headwise Transactions`: income/expense category transaction drill-down.

Tip: Use on-screen filters first, then use PDF export for final print/share.

---

## 6. Month-end closing checklist

1. Ensure all incomes/expenses are posted.
2. Ensure shareholder capital/withdrawal transactions are complete.
3. Reconcile cash and bank balances.
4. Review journal entries for missing mappings.
5. Export key reports (Trial Balance, Income & Expenditure, Balance Sheet, Cash Flow).
6. Lock period from `Accounting Periods` only after final approval.

---

## 7. Common mistakes and how to avoid them

- Missing account mapping: can cause incomplete journal output.
  - Fix by creating proper `Accounts List` mappings for categories and sources.
- Wrong payment source selected:
  - Recheck `account_type`/`account_id` before saving income/expense.
- Closing period too early:
  - Always finish reconciliation before closing.
- Editing historical entries without review:
  - Re-run reports after edits to verify totals.

---

## 8. Suggested operating policy

- Use one responsible user/accountant role for final posting.
- Enforce daily transaction posting cutoff.
- Do weekly reconciliation and monthly formal closing.
- Keep PDF exports archived by month and fiscal year.

---

## 9. Quick start (short version)

1. Setup categories.
2. Setup cash/bank/mobile accounts.
3. Setup account groups and accounts mapping.
4. Create accounting period.
5. Post daily income/expense/capital/withdrawal.
6. Check transactions + ledger + journals.
7. Run reports.
8. Reconcile and close period.
