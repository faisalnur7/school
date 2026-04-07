# Accounts Module — User Manual

## Overview

The Accounts module provides a complete double-entry bookkeeping system. The recommended setup order is:

```
Account Groups → Chart of Accounts → Accounting Periods → Journal Entries → Reports
```

---

## 1. Account Groups

**Location:** Accounts → Account Groups

Account Groups are categories that organise your Chart of Accounts (e.g. Assets, Liabilities, Income, Expenses).

### Create a Group
1. On the Account Groups page, fill in the **Name** field.
2. Optionally select a **Parent Group** to create a sub-group (e.g. "Bank Accounts" under "Assets").
3. Click **Save**.

### Edit a Group
1. Click the **Edit** icon next to the group.
2. Update the name or parent, then click **Update**.

### Delete a Group
1. Click the **Delete** icon next to the group and confirm.

> **Note:** Do not delete a group that has accounts assigned to it.

---

## 2. Chart of Accounts

**Location:** Accounts → Chart of Accounts

Individual ledger accounts live here. Every financial transaction is posted to an account.

### Create an Account
1. Click **New Account**.
2. Fill in:
   - **Name** — descriptive label (e.g. "Cash in Hand")
   - **Account Group** — select the parent group
   - **Type** — asset / liability / income / expense / equity
   - **Opening Balance** — starting balance if migrating from another system
   - **Notes** — optional description
3. Click **Save**.

### Edit an Account
1. Click **Edit** next to the account, modify the fields, and click **Update**.

### Delete an Account
1. Click **Delete** next to the account and confirm.

> Accounts with posted journal lines cannot be deleted.

---

## 3. Accounting Periods

**Location:** Accounts → Accounting Periods

Accounting Periods define fiscal date ranges (e.g. "FY 2024-25"). Journal entries **cannot** be posted to a closed period.

### Create a Period
1. Enter a unique **Name** (e.g. `FY 2024-25`).
2. Set **Start Date** and **End Date** (end must be after start).
3. Click **Save**.

### Edit a Period
1. Click **Edit** next to an open period, update the fields, and click **Update**.

> Closed periods cannot be edited.

### Close a Period
1. Click **Close** next to an open period and confirm.

> This action is **irreversible**. Once closed, no journal entries can be posted for dates within that period.

### Delete a Period
1. Click **Delete** next to an open period and confirm.

> Closed periods cannot be deleted.

---

## 4. Journal Entries

Journal entries are the core of double-entry accounting. Every transaction must have equal debits and credits. They are auto-posted when income/expense transactions are recorded, and can also be created manually.

### View All Entries
- Navigate to `/journal-entries`.
- Filter by **Date From / To** or search by **Reference No** or **Description**.

### Create a Manual Entry
1. Go to `/journal-entries/create`.
2. Set the **Date** and optional **Description**.
3. Add at least **2 lines**, selecting an **Account** and entering either a **Debit** or **Credit** amount per line.
4. Ensure total Debits = total Credits (the form will warn you if unbalanced).
5. Click **Post Entry**.

A reference number is auto-generated in the format `JE-YYYYMMDD-XXXX`.

### View an Entry
- Click the **View** icon on any entry to see all lines, accounts, and the posting user.

### Delete an Entry
- Click **Delete** on the entry. Soft-deleted entries are removed from all reports.

**Rules enforced:**
- Entry must have ≥ 2 lines.
- Total debit must equal total credit (within 0.001 tolerance).
- Date must not fall within a closed accounting period.

---

## 5. Ledger

**Location:** Accounts → Ledger

The Ledger shows all financial transactions in chronological order with running totals.

### Filter the Ledger
Use the filter bar to narrow results by:
- **Type** — Income / Expense / Capital / Withdrawal
- **Payment Method** — Cash, Bank, Mobile Banking, etc.
- **Shareholder** — filter by a specific shareholder
- **From / To Date** — date range (format: `DD/MM/YYYY`)

The page displays:
- **Total Debit** — sum of expenses and withdrawals
- **Total Credit** — sum of income and capital contributions

---

## 6. Trial Balance

**Location:** Accounts → Trial Balance

Summarises all account balances for a selected year to verify that debits equal credits.

### Generate
1. Select the **Year** from the dropdown (defaults to current year).
2. The report auto-loads showing:

| Account | Debit | Credit |
|---|---|---|
| Income | — | Total income |
| Expenses | Total expenses | — |
| Capital Contributions | — | Total capital |
| Drawings / Withdrawals | Total withdrawals | — |

A balanced set of books will show **Total Debit = Total Credit**.

---

## 7. Balance Sheet

**Location:** Accounts → Balance Sheet

Shows the financial position (equity) for a selected year.

### Generate
1. Select the **Year** and click **Filter**.

The report shows:
- **Net Income** = Total Income − Total Expenses
- **Capital** = Total capital contributions
- **Withdrawals** = Total drawings
- **Equity** = Capital − Withdrawals + Net Income

---

## 8. Cash Book

**Location:** Accounts → Cash Book

Shows all **cash** transactions (payment method = Cash) within a date range.

### Generate
1. Set **From** and **To** dates (format: `DD/MM/YYYY`). Defaults to the current month.
2. Click **Filter**.

The report shows each cash transaction with date, description, type, and amount, plus:
- **Total In** — cash receipts (income + capital)
- **Total Out** — cash payments (expenses + withdrawals)

---

## 9. Day Book

**Location:** Accounts → Day Book

Shows **all transactions for a single day** across all payment methods.

### Generate
1. Select a **Date** (format: `DD/MM/YYYY`). Defaults to today.
2. Click **Filter**.

The report shows every transaction on that date with:
- **Total Debit** — expenses + withdrawals
- **Total Credit** — income + capital

---

## 10. Income & Expenditure

**Location:** Accounts → Income & Expenditure

Shows income and expenses broken down by category for a selected year.

### Generate
1. Select the **Year** and click **Filter**.

The report shows:
- **Income by Category** — each income category with its total
- **Expenses by Category** — each expense category with its total
- **Surplus / Deficit** = Total Income − Total Expenses

> A negative surplus indicates a deficit.

---

## Typical Workflow

```
1. Create Account Groups       (Assets, Liabilities, Income, Expenses, Equity)
2. Create Accounts             (Cash, Bank, Tuition Fee Income, Salaries, etc.)
3. Create an Accounting Period (e.g. FY 2024-25: 01/07/2024 – 30/06/2025)
4. Record transactions         (income/expenses auto-post journal entries)
5. Post manual Journal Entries for adjustments if needed
6. Review the Ledger           for a full transaction history
7. Run Trial Balance           to verify books are balanced
8. Run Balance Sheet           for financial position
9. Run Cash Book / Day Book    for operational review
10. Run Income & Expenditure   for performance summary
11. Close the Accounting Period at year-end
```

---

## Important Rules & Constraints

| Rule | Detail |
|---|---|
| Balanced entries | Every journal entry must have equal debits and credits |
| Minimum lines | A journal entry requires at least 2 lines |
| Closed period lock | No entries can be posted to a closed accounting period |
| Period close is final | A closed period cannot be reopened or deleted |
| Account deletion | Accounts with journal lines cannot be deleted |
| Date format | Date filters use `DD/MM/YYYY` format |
