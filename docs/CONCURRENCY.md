# APMS Concurrency — FEFO Row Locking

## How It Works
`SaleService::createSale()` uses `DB::transaction()` with `lockForUpdate()` on batches:
```php
$batches = Batch::where(...)->orderBy('expiry_date')->lockForUpdate()->get();
```
This issues `SELECT ... FOR UPDATE`, locking the rows until the transaction commits.

## Database Behavior

### MySQL / PostgreSQL (Production)
- `lockForUpdate()` works as expected — concurrent sales block on the same batches
- Second transaction waits for first to commit, then sees updated quantities
- Prevents overselling the same batch

### SQLite (Development)
- **SQLite does NOT support `SELECT ... FOR UPDATE`** — it is silently ignored
- SQLite uses database-level locking: writers block each other entirely
- For a single-user dev environment, this is fine
- **Do NOT rely on SQLite for concurrent POS testing**

## Recommendation
- Development: SQLite is fine for single-user testing
- Production: Use MySQL 8+ or PostgreSQL 14+
- Load testing: Must be done on MySQL/PostgreSQL, not SQLite

## Test Coverage
- `test_partial_batch_spanning_fefo`: Verifies multi-batch deduction
- `test_idempotent_sale`: Verifies duplicate prevention via idempotency key
- True concurrent-sale test (two simultaneous requests) requires MySQL — **NOT TESTED** on SQLite
