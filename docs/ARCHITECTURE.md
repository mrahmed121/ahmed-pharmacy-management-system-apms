# APMS Architecture

## Stack
- **Backend:** Laravel 11 (PHP 8.3), JWT Auth (tymon/jwt-auth)
- **Frontend:** React 18, Vite, Tailwind CSS, React Router
- **Database:** SQLite (dev), MySQL-ready (production)

## Domain Structure
```
app/Domains/
├── Inventory/     # Medicines, Batches (FEFO)
├── Sales/         # POS sales, SaleService (FEFO engine)
├── Purchases/     # Suppliers, Purchase Orders
├── Reports/       # Daily sales, analytics
└── Shared/        # Company, User, Role, Permission, Audit
```

## Key Design Decisions

### FEFO Engine (SaleService)
- `SELECT ... FOR UPDATE` row locking on batches ordered by expiry_date
- Transactional: all-or-nothing deduction across multiple batches
- Idempotency via `idempotency_key` (prevents double-charge on retry)

### Multi-tenancy
- Company-scoped queries via `BelongsToCompany` trait
- Global scope filters by authenticated user's company_id
- Super Admin (company_id=null) sees all

### API Versioning
All routes under `/api/v1`. Health check at `/api/v1/health` (no auth).
