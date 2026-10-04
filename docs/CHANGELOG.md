# APMS Changelog

## v1.0.0 (2026-10-04)
- Initial release
- FEFO inventory engine with batch/expiry tracking
- POS terminal with barcode search
- Purchase orders and supplier management
- Sales returns foundation
- 8-role RBAC with 16 permissions
- Audit logging
- Daily sales reports
- 12 backend tests, 7 frontend tests (all passing)

## v1.1.0 (2026-10-04)
- CSV import with validation (medicines + opening stock)
- CSV export (inventory)
- Concurrency documentation (FEFO row locking, SQLite vs MySQL)
- 5 new backend tests: expired batch, partial FEFO spanning, cross-company isolation, audit logging, schema checks
- GitHub Actions CI (backend tests, frontend tests, build)
- LICENSE updated to Copyright (c) 2026 Ahmed
- 9 real browser screenshots (desktop, mobile, tablet)
- Reliable PHP extension preflight via scripts/check-env.php
- DOCTOR_APMS.bat diagnostics tool
