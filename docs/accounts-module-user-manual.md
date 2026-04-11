# Accounts Module — User Manual

## Overview

The Accounts module provides a complete double-entry bookkeeping system. The recommended setup order is:

1. Account Groups → 2. Chart of Accounts → 3. Accounting Periods → 4. Journal Entries → 5. Reports

---

## 1. Account Groups

**Navigate to:** Accounts → Account Groups

Account Groups are categories that organize your accounts (e.g. Assets, Liabilities, Income, Expenses).

### Add a Group
1. Fill in the **Name** field (e.g. `Assets`, `Liabilities`, `Income`, `Expenses`).
2. Optionally select a **Parent Group** to create a sub-group (e.g. `Current Assets` under `Assets`).
3. Click **Save**.

### Edit a Group
1. Click the **edit (pencil)** icon on the group row.
2. Update the name or parent in the modal.
3. Click **Update**.

### Delete a Group
1. Click the **trash** icon on the group row.
2. Confirm the deletion prompt.

> **Note:** Do not delete a group that has accounts assigned to it.

---

## 2. Chart of Accounts

**Navigate to:** Accounts → Chart of Accounts

Individual ledger accounts belong here. Each account must be assigned to an Account Group.

### Add an Account
1. Click **+ Add Account**.
2. Enter the **Name** (e.g. `Cash in Hand`, `School Fee Income`).
3. Select an **Account Group**.
4. Optionally link to a physical account:
   - **Bank Account** — links to a registered bank account.
   - **Hand Cash** — links to a cash register.
   - **Mobile Banking** — links to a mobile banking account.
5. Add optional **Notes**.
6. Click **Save**.

### Edit an Account
1. Click the **edit** icon on the account row.
2. Modify fields and click **Update**.

### Delete an Account
1. Click the **trash** icon and confirm.

> **Note:** An account's running balance is calculated as:
> `Opening Balance + Total Debits − Total Credits` from all journal entry lines.

---

## 3. Accounting Periods

**Navigate to:** Accounts → Accounting Periods

Accounting periods define fiscal date ranges (e.g. `FY-2025`). Journal entries cannot be posted into a **closed** period.

### Create a Period
1. Click **+ New Period**.
2. Enter a unique **Name** (e.g. `FY-2025`).
3. Set the **Start Date** and **End Date**.
4. Click **Create**.

### Lock (Close) a Period
1. Click **Lock Period** on an open period row.
2. Confirm the prompt — **this cannot be undone**.
3. The period status changes to **Closed** and records who closed it.

> Once closed, no journal entries can be posted or edited for dates within that period.

### Delete a Period
- Only **open** (unlocked) periods can be deleted.
- Click **Del** and confirm.

---

## 4. Journal Entries

**Navigate to:** Accounts → (accessed via Journal Entries route)

Journal entries are the core of double-entry bookkeeping. Every entry must be **balanced** — total debits must equal total credits.

### Create a Journal Entry
1. Click **+ New Entry**.
2. Set the **Date**.
3. Enter a **Description / Narration**.
4. For each line:
   - Select an **Account** from the Chart of Accounts.
   - Enter either a **Debit** or **Credit** amount (not both).
5. The footer shows running **Total Debit** and **Total Credit**.
   - A green **✓ Balanced** badge appears when they match.
   - The **Post Journal Entry** button is disabled until the entry is balanced.
6. Click **+ Add Line** to add more lines (minimum 2 lines required).
7. Click **Post Journal Entry** to save.

> A unique reference number (e.g. `JE-20250501-0001`) is auto-generated.

### View a Journal Entry
- Click the **reference number** link in the list to see all lines, accounts, and amounts.

### Filter Journal Entries
- Filter by **Date From / To** or search by **reference number** or **description**.

### Delete a Journal Entry
- Click **Del** on the entry row and confirm.

> **Note:** You cannot post a journal entry if the date falls within a **closed accounting period**.

---

## 5. Ledger

**Navigate to:** Accounts → Ledger

The Ledger shows all financial transactions with debit/credit account mapping, amounts, and payment methods.

### Filter the Ledger
| Filter | Description |
|---|---|
| Type | Income, Expense, Capital, Withdrawal |
| Payment Method | Cash, Bank Transfer, Cheque, Mobile Banking, Other |
| Shareholder | Filter by a specific shareholder |
| From / To | Date range (format: dd/mm/yyyy) |

- Click **Filter** to apply.
- Click **Reset** to clear all filters.

### Summary Badges
- **Total Debit** — sum of expenses and withdrawals.
- **Total Credit** — sum of income and capital.
- **Net** — Credit minus Debit.

---

## 6. Reports

All reports support year or date-range filtering.

---

### 6.1 Trial Balance

**Navigate to:** Accounts → Trial Balance

Shows a summary of all account types for a selected year.

| Column | Description |
|---|---|
| Account | Account category (Income, Expenses, Capital, Withdrawals) |
| Debit | Total debit-side amounts |
| Credit | Total credit-side amounts |

- Select a **year** and click **Go**.
- Totals are shown in the footer row.

---

### 6.2 Balance Sheet

**Navigate to:** Accounts → Balance Sheet

Shows the financial position for a selected year.

| Row | Description |
|---|---|
| Capital Contributions | Total shareholder capital invested |
| Less: Withdrawals | Total shareholder withdrawals |
| Net Income / (Loss) | Income minus Expenses |
| **Total Equity** | Capital − Withdrawals + Net Income |

- Select a **year** and click **Go**.
- Negative values are shown in red with parentheses.

---

### 6.3 Cash Book

**Navigate to:** Accounts → Cash Book

Shows only **Cash** payment method transactions within a date range.

| Column | Description |
|---|---|
| Cash In | Income and Capital transactions |
| Cash Out | Expense and Withdrawal transactions |
| Balance | Cash In minus Cash Out |

- Set **From** and **To** dates (dd/mm/yyyy) and click **Filter**.
- Defaults to the current month.

---

### 6.4 Day Book

**Navigate to:** Accounts → Day Book

Shows all transactions for a **single day**, ordered by time.

- Select a **date** (dd/mm/yyyy) and click **Go**.
- Defaults to today.
- Shows debit account, credit account, and amount for each transaction.
- Summary badges show **Total Debit** and **Total Credit** for the day.

---

### 6.5 Income & Expenditure

**Navigate to:** Accounts → Income & Expenditure

Shows income and expenses broken down by **category** for a selected year.

- Select a **year** and click **Go**.
- Left panel: Income by category with total.
- Right panel: Expenditure by category with total.
- Bottom: **Surplus** (green) or **Deficit** (red) = Total Income − Total Expenditure.

---

## Typical Workflow

```
1. Create Account Groups       (e.g. Assets, Liabilities, Income, Expenses)
2. Create Accounts             (e.g. Cash, Bank, Fee Income, Salary Expense)
3. Create Accounting Period    (e.g. FY-2025: 01 Jan 2025 – 31 Dec 2025)
4. Post Journal Entries        (day-to-day transactions)
5. View Ledger                 (monitor all transactions)
6. Run Reports                 (Trial Balance, Balance Sheet, Cash Book, etc.)
7. Close Accounting Period     (at year-end to lock the books)
```

---

## Important Rules

- A journal entry requires **at least 2 lines** and must be **balanced** (Debit = Credit).
- Journal entries **cannot** be posted to a **closed** accounting period.
- A closed accounting period **cannot** be edited or deleted.
- Deleting an account group that has accounts assigned may cause data issues — reassign accounts first.
