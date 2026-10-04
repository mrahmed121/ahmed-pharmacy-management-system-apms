# APMS Features

## Core Differentiators (vs upstream)

1. **FEFO Engine** — First-Expired-First-Out stock deduction, transactional with row-level locking. Upstream has no batch tracking.
2. **Batch & Expiry Management** — Every unit traceable to batch with expiry date. Upstream: no.
3. **Idempotent Sales API** — `idempotency_key` prevents double-charge on network retry. Upstream: no.
4. **Smart Alerts** — Near-expiry (configurable days) and low-stock with dashboard warnings. Upstream: no.
5. **Purchase Orders** — Supplier management with PO workflow. Upstream has basic supplier only.
6. **Sales Returns** — Approval workflow for refunds. Upstream: no.
7. **Granular RBAC** — 8 roles, 16 permissions (vs upstream's basic roles).
8. **Audit Trail** — Every sale logged with invoice and total. Upstream: no.
9. **Real-time Dashboard** — Query-backed revenue, alerts. Upstream: no.
10. **Barcode Search** — Scan-to-cart via USB HID (keyboard wedge). Upstream: no.
11. **Company Isolation** — Multi-tenant with global scopes. Upstream: single-tenant.
12. **Modern Stack** — Laravel 11 API + React 18 SPA (vs MEAN stack).

## Standard Features
- POS terminal with cart
- Medicine master with rack locations
- Supplier management
- Daily sales reports with charts
- User management
- Settings
