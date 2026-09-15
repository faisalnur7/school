# Playwright application test and user manual

This folder contains the screenshots and machine-readable results from the read-only Playwright crawl.

## Important limitation

The protected part of the application could not be opened because the local admin account in the project database did not accept the documented password. Therefore, the protected screenshots show the sign-in screen, not the actual module pages. No records were created, edited, deleted, or paid during this run.

## What was tested

- Local site: `http://127.0.0.1`
- Browser: Playwright 1.63 with installed Google Chrome
- Viewport: 1440 × 1000
- Route candidates: 511 GET routes
- Screenshot files captured in this run: 117
- Authentication: unsuccessful
- Login redirects: 504
- Public pages reached: admission form and admission search
- HTTP errors found in the test data: 4 (mostly expected because placeholder ID/token `1` or `invalid-token` does not identify a real record)

## User manual

The plain-language operating guide is [Hub Pages User Manual](hub-pages-user-manual.md). It explains the dashboard, academics, admissions, students, results, attendance, fees, accounts, inventory, HR, assets, settings, roles, locations, and communications.

## Screenshots

Open the [Playwright artifacts folder](../artifacts/playwright/) to see the captured PNG files. The route-level evidence is in [results.json](../artifacts/playwright/results.json); [summary.json](../artifacts/playwright/summary.json) contains the run totals.

## To complete the authenticated pass

Run the same crawl with a valid authorized test account:

```bash
PLAYWRIGHT_EMAIL='authorized-account@example.com' \
PLAYWRIGHT_PASSWORD='your-password' \
node scripts/playwright-crawl.mjs
```

The account should be a non-production test account with permission to view all modules. A student ID is also needed for student-specific pages and a real admission token/application ID is needed for record-specific pages.
