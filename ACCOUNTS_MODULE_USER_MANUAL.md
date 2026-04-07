# Accounts Module User Manual

## Overview
The Accounts Module provides comprehensive double-entry bookkeeping functionality for the school management system. It allows you to maintain accurate financial records through account groups, chart of accounts, journal entries, and various financial reports.

## Module Structure
The Accounts Module consists of the following main components:
- **Account Groups**: Hierarchical categorization of accounts
- **Chart of Accounts**: Individual accounts within groups
- **Accounting Periods**: Fiscal period management
- **Journal Entries**: Double-entry transaction recording
- **Ledger**: Transaction history and account balances
- **Financial Reports**: Trial Balance, Balance Sheet, Cash Book, Day Book, Income & Expenditure

## Navigation
Access the Accounts Module through the sidebar menu under "Accounts". The module includes sub-menus for each component.

---

## 1. Accounting Periods

### Purpose
Accounting periods define the fiscal year or reporting periods for your financial records. They help organize transactions by time periods and prevent modifications to closed periods.

### Operations

#### View Accounting Periods
1. Navigate to **Accounts → Accounting Periods**
2. View all existing periods with their status (open/closed)
3. See start and end dates for each period

#### Create New Accounting Period
1. Click the **"Add Period"** button
2. Enter:
   - **Name**: Descriptive name (e.g., "FY 2024-2025")
   - **Start Date**: Beginning date of the period
   - **End Date**: Ending date of the period
3. Click **Save**

#### Edit Accounting Period
1. Click the **Edit** button next to an open period
2. Modify name, start date, or end date
3. Click **Update**

#### Close Accounting Period
1. Click the **Close** button next to an open period
2. Confirm closure - this prevents further modifications
3. Closed periods cannot be edited or deleted

#### Delete Accounting Period
1. Click the **Delete** button next to an open period
2. Confirm deletion
3. Note: Closed periods cannot be deleted

---

## 2. Account Groups

### Purpose
Account groups provide hierarchical categorization of accounts following standard accounting principles (Assets, Liabilities, Equity, Income, Expenses).

### Operations

#### View Account Groups
1. Navigate to **Accounts → Account Groups**
2. See hierarchical structure with parent-child relationships
3. View number of accounts in each group

#### Create Account Group
1. Click **"Add Group"** button
2. Enter:
   - **Name**: Group name (e.g., "Current Assets", "Revenue")
   - **Parent Group**: Select parent group for hierarchy (optional)
3. Click **Save**

#### Edit Account Group
1. Click **Edit** button next to the group
2. Modify name or parent relationship
3. Click **Update**

#### Delete Account Group
1. Click **Delete** button next to the group
2. Confirm deletion
3. Note: Cannot delete groups that contain accounts

---

## 3. Chart of Accounts

### Purpose
The Chart of Accounts contains all individual accounts used for recording transactions. Each account belongs to an account group and can be linked to bank accounts, mobile banking, or cash accounts.

### Operations

#### View Chart of Accounts
1. Navigate to **Accounts → Chart of Accounts**
2. See all accounts with their groups and current balances
3. Filter by account group if needed

#### Create New Account
1. Click **"Add Account"** button
2. Enter:
   - **Name**: Account name (e.g., "Petty Cash", "Tuition Fees")
   - **Account Group**: Select appropriate group
   - **Reference Type**: Link to Bank Account, Mobile Banking, or Hand Cash (optional)
   - **Reference**: Select specific account from the reference type
   - **Notes**: Additional information
3. Click **Save**

#### Edit Account
1. Click **Edit** button next to the account
2. Modify details as needed
3. Click **Update**

#### Delete Account
1. Click **Delete** button next to the account
2. Confirm deletion
3. Note: Cannot delete accounts with transaction history

---

## 4. Journal Entries

### Purpose
Journal Entries are the core of double-entry bookkeeping. Each transaction affects at least two accounts - one debit and one credit (or multiple debits/credits that balance).

### Operations

#### View Journal Entries
1. Navigate to **Accounts → Journal Entries**
2. See all journal entries with reference numbers, dates, and descriptions
3. Filter by date range or search by reference/description

#### Create Journal Entry
1. Click **"Add Entry"** button
2. Enter:
   - **Date**: Transaction date
   - **Description**: Brief description of the transaction
3. Add journal lines:
   - **Account**: Select account from chart of accounts
   - **Debit**: Amount to debit (money going out/increasing assets)
   - **Credit**: Amount to credit (money coming in/increasing liabilities/equity)
4. Ensure total debits equal total credits
5. Click **Save**

#### View Journal Entry Details
1. Click **View** button next to any entry
2. See complete entry with all lines and balances

#### Delete Journal Entry
1. Click **Delete** button next to the entry
2. Confirm deletion
3. Note: This permanently removes the transaction

---

## 5. Ledger

### Purpose
The Ledger provides a complete view of all financial transactions with filtering and search capabilities. It shows debit/credit amounts and running balances.

### Operations

#### View Ledger
1. Navigate to **Accounts → Ledger**
2. View paginated list of transactions
3. See transaction details, amounts, and balances

#### Filter Transactions
Use available filters:
- **Type**: Income, Expense, Capital, Withdrawal
- **Payment Method**: Cash, Bank Transfer, etc.
- **Shareholder**: Filter by specific shareholder
- **Date Range**: From/To dates
- **Search**: Reference numbers or descriptions

#### Export Data
- Use browser print function for PDF export
- Copy data for spreadsheet analysis

---

## 5. Financial Reports

### Purpose
Financial reports provide insights into the school's financial position and performance through standardized accounting reports.

### 5.1 Trial Balance

#### Purpose
Shows all account balances to verify that total debits equal total credits.

#### Generate Report
1. Navigate to **Accounts → Trial Balance**
2. Select **Year** (defaults to current year)
3. View debit and credit columns
4. Verify totals balance (should be equal)

#### Understanding the Report
- **Debit Column**: Expenses, withdrawals, asset increases
- **Credit Column**: Income, capital, liability/equity increases
- **Balanced**: Total debits should equal total credits

### 5.2 Balance Sheet

#### Purpose
Shows the school's financial position at a specific point in time.

#### Generate Report
1. Navigate to **Accounts → Balance Sheet**
2. Select **Year** (defaults to current year)
3. View Assets, Liabilities, and Equity

#### Understanding the Report
- **Assets**: What the school owns
- **Liabilities**: What the school owes
- **Equity**: Net worth (Assets - Liabilities)
- **Equation**: Assets = Liabilities + Equity

### 5.3 Cash Book

#### Purpose
Tracks all cash transactions and maintains cash balance.

#### Generate Report
1. Navigate to **Accounts → Cash Book**
2. Set **Date Range** (defaults to current month)
3. View cash inflows and outflows

#### Understanding the Report
- **Cash In**: Income and capital contributions
- **Cash Out**: Expenses and withdrawals
- **Net Cash Flow**: Difference between in and out

### 5.4 Day Book

#### Purpose
Shows all transactions for a specific date.

#### Generate Report
1. Navigate to **Accounts → Day Book**
2. Select **Date** (defaults to today)
3. View all transactions for that date

#### Understanding the Report
- **Debit**: Money going out or asset increases
- **Credit**: Money coming in or liability/equity increases
- **Total Verification**: Debits should equal credits

### 5.5 Income & Expenditure

#### Purpose
Shows revenue and expenses for a period, similar to a Profit & Loss statement.

#### Generate Report
1. Navigate to **Accounts → Income & Expenditure**
2. Select **Year** (defaults to current year)
3. View categorized income and expenses

#### Understanding the Report
- **Income Categories**: Revenue streams
- **Expense Categories**: Cost centers
- **Surplus/Deficit**: Net result (Income - Expenses)

---

## Complete Workflow

### Initial Setup
1. **Create Accounting Periods** for your fiscal years
2. **Set up Account Groups** following accounting standards:
   - Assets (Current Assets, Fixed Assets)
   - Liabilities (Current Liabilities, Long-term Liabilities)
   - Equity (Capital, Retained Earnings)
   - Income (Revenue streams)
   - Expenses (Cost categories)

3. **Create Chart of Accounts** under each group
4. **Link accounts** to actual bank/mobile/cash accounts

### Daily Operations
1. **Record Transactions** through journal entries
2. **Monitor Ledger** for transaction history
3. **Review Balances** regularly

### Reporting
1. **Generate Trial Balance** monthly to verify accuracy
2. **Create Balance Sheet** for financial position
3. **Review Cash Book** for cash management
4. **Analyze Income & Expenditure** for profitability
5. **Use Day Book** for daily transaction review

### Period-End Procedures
1. **Close Accounting Period** when fiscal year ends
2. **Generate Final Reports** for the period
3. **Archive Records** for compliance

---

## Best Practices

### Account Setup
- Use consistent naming conventions
- Create detailed account groups for better reporting
- Link all bank and cash accounts to chart of accounts

### Transaction Recording
- Record transactions promptly
- Ensure all entries are balanced (debits = credits)
- Use descriptive references and descriptions

### Reporting
- Generate trial balance monthly
- Review balance sheet quarterly
- Monitor cash flow weekly
- Analyze income/expenditure regularly

### Security
- Restrict access to accounting functions
- Regular backups of financial data
- Audit trail maintenance

---

## Troubleshooting

### Common Issues

**Unbalanced Entries**
- Verify all journal entries have equal debits and credits
- Check for calculation errors

**Incorrect Balances**
- Review recent transactions
- Verify opening balances
- Check for duplicate entries

**Missing Accounts**
- Ensure all necessary accounts are created
- Link accounts to appropriate groups

**Report Discrepancies**
- Verify date ranges
- Check transaction classifications
- Review account group assignments

---

## Support
For technical issues or questions about accounting procedures, contact your system administrator or accounting department.