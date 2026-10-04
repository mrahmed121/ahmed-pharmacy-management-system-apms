# APMS Security Review

**Date:** 2026-10-04 | **Reviewer:** Pepper (for Ahmed)

## Authentication
- ✅ JWT with secret from `jwt:secret` (not committed)
- ✅ Passwords hashed via Laravel `Hash`
- ✅ Token refresh endpoint, logout invalidates

## Authorization
- ✅ Granular RBAC: 8 roles, 16 permissions (verified via seeder counts)
- ✅ `RequirePermission` middleware on all routes
- ✅ Cashier cannot access inventory (403 verified in tests)
- ✅ Auditor read-only (403 on write verified in tests)

## Tenancy / IDOR
- ✅ `BelongsToCompany` global scope on all domain models
- ✅ Cross-company access returns 404 (not 403, no existence leak)
- ✅ SaleService validates medicine belongs to user's company

## Input Validation
- ✅ All endpoints use `$request->validate()`
- ✅ Quantity must be positive numeric
- ✅ Payment method whitelisted (cash/card/mobile)

## Business Logic
- ✅ FEFO deduction is transactional (DB::transaction)
- ✅ Row-level locking (`lockForUpdate`) prevents race conditions
- ✅ Insufficient stock rejected with 422 (not partial deduction)
- ✅ Idempotency key prevents double-charge

## Data Protection
- ✅ No secrets in repo (verified: 0 .env tracked)
- ✅ `.env.example` present, `.env` gitignored
- ✅ Audit logs for all sales

## Known Limitations (honest)
- Debug mode should be `false` in production (check .env)
- Rate limiting not implemented on login (recommend for production)
- No 2FA (out of scope for v1)
- SQLite used in dev; production should use MySQL/PostgreSQL with proper backups

**Verdict:** Solid for v1. Not claiming "fully secure" — production hardening (rate limits, WAF, backups) recommended.
