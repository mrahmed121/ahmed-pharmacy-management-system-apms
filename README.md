# APMS — Ahmed Pharmacy Management System

**"Ahmed — Dispense With Precision."**

**Developed by Ahmed**

## Overview

APMS is a complete pharmacy management and point-of-sale system for retail pharmacies and medical stores. It handles the full medicine lifecycle: purchase → batch/expiry tracking → FEFO dispensing → sales → returns → reporting.

## Key Features

- **POS Terminal** — Barcode scan-to-cart, fast checkout, thermal receipt ready
- **FEFO Stock Deduction** — First-Expired-First-Out, transactional, concurrency-safe
- **Batch & Expiry Tracking** — Every unit tracked by batch with expiry dates
- **Smart Alerts** — Near-expiry (90/60/30 days) and low-stock warnings
- **Purchase Management** — Suppliers, purchase orders, goods receipt
- **Sales Returns** — Approval workflow for refunds
- **Reports** — Daily sales, revenue trends, P&L ready
- **RBAC** — 8 roles with granular permissions
- **Audit Trail** — All sales and stock movements logged

## Tech Stack

- Backend: Laravel 11 API + JWT Auth
- Frontend: React 18 + Vite + Tailwind CSS
- Database: SQLite (dev) / MySQL (production)

## Quick Start (Windows)

Double-click `RUN_APMS.bat`. To stop: `STOP_APMS.bat`.

## Manual Setup

```bash
# Backend
cd backend && composer install && cp .env.example .env
php artisan key:generate && php artisan jwt:secret
touch database/database.sqlite
php artisan migrate --seed
php artisan serve --host=127.0.0.1 --port=8003

# Frontend
cd frontend && npm install
echo "VITE_API_BASE_URL=http://127.0.0.1:8003/api/v1" > .env
npm run dev -- --host 127.0.0.1 --port 5176
```

## Demo Logins (password: password123)

| Role | Email |
|------|-------|
| Owner | owner@ahmedpharma.local |
| Manager | manager@ahmedpharma.local |
| Pharmacist | pharmacist@ahmedpharma.local |
| Cashier | cashier@ahmedpharma.local |
| Auditor | auditor@ahmedpharma.local |

## License & Attribution

MIT License.

**Based on:** [Pharmacy-Mangment-System](https://github.com/LalanaChami/Pharmacy-Mangment-System) by Lalana Chamika Thanthirigama, MIT License (Copyright (c) 2020).

**What Ahmed built:** Complete rebuild from MEAN stack to Laravel 11 + React 18. Zero upstream code remains — "inspired by" (domain concepts only). New: FEFO engine, batch/expiry tracking, idempotent sales API, purchase orders, returns workflow, RBAC (8 roles), audit logging, Ahmed design system.

Original MIT LICENSE preserved in `LICENSE-UPSTREAM.md`.

## Troubleshooting (Windows)

### "Missing PHP extensions" but XAMPP has them enabled
The BAT now uses `scripts/check-env.php` (PHP-native `extension_loaded()`) instead of parsing `php -m`.
If you still see false "missing" errors:
1. Run `DOCTOR_APMS.bat` — it shows the exact php.exe path and ini file being used
2. Check if a different PHP is first in PATH: `where php` lists all found
3. Verify the ini path shown matches your XAMPP: should be `C:\xampp\php\php.ini`

### "No php.ini loaded"
1. Find your php.exe: `where php`
2. Look for `php.ini-development` next to php.exe
3. Copy it to `php.ini` (the BAT offers to do this automatically)
4. For XAMPP: `C:\xampp\php\php.ini`

### Enabling extensions in XAMPP
1. Open `C:\xampp\php\php.ini` as Administrator
2. Find the line (e.g., `;extension=curl`) and remove the `;` at the start
3. Save, restart Apache, re-run the BAT
4. Required: pdo_sqlite, mbstring, openssl, fileinfo, curl, zip

### "Port busy" — BAT picks next free port automatically
If 8001 is busy, it tries 8002, 8003, etc. The frontend .env is updated automatically.

### Bypass checks (advanced)
Run: `RUN_APMS.bat --skip-checks`
