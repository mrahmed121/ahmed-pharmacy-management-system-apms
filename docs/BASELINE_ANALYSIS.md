# Baseline Analysis — LalanaChami/Pharmacy-Mangment-System (upstream)

**Source:** https://github.com/LalanaChami/Pharmacy-Mangment-System
**Author:** Lalana Chamika Thanthirigama
**License:** MIT (Copyright (c) 2020 Lalana Chamika Thanthirigama)
**Stars:** 661 | **Pushed:** 2024-06

## What It Does
MEAN stack (MongoDB, Express, Angular, Node) pharmacy management:
- Inventory management (medicines, stock)
- Sales/POS
- Supplier management
- Doctor orders (prescription-based orders)
- User management with roles
- Order pickup workflow

**Scale:** 73 Angular spec files, 8 backend route modules, ~119 lines of models.

## Candidate Score: 72/100
| Criterion | Score | Notes |
|-----------|-------|-------|
| Architecture | 6/10 | MEAN stack, not Laravel — full rebuild required |
| Business depth | 8/10 | Inventory, sales, suppliers, doctor orders |
| Code quality | 6/10 | Standard MEAN patterns |
| UI foundation | 6/10 | Angular UI provides design reference |
| Documentation | 5/10 | Basic README |
| Test foundation | 5/10 | 73 spec files (frontend) |
| Extensibility | 7/10 | Modular structure |
| Portfolio value | 10/10 | Pharmacy not in portfolio, high demand |
| License clarity | 10/10 | MIT, clear copyright |
| Upgrade potential | 9/10 | Full Laravel 11 + React 18 rebuild with FEFO, batches |

Below 75 threshold but selected honestly: strongest MIT pharmacy candidate by far
(next best is 95 stars). The MEAN→Laravel rebuild is the reengineering value.

## Strengths (Keep Conceptually)
- Real pharmacy domain: inventory → sales → suppliers
- Doctor order workflow (prescription fulfillment)
- Role-based access concepts

## Weaknesses / Gaps
- No batch/expiry tracking (critical for pharmacy)
- No FEFO stock deduction
- No purchase orders / goods receipt
- No sales returns workflow
- No financial reports (P&L, expiry loss)
- No barcode support
- MongoDB (no relational integrity for financial data)

## Ahmed Transformation Plan
**Product:** Ahmed Pharmacy Management System (APMS)
**Tagline:** "Ahmed — Dispense With Precision."
**Stack:** Laravel 11 API + React 18 SPA + JWT + RBAC
**Repo:** ahmed-pharmacy-management-system-apms

**12 Differentiators:**
1. Batch-wise inventory with expiry dates and FEFO deduction (transactional)
2. Near-expiry (90/60/30 day) and low-stock alerts
3. Purchase orders → goods receipt → supplier ledger
4. Sales returns with approval workflow
5. Multi-counter shifts with cash drawer and EOD reconciliation
6. Controlled-drug register with mandatory audit trail
7. Profit & Loss, daily/monthly, expiry-loss reports with CSV export
8. Barcode scan-to-cart (USB HID keyboard wedge)
9. Thermal receipt printing
10. CSV import with validation (medicines, opening stock)
11. Granular RBAC (7 roles) with branch isolation
12. Idempotent sale APIs, N+1 free queries, proper DB constraints

**License compliance:** MIT LICENSE preserved, attribution in README.
**Upstream code remaining:** Zero — complete rebuild. "Inspired by" (domain concepts only).
