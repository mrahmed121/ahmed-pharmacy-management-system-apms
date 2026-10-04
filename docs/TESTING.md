# APMS Testing

## Backend (PHPUnit)
**Command:** `cd backend && php artisan test`

**Results (2026-10-04):** 12 passed, 33 assertions, 0 failed

| Test | What it verifies |
|------|------------------|
| health | API is running |
| login | JWT authentication works |
| medicines_list | Inventory listing |
| fefo_deduction | Earliest-expiry batch deducted first |
| insufficient_stock_rejected | 422 on oversell, no partial deduction |
| idempotent_sale | Same idempotency key returns same sale |
| cashier_cannot_view_inventory | 403 on inventory, POS allowed |
| auditor_readonly | 403 on sale creation |
| low_stock_alert | Low stock endpoint works |
| near_expiry_alert | Expiry alert returns batches |
| daily_sales_report | Report endpoint works |

## Frontend (Vitest + Testing Library)
**Command:** `cd frontend && npm test`

**Results (2026-10-04):** 7 passed, 0 failed

| Test File | Tests |
|-----------|-------|
| pos.test.jsx | Renders list, add to cart, total calculation, search filter (4) |
| dashboard.test.jsx | Welcome message, low stock alert, revenue display (3) |

## Browser QA
**Status:** PENDING — Playwright not available in this environment.
Manual verification done via API + frontend serving correctly.
