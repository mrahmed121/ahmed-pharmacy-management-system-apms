# APMS API Reference

Base: `/api/v1` | Auth: JWT Bearer token

## Public
- `GET /health` — Service status
- `POST /auth/login` — {email, password} → {token, user}

## Authenticated
- `POST /auth/logout`, `POST /auth/refresh`, `GET /auth/me`

## Dashboard
- `GET /dashboard` — Today's revenue, invoices, low stock, near expiry (permission: dashboard.view)

## Inventory
- `GET /medicines?search=&per_page=` — With batches and total_stock (inventory.view)
- `GET /medicines/low-stock` — Below reorder level (inventory.view)
- `GET /medicines/near-expiry?days=90` — Expiring batches (inventory.view)

## POS
- `POST /pos/sales` — Create sale with FEFO deduction (pos.use)
  Body: {items: [{medicine_id, quantity}], discount?, customer_name?, payment_method?, idempotency_key?}
- `GET /sales` — Sale history (pos.use)

## Purchases
- `GET /suppliers` — Supplier list (purchases.view)

## Reports
- `GET /reports/daily-sales?days=30` — Revenue by day (reports.view)

## System
- `GET /users` (users.view), `GET /settings` (settings.view), `PUT /settings` (settings.manage)
- `GET /audit-logs` (audit.view)
