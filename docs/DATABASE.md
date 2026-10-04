# APMS Database

## Tables (17)

### Foundation
- `companies` — Pharmacy companies (multi-tenant root)
- `users`, `roles`, `permissions`, `permission_role`, `role_user` — RBAC

### Inventory
- `medicines` — Medicine master (name, generic, barcode, rack, reorder_level, sale_price, is_controlled)
- `batches` — Stock batches (batch_number, expiry_date, quantity, purchase_price)
  - Unique: (company_id, medicine_id, batch_number)
  - Index: (medicine_id, expiry_date) for FEFO queries

### Purchases
- `suppliers` — Supplier master with balance
- `purchase_orders` — PO header (po_number unique, status, total)
- `purchase_items` — PO lines with batch/expiry for goods receipt

### Sales
- `sales` — Sale header (invoice_number unique, totals, payment_method, idempotency_key unique)
- `sale_items` — Sale lines linked to specific batch_id (traceability)
- `sale_returns` — Return requests with approval workflow

### Operations
- `shifts` — Cashier shifts with open/close cash reconciliation
- `audit_logs` — Immutable audit trail
- `settings` — Company settings (key-value)

## Key Constraints
- FEFO integrity: `sale_items.batch_id` → `batches.id` (every unit traceable to batch)
- No negative stock: application-level check + transactional deduction
- Idempotency: `sales.idempotency_key` unique prevents duplicate sales
