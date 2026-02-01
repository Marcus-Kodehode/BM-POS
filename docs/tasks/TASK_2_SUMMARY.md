# Task 2 Summary — Data Models & Relationships

**Date:** 2026-02-01  
**Status:** ✅ Complete  
**Related:** Task 1 (previous), Task 3 (next)

---

## What Was Done

Completed the entire database schema with migrations for items, orders, order_lines, and payments. Created all Eloquent models with relationships, soft deletes, and business logic. Implemented currency formatting helper and order authorization policy.

**Key Changes:**
- Created 4 database tables with foreign keys and soft deletes
- Built 4 Eloquent models with complete relationships
- Implemented auto-generation of order numbers (YYYY-NNN format)
- Added outstanding balance calculation on Order model
- Created `format_nok()` helper for currency formatting
- Implemented OrderPolicy for access control

---

## Files Changed

**Created:**
- `database/migrations/2026_02_01_130205_create_items_table.php` — Items/inventory table
- `database/migrations/2026_02_01_130230_create_orders_table.php` — Orders table with order_number
- `database/migrations/2026_02_01_130255_create_order_lines_table.php` — Order lines linking items to orders
- `database/migrations/2026_02_01_130326_create_payments_table.php` — Payments table
- `app/Models/Item.php` — Item model with status management
- `app/Models/Order.php` — Order model with auto-generated order numbers
- `app/Models/OrderLine.php` — OrderLine model with total calculation
- `app/Models/Payment.php` — Payment model with date casting
- `app/helpers.php` — Global helper functions
- `app/Policies/OrderPolicy.php` — Order authorization policy

**Modified:**
- `composer.json` — Added helpers.php to autoload files

---

## How to Test

**Quick Test (5 min):**
```bash
# Test currency formatting
php artisan tinker --execute="echo format_nok(15000);"
# Expected: 150,00 kr

# Test order number generation
php artisan tinker --execute="echo App\Models\Order::create(['customer_id' => 1, 'total_amount' => 50000])->order_number;"
# Expected: 2026-001

# Test relationships in tinker
php artisan tinker
$order = Order::first();
$order->customer;  // Should return User
$order->orderLines;  // Should return collection
$order->payments;  // Should return collection
```

**What to Expect:**
1. All migrations run successfully
2. `format_nok(15000)` returns "150,00 kr"
3. Order creation auto-generates order_number like "2026-001"
4. Model relationships work (order->customer, order->orderLines, etc.)
5. Outstanding balance calculation works: `$order->outstanding_amount`

**Database Structure:**
- `items` — 8 columns + soft deletes
- `orders` — 8 columns + soft deletes + unique order_number
- `order_lines` — 6 columns + soft deletes
- `payments` — 7 columns + soft deletes

---

## Security Notes

**What Protects This:**
- ✅ Soft deletes: All tables preserve audit trail
- ✅ Foreign keys: Cascade deletes maintain referential integrity
- ✅ OrderPolicy: Enforces admin-only modifications, customer ownership for viewing
- ✅ Mass assignment protection: All models have explicit `$fillable` arrays
- ✅ Type casting: All amounts cast to integer, dates cast to date
- ✅ Integer øre: All currency stored as integers (no float errors)

**Verified Checklist:**
- [x] All models have `$fillable` defined
- [x] All models use SoftDeletes trait
- [x] All amounts stored as integer øre
- [x] Foreign keys properly constrained
- [x] OrderPolicy enforces access control
- [x] Currency helper prevents inline formatting

---

## Notes for Next Task

**Dependencies:**
- Task 3 (Admin Dashboard) can now use these models
- All relationships are ready for eager loading
- `format_nok()` helper available globally
- OrderPolicy ready for route protection

**Known Issues:**
- None

**TODOs:**
- Task 3 will create controllers and views using these models
- Need to implement item status transitions (available → reserved → sold)
- Need to implement order total_amount recalculation when lines change

---

## Quick Reference

**New Models:**
- `Item` — Inventory management with status tracking
- `Order` — Sales orders with auto-generated order numbers
- `OrderLine` — Links items to orders with quantity
- `Payment` — Tracks partial payments against orders

**New Helper:**
- `format_nok(int $amount)` — Formats øre to "X,XX kr"

**Model Relationships:**
```
User
  └── hasMany → Order (customer_id)

Order
  ├── belongsTo → User (customer)
  ├── hasMany → OrderLine
  └── hasMany → Payment

OrderLine
  ├── belongsTo → Order
  └── belongsTo → Item

Item
  └── hasMany → OrderLine

Payment
  └── belongsTo → Order
```

**Order Model Methods:**
- `$order->paid_amount` — Total paid (accessor)
- `$order->outstanding_amount` — Total - paid (accessor)
- `$order->isOverpaid()` — Check if overpaid
- Auto-generates `order_number` on create

**Item Model Methods:**
- `$item->isAvailable()` — Check if status is 'available'

**OrderLine Model Methods:**
- `$orderLine->total` — unit_price × quantity (accessor)

**Database Schema:**
- All amounts in **integer øre**
- All tables have **soft deletes**
- Order numbers: **YYYY-NNN** format (e.g., "2026-001")
- Item statuses: **available, reserved, sold, archived**
- Order statuses: **open, closed, cancelled**

---

*Template Version: 1.0*
