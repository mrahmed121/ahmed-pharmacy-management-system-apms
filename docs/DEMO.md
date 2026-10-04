# APMS Demo Guide

## Quick Demo (5 minutes)

1. **Login** as Owner: `owner@ahmedpharma.local` / `password123`
2. **Dashboard** — See today's revenue, low stock alerts, near-expiry warnings
3. **POS Terminal** (`/pos`) — Search "Panadol", click to add to cart, Complete Sale
4. **Inventory** (`/inventory`) — View stock levels, click "Low Stock" tab
5. **Reports** (`/reports`) — Daily revenue chart

## Role Demo

- **Cashier** (`cashier@ahmedpharma.local`): Can use POS, cannot see Inventory (403)
- **Pharmacist** (`pharmacist@ahmedpharma.local`): POS + view inventory
- **Auditor** (`auditor@ahmedpharma.local`): View-only everywhere, cannot create sales

## FEFO Demo

1. Check Panadol Extra batches: B2024A (expires in 6 months, 200 units), B2025A (expires in 18 months, 300 units)
2. Sell 50 units via POS
3. Verify: B2024A now has 150 units (earliest expiry deducted first), B2025A untouched at 300

## Demo Data
- 3 medicines with 2 batches each (staggered expiry for FEFO demo)
- 1 supplier (MediDistributors)
- All demo users use password `password123`
